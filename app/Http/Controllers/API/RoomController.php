<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
// Trik Profesional: Gunakan DB Facade untuk membungkus operasi multi-tabel dalam Database Transaction (ACID)
use Illuminate\Support\Facades\DB;

// Impor ke-3 Model Relasional yang telah dimigrasikan
use App\Models\Transaction;
use App\Models\TransactionMember;
use App\Models\TransactionItem;

class RoomController extends Controller
{   
    /**
     * Memproses kalkulasi matematika split-bill, menyimpan data secara relasional 
     * ke dalam 3 tabel MySQL ternormalisasi, dan mengembalikan respons yang kompatibel.
     */
    public function calculateSplitBill(Request $request)
    {
        // 1. Definisikan aturan validasi, termasuk menangkap bankName dan rekening dinamis dari frontend
        $rules = [
            'restaurantName' => 'required|string|max:255',
            'tableNumber' => 'required|string|max:50',
            'hostName' => 'nullable|string|max:100',
            'bankName' => 'nullable|string|max:100',
            'rekening' => 'nullable|string|max:100',
            'members' => 'required|array|min:1',
            'members.*' => 'required|string|max:100',
            'items' => 'required|array|min:1',
            'additionalCosts' => 'nullable|array',
            'additionalCosts.taxPercent' => 'nullable|numeric|min:0|max:100',
            'additionalCosts.discount' => 'nullable|numeric|min:0',
            'additionalCosts.extraFees' => 'nullable|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input data tidak valid sesuai format QRoom.',
                'errors' => $validator->errors() 
            ], 422);
        }

        $validatedData = $validator->validated();

        // 2. Ekstraksi parameter dan setel nilai default (fallback)
        $members = $validatedData['members'];
        $items = $validatedData['items'];
        $additionalCosts = $validatedData['additionalCosts'] ?? [];
        
        $taxPercent = $additionalCosts['taxPercent'] ?? 0;
        $totalDiscount = $additionalCosts['discount'] ?? 0;
        $totalExtraFees = $additionalCosts['extraFees'] ?? 0;

        // Inisialisasi wadah perhitungan per individu
        $memberBillBreakdown = [];
        foreach ($members as $memberName) {
            $memberBillBreakdown[$memberName] = [
                'memberName' => $memberName,
                'baseCost' => 0,      
                'taxShare' => 0,      
                'discountShare' => 0, 
                'extraFeeShare' => 0, 
                'grandTotal' => 0,    
                'orderedItems' => []
            ];
        }

        // 3. KALKULASI LOOP 1: Hitung total biaya pokok per individu
        $totalRoomBaseCost = 0;
        foreach ($items as $item) {
            $itemTotalCost = $item['price'] * $item['qty'];
            $itemOwner = $item['user'];

            if (isset($memberBillBreakdown[$itemOwner])) {
                $memberBillBreakdown[$itemOwner]['baseCost'] += $itemTotalCost;
                $memberBillBreakdown[$itemOwner]['orderedItems'][] = [
                    'name' => $item['name'],
                    'price' => (float)$item['price'],
                    'qty' => (int)$item['qty'],
                    'total' => $itemTotalCost
                ];
                $totalRoomBaseCost += $itemTotalCost;
            }
        }

        // Hitung pembagian biaya tetap (flat)
        $memberCount = count($members);
        $flatFeeSharePerMember = $memberCount > 0 ? ($totalExtraFees / $memberCount) : 0;

        // 4. KALKULASI LOOP 2: Pajak, diskon, dan grand total per individu
        $totalRoomTax = 0;
        $totalRoomDiscount = 0;
        $calculatedRoomGrandTotal = 0;

        foreach ($members as $memberName) {
            $memberBase = $memberBillBreakdown[$memberName]['baseCost'];

            // Diskon proporsional
            $memberDiscountShare = 0;
            if ($totalRoomBaseCost > 0 && $totalDiscount > 0) {
                $memberDiscountShare = ($memberBase / $totalRoomBaseCost) * $totalDiscount;
            }

            // Pajak proporsional
            $memberTaxShare = $memberBase * ($taxPercent / 100);

            // Akumulasi grand total individu
            $memberGrandTotal = $memberBase + $memberTaxShare - $memberDiscountShare + $flatFeeSharePerMember;

            $memberBillBreakdown[$memberName]['taxShare'] = round($memberTaxShare);
            $memberBillBreakdown[$memberName]['discountShare'] = round($memberDiscountShare);
            $memberBillBreakdown[$memberName]['extraFeeShare'] = round($flatFeeSharePerMember);
            $memberBillBreakdown[$memberName]['grandTotal'] = round($memberGrandTotal);

            $totalRoomTax += $memberTaxShare;
            $totalRoomDiscount += $memberDiscountShare;
            $calculatedRoomGrandTotal += $memberGrandTotal;
        }

        // Susun informasi rekening tujuan transfer secara dinamis
        $hostTransferInfo = [
            'hostName' => $validatedData['hostName'] ?? $members[0],
            'paymentOptions' => [
                [
                    'provider' => !empty($validatedData['bankName']) ? $validatedData['bankName'] : 'BCA', 
                    'accountNumber' => !empty($validatedData['rekening']) ? $validatedData['rekening'] : '-'
                ]
            ]
        ];

        // Generate ID transaksi unik
        $transactionId = 'QR-' . date('Ymd') . '-' . rand(1000, 9999);

        // 5. PROSES PERSISTENSI DATABASE RELASIONAL (3-TIER NORMALIZED)
        // Gunakan DB::transaction untuk menjamin bahwa seluruh data masuk dengan sukses (jika salah satu gagal, batalkan semua)
        DB::transaction(function () use ($transactionId, $validatedData, $members, $items, $totalRoomBaseCost, $totalRoomTax, $totalDiscount, $totalExtraFees, $calculatedRoomGrandTotal, $memberBillBreakdown) {
            
            // A. INSERT KE TABEL 1: Transactions (Parent Row)
            Transaction::create([
                'transaction_id'    => $transactionId,
                'restaurant_name'   => $validatedData['restaurantName'],
                'table_number'      => $validatedData['tableNumber'],
                'host_name'         => $validatedData['hostName'] ?? $members[0],
                'total_base_cost'   => round($totalRoomBaseCost),
                'total_tax'         => round($totalRoomTax),
                'total_discount'    => round($totalDiscount),
                'total_extra_fees'  => round($totalExtraFees),
                'grand_total'       => round($calculatedRoomGrandTotal)
            ]);

            // B. INSERT KE TABEL 2: Transaction Members (Child 1)
            foreach ($memberBillBreakdown as $name => $breakdown) {
                TransactionMember::create([
                    'transaction_id'  => $transactionId,
                    'member_name'     => $name,
                    'base_cost'       => $breakdown['baseCost'],
                    'tax_share'       => $breakdown['taxShare'],
                    'discount_share'  => $breakdown['discountShare'],
                    'extra_fee_share' => $breakdown['extraFeeShare'],
                    'grand_total'     => $breakdown['grandTotal']
                ]);
            }

            // C. INSERT KE TABEL 3: Transaction Items (Child 2)
            foreach ($items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transactionId,
                    'item_name'      => $item['name'],
                    'price'          => (float)$item['price'],
                    'qty'            => (int)$item['qty'],
                    'total_price'    => (float)($item['price'] * $item['qty']),
                    'user_name'      => $item['user']
                ]);
            }
        });

        // 6. KEMBALIKAN RESPONS JSON: Struktur 100% SAMA agar nota.js & history.js tidak pecah
        return response()->json([
            'success' => true,
            'message' => 'Proses pembagian tagihan adil telah selesai dihitung dan disimpan secara relasional!',
            'data' => [
                'restaurantName' => $validatedData['restaurantName'],
                'tableNumber' => $validatedData['tableNumber'],
                'transactionId' => $transactionId,
                'date' => date('M d, Y'),
                'summary' => [
                    'totalBaseCost' => round($totalRoomBaseCost),
                    'totalTax' => round($totalRoomTax),
                    'totalDiscount' => round($totalDiscount),
                    'totalExtraFees' => round($totalExtraFees),
                    'grandTotal' => round($calculatedRoomGrandTotal)
                ],
                'membersBreakdown' => array_values($memberBillBreakdown),
                'transferInfo' => $hostTransferInfo
            ]
        ], 200);
    }


    /**
     * Mengarsipkan data transaksi lokal.
     */
    public function archiveRoom(Request $request)
    {
        $rules = [
            'restaurantName' => 'required|string|max:255',
            'grandTotal'     => 'required|numeric|min:0',
            'members'        => 'required|array|min:1',
            'members.*'      => 'required|string|max:100',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengarsipkan sesi. Struktur data tidak valid.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $sanitizedData = $validator->validated();

        $uniqueHex = strtoupper(bin2hex(random_bytes(2)));
        $transactionId = '#QR-2026-' . $uniqueHex;

        $archivePayload = [
            'transactionId'   => $transactionId,
            'restaurantName'  => strip_tags($sanitizedData['restaurantName']), 
            'date'            => date('M d, Y'), 
            'timestamp'       => time(), 
            'totalAmount'     => (float) $sanitizedData['grandTotal'],
            'membersCount'    => count($sanitizedData['members']),
            'membersPreview'  => array_slice($sanitizedData['members'], 0, 3),
            'status'          => 'COMPLETED' 
        ];

        return response()->json([
            'success' => true,
            'message' => 'Room berhasil dikunci. Sesi ini telah resmi diarsipkan.',
            'archive' => $archivePayload
        ], 200);
    }
}
<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
// Facade Validator yang kita gunakan untuk memvalidasi request data secara manual
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{   
     public function calculateSplitBill(Request $request)
    {
        // 1. Tentukan aturan validasi sesuai kontrak data terbaru
        $rules = [
            // Nama restoran wajib diisi berupa string teks biasa
            'restaurantName' => 'required|string|max:255',
            
            // Nomor meja atau catatan tambahan wajib diisi berupa string/karakter alfanumerik
            'tableNumber' => 'required|string|max:50',
            
            // Kolom anggota (members) wajib berupa array dan minimal harus ada 1 nama terdaftar
            'members' => 'required|array|min:1',
            'members.*' => 'required|string|max:100', // Setiap nama anggota harus berupa string teks
            
            // Kolom menu pesanan (items) wajib berupa array dan minimal ada 1 menu yang dipesan
            'items' => 'required|array|min:1',
            
            // Kolompok biaya tambahan bersifat opsional, namun jika dikirim harus berupa objek/array
            'additionalCosts' => 'nullable|array',
            
            // Persentase pajak opsional, jika diisi harus berupa angka desimal/bulat antara 0% hingga 100%
            'additionalCosts.taxPercent' => 'nullable|numeric|min:0|max:100',
            
            // Nominal diskon opsional, jika diisi harus berupa angka positif
            'additionalCosts.discount' => 'nullable|numeric|min:0',
            
            // Nominal biaya tambahan (ongkir/parkir) opsional, jika diisi harus berupa angka positif
            'additionalCosts.extraFees' => 'nullable|numeric|min:0',
        ];

        // 2. Lakukan pengecekan validasi terhadap data input request
        $validator = Validator::make($request->all(), $rules);

        // 3. Tangani jika data yang dikirim frontend tidak sesuai aturan validasi
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input data tidak valid sesuai format QRoom.',
                'errors' => $validator->errors() // Mengembalikan rincian error spesifik untuk diproses di frontend
            ], 422); // HTTP 422: Unprocessable Entity
        }

        // 4. Ambil data yang berhasil tervalidasi dengan aman
        $validatedData = $validator->validated();

        // 5. Ekstraksi data payload dari frontend dengan menyiapkan fallback default value
        $members = $validatedData['members'];
        $items = $validatedData['items'];
        $additionalCosts = $validatedData['additionalCosts'] ?? [];
        
        // Membaca biaya tambahan (pajak, diskon, biaya flat)
        $taxPercent = $additionalCosts['taxPercent'] ?? 0;
        $totalDiscount = $additionalCosts['discount'] ?? 0;
        $totalExtraFees = $additionalCosts['extraFees'] ?? 0;

        // 6. Siapkan wadah struktur data untuk rincian tagihan per individu
        $memberBillBreakdown = [];
        foreach ($members as $memberName) {
            $memberBillBreakdown[$memberName] = [
                'memberName' => $memberName,
                'baseCost' => 0,      // Menampung total harga makanan pokok yang dipesan sendiri
                'taxShare' => 0,      // Menampung porsi pajak proporsional hasil kalkulasi
                'discountShare' => 0, // Menampung porsi diskon proporsional hasil kalkulasi
                'extraFeeShare' => 0, // Menampung porsi biaya flat bagi rata
                'grandTotal' => 0,    // Menampung total tagihan akhir bersih per orang
                'orderedItems' => []  // Menampung histori item apa saja yang dipesan oleh orang ini
            ];
        }

        // 7. LANGKAH 1: Hitung total biaya makanan pokok (Base Cost) per anggota & total keseluruhan ruangan
        // Rumus Dasar: Total Harga Item = Harga Satuan * Kuantitas
        $totalRoomBaseCost = 0;
        foreach ($items as $item) {
            $itemTotalCost = $item['price'] * $item['qty'];
            $itemOwner = $item['user'];

            // Validasi apakah nama pemesan terdaftar di array members sebelum dimasukkan ke rincian
            if (isset($memberBillBreakdown[$itemOwner])) {
                $memberBillBreakdown[$itemOwner]['baseCost'] += $itemTotalCost;
                $memberBillBreakdown[$itemOwner]['orderedItems'][] = [
                    'name' => $item['name'],
                    'price' => (float)$item['price'],
                    'qty' => (int)$item['qty'],
                    'total' => $itemTotalCost
                ];
                // Akumulasikan total makanan pokok seluruh ruangan
                $totalRoomBaseCost += $itemTotalCost;
            }
        }

        // 8. LANGKAH 2: Hitung pembagian biaya flat (dibagi rata sama besar ke semua anggota terdaftar)
        // Rumus: Biaya Flat per Anggota = Total Biaya Luar / Jumlah Anggota Terdaftar
        $memberCount = count($members);
        $flatFeeSharePerMember = $memberCount > 0 ? ($totalExtraFees / $memberCount) : 0;

        // 9. LANGKAH 3: Loop kedua untuk menghitung variabel proporsional & total akhir bersih masing-masing orang
        $totalRoomTax = 0;
        $totalRoomDiscount = 0;
        $calculatedRoomGrandTotal = 0;

        foreach ($members as $memberName) {
            $memberBase = $memberBillBreakdown[$memberName]['baseCost'];

            // A. RUMUS DISKON PROPORSIONAL (Mencegah error division by zero):
            // Rasio Kontribusi = Base Cost Individu / Total Base Cost Kamar
            // Porsi Diskon = Rasio Kontribusi * Total Diskon Toko
            $memberDiscountShare = 0;
            if ($totalRoomBaseCost > 0 && $totalDiscount > 0) {
                $memberDiscountShare = ($memberBase / $totalRoomBaseCost) * $totalDiscount;
            }

            // B. RUMUS PAJAK PROPORSIONAL:
            // Porsi Pajak = Base Cost Individu * (Persentase Pajak / 100)
            $memberTaxShare = $memberBase * ($taxPercent / 100);

            // C. RUMUS GRAND TOTAL INDIVIDU:
            // Grand Total per Anggota = Base Cost + Porsi Pajak - Porsi Diskon + Porsi Biaya Flat
            $memberGrandTotal = $memberBase + $memberTaxShare - $memberDiscountShare + $flatFeeSharePerMember;

            // Membulatkan hasil kalkulasi desimal ke nominal rupiah bulat terdekat
            $memberBillBreakdown[$memberName]['taxShare'] = round($memberTaxShare);
            $memberBillBreakdown[$memberName]['discountShare'] = round($memberDiscountShare);
            $memberBillBreakdown[$memberName]['extraFeeShare'] = round($flatFeeSharePerMember);
            $memberBillBreakdown[$memberName]['grandTotal'] = round($memberGrandTotal);

            // Akumulasi data ringkasan (summary) seluruh ruangan untuk pelaporan nota
            $totalRoomTax += $memberTaxShare;
            $totalRoomDiscount += $memberDiscountShare;
            $calculatedRoomGrandTotal += $memberGrandTotal;
        }

        // 10. Konfigurasi info rekening host yang akan ditampilkan di struk digital
        $hostTransferInfo = [
            'hostName' => $validatedData['hostName'] ?? $members[0], // fallback ke anggota pertama jika host tidak didefinisikan
            'paymentOptions' => [
                ['provider' => 'BCA', 'accountNumber' => '1234567'],
                ['provider' => 'Dana', 'accountNumber' => '0812345']
            ]
        ];

        // 11. Mengirim kembali data kalkulasi utuh dalam format JSON standar REST API
        return response()->json([
            'success' => true,
            'message' => 'Proses pembagian tagihan (split-bill) adil telah selesai dihitung!',
            'data' => [
                'restaurantName' => $validatedData['restaurantName'],
                'tableNumber' => $validatedData['tableNumber'],
                'transactionId' => 'QR-' . date('Ymd') . '-' . rand(1000, 9999),
                'date' => date('M d, Y'),
                'summary' => [
                    'totalBaseCost' => round($totalRoomBaseCost),
                    'totalTax' => round($totalRoomTax),
                    'totalDiscount' => round($totalDiscount),
                    'totalExtraFees' => round($totalExtraFees),
                    'grandTotal' => round($calculatedRoomGrandTotal)
                ],
                // Mengubah array asosiatif ke bentuk numerik biasa agar ramah bagi pembacaan DOM Javascript di frontend
                'membersBreakdown' => array_values($memberBillBreakdown),
                'transferInfo' => $hostTransferInfo
            ]
        ], 200);
    }


 /**
     * Mengarsipkan data split-bill yang telah selesai dikunci oleh host.
     * Metode ini melakukan sanitasi input dan mengembalikan struktur data ringkas
     * yang dioptimalkan untuk penyimpanan histori lokal browser (localStorage/IndexedDB).
     */
    public function archiveRoom(Request $request)
    {
        // 1. Aturan Validasi: Memastikan data minimal untuk menyusun item histori terpenuhi
        $rules = [
            'restaurantName' => 'required|string|max:255',
            'grandTotal'     => 'required|numeric|min:0',
            'members'        => 'required|array|min:1',
            'members.*'      => 'required|string|max:100',
        ];

        // 2. Jalankan validasi untuk menolak payload yang rusak atau manipulatif
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengarsipkan sesi. Struktur data tidak valid.',
                'errors'  => $validator->errors()
            ], 422); // HTTP 422: Unprocessable Entity
        }

        // 3. Ambil data aman yang sudah lolos uji validasi
        $sanitizedData = $validator->validated();

        // 4. Generate ID transaksi unik berformat #QR-2026-XXXX menggunakan fungsi bawaan PHP yang aman.
        // bin2hex(random_bytes(2)) menghasilkan 4 karakter alfanumerik acak yang aman dari tabrakan ID (collision).
        $uniqueHex = strtoupper(bin2hex(random_bytes(2)));
        $transactionId = '#QR-2026-' . $uniqueHex;

        // 5. Rancang struktur payload arsip yang dioptimalkan untuk performa histori browser
        $archivePayload = [
            'transactionId'   => $transactionId,
            
            // XSS Protection: Bersihkan string nama restoran dari tag HTML tak dikenal
            'restaurantName'  => strip_tags($sanitizedData['restaurantName']), 
            
            // Format tanggal ramah pengguna untuk langsung dicetak di kartu UI
            'date'            => date('M d, Y'), 
            
            // Nilai angka murni unix timestamp untuk kemudahan sorting urutan di sisi JS
            'timestamp'       => time(), 
            
            // Konversi total tagihan menjadi float agar meminimalkan bug pembulatan angka di JS
            'totalAmount'     => (float) $sanitizedData['grandTotal'],
            
            // Simpan jumlah anggota secara langsung agar tidak perlu melakukan fungsi `.length` berulang kali di UI
            'membersCount'    => count($sanitizedData['members']),
            
            // Menyimpan maksimal 3 nama anggota pertama sebagai representasi visual avatar di daftar riwayat
            'membersPreview'  => array_slice($sanitizedData['members'], 0, 3),
            
            // Status konfirmasi akhir sesi
            'status'          => 'COMPLETED' 
        ];

        // 6. Kirim respons konfirmasi sukses agar frontend aman menginstruksikan penyimpanan localStorage
        return response()->json([
            'success' => true,
            'message' => 'Room berhasil dikunci. Sesi ini telah resmi diarsipkan.',
            'archive' => $archivePayload
        ], 200);
    }
}
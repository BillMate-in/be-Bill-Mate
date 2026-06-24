<?php

use App\Http\Controllers\API\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - QRoom Split-Bill Engine
|--------------------------------------------------------------------------
| Di sini kita mendaftarkan endpoint-endpoint yang akan diakses oleh frontend
| menggunakan standard HTTP Fetch API. Semua rute di bawah ini otomatis 
| memiliki awalan prefix '/api' (contoh: /api/split-bill/calculate).
|
*/

/**
 * 1. ENDPOINT: KALKULASI TAGIHAN (SPLIT-BILL)
 * 
 * Dipicu oleh: Halaman Dashboard (dashboard.html).
 * Saat Host menekan tombol hijau "Selesai & Kunci Room", frontend akan melakukan HTTP Fetch POST 
 * ke endpoint ini untuk mengirimkan daftar pesanan mentah, daftar anggota, dan biaya luar.
 * Backend kemudian mengolah matematika pembagian tagihan dan mengembalikan rincian nota final.
 */
Route::post('/split-bill/calculate', [RoomController::class, 'calculateSplitBill']);

/**
 * 2. ENDPOINT: PENGARSIPAN ROOM (ARCHIVE SISSION)
 * 
 * Dipicu oleh: Halaman Nota Digital (nota.html).
 * Saat Host menekan tombol "Selesai & Keluar" di bagian bawah struk pembayaran, frontend akan 
 * mengirimkan ringkasan data tagihan ke endpoint ini untuk disanitasi dan ditandai ID unik.
 * Respons sukses dari backend menjadi lampu hijau bagi frontend untuk menyimpan data tersebut ke dalam localStorage (History).
 */
Route::post('/split-bill/archive', [RoomController::class, 'archiveRoom']);
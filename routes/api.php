<?php

use App\Http\Controllers\API\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/split-bill/calculate', [RoomController::class, 'calculateSplitBill']);
Route::post('/split-bill/archive', [RoomController::class, 'archiveRoom']);
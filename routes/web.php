<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RoomController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/docs', function () {
    return view('swagger');
});


<?php

use App\Http\Controllers\DiskIndex;
use App\Http\Controllers\DiskUpload;
use Illuminate\Support\Facades\Route;

Route::get('/', [DiskIndex::class, 'index']);
Route::post('/delete', [DiskIndex::class, 'delete']);

Route::get('/upload', [DiskUpload::class, 'show']);
Route::post('/upload', DiskUpload::class . '@upload');

<?php

use Illuminate\Support\Facades\Route;
use \Acitjazz\UploadEndpoint\Http\Controllers\UploadController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/media/show/{id}', [UploadController::class, 'getdata']);

Route::post('/media/upload', [UploadController::class, 'store']);
Route::delete('/media/destroy', [UploadController::class, 'destroy']);

Route::post('/media/upload-cloudinary', [UploadController::class, 'storeCloudinary']);
Route::post('/media/destroy-cloudinary', [UploadController::class, 'destroyCloudinary']);

Route::post('/media/upload-aws', [UploadController::class, 'storeAws']);
Route::post('/media/destroy-aws', [UploadController::class, 'destroyAws']);


Route::get('/media/{path}', [UploadController::class, 'show'])->where('path', '.*');
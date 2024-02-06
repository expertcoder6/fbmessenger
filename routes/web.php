<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FbmessengerController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [App\Http\Controllers\FbmessengerController::class, 'index'])->name('home');

Route::get('/ajax_messages/{conversation_id}', [App\Http\Controllers\FbmessengerController::class, 'getAllMessages'])->name('ajax_messages');

Route::post('/ajax_send_message' , [App\Http\Controllers\FbmessengerController::class, 'sendMessage'])->name('ajax_send_message');

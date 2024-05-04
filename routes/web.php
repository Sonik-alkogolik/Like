<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserProfile\UserProfileController;
use App\Http\Controllers\ApiVkCustomController;
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


Auth::routes();
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/profile', [UserProfileController::class, 'showProfile'])->middleware('auth');
Route::get('/vk-serfing', [ApiVkCustomController::class, 'getUserById'])->name('vk.vk-user');
Route::post('/vk-save-link', [ApiVkCustomController::class, 'saveVkLink'])->name('vk.save-link');
Route::delete('/vk-delete-link', [ApiVkCustomController::class, 'deleteVkLink'])->name('vk.delete-link');



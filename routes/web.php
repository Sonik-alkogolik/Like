<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserProfile\UserProfileController;
use App\Http\Controllers\ApiVkCustomController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\VkController;

Auth::routes();
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/profile', [UserProfileController::class, 'showProfile'])->middleware('auth');
Route::get('/vk-serfing', [ApiVkCustomController::class, 'getUserById'])->name('vk.vk-user');
Route::get('/vk/auth', [ApiVkCustomController::class, 'index'])->name('vk.auth');
Route::get('/vk/auth/callback', [ApiVkCustomController::class, 'callback'])->name('vk.auth.callback');
Route::post('/vk-save-link', [ApiVkCustomController::class, 'saveVkLink'])->name('vk.save-link');
Route::post('/vk-delete', [ApiVkCustomController::class, 'deleteVkLink'])->name('vk.delete');
Route::post('/get-groups', [VkController::class, 'getGroups']);
Route::post('/check-group', [VkController::class, 'checkGroup']);
Route::post('/get-groups-and-check-group', [VkController::class, 'getGroupsAndCheckGroup']);
Route::post('/save-group', [VkController::class, 'saveGroup'])->name('save-group');





<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::resource('admin/contacts', AdminController::class)->only(['show', 'destroy']);
    Route::resource('admin/tags', TagController::class)->only(['store', 'edit', 'update', 'destroy']);
});
Route::get('/contacts', fn() => '入力フォーム（準備中）')->name('contacts.form');
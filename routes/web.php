<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/admin', fn() => '管理者画面（準備中）')->name('/admin')
    ->middleware('auth');
Route::get('/contacts', fn() => '入力フォーム（準備中）')->name('contacts.form');
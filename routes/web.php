<?php

use Illuminate\Support\Facades\Route;

Route::get('/portal/dashboard', function () {
    return redirect('/portal');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', function () {
    return redirect('/portal');
});

Route::get('/login', function () {
    return redirect('/portal/login');
})->name('login');

<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/panel-productos');
});

// Panel web que consume la API
Route::get('/panel-productos', function () {
    return view('panel-productos');
});

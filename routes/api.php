<?php

use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

// CRUD de productos -> /api/productos
Route::apiResource('productos', ProductoController::class);

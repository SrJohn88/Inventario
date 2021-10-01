<?php

use App\Http\Controllers\Admin\MarcaController;
use App\Http\Controllers\Admin\HomeController;
use Illuminate\Support\Facades\Route;

//rutas para opciones administrativas
Route::get('',[HomeController::class,'index']);

Route::resource('marcas', MarcaController::class)->names('admin.marcas');
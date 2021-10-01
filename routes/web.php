<?php

use App\Http\Controllers\Admin\MarcaController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EntidadController;
use App\Http\Controllers\RubroController;

Auth::routes();
Route::get('/',HomeController::class);
Route::get('/home', [HomeController::class, 'index'])->name('home');

//RUTAS PARA MARCAS
Route::get('/marcas',[MarcaController::class,'index'])->name("admin.marcas");
Route::get('/marcas/list',[MarcaController::class,'getMarca']);
Route::post('/marcas/save',[MarcaController::class,'store']);
Route::put('/marcas/update',[MarcaController::class,'update']);
Route::put('/marcas/change',[MarcaController::class,'change']);

// ENTIDADES
Route::get('/entidades',[EntidadController::class,'index'])->name("entidades.index");

//API ENTIDADES
Route::get('/Api/entidades',[EntidadController::class,'obtenerEntidades'])->name("entidadesApi.obtener");
Route::post('/Api/entidades',[EntidadController::class,'save'])->name("entidadesApi.save");
Route::post('/Api/entidades/{entidad}/edit',[EntidadController::class,'update'])->name("entidadesApi.update");
Route::delete('/Api/entidades/{entidad}',[EntidadController::class,'delete'])->name("entidadesApi.delete");

//RUBROS
Route::get('/rubros',[RubroController::class,'index'])->name("rubros.index");



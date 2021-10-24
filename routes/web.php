<?php

use App\Http\Controllers\Admin\MarcaController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EntidadController;
use App\Http\Controllers\RubroController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProcedenciaController;

use App\Http\Controllers\MovimientoController;

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
Route::delete('/Api/entidades/{entidad}/{accion}',[EntidadController::class,'delete'])->name("entidadesApi.delete");

//RUBROS
Route::get('/rubros',[RubroController::class,'index'])->name("rubros.index");

//API RUBROS
Route::get('/Api/rubros',[RubroController::class,'getRubros'])->name("rubros.get");
Route::post('/Api/rubros',[RubroController::class,'save'])->name("rubrosApi.save");
Route::post('/Api/rubros/{rubro}/edit',[RubroController::class,'update'])->name("rubrosApi.update");
Route::delete('/Api/rubros/{rubro}/{accion}',[RubroController::class,'delete'])->name("rubrosApi.delete");

// CUENTAS
Route::get('/cuentas',[CuentaController::class,'index'])->name("cuentas.index");

// API CUENTAS
Route::get('/Api/cuentas',[CuentaController::class,'getCuentas'])->name("cuentas.get");
Route::post('/Api/cuentas',[CuentaController::class,'save'])->name("cuentas.save");
Route::post('/Api/cuentas/{cuenta}/edit',[CuentaController::class,'update'])->name("cuentas.update");
Route::get('/Api/cuentas/{cuenta}/{accion}',[CuentaController::class,'delete'])->name("cuentas.delete");


//UBICACIONES
Route::get('/ubicaciones',[UbicacionController::class,'index'])->name("ubicaciones.index");

// UBICACIONES API
Route::get('/Api/ubicaciones',[UbicacionController::class,'getUbicaciones'])->name("ubicaciones.get");
Route::post('/Api/ubicaciones',[UbicacionController::class,'save'])->name("ubicaciones.save");
Route::post('/Api/ubicaciones/{ubicacion}/edit',[UbicacionController::class,'update'])->name("ubicaciones.update");
Route::delete('/Api/ubicaciones/{ubicacion}/{accion}',[UbicacionController::class,'delete'])->name("ubicaciones.delete");


// PROCEDENCIAS
Route::get('/Api/procedencias',[ProcedenciaController::class,'getProcedencias'])->name("procedencias.get");

//INVENTARIO
Route::get('/inventario/crear',[InventarioController::class,'crear'])->name("inventario.crear");
Route::get('/inventario/index',[InventarioController::class,'index'])->name("inventario.index");


//INVENTARIO API
Route::get('Api/inventario/activos',[InventarioController::class,'getActivos'])->name("inventario.get");
Route::get('Api/inventario/{id}/activos',[InventarioController::class,'getOneActivo'])->name("inventario.getOne");
Route::post('Api/inventario/save',[InventarioController::class,'save'])->name("inventario.save");
Route::post('Api/inventario/{inventario}/edit',[InventarioController::class,'update'])->name("inventario.update");


//MOVIMIENTO DE INVENTARIO
Route::get('/inventario/movimientos',[ MovimientoController::class, 'crear'])->name("movimiento.index");

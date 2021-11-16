<?php

use App\Http\Controllers\CargoController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MarcaController;
use App\Http\Controllers\EntidadController;
use App\Http\Controllers\RubroController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProcedenciaController;

use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\HistorialInventarioController;
use App\Http\Controllers\HistorialMovimientoController;
use App\Http\Controllers\TipoMovimientoController;
use App\Http\Controllers\UserController;
use App\Models\HistorialInventario;
use App\Models\HistorialMovimiento;

Auth::routes();
Route::get('/',HomeController::class);
Route::get('/home', [HomeController::class, 'index'])->name('home');

//RUTAS PARA MARCAS
Route::get('/marcas',[MarcaController::class,'index'])->name("admin.marcas");

// API MARCAS
Route::get('/Api/marcas',[MarcaController::class,'obtenerMarcas'])->name("MarcaApi.obtener");
Route::post('Api/marcas',[MarcaController::class,'save']);
Route::post('/Api/marcas/{marca}/edit', [MarcaController::class,'update']);
Route::delete('/Api/marcas/{marca}/{accion}', [ MarcaController::class, 'delete'])->name('marcasApi.delete');

// ENTIDADES
Route::get('/entidades',[EntidadController::class,'index'])->name("entidades.index");

//API ENTIDADES
Route::get('/Api/entidades',[EntidadController::class,'obtenerEntidades'])->name("entidadesApi.obtener");
Route::post('/Api/entidades',[EntidadController::class,'save'])->name("entidadesApi.save");
Route::post('/Api/entidades/{entidad}/edit',[EntidadController::class,'update'])->name("entidadesApi.update");
Route::delete('/Api/entidades/{entidad}/{accion}',[EntidadController::class, 'delete' ])->name("entidadesApi.delete");

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
Route::get('/inventario/detalle',[ InventarioController::class, 'detalle'])->name("movimiento.detalle");


//INVENTARIO API
Route::get('Api/inventario/activos',[InventarioController::class,'getActivos'])->name("inventario.get");
Route::get('Api/inventario/{id}/activos',[InventarioController::class,'getOneActivo'])->name("inventario.getOne");
Route::post('Api/inventario/save',[InventarioController::class,'save'])->name("inventario.save");
Route::post('Api/inventario/{inventario}/edit',[InventarioController::class,'update'])->name("inventario.update");

Route::get('Api/inventario/obtenerMovimientos',[InventarioController::class,'obtenerHistorialMovimientos'])->name("inventarioMovimiento.get");


// TIPOS DE MOVIMIENTOS
Route::get('Api/movimientos/tipos',[ TipoMovimientoController::class,'obtenerTiposMovimientos'])->name("tipoMovimientos.get");


// EMPLEADOS
Route::get('/empleados',[ EmpleadoController::class, 'index'])->name("empleados.index");

// API EMPLEADOS
Route::get('Api/empleados',[EmpleadoController::class,'obtenerEmpleados'])->name("empleados.get");
Route::get('Api/cargos',[CargoController::class,'obtenerCargos'])->name("cargos.get");
Route::post('Api/empleados',[EmpleadoController::class,'save'])->name("empleados.save");
Route::post('Api/empleados/{empleado}/edit',[EmpleadoController::class,'update'])->name("empleados.update");
Route::delete('Api/empleados/{empleado}/{acccion}',[EmpleadoController::class,'delete'])->name("empleados.delete");


//MOVIMIENTO DE INVENTARIO
Route::get('/inventario/movimientos',[ MovimientoController::class, 'index'])->name("movimiento.index");
Route::get('/inventario/movimientos/crear',[ MovimientoController::class, 'crear'])->name("movimiento.crear");
Route::get('/inventario/movimientos/detalle',[ MovimientoController::class, 'detalleMovimiento'])->name("movimiento.detalle");


//MOVIMIENTO API
Route::get('/Api/inventario/movimientos',[ MovimientoController::class, 'obtenerMovimientos'])->name("movimiento.get");



Route::post('/Api/inventario/movimientos/save',[ MovimientoController::class, 'save'])->name("movimiento.save");
Route::get('/Api/inventario/{movimiento}/movimientos',[ MovimientoController::class, 'obtenerDetMovimiento'])->name("detMovimiento.get");
Route::post('/Api/inventario/movimientos/update/{movimiento}',[ MovimientoController::class, 'updateMovimiento'])->name("movimiento.update");


// HISTORIAL DE INVENTARIO
Route::get('/Api/historial/respaldos/{inventario}/{desde}/{hasta}',[ HistorialInventarioController::class, 'obtenerHistorialActivo'])->name("historialInventario.get");
Route::get('/Api/historial/movimientos/{inventario}/{desde}/{hasta}',[ HistorialMovimientoController::class, 'obtenerHistorialMovimientos'])->name("historialMovimiento.get");


// USUARIOS
Route::get('/usuarios',[ UserController::class, 'index'])->name("usuarios.index");

// API USUARIOS
Route::get('/Api/usuarios',[ UserController::class, 'obtenerUsuarios'])->name("usuarios.get");


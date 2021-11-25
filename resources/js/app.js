/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

import Vue from 'vue';
import Vuetify from 'vuetify';

import 'vue2-datepicker/index.css';
import Datepicker from 'vue2-datepicker';
//import swal from 'sweetalert2';
//window.Swal = swal;
window.Swal = require('sweetalert2');

//import VueNumberInput from '@chenfengyuan/vue-number-input';


Vue.use(Vuetify);


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

//Vue.component('example-component', require('./components/ExampleComponent.vue').default);
import 'sweetalert2/dist/sweetalert2.min.css';

Vue.component('marcas', require('./components/Marcas.vue').default);
Vue.component('entidades', require('./components/Entidades.vue').default);
Vue.component('rubro', require('./components/Rubro.vue').default);
Vue.component('cuentas', require('./components/Cuentas.vue').default);
Vue.component('ubicaciones', require('./components/Ubicaciones.vue').default);

// INVENTARIO
Vue.component('inventario-crear', require('./components/Inventario/formularioCrear.vue').default);
Vue.component('inventario-index', require('./components/Inventario/inventario.vue').default);
Vue.component('inventario-detalle', require('./components/Inventario/inventarioDetalle.vue').default);


// MOVIMIENTOS DE INVENTARIO
Vue.component('movimiento-index', require('./components/Inventario/Movimientos/movimientoIndex.vue').default);
Vue.component('movimiento-crear', require('./components/Inventario/Movimientos/formularioCrear.vue').default);
Vue.component('update-movimiento', require('./components/Inventario/Movimientos/updateMovimiento.vue').default);

// DESCARGOS DE INVENTARIO
Vue.component('formulario-descargos', require('./components/Inventario/Descargos/formularioDescargos.vue').default);
Vue.component('descargos', require('./components/Inventario/Descargos/descargos.vue').default);

Vue.component('activos-descargados', require('./components/Inventario/Descargos/ActivosDescargados.vue').default);


// TIPOS DE DESCARGOS
Vue.component('tipo-descargo', require('./components/Inventario/Descargos/TipoDescargo.vue').default);

// CARGOS
Vue.component('cargo', require('./components/Cargo.vue').default);


//EMPLEADOS
Vue.component('empleados', require('./components/Empleados.vue').default);

// USUARIOS
Vue.component('usuarios', require('./components/Usuarios.vue').default);
Vue.component('formulario-usuario', require('./components/FormularioUsuario.vue').default);

// REPORTES
Vue.component('inventario-general', require('./components/Reportes/InventarioGeneral.vue').default);
Vue.component('reporte-procedencia', require('./components/Reportes/ReporteProcedencia.vue').default);
Vue.component('reporte-descargos', require('./components/Reportes/ReporteDescargos.vue').default);
Vue.component('reporte-compras', require('./components/Reportes/ReporteCompras.vue').default);
Vue.component('reporte-movimientos', require('./components/Reportes/ReporteMovimientos.vue').default);


import auth from './mixins/auth';
Vue.mixin(auth);

Vue.component('date-picker',Datepicker)

 const app = new Vue({
  el: '#app',
  vuetify: new Vuetify(),
  data:{
      option : 0,
      notifications: []
  },
});



@extends('adminlte::page') 

@section('title', 'Reporte Inventario')

@section('content_header')
    
@stop

@section('content')
    <!-- <p>Welcome to this beautiful admin panel.</p> -->
    @parent
    <div id="app">
        <v-app>
            <template>
                <reporte-descargos></reporte-descargos>
            </template>
        </v-app>
    </div>
    
@stop

@section('css')
    <!-- <link rel="stylesheet" href="/css/admin_custom.css"> -->
    <link href="{{asset('css/app.css')}}" rel="stylesheet">
@stop

@section('js')
   <!-- <script> console.log('Hi!'); </script> -->
   <script src="{{asset('js/app.js')}}"></script>
@stop
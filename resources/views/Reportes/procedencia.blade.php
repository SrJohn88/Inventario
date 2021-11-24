@extends('adminlte::page') 

@section('title', 'Reporte Procedencia')

@section('content_header')
    
@stop

@section('content')
    <!-- <p>Welcome to this beautiful admin panel.</p> -->
    @parent
    <div id="app">
        <v-app>
            <template>
                <reporte-procedencia></reporte-procedencia>
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
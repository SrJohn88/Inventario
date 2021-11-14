@extends('adminlte::page') 

@section('title', 'Movimientos')

@section('content_header')
    
@stop

@section('content')
    <!-- <p>Welcome to this beautiful admin panel.</p> -->
    @parent
    <div id="app">
        <v-app>
            <template>
                <update-movimiento></update-movimiento>
            </template>
        </v-app>
    </div>
    
@stop

@section('css')
    <link href="{{asset('css/app.css')}}" rel="stylesheet">
@stop

@section('js')
   <!-- <script> console.log('Hi!'); </script> -->
   <script src="{{asset('js/app.js')}}"></script>
@stop
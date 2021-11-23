@extends('adminlte::page') 

@section('title', 'Usuarios')

@section('content_header')
    
@stop

@section('content')
    @parent
    <div id="app">
        <v-app>
            <template>
                <formulario-usuario></formulario-usuario>
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
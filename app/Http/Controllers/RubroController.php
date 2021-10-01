<?php

namespace App\Http\Controllers;
use App\Models\Rubro;

use Illuminate\Http\Request;

class RubroController extends Controller
{
    function index ()
    {
        return view('rubros.index');
    }

    function obtenerRubros ()
    {

    }

    function save ( Request $request )
    {

    }

    function update (Request $request, Rubro $rubro ) 
    {

    }

    function delete ( Rubro $rubro )
    {

    }
}

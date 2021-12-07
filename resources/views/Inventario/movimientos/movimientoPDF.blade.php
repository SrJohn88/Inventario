<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">   
    
    <title>Reporte Movimiento</title>
</head>
<body>

    <div class="header">
        <h3>INSTITUTO TECNOLOGICO DE CHALATENANGO</h3>
        <h4>ASOCIACION AGAPE DE EL SALVADOR</h4>
        <hr>
    </div>

    <div class="tipoMovimiento">
        <h4>CONTROL DE MOVIMIENTOS DE INVENTARIO Y OTROS</h4>
        <ul>
            @foreach ($tiposMovimientos as $tipo)
                <li>{{ $tipo->tipo }} ( {{ $tipo->id == $movimiento->tipoMovimiento->id ? 'X' : ''}} )</li>
            @endforeach
        </ul>
    </div>

    <div class="datos">
        <table border="0" cellspacing="0" cellpadding="0" class="primera">
            <thead>
                <tr>
                    <th>RUBRO</th>
                    <th>CODIGO</th>
                    <th>DESCRIPCIÓN DEL ACTIVO</th>                
                    <th>ESTADO O FALLA</th>
                    <th>REINGRESO</th>
                    <th>OBSERVACIÓN</th>
                </tr>                
            </thead>
            <tbody>
                @foreach ($movimiento->inventario as $activo )
                    <tr>
                        <td>{{$activo->rubro->rubro}}</td>
                        <td>{{$activo->codigo}}</td>
                        <td>{{$activo->descripcion}}</td>
                        <td>{{ $activo->pivot->falla }}</td>
                        @if( $activo->pivot->recibido )
                            <td>{{ $activo->pivot->updated_at ?? '' }}</td>
                        @else
                            <td></td>
                        @endif
                        
                        <td> {{ $activo->pivot->observaciones }}</td>                
                    </tr>   
                @endforeach                
            </tbody>
        </table>
    </div>

    <div class="detalleSalido">
        <table border="0" cellspacing="0" cellpadding="0" id="detalle">
            <tbody>
                @if( $movimiento->tipo_id == 4 )
                    <tr>
                        <td>Se trasalada a:</td>
                        <td>{{ $movimiento->detalleSalida }}</td>
                    </tr>
                @else
                    <tr>
                        <td>Se traslada a:</td>                       
                        <td colspan="2"> {{ $movimiento->ubicacion->ubicacion ?? '' }}</td>                        
                    </tr>
                @endif
                
                <tr>
                    <td>Fecha de reingreso:</td>                       
                    <td>{{ $movimiento->fechaReingreso }}</td> 
                    <td>Fecha de registro:</td>                       
                    <td>{{ $movimiento->created_at }}</td>                        
                </tr>
                <tr>
                    <td>Observaciones:</td>                       
                    <td colspan="2"> {{ $movimiento->descripcion ?? '' }}</td>                        
                </tr>
                <tr>
                                           
                </tr>
            </tbody>
    </div>
    
    <br>
    <div class="firmas">
       
        <div class="firma">
            <table border="0" cellspacing="0" cellpadding="0" class="tblFirma">
                <tbody>
                    <tr>
                        <td colspan="2">Entregado por:</td>                        
                    </tr>
                    <tr>
                        <td>Nombre:</td>
                        <td>{{ $movimiento->user->name.' '. $movimiento->user->lastName}}</td>
                    </tr>
                    <tr>
                        <td>Cargo</td>
                        <td>CONTADOR</td>
                    </tr>
                    <tr>
                        <td>Firma</td>
                        <td>____________</td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="firma">
            <table border="0" cellspacing="0" cellpadding="0" class="tblFirma">
                <tbody>
                    <tr>
                        <td colspan="2">Aprobado por:</td>                        
                    </tr>
                    <tr>
                        <td>Nombre:</td>
                        @if( $movimiento->aprueba )
                            <td>{{ $movimiento->aprueba->nombre.''.$movimiento->aprueba->apellido}}</td>
                        @else 
                            <td></td>
                        @endif
                        
                    </tr>
                    <tr>
                        <td>Cargo</td>
                        @if( $movimiento->aprueba )
                            <td>{{ $movimiento->aprueba->cargo->cargo}}</td>
                        @else
                            <td></td>    
                        @endif                        
                    </tr>
                    <tr>
                        <td>Firma</td>
                        <td>____________</td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="firma">
            <table border="0" cellspacing="0" cellpadding="0" class="tblFirma">
                <tbody>
                    <tr>
                        <td colspan="2">Autorizado agencia:</td>                        
                    </tr>
                    <tr>
                        <td>Nombre</td>
                        @if ( $movimiento->aprobadoGerencia)
                            <td> {{ $movimiento->aprobadoGerencia->nombre.' '.$movimiento->aprobadoGerencia->apellido}}</td>
                        @else
                            <td></td>
                        @endif
                    </tr>
                    <tr>
                        <td>Cargo</td>
                        @if ( $movimiento->aprobadoGerencia)
                            <td> {{ $movimiento->aprobadoGerencia->cargo->cargo }}</td>
                        @else
                            <td></td>
                        @endif
                    </tr>
                    <tr>
                        <td>Firma</td>
                        <td>____________</td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="firma">
            <table border="0" cellspacing="0" cellpadding="0" class="tblFirma">
                <tbody>
                    <tr>
                        <td colspan="2">Recibe:</td>                        
                    </tr>
                    <tr>
                        <td>Nombre:</td>
                        @if ( $movimiento->recibe )
                            <td> {{ $movimiento->recibe->nombre.' '.$movimiento->recibe->apellido }}</td>
                        @else
                            <td></td>
                        @endif
                    </tr>
                    <tr>
                        <td>Cargo</td>
                        @if ( $movimiento->recibe )
                            <td> {{ $movimiento->recibe->cargo->cargo }}</td>
                        @else
                            <td></td>
                        @endif
                    </tr>
                    <tr>
                        <td>Firma</td>
                        <td>____________</td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>

    
</body>
</html>

<style>

    div.header {
        font-size: 18px;
        text-align: center;
    }

    div.header h3 {
        margin: 0px;
    }

    div.header h4 {
        margin: 0px;
    }

    .tipoMovimiento {
        width: 100%;
        margin: auto;
        text-align: center;
    }
    .tipoMovimiento ul
    {
        text-align: center;
        padding-right: 0px;
        padding-left: 0px;
        padding-bottom: 0px;
        padding-top: 0px;
        margin: 0px;
    }
    .tipoMovimiento li
    {
        list-style-type: none;        
        padding-right: 5px;
        display: inline-block;
    }

    .datos {
        margin-top: 10px;
    }

    table {
        font-size: 12px;
        width: 100%;
        border: 1px;
        border-collapse: collapse;
    }

    th {
        background-color: #04AA6D;
        color: white;
    }
    .primera th, .primera td {
        padding: 5px;
        text-align: left;
        border: 1px solid black;
    }

    .detalleSalido {
        margin-top: 10px;
    }

    table#detalle {
        margin-top: 15px;
        font-size: 16px;
        border: 0px;
        border-bottom: 1px solid black;
    }

    #detalle td {
        padding: 8px;
    }

    table.tblFirma {
        font-size: 16px;
        height: 150px;    
    }

    .tblFirma td {
        padding: 8px;    
        text-align: left;
    }

    div.firma {
        height: 150px;
        width: 220px;
        float: left;
        margin: 20px;
    }
</style>
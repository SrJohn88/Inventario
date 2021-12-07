<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <title>Reporte de revision</title>
</head>
<body>

    <div class="header">
        <h3>ASOCIACION AGAPE DE EL SALVADOR</h3>
        <h4>LISTADO PARA TOMA FISICA</h4>
        <hr>
    </div>


    <div class="revision">
        <table border="0" cellspacing="0" cellpadding="0" id="revision">
            <tbody>
                <tr>
                    <th>Nombre de la revisión</th>
                    <th>Elaborado por:</th>
                    <th>Fecha de registro</th>
                </tr>
                <tr>
                    <td>{{ $revision->nombre }}</td>
                    <td> {{ $revision->user->name. ' '.$revision->user->lastName }}</td>
                    <td>{{ $revision->created_at }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="activos">
        <h4>INVENTARIO GENERAL | Total activos : [ {{ count( $revision->inventario) }} ]</h4>
        <table border="0" cellspacing="0" cellpadding="0" id="activos">
            <tbody>
                <tr>
                    <th>FECHA ADQUISICION</th>
                    <th>DESCRIPCIÓN</th>
                    <th>CODIGO</th>
                    <th>ESTADO</th>
                    <th>UBICACION</th>
                    <th>REVISADO</th>
                    <th>UBICACIÓN CORRECTA</th>
                    <th>OBSERVACIÓN</th>
                </tr>
                @foreach ($revision->inventario as $activo )
                    <tr>
                        <td>{{ $activo->fecha_adquision }}</td>
                        <td>{{ $activo->descripcion }}</td>
                        <td>{{ $activo->codigo }}</td>
                        <td>{{ $activo->estado->estado }}</td>
                        <td> {{ $activo->ubicacion->ubicacion }}</td>
                        <td>{{ $activo->pivot->revisado ? 'SI' : '' }}</td>
                        <td>{{ $activo->pivot->esCorrecto ? 'SI' : '' }}</td>
                        <td>{{ $activo->pivot->observacion }}</td>
                    </tr>    
                @endforeach                
            </tbody>
        </table>
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

    div.activos {
        margin-top: 25px;

    }
    table#revision {
        font-size: 12px;
        width: 100%;
        border: 1px;
        border-collapse: collapse;
    }

    table#revision th {
        padding: 8px;
        text-align: center;
        border: 1px solid black;
        background-color: #44494e;
        color: white;
    }

    table#revision td {
        padding: 5px;
        text-align: center;
        border: 1px solid black;
    }

    table#activos {
        font-size: 12px;
        width: 100%;
        border: 1px;
        margin-top: 
        border-collapse: collapse;
    }

    table#activos th {
        padding: 8px;
        text-align: center;
        border: 1px solid black;
        background-color: #3651c7;
        color: white;
    }

    table#activos td {
        padding: 5px;
        text-align: center;
        border: 1px solid black;
    }
</style>
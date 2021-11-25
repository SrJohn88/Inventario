<template>
    <div class="content">
        <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">

            <v-overlay :value="loader" :z-index="'99999999'">
                <v-progress-circular
                    indeterminate
                    size="80"
                    color="grey darken-4"
                ></v-progress-circular>
            </v-overlay>

            <v-card>
                <v-card-title>
                    Movimientos de inventario
                </v-card-title>

                <v-data-table
                    :headers="encabezados"
                    :items="activos"
                    :footer-props="{
                        'items-per-page-options': [15, 25, 35, 45],
                        'items-per-page-text': 'Registros Por Página',
                    }"
                    :items-per-page="10"
                    :search="buscarActivo"
                    multi-sort
                    no-data-text="No hay activos para mostrar"
                    class="elevation-1"
                >

                    <template v-slot:top>
                        <v-card-title>
                            <v-row>
                                <v-col cols="6" align="right">
                                    <v-autocomplete
                                        no-data-text="No hay tipos de movimientos"
                                        style="width: 75% !important"
                                        v-model="tipoMovimiento"
                                        :items="tiposMovimientos"
                                        required
                                        label="Seleccione el tipo movimientos"
                                        item-text="tipo"
                                        item-value="id"
                                        return-object
                                        clearable
                                        :menu-props="{ closeOnClick: true }"
                                    ></v-autocomplete>
                                </v-col>
                                <v-col cols="6" align="center">
                                    <v-radio-group v-model="tipoReporte" row>
                                        <v-radio
                                            label="Pendientes"
                                            value="PENDIENTES"                  
                                        ></v-radio>
                                        <v-radio
                                            label="Completados"
                                            value="COMPLETOS"                    
                                        ></v-radio>
                                    </v-radio-group>
                                </v-col>
                                <v-col cols="6" align="right">                
                                    <date-picker
                                        v-model="desde"
                                        :editable="false"
                                        lang="es"
                                        style="width: 75% !important"
                                        input-class="form-control"
                                        class="mt-2"
                                        value-type="format"
                                        format="DD-MM-YYYY"
                                        placeholder="Fecha Desde"
                                        >
                                    </date-picker>
                                </v-col>
                                <v-col cols="6" align="left">                    
                                    <date-picker
                                        v-model="hasta"
                                        :editable="false"
                                        lang="es"
                                        style="width: 75% !important"
                                        input-class="form-control"
                                        class="mt-2"
                                        value-type="format"
                                        format="DD-MM-YYYY"
                                        placeholder="Fecha Hasta"
                                        >
                                    </date-picker>
                                </v-col>                                
                            </v-row>
                        </v-card-title>  

                        <v-toolbar flat color="white">
                            <div class="flex-grow-1"></div>
                            <v-dialog
                                v-model="modalReporte"
                                persistent
                                max-width="700px"
                            >
                            <template v-slot:activator="{ on }">
                            <div class="text-center ma-4">
                                <v-btn
                                    elevation="10"
                                    color="primary"
                                    style="font-size: 10px"
                                    
                                    small
                                    :loading="loader"
                                    :disabled="loader"
                                    @click="generar"
                                    >
                                    GENERAR REPORTES
                                </v-btn>

                                <v-btn
                                    elevation="10"
                                    color="red"
                                    style="font-size: 10px"
                                    small
                                    dark
                                    :loading="loader"
                                    :disabled="loader"
                                    @click="exportarExcel"
                                    >
                                    EXPORTAR EN EXCEL                                
                                </v-btn>
                            </div>
                        </template>
                        </v-dialog>
                        </v-toolbar>                  
                    </template>

                </v-data-table>

                <template>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12"  align="right">
                                <v-text-field
                                    label="Precio total:"
                                    outlined
                                    prefix="$"
                                    style="width: 20% !important"
                                    input-class="form-control"
                                    v-model="precioTotal"
                                    readonly
                                ></v-text-field>                                                                    
                            </v-col>
                        </v-row>
                    </v-card-text>
                </template>


            </v-card>

        </div>
    </div>
</template>

<script>

import { json2excel } from 'js2excel';

export default {
    data()
    {
        return {
            tipoReporte: null,
            loader: false,
            modalReporte: false,
            buscarActivo: '',
            encabezados: [
                { text: "Código", value: "codigo", sortable: false },
                { text: "Descripción", value: "descripcion", sortable: false },
                { text: "Marca", value: "marca.marca", sortable: false },
                { text: "Modelo", value: "modelo", sortable: false },
                { text: "Serie", value: "serie", sortable: false },
                { text: "Rubro", value: "rubro.rubro", sortable: false },
                { text: "Precio", value: "precio", sortable: false },
                { text: "Ubicación", value: "ubicacion.ubicacion", sortable: false },
                { text: "Tipo Movimiento", value: "tipoMovimiento",sortable: false,},
                { text: "Fecha Movimiento", value: "fechaCreacion", sortable: false },
            ],
            tiposMovimientos: [],
            tipoMovimiento: null, desde: '', hasta: '',
            activos: [],
            precioTotal: 0
        }
    },
    mounted()
    {
        this.obtenerTipoMovimientos()
    },
    methods: {
        obtenerTipoMovimientos()
        {
            axios
                .get("/Api/movimientos/tipos")
                .then(({ data: { tiposMovimientos } }) => {
                    this.tiposMovimientos = tiposMovimientos.filter( tipo => tipo.eliminado == false )                
                })
                .catch(console.error);
        },
        generar()
        {
            if ( this.tipoMovimiento && this.desde && this.hasta && this.tipoReporte)
            {
                this.loader = true
                
                const path = `/Api/reportes/inventario/movimientos/${this.tipoMovimiento.id}/${this.desde}/${this.hasta}/${this.tipoReporte}`
                this.activos = []
                this.precioTotal = 0

                axios
                    .get( path )
                    .then( ( { data: { activos } } ) => {
                        this.loader = false
                        activos.forEach( activo => {
                                        
                            if ( activo.precio )
                            {
                                this.precioTotal += parseFloat( activo.precio)
                            }
                            this.activos.push( { ...activo } )
                        })
                        this.precioTotal = this.precioTotal.toFixed(2)
                    }).catch( () => {
                        this.loader = false

                        
                    })

            } else 
            {
                Swal.fire({
                    icon: 'warning',
                    title: '¡IMPORTANTE!',
                    text: 'Completa los campos, son de caracter obligatorio',
                })
            }

        },
        exportarExcel()
        {
            if ( this.activos.length > 0 )
            {
                this.loader = true

                let data = []

                this.activos.forEach( activo => {
                    data.push({
                    id: activo.id,
                    codigo: activo.codigo,
                    serie: activo.serie,
                    descripcion: activo.descripcion,
                    marca: activo.marca ? activo.marca.marca : '',
                    modelo: activo.modelo,
                    procedencia: activo.procedencia.procedencia,
                    entidad: activo.entidad ? activo.entidad.entidad : '',
                    cuenta: activo.cuenta ? activo.cuenta.cuenta : '',
                    precio: activo.precio ? activo.precio : '',
                    rubro: activo.rubro ? activo.rubro.rubro : '',
                    ubicacion: activo.ubicacion ?  activo.ubicacion.ubicacion : '',
                    fechaAdquision: activo.fecha_adquision,
                    estado: activo.estado ? activo.estado.estado : '',
                    observacion: activo.observacion,
                    idMovimiento: activo.idMovimiento,
                    tipoMovimiento: activo.tipoMovimiento,
                    fechaMovimiento: activo.fechaCreacion,
                    fechaRegistro: activo.created_at,
                    ultimaActualizacion: activo.updated_at
                    })
                })
                
                data.push(
                   { precio: `Precio total: ${ this.precioTotal}`}
                )

                try {
                    
                    json2excel({
                    data,
                    name: 'ReporteMovimientos',
                    formateDate: 'yyyy/mm/dd'
                    })
                    this.loader = false
                    
                } catch (e) {
                    this.loader = false
                    console.error('export error');
                }

            } else 
            {
                Swal.fire({
                    icon: 'warning',
                    title: '¡IMPORTANTE!',
                    text: 'No hay datos para exportar',
                })
            }
        }
    }
}
</script>

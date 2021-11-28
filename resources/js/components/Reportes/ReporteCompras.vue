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
                    Inventario por compras
                    <div class="flex-grow-1"></div>          
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
                    <template>
                        <v-card-text>
                            <v-row>
                                <v-col cols="6"  align="center">
                                    <v-autocomplete
                                    style="width: 75% !important"
                                    input-class="form-control"
                                    class="mt-2"
                                    v-model="reporte.cuenta"
                                    :items="cuentas"
                                    required
                                    label="Cuentas"
                                    item-text="cuenta"
                                    item-value="id"
                                    return-object
                                    clearable
                                    :menu-props="{
                                        closeOnClick: true,
                                    }"
                                    ></v-autocomplete>
                                </v-col>

                                <v-col cols="6"  align="center">
                                    <v-autocomplete
                                    style="width: 75% !important"
                                    input-class="form-control"
                                    class="mt-2"
                                    v-model="reporte.rubro"
                                    :items="rubros"
                                    required
                                    label="Rubros"
                                    item-text="rubro"
                                    item-value="id"
                                    return-object
                                    clearable
                                    :menu-props="{
                                        closeOnClick: true,
                                    }"
                                    ></v-autocomplete>
                                </v-col>
                            </v-row>
                        </v-card-text>

                        <v-card-text>
                            <v-row>
                                <v-col cols="6" align="center">
                                    <!-- <label>Fecha Inicio: </label> -->
                                    <date-picker                               
                                    v-model="reporte.desde"
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
                                <v-col cols="6" align="center">
                                    <!-- <label>Fecha Final: </label> -->
                                    <date-picker                                                                       
                                    v-model="reporte.hasta"
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
                         </v-card-text>  
                    </template>

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
                                <!-- <v-icon right >fas fa-file-excel</v-icon> -->
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
                { text: "Fecha Adquisición", value: "fecha_adquision",sortable: false,},
                { text: "Observación", value: "observacion", sortable: false },
            ],
            activos: [],
            cuentas: [], 
            reporte: { 
                cuenta: null, 
                rubro: null,
                desde: '', hasta: ''
            },
            rubros: [],
            precioTotal: 0
        }
    },
    mounted()
    {
        this.obtenerCuentas()
        this.obtenerRubros()
        this.obtenerInventarioPorCompras()
    },
    methods: {
        obtenerRubros()
        {
            axios
                .get("/Api/rubros")
                .then(({ data: { rubros } }) => {
                    this.rubros = rubros.filter((r) => r.eliminado == false)          
                })
                .catch(console.error);
        },
        obtenerCuentas()
        {            
            axios
                .get("/Api/cuentas")
                .then( ( { data: { cuentas } } ) => {
                    this.cuentas = cuentas.filter((r) => r.eliminado == false)
                })
                .catch(console.error)
        },
        obtenerInventarioPorCompras()
        {
            this.loader = true
            
            axios.get(`/Api/reportes/compras`)
                .then(({ data: { activos } }) => {
                    this.activos = [ ...activos ]
                    this.loader = false
            })
        },
        generar()
        {           
            if ( this.reporte.cuenta || this.reporte.rubro )
            {
                    this.loader = true
                    this.activos = []
                    this.precioTotal = 0

                    const path = `/Api/reportes/inventario/compras`
                    axios
                        .post( path, this.reporte )
                        .then( response => {
                            this.loader = false

                            if ( response.status == 200 )
                            {
                                const { activos } = response.data                                        

                                activos.forEach( activo => {
                                        
                                    if ( activo.precio )
                                    {
                                        this.precioTotal += parseFloat( activo.precio)
                                    }
                                    this.activos.push( { ...activo } )
                                })
                                 this.precioTotal = this.precioTotal.toFixed(2)
                            } else {
                                throw new Error('Ocurrio un error en el servidor')
                            }
                        })
                        .catch( () => {
                            this.loader = false
                            Swal.fire({
                                icon: 'warning',
                                title: '¡IMPORTANTE!',
                                text: 'Ocurrio un error al generar el reporte',
                            })
                        })                
            } else 
            {
                this.obtenerInventarioPorCompras()
            }

        },
        exportarExcel()
        {
            if ( this.activos.length > 0 )
            {
                let data = []

                this.activos.forEach( activo => {
                    data.push({                    
                    CODIGO: activo.codigo,
                    SERIE: activo.serie,
                    DESCRIPCION: activo.descripcion,
                    MARCA: activo.marca ? activo.marca.marca : '',
                    MODELO: activo.modelo,
                    PROCEDENCIA: activo.procedencia.procedencia,
                    ENTIDAD: activo.entidad ? activo.entidad.entidad : '',
                    CUENTA: activo.cuenta ? activo.cuenta.cuenta : '',
                    PRECIO: activo.precio,
                    RUBRO: activo.rubro ? activo.rubro.rubro : '',
                    UBICACION: activo.ubicacion ?  activo.ubicacion.ubicacion : '',
                    FECHA_ADQUISICION: activo.fecha_adquision,
                    ESTADO: activo.estado ? activo.estado.estado : '',
                    OBSERVACION: activo.observacion,
                    FECHA_REGISTRO: activo.created_at,
                    ULTIMA_ACTUALIZACION: activo.updated_at
                    })
                })

                data.push( { PRECIO: `Precio Total: ${this.precioTotal }` } )

                try {            
                    json2excel({
                        data,
                        name: 'ReporteDeCompras',
                        formateDate: 'yyyy/mm/dd'
                        })
                        
                    } catch (e) {
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

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
                    Listado de Inventario
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
                                <v-col cols="4"  align="center">
                                    <v-autocomplete
                                    no-data-text="No hay procedencias"
                                    style="width: 90% !important"
                                    input-class="form-control"
                                    class="mt-2"
                                    v-model="reporte.procedencia"
                                    :items="procedencias"
                                    required
                                    label="Procedencias"
                                    item-text="procedencia"
                                    item-value="id"
                                    return-object
                                    clearable
                                    :menu-props="{
                                        closeOnClick: true,
                                    }"
                                    ></v-autocomplete>
                                </v-col>

                                <v-col cols="4"  align="center">
                                    <v-autocomplete
                                    no-data-text="No hay entidades"
                                    style="width: 90% !important"
                                    input-class="form-control"
                                    class="mt-2"
                                    v-model="reporte.entidad"
                                    :items="entidades"
                                    required
                                    label="Entidad"
                                    item-text="entidad"
                                    item-value="id"
                                    return-object
                                    clearable
                                    :menu-props="{
                                        closeOnClick: true,
                                    }"
                                    ></v-autocomplete>
                                </v-col>
                                <!-- Rubro -->
                                
                                <v-col cols="4"  align="center">
                                    <v-autocomplete
                                    no-data-text="No hay rubros"
                                    style="width: 90% !important"
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
            entidades:  [], rubros:[], procedencias: [],

            reporte: {
                procedencia: null,
                entidad: null,
                rubro: null,
                desde: null, hasta: null
            },
            erroresReporte: [],
            precioTotal : 0
        }
    },
    mounted()
    {
        this.obtenerProcedencias()
        this.obtenerRubros()
        this.obtenerEntidades()
    },
    methods: {
        obtenerProcedencias()
        {
            axios
                .get(`/Api/procedencias`)
                .then(({ data: { procedencias } }) => {
                    procedencias.forEach( procedencia => {
                        if ( procedencia.procedencia != 'COMPRAS')
                        {
                            this.procedencias.push( {...procedencia } )
                        }
                    })                    
                })
                .catch(console.error)
        },
        obtenerRubros()
        {
            axios
                .get("/Api/rubros")
                .then(({ data: { rubros } }) => {
                    this.rubros = rubros.filter((r) => r.eliminado == false)          
                })
                .catch(console.error);
        },
        obtenerEntidades()
        {
            this.loader = true;
            axios
                .get("/Api/entidades")
                .then(({ data: { entidades } }) => {
                    this.entidades = entidades.filter( ent => ent.eliminado == false);        
                    this.loader = false
                })
                .catch(console.error)
        },
        obtenerInventarioProcedencia()
        {
            this.loader = true;
            axios
                .get("/Api/entidades")
                .then(({ data: { entidades } }) => {
                    this.entidades = entidades.filter( ent => ent.eliminado == false);        
                    this.loader = false
                })
                .catch(console.error)
        },
        generar()
        {            
            let path = ``
            let valido = true
            this.loader = true
            this.activos = []
            this.precioTotal = 0

            if ( this.reporte.procedencia && !this.reporte.entidad && !this.reporte.rubro )
            {
                console.log('obteniendo procedencias ')
                path = `/Api/reportes/inventario/procedencia/${this.reporte.procedencia.id}/${this.reporte.desde}/${this.reporte.hasta}`
                axios
                    .get( path, this.reporte )
                    .then( ( { data: { activos, respuesta } } ) => {
                        this.loader = false

                        if ( respuesta )
                        {
                            activos.forEach( activo => {                                
                                if ( activo.precio )
                                {
                                    this.precioTotal += parseFloat( activo.precio)
                                }
                                this.activos.push( { ...activo } )
                                })
                                this.precioTotal = this.precioTotal.toFixed(2)                                                    
                        } else {
                            throw new Error('Revisa los datos')
                        }
                        
                    }).catch( () => {
                        this.loader = false
                        Swal.fire({
                            icon: 'warning',
                            title: '¡IMPORTANTE!',
                            text: 'Ocurrio un error al generar el reporte, revisa los datos',
                        })
                    })
                
            } else 
            {          
                path = `/Api/reportes/inventario/entidadesRubros`  
                axios
                    .post( path, this.reporte )
                    .then( response => {
                        this.loader = false

                        if ( response.status == 200 )
                        {
                            const { respuesta, mensaje } = response.data

                            if ( respuesta )
                            {
                                const { activos } = response.data                                        

                                activos.forEach( activo => {
                                        
                                    if ( activo.precio )
                                    {
                                        this.precioTotal = parseFloat( Number(this.precioTotal) ) + parseFloat( Number(activo.precio) )
                                    }
                                    this.activos.push( {...activo } )
                                });

                                    this.precioTotal = this.precioTotal.toFixed(2) 
                                } else 
                                {
                                    throw new Error('No fue exitoso')
                                }
                            }                            
                        })  
                        .catch( () => {
                            this.loader = false
                            Swal.fire({
                            icon: 'warning',
                            title: '¡IMPORTANTE!',
                            text: 'Ocurrio un error al generar el reporte, revisa que el rango de fechas sea correcto',
                            })
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
                    CODIGO: activo.codigo,
                    SERIE: activo.serie,
                    DESCRIPCION: activo.descripcion,
                    MARCA: activo.marca ? activo.marca.marca : '',
                    MODELO: activo.modelo,
                    PROCEDENCIA: activo.procedencia.procedencia,
                    ENTIDAD: activo.entidad ? activo.entidad.entidad : '',
                    CUENTA: activo.cuenta ? activo.cuenta.cuenta : '',
                    PRECIO: activo.precio ? activo.precio : '',
                    RUBRO: activo.rubro ? activo.rubro.rubro : '',
                    UBICACION: activo.ubicacion ?  activo.ubicacion.ubicacion : '',
                    FECHA_ADQUISICION: activo.fecha_adquision,
                    ESTADO: activo.estado ? activo.estado.estado : '',
                    OBSERVACION: activo.observacion,
                    FECHA_ADQUISICION: activo.created_at,
                    ULTIMA_ACTUALIZACION: activo.updated_at
                    })
                })
                
                data.push(
                   { PRECIO: `Precio total: ${ this.precioTotal}`}
                )

                try {
                    
                    json2excel({
                    data,
                    name: 'ReportePorProcedencia',
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

<style>

</style>
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
           Descargos de Inventario
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
                    <v-row>
                        <v-col cols="12" align="center">
                            <v-autocomplete
                            style="width: 40% !important"
                            v-model="tipoDescargo"
                            :items="tiposDescargos"
                            required
                            label="Tipo Descargo"
                            item-text="tipoDescargo"
                            item-value="id"
                            return-object
                            clearable
                            :menu-props="{ closeOnClick: true }"
                            ></v-autocomplete>
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
            </template>

        </v-data-table>

      </v-card>
    </div>
  </div>
</template>

<script>

import { json2excel } from 'js2excel'

export default {
    data()
    {
        return {
            loader: false,
            tipoReporte: null,
            buscarActivo: '',
            modalReporte: false,
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
                { text: "N° Acta", value: "acta", sortable: false },
                { text: "Observación", value: "observacion", sortable: false },
            ],
            activos: [],
            tipoDescargo: null,
            tiposDescargos: [],
            desde: '', hasta: '',
            precioTotal: 0
        }
    },
    mounted()
    {
        this.obtenerTiposDescargos()
    },
    methods: {
        obtenerTiposDescargos()
        {
            axios.get(`/Api/inventario/descargos/tipos`)
                .then(({ data: { tiposDescargos } }) => {
                    this.tiposDescargos = tiposDescargos.filter((r) => r.eliminado == false )
            })
        },    
        generar()
        {
            let path = ``

            if ( this.tipoDescargo && this.tipoDescargo.id && ( this.desde && this.hasta ) )
            {

            } else if ( true )
            {

            }

            if ( this.tipoDescargo && this.tipoDescargo.id && this.desde && this.hasta )
            {   
                this.loader = true  

                this.precioTotal = 0
                this.activos = []

                const path = `/Api/reportes/inventario/descargos/${this.tipoDescargo.id}/${'2021-10-1'}/${'2021-11-24'}`
                axios
                    .get( path )
                    .then( response => {
                        this.loader = false

                        if ( response.status == 200 )
                        {                                                        
                            const { activos } = response.data                                        

                            activos.forEach( activo => {
                                    
                                if ( activo.precio )
                                {
                                    this.precioTotal = parseFloat( Number(this.precioTotal) ) + parseFloat( Number(activo.precio) )
                                }
                                this.activos.push( {...activo } )
                            });

                            console.log( parseFloat(this.precioTotal).toFixed(2) )                            
                        }                    
                    }).catch( console.error )

            } else 
            {
                Swal.fire({
                    icon: 'warning',
                    title: '¡IMPORTANTE!',
                    text: 'Comprueba que los datos esten completos',
                })
            }            
        },
        exportarExcel()
        {
            if ( this.activos.length > 0 )
            {
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
                    precio: activo.precio,
                    rubro: activo.rubro ? activo.rubro.rubro : '',
                    ubicacion: activo.ubicacion ?  activo.ubicacion.ubicacion : '',
                    fechaAdquision: activo.fecha_adquision,
                    estado: activo.estado ? activo.estado.estado : '',
                    observacion: activo.observacion,
                    acta: activo.acta,
                    fechaActa: activo.fechaActa,
                    fechaRegistro: activo.created_at,
                    ultimaActualizacion: activo.updated_at
                    })
                })

                try {            
                    json2excel({
                        data,
                        name: 'ReporteDescargos',
                        formateDate: 'yyyy/mm/dd'
                        })                    
                    } catch (e) {
                        console.log( e )
                        console.error('export error');
                    }
            } else 
            {
                Swal.fire({
                    icon: 'warning',
                    title: '¡IMPORTANTE!',
                    text: 'No hay activos para exportar',
                })
            }
        }
    }
}
</script>

<style>

</style>
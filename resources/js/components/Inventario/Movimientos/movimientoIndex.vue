<template>
  <div class="content">
        <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
            <v-card>
                <v-card-title>
                    Movimientos de inventario
                    <div class="flex-grow-1"></div>
                    <v-text-field v-model="buscarMovimiento" label="Buscar Movimiento" hide-details></v-text-field>
                    
                    <template>
                        <div class="text-center ma-2">
                            <v-btn
                            
                            color="primary"
                            dark
                            elevation="2"
                            small
                            @click="formMovimiento()"                        
                            >
                            Nuevo movimiento
                            </v-btn>
                        </div>
                    </template>
                </v-card-title>

                 <v-data-table
                    :headers="TheadTable"
                    :items="movimientos"
                    :footer-props="{
                        'items-per-page-options': [10, 15, 25, 35, 45],
                        'items-per-page-text': 'Registros Por Página'
                    }"
                    :items-per-page="10"
                    :search="buscarMovimiento"
                    multi-sort
                    class="elevation-1"
                >
                    <template v-slot:item.recibe="{item}" v-slot:activator="{ on }">
                        {{ item.recibe.nombre }} {{ item.recibe.apellido  }}
                    </template>

                    <template v-slot:item.aprueba="{item}" v-slot:activator="{ on }">
                        {{ item.aprueba.nombre }} {{ item.aprueba.apellido  }}
                    </template>

                    <template v-slot:item.user="{item}" v-slot:activator="{ on }">
                        {{ item.user.name }}
                    </template>

                    <template v-slot:item.action="{item}" v-slot:activator="{ on }">
                        <v-tooltip top >
                        <template v-slot:activator="{ on }" >
                            <v-btn
                            color="success"
                            class="mx-1"
                            elevation="8"
                            small
                            dark
                            :disabled="item.id < 0"
                            v-on="on"
                            @click="detalle( item )"
                            >
                            <v-icon>far fa-clipboard</v-icon>
                            </v-btn>

                            <v-btn
                            color="success"
                            class="mx-1"
                            elevation="8"
                            small
                            dark
                            :disabled="item.id < 0"
                            v-on="on"
                            @click="generarPDF( item )"
                            >
                            <v-icon>far fa-clipboard</v-icon>
                            </v-btn>
                        </template>
                        <span>Generar reporte</span>
                        </v-tooltip>
                    </template>

                    <template v-slot:item.recibidos="{item}" v-slot:activator="{ on }">
                        <v-progress-linear
                            color="amber"
                            height="15"
                            :value=" item.porcentaje"
                            striped
                        >
                            <template v-slot:default="{ value }">
                                <strong>{{ Math.ceil(value) }}%</strong>
                            </template>
                        </v-progress-linear>
                    </template>


                 </v-data-table>

            </v-card>
        </div>
  </div>
</template>

<script>
export default {
    data() 
    {
        return {
            buscarMovimiento: '',
            TheadTable: [
                { text: 'Tipo Movimiento', value: 'tipo_movimiento.tipo' },
                { text: "Recibido por", value: "recibe" },
                { text: "Entregado por", value: "user" },
                { text: "Descripcion", value: "descripcion" }, 
                { text: "Fecha registro", value: "created_at" },
                { text: "Recibidos", value: "recibidos" },                
                { text: "Acciones", value: "action", sortable: false, align: "center" },
            ],
            movimientos: []
        }
    },
    mounted () {
        this.obtenerMovimientos();
    },
    methods: {
        formMovimiento () {
            window.location = '/inventario/movimientos/crear'
        },
        obtenerMovimientos () {
            axios.get(`/Api/inventario/movimientos`)
                .then( ( { data: { movimientos } } ) => {

                    movimientos.forEach( movimiento => {                        
                        const { 
                           id, tipo_movimiento, recibe, user, descripcion, created_at
                        } = movimiento
                        
                        let totalActivos = movimiento.inventario.length
                        let activosRecibidos = movimiento.inventario.filter(activo=>activo.pivot.recibido == true).length

                        let porcentaje = ( activosRecibidos * 100 ) / totalActivos
                        this.movimientos.push( { 
                            id, tipo_movimiento, recibe, user, descripcion, 
                            created_at, porcentaje
                        })
                    });
                })
                .catch( console.error )
        },
        detalle ( movimiento )
        {
            window.location = `/inventario/movimientos/detalle?id=${movimiento.id}`
        },
        generarPDF ( {...movimiento })
        {
            window.open(`http://localhost:8000/inventario/movimientos/documento/${movimiento.id}`, '_blank')
        }
    }

}
</script>

<style>

</style>
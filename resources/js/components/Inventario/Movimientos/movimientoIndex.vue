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
                        @click="detalle()"
                        >
                        <v-icon>far fa-clipboard</v-icon>
                        </v-btn>
                    </template>
                    <span>Ver Detalle</span>
                    </v-tooltip>
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
                { text: 'ID', value: 'id'},
                { text: 'Tipo Movimiento', value: 'tipo_movimiento.tipo' },
                { text: "Recibido por", value: "recibe" },
                { text: "Aprobado por", value: "aprueba" },
                { text: "Entregado por", value: "user" },
                { text: "Descripcion", value: "descripcion" }, 
                { text: "Fecha registro", value: "created_at" },                
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
                    console.log( movimientos)
                    this.movimientos = movimientos;
                })
                .catch( console.error )
        },
        detalle ()
        {

        }
    }

}
</script>

<style>

</style>
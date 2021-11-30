<template>
    <div class="content">
        <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
            <v-card>
                <v-card-title>
                    Activos en movimientos
                    <div class="flex-grow-1"></div>
                    <v-text-field v-model="buscarActivo" label="Buscar Activo" hide-details></v-text-field>
                    
                    <template>
                        <div class="text-center ma-4">
                            <v-btn
                            
                            color="primary"
                            dark
                            elevation="2"
                            small                      
                            >
                            Ver Movimientos
                            </v-btn>
                        </div>
                    </template>

                </v-card-title>

                <v-data-table
                    :headers="TheadTable"
                    :items="activos"
                    :footer-props="{
                        'items-per-page-options': [10, 15, 25, 35, 45],
                        'items-per-page-text': 'Registros Por Página'
                    }"
                    :items-per-page="10"
                    :search="buscarActivo"
                    multi-sort
                    class="elevation-1"
                >

                    <template v-slot:item.action="{item}" v-slot:activator="{ on }">                    
                    <v-tooltip top >
                        <template v-slot:activator="{ on }" >
                            <v-btn
                            color="info"
                            class="mx-1"
                            elevation="8"
                            small
                            dark
                            :disabled="item.id < 0"
                            v-on="on"
                            @click="detalle(item)"
                            >
                            <v-icon>far fa-clipboard</v-icon>
                            </v-btn>
                        </template>
                        <span>Ver Detalle</span>
                    </v-tooltip>
                    <v-tooltip top >
                        <template v-slot:activator="{ on }" >
                            <v-btn
                            color="amber"
                            class="mx-1"
                            elevation="8"
                            small
                            dark
                            :disabled="item.id < 0"
                            v-on="on"
                            @click="irMovimiento(item)"
                            >
                            <v-icon>far fa-clipboard</v-icon>
                            </v-btn>
                        </template>
                        <span>Ver movimiento</span>
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
            buscarActivo: '',
            TheadTable: [
                { text: 'Codigo', value: 'codigo'},
                { text: "Descripcion", value: "descripcion" },
                { text: "Marca", value: "marca.marca" },
                { text: "Modelo", value: "modelo" },
                { text: "Serie", value: "serie" },
                { text: "Precio", value: "precio" },
                { text: "Ubicacion", value: "ubicacion.ubicacion" },                                
                { text: "Acciones", value: "action", sortable: false, align: "center" },
            ],
            activos: []
        }
    },
    mounted()
    {
        this.obtenerActivosEnMovimiento()
    },
    methods : {
        obtenerActivosEnMovimiento()
        {
            axios
                .get("/Api/activos/movimientos")
                .then( ( { data: { activos } } ) => {                    
                    this.activos = activos
                })
                .catch(console.error);
        },
        irMovimiento( { ...activo })
        {
            window.location = `/inventario/movimientos/detalle?id=${activo.pivot.movimiento_id}`
        },
        detalle( { ...activo } )
        {
            window.location = `/inventario/crear?id=${activo.id}&detalle=true`
        }
    }
}
</script>

<style>

</style>
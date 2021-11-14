<template>
  <div class="content">
        <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
            <v-card>
                <v-card-title>
                    Detalle de Movimiento
                    <div class="flex-grow-1"></div>                    
                </v-card-title>
            

                <v-form>
                    <v-container>
                        <v-row>
                            <v-col cols="12" sm="6" >
                                <v-text-field
                                    v-model="movimiento.tipoMovimiento.tipo"
                                    label="Tipo de movimiento"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6" >
                                <v-text-field
                                    v-model="movimiento.aprueba.nombre"
                                    label="Aprobo"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6" >
                                <v-text-field
                                    v-model="movimiento.recibe.nombre"
                                    label="Recibio"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6" >
                                <v-text-field
                                    v-model="movimiento.gerencia.nombre"
                                    label="Aprobo por gerencia"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6" >
                                <v-text-field
                                    v-model="movimiento.usuario.name"
                                    label="Usuario"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6" >
                                <v-text-field
                                    v-model="movimiento.gerencia.nombre"
                                    label="Fecha de registro"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="12" >
                                <v-text-field
                                    v-model="movimiento.observaciones"
                                    label="Fecha de registro"
                                    disabled
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-form>

                <v-container>
                    <v-data-table
                    v-model="selected"
                    :headers="headMovimientos"
                    :items="activosPendientes"
                    no-data-text="No hay activos"
                    :items-per-page="10"
                    :footer-props="{
                        'item-per-page-options': [10, 20, 30],
                        showFirstLastPage: true,
                    }"
                    :single-select="singleSelect"
                    item-key="codigo"
                    show-select
                    >

                    <template #item="{ item }">
                        <tr>
                            <td><v-checkbox                                                       :disabled="item.calories > 250"
                                class="pa-0 ma-0"
                                :ripple="false"
                                v-model="selected"
                                :value="item"
                                hide-details></v-checkbox></td>
                            <td>{{item.codigo}}</td>
                            <td>{{item.descripcion}}</td>
                            <td>{{item.marca.marca}}</td>
                            <td>{{item.modelo}}</td>
                            <td>{{item.modelo}}</td>
                            <td>{{item.modelo}}</td>
                        </tr>
                        </template>
                </v-data-table>

                 <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="red darken-1" text @click="cancelar">Cerrar</v-btn>
                    <v-btn
                        color="info darken-1"
                        text
                        :disabled="false"
                        @click="guardar"
                        >Guardar</v-btn
                    >
                    </v-card-actions>
                </v-container>
            </v-card>
        </div>
  </div>
</template>

<script>
export default {

    data()
    {
        return {
            singleSelect: false, selected: [],
            idMovimiento: null,
            movimiento: {
                tipoMovimiento: { id: null, tipo: "" },
                recibe: { id: null, nombre: "" },
                aprueba: { id: null, nombre: "" },
                gerencia: { id: null, nombre: "" },
                ubicacion: { id: null, ubicacion: "" },
                fecha: "",
                registro: null,
                usuario: { id: null, nombre: ''},
                observaciones: "",
                activos: [],
            },
            headMovimientos: [
                { text: "Codigo", value: "codigo", align: "left" },
                { text: "Descripción", value: "descripcion", align: "left" },            
                { text: "Marca", value: "marca.marca", align: "left" },            
                { text: "Modelo", value: "modelo", align: "left" },            
                { text: "Falla", value: "pivot.falla", align: "left" },
                { text: "Observaciones", value: "pivot.observaciones", align: "left" },
            ],
        }
    },
    computed: {
        activosPendientes() 
        {
            return this.movimiento.activos.map( activo => ({ ... activo, isSelected: activo.id != 1}))
        }
    },
    created()
    {
        let uri = window.location.search.substring(1);
        let params = new URLSearchParams(uri);

        this.idMovimiento = params.has("id") ? params.get("id") : null;
    },
    mounted()
    {
        if ( this.idMovimiento)
        {
            this.obtenerMovimiento();
        }
    }, 
    methods: {
        obtenerMovimiento()
        {
            this.loader = true;
            axios
                .get(`/Api/inventario/${this.idMovimiento}/movimientos`)
                .then(({ data: { movimiento } }) => {
                    let {
                        tipo_movimiento,
                        recibe,
                        aprueba,
                        user,
                        aprobado_gerencia,
                        created_at,
                        descripcion,
                        inventario
                    } = movimiento[0]

                    this.movimiento.tipoMovimiento = { ... tipo_movimiento }
                    this.movimiento.recibe = { ... recibe }
                    this.movimiento.aprueba = { ... aprueba}
                    this.movimiento.gerencia = { ... aprobado_gerencia}
                    this.movimiento.registro = created_at
                    this.movimiento.usuario = { ... user }
                    this.movimiento.observaciones = descripcion
                    this.movimiento.activos = [ ... inventario ]
            

                }).catch( console.error )

        },
        guardar()
        {
            console.log( this.getIdSeleccion() )
        }, 
        cancelar()
        {

        },
        getIdSeleccion ()
        {
            let activosRecibidos = []

            this.selected.forEach( activo => {
                activosRecibidos.push( activo.id )
            });

            return activosRecibidos
        }
    }
}
</script>

<style>

</style>
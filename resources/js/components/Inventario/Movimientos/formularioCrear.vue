<template>
    <div class="content">
        <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
            
            <v-card>
                <v-card-title v-text="'Movimientos de inventarios'" >
                    <div class="flex-row-1"></div>
                </v-card-title>

                <v-container fluid>                
                    <v-form
                        ref="formMovimiento"
                        v-model="formularioValido"
                        :lazy-validation="true" >

                        <v-card-title>
                            <v-row>
                                <v-col cols="6">
                                    <v-autocomplete
                                        v-model="movimiento.tipoMovimiento"
                                        :items="tiposMovimientos"
                                        required
                                        :rules="[ v => !!v || 'Tipo cuenta del activo es requerido']"
                                        label="Tipo de movimientos"
                                        item-text="tipo"
                                        item-value="id"
                                        return-object
                                        clearable
                                        :menu-props="{ closeOnClick: true }"
                                    ></v-autocomplete>
                                </v-col>

                                <v-col cols="6">
                                    <v-autocomplete
                                        v-model="movimiento.empleados"
                                        :items="empleados"
                                        required
                                        :rules="[ v => !!v || 'Tipo cuenta del activo es requerido']"
                                        label="Recibe"
                                        item-text="nombre"
                                        item-value="id"
                                        return-object
                                        clearable
                                        :menu-props="{ closeOnClick: true }"
                                    ></v-autocomplete>
                                </v-col>

                                <v-col cols="6">
                                    <v-autocomplete
                                        v-model="movimiento.empleados"
                                        :items="empleados"
                                        required
                                        :rules="[ v => !!v || 'Tipo cuenta del activo es requerido']"
                                        label="Aprobado"
                                        item-text="nombre"
                                        item-value="id"
                                        return-object
                                        clearable
                                        :menu-props="{ closeOnClick: true }"
                                    ></v-autocomplete>
                                </v-col>

                                <v-col cols="6">
                                    <v-menu
                                        v-model="menu"
                                        :close-on-content-click="false"
                                        :nudge-right="40"
                                        transition="scale-transition"
                                        offset-y
                                        min-width="auto">
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    v-model="movimiento.fecha"
                                                    label="Seleccione fecha de reingreso"
                                                    prepend-icon="mdi-calendar"
                                                    readonly
                                                    v-bind="attrs"
                                                    v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="movimiento.fecha"
                                                @input="menu = false"
                                            ></v-date-picker>
                                    </v-menu>
                                </v-col> 

                                <v-col cols="12" v-if= "movimiento.tipoMovimiento.id == 2">
                                    <v-textarea
                                        v-model="movimiento.observacion"
                                        label="Se envia a: "
                                        rows="2"
                                        required
                                        :error-messages="errors"
                                    ></v-textarea>
                                </v-col>

                                <v-col cols="12" v-if= "movimiento.tipoMovimiento.id == 1">
                                    <v-textarea
                                        v-model="movimiento.observacion"
                                        label="Se presta a:"
                                        rows="2"
                                        required
                                        :error-messages="errors"
                                    ></v-textarea>
                                </v-col>

                                <v-col cols="12">
                                    <v-textarea
                                        v-model="movimiento.observacion"
                                        label="Observacion"
                                        rows="2"
                                        required
                                        :error-messages="errors"
                                    ></v-textarea>
                                </v-col>

                            </v-row>
                        </v-card-title>

                        <v-data-table
                            :headers="headMovimientos"
                            :items="inventarios"
                            no-data-text="No hay movimientos agregados"
                            :items-per-page="10"
                            :search="buscarInventario"
                            :footer-props="{
                                'item-per-page-options': [10, 20, 30],
                                showFirstLastPage: true,
                            }" >

                            <template v-slot:top>
                                <v-toolbar flat color="white">
                                <div class="flex-grow-1"></div>                
                                <v-dialog
                                    v-model="modalNMovimientos"
                                    persistent
                                    max-width="1240px"
                                    scrollable
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-btn
                                            elevation="10"
                                            color="blue  darken-3"
                                            dark
                                            class="mb-2"
                                            v-on="on"
                                        >
                                            Agregar Nuevo Movimiento&nbsp;
                                            <v-icon>mdi-plus-box-multiple-outline</v-icon>
                                            </v-btn>
                                    </template>

                                    <v-card>
                                        <v-card-title
                                            class="headline grey lighten-2"
                                            primary-title
                                        >
                                            <span class="headline" v-text="formTitle"></span>
                                        </v-card-title>
                                        <v-card-text>                        
                                            <n-inventario
                                            ref="inventario"
                                            @added="onAddedItem"
                                            />
                                        </v-card-text>
                                        <v-divider></v-divider>
                                        <v-card-actions>
                                            <div class="flex-grow-1"></div>
                                            <v-btn
                                            color="success darken-1"
                                            text
                                            @click="modalNMovimientos = false"
                                            >Finalizar
                                            </v-btn>                                        
                                        </v-card-actions>
                                    </v-card>
                                </v-dialog>
                                </v-toolbar>
                            </template>
                        </v-data-table>

                    </v-form>
                </v-container>

                <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="red darken-1" text
                      >Cerrar</v-btn
                    >
                    <v-btn
                      color="info darken-1"
                      text                     
                    >Guardar</v-btn>
                  </v-card-actions>
            </v-card>

        </div>
    </div>
</template>

<script>

Vue.component('n-inventario', require('..//Modals/Movimiento.vue').default);

export default {
    name: 'movimiento-crear',
    data() {
        return {
            menu: false,
            formTitle: 'Inventario General',
            errors:[],
            formularioValido: false,
            modalNMovimientos: false,
            movimiento: {
                tipoMovimiento: { id: null, tipo: '' }, empleados: {id: null, nombre: ''},
                fecha: '', observaciones: '',
                activos: this.inventarios
            },
            tiposMovimientos: [
                { id: 1, tipo: 'TIPO 1' },
                { id: 2, tipo: 'TIPO 2' }
            ],
            empleados: [
                { id: 1, nombre: 'Jonathan Alfonso'},
                { id: 2, nombre: 'Leonel Messi' }
            ],
            buscarInventario: '',
            headMovimientos: [
                { text: "Codigo", value: "codigo", align: "left" },
                { text: "Descripción", value: "descripcion", align: "left" },
                { text: "Marca", value: "marca.nombre", align: "left" },
                { text: "Modelo", value: "modelo", align: "left" },
                { text: "Serie", value: "serie", align: "left" },
                { text: "Falla", value: "falla", align: "left" },
            ],
            inventarios: []
            // SEGUIR DISEÑANDO VISTA PARA EL MOVIMIENTO
            // MODAL CON INVENTARIO, Y BOTONES PARA DISPARAR ACCIONES
        }
    },
    methods: {
        getInventario() {
            console.log('creando desde movimiento')
            this.$refs.activo = [];
        },
        onAddedItem( valores ) {
            this.inventarios = valores;
            console.log( this.inventarios )
        },
        getProductFromChild () {

        }
    }
}
</script>


    
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
                                        v-model="movimiento.recibe"
                                        :items="empleados"
                                        required
                                        :rules="[ v => !!v || 'Tipo cuenta del activo es requerido']"
                                        label="Recibe"
                                        :item-text="EmpleadoNombreCompleto"
                                        item-value="id"
                                        return-object
                                        clearable
                                        :menu-props="{ closeOnClick: true }"
                                    ></v-autocomplete>
                                </v-col>

                                <v-col cols="6">
                                    <v-autocomplete
                                        v-model="movimiento.aprueba"
                                        :items="empleados"
                                        required
                                        :rules="[ v => !!v || 'Tipo cuenta del activo es requerido']"
                                        label="Aprobado"
                                        :item-text="EmpleadoNombreCompleto"
                                        item-value="id"
                                        return-object
                                        clearable
                                        :menu-props="{ closeOnClick: true }"
                                    ></v-autocomplete>
                                </v-col>

                                <v-col cols="6">
                                    <v-autocomplete
                                        v-model="movimiento.gerencia"
                                        :items="empleados"
                                        required
                                        :rules="[ v => !!v || 'Tipo cuenta del activo es requerido']"
                                        label="Aprobado por gerencia"
                                        :item-text="EmpleadoNombreCompleto"
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

                                <v-col cols="5" v-if="movimiento.tipoMovimiento.id == 2 || movimiento.tipoMovimiento.id == 3 ">
                                    <v-autocomplete
                                        append-icon="fas fa-map-marker-alt"
                                        v-model="movimiento.ubicacion"
                                        :items="ubicaciones"
                                        required
                                        :rules="[ v => !!v || 'Ubicación del activo es requerido']"
                                        label="Se translado a:"
                                        item-text="ubicacion"
                                        item-value="id"
                                        return-object
                                        clearable
                                        :menu-props="{ closeOnClick: true }"
                                    ></v-autocomplete>
                                </v-col>

                                <v-col cols="1" md="1" v-if ="movimiento.tipoMovimiento.id == 2 || movimiento.tipoMovimiento.id == 3">
                                    <Ubicacion @saved="onSavedUbicacion" ref="ubicacion" />
                                    <v-btn
                                        elevation="5"
                                        class="mt-8"
                                        text
                                        icon
                                        color="primary"
                                        @click="mostrarModalUbicacion()"
                                        dark
                                    >
                                    <v-icon>mdi-plus-circle</v-icon>
                                    </v-btn>
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

                            <template v-slot:item.falla="props">
                                <v-edit-dialog
                                    :return-value.sync="props.item.falla"
                                    large
                                    persistent
                                    @save="guardarFallar( props.item )"
                                    @cancel="cancelarFalla"
                                    >
                                        {{ props.item.falla }}
                                    <template v-slot:input>
                                        <div class="mt-4 text-h6">
                                            Falla:
                                        </div>
                                        <v-text-field
                                        v-model="props.item.falla"
                                        :rules="[]"                                        
                                        label="Descripción"
                                        single-line
                                        counter
                                        autofocus
                                        ></v-text-field>
                                    </template>
                                </v-edit-dialog>
                            </template>



                        </v-data-table>

                    </v-form>
                </v-container>

                <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="red darken-1" text
                    @click="cancelar"
                      >Cerrar</v-btn
                    >
                    <v-btn
                      color="info darken-1"
                      text
                      @click="guardarMovimiento"                     
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
                tipoMovimiento: { id: null, tipo: '' }, recibe: {id: null, nombre: ''},  aprueba: {id: null, nombre: ''},
                gerencia : {id: null, nombre: ''},
                ubicacion: { id: null, ubicacion: '' },
                fecha: '', observaciones: '',
                activos: []
            },
            tiposMovimientos: [],
            ubicaciones: [],
            empleados: [],
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
        }
    },
    computed: {
        
    },
    mounted () 
    {
        this.getEmpleados();
        this.getTiposMovimientos();
        this.getUbicaciones();
    },
    methods: {
        EmpleadoNombreCompleto( empleado )
        {
            return empleado.nombre + ' ' + empleado.apellido;
        },
        getUbicaciones() 
        {
            axios.get(`/Api/ubicaciones`)
                .then ( ( { data: { ubicaciones } } ) => {
                    this.ubicaciones = ubicaciones;
                });
        },
        getInventario() {
            console.log('creando desde movimiento')
            this.$refs.activo = [];
        },
        getEmpleados ()
        {
            axios
                .get("/Api/empleados")
                .then(({ data: { empleados } }) => {
                    this.empleados = empleados ;                  
                    console.log( this.empleados )
                })
                .catch(console.error);
        },
        getTiposMovimientos()
        {
            axios
                .get("/Api/movimientos/tipos")
                .then(({ data: { tiposMovimientos } }) => {
                    this.tiposMovimientos = tiposMovimientos; 
                    console.log( this.tiposMovimientos )                 
                })
                .catch(console.error);
        },
        onAddedItem( valores ) {
            this.inventarios = valores;
            console.log( this.inventarios )
        },
        getProductFromChild () {

        },
        guardarMovimiento()
        {
            console.log ( this.inventarios );
            

            let activosTemp = [...this.inventarios ];

            activosTemp.forEach( activo => {
                this.movimiento.activos.push( 
                    { inventario_id: activo.id, falla: activo.falla, observaciones: activo.observaciones  } )
            });

            const path = `/Api/inventario/movimientos/save`;

            axios
                .post( path, this.movimiento )
                .then( response => {

                    console.log( response );

                    if ( response.status == 200 )
                    {
                        const { respuesta, mensaje } = response.data

                        if ( respuesta )
                        {
                            this.alerta( mensaje, 'success', '¡Bien hecho!')
                                .then( () => {
                                    Swal.fire({
                                        title: 'INFORMACION',
                                        text: "¿Quieres agregar otro movimiento?",
                                        icon: 'info',
                                        icon: "info",
                                        showCancelButton: true,
                                        confirmButtonColor: "#3698e3",
                                        cancelButtonColor: "#d33",
                                        confirmButtonText: "Si",
                                        cancelButtonText: 'No',
                                        allowOutsideClick: false
                                    }).then((result) => {
                                        if ( result.isConfirmed ) {
                                            this.movimiento = {}
                                                                                
                                        } else {
                                            window.location = '/inventario/movimientos';                                    
                                        }
                                    });
                                });                
                        }
                    }
                })
                .catch ( console.error )
        },
        cancelar() {
            window.location = '/inventario/movimientos'
        },
        alerta (mensaje, icono = 'info', titulo = '')
        {
            return new Promise( ( resolve, reject ) => {
                Swal.fire({
                position: "top-end",
                icon: icono,
                title: titulo,
                text: mensaje,
                showConfirmButton: false,
                timer: 1500,
                }).then( resolve );
            });
            
        },
        guardarFallar( item ) {
            
        },
        cancelarFalla() {

        },
        mostrarModalUbicacion()
        {

        },
        onSavedUbicacion( value )
        {

        }
    }
}
</script>


    
<template>
    <div class="content">
        <div
            class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100"
        >

            <v-overlay :value="loader" :z-index="'99999999'">
                <v-progress-circular
                indeterminate
                size="80"
                color="grey darken-4"
                ></v-progress-circular>
            </v-overlay>

            <v-card>
                <v-card-title>
                Empleados de ITCHA
                <div class="flex-grow-1"></div>
                <v-text-field
                    v-model="buscarEmpleado"
                    label="Buscar Empleado"
                    hide-details
                ></v-text-field>
                </v-card-title>

                <v-data-table
                    :headers="TheadTable"
                    :items=" mostrarEmpleadosEliminados ? empleadosEliminado : empleados"
                    :footer-props="{
                        'items-per-page-options': [5, 10, 20, 30, 40],
                        'items-per-page-text': 'Registros Por Página',
                    }"
                    :items-per-page="5"
                    :search="buscarEmpleado"
                    multi-sort
                    class="elevation-1"
                    >
                    
                    <template v-slot:top>
                        <v-toolbar flat color="white">
                        <div class="flex-grow-1"></div>
                        <v-dialog v-model="modal" persistent max-width="700px">
                            <template v-slot:activator="{ on }">
                            <v-btn
                                elevation="10"
                                color="blue  darken-3"
                                dark
                                v-show= "!mostrarEmpleadosEliminados"
                                class="mb-2"
                                style="font-size: 10px"
                                v-on="on"
                            >
                                Agregar Empleado&nbsp;
                                <v-icon>mdi-plus-box-multiple-outline</v-icon>
                            </v-btn>

                            <v-checkbox
                                v-model="mostrarEmpleadosEliminados"
                                class="mx-10"
                                style="margin-top: 1.5rem"
                                label="Empleados desactivados"
                            />
                            </template>
                            <v-card>
                            <v-card-title class="headline grey lighten-2" primary-titles>
                                <span class="headline" v-text="formTitle"></span>
                            </v-card-title>
                            <v-card-text>
                                <v-container>
                                <v-form
                                    ref="formEmpleado"
                                    v-model="validForm"
                                    :lazy-validation="true"
                                >

                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field
                                            append-icon="fas fa-user-tag"
                                            v-model="empleado.nombre"
                                            @keyup="errorsEmpleado.nombre = []"
                                            :rules= "[
                                                reglas.requerido,
                                                reglas.min,
                                                reglas.expresion,
                                            ]"
                                            label="Nombres"
                                            required
                                            :error-messages="errorsEmpleado.nombre"
                                            ></v-text-field>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-text-field
                                            append-icon="fas fa-user-tag"
                                            v-model="empleado.apellido"
                                            @keyup="errorsEmpleado.apellido = []"
                                            :rules= "[
                                                reglas.requerido,
                                                reglas.min,
                                                reglas.expresion,
                                            ]"
                                            label="Apellidos"
                                            required
                                            :error-messages="errorsEmpleado.apellido"
                                            ></v-text-field>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-text-field
                                            append-icon="fas fa-id-card"
                                            v-model="empleado.dui"
                                            @keyup="errorsEmpleado.dui = []"
                                            :rules= "[
                                                reglas.dui
                                            ]"
                                            label="DUI"
                                            required
                                            :error-messages="errorsEmpleado.dui"
                                            ></v-text-field>
                                    </v-col>

                                    <v-col cols="12">
                                            <v-autocomplete
                                            append-icon="fas fa-cogs"
                                                v-model="empleado.cargo"
                                                :items="cargos"
                                                required
                                                label="Cargos"
                                                item-text="cargo"
                                                item-value="id"
                                                aling="center"
                                                return-object
                                                clearable
                                                :menu-props="{ closeOnClick: true }"
                                            ></v-autocomplete>
                                        </v-col>
                                </v-row>                                

                                </v-form>
                                </v-container>
                            </v-card-text>
                            <v-divider></v-divider>
                            <v-card-actions>
                                <div class="flex-grow-1"></div>
                                <v-btn color="red darken-1" text @click="cerrarModal"
                                >Cerrar</v-btn
                                >
                                <v-btn
                                color="info darken-1"
                                :disabled="!validForm"
                                @click="save()"
                                text
                                v-text="tituloBtnGuardar"
                                ></v-btn>
                            </v-card-actions>
                            </v-card>
                        </v-dialog>
                        </v-toolbar>
                    </template>


                    <template v-slot:item.action="{item}" v-slot:activator="{ on }">
                        <v-tooltip top>
                        <template v-slot:activator="{ on }">
                            <v-btn
                            v-show="!mostrarEmpleadosEliminados"
                            color="success"
                            elevation="8"
                            small
                            dark
                            :disabled="item.id < 0"
                            v-on="on"
                            @click="mostrarModal(item)"
                            >
                            <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </template>
                        <span>Actualizar Empleado</span>
                        </v-tooltip>
                        <v-tooltip top >
                        <template v-slot:activator="{ on }" >
                            <v-btn
                            v-show="!mostrarEmpleadosEliminados"
                            color="info"
                            class="mx-1"
                            elevation="8"
                            small
                            dark
                            :disabled="item.id < 0"
                            v-on="on"
                            @click="eliminar( item )"
                            >
                            <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                        <span>Desactivar Empleado</span>
                        </v-tooltip>
                        <v-tooltip top >
                        <template v-slot:activator="{ on }" >
                            <v-btn
                            v-show=" mostrarEmpleadosEliminados"
                            color="teal"
                            class="mx-1"
                            elevation="8"
                            small
                            dark
                            :disabled="item.id < 0"
                            v-on="on"
                            @click="restaurar( item )"
                            >
                            <v-icon>mdi-restore</v-icon>
                            </v-btn>
                        </template>
                        <span>Activar Empleado</span>
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
            loader: false,
            validForm: false,
            modal: false,
            buscarEmpleado: '',
            TheadTable: [
                { text: "Nombre", value: "nombre", align: 'center', },
                { text: "Apellido", value: "apellido", align: 'center', },
                { text: "DUI", value: "dui", align: 'center', },
                { text: "Cargo", value: "cargo.cargo", align: 'center', },
                { text: "Acciones", value: "action", sortable: false, align: "center" },
            ],
            empleados: [],
            empleadosEliminado: [],
            mostrarEmpleadosEliminados: false,
            empleado: { id: null, nombre: '', apellido: '', cargo: { id: null, cargo: ''} },
            errorsEmpleado: {},
            reglas: {
                requerido: (v) => !!v || "Nombre del empleado es requerido",
                min: (v) =>
                (v.length >= 2 && v.length <= 100) ||
                "Nombre de la cuenta debe ser mayor a 2 caracteres",
                expresion: (v) =>
                /^[A-Za-záéíóúÁÉÍÓÚ\s]+$/g.test(v) ||
                "Nombre del empleado solo puede contener letras",
                dui: (v) => 
                v && /^[0-9]{8}-[0-9]{1}$/.test(v) || "Ingresa un numero de DUI valido"
            },
            cargos: [],

        }
    },
    computed: {
        formTitle()
        {
            return ( this.empleado.id ) ? 'Actualizar Empleado' : 'Agregar Empleado'
        },
        tituloBtnGuardar ()
        {
            return this.empleado.id ? 'Actualizar' : 'Guardar'
        }
    },
    mounted() 
    {
        this.obtenerEmpleados()
        this.obtenerCargos()
    },
    methods: {
        obtenerEmpleados()
        {
            this.loader = true
            axios
                .get("/Api/empleados")
                .then( ( { data: { empleados } } ) => {
                    this.loader = false
                    this.empleados = empleados.filter((r) => r.eliminado == false);
                    this.empleadosEliminado = empleados.filter((r) => r.eliminado == true);                
                })
                .catch(console.error);
        },
        obtenerCargos()
        {
            axios
                .get("/Api/cargos")
                .then( ( { data: { cargos } } ) => {
                    this.cargos = cargos.filter((r) => r.eliminado == false);              
                })
                .catch(console.error);
        },
        save()
        {
            const path = this.empleado.id == null ? '/Api/empleados' : `Api/empleados/${this.empleado.id}/edit`
            axios
                .post(path, this.empleado )
                .then( (response) => {
                    
                    const { respuesta, mensaje } = response.data

                    if ( respuesta )
                    {
                        this.obtenerEmpleados()
                        this.alerta( mensaje, 'success', '¡Bien hecho!')
                        this.cerrarModal()
                    } else {
                        this.errorsEmpleado = {}
                        let { errors } = response.data
                        this.errorsEmpleado = errors
                    }
                }).catch( () => {
                    this.alerta( mensaje, 'error', '¡Algo salio mal!');
                    this.cerrarModal();
                })

        },
        mostrarModal( { ... empleado} )
        {
            this.empleado = empleado
            this.modal = true
        },
        cerrarModal()
        {
            this.modal = false
            setTimeout(() => {
                this.empleado = { id: null, nombre: "", apellido: '', dui: '', cargo: { id: null, cargo: '' } };
                this.resetValidation();
            }, 300);
        },
        resetValidation() {
            this.errorsEmpleado = [];
            this.$refs.formEmpleado.resetValidation();
        },
        eliminar( { ...empleado } )
        {
            Swal.fire({
                title: "INFORMACION",
                text: `¿Estas seguro de desactivar el emplado ${empleado.nombre} ?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3698e3",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si",
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) 
                {
                    this.cambiarEstadoEmpleado( empleado );
                }
            });

        },
        restaurar( {... empleado } )
        {
        
            Swal.fire({
                title: "INFORMACION",
                text: `¿Estas seguro de activar el empleado ${empleado.nombre} ?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3698e3",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si",
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) 
                {
                    this.cambiarEstadoEmpleado( empleado, false );
                }
            });
        },
        cambiarEstadoEmpleado( empleado, eliminar = true  )
        {
            axios
                .delete(`/Api/empleados/${ empleado.id}/${eliminar}`)
                .then( (response) => { 
                    
                    if ( response.status == 200 )
                    {
                        const { respuesta, mensaje } = response.data

                        if( respuesta )
                        {
                            this.alerta(mensaje, 'success', '¡Bien Hecho!')
                            this.obtenerEmpleados();

                        } else {
                            this.alerta( mensaje, 'error', 'IMPORTANTE');
                        }
                    }
                }).catch( () => {
                    this.alerta('Algo salio mal', 'error', 'IMPORTANTE')                    
                })

        },
        alerta (mensaje, icono = 'info', titulo = '')
        {
            Swal.fire({
                position: "top-end",
                icon: icono,
                title: titulo,
                text: mensaje,
                showConfirmButton: false,
                timer: 1500,
                });
        }
    }
}
</script>

<style>

</style>
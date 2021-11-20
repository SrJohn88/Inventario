<template>
    <div class="content">
        <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
            
            <v-overlay :value="loader" :z-index="'99999999'">
                <v-progress-circular indeterminate size="80" color="grey darken-4"></v-progress-circular>
            </v-overlay>

            <v-card>
                <v-card-title>
                    CATALOGO DE TIPOS DE DESCARGOS
                    <div class="flex-grow-1"></div>
                    <v-text-field v-model="buscarTipoDescargo" label="Buscar tipo descargo" hide-details></v-text-field>
                </v-card-title>

                <v-data-table
                    :headers="encabezadosTbl"
                    :items=" estado ? tiposDescargosDesactivos : tiposDescargos"
                    :footer-props="{
                        'items-per-page-options': [5,10, 20, 30,40],
                        'items-per-page-text' : 'Registros Por Página'
                    }"
                    :items-per-page="5"
                    :search="buscarTipoDescargo"
                    multi-sort
                    class="elevation-1"
                    no-data-text="No hay tipos de descargos para mostrar"
                >

                <template v-slot:top >
                    <v-toolbar flat color="white">
                    <div class="flex-grow-1"></div>
                    <v-dialog v-model="modal" persistent max-width="700px">
                        <template v-slot:activator="{ on }">
                        <v-btn v-show="!estado" elevation="10" color="blue  darken-3" dark class="mb-2" v-on="on">
                            Agregar tipo de descargo &nbsp;
                            <v-icon>mdi-plus-box-multiple-outline</v-icon>
                        </v-btn>
                        
                            <v-checkbox 
                                v-model="estado"
                                class="mx-10"
                                style="margin-top: 1.5rem;"
                                label="Tipos de descargo desactivados"
                                value="false"
                            />
                        </template>
                        <v-card>
                        <v-card-title class="headline grey lighten-2" primary-titles>
                            <span class="headline" v-text="formTitle"></span>
                        </v-card-title>
                        <v-card-text>
                            <v-container>
                            <v-form ref="formTipoDescargo" v-model="validForm" :lazy-validation="true">
                                <v-text-field
                                append-icon="mdi-folder-outline"
                                v-model="tipoDescargo.tipoDescargo"
                                @keyup="errors = []"
                                :rules="[reglas.min, reglas.requerido, reglas.expresion]"
                                label="Tipo de descargo"
                                required
                                :error-messages="errors"
                                ></v-text-field>
                                
                            </v-form>
                            </v-container>
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <div class="flex-grow-1"></div>
                            <v-btn color="red darken-1" text @click="cerrarModal()">Cerrar</v-btn>
                            <v-btn
                            color="info darken-1"
                            :disabled="!validForm"
                            @click="save()"
                            text
                            v-text="btnTitle"
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
                        v-show="!estado"
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
                    <span>Actualizar tipo descargo</span>
                    </v-tooltip>
                    <v-tooltip top >
                    <template v-slot:activator="{ on }" >
                        <v-btn
                        v-show="!estado"
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
                    <span>Desactivar tipo descargo</span>
                    </v-tooltip>
                    <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-btn
                        v-show="estado"
                        color="teal"
                        class="mx-1"
                        elevation="8"
                        small
                        dark
                        :disabled="item.id < 0"
                        v-on="on"
                        @click="restaurar(item)"
                        >
                        <v-icon>mdi-restore</v-icon>
                        </v-btn>
                    </template>
                    <span>Activar tipo descargo</span>
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
            validForm: false,
            loader: false, modal: false,
            buscarTipoDescargo: '',
            estado: false,
            encabezadosTbl: [
                { text: "Tipo de descargo", value: "tipoDescargo", align: "center" },
                { text: "Acciones", value: "action", sortable: false, align: "center" },
            ],
            tiposDescargos: [],
            tiposDescargosDesactivos: [],
            tipoDescargo: { id: null, tipoDescargo: '' },
            reglas: {
                requerido: (v) => !!v || "Campo requerido",
                min: (v) => (v && v.length >= 2 && v.length <= 100) ||
                "Este campo debe ser mayor a 2 caracteres",
                expresion: (v) =>
                /^[A-Za-z0-9-ÑñáéíóúÁÉÍÓÚ \s]+$/g.test(v) ||
                "Este campo no puede tener caracteres especiales",
            },
            errors: []
        }
    },
    computed: {
        formTitle()
        {
            return this.tipoDescargo.id != null ? 'Actualizar tipo de descargo' : 'Guardar tipo de descargo'
        },
        btnTitle ()
        {
            return this.tipoDescargo.id != null ? 'Actualizar' : 'Guardar'
        }
    },
    mounted()
    {
        this.obtenerTiposDescargos()
    },
    methods: {
        obtenerTiposDescargos()
        {
            this.loader = true
            axios.get(`/Api/inventario/descargos/tipos`)
                .then( ( { data: { tiposDescargos } } ) => {
                this.loader = false
                this.tiposDescargos = tiposDescargos.filter((r) => r.eliminado == false );
                this.tiposDescargosDesactivos = tiposDescargos.filter((r) => r.eliminado == true);
                })
                .catch( (e) => {
                    this.loader = false
                });
        },
        save()
        {
            const path = this.tipoDescargo.id != null   
                            ? `/Api/inventario/descargo/tiposDescargo/${this.tipoDescargo.id}/edit` 
                            : '/Api/inventario/descargo/tiposDescargo'
            
            axios.post(path, this.tipoDescargo )
                .then( response => {

                    this.loader = false
                    
                    if (response.status == 200) 
                    {
                        const { respuesta, mensaje } = response.data;

                        if ( respuesta ) 
                        { 
                            const { tipoDescargo } = response.data
                        
                            this.alerta( mensaje, 'success', 'Buena hecho')
                            this.obtenerTiposDescargos()
                            this.cerrarModal()
                            
                        } else 
                        {
                            const { tipoDescargo } = response.data;
                            this.errors = tipoDescargo
                        }
                    } else 
                    {
                        this.alerta('Ocurrio un error en el servidor', 'error', 'IMPORTANTE')
                    }
                }).catch( () => {
                    this.alerta('Ocurrio un error en el servidor', 'error', 'IMPORTANTE')
                })
        },
        mostrarModal( { ...tipoDescargo } )
        {
            this.tipoDescargo = tipoDescargo
            this.modal = true
        },
        cerrarModal()
        {
            this.modal = false
            setTimeout(() => {
                this.tipoDescargo = { id: null, tipoDescargo: '' }
                this.resetValidation()
            }, 200 )
        },
        resetValidation() {
            this.errors = [];
            this.$refs.formTipoDescargo.resetValidation();
        },
        eliminar( {...tipoDescargo })
        {
            Swal.fire({
                title: "INFORMACION",
                text: `¿Estas que quieres desactivar el tipo de descargo ${ tipoDescargo.tipoDescargo } ?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3698e3",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si",
                cancelButtonText: 'No'
            }).then((result) => {
                this.cambiarEstado( tipoDescargo )
            })
        },
        restaurar( {... tipoDescargo }) 
        {
            Swal.fire({
                title: "INFORMACION",
                text: `¿Estas que quieres activar el tipo de descargo ${ tipoDescargo.tipoDescargo } ?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3698e3",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si",
                cancelButtonText: 'No'
            }).then((result) => {
                this.cambiarEstado( tipoDescargo, false )
            })
        },
        cambiarEstado( tipoDescargo, eliminar = true) 
        {
            axios
            .delete(`/Api/inventario/descargo/tiposDescargo/${tipoDescargo.id}/${eliminar}`)
            .then((response) => {

                if (response.status == 200) {
                    const { respuesta, mensaje } = response.data;

                    if (respuesta) {
                        this.obtenerTiposDescargos()
                        this.alerta( mensaje, 'success', '¡Bien hecho!');
                    } else {
                        this.alerta( mensaje, 'error', '¡Importante!');
                    }
                }
            })
            .catch(console.error);
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

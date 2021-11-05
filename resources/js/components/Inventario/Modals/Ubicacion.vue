<template>
    <v-row justify="center">
        <v-dialog v-model="dialog" persistent max-width="700px">
            <v-card>
                <v-card-title class="headline grey lighten-2" primary-titles>
                    <span class="headline" v-text="modalTittle"></span>
                </v-card-title>

                <v-progress-linear
                    indeterminate
                    v-if="loader" 
                    color="yellow darken-2" 
                    >
                </v-progress-linear>

                <v-card-text>
                    <v-container>
                        <v-form
                            ref="formUbicacion"
                            v-model="validForm"
                            :lazy-validation="true"
                            >
                            <v-text-field
                                append-icon="md-home"
                                v-model="ubicacion.ubicacion"
                                @keyup="errors = []"
                                :rules="[(v) => !!v || 'La ubicación es requerido']"
                                label="Detalle de la ubicación"
                                required
                                :error-messages="errors"
                            >
                            </v-text-field>
                        </v-form>
                    </v-container>
                </v-card-text>

                <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="red darken-1" text @click="cerrarModal">Cerrar</v-btn>
                    <v-btn
                        color="info darken-1"
                        :disabled="!validForm"
                        @click="save()"
                        text
                        v-text="'Guardar'"
                    ></v-btn>
                    </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
<script>
export default {
    data() {
        return {
            loader: false,
            validForm: false,
            dialog: false,
            ubicacion: { id : null, ubicacion: '' },
            errors: [],
        }
    },
    computed: {
        modalTittle () {
            return this.ubicacion.id  ? 'Actualizar Ubicacion' : 'Agregar Ubicacion'
        }
    },
    methods: {
        save() {
            this.loader = true
            const path =
                this.ubicacion.id == null
                ? "/Api/ubicaciones"
                : `/Api/ubicaciones/${this.ubicacion.id}/edit`;
            axios.post(path, this.ubicacion ).then( response => {
                if (response.status == 200) {
                    this.loader = false

                    const { respuesta, mensaje, ubicacion } = response.data;

                    if (respuesta) {
                        this.alerta( mensaje, 'success', 'Buena hecho');
                        this.$emit("saved", {...ubicacion} ); 
                        this.cerrarModal();
                    } else {
                        let { ubicacion } = response.data

                        this.errors = ubicacion
                    }
                }
            }).catch( () => {
                this.loader = false
                this.alerta( 'Ocurrio un error', 'error', 'INFORMACION')
            })
        },
        mostrarModal()
        {
            setTimeout(() => {
                this.ubicacion = { id: null, ubicacion: '' }
                this.$refs.formUbicacion.resetValidation()
            }, 50);
            this.dialog = true;
        },
        cerrarModal() {
            this.errors = []
            this.ubicacion = { id: null, ubicacion: '' }
            this.dialog = false;
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

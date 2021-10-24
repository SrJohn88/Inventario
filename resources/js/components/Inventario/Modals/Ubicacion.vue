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
                            ref="formCuenta"
                            v-model="validForm"
                            :lazy-validation="true"
                            >

                            <v-text-field
                                append-icon="md-home"
                                v-model="ubicacion.ubicacion"
                                @keyup="errors = []"
                                :rules="[(v) => !!v || 'Nombre es requerido']"
                                label="Nombre"
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
            cerrarModal: false,
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
            const path =
                this.ubicacion.id == null
                ? "/Api/ubicaciones"
                : `/Api/ubicaciones/${this.ubicacion.id}/edit`;
            axios.post(path, this.ubicacion ).then(( response) => {
                if (response.status == 200) {
                    const { respuesta, mensaje } = response.data;

                    if (respuesta) {
                        this.obtenerUbicaciones();

                        this.alerta( mensaje, 'success', 'Buena hecho');
                        this.cerrarModal();
                    } else {
                        this.alerta( mensaje, 'error', 'Importante');
                    }
                }
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

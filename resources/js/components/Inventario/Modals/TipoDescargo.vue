<template>
    <v-row justify="center">
        <v-dialog v-model="dialog" persistent max-width="700px">
            <v-card>

                <v-card-title class="headline grey lighten-2" primary-titles>
                    <span class="headline" v-text="'Agregar nuevo tipo de descargo'"></span>
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
                            ref="formTipoDescargo"
                            v-model="form"
                            :lazy-validation="true"
                            >

                            <v-text-field
                                append-icon="md-home"
                                v-model="tipoDescargo.tipoDescargo"
                                @keyup="errors = []"
                                :rules="[reglas.min, reglas.requerido, reglas.expresion]"
                                label="Tipo de descargos"
                                required
                                :error-messages="errors"
                            >
                            </v-text-field>

                        </v-form>
                    </v-container>
                </v-card-text>

            </v-card>
        </v-dialog>
    </v-row>
</template>

<script>
export default {
    data() 
    {
        return {
            dialog: false,
            form: false,
            loader: false,
            errors: [],
            tipoDescargo: { id: null, tipoDescargo: '' },
            reglas : {
                requerido: (v) => !!v || "Campo requerido",
                min: (v) =>
                (v && v.length >= 2 && v.length <= 100) ||
                "Este campo debe ser mayor a 2 caracteres",
                expresion: (v) =>
                /^[A-Za-z0-9-ñáéíóúÁÉÍÓÚ\s]+$/g.test(v) ||
                "Este campo no puede tener caracteres especiales",
            }
        }
    },
    methods: {

        mostrarModal ()
        {
            this.dialog = true
        },
        cerrarModal ()
        {
            this.dialog = false
        }
    }
}
</script>

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

                <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="red darken-1" text @click="cerrarModal()">Cerrar</v-btn>
                    <v-btn
                        color="info darken-1"
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
                /^[A-Za-z0-9-ÑñáéíóúÁÉÍÓÚ\s]+$/g.test(v) ||
                "Este campo no puede tener caracteres especiales",
            }
        }
    },
    methods: {
        mostrarModal ()
        {
            setTimeout(() => {
                this.tipoDescargo = { id: null, tipoDescargo: '' }
                this.$refs.formTipoDescargo.resetValidation()
            }, 100);
            
            this.dialog = true
        },
        cerrarModal ()
        {
            setTimeout(() => {
                this.tipoDescargo = { id: null, tipoDescargo: '' }
                this.$refs.formTipoDescargo.resetValidation()
            }, 100);
            this.dialog = false
        },
        save ()
        {
            this.loader = true
            const path = '/Api/inventario/descargo/tiposDescargo'

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
                            this.cerrarModal()
                            this.$emit("saved", { ...tipoDescargo } )
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

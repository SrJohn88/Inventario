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
                            ref="formMarca"
                            v-model="validForm"
                            :lazy-validation="true"
                            >

                            <v-text-field
                                append-icon="md-home"
                                v-model="marca.marca"
                                @keyup="errorsNombre = []"
                                :rules="[reglas.min, reglas.requerido, reglas.expresion]"
                                label="Nombre"
                                required
                                :error-messages="errorsNombre"
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
            marca: { id : null, marca: '' },
            errorsNombre: [],
            reglas: {
                requerido: (v) => !!v || "Nombre de la marca es requerido",
                min: (v) =>
                (v && v.length >= 2 && v.length <= 100) ||
                "Nombre de la marca debe ser mayor a 2 caracteres",
                expresion: (v) =>
                /^[A-Za-z0-9-ñáéíóúÁÉÍÓÚ\s]+$/g.test(v) ||
                "Nombre de la marca no puede tener caracteres especiales",
            },
        }
    },
    computed: {
        modalTittle () {
            return this.marca.id  ? 'Actualizar Marca' : 'Agregar Marca'
        }
    },
    methods: {
        save() {
            this.loader = true

            const path = '/Api/marcas'

            axios.post(path, this.marca )
                .then( response => {
                    this.loader = false

                    if (response.status == 200) 
                    {
                    const { respuesta, mensaje } = response.data;

                    if ( respuesta ) 
                    {                  
                        const { marca } = response.data
                        
                        this.alerta( mensaje, 'success', 'Buena hecho');
                        
                        this.cerrarModal();
                        this.$emit("saved", {...marca} );

                    } else {
                        const { marca } = response.data;
                        this.errorsNombre = marca
                }
                    }
                })
        },
        mostrarModal()
        {
            setTimeout(() => {
                this.marca = { id: null, marca: '' }
                this.$refs.formMarca.resetValidation()
            }, 100);
            this.dialog = true;
        },
        cerrarModal()
        {
            this.dialog = false
            this.marca = { id: null, marca: ''}
            this.errorsNombre = []
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

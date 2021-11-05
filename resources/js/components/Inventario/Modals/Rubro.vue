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
                            ref="formRubro"
                            v-model="validForm"
                            :lazy-validation="true"
                            >

                            <v-text-field
                                append-icon="md-home"
                                v-model="rubro.rubro"
                                @keyup="errors = []"
                                :rules="[ reglas.requerido, reglas.min, reglas.expresion ]"
                                label="Ingrese el nombre del rubro"
                                required
                                :error-messages="errors"
                            >
                            </v-text-field>
                        </v-form>
                    </v-container>
                </v-card-text>

                <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="red darken-1" text @click="cancelar">Cerrar</v-btn>
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
            rubro: { id : null, rubro: '' },
            errors: [],
            reglas: {
                requerido: (v) => !!v || "Nombre de la cuenta es requerido",
                min: (v) =>
                (v.length >= 2 && v.length <= 100) ||
                "Nombre de la cuenta debe ser mayor a 2 caracteres",
                expresion: (v) =>
                /^[A-Za-z0-9-ñ \s]+$/g.test(v) ||
                "Nombre de la cuenta no puede tener caracteres especiales",
            },
        }
    },
    computed: {
        modalTittle () {
            return this.rubro.id  ? 'Actualizar Rubro' : 'Agregar Rubro'
        }
    },
    methods: {
        save() {
            
            this.loader = true;

            let path = this.rubro.id
                ? `/Api/rubros/${this.rubro.id}/edit`
                : "/Api/rubros";
            axios
            .post(path, this.rubro)
            .then( response => {
                this.loader = false

                if (response.status == 200) {                
                    
                    let { respuesta, mensaje, rubro } = response.data;

                    if (respuesta) {

                    Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: "Buen trabajo",
                        text: mensaje,
                        showConfirmButton: false,
                        timer: 1500,
                    });
                        this.$emit("saved", {...rubro} ); 
                        this.cancelar()
                    } else 
                    {
                        let { rubro } = response.data
                        this.errors = rubro
                    }
                }
            })
            .catch( () => {
                this.loader = false
                Swal.fire({
                        position: "top-end",
                        icon: "error",
                        title: "INFORMACIÓN",
                        text: 'Ocurrio un error en el sistema',
                        showConfirmButton: false,
                        timer: 1500,
                    });
            });
        },
        mostrarModal()
        {
            setTimeout(() => {
                this.rubro = { id: null, rubro: '' }
                this.$refs.formRubro.resetValidation()
            }, 100);
            this.dialog = true;
        },
        cancelar() {
            this.errors = []
            this.dialog = false;
        }
    }
}
</script>

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
                                v-model="cuenta.cuenta"
                                @keyup="errors = []"
                                :rules="[
                                    reglas.requerido,
                                    reglas.min,
                                    reglas.expresion,
                                ]"
                                label="Ingrese el nombre de la cuenta"
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
            reglas: {
                requerido: (v) => !!v || "Nombre de la cuenta es requerido",
                min: (v) =>
                (v.length >= 2 && v.length <= 100) ||
                "Nombre de la cuenta debe ser mayor a 2 caracteres",
                expresion: (v) =>
                /^[A-Za-z0-9- \s]+$/g.test(v) ||
                "Nombre de la cuenta no puede tener caracteres especiales",
            },
            cerrarModal: false,
            loader: false,
            validForm: false,
            dialog: false,
            cuenta: { id: null, cuenta: '' },
            errors: [],
        }
    },
    computed: {
        modalTittle () {
            return this.cuenta.id  ? 'Actualizar Cuenta' : 'Agregar Cuenta'
        }
    },
    methods: {
        save() {
        const path = this.cuenta.id == null ? '/Api/cuentas' : `/Api/cuentas/${this.cuenta.id}/edit`; 
        axios
        .post(path, this.cuenta)
        .then((response) => {
          if (response.status == 200) {
            let { respuesta, mensaje } = response.data;

            if (respuesta) {

              this.alerta( mensaje, 'success', '¡Bien hecho!');

              this.dialog = false;
            } else
            {
                this.alerta( mensaje, 'info', '¡Información!');

              this.dialog = false;
            }
          }
        })
        .catch(() => {
          this.alerta( mensaje, 'error', '¡Algo salio mal!');
          this.modal = false;

        });
    },
    cancelar() {
        this.validForm = false;
        this.dialog = false;
        this.cuenta = { id: null, cuenta:''}; 
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

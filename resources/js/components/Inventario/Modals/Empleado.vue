<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent max-width="700px">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-titles>
          <span class="headline" v-text="'Agregar empleado de ITCHA'"></span>
        </v-card-title>

        <v-progress-linear indeterminate v-if="loader" color="yellow darken-2">
        </v-progress-linear>

        <v-card-text>
          <v-container>
            <v-form
              ref="formEmpleadosModal"
              v-model="validForm"
              :lazy-validation="true"
            >
              <v-col cols="12">
                <v-text-field
                  append-icon="mdi-account-circle"
                  v-model="empleado.nombre"
                  @keyup="errors.nombre = []"
                  :rules= "[
                            reglas.requerido,
                            reglas.min,                        
                            reglas.expresion,
                            ]"
                  label="Nombres"
                  required
                  :error-messages="errors.nombre"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-text-field
                  append-icon="mdi-account-circle"
                  v-model="empleado.apellido"
                  @keyup="errors.apellido = []"
                  :rules= "[
                            reglas.requerido,
                            reglas.min,                        
                            reglas.expresion,
                            ]"
                  label="Apellidos"
                  required
                  :error-messages="errors.apellido"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-text-field
                  append-icon="mdi-account-card-details"
                  v-model="empleado.dui"
                  @keyup="errors.dui = []"
                  :rules="[ reglas.dui ]"
                  label="DUI"
                  :error-messages="errors.dui"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-autocomplete
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
            </v-form>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <div class="flex-grow-1"></div>
            <v-btn color="red darken-1" text @click="cerrarModal">Cerrar</v-btn>
            <v-btn color="info darken-1" @click="save()" text v-text="'Guardar'">
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
export default {
  data() {
    return {
      dialog: false,
      loader: false,
      validForm: false,
      errors: [],
      reglas: {
        requerido: (v) => !!v || "Nombre del empleado es requerido",
        min: (v) =>
          ( v && v.length >= 2 && v.length <= 100) ||
          "Nombre de la cuenta debe ser mayor a 2 caracteres",
        expresion: (v) =>
          /^[A-Za-záéíóúÁÉÍÓÚ\s]+$/g.test(v) ||
          "Nombre del empleado solo puede contener letras",
        dui: (v) =>
          /^[0-9]{8}-[0-9]{1}$/.test(v) || "Ingresa un numero de DUI valido",
      },
      empleado: {
        id: null,
        nombre: '',
        apellido: '',
        dui: '',
        cargo: { id: null, cargo: '' },
      },
      cargos: [],
    };
  },
  mounted() {
    this.obtenerCargos();
  },
  methods: {
    obtenerCargos() {
      this.loader = true;
      axios
        .get("/Api/cargos")
        .then(({ data: { cargos } }) => {
          this.cargos = cargos.filter((r) => r.eliminado == false);
          this.loader = false;
        })
        .catch(() => {
          this.loader = false;
        });
    },
    save() {
        this.loader = true

      const path = `/Api/empleados`;
      axios
        .post(path, this.empleado)
        .then( response => {

            this.loader = false

            if (response.status == 200) 
            {
                const { respuesta, mensaje, empleado} = response.data;
                
                if (respuesta) 
                {
                    this.alerta(mensaje, "success", "¡Bien hecho!")
                    this.$emit("saved", empleado ); 
                    this.cerrarModal()           
                
                } else {            
                    let { errors } = response.data;
                    this.errors = errors;
                }
            } else 
            {
                this.alerta( 'Ocurrio un error en el servidor.', 'error', '¡IMPORTANTE!')
            }
          
        })
        .catch(() => {
            this.alerta( 'Ocurrio un error en el servidor', 'error', '¡IMPORTANTE!')
        })
    },
    mostrarModal() {
        setTimeout(() => {
            this.empleado = {
            id: null,
            nombres: '',
            apellidos: '',
            dui: '',
            cargo: { id: null, cargo: '' }
            };
            this.resetValidation();
        }, 50);

      this.dialog = true;
    },
    cerrarModal() {
      this.dialog = false;      
    },
    resetValidation() {
      this.errors = [];
      this.$refs.formEmpleadosModal.resetValidation();
    },
    alerta(mensaje, icono = "info", titulo = "") {
      Swal.fire({
        position: "top-end",
        icon: icono,
        title: titulo,
        text: mensaje,
        showConfirmButton: false,
        timer: 1500,
      });
    },
  },
};
</script>

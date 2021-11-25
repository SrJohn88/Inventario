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
          Cuentas de inventario
          <div class="flex-grow-1"></div>
          <v-text-field
            v-model="buscarCuentas"
            label="Buscar Cuentas"
            hide-details
          ></v-text-field>
        </v-card-title>

        <v-data-table
          :headers="TheadTable"
          :items=" mostrarCuentasEliminadas ? cuentasEliminadas : cuentas"
          :footer-props="{
            'items-per-page-options': [5, 10, 20, 30, 40],
            'items-per-page-text': 'Registros Por Página',
          }"
          :items-per-page="5"
          :search="buscarCuentas"
          multi-sort
          class="elevation-1"
          no-data-text="No hay cuentas para mostrar"
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
                    v-show= "!mostrarCuentasEliminadas"
                    class="mb-2"
                    style="font-size: 10px"
                    v-on="on"
                  >
                    Agregar Cuenta&nbsp;
                    <v-icon>mdi-plus-box-multiple-outline</v-icon>
                  </v-btn>

                  <!-- MOSTRAR CUENTAS -->
                  <v-checkbox
                    v-model="mostrarCuentasEliminadas"
                    class="mx-10"
                    style="margin-top: 1.5rem"
                    label="Cuentas desactivadas"
                  />
                </template>
                <v-card>
                  <v-card-title class="headline grey lighten-2" primary-titles>
                    <span class="headline" v-text="formTitle"></span>
                  </v-card-title>
                  <v-card-text>
                    <v-container>
                      <v-form
                        ref="formCuenta"
                        v-model="validForm"
                        :lazy-validation="true"
                      >
                        <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="cuenta.cuenta"
                          @keyup="errorCuenta = []"
                          :rules= "[
                            reglas.requerido,
                            reglas.min,
                            reglas.expresion,
                          ]"
                          label="Nombre"
                          required
                          :error-messages="errorCuenta"
                        ></v-text-field>
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
                  v-show="!mostrarCuentasEliminadas"
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
              <span>Actualizar Cuenta</span>
            </v-tooltip>
            <v-tooltip top >
              <template v-slot:activator="{ on }" >
                <v-btn
                  v-show="!mostrarCuentasEliminadas"
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
              <span>Desactivar Cuenta</span>
            </v-tooltip>
            <v-tooltip top >
              <template v-slot:activator="{ on }" >
                <v-btn
                  v-show=" mostrarCuentasEliminadas"
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
              <span>Activar Cuenta</span>
            </v-tooltip>
          </template>

        </v-data-table>        
      </v-card>
    </div>
  </div>
</template>



<script>
export default {
  data() {
    return {
      loader: false,
      modal: false,
      buscarCuentas: "",
      cuentas: [],
      mostrarCuentasEliminadas: false,
      cuentasEliminadas: [],
      cuenta: { id: null, cuenta: "" },
      validForm: true,
      TheadTable: [
        { text: "Cuenta", value: "cuenta", align: 'center', },
        { text: "Acciones", value: "action", sortable: false, align: "center" },
      ],
      errorCuenta: [],
      reglas: {
        requerido: (v) => !!v || "Nombre de la cuenta es requerido",
        min: (v) =>
          (v.length >= 2 && v.length <= 100) ||
          "Nombre de la cuenta debe ser mayor a 2 caracteres",
        expresion: (v) =>
          /^[A-Za-z0-9-ñáéíóúÁÉÍÓÚ \s]+$/g.test(v) ||
          "Nombre de la cuenta no puede tener caracteres especiales",
      },
    };
  },
  computed: {
    formTitle() {
      return this.cuenta.id == null ? "Agregar una cuenta" : "Actualizar una cuenta";
    },
    tituloBtnGuardar() {
      return this.cuenta.id == null ? "Guardar cuenta" : "Actualizar cuenta";
    },
  },
  mounted() {
    this.obtenerCuentas();
  },
  methods: {
    obtenerCuentas() {
      this.cuentas = [];
      this.cuentasEliminadas = [];
      axios
        .get("/Api/cuentas")
        .then( ( { data: { cuentas } } ) => {
          this.cuentas = cuentas.filter((r) => r.eliminado == false);
          this.cuentasEliminadas = cuentas.filter((r) => r.eliminado == true);
        })
        .catch(console.error);
    },
    save() {
        const path = this.cuenta.id == null ? '/Api/cuentas' : `/Api/cuentas/${this.cuenta.id}/edit`; 
        axios
        .post(path, this.cuenta)
        .then((response) => {
          if (response.status == 200) {
            let { respuesta, mensaje } = response.data;

            if (respuesta) {
              
              this.obtenerCuentas();

              this.alerta( mensaje, 'success', '¡Bien hecho!');

              this.cerrarModal();
            } else
            {
              const { cuenta } = response.data;
              this.errorCuenta = cuenta;
            }
          }
        })
        .catch(() => {
          this.alerta( mensaje, 'error', '¡Algo salio mal!');
          this.modal = false;

        });
    },
    eliminar( { ...cuenta } )
    {
      Swal.fire({
        title: "INFORMACION",
        text: `¿Estas seguro de desactivar la cuenta ${cuenta.cuenta} ?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3698e3",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
              this.cambiarEstadoCuenta(cuenta);
            }
          });

    },
    restaurar ( {...cuenta} )
    {
        Swal.fire({
        title: "INFORMACION",
        text: `¿Quieres activar la cuenta ${cuenta.cuenta} ?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3698e3",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
              this.cambiarEstadoCuenta(cuenta, false);
            }
          });
    },
    cambiarEstadoCuenta( cuenta, eliminar = true) {
      axios
            .get(`/Api/cuentas/${cuenta.id}/${eliminar}`)
            .then((response) => {

              if (response.status == 200) {
              const { respuesta, mensaje } = response.data;

              if (respuesta) {
                this.alerta( mensaje, 'success', 'Buena hecho');                
                this.obtenerCuentas();

              } else {
                this.alerta( mensaje, 'error', 'Importante');
              }
            }
            })
            .catch(console.error);
    },
    mostrarModal( cuenta )
    {
        this.cuenta = { ...cuenta };
        this.modal = true;

    },
    cerrarModal()
    {
        this.modal = false;
        setTimeout(() => {
          this.cuenta = { id: null, cuenta: "" };
          this.resetValidation();
        }, 300);
    },
    resetValidation() {
      this.errorCuenta = [];
      this.$refs.formCuenta.resetValidation();
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
  },
};
</script>
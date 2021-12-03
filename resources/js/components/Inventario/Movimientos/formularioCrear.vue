<template>
  <div class="content">
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100"
    >
      <v-card>
        <v-card-title v-text="'Movimientos de Inventario'">
          <div class="flex-row-1"></div>
        </v-card-title>

        <v-container fluid>
          <v-form
            ref="formMovimiento"
            v-model="formularioMovimiento"
            :lazy-validation="true"
          >
            <v-card-title>
              <v-row>
                <v-col cols="6">
                  <v-autocomplete
                  append-icon="fas fa-code"
                    v-model="movimiento.tipoMovimiento"
                    :items="tiposMovimientos"
                    :rules="[
                      (v) => !!v || 'Tipo de movimiento es requerido',
                    ]"
                    label="Tipo de movimientos"
                    item-text="tipo"
                    item-value="id"
                    return-object
                    clearable
                    @change="tipoMovimientoChange"
                    :error-messages="errors['tipoMovimiento.id']"
                    :menu-props="{ closeOnClick: true }"
                  ></v-autocomplete>
                </v-col>

                <!--  CAJA DE RECIBE  !-->
                <v-col cols="5">
                  <v-autocomplete
                  append-icon="fas fa-user"
                    v-model="movimiento.recibe"
                    :items="empleados"
                    required
                    :rules="[
                      (v) => !!v || 'Selecciona quien recibe el activo',
                    ]"
                    label="Recibe"
                    :item-text="EmpleadoNombreCompleto"
                    item-value="id"
                    return-object
                    @change="errors['recibe.id'] = []"
                    :error-messages="errors['recibe.id']"
                    clearable
                    :menu-props="{ closeOnClick: true }"
                  ></v-autocomplete>
                </v-col>

                <v-col cols="1" md="1">
                  <Empleado @saved="onSavedEmpleado" ref="empleado" />
                  <v-btn
                    elevation="5"
                    class="mt-8"
                    text
                    icon
                    color="primary"
                    @click="mostrarModalEmpleados()"
                    dark
                  >
                    <v-icon>mdi-plus-circle</v-icon>
                  </v-btn>
                </v-col>

                <!-- FIN DE QUIEN RECIBE !-->

                <!-- INICIO DE APRUEBA !-->

                <v-col cols="5">
                  <v-autocomplete
                    append-icon="fas fa-user"
                    v-model="movimiento.aprueba"
                    :items="empleados"                    
                    :rules="[]"
                    label="Aprobado"
                    :item-text="EmpleadoNombreCompleto"
                    item-value="id"
                    return-object
                    clearable
                    @change="errors['aprueba.id'] = []"
                    :error-messages="errors['aprueba.id']"
                    :menu-props="{ closeOnClick: true }"
                  ></v-autocomplete>
                </v-col>

                <v-col cols="1" md="1">
                  <Empleado @saved="onSavedEmpleado" ref="empleado" />
                  <v-btn
                    elevation="5"
                    class="mt-8"
                    text
                    icon
                    color="primary"
                    @click="mostrarModalEmpleados()"
                    dark
                  >
                    <v-icon>mdi-plus-circle</v-icon>
                  </v-btn>
                </v-col>

                <!-- FIN DE APRUEBA  !-->

                <!-- INICIO APROBADO POR GERENCIA !-->
                
                <v-col cols="5">
                  <v-autocomplete
                  append-icon="fas fa-user"
                    v-model="movimiento.gerencia"
                    :items="empleados"                    
                    :rules="[]"
                    label="Aprobado por gerencia"
                    :item-text="EmpleadoNombreCompleto"
                    item-value="id"
                    return-object
                    clearable
                    :menu-props="{ closeOnClick: true }"
                  ></v-autocomplete>
                </v-col>

                <v-col cols="1" md="1">
                  <Empleado @saved="onSavedEmpleado" ref="empleado" />
                  <v-btn
                    elevation="5"
                    class="mt-8"
                    text
                    icon
                    color="primary"
                    @click="mostrarModalEmpleados()"
                    dark
                  >
                    <v-icon>mdi-plus-circle</v-icon>
                  </v-btn>
                </v-col>

                <!-- FIN DE APROBADO POR GERENCIA  !-->

                <v-col cols="6" v-show="movimiento.tipoMovimiento.id != null && ( movimiento.tipoMovimiento.id != 2)">
                  <v-menu
                    v-model="menu"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"                    
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                      append-icon="fas fa-calendar-alt"
                        v-model="movimiento.fecha"
                        label="Seleccione fecha de reingreso"
                        prepend-icon="mdi-calendar"
                        readonly
                        required                        
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="movimiento.fecha"
                      locale="es"
                      @input="menu = false"
                      :min="getFechaActual"
                    ></v-date-picker>
                  </v-menu>
                </v-col>                

                <v-col cols="5" 
                  v-if="movimiento.tipoMovimiento.id != null && ( movimiento.tipoMovimiento.id != 4)"
                >
                  <v-autocomplete
                    append-icon="fas fa-map-marker-alt"
                    v-model="movimiento.ubicacion"
                    :items="ubicaciones"
                    required
                    :rules="[(v) => !!v || 'Ubicación del activo es requerido']"
                    label="Se translado a:"
                    item-text="ubicacion"
                    item-value="id"
                    return-object
                    clearable
                    :menu-props="{ closeOnClick: true }"
                  ></v-autocomplete>
                </v-col>

                <v-col
                  cols="1"
                  md="1"
                  v-if="
                    movimiento.tipoMovimiento.id != null && ( movimiento.tipoMovimiento.id != 4)
                  "
                >
                  <Ubicacion @saved="onSavedUbicacion" ref="ubicacion" />
                  <v-btn
                    elevation="5"
                    class="mt-8"
                    text
                    icon
                    color="primary"
                    @click="mostrarModalUbicacion()"
                    dark
                  >
                    <v-icon>mdi-plus-circle</v-icon>
                  </v-btn>
                </v-col>

                <v-col cols="6" v-show="movimiento.tipoMovimiento.id != null && movimiento.tipoMovimiento.id == 4">
                  <v-textarea
                    append-icon="fas fa-file"
                    v-model="movimiento.detalleSalida"
                    label="Se Traslada a:"
                    rows="1"
                  ></v-textarea>
                </v-col>

                <v-col cols="12">
                  <v-textarea
                    append-icon="fas fa-eye"
                    v-model="movimiento.observacion"
                    label="Observacion"
                    rows="2"
                  ></v-textarea>
                </v-col>
              </v-row>
            </v-card-title>

            <v-data-table
              :headers="headMovimientos"
              :items="inventarios"
              no-data-text="No hay movimientos agregados"
              :items-per-page="10"
              :search="buscarInventario"
              :footer-props="{
                'item-per-page-options': [10, 20, 30],
                showFirstLastPage: true,
              }"
            >
              <template v-slot:top>
                <v-toolbar flat color="white">
                  <div class="flex-grow-1"></div>
                  <v-dialog
                    v-model="modalNMovimientos"
                    persistent
                    max-width="1240px"
                    scrollable
                  >
                    <template v-slot:activator="{ on }">
                      <v-btn
                        elevation="10"
                        color="blue  darken-3"
                        dark
                        class="mb-2"
                        v-on="on"
                      >
                        Agregar Nuevo Movimiento&nbsp;
                        <v-icon>mdi-plus-box-multiple-outline</v-icon>
                      </v-btn>
                    </template>

                    <v-card>
                      <v-card-title
                        class="headline grey lighten-2"
                        primary-title
                      >
                        <span class="headline" v-text="formTitle"></span>
                      </v-card-title>
                      <v-card-text>
                        <n-inventario ref="inventario" @added="onAddedItem" />
                      </v-card-text>
                      <v-divider></v-divider>
                      <v-card-actions>
                        <div class="flex-grow-1"></div>
                        <v-btn
                          color="success darken-1"
                          text
                          @click="modalNMovimientos = false"
                          >Finalizar
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                </v-toolbar>
              </template>

              <template v-slot:item.falla="props">
                <v-edit-dialog
                  :return-value.sync="props.item.falla"
                  large
                  persistent
                  @save="guardarFallar(props.item)"
                  @cancel="cancelarFalla"
                >
                  {{ props.item.falla }}
                  <template v-slot:input>
                    <div class="mt-4 text-h6">Falla:</div>
                    <v-text-field
                      v-model="props.item.falla"
                      :rules="[]"
                      label="Descripción"
                      single-line
                      counter
                      autofocus
                    ></v-text-field>
                  </template>
                </v-edit-dialog>
              </template>

              <template v-slot:item.observaciones="props">
                <v-edit-dialog
                  :return-value.sync="props.item.observaciones"
                  large
                  persistent
                  @save="guardarFallar(props.item)"
                  @cancel="cancelarFalla"
                >
                  {{ props.item.observaciones }}
                  <template v-slot:input>
                    <div class="mt-4 text-h6">Observaciones:</div>
                    <v-text-field
                      v-model="props.item.observaciones"
                      :rules="[]"
                      label="Observaciones"
                      single-line
                      counter
                      autofocus
                    ></v-text-field>
                  </template>
                </v-edit-dialog>
              </template>
            </v-data-table>
          </v-form>
        </v-container>

        <v-card-actions>
          <div class="flex-grow-1"></div>
          <v-btn color="red darken-1" text @click="cancelar">Cerrar</v-btn>
          <v-btn
            color="info darken-1"
            text
            :disabled="!formularioMovimiento"
            @click="guardarMovimiento"
            >Guardar</v-btn
          >
        </v-card-actions>
      </v-card>
    </div>
  </div>
</template>

<script>
Vue.component("Empleado", require("../Modals/Empleado.vue").default);
Vue.component("Ubicacion", require("../Modals/Ubicacion.vue").default);
Vue.component("n-inventario", require("..//Modals/Movimiento.vue").default);

export default {
  name: "movimiento-crear",
  data() {
    return {
      menu: false,
      formTitle: "Inventario General",
      errors: [],
      formularioMovimiento: true,
      modalNMovimientos: false,
      fechaActual: new Date() ,
      movimiento: {
        tipoMovimiento: { id: null, tipo: "" },
        recibe: { id: null, nombre: "" },
        aprueba: { id: null, nombre: "" },
        gerencia: { id: null, nombre: "" },
        ubicacion: { id: null, ubicacion: "" },
        observaciones: "", detalleSalida: '',
        fecha: '',
        activos: [],
      },
      tiposMovimientos: [],
      ubicaciones: [],
      empleados: [],
      buscarInventario: "",
      headMovimientos: [
        { text: "Codigo", value: "codigo", align: "left" },
        { text: "Descripción", value: "descripcion", align: "left" },
        { text: "Marca", value: "marca.marca", align: "left" },
        { text: "Ubicacion", value: "ubicacion.ubicacion", align: "left" },
        { text: "Serie", value: "serie", align: "left" },
        { text: "Falla", value: "falla", align: "left" },
        { text: "Observaciones", value: "observaciones", align: "left" },
      ],
      inventarios: [],
    };
  },
  computed: {
    getFechaActual () 
    {
      return `${this.fechaActual.getFullYear()}-${ this.fechaActual.getMonth() + 1 < 10 ? '0'+this.fechaActual.getMonth() + 1 : this.fechaActual.getMonth() + 1 }-${ this.fechaActual.getDate() < 10 ? '0'+this.fechaActual.getDate() : this.fechaActual.getDate()}`
    }
  },
  mounted() {
    this.getEmpleados();
    this.getTiposMovimientos();
    this.getUbicaciones();
  },
  methods: {
    EmpleadoNombreCompleto(empleado) {
      return empleado.nombre + " " + empleado.apellido;
    },
    getUbicaciones() {
      axios.get(`/Api/ubicaciones`).then(({ data: { ubicaciones } }) => {
        this.ubicaciones = ubicaciones;
      });
    },
    getInventario() {
      this.$refs.activo = [];
    },
    getEmpleados() {
      axios
        .get("/Api/empleados")
        .then(({ data: { empleados } }) => {
          this.empleados = empleados          
        })
        .catch(console.error);
    },
    getTiposMovimientos() {
      axios
        .get("/Api/movimientos/tipos")
        .then(({ data: { tiposMovimientos } }) => {
          this.tiposMovimientos = tiposMovimientos;
          console.log(this.tiposMovimientos);
        })
        .catch(console.error);
    },
    onAddedItem(valores) {
      this.inventarios = valores;
    },
    getProductFromChild() {},
    guardarMovimiento() {

      let datosValidos = true
      let mensaje = ''

      if ( this.movimiento.tipoMovimiento.id == null || this.inventarios.length == 0) 
      {
          mensaje = 'Debes seleccionar el tipo de movimiento y agregar al menos un activo'
          datosValidos = false
      } else 
      {
          if ( this.movimiento.tipoMovimiento.id == 1 || this.movimiento.tipoMovimiento.id == 3 )
          {
              if ( this.movimiento.ubicacion.id == null || this.movimiento.fecha.length == 0 )
              {
                mensaje = 'El campo se traslada y fecha de reingreso son requeridos'
                datosValidos = false
              }
          } else if ( this.movimiento.tipoMovimiento.id == 4 )
          {
              if ( this.movimiento.detalleSalida.length == 0 || this.movimiento.fecha.length == 0 )
              {
                mensaje = 'El campo se traslada y fecha reingreso son requeridos'
                datosValidos = false
              }
          }
      }

      if ( datosValidos ) {
        Swal.fire({
          title: "¡Importante!",
          text: "Estas seguro/as que los datos son correctos ¿Deseas guardar el movimiento?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Guardar",
          cancelButtonText: "Cancelar",
        }).then((result) => {
          if (result.isConfirmed) {
            let activosTemp = [...this.inventarios];            

            activosTemp.forEach((activo) => {
              
              this.movimiento.activos.push({
                inventario_id: activo.id,
                falla: activo.falla,
                observaciones: activo.observaciones,
              });
            });

            const path = `/Api/inventario/movimientos/save`;

            axios
              .post(path, this.movimiento)
              .then((response) => {
                if (response.status == 200) {
                  const { respuesta, mensaje } = response.data;

                  if (respuesta) {
                    this.alerta(mensaje, "success", "¡Bien hecho!").then(() => {
                      Swal.fire({
                        title: "INFORMACION",
                        text: "¿Quieres agregar otro movimiento?",
                        icon: "info",
                        icon: "info",
                        showCancelButton: true,
                        confirmButtonColor: "#3698e3",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Si",
                        cancelButtonText: "No",
                        allowOutsideClick: false,
                      }).then((result) => {
                        if (result.isConfirmed) {
                          this.movimiento = {
                              tipoMovimiento: { id: null, tipo: "" },
                              recibe: { id: null, nombre: "" },
                              aprueba: { id: null, nombre: "" },
                              gerencia: { id: null, nombre: "" },
                              ubicacion: { id: null, ubicacion: "" },
                              observaciones: "", detalleSalida: '',
                              fecha: '',
                              activos: [],
                            }
                          this.inventarios = []
                          this.errors = [];
                          this.$refs.formMovimiento.resetValidation();
                        } else {
                          window.location = "/inventario/movimientos";
                        }
                      });
                    });
                  } else 
                  {
                    const { errors } = response.data
                    this.errors = errors
                  }
                }
              })
              .catch(console.error);
          }
        });
      } else {
          this.alerta( mensaje , 'warning', 'IMPORTANTE')
      }
    },
    cancelar() {
      window.location = "/inventario/movimientos";
    },
    alerta(mensaje, icono = "info", titulo = "") {
      return new Promise((resolve, reject) => {
        Swal.fire({
          position: "top-end",
          icon: icono,
          title: titulo,
          text: mensaje,
          showConfirmButton: false,
          timer: 1500,
        }).then(resolve);
      });
    },
    guardarFallar(item) {},
    cancelarFalla() {},
    mostrarModalUbicacion() {
      this.$refs.ubicacion.mostrarModal()
    },
    onSavedUbicacion( { ... ubicacion } ) {
        this.ubicaciones.push( ubicacion )
    },  
    onSavedEmpleado( empleado ) 
    {
      this.empleados.push( { ...empleado })
    },
    mostrarModalEmpleados()
    {
      this.$refs.empleado.mostrarModal();
    },
    tipoMovimientoChange()
    {
      this.errors['tipoMovimiento.id'] = []

        this.movimiento.recibe = { id: null, nombre: "" }
        this.movimiento.aprueba = { id: null, nombre: "" }
        this.movimiento.gerencia = { id: null, nombre: "" }
        this.movimiento.ubicacion = { id: null, ubicacion: "" }
        this.movimiento.observaciones = ''
        this.movimiento.detalleSalida = ''
        this.$refs.formMovimiento.resetValidation()
      

    }
  },
};
</script>


    
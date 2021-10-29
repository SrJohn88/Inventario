<template>
  <div class="content">
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100"
    >
      <v-overlay :value="loader" :z-index="'99999999'">
        <v-progress-circular indeterminate size="80" color="grey darken-4"></v-progress-circular>
      </v-overlay>

        <v-card>
        <v-card-title>
          Ubicaciones
          <div class="flex-grow-1"></div>
          <v-text-field
            v-model="buscarUbicacion"
            label="Buscar"
            hide-details
          ></v-text-field>
        </v-card-title>
        <v-data-table
          :headers="encabezados"
          :items="estadoUbicacion ? ubicacionesEliminadas : ubicacionesActivas"
          :footer-props="{
            'items-per-page-options': [5, 10, 20, 30, 40],
            'items-per-page-text': 'Registros Por Página',
          }"
          :items-per-page="5"
          :search="buscarUbicacion"
          multi-sort
          class="elevation-1"
        >

        <template v-slot:top >
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="modal" persistent max-width="700px">
                <template v-slot:activator="{ on }">
                  <v-btn elevation="10" v-if="!estadoUbicacion" color="blue  darken-3" dark class="mb-2" v-on="on">
                    Agregar Ubicación&nbsp;
                    <v-icon>mdi-plus-box-multiple-outline</v-icon>
                  </v-btn>
                  
                  <!-- MOSTRAR UBICACIÓNES -->
                    <v-checkbox 
                        v-model="estadoUbicacion"
                        class="mx-10"
                        style="margin-top: 1.5rem;"
                        label="Mostrar Las Ubicaciones Removidas"
                        value="false"
                    />
                </template>
                <v-card>
                  <v-card-title class="headline grey lighten-2" primary-titles>
                    <span class="headline" v-text="formTitle"></span>
                  </v-card-title>
                  <v-card-text>
                    <v-container>
                      <v-form ref="formUbicacion" v-model="validForm" :lazy-validation="true">
                        <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="ubicacion.ubicacion"
                          @keyup="errorsNombre = []"
                          :rules="[
                            reglas.requerido,
                            reglas.min,
                            reglas.expresion,
                          ]"
                          label="Ubicación"
                          required
                          :error-messages="errorsNombre"
                        ></v-text-field>
                        
                      </v-form>
                    </v-container>
                  </v-card-text>
                  <v-divider></v-divider>
                  <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="red darken-1" text @click="cerrarModal">Cerrar</v-btn>
                    <v-btn
                      color="info darken-1"
                      :disabled="!validForm"
                      @click="save()"
                      text
                      v-text="btnTitle"
                    ></v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </v-toolbar>
          </template>

        <template v-slot:item.action="{ item }" v-slot:activator="{ on }">
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                 v-show="!estadoUbicacion"
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
              <span>Actualizar Ubicacion</span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                   v-show="!estadoUbicacion"
                  color="info"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="eliminarUbicacion(item)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Eliminar Entidad</span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                  v-show="estadoUbicacion"
                  color="teal"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="restaurar(item)"
                >
                  <v-icon>mdi-restore</v-icon>
                </v-btn>
              </template>
              <span>Eliminar Entidad</span>
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
            errorsNombre: [],
            modal: false,
            loader: false,
            validForm: false,
            buscarUbicacion: '',
            encabezados: [
                { text: "ID", value: "id" },
                { text: "Ubicacion", value: "ubicacion" },
                { text: "Acciones", value: "action", sortable: false, align: "center" },
            ],
            estadoUbicacion: false,
            ubicacionesActivas: [], ubicacionesEliminadas: [],
            ubicacion: {id: null, ubicacion: ''},
            reglas: {
            requerido: (v) => !!v || "Ubicacion requerida",
            min: (v) =>
              (v && v.length >= 2 && v.length <= 100) ||
              "El detalle debe ser mayor a 2 caracteres",
            expresion: (v) =>
              /^[A-Za-z0-9-ñáéíóúÁÉÍÓÚ\s]+$/g.test(v) ||
              "La ubicacion no puede tener caracteres especiales",
          },
        }
    },
    computed: {
        formTitle() {
            return this.ubicacion.id == null ? 'Guardar' : 'Actualizar'
        },
        btnTitle() {
            return this.ubicacion.id == null ? 'Guardar' : 'Actualizar'
        }
    },
    mounted() {
      this.obtenerUbicaciones();
    },
    methods: {
        obtenerUbicaciones () {
              axios
          .get("/Api/ubicaciones")
          .then(({ data: { ubicaciones } }) => {
            this.ubicacionesActivas = ubicaciones.filter((r) => r.eliminado == false);
            this.ubicacionesEliminadas = ubicaciones.filter((r) => r.eliminado == true);
          })
          .catch(console.error);
        },
        save() {
            console.log( this.ubicacion );
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
                        const { ubicacion } = response.data;

                        this.errorsNombre = ubicacion;
                    }
                }
            })
        },
        eliminarUbicacion ( {...ubicacion } ) {
          Swal.fire({
          title: "INFORMACION",
          text: `¡Estas seguro de eliminar la ubicacion ${ubicacion.ubicacion} ?`,
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3698e3",
          cancelButtonColor: "#d33",
          confirmButtonText: "Si",
          cancelButtonText: 'No'
        }).then((result) => {
          if (result.isConfirmed) {
            this.cambiarEstadoUbicacion( ubicacion );
          }
        });
        },
        restaurar ( {...ubicacion } ){
            Swal.fire({
              title: "INFORMACION",
              text: `¡Estas seguro de restaurar la ubicacion ${ubicacion.ubicacion} ?`,
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3698e3",
              cancelButtonColor: "#d33",
              confirmButtonText: "Si",
              cancelButtonText: 'No'
            }).then((result) => {
              if (result.isConfirmed) {
                this.cambiarEstadoUbicacion( ubicacion, false );
              }
            });
        },
        cambiarEstadoUbicacion( ubicacion, eliminar = true) {
          axios
            .delete(`/Api/ubicaciones/${ubicacion.id}/${eliminar}`)
            .then((response) => {
              if (response.status == 200) {
              const { respuesta, mensaje } = response.data;

              if (respuesta) {
                this.obtenerUbicaciones();
                this.alerta( mensaje, 'success', '¡Bien hecho!');
              } else {
                this.alerta( mensaje, 'error', '¡Importante!');
              }
            }
            })
            .catch(console.error);
        },
        mostrarModal( {...ubicacion} ) {
          this.ubicacion = ubicacion;
          this.modal = true;
        },
        cerrarModal() {
          this.modal = false;
          setTimeout(() => {
            this.ubicacion = { id: null, ubicacion: '' };
            this.resetValidation();
          }, 300);

        },
        resetValidation() {
          this.errorsNombre = [];
          this.$refs.formUbicacion.resetValidation();
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

<style>

</style>
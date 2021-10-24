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
          Rubros
          <div class="flex-grow-1"></div>
          <v-text-field
            v-model="buscarRubro"
            label="Buscar"
            hide-details
          ></v-text-field>
        </v-card-title>
        <v-data-table
          :headers="encabezados"
          :items="EstadoRubro ? rubrosEliminado : rubrosActivos"
          :footer-props="{
            'items-per-page-options': [5, 10, 20, 30, 40],
            'items-per-page-text': 'Registros Por Página',
          }"
          :items-per-page="5"
          :search="buscarRubro"
          multi-sort
          class="elevation-1"
        >
          <template v-slot:item.action="{ item }" v-slot:activator="{ on }">
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                 v-show="!EstadoRubro"
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
              <span>Actualizar Entidad</span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                   v-show="!EstadoRubro"
                  color="info"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="deleteRubro(item)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Eliminar Entidad</span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                  v-show="EstadoRubro"
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

          <template v-slot:top>
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="modalRubro" persistent max-width="700px">
                <template v-slot:activator="{ on }">
                  <v-btn
                    v-show="!EstadoRubro"
                    elevation="10"
                    color="blue  darken-3"
                    dark
                    class="mb-2"
                    v-on="on"
                  >
                    Agregar Rubro&nbsp;
                    <v-icon>mdi-plus-box-multiple-outline</v-icon>
                  </v-btn>

                    <v-checkbox 
                        v-model="EstadoRubro"
                        class="mx-10"
                        style="margin-top: 1.5rem;"
                        label="Mostrar Los Rubros Removidas"
                        value="false"
                    />

                </template>
                <v-card>
                  <v-card-title class="headline grey lighten-2" primary-titles>
                    <span class="headline" v-text="tituloFormulario"></span>
                  </v-card-title>
                  <v-card-text>
                    <v-container>
                      <v-form
                        ref="formEntidad"
                        v-model="formularioValido"
                        :lazy-validation="true"
                      >
                        <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="rubro.rubro"
                          @keyup="errores = []"
                          :rules="[
                            reglas.requerido,
                            reglas.min,
                            reglas.expresion,
                          ]"
                          label="Ingrese el nombre de la entidad"
                          required
                          :error-messages="errores"
                        ></v-text-field>                      

                      </v-form>
                    </v-container>
                  </v-card-text>
                  <v-divider></v-divider>
                  <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="red darken-1" text @click="cerrarModal"
                      >Cancelar</v-btn
                    >
                    <v-btn
                      color="info darken-1"
                      :disabled="!formularioValido"
                      @click="save()"
                      text
                      v-text="tituloBtnGuardar"
                    ></v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </v-toolbar>
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
      encabezados: [
        { text: "ID", value: "id" },
        { text: "Rubro", value: "rubro" },
        { text: "Acciones", value: "action", sortable: false, align: "center" },
      ],
      rubro: { id: null, rubro: "" },
      EstadoRubro: false,
      rubrosActivos: [],
      rubrosEliminado: [],
      buscarRubro: "",
      modalRubro: false,
      formularioValido: false,
      errores: [],
      reglas: {
        requerido: (v) => !!v || "Nombre de la entidad es requerido",
        min: (v) =>
          (v && v.length >= 2 && v.length <= 100) ||
          "Nombre de la entidad debe ser mayor a 2 caracteres",
        expresion: (v) =>
          /^[A-Za-z0-9-ñáéíóúÁÉÍÓÚ\s]+$/g.test(v) ||
          "Nombre de la entidad no puede tener caracteres especiales",
      },
    };
  },
  computed: {
    tituloFormulario() {
      return this.rubro.id == null ? "Agregar nuevo rubro" : "Actualizar rubro";
    },
    tituloBtnGuardar() {
      return this.rubro.id == null ? "Guardar" : "Actualizar";
    },
  },
  mounted() {
    this.obtenerRubros();
  },
  methods: {
    obtenerRubros() {
      axios
        .get("/Api/rubros")
        .then(({ data: { rubros } }) => {
          this.rubrosActivos = rubros.filter((r) => r.eliminado == false);
          this.rubrosEliminado = rubros.filter((r) => r.eliminado == true);
        })
        .catch(console.error);
    },
    save() {
      console.log( this.rubro );
      const path =
        this.rubro.id == null
          ? "/Api/rubros"
          : `/Api/rubros/${this.rubro.id}/edit`;
      axios.post(path, this.rubro ).then(( response) => {
        if (response.status == 200) {
          const { respuesta, mensaje } = response.data;

          if (respuesta) {
            this.obtenerRubros();
            this.alerta( mensaje, 'success', 'Buena hecho');
            this.cerrarModal();
          } else {
            this.alerta( mensaje, 'error', 'Importante');
          }
        }
      });
    },
    deleteRubro( {... rubro } ) {
        Swal.fire({
        title: "INFORMACION",
        text: `¡Estas seguro de eliminar el rubro ${rubro.rubro} ?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3698e3",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
          this.cambiarEstadoRubro(rubro)
        }
      });
    },
    restaurar ( {...rubro} )
    {
      Swal.fire({
        title: "INFORMACION",
        text: `¿Estas que quieres restaurar el rubro ${rubro.rubro} ?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3698e3",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
          this.cambiarEstadoRubro( rubro, false )
        }
      });

    },
    cambiarEstadoRubro( rubro, eliminar = true) {
      axios
        .delete(`/Api/rubros/${rubro.id}/${eliminar}`)
        .then((response) => {

          if (response.status == 200) {
          const { respuesta, mensaje } = response.data;

          if (respuesta) {
            this.obtenerRubros();
            this.alerta( mensaje, 'success', '¡Bien hecho!');
          } else {
            this.alerta( mensaje, 'error', '¡Importante!');
          }
        }
        })
        .catch(console.error);
    },
    mostrarModal( { ...rubro} ) {
      this.rubro = rubro;
      this.modalRubro = true;
    },
    cerrarModal() {
      this.rubro = { id: null, rubro:"" };
      this.modalRubro = false;
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

<style>
</style>
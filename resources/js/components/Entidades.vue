<template>
  <div class="content">
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100"
    >
      <v-card>
        <v-card-title>
          Entidades
          <div class="flex-grow-1"></div>
          <v-text-field
            v-model="buscarEntidad"
            label="Buscar"
            hide-details
          ></v-text-field>
        </v-card-title>
        <v-data-table
          :headers="theadTable"
          :items="entidades"
          :footer-props="{
            'items-per-page-options': [5, 10, 20, 30, 40],
            'items-per-page-text': 'Registros Por Página',
          }"
          :items-per-page="5"
          :search="buscarEntidad"
          multi-sort
          class="elevation-1"
        >
          <template v-slot:item.action="{ item }" v-slot:activator="{ on }">
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
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
                  color="info"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="deleteEntidad(item)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Eliminar Entidad</span>
            </v-tooltip>
          </template>
          <!-- MODAL -->
          <template v-slot:top>
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="modalEntidad" persistent max-width="700px">
                <template v-slot:activator="{ on }">
                  <v-btn
                    elevation="10"
                    color="blue  darken-3"
                    dark
                    class="mb-2"
                    v-on="on"
                  >
                    Agregar Entidad&nbsp;
                    <v-icon>mdi-plus-box-multiple-outline</v-icon>
                  </v-btn>
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
                          v-model="entidad.entidad"
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
      theadTable: [
        { text: "ID", value: "id" },
        { text: "Entidad", value: "entidad" },
        { text: "Acciones", value: "action", sortable: false, align: "center" },
      ],
      buscarEntidad: "",
      entidades: [],
      modalEntidad: false,
      entidad: { id: null, entidad: "" },
      formularioValido: false,
      errores: [],
      reglas: {
        requerido: (v) => !!v || "Nombre de la entidad es requerido",
        min: (v) =>
          (v.length >= 2 && v.length <= 100) ||
          "Nombre de la entidad debe ser mayor a 2 caracteres",
        expresion: (v) =>
          /^[A-Za-z0-9- \s]+$/g.test(v) ||
          "Nombre de la entidad no puede tener caracteres especiales",
      },
    };
  },
  computed: {
    tituloBtnGuardar() {
      return this.entidad.id == null ? "Guardar" : "Actualizar";
    },
    tituloFormulario() {
      return this.entidad.id == null
        ? "Agregar nueva entidad"
        : "Actualizar entidad";
    },
  },
  mounted() {
    this.get();
  },
  methods: {
    get() {
      axios
        .get("/Api/entidades")
        .then(({ data: { entidades } }) => {
          this.entidades = entidades;
        })
        .catch(console.error);
    },
    save() {
      let path = this.entidad.id
        ? `/Api/entidades/${this.entidad.id}/edit`
        : "/Api/entidades";
      axios
        .post(path, this.entidad)
        .then((response) => {
          if (response.status == 200) {
            let { respuesta, mensaje } = response.data;

            if (respuesta) {
              this.get();

              Swal.fire({
                position: "top-end",
                icon: "success",
                title: "Buen trabajo",
                text: mensaje,
                showConfirmButton: false,
                timer: 1500,
              });

              this.cerrarModal();
            }
          }
        })
        .catch(() => {
          console.error("Error en la promesa de guardar");
        });
    },
    deleteEntidad(entidad) {
      Swal.fire({
        title: "INFORMACION",
        text: `¡Estas seguro de eliminar la entidad ${entidad.entidad}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3698e3",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
          axios
        .delete(`/Api/entidades/${entidad.id}`)
        .then((response) => {
          console.log(response);
        })
        .catch(console.error);
        }
      });
    },
    mostrarModal(entidad) {
      // console.log(entidad);
      this.entidad = { ...entidad };
      this.modalEntidad = true;
    },
    cerrarModal() {
      this.entidad = { id: null, entidad: "" };
      this.modalEntidad = false;
    },
  },
};
</script>

<style>
</style>
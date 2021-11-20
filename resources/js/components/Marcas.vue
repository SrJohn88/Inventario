<template>
  <div class="content">
    <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
      
      <v-overlay :value="loader" :z-index="'99999999'">
        <v-progress-circular indeterminate size="80" color="grey darken-4"></v-progress-circular>
      </v-overlay>
      <v-card>
        <v-card-title>
          LISTADO DE MARCAS DE INVENTARIO
          <div class="flex-grow-1"></div>
          <v-text-field v-model="searchMarcas" label="Buscar Marcas" hide-details></v-text-field>
        </v-card-title>
        <v-data-table
          :headers="hTBMarcas"
          :items=" estadoMarca ? marcarEliminadas : marcas"
          :footer-props="{
            'items-per-page-options': [5,10, 20, 30,40],
            'items-per-page-text' : 'Registros Por Página'
          }"
          :items-per-page="5"
          :search="searchMarcas"
          multi-sort
          class="elevation-1"
          no-data-text="No hay marcas para mostrar"
        >
          <!-- Template Para Modal de Actualizar y Agregar Categoria -->

          <template v-slot:top >
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="modalMarca" persistent max-width="700px">
                <template v-slot:activator="{ on }">
                  <v-btn v-show="!estadoMarca" elevation="10" color="blue  darken-3" dark class="mb-2" v-on="on">
                    Agregar Marca&nbsp;
                    <v-icon>mdi-plus-box-multiple-outline</v-icon>
                  </v-btn>
                  
                  <!-- MOSTRAR MARCAS -->
                    <v-checkbox 
                        v-model="estadoMarca"
                        class="mx-10"
                        style="margin-top: 1.5rem;"
                        label="Marcas desactivadas"
                        value="false"
                    />
                </template>
                <v-card>
                  <v-card-title class="headline grey lighten-2" primary-titles>
                    <span class="headline" v-text="formTitle"></span>
                  </v-card-title>
                  <v-card-text>
                    <v-container>
                      <v-form ref="formMarca" v-model="validForm" :lazy-validation="true">
                        <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="marca.marca"
                          @keyup="errorsNombre = []"
                          :rules="[reglas.min, reglas.requerido, reglas.expresion]"
                          label="Nombre"
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
         
          <!-- Template que va en la tabla en la columna de Acciones (Editar,Desactivar) -->

          <template v-slot:item.action="{item}" v-slot:activator="{ on }">
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                  v-show="!estadoMarca"
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
              <span>Actualizar Marca</span>
            </v-tooltip>
            <v-tooltip top >
              <template v-slot:activator="{ on }" >
                <v-btn
                  v-show="!estadoMarca"
                  color="info"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="deleteMarca( item )"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Desactivar Marca</span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                  v-show="estadoMarca"
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
              <span>Activar marca</span>
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
      estadoMarca: false,
      marcas: [],
      hTBMarcas: [
        { text: "Nombre", value: "marca", align: "center" },
        { text: "Acciones", value: "action", sortable: false, align: "center" },
      ],
      reglas: {
        requerido: (v) => !!v || "Nombre de la marca es requerido",
        min: (v) => (v && v.length >= 2 && v.length <= 100) ||
                "Nombre de la marca debe ser mayor a 2 caracteres",
        expresion: (v) =>
                /^[A-Za-z0-9-ñáéíóúÁÉÍÓÚ \s]+$/g.test(v) ||
                "Nombre de la marca no puede tener caracteres especiales",
      },
      loader: false,
      searchMarcas: "",
      marcarEliminadas: false,
      modalMarca: false,
      marca: { id: null, nombre: "" },
      validForm: true,
      errorsNombre: []
    };
  },
  watch: {},
  computed: {
    formTitle() {
      return this.marca.id === null ? "Agregar Marca" : "Actualizar Marca";
    },
    btnTitle() {
      return this.marca.id === null ? "Guardar" : "Actualizar";
    },
  },
  methods: {
    obtenerMarcar () 
    {
      this.loader = true
      axios.get(`/Api/marcas`)
        .then( ({ data: {marcas } } ) => {
          this.loader = false
          this.marcas = marcas.filter((r) => r.eliminado == false );
          this.marcarEliminadas = marcas.filter((r) => r.eliminado == true);
        })
        .catch( (e) => {
          this.loader = false;
        });
    },
    save()
    {
      this.loader = true

      const path = this.marca.id == null ? '/Api/marcas' : `/Api/marcas/${ this.marca.id }/edit`

      axios.post(path, this.marca )
          .then( response => {
            this.loader = false

            if (response.status == 200) 
            {
              const { respuesta, mensaje } = response.data;

              if ( respuesta ) 
              {                  
                const { marca } = response.data
                this.obtenerMarcar()
                this.alerta( mensaje, 'success', 'Buena hecho');

                this.cerrarModal();

              } else {
                const { marca } = response.data;
                this.errorsNombre = marca
          }
            }
          })
    },
    deleteMarca( {... marca } ) {
        Swal.fire({
        title: "INFORMACION",
        text: `¡Estas seguro de desactivar la marca ${marca.marca} ?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3698e3",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
          this.cambiarEstadoMarca( marca )
        }
      });
    },
    restaurar ( {...marca} )
    {
      Swal.fire({
        title: "INFORMACION",
        text: `¿Estas que quieres activar la marca ${marca.marca} ?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3698e3",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
          this.cambiarEstadoMarca( marca, false )
        }
      });

    },
    cambiarEstadoMarca( marca, eliminar = true) 
    {
      axios
        .delete(`/Api/marcas/${marca.id}/${eliminar}`)
        .then((response) => {

          if (response.status == 200) {
          const { respuesta, mensaje } = response.data;

          if (respuesta) {
            this.obtenerMarcar()
            this.alerta( mensaje, 'success', '¡Bien hecho!');
          } else {
            this.alerta( mensaje, 'error', '¡Importante!');
          }
        }
        })
        .catch(console.error);
    },
    cerrarModal() {
      this.modalMarca = false;
      setTimeout(() => {
        this.marca = {
          id: null,
          nombre: ""
        };
        this.resetValidation();
      }, 300);
    },
    resetValidation() {
      this.errorsNombre = [];
      this.$refs.formMarca.resetValidation();
    },
    mostrarModal( {...marca })
    {
      this.marca = marca
      this.modalMarca = true
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
  mounted() {
    this.obtenerMarcar()
  }
};
</script>
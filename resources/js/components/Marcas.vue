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
          :items="arrayMarcas"
          :footer-props="{
            'items-per-page-options': [5,10, 20, 30,40],
            'items-per-page-text' : 'Registros Por Página'
          }"
          :items-per-page="5"
          :search="searchMarcas"
          multi-sort
          class="elevation-1"
        >
          <!-- Template Para Modal de Actualizar y Agregar Categoria -->

          <template v-slot:top >
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="modalMarca" persistent max-width="700px">
                <template v-slot:activator="{ on }">
                  <v-btn elevation="10" color="blue  darken-3" dark class="mb-2" v-on="on">
                    Agregar Marca&nbsp;
                    <v-icon>mdi-plus-box-multiple-outline</v-icon>
                  </v-btn>
                  
                  <!-- MOSTRAR MARCAS -->
                    <v-checkbox 
                        v-model="marcasRemovidas"
                        @change="fetchMarcasRemovidas()"
                        class="mx-10"
                        style="margin-top: 1.5rem;"
                        label="Mostrar Las Marcas Removidas"
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
                          v-model="marca.nombre"
                          @keyup="errorsNombre = []"
                          :rules="[v => !!v || 'Nombre Es Requerido']"
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
                      @click="saveMarca()"
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
                  color="success"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="showModalEditar(item)"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>
              <span>Actualizar Marca</span>
            </v-tooltip>
            <v-tooltip top >
              <template v-slot:activator="{ on }" >
                <v-btn
                  color="info"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="cambiarEstado(item,'R')"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Remover Marca</span>
            </v-tooltip>
          </template>
        </v-data-table>
        <v-snackbar v-model="snackbar">
          {{ msjSnackBar }}
          <v-btn color="red" text @click="snackbar = false">Cerrar</v-btn>
        </v-snackbar>
      </v-card>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      arrayMarcas: [],
      hTBMarcas: [
        { text: "Nombre", value: "nombre" },
        /* { text: "Fecha de Removido", value: "fecha_removida" }, */
        { text: "Acciones", value: "action", sortable: false, align: "center" },
      ],
      
      loader: false,
      searchMarcas: "",
      marcasRemovidas: false,
      modalMarca: false,
      marca: {
        id: null,
        nombre: ""
      },
      validForm: true,
      snackbar: false,
      msjSnackBar: "",
      errorsNombre: [],
      editedMarca:-1,
    };
  },
  watch: {},
  computed: {
    formTitle() {
      return this.marca.id === null
        ? "Agregar Marca"
        : "Actualizar Marca";
    },
    btnTitle() {
      return this.marca.id === null ? "Guardar" : "Actualizar";
    },
  },
  methods: {
    fetchMarcas() {
      let me = this;
      me.loader = true;
      
      axios.get(`/marcas/list?type=A`)
        .then(function(response) {
          me.arrayMarcas = response.data;
          me.loader = false;
        })
        .catch(function(error) {
          me.loader = false;
          console.log(error);
        });
    },
    
    fetchMarcasRemovidas() {
            let me = this;
            me.loader = true;
            if(me.marcasRemovidas){
              axios.get(`/marcas/list?type=R`).then(function(response) {
                   // console.log(response.data);
                    me.arrayMarcas = response.data;
                    me.loader = false;
                })
                .catch(function(error) {
                    me.loader = false;
                    console.log(error);
                });
             }else{     
                me.fetchMarcas();
            }

    },
      
    cambiarEstado(marca,accion) {
      let me = this;

      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: true,
        timer: 3000
        });
        var question,request,state='';
        if(accion=='R'){
            question = "¿Está seguro de remover marca?";
            request = "La marca se ha removido"
            state="R"; 
          }
        Swal.fire({
          title: question,
          text: "Una ves realizada la acción no podra revertir !",
          type: 'question',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, remover marca!',
          cancelButtonText: 'Cancelar'
        }).then(result => {
          if (result.value) {
              me.loader = true;
              axios.put(`/marcas/change`,{id:marca.id,estado:state}).then(function(response) {
                console.log(response.data);
                me.loader = false;
                if (response.status == 200) {
                    Swal.fire(
                      "Resultado",
                      request,
                      "success"
                    );
                    var index = me.arrayMarcas.indexOf(marca);
                                me.arrayMarcas.splice(index,1);
                }
              })
              .catch(function(error){
                me.loader = false;
                Toast.fire({
                  type:"error",
                  tittle: "Ocurrio un errror intente nuevamente"
                });
              });
          }
        });
    },
     verificarAccionDato(marca, statusCode, accion) {
      let me = this;
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
        });
      switch (accion) {
        case "add":
          //Agrego al array de categorias el objecto que devuelve el Backend
          //me.arrayCategorias.unshift(categoria);
          this.fetchMarcas(); 
          Toast.fire({
            icon: 'success',
            title: 'Marca Registrada con Exito'
           });
          me.loader = false;
          break;
        case "upd":
          //Actualizo al array de categorias el objecto que devuelve el Backend ya con los datos actualizados
          //Object.assign(me.arrayCategorias[me.editedCategoria], categoria);
          this.fetchMarcas(); 
           Toast.fire({
            icon: 'success',
            title: 'Marcas Actualizada con Exito'
           });
          me.loader = false;
          break;
        case "cha":
          if (statusCode == 200) {
            try {
              //Se remueve del array de Categorias Activos si todo esta bien en el backend
              me.arrayMarcas.splice(me.editedMarca, 1);
              //Se Lanza mensaje Final
              Toast.fire({
                icon: 'success',
                title: 'Marca Removida...!!!'
              });
            } catch (error) {
              console.log(error);
            }
          } else {
             Toast.fire({
                icon: 'error',
                title: 'Ocurrió un error, intente de nuevo'
              });
          }
          break;
      }
      
    },

    setMessageToSnackBar(msj, estado) {
            let me = this;
            me.snackbar = estado;
            me.msjSnackBar = msj;
        
    },

    cerrarModal() {
      let me = this;
      me.modalMarca = false;
      setTimeout(() => {
        me.marca = {
          id: null,
          nombre: ""
        };
        me.resetValidation();
      }, 300);
    },
    resetValidation() {
      let me = this;
      me.errorsNombre = [];
      me.$refs.formMarca.resetValidation();
    },
    showModalEditar(marca) {
      let me = this;
      me.editedMarca = me.arrayMarcas.indexOf(marca);
      me.marca = Object.assign({}, marca);
      me.modalMarca = true;
    },
    saveMarca() {
      let me = this;
      if (me.$refs.formMarca.validate()) {
        let accion = me.marca.id == null ? "add" : "upd";
        me.loader = true;
        if(accion=="add"){
           axios.post('/marcas/save', me.marca)
            .then(function(response) {
            me.verificarAccionDato(response.data, response.status, accion);
            me.cerrarModal();
          })
         .catch(function(error) {
            console.log(error);
            //409 Conflicts Error (Proveedor Ya Existente En la BD)
            if (error.response.status == 409) {
              me.setMessageToSnackBar("Marca Ya Existe", true);
              me.errorsNombre = ["Nombre De Marca Existente"];
            } else {
                  Swal.fire({
                  title: '¡PRECAUCIÓN!',
                  text: 'Ingrese una diferente',
                  icon: 'error',
                  confirmButtonText: 'Aceptar'
                })            }
            me.loader = false;
          });

        }else{
            //para actualizar
            axios.put('/marcas/update', me.marca)
               .then(function(response) {
                   //console.log(response.data);
                    me.verificarAccionDato(response.data, response.status, accion);
                    me.cerrarModal();
            })
          .catch(function(error) {
            console.log(error);
            //409 Conflicts Error (Proveedor Ya Existente En la BD)
            if (error.response.status == 409) {
              me.setMessageToSnackBar("Marca ya existe", true);
              me.errorsNombre = ["Nombre de marca existente"];
            } else {
                  Swal.fire({
                  title: '¡PRECAUCIÓN!',
                  text: 'Por favor ingrese una diferente',
                  icon: 'error',
                  confirmButtonText: 'Aceptar'
                })            }
            me.loader = false;
          });
        }
      
      }
    },


  },
  mounted() {
    let me = this;
    me.fetchMarcas();
  }
};
</script>
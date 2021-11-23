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
          Usuarios del sistema
          <div class="flex-grow-1"></div>

          
          <v-text-field
            v-model="buscarUsuario"
            label="Buscar Usuario"
            hide-details
          ></v-text-field>

            <template>
              <div class="text-center ma-4">              
                <v-btn                          
                  color="primary"
                  dark
                  elevation="2"
                  small
                  @click="crear()"                        
                >
                  Agregar Usuario
                </v-btn>
              </div>
            </template>
        </v-card-title>

        <v-data-table
          :headers="TheadTable"
          :items="mostrarUsuariosEliminados ? usuariosEliminados : usuarios "
          :footer-props="{
            'items-per-page-options': [5, 10, 20, 30, 40],
            'items-per-page-text': 'Registros Por Página',
          }"
          :items-per-page="5"
          :search="buscarUsuario"
          multi-sort
          class="elevation-1"
          no-data-text="No hay usuarios para mostrar"
        >
          

          <template v-slot:top >
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="modalContraseña" persistent max-width="700px">
                
                <template v-slot:activator="{ on }">                            
                    <v-checkbox 
                        v-model="mostrarUsuariosEliminados"
                        class="mx-10"
                        style="margin-top: 1.5rem;"
                        label="Usuarios desactivados"
                        value="false"
                    />
                </template>
                
                <v-card>
                  <v-card-title class="headline lighten-2" primary-titles>
                    <span class="headline" v-text="'Cambiar contraseña'"></span>
                  </v-card-title>
                
                  <v-card-text>
                    <v-container>
                      <v-form ref="formPassword" :lazy-validation="true">
                        <v-row>
                          <v-col cols="12">
                            <v-text-field
                                v-model="cambioContraseña.actual"
                                :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"                                
                                :type="show1 ? 'text' : 'password'"
                                name="input-10-1"
                                label="Contraseña Actual"                                
                                counter
                                @click:append="show1 = !show1"
                                @keyup="errorsContraseña.actual = []"
                                :error-messages="errorsContraseña.actual"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12">
                            <v-text-field
                                v-model="cambioContraseña.password"
                                :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"                                
                                :type="show1 ? 'text' : 'password'"
                                name="input-10-1"
                                label="Contraseña Actual"                                
                                counter
                                @click:append="show1 = !show1"
                                @keyup="errorsContraseña.password = []"
                                :error-messages="errorsContraseña.password"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12">
                            <v-text-field
                                v-model="cambioContraseña.password2"
                                :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"                                
                                :type="show1 ? 'text' : 'password'"
                                name="input-10-1"
                                label="Contraseña Actual"                                
                                counter
                                @click:append="show1 = !show1"
                                @keyup="errorsContraseña.password2 = []"
                              :error-messages="errorsContraseña.password2"
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      </v-form>
                    </v-container>
                  </v-card-text>
                  <v-divider></v-divider>

                  <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn
                      color="red darken-1"
                      text
                      v-text="'Cerrar'"
                      @click="cerrarModalContraseña()"
                    ></v-btn>
                    <v-btn
                      color="info darken-1"
                      text
                      v-text="'Guardar'"
                      @click="cambiarPassword()"
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
                  v-show="!mostrarUsuariosEliminados  && usuarioSesion.rol.rol == 'Administrador'"
                  color="success"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="editar(item)"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>
              <span>Actualizar Usuario</span>
            </v-tooltip>

            <v-tooltip v-if="item.id == usuarioSesion.id">
              <template v-slot:activator="{ on }">
                <v-btn
                  v-show="!mostrarUsuariosEliminados"
                  color="warning"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="mostrarModalContraseña(item)"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
              </template>
              <span>Cambiar clave</span>
            </v-tooltip>

            <v-tooltip top v-if="item.id != usuarioSesion.id">
              <template v-slot:activator="{ on }">
                <v-btn
                  v-show="!mostrarUsuariosEliminados"
                  color="amber"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="resetear(item)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Resetear Usuario</span>
            </v-tooltip>

            <v-tooltip top v-if="item.id != usuarioSesion.id">
              <template v-slot:activator="{ on }">
                <v-btn
                  v-show="!mostrarUsuariosEliminados"
                  color="info"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="eliminar(item)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Desactivar Usuario</span>
            </v-tooltip>


            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                  v-show="mostrarUsuariosEliminados"
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
              <span>Activar Usuario</span>
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
      show1: false,
      formularioValido: true,
      modal: false,
      loader: false,
      buscarUsuario: "",
      TheadTable: [
        { text: "Nombre", value: "name", align: "center" },
        { text: "Apellido", value: "lastName", align: "center" },
        { text: "Email", value: "email", align: "center" },
        { text: "Cargo", value: "rol.rol", align: "center" },
        { text: "Acciones", value: "action", sortable: false, align: "center" },
      ],      
      usuarios: [],
      usuariosEliminados: [],
      mostrarUsuariosEliminados: false,
      usuario: {
        id: null,
        nombre: "",
        apellido: "",
        email: "",
        password: "",
        password2: "",
        rol: { id: null, rol: "" },
      },
      
      roles: [],
      modalContraseña: false,
      errorsContraseña : [],
      cambioContraseña: { id: null, actual: "", password: "", password2: "" },
      esAdministrador: false,
      usuarioSesion: { id: null, nombre: "", rol: { id: null, rol: "" } },
    };
  },
  computed: {
  },
  mounted() {
    this.obtenerSession()
      .then( () => {
        this.obtenerUsuarios()
      });
  },
  methods: {
    obtenerSession() {
      return new Promise( (resolve, reject) => {
        this.loader = true;
          axios
        .get("/Api/usuarios/sesion")
        .then(({ data: { usuario } }) => {
          this.loader = false;

          this.usuarioSesion = {... usuario }
          resolve()
        })
        .catch( reject );
      })      
    },
    obtenerUsuarios() {
      this.loader = true;
      axios
        .get("/Api/usuarios")
        .then(({ data: { usuarios } }) => {
          
          console.log( this.usuarioSesion.rol.rol )

          if ( this.usuarioSesion.rol.rol == 'Administrador')
          {
            this.usuarios = usuarios.filter(
              (usuario) => usuario.eliminado == false
            );
            this.usuariosEliminados = usuarios.filter(
              (usuario) => usuario.eliminado == true
            );
          } else {
            this.usuarios = usuarios.filter(
              (usuario) => usuario.eliminado == false && usuario.id == this.usuarioSesion.id
            );
            this.usuariosEliminados = usuarios.filter(
              (usuario) => usuario.eliminado == true && usuario.id == this.usuarioSesion.id
            );
          }

          this.loader = false;
        })
        .catch(console.error);
    },
    cambiarPassword()
    {
      this.loader = true;
      const path = `/Api/usuarios/${this.cambioContraseña.id}/password`  
      
      axios
        .post(path, this.cambioContraseña)
        .then( response => {
          this.loader = false

          if ( response.status == 200 )
          {
            const { respuesta, mensaje } = response.data

            if ( respuesta )
            {
              this.alerta(mensaje, 'success', '¡Bien hecho!')
              this.modalContraseña = false

            } else 
            {              
              const { errors } = response.data
              
              if ( Object.keys(errors).length > 0 )
              {
                this.errorsContraseña = { ...errors }
              } else 
              {
                this.alerta( mensaje, 'warning', '¡IMPORTANTE!')
              }
              
            }
          } else {
            console.log('La respuesta no fue ok')
          }
        }).catch( () => {
            this.alerta('Ocurrio un error inesperado', 'error', '¡IMPORTANTE!')
        })
    },
    crear()
    {
      window.location = `/usuarios/crear`
    },
    editar( {... usuario } )
    {
      window.location = `/usuarios/crear?id=${usuario.id}`
    },
    resetear( {...usuario})
    {
      Swal.fire({
              title: "INFORMACION",
              text: `¡Estas seguro de restablecer la clave del usuario ${usuario.name} ${usuario.lastName} ?`,
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3698e3",
              cancelButtonColor: "#d33",
              confirmButtonText: "Si",
              cancelButtonText: 'No'
            }).then((result) => {
              if (result.isConfirmed) {
                axios
                  .get(`/Api/usuarios/${usuario.id}/resetear`)
                  .then( response => {
                    if (response.status == 200) {
                      const { respuesta, mensaje } = response.data;

                      if (respuesta) {

                        this.obtenerUsuarios()
                        Swal.fire({
                          icon: 'success',
                          title: '¡BIEN HECHO!',
                          text: mensaje,
                        })

                      } else {

                        this.alerta( mensaje, 'error', '¡Importante!')
                      }
                    }
                  }).catch( () => {
                    this.alerta('Ocurrio un error en el servidor', 'error', '!IMPORTANTE¡')
                  })
              }
            });
    },
    eliminar( {...usuario} ) {
      Swal.fire({
              title: "INFORMACION",
              text: `¡Estas seguro de desactivar el usuario ${usuario.name} ${usuario.lastName} ?`,
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3698e3",
              cancelButtonColor: "#d33",
              confirmButtonText: "Si",
              cancelButtonText: 'No'
            }).then((result) => {
              if (result.isConfirmed) {
                this.cambiarEstado( usuario )
              }
            });
    },
    restaurar( {... usuario}) {
      Swal.fire({
              title: "INFORMACION",
              text: `¡Estas seguro quieres activar el usuario ${usuario.name} ${usuario.lastName} ?`,
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3698e3",
              cancelButtonColor: "#d33",
              confirmButtonText: "Si",
              cancelButtonText: 'No'
            }).then((result) => {
              if (result.isConfirmed) {
                this.cambiarEstado( usuario, false)
              }
            });
    },
    cambiarEstado( usuario, eliminar = true ) 
    {
      axios
        .delete(`/Api/usuarios/${usuario.id}/${eliminar}`)
        .then( response => {
          if (response.status == 200) {
              
              const { respuesta, mensaje } = response.data;

              if (respuesta) {

                this.obtenerUsuarios()

                this.alerta( mensaje, 'success', '¡Bien hecho!')

              } else {

                this.alerta( mensaje, 'error', '¡Importante!')
              }
            }
        }).catch( console.error )
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
    mostrarModalContraseña( { ... usuario } ) {

      this.cambioContraseña.id = usuario.id

      this.modalContraseña = true
    },
    cerrarModalContraseña() {
      this.modalContraseña = false
      setTimeout(() => {
            this.cambioContraseña = { id: null, actual: "", password: "", password2: "" }
            this.resetValidation();
      }, 300)
    },
    resetValidation() {
      this.errorsContraseña = [];
      this.$refs.formPassword.resetValidation();
    },
  },
};
</script>

<style>
</style>
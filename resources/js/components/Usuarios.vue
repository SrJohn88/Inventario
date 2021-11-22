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
        </v-card-title>

        <v-data-table
          :headers="TheadTable"
          :items="usuarios"
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

          <template v-slot:top>
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="modal" persistent max-width="700px">
                <template v-slot:activator="{ on }">
                  <v-btn
                    v-show="!mostrarUsuariosEliminados"
                    elevation="10"
                    color="blue  darken-3"
                    dark
                    class="mb-2"
                    v-on="on"
                  >
                    Crear usuario&nbsp;
                    <v-icon>mdi-plus-box-multiple-outline</v-icon>
                  </v-btn>

                  <v-checkbox
                    v-model="mostrarUsuariosEliminados"
                    class="mx-10"
                    style="margin-top: 1.5rem"
                    label="Usuarios desactivados"
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
                        ref="form"
                        v-model="formularioValido"
                        lazy-validation
                      >
                        <v-row>
                          <v-col cols="6">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="usuario.nombre"
                              @keyup="errores = []"
                              :rules="[
                                reglas.requerido,
                                reglas.min,
                                reglas.expresion,
                              ]"
                              label="Nombres"
                              required
                              :error-messages="errores"
                            ></v-text-field>
                          </v-col>

                          <v-col cols="6">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="usuario.apellido"
                              @keyup="errores = []"
                              :rules="[
                                reglas.requerido,
                                reglas.min,
                                reglas.expresion,
                              ]"
                              label="Apellidos"
                              required
                              :error-messages="errores"
                            ></v-text-field>
                          </v-col>                         

                          <v-col cols="6">
                            <v-text-field                              
                              append-icon="mdi-folder-outline"
                              v-model="usuario.email"
                              @keyup="errores = []"
                              :rules="[
                                reglas.email,
                              ]"
                              label="Email"
                              required
                              :error-messages="errores"
                            ></v-text-field>
                          </v-col>

                          <v-col cols="6">
                            <v-select
                              :error-messages="errores['rol.id']"
                              :items="roles"
                              v-model="usuario.rol"
                              item-text="rol"
                              item-value="id"
                              label="Seleccione rol"
                              return-object
                              @change="errores['rol.id'] = []"                    
                              :menu-props="{ closeOnClick: true }"
                              :rules="[(v) => !!v || 'Tipo rol es campo obligatorio']"                              
                            ></v-select>
                          </v-col>

                          <v-col cols="6" v-if="usuario.id == null">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="usuario.password"
                              @keyup="errores = []"
                              :rules="[
                                reglas.requerido,
                                reglas.min,
                                reglas.expresion,
                              ]"
                              label="Contraseña"
                              required
                              :error-messages="errores"
                            ></v-text-field>
                          </v-col>

                          <v-col cols="6" v-if="usuario.id == null">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="usuario.password"
                              @keyup="errores = []"
                              :rules="[
                                reglas.requerido,
                                reglas.min,
                                reglas.expresion,
                              ]"
                              label="Contraseña"
                              required
                              :error-messages="errores"
                            ></v-text-field>
                          </v-col>

                        </v-row>
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

          <template v-slot:top>
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="modalContraseña" persistent max-width="700px">
                <v-card>
                  <v-card-title class="headline lighten-2" primary-titles>
                    <span class="headline" v-text="'Cambiar contraseña'"></span>
                  </v-card-title>

                  <v-card-text>
                    <v-container>
                      <v-form :lazy-validation="true">
                        <v-row>
                          <v-col cols="12">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="cambioContraseña.actual"
                              label="Contraseña Actual"
                              required
                              :readonly="true"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="cambioContraseña.nueva"
                              label="Nueva contraseña"
                              required
                              :readonly="true"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="cambioContraseña.nueva2"
                              label="Repite la nueva contraseña"
                              required
                              :readonly="true"
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
                      color="info darken-1"
                      text
                      v-text="'Cerrar'"
                      @click="cerrarModalContraseña()"
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
                  v-show="!estado"
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
                <span>Actualizar Usuario</span>
            </v-tooltip>

            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                  v-show="!estado"
                  color="warning"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="mostrarModalContraseña()"
                  >
                    <v-icon>mdi-check</v-icon>
                </v-btn>
              </template>
                <span>Resetear Usuario</span>
            </v-tooltip>

            <v-tooltip top >
              <template v-slot:activator="{ on }" >
                <v-btn
                  v-show="!estado"
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
              <span>Desactivar Usuario</span>
            </v-tooltip>

            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                  v-show="estado"
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
      estado: false,
      formularioValido: true,
      modal: false,
      loader: false,
      buscarUsuario: "",
      TheadTable: [
        { text: "Nombre", value: "name", align: "center" },
        { text: "Email", value: "email", align: "center" },
        { text: "Cargo", value: "rol.rol", align: "center" },
        { text: "Acciones", value: "action", sortable: false, align: "center" },
      ],
      usuarios: [],
      usuariosEliminados: [],
      mostrarUsuariosEliminados: false,
      usuario: { 
        id: null, nombre: '', apellido: '', email: '', password: '',
        password2: '', 
        rol: { id: null, rol: '' }
      },
      errores: [],
      reglas: {
        email: (v) => !!v || 'Datos incorrecto',
        requerido: (v) => !!v || "Nombre de la entidad es requerido",
        min: (v) =>
          (v.length >= 2 && v.length <= 100) ||
          "Nombre de la entidad debe ser mayor a 2 caracteres",
        expresion: (v) =>
          /^[A-Za-z0-9-ñáéíóúÁÉÍÓÚ \s]+$/g.test(v) ||
          "Nombre de la entidad no puede tener caracteres especiales",
      },
      roles: [],
      modalContraseña: false,
      cambioContraseña: { actual: '', nueva: '', nueva2: '' }
    };
  },
  computed: {
    tituloFormulario() {
      return this.usuario.id == null
        ? "Crear nuevo usuarios"
        : "Actualizar usuarios";
    },
    tituloBtnGuardar() {
      return this.usuario.id == null ? "Guardar" : "Actualizar";
    },
  },
  mounted() {
    this.obtenerRoles()
    this.obtenerUsuarios()
  },
  methods: {
    obtenerRoles()
    {
      this.loader = true;
      axios
        .get("/Api/usuarios/roles")
        .then( ( { data: { roles } } )  => {
          this.loader = false
          this.roles = [ ... roles ]

        }).catch(console.error)
    },
    obtenerUsuarios() {
      this.loader = true;
      axios
        .get("/Api/usuarios")
        .then(({ data: { usuarios } }) => {
          this.usuarios = usuarios.filter(
            (usuario) => usuario.eliminado == false
          );
          this.usuariosEliminados = usuarios.filter(
            (usuario) => usuario.eliminado == true
          );

          this.loader = false;
        })
        .catch(console.error);
    },
    save()
    {
        this.loader = true

        const path = this.usuario.id != null 
                      ? `/Api/usuarios/${this.usuario.id}/edit`
                      : '/Api/usuarios'        
        axios
          .post(path, this.usuario )
          .then( response => {

            this.loader = false

            if ( response.status == 200 )
            {
              const { respuesta, mensaje } = response.data

              if (respuesta) 
              {
                this.obtenerUsuarios()
                this.alerta( mensaje, 'success', '¡Bien hecho!');
                this.cerrarModal();
              } else
              {
                const { errors } = response.data
                this.errors = errors
              }
            } else
            {
              this.alerta('Ocurrio un error en el servidor', 'error', '¡IMPORTANTE!')
            }

          }).catch( () => {
              this.loader = false
              this.alerta('Ocurrio un error en el servidor', 'error', '¡IMPORTANTE!')
          })
    },
    delete()
    {
      
    },
    restaurar()
    {

    },
    cambiarEstado() 
    {

    },
    mostrarModal( {...usuario }) {
      this.usuario.id = usuario.id
      this.usuario.nombre = usuario.name
      this.usuario.apellido = usuario.lastName
      this.usuario.email = usuario.email
      this.usuario.rol = { ... usuario.rol}
      this.modal = true
    },
    cerrarModal() {
      this.modal = false
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
    },
    mostrarModalContraseña()
    {
      this.modalContraseña = true
    },
    cerrarModalContraseña()
    {
      this.modalContraseña = false
    }
  },
};
</script>

<style>
</style>
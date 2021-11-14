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
                        style="margin-top: 1.5rem;"
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
                        <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="usuario.nombre"
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
            formularioValido: true,
            modal: false,
            loader: false,
            buscarUsuario: '',
            TheadTable: [
                { text: "Nombre", value: "name", align: 'center', },
                { text: "Email", value: "email", align: 'center', },                
                { text: "Cargo", value: "rol.rol", align: 'center', },
                { text: "Acciones", value: "action", sortable: false, align: "center" },
            ],
            usuarios: [], usuariosEliminados: [],
            mostrarUsuariosEliminados: false,
            usuario : { id: null, nombre: ''},
            errores : [],
            reglas: {
                requerido: (v) => !!v || "Nombre de la entidad es requerido",
                min: (v) =>
                (v.length >= 2 && v.length <= 100) ||
                "Nombre de la entidad debe ser mayor a 2 caracteres",
                expresion: (v) =>
                /^[A-Za-z0-9-ñáéíóúÁÉÍÓÚ \s]+$/g.test(v) ||
                "Nombre de la entidad no puede tener caracteres especiales",
            },
        }
    },
    computed:{
        tituloFormulario ()
        {
            return this.usuario.id != null ? 'Crear nuevo usuarios' : 'Actualizar usuarios'
        },
        tituloBtnGuardar ()
        {
            return this.usuario.id != null ? 'Guardar' : 'Actualizar'
        }
    },
    mounted ()
    {
        this.obtenerUsuarios();
    },
    methods: {
        obtenerUsuarios()
        {
            this.loader = true;
            axios
                .get("/Api/usuarios")
                .then(({ data: { usuarios } }) => {                                        
                    this.usuarios = usuarios.filter( usuario => usuario.eliminado == false);
                    this.usuariosEliminados = usuarios.filter( usuario => usuario.eliminado == true);

                    this.loader = false;
                })
                .catch(console.error);
        },
        mostrarModal()
        {

        },
        cerrarModal()
        {

        }
    }
}
</script>

<style>

</style>
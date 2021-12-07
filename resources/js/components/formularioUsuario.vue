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
          {{ titulo }}
          <div class="flex-grow-1"></div>
        </v-card-title>

        <v-container fluid>
          <v-form
            ref="formUsuario"
            v-model="formularioUsuario"
            :lazy-validation="true"
          >
            <v-row>
              <v-col cols="6">
                <v-text-field
                  append-icon="fas fa-user-tag"
                  v-model="usuario.nombre"
                  @keyup="errores.nombre = []"
                  :rules="[reglas.requerido, reglas.min, reglas.expresion]"
                  label="Nombres"
                  required
                  :error-messages="errores.nombre"
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <v-text-field
                  append-icon="fas fa-user-tag"
                  v-model="usuario.apellido"
                  @keyup="errores.apellido = []"
                  :rules="[reglas.requerido, reglas.min, reglas.expresion]"
                  label="Apellidos"
                  required
                  :error-messages="errores.apellido"
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <v-text-field
                  append-icon="fas fa-envelope"
                  v-model="usuario.email"
                  @keyup="errores.email = []"
                  :rules="[reglas.email]"
                  label="Email"
                  required
                  :error-messages="errores.email"
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <v-select
                  append-icon="fas fa-cogs"
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
                  v-model="usuario.password"
                  :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"                                
                  :type="show1 ? 'text' : 'password'"
                  name="input-10-1"
                  label="Contraseña"                                
                  counter
                  @click:append="show1 = !show1"
                  @keyup="errores.password = []"
                  :error-messages="errores.password"
                ></v-text-field>
              </v-col>

              <v-col cols="6" v-if="usuario.id == null">

                <v-text-field
                  v-model="usuario.password2"
                  :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"                                
                  :type="show1 ? 'text' : 'password'"
                  name="input-10-1"
                  label="Repite la contraseña"                                
                  counter
                  @click:append="show2 = !show2"
                  @keyup="errores.password2 = []"
                  :error-messages="errores.password2"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-container>

        <v-divider></v-divider>
        <v-card-actions>
          <div class="flex-grow-1"></div>
          <v-btn color="red darken-1" text @click="cancelar()">Cancelar</v-btn>
          <v-btn
            color="info darken-1"
            :disabled="!formularioUsuario"
            @click="save()"
            text
            v-text="tituloBtnGuardar"
          ></v-btn>
        </v-card-actions>
      </v-card>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      show1: false, show2: false,
      loader: false,
      formularioUsuario: false,
      usuario: {
        id: null,
        nombre: "",
        apellido: "",
        email: "",
        password: "",
        password2: "",
        rol: { id: null, rol: "" },
      },
      errores: [],
      reglas: {        
        requerido: (v) => !!v || "Campo requerido",
        min: (v) =>
          (v.length >= 2 && v.length <= 100) ||
          "Este campo debe ser mayor a 2 caracteres",
        expresion: (v) =>
          /^[A-Za-z0-9-ñáéíóúÁÉÍÓÚ \s]+$/g.test(v) ||
          "Este campo no puede tener caracteres especiales",
        email: (v) =>
              !!v && /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) ||
              "No parece un email",
      },
      roles: [],
      usuarioSesion : {}
    };
  },
  computed: {
    titulo() {
      return this.usuario.id == null ? "Crear usuario" : "Actualizar usuario";
    },
    tituloBtnGuardar() {
      return this.usuario.id == null ? "Guardar" : "Actualizar";
    },
  },
  created ()
  {
    let uri = window.location.search.substring(1);
    let params = new URLSearchParams(uri);

    this.usuario.id = params.has("id") ? params.get("id") : null;
  },
  created()
  {
    this.obtenerSession();
  },
  mounted() {    
    this.obtenerRoles();

    if ( this.usuario.id != null )
    {
      this.obtenerUsuario()      
    }
  },
  methods: {
    obtenerSession() {
      this.loader = true;
      axios
        .get("/Api/usuarios/sesion")
        .then(({ data: { usuario } }) => {
          this.loader = false;
          
          if ( usuario.rol.rol !== 'Administrador' )
          {
              window.location = '/usuarios'
          }
        })
        .catch(console.error);           
    },
    obtenerRoles() {
      this.loader = true;
      axios
        .get("/Api/usuarios/roles")
        .then(({ data: { roles } }) => {
          this.loader = false;
          this.roles = [...roles];
        })
        .catch(console.error);
    },
    obtenerUsuario()
    {
      this.loader = true;
      axios
        .get(`/Api/usuarios/${this.usuario.id}`)
        .then(({ data: { usuario } }) => {
          this.loader = false;
          const {
            id, name, lastName, email, rol
          } = usuario[0]
          this.usuario.nombre = name
          this.usuario.apellido = lastName
          this.usuario.email = email
          this.usuario.rol = { ...rol }
        })
        .catch(console.error);
    },
    save() {
      this.loader = true;

      const path =
        this.usuario.id != null
          ? `/Api/usuarios/${this.usuario.id}/edit`
          : "/Api/usuarios";
      axios
        .post(path, this.usuario)
        .then((response) => {
          this.loader = false;

          if (response.status == 200) {
            const { respuesta, mensaje } = response.data;

            if (respuesta) {
              this.alerta(mensaje, "success", "¡Bien hecho!")
                  .then( () => {
                    window.location = '/usuarios'
                  })
            } else {
              const { errors } = response.data;

              if ( Object.keys(errors).length > 0 )
              {
                this.errores = errors;
              } else {
                this.alerta(mensaje, 'warning', '¡IMPORTANTE!')
              }
              
            }
          } else {
            this.alerta(
              "Ocurrio un error en el servidor",
              "error",
              "¡IMPORTANTE!"
            );
          }
        })
        .catch(() => {
          this.loader = false;
          this.alerta(
            "Ocurrio un error en el servidor",
            "error",
            "¡IMPORTANTE!"
          );
        });
    },
    cancelar () {
      window.location = '/usuarios'
    },
    alerta(mensaje, icono = "info", titulo = "") {
      return new Promise( (resolve, reject ) =>{
        Swal.fire({
          position: "top-end",
          icon: icono,
          title: titulo,
          text: mensaje,
          showConfirmButton: false,
          timer: 1500,
        }).then( resolve );
      })
    },
  },
};
</script>

<style>
</style>

    
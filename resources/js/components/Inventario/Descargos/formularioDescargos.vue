<template>
  <div class="content">
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100"
    >
      <v-card>
        <v-card-title v-text="title">
          <div class="flex-row-1"></div>
        </v-card-title>

          <v-container fluid>
            <v-form
              ref="formMovimiento"
              v-model="formDescargos"
              :lazy-validation="true"
            >

              <v-row>

                  <v-col cols="5">
                    <v-select
                      ref="cboTipoDescargo"
                      :error-messages="errors['tipoDescargo.id']"
                      :items="tiposDescargos"
                      v-model="descargo.tipoDescargo"
                      item-text="tipoDescargo"
                      item-value="id"
                      label="Tipo de descargo"
                      return-object
                      @change="errors['tipoDescargo.id'] = []"                    
                      :menu-props="{ closeOnClick: true }"
                      :rules="[(v) => !!v || 'Tipo de descargo es campo obligatorio']"
                      :disabled = "descargo.id != null"
                    ></v-select>
                  </v-col>

                  <v-col cols="1" md="1">
                    <tipo-descargo @saved="onSavedTipoDescargo" ref="tipoDescargo" />
                    <v-btn
                      elevation="5"
                      class="mt-8"
                      text
                      icon
                      v-if="descargo.id == null"
                      color="primary"
                      @click="mostarModalTipoDescargos()"
                      dark
                    >
                      <v-icon>mdi-plus-circle</v-icon>
                    </v-btn>
                  </v-col>

                  <v-col cols="6">
                    <v-text-field
                      append-icon="mdi-file"
                      v-model="descargo.acta"
                      :rules="[]"
                      label="Numero de acta"
                      :disabled= "descargo.id != null"
                  ></v-text-field>
                  </v-col>

                  <v-col cols="6">
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
                        v-model="descargo.fecha"
                        label="Seleccione la fecha del acta"
                        prepend-icon="mdi-calendar"
                        readonly
                        required                        
                        v-bind="attrs"
                        v-on="on"
                        :disabled = "descargo.id != null"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="descargo.fecha"
                      @input="menu = false"
                      :max="getFechaActual"
                    ></v-date-picker>
                  </v-menu>
                </v-col>

                  <v-col cols="6">
                    <v-textarea                    
                      label="Observación"
                      v-model="descargo.observaciones"
                      no-resize
                      rows="1"
                      row-height="10"
                      :disabled = " descargo.id != null "
                    ></v-textarea>                    
                  </v-col>

                  <v-col cols="6">
                    <v-textarea
                      disabled                    
                      label="Creado por:"
                      v-model="descargo.usuario"
                      no-resize
                      rows="1"
                      row-height="10"
                      v-if = " descargo.id != null "
                    ></v-textarea>                    
                  </v-col>

                  <v-col cols="6">
                    <v-textarea   
                      disabled                 
                      label="Fecha de Registro"
                      v-model="descargo.fechaRegistro"
                      no-resize
                      rows="1"
                      row-height="10"
                      v-if = "descargo.id != null "
                    ></v-textarea>                    
                  </v-col>

              </v-row>
            </v-form>

            <v-data-table
              :headers="encabezadosTbl"
              :items="inventarios"
              no-data-text="No hay activos seleccionados"
              :items-per-page="10"
              :search="buscarActivo"
              :footer-props="{
                'item-per-page-options': [10, 20, 30],
                showFirstLastPage: true,
              }"
            >

              <template v-slot:top>
                <v-toolbar flat color="white">
                  <div class="flex-grow-1"></div>
                  <v-dialog
                    v-model="modalInventario"
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
                        v-if="descargo.id == null"
                      >
                        Agregar activo&nbsp;
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
                          @click="modalInventario = false"
                          >Finalizar
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                </v-toolbar>
              </template>

              <template v-slot:item.observaciones="props" v-if="descargo.id == null">
                <v-edit-dialog
                  :return-value.sync="props.item.observaciones"
                  large
                  persistent
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

          </v-container>

          <v-card-actions>
            <div class="flex-grow-1"></div>
            <v-btn color="red darken-1" 
                text 
                v-if="descargo.id == null"
                @click="cancelar">
                  Cancelar
            </v-btn>
            <v-btn
              color="info darken-1"
              text
              @click="guardar"
              v-if="descargo.id == null"
              >Guardar</v-btn>

            <v-btn
              color="amber darken-1"
              text
              @click="cancelar"
              v-if="descargo.id != null"
              >Regresar</v-btn>
        </v-card-actions>
      </v-card>
    </div>
  </div>
</template>

<script>
import TipoDescargo from '../Modals/TipoDescargo.vue';

Vue.component("tipo-descargo", require("../Modals/TipoDescargo.vue").default);

export default {
  components: { TipoDescargo },
  name: 'formulario-descargo',
  data()
  {
    return {
      menu: false,
      formDescargos: false,
      errors: [],
      tiposDescargos: [],
      inventarios: [],
      encabezadosTbl: [
        { text: "Codigo", value: "codigo", align: "left" },
        { text: "Descripción", value: "descripcion", align: "left" },
        { text: "Marca", value: "marca.marca", align: "left" },
        { text: "Modelo", value: "modelo", align: "left" },
        { text: "Serie", value: "serie", align: "left" },
        { text: "Observación", value: "observaciones", align: "left" },
      ],
      buscarActivo: '',
      descargo : {
        id: null,
        tipoDescargo: { id: null, tipo: '' }, 
        observaciones: '',
        acta: '',
        fecha : '',
        fechaRegistro: null, usuario: '',
        activo: []
      },
      fechaActual: new Date(),
      formTitle: 'Agregar activo',
      modalInventario: false,
    }
  },
  computed : 
  {
    getFechaActual () 
    {
      return `${this.fechaActual.getFullYear()}-${ this.fechaActual.getMonth() + 1}-${ this.fechaActual.getDate()}`
    },
    title ()
    {
      return this.descargo.id == null ? 'Descargo de inventario' : 'Detalle de descargo'
    }
  },
  created ()
  {
    let uri = window.location.search.substring(1);
    let params = new URLSearchParams(uri);

    this.descargo.id = params.has("id") ? params.get("id") : null;
  },
  mounted ()
  {
      this.obtenerTiposDescargos()

      if ( this.descargo.id != null )
      {
          this.obtenerDescargo()
      }
  },
  methods : {
    obtenerDescargo()
    {
      axios.get(`/Api/inventario/descargos/${ this.descargo.id }`)
        .then(({ data: { descargo } }) => {
          
          const { 
              tipo_descargo,
              acta,
              fechaActa,
              observacion,
              created_at,
              user,
              inventario
          } = descargo[0]
          this.descargo.tipoDescargo = { ... tipo_descargo }
          this.descargo.acta = acta
          this.descargo.fecha = fechaActa
          this.descargo.observaciones = observacion
          this.descargo.fechaRegistro = created_at
          this.descargo.usuario = `${user.name} ${user.lastName}`
          
          inventario.forEach( activo => {
            activo.observaciones = activo.pivot.observacion
            this.inventarios.push( { ...activo } )
          })

      }).catch(() =>{
          this.alerta('Ocurrio un error en el servidor', 'error', '¡IMPORTANTE!')
            .then( () => {
              window.location = '/inventario/descargos'
            })
      });
    },
    obtenerTiposDescargos()
    {
      axios.get(`/Api/inventario/descargos/tipos`)
        .then(({ data: { tiposDescargos } }) => {
          this.tiposDescargos = tiposDescargos.filter((r) => r.eliminado == false )
      });
    },
    onAddedItem ( valores )
    {      
      this.inventarios = valores;
    },
    guardar()
    {
        if ( this.inventarios.length > 0 )
        {
          Swal.fire({
            title: "¡Importante!",
            text: "Estas seguro/as que los datos son correctos ¿Deseas hacer el descargos de estos activos?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Guardar",
            cancelButtonText: "Cancelar",
          }).then( result => {
            if (result.isConfirmed) 
            {
              this.descargo.activo = []

              this.inventarios.forEach( activo => {
                this.descargo.activo.push({
                  inventario_id: activo.id,
                  observaciones: activo.observaciones,                  
                })
              })

               const path = `/Api/inventario/descargos/save`;

              axios
                .post(path, this.descargo)
                .then( response => {

                  if (response.status == 200) 
                  {
                      const { respuesta, mensaje } = response.data;

                      if (respuesta) 
                      {
                          this.alerta(mensaje, "success", "¡Bien hecho!")
                            .then( () => {
                              window.location = '/inventario/descargos'
                            })
                      } else 
                      {
                        const { errors } = response.data
                        this.errors = errors
                      }
                  }
                }).catch( () => {
                    this.alerta('Ocurrio un error en el servidor', 'error', '¡IMPORTANTE!')
                })              
            }
          })

        } else 
        {
          this.alerta('Datos incompletos', 'warning', '¡IMPORTANTE!')
        }
    },
    cancelar()
    {
      Swal.fire({
        title: "¡Importante!",
        text: "¿ Estas seguro/as que quieres abandonar esta pantalla ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
        cancelButtonText: "No",
      }).then( result => {
          if (result.isConfirmed) 
          { 
              window.location = '/inventario/descargos';
          } 
      })
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
    mostarModalTipoDescargos()
    {
      this.$refs.tipoDescargo.mostrarModal()
    },
    onSavedTipoDescargo( tipoDescargo )
    {
      this.tiposDescargos.push( {...tipoDescargo } )
    }
  }
}
</script>

<style>

</style>
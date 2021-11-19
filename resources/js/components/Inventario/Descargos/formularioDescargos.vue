<template>
  <div class="content">
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100"
    >
      <v-card>
        <v-card-title v-text="'Descargo de inventario'">
          <div class="flex-row-1"></div>
        </v-card-title>

          <v-container fluid>
            <v-form
              ref="formMovimiento"
              v-model="formDescargos"
              :lazy-validation="true"
            >

              <v-row>

                  <v-col cols="6">
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
                      required
                    ></v-select>
                  </v-col>

                  <v-col cols="6">
                    <v-text-field
                      append-icon="fas fa-tags"
                      v-model="descargo.acta"
                      :rules="[]"
                      label="Numero de acta"
                  ></v-text-field>
                  </v-col>

                  <v-col cols="12">
                    <v-textarea                    
                      label="Observación"
                      v-model="descargo.observacion"
                      no-resize
                      rows="1"
                      row-height="10"
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

            </v-data-table>

          </v-container>

          <v-card-actions>
            <div class="flex-grow-1"></div>
            <v-btn color="red darken-1" text @click="cancelar">Cancelar</v-btn>
            <v-btn
              color="info darken-1"
              text
              @click="guardar"
              >Guardar</v-btn
            >
        </v-card-actions>
      </v-card>
    </div>
  </div>
</template>

<script>
export default {
  data()
  {
    return {
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
        tipoDescargo: { id: null, tipo: '' }, 
        observacion: '',
        acta: '',
        activo: []
      },

      formTitle: 'Agregar activo',
      modalInventario: false,
    }
  },
  mounted ()
  {
      this.obtenerTiposDescargos()
  },
  methods : {
    obtenerTiposDescargos()
    {
      axios.get(`/Api/inventario/descargos/tipos`)
        .then(({ data: { tiposDescargos } }) => {
          this.tiposDescargos = tiposDescargos
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
              this.inventarios.forEach( activo => {
                this.descargo.activo.push({
                  inventario_id: activo.id,
                  observacion: activo.observaciones,                  
                })
              })

              console.log( this.descargo )
               console.log( 'guardando' )
            } else 
            {
              console.log('cancelado')
            }
          })

        } else 
        {
          this.alerta('Datos incompletos', 'warning', '¡IMPORTANTE!')
        }
    },
    cancelar()
    {

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
  }
}
</script>

<style>

</style>
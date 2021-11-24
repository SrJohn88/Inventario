<template>
  <div class="content">
    <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
        
        <v-overlay :value="loader" :z-index="'99999999'">
          <v-progress-circular
            indeterminate
            size="80"
            color="grey darken-4"
          ></v-progress-circular>
      </v-overlay>

      <v-card>
        <v-card-title>
           Inventario
        </v-card-title>

        <v-data-table
          :headers="encabezados"
          :items="activos"
          :footer-props="{
            'items-per-page-options': [15, 25, 35, 45],
            'items-per-page-text': 'Registros Por Página',
          }"
          :items-per-page="10"
          :search="buscarActivo"
          multi-sort
          no-data-text="No hay activos para mostrar"
          class="elevation-1"
        >

          <template v-slot:top>
              <v-container fluid>
                <v-radio-group v-model="tipoReporte" row>
                  <v-radio
                    label="General"
                    value="General"
                    @click="cambiarValor(true)"
                  ></v-radio>
                  <v-radio
                    label="Ubicación"
                    value="Ubicacion"
                    @click="cambiarValor(false)"
                  ></v-radio>
                </v-radio-group>
              </v-container>

              <v-card-title>
                <v-row>
                  <v-col cols="6" align="center" v-if="rangoFechas">
                    <!-- <label>Fecha Inicio: </label> -->
                    <date-picker
                      v-model="desde"
                      :editable="false"
                      lang="es"
                      style="width: 75% !important"
                      input-class="form-control"
                      class="mt-2"
                      value-type="format"
                      format="DD-MM-YYYY"
                      placeholder="Fecha Desde"
                    >
                    </date-picker>
                  </v-col>
                  <v-col cols="6" align="center" v-if="rangoFechas">
                    <!-- <label>Fecha Final: </label> -->
                    <date-picker
                      v-model="hasta"
                      :editable="false"
                      lang="es"
                      style="width: 75% !important"
                      input-class="form-control"
                      class="mt-2"
                      value-type="format"
                      format="DD-MM-YYYY"
                      placeholder="Fecha Hasta"
                    >
                    </date-picker>
                  </v-col>
                  <v-col cols="12" align="center" v-if="true">
                  <v-autocomplete
                    style="width: 50% !important"
                    v-model="ubicacion"
                    :items="ubicaciones"
                    required
                    label="Seleccione una ubicación"
                    item-text="ubicacion"
                    item-value="id"
                    return-object
                    clearable
                    :menu-props="{ closeOnClick: true }"
                  ></v-autocomplete>
                  </v-col>
                </v-row>              
              </v-card-title>

              <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog
                v-model="modalReporte"
                persistent
                max-width="700px"
              >
                <template v-slot:activator="{ on }">
                  <div class="text-center ma-4">
                    <v-btn
                      elevation="10"
                      color="primary"
                      style="font-size: 10px"
                      
                      small
                      :loading="loader"
                      :disabled="loader"
                      @click="generar"
                    >
                      GENERAR REPORTES
                    </v-btn>

                    <v-btn
                      elevation="10"
                      color="red"
                      style="font-size: 10px"
                      small
                      dark
                      :loading="loader"
                      :disabled="loader"
                    >
                      EXPORTAR EN EXCEL
                      <!-- <v-icon right >fas fa-file-excel</v-icon> -->
                    </v-btn>
                  </div>
                </template>
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
  data()
  {
    return {
      loader: false,
      tipoReporte: null,
      buscarActivo: '',
      encabezados: [
        { text: "Código", value: "codigo", sortable: false },
        { text: "Descripción", value: "descripcion", sortable: false },
        { text: "Marca", value: "marca.marca", sortable: false },
        { text: "Modelo", value: "modelo", sortable: false },
        { text: "Serie", value: "serie", sortable: false },
        { text: "Rubro", value: "rubro.rubro", sortable: false },
        { text: "Precio", value: "precio", sortable: false },
        { text: "Ubicación", value: "ubicacion.ubicacion", sortable: false },
        { text: "Fecha Adquisición", value: "fecha_adquision",sortable: false,},
        { text: "Observación", value: "observacion", sortable: false },
      ],
      activos: [],
      ubicaciones: [],
      ubicacion: { id: null, ubicacion: '' },
      rangoFechas: true,
      desde: null, hasta: null,
      modalReporte: false
    }
  },
  mounted()
  {
    this.obtenerUbicaciones()
  },
  methods: {
    obtenerUbicaciones()
    {
      axios
        .get("/Api/ubicaciones")
        .then( ({ data: { ubicaciones } }) => {
            this.ubicaciones = ubicaciones.filter((r) => r.eliminado == false)
        })
        .catch(console.error);
    },
    generar()
    {
      if ( this.tipoReporte )
      {
        const path = this.ubicacion == null || this.ubicacion.id == null
                ? `/Api/reportes/inventario/${this.desde}/${this.hasta}` 
                : `/Api/reportes/inventario/${this.desde}/${this.hasta}/${this.ubicacion.id}` 
        
        this.loader = true

        axios
          .get( path )
          .then( ( { data : { activos } } ) => {
            this.loader = false
            this.activos = [ ...activos ]
          }).catch( () => {
            this.loader = false
          })
        
      } else 
      {
        Swal.fire({
          icon: 'error',
          title: '¡IMPORTANTE!',
          text: 'Completa los campos',
        })
      }
    },
    obtenerInventario()
    {

    },
    cambiarValor( value )
    {

    }
  }
}
</script>
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
          Revisiones de inventario
          <div class="flex-grow-1"></div>
          <v-text-field
            v-model="buscar"
            label="Buscar revisión"
            hide-details
          ></v-text-field>
        </v-card-title>

        <v-data-table
          :headers="encabezados"
          :items="revisiones"
          :footer-props="{
            'items-per-page-options': [5,10, 20, 30,40],
            'items-per-page-text' : 'Registros Por Página'
          }"
          :items-per-page="5"
          :search="buscar"
          multi-sort
          class="elevation-1"
          no-data-text="No hay revisiones para mostrar"
        >

          <template v-slot:top >
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="modal" persistent max-width="700px">
                <template v-slot:activator="{ on }">
                  <v-btn elevation="10" color="blue  darken-3" dark class="mb-2" v-on="on">
                    Nueva revisión&nbsp;
                    <v-icon>mdi-plus-box-multiple-outline</v-icon>
                  </v-btn>

                </template>
                <v-card>
                  <v-card-title class="headline grey lighten-2" primary-titles>
                    <span class="headline" v-text="'Nueva revisón'"></span>
                  </v-card-title>
                  <v-card-text>
                    <v-container>
                      <v-form ref="formRevision" v-model="validForm" :lazy-validation="true">
                        <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="revision.nombre"
                          @keyup="errors = []"
                          :rules="[reglas.min, reglas.requerido, reglas.expresion]"
                          label="Nombre"
                          required
                          :error-messages="errors"
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
                      @click="save"
                      text
                      v-text="'Crear revisión'"
                    ></v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </v-toolbar>
          </template>

          <template v-slot:item.action="{item}" v-slot:activator="{ on }">
            <v-tooltip top >
              <template v-slot:activator="{ on }" >
                <v-btn
                  color="success"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="editar( item )"
                >
                  <v-icon>far fa-clipboard</v-icon>
                </v-btn>

                <v-btn
                  color="success"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="generarPdf( item )"
                >
                  <v-icon>far fa-clipboard</v-icon>
                </v-btn>
              </template>
              <span>Generar reporte</span>
            </v-tooltip>
          </template>

          <template v-slot:item.progreso="{item}" v-slot:activator="{ on }">
            <v-progress-linear
              color="amber"
              height="15"
              :value=" item.porcentaje"
              striped
            >
              <template v-slot:default="{ value }">
                <strong>{{ Math.ceil(value) }}%</strong>
              </template>
            </v-progress-linear>
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
      buscar: '',
      revisiones: [],
      validForm: false, modal: false,
      encabezados: [
        { text: "Revision", value: "nombre", align: "center" },
        { text: "Elaborada por", value: "user.name", align: "center" },
        { text: "Progreso", value: "progreso", align: "center" },
        { text: "Revisados", value: "revisados", align: "center" },
        { text: "Total a revisar", value: "total", align: "center" },
        { text: "Fecha de registro", value: "created_at", align: "center" },
        { text: "Acciones", value: "action", sortable: false, align: "center" },
      ],      
      errors: [],
      revision: { id: null, nombre: '' },
      reglas: {
        requerido: (v) => !!v || "Campo requerido",
        min: (v) => (v && v.length >= 2 && v.length <= 100) ||
                "Este campo debe ser mayor a 2 caracteres",
        expresion: (v) =>
                /^[A-Za-z0-9-ñáéíóúÁÉÍÓÚ \s]+$/g.test(v) ||
                "Este campo no puede tener caracteres especiales",
      },
    }
  },
  mounted()
  {
    this.obtenerRevisiones()
  },
  methods : {
    obtenerRevisiones()
    {
      this.loader = true
      axios.get(`/Api/inventario/revisiones`)
        .then( ({ data: { revisiones } } ) => {
          
          this.revisiones = []

          this.loader = false

          let revisionesActivas = revisiones.filter((r) => r.eliminado == false );

          revisionesActivas.forEach( revision =>{
            let totalActivos = revision.inventario.length
            let activosRevisados = revision.inventario.filter(activo => activo.pivot.revisado == true).length
            let porcentaje = ( activosRevisados * 100 ) / totalActivos

            this.revisiones.push(  { ...revision, porcentaje, total: totalActivos, revisados: activosRevisados } )

          })
        })
        .catch( (e) => {
          this.loader = false;
        });
    },
    save() 
    {
      this.loader = true
      const path = `/Api/inventario/revisiones`

      axios
        .post( path, this.revision )
        .then( response => {
          
          this.loader = false
          
          if (response.status == 200) 
          {
            const { respuesta, mensaje } = response.data 

            if (respuesta) 
            {
              this.obtenerRevisiones()
              this.alerta( mensaje, 'success', '¡Bien hecho!');
              this.cerrarModal();
            } else
            {
              const { errors } = response.data
              this.errors = [...errors]
              console.log( this.errors )
            }
          
          } else 
          {

          }
        }).catch( () => {

        })

    },
    cerrarModal() 
    {
      this.modal = false;
        setTimeout(() => {
          this.revision = { id: null, nombre: null }
          this.resetValidation();
        }, 300);    
    },
    resetValidation() {
      this.errors = [];
      this.$refs.formRevision.resetValidation();
    },
    editar( { ...revision } )
    {
      window.location = `/inventario/revision/crear?id=${revision.id}`
    },
    generarPdf( {...revision })
    {
      window.open(`http://localhost:8000/inventario/revisiones/documento/${revision.id}`, '_blank')
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
    }
};
</script>

<style>
</style>
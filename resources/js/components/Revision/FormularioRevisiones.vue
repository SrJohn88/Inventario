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
           Revision de inventario
        </v-card-title>

        <v-card-title>
          <v-row>
            <v-col cols="4" align="right">
              <v-text-field
                label="Nombre de la revisión"
                outlined              
                input-class="form-control"
                v-model="revision.nombre"
                readonly
              ></v-text-field>                                                                    
            </v-col>
            <v-col cols="4" align="right">
              <v-text-field
                label="Elaborado por"
                outlined                
                input-class="form-control"
                v-model="revision.user.name"
                readonly
              ></v-text-field>                                                                    
            </v-col>
            <v-col cols="4" align="right">
              <v-text-field
                label="Fecha de registro"
                outlined                
                input-class="form-control"
                v-model="revision.created_at"
                readonly
              ></v-text-field>                                                                    
            </v-col>
          </v-row>
        </v-card-title>

        <v-card-title>
          <v-row>
            <v-col cols="12" align="center">
              <v-btn
              elevation="10"
              color="red"
              style="font-size: 10px"
              small
              dark
              :loading="loader"
              :disabled="loader"
              @click="cancelar"
            >
              Cancelar
              </v-btn>

              <v-btn
                elevation="10"
                color="primary"
                style="font-size: 10px"
                small
                dark
                :loading="loader"
                :disabled="loader"
                @click="save"
              >
                Guardar             
              </v-btn>

            </v-col>
          </v-row>
          
          <div class="text-center ma-4">
            

          </div>
        </v-card-title>

        <v-card-title>
          Activos
          <div class="flex-grow-1"></div>
          <v-text-field v-model="buscar" label="Buscar activo" hide-details></v-text-field>
        </v-card-title>

        <v-data-table
          :headers="encabezados"
          :items="activos"
          :footer-props="{
            'items-per-page-options': [ 50, 100, 150 ],
            'items-per-page-text': 'Registros Por Página',
          }"
          :items-per-page="50"
          :search="buscar"
          multi-sort
          no-data-text="No hay activos para mostrar"
          class="elevation-1"
        >

        <template v-slot:item.revisado="{ item }" v-if="true">
          <v-simple-checkbox
              v-model="item.pivot.revisado"
              :disabled="item.pivot.revisado && item.completo"
              color="primary"
          ></v-simple-checkbox>
        </template>

        <template v-slot:item.esCorrecto="{ item }" v-if="true">
          <v-simple-checkbox
              v-model="item.pivot.esCorrecto"
              :disabled="item.pivot.revisado && item.completo"
              color="primary"
            ></v-simple-checkbox>
        </template>

        <template v-slot:item.observacion="{item}">
                <v-edit-dialog
                  :return-value.sync="item.pivot.observacion"
                  large
                  persistent
                  v-if="!item.completo"
                >
                  {{ item.pivot.observacion }}
                  <template v-slot:input>
                    <div class="mt-4 text-h6">Observaciones:</div>
                    <v-text-field
                      v-model="item.pivot.observacion"
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
      </v-card>

    </div>
  </div>
</template>

<script>
export default {
  data()
  {
    return {
      buscar: '',
      loader: false,
      activos: [],
      encabezados: [
        { text: "Fecha Adquisición", value: "fecha_adquision",sortable: false, align: "center" },
        { text: "Código", value: "codigo", sortable: false },
        { text: "Descripción", value: "descripcion" },
        { text: "Estado", value: "estado.estado" },
        { text: "Revisado", value: "revisado", align: 'center', sortable: false },
        { text: "Ubicacion Correcta", value: "esCorrecto", align: 'center', sortable: false },                        
        { text: "Observación", value: "observacion", sortable: false },
      ],
      revision: { 
          id : null, nombre: '', 
          user: { id: null, name: '' },
          created_at: ''
        }
    }
  },
  created()
  {
    let uri = window.location.search.substring(1);
    let params = new URLSearchParams(uri);

    this.revision.id = params.has("id") ? params.get("id") : null;
  },
  mounted()
  {
    this.obtenerRevision()
  },
  methods : {
    obtenerRevision()
    {
      this.loader = true

      axios.get(`/Api/inventario/revisiones/${this.revision.id}`)
            .then( ( { data: { revisiones = [] } } ) => {
              
              this.loader = false

              const {
                user, nombre, inventario, created_at
              } = revisiones[0];

              this.revision.nombre = nombre
              this.revision.user = { ...user }
              // this.activos = [...inventario ]
              this.revision.created_at = created_at

              inventario.forEach( activo => {
                activo.completo = (!!parseInt( activo.pivot.revisado ) ? true : false )
                activo.pivot.revisado = (!!parseInt( activo.pivot.revisado ) ? true : false )
                activo.pivot.esCorrecto = (!!parseInt( activo.pivot.esCorrecto ) ? true : false )

                this.activos.push( { ...activo } )
              })
              
            }).catch( console.error )
    },
    save()
    {
      this.loader = true

      const path = `/Api/inventario/revisiones/${ this.revision.id }`

      let datos = []

      this.activos.forEach( activo => {
        datos.push({
          'inventario_id': activo.id,
          'revisado' : activo.pivot.revisado,
          'esCorrecto' : activo.pivot.esCorrecto,
          'observacion' : activo.pivot.observacion
        })
      })

      axios
        .post( path, {
          ... this.revision, activos: [... datos ]
        }).then( response => {
          this.loader = false

          if ( response.status == 200 )
          {
            const { respuesta, mensaje } = response.data 

            if ( respuesta )
            {
              this.alerta( mensaje, 'success', '¡Bien hecho!')
                .then( () => {
                  window.location = '/inventario/revision'
                })

            } else 
            {
              this.alerta( mensaje, 'warning', '¡IMPORTANTE!')
            }
          } else {
            throw new Error('Ocurrio un error en el servidor')
          }
        }).catch( () => {
          this.loader = false
          Swal.fire({
            icon: 'warning',
            title: '¡IMPORTANTE!',
            text: 'Ocurrio un error en el servidor',
          })
        })
    },
    cancelar()
    {
      Swal.fire({
        title: "INFORMACION",
        text: `¿Quieres salir de esta pantalla?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3698e3",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) 
        {
          window.location = '/inventario/revision'
        }
      })
    },
    alerta (mensaje, icono = 'info', titulo = '')
    {
      return new Promise( ( resolve, reject ) => {
        Swal.fire({
          position: "top-end",
          icon: icono,
          title: titulo,
          text: mensaje,
          showConfirmButton: false,
          timer: 1500,
        }).then( resolve )
      })
    }
  }
}
</script>

<style>

</style>
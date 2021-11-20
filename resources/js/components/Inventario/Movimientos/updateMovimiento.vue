<template>
  <div class="content">
        <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
            <v-card>
                <v-card-title>
                     {{ isCompleto }}
                    <div class="flex-grow-1"></div>                    
                </v-card-title>
            

                <v-form>
                    <v-container>
                        <v-row>
                            <v-col cols="12" sm="6" >
                                <v-text-field
                                    v-model="movimiento.tipoMovimiento.tipo"
                                    label="Tipo de movimiento"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6" >
                                <v-text-field
                                    v-model="movimiento.aprueba.nombre"
                                    label="Aprobo"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6" >
                                <v-text-field
                                    v-model="movimiento.recibe.nombre"
                                    label="Recibio"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6" >
                                <v-text-field
                                    v-model="movimiento.gerencia.nombre"
                                    label="Aprobo por gerencia"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6" >
                                <v-text-field
                                    v-model="movimiento.usuario.name"
                                    label="Entregado por"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6" >
                                <v-text-field
                                    v-model="movimiento.created_at"
                                    label="Fecha de registro"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="12" >
                                <v-text-field
                                    v-model="movimiento.observaciones"
                                    label="Observaciones"
                                    disabled
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-form>

                <v-container>
                    <v-data-table
                    v-model="selected"
                    :headers="headMovimientos"
                    :items="activosPendientes"
                    no-data-text="No hay activos"
                    :items-per-page="10"
                    :footer-props="{
                        'item-per-page-options': [10, 20, 30],
                        showFirstLastPage: true,
                    }"
                    item-key="codigo"
                    show-select
                    >

                    <template v-slot:item.pivot.falla="props">
                        <v-edit-dialog
                            :return-value.sync="props.item.pivot.falla"
                            @save="guardarFallar"
                            @cancel="cancelarFalla"
                            >
                            {{ props.item.pivot.falla }}
                            <template v-slot:input>
                                <v-text-field
                                v-model="props.item.pivot.falla"
                                :rules="[]"
                                label="Edit"
                                single-line
                                counter
                                ></v-text-field>
                            </template>
                        </v-edit-dialog>
                    </template>

                    <template v-slot:item.pivot.observaciones="props">
                        <v-edit-dialog
                            :return-value.sync="props.item.pivot.observaciones"
                            @save="guardarFallar"
                            @cancel="cancelarFalla"
                            >
                            {{ props.item.pivot.observaciones }}
                            <template v-slot:input>
                                <v-text-field
                                v-model="props.item.pivot.observaciones"
                                :rules="[]"
                                label="Edit"
                                single-line
                                counter
                                ></v-text-field>
                            </template>
                        </v-edit-dialog>
                    </template>

                    <template v-slot:item.action="{item}" v-slot:activator="{ on }">
                        <v-tooltip top>
                        <template v-slot:activator="{ on }">
                            <v-btn
                            v-if="item.pivot.recibido"
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
                        <span>Actualizar Cuenta</span>
                        </v-tooltip>
                    </template>
                    
                    <template v-slot:top>
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="modal" persistent max-width="700px">                
                <v-card>
                  <v-card-title class="headline lighten-2" primary-titles>
                    <span class="headline" v-text="'Detalle de activo'"></span>
                  </v-card-title>
                  <v-card-text>
                    <v-container>
                      <v-form
                        :lazy-validation="true"
                      >
                        <v-row>
                            <v-col cols="6" >
                                <v-text-field
                                    append-icon="mdi-folder-outline"
                                    v-model="detalleActivo.usuario"
                                    @keyup="errors = []"
                                    :rules= "[]"
                                    label="Usuario"
                                    required
                                    :readonly = "true"
                                    :error-messages="errors"
                                    ></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                    <v-text-field
                                        append-icon="mdi-folder-outline"
                                        v-model="detalleActivo.fecha"
                                        @keyup="errors = []"
                                        :rules= "[]"
                                        label="Fecha de recibido"
                                        required
                                        :readonly = "true"
                                        :error-messages="errors"
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
                      @click="ocultarModal"
                    ></v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </v-toolbar>
                    </template>

                </v-data-table>

                 <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn
                        v-if="mostrarBotones" 
                        color="red darken-1" 
                        text @click="cancelar">
                            Cerrar
                    </v-btn>
                    <v-btn
                        color="info darken-1"
                        text
                        v-if="mostrarBotones"
                        :disabled="false"
                        @click="guardar"
                        >Guardar</v-btn
                    >
                    </v-card-actions>
                </v-container>
            </v-card>
        </div>
  </div>
</template>

<script>
export default {

    data()
    {
        return {
            errors: [], modal: false, detalleActivo: { usuario : null, fecha: null },
            contadorPendientes : 0,
            singleSelect: false, selected: [],
            idMovimiento: null,
            movimiento: {
                tipoMovimiento: { id: null, tipo: "" },
                recibe: { id: null, nombre: "" },
                aprueba: { id: null, nombre: "" },
                gerencia: { id: null, nombre: "" },
                ubicacion: { id: null, ubicacion: "" },
                fecha: "",
                registro: null,
                usuario: { id: null, nombre: ''},
                created_at: '',
                observaciones: "",
                activos: [],
            },
            headMovimientos: [
                { text: "Codigo", value: "codigo", align: "left" },
                { text: "Descripción", value: "descripcion", align: "left" },            
                { text: "Marca", value: "marca.marca", align: "left" },            
                { text: "Modelo", value: "modelo", align: "left" },            
                { text: "Falla", value: "pivot.falla", align: "left" },
                { text: "Observaciones", value: "pivot.observaciones", align: "left" },
                { text: "Estado", value: "estado", align: "left" },
                { text: "Acciones", value: "action", align: "left" },
            ],
        }
    },
    computed: {
        activosPendientes() 
        {
            return this.movimiento.activos.map( activo => ({ ... activo, isSelectable: activo.pivot.recibido == false }))
        },
        mostrarBotones()
        {
            return this.contadorPendientes > 0 
        },
        isCompleto()
        {
            return this.contadorPendientes > 0 
                            ? `Movimiento | Pendiente de completar` 
                            : 'Movimiento | Completo'
        }
    },
    created()
    {
        let uri = window.location.search.substring(1);
        let params = new URLSearchParams(uri);

        this.idMovimiento = params.has("id") ? params.get("id") : null;
    },
    mounted()
    {
        if ( this.idMovimiento)
        {
            this.obtenerMovimiento();
        }
    }, 
    methods: {
        fueRecibido( detalle  ) {
            return detalle.recibido == true ? 'RECIBIDO' : 'PENDIENTE' ;
        },
        obtenerMovimiento()
        {
            this.loader = true;
            axios
                .get(`/Api/inventario/${this.idMovimiento}/movimientos`)
                .then(({ data: { movimiento } }) => {
                    let {
                        tipo_movimiento,
                        recibe,
                        aprueba,
                        user,
                        aprobado_gerencia,
                        created_at,
                        descripcion,
                        inventario                        
                    } = movimiento[0]

                    this.movimiento.tipoMovimiento = { ... tipo_movimiento }
                    this.movimiento.recibe = { ... recibe }
                    this.movimiento.aprueba = { ... aprueba}
                    this.movimiento.gerencia = { ... aprobado_gerencia}
                    this.movimiento.registro = created_at
                    this.movimiento.usuario = { ... user }
                    this.movimiento.observaciones = descripcion
                    this.movimiento.created_at = created_at

                    inventario.forEach( inventario => {
                        if ( !inventario.marca )
                        {
                            inventario.marca = { id: null, marca: '' }
                        }
                        inventario.estado = inventario.pivot.recibido == true ? 'RECIBIDO': 'PENDIENTE'
                        
                        if ( ! inventario.pivot.recibido )
                        {
                            this.contadorPendientes++
                        }
                    })
                    this.movimiento.activos = [ ... inventario ]
            
                    console.log( { inventario })

                }).catch( console.error )

        },
        guardar()
        {
            if ( this.selected.length == 0 )
            {
                this.alerta(
                    'Selecciona al menos un activo de la lista',
                    'warning', '¡INFORMACION!')
            } else
            {
                Swal.fire({
                title: "¡Importante!",
                text: "¿Estas seguro de realizar la operación?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Guardar",
                cancelButtonText: "Cancelar",
            }).then( result => {
                if (result.isConfirmed) {
                    this.loader = true;
                    const path = `/Api/inventario/movimientos/update/${ this.idMovimiento }`
                    const datos = {
                        activos: [ ...this.prepararDatos() ]                     
                    }

                    axios
                        .post(path, { ...datos } )
                        .then( response => {
                            if (response.status == 200) 
                            {
                                const { respuesta, mensaje } = response.data

                                if ( respuesta ) 
                                {
                                    this.alerta( mensaje, 'success', '¡Bien hecho!')
                                        .then( () => {
                                            window.location = '/inventario/movimientos'
                                        })
                                }
                            } else {
                                this.alerta('Ocurrio un error inesperado', 'error', '¡INFORMACION!')
                            }
                        }).catch( () => {
                            this.alerta('Ocurrio un error inesperado', 'error', '¡INFORMACION!')
                        })                   
                }
            })
            }      
            //console.log( this.getIdSeleccion() )
        }, 
        cancelar()
        {
            window.location = '/inventario/movimientos'
        },
        prepararDatos ()
        {
            let activosRecibidos = []

            this.selected.forEach( activo => {
                activosRecibidos.push({
                    inventario_id : activo.id,
                    falla: activo.pivot.falla,
                    observaciones: activo.pivot.observaciones,
                    recibido : true
                })
            });

            return activosRecibidos
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
        mostrarModal( item )
        {
            const fechaRecibido = new Date( item.pivot.updated_at)
            this.detalleActivo.usuario = item.pivot.usuario            
            this.detalleActivo.fecha = `${fechaRecibido.getDate()}-${fechaRecibido.getMonth()}-${fechaRecibido.getFullYear()} ${fechaRecibido.getHours()}:${fechaRecibido.getMinutes()}`
            this.modal = true
        },
        ocultarModal()
        {
            this.modal = false
            this.detalleActivo = { fecha: null, usuario: '' }
        },
        guardarFallar( item )
        {
        },
        cancelarFalla( item )
        {

        },
    }
}
</script>

<style>

</style>
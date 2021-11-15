<template>
  <div class="content">
        <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
            <v-card>
                <v-card-title>
                    Detalle de Movimiento
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
                                    label="Usuario"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="6" >
                                <v-text-field
                                    v-model="movimiento.gerencia.nombre"
                                    label="Fecha de registro"
                                    disabled
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="12" >
                                <v-text-field
                                    v-model="movimiento.observaciones"
                                    label="Fecha de registro"
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
                    

                </v-data-table>

                 <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="red darken-1" text @click="cancelar">Cerrar</v-btn>
                    <v-btn
                        color="info darken-1"
                        text
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
            ],
        }
    },
    computed: {
        activosPendientes() 
        {
            return this.movimiento.activos.map( activo => ({ ... activo, isSelectable: activo.pivot.recibido == false }))
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

                    inventario.forEach( inventario => {
                        if ( !inventario.marca )
                        {
                            inventario.marca = { id: null, marca: '' }
                        }
                        inventario.estado = inventario.pivot.recibido == true ? 'RECIBIDO': 'PENDIENTE'
                        
                    })
                    this.movimiento.activos = [ ... inventario ]
            
                    console.log( { inventario })

                }).catch( console.error )

        },
        guardar()
        {
            console.log( { ... this.prepararDatos() } )
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
                        .then( console.log ).catch(console.error)                   
                }
            })
            //console.log( this.getIdSeleccion() )
        }, 
        cancelar()
        {

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
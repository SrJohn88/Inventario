<template>
    <v-container>
        <v-row>
            <div class="flex-grow-1"></div>
            <v-text-field v-model="buscarInventario" label="Buscar Movimiento" hide-details/>

            <v-col cols="12" md="12" lg="12" xl="12" sm="12">
                <v-data-table
                v-model="selected"
                :headers="headTablaInventario"
                :items ="activosInventario"
                :items-per-page="5"
                :footer-props="{
                    'items-per-page-options': [5, 10, 15, 20],
                }"
                :search="buscarInventario"
                :single-select="singleSelect"
                item-key="codigo"
                show-select
                >
                
                </v-data-table>
            </v-col>
        </v-row>
  </v-container>
</template>

<script>
export default {
    data() {
        return {
            singleSelect: false,
            buscarInventario: '',
            headTablaInventario: [
                { text: "Codigo", value: "codigo", align: "left" },
                { text: "Descripción", value: "descripcion", align: "left" },
                { text: "Marca", value: "marca.marca", align: "left" },
                { text: "Ubicacion", value: "ubicacion.ubicacion", align: "left" },
                { text: "Serie", value: "serie", align: "left" },
                { text: "Estado", value: "estado.estado", align: "left" },
            ],
            activosInventario: [],
            activo: {  },
            selected: []
        }
    },
    mounted () {
        this.getInventario();
    },
    watch: {
        selected: function() {
            this.$emit("added", this.selected);
        }
    },
    methods: {
        getInventario () {
            axios
                .get("/Api/inventario/activos")
                .then( ( { data: { activos } } ) => {
                    //console.log( activos );
                    activos = activos.filter( c => c.estado_id == 1)
                    activos.forEach( activo => {
                        this.activosInventario.push(
                            { ...activo, falla: '', observaciones: '' }
                        )
                    })

                    //this.activosInventario = activos;
                })
                .catch(console.error);
        },
        datos() {
            console.log( this.selected );
        }
    }
}
</script>

<style>

</style>
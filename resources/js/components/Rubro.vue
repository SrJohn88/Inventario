<template>
    <div class="content">
        <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">

            <v-card>
                <v-card-title>
                    Rubros
                    <div class="flex-grow-1"></div>
                    <v-text-field
                        v-model="buscarRubro"
                        label="Buscar"
                        hide-details
                    ></v-text-field>
                </v-card-title>
                <v-data-table
                    :headers="encabezados"
                    :items="rubros"
                    :footer-props="{
                        'items-per-page-options': [5, 10, 20, 30, 40],
                        'items-per-page-text': 'Registros Por Página',
                    }"
                    :items-per-page="5"
                    :search="buscarEntidad"
                    multi-sort
                    class="elevation-1"
                    >
                </v-data-table>
            </v-card>
        </div>
    </div>
    
</template>

<script>
export default {
    data() {
        return {
            encabezados: [
                { text: "ID", value: "id" },
                { text: "Rubro", value: "rubro" },
                { text: "Acciones", value: "action", sortable: false, align: "center" },
            ],
            rubros: [],
            buscarRubro: '',

        }
    }
}
</script>

<style>

</style>
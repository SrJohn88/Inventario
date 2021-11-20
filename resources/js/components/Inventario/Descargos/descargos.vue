<template>
  <div class="content">
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100"
    >
      <v-card>
        <v-card-title>
          Descargos de inventario
          <div class="flex-grow-1"></div>
          <v-text-field
            v-model="buscarDescargo"
            label="Buscar Movimiento"
            hide-details
          ></v-text-field>

          <template>
            <div class="text-center ma-2">
              <v-btn
                color="primary"
                dark
                elevation="2"
                small
                @click="formDescargo()"
              >
                Nuevo Descargo
              </v-btn>
            </div>
          </template>
        </v-card-title>

        <v-data-table
          :headers="encabezadosTbl"
          :items="descargos"
          :footer-props="{
            'items-per-page-options': [10, 15, 25, 35, 45],
            'items-per-page-text': 'Registros Por Página',
          }"
          :items-per-page="10"
          :search="buscarDescargo"
          multi-sort
          class="elevation-1"
        >
          <template v-slot:item.action="{ item }" v-slot:activator="{ on }">
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                  color="success"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="detalle(item)"
                >
                  <v-icon>far fa-clipboard</v-icon>
                </v-btn>
              </template>
              <span>Ver Detalle</span>
            </v-tooltip>
          </template>
        </v-data-table>
      </v-card>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      descargos: [],
      encabezadosTbl: [
        { text: "Tipo Descargo", value: "tipo_descargo.tipoDescargo" },
        { text: "Realizado por", value: "user.name" },
        { text: "N° acta", value: "acta" },
        { text: "Fecha de acta", value: "fechaActa" },
        { text: "Fecha de registro", value: "created_at" },
        { text: "Acciones", value: "action", sortable: false, align: "center" },
      ],
      buscarDescargo: "",
    };
  },
  mounted() {
    this.obtenerDescargos();
  },
  methods: {
    obtenerDescargos() {
      axios
        .get("/Api/inventario/descargos")
        .then(({ data: { descargos } }) => {
          this.descargos = [...descargos];
        })
        .catch(console.error);
    },
    formDescargo() {
      window.location = "/inventario/descargos/crear";
    },
    detalle( item )
    {
      window.location = `/inventario/descargos/crear?id=${item.id}`
    }
  },
};
</script>

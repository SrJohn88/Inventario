
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
          {{ cardTitle }}
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form
              ref="formInventario"
              v-model="inventarioValido"
              :lazy-validation="true"
            >
              <v-row>
                <v-col cols="6">
                  <v-text-field
                    append-icon="fas fa-barcode"
                    v-model="inventario.codigo"
                    @keyup="errors.codigo = []"
                    :rules="[ reglas.codigo, reglas.min ]"
                    label="Código"
                    required
                    :disabled="detalle"
                    :error-messages="errors.codigo"
                  ></v-text-field>
                </v-col>

                <v-col cols="6">
                  <v-text-field
                    append-icon="fas fa-code"
                    :rules="[reglas.noRequerido]"
                    v-model="inventario.serie"
                    label="Serie"
                    :disabled="detalle"
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-textarea
                    append-icon="fas fa-tags"
                    @keyup="errors.descripcion = []"
                    :error-messages="errors.descripcion"
                    v-model="inventario.descripcion"
                    label="Descripcion"
                    rows="2"
                    :rules="[(v) => !!v || 'Codigo Es Requerido']"
                    :disabled="detalle"
                    required
                  ></v-textarea>
                </v-col>

                <v-col cols="5">
                  <v-autocomplete
                    append-icon="fas fa-building"
                    v-model="inventario.marca"
                    :items="marcas"
                    label="Marca"
                    item-text="marca"
                    item-value="id"
                    aling="center"
                    return-object
                    clearable
                    :disabled="detalle"
                    :menu-props="{ closeOnClick: true }"
                  ></v-autocomplete>
                </v-col>

                <v-col cols="1" md="1">
                  <Marca @saved="onSavedMarca" ref="marca" />
                  <v-btn
                    elevation="5"
                    class="mt-8"
                    text
                    icon
                    v-show="!detalle"
                    color="primary"
                    @click="mostrarModalMarca()"
                    dark
                  >
                    <v-icon>mdi-plus-circle</v-icon>
                  </v-btn>
                </v-col>

                <v-col cols="6">
                  <v-text-field
                    append-icon="fas fa-cube"
                    v-model="inventario.modelo"
                    label="Modelo"
                    :disabled="detalle"
                  ></v-text-field>
                </v-col>

                <v-col cols="6">
                  <v-select
                  append-icon="fas fa-compass"
                    ref="autocompleteCuenta"                    
                    :error-messages="errors['procedencia.id']"
                    :items="procedencias"
                    v-model="inventario.procedencia"
                    item-text="procedencia"
                    item-value="id"
                    label="Procedencia"
                    return-object
                    @change="onChangeProcedencia"
                    :disabled="detalle"
                    :menu-props="{ closeOnClick: true }"
                    :rules="[(v) => !!v || 'Procedencia es campo obligatorio']"
                    required
                  ></v-select>
                </v-col>

                <v-col cols="5" v-show="inventario.procedencia.id == 1">
                  <v-autocomplete
                  append-icon="fas fa-wallet"
                    v-model="inventario.cuenta"
                    :items="cuentas"                    
                    label="Cuenta"
                    item-text="cuenta"
                    item-value="id"
                    return-object
                    :disabled="detalle"
                    clearable
                    :menu-props="{ closeOnClick: true }"
                  ></v-autocomplete>
                </v-col>

                <v-col cols="1" md="1" v-show="inventario.procedencia.id == 1">
                  <Cuenta @saved="onSavedCuenta" ref="cuenta" />
                  <v-btn
                    elevation="5"
                    class="mt-8"
                    text
                    :disabled="detalle"
                    icon
                    color="primary"
                    @click="mostrarModalCuenta()"
                    dark
                  >
                    <v-icon>mdi-plus-circle</v-icon>
                  </v-btn>
                </v-col>

                <v-col cols="5" v-if="inventario.procedencia.id != 1">
                  <v-autocomplete
                    :disabled="detalle"
                    append-icon="fas fa-home"
                    v-model="inventario.entidad"
                    :items="entidades"                    
                    label="Entidad Donante"
                    item-text="entidad"
                    item-value="id"
                    return-object
                    clearable
                    :menu-props="{ closeOnClick: true }"
                  ></v-autocomplete>
                </v-col>

                <v-col cols="1" md="1" v-if="inventario.procedencia.id != 1">
                  <Entidad @saved="onSaveEntidad" ref="entidad" />
                  <v-btn
                    elevation="5"
                    class="mt-8"
                    text
                    icon
                    color="primary"
                    @click="mostrarModalEntidad()"
                    dark
                    :disabled="detalle"
                  >
                    <v-icon>mdi-plus-circle</v-icon>
                  </v-btn>
                </v-col>

                <v-col cols="6">
                  <v-text-field
                    :disabled="detalle"
                    append-icon="fas fa-money-bill-wave"
                    v-model="inventario.precio"
                    :rules="[reglas.precio]"
                    label="Precio"
                    prefix="$"
                  ></v-text-field>
                </v-col>

                <v-col cols="5">
                  <v-autocomplete
                    append-icon="fas fa-book-open"
                    @change="errors['rubro.id'] = []"
                    :error-messages="errors['rubro.id']"
                    :disabled="detalle"
                    v-model="inventario.rubro"
                    :items="rubros"
                    required
                    :rules="[(v) => !!v || 'El rubro es requerido']"
                    label="Rubros"
                    item-text="rubro"
                    item-value="id"
                    return-object
                    clearable
                    :menu-props="{ closeOnClick: true }"
                  ></v-autocomplete>
                </v-col>

                <v-col cols="1" md="1">
                  <Rubro @saved="onSavedRubro" ref="rubro" />
                  <v-btn
                    :disabled="detalle"
                    elevation="5"
                    class="mt-8"
                    text
                    icon
                    color="primary"
                    @click="mostrarModalRubro()"
                    dark
                  >
                    <v-icon>mdi-plus-circle</v-icon>
                  </v-btn>
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
                        :disabled="detalle"
                        v-model="inventario.fecha"
                        label="Fecha adquisición"
                        append-icon="fas fa-calendar-alt"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="inventario.fecha"
                      locale="es"
                      :date-format="date => $moment(date).format('DD-MM-YYYY')"
                      @input="menu = false"
                      :max="limitFecha"
                    ></v-date-picker>
                  </v-menu>
                </v-col>

                <v-col cols="5">
                  <v-autocomplete
                    @change="errors['ubicacion.id'] = []"
                    :error-messages="errors['ubicacion.id']"
                    :disabled="detalle"
                    append-icon="fas fa-map-marker-alt"
                    v-model="inventario.ubicacion"
                    :items="ubicaciones"
                    :rules="[(v) => !!v || 'Ubicación del activo es requerido']"
                    label="Ubicación"
                    item-text="ubicacion"
                    item-value="id"
                    return-object
                    clearable
                    :menu-props="{ closeOnClick: true }"
                  ></v-autocomplete>
                </v-col>

                <v-col cols="1" md="1">
                  <Ubicacion @saved="onSavedUbicacion" ref="ubicacion" />
                  <v-btn
                    required
                    :disabled="detalle"
                    elevation="5"
                    class="mt-8"
                    text
                    icon
                    color="primary"
                    @click="mostrarModalUbicacion()"
                    dark
                  >
                    <v-icon>mdi-plus-circle</v-icon>
                  </v-btn>
                </v-col>

                <v-col cols="12">
                  <v-textarea
                  append-icon="fas fa-file"
                    :disabled="detalle"
                    label="Observación"
                    v-model="inventario.observaciones"
                    no-resize
                    rows="1"
                    row-height="10"
                  ></v-textarea>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
        </v-card-text>

        <v-divider></v-divider>
        <v-card-actions v-if="!detalle">
          <div class="flex-grow-1"></div>
          <v-checkbox
            v-if="this.idPrueba != null && !detalle"
            v-model="inventario.guardarHistorial"
            class="mx-10"
            style="margin-top: 1.5rem"
            label="¿Desea guardar una copia de los cambios?"
          />
          <v-btn color="red darken-1" text @click="cancelar()">Cancelar</v-btn>
          <v-btn            
            ref="btnGuardarInventario"
            color="info darken-1"
            :disabled="!inventarioValido"
            @click="save()"
            v-text="textButton"
          ></v-btn>
        </v-card-actions>
      </v-card>

      <v-card v-if="detalle">
        <v-card-title>
          Historial de activo
          <div class="flex-grow-1"></div>
        </v-card-title>

        <v-container fluid>
          <v-form
            ref="formLogs"
            v-model="formularioBuscarCopias"
            :lazy-validation="true"
          >
            <v-row align="center" justify="space-around">
              <v-col cols="5">
                <v-menu
                  v-model="menuFechaInicioMovi"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="cpFechaInicioMovi"
                      label="Desde"
                      prepend-icon="mdi-calendar"
                      readonly                      
                      required
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    locale="es"
                    v-model="cpFechaInicioMovi"
                    @input="menuFechaInicioMovi = false"
                    :max="limitFecha"
                  ></v-date-picker>
                </v-menu>
              </v-col>

              <v-col cols="5">
                <v-menu
                  v-model="menuFechaFinalMovi"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="cpFechaFinalMovi"
                      label="Hasta"
                      prepend-icon="mdi-calendar"
                      readonly
                      required
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                  locale="es"
                    v-model="cpFechaFinalMovi"
                    @input="menuFechaFinalMovi = false"
                    :max="limitFecha"
                  ></v-date-picker>
                </v-menu>
              </v-col>

              <v-col class="2">
                <v-btn color="primary" @click="buscarMovimientosActivos">
                  Buscar
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-container>

        <v-data-table
          :headers="headerMovimiento"
          :items="historial"
          :footer-props="{
            'items-per-page-options': [10, 15, 25, 35, 45],
            'items-per-page-text': 'Registros Por Página',
          }"
          :items-per-page="10"
          :search="buscarMovimiento"
          multi-sort
          class="elevation-1"
        >
        </v-data-table>
      </v-card>

      <v-card v-if="detalle">
        <v-card-title>
          Copias antiguas del activo
          <div class="flex-grow-1"></div>
        </v-card-title>

        <v-container fluid>
          <v-form
            ref="fmdaasas"
            v-model="formularioBuscarCopias"
            :lazy-validation="true"
          >
            <v-row align="center" justify="space-around">
              <v-col cols="5">
                <v-menu
                  v-model="menuFechaInicio"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="cpFechaInicio"
                      label="Desde"
                      prepend-icon="mdi-calendar"
                      readonly
                      required
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    locale="es"
                    v-model="cpFechaInicio"
                    @input="menuFechaInicio = false"
                    :max="limitFecha"
                  ></v-date-picker>
                </v-menu>
              </v-col>

              <v-col cols="5">
                <v-menu
                  v-model="menuFechaFinal"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="cpFechaFinal"
                      label="Hasta"
                      prepend-icon="mdi-calendar"
                      readonly
                      required
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                  locale="es"
                    v-model="cpFechaFinal"
                    @input="menuFechaFinal = false"
                    :max="limitFecha"
                  ></v-date-picker>
                </v-menu>
              </v-col>

              <v-col class="2">
                <v-btn color="primary" @click="buscarCopias"> Buscar </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-container>

        <v-data-table
          :headers="headCopias"
          :items="copias"
          :footer-props="{
            'items-per-page-options': [10, 15, 25, 35, 45],
            'items-per-page-text': 'Registros Por Página',
          }"
          :items-per-page="10"
          :search="buscarCopia"
          multi-sort
          class="elevation-1"
        >
          <template v-slot:item.action="{ item }" v-slot:activator="{ on }">
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                  color="success"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="mostrarModalHistorial(item)"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>
              <span>Ver detalle</span>
            </v-tooltip>
          </template>

          <template v-slot:top>
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="modalHistorial" persistent max-width="700px">
                <v-card>
                  <v-card-title class="headline lighten-2" primary-titles>
                    <span class="headline" v-text="'Detalle de activo'"></span>
                  </v-card-title>

                  <v-card-text>
                    <v-container>
                      <v-form :lazy-validation="true">
                        <v-row>
                          <v-col cols="6">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="auxHistorial.procedencia.procedencia"
                              label="Procedencia"
                              required
                              :readonly="true"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="6">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="auxHistorial.entidad.entidad"
                              label="Entidad"
                              required
                              :readonly="true"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="6">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="auxHistorial.cuenta.cuenta"
                              label="Cuenta"
                              required
                              :readonly="true"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="6">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="auxHistorial.rubro.rubro"
                              label="Rubro"
                              required
                              :readonly="true"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="6">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="auxHistorial.ubicacion.ubicacion"
                              label="Ubicacion"
                              required
                              :readonly="true"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="6">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="auxHistorial.fecha_adquision"
                              label="Fecha de adquisión"
                              required
                              :readonly="true"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="6">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="auxHistorial.user.name"
                              label="Realizado por"
                              required
                              :readonly="true"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="6">
                            <v-text-field
                              append-icon="mdi-folder-outline"
                              v-model="auxHistorial.fechaRegistro"
                              label="Fecha de registro"
                              required
                              :readonly="true"
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
                      @click="ocultarModalHistorial()"
                    ></v-btn>
                  </v-card-actions>

                </v-card>
              </v-dialog>
            </v-toolbar>
          </template>
        </v-data-table>
      </v-card>
    </div>
  </div>
</template>

<script>
Vue.component("Entidad", require(".//Modals/Entidad.vue").default);
Vue.component("Cuenta", require(".//Modals/Cuenta.vue").default);
Vue.component("Marca", require(".//Modals/Marca.vue").default);
Vue.component("Rubro", require(".//Modals/Rubro.vue").default);
Vue.component("Ubicacion", require(".//Modals/Ubicacion.vue").default);

export default {
  name: "inventario-crear",
  data() {
    return {
      buscarMovimiento: "",
      detalle: false,
      historial: [],
      limitFecha: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),
      reglas: {
        requerido: (v) => !!v || "Campo requerido",
        min: (v) =>
          (v.length >= 2 && v.length <= 100) ||
          "Este campo debe ser mayor a 2 caracteres",
        expresion: (v) =>
          /^[A-Za-z0-9-ñáéíóúÁÉÍÓÚ \s]+$/g.test(v) ||
          "Este campo no puede tener caracteres especiales",
        precio: (v) =>
          v.length == 0 ||
          /^[0-9. \s]+$/g.test(v) ||
          "No parece formato de dinero",
        codigo: (v) => !! v &&         
          /^[0-9- \s]+$/g.test(v) ||
          "Código no valido",
        noRequerido: (v) =>
          v.length == 0 ||
          (v.length >= 2 && v.length <= 100) ||
          "Verifica el valor",
      },
      menu: false,
      idPrueba: 0,
      loader: false,
      inventarioValido: true,
      marcas: [],
      entidades: [],
      rubros: [],
      ubicaciones: [],
      cuentas: [],
      inventario: {
        id: null,
        codigo: "",
        serie: "",
        descripcion: "",
        modelo: "",
        observaciones: "",
        marca: { id: null, marca: "" },
        cuenta: { id: null, cuenta: "" },
        rubro: { id: null, rubro: "" },
        procedencia: { id: null, procedencia: "" },
        precio: 0.0,
        ubicacion: { id: null, ubicacion: "" },
        entidad: { id: null, entidad: "" },
        fecha: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),
        guardarHistorial: false,
      },
      errors: [],
      procedencias: [],
      headerMovimiento: [
        { text: "Campo", value: "campo" },
        { text: "Estado Anterior", value: "valor_anterior" },
        { text: "Estado Actual", value: "valor_nuevo" },
        { text: "Fecha registro", value: "created_at" },
      ],
      esperando: false,
      copias: [],
      headCopias: [
        { text: "Codigo", value: "codigo" },
        { text: "Descripcion", value: "descripcion" },
        { text: "Marca", value: "marca.marca" },
        { text: "Modelo", value: "modelo" },
        { text: "Serie", value: "serie" },
        { text: "Precio", value: "precio" },
        { text: "Ubicacion", value: "ubicacion.ubicacion" },
        { text: "Acciones", value: "action" },
      ],
      buscarCopia: "",

      menuFechaInicio: false,
      cpFechaInicio: "",
      menuFechaFinal: false,
      cpFechaFinal: "",
      formularioBuscarCopias: true,
      menuFechaInicioMovi: false,
      cpFechaInicioMovi: "",
      menuFechaFinalMovi: false,
      cpFechaFinalMovi: "",
      formularioBuscarMovimientos: true,

      // MODAL HISTORIAL INVENTARIO
      modalHistorial: false,
      auxHistorial: {
        entidad: { id: null, entidad: ''},
        procedencia : { id : null, procedencia: ''},
        cuenta: { id: null, cuenta: ''},
        rubro: { id: null, rubro: ''},
        ubicacion: { id: null, ubicacion: ''},
        fecha_adquision: '',
        user : { id: null, name : ''},
        fechaRegistro: ''
      }
    };
  },
  created() {
    let uri = window.location.search.substring(1);
    let params = new URLSearchParams(uri);

    this.idPrueba = params.has("id") ? params.get("id") : null;
    this.detalle = params.has("detalle")
      ? Boolean(params.get("detalle"))
      : false;

    if (this.detalle) {
      let fechaActual = new Date();
      this.cpFechaInicio = `${fechaActual.getFullYear()}-${fechaActual.getMonth()}-${fechaActual.getDate()}`;
      this.cpFechaFinal = `${fechaActual.getFullYear()}-${
        fechaActual.getMonth() + 1
      }-${fechaActual.getDate()}`;
      this.cpFechaInicioMovi = `${fechaActual.getFullYear()}-${fechaActual.getMonth()}-${fechaActual.getDate()}`;
      this.cpFechaFinalMovi = `${fechaActual.getFullYear()}-${
        fechaActual.getMonth() + 1
      }-${fechaActual.getDate()}`;
    }
  },
  mounted() {
    this.obtenerMarcar();
    this.obtenerCuentas();
    this.obtenerRubros();
    this.obtenerEntidades();
    this.obtenerUbicaciones();
    this.obtenerProcedencias();

    if (this.idPrueba != null) {
      this.obtenerActivo();
    }

    if (this.idPrueba != null && this.detalle) {
      this.buscarCopias();
      this.buscarMovimientosActivos();
    }
  },
  computed: {
    cardTitle() {
      return this.idPrueba != null
        ? this.idPrueba != null && this.detalle
          ? "Detalle del activo"
          : "Actualizar Inventario"
        : "Guardar Inventario";
    },
    textButton() {
      return this.idPrueba != null ? "Actualizar" : "Guardar";
    },
  },
  methods: {
    obtenerActivo() {
      this.loader = true;
      axios
        .get(`/Api/inventario/${this.idPrueba}/activos`)
        .then(({ data: { activo } }) => {
          let {
            id,
            codigo,
            descripcion,
            fecha_adquision,
            precio,
            serie,
            modelo,
            ubicacion,
            entidad,
            rubro,
            marca,
            procedencia,
            cuenta,
            observacion,
          } = { ...activo };

          this.inventario.id = id;
          this.inventario.codigo = codigo;
          this.inventario.serie = serie ?? "";
          this.inventario.descripcion = descripcion;
          this.inventario.modelo = modelo ?? "";
          this.inventario.precio = precio ?? "";
          this.inventario.fecha = fecha_adquision;
          this.inventario.observaciones = observacion ?? "";

          this.inventario.procedencia = procedencia;
          this.inventario.entidad = entidad ?? { id: null, entidad: "" };
          this.inventario.marca = marca ?? { id: null, marca: "" };
          this.inventario.rubro = rubro;
          this.inventario.cuenta = cuenta;
          this.inventario.ubicacion = ubicacion;

          //console.log(historial);
          //this.historial = [...historial];
          //this.copias = [...respaldos];
          this.loader = false;
        })
        .catch(console.error);
    },
    obtenerProcedencias() {
      axios
        .get(`/Api/procedencias`)
        .then(({ data: { procedencias } }) => {
          this.procedencias = [ ... procedencias];
        })
        .catch(console.error);
    },
    obtenerMarcar() {
      axios
        .get(`/Api/marcas`)
        .then(({ data: { marcas } }) => {
          this.marcas = marcas.filter( r => r.eliminado == false);
        })
        .catch(console.error);
    },
    obtenerEntidades() {
      axios
        .get(`/Api/entidades`)
        .then(({ data: { entidades } }) => {
          this.entidades = entidades.filter((r) => r.eliminado == false);
        })
        .catch(console.error);
    },
    obtenerCuentas() {
      axios
        .get(`/Api/cuentas`)
        .then(({ data: { cuentas } }) => {
          this.cuentas = cuentas.filter((r) => r.eliminado == false);
        })
        .catch(console.error);
    },
    obtenerRubros() {
      axios
        .get(`/Api/rubros`)
        .then(({ data: { rubros } }) => {
          this.rubros = rubros.filter((r) => r.eliminado == false);
        })
        .catch(console.error);
    },
    obtenerUbicaciones() {
      axios
        .get("/Api/ubicaciones")
        .then(({ data: { ubicaciones } }) => {
          this.ubicaciones = ubicaciones.filter((r) => r.eliminado == false);
        })
        .catch(console.error);
    },
    save() {
      Swal.fire({
        title: "¡Importante!",
        text: "Estas seguro/as que los datos son correctos ¿Deseas guardarlo?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Guardar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          this.loader = true;
          const path =
            this.idPrueba != null
              ? `/Api/inventario/${this.idPrueba}/edit`
              : `/Api/inventario/save`;
          axios
            .post(path, this.inventario)
            .then((response) => {
              this.loader = false;
              if (response.status == 200) {
                const { respuesta, mensaje } = response.data;

                if (respuesta) {
                  this.alerta(mensaje, "success", "Bien hecho");

                  if (this.idPrueba != null) {
                    window.location = "/inventario/index";
                  } else {
                    Swal.fire({
                      title: "INFORMACION",
                      text: `Deseas agregar un nuevo producto al inventario`,
                      icon: "info",
                      showCancelButton: true,
                      confirmButtonColor: "#3698e3",
                      cancelButtonColor: "#d33",
                      confirmButtonText: "Si",
                      cancelButtonText: "No",
                      allowOutsideClick: false,
                    }).then((result) => {
                      if (result.isConfirmed) {
                        this.inventario = {
                          codigo: "",
                          serie: "",
                          descripcion: "",
                          modelo: "",
                          observaciones: "",
                          marca: { id: null, marca: "" },
                          cuenta: { id: null, cuenta: "" },
                          rubro: { id: null, rubro: "" },
                          procedencia: { id: null, procedencia: "" },
                          precio: 0.0,
                          ubicacion: { id: null, ubicacion: "" },
                          entidad: { id: null, entidad: "" },
                        };

                        this.$refs.formInventario.resetValidation();
                      } else {
                        window.location = "/inventario/index";
                      }
                    });
                  }
                } else {
                  const { errors } = response.data;
                  this.errors = errors;
                }
              }
            })
            .catch(() => {
              this.loader = false;
              this.alerta("Ocurrio un error en el sistema");
            });
        }
      });
    },
    cancelar() {      
      Swal.fire({
        title: "INFORMACION",
        text: `¿Quieres cancelar el registro`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3698e3",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
        cancelButtonText: "No",
      }).then((result) => {
        if (result.isConfirmed) {
          window.location = "/inventario/index";
        }
      });
    },
    alerta(mensaje, icono = "info", titulo = "") {
      Swal.fire({
        position: "top-end",
        icon: icono,
        title: titulo,
        text: mensaje,
        showConfirmButton: false,
        timer: 1500,
      });
    },
    mostrarModalEntidad() {
      this.$refs.entidad.mostrarModal();
    },
    mostrarModalCuenta() {
      this.$refs.cuenta.mostrarModal();
    },
    mostrarModalMarca() {
      this.$refs.marca.mostrarModal();
    },
    mostrarModalRubro() {
      this.$refs.rubro.mostrarModal();
    },
    mostrarModalUbicacion() {
      this.$refs.ubicacion.mostrarModal();
    },
    onSavedCuenta(value) {
      if (value) {
        this.cuentas.push(value);
      }
    },
    onSaveEntidad(value) {
      if (value) {
        console.log(value);
        this.entidades.push({ ...value });
      }
    },
    onSavedMarca(value) {
      if (value) {
        this.marcas.push({ ...value });
      }
    },
    onSavedRubro(value) {
      if (value) {
        this.rubros.push({ ...value });
      }
    },
    onSavedUbicacion(value) {
      if (value) {
        this.ubicaciones.push({ ...value });
      }
    },
    buscarCopias() {
      axios
        .get(
          `/Api/historial/respaldos/${this.idPrueba}/${this.cpFechaInicio}/${this.cpFechaFinal}`
        )
        .then(({ data: { historial } }) => {
          console.log(historial);
          this.copias = [...historial];
        })
        .catch(console.error);
    },
    buscarMovimientosActivos() {
      console.log({
        inicio: this.cpFechaInicioMovi,
        final: this.cpFechaFinalMovi,
      });
      axios
        .get(
          `/Api/historial/movimientos/${this.idPrueba}/${this.cpFechaInicioMovi}/${this.cpFechaFinalMovi}`
        )
        .then(({ data: { historial } }) => {
          console.log(historial);
          this.historial = historial;
        })
        .catch(console.error);
    },
    onChangeProcedencia() {      
      errors['procedencia.id'] = []
      this.inventario.cuenta = { id: null, cuenta: null }
      this.inventario.entidad = { id: null, entidad: null }
    },
    mostrarModalHistorial( activo )
    {
      console.log( activo.created_at )
    
      this.auxHistorial.entidad = { ...activo.entidad }
      this.auxHistorial.procedencia = {...activo.procedencia }
      this.auxHistorial.cuenta = { ...activo.cuenta }
      this.auxHistorial.fecha_adquision = activo.fecha_adquision
      this.auxHistorial.rubro = { ...activo.rubro}
      this.auxHistorial.ubicacion = { ...activo.ubicacion }
      this.auxHistorial.user = {... activo.user }
      this.auxHistorial.fechaRegistro = activo.created_at

      this.modalHistorial = true
    },
    ocultarModalHistorial() {
      this.modalHistorial = false;
      this.auxHistorial = {
        entidad: { id: null, entidad: ''},
        procedencia : { id : null, procedencia: ''},
        cuenta: { id: null, cuenta: ''},
        rubro: { id: null, rubro: ''},
        ubicacion: { id: null, ubicacion: ''},
        fecha_adquision: '',
        user : { id: null, name : ''},
        fechaRegistro: ''
      }
    },
  },
};
</script>

<template>
    <div class="content">
        <div
            class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">

                <v-overlay :value="loader" :z-index="'99999999'">
                    <v-progress-circular
                        indeterminate
                        size="80"
                        color="grey darken-4"
                    ></v-progress-circular>
                </v-overlay>

                <v-card>
                    <v-card-text>
                        <v-container>
                            <v-form ref="formInventario"
                                v-model="validForm"
                                :lazy-validation="true">
                                    <v-row>
                                        <v-col cols="6">
                                            <v-text-field
                                                append-icon="fas fa-barcode"
                                                v-model="inventario.codigo"
                                                @keyup="errorsNombre = []"
                                                :rules="[
                                                    v => !!v || 'Codigo Es Requerido'
                                                ]"
                                                label="Código"
                                                required
                                                :error-messages="errorsNombre"
                                            ></v-text-field>
                                        </v-col>

                                        <v-col cols="6">
                                            <v-text-field
                                                    append-icon=""
                                                    v-model="inventario.Serie"
                                                    @keyup="errorsNombre = []"
                                                    label="Serie"
                                                    required
                                                    :error-messages="errorsNombre"
                                            ></v-text-field>
                                        </v-col>

                                        <v-col cols="12">
                                            <v-textarea
                                                v-model="inventario.descripcion"
                                                label="Descripcion"
                                                rows="2"
                                                required
                                                :error-messages="errorsNombre"
                                            ></v-textarea>
                                        </v-col>

                                        <v-col cols="5">
                                            <v-autocomplete
                                                v-model="inventario.marca"
                                                :items="marcas"
                                                required
                                                label="Marca"
                                                item-text="nombre"
                                                item-value="id"
                                                aling="center"
                                                return-object
                                                clearable
                                                :menu-props="{ closeOnClick: true }"
                                            ></v-autocomplete>
                                        </v-col>

                                        <v-col cols="6">
                                            <v-text-field
                                                append-icon=""
                                                v-model="inventario.modelo"
                                                @keyup="errorsNombre = []"
                                                label="Modelo"
                                                required
                                                :error-messages="errorsNombre"
                                            ></v-text-field>
                                        </v-col>

                                        <v-col cols="5">
                                            <v-select
                                                :items="procedencias"
                                                v-model="inventario.procedencia"
                                                item-text="nombre"
                                                item-value="procedencia"
                                                label="Procedencia"
                                            ></v-select>
                                        </v-col>

                                        <v-col cols="5">
                                            <v-autocomplete
                                                v-model="inventario.entidad"
                                                :items="entidades"
                                                required
                                                :rules="[ v => !!v || 'Tipo entidad del activo es requerido']"
                                                label="Entidad"
                                                item-text="entidad"
                                                item-value="id"
                                                return-object
                                                clearable
                                                :menu-props="{ closeOnClick: true }"
                                            ></v-autocomplete>
                                        </v-col>

                                        <v-col cols="5">
                                            <v-text-field
                                                append-icon="fas fa-tags"
                                                v-model="inventario.precio"
                                                @keyup="errorsNombre = []"
                                                label="Precio"
                                                required
                                                :error-messages="errorsNombre"
                                            ></v-text-field>
                                        </v-col>
                                        

                                        <v-col cols="5">
                                            <v-autocomplete
                                                v-model="inventario.cuenta"
                                                :items="cuentas"
                                                required
                                                :rules="[ v => !!v || 'Tipo entidad del activo es requerido']"
                                                label="Cuenta"
                                                item-text="cuenta"
                                                item-value="id"
                                                return-object
                                                clearable
                                                :menu-props="{ closeOnClick: true }"
                                            ></v-autocomplete>
                                        </v-col>

                                        <v-col cols="5">
                                            <v-autocomplete
                                                v-model="inventario.rubros"
                                                :items="rubros"
                                                required
                                                :rules="[ v => !!v || 'Tipo entidad del activo es requerido']"
                                                label="Rubros"
                                                item-text="rubro"
                                                item-value="id"
                                                return-object
                                                clearable
                                                :menu-props="{ closeOnClick: true }"
                                            ></v-autocomplete>
                                        </v-col>

                                        <v-col cols="5">
                                            <v-autocomplete
                                                append-icon="fas fa-map-marker-alt"
                                                v-model="inventario.ubicacion"
                                                :items="ubicaciones"
                                                required
                                                :rules="[ v => !!v || 'Ubicación del activo es requerido']"
                                                label="Ubicación"
                                                item-text="nombre"
                                                item-value="id"
                                                return-object
                                                clearable
                                                :menu-props="{ closeOnClick: true }"
                                            ></v-autocomplete>
                                        </v-col>

                                        <v-col cols="12">
                                            <v-textarea
                                                label="Observación"
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
                    <v-card-actions>
                        <div class="flex-grow-1"></div>
                        <v-btn
                            color="red darken-1"
                            text
                            >Cancelar</v-btn>
                        <v-btn
                            color="info darken-1"
                            :disabled="false"
                            text
                        >Guardar</v-btn>
                    </v-card-actions>
                </v-card>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loader: false,
            validForm: false,
            marcas: [], entidades: [], rubros: [], ubicaciones: [],
            cuentas: [],
            inventario: {
                codigo: '', serie: '', descripcion: '', modelo: '', marca: { id: null, marca: '' }, cuenta: { id: null, cuenta: ''},
                rubro: { id: null, rubro: '' }, procedencia: { id: null, procedencia: ''}, precio: 0.00, ubicacion: {id: null, ubicacion: ''},
                entidad: { id: null, entidad: '' }
            },
            errorsNombre: [],
            procedencias: [
                { procedencia: 1, nombre: 'COMPRA'},
                { procedencia: 2, nombre: 'DONACION'},
            ]
        }
    },
    mounted ()
    {
        this.obtenerMarcar();
        this.obtenerCuentas();
        this.obtenerRubros();
        this.obtenerEntidades();
    }, 
    methods: {
        obtenerMarcar () {
            axios.get(`/marcas/list?type=A`)
                .then( ( {data: marcas} ) => {
                    console.log(marcas);
                    this.marcas = marcas;
                }).catch(console.error)
        },
        obtenerEntidades() {
            axios.get(`/Api/entidades`)
                .then( ( { data: { entidades }} ) => {
                    console.log(entidades);
                    this.entidades = entidades;
                }).catch(console.error)
        },
        obtenerCuentas() {
            axios.get(`/Api/cuentas`)
                .then( ( { data: { cuentas }} ) => {
                    console.log(cuentas);
                    this.cuentas = cuentas;
                }).catch(console.error)
        },
        obtenerRubros () {
            axios.get(`/Api/rubros`)
                .then( ( { data: { rubros }} ) => {
                    console.log(rubros);
                    this.rubros = rubros;
                }).catch(console.error)
        }
    }
}
</script>

<style>

</style>
<template>
    <div>
        <h2 class="text-center mb-10">Promociones</h2>
        <div class="text-end my-5">
            <v-btn variant="tonal" color="primary" @click="card = true, cardAction = 'nuevo'"><i
                    class="fa-solid fa-plus"></i> Nuevo</v-btn>
        </div>
        <v-text-field variant="outlined" label="buscar" v-model="search"></v-text-field>
        <v-data-table :headers="headers" :items="items" :search="search">
            <template v-slot:[`item.lote_productos.precio`]="{ item }">
                {{ formato_numero(item.lote_productos.precio) }}
            </template>
            <template v-slot:[`item.lote_productos.cantidad_restante`]="{ item }">
                {{ formato_numero(item.lote_productos.precio - item.promocion.descuento) }}
            </template>
            <template v-slot:[`item.promocion.descuento`]="{ item }">
                Descuento: <b>{{ formato_numero(item.promocion.descuento) }}</b> Porcentaje: <b>{{ Math.round((item.promocion.descuento*100)/item.lote_productos.precio, 0) }}%</b>
            </template>
            <template v-slot:[`item.created_at`]="{ item }">
                {{ new Date(item.created_at).toLocaleString() }}
            </template>
            <template v-slot:[`item.updated_at`]="{ item }">
                {{ new Date(item.updated_at).toLocaleString() }}
            </template>
            <template v-slot:[`item.actions`]="{ item }">
                <v-btn color="secondary" class="me-2" variant="tonal" @click="editar(item)"><i
                        class="fa-solid fa-pen"></i></v-btn>
                <v-btn color="red" variant="tonal" @click="dialogEliminar=true, dataSave.id = item.id"><i class="fa-solid fa-trash"></i></v-btn>
            </template>
        </v-data-table>
    </div>

    <v-dialog v-model="card" persistent>
        <v-card width="1000" class="mx-auto">
            <v-card-title class="text-center">{{ cardAction == 'nuevo' ? 'Registro de Categorías' : 'Actualizar Categoría' }}</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12">
                        <v-autocomplete label="Seleccione producto" variant="outlined"
                            :items="itemsProductos" item-title="nombre" item-value="id"
                            v-model="dataSave.lote" :error-messages="errors.lote" ></v-autocomplete>
                        <v-text-field label="Descuento en Quetzales" v-model="dataSave.descuento"
                            :error-messages="errors.descuento" variant="outlined"></v-text-field>
                        <v-text-field type="date" label="Fecha inicio" v-model="dataSave.fecha_inicio"
                                :error-messages="errors.fecha_inicio" variant="outlined"></v-text-field>
                        <v-text-field type="date" label="Fecha fin" v-model="dataSave.fecha_fin"
                                :error-messages="errors.fecha_fin" variant="outlined"></v-text-field>
                        <v-textarea label="Descripción"
                                v-model="dataSave.descripcion" variant="outlined"></v-textarea>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-actions>
                <v-btn color="secondary" v-if="cardAction=='nuevo'" variant="tonal" @click="guardar">Guardar</v-btn>
                <v-btn color="secondary" v-if="cardAction=='editar'" variant="tonal" @click="guardarCambios">Guardar Cambios</v-btn>
                <v-btn color="red" variant="tonal" @click="card = false, limpiar()">Cancelar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-overlay :model-value="overlay" persistent style="background-color: black; opacity: 0.8;"
        class="align-center justify-center">
        <v-card style="background-color: transparent; border: none;" flat>
            <v-card-text>

                <h1 class="text-center text-white"> Cargando datos </h1>
            </v-card-text>
            <v-card-actions class="justify-center aling-center">

                <v-progress-circular color="success" indeterminate size="64"></v-progress-circular>
            </v-card-actions>
        </v-card>
    </v-overlay>

    <v-dialog v-model="dialogEliminar" max-width="500" persistent>
        <v-card>
            <v-card-title class="headline">Confirmar acción</v-card-title>
            <v-card-text>¿Estás seguro de que deseas eliminar el dato?</v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="green darken-1" text @click="eliminar">Confirmar</v-btn>
                <v-btn color="red darken-1" text @click="dialogEliminar = false">Cancelar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
export default {
    name: 'promocionesVue',
    data() {
        return {
            headers: [
                { title: 'Producto', key: 'lote_productos.productos.nombre', align: 'start' },
                { title: 'Precio Original', key: 'lote_productos.precio', align: 'start' },
                { title: 'Precio con Descuento', key: 'lote_productos.cantidad_restante', align: 'start' },
                { title: 'Descripción', key: 'promocion.descripcion', align: 'start' },
                { title: 'Descuento', key: 'promocion.descuento', align: 'start' },
                { title: 'Fecha Inicio', key: 'promocion.fecha_inicio', align: 'start' },
                { title: 'Fecha Fin', key: 'promocion.fecha_fin', align: 'start' },
                { title: 'No. Lote', key: 'id', align: 'start' },
                { title: 'Creado', key: 'created_at', align: 'center' },
                { title: 'Última actualización', key: 'updated_at', align: 'center' },
                { title: 'Opciones', key: 'actions', align: 'center' },
            ],
            items: [],
            itemsProductos: [],
            card: false,
            cardAction: '',
            search: '',
            dataSave: {
                id: '',
                descuento: '',
                descripcion: '',
                fecha_inicio: '',
                fecha_fin: '',
                lote: null
            },
            errors: {},
            overlay: false,
            dialogEliminar: false,
        }
    },
    methods: {
        getData() {
            this.overlay = true;
            axios.get("/promociones").then(res => {
                this.overlay = false;
                this.items = res.data;
                this.getProductos();
            }).catch((err) => {
                this.overlay = false;
                Swal.fire({
                    title: '¡Hubo un error al obtener datos!',
                    icon: 'error',
                    allowOutsideClick: false,
                    confirmButtonColor: '#00A38C',
                    customClass: {
                        confirmButton: 'custom-confirm-button',  // Clase personalizada
                    }
                });
            });
        },
        getProductos() {
            this.overlay = true;
            axios.get("/loteProductosPromociones").then(res => {
                this.overlay = false;
                this.itemsProductos = res.data;
            }).catch((err) => {
                this.overlay = false;
                Swal.fire({
                    title: '¡Hubo un error al obtener datos!',
                    icon: 'error',
                    allowOutsideClick: false,
                    confirmButtonColor: '#00A38C',
                    customClass: {
                        confirmButton: 'custom-confirm-button',
                    }
                });
            });
        },
        limpiar(){
            this.dataSave.id = null;
            this.dataSave.descuento = '';
            this.dataSave.descripcion = '';
            this.dataSave.fecha_inicio = '';
            this.dataSave.fecha_fin = '';
            this.dataSave.lote = null;
            this.errors = {};
        },
        guardar() {
            this.overlay = true;
            axios.post("/promociones", {
                descuento: this.dataSave.descuento,
                descripcion: this.dataSave.descripcion,
                fecha_inicio: this.dataSave.fecha_inicio,
                fecha_fin: this.dataSave.fecha_fin,
                lote: this.dataSave.lote,
            }).then(res => {
                this.overlay = false;
                toast.success("Se han guardado los datos", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                this.limpiar();
                this.card = false;
                this.getData();
            }).catch((error) => {
                this.overlay = false;
                toast.error("¡Hubo un error al guardar los datos!", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    // Manejo de otros errores o código adicional
                }
            });
        },
        editar(item) {
            this.card = true;
            this.cardAction = 'editar';
            this.dataSave.descuento = item.promocion.descuento;
            this.dataSave.descripcion = item.promocion.descripcion;
            this.dataSave.fecha_inicio = item.promocion.fecha_inicio;
            this.dataSave.fecha_fin = item.promocion.fecha_fin;
            this.dataSave.lote = item.lote_productos.id;
            this.dataSave.id = item.id;
        },
        guardarCambios(){
            this.overlay = true;
            axios.put(`/promociones/${this.dataSave.id}`,{
                descuento: this.dataSave.descuento,
                descripcion: this.dataSave.descripcion,
                fecha_inicio: this.dataSave.fecha_inicio,
                fecha_fin: this.dataSave.fecha_fin,
                lote: this.dataSave.lote,
            }).then(res => {
                this.overlay = false;
                toast.success("Se han guardado los datos", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                this.limpiar();
                this.card = false;
                this.getData();
            }).catch((error) => {
                this.overlay = false;
                toast.error("¡Hubo un error al guardar cambios a los datos!", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    // Manejo de otros errores o código adicional
                }
            });
        },
        eliminar() {
            this.overlay = true;
            axios.delete(`/categorias/${this.dataSave.id}`).then(res=>{
                this.overlay = false;
                toast.success("Se han guardado los datos", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                this.dialogEliminar = false;
                this.getData();
            }).catch((error) => {
                this.overlay = false;
                toast.error("¡Hubo un error al eliminar los datos!", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    // Manejo de otros errores o código adicional
                }
            });
        },
        formato_numero(amount) {
            var newAmount = new Intl.NumberFormat("es-GT", { style: "currency", currency: "GTQ", }).format(amount);
            return newAmount;
        },
    },
    mounted() {
        this.getData();
    }
}
</script>
<style>
.custom-confirm-button {
    color: #ffffff;
}
</style>
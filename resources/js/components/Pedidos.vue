<template>
    <div>
        <h2 class="text-center mb-10">Pedidos</h2>
        <div class="text-end my-5">
            <v-btn variant="tonal" color="primary" @click="card = true, cardAction = 'nuevo'"><i
                    class="fa-solid fa-plus"></i> Nuevo</v-btn>
        </div>
        <v-text-field variant="outlined" label="buscar" v-model="search"></v-text-field>
        <v-data-table :headers="headers" :items="itemsData" :search="search">
            <template v-slot:[`item.created_at`]="{ item }">
                {{ new Date(item.created_at).toLocaleString() }}
            </template>
            <template v-slot:[`item.estado`]="{ item }">
                {{ item.estado==1 ? 'Pendiente' : 'Confirmado' }}
            </template>
            <template v-slot:[`item.updated_at`]="{ item }">
                {{ new Date(item.updated_at).toLocaleString() }}
            </template>
            <template v-slot:[`item.actions`]="{ item }">
                <v-btn color="secondary" class="me-2" variant="tonal" @click="viewDetalle(item)"><i class="fa-solid fa-eye"></i></v-btn>
                <v-btn v-if="item.estado==1" color="red" variant="tonal" class="me-2" @click="dialogEliminar=true, dataSave.id = item.id"><i
                        class="fa-solid fa-trash"></i></v-btn>
                <v-btn v-if="item.estado==1" color="primary" variant="tonal" @click="dialogAprobar=true, pedidoId = item.id"><i class="fa-solid fa-square-check"></i></v-btn>
            </template>
        </v-data-table>
    </div>

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
    <v-dialog v-model="dialogAprobar" max-width="500" persistent>
        <v-card>
            <v-card-title class="headline">Confirmar acción</v-card-title>
            <v-card-text>¿Estás seguro de confirmar la venta del pedido?</v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="green darken-1" text @click="aprobar">Confirmar</v-btn>
                <v-btn color="red darken-1" text @click="dialogAprobar = false">Cancelar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="cardDetalle">
        <v-card width="1000" class="mx-auto">
            <v-card-title>Detalle del Pedido</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12">
                        <p><b>Pedido No:</b> {{ itemDetalle.id }}</p>
                        <p><b>Fecha compra:</b> {{ new Date(itemDetalle.created_at).toLocaleString() }}</p>
                        <p><b>Cliente:</b> {{ itemDetalle.cliente.name }}</p>
                        <p><b>Telefono:</b> {{ itemDetalle.cliente.telefono==null ? 'sin datos' : itemDetalle.cliente.telefono}}</p>
                        <p><b>NIT:</b> {{ itemDetalle.cliente.nit==null ? 'sin datos' : itemDetalle.cliente.nit}}</p>
                        <p><b>Correo:</b> {{ itemDetalle.cliente.email==null ? 'sin datos' : itemDetalle.cliente.email}}</p>
                        <p><b>Dirección:</b> {{ itemDetalle.cliente.direccion==null ? 'sin datos' : itemDetalle.cliente.direccion}}</p>
                    </v-col>
                    <v-col cols="12">
                        <v-table>
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Sub-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="detalle in itemDetalle.detalle_pedido" :key="detalle.index">
                                    <td>{{ detalle.producto.nombre }}</td>
                                    <td>{{ formato_numero(detalle.precio_unitario) }}</td>
                                    <td>{{ detalle.cantidad }}</td>
                                    <td>{{ formato_numero(detalle.subtotal) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3"><b style="color: red;">Total</b></td>
                                    <td><b style="color: red;">{{ formato_numero(itemDetalle.total) }}</b></td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-col>
                </v-row>
                <v-row id="tabla-detalle" v-show="false">
                    <v-col cols="12">
                        <p><b>Pedido No:</b> {{ itemDetalle.id }}</p>
                        <p><b>Fecha compra:</b> {{ new Date(itemDetalle.created_at).toLocaleString() }}</p>
                        <p><b>Cliente:</b> {{ itemDetalle.cliente.name }}</p>
                        <p><b>Telefono:</b> {{ itemDetalle.cliente.telefono==null ? 'sin datos' : itemDetalle.cliente.telefono}}</p>
                        <p><b>NIT:</b> {{ itemDetalle.cliente.nit==null ? 'sin datos' : itemDetalle.cliente.nit}}</p>
                        <p><b>Correo:</b> {{ itemDetalle.cliente.email==null ? 'sin datos' : itemDetalle.cliente.email}}</p>
                        <p><b>Dirección:</b> {{ itemDetalle.cliente.direccion==null ? 'sin datos' : itemDetalle.cliente.direccion}}</p>
                    </v-col>
                    <v-col cols="12">
                        <v-table density="compact">
                            <thead>
                                <tr>
                                    <th style="font-weight: bold; border-bottom: solid 1px black !important;">Producto</th>
                                    <th style="font-weight: bold; border-bottom: solid 1px black !important;">Precio</th>
                                    <th style="font-weight: bold; border-bottom: solid 1px black !important;">Cantidad</th>
                                    <th style="font-weight: bold; border-bottom: solid 1px black !important;">Sub-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="detalle in itemDetalle.detalle_pedido" :key="detalle.index">
                                    <td style="border-bottom: solid 1px gray !important;">{{ detalle.producto.nombre }}</td>
                                    <td style="border-bottom: solid 1px gray !important;">{{ formato_numero(detalle.precio_unitario) }}</td>
                                    <td style="border-bottom: solid 1px gray !important;">{{ detalle.cantidad }}</td>
                                    <td style="border-bottom: solid 1px gray !important;">{{ formato_numero(detalle.subtotal) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="border-bottom: solid 1px gray !important;"><b style="color: red;">Total</b></td>
                                    <td style="border-bottom: solid 1px gray !important;"><b style="color: red;">{{ formato_numero(itemDetalle.total) }}</b></td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-actions>
                <v-btn color="secondary" variant="tonal"  @click="descargarPDF('tabla-detalle')">Imprimir</v-btn>
                <v-btn color="red" variant="tonal" @click="cardDetalle = false">Cancelar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
export default {
    name: 'pedidosVue',
    data() {
        return {
            headers: [
                { title: 'No. pedido', key: 'id', align: 'start' },
                { title: 'Fecha compra', key: 'created_at', align: 'start' },
                { title: 'Cliente', key: 'cliente.name', align: 'start' },
                { title: 'Total', key: 'total', align: 'start' },
                { title: 'Estado', key: 'estado', align: 'start' },
                { title: 'Última actualización', key: 'updated_at', align: 'center' },
                { title: 'Opciones', key: 'actions', align: 'center' },
            ],
            headers2: [
                { title: 'Producto', key: 'producto.nombreAux', align: 'start' },
                { title: 'Precio', key: 'precio', align: 'start' },
                { title: 'Cantidad', key: 'cantidad', align: 'start' },
                { title: 'Sub-total', key: 'subtotal', align: 'start' },
                { title: 'Opciones', key: 'actions', align: 'center' },
            ],
            itemsData: [],
            card: false,
            cardAction: '',
            search: '',
            dataSave: {
                id: '',
                total: 0,
                cliente: null,
            },
            errors: {},
            overlay: false,
            dialogEliminar: false,
            itemsClientes: [],
            cardCliente: false,
            dataSave2: {
                id: '',
                nombre: '',
                email: '',
                direccion: '',
                nit: '',
                telefono: '',
            },
            dataSaveDetalle: {
                producto: null,
                cantidad: '',
                id: null,
            },
            errors2: {},
            itemsDetalle: [],
            itemsProductos: [],
            numeroRules: [
                v => !!v || 'El campo es requerido',
                v => Number.isInteger(Number(v)) || 'Debe ser un número entero',
            ],
            requiredRules: [
                v => !!v || 'El campo es requerido',
            ],
            index: 0,
            total: 0,
            cardDetalle: false,
            itemDetalle: [],
            dialogAprobar: false,
            pedidoId: null,
        }
    },
    methods: {
        getData() {
            this.overlay = true;
            axios.get("/pedidos").then(res => {
                this.overlay = false;
                this.itemsData = res.data;
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
        limpiar(){
            this.dataSave.id = '';
            this.dataSave.total = 0;
            this.dataSave.proveedor = '';
            this.errors = {};
            this.itemsDetalle = [];
            this.total = 0;
        },
        limpiar2(){
            this.dataSave2.nombre = '';
            this.dataSave2.contacto = '';
            this.dataSave2.direccion = '';
            this.dataSave2.telefono = '';
            this.dataSave2.email = '';
            this.dataSave2.id = null;
            this.errors2 = {};
        },
        guardar() {
            this.overlay = true;
            axios.post("/pedidos", {
                cliente: this.dataSave.cliente,
                total: this.total,
                carrito: this.itemsDetalle,
            }).then(res => {
                this.overlay = false;
                toast.success("Se han guardado los datos", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                this.limpiar();
                this.limpiar2();
                this.card = false;
                this.getData();
                this.getProductos();
            }).catch((error) => {
                this.overlay = false;
                toast.error("¡Hubo un error al guardar los datos!", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                    if (this.errors.carrito) {
                        this.card = false;
                        Swal.fire({
                            title: '¡Para guardar una compra debes agregar productos!',
                            icon: 'warning',
                            allowOutsideClick: false,
                            confirmButtonColor: '#00A38C',
                            customClass: {
                                confirmButton: 'custom-confirm-button',
                            }
                        }).then(response => {
                            this.card = true;
                        })
                    }
                } else {
                    // Manejo de otros errores o código adicional
                }
            });
        },
        editar(item) {
            
        },
        guardarCambios(){
            
        },
        eliminar() {
            this.overlay = true;
            axios.delete(`/pedidos/${this.dataSave.id}`).then(res=>{
                this.overlay = false;
                toast.success("Se han guardado los cambios", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                this.dialogEliminar = false;
                this.getData();
                this.getProductos();
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
        
        viewDetalle(item){
            this.cardDetalle = true;
            console.log(item);
            this.itemDetalle = item;
        },
        async descargarPDF(id) {
            this.imprimir = true;
            this.$htmlToPaper(id)

            this.$nextTick(() => {
                this.imprimir = false;
            })
        },
        aprobar(){
            this.overlay = true;
            axios.post("/categorias", {
                nombre: this.dataSave.nombre,
                descripcion: this.dataSave.descripcion,
            }).then(res => {
                this.overlay = false;
                toast.success("Se han guardado los datos", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                this.limpiar();
                this.card = false;
                this.getCategorias();
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
        }
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
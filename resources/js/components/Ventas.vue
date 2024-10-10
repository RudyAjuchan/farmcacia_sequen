<template>
    <div>
        <h2 class="text-center mb-10">Ventas</h2>
        <div class="text-end my-5">
            <v-btn variant="tonal" color="primary" @click="card = true, cardAction = 'nuevo'"><i
                    class="fa-solid fa-plus"></i> Nuevo</v-btn>
        </div>
        <v-text-field variant="outlined" label="buscar" v-model="search"></v-text-field>
        <v-data-table :headers="headers" :items="itemsData" :search="search">
            <template v-slot:[`item.created_at`]="{ item }">
                {{ new Date(item.created_at).toLocaleString() }}
            </template>
            <template v-slot:[`item.updated_at`]="{ item }">
                {{ new Date(item.updated_at).toLocaleString() }}
            </template>
            <template v-slot:[`item.actions`]="{ item }">
                <v-btn color="secondary" class="me-2" variant="tonal" @click="viewDetalle(item)"><i class="fa-solid fa-eye"></i></v-btn>
                <v-btn color="red" variant="tonal" @click="dialogEliminar=true, dataSave.id = item.id"><i
                        class="fa-solid fa-trash"></i></v-btn>
            </template>
        </v-data-table>
    </div>

    <v-dialog v-model="card" persistent>
        <v-card width="1100" class="mx-auto">
            <v-card-title class="text-center">{{ cardAction == 'nuevo' ? 'Registro de ventas' : 'Actualizar venta'
                }}</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12">
                        <v-autocomplete variant="outlined" label="Seleccione el cliente" v-model="dataSave.cliente"
                            :items="itemsClientes" item-title="name" item-value="id"
                            :error-messages="errors.cliente" append-inner-icon="mdi-plus"
                            @click:append-inner="card=false, cardCliente=true"></v-autocomplete>
                    </v-col>
                    <v-col cols="12">
                        <v-card>
                            <v-card-title>
                                <h5>Detalle de venta</h5>
                            </v-card-title>
                            <v-card-text>
                                <v-form ref="form">
                                    <v-row>
                                        <v-col cols="6">
                                            <v-autocomplete label="Seleccione producto" variant="outlined"
                                                :items="itemsProductos" item-title="nombre" item-value="id"
                                                v-model="dataSaveDetalle.producto" required
                                                :rules="requiredRules"></v-autocomplete>
                                        </v-col>
                                        <v-col cols="4">
                                            <v-text-field label="cantidad" variant="outlined" required
                                                :rules="numeroRules" v-model="dataSaveDetalle.cantidad"></v-text-field>
                                        </v-col>
                                        <v-col cols="2" class="text-center mt-2">
                                            <v-btn variant="tonal" color="primary" @click="agregarCarrito">{{ dataSaveDetalle.id==null ? 'AGREGAR' : 'Modificar' }}</v-btn>
                                        </v-col>
                                    </v-row>
                                </v-form>
                                <v-data-table :headers="headers2" :items="itemsDetalle">
                                    <template v-slot:[`item.precio`]="{ item }">
                                        {{ formato_numero(item.precio) }}
                                    </template>
                                    <template v-slot:[`item.venta`]="{ item }">
                                        {{ formato_numero(item.venta) }}
                                    </template>
                                    <template v-slot:[`item.subtotal`]="{ item }">
                                        {{ formato_numero(item.subtotal) }}
                                    </template>
                                    <template v-slot:[`item.actions`]="{ item }">
                                        <v-btn color="secondary" class="me-2" variant="tonal" @click="editarCarrito(item)"><i
                                                class="fa-solid fa-pen"></i></v-btn>
                                        <v-btn color="red" variant="tonal"
                                            @click="eliminarCarrito(item)"><i
                                                class="fa-solid fa-trash"></i></v-btn>
                                    </template>
                                </v-data-table>
                            </v-card-text>
                        </v-card>
                    </v-col>
                    <v-col class="pt-0 text-end" style="font-size: 1.3rem;">
                        <b>Total: <span style="color: red;">{{ formato_numero(total) }}</span></b>
                    </v-col>

                </v-row>
            </v-card-text>
            <v-card-actions>
                <v-btn color="secondary" v-if="cardAction=='nuevo'" variant="tonal" @click="guardar">Guardar</v-btn>
                <v-btn color="secondary" v-if="cardAction=='editar'" variant="tonal" @click="guardarCambios">Guardar
                    Cambios</v-btn>
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

    <!-- DIALOG PARA INSERTAR DATOS DE CLIENTES -->
    <v-dialog v-model="cardCliente" persistent>
        <v-card width="1000" class="mx-auto">
            <v-card-title class="text-center">Registro de Clientes</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12">
                        <v-text-field label="Nombre" v-model="dataSave2.nombre" :error-messages="errors2.nombre"
                            variant="outlined"></v-text-field>
                        <v-text-field label="correo" type="email" v-model="dataSave2.email" variant="outlined"></v-text-field>
                        <v-text-field label="Dirección" v-model="dataSave2.direccion" variant="outlined"></v-text-field>
                        <v-text-field label="Nit" v-model="dataSave2.nit" variant="outlined"></v-text-field>
                        <v-text-field label="Telefono" v-model="dataSave2.telefono" variant="outlined"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-actions>
                <v-btn color="secondary" variant="tonal" @click="guardarCliente">Guardar</v-btn>
                <v-btn color="red" variant="tonal" @click="cardCliente=false,card = true, limpiar2()">Cancelar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="cardDetalle">
        <v-card width="1000" class="mx-auto">
            <v-card-title>Detalle de la venta</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12">
                        <p><b>No Recibo:</b> {{ itemDetalle.id }}</p>
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
                                <tr v-for="detalle in itemDetalle.detalle_ventas" :key="detalle.index">
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
                        <p><b>No Recibo:</b> {{ itemDetalle.id }}</p>
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
                                <tr v-for="detalle in itemDetalle.detalle_ventas" :key="detalle.index">
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
                        <p class="text-end mt-10"><b>Venta hecha por: </b>{{ itemDetalle.user.name }}</p>
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
    name: 'ventasVue',
    data() {
        return {
            headers: [
                { title: 'No. venta', key: 'id', align: 'start' },
                { title: 'Fecha compra', key: 'created_at', align: 'start' },
                { title: 'Cliente', key: 'cliente.name', align: 'start' },
                { title: 'Total', key: 'total', align: 'start' },
                { title: 'Hecha por', key: 'user.name', align: 'center' },
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
        }
    },
    methods: {
        getData() {
            this.overlay = true;
            axios.get("/ventas").then(res => {
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
        getClientes() {
            this.overlay = true;
            axios.get("/clientes").then(res => {
                this.overlay = false;
                this.itemsClientes = res.data;
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
        getProductos() {
            this.overlay = true;
            axios.get("/loteProductos").then(res => {
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
            axios.post("/ventas", {
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
        guardarCliente() {
            this.overlay = true;
            axios.post("/clientes", {
                nombre: this.dataSave2.nombre,
                email: this.dataSave2.email,
                direccion: this.dataSave2.direccion,
                nit: this.dataSave2.nit,
                telefono: this.dataSave2.telefono,
            }).then(res => {
                this.overlay = false;
                toast.success("Se han guardado los datos", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                this.limpiar2();
                this.card = true;
                this.cardCliente = false;
                this.getClientes();
                this.dataSave.cliente = res.data.data.id;
            }).catch((error) => {
                this.overlay = false;
                toast.error("¡Hubo un error al guardar los datos!", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                if (error.response && error.response.status === 422) {
                    this.errors2 = error.response.data.errors;
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
            axios.delete(`/ventas/${this.dataSave.id}`).then(res=>{
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
        async agregarCarrito(){
            this.$refs.form.resetValidation();
            const result = await this.$refs.form.validate();

            if(result.valid){
                const producto = this.itemsProductos.find(p => p.id === this.dataSaveDetalle.producto);
                if(this.dataSaveDetalle.id==null){
                    const busquedaCarrito = this.itemsDetalle.find(c => c.producto.id === producto.id);
                    if(busquedaCarrito!==undefined){
                        console.log(parseInt(this.itemsDetalle[busquedaCarrito.id].cantidad) + parseInt(this.dataSaveDetalle.cantidad));
                        if((parseInt(this.itemsDetalle[busquedaCarrito.id].cantidad) + parseInt(this.dataSaveDetalle.cantidad)) <= producto.cantidad_restante){
                            this.itemsDetalle[busquedaCarrito.id].cantidad = parseInt(this.itemsDetalle[busquedaCarrito.id].cantidad);
                            this.itemsDetalle[busquedaCarrito.id].cantidad+=parseInt(this.dataSaveDetalle.cantidad);
                            this.itemsDetalle[busquedaCarrito.id].precio=producto.precio;
                            this.itemsDetalle[busquedaCarrito.id].subtotal=(parseFloat(producto.precio)*parseFloat(this.itemsDetalle[busquedaCarrito.id].cantidad));
                        }else{
                            this.card = false;
                            Swal.fire({
                                title: '¡No tenemos suficientes para vender!',
                                text: `Solo tenemos ${producto.cantidad_restante} para vender`,
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
                    }else{
                        if(this.dataSaveDetalle.cantidad <= producto.cantidad_restante){
                            this.itemsDetalle.push({
                                id: this.index,
                                producto: producto,
                                cantidad: this.dataSaveDetalle.cantidad,
                                precio: producto.precio,
                                subtotal: (parseFloat(producto.precio)*parseFloat(this.dataSaveDetalle.cantidad)),
                                nuevo: true,
                            })
                            this.index++;
                        }else{
                            this.card = false;
                            Swal.fire({
                                title: '¡No tenemos suficientes para vender!',
                                text: `Solo tenemos ${producto.cantidad_restante} para vender`,
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
                    }
                    this.total = this.itemsDetalle.reduce((sum, item) => sum + item.subtotal, 0);
                }else{
                    const indexB = this.itemsDetalle.findIndex(itemsD => itemsD.id === this.dataSaveDetalle.id);
                    this.itemsDetalle[indexB].producto = producto;
                    this.itemsDetalle[indexB].cantidad = this.dataSaveDetalle.cantidad;
                    this.itemsDetalle[indexB].precio = producto.precio;
                    this.itemsDetalle[indexB].subtotal = (parseFloat(producto.precio)*parseFloat(this.dataSaveDetalle.cantidad));
                    this.total = this.itemsDetalle.reduce((sum, item) => sum + item.subtotal, 0);
                }
                this.dataSaveDetalle.producto = null;
                this.dataSaveDetalle.cantidad = null;
                this.dataSaveDetalle.id = null;
            }else{
                this.card = false;
                Swal.fire({
                    title: '¡Para agregar compras debe debe completar los campos!',
                    icon: 'warning',
                    allowOutsideClick: false,
                    confirmButtonColor: '#00A38C',
                    customClass: {
                        confirmButton: 'custom-confirm-button',
                    }
                }).then(response =>{
                    this.card = true;
                })
            }
        },
        formato_numero(amount) {
            var newAmount = new Intl.NumberFormat("es-GT", { style: "currency", currency: "GTQ", }).format(amount);
            return newAmount;
        },
        editarCarrito(item){
            this.dataSaveDetalle.producto = item.producto.id;
            this.dataSaveDetalle.cantidad = item.cantidad;
            this.dataSaveDetalle.id = item.id;
        },
        eliminarCarrito(item){
            this.itemsDetalle = this.itemsDetalle.filter(c => c.id!==item.id);
            this.total = this.itemsDetalle.reduce((sum, item) => sum + item.subtotal, 0);
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
    },
    mounted() {
        this.getData();
        this.getClientes();
        this.getProductos();
    }
}
</script>
<style>
.custom-confirm-button {
    color: #ffffff;
}
</style>
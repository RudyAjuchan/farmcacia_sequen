<template>
    <div>
        <!-- Navbar -->
        <navBar :logo="imgLogo"></navBar>

        <!-- Contenido del carrito -->
        <div class="container mt-5" id="carritoSection">
            <h2 class="text-primary mb-4">Tu Carrito</h2>
            
            <div v-if="cartItems.length === 0" class="alert alert-info text-center">
                Tu carrito está vacío.
            </div>
            
            <div v-else>
                <!-- Listado de productos en el carrito -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-accent text-white">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Descuento</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in cartItems" :key="item.id">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <!-- <img :src="item.image" alt="Producto" class="img-fluid me-3" style="width: 50px;"> -->
                                        <div>{{ item.nombre }}</div>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" v-model="item.cantidad" class="form-control w-50" min="1">
                                </td>
                                <td>{{ formato_numero(item.precio) }}</td>
                                <td>{{ formato_numero(item.descuento) }}</td>
                                <td>{{ formato_numero(item.subtotal) }}</td>
                                <td>
                                    <button @click="removeFromCart(item.id)" class="btn btn-danger">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Resumen del carrito -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <h5>Total: <b>{{ formato_numero(cartTotal) }}</b></h5>
                    <button class="btn btn-secondary">Proceder al Pago</button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <Footer></Footer>
    </div>
</template>
<script>
import navBar from './subcomponents/navBar.vue'
import Footer from './subcomponents/footer.vue'

import { useCarritoStore } from '../../store/carrito';
export default {
    name: 'carritoVue',
    components: {
        navBar,
        Footer,
    },
    data() {
        return {
            imgLogo: "/images/image.png",
            cartItems: [],
        }
    },
    methods: {
        formato_numero(amount) {
            var newAmount = new Intl.NumberFormat("es-GT", { style: "currency", currency: "GTQ", }).format(amount);
            return newAmount;
        },
        removeFromCart(itemId) {
            this.cartItems = this.cartItems.filter(item => item.id !== itemId);
        }
    },
    mounted() {
    },
    computed: {
        cartTotal() {
            return this.cartItems.reduce((total, item) => {
                return total + item.precio * item.cantidad;
            }, 0);
        }
    },

    created(){
        this.store = useCarritoStore();
        this.store.obtenerProductos();
        this.cartItems = this.store.productos;

    }
}
</script>

<style>
    .text-primary { color: #1E617B; }
    .bg-accent { background-color: #65C5B9; }
    .table-hover tbody tr:hover {
        background-color: #D3EEEB; /* Color de fondo al hacer hover */
    }
    #carritoSection{
        min-height: calc(100vh - 224px);
    }
</style>
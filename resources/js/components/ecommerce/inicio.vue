<template>
    <div>
        <navBar :logo="imgLogo" :cantidad_producto="cant_producto" ref="compNavBar"></navBar>

        <!-- HERO -->
        <section class="hero">
            <div class="row h-100 justify-content-center align-items-center">
                <div class="col-md-5 text-center">
                    <h1 style="color: white;"><b>Bienvenido a Farmacia Sequén</b></h1>
                    <p style="font-size: 1.2rem; color: white;">Cuidamos de tu salud con productos de calidad</p>
                    <a href="#/productos" class="btn btn-success">Ver productos</a>
                </div>
            </div>
        </section>

        <div class="container">
            <h2 class="my-4 text-center"><b>Productos Destacados</b></h2>
            <swiper :slidesPerView="3" :navigation="true" :spaceBetween="30" :freeMode="true" :loop="true"
                :pagination="{ clickable: true, }" :modules="modules" class="mySwiper py-5">
                <swiper-slide v-for="(product, index) in products" :key="index">
                    <a :href="`#/detalle/${product.lote_productos.id}`" class="text-decoration-none text-reset" target="_blank">
                        <div class="card text-center">
                            <div class="card-body">
                                <img :src="product.imagen ? `/storage/${product.imagen}` : '/storage/no-disponible.png'"
                                    class="mx-auto mb-4" width="150" height="150">
                                <h3>{{ product.nombre }}</h3>
                                <p>
                                    <del v-if="product.promociones[0]?.promocion">{{ formato_numero(product.lote_productos.precio) }}</del>
                                    <span class="text-success" v-if="!product.promociones[0]?.promocion"><b> {{ formato_numero(product.lote_productos.precio) }}</b></span> &nbsp;
                                    <span class="text-success" v-if="product.promociones[0]?.promocion"><b>{{ formato_numero(product.lote_productos.precio - product.promociones[0]?.promocion.descuento) }}</b></span>
                                </p>
                                <div v-if="!product.promociones[0]?.promocion" style="height: 40px;"></div>
                                <span class="badge bg-danger mb-3" v-if="product.promociones[0]?.promocion">{{ 
                                    Math.round((product.promociones[0]?.promocion.descuento*100)/product.lote_productos.precio ,0) }}% de descuento</span>
                                <p>{{ truncateText(product.descripcion, 120) }}</p>
                                <a href="#" class="btn btn-success" @click="agregarCarrito(product)">agregar al carrito</a>
                            </div>
                        </div>
                    </a>
                </swiper-slide>
            </swiper>
        </div>

        <div class="container">
            <h2 class="my-4 text-center"><b>Productos Recientes</b></h2>
            <swiper :slidesPerView="3" :navigation="true" :spaceBetween="30" :freeMode="true" :loop="true"
                :pagination="{ clickable: true, }" :modules="modules" class="mySwiper py-5">
                <swiper-slide v-for="(product, index) in productsRecientes" :key="index">
                    <a :href="`#/detalle/${product.lote_productos.id}`" class="text-decoration-none text-reset" target="_blank">
                        <div class="card text-center">
                            <div class="card-body">
                                <img :src="product.imagen ? `/storage/${product.imagen}` : '/storage/no-disponible.png'"
                                    class="mx-auto mb-4" width="150" height="150">
                                <h3>{{ product.nombre }}</h3>
                                <p>
                                    <del v-if="product.promociones[0]?.promocion">{{ formato_numero(product.lote_productos.precio) }}</del>
                                    <span class="text-success" v-if="!product.promociones[0]?.promocion"><b> {{ formato_numero(product.lote_productos.precio) }}</b></span> &nbsp;
                                    <span class="text-success" v-if="product.promociones[0]?.promocion"><b>{{ formato_numero(product.lote_productos.precio - product.promociones[0]?.promocion.descuento) }}</b></span>
                                </p>
                                <div v-if="!product.promociones[0]?.promocion" style="height: 40px;"></div>
                                <span class="badge bg-danger mb-3" v-if="product.promociones[0]?.promocion">{{ 
                                    Math.round((product.promociones[0]?.promocion.descuento*100)/product.lote_productos.precio ,0) }}% de descuento</span>
                                <p>{{ truncateText(product.descripcion, 120) }}</p>
                                <a href="#" class="btn btn-success" @click="agregarCarrito(product)">agregar al carrito</a>
                            </div>
                        </div>
                    </a>
                </swiper-slide>
            </swiper>
        </div>

        <!-- SERVICIOS -->
        <section class="servicios">
            <div class="container h-100">
                <div class="row h-100 justify-content-center align-items-center">
                    <h1 class="text-center"><b>Nuestros servicios</b></h1>
                    <div class="col-md-4 text-center" v-for="(service, index) in services" :key="index">
                        <h3>{{ service.title }}</h3>
                        <p>{{ service.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- PIE -->
        <Footer></Footer>
    </div>

    <v-dialog v-model="dialogCompra" persistent>
        <v-card width="500" class="mx-auto">
            <v-card-title class="text-center">Información</v-card-title>
            <v-card-text class="text-center">
                <p>El producto fue agregado con éxito</p>
                <img :src="imgSuccess" alt="" width="150">
            </v-card-text>
            <v-card-actions>
                <router-link to="carrito"><v-btn color="secondary" variant="tonal">Ir a carrito</v-btn></router-link>
                <v-btn color="primary" variant="tonal" @click="dialogCompra = false">Cancelar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="dialogLogin" persistent>
        <v-card width="500" class="mx-auto">
            <v-card-title class="text-center">Información</v-card-title>
            <v-card-text class="text-center">
                <p>Para poder comprar debes iniciar sesión</p>
                <img :src="imgWarnign" alt="" width="150">
            </v-card-text>
            <v-card-actions>
                <a href="/log_in"><v-btn color="secondary" variant="tonal">Iniciar Sesión</v-btn></a>
                <v-btn color="red" variant="tonal" @click="dialogLogin = false">Cancelar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-overlay :model-value="overlay" persistent style="background-color: black; opacity: 0.8;"
        class="align-center justify-center">
        <v-card style="background-color: transparent; border: none;" flat>
            <v-card-text>

                <h3 class="text-center text-white"> Cargando datos </h3>
            </v-card-text>
            <v-card-actions class="justify-center aling-center">

                <v-progress-circular color="success" indeterminate size="64"></v-progress-circular>
            </v-card-actions>
        </v-card>
    </v-overlay>
</template>
<script>
import { Swiper, SwiperSlide } from 'swiper/vue';
// import required modules
import { Navigation, FreeMode, Pagination } from 'swiper/modules';
// Import Swiper styles
import 'swiper/css';
import 'swiper/css/free-mode';
import 'swiper/css/pagination';
import 'swiper/css/navigation';

import navBar from './subcomponents/navBar.vue'
import Footer from './subcomponents/footer.vue'
import Swal from 'sweetalert2';

/* PARA PINIA */
import { useCarritoStore } from '../../store/carrito';
export default {
    name: 'inicioVUe',
    components: {
        navBar,
        Footer,
        Swiper,
        SwiperSlide,
    },
    data() {
        return {
            isMenuOpen: false,
            imgSuccess: '/images/verificar.png',
            products: [],
            productsRecientes: [],
            services: [
                { title: 'Entrega a domicilio', description: 'Recibe tus productos en la comodidad de tu hogar' },
                { title: 'Consultas Médicas', description: 'Consulta a nuestros especialistas en salud.' },
                { title: 'Asesoramiento Farmacéutico', description: 'Te ayudamos a escoger los productos correctos.' },
            ],
            imgLogo: "/images/image.png",
            modules: [FreeMode, Pagination, Navigation],
            overlay: false,
            store: null,
            cant_producto: 0,
            dialogCompra: false,
            logueado: false,
            dialogLogin: false,
            imgWarnign: '/images/advertencia.png',
        }
    },
    methods: {
        productosDestacados(){
            this.overlay = true;
            axios.get("/productosDestacadas").then(res => {
                this.products = res.data
                this.productosRecientes();
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
        productosRecientes(){
            axios.get("/productosRecientes").then(res => {
                this.productsRecientes = res.data
                this.overlay = false;
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
        truncateText(text, maxLength) {
            if (text.length > maxLength) {
                let truncated = text.substring(0, maxLength);
                let lastSpace = truncated.lastIndexOf(' ');
                if (lastSpace > 0) {
                    truncated = truncated.substring(0, lastSpace);
                }
                return truncated + '...';
            } else {
                // Rellenamos con "&nbsp;" en lugar de espacios regulares
                return text + '\n' + '\u00A0'.repeat((maxLength+50) - text.length);
            }
        },
        formato_numero(amount) {
            var newAmount = new Intl.NumberFormat("es-GT", { style: "currency", currency: "GTQ", }).format(amount);
            return newAmount;
        },
        agregarCarrito(producto){
            if(this.store.logueado){
                this.store.agregarProductos(producto);
                this.cant_producto = this.store.productos.length;
                this.$refs.compNavBar.setCantidad(this.cant_producto);
                this.dialogCompra = true;
            }else{
                this.dialogLogin = true;
            }
        },
    },
    mounted() {
        this.productosDestacados();
    },
    created(){
        this.store = useCarritoStore();
        this.store.obtenerProductos();
        this.cant_producto = this.store.productos.length;
    }
}
</script>
<style scoped>
body {
    font-family: 'Inter', sans-serif;
    font-optical-sizing: auto;
}

.hero {
    height: 300px;
    background-image: url('/images/farmacia.jpg');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
}

.servicios {
    height: 300px;
    background-color: #ccfbf1;
}

h1 {
    color: #0f766e;
}

.btn-success {
    background-color: #0f766e;
}

.btn-success:hover {
    background-color: #0d5e57;
}

h2 {
    color: #00a38c;
}

h3 {
    color: #0f766e;
}

.swiper-pagination {
    bottom: 0px !important;
}
</style>
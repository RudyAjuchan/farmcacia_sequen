<template>
    <div>
        <navBar :logo="imgLogo"></navBar>

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
                <swiper-slide  v-for="(product, index) in products" :key="index">
                    <div class="card text-center">
                        <div class="card-body">
                            <img :src="product.imagen ? `/storage/${product.imagen}` : '/storage/no-disponible.png'" class="mx-auto mb-4" width="150" height="150">
                            <h3>{{ product.nombre }}</h3>
                            <p>{{ truncateText(product.descripcion, 120) }}</p>
                            <a href="#" class="btn btn-success">Comprar</a>
                        </div>
                    </div>
                </swiper-slide>
            </swiper>
        </div>

        <div class="container">
            <h2 class="my-4 text-center"><b>Productos Recientes</b></h2>
            <swiper :slidesPerView="3" :navigation="true" :spaceBetween="30" :freeMode="true" :loop="true"
                :pagination="{ clickable: true, }" :modules="modules" class="mySwiper py-5">
                <swiper-slide  v-for="(product, index) in productsRecientes" :key="index">
                    <div class="card text-center">
                        <div class="card-body">
                            <img :src="product.imagen ? `/storage/${product.imagen}` : '/storage/no-disponible.png'" class="mx-auto mb-4" width="150" height="150">
                            <h3>{{ product.nombre }}</h3>
                            <p>{{ truncateText(product.descripcion, 120) }}</p>
                            <a href="#" class="btn btn-success">Comprar</a>
                        </div>
                    </div>
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
                return text.substring(0, maxLength) + '...';
            }
            return text;
        }
    },
    mounted() {
        this.productosDestacados();
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
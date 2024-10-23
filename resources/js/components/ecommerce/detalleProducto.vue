<template>
    <navBar :logo="imgLogo"></navBar>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <img :src="producto.productos.imagen ? `/storage/${producto.productos.imagen}` : '/storage/no-disponible.png'" class="img-fluid" width="250" :alt="producto.productos.nombre" />
            </div>
            <div class="col-md-6">
                <h2>{{ producto.productos.nombre }}</h2>
                <p class="text-muted">
                    <del v-if="producto.producto_promocion[0]?.promocion">{{ formato_numero(producto.precio) }}</del>
                    <span class="text-success" v-if="!producto.producto_promocion[0]?.promocion"><b> {{ formato_numero(producto.precio) }}</b></span>
                    <span class="text-success" v-if="producto.producto_promocion[0]?.promocion"><b>{{ formato_numero(producto.precio - producto.producto_promocion[0]?.promocion?.descuento) }}</b></span>
                </p>
                <span class="badge bg-danger" v-if="producto.producto_promocion[0]?.promocion">{{ 
                    Math.round((producto.producto_promocion[0]?.promocion?.descuento*100)/producto.precio ,0) }}% dedescuento</span>
                <p class="mt-3">{{ producto.productos.descripcion }}</p>


                <button class="btn btn-success mt-3" @click="agregarAlCarrito">
                    Añadir al carrito
                </button>
            </div>
        </div>

        <!-- Sección de Productos Similares -->
        <h3 class="mt-5">Productos Similares</h3>
        <div class="container">
            <swiper :slidesPerView="3" :navigation="true" :spaceBetween="30" :freeMode="true" :loop="true"
                :pagination="{ clickable: true, }" :modules="modules" class="mySwiper py-5">
                <swiper-slide v-for="(product, index) in productosSimilares" :key="index">
                    <a :href="`#/detalle/${product.lote_productos[0].id}`" class="text-decoration-none text-reset">
                        <div class="card text-center">
                            <div class="card-body">
                                <img :src="product.imagen ? `/storage/${product.imagen}` : '/storage/no-disponible.png'"
                                    class="mx-auto mb-4" width="150" height="150">
                                <h3>{{ product.nombre }}</h3>
                                <p>{{ truncateText(product.descripcion, 120) }}</p>
                                <a href="#" class="btn btn-success">Comprar</a>
                            </div>
                        </div>
                    </a>
                </swiper-slide>
            </swiper>
        </div>
    </div>

    <!-- PIE -->
    <Footer></Footer>
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
export default {
    name: 'detalleProductoVue',
    components:{
        navBar,
        Footer,
        Swiper,
        SwiperSlide,
    },
    props: {
        id: {
            type: String,
            default: 0,
        }
    },
    data() {
        return {
            imgLogo: "/images/image.png",
            cantidad: 1,
            nuevoComentario: '',
            producto: {
                productos: {},
                producto_promocion: [],
            },
            productosSimilares: [],
            productosSimilares: [],
            modules: [FreeMode, Pagination, Navigation],
        }
    },
    methods: {
        agregarAlCarrito(producto) {
            // Lógica para agregar el producto al carrito
            console.log('Agregando al carrito:', producto, 'Cantidad:', this.cantidad);
        },
        agregarComentario() {
            if (this.nuevoComentario.trim()) {
                this.comentarios.push({
                    id: this.comentarios.length + 1,
                    usuario: 'Usuario Desconocido',
                    texto: this.nuevoComentario,
                });
                this.nuevoComentario = '';
            }
        },
        getProducto() {
            this.overlay = true;
            axios.post("/productoEcommerce", { id: this.id}).then(res => {
                this.overlay = false;
                this.producto = res.data;
                this.getProductosSimilares();
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
        getProductosSimilares() {
            this.overlay = true;
            axios.post("/productosSimilaresEcommerce", { id: this.id}).then(res => {
                this.overlay = false;
                this.productosSimilares = res.data;
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
        formato_numero(amount) {
            var newAmount = new Intl.NumberFormat("es-GT", { style: "currency", currency: "GTQ", }).format(amount);
            return newAmount;
        },
        truncateText(text, maxLength) {
            if (text.length > maxLength) {
                return text.substring(0, maxLength) + '...';
            }
            return text;
        }
    },
    mounted() {
        this.getProducto();
    }
}
</script>
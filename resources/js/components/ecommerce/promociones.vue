<template>
    <div>
        <navBar :logo="imgLogo" :cantidad_producto="cant_producto" ref="compNavBar"></navBar>
        <!-- Banner de Promociones -->
        <div class="promo-banner mb-4">

        </div>
        <div class="container">
            <!-- Filtros -->
            <v-row>
                <v-col cols="4">
                    <v-autocomplete variant="outlined" label="Filtrar por categoría" :items="itemsCategorias"
                        item-title="nombre" item-value="id" @update:modelValue="filtrarPorCategoria"
                        v-model="modelCategoria"></v-autocomplete>
                </v-col>
                <v-col cols="4">
                    <v-autocomplete variant="outlined" label="Filtrar por subcategoría" :items="itemsSubcategorias"
                        item-title="nombre" item-value="id" @update:modelValue="filtrarPorSubcategoria"
                        v-model="modelSubCategoria"></v-autocomplete>
                </v-col>
                <v-col cols="4">
                    <v-autocomplete variant="outlined" label="Filtrar por rango de precio" :items="itemsRangos"
                        item-title="nombre" item-value="id" v-model="modelRangos"
                        @update:modelValue="filtrarPorPrecio"></v-autocomplete>
                </v-col>
            </v-row>

            <!-- Sección de Promociones Destacadas -->
            <h2 class="mb-3">Promociones Destacadas</h2>
            <div class="row g-4">
                <div class="col-md-4" v-for="producto in productosFiltrados" :key="producto.id">
                    <div class="card h-100">
                        <a :href="`#/detalle/${producto.lote_productos.id}`" class="text-decoration-none text-reset" target="_blank">
                            <div class="card-body text-center">
                                <img :src="producto.lote_productos.productos.imagen ? `/storage/${producto.lote_productos.productos.imagen}` : '/storage/no-disponible.png'"
                                    width="150" height="150" :alt="producto.lote_productos.productos.nombre" />
                                <h5 class="card-title">{{ producto.lote_productos.productos.nombre }}</h5>
                                <p class="text-muted">
                                    <del>{{ formato_numero(producto.lote_productos.precio) }}</del>&nbsp;
                                    <span class="text-success"><b>{{ formato_numero(producto.lote_productos.precio -
                                        producto.promocion.descuento) }}</b></span>
                                </p>
                                <span class="badge bg-danger">{{
                                    Math.round((producto.promocion.descuento * 100) / producto.lote_productos.precio, 0) }}%
                                    de
                                    descuento</span><br />
                            </div>
                        </a>
                        <div class="text-center">
                            <button class="btn btn-success mt-3" @click="agregarAlCarrito(producto)">
                                Añadir al carrito
                            </button>
                            <p class="mt-2">
                                <span>Oferta válida del {{ formato_fecha(producto.promocion.fecha_inicio) }} al {{
                                    formato_fecha(producto.promocion.fecha_fin) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paginación (si es necesario) -->
            <div class="d-flex justify-content-center mt-4">
                <!-- Paginación -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item" :class="{ disabled: paginaActual === 1 }">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(paginaActual - 1)">
                                Anterior
                            </a>
                        </li>
                        <li class="page-item" v-for="pagina in totalPaginas" :key="pagina"
                            :class="{ active: pagina === paginaActual }">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagina)">
                                {{ pagina }}
                            </a>
                        </li>
                        <li class="page-item" :class="{ disabled: paginaActual === totalPaginas }">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(paginaActual + 1)">
                                Siguiente
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- PIE -->
        <Footer></Footer>

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
                <v-btn color="primary" variant="tonal" @click="dialogCompra = false">Seguir comprando</v-btn>
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
</template>
<script>
import navBar from './subcomponents/navBar.vue'
import Footer from './subcomponents/footer.vue'
import Swal from 'sweetalert2';
/* PARA PINIA */
import { useCarritoStore } from '../../store/carrito';
export default {
    name: 'promocionesVue',
    components:{
        navBar,
        Footer,
    },
    data() {
        return {
            imgLogo: "/images/image.png",
            filtros: {
                categoria: '',
                descuento: '',
                precio: ''
            },
            productos: [],
            paginaActual: 1,
            totalPaginas: 3,

            itemsCategorias: [],
            itemsSubcategorias: [],
            itemsRangos: [
                {id: 0, nombre: '0 - Q.200'},
                {id: 1, nombre: 'Q,201 - Q.400'},
                {id: 2, nombre: 'Q.401 - Q.600'},
                {id: 3, nombre: 'Q.601 - Q.800'},
                {id: 4, nombre: 'Q.801 - Q.1000'},
                {id: 5, nombre: 'mayor de Q.1000'},
            ],

            overlay: false,
            filtroCategoria: null, // Filtro de categoría
            filtroSubcategoria: null, // Filtro de subcategoría
            filtroPrecio: null,
            productosFiltrados: [], // Productos filtrados localmente
            paginaActual: 1,
            totalPaginas: 0,
            modelCategoria: null,
            modelSubCategoria: null,
            modelRangos: null,

            imgSuccess: '/images/verificar.png',
            store: null,
            cant_producto: 0,
            dialogCompra: false,
            logueado: false,
            dialogLogin: false,
            imgWarnign: '/images/advertencia.png',
        }
    },
    methods: {
        obtenerProductos(pagina = 1){
            this.overlay = true;
            axios.get(`/productosPromocion?page=${pagina}`,{
                params: {
                    categoria: this.filtroCategoria,
                    subcategoria: this.filtroSubcategoria,
                    precio: this.filtroPrecio,
                }
            }).then(res => {
                this.productos = res.data.data;
                this.productosFiltrados = this.productos; // Llenar productos iniciales
                this.paginaActual = res.data.current_page;
                this.totalPaginas = res.data.last_page;
                this.overlay = false;
            }).catch((err) => {
                this.overlay = false;
                Swal.fire({
                    title: '¡Hubo un error al obtener datos de productos!',
                    text: err,
                    icon: 'error',
                    allowOutsideClick: false,
                    confirmButtonColor: '#00A38C',
                    customClass: {
                        confirmButton: 'custom-confirm-button',  // Clase personalizada
                    }
                });
            });
        },
        agregarAlCarrito(producto) {
            if(this.store.logueado){
                producto["promociones"] = [];
                producto["promociones"][0] = [];
                producto["promociones"][0]['promocion'] = producto["promocion"]
                producto["nombre"] = producto["lote_productos"]["productos"]["nombre"];
                producto["imagen"] = producto["lote_productos"]["productos"]["imagen"];
                producto["id"] = producto["lote_productos"]["productos"]["id"];
                this.store.agregarProductos(producto);
                this.cant_producto = this.store.productos.length;
                this.$refs.compNavBar.setCantidad(this.cant_producto);
                this.dialogCompra = true;
            }else{
                this.dialogLogin = true;
            }
        },
        cambiarPagina(pagina) {
            if (pagina >= 1 && pagina <= this.totalPaginas) {
                this.obtenerProductos(pagina);
            }
        },
        categorias(){
            this.overlay = true;
            axios.get("/categoriasEcomemrce").then(res => {
                this.itemsCategorias = res.data
                this.subcategorias();
            }).catch((err) => {
                Swal.fire({
                    title: '¡Hubo un error al obtener categorías!',
                    icon: 'error',
                    allowOutsideClick: false,
                    confirmButtonColor: '#00A38C',
                    customClass: {
                        confirmButton: 'custom-confirm-button',  // Clase personalizada
                    }
                });
            });
        },
        subcategorias(){
            axios.get("/subCategoriasEcommerce").then(res => {
                this.itemsSubcategorias = res.data
                this.obtenerProductos();
            }).catch((err) => {
                this.overlay = false;
                Swal.fire({
                    title: '¡Hubo un error al obtener subcategorías!',
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
        formato_fecha(fechaF){
            // Extraemos año, mes y día manualmente
            const [year, month, day] = fechaF.split('-');

            // Formateamos la fecha en el formato deseado
            const fechaFormateada = `${day}/${month}/${year}`;

            return fechaFormateada;
        },
        filtrarPorCategoria(categoriaId) {
            this.filtroCategoria = categoriaId;
            this.filtroSubcategoria = null;
            this.filtroPrecio = null;
            this.obtenerProductos(1);

            this.modelSubCategoria = null;
            this.modelRangos = null;
        },
        filtrarPorSubcategoria(subcategoriaId) {
            this.filtroSubcategoria = subcategoriaId;
            this.filtroCategoria = null;
            this.filtroPrecio = null;
            this.obtenerProductos(1);

            this.modelCategoria = null;
            this.modelRangos = null;
        },
        filtrarPorPrecio(precioId) {
            console.log("si pasa");
            this.filtroPrecio = precioId;
            this.filtroCategoria = null;
            this.filtroSubcategoria = null;
            this.obtenerProductos(1);

            this.modelCategoria = null;
            this.modelSubCategoria = null;
        },
    },
    mounted() {
        this.categorias();
    },
    computed: {        
    },
    created(){
        this.store = useCarritoStore();
        this.store.obtenerProductos();
        this.cant_producto = this.store.productos.length;
    }
}
</script>

<style scoped>
.promo-banner {
    height: 300px;
    background-image: url("/images/banner_promocion.jpg");
    background-repeat: no-repeat;
    background-size: cover;
}
.pagination .page-link {
    color: #0F766E;
}

.pagination .page-item.active .page-link {
    background-color: #0F766E;
    border-color: #0F766E;
}
.active a{
    color: white !important;
}
</style>
<template>
    <div>
        <navBar :logo="imgLogo"></navBar>
        <!-- Sección de productos -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <!-- Barra lateral de categorías -->
                    <aside class="col-md-3">
                        <h3><b>Categorías</b></h3>
                        <!-- Añadir barra de desplazamiento si hay muchas categorías -->
                        <ul class="category-list list-unstyled overflow-auto" style="max-height: 260px;">
                            <li v-for="categoria in itemsCategorias" :key="categoria.id">
                                <a href="#" @click.prevent="filtrarPorCategoria(categoria.id)">
                                    {{ categoria.nombre }}
                                </a>
                            </li>
                        </ul>

                        <h4 class="mt-4">Subcategorías</h4>
                        <!-- Añadir barra de desplazamiento si hay muchas subcategorías -->
                        <ul class="category-list list-unstyled overflow-auto" style="max-height: 260px;">
                            <li v-for="subcategoria in itemsSubcategorias" :key="subcategoria.id">
                                <a href="#" @click.prevent="filtrarPorSubcategoria(subcategoria.id)">
                                    {{ subcategoria.nombre }}
                                </a>
                            </li>
                        </ul>
                    </aside>

                    <!-- Grid de productos -->
                    <div class="col-md-9">
                        <h1 class="text-center mb-4"><b>Nuestros Productos</b></h1>
                        <v-text-field variant="outlined" label="Buscar"></v-text-field>
                        <div class="row g-4">
                            <!-- Productos -->
                            <div class="col-sm-6 col-md-4" v-for="producto in itemsProductos" :key="producto.id">
                                <div class="product-card p-3 text-center">
                                    <img :src="producto.productos.imagen ? `/storage/${producto.productos.imagen}` : '/storage/no-disponible.png'"
                                        width="150" height="150" :alt="producto.productos.nombre" />
                                    <h5>{{ producto.productos.nombre }}</h5>
                                    <p class="text-success">{{ formato_numero(producto.precio) }}</p>
                                    <button class="btn btn-success" @click="agregarAlCarrito(producto)">
                                        Agregar al Carrito
                                    </button>
                                </div>
                            </div>
                        </div>

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
import navBar from './subcomponents/navBar.vue'
import Footer from './subcomponents/footer.vue'
import Swal from 'sweetalert2';
export default {
    name: 'productosVue',
    components:{
        navBar,
        Footer,
    },
    data() {
        return {
            imgLogo: "/images/image.png",
            itemsCategorias: [],
            itemsSubcategorias: [],
            itemsProductos: [],
            paginaActual: 1,
            totalPaginas: 0,
            perPage: 6,
            overlay: false,
            filtroCategoria: null, // Filtro de categoría
            filtroSubcategoria: null // Filtro de subcategoría
        }
    },
    methods: {
        categorias(){
            axios.get("/categoriasEcomemrce").then(res => {
                this.itemsCategorias = res.data
            }).catch((err) => {
                this.overlay = false;
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
        obtenerProductos(pagina = 1){
            this.overlay = true;
            axios.get(`/productosEcommerce?page=${pagina}`,{
                params: {
                    categoria: this.filtroCategoria,
                    subcategoria: this.filtroSubcategoria,
                }
            }).then(res => {
                this.itemsProductos = res.data.data
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
        cambiarPagina(pagina) {
            if (pagina >= 1 && pagina <= this.totalPaginas) {
                this.obtenerProductos(pagina);
            }
        },
        agregarAlCarrito(producto) {
            // Lógica para agregar producto al carrito
            console.log('Agregando al carrito:', producto);
        },
        formato_numero(amount) {
            var newAmount = new Intl.NumberFormat("es-GT", { style: "currency", currency: "GTQ", }).format(amount);
            return newAmount;
        },
        filtrarPorCategoria(categoriaId) {
            this.filtroCategoria = categoriaId;
            this.filtroSubcategoria = null;
            this.obtenerProductos(1);
        },
        filtrarPorSubcategoria(subcategoriaId) {
            this.filtroSubcategoria = subcategoriaId;
            this.obtenerProductos(1);
        },
    },
    mounted() {
        this.categorias();
        this.subcategorias();
        this.obtenerProductos();
    }
}
</script>

<style scoped>
.product-card {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 15px;
    transition: transform 0.2s;
}

.product-card:hover {
    transform: scale(1.05);
}

.btn-success {
    background-color: #0F766E;
}

.btn-success:hover {
    background-color: #0d5e57;
}

.category-list a {
    color: #0D9488;
    text-decoration: none;
    padding: 10px 0;
    display: block;
}

.category-list a:hover {
    color: #0F766E;
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
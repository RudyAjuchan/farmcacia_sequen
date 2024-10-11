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
                        <ul class="category-list list-unstyled">
                            <li v-for="categoria in categorias" :key="categoria">
                                <a href="#">{{ categoria }}</a>
                            </li>
                        </ul>

                        <h4 class="mt-4">Subcategorías</h4>
                        <ul class="category-list list-unstyled">
                            <li v-for="subcategoria in subcategorias" :key="subcategoria">
                                <a href="#">{{ subcategoria }}</a>
                            </li>
                        </ul>
                    </aside>

                    <!-- Grid de productos -->
                    <div class="col-md-9">
                        <h1 class="text-center mb-4"><b>Nuestros Productos</b></h1>
                        <div class="row g-4">
                            <!-- Productos -->
                            <div class="col-sm-6 col-md-4" v-for="producto in productos" :key="producto.id">
                                <div class="product-card p-3 text-center">
                                    <img :src="producto.imagen" class="img-fluid mb-3" :alt="producto.nombre" />
                                    <h5>{{ producto.nombre }}</h5>
                                    <p class="text-success">{{ producto.precio }}</p>
                                    <button class="btn btn-success" @click="agregarAlCarrito(producto)">
                                        Agregar al Carrito
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Paginación -->
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item">
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
                                <li class="page-item">
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
</template>
<script>
import navBar from './subcomponents/navBar.vue'
import Footer from './subcomponents/footer.vue'
export default {
    name: 'productosVue',
    components:{
        navBar,
        Footer,
    },
    data() {
        return {
            imgLogo: "/images/image.png",
            categorias: [
                'Medicamentos',
                'Suplementos',
                'Cuidado personal',
                'Cuidado infantil',
                'Equipos médicos'
            ],
            subcategorias: [
                'Analgésicos',
                'Antibióticos',
                'Vitaminas',
                'Jabones'
            ],
            productos: [
                {
                    id: 1,
                    nombre: 'Producto 1',
                    precio: '$10.99',
                    imagen: 'https://via.placeholder.com/150'
                },
                {
                    id: 2,
                    nombre: 'Producto 2',
                    precio: '$15.49',
                    imagen: 'https://via.placeholder.com/150'
                },
                {
                    id: 3,
                    nombre: 'Producto 3',
                    precio: '$8.75',
                    imagen: 'https://via.placeholder.com/150'
                }
                // Puedes agregar más productos aquí...
            ],
            paginaActual: 1,
            totalPaginas: 3
        }
    },
    methods: {
        agregarAlCarrito(producto) {
            // Lógica para agregar producto al carrito
            console.log('Agregando al carrito:', producto);
        },
        cambiarPagina(pagina) {
            if (pagina > 0 && pagina <= this.totalPaginas) {
                this.paginaActual = pagina;
                // Lógica para cambiar de página
                console.log('Cambiando a la página:', pagina);
            }
        }
    },
    mounted() {
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
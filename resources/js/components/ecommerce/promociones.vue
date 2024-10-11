<template>
    <div>
        <navBar :logo="imgLogo"></navBar>
        <!-- Banner de Promociones -->
        <div class="promo-banner mb-4">

        </div>
        <div class="container">
            <!-- Filtros -->
            <div class="filters mb-4 d-flex justify-content-between">
                <div class="category-filter">
                    <label for="category">Categoría:</label>
                    <select id="category" class="form-select" v-model="filtros.categoria">
                        <option value="">Todas</option>
                        <option value="medicinas">Medicinas</option>
                        <option value="cosmeticos">Cosméticos</option>
                        <!-- Otras categorías -->
                    </select>
                </div>

                <div class="discount-filter">
                    <label for="discount">Descuento:</label>
                    <select id="discount" class="form-select" v-model="filtros.descuento">
                        <option value="">Todos</option>
                        <option value="10">10% o más</option>
                        <option value="25">25% o más</option>
                        <option value="50">50% o más</option>
                    </select>
                </div>

                <div class="price-filter">
                    <label for="price">Rango de precios:</label>
                    <select id="price" class="form-select" v-model="filtros.precio">
                        <option value="">Todos</option>
                        <option value="0-50">0 - 50</option>
                        <option value="50-100">50 - 100</option>
                        <option value="100-200">100 - 200</option>
                    </select>
                </div>
            </div>

            <!-- Sección de Promociones Destacadas -->
            <h2 class="mb-3">Promociones Destacadas</h2>
            <div class="row g-4">
                <div class="col-md-4" v-for="producto in productosFiltrados" :key="producto.id">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img :src="producto.imagen" class="img-fluid mb-3" :alt="producto.nombre" />
                            <h5 class="card-title">{{ producto.nombre }}</h5>
                            <p class="text-muted">
                                <del>{{ producto.precioOriginal }}</del>
                                <span class="text-success">{{ producto.precioConDescuento }}</span>
                            </p>
                            <span class="badge bg-danger">{{ producto.descuento }}% OFF</span><br />
                            <button class="btn btn-primary mt-3" @click="agregarAlCarrito(producto)">
                                Añadir al carrito
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paginación (si es necesario) -->
            <div class="d-flex justify-content-center mt-4">
                <nav>
                    <ul class="pagination">
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
        <!-- PIE -->
        <Footer></Footer>
    </div>
</template>
<script>
import navBar from './subcomponents/navBar.vue'
import Footer from './subcomponents/footer.vue'
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
            productos: [
                {
                    id: 1,
                    nombre: 'Producto 1',
                    precioOriginal: '$100',
                    precioConDescuento: '$75',
                    descuento: 25,
                    imagen: 'https://via.placeholder.com/150'
                },
                {
                    id: 2,
                    nombre: 'Producto 2',
                    precioOriginal: '$50',
                    precioConDescuento: '$40',
                    descuento: 20,
                    imagen: 'https://via.placeholder.com/150'
                },
                // Agrega más productos aquí...
            ],
            paginaActual: 1,
            totalPaginas: 3
        }
    },
    methods: {
        verificarRangoPrecio(producto) {
            const rango = this.filtros.precio.split('-');
            const precioConDescuento = parseFloat(
                producto.precioConDescuento.replace('$', '')
            );
            return (
                precioConDescuento >= parseFloat(rango[0]) &&
                precioConDescuento <= parseFloat(rango[1])
            );
        },
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
    },
    computed: {
        productosFiltrados() {
            return this.productos.filter((producto) => {
                const cumpleCategoria =
                    this.filtros.categoria === '' ||
                    producto.categoria === this.filtros.categoria;
                const cumpleDescuento =
                    this.filtros.descuento === '' ||
                    producto.descuento >= Number(this.filtros.descuento);
                const cumplePrecio =
                    this.filtros.precio === '' || this.verificarRangoPrecio(producto);

                return cumpleCategoria && cumpleDescuento && cumplePrecio;
            });
        }
    },
}
</script>

<style scoped>
.promo-banner {
    height: 300px;
    background-image: url("/images/banner_promocion.jpg");
    background-repeat: no-repeat;
    background-size: cover;
}
</style>
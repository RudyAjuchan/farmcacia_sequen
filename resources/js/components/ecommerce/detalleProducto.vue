<template>
    <navBar :logo="imgLogo"></navBar>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <img :src="producto.imagen" class="img-fluid" :alt="producto.nombre" />
            </div>
            <div class="col-md-6">
                <h2>{{ producto.nombre }}</h2>
                <p class="text-muted">
                    <del>{{ producto.precioOriginal }}</del>
                    <span class="text-success">{{ producto.precioConDescuento }}</span>
                </p>
                <span class="badge bg-danger">{{ producto.descuento }}% OFF</span>
                <p class="mt-3">{{ producto.descripcion }}</p>

                <div class="d-flex align-items-center">
                    <label class="me-2">Cantidad:</label>
                    <input type="number" v-model="cantidad" min="1" class="form-control w-25" />
                </div>

                <button class="btn btn-primary mt-3" @click="agregarAlCarrito">
                    Añadir al carrito
                </button>
            </div>
        </div>

        <!-- Sección de Productos Similares -->
        <h3 class="mt-5">Productos Similares</h3>
        <div class="row g-4">
            <div class="col-md-4" v-for="productoSimilares in productosSimilares" :key="productoSimilares.id">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img :src="productoSimilares.imagen" class="img-fluid mb-3" :alt="productoSimilares.nombre" />
                        <h5 class="card-title">{{ productoSimilares.nombre }}</h5>
                        <p class="text-muted">
                            <del>{{ productoSimilares.precioOriginal }}</del>
                            <span class="text-success">{{ productoSimilares.precioConDescuento }}</span>
                        </p>
                        <span class="badge bg-danger">{{ productoSimilares.descuento }}% OFF</span>
                        <button class="btn btn-primary mt-3" @click="agregarAlCarrito(productoSimilares)">
                            Añadir al carrito
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PIE -->
    <Footer></Footer>
</template>
<script>
import navBar from './subcomponents/navBar.vue'
import Footer from './subcomponents/footer.vue'
export default {
    name: 'detalleProductoVue',
    components:{
        navBar,
        Footer,
    },
    data() {
        return {
            imgLogo: "/images/image.png",
            cantidad: 1,
            nuevoComentario: '',
            producto: {
                id: 1,
                nombre: 'Producto Ejemplo',
                precioOriginal: '$100',
                precioConDescuento: '$75',
                descuento: 25,
                imagen: 'https://via.placeholder.com/150',
                descripcion: 'Descripción detallada del producto aquí.',
            },
            productosSimilares: [
                {
                    id: 2,
                    nombre: 'Producto Similar 1',
                    precioOriginal: '$50',
                    precioConDescuento: '$40',
                    descuento: 20,
                    imagen: 'https://via.placeholder.com/150',
                },
                {
                    id: 3,
                    nombre: 'Producto Similar 2',
                    precioOriginal: '$80',
                    precioConDescuento: '$60',
                    descuento: 25,
                    imagen: 'https://via.placeholder.com/150',
                },
                // Agrega más productos similares aquí...
            ],
            comentarios: [
                { id: 1, usuario: 'Usuario1', texto: 'Excelente producto!' },
                { id: 2, usuario: 'Usuario2', texto: 'Muy útil y de buena calidad.' },
            ],
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
    },
    mounted() {
    }
}
</script>
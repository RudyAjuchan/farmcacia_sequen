<template>
    <div>
        <header>
            <nav class="navbar navbar-dark navbar-expand-lg text-white" style="background-color: #0D9488;">
                <div class="container">
                    <img :src="logo" alt="" class="img-fluid" width="50" />
                    <a class="navbar-brand" href="#">&nbsp;<b>Farmacia Sequén</b></a>
                    <button class="navbar-toggler" type="button" @click="toggleMenu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-center" :class="{ 'show': isMenuOpen }">
                        <ul class="navbar-nav mb-2 mb-lg-0 text-center">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="/">Inicio</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="#/productos">Productos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="#/promociones">Promociones</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="#/acercade">Acerca de</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="#/contacto">Contacto</a>
                            </li>
                        </ul>
                        <div class="text-center d-block d-lg-none">
                            <a href="#" class="btn btn-light">Iniciar Sesión</a>
                        </div>
                    </div>
                    <div class="text-center d-none d-lg-block">
                        <template v-if="user">
                            <span class="text-white">Hola, {{ user.name }}</span> &nbsp;
                            <button class="btn btn-light" @click="logout">Cerrar Sesión</button>
                        </template>
                        <template v-else>
                            <a href="/log_in" class="btn btn-light">Iniciar Sesión</a>
                        </template>
                        <!-- Carrito con indicador de productos -->
                        <a href="#/carrito" target="_blank" class="text-decoration-none text-reset ms-4 position-relative">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <!-- Mostrar el indicador solo si hay productos en el carrito -->
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ cantidadProducto }}
                            </span>
                        </a>
                    </div>
                </div>
            </nav>
        </header>
    </div>
</template>
<script>
import { useCarritoStore } from '../../../store/carrito';
export default {
    props:{
        logo: {
            type: String,
            require: true,
        },
        cantidad_producto: {
            type: Number,
            require: true,
        }
    },
    name: 'navBarVue',
    data() {
        return {
            isMenuOpen: false,
            user: null,
            carrito: [],
            store: null,
        }
    },
    methods: {
        toggleMenu() {
            this.isMenuOpen = !this.isMenuOpen;
        },
        getUser(){
            axios.get('/user').then(res => {
                this.user = res.data.name ? res.data : null ;
                const estado = res.data.name ? true: false;
                this.store.setAuth(estado);
            }).catch(error => {
                console.error('Error fetching user:', error);
            });
        },
        logout() {
            axios.post('/logout')
                .then(() => {
                    this.user = null;  // Elimina los datos del usuario en el frontend
                    this.store.logout();
                    window.location.href = '/';  // Redirige al usuario a la página principal
                })
                .catch(error => {
                    console.error('Error logging out:', error);
                });
        },
        setCantidad(cantidad){
            this.cantidadProducto = cantidad;
        }
    },
    mounted() {
        this.store = useCarritoStore();
        this.getUser();
    },
    computed:{
        cantidadProducto(){
            return this.cantidad_producto;
        }
    }
}
</script>
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
                        <a href="#" class="btn btn-light">Iniciar Sesión</a>
                    </div>
                </div>
            </nav>
        </header>
    </div>
</template>
<script>
export default {
    props:{
        logo: {
            type: String,
            require: true,
        },
    },
    name: 'navBarVue',
    data() {
        return {
            isMenuOpen: false,
        }
    },
    methods: {
    },
    mounted() {
    }
}
</script>
import { createRouter, createWebHashHistory } from "vue-router";

const HomeComponent = () => import('../components/ecommerce/inicio.vue');
const ContactoComponent = () => import('../components/ecommerce/contacto.vue');
const acercadeComponent = () => import('../components/ecommerce/acercade.vue');
const productosComponent = () => import('../components/ecommerce/productos.vue');
const promocionesComponent = () => import('../components/ecommerce/promociones.vue');
const detalleProductoComponent = () => import('../components/ecommerce/detalleProducto.vue');
const carritoComponent = () => import('../components/ecommerce/carrito.vue');

const routes = [
    { 
        path: '/', 
        name: 'inicioVue', 
        component: HomeComponent 
    },
    { 
        path: '/contacto', 
        name: 'contactoVue', 
        component: ContactoComponent 
    },
    { 
        path: '/acercade', 
        name: 'acercadeVue', 
        component: acercadeComponent 
    },
    { 
        path: '/productos', 
        name: 'productosVue', 
        component: productosComponent 
    },
    { 
        path: '/promociones', 
        name: 'promocionesVue', 
        component: promocionesComponent 
    },
    { 
        path: '/detalle', 
        name: 'detalleProductoVue', 
        component: detalleProductoComponent 
    },
    { 
        path: '/carrito', 
        name: 'carritoVue', 
        component: carritoComponent 
    },
];
const router = createRouter({
    history: createWebHashHistory(import.meta.env.BASE_URL),
    routes
});
export default router;
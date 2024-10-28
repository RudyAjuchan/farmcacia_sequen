import { createRouter, createWebHashHistory } from "vue-router";

const HomeComponent = () => import('../components/Home.vue');
const CategoriasComponent = () => import('../components/categorias.vue');
const subCategoriasComponent = () => import('../components/subCategorias.vue');
const proveedoresComponent = () => import('../components/proveedores.vue');
const productosComponent = () => import('../components/productos.vue');
const comprasComponent = () => import('../components/Compras.vue');
const ventasComponent = () => import('../components/Ventas.vue');
const promocionesComponent = () => import('../components/promociones.vue');
const pedidosComponent = () => import('../components/Pedidos.vue');
const routes = [
    { 
        path: '/', 
        name: 'HomeVue', 
        component: HomeComponent 
    },
    { 
        path: '/categorias', 
        name: 'CategoriasVue', 
        component: CategoriasComponent 
    },
    { 
        path: '/subcategorias', 
        name: 'subCategoriasVue', 
        component: subCategoriasComponent 
    },
    { 
        path: '/proveedores', 
        name: 'proveedoresVue', 
        component: proveedoresComponent 
    },
    { 
        path: '/productos', 
        name: 'productosVue', 
        component: productosComponent 
    },
    { 
        path: '/compras', 
        name: 'comprasVue', 
        component: comprasComponent 
    },
    { 
        path: '/ventas', 
        name: 'ventasVue', 
        component: ventasComponent 
    },
    { 
        path: '/promociones', 
        name: 'promocionesVue', 
        component: promocionesComponent 
    },
    { 
        path: '/pedidos', 
        name: 'pedidosVue', 
        component: pedidosComponent 
    },
];
const router = createRouter({
    history: createWebHashHistory(import.meta.env.BASE_URL),
    routes
});
export default router;
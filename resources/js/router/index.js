import { createRouter, createWebHashHistory } from "vue-router";

const HomeComponent = () => import('../components/Home.vue');
const CategoriasComponent = () => import('../components/categorias.vue');
const subCategoriasComponent = () => import('../components/subCategorias.vue');
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
];
const router = createRouter({
    history: createWebHashHistory(import.meta.env.BASE_URL),
    routes
});
export default router;
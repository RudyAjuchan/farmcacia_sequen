import { createRouter, createWebHashHistory } from "vue-router";

const HomeComponent = () => import('../components/Home.vue');
const CategoriasComponent = () => import('../components/categorias.vue');
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
];
const router = createRouter({
    history: createWebHashHistory(import.meta.env.BASE_URL),
    routes
});
export default router;
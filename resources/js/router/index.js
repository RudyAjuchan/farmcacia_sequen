import { createRouter, createWebHashHistory } from "vue-router";

const CategoriasComponent = () => import('../components/categorias.vue');
const routes = [
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
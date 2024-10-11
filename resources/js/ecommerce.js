import './bootstrap';

import { createApp } from 'vue';
import RouterWeb from './router/ecommerce_router';

/* CONFIGURACIÓN AXIOS */
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('ERROR AL OBTENER TOKEN!');
}
/* FIN CONFIGURACIÓN AXIOS */

// Vuetify
import '@mdi/font/css/materialdesignicons.css'
import '@fortawesome/fontawesome-free/css/all.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import VueHtmlToPaper from 'vue-html-to-paper';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
const options = {
    name: 'Imprimir',
    specs: [
        'fullscreen=yes',
        'titlebar=yes',
        'scrollbars=yes'
    ],
    styles: [
        'https://cdn.jsdelivr.net/npm/vuetify@3.3.23/dist/vuetify.min.css'
    ],
    autoClose: true,
    windowTitle: window.document.title,
}

const vuetify = createVuetify({
    components,
    directives
})

const app = createApp({

});

app.use(RouterWeb);
app.use(vuetify);
app.use(VueHtmlToPaper, options);
app.mount('#app');
<template>
    <div>
        <navBar :logo="imgLogo" :cantidad_producto="cant_producto" ref="compNavBar"></navBar>

        <section class="py-5">
            <div class="container">
                <h1 class="text-center mb-4"><b>Contáctanos</b></h1>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <h3 class="text-center">Información de Contacto</h3>
                        <ul class="list-unstyled text-center mb-4">
                            <li><b>Teléfono:</b> +502 4399 5107</li>
                            <li><b>Email:</b> farmaciasequen@gmail.com</li>
                            <li><b>Dirección:</b> Aldea San Jacinto, Chimaltenango</li>
                            <li><b>Horario:</b> Lunes a Viernes 8:00 am - 9:30 pm</li>
                            <li><b>Sábados:</b> 9:00 am - 9:00 pm</li>
                            <li><b>Domingos:</b> 7:00 am - 9:00 am y 12:00 pm - 9:30 pm</li>
                        </ul>

                        <h3 class="text-center mb-4">Formulario de Contacto</h3>
                        <form @submit.prevent="submitForm">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" v-model="form.nombre" class="form-control" id="nombre"
                                    placeholder="Tu nombre completo" />
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" v-model="form.email" class="form-control" id="email"
                                    placeholder="nombre@ejemplo.com" />
                            </div>
                            <div class="mb-3">
                                <label for="mensaje" class="form-label">Mensaje</label>
                                <textarea v-model="form.mensaje" class="form-control" id="mensaje" rows="4"
                                    placeholder="Escribe tu mensaje"></textarea>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5" style="background-color: #CCFBF1;">
            <div class="container">
                <h2 class="text-center mb-4"><b>Ubícanos en el mapa</b></h2>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d4426.349418101898!2d-90.77400878911878!3d14.716577988553597!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTTCsDQzJzAwLjEiTiA5MMKwNDYnMjUuMSJX!5e0!3m2!1ses-419!2sgt!4v1728945793012!5m2!1ses-419!2sgt"
                            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </section>

        <Footer></Footer>

        <v-overlay :model-value="overlay" persistent style="background-color: black; opacity: 0.8;"
            class="align-center justify-center">
            <v-card style="background-color: transparent; border: none;" flat>
                <v-card-text>

                    <h3 class="text-center text-white"> Cargando datos </h3>
                </v-card-text>
                <v-card-actions class="justify-center aling-center">

                    <v-progress-circular color="success" indeterminate size="64"></v-progress-circular>
                </v-card-actions>
            </v-card>
        </v-overlay>
    </div>
</template>
<script>
import navBar from './subcomponents/navBar.vue'
import Footer from './subcomponents/footer.vue'
import { useCarritoStore } from '../../store/carrito';
import Swal from 'sweetalert2';
export default {
    name: 'contactoVue',
    components: {
        navBar,
        Footer,
    },
    data() {
        return {
            form: {
                nombre: '',
                email: '',
                mensaje: '',
            },
            imgLogo: "/images/image.png",
            store: null,
            cant_producto: 0,
            dialogCompra: false,
            overlay: false,
        }
    },
    methods: {
        async submitForm() {
            this.overlay = true
            try {
                const response = await axios.post('/contacto', this.form);
                
                if (response.status === 200) {
                    this.overlay = false;
                    Swal.fire({
                        title: '¡Información!',
                        text: 'Se ha enviado el correo correctamente, te responderemos lo más pronto posible, muchas gracias por escribirnos.',
                        icon: 'success',
                        allowOutsideClick: false,
                        confirmButtonColor: '#00A38C',
                        customClass: {
                            confirmButton: 'custom-confirm-button',  // Clase personalizada
                        }
                    });
                    // Limpia el formulario después de enviarlo
                    this.form.nombre = '';
                    this.form.email = '';
                    this.form.mensaje = '';
                } else {
                    this.overlay = false;
                    alert('Hubo un problema al enviar el correo.');
                }
            } catch (error) {
                this.overlay = false;
                console.error('Error al enviar el correo:', error);
                alert('Hubo un error al enviar el formulario.');
            }
        },
    },
    mounted() {
    },
    created(){
        this.store = useCarritoStore();
        this.store.obtenerProductos();
        this.cant_producto = this.store.productos.length;
    }
}
</script>
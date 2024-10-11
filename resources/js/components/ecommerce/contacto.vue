<template>
    <div>
        <navBar :logo="imgLogo"></navBar>

        <section class="py-5">
            <div class="container">
                <h1 class="text-center mb-4"><b>Contáctanos</b></h1>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <h3 class="text-center">Información de Contacto</h3>
                        <ul class="list-unstyled text-center mb-4">
                            <li><b>Teléfono:</b> +502 4399 5107</li>
                            <li><b>Email:</b> contacto@farmaciasequen.com</li>
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
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387190.2799073087!2d-74.25987147563802!3d40.69767006353386!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c2f6d35326d723%3A0x695b026b90b11a05!2sManhattan%2C+NY!5e0!3m2!1sen!2sus!4v1630328234714!5m2!1sen!2sus"
                            width="100%" height="450" style="border: 0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </section>

        <Footer></Footer>
    </div>
</template>
<script>
import navBar from './subcomponents/navBar.vue'
import Footer from './subcomponents/footer.vue'
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
        }
    },
    methods: {
        submitForm() {
            // Lógica para enviar el formulario
            console.log("Formulario enviado:", this.form);
            // Aquí puedes agregar la lógica para enviar el formulario
            // a tu backend o servicio correspondiente.
        },
    },
    mounted() {
    }
}
</script>
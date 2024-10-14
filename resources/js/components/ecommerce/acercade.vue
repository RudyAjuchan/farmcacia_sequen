<template>
    <div>
        <navBar :logo="imgLogo"></navBar>
        <section class="hero-about">
            <h1>Acerca de Nosotros</h1>
        </section>

        <!-- About Us Section -->
        <section class="section-about">
            <div class="container">
                <h2 class="text-center mb-5"><b>¿Quiénes Somos?</b></h2>
                <div class="row">
                    <div class="col-md-6">
                        <h3>Misión</h3>
                        <p>
                            Ofrecer a nuestros valiosos clientes una amplia gama de productos de salud y 
                            bienestar de alta calidad a precios accesibles. Nuestra dedicación es ser un 
                            faro de confianza para la comunidad, proporcionando soluciones de salud genuinas 
                            y mejorando la calidad de vida de quienes confían en nosotros.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h3>Visión</h3>
                        <p>
                            Ser la principal fuente de salud y bienestar en nuestra comunidad, contribuyendo a 
                            mejorar la calidad de vida de las personas mediante productos y servicios de alta 
                            calidad. Buscamos expandir nuestra presencia y ser un faro confiable en temas de 
                            salud y bienestar.
                        </p>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-12">
                        <h3>Historia</h3>
                        <p>
                            La Farmacia Sequén, surgió a mediados del año 2021 en el tranquilo pueblo de la Aldea San Jacinto, municipio de Chimaltenango, departamento de Chimaltenango donde ha estado brindando cuidado y apoyo a la salud de la comunidad. Es un emprendimiento familiar que ha florecido con un compromiso inquebrantable con el bienestar de sus clientes.
                            Desde sus modestos comienzos, Farmacia Sequén se ha convertido en un faro de salud en la comunidad. Ofreciendo una amplia gama de productos, desde vitaminas y jarabes hasta pastillas e inyecciones, el compromiso de la farmacia con la calidad y la autenticidad de sus productos ha ganado la confianza y el respeto de todos sus clientes.
                            La atención es amable y cálida, como la de una familia extendida que siempre está dispuesta a ayudar. No importa si alguien busca alivio para un resfriado, consejos sobre suplementos vitamínicos o soluciones para afecciones más serias; Farmacia Sequén se enorgullece de estar allí para satisfacer cada necesidad de salud., un emprendimiento familiar que ha florecido con un compromiso inquebrantable con el bienestar de sus clientes.
                            Farmacia Sequén es mucho más que una simple farmacia; es un pilar de la salud y el bienestar, esta farmacia ha demostrado que el amor por la salud y el cuidado genuino pueden marcar la diferencia en la vida de las personas.
                        </p>
                    </div>
                    <div class="col-md-6 mt-5">
                        <h3>Valores</h3>
                        <ul>
                            <li><b>Compromiso:</b> Con la salud y bienestar de nuestros clientes.</li>
                            <li><b>Confianza:</b> Productos y servicios confiables.</li>
                            <li><b>Calidad:</b> con los productos.</li>
                            <li><b>Ética profesional:</b> con los estándares de salud.</li>
                            <li><b>Cuidado y amabilidad:</b> con los productos y nuestros clientes.</li>
                        </ul>
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
    name: 'acercadeVue',
    components:{
        navBar,
        Footer,
    },
    data() {
        return {
            imgLogo: "/images/image.png",
        }
    },
    methods: {
    },
    mounted() {
    }
}
</script>

<style scoped>
.hero-about {
    height: 300px;
    background-image: url('/images/doctors.jpg');
    /* Reutiliza la imagen del hero */
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-about h1 {
    color: white;
    font-size: 3rem;
}

.section-about {
    padding: 50px 0;
}
</style>
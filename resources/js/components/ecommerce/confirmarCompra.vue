<template>
    <div>
        <!-- Navbar -->
        <navBar :logo="imgLogo" :cantidad_producto="cant_producto" ref="compNavBar"></navBar>
        <div id="confirmarSection">
            <v-container v-if="user.name" class="mt-5">
                <v-card>
                    <v-card-title>Confirma tus datos</v-card-title>
                    <v-card-text>
                        <v-table>
                            <tbody>
                                <tr>
                                    <th>Nombre</th>
                                    <td>{{ user.name }}</td>
                                </tr>
                                <tr>
                                    <th>Correo</th>
                                    <td>{{ user.email }}</td>
                                </tr>
                                <tr>
                                    <th>Dirección *</th>
                                    <td><input :value="user.direccion" class="form-control"
                                            placeholder="Introduce tu dirección (obligatorio)"
                                            @input="setDireccion($event.target.value)"></td>
                                </tr>
                                <tr>
                                    <th>Nit</th>
                                    <td><input :value="user.nit" class="form-control" placeholder="Introduce tu Nit"
                                            @input="setNit($event.target.value)"></td>
                                </tr>
                                <tr>
                                    <th>Telefono *</th>
                                    <td><input :value="user.telefono" class="form-control"
                                            placeholder="Introduce tu número de celular o teléfono (obligatorio)"
                                            @input="setTelefono($event.target.value)"></td>
                                </tr>
                            </tbody>
                        </v-table>


                    </v-card-text>
                </v-card>
                <div class="d-flex justify-content-end align-items-center mt-4">
                    <v-btn color="primary" variant="tonal" @click="finalizarCompra()"
                        :disabled="cant_producto>0 ? false: true">Finalizar compra</v-btn>
                </div>
            </v-container>
        </div>
        <!-- Footer -->
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
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
export default {
    name: 'confirmarVue',
    components: {
        navBar,
        Footer,
    },
    data() {
        return {
            store: null,
            cant_producto: 0,
            imgLogo: "/images/image.png",
            user: [],
            productos: [],
            overlay: false,
        }
    },
    methods: {
        getUser(){
            axios.get('/user').then(res => {
                this.user = res.data.name ? res.data : null ;
            }).catch(error => {
                console.error('Error fetching user:', error);
            });
        },
        async finalizarCompra(){
            this.overlay = true
            if(this.user.direccion!=null && this.user.telefono!=null){
                let total = this.productos.reduce((total, item) => {
                    return total + item.subtotal;
                }, 0);
                const response = await axios.post('/pedidos', {
                    productos: this.productos,
                    usuario: this.user,
                    total: total,
                });

                if (response.status === 201) {
                    this.overlay = false
                    Swal.fire({
                        title: '¡Información de compra!',
                        text: 'La compra se realizó con éxito, por favor espere de 1 a 2 días hábiles para entregar según zona de ubicación, le hemos enviado también el detalle de su pedido a su correo',
                        icon: 'success',
                        allowOutsideClick: false,
                        confirmButtonColor: '#00A38C',
                        customClass: {
                            confirmButton: 'custom-confirm-button',  // Clase personalizada
                        }
                    }).then(re => {
                        this.store.resetProducto();
                        this.store.obtenerProductos();
                        this.cant_producto = this.store.productos.length;
                        this.$router.push('/');
                    })
                }else{
                    this.overlay = false;
                    toast.error("¡Hubo un error al guardar los datos!", {
                        autoClose: 3000,
                        position: toast.POSITION.BOTTOM_RIGHT,
                    });
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        // Manejo de otros errores o código adicional
                    }
                }
            }else{
                Swal.fire({
                    title: '¡Falta dirección o telefono en ingresar!',
                    icon: 'error',
                    allowOutsideClick: false,
                    confirmButtonColor: '#00A38C',
                    customClass: {
                        confirmButton: 'custom-confirm-button',  // Clase personalizada
                    }
                });
            }
        },
        setDireccion(value){
            this.user.direccion = value;
        },
        setNit(value){
            this.user.nit = value;
        },
        setTelefono(value){
            this.user.telefono = value;
        }
    },
    mounted() {
        this.getUser();
    },
    created(){
        this.store = useCarritoStore();
        this.store.obtenerProductos();
        this.productos = this.store.productos;
        this.cant_producto = this.store.productos.length;
    }
}
</script>

<style>
    .text-primary { color: #1E617B; }
    .bg-accent { background-color: #65C5B9; }
    .table-hover tbody tr:hover {
        background-color: #D3EEEB; /* Color de fondo al hacer hover */
    }
    #confirmarSection{
        min-height: calc(100vh - 224px);
    }
    .custom-confirm-button {
        color: #ffffff;
    }
</style>
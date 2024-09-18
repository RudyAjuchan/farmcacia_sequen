<template>
    <div>
        <h2 class="text-center mb-10">Productos</h2>
        <div class="text-end my-5">
            <v-btn variant="tonal" color="primary" @click="card = true, cardAction = 'nuevo'"><i
                    class="fa-solid fa-plus"></i> Nuevo</v-btn>
        </div>
        <v-text-field variant="outlined" label="buscar" v-model="search"></v-text-field>
        <v-data-table :headers="headers" :items="itemsProductos" :search="search">
            <template v-slot:[`item.created_at`]="{ item }">
                {{ new Date(item.created_at).toLocaleString() }}
            </template>
            <template v-slot:[`item.updated_at`]="{ item }">
                {{ new Date(item.updated_at).toLocaleString() }}
            </template>
            <template v-slot:[`item.actions`]="{ item }">
                <v-btn color="secondary" class="me-2" variant="tonal" @click="editar(item)"><i
                        class="fa-solid fa-pen"></i></v-btn>
                <v-btn color="red" variant="tonal" @click="dialogEliminar=true, dataSave.id = item.id"><i class="fa-solid fa-trash"></i></v-btn>
            </template>
        </v-data-table>
    </div>

    <v-dialog v-model="card" persistent>
        <v-card width="1000" class="mx-auto">
            <v-card-title class="text-center">{{ cardAction == 'nuevo' ? 'Registro de Producto' : 'Actualizar Producto' }}</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12"><v-autocomplete variant="outlined" label="Seleccione el proveedor" v-model="dataSave.proveedor" :items="itemsProveedores" 
                        item-title="nombre" item-value="id" :error-messages="errors.proveedor" append-inner-icon="mdi-plus"
                        @click:append-inner="card=false, cardProveedor=true"></v-autocomplete></v-col>
                    <v-col cols="12"><v-text-field label="Nombre" v-model="dataSave.nombre"
                            :error-messages="errors.nombre" variant="outlined"></v-text-field></v-col>
                    <v-col cols="12"><v-textarea label="Descripción"
                            v-model="dataSave.descripcion" variant="outlined" :error-messages="errors.descripcion"></v-textarea></v-col>
                </v-row>
            </v-card-text>
            <v-card-actions>
                <v-btn color="secondary" v-if="cardAction=='nuevo'" variant="tonal" @click="guardar">Guardar</v-btn>
                <v-btn color="secondary" v-if="cardAction=='editar'" variant="tonal" @click="guardarCambios">Guardar Cambios</v-btn>
                <v-btn color="red" variant="tonal" @click="card = false, limpiar()">Cancelar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-overlay :model-value="overlay" persistent style="background-color: black; opacity: 0.8;"
        class="align-center justify-center">
        <v-card style="background-color: transparent; border: none;" flat>
            <v-card-text>

                <h1 class="text-center text-white"> Cargando datos </h1>
            </v-card-text>
            <v-card-actions class="justify-center aling-center">

                <v-progress-circular color="success" indeterminate size="64"></v-progress-circular>
            </v-card-actions>
        </v-card>
    </v-overlay>

    <v-dialog v-model="dialogEliminar" max-width="500" persistent>
        <v-card>
            <v-card-title class="headline">Confirmar acción</v-card-title>
            <v-card-text>¿Estás seguro de que deseas eliminar el dato?</v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="green darken-1" text @click="eliminar">Confirmar</v-btn>
                <v-btn color="red darken-1" text @click="dialogEliminar = false">Cancelar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- DIALOG PARA INSERTAR DATOS DE PROVEEDORES -->
    <v-dialog v-model="cardProveedor" persistent>
        <v-card width="1000" class="mx-auto">
            <v-card-title class="text-center">Registro de Proveedor</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12">
                        <v-text-field label="Nombre" v-model="dataSave2.nombre"
                        :error-messages="errors2.nombre" variant="outlined"></v-text-field>
                        <v-text-field label="Contacto"
                        v-model="dataSave2.contacto" variant="outlined"></v-text-field>
                        <v-text-field label="Dirección"
                        v-model="dataSave2.direccion" variant="outlined"></v-text-field>
                        <v-text-field label="Telefono"
                        v-model="dataSave2.telefono" variant="outlined" :error-messages="errors2.telefono"></v-text-field>
                        <v-text-field label="Correo electrónico"
                        v-model="dataSave2.email" variant="outlined" :error-messages="errors2.email"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-actions>
                <v-btn color="secondary" variant="tonal" @click="guardarProveedor">Guardar</v-btn>
                <v-btn color="red" variant="tonal" @click="cardProveedor=false,card = true, limpiar2()">Cancelar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

</template>
<script>
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
export default {
    name: 'subCategoriasVue',
    data() {
        return {
            headers: [
                { title: 'Nombre', key: 'nombre', align: 'start' },
                { title: 'Descripción', key: 'descripcion', align: 'start' },
                { title: 'Proveedor', key: 'proveedor.nombre', align: 'start' },
                { title: 'Stock', key: 'stock', align: 'start' },
                { title: 'Creado', key: 'created_at', align: 'center' },
                { title: 'Última actualización', key: 'updated_at', align: 'center' },
                { title: 'Opciones', key: 'actions', align: 'center' },
            ],
            itemsProductos: [],
            itemsProveedores: [],
            card: false,
            cardProveedor: false,
            cardAction: '',
            search: '',
            dataSave: {
                id: '',
                nombre: '',
                descripcion: '',
                proveedor: null,
            },
            errors: {},
            errors2: {},
            overlay: false,
            dialogEliminar: false,
            dataSave2: {
                id: '',
                nombre: '',
                contacto: '',
                direccion: '',
                telefono: '',
                email: '',
            },
        }
    },
    methods: {
        getData() {
            this.overlay = true;
            axios.get("/productos").then(res => {
                this.overlay = false;
                this.itemsProductos = res.data;
            }).catch((err) => {
                this.overlay = false;
                Swal.fire({
                    title: '¡Hubo un error al obtener datos!',
                    icon: 'error',
                    allowOutsideClick: false,
                    confirmButtonColor: '#00A38C',
                    customClass: {
                        confirmButton: 'custom-confirm-button',
                    }
                });
            });
        },
        getProveedores() {
            this.overlay = true;
            axios.get("/proveedores").then(res => {
                this.overlay = false;
                this.itemsProveedores = res.data;
            }).catch((err) => {
                this.overlay = false;
                Swal.fire({
                    title: '¡Hubo un error al obtener datos!',
                    icon: 'error',
                    allowOutsideClick: false,
                    confirmButtonColor: '#00A38C',
                    customClass: {
                        confirmButton: 'custom-confirm-button',
                    }
                });
            });
        },
        limpiar(){
            this.dataSave.nombre = '';
            this.dataSave.descripcion = '';
            this.dataSave.categoria = '';
            this.dataSave.id = null;
            this.errors = {};
        },
        limpiar2(){
            this.dataSave2.nombre = '';
            this.dataSave2.contacto = '';
            this.dataSave2.direccion = '';
            this.dataSave2.telefono = '';
            this.dataSave2.email = '';
            this.dataSave2.id = null;
            this.errors2 = {};
        },
        guardar() {
            this.overlay = true;
            axios.post("/productos", {
                nombre: this.dataSave.nombre,
                descripcion: this.dataSave.descripcion,
                proveedor: this.dataSave.proveedor,
            }).then(res => {
                this.overlay = false;
                toast.success("Se han guardado los datos", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                this.limpiar();
                this.card = false;
                this.getData();
            }).catch((error) => {
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
            });
        },
        guardarProveedor() {
            this.overlay = true;
            axios.post("/proveedores", {
                nombre: this.dataSave2.nombre,
                contacto: this.dataSave2.contacto,
                direccion: this.dataSave2.direccion,
                telefono: this.dataSave2.telefono,
                email: this.dataSave2.email,
            }).then(res => {
                this.overlay = false;
                toast.success("Se han guardado los datos", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                this.limpiar2();
                this.card = true;
                this.cardProveedor = false;
                this.getProveedores();
                this.dataSave.proveedor = res.data.data.id;
            }).catch((error) => {
                this.overlay = false;
                toast.error("¡Hubo un error al guardar los datos!", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                if (error.response && error.response.status === 422) {
                    this.errors2 = error.response.data.errors;
                } else {
                    // Manejo de otros errores o código adicional
                }
            });
        },
        editar(item) {
            this.card = true;
            this.cardAction = 'editar';
            this.dataSave.nombre = item.nombre;
            this.dataSave.descripcion = item.descripcion;
            this.dataSave.proveedor = item.proveedor.id;
            this.dataSave.id = item.id;
        },
        guardarCambios(){
            this.overlay = true;
            axios.put(`/productos/${this.dataSave.id}`,{
                nombre: this.dataSave.nombre,
                descripcion: this.dataSave.descripcion,
                proveedor: this.dataSave.proveedor,
            }).then(res => {
                this.overlay = false;
                toast.success("Se han guardado los datos", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                this.limpiar();
                this.card = false;
                this.getData();
            }).catch((error) => {
                this.overlay = false;
                toast.error("¡Hubo un error al guardar cambios a los datos!", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    // Manejo de otros errores o código adicional
                }
            });
        },
        eliminar() {
            this.overlay = true;
            axios.delete(`/productos/${this.dataSave.id}`).then(res=>{
                this.overlay = false;
                toast.success("Se han guardado los datos", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                this.dialogEliminar = false;
                this.getData();
            }).catch((error) => {
                this.overlay = false;
                toast.error("¡Hubo un error al eliminar los datos!", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    // Manejo de otros errores o código adicional
                }
            });
        }
    },
    mounted() {
        this.getData();
        this.getProveedores();
    }
}
</script>
<style>
.custom-confirm-button {
    color: #ffffff;
    /* Cambia este valor por el color que desees */
}
</style>
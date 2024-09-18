<template>
    <div>
        <h2 class="text-center mb-10">Proveedores</h2>
        <div class="text-end my-5">
            <v-btn variant="tonal" color="primary" @click="card = true, cardAction = 'nuevo'"><i
                    class="fa-solid fa-plus"></i> Nuevo</v-btn>
        </div>
        <v-text-field variant="outlined" label="buscar" v-model="search"></v-text-field>
        <v-data-table :headers="headers" :items="itemsProveedor" :search="search">
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
            <v-card-title class="text-center">{{ cardAction == 'nuevo' ? 'Registro de Proveedor' : 'Actualizar proveedor' }}</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12">
                        <v-text-field label="Nombre" v-model="dataSave.nombre"
                        :error-messages="errors.nombre" variant="outlined"></v-text-field>
                        <v-text-field label="Contacto"
                        v-model="dataSave.contacto" variant="outlined"></v-text-field>
                        <v-text-field label="Dirección"
                        v-model="dataSave.direccion" variant="outlined"></v-text-field>
                        <v-text-field label="Telefono"
                        v-model="dataSave.telefono" variant="outlined" :error-messages="errors.telefono"></v-text-field>
                        <v-text-field label="Correo electrónico"
                        v-model="dataSave.email" variant="outlined" :error-messages="errors.email"></v-text-field>
                    </v-col>
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
</template>
<script>
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
export default {
    name: 'proveedoresVue',
    data() {
        return {
            headers: [
                { title: 'Nombre', key: 'nombre', align: 'start' },
                { title: 'Contacto', key: 'contacto', align: 'start' },
                { title: 'Dirección', key: 'direccion', align: 'start' },
                { title: 'Teléfono', key: 'telefono', align: 'start' },
                { title: 'Correo', key: 'email', align: 'start' },
                { title: 'Creado', key: 'created_at', align: 'center' },
                { title: 'Última actualización', key: 'updated_at', align: 'center' },
                { title: 'Opciones', key: 'actions', align: 'center' },
            ],
            itemsProveedor: [],
            card: false,
            cardAction: '',
            search: '',
            dataSave: {
                id: '',
                nombre: '',
                contacto: '',
                direccion: '',
                telefono: '',
                email: '',
            },
            errors: {},
            overlay: false,
            dialogEliminar: false,
        }
    },
    methods: {
        getData() {
            this.overlay = true;
            axios.get("/proveedores").then(res => {
                this.overlay = false;
                this.itemsProveedor = res.data;
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
            this.dataSave.contacto = '';
            this.dataSave.direccion = '';
            this.dataSave.telefono = '';
            this.dataSave.email = '';
            this.dataSave.id = null;
            this.errors = {};
        },
        guardar() {
            this.overlay = true;
            axios.post("/proveedores", {
                nombre: this.dataSave.nombre,
                contacto: this.dataSave.contacto,
                direccion: this.dataSave.direccion,
                telefono: this.dataSave.telefono,
                email: this.dataSave.email,
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
        editar(item) {
            this.card = true;
            this.cardAction = 'editar';
            this.dataSave.nombre = item.nombre;
            this.dataSave.contacto = item.contacto;
            this.dataSave.direccion = item.direccion;
            this.dataSave.telefono = item.telefono;
            this.dataSave.email = item.email;
            this.dataSave.id = item.id;
        },
        guardarCambios(){
            this.overlay = true;
            console.log(this.dataSave.id);
            axios.put(`/proveedores/${this.dataSave.id}`,{
                nombre: this.dataSave.nombre,
                contacto: this.dataSave.contacto,
                direccion: this.dataSave.direccion,
                telefono: this.dataSave.telefono,
                email: this.dataSave.email,
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
            axios.delete(`/proveedores/${this.dataSave.id}`).then(res=>{
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
    }
}
</script>
<style>
.custom-confirm-button {
    color: #ffffff;
    /* Cambia este valor por el color que desees */
}
</style>
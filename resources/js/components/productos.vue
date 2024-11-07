<template>
    <div>
        <h2 class="text-center mb-10">Productos</h2>
        <div class="text-end my-5">
            <v-btn variant="tonal" color="primary" @click="card = true, cardAction = 'nuevo'"><i
                    class="fa-solid fa-plus"></i> Nuevo</v-btn>
        </div>
        <v-text-field variant="outlined" label="buscar" v-model="search"></v-text-field>
        <v-data-table :headers="headers" :items="itemsProductos" :search="search">
            <template v-slot:[`item.descripcion`]="{ item }">
                {{ truncateText(item.descripcion, 50) }}
            </template>
            <template v-slot:[`item.imagen`]="{ item }">
                <img width="60" :src="item.imagen ? `/storage/${item.imagen}` : '/storage/no-disponible.png'" alt="Imagen del producto" />
            </template>
            <template v-slot:[`item.created_at`]="{ item }">
                {{ new Date(item.created_at).toLocaleString() }}
            </template>
            <template v-slot:[`item.updated_at`]="{ item }">
                {{ new Date(item.updated_at).toLocaleString() }}
            </template>
            <template v-slot:[`item.actions`]="{ item }">
                <v-btn color="secondary" class="me-2 mt-1" variant="tonal" @click="editar(item)"><i
                        class="fa-solid fa-pen"></i></v-btn>
                <v-btn color="red" variant="tonal" @click="dialogEliminar=true, dataSave.id = item.id" class="me-2 mt-1"><i class="fa-solid fa-trash"></i></v-btn>
            </template>
        </v-data-table>
    </div>

    <v-dialog v-model="card" persistent>
        <v-card width="1000" class="mx-auto">
            <v-card-title class="text-center">{{ cardAction == 'nuevo' ? 'Registro de Producto' : 'Actualizar Producto' }}</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="6"><v-autocomplete variant="outlined" label="Seleccione el proveedor" v-model="dataSave.proveedor" :items="itemsProveedores" 
                        item-title="nombre" item-value="id" :error-messages="errors.proveedor" append-inner-icon="mdi-plus"
                        @click:append-inner="card=false, cardProveedor=true"></v-autocomplete>
                    </v-col>
                    <v-col cols="6">
                        <v-autocomplete variant="outlined" label="Seleccione la subcategoría" v-model="dataSave.subcategoria" :items="itemsSubCategoria" 
                        item-title="nombre" item-value="id" :error-messages="errors.subcategoria" append-inner-icon="mdi-plus"
                        @click:append-inner="card=false, cardSubcategoria=true"></v-autocomplete>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field label="Nombre" v-model="dataSave.nombre"
                            :error-messages="errors.nombre" variant="outlined"></v-text-field></v-col>
                    <v-col cols="6"><v-textarea label="Descripción"
                            v-model="dataSave.descripcion" variant="outlined" :error-messages="errors.descripcion"></v-textarea></v-col>
                    <v-col cols="6">
                        <file-pond name="file" ref="pond"
                            label-idle='Arrastra tu archivo o <span class="filepond--label-action"> Sube uno </span>'
                            :server="serverOptions" accepted-file-types="image/jpeg, image/png" allow-multiple="false"
                            instant-upload="true" v-model="dataSave.imagen" :files="preloadedFiles"
                            @removefile="handleRemoveFile"/>
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

    <!-- DIALOG PARA GUARDAR SUBCATEGORÍAS -->
    <v-dialog v-model="cardSubcategoria" persistent>
        <v-card width="1000" class="mx-auto">
            <v-card-title class="text-center">Registro de Sub Categorías</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12"><v-autocomplete variant="outlined" label="Seleccione la categoría" v-model="dataSubCategoria.categoria" 
                        :items="itemsCategorias" item-title="nombre" item-value="id" :error-messages="errorsSubCategorias.categoria"
                        append-inner-icon="mdi-plus" @click:append-inner="cardSubcategoria=false, cardCategoria=true"></v-autocomplete></v-col>
                    <v-col cols="12"><v-text-field label="Nombre" v-model="dataSubCategoria.nombre"
                            :error-messages="errorsSubCategorias.nombre" variant="outlined"></v-text-field></v-col>
                    <v-col cols="12"><v-textarea label="Descripción"
                            v-model="dataSubCategoria.descripcion" variant="outlined"></v-textarea></v-col>
                </v-row>
            </v-card-text>
            <v-card-actions>
                <v-btn color="secondary" variant="tonal" @click="guardarSubcategoria">Guardar</v-btn>
                <v-btn color="red" variant="tonal" @click="cardSubcategoria = false, limpiarSubCategoria(), card = true">Cancelar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- DIALOG PARA GUARDAR CATEGORÍAS -->
    <v-dialog v-model="cardCategoria" persistent>
        <v-card width="1000" class="mx-auto">
            <v-card-title class="text-center">Registro de Categorías</v-card-title>
            <v-card-text>
                <v-row>
                    <v-col cols="12"><v-text-field label="Nombre" v-model="dataCategoria.nombre"
                            :error-messages="errorsCategoria.nombre" variant="outlined"></v-text-field></v-col>
                    <v-col cols="12"><v-textarea label="Descripción"
                            v-model="dataCategoria.descripcion" variant="outlined"></v-textarea></v-col>
                </v-row>
            </v-card-text>
            <v-card-actions>
                <v-btn color="secondary" variant="tonal" @click="guardarCategoria">Guardar</v-btn>
                <v-btn color="red" variant="tonal" @click="cardCategoria = false, limpiarCategoria(), cardSubcategoria = true">Cancelar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

</template>
<script>
import Swal from 'sweetalert2';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
/* Importaciones para manejo de imágenes */
import vueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
// Crear el componente FilePond con los plugins necesarios
const FilePond = vueFilePond(FilePondPluginImagePreview, FilePondPluginFileValidateType);
export default {
    name: 'subCategoriasVue',
    components: {
        FilePond,
    },
    data() {
        return {
            headers: [
                { title: 'Nombre', key: 'nombre', align: 'start' },
                { title: 'Descripción', key: 'descripcion', align: 'start' },
                { title: 'Proveedor', key: 'proveedor.nombre', align: 'start' },
                { title: 'Stock', key: 'stock', align: 'start' },
                { title: 'Sub-Categoría', key: 'subcategoria.nombre', align: 'start' },
                { title: 'Categoría', key: 'subcategoria.categoria.nombre', align: 'start' },
                { title: 'Imagen', key: 'imagen', align: 'start' },
                { title: 'Creado', key: 'created_at', align: 'center' },
                { title: 'Última actualización', key: 'updated_at', align: 'center' },
                { title: 'Opciones', key: 'actions', align: 'center' },
            ],
            itemsProductos: [],
            itemsProveedores: [],
            itemsSubCategoria: [],
            card: false,
            cardProveedor: false,
            cardSubcategoria: false,
            cardCategoria: false,
            cardAction: '',
            search: '',
            dataSave: {
                id: '',
                nombre: '',
                descripcion: '',
                proveedor: null,
                subcategoria: null,
                imagen: '',
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
            dataSubCategoria: {
                id: '',
                nombre: '',
                descripcion: '',
                categoria: null,
            },
            itemsCategorias: [],
            errorsSubCategorias: {},
            dataCategoria: {
                id: '',
                nombre: '',
                descripcion: '',
            },
            errorsCategoria: {},

            /* para manejo de imágenes */
            serverOptions: {
                process: {
                    url: '/upload', // Ruta en Laravel
                    method: 'POST',
                    withCredentials: false,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    onload: response => {
                        const responseData = JSON.parse(response);
                        // Asigna la respuesta a `data.imagen`
                        this.dataSave.imagen = responseData.fileId;
                        return responseData.fileId;
                    },
                },
                revert: (uniqueFileId, load, error) => {
                    let imagenComparar = ''
                    if(this.preloadedFiles.length>0){
                        imagenComparar = this.preloadedFiles[0].image
                    }
                    if(uniqueFileId!=imagenComparar){
                        axios.post(`/uploadDelete`,{ file: uniqueFileId })
                            .then(response => load())
                            .catch(err => error(err.message));
                    }else{
                        this.dataSave.imagen = ''
                    }
                },
                load: (uniqueFileId, load) => {
                fetch(uniqueFileId)
                    .then(res => res.blob())
                    .then(load);
                },
            },
            preloadedFiles: [],
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
        getSubcategorias() {
            this.overlay = true;
            axios.get("/subcategorias").then(res => {
                this.overlay = false;
                this.itemsSubCategoria = res.data;
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
        getCategorias() {
            this.overlay = true;
            axios.get("/categorias").then(res => {
                this.overlay = false;
                this.itemsCategorias = res.data;
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
            this.dataSave.id = null;
            this.dataSave.subcategoria = null;
            this.dataSave.imagen = '';
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
        limpiarCategoria(){
            this.dataCategoria.nombre = '';
            this.dataCategoria.descripcion = '';
            this.errorsCategoria = {};
        },
        limpiarSubCategoria(){
            this.dataSubCategoria.nombre = '';
            this.dataSubCategoria.descripcion = '';
            this.dataSubCategoria.categoria = '';
            this.dataSubCategoria.id = null;
            this.errorsSubCategorias = {};
        },
        guardar() {
            this.overlay = true;
            axios.post("/productos", {
                nombre: this.dataSave.nombre,
                descripcion: this.dataSave.descripcion,
                proveedor: this.dataSave.proveedor,
                subcategoria: this.dataSave.subcategoria,
                imagen: this.dataSave.imagen,
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
            this.dataSave.subcategoria = item.subcategoria.id
            this.dataSave.id = item.id;

            if(item.imagen){
                this.dataSave.imagen = item.imagen
                const image = item.imagen.split("/")
                this.preloadedFiles = [{
                    source: `/storage/${item.imagen}`,
                    options: {
                        type: 'local',
                    },
                    image: image[1]
                }];
            }
        },
        guardarCambios(){
            this.overlay = true;
            axios.put(`/productos/${this.dataSave.id}`,{
                nombre: this.dataSave.nombre,
                descripcion: this.dataSave.descripcion,
                proveedor: this.dataSave.proveedor,
                imagen: this.dataSave.imagen,
                subcategoria: this.dataSave.subcategoria,
                image_antigua: this.preloadedFiles[0] ? this.preloadedFiles[0].image: '',
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
        },
        guardarSubcategoria(){
            this.overlay = true;
            axios.post("/subcategorias", {
                nombre: this.dataSubCategoria.nombre,
                descripcion: this.dataSubCategoria.descripcion,
                categoria: this.dataSubCategoria.categoria,
            }).then(res => {
                this.overlay = false;
                toast.success("Se han guardado los datos", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                this.limpiarSubCategoria();
                this.getSubcategorias();
                this.dataSave.subcategoria = res.data.data.id;
                this.cardSubcategoria = false;
                this.card = true;
            }).catch((error) => {
                this.overlay = false;
                toast.error("¡Hubo un error al guardar los datos!", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                if (error.response && error.response.status === 422) {
                    this.errorsSubCategorias = error.response.data.errors;
                } else {
                    // Manejo de otros errores o código adicional
                }
            });
        },
        guardarCategoria(){
            this.overlay = true;
            axios.post("/categorias", {
                nombre: this.dataCategoria.nombre,
                descripcion: this.dataCategoria.descripcion,
            }).then(res => {
                this.overlay = false;
                toast.success("Se han guardado los datos", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                this.limpiarCategoria();
                this.getCategorias();
                this.dataSubCategoria.categoria = res.data.data.id;
                this.cardCategoria = false;
                this.cardSubcategoria = true;
            }).catch((error) => {
                this.overlay = false;
                toast.error("¡Hubo un error al guardar los datos!", {
                    autoClose: 3000,
                    position: toast.POSITION.BOTTOM_RIGHT,
                });
                if (error.response && error.response.status === 422) {
                    this.errorsCategoria = error.response.data.errors;
                } else {
                    // Manejo de otros errores o código adicional
                }
            });
        },
        handleRemoveFile(){
            this.dataSave.imagen = ''
        },
        truncateText(text, maxLength) {
            if (text.length > maxLength) {
                let truncated = text.substring(0, maxLength);
                let lastSpace = truncated.lastIndexOf(' ');
                if (lastSpace > 0) {
                    truncated = truncated.substring(0, lastSpace);
                }
                return truncated + '...';
            }else{
                return text
            }
        },
    },
    mounted() {
        this.getData();
        this.getProveedores();
        this.getSubcategorias();
        this.getCategorias();
    }
}
</script>
<style>
.custom-confirm-button {
    color: #ffffff;
}
</style>
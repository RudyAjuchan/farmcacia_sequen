import { defineStore } from "pinia";

export const useCarritoStore = defineStore('carrito', {
    state: () => {
        return {
            productos: [],
            logueado: false,
        }
    },
    actions: {
        obtenerProductos() {
            this.productos = JSON.parse(localStorage.getItem('productos')) || [];
        },
        agregarProductos(producto) {
            const descuento = producto.promociones[0].promocion ? producto.promociones[0].promocion.descuento : 0;
            const productoExistente = this.productos.find(item => item.id === producto.id);
            if (productoExistente && (productoExistente.cantidad + 1) <= productoExistente.cantidad_restante) {
                productoExistente.cantidad += 1;
                productoExistente.descuento = productoExistente.descuentoOriginal * productoExistente.cantidad;
                productoExistente.subtotal = (productoExistente.precio * productoExistente.cantidad) - productoExistente.descuento;
            } else {
                this.productos.push({
                    id: producto.id,
                    nombre: producto.nombre,
                    cantidad: 1,
                    imagen: producto.imagen,
                    precio: producto.lote_productos.precio,
                    descuento: descuento,
                    descuentoOriginal: descuento,
                    cantidad_restante: producto.lote_productos.cantidad_restante,
                    subtotal: producto.lote_productos.precio - descuento,
                })
            }
    
            localStorage.setItem('productos', JSON.stringify(this.productos));
        },
        actualizarCantidad(id, cantidad, precio) {
            const producto = this.productos.find(item => item.id === id);
            if (producto && cantidad <= producto.cantidad_restante) {
                producto.cantidad = cantidad;
                producto.descuento = (producto.descuentoOriginal * cantidad)
                producto.subtotal = (precio * cantidad) - producto.descuento; // Actualizar subtotal
                localStorage.setItem('productos', JSON.stringify(this.productos));
            }
        },
        eliminarProducto(id) {
            this.productos = this.productos.filter(item => item.id !== id);
            localStorage.setItem('productos', JSON.stringify(this.productos));
        },
        setAuth(estado){
            this.logueado = estado;
        },
        logout(){
            this.productos = [];
            this.logueado = false;
            localStorage.setItem('productos', JSON.stringify(this.productos));
        }
    }
});
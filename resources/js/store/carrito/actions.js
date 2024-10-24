export default{
    obtenerProductos() {
        this.productos = JSON.parse(localStorage.getItem('productos')) || [];
    },
    agregarProductos(producto) {
        const descuento = producto.promociones[0].promocion ? producto.promociones[0].promocion.descuento : 0;
        const productoExistente = this.productos.find(item => item.id === producto.id);
        if (productoExistente) {
            productoExistente.cantidad += 1;
            productoExistente.descuento += productoExistente.descuento;
            productoExistente.subtotal = (productoExistente.precio * productoExistente.cantidad) - productoExistente.descuento;
        } else {
            this.productos.push({
                id: producto.id,
                nombre: producto.nombre,
                cantidad: 1,
                precio: producto.lote_productos.precio,
                descuento: descuento,
                subtotal: producto.lote_productos.precio - descuento,
            })
        }

        localStorage.setItem('productos', JSON.stringify(this.productos));
    }
}
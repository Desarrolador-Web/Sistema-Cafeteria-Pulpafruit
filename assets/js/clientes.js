class Clientes {
    // Método para mostrar el modal de "Inicios de Sesión"
    mostrarIniciosSesion() {
        axios.get('http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=iniciosSesion')
            .then((response) => {
                if (response.data.tipo === 'success') {
                    const datos = response.data.data;
                    const contenidoTabla = datos.map(fila => `
                        <tr>
                            <td>${fila.nombre_completo}</td>
                            <td>${fila.nombre_sede}</td>
                            <td>${fila.fecha_apertura}</td>
                            <td>${fila.hora_apertura}</td>
                            <td>${fila.fecha_cierre}</td>
                            <td>${fila.hora_cierre}</td>
                        </tr>
                    `).join('');
                    document.getElementById('tablaIniciosSesionBody').innerHTML = contenidoTabla;
                    new bootstrap.Modal(document.getElementById('modalIniciosSesion')).show();
                } else {
                    alert('Error al cargar los datos: ' + response.data.mensaje);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Hubo un error al conectar con el servidor.');
            });
    }

    // Método para mostrar el modal de "Usuarios Registrados"
    mostrarUsuariosRegistrados() {
        axios.get('http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=usuariosRegistrados')
            .then((response) => {
                if (response.data.tipo === 'success') {
                    const contenidoTabla = response.data.data.map(usuario => `
                        <tr>
                            <td>${usuario.nombre_completo}</td>
                            <td>${usuario.rol}</td>
                        </tr>
                    `).join('');
                    document.getElementById('tablaUsuariosBody').innerHTML = contenidoTabla;
                    new bootstrap.Modal(document.getElementById('modalUsuariosRegistrados')).show();
                } else {
                    alert('Error al cargar los datos: ' + response.data.mensaje);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Hubo un error al conectar con el servidor.');
            });
    }

    // Método para mostrar el modal de "Productos Agotados"
    mostrarProductosAgotados() {
        axios.get('http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=productosAgotados')
            .then((response) => {
                if (response.data.tipo === 'success') {
                    const contenidoTabla = response.data.data.map(producto => `
                        <tr>
                            <td>${producto.producto}</td>
                            <td>${producto.ultima_fecha_compra || 'N/A'}</td>
                            <td>${producto.ultima_fecha_venta || 'N/A'}</td>
                        </tr>
                    `).join('');
                    document.getElementById('tablaProductosAgotadosBody').innerHTML = contenidoTabla;
                    new bootstrap.Modal(document.getElementById('modalProductosAgotados')).show();
                } else {
                    alert('Error al cargar los productos agotados.');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Hubo un error al conectar con el servidor.');
            });
    }

    // Método para mostrar el modal de "Nivelar Inventario"
    mostrarNivelarInventario() {
        axios.get('http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=nivelarInventario')
            .then((response) => {
                if (response.data.tipo === 'success') {
                    const datos = response.data.data;
                    const contenidoTabla = datos.map(producto => `
                        <tr>
                            <td>${producto.codigo_producto}</td>
                            <td>${producto.descripcion}</td>
                            <td contenteditable="false" class="editable">${producto.existencia}</td>
                            <td contenteditable="false" class="editable">${producto.precio_compra}</td>
                            <td contenteditable="false" class="editable">${producto.precio_venta}</td>
                            <td><img src="${producto.imagen || 'default.jpg'}" alt="Imagen del producto" width="50"></td>
                            <td><button class="btn btn-info" onclick="cliente.habilitarEdicion(this)">Editar</button></td>
                        </tr>
                    `).join('');                    
                    
                    document.getElementById('tablaNivelarInventarioBody').innerHTML = contenidoTabla;
                    
                    // Asegurar que el modal se muestra correctamente
                    let modalElement = document.getElementById('modalNivelarInventario');
                    modalElement.removeAttribute('aria-hidden');
                    modalElement.style.display = 'block';
                    let modal = new bootstrap.Modal(modalElement);
                    modal.show();
                } else {
                    alert('Error al cargar los productos.');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Hubo un error al conectar con el servidor.');
            });
        }

    // Método para habilitar edición de celdas en la tabla
    habilitarEdicion(boton) {
        let fila = boton.closest('tr');
        fila.querySelectorAll('.editable').forEach(celda => {
            celda.contentEditable = "true";
            celda.style.backgroundColor = "#ffffcc"; // Cambia el fondo para indicar edición
        });

        boton.textContent = "Guardar";
        boton.classList.remove("btn-info");
        boton.classList.add("btn-success");
        boton.setAttribute("onclick", "cliente.guardarEdicion(this)");
    }

    // Método para guardar los cambios en la tabla
    guardarEdicion(boton) {
        let fila = boton.closest('tr');
        let codigo_producto = fila.cells[0].textContent;
        let existencia = fila.cells[2].textContent;
        let precio_compra = fila.cells[3].textContent;
        let precio_venta = fila.cells[4].textContent;
    
        // Enviar datos al backend con Axios
        let formData = new FormData();
        formData.append("codigo_producto", codigo_producto);
        formData.append("existencia", existencia);
        formData.append("precio_compra", precio_compra);
        formData.append("precio_venta", precio_venta);
    
        axios.post('http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=actualizarProducto', formData)
        .then(response => {
            if (response.data.tipo === 'success') {
                alert("Producto actualizado correctamente.");
                fila.querySelectorAll('.editable').forEach(celda => {
                    celda.contentEditable = "false";
                    celda.style.backgroundColor = ""; // Restaurar el fondo original
                });
    
                boton.textContent = "Editar";
                boton.classList.remove("btn-success");
                boton.classList.add("btn-info");
                boton.setAttribute("onclick", "cliente.habilitarEdicion(this)");
            } else {
                alert("Error al actualizar el producto: " + response.data.mensaje);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error al conectar con el servidor.');
        });
    }
    
}


// Inicialización de cliente y eventos
const cliente = new Clientes();
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('metric-inicios-sesion').addEventListener('click', () => cliente.mostrarIniciosSesion());
    document.getElementById('metric-usuarios-registrados').addEventListener('click', () => cliente.mostrarUsuariosRegistrados());
    document.getElementById('metric-productos-agotados').addEventListener('click', () => cliente.mostrarProductosAgotados());
    document.getElementById('metric-nivelar-inventario').addEventListener('click', () => cliente.mostrarNivelarInventario());
});

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
                            <td><img src="${producto.imagen}" alt="Imagen del producto" width="50"></td>
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

    // Método para habilitar edición de celdas en una sola fila
    habilitarEdicion(boton) {
        // Deshabilitar cualquier otra fila en edición
        document.querySelectorAll('.editable').forEach(celda => {
            celda.contentEditable = "false";
            celda.style.backgroundColor = "";
            celda.removeEventListener('keydown', this.moverConEnter);
        });
        document.querySelectorAll('.guardar-btn').forEach(btn => {
            btn.textContent = "Editar";
            btn.classList.remove("btn-success");
            btn.classList.add("btn-info");
            btn.setAttribute("onclick", "cliente.habilitarEdicion(this)");
        });

        // Habilitar la edición solo en la fila seleccionada
        let fila = boton.closest('tr');
        fila.querySelectorAll('.editable').forEach(celda => {
            celda.contentEditable = "true";
            celda.style.backgroundColor = "#d4edda"; // Verde claro
            celda.addEventListener('keydown', this.moverConEnter);
        });
        boton.textContent = "Guardar";
        boton.classList.remove("btn-info");
        boton.classList.add("btn-success");
        boton.setAttribute("onclick", "cliente.guardarEdicion(this)");
        boton.classList.add("guardar-btn");
    }

    // Método para mover con Enter entre celdas
    moverConEnter(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Evita la nueva línea
            let celdaActual = event.target;
            let siguienteCelda = celdaActual.parentElement.cells[celdaActual.cellIndex + 1];
            if (siguienteCelda && siguienteCelda.classList.contains('editable')) {
                siguienteCelda.focus();
            }
        }
    }

    // Método para guardar los cambios en la tabla con validación de reducción de precio
    guardarEdicion(boton) {
        let fila = boton.closest('tr');
        let codigo_producto = fila.cells[0].textContent.trim();
        let existencia = parseInt(fila.cells[2].textContent.trim(), 10);
        let precio_compra = parseFloat(fila.cells[3].textContent.trim());
        let precio_venta = parseFloat(fila.cells[4].textContent.trim());
    
        let existencia_anterior = parseInt(fila.dataset.existencia, 10);
        let precio_compra_anterior = parseFloat(fila.dataset.precioCompra);
        let precio_venta_anterior = parseFloat(fila.dataset.precioVenta);
    
        // Verificar si hay reducción de valores
        let reduccionExistencia = existencia < existencia_anterior;
        let reduccionPrecioCompra = precio_compra < precio_compra_anterior;
        let reduccionPrecioVenta = precio_venta < precio_venta_anterior;
    
        if (reduccionExistencia || reduccionPrecioCompra || reduccionPrecioVenta) {
            let mensaje = "Está intentando reducir los siguientes valores:\n";
            if (reduccionExistencia) {
                mensaje += `- Existencia: de ${existencia_anterior} a ${existencia}\n`;
            }
            if (reduccionPrecioCompra) {
                mensaje += `- Precio de compra: de ${precio_compra_anterior} a ${precio_compra}\n`;
            }
            if (reduccionPrecioVenta) {
                mensaje += `- Precio de venta: de ${precio_venta_anterior} a ${precio_venta}`;
            }
    
            Swal.fire({
                title: 'Confirmar reducción',
                text: mensaje,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.enviarEdicion(fila, codigo_producto, existencia, precio_compra, precio_venta, boton);
                }
            });
        } else {
            this.enviarEdicion(fila, codigo_producto, existencia, precio_compra, precio_venta, boton);
        }
    }
    
    enviarEdicion(fila, codigo_producto, existencia, precio_compra, precio_venta, boton, confirmacion = false) {
        let formData = new FormData();
        formData.append("codigo_producto", codigo_producto);
        formData.append("existencia", existencia);
        formData.append("precio_compra", precio_compra);
        formData.append("precio_venta", precio_venta);
        
        // Si el usuario ya confirmó la reducción, se envía este parámetro
        if (confirmacion) {
            formData.append("confirmacion", "true");
        }
    
        axios.post('http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=actualizarProducto', formData)
        .then(response => {
            if (response.data.tipo === 'success') {
                Swal.fire('Éxito', 'Producto actualizado correctamente.', 'success');
                this.finalizarEdicion(fila, boton);
            } else if (response.data.tipo === 'alerta' && !confirmacion) {
                // Si el backend detecta reducción y aún no ha sido confirmada, mostrar la alerta
                Swal.fire({
                    title: 'Confirmar reducción',
                    text: response.data.mensaje,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, continuar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si el usuario confirma, volver a llamar a enviarEdicion con confirmación
                        this.enviarEdicion(fila, codigo_producto, existencia, precio_compra, precio_venta, boton, true);
                    }
                });
            } else {
                Swal.fire('Error', response.data.mensaje, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Hubo un error al conectar con el servidor.', 'error');
        });
    }
    
    
    // Función para finalizar la edición y volver al estado inicial de la fila
    finalizarEdicion(fila, boton) {
        fila.querySelectorAll('.editable').forEach(celda => {
            celda.contentEditable = "false";
            celda.style.backgroundColor = "";
            celda.removeEventListener('keydown', this.moverConEnter);
        });
    
        boton.textContent = "Editar";
        boton.classList.remove("btn-success");
        boton.classList.add("btn-info");
        boton.setAttribute("onclick", "cliente.habilitarEdicion(this)");
    } 
    

    // Mostrar el modal con los datos del informe
    mostrarDescargarInforme() {
        axios.get('http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=descargarInforme')
            .then((response) => {
                if (response.data.tipo === 'success') {
                    const contenidoTabla = response.data.data.map(persona => `
                        <tr>
                            <td>${persona.cedula}</td>
                            <td>${persona.nombre}</td>
                            <td>${persona.deuda}</td>
                        </tr>
                    `).join('');
                    document.getElementById('tablaDescargarInforme').innerHTML = contenidoTabla;
                    new bootstrap.Modal(document.getElementById('modalDescargarInforme')).show();
                } else {
                    alert('Error al cargar los datos: ' + response.data.mensaje);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Hubo un error al conectar con el servidor.');
            });
    }

    // Función para filtrar la tabla en tiempo real
    filtrarTabla() {
        let input = document.getElementById("buscadorInforme").value.toLowerCase();
        let filas = document.querySelectorAll("#tablaDescargarInforme tr");

        filas.forEach(fila => {
            let cedula = fila.cells[0].textContent.toLowerCase();
            let nombre = fila.cells[1].textContent.toLowerCase();
            fila.style.display = (cedula.includes(input) || nombre.includes(input)) ? "" : "none";
        });
    }

    // Descargar PDF
    descargarPDF() {
        window.location.href = 'http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=descargarPDF';
    }

    // Descargar Excel
    descargarExcel() {
        window.location.href = 'http://localhost/Sistema-Cafeteria-Pulpafruit/controllers/clientesController.php?option=descargarExcel';
    }

}



// Inicialización de cliente y eventos
const cliente = new Clientes();
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('metric-inicios-sesion').addEventListener('click', () => cliente.mostrarIniciosSesion());
    document.getElementById('metric-usuarios-registrados').addEventListener('click', () => cliente.mostrarUsuariosRegistrados());
    document.getElementById('metric-productos-agotados').addEventListener('click', () => cliente.mostrarProductosAgotados());
    document.getElementById('metric-nivelar-inventario').addEventListener('click', () => cliente.mostrarNivelarInventario());
    document.getElementById('metric-descargar-informe').addEventListener('click', () => cliente.mostrarDescargarInforme());
});
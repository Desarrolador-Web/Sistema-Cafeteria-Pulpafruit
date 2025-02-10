$(document).ready(function() {
    $('#table_productos').DataTable({
        "ajax": {
            "url": "controllers/productosController.php?option=listar",
            "dataSrc": "",
            "error": function(xhr, error, code){
                console.log(xhr.responseText);
            }
        },
        "columns": [
            {"data": "id_producto"},
            {"data": "codigo_producto"},
            {"data": "descripcion"},
            {"data": "precio_compra"},
            {"data": "precio_venta"},
            {
              "data": "imagen",
              "render": function(data, type, row) {
                  if (data) {
                      // imagen para que sea relativa a la raíz del servidor web
                      var relativePath = data.replace("C:\\xampp\\htdocs\\venta\\", "").replace(/\\/g, "/");
                      return '<img src="' + relativePath + '" width="50" height="50"/>';
                  } else {
                      return '<img src="uploads/default.png" width="50" height="50"/>'; // Ruta a una imagen por defecto
                  }
              }
          },
          
          
            {"data": "existencia"},
        ]
    });
  
    
  });
  
  
  function deleteProducto(id) {
    $.ajax({
        url: 'controllers/productosController.php?option=delete&id=' + id,
        type: 'GET',
        success: function(response) {
            var res = JSON.parse(response);
            if (res.tipo === 'success') {
                $('#table_productos').DataTable().ajax.reload();
                alert(res.mensaje);
            } else {
                alert(res.mensaje);
            }
        }
    });
  }
  
  function editProducto(id) {
    $.ajax({
        url: 'controllers/productosController.php?option=edit&id=' + id,
        type: 'GET',
        success: function(response) {
            var data = JSON.parse(response);
            $('#id_product').val(data.codproducto);
            $('#barcode').val(data.codigo);
            $('#nombre').val(data.descripcion);
            $('#precio').val(data.precio_compra); 
            $('#stock').val(data.existencia);
            $('#modalProducto').modal('show');
        }
    });
  }
  
  $('#formProducto').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: 'controllers/productosController.php?option=save',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            var res = JSON.parse(response);
            if (res.tipo === 'success') {
                $('#table_productos').DataTable().ajax.reload();
                $('#modalProducto').modal('hide');
                alert(res.mensaje);
            } else {
                alert(res.mensaje);
            }
        }
    });
  });
  
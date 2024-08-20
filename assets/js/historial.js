let minDate, maxDate, table;

document.addEventListener('DOMContentLoaded', function () {
  
  // Inicializar DateTime Pickers
  minDate = new DateTime($('#desde'), {
    format: 'YYYY-MM-DD'
  });
  maxDate = new DateTime($('#hasta'), {
    format: 'YYYY-MM-DD'
  });

  // Inicializar DataTable
  table = $('#table_ventas').DataTable({
    ajax: {
      url: ruta + 'controllers/historialVentasController.php?option=historial',
      dataSrc: ''
    },
    columns: [
      { data: 'id_ventas' },
      { data: 'nombres' },
      { data: 'producto' },
      { data: 'cantidad' },  
      { data: 'precio_venta' },  
      { data: 'subtotal' },  
      { data: 'fecha' },
      { data: 'metodo_pago' }  // Asegúrate de que esta columna está presente
    ],
    language: {
      url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
    }
  });

  // Filtrar la tabla al cambiar las fechas
  $('#desde, #hasta').on('change', function () {
    table.draw();
  });

  // Función de filtrado por fechas
  $.fn.dataTable.ext.search.push(
    function (settings, data, dataIndex) {
      var min = minDate.val();
      var max = maxDate.val();
      var date = new Date(data[6]); 

      if (
        (!min && !max) ||
        (!min && date <= new Date(max)) ||
        (new Date(min) <= date && !max) ||
        (new Date(min) <= date && date <= new Date(max))
      ) {
        return true;
      }
      return false;
    }
  );
});

document.getElementById('export_pdf').addEventListener('click', function () {
  exportTableToPDF();
});

function exportTableToPDF() {
  // Crear una ventana emergente
  var pdfWindow = window.open('', '_blank');

  // Crear el contenido HTML del documento de la tabla
  var style = `
      <style>
          table {width: 100%; border-collapse: collapse;}
          table, th, td {border: 1px solid black; padding: 8px; text-align: left;}
          th {background-color: #f2f2f2;}
      </style>
  `;

  var tableHTML = document.getElementById('table_ventas').outerHTML;

  var content = `
      <html>
      <head>
          <title>Historial de Ventas</title>
          ${style}
      </head>
      <body>
          <h1>Historial de Ventas</h1>
          ${tableHTML}
      </body>
      </html>
  `;

  // Escribir el contenido en la nueva ventana
  pdfWindow.document.write(content);

  // Esperar a que el documento se cargue, luego llamar a print
  pdfWindow.document.close();
  pdfWindow.focus();
  pdfWindow.print();
  pdfWindow.close();
}

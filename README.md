### Descripción de Carpetas y Archivos
- *assets/*: Contiene archivos estáticos como CSS, JavaScript e imágenes.
- *controllers/*: Contiene los controladores que manejan la lógica de negocio para diferentes módulos.
- *models/*: Contiene los modelos que representan las entidades del sistema y manejan la lógica de la base de datos.
- *views/*: Contiene las vistas que renderizan la interfaz de usuario.
- *uploads/*: Carpeta destinada a almacenar archivos subidos por los usuarios.
- *config.php*: Archivo de configuración del sistema.
- *index.php*: Punto de entrada principal de la aplicación.
- *plantilla.php*: Archivo de plantilla principal que define la estructura básica de las páginas.
- *README.md*: Documento actual con información sobre el funcionamiento del proyecto.

## Uso de .htaccess
En este proyecto no se utiliza un archivo .htaccess. En su lugar, la configuración de redireccionamientos y manejo de URL se realiza directamente en los archivos PHP de controladores y en la configuración del servidor web.

## Uso de Axios
Axios es una biblioteca de JavaScript utilizada para hacer solicitudes HTTP desde el navegador. Dentro de este proyecto, Axios se utiliza principalmente para realizar solicitudes asíncronas al servidor, lo que permite una mejor experiencia de usuario sin necesidad de recargar la página completa.

### Cómo Funciona Axios en el Proyecto

1. *Instalación*: Axios está incluido en el proyecto y se puede encontrar en los archivos JavaScript dentro de la carpeta assets/js/.
2. *Configuración*: En los archivos JavaScript, se configura Axios para realizar solicitudes HTTP a los controladores PHP.
3. *Uso*: Se utiliza para enviar datos a los controladores y recibir respuestas sin necesidad de recargar la página. Esto es útil para operaciones como agregar, editar y eliminar registros.

#### Ejemplo de Uso de Axios
```javascript
// Ejemplo de cómo hacer una solicitud GET con Axios
axios.get('/controllers/ventasController.php?action=list')
  .then(response => {
    console.log(response.data);
  })
  .catch(error => {
    console.error('Error fetching sales:', error);
  });

// Ejemplo de cómo hacer una solicitud POST con Axios
axios.post('/controllers/comprasController.php?action=add', {
    product_id: 1,
    quantity: 10
  })
  .then(response => {
    console.log('Compra añadida:', response.data);
  })
  .catch(error => {
    console.error('Error añadiendo el elemento', error);
  });

## Módulos
### Módulo de Compras
- *Función:* Gestiona el proceso de registro y seguimiento de compras.
- *Componentes:* controllers/comprasController.php, models/compras.php, views/compras/.
- *Cómo Funciona:* 
  - *Controlador:* comprasController.php maneja las solicitudes relacionadas con las compras, incluyendo la creación, edición y visualización de registros de compras.
  - *Modelo:* compras.php interactúa con la base de datos para realizar operaciones CRUD sobre la tabla de compras.
  - *Vista:* Las vistas en views/compras/ renderizan las páginas de la interfaz de usuario relacionadas con las compras.
- *Modificar:* Para añadir funcionalidades, se debe actualizar el controlador para manejar nuevas acciones, el modelo para incluir nuevas interacciones con la base de datos y las vistas para reflejar los cambios en la UI.

### Módulo de Ventas
- *Función:* Gestiona el proceso de ventas, incluyendo el registro y seguimiento de ventas realizadas.
- *Componentes:* controllers/ventasController.php, models/ventas.php, views/ventas/.
- *Cómo Funciona:*
  - *Controlador:* ventasController.php maneja las solicitudes relacionadas con las ventas.
  - *Modelo:* ventas.php se encarga de las operaciones en la base de datos para la tabla de ventas.
  - *Vista:* Las vistas en views/ventas/ se encargan de mostrar la información relacionada con las ventas.
- *Modificar:* Se siguen pasos similares a los del módulo de compras.

### Módulo de Clientes
- *Función:* Administra la información de los clientes.
- *Componentes:* controllers/clientesController.php, models/cliente.php, views/clientes/.
- *Cómo Funciona:*
  - *Controlador:* clientesController.php maneja todas las operaciones relacionadas con los clientes.
  - *Modelo:* cliente.php realiza operaciones sobre la base de datos de clientes.
  - *Vista:* Las vistas en views/clientes/ se utilizan para mostrar y gestionar la información de los clientes.
- *Modificar:* Se deben actualizar el controlador, el modelo y las vistas para reflejar cualquier cambio.

### Módulo de Proveedores
- *Función:* Administra la información de los proveedores.
- *Componentes:* controllers/proveedoresController.php, models/proveedor.php, views/proveedores/.
- *Cómo Funciona:*
  - *Controlador:* proveedoresController.php maneja las solicitudes relacionadas con los proveedores.
  - *Modelo:* proveedor.php se encarga de las interacciones con la base de datos de proveedores.
  - *Vista:* Las vistas en views/proveedores/ se utilizan para la gestión visual de proveedores.
- *Modificar:* Se deben actualizar el controlador, el modelo y las vistas para reflejar cualquier cambio.

## Creación de Nuevos Módulos
Para crear un nuevo módulo:

1. Crear el archivo del controlador en la carpeta controllers.
2. Crear el archivo del modelo en la carpeta models.
3. Crear las vistas necesarias en la carpeta correspondiente dentro de views.
4. Actualizar el archivo index.php o el enrutador correspondiente para incluir las nuevas rutas.

## Cambios de Estilo
Para modificar los estilos:

1. Editar los archivos CSS en la carpeta assets/css.
2. Para cambios en el JavaScript, editar los archivos en assets/js (Cada módulo tiene su respectivo js).
3. Asegurarse de que las vistas están utilizando las nuevas clases o estilos definidos.

## Generalidades del sistema
### Archivo plantilla.php y su uso:

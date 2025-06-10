<?php
include_once("views/View.php");
include_once("models/listadeseos.php");
include_once("models/productos.php");

class ListaDeseosController {
    private $listaDeseosDAO;
    private $productoDAO;
    
    public function __construct() {
        $this->listaDeseosDAO = new ListaDeseosDAO();
        $this->productoDAO = new ProductoDAO();
    }
    
    // Mostrar lista de deseos del usuario
    public function mostrarListaDeseos() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        // Los admins no usan lista de deseos
        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
            $_SESSION['mensaje'] = "Los administradores no tienen acceso a la lista de deseos";
            header("Location: index.php");
            exit();
        }
        
        // Usar usuario fijo para simplicidad
        $productos = $this->listaDeseosDAO->getProductosListaDeseos(2);
        View::show("lista_deseos", ['listaDeseos' => $productos]);
    }
    
    // Añadir producto a favoritos
    public function agregarProducto() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if (!isset($_REQUEST['producto_id'])) {
            $_SESSION['mensaje'] = "Producto no encontrado";
            header("Location: index.php");
            exit();
        }
        
        // Verificar que no sea admin
        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
            $_SESSION['mensaje'] = "Los administradores no pueden usar la lista de deseos";
            header("Location: index.php");
            exit();
        }
        
        $producto_id = $_REQUEST['producto_id'];
        $redirect = $_REQUEST['redirect'] ?? 'default';
        
        // Agregar a favoritos
        if ($this->listaDeseosDAO->agregarProducto(2, $producto_id)) {
            $_SESSION['mensaje'] = "Producto añadido a la lista de deseos";
        } else {
            $_SESSION['mensaje'] = "Error al añadir el producto o ya está en la lista";
        }
        
        // Redirigir según corresponda
        $this->redirigirSegunParametro($redirect);
    }
    
    // Quitar producto de favoritos
    public function eliminarProducto() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if (!isset($_REQUEST['producto_id'])) {
            header("Location: index.php");
            exit();
        }
        
        // Verificar que no sea admin
        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
            $_SESSION['mensaje'] = "Los administradores no pueden usar la lista de deseos";
            header("Location: index.php");
            exit();
        }
        
        $producto_id = $_REQUEST['producto_id'];
        $redirect = $_REQUEST['redirect'] ?? 'default';
        
        // Eliminar de favoritos
        $this->listaDeseosDAO->eliminarProducto(2, $producto_id);
        $_SESSION['mensaje'] = "Producto eliminado de la lista de deseos";
        
        // Redirigir según corresponda
        $this->redirigirSegunParametro($redirect);
    }
    
    // Verificar si producto está en lista (para AJAX)
    public function isInListaDeseos() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        $response = ['inWishlist' => false];
        
        if (isset($_REQUEST['producto_id'])) {
            if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
                $response['inWishlist'] = false;
            } else {
                $response['inWishlist'] = $this->listaDeseosDAO->isProductoEnListaDeseos(2, $_REQUEST['producto_id']);
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Función auxiliar para redirecciones
    private function redirigirSegunParametro($redirect) {
        switch ($redirect) {
            case 'home':
                header("Location: index.php");
                break;
            case 'lista':
                header("Location: index.php?controller=ListaDeseosController&action=mostrarListaDeseos");
                break;
            default:
                header("Location: index.php");
                break;
        }
        exit();
    }
}
?>
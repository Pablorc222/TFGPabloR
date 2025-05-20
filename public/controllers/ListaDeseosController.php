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
    
    // Mostrar lista de deseos
    public function mostrarListaDeseos() {
        // Iniciar sesión solo si no está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['mensaje'] = "Necesitas iniciar sesión";
            header("Location: index.php?controller=UsuController&action=showiniciosesion");
            exit();
        }
        
        $productos = $this->listaDeseosDAO->getProductosListaDeseos($_SESSION['usuario_id']);
        View::show("lista_deseos", ['listaDeseos' => $productos]);
    }
    
    // Agregar producto a lista de deseos
    public function agregarProducto() {
        // Iniciar sesión solo si no está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario_id']) || !isset($_REQUEST['producto_id'])) {
            $_SESSION['mensaje'] = isset($_SESSION['usuario_id']) ? "Producto no encontrado" : "Necesitas iniciar sesión";
            header("Location: index.php");
            exit();
        }
        
        $producto_id = $_REQUEST['producto_id'];
        
        if ($this->listaDeseosDAO->agregarProducto($_SESSION['usuario_id'], $producto_id)) {
            $_SESSION['mensaje'] = "Producto añadido a tu lista de deseos";
        } else {
            $_SESSION['mensaje'] = "Error al añadir el producto";
        }
        
        header("Location: index.php?controller=ProductController&action=verDetallesProducto&idProducto=" . $producto_id);
        exit();
    }
    
    // Eliminar producto de lista de deseos
    public function eliminarProducto() {
        // Iniciar sesión solo si no está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario_id']) || !isset($_REQUEST['producto_id'])) {
            header("Location: index.php");
            exit();
        }
        
        $producto_id = $_REQUEST['producto_id'];
        $this->listaDeseosDAO->eliminarProducto($_SESSION['usuario_id'], $producto_id);
        
        // Redirigir según desde donde se llamó
        $redirect = isset($_REQUEST['redirect']) && $_REQUEST['redirect'] == 'lista' 
            ? "index.php?controller=ListaDeseosController&action=mostrarListaDeseos"
            : "index.php?controller=ProductController&action=verDetallesProducto&idProducto=" . $producto_id;
        
        $_SESSION['mensaje'] = "Producto eliminado de tu lista de deseos";
        header("Location: " . $redirect);
        exit();
    }
    
    // Verificar si un producto está en la lista 
    public function isInListaDeseos() {
        // Iniciar sesión solo si no está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $response = ['inWishlist' => false];
        
        if (isset($_SESSION['usuario_id']) && isset($_REQUEST['producto_id'])) {
            $response['inWishlist'] = $this->listaDeseosDAO->isProductoEnListaDeseos(
                $_SESSION['usuario_id'], 
                $_REQUEST['producto_id']
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}
?>
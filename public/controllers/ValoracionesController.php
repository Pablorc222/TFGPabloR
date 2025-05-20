<?php
// ValoracionesController.php - Versión ultra-simplificada
include_once("views/View.php");
include_once("models/valoraciones.php");
include_once("models/productos.php");

class ValoracionesController {
    private $valoracionesDAO;
    private $productoDAO;
    
    public function __construct() {
        $this->valoracionesDAO = new ValoracionesDAO();
        $this->productoDAO = new ProductoDAO();
    }
    
    // Mostrar formulario para valorar un producto
    public function mostrarFormValoracion() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=UsuController&action=showiniciosesion");
            exit();
        }
        $producto = $this->productoDAO->getProductById($_REQUEST['producto_id']);
        View::show("form_valoracion", ['producto' => $producto]);
    }
    
    // Procesar valoración
    public function procesarValoracion() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=UsuController&action=showiniciosesion");
            exit();
        }
        $producto_id = $_POST['producto_id'];
        $puntuacion = $_POST['puntuacion'];
        $comentario = $_POST['comentario'];
        
        if ($puntuacion >= 1 && $puntuacion <= 5) {
            $this->valoracionesDAO->guardarValoracion($_SESSION['usuario_id'], $producto_id, $puntuacion, $comentario);
            $_SESSION['mensaje'] = "Valoración guardada correctamente";
        }
        header("Location: index.php?controller=ProductController&action=verDetallesProducto&idProducto=$producto_id");
        exit();
    }
    
    // Ver valoraciones
    public function verValoracionesProducto() {
        $producto_id = $_REQUEST['producto_id'];
        $producto = $this->productoDAO->getProductById($producto_id);
        $valoraciones = $this->valoracionesDAO->getValoracionesProducto($producto_id);
        
        // Calcular promedio
        $promedio = 0;
        if (!empty($valoraciones)) {
            $suma = array_sum(array_column($valoraciones, 'puntuacion'));
            $promedio = $suma / count($valoraciones);
        }
        View::show("valoraciones_producto", [
            'producto' => $producto, 
            'valoraciones' => $valoraciones,
            'promedio_valoraciones' => $promedio
        ]);
    }
    
    // Valorar después de compra
    public function mostrarValoracionPostCompra() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
            header("Location: index.php"); exit();
        }
        
        // Obtener productos y mostrar formulario
        $productos = [];
        foreach ($_SESSION['carrito'] as $id => $cantidad) {
            if (is_numeric($id)) {
                $producto = $this->productoDAO->getProductById($id);
                if ($producto) $productos[] = $producto;
            }
        }
        $_SESSION['carrito'] = []; // Limpiar carrito
        View::show("valorar_productos_comprados", ['productos' => $productos]);
    }
    
    // Procesar valoraciones post-compra
    public function procesarValoracionesCompra() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id']) || !isset($_POST['valoraciones'])) {
            header("Location: index.php"); exit();
        }
        
        foreach ($_POST['valoraciones'] as $id => $val) {
            if (isset($val['puntuacion']) && $val['puntuacion'] >= 1 && $val['puntuacion'] <= 5) {
                $this->valoracionesDAO->guardarValoracion(
                    $_SESSION['usuario_id'], 
                    $id, 
                    $val['puntuacion'], 
                    isset($val['comentario']) ? $val['comentario'] : ''
                );
            }
        }
        $_SESSION['mensaje'] = "¡Gracias por tus valoraciones!";
        header("Location: index.php"); exit();
    }
}
?>
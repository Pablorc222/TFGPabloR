<?php
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
    
    // Mostrar formulario para valorar producto
    public function mostrarFormValoracion() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=UsuController&action=showiniciosesion");
            exit();
        }
        
        $producto = $this->productoDAO->getProductById($_REQUEST['producto_id']);
        View::show("form_valoracion", ['producto' => $producto]);
    }
    
    // Guardar valoración individual
    public function procesarValoracion() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=UsuController&action=showiniciosesion");
            exit();
        }
        
        $producto_id = $_POST['producto_id'];
        $puntuacion = $_POST['puntuacion'];
        $comentario = $_POST['comentario'];
        
        // Validar puntuación
        if ($puntuacion >= 1 && $puntuacion <= 5) {
            $this->valoracionesDAO->guardarValoracion($_SESSION['usuario_id'], $producto_id, $puntuacion, $comentario);
            $_SESSION['mensaje'] = "Valoración guardada correctamente";
        }
        
        header("Location: index.php?controller=ProductController&action=verDetallesProducto&idProducto=$producto_id");
        exit();
    }
    
    // Ver todas las valoraciones de un producto
    public function verValoracionesProducto() {
        $producto_id = $_REQUEST['producto_id'];
        $producto = $this->productoDAO->getProductById($producto_id);
        $valoraciones = $this->valoracionesDAO->getValoracionesProducto($producto_id);
        
        // Calcular promedio de valoraciones
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
    
    // Mostrar formulario de valoraciones después de compra
    public function mostrarValoracionPostCompra() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=UsuController&action=showiniciosesion");
            exit();
        }
        
        // Obtener productos comprados para valorar
        $productos = [];
        if (isset($_SESSION['productos_para_valorar'])) {
            $productos = $_SESSION['productos_para_valorar'];
            unset($_SESSION['productos_para_valorar']); // Limpiar después de obtener
        }
        
        // Si no hay productos, redirigir
        if (empty($productos)) {
            $_SESSION['mensaje'] = "No hay productos para valorar.";
            header("Location: index.php");
            exit();
        }
        
        View::show("valorar_productos_comprados", ['productos' => $productos]);
    }
    
    // Procesar múltiples valoraciones post-compra
    public function procesarValoracionesCompra() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['usuario_id']) || !isset($_POST['valoraciones'])) {
            header("Location: index.php"); 
            exit();
        }
        
        // Guardar cada valoración
        foreach ($_POST['valoraciones'] as $id => $val) {
            if (isset($val['puntuacion']) && $val['puntuacion'] >= 1 && $val['puntuacion'] <= 5) {
                $comentario = isset($val['comentario']) ? $val['comentario'] : '';
                $this->valoracionesDAO->guardarValoracion($_SESSION['usuario_id'], $id, $val['puntuacion'], $comentario);
            }
        }
        
        $_SESSION['mensaje'] = "¡Gracias por tus valoraciones!";
        header("Location: index.php"); 
        exit();
    }
}
?>
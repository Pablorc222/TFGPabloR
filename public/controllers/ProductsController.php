<?php
include_once("views/View.php");
include_once("models/productos.php");

class ProductController {
    
    // Mostrar página principal con productos
    public function getAllProducts() {
        if (session_status() == PHP_SESSION_NONE) session_start();

        $pDAO = new ProductoDAO();
        $products = $pDAO->getAllProducts();
        View::show("showProducts", $products);
    }

    // Ver detalles de producto específico
    public function verDetallesProducto() {
        if (session_status() == PHP_SESSION_NONE) session_start();

        if (isset($_REQUEST['idProducto'])) {
            $idProducto = $_REQUEST['idProducto'];
            $pDAO = new ProductoDAO();
            $producto = $pDAO->getProductById($idProducto);
            
            if ($producto) {
                View::show("productDetails", ['producto' => $producto]);
            } else {
                header("Location: index.php");
            }
        } else {
            $this->getAllProducts();
        }
    }

    // Ver contenido del carrito
    public function verCarrito() {
        if (session_status() == PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = array();
        }

        $ProductoDAO = new ProductoDAO();
        $productosCarrito = array();
        $total = 0;

        // Procesar productos del carrito
        foreach ($_SESSION['carrito'] as $id => $cantidad) {
            $producto = $ProductoDAO->getProductById($id);
            if ($producto) {
                $producto['cantidad'] = $cantidad;
                $producto['subtotal'] = $producto['Precio'] * $cantidad;
                $total += $producto['subtotal'];
                $productosCarrito[] = $producto;
            }
        }

        View::show("cart", $productosCarrito);
    }

    // Añadir producto al carrito
    public function addCarrito() {
        if (session_status() == PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = array();
        }

        $idproducto = $_REQUEST['id'];
        $cantidad = isset($_REQUEST['cantidad']) ? intval($_REQUEST['cantidad']) : 1;

        // Agregar o incrementar cantidad
        if (!isset($_SESSION['carrito'][$idproducto])) {
            $_SESSION['carrito'][$idproducto] = $cantidad;
        } else {
            $_SESSION['carrito'][$idproducto] += $cantidad;
        }

        $_SESSION['mensaje'] = "Producto añadido al carrito.";
        header("Location: index.php");
        exit();
    }

    // Quitar producto del carrito
    public function eliminarDelCarrito() {
        if (session_status() == PHP_SESSION_NONE) session_start();

        if (isset($_REQUEST['id'])) {
            $id = $_REQUEST['id'];
            unset($_SESSION['carrito'][$id]);
            $_SESSION['mensaje'] = "Producto eliminado del carrito.";
        }

        header("Location: index.php?controller=ProductController&action=verCarrito");
        exit();
    }

    // Procesar compra y crear pedido
    public function confirmarPedido() {
        if (session_status() == PHP_SESSION_NONE) session_start();

        // Validar usuario logueado
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensaje'] = "Debes iniciar sesión para confirmar el pedido.";
            header("Location: index.php?controller=UsuController&action=showiniciosesion");
            exit();
        }

        // Validar carrito no vacío
        if (empty($_SESSION['carrito'])) {
            $_SESSION['mensaje'] = "No hay productos en tu carrito.";
            header("Location: index.php");
            exit();
        }

        include_once 'models/pedidos.php';
        $pedidoDAO = new PedidoDAO();
        $pDAO = new ProductoDAO();

        // Calcular total y preparar datos
        $total = 0;
        $productosCarrito = array();
        $productosParaValorar = array();

        foreach ($_SESSION['carrito'] as $id => $cantidad) {
            $producto = $pDAO->getProductById($id);
            if ($producto) {
                $subtotal = $producto['Precio'] * $cantidad;
                $total += $subtotal;
                
                // Para el pedido
                $productosCarrito[] = array(
                    'id' => $id,
                    'cantidad' => $cantidad,
                    'precio' => $producto['Precio']
                );
                
                // Para valoraciones
                $productosParaValorar[] = $producto;
            }
        }

        // Crear pedido en la base de datos
        $pedido_id = $pedidoDAO->createPedido($_SESSION['usuario_id'], $total);

        if ($pedido_id) {
            // Agregar productos al pedido
            foreach ($productosCarrito as $producto) {
                $pedidoDAO->addProductToPedido(
                    $pedido_id, 
                    $producto['id'], 
                    $producto['cantidad'], 
                    $producto['precio']
                );
            }

            // Preparar valoraciones y limpiar carrito
            $_SESSION['productos_para_valorar'] = $productosParaValorar;
            $_SESSION['carrito'] = array();
            $_SESSION['mensaje'] = "¡Pedido realizado con éxito!";
            
            header("Location: index.php?controller=ValoracionesController&action=mostrarValoracionPostCompra");
            exit();
        } else {
            $_SESSION['mensaje'] = "Error al procesar el pedido.";
            header("Location: index.php?controller=ProductController&action=verCarrito");
            exit();
        }
    }

    // Crear nuevo producto (solo admin)
    public function insertProduct() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener datos del formulario
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $imagen_frontal_url = $_POST['imagen_frontal_url'] ?? '';
            $imagen_trasera_url = $_POST['imagen_trasera_url'] ?? '';
            
            // Validar campos obligatorios
            if (empty($nombre) || empty($descripcion) || $precio <= 0) {
                $_SESSION['mensaje'] = "Todos los campos son obligatorios y el precio debe ser mayor que 0";
                View::show("addProduct", ['general' => 'Todos los campos son obligatorios y el precio debe ser mayor que 0']);
                return;
            }
            
            // Generar URL trasera automáticamente si no se proporciona
            if (empty($imagen_trasera_url) && !empty($imagen_frontal_url)) {
                $imagen_trasera_url = $this->generarUrlTrasera($imagen_frontal_url);
            }
            
            // Combinar URLs para guardar en BD
            $imagenes_combinadas = $imagen_frontal_url . '|' . $imagen_trasera_url;
            
            $producto = array(
                'Nombre' => $nombre,
                'Descripcion' => $descripcion,
                'Precio' => $precio,
                'Imagen' => $imagenes_combinadas
            );
            
            // Guardar producto
            $pDAO = new ProductoDAO();
            $result = $pDAO->insertProduct($producto);
            
            if ($result) {
                $_SESSION['mensaje'] = "Producto añadido correctamente";
                header("Location: index.php");
            } else {
                $_SESSION['mensaje'] = "Error al añadir el producto";
                View::show("addProduct", ['general' => 'Error al añadir el producto']);
            }
            exit();
        } else {
            // Mostrar formulario (verificar permisos)
            if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
                $_SESSION['mensaje'] = "No tienes permisos para añadir productos";
                header("Location: index.php");
                exit();
            }
            
            View::show("addProduct", null);
        }
    }
    
    // Eliminar producto (solo admin)
    public function eliminarProducto() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        // Verificar permisos de admin
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            header("Location: index.php");
            exit();
        }
        
        if (isset($_REQUEST['id'])) {
            $pDAO = new ProductoDAO();
            $pDAO->borrarprod($_REQUEST['id']);
            $_SESSION['mensaje'] = "Producto eliminado correctamente";
        }
        
        header("Location: index.php");
        exit();
    }
    
    // Función auxiliar para generar URL trasera
    private function generarUrlTrasera($urlFrontal) {
        if (strpos($urlFrontal, '-frontal') !== false) {
            return str_replace('-frontal', '-trasera', $urlFrontal);
        } elseif (strpos($urlFrontal, '_frontal') !== false) {
            return str_replace('_frontal', '_trasera', $urlFrontal);
        } else {
            $info = pathinfo($urlFrontal);
            if (isset($info['extension'])) {
                return $info['dirname'] . '/' . $info['filename'] . '-trasera.' . $info['extension'];
            }
        }
        return $urlFrontal;
    }
}
?>
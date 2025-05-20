<?php
include_once("views/View.php");
include_once("models/productos.php");

class ProductsController {
    // Mostrar todos los productos
    public function getAllProducts() {
        // Iniciar sesión solo si no está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $pDAO = new ProductoDAO();
        $products = $pDAO->getAllProducts();
        
        // Mostrar vista con productos
        View::show("showProducts", $products);
    }

    // Ver detalles de un producto específico
    public function verDetallesProducto() {
        // Iniciar sesión solo si no está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

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

    // Ver carrito de compra
    public function verCarrito() {
        // Iniciar sesión solo si no está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = array();
        }

        $ProductoDAO = new ProductoDAO();
        $productosCarrito = array();
        $total = 0;

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

    // Agregar producto al carrito
    public function addCarrito() {
        // Iniciar sesión solo si no está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = array();
        }

        $idproducto = $_REQUEST['id'];
        $cantidad = isset($_REQUEST['cantidad']) ? intval($_REQUEST['cantidad']) : 1;

        if (!isset($_SESSION['carrito'][$idproducto])) {
            $_SESSION['carrito'][$idproducto] = $cantidad;
        } else {
            $_SESSION['carrito'][$idproducto] += $cantidad;
        }

        $_SESSION['mensaje'] = "Producto añadido al carrito.";
        header("Location: index.php");
        exit();
    }

    // Confirmar pedido
    public function confirmarPedido() {
        // Iniciar sesión solo si no está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'])) {
            $_SESSION['mensaje'] = "Debes iniciar sesión para confirmar el pedido.";
            header("Location: index.php?controller=UsuController&action=showiniciosesion");
            exit();
        }

        if (empty($_SESSION['carrito'])) {
            $_SESSION['mensaje'] = "No hay productos en tu carrito.";
            header("Location: index.php");
            exit();
        }

        include_once 'models/pedidos.php';
        $pedidoDAO = new PedidoDAO();

        // Calcular total
        $total = 0;
        $productosCarrito = array();
        $pDAO = new ProductoDAO();

        foreach ($_SESSION['carrito'] as $id => $cantidad) {
            $producto = $pDAO->getProductById($id);
            if ($producto) {
                $subtotal = $producto['Precio'] * $cantidad;
                $total += $subtotal;
                $productosCarrito[] = array(
                    'id' => $id,
                    'cantidad' => $cantidad,
                    'precio' => $producto['Precio']
                );
            }
        }

        // Crear pedido
        $pedido_id = $pedidoDAO->createPedido($_SESSION['usuario_id'], $total);

        if ($pedido_id) {
            foreach ($productosCarrito as $producto) {
                $pedidoDAO->addProductToPedido(
                    $pedido_id, 
                    $producto['id'], 
                    $producto['cantidad'], 
                    $producto['precio']
                );
            }

            $_SESSION['carrito'] = array();
            $_SESSION['mensaje'] = "¡Pedido realizado con éxito!";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['mensaje'] = "Error al procesar el pedido.";
            header("Location: index.php?controller=ProductsController&action=verCarrito");
            exit();
        }
    }

    // Método para insertar un nuevo producto
    public function insertProduct() {
        // Iniciar sesión solo si no está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si se enviaron datos del formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener datos del formulario
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $stock = $_POST['stock'] ?? 0;
            $categoria = $_POST['categoria'] ?? '';
            $imagen = $_POST['imagen'] ?? '';
            
            // Validar datos
            if (empty($nombre) || empty($descripcion) || $precio <= 0) {
                $_SESSION['mensaje'] = "Todos los campos son obligatorios y el precio debe ser mayor que 0";
                header("Location: index.php?controller=ProductsController&action=showAddProductForm");
                exit();
            }
            
            // Crear objeto producto
            $producto = array(
                'Nombre' => $nombre,
                'Descripcion' => $descripcion,
                'Precio' => $precio,
                'Stock' => $stock,
                'Categoria' => $categoria,
                'Imagen' => $imagen
            );
            
            // Guardar en la base de datos
            $pDAO = new ProductoDAO();
            $result = $pDAO->insertProduct($producto);
            
            if ($result) {
                $_SESSION['mensaje'] = "Producto añadido correctamente";
                header("Location: index.php");
            } else {
                $_SESSION['mensaje'] = "Error al añadir el producto";
                header("Location: index.php?controller=ProductsController&action=showAddProductForm");
            }
            exit();
        } else {
            // Si no es una petición POST, mostrar formulario
            $this->showAddProductForm();
        }
    }

    // Método para mostrar el formulario de añadir producto
    public function showAddProductForm() {
        // Iniciar sesión solo si no está activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si el usuario tiene permisos (opcional)
        if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
            $_SESSION['mensaje'] = "No tienes permisos para añadir productos";
            header("Location: index.php");
            exit();
        }
        
        // Mostrar formulario
        View::show("addProduct", null);
    }
    
    // Método para eliminar un producto
// Método para eliminar un producto
public function eliminarProducto() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Verificar permisos
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
        header("Location: index.php");
        exit();
    }
    
    if (isset($_REQUEST['id'])) {
        $pDAO = new ProductoDAO();
        $pDAO->borrarprod($_REQUEST['id']);
    }
    
    header("Location: index.php");
    exit();
}
}
?>
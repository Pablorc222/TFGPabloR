<?php
class UsuController {
    
    // Mostrar formulario de login
    public function showiniciosesion() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        View::show("login");
    }
    
    // Validar inicio de sesión
    public function validacioniniciosesion() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $errores = array();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
            $contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';
            
            // Validaciones básicas
            if (empty($usuario)) {
                $errores['usuario'] = "El usuario no puede estar vacío";
            }
            
            if (empty($contrasena)) {
                $errores['contrasena'] = "La contraseña no puede estar vacía";
            }
            
            if (empty($errores)) {
                include_once('models/usuarios.php');
                $uDAO = new UsuarioDAO();
                $usuario_encontrado = $uDAO->getUser($usuario, $contrasena);
                
                if ($usuario_encontrado) {
                    // Guardar datos del usuario en la sesión
                    $_SESSION['usuario'] = $usuario_encontrado['username'];
                    $_SESSION['usuario_id'] = $usuario_encontrado['id'];
                    $_SESSION['rol'] = $usuario_encontrado['rol'];
                    
                    // Registrar en el log para depuración
                    error_log("Usuario logueado: ID=".$_SESSION['usuario_id'].", Username=".$_SESSION['usuario'].", Rol=".$_SESSION['rol']);
                    
                    // Redirigir a la página guardada o a la principal
                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirect = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                        header("Location: " . $redirect);
                    } else {
                        header("Location: index.php");
                    }
                    exit();
                } else {
                    $errores['general'] = "Usuario o contraseña incorrectos";
                    View::show("login", $errores);
                }
            } else {
                View::show("login", $errores);
            }
        } else {
            header("Location: index.php?controller=UsuController&action=showiniciosesion");
            exit();
        }
    }
    
    // Cerrar sesión
    public function cerrarsesion() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION = array();
        session_destroy();
        
        header("Location: index.php");
        exit();
    }
    
    // Método para listar pedidos
    public function listarPedidos() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Depuración: Registrar estado actual
        error_log("Iniciando listarPedidos()");
        error_log("Usuario actual: " . (isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'No definido'));
        error_log("Rol actual: " . (isset($_SESSION['rol']) ? $_SESSION['rol'] : 'No definido'));
        
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            $_SESSION['mensaje'] = "No tienes permisos para acceder a esta sección.";
            header("Location: index.php");
            exit();
        }
        
        // Cargar el modelo de pedidos
        include_once("models/pedidos.php");
        $pDAO = new PedidoDAO();
        
        // Verificar conexión a la base de datos
        try {
            if (!$pDAO->db_con) {
                error_log("Error: No hay conexión a la base de datos en PedidoDAO");
            }
        } catch (Exception $e) {
            error_log("Error al verificar conexión a DB: " . $e->getMessage());
        }
        
        // Obtener pedidos
        $pedidos = $pDAO->getAllPedidos();
        
        // Depuración: Verificar si se obtuvieron pedidos
        error_log("Pedidos obtenidos: " . count($pedidos));
        if (empty($pedidos)) {
            error_log("No se encontraron pedidos. Verificando estructura de la tabla...");
            
            // Intentar obtener la estructura de la tabla para depuración
            try {
                $db = Database::connect();
                $query = "DESCRIBE Pedidos";
                $stmt = $db->query($query);
                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                error_log("Columnas de la tabla Pedidos: " . implode(", ", $columns));
                
                // Verificar si hay datos en la tabla
                $query = "SELECT COUNT(*) as total FROM Pedidos";
                $stmt = $db->query($query);
                $count = $stmt->fetch(PDO::FETCH_ASSOC);
                error_log("Total de registros en Pedidos: " . $count['total']);
                
                // Intentar obtener todos los pedidos directamente para ver si hay error
                $query = "SELECT * FROM Pedidos";
                $stmt = $db->query($query);
                $allPedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("Pedidos directos: " . count($allPedidos));
                if (count($allPedidos) > 0) {
                    error_log("Ejemplo de pedido: " . print_r($allPedidos[0], true));
                }
            } catch (Exception $e) {
                error_log("Error al verificar tabla Pedidos: " . $e->getMessage());
            }
        } else {
            error_log("Primer pedido: " . print_r($pedidos[0], true));
        }
        
        // Mostrar la vista con los pedidos
        View::show("listarPedidos", $pedidos);
    }
    
    // Método adicional para ver detalles de un pedido específico
    public function verDetallePedido() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            $_SESSION['mensaje'] = "No tienes permisos para acceder a esta sección.";
            header("Location: index.php");
            exit();
        }
        
        if (isset($_REQUEST['id'])) {
            $pedido_id = $_REQUEST['id'];
            
            include_once("models/pedidos.php");
            $pDAO = new PedidoDAO();
            
            // Obtener detalles del pedido
            $detalles = $pDAO->getDetallePedido($pedido_id);
            
            // Obtener información del pedido
            $pedidoInfo = $pDAO->getPedidoById($pedido_id);
            
            if ($pedidoInfo) {
                View::show("detallePedido", [
                    'pedido' => $pedidoInfo,
                    'detalles' => $detalles
                ]);
            } else {
                $_SESSION['mensaje'] = "Pedido no encontrado.";
                header("Location: index.php?controller=UsuController&action=listarPedidos");
                exit();
            }
        } else {
            header("Location: index.php?controller=UsuController&action=listarPedidos");
            exit();
        }
    }
}
?>
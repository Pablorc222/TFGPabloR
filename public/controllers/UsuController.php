<?php
class UsuController {
    
    // Mostrar formulario de login
    public function showiniciosesion() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        View::show("login");
    }
    
    // Procesar inicio de sesión
    public function validacioniniciosesion() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        $errores = array();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
            $contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';
            
            // Validar campos
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
                    // Iniciar sesión del usuario
                    $_SESSION['usuario'] = $usuario_encontrado['username'];
                    $_SESSION['usuario_id'] = $usuario_encontrado['id'];
                    $_SESSION['rol'] = $usuario_encontrado['rol'];
                    
                    // Redirigir a página guardada o principal
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
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        $_SESSION = array();
        session_destroy();
        
        header("Location: index.php");
        exit();
    }

    // Alias para cerrar sesión (compatibilidad)
    public function logout() {
        $this->cerrarsesion();
    }
    
    // Listar todos los pedidos (solo admin)
    public function listarPedidos() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        // Verificar permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            $_SESSION['mensaje'] = "No tienes permisos para acceder a esta sección.";
            header("Location: index.php");
            exit();
        }
        
        // Cargar pedidos
        include_once("models/pedidos.php");
        $pDAO = new PedidoDAO();
        $pedidos = $pDAO->getAllPedidos();
        
        // Debug temporal: ver qué campos llegan
        if (!empty($pedidos)) {
            error_log("Campos del primer pedido: " . print_r(array_keys($pedidos[0]), true));
        }
        
        View::show("listarPedidos", $pedidos);
    }
    
    // Ver detalles de pedido específico (solo admin)
    public function verDetallePedido() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        // Verificar permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            $_SESSION['mensaje'] = "No tienes permisos para acceder a esta sección.";
            header("Location: index.php");
            exit();
        }
        
        if (isset($_REQUEST['id'])) {
            $pedido_id = $_REQUEST['id'];
            
            include_once("models/pedidos.php");
            $pDAO = new PedidoDAO();
            
            // Obtener información del pedido y sus detalles
            $detalles = $pDAO->getDetallePedido($pedido_id);
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

    // Cambiar estado de pedido (solo admin)
    public function cambiarEstadoPedido() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        // Verificar permisos
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            header("Location: index.php");
            exit();
        }
        
        if (isset($_REQUEST['id']) && isset($_REQUEST['estado'])) {
            include_once("models/pedidos.php");
            $pDAO = new PedidoDAO();
            $pDAO->cambiarEstadoPedido($_REQUEST['id'], $_REQUEST['estado']);
        }
        
        header("Location: index.php?controller=UsuController&action=listarPedidos");
        exit();
    }
}
?>
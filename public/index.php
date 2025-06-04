<?php
session_start();

// Dependencias necesarias
include_once("views/View.php");
include_once("controllers/ProductsController.php");  
include_once("controllers/UsuController.php");
include_once("controllers/ValoracionesController.php");
include_once("controllers/ListaDeseosController.php");
include_once("controllers/DashboardController.php");

// Obtener el controlador y la acción desde la URL
$controller = isset($_REQUEST['controller']) ? $_REQUEST['controller'] : 'ProductController';  // Cambiado default
// Normalizar nombres - ahora ambos van a ProductController
if($controller == 'ProductsController') $controller = 'ProductController';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'getAllProducts';

// Verificación de seguridad
if(!is_string($action)) {
    $action = 'getAllProducts';
}

// Verificar si el usuario ha iniciado sesión
if(isset($_SESSION['usuario'])) {
    // Usuario logueado
    $controllerInstance = new $controller();
    if(method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        $productController = new ProductController();  // Cambiado
        $productController->getAllProducts();
    }
} else {
    // Usuario no logueado
    if(isset($_REQUEST['action']) && isset($_REQUEST['controller'])) {
        $authRequired = [
            'ValoracionesController' => ['*']
        ];
        
        $exceptions = [
            'ListaDeseosController' => ['isInListaDeseos'],
            'ValoracionesController' => ['verValoracionesProducto']
        ];
        
        $reqController = $_REQUEST['controller'];
        $reqAction = $_REQUEST['action'];
        
        $requiresAuth = false;
        
        if(isset($authRequired[$reqController])) {
            if($authRequired[$reqController][0] === '*') {
                $requiresAuth = true;
                if(isset($exceptions[$reqController]) && in_array($reqAction, $exceptions[$reqController])) {
                    $requiresAuth = false;
                }
            }
        }
        
        if($requiresAuth) {
            $_SESSION['redirect_after_login'] = "index.php?controller=" . $reqController . "&action=" . $reqAction;
            $_SESSION['mensaje'] = "Debes iniciar sesión para acceder a esta funcionalidad.";
            header("Location: index.php?controller=UsuController&action=showiniciosesion");
            exit();
        } else {
            $cont = $reqController;
            // Normalizar nombres
            if($cont == 'ProductsController') $cont = 'ProductController';
            
            try {
                $controllerObj = new $cont();
                if(method_exists($controllerObj, $reqAction)) {
                    $controllerObj->$reqAction();
                } else {
                    throw new Exception("Método no encontrado");
                }
            } catch(Exception $e) {
                $productController = new ProductController();  // Cambiado
                $productController->getAllProducts();
            }
        }
    } else {
        $productController = new ProductController();  // Cambiado
        $productController->getAllProducts();
    }
}
?>
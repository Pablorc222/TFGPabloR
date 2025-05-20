<?php
session_start();

// Dependencias necesarias
include_once("views/View.php");
include_once("controllers/ProductsController.php");
include_once("controllers/UsuController.php");
include_once("controllers/ValoracionesController.php");
include_once("controllers/ListaDeseosController.php");

// Obtener el controlador y la acción desde la URL
$controller = isset($_REQUEST['controller']) ? $_REQUEST['controller'] : 'ProductsController';
if($controller == 'ProductController') $controller = 'ProductsController';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'getAllProducts';

// Verificación de seguridad - asegurarse de que action es una cadena de texto
if(!is_string($action)) {
    $action = 'getAllProducts';
}

// Verificar si el usuario ha iniciado sesión
if(isset($_SESSION['usuario'])) {
    // Si el usuario está logueado, proceder con la acción solicitada
    $controllerInstance = new $controller();
    if(method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    } else {
        // Método no encontrado, redirigir a página de inicio
        $productController = new ProductsController();
        $productController->getAllProducts();
    }
} else {
    // Si el usuario no está logueado, verificar la acción solicitada
    if(isset($_REQUEST['action']) && isset($_REQUEST['controller'])) {
        // Acciones que requieren autenticación
        $authRequired = [
            'CartController' => ['*'],
            'OrderController' => ['*'],
            'ListaDeseosController' => ['*'], 
            'ValoracionesController' => ['*']
        ];
        
        // Excepciones que no requieren autenticación
        $exceptions = [
            'ListaDeseosController' => ['isInListaDeseos'],
            'ValoracionesController' => ['verValoracionesProducto']
        ];
        
        $reqController = $_REQUEST['controller'];
        $reqAction = $_REQUEST['action'];
        
        // Verificar si la acción requiere autenticación
        $requiresAuth = false;
        
        if(isset($authRequired[$reqController])) {
            if($authRequired[$reqController][0] === '*') {
                $requiresAuth = true;
                
                // Verificar excepciones
                if(isset($exceptions[$reqController]) && in_array($reqAction, $exceptions[$reqController])) {
                    $requiresAuth = false;
                }
            }
        }
        
        // Si requiere autenticación, redirigir al login
        if($requiresAuth) {
            $_SESSION['redirect_after_login'] = "index.php?controller=" . $reqController . "&action=" . $reqAction;
            $_SESSION['mensaje'] = "Debes iniciar sesión para acceder a esta funcionalidad.";
            header("Location: index.php?controller=UsuController&action=showiniciosesion");
            exit();
        } else {
            // Si no requiere autenticación, procesar la solicitud
            $cont = $reqController;
            if($cont == 'ProductController') $cont = 'ProductsController';
            
            try {
                $controllerObj = new $cont();
                if(method_exists($controllerObj, $reqAction)) {
                    $controllerObj->$reqAction();
                } else {
                    throw new Exception("Método no encontrado");
                }
            } catch(Exception $e) {
                // Si hay error, mostrar la página de productos
                $productController = new ProductsController();
                $productController->getAllProducts();
            }
        }
    } else {
        // Si no hay acción en la URL, mostrar los productos
        $productController = new ProductsController();
        $productController->getAllProducts();
    }
}
?>
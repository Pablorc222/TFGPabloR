<?php
session_start();

// Incluir todos los controladores
include_once("controllers/ProductsController.php");
include_once("controllers/UsuController.php");
include_once("controllers/ValoracionesController.php");
include_once("controllers/ListaDeseosController.php");
include_once("controllers/DashboardController.php");

// Incluir la clase View
include_once("views/View.php");

$controller = $_GET['controller'] ?? 'ProductController';
$action = $_GET['action'] ?? 'getAllProducts';

switch($controller) {
    case 'ProductController':
        $productController = new ProductController();
        if (method_exists($productController, $action)) {
            $productController->$action();
        } else {
            $productController->getAllProducts();
        }
        break;
        
    case 'UsuController':
        $usuController = new UsuController();
        if (method_exists($usuController, $action)) {
            $usuController->$action();
        } else {
            header("Location: index.php");
        }
        break;
        
    case 'ValoracionesController':
        $valoracionesController = new ValoracionesController();
        if (method_exists($valoracionesController, $action)) {
            $valoracionesController->$action();
        } else {
            header("Location: index.php");
        }
        break;
        
    case 'ListaDeseosController':
        $listaDeseosController = new ListaDeseosController();
        if (method_exists($listaDeseosController, $action)) {
            $listaDeseosController->$action();
        } else {
            $listaDeseosController->mostrarListaDeseos();
        }
        break;
        
    case 'DashboardController':
        $dashboardController = new DashboardController();
        if (method_exists($dashboardController, $action)) {
            $dashboardController->$action();
        } else {
            $dashboardController->mostrarDashboard();
        }
        break;
        
    default:
        // Si no encuentra el controlador, ir al catálogo principal
        $productController = new ProductController();
        $productController->getAllProducts();
        break;
}
?>
<?php
include_once("views/View.php");
include_once("models/estadisticas.php");

class DashboardController {
    
    public function mostrarDashboard() {
        // Verificar admin
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
            header("Location: index.php");
            exit();
        }
        
        // Obtener datos
        $stats = new EstadisticasDAO();
        $data = array(
            'ingresos_totales' => $stats->getIngresosTotales(),
            'total_usuarios' => $stats->getTotalUsuarios(),
            'total_pedidos' => $stats->getTotalPedidos(),
            'pedidos_mes' => $stats->getPedidosMesActual()
        );
        
        View::show("dashboard", $data);
    }
}
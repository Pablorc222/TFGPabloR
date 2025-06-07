<?php
class EstadisticasDAO {
    private $conexion;
    
    public function __construct() {
        $dsn = 'mysql:host=mariadb;dbname=FootStore;charset=UTF8';
        $this->conexion = new PDO($dsn, 'root', 'changepassword');
    }
    
    public function getIngresosTotales() {
        $stmt = $this->conexion->query("SELECT SUM(total) as total FROM Pedidos");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    public function getTotalUsuarios() {
        $stmt = $this->conexion->query("SELECT COUNT(*) as total FROM Usuarios");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    public function getTotalPedidos() {
        $stmt = $this->conexion->query("SELECT COUNT(*) as total FROM Pedidos");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    public function getPedidosMesActual() {
        $stmt = $this->conexion->query("SELECT COUNT(*) as total FROM Pedidos WHERE MONTH(fecha) = MONTH(CURDATE())");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Guardar estadísticas en la tabla Dashboard
    public function guardarEstadisticas() {
        $ingresos = $this->getIngresosTotales();
        $usuarios = $this->getTotalUsuarios();
        $pedidos = $this->getTotalPedidos();
        
        $stmt = $this->conexion->prepare("INSERT INTO Dashboard (fecha, total_ventas, total_usuarios, total_pedidos) VALUES (CURDATE(), ?, ?, ?)");
        $stmt->execute([$ingresos, $usuarios, $pedidos]);
        return true;
    }
    
    // Obtener estadísticas guardadas de la tabla Dashboard
    public function getEstadisticasGuardadas($fecha = null) {
        if ($fecha) {
            $stmt = $this->conexion->prepare("SELECT * FROM Dashboard WHERE fecha = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$fecha]);
        } else {
            $stmt = $this->conexion->query("SELECT * FROM Dashboard ORDER BY created_at DESC LIMIT 1");
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Obtener productos con stock bajo
    public function getProductosStockBajo($limite = 5) {
        $stmt = $this->conexion->prepare("SELECT ID_Producto, Nombre_Producto, Stock FROM Productos WHERE Stock <= ? ORDER BY Stock ASC");
        $stmt->execute([$limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener total de productos
    public function getTotalProductos() {
        $stmt = $this->conexion->query("SELECT COUNT(*) as total FROM Productos");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Obtener productos más vendidos
    public function getProductosMasVendidos($limite = 5) {
        $query = "SELECT p.Nombre_Producto, SUM(dp.cantidad) as total_vendido 
                  FROM Productos p 
                  JOIN DetallePedidos dp ON p.ID_Producto = dp.producto_id 
                  GROUP BY p.ID_Producto 
                  ORDER BY total_vendido DESC 
                  LIMIT ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute([$limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener ventas del mes actual
    public function getVentasMesActual() {
        $stmt = $this->conexion->query("SELECT SUM(total) as total FROM Pedidos WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}
?>
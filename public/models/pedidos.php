<?php
require_once 'db/database.php';

if (!class_exists('PedidoDAO')) {
    class PedidoDAO {
        public $db_con;
        
        public function __construct() {
            $this->db_con = Database::connect();
        }
        
        // Crear nuevo pedido
        public function createPedido($usuario_id, $total) {
            if (empty($usuario_id) || !is_numeric($usuario_id)) return null;
            
            $stmt = $this->db_con->prepare("INSERT INTO Pedidos (usuario_id, total, fecha, estado) VALUES (?, ?, NOW(), 'pendiente')");
            $result = $stmt->execute([$usuario_id, $total]);
            
            return $result ? $this->db_con->lastInsertId() : null;
        }
        
        // Añadir producto al pedido
        public function addProductToPedido($pedido_id, $producto_id, $cantidad, $precio_unitario) {
            if (empty($pedido_id) || !is_numeric($pedido_id)) return false;
            
            try {
                $stmt = $this->db_con->prepare("INSERT INTO DetallePedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
                return $stmt->execute([$pedido_id, $producto_id, $cantidad, $precio_unitario]);
            } catch (PDOException $e) {
                $stmt = $this->db_con->prepare("INSERT INTO Pedidos_productos (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
                return $stmt->execute([$pedido_id, $producto_id, $cantidad, $precio_unitario]);
            }
        }
        
        // Obtener pedidos del usuario
        public function getPedidosByUsuario($usuario_id) {
            $stmt = $this->db_con->prepare("SELECT * FROM Pedidos WHERE usuario_id = ? ORDER BY fecha DESC");
            $stmt->execute([$usuario_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Obtener pedido específico
        public function getPedidoById($pedido_id) {
            $stmt = $this->db_con->prepare("SELECT p.*, u.username FROM Pedidos p JOIN Usuarios u ON p.usuario_id = u.id WHERE p.id = ?");
            $stmt->execute([$pedido_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // Obtener detalles del pedido
        public function getDetallePedido($pedido_id) {
            try {
                $stmt = $this->db_con->prepare("SELECT d.*, p.Nombre AS Nombre_Producto FROM DetallePedidos d JOIN Productos p ON d.producto_id = p.ID_Producto WHERE d.pedido_id = ?");
                $stmt->execute([$pedido_id]);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($result) > 0) return $result;
                
                $stmt = $this->db_con->prepare("SELECT pp.*, p.Nombre AS Nombre_Producto FROM Pedidos_productos pp JOIN Productos p ON pp.producto_id = p.ID_Producto WHERE pp.pedido_id = ?");
                $stmt->execute([$pedido_id]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return [];
            }
        }
        
        // Obtener todos los pedidos (para admin) - CORREGIDO
        public function getAllPedidos() {
            $query = "SELECT p.id, u.username as usuario_nombre, p.total, p.fecha, COALESCE(p.estado, 'Pendiente') as estado 
                      FROM Pedidos p LEFT JOIN Usuarios u ON p.usuario_id = u.id ORDER BY p.fecha DESC";
            
            $stmt = $this->db_con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Cambiar estado del pedido
        public function cambiarEstadoPedido($pedido_id, $nuevo_estado) {
            $stmt = $this->db_con->prepare("UPDATE Pedidos SET estado = ? WHERE id = ?");
            return $stmt->execute([$nuevo_estado, $pedido_id]);
        }

        // Eliminar pedido completo
        public function deletePedido($pedido_id) {
            try {
                $stmt = $this->db_con->prepare("DELETE FROM DetallePedidos WHERE pedido_id = ?");
                $stmt->execute([$pedido_id]);
            } catch (PDOException $e) {
                $stmt = $this->db_con->prepare("DELETE FROM Pedidos_productos WHERE pedido_id = ?");
                $stmt->execute([$pedido_id]);
            }
            
            $stmt = $this->db_con->prepare("DELETE FROM Pedidos WHERE id = ?");
            return $stmt->execute([$pedido_id]);
        }

        // Actualizar total del pedido
        public function updateTotalPedido($pedido_id) {
            try {
                $query = "UPDATE Pedidos SET total = (SELECT SUM(cantidad * precio_unitario) FROM DetallePedidos WHERE pedido_id = ?) WHERE id = ?";
            } catch (PDOException $e) {
                $query = "UPDATE Pedidos SET total = (SELECT SUM(cantidad * precio_unitario) FROM Pedidos_productos WHERE pedido_id = ?) WHERE id = ?";
            }
            
            $stmt = $this->db_con->prepare($query);
            return $stmt->execute([$pedido_id, $pedido_id]);
        }

        // Obtener estadísticas de pedidos
        public function getEstadisticasPedidos() {
            $query = "SELECT COUNT(*) as total_pedidos, SUM(total) as total_ventas, AVG(total) as promedio_pedido,
                      COUNT(CASE WHEN COALESCE(estado, 'Pendiente') = 'Pendiente' THEN 1 END) as pendientes,
                      COUNT(CASE WHEN estado = 'Enviado' THEN 1 END) as enviados,
                      COUNT(CASE WHEN estado = 'Entregado' THEN 1 END) as entregados,
                      COUNT(CASE WHEN estado = 'Cancelado' THEN 1 END) as cancelados FROM Pedidos";
            
            $stmt = $this->db_con->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Obtener pedidos por estado
        public function getPedidosByEstado($estado) {
            $stmt = $this->db_con->prepare("SELECT p.*, u.username FROM Pedidos p LEFT JOIN Usuarios u ON p.usuario_id = u.id WHERE COALESCE(p.estado, 'Pendiente') = ? ORDER BY p.fecha DESC");
            $stmt->execute([$estado]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Obtener pedidos recientes
        public function getPedidosRecientes($limite = 10) {
            $stmt = $this->db_con->prepare("SELECT p.*, u.username FROM Pedidos p LEFT JOIN Usuarios u ON p.usuario_id = u.id WHERE p.fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY p.fecha DESC LIMIT ?");
            $stmt->execute([$limite]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Buscar pedidos por usuario
        public function buscarPedidosPorUsuario($termino_busqueda) {
            $stmt = $this->db_con->prepare("SELECT p.*, u.username FROM Pedidos p LEFT JOIN Usuarios u ON p.usuario_id = u.id WHERE u.username LIKE ? OR u.email LIKE ? ORDER BY p.fecha DESC");
            $termino = '%' . $termino_busqueda . '%';
            $stmt->execute([$termino, $termino]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>
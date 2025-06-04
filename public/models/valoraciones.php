<?php
require_once 'db/database.php';

class ValoracionesDAO {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // Guardar valoración del usuario
    public function guardarValoracion($usuario_id, $producto_id, $puntuacion, $comentario) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Valoraciones (usuario_id, producto_id, puntuacion, comentario, fecha) 
                VALUES (?, ?, ?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE 
                puntuacion = ?, comentario = ?, fecha = NOW()
            ");
            return $stmt->execute([$usuario_id, $producto_id, $puntuacion, $comentario, $puntuacion, $comentario]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Obtener valoraciones de un producto
    public function getValoracionesProducto($producto_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT v.*, u.username FROM Valoraciones v 
                JOIN Usuarios u ON v.usuario_id = u.id 
                WHERE v.producto_id = ? 
                ORDER BY v.fecha DESC
            ");
            $stmt->execute([$producto_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Obtener valoraciones del usuario
    public function getValoracionesByUsuarioId($usuario_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT v.*, p.Nombre_Producto, p.Imagen, p.ID_Producto as producto_id, p.Precio
                FROM Valoraciones v
                JOIN Productos p ON v.producto_id = p.ID_Producto
                WHERE v.usuario_id = ?
                ORDER BY v.fecha DESC
            ");
            $stmt->execute([$usuario_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Obtener valoraciones por producto (para detalles)
    public function getValoracionesByProductoId($producto_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT v.*, u.username, v.fecha as fecha_creacion
                FROM Valoraciones v
                JOIN Usuarios u ON v.usuario_id = u.id
                WHERE v.producto_id = ?
                ORDER BY v.fecha DESC
            ");
            $stmt->execute([$producto_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
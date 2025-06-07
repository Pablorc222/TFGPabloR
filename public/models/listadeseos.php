<?php
require_once 'db/database.php';

if (!class_exists('ListaDeseosDAO')) {
    class ListaDeseosDAO {
        private $db_con;

        public function __construct() {
            $this->db_con = Database::connect();
        }

        // Obtener productos favoritos del usuario
        public function getProductosListaDeseos($usuario_id) {
            try {
                $stmt = $this->db_con->prepare("
                    SELECT p.* 
                    FROM ListaDeseos ld
                    JOIN Productos p ON ld.producto_id = p.ID_Producto
                    WHERE ld.usuario_id = ?
                ");
                $stmt->execute([$usuario_id]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return [];
            }
        }

        // Verificar si producto está en favoritos
        public function isProductoEnListaDeseos($usuario_id, $producto_id) {
            try {
                $stmt = $this->db_con->prepare("
                    SELECT COUNT(*) FROM ListaDeseos 
                    WHERE usuario_id = ? AND producto_id = ?
                ");
                $stmt->execute([$usuario_id, $producto_id]);
                return $stmt->fetchColumn() > 0;
            } catch (PDOException $e) {
                return false;
            }
        }

        // Añadir producto a favoritos
        public function agregarProducto($usuario_id, $producto_id) {
            // No agregar si ya existe
            if ($this->isProductoEnListaDeseos($usuario_id, $producto_id)) {
                return true;
            }
            
            try {
                $stmt = $this->db_con->prepare("
                    INSERT INTO ListaDeseos (usuario_id, producto_id) 
                    VALUES (?, ?)
                ");
                return $stmt->execute([$usuario_id, $producto_id]);
            } catch (PDOException $e) {
                return false;
            }
        }

        // Quitar producto de favoritos
        public function eliminarProducto($usuario_id, $producto_id) {
            try {
                $stmt = $this->db_con->prepare("
                    DELETE FROM ListaDeseos 
                    WHERE usuario_id = ? AND producto_id = ?
                ");
                $stmt->execute([$usuario_id, $producto_id]);
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
    }
}
?>
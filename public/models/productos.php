<?php
require_once 'db/database.php';

if (!class_exists('ProductoDAO')) {
    class ProductoDAO {
        public $db_con;
        
        public function __construct() {
            $this->db_con = Database::connect();
        }
        
        // Obtener todos los productos
        public function getAllProducts() {
            try {
                $stmt = $this->db_con->query("SELECT * FROM Productos");
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return [];
            }
        }
        
        // Obtener producto por ID
        public function getProductById($id) {
            try {
                $stmt = $this->db_con->prepare("SELECT * FROM Productos WHERE ID_producto = ?");
                $stmt->execute([$id]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return null;
            }
        }
        
        // Crear nuevo producto
        public function insertProduct($producto) {
            try {
                $stmt = $this->db_con->prepare("
                    INSERT INTO Productos (Nombre_Producto, precio, Descripcion, Imagen)
                    VALUES (?, ?, ?, ?)
                ");
                return $stmt->execute([
                    $producto['Nombre'], 
                    $producto['Precio'], 
                    $producto['Descripcion'], 
                    $producto['Imagen'] ?? ''
                ]);
            } catch (PDOException $e) {
                return false;
            }
        }
        
        // Eliminar producto
        public function borrarprod($id) {
            try {
                $stmt = $this->db_con->prepare("DELETE FROM Productos WHERE ID_Producto = ?");
                return $stmt->execute([$id]);
            } catch (PDOException $e) {
                return false;
            }
        }
    }
}
?>
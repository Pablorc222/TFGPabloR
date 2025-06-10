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
        
        // Verificar si hay stock suficiente
        public function verificarStock($producto_id, $cantidad_solicitada) {
            try {
                $stmt = $this->db_con->prepare("SELECT Stock, Nombre_Producto FROM Productos WHERE ID_Producto = ?");
                $stmt->execute([$producto_id]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$producto) {
                    return ['disponible' => false, 'mensaje' => 'Producto no encontrado'];
                }
                
                $stock_actual = $producto['Stock'];
                $nombre = $producto['Nombre_Producto'];
                
                if ($stock_actual >= $cantidad_solicitada) {
                    return ['disponible' => true, 'stock_actual' => $stock_actual];
                } else {
                    return [
                        'disponible' => false, 
                        'mensaje' => "❌ $nombre: Solo hay $stock_actual unidades disponibles, no se pueden comprar $cantidad_solicitada",
                        'stock_actual' => $stock_actual
                    ];
                }
            } catch (PDOException $e) {
                return ['disponible' => false, 'mensaje' => 'Error al verificar stock'];
            }
        }
        
        // Reducir stock después de compra exitosa
        public function reducirStock($producto_id, $cantidad) {
            try {
                $stmt = $this->db_con->prepare("UPDATE Productos SET Stock = Stock - ? WHERE ID_Producto = ? AND Stock >= ?");
                return $stmt->execute([$cantidad, $producto_id, $cantidad]);
            } catch (PDOException $e) {
                return false;
            }
        }
        
        // Obtener stock actual de un producto
        public function getStock($producto_id) {
            try {
                $stmt = $this->db_con->prepare("SELECT Stock FROM Productos WHERE ID_Producto = ?");
                $stmt->execute([$producto_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result ? $result['Stock'] : 0;
            } catch (PDOException $e) {
                return 0;
            }
        }
    }
}
?>
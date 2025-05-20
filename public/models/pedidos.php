<?php
require_once 'db/database.php';

// Verificar si la clase ya existe antes de declararla
if (!class_exists('PedidoDAO')) {
    class PedidoDAO {
        public $db_con;
        
        public function __construct() {
            $this->db_con = Database::connect();
        }
        
        // Crear pedido
        public function createPedido($usuario_id, $total) {
            try {
                // Validar parámetros
                if (empty($usuario_id) || !is_numeric($usuario_id)) {
                    error_log("Error: ID de usuario inválido: $usuario_id");
                    return null;
                }
                
                error_log("Creando pedido para usuario ID: $usuario_id, Total: $total");
                
                $stmt = $this->db_con->prepare("
                    INSERT INTO Pedidos (usuario_id, total, fecha) 
                    VALUES (?, ?, NOW())
                ");
                $result = $stmt->execute([$usuario_id, $total]);
                
                if ($result) {
                    $pedido_id = $this->db_con->lastInsertId();
                    error_log("Pedido creado exitosamente con ID: $pedido_id");
                    return $pedido_id;
                } else {
                    error_log("Error al insertar pedido: " . print_r($stmt->errorInfo(), true));
                    return null;
                }
            } catch (PDOException $e) {
                error_log("Excepción al crear pedido: " . $e->getMessage());
                return null;
            }
        }
        
        // Añadir producto a pedido
        public function addProductToPedido($pedido_id, $producto_id, $cantidad, $precio_unitario) {
            try {
                // Validar parámetros
                if (empty($pedido_id) || !is_numeric($pedido_id)) {
                    error_log("Error: ID de pedido inválido: $pedido_id");
                    return false;
                }
                
                error_log("Añadiendo producto $producto_id al pedido $pedido_id");
                
                // Intentar con DetallePedidos primero
                try {
                    $stmt = $this->db_con->prepare("
                        INSERT INTO DetallePedidos (pedido_id, producto_id, cantidad, precio_unitario) 
                        VALUES (?, ?, ?, ?)
                    ");
                    return $stmt->execute([$pedido_id, $producto_id, $cantidad, $precio_unitario]);
                } catch (PDOException $e) {
                    // Si falla, intentar con Pedidos_productos
                    error_log("Error con DetallePedidos, intentando Pedidos_productos: " . $e->getMessage());
                    $stmt = $this->db_con->prepare("
                        INSERT INTO Pedidos_productos (pedido_id, producto_id, cantidad, precio_unitario) 
                        VALUES (?, ?, ?, ?)
                    ");
                    return $stmt->execute([$pedido_id, $producto_id, $cantidad, $precio_unitario]);
                }
            } catch (PDOException $e) {
                error_log("Error al añadir producto a pedido: " . $e->getMessage());
                return false;
            }
        }
        
        // Obtener pedidos de usuario
        public function getPedidosByUsuario($usuario_id) {
            try {
                $stmt = $this->db_con->prepare("SELECT * FROM Pedidos WHERE usuario_id = ?");
                $stmt->execute([$usuario_id]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error al obtener pedidos de usuario: " . $e->getMessage());
                return [];
            }
        }
        
        // Obtener pedido por ID
        public function getPedidoById($pedido_id) {
            try {
                $stmt = $this->db_con->prepare("
                    SELECT p.*, u.username 
                    FROM Pedidos p
                    JOIN Usuarios u ON p.usuario_id = u.id
                    WHERE p.ID_Pedido = ?
                ");
                $stmt->execute([$pedido_id]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Error al obtener pedido por ID: " . $e->getMessage());
                return null;
            }
        }
        
        // Obtener detalles de un pedido específico
        public function getDetallePedido($pedido_id) {
            try {
                // Intentar con DetallePedidos primero
                try {
                    $stmt = $this->db_con->prepare("
                        SELECT d.*, p.Nombre_Producto 
                        FROM DetallePedidos d
                        JOIN Productos p ON d.producto_id = p.ID_Producto
                        WHERE d.pedido_id = ?
                    ");
                    $stmt->execute([$pedido_id]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($result) > 0) {
                        return $result;
                    }
                    
                    // Si no hay resultados, intentar con la otra tabla
                    throw new PDOException("No hay resultados en DetallePedidos");
                } catch (PDOException $e) {
                    // Intentar con Pedidos_productos
                    $stmt = $this->db_con->prepare("
                        SELECT pp.*, p.Nombre_Producto 
                        FROM Pedidos_productos pp
                        JOIN Productos p ON pp.producto_id = p.ID_Producto
                        WHERE pp.pedido_id = ?
                    ");
                    $stmt->execute([$pedido_id]);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (PDOException $e) {
                error_log("Error al obtener detalle de pedido: " . $e->getMessage());
                return [];
            }
        }
        
        // Obtener todos los pedidos (para administradores)
        public function getAllPedidos() {
            try {
                error_log("=== INICIANDO getAllPedidos ===");
                
                // Comprobar conexión
                if (!$this->db_con) {
                    $this->db_con = Database::connect();
                    error_log("Reconectando a la base de datos...");
                }
                
                // Verificar que la tabla existe
                $tableCheck = $this->db_con->query("SHOW TABLES LIKE 'Pedidos'");
                if ($tableCheck->rowCount() === 0) {
                    error_log("ERROR: Tabla 'Pedidos' no encontrada");
                    return [];
                }
                
                // Obtener nombre de columnas
                $columnsStmt = $this->db_con->query("DESCRIBE Pedidos");
                $columns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);
                error_log("Columnas en Pedidos: " . implode(", ", $columns));
                
                // Determinar columna ID
                $idColumn = in_array('ID_Pedido', $columns) ? 'ID_Pedido' : 
                           (in_array('id', $columns) ? 'id' : 
                            (in_array('ID', $columns) ? 'ID' : 'ID_Pedido')); // Default a ID_Pedido
                
                error_log("Columna ID seleccionada: " . $idColumn);
                
                // Verificar usuarios
                $usuariosCheck = $this->db_con->query("SHOW TABLES LIKE 'Usuarios'");
                if ($usuariosCheck->rowCount() === 0) {
                    error_log("ADVERTENCIA: Tabla 'Usuarios' no encontrada, no se podrán obtener nombres");
                    
                    // Consulta sin JOIN
                    $query = "
                        SELECT 
                            p.{$idColumn} as ID_Pedido,
                            p.usuario_id as usuario_nombre,
                            p.total,
                            p.fecha
                        FROM Pedidos p
                        ORDER BY p.fecha DESC
                    ";
                } else {
                    // Verificar campos en Usuarios
                    $userColumnsStmt = $this->db_con->query("DESCRIBE Usuarios");
                    $userColumns = $userColumnsStmt->fetchAll(PDO::FETCH_COLUMN);
                    
                    // Determinar campo de nombre de usuario
                    $usernameField = in_array('username', $userColumns) ? 'username' : 
                                    (in_array('nombre', $userColumns) ? 'nombre' : 
                                     (in_array('email', $userColumns) ? 'email' : 'username'));
                    
                    error_log("Campo de nombre de usuario: " . $usernameField);
                    
                    // Consulta con JOIN
                    $query = "
                        SELECT 
                            p.{$idColumn} as ID_Pedido,
                            u.{$usernameField} as usuario_nombre,
                            p.total,
                            p.fecha
                        FROM Pedidos p
                        LEFT JOIN Usuarios u ON p.usuario_id = u.id
                        ORDER BY p.fecha DESC
                    ";
                }
                
                error_log("Consulta SQL: " . $query);
                
                // Ejecutar consulta
                $stmt = $this->db_con->prepare($query);
                $success = $stmt->execute();
                
                if (!$success) {
                    error_log("Error al ejecutar consulta: " . json_encode($stmt->errorInfo()));
                    return [];
                }
                
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("Total de pedidos encontrados: " . count($result));
                
                if (count($result) > 0) {
                    error_log("Primer pedido: " . json_encode($result[0]));
                    error_log("Último pedido: " . json_encode($result[count($result)-1]));
                }
                
                return $result;
            } catch (PDOException $e) {
                error_log("ERROR en getAllPedidos: " . $e->getMessage());
                return [];
            }
        }
    }
}
?>
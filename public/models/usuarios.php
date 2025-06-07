<?php 
require_once 'db/database.php';

if (!class_exists('UsuarioDAO')) {
    class UsuarioDAO {
        private $db_con;

        public function __construct() {
            $this->db_con = Database::connect();
        }

        // Validar credenciales de usuario
        public function getUser($usuario, $contrasena) {
            try {
                // Usuarios hardcoded para desarrollo
                if (($usuario === 'admin' && $contrasena === '1234') || 
                    ($usuario === 'usuario1' && $contrasena === '1234')) {
                    
                    $stmt = $this->db_con->prepare("SELECT * FROM Usuarios WHERE username = ?");
                    $stmt->execute([$usuario]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($result) {
                        $this->updateLastConnection($result['id']);
                        return $result;
                    }
                }
                
                // Verificar en base de datos
                $stmt = $this->db_con->prepare("SELECT * FROM Usuarios WHERE username = ?");
                $stmt->execute([$usuario]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result && ($contrasena === $result['password'])) {
                    $this->updateLastConnection($result['id']);
                    return $result;
                }
                
                return null;
            } catch (PDOException $e) {
                return null;
            }
        }

        // Actualizar última conexión del usuario
        private function updateLastConnection($userId) {
            try {
                $stmt = $this->db_con->prepare("UPDATE Usuarios SET ultima_conexion = NOW() WHERE id = ?");
                $stmt->execute([$userId]);
            } catch (PDOException $e) {
                // Ignorar errores
            }
        }

        // Obtener usuario por nombre
        public function getUserByUsername($username) {
            try {
                $stmt = $this->db_con->prepare("SELECT * FROM Usuarios WHERE username = ?");
                $stmt->execute([$username]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                return null;
            }
        }
    }
}
?>
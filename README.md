# ⚽ SoccerStore - Mi TFG de E-commerce

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

**Trabajo de Fin de Grado** - Tienda online de artículos de fútbol desarrollada con PHP y MySQL.

---

## 📋 ¿Qué es SoccerStore?

SoccerStore es mi proyecto de TFG, una tienda online donde puedes comprar camisetas de fútbol. He usado PHP para el backend, MySQL para la base de datos y algo de JavaScript para hacer la página más interactiva.

## ✨ Lo que puedes hacer

### Si eres usuario normal:
- 🔐 Registrarte y hacer login
- 👕 Ver todos los productos del catálogo
- 🛒 Añadir productos al carrito
- ⭐ Dejar reseñas y valoraciones
- ❤️ Guardar productos en tu lista de deseos
- 📦 Ver el historial de tus pedidos

### Si eres administrador:
- 📊 Ver estadísticas en el dashboard
- ➕ Añadir nuevos productos
- ✏️ Editar productos existentes
- 🗑️ Eliminar productos
- 👥 Gestionar usuarios
- 📈 Ver gráficos de ventas

## 🛠 Tecnologías que he usado

- **PHP**: Para toda la lógica del servidor
- **MySQL**: Base de datos donde guardo todo
- **HTML/CSS**: Para el diseño de las páginas
- **JavaScript**: Para las interacciones (como las estrellas de valoración)
- **Bootstrap**: Para que se vea mejor en móviles
- **Docker**: Para tener todo organizado en contenedores

## 📁 Cómo está organizado el proyecto

```
public/
├── controllers/           # Aquí está la lógica principal
│   ├── DashboardController.php
│   ├── ListaDeseosController.php
│   ├── ProductsController.php
│   ├── UsuController.php
│   └── ValoracionesController.php
├── db/
│   └── database.php       # Conexión a la base de datos
├── models/               # Clases para manejar los datos
│   ├── estadisticas.php
│   ├── lista_deseos.php
│   ├── pedidos.php
│   ├── productos.php
│   ├── usuarios.php
│   └── valoraciones.php
├── views/                # Las páginas que ve el usuario
│   ├── addProduct.php
│   ├── cart.php
│   ├── dashboard.php
│   ├── footer.php
│   ├── header.php
│   ├── lista_deseos.php
│   ├── listarProductos.php
│   ├── login.php
│   ├── productDetails.php
│   ├── valorar_producto.php
│   └── view.php
└── index.php            # Página principal
```

## 🗄 Base de Datos

He creado estas tablas principales:

```sql
-- Usuarios del sistema
CREATE TABLE Usuario (
    ID_Usuario INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Usuario VARCHAR(50),
    Email VARCHAR(100),
    Contraseña VARCHAR(255),
    Rol ENUM('usuario', 'administrador')
);

-- Productos de la tienda
CREATE TABLE Producto (
    ID_Producto INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Producto VARCHAR(100),
    Descripcion TEXT,
    Precio DECIMAL(10,2),
    Marca VARCHAR(50),
    Stock INT,
    Imagen VARCHAR(255)
);

-- Valoraciones de los usuarios
CREATE TABLE Valoracion (
    ID_Valoracion INT PRIMARY KEY AUTO_INCREMENT,
    ID_Usuario INT,
    ID_Producto INT,
    Puntuacion INT,
    Comentario TEXT,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario),
    FOREIGN KEY (ID_Producto) REFERENCES Producto(ID_Producto)
);

-- Lista de deseos
CREATE TABLE Lista_Deseos (
    ID_Usuario INT,
    ID_Producto INT,
    PRIMARY KEY (ID_Usuario, ID_Producto)
);
```

## 🚀 Cómo ejecutar el proyecto

### Con Docker (más fácil):

```bash
# Clonar el repositorio
git clone [tu-repositorio]

# Levantar los contenedores
docker-compose up -d

# La página estará en: http://localhost:8080
```

### Manual:
1. Instalar XAMPP o similar
2. Crear una base de datos llamada "SoccerStore"
3. Importar el archivo SQL
4. Poner los archivos en htdocs
5. Abrir en el navegador

## 📊 Algunos datos del proyecto

- **Tiempo que me llevó**: Unos 3-4 meses
- **Líneas de código**: Aproximadamente 3,000-4,000
- **Archivos PHP**: 20+
- **Tablas en la BD**: 6 principales
- **Funcionalidades**: Login, carrito, valoraciones, administración

## 🎯 Ejemplos de código

### Cómo conecto a la base de datos:
```php
<?php
class Database {
    private $host = 'mariadb';
    private $dbname = 'SoccerStore';
    private $username = 'root';
    private $password = 'secret';
    
    public function getConnection() {
        try {
            $pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", 
                          $this->username, $this->password);
            return $pdo;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
```

### Cómo añado productos al carrito:
```php
public function añadirAlCarrito($idProducto, $cantidad = 1) {
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = array();
    }
    
    if (isset($_SESSION['carrito'][$idProducto])) {
        $_SESSION['carrito'][$idProducto] += $cantidad;
    } else {
        $_SESSION['carrito'][$idProducto] = $cantidad;
    }
}
```

### Sistema de estrellas para valoraciones:
```javascript
function valorarProducto(puntuacion) {
    const estrellas = document.querySelectorAll('.estrella');
    
    estrellas.forEach((estrella, index) => {
        if (index < puntuacion) {
            estrella.classList.add('activa');
        } else {
            estrella.classList.remove('activa');
        }
    });
    
    // Enviar la valoración al servidor
    fetch('valorar.php', {
        method: 'POST',
        body: JSON.stringify({
            producto_id: productoId,
            puntuacion: puntuacion
        })
    });
}
```

## 🔒 Seguridad básica

He implementado algunas medidas de seguridad básicas:

```php
// Proteger contraseñas
$contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

// Verificar login
if (password_verify($contraseña_input, $contraseña_hash)) {
    // Login correcto
}

// Evitar inyección SQL
$stmt = $pdo->prepare("SELECT * FROM Usuario WHERE email = ?");
$stmt->execute([$email]);
```

## 🐛 Problemas que tuve y cómo los solucioné

1. **Error de conexión a la BD**: Al principio tenía problemas con Docker, tuve que cambiar el host de 'localhost' a 'mariadb'
2. **Sesiones que no funcionaban**: No tenía `session_start()` en todos los archivos que lo necesitaban
3. **Imágenes que no se veían**: Problemas con las rutas, las puse en una carpeta uploads/
4. **Responsive**: Tuve que usar Bootstrap para que se viera bien en móviles

## 🔧 Cosas que me gustaría mejorar

- [ ] Añadir un sistema de pagos real
- [ ] Hacer que las notificaciones sean más bonitas
- [ ] Añadir más filtros en el catálogo
- [ ] Mejorar el diseño del dashboard
- [ ] Añadir más validaciones en los formularios
- [ ] Hacer tests para comprobar que todo funciona

## 📱 Capturas del proyecto

*Aquí podrías añadir algunas capturas de pantalla de tu aplicación*

## 👨‍💻 Sobre mí

Este es mi primer proyecto grande con PHP y MySQL. He aprendido mucho haciéndolo, especialmente sobre:
- Cómo estructurar un proyecto web
- Trabajar con bases de datos
- Manejar sesiones y cookies
- Hacer páginas responsive
- Usar Docker para desarrollo

## 🎓 Conclusión

SoccerStore ha sido un proyecto muy interesante donde he podido aplicar todo lo que he aprendido durante la carrera. Aunque no es perfecto, estoy contento con el resultado y creo que demuestra que puedo desarrollar una aplicación web completa desde cero.

---

**Desarrollado como TFG - [Tu Nombre]**  
📧 [tu.email@ejemplo.com]  
🔗 [Tu perfil de LinkedIn]

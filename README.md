# ⚽ SoccerStore - E-commerce de Artículos de Fútbol

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)

**Trabajo de Fin de Grado** - Plataforma completa de e-commerce especializada en artículos de fútbol con arquitectura MVC, gestión avanzada de inventario y sistema de reseñas interactivo.

---

## 📋 Índice

- [Descripción del Proyecto](#-descripción-del-proyecto)
- [Características Principales](#-características-principales)
- [Stack Tecnológico](#-stack-tecnológico)
- [Arquitectura del Sistema](#-arquitectura-del-sistema)
- [Estructura de Archivos](#-estructura-de-archivos)
- [Base de Datos](#-base-de-datos)
- [Instalación](#-instalación)
- [Uso del Sistema](#-uso-del-sistema)
- [Ejemplos de Código](#-ejemplos-de-código)
- [Seguridad](#-seguridad)
- [Testing](#-testing)
- [Docker](#-docker)
- [Mejoras Futuras](#-mejoras-futuras)
- [Autor](#-autor)

---

## 🚀 Descripción del Proyecto

SoccerStore es una **plataforma de e-commerce moderna** desarrollada como Trabajo de Fin de Grado, especializada en la venta de artículos de fútbol. El sistema implementa un patrón **MVC (Modelo-Vista-Controlador)** robusto con **PHP** y **MySQL**, ofreciendo una experiencia completa tanto para usuarios como administradores.

### 🎯 Objetivos del TFG
- Desarrollar una aplicación web escalable y segura
- Implementar patrones de diseño profesionales (MVC, DAO)
- Crear un sistema de gestión integral de inventario
- Diseñar una interfaz responsive y moderna
- Establecer medidas de seguridad robustas

---

## ✨ Características Principales

### 👤 Para Usuarios
- **Registro y autenticación** segura con validación
- **Catálogo dinámico** con filtros avanzados (marca, precio, categoría)
- **Carrito de compras** persistente con sesiones
- **Sistema de reseñas** interactivo con puntuación por estrellas
- **Gestión de perfil** con historial de pedidos
- **Diseño responsive** optimizado para móviles

### 🔧 Para Administradores
- **Panel de control** completo y intuitivo
- **Gestión de productos** (CRUD completo)
- **Control de inventario** con alertas de stock bajo
- **Gestión de usuarios** y permisos
- **Estadísticas de ventas** y métricas de negocio
- **Moderación de reseñas** y contenido

---

## 🛠 Stack Tecnológico

### Backend
```
PHP 8.1+          - Lógica del servidor y API
MySQL 8.0         - Base de datos relacional
Apache 2.4        - Servidor web
```

### Frontend
```
HTML5             - Estructura semántica
CSS3              - Estilos y animaciones
JavaScript ES6+   - Interactividad del cliente
Bootstrap 5       - Framework responsive
```

### DevOps y Herramientas
```
Docker            - Containerización
Docker Compose    - Orquestación de servicios
Git               - Control de versiones
PHPMyAdmin        - Administración de BD
```

---

## 🏗 Arquitectura del Sistema

### Patrón MVC Implementado

```
soccerstore/
├── controllers/          # Controladores (Lógica de negocio)
│   ├── AuthController.php
│   ├── ProductController.php
│   └── AdminController.php
├── models/              # Modelos (Entidades)
│   ├── User.php
│   ├── Product.php
│   └── Review.php
├── dao/                 # Data Access Objects
│   ├── UserDAO.php
│   ├── ProductDAO.php
│   └── ReviewDAO.php
└── views/               # Vistas (Presentación)
    ├── auth/
    ├── products/
    └── admin/
```

### Flujo de Datos
```
Usuario → Controlador → DAO → Modelo → Base de Datos
                    ↓
                  Vista ← Datos procesados
```

---

## 📁 Estructura de Archivos

```
soccerstore/
├── 📂 config/
│   ├── database.php         # Configuración BD
│   └── constants.php        # Constantes globales
├── 📂 controllers/
│   ├── AuthController.php   # Autenticación
│   ├── ProductController.php # Productos
│   ├── CartController.php   # Carrito
│   └── AdminController.php  # Administración
├── 📂 dao/
│   ├── UserDAO.php         # Acceso datos usuarios
│   ├── ProductDAO.php      # Acceso datos productos
│   └── ReviewDAO.php       # Acceso datos reseñas
├── 📂 models/
│   ├── User.php           # Modelo usuario
│   ├── Product.php        # Modelo producto
│   └── Review.php         # Modelo reseña
├── 📂 views/
│   ├── 📂 auth/           # Vistas autenticación
│   ├── 📂 products/       # Vistas productos
│   ├── 📂 admin/          # Panel administrador
│   └── 📂 layouts/        # Plantillas base
├── 📂 assets/
│   ├── 📂 css/           # Estilos CSS
│   ├── 📂 js/            # Scripts JavaScript
│   └── 📂 images/        # Imágenes del sitio
├── 📂 uploads/           # Imágenes de productos
├── 📂 sql/              # Scripts base de datos
└── 📄 index.php         # Punto de entrada
```

---

## 🗄 Base de Datos

### Esquema Principal

```sql
-- Tabla de usuarios
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de productos
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    brand VARCHAR(50) NOT NULL,
    category ENUM('camisetas', 'pantalones', 'botas', 'accesorios') NOT NULL,
    stock INT DEFAULT 0,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de reseñas
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```

### Triggers Automatizados

```sql
-- Trigger para actualizar stock automáticamente
DELIMITER $$
CREATE TRIGGER update_stock_after_purchase
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE products 
    SET stock = stock - NEW.quantity 
    WHERE id = NEW.product_id;
END$$
DELIMITER ;
```

---

## 🚀 Instalación

### Prerrequisitos
- Docker y Docker Compose
- Git
- Puerto 8080 disponible

### Instalación con Docker (Recomendado)

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/soccerstore.git
cd soccerstore

# 2. Construir y levantar contenedores
docker-compose up -d --build

# 3. Importar base de datos
docker exec -i soccerstore_db mysql -u root -p soccerstore < sql/soccerstore.sql

# 4. Acceder a la aplicación
# Web: http://localhost:8080
# PHPMyAdmin: http://localhost:8081
```

### Instalación Manual

```bash
# 1. Configurar servidor web (Apache/Nginx)
# 2. Crear base de datos MySQL
CREATE DATABASE soccerstore;

# 3. Importar esquema
mysql -u root -p soccerstore < sql/soccerstore.sql

# 4. Configurar archivo de conexión
cp config/database.example.php config/database.php
# Editar credenciales de BD

# 5. Configurar permisos
chmod 755 uploads/
```

---

## 💻 Uso del Sistema

### Usuario Final
1. **Registro**: Crear cuenta con email y contraseña
2. **Explorar**: Navegar catálogo con filtros
3. **Comprar**: Añadir productos al carrito
4. **Reseñar**: Valorar productos comprados
5. **Perfil**: Gestionar datos y pedidos

### Administrador
1. **Login**: Acceso con credenciales admin
2. **Dashboard**: Ver métricas y estadísticas
3. **Productos**: Gestionar catálogo completo
4. **Usuarios**: Administrar cuentas de usuario
5. **Inventario**: Controlar stock y alertas

### Credenciales por Defecto
```
Admin:
- Usuario: admin@soccerstore.com
- Contraseña: admin123

Usuario Demo:
- Usuario: demo@soccerstore.com
- Contraseña: demo123
```

---

## 🔧 Ejemplos de Código

### Controlador de Productos (PHP)

```php
<?php
class ProductController {
    private $productDAO;
    
    public function __construct() {
        $this->productDAO = new ProductDAO();
    }
    
    public function getProducts($filters = []) {
        try {
            $products = $this->productDAO->getAllProducts($filters);
            return $this->jsonResponse($products);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    
    public function createProduct($data) {
        // Validación de datos
        $validator = new ProductValidator();
        if (!$validator->validate($data)) {
            return $this->errorResponse('Datos inválidos');
        }
        
        // Crear producto
        $product = new Product($data);
        $result = $this->productDAO->save($product);
        
        return $this->jsonResponse(['id' => $result]);
    }
}
?>
```

### DAO con Prepared Statements

```php
<?php
class ProductDAO {
    private $connection;
    
    public function getAllProducts($filters = []) {
        $query = "SELECT * FROM products WHERE 1=1";
        $params = [];
        
        if (!empty($filters['brand'])) {
            $query .= " AND brand = ?";
            $params[] = $filters['brand'];
        }
        
        if (!empty($filters['category'])) {
            $query .= " AND category = ?";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['max_price'])) {
            $query .= " AND price <= ?";
            $params[] = $filters['max_price'];
        }
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
```

### Sistema de Reseñas (JavaScript)

```javascript
class ReviewSystem {
    constructor() {
        this.initStarRating();
    }
    
    initStarRating() {
        document.querySelectorAll('.star-rating').forEach(rating => {
            const stars = rating.querySelectorAll('.star');
            
            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    this.setRating(stars, index + 1);
                });
                
                star.addEventListener('mouseover', () => {
                    this.highlightStars(stars, index + 1);
                });
            });
        });
    }
    
    setRating(stars, rating) {
        stars.forEach((star, index) => {
            star.classList.toggle('active', index < rating);
        });
        
        // Enviar rating al servidor
        this.submitRating(rating);
    }
    
    async submitRating(rating) {
        try {
            const response = await fetch('/api/reviews', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: this.productId,
                    rating: rating,
                    comment: document.querySelector('#review-comment').value
                })
            });
            
            const result = await response.json();
            this.showFeedback(result.success);
        } catch (error) {
            console.error('Error al enviar reseña:', error);
        }
    }
}
```

### CSS Grid Responsive

```css
/* Layout principal con CSS Grid */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    padding: 2rem;
}

.product-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Sistema de estrellas */
.star-rating {
    display: flex;
    gap: 0.25rem;
}

.star {
    font-size: 1.5rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s ease;
}

.star:hover,
.star.active {
    color: #ffd700;
}

/* Responsive design */
@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: 1fr;
        padding: 1rem;
    }
    
    .navbar {
        flex-direction: column;
    }
}
```

---

## 🔒 Seguridad

### Medidas Implementadas

#### Autenticación y Autorización
```php
// Hash seguro de contraseñas
$hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

// Verificación de contraseña
if (password_verify($inputPassword, $hashedPassword)) {
    // Acceso concedido
}

// Control de sesiones
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
```

#### Prevención de Inyección SQL
```php
// Prepared Statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND active = 1");
$stmt->execute([$email]);
```

#### Validación y Sanitización
```php
class InputValidator {
    public static function sanitizeString($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function validatePrice($price) {
        return is_numeric($price) && $price > 0;
    }
}
```

#### Protección CSRF
```php
// Generar token CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validar token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    throw new Exception('Token CSRF inválido');
}
```

---

## 🧪 Testing

### Estructura de Pruebas
```
tests/
├── unit/              # Pruebas unitarias
│   ├── UserTest.php
│   └── ProductTest.php
├── integration/       # Pruebas de integración
│   └── DatabaseTest.php
└── functional/        # Pruebas funcionales
    └── AuthTest.php
```

### Ejemplo de Test Unitario
```php
<?php
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase {
    public function testProductCreation() {
        $productData = [
            'name' => 'Nike Air Max',
            'price' => 129.99,
            'brand' => 'Nike'
        ];
        
        $product = new Product($productData);
        
        $this->assertEquals('Nike Air Max', $product->getName());
        $this->assertEquals(129.99, $product->getPrice());
        $this->assertEquals('Nike', $product->getBrand());
    }
    
    public function testPriceValidation() {
        $this->expectException(InvalidArgumentException::class);
        
        $product = new Product([
            'name' => 'Test',
            'price' => -10,  // Precio inválido
            'brand' => 'Test'
        ]);
    }
}
```

### Ejecutar Tests
```bash
# Instalar PHPUnit
composer require --dev phpunit/phpunit

# Ejecutar todas las pruebas
./vendor/bin/phpunit tests/

# Ejecutar pruebas específicas
./vendor/bin/phpunit tests/unit/ProductTest.php
```

---

## 🐳 Docker

### docker-compose.yml
```yaml
version: '3.8'

services:
  web:
    build: .
    container_name: soccerstore_web
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_NAME=soccerstore
      - DB_USER=root
      - DB_PASS=rootpassword

  db:
    image: mysql:8.0
    container_name: soccerstore_db
    ports:
      - "3306:3306"
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: soccerstore
    volumes:
      - db_data:/var/lib/mysql
      - ./sql:/docker-entrypoint-initdb.d

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: soccerstore_pma
    ports:
      - "8081:80"
    environment:
      PMA_HOST: db
      MYSQL_ROOT_PASSWORD: rootpassword
    depends_on:
      - db

volumes:
  db_data:
```

### Dockerfile
```dockerfile
FROM php:8.1-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80
```

### Comandos Útiles
```bash
# Construir imagen
docker-compose build

# Levantar servicios
docker-compose up -d

# Ver logs
docker-compose logs web

# Ejecutar comandos en contenedor
docker exec -it soccerstore_web bash

# Backup de base de datos
docker exec soccerstore_db mysqldump -u root -p soccerstore > backup.sql

# Parar servicios
docker-compose down
```

---

## 📊 Métricas y Estadísticas

### Métricas de Desarrollo
- **Líneas de código**: ~5,000 líneas
- **Archivos PHP**: 25+
- **Tablas de BD**: 8 principales
- **Endpoints API**: 15+
- **Tiempo desarrollo**: 4 meses

### Funcionalidades Implementadas
- ✅ Sistema de autenticación completo
- ✅ CRUD de productos con imágenes
- ✅ Carrito de compras persistente
- ✅ Sistema de reseñas interactivo
- ✅ Panel de administración
- ✅ Diseño responsive
- ✅ Validación de formularios
- ✅ Medidas de seguridad
- ✅ Dockerización completa

---

## 🚀 Mejoras Futuras

### Funcionalidades Planificadas
- [ ] **Sistema de pagos** con PayPal/Stripe
- [ ] **Notificaciones push** para ofertas
- [ ] **Chat en vivo** para soporte
- [ ] **Recomendaciones IA** personalizadas
- [ ] **API REST** completa para móviles
- [ ] **Sistema de cupones** y descuentos
- [ ] **Multidioma** (ES/EN/FR)
- [ ] **Analytics avanzados** con gráficos

### Optimizaciones Técnicas
- [ ] **Cache Redis** para mejor rendimiento
- [ ] **CDN** para imágenes
- [ ] **Lazy loading** de productos
- [ ] **PWA** (Progressive Web App)
- [ ] **Tests automatizados** con CI/CD
- [ ] **Monitoreo** con logs estructurados

---

## 📈 Conclusiones del TFG

### Objetivos Cumplidos
✅ **Aplicación funcional completa** con todas las características planificadas  
✅ **Arquitectura MVC sólida** siguiendo buenas prácticas de desarrollo  
✅ **Interfaz moderna y responsive** con excelente experiencia de usuario  
✅ **Seguridad robusta** con validación y protección contra vulnerabilidades  
✅ **Base de datos optimizada** con relaciones bien definidas y triggers automáticos  
✅ **Documentación completa** del proyecto y código  

### Aprendizajes Clave
- **Patrones de diseño** aplicados en entornos reales
- **Desarrollo full-stack** con tecnologías modernas
- **Gestión de proyectos** y metodologías ágiles
- **Optimización de rendimiento** y escalabilidad
- **Implementación de seguridad** en aplicaciones web
- **DevOps básico** con Docker y containerización

### Valor Profesional
Este proyecto demuestra competencias técnicas sólidas en desarrollo web moderno, desde la planificación y diseño hasta la implementación y despliegue, preparando para el mercado laboral en desarrollo de software especializándose en e-commerce de artículos deportivos.

---

## 👨‍💻 Autor

**[Tu Nombre]**  
📧 Email: [tu.email@ejemplo.com]  
🔗 LinkedIn: [tu-perfil-linkedin]  
🐙 GitHub: [tu-usuario-github]  

---

### 📄 Licencia

Este proyecto fue desarrollado como Trabajo de Fin de Grado para [Universidad/Instituto].  
Código disponible bajo licencia MIT para fines educativos.

---

<div align="center">

### 🌟 ¡Gracias por revisar SoccerStore! 

**Desarrollado con ❤️ para el TFG**

[⬆️ Volver arriba](#-soccerstore---e-commerce-de-artículos-de-fútbol)

</div>

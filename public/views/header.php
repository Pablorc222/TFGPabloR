<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Contar productos en carrito
$itemsCarrito = 0;
if (isset($_SESSION['carrito'])) {
    $itemsCarrito = array_sum($_SESSION['carrito']);
}

// Contar favoritos (solo para usuarios no admin)
$itemsListaDeseos = 0;
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    include_once("models/listadeseos.php");
    $ldDAO = new ListaDeseosDAO();
    $listaDeseos = $ldDAO->getProductosListaDeseos(2);
    $itemsListaDeseos = count($listaDeseos);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoccerStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=REM:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />    <style>
        body {
            margin: 0;
            font-family: "REM", sans-serif;
            background: url('utils/fondo2.jpg') no-repeat center center fixed;
            background-size: cover;
            padding-bottom: 1px;
        }

        header {
            background-color: rgba(5, 5, 23, 0.9);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            box-sizing: border-box;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.6);
            z-index: 1000;
        }

        header h1 {
            margin: 0;
            color: #fff;
            font-size: 2em;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        header h1:hover {
            transform: scale(1.1);
        }

        header h1 i {
            color: #F5A623;
            font-weight: 900 !important;
            font-style: normal !important;
        }

        nav {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease, transform 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        nav a:hover {
            color: #F5A623;
            transform: scale(1.1);
        }

        .carrito-icono, .deseos-icono {
            position: relative;
        }

        .cantidad-carrito, .cantidad-deseos {
            background: #F5A623;
            color: white;
            font-weight: bold;
            border-radius: 50%;
            padding: 5px;
            position: absolute;
            top: -10px;
            right: -10px;
            font-size: 0.9em;
        }

        span {
            color: #F5A623;
            font-weight: bold;
        }

        .nav-icon {
            margin-right: 5px;
            font-size: 16px;
        }

        .content-spacer {
            height: 80px;
        }
        /* Forzar el mismo estilo de balón en todas las páginas */
.fas.fa-futbol {
    font-weight: 900 !important;
    font-family: "Font Awesome 6 Free" !important;
    font-style: normal !important;
}

/* Asegurar que el icono del header sea consistente */
header h1 i.fa-futbol {
    font-weight: 900 !important;
    font-family: "Font Awesome 6 Free" !important;
    font-variation-settings: "wght" 900 !important;
}
    </style>
</head>
<body>
<header>
    <a href="index.php">
        <h1><i class="fas fa-futbol"></i> SoccerStore</h1>
    </a>
    <nav>
        <?php if (isset($_SESSION['usuario'])): ?>
            <span>
                <i class="fas fa-user"></i> Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?>
            </span>

            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>
                <!-- Menú para administradores -->
                <a href="index.php?controller=DashboardController&action=mostrarDashboard">
                    <i class="fas fa-chart-bar nav-icon"></i> Dashboard
                </a>
                <a href="index.php?controller=UsuController&action=listarPedidos">
                    <i class="fas fa-box nav-icon"></i> Listar Pedidos
                </a>
                <a href="index.php?controller=ProductController&action=insertProduct">
                    <i class="fas fa-plus-circle nav-icon"></i> Crear
                </a>
            <?php else: ?>
                <!-- Menú para usuarios normales -->
                <a href="index.php?controller=ListaDeseosController&action=mostrarListaDeseos" class="deseos-icono">
                    <i class="fas fa-heart nav-icon"></i> Lista de Deseos
                    <?php if ($itemsListaDeseos > 0): ?>
                        <span class="cantidad-deseos">
                            <?php echo $itemsListaDeseos; ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <a href="index.php?controller=ProductController&action=verCarrito" class="carrito-icono">
                <i class="fas fa-shopping-cart nav-icon"></i> Carrito
                <?php if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                    <span class="cantidad-carrito">
                        <?php echo $itemsCarrito; ?>
                    </span>
                <?php endif; ?>
            </a>

            <a href="index.php?controller=UsuController&action=cerrarsesion">
                <i class="fas fa-sign-out-alt nav-icon"></i> Cerrar Sesión
            </a>

        <?php else: ?>
            <!-- Menú para usuarios no logueados -->
            <a href="index.php?controller=ListaDeseosController&action=mostrarListaDeseos" class="deseos-icono">
                <i class="fas fa-heart nav-icon"></i> Lista de Deseos
                <?php if ($itemsListaDeseos > 0): ?>
                    <span class="cantidad-deseos">
                        <?php echo $itemsListaDeseos; ?>
                    </span>
                <?php endif; ?>
            </a>
            
            <a href="index.php?controller=ProductController&action=verCarrito" class="carrito-icono">
                <i class="fas fa-shopping-cart nav-icon"></i> Carrito
                <?php if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                    <span class="cantidad-carrito">
                        <?php echo $itemsCarrito; ?>
                    </span>
                <?php endif; ?>
            </a>
            
            <a href="index.php?controller=UsuController&action=showiniciosesion">
                <i class="fas fa-key nav-icon"></i> Login
            </a>
        <?php endif; ?>
    </nav>
</header>

<div class="content-spacer"></div>
<?php
// Determinar la ruta base para recursos
$basePath = '';
if (!file_exists('utils/facebook.svg') && file_exists('../utils/facebook.svg')) {
    $basePath = '../';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FootStore</title>
    <style>
        /* Estilos del footer */
        footer {
            background-color: #050517;
            color: #fff;
            text-align: center;
            padding: 5px;
            font-size: 12px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        footer a {
            color: #fff;
            text-decoration: none;
            margin: 0 5px;
        }

        footer img {
            width: 3%;
        }
    </style>
</head>
<body>
    <!-- Contenido del footer -->
    <footer>
        <div>
            <p>© 2024 FootStore. Todos los derechos reservados.</p>
        </div>
        <div>
            <a href="https://www.facebook.com/"><img src="<?php echo $basePath; ?>utils/facebook.svg" alt="facebook"></a>
            <a href="https://twitter.com/"><img src="<?php echo $basePath; ?>utils/twitter.svg" alt="twitter"></a>
            <a href="https://www.instagram.com/"><img src="<?php echo $basePath; ?>utils/instagram.svg" alt="instagram"></a>
        </div>
    </footer>
</body>
</html>
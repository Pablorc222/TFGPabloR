<?php
// Clase View simplificada
if (!class_exists('View')) {
    class View {
        public static function show($viewName, $data=null) {
            // Obtener la ruta base
            $basePath = '';
            
            // Verificar posibles ubicaciones para el header
            $headerPaths = ['header.php', 'views/header.php', '../header.php', '../views/header.php'];
            foreach ($headerPaths as $path) {
                if (file_exists($path)) {
                    include_once($path);
                    break;
                }
            }
            
            // Incluir la vista
            if (file_exists($viewName . '.php')) {
                include($viewName . '.php');
            } elseif (file_exists('views/' . $viewName . '.php')) {
                include('views/' . $viewName . '.php');
            } elseif (file_exists('../views/' . $viewName . '.php')) {
                include('../views/' . $viewName . '.php');
            } else {
                echo "No se encontró la vista: $viewName";
            }
            
            // Verificar posibles ubicaciones para el footer
            $footerPaths = ['footer.php', 'views/footer.php', '../footer.php', '../views/footer.php'];
            foreach ($footerPaths as $path) {
                if (file_exists($path)) {
                    include_once($path);
                    break;
                }
            }
        }
    }
}
?>
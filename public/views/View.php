<?php
class View {
    public static function show($nombreVista, $data = null) {
        // Incluir header común
        include_once 'views/header.php';
        
        // Incluir la vista específica
        $archivoVista = "views/$nombreVista.php";
        if (file_exists($archivoVista)) {
            include $archivoVista;
        } else {
            echo "<div class='error'>Vista no encontrada: $nombreVista</div>";
        }
        
        // Incluir footer común
        include_once 'views/footer.php';
    }
}
?>
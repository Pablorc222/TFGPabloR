<?php
echo '
<div style="margin-top: 75px; margin-bottom: 150px;">
    <div style="font-family: Arial, sans-serif; display: flex; flex-direction: column; align-items: center; padding: 20px;">
        <h1 style="margin-bottom: 20px;">Detalles del Producto</h1>
    </div>';

echo '<div style="display: flex; flex-wrap: wrap; justify-content: center;">';

// Separar las URLs de las imágenes
$imagenes = explode('|', $data['producto']["Imagen"]);
$imagenDelante = !empty($imagenes[0]) ? $imagenes[0] : 'utils/placeholder.png';
$imagenDetras = !empty($imagenes[1]) ? $imagenes[1] : 'utils/placeholder.png';

echo '
    <div style="width: calc(75% - 20px); background-color: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); overflow: hidden; display: flex; flex-direction: column; align-items: center; padding: 20px;">
        
        <div style="display: flex; gap: 20px; margin-bottom: 20px; justify-content: center;">
            <div style="text-align: center;">
                <h4 style="margin: 0 0 10px 0; color: #555;">Vista Frontal</h4>
                <img src="' . htmlspecialchars($imagenDelante) . '" alt="' . htmlspecialchars($data['producto']['Nombre_Producto']) . '" 
                     style="width: 300px; height: 200px; border-radius: 10px; object-fit: contain; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                     onerror="this.onerror=null; this.src=\'utils/placeholder.png\';">
            </div>
            <div style="text-align: center;">
                <h4 style="margin: 0 0 10px 0; color: #555;">Vista Trasera</h4>
                <img src="' . htmlspecialchars($imagenDetras) . '" alt="' . htmlspecialchars($data['producto']['Nombre_Producto']) . '" 
                     style="width: 300px; height: 200px; border-radius: 10px; object-fit: contain; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                     onerror="this.onerror=null; this.src=\'utils/placeholder.png\';">
            </div>
        </div>
        
        <div style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
            <div style="flex-grow: 1; display: flex; flex-direction: row; align-items: center; gap: 20px">
                <h3 style="margin-top: 20px; font-size: 26px;">' . htmlspecialchars($data['producto']['Nombre_Producto']) . '</h3>
                <form action="index.php" method="GET" style="display: flex; align-items: center;">
                    <input type="hidden" name="controller" value="ProductController">
                    <input type="hidden" name="action" value="addCarrito">
                    <input type="hidden" name="id" value="' . htmlspecialchars($data['producto']['ID_Producto']) . '">
                    <input type="number" name="cantidad" value="1" min="1" style="width: 40px; margin-right: 10px;">
                    <button type="submit" style="text-decoration: none; color: inherit; padding: 10px 20px; background-color: #98D9C2; border: none; border-radius: 5px; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 10px">
                        <p style="font-size: 16px; margin: 0;">' . htmlspecialchars($data['producto']['Precio']) . ' €</p>
                        <img style="width: 20px;" src="../utils/cart.svg">
                    </button>
                </form>';

// Botón de lista de deseos - SOLO PARA USUARIOS NO ADMIN
if (isset($_SESSION['usuario_id']) && (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin')) {
    include_once("models/listadeseos.php");
    $ldDAO = new ListaDeseosDAO();
    $enListaDeseos = $ldDAO->isProductoEnListaDeseos($_SESSION['usuario_id'], $data['producto']['ID_Producto']);
    
    if ($enListaDeseos) {
        echo '<a href="index.php?controller=ListaDeseosController&action=eliminarProducto&producto_id=' . htmlspecialchars($data['producto']['ID_Producto']) . '" style="margin-left: 10px; padding: 10px 20px; background-color: #fff; border: 1px solid #f8d7da; color: #e74c3c; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center; gap: 5px; transition: all 0.3s;">
            <i class="fas fa-heart" style="color: #e74c3c; font-size: 18px;"></i>
            <span>En mi lista</span>
        </a>';
    } else {
        echo '<a href="index.php?controller=ListaDeseosController&action=agregarProducto&producto_id=' . htmlspecialchars($data['producto']['ID_Producto']) . '" style="margin-left: 10px; padding: 10px 20px; background-color: #fff; border: 1px solid #ddd; color: #333; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center; gap: 5px; transition: all 0.3s;">
            <i class="far fa-heart" style="color: #999; font-size: 18px;"></i>
            <span>Añadir a favoritos</span>
        </a>';
    }
} elseif (!isset($_SESSION['usuario_id'])) {
    echo '<a href="index.php?controller=UsuController&action=showiniciosesion" style="margin-left: 10px; padding: 10px 20px; background-color: #fff; border: 1px solid #ddd; color: #333; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center; gap: 5px; transition: all 0.3s;">
        <i class="far fa-heart" style="color: #999; font-size: 18px;"></i>
        <span>Añadir a favoritos</span>
    </a>';
}

echo '
            </div>  
            <p style="margin-top: 10px; font-size: 16px;">' . htmlspecialchars($data['producto']['Descripcion']) . '</p>
            <a href="index.php" style="margin-top: 20px; padding: 10px 20px; background-color: #eee; color: #333; text-decoration: none; border-radius: 5px;">Volver a productos</a>
        </div>
    </div>';

echo '</div>';

// Valoraciones simplificadas
include_once("models/valoraciones.php");
$vDAO = new ValoracionesDAO();
$valoraciones = $vDAO->getValoracionesByProductoId($data['producto']['ID_Producto']);

$promedio = 0;
$total = count($valoraciones);
if ($total > 0) {
    $suma = array_sum(array_column($valoraciones, 'puntuacion'));
    $promedio = $suma / $total;
}

echo '
<div style="margin-top: 30px; padding: 20px; background-color: #f8f8f8; border-radius: 10px;">
    <h3 style="margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Valoraciones</h3>
    
    <div style="text-align: center; margin-bottom: 15px;">
        <span style="font-size: 28px; font-weight: bold;">' . number_format($promedio, 1) . '</span>
        <div style="margin: 5px 0; color: #FFD700;">';

for ($i = 1; $i <= 5; $i++) {
    echo ($i <= round($promedio)) ? '<i class="fas fa-star"></i> ' : '<i class="far fa-star"></i> ';
}

echo '  </div>
        <div style="font-size: 12px; color: #666;">' . $total . ' valoración(es)</div>
    </div>';

if (!empty($valoraciones)) {
    echo '<div>';
    foreach ($valoraciones as $val) {
        echo '
        <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <div style="font-weight: bold;"><i class="fas fa-user-circle"></i> ' . htmlspecialchars($val['username']) . '</div>
                <div style="font-size: 12px; color: #777;">' . date('d/m/Y', strtotime($val['fecha_creacion'])) . '</div>
            </div>
            <div style="margin-bottom: 8px; color: #FFD700;">';
            
        for ($i = 1; $i <= 5; $i++) {
            echo ($i <= $val['puntuacion']) ? '<i class="fas fa-star"></i> ' : '<i class="far fa-star"></i> ';
        }
        
        echo '</div>
            <div style="color: #333;">' . nl2br(htmlspecialchars($val['comentario'])) . '</div>
        </div>';
    }
    echo '</div>';
} else {
    echo '<p style="text-align: center; color: #666;">Aún no hay valoraciones para este producto.</p>';
}

echo '</div></div>';
?>
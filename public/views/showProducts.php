<?php
echo '
<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: "REM", Arial, sans-serif;
        overflow-y: auto;
    }
    body {
        background-color: #050517;
        display: flex;
        flex-direction: column;
        align-items: center;
        background-image: url("utils/fondo2.jpg");
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }
    .container {
        width: 100%;
        max-width: 1200px;
        padding: 20px;
        padding-top: 80px;
        padding-bottom: 80px;
        margin-top: 20px;
    }
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        width: 100%;
    }
    .product-card {
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 10px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 15px;
        text-align: center;
        min-height: 280px;
        justify-content: space-between;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .product-card .img-container {
        width: 100%;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        background-color: #f9f9f9;
        border-radius: 5px;
        overflow: hidden;
    }
    .product-card img {
        width: 90%;
        height: 130px;
        object-fit: contain;
        object-position: center;
        transition: transform 0.3s;
    }
    .product-card:hover img {
        transform: scale(1.05);
    }
    .product-card h3 {
        font-size: 15px;
        color: #333;
        margin: 0 0 8px 0;
        font-weight: 600;
        height: 40px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .product-card p {
        font-size: 17px;
        font-weight: bold;
        color: #437F97;
        margin: 0 0 12px 0;
    }
    .actions {
        display: flex;
        gap: 8px;
        width: 100%;
        justify-content: center;
        align-items: center;
        margin-top: 8px;
    }
    .actions a, .actions button {
        padding: 8px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .view {
        background-color: #98D9C2;
    }
    .view:hover {
        background-color: #7bc0a9;
    }
    .cart {
        background-color: #98D9C2;
    }
    .cart:hover {
        background-color: #7bc0a9;
    }
    .delete {
        background-color: #F08080;
    }
    .delete:hover {
        background-color: #e06060;
    }
    .wishlist {
        background-color: #fff;
        border: 1px solid #eee;
        color: #e74c3c;
    }
    .wishlist:hover {
        background-color: #fff5f5;
    }
    .wishlist-added {
        background-color: #ffe6e6;
        border: 1px solid #ff9999;
        color: #cc0000;
    }
    .input-quantity {
        width: 35px;
        height: 35px;
        padding: 5px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }
    footer {
        width: 100%;
        background-color: rgba(5, 5, 23, 0.9);
        color: white;
        text-align: center;
        padding: 15px 0;
        margin-top: auto;
    }
    html {
        overflow-y: scroll;
    }
    body {
        min-height: 120vh;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<div class="container">
    <div class="grid-container">';

foreach ($data as $article) {
    // Obtener imagen del producto (usar URLs de la base de datos)
    $imagen = "utils/placeholder.png"; // Imagen por defecto
    
    if (!empty($article["Imagen"])) {
        // Si el campo contiene URLs separadas por |, tomar solo la primera (frontal)
        if (strpos($article["Imagen"], '|') !== false) {
            $imagenes = explode('|', $article["Imagen"]);
            $imagen = !empty($imagenes[0]) ? trim($imagenes[0]) : "utils/placeholder.png";
        } else {
            // Si es una sola URL, usarla directamente
            $imagen = $article["Imagen"];
        }
    }
    
    echo '
        <div class="product-card">
            <div class="img-container">
                <img src="' . htmlspecialchars($imagen) . '" alt="' . htmlspecialchars($article["Nombre_Producto"]) . '">
            </div>
            <h3>' . htmlspecialchars($article["Nombre_Producto"]) . '</h3>
            <p>' . htmlspecialchars($article["Precio"]) . '€</p>
            <form action="index.php" method="GET" style="margin-top: auto; width: 100%;" id="form_' . htmlspecialchars($article["ID_Producto"]) . '">
                <div class="actions">
                    <a href="index.php?controller=ProductController&action=verDetallesProducto&idProducto=' . htmlspecialchars($article["ID_Producto"]) . '" class="view" title="Ver detalles">
                        <img style="width: 20px;" src="utils/eye.svg"/>
                    </a>';
    
    // Solo mostrar botón de lista de deseos para usuarios NO admin
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
        // Verificar si ya está en la lista (usando usuario_id de la sesión o ID fijo)
        $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 2;
        
        include_once("models/listadeseos.php");
        $ldDAO = new ListaDeseosDAO();
        $enListaDeseos = $ldDAO->isProductoEnListaDeseos($usuario_id, $article["ID_Producto"]);
        
        if ($enListaDeseos) {
            // Ya está en la lista - mostrar corazón lleno y enlace para eliminar
            echo '
                <a href="index.php?controller=ListaDeseosController&action=eliminarProducto&producto_id=' . htmlspecialchars($article["ID_Producto"]) . '&redirect=home" class="wishlist wishlist-added" title="Eliminar de lista de deseos">
                    <i class="fas fa-heart" style="font-size: 18px;"></i>
                </a>';
        } else {
            // No está en la lista - mostrar corazón vacío y enlace para agregar
            echo '
                <a href="index.php?controller=ListaDeseosController&action=agregarProducto&producto_id=' . htmlspecialchars($article["ID_Producto"]) . '&redirect=home" class="wishlist" title="Añadir a lista de deseos">
                    <i class="far fa-heart" style="font-size: 18px;"></i>
                </a>';
        }
    }
    
    echo '
                    <input type="hidden" name="controller" value="ProductController">
                    <input type="hidden" name="action" value="addCarrito">
                    <input type="hidden" name="id" value="' . htmlspecialchars($article["ID_Producto"]) . '">
                    <input type="number" name="cantidad" value="1" min="1" class="input-quantity">

                    <button type="submit" class="cart" title="Añadir al carrito">
                        <img style="width: 22px;" src="utils/cart.svg"/>
                    </button>';
    
    // Solo mostrar botón de eliminar para administradores
    if (isset($_SESSION["rol"]) && $_SESSION["rol"] === "admin") {
        echo '
                    <a href="index.php?controller=ProductController&action=eliminarProducto&id=' . htmlspecialchars($article["ID_Producto"]) . '" class="delete" title="Eliminar producto" onclick="return confirm(\'¿Estás seguro de que deseas eliminar este producto?\');">
                        <img style="width: 20px;" src="utils/trash.svg"/>
                    </a>';
    }
    
    echo '
                </div>
            </form>
        </div>';
}

echo '</div></div>';

// Si hay un mensaje en la sesión, mostrar un script para mostrarlo como alerta
if (isset($_SESSION['mensaje'])) {
    echo '<script>
    alert("' . htmlspecialchars($_SESSION['mensaje']) . '");
    </script>';
    unset($_SESSION['mensaje']);
}
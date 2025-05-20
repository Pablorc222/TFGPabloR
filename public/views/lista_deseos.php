<?php
// Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener datos de la lista de deseos
$listaDeseos = isset($data['listaDeseos']) ? $data['listaDeseos'] : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Deseos - FootStore</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; display: flex; flex-direction: column; align-items: center; padding: 60px;">
        <h1 style="margin-bottom: 40px;">Lista de Deseos</h1>
        
        <!-- Contenido de la lista de deseos -->
        <div style="width: 50%; background-color: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); padding: 20px; margin-bottom: 20px; text-align: center;">
            <?php if (empty($listaDeseos)): ?>
                <div style="text-align: center; padding: 20px;">
                    <p>Tu lista de deseos está vacía</p>
                    <a href="index.php?controller=ProductController&action=getAllProducts" style="display: inline-block; margin-top: 15px; padding: 8px 15px; background-color: #437F97; color: white; text-decoration: none; border-radius: 5px;">Explorar productos</a>
                </div>
            <?php else: ?>
                <?php 
                foreach ($listaDeseos as $producto): 
                ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee; text-align: left;">
                        <div>
                            <h3 style="margin: 0;"><?php echo $producto['Nombre_Producto']; ?></h3>
                            <p style="margin: 5px 0;">Precio: <?php echo $producto['Precio']; ?>€</p>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <a href="index.php?controller=ProductController&action=verDetallesProducto&idProducto=<?php echo $producto['ID_Producto']; ?>" style="text-decoration: none; color: inherit; padding: 10px; background-color: #437F97; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                Ver
                            </a>
                            <a href="index.php?controller=ProductController&action=addCarrito&id=<?php echo $producto['ID_Producto']; ?>&cantidad=1" style="text-decoration: none; color: inherit; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                Añadir al carrito
                            </a>
                            <a href="index.php?controller=ListaDeseosController&action=eliminarProducto&producto_id=<?php echo $producto['ID_Producto']; ?>&redirect=lista" style="text-decoration: none; color: inherit; padding: 10px; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                Eliminar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Botones de acción -->
                <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                    <a href="index.php?controller=ProductController&action=getAllProducts" style="text-decoration: none;">
                        <button type="button" style="padding: 10px 20px; background-color: #98D9C2; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            <span style="font-size: 16px;">Seguir comprando</span>
                        </button>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
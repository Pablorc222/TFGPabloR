<div style="font-family: Arial, sans-serif; display: flex; flex-direction: column; align-items: center; padding: 60px; margin-top: 75px; margin-bottom: 150px;">
    <h1 style="margin-bottom: 40px; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Carrito de Compra</h1>
    
    <div style="width: 50%; background-color: rgba(255, 255, 255, 0.95); border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); padding: 20px; margin-bottom: 20px;">
        <?php if (empty($data)): ?>
            <div style="text-align: center; padding: 20px;">
                <p>No hay productos en el carrito</p>
                <a href="index.php?controller=ProductController&action=getAllProducts" style="display: inline-block; margin-top: 15px; padding: 8px 15px; background-color: #437F97; color: white; text-decoration: none; border-radius: 5px;">Seguir comprando</a>
            </div>
        <?php else: ?>
            <?php
            $total = 0;
            foreach ($data as $producto):
                $subtotal = $producto['Precio'] * $producto['cantidad'];
                $total += $subtotal;
            ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                    <div>
                        <h3 style="margin: 0;"><?php echo htmlspecialchars($producto['Nombre_Producto']); ?></h3>
                        <p style="margin: 5px 0;">Precio: <?php echo htmlspecialchars($producto['Precio']); ?>€ | Cantidad: <?php echo htmlspecialchars($producto['cantidad']); ?></p>
                        <p style="margin: 0; font-weight: bold;">Subtotal: <?php echo number_format($subtotal, 2); ?>€</p>
                    </div>
                    <a href="index.php?controller=ProductController&action=eliminarDelCarrito&id=<?php echo htmlspecialchars($producto['ID_Producto']); ?>" 
                       style="text-decoration: none; color: inherit; padding: 10px; background-color: #98D9C2; border: none; border-radius: 5px; cursor: pointer; width: 40px; height: 40px; display: flex; justify-content: center; align-items: center;">
                        <img style="width: 75%;" src="utils/trash.svg" alt="Eliminar">
                    </a>
                </div>
            <?php endforeach; ?>
            
            <!-- Total del carrito -->
            <div style="margin-top: 20px; padding-top: 15px; border-top: 2px solid #437F97; text-align: right;">
                <h3>Total: <?php echo number_format($total, 2); ?>€</h3>
            </div>
            
            <!-- Botones de acción -->
            <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                <a href="index.php?controller=ProductController&action=getAllProducts" style="text-decoration: none;">
                    <button type="button" style="padding: 10px 20px; background-color: #98D9C2; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        <span style="font-size: 16px;">Seguir comprando</span>
                    </button>
                </a>
                
                <a href="index.php?controller=ProductController&action=confirmarPedido" style="text-decoration: none;">
                    <button type="button" style="padding: 10px 20px; background-color: #437F97; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        <span style="font-size: 16px;">Confirmar Pedido</span>
                    </button>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
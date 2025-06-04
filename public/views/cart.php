<div style="font-family: Arial, sans-serif; display: flex; flex-direction: column; align-items: center; padding: 60px; margin-top: 75px; margin-bottom: 150px;">
    <h1 style="margin-bottom: 40px; color: #333; font-size: 1.8rem; text-align: center;">Carrito de Compra</h1>
    
    <div style="width: 60%; max-width: 800px; background-color: rgba(255, 255, 255, 0.95); border-radius: 15px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); padding: 30px;">
        <?php if (empty($data)): ?>
            <div style="text-align: center; padding: 20px;">
                <p style="color: #333; font-size: 1rem; margin-bottom: 20px;">Tu carrito está vacío</p>
                <a href="index.php?controller=ProductController&action=getAllProducts" 
                   style="display: inline-block; padding: 10px 20px; background-color: #437F97; color: white; text-decoration: none; border-radius: 8px; font-size: 0.9rem; transition: background-color 0.3s;">
                    Explorar productos
                </a>
            </div>
        <?php else: ?>
            <?php
            $total = 0;
            foreach ($data as $producto):
                $subtotal = $producto['Precio'] * $producto['cantidad'];
                $total += $subtotal;
            ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                    <div>
                        <h3 style="margin: 0; color: #333; font-size: 1.1rem;"><?php echo htmlspecialchars($producto['Nombre_Producto']); ?></h3>
                        <p style="margin: 5px 0; color: #666; font-size: 0.9rem;">Precio: <?php echo htmlspecialchars($producto['Precio']); ?>€ | Cantidad: <?php echo htmlspecialchars($producto['cantidad']); ?></p>
                        <p style="margin: 0; font-weight: bold; color: #333; font-size: 0.95rem;">Subtotal: <?php echo number_format($subtotal, 2); ?>€</p>
                    </div>
                    <a href="index.php?controller=ProductController&action=eliminarDelCarrito&id=<?php echo htmlspecialchars($producto['ID_Producto']); ?>" 
                       style="text-decoration: none; color: inherit; padding: 12px; background-color: #98D9C2; border: none; border-radius: 8px; cursor: pointer; width: 45px; height: 45px; display: flex; justify-content: center; align-items: center; transition: background-color 0.3s;">
                        <img style="width: 75%;" src="utils/trash.svg" alt="Eliminar">
                    </a>
                </div>
            <?php endforeach; ?>
            
            <!-- Total del carrito -->
            <div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid #437F97; text-align: right;">
                <h3 style="color: #333; font-size: 1.2rem;">Total: <?php echo number_format($total, 2); ?>€</h3>
            </div>
            
            <!-- Botones de acción -->
            <div style="display: flex; justify-content: space-between; margin-top: 25px; gap: 15px;">
                <a href="index.php?controller=ProductController&action=getAllProducts" style="text-decoration: none; flex: 1;">
                    <button type="button" style="width: 100%; padding: 10px 15px; background-color: #98D9C2; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: background-color 0.3s;">
                        Seguir comprando
                    </button>
                </a>
                
                <a href="index.php?controller=ProductController&action=confirmarPedido" style="text-decoration: none; flex: 1;">
                    <button type="button" style="width: 100%; padding: 10px 15px; background-color: #437F97; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: background-color 0.3s;">
                        Confirmar Pedido
                    </button>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
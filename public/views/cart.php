<?php
// VALIDACIÓN DE STOCK AL INICIO
$hay_errores_stock = false;
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    require_once 'models/productos.php';
    $productoDAO = new ProductoDAO();
    $errores_stock = [];
    
    foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
        $verificacion = $productoDAO->verificarStock($producto_id, $cantidad);
        if (!$verificacion['disponible']) {
            $errores_stock[] = $verificacion['mensaje'];
            $hay_errores_stock = true;
        }
    }
    
    if (!empty($errores_stock)) {
        $_SESSION['errores_stock'] = $errores_stock;
    }
}
?>

<style>
    body {
        overflow-x: hidden;
    }
    .carrito-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 60px 20px 20px 20px;
        margin-top: 75px;
        margin-bottom: 150px;
    }
</style>

<div class="carrito-container" style="font-family: Arial, sans-serif;">
    <h1 style="margin-bottom: 40px; color: #333; font-size: 1.8rem; text-align: center;">Carrito de Compra</h1>
    
    <?php if (isset($_SESSION['errores_stock'])): ?>
        <div style="background-color: #ffebee; border: 2px solid #f44336; padding: 20px; margin: 20px auto; border-radius: 10px; width: 60%; max-width: 800px;">
            <h3 style="color: #c62828; margin: 0 0 15px 0; text-align: center;">⚠️ Problemas de Stock</h3>
            <?php foreach ($_SESSION['errores_stock'] as $error): ?>
                <p style="color: #c62828; margin: 8px 0; font-size: 1rem; text-align: center;"><?php echo $error; ?></p>
            <?php endforeach; ?>
            <p style="color: #666; margin: 15px 0 0 0; text-align: center; font-size: 0.9rem;">
                <strong>Ajusta las cantidades para poder continuar con la compra.</strong>
            </p>
        </div>
        <?php unset($_SESSION['errores_stock']); ?>
    <?php endif; ?>
    
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
                    <div style="flex: 1;">
                        <h3 style="margin: 0; color: #333; font-size: 1.1rem;"><?php echo htmlspecialchars($producto['Nombre_Producto']); ?></h3>
                        <p style="margin: 5px 0; color: #666; font-size: 0.9rem;">Precio: <?php echo htmlspecialchars($producto['Precio']); ?>€</p>
                        <p style="margin: 0; font-weight: bold; color: #333; font-size: 0.95rem;">Subtotal: <?php echo number_format($subtotal, 2); ?>€</p>
                    </div>
                    
                    <!-- Controles de cantidad -->
                    <div style="display: flex; align-items: center; gap: 10px; margin: 0 15px;">
                        <!-- Botón disminuir -->
                        <a href="index.php?controller=ProductController&action=actualizarCantidad&id=<?php echo htmlspecialchars($producto['ID_Producto']); ?>&operacion=disminuir" 
                           style="text-decoration: none; width: 30px; height: 30px; background-color: <?php echo ($producto['cantidad'] <= 1) ? '#ccc' : '#f44336'; ?>; color: white; border: none; border-radius: 5px; cursor: <?php echo ($producto['cantidad'] <= 1) ? 'not-allowed' : 'pointer'; ?>; font-size: 16px; display: flex; align-items: center; justify-content: center; font-weight: bold;"
                           <?php echo ($producto['cantidad'] <= 1) ? 'onclick="return false;"' : ''; ?>>
                            -
                        </a>
                        
                        <!-- Cantidad actual -->
                        <span style="min-width: 20px; text-align: center; font-weight: bold; font-size: 1rem;">
                            <?php echo htmlspecialchars($producto['cantidad']); ?>
                        </span>
                        
                        <!-- Botón aumentar -->
                        <a href="index.php?controller=ProductController&action=actualizarCantidad&id=<?php echo htmlspecialchars($producto['ID_Producto']); ?>&operacion=aumentar" 
                           style="text-decoration: none; width: 30px; height: 30px; background-color: #4caf50; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                            +
                        </a>
                    </div>
                    
                    <!-- Botón eliminar -->
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
                
                <?php if ($hay_errores_stock): ?>
                    <!-- Botón BLOQUEADO cuando hay errores de stock -->
                    <div style="flex: 1;">
                        <button type="button" disabled style="width: 100%; padding: 10px 15px; background-color: #ccc; color: #666; border: none; border-radius: 8px; cursor: not-allowed; font-size: 0.9rem;">
                            ❌ Sin Stock Suficiente
                        </button>
                    </div>
                <?php else: ?>
                    <!-- Botón NORMAL cuando todo está bien -->
                    <a href="index.php?controller=ProductController&action=confirmarPedido" style="text-decoration: none; flex: 1;">
                        <button type="button" style="width: 100%; padding: 10px 15px; background-color: #437F97; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: background-color 0.3s;">
                            ✅ Confirmar Pedido
                        </button>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
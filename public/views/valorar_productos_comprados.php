<?php
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?controller=UsuController&action=showiniciosesion");
    exit();
}
$productos = isset($data['productos']) ? $data['productos'] : [];
?>

<div style="max-width: 900px; margin: 50px auto; background-color: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h1 style="text-align: center; margin-bottom: 15px;">¡Gracias por tu compra!</h1>
    <p style="text-align: center; color: #666; margin-bottom: 30px;">Nos encantaría conocer tu opinión sobre los productos que has adquirido. Tus valoraciones ayudan a otros compradores a tomar mejores decisiones.</p>
    
    <?php if (empty($productos)): ?>
    <?php else: ?>
        <form action="index.php?controller=ValoracionesController&action=procesarValoracionesCompra" method="POST">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; margin-bottom: 30px;">
                <?php foreach ($productos as $producto): ?>
                    <div style="background: #f9f9f9; border-radius: 8px; padding: 15px;">
                        <div style="height: 160px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; background: white; border-radius: 6px;">
                            <img src="<?php echo htmlspecialchars($producto['Imagen']); ?>" alt="<?php echo htmlspecialchars($producto['Nombre_Producto']); ?>" style="max-width: 100%; max-height: 140px; object-fit: contain;">
                        </div>
                        <div style="font-weight: bold; margin-bottom: 10px;"><?php echo htmlspecialchars($producto['Nombre_Producto']); ?></div>
                        <div style="color: #437F97; font-weight: bold; margin-bottom: 15px;"><?php echo htmlspecialchars($producto['Precio']); ?> €</div>
                        
                        <div style="text-align: center; margin-bottom: 10px;">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?php echo $i; ?>-<?php echo $producto['ID_Producto']; ?>" name="valoraciones[<?php echo $producto['ID_Producto']; ?>][puntuacion]" value="<?php echo $i; ?>" style="display: none;">
                                <label for="star<?php echo $i; ?>-<?php echo $producto['ID_Producto']; ?>" style="cursor: pointer; font-size: 24px; color: #ddd;">&#9733;</label>
                            <?php endfor; ?>
                        </div>
                        <textarea name="valoraciones[<?php echo $producto['ID_Producto']; ?>][comentario]" placeholder="¿Qué te ha parecido este producto?" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-height: 80px; resize: vertical;"></textarea>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: right;">
                <button type="submit" style="background-color: #437F97; color: white; border: none; border-radius: 5px; padding: 12px 24px; font-size: 16px; cursor: pointer;">Enviar valoraciones</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<style>
    label[for^="star"] {
        transition: color 0.3s;
    }
    input[name^="valoraciones"]:checked ~ label,
    label[for^="star"]:hover,
    label[for^="star"]:hover ~ label {
        color: #FFD700 !important;
    }
</style>
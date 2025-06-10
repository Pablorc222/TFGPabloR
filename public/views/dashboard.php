<div style="height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 20px; box-sizing: border-box; overflow: hidden;">
    <h1 style="color: white; font-size: 2.5rem; margin-bottom: 30px; text-shadow: 2px 2px 4px rgba(0,0,0,0.7);">
        📊 Dashboard
    </h1>
    
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; max-width: 700px; width: 100%; margin-bottom: 30px;">
        <div style="background: rgba(46, 204, 113, 0.9); padding: 20px; border-radius: 12px; color: white; text-align: center;">
            <h2 style="font-size: 2rem; margin: 0;">€<?php echo number_format($data['ingresos_totales'] ?? 0, 2); ?></h2>
            <p style="margin: 8px 0 0; font-size: 1rem;">💰 Ingresos</p>
        </div>
        
        <div style="background: rgba(52, 152, 219, 0.9); padding: 20px; border-radius: 12px; color: white; text-align: center;">
            <h2 style="font-size: 2rem; margin: 0;"><?php echo $data['total_usuarios'] ?? 0; ?></h2>
            <p style="margin: 8px 0 0; font-size: 1rem;">👥 Usuarios</p>
        </div>
        
        <div style="background: rgba(231, 76, 60, 0.9); padding: 20px; border-radius: 12px; color: white; text-align: center;">
            <h2 style="font-size: 2rem; margin: 0;"><?php echo $data['total_pedidos'] ?? 0; ?></h2>
            <p style="margin: 8px 0 0; font-size: 1rem;">🛒 Pedidos</p>
        </div>
        
        <div style="background: rgba(243, 156, 18, 0.9); padding: 20px; border-radius: 12px; color: white; text-align: center;">
            <h2 style="font-size: 2rem; margin: 0;"><?php echo $data['pedidos_mes'] ?? 0; ?></h2>
            <p style="margin: 8px 0 0; font-size: 1rem;">📅 Este Mes</p>
        </div>
    </div>
    
    <a href="index.php" style="padding: 12px 25px; background: #3498db; color: white; text-decoration: none; border-radius: 8px; font-size: 1.1rem; font-weight: bold;">
        ⬅️ Volver
    </a>
</div>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Pedidos</title>
    <style>
        .container { max-width: 1400px; margin: 20px auto; padding: 20px; }
        h1 { text-align: center; color: black; margin-bottom: 30px; }
        .white-container { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        
        /* Filtros */
        .filter-bar { display: flex; gap: 10px; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #667eea; flex-wrap: wrap; }
        .filter-btn { padding: 8px 16px; border: none; border-radius: 20px; background: #e9ecef; color: #495057; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .filter-btn.active { background: linear-gradient(45deg, #667eea, #764ba2); color: white; }
        .filter-btn:hover { transform: translateY(-1px); }
        
        /* Tabla */
        .table-container { max-height: 70vh; overflow: auto; margin-bottom: 50px; }
        table { width: 100%; border-collapse: collapse; color: white; min-width: 800px; }
        thead { position: sticky; top: 0; background: #050517; z-index: 10; }
        th { padding: 12px 10px; text-align: left; white-space: nowrap; }
        tr:nth-child(odd) { background: rgba(0,0,0,0.6); }
        tr:nth-child(even) { background: rgba(0,0,0,0.7); }
        td { padding: 10px; vertical-align: middle; }
        
        /* Estados */
        .estado-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; text-align: center; min-width: 90px; display: inline-block; text-transform: capitalize; }
        .estado-pendiente { background: linear-gradient(45deg, #ffa726, #ff9800); color: white; }
        .estado-enviado { background: linear-gradient(45deg, #66bb6a, #4caf50); color: white; }
        .estado-entregado { background: linear-gradient(45deg, #42a5f5, #2196f3); color: white; }
        .estado-cancelado { background: linear-gradient(45deg, #ef5350, #f44336); color: white; }
        
        /* Botones */
        .estado-actions { display: flex; gap: 5px; flex-wrap: wrap; }
        .estado-btn { padding: 4px 8px; border: none; border-radius: 12px; cursor: pointer; font-size: 0.75rem; font-weight: 600; transition: all 0.3s; text-decoration: none; color: white; text-transform: capitalize; }
        .btn-pendiente { background: linear-gradient(45deg, #ffa726, #ff9800); }
        .btn-enviado { background: linear-gradient(45deg, #66bb6a, #4caf50); }
        .btn-entregado { background: linear-gradient(45deg, #42a5f5, #2196f3); }
        .btn-cancelado { background: linear-gradient(45deg, #ef5350, #f44336); }
        .estado-btn:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
        
        @media (max-width: 768px) {
            .estado-actions { flex-direction: column; gap: 3px; }
            .estado-btn { width: 100%; text-align: center; }
            th, td { padding: 8px 5px; font-size: 0.9rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Listado de Pedidos</h1>
        
        <div class="white-container">
            <div class="filter-bar">
                <button class="filter-btn active" onclick="filtrarEstado('todos')">Todos</button>
                <button class="filter-btn" onclick="filtrarEstado('pendiente')">Pendientes</button>
                <button class="filter-btn" onclick="filtrarEstado('enviado')">Enviados</button>
                <button class="filter-btn" onclick="filtrarEstado('entregado')">Entregados</button>
                <button class="filter-btn" onclick="filtrarEstado('cancelado')">Cancelados</button>
            </div>
            
            <?php if (!empty($data)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr><th>ID</th><th>Usuario</th><th>Total</th><th>Fecha</th><th>Estado</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ordenar por fecha descendente (pedidos más recientes primero)
                            usort($data, function($a, $b) {
                                return strtotime($b['fecha']) - strtotime($a['fecha']);
                            });
                            
                            foreach ($data as $pedido):
                                $estado_actual = isset($pedido['estado']) ? $pedido['estado'] : 'Pendiente';
                                $estados_disponibles = ['Pendiente', 'Enviado', 'Entregado', 'Cancelado'];
                            ?>
                                <tr class="pedido-row" data-estado="<?php echo strtolower($estado_actual); ?>">
                                    <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['usuario_nombre']); ?></td>
                                    <td><?php echo number_format($pedido['total'], 2); ?> €</td>
                                    <td>
                                        <?php 
                                        try {
                                            $fecha = new DateTime($pedido['fecha']); 
                                            $fecha->setTimezone(new DateTimeZone('Europe/Madrid'));
                                            echo $fecha->format('d/m/Y H:i:s'); 
                                        } catch (Exception $e) {
                                            echo htmlspecialchars($pedido['fecha']);
                                        }
                                        ?>
                                    </td>
                                    <td><span class="estado-badge estado-<?php echo strtolower($estado_actual); ?>"><?php echo htmlspecialchars($estado_actual); ?></span></td>
                                    <td>
                                        <div class="estado-actions">
                                            <?php foreach ($estados_disponibles as $estado): if ($estado !== $estado_actual): ?>
                                                <a href="index.php?controller=UsuController&action=cambiarEstadoPedido&id=<?php echo urlencode($pedido['id']); ?>&estado=<?php echo urlencode($estado); ?>" 
                                                   class="estado-btn btn-<?php echo strtolower($estado); ?>"
                                                   onclick="return confirm('¿Cambiar estado a <?php echo htmlspecialchars($estado); ?>?')"><?php echo htmlspecialchars($estado); ?></a>
                                            <?php endif; endforeach; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="text-align: center; padding: 20px; color: #666;">No hay pedidos disponibles.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function filtrarEstado(estado) {
            const rows = document.querySelectorAll('.pedido-row');
            const buttons = document.querySelectorAll('.filter-btn');
            
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            rows.forEach(row => {
                row.style.display = (estado === 'todos' || row.dataset.estado === estado) ? '' : 'none';
            });
        }
    </script>
</body>
</html>
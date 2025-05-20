<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Pedidos</title>
    <style>
        /* Estilos básicos */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        
        h1 {
            text-align: center;
            color: black;
            margin-bottom: 30px;
        }
        
        .white-container {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        /* Estilos de la tabla */
        .table-container {
            max-height: 65vh;
            overflow-y: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            color: white;
        }
        
        thead {
            position: sticky;
            top: 0;
            background-color: #050517;
            z-index: 10;
        }
        
        th {
            padding: 12px 10px;
            text-align: left;
        }
        
        tr:nth-child(odd) {
            background-color: rgba(0, 0, 0, 0.6);
        }
        
        tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.7);
        }
        
        td {
            padding: 10px;
        }
        
        /* Estilo del botón */
        .btn {
            background-color: #437F97;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        
        .btn:hover {
            background-color: #366475;
        }
        
        .btn-container {
            text-align: right;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Listado de Pedidos</h1>
        
        <div class="white-container">
            <div class="btn-container">
                <button class="btn" onclick="window.print()">Imprimir listado</button>
            </div>
            
            <?php if (!empty($data)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Pedido</th>
                                <th>Usuario</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Invertir el orden de los pedidos
                            $data = array_reverse($data);
                            foreach ($data as $pedido):
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($pedido['ID_Pedido']); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['usuario_nombre']); ?></td>
                                    <td><?php echo number_format($pedido['total'], 2); ?> €</td>
                                    <td><?php 
                                        $fecha = new DateTime($pedido['fecha']); 
                                        $fecha->setTimezone(new DateTimeZone('Europe/Madrid'));
                                        echo $fecha->format('d/m/Y H:i:s'); 
                                    ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="text-align: center; padding: 20px;">No hay pedidos disponibles.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
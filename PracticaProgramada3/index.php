<?php
session_start();
if (!isset($_SESSION['transacciones'])) {
    $_SESSION['transacciones'] = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Transacciones</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Registro de Transacciones</h2>
        <form action="guardar_transaccion.php" method="post">
            <label for="descripcion">Descripción:</label>
            <input type="text" name="descripcion" required>
            
            <label for="monto">Monto:</label>
            <input type="number" step="0.01" name="monto" required>
            
            <button type="submit">Agregar Transacción</button>
        </form>

        <h2>Estado de Cuenta</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Monto</th>
            </tr>
            <?php
            $totalContado = 0;
            foreach ($_SESSION['transacciones'] as $index => $transaccion) {
                echo "<tr>
                        <td>".($index + 1)."</td>
                        <td>{$transaccion['descripcion']}</td>
                        <td>\${$transaccion['monto']}</td>
                      </tr>";
                $totalContado += $transaccion['monto'];
            }

            $interes = $totalContado * 0.026;
            $cashback = $totalContado * 0.001;
            $totalPagar = $totalContado + $interes - $cashback;
            ?>
        </table>

        <h3>Totales</h3>
        <p>Monto Total de Contado: $<?php echo number_format($totalContado, 2); ?></p>
        <p>Monto con Interés (2.6%): $<?php echo number_format($totalContado + $interes, 2); ?></p>
        <p>Cashback (0.1%): $<?php echo number_format($cashback, 2); ?></p>
        <p><strong>Monto Final a Pagar: $<?php echo number_format($totalPagar, 2); ?></strong></p>
    </div>
</body>
</html>
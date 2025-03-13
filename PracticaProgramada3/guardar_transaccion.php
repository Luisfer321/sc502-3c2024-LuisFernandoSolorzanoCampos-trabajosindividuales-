<?php
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = $_POST['descripcion'];
    

    $monto = str_replace(",", ".", $_POST['monto']);
    $monto = floatval($monto);

    if ($monto > 0) {
        $_SESSION['transacciones'][] = [
            'descripcion' => $descripcion,
            'monto' => $monto
        ];
    }
}


$totalContado = 0;
$estadoCuenta = "ESTADO DE CUENTA\n--------------------------\n";
$estadoCuenta .= "ID\tDescripción\tMonto\n--------------------------\n";

foreach ($_SESSION['transacciones'] as $index => $transaccion) {
    $estadoCuenta .= ($index + 1) . "\t" . $transaccion['descripcion'] . "\t$" . number_format($transaccion['monto'], 2) . "\n";
    $totalContado += $transaccion['monto'];
}

$interes = $totalContado * 0.026;
$cashback = $totalContado * 0.001;
$totalPagar = $totalContado + $interes - $cashback;

$estadoCuenta .= "--------------------------\n";
$estadoCuenta .= "Monto Total de Contado: $" . number_format($totalContado, 2) . "\n";
$estadoCuenta .= "Monto con Interés (2.6%): $" . number_format($totalContado + $interes, 2) . "\n";
$estadoCuenta .= "Cashback (0.1%): $" . number_format($cashback, 2) . "\n";
$estadoCuenta .= "Monto Final a Pagar: $" . number_format($totalPagar, 2) . "\n";


file_put_contents("estado_cuenta.txt", $estadoCuenta);


header("Location: index.php");
exit();
?>
<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

$conexion = new mysqli("localhost", "root", "", "ventas");

$hoy = date("Y-m-d");

$sql = "SELECT COUNT(*) AS cantidad, SUM(total) AS total_dia FROM facturas WHERE fecha = ?";
 $stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $hoy);
$stmt->execute();
   $res = $stmt->get_result()->fetch_assoc();

$cantidad = $res["cantidad"] ?? 0;
$total_dia = $res["total_dia"] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Diario</title>
</head>
<body>
    <h2>Reporte del dia <?php echo $hoy; ?></h2>
      <p><strong>Facturas hechas:</strong> <?php echo $cantidad; ?></p>
    <p><strong>Total cobrado:</strong> RD$<?php echo number_format($total_dia, 2); ?></p>
     <a href="index.php"> Volver al Inicio</a>
</body>
</html>

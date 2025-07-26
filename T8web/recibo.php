<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"])) {
    die("Factura no especificada.");
}

$conexion = new mysqli("localhost", "root", "", "ventas");

$id = $_GET["id"];

$sql = "SELECT f.*, c.codigo, c.nombre FROM facturas f
            JOIN clientes c ON f.cliente_id = c.id
            WHERE f.id = ?";
 $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    die("Factura no encontrada.");
}

$factura = $res->fetch_assoc();

$sql = "SELECT * FROM articulos_factura WHERE factura_id = ?";
 $stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
    $stmt->execute();
$articulos = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo <?php echo $factura["numero_recibo"]; ?></title>
    <style>
        body { font-family: monospace; width: 300px; margin: 0 auto; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td, th { border-bottom: 1px solid #ccc; padding: 4px; text-align: left; }
        .total { font-weight: bold; }
        .center { text-align: center; margin-top: 20px; }
        @media print {
            button { display: none; }
        }
    </style>
</head>
<body>
    <h2>La Rubia</h2>
    <p><strong>Recibo:</strong> <?php echo $factura["numero_recibo"]; ?><br>
    <strong>Fecha:</strong> <?php echo $factura["fecha"]; ?><br>
    <strong>Codigo Cliente:</strong> <?php echo $factura["codigo"]; ?><br>
    <strong>Nombre:</strong> <?php echo $factura["nombre"]; ?></p>

    <table>
        <tr>
            <th>Articulo</th>
            <th>Cant.</th>
            <th>Precio</th>
            <th>Total</th>
        </tr>
        <?php while ($row = $articulos->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row["nombre_articulo"]; ?></td>
            <td><?php echo $row["cantidad"]; ?></td>
            <td>RD$<?php echo number_format($row["precio_unitario"], 2); ?></td>
            <td>RD$<?php echo number_format($row["total"], 2); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p class="total">Total a pagar:<?php echo number_format($factura["total"], 2); ?></p>

    <?php if ($factura["comentario"]): ?>
        <p><strong>Comentario:</strong> <?php echo $factura["comentario"]; ?></p>
    <?php endif; ?>

    <div class="center">
        <button onclick="window.print()">Imprimir</button><br>
        <a href="index.php">Volver</a>
    </div>
</body>
</html>

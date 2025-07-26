<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

$conexion = new mysqli("localhost", "root", "", "ventas");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_cliente = $_POST["codigo_cliente"];
     $nombre_cliente = $_POST["nombre_cliente"];
    $comentario = $_POST["comentario"];
      $fecha = date("Y-m-d");

    $stmt = $conexion->prepare("SELECT id FROM clientes WHERE codigo = ?");
     $stmt->bind_param("s", $codigo_cliente);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $cliente = $res->fetch_assoc();
        $cliente_id = $cliente["id"];
    } else {
        $stmt = $conexion->prepare("INSERT INTO clientes (codigo, nombre) VALUES (?, ?)");
        $stmt->bind_param("ss", $codigo_cliente, $nombre_cliente);
    $stmt->execute();
        $cliente_id = $stmt->insert_id;
    }

    $total_general = 0;
    foreach ($_POST["articulos"] as $art) {
        $total_general += $art["cantidad"] * $art["precio"];
    }

    $res = $conexion->query("SELECT COUNT(*) as total FROM facturas");
    $count = $res->fetch_assoc()["total"] + 1;
    $numero_recibo = "REC-" . str_pad($count, 3, "0", STR_PAD_LEFT);

    $stmt = $conexion->prepare("INSERT INTO facturas (numero_recibo, fecha, cliente_id, comentario, total) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisd", $numero_recibo, $fecha, $cliente_id, $comentario, $total_general);
    $stmt->execute();
    $factura_id = $stmt->insert_id;

    foreach ($_POST["articulos"] as $art) {
     $nombre = $art["nombre"];
    $cantidad = $art["cantidad"];
        $precio = $art["precio"];
        $total = $cantidad * $precio;

        $stmt = $conexion->prepare("INSERT INTO articulos_factura (factura_id, nombre_articulo, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isidd", $factura_id, $nombre, $cantidad, $precio, $total);
        $stmt->execute();
    }

    header("Location: recibo.php?id=" . $factura_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Factura</title>
    <script>
        function agregarArticulo() {
            const contenedor = document.getElementById("articulos");
            const index = document.querySelectorAll(".articulo").length;

            const html = `
                <div class="articulo">
                    <input type="text" name="articulos[${index}][nombre]" placeholder="Artículo" required>
                    <input type="number" name="articulos[${index}][cantidad]" placeholder="Cant." min="1" required>
                    <input type="number" name="articulos[${index}][precio]" placeholder="Precio" min="0" step="0.01" required>
                    <button type="button" onclick="this.parentNode.remove()">❌</button>
                </div>`;
            contenedor.insertAdjacentHTML("beforeend", html);
        }
    </script>
</head>
<body>
    <h2>Registrar nueva factura</h2>
    <form method="POST">
        <label>Codigo:</label>
    <input type="text" name="codigo_cliente" required><br><br>

            <label>Cliente:</label>
            <input type="text" name="nombre_cliente" required><br><br>

        <div id="articulos">
        </div>
         <button type="button" onclick="agregarArticulo()">Agregar</button><br><br>

        <label>Comentario:</label><br>
    <textarea name="comentario" rows="3" cols="40"></textarea><br><br>

        <button type="submit">Imprimir</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>

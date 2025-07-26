<?php
//Marializ Disla 2023-1098

session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>FacturaFacil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 500px;">
        <div class="card-body text-center">
            <h3 class="card-title mb-4">Bienvenido a <strong>FacturaFácil</strong></h3>
            <p class="text-muted mb-4">Usuario: <strong><?php echo htmlspecialchars($_SESSION["usuario"]); ?></strong></p>
            <div class="d-grid gap-3">
                <a href="factura_nueva.php" class="btn btn-primary">Registrar nueva factura</a>
                <a href="reporte_diario.php" class="btn btn-info text-white">Ver reporte diario</a>
                <a href="cerrar.php" class="btn btn-danger">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>

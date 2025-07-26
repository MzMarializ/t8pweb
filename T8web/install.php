<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "ventas";

$conn = new mysqli($host, $user, $password);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
    }

$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Basecreada correctamente.<br>";
} else {
   
    die("Error creando base de datos: " . $conn->error);
}

$conn->select_db($dbname);

$queries = [

    "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario VARCHAR(50) NOT NULL,
        clave VARCHAR(255) NOT NULL
    )",

    "CREATE TABLE IF NOT EXISTS clientes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codigo VARCHAR(20) NOT NULL UNIQUE,
        nombre VARCHAR(100) NOT NULL
    )",

    "CREATE TABLE IF NOT EXISTS facturas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        numero_recibo VARCHAR(20) NOT NULL UNIQUE,
        fecha DATE NOT NULL,
        cliente_id INT,
        comentario TEXT,
        total DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (cliente_id) REFERENCES clientes(id)
    )",

    "CREATE TABLE IF NOT EXISTS articulos_factura (
        id INT AUTO_INCREMENT PRIMARY KEY,
        factura_id INT,
        nombre_articulo VARCHAR(100),
        cantidad INT,
        precio_unitario DECIMAL(10,2),
        total DECIMAL(10,2),
        FOREIGN KEY (factura_id) REFERENCES facturas(id)
    )"
];

foreach ($queries as $query) {
    if ($conn->query($query) === TRUE) {
        echo "Tabla creada.<br>";
    } else {
        echo "Error creando tabla: " . $conn->error . "<br>";
    }
}

$usuario = 'demo';
$clave = password_hash('tareafacil25', PASSWORD_DEFAULT);

$check = $conn->query("SELECT * FROM usuarios WHERE usuario = 'demo'");
if ($check->num_rows == 0) {
    $insert = $conn->prepare("INSERT INTO usuarios (usuario, clave) VALUES (?, ?)");
      $insert->bind_param("ss", $usuario, $clave);
    if ($insert->execute()) {
        echo "Usuario demo creado.<br>";
      } else {
        echo "Error al insertar usuario.<br>";
    }
} else {
     echo "Este usuario ya existe.<br>";
 }

$conn->close();
?>

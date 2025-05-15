
<?php
$host     = getenv('RAILWAY_MYSQL_HOST');
$port     = getenv('RAILWAY_MYSQL_PORT');
$user     = getenv('RAILWAY_MYSQL_USER');
$pass     = getenv('RAILWAY_MYSQL_PASSWORD');
$dbName   = getenv('RAILWAY_MYSQL_DATABASE');

$conexion = mysqli_connect($host, $user, $pass, $dbName, (int)$port);
if (!$conexion) {
    die('Error al conectar la BD: ' . mysqli_connect_error());
}

// Manejo de errores
if (!$conexion) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Opcional: establece charset (utf8mb4)
mysqli_set_charset($conexion, 'utf8mb4');
?>
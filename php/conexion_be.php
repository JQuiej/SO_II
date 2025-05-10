/*
$conexion = mysqli_connect("db", "root", "secret", "login_registro_db");

if ($conexion) {
    echo 'Conexión exitosa a la base de datos!';
} else {
    echo 'Conexión fallida a la base de datos: ' . mysqli_connect_error();
}*/

<?php
// conexion_be.php

// Recupera los valores de las variables de entorno de Railway
$host     = getenv('RAILWAY_MYSQL_HOST');
$port     = getenv('RAILWAY_MYSQL_PORT');
$user     = getenv('RAILWAY_MYSQL_USER');
$password = getenv('RAILWAY_MYSQL_PASSWORD');
$database = getenv('RAILWAY_MYSQL_DATABASE');

// Intenta la conexión (el puerto se pasa como quinto parámetro)
$conexion = mysqli_connect($host, $user, $password, $database, $port);

// Manejo de errores
if (!$conexion) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Opcional: establece charset (utf8mb4)
mysqli_set_charset($conexion, 'utf8mb4');
?>
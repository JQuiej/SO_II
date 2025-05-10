<?php
include 'conexion_be.php';

$token = $_POST['token'];
$nueva = password_hash($_POST['nueva_contrasena'], PASSWORD_DEFAULT);

// Obtener usuario por token
$stmt = mysqli_prepare($conexion, "SELECT id FROM usuarios WHERE token_recuperacion = ?");
mysqli_stmt_bind_param($stmt, "s", $token);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($usuario = mysqli_fetch_assoc($result)) {
    $id = $usuario['id'];

    // Actualizar contraseña y eliminar token
    $stmt = mysqli_prepare($conexion, "UPDATE usuarios SET contrasena = ?, token_recuperacion = NULL WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $nueva, $id);
    mysqli_stmt_execute($stmt);

    echo "Contraseña actualizada con éxito. Ya puedes iniciar sesión.";
} else {
    echo "Token inválido.";
}

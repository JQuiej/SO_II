<?php
include 'conexion_be.php';

// 1. Leer y normalizar el correo
$correo = trim(strtolower($_POST['correo'] ?? ''));

// 1.a. Debug rápido: escribe el valor en los logs de Docker/PHP
error_log("[DEBUG] correo recibido para recuperación: '{$correo}'");

// 2. Si el campo está vacío, redirige con error
if ($correo === '') {
    header('Location: ../index.php?error=correo_vacio');
    exit();
}

// 3. Prepara la consulta usando LOWER() para ignorar mayúsculas
$stmt = mysqli_prepare(
    $conexion,
    "SELECT id FROM usuarios WHERE LOWER(correo) = ? LIMIT 1"
);
mysqli_stmt_bind_param($stmt, 's', $correo);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// 4. Si no hay fila, redirige con error
if (! $usuario = mysqli_fetch_assoc($result)) {
    header('Location: ../index.php?error=correo_no_encontrado');
    exit();
}

// 5. Genera y guarda el token de recuperación
$id    = $usuario['id'];
$token = bin2hex(random_bytes(16));
$stmt  = mysqli_prepare(
    $conexion,
    "UPDATE usuarios SET token_recuperacion = ? WHERE id = ?"
);
mysqli_stmt_bind_param($stmt, 'si', $token, $id);
mysqli_stmt_execute($stmt);

// 6. Monta la URL de restablecimiento basándote en $_SERVER
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'];            // e.g. localhost:8080
$base   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); // e.g. /php
$link   = "{$scheme}://{$host}{$base}/restablecer_password.php?token=" . urlencode($token);

// 7. Para entorno local mostramos la URL en pantalla
echo "<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='UTF-8'>
  <title>Recuperación de contraseña</title>
  <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head>
<body>
  <script>
    Swal.fire({
      icon: 'info',
      title: 'Enlace de recuperación',
      html: 'Copia y pega este enlace en tu navegador:<br><br>' +
            '<a href=\"{$link}\" target=\"_blank\">{$link}</a>',
      confirmButtonText: 'Cerrar'
    }).then(() => {
      window.location.href = '../index.php';
    });
  </script>
</body>
</html>";

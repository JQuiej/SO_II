<?php
include __DIR__ . '/conexion_be.php';

$token = $_POST['token'];
$nuevaPlano = $_POST['nueva'] ?? '';

if (strlen($nuevaPlano) < 6) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
    Swal.fire({
        icon: 'warning',
        title: 'Contraseña demasiado corta',
        text: 'Debe tener al menos 6 caracteres.'
    }).then(() => {
        window.history.back();
    });
    </script>
    ";
    exit;
}
$nueva = password_hash($nuevaPlano, PASSWORD_DEFAULT);
$nueva = password_hash($_POST['nueva'], PASSWORD_DEFAULT);

$res = mysqli_query($conexion, "SELECT id FROM usuarios WHERE token_recuperacion = '$token'");
if ($user = mysqli_fetch_assoc($res)) {
    $id = $user['id'];
    mysqli_query($conexion, "UPDATE usuarios SET contrasena = '$nueva', token_recuperacion = NULL WHERE id = $id");

    // ✅ Mostrar mensaje de éxito y redirigir
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Contraseña actualizada</title>
        <link rel='stylesheet' href='../assets/css/estilo_recuperacion.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
        Swal.fire({
            icon: 'success',
            title: '¡Contraseña actualizada!',
            text: 'Ahora puedes iniciar sesión con tu nueva contraseña.',
            confirmButtonText: 'Ir al login'
        }).then(() => {
            window.location.href = '../index.php';
        });
        </script>
    </body>
    </html>
    ";
    
} else {
    // ❌ Token inválido
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Error de token</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Token inválido',
        text: 'El enlace ha expirado o es incorrecto.',
        confirmButtonText: 'Volver al inicio'
    }).then(() => {
        window.location.href = '../index.php';
    });
    </script>
    </body>
    </html>
    ";
}
?>



<?php
session_start();
include __DIR__ . '/conexion_be.php';

// Recibir datos
$nombre_completo = $_POST['nombre_completo'] ?? '';
$correo = $_POST['correo'] ?? '';
$usuario = $_POST['usuario'] ?? '';
$contrasena = $_POST['contrasena'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$avatar_url = null;

// Validaciones básicas
if (empty($nombre_completo) || empty($correo) || empty($usuario) || empty($contrasena)) {
    header("Location: ../index.php?error=Todos+los+campos+son+obligatorios");
    exit();
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../index.php?error=Correo+inválido");
    exit();
}

// Verificar si el usuario o correo ya existe
$verificar = mysqli_prepare($conexion, "SELECT id FROM usuarios WHERE usuario = ? OR correo = ?");
mysqli_stmt_bind_param($verificar, "ss", $usuario, $correo);
mysqli_stmt_execute($verificar);
mysqli_stmt_store_result($verificar);

if (mysqli_stmt_num_rows($verificar) > 0) {
    header("Location: ../index.php?error=El+usuario+o+correo+ya+existe");
    exit();
}
mysqli_stmt_close($verificar);

// Procesar imagen (avatar)
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $archivo = $_FILES['avatar'];
    $tamano_maximo = 2 * 1024 * 1024; // 2MB
    $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    $nombre_original = $archivo['name'];
    $tmp_name = $archivo['tmp_name'];
    $tamano = $archivo['size'];
    $ext = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

    if (!in_array($ext, $ext_permitidas)) {
        header("Location: ../index.php?error=Formato+de+imagen+no+permitido");
        exit();
    }

    if ($tamano > $tamano_maximo) {
        header("Location: ../index.php?error=La+imagen+excede+los+2MB");
        exit();
    }

    $nombre_archivo = uniqid() . '.' . $ext;
    $ruta_relativa = 'uploads/' . $nombre_archivo;
    $ruta_absoluta = '../' . $ruta_relativa;

    if (move_uploaded_file($tmp_name, $ruta_absoluta)) {
        $avatar_url = $ruta_relativa;
    } else {
        header("Location: ../index.php?error=Error+al+subir+la+imagen");
        exit();
    }
}

// Hashear contraseña
$contrasena_segura = password_hash($contrasena, PASSWORD_DEFAULT);
$result = mysqli_query($conexion, "SELECT COALESCE(MAX(id),0) + 1 AS next_id FROM usuarios");
$row    = mysqli_fetch_assoc($result);
$nextId = $row['next_id'];
// Insertar usuario
$insertar = mysqli_prepare($conexion, "INSERT INTO usuarios (id, nombre_completo, correo, usuario, contrasena, telefono, avatar_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($insertar, "issssss",$nextId, $nombre_completo, $correo, $usuario, $contrasena_segura, $telefono, $avatar_url);
mysqli_stmt_execute($insertar);

if (mysqli_stmt_affected_rows($insertar) > 0) {
    header("Location: ../index.php?mensaje=Registro+exitoso");
    exit();
} else {
    header("Location: ../index.php?error=Error+al+registrar+usuario");
    exit();
}
?>


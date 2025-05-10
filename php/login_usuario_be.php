<?php
session_start();
include 'conexion_be.php';

$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];

if (empty($usuario) || empty($contrasena)) {
    header("Location: ../index.php?error=Debes+llenar+todos+los+campos");
    exit();
}

$query = "SELECT id, usuario, contrasena, rol, estado FROM usuarios WHERE usuario = ?";


$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($resultado && mysqli_num_rows($resultado) === 1) {
    $fila = mysqli_fetch_assoc($resultado);

    if ($fila['estado'] !== 'activo') {
        header("Location: ../index.php?error=Tu+cuenta+está+desactivada+por+el+administrador");
        exit();
    }

    if (password_verify($contrasena, $fila['contrasena'])) {
        session_regenerate_id(true);
        $_SESSION['usuario'] = $fila['usuario'];
        $_SESSION['rol'] = $fila['rol']; // Guardar rol
        $_SESSION['id'] = $fila['id']; // ✅ Guardar ID del usuario logueado
        // ✅ Registrar en historial
        $usuario = $_SESSION['usuario'];
        $accion = "Inició sesión";
        mysqli_query($conexion, "INSERT INTO historial (usuario, accion) VALUES ('$usuario', '$accion')");
        header("Location: ../php/bienvenida.php");
        exit();
    } else {
        header("Location: ../index.php?error=Contraseña+incorrecta");
        exit();
    }
} else {
    header("Location: ../index.php?error=Usuario+no+encontrado");
    exit();
}
?>

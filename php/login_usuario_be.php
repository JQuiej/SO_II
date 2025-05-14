<?php
session_start();
// regeneramos ID justo después de arrancar sesión
session_regenerate_id(true);

include 'conexion_be.php';  // nuestra conexión “Railway”

$usuario    = $_POST['usuario']    ?? '';
$contrasena = $_POST['contrasena'] ?? '';

if (empty($usuario) || empty($contrasena)) {
    header("Location: ../index.php?error=Debes+llenar+todos+los+campos");
    exit();
}

// Preparamos y ejecutamos la consulta
$sql  = "SELECT id, usuario, contrasena, rol, estado FROM usuarios WHERE usuario = ?";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if ($res && mysqli_num_rows($res) === 1) {
    $fila = mysqli_fetch_assoc($res);

    if ($fila['estado'] !== 'activo') {
        header("Location: ../index.php?error=Tu+cuenta+está+desactivada+por+el+administrador");
        exit();
    }

    if (password_verify($contrasena, $fila['contrasena'])) {
        // Login OK
        session_regenerate_id(true);
        $_SESSION['usuario'] = $fila['usuario'];
        $_SESSION['rol']     = $fila['rol'];
        $_SESSION['id']      = $fila['id'];

        // Registro en historial (ya tu tabla debe tener AUTO_INCREMENT en id)
        $accion = "Inició sesión";
        mysqli_query(
          $conexion,
          "INSERT INTO historial (usuario, accion) VALUES ('{$fila['usuario']}', '$accion')"
        );

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

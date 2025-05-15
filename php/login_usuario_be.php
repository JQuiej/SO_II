<?php
session_start();
// regeneramos ID justo después de arrancar sesión
session_regenerate_id(true);

include __DIR__ . '/conexion_be.php';

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

// ahora registramos en historial con una prepared statement
        $accion = "Inició sesión";
        $stmtHist = mysqli_prepare(
        $conexion,
        "INSERT INTO historial (`usuario`,`accion`) VALUES (?, ?)"
        );
        mysqli_stmt_bind_param($stmtHist, "ss", $fila['usuario'], $accion);
        mysqli_stmt_execute($stmtHist);
        mysqli_stmt_close($stmtHist);

        // y redirigimos…
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

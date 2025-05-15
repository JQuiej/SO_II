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
        $_SESSION['usuario'] = $fila['usuario'];
        $_SESSION['rol']     = $fila['rol'];
        $_SESSION['id']      = $fila['id'];
        
        // calculamos manualmente el próximo ID
        $result = mysqli_query($conexion, "SELECT COALESCE(MAX(id),0) + 1 AS next_id FROM historial");
        $row    = mysqli_fetch_assoc($result);
        $nextId = $row['next_id'];
// ahora registramos en historial con una prepared statement
        $accion = "Inició sesión";
        $fecha = date('Y-m-d H:i:s');  // hora local

        $stmtHist = mysqli_prepare(
            $conexion,
            "INSERT INTO historial (`id`,`usuario`,`accion`,`fecha`) VALUES (?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmtHist, "isss",$nextId, $fila['usuario'], $accion, $fecha);
        mysqli_stmt_execute($stmtHist);
        mysqli_stmt_close($stmtHist);

        // y redirigimos…
        header("Location: ../php/bienvenida.php");
    exit();
    } else {
        header("Location: ../index.php?error=Contraseña+incorrecta");
        exit();
    }
}

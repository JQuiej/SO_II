<?php
include 'conexion_be.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    echo "Token no proporcionado.";
    exit;
}

$res = mysqli_query($conexion, "SELECT * FROM usuarios WHERE token_recuperacion = '$token'");
if (!mysqli_num_rows($res)) {
    echo "Token inválido o expirado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
    <link rel="stylesheet" href="../css/estilo_recuperacion.css">
</head>
<body>
    <div class="contenedor-recuperacion">
        <h2>🔐 Nueva Contraseña</h2>
        <form action="guardar_nueva_password.php" method="POST" onsubmit="return validarPassword()">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="password" name="nueva" id="nueva" placeholder="Nueva contraseña" required>
            <button type="submit">Guardar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function validarPassword() {
            const nueva = document.getElementById("nueva").value;
            if (nueva.length < 6) {
                Swal.fire({
                    icon: "warning",
                    title: "Contraseña muy corta",
                    text: "Debe tener al menos 6 caracteres."
                });
                return false;
            }
            return true;
        }
    </script>
</body>
</html>



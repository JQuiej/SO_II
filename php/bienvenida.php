<?php
// Iniciamos sesión y buffer para evitar "headers already sent"
ob_start();
session_start();


// Si no hay rol en sesión, redirige al login
if (!isset($_SESSION['rol'])) {
    $_SESSION['rol'] = 'usuario';
    header("Location: ../index.php");
    exit();
}

include __DIR__ . '/conexion_be.php';

// Recupera datos de usuario
$usuario = $_SESSION['usuario'];
$query   = "SELECT nombre_completo, avatar_url, telefono, rol FROM usuarios WHERE usuario = ?";
$stmt    = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

// Valores por defecto
$nombre_completo = "Invitado";
$avatar_url      = null;
$telefono        = "";

if ($fila = mysqli_fetch_assoc($resultado)) {
    $nombre_completo = $fila['nombre_completo'];
    $avatar_url      = $fila['avatar_url'];   // e.g. "uploads/imagen.jpg"
    $telefono        = $fila['telefono'];
    $rol             = $fila['rol'];
} else {
    // Por si algo raro sucede
    $nombre_completo = "Invitado";
    $avatar_url      = null;
    $telefono        = "";
    $rol             = "usuario";
}

mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Bienvenida</title>

  <!-- Estilos generales -->
  <link rel="stylesheet" href="../assets/css/estilo_general.css">


  <!-- Estilos específicos -->
  <style>
    body {
    margin: 0;
    padding: 5px;
    font-family: 'Segoe UI', Arial, sans-serif;
    text-align: center;
    background-image: linear-gradient(rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.41)),
                url(../images/bground3.jpg) no-repeat center center fixed;
    background-size: cover;
    color: #fff;
    max-width: 600px;
    margin: 40px auto;
    }
    .avatar {
      width: 120px;
      border-radius: 50%;
      margin-bottom: 10px;
      border: 3px solid #fff;
    }
    .boton-admin {
      display: inline-block;
      padding: 10px 20px;
      background: linear-gradient(0deg, rgba(96,9,240,1) 0%, rgba(129,5,240,1) 100%);
      border: none;
      border-radius: 8px;
      color: #fff;
      text-decoration: none;
      font-weight: bold;
      margin: 10px;
    }
    .enlace-botones {
      display: inline-block;
      padding: 10px 20px;
      background: linear-gradient(0deg, rgba(96,9,240,1) 0%, rgba(129,5,240,1) 100%);
      border: none;
      border-radius: 8px;
      color: #fff;
      text-decoration: none;
      margin: 10px;
    }
  </style>

  <!-- SweetAlert si perfil fue actualizado -->
  <?php if (isset($_GET['perfil_actualizado'])): ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Perfil actualizado',
      text: 'Tus datos fueron guardados correctamente.',
      timer: 2000,
      showConfirmButton: false
    });
  </script>
  <?php endif; ?>
</head>

<body>
  <div class="container">
    <?php if ($avatar_url): ?>
      <!-- Ruta absoluta para el avatar -->
      <img src="/<?php echo htmlspecialchars($avatar_url); ?>" class="avatar" alt="Avatar">
    <?php endif; ?>

    <h1>Bienvenido, <?php echo htmlspecialchars($nombre_completo); ?> 👋</h1>

    <?php if ($_SESSION['rol'] === 'admin'): ?>
      <p><strong>Rol:</strong> Administrador 👑</p>
      <a href="admin_panel.php" class="boton-admin">⚙️ Panel de Admin</a>
    <?php else: ?>
      <p><strong>Rol:</strong> Usuario estándar 👤</p>
    <?php endif; ?>

    <a href="editar_perfil.php" class="enlace-botones">✏️ Editar Perfil</a>
    <a href="logout.php"      class="enlace-botones">✖️ Cerrar Sesión</a>
  </div>

  <?php ob_end_flush(); ?>
</body>
</html>

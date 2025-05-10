<?php
session_start();
include 'conexion_be.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de usuario no válido.";
    exit();
}

$id_usuario = (int) $_GET['id'];

// Obtener datos del usuario
$stmt = mysqli_prepare($conexion, "SELECT nombre_completo, correo, telefono, rol, avatar_url FROM usuarios WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit();
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_completo'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];

    // Avatar (opcional)
    $avatar_url = $usuario['avatar_url'];
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $filename = 'uploads/avatar_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['avatar']['tmp_name'], '../' . $filename);
        $avatar_url = $filename;
    }

    // Actualizar
    $update = mysqli_prepare($conexion, "UPDATE usuarios SET nombre_completo = ?, correo = ?, telefono = ?, rol = ?, avatar_url = ? WHERE id = ?");
    mysqli_stmt_bind_param($update, "sssssi", $nombre, $correo, $telefono, $rol, $avatar_url, $id_usuario);
    mysqli_stmt_execute($update);

    header("Location: admin_panel.php?actualizado=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <style>
      body {
            font-family: Arial;
            padding: 5px;
            text-align: center;
          }
          body{
            background-image: linear-gradient(rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.41)), 
            url(../images/bground3.jpg);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
                }

          form {
            max-width: 400px;
            background:rgb(19, 38, 147);
            padding: 20px;
            color: white;
            border-radius: 10px;
            margin: 0 auto;
          }
          form input, form select, form textarea {
            width: 100%; /* Ajusta el ancho de los campos al 100% del contenedor */
            padding: 10px; /* Agrega un poco de padding para mejorar la legibilidad */
            margin-bottom: 20px; /* Agrega un poco de margen para separar los campos */
            border: 2px solid #ccc; /* Agrega un borde para mejorar la apariencia */
            box-sizing: border-box; /* Ajusta el cálculo del ancho y el alto para incluir el borde y el padding */
            font-size: 16px; /* Ajusta el tamaño de la fuente a 16px */
            color:gray;
            display: block; /* Muestra los campos uno debajo del otro */  
            border-radius: 10px;
          }
         
            input, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
          }

          label {
                  font-weight: bold;
                  margin-bottom: 5px;
                  transform: translateY(-50%); /* Ajusta el label a la mitad de la altura de la imagen */
                  position: relative; /* Agrega esta propiedad para que el label se posicione relativo a su padre */
                  top: 50%; /* Ajusta el label a la mitad de la altura de la imagen */
                }

                img.avatar {
                  width: 100px;
                  margin-top: 10px;
                  border-radius: 10px;
                }

          a {
            display: inline-block;
            margin-top: 10px;
            color: #3498db;
          }

          .volver-panel {
            display: inline-block;
            padding: 10px 20px;
            background-color:rgb(19, 38, 147);
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
          }

          .volver-panel:hover {
            background: linear-gradient(to bottom,rgb(46, 59, 204),rgb(127, 51, 199));
          }
          h2 {
            font-size: 28px;
            color:rgb(255, 255, 255);
            text-align: center;
            margin-bottom: 20px;
            
          }

          button {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
          }

          button:hover {
            background: linear-gradient(to bottom,rgb(46, 59, 204),rgb(127, 51, 199));
          }
      
    </style>
</head>
<body>

<h2>🛠 Editar Usuario</h2>

<form method="POST" enctype="multipart/form-data">
  <label>Nombre completo:</label>
  <input type="text" name="nombre_completo" value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" required>

  <label>Correo electrónico:</label>
  <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>

  <label>Teléfono:</label>
  <input type="text" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>">

  <label>Rol:</label>
  <select name="rol">
    <option value="usuario" <?php if ($usuario['rol'] === 'usuario') echo 'selected'; ?>>usuario</option>
    <option value="admin" <?php if ($usuario['rol'] === 'admin') echo 'selected'; ?>>admin</option>
  </select>

  <label for="avatar" class="volver-panel">Seleccione el avatar </label>
  <input type="file" name="avatar" accept="image/*" id="avatar" style="display: none;">
  <img src="../<?php echo $usuario['avatar_url']; ?>" class="avatar" id="preview-avatar">
  <script>
    document.getElementById('avatar').addEventListener('change', function() {
      var reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('preview-avatar').src = e.target.result;
      };
      reader.readAsDataURL(this.files[0]);
    });
  </script>
  <button type="submit"> Guardar cambios</button>
  
</form>

<a href="admin_panel.php" class="volver-panel"> ← Volver al Panel de Administración</a>
</html>

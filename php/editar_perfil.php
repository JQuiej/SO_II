<?php
session_start();
include __DIR__ . '/conexion_be.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

$usuario = $_SESSION['usuario'];

// Obtener datos actuales
$query = "SELECT nombre_completo, telefono, avatar_url FROM usuarios WHERE usuario = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$nombre_completo = '';
$telefono = '';
$avatar_url = '';

if ($fila = mysqli_fetch_assoc($resultado)) {
    $nombre_completo = $fila['nombre_completo'];
    $telefono = $fila['telefono'];
    $avatar_url = $fila['avatar_url'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = $_POST['nombre_completo'] ?? '';
    $nuevo_telefono = $_POST['telefono'] ?? '';
    $nueva_contrasena = $_POST['contrasena'] ?? '';
    $nuevo_avatar = null;

    // Validar campos
    if (empty($nuevo_nombre)) {
        header("Location: editar_perfil.php?error=El+nombre+no+puede+estar+vacío");
        exit();
    }

    // Procesar nuevo avatar (opcional)
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $archivo = $_FILES['avatar'];
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $max_size = 2 * 1024 * 1024;

        if (!in_array($ext, $permitidos)) {
            header("Location: editar_perfil.php?error=Formato+de+imagen+no+permitido");
            exit();
        }
        if ($archivo['size'] > $max_size) {
            header("Location: editar_perfil.php?error=La+imagen+excede+2MB");
            exit();
        }

        $nombre_archivo = uniqid() . '.' . $ext;
        $ruta_relativa = 'uploads/' . $nombre_archivo;
        $ruta_absoluta = '../' . $ruta_relativa;

        if (move_uploaded_file($archivo['tmp_name'], $ruta_absoluta)) {
            $nuevo_avatar = $ruta_relativa;
        }
    }

    // Actualizar datos
    if (!empty($nueva_contrasena)) {
        $nueva_contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        $update = mysqli_prepare($conexion, "UPDATE usuarios SET nombre_completo = ?, telefono = ?, contrasena = ?, avatar_url = IFNULL(?, avatar_url) WHERE usuario = ?");
        mysqli_stmt_bind_param($update, "sssss", $nuevo_nombre, $nuevo_telefono, $nueva_contrasena_hash, $nuevo_avatar, $usuario);
    } else {
        $update = mysqli_prepare($conexion, "UPDATE usuarios SET nombre_completo = ?, telefono = ?, avatar_url = IFNULL(?, avatar_url) WHERE usuario = ?");
        mysqli_stmt_bind_param($update, "ssss", $nuevo_nombre, $nuevo_telefono, $nuevo_avatar, $usuario);
    }

    $result = mysqli_query($conexion, "SELECT COALESCE(MAX(id),0) + 1 AS next_id FROM historial");
    $row    = mysqli_fetch_assoc($result);
    $nextId = $row['next_id'];

    mysqli_stmt_execute($update);
    $accion = "Actualizó su perfil";
    $fecha = date('Y-m-d H:i:s');  // hora local

    $stmtHist = mysqli_prepare(
        $conexion,
        "INSERT INTO historial (`id`,`usuario`,`accion`,`fecha`) VALUES (?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmtHist, "isss",$nextId, $fila['nombre_completo'], $accion, $fecha);
    mysqli_stmt_execute($stmtHist);
    mysqli_stmt_close($stmtHist);

    // Redirigir a la página de bienvenida
    if ($nuevo_avatar) {
        $avatar_url = $nuevo_avatar;
    }

    // Actualizar la sesión
    $_SESSION['nombre_completo'] = $nuevo_nombre;
    $_SESSION['telefono'] = $nuevo_telefono;
    $_SESSION['avatar_url'] = $avatar_url;

    // Redirigir a la página de bienvenida
    
header("Location: bienvenida.php?perfil_actualizado=1");
exit;
header("Location: admin_panel.php?actualizado=$id");
    
exit;

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="../assets/css/estilo_general.css">

<meta charset="UTF-8">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="estilos.css">
    </head>
<body>
    <div class="container">
        <h2>Editar Perfil</h2>
        <form method="POST" enctype="multipart/form-data">
    <style>

body {
    font-family: Arial;
            padding: 5px;
            text-align: center;
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 40px auto;
            color: #fff;
        }
        body{
            background-image: linear-gradient(rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.41)), 
            url(../images/bground3.jpg)no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
                }

            .container {
                background: linear-gradient(to bottom,rgb(46, 59, 204),rgb(127, 51, 199)));
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
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
            color:white;
            display: block; /* Muestra los campos uno debajo del otro */  
            border-radius: 10px;
          }
         
            img.avatar {
                width: 120px;
                border-radius: 50%;
                margin-bottom: 30px;
                border: 3px solid #fff;
            }

            label {
                display: block;
                margin-top: 15px;
                color: #fff;
            }
            h2{
                color: #fff;
                text-align: center;
                margin-bottom: 20px;
                font-size: 32px;
                font-weight: bold;
            }

            input {
                width: 100%;
                padding: 8px;
                margin-top: 5px;
                background: rgba(255, 255, 255, 0.1);
                border: none;
                border-radius: 5px;
                color: #fff;
            }

button {
    margin-top: 20px;
    padding: 10px 20px;
    background: linear-gradient(to bottom,rgb(41, 38, 178),rgb(57, 130, 213));
    border: none;
    border-radius: 5px;
    color: #fff;
    cursor: pointer;
}

button:hover {
    background: linear-gradient(to bottom,rgb(46, 59, 204),rgb(127, 51, 199));
}

.mensaje {
    margin: 20px 0;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
}

.success {
    background: linear-gradient(to bottom,rgb(147, 51, 211),rgb(57, 130, 213));
    color: #fff;
}

.error {
    background: #e74c3c;
    color: #fff;
}
p {
    text-align: center;
    margin-top: 20px;
}

a {
    background: linear-gradient(to bottom,rgb(41, 38, 178),rgb(57, 130, 213));
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
}

a:hover {
    background: linear-gradient(to bottom,rgb(46, 59, 204),rgb(127, 51, 199));
}

    </style>
</head>
<body>

     <?php if (isset($_GET['mensaje'])): ?>
        <div class="mensaje success"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="mensaje error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <?php if ($avatar_url): ?>
        <img src="../<?php echo htmlspecialchars($avatar_url); ?>" class="avatar" alt="Avatar">
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Nombre Completo</label>
        <input type="text" name="nombre_completo" value="<?php echo htmlspecialchars($nombre_completo); ?>">

        <label>Teléfono</label>
        <input type="text" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>">

        <label>Contraseña (deja en blanco para no cambiar)</label>
        <input type="password" name="contrasena">

        <label>Actualizar Avatar</label>
        <input type="file" name="avatar" accept="image/*">

        <button type="submit">Guardar Cambios</button>
    </form>

    <p><a href="bienvenida.php">← Regresar</a></p>
</body>
</html>

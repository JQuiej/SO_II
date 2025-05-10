<?php
session_start();
include 'conexion_be.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php?error=Acceso+denegado");
    exit();
}

// Limpiar historial si se solicita
if (isset($_POST['limpiar_historial'])) {
    mysqli_query($conexion, "DELETE FROM historial");
    header("Location: historial.php?eliminado=1");
exit;

}

$historial = mysqli_query($conexion, "SELECT * FROM historial ORDER BY fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    
    <meta charset="UTF-8">
    <title>Historial de Actividades</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../assets/css/estilo_general.css">

    <style>
        body {
            margin: 0 auto !important;
            margin-left: auto;
            margin-right: auto;
            padding: 20px;
            font-family: 'Segoe UI', sans-serif;
            background-image: url(../images/bground3.jpg)no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            background: rgba(0, 0, 70, 0.9);
            border-radius: 16px;
            padding: 30px 40px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
            text-align: center;
            color: white;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 25px;
        }

        .tabla-historial {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .tabla-historial th,
        .tabla-historial td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        .tabla-historial th {
            background-color: #2c3e50;
            color: white;
        }

        .volver {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            margin-right: 10px;
            border: none;
            cursor: pointer;
        } .limpiar {
            width: 150px; /* Ancho fijo */
            height: 44px; /* Alto fijo */
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            margin-right: 10px;
            border: none;
            cursor: pointer;
        }
        .limpiar:hover {
            background-color: #e74c3c;
        }
        .volver:hover {
            background: linear-gradient(to bottom,rgb(41, 38, 178),rgb(57, 130, 213));
            border: none;
        }

        .limpiar.button {
    position: relative;
    width: 180px; /* Ajusta el ancho del botón */
    height: 40px;
    cursor: pointer;
    display: flex;
    align-items: center;
    border: 1px solid #cc0000;
    background-color: #e50000;
    overflow: hidden;
}

.limpiar.button, .limpiar.button__icon, .limpiar.button__text {
    transition: all 0.3s;
}

.limpiar.button .button__text {
    transform: translateX(35px);
    color: #fff;
    font-weight: 600;
}

.limpiar.button .button__icon {
    position: absolute;
    transform: translateX(109px);
    height: 100%;
    width: 39px;
    background-color: #cc0000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.limpiar.button .svg {
    width: 20px;
}

.limpiar.button:hover {
    background: #cc0000;
}

.limpiar.button:hover .button__text {
    color: transparent;
}

.limpiar.button:hover .button__icon {
    width: 148px;
    transform: translateX(0);
}

.limpiar.button:active .button__icon {
    background-color: #b20000;
}

.limpiar.button:active {
    border: 1px solid #b20000;
}
.botones-inline {
    display: inline-block;
    margin-left: 10px;
}
.button__icon {
    position: absolute;
    margin-top: -15px;
    top: 40%;
    transform: translateY(-40%);
    right: 20px;
    height: 20px;
    width: 20px;
    background-color: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
}
.button__text {
    z-index: 5;
}

.button__icon {
    z-index: 5;
}
    </style>
</head>
<body>
<?php if (isset($_GET['eliminado'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: 'success',
    title: 'Historial eliminado',
    text: 'Todos los registros fueron borrados correctamente.',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true
});
</script>
<?php endif; ?>

    <div class="container">
        <h2>📋 Historial de Actividades</h2>
        <table class="tabla-historial">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = mysqli_fetch_assoc($historial)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($fila['usuario'] ?: '(desconocido)'); ?></td>
                        <td><?php echo htmlspecialchars($fila['accion']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="admin_panel.php" class="volver">← Volver al panel</a>
<button class="limpiar button" style="display: inline-block; margin-left: 10px;" onclick="confirmarLimpiar()">
    <span class="button__text">Eliminar</span>
    <span class="button__icon">
        <svg class="svg" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg">
        <title></title><path d="M112,112l20,320c.95,18.49,14.4,32,32,32H348c17.67,0,30.87-13.51,32-32l20-320" 
        style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"></path>
        <line style="stroke:#fff;stroke-linecap:round;stroke-miterlimit:10;stroke-width:32px" x1="80" x2="432" y1="112" y2="112">
        </line><path d="M192,112V72h0a23.93,23.93,0,0,1,24-24h80a23.93,23.93,0,0,1,24,24h0v40" 
        style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px">
    </path><line style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px" 
    x1="256" x2="256" y1="176" y2="400">
    </line><line style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px" x1="184" x2="192" y1="176" y2="400">
    </line><line style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px" x1="328" x2="320" y1="176" y2="400">
        
    </line></svg></span>
</button>
        </svg>
    </span>
</button>

        </svg>
    </span>
</button>

        <form id="form-limpiar" method="POST" style="display:none;">
            <input type="hidden" name="limpiar_historial" value="1">
        </form>

     
    </div>
    
    <script>
    function confirmarLimpiar() {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡Esta acción eliminará todo el historial!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-limpiar').submit();
        }
    });
}
</script>

</body>
</html>

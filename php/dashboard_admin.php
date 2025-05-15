<?php
session_start();
include __DIR__ . '/conexion_be.php';

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php?error=Acceso+denegado");
    exit();
}

// Contadores
$total_usuarios = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM usuarios"))['total'];
$total_admins = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin'"))['total'];
$total_estandar = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM usuarios WHERE rol = 'usuario'"))['total'];

// Últimos movimientos
$ultimos_logs = mysqli_query($conexion, "SELECT * FROM historial ORDER BY fecha DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
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
    background-position: center ;
    display: flex;
    justify-content: center ;
    align-items: center;
    min-height: 100vh;
}

.dashboard {
    background: rgba(0, 0, 70, 0.9);
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 0 20px rgba(0,0,0,0.4);
    max-width: 800px;
    margin: 0 auto !important;
    margin-left: auto;
    margin-right: auto;
    color: white;
    text-align: center;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
}




        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .resumen {
            display: flex;
            justify-content: space-aroundc;
            margin-bottom: 30px;
        }

        .card {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            padding: 20px;
            border-radius: 10px;
            width: 40%;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            justify-content: center;
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 26px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #34495e;
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
        }
        .volver:hover {
            background: linear-gradient(to bottom,rgb(41, 38, 178),rgb(57, 130, 213));
            border: none;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>📊 Dashboard Admin</h1>

        <div class="resumen">
            <div class="card">
                <h3>Usuarios Totales</h3>
                <p><?php echo $total_usuarios; ?></p>
            </div>
            <div class="card">
                <h3>Administradores</h3>
                <p><?php echo $total_admins; ?></p>
            </div>
            <div class="card">
                <h3>Usuarios Estándar</h3>
                <p><?php echo $total_estandar; ?></p>
            </div>
        </div>

        <h2>🕒 Últimas 5 Actividades</h2>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($log = mysqli_fetch_assoc($ultimos_logs)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['fecha']  ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($log['usuario'] ?? '(desconocido)'); ?></td>
                    <td><?php echo htmlspecialchars($log['accion']  ?? ''); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <a href="admin_panel.php" class="volver">← Volver al Panel</a>
    </div>
</body>
</html>

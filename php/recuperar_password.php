<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>
    <link rel="stylesheet" href="../assets/css/estilo_recuperacion.css">
</head>
<body>
    <div class="contenedor-recuperacion">
        <h2>🔒 Recuperar contraseña</h2>
        <form action="procesar_recuperacion.php" method="POST">
            <input type="email" name="correo" placeholder="Ingresa tu correo" required>
            <button type="submit">Enviar enlace</button>
        </form>

        <!-- ✅ Botón de regreso -->
        <a href="../index.php" class="btn-volver">← Volver al login</a>
    </div>
</body>
</html>

<style>
/* Fondo con imagen */
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-image: url(../images/bground3.jpg)no-repeat center center fixed;
    background-size: cover;
    background-position: center;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Contenedor principal */
.contenedor-recuperacion {
    background: rgba(0, 0, 70, 0.8);
    border-radius: 16px;
    padding: 40px 50px;
    width: 400px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    text-align: center;
    backdrop-filter: blur(4px);
}

/* Título */
.contenedor-recuperacion h2 {
    color: #fff;
    margin-bottom: 30px;
    font-size: 26px;
}

/* Inputs */
.contenedor-recuperacion input[type="email"],
.contenedor-recuperacion input[type="password"] {
  width: 80%;
  padding: 12px;
  margin: 10px auto 20px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
}

/* Botón enviar */
.contenedor-recuperacion button {
  width: 80%;
  padding: 12px;
  margin: 10px auto;
  text-align: center;
  background: linear-gradient(to right, #2c3e50, #3498db);
  color: white;
  font-size: 16px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s ease;
}

.contenedor-recuperacion button:hover {
    background: linear-gradient(to right, #2980b9, #2c3e50);
}

/* ✅ Botón volver */
.btn-volver {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    background: linear-gradient(to right, #34495e, #2c3e50);
    color: white;
    border-radius: 8px;
    text-decoration: none;
    transition: background 0.3s ease;
}

.btn-volver:hover {
    background: linear-gradient(to right, #2c3e50, #34495e);
}
</style>

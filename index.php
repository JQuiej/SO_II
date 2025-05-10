<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header('Location: php/bienvenida.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Registro - UMG</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css">

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
        
<body>

    <main>
        <div class="contenedor__todo">
            <div class="caja__trasera">
                <div class="caja__trasera-login">
                    <h3>¿Ya tienes una cuenta?</h3>
                    <p>Inicia sesión para entrar a la página</p>
                    <button id="btn__iniciar-sesion">Iniciar Sesión</button>
                </div>
                <div class="caja__trasera-registro">
                    <h3>¿Aún no tienes una cuenta?</h3>
                    <p>Registrate para poder iniciar sesión en la página</p>
                    <button id="btn__registrarse">Registrarse</button>
                </div>
            </div>
            <!--Formulario de Login y Registro-->
            <div class="contenedor__login-registro">
                <!--Formulario de Login-->
                <form action="php/login_usuario_be.php" method="POST" class="formulario__login">
                    <h2>Iniciar Sesión</h2>
                    <input type="text" placeholder="Usuario" name="usuario">
                    <input type="password" placeholder="Contraseña" name="contrasena">
                    <button>Entrar</button>
                    <p><a href="php/recuperar_password.php">¿Olvidaste tu contraseña?</a></p>

                </form>
                <!--Formulario Registro-->
                <form action="php/registro_usuario_be.php" method="POST" class="formulario__registro" enctype="multipart/form-data">
                    <h2>Registrarse</h2>
                    <input type="text" placeholder="Nombre Completo" name="nombre_completo">
                    <input type="text" placeholder="Correo Electrónico" name="correo">
                    <input type="text" placeholder="Usuario" name="usuario">
                    <input type="password" placeholder="Contraseña" name="contrasena">
                    <input type="text" placeholder="Teléfono" name="telefono">
                    <label for="avatar" class="btn btn-secondary">Seleccione su avatar 👤</label>
                    <input type="file" name="avatar" accept="image/*" id="avatar" style="display: none;">
                    <img id="vista_previa" placeholder="Avatar" style="margin-top: 10px; width: 100px; display: none; border-radius: 10px;">
                    <button>Registrarse</button>
                </form>


                </form>
            </div>
        </div>
    </main>   

    <!-- JS principal -->
    <script src="assets/js/script.js"></script>
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'recuperacion_exitosa'): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Contraseña actualizada!',
        text: 'Ya puedes iniciar sesión con tu nueva contraseña.',
        confirmButtonText: 'Continuar'
    });
</script>
<?php endif; ?>

</body>
</html>

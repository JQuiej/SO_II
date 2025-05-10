<?php
session_start();
include 'conexion_be.php';

// Verificar acceso solo admin
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php?error=Acceso+denegado");
    exit();
}

// Guardar ID propio si no existe
if (!isset($_SESSION['id'])) {
    $query_id = mysqli_prepare($conexion, "SELECT id FROM usuarios WHERE usuario = ?");
    mysqli_stmt_bind_param($query_id, "s", $_SESSION['usuario']);
    mysqli_stmt_execute($query_id);
    $res_id = mysqli_stmt_get_result($query_id);
    if ($res_id && $row = mysqli_fetch_assoc($res_id)) {
        $_SESSION['id'] = $row['id'];
    }
}

// Cambiar rol y editar datos del usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'], $_POST['nuevo_rol'])) {
    $id_usuario = (int) $_POST['id_usuario'];
    $nuevo_rol = $_POST['nuevo_rol'];
    if ($id_usuario != $_SESSION['id']) {
        // Obtener el usuario afectado
        $res = mysqli_query($conexion, "SELECT usuario FROM usuarios WHERE id = $id_usuario");
        $row = mysqli_fetch_assoc($res);
        $usuario_objetivo = $row['usuario'] ?? '(desconocido)';

        // Actualizar rol
        $update = mysqli_prepare($conexion, "UPDATE usuarios SET rol = ? WHERE id = ?");
        mysqli_stmt_bind_param($update, "si", $nuevo_rol, $id_usuario);
        mysqli_stmt_execute($update);

        // Insertar en historial
        $admin = $_SESSION['usuario'];
        $accion = "Cambió rol de $usuario_objetivo a $nuevo_rol";
        mysqli_query($conexion, "INSERT INTO historial (usuario, accion) VALUES ('$admin', '$accion')");
    }
}

// Eliminar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    header("Location: admin_panel.php");
    $id_eliminar = (int) $_POST['eliminar_id'];
    if ($id_eliminar != $_SESSION['id']) {
        // Obtener usuario eliminado
        $res = mysqli_query($conexion, "SELECT usuario FROM usuarios WHERE id = $id_eliminar");
        $row = mysqli_fetch_assoc($res);
        $usuario_eliminado = $row['usuario'] ?? '(desconocido)';


        // Eliminar de la base de datos
        $eliminar = mysqli_prepare($conexion, "DELETE FROM usuarios WHERE id = ?");
        mysqli_stmt_bind_param($eliminar, "i", $id_eliminar);
        mysqli_stmt_execute($eliminar);

        // Insertar en historial
        $admin = $_SESSION['usuario'];
        $accion = "Eliminó al usuario $usuario_eliminado";
        mysqli_query($conexion, "INSERT INTO historial (usuario, accion) VALUES ('$admin', '$accion')");
    }
}
// Cambiar estado activo/inactivo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_estado'], $_POST['nuevo_estado'])) {
    header("Location: admin_panel.php");
    $id_estado = (int) $_POST['id_estado'];
    $nuevo_estado = $_POST['nuevo_estado'];

    if ($id_estado != $_SESSION['id']) {
        $update_estado = mysqli_prepare($conexion, "UPDATE usuarios SET estado = ? WHERE id = ?");
        mysqli_stmt_bind_param($update_estado, "si", $nuevo_estado, $id_estado);
        mysqli_stmt_execute($update_estado);

        $admin = $_SESSION['usuario'];
        $accion = ($nuevo_estado === 'activo') ? "Activó cuenta del usuario ID $id_estado" : "Desactivó cuenta del usuario ID $id_estado";
        mysqli_query($conexion, "INSERT INTO historial (usuario, accion) VALUES ('$admin', '$accion')");

        header("Location: admin_panel.php");
        exit();
    }
}
// Filtros de búsqueda
$busqueda = $_GET['busqueda'] ?? '';
$filtro_rol = $_GET['rol'] ?? '';
$condiciones = [];
$params = [];
$tipos = '';

if (!empty($busqueda)) {
    $condiciones[] = "(nombre_completo LIKE CONCAT('%', ?, '%') OR usuario LIKE CONCAT('%', ?, '%') OR correo LIKE CONCAT('%', ?, '%'))";
    $params[] = $busqueda;
    $params[] = $busqueda;
    $params[] = $busqueda;
    $tipos .= 'sss';
}

if (!empty($filtro_rol)) {
    $condiciones[] = "rol = ?";
    $params[] = $filtro_rol;
    $tipos .= 's';
}

$where_sql = '';
if ($condiciones) {
    $where_sql = 'WHERE ' . implode(' AND ', $condiciones);
}

// Paginación
$limite = 10;
$pagina = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;
$offset = ($pagina - 1) * $limite;

$sql_base = "SELECT SQL_CALC_FOUND_ROWS * FROM usuarios $where_sql LIMIT ?, ?";
$params[] = $offset;
$params[] = $limite;
$tipos .= 'ii';

$stmt = mysqli_prepare($conexion, $sql_base);
mysqli_stmt_bind_param($stmt, $tipos, ...$params);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$total_result = mysqli_query($conexion, "SELECT FOUND_ROWS() as total");
$total_filas = mysqli_fetch_assoc($total_result)['total'];
$total_paginas = ceil($total_filas / $limite);

$total_admins = 0;
$total_usuarios = 0;
$res_roles = mysqli_query($conexion, "SELECT rol, COUNT(*) as total FROM usuarios GROUP BY rol");
while ($fila = mysqli_fetch_assoc($res_roles)) {
    if ($fila['rol'] === 'admin') {
        $total_admins = $fila['total'];
    } elseif ($fila['rol'] === 'usuario') {
        $total_usuarios = $fila['total'];
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_estado'], $_POST['nuevo_estado'])) {
    $id_estado = (int) $_POST['id_estado'];
    $nuevo_estado = $_POST['nuevo_estado'];

    if ($id_estado != $_SESSION['id']) {
        $update_estado = mysqli_prepare($conexion, "UPDATE usuarios SET estado = ? WHERE id = ?");
        mysqli_stmt_bind_param($update_estado, "si", $nuevo_estado, $id_estado);
        mysqli_stmt_execute($update_estado);

        // Registrar en historial
        $admin = $_SESSION['usuario'];
        $accion = ($nuevo_estado === 'activo') ? "Activó cuenta del usuario ID $id_estado" : "Desactivó cuenta del usuario ID $id_estado";
        mysqli_query($conexion, "INSERT INTO historial (usuario, accion) VALUES ('$admin', '$accion')");
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin</title>
      <!-- Estilos globales compartidos -->
    <!-- Estilos específicos del panel de admin -->
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<!-- jQuery primero -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Luego table2excel que depende de jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery-table2excel@1.1.4/dist/jquery.table2excel.min.js"></script>


<!-- Exportar a PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<body>
<?php if (isset($_GET['actualizado'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: 'success',
    title: '¡Actualización exitosa!',
    text: 'Los datos del usuario se han actualizado correctamente.',
    confirmButtonColor: '#2ecc71'
});
</script>
<?php endif; ?>


<h1>
    <span style="font-size: 40px;">👑</span>
    <span class="título-gradiente">Panel de Administración</span>
</h1>

<p><strong>👑 Admins:</strong> <?php echo $total_admins; ?> | <strong>👤 Usuarios:</strong> <?php echo $total_usuarios; ?></p>


<div class="filtros">
    <form method="GET">
        <input type="text" name="busqueda" placeholder="Buscar..." value="<?php echo htmlspecialchars($busqueda); ?>">
        <select name="rol">
            <option value="">Todos</option>
            <option value="usuario" <?php if ($filtro_rol === 'usuario') echo 'selected'; ?>>Usuario</option>
            <option value="admin" <?php if ($filtro_rol === 'admin') echo 'selected'; ?>>Admin</option>
        </select>
        <button type="submit">Aplicar</button>
    </form>
</div>

<table id="tabla" class="tabla-usuarios">

    <thead>
        <tr>
            <th>ID</th>
            <th>Avatar</th>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Rol actual</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($usuario = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?php echo $usuario['id']; ?></td>
                <td>
                    <?php if ($usuario['avatar_url']): ?>
                        <img src="../<?php echo htmlspecialchars($usuario['avatar_url']); ?>" class="avatar">
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                <td><?php echo htmlspecialchars($usuario['estado']); ?></td>

                <td>
                    <?php if ($usuario['id'] != $_SESSION['id']): ?>
                        <form method="POST" class="form-actualizar" data-usuario-id="<?php echo $usuario['id']; ?>">

                            <input type="hidden" name="id_usuario" value="<?php echo $usuario['id']; ?>">
                            <select name="nuevo_rol">
                                <option value="usuario" <?php if ($usuario['rol'] === 'usuario') echo 'selected'; ?>>usuario</option>
                                <option value="admin" <?php if ($usuario['rol'] === 'admin') echo 'selected'; ?>>admin</option>
                            </select>
                            <button type="submit">Actualizar</button>
                        </form>
                        <form method="POST" class="form-estado" data-usuario-id="<?php echo $usuario['id']; ?>">

                            <input type="hidden" name="id_estado" value="<?php echo $usuario['id']; ?>">
                            <input type="hidden" name="nuevo_estado" value="<?php echo $usuario['estado'] === 'activo' ? 'inactivo' : 'activo'; ?>">
                            <button type="submit">
                                <?php echo $usuario['estado'] === 'activo' ? '🔒 Desactivar' : '🔓 Activar'; ?>
                            </button>
                        </form>


                        <form method="POST" class="form-eliminar">
                            <input type="hidden" name="eliminar_id" value="<?php echo $usuario['id']; ?>">
                            <button type="submit" style="background-color: #e74c3c; color: white;">Eliminar</button>

                        </form>
                        <form action="editar_usuario.php" method="get">
                            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                            <button type="submit" style="background-color: #2ecc71; color: white;">Editar</button>
                            
                        </form>
                        
                    <?php else: ?>
                        <em>(Tú) No editable</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
    <div class="botones-exportar">
    <div>
        <button class="custom-btn btn-1" onclick="exportarExcel()">📥 Exportar a Excel</button>
        <button class="custom-btn btn-1" onclick="exportarPDF()">📄 Exportar a PDF</button>
    </div>
    <button class="dashboard-btn" onclick="irADashboard()">📊 Ver Dashboard</button>
<button class="custom-btn btn-1 button-link" onclick="verHistorial()">📋 Ver historial</button>
</div>

</table>

<div class="paginador">
    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
        <a href="?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($busqueda); ?>&rol=<?php echo urlencode($filtro_rol); ?>"
            <?php if ($i == $pagina) echo 'style="font-weight:bold; background:#2980b9;"'; ?>>
            <?php echo $i; ?>
        </a>
      

    <?php endfor; ?>
</div>

<a class="volver" href="bienvenida.php">← Volver</a>
<script>

function exportarPDF() {
    const tabla = document.querySelector("table");
    const columnas = tabla.querySelectorAll("th:last-child, td:last-child");

    columnas.forEach(col => col.style.display = "none");

    html2canvas(tabla).then(canvas => {
        const imgData = canvas.toDataURL("image/png");
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF();
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = (canvas.height * pageWidth) / canvas.width;

        const fechaHora = new Date().toLocaleString();
        pdf.setFontSize(18);
        pdf.setTextColor(40);
        pdf.text("Reporte de Usuarios - Panel Administrativo", pageWidth / 2, 15, { align: "center" });
        pdf.setFontSize(11);
        pdf.text(`Generado el: ${fechaHora}`, pageWidth / 2, 22, { align: "center" });

        pdf.addImage(imgData, 'PNG', 0, 30, pageWidth, pageHeight);
        pdf.save("usuarios.pdf");

        columnas.forEach(col => col.style.display = "");

        // ✅ Alerta de éxito
        Swal.fire({
            icon: 'success',
            title: 'PDF exportado',
            text: 'El archivo se descargó correctamente.'
        });
    });
}

function exportarExcel() {
    const tablaOriginal = document.querySelector('.tabla-usuarios');

    if (!tablaOriginal) {
        alert("No se encontró la tabla.");
        return;
    }

    const tablaClon = tablaOriginal.cloneNode(true);
    tablaClon.querySelectorAll("tr").forEach(fila => {
        if (fila.cells.length >= 2) fila.deleteCell(1);
        fila.deleteCell(fila.cells.length - 1);
    });

    const caption = document.createElement("caption");
    caption.innerHTML = "<strong>Reporte de Usuarios - Panel Administrativo</strong><br><small>Generado el: " 
    + new Date().toLocaleString() + "</small><br><br>";
    caption.style.textAlign = "center";
    tablaClon.insertBefore(caption, tablaClon.firstChild);

    const htmlTabla = tablaClon.outerHTML.replace(/ /g, '%20');
    const url = 'data:application/vnd.ms-excel,' + htmlTabla;

    const link = document.createElement('a');
    link.href = url;
    link.download = 'usuarios.xls';
    link.click();

    // ✅ Alerta
    Swal.fire({
        icon: 'success',
        title: 'Excel exportado',
        text: 'El archivo se descargó correctamente.'
    });
}



</script>
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.form-eliminar').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            // Evita el envío inmediato
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¡Esto eliminará al usuario permanentemente!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#3498db'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Enviar solo si se confirma
                }
            });
        });
    });
</script>
<script>
     document.querySelectorAll('.form-actualizar').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Actualizar rol?',
                text: 'Estás a punto de modificar los permisos de este usuario.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#2ecc71',
                cancelButtonColor: '#7f8c8d'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(form);
                    fetch('', { method: 'POST', body: formData })
                        .then(res => {
                            const userId = form.dataset.usuarioId;
                            const fila = document.querySelector(`tr[data-id="${userId}"]`);
                            const nuevoRol = form.querySelector('select[name="nuevo_rol"]').value;
                            if (fila) {
                                fila.children[6].textContent = nuevoRol;
                            }
                            Swal.fire('Rol actualizado', '', 'success');
                        });
                }
            });
        });
    });

    document.querySelectorAll('.form-estado').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const estado = form.querySelector('input[name="nuevo_estado"]').value;
            const accion = estado === 'activo' ? 'activar' : 'desactivar';

            Swal.fire({
                title: `¿Deseas ${accion} esta cuenta?`,
                text: `El usuario será ${estado === 'activo' ? 'habilitado para iniciar sesión' : 'bloqueado del sistema'}.`,
                icon: estado === 'activo' ? 'success' : 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: estado === 'activo' ? '#2ecc71' : '#e74c3c',
                cancelButtonColor: '#95a5a6'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(form);
                    fetch('', { method: 'POST', body: formData })
                        .then(() => {
                            const userId = form.dataset.usuarioId;
                            const fila = document.querySelector(`tr[data-id="${userId}"]`);
                            if (fila) {
                                fila.children[7].textContent = estado;
                                form.querySelector('input[name="nuevo_estado"]').value = estado === 'activo' ? 'inactivo' : 'activo';
                                form.querySelector('button').textContent = estado === 'activo' ? '🔒 Desactivar' : '🔓 Activar';
                            }
                            Swal.fire(`Cuenta ${accion}da`, '', 'success');
                        });
                }
            });
        });
    });

function irADashboard() {
    Swal.fire({
        title: 'Cargando...',
        icon: 'info',
        timer: 1000,
        showConfirmButton: false
    }).then(() => {
        window.location.href = 'dashboard_admin.php';
    });
}

function verHistorial() {
    Swal.fire({
        title: 'Redirigiendo al historial...',
        icon: 'info',
        timer: 1000,
        showConfirmButton: false
    }).then(() => {
        window.location.href = 'historial.php';
    });
}

document.querySelectorAll('.form-actualizar').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Actualizar rol?',
            text: 'Estás a punto de modificar los permisos de este usuario.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2ecc71',
            cancelButtonColor: '#7f8c8d'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
                Swal.fire({
                    icon: 'success',
                    title: 'Rol actualizado',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        });
    });
});

document.querySelectorAll('.form-eliminar').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¡Esto eliminará al usuario permanentemente!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#3498db'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
                Swal.fire({
                    icon: 'success',
                    title: 'Usuario eliminado',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        });
    });
});

document.querySelectorAll('.form-estado').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const estado = form.querySelector('input[name="nuevo_estado"]').value;
        const accion = estado === 'activo' ? 'activar' : 'desactivar';

        Swal.fire({
            title: `¿Deseas ${accion} esta cuenta?`,
            text: `El usuario será ${estado === 'activo' ? 'habilitado para iniciar sesión' : 'bloqueado del sistema'}.`,
            icon: estado === 'activo' ? 'success' : 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: estado === 'activo' ? '#2ecc71' : '#e74c3c',
            cancelButtonColor: '#95a5a6'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
                Swal.fire({
                    icon: 'success',
                    title: `Cuenta ${estado === 'activo' ? 'activada' : 'desactivada'}`,
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        });
    });
});

</script>
// Selecciono los elementos
var contenedor_login_registro = document.querySelector(".contenedor__login-registro");
var formulario_login = document.querySelector(".formulario__login");
var caja_trasera_login = document.querySelector(".caja__trasera-login");
var formulario_registro = document.querySelector(".formulario__registro");
var caja_trasera_registro = document.querySelector(".caja__trasera-registro");

// Restablecer los formularios
document.forms[0].reset();
document.forms[1].reset();

// Asegurar que el formulario de inicio de sesión se muestre primero
formulario_login.style.display = "block";
formulario_registro.style.display = "none";

// Mostrar alertas emergentes si hay mensajes en la URL
window.addEventListener("load", function () {
    iniciarSesion(); // Asegurar que se muestre login al cargar
    anchoPagina();
    mostrarAlertaDesdeURL();
});

document.getElementById("btn__iniciar-sesion").addEventListener("click", iniciarSesion);
document.getElementById("btn__registrarse").addEventListener("click", registro);
window.addEventListener("resize", anchoPagina);

// Función para el ancho de la página
function anchoPagina() {
    if (window.innerWidth > 850) {
        caja_trasera_login.style.display = "block";
        caja_trasera_registro.style.display = "block";
    } else {
        caja_trasera_registro.style.display = "block";
        caja_trasera_registro.style.opacity = "1";
        caja_trasera_login.style.display = "none";
        formulario_login.style.display = "block";
        formulario_registro.style.display = "none";
        contenedor_login_registro.style.left = "0px";
    }
}

// Función para iniciar sesión
function iniciarSesion() {
    formulario_registro.style.display = "none";
    formulario_login.style.display = "block";

    if (window.innerWidth > 850) {
        contenedor_login_registro.style.left = "10px";
        caja_trasera_registro.style.opacity = "1";
        caja_trasera_login.style.opacity = "0";
    } else {
        contenedor_login_registro.style.left = "0px";
        caja_trasera_registro.style.display = "block";
        caja_trasera_login.style.display = "none";
    }
}

// Función para registro
function registro() {
    formulario_registro.style.display = "block";
    formulario_login.style.display = "none";

    if (window.innerWidth > 850) {
        contenedor_login_registro.style.left = "410px";
        caja_trasera_registro.style.opacity = "0";
        caja_trasera_login.style.opacity = "1";
    } else {
        contenedor_login_registro.style.left = "0px";
        caja_trasera_registro.style.display = "none";
        caja_trasera_login.style.display = "block";
        caja_trasera_login.style.opacity = "1";
    }
}

// ✅ Función para mostrar alertas emergentes desde la URL
// Mostrar alertas desde la URL con SweetAlert2
function mostrarAlertaDesdeURL() {
    const params = new URLSearchParams(window.location.search);
    const mensaje = params.get("mensaje");
    const error = params.get("error");

    if (mensaje) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: decodeURIComponent(mensaje),
            confirmButtonColor: '#2ecc71'
        }).then(() => {
            iniciarSesion();
        });
        history.replaceState(null, "", window.location.pathname);
    }

    if (error) {
        const texto = decodeURIComponent(error).toLowerCase();

        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: decodeURIComponent(error),
            confirmButtonColor: '#e74c3c'
        }).then(() => {
            if (texto.includes('contraseña') || texto.includes('usuario') || texto.includes('no encontrado')) {
                iniciarSesion();
            } else {
                registro();
            }
        });

        history.replaceState(null, "", window.location.pathname);
    }
}
// Vista previa de imagen de perfil
document.addEventListener("DOMContentLoaded", function () {
    const inputAvatar = document.getElementById("avatar");
    const vistaPrevia = document.getElementById("vista_previa");

    if (inputAvatar) {
        inputAvatar.addEventListener("change", function () {
            const archivo = inputAvatar.files[0];

            if (archivo && archivo.type.startsWith("image/")) {
                const lector = new FileReader();
                lector.onload = function (e) {
                    vistaPrevia.src = e.target.result;
                    vistaPrevia.style.display = "block";
                };
                lector.readAsDataURL(archivo);
            } else {
                vistaPrevia.src = "";
                vistaPrevia.style.display = "none";
            }
        });
    }
});

document.querySelector('form').addEventListener('submit', function(e) {
    const ipInput = this.ip.value.trim();
    const ipRegex = /^(\d{1,3}\.){3}\d{1,3}$/;
    if (!ipRegex.test(ipInput)) {
        alert('Por favor, introduce una IP válida en formato xxx.xxx.xxx.xxx');
        e.preventDefault();
    }
});


// Confirmación antes de eliminar registros
document.addEventListener("DOMContentLoaded", function () {
    const enlacesEliminar = document.querySelectorAll("a.eliminar");

    enlacesEliminar.forEach(function (enlace) {
        enlace.addEventListener("click", function (e) {
            const confirmar = confirm("¿Estás seguro de que quieres eliminar este elemento?");
            if (!confirmar) {
                e.preventDefault();
            }
        });
    });

    // Mostrar / ocultar contraseña
    const toggleBtn = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener("click", function () {
            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";

            // Cambiar icono
            const icon = document.getElementById("toggleIcon");
            if (icon) {
                icon.className = isPassword ? "bi bi-eye-slash" : "bi bi-eye";
            }

            // Cambiar texto sin romper el botón
            const textoSpan = this.querySelector("span.texto");
            if (textoSpan) {
                textoSpan.textContent = isPassword ? " Ocultar" : " Mostrar";
            }
        });
    }

    // Validación de formulario de registro
    const registroForm = document.getElementById("formRegistro");

    if (registroForm) {
        registroForm.addEventListener("submit", function (e) {
            const nombre = document.getElementById("nombre").value.trim();
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;

            let errores = [];

            if (nombre === "") errores.push("El nombre es obligatorio.");
            if (email === "" || !email.includes("@")) errores.push("Email inválido.");
            if (password.length < 6) errores.push("La contraseña debe tener al menos 6 caracteres.");

            if (errores.length > 0) {
                e.preventDefault();
                alert(errores.join("\n"));
            }
        });
    }

    // Mostrar flash de PHP como toast animado
    const flash = document.getElementById("flash-data");
    if (flash) {
        const tipo = flash.dataset.tipo;
        const mensaje = flash.dataset.mensaje;
        mostrarToast(tipo, mensaje);
    }
});

// Función para mostrar toast
function mostrarToast(tipo, mensaje) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${tipo}`;
    toast.innerText = mensaje;

    container.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 5000);
}


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
            const tipo = passwordInput.type === "password" ? "text" : "password";
            passwordInput.type = tipo;
            toggleBtn.textContent = tipo === "password" ? "Mostrar" : "Ocultar";
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
});


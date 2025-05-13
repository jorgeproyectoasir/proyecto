// Confirmación antes de eliminar registros
document.addEventListener("DOMContentLoaded", function () {
    const enlacesEliminar = document.querySelectorAll("a.eliminar");

    enlacesEliminar.forEach(function (enlace) {
        enlace.addEventListener("click", function (e) {
            const confirmar = confirm("¿Estás seguro de que quieres eliminar este elemento?");
            if (!confirmar) {
                e.preventDefault(); // Cancela el enlace si el usuario no confirma
            }
        });
    });
});


</div> <!-- Cierre del div principal abierto en header.php -->

<footer class="text-white py-4" style="background-color: #004085; font-size: 1.3rem;">
  <div class="container d-flex justify-content-between align-items-center flex-wrap text-center">
    <div>
      &copy; <?= date('Y') ?> Plataforma IT. Desarrollado por <strong>Jorge Juncá López</strong> — Proyecto ASIR
    </div>
    <div>
      <a href="<?= $path_prefix ?>cookies.php" class="text-white text-decoration-underline me-4">Política de Cookies</a>
      <a href="<?= $path_prefix ?>privacidad.php" class="text-white text-decoration-underline">Política de Privacidad</a>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Tiempo de inactividad antes de la expiración (en segundos)
const tiempoLimite = 900; // 15 minutos
const avisoAntes = 60;

let contador = tiempoLimite;

const alerta = document.createElement("div");
alerta.textContent = "⚠️ Tu sesión está a punto de expirar por inactividad.";
alerta.style.cssText = `
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #004085;
    color: black;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    font-weight: bold;
    display: none;
    z-index: 9999;
`;
document.body.appendChild(alerta);

const intervalo = setInterval(() => {
    contador--;

    if (contador === avisoAntes) {
        alerta.style.display = 'block';
    }

    if (contador <= 0) {
        clearInterval(intervalo);
        window.location.href = '/proyecto/index.php?expirado=1';
    }
}, 1000);

['mousemove', 'keydown', 'click', 'scroll'].forEach(evento => {
    document.addEventListener(evento, () => {
        contador = tiempoLimite;
        alerta.style.display = 'none';
    });
});
</script>

</body>
</html>

<?php
include 'includes/auth.php';
include 'includes/header.php';
echo "<h2>Bienvenido, {$_SESSION['nombre']}</h2>";
echo "<p>Rol: {$_SESSION['rol']}</p>";

// Contenido personalizado
switch ($_SESSION['rol']) {
    case 'admin':
        echo "<ul>
                <li><a href='usuarios/listar.php'>Gestión de usuarios</a></li>
                <li><a href='usuarios/asignar_roles.php'>Asignar roles</a></li>
                <li><a href='dispositivos/listar.php'>Gestión de dispositivos</a></li>
                <li><a href='logs/accesos.php'>Ver logs de acceso</a></li>
              </ul>";
        break;

    case 'tecnico':
        echo "<ul>
                <li><a href='dispositivos/listar.php'>Ver dispositivos</a></li>
                <li><a href='tareas/listar.php'>Ver tareas programadas</a></li>
                <li><a href='eventos/ver_logs.php'>Ver eventos</a></li>
              </ul>";
        break;

    case 'usuario':
        echo "<ul>
                <li><a href='incidencias/crear.php'>Reportar incidencia</a></li>
                <li><a href='incidencias/listar.php'>Mis incidencias</a></li>
              </ul>";
        break;

    default:
        echo "<p>Rol desconocido</p>";
}

echo "<br><a href='logout.php' class='btn btn-danger'>Cerrar sesión</a>";

include 'includes/footer.php';


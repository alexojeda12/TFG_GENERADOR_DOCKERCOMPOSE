<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$num = $_POST['num_contenedores'] ?? 1;
$archivo = $_POST['nombre_archivo'] ?? 'docker_compose';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de contenedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        pre {
            background: #f8f9fa;
            padding: 1em;
            border-radius: 8px;
            font-family: monospace;
            white-space: pre-wrap;
        }
    </style>
</head>
<body class="container py-5">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="inicio.php">DockerGen</a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="nav-link disabled text-white">¡Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mis_dockers.php">Ver mis Dockers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<h1 class="mb-4">Configura tus contenedores</h1>
<form method="POST" action="generar.php" id="dockerForm">
    <input type="hidden" name="nombre_archivo" value="<?= htmlspecialchars($archivo) ?>">
    <input type="hidden" name="num_contenedores" value="<?= $num ?>">

    <!-- Subred personalizada -->
    <div class="mb-4">
        <label>Subred personalizada (opcional, formato CIDR):</label>
        <input name="subred" id="subred" class="form-control" placeholder="Ej: 192.168.100.0/24">
    </div>

    <!-- Nombre de la subred -->
    <div class="mb-4">
        <label>Nombre de la subred (opcional, por defecto 'custom_net'):</label>
        <input name="nombre_subred" id="nombre_subred" class="form-control" placeholder="Ej: red_privada">
    </div>

    <?php for ($i = 1; $i <= $num; $i++): ?>
        <div class="contenedor-group mb-4" data-id="<?= $i ?>">
            <h5>Contenedor <?= $i ?>:</h5>
            <div class="mb-3">
                <label>Nombre:</label>
                <input name="contenedor[<?= $i ?>][nombre]" class="form-control nombre" required>
            </div>
            <div class="mb-3">
                <label>Imagen (sin versión):</label>
                <input name="contenedor[<?= $i ?>][imagen]" class="form-control imagen" required>
            </div>
            <div class="mb-3">
                <label>Versión de la imagen (opcional):</label>
                <input name="contenedor[<?= $i ?>][version]" class="form-control version">
            </div>
            <div class="mb-3">
                <label>Puertos (host:contenedor):</label>
                <input name="contenedor[<?= $i ?>][puertos]" class="form-control puertos">
            </div>
            <hr>
        </div>
    <?php endfor; ?>

    <button type="submit" class="btn btn-success">Generar Docker Compose</button>
</form>

<h3 class="mt-5">Vista previa:</h3>
<pre id="vistaPrevia">version: '3'
services:
  ...
</pre>

<script>
function generarVistaPrevia() {
    const contenedores = document.querySelectorAll('.contenedor-group');
    const subred = document.getElementById('subred')?.value.trim();
    const nombreSubred = document.getElementById('nombre_subred')?.value.trim() || 'custom_net';

    let resultado = "version: '3'\nservices:\n";

    contenedores.forEach(group => {
        const nombre = group.querySelector('.nombre')?.value.trim();
        const imagen = group.querySelector('.imagen')?.value.trim();
        let version = group.querySelector('.version')?.value.trim();
        const puertos = group.querySelector('.puertos')?.value.trim();

        if (nombre && imagen) {
            version = version || "latest";
            resultado += `  ${nombre}:\n`;
            resultado += `    image: ${imagen}:${version}\n`;
            if (puertos) {
                resultado += `    ports:\n      - "${puertos}"\n`;
            }
            if (subred) {
                resultado += `    networks:\n      - ${nombreSubred}\n`;
            }
        }
    });

    if (subred) {
        resultado += "networks:\n";
        resultado += `  ${nombreSubred}:\n`;
        resultado += "    driver: bridge\n";
        resultado += "    ipam:\n";
        resultado += "      config:\n";
        resultado += `        - subnet: ${subred}\n`;
    }

    document.getElementById('vistaPrevia').textContent = resultado;
}

document.querySelectorAll('.nombre, .imagen, .version, .puertos').forEach(input => {
    input.addEventListener('input', generarVistaPrevia);
});
document.getElementById('subred').addEventListener('input', generarVistaPrevia);
document.getElementById('nombre_subred').addEventListener('input', generarVistaPrevia);

generarVistaPrevia();
</script>

</body>
</html>

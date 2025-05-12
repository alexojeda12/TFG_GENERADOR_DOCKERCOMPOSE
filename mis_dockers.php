<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'conexion.php';

$stmt = $conn->prepare("SELECT id, nombre_contenedor, contenido, fecha_creacion FROM dockers WHERE user_id = :user_id ORDER BY fecha_creacion DESC");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$dockers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Dockers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <a class="nav-link" href="logout.php">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<h1 class="mb-4">Tus Docker Compose Generados</h1>

<?php if (count($dockers) > 0): ?>
    <div class="accordion" id="dockersAccordion">
        <?php foreach ($dockers as $i => $docker): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?= $i ?>">
                    <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $i ?>">
                        <?= htmlspecialchars($docker['nombre_contenedor']) ?> - <?= $docker['fecha_creacion'] ?>
                    </button>
                </h2>
                <div id="collapse<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $i ?>" data-bs-parent="#dockersAccordion">
                    <div class="accordion-body">
                        <pre><?= htmlspecialchars($docker['contenido']) ?></pre>

                        <form action="renombrar_docker.php" method="POST" class="d-inline-block mt-2 me-2">
                            <input type="hidden" name="docker_id" value="<?= $docker['id'] ?>">
                            <div class="input-group">
                                <input type="text" name="nuevo_nombre" class="form-control form-control-sm" placeholder="Nuevo nombre" required>
                                <button type="submit" class="btn btn-primary btn-sm">Renombrar</button>
                            </div>
                        </form>

                        <form action="eliminar_docker.php" method="POST" class="d-inline-block mt-2" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este Docker Compose?');">
                            <input type="hidden" name="docker_id" value="<?= $docker['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">Aún no has generado ningún archivo Docker Compose.</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

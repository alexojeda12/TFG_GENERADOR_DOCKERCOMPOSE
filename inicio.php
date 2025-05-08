<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generador Docker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">DockerGen</a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <span class="nav-link disabled text-white">¡Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mis_dockers.php">Ver mis Dockers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar sesión</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Iniciar sesión</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<h1 class="mb-4">Generador de docker-compose</h1>

<?php if (!isset($_SESSION['user_id'])): ?>
    <div class="alert alert-warning">Debes iniciar sesión para crear contenedores.</div>
<?php else: ?>
    <form method="POST" action="formulario.php" class="mb-3">
        <div class="mb-3">
            <label for="nombre_archivo" class="form-label">Nombre del archivo (sin extensión):</label>
            <input type="text" class="form-control" id="nombre_archivo" name="nombre_archivo" required>
        </div>
        <div class="mb-3">
            <label for="num_contenedores" class="form-label">¿Cuántos contenedores quieres crear?</label>
            <input type="number" min="1" class="form-control" id="num_contenedores" name="num_contenedores" required>
        </div>
        <button type="submit" class="btn btn-primary">Siguiente</button>
    </form>
<?php endif; ?>

</body>
</html>

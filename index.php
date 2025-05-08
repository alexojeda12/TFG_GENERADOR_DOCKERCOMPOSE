<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: formulario.php"); // o donde quieras redirigir si ya está logueado
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a DockerGen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .center-box {
            max-width: 500px;
            margin: 8% auto;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            font-weight: bold;
            margin-bottom: 30px;
        }
        .btn-lg {
            width: 100%;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="center-box">
    <h1>DockerGen</h1>
    <p class="lead">Genera tus archivos Docker Compose fácilmente</p>
    <a href="login.php" class="btn btn-primary btn-lg">Iniciar Sesión</a>
    <a href="registro.php" class="btn btn-outline-secondary btn-lg">Registrarse</a>
</div>
</body>
</html>

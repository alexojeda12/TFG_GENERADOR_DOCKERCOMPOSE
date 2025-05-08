<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'conexion.php'; // Conexión a la base de datos

$nombre = $_POST['nombre_archivo'] ?? 'docker_compose';
$contenedores = $_POST['contenedor'] ?? [];

$subred = trim($_POST['subred'] ?? '');
$nombreSubred = trim($_POST['nombre_subred'] ?? '');
if ($subred !== '' && $nombreSubred === '') {
    $nombreSubred = 'custom_net';
}

$yml = "version: '3'\nservices:\n";

foreach ($contenedores as $c) {
    $nombreContenedor = trim($c['nombre'] ?? '');
    $imagen = trim($c['imagen'] ?? '');
    $version = trim($c['version'] ?? '');
    $puertos = trim($c['puertos'] ?? '');

    if ($nombreContenedor === '' || $imagen === '') {
        continue; // Evita errores por campos vacíos
    }

    $imagenCompleta = $imagen . ':' . ($version !== '' ? $version : 'latest');

    $yml .= "  {$nombreContenedor}:\n";
    $yml .= "    image: {$imagenCompleta}\n";

    if (!empty($puertos)) {
        $yml .= "    ports:\n      - \"{$puertos}\"\n";
    }

    if (!empty($subred)) {
        $yml .= "    networks:\n      - {$nombreSubred}\n";
    }
}

// Si se definió subred, añadir sección de red personalizada
if (!empty($subred)) {
    $yml .= "networks:\n";
    $yml .= "  {$nombreSubred}:\n";
    $yml .= "    driver: bridge\n";
    $yml .= "    ipam:\n";
    $yml .= "      config:\n";
    $yml .= "        - subnet: {$subred}\n";
}

// Guardar en la base de datos
try {
    $stmt = $conn->prepare("INSERT INTO dockers (user_id, nombre_contenedor, contenido) VALUES (:user_id, :nombre, :contenido)");
    $stmt->execute([
        ":user_id" => $_SESSION["user_id"],
        ":nombre" => $nombre,
        ":contenido" => $yml
    ]);
} catch (PDOException $e) {
    die("Error al guardar el Docker Compose: " . $e->getMessage());
}

// Descargar el archivo generado
header('Content-Type: text/plain');
header("Content-Disposition: attachment; filename=docker-compose.yaml");
echo $yml;
exit;
?>

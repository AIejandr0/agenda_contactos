<?php
session_start();

$es_local = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);

if ($es_local) {
    $servidor    = "localhost";
    $base_datos  = "agenda_contactos";
    $usuario_db  = "root";
    $password_db = "";
} else {
    $servidor    = "sql208.infinityfree.com";
    $base_datos  = "if0_43509587_agenda";
    $usuario_db  = "if0_43509587";
    $password_db = "CONTRASEÑA_HOSTING";
}

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$base_datos;charset=utf8mb4", $usuario_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos.");
}

function verificarSesion() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit();
    }
}
?>
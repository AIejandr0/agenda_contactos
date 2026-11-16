<?php
include 'conexion.php';
verificarSesion();

if (isset($_GET['id'])) {
    $id_contacto = (int)$_GET['id'];
    $id_usuario = $_SESSION['usuario_id'];

    $sql = "DELETE FROM contactos WHERE id = ? AND id_usuario = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_contacto, $id_usuario]);
}

header("Location: index.php");
exit();
?>

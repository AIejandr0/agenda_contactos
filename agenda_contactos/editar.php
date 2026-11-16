<?php
include 'conexion.php';
verificarSesion();

$id_usuario = $_SESSION['usuario_id'];
$error = "";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_contacto = (int)$_GET['id'];

$sql = "SELECT * FROM contactos WHERE id = ? AND id_usuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_contacto, $id_usuario]);
$contacto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contacto) {
    die("Error: No tienes permiso para editar este contacto o no existe.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $ciudad = trim($_POST['ciudad']);

    if (empty($nombre) || empty($telefono) || empty($correo) || empty($ciudad)) {
        $error = "Ningun campo puede quedar vacio.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "Ingresa un correo electronico valido.";
    } else {
        $sql_update = "UPDATE contactos SET nombre = ?, telefono = ?, correo = ?, ciudad = ? WHERE id = ? AND id_usuario = ?";
        $stmt_up = $pdo->prepare($sql_update);
        $stmt_up->execute([$nombre, $telefono, $correo, $ciudad, $id_contacto, $id_usuario]);

        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Contaco</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="auth-container">
        <form method="POST">
            <h2>Editar Contacto</h2>

            <?php if ($error != ""): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($contacto['nombre']); ?>" required>

            <label>Teléfono:</label>
            <input type="text" name="telefono" value="<?php echo htmlspecialchars($contacto['telefono']); ?>" required>

            <label>Correo electrónico:</label>
            <input type="email" name="correo" value="<?php echo htmlspecialchars($contacto['correo']); ?>" required>

            <label>Ciudad:</label>
            <input type="text" name="ciudad" value="<?php echo htmlspecialchars($contacto['ciudad']); ?>" required>

            <button type="submit">Actualizar Datos</button>
            <br><br>
            <div style="text-align: center;">
                <a class="btn-link" href="index.php">Cancelar y regresar</a>
            </div>
        </form>
    </div>
</body>
</html>

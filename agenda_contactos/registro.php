<?php
include 'conexion.php';

$mensaje = "";
$registro_ok = false; // variable de prueba

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    if (empty($nombre) || empty($correo) || empty($password)) {
        $mensaje = "Todos los campos son obligatorios.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El formato del correo electronico no es valido.";
    } elseif (strlen($password) < 6) {
        $mensaje = "La contraseña debe tener minimo 6 caracteres.";
    } else {
        $sql_verificar = "SELECT id FROM usuarios WHERE correo = ?";
        $stmt = $pdo->prepare($sql_verificar);
        $stmt->execute([$correo]);

        if ($stmt->rowCount() > 0) {
            $mensaje = "Este correo electronico ya esta registrado.";
        } else {
            $password_encriptada = password_hash($password, PASSWORD_DEFAULT);

            $sql_insertar = "INSERT INTO usuarios (nombre, correo, password) VALUES (?, ?, ?)";
            $stmt_insert = $pdo->prepare($sql_insertar);
            
            if ($stmt_insert->execute([$nombre, $correo, $password_encriptada])) {
                header("Location: login.php?registrado=1");
                exit();
            } else {
                $mensaje = "Hubo un problema al registrar el usuario.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Agenda de Contactos</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="auth-container">
        <form method="POST" action="registro.php">
            <h2>Registro de Usuario</h2>
            
            <?php if ($mensaje != ""): ?>
                <div class="error"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>

            <label>Nombre completo:</label>
            <input type="text" name="nombre" placeholder="Ej: Juan Perez" required>

            <label>Correo electrónico:</label>
            <input type="email" name="correo" placeholder="correo@ejemplo.com" required>

            <label>Contraseña:</label>
            <input type="password" name="password" placeholder="Mínimo 6 caracteres" required>

            <button type="submit">Registrarse</button>
            <br><br>
            <div style="text-align: center;">
                <a class="btn-link" href="login.php">¿Ya tienes cuenta? Inicia sesión aquí</a>
            </div>
        </form>
    </div>
</body>
</html>

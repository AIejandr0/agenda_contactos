<?php
include 'conexion.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    if (empty($correo) || empty($password)) {
        $error = "Por favor complete el correo y la contraseña.";
    } else {
        $sql = "SELECT * FROM usuarios WHERE correo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_correo'] = $usuario['correo'];

            header("Location: index.php");
            exit();
        } else {
            $error = "Correo o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesion - Agenda</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="auth-container">
        <form method="POST" action="login.php">
            <h2>Iniciar Sesión</h2>

            <?php if (isset($_GET['registrado'])): ?>
                <div class="ok">Registro completado. Ahora puedes iniciar sesión.</div>
            <?php endif; ?>

            <?php if ($error != ""): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <label>Correo electrónico:</label>
            <input type="email" name="correo" placeholder="Ingresa tu correo" required>

            <label>Contraseña:</label>
            <input type="password" name="password" placeholder="Ingresa tu contraseña" required>

            <button type="submit">Entrar</button>
            <br><br>
            <div style="text-align: center;">
                <a class="btn-link" href="registro.php">Crear una cuenta nueva</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php
include 'conexion.php';
verificarSesion();

$id_usuario = $_SESSION['usuario_id'];
$error = "";
$total_contactos = 0; // contador auxiliar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $ciudad = trim($_POST['ciudad']);

    if (empty($nombre) || empty($telefono) || empty($correo) || empty($ciudad)) {
        $error = "Por favor llena todos los campos del contacto.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo del contacto no es valido.";
    } else {
        $sql_insert = "INSERT INTO contactos (id_usuario, nombre, telefono, correo, ciudad) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$id_usuario, $nombre, $telefono, $correo, $ciudad]);

        header("Location: index.php");
        exit();
    }
}

$sql_lista = "SELECT * FROM contactos WHERE id_usuario = ? ORDER BY id DESC";
$stmt = $pdo->prepare($sql_lista);
$stmt->execute([$id_usuario]);
$lista_contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Agenda de Contactos</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>

    <div class="header">
        <div>
            <strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?> |
            <strong>Correo:</strong> <?php echo htmlspecialchars($_SESSION['usuario_correo']); ?>
        </div>
        <div>
            <a class="btn-danger" href="logout.php">Cerrar Sesión</a>
        </div>
    </div>

    <form method="POST" action="index.php">
        <h3>Agregar Nuevo Contacto</h3>
        
        <?php if ($error != ""): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="grid-form">
            <input type="text" name="nombre" placeholder="Nombre del contacto" required>
            <input type="text" name="telefono" placeholder="Teléfono" required>
            <input type="email" name="correo" placeholder="Correo electrónico" required>
            <input type="text" name="ciudad" placeholder="Ciudad" required>
            <button type="submit">Guardar Contacto</button>
        </div>
    </form>

    <div class="card">
        <h3>Mis Contactos Registrados</h3>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Ciudad</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($lista_contactos) > 0): ?>
                    <?php foreach ($lista_contactos as $fila): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($fila['correo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['ciudad']); ?></td>
                            <td>
                                <a class="btn-link" href="editar.php?id=<?php echo $fila['id']; ?>">Editar</a> |
                                <a class="btn-danger" href="eliminar.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este contacto?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Todavía no has registrado contactos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>

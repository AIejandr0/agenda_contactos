<?php
include 'conexion.php';
verificarSesion();

$id_usuario = $_SESSION['usuario_id'];
$error = "";

// Agregar nuevo contacto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $correo   = trim($_POST['correo']);
    $ciudad   = trim($_POST['ciudad']);
    $favorito = (isset($_POST['favorito']) && $_POST['favorito'] == '1') ? 1 : 0;

    if (empty($nombre) || empty($telefono) || empty($correo) || empty($ciudad)) {
        $error = "Por favor llena todos los campos del contacto.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo del contacto no es valido.";
    } else {
        $sql_insert = "INSERT INTO contactos (id_usuario, nombre, telefono, correo, ciudad, favorito) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->execute([$id_usuario, $nombre, $telefono, $correo, $ciudad, $favorito]);

        header("Location: index.php");
        exit();
    }
}

// Alternar estado de favorito (Sí / No) desde la tabla
if (isset($_GET['toggle_fav'])) {
    $id_fav = (int)$_GET['toggle_fav'];
    $sql_fav = "UPDATE contactos SET favorito = NOT favorito WHERE id = ? AND id_usuario = ?";
    $stmt_fav = $pdo->prepare($sql_fav);
    $stmt_fav->execute([$id_fav, $id_usuario]);

    header("Location: index.php");
    exit();
}

// Parámetros GET para Búsqueda (A), Ordenamiento (B) y Solo Favoritos (C)
$buscar         = trim($_GET['buscar'] ?? '');
$orden          = $_GET['orden'] ?? 'fecha_desc';
$solo_favoritos = (isset($_GET['solo_favoritos']) && $_GET['solo_favoritos'] == '1');

$ordenes_validos = [
    'fecha_desc'  => 'fecha_creacion DESC, id DESC',
    'fecha_asc'   => 'fecha_creacion ASC, id ASC',
    'nombre_asc'  => 'nombre ASC',
    'nombre_desc' => 'nombre DESC',
    'ciudad_asc'  => 'ciudad ASC',
    'ciudad_desc' => 'ciudad DESC',
];
$order_by = $ordenes_validos[$orden] ?? 'fecha_creacion DESC, id DESC';

// Consulta filtrada y ordenada
$sql_lista = "SELECT * FROM contactos WHERE id_usuario = ?";
$params = [$id_usuario];

if ($solo_favoritos) {
    $sql_lista .= " AND favorito = 1";
}

if ($buscar !== '') {
    $sql_lista .= " AND (nombre LIKE ? OR correo LIKE ? OR ciudad LIKE ?)";
    $termino = "%$buscar%";
    $params[] = $termino;
    $params[] = $termino;
    $params[] = $termino;
}

$sql_lista .= " ORDER BY $order_by";

$stmt = $pdo->prepare($sql_lista);
$stmt->execute($params);
$lista_contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// URL para alternar vista de solo favoritos manteniendo búsqueda y orden
$query_fav = [
    'buscar' => $buscar,
    'orden'  => $orden,
];
if (!$solo_favoritos) {
    $query_fav['solo_favoritos'] = '1';
}
$url_favoritos = 'index.php?' . http_build_query(array_filter($query_fav));
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
            <select name="favorito" style="grid-column: span 2;">
                <option value="0">Favorito: No</option>
                <option value="1">Favorito: Sí</option>
            </select>
            <button type="submit">Guardar Contacto</button>
        </div>
    </form>

    <div class="card">
        <h3>Mis Contactos Registrados</h3>

        <!-- Barra de Búsqueda, Ordenamiento y Filtro de Favoritos -->
        <form method="GET" action="index.php" class="filter-bar">
            <?php if ($solo_favoritos): ?>
                <input type="hidden" name="solo_favoritos" value="1">
            <?php endif; ?>

            <input type="text" name="buscar" value="<?php echo htmlspecialchars($buscar); ?>" placeholder="Buscar por nombre, correo o ciudad...">

            <select name="orden" onchange="this.form.submit()">
                <option value="fecha_desc"  <?php echo $orden == 'fecha_desc'  ? 'selected' : ''; ?>>Fecha: más recientes</option>
                <option value="fecha_asc"   <?php echo $orden == 'fecha_asc'   ? 'selected' : ''; ?>>Fecha: más antiguos</option>
                <option value="nombre_asc"  <?php echo $orden == 'nombre_asc'  ? 'selected' : ''; ?>>Nombre (A - Z)</option>
                <option value="nombre_desc" <?php echo $orden == 'nombre_desc' ? 'selected' : ''; ?>>Nombre (Z - A)</option>
                <option value="ciudad_asc"  <?php echo $orden == 'ciudad_asc'  ? 'selected' : ''; ?>>Ciudad (A - Z)</option>
                <option value="ciudad_desc" <?php echo $orden == 'ciudad_desc' ? 'selected' : ''; ?>>Ciudad (Z - A)</option>
            </select>

            <button type="submit">Buscar</button>

            <a class="btn-filter <?php echo $solo_favoritos ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($url_favoritos); ?>">
                <?php echo $solo_favoritos ? '★ Solo favoritos' : '☆ Ver favoritos'; ?>
            </a>

            <?php if ($buscar !== '' || $orden !== 'fecha_desc' || $solo_favoritos): ?>
                <a class="btn-filter" href="index.php">Limpiar</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Favorito</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Ciudad</th>
                    <th>Fecha</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($lista_contactos) > 0): ?>
                    <?php foreach ($lista_contactos as $fila): ?>
                        <tr>
                            <td>
                                <a class="<?php echo $fila['favorito'] ? 'fav-si' : 'fav-no'; ?>" href="index.php?toggle_fav=<?php echo $fila['id']; ?>" title="Clic para cambiar">
                                    <?php echo $fila['favorito'] ? 'Sí' : 'No'; ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($fila['correo']); ?></td>
                            <td><?php echo htmlspecialchars($fila['ciudad']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($fila['fecha_creacion'])); ?></td>
                            <td>
                                <a class="btn-link" href="editar.php?id=<?php echo $fila['id']; ?>">Editar</a> |
                                <a class="btn-danger" href="eliminar.php?id=<?php echo $fila['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este contacto?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No se encontraron contactos con los filtros aplicados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
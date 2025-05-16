<?php
include('conexion.php'); // Asegúrate de incluir el archivo de conexión a la base de datos

// Si el formulario es enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_paciente'])) {
    $turno_id = $_POST['turno_id']; // ID del turno que el usuario seleccionó
    $nombre_paciente = $_POST['nombre_paciente']; // Nombre del paciente

    // Verificar si el turno está disponible
    $query = "SELECT estado FROM turnos WHERE id = :turno_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':turno_id', $turno_id);
    $stmt->execute();
    $turno = $stmt->fetch();

    if ($turno && $turno['estado'] == 'disponible') {
        // Actualizar el estado del turno a 'ocupado'
        $update_query = "UPDATE turnos SET estado = 'ocupado', nombre_paciente = :nombre_paciente WHERE id = :turno_id";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->bindParam(':nombre_paciente', $nombre_paciente);
        $update_stmt->bindParam(':turno_id', $turno_id);
        $update_stmt->execute();

        $mensaje = "¡Turno reservado con éxito!";
    } else {
        $mensaje = "El turno ya está ocupado o no existe.";
    }
}

// Obtener la fecha seleccionada (si no se seleccionó una, usamos la fecha actual)
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Consulta para obtener los turnos disponibles para la fecha seleccionada
$query_turnos = "
    SELECT t.id, t.fecha, t.hora, t.estado, m.nombre AS medico_nombre, m.apellido AS medico_apellido, e.nombre AS especialidad
    FROM turnos t
    JOIN medicos m ON t.medico_id = m.id
    JOIN especialidades e ON m.especialidad_id = e.id
    WHERE t.fecha = :fecha AND t.estado = 'disponible'
    ORDER BY t.hora
";
$stmt_turnos = $pdo->prepare($query_turnos);
$stmt_turnos->bindParam(':fecha', $fecha);
$stmt_turnos->execute();
$turnos = $stmt_turnos->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Turno</title>
    <link rel="stylesheet" href="styles.css"> <!-- Puedes agregar tu archivo de estilos -->
</head>
<body>

    <div style="width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2>Reservar turno</h2>

        <!-- Formulario para elegir la fecha -->
        <form method="GET" action="reservar_turno.php" style="margin-bottom: 20px;">
            <label for="fecha">Seleccionar Fecha: </label>
            <input type="date" name="fecha" value="<?php echo $fecha; ?>" required>
            <input type="submit" value="Ver turnos">
        </form>

        <!-- Mostrar mensaje después de procesar el formulario -->
        <?php if (isset($mensaje)) { ?>
            <div style="color: #D8000C; background-color: #FFBABA; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
                <?php echo $mensaje; ?>
            </div>
        <?php } ?>

        <?php if (count($turnos) > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Médico</th>
                        <th>Especialidad</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($turnos as $turno): ?>
                        <tr>
                            <td><?php echo $turno['hora']; ?></td>
                            <td><?php echo $turno['medico_nombre'] . ' ' . $turno['medico_apellido']; ?></td>
                            <td><?php echo $turno['especialidad']; ?></td>
                            <td>
                                <form method="POST" action="reservar_turno.php">
                                    <input type="hidden" name="turno_id" value="<?php echo $turno['id']; ?>">
                                    <input type="text" name="nombre_paciente" placeholder="Tu nombre" required>
                                    <input type="submit" value="Reservar" style="padding: 5px 10px; background-color: #007bff; color: white; border: none; border-radius: 5px;">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay turnos disponibles para esta fecha.</p>
        <?php endif; ?>
    </div>

</body>
</html>

<?php
include('conexion.php'); // Conexión a la base de datos

// Si el formulario para reservar turno es enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_paciente'])) {
    $turno_id = $_POST['turno_id']; // ID del turno seleccionado
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

// Obtener todos los especialistas (médicos)
$query_medicos = "
    SELECT m.id, m.nombre, m.apellido, e.nombre AS especialidad
    FROM medicos m
    JOIN especialidades e ON m.especialidad_id = e.id
    ORDER BY m.nombre";
$stmt_medicos = $pdo->prepare($query_medicos);
$stmt_medicos->execute();
$medicos = $stmt_medicos->fetchAll();

// Obtener los turnos del especialista seleccionado
$turnos = [];
if (isset($_GET['medico_id'])) {
    $medico_id = $_GET['medico_id'];
    
    // Consulta para obtener los turnos disponibles para ese especialista
    $query_turnos = "
        SELECT t.id, t.fecha, t.hora, t.estado
        FROM turnos t
        WHERE t.medico_id = :medico_id AND t.estado = 'disponible'
        ORDER BY t.fecha, t.hora";
    $stmt_turnos = $pdo->prepare($query_turnos);
    $stmt_turnos->bindParam(':medico_id', $medico_id);
    $stmt_turnos->execute();
    $turnos = $stmt_turnos->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Turno por Especialista</title>
    <link rel="stylesheet" href="styles.css"> <!-- Puedes agregar tu archivo de estilos -->
</head>
<body>

    <div style="width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2>Reservar Turno con Especialista</h2>
        
        <!-- Seleccionar especialista -->
        <form method="GET" action="reservar_por_especialista.php" style="margin-bottom: 20px;">
            <label for="medico_id">Seleccionar Especialista: </label>
            <select name="medico_id" id="medico_id" required>
                <option value="">Seleccione un especialista</option>
                <?php foreach ($medicos as $medico): ?>
                    <option value="<?php echo $medico['id']; ?>"><?php echo $medico['nombre'] . ' ' . $medico['apellido'] . ' - ' . $medico['especialidad']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Ver Turnos">
        </form>

        <!-- Mostrar mensaje después de procesar el formulario -->
        <?php if (isset($mensaje)) { ?>
            <div style="color: #D8000C; background-color: #FFBABA; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
                <?php echo $mensaje; ?>
            </div>
        <?php } ?>

        <!-- Si se ha seleccionado un especialista, mostrar los turnos disponibles -->
        <?php if (count($turnos) > 0): ?>
            <h3>Turnos Disponibles</h3>
            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($turnos as $turno): ?>
                        <tr>
                            <td><?php echo $turno['fecha']; ?></td>
                            <td><?php echo $turno['hora']; ?></td>
                            <td>
                                <form method="POST" action="reservar_por_especialista.php">
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
            <p>No hay turnos disponibles para este especialista.</p>
        <?php endif; ?>
    </div>

</body>
</html>

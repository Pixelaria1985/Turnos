<?php
include('conexion.php'); // Asegúrate de incluir el archivo de conexión a la base de datos

// Verificar si se ha enviado la fecha a través de un formulario o URL
if (isset($_GET['fecha'])) {
    $fecha = $_GET['fecha'];
} else {
    // Si no se ha enviado una fecha, asignamos la fecha actual
    $fecha = date('Y-m-d');
}

// Consulta SQL para obtener los turnos de un día específico con la información del médico
$query = "
    SELECT t.id, t.fecha, t.hora, t.estado, m.nombre AS medico_nombre, m.apellido AS medico_apellido, e.nombre AS especialidad
    FROM turnos t
    JOIN medicos m ON t.medico_id = m.id
    JOIN especialidades e ON m.especialidad_id = e.id
    WHERE t.fecha = :fecha
    ORDER BY t.hora
";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':fecha', $fecha);
$stmt->execute();

// Obtener los resultados
$turnos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mostrar Turnos</title>
    <link rel="stylesheet" href="styles.css"> <!-- Puedes agregar tu archivo de estilos -->
</head>
<body>

    <div style="width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2>Turnos del día <?php echo $fecha; ?></h2>
        
        <form method="GET" action="mostrar_turnos.php">
            <label for="fecha">Seleccionar fecha:</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>" required>
            <input type="submit" value="Buscar turnos" style="padding: 5px 10px; background-color: #007bff; color: white; border: none; border-radius: 5px;">
        </form>

        <?php if (count($turnos) > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Médico</th>
                        <th>Especialidad</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($turnos as $turno): ?>
                        <tr>
                            <td><?php echo $turno['hora']; ?></td>
                            <td><?php echo $turno['medico_nombre'] . ' ' . $turno['medico_apellido']; ?></td>
                            <td><?php echo $turno['especialidad']; ?></td>
                            <td><?php echo $turno['estado']; ?></td>
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

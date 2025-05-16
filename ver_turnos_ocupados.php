<?php
include('conexion.php'); // Asegúrate de incluir el archivo de conexión a la base de datos

// Consulta SQL para obtener los turnos ocupados
$query = "
    SELECT t.id, t.fecha, t.hora, t.estado, m.nombre AS medico_nombre, m.apellido AS medico_apellido, e.nombre AS especialidad, t.nombre_paciente
    FROM turnos t
    JOIN medicos m ON t.medico_id = m.id
    JOIN especialidades e ON m.especialidad_id = e.id
    WHERE t.estado = 'ocupado'
    ORDER BY t.fecha, t.hora
";
$stmt = $pdo->prepare($query);
$stmt->execute();

// Obtener los resultados
$turnos_ocupados = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos Ocupados</title>
    <link rel="stylesheet" href="styles.css"> <!-- Puedes agregar tu archivo de estilos -->
</head>
<body>

    <div style="width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2>Turnos Ocupados</h2>
        
        <?php if (count($turnos_ocupados) > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Médico</th>
                        <th>Especialidad</th>
                        <th>Paciente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($turnos_ocupados as $turno): ?>
                        <tr>
                            <td><?php echo $turno['fecha']; ?></td>
                            <td><?php echo $turno['hora']; ?></td>
                            <td><?php echo $turno['medico_nombre'] . ' ' . $turno['medico_apellido']; ?></td>
                            <td><?php echo $turno['especialidad']; ?></td>
                            <td><?php echo $turno['nombre_paciente']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay turnos ocupados actualmente.</p>
        <?php endif; ?>
    </div>

</body>
</html>

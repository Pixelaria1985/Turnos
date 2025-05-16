<?php
include('conexion.php'); // Conexión a la base de datos

// Si el formulario es enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $medico_id = $_POST['medico_id']; // Obtener el médico seleccionado

    // Verificar si el turno ya existe para esa fecha, hora y médico
    $query = "SELECT * FROM turnos WHERE fecha = :fecha AND hora = :hora AND medico_id = :medico_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':hora', $hora);
    $stmt->bindParam(':medico_id', $medico_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Si el turno ya está ocupado para ese médico
        $mensaje = "¡Este turno ya está ocupado para el médico seleccionado!";
    } else {
        // Insertar nuevo turno como disponible con el médico seleccionado
        $insert_query = "INSERT INTO turnos (fecha, hora, estado, medico_id) VALUES (:fecha, :hora, 'disponible', :medico_id)";
        $insert_stmt = $pdo->prepare($insert_query);
        $insert_stmt->bindParam(':fecha', $fecha);
        $insert_stmt->bindParam(':hora', $hora);
        $insert_stmt->bindParam(':medico_id', $medico_id);
        $insert_stmt->execute();

        // Mensaje de éxito
        $mensaje = "¡El turno ha sido cargado con éxito para el médico seleccionado!";
    }
}

// Obtener lista de médicos para el desplegable
$query_medicos = "SELECT m.id, m.nombre, m.apellido, e.nombre AS especialidad FROM medicos m JOIN especialidades e ON m.especialidad_id = e.id";
$stmt_medicos = $pdo->prepare($query_medicos);
$stmt_medicos->execute();
$medicos = $stmt_medicos->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Turnos Disponibles</title>
    <link rel="stylesheet" href="styles.css"> <!-- Puedes agregar tu archivo de estilos -->
</head>
<body>

    <div style="width: 50%; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2>Cargar nuevo turno disponible</h2>

        <!-- Mostrar mensaje después de procesar el formulario -->
        <?php if (isset($mensaje)) { ?>
            <div style="color: #D8000C; background-color: #FFBABA; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
                <?php echo $mensaje; ?>
            </div>
        <?php } ?>

        <!-- Formulario para ingresar nuevo turno -->
        <form method="POST" action="cargar_turnos.php">
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" required>
            <br><br>
            
            <label for="hora">Hora:</label>
            <input type="time" id="hora" name="hora" required>
            <br><br>
            
            <label for="medico_id">Médico:</label>
            <select id="medico_id" name="medico_id" required>
                <option value="">Selecciona un médico</option>
                <?php foreach ($medicos as $medico) { ?>
                    <option value="<?php echo $medico['id']; ?>"><?php echo $medico['nombre'] . ' ' . $medico['apellido'] . ' - ' . $medico['especialidad']; ?></option>
                <?php } ?>
            </select>
            <br><br>
            
            <input type="submit" value="Cargar turno" style="padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px;">
        </form>
    </div>

</body>
</html>

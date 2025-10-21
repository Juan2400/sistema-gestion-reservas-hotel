<?php
try {
    require 'conexion_bd.php';

    // Establecer la zona horaria correcta
    date_default_timezone_set('America/Lima');
    $fecha_hora_actual = date('Y-m-d H:i:s');

    // Consulta base para todas las reservas
    $base_query = "SELECT r.id_reserva, u.nombres, r.fecha_inicio, r.fecha_fin, te.tipo as tipo_estadia, 
                          r.codigo_alfanumerico, r.confirmada, r.completada, h.numero_habitacion 
                   FROM Reserva r
                   JOIN Usuario u ON r.id_usuario = u.id_usuario
                   JOIN Habitacion h ON r.id_habitacion = h.id_habitacion
                   JOIN TipoEstadia te ON r.id_tipo_estadia = te.id_tipo_estadia";

    // Actualizar el estado de la habitación si la reserva está confirmada y completada
    $sql_actualizar_estado = "UPDATE Habitacion h
                              JOIN Reserva r ON h.id_habitacion = r.id_habitacion
                              SET h.id_estado_habitacion = (SELECT id_estado_habitacion FROM EstadoHabitacion WHERE estado = 'disponible')
                              WHERE r.confirmada = 1 AND r.completada = 1 
                              AND h.id_estado_habitacion = (SELECT id_estado_habitacion FROM EstadoHabitacion WHERE estado = 'ocupada')";
    $stmt_actualizar_estado = $conn->prepare($sql_actualizar_estado);
    $stmt_actualizar_estado->execute();

    // Consultas para diferentes tipos de reservas
    $sql_reservas_futuras = $base_query . " WHERE r.fecha_inicio >= ? AND r.confirmada = 0 AND r.completada = 0 ORDER BY r.fecha_inicio ASC";
    $sql_atendiendo = $base_query . " WHERE r.fecha_inicio <= ? AND r.fecha_fin >= ? AND r.confirmada = 1 AND r.completada = 0 ORDER BY r.fecha_inicio ASC";
    $sql_historial = $base_query . " WHERE r.confirmada = 1 AND r.completada = 1 ORDER BY r.fecha_fin DESC";
    $sql_sin_atender = $base_query . " WHERE r.fecha_fin < ? AND r.confirmada = 0 AND r.completada = 0 ORDER BY r.fecha_fin DESC";

    // Preparar y ejecutar las consultas
    $stmt_futuras = $conn->prepare($sql_reservas_futuras);
    $stmt_futuras->execute([$fecha_hora_actual]);
    $reservas_futuras = $stmt_futuras->fetchAll(PDO::FETCH_ASSOC);

    $stmt_atendiendo = $conn->prepare($sql_atendiendo);
    $stmt_atendiendo->execute([$fecha_hora_actual, $fecha_hora_actual]);
    $reservas_atendiendo = $stmt_atendiendo->fetchAll(PDO::FETCH_ASSOC);

    $stmt_historial = $conn->prepare($sql_historial);
    $stmt_historial->execute();
    $reservas_historial = $stmt_historial->fetchAll(PDO::FETCH_ASSOC);

    $stmt_sin_atender = $conn->prepare($sql_sin_atender);
    $stmt_sin_atender->execute([$fecha_hora_actual]);
    $reservas_sin_atender = $stmt_sin_atender->fetchAll(PDO::FETCH_ASSOC);

    // Búsqueda por código alfanumérico o DNI
    if (isset($_POST['busqueda']) && !empty($_POST['busqueda'])) {
        $busqueda = $_POST['busqueda'];
        $sql_busqueda = $base_query . " WHERE r.codigo_alfanumerico = ? OR u.dni = ?";
        $stmt_busqueda = $conn->prepare($sql_busqueda);
        $stmt_busqueda->execute([$busqueda, $busqueda]);
        $resultados_busqueda = $stmt_busqueda->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die('Error en la base de datos: ' . $e->getMessage());
} finally {
    $conn = null;
}

// El resto del código HTML permanece sin cambios
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas - Hotel</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles_admin.css">
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'administradorHeader.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4 text-center">Gestión de Reservas</h1>

        <!-- Formulario de búsqueda -->
        <form method="POST" action="" class="mb-4">
            <div class="form-group">
                <label for="busqueda">Buscar por Código Alfanumérico o DNI:</label>
                <input type="text" class="form-control" id="busqueda" name="busqueda">
            </div>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        <!-- Botones de navegación -->
        <div class="mb-4">
            <button onclick="mostrarSeccion('reservas-futuras')" class="btn btn-info mr-2">Reservas Futuras</button>
            <button onclick="mostrarSeccion('reservas-atendiendo')" class="btn btn-warning mr-2">Reservas Atendiendo</button>
            <button onclick="mostrarSeccion('reservas-sin-atender')" class="btn btn-danger mr-2">Reservas Sin Atender</button>
            <button onclick="mostrarSeccion('historial-reservas')" class="btn btn-secondary">Historial de Reservas</button>
        </div>

        <!-- Función para generar la tabla de reservas -->
        <?php
        function generarTablaReservas($reservas)
        {
            echo '<div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Reserva</th>
                                <th>Nombre Usuario</th>
                                <th>Fecha de Inicio</th>
                                <th>Fecha de Fin</th>
                                <th>Tipo de Estadia</th>
                                <th>Código Alfanumérico</th>
                                <th>Número Habitación</th>
                                <th>Confirmada</th>
                                <th>Completada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($reservas as $reserva) {
                echo '<tr>
                        <td>' . htmlspecialchars($reserva['id_reserva']) . '</td>
                        <td>' . htmlspecialchars($reserva['nombres']) . '</td>
                        <td>' . htmlspecialchars($reserva['fecha_inicio']) . '</td>
                        <td>' . htmlspecialchars($reserva['fecha_fin']) . '</td>
                        <td>' . htmlspecialchars($reserva['tipo_estadia']) . '</td>
                        <td>' . htmlspecialchars($reserva['codigo_alfanumerico']) . '</td>
                        <td>' . htmlspecialchars($reserva['numero_habitacion']) . '</td>
                        <td>' . (htmlspecialchars($reserva['confirmada']) ? 'Sí' : 'No') . '</td>
                        <td>' . (htmlspecialchars($reserva['completada']) ? 'Sí' : 'No') . '</td>
                        <td>
                            <a href="editar_reserva.php?id=' . $reserva['id_reserva'] . '" class="btn btn-sm btn-primary">Editar</a>
                            <a href="eliminar_reserva.php?id=' . $reserva['id_reserva'] . '" class="btn btn-sm btn-danger" onclick="showConfirmModal(event, this.href)">Eliminar</a>
                        </td>
                    </tr>';
            }
            echo '</tbody></table></div>';
        }
        ?>

        <!-- Resultados de la Búsqueda -->
        <?php if (isset($resultados_busqueda)) : ?>
            <div id="resultados-busqueda" class="mb-5">
                <h2 class="mb-4 text-center">Resultados de la Búsqueda</h2>
                <?php generarTablaReservas($resultados_busqueda); ?>
            </div>
        <?php endif; ?>

        <!-- Reservas Futuras -->
        <div id="reservas-futuras" class="mb-5 hidden">
            <h2 class="mb-4 text-center">Reservas Futuras</h2>
            <?php generarTablaReservas($reservas_futuras); ?>
        </div>

        <!-- Reservas Atendiendo -->
        <div id="reservas-atendiendo" class="mb-5 hidden">
            <h2 class="mb-4 text-center">Reservas Atendiendo</h2>
            <?php generarTablaReservas($reservas_atendiendo); ?>
        </div>

        <!-- Reservas Sin Atender -->
        <div id="reservas-sin-atender" class="mb-5 hidden">
            <h2 class="mb-4 text-center">Reservas Sin Atender</h2>
            <?php generarTablaReservas($reservas_sin_atender); ?>
        </div>

        <!-- Historial de Reservas -->
        <div id="historial-reservas" class="mb-5 hidden">
            <h2 class="mb-4 text-center">Historial de Reservas</h2>
            <?php generarTablaReservas($reservas_historial); ?>
        </div>

        <!-- Modal de Confirmación -->
        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Confirmar Acción</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas eliminar esta reserva?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <a href="#" id="confirmActionBtn" class="btn btn-primary">Confirmar</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function mostrarSeccion(id) {
            // Ocultar todas las secciones
            document.getElementById('reservas-futuras').classList.add('hidden');
            document.getElementById('reservas-atendiendo').classList.add('hidden');
            document.getElementById('reservas-sin-atender').classList.add('hidden');
            document.getElementById('historial-reservas').classList.add('hidden');

            // Mostrar la sección seleccionada
            document.getElementById(id).classList.remove('hidden');
        }

        function showConfirmModal(event, url) {
            event.preventDefault();
            const confirmActionBtn = document.getElementById('confirmActionBtn');
            confirmActionBtn.href = url; // Set the URL to the modal confirm button
            $('#confirmModal').modal('show'); // Show the modal
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

</body>

</html>
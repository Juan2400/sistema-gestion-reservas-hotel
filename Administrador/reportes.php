<?php
try {
    require 'conexion_bd.php';

    // Consulta para obtener todas las habitaciones ordenadas de la más reservada a la menos reservada
    $sql_habitaciones_frecuentes = "
        SELECT h.numero_habitacion, COUNT(r.id_habitacion) AS total_reservas
        FROM Reserva r
        JOIN Habitacion h ON r.id_habitacion = h.id_habitacion
        GROUP BY r.id_habitacion
        ORDER BY total_reservas DESC";
    $stmt_habitaciones_frecuentes = $conn->prepare($sql_habitaciones_frecuentes);
    $stmt_habitaciones_frecuentes->execute();
    $habitaciones_frecuentes = $stmt_habitaciones_frecuentes->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener todos los clientes ordenados del que más reservó al que menos
    $sql_clientes_frecuentes = "
        SELECT u.nombres, COUNT(r.id_usuario) AS total_reservas
        FROM Reserva r
        JOIN Usuario u ON r.id_usuario = u.id_usuario
        GROUP BY r.id_usuario
        ORDER BY total_reservas DESC";
    $stmt_clientes_frecuentes = $conn->prepare($sql_clientes_frecuentes);
    $stmt_clientes_frecuentes->execute();
    $clientes_frecuentes = $stmt_clientes_frecuentes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error en la base de datos: ' . $e->getMessage());
} finally {
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Reservas - Hotel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles_admin.css">
</head>

<body>

    <?php include 'administradorHeader.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Estadísticas de Reservas</h2>

        <!-- Menú de opciones -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="habitaciones-tab" data-toggle="tab" href="#habitaciones" role="tab">Habitaciones Más Frecuentes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="clientes-tab" data-toggle="tab" href="#clientes" role="tab">Clientes Más Frecuentes</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Habitaciones Más Frecuentes -->
            <div class="tab-pane fade show active" id="habitaciones" role="tabpanel">
                <h3 class="mt-4">Habitaciones Más Reservadas</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Número de Habitación</th>
                            <th>Total de Reservas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($habitaciones_frecuentes as $habitacion): ?>
                            <tr>
                                <td><?= htmlspecialchars($habitacion['numero_habitacion']) ?></td>
                                <td><?= htmlspecialchars($habitacion['total_reservas']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Clientes Más Frecuentes -->
            <div class="tab-pane fade" id="clientes" role="tabpanel">
                <h3 class="mt-4">Clientes con Más Reservas</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre del Cliente</th>
                            <th>Total de Reservas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes_frecuentes as $cliente): ?>
                            <tr>
                                <td><?= htmlspecialchars($cliente['nombres']) ?></td>
                                <td><?= htmlspecialchars($cliente['total_reservas']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
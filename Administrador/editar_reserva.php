<?php
try {
    require 'conexion_bd.php';

    // Obtener ID de reserva de la URL
    $id_reserva = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Obtener datos de la reserva para editar
    if ($id_reserva > 0) {
        $sql = "SELECT r.id_reserva, r.fecha_inicio, r.fecha_fin, te.id_tipo_estadia, te.tipo as tipo_estadia, 
                       r.codigo_alfanumerico, r.confirmada, r.completada, h.id_habitacion, h.numero_habitacion
                FROM Reserva r
                JOIN TipoEstadia te ON r.id_tipo_estadia = te.id_tipo_estadia
                JOIN Habitacion h ON r.id_habitacion = h.id_habitacion
                WHERE r.id_reserva = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_reserva]);
        $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener todos los tipos de estadía
    $sql_tipos_estadia = "SELECT id_tipo_estadia, tipo FROM TipoEstadia";
    $stmt_tipos_estadia = $conn->query($sql_tipos_estadia);
    $tipos_estadia = $stmt_tipos_estadia->fetchAll(PDO::FETCH_ASSOC);

    // Actualizar reserva
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $id_tipo_estadia = $_POST['id_tipo_estadia'];
        $codigo_alfanumerico = $_POST['codigo_alfanumerico'];
        $confirmada = isset($_POST['confirmada']) ? 1 : 0;
        $completada = isset($_POST['completada']) ? 1 : 0;
        $id_habitacion = $reserva['id_habitacion']; // ID de la habitación asociada

        // Actualizar la reserva en la base de datos
        $sql_update = "UPDATE Reserva
                       SET fecha_inicio = ?, fecha_fin = ?, id_tipo_estadia = ?, 
                           codigo_alfanumerico = ?, confirmada = ?, completada = ?
                       WHERE id_reserva = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->execute([$fecha_inicio, $fecha_fin, $id_tipo_estadia, $codigo_alfanumerico, $confirmada, $completada, $id_reserva]);

        // Verificar si tanto confirmada como completada son 0
        if ($confirmada == 0 && $completada == 0) {
            // Cambiar el estado de la habitación a "ocupada" (id_estado_habitacion = 2)
            $sql_update_habitacion = "UPDATE Habitacion 
                                      SET id_estado_habitacion = 2 
                                      WHERE id_habitacion = ?";
            $stmt_update_habitacion = $conn->prepare($sql_update_habitacion);
            $stmt_update_habitacion->execute([$id_habitacion]);
        }

        header('Location: administradorReservas.php');
        exit();
    }
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
    <title>Editar Reserva</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Editar Reserva</h2>
        <?php if ($reserva) : ?>
            <form id="reservaForm" method="POST" action="">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($reserva['fecha_inicio']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="fecha_fin">Fecha de Fin:</label>
                    <input type="datetime-local" class="form-control" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($reserva['fecha_fin']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="tipo_estadia">Tipo de Estadia:</label>
                    <select class="form-control" id="tipo_estadia" name="id_tipo_estadia" required>
                        <?php foreach ($tipos_estadia as $tipo_estadia) : ?>
                            <option value="<?= $tipo_estadia['id_tipo_estadia'] ?>" <?= $reserva['id_tipo_estadia'] == $tipo_estadia['id_tipo_estadia'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tipo_estadia['tipo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="codigo_alfanumerico">Código Alfanumérico:</label>
                    <input type="text" class="form-control" id="codigo_alfanumerico" name="codigo_alfanumerico" value="<?= htmlspecialchars($reserva['codigo_alfanumerico']) ?>" required>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="confirmada" name="confirmada" <?= $reserva['confirmada'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="confirmada">Confirmada</label>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="completada" name="completada" <?= $reserva['completada'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="completada">Completada</label>
                </div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">Actualizar Reserva</button>
            </form>
        <?php else : ?>
            <p class="text-danger">Reserva no encontrada.</p>
        <?php endif; ?>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirmar Actualización</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas actualizar esta reserva?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmUpdate">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.11.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

    <script>
        document.getElementById('confirmUpdate').addEventListener('click', function() {
            document.getElementById('reservaForm').submit();
        });
    </script>
</body>

</html>

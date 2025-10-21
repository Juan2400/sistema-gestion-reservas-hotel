<?php
require 'conexion_bd.php';

$message = '';

try {
    // Crear Habitación
    if (isset($_POST['create'])) {
        if (!empty($_POST['numero_habitacion']) && !empty($_POST['id_tipo_habitacion']) && !empty($_POST['id_estado_habitacion'])) {
            $numero_habitacion = $_POST['numero_habitacion'];

            // Verificar si el número de habitación ya existe en la base de datos
            $sql = "SELECT COUNT(*) as count FROM Habitacion WHERE numero_habitacion = :numero_habitacion";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':numero_habitacion', $numero_habitacion);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                $message = 'El número de habitación ya está registrado. Por favor, use otro número.';
            } else {
                // Si el número no está registrado, proceder con el registro
                $sql = "INSERT INTO Habitacion (numero_habitacion, id_tipo_habitacion, id_estado_habitacion) VALUES (:numero_habitacion, :id_tipo_habitacion, :id_estado_habitacion)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':numero_habitacion', $_POST['numero_habitacion']);
                $stmt->bindParam(':id_tipo_habitacion', $_POST['id_tipo_habitacion']);
                $stmt->bindParam(':id_estado_habitacion', $_POST['id_estado_habitacion']);

                if ($stmt->execute()) {
                    $message = 'Habitación registrada con éxito';
                } else {
                    $message = 'No se pudo registrar la habitación';
                }
            }
        } else {
            $message = 'Por favor, complete todos los campos del formulario';
        }
    }

    // Actualizar Habitación
    if (isset($_POST['update'])) {
        if (!empty($_POST['numero_habitacion']) && !empty($_POST['id_tipo_habitacion']) && !empty($_POST['id_estado_habitacion'])) {
            $id_habitacion = $_POST['id_habitacion'];
            $numero_habitacion = $_POST['numero_habitacion'];

            // Verificar si el número de habitación ya existe en la base de datos, excluyendo la actual habitación
            $sql = "SELECT COUNT(*) as count FROM Habitacion WHERE numero_habitacion = :numero_habitacion AND id_habitacion != :id_habitacion";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':numero_habitacion', $numero_habitacion);
            $stmt->bindParam(':id_habitacion', $id_habitacion);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                $message = 'El número de habitación ya está registrado. Por favor, use otro número.';
            } else {
                // Si el número no está registrado, proceder con la actualización
                $sql = "UPDATE Habitacion SET numero_habitacion = :numero_habitacion, id_tipo_habitacion = :id_tipo_habitacion, id_estado_habitacion = :id_estado_habitacion WHERE id_habitacion = :id_habitacion";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':numero_habitacion', $_POST['numero_habitacion']);
                $stmt->bindParam(':id_tipo_habitacion', $_POST['id_tipo_habitacion']);
                $stmt->bindParam(':id_estado_habitacion', $_POST['id_estado_habitacion']);
                $stmt->bindParam(':id_habitacion', $_POST['id_habitacion']);

                if ($stmt->execute()) {
                    $message = 'Habitación actualizada con éxito';
                } else {
                    $message = 'No se pudo actualizar la habitación';
                }
            }
        } else {
            $message = 'Por favor, complete todos los campos del formulario';
        }
    }

    // Eliminar Habitación
    if (isset($_POST['delete'])) {
        $id_habitacion = (int) $_POST['delete'];

        $sql = "DELETE FROM Habitacion WHERE id_habitacion = :id_habitacion";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_habitacion', $id_habitacion, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: administradorHabitacion.php");
    }

    // Leer Habitaciones con información del tipo y estado
    $sql_habitacion = $conn->prepare("SELECT h.id_habitacion, h.numero_habitacion, h.id_tipo_habitacion, h.id_estado_habitacion, t.nombre as tipo_nombre, e.estado as estado_nombre 
                                      FROM Habitacion h 
                                      JOIN TipoHabitacion t ON h.id_tipo_habitacion = t.id_tipo_habitacion 
                                      JOIN EstadoHabitacion e ON h.id_estado_habitacion = e.id_estado_habitacion");
    $sql_habitacion->execute();
    $resultado = $sql_habitacion->fetchAll(PDO::FETCH_ASSOC);

    // Leer Tipos de Habitación
    $sql_tipos = $conn->prepare("SELECT id_tipo_habitacion, nombre FROM TipoHabitacion");
    $sql_tipos->execute();
    $tipos_habitacion = $sql_tipos->fetchAll(PDO::FETCH_ASSOC);

    // Leer Estados de Habitación
    $sql_estados = $conn->prepare("SELECT id_estado_habitacion, estado FROM EstadoHabitacion");
    $sql_estados->execute();
    $estados_habitacion = $sql_estados->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejo de errores
    die('Error en la base de datos: ' . $e->getMessage());
} finally {
    // Cerrar la conexión
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Habitacion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles_admin.css">
</head>

<body>
    <!-- Header -->
    <?php include 'administradorHeader.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Lista de Habitacion</h2>
        <?php if ($message) : ?>
            <div class="alert alert-warning">
                <?= $message ?>
            </div>
        <?php endif; ?>
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">Agregar Habitación</button>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Número de Habitación</th>
                        <th>Tipo de Habitación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultado as $row) : ?>
                        <tr>
                            <td><?= $row['id_habitacion'] ?></td>
                            <td><?= $row['numero_habitacion'] ?></td>
                            <td><?= $row['tipo_nombre'] ?></td>
                            <td><?= $row['estado_nombre'] ?></td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Acciones">
                                    <!-- Botón Editar -->
                                    <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#updateModal"
                                        data-id="<?= $row['id_habitacion'] ?>"
                                        data-numero_habitacion="<?= $row['numero_habitacion'] ?>"
                                        data-id_tipo_habitacion="<?= $row['id_tipo_habitacion'] ?>"
                                        data-id_estado_habitacion="<?= $row['id_estado_habitacion'] ?>">Editar</button>

                                    <!-- Botón Eliminar -->
                                    <button class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#confirmDeleteModal"
                                        data-id="<?= $row['id_habitacion'] ?>">Eliminar</button>
                                </div>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Crear Habitación -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Agregar Habitación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="numero_habitacion">Número de Habitación</label>
                            <input type="text" class="form-control" id="numero_habitacion" name="numero_habitacion" required>
                        </div>
                        <div class="form-group">
                            <label for="id_tipo_habitacion">Tipo de Habitación</label>
                            <select class="form-control" id="id_tipo_habitacion" name="id_tipo_habitacion" required>
                                <?php foreach ($tipos_habitacion as $tipo) : ?>
                                    <option value="<?= $tipo['id_tipo_habitacion'] ?>"><?= $tipo['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_estado_habitacion">Estado</label>
                            <select class="form-control" id="id_estado_habitacion" name="id_estado_habitacion" required>
                                <?php foreach ($estados_habitacion as $estado) : ?>
                                    <option value="<?= $estado['id_estado_habitacion'] ?>"><?= $estado['estado'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="create">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Habitación -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Editar Habitación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id_habitacion" name="id_habitacion">
                        <div class="form-group">
                            <label for="numero_habitacion">Número de Habitación</label>
                            <input type="text" class="form-control" id="numero_habitacion" name="numero_habitacion" required>
                        </div>
                        <div class="form-group">
                            <label for="id_tipo_habitacion">Tipo de Habitación</label>
                            <select class="form-control" id="id_tipo_habitacion" name="id_tipo_habitacion" required>
                                <?php foreach ($tipos_habitacion as $tipo) : ?>
                                    <option value="<?= $tipo['id_tipo_habitacion'] ?>"><?= $tipo['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_estado_habitacion">Estado</label>
                            <select class="form-control" id="id_estado_habitacion" name="id_estado_habitacion" required>
                                <?php foreach ($estados_habitacion as $estado) : ?>
                                    <option value="<?= $estado['id_estado_habitacion'] ?>" <?= $row['id_estado_habitacion'] == $estado['id_estado_habitacion'] ? 'selected' : '' ?>><?= $estado['estado'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="update">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Eliminación -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar esta habitación?
                </div>
                <div class="modal-footer">
                    <form method="POST" action="">
                        <input type="hidden" id="id_habitacion_delete" name="delete">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        // Pasar datos al modal de edición
        $('#updateModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id_habitacion = button.data('id');
            var numero_habitacion = button.data('numero_habitacion');
            var id_tipo_habitacion = button.data('id_tipo_habitacion');
            var estado = button.data('estado');

            var modal = $(this);
            modal.find('#id_habitacion').val(id_habitacion);
            modal.find('#numero_habitacion').val(numero_habitacion);
            modal.find('#id_tipo_habitacion').val(id_tipo_habitacion);
            modal.find('#estado').val(estado);
        });

        // Pasar datos al modal de confirmación de eliminación
        $('#confirmDeleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id_habitacion = button.data('id');

            var modal = $(this);
            modal.find('#id_habitacion_delete').val(id_habitacion);
        });
    </script>
</body>

</html>
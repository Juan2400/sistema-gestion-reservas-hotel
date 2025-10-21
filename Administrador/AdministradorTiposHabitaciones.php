<?php
require 'conexion_bd.php';

$message = '';

try {
    // Crear Tipo de Habitación
    if (isset($_POST['create'])) {
        if (!empty($_POST['nombre']) && !empty($_POST['descripcion']) && !empty($_POST['tarifa_hora'])) {
            $nombre = $_POST['nombre'];

            // Verificar si el tipo de habitación ya existe en la base de datos
            $sql = "SELECT COUNT(*) as count FROM TipoHabitacion WHERE nombre = :nombre";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                $message = 'El tipo de habitación ya está registrado. Por favor, elija otro nombre.';
            } else {
                // Si el nombre no está registrado, proceder con el registro
                $sql = "INSERT INTO TipoHabitacion (nombre, descripcion, tarifa_hora) VALUES (:nombre, :descripcion, :tarifa_hora)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':nombre', $_POST['nombre']);
                $stmt->bindParam(':descripcion', $_POST['descripcion']);
                $stmt->bindParam(':tarifa_hora', $_POST['tarifa_hora']);

                if ($stmt->execute()) {
                    $message = 'Tipo de habitación registrado con éxito';
                } else {
                    $message = 'No se pudo registrar el tipo de habitación';
                }
            }
        } else {
            $message = 'Por favor, complete todos los campos del formulario';
        }
    }

    // Actualizar Tipo de Habitación
    if (isset($_POST['update'])) {
        if (!empty($_POST['nombre']) && !empty($_POST['descripcion']) && !empty($_POST['tarifa_hora'])) {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];

            // Verificar si el nombre ya existe en la base de datos, excluyendo el actual tipo de habitación
            $sql = "SELECT COUNT(*) as count FROM TipoHabitacion WHERE nombre = :nombre AND id_tipo_habitacion != :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                $message = 'El tipo de habitación ya está registrado. Por favor, elija otro nombre.';
            } else {
                // Si el nombre no está registrado, proceder con la actualización
                $sql = "UPDATE TipoHabitacion SET nombre = :nombre, descripcion = :descripcion, tarifa_hora = :tarifa_hora WHERE id_tipo_habitacion = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':nombre', $_POST['nombre']);
                $stmt->bindParam(':descripcion', $_POST['descripcion']);
                $stmt->bindParam(':tarifa_hora', $_POST['tarifa_hora']);
                $stmt->bindParam(':id', $_POST['id']);

                if ($stmt->execute()) {
                    $message = 'Tipo de habitación actualizado con éxito';
                } else {
                    $message = 'No se pudo actualizar el tipo de habitación';
                }
            }
        } else {
            $message = 'Por favor, complete todos los campos del formulario';
        }
    }

    // Eliminar Tipo de Habitación
    if (isset($_POST['delete'])) {
        $id = (int) $_POST['delete'];

        $sql = "DELETE FROM TipoHabitacion WHERE id_tipo_habitacion = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: AdministradorTiposHabitaciones.php");
    }

    // Leer Tipos de Habitaciones
    $sql_types = $conn->prepare("SELECT id_tipo_habitacion, nombre, descripcion, tarifa_hora FROM TipoHabitacion");
    $sql_types->execute();
    $resultado = $sql_types->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Gestión de Tipos de Habitaciones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles_admin.css">
</head>

<body>
    <!-- Header -->
    <?php include 'administradorHeader.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Lista de Tipos de Habitaciones</h2>
        <?php if ($message) : ?>
            <div class="alert alert-warning">
                <?= $message ?>
            </div>
        <?php endif; ?>
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">Agregar Tipo de Habitación</button>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Tarifa por Hora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultado as $row) : ?>
                        <tr>
                            <td><?= $row['id_tipo_habitacion'] ?></td>
                            <td><?= $row['nombre'] ?></td>
                            <td><?= $row['descripcion'] ?></td>
                            <td><?= $row['tarifa_hora'] ?></td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Acciones">
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#updateModal" data-id="<?= $row['id_tipo_habitacion'] ?>" data-nombre="<?= $row['nombre'] ?>" data-descripcion="<?= $row['descripcion'] ?>" data-tarifa_hora="<?= $row['tarifa_hora'] ?>">Editar</button>
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmDeleteModal" data-id="<?= $row['id_tipo_habitacion'] ?>">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Crear Tipo de Habitación -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Agregar Tipo de Habitación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required></input>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="tarifa_hora">Tarifa por Hora</label>
                            <input type="number" class="form-control" id="tarifa_hora" name="tarifa_hora" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" name="create">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Tipo de Habitación -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Actualizar Tipo de Habitación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="update-id" name="id">
                        <div class="form-group">
                            <label for="update-nombre">Nombre</label>
                            <input type="text" class="form-control" id="update-nombre" name="nombre" required></input>
                        </div>
                        <div class="form-group">
                            <label for="update-descripcion">Descripción</label>
                            <textarea class="form-control" id="update-descripcion" name="descripcion" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="update-tarifa_hora">Tarifa por Hora</label>
                            <input type="number" class="form-control" id="update-tarifa_hora" name="tarifa_hora" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" name="update">Actualizar</button>
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
                    ¿Estás seguro de que deseas eliminar este tipo de habitación?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <form method="POST" action="">
                        <input type="hidden" id="delete-id" name="delete">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.11.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <script>
        // Rellenar campos del modal de actualización
        $('#updateModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nombre = button.data('nombre');
            var descripcion = button.data('descripcion');
            var tarifa_hora = button.data('tarifa_hora');

            var modal = $(this);
            modal.find('#update-id').val(id);
            modal.find('#update-nombre').val(nombre);
            modal.find('#update-descripcion').val(descripcion);
            modal.find('#update-tarifa_hora').val(tarifa_hora);
        });

        // Configurar ID de eliminación en el modal de confirmación
        $('#confirmDeleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            var modal = $(this);
            modal.find('#delete-id').val(id);
        });
    </script>
</body>

</html>

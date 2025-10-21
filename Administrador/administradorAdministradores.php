<?php
require 'conexion_bd.php';

$message = '';

try {
    // Crear Usuario
    if (isset($_POST['create'])) {
        if (!empty($_POST['nombres']) && !empty($_POST['dni']) && !empty($_POST['correo_electronico']) && !empty($_POST['contrasena']) && !empty($_POST['confirm_password']) && !empty($_POST['edad'])) {
            $correo_electronico = $_POST['correo_electronico'];

            // Verificar si el correo ya existe en la base de datos
            $sql = "SELECT COUNT(*) as count FROM Usuario WHERE correo_electronico = :correo_electronico";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':correo_electronico', $correo_electronico);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                $message = 'El correo electrónico ya está registrado. Por favor, use otro correo.';
            } else {
                // Verificar que las contraseñas coincidan
                if ($_POST['contrasena'] === $_POST['confirm_password']) {
                    // Si el correo no está registrado, proceder con el registro
                    $sql = "INSERT INTO Usuario (nombres, dni, correo_electronico, contrasena, id_tipo_usuario, edad) VALUES (:nombres, :dni, :correo_electronico, :contrasena, 2, :edad)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':nombres', $_POST['nombres']);
                    $stmt->bindParam(':dni', $_POST['dni']);
                    $stmt->bindParam(':correo_electronico', $_POST['correo_electronico']);
                    $contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT);
                    $stmt->bindParam(':contrasena', $contrasena);
                    $stmt->bindParam(':edad', $_POST['edad']);

                    if ($stmt->execute()) {
                        $message = 'Administrador registrado con éxito';
                    } else {
                        $message = 'No se pudo registrar el administrador';
                    }
                } else {
                    $message = 'Las contraseñas no coinciden. Por favor, inténtelo de nuevo.';
                }
            }
        } else {
            $message = 'Por favor, complete todos los campos del formulario';
        }
    }

    // Actualizar Usuario
    if (isset($_POST['update'])) {
        if (!empty($_POST['nombres']) && !empty($_POST['dni']) && !empty($_POST['correo_electronico']) && !empty($_POST['edad'])) {
            $id = $_POST['id'];
            $correo_electronico = $_POST['correo_electronico'];

            // Verificar si el correo ya existe en la base de datos, excluyendo el actual usuario
            $sql = "SELECT COUNT(*) as count FROM Usuario WHERE correo_electronico = :correo_electronico AND id_usuario != :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':correo_electronico', $correo_electronico);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                $message = 'El correo electrónico ya está registrado. Por favor, use otro correo.';
            } else {
                // Si el correo no está registrado, proceder con la actualización
                $sql = "UPDATE Usuario SET nombres = :nombres, dni = :dni, correo_electronico = :correo_electronico, edad = :edad WHERE id_usuario = :id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':nombres', $_POST['nombres']);
                $stmt->bindParam(':dni', $_POST['dni']);
                $stmt->bindParam(':correo_electronico', $_POST['correo_electronico']);
                $stmt->bindParam(':edad', $_POST['edad']);
                $stmt->bindParam(':id', $_POST['id']);

                if ($stmt->execute()) {
                    $message = 'Administrador actualizado con éxito';
                } else {
                    $message = 'No se pudo actualizar el administrador';
                }
            }
        } else {
            $message = 'Por favor, complete todos los campos del formulario';
        }
    }

    // Eliminar Usuario
    if (isset($_POST['delete'])) {
        $id = (int) $_POST['delete'];

        $sql = "DELETE FROM Usuario WHERE id_usuario = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: administradorAdministradores.php");
    }

    // Leer Usuarios
    $sql_users = $conn->prepare("SELECT id_usuario, nombres, dni, correo_electronico, edad FROM Usuario WHERE id_tipo_usuario = 2");
    $sql_users->execute();
    $resultado = $sql_users->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Gestión de Administradores</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles_admin.css">
</head>

<body>
    <!-- Header -->
    <?php include 'administradorHeader.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Lista de Administradores</h2>
        <?php if ($message) : ?>
            <div class="alert alert-warning">
                <?= $message ?>
            </div>
        <?php endif; ?>
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">Agregar Administrador</button>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>DNI</th>
                        <th>Email</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultado as $row) : ?>
                        <tr>
                            <td><?= $row['id_usuario'] ?></td>
                            <td><?= $row['nombres'] ?></td>
                            <td><?= $row['dni'] ?></td>
                            <td><?= $row['correo_electronico'] ?></td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Acciones">
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#updateModal"
                                        data-id="<?= $row['id_usuario'] ?>"
                                        data-nombres="<?= $row['nombres'] ?>"
                                        data-dni="<?= $row['dni'] ?>"
                                        data-email="<?= $row['correo_electronico'] ?>"
                                        data-edad="<?= $row['edad'] ?>">Editar</button>

                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmDeleteModal" data-id="<?= $row['id_usuario'] ?>">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Crear Administrador -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Agregar Administrador</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nombres">Nombre</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" required>
                        </div>
                        <div class="form-group">
                            <label for="dni">DNI</label>
                            <input type="text" class="form-control" id="dni" name="dni" required>
                        </div>
                        <div class="form-group">
                            <label for="correo_electronico">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" required>
                        </div>
                        <div class="form-group">
                            <label for="edad">Edad</label>
                            <input type="number" class="form-control" id="edad" name="edad" required min="18">
                            <small class="form-text text-muted">Debe ser mayor de 18 años para registrarse como administrador.</small>
                        </div>
                        <div class="form-group">
                            <label for="contrasena">Contraseña</label>
                            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
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

    <!-- Modal Editar Administrador -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Editar Administrador</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        <div class="form-group">
                            <label for="nombres">Nombre</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" required>
                        </div>
                        <div class="form-group">
                            <label for="dni">DNI</label>
                            <input type="text" class="form-control" id="dni" name="dni" required>
                        </div>
                        <div class="form-group">
                            <label for="correo_electronico">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" required>
                        </div>
                        <div class="form-group">
                            <label for="edad">Edad</label>
                            <input type="number" class="form-control" id="edad" name="edad" required min="18">
                            <small class="form-text text-muted">Debe ser mayor de 18 años para registrarse como administrador.</small>
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
                    ¿Está seguro de que desea eliminar este administrador?
                </div>
                <div class="modal-footer">
                    <form method="POST" action="">
                        <input type="hidden" id="delete_id" name="delete">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->

    <!-- Scripts de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script>
        $('#updateModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nombres = button.data('nombres');
            var dni = button.data('dni');
            var email = button.data('email');
            var edad = button.data('edad'); // Obtiene la edad del data-edad

            var modal = $(this);
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #nombres').val(nombres);
            modal.find('.modal-body #dni').val(dni);
            modal.find('.modal-body #correo_electronico').val(email);
            modal.find('.modal-body #edad').val(edad); // Asigna la edad al campo de edad
        });


        $('#confirmDeleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            var modal = $(this);
            modal.find('.modal-footer #delete_id').val(id);
        });
    </script>

</body>

</html>
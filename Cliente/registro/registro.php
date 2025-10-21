<?php
require '../conexion.php';

$message = '';

try {
    $conexion = new Conexion(); // Crear instancia de la clase Conexion
    $conn = $conexion->getConexion(); // Obtener la conexión

    // Verificar si los campos del formulario no están vacíos
    if (!empty($_POST['nombre']) && !empty($_POST['edad']) && !empty($_POST['dni']) && !empty($_POST['correo_electronico']) && !empty($_POST['contrasena'])) {
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
            if ($_POST['contrasena'] === $_POST['confirmarContrasena']) {
                // Si el correo no está registrado, proceder con el registro
                $sql = "INSERT INTO Usuario (nombres, dni, correo_electronico, contrasena, id_tipo_usuario, edad) 
                        VALUES (:nombres, :dni, :correo_electronico, :contrasena, 1, :edad)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':nombres', $_POST['nombre']);
                $stmt->bindParam(':dni', $_POST['dni']);
                $stmt->bindParam(':correo_electronico', $_POST['correo_electronico']);
                $contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT);
                $stmt->bindParam(':contrasena', $contrasena);
                $stmt->bindParam(':edad', $_POST['edad']);

                if ($stmt->execute()) {
                    $message = 'Usuario registrado con éxito';
                } else {
                    // Mostrar el error que impide la ejecución
                    $errorInfo = $stmt->errorInfo();
                    $message = 'Error al registrar el usuario: ' . $errorInfo[2];
                }
            } else {
                $message = 'Las contraseñas no coinciden. Por favor, inténtelo de nuevo.';
            }
        }
    } else {
        $message = 'Por favor, complete todos los campos del formulario';
    }
} catch (PDOException $e) {
    $message = 'Error en la base de datos: ' . $e->getMessage();
} finally {
    // Cerrar la conexión a la base de datos
    if ($conexion) {
        $conexion->cerrarConexion();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GOLDEN RED</title>
    <link rel="shortcut icon" type="image" href="./image/logo.png">
    <link rel="stylesheet" href="stylesForm.css">
    <!-- icons links -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

    <form action="" method="POST" id="registerForm">
        <a href="../index.php"><i class='bx bx-x-circle'></i></a>
        <h1 class="title">Regístrate</h1>
        <?php if ($message) : ?>
            <div class="alert alert-warning">
                <?= $message ?>
            </div>
        <?php endif; ?>
        <label>
            <i class='bx bx-user'></i>
            <input placeholder="Ingresar nombre" type="text" id="nombre" name="nombre" maxlength="50" required>
        </label>
        <label>
            <i class='bx bx-check'></i>
            <input placeholder="Edad" type="number" class="form-control" id="edad" name="edad" required min="18" maxlength="255" required>
        </label>
        <label>
            <i class='bx bx-id-card'></i>
            <input placeholder="Ingresar DNI" type="text" id="dni" name="dni" maxlength="20" required>
        </label>
        <label>
            <i class='bx bx-envelope'></i>
            <input placeholder="Ingresar correo electrónico" type="email" id="correo_electronico" name="correo_electronico" maxlength="100" required>
        </label>
        <label>
            <i class='bx bx-key'></i>
            <input placeholder="Ingresar contraseña" type="password" id="contrasena" name="contrasena" maxlength="255" required>
        </label>
        <label>
            <i class='bx bx-check'></i>
            <input placeholder="Confirmar contraseña" type="password" id="confirmarContrasena" name="confirmarContrasena" maxlength="255" required>
        </label>
        <button type="submit" id="button">Registrarme</button>
        <small class="form-text text-muted">Debe ser mayor de 18 años</small>
    </form>
    <?php include 'mensajeFlotante.php'; ?>
    <script src="scriptRegister.js"></script>
</body>

</html>
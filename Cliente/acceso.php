<?php
session_start();

try {
    if (isset($_SESSION['user_id'])) {
        // Obtener el tipo de usuario
        require 'conexion.php';

        $conexion = new Conexion(); // Crear instancia de la clase Conexion
        $conn = $conexion->getConexion(); // Obtener la conexión

        // Cambiado para obtener el 'tipo' desde la tabla 'TipoUsuario'
        $records = $conn->prepare('
            SELECT tu.tipo 
            FROM Usuario u
            INNER JOIN TipoUsuario tu ON u.id_tipo_usuario = tu.id_tipo_usuario
            WHERE u.id_usuario = :id_usuario
        ');
        $records->bindParam(':id_usuario', $_SESSION['user_id']);
        $records->execute();
        $results = $records->fetch(PDO::FETCH_ASSOC);

        if ($results) {
            // Redirigir según el tipo de usuario
            if ($results['tipo'] == 'cliente') {
                header('Location: index.php');
            } elseif ($results['tipo'] == 'administrador') {
                header('Location: ../Administrador/administradorAdministradores.php');
            }
            exit();
        }
    }

    require 'conexion.php';

    $message = '';

    $conexion = new Conexion(); // Crear instancia de la clase Conexion
    $conn = $conexion->getConexion(); // Obtener la conexión

    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        // Cambiado para obtener el 'tipo' desde la tabla 'TipoUsuario'
        $records = $conn->prepare('
            SELECT u.id_usuario, u.correo_electronico, u.contrasena, tu.tipo 
            FROM Usuario u
            INNER JOIN TipoUsuario tu ON u.id_tipo_usuario = tu.id_tipo_usuario
            WHERE u.correo_electronico = :email
        ');
        $records->bindParam(':email', $_POST['email']);
        $records->execute();
        $results = $records->fetch(PDO::FETCH_ASSOC);

        if ($results && password_verify($_POST['password'], $results['contrasena'])) {
            $_SESSION['user_id'] = $results['id_usuario'];

            // Redirigir según el tipo de usuario
            if ($results['tipo'] == 'cliente') {
                header('Location: index.php');
            } elseif ($results['tipo'] == 'administrador') {
                header('Location: ../../Administrador/administradorAdministradores.php');
            }
            exit();
        } else {
            $message = 'Lo siento, esas credenciales no coinciden';
        }
    }
} catch (PDOException $e) {
    // Manejo de errores
    $message = 'Error en la base de datos: ' . $e->getMessage();
    exit;
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
    <title>Decofruta</title>
    <link rel="shortcut icon" type="image" href="./image/logo.png">
    <link rel="stylesheet" href="registro/stylesForm.css">
    <!-- icons links -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

    <form action="" method="POST">
        <a href="index.php"><i class='bx bx-x-circle'></i></a>
        <h1 class="title">Acceso</h1>
        <label>
            <i class='bx bx-envelope'></i>
            <input placeholder="correo electrónico" type="email" id="email" name="email" required>
        </label>
        <label>
            <i class='bx bx-key'></i>
            <input placeholder="contraseña" type="password" id="password" name="password" required>
        </label>
        <a href="#" class="link">¿Olvidaste tu contraseña?</a>
        <a href="registro/registro.php" class="link">Crea una cuenta</a>

        <button type="submit" id="button">Acceder</button>
    </form>

</body>

</html>
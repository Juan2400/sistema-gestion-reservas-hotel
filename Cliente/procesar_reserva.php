<?php
session_start();
require 'conexion.php';
require_once '../enviarCorreo/enviarCorreo.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$conexion = new Conexion();
$conn = $conexion->getConexion();

// Recibir y decodificar los datos JSON
$datos = json_decode(file_get_contents('php://input'), true);

// Validar los datos recibidos
$campos_requeridos = ['id_habitacion', 'fecha_inicio', 'id_tipo_estadia', 'duracion', 'monto_total'];
$campos_faltantes = [];

foreach ($campos_requeridos as $campo) {
    if (empty($datos[$campo])) {
        $campos_faltantes[] = $campo;
    }
}

if (!empty($campos_faltantes)) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos. Campos faltantes: ' . implode(', ', $campos_faltantes)
    ]);
    exit;
}

try {
    $conn->beginTransaction();

    $id_habitacion = $datos['id_habitacion'];
    $fecha_inicio = $datos['fecha_inicio'];
    $fecha_fin = date('Y-m-d H:i:s', strtotime($fecha_inicio . ' + ' . $datos['duracion'] . ' hours'));

    // Generar un código alfanumérico único
    $codigo_alfanumerico = generarCodigoAlfanumerico($conn);

    // Verificar si la habitación está disponible
    $sql = "SELECT id_estado_habitacion FROM Habitacion WHERE id_habitacion = :id_habitacion";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_habitacion', $id_habitacion);
    $stmt->execute();
    $estado_habitacion = $stmt->fetchColumn();

    if ($estado_habitacion != 1) {
        throw new Exception('La habitación seleccionada ya no está disponible');
    }

    // Insertar la reserva
    $sql = "INSERT INTO Reserva (id_usuario, id_habitacion, fecha_inicio, fecha_fin, id_tipo_estadia, codigo_alfanumerico, confirmada, completada) 
            VALUES (:id_usuario, :id_habitacion, :fecha_inicio, :fecha_fin, :id_tipo_estadia, :codigo_alfanumerico, 0, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $_SESSION['user_id']);
    $stmt->bindParam(':id_habitacion', $id_habitacion);
    $stmt->bindParam(':fecha_inicio', $fecha_inicio);
    $stmt->bindParam(':fecha_fin', $fecha_fin);
    $stmt->bindParam(':id_tipo_estadia', $datos['id_tipo_estadia']);
    $stmt->bindParam(':codigo_alfanumerico', $codigo_alfanumerico);
    $stmt->execute();

    $id_reserva = $conn->lastInsertId();

    // Insertar el pago
    $sql = "INSERT INTO Pago (id_reserva, monto, id_metodo_pago) VALUES (:id_reserva, :monto, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_reserva', $id_reserva);
    $stmt->bindParam(':monto', $datos['monto_total']);
    $stmt->execute();

    // Actualizar el estado de la habitación
    $sql = "UPDATE Habitacion SET id_estado_habitacion = 2 WHERE id_habitacion = :id_habitacion";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_habitacion', $id_habitacion);
    $stmt->execute();

    // Obtener el correo electrónico del usuario
    $sql = "SELECT correo_electronico FROM Usuario WHERE id_usuario = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $_SESSION['user_id']);
    $stmt->execute();
    $correo_electronico = $stmt->fetchColumn();

    // Enviar el correo
    $resultadoCorreo = enviarCorreo(
        $correo_electronico,
        $codigo_alfanumerico,
        $fecha_inicio,
        $fecha_fin,
        $datos['monto_total']
    );

    if (!$resultadoCorreo['success']) {
        throw new Exception('Error al enviar el correo: ' . $resultadoCorreo['message']);
    }

    $conn->commit();

    // Respuesta JSON única
    echo json_encode([
        'success' => true,
        'id_reserva' => $id_reserva,
        'codigo_alfanumerico' => $codigo_alfanumerico,
        'email_sent' => true,
        'email_recipient' => $correo_electronico
    ]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Funciones auxiliares
function generarCodigoAlfanumerico($conn, $length = 10) {
    $codigo = '';
    do {
        $codigo = generarCodigoRandom($length);
    } while (!codigoAlfanumericoUnico($conn, $codigo));
    return $codigo;
}

function generarCodigoRandom($length = 10) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function codigoAlfanumericoUnico($conn, $codigo) {
    $sql = "SELECT COUNT(*) FROM Reserva WHERE codigo_alfanumerico = :codigo";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':codigo', $codigo);
    $stmt->execute();
    return $stmt->fetchColumn() == 0;
}
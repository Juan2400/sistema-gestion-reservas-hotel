<?php 
require 'conexion.php';
$conexion = new Conexion();
$conn = $conexion->getConexion();

// Configurar encabezados para permitir solicitudes AJAX
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['tipoEstadia']) || !isset($data['tipoHabitacion'])) {
        throw new Exception('Datos incompletos');
    }

    $id_tipo_estadia = $data['tipoEstadia'];
    $id_tipo_habitacion = $data['tipoHabitacion'];

    // Obtener la duración de la estadía
    $sql_duracion = "SELECT duracion_horas FROM TipoEstadia WHERE id_tipo_estadia = :id_tipo_estadia";
    $stmt = $conn->prepare($sql_duracion);
    $stmt->bindParam(':id_tipo_estadia', $id_tipo_estadia, PDO::PARAM_INT);
    $stmt->execute();
    $duracion = $stmt->fetchColumn();

    if ($duracion === false) {
        throw new Exception('Tipo de estadía no encontrado');
    }

    // Obtener la tarifa por hora de la habitación
    $sql_tarifa = "SELECT tarifa_hora FROM TipoHabitacion WHERE id_tipo_habitacion = :id_tipo_habitacion";
    $stmt = $conn->prepare($sql_tarifa);
    $stmt->bindParam(':id_tipo_habitacion', $id_tipo_habitacion, PDO::PARAM_INT);
    $stmt->execute();
    $tarifa_hora = $stmt->fetchColumn();

    if ($tarifa_hora === false) {
        throw new Exception('No se encontró la tarifa para el tipo de habitación especificado');
    }

    // Calcular el monto total
    $monto = $tarifa_hora * $duracion;

    echo json_encode([
        'success' => true,
        'monto' => number_format($monto, 2) // Formato con dos decimales
    ]);
} catch (Exception $e) {
    error_log('Error en obtener_monto.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn = null;
?>

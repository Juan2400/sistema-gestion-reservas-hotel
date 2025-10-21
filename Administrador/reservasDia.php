<?php
// Incluir la clase de conexión
include 'conexion_bd.php';

// Incluir el autoloader de Composer
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Obtener la fecha actual en formato 'YYYY-MM-DD'
date_default_timezone_set('America/Lima');
$fecha_actual = date('Y-m-d');

// Consultar las reservas del día
try {
    $query = "SELECT 
                t.nombre AS tipo_habitacion, 
                h.numero_habitacion, 
                u.nombres, 
                u.dni, 
                r.fecha_inicio, 
                r.fecha_fin, 
                m.metodo AS metodo_pago, 
                p.monto 
              FROM 
                reserva r
              INNER JOIN 
                habitacion h ON r.id_habitacion = h.id_habitacion
              INNER JOIN 
                pago p ON r.id_reserva = p.id_reserva
              INNER JOIN 
                metodopago m ON p.id_metodo_pago = m.id_metodo_pago
              INNER JOIN 
                usuario u ON r.id_usuario = u.id_usuario
              INNER JOIN 
                tipohabitacion t ON h.id_tipo_habitacion = t.id_tipo_habitacion
              WHERE 
                DATE(r.fecha_inicio) = :fecha_actual";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':fecha_actual', $fecha_actual);
    $stmt->execute();
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular el total de los montos
    $total_monto = 0;
    foreach ($reservas as $reserva) {
        $total_monto += $reserva['monto'];
    }
} catch (PDOException $e) {
    die('Error al ejecutar la consulta: ' . $e->getMessage());
}

// Descargar Excel
if (isset($_GET['exportar']) && $_GET['exportar'] === 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Escribir encabezados
    $sheet->setCellValue('A1', 'Tipo de Habitación');
    $sheet->setCellValue('B1', 'Número de Habitación');
    $sheet->setCellValue('C1', 'Nombre');
    $sheet->setCellValue('D1', 'DNI');
    $sheet->setCellValue('E1', 'Fecha Inicio');
    $sheet->setCellValue('F1', 'Fecha Fin');
    $sheet->setCellValue('G1', 'Método de Pago');
    $sheet->setCellValue('H1', 'Monto');

    // Escribir datos
    $fila = 2;
    $total_monto = 0;
    foreach ($reservas as $reserva) {
        $sheet->setCellValue('A' . $fila, $reserva['tipo_habitacion']);
        $sheet->setCellValue('B' . $fila, $reserva['numero_habitacion']);
        $sheet->setCellValue('C' . $fila, $reserva['nombres']);
        $sheet->setCellValue('D' . $fila, $reserva['dni']);
        $sheet->setCellValue('E' . $fila, $reserva['fecha_inicio']);
        $sheet->setCellValue('F' . $fila, $reserva['fecha_fin']);
        $sheet->setCellValue('G' . $fila, $reserva['metodo_pago']);
        $sheet->setCellValue('H' . $fila, $reserva['monto']);
        $total_monto += $reserva['monto'];
        $fila++;
    }

    // Añadir total de los montos
    $sheet->setCellValue('G' . $fila, 'Total:');
    $sheet->setCellValue('H' . $fila, $total_monto);

    // Aplicar estilos opcionales
    $sheet->getStyle('A1:H1')->getFont()->setBold(true); // Encabezados en negrita
    $sheet->getStyle('G' . $fila . ':H' . $fila)->getFont()->setBold(true); // Total en negrita

    // Ajustar ancho automáticamente
    foreach (range('A', 'H') as $columna) {
        $sheet->getColumnDimension($columna)->setAutoSize(true);
    }

    // Configurar para descargar el archivo
    try {
        ob_end_clean(); // Limpia cualquier salida anterior
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reservas_' . $fecha_actual . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } catch (Exception $e) {
        die("Error al generar el archivo: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas del Día</title>
    <!-- Enlace a Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles_admin.css">
</head>
<body>
<?php include 'administradorHeader.php'; ?>
<div class="container mt-5">
    <h1 class="text-center mb-4">Reservas del Día: <?php echo $fecha_actual; ?></h1>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-primary text-center">
                <tr>
                    <th>Tipo de Habitación</th>
                    <th>Número de Habitación</th>
                    <th>Nombre</th>
                    <th>DNI</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Método de Pago</th>
                    <th>Monto (S/)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reservas)): ?>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reserva['tipo_habitacion']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['numero_habitacion']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['nombres']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['dni']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['fecha_inicio']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['fecha_fin']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['metodo_pago']); ?></td>
                            <td class="text-end"><?php echo htmlspecialchars($reserva['monto']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No hay reservas para el día de hoy.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="text-center mt-4">
        <a href="?exportar=excel" class="btn btn-success">Descargar Excel</a>
        <a href="index.php" class="btn btn-primary">Volver al Inicio</a>
    </div>
</div>

<!-- Enlace a Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

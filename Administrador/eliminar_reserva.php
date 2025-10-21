<?php
try {
    require 'conexion_bd.php';

    // Obtener ID de reserva de la URL
    $id_reserva = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Eliminar reserva y actualizar estado de la habitación
    if ($id_reserva > 0) {
        // Iniciar transacción
        $conn->beginTransaction();

        try {
            // Obtener id_habitacion asociada a la reserva antes de eliminarla
            $sql_select_habitacion = "SELECT id_habitacion FROM Reserva WHERE id_reserva = ?";
            $stmt_select_habitacion = $conn->prepare($sql_select_habitacion);
            $stmt_select_habitacion->execute([$id_reserva]);
            $habitacion = $stmt_select_habitacion->fetch(PDO::FETCH_ASSOC);

            if ($habitacion) {
                $id_habitacion = $habitacion['id_habitacion'];

                // Eliminar pagos asociados a la reserva
                $sql_delete_pagos = "DELETE FROM Pago WHERE id_reserva = ?";
                $stmt_delete_pagos = $conn->prepare($sql_delete_pagos);
                $stmt_delete_pagos->execute([$id_reserva]);

                // Eliminar la reserva
                $sql_delete_reserva = "DELETE FROM Reserva WHERE id_reserva = ?";
                $stmt_delete_reserva = $conn->prepare($sql_delete_reserva);
                $stmt_delete_reserva->execute([$id_reserva]);

                // Actualizar el estado de la habitación a '1' (disponible)
                $sql_update_habitacion = "UPDATE Habitacion SET id_estado_habitacion = 1 WHERE id_habitacion = ?";
                $stmt_update_habitacion = $conn->prepare($sql_update_habitacion);
                $stmt_update_habitacion->execute([$id_habitacion]);

                // Confirmar transacción
                $conn->commit();

                // Redirigir a la página de administración
                header('Location: administradorReservas.php');
                exit();
            } else {
                throw new Exception("No se encontró una habitación asociada con la reserva.");
            }
        } catch (Exception $e) {
            // Revertir cambios si hay un error
            $conn->rollBack();
            throw $e;
        }
    }
} catch (PDOException $e) {
    die('Error en la base de datos: ' . $e->getMessage());
} finally {
    $conn = null;
}
?>

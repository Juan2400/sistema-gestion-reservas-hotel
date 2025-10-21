<?php
session_start();
require 'conexion.php';
$message = '';
$conexion = new Conexion();
$conn = $conexion->getConexion();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    $message = 'Por favor, inicie sesión para hacer una reserva';
} else {
    // Obtener habitaciones disponibles y tipos de estadía
    $habitacionesDisponibles = $conn->query("
        SELECT h.id_habitacion, h.numero_habitacion, th.nombre as tipo_habitacion, th.tarifa_hora 
        FROM Habitacion h 
        JOIN TipoHabitacion th ON h.id_tipo_habitacion = th.id_tipo_habitacion 
        WHERE h.id_estado_habitacion = 1")->fetchAll(PDO::FETCH_ASSOC);

    $tiposEstadia = $conn->query("SELECT id_tipo_estadia, tipo, duracion_horas FROM TipoEstadia")->fetchAll(PDO::FETCH_ASSOC);

}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de Habitación</title>
    <link rel="icon" href="imagenes/icono.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <!-- Barra de Navegación -->
    <?php include 'navegacionCliente.php'; ?>


    <div class="container" id="contact">
        <?php if (!empty($message)) : ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <h1>Reservar Habitación</h1>
        <div class="row" style="margin-top:50px;">
            <div class="col-md-6 py-3 py-md-0">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="habitacion">Habitación Disponible</label>
                        <select class="form-control" id="habitacion" name="habitacion" required>
                            <option value="" disabled selected>Seleccione una habitación</option>
                            <?php foreach ($habitacionesDisponibles as $habitacion) : ?>
                                <option value="<?php echo $habitacion['id_habitacion']; ?>" data-precio="<?php echo $habitacion['tarifa_hora']; ?>">
                                    <?php echo htmlspecialchars($habitacion['numero_habitacion'] . ' - ' . $habitacion['tipo_habitacion']); ?> - S/<?php echo $habitacion['tarifa_hora']; ?>/hora
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fechaInicio">Fecha y Hora de Inicio</label>
                        <input type="datetime-local" class="form-control" id="fechaInicio" name="fechaInicio" required>
                    </div>
                    <div class="form-group">
                        <label for="tipoEstadia">Tipo de Estadía</label>
                        <select class="form-control" id="tipoEstadia" name="tipoEstadia" required>
                            <option value="" disabled selected>Seleccione el tipo de estadía</option>
                            <?php foreach ($tiposEstadia as $tipo) : ?>
                                <option value="<?php echo $tipo['id_tipo_estadia']; ?>" data-duracion="<?php echo $tipo['duracion_horas']; ?>">
                                    <?php echo htmlspecialchars($tipo['tipo']); ?> (<?php echo $tipo['duracion_horas']; ?> horas)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fechaFin">Fecha y Hora de Fin (Calculado automáticamente)</label>
                        <input type="text" class="form-control" id="fechaFin" name="fechaFin" readonly>
                    </div>
                    <div class="form-group">
                        <label for="montoTotal">Monto Total</label>
                        <input type="text" class="form-control" id="montoTotal" name="montoTotal" readonly>
                    </div>
                    <input type="hidden" id="duracion" name="duracion">
                    <button type="submit" class="btn btn-primary">Reservar y Proceder al Pago</button>
                </form>
            </div>
            <div class="col-md-6 py-3 py-md-0">
                <div class="card h-100">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3973.5290625334974!2d-80.6480148262268!3d-5.179163094798296!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x904a1be00b5b665b%3A0x72fa4e6d2fb3d6ad!2sHOSPEDAJE%20GOLDEN%20RED!5e0!3m2!1ses-419!2spe!4v1724365048793!5m2!1ses-419!2spe" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Pago -->
    <div class="modal fade" id="modalConfirmarPago" tabindex="-1" aria-labelledby="modalConfirmarPagoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmarPagoLabel">Confirmar Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>El monto total es de S/<span id="montoTotalModal"></span>.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="finalizarPagoBtn">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ingresar Datos de la Tarjeta -->
    <div class="modal fade" id="modalDatosTarjeta" tabindex="-1" aria-labelledby="modalDatosTarjetaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDatosTarjetaLabel">Datos de la Tarjeta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formPagoTarjeta">
                        <div class="mb-3">
                            <label for="numeroTarjeta" class="form-label">Número de Tarjeta</label>
                            <input type="text" class="form-control" id="numeroTarjeta" required>
                        </div>
                        <div class="mb-3">
                            <label for="fechaExpiracion" class="form-label">Fecha de Expiración</label>
                            <input type="month" class="form-control" id="fechaExpiracion" required>
                        </div>
                        <div class="mb-3">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="cvv" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="finalizarReservaBtn">Finalizar Pago</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const habitacionSelect = document.getElementById('habitacion');
            const tipoEstadiaSelect = document.getElementById('tipoEstadia');
            const fechaInicioInput = document.getElementById('fechaInicio');
            const fechaFinInput = document.getElementById('fechaFin');
            const duracionInput = document.getElementById('duracion');
            const montoTotalInput = document.getElementById('montoTotal');

            function calcularFechaFin() {
                const fechaInicio = new Date(fechaInicioInput.value);
                const duracion = parseInt(duracionInput.value);
                if (!isNaN(fechaInicio.getTime()) && !isNaN(duracion)) {
                    const fechaFin = new Date(fechaInicio.getTime() + duracion * 60 * 60 * 1000);
                    fechaFinInput.value = fechaFin.toLocaleString('es-PE', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            }

            function calcularMontoTotal() {
                const precioHora = parseFloat(habitacionSelect.options[habitacionSelect.selectedIndex].dataset.precio);
                const duracion = parseInt(duracionInput.value);
                if (!isNaN(precioHora) && !isNaN(duracion)) {
                    const montoTotal = precioHora * duracion;
                    montoTotalInput.value = 'S/' + montoTotal.toFixed(2);
                }
            }

            habitacionSelect.addEventListener('change', calcularMontoTotal);
            tipoEstadiaSelect.addEventListener('change', function() {
                duracionInput.value = this.options[this.selectedIndex].dataset.duracion;
                calcularFechaFin();
                calcularMontoTotal();
            });
            fechaInicioInput.addEventListener('change', calcularFechaFin);

            // Manejar la apertura del modal de confirmación de pago
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const montoTotal = montoTotalInput.value.replace('S/', '');
                document.getElementById('montoTotalModal').textContent = montoTotal;
                const modalConfirmarPago = new bootstrap.Modal(document.getElementById('modalConfirmarPago'));
                modalConfirmarPago.show();
            });

            // Manejar el botón de finalizar pago
            document.getElementById('finalizarPagoBtn').addEventListener('click', function() {
                const modalConfirmarPago = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarPago'));
                modalConfirmarPago.hide();
                const modalDatosTarjeta = new bootstrap.Modal(document.getElementById('modalDatosTarjeta'));
                modalDatosTarjeta.show();
            });

            // Manejar el envío del formulario de pago
            document.getElementById('finalizarReservaBtn').addEventListener('click', function() {
                const formPagoTarjeta = document.getElementById('formPagoTarjeta');
                if (formPagoTarjeta.checkValidity()) {
                    // Recopilar todos los datos necesarios
                    const datos = {
                        id_habitacion: document.getElementById('habitacion').value,
                        fecha_inicio: document.getElementById('fechaInicio').value,
                        id_tipo_estadia: document.getElementById('tipoEstadia').value,
                        duracion: document.getElementById('duracion').value,
                        monto_total: document.getElementById('montoTotal').value.replace('S/', ''),
                        numero_tarjeta: document.getElementById('numeroTarjeta').value,
                        fecha_expiracion: document.getElementById('fechaExpiracion').value,
                        cvv: document.getElementById('cvv').value
                    };

                    console.log('Datos a enviar:', datos); // Para depuración

                    // Enviar datos al servidor
                    fetch('procesar_reserva.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(datos),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (data.codigo_alfanumerico) {
                                    alert('La reserva se realizó con éxito. Código de reserva: ' + data.codigo_alfanumerico);
                                } else {
                                    alert('La reserva se realizó con éxito, pero no se recibió un código de reserva.');
                                }
                                window.location.reload(); // Actualiza la misma página
                            } else {
                                alert('Error al procesar la reserva: ' + data.message);
                                console.error('Respuesta del servidor:', data); // Para depuración
                            }
                        })
                        .catch((error) => {
                            //console.error('Error:', error);
                            alert('Error al procesar la reserva: ',error);
                        });
                } else {
                    formPagoTarjeta.reportValidity();
                }
            });
        });
    </script>
</body>

</html>
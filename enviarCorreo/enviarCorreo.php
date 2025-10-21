<?php
// Incluir los archivos de PHPMailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar configuración del servidor SMTP
$config = require 'config.php';

function enviarCorreo($destinatario, $codigo_alfanumerico, $fecha_inicio, $fecha_fin, $monto_total) {
    global $config;

    $mail = new PHPMailer(true); // Habilitar excepciones

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_user'];
        $mail->Password = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['smtp_port'];

        // Configuración del correo
        $mail->setFrom($config['smtp_user'], 'Reserva Golden Red');
        $mail->addAddress($destinatario); // Destinatario
        $mail->isHTML(true);             // Habilitar HTML
        $mail->Subject = 'Confirmacion de Reserva - Hotel Golden Red';
        $mail->Body    = "
            <h2>Detalle de su Reserva</h2>
            <p>Gracias por elegir Hotel Golden Red. Aquí están los detalles de su reserva:</p>
            <ul>
                <li><strong>Código de Confirmación:</strong> $codigo_alfanumerico</li>
                <li><strong>Fecha de Inicio:</strong> $fecha_inicio</li>
                <li><strong>Fecha de Fin:</strong> $fecha_fin</li>
                <li><strong>Monto Total:</strong> S/. $monto_total</li>
            </ul>
            <p>Si tiene alguna pregunta, no dude en contactarnos.</p>
            <p>¡Esperamos que disfrute su estadía!</p>
        ";

        // Enviar el correo
        $mail->send();

        // Si el correo se envió exitosamente, devuelve un arreglo con success: true
        return ['success' => true, 'message' => 'Correo enviado correctamente'];
    } catch (Exception $e) {
        // Si hubo un error, devuelve un arreglo con success: false y el mensaje de error
        return ['success' => false, 'message' => "Error al enviar correo: {$mail->ErrorInfo}"];
    }
}
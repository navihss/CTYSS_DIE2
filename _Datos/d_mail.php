<?php

/**
 * Definición de la Capa de Datos para enviar Mails
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */
header('Content-Type: text/html; charset=UTF-8');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/assets/libs/PHPMailer/PHPMailer.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/assets/libs/PHPMailer/SMTP.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/assets/libs/PHPMailer/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Mail.php');

class d_mail
{
    //Enviamos el mail
    function Envair_Mail($obj_Mail)
    {
        $resultado = '';

        try {
            $correo = new PHPMailer(true);

            // Configuración básica
            $correo->CharSet = 'UTF-8';
            $correo->isSMTP();
            $correo->SMTPAuth = true;
            $correo->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            $correo->Host       = $obj_Mail->get_Host();
            $correo->Port       = $obj_Mail->get_Puerto();
            $correo->Username   = $obj_Mail->get_Username();
            $correo->Password   = $obj_Mail->get_Password();
            $correo->Timeout    = 30;

            // Remitente
            $correo->setFrom($obj_Mail->get_From(), $obj_Mail->get_Fromname());

            // Destinatarios
            if ($obj_Mail->get_Correo_Destinatarios() != '') {
                $arr_para = preg_split("/[,]/", $obj_Mail->get_Correo_Destinatarios());
                foreach ($arr_para as $email) {
                    if (trim($email) !== '') {
                        $correo->addAddress(trim($email));
                    }
                }
            }

            // Copias Ocultas (BCC)
            if ($obj_Mail->get_Correo_Copia_Oculta() != '') {
                $arr_cco = preg_split("/[,]/", $obj_Mail->get_Correo_Copia_Oculta());
                foreach ($arr_cco as $email) {
                    if (trim($email) !== '') {
                        $correo->addBCC(trim($email));
                    }
                }
            }

            // Asunto y cuerpo
            $correo->isHTML(true);
            $correo->Subject = $obj_Mail->get_Asunto();
            $correo->Body    = $obj_Mail->get_Mensaje();
            $correo->AltBody = strip_tags($obj_Mail->get_Mensaje());

            // Enviar correo
            if ($correo->send()) {
                $resultado = '<br><b>Correo enviado con éxito a: ' . $obj_Mail->get_Correo_Destinatarios() . '</b><br>';
            } else {
                $resultado = '<br>Error al enviar el correo: ' . $correo->ErrorInfo . '<br>';
            }

        } catch (Exception $e) {
            $resultado = '<br><b>Excepción al enviar correo:</b> ' . $e->getMessage() . '<br>';
        }

        return $resultado;

    } //Fin Enviar Mail

    //Enviamos el mail
    function Obtener_Correos($lista_Destinatarios)
    {

        try {
        } catch (Exception $ex) {
        }
    } //Fin Enviar Mail

}

//$obj = new d_mail();
//$mi_mail = new Mail();
//$mensaje= "
//        <table style='border : 1px solid blue;'>
//        <tr><td><b>Dear user,</b></td></tr>
//        <tr style='border : 1px solid blue;'><td>Click on the following link to reset your password:</td></tr>
//        <tr><td>http://www.example.com/users/lostpassword.php?</td></tr></table>";
//
//
//
//$mi_mail->set_Correo_Destinatarios('rogelioreyesm@prodigy.net.mx');
//$mi_mail->set_Asunto('Prueba');
//$mi_mail->set_Mensaje($mensaje);
//echo $obj->Envair_Mail($mi_mail);
<?php

/**
 * Definición de la Capa de Datos para enviar Mails
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */
    header('Content-Type: text/html; charset=UTF-8');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/Conexion.php');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/Php_Mailer524/class.phpmailer.php');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/Php_Mailer524/class.smtp.php');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Mail.php');
    
class d_mail {
    
    //Enviamos el mail
    function Envair_Mail($obj_Mail){
        $resultado = '';

            $mi_mail = new Mail();
            $mi_mail = $obj_Mail;                        
            
            $correo = new PHPMailer();
            $correo->CharSet= 'UTF-8';
            $correo->IsSMTP();
            $correo->IsHTML(true);
            $correo->SMTPAuth = true;
            $correo->SMTPSecure = 'tls';
            $correo->Timeout = 30;
            $correo->Host = $mi_mail->get_Host();
            $correo->Port = $mi_mail->get_Puerto();
            $correo->Username = $mi_mail->get_Username();
            $correo->Password = $mi_mail->get_Password();
            $correo->From = $mi_mail->get_From();
            $correo->FromName = $mi_mail->get_Fromname();
                       
//            $correo->AddAddress($mi_mail->get_Correo_Destinatarios());            
            if($mi_mail->get_Correo_Destinatarios() !=''){
                $arr_para = preg_split("/[,]/", $mi_mail->get_Correo_Destinatarios());
                $renglones = count($arr_para); 
                for($i=0; $i < $renglones; $i++ ){
                    if(!$correo->AddAddress($arr_para[$i])){
                        continue;
                    }
                }        
            }

            
            if($mi_mail->get_Correo_Copia_Oculta() !=''){
                $arr_destinatarios = preg_split("/[,]/", $mi_mail->get_Correo_Copia_Oculta());
                $renglones = count($arr_destinatarios); 
                for($i=0; $i < $renglones; $i++ ){
//                    $correo->AddBCC($arr_destinatarios[$i]);
                    if(!$correo->AddCC($arr_destinatarios[$i])){
                        continue;
                    }
                }        
            }
            
            $correo->Subject = $mi_mail->get_Asunto();
            $correo->Body = $mi_mail->get_Mensaje();
            if(!$correo->Send())
            {
                $resultado .= $correo->ErrorInfo;
                $resultado .= ' *** Error en el envio del correo electrónico <br>';
                $resultado .= 'To: ' . $mi_mail->get_Correo_Destinatarios() . ' ** ';
                $resultado .= 'Subject: ' . $mi_mail->get_Asunto() . ' ** ';
                $resultado .= 'Body: ' . $mi_mail->get_Mensaje();
            }
            else{
                $resultado .= 'Correo envíado. OK';
            }
            return $resultado;
    } //Fin Enviar Mail
    
    //Enviamos el mail
    function Obtener_Correos($lista_Destinatarios){
        
        try{                    

            
        }
        catch (Exception $ex){               

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
<?php

/**
 * Definición de la Clase Mail
 * Propiedades
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */

class Mail {
    
    /* Definición de Propiedades */
    private $correo_Destinatarios;
    private $correo_Copia_Oculta;
    private $asunto;
    private $mensaje;
    
    private $host;
    private $puerto;
    private $username;
    private $password;
    private $from;
    private $fromname;
    
    function set_Correo_Destinatarios($correoDestinatarios){
        $this->correo_Destinatarios=$correoDestinatarios;        
    }
    function get_Correo_Destinatarios(){
        return $this->correo_Destinatarios;
    }
    function set_Correo_Copia_Oculta($correoOculto){
        $this->correo_Copia_Oculta=$correoOculto;        
    }
    function get_Correo_Copia_Oculta(){
        return $this->correo_Copia_Oculta;
    }

    
    function set_Asunto($asunto){
        $this->asunto=$asunto;        
    }
    function get_Asunto(){
        return $this->asunto;
    }

    function set_Mensaje($mensaje){
        $this->mensaje = $mensaje;
    }
    function get_Mensaje(){
        return $this->mensaje;
    }  

    function get_Host(){
        return $this->host;
    }  

    function get_Puerto(){
        return $this->puerto;
    }  
    
    function get_Username(){
        return $this->username;
    }  

    function get_Password(){
        return $this->password;
    }  

    function get_From(){
        return $this->from;
    }  

    function get_Fromname(){
        return $this->fromname;
    }  
        
    function __construct()
    {
        $ini_array = parse_ini_file($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/Config/SMTP.ini');
        $this->host = $ini_array['host'];
        $this->puerto = $ini_array['puerto'];
        $this->username = $ini_array['username'];
        $this->password = $ini_array['password'];
        $this->from = $ini_array['from'];
        $this->fromname = $ini_array['fromname'];
    }    
}

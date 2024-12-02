<?php
//    require_once '../Config/ruta_Proyecto.php';
//    require_once $ruta_Proyecto . 'Config/Seguridad.php';
      include ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/Config/Seguridad.php');
      
    class Conexion
    {
            private $serverName;
            private $usuario_bd;
            private $password_bd;
            private $base_de_datos;
            private $puerto_bd;
            private $cnn;
            private $error_mensaje;

            function __construct()
            {
                    //obtenemos los parametros de la Aplicación
                    $ini_array = parse_ini_file($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/Config/Aplicacion.ini');
                    //configuramos el objeto conexión
                    $this->serverName = $ini_array['db_hostname'];
                    $this->usuario_bd = $ini_array['db_username'];
                    if($ini_array['db_password'] == ''){
                        $this->password_bd = '';
                    }else{
                        $this->password_bd = trim(Seguridad::desencriptar_aes($ini_array['db_password'], $ini_array['db_semilla']));
                    }                        
                    $this->base_de_datos =$ini_array['db_database'];		
                    $this->puerto_bd =$ini_array['db_puerto'];
                    
                    $cadena = "pgsql:" .
                              "host='" . $this->serverName . "' " .
                              "port='" . $this->puerto_bd . "' " .
                              "dbname='" . $this->base_de_datos . "' " .
                              "user='" . $this->usuario_bd . "' " .
                              "password='" . $this->password_bd . "'";
                    
                      try {
                          $this->cnn = new PDO($cadena);
                          $this->error_mensaje = '';
                      } catch (Exception $ex) {
                          $this->cnn = false;
                          $this->error_mensaje =  $cadena . " Error en los parametros de conexión a la Base de Datos.<br> " . utf8_encode($ex->getMessage());                          
                      }                    
            }
            public function getServerName()
            {
                    return $this->serverName;
            }
            public function getDataBase()
            {
                    return $this->base_de_datos;
            }
            public function getConexion()
            {
                    return $this->cnn;
            }
            public function getError(){
                    return $this->error_mensaje;
            }            
    }
    
//    $obj_cnn = new Conexion();
//    if($obj_cnn->getConexion()){
//        echo "conectado";
//    }else {
//        echo "sin conexion" . $obj_cnn->getError();
//    }
    
?>


<?php

header('Content-Type: text/html; charset=UTF-8');

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Usuario.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_Usuario.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Bitacora.php');

class d_Usuario {
    //Definición de Metodos
       
    function Agregar($objUsuario, $conn){
        $obj_Usuario = new Usuario();
        $obj_Usuario = $objUsuario;                
        
        $tsql1 = "INSERT INTO usuarios
        (id_usuario, 
         contrasena_usuario, 
         nombre_usuario, 
         apellido_paterno_usuario, 
         apellido_materno_usuario,
         fecha_alta_usuario,
         email_usuario,
         activo_usuario,
         id_tipo_usuario,
         id_tipo_baja,
         id_genero,
         id_division)
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
       
        /* Valor de los parámetros. */
        $params1 = array($obj_Usuario->get_Id_Usuario(), 
                 $obj_Usuario->get_Contrasena(), 
                 $obj_Usuario->get_Nombre(), 
                 $obj_Usuario->get_Apellido_Paterno(), 
                 $obj_Usuario->get_Apellido_Materno(),
                 $obj_Usuario->get_Fecha_Alta(),
                 $obj_Usuario->get_Correo_Electronico(),
                 $obj_Usuario->get_Activo(),
                 $obj_Usuario->get_Id_Tipo_Usuario(),
                 $obj_Usuario->get_Id_Tipo_Baja(),                 
                 $obj_Usuario->get_Id_Genero(),
                 $obj_Usuario->get_Id_Division());
                              
        /* Preparamos la sentencia a ejecutar */
        $stmt = $conn->prepare($tsql1);

        if ($stmt){
            /*Ejecutamos el Query*/
            $result = 0;
            $result = $stmt->execute($params1); 
            if ($result){
                if ($result == 0){
                    $error = $stmt->errorInfo();
                    return ("Error. " . $error[2]);
                }
                else{
                    return '';
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_error = "No se pudo Agregar la entidad Usuario.<br/>"  . $error[2] .'<br>';
                return ($mensaje_error);
            }
        }
        else{
            $error = $stmt->errorInfo();
            $mensaje_error = "Error en la sentencia SQL para Agregar al Usuario.<br>" . $error[2] .'<br>';
            return ($mensaje_error);
        }   
    }
     
    function Actualizar($objUsuario, $conn){
        $obj_Usuario = new Usuario();
        $obj_Usuario = $objUsuario;                
        
        $tsql1 = "UPDATE usuarios SET
                nombre_usuario = ?, 
                apellido_paterno_usuario = ?, 
                apellido_materno_usuario = ?,
                email_usuario = ?,
                id_genero = ?
                WHERE id_usuario = ?;";
       
        /* Valor de los par�metros. */
        $params1 = array($obj_Usuario->get_Nombre(), 
                 $obj_Usuario->get_Apellido_Paterno(), 
                 $obj_Usuario->get_Apellido_Materno(),
                 $obj_Usuario->get_Correo_Electronico(),
                 $obj_Usuario->get_Id_Genero(),
                 $obj_Usuario->get_Id_Usuario());
                
        /* Preparamos la sentencia a ejecutar */
        $stmt = $conn->prepare($tsql1);

        /*Ejecutamos el Query*/
        $result = 0;
        $result = $stmt->execute($params1); 
        
        if ($result == 0){            
            $error = $stmt->errorInfo();
            return ($error[2]);
        }
        else{
            return '';
        }                    
    }
    
    function Iniciar_Sesion($usr, $pass){    
        $conn = '';       
        $jsondata = array();

        try{        
            $cnn = new Conexion();               
            $conn = $cnn->getConexion();

            if( $conn === false ){
                $jsondata['success'] = false;
                throw new Exception($cnn->getError());     
            }            

            /* Consultamos los datos del Usuario    
            /* Query parametrizado. */
            $tsql = "SELECT  a.id_usuario, a.contrasena_usuario, a.id_tipo_usuario, a.id_division, d.descripcion_division,  a.nombre_usuario, 
                    		a.apellido_paterno_usuario, a.apellido_materno_usuario, a.activo_usuario, 
                    		b.descripcion_tipo_usuario, a.email_usuario
                    FROM usuarios a
                    INNER JOIN tipo_usuario b ON a.id_tipo_usuario = b.id_tipo_usuario
                    INNER JOIN divisiones d on d.id_division = a.id_division 
                    WHERE a.id_usuario= ? AND a.contrasena_usuario = ?;";
            /* Valor de los parámetros. */
            $params = array($usr, $pass);                       
           
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);

            if ($stmt){
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);             
                if ($result !== FALSE){
                    if($stmt->rowCount() > 0){
                        session_start();
                        $obj_Bitacora = new d_Usuario_Bitacora();
                        $obj_miBitacora = new Bitacora();
                        while($row = $stmt->fetch()){
                            if ($row['activo_usuario']){                    
                                $_SESSION["id_usuario"] = $row["id_usuario"];
                                $_SESSION["id_tipo_usuario"] = $row["id_tipo_usuario"];
                                $_SESSION["id_division"] = $row["id_division"];
                                $_SESSION["descripcion_division"] = $row["descripcion_division"];
                                $_SESSION["descripcion_tipo_usuario"] = $row["descripcion_tipo_usuario"];
                                $_SESSION["nombre_usuario"] = $row["nombre_usuario"] .' '.$row["apellido_paterno_usuario"].
                                        ' '.$row["apellido_materno_usuario"];
                                $_SESSION["correo_usuario_sesion"] = $row["email_usuario"];                                
                                
                                $descripcionEvento = ''; 
                                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                                $obj_miBitacora->set_Id_Tema_Bitacora(160);
                                $obj_miBitacora->set_Id_Tipo_Evento(35);
                                $obj_miBitacora->set_Id_Usuario_Genera($row["id_usuario"]);
                                $obj_miBitacora->set_Id_Usuario_Destinatario('');
                                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                                $obj_miBitacora->set_Id_Division($row["id_division"]);
                                
                                $obj_Bitacora->Agregar($obj_miBitacora);
                                
                                $jsondata['success'] = true;
                                $jsondata['data']['message'] = '';
                                echo json_encode($jsondata);
                                exit();                            
                            }
                            else {                        
                                $jsondata['success'] = false;
                                throw new Exception('La Cuenta '. $usr . ' está Inactiva.');                    
                            }                        
                        } //fin while          
                    }
                    else {
                        $jsondata['success'] = false;
                        throw new Exception('La Cuenta ' . $usr . ' NO EXISTE en la Base de Datos.');
                    }                                                                    
                }
                else {    
                    $error = $stmt->errorInfo();
                    $jsondata['success'] = 'ERROR';
                    throw new Exception('Error al ejecutar el Query.<br>' . $error[2]);
                }                            
            }
        } catch (Exception $ex) {            
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();
        }        
    } //Fin Iniciar_Sesion
    
    
    function Valida_NoCuenta($NoCta, $Id_Carrera){
        $htmlDGAE = file_get_contents("https://www.siass.unam.mx/consulta?numero_cuenta=" . 
                $NoCta . "&sistema_pertenece=dgae&facultad_id=11&carrera_id=" . $Id_Carrera);
	if (!strpos($htmlDGAE,'Ver detalle'))
	{
            return FALSE;
	}else{
            return TRUE;
        }        
        
    } //Fin Valida_NoCuenta
    
    
} //Fin Clase

//Para probar la Clase Usuario
//$obj_Usuario = new d_Usuario();
//echo $obj_Usuario->Iniciar_Sesion('086198516', '123');

//Para probar Valida_NoCuenta
//$obj_Usuario = new d_Usuario();
//echo $obj_Usuario->Valida_NoCuenta('305167716', 10);

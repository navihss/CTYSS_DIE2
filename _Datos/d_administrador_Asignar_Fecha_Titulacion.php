<?php

/**
 * Definición de la Capa de Datos para Asignar Fecha de titulacion a un usuario
 * Metodos
 * @author Carlos Alfonso Aguilar Castro
 * Julio 2024
 */
header('Content-Type: text/html; charset=UTF-8');

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Administrador.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Coordinador.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Profesor.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Alumno.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Usuario.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');

class d_administrador_Asignar_Fecha_Titulacion {
    
    function Existe_Clave_Usuario($clave, $id_division){
    
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
          
            $tsql = "SELECT u.id_usuario, (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario) as nombre,
                    ac.id_carrera, c.descripcion_carrera
                    FROM usuarios u
                    JOIN alumno_carrera ac on u.id_usuario = ac.id_alumno
                    JOIN carreras c on ac.id_carrera = c.id_carrera
                    WHERE u.id_usuario = ? and u.id_division = ?";
        
            /* Valor de los parámetros. */
            $params = array($clave, $id_division);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            /*Verificamos el contenido de la ejecución*/                        
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);                                 
                if ($result){                    
                    if($stmt->rowCount() > 0){                                                
                        $jsondata['success'] = 'EXISTE';
                        $jsondata['data']['message'] = 'Ya existe este usuario';
                        $jsondata['data']['registros'] = array();

                        while($row = $stmt->fetch(PDO::FETCH_OBJ)){
                            $jsondata['data']['registros'][] = $row;
                        }
                        
                        echo json_encode($jsondata);
                        exit();             
                    }
                    else {
                        $jsondata['success'] = 'NOEXISTE';
                        $jsondata['data']['message'] = 'No existe este usuario';
                        echo json_encode($jsondata);
                        exit();             
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $jsondata['success'] = 'ERROR';
                    $jsondata['data']['message'] = ($error[2]);
                    echo json_encode($jsondata);
                    exit();                             
                }                
            } 
            else {    
                $jsondata['success'] = 'ERROR';
                $jsondata['data']['message'] = 'Error al ejecutar el Query.';
                echo json_encode($jsondata);
                exit();       
            }
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();
        } 
    }  
    
    function Asignar_Fecha_Titulacion($id_usr, $id_carrera, $fecha_titulacion){
                
        try{                   
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
  
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
            /* Agregamos la Fecha Titulacion */ 
            /* Query parametrizado. */
                
            $tsql=" UPDATE alumno_carrera SET 
                    fecha_titulacion=? 
                    WHERE id_alumno = ?
                    AND id_carrera = ?";
	    /* Valor de los parámetros. */
	    $params = array($fecha_titulacion, $id_usr, $id_carrera);

            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);

            /*Ejecutamos el Query*/
            $result = 0;
            $result = $stmt->execute($params);   

            if( $result > 0 ) {                
                $jsondata['success'] = true;
                $jsondata['data']['message'] = 'Fecha de Titulacion registrada satisfactoriamente.';
                
                echo json_encode($jsondata);
                exit();
            } else {
                $error = $stmt->errorInfo();
		$jsondata['success'] = 'ERROR';
                $jsondata['data']['message'] = ($error[2]);
                echo json_encode($jsondata);
                exit();  
            }                                                 
        }
        catch (Exception $ex){     
            $jsondata['success'] = false;
	    $jsondata['data']= array('message'=>$ex->getMessage());
            echo json_encode($jsondata);
            exit();   
        }  
    }
}


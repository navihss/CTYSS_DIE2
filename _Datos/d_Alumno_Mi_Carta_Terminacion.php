<?php
use App\Database\Connection;
/**
 * Definición de la Capa de Datos para la Clase Mi Carta de Terminación
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */
    header('Content-Type: text/html; charset=UTF-8');
    require_once __DIR__ . '/../app/Database/Connection.php';
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');

class d_Alumno_Mi_Carta_Terminacion {

    //OBTENEMOS LAS HORAS REALIZADAS DE SERVICIO SOCIAL
    function Obtener_SS_Horas_Laboradas($id_alumno, $id_carrera){
        
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
           
            $tsql = "SELECT a.id_ss,  a.id_programa, a.fecha_inicio_ss, a.duracion_meses_ss, b.descripcion_estatus, 
                        (SELECT sum(horas_obligatorias)
				FROM reportes_bimestrales
				WHERE id_estatus <> 4AND id_ss=a.id_ss) as horas_obligatorias,
			(SELECT sum(horas_laboradas)
			FROM reportes_bimestrales
			WHERE id_estatus = 3 AND id_ss= a.id_ss) as horas_laboradas, 
			((SELECT sum(horas_obligatorias)
				FROM reportes_bimestrales
				WHERE id_estatus <> 4 AND id_ss=a.id_ss) - (SELECT sum(horas_laboradas)
			FROM reportes_bimestrales
			WHERE id_estatus = 3 AND id_ss= a.id_ss)) as horas_pendientes
                    FROM servicio_social a INNER JOIN estatus b 
                    ON a.id_estatus = b.id_estatus 
                    WHERE a.id_alumno = ? AND a.id_carrera = ? AND a.id_estatus IN (3,8)
                    ORDER BY id_ss;";
            
            /* Valor de los parámetros. */
            $params = array($id_alumno, $id_carrera);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            /*Verificamos el contenido de la ejecución*/                        
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);                                 
                if ($result){                    
                    if($stmt->rowCount() > 0){                                                
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['registros'] = array();

                        while($row = $stmt->fetch(PDO::FETCH_OBJ)){
                            $jsondata['data']['registros'][] = $row;
                        }
                        $stmt=null;
                        $conn=null;
                        echo json_encode($jsondata);
                        exit();
                    }
                    else{
                        $jsondata['success'] = false;
                        $jsondata['data']= array('message'=>'No hay información del Servicio Social.');
                        $stmt=null;
                        $conn=null;                        
                        echo json_encode($jsondata);
                        exit();                                                        
                    }
                }
            }
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener horas de Servicio Social
    

    function Obtener_Mis_Documentos($id_ss){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
           
            $tsql = "SELECT a.id_ss, a.id_documento, a.id_estatus, " .
                    "c.descripcion_documento, c.descripcion_para_nom_archivo, a.id_version, " .
                    "a.fecha_recepcion_doc, b.descripcion_estatus, a.nota, d.id_alumno, d.id_carrera " .
                    "FROM servicio_social_docs a " .
                    "INNER JOIN estatus b ON a.id_estatus = b.id_estatus " .
                    "INNER JOIN documentos c ON a.id_documento = c.id_documento " .
                    "INNER JOIN servicio_social d ON a.id_ss = d.id_ss " .
                    "WHERE a.id_ss = ? AND a.id_documento = 3 " .
                    "ORDER BY a.id_documento, a.id_version;";
                        
            /* Valor de los parámetros. */
            $params = array($id_ss);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            /*Verificamos el contenido de la ejecución*/                        
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);                                 
                if ($result){                    
                    if($stmt->rowCount() > 0){                                                
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['registros'] = array();

                        while($row = $stmt->fetch(PDO::FETCH_OBJ)){
                            $jsondata['data']['registros'][] = $row;
                        }
                        $stmt=null;
                        $conn=null;
                        echo json_encode($jsondata);
                        exit();
                    }
                    else{
                        $mensaje_Transacciones = "No hay información de los Documentos Enviados para el Servicio Social.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Documentos Enviados para el Servicio Social.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Mis Documentos Enviados CARTAS DE TERMINACION
    
      
}

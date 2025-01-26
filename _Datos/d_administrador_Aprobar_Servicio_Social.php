<?php
use App\Database\Connection;
/**
 * Definición de la Capa de Datos para la Autorización de las Cartas de Aceptación
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */
    header('Content-Type: text/html; charset=UTF-8');
    require_once __DIR__ . '/../app/Database/Connection.php';
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');

class d_administrador_Aprobar_Servicio_Social {

    //Obtenemos los Servios Sociales pendientes de Autorizar y que tienen ambos documentos enviados
    //Carta de Aceptación e Historial Académico con estatus 2. Por Autorizar
    function Obtener_SS_Por_Estatus($id_estatus, $id_division){
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT  a.id_estatus, a.id_ss, a.id_alumno,
                    (c.nombre_usuario || ' ' || c.apellido_paterno_usuario || ' ' || c.apellido_materno_usuario) as nombre, 
                    f.descripcion_carrera, d.descripcion_estatus, a.fecha_inicio_ss, a.duracion_meses_ss, 
                    a.avance_creditos_ss, a.avance_porcentaje_ss, a.jefe_inmediato_ss, 
                    e.descripcion_tipo_remuneracion, a.id_programa, c.email_usuario, a.id_carrera,
                    g.descripcion_pss, h.telefono_fijo_alumno, h.telefono_celular_alumno
                    FROM servicio_social a
                        INNER JOIN usuarios c ON a.id_alumno = c.id_usuario
                        INNER JOIN estatus d ON a.id_estatus = d.id_estatus
                        INNER JOIN tipo_remuneracion e ON a.id_tipo_remuneracion = e.id_tipo_remuneracion
                        INNER JOIN carreras f ON a.id_carrera = f.id_carrera
                        INNER JOIN programas_ss g ON a.id_programa = g.id_programa
                        INNER JOIN alumnos h ON c.id_usuario = h.id_alumno
                    WHERE a.id_estatus = ? AND (SELECT COUNT(id_documento) FROM servicio_social_docs WHERE id_ss= a.id_ss AND id_estatus=2) > 0
                      AND g.id_division = ?
                    ORDER BY a.id_ss;";
                        
            /* Valor de los parámetros. */
            $params = array($id_estatus, $id_division);
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
                        $mensaje_Transacciones = "No hay ningún Servicio Social por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Servicios Sociales por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Documentos        
    

    //OBTENERMOS TOTAL DE APROBACION DE SERVICIOS
    function Obtener_Total_SS($id_estatus, $id_division){
        try{
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if($cnn === false)
            {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT count(a.id_ss) as total2
            FROM servicio_social a
                        INNER JOIN usuarios c ON a.id_alumno = c.id_usuario
                        INNER JOIN estatus d ON a.id_estatus = d.id_estatus
                        INNER JOIN tipo_remuneracion e ON a.id_tipo_remuneracion = e.id_tipo_remuneracion
                        INNER JOIN carreras f ON a.id_carrera = f.id_carrera
                        INNER JOIN programas_ss g ON a.id_programa = g.id_programa
                        INNER JOIN alumnos h ON c.id_usuario = h.id_alumno
                    WHERE a.id_estatus = ? and f.id_division = ?";
        
        /* Valor de los parámetros. */
            $params = array($id_estatus, $id_division);
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

                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            $jsondata['data']['registros'][] = $row;
                        }
                        $stmt=null;
                        $conn=null;
                        //echo json_encode($jsondata);
                        return ($jsondata);
                        //exit();
                    }
                    else{
                        $mensaje_Transacciones = "No hay ningún Servicio Social por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Servicios Sociales por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }
    }
    //FIN OBTENEMOS TOTAL SS

    //Obtenemos los datos del Documento Seleccionado por el Administrador para su visualización PDF
    function Obtener_Documento($id_ss, $id_documento, $id_estatus, $id_division){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
            
            $tsql = "SELECT a.id_ss, a.id_documento, a.id_estatus, c.id_alumno, a.id_version, 
                a.fecha_recepcion_doc, d.descripcion_documento, b.descripcion_estatus
                FROM servicio_social_docs a 
                        INNER JOIN estatus b ON a.id_estatus = b.id_estatus
                        INNER JOIN servicio_social c ON a.id_ss = c.id_ss
                        INNER JOIN documentos d ON a.id_documento = d.id_documento
                WHERE a.id_ss = ? AND a.id_documento = ?
			AND a.id_estatus IN (2,3,4) AND a.id_version = (SELECT max(e.id_version)
									FROM servicio_social_docs e
									WHERE e.id_ss= a.id_ss AND e.id_documento = a.id_documento)
									AND c.id_division = ?";
            /* Valor de los parámetros. */
            $params = array($id_ss, $id_documento, $id_division);
            
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
                        $mensaje_Transacciones = "No hay Documento por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "Error en los parámetros para obtener los Documentos por Autorizar.<br/>"  . $error[2];
                    throw new Exception($mensaje_Transacciones);                  
                }                
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Documentos por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Documento
    
    
}

?>
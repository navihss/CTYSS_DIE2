<?php
/**
 * Definición de la Capa de Datos para la Clase Catálogo de Coordinaciones y Jefes de Dpto
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */

header('Content-Type: text/html; charset=UTF-8');

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');

class d_administrador_admon_Coord_Dptos {
    
    //ACTUALIZAMOS LA ASIGNACION DE COORDINACION / DPTO
    function Actualizar_Coord_Dpto($tipo_catalogo, $id_jefe_actual, $id_jefe_nuevo, $nom_nvo_jefe, $id_administrador, $id_division){

        $mensaje_Transacciones = '';
        $nom_Area ='';
        $jsondata = array();
                
        try{    

//             $jsondata['success'] = false;
//            $jsondata['data']['message'] = 'en da';
//            echo json_encode($jsondata);
//            exit();  
            
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
                           
            $conn->beginTransaction();
            
            if($tipo_catalogo == 'C'){
                $nom_Area = "Coordinación";
                $tsql=" UPDATE jefes_coordinacion SET ".
                        "actual_jefe = '0' ".
                        "WHERE id_coordinador = ?;";                    
                $params = array($id_jefe_actual);            
                
                $tsql2=" UPDATE jefes_coordinacion SET ".
                        "actual_jefe = '1' ".
                        "WHERE id_coordinador = ?;";                        
                $params2 = array($id_jefe_nuevo);                        
                
            }
            else{
                $nom_Area = "Departamento";
                $tsql=" UPDATE jefes_departamento SET ".
                        "actual_jefe = '0' ".
                        "WHERE id_jefe_departamento = ?;";                        
                $params = array($id_jefe_actual);                        
                
                $tsql2=" UPDATE jefes_departamento SET ".
                        "actual_jefe = '1' ".
                        "WHERE id_jefe_departamento = ?;";                        
                $params2 = array($id_jefe_nuevo);                        
                
            }

            if($id_jefe_actual != '0'){
                $stmt = $conn->prepare($tsql);
                if($stmt){
                    $result = $stmt->execute($params); 
                    if ($result){
                        if($stmt->rowCount() > 0){                    
                            $mensaje_Transacciones .= "Jefatura Actualizada. OK.<br/>";
                        }
                        else{
                            $error = $stmt->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Actualizar la Jefatura.<br>"  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se actualizó la Jefatura.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }
                }
                else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar la Jefatura.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }
            }

            $stmt2 = $conn->prepare($tsql2);
            if($stmt2){
                $result2 = $stmt2->execute($params2); 
                if ($result2){
                    if($stmt2->rowCount() > 0){                    
                        $mensaje_Transacciones .= "Jefatura Actualizada. OK.<br/>";
                    }
                    else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Actualizar la Jefatura.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "No se actualizó la Jefatura.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar la Jefatura.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }
            
            $conn->commit();
            
            $id_tema_evento = 130; //Admon. de Coordinaciones y Dptos.
            $id_tipo_evento = 15; // Actualización
            $descripcion_evento = "Cambio de Jefe de " . $nom_Area . ". *** Se asignó la Jefatura a: " . $nom_nvo_jefe .
                    " *** Con Clave : " . $id_jefe_nuevo;

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_evento;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento);
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);              
            
            
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora;
            echo json_encode($jsondata);
            exit();                 
        }
        catch (Exception $ex){   
            $conn->rollBack();
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();   
        }   
    }  
    //FIN ACTUALIZAMOS LA ASIGNACION DE COORDINACION / DPTO    
    
    //*********************************************************************                
    //OBTENEMOS COORD/DPTOS Y SUS JEFES ACTUALES
    function Obtener_Area_Jefes_Actuales($tipo_catalogo, $id_division){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            //OBTENEMOS LAS COORD O DPTOS
            $titulo_area='';
            if($tipo_catalogo == 'C'){  //Coordinaciones
            $tsql = " SELECT a.id_coordinacion as id, a.descripcion_coordinacion as descripcion, a.id_carrera, b.descripcion_carrera
                                FROM coordinaciones a
                                        INNER JOIN carreras b ON a.id_carrera = b.id_carrera and a.id_division = ?
                        ORDER BY b.descripcion_carrera, a.descripcion_coordinacion;";
            $titulo_area = "Coordinaciones";
            }
            else{
            $tsql = " SELECT a.id_departamento as id, a.descripcion_departamento as descripcion, a.id_carrera, b.descripcion_carrera
                                FROM departamentos a
                                        INNER JOIN carreras b ON a.id_carrera = b.id_carrera and a.id_division = ?
                        ORDER BY b.descripcion_carrera, a.descripcion_departamento;";
            $titulo_area = "Departamentos";
            }
                                        
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            $params = array($id_division);
            /*Verificamos el contenido de la ejecución*/                        
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);                                 
                if ($result){                    
                    if($stmt->rowCount() > 0){                                                
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['areas'] = array();

                        while($row = $stmt->fetch(PDO::FETCH_OBJ)){
                            $jsondata['data']['areas'][] = $row;
                        }
                    }
                    else{
                        $mensaje_Transacciones = "No hay información de " . $titulo_area . ".";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "No se pudo obtener la información de " . $titulo_area . ".<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);                                                                                                    
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener información de " . $titulo_area . ".<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            } 
                        
            //OBTENEMOS LOS JEFES ACTUALES DEL AREA SELECCIONADA
            if($tipo_catalogo == 'C'){  //Coordinaciones
                $tsql2 = "  SELECT a.id_coordinador as id_jefe, a.id_coordinacion as id, a.actual_jefe,
                                    (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) as nombre
                            FROM jefes_coordinacion a
                                    INNER JOIN usuarios b ON a.id_coordinador = b.id_usuario
                            WHERE a.actual_jefe = '1'
                            ORDER BY a.id_coordinador;";
                $titulo_area = "Jefes de Coordinación";
            }
            else{
                $tsql2 = "  SELECT a.id_jefe_departamento as id_jefe, a.id_departamento as id, a.actual_jefe,
                                    (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) as nombre
                            FROM jefes_departamento a
                                    INNER JOIN usuarios b ON a.id_jefe_departamento = b.id_usuario
                            WHERE a.actual_jefe = '1'
                            ORDER BY a.id_jefe_departamento;";                
                $titulo_area = "Jefes de Departamento";
            }
            
            /* Preparamos la sentencia a ejecutar */
            $stmt2 = $conn->prepare($tsql2);
            $params2 = array();
            /*Verificamos el contenido de la ejecución*/                        
            if($stmt2){        
                /*Ejecutamos el Query*/
                $result2 = $stmt2->execute($params2);                                 
                if ($result2){                    
                    if($stmt2->rowCount() > 0){                                                
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['jefes_actuales'] = array();

                        while($row = $stmt2->fetch(PDO::FETCH_OBJ)){
                            $jsondata['data']['jefes_actuales'][] = $row;
                        }
                    }
                    else{
//                        $mensaje_Transacciones = "No hay información de ". $titulo_area . ".";
                        $jsondata['data']['jefes_actuales'] = array();                                                                                                        
                    }
                }
                else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones = "No se pudo obtener la información de ". $titulo_area . ".<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);                                                                                                    
                }
            }
            else{
                $error = $stmt2->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener información de ". $titulo_area . ".<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }  
            
           $jsondata['success'] = true;
           echo json_encode($jsondata);
           exit();             
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //FIN OBTENER AREAS JEFES ACTUALES
    //********************************************************************* 
    
}

//$obj = new d_administrador_admon_Coord_Dptos();
//echo $obj->Obtener_Area_Jefes_Actuales('C');
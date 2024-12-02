<?php
/**
 * Definición de la Capa de Datos para la Autorización de los Reportes Bimestrales
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */

header('Content-Type: text/html; charset=UTF-8');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_mail.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Mail.php');


class d_administrador_Aprobar_Reporte_Bimestral {
  
   
    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Reportes_x_Autorizar($id_estatus, $id_division){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
          
            $tsql = "SELECT c.id_alumno,c.id_carrera, d.email_usuario, a.id_ss, a.numero_reporte_bi, 
                    a.fecha_prog_inicio, a.fecha_prog_fin, a.id_version, a.id_estatus,
                    a.fecha_recepcion_rpt, a.fecha_real_inicio, a.fecha_real_fin, 
                    a.horas_laboradas, b.descripcion_estatus, a.horas_obligatorias, a.nota,
                    e.descripcion_documento, e.descripcion_para_nom_archivo
                    FROM reportes_bimestrales a 
			INNER JOIN estatus b ON a.id_estatus = b.id_estatus 
			INNER JOIN servicio_social c ON a.id_ss = c.id_ss
                        INNER JOIN usuarios d ON c.id_alumno = d.id_usuario
                        INNER JOIN documentos e ON a.id_documento = e.id_documento
                    WHERE a.id_estatus = ? and a.id_division = ?;";
                        
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
						//return ($jsondata);
                        echo json_encode($jsondata);
                        exit();
                    }
                    else{
                        $mensaje_Transacciones = "No hay información de Reportes Bimestrales por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes Bimestrales por Autorizar.<br/>"  . $error[2];
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
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR 

    //OBTENER TOTAL DE REPORTES PENDIENTES
    function Obtener_Total_Reportes_x_Autorizar($id_estatus, $id_division){
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
          
            $tsql = "SELECT count(a.id_estatus) as total2
                    FROM reportes_bimestrales a 
            INNER JOIN estatus b ON a.id_estatus = b.id_estatus 
            INNER JOIN servicio_social c ON a.id_ss = c.id_ss
                        INNER JOIN usuarios d ON c.id_alumno = d.id_usuario
                        INNER JOIN documentos e ON a.id_documento = e.id_documento
                    WHERE a.id_estatus = ? and a.id_division = ?;";
                        
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
                        //exit();
                        return ($jsondata);
                    }
                    else{
                        $mensaje_Transacciones = "No hay información de Reportes Bimestrales por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes Bimestrales por Autorizar.<br/>"  . $error[2];
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
    //FIN OBTENER TOTAL DE REPORTES

    //ACTUALIZAMOS EL ESTATUS DEL REPORTES BIMESTRAL
    function Actualizar_Aceptacion_Reporte_Bimestral($id_SS, $numero_reporte_bi, $id_Version, 
            $id_Estatus, $id_administrador, $nota, $fecha_prog_inicio, $fecha_prog_fin, $horas_obligatorias,
            $id_Usr_Destinatario, $correo_usr, $carrera_usr, $desc_documento, $id_tema_bitacora, $id_division){
        
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();

        $id_tipo_evento = 25; //aprobación
        $id_tema_evento = $id_tema_bitacora; 
        $descripcion_evento = "Documento " .$desc_documento. " No. " . $numero_reporte_bi . " Versión " . $id_Version . 
                " ** SERV SOC. " . $id_SS . " ** CARRERA " . $carrera_usr ;
        $descripcion_Correo = "Su Documento " .$desc_documento. " No. " . $numero_reporte_bi . " Versión " . $id_Version . 
                "<br>** Con Serv Soc " . $id_SS . " ** Y Carrera " . $carrera_usr . ' ha sido <b>ACEPTADO</b> <br>';
        
        try{                
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
            
            /*Iniciar la transacción. */
            $conn->beginTransaction();
                        
            //Actualizamos el estatus del Reporte especificado
            /* Query parametrizado. */
            $tsql=" UPDATE reportes_bimestrales SET " .
                    "id_estatus =  ?, " .
                    "fecha_verifico_rpt = ?, " .
                    "nota = ?, " .
                    "id_administrador = ? " .
                    "WHERE id_ss = ? AND " .
                    "numero_reporte_bi =? AND id_version = ?;";                    
            /* Valor de los parámetros. */
            $params = array(
                        $id_Estatus, date('d-m-Y H:i:s'),
                        $nota, $id_administrador,
                        $id_SS, $numero_reporte_bi, $id_Version);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result){
                    if($stmt->rowCount() > 0){                    
                        $mensaje_Transacciones .= "Estatus Actualizado. OK.<br/>";
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Actualizar el Estatus del Reporte Bimestral<br>."  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se actualizó el Estatus del Reporte Bimestral.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Estatus del Reporte Bimestral.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }

            //Agregamos una nueva versión del Reporte Rechazado para que el Alumno Reenvíe el Reporte
            if ($id_Estatus ==4){ //4. Rechazado
                $id_tipo_evento = 30;    //rechazo
                $descripcion_evento = "Documento " . $desc_documento. " No. " . $numero_reporte_bi . " Versión " . $id_Version . 
                        " ** SERV SOC. " . $id_SS . " ** CARRERA " . $carrera_usr ;
                $descripcion_Correo = "Su Documento " .$desc_documento. " No. " . $numero_reporte_bi . " Versión " . $id_Version . 
                " <br>Con Serv Soc " . $id_SS . " Y Carrera " . $carrera_usr . ' ha sido <b>RECHAZADO</b> ' .
                " <br>Se ha generado un nuevo número de versión para que pueda reenvíarlo. <br>";
                
                /* Query parametrizado. */
                $tsql2="INSERT INTO reportes_bimestrales(
                            id_ss, numero_reporte_bi, id_version,
                            fecha_prog_inicio, fecha_prog_fin, horas_laboradas, horas_obligatorias, 
                            nota, id_estatus, id_documento, id_division) 
                        VALUES(?,?,?,?,?,?,?,?,?,?,?)";
                    
                /* Valor de los parámetros. */
                $params2 = array($id_SS, $numero_reporte_bi, $id_Version + 1,
                    $fecha_prog_inicio, $fecha_prog_fin, 0, $horas_obligatorias, '', 1, 7, $id_division);
                /* Preparamos la sentencia a ejecutar */
                $stmt2 = $conn->prepare($tsql2);
                if($stmt2){
                    /*Ejecutamos el Query*/                
                    $result2 = $stmt2->execute($params2); 
                    if ($result2){
                        if($stmt2->rowCount() > 0){                    
                            $mensaje_Transacciones .= "Nuevo Reporte Agregado para su Reenvío. OK.<br/>";
                        }
                        else{
                            $error = $stmt2->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Agregar el Nuevo Reporte para su Reenvío<br>."  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                    }
                    else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "No se puedo Agregar el Nuevo Reporte para su Reenvío.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }
                }
                else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Agregar el Nuevo Reporte para su Reenvío.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }     
                //BORRAMOS EL ARCHIVO PDF
                $nom_archivo = $id_Usr_Destinatario.'_'.
                               $carrera_usr.'_'.
                               $id_SS.'_'.
                               $numero_reporte_bi.'_'.
                               $id_Version.'_'.
                               $desc_documento.'.pdf';
                $nom_archivo = $_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/Docs/Reportes_Bimestrales/'.$nom_archivo;
                if (file_exists($nom_archivo)) {
                    unlink($nom_archivo);   
                }                
            }
            
            $conn->commit();

            //  Estatus = 4 significa rechazado.
            if($id_Estatus == 4) {
                $mensaje_Transacciones = "Reporte bimestral rechazado.";
            } else {
                $mensaje_Transacciones = "Reporte bimestral aceptado.";
            }
            
            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_evento . ' --- ' . $nota;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento);
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_Usr_Destinatario);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);            
            sleep(1);
            
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_Correo . ' --- ' . $nota;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento(50);
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_Usr_Destinatario);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora); 

            $obj = new d_mail();
            $mi_mail = new Mail();
            $mensaje= $descripcion_Correo . ' --- ' . $nota;
                                   
            $mi_mail->set_Correo_Destinatarios($correo_usr);
            $mi_mail->set_Asunto('AVISO REVISIÓN DE REPORTE BIMESTRAL');
            $mi_mail->set_Mensaje($mensaje);
            $respuesta_mail = $obj->Envair_Mail($mi_mail);
     
            $conn = null;
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora;// . $respuesta_mail;
            echo json_encode($jsondata);
            exit();                 
        }
        catch (Exception $ex){
            $conn->rollBack();
            $conn = null;
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();   
        }   
    }    
    //FIN ACTUALIZAMOS EL ESTATUS DEL REPORTES BIMESTRAL
    
}

//    $obj = new d_administrador_Aprobar_Reporte_Bimestral();
//    echo  $obj->Obtener_Reportes_x_Autorizar(2);

    
?>

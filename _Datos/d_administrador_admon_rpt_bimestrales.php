<?php

header('Content-Type: text/html; charset=UTF-8');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_mail.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Mail.php');   

class d_administrador_admon_rpt_bimestrales {

    function Obtener_Reportes_Bimestrales($id_ss, $id_division){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
   
            $tsql = "SELECT a.id_ss, a.numero_reporte_bi, 
                            a.fecha_prog_inicio, a.fecha_prog_fin, a.id_version, a.id_estatus,
                            a.fecha_real_inicio, a.fecha_real_fin,
                            a.fecha_recepcion_rpt, a.horas_laboradas, a.nota, a.id_estatus, b.descripcion_estatus, horas_obligatorias,
                            c.id_alumno, c.id_carrera, d.email_usuario
                    FROM reportes_bimestrales a INNER JOIN estatus b ON a.id_estatus = b.id_estatus 
                            INNER JOIN servicio_social c ON a.id_ss = c.id_ss
                            INNER JOIN usuarios d ON c.id_alumno = d.id_usuario
                    WHERE a.id_ss= ? AND a.id_version IN (SELECT max(b.id_version)
                                                                    FROM reportes_bimestrales b
                                                                    WHERE b.id_ss= a.id_ss AND b.numero_reporte_bi = a.numero_reporte_bi)
                      AND c.id_division = ?
                    ORDER BY a.numero_reporte_bi, a.id_version;";
                        
            /* Valor de los parámetros. */
            $params = array($id_ss, $id_division);
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
                        $mensaje_Transacciones = "No hay información del Calendario de Entregas para este Servicio Social.". $id_ss."<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Calendario de Entregas para este Servicio Social.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Reportes Bimestrales
    
   //AGREGAMOS UN REPORTE 
   function Agregar_Reporte($id_SS, $numero_reporte_bi,  
            $fecha_prog_inicio, $fecha_prog_fin, $horas_obligatorias, $nota, 
           $id_administrador, $correo_administrador,$id_alumno,$correo_usr, $id_division){

        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
  
        try{    
            
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
                            
            //Agregamos el Reportes Bimestrales a la BD
            $tsql2=" INSERT INTO reportes_bimestrales(id_ss, numero_reporte_bi, id_version,
                    fecha_prog_inicio, fecha_prog_fin, horas_laboradas, horas_obligatorias, 
                    nota, id_estatus, id_documento, id_division) VALUES(
                    ?,?,?,?,?,?,?,?,?,?,?);";

            /* Valor de los parámetros. */
            $params2 = array($id_SS, $numero_reporte_bi, 1,
                $fecha_prog_inicio, $fecha_prog_fin, 0, $horas_obligatorias, $nota, 1, 7, $id_division);
            /* Preparamos la sentencia a ejecutar */
            $stmt2 = $conn->prepare($tsql2);
            if($stmt2){
                /*Ejecutamos el Query*/                
                $result2 = $stmt2->execute($params2); 
                if ($result2){
                    if($stmt2->rowCount() > 0){                    
                        $mensaje_Transacciones .= "Nuevo Reporte Agregado. OK.<br/>";
                    }
                    else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Agregar el Nuevo Reporte.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "No se pudo Agregar el Nuevo Reporte.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Agregar el Nuevo Reporte.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }                       
                
            //AGREGAMOS EL MOVIMIENTO DEL ADMIN A LA BITACORA
                $obj_Bitacora = new d_Usuario_Bitacora();
                $obj_miBitacora = new Bitacora();

                $descripcionEvento = 'Se agregó un Nuevo Reporte Bimestral. *** Alumno: '. $id_alumno .
                        ' *** Servicio Social: ' . $id_SS . ' *** Reporte No. ' . $numero_reporte_bi .
                        ' *** Versión: 1 ' . ' *** Fecha de Inicio: ' . $fecha_prog_inicio .
                        ' *** Fecha de Término: ' . $fecha_prog_fin .
                        ' *** Horas Obligatorias: ' . $horas_obligatorias . 
                        ' --- Nota: ' . $nota;
                $descripcionCorreo = '<b>Se agregó un Nuevo Reporte Bimestral. </b><br>*** Alumno: '. $id_alumno .
                        '<br> *** Servicio Social: ' . $id_SS . 
                        '<br> *** Reporte No. ' . $numero_reporte_bi . ' *** Versión: 1 ' . 
                        '<br> *** Fecha de Inicio: ' . $fecha_prog_inicio .
                        '<br> *** Fecha de Término: ' . $fecha_prog_fin .
                        '<br> *** Horas Obligatorias: ' . $horas_obligatorias . 
                        '<br> --- Nota: ' . $nota;
                
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora(125);//admon de rpts bimestrales
                $obj_miBitacora->set_Id_Tipo_Evento(5); //alta
                $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
                $obj_miBitacora->set_Id_Usuario_Destinatario('');
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($id_division);

                $resultado_Bitacora ='';
                $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
            
            $obj = new d_mail();
            $mi_mail = new Mail();
            $mensaje= $descripcionCorreo;
                                   
            $mi_mail->set_Correo_Destinatarios($correo_usr);
            $mi_mail->set_Correo_Copia_Oculta($correo_administrador);
            $mi_mail->set_Asunto('AVISO REVISIÓN DE REPORTES BIMESTRALES');
            $mi_mail->set_Mensaje($mensaje);
            $respuesta_mail = $obj->Envair_Mail($mi_mail);
                        
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora . $respuesta_mail;
            echo json_encode($jsondata);
            exit();                 
        }
        catch (Exception $ex){     
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();   
        }
    } 
    //FIN AGREGAR REPORTE
}
//$obj = new d_administrador_admon_rpt_bimestrales();
//echo $obj->Obtener_Reportes_Bimestrales('201603-022');
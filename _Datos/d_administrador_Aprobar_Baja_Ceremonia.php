<?php
use App\Database\Connection;
/**
 * Definición de la Capa de Datos para la Autorización la baja de ceremonia
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Septiembre 2016
 */
    header('Content-Type: text/html; charset=UTF-8');
    require_once __DIR__ . '/../app/Database/Connection.php';
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_mail.php');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Mail.php');       
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
    
class d_administrador_Aprobar_Baja_Ceremonia {

  //Obtenemos las Solicitudes de Baja de Ceremonia pendientes de Autorizar 
    function Obtener_Solicitudes_De_Baja(){
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
          
            $tsql = "SELECT a.id_ceremonia, a.id_alumno, a.id_carrera, a.nota_baja, c.descripcion_tipo_propuesta, email_usuario,
                            (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) as nombre
                    FROM inscripcion_ceremonia a
                        INNER JOIN usuarios b ON a.id_alumno = b.id_usuario
                        INNER JOIN tipos_propuesta c ON a.id_tipo_propuesta = c.id_tipo_propuesta
                    WHERE id_estatus = 15;";
                        
            /* Valor de los parámetros. */
            $params = array();
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
                        $mensaje_Transacciones = "No hay Solicitudes de Baja de Ceremonia por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener las Solicitudes de Baja de Ceremonia por Autorizar.<br/>"  . $error[2];
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
    //Fin Obtener Solicitudes de Baja de ceremonia
    
      function Actualizar_Estatus_Ceremonia($id_ceremonia, $id_usuario, $id_estatus, $id_tipo_baja, 
              $nota, $id_carrera, $id_administrador, $correo_usr, $id_division){
          
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $descripcion_Correo = '';
                
        try{    
            $cnn = new Connection();
            $conn = $cnn->getConexion();
                           
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
          
            $nvo_id_estatus=0;
            $id_tipo_Evento = 0;
            if($id_estatus == 3){ //aceptada
                $nvo_id_estatus = 13; //Baja autorizada por el administrador
                $id_tipo_Evento = 25; //Aprobación
                $descripcion_Correo = 'SOLICITUD DE BAJA DE CEREMONIA <br>'.
                        ' *** Clave Ceremonia : ' . $id_ceremonia . '<br>'.
                        ' *** Carrera : ' . $id_carrera . '<br>'.
                        ' *** No. de Cta. : ' . $id_usuario . '<br>'.
                        ' *** APROBADA';
            }
            else{
                $id_tipo_Evento = 30; //Rechazo
                $descripcion_Correo = 'SOLICITUD DE BAJA DE CEREMONIA <br>'.
                        ' *** Clave Ceremonia : ' . $id_ceremonia . '<br>'.
                        ' *** Carrera : ' . $id_carrera . '<br>'.
                        ' *** No. de Cta. : ' . $id_usuario . '<br>'.
                        ' *** RECHAZADA';
                
                $nvo_id_estatus = 3; //Se regresa el estatus a Aceptado
            }
            
            /* Iniciar la transacción. */
            $conn->beginTransaction();
            
            /*Cambiamos el Estatus de los Documentos de la Ceremonia */                
            if($id_estatus == 3){ //fue Aceptada la Solicitud de Baja
                $tsql1="UPDATE ceremonia_docs 
                        SET id_estatus = ?
                        WHERE id_ceremonia = ?;";

                $params1 = array($nvo_id_estatus, $id_ceremonia);

                /* Preparamos la sentencia a ejecutar */
                $stmt1 = $conn->prepare($tsql1);
                if($stmt1){
                    /*Ejecutamos el Query*/
                    $result1 = $stmt1->execute($params1);                                 
                    $mensaje_Transacciones .= "Cambio de Estatus a los Documentos de la Ceremonia. OK<br/>";
                    }
                else {
                        $error = $stmt1->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Cambiar el Estatus de los Documentos de Aceptación de Ceremonia.<br/>"  . $error[2];
                        throw new Exception($mensaje_Transacciones);        
                    }                                        
            }
            /*Cambiamos el Estatus de la Ceremonia*/                
            $tsql3="UPDATE inscripcion_ceremonia
                    SET id_estatus = ?,
                    id_tipo_baja = ?,
                    nota_baja = ?,
                    fecha_baja = ?
                    WHERE id_ceremonia = ?;";
            $params3 = array( $nvo_id_estatus, $id_tipo_baja, $nota, date('d-m-Y H:i:s'), $id_ceremonia);

            /* Preparamos la sentencia a ejecutar */
            $stmt3 = $conn->prepare($tsql3);
            if($stmt3){
                /*Ejecutamos el Query*/
                $result3 = $stmt3->execute($params3);                                 
                $mensaje_Transacciones .= "Cambio de Estatus de la Ceremonia. OK<br/>";
                }
            else {
                    $error = $stmt3->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Cambiar el Estatus de la Ceremonia.<br/>"  . $error[2];
                    throw new Exception($mensaje_Transacciones);        
                }                                        
                
            $conn->commit();
            
            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = 'REF. CARRERA/CEREMONIA ' . $id_carrera . "/". $id_ceremonia . ' --- ' . $nota;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(122); // Admon. Aprobar Baja de Ceremonia
            $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_Evento); 
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_usuario);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
            sleep(1);
               
            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_Correo . ' --- ' . $nota;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(122); // Admon. Aprobar Baja de Ceremonia
            $obj_miBitacora->set_Id_Tipo_Evento(50); //ENVIÓ MAIL 
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_usuario);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
            
            //MANDAMOS EL MAIL
            $obj = new d_mail();
            $mi_mail = new Mail();
            $mensaje= $descripcion_Correo . ' --- ' . $nota;
                                   
            $mi_mail->set_Correo_Destinatarios($correo_usr);
            $mi_mail->set_Correo_Copia_Oculta('');
            $mi_mail->set_Asunto('AVISO SOLICITUD DE BAJA DE CEREMONIA');
            $mi_mail->set_Mensaje($mensaje);
            $respuesta_mail = $obj->Envair_Mail($mi_mail);
            
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora . $respuesta_mail;
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
}

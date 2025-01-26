<?php
use App\Database\Connection;
/**
 * Definición de la Capa de Datos para la Clase Mi Jurado
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */
    header('Content-Type: text/html; charset=UTF-8');
    require_once __DIR__ . '/../app/Database/Connection.php';
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_coord_jdpto_Aprobar_Propuesta.php');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_mail.php');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Mail.php');   
    
class d_Alumno_Mi_Jurado {
    
    function Obtener_Mi_Jurado($id_alumno, $id_carrera){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
           
            $tsql = "SELECT a.id_propuesta, a.version, a.fecha_propuesto, a.id_alumno_registro,
                            a.id_administrador_definitivos, a.id_estatus, b.descripcion_estatus,
                            d.version_propuesta, e.id_alumno, c.titulo_propuesta,
                            (f.apellido_paterno_usuario || ' ' || 
                            f.apellido_materno_usuario || ' ' || f.nombre_usuario) as nombre
                    FROM jurado a
                            INNER JOIN estatus b ON a.id_estatus = b.id_estatus
                            INNER JOIN propuestas_profesor c ON a.id_propuesta = c.id_propuesta
                            INNER JOIN propuesta_version d ON c.id_propuesta = d.id_propuesta
                            INNER JOIN inscripcion_propuesta e ON e.id_propuesta = c.id_propuesta
                            LEFT JOIN usuarios f ON a.id_alumno_registro = f.id_usuario
                    WHERE d.id_documento = 4 AND d.id_estatus = 3 AND e.id_estatus=3 AND 
                        e.id_alumno = ?  AND e.id_carrera= ?
                    ORDER BY a.id_propuesta;";
                        
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
                        $mensaje_Transacciones = "Actualmente NO tiene ACEPTADA una inscripción a Propuesta.";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Jurado Registrado.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Mi Jurado
    
    //OBTENEMOS LOS SINODALES PARA LA PROPUESTA
    function Obtener_Sinodales($id_propuesta, $id_version){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
           

            $tsql = "SELECT aa.id_propuesta, aa.version, aa.num_profesor, 
                    aa.nombre_sinodal_propuesto, aa.id_usuario,                  
                    ((SELECT b.descripcion_grado_estudio
				FROM jefes_coordinacion a
				INNER JOIN grados_estudio b ON a.id_grado_estudio = b.id_grado_estudio
				WHERE a.id_usuario = aa.id_usuario
			UNION
			SELECT b.descripcion_grado_estudio
				FROM jefes_departamento a
				INNER JOIN grados_estudio b ON a.id_grado_estudio = b.id_grado_estudio
				WHERE a.id_usuario = aa.id_usuario
			UNION
			SELECT b.descripcion_grado_estudio
				FROM profesores a
				INNER JOIN grados_estudio b ON a.id_grado_estudio = b.id_grado_estudio
				WHERE a.id_usuario = aa.id_usuario) || ' ' || b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) as sinodal_definitivo
                    FROM sinodales aa
			LEFT JOIN usuarios b ON aa.id_usuario = b.id_usuario
                    WHERE aa.id_propuesta = ? AND aa.version = ?
                    ORDER BY aa.num_profesor";
            
            /* Valor de los parámetros. */
            $params = array($id_propuesta, $id_version);
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
                        $mensaje_Transacciones = "No existen Sinodales Registrado (Reportelo al Administrador del Sistema).<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Sinodales.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Sinodales

    //ACTUALIZAMOS LOS SINODALES
    function Actualizar_Sinodales($id_propuesta, $id_version,$id_sinodales, $id_alumno_propone, $titulo_propuesta, $id_division){
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $nombre_propuestos = '';
                
        try{    
            
            $cnn = new Connection();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
                           

            $conn->beginTransaction();

            //OBTENEMOS CORREO Y USUARIO DE LOS COORD/JDPTO QUE REVISARON LA PROPUESTA, ALUMNO, PROFESOR Y ADMINISTRADOR
            $obj_ = new d_coord_jdpto_Aprobar_Propuesta();
            $datos_prop = $obj_->Obtener_Usr_Mail_Propuesta_JDefinitivo($id_propuesta, 4, $id_version, $id_alumno_propone);
            if($datos_prop ==''){
                $mensaje_Transacciones .= "No se pudo Obtener los Correos de Coordinadores/Jefes de Dpto., Alumnos, Profesor y Administrador.";
                throw new Exception($mensaje_Transacciones);                 
            }
            $arr_datos_prop = preg_split("/[|]/", $datos_prop); 
            $arr_coord_dpto_id_usuarios = $arr_datos_prop[0];
            $arr_coord_dpto_correos = $arr_datos_prop[1];
            //FIN OBTENEMOS CORREO Y USUARIO DE LOS COORD/JDPTO QUE REVISARON LA PROPUESTA
            //*********************************************************************            
            
            //Actualizamos a los sinodales            
            $arr_sinodales = preg_split("/[|]/", $id_sinodales);
            $renglones = count($arr_sinodales);

            $tsql4=" UPDATE sinodales SET
                nombre_sinodal_propuesto = ?
                WHERE id_propuesta = ? AND version = ? AND num_profesor = ?;";
            
            for($i=0; $i < $renglones; $i++ ){
                $stmt4 = $conn->prepare($tsql4);
                if ($stmt4){
                    $arr_un_sinodal = preg_split("/[:]/", $arr_sinodales[$i]);
                    $nombre_propuestos .= ($i+1) . '.- ' . $arr_un_sinodal[1] . '<br>';
                    $result4=$stmt4->execute(array($arr_un_sinodal[1], $id_propuesta, $id_version, $arr_un_sinodal[0]));
                    if ($result4){
                            if($stmt4->rowCount() > 0){
                                $mensaje_Transacciones .= "Sinodal Actualizado. OK.<br/>";
                            }
                            else{
                                $error = $stmt4->errorInfo();
                                $mensaje_Transacciones .= "Error al Actualizar el Sinodal.<br>"  . $error[2] .'<br>';
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt4->errorInfo();
                        $mensaje_Transacciones .= "No se Actualizó el Sinodal.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }                    

                }
                else{
                        $error = $stmt4->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Sinodal.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }                        
            }
            
            //ACTUALIZAMOS EL ESTATUS DE LA SOLICITUD DE JURADO
            $tsql=" UPDATE jurado SET ".
                    "id_estatus =  ?, ".
                    "fecha_propuesto = ?, ".
                    "id_alumno_registro = ? ".
                    "WHERE id_propuesta = ? AND version = ?;";                    
            /* Valor de los parámetros. */
            $params = array(12,date('d-m-Y H:i:s'),$id_alumno_propone, $id_propuesta, $id_version);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result){
                        if($stmt->rowCount() > 0){                    
                            $mensaje_Transacciones .= "Estatus del Jurado Actualizado. OK.<br/>";
                        }
                        else{
                            $error = $stmt->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Actualizar el Estatus del Jurado.<br>"  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se actualizó el Estatus del Jurado.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Estatus del Jurado.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }            

            //ACTUALIZAMOS EL ESTATUS DEL VoBo DE COORDINADORES
            $tsql=" UPDATE jurado_vobo SET ".
                    "id_estatus =  ? ".
                    "WHERE id_propuesta = ? AND version = ?;";                    
            /* Valor de los parámetros. */
            $params = array(12,$id_propuesta, $id_version);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result){
                        if($stmt->rowCount() > 0){                    
                            $mensaje_Transacciones .= "Estatus de VoBo Coord/Dpto Actualizado. OK.<br/>";
                        }
                        else{
                            $error = $stmt->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Actualizar el Estatus del VoBo Coord/Dpto.<br>"  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se actualizó el Estatus del VoBo Coord/Dpto.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Estatus del VoBo Coord/Dpto.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }

            $conn->commit();
            
            //CONFIGURAMOS PARA LA BITACORA Y CORREOS
            //MOVIMIENTO DEL ALUMNO
            $respuesta_mail = '';
            $id_tema_evento1 = 40; // Solicitar Jurado
            $id_tipo_evento1 = 5; // Alta 
            $descripcion_evento1 = '';
            //AGREGAMOS A LA BITÁCORA EL MOVIMIENTO DEL ALUMNO
            $descripcion_evento1 = "Solicitud de Jurado para la Propuesta No. " . $id_propuesta . " Versión " . $id_version . 
                    " ** Con Título " . $titulo_propuesta ;
                        
            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_evento1;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento1);
            $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento1);
            $obj_miBitacora->set_Id_Usuario_Genera($id_alumno_propone);
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
            
            //AVISAMOS DEL JURADO PROPUESTO A LOS ALUMNOS DE LA PROPUESTA, COORD/JDPTO, ADMINISTRADOR Y PROFESOR
            $id_tema_evento2 = 40; // Solicitud de Jurado
            $id_tipo_evento2 = 50; // Envio de mail
            $descripcion_correo2 = '';
            //AGREGAMOS A LA BITÁCORA EL ENVIO DEL MAIL
            $descripcion_correo2 = "<b>Solicitud de Jurado:</b><br>" . $nombre_propuestos . "<br><br> Para la Propuesta No. " . $id_propuesta . " Versión " . $id_version . 
                        "<br> ** Con Título " . $titulo_propuesta ;

            $destinatarios_bitacora = $arr_coord_dpto_id_usuarios;
            $destinatarios_correo = $arr_coord_dpto_correos;

            $arr_destinatarios_bitacora = preg_split("/[,]/", $destinatarios_bitacora);

            $renglones = count($arr_destinatarios_bitacora); 

            for($i=0; $i < $renglones; $i++ ){
                sleep(1);
                $obj_miBitacora = new Bitacora();

                $descripcionEvento = $descripcion_correo2;
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento2);
                $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento2);
                $obj_miBitacora->set_Id_Usuario_Genera($id_alumno_propone);
                $obj_miBitacora->set_Id_Usuario_Destinatario($arr_destinatarios_bitacora[$i]);
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($id_division);

                $resultado_Bitacora ='';
                $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);                                       
            }  

            //Mandamos los correos
            $obj = new d_mail();
            $mi_mail = new Mail();
            $mensaje= $descripcion_correo2;

            $mi_mail->set_Correo_Destinatarios($destinatarios_correo);
            $mi_mail->set_Asunto('AVISO DE SOLICITUD DE JURADO');
            $mi_mail->set_Mensaje($mensaje);

            $mi_mail->set_Correo_Copia_Oculta('');

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
    //FIN ACTUALIZAMOS LOS SINODALES
     
}

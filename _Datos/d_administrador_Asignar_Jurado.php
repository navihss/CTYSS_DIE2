<?php
use App\Database\Connection;
/**
 * Definición de la Capa de Datos para la Clase Jurado Definitivo
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */

header('Content-Type: text/html; charset=UTF-8');
require_once __DIR__ . '/../app/Database/Connection.php';
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_coord_jdpto_Aprobar_Propuesta.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_mail.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Mail.php');


class d_administrador_Asignar_Jurado {

    //*********************************************************************                
    //OBTENEMOS LAS PROPUESTAS POR AUTORIZAR
    function Obtener_Jurados_Por_Autorizar(){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT a.id_propuesta, a.version, a.fecha_propuesto, 
                            a.id_estatus, b.descripcion_estatus,
                            d.version_propuesta, e.descripcion_para_nom_archivo, c.id_profesor, c.titulo_propuesta,
                            (f.apellido_paterno_usuario || ' ' || 
                            f.apellido_materno_usuario || ' ' || f.nombre_usuario) as nombre
                    FROM jurado a
                            INNER JOIN estatus b ON a.id_estatus = b.id_estatus
                            INNER JOIN propuestas_profesor c ON a.id_propuesta = c.id_propuesta
                            INNER JOIN propuesta_version d ON c.id_propuesta = d.id_propuesta
                            INNER JOIN documentos e ON d.id_documento = e.id_documento
                            INNER JOIN usuarios f ON c.id_profesor = f.id_usuario
                    WHERE d.id_documento = 4 AND d.id_estatus = 3 AND a.id_estatus = 2 
                    ORDER BY a.fecha_propuesto;";
                        
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            /*Verificamos el contenido de la ejecución*/                        
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute();                                 
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
                        $mensaje_Transacciones = "No hay Jurados por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
                else{
                    $mensaje_Transacciones = "No se pudo obtener los Jurados por Autorizar.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);                                                                                                    
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener Jurados por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //FIN OBTENEMOS JURADOS PENDIENTESPROPUESTAS POR AUTORIZAR
    //*********************************************************************     
    
    //*********************************************************************                
    //OBTENEMOS EL JURADO SELECCIONADO PARA VoBo DEFINITIVO
    function Obtener_Jurado_Seleccionado($id_propuesta, $id_version){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            //OBTENEMOS LOS SINODALES PROPUESTOS Y DEFINITIVOS
            $tsql = " SELECT id_propuesta, version, num_profesor, 
                    nombre_sinodal_propuesto
                    FROM sinodales
                    WHERE id_propuesta = ? AND version = ?
                    ORDER BY num_profesor;";
            
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            $params = array($id_propuesta, $id_version);
            /*Verificamos el contenido de la ejecución*/                        
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);                                 
                if ($result){                    
                    if($stmt->rowCount() > 0){                                                
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['propuestos'] = array();

                        while($row = $stmt->fetch(PDO::FETCH_OBJ)){
                            $jsondata['data']['propuestos'][] = $row;
                        }
                    }
                    else{
                        $mensaje_Transacciones = "No hay información de los Sinodales.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
                else{
                    $mensaje_Transacciones = "No se pudo obtener la información de los Sinodales.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);                                                                                                    
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Sinodales.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            } 
                        
            //OBTENEMOS TODOS LOS VoBo DE COORD/DPTO
            $tsql2 = "  SELECT a.id_propuesta, a.version, a.num_profesor, b.aceptado, b.nota,
                            b.id_usuario, a.nombre_sinodal_propuesto,  b.id_estatus, b.fecha_verificado,
                            (SELECT p.descripcion_departamento as descripcion
                            FROM jefes_departamento o
                                    INNER JOIN departamentos p ON o.id_departamento = p.id_departamento
                            WHERE o.id_usuario = b.id_usuario
                            UNION
                            SELECT p.descripcion_coordinacion as descripcion
                            FROM jefes_coordinacion o
                                    INNER JOIN coordinaciones p ON o.id_coordinacion = p.id_coordinacion
                            WHERE o.id_usuario = b.id_usuario) as area                            
                        FROM sinodales a
                                INNER JOIN jurado_vobo b ON a.id_propuesta = b.id_propuesta 
                                        AND a.version = b.version AND a.num_profesor = b.num_profesor
                        WHERE a.id_propuesta= ? AND a.version = ?
                        ORDER BY a.version, a.num_profesor;";
            
            /* Preparamos la sentencia a ejecutar */
            $stmt2 = $conn->prepare($tsql2);
            $params2 = array($id_propuesta, $id_version);
            /*Verificamos el contenido de la ejecución*/                        
            if($stmt2){        
                /*Ejecutamos el Query*/
                $result2 = $stmt2->execute($params2);                                 
                if ($result2){                    
                    if($stmt2->rowCount() > 0){                                                
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['vobo'] = array();

                        while($row = $stmt2->fetch(PDO::FETCH_OBJ)){
                            $jsondata['data']['vobo'][] = $row;
                        }
                        echo json_encode($jsondata);
                        exit();
                    }
                    else{
                        $mensaje_Transacciones = "No hay información de las Notas de Coord..<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
                else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones = "No se pudo obtener la información de las Notas de Coord.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);                                                                                                    
                }
            }
            else{
                $error = $stmt2->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener las Notas de Coord.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //FIN OBTENEMOS EL JURADO SELECCIONADO PARA VoBo DEFINITIVO
    //********************************************************************* 
    
   //*********************************************************************                
    //OBTENEMOS PROFESORES
    function Obtener_Profesores($textoBuscar){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
            
            $tsql ="SELECT a.id_usuario, es_externo, c.descripcion_grado_estudio, a.id_grado_estudio,
                                    (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) as nombre
                                    FROM profesores a
                                            INNER JOIN usuarios b ON a.id_profesor = b.id_usuario
                                            INNER JOIN grados_estudio c ON a.id_grado_estudio = c.id_grado_estudio
                                    WHERE (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) ILIKE '%" . $textoBuscar . "%'
                    UNION
                    SELECT a.id_usuario, '0' as es_externo, c.descripcion_grado_estudio, a.id_grado_estudio,
                                    (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) as nombre
                                    FROM jefes_coordinacion a
                                            INNER JOIN usuarios b ON a.id_coordinador = b.id_usuario
                                            INNER JOIN grados_estudio c ON a.id_grado_estudio = c.id_grado_estudio
                                    WHERE (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) ILIKE '%" . $textoBuscar . "%'
                    UNION
                    SELECT a.id_usuario, '0' as es_externo, c.descripcion_grado_estudio, a.id_grado_estudio,
                                    (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) as nombre
                                    FROM jefes_departamento a
                                            INNER JOIN usuarios b ON a.id_jefe_departamento = b.id_usuario
                                            INNER JOIN grados_estudio c ON a.id_grado_estudio = c.id_grado_estudio
                                    WHERE (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) ILIKE '%" . $textoBuscar . "%'
                    ORDER BY nombre";
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            /*Verificamos el contenido de la ejecución*/                        
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute();                                 
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
                        $mensaje_Transacciones = "No hay Profesores con este patrón de caracteres.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "No se pudo obtener Profesores con este patrón de caracteres.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);                                                                                                    
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener Profesores con este patrón de caracteres.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //FIN OBTENEMOS PROFESORES
    //*********************************************************************  

    //*********************************************************************                
    //ACTUALIZAMOS EL JURADO DEFINITIVO
    function Actualizar_Jurado_Def($id_propuesta, $id_version, $id_usuario, $lista_Definitivos, $titulo_propuesta, $id_division){
        
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $nombre_definitivos = '';
        
        try{                
            $cnn = new Connection();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
           
            /*Iniciar la transacción. */
            $conn->beginTransaction();

            //OBTENEMOS CORREO Y USUARIO DE LOS COORD/JDPTO QUE REVISARON LA PROPUESTA, ALUMNO, PROFESOR Y ADMINISTRADOR
            $obj_ = new d_coord_jdpto_Aprobar_Propuesta();
            $datos_prop = $obj_->Obtener_Usr_Mail_Propuesta_JDefinitivo($id_propuesta, 4, $id_version, $id_usuario);
            if($datos_prop ==''){
                $mensaje_Transacciones .= "No se pudo Obtener los Correos de Coordinadores/Jefes de Dpto., Alumnos, Profesor y Administrador.";
                throw new Exception($mensaje_Transacciones);                 
            }
            $arr_datos_prop = preg_split("/[|]/", $datos_prop); 
            $arr_coord_dpto_id_usuarios = $arr_datos_prop[0];
            $arr_coord_dpto_correos = $arr_datos_prop[1];
            //FIN OBTENEMOS CORREO Y USUARIO DE LOS COORD/JDPTO QUE REVISARON LA PROPUESTA
            //*********************************************************************            
            
            $arr_lista_Definitivos = preg_split("/[|]/", $lista_Definitivos);
            $renglones = count($arr_lista_Definitivos);
            
            //*********************************************************************    
            //ACTUALIZAMOS EL ID DE LOS SINODALES DEFINITIVOS
            $tsql4=" UPDATE sinodales SET 
                    id_usuario = ?
                    WHERE id_propuesta=? AND version=? AND num_profesor=?;";

            for($i=0; $i < $renglones; $i++ ){
                $stmt4 = $conn->prepare($tsql4);
                if ($stmt4){
                    $arr_num_id_prof = preg_split("/[,]/", $arr_lista_Definitivos[$i]);
                    $nombre_definitivos .= ($i+1) . '.- ' . $arr_num_id_prof[2] . '<br>';
                    $result4=$stmt4->execute(array($arr_num_id_prof[1],
                        $id_propuesta, $id_version, $arr_num_id_prof[0]));
                    if ($result4){
                            if($stmt4->rowCount() > 0){
                                $mensaje_Transacciones .= "Sinodal Definitivo Actualizado. OK.<br/>";
                            }
                            else{
                                $error = $stmt4->errorInfo();
                                $mensaje_Transacciones .= "Error al Actualizar el Sinodal Definitivo.<br>"  . $error[2] .'<br>';
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt4->errorInfo();
                        $mensaje_Transacciones .= "No se Actualizó el Sinodal Definitivo.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }                    

                }
                else{
                        $error = $stmt4->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Sinodal Definitivo.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }                        
            }             
            //FIN ACTUALIZAMOS EL ID DE LOS SINODALES DEFINITIVOS
            
            //ACTUALIZAMOS EL ESTATUS EN JURADO_VOBO 3. Aceptado
           $tsql5=" UPDATE jurado_vobo SET 
                id_estatus = 3
                WHERE id_propuesta = ? AND version = ?;";

            $stmt5 = $conn->prepare($tsql5);
            if ($stmt5){
                $result5=$stmt5->execute(array($id_propuesta, $id_version));
                if ($result5){
                        if($stmt5->rowCount() > 0){
                            $mensaje_Transacciones .= "VoBo Actualizado. OK.<br/>";
                        }
                        else{
                            $error = $stmt5->errorInfo();
                            $mensaje_Transacciones .= "Error al Actualizar el VoBo de los Sinodales.<br>"  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                }
                else{
                    $error = $stmt5->errorInfo();
                    $mensaje_Transacciones .= "No se Actualizó el VoBo de los Sinodales.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }                    

            }
            else{
                    $error = $stmt5->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el VoBo de los Sinodales.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }                        
                         
            //FIN ACTUALIZAMOS EL ESTATUS EN JURADO_VOBO
            
           //ACTUALIZAMOS EL ESTATUS DEL JURADO 3. Aceptado
           $tsql6=" UPDATE jurado SET 
                fecha_asigno_definitivos = ?,
                id_administrador_definitivos = ?, 
                id_estatus = 3
                WHERE id_propuesta = ? AND version = ?;";

            $stmt6 = $conn->prepare($tsql6);
            if ($stmt6){
                $result6=$stmt6->execute(array(date('d-m-Y H:i:s'), $id_usuario, $id_propuesta, $id_version));
                if ($result6){
                        if($stmt6->rowCount() > 0){
                            $mensaje_Transacciones .= "Jurado Actualizado. OK.<br/>";
                        }
                        else{
                            $error = $stmt6->errorInfo();
                            $mensaje_Transacciones .= "Error al Actualizar el Jurado.<br>"  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                }
                else{
                    $error = $stmt6->errorInfo();
                    $mensaje_Transacciones .= "No se Actualizó el Jurado.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }                    

            }
            else{
                    $error = $stmt6->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Jurado.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }                                                
            //FIN ACTUALIZAMOS EL ESTATUS DEL JURADO

            $conn->commit();
            
            //CONFIGURAMOS PARA LA BITACORA Y CORREOS
            //MOVIMIENTO DEL ADMINISTRADOR
            $respuesta_mail = '';
            $id_tema_evento1 = 118; // Definir Jurado
            $id_tipo_evento1 = 25; // Aprobación 
            $descripcion_evento1 = '';
            //AGREGAMOS A LA BITÁCORA EL MOVIMIENTO DEL ADMINISTRADOR
            $descripcion_evento1 = "Asignación del Jurado Definitivo para la Propuesta No. " . $id_propuesta . " Versión " . $id_version . 
                    " ** Con Título " . $titulo_propuesta ;
                        
            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_evento1;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento1);
            $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento1);
            $obj_miBitacora->set_Id_Usuario_Genera($id_usuario);
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
            
            //AVISAMOS DEL JURADO DEFINITIVO A LOS ALUMNOS, COORD/JDPTO, ADMINISTRADOR Y PROFESOR
            $id_tema_evento2 = 118; // Asignar Jurado Definitivo
            $id_tipo_evento2 = 50; // Envio de mail
            $descripcion_correo2 = '';
            //AGREGAMOS A LA BITÁCORA EL ENVIO DEL MAIL
            $descripcion_correo2 = "<b>Jurado Definitivo:</b><br>" . $nombre_definitivos . "<br><br> Para la Propuesta No. " . $id_propuesta . " Versión " . $id_version . 
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
                $obj_miBitacora->set_Id_Usuario_Genera($id_usuario);
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
            $mi_mail->set_Asunto('AVISO DE JURADO DEFINITIVO');
            $mi_mail->set_Mensaje($mensaje);

            $mi_mail->set_Correo_Copia_Oculta('');

            $respuesta_mail = $obj->Envair_Mail($mi_mail);                          
            
            $conn = null;
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora . $respuesta_mail;
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
    //FIN ACTUALIZAMOS EL JURADO DEFINITIVO
    
}
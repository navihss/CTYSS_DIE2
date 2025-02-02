<?php

/**
 * Definición de la Capa de Datos para la Clase Autorizar Jurados
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */

header('Content-Type: text/html; charset=UTF-8');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_coord_jdpto_Aprobar_Propuesta.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_mail.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Mail.php');

class d_coord_jdpto_Aprobar_Jurado {

    //*********************************************************************                
    //OBTENEMOS LAS PROPUESTAS POR AUTORIZAR
    function Obtener_Jurados_Por_Autorizar($id_usuario){
      
        try{                    
            $cnn = new Conexion();
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
                    WHERE d.id_documento = 4 AND d.id_estatus = 3 AND a.id_estatus = 12 AND
                    ? IN (SELECT g.id_usuario
				FROM jurado_vobo g
				WHERE a.id_propuesta = g.id_propuesta AND a.version = g.version AND id_usuario = ? AND g.id_estatus =12 )			
                    ORDER BY a.fecha_propuesto;";
                        
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            $params = array($id_usuario, $id_usuario);
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
                        $mensaje_Transacciones = "No hay Jurados por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
                else{
                    $error = $stmt->errorInfo();
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
    //OBTENEMOS EL TOTAL DE JURADOS PROPUESTAS POR AUTORIZAR
    function Obtener_Total_Jurados_Por_Autorizar($id_usuario){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT count(a.id_propuesta) as total1
                    FROM jurado a
                            INNER JOIN estatus b ON a.id_estatus = b.id_estatus
                            INNER JOIN propuestas_profesor c ON a.id_propuesta = c.id_propuesta
                            INNER JOIN propuesta_version d ON c.id_propuesta = d.id_propuesta
                            INNER JOIN documentos e ON d.id_documento = e.id_documento
                            INNER JOIN usuarios f ON c.id_profesor = f.id_usuario
                    WHERE d.id_documento = 4 AND d.id_estatus = 3 AND a.id_estatus = 12 AND
                    ? IN (SELECT g.id_usuario
				FROM jurado_vobo g
				WHERE a.id_propuesta = g.id_propuesta AND a.version = g.version AND id_usuario = ? AND g.id_estatus =12 )";
                        
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            $params = array($id_usuario, $id_usuario);
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
                        return ($jsondata);
//                        echo json_encode($jsondata);
//                        exit();
//                        print_r($jsondata);
                    }
                    else{
                        $mensaje_Transacciones = "No hay Jurados por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "No se pudo obtener el Total de Jurados por Autorizar.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);                                                                                                    
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Total de Jurados por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //FIN OBTENEMOS EL TOTAL DE JURADOS PENDIENTESPROPUESTAS POR AUTORIZAR
    //*********************************************************************     
    
    
    //*********************************************************************                
    //OBTENEMOS EL JURADO SELECCIONADO PARA VoBo
    function Obtener_Jurado_Seleccionado($id_usuario, $id_propuesta, $id_version){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT a.id_propuesta, a.version, a.num_profesor, a.nombre_sinodal_propuesto,
                            b.id_usuario, b.nota, aceptado, b.id_estatus, b.fecha_verificado
                    FROM sinodales a
                            INNER JOIN jurado_vobo b ON a.id_propuesta = b.id_propuesta 
                                    AND a.version = b.version AND a.num_profesor = b.num_profesor
                    WHERE b.id_usuario = ? AND b.id_estatus=12 AND a.id_propuesta= ? AND a.version = ?
                    ORDER BY a.version, a.num_profesor;";
            
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            $params = array($id_usuario, $id_propuesta, $id_version);
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
                        $mensaje_Transacciones = "No hay información para este Jurado.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "No se pudo obtener la información de este Jurado.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);                                                                                                    
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener la información de este Jurado.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //FIN OBTENEMOS EL JURADO SELECCIONADO PARA VoBo
    //********************************************************************* 

    //*********************************************************************                
    //OBTENER SI TODOS LOS COORD/DPTO YA REVISARON A LOS SINODALES
    function N_Documentos_Por_Revisar($id_propuesta, $id_version){
        $mensaje_Transacciones = 0;
        $conn = '';
        $resultado = '';        
        try{                
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
                           
            $tsql=" SELECT count(id_estatus) as pendientes
                    FROM jurado_vobo
                    WHERE id_propuesta = ? AND version= ? AND id_estatus = 12;";  //12. En VoBo Coord/Dpto
            /* Valor de los parámetros. */
            $params = array($id_propuesta,$id_version);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);                                 
                if ($result){                    
                    if($stmt->rowCount() > 0){                                                
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $resultado = $row['pendientes'];
                        return $resultado;
                        $conn = null;
                        exit();
                    }
                    else{
                        $mensaje_Transacciones = 0;
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = 0;
                throw new Exception($mensaje_Transacciones);                  
            }   
            exit();                 
        }
        catch (Exception $ex){
            $conn = null;
            return ('');
            exit();   
        }   
        
    }    
    //FIN OBTENER SI TODOS LOS COORD/DPTO YA REVISARON A LOS SINODALES
    //********************************************************************* 
        
    //*********************************************************************                
    //ACTUALIZAMOS EL ESTATUS DEL VoBo
    function Actualizar_VoBo($id_propuesta, $id_version, $id_usuario, $vobo_usuario, $titulo_propuesta, $id_division){
        
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
           
            /*Iniciar la transacción. */
            $conn->beginTransaction();

            //OBTENEMOS SI TODOS LOS COORD/DPTO YA REVISARON A TODOS LOS SINODALES
            $obj_ = new d_coord_jdpto_Aprobar_Jurado();
            $pendientes = $obj_->N_Documentos_Por_Revisar($id_propuesta,$id_version);
            if($pendientes ==''){
                $mensaje_Transacciones .= "No se pudo Obtener el Número de Sinodales por Valorar.";
                throw new Exception($mensaje_Transacciones);                 
            }
            //FIN OBTENEMOS SI TODOS LOS COORD/DPTO YA REVISARON A TODOS LOS SINODALES
            //*********************************************************************            

            //OBTENEMOS CORREO Y USUARIO DE LOS COORD/JDPTO QUE REVISARON LA PROPUESTA
            $obj_ = new d_coord_jdpto_Aprobar_Propuesta();
            $datos_prop = $obj_->Obtener_Usr_Mail_Propuesta($id_propuesta, 4, $id_version);
            if($datos_prop ==''){
                $mensaje_Transacciones .= "No se pudo Obtener los Correos de Coordinadores/Jefes de Dpto.";
                throw new Exception($mensaje_Transacciones);                 
            }
            $arr_datos_prop = preg_split("/[|]/", $datos_prop); 
            $arr_coord_dpto_id_usuarios = $arr_datos_prop[0];
            $arr_coord_dpto_correos = $arr_datos_prop[1];
            //FIN OBTENEMOS CORREO Y USUARIO DE LOS COORD/JDPTO QUE REVISARON LA PROPUESTA
            //*********************************************************************            
            
//             $jsondata['success'] = false;
//            $jsondata['data']['message'] = $pendientes;
//            echo json_encode($jsondata);
//            exit(); 
            
            $arr_vobo_por_usuario = preg_split("/[|]/", $vobo_usuario);
            $renglones = count($arr_vobo_por_usuario);
            
            //*********************************************************************                        
            //ACTUALIZAMOS LA ACEPTACION/RECHAZO DEL COORD/DPTO
            $tsql4=" UPDATE jurado_vobo SET 
                aceptado = ?,
                nota = ?, 
                fecha_verificado = ?,
                id_estatus = 2
                WHERE id_propuesta = ? AND version = ? AND num_profesor = ? AND id_usuario = ?;";

            for($i=0; $i < $renglones; $i++ ){
                $stmt4 = $conn->prepare($tsql4);
                if ($stmt4){
                    $arr_vobo_usuario = preg_split("/[,]/", $arr_vobo_por_usuario[$i]);
                    $result4=$stmt4->execute(array($arr_vobo_usuario[1], $arr_vobo_usuario[2], date('d-m-Y H:i:s'),
                        $id_propuesta, $id_version, $arr_vobo_usuario[0], $id_usuario));
                    if ($result4){
                            if($stmt4->rowCount() > 0){
                                $mensaje_Transacciones .= "VoBo Actualizado. OK.<br/>";
                            }
                            else{
                                $error = $stmt4->errorInfo();
                                $mensaje_Transacciones .= "Error al Actualizar el VoBo del Sinodal.<br>"  . $error[2] .'<br>';
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt4->errorInfo();
                        $mensaje_Transacciones .= "No se Actualizó el VoBo del Sinodal.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }                    

                }
                else{
                        $error = $stmt4->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el VoBo del Sinodal.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }                        
            }                

             $pendientes_VoBo = ($pendientes - $renglones);

             //ACTUALIZAMOS EL ESTATUS DEL JURADO
            if(($pendientes_VoBo) == '0'){ //YA TODOS REVISARON A LOS SINODALES
                $tsql1=" UPDATE jurado SET " .
                        "id_estatus =  2 " .    //2. Por Autorizar Admon.
                        "WHERE id_propuesta = ? AND " .
                        "version = ?;";                    
                /* Valor de los parámetros. */
                $params1 = array($id_propuesta, $id_version);
                /* Preparamos la sentencia a ejecutar */
                $stmt1 = $conn->prepare($tsql1);
                if($stmt1){
                    /*Ejecutamos el Query*/                
                    $result1 = $stmt1->execute($params1); 
                    if ($result1){
                        if($stmt1->rowCount() > 0){                    
                            $mensaje_Transacciones .= "Estatus del Jurado Actualizado. OK.<br/>";
                        }
                        else{
                            $error = $stmt1->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Actualizar el Estatus del Jurado.<br>"  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                    }
                    else{
                        $error = $stmt1->errorInfo();
                        $mensaje_Transacciones .= "No se pudo actualizar el Estatus del Jurado.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }
                }
                else{
                        $error = $stmt1->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Estatus del Jurado.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }                  
            }
            //FIN ACTUALIZAMOS EL ESTATUS DEL JURADO
            //*********************************************************************            
            
            $conn->commit();
            
            //CONFIGURAMOS PARA LA BITACORA Y CORREOS
            //MOVIMIENTO DEL COORD/JDPTO
            $respuesta_mail = '';
            $id_tema_evento1 = 85; // Definir Jurado
            $id_tipo_evento1 = 55; // Revisión
            $descripcion_evento1 = '';
            //AGREGAMOS A LA BITÁCORA QUE EL COORD/JDPTO YA REVISO AL JURADO PROPUESTO
            $descripcion_evento1 = "Revisé y dí el VoBo al Jurado de la Propuesta No. " . $id_propuesta . " Versión " . $id_version . 
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
            
            if(($pendientes_VoBo) == '0'){ //YA TODOS REVISARON A LOS SINODALES
                //AVISAMOS QUE TERMINO LA REVISIÓN Y VoBo DEL JURADO
                $id_tema_evento2 = 118; // Asignar Jurado Definitivo
                $id_tipo_evento2 = 50; // Envio de mail
                $descripcion_correo2 = '';
                //AGREGAMOS A LA BITÁCORA LA TERMINACIÓN DE LA REVISIÓN
                $descripcion_correo2 = "Coordinadores y Jefes de Dpto., han concluído la Revisión y VoBo de la Propuesta No. " . $id_propuesta . " Versión " . $id_version . 
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
                    $obj_miBitacora->set_Id_Usuario_Genera(1);
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
                $mi_mail->set_Asunto('AVISO REVISIÓN Y VoBo DE JURADO PROPUESTO');
                $mi_mail->set_Mensaje($mensaje);

                $mi_mail->set_Correo_Copia_Oculta('');

                $respuesta_mail = $obj->Envair_Mail($mi_mail);                          
            }
            
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
    //FIN ACTUALIZAMOS EL ESTATUS DEL VoBo
    
}

//$obj = new d_coord_jdpto_Aprobar_Jurado();
//$obj_resultado = $obj->Obtener_Total_Jurados_Por_Autorizar('rhernandez');
//$exito = ($obj_resultado['success']);
//$mensaje = $obj_resultado['data']['message'];
//$totalpendientes = $obj_resultado['data']['registros'][0]['total1'];
//echo ('success: ');
//echo($exito);
//echo (', mensaje: ');
//echo($mensaje);
//echo (', total pendientes:');
//echo($totalpendientes);
//
//$otroArray['success'] = $exito;
//$otroArray['data']['message'] = $mensaje;
//$otroArray['data']['registros'][0]['total1'] = $totalpendientes;
//$otroArray['data']['registros'][1]['total1'] = $totalpendientes + 5;
//
//print_r ($otroArray);
//
//$codificadoJson =json_encode($otroArray);
//$segundoArray = json_decode($codificadoJson);
//
//echo ($codificadoJson);
//
//print_r($segundoArray);


//$ob = new d_coord_jdpto_Aprobar_Jurado();
//echo $ob->Obtener_Jurado_Seleccionado('joel', '12', 1);
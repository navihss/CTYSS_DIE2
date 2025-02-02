<?php

/**
 * Definición de la Capa de Datos para la Clase Aprobar Ceremonia
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */

header('Content-Type: text/html; charset=UTF-8');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_coord_jdpto_Aprobar_Propuesta.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_mail.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Mail.php');

class d_administrador_Aprobar_Ceremonia {

    //*********************************************************************                
    //OBTENEMOS LAS CEREMONIAS POR AUTORIZAR
    function Obtener_Ceremonias_Por_Autorizar($id_division){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT c.id_ceremonia, c.fecha_alta, c.id_alumno, c.id_carrera, c.id_tipo_propuesta,
                            b.descripcion_tipo_propuesta, d.email_usuario, (c.id_estatus) as estatus_ceremonia,
                            (d.nombre_usuario || ' ' || d.apellido_paterno_usuario || ' ' || d.apellido_materno_usuario) as nombre
                     FROM inscripcion_ceremonia c
                             INNER JOIN tipos_propuesta b ON c.id_tipo_propuesta = b.id_tipo_propuesta
                             INNER JOIN usuarios d ON c.id_alumno = d.id_usuario and d.id_division = ?
                     WHERE c.id_estatus = 2 AND (
                             (SELECT count(a.id_documento)
                             FROM ceremonia_docs a
                             WHERE a.version = 1 AND a.id_ceremonia = c.id_ceremonia and a.id_division = ?) =
                             (SELECT count(a.id_documento)
                             FROM ceremonia_docs a
                             WHERE a.id_estatus='2' AND a.id_ceremonia = c.id_ceremonia and a.id_division = ?) + 
                             (SELECT count(a.id_documento)
                             FROM ceremonia_docs a
                             WHERE a.id_estatus='3' AND a.id_ceremonia = c.id_ceremonia and a.id_division = ?)) AND c.id_estatus = 2;";
                        
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            $params = array($id_division, $id_division, $id_division, $id_division);
            /*Verificamos el contenido de la ejecución*/                        
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if (!$result === FALSE){                    
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
                        $mensaje_Transacciones = "No hay Ceremonias por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
                else{
                    $mensaje_Transacciones = "Error en los parámetros para Obtener las Ceremonias por Autorizar.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);                                                                                                    
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para Obtener las Ceremonias por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //FIN OBTENEMOS CEREMONIAS POR AUTORIZAR
    //*********************************************************************
    
    //OBTENEMOS EL TOTAL DE CEREMONIAS POR AUTORIZAR
    function Obtener_Total_Ceremonias_Por_Autorizar($id_estatus, $id_division){
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT count(c.id_ceremonia) as total2
                     FROM inscripcion_ceremonia c
                             INNER JOIN tipos_propuesta b ON c.id_tipo_propuesta = b.id_tipo_propuesta
                             INNER JOIN usuarios d ON c.id_alumno = d.id_usuario
                     WHERE c.id_estatus = ? and d.id_division = ?";
                        
            /* Valor de los parámetros. */
            $params = array($id_estatus, $id_division); //ESTO
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            /*Verificamos el contenido de la ejecución*/                        
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params); //ESTO
                if (!$result === FALSE){                    
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
                        //echo json_encode($jsondata);
                        //exit();
                    }
                    else{
                        $mensaje_Transacciones = "No hay Ceremonias por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
                else{
                    $mensaje_Transacciones = "Error en los parámetros para Obtener las Ceremonias por Autorizar.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);                                                                                                    
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para Obtener las Ceremonias por Autorizar.<br/>"  . $error[2];
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
    //FIN DE OBTENER TOTAL CEREMONIAS POR AUTORIZAR

    //OBTENEMOS DOCUMENTOS POR AUTORIZAR
    function Obtener_Documentos_Por_Autorizar($id_ceremonia, $id_estatus){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT a.id_ceremonia, a.id_documento, a.version, a.id_estatus,
                                b.descripcion_documento, b.descripcion_para_nom_archivo,
                                c.descripcion_estatus, 
                                d.id_estatus as stat_ceremonia, d.id_alumno, d.id_carrera
                        FROM ceremonia_docs a
                                INNER JOIN documentos b ON a.id_documento = b.id_documento
                                INNER JOIN estatus c ON a.id_estatus = c.id_estatus
                                INNER JOIN inscripcion_ceremonia d ON d.id_ceremonia = a.id_ceremonia
                        WHERE d.id_ceremonia = ? AND d.id_estatus = ? AND a.id_estatus IN (2,3)
                        ORDER BY b.descripcion_documento;";
                        
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            /*Verificamos el contenido de la ejecución*/   
            $params = array($id_ceremonia, $id_estatus);
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);                                 
                if (!$result === FALSE){                    
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
                        $mensaje_Transacciones = "No hay Documentos para la Ceremonia.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
                else{
                    $mensaje_Transacciones = "Error en los parámetros para Obtener los Documentos para la Ceremonia.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);                                                                                                    
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para Obtener los Documentos para la Ceremonia.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //FIN OBTENEMOS DOCUMENTOS POR AUTORIZAR
    //*********************************************************************    

    //OBTENER CORREOS Y USUARIOS DE UNA PROPUESTA
    function Obtener_Usr_Mail_Propuesta($id_propuesta, $id_documento, $id_version){
        $mensaje_Transacciones = '';
        $conn = '';
        $resultado = '';        
        $usuarios = '';
        $correos ='';
        
        try{                
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
                           
            $tsql=" SELECT a.id_usuario, b.email_usuario
                    FROM propuesta_vobo a
                    INNER JOIN usuarios b ON a.id_usuario = b.id_usuario
                    WHERE a.id_propuesta = ? AND a.id_documento= ? AND a.version_propuesta = ?
                    UNION
                    SELECT c.id_usuario, c.email_usuario
                    FROM usuarios c
                    WHERE c.id_tipo_usuario = 1"; 
            /* Valor de los parámetros. */
            $params = array($id_propuesta,$id_documento, $id_version);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);                                 
                if ($result){                    
                    if($stmt->rowCount() > 0){   
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){                        
                            $usuarios .= $row['id_usuario'] . ',';
                            $correos .= $row['email_usuario'] . ',';
                        }
                        $usuarios = substr($usuarios, 0, strlen($usuarios)-1);
                        $correos = substr($correos, 0, strlen($correos)-1);
                        $resultado = $usuarios . '|' . $correos;
                        return $resultado;
                        $conn = null;
                        exit();
                    }
                    else{
                        $mensaje_Transacciones = '';
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
    //FIN OBTENER CORREO Y USUARIOS
    //********************************************************************* 
    
  function Actualizar_Estatus_Doc_($id_ceremonia, $id_documento, $version, $desc_documento,
          $nota, $id_estatus, $id_admin, $mail_admin, $todos_revisados, $desc_corta_nom_archivo, $id_division){
      
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();

        $id_alumno = '';
        $id_carrera = '';
        $nom_alumno = '';
        $desc_propuesta = '';
        $mail_alumno = '';
        $id_coordinador = '';
        $mail_coordinador = '';
        
        try{    
                              
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
            
            /*Iniciar la transacción. */
            $conn->beginTransaction();

            $tsql=" SELECT a.id_alumno, a.id_carrera,
                            b.descripcion_tipo_propuesta,
                            (c.nombre_usuario || ' ' || c.apellido_paterno_usuario || ' ' || c.apellido_materno_usuario) as nom_alumno, c.email_usuario as mail_alumno,
                            f.id_coordinador, g.email_usuario as mail_coordinador
                    FROM inscripcion_ceremonia a
                            INNER JOIN tipos_propuesta b ON a.id_tipo_propuesta = b.id_tipo_propuesta
                            INNER JOIN usuarios c ON a.id_alumno = c.id_usuario
                            INNER JOIN carreras d ON a.id_carrera = d.id_carrera
                            INNER JOIN coordinaciones e ON d.id_carrera = e.id_carrera
                            INNER JOIN jefes_coordinacion f ON e.id_coordinacion = f.id_coordinacion
                            INNER JOIN usuarios g ON f.id_coordinador = g.id_usuario
                    WHERE a.id_ceremonia = ? AND f.actual_jefe = '1';"; 
            
            /* Valor de los parámetros. */
            $params = array($id_ceremonia);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);                                 
                if (!$result === FALSE){                    
                    if($stmt->rowCount() > 0){   
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $id_alumno = $row['id_alumno'];
                        $id_carrera = $row['id_carrera'];
                        $nom_alumno = $row['nom_alumno'];
                        $desc_propuesta = $row['descripcion_tipo_propuesta'];
                        $mail_alumno = $row['mail_alumno'];
                        $id_coordinador = $row['id_coordinador'];
                        $mail_coordinador = $row['mail_coordinador'];
                    }
                    else{
                        $mensaje_Transacciones = 'No se encontro la información de está Ceremonia.';
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }  
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros para obtener la información de la Ceremonia.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Obtener la información de la Ceremonia.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }           
            
            $id_tema_evento = 120; //aprobar ceremonias
            $id_tipo_evento = 25; //aprobación
            $descripcion_evento = "Documento  " .$desc_documento. " Versión " . $version . 
                    " ** Ceremonia. " . $id_ceremonia . ' ' . $desc_propuesta . 
                    " ** Carrera " . $id_carrera . ' --- ' . $nota;
            $descripcion_Correo = "El Documento  " . $desc_documento. " Versión " . $version . 
                    "<br>** Ceremonia. " . $id_ceremonia . ' ' . $desc_propuesta .
                    "<br>** Carrera " . $id_carrera . "<br>*** ha sido <b>ACEPTADO</b>" . "<br> --- " . $nota;
            
            //Actualizamos el estatus del Documento especificado
            /* Query parametrizado. */
            $tsql=" UPDATE ceremonia_docs SET " .
                    "id_estatus =  ?, " .
                    "fecha_revision_admin = ?, " .
                    "nota_admin = ?, " .
                    "id_administrador = ? " .
                    "WHERE id_ceremonia = ? AND " .
                    "id_documento =? AND version = ?;";                    
            /* Valor de los parámetros. */
            $params = array(
                        $id_estatus, date('d-m-Y H:i:s'),
                        $nota, $id_admin,
                        $id_ceremonia, $id_documento, $version);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if (!$result === FALSE){
                    if($stmt->rowCount() > 0){                    
                        $mensaje_Transacciones .= "Estatus Actualizado. OK.<br/>";
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Actualizar el Estatus del Documento<br>."  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros para Actualizar  el Estatus del Documento.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Estatus del Documento.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }

            //Agregamos una nueva versión del Documento Rechazado para que el Alumno Reenvíe el Documento
            if ($id_estatus ==4){ //4. Rechazado
                $id_tipo_evento = 30;    //rechazo
                $descripcion_evento = "Documento " .$desc_documento. " Versión " . $version . 
                " ** Ceremonia. " . $id_ceremonia . ' ' . $desc_propuesta .
                " ** Carrera " . $id_carrera . ' --- ' . $nota;
                $descripcion_Correo = "Su Documento " .$desc_documento. " Versión " . $version . 
                "<br>** Ceremonia. " . $id_ceremonia . ' ' . $desc_propuesta .
                "<br>** Carrera " . $id_carrera . "<br>*** ha sido <b>RECHAZADO</b>" .
                " <br>Se ha generado un nuevo número de versión para que pueda reenvíarlo. <br> --- " . $nota;
                
                /* Query parametrizado. */
                $tsql2=" INSERT INTO ceremonia_docs(
                    id_ceremonia, id_documento, version, id_estatus, id_division)
                    VALUES(?,?,?,?,?);";
                /* Valor de los parámetros. */
                $params2 = array($id_ceremonia, $id_documento, $version + 1, 1, $id_division);
                /* Preparamos la sentencia a ejecutar */
                $stmt2 = $conn->prepare($tsql2);
                if($stmt2){
                    /*Ejecutamos el Query*/                
                    $result2 = $stmt2->execute($params2); 
                    if (!$result2 === FALSE){
                        if($stmt2->rowCount() > 0){                    
                            $mensaje_Transacciones .= "Nuevo Documento Agregado para su Reenvío. OK.<br/>";
                        }
                        else{
                            $error = $stmt2->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Agregar el Nuevo Documento para su Reenvío<br>."  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                    }
                    else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "Error en los parámetros para Agregar el Nuevo Documento para su Reenvío.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }
                }
                else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Agregar el Nuevo Documento para su Reenvío.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }   
                
                //BORRAMOS EL ARCHIVO
                $archivo_pdf = $_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/Docs/Ceremonias/'.$id_alumno .'_'. $id_carrera.'_'.
                        $id_ceremonia.'_'.$version.'_'.$desc_corta_nom_archivo.'.pdf';
                if (file_exists($archivo_pdf)) {
                    unlink($archivo_pdf);
                }                
            }
            if ($todos_revisados == 1){
               //ACTUALIAMOS EL ESTATUS DE LOS DOCUMENTOS DE CEREMONIA PARA QUE LO REVISE COORDINACIÓN
                $tsql=" UPDATE ceremonia_docs SET " .
                        "id_estatus =  ? " .
                        "WHERE id_ceremonia = ? AND " .
                        "id_estatus = ?;";                    
                /* Valor de los parámetros. */
                $params = array(9, //por aut. coordinacion/dpto
                            $id_ceremonia, 3); //aceptados por Admin
                /* Preparamos la sentencia a ejecutar */
                $stmt = $conn->prepare($tsql);
                if($stmt){
                    /*Ejecutamos el Query*/                
                    $result = $stmt->execute($params); 
                    if (!$result === FALSE){
                        if($stmt->rowCount() > 0){                    
                            $mensaje_Transacciones .= "Estatus de los Documentos para Coordinación Actualizado. OK.<br/>";
                        }
                        else{
                            $error = $stmt->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Actualizar el Estatus de los Docs para Coordinación<br>."  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "Error en los parámetros para Actualizar el Estatus de los Docs para Coordinación.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }
                }
                else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Estatus de los Docs para Coordinación.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }     
                
                //ACTUALIAMOS EL ESTATUS DE LA CEREMONIA PARA QUE LO REVISE COORDINACIÓN
                $tsql=" UPDATE inscripcion_ceremonia SET " .
                        "id_estatus =  ? " .
                        "WHERE id_ceremonia = ?;";                    
                /* Valor de los parámetros. */
                $params = array(9, //por aut. coordinacion/dpto
                            $id_ceremonia);
                /* Preparamos la sentencia a ejecutar */
                $stmt = $conn->prepare($tsql);
                if($stmt){
                    /*Ejecutamos el Query*/                
                    $result = $stmt->execute($params); 
                    if (!$result === FALSE){
                        if($stmt->rowCount() > 0){                    
                            $mensaje_Transacciones .= "Estatus de la Ceremonia Actualizado. OK.<br/>";
                        }
                        else{
                            $error = $stmt->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Actualizar el Estatus de la Ceremonia<br>."  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "Error en los parámetros para Actualizar el Estatus de la Ceremonia.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }
                }
                else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Estatus de la Ceremonia.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }                
            }
            
            $conn->commit();

            if($id_estatus == 4) {
                $mensaje_Transacciones = "Documento rechazado";
            } else {
                $mensaje_Transacciones = "Documento aceptado";
            }

            //A BITACORA EL MOVIMIENTO DE ADMIN                        
            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_evento;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento);
            $obj_miBitacora->set_Id_Usuario_Genera($id_admin);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_alumno);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);            
            sleep(1);
             //A BITACORA EL MOV. DE ENVIO DE CORREO
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_Correo;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento(50);//envio mail
            $obj_miBitacora->set_Id_Usuario_Genera($id_admin);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_alumno);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);            
            
            //LA PROPUESTA PASÓ A COORDINACIÓN PARA SU REVISIÓN
            $id_tema_evento2 = 120; //admin aprobar ceremonias 
            $id_tipo_evento2 = 50; //mail
            $descripcion_evento2 = "La Ceremonia. " . $id_ceremonia . ' ' . $desc_propuesta . 
                    " ** Carrera " . $id_carrera . ' ** A pasado a Coordinación para su Revisión.';
            $descripcion_Correo2 = "La Ceremonia. " . $id_ceremonia . ' ' . $desc_propuesta . 
                    "<br> ** Carrera " . $id_carrera . '<br> ** A pasado a Coordinación para su Revisión.';

            
            if ($todos_revisados == 1){
                sleep(1);
                $obj_Bitacora = new d_Usuario_Bitacora();
                $obj_miBitacora = new Bitacora();

                $descripcionEvento = $descripcion_evento2;
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento2);
                $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento2);
                $obj_miBitacora->set_Id_Usuario_Genera($id_admin);
                $obj_miBitacora->set_Id_Usuario_Destinatario($id_alumno);
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($id_division);

                $resultado_Bitacora ='';
                $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
                sleep(1);
                
                $obj_miBitacora = new Bitacora();

                $descripcionEvento = $descripcion_evento2;
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento2);
                $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento2);
                $obj_miBitacora->set_Id_Usuario_Genera($id_admin);
                $obj_miBitacora->set_Id_Usuario_Destinatario($id_coordinador);
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($id_division);

                $resultado_Bitacora ='';
                $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);  

                $mensaje_Transacciones .= "<br>La Ceremonia ha pasado a la coordinación para su revisión.";          
                
            }
            
            $obj = new d_mail();
            $mi_mail = new Mail();
            $mensaje= $descripcion_Correo;
                                   
            $mi_mail->set_Correo_Destinatarios($mail_alumno);
            $mi_mail->set_Correo_Copia_Oculta($mail_admin);
            $mi_mail->set_Asunto('AVISO APROBACIÓN DE DOCUMENTOS PARA CEREMONIA');
            $mi_mail->set_Mensaje($mensaje);
            $respuesta_mail = $obj->Envair_Mail($mi_mail);
            
            if ($todos_revisados == 1){
                sleep(1);
                $obj = new d_mail();
                $mi_mail = new Mail();
                $mensaje= $descripcion_Correo2;

                $mi_mail->set_Correo_Destinatarios($mail_alumno);
                $mi_mail->set_Correo_Copia_Oculta($mail_coordinador.','.$mail_admin);
                $mi_mail->set_Asunto('AVISO APROBACIÓN DE DOCUMENTOS PARA CEREMONIA');
                $mi_mail->set_Mensaje($mensaje);
                $respuesta_mail = $obj->Envair_Mail($mi_mail);
            }
            
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
    
    
}

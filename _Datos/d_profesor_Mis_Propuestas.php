<?php

/**
 * Definición de la Capa de Datos para la Clase Profesor Mis Propuestas
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */
header('Content-Type: text/html; charset=UTF-8');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Propuesta_Profesor.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');

class d_profesor_Mis_Propuestas {

    //OBTENEMOS LAS PROPUESTAS DEL PROFESOR
    function Obtener($id_profesor){

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
                           
            /* Query parametrizado. */
            $tsql=" SELECT a.id_propuesta, a.titulo_propuesta, a.organismos_colaboradores, a.fecha_registrada, 
                    a.id_tipo_propuesta, a.id_profesor, a.id_estatus, b.descripcion_tipo_propuesta,
                    (c.nombre_usuario || ' ' || c.apellido_paterno_usuario || ' ' || c.apellido_materno_usuario) as nombre,
                    d.descripcion_estatus, a.nota_baja, fecha_aceptacion, fecha_vigencia, a.id_division
               FROM propuestas_profesor a 
                     INNER JOIN tipos_propuesta b ON a.id_tipo_propuesta = b.id_tipo_propuesta
                     INNER JOIN usuarios c ON a.id_profesor = c.id_usuario
                     INNER JOIN estatus d ON a.id_estatus = d.id_estatus
               WHERE a.id_profesor = ?
               ORDER BY a.fecha_registrada;";

            /* Valor de los parámetros. */
            $params = array($id_profesor);
            
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
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
                        $mensaje_Transacciones .= "El profesor no tiene Propuestas registradas actualmente.";
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se obtuvieron las Propuestas del Profesor.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para obtener las Propuestas del Profesor.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }

            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones;
            return json_encode($jsondata);
            exit();                 
        }
        catch (Exception $ex){     
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            return json_encode($jsondata);
            exit();   
        }   
    }  
    //FIN OBTENER LAS PROPUESTAS DEL PROFESOR

    //OBTENEMOS LA PROPUESTA DEL PROFESOR
    function Seleccionar($id_propuesta){

        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        
        $obj_Propuesta_Profesor = new Propuesta_Profesor();
        
        try{    
            
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
                           
            /* Query parametrizado. */
//            $tsql=" SELECT id_propuesta, titulo_propuesta, organismos_colaboradores, fecha_registrada, 
//                        asesoria_l, asesoria_m, asesoria_mi, asesoria_j, asesoria_v, 
//                        asesoria_s, id_tipo_propuesta, id_profesor, id_estatus
//                   FROM propuestas_profesor
//                   WHERE id_propuesta = ?;";

            $tsql=" SELECT a.id_propuesta, a.titulo_propuesta, a.organismos_colaboradores, a.fecha_registrada, 
                    a.id_tipo_propuesta, a.id_profesor, a.id_estatus, b.descripcion_tipo_propuesta, a.aceptar_inscripciones,
                    a.fecha_aceptacion, a.fecha_vigencia
                   FROM propuestas_profesor a 
			INNER JOIN tipos_propuesta b ON a.id_tipo_propuesta = b.id_tipo_propuesta
                   WHERE id_propuesta = ?;";
            /* Valor de los parámetros. */
            $params = array($id_propuesta);
            
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result === FALSE){
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se obtuvo información de la Propuesta seleccionada.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                            
                }
                else{
                    if($stmt->rowCount() > 0){                    
//                        $jsondata['success'] = true;
//                        $jsondata['data']['message'] = 'Registros encontrados';
//                        $jsondata['data']['registros'] = array();

//                        while($row = $stmt->fetch(PDO::FETCH_OBJ)){
//                            $jsondata['data']['registros'][] = $row;
//                        }                        
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $obj_Propuesta_Profesor->set_Id_Propuesta($row['id_propuesta']);
                        $obj_Propuesta_Profesor->set_Titulo($row['titulo_propuesta']);
                        $obj_Propuesta_Profesor->set_Organismos_Colaboradores($row['organismos_colaboradores']);
                        $obj_Propuesta_Profesor->set_Fecha_Registrada($row['fecha_registrada']);
                        $obj_Propuesta_Profesor->set_Id_Tipo_Propuesta($row['id_tipo_propuesta']);
                        $obj_Propuesta_Profesor->set_Id_Profesor($row['id_profesor']);
                        $obj_Propuesta_Profesor->set_Id_Estatus($row['id_estatus']);
                        $obj_Propuesta_Profesor->set_Descripcion_Tipo_Propuesta($row['descripcion_tipo_propuesta']);
                        $obj_Propuesta_Profesor->set_Requerimiento_Alumnos('');
                        $obj_Propuesta_Profesor->set_Aceptar_Inscripciones($row['aceptar_inscripciones']);
                        $obj_Propuesta_Profesor->set_Horarios('');
                        
//                        $stmt=null;
//                        $conn=null;
//                        echo json_encode($jsondata);
//                        exit();
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se encontró información General de la Propuesta seleccionada.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }                    
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para obtener información General de la Propuesta seleccionada.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }

            //Obtenemos los requerimientos para la propuesta
            /* Query parametrizado. */
            $tsql=" SELECT a.id_propuesta, a.id_carrera, a.alumnos_requeridos, a.vacantes, b.descripcion_carrera
                    FROM propuesta_profesor_carrera_requeridos a
                    INNER JOIN carreras b ON a.id_carrera =  b.id_carrera
                    WHERE a.id_propuesta = ?
                    ORDER BY b.descripcion_carrera;";

            /* Valor de los parámetros. */
            $params = array($id_propuesta);
            
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result === FALSE){
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se obtuvo información de los Requerimientos de la Propuesta seleccionada.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                            
                }
                else{
                    if($stmt->rowCount() > 0){                    
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['registros'] = array();

                        $id_carrera='';
                        $desc_carrera = '';
                        $n_alumnos ='';
                        $cadena_requerimiento = '';
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            $id_carrera = $row['id_carrera'];
                            $desc_carrera = $row['descripcion_carrera'];
                            $n_alumnos = $row['alumnos_requeridos'];
                            $cadena_requerimiento .= $id_carrera .','.$desc_carrera.','.$n_alumnos.'|';
                        }
                        $cadena_requerimiento = substr($cadena_requerimiento, 0, strlen($cadena_requerimiento)-1);
                        $obj_Propuesta_Profesor->set_Requerimiento_Alumnos($cadena_requerimiento);
//                        var_dump($obj_Propuesta_Profesor);
                        $jsondata['data']['registros']= (array) $obj_Propuesta_Profesor;
//                        echo str_replace('\\u0000', "", json_encode($jsondata));
//                        exit();
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se encontró información de los Requerimientos de la Propuesta seleccionada.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }                    
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para obtener información de los Requerimientos de la Propuesta seleccionada.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }   
            
            //Obtenemos los Horarios de Asesoria para la propuesta
            /* Query parametrizado. */
            $tsql=" select a.id_dia, a.id_horario, a.id_propuesta, c.descripcion_dias, d.horario
                    from propuesta_horarios_asesoria a
                    inner join horarios_asesoria b on (a.id_dia = b.id_dia and a.id_horario = b.id_horario)
                    inner join dias_asesoria c on b.id_dia = c.id_dia
                    inner join horas_asesoria d on b.id_horario = d.id_horario
                    where a.id_propuesta = ?
                    order by a.id_dia, a.id_horario 
                    ;";

            /* Valor de los parámetros. */
            $params = array($id_propuesta);
            
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result === FALSE){
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se obtuvo información de los Horarios de Asesoría de la Propuesta seleccionada.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                            
                }
                else{
                    if($stmt->rowCount() > 0){                    
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['registros'] = array();

                        $id_dia='';
                        $id_horario='';
                        $cadena_dia_hora = '';
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            $id_dia = $row['id_dia'];
                            $id_horario = $row['id_horario'];
                            $desc_dias = $row['descripcion_dias'];
                            $desc_horario = $row['horario'];
                            $cadena_dia_hora .= $id_dia .','.$id_horario.','.$desc_dias.','.$desc_horario.'|';
                        }
                        $cadena_dia_hora = substr($cadena_dia_hora, 0, strlen($cadena_dia_hora)-1);
                        $obj_Propuesta_Profesor->set_Horarios($cadena_dia_hora);
//                        var_dump($obj_Propuesta_Profesor);
                        $jsondata['data']['registros']= (array) $obj_Propuesta_Profesor;
                        echo str_replace('\\u0000', "", json_encode($jsondata));
                        exit();
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se encontró información de los Horarios de Asesoría de la Propuesta seleccionada.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }                    
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para obtener información de los Horarios de la Propuesta seleccionada.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }  
            
        }
        catch (Exception $ex){     
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            return json_encode($jsondata);
            exit();   
        }   
    }  
    //FIN OBTENER LA PROPUESTA DEL PROFESOR
 
    //OBTENEMOS LOS DOCUMENTOS ENVIADOS
    function Obtener_Documentos_Enviados($id_propuesta){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_propuesta, a.id_documento, a.version_propuesta, a.id_estatus, b.descripcion_documento, 
                    b.descripcion_para_nom_archivo, c.descripcion_estatus, a.fecha_recepcion_doc, a.nota, d.id_profesor,
                    d.titulo_propuesta, d.id_tipo_propuesta
                    FROM propuesta_version a
                            INNER JOIN documentos b ON a.id_documento = b.id_documento
                            INNER JOIN estatus c ON a.id_estatus = c.id_estatus
                            INNER JOIN propuestas_profesor d ON a.id_propuesta = d.id_propuesta
                    WHERE a.id_propuesta = ?
                    ORDER BY a.id_documento, a.fecha_recepcion_doc;";
                        
            /* Valor de los parámetros. */
            $params = array($id_propuesta);
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
                        $mensaje_Transacciones = "No hay información de los Documentos Enviados de la Propuesta.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Documentos Enviados de la Propuesta.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Mis Documentos Enviados

    
    //OBTENEMOS LOS VoBo
    function Obtener_VoBo($id_propuesta, $id_documento, $id_version){

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
                           
            /* Query parametrizado. */
            $tsql=" SELECT a.id_propuesta, a.id_documento, a.version_propuesta,
                            a.id_estatus, a.fecha_revision, b.descripcion_estatus, a.nota
                    FROM propuesta_vobo a
                            INNER JOIN estatus b ON a.id_estatus = b.id_estatus
                    WHERE a.id_propuesta = ? AND a.id_documento = ? AND a.version_propuesta= ?
                    ORDER BY a.fecha_revision";

            /* Valor de los parámetros. */
            $params = array($id_propuesta, $id_documento, $id_version);
            
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
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
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No hay VoBo para la Propuesta seleccionada.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se obtuvieron los VoBo de la Propuesta seleccionada.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para obtener los VoBo de la Propuesta seleccionada.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }

            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones;
            return json_encode($jsondata);
            exit();                 
        }
        catch (Exception $ex){     
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            return json_encode($jsondata);
            exit();   
        }   
    }  
    //FIN OBTENER LOS VoBo

    //OBTENEMOS LAS PROPUESTAS AUTORIZADAS POR CARRERA
    function Obtener_Propuestas_Autorizadas($id_carrera){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
          
            $tsql = "SELECT a.id_propuesta, e.version_propuesta, a.id_tipo_propuesta, a.id_profesor, a.id_estatus,
                            a.titulo_propuesta, b.descripcion_estatus, c.descripcion_tipo_propuesta,
                            (d.nombre_usuario || ' ' || d.apellido_paterno_usuario || ' ' || d.apellido_materno_usuario) as nom_profesor,
                            g.descripcion_para_nom_archivo, a.aceptar_inscripciones, a.fecha_aceptacion, a.fecha_vigencia
                    FROM propuestas_profesor a
                            INNER JOIN estatus b ON a.id_estatus = b.id_estatus
                            INNER JOIN tipos_propuesta c ON a.id_tipo_propuesta = c.id_tipo_propuesta
                            INNER JOIN usuarios d ON a.id_profesor = d.id_usuario
                            INNER JOIN propuesta_version e ON a.id_propuesta = e.id_propuesta
                            INNER JOIN propuesta_profesor_carrera_requeridos f ON a.id_propuesta = f.id_propuesta
                            INNER JOIN documentos g ON e.id_documento = g.id_documento
                    WHERE e.id_documento = 4 AND e.id_estatus = 3 AND f.id_carrera = ?  AND f.vacantes>0
                    ORDER BY c.descripcion_tipo_propuesta, nom_profesor, a.fecha_registrada;";
                        
            /* Valor de los parámetros. */
            $params = array($id_carrera);
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
                        $mensaje_Transacciones = "No hay información de Propuestas para esta Carrera.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener las Propuestas para esta Carrera.<br/>"  . $error[2];
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
    //FIN OBTENEMOS LAS PROPUESTAS AUTORIZADAS POR CARRERA
        
    //AGREGAMOS LA PROPUESTA
    function Agregar($objMiPropuesta){
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $nuevoConsecutivo =0;
                
        try{    
            
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
                
            $obj_MP = new Propuesta_Profesor();
            $obj_MP = $objMiPropuesta;
           
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
            
            /* Iniciar la transacción. */

                $conn->beginTransaction();
                                
                /* Query para obtener el consecutivo PROVISIONAL para la Propuesta */
                $tsql1=" SELECT consecutivo + 1 as siguiente_digito
                        FROM folios_provisionales
                        WHERE proceso = ?;
                    ";
                
                /* Valor de los parámetros. */
                $params1 = array('propuesta_profesor');

                /* Preparamos la sentencia a ejecutar */
                $stmt1 = $conn->prepare($tsql1);
                $result1 = $stmt1->execute($params1);   
                $clave_MP ='';
                
                if($stmt1){
                    /*Ejecutamos el Query*/
                    $result1 = $stmt1->execute($params1);                                 
                    if ($result1){
                        if($stmt1->rowCount() > 0){                        
                            $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                            $nuevoConsecutivo = $row['siguiente_digito'];
                            $clave_MP = $nuevoConsecutivo;
                        }
                        else{
                            $error = $stmt1->errorInfo();
                            $mensaje_Transacciones = "No se puedo obtener el Consecutivo PROVISIONAL para la Propuesta.<br/>"  . $error[2];
                            throw new Exception($mensaje_Transacciones);                               
                        }
                    }
                else {
                        $error = $stmt1->errorInfo();
                        $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Consecutivo PROVISIONAL de la Propuesta.<br/>"  . $error[2];
                        throw new Exception($mensaje_Transacciones);        
                    }                                        
                }                
                $mensaje_Transacciones = "";  //Clave PROVISIONAL para Propuesta obtenida. OK.<br/>";
                //Agregamos la Propuesta a la BD
//              /* Query parametrizado para la Propuesta. */
                $tsql2=" INSERT INTO propuestas_profesor(
                            id_propuesta, titulo_propuesta, organismos_colaboradores, fecha_registrada, 
                            id_tipo_propuesta, id_profesor, id_estatus, consecutivo, id_previo, aceptar_inscripciones,
                            id_division)
                         VALUES (?, ?, ?, ?, 
                                 ?, ?, ?, ?, ?, ?, ?);";
                
                /* Valor de los parámetros. */
                $params2 = array($clave_MP,
                            $obj_MP->get_Titulo(),
                            $obj_MP->get_Organismos_Colaboradores(),
                            $obj_MP->get_Fecha_Registrada(),
                            $obj_MP->get_Id_Tipo_Propuesta(),
                            $obj_MP->get_Id_Profesor(),
                            $obj_MP->get_Id_Estatus(),0, $clave_MP, $obj_MP->get_Aceptar_Inscripciones(), $obj_MP->get_Id_Division());
                /* Preparamos la sentencia a ejecutar */
                $stmt2 = $conn->prepare($tsql2);
                if($stmt2){
                    /*Ejecutamos el Query*/                
                    $result2 = $stmt2->execute($params2); 
                    if ($result2){
                            if($stmt2->rowCount() > 0){                    
                                $mensaje_Transacciones .= "Adjuntar el índice de su propuesta"; //Propuesta Agregada. OK.<br><br><p style='padding:5px;color:white; background-color:#ff1493;'>AHORA DEBE DE ENVIAR EL INDICE DE SU PROPUESTA PARA SU ACEPTACIÓN.</p>";
                            }
                            else{
//                                $conn->rollBack();
                                $error = $stmt2->errorInfo();
                                $mensaje_Transacciones .= "No se pudo Agregar la Propuesta<br>."  . $error[2] .'<br>';
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "No se agregó la Propuesta.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }
                }
                else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL de la Propuesta.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }

                //Agregamos la versión 1 de la Propuesta la cual el Administrador le asigna coordinadores y
                //los Coordinadores son los que dan el VoBo
                $tsql3=" INSERT INTO propuesta_version(
                            id_propuesta, id_documento, version_propuesta, fecha_generada, id_estatus, id_division)
                        VALUES (?, ?, ?, ?, ?, ?);";
                /* Valor de los parámetros. */
                $params3 = array($clave_MP, 4, 1, date('d-m-Y H:i:s'), 1, $obj_MP->get_Id_Division()); //1. Sin Enviar
                /* Preparamos la sentencia a ejecutar */
                $stmt3 = $conn->prepare($tsql3);
                if ($stmt3){
                /*Ejecutamos el Query*/                
                    $result3 = $stmt3->execute($params3); 
                    if ($result3){
                            if($stmt3->rowCount() > 0){  
                                $mensaje_Transacciones .= ""; //Versión de la Propuesta Agregada. OK.<br/>";
                            }
                            else{
                                $error = $stmt3->errorInfo();
                                $mensaje_Transacciones .= "No se pudo Agregar la Versión de la Propuesta.<br>"  . $error[2] .'<br>';
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt3->errorInfo();
                        $mensaje_Transacciones .= "No se agregó la Versión de la Propuesta.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }                    
                }
                else{
                    $error = $stmt3->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentenciaSQL para agregar la Versión de la Propuesta.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                            
                }
                
                //Agregamos el Numero de Alumnos requeridos por Carrera para la Propuesta
                //Los datos vienen (carrena,n_alumnos|....), lo separamos en un array.
                $arr_alumnos_por_carrera = preg_split("/[|]/", $obj_MP->get_Requerimiento_Alumnos());
                $renglones = count($arr_alumnos_por_carrera);
                
                $tsql4=" INSERT INTO propuesta_profesor_carrera_requeridos(
                    id_propuesta, id_carrera, alumnos_requeridos, vacantes, id_division)
                    VALUES (?,?,?,?,?);
                    ";
                
                for($i=0; $i < $renglones; $i++ ){
                    $stmt4 = $conn->prepare($tsql4);
                    if ($stmt4){
                        $arr_carr_alum = preg_split("/[,]/", $arr_alumnos_por_carrera[$i]);
                        $result4=$stmt4->execute(array($clave_MP, $arr_carr_alum[0], $arr_carr_alum[1],$arr_carr_alum[1], $obj_MP->get_Id_Division()));
                        if ($result4){
                                if($stmt4->rowCount() > 0){
                                    $mensaje_Transacciones .= ""; // "Requerimiento Agregado. OK.<br/>";
                                }
                                else{
                                    $error = $stmt4->errorInfo();
                                    $mensaje_Transacciones .= "Error al Agregar el Requerimiento.<br>"  . $error[2] .'<br>';
                                    throw new Exception($mensaje_Transacciones);                                                              
                                }
                        }
                        else{
                            $error = $stmt4->errorInfo();
                            $mensaje_Transacciones .= "No se Agregó el Requerimiento.<br/>"  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);        
                        }                    

                    }
                    else{
                            $error = $stmt4->errorInfo();
                            $mensaje_Transacciones .= "Error en la sentencia SQL del Requerimiento.<br/>"  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                
                    }                        
                }                

                //Agregamos los horarios
                //Los datos vienen (id_dia,id_horario|....), lo separamos en un array.
                $arr_horarios = preg_split("/[|]/", $obj_MP->get_Horarios());
                $renglones = count($arr_horarios);
                
                $tsql4=" INSERT INTO propuesta_horarios_asesoria(
                    id_dia, id_horario, id_propuesta, id_division)
                    VALUES (?,?,?,?);
                    ";
                
                for($i=0; $i < $renglones; $i++ ){
                    $stmt4 = $conn->prepare($tsql4);
                    if ($stmt4){
                        $arr_dia_hr = preg_split("/[,]/", $arr_horarios[$i]);
                        $result4=$stmt4->execute(array($arr_dia_hr[0], $arr_dia_hr[1], $clave_MP, $obj_MP->get_Id_Division()));
                        if ($result4){
                                if($stmt4->rowCount() > 0){
                                    $mensaje_Transacciones .= ""; // "Horarios Agregado. OK.<br/>";
                                }
                                else{
                                    $error = $stmt4->errorInfo();
                                    $mensaje_Transacciones .= "Error al Agregar los Horarios.<br>"  . $error[2] .'<br>';
                                    throw new Exception($mensaje_Transacciones);                                                              
                                }
                        }
                        else{
                            $error = $stmt4->errorInfo();
                            $mensaje_Transacciones .= "No se Agregó el Horario.<br/>"  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);        
                        }                    

                    }
                    else{
                            $error = $stmt4->errorInfo();
                            $mensaje_Transacciones .= "Error en la sentencia SQL para Agregar el Horario.<br/>"  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                
                    }                        
                } 
                
                //Incrementamos el Contador PROVISIONAL para las Propuestas en la BD
//              /* Query parametrizado para el incremento del Contador de la Propuesta. */
                $tsql5=" UPDATE folios_provisionales SET
                consecutivo = " . $nuevoConsecutivo . " " .
                "WHERE proceso = ?;";

                /* Valor de los parámetros. */
                $params5= array("propuesta_profesor");
                /* Preparamos la sentencia a ejecutar */
                $stmt5 = $conn->prepare($tsql5);
                if($stmt5){
                    /*Ejecutamos el Query*/                
                    $result5 = $stmt5->execute($params5); 
                    if ($result5){
                            if($stmt5->rowCount() > 0){                    
                                $mensaje_Transacciones .= ""; //Contador PROVISIONAL. OK.<br/>";
                            }
                            else{
                                $error = $stmt5->errorInfo();
                                $mensaje_Transacciones .= "No se pudo Incrementar el Contador PROVISIONAL<br>."  . $error[2] .'<br>';
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt5->errorInfo();
                        $mensaje_Transacciones .= "No se Incrementó el Contador PROVISIONAL de Propuestas.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }
                }
                else{
                        $error = $stmt5->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para incrementar el Contador de Propuestas.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }

                $conn->commit();
                
                //AGREGAMOS EL MOVIMIENTO A LA BITACORA
                $obj_Bitacora = new d_Usuario_Bitacora();
                $obj_miBitacora = new Bitacora();

                $descripcionEvento = 'PROPUESTA AGREGADA ID TEMPORAL ' . $clave_MP .' *** '.' Título: ' . $obj_MP->get_Titulo();
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora(60); //MIS PROPUESTAS
                $obj_miBitacora->set_Id_Tipo_Evento(5); // ALTA
                $obj_miBitacora->set_Id_Usuario_Genera($obj_MP->get_Id_Profesor());
                $obj_miBitacora->set_Id_Usuario_Destinatario('');
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($obj_MP->get_Id_Division());

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

    function Actualizar($objMiPropuesta){
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $nuevoConsecutivo =0;
                
        try{    
            
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
                
            $obj_MP = new Propuesta_Profesor();
            $obj_MP = $objMiPropuesta;
           
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
            
            /* Iniciar la transacción. */

                $conn->beginTransaction();
                                
                //Si la propuesta aún no esta autorizada podrá modificar el tipo de la propuesta, titulo, requerimientos y horarios
                //en caso contrario solo los datos generales
                if ($obj_MP->get_Id_Estatus() == 2){ //2. Sin Autorizar
                    //Actualizamos la Propuesta en la BD
    //              /* Query parametrizado para la Propuesta. */
                    $tsql2=" UPDATE propuestas_profesor SET
                                titulo_propuesta = ?, 
                                organismos_colaboradores = ?,
                                aceptar_inscripciones = ?,
                                id_tipo_propuesta = ?
                             WHERE id_propuesta  = ?;";

                    /* Valor de los parámetros. */
                    $params2 = array($obj_MP->get_Titulo(),
                                $obj_MP->get_Organismos_Colaboradores(),
                                $obj_MP->get_Aceptar_Inscripciones(),
                                $obj_MP->get_Id_Tipo_Propuesta(),
                                $obj_MP->get_Id_Propuesta());
                }
                else {//3. Autorizada
    //              /* Query parametrizado para la Propuesta. */
                    $tsql2=" UPDATE propuestas_profesor SET
                                titulo_propuesta = ?,
                                organismos_colaboradores = ?,
                                aceptar_inscripciones = ?
                             WHERE id_propuesta  = ?;";

                    /* Valor de los parámetros. */
                    $params2 = array($obj_MP->get_Titulo(),
                                $obj_MP->get_Organismos_Colaboradores(),
                                $obj_MP->get_Aceptar_Inscripciones(),
                                $obj_MP->get_Id_Propuesta());                    
                }
                
                /* Preparamos la sentencia a ejecutar */
                $stmt2 = $conn->prepare($tsql2);
                if($stmt2){
                    /*Ejecutamos el Query*/                
                    $result2 = $stmt2->execute($params2); 
                    if ($result2){
                            if($stmt2->rowCount() > 0){                    
                                $mensaje_Transacciones .= "Propuesta Actualizada. OK.<br/>";
                            }
                            else{
                                $error = $stmt2->errorInfo();
                                $mensaje_Transacciones .= "No se pudo Actualizar la Propuesta<br>."  . $error[2] .'<br>';
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "No se actualizó la Propuesta.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }
                }
                else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL de actualizar Propuesta.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }

                //Si la propuesta aún no está autorizada, actualizamos los requerimientos solicitados
                if ($obj_MP->get_Id_Estatus() == 2){                    
                    //Borramos los requerimientos que tiene registrados actualmente
                    $tsql3=" DELETE FROM propuesta_profesor_carrera_requeridos
                             WHERE id_propuesta  = ?;";
                    /* Preparamos la sentencia a ejecutar */
                    $stmt3 = $conn->prepare($tsql3);

                    /* Valor de los parámetros. */
                    $params3 = array($obj_MP->get_Id_Propuesta());                    
                
                    $result3 = $stmt3->execute($params3); 
                    if ($result3){
                        if($result3 > 0){                    
                            $mensaje_Transacciones .= "Requerimientos Borrados. OK.<br/>";
                        }
                        else{
                            $error = $stmt3->errorInfo();
                            $mensaje_Transacciones .= "No hubo Requerimientos para esta Propuesta.<br>"  . $error[2] .'<br>';
//                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                    }
                    
                    //Agregamos los nuevos requerimientos
                    //Agregamos el Numero de Alumnos requeridos por Carrera para la Propuesta
                    //Los datos vienen (carrena,n_alumnos|....), lo separamos en un array.
                    $arr_alumnos_por_carrera = preg_split("/[|]/", $obj_MP->get_Requerimiento_Alumnos());
                    $renglones = count($arr_alumnos_por_carrera);               

                    $tsql4=" INSERT INTO propuesta_profesor_carrera_requeridos(
                        id_propuesta, id_carrera, alumnos_requeridos, vacantes, id_division)
                        VALUES (?,?,?,?,?);
                        ";

                    for($i=0; $i < $renglones; $i++ ){
                        $stmt4 = $conn->prepare($tsql4);
                        if ($stmt4){
                            $arr_carr_alum = preg_split("/[,]/", $arr_alumnos_por_carrera[$i]);
                            $result4=$stmt4->execute(array($obj_MP->get_Id_Propuesta(), $arr_carr_alum[0], $arr_carr_alum[1],$arr_carr_alum[1], $obj_MP->get_Id_Division()));
                            if ($result4){
                                    if($stmt4->rowCount() > 0){
                                        $mensaje_Transacciones .= "Requerimiento Agregado. OK.<br/>";
                                    }
                                    else{
                                        $error = $stmt4->errorInfo();
                                        $mensaje_Transacciones .= "Error al Agregar el Requerimiento.<br>"  . $error[2] .'<br>';
                                        throw new Exception($mensaje_Transacciones);                                                              
                                    }
                            }
                            else{
                                $error = $stmt4->errorInfo();
                                $mensaje_Transacciones .= "No se Agregó el Requerimiento.<br/>"  . $error[2] .'<br>';
                                throw new Exception($mensaje_Transacciones);        
                            }                    

                        }
                        else{
                                $error = $stmt4->errorInfo();
                                $mensaje_Transacciones .= "Error en la sentencia SQL del Requerimiento.<br/>"  . $error[2] .'<br>';
                                throw new Exception($mensaje_Transacciones);                                
                        }                        
                    }                         
                }

                //Actualizamos los Horarios de Asesoría
                //Borramos los horarios actuales
                $tsql3=" DELETE FROM propuesta_horarios_asesoria
                         WHERE id_propuesta  = ?;";
                /* Preparamos la sentencia a ejecutar */
                $stmt3 = $conn->prepare($tsql3);

                /* Valor de los parámetros. */
                $params3 = array($obj_MP->get_Id_Propuesta());                    

                $result3 = $stmt3->execute($params3); 
                if ($result3){
                    if($result3 > 0){                    
                        $mensaje_Transacciones .= "Horarios Borrados. OK.<br/>";
                    }
                    else{
                        $error = $stmt3->errorInfo();
                        $mensaje_Transacciones .= "No hubo Horarios para esta Propuesta.<br>"  . $error[2] .'<br>';
                    }
                }

                //Agregamos los nuevos horarios
                //Los datos vienen (id_dia,id_horario|....), lo separamos en un array.
                $arr_horarios = preg_split("/[|]/", $obj_MP->get_Horarios());
                $renglones = count($arr_horarios);               

                $tsql4=" INSERT INTO propuesta_horarios_asesoria(
                    id_propuesta, id_dia, id_horario, id_division)
                    VALUES (?, ?, ?, ?);
                    ";

                for($i=0; $i < $renglones; $i++ ){
                    $stmt4 = $conn->prepare($tsql4);
                    if ($stmt4){
                        $arr_dia_horario = preg_split("/[,]/", $arr_horarios[$i]);
                        $result4=$stmt4->execute(array($obj_MP->get_Id_Propuesta(), $arr_dia_horario[0], $arr_dia_horario[1], $obj_MP->get_Id_Division()));
                        if ($result4){
                                if($stmt4->rowCount() > 0){
                                    $mensaje_Transacciones .= "Horario Agregado. OK.<br/>";
                                }
                                else{
                                    $error = $stmt4->errorInfo();
                                    $mensaje_Transacciones .= "Error al Agregar el Horario.<br>"  . $error[2] .'<br>';
                                    throw new Exception($mensaje_Transacciones);                                                              
                                }
                        }
                        else{
                            $error = $stmt4->errorInfo();
                            $mensaje_Transacciones .= "No se Agregó el Horario.<br/>"  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);        
                        }                    

                    }
                    else{
                            $error = $stmt4->errorInfo();
                            $mensaje_Transacciones .= "Error en la sentencia SQL para Agregar el Horario.<br/>"  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                
                    }                        
                }                         
                
                
                $conn->commit();

                $mensaje_Transacciones = "Propuesta Actualizada.";
                
                //AGREGAMOS EL MOVIMIENTO A LA BITACORA
                $obj_Bitacora = new d_Usuario_Bitacora();
                $obj_miBitacora = new Bitacora();

                $descripcionEvento = 'PROPUESTA AGREGADA ID ' . $obj_MP->get_Id_Propuesta() .' *** '.' Título: ' . $obj_MP->get_Titulo();
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora(60); //MIS PROPUESTAS
                $obj_miBitacora->set_Id_Tipo_Evento(15); // ACTUALIZACION
                $obj_miBitacora->set_Id_Usuario_Genera($obj_MP->get_Id_Profesor());
                $obj_miBitacora->set_Id_Usuario_Destinatario('');
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($obj_MP->get_Id_Division());

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
    
    function Actualizar_Estatus_Doc_Enviado($id_propuesta, $id_documento, $id_version, $id_estatus){
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
                           
            //Actualizamos el estatus del Documento especificado
            /* Query parametrizado. */
            $tsql=" UPDATE propuesta_version SET ".
                    "id_estatus =  ?, ".
                    "fecha_recepcion_doc = ? ".
                    "WHERE id_propuesta = ? AND " .
                    "id_documento =? AND version_propuesta = ?;";                    
            /* Valor de los parámetros. */
            $params = array(
                        $id_estatus, date('d-m-Y H:i:s'),
                        $id_propuesta,
                        $id_documento,
                        $id_version);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result){
                    if($stmt->rowCount() > 0){                    
                        $mensaje_Transacciones .= ""; //"Estatus Actualizado. OK.<br/>";
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Actualizar el Estatus del Documento<br>."  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se actualizó el Estatus del Documento.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Estatus del Documento.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }

            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones;
            return json_encode($jsondata);
            exit();                 
        }
        catch (Exception $ex){     
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            return json_encode($jsondata);
            exit();   
        }   
    }
    
    function Obtener_Carreras($id_propuesta){
        
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
           
            $tsql = "SELECT a.id_carrera, a.descripcion_carrera
                    FROM carreras a
                    WHERE a.id_carrera NOT IN (SELECT id_carrera
                        FROM propuesta_profesor_carrera_requeridos
                        WHERE id_propuesta = ?)
                    ORDER BY a.descripcion_carrera;";
            
            /* Valor de los parámetros. */
            $params = array($id_propuesta);
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
                        $jsondata['data']= array('message'=>'No hay información del Catálogo Carreras.');
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
    } //Fin Obtener
    
    
    //BORRAR PROPUESTA
    function Borrar_Propuesta($id_propuesta, $id_profesor, $id_division, $titulo_propuesta, $descripcion_tipo_propuesta, $nota){
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

            $conn->beginTransaction();
                        
            $tsql2=" UPDATE propuesta_version 
                    SET id_estatus = ?
                    WHERE id_propuesta = ?;";
            
            /* Valor de los parámetros. */
            $params2 = array(14, $id_propuesta); //14. Baja realizada por el Usuario
            /* Preparamos la sentencia a ejecutar */
            $stmt2 = $conn->prepare($tsql2);
            if($stmt2){
                /*Ejecutamos el Query*/                
                $result2 = $stmt2->execute($params2); 
                if ($result2){
                    if($stmt2->rowCount() > 0){                    
                        $mensaje_Transacciones .= "Versiones de Propuesta de Baja. OK.<br/>";
                    }
                    else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Dar de Baja las Versiones de Propuesta.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "No se dió de Baja las Versiones de Propuesta.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para dar de Baja las Versiones de Propuesta.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }

            //BORRAMOS LA INSCRIPCION
            $tsql=" UPDATE propuestas_profesor
                    SET id_estatus = ?, nota_baja = ?, fecha_baja = ?
                    WHERE id_propuesta = ?;";                    
            
            /* Valor de los parámetros. */
            $params = array(14, $nota, date('d-m-Y H:i:s'), $id_propuesta); //14. Baja realizada por el Usuario
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result){
                    if($stmt->rowCount() > 0){                    
                        $mensaje_Transacciones .= "La Propuesta se dió de Baja. OK.<br/>";
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo dar de Baja la Propuesta.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se dió de Baja la Propuesta.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para dar de Baja la Propuesta.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }
            //BORRAMOS EL ARCHIVO PDF
            $nom_archivo = $id_profesor.'_'.
                           $id_propuesta.'_*.pdf';
            $nom_archivo = $_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/Docs/Propuestas_Profesor/'.$nom_archivo;
            array_map("unlink", glob($nom_archivo));
            
            $conn->commit();
            
            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = ' ** Se dió de Baja la Propuesta ' . $id_propuesta . ' **Con Título ' . 
                    $titulo_propuesta . ' ** Tipo : ' . $descripcion_tipo_propuesta . ' --- '. $nota;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(60); //Mis Propuestas
            $obj_miBitacora->set_Id_Tipo_Evento(10); // baja
            $obj_miBitacora->set_Id_Usuario_Genera($id_profesor);
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Id_Division($id_division);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
                        
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora;
            return json_encode($jsondata);
            exit();                 
        }
        catch (Exception $ex){  
            $conn->rollBack();
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            return json_encode($jsondata);
            exit();   
        }   
    } 
    //FIN BORRAR INSCRIPCION DE UNA PROPUESTA
        
    
    //OBTENEMOS EL NOMBRE DEL PROFESOR DE LA PROPUESTA
    function Obtener_Nombre_Profesor($id_propuesta){

        $conn = '';
        $jsondata = array();
        $mensaje_Transacciones='';
        $nombre_Profesor ='';
        try{                           
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
                           
            $tsql=" select a.id_propuesta,
                    d.descripcion_grado_estudio || ' ' || b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario as nombre
                    from propuestas_profesor a
                    inner join usuarios b on a.id_profesor = b.id_usuario
                    inner join profesores c on b.id_usuario = c.id_profesor
		    inner join grados_estudio d on c.id_grado_estudio = d.id_grado_estudio
                    where a.id_propuesta= ?";
                    
            /* Valor de los parámetros. */
            $params = array($id_propuesta);
            
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result === FALSE){
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se pudo obtener el Nombre del Propietario de la Propuesta.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                            
                }
                else{
                    if($stmt->rowCount() > 0){                    
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $nombre_Profesor = $row['nombre'];
                        return $nombre_Profesor;
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No existe un Nombre de Profesor para esta Propuesta.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }                    
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para obtener el Nombre del propietario de la Propuesta.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }
        }
        catch (Exception $ex){     
            return $ex->getMessage();
        }   
    }  
    //FIN OBTENER NOMBRE DEL PROFESOR DE LA PROPUESTA
}
//
//$obj = new d_profesor_Mis_Propuestas();
//echo $obj->Seleccionar('10');

//$obj = new d_profesor_Mis_Propuestas();
//echo($obj->Obtener_Nombre_Profesor('2017-2-001'));
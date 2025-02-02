<?php
/**
 * Definición de la Capa de Datos para Aceptar Alumnos en una propuesta
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */
header('Content-Type: text/html; charset=UTF-8');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/zonaHoraria.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_mail.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Mail.php');

class d_profesor_Aceptar_Alumnos {
    
    //OBTENEMOS LAS INSCRIPCIONES PENDIENTES DE AUTORIZAR
    function Obtener_Inscripciones_Por_Autorizar($id_estatus, $id_profesor){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
          
            $tsql = "SELECT a.id_inscripcion, a.id_documento, a.numero_version, a.fecha_revision, 
                            a.id_estatus, a.fecha_recepcion, b.id_propuesta, c.titulo_propuesta,
                            d.descripcion_tipo_propuesta, b.id_alumno, e.email_usuario, b.id_carrera,
                            (e.nombre_usuario || ' ' || e.apellido_paterno_usuario || ' ' || e.apellido_materno_usuario) as nom_alumno,
                            c.id_profesor, b.fecha_inscripcion,
                            f.descripcion_para_nom_archivo
                     FROM inscripcion_propuesta_version a
                             INNER JOIN inscripcion_propuesta b ON a.id_inscripcion = b.id_inscripcion
                             INNER JOIN propuestas_profesor c ON b.id_propuesta = c.id_propuesta
                             INNER JOIN tipos_propuesta d ON c.id_tipo_propuesta = d.id_tipo_propuesta
                             INNER JOIN usuarios e ON b.id_alumno = e.id_usuario
                             INNER JOIN documentos f ON a.id_documento = f.id_documento
                     WHERE a.id_estatus = ? AND c.id_profesor = ?
                     ORDER BY a.fecha_recepcion;";
                        
            /* Valor de los parámetros. */
            $params = array($id_estatus, $id_profesor);
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
                        $mensaje_Transacciones = "No hay Inscripciones por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener las Inscripciones por Autorizar.<br/>"  . $error[2];
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
    //FIN OBTENEMOS LAS INSCRIPCIONES PENDIENTES DE AUTORIZAR

    //OBTENEMOS EL TOTAL DE LAS INSCRIPCIONES PENDIENTES DE AUTORIZAR
    function Obtener_Total_Inscripciones_Por_Autorizar($id_estatus, $id_profesor){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
          
            $tsql = "SELECT count(a.id_inscripcion) as totalInscripcionesPendientes
                     FROM inscripcion_propuesta_version a
                             INNER JOIN inscripcion_propuesta b ON a.id_inscripcion = b.id_inscripcion
                             INNER JOIN propuestas_profesor c ON b.id_propuesta = c.id_propuesta
                             INNER JOIN tipos_propuesta d ON c.id_tipo_propuesta = d.id_tipo_propuesta
                             INNER JOIN usuarios e ON b.id_alumno = e.id_usuario
                             INNER JOIN documentos f ON a.id_documento = f.id_documento
                     WHERE a.id_estatus = ? AND c.id_profesor = ?";
                        
            /* Valor de los parámetros. */
            $params = array($id_estatus, $id_profesor);
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
                        $mensaje_Transacciones = "No hay Inscripciones Pendientes por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Total de Inscripciones por Autorizar.<br/>"  . $error[2];
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
    //FIN OBTENEMOS TOTAL DE LAS INSCRIPCIONES PENDIENTES DE AUTORIZAR
        
    //LIBERAR LA INSCRIPCIÓN DADA DE BAJA
    function Borrar_Propuesta($id_propuesta, $id_carrera){
        $mensaje_Transacciones = '';
        $conn = '';

        try{    
            
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            

            $conn->beginTransaction();
            
            //Aumentamos el número de requerimientos de la carrera
            /* Query parametrizado. */
            $tsql=" UPDATE propuesta_profesor_carrera_requeridos SET ".
                    "vacantes =  vacantes + 1 ".
                    "WHERE id_propuesta = ? AND " .
                    "id_carrera =?;";                    
            /* Valor de los parámetros. */
            $params = array($id_propuesta, $id_carrera);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result){
                    if($stmt->rowCount() > 0){                    
                        $mensaje_Transacciones = '';
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Actualizar las Vacantes en la Propuesta.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se actualizó las Vacantes en la Propuesta.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar las Vacantes en la Propuesta.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }
                        
            $conn->commit();           
            return $mensaje_Transacciones;
            exit();                 
        }
        catch (Exception $ex){  
            $conn->rollBack();
            return $ex->getMessage();
            exit();   
        }   
    } 
    //FIN LIBERAR LA INSCRIPCIÓN DE LA BAJA
        
    //VACANTES EN LA PROPUESTA SEGUN LA CARRERA
    function vacantes_Propuesta($id_propuesta, $id_carrera){
        $mensaje_Transacciones = '';
        $conn = '';

        try{    
            
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            

            //Obtenemos las Vacantes de la Propuesta-Carrera
            /* Query parametrizado. */
            $tsql=" SELECT vacantes 
                    FROM propuesta_profesor_carrera_requeridos
                    WHERE id_propuesta = ? AND id_carrera = ?;";                    
            /* Valor de los parámetros. */
            $params = array($id_propuesta, $id_carrera);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result){
                    if($stmt->rowCount() > 0){     
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $mensaje_Transacciones = $row['vacantes'];
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Obtener las Vacantes de la Propuesta.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros para Obtener las Vacantes de la Propuesta.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Obtener las Vacantes de la Propuesta.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }
            return $mensaje_Transacciones;
        }
        catch (Exception $ex){  
            return $ex->getMessage();
        }   
    } 
    //FIN VACANTES EN LA PROPUESTA SEGUN LA CARRERA
        
    //ACTUALIZAMOS EL ESTATUS DE LA INSCRIPCION DEL ALUMNO
    function Actualizar_Estatus_Doc_Enviado($id_inscripcion, $id_documento, $id_version, 
            $id_estatus, $nota, $id_alumno,$id_propuesta, $titulo_propuesta, $id_profesor, 
            $correo_alumno, $id_carrera, $desc_corta_archivo, $id_division){
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
                        
            //Obtengo las vacantes
            if($id_documento != 5 && $id_estatus !=4){//5. Baja de Inscripción 4.Rechazo
                $obj_d_prof_Acep_Alum = new d_profesor_Aceptar_Alumnos();
                $vacantes = $obj_d_prof_Acep_Alum->vacantes_Propuesta($id_propuesta, $id_carrera);

                if(is_numeric($vacantes)){
                    if($vacantes == '0'){
                        $mensaje_Transacciones .= "No existen Vacantes para la Carrera " . $id_carrera . " en la Propuesta " . $id_propuesta .".<br>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
                else {
                    $mensaje_Transacciones .= $vacantes;
                    throw new Exception($mensaje_Transacciones);                                                                              
                }
            }
            
            if ($id_estatus ==3){ //3. Aceptado 
                //Disminuímos/Aumentamos el número de vancantes según la carrera
                if($id_documento == 5){ //5.Baja de inscripción
                    $tsql5=" UPDATE propuesta_profesor_carrera_requeridos SET
                    vacantes = vacantes + 1 
                    WHERE id_propuesta = ? AND id_carrera = ?;";
                }
                else{
                    $tsql5=" UPDATE propuesta_profesor_carrera_requeridos SET
                    vacantes = vacantes - 1 
                    WHERE id_propuesta = ? AND id_carrera = ?;";                    
                }
                /* Valor de los parámetros. */
                $params5= array($id_propuesta, $id_carrera);
                /* Preparamos la sentencia a ejecutar */
                $stmt5 = $conn->prepare($tsql5);
                if($stmt5){
                    /*Ejecutamos el Query*/                
                    $result5 = $stmt5->execute($params5); 
                    if ($result5){
                            if($stmt5->rowCount() > 0){                    
                                $mensaje_Transacciones .= "Vacantes disminuídas. OK.<br/>";
                            }
                            else{
                                $error = $stmt5->errorInfo();
                                $mensaje_Transacciones .= "No se pudo Disminuír las Vancantes.<br>"  . $error[2] .'<br>';
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt5->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Disminuír las Vancantes.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }
                }
                else{
                        $error = $stmt5->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Disminuír las Vancantes.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }            
            }
            //Actualizamos el estatus del Documento especificado
            /* Query parametrizado. */
            $tsql=" UPDATE inscripcion_propuesta_version SET ".
                    "id_estatus =  ?, ".
                    "fecha_revision = ?, ".
                    "nota = ? ".
                    "WHERE id_inscripcion = ? AND " .
                    "id_documento =? AND numero_version = ?;";                    
            /* Valor de los parámetros. */
            $params = array(
                        $id_estatus, date('d-m-Y H:i:s'),
                        $nota,
                        $id_inscripcion,
                        $id_documento,
                        $id_version);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result){
                    if($stmt->rowCount() > 0){                    
                        $mensaje_Transacciones .= "Estatus del Documento Actualizado. OK.<br/>";
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error";
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un error";
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un error";
                    throw new Exception($mensaje_Transacciones);                                
            }

            $id_tema_evento = 65; // baja y aprobación de alumnos
            $id_tipo_evento = 0;
            $descripcion_evento = "";
            $descripcion_Correo = "";
            
            //Actualizamos el estatus de la inscripción del alumno
            if ($id_estatus ==3){ //3. Aceptado
                $id_tipo_evento = 25; //aprobación
                if($id_documento ==5){ //5.Baja de Inscripción
//                    $obj_d_prof_Acep_Alum = new d_profesor_Aceptar_Alumnos();
//                    $resultado_baja = $obj_d_prof_Acep_Alum->Borrar_Propuesta($id_propuesta, $id_carrera);
//                    if($resultado_baja !=''){
//                        $mensaje_Transacciones .= "No se pudo Actualizar las Vacantes de la Propuesta.<br>";
//                        throw new Exception($mensaje_Transacciones);                                                                                      
//                    }
                    $descripcion_evento = "La Solicitud de Baja del Alumno con Versión de Doc. " . $id_version .  
                            " *** No. de Cta. " . $id_alumno . 
                            " *** No. de Inscripción ". $id_inscripcion . 
                            " *** Carrera " . $id_carrera .
                            " Ha sido ACEPTADA *** Ha sido dado de BAJA de la propuesta " . 
                            $id_propuesta . " *** Con Título " .$titulo_propuesta . " --- " . $nota;
                    $descripcion_Correo = "La <b>Solicitud de Baja</b> del Alumno con Versión de Doc. " . $id_version .  
                            "<br> *** No. de Cta. " . $id_alumno . 
                            "<br> *** No. de Inscripción ". $id_inscripcion . 
                            "<br> *** Carrera " . $id_carrera .
                            "<br> Ha sido <b>ACEPTADA</b> <br>*** Ha sido dado de BAJA de la propuesta " .
                            $id_propuesta . "<br> *** Con Título " .$titulo_propuesta . "<br> --- " . $nota;
                    
                    $tsql3=" UPDATE inscripcion_propuesta SET "
                            . "id_estatus = ?, "
                            . "fecha_baja = ?, "
                            . "nota_baja = ? "
                            . "WHERE id_inscripcion = ?;";
                    $params3 = array(6, date('d-m-Y H:i:s'), $nota, $id_inscripcion);                    
                }
                else{
                    $descripcion_evento = "Su Historial Académico con Versión de Doc. " . $id_version .  
                        " *** No. de Cta. " . $id_alumno . 
                        " *** No. de Inscripción ". $id_inscripcion . 
                        " *** Carrera " . $id_carrera .    
                        " Ha sido ACEPTADO *** Ha sido ACEPTADO en la propuesta " . 
                        $id_propuesta . " *** Con Título " .$titulo_propuesta . " --- " . $nota;
                    $descripcion_Correo = "Su Historial Académico con Versión de Doc. " . $id_version .  
                        "<br> *** No. de Cta. " . $id_alumno . 
                        "<br> *** No. de Inscripción ". $id_inscripcion . 
                        "<br> *** Carrera " . $id_carrera .
                        " Ha sido ACEPTADO <br>*** Ha sido <b>ACEPTADO en la propuesta " . 
                        $id_propuesta . "<br> *** Con Título " .$titulo_propuesta . " </b><br>--- " . $nota;
                    
                    $tsql3=" UPDATE inscripcion_propuesta SET "
                            . "id_estatus = ? "
                            . "WHERE id_inscripcion = ?;";
                    $params3 = array($id_estatus, $id_inscripcion);
                }    
               
                /* Preparamos la sentencia a ejecutar */
                $stmt3 = $conn->prepare($tsql3);
                if($stmt3){
                    /*Ejecutamos el Query*/                
                    $result3 = $stmt3->execute($params3); 
                    if ($result3){
                        if($stmt3->rowCount() > 0){                    
                            $mensaje_Transacciones .= "Estatus de la Inscripción Actualizada. OK.<br/>";
                        }
                        else{
                            $error = $stmt3->errorInfo();
                            $mensaje_Transacciones = "Ocurrió un error";
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                    }
                    else{
                        $error = $stmt3->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error";
                        throw new Exception($mensaje_Transacciones);        
                    }
                }
                else{
                        $error = $stmt3->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error";
                        throw new Exception($mensaje_Transacciones);                                
                }  
                //BORRAMOS LOS PDF 
                if($id_documento ==5){ //5. baja de inscripción
                    //BORRAMOS EL ARCHIVO PDF
                    $nom_archivo = $id_alumno.'_'.
                                   $id_carrera.'_'.
                                   $id_propuesta.'_*.pdf';
                    $nom_archivo = $_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/Docs/Inscripcion_A_Propuesta/'.$nom_archivo;
                    array_map("unlink", glob($nom_archivo));
                    
                    $nom_archivo = $id_alumno.'_'.
                                   $id_carrera.'_'.
                                   $id_propuesta.'_'.
                                   $id_version.'_Baja_Inscripcion.pdf';
                    $nom_archivo = $_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/Docs/Baja_de_Propuesta/'.$nom_archivo;
                    if (file_exists($nom_archivo)) {
                        unlink($nom_archivo);   
                    }                    
                }
                
            }
            
            //Agregamos una nueva versión de la Solicitud de Inscripción para que el Alumno Reenvíe el Documento
            if ($id_estatus ==4){ //4. Rechazado
                $id_tipo_evento = 30; //Rechazado
                if($id_documento ==5){ //5.Baja de Inscripción
                    $descripcion_evento = "La Solicitud de Baja del Alumno con Versión de Doc. " . $id_version .  
                            " *** No. de Cta. " . $id_alumno . 
                            " *** No. de Inscripción ". $id_inscripcion . 
                            " *** Carrera " . $id_carrera .
                            " Ha sido RECHAZADA *** Propuesta " . 
                            $id_propuesta . " *** Con Título " .$titulo_propuesta . " --- " . $nota;
                    $descripcion_Correo = "La <b>Solicitud de Baja</b> del Alumno con Versión de Doc. " . $id_version .  
                            "<br> *** No. de Cta. " . $id_alumno . 
                            "<br> *** No. de Inscripción ". $id_inscripcion . 
                            "<br> *** Carrera " . $id_carrera .
                            "<br> Ha sido <b>RECHAZADA</b> <br>*** Propuesta " .
                            $id_propuesta . "<br> *** Con Título " .$titulo_propuesta . "<br> --- " . $nota;
                    
//                    $tsql3=" UPDATE inscripcion_propuesta SET "
//                            . "id_estatus = ?, "
//                            . "fecha_baja = ? "
//                            . "WHERE id_inscripcion = ?;";
//                    $params3 = array(6, date('d-m-Y H:i:s'), $id_inscripcion);                    
                }
                else{
                    $descripcion_evento = "Su Historial Académico con Versión de Doc. " . $id_version .  
                        " *** No. de Cta. " . $id_alumno . 
                        " *** No. de Inscripción ". $id_inscripcion . 
                        " *** Carrera " . $id_carrera .
                        " Ha sido RECHAZADO *** Propuesta " . 
                        $id_propuesta . " *** Con Título " .$titulo_propuesta . " --- " . $nota;
                    $descripcion_Correo = "Su <b>Historial Académico</b> con Versión de Doc. " . $id_version .  
                        "<br> *** No. de Cta. " . $id_alumno . 
                        "<br> *** No. de Inscripción ". $id_inscripcion . 
                        "<br> *** Carrera " . $id_carrera .
                        " Ha sido <b>RECHAZADO</b> <br>*** Propuesta " . 
                        $id_propuesta . "<br> *** Con Título " .$titulo_propuesta . "<br>--- " . $nota;
                    
//                    $tsql3=" UPDATE inscripcion_propuesta SET "
//                            . "id_estatus = ? "
//                            . "WHERE id_inscripcion = ?;";
//                    $params3 = array($id_estatus, $id_inscripcion);
                }                 
                
                /* Query parametrizado. */
                $tsql2=" INSERT INTO inscripcion_propuesta_version(id_inscripcion, id_documento,
                        numero_version, fecha_generada, nota, id_estatus) VALUES(
                        ?,?,?,?,?,?);";
                    
                /* Valor de los parámetros. */
                $params2 = array($id_inscripcion, $id_documento, $id_version + 1,
                     date('d-m-Y H:i:s'), '', 1);
                /* Preparamos la sentencia a ejecutar */
                $stmt2 = $conn->prepare($tsql2);
                if($stmt2){
                    /*Ejecutamos el Query*/                
                    $result2 = $stmt2->execute($params2); 
                    if ($result2){
                        if($stmt2->rowCount() > 0){                    
                            $mensaje_Transacciones .= "Nueva Versión de la Inscripción Agregada para su Reenvío. OK.<br/>";
                        }
                        else{
                            $error = $stmt2->errorInfo();
                            $mensaje_Transacciones = "Ocurrió un error";
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                    }
                    else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error";
                        throw new Exception($mensaje_Transacciones);        
                    }
                }
                else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error";
                        throw new Exception($mensaje_Transacciones);                                
                }
                if($id_documento !=5){ //5. baja de inscripción
                    //BORRAMOS EL ARCHIVO PDF
                    $nom_archivo = $id_alumno.'_'.
                                   $id_carrera.'_'.
                                   $id_propuesta.'_'.
                                   $id_version.'_'.
                                   $desc_corta_archivo.'.pdf';
                    $nom_archivo = $_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/Docs/Inscripcion_A_Propuesta/'.$nom_archivo;
                    if (file_exists($nom_archivo)) {
                        unlink($nom_archivo);   
                    }
                }
                else{
                    //BORRAMOS EL ARCHIVO PDF
                    $nom_archivo = $id_alumno.'_'.
                                   $id_carrera.'_'.
                                   $id_propuesta.'_'.
                                   $id_version.'_'.
                                   $desc_corta_archivo.'.pdf';
                    $nom_archivo = $_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/Docs/Baja_de_Propuesta/'.$nom_archivo;
                    if (file_exists($nom_archivo)) {
                        unlink($nom_archivo);   
                    }                    
                }
            }

//            if ($id_estatus ==3 && $id_documento ==5){ //3. Aceptado 5. baja
//                $obj_d_prof_Acep_Alum = new d_profesor_Aceptar_Alumnos();
//                $resultado_baja = $obj_d_prof_Acep_Alum->Borrar_Propuesta($id_propuesta, $id_carrera);
//                if($resultado_baja !=''){
//                    $mensaje_Transacciones .= "No se pudo Actualizar las Vacantes de la Propuesta.<br>";
//                    throw new Exception($mensaje_Transacciones);                                                                                      
//                }
//            }
            
            $conn->commit();

            if ($id_estatus == 3){
                $mensaje_Transacciones = "Documento Aceptado";
            }
            else {
                $mensaje_Transacciones = "Documento Rechazado";
            }
            
            //REGISTRAMOS MOVIMIENTO DEL PROFESOR
            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_evento;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento);
            $obj_miBitacora->set_Id_Usuario_Genera($id_profesor);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_alumno);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);            
            
            //AGREGAMOS EL MAIL ENVIADO
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_Correo;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento(50);
            $obj_miBitacora->set_Id_Usuario_Genera($id_profesor);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_alumno);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora); 
            
            $obj = new d_mail();
            $mi_mail = new Mail();
            $mensaje= $descripcion_Correo;
                                   
            $mi_mail->set_Correo_Destinatarios($correo_alumno);
            $mi_mail->set_Asunto('AVISO APROBACIÓN DE INSCRIPCIÓN A PROPUESTA');
            $mi_mail->set_Mensaje($mensaje);
            $respuesta_mail = $obj->Envair_Mail($mi_mail);
            
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora;// . $respuesta_mail;
            echo json_encode($jsondata);
            exit();                 
        }
        catch (Exception $ex){   
            $conn->commit();
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();   
        }   
    }  
    //FIN ACTUALIZAMOS EL ESTATUS DE LA INSCRIPCION DEL ALUMNO
    
}

//$obj = new d_profesor_Aceptar_Alumnos();
//$x = $obj->vacantes_Propuesta('3', '1101');
//echo $x;

//$obj = new d_profesor_Aceptar_Alumnos();
//$x = $obj->Obtener_Total_Inscripciones_Por_Autorizar(10, 'ehernandez');
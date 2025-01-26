<?php
use App\Database\Connection;
/**
 * Definición de la Capa de Datos para la Clase Asignar Coordinadores
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */

header('Content-Type: text/html; charset=UTF-8');
require_once __DIR__ . '/../app/Database/Connection.php';
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Propuesta_Profesor.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_mail.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Mail.php');

class d_administrador_Asignar_Coordinadores {


    function Traer_Indice($id_propuesta) {
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT indice 
                    FROM propuesta_tesis
                    WHERE id_propuesta = ?";
                        

            $stmt = $conn->prepare($tsql);
            $params = array($id_propuesta);

            if($stmt){        
               
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
                        $mensaje_Transacciones = "No hay registros";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la BD";
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
    function Traer_Indice_Completo($id_propuesta) {
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = 'SELECT objetivo,definicion_problema, metodo, temas_utilizar, resultados_esperados 
                    FROM propuesta_tesis
                    WHERE id_propuesta = ?';
                        

            $stmt = $conn->prepare($tsql);
            $params = array($id_propuesta);

            if($stmt){        
               
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
                        $mensaje_Transacciones = "No hay registros";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la BD";
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

    //OBTENEMOS LOS DOCUMENTOS POR ASIGNAR COORDINADORES
    function Obtener_Documentos_Por_Autorizar($id_estatus, $id_division){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_propuesta, a.id_documento, a.version_propuesta, a.id_estatus, b.descripcion_documento, 
                            b.descripcion_para_nom_archivo, c.descripcion_estatus, a.fecha_recepcion_doc, a.nota, 
                            d.id_profesor,d.titulo_propuesta, f.descripcion_tipo_propuesta, e.email_usuario,
                            (e.nombre_usuario || ' ' || e.apellido_paterno_usuario || ' ' || e.apellido_materno_usuario) as nombre
                    FROM propuesta_version a
                            INNER JOIN documentos b ON a.id_documento = b.id_documento
                            INNER JOIN estatus c ON a.id_estatus = c.id_estatus
                            INNER JOIN propuestas_profesor d ON a.id_propuesta = d.id_propuesta
                            INNER JOIN usuarios e ON d.id_profesor = e.id_usuario
                            INNER JOIN tipos_propuesta f ON d.id_tipo_propuesta = f.id_tipo_propuesta
                    WHERE a.id_estatus = ? and d.id_division = ?
                    ORDER BY a.id_documento, a.fecha_recepcion_doc DESC;";
                        
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            $params = array($id_estatus, $id_division);
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
                        $mensaje_Transacciones = "No hay Documentos para Asignarles Coordinadores.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Documentos para Asignarles Coordinadores.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //FIN OBTENEMOS LOS DOCUMENTOS POR ASIGNAR COORDINADORES

    //ACTUALIZAMOS EL ESTATUS DEL DOC DEL PROFESOR
    function Actualizar_Aceptacion_Doc($id_propuesta_doc, $id_documento_doc, $id_version_doc, 
            $id_estatus, $id_administrador, $nota, $coordinaciones, $departamentos, 
            $id_profesor, $titulo_propuesta, $correo_profesor, $desc_documento, $id_division){
 
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $id_tipo_evento=0;
        $id_tema_evento=0;
        $descripcion_evento='';
        $descripcion_Correo ='';
        $destinatarios_de_correo = '';
        $destinatarios_bitacora = '';
        
        if($id_estatus == 4){ //Rechazada por el administrador
                $id_tipo_evento = 30; //rechazado
                $id_tema_evento = 115; // Asignar Coordinadores a la propuesta
                $descripcion_evento = "Su Propuesta No. " . $id_propuesta_doc . " Versión " . $id_version_doc . 
                        " ** Con Título " . $titulo_propuesta . " ha sido RECHAZADA POR EL ADMINISTRADOR y "
                        . "se ha creado una nueva versión para que la envie nuevamente. --- " . $nota;
                $descripcion_Correo = "Su Propuesta No. " . $id_propuesta_doc . " Versión " . $id_version_doc . 
                        "<br>*** Con Título " . $titulo_propuesta . " ha sido <b>RECHAZADA</b> POR EL ADMINISTRADOR y "
                        . "<br>se ha creado una nueva versión para que la envie nuevamente. <br> --- " . $nota;
                
        }
        elseif ($id_estatus == 9){ //Enviada a Coordinadores y Jefes de Dpto para su revisión
            $id_tipo_evento = 25; //aprobación
            $id_tema_evento = 115; // Asignar Coordinadores a la propuesta
            $descripcion_evento = "Su Propuesta No. " . $id_propuesta_doc . " Versión " . $id_version_doc . 
                    " *** Con Título " . $titulo_propuesta . " ha sido ENVIADA a los Coordinadores y Jefes de Dpto Correspondientes para su revisión. --- " . $nota ;
            $descripcion_Correo = "Su Propuesta No. " . $id_propuesta_doc . " Versión " . $id_version_doc . 
                    "<br>*** Con Título " . $titulo_propuesta . " ha sido <b>ENVIADA</b> a los Coordinadores y Jefes de Dpto Correspondientes para su revision. <br> --- " . $nota ;            
        }
        
        try{                
            $cnn = new Connection();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
            
            /*Iniciar la transacción. */
            $conn->beginTransaction();

            //Actualizamos el estatus de la Propuesta
            /* Query parametrizado. */
            $tsql=" UPDATE propuesta_version SET " .
                    "id_estatus =  ?, " .
                    "fecha_verificacion = ?, " .
                    "nota = ?, " .
                    "id_administrador = ? " .
                    "WHERE id_propuesta = ? AND " .
                    "id_documento =? AND version_propuesta = ?;";                    
            /* Valor de los parámetros. */
            $params = array(
                        $id_estatus, date('d-m-Y H:i:s'),
                        $nota, $id_administrador,
                        $id_propuesta_doc, $id_documento_doc, $id_version_doc);
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
            
            //Agregamos a los coordinadores y dptos que darán el VoBo a la Propuesta
            if($id_estatus ==9){    
                //9. Por Autorizar Coordinadores
                //Obtener las claves de los jefes de las coordinaciones y departamentos seleccionados
//                $arr_coordinaciones = preg_split("/[,]/", $coordinaciones);
//                $arr_departamentos = preg_split("/[,]/", $departamentos);
//                $arr_departamentos = count($arr_alumnos_por_carrera);
                
                //Agregar cada uno de ellos a la tabla propuesta VoBo
                /* Obtenemos a los jefes de cada Coordinacion y Depto seleccionado. */
                if (!$coordinaciones){
                    $coordinaciones = -1;
                }
                if(!$departamentos){
                    $departamentos = -1;
                }
                
//                if($coordinaciones && $departamentos){
                $tsql3=" SELECT a.id_coordinacion as id_organo, 
                            a.descripcion_coordinacion as descripcion_organo, b.id_usuario as clave_usuario, c.email_usuario
                        FROM Coordinaciones a
                            INNER JOIN jefes_coordinacion b ON a.id_coordinacion = b.id_coordinacion 
                            INNER JOIN usuarios c ON b.id_usuario = c.id_usuario
                        WHERE b.actual_jefe = '1' AND a.id_coordinacion IN (". $coordinaciones . ")
                        UNION
                        SELECT a.id_departamento as id_organo, 
                            a.descripcion_departamento as descripcion_organo, b.id_usuario as clave_usuario, c.email_usuario
                        FROM Departamentos a
                                INNER JOIN jefes_departamento b ON a.id_departamento = b.id_departamento
                                INNER JOIN usuarios c ON b.id_usuario = c.id_usuario
                        WHERE b.actual_jefe = '1' AND a.id_departamento IN (". $departamentos . ")";                      

                
                /* Preparamos la sentencia a ejecutar */
                $stmt3= $conn->prepare($tsql3);
                if($stmt3){
                    /*Ejecutamos el Query*/                
//                    $result3 = $stmt3->execute($params3); 
                    $result3 = $stmt3->execute(); 
                    if ($result3){
                        if($stmt3->rowCount() > 0){     
                                                    
                            $tsql4 = " INSERT INTO propuesta_VoBo(
                                            id_propuesta, id_documento, version_propuesta, id_usuario, 
                                            nota, id_estatus, id_division)
                                       VALUES (?, ?, ?, ?, ?, ?, ?);";

                            //Insertamos la clave de los jefes de coordinación y departamento seleccionados
                            while($row = $stmt3->fetch(PDO::FETCH_ASSOC)){
                                $usuario_vobo = $row['clave_usuario'];
                                $destinatarios_de_correo .= $row['email_usuario'] . ',';
                                $destinatarios_bitacora .= $row['clave_usuario'] . ',';
                                $params4 = array($id_propuesta_doc, $id_documento_doc, $id_version_doc, $usuario_vobo,'', $id_estatus, $id_division);

                                $stmt4 = $conn->prepare($tsql4);
                                if($stmt4){
                                    $result4=$stmt4->execute($params4);
                                    if ($result4){
                                            if($stmt4->rowCount() > 0){
                                                $mensaje_Transacciones .= "Jefe Coordinación/Departamento Agregado. OK.<br/>";
                                            }
                                            else{
                                                $error = $stmt4->errorInfo();
                                                $mensaje_Transacciones = "Ocurrió un error";
                                                throw new Exception($mensaje_Transacciones);                                                              
                                            }
                                    }
                                    else{
                                        $error = $stmt4->errorInfo();
                                        $mensaje_Transacciones = "Ocurrió un error";
                                        throw new Exception($mensaje_Transacciones);        
                                    }
                                }
                                else{
                                    $error = $stmt4->errorInfo();
                                    $$mensaje_Transacciones = "Ocurrió un error";
                                    throw new Exception($mensaje_Transacciones);                                            
                                }
                            }   //while
                            if($destinatarios_de_correo != ''){
                                $destinatarios_de_correo = substr($destinatarios_de_correo, 0, strlen($destinatarios_de_correo)-1);
                            }
                            if($destinatarios_bitacora != ''){
                                $destinatarios_bitacora = substr($destinatarios_bitacora, 0, strlen($destinatarios_bitacora)-1);
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
                }
                else{
                    $error = $stmt3->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un error";
                    throw new Exception($mensaje_Transacciones);        
                }                        
            }                     
                            
            //Agregamos una nueva versión del Documento para que el Profesor Reenvíe su Documento
            if ($id_estatus ==4){ //4. Rechazado
                /* Query parametrizado. */
                $tsql2=" INSERT INTO propuesta_version(id_propuesta, id_documento, version_propuesta,
                        fecha_generada, nota, id_estatus, id_division) VALUES(
                        ?,?,?,?,?,?,?);";
                    
                /* Valor de los parámetros. */
                $params2 = array($id_propuesta_doc, $id_documento_doc, $id_version_doc + 1,
                    date('d-m-Y H:i:s'), '', 1, $id_division); //1. Sin Enviar
                /* Preparamos la sentencia a ejecutar */
                $stmt2 = $conn->prepare($tsql2);
                if($stmt2){
                    /*Ejecutamos el Query*/                
                    $result2 = $stmt2->execute($params2); 
                    if ($result2){
                        if($stmt2->rowCount() > 0){                    
                            $mensaje_Transacciones .= "Nuevo Documento Agregado para su Reenvío. OK.<br/>";
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
                //BORRAMOS EL ARCHIVO PDF
                $archivo_pdf = $_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/Docs/Propuestas_Profesor/'.
                            $id_profesor .'_'.$id_propuesta_doc.'_'.
                            $id_version_doc.'_'.$desc_documento.'.pdf';                    
                 
                if (file_exists($archivo_pdf)) {
                    unlink($archivo_pdf);
                }                           
                
            }
            
            $conn->commit();
            if($id_estatus==4){
                $mensaje_Transacciones = "Documento rechazado";
            }else{
                $mensaje_Transacciones = "El documento se mandó a los coordinadores para su aprobación";
            }

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_evento;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento);
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_profesor);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);            
            
            $obj = new d_mail();
            $mi_mail = new Mail();
            $mensaje= $descripcion_Correo;
                                   
            $mi_mail->set_Correo_Destinatarios($correo_profesor);
            $mi_mail->set_Asunto('AVISO APROBACIÓN DE PROPUESTA');
            $mi_mail->set_Mensaje($mensaje);

            if($id_estatus == 9){ //Enviada a Coordinadores y Jefes de Dpto
                $mi_mail->set_Correo_Copia_Oculta($destinatarios_de_correo);
            }
            
            $respuesta_mail = $obj->Envair_Mail($mi_mail);
            
            $destinatarios_bitacora .= "," . $id_profesor;
            $arr_destinatarios_bitacora = preg_split("/[,]/", $destinatarios_bitacora);
            $renglones = count($arr_destinatarios_bitacora); 
            
            for($i=0; $i < $renglones; $i++ ){
                sleep(1);
                $obj_miBitacora = new Bitacora();

                $descripcionEvento = $mensaje;
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
                $obj_miBitacora->set_Id_Tipo_Evento(50);
                $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
                $obj_miBitacora->set_Id_Usuario_Destinatario($arr_destinatarios_bitacora[$i]);
                $obj_miBitacora->set_Descripcion_Evento($descripcion_Correo);
                $obj_miBitacora->set_Id_Division($id_division);

                $resultado_Bitacora ='';
                $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora); 
            }        
            $conn = null;
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora;//. $respuesta_mail;
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
    //FIN ACTUALIZAMOS EL ESTATUS DEL DOCUMENTO

    //OBTENEMOS BITACORA DE PROPUESTAS
    function Obtener_Bitacora_Propuestas($id_propuesta){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_propuesta, a.version_propuesta, a.id_estatus, c.descripcion_estatus,TO_CHAR (a.fecha_generada, 'dd-mm-yyyy') as fecha_generada, a.nota, d.id_profesor, concat(u.nombre_usuario,' ', u.apellido_paterno_usuario, ' ', u.apellido_materno_usuario) as profesor,
                            d.titulo_propuesta, d.id_tipo_propuesta, tp.descripcion_tipo_propuesta 
                    FROM propuesta_version a
                            INNER JOIN documentos b ON a.id_documento = b.id_documento
                            INNER JOIN estatus c ON a.id_estatus = c.id_estatus
                            INNER JOIN propuestas_profesor d ON a.id_propuesta = d.id_propuesta
                            INNER JOIN tipos_propuesta tp ON d.id_tipo_propuesta = tp.id_tipo_propuesta
                            INNER JOIN usuarios u ON d.id_profesor = u.id_usuario
                    WHERE a.id_propuesta = ?
                    ORDER BY a.id_documento, a.fecha_generada;";
                        
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
    } //Fin Obtener Bitacora de Propuestas    

    
}
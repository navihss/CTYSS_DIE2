<?php

/**
 * Definición de la Capa de Datos para Agregar los Docs del Usuario
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Octubre 2016
 */
header('Content-Type: text/html; charset=UTF-8');

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/zonaHoraria.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Bitacora.php');
class d_profesor_Mis_Docs {

    function Agregar_Documento($id_usuario, $id_doc, $nombre_archivo){
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

            $tsql1=" DELETE FROM documentacion_del_usuario
                    WHERE id_usuario = ? AND id_documento = ?;
                ";
            /* Valor de los parámetros. */
            $params1 = array($id_usuario, $id_doc);
            
                       
            $tsql2=" INSERT INTO documentacion_del_usuario
                    (id_usuario,
                    id_documento,
                    compartido,
                    nombre_archivo,
                    fecha_enviado)
                    VALUES
                    (?, ?, ?, ?, ?);
                ";
            /* Valor de los parámetros. */
            $params2 = array($id_usuario, $id_doc, 0, $nombre_archivo,date('d-m-Y H:i:s'));
                
            
            /* Borramos el ID del documento*/
            $stmt1 = $conn->prepare($tsql1);

            /*Verificamos el contenido de la ejecución*/                        
            if($stmt1){        
                /*Ejecutamos el Query*/
                $result1 = $stmt1->execute($params1); 
            }
            else{
                $error = $stmt1->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para Borrar el Documento " . $id_doc . ".<br/>"  . $error[2] .'<br>';
                throw new Exception($mensaje_Transacciones);                                
            }   
                   
            /* Agregamos el nuevo Documento */
            $stmt2 = $conn->prepare($tsql2);
            if($stmt2){
                /*Ejecutamos el Query*/                
                $result2 = $stmt2->execute($params2); 
                if (!$result2===FALSE){
                        if($stmt2->rowCount() > 0){                    
                            $mensaje_Transacciones .= "Documento Agregado. OK.<br><br>";
                        }
                        else{
                            $error = $stmt2->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Agregar el Documento<br>."  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                }
                else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros. NO se Agregó el Documento.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Agregar el Documento.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }

            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones;
            return json_encode($jsondata);
        }
        catch (Exception $ex){ 
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            return json_encode($jsondata);
        }        
    }

    function Borrar_Documento($id_usuario, $id_doc, $nom_archivo){
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

            $tsql2=" DELETE FROM documentacion_del_usuario
                    WHERE id_usuario = ? AND id_documento = ?;
                ";
            $params2 = array($id_usuario, $id_doc);

            /* Borramos el Documento */
            $stmt2 = $conn->prepare($tsql2);
            if($stmt2){
                /*Ejecutamos el Query*/                
                $result2 = $stmt2->execute($params2); 
                if (!$result2===FALSE){
                        if($stmt2->rowCount() > 0){                    
                            $mensaje_Transacciones .= "Documento Borrado. OK.<br><br>";
                            $nom_archivo = $_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/Docs/Docs_Profesores/'. utf8_decode($nom_archivo);
                            if (file_exists($nom_archivo)) {
                                unlink($nom_archivo);
                            }                        }
                        else{
                            $error = $stmt2->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Borrar el Documento<br>."  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                }
                else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros. NO se Borro el Documento.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Borrar el Documento.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }

            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones;
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

    function Mis_Documentos($id_usuario){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
          
            $tsql = "SELECT id_usuario, id_documento, compartido, nombre_archivo
                    FROM documentacion_del_usuario
                    WHERE id_usuario = ?;";
                        
            /* Valor de los parámetros. */
            $params = array($id_usuario);
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
                        $mensaje_Transacciones = "No hay Documentos Compartidos.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Documentos.<br/>"  . $error[2];
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
    
    function Compartir_Documento($id_usuario, $id_doc, $compartir){
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $accion = 'El Documento se ha Compartido.';
        if($compartir==0){$accion = 'El Documento se ha dejado de Compartir.';}
        try{                   
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }                        

            $tsql2=" UPDATE documentacion_del_usuario
                    SET compartido = ?
                    WHERE id_usuario = ? AND id_documento = ?;
                ";
            $params2 = array($compartir, $id_usuario, $id_doc);

            /* Borramos el Documento */
            $stmt2 = $conn->prepare($tsql2);
            if($stmt2){
                /*Ejecutamos el Query*/                
                $result2 = $stmt2->execute($params2); 
                if (!$result2===FALSE){
                        if($stmt2->rowCount() > 0){                    
                            $mensaje_Transacciones .= $accion . " OK.<br><br>";
                        }
                        else{
                            $error = $stmt2->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Compartir el Documento<br>."  . $error[2] .'<br>';
                            throw new Exception($mensaje_Transacciones);                                                              
                        }
                }
                else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros. NO se pudo Compartir el Documento.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Compartir el Documento.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }

            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones;
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
    
}


<?php
/**
 * Definición de la Capa de Datos para Cambiar la contraseña del alumno
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */
header('Content-Type: text/html; charset=UTF-8');

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/zonaHoraria.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Bitacora.php');

class d_alumno_Cambio_Contrasena {

  function Cambiar_Contrasena($id_usuario, $contrasenaNueva, $id_division){
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
                           
            $tsql=" UPDATE usuarios SET ".
                    "contrasena_usuario =  ? ".
                    "WHERE id_usuario = ?;";                    
                /* Valor de los parámetros. */
                $params = array($contrasenaNueva, $id_usuario);

                /* Preparamos la sentencia a ejecutar */
                $stmt = $conn->prepare($tsql);
                if($stmt){
                    /*Ejecutamos el Query*/                
                    $result = $stmt->execute($params); 
                    if ($result){
                            if($stmt->rowCount() > 0){                    
                                $mensaje_Transacciones .= "La Contraseña del Usuario fué Actualizada. OK.<br/>";
                            }
                            else{
                                $error = $stmt->errorInfo();
                                $mensaje_Transacciones .= "No se pudo Actualizar la Contraseña del Usuario.<br>"  . $error[2] .'<br>';
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se actualizó la Contraseña del Usuario.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }
                }
                else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar la Contraseña del Usuario.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }

                $id_tema_evento = 50; //Cambio de contraseña
                $id_tipo_evento = 15; // Actualización
                $descripcion_evento = "Cambio de Contraseña para el Usuario : " . $id_usuario; 

                $obj_Bitacora = new d_Usuario_Bitacora();
                $obj_miBitacora = new Bitacora();

                $descripcionEvento = $descripcion_evento;
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
                $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento);
                $obj_miBitacora->set_Id_Usuario_Genera($id_usuario);
                $obj_miBitacora->set_Id_Usuario_Destinatario('');
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($id_division);

                $resultado_Bitacora ='';
                $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);                
                
                $jsondata['success'] = true;
                $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora;
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

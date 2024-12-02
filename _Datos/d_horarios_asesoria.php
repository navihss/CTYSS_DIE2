<?php

/**
 * Definición de la Capa de Datos para los Días de Asesoría
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Octubre 2016
 */
header('Content-Type: text/html; charset=UTF-8');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/Conexion.php');

class d_horarios_asesoria {

    function Obtener(){

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
            $tsql=" select a.id_dia, a.id_horario, b.descripcion_dias, c.horario
                    from horarios_asesoria a
                    inner join dias_asesoria b on a.id_dia = b.id_dia
                    inner join horas_asesoria c on a.id_horario = c.id_horario
                    order by a.id_dia, a.id_horario;";

            /* Valor de los parámetros. */
            $params = array();
            
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

                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
//                            $jsondata['data']['registros'][] = $row;
                            $jsondata['data']['registros'][$row['descripcion_dias']][] = array('id_horario'=>$row['id_horario'], 
                                'id_dia'=>$row['id_dia'], 'horario'=>$row['horario']);
                        }
                        $stmt=null;
                        $conn=null;
                        echo json_encode($jsondata);
                        exit();
                    }
                    else{
                        $mensaje_Transacciones .= "No Existen Días de Asesoría.";
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se obtuvieron los Dias de Asesoría.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para obtener los Horarios de Asesoría.<br/>"  . $error[2] .'<br>';
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
    
}
//
//$obj = new d_horarios_asesoria();
//echo $obj->Obtener();
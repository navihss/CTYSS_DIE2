<?php
use App\Database\Connection;
/**
 * Definición de la Capa de Datos para la Clase Alumno Mi Fecha Titulacion
 * Metodos
 * @author Carlos Alfonso Aguilar Castro
 * Agosto 2024
 */
    header('Content-Type: text/html; charset=UTF-8');
    require_once __DIR__ . '/../app/Database/Connection.php';
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
    
class d_Alumno_Mi_Fecha_Titulacion {

    function Obtener_Fecha_Titulacion($id_alumno){
        
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
            
            $tsql ="SELECT ac.fecha_titulacion
                    FROM alumno_carrera ac
                    WHERE ac.id_alumno = ?;";
                    
            /* Valor de los parámetros. */
            $params = array($id_alumno);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            /*Verificamos el contenido de la ejecución*/                        
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);                                 
                if ($result){                    
                    if($stmt->rowCount() > 0){                                                
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'EXISTE_FECHA';
                        $jsondata['data']['registros'] = array();

                        while($row = $stmt->fetch(PDO::FETCH_OBJ)){
                            $jsondata['data']['registros'][] = $row;
                        }
                        $stmt=null;
                        $conn=null;
                        return $jsondata;
                    }
                    else{
                        $jsondata['success'] = false;
                        $jsondata['data']= array('message'=>'NO_EXISTE_FECHA');
                        $stmt=null;
                        $conn=null;                       
                        return $jsondata; 
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
}

?>

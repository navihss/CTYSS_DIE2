<?php
use App\Database\Connection;
header('Content-Type: text/html; charset=UTF-8');
require_once __DIR__ . '/../app/Database/Connection.php';
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');

class d_administrador_admon_datos_alumno {

    function Obtener_Datos_Alumno($id_numero_cuenta){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
   
            $tsql = "select
                        a.id_alumno,
                        (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario) as nombre,
                        c.descripcion_carrera,
                        u.email_usuario,
                        a.telefono_fijo_alumno,
                        a.telefono_celular_alumno,
                        a.semestre_ingreso_fi_alumno,
                        ac.fecha_titulacion
                    from
                        alumnos a
                    join usuarios u on
                        a.id_alumno = u.id_usuario
                    join alumno_carrera ac on
                        a.id_alumno = ac.id_alumno
                    join carreras c on
                        ac.id_carrera = c.id_carrera
                    where 
                        a.id_alumno = ?;";
                        
            /* Valor de los parámetros. */
            $params = array($id_numero_cuenta);
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
                        $mensaje_Transacciones = "No hay información del número de cuenta.". $id_numero_cuenta."<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener la información del alumno.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Reportes Bimestrales
}
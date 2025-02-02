<?php

header('Content-Type: text/html; charset=UTF-8');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/zonaHoraria.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Bitacora.php');


class d_Alumno_Mis_Reportes {

        function Obtener_Mi_SS_x_Carrera($id_alumno){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
            
            //Obtenemos los Servicios Sociales (3. Aceptados) x Carrera (5. Activas)
//            $tsql = "SELECT a.id_ss, a.id_carrera, a.id_alumno, c.descripcion_carrera ".
//                    "FROM servicio_social a " .
//                    "INNER JOIN alumno_carrera b ON (a.id_carrera = b.id_carrera AND a.id_alumno = b.id_alumno) " .
//                    "INNER JOIN carreras c ON b.id_carrera = c.id_carrera " .
//                    "WHERE a.id_alumno = ? AND a.id_estatus IN (3,8) AND b.id_estatus =5 " .
//                    "ORDER BY a.id_carrera;";

            $tsql =" SELECT a.id_ss, b.descripcion_carrera, a.id_carrera
                    FROM servicio_social a 
                            INNER JOIN carreras b ON a.id_carrera = b.id_carrera
                    WHERE a.id_alumno = ? AND a.id_estatus IN (3,8)
                    ORDER BY b.descripcion_carrera, a.id_ss;";

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
                        $mensaje_Transacciones = "No tiene un Servicio Social Autorizado.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener las Carreras del Alumno.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Mi Servicio Social x Carrera
    
    function Obtener_Mis_Reportes($id_ss){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_ss, a.numero_reporte_bi, 
                    a.fecha_prog_inicio, a.fecha_prog_fin, a.id_version, a.id_estatus,
                    a.fecha_real_inicio, a.fecha_real_fin, 
                    a.fecha_recepcion_rpt, a.horas_laboradas, a.nota, 
                    a.id_estatus, b.descripcion_estatus, horas_obligatorias, c.id_alumno, c.id_carrera,
                        (SELECT max(numero_reporte_bi)
                        FROM reportes_bimestrales
                        WHERE id_ss = a.id_ss AND id_estatus = 1) as ultimo_reporte
                    FROM reportes_bimestrales a INNER JOIN estatus b ON a.id_estatus = b.id_estatus 
                        INNER JOIN servicio_social c ON a.id_ss=c.id_ss
                    WHERE a.id_ss= ? 
                    ORDER BY a.numero_reporte_bi, a.id_version;";
                        
            /* Valor de los parámetros. */
            $params = array($id_ss);
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
                        $mensaje_Transacciones = "No hay información del Calendario de Entregas de los Reportes Bimestrales.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Calendario de Entregas de los Reportes Bimestrales.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Mis Reportes Bimestrales


    //TOTALES
    function Obtener_Total_Reportes($id_estatus, $id_alumno){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT count(a.id_ss) as total5
                    FROM reportes_bimestrales a INNER JOIN estatus b ON a.id_estatus = b.id_estatus 
                        INNER JOIN servicio_social c ON a.id_ss=c.id_ss
                    WHERE a.id_estatus = ? and c.id_alumno = ?";
                    //ORDER BY a.numero_reporte_bi, a.id_version;";
                        
            /* Valor de los parámetros. */
            $params = array($id_estatus, $id_alumno);
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

                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            $jsondata['data']['registros'][] = $row;
                        }
                        $stmt=null;
                        $conn=null;
                        //echo json_encode($jsondata);
                        //exit();
                        return $jsondata;
                    }
                    else{
                        $mensaje_Transacciones = "No hay información del Calendario de Entregas de los Reportes Bimestrales.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Calendario de Entregas de los Reportes Bimestrales.<br/>"  . $error[2];
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
    //FIN TOTALES
    
    function Obtener_Datos_Grales($id_ss){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT c.id_alumno, a.id_ss, a.numero_reporte_bi, 
                    a.fecha_prog_inicio, a.fecha_prog_fin, a.id_version, a.id_estatus,
                    a.fecha_recepcion_rpt, a.fecha_real_inicio, a.fecha_real_fin, 
                    a.horas_laboradas, b.descripcion_estatus, a.nota, a.horas_obligatorias 
                    FROM reportes_bimestrales a 
			INNER JOIN estatus b ON a.id_estatus = b.id_estatus 
			INNER JOIN servicio_social c ON a.id_ss = c.id_ss
                    WHERE c.id_ss = ? 
                    ORDER BY a.numero_reporte_bi, a.id_version;";
                        
            /* Valor de los parámetros. */
            $params = array($id_ss);
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
                        $mensaje_Transacciones = "No hay información general del Calendario de Entregas de los Reportes Bimestrales.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Calendario de Entregas de los Reportes Bimestrales.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Datos Generales de los Reportes Bimestrales    

    
    //ACTUALIZAMOS EL ESTATUS DEL REPORTE COMO ENVIADO
    function Actualizar_Estatus_Rpt_Enviado($id_ss, $numero_reporte_bi, $id_version, $id_estatus,
            $fecha_real_inicio,$fecha_real_fin, $horas_laboradas){
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
            $tsql=" UPDATE reportes_bimestrales SET ".
                    "id_estatus =  ?, ".
                    "fecha_recepcion_rpt = ?, ".
                    "horas_laboradas = ?, ".
                    "fecha_real_inicio = ?, " .
                    "fecha_real_fin = ? " .
                    "WHERE id_ss = ? AND " .
                    "numero_reporte_bi =? AND id_version = ?;";                    
            /* Valor de los parámetros. */
            $params = array(
                        $id_estatus, date('d-m-Y H:i:s'), $horas_laboradas,
                        $fecha_real_inicio, $fecha_real_fin,
                        $id_ss,
                        $numero_reporte_bi,
                        $id_version);
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
                        $mensaje_Transacciones .= "No se pudo Actualizar el Estatus del Reporte.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se actualizó el Estatus del Reporte.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Estatus del Reporte.<br/>"  . $error[2] .'<br>';
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
    //FIN ACTUALIZAMOS EL ESTATUS DEL REPORTE COMO ENVIADO
       
}
//
//$obj = new d_Alumno_Mis_Reportes();
//echo $obj->Obtener_Datos_Grales('201603-021');

//$obj = new d_Alumno_Mis_Reportes();
//echo $obj->Obtener_Mi_SS_x_Carrera('086198517');
?>

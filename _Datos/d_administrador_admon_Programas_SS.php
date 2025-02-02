<?php

header('Content-Type: text/html; charset=UTF-8');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/zonaHoraria.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Programa_SS.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Bitacora.php');

class d_administrador_admon_Programas_SS {

    function Obtener_Programas($id_programa, $desc_programa){
      
        try{                    
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
            $params = array();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }
   
            $filtro = 'WHERE ';
           
            if($id_programa){                
                $operador = $filtro != 'WHERE ' ? " OR " : " ";                 
                $filtro .= $operador . " a.id_programa = ? ";
                array_push($params, $id_programa);
            }
            if($desc_programa){                
                $operador = $filtro != 'WHERE ' ? " OR " : " ";                 
                $filtro .= $operador . " a.descripcion_pss ILIKE ? ";
                array_push($params, '%'.$desc_programa.'%');
            }
            
            if($filtro == 'WHERE '){
                $filtro = '';
            }
            
            $tsql = "SELECT a.id_programa, a.descripcion_pss,  b.descripcion_dependencia_ss, a.subdireccion_departamento_pss, 
                        a.responsable_pss, a.cargo_responsable_pss, a.email_pss, a.telefono_servicio_social_pss, 
                        c.descripcion_tipo_programa_ss, d.descripcion_estatus,
                        a.telefono_dependencia_ss_pss, a.id_dependencia_ss, a.tipo_programa_ss, 
                        a.id_estado_republica, a.id_estatus
                     FROM programas_ss a
                         LEFT JOIN dependencias_ss b ON a.id_dependencia_ss = b.id_dependencia_ss
                         LEFT JOIN tipos_programa_ss c ON a.tipo_programa_ss = c.id_tipo_programa_ss
                         LEFT JOIN estatus d ON a.id_estatus = d.id_estatus
                     " . $filtro . " " .  
                     "ORDER BY a.id_programa";
                        
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
                        $mensaje_Transacciones = "No hay información de Programas para Servicio Social.";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
                else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "Error en los parámetros para obtener Programas de Servicio Social.<br/>"  . $error[2];
                    throw new Exception($mensaje_Transacciones);                                      
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener Programas de Servicio Social.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Programas

    //OBTENEMOS EL PROGRAMA SELECCIONADO
    function Obtener_Programa($id_programa){

        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        
        $obj_programa = new Programa_SS();
        
        try{    
            
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
                
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
                           
            $tsql=" SELECT id_programa, descripcion_pss, subdireccion_departamento_pss, 
                        responsable_pss, cargo_responsable_pss, email_pss, oficina_seccion_pss, 
                        calle_pss, colonia_pss, delegacion_municipio_pss, codigo_postal_pss, 
                        num_exterior_pss, num_interior_pss, telefono_servicio_social_pss, 
                        telefono_dependencia_ss_pss, id_dependencia_ss, tipo_programa_ss, 
                        id_estado_republica, id_estatus
                   FROM programas_ss
                   WHERE id_programa = ?;";
            /* Valor de los parámetros. */
            $params = array($id_programa);
            
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result === FALSE){
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros para obtener la información del Programa seleccionado.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                            
                }
                else{
                    if($stmt->rowCount() > 0){                    
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $obj_programa->set_CP($row['codigo_postal_pss']);
                        $obj_programa->set_Calle($row['calle_pss']);
                        $obj_programa->set_Cargo($row['cargo_responsable_pss']);
                        $obj_programa->set_Colonia($row['colonia_pss']);
                        $obj_programa->set_Delegacion($row['delegacion_municipio_pss']);
                        $obj_programa->set_Descripcion($row['descripcion_pss']);
                        $obj_programa->set_Email($row['email_pss']);
                        $obj_programa->set_Id_Dependencia($row['id_dependencia_ss']);
                        $obj_programa->set_Id_Estado($row['id_estado_republica']);
                        $obj_programa->set_Id_Estatus($row['id_estatus']);
                        $obj_programa->set_Id_Programa($row['id_programa']);
                        $obj_programa->set_Num_Exterior($row['num_exterior_pss']);
                        $obj_programa->set_Num_Interior($row['num_interior_pss']);
                        $obj_programa->set_Oficina($row['oficina_seccion_pss']);
                        $obj_programa->set_Responsable($row['responsable_pss']);
                        $obj_programa->set_Subdireccion($row['subdireccion_departamento_pss']);
                        $obj_programa->set_Telefono_Dependencia($row['telefono_dependencia_ss_pss']);
                        $obj_programa->set_Telefono_SS($row['telefono_servicio_social_pss']);
                        $obj_programa->set_Tipo_Programa($row['tipo_programa_ss']);
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se encontró información del Programa seleccionado.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }                    
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para obtener información del Programa seleccionado.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            }

            //Obtenemos las carreras del programa
            /* Query parametrizado. */
            $tsql=" SELECT a.id_carrera, a.id_programa, b.descripcion_carrera
                    FROM programa_carrera a
                    INNER JOIN carreras b ON a.id_carrera = b.id_carrera
                    WHERE a.id_programa = ?;";

            /* Valor de los parámetros. */
            $params = array($id_programa);
            
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if($stmt){
                /*Ejecutamos el Query*/                
                $result = $stmt->execute($params); 
                if ($result === FALSE){
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se obtuvo información de las Carreras en las que Aplica el Programa seleccionado.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                            
                }
                else{
                    if($stmt->rowCount() > 0){                    
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['registros'] = array();

                        $id_carrera='';
                        $desc_carrera = '';
                        $cadena_requerimiento = '';
                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            $id_carrera = $row['id_carrera'];
                            $desc_carrera = $row['descripcion_carrera'];
                            $cadena_requerimiento .= $id_carrera .','.$desc_carrera.'|';
                        }
                        $cadena_requerimiento = substr($cadena_requerimiento, 0, strlen($cadena_requerimiento)-1);
                        $obj_programa->set_Carreras($cadena_requerimiento);
                        $jsondata['data']['registros']= (array) $obj_programa;
                        echo str_replace('\\u0000', "", json_encode($jsondata));
                        exit();
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se encontró información de las Carreras en las que Aplica el Programa seleccionado.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }                    
                }
            }
            else{
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para obtener información de las Carreras en las que Aplica el Programa seleccionado.<br/>"  . $error[2] .'<br>';
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
    //FIN OBTENER EL PROGRAMA
  
    
    //AGREGAMOS EL PROGRAMA
    function Agregar($obj_programa, $id_administrador, $carreras, $id_division){
 
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

            $obj_Programa = new Programa_SS();
            $obj_Programa = $obj_programa;
            
            /* Query parametrizado. */
            $tsql2=" INSERT INTO programas_ss(
            id_programa, descripcion_pss, subdireccion_departamento_pss, 
            responsable_pss, cargo_responsable_pss, email_pss, oficina_seccion_pss, 
            calle_pss, colonia_pss, delegacion_municipio_pss, codigo_postal_pss, 
            num_exterior_pss, num_interior_pss, telefono_servicio_social_pss, 
            telefono_dependencia_ss_pss, id_dependencia_ss, tipo_programa_ss, 
            id_estado_republica, id_estatus, id_division)
            VALUES (?, ?, ?, 
                    ?, ?, ?, ?, 
                    ?, ?, ?, ?, 
                    ?, ?, ?, 
                    ?, ?, ?, 
                    ?, ?, ?);";

            /* Valor de los parámetros. */
            $params2 = array($obj_Programa->get_Id_Programa(), $obj_Programa->get_Descripcion(),
                $obj_Programa->get_Subdireccion(), $obj_Programa->get_Responsable(),
                $obj_Programa->get_Cargo(), $obj_Programa->get_Email(), $obj_Programa->get_Oficina(),
                $obj_Programa->get_Calle(), $obj_Programa->get_Colonia(), $obj_Programa->get_Delegacion(), $obj_Programa->get_CP(),
                $obj_Programa->get_Num_Exterior(), $obj_Programa->get_Num_Interior(), $obj_Programa->get_Telefono_SS(),
                $obj_Programa->get_Telefono_Dependencia(), $obj_Programa->get_Id_Dependencia(), $obj_Programa->get_Tipo_Programa(),
                $obj_Programa->get_Id_Estado(), $obj_Programa->get_Id_Estatus(), $id_division);
            /* Preparamos la sentencia a ejecutar */
            $stmt2 = $conn->prepare($tsql2);
            if($stmt2){
                /*Ejecutamos el Query*/                
                $result2 = $stmt2->execute($params2); 
                if ($result2){
                    if($stmt2->rowCount() > 0){                    
                        $mensaje_Transacciones .= "Programa Agregado. OK.<br/>";
                    }
                    else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Agregar el Programa.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros para Agregar el Programa.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Agregar el Programa.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            } 

            //AGREGAMOS TODAS LAS CARRERAS QUE APLICAN
            $arr_carrera = preg_split("/[|]/", $carreras);
            $renglones = count($arr_carrera);

            $tsql4=" INSERT INTO programa_carrera(
                            id_carrera, id_programa, id_division)
                    VALUES (?, ?, ?);";

            for($i=0; $i < $renglones; $i++ ){
                $stmt4 = $conn->prepare($tsql4);
                if ($stmt4){
                    $result4=$stmt4->execute(array($arr_carrera[$i], $obj_Programa->get_Id_Programa(), $id_division));
                    if (!$result4 === FALSE){
                            if($stmt4->rowCount() > 0){
                                $mensaje_Transacciones .= "Carreras que Aplican Agregadas. OK.<br/>";
                            }
                            else{
                                $error = $stmt4->errorInfo();
                                $mensaje_Transacciones .= "Error al Agregar las Carreras que Aplican.<br>"  . $error[2] .'<br>';
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt4->errorInfo();
                        $mensaje_Transacciones .= "Error en los parámetros para Agregar las Carreras que Aplican.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }                    
                }
                else{
                        $error = $stmt4->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Agregar las Carreras que Aolican.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }                        
            }               
            //FIN AGREGAMOS TODAS LAS CARRERAS QUE APLICAN
            
            $conn->commit();

            $mensaje_Transacciones = "Programa de Servicio Social agregado correctamente.";

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = "Alta del Programa " . $obj_Programa->get_Id_Programa().'*** '. 
                    $obj_Programa->get_Descripcion();
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(155);
            $obj_miBitacora->set_Id_Tipo_Evento(5);
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);            
            
  
            $conn = null;
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora;
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
    //FIN AGREGAMOS EL PROGRAMA

    //ACTUALIZAR EL PROGRAMA
    function Actualizar($obj_programa, $id_administrador, $id_division){
 
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

            $obj_Programa = new Programa_SS();
            $obj_Programa = $obj_programa;
            
            /* Query parametrizado. */
            $tsql2=" UPDATE programas_ss
                        SET descripcion_pss=?, subdireccion_departamento_pss=?, 
                            responsable_pss=?, cargo_responsable_pss=?, email_pss=?, oficina_seccion_pss=?, 
                            calle_pss=?, colonia_pss=?, delegacion_municipio_pss=?, codigo_postal_pss=?, 
                            num_exterior_pss=?, num_interior_pss=?, telefono_servicio_social_pss=?, 
                            telefono_dependencia_ss_pss=?, id_dependencia_ss=?, tipo_programa_ss=?, 
                            id_estado_republica=?, id_estatus=?
                      WHERE id_programa = ?;";

            /* Valor de los parámetros. */
            $params2 = array($obj_Programa->get_Descripcion(),
                $obj_Programa->get_Subdireccion(), $obj_Programa->get_Responsable(),
                $obj_Programa->get_Cargo(), $obj_Programa->get_Email(), $obj_Programa->get_Oficina(),
                $obj_Programa->get_Calle(), $obj_Programa->get_Colonia(), $obj_Programa->get_Delegacion(), $obj_Programa->get_CP(),
                $obj_Programa->get_Num_Exterior(), $obj_Programa->get_Num_Interior(), $obj_Programa->get_Telefono_SS(),
                $obj_Programa->get_Telefono_Dependencia(), $obj_Programa->get_Id_Dependencia(), $obj_Programa->get_Tipo_Programa(),
                $obj_Programa->get_Id_Estado(), $obj_Programa->get_Id_Estatus(),$obj_Programa->get_Id_Programa());
            /* Preparamos la sentencia a ejecutar */
            $stmt2 = $conn->prepare($tsql2);
            if($stmt2){
                /*Ejecutamos el Query*/                
                $result2 = $stmt2->execute($params2); 
                if ($result2){
                    if($stmt2->rowCount() > 0){                    
                        $mensaje_Transacciones .= "Programa Actualizado. OK.<br/>";
                    }
                    else{
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Actualizar el Programa.<br>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                                              
                    }
                }
                else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros para Actualizar el Programa.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);        
                }
            }
            else{
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Programa.<br/>"  . $error[2] .'<br>';
                    throw new Exception($mensaje_Transacciones);                                
            } 

            //BORRAMOS TODAS LAS CARRERAS QUE APLICAN                  
            $tsql3=" DELETE FROM programa_carrera
                     WHERE id_programa  = ?;";
            /* Preparamos la sentencia a ejecutar */
            $stmt3 = $conn->prepare($tsql3);

            /* Valor de los parámetros. */
            $params3 = array($obj_Programa->get_Id_Programa());                    

            $result3 = $stmt3->execute($params3); 
            if ($result3){
                if($result3 > 0){                    
                    $mensaje_Transacciones .= "Carreras que Aplican Borradas. OK.<br/>";
                }
                else{
                    $error = $stmt3->errorInfo();
                    $mensaje_Transacciones .= "No hubo Carreras que Aplican al Programa.<br>"  . $error[2] .'<br>';
//                    throw new Exception($mensaje_Transacciones);                                                              
                }
            }        
            //FIN BORRAMOS TODAS LAS CARRERAS QUE APLICAN
                       
            //AGREGAMOS TODAS LAS CARRERAS QUE APLICAN
            $arr_carrera = preg_split("/[|]/", $obj_Programa->get_Carreras());
            $renglones = count($arr_carrera);

            $tsql4=" INSERT INTO programa_carrera(
                            id_carrera, id_programa, id_division)
                    VALUES (?, ?, ?);";

            for($i=0; $i < $renglones; $i++ ){
                $stmt4 = $conn->prepare($tsql4);
                if ($stmt4){
                    $result4=$stmt4->execute(array($arr_carrera[$i], $obj_Programa->get_Id_Programa(), $id_division));
                    if (!$result4 === FALSE){
                            if($stmt4->rowCount() > 0){
                                $mensaje_Transacciones .= "Carreras que Aplican Actualizadas. OK.<br/>";
                            }
                            else{
                                $error = $stmt4->errorInfo();
                                $mensaje_Transacciones .= "Error al Actualizar las Carreras que Aplican.<br>"  . $error[2] .'<br>';
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt4->errorInfo();
                        $mensaje_Transacciones .= "Error en los parámetros para Actualizar las Carreras que Aplican.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);        
                    }                    
                }
                else{
                        $error = $stmt4->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar las Carreras que Aolican.<br/>"  . $error[2] .'<br>';
                        throw new Exception($mensaje_Transacciones);                                
                }                        
            }               
            //FIN BORRAMOS TODAS LAS CARRERAS QUE APLICAN
            
            $conn->commit();

            $mensaje_Transacciones = "Programa de Servicio Social actualizado correctamente.";

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = "Actualización del Programa " . $obj_Programa->get_Id_Programa().'*** '. 
                    $obj_Programa->get_Descripcion();
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(155);
            $obj_miBitacora->set_Id_Tipo_Evento(15); //15. actualización
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);            
              
            $conn = null;
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora;
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
    //FIN ACTUALIZAR EL PROGRAMA
    
}
//$obj = new d_administrador_admon_Programas_SS();
//echo $obj->Obtener_Programa('2016-2/30-88');
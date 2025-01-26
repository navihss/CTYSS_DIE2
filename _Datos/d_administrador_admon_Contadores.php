<?php
/**
 * Definición de la Capa de Datos para la Clase Contadores de Serv Soc y Propuestas de Profesor
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */
use App\Database\Connection;

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../app/Database/Connection.php';
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');

class d_administrador_admon_Contadores {

   //Obtenemos el último contador para Servicio Social
    function Obtener_Ultimo_Contador_SS(){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT anio, mes, consecutivo
                    FROM servicio_social_contador
                    ORDER BY anio, mes, consecutivo DESC;";
            
            /* Valor de los parámetros. */
            $params = array();
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
                        $mensaje_Transacciones = "No hay Contadores para el Catálogo de Servicio Social.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Último Contador para Servicio Social.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Contador SS
    
   //Verificamos si existe el período
    function Existe_Periodo($nom_tabla, $periodo){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            if($nom_tabla == "servicio_social_contador"){
                $tsql = "SELECT count(anio) as cuantos
                        FROM servicio_social_contador
                        WHERE anio = ?;";
            }
            elseif($nom_tabla == "propuesta_profesor_contador"){
                $tsql = "SELECT count(anio) as cuantos
                        FROM propuesta_profesor_contador
                        WHERE anio = ?;";                
            }
            elseif($nom_tabla == "inscripcion_ceremonia_contador"){
                $tsql = "SELECT count(anio) as cuantos
                        FROM inscripcion_ceremonia_contador
                        WHERE anio = ?;";                
            }
                
            /* Valor de los parámetros. */
            $params = array($periodo);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            /*Verificamos el contenido de la ejecución*/                        
            $existen =0;
            if($stmt){        
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);                                 
                if ($result){                    
                    if($stmt->rowCount() > 0){                                                
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['registros'] = array();

                        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            $existen = $row['cuantos'];
                        }                        
                    }
                }
            }
            return $existen;
        }
        catch (Exception $ex){               
           return "ERROR";
        }          
    } //Fin Existe Período
        
    //Obtenemos el último contador para Propuesta
    function Obtener_Ultimo_Contador_Propuestas(){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT anio, semestre, consecutivo
                    FROM propuesta_profesor_contador
                    ORDER BY anio, semestre, consecutivo DESC;";
            
            /* Valor de los parámetros. */
            $params = array();
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
                        $mensaje_Transacciones = "No hay Contadores para el Catálogo de Propuestas.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Último Contador para Propuestas.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Contador Propuestas
    
   //Obtenemos el último contador para Ceremonia
    function Obtener_Ultimo_Ceremonia(){
      
        try{                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if( $cnn === false )
            {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT anio, semestre, consecutivo
                    FROM inscripcion_ceremonia_contador
                    ORDER BY anio, semestre, consecutivo DESC;";
            
            /* Valor de los parámetros. */
            $params = array();
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
                        $mensaje_Transacciones = "No hay Contadores para el Catálogo de Ceremonias.<br/>";
                        throw new Exception($mensaje_Transacciones);                                                                                
                    }
                }
            }
            else{
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Último Contador para Ceremonias.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);                  
            }                
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //Fin Obtener Contador Ceremonia
    
    function Agregar_Periodo_SS($periodo, $id_administrador, $id_division){
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
                
        try{    
            
            $cnn = new Connection();
            $conn = $cnn->getConexion();
                           
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }        
            
            $conn->beginTransaction();
            
            $obj_d = new d_administrador_admon_Contadores();
            $existen = $obj_d->Existe_Periodo('servicio_social_contador', $periodo);
            
            if ($existen > '0'){
                $mensaje_Transacciones .= "Este período Ya Existe.<br>";
                throw new Exception($mensaje_Transacciones);                    
            }

            $tsql=" INSERT INTO servicio_social_contador(
                anio, mes, consecutivo, id_division)
                VALUES (?,?,?,?);";

            for($i=1; $i <= 12; $i++ ){
                $stmt = $conn->prepare($tsql);
                if ($stmt){
                    $result=$stmt->execute(array($periodo, $i, 0));
                    if ($result){
                            if($stmt->rowCount() > 0){
                                //$mensaje_Transacciones .= "Mes " . $i . " Agregado. OK.<br/>";
                            }
                            else{
                                $error = $stmt->errorInfo();
                                $mensaje_Transacciones = "Ocurrió un error.";
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error.";
                        throw new Exception($mensaje_Transacciones);        
                    }                    

                }
                else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error.";
                        throw new Exception($mensaje_Transacciones);                                
                }                        
            }
            $conn->commit();
            
            $mensaje_Transacciones = "Contador de servicio social, generado correctamente.";
            //AGREGAMOS EL MOVIMIENTO A LA BITACORA
            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = "Contador de servicio social, generado correctamente.";
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(150); //admon. contadores
            $obj_miBitacora->set_Id_Tipo_Evento(5);//alta
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
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
            $conn->rollBack();
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();   
        }
    }   
    
   function Agregar_Periodo_PP($periodo, $id_administrador, $id_division){
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
                
        try{    
            
            $cnn = new Connection();
            $conn = $cnn->getConexion();
                           
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }        
            
            $conn->beginTransaction();
           
            $obj_d = new d_administrador_admon_Contadores();
            $existen = $obj_d->Existe_Periodo('propuesta_profesor_contador', $periodo);
            if ($existen > '0'){
                $mensaje_Transacciones .= "Este período Ya Existe.<br>";
                throw new Exception($mensaje_Transacciones);                    
            }

            $tsql=" INSERT INTO propuesta_profesor_contador(
                anio, semestre, consecutivo, id_division)
                VALUES (?,?,?,?);";
            
            for($j=1; $j<=2; $j++){
                $stmt = $conn->prepare($tsql);
                if ($stmt){
                    $result=$stmt->execute(array($periodo, $j, 0, $id_division));
                    if ($result){
                            if($stmt->rowCount() > 0){
                                $mensaje_Transacciones .= "Año " . $periodo . " Semestre " . $j . " Agregado. OK.<br/>";
                            }
                            else{
                                $error = $stmt->errorInfo();
                                $mensaje_Transacciones = "Ocurrió un error.";
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error.";
                        throw new Exception($mensaje_Transacciones);        
                    }                    

                }
                else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error.";
                        throw new Exception($mensaje_Transacciones);                                
                } 
            }
            $conn->commit();
            if ($periodo != 0) {
                $mensaje_Transacciones = "Contador de propuestas " . $periodo . " generado correctamente.";
            }
            //AGREGAMOS EL MOVIMIENTO A LA BITACORA
            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = 'Para las Propuestas de Profesor se Agregó el período ' . $periodo;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(150); //admon. contadores
            $obj_miBitacora->set_Id_Tipo_Evento(5);//alta
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
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
            $conn->rollBack();
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();   
        }
    }

   function Agregar_Periodo_Ceremonia($periodo, $id_administrador, $id_division){
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
                
        try{    
            
            $cnn = new Connection();
            $conn = $cnn->getConexion();
                           
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }        
            
            $conn->beginTransaction();
            
            $obj_d = new d_administrador_admon_Contadores();
            $existen = $obj_d->Existe_Periodo('inscripcion_ceremonia_contador', $periodo);
            if ($existen > '0'){
                $mensaje_Transacciones .= "Este período Ya Existe.<br>";
                throw new Exception($mensaje_Transacciones);                    
            }

            $tsql=" INSERT INTO inscripcion_ceremonia_contador(
                anio, semestre, consecutivo, id_division)
                VALUES (?,?,?,?);";

            for($j=1; $j<=2; $j++){
                $stmt = $conn->prepare($tsql);
                if ($stmt){
                    $result=$stmt->execute(array($periodo, $j, 0, $id_division));
                    if ($result){
                            if($stmt->rowCount() > 0){
                                $mensaje_Transacciones .= "Año " . $periodo . " Semestre " . $j . " Agregado. OK.<br/>";
                            }
                            else{
                                $error = $stmt->errorInfo();
                                $mensaje_Transacciones = "Ocurrió un error.";
                                throw new Exception($mensaje_Transacciones);                                                              
                            }
                    }
                    else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error.";
                        throw new Exception($mensaje_Transacciones);        
                    }                    

                }
                else{
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error.";
                        throw new Exception($mensaje_Transacciones);                                
                } 
            }
            $conn->commit();
            
            if ($periodo != 0) {
                $mensaje_Transacciones = "Contador de propuestas " . $periodo . " generado correctamente.";
            }
            
            //AGREGAMOS EL MOVIMIENTO A LA BITACORA
            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = 'Para las Ceremonias se Agregó el período ' . $periodo;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(150); //admon. contadores
            $obj_miBitacora->set_Id_Tipo_Evento(5);//alta
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
                            
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora ;
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
    
}
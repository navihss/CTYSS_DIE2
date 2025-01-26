<?php
use App\Database\Connection;
/**
 * Definición de la Capa de Datos para la Clase Alumno
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Mayo 2016
 */

header('Content-Type: text/html; charset=UTF-8');

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Alumno.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Usuario.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario.php');
require_once __DIR__ . '/../app/Database/Connection.php';
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/AlumnoDatosGenerales.php');

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');


class d_Alumno {    
    // Definición de Metodos
    
    function Obtener_Alumno($id_usr){
        $mensaje_Transacciones = '';
        $conn = '';        
        $obj_Usuario = new Usuario();
        $jsondata = array();

        try{                  
            $cnn = new Connection();
            $conn = $cnn->getConexion();
    
            if( $conn === false )
            {
                $conn=null; 
                throw new Exception($cnn->getError());     
            } 
            
            $tsql = "SELECT a.id_usuario, a.email_usuario, a.fecha_alta_usuario, a.nombre_usuario, 
                    a.apellido_paterno_usuario, a.apellido_materno_usuario, 
                    a.id_tipo_usuario, a.id_genero,
                    b.calle_numero_alumno, b.colonia_alumno, b.delegacion_municipio_alumno,
                    b.codigo_postal_alumno, b.telefono_fijo_alumno, b.telefono_celular_alumno,
                    b.fecha_nacimiento_alumno, b.anio_ingreso_fi_alumno, b.semestre_ingreso_fi_alumno,
                    b.id_estado_republica, a.email_usuario
                    FROM usuarios a
                    INNER JOIN alumnos b ON b.id_alumno = a.id_usuario
                    WHERE a.id_usuario = ?;";
       
            /* Valor de los parámetros. */
            $params = array($id_usr);
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
                        $jsondata['success'] = false;
                        $jsondata['data']= array('message'=>'No se encontró información de este Alumno.');
                        $stmt=null;
                        $conn=null;                        
                        echo json_encode($jsondata);
                        exit();                                                        
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
    }

    function Obtener_Alumno_DatosGenerales($id_inscripcion, $id_propuesta){
        $mensaje_Transacciones = '';
        $conn = '';        
        $obj_AlumnoDatosGenerales = new AlumnoDatosGenerales();
        $jsondata = array();

        try{                  
            $cnn = new Connection();
            $conn = $cnn->getConexion();
    
            if( $conn === false )
            {
                $conn=null; 
                throw new Exception($cnn->getError());     
            } 
            
            if ($id_inscripcion <> '')
            {
            $tsql = "select a.id_inscripcion, a.id_alumno, a.id_carrera, a.id_estatus, a.id_propuesta,
                        b.email_usuario, b.nombre_usuario, b.apellido_paterno_usuario, b.apellido_materno_usuario,
                        c.telefono_fijo_alumno, c.telefono_celular_alumno,
                        d.descripcion_carrera,
                        e.descripcion_estatus
                    from inscripcion_propuesta a
                        inner join usuarios b on a.id_alumno = b.id_usuario
                        inner join alumnos c on a.id_alumno = c.id_alumno
                        inner join carreras d on a.id_carrera = d.id_carrera 
                        inner join estatus e on a.id_estatus = e.id_estatus
                    where a.id_inscripcion = ?;";
                /* Valor de los parámetros. */
                $params = array($id_inscripcion);
            }
            else
            {
            $tsql = "select a.id_inscripcion, a.id_alumno, a.id_carrera, a.id_estatus, a.id_propuesta,
                        b.email_usuario, b.nombre_usuario, b.apellido_paterno_usuario, b.apellido_materno_usuario,
                        c.telefono_fijo_alumno, c.telefono_celular_alumno,
                        d.descripcion_carrera,
                        e.descripcion_estatus
                    from inscripcion_propuesta a
                        inner join usuarios b on a.id_alumno = b.id_usuario
                        inner join alumnos c on a.id_alumno = c.id_alumno
                        inner join carreras d on a.id_carrera = d.id_carrera 
                        inner join estatus e on a.id_estatus = e.id_estatus
                    where a.id_propuesta = ? and a.id_estatus = ?;";
                /* Valor de los parámetros. */
                $params = array($id_propuesta, 3);            
            }
            
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
                        $jsondata['success'] = false;
                        $jsondata['data']= array('message'=>'No se encontró información de los Datos Generales del Alumno.');
                        $stmt=null;
                        $conn=null;                        
                        echo json_encode($jsondata);
                        exit();                                                        
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
    }
    
    function Agregar($objAlumno, $id_division, $id_administrador){
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $id_genera = '';
        $id_destinatario = '';
        $id_tema_bitacora = 5; //Nueva cuenta que hace el Alumno

        try{                   
            $cnn = new Connection();
            $conn = $cnn->getConexion();
                
            $obj_Alumno = new Alumno();
            $obj_Alumno = $objAlumno;

            if ($id_administrador == ''){
                $id_genera = $obj_Alumno->get_Id_Alumno();
                $id_destinatario = '';
            }
            else{
                $id_genera = $id_administrador;
                $id_destinatario = $obj_Alumno->get_Id_Alumno();
                $id_tema_bitacora = 135; //Admon. Nueva Cuenta de Usuario
            }
            
            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
            
            /* Iniciar la transacción. */
//            try {
                $conn->beginTransaction();
                
                /* Agregamos al Usuario    
                /* Query parametrizado. */
                $obj_Usuario_cd = new d_Usuario();
                $obj_Usuario = new Usuario();
                $obj_Usuario = $objAlumno;
                $obj_Usuario->set_Id_Tipo_Baja(1);
                $obj_Usuario->set_Id_Division($id_division);

//                $stmt1 = false;
                $errorEnUsuarios ='';   
                $errorEnUsuarios = $obj_Usuario_cd->Agregar($obj_Usuario, $conn);
                    
                if( $errorEnUsuarios =='' ) {                
                     $mensaje_Transacciones = ("");                
                } else {
                    $mensaje_Transacciones = ("Ocurrió un error");
                    throw new Exception($mensaje_Transacciones);        
                }        
                /* Agregamos el Alumno  
                /* Query parametrizado. */
                
                $tsql2=" INSERT INTO alumnos
                        (id_alumno,
                        calle_numero_alumno,
                        colonia_alumno,
                        delegacion_municipio_alumno,
                        codigo_postal_alumno,
                        telefono_fijo_alumno,
                        telefono_celular_alumno,
                        fecha_nacimiento_alumno,
                        anio_ingreso_fi_alumno,
                        semestre_ingreso_fi_alumno,
                        id_usuario,
                        id_estado_republica,
                        id_division)
                        VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
                    ";
                /* Valor de los parámetros. */
                $params2 = array($obj_Alumno->get_Id_Alumno(),
                            $obj_Alumno->get_Calle_Numero(),
                            $obj_Alumno->get_Colonia(),
                            $obj_Alumno->get_Delegacion_Municipio(),
                            $obj_Alumno->get_Codigo_Postal(),
                            $obj_Alumno->get_Telefono_Fijo(),
                            $obj_Alumno->get_Celular(),
                            $obj_Alumno->get_Fecha_Nacimiento(),
                            $obj_Alumno->get_Anio_Ingreso_FI(),
                            $obj_Alumno->get_Semestre_Ingreso_FI(),
                            $obj_Alumno->get_IdUsuario(),
                            $obj_Alumno->get_Id_Estado(),
                            $id_division);

                /* Preparamos la sentencia a ejecutar */
                $stmt = $conn->prepare($tsql2);
                
                /*Ejecutamos el Query*/
                $errorEnAlumnos ='';   
                $result = 0;
                $result = $stmt->execute($params2);   
                
             
                
                if( $result > 0 ) {                
                     $mensaje_Transacciones .= ("");  
                                         
                } else {
                     $mensaje_Transacciones .= ("Ocurrió un error.");
                    throw new Exception($mensaje_Transacciones);             
                }                  

            
                /* Agregamos la carrera del Alumno  
                /* Query parametrizado. */
                
                $tsql2=" INSERT INTO alumno_carrera
                        (id_alumno,
                        id_carrera,
                        id_estatus,
                         id_division)
                        VALUES
                        (?, ?, ?, ?);
                    ";
                /* Valor de los parámetros. */
                $params2 = array($obj_Alumno->get_Id_Alumno(),
                            $obj_Alumno->get_Id_Carrera(),
                            5,
                            $id_division);

                /* Preparamos la sentencia a ejecutar */
                $stmt = $conn->prepare($tsql2);
                
                /*Ejecutamos el Query*/
                $errorEnAlumnos ='';   
                $result = 0;
                $result = $stmt->execute($params2);   
                
                if( $result > 0 ) { 
                    if($id_administrador == ''){
                        $mensaje_Transacciones .= ("Su cuenta se ha creado satisfactoriamente.");  
                    }else{
                        $mensaje_Transacciones .= ("Cuenta de alumno creada satisfactoriamente.");  
                    }          
                     
                     if($id_administrador == ''){
                       session_start(); 
                       $_SESSION["id_usuario"] = $obj_Alumno->get_Id_Alumno();
                       $_SESSION["id_tipo_usuario"] = $obj_Alumno->get_Id_Tipo_Usuario();
                       $_SESSION["descripcion_tipo_usuario"] = 'ALUMNO';
                       $_SESSION["nombre_usuario"] = $obj_Alumno->get_Nombre() .' '.$obj_Alumno->get_Apellido_Paterno().
                               ' '.$obj_Alumno->get_Apellido_Materno();
                     }
                                         
                } else {
                     $mensaje_Transacciones .= ("Ocurrió un error.");
                    throw new Exception($mensaje_Transacciones);             
                }  
                
                $conn->commit();
                
                $obj_Bitacora = new d_Usuario_Bitacora();
                $obj_miBitacora = new Bitacora();
                
                $descripcionEvento = 'Registrado en el Sistema'; 
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_bitacora);
                $obj_miBitacora->set_Id_Tipo_Evento(5);
                $obj_miBitacora->set_Id_Usuario_Genera($id_genera);
                $obj_miBitacora->set_Id_Usuario_Destinatario($id_destinatario);
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($id_division);

                $res_bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
                $mensaje_Transacciones .= $res_bitacora;
                
                $jsondata['success'] = true;
                $jsondata['data']['message'] = $mensaje_Transacciones;
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

    function Actualizar($objAlumno){
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();

        try{                         
            $cnn = new Connection();
            $conn = $cnn->getConexion();
                
            $obj_Alumno = new Alumno();
            $obj_Alumno = $objAlumno;

            if( $conn === false )
            {
                throw new Exception($cnn->getError());     
            }            
            
            /* Iniciar la transacci�n. */
            $conn->beginTransaction();
                
            /* Agregamos al Usuario    
            /* Query parametrizado. */
            $obj_Usuario_cd = new d_Usuario();
            $obj_Usuario = new Usuario();
            $obj_Usuario = $objAlumno;
            $obj_Usuario->set_Id_Usuario($obj_Alumno->get_Id_Alumno());

//                $stmt1 = false;
            $errorEnUsuarios ='';   
            $errorEnUsuarios = $obj_Usuario_cd->Actualizar($obj_Usuario, $conn);

            if( $errorEnUsuarios =='' ) {                
                 $mensaje_Transacciones = ("Transacción con éxito en Usuarios.<br/>");                
            } else {
                $mensaje_Transacciones = ("Transacción sin éxito en Usuarios.<br/>" . $errorEnUsuarios);
                throw new Exception($mensaje_Transacciones);        
            }        
            /* Agregamos el Alumno  
            /* Query parametrizado. */

            $tsql2="UPDATE alumnos SET
                    calle_numero_alumno = ?,
                    colonia_alumno = ?,
                    delegacion_municipio_alumno = ?,
                    codigo_postal_alumno = ?,
                    telefono_fijo_alumno = ?,
                    telefono_celular_alumno = ?,
                    anio_ingreso_fi_alumno = ?,
                    semestre_ingreso_fi_alumno = ?,
                    id_estado_republica = ?,
                    fecha_nacimiento_alumno = ?
                    WHERE id_alumno = ?;";
            /* Valor de los parámetros. */
            $params2 = array($obj_Alumno->get_Calle_Numero(),
                        $obj_Alumno->get_Colonia(),
                        $obj_Alumno->get_Delegacion_Municipio(),
                        $obj_Alumno->get_Codigo_Postal(),
                        $obj_Alumno->get_Telefono_Fijo(),
                        $obj_Alumno->get_Celular(),
                        $obj_Alumno->get_Anio_Ingreso_FI(),
                        $obj_Alumno->get_Semestre_Ingreso_FI(),
                        $obj_Alumno->get_Id_Estado(),
                        $obj_Alumno->get_Fecha_Nacimiento(),
                        $obj_Alumno->get_Id_Usuario());

            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql2);

            /*Ejecutamos el Query*/
            $errorEnAlumnos ='';   
            $result = 0;
            $result = $stmt->execute($params2);   

            if( $result > 0 ) {                
                 $mensaje_Transacciones .= ("Transacción con éxito en Alumnos.<br/>");                
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= ("Transacción sin éxito en Alumnos.<br/>"  . $error[2]);
                throw new Exception($mensaje_Transacciones);             
            }                  

            $conn->commit();

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = '';
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(10);
            $obj_miBitacora->set_Id_Tipo_Evento(15);
            $obj_miBitacora->set_Id_Usuario_Genera($obj_Alumno->get_IdUsuario());
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($obj_Alumno->get_Id_Division());

            $obj_Bitacora->Agregar($obj_miBitacora);                       
            
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones;
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
} // Fin Definición de Metodo Actualizar

} 
?>






         

<?php
/**
 * Interfaz de la Capa Negocio para la Clase Profesor.
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */
session_start();
if(!isset($_SESSION["id_usuario"]) and 
        !isset($_SESSION["id_tipo_usuario"]) and
        !isset($_SESSION["descripcion_tipo_usuario"]) and
        !isset($_SESSION["nombre_usuario"])){
    header('Location: ../index.php');
}
if(!isset($_POST['Tipo_Movimiento']) and 
        !isset($_POST['Id_Tipo_Usuario'])){
    header('Location: ../index.php');
}
$id_division=0;
if(isset($_SESSION["id_division"])){
    $id_division=$_SESSION["id_division"];
}

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_profesor_Mi_Perfil.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Profesor.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/zonaHoraria.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];
$tipo_Usuario = $_POST['Id_Tipo_Usuario'];

//$id_Usuario = 'x';
//$jsondata = array();
//$jsondata['success'] = true;
//$jsondata['data']['message'] = 'dentro de la capa de negocio ' . date_default_timezone_get() . ', ' . $obj_Alumno->get_Fecha_Alta();
//echo json_encode($jsondata);
//exit();
                
switch ($tipo_Usuario){
    case 5: //Usuario-Alumno
        switch ($tipo_Movimiento){
            case 'OBTENER_DATOS':
                $id_Usuario = $_POST['Id_Usuario'];
                $obj_d_Alumno = new d_Alumno();
                echo $obj_d_Alumno->Obtener_Alumno($id_Usuario);
                break;
            case 'AGREGAR':                                
                $obj_Alumno = new Alumno();
                $obj_d_Alumno = new d_Alumno();
                $obj_Alumno->set_Id_Alumno($_POST['clave']);
                $obj_Alumno->set_Calle_Numero("");
                $obj_Alumno->set_Colonia("");
                $obj_Alumno->set_Delegacion_Municipio("");
                $obj_Alumno->set_Codigo_Postal("");
                $obj_Alumno->set_Telefono_Fijo("");
                $obj_Alumno->set_Celular("");
                $obj_Alumno->set_Fecha_Nacimiento($_POST['fechaNacimiento']);
                $obj_Alumno->set_Anio_Ingreso_FI(0);
                $obj_Alumno->set_Semestre_Ingreso_FI(0);
                $obj_Alumno->set_Id_Estado(1);
                $obj_Alumno->set_IdUsuario($_POST['clave']);
                $obj_Alumno->set_Id_Usuario($_POST['clave']);
                $obj_Alumno->set_Contrasena(($_POST['contrasena']));
                $obj_Alumno->set_Nombre($_POST['nombre']);
                $obj_Alumno->set_Apellido_Paterno(($_POST['apellidoPaterno']));
                $obj_Alumno->set_Apellido_Materno(($_POST['apellidoMaterno']));
                $obj_Alumno->set_Id_Tipo_Usuario(5);
//                $Fec = new DateTime(date('Y-n-j'));
//                $fecha = $Fec->format("d-m-Y");
//                $obj_Alumno->set_Fecha_Alta($fecha);
                
//                $obj_Alumno->set_Fecha_Alta(date('Y-n-j'));
                $obj_Alumno->set_Fecha_Alta(date('d-m-Y H:i:s'));                

                               
                $obj_Alumno->set_Correo_Electronico($_POST['correo']);
                $obj_Alumno->set_Id_Genero($_POST['genero']);
                $obj_Alumno->set_Activo(1);

//                $jsondata = array();
//                $jsondata['success'] = true;
//                $jsondata['data']['message'] = 'dentro de la capa de negocio tipo_mov, tipo_usuario ' . $tipo_Movimiento . ', ' . $tipo_Usuario;
//                echo json_encode($jsondata);
//                exit();
                
//                $jsondata["data"]["users"] = array();
                echo $obj_d_Alumno->Agregar($obj_Alumno, $id_division);
                
                break;
 case 'ACTUALIZAR':
                $id_Usuario = $_POST['Id_Usuario'];
                $obj_Alumno = new Alumno();
                $obj_d_Alumno = new d_Alumno();                
                $obj_Alumno->set_Id_Alumno($id_Usuario );                 
                $obj_Alumno->set_Id_Usuario($id_Usuario);
                $obj_Alumno->set_Calle_Numero($_POST['calle_Numero']);
                $obj_Alumno->set_Colonia($_POST['colonia']);
                $obj_Alumno->set_Delegacion_Municipio($_POST['delegacion_Municipio']);
                $obj_Alumno->set_Codigo_Postal($_POST['codigo_Postal']);
                $obj_Alumno->set_Telefono_Fijo($_POST['telefono_Fijo']);
                $obj_Alumno->set_Celular($_POST['celular']);
                $obj_Alumno->set_Fecha_Nacimiento($_POST['fecha_Nacimiento']);
                $obj_Alumno->set_Anio_Ingreso_FI($_POST['anio_Ingreso_FI']);
                $obj_Alumno->set_Semestre_Ingreso_FI($_POST['semestre_Ingreso_FI']);
                $obj_Alumno->set_Nombre($_POST['nombre']);
                $obj_Alumno->set_Apellido_Paterno(($_POST['apellido_Paterno']));
                $obj_Alumno->set_Apellido_Materno(($_POST['apellido_Materno']));                              
                $obj_Alumno->set_Correo_Electronico($_POST['correo_Electronico']);
                $obj_Alumno->set_Id_Genero($_POST['genero']);
                $obj_Alumno->set_Id_Estado($_POST['estado']);
                $obj_Alumno->set_Id_Division($id_division);

                echo $obj_d_Alumno->Actualizar($obj_Alumno);
                break;
         }
         break;
}

?>

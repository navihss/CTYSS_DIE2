<?php
/**
 * Interfaz de la Capa Negocio para la Clase Usuario.
 * @author Rogelio Reyes Mendoza
 * Mayo 2016
 */

if(!isset($_POST["Tipo_Movimiento"]) and 
        !isset($_POST["Id_Tipo_Usuario"]) and
        !isset($_POST["Id_Usuario"])){
    header('Location: ../index.php');
}
$id_division=0;
if(isset($_SESSION["id_division"])){
    $id_division=$_SESSION["id_division"];
}

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Alumno.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
 
$tipo_Movimiento = $_POST['Tipo_Movimiento'];
$tipo_Usuario = $_POST['Id_Tipo_Usuario'];
$id_Usuario = $_POST['Id_Usuario'];

switch ($tipo_Usuario){
    case 999: //Validar la Clave con la que se firma
        switch ($tipo_Movimiento){
            case 'VALIDARNoCUENTA':
                $obj_d_Usuario = new d_Usuario();
                echo $obj_d_Usuario->Iniciar_Sesion($_POST['usuario'], $_POST['contrasena']);                
                break;
        }
        
    case 5: //Usuario-Alumno
        switch ($tipo_Movimiento){
            case 'OBTENER_DATOS':
                $obj_d_Alumno = new d_Alumno();
                echo $obj_d_Alumno->Obtener_Alumno($id_Usuario);
                break;
            case 'OBTENER_DATOSGENERALES':
                $obj_d_Alumno = new d_Alumno();
                echo $obj_d_Alumno->Obtener_Alumno_DatosGenerales($_POST['id_inscripcion'], $_POST['id_propuesta']);
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
                $obj_Alumno->set_Apellido_Paterno(strtoupper($_POST['apellidoPaterno']));
                $obj_Alumno->set_Apellido_Materno(strtoupper($_POST['apellidoMaterno']));
                $obj_Alumno->set_Id_Tipo_Usuario(5);
                $obj_Alumno->set_Id_Tipo_Baja(5);
                $obj_Alumno->set_Id_Carrera($_POST['carrera']);
//                $Fec = new DateTime(date('Y-n-j'));
//                $fecha = $Fec->format("d-m-Y");
//                $obj_Alumno->set_Fecha_Alta($fecha);
                
//                $obj_Alumno->set_Fecha_Alta(date('Y-n-j'));
                $obj_Alumno->set_Fecha_Alta(date('d-m-Y H:i:s'));                

                               
                $obj_Alumno->set_Correo_Electronico($_POST['correo']);
                $obj_Alumno->set_Id_Genero($_POST['genero']);
                $obj_Alumno->set_Activo(1);

                $id_administrador = '';
                echo $obj_d_Alumno->Agregar($obj_Alumno, $id_division, $id_administrador);
                
                break;
            
            case 'ACTUALIZAR':      
            
                $obj_Alumno = new Alumno();
                $obj_d_Alumno = new d_Alumno();                
                $obj_Alumno->set_Id_Alumno($id_Usuario );                 
                $obj_Alumno->set_Id_Usuario($id_Usuario);
                $obj_Alumno->set_IdUsuario($id_Usuario);
                $obj_Alumno->set_Calle_Numero(strtoupper($_POST['calle_Numero']));
                $obj_Alumno->set_Colonia(strtoupper($_POST['colonia']));
                $obj_Alumno->set_Delegacion_Municipio(strtoupper($_POST['delegacion_Municipio']));
                $obj_Alumno->set_Codigo_Postal($_POST['codigo_Postal']);
                $obj_Alumno->set_Telefono_Fijo($_POST['telefono_Fijo']);
                $obj_Alumno->set_Celular($_POST['celular']);
                $obj_Alumno->set_Fecha_Nacimiento($_POST['fecha_Nacimiento']);
                $obj_Alumno->set_Anio_Ingreso_FI($_POST['anio_Ingreso_FI']);
                $obj_Alumno->set_Semestre_Ingreso_FI($_POST['semestre_Ingreso_FI']);
                $obj_Alumno->set_Nombre(strtoupper($_POST['nombre']));
                $obj_Alumno->set_Apellido_Paterno(strtoupper($_POST['apellido_Paterno']));
                $obj_Alumno->set_Apellido_Materno(strtoupper($_POST['apellido_Materno']));                              
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
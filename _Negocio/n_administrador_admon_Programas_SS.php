<?php
/**
 * Interfaz de la Capa Negocio para los Programas de Servicio Social
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */
session_start();
if(!isset($_SESSION["id_usuario"]) and 
        !isset($_SESSION["id_tipo_usuario"]) and
        !isset($_SESSION["descripcion_tipo_usuario"]) and
        !isset($_SESSION["nombre_usuario"])){
    header('Location: ../index.php');
}
if(!isset($_POST['Tipo_Movimiento'])){
    header('Location: ../index.php');
}
$id_division=0;
if(isset($_SESSION["id_division"])){
    $id_division=$_SESSION["id_division"];
}

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_administrador_admon_Programas_SS.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Programa_SS.php');
          
$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_d_administrador_admon_Programas_SS = new d_administrador_admon_Programas_SS();

switch ($tipo_Movimiento){
    case "OBTENER_PROGRAMAS": 

        $id_programa = $_POST['id_programa'];
        $desc_programa = $_POST['desc_programa'];

        echo  $obj_d_administrador_admon_Programas_SS->Obtener_Programas($id_programa, $desc_programa);
        break;

    case "OBTENER_PROGRAMA": 

        $id_programa = $_POST['id_programa'];

        echo  $obj_d_administrador_admon_Programas_SS->Obtener_Programa($id_programa);
        break;
    
    case "AGREGAR": 
        $obj_programa = new Programa_SS();
        
        $obj_programa->set_CP($_POST['codigo_Postal']);
        $obj_programa->set_Calle($_POST['calle_Numero']);
        $obj_programa->set_Cargo($_POST['cargo_Responsable']);
        $obj_programa->set_Colonia($_POST['colonia']);
        $obj_programa->set_Delegacion($_POST['delegacion_Municipio']);
        $obj_programa->set_Descripcion($_POST['descripcion_programa_SS']);
        $obj_programa->set_Email($_POST['correo_Electronico']);
        $obj_programa->set_Id_Dependencia($_POST['dependencia']);
        $obj_programa->set_Id_Estado($_POST['estado']);
        $obj_programa->set_Id_Estatus($_POST['estatus']);
        $obj_programa->set_Id_Programa($_POST['id_programa_SS']);
        $obj_programa->set_Num_Exterior($_POST['num_exterior']);
        $obj_programa->set_Num_Interior($_POST['num_interior']);
        $obj_programa->set_Oficina($_POST['oficina_seccion']);
        $obj_programa->set_Responsable($_POST['responsable']);
        $obj_programa->set_Subdireccion($_POST['subdireccion']);
        $obj_programa->set_Telefono_Dependencia($_POST['telefono_Dependencia']);
        $obj_programa->set_Telefono_SS($_POST['telefono_Servicio_Social']);
        $obj_programa->set_Tipo_Programa($_POST['tipo_programa']);
        
        $id_administrador = $_POST['Id_Usuario'];
        $carreras = $_POST['id_carreras'];

        echo  $obj_d_administrador_admon_Programas_SS->Agregar($obj_programa, $id_administrador, $carreras, $id_division);
        break;

    case "ACTUALIZAR": 
        $obj_programa = new Programa_SS();
        
        $obj_programa->set_CP($_POST['codigo_Postal']);
        $obj_programa->set_Calle($_POST['calle_Numero']);
        $obj_programa->set_Cargo($_POST['cargo_Responsable']);
        $obj_programa->set_Colonia($_POST['colonia']);
        $obj_programa->set_Delegacion($_POST['delegacion_Municipio']);
        $obj_programa->set_Descripcion($_POST['descripcion_programa_SS']);
        $obj_programa->set_Email($_POST['correo_Electronico']);
        $obj_programa->set_Id_Dependencia($_POST['dependencia']);
        $obj_programa->set_Id_Estado($_POST['estado']);
        $obj_programa->set_Id_Estatus($_POST['estatus']);
        $obj_programa->set_Id_Programa($_POST['id_programa_seleccionado']);
        $obj_programa->set_Num_Exterior($_POST['num_exterior']);
        $obj_programa->set_Num_Interior($_POST['num_interior']);
        $obj_programa->set_Oficina($_POST['oficina_seccion']);
        $obj_programa->set_Responsable($_POST['responsable']);
        $obj_programa->set_Subdireccion($_POST['subdireccion']);
        $obj_programa->set_Telefono_Dependencia($_POST['telefono_Dependencia']);
        $obj_programa->set_Telefono_SS($_POST['telefono_Servicio_Social']);
        $obj_programa->set_Tipo_Programa($_POST['tipo_programa']);
        $obj_programa->set_Carreras($_POST['id_carreras']);
        
        $id_administrador = $_POST['Id_Usuario'];         
            
        echo  $obj_d_administrador_admon_Programas_SS->Actualizar($obj_programa, $id_administrador, $id_division);
        break;
    

}   

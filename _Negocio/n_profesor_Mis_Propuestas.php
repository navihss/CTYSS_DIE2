<?php
/**
 * Interfaz de la Capa Negocio para la Clase Usuario.
 * @author Rogelio Reyes Mendoza
 * Junio 2016
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

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_profesor_Mis_Propuestas.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Propuesta_Profesor.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_d_profesor_MP = new d_profesor_Mis_Propuestas();

switch ($tipo_Movimiento){
    case "OBTENER":
        $id_profesor = $_POST['id_profesor'];
        echo $obj_d_profesor_MP->Obtener($id_profesor);
        break;
    case "SELECCIONAR":
        $id_propuesta = $_POST['id_propuesta'];        
        echo $obj_d_profesor_MP->Seleccionar($id_propuesta);
        break;
    case "OBTENER_VOBO":
        $id_propuesta = $_POST['id_propuesta'];        
        $id_documento = $_POST['id_documento'];        
        $id_version = $_POST['id_version'];        
        echo $obj_d_profesor_MP->Obtener_VoBo($id_propuesta, $id_documento, $id_version);
        break;    
  case "OBTENER_PROPUESTAS_AUT_CARRERA":
        $id_carrera = $_POST['id_carrera'];   

//$jsondata = array();
//$jsondata['success'] = false;
//$jsondata['data']['message'] = 'dentro de la capa de neg ' . $id_carrera;
//echo json_encode($jsondata);
//exit();      
        echo $obj_d_profesor_MP->Obtener_Propuestas_Autorizadas($id_carrera);
        break;        
    case "AGREGAR":
        $obj_Prop_Profesor = new Propuesta_Profesor;
        $obj_Prop_Profesor->set_Id_Propuesta($_POST['id_propuesta']);
        $obj_Prop_Profesor->set_Titulo($_POST['titulo_prop']);
        $obj_Prop_Profesor->set_Id_Profesor($_POST['Id_Usuario']);
        $obj_Prop_Profesor->set_Id_Division($_POST['id_division']);
        $obj_Prop_Profesor->set_Id_Tipo_Propuesta($_POST['tipo_propuesta']);
        $obj_Prop_Profesor->set_Id_Estatus(2); //2. Por autorizar admin.
        $obj_Prop_Profesor->set_Organismos_Colaboradores($_POST['organismos_colaboradores']);
        $obj_Prop_Profesor->set_Horarios($_POST['Mis_Horarios']);
        $obj_Prop_Profesor->set_Requerimiento_Alumnos($_POST['Carrera_Alumnos']);
        $obj_Prop_Profesor->set_Fecha_Registrada(date('d-m-Y H:i:s'));
        
        $aceptar_inscripciones = 0;
        if(isset($_POST['aceptar_inscripciones'])){
                $aceptar_inscripciones = 1;
        }
        $obj_Prop_Profesor->set_Aceptar_Inscripciones($aceptar_inscripciones);
        
        echo $obj_d_profesor_MP->Agregar($obj_Prop_Profesor);
        break;
    case "ACTUALIZAR":
        $obj_Prop_Profesor = new Propuesta_Profesor;
        $obj_Prop_Profesor->set_Id_Propuesta($_POST['id_propuesta']);
        $obj_Prop_Profesor->set_Titulo($_POST['titulo_prop']);
        $obj_Prop_Profesor->set_Id_Profesor($_POST['Id_Usuario']);
        $obj_Prop_Profesor->set_Id_Division($_POST['id_division']);
        $obj_Prop_Profesor->set_Id_Tipo_Propuesta($_POST['tipo_propuesta']);
        $obj_Prop_Profesor->set_Id_Estatus($_POST['Id_Estatus_Propuesta']);
        $obj_Prop_Profesor->set_Organismos_Colaboradores($_POST['organismos_colaboradores']);
        $obj_Prop_Profesor->set_Requerimiento_Alumnos($_POST['Carrera_Alumnos']);
        $obj_Prop_Profesor->set_Horarios($_POST['Mis_Horarios']);

        $aceptar_inscripciones = 0;
        if(isset($_POST['aceptar_inscripciones'])){
                $aceptar_inscripciones = 1;
        }
        $obj_Prop_Profesor->set_Aceptar_Inscripciones($aceptar_inscripciones);
        
        echo $obj_d_profesor_MP->Actualizar($obj_Prop_Profesor);
        break;

    case "DOCUMENTOS_ENVIADOS":
        $id_propuesta= $_POST['id_propuesta'];
        echo $obj_d_profesor_MP->Obtener_Documentos_Enviados($id_propuesta);
        break;
        
    case "BORRAR_PROPUESTA":
        $id_propuesta = $_POST['id_propuesta'];
        $id_profesor = $_POST['id_profesor'];
        $titulo_propuesta = $_POST['titulo_propuesta'];
        $descripcion_tipo_propuesta = $_POST['descripcion_tipo_propuesta']; 
        $nota = $_POST['nota'];
        $id_division = $_POST['id_division'];        
        
        echo $obj_d_profesor_MP->Borrar_Propuesta($id_propuesta, $id_profesor, $id_division, $titulo_propuesta, $descripcion_tipo_propuesta, $nota);
        break;
        
}


?>

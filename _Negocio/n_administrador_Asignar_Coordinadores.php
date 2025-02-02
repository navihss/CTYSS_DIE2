<?php
/**
 * Interfaz de la Capa Negocio para Asiganr Coordinadores a la Propuesta
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
if(!isset($_POST['Tipo_Movimiento'])){
    header('Location: ../index.php');
}
$id_division=0;
if(isset($_SESSION["id_division"])){
    $id_division=$_SESSION["id_division"];
}

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_administrador_Asignar_Coordinadores.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/zonaHoraria.php');
          
$tipo_Movimiento = $_POST['Tipo_Movimiento'];

switch ($tipo_Movimiento){
    case "TRAER_INDICE":
        $id_propuesta = $_POST['id_propuesta'];

        $obj_d_Admon_Asignar_C = new d_administrador_Asignar_Coordinadores();


        echo  $obj_d_Admon_Asignar_C->Traer_Indice($id_propuesta);

        break;
    case "TRAER_INDICE_COMPLETO":
        $id_propuesta = $_POST['id_propuesta'];

        $obj_d_Admon_Asignar_C = new d_administrador_Asignar_Coordinadores();


        echo  $obj_d_Admon_Asignar_C->Traer_Indice_Completo($id_propuesta);

        break;

    case "OBTENER": //Obtenemos las propuestas pendientes de asignar Coordinadores
        $id_estatus = $_POST['id_estatus'];
        $id_division = $_POST['id_division'];
        $obj_d_Admon_Asignar_C = new d_administrador_Asignar_Coordinadores();
        
        echo  $obj_d_Admon_Asignar_C->Obtener_Documentos_Por_Autorizar($id_estatus, $id_division);
        break;
    
    case "ACTUALIZAR_ESTATUS_DOC": //Actualizar el Estatus del Doc del profesor
        $id_propuesta_doc = $_POST['id_propuesta'];
        $id_documento_doc = $_POST['id_documento'];
        $id_version_doc = $_POST['id_version'];
        $id_estatus = $_POST['id_estatus'];
        $id_administrador = $_POST['id_administrador'];
        $nota = $_POST['nota'];
        $coordinaciones = $_POST['coordinaciones'];
        $departamentos = $_POST['departamentos'];

        $id_profesor = $_POST['id_profesor'];
        $titulo_propuesta = $_POST['titulo_propuesta'];
        $correo_profesor = $_POST['correo_profesor'];
        
        $desc_documento = $_POST['desc_corta_doc'];
        
        $obj_d_Admon_Aprobar_DOC = new d_administrador_Asignar_Coordinadores();
        
        echo  $obj_d_Admon_Aprobar_DOC->Actualizar_Aceptacion_Doc($id_propuesta_doc, $id_documento_doc, 
                $id_version_doc, $id_estatus, $id_administrador, $nota, $coordinaciones, $departamentos,
                $id_profesor, $titulo_propuesta, $correo_profesor, $desc_documento, $id_division);
        break;
        
        case "BITACORA_PROPUESTAS":
            $obj_d_Admon_Bitacora_Propuestas = new d_administrador_Asignar_Coordinadores();
            $id_propuesta= $_POST['id_propuesta'];
            echo $obj_d_Admon_Bitacora_Propuestas->Obtener_Bitacora_Propuestas($id_propuesta);
            break;

}   


?>
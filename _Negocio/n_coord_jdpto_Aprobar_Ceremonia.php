<?php
/**
 * Interfaz de la Capa Negocio para Aprobar Ceremonias Coordinación
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

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_coord_jdpto_Aprobar_Ceremonia.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/zonaHoraria.php');
          
$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_coord_jdpto_Aprobar_Ceremonia = new d_coord_jdpto_Aprobar_Ceremonia();

switch ($tipo_Movimiento){
    case "OBTENER_CEREMONIAS_PENDIENTES": //Obtenemos las ceremonias pendientes de aprobar
        $id_usuario = $_POST['id_usuario'];
        echo  $obj_coord_jdpto_Aprobar_Ceremonia->Obtener_Ceremonias_Por_Autorizar($id_usuario );
        break;

    case "OBTENER_DOCUMENTOS_PENDIENTES": //Obtenemos documentos de la ceremonia
        $id_ceremonia = $_POST['id_ceremonia'];
        $id_estatus = $_POST['id_estatus'];
        
        echo  $obj_coord_jdpto_Aprobar_Ceremonia->Obtener_Documentos_Por_Autorizar($id_ceremonia, $id_estatus);
        break;
    
    case "ACTUALIZAR_ESTATUS_DOC": //Actualizar el Estatus del documento
        $id_ceremonia =  $_POST['id_ceremonia'];
        $id_documento = $_POST['id_documento'];
        $version = $_POST['version'];
        $desc_documento = $_POST['desc_documento'];
        $nota = $_POST['nota'];
        $id_estatus = $_POST['id_estatus'];
        $id_coordinador =  $_POST['id_admin'];
        $mail_coordinador = $_POST['mail_admin'];
        $todos_revisados = $_POST['todos_revisados'];
        $desc_corta_nom_archivo = $_POST['desc_corta_nom_archivo'];
        $rechazados = $_POST['rechazados'];
        $datos_archivos = $_POST['datos_archivos'];
        $fecha_alta_ceremonia = $_POST['fecha_alta_ceremonia'];
                        
        echo  $obj_coord_jdpto_Aprobar_Ceremonia->Actualizar_Estatus_Doc_($id_ceremonia, 
                $id_documento, $version, $desc_documento, $nota, $id_estatus, $id_coordinador, 
                $mail_coordinador, $todos_revisados, $desc_corta_nom_archivo, 
                $rechazados, $fecha_alta_ceremonia , $datos_archivos, $id_division);
               
        break;        

}   

?>
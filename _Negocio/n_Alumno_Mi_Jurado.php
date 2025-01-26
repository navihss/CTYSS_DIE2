<?php
/**
 * Interfaz de la Capa Negocio para el Jurado
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */
session_start();
if(!isset($_SESSION["id_usuario"]) and 
        !isset($_SESSION["id_tipo_usuario"]) and
        !isset($_SESSION["descripcion_tipo_usuario"]) and
        !isset($_SESSION["nombre_usuario"]) and 
        !isset($_POST['Tipo_Movimiento'])){
    header('Location: ../index.php');
}
if(!isset($_POST['Tipo_Movimiento'])){
    header('Location: ../index.php');
}
$id_division=0;
if(isset($_SESSION["id_division"])){
    $id_division=$_SESSION["id_division"];
}

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Mi_Jurado.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');

    $tipo_Movimiento = $_POST['Tipo_Movimiento'];

    $obj_d_Alumno_Mi_Jurado = new d_Alumno_Mi_Jurado();
           
    switch ($tipo_Movimiento){
        case "OBTENER_MI_JURADO":
            $id_alumno= $_POST['id_usuario'];
            $id_carrera = $_POST['id_carrera'];
            echo $obj_d_Alumno_Mi_Jurado->Obtener_Mi_Jurado($id_alumno, $id_carrera);
            break;
        case "OBTENER_MIS_SINODALES":            
            $id_propuesta= $_POST['id_propuesta'];
            $id_version = $_POST['id_version'];
            echo $obj_d_Alumno_Mi_Jurado->Obtener_Sinodales($id_propuesta, $id_version);
            break;                
        case "ACTUALIZAR":                   
            $id_propuesta= $_POST['Id_Propuesta'];
            $id_version = $_POST['id_Version'];
            $id_sinodales = $_POST['id_sinodales'];
            $id_alumno = $_POST['Id_Usuario'];
            $titulo_propuesta = $_POST['titulo_propuesta'];
            echo $obj_d_Alumno_Mi_Jurado->Actualizar_Sinodales($id_propuesta, $id_version, $id_sinodales, $id_alumno, $titulo_propuesta, $id_division);
            break;                
        
    }        
?>
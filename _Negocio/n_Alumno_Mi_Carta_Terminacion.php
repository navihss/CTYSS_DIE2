<?php
/**
 * Interfaz de la Capa Negocio para las Cartas de Terminación.
 * @author Rogelio Reyes Mendoza
 * Julio 2016
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
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Mi_Carta_Terminacion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');

//    $jsondata = array();
//    $jsondata['success'] = false;
//    $jsondata['data']['message'] = 'dentro de la capa de datos ';
//    echo json_encode($jsondata);
//    exit();
//    
    $tipo_Movimiento = $_POST['Tipo_Movimiento'];

    $obj_d_Alumno_Mi_CT = new d_Alumno_Mi_Carta_Terminacion();

    switch ($tipo_Movimiento){
        case "OBTENER_SERVICIO_SOCIAL_HORAS_LABORADAS":
            $id_alumno= $_POST['Id_Usuario'];
            $id_carrera = $_POST['Id_Carrera'];
            echo $obj_d_Alumno_Mi_CT->Obtener_SS_Horas_Laboradas($id_alumno, $id_carrera);
            break;
        case "OBTENER_MIS_CARTAS_TERMINACION":
            $id_ss= $_POST['clave'];
            echo $obj_d_Alumno_Mi_CT->Obtener_Mis_Documentos($id_ss);
            break;            
    }        
?>
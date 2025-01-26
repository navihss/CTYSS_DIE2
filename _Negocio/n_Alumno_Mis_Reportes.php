<?php
/**
 * Interfaz de la Capa Negocio para los Reportes Bimestrales.
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

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Mis_Reportes.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');

    $tipo_Movimiento = $_POST['Tipo_Movimiento'];

    $obj_d_Alumno_Mis_Reportes = new d_Alumno_Mis_Reportes();

    switch ($tipo_Movimiento){
        case "OBTENER_CARRERAS_x_SS":
            $id_alumno= $_POST['id_alumno'];
            echo $obj_d_Alumno_Mis_Reportes->Obtener_Mi_SS_x_Carrera($id_alumno);
            break;
        case "OBTENER_MIS_REPORTES_BIMESTRALES":
            $id_ss= $_POST['id_ss'];
            echo $obj_d_Alumno_Mis_Reportes->Obtener_Mis_Reportes($id_ss);
            break;            
        case "OBTENER_DATOS_GENERALES_REPORTES_BIMESTRALES":
            $id_ss= $_POST['id_ss'];
            echo $obj_d_Alumno_Mis_Reportes->Obtener_Datos_Grales($id_ss);
            break;     
        case "PUEDE_ENVIAR_RPT":
            $jsondata = array();
            $jsondata['success'] = true;
            $hoy = strtotime(date("Y-m-d"));
            $fecha_termino = strtotime($_POST['fecha_termino_rpt']);
            $es_mayorHoy ="false";
            if ($hoy > $fecha_termino)
            {
                $es_mayorHoy ="true";
            }
//            $jsondata['data']['message'] =  ($hoy > $fecha_termino);
            $jsondata['data']['message'] =  $hoy > $fecha_termino;
            echo json_encode($jsondata);            
            break;
    }        
?>
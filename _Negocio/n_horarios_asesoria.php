<?php
/**
 * Interfaz de la Capa Negocio para los Días de Asesoría
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

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_horarios_asesoria.php');
         
$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_d_horarios = new d_horarios_asesoria();

switch ($tipo_Movimiento){
    case "OBTENER":

        echo $obj_d_horarios->Obtener();
        break;
}

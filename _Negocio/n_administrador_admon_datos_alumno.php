<?php
/**
 * Interfaz de la Capa Negocio para consultar datos de alumno.
 * @author Carlos Aguilar
 * Octubre 2024
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

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_administrador_admon_datos_alumno.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');

    $tipo_Movimiento = $_POST['Tipo_Movimiento'];

    $obj_d_Admin_DA = new d_administrador_admon_datos_alumno();

    switch ($tipo_Movimiento){
        case "OBTENER_DATOS_ALUMNO":
            $id_numero_cuenta= $_POST['id_numero_cuenta'];
          
            echo $obj_d_Admin_DA->Obtener_Datos_Alumno($id_numero_cuenta);
            break;
    }        
?>

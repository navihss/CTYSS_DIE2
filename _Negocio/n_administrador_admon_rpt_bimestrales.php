<?php
/**
 * Interfaz de la Capa Negocio para administrar los Reportes Bimestrales.
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

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_administrador_admon_rpt_bimestrales.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');

    $tipo_Movimiento = $_POST['Tipo_Movimiento'];

    $obj_d_Admin_RB = new d_administrador_admon_rpt_bimestrales();

    switch ($tipo_Movimiento){
        case "OBTENER_REPORTES_BIMESTRALES":
            $id_ss= $_POST['id_ss'];
          
            echo $obj_d_Admin_RB->Obtener_Reportes_Bimestrales($id_ss, $id_division);
            break;
        case "AGREGAR_REPORTE":
            $id_ss = $_POST['id_ss'];
            $numero_reporte_bi = $_POST['numero_reporte_bi'];
            $nota = $_POST['nota'];
            $fecha_prog_inicio = $_POST['fecha_prog_inicio'];
            $fecha_prog_fin = $_POST['fecha_prog_fin'];
            $horas_obligatorias = $_POST['horas_obligatorias'];

            $id_administrador = $_POST['id_administrador'];
            $correo_administrador = $_POST['correo_administrador'];
            $id_alumno = $_POST['id_alumno'];
            $correo_usr = $_POST['correo_usr'];

            
            echo $obj_d_Admin_RB->Agregar_Reporte($id_ss, $numero_reporte_bi, $fecha_prog_inicio, $fecha_prog_fin, $horas_obligatorias, $nota,
                    $id_administrador, $correo_administrador,$id_alumno,$correo_usr, $id_division);
            break;            
        
    }        
?>

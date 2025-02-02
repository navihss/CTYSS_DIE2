<?php
/**
 * Interfaz de la Capa Negocio para Autorizar los Reportes Bimestrales del Alumno.
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
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_administrador_Aprobar_Reporte_Bimestral.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/zonaHoraria.php');
          
$tipo_Movimiento = $_POST['Tipo_Movimiento'];

switch ($tipo_Movimiento){
    case "OBTENER_REPORTES_BIMESTRALES": //Mostrar los Reportes Bimestrales por autorizar
        $id_estatus = $_POST['id_estatus'];
 
        $obj_d_Admon_Aprobar_RB = new d_administrador_Aprobar_Reporte_Bimestral();
        
        echo  $obj_d_Admon_Aprobar_RB->Obtener_Reportes_x_Autorizar($id_estatus, $id_division);
        break;
    case "ACTUALIZAR_ESTATUS_REPORTE": //Actualizar el Estatus del Reporte Bimestral
        $id_ss = $_POST['id_ss'];
        $numero_reporte_bi = $_POST['numero_reporte_bi'];
        $id_version = $_POST['id_version'];
        $id_estatus = $_POST['id_estatus'];
        $id_administrador = $_POST['id_administrador'];
        $nota = $_POST['nota'];
        $fecha_prog_inicio = $_POST['fecha_prog_inicio'];
        $fecha_prog_fin = $_POST['fecha_prog_fin'];
        $horas_obligatorias = $_POST['horas_obligatorias'];

        $id_Usr_Destinatario = $_POST['id_Usr_Destinatario'];  
        $correo_usr = $_POST['correo_usr']; 
        $carrera_usr = $_POST['carrera_usr']; 
        $desc_documento = $_POST['desc_documento'];   
        $id_tema_bitacora = $_POST['id_tema_bitacora'];   
        
        $obj_d_Admon_Aprobar_RB = new d_administrador_Aprobar_Reporte_Bimestral();

        echo  $obj_d_Admon_Aprobar_RB->Actualizar_Aceptacion_Reporte_Bimestral($id_ss, $numero_reporte_bi, 
                $id_version, $id_estatus, $id_administrador, $nota, $fecha_prog_inicio, $fecha_prog_fin, $horas_obligatorias,
                $id_Usr_Destinatario, $correo_usr, $carrera_usr, $desc_documento, $id_tema_bitacora, $id_division);
        break;    
    
}


?>
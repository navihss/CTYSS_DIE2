<?php
/**
 * Interfaz de la Capa Negocio para Autorizar las Cartas de Terminación
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

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_administrador_Aprobar_Carta_Terminacion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Mi_Servicio.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
          
$tipo_Movimiento = $_POST['Tipo_Movimiento'];

switch ($tipo_Movimiento){
    case "OBTENER_CARTAS_TERMINACION": //Mostrar las Cartas de Termianción por autorizar
        $id_estatus_ss = $_POST['id_estatus'];
 
        $obj_d_Admon_Aprobar_CT = new d_administrador_Aprobar_Carta_Terminacion();
        
        echo  $obj_d_Admon_Aprobar_CT->Obtener_SS_Horas_Laboradas($id_estatus_ss, $id_division);
        break;
    
    case "ACTUALIZAR_ESTATUS_CARTA_TERMINACION": //Actualizar el Estatus de la Carta de Aceptación
        $id_ss = $_POST['id_ss'];
        $id_doc = $_POST['id_doc'];
        $id_version = $_POST['id_version'];
        $id_estatus = $_POST['id_estatus'];
        $id_administrador = $_POST['id_administrador'];
        $nota = $_POST['nota'];
        
        $id_Usr_Destinatario = $_POST['id_Usr_Destinatario'];
        $correo_usr = $_POST['correo_usr'];
        $carrera_usr = $_POST['carrera_usr'];
        $desc_documento = $_POST['desc_documento'];
        
        $obj_d_Alumno_Mi_Servicio = new d_Alumno_Mi_Servicio();
   
        echo $obj_d_Alumno_Mi_Servicio->Actualizar_Aceptacion_Doc_($id_ss, $id_doc, $id_version, 
                $id_estatus, $id_administrador, $nota, $id_Usr_Destinatario, $correo_usr, 
                $carrera_usr, $desc_documento, $id_division);
        break;

    case "ACTUALIZAR_ESTATUS_SERVICIO_SOCIAL":  //Actualizar el Estatus del Servicio Social a Terminado
        $id_ss = $_POST['id_ss'];
        $id_estatus = $_POST['id_estatus'];
        
        $id_Usr_Destinatario = $_POST['id_Usr_Destinatario'];
        $correo_usr = $_POST['correo_usr'];
        $carrera_usr = $_POST['carrera_usr'];
        $id_administrador = $_POST['id_administrador'];
        
        $obj_d_Alumno_Mi_Servicio = new d_Alumno_Mi_Servicio();   
        echo $obj_d_Alumno_Mi_Servicio->Actualizar_Termino_Servicio_Social($id_ss, $id_estatus, $id_Usr_Destinatario, $correo_usr, $carrera_usr, $id_administrador, $id_division);
        break;
        
}   


?>
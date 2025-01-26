<?php
/**
 * Interfaz de la Capa Negocio para Autorizar el Servicio Social del Alumno.
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

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_administrador_Aprobar_Servicio_Social.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Mi_Servicio.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
          
$tipo_Movimiento = $_POST['Tipo_Movimiento'];

switch ($tipo_Movimiento){
    case "OBTENER_SS": //Mostrar los Servicios Sociales por autorizar
        $id_estatus = $_POST['id_estatus'];
 
        $obj_d_Admon_Aprobar_SS = new d_administrador_Aprobar_Servicio_Social();
        
        echo  $obj_d_Admon_Aprobar_SS->Obtener_SS_Por_Estatus($id_estatus, $id_division);
        break;

    case "OBTENER_TOTAL_SS":
        $id_estatus = $_POST['id_estatus'];
        $obj_d_Admon_Aprobar_SS = new d_administrador_Aprobar_Servicio_Social();

        echo $obj_d_Admon_Aprobar_SS->Obtener_Total_SS($id_estatus, $id_division);
        break;

    case "OBTENER_DOC": //Obtenemos el Documento seleccionado para mostrar
        $id_ss = $_POST['id_ss'];
        $id_estatus = $_POST['id_estatus'];
        $id_documento = $_POST['id_documento'];

        $obj_d_Admon_Aprobar_SS = new d_administrador_Aprobar_Servicio_Social();
        
        echo $obj_d_Admon_Aprobar_SS->Obtener_Documento($id_ss, $id_documento, $id_estatus, $id_division);
        break;
    
    case "ACTUALIZAR_ESTATUS_DOC": //Actualizamos el Estatus del Documento Aprobado por el Administrador
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
   
        echo $obj_d_Alumno_Mi_Servicio->Actualizar_Aceptacion_Doc_($id_ss, $id_doc, $id_version, $id_estatus, $id_administrador, $nota, $id_Usr_Destinatario, $correo_usr, $carrera_usr, $desc_documento, $id_division);
        break;
    
    case "ACTUALIZAR_SERVICIO_SOCIAL": //Actualizamos el Servicio Social, si los Todos los Documentos ya fueron Aceptados
        $id_ss = $_POST['id_ss'];
        $fecha_inicio_ss = $_POST['fecha_inicio_ss'];   
        $id_Usr_Destinatario = $_POST['id_Usr_Destinatario'];
        $correo_usr = $_POST['correo_usr'];
        $carrera_usr = $_POST['carrera_usr'];        
        $id_administrador= $_POST['id_administrador'];
                
        $obj_d_Alumno_Mi_Servicio = new d_Alumno_Mi_Servicio();

        echo $obj_d_Alumno_Mi_Servicio->Actualizar_Estatus_Servicio_Social($id_ss, $fecha_inicio_ss, $id_Usr_Destinatario, $correo_usr, $carrera_usr, $id_administrador, $id_division);
}


?>
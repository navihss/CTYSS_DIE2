<?php
/**
 * Interfaz de la Capa Negocio para Autorizar la Baja de Servicio Social del Alumno.
 * @author Rogelio Reyes Mendoza
 * Septiembre 2016
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

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_administrador_Aprobar_Baja_Servicio_Social.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/zonaHoraria.php');
          
$tipo_Movimiento = $_POST['Tipo_Movimiento'];
$obj_d_Admon_Aprobar_Baja_SS = new d_administrador_Aprobar_Baja_Servicio_Social();

switch ($tipo_Movimiento){
    case "OBTENER_SOLICITUDES": //Mostrar las Solicitudes de Baja de Servicio Social por autorizar

        echo  $obj_d_Admon_Aprobar_Baja_SS->Obtener_Solicitudes_De_Baja($id_division);
        break;

    case "ACTUALIZAR_ESTATUS_SERVICIO_SOCIAL": //Actualizamos el Estatus del Servicio Social
        $id_ss = $_POST['id_ss'];
        $id_usuario = $_POST['id_Usr_Destinatario'];
        $id_estatus = $_POST['id_estatus'];
        $id_tipo_baja = $_POST['id_tipo_baja'];
        $nota = $_POST['nota'];
        $id_carrera = $_POST['carrera_usr'];
        $docs_aprobados = $_POST['docs_aprobados'];
        $id_administrador = $_POST['id_administrador'];        
        $correo_usr = $_POST['correo_usr'];

//            $jsondata['success'] = false;
//            $jsondata['data']['message'] = $id_ss.','.$id_usuario.','.$id_estatus.','.
//                    $id_tipo_baja.','.$nota.','.$id_carrera.','.$docs_aprobados.','.$id_administrador.','.$correo_usr;
//            echo json_encode($jsondata);
//            exit(); 
        echo $obj_d_Admon_Aprobar_Baja_SS->Actualizar_Estatus_SS($id_ss, $id_usuario, $id_estatus, 
                $id_tipo_baja, $nota, $id_carrera, $docs_aprobados, $id_administrador, $correo_usr, $id_division);
        break;    
}


?>
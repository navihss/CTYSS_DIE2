<?php
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

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_alumno_Pendientes.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/zonaHoraria.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_Alumno_Pendientes = new d_alumno_Pendientes();

switch ($tipo_Movimiento){
	case "OBTENER_PENDIENTES_ALUMNO":
		$id_estatus = $_POST['id_estatus'];
		$id_alumno = $_SESSION['id_usuario'];
		echo $obj_Alumno_Pendientes->Obtener_Pendientes_Alumno($id_estatus, $id_alumno);
		break;
}

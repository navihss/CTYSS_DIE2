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
$id_division=0;
if(isset($_SESSION["id_division"])){
    $id_division=$_SESSION["id_division"];
}
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_administrador_Pendientes.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_Administrador_Pendientes = new d_administrador_Pendientes();
switch ($tipo_Movimiento){
    case "OBTENER_PENDIENTES_ADMINISTRADOR":
        $id_estatus = $_POST['id_estatus'];
	echo $obj_Administrador_Pendientes->Obtener_Pendientes_Administrador($id_estatus, $id_division);
	break;
}

<?php
session_start();

if(!isset($_SESSION["id_usuario"]) and 
        !isset($_SESSION["id_tipo_usuario"]) and
        !isset($_SESSION["descripcion_tipo_usuario"]) and
        !isset($_SESSION["nombre_usuario"])){
    header('Location: /index.php');

}

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');

$id_division=0;
if(isset($_SESSION["id_division"])){
    $id_division=$_SESSION["id_division"];
}

$obj_Bitacora = new d_Usuario_Bitacora();
$obj_miBitacora = new Bitacora();

$descripcionEvento = ''; 
$obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
$obj_miBitacora->set_Id_Tema_Bitacora(160);
$obj_miBitacora->set_Id_Tipo_Evento(40);
$obj_miBitacora->set_Id_Usuario_Genera($_SESSION["id_usuario"]);
$obj_miBitacora->set_Id_Usuario_Destinatario('');
$obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
$obj_miBitacora->set_Id_Division($id_division);

$obj_Bitacora->Agregar($obj_miBitacora);

session_unset();
$_SESSION = array();
session_destroy();

exit;

?>
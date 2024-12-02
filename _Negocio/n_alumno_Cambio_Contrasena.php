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

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Cambio_Contrasena.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
      
$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_d_Alumno_Cambiar_Contrasena = new d_alumno_Cambio_Contrasena();

switch ($tipo_Movimiento){
    case 'CAMBIAR_CONTRASENA': //CAMBIAMOS LA CONTRASEÑA DEL USUARIO
           $id_usuario = $_POST['clave'];
           $contrasenaNueva = $_POST['contrasena'];          
        
           echo $obj_d_Alumno_Cambiar_Contrasena->Cambiar_Contrasena($id_usuario, $contrasenaNueva, $id_division);
        break;
}
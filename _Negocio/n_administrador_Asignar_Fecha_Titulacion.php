<?php
/**
 * Interfaz de la Capa Negocio para crear una Nueva Cuenta de Usuario.
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
         
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Administrador.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Coordinador.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Jefe_Departamento.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Profesor.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Alumno.php');

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_administrador_Asignar_Fecha_Titulacion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Catalogos_Generales.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
    
$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_d_Administrador_AFT = new d_administrador_Asignar_Fecha_Titulacion();

switch ($tipo_Movimiento){      
    case "EXISTE_USUARIO":
        $id_usuario = $_POST['claveUsuario'];
        echo $obj_d_Administrador_AFT->Existe_Clave_Usuario($id_usuario, $id_division);
        break;
    case "AGREGAR_FECHA_TITULACION":
        $id_usuario = $_POST['claveUsuario'];
        $id_carrera = $_POST['carrera'];
	$fecha_titulacion = $_POST['fechaTitulacion'];
        echo $obj_d_Administrador_AFT->Asignar_Fecha_Titulacion($id_usuario, $id_carrera, $fecha_titulacion);
        break;
}        
?>

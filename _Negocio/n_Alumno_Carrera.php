<?php

/**
 * Interfaz de la Capa Negocio para las Carreras del Alumno
 * @author Rogelio Reyes Mendoza
 * Junio 2016
 */
session_start();
if (
    !isset($_SESSION["id_usuario"]) and
    !isset($_SESSION["id_tipo_usuario"]) and
    !isset($_SESSION["descripcion_tipo_usuario"]) and
    !isset($_SESSION["nombre_usuario"])
) {
    header('Location: ../index.php');
}
if (
    !isset($_POST['Tipo_Movimiento']) and
    !isset($_POST['Id_Usuario']) and
    !isset($_POST['Id_Carrera'])
) {
    header('Location: ../index.php');
}
$id_division = 0;
if (isset($_SESSION["id_division"])) {
    $id_division = $_SESSION["id_division"];
}

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Alumno_Carrera.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];
$id_Usuario = $_POST['Id_Usuario'];
$id_Carrera = $_POST['Id_Carrera'];
$obj_d_Alumno_Carrera = new d_Alumno_Carrera();

switch ($tipo_Movimiento) {
    case "OBTENER":
        echo $obj_d_Alumno_Carrera->Mis_Carreras($id_Usuario);
        break;
    case "SELECCIONAR":
        echo $obj_d_Alumno_Carrera->Seleccionar_Carrera($id_Usuario);
        break;
    case "AGREGAR":
        echo $obj_d_Alumno_Carrera->Agregar($id_Usuario, $id_Carrera, $id_division);
        break;
    case "BORRAR":
        echo $obj_d_Alumno_Carrera->Borrar($id_Usuario, $id_Carrera);
        break;
}

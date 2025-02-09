<?php

/**
 * Interfaz de la Capa Negocio para las Inscripciones a propuesta del alumno
 * @author Rogelio Reyes Mendoza
 * Julio 2016
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
if (!isset($_POST['Tipo_Movimiento'])) {
    header('Location: ../index.php');
}
$id_division = 0;
if (isset($_SESSION["id_division"])) {
    $id_division = $_SESSION["id_division"];
}

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Alumno_Mi_Titulacion_Por_Propuesta.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');

//    $jsondata = array();
//    $jsondata['success'] = false;
//    $jsondata['data']['message'] = 'dentro de la capa de datos ';
//    echo json_encode($jsondata);
//    exit();    
$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_d_Alumno_Mi_TPP = new d_alumno_Mi_Titulacion_Por_Propuesta();

switch ($tipo_Movimiento) {
    case "ACTUALIZAR_FECHA_ESTIMADA":
        $id_inscripcion = $_POST['id_inscripcion'];
        $fechaEstimada = $_POST['fechaEstimada'];
        $motivoTitulacion = $_POST['motivoTitulacion'];
        echo $obj_d_Alumno_Mi_TPP->Registrar_Motivo_Propuesta($id_inscripcion, $fechaEstimada, $motivoTitulacion);
        break;
    case "OBTENER_MIS_INSCRIPCIONES":
        $id_alumno = $_POST['id_alumno'];
        $id_carrera = $_POST['id_carrera'];
        echo $obj_d_Alumno_Mi_TPP->Obtener_Mis_Inscripcines($id_alumno, $id_carrera);
        break;
    case "OBTENER_DOCS_ENVIADOS":
        $id_inscripcion = $_POST['id_inscripcion'];
        $id_documento = $_POST['id_documento'];
        echo $obj_d_Alumno_Mi_TPP->Obtener_Docs_Enviados($id_inscripcion, $id_documento);
        break;
    case "AGREGAR":
        $id_propuesta = $_POST['id_propuesta'];
        $id_estatus = $_POST['id_estatus'];
        $id_alumno = $_POST['id_alumno'];
        $id_carrera = $_POST['id_carrera'];
        $titulo_propuesta = $_POST['titulo_propuesta'];

        echo $obj_d_Alumno_Mi_TPP->Agregar($id_propuesta, $id_estatus, $id_alumno, $id_carrera, $titulo_propuesta, $id_division);
        break;
    case "BORRAR_INSCRIPCION":
        $id_propuesta = $_POST['id_propuesta'];
        $id_carrera = $_POST['id_carrera'];
        $id_inscripcion = $_POST['id_inscripcion'];
        $id_alumno = $_POST['id_alumno'];
        $titulo_propuesta = $_POST['titulo_propuesta'];
        $nota = $_POST['nota'];
        echo $obj_d_Alumno_Mi_TPP->Borrar_Propuesta($id_propuesta, $id_carrera, $id_inscripcion, $id_alumno, $titulo_propuesta, $nota, $id_division);
        break;
}

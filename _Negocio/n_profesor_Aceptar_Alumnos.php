<?php

/**
 * Interfaz de la Capa Negocio para Aceptar Alumnos en una Propuesta.
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
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_profesor_Aceptar_Alumnos.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_d_profesor_AA = new d_profesor_Aceptar_Alumnos();

switch ($tipo_Movimiento) {
    case "OBTENER_INSCRIPCIONES_POR_AUTORIZAR":
        $id_profesor = $_POST['id_profesor'];
        $id_estatus = $_POST['id_estatus'];

        echo $obj_d_profesor_AA->Obtener_Inscripciones_Por_Autorizar($id_estatus, $id_profesor);
        break;

    case "OBTENER_TOTAL_INSCRIPCIONES_POR_AUTORIZAR":
        $id_profesor = $_POST['id_profesor'];
        $id_estatus = $_POST['id_estatus'];

        echo $obj_d_profesor_AA->Obtener_Total_Inscripciones_Por_Autorizar($id_estatus, $id_profesor);
        break;

    case "ACTUALIZAR_ESTATUS_INSCRIPCION":

        $id_inscripcion = $_POST['id_inscripcion'];
        $id_documento = $_POST['id_documento'];

        $id_version = $_POST['id_version'];

        $id_estatus = $_POST['id_estatus'];
        $nota = $_POST['nota'];

        $id_alumno = $_POST['id_alumno'];
        $id_propuesta = $_POST['id_propuesta'];
        $titulo_propuesta = $_POST['titulo_propuesta'];
        $id_profesor = $_POST['id_profesor'];
        $correo_alumno = $_POST['correo_alumno'];
        $id_carrera = $_POST['id_carrera'];
        $desc_corta_archivo = $_POST['desc_corta_archivo'];
        //                     $jsondata['success'] = false;
        //            $jsondata['data']['message'] = 'negocio' . ' '.$id_inscripcion.' '.$id_documento.' '.$id_version.' '. $id_estatus.' '.$nota;
        //            echo json_encode($jsondata);   
        //            exit();

        echo $obj_d_profesor_AA->Actualizar_Estatus_Doc_Enviado(
            $id_inscripcion,
            $id_documento,
            $id_version,
            $id_estatus,
            $nota,
            $id_alumno,
            $id_propuesta,
            $titulo_propuesta,
            $id_profesor,
            $correo_alumno,
            $id_carrera,
            $desc_corta_archivo,
            $id_division
        );
        break;
}

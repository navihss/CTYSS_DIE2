<?php

/**
 * Interfaz de la Capa Negocio para Aprobar las propuestas del profesor
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
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_coord_jdpto_Aprobar_Propuesta.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];

switch ($tipo_Movimiento) {

    case "TRAER_INDICE":
        $id_propuesta = $_POST['id_propuesta'];

        $obj_d_Usuario = new d_coord_jdpto_Aprobar_Propuesta();


        echo  $obj_d_Usuario->Traer_Indice($id_propuesta);

        break;
    case "TRAER_INDICE_COMPLETO":
        $id_propuesta = $_POST['id_propuesta'];

        $obj_d_Usuario = new d_coord_jdpto_Aprobar_Propuesta();


        echo  $obj_d_Usuario->Traer_Indice_Completo($id_propuesta);

        break;
    case "OBTENER": //Obtenemos las propuestas pendientes de aprobar
        $id_estatus = $_POST['id_estatus'];
        $id_usuario = $_POST['id_usuario'];

        $obj_d_Usuario = new d_coord_jdpto_Aprobar_Propuesta();
        echo  $obj_d_Usuario->Obtener_Documentos_Por_Autorizar($id_estatus, $id_usuario);
        break;

    case "ACTUALIZAR_ESTATUS_DOC": //Actualizar el Estatus de Aprobación del Coord / Dpto.
        $id_propuesta_doc = $_POST['id_propuesta'];
        $id_documento_doc = $_POST['id_documento'];
        $id_version_doc = $_POST['id_version'];
        $id_estatus = $_POST['id_estatus'];
        $id_usuario = $_POST['id_usuario'];
        $nota = $_POST['nota'];
        $fecha_registro_doc = $_POST['fecha_registro'];
        $id_profesor = $_POST['id_profesor'];
        $correo_profesor = $_POST['correo_profesor'];
        $titulo_propuesta = $_POST['titulo_propuesta'];
        $desc_corta_doc = $_POST['desc_corta_doc'];

        $obj_d_Usuario = new d_coord_jdpto_Aprobar_Propuesta();
        echo  $obj_d_Usuario->Actualizar_Aceptacion_Doc(
            $id_propuesta_doc,
            $id_documento_doc,
            $id_version_doc,
            $id_estatus,
            $id_usuario,
            $nota,
            $fecha_registro_doc,
            $id_profesor,
            $correo_profesor,
            $titulo_propuesta,
            $desc_corta_doc,
            $id_division
        );
        break;
}

<?php

/**
 * Interfaz de la Capa Negocio para Aprobar Jurado del Alumno
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
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
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_coord_jdpto_Aprobar_Jurado.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_d_Aprobar_Jurado = new d_coord_jdpto_Aprobar_Jurado();

switch($tipo_Movimiento){

    case "OBTENER_JURADOS_PENDIENTES":
        $id_usuario = $_POST['id_usuario'];
        echo $obj_d_Aprobar_Jurado->Obtener_Jurados_Por_Autorizar($id_usuario);
        break;

    case "OBTENER_JURADOS_SELECCIONADO":
        $id_usuario   = $_POST['id_usuario'];
        $id_propuesta = $_POST['id_propuesta'];
        $id_version   = $_POST['id_version'];
        echo $obj_d_Aprobar_Jurado->Obtener_Jurado_Seleccionado($id_usuario, $id_propuesta, $id_version);
        break;

    // NUEVO: Para llenar el <select> "Reemplazar Por"
    case "OBTENER_PROFESORES_COORD":
        $profesores = $obj_d_Aprobar_Jurado->Obtener_Profesores_Division($id_division);
        $jsondata['success'] = true;
        $jsondata['data']['message'] = 'Profesores disponibles';
        $jsondata['data']['profesores'] = $profesores;
        echo json_encode($jsondata);
        break;

    case "ACTUALIZAR_VoBo":
        $id_propuesta     = $_POST['Id_Propuesta'];
        $id_version       = $_POST['id_Version'];
        $id_usuario       = $_POST['Id_Usuario'];
        $vobo_usuario     = $_POST['lista_VoBo']; // "1,1,0,nota|2,0,15,nota2..."
        $titulo_propuesta = $_POST['titulo_propuesta'];

        echo $obj_d_Aprobar_Jurado->Actualizar_VoBo(
             $id_propuesta,
             $id_version,
             $id_usuario,
             $vobo_usuario,
             $titulo_propuesta,
             $id_division
        );
        break;

    default:
        $json['success'] = false;
        $json['data']['message'] = 'Movimiento no reconocido.';
        echo json_encode($json);
        break;
}
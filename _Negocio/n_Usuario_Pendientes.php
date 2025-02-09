<?php

/**
 * Interfaz de la Capa Negocio para Obtener el Totalde Pendientes
 * @author Rogelio Reyes Mendoza
 * Noviembre 2016
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

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario_Pendientes.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_Usuario_Pendientes = new d_Usuario_Pendientes();

switch ($tipo_Movimiento) {
    case "OBTENER_CEREMONIAS_JURADO_PENDIENTES": //Obtenemos el total de ceremonias y jurados pendientes de aprobar
        $id_usuario = $_POST['id_usuario'];
        echo  $obj_Usuario_Pendientes->Obtener_Pendientes_Coordinador($id_usuario);
        break;
}

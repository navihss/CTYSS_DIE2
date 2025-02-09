<?php

/**
 * Interfaz de la Capa Negocio para Autorizar la Baja de Ceremonia del Alumno.
 * @author Rogelio Reyes Mendoza
 * Septiembre 2016
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

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_administrador_Aprobar_Baja_Ceremonia.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];
$obj_d_Admon_Aprobar_Baja_Ceremonia = new d_administrador_Aprobar_Baja_Ceremonia();

switch ($tipo_Movimiento) {
    case "OBTENER_SOLICITUDES": //Mostrar las Solicitudes de Baja de Ceremonia por autorizar

        echo  $obj_d_Admon_Aprobar_Baja_Ceremonia->Obtener_Solicitudes_De_Baja();
        break;

    case "ACTUALIZAR_ESTATUS_CEREMONIA": //Actualizamos el Estatus del Servicio Social
        $id_ceremonia = $_POST['id_ceremonia'];
        $id_usuario = $_POST['id_Usr_Destinatario'];
        $id_estatus = $_POST['id_estatus'];
        $id_tipo_baja = $_POST['id_tipo_baja'];
        $nota = $_POST['nota'];
        $id_carrera = $_POST['carrera_usr'];
        $id_administrador = $_POST['id_administrador'];
        $correo_usr = $_POST['correo_usr'];

        echo $obj_d_Admon_Aprobar_Baja_Ceremonia->Actualizar_Estatus_Ceremonia(
            $id_ceremonia,
            $id_usuario,
            $id_estatus,
            $id_tipo_baja,
            $nota,
            $id_carrera,
            $id_administrador,
            $correo_usr,
            $id_division
        );
        break;
}

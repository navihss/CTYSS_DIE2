<?php

/**
 * Interfaz de la Capa Negocio para la Bitácora
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

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Bitacora.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];



$obj_d_Usuario_Bitacora = new d_Usuario_Bitacora();

switch ($tipo_Movimiento) {
    case "GENERAR_REPORTE":

        if (isset($_POST['chk_Propuesta'])) {
            $chk_Propuesta = 1;
        } else {
            $chk_Propuesta = 0;
        }

        if (isset($_POST['chk_Ceremonia'])) {
            $chk_Ceremonia = 2;
        } else {
            $chk_Ceremonia = 0;
        }

        $carreras = '';

        if (isset($_POST['chk_Electrica'])) {
            if (strcmp($carreras, '') == 0) {
                $carreras = '109';
            }
        }

        if (isset($_POST['chk_Computacion'])) {
            if (strcmp($carreras, '') == 0) {
                $carreras = '110';
            } else {
                $carreras = $carreras . ', 110';
            }
        }

        if (isset($_POST['chk_Telecomunicaciones'])) {
            if (strcmp($carreras, '') == 0) {
                $carreras = '111';
            } else {
                $carreras = $carreras . ', 111';
            }
        }


        $tipo_usuario = $_POST['tipo_usuario'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_termino = $_POST['fecha_termino'];

        echo $obj_d_Usuario_Bitacora->Generar_Reporte($tipo_usuario, $chk_Propuesta, $chk_Ceremonia, $fecha_inicio, $fecha_termino, $carreras);
        break;
    case "OBTENER_TEMAS":
        $tipo_usuario = $_POST['tipo_usuario'];
        echo $obj_d_Usuario_Bitacora->Obtener_Temas($tipo_usuario);
        break;
    case "OBTENER_BITACORA":
        $obj_Bitacora = new Bitacora();

        if (isset($_POST['chk_Tema'])) {
            $arr_chk_Tema = $_POST['chk_Tema'];
            $renglones = count($arr_chk_Tema);
            $ids = '';

            for ($i = 0; $i < $renglones; $i++) {
                $ids .= $arr_chk_Tema[$i] . ',';
            }
            $ids = substr($ids, 0, strlen($ids) - 1);
            $obj_Bitacora->set_Id_Tema_Bitacora($ids);
        } else {
            $obj_Bitacora->set_Id_Tema_Bitacora('');
        }

        if (isset($_POST['chk_Tipos_Evento'])) {
            $arr_chk_Tipos_Evento = $_POST['chk_Tipos_Evento'];
            $renglones = count($arr_chk_Tipos_Evento);
            $ids = '';

            for ($i = 0; $i < $renglones; $i++) {
                $ids .= $arr_chk_Tipos_Evento[$i] . ',';
            }
            $ids = substr($ids, 0, strlen($ids) - 1);
            $obj_Bitacora->set_Id_Tipo_Evento($ids);
        } else {
            $obj_Bitacora->set_Id_Tipo_Evento('');
        }

        $obj_Bitacora->set_Id_Usuario_Genera($_POST['id_usuario_genera']);
        $obj_Bitacora->set_Id_Usuario_Destinatario($_POST['id_usuario_destinatario']);
        $tipo_usuario = $_POST['Id_Tipo_User'];
        $f_inicio = $_POST['fecha_inicio'];
        $f_termino = $_POST['fecha_termino'];

        echo $obj_d_Usuario_Bitacora->Obtener($obj_Bitacora, $f_inicio, $f_termino, $tipo_usuario);
        break;
}

<?php

/**
 * Interfaz de la Capa Negocio para los Documentos del Usuario
 * @author Rogelio Reyes Mendoza
 * Octubre 2016
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
    !isset($_POST['Id_Tipo_Usuario'])
) {
    header('Location: ../index.php');
}

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_profesor_Mis_Docs.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];

switch ($tipo_Movimiento) {
    case 'MIS_DOCUMENTOS':
        $obj_Docs = new d_profesor_Mis_Docs();
        $id_Usuario = $_POST['id_usuario'];
        echo $obj_Docs->Mis_Documentos($id_Usuario);
        break;
    case 'BORRAR_DOCUMENTO':
        $obj_Docs = new d_profesor_Mis_Docs();
        $id_usuario = $_POST['id_usuario'];
        $id_doc = $_POST['id_doc'];
        $nom_archivo = $_POST['nom_archivo'];

        echo $obj_Docs->Borrar_Documento($id_usuario, $id_doc, $nom_archivo);
        break;

    case 'COMPARTIR_DOCUMENTO':
        $obj_Docs = new d_profesor_Mis_Docs();
        $id_usuario = $_POST['id_usuario'];
        $id_doc = $_POST['id_doc'];
        $compartir = $_POST['compartir'];

        echo $obj_Docs->Compartir_Documento($id_usuario, $id_doc, $compartir);
        break;
}

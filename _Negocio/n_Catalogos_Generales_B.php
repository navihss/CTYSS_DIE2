<?php

/**
 * Interfaz de la Capa Negocio para los Catalogos Generales B
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

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Catalogos_Generales.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];
$id_division = $_POST['id_division'];

$obj_d_Cat_Grales = new d_Catalogos_Generales();

switch ($tipo_Movimiento) {
    case "OBTENER_CAT_GRALES":
        $tabla_Catalogo = $_POST['tabla_Catalogo'];
        $tabla_Campos = $_POST['tabla_Campos'];
        $Condicion = '';
        $OrderBy = 'Descripcion';
        echo $obj_d_Cat_Grales->Obtener($tabla_Catalogo, $Campos, $Condicion, $OrderBy);

    case "OBTENER_COORDINACIONES":
        echo $obj_d_Cat_Grales->Obtener_Coordinaciones($id_division);
        break;

    case "OBTENER_DEPARTAMENTOS":
        echo $obj_d_Cat_Grales->Obtener_Departamentos($id_division);
        break;

    case "BUSCAR_COORDINADORES":
        //CUANDO SE BUSCA UN COORDINADOR POR SU NOMBRE PARA NUEVO JEFE DE COORDINACION
        $textoBuscar = $_POST['textoBuscar'];
        $id_area = $_POST['id_coord_dpto'];
        echo $obj_d_Cat_Grales->Obtener_Coordinadores_Activos($textoBuscar, $id_area);
        break;

    case "BUSCAR_JEFES_DPTO":
        //CUANDO SE BUSCA A JEFE DE DPTO POR SU NOMBRE PARA NUEVO JEFE DE DPTO
        $textoBuscar = $_POST['textoBuscar'];
        $id_area = $_POST['id_coord_dpto'];
        echo $obj_d_Cat_Grales->Obtener_Jefes_Dpto_Activos($textoBuscar, $id_area);
        break;
}

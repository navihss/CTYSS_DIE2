<?php

/**
 * Interfaz de la Capa Negocio para las Inscripciones a propuesta del alumno por ceremonia
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */
session_start();
if (
    !isset($_SESSION["id_usuario"]) and
    !isset($_SESSION["id_tipo_usuario"]) and
    !isset($_SESSION["descripcion_tipo_usuario"]) and
    !isset($_SESSION["nombre_usuario"]) and
    !isset($_POST['Tipo_Movimiento'])
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
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Alumno_Mi_Ceremonia.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Ceremonia.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_d_Alumno_Mi_Ceremonia = new d_Alumno_Mi_Ceremonia();

switch ($tipo_Movimiento) {
    case "ACTUALIZAR_FECHA_ESTIMADA":
        $id_ceremonia = $_POST['id_ceremonia'];
        $fechaEstimada = $_POST['fechaEstimada'];
        $motivoTitulacion = $_POST['motivoTitulacion'];
        echo $obj_d_Alumno_Mi_Ceremonia->Registrar_Motivo_Ceremonia($id_ceremonia, $fechaEstimada, $motivoTitulacion);
        break;
    case "OBTENER_MIS_CEREMONIAS":
        $id_alumno = $_POST['id_alumno'];
        $id_carrera = $_POST['id_carrera'];

        echo $obj_d_Alumno_Mi_Ceremonia->Obtener_Mis_Ceremonias($id_alumno, $id_carrera);
        break;
    case "SELECCIONAR_MODALIDAD":
        $id_alumno = $_POST['id_alumno'];
        $id_carrera = $_POST['id_carrera'];

        echo $obj_d_Alumno_Mi_Ceremonia->Obtener_Modalidades($id_alumno, $id_carrera);
        break;
    case "AGREGAR":
        $id_alumno = $_POST['id_alumno'];
        $id_carrera = $_POST['id_carrera'];
        $id_modalidad = $_POST['id_modalidad'];
        $desc_modalidad = $_POST['desc_modalidad'];

        echo $obj_d_Alumno_Mi_Ceremonia->Agregar($id_alumno, $id_carrera, $id_modalidad, $desc_modalidad);
        break;
    case "ACTUALIZAR":
        $obj_Ceremonia = new Ceremonia();
        $obj_Ceremonia->set_Diplomados_Cursos($_POST['diplomados_cursos']);
        $obj_Ceremonia->set_Id_Alumno($_POST['Id_Usuario']);
        $obj_Ceremonia->set_Id_Carrera($_POST['Id_Carrera']);
        $obj_Ceremonia->set_Id_Ceremonia($_POST['Id_Ceremonia']);
        $obj_Ceremonia->set_Id_Tipo_Propuesta($_POST['id_tipo_propuesta']);
        $obj_Ceremonia->set_Materias($_POST['materias']);
        $obj_Ceremonia->set_Nombre_Articulo($_POST['titulo_articulo']);
        $obj_Ceremonia->set_Nombre_Revista($_POST['nombre_Revista']);
        $obj_Ceremonia->set_Programa_Posgrado($_POST['programa_posgrado']);
        $obj_Ceremonia->set_Sedes($_POST['sedes']);
        $obj_Ceremonia->set_Desc_Propuesta($_POST['Desc_Propuesta']);

        echo $obj_d_Alumno_Mi_Ceremonia->Actualizar($obj_Ceremonia, $id_division);
        break;
    case "OBTENER_CEREMONIA":
        $id_ceremonia = $_POST['id_ceremonia'];

        echo $obj_d_Alumno_Mi_Ceremonia->Obtener_Ceremonia($id_ceremonia);
        break;
    case "OBTENER_MIS_DOCUMENTOS":
        $id_ceremonia = $_POST['id_ceremonia'];

        echo $obj_d_Alumno_Mi_Ceremonia->Obtener_Mis_Documentos($id_ceremonia);
        break;

    case "BORRAR_CEREMONIA":

        $id_ceremonia = $_POST['id_ceremonia'];
        $id_carrera = $_POST['id_carrera'];
        $id_alumno = $_POST['id_alumno'];
        $descripcion_ceremonia = $_POST['descripcion_ceremonia'];
        $nota = $_POST['nota'];
        echo $obj_d_Alumno_Mi_Ceremonia->Borrar_Ceremonia($id_ceremonia, $id_alumno, $id_carrera, $descripcion_ceremonia, $nota, $id_division);
        break;
}

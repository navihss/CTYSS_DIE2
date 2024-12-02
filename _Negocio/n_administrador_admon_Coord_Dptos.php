<?php
/**
 * Interfaz de la Capa Negocio para el Catálogo de Coordinaciones y Departamentos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */
session_start();
if(!isset($_SESSION["id_usuario"]) and 
        !isset($_SESSION["id_tipo_usuario"]) and
        !isset($_SESSION["descripcion_tipo_usuario"]) and
        !isset($_SESSION["nombre_usuario"])){
    header('Location: ../index.php');
}
if(!isset($_POST['Tipo_Movimiento'])){
    header('Location: ../index.php');
}
$id_division=0;
if(isset($_SESSION["id_division"])){
    $id_division=$_SESSION["id_division"];
}

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_administrador_admon_Coord_Dptos.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');

 

    $tipo_Movimiento = $_POST['Tipo_Movimiento'];

    $obj_d_admon_coord_dptos = new d_administrador_admon_Coord_Dptos();

    switch ($tipo_Movimiento){
        case "ACTUALIZAR_JEFE_COORD_DPTO":

            $tipo_catalogo = $_POST['tipo_catalogo'];
            $id_jefe_actual = $_POST['id_jefe_actual']; 
            $id_jefe_nuevo = $_POST['id_jefe_nuevo'];
            $nom_nvo_jefe = $_POST['nom_nvo_jefe'];
            $id_administrador = $_POST['id_administrador'];
            echo $obj_d_admon_coord_dptos->Actualizar_Coord_Dpto($tipo_catalogo, $id_jefe_actual, $id_jefe_nuevo, $nom_nvo_jefe, $id_administrador, $id_division);
            break;

        case "OBTENER_AREAS_JEFES":

            $tipo_catalogo = $_POST['tipo_catalogo'];
            
            echo $obj_d_admon_coord_dptos->Obtener_Area_Jefes_Actuales($tipo_catalogo, $id_division);
            break;

        }        
?>
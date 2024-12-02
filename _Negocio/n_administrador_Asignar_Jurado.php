<?php
/**
 * Interfaz de la Capa Negocio para Aprobar Jurado del Alumno
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

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_administrador_Asignar_Jurado.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
          
$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_d_Asignar_Jurado = new d_administrador_Asignar_Jurado();

switch ($tipo_Movimiento){
    case "OBTENER_JURADOS_PENDIENTES": //Obtenemos los Jurados pendientes de aprobar

        echo  $obj_d_Asignar_Jurado->Obtener_Jurados_Por_Autorizar();
        break;
    
    case "OBTENER_JURADOS_SELECCIONADO": //Obtenemos los Jurados seleccionado
        $id_propuesta = $_POST['id_propuesta'];
        $id_version = $_POST['id_version'];
        echo  $obj_d_Asignar_Jurado->Obtener_Jurado_Seleccionado($id_propuesta, $id_version);
        break;

    case "OBTENER_PROFESORES": //Buscar Profesores
        $textoBuscar = $_POST['textoBuscar'];
        echo  $obj_d_Asignar_Jurado->Obtener_Profesores($textoBuscar);
        break;
    
    case "ACTUALIZAR_DEFINITIVOS": //Actualizar el JURADO definitivo
        $id_propuesta = $_POST['Id_Propuesta'];
        $id_version = $_POST['id_Version'];
        $id_usuario = $_POST['Id_Usuario'];
        $lista_Definitivos = $_POST['lista_Definitivos'];
        $titulo_propuesta = $_POST['titulo_propuesta'];

        print_r($id_propuesta.'\n');
        print_r($id_version.'\n');
        print_r($id_usuario.'\n');
        print_r($lista_Definitivos.'\n');
        print_r($titulo_propuesta.'\n');
            
        echo  $obj_d_Asignar_Jurado->Actualizar_Jurado_Def($id_propuesta, $id_version, $id_usuario, $lista_Definitivos, $titulo_propuesta, $id_division);
        break;    

}   

?>
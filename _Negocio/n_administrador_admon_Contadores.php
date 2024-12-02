<?php
/**
 * Interfaz de la Capa Negocio para los contadores de serv social y propuestas
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

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_administrador_admon_Contadores.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
 
    $tipo_Movimiento = $_POST['Tipo_Movimiento'];

    $obj_d_Aministrador_Admon_Contadores = new d_administrador_admon_Contadores();
           
    switch ($tipo_Movimiento){
        case "OBTENER_ULTIMO_CONTADOR_SS":
            echo $obj_d_Aministrador_Admon_Contadores->Obtener_Ultimo_Contador_SS();
            break;
        case "OBTENER_ULTIMO_CONTADOR_PP":
            echo $obj_d_Aministrador_Admon_Contadores->Obtener_Ultimo_Contador_Propuestas();
            break;
        case "OBTENER_ULTIMO_CONTADOR_CEREMONIA":
            echo $obj_d_Aministrador_Admon_Contadores->Obtener_Ultimo_Ceremonia();
            break;
        
        case "AGREGAR_PERIODO_SS":
            $periodo = $_POST['periodo'];
            $id_administrador = $_POST['id_administrador'];
            echo $obj_d_Aministrador_Admon_Contadores->Agregar_Periodo_SS($periodo, $id_administrador, $id_division);
            break;        
        case "AGREGAR_PERIODO_PP":
            $periodo = $_POST['periodo'];
            $id_administrador = $_POST['id_administrador'];
            echo $obj_d_Aministrador_Admon_Contadores->Agregar_Periodo_PP($periodo, $id_administrador, $id_division);
            break;                
        case "AGREGAR_PERIODO_CEREMONIA":
            $periodo = $_POST['periodo'];
            $id_administrador = $_POST['id_administrador'];
            echo $obj_d_Aministrador_Admon_Contadores->Agregar_Periodo_Ceremonia($periodo, $id_administrador, $id_division);
            break;                
        
    }        
?>
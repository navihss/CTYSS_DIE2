<?php
/**
 * Interfaz de la Capa Negocio para la Clase Usuario.
 * @author Rogelio Reyes Mendoza
 * Junio 2016
 */
session_start();
if(!isset($_SESSION["id_usuario"]) and 
        !isset($_SESSION["id_tipo_usuario"]) and
        !isset($_SESSION["descripcion_tipo_usuario"]) and
        !isset($_SESSION["nombre_usuario"]) and 
        !isset($_POST['Tipo_Movimiento'])){
    header('Location: ../index.php');
}

if(!isset($_POST['Tipo_Movimiento']) and 
        !isset($_POST['Id_Usuario']) and
        !isset($_POST['Id_Carrera']) and
        !isset($_POST['clave'])){
    header('Location: ../index.php');
}

$id_division=0;
if(isset($_SESSION["id_division"])){
    $id_division=$_SESSION["id_division"];
}

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Mi_Servicio.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Mis_Reportes.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Servicio_Social.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');

function sumaFechas ($suma,$fechaInicial = false)
{
  $fecha = !empty($fechaInicial) ? $fechaInicial : date('d-m-Y'); 
  $nuevaFecha = strtotime ($suma , strtotime ( $fecha ) ) ;
  $nuevaFecha = date ( 'd-m-Y' , $nuevaFecha );
  return $nuevaFecha;
}
       
$tipo_Movimiento = $_POST['Tipo_Movimiento'];
$id_Usuario = $_POST['Id_Usuario'];
$id_Carrera = $_POST['Id_Carrera'];
$id_SS = $_POST['clave'];
 
            
$obj_d_Alumno_SS = new d_Alumno_Mi_Servicio();

switch ($tipo_Movimiento){
    case "OBTENER": //Mostrar los Servicios Sociales para la Carrera Seleccionada
        echo $obj_d_Alumno_SS->Obtener_SS_Todos($id_Usuario, $id_Carrera); //>Obtener_SS($id_Usuario, $id_Carrera, $id_SS);
        break;
    case "SELECCIONAR": //Recuperar los datos del Servicio Social seleccionado para Editarlo         
        echo $obj_d_Alumno_SS->Obtener_SS_Id($id_SS);
        break;
    case "OBTENER_MIS_DOCUMENTOS": //Recuperar los documentos enviados para la autorización del Servicio Social
        echo $obj_d_Alumno_SS->Obtener_Mis_Documentos($id_SS);
        break;        
    case "OBTENER_DATOS_GENERALES_SS": //Recuperar la información general del Servico Social indicado
        
       
        echo $obj_d_Alumno_SS->Obtener_Datos_Grales($id_SS);
        break;        

    case "AGREGAR": //Agregar un Servicio Social       
             
        $objServicioSocial = new Servicio_Social();
        $objServicioSocial->set_Id_SS('');
        $objServicioSocial->set_Duracion_Meses($_POST['duracion']);
        $meses = $_POST['duracion'];
        $fecha = $_POST['fecha_Inicio'];
        $fecha = str_replace('/','-',$fecha);
        $fecha = date('Y-m-d',strtotime($fecha));
        $fecha_termino=strtotime ( '+' . $meses . ' month' , strtotime ($fecha) ) ;  
        $fecha_termino=date('Y-m-d',$fecha_termino);
        $objServicioSocial->set_Fecha_Inicio($fecha);
        $objServicioSocial->set_Fecha_Termino($fecha_termino);
        $objServicioSocial->set_Avance_Creditos($_POST['numero_Creditos']);
        $objServicioSocial->set_Avance_Porcentaje($_POST['porcentaje_Avance']);
        $objServicioSocial->set_Promedio($_POST['promedio']);
        $objServicioSocial->set_Jefe_Inmediato(($_POST['jefe_Inmediato']));
        $objServicioSocial->set_Percepcion_Mensual($_POST['percepcion_Mensual']);
        $objServicioSocial->set_Id_Programa($_POST['clave_Programa']);
        $objServicioSocial->set_Id_Tipo_Remuneracion($_POST['Tipo_Remuneracion']);
        $objServicioSocial->set_Id_Tipo_Baja(5);    
        $objServicioSocial->set_Id_Estatus(2); //Por Autorizar
        $objServicioSocial->set_Id_Carrera($id_Carrera);
        $objServicioSocial->set_Id_Alumno($id_Usuario);
        $objServicioSocial->set_Id_Division($id_division);
        echo $obj_d_Alumno_SS->Agregar($objServicioSocial);
        break;
    
    case "ACTUALIZAR": //Actualizamos los datos del Servicio Social

        $objServicioSocial = new Servicio_Social();
        $objServicioSocial->set_Id_SS($id_SS);
        $objServicioSocial->set_Percepcion_Mensual($_POST['percepcion_Mensual']);
        $objServicioSocial->set_Id_Tipo_Remuneracion($_POST['Tipo_Remuneracion']);
        $objServicioSocial->set_Id_Alumno($id_Usuario);
        $objServicioSocial->set_Id_Carrera($id_Carrera);
        $objServicioSocial->set_Id_Division($id_division);
              
        echo $obj_d_Alumno_SS->Actualizar($objServicioSocial);
        break;
        
    case "BORRAR": //Borrar un Servicio Social  
        $id_SS_Borrar = $_POST['clave'];
        $nvo_id_estatus= $_POST['nvo_id_estatus'];
        $nvo_id_baja = $_POST['nvo_id_baja'];
        $id_usuario = $_POST['Id_Usuario'];
        $nota = $_POST['nota'];
        $id_carrera = $_POST['Id_Carrera'];

        echo $obj_d_Alumno_SS->Borrar_SS($id_SS, $id_usuario, $nvo_id_estatus, $nvo_id_baja, $nota, $id_carrera, $id_division);
        break;
    
    case "SOLICITAR_BAJA": //Solicitar Borrado
        $id_SS_Borrar = $_POST['clave'];
        $nvo_id_estatus= $_POST['nvo_id_estatus'];
        $nvo_id_baja = $_POST['nvo_id_baja'];
        $id_usuario = $_POST['Id_Usuario'];
        $nota = $_POST['nota'];
        $id_carrera = $_POST['Id_Carrera'];

        echo $obj_d_Alumno_SS->Solicitar_Baja_SS($id_SS, $id_usuario, $nvo_id_estatus, $nvo_id_baja, $nota, $id_carrera);
        break;
    
}


?>
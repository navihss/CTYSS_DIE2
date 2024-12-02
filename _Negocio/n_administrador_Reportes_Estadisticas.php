<?php
/**
 * Interfaz de la Capa Negocio para Autorizar los Reportes Bimestrales del Alumno.
 * @author Rogelio Reyes Mendoza
 * Julio 2016
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
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_administrador_Reportes_Estadisticas.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
          
$tipo_Movimiento = $_POST['Tipo_Movimiento'];

switch ($tipo_Movimiento){
    case "OBTENER_REPORTES_ESTADISTICAS": //Mostrar los Reportes Bimestrales por autorizar
        $tx_profesor     = $_POST['tx_profesor'];
        $fecha_inicio    = $_POST['fecha_inicio'];
        
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Reportes_No_Alumnos_Titulados($tx_profesor,$fecha_inicio);
        break;  
        
    case "OBTENER_REPORTES_TITULADO": //Mostrar los Reportes Bimestrales por autorizar
        $id_anio    = $_POST['id_anio'];
        $vision     = $_POST['vision'];
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Reportes_No_Alumnos_Titulados_por_anio($id_anio,$vision);
        break; 
        
    case "OBTENER_REPORTES_ANIOS": //Mostrar los Reportes Bimestrales por autorizar
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Reportes_Anios();
        break; 
        
    case "OBTENER_PROGRAMAS_SERVICO_SOCIAL": //Mostrar los Reportes Bimestrales por autorizar
        
        $tx_alumno		    = $_POST['tx_alumno'];
        $id_carrera		    = $_POST['id_carrera'];
        $fecha_inicio 		= $_POST['fecha_inicio'];
        $fecha_fin 			= $_POST['fecha_fin'];
	$fecha_verifico 	= $_POST['fecha_verifico'];
        $fecha_verifico_fin = $_POST['fecha_verifico_fin'];
	$anio 				= $_POST['anio'];
        $no_registro 		= $_POST['no_registro'];
        $id_programa 		= $_POST['id_programa'];
        $tx_nombre_programa = $_POST['tx_nombre_programa'];
        $tx_dependencia 	= $_POST['tx_dependencia'];
        $tx_responsable 	= $_POST['tx_responsable'];
        $tx_jefe_inmediato 	= $_POST['tx_jefe_inmediato'];
        $id_estatus 		= $_POST['id_estatus'];
        $id_genero 			= $_POST['id_genero'];
	$num_cuenta 	    	= $_POST['num_cuenta'];


        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Reportes_Programas_servicio_social(
                                                                        $tx_alumno,$id_carrera,
                                                                        $fecha_inicio,$fecha_fin,
									$fecha_verifico,$fecha_verifico_fin,
                                                                        $anio,$no_registro,
                                                                        $id_programa,$tx_nombre_programa,
                                                                        $tx_dependencia,$tx_responsable,$tx_jefe_inmediato,
                                                                        $id_estatus,$id_genero,$num_cuenta
                                                                        );
        break;
        
    case "OBTENER_PROGRAMAS_SERVICO_SOCIAL_PP": //Mostrar los Reportes Bimestrales por autorizar
        
        $tx_alumno		    = $_POST['tx_alumno'];
        $id_carrera		    = $_POST['id_carrera'];
        $fecha_inicio 		= $_POST['fecha_inicio'];
        $fecha_fin 			= $_POST['fecha_fin'];
        $anio 				= $_POST['anio'];
        $no_registro 		= $_POST['no_registro'];
        $id_programa 		= $_POST['id_programa'];
        $tx_nombre_programa = $_POST['tx_nombre_programa'];
        $tx_dependencia 	= $_POST['tx_dependencia'];
        $tx_responsable 	= $_POST['tx_responsable'];
        $tx_jefe_inmediato 	= $_POST['tx_jefe_inmediato'];
        $id_estatus 		= $_POST['id_estatus'];
        $id_genero 			= $_POST['id_genero'];

        
        
        
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Reportes_Programas_servicio_social_pp(
                                                                            $tx_alumno,$id_carrera,
                                                                            $fecha_inicio,$fecha_fin,
                                                                            $anio,$no_registro,
                                                                            $id_programa,$tx_nombre_programa,
                                                                            $tx_dependencia,$tx_responsable,$tx_jefe_inmediato,
                                                                            $id_estatus,$id_genero
                                                                            );
        break;
        
    case "OBTENER_CATALOGO_CARRERA": //Mostrar los Reportes Bimestrales por autorizar
        $id_carrera= $_POST['id_carrera'];
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Catalogo_Carrera();
        break; 
    case "OBTENER_CATALOGO_ESTATUS": //Mostrar los Reportes Bimestrales por autorizar
        $id_carrera= $_POST['id_carrera'];
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Catalogo_Estatus();
        break; 
    case "OBTENER_CATALOGO_GENERO": //Mostrar los Reportes Bimestrales por autorizar
        $id_carrera= $_POST['id_carrera'];
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Catalogo_Genero();
        break; 
    case "OBTENER_CATALOGO_DEPENDENCIA": //Mostrar los Reportes Bimestrales por autorizar
        $id_carrera= $_POST['id_carrera'];
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Catalogo_Dependencia();
        break; 
    case "OBTENER_CATALOGO_TIPO_SERVICIO_SOCIAL": //Mostrar los Reportes Bimestrales por autorizar
        $id_carrera= $_POST['id_carrera'];
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Catalogo_Tipo_Servicio();
        break; 
        
    case "OBTENER_CATALOGO_RECINTO": //Mostrar los Reportes Bimestrales por autorizar
        $id_carrera= $_POST['id_carrera'];
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Catalogo_Recinto();
        break; 
        
    case "OBTENER_CATALOGO_MODALIDAD": //Mostrar los Reportes Bimestrales por autorizar
        $id_carrera= $_POST['id_carrera'];
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Catalogo_Modalidad();
        break; 
        
    case "OBTENER_REPORTES_FORMATOS": //Mostrar los Reportes Bimestrales por autorizar
        $tx_profesor       = $_POST['tx_profesor'];
        $tx_tipo_trabajo    = $_POST['tx_tipo_trabajo'];
        $fecha_inicio       = $_POST['fecha_inicio'];
        
        
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Reportes_Formatos($tx_profesor,$tx_tipo_trabajo,$fecha_inicio);
        break;  
        
    case "OBTENER_REPORTES_TITULACION_ALUMNOS": //Mostrar los Reportes Bimestrales por autorizar
        $tx_sinodales       = $_POST['tx_sinodales'];
        $fecha_inicio       = $_POST['fecha_inicio'];
        $slc_alumnosProceso = $_POST['slc_alumnosProceso'];
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Reportes_Titulacion_Alumnos($tx_sinodales,$fecha_inicio,$slc_alumnosProceso);
        break; 
        
    case "OBTENER_REPORTES_TITULACION_PROFESORES": //Mostrar los Reportes Bimestrales por autorizar
        $id_estatus = $_POST['id_estatus'];
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Reportes_Titulacion_Profesores($id_estatus);
        break; 
        
    case "OBTENER_REPORTES_TITULACION_CEREMONIA": //Mostrar los Reportes Bimestrales por autorizar
        $tx_alumno      = $_POST['tx_alumno'];
        $id_carrera     = $_POST['id_carrera'];
        $fecha_inicio   = $_POST['fecha_inicio'];
        $tx_recinto     = $_POST['tx_recinto'];
        $tx_modalidad   = $_POST['tx_modalidad'];

        
        $obj_d_Admon_Reportes= new d_administrador_Reportes_Estadisticas();
        echo  $obj_d_Admon_Reportes->Obtener_Reportes_Titulacion_Ceremonia($tx_alumno,
                                                                            $id_carrera,
                                                                            $fecha_inicio,
                                                                            $tx_recinto,
                                                                            $tx_modalidad
                                                                            );
        break; 
        
}


?>

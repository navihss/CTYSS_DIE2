<?php
use App\Database\Connection;
header('Content-Type: text/html; charset=UTF-8');
require_once __DIR__ . '/../app/Database/Connection.php';
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Mi_Servicio.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Mis_Reportes.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Mi_Titulacion_Por_Propuesta.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Mi_Fecha_Titulacion.php');

class d_alumno_Pendientes{
	function Obtener_Pendientes_Alumno($id_estatus, $id_alumno){
		try{
			$jsondata['success'] = true;
            $jsondata['data']['message'] = '';
            $jsondata['data']['registros'] = array();

            $obj_alumno_Mi_Servicio = new d_Alumno_Mi_Servicio();
            $pendientesServicio = $obj_alumno_Mi_Servicio->Obtener_Total_SS($id_estatus, $id_alumno);

            $obj_alumno_Mis_Reportes = new d_Alumno_Mis_Reportes();
            $pendientesReportes = $obj_alumno_Mis_Reportes->Obtener_Total_Reportes($id_estatus, $id_alumno);

            $obj_alumno_Mi_Titulacion_Por_Propuesta = new d_alumno_Mi_Titulacion_Por_Propuesta();
            $pendientesPropuesta = $obj_alumno_Mi_Titulacion_Por_Propuesta->Obtener_Total_Propuesta($id_estatus, $id_alumno);

            $obj_alumno_Mi_Fecha_Titulacion = new d_alumno_Mi_Fecha_Titulacion();
            $fechaTitulacion = $obj_alumno_Mi_Fecha_Titulacion->Obtener_Fecha_Titulacion($id_alumno);

            $totalpendientesServicio = $pendientesServicio['data']['registros'][0]['total5'];
            $jsondata['data']['registros'][0]['total5'] = $totalpendientesServicio;

            $totalpendientesReportes = $pendientesReportes['data']['registros'][0]['total5'];
            $jsondata['data']['registros'][1]['total5'] = $totalpendientesReportes;

            $totalpendientesPropuestas = $pendientesPropuesta['data']['registros'][0]['total5'];
            $jsondata['data']['registros'][2]['total5'] = $totalpendientesPropuestas;

            if ($fechaTitulacion['data']['message']=='EXISTE_FECHA'){
                $jsondata['data']['registros'][3]['total5'] = 1;
            }
            else{
                $jsondata['data']['registros'][3]['total5'] = 0;
            }

            echo json_encode($jsondata);
            exit();
		}
		catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }
	}
}

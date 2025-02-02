<?php

/**
 * Definición de la Capa de Datos para obtener Mis Pendientes según el tipo de usuario
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Noviembre 2017
 */

header('Content-Type: text/html; charset=UTF-8');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_coord_jdpto_Aprobar_Ceremonia.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_coord_jdpto_Aprobar_Jurado.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_coord_jdpto_Aprobar_Propuesta.php');

class d_Usuario_Pendientes {
    
    //*********************************************************************                
    //OBTENEMOS LOS PENDIENTES DEL COORDINADOR
    function Obtener_Pendientes_Coordinador($id_usuario){
      
        try{
            $jsondata['success'] = true;
            $jsondata['data']['message'] = '';
            $jsondata['data']['registros'] = array();
            
            $obj_coord_jdpto_Aprobar_Propuesta = new d_coord_jdpto_Aprobar_Propuesta();
            $pendientesPropuestas = $obj_coord_jdpto_Aprobar_Propuesta->Obtener_Total_Documentos_Por_Autorizar($id_usuario);
            
            $obj_coord_jdpto_Aprobar_Ceremonia = new d_coord_jdpto_Aprobar_Ceremonia();            
            $pendientesCeremonias = $obj_coord_jdpto_Aprobar_Ceremonia->Obtener_Total_Ceremonias_Por_Autorizar($id_usuario);
          
            $obj_coord_jdpto_Aprobar_Jurado = new d_coord_jdpto_Aprobar_Jurado();
            $pendientesJurados = $obj_coord_jdpto_Aprobar_Jurado->Obtener_Total_Jurados_Por_Autorizar($id_usuario);

            $totalpendientesPropuestas = $pendientesPropuestas['data']['registros'][0]['total1'];                        
            $jsondata['data']['registros'][0]['total1'] = $totalpendientesPropuestas;

            $totalpendientesJurados = $pendientesJurados['data']['registros'][0]['total1'];            
            $jsondata['data']['registros'][1]['total1'] = $totalpendientesJurados;
            
            $totalpendientesCeremonias = $pendientesCeremonias['data']['registros'][0]['total1'];                        
            $jsondata['data']['registros'][2]['total1'] = $totalpendientesCeremonias;
            
            
            echo json_encode($jsondata);
            exit();
        }
        catch (Exception $ex){               
           $jsondata['success'] = false;
           $jsondata['data']= array('message'=>$ex->getMessage());
           echo json_encode($jsondata);
           exit();                                                                    
        }          
    } //FIN PENDIENTES DEL COORDINADOR
    //*********************************************************************                
    
}

//$obj = new d_Usuario_Pendientes();
//echo $obj->Obtener_Pendientes_Coordinador(0, 'ELVA');
//$obj = new d_coord_jdpto_Aprobar_Propuesta();
//echo $obj->Obtener_Usr_Mail_Propuesta_JDefinitivo('2016-2-004', 4, 1, '086198517');
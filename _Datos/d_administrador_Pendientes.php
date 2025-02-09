<?php

header('Content-Type: text/html; charset=UTF-8');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_administrador_Aprobar_Servicio_Social.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_administrador_Aprobar_Reporte_Bimestral.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_administrador_Aprobar_Ceremonia.php');

class d_administrador_Pendientes
{
    function Obtener_Pendientes_Administrador($id_estatus, $id_division)
    {
        try {
            $jsondata['success'] = true;
            $jsondata['data']['message'] = '';
            $jsondata['data']['registros'] = array();

            $obj_administrador_Aprobar_Servicio_Social = new d_administrador_Aprobar_Servicio_Social();
            $pendientesServicio = $obj_administrador_Aprobar_Servicio_Social->Obtener_Total_SS($id_estatus, $id_division);

            $obj_administrador_Aprobar_Reporte_Bimestral  = new d_administrador_Aprobar_Reporte_Bimestral();
            $pendientesReportes = $obj_administrador_Aprobar_Reporte_Bimestral->Obtener_Total_Reportes_x_Autorizar($id_estatus, $id_division);

            $obj_administrador_Aprobar_Ceremonia = new d_administrador_Aprobar_Ceremonia();
            $pendientesCeremonias = $obj_administrador_Aprobar_Ceremonia->Obtener_Total_Ceremonias_Por_Autorizar($id_estatus, $id_division);

            $totalpendientesServicio = $pendientesServicio['data']['registros'][0]['total2'];
            $jsondata['data']['registros'][0]['total2'] = $totalpendientesServicio;

            $totalpendientesReportes = $pendientesReportes['data']['registros'][0]['total2'];
            $jsondata['data']['registros'][1]['total2'] = $totalpendientesReportes;

            $totalpendientesCeremonias = $pendientesCeremonias['data']['registros'][0]['total2'];
            $jsondata['data']['registros'][2]['total2'] = $totalpendientesCeremonias;

            echo json_encode($jsondata);
            exit();
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
}

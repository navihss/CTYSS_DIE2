<?php

/**
 * Definición de la Capa de Datos para autorizar las Cartasde Terminación
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */
header('Content-Type: text/html; charset=UTF-8');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');

class d_administrador_Aprobar_Carta_Terminacion
{

    //OBTENEMOS LAS HORAS REALIZADAS DE SERVICIO SOCIAL
    function Obtener_SS_Horas_Laboradas($id_estatus_ss, $id_division)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_alumno, a.id_carrera, a.id_ss,  a.id_programa, a.fecha_inicio_ss, a.duracion_meses_ss, 
                        b.descripcion_estatus, c.id_documento, c.id_version, d.descripcion_para_nom_archivo, e.email_usuario,
                        (SELECT sum(horas_obligatorias)
				FROM reportes_bimestrales
				WHERE id_estatus = 3 AND id_ss=a.id_ss) as horas_obligatorias,
			(SELECT sum(horas_laboradas)
        			FROM reportes_bimestrales
                                WHERE id_estatus = 3 AND id_ss= a.id_ss) as horas_laboradas, 
			((SELECT sum(horas_obligatorias)
				FROM reportes_bimestrales
				WHERE id_estatus = 3 AND id_ss=a.id_ss) - (SELECT sum(horas_laboradas)
                                FROM reportes_bimestrales
                                WHERE id_estatus = 3 AND id_ss= a.id_ss)) as horas_pendientes
                    FROM servicio_social a INNER JOIN estatus b ON a.id_estatus = b.id_estatus 
			INNER JOIN servicio_social_docs c ON a.id_ss = c.id_ss
                        INNER JOIN documentos d ON c.id_documento = d.id_documento
                        INNER JOIN usuarios e ON a.id_alumno = e.id_usuario
                    WHERE a.id_estatus = 3 AND c.id_documento = 3 AND c.id_estatus = ? AND a.id_division = ?
                    ORDER BY id_ss;";

            /* Valor de los parámetros. */
            $params = array($id_estatus_ss, $id_division);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            /*Verificamos el contenido de la ejecución*/
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['registros'] = array();

                        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                            $jsondata['data']['registros'][] = $row;
                        }
                        $stmt = null;
                        $conn = null;
                        echo json_encode($jsondata);
                        exit();
                    } else {
                        $jsondata['success'] = false;
                        $jsondata['data'] = array('message' => 'No hay se encontraron Cartas de Terminación por Autorizar.');
                        $stmt = null;
                        $conn = null;
                        echo json_encode($jsondata);
                        exit();
                    }
                }
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    } //Fin Obtener horas de Servicio Social

}

//$obj = new d_administrador_Aprobar_Carta_Terminacion();
//echo $obj->Obtener_SS_Horas_Laboradas(2);
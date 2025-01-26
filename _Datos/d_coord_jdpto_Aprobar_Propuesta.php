<?php
use App\Database\Connection;
/**
 * Definición de la Capa de Datos para la Clase Aprobar Propuesta
 * Métodos
 * @author ...
 * Julio 2016
 */

header('Content-Type: text/html; charset=UTF-8');
require_once ($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE2/_Datos/Conexion.php');
require_once ($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE2/_Datos/d_profesor_Mis_Propuestas.php');
require_once ($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE2/_Entidades/Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE2/_Datos/d_mail.php');
require_once ($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE2/_Entidades/Mail.php');

class d_coord_jdpto_Aprobar_Propuesta {

    function Traer_Indice($id_propuesta) {
        try {
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT indice 
                    FROM propuesta_tesis
                    WHERE id_propuesta = ?";

            $stmt = $conn->prepare($tsql);
            $params = array($id_propuesta);

            if ($stmt) {

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
                        $mensaje_Transacciones = "No hay registros";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la BD";
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }

    function Traer_Indice_Completo($id_propuesta) {
        try {
            $cnn = new Connection();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = 'SELECT objetivo,definicion_problema, metodo, temas_utilizar, resultados_esperados 
                    FROM propuesta_tesis
                    WHERE id_propuesta = ?';

            $stmt = $conn->prepare($tsql);
            $params = array($id_propuesta);

            if ($stmt) {

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
                        $mensaje_Transacciones = "No hay registros";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la BD";
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }

    //*********************************************************************                
    //OBTENEMOS LAS PROPUESTAS POR AUTORIZAR
    function Obtener_Documentos_Por_Autorizar($id_estatus, $id_usuario) {
        try {                    
            $cnn = new Connection();
            $conn = $cnn->getConexion();
    
            if ($conn === false) {
                throw new Exception($cnn->getError());
            }
    
            $tsql = " SELECT a.id_propuesta, a.id_documento, a.version_propuesta, a.id_estatus, b.descripcion_documento, 
                            b.descripcion_para_nom_archivo, c.descripcion_estatus, a.fecha_recepcion_doc, a.nota, 
                            d.id_profesor,d.titulo_propuesta, f.descripcion_tipo_propuesta,
                            (e.nombre_usuario || ' ' || e.apellido_paterno_usuario || ' ' || e.apellido_materno_usuario) as nombre,
                            g.id_usuario, e.email_usuario, d.titulo_propuesta, to_char(d.fecha_registrada,'YYYY/MM/DD') as fecha_registrada
                      FROM propuesta_version a
                            INNER JOIN documentos b ON a.id_documento = b.id_documento
                            INNER JOIN estatus c ON a.id_estatus = c.id_estatus
                            INNER JOIN propuestas_profesor d ON a.id_propuesta = d.id_propuesta
                            INNER JOIN tipos_propuesta f ON d.id_tipo_propuesta = f.id_tipo_propuesta
                            INNER JOIN propuesta_vobo g ON (a.id_propuesta = g.id_propuesta AND 
              							          a.id_documento = g.id_documento AND
              							          a.version_propuesta = g.version_propuesta)
  							            INNER JOIN usuarios e ON d.id_profesor = e.id_usuario
  									            AND a.id_division = g.id_division
                          			AND b.id_division = g.id_division
                          			AND d.id_division = g.id_division
                          			AND f.id_division = g.id_division
                          			AND g.id_division = g.id_division
                        WHERE g.id_estatus = ? AND g.id_usuario = ?
                        ORDER BY a.id_documento, a.fecha_recepcion_doc DESC;";
    
            $stmt = $conn->prepare($tsql);
            
            $params = array($id_estatus, $id_usuario);
    
            if($stmt) {        
                $result = $stmt->execute($params);                                 
                if ($result) {                    
                    if($stmt->rowCount() > 0) {                                                
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['registros'] = array();
    
                        while($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                            $jsondata['data']['registros'][] = $row;
                        }
                    } else {
                        $jsondata['success'] = false;
                        $jsondata['data']['message'] = 'No hay Propuestas por Autorizar.';
                    }
    
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($jsondata);
                    exit();
                }
            }
            throw new Exception("Error en la consulta SQL");
        }
        catch (Exception $ex) {        
            $errorMessage = "Error en: " . $ex->getFile() . " en la línea " . $ex->getLine() . "\n";
            $errorMessage .= "Mensaje: " . $ex->getMessage() . "\n";
            $errorMessage .= "Stack Trace: " . $ex->getTraceAsString() . "\n";
    
            // Puedes guardar el error en la sesión o en una variable global
            $_SESSION['error_message'] = $errorMessage;
                
            // Redirigir a una página de errores
            header('Location: ' . $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE2/error_page.php');
            
            exit();                                                                    
        }          
    }
    //FIN OBTENEMOS LAS PROPUESTAS POR AUTORIZAR
    //*********************************************************************                

    // Otras funciones permanecen sin cambios
    // ...
    // OBTENEMOS EL TOTAL DE DOCUMENTOS POR AUTORIZAR POR USUARIO
    function Obtener_Total_Documentos_Por_Autorizar($id_usuario) {
        try {
            $cnn = new Connection();
            $conn = $cnn->getConexion();
    
            if ($conn === false) {
                throw new Exception($cnn->getError());
            }
    
            $tsql = "SELECT COUNT(*) AS total
                     FROM propuesta_version a
                     INNER JOIN propuesta_vobo g ON (a.id_propuesta = g.id_propuesta AND 
                                                     a.id_documento = g.id_documento AND
                                                     a.version_propuesta = g.version_propuesta)
                     WHERE g.id_usuario = ? AND g.id_estatus = 9";
    
            $stmt = $conn->prepare($tsql);
            $params = array($id_usuario);
    
            if ($stmt) {
                $result = $stmt->execute($params);
    
                if ($result) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $total = $row ? $row['total'] : 0; // Asignar 0 si no hay registros
    
                    $jsondata['success'] = true;
                    $jsondata['data']['registros'][] = array("total1" => $total);
                    return $jsondata;
                }
            }
    
            // Manejo de errores en caso de consulta vacía o sin éxito
            $jsondata['success'] = true;
            $jsondata['data']['registros'][] = array("total1" => 0);
            return $jsondata;
    
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            return $jsondata;
        }
    }
    

    // Restante del código original

    // Crear método para revisar bitácora
}

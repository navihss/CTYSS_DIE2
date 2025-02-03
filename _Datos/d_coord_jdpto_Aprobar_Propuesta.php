<?php

/**
 * Definición de la Capa de Datos para la Clase Aprobar Propuesta
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */

header('Content-Type: text/html; charset=UTF-8');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_profesor_Mis_Propuestas.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_mail.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Mail.php');


class d_coord_jdpto_Aprobar_Propuesta
{

    function Traer_Indice($id_propuesta)
    {
        try {
            $cnn = new Conexion();
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


    function Traer_Indice_Completo($id_propuesta)
    {
        try {
            $cnn = new Conexion();
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
    function Obtener_Documentos_Por_Autorizar($id_estatus, $id_usuario)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
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

            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            $params = array($id_estatus, $id_usuario);
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
                    } else {
                        $jsondata['success'] = false;
                        $jsondata['data']['message'] = 'No hay Propuestas por Autorizar.';
                    }

                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($jsondata);
                    exit();
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener las Propuestas por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
            throw new Exception("Error en la consulta SQL");
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    } //FIN OBTENEMOS LAS PROPUESTAS POR AUTORIZAR
    //*********************************************************************                

    //OBTENEMOS TOTAL DE LAS PROPUESTAS POR AUTORIZAR
    function Obtener_Total_Documentos_Por_Autorizar($id_usuario)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            // Se agrega Division
            $tsql = "SELECT COUNT(*) AS total
                     FROM propuesta_version a
                     INNER JOIN documentos b ON a.id_documento = b.id_documento
                     INNER JOIN propuestas_profesor d ON a.id_propuesta = d.id_propuesta
                     INNER JOIN propuesta_vobo g ON (a.id_propuesta = g.id_propuesta AND 
                                                   a.id_documento = g.id_documento AND
                                                   a.version_propuesta = g.version_propuesta)
                     WHERE g.id_usuario = ? 
                     AND g.id_estatus = 9
                     AND a.id_division = g.id_division
                     AND b.id_division = g.id_division
                     AND d.id_division = g.id_division";
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            $params = array($id_usuario);
            /*Verificamos el contenido de la ejecución*/
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);

                if ($result) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $total = $row ? $row['total'] : 0;

                    $jsondata['success'] = true;
                    $jsondata['data']['registros'][] = array("total1" => $total);
                    return $jsondata;
                }
            }

            // Respuesta por defecto si no hay resultados
            $jsondata['success'] = true;
            $jsondata['data']['registros'][] = array("total1" => 0);
            return $jsondata;
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            return $jsondata;
        }
    }

    // Crear método para revisar bitácora
    /**
     * Obtiene el historial de una propuesta específica
     * @param int $id_propuesta ID de la propuesta a consultar
     * @return string JSON con el historial de la propuesta
     */
    function Obtener_Bitacora_Propuestas($id_propuesta)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT p.titulo_propuesta, 
                        pv.version_propuesta,
                        to_char(pv.fecha_recepcion_doc, 'YYYY/MM/DD') as fecha_generada,
                        e.descripcion_estatus,
                        pv.nota,
                        (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || 
                            u.apellido_materno_usuario) as profesor,
                        tp.descripcion_tipo_propuesta
                    FROM propuesta_version pv
                    INNER JOIN propuestas_profesor p ON pv.id_propuesta = p.id_propuesta
                    INNER JOIN estatus e ON pv.id_estatus = e.id_estatus
                    INNER JOIN usuarios u ON p.id_profesor = u.id_usuario
                    INNER JOIN tipos_propuesta tp ON p.id_tipo_propuesta = tp.id_tipo_propuesta
                    WHERE pv.id_propuesta = ?
                    ORDER BY pv.version_propuesta DESC, pv.fecha_recepcion_doc DESC";

            $stmt = $conn->prepare($tsql);
            $params = array($id_propuesta);

            if ($stmt) {
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['registros'] = array();

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $jsondata['data']['registros'][] = array(
                                'titulo_propuesta' => $row['titulo_propuesta'],
                                'version_propuesta' => $row['version_propuesta'],
                                'fecha_generada' => $row['fecha_generada'],
                                'descripcion_estatus' => $row['descripcion_estatus'],
                                'nota' => $row['nota'],
                                'profesor' => $row['profesor'],
                                'descripcion_tipo_propuesta' => $row['descripcion_tipo_propuesta']
                            );
                        }
                    } else {
                        throw new Exception("No se encontró historial para esta propuesta.");
                    }
                } else {
                    throw new Exception("Error al ejecutar la consulta.");
                }
            } else {
                throw new Exception("Error al preparar la consulta.");
            }

            if (ob_get_length()) ob_clean();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($jsondata);
            exit();
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            if (ob_get_length()) ob_clean();
            echo json_encode($jsondata);
            exit();
        }
    } //FIN OBTENEMOS TOTAL DE LAS PROPUESTAS POR AUTORIZAR
    //*********************************************************************                

    //*********************************************************************                
    //OBTENER CUANTOS COORD/DPTO FALTAN POR REVISAR LA PROPUESTA
    function N_Documentos_Por_Revisar($id_propuesta, $id_version)
    {
        $mensaje_Transacciones = 0;
        $conn = '';
        $resultado = '';
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT
                    (SELECT count(id_propuesta)
                    FROM propuesta_vobo
                    WHERE id_propuesta = ? AND version_propuesta = ? AND id_estatus = 9) as por_revisar,
                    (SELECT count(id_propuesta)
                    FROM propuesta_vobo
                    WHERE id_propuesta = ? AND version_propuesta = ?) as total_dar_vobo,
                    (SELECT count(id_propuesta)
                    FROM propuesta_vobo
                    WHERE id_propuesta = ? AND version_propuesta = ? AND id_estatus = 3) as aceptadas;";  //9. Por Autorizar Coord/Dpto 3. Aceptada
            /* Valor de los parámetros. */
            $params = array($id_propuesta, $id_version, $id_propuesta, $id_version, $id_propuesta, $id_version);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $resultado = ($row['por_revisar'] . ',' . $row['total_dar_vobo'] . ',' . $row['aceptadas']);
                        return $resultado;
                        $conn = null;
                        exit();
                    } else {
                        $mensaje_Transacciones = 0;
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = 0;
                throw new Exception($mensaje_Transacciones);
            }
            exit();
        } catch (Exception $ex) {
            $conn = null;
            return ('');
            exit();
        }
    }
    //FIN OBTENER CUANTOS COORD/DPTO FALTAN POR REVISAR LA PROPUESTA
    //*********************************************************************            

    //*********************************************************************                
    //OBTENER CORREOS Y USUARIOS DE UNA PROPUESTA
    function Obtener_Usr_Mail_Propuesta($id_propuesta, $id_documento, $id_version)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $resultado = '';
        $usuarios = '';
        $correos = '';

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT a.id_usuario, b.email_usuario
                    FROM propuesta_vobo a
                    INNER JOIN usuarios b ON a.id_usuario = b.id_usuario
                    WHERE a.id_propuesta = ? AND a.id_documento= ? AND a.version_propuesta = ?
                    UNION
                    SELECT c.id_usuario, c.email_usuario
                    FROM usuarios c
                    WHERE c.id_tipo_usuario in (2,3)";
            /* Valor de los parámetros. */
            $params = array($id_propuesta, $id_documento, $id_version);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $usuarios .= $row['id_usuario'] . ',';
                            $correos .= $row['email_usuario'] . ',';
                        }
                        $usuarios = substr($usuarios, 0, strlen($usuarios) - 1);
                        $correos = substr($correos, 0, strlen($correos) - 1);
                        $resultado = $usuarios . '|' . $correos;
                        return $resultado;
                        $conn = null;
                        exit();
                    } else {
                        $mensaje_Transacciones = '';
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = 0;
                throw new Exception($mensaje_Transacciones);
            }
            exit();
        } catch (Exception $ex) {
            $conn = null;
            return ('');
            exit();
        }
    }
    //FIN OBTENER CORREO Y USUARIOS
    //********************************************************************* 

    //*********************************************************************                
    //OBTENER CORREOS Y USUARIOS DE UNA PROPUESTA
    function Obtener_Usr_Mail_Propuesta_JDefinitivo($id_propuesta, $id_documento, $id_version, $id_usuario)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $resultado = '';
        $usuarios = '';
        $correos = '';

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            //COORDINADORES + 
            //(EL QUE PROPONE EL JURADO o ADMIN) +
            //PROFESOR +
            //ALUMNOS INSCRITOS
            $tsql = " SELECT a.id_usuario, b.email_usuario, (b.id_tipo_usuario) as id_tipo_usr
                            FROM propuesta_vobo a
                            INNER JOIN usuarios b ON a.id_usuario = b.id_usuario
                            WHERE a.id_propuesta = ?  AND a.id_documento= ?  AND a.version_propuesta = ?
                    UNION
                    SELECT c.id_usuario, c.email_usuario, (c.id_tipo_usuario) as id_tipo_usr
                            FROM usuarios c
                            WHERE (c.id_usuario = ? OR c.id_usuario = ?)
                    UNION
                    SELECT d.id_usuario, d.email_usuario, (d.id_tipo_usuario) as id_tipo_usr
                            FROM propuestas_profesor e
                            INNER JOIN usuarios d ON e.id_profesor = d.id_usuario
                            WHERE e.id_propuesta = ?
                    UNION
                    SELECT f.id_usuario, f.email_usuario, (f.id_tipo_usuario) as id_tipo_usr
                            FROM inscripcion_propuesta g
                            INNER JOIN usuarios f ON g.id_alumno = f.id_usuario
                            WHERE (g.id_propuesta = ? AND g.id_estatus = ?)
                    ORDER BY id_tipo_usr DESC";
            /* Valor de los parámetros. */
            $params = array($id_propuesta, $id_documento, $id_version, $id_usuario, 1, $id_propuesta, $id_propuesta, 3); //3. alumnos aceptados
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $usuarios .= $row['id_usuario'] . ',';
                            $correos .= $row['email_usuario'] . ',';
                        }
                        $usuarios = substr($usuarios, 0, strlen($usuarios) - 1);
                        $correos = substr($correos, 0, strlen($correos) - 1);
                        $resultado = $usuarios . '|' . $correos;
                        return $resultado;
                        $conn = null;
                        exit();
                    } else {
                        $mensaje_Transacciones = '';
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = 0;
                throw new Exception($mensaje_Transacciones);
            }
            exit();
        } catch (Exception $ex) {
            $conn = null;
            return ('');
            exit();
        }
    }
    //FIN OBTENER CORREO Y USUARIOS
    //********************************************************************* 
    //    
    //*********************************************************************                
    //ACTUALIZAMOS EL ESTATUS DE LA PROPUESTA
    function Actualizar_Aceptacion_Doc(
        $id_propuesta_doc,
        $id_documento_doc,
        $id_version_doc,
        $id_estatus,
        $id_usuario,
        $nota,
        $fecha_registro_doc,
        $id_profesor,
        $correo_profesor,
        $titulo_propuesta,
        $desc_corta_doc,
        $id_division
    ) {

        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $nombre_Profesor_Propuesta = '';

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            /*Iniciar la transacción. */
            $conn->beginTransaction();

            //OBTENEMOS EL NOMBRE DEL PROPIETARIO DE LA PROPUESTA PARA PONERLO COMO EL SINODAL 6
            $obj_Propuestas_Profesor = new d_profesor_Mis_Propuestas();
            $nombre_Profesor_Propuesta = $obj_Propuestas_Profesor->Obtener_Nombre_Profesor($id_propuesta_doc);

            //FIN OBTENEMOS EL NOMBRE DEL PROPIETARIO DE LA PROPUESTA PARA PONERLO COMO EL SINODAL 6

            //OBTENEMOS SI TODOS LOS COORD/DPTO YA REVISARON LA PROPUESTA
            $obj_ = new d_coord_jdpto_Aprobar_Propuesta();
            $estado_prop = $obj_->N_Documentos_Por_Revisar($id_propuesta_doc, $id_version_doc);
            if ($estado_prop == '') {
                $mensaje_Transacciones .= "No se pudo Obtener el Estado de la Propuesta.";
                throw new Exception($mensaje_Transacciones);
            }
            $arr_estado_prop = preg_split("/[,]/", $estado_prop); //0.por_revisar 1.total_dar_vobo 2.aceptadas
            $coord_dpto_por_revisar_doc = $arr_estado_prop[0];
            $coord_dpto_total_dar_voto = $arr_estado_prop[1];
            $coord_dpto_aceptadas = $arr_estado_prop[2];
            //FIN OBTENEMOS SI TODOS LOS COORD/DPTO YA REVISARON LA PROPUESTA
            //*********************************************************************            

            //OBTENEMOS CORREO Y USUARIO DE LOS COORD/JDPTO QUE REVISARON LA PROPUESTA
            $datos_prop = $obj_->Obtener_Usr_Mail_Propuesta($id_propuesta_doc, $id_documento_doc, $id_version_doc);
            if ($datos_prop == '') {
                $mensaje_Transacciones .= "No se pudo Obtener los Correos de Coordinadores/Jefes de Dpto.";
                throw new Exception($mensaje_Transacciones);
            }
            $arr_datos_prop = preg_split("/[|]/", $datos_prop);
            $arr_coord_dpto_id_usuarios = $arr_datos_prop[0];
            $arr_coord_dpto_correos = $arr_datos_prop[1];
            //FIN OBTENEMOS CORREO Y USUARIO DE LOS COORD/JDPTO QUE REVISARON LA PROPUESTA
            //*********************************************************************            

            //*********************************************************************                        
            //ACTUALIZAMOS LA ACEPTACION/RECHAZO DEL COORD/DPTO
            $tsql1 = " UPDATE propuesta_vobo SET " .
                "id_estatus =  ?, " .
                "fecha_revision = ?, " .
                "nota = ? " .
                "WHERE id_propuesta = ? AND " .
                "id_documento =? AND version_propuesta = ? AND id_usuario = ?;";


            /* Valor de los parámetros. */
            $params1 = array(
                $id_estatus,
                date('d-m-Y H:i:s'),
                $nota,
                $id_propuesta_doc,
                $id_documento_doc,
                $id_version_doc,
                $id_usuario
            );
            /* Preparamos la sentencia a ejecutar */
            $stmt1 = $conn->prepare($tsql1);

            if ($stmt1) {
                /*Ejecutamos el Query*/
                $result1 = $stmt1->execute($params1);

                if ($result1) {
                    if ($stmt1->rowCount() > 0) {
                        $mensaje_Transacciones .= "VoBo del Usuario Actualizado.<br/>";
                    } else {
                        $error = $stmt1->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error1";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt1->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un error2";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt1->errorInfo();
                $mensaje_Transacciones = "Ocurrió un error3";
                throw new Exception($mensaje_Transacciones);
            }

            //FIN ACTUALIZAMOS LA ACEPTACION/RECHAZO DEL COORD/DPTO
            //*********************************************************************            

            $nvo_Estatus = $id_estatus;
            $propuesta_Revisada = false;

            $coord_dpto_por_revisar_doc -= 1;

            if ($id_estatus == 3) { //3.Aceptada
                $coord_dpto_aceptadas += 1;
            }

            if ($coord_dpto_aceptadas == $coord_dpto_total_dar_voto) { //Todos Aceptaron la propuesta
                $nvo_Estatus = 3;    //Aceptada
                $propuesta_Revisada = true;
            } elseif ($coord_dpto_por_revisar_doc == 0 && $coord_dpto_aceptadas != $coord_dpto_total_dar_voto) {   //Todos ya revisaron y al menos uno Rechazo la propuesta
                $nvo_Estatus = 4;   //Rechazada
                $propuesta_Revisada = true;
            }

            //*********************************************************************   
            //Actualizamos la versión de la Propuesta
            //Todos Ya Revisaron la propuesta
            if ($propuesta_Revisada) {
                /* Query parametrizado. */
                $tsql2 = " UPDATE propuesta_version SET " .
                    "id_estatus =  ? " .
                    "WHERE id_propuesta = ? AND " .
                    "id_documento =? AND version_propuesta = ?;";
                /* Valor de los parámetros. */
                $params2 = array($nvo_Estatus, $id_propuesta_doc, $id_documento_doc, $id_version_doc);
                /* Preparamos la sentencia a ejecutar */
                $stmt2 = $conn->prepare($tsql2);
                if ($stmt2) {
                    /*Ejecutamos el Query*/
                    $result2 = $stmt2->execute($params2);
                    if ($result2) {
                        if ($stmt2->rowCount() > 0) {
                            $mensaje_Transacciones .= "Estatus Actualizado de la Propuesta_Versión. OK.<br/>";
                        } else {
                            $error = $stmt2->errorInfo();
                            $mensaje_Transacciones = "Ocurrió un error4";
                            throw new Exception($mensaje_Transacciones);
                        }
                    } else {
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error5";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un error6";
                    throw new Exception($mensaje_Transacciones);
                }
                //FIN Actualizamos la versión de la Propuesta
                //*********************************************************************

                //*********************************************************************
                //Actualizamos la Propuesta solamente si todos aceptaron la propuesta_version
                if ($nvo_Estatus == 3) {   //3. Aceptado


                    //*********************************************************************
                    //AGREGAMOS UN JURADO PARA LA PROPUESTA
                    $tsql5 = " INSERT INTO jurado(id_propuesta, version, id_estatus) VALUES(" .
                        "?, ?, ?);";
                    /* Valor de los parámetros. */
                    $params5 = array($id_propuesta_doc, $id_version_doc, 11);
                    /* Preparamos la sentencia a ejecutar */
                    $stmt5 = $conn->prepare($tsql5);
                    if ($stmt5) {
                        /*Ejecutamos el Query*/
                        $result5 = $stmt5->execute($params5);
                        if ($result5) {
                            if ($stmt5->rowCount() > 0) {
                                $mensaje_Transacciones .= "Jurado Agregado. OK.<br/>";
                            } else {
                                $error = $stmt5->errorInfo();
                                $mensaje_Transacciones = "Ocurrió un error7";
                                throw new Exception($mensaje_Transacciones);
                            }
                        } else {
                            $error = $stmt5->errorInfo();
                            $mensaje_Transacciones = "Ocurrió un error8";
                            throw new Exception($mensaje_Transacciones);
                        }
                    } else {
                        $error = $stmt5->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error9";
                        throw new Exception($mensaje_Transacciones);
                    }
                    //FIN AGREGAMOS UN JURADO PARA LA PROPUESTA
                    //*********************************************************************

                    //*********************************************************************
                    //AGREGAMOS 5 SINODALES SIN NOMBRE PARA EL JURADO QUE DEFINIRÁ EL ALUMNO
                    //EL 6 SERA PARA EL SINODAL
                    $tsql6 = " INSERT INTO sinodales(id_propuesta, version, num_profesor, nombre_sinodal_propuesto) VALUES(" .
                        "?, ?, ?, ?);";
                    //Se cambia para agregar 4 Sinodales en vez de 5
                    for ($i = 1; $i <= 5; $i++) {
                        $stmt6 = $conn->prepare($tsql6);
                        if ($stmt6) {
                            /*Ejecutamos el Query*/
                            //El último sinodal es el profesor de la propuesta.
                            if ($i == 5) {
                                $params6 = array($id_propuesta_doc, $id_version_doc, $i, $nombre_Profesor_Propuesta);
                            } else {
                                $params6 = array($id_propuesta_doc, $id_version_doc, $i, '');
                            }

                            $result6 = $stmt6->execute($params6);
                            if ($result6) {
                                if ($stmt6->rowCount() > 0) {
                                    $mensaje_Transacciones .= "Sinodal " . $i . " Agregado. OK.<br/>";

                                    //AGREGAMOS A LOS COORD. VoBo.
                                    $tsql7 = " INSERT INTO jurado_vobo(id_propuesta, version, num_profesor, id_usuario, id_estatus) 
                                             SELECT ?, ?, ?, id_usuario, ?
                                                FROM propuesta_vobo
                                                WHERE id_propuesta= ? AND id_documento=4 AND version_propuesta= ?
                                                ORDER BY id_usuario;";
                                    /* Valor de los parámetros. */
                                    $params7 = array(
                                        $id_propuesta_doc,
                                        $id_version_doc,
                                        $i,
                                        11,
                                        $id_propuesta_doc,
                                        $id_version_doc
                                    );
                                    /* Preparamos la sentencia a ejecutar */
                                    $stmt7 = $conn->prepare($tsql7);
                                    if ($stmt7) {
                                        /*Ejecutamos el Query*/
                                        $result7 = $stmt7->execute($params7);
                                        if ($result7) {
                                            if ($stmt7->rowCount() > 0) {
                                                $mensaje_Transacciones .= "Sinodales Agregados. OK.<br/>";
                                            } else {
                                                $error = $stmt7->errorInfo();
                                                $mensaje_Transacciones = "Ocurrió un error10";
                                                throw new Exception($mensaje_Transacciones);
                                            }
                                        } else {
                                            $error = $stmt7->errorInfo();
                                            $mensaje_Transacciones = "Ocurrió un error11";
                                            throw new Exception($mensaje_Transacciones);
                                        }
                                    } else {
                                        $error = $stmt7->errorInfo();
                                        $mensaje_Transacciones = "Ocurrió un error12";
                                        throw new Exception($mensaje_Transacciones);
                                    }
                                    //FINAGREGAMOS A LOS COORD. VoBo.

                                } else {
                                    $error = $stmt6->errorInfo();
                                    $mensaje_Transacciones = "Ocurrió un error13";
                                    throw new Exception($mensaje_Transacciones);
                                }
                            } else {
                                $error = $stmt6->errorInfo();
                                $mensaje_Transacciones = "Ocurrió un error14";
                                throw new Exception($mensaje_Transacciones);
                            }
                        } else {
                            $error = $stmt6->errorInfo();
                            $mensaje_Transacciones = "Ocurrió un error15";
                            throw new Exception($mensaje_Transacciones);
                        }
                    }
                    //FIN AGREGAMOS 5 SINODALES SIN NOMBRE PARA EL JURADO QUE DEFINIRÁ EL ALUMNO
                    //*********************************************************************

                    //*********************************************************************
                    //ASIGNAMOS UNA CLAVE DEFINITIVA PARA LA PROPUESTA
                    //Obtenemos la nueva clave para la propuesta ACEPTADA
                    $fecha_p = strtotime($fecha_registro_doc);
                    $fecha_p = date('d-m-Y', $fecha_p);
                    $anio = date("Y", strtotime($fecha_p));
                    $mes = date("m", strtotime($fecha_p));
                    $semestre = 1;
                    if ($mes > 6) {
                        $semestre = 2;
                    }

                    /* Query para obtener el consecutivo PROVISIONAL del Servicio Social */
                    $tsql10 = " SELECT consecutivo + 1 as siguiente_digito
                            FROM propuesta_profesor_contador
                            WHERE anio = ? AND semestre = ?;
                        ";
                    /* Valor de los parámetros. */
                    $params10 = array($anio, $semestre);
                    /* Preparamos la sentencia a ejecutar */
                    $stmt10 = $conn->prepare($tsql10);
                    $result10 = $stmt10->execute($params10);
                    $clave_Propuesta = '';
                    $nuevoConsecutivo = 0;

                    if ($stmt10) {
                        /*Ejecutamos el Query*/
                        $result10 = $stmt10->execute($params10);
                        if ($result10) {
                            if ($stmt10->rowCount() > 0) {
                                $row = $stmt10->fetch(PDO::FETCH_ASSOC);
                                $nuevoConsecutivo = $row['siguiente_digito'];
                                $consecutivo = str_pad($row['siguiente_digito'], 3, "0", STR_PAD_LEFT);
                                $clave_Propuesta = $anio . "-" . $semestre . "-" . $consecutivo;
                            } else {
                                $error = $stmt10->errorInfo();
                                $mensaje_Transacciones = "Ocurrió un error16";
                                throw new Exception($mensaje_Transacciones);
                            }
                        } else {
                            $error = $stmt10->errorInfo();
                            $mensaje_Transacciones = "Ocurrió un error17";
                            throw new Exception($mensaje_Transacciones);
                        }
                    }
                    $mensaje_Transacciones .= "Clave Definitiva de la Propuesta obtenida. OK.<br/>";

                    //Actualizamos la Propuesta con su Clave DEFINITIVA y estatus ACEPTADO
                    /* Query parametrizado. */
                    $fecha = date('Y-m-j');
                    $nuevafecha = strtotime('+1 year', strtotime($fecha));
                    $nuevafecha = date('Y-m-j', $nuevafecha);

                    $tsql = " UPDATE propuestas_profesor SET " .
                        "id_propuesta = ?, " .
                        "id_estatus =  ?, " .
                        "fecha_aceptacion = ?, " .
                        "fecha_vigencia = ? " .
                        "WHERE id_propuesta = ?;";
                    /* Valor de los parámetros. */
                    $params = array($id_propuesta_doc, 3, $fecha, $nuevafecha, $id_propuesta_doc);  //3. Aceptado
                    /* Preparamos la sentencia a ejecutar */
                    $stmt = $conn->prepare($tsql);

                    if ($stmt) {
                        /*Ejecutamos el Query*/
                        $result = $stmt->execute($params);
                        if ($result) {
                            if ($stmt->rowCount() > 0) {
                                $mensaje_Transacciones .= "Estatus de la Propuesta Actualizado. OK.<br/>";
                            } else {
                                $error = $stmt->errorInfo();
                                $mensaje_Transacciones = "Ocurrió un error18";
                                throw new Exception($mensaje_Transacciones);
                            }
                        } else {
                            $error = $stmt->errorInfo();
                            $mensaje_Transacciones = "Ocurrió un error19";
                            throw new Exception($mensaje_Transacciones);
                        }
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error20";
                        throw new Exception($mensaje_Transacciones);
                    }

                    //Incrementamos el Contador para la Propuesta en la BD
                    /* Query parametrizado para el incremento del Contador mensual de Propuestas. */
                    $tsql5 = " UPDATE propuesta_profesor_contador SET
                            consecutivo = " . $nuevoConsecutivo . " " .
                        "WHERE anio = ?  AND semestre = ?;";
                    /* Valor de los parámetros. */
                    $params5 = array($anio, $semestre);
                    /* Preparamos la sentencia a ejecutar */
                    $stmt5 = $conn->prepare($tsql5);
                    if ($stmt5) {
                        /*Ejecutamos el Query*/
                        $result5 = $stmt5->execute($params5);
                        if ($result5) {
                            if ($stmt5->rowCount() > 0) {
                                $mensaje_Transacciones .= "Contador PROVISIONAL. OK.<br/>";
                            } else {
                                $error = $stmt5->errorInfo();
                                $mensaje_Transacciones = "Ocurrió un error21";
                                throw new Exception($mensaje_Transacciones);
                            }
                        } else {
                            $error = $stmt5->errorInfo();
                            $mensaje_Transacciones = "Ocurrió un error22";
                            throw new Exception($mensaje_Transacciones);
                        }
                    } else {
                        $error = $stmt5->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error23";
                        throw new Exception($mensaje_Transacciones);
                    }
                    $nom_archivo_actual = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Docs/Propuestas_Profesor/' .
                        $id_profesor . '_' . $id_propuesta_doc . '_' .
                        $id_version_doc . '_' . $desc_corta_doc . '.pdf';
                    $nom_archivo_nuevo = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Docs/Propuestas_Profesor/' .
                        $id_profesor . '_' . $clave_Propuesta . '_' .
                        $id_version_doc . '_' . $desc_corta_doc . '.pdf';

                    if (file_exists($nom_archivo_actual)) {
                        rename($nom_archivo_actual, $nom_archivo_nuevo);
                    }

                    //FIN ASIGNAMOS UNA CLAVE DEFINITIVA PARA LA PROPUESTA
                    //*********************************************************************
                }

                //*********************************************************************
                //SI YA FUE REVISADA POR TODOS Y FUE RECHAZADA AGREGAMOS UNA NUEVA VERSION
                if ($nvo_Estatus == 4) { //4. Rechazado
                    /* Query parametrizado. */
                    $tsql4 = " INSERT INTO propuesta_version(id_propuesta, id_documento, version_propuesta,
                            fecha_generada, nota, id_estatus) VALUES(
                            ?,?,?,?,?,?);";

                    /* Valor de los parámetros. */
                    $params4 = array(
                        $id_propuesta_doc,
                        $id_documento_doc,
                        $id_version_doc + 1,
                        date('d-m-Y H:i:s'),
                        '',
                        1
                    ); //1. Sin Enviar
                    /* Preparamos la sentencia a ejecutar */
                    $stmt4 = $conn->prepare($tsql4);
                    if ($stmt4) {
                        /*Ejecutamos el Query*/
                        $result4 = $stmt4->execute($params4);
                        if ($result4) {
                            if ($stmt4->rowCount() > 0) {
                                $mensaje_Transacciones .= "Nuevo Documento Agregado para su Reenvío. OK.<br/>";
                            } else {
                                $error = $stmt4->errorInfo();
                                $mensaje_Transacciones = "Ocurrió un error24";
                                throw new Exception($mensaje_Transacciones);
                            }
                        } else {
                            $error = $stmt4->errorInfo();
                            $mensaje_Transacciones = "Ocurrió un error25";
                            throw new Exception($mensaje_Transacciones);
                        }
                    } else {
                        $error = $stmt4->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error26";
                        throw new Exception($mensaje_Transacciones);
                    }
                    //BORRAMOS EL ARCHIVO PDF
                    $archivo_pdf = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Docs/Propuestas_Profesor/' .
                        $id_profesor . '_' . $id_propuesta_doc . '_' .
                        $id_version_doc . '_' . $desc_corta_doc . '.pdf';

                    if (file_exists($archivo_pdf)) {
                        unlink($archivo_pdf);
                    }
                }
                //FIN SI YA FUE REVISADA POR TODOS Y FUE RECHAZADA AGREGAMOS UNA NUEVA VERSION
                //*********************************************************************
            }

            $conn->commit();
            if ($nvo_Estatus == 4) {
                $mensaje_Transacciones = "Propuesta rechazada correctamente";
            } else {
                $mensaje_Transacciones = "Propuesta aceptada correctamente";
            }
            //CONFIGURAMOS PARA LA BITACORA Y CORREOS
            //MOVIMIENTO DEL COORD/JDPTO
            $respuesta_mail = '';
            $id_tema_evento1 = 80; // Aprobar Propuestas
            $id_tipo_evento1 = 0;
            $descripcion_evento1 = '';
            //AGREGAMOS A LA BITÁCORA LA NOTA DEL COORD/JDPTO
            //3. aceptado 25. aprobada 30. rechazado
            if ($id_estatus == 3) {
                $id_tipo_evento1 = 25;
                $descripcion_evento1 = "La Propuesta No. " . $id_propuesta_doc . " Versión " . $id_version_doc .
                    " ** Del Profesor " . $id_profesor . " ** Con Título " . $titulo_propuesta . " Con VoBo Aceptable. *** " . $nota;
            } else {
                $id_tipo_evento1 = 30; // 
                $descripcion_evento1 = "La Propuesta No. " . $id_propuesta_doc . " Versión " . $id_version_doc .
                    " ** Del Profesor " . $id_profesor . " ** Con Título " . $titulo_propuesta . " Con VoBoh No Aceptable. *** " . $nota;
            }

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_evento1;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento1);
            $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento1);
            $obj_miBitacora->set_Id_Usuario_Genera($id_usuario);
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora = '';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);


            if ($propuesta_Revisada) {
                //MOVIMIENTO DE ACEPTACION O RECHAZO DE LA PROPUESTA EN GRAL
                $id_tema_evento2 = 115; // Asignar Propuesta a Coord/JDpto
                $id_tipo_evento2 = 0;
                $descripcion_evento2 = '';
                $descripcion_correo2 = '';
                //AGREGAMOS A LA BITÁCORA LA ACEPTACION/RECHAZO CON DESTINATARIO AL PROFESOR
                //3. aceptado 25. aprobada 30. rechazado
                if ($nvo_Estatus == 3) {
                    $id_tipo_evento2 = 25;
                    $descripcion_evento2 = "La Propuesta No. " . $id_propuesta_doc . " Versión " . $id_version_doc .
                        " ** Del Profesor " . $id_profesor . " ** Con Título " . $titulo_propuesta . " ha sido ACEPTADA " .
                        " ** Clave definitiva asignada " . $clave_Propuesta .
                        " !!! RECUERDE QUE SU PROPUESTA DEBE DE ESTAR CON EL ATRIBUTO -ACEPTAR INSCRIPCIONES- PARA QUE LOS ALUMNOS PUEDAN INSCRIBIRSE !!!.";
                    $descripcion_correo2 = "La Propuesta No. " . $id_propuesta_doc . " Versión " . $id_version_doc .
                        "<br> ** Del Profesor " . $id_profesor . "<br> ** Con Título " . $titulo_propuesta . " ha sido ACEPTADA " .
                        "<br> ** Clave temporal anterior " . $id_propuesta_doc .
                        "<br> ** Clave definitiva asignada " . $clave_Propuesta .
                        "<br> !!! RECUERDE QUE SU PROPUESTA DEBE DE ESTAR CON EL ATRIBUTO -ACEPTAR INSCRIPCIONES- PARA QUE LOS ALUMNOS PUEDAN INSCRIBIRSE !!!.";
                } else {
                    $id_tipo_evento2 = 30; // 
                    $descripcion_evento2 = "La Propuesta No. " . $id_propuesta_doc . " Versión " . $id_version_doc .
                        " ** Del Profesor " . $id_profesor . " ** Con Título " . $titulo_propuesta . " ha sido RECHAZADA. ** REVISAR las NOTAS Correspondientes.. " .
                        " ** Se ha generando una nueva versión del documento para que pueda enviarlo nuevamente.";
                    $descripcion_correo2 = "La Propuesta No. " . $id_propuesta_doc . " Versión " . $id_version_doc .
                        "<br> ** Del Profesor " . $id_profesor . "<br> ** Con Título " . $titulo_propuesta . " ha sido RECHAZADA. <br>** REVISAR las NOTAS Correspondientes.. " .
                        "<br> ** Se ha generando una nueva versión del documento para que pueda enviarlo nuevamente.";
                }

                $destinatarios_bitacora = $arr_coord_dpto_id_usuarios . "," . $id_profesor;
                $destinatarios_correo = $arr_coord_dpto_correos . "," . $correo_profesor;

                $arr_destinatarios_bitacora = preg_split("/[,]/", $destinatarios_bitacora);

                $renglones = count($arr_destinatarios_bitacora);

                for ($i = 0; $i < $renglones; $i++) {
                    sleep(1);
                    $obj_miBitacora = new Bitacora();

                    $descripcionEvento = $descripcion_evento2;
                    $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                    $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento2);
                    $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento2);
                    $obj_miBitacora->set_Id_Usuario_Genera(1);
                    $obj_miBitacora->set_Id_Usuario_Destinatario($arr_destinatarios_bitacora[$i]);
                    $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                    $obj_miBitacora->set_Id_Division($id_division);

                    $resultado_Bitacora = '';
                    $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);

                    sleep(1);
                    $obj_miBitacora = new Bitacora();

                    $descripcionEvento = $descripcion_correo2;
                    $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                    $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento2);
                    $obj_miBitacora->set_Id_Tipo_Evento(50);
                    $obj_miBitacora->set_Id_Usuario_Genera(1);
                    $obj_miBitacora->set_Id_Usuario_Destinatario($arr_destinatarios_bitacora[$i]);
                    $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                    $obj_miBitacora->set_Id_Division($id_division);

                    $resultado_Bitacora = '';
                    $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
                }

                //Mandamos los correos
                $obj = new d_mail();
                $mi_mail = new Mail();
                $mensaje = $descripcion_correo2;

                $mi_mail->set_Correo_Destinatarios($correo_profesor);
                $mi_mail->set_Asunto('AVISO APROBACIÓN DE PROPUESTA');
                $mi_mail->set_Mensaje($mensaje);

                $mi_mail->set_Correo_Copia_Oculta($destinatarios_correo);

                $respuesta_mail = $obj->Envair_Mail($mi_mail);
            }

            //            $conn->rollBack();
            //            $conn = null;
            //            $jsondata['success'] = false;
            //            $jsondata['data']['message'] = $descripcion_evento1 . $resultado_Bitacora;
            //            echo json_encode($jsondata);
            //            exit();  

            $conn = null;
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora;
            echo json_encode($jsondata);
            exit();
        } catch (Exception $ex) {
            $conn->rollBack();
            $conn = null;
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN ACTUALIZAMOS EL ESTATUS DE LA PROPUESTA


}

//$obj = new d_coord_jdpto_Aprobar_Propuesta();
//print_r($obj->Obtener_Total_Documentos_Por_Autorizar('ELVA'));
//echo $obj->Obtener_Usr_Mail_Propuesta_JDefinitivo('2016-2-004', 4, 1, '086198517');

//$obj_ = new d_profesor_Mis_Propuestas();
//$nom = $obj_->Obtener_Nombre_Profesor('4');
//echo $nom;

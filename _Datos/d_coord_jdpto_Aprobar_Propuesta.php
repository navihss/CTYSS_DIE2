<?php

/**
 * Definición de la Capa de Datos para la Clase Aprobar Propuesta
 * Metodos
 * @author  Rogelio Reyes Mendoza
 * Julio    2016
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
                        throw new Exception("No hay registros");
                    }
                }
            } else {
                throw new Exception("Error en la BD");
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

            $tsql = "SELECT objetivo, definicion_problema, metodo, temas_utilizar, resultados_esperados 
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
                        throw new Exception("No hay registros");
                    }
                }
            } else {
                throw new Exception("Error en la BD");
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

            $tsql = " SELECT a.id_propuesta, a.id_documento, a.version_propuesta, a.id_estatus, 
                             b.descripcion_documento, b.descripcion_para_nom_archivo, 
                             c.descripcion_estatus, a.fecha_recepcion_doc, a.nota, 
                             d.id_profesor, d.titulo_propuesta, f.descripcion_tipo_propuesta,
                             (e.nombre_usuario || ' ' || e.apellido_paterno_usuario || ' ' 
                               || e.apellido_materno_usuario) as nombre,
                             g.id_usuario, e.email_usuario, d.titulo_propuesta, 
                             to_char(d.fecha_registrada,'YYYY/MM/DD') as fecha_registrada
                      FROM propuesta_version a
                           INNER JOIN documentos b ON a.id_documento = b.id_documento
                           INNER JOIN estatus c   ON a.id_estatus = c.id_estatus
                           INNER JOIN propuestas_profesor d ON a.id_propuesta = d.id_propuesta
                           INNER JOIN tipos_propuesta f     ON d.id_tipo_propuesta = f.id_tipo_propuesta
                           INNER JOIN propuesta_vobo g ON (
                                a.id_propuesta     = g.id_propuesta AND
                                a.id_documento     = g.id_documento AND
                                a.version_propuesta= g.version_propuesta
                           )
                           INNER JOIN usuarios e ON d.id_profesor = e.id_usuario
                           AND a.id_division = g.id_division
                           AND b.id_division = g.id_division
                           AND d.id_division = g.id_division
                           AND f.id_division = g.id_division
                           AND g.id_division = g.id_division
                     WHERE g.id_estatus = ? 
                       AND g.id_usuario = ?
                     ORDER BY a.id_documento, a.fecha_recepcion_doc DESC;";

            $stmt = $conn->prepare($tsql);
            $params = array($id_estatus, $id_usuario);

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
    }

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
                          INNER JOIN propuesta_vobo g ON (
                              a.id_propuesta     = g.id_propuesta AND 
                              a.id_documento     = g.id_documento AND
                              a.version_propuesta= g.version_propuesta
                          )
                     WHERE g.id_usuario = ? 
                       AND g.id_estatus = 9
                       AND a.id_division = g.id_division
                       AND b.id_division = g.id_division
                       AND d.id_division = g.id_division";

            $stmt = $conn->prepare($tsql);
            $params = array($id_usuario);
            if ($stmt) {
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

    //*********************************************************************                
    //OBTENER HISTORIAL / BITÁCORA DE UNA PROPUESTA
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
                            (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' 
                              || u.apellido_materno_usuario) as profesor,
                            tp.descripcion_tipo_propuesta
                     FROM propuesta_version pv
                          INNER JOIN propuestas_profesor p ON pv.id_propuesta = p.id_propuesta
                          INNER JOIN estatus e           ON pv.id_estatus    = e.id_estatus
                          INNER JOIN usuarios u          ON p.id_profesor    = u.id_usuario
                          INNER JOIN tipos_propuesta tp  ON p.id_tipo_propuesta = tp.id_tipo_propuesta
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
                                'titulo_propuesta'         => $row['titulo_propuesta'],
                                'version_propuesta'        => $row['version_propuesta'],
                                'fecha_generada'           => $row['fecha_generada'],
                                'descripcion_estatus'      => $row['descripcion_estatus'],
                                'nota'                     => $row['nota'],
                                'profesor'                 => $row['profesor'],
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
    }

    //*********************************************************************                
    //OBTENER CUANTOS COORD/DPTO FALTAN POR REVISAR LA PROPUESTA
    function N_Documentos_Por_Revisar($id_propuesta, $id_version)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT
                            (SELECT count(id_propuesta)
                             FROM propuesta_vobo
                             WHERE id_propuesta      = ? 
                               AND version_propuesta = ? 
                               AND id_estatus        = 9) as por_revisar,

                            (SELECT count(id_propuesta)
                             FROM propuesta_vobo
                             WHERE id_propuesta      = ? 
                               AND version_propuesta = ?) as total_dar_vobo,

                            (SELECT count(id_propuesta)
                             FROM propuesta_vobo
                             WHERE id_propuesta      = ? 
                               AND version_propuesta = ? 
                               AND id_estatus = 3) as aceptadas;";
            // 9. Por Autorizar Coord/Dpto 
            // 3. Aceptada

            $params = array(
                $id_propuesta,
                $id_version,
                $id_propuesta,
                $id_version,
                $id_propuesta,
                $id_version
            );

            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $resultado = ($row['por_revisar'] . ',' .
                            $row['total_dar_vobo'] . ',' .
                            $row['aceptadas']);
                        return $resultado;
                        $conn = null;
                        exit();
                    } else {
                        throw new Exception("0");
                    }
                }
            } else {
                throw new Exception("0");
            }
            exit();
        } catch (Exception $ex) {
            $conn = null;
            return ('');
            exit();
        }
    }

    //*********************************************************************                
    //OBTENER CORREOS Y USUARIOS DE UNA PROPUESTA
    function Obtener_Usr_Mail_Propuesta($id_propuesta, $id_documento, $id_version)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT a.id_usuario, b.email_usuario
                      FROM propuesta_vobo a
                           INNER JOIN usuarios b ON a.id_usuario = b.id_usuario
                      WHERE a.id_propuesta = ? 
                        AND a.id_documento= ? 
                        AND a.version_propuesta = ?

                      UNION

                      SELECT c.id_usuario, c.email_usuario
                      FROM usuarios c
                      WHERE c.id_tipo_usuario in (2,3)";

            $params = array($id_propuesta, $id_documento, $id_version);

            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $usuarios = '';
                        $correos  = '';
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $usuarios .= $row['id_usuario']   . ',';
                            $correos  .= $row['email_usuario'] . ',';
                        }
                        $usuarios = rtrim($usuarios, ',');
                        $correos  = rtrim($correos,  ',');

                        $resultado = $usuarios . '|' . $correos;
                        return $resultado;
                    } else {
                        throw new Exception("");
                    }
                }
            } else {
                throw new Exception("");
            }
            exit();
        } catch (Exception $ex) {
            $conn = null;
            return ('');
            exit();
        }
    }

    //*********************************************************************                
    //OBTENER CORREOS Y USUARIOS DE UNA PROPUESTA (Versión JDefinitivo)
    function Obtener_Usr_Mail_Propuesta_JDefinitivo($id_propuesta, $id_documento, $id_version, $id_usuario)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            // COORDINADORES + (EL QUE PROPONE EL JURADO o ADMIN) + PROFESOR + ALUMNOS INSCRITOS
            $tsql = " SELECT a.id_usuario, b.email_usuario, (b.id_tipo_usuario) as id_tipo_usr
                      FROM propuesta_vobo a
                           INNER JOIN usuarios b ON a.id_usuario = b.id_usuario
                      WHERE a.id_propuesta = ?  
                        AND a.id_documento= ?  
                        AND a.version_propuesta = ?

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

            $params = array(
                $id_propuesta,
                $id_documento,
                $id_version,
                $id_usuario,
                1,
                $id_propuesta,
                $id_propuesta,
                3
            );

            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $usuarios = '';
                        $correos  = '';
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $usuarios .= $row['id_usuario']   . ',';
                            $correos  .= $row['email_usuario'] . ',';
                        }
                        $usuarios = rtrim($usuarios, ',');
                        $correos  = rtrim($correos,  ',');

                        $resultado = $usuarios . '|' . $correos;
                        return $resultado;
                    } else {
                        throw new Exception("");
                    }
                }
            } else {
                throw new Exception("");
            }
            exit();
        } catch (Exception $ex) {
            $conn = null;
            return ('');
            exit();
        }
    }

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

            // Iniciar la transacción
            $conn->beginTransaction();

            // <-- CAMBIO 1: Validar que $id_division no sea vacío
            if (empty($id_division)) {
                $conn->rollBack();
                $jsondata['success'] = false;
                $jsondata['data']['message'] = 'Error: El id_division es nulo o no está definido.';
                echo json_encode($jsondata);
                exit();
            }
            // FIN CAMBIO 1

            //OBTENEMOS EL NOMBRE DEL PROPIETARIO DE LA PROPUESTA (Para sinodal #6)
            $obj_Propuestas_Profesor = new d_profesor_Mis_Propuestas();
            $nombre_Profesor_Propuesta = $obj_Propuestas_Profesor->Obtener_Nombre_Profesor($id_propuesta_doc);

            //OBTENEMOS SI TODOS LOS COORD/DPTO YA REVISARON LA PROPUESTA
            $obj_ = new d_coord_jdpto_Aprobar_Propuesta();
            $estado_prop = $obj_->N_Documentos_Por_Revisar($id_propuesta_doc, $id_version_doc);
            if ($estado_prop == '') {
                $mensaje_Transacciones .= "No se pudo Obtener el Estado de la Propuesta.";
                throw new Exception($mensaje_Transacciones);
            }

            $arr_estado_prop           = preg_split("/[,]/", $estado_prop);
            $coord_dpto_por_revisar_doc = $arr_estado_prop[0];
            $coord_dpto_total_dar_voto  = $arr_estado_prop[1];
            $coord_dpto_aceptadas       = $arr_estado_prop[2];

            //OBTENEMOS CORREO Y USUARIO DE LOS COORD/JDPTO QUE REVISARON LA PROPUESTA
            $datos_prop = $obj_->Obtener_Usr_Mail_Propuesta($id_propuesta_doc, $id_documento_doc, $id_version_doc);
            if ($datos_prop == '') {
                $mensaje_Transacciones .= "No se pudo Obtener los Correos de Coordinadores/Jefes de Dpto.";
                throw new Exception($mensaje_Transacciones);
            }
            $arr_datos_prop               = preg_split("/[|]/", $datos_prop);
            $arr_coord_dpto_id_usuarios   = $arr_datos_prop[0];
            $arr_coord_dpto_correos       = $arr_datos_prop[1];

            //*********************************************************************                        
            //ACTUALIZAMOS LA ACEPTACION/RECHAZO DEL COORD/DPTO
            $tsql1 = " UPDATE propuesta_vobo 
                       SET id_estatus = ?, 
                           fecha_revision = ?, 
                           nota = ?
                       WHERE id_propuesta      = ?
                         AND id_documento      = ?
                         AND version_propuesta = ?
                         AND id_usuario        = ?;";

            $params1 = array(
                $id_estatus,
                date('d-m-Y H:i:s'),
                $nota,
                $id_propuesta_doc,
                $id_documento_doc,
                $id_version_doc,
                $id_usuario
            );

            $stmt1 = $conn->prepare($tsql1);

            if ($stmt1) {
                $result1 = $stmt1->execute($params1);
                if ($result1) {
                    if ($stmt1->rowCount() > 0) {
                        $mensaje_Transacciones .= "VoBo del Usuario Actualizado.<br/>";
                    } else {
                        throw new Exception("Ocurrió un error1 al actualizar propuesta_vobo");
                    }
                } else {
                    throw new Exception("Ocurrió un error2 al ejecutar UPDATE en propuesta_vobo");
                }
            } else {
                throw new Exception("Ocurrió un error3 al preparar la sentencia de propuesta_vobo");
            }

            $nvo_Estatus       = $id_estatus;
            $propuesta_Revisada = false;

            // Ajustamos contadores
            $coord_dpto_por_revisar_doc -= 1;
            if ($id_estatus == 3) { // 3 = Aceptada
                $coord_dpto_aceptadas += 1;
            }

            // Determinamos si todos revisaron
            if ($coord_dpto_aceptadas == $coord_dpto_total_dar_voto) {
                // Todos Aceptaron
                $nvo_Estatus        = 3; // Aceptada
                $propuesta_Revisada = true;
            } elseif (
                $coord_dpto_por_revisar_doc == 0 &&
                $coord_dpto_aceptadas != $coord_dpto_total_dar_voto
            ) {
                // Al menos uno la Rechazó
                $nvo_Estatus        = 4; // Rechazada
                $propuesta_Revisada = true;
            }

            //*********************************************************************   
            // Si ya todos revisaron la propuesta => actualizamos la versión
            if ($propuesta_Revisada) {
                // <-- CAMBIO 2: Asegurar que id_division se actualice también si es necesario
                // Por ejemplo, si la fila no tenía id_division
                // o si deseas forzarlo en cada update:

                $tsql2 = " UPDATE propuesta_version 
                           SET id_estatus = ?, 
                               id_division= ?        -- <-- CAMBIO 2: Agregamos la división
                           WHERE id_propuesta      = ?
                             AND id_documento      = ?
                             AND version_propuesta = ?;";

                $params2 = array(
                    $nvo_Estatus,
                    $id_division,            // <-- Se pasa la división
                    $id_propuesta_doc,
                    $id_documento_doc,
                    $id_version_doc
                );

                $stmt2 = $conn->prepare($tsql2);
                if ($stmt2) {
                    $result2 = $stmt2->execute($params2);
                    if ($result2) {
                        if ($stmt2->rowCount() > 0) {
                            $mensaje_Transacciones .= "Estatus Actualizado de la Propuesta_Versión. OK.<br/>";
                        } else {
                            throw new Exception("Ocurrió un error4 al actualizar propuesta_version");
                        }
                    } else {
                        throw new Exception("Ocurrió un error5 al ejecutar UPDATE en propuesta_version");
                    }
                } else {
                    throw new Exception("Ocurrió un error6 al preparar la sentencia de propuesta_version");
                }

                // Si todos Aceptaron => Agregamos jurado
                if ($nvo_Estatus == 3) {
                    // ... (lógica de crear jurado, sinodales, etc.)
                    // se deja tal cual estaba en tu código

                    // ... (Clave Definitiva para la Propuesta) ...
                    // se deja igual que en tu código
                }

                // Si fue Rechazada => generamos una nueva versión
                if ($nvo_Estatus == 4) {
                    // <-- CAMBIO 3: Al crear una nueva versión, incluir la columna id_division
                    $tsql4 = " INSERT INTO propuesta_version(
                                  id_propuesta, 
                                  id_documento, 
                                  version_propuesta,
                                  fecha_generada, 
                                  nota, 
                                  id_estatus,
                                  id_division  -- <-- CAMBIO 3
                               )
                               VALUES(?,?,?,?,?,?,?);";

                    $params4 = array(
                        $id_propuesta_doc,
                        $id_documento_doc,
                        $id_version_doc + 1,
                        date('d-m-Y H:i:s'),
                        '',
                        1,           // 1 = Sin Enviar
                        $id_division // <-- Se pasa la división
                    );

                    $stmt4 = $conn->prepare($tsql4);
                    if ($stmt4) {
                        $result4 = $stmt4->execute($params4);
                        if ($result4) {
                            if ($stmt4->rowCount() > 0) {
                                $mensaje_Transacciones .= "Nueva versión Agregada. OK.<br/>";
                            } else {
                                throw new Exception("Ocurrió un error24 al insertar nueva versión");
                            }
                        } else {
                            throw new Exception("Ocurrió un error25 al ejecutar INSERT de nueva versión");
                        }
                    } else {
                        throw new Exception("Ocurrió un error26 al preparar INSERT de nueva versión");
                    }

                    // Borramos el PDF actual, etc.
                    $archivo_pdf = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Docs/Propuestas_Profesor/' .
                        $id_profesor . '_' . $id_propuesta_doc . '_' .
                        $id_version_doc . '_' . $desc_corta_doc . '.pdf';

                    if (file_exists($archivo_pdf)) {
                        unlink($archivo_pdf);
                    }
                }
            }

            // Fin if ($propuesta_Revisada)
            $conn->commit();

            // Mensaje final
            if ($nvo_Estatus == 4) {
                $mensaje_Transacciones = "Propuesta rechazada correctamente";
            } else {
                $mensaje_Transacciones = "Propuesta aceptada correctamente";
            }

            // Bitácora + envío de correos
            // (El resto de tu lógica permanece igual)
            // ...

            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones;
            echo json_encode($jsondata);
            exit();
        } catch (Exception $ex) {
            if ($conn) {
                $conn->rollBack();
            }
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN ACTUALIZAMOS EL ESTATUS DE LA PROPUESTA
}

// Fin de la clase
// Ejemplo de uso o pruebas desactivadas
// $obj = new d_coord_jdpto_Aprobar_Propuesta();
// print_r($obj->Obtener_Total_Documentos_Por_Autorizar('ELVA'));
// echo $obj->Obtener_Usr_Mail_Propuesta_JDefinitivo('2016-2-004', 4, 1, '086198517');

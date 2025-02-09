<?php

/**
 * Definición de la Capa de Datos para la Clase Aprobar Ceremonia
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */

header('Content-Type: text/html; charset=UTF-8');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_coord_jdpto_Aprobar_Propuesta.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_mail.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Mail.php');


class d_coord_jdpto_Aprobar_Ceremonia
{
    //*********************************************************************                
    //OBTENEMOS LAS CEREMONIAS POR AUTORIZAR
    function Obtener_Ceremonias_Por_Autorizar($id_usuario)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT c.id_ceremonia, c.fecha_alta, c.id_alumno, c.id_carrera, c.id_tipo_propuesta,
                            b.descripcion_tipo_propuesta, d.email_usuario, (c.id_estatus) as estatus_ceremonia, c.fecha_alta,
                            (d.nombre_usuario || ' ' || d.apellido_paterno_usuario || ' ' || d.apellido_materno_usuario) as nombre
                    FROM inscripcion_ceremonia c
                            INNER JOIN tipos_propuesta b ON c.id_tipo_propuesta = b.id_tipo_propuesta
                            INNER JOIN usuarios d ON c.id_alumno = d.id_usuario
                            INNER JOIN carreras e ON c.id_carrera = e.id_carrera
                            INNER JOIN coordinaciones f ON e.id_carrera = f.id_carrera
                            INNER JOIN jefes_coordinacion g ON f.id_coordinacion = g.id_coordinacion
                    WHERE c.id_estatus = 9 AND g.actual_jefe = '1' AND f.id_carrera = c.id_carrera 
                            AND f.id_coordinacion not in (4,5,6) AND g.id_coordinador = ?;";

            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            $params = array($id_usuario);
            /*Verificamos el contenido de la ejecución*/
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if (!$result === FALSE) {
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
                        $mensaje_Transacciones = "No hay Ceremonias por Autorizar para la Coordinación.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "Error en los parámetros para Obtener las Ceremonias por Autorizar por la Coordinación.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para Obtener las Ceremonias por Autorizar por la Coordinación.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    } //FIN OBTENEMOS CEREMONIAS POR AUTORIZAR
    //*********************************************************************

    //OBTENEMOS TOTAL DE CEREMONIAS POR AUTORIZAR
    function Obtener_Total_Ceremonias_Por_Autorizar($id_usuario)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT count(c.id_ceremonia) total1
                    FROM inscripcion_ceremonia c
                            INNER JOIN tipos_propuesta b ON c.id_tipo_propuesta = b.id_tipo_propuesta
                            INNER JOIN usuarios d ON c.id_alumno = d.id_usuario
                            INNER JOIN carreras e ON c.id_carrera = e.id_carrera
                            INNER JOIN coordinaciones f ON e.id_carrera = f.id_carrera
                            INNER JOIN jefes_coordinacion g ON f.id_coordinacion = g.id_coordinacion
                    WHERE c.id_estatus = 9 AND g.actual_jefe = '1' AND f.id_carrera = c.id_carrera 
                            AND f.id_coordinacion not in (4,5,6) AND g.id_coordinador = ?;";

            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            $params = array($id_usuario);
            /*Verificamos el contenido de la ejecución*/
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if (!$result === FALSE) {
                    if ($stmt->rowCount() > 0) {
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Registros encontrados';
                        $jsondata['data']['registros'] = array();

                        $x = '';
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $jsondata['data']['registros'][] = $row;
                        }
                        $stmt = null;
                        $conn = null;
                        return ($jsondata);
                        //                        echo json_encode($jsondata);
                        //                        exit();
                    } else {
                        $mensaje_Transacciones = "No hay Ceremonias por Autorizar para la Coordinación.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "Error en los parámetros para Obtener el Total de Ceremonias por Autorizar por la Coordinación.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para Obtener el Total de Ceremonias por Autorizar por la Coordinación.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    } //FIN OBTENEMOS TOTAL DE CEREMONIAS POR AUTORIZAR
    //*********************************************************************

    //OBTENEMOS DOCUMENTOS POR AUTORIZAR
    function Obtener_Documentos_Por_Autorizar($id_ceremonia, $id_estatus)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT a.id_ceremonia, a.id_documento, a.version, a.id_estatus, 
                            b.descripcion_documento, c.descripcion_estatus, b.descripcion_para_nom_archivo,
                            d.id_estatus as stat_ceremonia, d.id_alumno, d.id_carrera
                    FROM ceremonia_docs a
                            INNER JOIN documentos b ON a.id_documento = b.id_documento
                            INNER JOIN estatus c ON a.id_estatus = c.id_estatus
                            INNER JOIN inscripcion_ceremonia d ON d.id_ceremonia = a.id_ceremonia
                    WHERE d.id_ceremonia = ? AND d.id_estatus = ? AND a.id_estatus IN (3,4,9) 
                            AND a.version IN (SELECT max(e.version)
                                            FROM ceremonia_docs e
                                            WHERE e.id_ceremonia= a.id_ceremonia AND e.id_documento = a.id_documento)
                    ORDER BY b.descripcion_documento;";

            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            /*Verificamos el contenido de la ejecución*/
            $params = array($id_ceremonia, $id_estatus);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if (!$result === FALSE) {
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
                        $mensaje_Transacciones = "No hay Documentos para la Ceremonia.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "Error en los parámetros para Obtener los Documentos para la Ceremonia.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para Obtener los Documentos para la Ceremonia.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    } //FIN OBTENEMOS DOCUMENTOS POR AUTORIZAR
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

    function Actualizar_Estatus_Doc_(
        $id_ceremonia,
        $id_documento,
        $version,
        $desc_documento,
        $nota,
        $id_estatus,
        $id_coordinador,
        $mail_coordinador,
        $todos_revisados,
        $desc_corta_nom_archivo,
        $rechazados,
        $fecha_alta_ceremonia,
        $datos_archivos,
        $id_division
    ) {

        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();

        $id_alumno = '';
        $id_carrera = '';
        $nom_alumno = '';
        $desc_propuesta = '';
        $mail_alumno = '';
        $id_coordinador = '';
        $mail_coordinador = '';
        $mail_admin = '';
        $nuevoConsecutivo = 0;

        $nva_Clave_Ceremonia = '';

        try {

            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            /*Iniciar la transacción. */
            $conn->beginTransaction();

            //OBTENEMOS DATOS GRLES DE LA CEREMONIA
            $tsql = " SELECT a.id_alumno, a.id_carrera,
                            b.descripcion_tipo_propuesta,
                            (c.nombre_usuario || ' ' || c.apellido_paterno_usuario || ' ' || c.apellido_materno_usuario) as nom_alumno, c.email_usuario as mail_alumno,
                            f.id_coordinador, g.email_usuario as mail_coordinador
                    FROM inscripcion_ceremonia a
                            INNER JOIN tipos_propuesta b ON a.id_tipo_propuesta = b.id_tipo_propuesta
                            INNER JOIN usuarios c ON a.id_alumno = c.id_usuario
                            INNER JOIN carreras d ON a.id_carrera = d.id_carrera
                            INNER JOIN coordinaciones e ON d.id_carrera = e.id_carrera
                            INNER JOIN jefes_coordinacion f ON e.id_coordinacion = f.id_coordinacion
                            INNER JOIN usuarios g ON f.id_coordinador = g.id_usuario
                    WHERE a.id_ceremonia = ? AND f.actual_jefe = '1';";

            /* Valor de los parámetros. */
            $params = array($id_ceremonia);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if (!$result === FALSE) {
                    if ($stmt->rowCount() > 0) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $id_alumno = $row['id_alumno'];
                        $id_carrera = $row['id_carrera'];
                        $nom_alumno = $row['nom_alumno'];
                        $desc_propuesta = $row['descripcion_tipo_propuesta'];
                        $mail_alumno = $row['mail_alumno'];
                        $id_coordinador = $row['id_coordinador'];
                        $mail_coordinador = $row['mail_coordinador'];
                    } else {
                        $mensaje_Transacciones = 'No se encontró la información de esta Ceremonia.';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros para obtener la información de la Ceremonia.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para Obtener la información de la Ceremonia.<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }
            //FIN OBTENEMOS DATOS GRLES DE LA CEREMONIA

            //obtenemos el correo de admin
            $tsql = " SELECT email_usuario
                    FROM usuarios
                    WHERE id_tipo_usuario in (2,3)";
            //print_r($tsql);

            /* Valor de los parámetros. */
            $params = array();
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute();
                if (!$result === FALSE) {
                    if ($stmt->rowCount() > 0) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $mail_admin = $row['email_usuario'];
                    } else {
                        $mensaje_Transacciones = 'No se encontró la información de ADMIN.';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros para obtener la información de ADMIN.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para Obtener la información de ADMIN.<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }
            //FIN OBTENER CORREO DE ADMIN

            //Actualizamos el estatus del Documento especificado
            /* Query parametrizado. */
            $tsql = " UPDATE ceremonia_docs SET " .
                "id_estatus =  ?, " .
                "fecha_revision_coordinador = ?, " .
                "nota_coordinador = ?, " .
                "id_coordinador = ? " .
                "WHERE id_ceremonia = ? AND " .
                "id_documento =? AND version = ?;";
            /* Valor de los parámetros. */
            $params = array(
                $id_estatus,
                date('d-m-Y H:i:s'),
                $nota,
                $id_coordinador,
                $id_ceremonia,
                $id_documento,
                $version
            );
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if (!$result === FALSE) {
                    if ($stmt->rowCount() > 0) {
                        if ($id_estatus == 4) {
                            $mensaje_Transacciones = "Documento rechazado.<br/>";
                        } else {
                            $mensaje_Transacciones = "Documento aceptado.<br/>";
                        }
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones = "No se pudo Actualizar el Estatus del Documento<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros para Actualizar  el Estatus del Documento.<br/>";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en la BD al Actualizar el Estatus del Documento.<br/>";
                throw new Exception($mensaje_Transacciones);
            }
            //FIN Actualizamos el estatus del Documento especificado

            $nvo_estatus = 0;
            $fecha_inicio = '';
            $anio = 0;
            $mes = 0;
            $semestre = 0;

            //SI TODOS ESTAN REVISADOS CAMBIAMOS EL ESTATUS DE LA CEREMONIA
            if ($todos_revisados == 1) { //todos los documentos han sido revisados
                $nvo_estatus = 3; //3. aceptada
                if ($rechazados != '') { //NO todos los documentos fueron aceptados
                    $nvo_estatus = 2;   // por autorizar admin
                }

                //Obtenemos la nueva clave para la ceremonia Aceptada
                if ($nvo_estatus == 3) { //todas fueron aceptadas
                    //Obtenemos la nueva clave para la ceremonia Aceptada 
                    $fecha_inicio = strtotime($fecha_alta_ceremonia);
                    $fecha_inicio = date('d-m-Y', $fecha_inicio);
                    $anio = date("Y", strtotime($fecha_inicio));
                    $mes = date("m", strtotime($fecha_inicio));
                    $semestre = 1;
                    if ($mes > 6) {
                        $semestre = 2;
                    }
                    /* Query para obtener el consecutivo */
                    $tsql1 = " SELECT consecutivo + 1 as siguiente_digito
                            FROM inscripcion_ceremonia_contador
                            WHERE anio = ? AND semestre = ?;
                        ";
                    /* Valor de los parámetros. */
                    $params1 = array($anio, $semestre);
                    /* Preparamos la sentencia a ejecutar */
                    $stmt1 = $conn->prepare($tsql1);
                    $result1 = $stmt1->execute($params1);
                    $nva_Clave_Ceremonia = '';

                    if ($stmt1) {
                        /*Ejecutamos el Query*/
                        $result1 = $stmt1->execute($params1);
                        if (!$result1 === FALSE) {
                            if ($stmt1->rowCount() > 0) {
                                $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                                $nuevoConsecutivo = $row['siguiente_digito'];
                                $consecutivo = str_pad($row['siguiente_digito'], 3, "0", STR_PAD_LEFT);
                                $nva_Clave_Ceremonia = $anio . "" .
                                    $mes . "-" . $consecutivo;
                            } else {
                                $error = $stmt1->errorInfo();
                                $mensaje_Transacciones .= "No se puedo obtener el Consecutivo para la Clave de la Ceremonia.<br/>"  . $error[2];
                                throw new Exception($mensaje_Transacciones);
                            }
                        } else {
                            $error = $stmt1->errorInfo();
                            $mensaje_Transacciones .= "Error en los parámetros para obtener el Consecutivo de la Clave de la Ceremonia.<br/>"  . $error[2];
                            throw new Exception($mensaje_Transacciones);
                        }
                    } else {
                        $error = $stmt1->errorInfo();
                        $mensaje_Transacciones .= "Error en la sentencia SQL para obtener el Consecutivo de la Clave de la Ceremonia.<br/>"  . $error[2];
                        throw new Exception($mensaje_Transacciones);
                    }
                }
                //FIN Obtenemos la nueva clave para la ceremonia Aceptada                 
                //$mensaje_Transacciones .= "Clave Definitiva para la Ceremonia obtenida. OK.<br/>";

                //QUERY PARA ACUALIZAR EL ESTATUS DE LA CEREMONIA
                if ($nvo_estatus == 3) { //LA CEREMONIA A SIDO ACEPTADA
                    $tsql = " UPDATE inscripcion_ceremonia SET " .
                        "id_ceremonia = ?, " .
                        "id_estatus =  ? " .
                        "WHERE id_ceremonia = ?;";
                    $params = array($id_ceremonia, $nvo_estatus, $id_ceremonia);
                } else {
                    $tsql = " UPDATE inscripcion_ceremonia SET " .
                        "id_estatus =  ? " .
                        "WHERE id_ceremonia = ?;";
                    /* Valor de los parámetros. */
                    $params = array($nvo_estatus, $id_ceremonia);
                }
                //FIN QUERY PARA ACUALIZAR EL ESTATUS DE LA CEREMONIA

                //ACUALIZAMOS EL ESTATUS DE LA CEREMONIA
                /* Preparamos la sentencia a ejecutar */
                $stmt = $conn->prepare($tsql);
                if ($stmt) {
                    /*Ejecutamos el Query*/
                    $result = $stmt->execute($params);
                    if (!$result === FALSE) {
                        if ($stmt->rowCount() > 0) {
                            if ($nvo_estatus == 3) {
                                $mensaje_Transacciones .= "Ceremonia aceptada. <br/>";
                            } else {
                                $mensaje_Transacciones .= "Ceremonia rechazada. <br/>";
                            }
                        } else {
                            $error = $stmt->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Actualizar el Estatus de la Ceremonia<br>.";
                            throw new Exception($mensaje_Transacciones);
                        }
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "Error en los parámetros para Actualizar el Estatus de la Ceremonia.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la BD al Actualizar el Estatus de la Ceremonia.<br/>";
                    throw new Exception($mensaje_Transacciones);
                }
                //FIN ACUALIZAMOS EL ESTATUS DE LA CEREMONIA

                //ACTUALIZAMOS EL CONTADOR DE CEREMONIA
                if ($nvo_estatus == 3) { // 3. Aceptada
                    $tsql5 = " UPDATE inscripcion_ceremonia_contador SET
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
                                $mensaje_Transacciones .= "";
                            } else {
                                $error = $stmt5->errorInfo();
                                $mensaje_Transacciones .= "Ocurrió un error.";
                                throw new Exception($mensaje_Transacciones);
                            }
                        } else {
                            $error = $stmt5->errorInfo();
                            $mensaje_Transacciones .= "Ocurrió un error";
                            throw new Exception($mensaje_Transacciones);
                        }
                    } else {
                        $error = $stmt5->errorInfo();
                        $mensaje_Transacciones .= "Ocurrió un error en la BD.";
                        throw new Exception($mensaje_Transacciones);
                    }
                }

                //NO TODOS LOS DOCUMENTOS FUERON ACEPTADOS
                if ($rechazados != '') {
                    //CREAMOS UNA NUEVA VERSION DE LOS DOCUMENTOS RECHAZADOS CON ESTATUS 1. SIN ENVIAR
                    //BORRAR LOS PDF DE LOS DOCUMENTOS RECHAZADOS
                    $arr_lista_Rechazados = preg_split("/[|]/", $rechazados);
                    $renglones = count($arr_lista_Rechazados);

                    $tsql2 = " INSERT INTO ceremonia_docs(
                                  id_ceremonia, id_documento, version, id_estatus)
                                  VALUES(?,?,?,?);";
                    for ($i = 0; $i < $renglones; $i++) {
                        $arr_doc_rechazado = preg_split("/[,]/", $arr_lista_Rechazados[$i]);
                        $params2 = array(
                            $id_ceremonia,
                            $arr_doc_rechazado[0],
                            $arr_doc_rechazado[1] + 1,
                            1
                        );
                        /* Preparamos la sentencia a ejecutar */
                        $stmt2 = $conn->prepare($tsql2);
                        if ($stmt2) {
                            /*Ejecutamos el Query*/
                            $result2 = $stmt2->execute($params2);
                            if (!$result2 === FALSE) {
                                if ($stmt2->rowCount() > 0) {
                                    $mensaje_Transacciones .= "";
                                } else {
                                    $error = $stmt2->errorInfo();
                                    $mensaje_Transacciones .= "No se pudo Agregar el Nuevo Documento para su Reenvío<br>.";
                                    throw new Exception($mensaje_Transacciones);
                                }
                            } else {
                                $error = $stmt2->errorInfo();
                                $mensaje_Transacciones .= "Error en los parámetros para Agregar el Nuevo Documento para su Reenvío.<br/>";
                                throw new Exception($mensaje_Transacciones);
                            }
                        } else {
                            $error = $stmt2->errorInfo();
                            $mensaje_Transacciones .= "Error en la BD al Agregar el Nuevo Documento para su Reenvío.<br/>";
                            throw new Exception($mensaje_Transacciones);
                        }

                        //BORRAMOS EL ARCHIVO
                        $archivo_pdf = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Docs/Ceremonias/' . $id_alumno . '_' . $id_carrera . '_' .
                            $id_ceremonia . '_' . $arr_doc_rechazado[1] . '_' . $arr_doc_rechazado[2] . '.pdf';

                        if (file_exists($archivo_pdf)) {
                            unlink($archivo_pdf);
                        }
                    }
                }
                //FIN NO TODOS LOS DOCUMENTOS FUERON ACEPTADOS

                //RENOMBRAR LOS ARCHIVOS PDF CON LA NUEVA CLAVE DE LA CEREMONIA
                //                $proc_rename ='';
                if ($nvo_estatus == 3) { //LA CEREMONIA A SIDO ACEPTADA
                    //OBTENER LISTADO DE DOCUMENTOS A RENOMBRAR
                    $arr_lista_Documentos = preg_split("/[|]/", $datos_archivos);
                    $renglones = count($arr_lista_Documentos);

                    for ($i = 0; $i < $renglones; $i++) {
                        $arr_documento = preg_split("/[,]/", $arr_lista_Documentos[$i]);
                        $nom_archivo_actual = $arr_documento[0] . '_' .
                            $arr_documento[1] . '_' .
                            $arr_documento[2] . '_' .
                            $arr_documento[3] . '_' .
                            $arr_documento[4] . '.pdf';
                        $nom_archivo_nuevo = $arr_documento[0] . '_' .
                            $arr_documento[1] . '_' .
                            $nva_Clave_Ceremonia . '_' .
                            $arr_documento[3] . '_' .
                            $arr_documento[4] . '.pdf';
                        $nom_archivo_actual = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Docs/Ceremonias/' . $nom_archivo_actual;
                        $nom_archivo_nuevo = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Docs/Ceremonias/' . $nom_archivo_nuevo;
                        if (file_exists($nom_archivo_actual)) {
                            rename($nom_archivo_actual, $nom_archivo_nuevo);
                        }
                        //                        $proc_rename .= $nom_archivo_actual.','.$nom_archivo_nuevo.'|';
                    }
                }
            }
            //FIN SI TODOS ESTAN REVISADOS CAMBIAMOS EL ESTATUS DE LA CEREMONIA

            $id_tema_evento = 90; //aprobar ceremonias Coord
            $id_tipo_evento = 25; //aprobación
            $descripcion_evento = "Documento  " . $desc_documento . " Versión " . $version .
                " ** Ceremonia. " . $id_ceremonia . ' ' . $desc_propuesta .
                " ** Nueva Clave de Ceremonia " . $nva_Clave_Ceremonia  .
                " ** Carrera " . $id_carrera .
                " ** Alumno " . $id_alumno . ' ** ' . $nom_alumno .
                " ** ha sido ACEPTADO" . ' --- ' . $nota;

            if ($id_estatus != 3) {
                $id_tipo_evento = 30; //rechazado
                $descripcion_evento = "Documento  " . $desc_documento . " Versión " . $version .
                    " ** Ceremonia. " . $id_ceremonia . ' ' . $desc_propuesta .
                    " ** Carrera " . $id_carrera .
                    " ** Alumno " . $id_alumno . ' ** ' . $nom_alumno .
                    " ** ha sido RECHAZADO" . ' --- ' . $nota;
            }

            $descripcion_Correo = "Su Ceremonia " . $id_ceremonia . ' ' . $desc_propuesta .
                "<br>** Nueva Clave de Ceremonia " . $nva_Clave_Ceremonia  .
                "<br>** Carrera " . $id_carrera .
                "<br>** Alumno " . $id_alumno . ' ** ' . $nom_alumno .
                "<br>*** ha sido <b>ACEPTADO</b>" . ' --- ' . $nota;
            if ($rechazados != '') {
                $descripcion_Correo = "Su Ceremonia " . $id_ceremonia . ' ' . $desc_propuesta .
                    "<br>** Carrera " . $id_carrera .
                    "<br>** Alumno " . $id_alumno . ' ** ' . $nom_alumno .
                    "<br>*** ha sido <b>RECHAZADA</b>" .
                    "<br>Se ha generado un nuevo número de versión de Documentos para que pueda reenvíarlo. <br> --- " . $nota;
            }

            $conn->commit();

            //A BITACORA EL MOVIMIENTO DEL COORDINADOR                        
            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_evento;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento);
            $obj_miBitacora->set_Id_Usuario_Genera($id_coordinador);
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $respuesta_mail = '';
            $resultado_Bitacora = '';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
            sleep(1);

            if ($todos_revisados == 1) {
                //A BITACORA EL MOV. DE ENVIO DE CORREO
                $obj_miBitacora = new Bitacora();

                $descripcionEvento = $descripcion_Correo;
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
                $obj_miBitacora->set_Id_Tipo_Evento(50); //envio mail
                $obj_miBitacora->set_Id_Usuario_Genera(1);
                $obj_miBitacora->set_Id_Usuario_Destinatario($id_alumno);
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($id_division);

                $resultado_Bitacora = '';
                $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
                sleep(1);

                $obj_miBitacora = new Bitacora();

                $descripcionEvento = $descripcion_Correo;
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
                $obj_miBitacora->set_Id_Tipo_Evento(50); //envio mail
                $obj_miBitacora->set_Id_Usuario_Genera(1);
                $obj_miBitacora->set_Id_Usuario_Destinatario($id_coordinador);
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($id_division);

                $resultado_Bitacora = '';
                $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
                sleep(1);

                $obj = new d_mail();
                $mi_mail = new Mail();
                $mensaje = $descripcion_Correo;

                $mi_mail->set_Correo_Destinatarios($mail_alumno);
                $mi_mail->set_Correo_Copia_Oculta($mail_coordinador . ',' . $mail_admin);
                $mi_mail->set_Asunto('AVISO APROBACIÓN DE CEREMONIA');
                $mi_mail->set_Mensaje($mensaje);
                $respuesta_mail = $obj->Envair_Mail($mi_mail);
            }

            $conn = null;
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora; // . $respuesta_mail;
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
}

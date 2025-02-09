<?php

/**
 * Definición de la Capa de Datos para las Inscripciones a una Propuesta
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */

header('Content-Type: text/html; charset=UTF-8');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_mail.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Mail.php');

class d_alumno_Mi_Titulacion_Por_Propuesta
{

    //OBTENEMOS LAS INSCRIPCIONES DEL ALUMNO A PROPUESTAS DE PROFESOR
    function Obtener_Mis_Inscripcines($id_alumno, $id_carrera)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            //            $tsql = "SELECT a.id_inscripcion, a.id_propuesta, a.id_alumno, a.id_carrera, a.id_estatus, a.fecha_inscripcion, a.fecha_baja,
            //                            b.descripcion_estatus, c.titulo_propuesta, d.descripcion_tipo_propuesta,
            //                            (e.nombre_usuario || ' ' || e.apellido_paterno_usuario|| ' ' || e.apellido_materno_usuario) as nom_alumno,
            //                            (f.nombre_usuario || ' ' || f.apellido_paterno_usuario|| ' ' || f.apellido_materno_usuario) as nom_profesor,
            //                            a.nota_baja
            //                    FROM inscripcion_propuesta a
            //                            INNER JOIN estatus b ON a.id_estatus = b.id_estatus
            //                            INNER JOIN propuestas_profesor c ON a.id_propuesta = c.id_propuesta
            //                            INNER JOIN tipos_propuesta d ON c.id_tipo_propuesta = d.id_tipo_propuesta
            //                            INNER JOIN usuarios e ON a.id_alumno = e.id_usuario
            //                            INNER JOIN usuarios f ON c.id_profesor = f.id_usuario
            //                    WHERE a.id_alumno = ? and a.id_carrera = ?
            //                    ORDER BY a.fecha_inscripcion;";

            $tsql = "SELECT a.id_inscripcion, a.id_propuesta, a.id_alumno, a.id_carrera, a.id_estatus, a.fecha_inscripcion, a.fecha_baja,
                            b.descripcion_estatus, c.titulo_propuesta, d.descripcion_tipo_propuesta,
                            (e.nombre_usuario || ' ' || e.apellido_paterno_usuario|| ' ' || e.apellido_materno_usuario) as nom_alumno,
                            (f.nombre_usuario || ' ' || f.apellido_paterno_usuario|| ' ' || f.apellido_materno_usuario) as nom_profesor,
                            a.nota_baja, g.version_propuesta, c.id_profesor, h.descripcion_para_nom_archivo, a.llenofechaestimada
                    FROM inscripcion_propuesta a
                            INNER JOIN estatus b ON a.id_estatus = b.id_estatus
                            INNER JOIN propuestas_profesor c ON a.id_propuesta = c.id_propuesta
                            INNER JOIN tipos_propuesta d ON c.id_tipo_propuesta = d.id_tipo_propuesta
                            INNER JOIN usuarios e ON a.id_alumno = e.id_usuario
                            INNER JOIN usuarios f ON c.id_profesor = f.id_usuario
                            INNER JOIN propuesta_version g ON a.id_propuesta = g.id_propuesta
                            INNER JOIN documentos h ON g.id_documento = h.id_documento
                    WHERE a.id_alumno = ? and a.id_carrera = ? AND g.id_documento = 4 AND g.id_estatus = 3
                    ORDER BY a.fecha_inscripcion;";
            /* Valor de los parámetros. */
            $params = array($id_alumno, $id_carrera);
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
                        $mensaje_Transacciones = "No hay información de Inscripciones a Propuestas.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener las Inscripciones a Propuestas.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LAS INSCRIPCIONES DEL ALUMNO A PROPUESTAS DE PROFESOR 

    //TOTALES
    function Obtener_Total_Propuesta($id_estatus, $id_alumno)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            //            $tsql = "SELECT a.id_inscripcion, a.id_propuesta, a.id_alumno, a.id_carrera, a.id_estatus, a.fecha_inscripcion, a.fecha_baja,
            //                            b.descripcion_estatus, c.titulo_propuesta, d.descripcion_tipo_propuesta,
            //                            (e.nombre_usuario || ' ' || e.apellido_paterno_usuario|| ' ' || e.apellido_materno_usuario) as nom_alumno,
            //                            (f.nombre_usuario || ' ' || f.apellido_paterno_usuario|| ' ' || f.apellido_materno_usuario) as nom_profesor,
            //                            a.nota_baja
            //                    FROM inscripcion_propuesta a
            //                            INNER JOIN estatus b ON a.id_estatus = b.id_estatus
            //                            INNER JOIN propuestas_profesor c ON a.id_propuesta = c.id_propuesta
            //                            INNER JOIN tipos_propuesta d ON c.id_tipo_propuesta = d.id_tipo_propuesta
            //                            INNER JOIN usuarios e ON a.id_alumno = e.id_usuario
            //                            INNER JOIN usuarios f ON c.id_profesor = f.id_usuario
            //                    WHERE a.id_alumno = ? and a.id_carrera = ?
            //                    ORDER BY a.fecha_inscripcion;";

            $tsql = "SELECT count(a.id_estatus) as total5
                    FROM inscripcion_propuesta a
                            INNER JOIN estatus b ON a.id_estatus = b.id_estatus
                            INNER JOIN propuestas_profesor c ON a.id_propuesta = c.id_propuesta
                            INNER JOIN tipos_propuesta d ON c.id_tipo_propuesta = d.id_tipo_propuesta
                            INNER JOIN usuarios e ON a.id_alumno = e.id_usuario
                            INNER JOIN usuarios f ON c.id_profesor = f.id_usuario
                            INNER JOIN propuesta_version g ON a.id_propuesta = g.id_propuesta
                            INNER JOIN documentos h ON g.id_documento = h.id_documento
                    WHERE a.id_estatus = ? and a.id_alumno = ?;";
            /* Valor de los parámetros. */
            $params = array($id_estatus, $id_alumno);
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

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $jsondata['data']['registros'][] = $row;
                        }
                        $stmt = null;
                        $conn = null;
                        return $jsondata;
                        //echo json_encode($jsondata);
                        //exit();
                    } else {
                        $mensaje_Transacciones = "No hay información de Inscripciones a Propuestas.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener las Inscripciones a Propuestas.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN TOTALES

    //OBTENEMOS LOS DOCUMENTOS ENVIADOS
    function Obtener_Docs_Enviados($id_inscripcion, $id_documento)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_inscripcion, a.id_documento, a.numero_version, a.fecha_revision, 
                            a.fecha_generada, a.nota, a.id_estatus, b.descripcion_estatus, 
                            c.descripcion_documento, c.descripcion_para_nom_archivo, a.fecha_recepcion,
                            d.id_carrera, d.id_propuesta, d.id_alumno
                       FROM inscripcion_propuesta_version a
                             INNER JOIN estatus b ON a.id_estatus = b.id_estatus
                             INNER JOIN documentos c ON a.id_documento = c.id_documento
                             INNER JOIN inscripcion_propuesta d ON a.id_inscripcion = d.id_inscripcion
                       WHERE a.id_inscripcion = ? AND a.id_documento = ?
                       ORDER BY a.id_inscripcion, a.numero_version";

            /* Valor de los parámetros. */
            $params = array($id_inscripcion, $id_documento);
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
                        $mensaje_Transacciones = "No hay información de Documentos Enviados.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Documentos Enviados.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS DOCS ENVIADOS

    //AGREGAMOS LA INSCRIPCION A LA PROPUESTA SELECCIONADA
    function Agregar($id_propuesta, $id_estatus, $id_alumno, $id_carrera, $titulo_propuesta, $id_division)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();

        try {

            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            /* Iniciar la transacción. */

            $conn->beginTransaction();

            //OBTENEMOS EL TOTAL DE ALUMNOS INSCRITOS EN LA PROPUESTA
            //            $tsql1=" SELECT count(id_alumno) as inscritos
            //                        FROM inscripcion_propuesta
            //                        WHERE id_propuesta = ?";

            //OBTENEMOS EL SIGUIENTE CONSUCUTIVO DE LA PROPUESTA
            $tsql1 = " SELECT (consecutivo) as siguiente
                        FROM propuestas_profesor
                        WHERE id_propuesta = ?";

            /* Valor de los parámetros. */
            $params1 = array($id_propuesta);

            /* Preparamos la sentencia a ejecutar */
            $stmt1 = $conn->prepare($tsql1);
            $result1 = $stmt1->execute($params1);

            if ($stmt1) {
                /*Ejecutamos el Query*/
                $result1 = $stmt1->execute($params1);
                if ($result1) {
                    if ($stmt1->rowCount() > 0) {
                        $row = $stmt1->fetch(PDO::FETCH_ASSOC);

                        //OBTENEMOS EL TOTAL DE INSCRITOS EN LA PROPUESTA
                        $totalInscritos = $row['siguiente'];
                        //$id_inscripcion = $id_propuesta . '-'. str_pad($totalInscritos +1,2,"0",STR_PAD_LEFT);
                        $id_inscripcion = $id_propuesta + $totalInscritos + 1;
                    } else {
                        $error = $stmt1->errorInfo();
                        $mensaje_Transacciones = "No se puedo obtener el Consecutivo para la Inscripción a la Propuesta.<br/>"  . $error[2];
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt1->errorInfo();
                    $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Consecutivo para la Inscripción a la Propuesta.<br/>"  . $error[2];
                    throw new Exception($mensaje_Transacciones);
                }
            }
            //$mensaje_Transacciones = "Clave para Inscripción obtenida. OK.<br/>";

            //Agregamos la Inscripción a la BD
            $tsql2 = " INSERT INTO inscripcion_propuesta(
                            id_inscripcion, fecha_inscripcion, id_propuesta, 
                            id_estatus, id_alumno, id_carrera, id_division)
                    VALUES (?, ?, ?, ?, ?, ?, ?);";
            /* Valor de los parámetros. */
            $params2 = array($id_inscripcion, date('d-m-Y H:i:s'), $id_propuesta, $id_estatus, $id_alumno, $id_carrera, $id_division); //10. Por Autorizar Prof.
            /* Preparamos la sentencia a ejecutar */
            $stmt2 = $conn->prepare($tsql2);
            if ($stmt2) {
                /*Ejecutamos el Query*/
                $result2 = $stmt2->execute($params2);
                if ($result2) {
                    if ($stmt2->rowCount() > 0) {
                        $mensaje_Transacciones = "Propuesta Examen agregada correctamente.";
                    } else {
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un error";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt2->errorInfo();
                $mensaje_Transacciones = "Ocurrió un error";
                throw new Exception($mensaje_Transacciones);
            }

            //AGREGAMOS LOS DOCUMENTOS HIST. ACADEM Y BAJA PARA LAS AUTORIZACIONES CORRESPONDIENTES
            $tsql3 = " INSERT INTO inscripcion_propuesta_version(
                id_inscripcion, id_documento, numero_version, fecha_generada, nota, id_estatus, id_division)
                SELECT ? ,id_documento, 1, ? , '', 1 , ?
                FROM documentos WHERE id_documento IN (2,5);
                ";
            /* Valor de los parámetros. */
            $params3 = array($id_inscripcion, date('d-m-Y H:i:s'), $id_division);
            /* Preparamos la sentencia a ejecutar */
            $stmt3 = $conn->prepare($tsql3);
            if ($stmt3) {
                /*Ejecutamos el Query*/
                $result3 = $stmt3->execute($params3);
                if ($result3) {
                    if ($stmt3->rowCount() > 0) {
                        //$mensaje_Transacciones .= "Documentos de la Inscripción Agregados. OK.<br/>";
                    } else {
                        $error = $stmt3->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt3->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un error";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt3->errorInfo();
                $mensaje_Transacciones = "Ocurrió un error";
                throw new Exception($mensaje_Transacciones);
            }

            //Aumentamos el contador de inscritos en la propuesta
            $tsql5 = " UPDATE propuestas_profesor SET
            consecutivo = consecutivo + 1 
            WHERE id_propuesta = ?;";

            /* Valor de los parámetros. */
            $params5 = array($id_propuesta);
            /* Preparamos la sentencia a ejecutar */
            $stmt5 = $conn->prepare($tsql5);
            if ($stmt5) {
                /*Ejecutamos el Query*/
                $result5 = $stmt5->execute($params5);
                if ($result5) {
                    if ($stmt5->rowCount() > 0) {
                        //$mensaje_Transacciones .= "Contador de Inscritos aumentado. OK.<br/>";
                    } else {
                        $error = $stmt5->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt5->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un error";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt5->errorInfo();
                $mensaje_Transacciones = "Ocurrió un error";
                throw new Exception($mensaje_Transacciones);
            }

            //Disminuímos el número de vancantes según la carrera
            //                $tsql5=" UPDATE propuesta_profesor_carrera_requeridos SET
            //                vacantes = vacantes -1 
            //                WHERE id_propuesta = ? AND id_carrera = ?;";

            //                /* Valor de los parámetros. */
            //                $params5= array($id_propuesta, $id_carrera);
            //                /* Preparamos la sentencia a ejecutar */
            //                $stmt5 = $conn->prepare($tsql5);
            //                if($stmt5){
            //                    /*Ejecutamos el Query*/                
            //                    $result5 = $stmt5->execute($params5); 
            //                    if ($result5){
            //                            if($stmt5->rowCount() > 0){                    
            //                                $mensaje_Transacciones .= "Vacantes disminuídas. OK.<br/>";
            //                            }
            //                            else{
            //                                $error = $stmt5->errorInfo();
            //                                $mensaje_Transacciones .= "No se pudo Disminuír las Vancantes.<br>"  . $error[2] .'<br>';
            //                                throw new Exception($mensaje_Transacciones);                                                              
            //                            }
            //                    }
            //                    else{
            //                        $error = $stmt5->errorInfo();
            //                        $mensaje_Transacciones .= "No se pudo Disminuír las Vancantes.<br/>"  . $error[2] .'<br>';
            //                        throw new Exception($mensaje_Transacciones);        
            //                    }
            //                }
            //                else{
            //                        $error = $stmt5->errorInfo();
            //                        $mensaje_Transacciones .= "Error en la sentencia SQL para Disminuír las Vancantes.<br/>"  . $error[2] .'<br>';
            //                        throw new Exception($mensaje_Transacciones);                                
            //                }

            $conn->commit();

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = ' ** Solicitud de Inscripción a la Propuesta ' . $id_propuesta . ' **Con Título ' .
                $titulo_propuesta . ' ** Para la Carrera : ' . $id_carrera . ' ** No. de Inscripción : ' . $id_inscripcion;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(35); //inscripción a propuesta
            $obj_miBitacora->set_Id_Tipo_Evento(5); // alta
            $obj_miBitacora->set_Id_Usuario_Genera($id_alumno);
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora = '';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);


            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora;
            echo json_encode($jsondata);
            exit();
        } catch (Exception $ex) {
            $conn->rollBack();
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();
        }
    }


    //FIN AGREGAMOS LA INSCRIPCION A LA PROPUESTA SELECCIONADA


    //  MÉTODO PARA ACTUALIZAR INFORMACIÓN SOBRE LA FECHA ESTIMADA PARA TITULACIÓN Y MOTIVO
    function Registrar_Motivo_Propuesta($id_inscripcion, $fechaEstimada, $motivoTitulacion)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            //  ACTUALIZAR LA INFORMACIÓN SOBRE LA CEREMONIA
            $tsql = "UPDATE inscripcion_propuesta SET " .
                "llenofechaestimada = true, " .
                "fechaestimadatitulacion = ?, " .
                "motivotitulacion = ? " .
                "WHERE id_inscripcion = ?;";

            $params = array($fechaEstimada, $motivoTitulacion, $id_inscripcion);

            $stmt = $conn->prepare($tsql);

            if ($stmt) {
                $result = $stmt->execute($params);

                if (!$result === FALSE) {
                    if ($stmt->rowCount() > 0) {
                        $mensaje_Transacciones .= "Información actualizada.<br/>";
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "Error al actualizar la información<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros al actualizar la información.<br/>";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en la sintaxis SQL.<br/>";
                throw new Exception($mensaje_Transacciones);
            }

            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones;
            return json_encode($jsondata);
            exit();
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            return json_encode($jsondata);
            exit();
        }
    }

    //ACTUALIZAMOS EL ESTATUS DEL DOCUMENTO ENVIADO POR EL ALUMNO
    function Actualizar_Estatus_Doc_Enviado($id_inscripcion, $id_documento, $id_version, $id_estatus)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();

        try {

            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            //Actualizamos el estatus del Documento especificado
            /* Query parametrizado. */
            $tsql = " UPDATE inscripcion_propuesta_version SET " .
                "id_estatus =  ?, " .
                "fecha_recepcion = ? " .
                "WHERE id_inscripcion = ? AND " .
                "id_documento =? AND numero_version = ?;";
            /* Valor de los parámetros. */
            $params = array(
                $id_estatus,
                date('d-m-Y H:i:s'),
                $id_inscripcion,
                $id_documento,
                $id_version
            );
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $mensaje_Transacciones .= "Estatus Actualizado. OK.<br/>";
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Actualizar el Estatus del Documento<br>."  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se actualizó el Estatus del Documento.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Estatus del Documento.<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }

            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones;
            return json_encode($jsondata);
            exit();
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            return json_encode($jsondata);
            exit();
        }
    }
    //FIN ACTUALIZAMOS EL ESTATUS DEL DOCUMENTO ENVIADO POR EL ALUMNO

    //BORRAR INSCRIPCION DE UNA PROPUESTA
    function Borrar_Propuesta($id_propuesta, $id_carrera, $id_inscripcion, $id_alumno, $titulo_propuesta, $nota, $id_division)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();

        try {

            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            $conn->beginTransaction();

            //Aumentamos el número de requerimientos de la carrera
            /* Query parametrizado. */
            //            $tsql=" UPDATE propuesta_profesor_carrera_requeridos SET ".
            //                    "vacantes =  vacantes + 1 ".
            //                    "WHERE id_propuesta = ? AND " .
            //                    "id_carrera =?;";                    
            //            /* Valor de los parámetros. */
            //            $params = array($id_propuesta, $id_carrera);
            //            /* Preparamos la sentencia a ejecutar */
            //            $stmt = $conn->prepare($tsql);
            //            if($stmt){
            //                /*Ejecutamos el Query*/                
            //                $result = $stmt->execute($params); 
            //                if ($result){
            //                    if($stmt->rowCount() > 0){                    
            //                        $mensaje_Transacciones .= "Vancantes en la Propuesta Actualizado. OK.<br/>";
            //                    }
            //                    else{
            //                        $error = $stmt->errorInfo();
            //                        $mensaje_Transacciones .= "No se pudo Actualizar las Vacantes en la Propuesta.<br>"  . $error[2] .'<br>';
            //                        throw new Exception($mensaje_Transacciones);                                                              
            //                    }
            //                }
            //                else{
            //                    $error = $stmt->errorInfo();
            //                    $mensaje_Transacciones .= "No se actualizó las Vacantes en la Propuesta.<br/>"  . $error[2] .'<br>';
            //                    throw new Exception($mensaje_Transacciones);        
            //                }
            //            }
            //            else{
            //                    $error = $stmt->errorInfo();
            //                    $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar las Vacantes en la Propuesta.<br/>"  . $error[2] .'<br>';
            //                    throw new Exception($mensaje_Transacciones);                                
            //            }

            //VERFICAMOS EL ESTATUS DEL DOCUMENTO.
            //            $tsql2="SELECT id_estatus
            //                    FROM inscripcion_propuesta
            //                    WHERE id_inscripcion = ?;";
            //            /* Valor de los parámetros. */
            //            $params2 = array($id_inscripcion);
            //            /* Preparamos la sentencia a ejecutar */
            //            $stmt2 = $conn->prepare($tsql2);
            //            if($stmt2){
            //                /*Ejecutamos el Query*/                
            //                $result2 = $stmt2->execute($params2); 
            //                if (!$result2 === FALSE){
            //                    if($stmt2->rowCount() > 0){                    
            //                        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            //                        $estatus = $row['id_estatus'];                                
            //                    }
            //                    else{
            //                        $mensaje_Transacciones .= "No se pudo Obtener el Estatus de la Inscripción.<br>";
            //                        throw new Exception($mensaje_Transacciones);                                                              
            //                    }
            //                }
            //                else{
            //                    $error = $stmt2->errorInfo();
            //                    $mensaje_Transacciones .= "Error en los parámetros para Obtener el Estatus de la Inscripción.<br/>"  . $error[2] .'<br>';
            //                    throw new Exception($mensaje_Transacciones);        
            //                }
            //            }
            //            else{
            //                    $error = $stmt2->errorInfo();
            //                    $mensaje_Transacciones .= "Error en la sentencia SQL para Obtener el Estatus de la Inscripción.<br/>"  . $error[2] .'<br>';
            //                    throw new Exception($mensaje_Transacciones);                                
            //            }                        
            //FIN VERIFICAMOS EL ESTATUS DEL DOCUMENTO

            //            if($estatus == '3'){ //3.Aceptado
            //                $mensaje_Transacciones .= "Esta Inscripción YA esta Aceptada. Actualice la vista del módulo Mi Titulación Por Propuesta";
            //                throw new Exception($mensaje_Transacciones);                                                
            //            }
            //            
            //BORRAMOS LAS VERSIONES DE LA INSCRIPCION
            //            $tsql2=" DELETE FROM inscripcion_propuesta_version 
            //                    WHERE id_inscripcion = ?;";

            $tsql2 = " UPDATE inscripcion_propuesta_version 
                    SET id_estatus = ?
                    WHERE id_inscripcion = ?;";

            /* Valor de los parámetros. */
            $params2 = array(14, $id_inscripcion); //14. Baja realizada por el Usuario
            /* Preparamos la sentencia a ejecutar */
            $stmt2 = $conn->prepare($tsql2);
            if ($stmt2) {
                /*Ejecutamos el Query*/
                $result2 = $stmt2->execute($params2);
                if ($result2) {
                    if ($stmt2->rowCount() > 0) {
                        $mensaje_Transacciones .= "Versiones de Inscripción de Propuesta De Baja. OK.<br/>";
                    } else {
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un error";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt2->errorInfo();
                $mensaje_Transacciones = "Ocurrió un error";
                throw new Exception($mensaje_Transacciones);
            }

            //BORRAMOS LA INSCRIPCION
            //            $tsql=" DELETE FROM inscripcion_propuesta 
            //                    WHERE id_inscripcion = ?;";    
            $tsql = " UPDATE inscripcion_propuesta 
                    SET id_estatus = ?, nota_baja = ?, fecha_baja = ?
                    WHERE id_inscripcion = ?;";

            /* Valor de los parámetros. */
            $params = array(14, $nota, date('d-m-Y H:i:s'), $id_inscripcion); //14. Baja realizada por el Usuario
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $mensaje_Transacciones .= "La Inscripción se dió de Baja. OK.<br/>";
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un error";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Ocurrió un error";
                throw new Exception($mensaje_Transacciones);
            }
            //BORRAMOS EL ARCHIVO PDF
            $nom_archivo = $id_alumno . '_' .
                $id_carrera . '_' .
                $id_propuesta . '_*.pdf';
            $nom_archivo = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Docs/Inscripcion_A_Propuesta/' . $nom_archivo;
            array_map("unlink", glob($nom_archivo));

            $conn->commit();
            $mensaje_Transacciones = "Inscripción a propuesta, dada de baja correctamente.";

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = ' ** Se dió de Baja de la Inscripción a la Propuesta ' . $id_propuesta . ' **Con Título ' .
                $titulo_propuesta . ' ** Para la Carrera : ' . $id_carrera . ' ** No. de Inscripción : ' . $id_inscripcion . ' --- ' . $nota;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(35); //inscripción a propuesta
            $obj_miBitacora->set_Id_Tipo_Evento(10); // baja
            $obj_miBitacora->set_Id_Usuario_Genera($id_alumno);
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora = '';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);


            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora;
            return json_encode($jsondata);
            exit();
        } catch (Exception $ex) {
            $conn->rollBack();
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            return json_encode($jsondata);
            exit();
        }
    }
    //FIN BORRAR INSCRIPCION DE UNA PROPUESTA

}

//$obj = new d_alumno_Mi_Titulacion_Por_Propuesta();
//echo $obj->Obtener_Mis_Inscripcines('5', '4');
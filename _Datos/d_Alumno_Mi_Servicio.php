<?php

/**
 * Definición de la Capa de Datos para la Clase Alumno Mi Servicio
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Junio 2016
 */
header('Content-Type: text/html; charset=UTF-8');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Servicio_Social.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_mail.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Mail.php');

class d_Alumno_Mi_Servicio
{

    function Obtener_SS_Todos($id_alumno, $id_carrera)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            //            $tsql = "SELECT a.id_ss, a.fecha_inicio_ss, a.duracion_meses_ss, b.descripcion_estatus, a.id_estatus " .
            //                    "FROM servicio_social a INNER JOIN estatus b " .
            //                    "ON a.id_estatus = b.id_estatus " .
            //                    "WHERE a.id_alumno = ? AND a.id_carrera = ? " .
            //                    "ORDER BY id_ss;";

            $tsql = "SELECT a.id_ss, a.fecha_inicio_ss, a.duracion_meses_ss, b.descripcion_estatus, a.id_estatus, a.nota_baja,
                        (SELECT count(id_documento)
                        FROM servicio_social_docs 
                        WHERE id_ss=a.id_ss AND id_documento IN (1,2,3,6) AND id_version=1 AND id_estatus=1) as docs_sin_enviar
                    FROM servicio_social a INNER JOIN estatus b 
                    ON a.id_estatus = b.id_estatus 
                    WHERE a.id_alumno = ? AND a.id_carrera = ?
                    ORDER BY a.fecha_inicio_ss;";

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
                        $jsondata['success'] = false;
                        $jsondata['data'] = array('message' => 'No hay información del Servicio Social.');
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
    } //Fin Obtener

    //TOTALES
    function Obtener_Total_SS($id_estatus, $id_alumno)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT count(a.id_ss) as total5
                    FROM servicio_social a INNER JOIN estatus b 
                    ON a.id_estatus = b.id_estatus 
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
                        //FETCH_OBJ
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $jsondata['data']['registros'][] = $row;
                        }
                        $stmt = null;
                        $conn = null;
                        //echo json_encode($jsondata);
                        //exit();
                        return $jsondata;
                    } else {
                        $jsondata['success'] = false;
                        $jsondata['data'] = array('message' => 'No hay información del Servicio Social.');
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
    }
    //FIN TOTALES

    function Obtener_SS_Id($id_ss)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_ss, a.fecha_inicio_ss, a.fecha_termino_ss, a.duracion_meses_ss, " .
                "a.avance_creditos_ss, a.avance_porcentaje_ss, a.promedio_ss, a.jefe_inmediato_ss," .
                "a.percepcion_mensual_ss, a.id_programa, a.id_tipo_remuneracion," .
                "a.id_tipo_baja, a.id_estatus, a.id_carrera, a.id_alumno, b.descripcion_estatus, " .
                "c.descripcion_pss " .
                "FROM servicio_social a INNER JOIN estatus b " .
                "ON a.id_estatus = b.id_estatus " .
                "INNER JOIN programas_ss c ON a.id_programa = c.id_programa " .
                "WHERE a.id_ss = ? " .
                "ORDER BY id_ss; ";

            /* Valor de los parámetros. */
            $params = array($id_ss);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
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
                        $mensaje_Transacciones = "No hay información del Servicio Social.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "Error en los Parámetros de la sentencia SQL para obtener la información del Servicio Social.<br/>"  . $error[2];
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener la información del Servicio Social.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    } //Fin Obtener

    function Obtener_Datos_Grales($id_ss)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT d.descripcion_carrera, a.id_ss, a.id_alumno, 
                c.nombre_usuario || ' ' || c.apellido_paterno_usuario || ' ' || c.apellido_materno_usuario as nombre, 
                a.fecha_inicio_ss, a.fecha_termino_ss, a.duracion_meses_ss, 
                a.avance_creditos_ss, a.avance_porcentaje_ss, a.promedio_ss, a.jefe_inmediato_ss,
                a.percepcion_mensual_ss, a.id_programa, a.id_tipo_remuneracion,
                a.id_tipo_baja, a.id_estatus, a.id_carrera, b.descripcion_estatus,
                e.telefono_fijo_alumno, e.telefono_celular_alumno, f.descripcion_pss
                FROM servicio_social a 
                 INNER JOIN estatus b ON a.id_estatus = b.id_estatus 
                 INNER JOIN usuarios c ON c.id_usuario = a.id_alumno
                 INNER JOIN carreras d ON a.id_carrera = d.id_carrera
                 INNER JOIN alumnos e ON a.id_alumno = e.id_alumno
                 INNER JOIN programas_ss f ON a.id_programa = f.id_programa
                WHERE a.id_ss = ? ";

            /* Valor de los parámetros. */
            $params = array($id_ss);
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
                        $mensaje_Transacciones = "No hay información del Servicio Social.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener la información del Servicio Social.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    } //Fin Obtener

    function Obtener_Mis_Documentos($id_ss)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            //           $jsondata['success'] = false;
            //           $jsondata['data']= array('message'=>'en obtener ss id');
            //           echo json_encode($jsondata);
            //           exit();  

            $tsql = "SELECT a.id_ss, a.id_documento, a.id_estatus, d.id_carrera, " .
                "c.descripcion_documento, c.descripcion_para_nom_archivo, a.id_version, " .
                "a.fecha_recepcion_doc, b.descripcion_estatus, a.nota, d.id_alumno " .
                "FROM servicio_social_docs a " .
                "INNER JOIN estatus b ON a.id_estatus = b.id_estatus " .
                "INNER JOIN documentos c ON a.id_documento = c.id_documento " .
                "INNER JOIN servicio_social d ON a.id_ss = d.id_ss " .
                "WHERE a.id_ss = ? AND a.id_documento IN (1,2,6) " .
                "ORDER BY a.id_documento, a.fecha_recepcion_doc;";

            /* Valor de los parámetros. */
            $params = array($id_ss);
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
                        $mensaje_Transacciones = "No hay información de los Documentos Enviados para el Servicio Social.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Documentos Enviados para el Servicio Social.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    } //Fin Obtener Mis Documentos Enviados


    function calcular_Fechas_RptBim($fecha_inicio, $duracion)
    {
        $contador1 = 0;
        $num_reportes = 0;
        $f_chas = array();
        $par = true;
        $fecha_termino = '';
        $horas_obligatorias = 480;
        $horas_mensuales_obligatorias = (int) ($horas_obligatorias / $duracion);
        $horas_acumuladas_obligatorias = 0;
        $horas_periodo = 0;

        if ($duracion % 2 == 0) {
            $num_reportes = $duracion / 2;
            $par = true;
        } else {
            $num_reportes = ($duracion / 2 + .5);
            $par = false;
        }

        for ($contador1 = 0; $contador1 < $num_reportes; $contador1++) {
            $f_chas[$contador1][0] = $fecha_inicio;
            $fecha_termino = strtotime('+2 month', strtotime($fecha_inicio));
            $fecha_termino = date('Y-m-j', $fecha_termino);
            $horas_periodo = (int)($horas_mensuales_obligatorias * 2);
            if ($contador1 < $num_reportes - 1) {
                $fecha_termino_rp = strtotime('-1 day', strtotime($fecha_termino));
                $f_chas[$contador1][1] = date('Y-m-j', $fecha_termino_rp);
            } else {
                $horas_periodo = (int)($horas_obligatorias - $horas_acumuladas_obligatorias);
                if ($par == false) {
                    $fecha_termino = strtotime('-1 month', strtotime($fecha_termino));
                    $fecha_termino = date('Y-m-j', $fecha_termino);
                }
                $fecha_termino_rp = $fecha_termino;
                $f_chas[$contador1][1] = $fecha_termino_rp;
            }
            $fecha_inicio = $fecha_termino;
            $horas_acumuladas_obligatorias += $horas_periodo;
            $f_chas[$contador1][2] = $horas_periodo;
        }
        return $f_chas;
    }

    function Agregar($objServicioSocial)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $nuevoConsecutivo = 0;
        $anio = 0;
        $mes = 0;

        try {

            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            $obj_SS = new Servicio_Social();
            $obj_SS = $objServicioSocial;

            $fecha_inicio = strtotime($obj_SS->get_Fecha_Inicio());
            $fecha_inicio = date('d-m-Y', $fecha_inicio);
            $anio = date("Y", strtotime($fecha_inicio));
            $mes = date("m", strtotime($fecha_inicio));


            //            $anio = date("Y", strtotime($obj_SS->get_Fecha_Inicio()));
            //            $mes = date("m", strtotime($obj_SS->get_Fecha_Inicio()));

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            /* Iniciar la transacción. */

            $conn->beginTransaction();

            /*Obtenemos las fechas para los reportes bimestrales*/
            $obj_miServicio = new d_Alumno_Mi_Servicio();
            $calendario_Entrega_RptBim = $obj_miServicio->calcular_Fechas_RptBim($obj_SS->get_Fecha_Inicio(), $obj_SS->get_Duracion_Meses());
            if (!$calendario_Entrega_RptBim) {
                $mensaje_Transacciones = ("Error al Calcular el Calendario de Entregas de los Reportes Bimestrales.");
                throw new Exception($mensaje_Transacciones);
            }
            /* Query para obtener el consecutivo PROVISIONAL del Servicio Social */
            //                $tsql1=" SELECT consecutivo + 1 as siguiente_digito
            //                        FROM servicio_social_contador
            //                        WHERE anio = ? AND mes = ?;
            //                    ";
            $tsql1 = " SELECT consecutivo + 1 as siguiente_digito
                        FROM folios_provisionales
                        WHERE proceso = ?;
                    ";

            /* Valor de los parámetros. */
            //                $params1 = array($anio,$mes);
            $params1 = array('servicio_social');

            /* Preparamos la sentencia a ejecutar */
            $stmt1 = $conn->prepare($tsql1);
            $result1 = $stmt1->execute($params1);
            $clave_SS = '';

            if ($stmt1) {
                /*Ejecutamos el Query*/
                $result1 = $stmt1->execute($params1);
                if ($result1) {
                    if ($stmt1->rowCount() > 0) {
                        $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                        $nuevoConsecutivo = $row['siguiente_digito'];
                        //                            $consecutivo = str_pad($row['siguiente_digito'],3,"0",STR_PAD_LEFT);
                        //                            $clave_SS = date("Y", strtotime($obj_SS->get_Fecha_Inicio())) . "". 
                        //                                    date("m", strtotime($obj_SS->get_Fecha_Inicio())) . "-" .$consecutivo;
                        $clave_SS = $nuevoConsecutivo;
                    } else {
                        $error = $stmt1->errorInfo();
                        $mensaje_Transacciones = "No se puedo obtener el Consecutivo PROVISIONAL para la Clave del Servicio Social.<br/>"  . $error[2];
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt1->errorInfo();
                    $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Consecutivo PROVISIONAL de la Clave del Servicio Social.<br/>"  . $error[2];
                    throw new Exception($mensaje_Transacciones);
                }
            }
            $mensaje_Transacciones = "Clave PROVISIONAL de Servicio Social obtenida. OK.<br/>";
            //Agregamos el Servicio Social a la BD
            //              /* Query parametrizado para el servicio social. */
            $tsql2 = " INSERT INTO servicio_social
                        (id_ss,
                        fecha_inicio_ss,
                        fecha_termino_ss,
                        duracion_meses_ss,
                        avance_creditos_ss,
                        avance_porcentaje_ss,
                        promedio_ss,
                        jefe_inmediato_ss,
                        percepcion_mensual_ss,
                        id_programa,
                        id_tipo_remuneracion,
                        id_tipo_baja,
                        id_estatus,
                        id_carrera,
                        id_alumno,
                        id_previo,
                        id_division)
                        VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?, ?, ?, ?, ?);
                    ";
            /* Valor de los parámetros. */
            $params2 = array(
                $clave_SS,
                $obj_SS->get_Fecha_Inicio(),
                $obj_SS->get_Fecha_Termino(),
                $obj_SS->get_Duracion_Meses(),
                $obj_SS->get_Avance_Creditos(),
                $obj_SS->get_Avance_Porcentaje(),
                $obj_SS->get_Promedio(),
                $obj_SS->get_Jefe_Inmediato(),
                $obj_SS->get_Percepcion_Mensual(),
                $obj_SS->get_Id_Programa(),
                $obj_SS->get_Id_Tipo_Remuneracion(),
                $obj_SS->get_Id_Tipo_Baja(),
                $obj_SS->get_Id_Estatus(),
                $obj_SS->get_Id_Carrera(),
                $obj_SS->get_Id_Alumno(),
                $clave_SS,
                $obj_SS->get_Id_Division()
            );
            /* Preparamos la sentencia a ejecutar */
            $stmt2 = $conn->prepare($tsql2);
            if ($stmt2) {
                /*Ejecutamos el Query*/
                $result2 = $stmt2->execute($params2);
                if (!$result2 === FALSE) {
                    if ($stmt2->rowCount() > 0) {
                        $mensaje_Transacciones .= "Servicio Social Agregado. OK.<br><br>";
                        $mensaje_Transacciones .= "<p style='padding:5px;color:white; background-color:#ff1493;'>AHORA DEBE DE ENVIAR LOS DOCUMENTOS CORRESPONDIENTES.</p>";
                    } else {
                        //                                $conn->rollBack();
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Agregar el Servicio Social<br>."  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros. NO se agregó el Servicio Social.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt2->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL del Servicio Social.<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }

            //Agregamos los documentos que debe autorizar el Administrador para que sea válido el Servicio Social
            /* Query parametrizado para agregar los docs carta acep y historial academ. */
            $tsql3 = " INSERT INTO servicio_social_docs(
                    id_ss, id_documento, id_version, id_estatus, id_division)
                    SELECT ? ,id_documento, 1, 1, ?
                    FROM documentos WHERE id_documento IN (1,2,3,6);
                    ";
            /* Valor de los parámetros. */
            $params3 = array($clave_SS, $obj_SS->get_Id_Division());
            /* Preparamos la sentencia a ejecutar */
            $stmt3 = $conn->prepare($tsql3);
            if ($stmt3) {
                /*Ejecutamos el Query*/
                $result3 = $stmt3->execute($params3);
                if ($result3) {
                    if ($stmt3->rowCount() > 0) {
                        $mensaje_Transacciones .= "Documentos del Servicio Social Agregados. OK.<br/>";
                    } else {
                        $error = $stmt3->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Agregar los Documentos del Servicio Social<br>."  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt3->errorInfo();
                    $mensaje_Transacciones .= "No se agregaron los Documentos del Servicio Social.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt3->errorInfo();
                $mensaje_Transacciones .= "Error en la sentenciaSQL para agregar los Documentos del Servicio Social.<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }
            //Agregamos el Calendario de Reportes Bimestrales a la BD
            /* Query parametrizado para agregar el Calendario de Reportes Bimestrales. */

            $renglones = count($calendario_Entrega_RptBim);
            $columnas = count($calendario_Entrega_RptBim[0]);

            $tsql4 = " INSERT INTO reportes_bimestrales(
                    id_ss, fecha_prog_inicio, fecha_prog_fin, numero_reporte_bi, 
                    id_version, id_estatus, horas_laboradas, horas_obligatorias, nota,id_documento, id_division)
                    VALUES (?,?,?,?,?,?,0,?,'',7,?);
                    ";

            for ($i = 0; $i < $renglones; $i++) {
                $stmt4 = $conn->prepare($tsql4);
                if ($stmt4) {
                    $result4 = $stmt4->execute(array(
                        $clave_SS,
                        $calendario_Entrega_RptBim[$i][0],
                        $calendario_Entrega_RptBim[$i][1],
                        $i + 1,
                        1,
                        1,
                        $calendario_Entrega_RptBim[$i][2],
                        $obj_SS->get_Id_Division()
                    ));
                    if (!$result4 === FALSE) {
                        if ($stmt4->rowCount() > 0) {
                            $mensaje_Transacciones .= "Fecha de Entrega Agregada. OK.<br/>";
                        } else {
                            $error = $stmt4->errorInfo();
                            $mensaje_Transacciones .= "Error al Agregar la Fecha de Entrega.<br>"  . $error[2] . '<br>';
                            throw new Exception($mensaje_Transacciones);
                        }
                    } else {
                        $error = $stmt4->errorInfo();
                        $mensaje_Transacciones .= "Error en los parámetros, NO se Agregó la Fecha de Entrega.<br/>"  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt4->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL de la Fecha de Entrega.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            }

            //Incrementamos el Contador PROVISIONAL para el Servicio Social en la BD
            //              /* Query parametrizado para el incremento del Contador mensual del servicio social. */
            //                $tsql5=" UPDATE servicio_social_contador SET
            //                        consecutivo = " . $nuevoConsecutivo . " " .
            //                        "WHERE anio = ?  AND mes = ?;";
            $tsql5 = " UPDATE folios_provisionales SET
                consecutivo = " . $nuevoConsecutivo . " " .
                "WHERE proceso = ?;";

            /* Valor de los parámetros. */
            //                $params5 = array($anio, $mes);
            $params5 = array("servicio_social");
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
                        $mensaje_Transacciones .= "No se pudo Incrementar el Contador PROVISIONAL<br>."  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt5->errorInfo();
                    $mensaje_Transacciones .= "No se Incrementó el Contador PROVISIONAL del Servicio Social.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt5->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para incrementar el Contador PROVISIONAL del Servicio Social.<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }

            $conn->commit();

            $mensaje_Transacciones = "Adjuntar y enviar los documentos correspondientes.";

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = 'REF. CARRERA/SERV SOC/PROGRAMA ' . $obj_SS->get_Id_Carrera() . "/" . $clave_SS . "/" . $obj_SS->get_Id_Programa();
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(20);
            $obj_miBitacora->set_Id_Tipo_Evento(5);
            $obj_miBitacora->set_Id_Usuario_Genera($obj_SS->get_Id_Alumno());
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($obj_SS->get_Id_Division());

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

    function Actualizar($objServicioSocial)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();

        try {

            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            $obj_SS = new Servicio_Social();
            $obj_SS = $objServicioSocial;

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            //Actualizamos los datos del Servicio Social en la BD
            /* Query parametrizado para el servicio social. */
            $tsql = " UPDATE servicio_social SET " .
                "percepcion_mensual_ss = ?, " .
                "id_tipo_remuneracion = ? " .
                "WHERE id_ss = ?;";
            /* Valor de los parámetros. */
            $params = array(
                $obj_SS->get_Percepcion_Mensual(),
                $obj_SS->get_Id_Tipo_Remuneracion(),
                $obj_SS->get_Id_SS()
            );
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if (!$result === FALSE) {
                    if ($stmt->rowCount() > 0) {
                        $mensaje_Transacciones .= "Servicio Social Actualizado. OK.<br/>";
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Actualizar los datos del Servicio Social<br>."  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se actualizó el Servicio Social.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar los datos del Servicio Social.<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = 'REF. CARRERA/SERV SOC ' . $obj_SS->get_Id_Carrera() . "/" . $obj_SS->get_Id_SS();
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(20);
            $obj_miBitacora->set_Id_Tipo_Evento(15);
            $obj_miBitacora->set_Id_Usuario_Genera($obj_SS->get_Id_Alumno());
            $obj_miBitacora->set_Id_Usuario_Destinatario('');
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($obj_SS->get_Id_Division());

            $resultado_Bitacora = '';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);

            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora;
            echo json_encode($jsondata);
            exit();
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();
        }
    }


    function Borrar_SS($id_SS, $id_usuario, $nvo_id_estatus, $nvo_id_baja, $nota, $id_carrera, $id_division)
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

            /*Cambiamos el Estatus de los Documentos del Servicio Social */
            /* Query para para cambiar el estatus de los Docs del SS */
            $tsql1 = "UPDATE servicio_social_docs 
                    SET id_estatus = ?
                    WHERE id_ss = ?;";

            $params1 = array($nvo_id_estatus, $id_SS);

            /* Preparamos la sentencia a ejecutar */
            $stmt1 = $conn->prepare($tsql1);
            if ($stmt1) {
                /*Ejecutamos el Query*/
                $result1 = $stmt1->execute($params1);
                $mensaje_Transacciones = "Cambio de Estatus a los Documentos de Servicio Social. OK<br/>";
            } else {
                $error = $stmt1->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para Cambair el Estatus de los Documentos de Aceptación de Servicio Social.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }

            /*Cambiamos el Estatus de los Reportes Bimestrales del Servicio Social*/
            /* Query para para cambiar el estatus de los Rpt Bim del SS */
            $tsql2 = "UPDATE reportes_bimestrales 
                    SET id_estatus = ?
                    WHERE id_ss = ?;";
            $params2 = array($nvo_id_estatus, $id_SS);

            /* Preparamos la sentencia a ejecutar */
            $stmt2 = $conn->prepare($tsql2);
            if ($stmt2) {
                /*Ejecutamos el Query*/
                $result2 = $stmt2->execute($params2);
                $mensaje_Transacciones .= "Cambio de Estatus a los Reportes Bimestrales de Servicio Social. OK<br/>";
            } else {
                $error = $stmt2->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para Cambair el Estatus de los Reportes Bimestrales de Servicio Social.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }

            /*Cambiamos el Estatus del Servicio Social*/
            /* Query para para cambiar el estatus de los Rpt Bim del SS */
            $tsql3 = "UPDATE servicio_social
                    SET id_estatus = ?,
                    id_tipo_baja = ?,
                    nota_baja = ?
                    WHERE id_ss = ?;";
            $params3 = array($nvo_id_estatus, $nvo_id_baja, $nota, $id_SS);

            /* Preparamos la sentencia a ejecutar */
            $stmt3 = $conn->prepare($tsql3);
            if ($stmt3) {
                /*Ejecutamos el Query*/
                $result3 = $stmt3->execute($params3);
                $mensaje_Transacciones .= "Cambio de Estatus del Servicio Social. OK<br/>";
            } else {
                $error = $stmt3->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para Cambair el Estatus del Servicio Social.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }

            $conn->commit();

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = 'REF. CARRERA/SERV SOC ' . $id_carrera . "/" . $id_SS . " --- " . $nota;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(20); // Mi servicio social
            $obj_miBitacora->set_Id_Tipo_Evento(10); // Baja
            $obj_miBitacora->set_Id_Usuario_Genera($id_usuario);
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

    function Solicitar_Baja_SS($id_SS, $id_usuario, $nvo_id_estatus, $nvo_id_baja, $nota, $id_carrera)
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

            /*Cambiamos el Estatus del Servicio Social*/
            $tsql3 = "UPDATE servicio_social
                    SET id_estatus = ?,
                    id_tipo_baja = ?,
                    nota_baja = ?
                    WHERE id_ss = ?;";
            $params3 = array($nvo_id_estatus, $nvo_id_baja, $nota, $id_SS);

            /* Preparamos la sentencia a ejecutar */
            $stmt3 = $conn->prepare($tsql3);
            if ($stmt3) {
                /*Ejecutamos el Query*/
                $result3 = $stmt3->execute($params3);
                $mensaje_Transacciones .= "Cambio de Estatus del Servicio Social. OK<br><br>";
                $mensaje_Transacciones .= "<p style='padding:5px;color:white; background-color:#ff1493;'>DEBE DE ESPERAR A QUE EL ADMINISTRADOR REALICE LA BAJA DE ESTE SERVICIO SOCIAL.</p>";
            } else {
                $error = $stmt3->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para Cambair el Estatus del Servicio Social.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }

            $conn->commit();

            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones;
            return json_encode($jsondata);
            exit();
        } catch (Exception $ex) {
            $conn->rollBack();
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();
        }
    }

    function Actualizar_Estatus_Doc_Enviado($id_SS, $id_Doc, $id_Version, $id_Estatus)
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
            $tsql = " UPDATE servicio_social_docs SET " .
                "id_estatus =  ?, " .
                "fecha_recepcion_doc = ? " .
                "WHERE id_ss = ? AND " .
                "id_documento =? AND id_version = ?;";
            /* Valor de los parámetros. */
            $params = array(
                $id_Estatus,
                date('d-m-Y H:i:s'),
                $id_SS,
                $id_Doc,
                $id_Version
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

    //Retorna el número de documentos que ya fueron Aceptados por el Administrador
    function N_Documentos_Aceptados($id_SS)
    {
        $mensaje_Transacciones = 0;
        $conn = '';
        //        $jsondata = array();

        try {

            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            //Obtenemos el número de documentos Aceptados
            /* Query parametrizado. */
            $tsql = " SELECT count(id_documento) as cuantos
                    FROM servicio_social_docs
                    WHERE id_ss= ? AND id_estatus =3;";  //3. Aceptado                    
            /* Valor de los parámetros. */
            $params = array($id_SS);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        //                        return; json_encode($jsondata);
                        return ($row['cuantos']);
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
            return ($ex->getMessage());
            exit();
        }
    }

    //Actualizar el estatus del Servicio Social si sus documentos ya están Aceptados
    function Actualizar_Estatus_Servicio_Social($id_ss, $fecha_inicio_ss, $id_Usr_Destinatario, $correo_usr, $carrera_usr, $id_administrador, $id_division)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $nuevoConsecutivo = 0;
        $anio = 0;
        $mes = 0;
        $documentos_servicio_social = '';
        $clave_SS = '';

        //        $obj_d_Alumno_Mi_Servicio = new d_Alumno_Mi_Servicio();
        //        $respuesta =  ($obj_d_Alumno_Mi_Servicio->N_Documentos_Aceptados($id_ss));

        //           $jsondata['success'] = true;
        //           $jsondata['data']= array('message'=> $respuesta);
        //           echo json_encode($jsondata);
        //           exit();

        //        if($respuesta == 2){
        //Actualiamos el estatus del Servicio Social como Aceptado
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }


            $respuesta = 0;
            //Obtenemos el número de documentos Aceptados
            /* Query parametrizado. */
            $tsql9 = " SELECT count(id_documento) as cuantos
                    FROM servicio_social_docs
                    WHERE id_ss= ? AND id_estatus =3;";  //3. Aceptado                    
            /* Valor de los parámetros. */
            $params9 = array($id_ss);
            /* Preparamos la sentencia a ejecutar */
            $stmt9 = $conn->prepare($tsql9);
            if ($stmt9) {
                /*Ejecutamos el Query*/
                $result9 = $stmt9->execute($params9);
                if ($result9) {
                    if ($stmt9->rowCount() > 0) {
                        $row = $stmt9->fetch(PDO::FETCH_ASSOC);
                        //                        return; json_encode($jsondata);
                        $respuesta = ($row['cuantos']);
                    }
                }
            }

            //OBTENEMOS LOS DOCUMENTOS YA ACEPTADOS
            $tsql9 = " SELECT c.id_alumno, c.id_carrera, a.id_ss, a.id_version, d.descripcion_para_nom_archivo
                    FROM servicio_social_docs a 
                            INNER JOIN estatus b ON a.id_estatus = b.id_estatus
                            INNER JOIN servicio_social c ON a.id_ss = c.id_ss
                            INNER JOIN documentos d ON a.id_documento = d.id_documento
                    WHERE a.id_ss = ? AND a.id_documento IN (1,2,6)
                            AND a.id_estatus= 3 AND a.id_version = (SELECT max(e.id_version)
                                                                            FROM servicio_social_docs e
                                                                            WHERE e.id_ss= a.id_ss AND e.id_documento = a.id_documento);";
            /* Valor de los parámetros. */
            $params9 = array($id_ss);
            /* Preparamos la sentencia a ejecutar */
            $stmt9 = $conn->prepare($tsql9);
            if ($stmt9) {
                /*Ejecutamos el Query*/
                $result9 = $stmt9->execute($params9);
                if ($result9) {
                    if ($stmt9->rowCount() > 0) {
                        while ($row = $stmt9->fetch(PDO::FETCH_ASSOC)) {
                            $documentos_servicio_social .= $row['id_alumno'] . ',' .
                                $row['id_carrera'] . ',' .
                                $row['id_ss'] . ',' .
                                $row['id_version'] . ',' .
                                $row['descripcion_para_nom_archivo'] . '|';
                        }
                        $documentos_servicio_social = substr($documentos_servicio_social, 0, strlen($documentos_servicio_social) - 1);
                    }
                }
            }

            //FIN OBTENEMOS LOS DOCUMENTOS YA ACEPTADOS

            if ($respuesta == 3) {

                //Obtenemos la nueva clave para el SS ya Aceptado           
                $fecha_inicio = strtotime($fecha_inicio_ss);
                $fecha_inicio = date('d-m-Y', $fecha_inicio);
                $anio = date("Y", strtotime($fecha_inicio));
                $mes = date("m", strtotime($fecha_inicio));

                /* Iniciar la transacción. */
                $conn->beginTransaction();

                /* Query para obtener el consecutivo PROVISIONAL del Servicio Social */
                $tsql1 = " SELECT consecutivo + 1 as siguiente_digito
                        FROM servicio_social_contador
                        WHERE anio = ? AND mes = ?;
                    ";
                /* Valor de los parámetros. */
                $params1 = array($anio, $mes);
                /* Preparamos la sentencia a ejecutar */
                $stmt1 = $conn->prepare($tsql1);
                $result1 = $stmt1->execute($params1);
                $clave_SS = '';

                if ($stmt1) {
                    /*Ejecutamos el Query*/
                    $result1 = $stmt1->execute($params1);
                    if ($result1) {
                        if ($stmt1->rowCount() > 0) {
                            $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                            $nuevoConsecutivo = $row['siguiente_digito'];
                            $consecutivo = str_pad($row['siguiente_digito'], 3, "0", STR_PAD_LEFT);
                            $clave_SS = date("Y", strtotime($fecha_inicio_ss)) . "" .
                                date("m", strtotime($fecha_inicio_ss)) . "-" . $consecutivo;
                            //                            $clave_SS = $consecutivo;
                        } else {
                            $error = $stmt1->errorInfo();
                            $mensaje_Transacciones = "No se puedo obtener el Consecutivo para la Clave del Servicio Social.<br/>"  . $error[2];
                            throw new Exception($mensaje_Transacciones);
                        }
                    } else {
                        $error = $stmt1->errorInfo();
                        $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Consecutivo de la Clave del Servicio Social.<br/>"  . $error[2];
                        throw new Exception($mensaje_Transacciones);
                    }
                }
                $mensaje_Transacciones = "Clave Definitiva de Servicio Social obtenida. OK.<br/>";

                //Actualizamos el Servicio Social con su Clave DEFINITIVA y estatus ACEPTADO
                /* Query parametrizado. */
                $tsql = " UPDATE servicio_social SET " .
                    "id_ss = ?, " .
                    "id_estatus =  ? " .
                    "WHERE id_ss = ?;";
                /* Valor de los parámetros. */
                $params = array($clave_SS, 3, $id_ss);  //3. Aceptado
                /* Preparamos la sentencia a ejecutar */
                $stmt = $conn->prepare($tsql);

                if ($stmt) {
                    /*Ejecutamos el Query*/
                    $result = $stmt->execute($params);
                    if ($result) {
                        if ($stmt->rowCount() > 0) {
                            $mensaje_Transacciones .= "Estatus del Servicio Social Actualizado. OK.<br/>";
                        } else {
                            $error = $stmt->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Actualizar el Estatus del Servicio Social.<br>."  . $error[2] . '<br>';
                            throw new Exception($mensaje_Transacciones);
                        }
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se actualizó el Estatus del Servicio Social.<br/>"  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Estatus del Servicio Social.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }

                //Incrementamos el Contador para el Servicio Social en la BD
                /* Query parametrizado para el incremento del Contador mensual del servicio social. */
                $tsql5 = " UPDATE servicio_social_contador SET
                        consecutivo = " . $nuevoConsecutivo . " " .
                    "WHERE anio = ?  AND mes = ?;";
                /* Valor de los parámetros. */
                $params5 = array($anio, $mes);
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
                            $mensaje_Transacciones .= "No se pudo Incrementar el Contador PROVISIONAL<br>."  . $error[2] . '<br>';
                            throw new Exception($mensaje_Transacciones);
                        }
                    } else {
                        $error = $stmt5->errorInfo();
                        $mensaje_Transacciones .= "No se Incrementó el Contador PROVISIONAL del Servicio Social.<br/>"  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt5->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para incrementar el Contador PROVISIONAL del Servicio Social.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }

                //RENOMBRAMOS LOS ARCHIVOS CON LA NUEVA CLAVE DEL SERV SOC 
                if (strlen($documentos_servicio_social) > 0) {
                    $arr_lista_campos_Documento = preg_split("/[|]/", $documentos_servicio_social);
                    $renglones = count($arr_lista_campos_Documento);
                    for ($i = 0; $i < $renglones; $i++) {
                        $arr_lista_campos = preg_split("/[,]/", $arr_lista_campos_Documento[$i]);
                        $nom_archivo_actual = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Docs/Servicio_Social/' .
                            $arr_lista_campos[0] . '_' .
                            $arr_lista_campos[1] . '_' .
                            $id_ss . '_' .
                            $arr_lista_campos[3] . '_' .
                            $arr_lista_campos[4] . '.pdf';
                        $nom_archivo_nuevo = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Docs/Servicio_Social/' .
                            $arr_lista_campos[0] . '_' .
                            $arr_lista_campos[1] . '_' .
                            $clave_SS . '_' .
                            $arr_lista_campos[3] . '_' .
                            $arr_lista_campos[4] . '.pdf';

                        if (file_exists($nom_archivo_actual)) {
                            rename($nom_archivo_actual, $nom_archivo_nuevo);
                        }
                    }
                }


                $conn->commit();

                $mensaje_Transacciones = "Servicio Social aprobado, clave ="  . $clave_SS;

                //REGISTRO EN BITACORA Y ENVIO DE MAIL
                $id_tipo_evento = 25; //aprobación
                $id_tema_evento = 100; // aprobar SS
                $descripcion_evento = "SERV SOC. " . $id_ss . " ** CARRERA " . $carrera_usr . " ** APROBADO ***" .
                    " *** CLAVE SERVICIO SOCIAL DEFINITIVA " . $clave_SS;
                $descripcion_Correo = "Su Servicio Social " . $id_ss . " ha sido <b>APROBADO</b>" .
                    "<br> *** CLAVE SERVICIO SOCIAL DEFINITIVA " . $clave_SS;

                $obj_Bitacora = new d_Usuario_Bitacora();
                $obj_miBitacora = new Bitacora();

                $descripcionEvento = $descripcion_evento;
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
                $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento);
                $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
                $obj_miBitacora->set_Id_Usuario_Destinatario($id_Usr_Destinatario);
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($id_division);

                $resultado_Bitacora = '';
                $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
                sleep(1);

                $obj_miBitacora = new Bitacora();

                $descripcionEvento = $descripcion_Correo;
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
                $obj_miBitacora->set_Id_Tipo_Evento(50);  //envio de mail
                $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
                $obj_miBitacora->set_Id_Usuario_Destinatario($id_Usr_Destinatario);
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($id_division);

                $resultado_Bitacora = '';
                $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);

                $obj = new d_mail();
                $mi_mail = new Mail();
                $mensaje = $descripcion_Correo;

                $mi_mail->set_Correo_Destinatarios($correo_usr);
                $mi_mail->set_Asunto('AVISO APROBACIÓN DE SERVICIO SOCIAL');
                $mi_mail->set_Mensaje($mensaje);
                $respuesta_mail = $obj->Envair_Mail($mi_mail);



                $conn = null;
                $jsondata['success'] = true;
                $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora; //;
                echo json_encode($jsondata);
                exit();
            } else {
                $jsondata['success'] = true;
                $jsondata['data']['message'] = 'Tiene documentos pendientes de aceptar para tramitar el Servicio Social.';
                echo json_encode($jsondata);
                exit();
            }
        } catch (Exception $ex) {
            $conn->rollBack();
            $conn = null;
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();
        }
        //        }
        //        else{
        //            $jsondata['success'] = true;
        //            $jsondata['data']['message'] = 'Hay Documentos PENDIENTES por ACEPTAR, para que el Servicio Social sea ACEPTADO.';
        //            echo json_encode($jsondata);
        //            exit();                             
        //        }
    }

    function Actualizar_Termino_Servicio_Social($id_SS, $id_estatus, $id_Usr_Destinatario, $correo_usr, $carrera_usr, $id_administrador, $id_division)
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

            //Actualizamos el estatus del Servicio Social
            /* Query parametrizado. */
            $tsql = " UPDATE servicio_social SET " .
                "id_estatus =  ? " .
                "WHERE id_ss = ?;";
            /* Valor de los parámetros. */
            $params = array($id_estatus, $id_SS);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $mensaje_Transacciones .= "";
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Actualizar el Estatus del Servicio Social a TERMINADO.<br>."  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se actualizó el Estatus del Servicio Social a TERMINADO.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar el Estatus del Servicio Social a TERMINADO.<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }

            $mensaje_Transacciones = "Servicio Social concluido.";

            //REGISTRO EN BITACORA Y ENVIO DE MAIL
            $id_tipo_evento = 25; //aprobación
            $id_tema_evento = 110; // aprobar Carta de Término
            $descripcion_evento = "SERV SOC. " . $id_SS . " ** CARRERA " . $carrera_usr . " ** TERMINADO ***";
            $descripcion_Correo = "Su Servicio Social " . $id_SS . " ha sido <b>TERMINADO</b>";

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_evento;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento($id_tema_evento);
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_Usr_Destinatario);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora = '';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);

            $obj = new d_mail();
            $mi_mail = new Mail();
            $mensaje = $descripcion_Correo;

            $mi_mail->set_Correo_Destinatarios($correo_usr);
            $mi_mail->set_Asunto('AVISO TERMINACIÓN DE SERVICIO SOCIAL');
            $mi_mail->set_Mensaje($mensaje);
            $respuesta_mail = $obj->Envair_Mail($mi_mail);

            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $mensaje;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento(50);  //envio de mail
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_Usr_Destinatario);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora = '';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);

            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora; // . $respuesta_mail;
            return json_encode($jsondata);
            exit();
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            return json_encode($jsondata);
            exit();
        }
    }

    function Actualizar_Aceptacion_Doc_(
        $id_SS,
        $id_Doc,
        $id_Version,
        $id_Estatus,
        $id_administrador,
        $nota,
        $id_Usr_Destinatario,
        $correo_usr,
        $carrera_usr,
        $desc_documento,
        $id_division
    ) {
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $id_tema = 0;

        if ($id_Doc == 1) {
            $id_tema = 110;
        } else {
            $id_tema = 100;
        }

        $id_tipo_evento = 25; //aprobación
        $id_tema_evento = $id_tema; // aprobar SS
        $descripcion_evento = "Documento " . $desc_documento . " Versión " . $id_Version .
            " ** SERV SOC. " . $id_SS . " ** CARRERA " . $carrera_usr;
        $descripcion_Correo = "Su Documento " . $desc_documento . " Versión " . $id_Version .
            "<br>** Con Serv Soc " . $id_SS . " ** Y Carrera " . $carrera_usr . ' ha sido <b>ACEPTADO</b><br>';

        try {

            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            /*Iniciar la transacción. */
            $conn->beginTransaction();

            //Actualizamos el estatus del Documento especificado
            /* Query parametrizado. */
            $tsql = " UPDATE servicio_social_docs SET " .
                "id_estatus =  ?, " .
                "fecha_verifico_doc = ?, " .
                "nota = ?, " .
                "id_administrador = ? " .
                "WHERE id_ss = ? AND " .
                "id_documento =? AND id_version = ?;";
            /* Valor de los parámetros. */
            $params = array(
                $id_Estatus,
                date('d-m-Y H:i:s'),
                $nota,
                $id_administrador,
                $id_SS,
                $id_Doc,
                $id_Version
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

            //Agregamos una nueva versión del Documento Rechazado para que el Alumno Reenvíe el Documento
            if ($id_Estatus == 4) { //4. Rechazado
                $id_tipo_evento = 30;    //rechazo
                $descripcion_evento = "Documento " . $desc_documento . " Versión " . $id_Version .
                    " ** SERV SOC. " . $id_SS . " ** CARRERA " . $carrera_usr;
                $descripcion_Correo = "Su Documento " . $desc_documento . " Versión " . $id_Version .
                    " <br>Con Serv Soc " . $id_SS . " Y Carrera " . $carrera_usr . ' ha sido <b>RECHAZADO</b> ' .
                    " <br>Se ha generado un nuevo número de versión para que pueda reenvíarlo. <br>";

                /* Query parametrizado. */
                $tsql2 = " INSERT INTO servicio_social_docs(
                    id_ss, id_documento, id_version, id_estatus, id_division)
                    VALUES(?,?,?,?,?);";
                /* Valor de los parámetros. */
                $params2 = array($id_SS, $id_Doc, $id_Version + 1, 1, $id_division);
                /* Preparamos la sentencia a ejecutar */
                $stmt2 = $conn->prepare($tsql2);
                if ($stmt2) {
                    /*Ejecutamos el Query*/
                    $result2 = $stmt2->execute($params2);
                    if ($result2) {
                        if ($stmt2->rowCount() > 0) {
                            $mensaje_Transacciones .= "Nuevo Documento Agregado para su Reenvío. OK.<br/>";
                        } else {
                            $error = $stmt2->errorInfo();
                            $mensaje_Transacciones .= "No se pudo Agregar el Nuevo Documento para su Reenvío<br>."  . $error[2] . '<br>';
                            throw new Exception($mensaje_Transacciones);
                        }
                    } else {
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "No se puedo Agregar el Nuevo Documento para su Reenvío.<br/>"  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL para Agregar el Nuevo Documento para su Reenvío.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
                //BORRAMOS EL DOCUMENTO RECHAZADO DE SERV SOCIAL
                $archivo_pdf = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Docs/Servicio_Social/' .
                    $id_Usr_Destinatario . '_' . $carrera_usr . '_' . $id_SS . '_' .
                    $id_Version . '_' . $desc_documento . '.pdf';

                if ($id_Doc == 3) { //3.Carta de Termino
                    $archivo_pdf = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Docs/Carta_Terminacion/' .
                        $id_Usr_Destinatario . '_' . $carrera_usr . '_' . $id_SS . '_' .
                        $id_Version . '_' . $desc_documento . '.pdf';
                }
                if (file_exists($archivo_pdf)) {
                    unlink($archivo_pdf);
                }
            }

            $conn->commit();



            //  En la base de datos 4 es el código para rechazado.
            //  En la base de datos id_Doc = 3 es una carta de terminación
            if ($id_Estatus == 4) {
                if ($id_Doc == 3) {
                    $mensaje_Transacciones = "Carta de terminación rechazada.";
                } else {
                    $mensaje_Transacciones = "Documento rechazado.";
                }
            } else {
                if ($id_Doc == 3) {
                    $mensaje_Transacciones = "Servicio Social concluido.";
                } else {
                    $mensaje_Transacciones = "Documento aceptado.";
                }
            }

            /*
            //A BITACORA EL MOVIMIENTO DE ACT DEL ESTATUS DEL DOC
            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_evento . ' --- ' . $nota;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento);
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_Usr_Destinatario);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);            
            sleep(1);

            $obj_miBitacora = new Bitacora();
            
            //A BITACORA EL MAIL ENVIADO
            $descripcionEvento = $descripcion_Correo . ' --- ' . $nota;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento(50);
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_Usr_Destinatario);
            $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
            $obj_miBitacora->set_Id_Division($id_division);

            $resultado_Bitacora ='';
            $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);  
            sleep(1);
            */

            //MANDAMOS EL MAIL
            /*
            $obj = new d_mail();
            $mi_mail = new Mail();
            $mensaje= $descripcion_Correo . ' --- ' . $nota;
                                   
            $mi_mail->set_Correo_Destinatarios($correo_usr);
            $mi_mail->set_Correo_Copia_Oculta('');
            $mi_mail->set_Asunto('AVISO APROBACIÓN DE DOCUMENTOS PARA SERVICIO SOCIAL');
            $mi_mail->set_Mensaje($mensaje);
            $respuesta_mail = $obj->Envair_Mail($mi_mail);
            */
            $conn = null;
            $jsondata['success'] = true;
            //$jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora; 
            $jsondata['data']['message'] = $mensaje_Transacciones;
            //$respuesta_mail;
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

//para probar el metodo Agregar
//$obj_ = new d_Alumno_Mi_Servicio();
//                               
//$objServicioSocial = new Servicio_Social();
//$objServicioSocial->set_Fecha_Inicio(date('Y-m-d',strtotime('25-06-2016')));
//$objServicioSocial->set_Duracion_Meses(7);
////$objServicioSocial->set_Fecha_Inicio('2016-06-25');
//$fecha_termino=strtotime ( '+' . $objServicioSocial->get_Duracion_Meses() . ' month', strtotime ($objServicioSocial->get_Fecha_Inicio()));  
//$objServicioSocial->set_Id_SS(110);
//$objServicioSocial->set_Fecha_Termino(date('Y-m-j',$fecha_termino));
//$objServicioSocial->set_Avance_Creditos(200);
//$objServicioSocial->set_Avance_Porcentaje(80.5);
//$objServicioSocial->set_Promedio(8.5);
//$objServicioSocial->set_Jefe_Inmediato('erika padilla');
//$objServicioSocial->set_Percepcion_Mensual(150.80);
//$objServicioSocial->set_Id_Programa("2015-10/39-287");
//$objServicioSocial->set_Id_Tipo_Remuneracion(6);
//$objServicioSocial->set_Id_Tipo_Baja(5);    
//$objServicioSocial->set_Id_Estatus(5);
//$objServicioSocial->set_Id_Carrera(110);
//$objServicioSocial->set_Id_Alumno('086198516');
//
//echo $obj_->Agregar($objServicioSocial);

//$obj = new d_Alumno_Mi_Servicio();
//echo $obj->N_Documentos_Aceptados('100');
//echo $obj->Actualizar_Aceptacion_Doc_('100', 2, 1, 3, 'admin', '');
//echo $obj->Actualizar_Estatus_Servicio_Social('100', '2016-03-28');

//$obj =  new d_Alumno_Mi_Servicio();
//echo $obj->Obtener_Datos_Grales('201603-021');

//$obj = new d_Alumno_Mi_Servicio();
//print_r($obj->calcular_Fechas_RptBim(date('Y-m-d',strtotime('2016-03-28')), 9));

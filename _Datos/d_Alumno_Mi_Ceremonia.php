<?php

/**
 * Definición de la Capa de Datos para las Inscripciones a una Propuesta Por Ceremonia
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */

header('Content-Type: text/html; charset=UTF-8');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Ceremonia.php');

class d_Alumno_Mi_Ceremonia
{

    function Obtener_Mis_Documentos($id_ceremonia)
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

            $tsql = "SELECT a.id_ceremonia, a.id_documento, a.version, a.id_estatus, 
                    c.descripcion_documento, c.descripcion_para_nom_archivo,
                    a.fecha_recepcion, b.descripcion_estatus, a.nota_admin, a.nota_coordinador, 
                    d.id_alumno, d.id_carrera
                    FROM ceremonia_docs a 
                    INNER JOIN estatus b ON a.id_estatus = b.id_estatus 
                    INNER JOIN documentos c ON a.id_documento = c.id_documento 
                    INNER JOIN inscripcion_ceremonia d ON a.id_ceremonia = d.id_ceremonia 
                    WHERE a.id_ceremonia = ?
                    ORDER BY a.id_documento, a.version;";

            /* Valor de los parámetros. */
            $params = array($id_ceremonia);
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
                        $mensaje_Transacciones = "No hay información de los Documentos Enviados para la Ceremonia.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "Error en los parámetros para obtener los Documentos Enviados de la Ceremonia.<br/>"  . $error[2];
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Documentos Enviados para el Ceremonia.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    } //Fin Obtener Mis Documentos Enviados

    //OBTENEMOS LA CEREMONIA
    function Obtener_Ceremonia($id_ceremonia)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_ceremonia, a.fecha_alta, a.sedes, a.diplomados_cursos, a.materias, 
                        a.programa_posgrado, a.nombre_articulo, a.nombre_revista, a.id_alumno, 
                        a.id_carrera, a.id_estatus, a.id_tipo_propuesta, b.descripcion_tipo_propuesta
                   FROM inscripcion_ceremonia a
                   INNER JOIN tipos_propuesta b ON a.id_tipo_propuesta = b.id_tipo_propuesta
                   WHERE a.id_ceremonia = ?;
                 ";

            /* Valor de los parámetros. */
            $params = array($id_ceremonia);
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
                        $mensaje_Transacciones = "No hay información de la Ceremonias.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "Error en los parámetros para Obtener la Ceremonia.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para Obtener la Ceremonia.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LA CEREMONIA

    //OBTENEMOS LAS CEREMONIAS DEL ALUMNO
    function Obtener_Mis_Ceremonias($id_alumno, $id_carrera)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_ceremonia, b.descripcion_tipo_propuesta, a.fecha_alta, 
                    c.descripcion_estatus,a.id_tipo_propuesta, a.id_estatus, a.id_carrera, a.id_alumno, a.llenoFechaEstimada, nota_baja
                    FROM inscripcion_ceremonia a
                            INNER JOIN tipos_propuesta b ON a.id_tipo_propuesta = b.id_tipo_propuesta
                            INNER JOIN estatus c ON a.id_estatus = c.id_estatus
                    WHERE a.id_alumno = ? AND a.id_carrera = ?
                    ORDER BY a.fecha_alta";

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
                        $mensaje_Transacciones = "No hay información de Inscripciones a Ceremonias.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "Error en los parámetros de Inscripción a Ceremonia.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener las Inscripciones a Ceremonia.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LAS CEREMONIAS DEL ALUMNO

    //OBTENEMOS LAS CEREMONIAS DEL ALUMNO
    function Obtener_Modalidades($id_alumno, $id_carrera)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_tipo_propuesta, a.descripcion_tipo_propuesta, a.id_tipo_titulacion
                    FROM tipos_propuesta a
                    WHERE a.id_tipo_propuesta NOT IN (SELECT id_tipo_propuesta
                                                    FROM inscripcion_ceremonia 
                                                    WHERE id_alumno = ? AND id_carrera= ?)
                          AND a.id_tipo_titulacion = 2
                    ORDER BY a.id_tipo_titulacion, a.descripcion_tipo_propuesta";

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
                        $mensaje_Transacciones = "No hay información de Modalidades para Ceremonia.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "Error en los parámetros de Modalidades para Ceremonia.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener las Modalidades para Ceremonia.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LAS MODALIDADES

    //AGREGAMOS LA CEREMONIA SELECCIONADA Y LOS DOCUMENTOS QUE DEBERÁ ENVIAR
    function Agregar($id_alumno, $id_carrera, $id_modalidad, $desc_modalidad, $id_division)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $nuevoConsecutivo = 0;

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            /* Iniciar la transacción. */

            $conn->beginTransaction();

            /* Query para obtener el consecutivo PROVISIONAL Para la Ceremonia */
            $tsql1 = " SELECT consecutivo + 1 as siguiente
                        FROM folios_provisionales
                        WHERE proceso = ?;
                    ";

            /* Valor de los parámetros. */
            $params1 = array('inscripcion_ceremonia');

            /* Preparamos la sentencia a ejecutar */
            $stmt1 = $conn->prepare($tsql1);
            $result1 = $stmt1->execute($params1);
            $clave_Ceremonia = '';

            if ($stmt1) {
                /*Ejecutamos el Query*/
                $result1 = $stmt1->execute($params1);
                if (!$result1 === FALSE) {
                    if ($stmt1->rowCount() > 0) {
                        $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                        $nuevoConsecutivo = $row['siguiente'];
                        $clave_Ceremonia = $nuevoConsecutivo;
                    } else {
                        $error = $stmt1->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un problema";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt1->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un problema";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt1->errorInfo();
                $mensaje_Transacciones = "Error al procesar la transacción";
                throw new Exception($mensaje_Transacciones);
            }

            $mensaje_Transacciones = "";

            //Agregamos la Ceremonia a la BD
            //              /* Query parametrizado para el servicio social. */
            $tsql2 = " INSERT INTO inscripcion_ceremonia
                        (id_ceremonia,
                        id_previo,
                        id_tipo_propuesta,
                        fecha_alta,
                        id_estatus,
                        id_carrera,
                        id_alumno,
                        id_tipo_baja,
                        id_division)
                        VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, ?);
                    ";
            /* Valor de los parámetros. */
            $params2 = array(
                $clave_Ceremonia,
                $clave_Ceremonia,
                $id_modalidad,
                date('d-m-Y H:i:s'),
                2,
                $id_carrera,
                $id_alumno,
                5,
                $id_division
            );
            /* Preparamos la sentencia a ejecutar */
            $stmt2 = $conn->prepare($tsql2);
            if ($stmt2) {
                /*Ejecutamos el Query*/
                $result2 = $stmt2->execute($params2);
                if (!$result2 === FALSE) {
                    if ($stmt2->rowCount() > 0) {
                        //$mensaje_Transacciones .= "Ceremonia Agregada. OK.<br><br>";
                        $mensaje_Transacciones = "Ceremonia agregada correctamente.";
                        //$mensaje_Transacciones .= "<p style='padding:5px;color:white; background-color:#ff1493;'>AHORA DEBE DE ENVIAR LOS DOCUMENTOS CORRESPONDIENTES.</p>";
                    } else {
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones = "No se pudo Agregar la Ceremonia.";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un error al procesar los datos";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt2->errorInfo();
                $mensaje_Transacciones = "Ocurrió un error al procesar los datos";
                throw new Exception($mensaje_Transacciones);
            }

            //Agregamos los documentos que son necesarios para el trámite
            $tsql3 = " INSERT INTO ceremonia_docs(
                    id_ceremonia, id_documento, version, id_estatus, id_division)
                    SELECT ?, id_documento, ?, ?, ?
                        FROM documentos_modalidad
                        WHERE id_tipo_propuesta = ?";
            /* Valor de los parámetros. */
            $params3 = array($clave_Ceremonia, 1, 1, $id_modalidad, $id_division);
            /* Preparamos la sentencia a ejecutar */
            $stmt3 = $conn->prepare($tsql3);
            if ($stmt3) {
                /*Ejecutamos el Query*/
                $result3 = $stmt3->execute($params3);
                if (!$result3 === FALSE) {
                    if ($stmt3->rowCount() > 0) {
                        //$mensaje_Transacciones .= "Documentos para la Ceremonia Agregados.";
                    } else {
                        $error = $stmt3->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error al procesar la ceremonia";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt3->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un error al procesar la ceremonia";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt3->errorInfo();
                $mensaje_Transacciones = "Ocurrió un error al procesar la ceremonia";
                throw new Exception($mensaje_Transacciones);
            }

            //Incrementamos el Contador PROVISIONAL de Ceremonias
            $tsql5 = " UPDATE folios_provisionales SET
                consecutivo = " . $nuevoConsecutivo . " " .
                "WHERE proceso = ?;";

            /* Valor de los parámetros. */
            $params5 = array("inscripcion_ceremonia");
            /* Preparamos la sentencia a ejecutar */
            $stmt5 = $conn->prepare($tsql5);
            if ($stmt5) {
                /*Ejecutamos el Query*/
                $result5 = $stmt5->execute($params5);
                if (!$result5 === FALSE) {
                    if ($stmt5->rowCount() > 0) {
                        //$mensaje_Transacciones .= "Contador PROVISIONAL. OK.<br/>";
                    } else {
                        $error = $stmt5->errorInfo();
                        $mensaje_Transacciones = "Ocurrió un error al procesar la ceremonia";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt5->errorInfo();
                    $mensaje_Transacciones = "Ocurrió un error al procesar la ceremonia";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt5->errorInfo();
                $mensaje_Transacciones = "Ocurrió un error al procesar la ceremonia";
                throw new Exception($mensaje_Transacciones);
            }

            $conn->commit();

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = 'SE AGREGO LA CEREMONIA ' . $desc_modalidad .
                ' *** para el Alumno: ' . $id_alumno . ' *** Para la Carrera ' . $id_carrera;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(45); //Inscripción a ceremonia
            $obj_miBitacora->set_Id_Tipo_Evento(5); //alta
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

    function Actualizar($objCeremonia, $id_division)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();

        try {

            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            $obj_Ceremonia = new Ceremonia();
            $obj_Ceremonia = $objCeremonia;

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            //Actualizamos los datos de la Ceremonia
            $tsql = " UPDATE inscripcion_ceremonia SET " .
                "sedes = ?, " .
                "diplomados_cursos = ?, " .
                "materias = ?, " .
                "programa_posgrado = ?, " .
                "nombre_articulo = ?, " .
                "nombre_revista = ? " .
                "WHERE id_ceremonia = ?;";
            /* Valor de los parámetros. */
            $params = array(
                $obj_Ceremonia->get_Sedes(),
                $obj_Ceremonia->get_Diplomados_Cursos(),
                $obj_Ceremonia->get_Materias(),
                $obj_Ceremonia->get_Programa_Posgrado(),
                $obj_Ceremonia->get_Nombre_Articulo(),
                $obj_Ceremonia->get_Nombre_Revista(),
                $obj_Ceremonia->get_Id_Ceremonia()
            );
            /* Preparamos la sentencia a ejecutar */
            //             $jsondata = array();
            //    $jsondata['success'] = false;
            //    $jsondata['data']['message'] = $params;
            //    echo json_encode($jsondata);
            //    exit();                    
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if (!$result === FALSE) {
                    if ($stmt->rowCount() > 0) {
                        $mensaje_Transacciones .= "Ceremonia Actualizada. OK.<br/>";
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Actualizar los datos de la Ceremonia.<br>"  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros para Actualizar los datos de la Ceremonia.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar los datos de la Ceremonia.<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = 'SE ACTUALIZÓ LOS DATOS DE LA CEREMONIA ' . $obj_Ceremonia->get_Id_Ceremonia() . ' *** ' . $obj_Ceremonia->get_Desc_Propuesta() .
                ' *** para el Alumno: ' . $obj_Ceremonia->get_Id_Alumno() . ' *** Para la Carrera ' . $obj_Ceremonia->get_Id_Carrera();
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(45); //Inscripción a ceremonia
            $obj_miBitacora->set_Id_Tipo_Evento(15); //actualización
            $obj_miBitacora->set_Id_Usuario_Genera($obj_Ceremonia->get_Id_Alumno());
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
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();
        }
    }

    //  MÉTODO PARA ACTUALIZAR INFORMACIÓN SOBRE LA FECHA ESTIMADA PARA TITULACIÓN Y MOTIVO
    function Registrar_Motivo_Ceremonia($id_ceremonia, $fechaEstimada, $motivoTitulacion)
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
            $tsql = "UPDATE inscripcion_ceremonia SET " .
                "llenofechaestimada = true, " .
                "fechaestimadatitulacion = ?, " .
                "motivotitulacion = ? " .
                "WHERE id_ceremonia = ?;";

            $params = array($fechaEstimada, $motivoTitulacion, $id_ceremonia);

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

    function Actualizar_Estatus_Doc_Enviado($id_ceremonia, $id_documento, $version, $id_estatus)
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
            $tsql = " UPDATE ceremonia_docs SET " .
                "id_estatus =  ?, " .
                "fecha_recepcion = ? " .
                "WHERE id_ceremonia = ? AND " .
                "id_documento =? AND version = ?;";
            /* Valor de los parámetros. */
            $params = array(
                $id_estatus,
                date('d-m-Y H:i:s'),
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
                        $mensaje_Transacciones .= "Estatus Actualizado. OK.<br/>";
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Actualizar el Estatus del Documento<br>."  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "Error en los parámetros para Actualizar el Estatus del Documento.<br/>"  . $error[2] . '<br>';
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

    //BORRAR LA CEREMONIA
    function Borrar_Ceremonia($id_ceremonia, $id_alumno, $id_carrera, $descripcion_ceremonia, $nota, $id_division)
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

            $tsql2 = " UPDATE ceremonia_docs 
                    SET id_estatus = ?
                    WHERE id_ceremonia = ?;";

            /* Valor de los parámetros. */
            $params2 = array(14, $id_ceremonia); //14.,15 Baja realizada por el Usuario/Administrador
            /* Preparamos la sentencia a ejecutar */
            $stmt2 = $conn->prepare($tsql2);
            if ($stmt2) {
                /*Ejecutamos el Query*/
                $result2 = $stmt2->execute($params2);
                if ($result2) {
                    if ($stmt2->rowCount() > 0) {
                        $mensaje_Transacciones .= "Documentos de Ceremonia de Baja. OK.<br/>";
                    } else {
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Dar de Baja los Documentos de Ceremonia.<br>"  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "No se dió de Baja los Documentos de Ceremonia.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt2->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para dar de Baja los Documentos de Ceremonia.<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }

            //BORRAMOS LA CEREMONIA
            $tsql = " UPDATE inscripcion_ceremonia
                    SET id_estatus = ?, nota_baja = ?, fecha_baja = ?
                    WHERE id_ceremonia = ?;";

            /* Valor de los parámetros. */
            $params = array(14, $nota, date('d-m-Y H:i:s'), $id_ceremonia); //14.,15 Baja realizada por el Usuario/Admin
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $mensaje_Transacciones .= "La Ceremonia se dió de Baja. OK.<br/>";
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo dar de Baja la Ceremonia.<br>"  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se dió de Baja la Ceremonia.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para dar de Baja la Ceremonia.<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }
            //BORRAMOS EL ARCHIVO PDF
            $nom_archivo = $id_alumno . '_' .
                $id_carrera . '_' .
                $id_ceremonia . '_*.pdf';
            $nom_archivo = $_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Docs/Ceremonias/' . $nom_archivo;
            array_map("unlink", glob($nom_archivo));

            $conn->commit();

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = ' ** Se dió de Baja la Ceremonia ' . $id_ceremonia . ' **Con Descripción ' .
                $descripcion_ceremonia . ' --- ' . $nota;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(45); //Inscripción Ceremonia
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
    //FIN BORRAR CEREMONIA

    function Solicitar_Baja_Ceremonia($id_ceremonia)
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

            /*Cambiamos el Estatus de la Ceremonia*/
            $tsql3 = "UPDATE inscripcion_ceremonia
                    SET id_estatus = ?
                    WHERE id_ceremonia = ?;";
            $params3 = array(15, $id_ceremonia); //Baja por autorizar Admin

            /* Preparamos la sentencia a ejecutar */
            $stmt3 = $conn->prepare($tsql3);
            if ($stmt3) {
                /*Ejecutamos el Query*/
                $result3 = $stmt3->execute($params3);
                $mensaje_Transacciones .= "Cambio de Estatus de la Ceremonia. OK<br><br>";
                $mensaje_Transacciones .= "<p style='padding:5px;color:white; background-color:#ff1493;'>DEBE DE ESPERAR A QUE EL ADMINISTRADOR REALICE LA BAJA DE ESTA CEREMONIA.</p>";
            } else {
                $error = $stmt3->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para Cambair el Estatus de la Ceremonia.<br/>"  . $error[2];
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
}

//$obj = new d_Alumno_Mi_Ceremonia();
//echo $obj->Obtener_Modalidades('01', '110');
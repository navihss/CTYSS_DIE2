<?php

/**
 * Definición de la Capa de Datos para la Bitácora
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */
header('Content-Type: text/html; charset=UTF-8');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Bitacora.php');

class d_Usuario_Bitacora
{

    //AGREGAMOS EL MOVIMIENTO A LA BITACORA
    function Agregar($objBitacora)
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

            $obj_bitacora = new Bitacora();
            $obj_bitacora = $objBitacora;

            if ($obj_bitacora->get_Id_Usuario_Destinatario() == '') {
                $tsql2 = " INSERT INTO bitacora (
                        id_usuario_genera, id_tipo_evento,
                        id_tema_bitacora, fecha_evento,
                        descripcion_evento, id_division) 
                        VALUES (?, ?, ?, ?, ?, ?);";
                $params2 = array(
                    $obj_bitacora->get_Id_Usuario_Genera(),
                    $obj_bitacora->get_Id_Tipo_Evento(),
                    $obj_bitacora->get_Id_Tema_Bitacora(),
                    $obj_bitacora->get_Fecha_Evento(),
                    $obj_bitacora->get_Descripcion_Evento(),
                    $obj_bitacora->get_Id_Division()
                );
            } else {
                $tsql2 = " INSERT INTO bitacora (
                        id_usuario_genera, id_tipo_evento,
                        id_tema_bitacora, fecha_evento,
                        id_usuario_destinatario, descripcion_evento, id_division) 
                        VALUES (?, ?, ?, ?, ?, ?, ?);";
                $params2 = array(
                    $obj_bitacora->get_Id_Usuario_Genera(),
                    $obj_bitacora->get_Id_Tipo_Evento(),
                    $obj_bitacora->get_Id_Tema_Bitacora(),
                    $obj_bitacora->get_Fecha_Evento(),
                    $obj_bitacora->get_Id_Usuario_Destinatario(),
                    $obj_bitacora->get_Descripcion_Evento(),
                    $obj_bitacora->get_Id_Division()
                );
            }

            $stmt2 = $conn->prepare($tsql2);
            if ($stmt2) {
                /*Ejecutamos el Query*/
                $result2 = $stmt2->execute($params2);
                if ($result2) {
                    if ($stmt2->rowCount() > 0) {
                        $mensaje_Transacciones .= ""; // "Movimiento Agregado en la Bitácora. OK.<br/>";
                    } else {
                        $error = $stmt2->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Agregar el Movimiento a la Bitácora.<br>"  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt2->errorInfo();
                    $mensaje_Transacciones .= "No se agregó el Movimiento a la Bitácora.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt2->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para agregar el Movimiento a la Bitácora.<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }

            //                $jsondata['success'] = true;
            //                $jsondata['data']['message'] = $mensaje_Transacciones;
            //                echo json_encode($jsondata);
            //                exit();                 
            return $mensaje_Transacciones;
        } catch (Exception $ex) {
            //            $jsondata['success'] = false;
            //            $jsondata['data']['message'] = $ex->getMessage();
            //            echo json_encode($jsondata);
            //            exit();   
            return $ex->getMessage();
        }
    }

    function Generar_Reporte($tipo_usuario, $chk_Propuesta, $chk_Ceremonia, $fecha_inicio, $fecha_termino, $carreras)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $resultado = $chk_Propuesta + $chk_Ceremonia;

            switch ($resultado) {
                case 1: //  Propuestas solamente.
                    $tsql = "SELECT ip.id_inscripcion as no_reg, ip.id_alumno as no_cuenta, u.nombre_usuario as nombre, u.apellido_paterno_usuario as ap_pat, u.apellido_materno_usuario as ap_mat, u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario || ' ' || u.nombre_usuario as nombre_completo, a.telefono_fijo_alumno as tel_fijo, a.telefono_celular_alumno as tel_cel, u.email_usuario as correo, ip.id_carrera as clave_carrera, c.descripcion_carrera as carrera, ip.motivotitulacion as motivo, to_char(ip.fechaestimadatitulacion, 'dd/Mon/yyyy') as fecha_estimada, 'Propuesta' as tipo
                        from inscripcion_propuesta ip
                        join usuarios u
                        on ip.id_alumno = u.id_usuario
                        join alumnos a
                        on u.id_usuario = a.id_alumno
                        join carreras c
                        on ip.id_carrera = c.id_carrera
                        where ip.llenofechaestimada = true
                        and to_date(?, 'dd/mm/yyyy') <= ip.fechaestimadatitulacion
                        and to_date(?, 'dd/mm/yyyy') >= ip.fechaestimadatitulacion
                        and ip.id_carrera in (" . $carreras . ");";
                    $params = array($fecha_inicio, $fecha_termino);
                    break;
                case 2: //  Ceremonias solamente.
                    $tsql = "SELECT ic.id_ceremonia as no_reg, ic.id_alumno as no_cuenta, u.nombre_usuario as nombre, u.apellido_paterno_usuario as ap_pat, u.apellido_materno_usuario as ap_mat, u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario || ' ' || u.nombre_usuario as nombre_completo, a.telefono_fijo_alumno as tel_fijo, a.telefono_celular_alumno as tel_cel, u.email_usuario as correo, ic.id_carrera as clave_carrera, c.descripcion_carrera as carrera, ic.motivotitulacion as motivo, to_char(ic.fechaestimadatitulacion, 'dd/Mon/yyyy') as fecha_estimada, 'Ceremonia' as tipo
                        from inscripcion_ceremonia ic
                        join usuarios u
                        on ic.id_alumno = u.id_usuario
                        join alumnos a
                        on u.id_usuario = a.id_alumno
                        join carreras c 
                        on ic.id_carrera = c.id_carrera
                        where ic.llenofechaestimada = true
                        and to_date(?, 'dd/mm/yyyy') <= ic.fechaestimadatitulacion
                        and to_date(?, 'dd/mm/yyyy') >= ic.fechaestimadatitulacion
                        and ic.id_carrera in (" . $carreras . ");";
                    $params = array($fecha_inicio, $fecha_termino);
                    break;
                default: //  Consulta completa.
                    $tsql = "SELECT ip.id_inscripcion as no_reg, ip.id_alumno as no_cuenta, u.nombre_usuario as nombre, u.apellido_paterno_usuario as ap_pat, u.apellido_materno_usuario as ap_mat, u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario || ' ' || u.nombre_usuario as nombre_completo, a.telefono_fijo_alumno as tel_fijo, a.telefono_celular_alumno as tel_cel, u.email_usuario as correo, ip.id_carrera as clave_carrera, c.descripcion_carrera as carrera, ip.motivotitulacion as motivo, to_char(ip.fechaestimadatitulacion, 'dd/Mon/yyyy') as fecha_estimada, 'Propuesta' as tipo
                        from inscripcion_propuesta ip
                        join usuarios u
                        on ip.id_alumno = u.id_usuario
                        join alumnos a
                        on u.id_usuario = a.id_alumno
                        join carreras c
                        on ip.id_carrera = c.id_carrera
                        where ip.llenofechaestimada = true
                        and to_date(?, 'dd/mm/yyyy') <= ip.fechaestimadatitulacion
                        and to_date(?, 'dd/mm/yyyy') >= ip.fechaestimadatitulacion
                        and ip.id_carrera in (" . $carreras . ")
                        union
                        SELECT ic.id_ceremonia as no_reg, ic.id_alumno as no_cuenta, u.nombre_usuario as nombre, u.apellido_paterno_usuario as ap_pat, u.apellido_materno_usuario as ap_mat, u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario || ' ' || u.nombre_usuario as nombre_completo, a.telefono_fijo_alumno as tel_fijo, a.telefono_celular_alumno as tel_cel, u.email_usuario as correo, ic.id_carrera as clave_carrera, c.descripcion_carrera as carrera, ic.motivotitulacion as motivo, to_char(ic.fechaestimadatitulacion, 'dd/Mon/yyyy') as fecha_estimada, 'Ceremonia' as tipo
                        from inscripcion_ceremonia ic
                        join usuarios u
                        on ic.id_alumno = u.id_usuario
                        join alumnos a
                        on u.id_usuario = a.id_alumno
                        join carreras c 
                        on ic.id_carrera = c.id_carrera
                        where ic.llenofechaestimada = true
                        and to_date(?, 'dd/mm/yyyy') <= ic.fechaestimadatitulacion
                        and to_date(?, 'dd/mm/yyyy') >= ic.fechaestimadatitulacion
                        and ic.id_carrera in (" . $carreras . ");";
                    $params = array($fecha_inicio, $fecha_termino, $fecha_inicio, $fecha_termino);
                    break;
            }

            $stmt = $conn->prepare($tsql);


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
                        $mensaje_Transacciones = "No se han encontrado registros con los parámetros actuales";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en las sentencias SQL.<br/>";
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }

    //OBTENER BITACORA CON PARAMETROS
    function Obtener($objBitacora, $f_inicio, $f_termino, $tipo_usuario)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $obj_Bitacora = new Bitacora();
            $obj_Bitacora = $objBitacora;
            $params = array();

            $filtro = 'WHERE ';

            if ($obj_Bitacora->get_Id_Tipo_Evento()) {
                $arr_ids = preg_split("/[,]/", $obj_Bitacora->get_Id_Tipo_Evento());
                $renglones = count($arr_ids);
                $ids = '';
                for ($i = 0; $i < $renglones; $i++) {
                    $ids .= $arr_ids[$i] . ',';
                }
                $ids = substr($ids, 0, strlen($ids) - 1);
                $operador = $filtro != 'WHERE ' ? " AND " : " ";
                $filtro .= $operador . " a.id_tipo_evento IN (" . $ids . ") ";
            }
            if ($obj_Bitacora->get_Id_Tema_Bitacora()) {
                $arr_ids = preg_split("/[,]/", $obj_Bitacora->get_Id_Tema_Bitacora());
                $renglones = count($arr_ids);
                $ids = '';
                for ($i = 0; $i < $renglones; $i++) {
                    $ids .= $arr_ids[$i] . ',';
                }
                $ids = substr($ids, 0, strlen($ids) - 1);

                $operador = $filtro != 'WHERE ' ? " AND " : " ";
                $filtro .= $operador . " a.id_tema_bitacora IN (" . $ids . ") ";
            }
            if ($obj_Bitacora->get_Id_Usuario_Genera()) {
                $operador = $filtro != 'WHERE ' ? " AND " : " ";
                $filtro .= $operador . " (a.id_usuario_genera = ? ";
                array_push($params, $obj_Bitacora->get_Id_Usuario_Genera());

                if ($obj_Bitacora->get_Id_Usuario_Destinatario()) {
                    $filtro .= " OR a.id_usuario_destinatario = ? ";
                    array_push($params, $obj_Bitacora->get_Id_Usuario_Destinatario());
                }
                $filtro .= " ) ";
            } else {
                if ($obj_Bitacora->get_Id_Usuario_Destinatario()) {
                    $operador = $filtro != 'WHERE ' ? " AND " : " ";
                    $filtro .= $operador . " a.id_usuario_destinatario = ? ";
                    array_push($params, $obj_Bitacora->get_Id_Usuario_Destinatario());
                }
            }

            if ($f_inicio) {
                $operador = $filtro != 'WHERE ' ? " AND " : " ";
                $filtro .= $operador . " to_char(a.fecha_evento, 'YYYY-MM-DD') >= ? ";
                $arr_fecha = preg_split("/[\/]/", $f_inicio);
                $nva_fecha = $arr_fecha[2] . '-' . $arr_fecha[1] . '-' . $arr_fecha[0];
                array_push($params, $nva_fecha);
            }
            if ($f_termino) {
                $operador = $filtro != 'WHERE ' ? " AND " : " ";
                $filtro .= $operador . " to_char(a.fecha_evento, 'YYYY-MM-DD') <= ? ";
                $arr_fecha2 = preg_split("/[\/]/", $f_termino);
                $nva_fecha2 = $arr_fecha2[2] . '-' . $arr_fecha2[1] . '-' . $arr_fecha2[0];
                array_push($params, $nva_fecha2);
            }

            if ($filtro == 'WHERE ') {
                $filtro = '';
            }

            $tsql = "SELECT a.id_usuario_genera, a.id_tipo_evento, a.id_tema_bitacora, a.fecha_evento, 
                            a.id_usuario_destinatario, a.descripcion_evento, b.descripcion_tema_bitacora,
                            c.descripcion_tipo_evento,
                            (d.nombre_usuario || ' ' || d.apellido_paterno_usuario || ' ' || d.apellido_materno_usuario) as usuario_genera,
                            (e.nombre_usuario || ' ' || e.apellido_paterno_usuario || ' ' || e.apellido_materno_usuario) as usuario_destinatario
                       FROM bitacora a
                             INNER JOIN tema_bitacora b ON a.id_tema_bitacora = b.id_tema_bitacora
                             INNER JOIN tipo_evento c ON a.id_tipo_evento = c.id_tipo_evento
                             INNER JOIN usuarios d ON a.id_usuario_genera =  d.id_usuario
                             LEFT JOIN usuarios e ON a.id_usuario_destinatario = e.id_usuario
                      " . $filtro . " " .
                " ORDER BY a.fecha_evento;";

            //            $jsondata['success'] = false;
            //            $jsondata['data']['message'] = $tsql;
            //            echo json_encode($jsondata);
            //            exit(); 

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
                        $mensaje_Transacciones = "No se encontraron Movimientos con estos Parámetros.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Movimientos de la Bitácora.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENER LA BITACORA CON PARAMETROS

    //OBTENER TEMAS POR TIPO DE USUARIO
    function Obtener_Temas($tipo_usuario)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_tema_bitacora, b.descripcion_tema_bitacora
                    FROM tema_tipo_usuario a
                            INNER JOIN tema_bitacora b ON a.id_tema_bitacora = b.id_tema_bitacora
                    WHERE a.id_tipo_usuario = ?
                    ORDER BY a.id_tema_bitacora;";

            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            $params = array($tipo_usuario);
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
                        $mensaje_Transacciones = "No se encontraron Temas para este Tipo de Usuario.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Temas para este Tipo de Usuario.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENER TEMAS POR TIPO DE USUARIO

}

//$obj_bit = new d_bitacora();
//$obj_ = new Bitacora();
//
//$obj_->set_Descripcion_Evento('');
//$obj_->set_Fecha_Evento(date('d-m-Y H:i:s'));
//$obj_->set_Id_Tema_Bitacora('1,5');
//$obj_->set_Id_Tipo_Evento('5,10');
////$obj_->set_Id_Usuario_Destinatario('%0861%');
//$obj_->set_Id_Usuario_Destinatario('');
//$obj_->set_Id_Usuario_Genera('');
//
////$f_inicio ='2016-08-14';
////$f_termino ='2016-08-14';
//$f_inicio ='';
//$f_termino ='';
//$tipo_usuario='';
//echo $obj_bit->Obtener($obj_, $f_inicio, $f_termino, $tipo_usuario);
//
//        
//$obj = new d_bitacora();
//echo $obj->Obtener_Temas(5);
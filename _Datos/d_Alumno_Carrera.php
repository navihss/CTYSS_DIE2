<?php

/**
 * Definición de la Capa de Datos para la Clase Alumno_Carrera
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Junio 2016
 */

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Bitacora.php');


class d_Alumno_Carrera
{
    function Mis_Carreras($id_usr)
    {

        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                $conn = null;
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_alumno, a.id_estatus, a.id_carrera, b.descripcion_carrera, c.descripcion_estatus
                    FROM alumno_carrera a INNER JOIN carreras b ON a.id_carrera = b.id_carrera
                    INNER JOIN estatus c ON a.id_estatus = c.id_estatus
                    WHERE a.id_alumno = ?
                    ORDER BY a.id_carrera";


            /* Valor de los parámetros. */
            $params = array($id_usr);
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
                        $jsondata['data']['message'] = 'No tiene Carreras registradas actualmente.';
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

    function Seleccionar_Carrera($id_usr)
    {
        $conn = '';
        $jsondata = array();

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                $conn = null;
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT id_carrera, descripcion_carrera
                    FROM carreras
                    WHERE id_carrera NOT IN (SELECT id_carrera FROM alumno_carrera
                                             WHERE id_alumno = ?)
                    ORDER BY id_carrera;";

            /* Valor de los parámetros. */
            $params = array($id_usr);
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
                        $jsondata['data']['message'] = 'No hay Carreras que pueda seleccionar.';
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

    function Agregar($id_usr, $id_carrera, $id_division)
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
            /* Agregamos la Carrera  
            /* Query parametrizado. */

            $tsql = " INSERT INTO alumno_carrera
                    (id_alumno, id_carrera, id_estatus, id_division) 
                    VALUES
                    (?, ?, 5, $id_division);
                ";
            /* Valor de los parámetros. */
            $params = array($id_usr, $id_carrera);

            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);

            /*Ejecutamos el Query*/
            $result = 0;
            $result = $stmt->execute($params);

            if ($result > 0) {
                $jsondata['success'] = true;
                $jsondata['data']['message'] = 'Carrera registrada satisfactoriamente.';

                $obj_Bitacora = new d_Usuario_Bitacora();
                $obj_miBitacora = new Bitacora();

                $descripcionEvento = 'REF. CARRERA ' . $id_carrera;
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora(15);
                $obj_miBitacora->set_Id_Tipo_Evento(5);
                $obj_miBitacora->set_Id_Usuario_Genera($id_usr);
                $obj_miBitacora->set_Id_Usuario_Destinatario('');
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($id_division);

                $obj_Bitacora->Agregar($obj_miBitacora);


                echo json_encode($jsondata);
                exit();
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= ("Transacción sin éxito en Alumno-Carrera.<br>" . $error[2]);
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();
        }
    }

    function Borrar($id_usr, $id_carrera)
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
            /* Borramos el Alumno-Carrera  
            /* Query parametrizado. */

            $tsql = " DELETE FROM alumno_carrera
                    WHERE id_alumno = ? AND id_carrera = ?;
                ";
            /* Valor de los parámetros. */
            $params = array($id_usr, $id_carrera);

            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);

            /*Ejecutamos el Query*/
            $result = 0;
            $result = $stmt->execute($params);

            if ($result > 0) {
                $jsondata['success'] = true;
                $jsondata['data']['message'] = 'Transacción con éxito en Alumno-Carrera.';
                echo json_encode($jsondata);
                exit();
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= ("Transacción sin éxito en Alumno-Carrera.<br>" . $error[2]);
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();
        }
    }
}
////
//$obj_d = new d_Alumno_Carrera();
//echo $obj_d->Seleccionar_Carrera('x');

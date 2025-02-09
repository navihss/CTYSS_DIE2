<?php

/**
 * Definición de la Capa de Datos para la Clase Catalogos Generales
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Mayo 2016
 */

header('Content-Type: text/html; charset=UTF-8');

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');

class d_Catalogos_Generales
{

    function Obtener($tabla_Catalogo, $Campos, $Condicion, $OrderBy)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            if ($Condicion) {
                $Condicion = " WHERE " . $Condicion;
            }
            if ($OrderBy) {
                $OrderBy = " ORDER BY " . $OrderBy;
            }

            $tsql = "SELECT " . $Campos .
                " FROM " . $tabla_Catalogo . ' ' . $Condicion . ' ' . $OrderBy;

            /* Valor de los parámetros. */
            $params = array();
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
                        $jsondata['data'] = array('message' => 'No hay información del Catálogo . ' . $tabla_Catalogo);
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

    function Obtener_Coordinaciones($id_division)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_coordinacion as id, a.descripcion_coordinacion as descripcion, b.id_coordinador as id_jefe, c.email_usuario,
                            (c.nombre_usuario || ' ' || c.apellido_paterno_usuario || ' ' || c.apellido_materno_usuario) as nombre,
                            d.descripcion_carrera 
                    FROM jefes_coordinacion b
                            INNER JOIN coordinaciones a ON b.id_coordinacion = a.id_coordinacion
                            INNER JOIN usuarios c ON b.id_coordinador = c.id_usuario
                            INNER JOIN carreras d ON a.id_carrera = d.id_carrera
                    WHERE b.actual_jefe = '1' and a.id_division=?
                    ORDER BY d.descripcion_carrera, a.descripcion_coordinacion;";

            /* Valor de los parámetros. */
            $params = array($id_division);
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
                        $jsondata['data'] = array('message' => 'No hay información del Catálogo de Coordinaciones .');
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

    function Obtener_Departamentos($id_division)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_departamento as id, a.descripcion_departamento as descripcion, b.id_jefe_departamento as id_jefe, c.email_usuario,
                            (c.nombre_usuario || ' ' || c.apellido_paterno_usuario || ' ' || c.apellido_materno_usuario) as nombre,
                            d.descripcion_carrera
                    FROM jefes_departamento b
                            INNER JOIN departamentos a ON b.id_departamento = a.id_departamento
                            INNER JOIN usuarios c ON b.id_jefe_departamento = c.id_usuario
                            INNER JOIN carreras d ON a.id_carrera = d.id_carrera
                    WHERE b.actual_jefe = '1' and a.id_division=?
                    ORDER BY d.descripcion_carrera, a.descripcion_departamento;";

            /* Valor de los parámetros. */
            $params = array($id_division);
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
                        $jsondata['data'] = array('message' => 'No hay información del Catálogo de Departamentos.');
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

    function Obtener_Coordinadores_Activos($textoBuscar, $id_area)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT a.id_coordinador as id, a.id_coordinacion, c.descripcion_grado_estudio,
                            (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) as nombre
                    FROM jefes_coordinacion a
                            INNER JOIN usuarios b ON a.id_coordinador = b.id_usuario
                            INNER JOIN grados_estudio c ON a.id_grado_estudio = c.id_grado_estudio
                    WHERE (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) ILIKE ?
                            AND b.activo_usuario = '1' AND a.id_coordinacion = ?
                    ORDER BY nombre  ;";

            /* Valor de los parámetros. */
            $params = array("%" . $textoBuscar . "%", $id_area);
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
                        $jsondata['data'] = array('message' => 'No hay Coordinadores con este patrón de ocurrencia.');
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

    function Obtener_Jefes_Dpto_Activos($textoBuscar, $id_area)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            //            $tsql = "SELECT a.id_jefe_departamento as id, a.id_departamento, c.descripcion_grado_estudio,
            //                            (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) as nombre
            //                    FROM jefes_departamento a
            //                            INNER JOIN usuarios b ON a.id_jefe_departamento = b.id_usuario
            //                            INNER JOIN grados_estudio c ON a.id_grado_estudio = c.id_grado_estudio
            //                    WHERE (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) ILIKE '%" . $textoBuscar . "%'
            //                            AND b.activo_usuario = '1' AND a.id_departamento = ?
            //                    ORDER BY nombre;";

            $tsql = "SELECT a.id_jefe_departamento as id, a.id_departamento, c.descripcion_grado_estudio,
                            (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) as nombre
                    FROM jefes_departamento a
                            INNER JOIN usuarios b ON a.id_jefe_departamento = b.id_usuario
                            INNER JOIN grados_estudio c ON a.id_grado_estudio = c.id_grado_estudio
                    WHERE (b.nombre_usuario || ' ' || b.apellido_paterno_usuario || ' ' || b.apellido_materno_usuario) ILIKE ?
                            AND b.activo_usuario = '1' AND a.id_departamento = ?
                    ORDER BY nombre;";

            /* Valor de los parámetros. */
            $params = array("%" . $textoBuscar . "%", $id_area);
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
                        $jsondata['data'] = array('message' => 'No hay Jefes de Departamento con este patrón de ocurrencia.');
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


} //Fin de la Clase

//$ob=new d_Catalogos_Generales();
//echo $ob->Obtener_Jefes_Dpto_Activos('', 30)

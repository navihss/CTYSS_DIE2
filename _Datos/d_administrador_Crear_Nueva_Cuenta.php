<?php

/**
 * Definición de la Capa de Datos para Agregar una Nueva Cuenta de Usuario
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */
header('Content-Type: text/html; charset=UTF-8');

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Administrador.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Coordinador.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Profesor.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Alumno.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Usuario.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Bitacora.php');

class d_administrador_Crear_Nueva_Cuenta
{

    function Agregar_Usuario($obj_Entidad, $id_administrador, $id_division)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $desc_Tipo_Usuario = '';
        $id_Nueva_Cuenta = '';
        $nom_Nuevo_Usuario = '';

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }
            //Identificar el tipo de instancia que recibimos
            if (is_a($obj_Entidad, 'Administrador')) {
                $desc_Tipo_Usuario = "Administrador";
                $obj_admin = new Administrador();
                $obj_admin = $obj_Entidad;

                $id_Nueva_Cuenta = $obj_admin->get_Id_Administrador();
                $tsql2 = " INSERT INTO administradores
                        (id_administrador,
                        id_puesto_trabajo,
                        id_usuario, 
                        id_division)
                        VALUES
                        (?, ?, ?, ?);
                    ";
                /* Valor de los parámetros. */
                $params2 = array(
                    $obj_admin->get_Id_Administrador(),
                    $obj_admin->get_Id_Puesto(),
                    $obj_admin->get_Id_Usuario(),
                    $id_division
                );
            } elseif (is_a($obj_Entidad, 'Coordinador')) {
                $desc_Tipo_Usuario = "Coordinador";
                $obj_Coord = new Coordinador();
                $obj_Coord = $obj_Entidad;

                $id_Nueva_Cuenta = $obj_Coord->get_Id_Coordinador();

                $tsql2 = " INSERT INTO jefes_coordinacion
                        (id_coordinador,
                        id_grado_estudio,
                        id_usuario,
                        id_coordinacion,
                        id_puesto_trabajo,
                        id_division)
                        VALUES
                        (?, ?, ?, ?, ?, ?);
                    ";
                /* Valor de los parámetros. */
                $params2 = array(
                    $obj_Coord->get_Id_Coordinador(),
                    $obj_Coord->get_Id_Grado_Estudio(),
                    $obj_Coord->get_Id_Usuario(),
                    $obj_Coord->get_Id_Coordinacion(),
                    $obj_Coord->get_Id_Puesto(),
                    $id_division
                );
            } elseif (is_a($obj_Entidad, 'Jefe_Departamento')) {
                $desc_Tipo_Usuario = "Jefe de Departamento";
                $obj_JefeDpto = new Jefe_Departamento();
                $obj_JefeDpto = $obj_Entidad;

                $id_Nueva_Cuenta = $obj_JefeDpto->get_Id_Jefe_Departamento();

                $tsql2 = " INSERT INTO jefes_departamento
                        (id_jefe_departamento,
                        id_grado_estudio,
                        id_usuario,
                        id_departamento,
                        id_puesto_trabajo,
                        id_division)
                        VALUES
                        (?, ?, ?, ?, ?, ?);
                    ";
                /* Valor de los parámetros. */
                $params2 = array(
                    $obj_JefeDpto->get_Id_Jefe_Departamento(),
                    $obj_JefeDpto->get_Id_Grado_Estudio(),
                    $obj_JefeDpto->get_Id_Usuario(),
                    $obj_JefeDpto->get_Id_Departamento(),
                    $obj_JefeDpto->get_Id_Puesto(),
                    $id_division
                );
            } elseif (is_a($obj_Entidad, 'Profesor')) {
                $desc_Tipo_Usuario = "Profesor";
                $obj_Prof = new Profesor();
                $obj_Prof = $obj_Entidad;

                $id_Nueva_Cuenta = $obj_Prof->get_Id_Profesor();

                $tsql2 = " INSERT INTO profesores
                        (id_profesor,
                        dependencia_laboral_profesor,
                        fecha_ingreso_fi_profesor,
                        rfc_profesor,
                        curp_profesor,
                        calle_numero_profesor,
                        colonia_profesor,
                        delegacion_municipio_profesor,
                        codigo_postal_profesor,
                        telefono_fijo_profesor,
                        telefono_extension_profesor,
                        telefono_celular_profesor,
                        id_usuario,
                        id_estado_republica, 
                        es_externo, 
                        id_grado_estudio,
                        id_division)
                        VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
                    ";
                /* Valor de los parámetros. */
                $params2 = array(
                    $obj_Prof->get_Id_Profesor(),
                    $obj_Prof->get_Dependencia_Laboral(),
                    $obj_Prof->get_Fecha_Ingreso_FI(),
                    $obj_Prof->get_RFC(),
                    $obj_Prof->get_CURP(),
                    $obj_Prof->get_Calle_Numero(),
                    $obj_Prof->get_Colonia(),
                    $obj_Prof->get_Delegacion_Municipio(),
                    $obj_Prof->get_CP(),
                    $obj_Prof->get_Telefono_Fijo(),
                    $obj_Prof->get_Telefono_Extension(),
                    $obj_Prof->get_Telefono_Celular(),
                    $obj_Prof->get_Id_Usuario(),
                    $obj_Prof->get_Id_Estado(),
                    $obj_Prof->get_Es_Externo(),
                    $obj_Prof->get_Id_Grado_Estudio(),
                    $id_division
                );
            }

            /* Iniciar la transacción. */
            $conn->beginTransaction();

            /* Agregamos al Usuario    
                /* Query parametrizado. */
            $obj_Usuario_cd = new d_Usuario();
            $obj_Usuario = new Usuario();
            $obj_Usuario = $obj_Entidad;
            $obj_Usuario->set_Id_Division($id_division);

            $nom_Nuevo_Usuario = $obj_Usuario->get_Nombre() . ' ' . $obj_Usuario->get_Apellido_Paterno() . ' ' . $obj_Usuario->get_Apellido_Materno();

            $errorEnUsuarios = '';
            $errorEnUsuarios = $obj_Usuario_cd->Agregar($obj_Usuario, $conn);

            if ($errorEnUsuarios == '') {
                $mensaje_Transacciones = ("Transacción con éxito en Usuarios.<br/>");
            } else {
                $mensaje_Transacciones = ("Transacción sin éxito en Usuarios.<br/>" . $errorEnUsuarios);
                throw new Exception($mensaje_Transacciones);
            }

            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql2);

            /*Verificamos el contenido de la ejecución*/
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params2);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $mensaje_Transacciones .= "Transacción con éxito en " . $desc_Tipo_Usuario . ".<br/>";
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Agregar la entidad " . $desc_Tipo_Usuario . ".<br>."  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se pudo Agregar la entidad " . $desc_Tipo_Usuario . ".<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para Agregar la entidad " . $desc_Tipo_Usuario . ".<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }

            /*Ejecutamos el Query*/
            //                $result = 0;
            //                $result = $stmt->execute($params2);   
            //                
            //                if( $result > 0 ) {                
            //                     $mensaje_Transacciones .= ("Transacción con éxito en ". $desc_Tipo_Usuario .".<br/>");                        
            //                } else {
            //                     $mensaje_Transacciones .= ("Transacción sin éxito en ". $desc_Tipo_Usuario .".<br/>");
            //                    throw new Exception($mensaje_Transacciones);             
            //                }                  

            $conn->commit();

            //  Obtener una revisión del tipo de usuario que se está insertando en la base de datos:
            if (is_a($obj_Entidad, 'Administrador')) {
                $mensaje_Transacciones = "Cuenta de Administrador creada satisfactoriamente";
            } else if (is_a($obj_Entidad, 'Jefe_Departamento')) {
                $mensaje_Transacciones = "Cuenta de Jefe de Departamento creada satisfactoriamente";
            } else if (is_a($obj_Entidad, 'Coordinador')) {
                $mensaje_Transacciones = "Cuenta de Coordinador creada satisfactoriamente";
            } else if (is_a($obj_Entidad, 'Profesor')) {
                $mensaje_Transacciones = "Cuenta de Profesor creada satisfactoriamente";
            }

            $id_tipo_evento = 5; //Nueva Cuenta
            $id_tema_evento = 135; // Nueva Cuenta
            $descripcion_evento = "Nueva Cuenta de Usuario: " . $id_Nueva_Cuenta .
                " *** Tipo de Usuario: " . $desc_Tipo_Usuario .
                " *** Nombre del Usuario: " . $nom_Nuevo_Usuario;

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_evento;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento);
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
            $obj_miBitacora->set_Id_Usuario_Destinatario($id_Nueva_Cuenta);
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

    function Actualizar_Usuario($obj_Entidad, $id_division)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $desc_Tipo_Usuario = '';

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            //Identificar el tipo de instancia que recibimos
            if (is_a($obj_Entidad, 'Administrador')) {
                $desc_Tipo_Usuario = "Administrador";
                $obj_admin = new Administrador();
                $obj_admin = $obj_Entidad;

                $tsql2 = " UPDATE administradores SET                        
                        id_puesto_trabajo = ?
                        WHERE id_administrador = ?;";
                /* Valor de los parámetros. */
                $params2 = array($obj_admin->get_Id_Puesto(), $obj_admin->get_Id_Administrador());
                $id_usuario_bitacora = $obj_admin->get_Id_Administrador();
            } elseif (is_a($obj_Entidad, 'Coordinador')) {
                $desc_Tipo_Usuario = "Coordinador";
                $obj_Coord = new Coordinador();
                $obj_Coord = $obj_Entidad;

                $tsql2 = " UPDATE jefes_coordinacion SET
                        id_grado_estudio = ?,
                        id_coordinacion = ?,
                        id_puesto_trabajo = ?
                        WHERE id_coordinador = ?;";
                /* Valor de los parámetros. */
                $params2 = array(
                    $obj_Coord->get_Id_Grado_Estudio(),
                    $obj_Coord->get_Id_Coordinacion(),
                    $obj_Coord->get_Id_Puesto(),
                    $obj_Coord->get_Id_Coordinador()
                );
                $id_usuario_bitacora = $obj_Coord->get_Id_Coordinador();
            } elseif (is_a($obj_Entidad, 'Jefe_Departamento')) {
                $desc_Tipo_Usuario = "Jefe Departamento";
                $obj_Dpto = new Jefe_Departamento();
                $obj_Dpto = $obj_Entidad;

                $tsql2 = " UPDATE jefes_departamento SET
                        id_grado_estudio = ?,
                        id_departamento = ?,
                        id_puesto_trabajo = ?
                        WHERE id_jefe_departamento = ?;";
                /* Valor de los parámetros. */
                $params2 = array(
                    $obj_Dpto->get_Id_Grado_Estudio(),
                    $obj_Dpto->get_Id_Departamento(),
                    $obj_Dpto->get_Id_Puesto(),
                    $obj_Dpto->get_Id_Jefe_Departamento()
                );
                $id_usuario_bitacora = $obj_Dpto->get_Id_Jefe_Departamento();
            } elseif (is_a($obj_Entidad, 'Profesor')) {
                $desc_Tipo_Usuario = "Profesor";
                $obj_Prof = new Profesor();
                $obj_Prof = $obj_Entidad;

                $tsql2 = " UPDATE profesores SET
                        dependencia_laboral_profesor = ?,
                        fecha_ingreso_fi_profesor = ?,
                        rfc_profesor = ?,
                        curp_profesor = ?,
                        calle_numero_profesor = ?,
                        colonia_profesor = ?,
                        delegacion_municipio_profesor = ?,
                        codigo_postal_profesor = ?,
                        telefono_fijo_profesor = ?,
                        telefono_extension_profesor = ?,
                        telefono_celular_profesor = ?,
                        id_estado_republica = ?,
                        es_externo = ?,
                        id_grado_estudio = ?
                        WHERE id_profesor = ?;";
                /* Valor de los parámetros. */
                $params2 = array(
                    $obj_Prof->get_Dependencia_Laboral(),
                    $obj_Prof->get_Fecha_Ingreso_FI(),
                    $obj_Prof->get_RFC(),
                    $obj_Prof->get_CURP(),
                    $obj_Prof->get_Calle_Numero(),
                    $obj_Prof->get_Colonia(),
                    $obj_Prof->get_Delegacion_Municipio(),
                    $obj_Prof->get_CP(),
                    $obj_Prof->get_Telefono_Fijo(),
                    $obj_Prof->get_Telefono_Extension(),
                    $obj_Prof->get_Telefono_Celular(),
                    $obj_Prof->get_Id_Estado(),
                    $obj_Prof->get_Es_Externo(),
                    $obj_Prof->get_Id_Grado_Estudio(),
                    $obj_Prof->get_Id_Profesor()
                );
                $id_usuario_bitacora = $obj_Prof->get_Id_Profesor();
            }

            /* Iniciar la transacción. */
            $conn->beginTransaction();

            /* Agregamos al Usuario    
                /* Query parametrizado. */
            $obj_Usuario_cd = new d_Usuario();
            $obj_Usuario = new Usuario();
            $obj_Usuario = $obj_Entidad;

            $errorEnUsuarios = '';
            $errorEnUsuarios = $obj_Usuario_cd->Actualizar($obj_Usuario, $conn);

            if ($errorEnUsuarios == '') {
                $mensaje_Transacciones = ("Transacción con éxito en Usuarios.<br/>");
            } else {
                $mensaje_Transacciones = ("Transacción sin éxito en Usuarios.<br/>" . $errorEnUsuarios);
                throw new Exception($mensaje_Transacciones);
            }

            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql2);

            /*Verificamos el contenido de la ejecución*/
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params2);
                if (!$result === FALSE) {
                    if ($stmt->rowCount() > 0) {
                        $mensaje_Transacciones .= "Transacción con éxito en " . $desc_Tipo_Usuario . ".<br/>";
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Actualizar la entidad " . $desc_Tipo_Usuario . ".<br>."  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se pudo Actualizar la entidad " . $desc_Tipo_Usuario . ".<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar la entidad " . $desc_Tipo_Usuario . ".<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }

            $conn->commit();

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = '';
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora(10);
            $obj_miBitacora->set_Id_Tipo_Evento(15);
            $obj_miBitacora->set_Id_Usuario_Genera($id_usuario_bitacora);
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

    function Obtener_Usuario($id_usr, $tipo_Usuario)
    {

        $mensaje_Transacciones = '';
        $conn = '';
        $obj_Usuario = new Usuario();
        $jsondata = array();
        $desc_Tipo_Usuario = '';

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                $conn = null;
                throw new Exception($cnn->getError());
            }

            if ($tipo_Usuario == '1') {
                $desc_Tipo_Usuario = "Administrador";
                $tsql = "SELECT a.id_administrador, a.id_puesto_trabajo, a.id_usuario, 
                                b.email_usuario, b.fecha_alta_usuario, 
                                b.nombre_usuario, b.apellido_paterno_usuario, b.apellido_materno_usuario, 
                                b.activo_usuario, b.id_tipo_usuario, b.id_genero, b.id_tipo_baja
                        FROM administradores a
                                INNER JOIN usuarios b ON a.id_usuario = b.id_usuario
                        WHERE id_administrador = ?;";
            }
            if ($tipo_Usuario == '2') {
                $desc_Tipo_Usuario = "Jefe Dpto.";
                $tsql = " SELECT a.id_jefe_departamento, a.id_grado_estudio, a.id_departamento, 
                                a.actual_jefe, a.id_puesto_trabajo, b.email_usuario, b.fecha_alta_usuario, 
                                b.nombre_usuario, b.apellido_paterno_usuario, b.apellido_materno_usuario, 
                                b.activo_usuario, b.id_tipo_usuario, b.id_genero, b.id_tipo_baja
                           FROM jefes_departamento a
                                 INNER JOIN usuarios b ON a.id_usuario = b.id_usuario
                           WHERE id_jefe_departamento = ?;";
            } elseif ($tipo_Usuario == '3') {
                $desc_Tipo_Usuario = "Coordinador";
                $tsql = " SELECT a.id_coordinador, a.id_grado_estudio, a.id_coordinacion, 
                                a.actual_jefe, a.id_puesto_trabajo, b.email_usuario, b.fecha_alta_usuario, 
                                b.nombre_usuario, b.apellido_paterno_usuario, b.apellido_materno_usuario, 
                                b.activo_usuario, b.id_tipo_usuario, b.id_genero, b.id_tipo_baja
                           FROM jefes_coordinacion a
                                 INNER JOIN usuarios b ON a.id_usuario = b.id_usuario
                           WHERE id_coordinador = ?;";
            } elseif ($tipo_Usuario == '4') {
                $desc_Tipo_Usuario = "Profesor";
                $tsql = "SELECT a.id_profesor, a.dependencia_laboral_profesor, a.fecha_ingreso_fi_profesor, 
                                a.rfc_profesor, a.curp_profesor, a.calle_numero_profesor, a.colonia_profesor, 
                                a.delegacion_municipio_profesor, a.codigo_postal_profesor, a.telefono_fijo_profesor, 
                                a.telefono_extension_profesor, a.telefono_celular_profesor, a.id_usuario, 
                                a.id_estado_republica, b.email_usuario, b.fecha_alta_usuario, a.es_externo,
                                b.nombre_usuario, b.apellido_paterno_usuario, b.apellido_materno_usuario, 
                                b.activo_usuario, b.id_tipo_usuario, b.id_genero, b.id_tipo_baja, a.id_grado_estudio
                           FROM profesores a
                                 INNER JOIN usuarios b ON a.id_usuario = b.id_usuario
                       WHERE id_profesor = ?;";
            }

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
                        $jsondata['data'] = array('message' => 'No se encontró información de la entidad ' . $desc_Tipo_Usuario . '.');
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

    //Obtenemos los Tipos de Usuario
    function Obtener_Tipos_Usuario()
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT id_tipo_usuario, descripcion_tipo_usuario
                        FROM tipo_usuario
                        WHERE id_tipo_usuario <> 2
                        ORDER BY descripcion_tipo_usuario;";

            /* Valor de los parámetros. */
            //            $params = array($id_ss);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            /*Verificamos el contenido de la ejecución*/
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute();
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
    } //Fin Obtener Tipos de Usuario  

    //VERIFCA SI EL USUARIO YA EXISTE EN LA BD
    function Existe_Clave_Usuario($clave, $id_division)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "SELECT id_usuario, (nombre_usuario || ' ' || apellido_paterno_usuario || ' ' || apellido_materno_usuario) as nombre
                    FROM usuarios
                    WHERE id_usuario = ? and id_division = ?";

            /* Valor de los parámetros. */
            $params = array($clave, $id_division);
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            /*Verificamos el contenido de la ejecución*/
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $jsondata['success'] = 'EXISTE';
                        $jsondata['data']['message'] = 'Ya existe este usuario';
                        $jsondata['data']['registros'] = array();

                        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                            $jsondata['data']['registros'][] = $row;
                        }

                        echo json_encode($jsondata);
                        exit();
                    } else {
                        $jsondata['success'] = 'NOEXISTE';
                        $jsondata['data']['message'] = 'No existe este usuario';
                        echo json_encode($jsondata);
                        exit();
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $jsondata['success'] = 'ERROR';
                    $jsondata['data']['message'] = ($error[2]);
                    echo json_encode($jsondata);
                    exit();
                }
            } else {
                $jsondata['success'] = 'ERROR';
                $jsondata['data']['message'] = 'Error al ejecutar el Query.';
                echo json_encode($jsondata);
                exit();
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN VERIFCA SI EL USUARIO YA EXISTE EN LA BD

    //GENERADOR DE CLAVES PARA LOS USUARIOS
    function genera_Clave()
    {
        $lim_inf = 0;
        $lim_sup = 35;
        $caracteres_validos = "1234567890qwertyuioplkjhgfdsazxcvbnm";
        $indice_contrasena = 1;
        $num_aleatorio = 1;
        $contrasena = '';
        $caracter = '';
        while ($indice_contrasena < 16) {
            $num_aleatorio = mt_rand($lim_inf, $lim_sup);
            $caracter = substr($caracteres_validos, $num_aleatorio, 1);
            $contrasena .= $caracter;
            $indice_contrasena += 1;
        }
        $jsondata['success'] = true;
        $jsondata['data'] = array('message' => $contrasena);
        echo json_encode($jsondata);
        exit();
    }

    function Cambiar_Contrasena($id_usuario, $contrasenaNueva, $id_administrador, $nom_usuario, $id_division)
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

            $tsql = " UPDATE usuarios SET " .
                "contrasena_usuario =  ? " .
                "WHERE id_usuario = ?;";
            /* Valor de los parámetros. */
            $params = array($contrasenaNueva, $id_usuario);

            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                /*Ejecutamos el Query*/
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount() > 0) {
                        $mensaje_Transacciones .= "La Contraseña del Usuario fué Actualizada. OK.<br/>";
                    } else {
                        $error = $stmt->errorInfo();
                        $mensaje_Transacciones .= "No se pudo Actualizar la Contraseña del Usuario.<br>"  . $error[2] . '<br>';
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se actualizó la Contraseña del Usuario.<br/>"  . $error[2] . '<br>';
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en la sentencia SQL para Actualizar la Contraseña del Usuario.<br/>"  . $error[2] . '<br>';
                throw new Exception($mensaje_Transacciones);
            }

            $id_tema_evento = 140; //Cambio de contraseña
            $id_tipo_evento = 15; // Actualización
            $descripcion_evento = "Cambio de Contraseña para el Usuario : " . $id_usuario .
                " *** Nombre del Usuario: " . $nom_usuario;

            $obj_Bitacora = new d_Usuario_Bitacora();
            $obj_miBitacora = new Bitacora();

            $descripcionEvento = $descripcion_evento;
            $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
            $obj_miBitacora->set_Id_Tema_Bitacora($id_tema_evento);
            $obj_miBitacora->set_Id_Tipo_Evento($id_tipo_evento);
            $obj_miBitacora->set_Id_Usuario_Genera($id_administrador);
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
}

//$obj = new d_administrador_Crear_Nueva_Cuenta();
//echo $obj->Obtener_Usuario('laura56', 4);
<?php

/**
 * Definición de la Capa de Datos para la Clase Mi Jurado
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */
header('Content-Type: text/html; charset=UTF-8');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_coord_jdpto_Aprobar_Propuesta.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_mail.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Mail.php');

class d_Alumno_Mi_Jurado
{

    public function Obtener_Profesores($id_division)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
            if ($conn === false) {
                throw new Exception($cnn->getError());
            }
    
            $sql = "SELECT
                        u.id_usuario,
                        COALESCE(
                            CASE 
                                WHEN p.id_usuario IS NOT NULL THEN
                                    (SELECT g.descripcion_grado_estudio 
                                     FROM grados_estudio g 
                                     WHERE g.id_grado_estudio = p.id_grado_estudio)
                                WHEN jc.id_usuario IS NOT NULL THEN
                                    (SELECT g.descripcion_grado_estudio 
                                     FROM grados_estudio g 
                                     WHERE g.id_grado_estudio = jc.id_grado_estudio)
                                WHEN jd.id_usuario IS NOT NULL THEN
                                    (SELECT g.descripcion_grado_estudio 
                                     FROM grados_estudio g 
                                     WHERE g.id_grado_estudio = jd.id_grado_estudio)
                                ELSE ''
                            END
                        ,'') 
                        || ' ' || u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' ||
                           COALESCE(u.apellido_materno_usuario, '') 
                        AS nombre_completo
                    FROM usuarios u
                    LEFT JOIN profesores p ON p.id_usuario = u.id_usuario
                    LEFT JOIN jefes_coordinacion jc ON jc.id_usuario = u.id_usuario
                    LEFT JOIN jefes_departamento jd ON jd.id_usuario = u.id_usuario
                    WHERE u.id_tipo_usuario IN (2,3,4)
                      AND u.activo_usuario = '1'
                      AND u.id_division = :id_division
                    ORDER BY u.apellido_paterno_usuario, u.nombre_usuario";
    
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id_division', (int)$id_division, PDO::PARAM_INT);
            $stmt->execute();
    
            $arr = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr[] = [
                    'id_usuario'      => $row['id_usuario'],
                    'nombre_completo' => trim($row['nombre_completo'])
                ];
            }
    
            $stmt = null;
            $conn = null;
            return $arr;
        } catch (Exception $ex) {
            // En caso de error, regresa array vacío
            return [];
        }
    }
    

    function Obtener_Mi_Jurado($id_alumno, $id_carrera)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            // Consulta modificada para manejar valores nulos y tipos numéricos correctamente
            $tsql = "SELECT DISTINCT 
                        COALESCE(j.id_propuesta, ip.id_propuesta) as id_propuesta,
                        COALESCE(j.version, 1) as version,
                        COALESCE(j.fecha_propuesto, CURRENT_TIMESTAMP) as fecha_propuesto,
                        COALESCE(j.id_alumno_registro, 0) as id_alumno_registro,
                        COALESCE(j.id_administrador_definitivos, 0) as id_administrador_definitivos,
                        COALESCE(j.id_estatus, 12) as id_estatus,
                        COALESCE(e.descripcion_estatus, 'En espera de registro') as descripcion_estatus,
                        COALESCE(pv.version_propuesta, 1) as version_propuesta,
                        ip.id_alumno,
                        pp.titulo_propuesta,
                        COALESCE(NULLIF(TRIM(u.apellido_paterno_usuario || ' ' || 
                                u.apellido_materno_usuario || ' ' || 
                                u.nombre_usuario), ''), 'Sin asignar') as nombre
                    FROM inscripcion_propuesta ip
                    INNER JOIN propuestas_profesor pp ON ip.id_propuesta = pp.id_propuesta
                    INNER JOIN propuesta_version pv ON pp.id_propuesta = pv.id_propuesta 
                        AND pv.id_documento = 4 
                        AND pv.id_estatus = 3
                    LEFT JOIN jurado j ON pp.id_propuesta = j.id_propuesta
                    LEFT JOIN estatus e ON j.id_estatus = e.id_estatus
                    LEFT JOIN usuarios u ON j.id_alumno_registro = u.id_usuario
                    WHERE ip.id_alumno = :id_alumno 
                    AND ip.id_carrera = :id_carrera
                    AND ip.id_estatus = 3
                    ORDER BY id_propuesta DESC";

            $stmt = $conn->prepare($tsql);

            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . implode(" ", $conn->errorInfo()));
            }

            // Aseguramos que los parámetros sean numéricos
            $stmt->bindValue(':id_alumno', (int)$id_alumno, PDO::PARAM_INT);
            $stmt->bindValue(':id_carrera', (int)$id_carrera, PDO::PARAM_INT);

            $result = $stmt->execute();

            if (!$result) {
                throw new Exception("Error ejecutando la consulta: " . implode(" ", $stmt->errorInfo()));
            }

            $jsondata = [
                'success' => true,
                'data' => [
                    'message' => 'Registros encontrados',
                    'registros' => []
                ]
            ];

            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                // Aseguramos que todos los campos numéricos sean de tipo correcto
                $row->id_propuesta = (int)$row->id_propuesta;
                $row->version = (int)$row->version;
                $row->id_alumno_registro = (int)$row->id_alumno_registro;
                $row->id_administrador_definitivos = (int)$row->id_administrador_definitivos;
                $row->id_estatus = (int)$row->id_estatus;
                $row->version_propuesta = (int)$row->version_propuesta;
                $row->id_alumno = (int)$row->id_alumno;

                $jsondata['data']['registros'][] = $row;
            }

            if (empty($jsondata['data']['registros'])) {
                throw new Exception("No se encontraron propuestas para el alumno especificado.");
            }

            return json_encode($jsondata);
        } catch (Exception $ex) {
            return json_encode([
                'success' => false,
                'data' => ['message' => $ex->getMessage()]
            ]);
        } finally {
            if (isset($stmt)) $stmt = null;
            if (isset($conn)) $conn = null;
        }
    }


    /**
     * MÉTODO PRINCIPAL PARA OBTENER SINODALES
     */
    function Obtener_Sinodales($id_propuesta, $id_version)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($conn === false) {
                throw new Exception($cnn->getError());
            }
            
            // CAMBIO: Obtener id_division de la sesión de forma segura
            $id_division = isset($_SESSION["id_division"]) ? $_SESSION["id_division"] : 1;

            // 1. Verificar si hay sinodales
            $sqlCheck = "SELECT COUNT(*) AS total
                        FROM sinodales
                        WHERE id_propuesta = ?
                        AND version      = ?";
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->execute([$id_propuesta, $id_version]);
            $count = $stmtCheck->fetch(PDO::FETCH_OBJ)->total;

            // 2. Si no hay, crear los espacios pasando id_division
            if ($count == 0) {
                // CAMBIO: Pasar id_division al método
                $this->Crear_Espacios_Sinodales($id_propuesta, $id_version, $id_division, $conn);
            }

            // Consulta final para recuperar y mostrar los sinodales
            $tsql = "SELECT
                        s.id_propuesta,
                        s.version,
                        s.num_profesor,
                        s.nombre_sinodal_propuesto,
                        s.id_usuario,
                        CASE 
                        WHEN s.id_usuario IS NULL THEN 'Sin asignar'
                        ELSE 
                            COALESCE(u.nombre_usuario, '') || ' ' ||
                            COALESCE(u.apellido_paterno_usuario, '') || ' ' ||
                            COALESCE(u.apellido_materno_usuario, '')
                        END AS sinodal_definitivo
                    FROM sinodales s
                    LEFT JOIN usuarios u ON s.id_usuario = u.id_usuario
                    WHERE s.id_propuesta = ?
                    AND s.version      = ?
                    ORDER BY s.num_profesor";

            $stmt = $conn->prepare($tsql);
            $stmt->execute([$id_propuesta, $id_version]);

            $jsondata['success'] = true;
            $jsondata['data']['message'] = 'Registros encontrados';
            $jsondata['data']['registros'] = [];

            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $jsondata['data']['registros'][] = $row;
            }

            $conn = null;
            echo json_encode($jsondata);
            exit();

        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = ['message' => $ex->getMessage()];
            echo json_encode($jsondata);
            exit();
        }
    }

    /**
     * MÉTODO PARA CREAR AUTOMÁTICAMENTE LOS ESPACIOS (jurado, sinodales, jurado_vobo)
     * EVITA violaciones de FK reordenando la inserción: 
     *   1) jurado, 2) sinodales, 3) jurado_vobo
     */
    private function Crear_Espacios_Sinodales($id_propuesta, $id_version, $id_division, $conn = null)
    {
        try {
            $closeConnection = false;
            if ($conn === null) {
                $cnn = new Conexion();
                $conn = $cnn->getConexion();
                $closeConnection = true;
                if ($conn === false) {
                    throw new Exception($cnn->getError());
                }
                $conn->beginTransaction();
            }

            // 1. Verificar o insertar en 'jurado'
            $checkJuradoSql = "SELECT id_propuesta
                            FROM jurado
                            WHERE id_propuesta = ? 
                            AND version      = ?";
            $checkJuradoStmt = $conn->prepare($checkJuradoSql);
            $checkJuradoStmt->execute([$id_propuesta, $id_version]);

            if ($checkJuradoStmt->rowCount() === 0) {
                // CAMBIO: Usar id_division en lugar de valor fijo 1
                $insertJuradoSql = "INSERT INTO jurado 
                                    (id_propuesta, version, id_estatus, fecha_propuesto, id_division)
                                    VALUES (?, ?, 12, CURRENT_TIMESTAMP, ?)";
                $conn->prepare($insertJuradoSql)
                    ->execute([$id_propuesta, $id_version, $id_division]);
            }

            // 2. Crear 5 'sinodales' "Sin asignar"
            $checkSinodalesSql = "SELECT COUNT(*) AS total
                                FROM sinodales
                                WHERE id_propuesta = ?
                                    AND version      = ?";
            $checkSinodalesStmt = $conn->prepare($checkSinodalesSql);
            $checkSinodalesStmt->execute([$id_propuesta, $id_version]);
            $yaExisten = $checkSinodalesStmt->fetch(PDO::FETCH_OBJ)->total;

            if ($yaExisten == 0) {
                // CAMBIO: Usar id_division en lugar de valor fijo 1
                $insertSinodalSql = "INSERT INTO sinodales
                                    (id_propuesta, version, num_profesor, nombre_sinodal_propuesto, id_division)
                                    VALUES (?, ?, ?, 'Sin asignar', ?)";
                $stmtInsert = $conn->prepare($insertSinodalSql);
                for ($i = 1; $i <= 5; $i++) {
                    $stmtInsert->execute([$id_propuesta, $id_version, $i, $id_division]);
                }
            }

            // 3. Verificar / crear 'jurado_vobo'
            $checkVoboSql = "SELECT id_propuesta
                            FROM jurado_vobo
                            WHERE id_propuesta = ?
                            AND version      = ?";
            $checkVoboStmt = $conn->prepare($checkVoboSql);
            $checkVoboStmt->execute([$id_propuesta, $id_version]);

            if ($checkVoboStmt->rowCount() === 0) {
                // Buscar el coordinador que ya aprobó la propuesta inicialmente
                $coordSql = "SELECT pv.id_usuario
                             FROM propuesta_vobo pv
                             INNER JOIN usuarios u ON pv.id_usuario = u.id_usuario
                             WHERE pv.id_propuesta = ?
                               AND pv.version_propuesta = ?
                               AND u.id_tipo_usuario = 3"; // 3 = Coordinador
                $stmtCoord = $conn->prepare($coordSql);
                $stmtCoord->execute([$id_propuesta, $id_version]);
                $coordRow = $stmtCoord->fetch(PDO::FETCH_OBJ);
            
                $coordId = null;
                if ($coordRow) {
                    // Si SÍ existe un registro de Coordinador en propuesta_vobo
                    $coordId = $coordRow->id_usuario;
                } else {
                    // Si NO se encontró un coordinador, asignar ID=1 (admin u otro)
                    $coordId = 1;
                }
            
                // Insertar registros en jurado_vobo con el ID del coordinador encontrado
                $insertVoboSql = "INSERT INTO jurado_vobo 
                                 (id_propuesta, version, num_profesor,
                                  id_usuario, id_estatus, id_division)
                                 VALUES (?, ?, ?, ?, 12, ?)";
                $stmtVobo = $conn->prepare($insertVoboSql);
            
                for ($i = 1; $i <= 5; $i++) {
                    $stmtVobo->execute([$id_propuesta, $id_version, $i, $coordId, $id_division]);
                }
            }

            // --------------------------------------------------
            // 4. Cerrar transacción si corresponde
            // --------------------------------------------------
            if ($closeConnection) {
                $conn->commit();
                $conn = null;
            }
            return true;

        } catch (Exception $ex) {
            if (isset($conn) && $closeConnection) {
                $conn->rollBack();
                $conn = null;
            }
            return false;
        }
    }
    

    //ACTUALIZAMOS LOS SINODALES
    function Actualizar_Sinodales($id_propuesta, $id_version, $id_sinodales, $id_alumno_propone, $titulo_propuesta, $id_division)
    {
        $mensaje_Transacciones = '';
        $conn = '';
        $jsondata = array();
        $nombre_propuestos = '';
        
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
            if ($conn === false) {
                throw new Exception($cnn->getError());
            }
            
            // Determinar tipo de usuario
            $id_tipo_usuario = isset($_SESSION["id_tipo_usuario"]) ? $_SESSION["id_tipo_usuario"] : 5; // 5=alumno
            
            // Determinar estatus según el rol
            if ($id_tipo_usuario == 3) {
                // coordinador
                $nuevo_estatus = 16;
            } else if ($id_tipo_usuario == 2) {
                // jefe de dpto
                $nuevo_estatus = 17; 
            } else {
                // alumno
                $nuevo_estatus = 12;
            }
            
            $conn->beginTransaction();
            
            // ============== OBTENER CORREOS DE QUIEN DEBE RECIBIR AVISO ==============
            $obj_ = new d_coord_jdpto_Aprobar_Propuesta();
            $datos_prop = $obj_->Obtener_Usr_Mail_Propuesta_JDefinitivo($id_propuesta, 4, $id_version, $id_alumno_propone);
            if ($datos_prop == '') {
                $mensaje_Transacciones .= "No se pudo Obtener los Correos de Coordinadores/Jefes, Alumno, etc.";
                throw new Exception($mensaje_Transacciones);
            }
            $arr_datos_prop = preg_split("/[|]/", $datos_prop);
            $arr_coord_dpto_id_usuarios = $arr_datos_prop[0];
            $arr_coord_dpto_correos     = $arr_datos_prop[1];

            // ============== ACTUALIZAR LA TABLA sinodales ==============
            $arr_sinodales = preg_split("/[|]/", $id_sinodales);
            $renglones = count($arr_sinodales);
            
            $tsql4 = "UPDATE sinodales
                    SET nombre_sinodal_propuesto = ?,
                        id_usuario = ?
                    WHERE id_propuesta = ?
                        AND version      = ?
                        AND num_profesor = ?";
            
            for ($i = 0; $i < $renglones; $i++) {
                $stmt4 = $conn->prepare($tsql4);
                if ($stmt4) {
                    $arr_un_sinodal = preg_split("/[:]/", $arr_sinodales[$i]);
                    
                    $num_profesor   = $arr_un_sinodal[0];
                    $id_usuario     = null;
                    $nombre_sinodal = '';
                    
                    if (count($arr_un_sinodal) >= 3) {
                        $id_usuario     = $arr_un_sinodal[1];
                        $nombre_sinodal = $arr_un_sinodal[2];
                    } else {
                        $nombre_sinodal = $arr_un_sinodal[1];
                    }
                    
                    $nombre_propuestos .= ($i + 1) . ".- " . $nombre_sinodal . "<br>";
                    
                    $result4 = $stmt4->execute([
                        $nombre_sinodal,
                        $id_usuario,
                        $id_propuesta,
                        $id_version,
                        $num_profesor
                    ]);
                    
                    if ($result4) {
                        $mensaje_Transacciones .= "Sinodal " . ($i+1) . " procesado.<br/>";
                    } else {
                        $error = $stmt4->errorInfo();
                        $mensaje_Transacciones .= "Error al actualizar sinodal: " . $error[2] . "<br>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt4->errorInfo();
                    $mensaje_Transacciones .= "Error en la sentencia SQL (sinodales): " . $error[2] . "<br>";
                    throw new Exception($mensaje_Transacciones);
                }
            }
            
            // ============== ACTUALIZAR LA TABLA jurado (estatus, etc.) ==============
            $tsql = "UPDATE jurado
                    SET id_estatus         = ?,
                        fecha_propuesto    = ?,
                        id_alumno_registro = ?
                    WHERE id_propuesta = ?
                    AND version      = ?";
            
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                $result = $stmt->execute([
                    $nuevo_estatus,
                    date('d-m-Y H:i:s'),
                    $id_alumno_propone,
                    $id_propuesta,
                    $id_version
                ]);
                if ($result) {
                    $mensaje_Transacciones .= "Estatus del Jurado Actualizado. OK.<br/>";
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se actualizó Estatus del Jurado: " . $error[2] . "<br>";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en el SQL (jurado): " . $error[2] . "<br>";
                throw new Exception($mensaje_Transacciones);
            }
            
            // ============== ACTUALIZAR LA TABLA jurado_vobo (mismo estatus) ==============
            $tsql = "UPDATE jurado_vobo
                    SET id_estatus = ?
                    WHERE id_propuesta = ?
                    AND version      = ?";
            
            $stmt = $conn->prepare($tsql);
            if ($stmt) {
                $result = $stmt->execute([
                    $nuevo_estatus,
                    $id_propuesta,
                    $id_version
                ]);
                if ($result) {
                    $mensaje_Transacciones .= "Estatus de VoBo Coord/Dpto Actualizado. OK.<br/>";
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones .= "No se actualizó VoBo Coord/Dpto: " . $error[2] . "<br>";
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones .= "Error en el SQL (jurado_vobo): " . $error[2] . "<br>";
                throw new Exception($mensaje_Transacciones);
            }

            $conn->commit();
            
            $descripcion_correo2 = "<b>Solicitud de Jurado:</b><br>"
                . $nombre_propuestos
                . "<br><br> Para la Propuesta No. " . $id_propuesta
                . " Versión " . $id_version
                . "<br> ** Con Título " . $titulo_propuesta;
            
            $destinatarios_correo = $arr_coord_dpto_correos; 

            $resultado_Bitacora = '';
            
            // ============================================================
            //   ENVÍO DE CORREO (SIN bitácora)
            // ============================================================
            $obj    = new d_mail();
            $mi_mail = new Mail();
            
            // Validar si hay correos
            if (trim($destinatarios_correo) == '') {
                $respuesta_mail = "<br>** No se envió correo (destinatarios vacíos).<br>";
            } else {
                // Mandar correo
                $mi_mail->set_Correo_Destinatarios($destinatarios_correo);
                $mi_mail->set_Asunto('AVISO DE SOLICITUD DE JURADO');
                $mi_mail->set_Mensaje($descripcion_correo2);
                $mi_mail->set_Correo_Copia_Oculta('');

                $respuesta_mail = $obj->Envair_Mail($mi_mail); 
            }
            
            // Respuesta final en JSON
            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones 
                                        . $resultado_Bitacora 
                                        . $respuesta_mail;
            echo json_encode($jsondata);
            exit();
            
        } catch (Exception $ex) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            echo json_encode($jsondata);
            exit();
        }
    } //FIN ACTUALIZAMOS LOS SINODALES

}

//$ob = new d_Alumno_Mi_Jurado();
//echo $ob->Obtener_Sinodales('12', 1);
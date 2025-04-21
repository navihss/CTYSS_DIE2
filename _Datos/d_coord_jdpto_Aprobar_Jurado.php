<?php

/**
 * Definición de la Capa de Datos para la Clase Autorizar Jurados
 * Metodos
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */

header('Content-Type: text/html; charset=UTF-8');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_coord_jdpto_Aprobar_Propuesta.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_mail.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Mail.php');

class d_coord_jdpto_Aprobar_Jurado
{

    //*********************************************************************                
    //OBTENEMOS LAS PROPUESTAS POR AUTORIZAR
    function Obtener_Jurados_Por_Autorizar($id_usuario)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT a.id_propuesta, a.version, a.fecha_propuesto,
                             a.id_estatus, b.descripcion_estatus,
                             d.version_propuesta, e.descripcion_para_nom_archivo,
                             c.id_profesor, c.titulo_propuesta,
                             (f.apellido_paterno_usuario || ' ' ||
                              f.apellido_materno_usuario || ' ' ||
                              f.nombre_usuario) as nombre
                      FROM jurado a
                           INNER JOIN estatus b ON a.id_estatus = b.id_estatus
                           INNER JOIN propuestas_profesor c ON a.id_propuesta = c.id_propuesta
                           INNER JOIN propuesta_version d ON c.id_propuesta = d.id_propuesta
                           INNER JOIN documentos e ON d.id_documento = e.id_documento
                           INNER JOIN usuarios f ON c.id_profesor = f.id_usuario
                      WHERE d.id_documento = 4
                        AND d.id_estatus = 3
                        AND a.id_estatus = 12
                        AND ? IN (
                            SELECT g.id_usuario
                            FROM jurado_vobo g
                            WHERE a.id_propuesta = g.id_propuesta
                              AND a.version = g.version
                              AND id_usuario = ?
                              AND g.id_estatus = 12
                        )
                      ORDER BY a.fecha_propuesto;";

            $stmt = $conn->prepare($tsql);
            $params = array($id_usuario, $id_usuario);

            if($stmt){
                $result = $stmt->execute($params);
                if($result){
                    if($stmt->rowCount()>0){
                        $jsondata['success'] = true;
                        $jsondata['data']['message']   = 'Registros encontrados';
                        $jsondata['data']['registros'] = array();

                        while($row = $stmt->fetch(\PDO::FETCH_OBJ)){
                            $jsondata['data']['registros'][] = $row;
                        }
                        $stmt = null;
                        $conn = null;
                        echo json_encode($jsondata);
                        exit();
                    } else {
                        $mensaje_Transacciones = "No hay Jurados por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "No se pudo obtener los Jurados por Autorizar.<br/>".$error[2];
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL.<br/>".$error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch(Exception $ex){
            $jsondata['success'] = false;
            $jsondata['data']     = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    } //FIN OBTENEMOS JURADOS PENDIENTESPROPUESTAS POR AUTORIZAR
    //********************************************************************* 

    //*********************************************************************                
    //OBTENEMOS EL TOTAL DE JURADOS PROPUESTAS POR AUTORIZAR
    function Obtener_Total_Jurados_Por_Autorizar($id_usuario, $tipoUsuario=3)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "";

            if($tipoUsuario==3) { // Coordinador
                $tsql = " SELECT count(a.id_propuesta) as total1
                        FROM jurado a
                                INNER JOIN estatus b ON a.id_estatus = b.id_estatus
                                INNER JOIN propuestas_profesor c ON a.id_propuesta = c.id_propuesta
                                INNER JOIN propuesta_version d ON c.id_propuesta = d.id_propuesta
                                INNER JOIN documentos e ON d.id_documento = e.id_documento
                                INNER JOIN usuarios f ON c.id_profesor = f.id_usuario
                        WHERE d.id_documento = 4 AND d.id_estatus = 3 AND a.id_estatus = 12 AND
                        ? IN (SELECT g.id_usuario
                    FROM jurado_vobo g
                    WHERE a.id_propuesta = g.id_propuesta AND a.version = g.version AND id_usuario = ? AND g.id_estatus =12 )";
            }
            else if($tipoUsuario==2) { // Jefe
                $tsql = "
                    SELECT count(a.id_propuesta) as total1
                    FROM jurado a
                    WHERE a.id_estatus = 16
                    AND ? IN (
                        SELECT g.id_usuario
                        FROM propuesta_vobo g
                        WHERE a.id_propuesta=g.id_propuesta
                        AND a.version=g.version_propuesta
                        AND g.id_usuario=?
                    )
                ";
            }
            
            /* Preparamos la sentencia a ejecutar */
            $stmt = $conn->prepare($tsql);
            $params = array($id_usuario, $id_usuario);
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
                        return ($jsondata);
                        //                        echo json_encode($jsondata);
                        //                        exit();
                        //                        print_r($jsondata);
                    } else {
                        $mensaje_Transacciones = "No hay Jurados por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "No se pudo obtener el Total de Jurados por Autorizar.<br/>" . $error[2];
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener el Total de Jurados por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    } //FIN OBTENEMOS EL TOTAL DE JURADOS PENDIENTESPROPUESTAS POR AUTORIZAR
    //*********************************************************************     


    //*********************************************************************                
    //OBTENEMOS EL JURADO SELECCIONADO PARA VoBo
    function Obtener_Jurado_Seleccionado($id_usuario, $id_propuesta, $id_version)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
            if($cnn===false){
                throw new Exception($cnn->getError());
            }

            $tsql = " SELECT a.id_propuesta, a.version, a.num_profesor, a.nombre_sinodal_propuesto,
                             b.id_usuario, b.nota, b.aceptado, b.id_estatus, b.fecha_verificado
                      FROM sinodales a
                           INNER JOIN jurado_vobo b
                           ON a.id_propuesta = b.id_propuesta
                              AND a.version = b.version
                              AND a.num_profesor = b.num_profesor
                      WHERE b.id_usuario   = ?
                        AND b.id_estatus   = 12
                        AND a.id_propuesta = ?
                        AND a.version      = ?
                      ORDER BY a.version, a.num_profesor;";

            $stmt = $conn->prepare($tsql);
            $params = array($id_usuario, $id_propuesta, $id_version);

            if($stmt){
                $result = $stmt->execute($params);
                if($result){
                    if($stmt->rowCount()>0){
                        $jsondata['success'] = true;
                        $jsondata['data']['message']   = 'Registros encontrados';
                        $jsondata['data']['registros'] = array();

                        while($row = $stmt->fetch(\PDO::FETCH_OBJ)){
                            $jsondata['data']['registros'][] = $row;
                        }
                        $stmt = null;
                        $conn = null;
                        echo json_encode($jsondata);
                        exit();
                    } else {
                        $mensaje_Transacciones = "No hay información para este Jurado.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                } else {
                    $error = $stmt->errorInfo();
                    $mensaje_Transacciones = "No se pudo obtener la información de este Jurado.<br/>".$error[2];
                    throw new Exception($mensaje_Transacciones);
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error SQL para obtener info del Jurado.<br/>".$error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch(Exception $ex){
            $jsondata['success'] = false;
            $jsondata['data']     = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    } //FIN OBTENEMOS EL JURADO SELECCIONADO PARA VoBo
    //********************************************************************* 

    //*********************************************************************                
    //OBTENER SI TODOS LOS COORD/DPTO YA REVISARON A LOS SINODALES
    function N_Documentos_Por_Revisar($id_propuesta, $id_version)
    {
        try {
            $cnn  = new Conexion();
            $conn = $cnn->getConexion();
            if($conn===false){
                throw new Exception($cnn->getError());
            }

            $sql = "SELECT COUNT(id_estatus) AS pendientes
                    FROM jurado_vobo
                    WHERE id_propuesta=?
                      AND version=?
                      AND id_estatus=12";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_propuesta, $id_version]);
            if($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
                return $row['pendientes'];
            }
            return '';
        } catch(Exception $ex){
            return '';
        }
    }
    //FIN OBTENER SI TODOS LOS COORD/DPTO YA REVISARON A LOS SINODALES
    //********************************************************************* 

    //*********************************************************************                
    //ACTUALIZAMOS EL ESTATUS DEL VoBo
    function Actualizar_VoBo($id_propuesta, $id_version, $id_usuario, $vobo_usuario, $titulo_propuesta, $id_division, $accion_jefe='')
    {
        $mensaje_Transacciones = '';
        $jsondata = [];
        $conn = null;

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
            if($conn===false){
                throw new Exception($cnn->getError());
            }
            $conn->beginTransaction();

            // 1) Contamos cuántos COORD/DPTO tienen estatus=12
            $pendientes = $this->N_Documentos_Por_Revisar($id_propuesta, $id_version);
            if($pendientes===''){
                $mensaje_Transacciones .= "No se pudo obtener el número de Sinodales pendientes.";
                throw new Exception($mensaje_Transacciones);
            }

            // 2) Obtenemos correos y usuarios (bitácora/correos)
            $obj_ = new d_coord_jdpto_Aprobar_Propuesta();
            $datos_prop = $obj_->Obtener_Usr_Mail_Propuesta($id_propuesta, 4, $id_version);
            if($datos_prop==''){
                $mensaje_Transacciones .= "No se pudo obtener los Correos de Coordinadores/Jefes de Dpto.";
                throw new Exception($mensaje_Transacciones);
            }
            $arr_datos_prop  = preg_split("/[|]/", $datos_prop);
            $arr_coord_users = $arr_datos_prop[0];  // IDs
            $arr_coord_mails = $arr_datos_prop[1];  // Correos

            $tipoUsuario = (isset($_SESSION['id_tipo_usuario'])) ? $_SESSION['id_tipo_usuario'] : 0;
            $fecha_verif = date('d-m-Y H:i:s');
            $pendientes_VoBo = 0;


            // ===================================================
            // A) COORDINADOR (tipo=3), usa la lógica original
            // ===================================================
            if($tipoUsuario==3 && $accion_jefe==''){
                $arr_vobo = explode('|', $vobo_usuario);
                $renglones = count($arr_vobo);

                // 4) Preparamos update en jurado_vobo
                $sqlVobo = "UPDATE jurado_vobo
                            SET aceptado=:ac,
                                nota=:nt,
                                fecha_verificado=:fv,
                                id_estatus=:newSt
                            WHERE id_propuesta=:ip
                            AND version=:ve
                            AND num_profesor=:np
                            AND id_usuario=:usr";
                $stmtVobo = $conn->prepare($sqlVobo);

                // Contador de rechazados
                $countRechazados = 0;

                foreach($arr_vobo as $linea){
                    $part = explode(',', $linea);
                    $numProf = (int)$part[0];
                    $aceptado = (int)$part[1];
                    $idReemp = (count($part)>=3) ? (int)$part[2] : 0;
                    $nota    = (count($part)>=4) ? trim($part[3]) : '';

                    // Si no se acepta y hay reemplazo => reasignar sinodales
                    if($aceptado==0 && $idReemp>0){
                        $sqlReemp = "UPDATE sinodales
                                    SET id_usuario=:newu,
                                        nombre_sinodal_propuesto=(
                                        SELECT (apellido_paterno_usuario || ' ' ||
                                                apellido_materno_usuario || ' ' ||
                                                nombre_usuario)
                                        FROM usuarios
                                        WHERE id_usuario=:newu
                                        )
                                    WHERE id_propuesta=:ip
                                    AND version=:ve
                                    AND num_profesor=:np";
                        $stmtR = $conn->prepare($sqlReemp);
                        $stmtR->execute([
                            ':newu'=>$idReemp,
                            ':ip'=>$id_propuesta,
                            ':ve'=>$id_version,
                            ':np'=>$numProf
                        ]);
                    }

                    // Determinamos el estatus para jurado_vobo según rol
                    $newEstatus = 2; // default para roles != 3
                    
                    if($aceptado==0){
                        $newEstatus = 19; // Aprob. Parcial
                        $countRechazados++;
                    } else {
                        $newEstatus = 16; // Aprobado por Coord
                    }

                    // Update en jurado_vobo
                    $stmtVobo->execute([
                        ':ac'    => $aceptado,
                        ':nt'    => $nota,
                        ':fv'    => $fecha_verif,
                        ':newSt' => $newEstatus,
                        ':ip'    => $id_propuesta,
                        ':ve'    => $id_version,
                        ':np'    => $numProf,
                        ':usr'   => $id_usuario
                    ]);
                }

                // 5) Ajustamos estatus en "jurado"
                $sqlJ = '';
                $pendientes_VoBo = $pendientes - $renglones;
                // Coordinador => si hay rechazados => 19, si no => 16
                if($countRechazados>0){
                    $sqlJ="UPDATE jurado SET id_estatus=19 WHERE id_propuesta=? AND version=?;";
                } else {
                    $sqlJ="UPDATE jurado SET id_estatus=16 WHERE id_propuesta=? AND version=?;";
                }

                $stmtJ = $conn->prepare($sqlJ);
                $ok = $stmtJ->execute([$id_propuesta, $id_version]);
                if(!$ok){
                    throw new Exception("No se pudo actualizar estatus del jurado (Coord).");
                }
            }

            // ===================================================
            // B) JDPTO (tipo=2), con $accion_jefe
            // ===================================================
            else if($tipoUsuario==2 && $accion_jefe!=''){
                // Decide 17 (revisión), 18 (final) o volver 12 (rechazo).
                if($accion_jefe=='revision'){
                    // Poner jurado en 17
                    $sqlJ="UPDATE jurado SET id_estatus=17 WHERE id_propuesta=? AND version=?;";
                    $stmtJ = $conn->prepare($sqlJ);
                    $stmtJ->execute([$id_propuesta, $id_version]);

                    // Actualizar su jurado_vobo a 17
                    $sqlV="UPDATE jurado_vobo
                           SET id_estatus=17,
                               fecha_verificado=:fv
                           WHERE id_propuesta=:ip
                             AND version=:ve
                             AND id_usuario=:usr;";
                    $stmtV = $conn->prepare($sqlV);
                    $stmtV->execute([
                        ':fv'=>$fecha_verif,
                        ':ip'=>$id_propuesta,
                        ':ve'=>$id_version,
                        ':usr'=>$id_usuario
                    ]);
                }
                else if($accion_jefe=='final'){
                    // Aprobación definitiva 18
                    $sqlJ="UPDATE jurado SET id_estatus=18 WHERE id_propuesta=? AND version=?;";
                    $stmtJ = $conn->prepare($sqlJ);
                    $stmtJ->execute([$id_propuesta, $id_version]);

                    $sqlV="UPDATE jurado_vobo
                           SET id_estatus=18,
                               fecha_verificado=:fv
                           WHERE id_propuesta=:ip
                             AND version=:ve
                             AND id_usuario=:usr;";
                    $stmtV = $conn->prepare($sqlV);
                    $stmtV->execute([
                        ':fv'=>$fecha_verif,
                        ':ip'=>$id_propuesta,
                        ':ve'=>$id_version,
                        ':usr'=>$id_usuario
                    ]);

                    $sqlAll = "UPDATE jurado_vobo
                            SET id_estatus=18,
                                fecha_verificado=?
                            WHERE id_propuesta=?
                                AND version=?
                                AND id_estatus = 16";
                    $stmtAll = $conn->prepare($sqlAll);
                    $stmtAll->execute([$fecha_verif, $id_propuesta, $id_version]);
                }
                else if($accion_jefe=='rechazo'){
                    // Rechazo total => devolver al alumno en 12
                    $sqlJ="UPDATE jurado SET id_estatus=12 WHERE id_propuesta=? AND version=?;";
                    $stmtJ = $conn->prepare($sqlJ);
                    $stmtJ->execute([$id_propuesta, $id_version]);

                    // Registrar nota en jurado_vobo
                    $nota = (isset($_POST['nota_rechazo'])) ? $_POST['nota_rechazo'] : 'Rechazado por Jefe de Dpto.';
                    $sqlV="UPDATE jurado_vobo
                           SET nota=:nt,
                               fecha_verificado=:fv
                           WHERE id_propuesta=:ip
                             AND version=:ve
                             AND id_usuario=:usr;";
                    $stmtV = $conn->prepare($sqlV);
                    $stmtV->execute([
                        ':nt'=>$nota,
                        ':fv'=>$fecha_verif,
                        ':ip'=>$id_propuesta,
                        ':ve'=>$id_version,
                        ':usr'=>$id_usuario
                    ]);

                    $sqlAll = "UPDATE jurado_vobo
                            SET id_estatus=12,
                                fecha_verificado=?,
                                nota=?
                            WHERE id_propuesta=?
                                AND version=?
                                AND id_estatus = 16";
                    $stmtAll = $conn->prepare($sqlAll);
                    $stmtAll->execute([$fecha_verif, $nota, $id_propuesta, $id_version]);
                }
            }
            
            $conn->commit();

            // 6) Correos y bitácora
            $respuesta_mail = '';
            $id_tema_evento1 = 85; // Definir Jurado
            $id_tipo_evento1 = 55; // Revisión
            $descEvento = "Revisé y dí el VoBo al Jurado de la Prop. No. "
                        . $id_propuesta." Versión ".$id_version
                        ." ** Título: ".$titulo_propuesta;

            $obj_Bitacora = new d_Usuario_Bitacora();
            $miBitacora = new Bitacora();
            $miBitacora->set_Fecha_Evento($fecha_verif);
            $miBitacora->set_Id_Tema_Bitacora($id_tema_evento1);
            $miBitacora->set_Id_Tipo_Evento($id_tipo_evento1);
            $miBitacora->set_Id_Usuario_Genera($id_usuario);
            $miBitacora->set_Id_Usuario_Destinatario('');
            $miBitacora->set_Descripcion_Evento($descEvento);
            $miBitacora->set_Id_Division($id_division);
            $resultado_Bitacora = $obj_Bitacora->Agregar($miBitacora);
            
            // Si $pendientes_VoBo == 0 => mandar aviso final
            if($pendientes_VoBo==0){
                $id_tema_evento2 = 118;
                $id_tipo_evento2 = 50;
                $descCorreo = "Coordinadores y Jefes de Dpto. han concluido la Revisión y VoBo de la "
                            . "Propuesta No. ".$id_propuesta." Versión ".$id_version
                            . "<br>** Título: ".$titulo_propuesta;

                $arr_ids   = preg_split("/[,]/", $arr_coord_users);
                $renglones2= count($arr_ids);

                for($i=0;$i<$renglones2;$i++){
                    sleep(1);
                    $b2 = new Bitacora();
                    $b2->set_Fecha_Evento(date('d-m-Y H:i:s'));
                    $b2->set_Id_Tema_Bitacora($id_tema_evento2);
                    $b2->set_Id_Tipo_Evento($id_tipo_evento2);
                    $b2->set_Id_Usuario_Genera(1);
                    $b2->set_Id_Usuario_Destinatario($arr_ids[$i]);
                    $b2->set_Descripcion_Evento($descCorreo);
                    $b2->set_Id_Division($id_division);
                    $resultado_Bitacora = $obj_Bitacora->Agregar($b2);
                }

                // Mandamos correos
                $dmail = new d_mail();
                $mail_ = new Mail();
                $mail_->set_Correo_Destinatarios($arr_coord_mails);
                $mail_->set_Asunto('AVISO REVISIÓN Y VoBo DE JURADO PROPUESTO');
                $mail_->set_Mensaje($descCorreo);
                $mail_->set_Correo_Copia_Oculta('');

                $respuesta_mail = $dmail->Envair_Mail($mail_);
            }

            $jsondata['success'] = true;
            $jsondata['data']['message'] = $mensaje_Transacciones . $resultado_Bitacora . $respuesta_mail;
            echo json_encode($jsondata);
            exit();

        } catch(Exception $ex){
            if($conn && $conn->inTransaction()){
                $conn->rollBack();
            }
            $jsondata['success'] = false;
            $jsondata['data']['message'] = $ex->getMessage();
            return json_encode($jsondata);
        }
    }
    //FIN ACTUALIZAMOS EL ESTATUS DEL VoBo

    function Obtener_Profesores_Division($id_division)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
            if($conn===false){
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
                        || ' ' || u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' '
                        || COALESCE(u.apellido_materno_usuario,'')
                        AS nombre_completo
                    FROM usuarios u
                    LEFT JOIN profesores p          ON p.id_usuario = u.id_usuario
                    LEFT JOIN jefes_coordinacion jc ON jc.id_usuario= u.id_usuario
                    LEFT JOIN jefes_departamento jd ON jd.id_usuario= u.id_usuario
                    WHERE u.id_tipo_usuario IN (2,3,4)
                      AND u.activo_usuario = '1'
                      AND u.id_division=:id_division
                    ORDER BY u.apellido_paterno_usuario, u.nombre_usuario";

            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id_division', (int)$id_division, \PDO::PARAM_INT);
            $stmt->execute();

            $arr = [];
            while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
                $arr[] = [
                    'id_usuario' => $row['id_usuario'],
                    'nombre_completo' => trim($row['nombre_completo'])
                ];
            }
            $stmt = null;
            $conn = null;
            return $arr;
        } catch(Exception $ex){
            // en caso de error, regresa array vacío
            return [];
        }
    }

    //OBTENER JURADOS PARA JEFE DE DEPARTAMENTO
    public function Obtener_Jurados_Para_Jefe($id_usuario)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
            if ($conn===false) {
                throw new Exception($cnn->getError());
            }

            // Buscar jurados con estatus=16 (Aprobado Coord) o 19 (Rechazo Parcial)
            // y que correspondan a un id_usuario que sea JDPTO
            $tsql = "
                SELECT a.id_propuesta,
                       a.version,
                       a.fecha_propuesto,
                       a.id_estatus,
                       b.descripcion_estatus,
                       c.id_profesor,
                       c.titulo_propuesta,
                       (f.apellido_paterno_usuario || ' ' ||
                        f.apellido_materno_usuario || ' ' ||
                        f.nombre_usuario) as nombre
                FROM jurado a
                     INNER JOIN estatus b          ON a.id_estatus = b.id_estatus
                     INNER JOIN propuestas_profesor c ON a.id_propuesta = c.id_propuesta
                     INNER JOIN usuarios f         ON c.id_profesor = f.id_usuario
                WHERE a.id_estatus = 16
                  AND ? IN (
                    SELECT g.id_usuario
                    FROM propuesta_vobo g
                    WHERE a.id_propuesta = g.id_propuesta
                      AND a.version      = g.version_propuesta
                      AND g.id_usuario  = ?
                  )
                ORDER BY a.fecha_propuesto;
            ";

            $stmt = $conn->prepare($tsql);
            $params = array($id_usuario, $id_usuario);

            if ($stmt) {
                $result = $stmt->execute($params);
                if ($result) {
                    if ($stmt->rowCount()>0) {
                        $jsondata['success'] = true;
                        $jsondata['data']['message'] = 'Jurados para Jefe';
                        $jsondata['data']['registros'] = array();
                        while($row = $stmt->fetch(\PDO::FETCH_OBJ)){
                            $jsondata['data']['registros'][] = $row;
                        }
                        echo json_encode($jsondata);
                        exit();
                    } else {
                        throw new Exception("No hay jurados pendientes para Jefe.");
                    }
                } else {
                    $error = $stmt->errorInfo();
                    throw new Exception("Error al consultar jurados Jefe: ".$error[2]);
                }
            } else {
                $error = $conn->errorInfo();
                throw new Exception("Error en la sentencia SQL Jefe: ".$error[2]);
            }
        } catch(Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }

    public function Obtener_Jurado_Seleccionado_Jefe($id_propuesta, $id_version)
    {
        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();
            if ($conn === false) {
                throw new Exception($cnn->getError());
            }

            // Obtenemos sinodales en estatus 16 o 19:
            $tsql = "
                SELECT a.id_propuesta,
                    a.version,
                    a.num_profesor,
                    a.nombre_sinodal_propuesto,
                    a.id_usuario,
                    b.id_estatus,
                    b.nota,
                    b.fecha_verificado
                FROM sinodales a
                    INNER JOIN jurado_vobo b
                        ON a.id_propuesta = b.id_propuesta
                    AND a.version      = b.version
                    AND a.num_profesor= b.num_profesor
                WHERE a.id_propuesta = ?
                AND a.version = ?
                AND b.id_estatus = 16
                ORDER BY a.num_profesor;
            ";

            $stmt = $conn->prepare($tsql);
            $stmt->execute([$id_propuesta, $id_version]);

            if ($stmt->rowCount() > 0) {
                $jsondata['success'] = true;
                $jsondata['data']['message']   = 'Sinodales para Jefe';
                $jsondata['data']['registros'] = $stmt->fetchAll(\PDO::FETCH_OBJ);
            } else {
                throw new Exception("No se encontraron sinodales en estatus 16/19 para esta propuesta.");
            }

            echo json_encode($jsondata);
            exit();

        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = ['message' => $ex->getMessage()];
            echo json_encode($jsondata);
            exit();
        }
    }
}

//$obj = new d_coord_jdpto_Aprobar_Jurado();
//$obj_resultado = $obj->Obtener_Total_Jurados_Por_Autorizar('rhernandez');
//$exito = ($obj_resultado['success']);
//$mensaje = $obj_resultado['data']['message'];
//$totalpendientes = $obj_resultado['data']['registros'][0]['total1'];
//echo ('success: ');
//echo($exito);
//echo (', mensaje: ');
//echo($mensaje);
//echo (', total pendientes:');
//echo($totalpendientes);
//
//$otroArray['success'] = $exito;
//$otroArray['data']['message'] = $mensaje;
//$otroArray['data']['registros'][0]['total1'] = $totalpendientes;
//$otroArray['data']['registros'][1]['total1'] = $totalpendientes + 5;
//
//print_r ($otroArray);
//
//$codificadoJson =json_encode($otroArray);
//$segundoArray = json_decode($codificadoJson);
//
//echo ($codificadoJson);
//
//print_r($segundoArray);


//$ob = new d_coord_jdpto_Aprobar_Jurado();
//echo $ob->Obtener_Jurado_Seleccionado('joel', '12', 1);
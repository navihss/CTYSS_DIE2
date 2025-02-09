<?php

/**
 * Definición de la Capa de Datos para los Reportes y Estadisticas
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


class d_administrador_Reportes_Estadisticas
{


    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Reportes_No_Alumnos_Titulados($tx_profesor, $fecha_inicio)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            /* Valor de los parámetros. */
            $params = array();

            $tsql = "select
                    nombre_profesor,
                    descripcion_categoria,
                    descripcion_division,--  tabla departamentos
                    sum(case when id_tipo_propuesta in(2,3)and id_tipo_titulacion=1 and id_estatus= 3 then 1 else 0 end) mediante_tesis_tesina_examen_prof_conc, 
                    sum(case when id_tipo_propuesta in(2,3)and id_tipo_titulacion=1 and id_estatus= 2 then 1 else 0 end) mediante_tesis_tesina_examen_prof_proc,
                    sum(case when id_tipo_propuesta = 5  and id_estatus= 3 then 1 else 0 end) trabajo_prof_conc,
                    sum(case when id_tipo_propuesta = 5  and id_estatus= 2 then 1 else 0 end) trabajo_prof_proc,
                    sum(case when id_tipo_propuesta = 7  and id_estatus= 3 then 1 else 0 end)actividad_inv_conc,  
                    sum(case when id_tipo_propuesta = 7  and id_estatus= 2 then 1 else 0 end)actividad_inv_proc, 
                    sum(case when id_tipo_propuesta = 6  and id_estatus= 3 then 1 else 0 end) servicio_social_conc,
                    sum(case when id_tipo_propuesta = 6  and id_estatus= 2 then 1 else 0 end) servicio_social_proc,
                    sum(case when id_tipo_propuesta= 1   and id_estatus= 3 then 1 else 0 end) seminario_conc,
                    sum(case when id_tipo_propuesta= 1   and id_estatus= 2 then 1 else 0 end) seminario_proc,
                    sum(case when id_tipo_propuesta = 2  and id_estatus= 3 then 1 else 0 end) tesis_esp_conc,  
                    sum(case when id_tipo_propuesta = 2  and id_estatus= 2 then 1 else 0 end) tesis_esp_proc, 
                    sum(case when id_tipo_propuesta = 8  and id_estatus= 3 then 1 else 0 end) examen_gral_conc,  
                    sum(case when id_tipo_propuesta = 8  and id_estatus= 2 then 1 else 0 end) examen_gral_proc, 
                    sum(case when id_tipo_propuesta = 12 and id_estatus= 3 then 1 else 0 end) apoyo_docedncia_conc,
                    sum(case when id_tipo_propuesta = 12 and id_estatus= 2 then 1 else 0 end) apoyo_docedncia_proc
                    from(
                        select al.id_alumno, 
                        (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario) as nombre_profesor,
                        cat.descripcion_categoria,
                        div.descripcion_division,
                        pp.id_tipo_propuesta,  			              /*tesis, tesina, ... etc*/
                        tt.id_tipo_titulacion,                       /*examen profesional o cermonia*/
                        est.id_estatus
                        from  alumnos al
                        inner join alumno_carrera alc       on al.id_alumno=alc.id_alumno
                        inner join inscripcion_propuesta ip on alc.id_alumno=ip.id_alumno and alc.id_carrera=ip.id_carrera
                        inner join carreras c               on alc.id_carrera=c.id_carrera
                        inner join divisiones div		on c.id_division=div.id_division
                        inner join division_categoria divc	on div.id_division =divc.id_division	
                        inner join categorias cat		on divc.id_categoria = cat.id_categoria	
                        inner join propuestas_profesor pp   on ip.id_propuesta=pp.id_propuesta
                        inner join tipos_propuesta  tp      on pp.id_tipo_propuesta=tp.id_tipo_propuesta
                        inner join tipos_titulacion tt      on tp.id_tipo_titulacion=tt.id_tipo_titulacion
                        inner join profesores p		on divc.id_div_cat=p.id_div_cat
                        inner join usuarios u               on p.id_usuario = u.id_usuario
                        inner join estatus est              on ip.id_estatus = est.id_estatus
                        union all                                   /* las tablas inscripcion_propuesta e inscripcion_ceremonia son excluyentes?  */
                        select al.id_alumno, 
                        (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario) as nombre_profesor,
                        cat.descripcion_categoria,
                        div.descripcion_division,
                        ic.id_tipo_propuesta,  			              /*tesis, tesina, ... etc*/
                        tt.id_tipo_titulacion,                       /*examen profesional o cermonia*/
                        est.id_estatus
                        from  alumnos al
                        inner join alumno_carrera alc       on al.id_alumno=alc.id_alumno
                        inner join inscripcion_ceremonia ic on alc.id_alumno=ic.id_alumno and alc.id_carrera=ic.id_carrera
                        inner join tipos_propuesta  tp      on ic.id_tipo_propuesta=tp.id_tipo_propuesta
                        inner join tipos_titulacion tt      on tp.id_tipo_titulacion=tt.id_tipo_titulacion
                        inner join carreras c               on alc.id_carrera=c.id_carrera
                        inner join divisiones div		    on c.id_division=div.id_division
                        inner join division_categoria divc	on div.id_division =divc.id_division	
                        inner join categorias cat		    on divc.id_categoria = cat.id_categoria	
                        inner join profesores p		        on divc.id_div_cat=p.id_div_cat
                        inner join usuarios u               on p.id_usuario = u.id_usuario
                        inner join estatus est              on ic.id_estatus = est.id_estatus 
                    ) as x
                    where 1=1  ";

            if ($tx_profesor != null && $tx_profesor != '') {
                $tsql = $tsql . " and nombre_profesor like ? ";
                $params[] = '%' . strtoupper($tx_profesor) . '%';
            }

            if ($fecha_inicio != null && $fecha_inicio != '') {
                $tsql = $tsql . " and to_char(fecha_inicio_ss,'dd/mm/yyyy') >=? ";
                $params[] = $fecha_inicio;
            }

            $tsql = $tsql . "group by
                            nombre_profesor,
                            descripcion_categoria,
                            descripcion_division ";



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
                        $mensaje_Transacciones = "No hay información de Reportes.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR 



    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Reportes_Anios()
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "select to_char(fecha_inicio_ss,'yyyy') id_anio from servicio_social group by to_char(fecha_inicio_ss,'yyyy')";
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
                        echo json_encode($jsondata, JSON_UNESCAPED_UNICODE);
                        exit();
                    } else {
                        $mensaje_Transacciones = "No hay información de Reportes Bimestrales por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes Bimestrales por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR 


    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Reportes_No_Alumnos_Titulados_por_anio($id_anio, $vision)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }
            if ($vision == "0") {
                $tsql = "select 
                        to_char(fecha_inicio_ss,'yyyy') as id_anio,
                        sum(case when ss.fecha_inicio_ss is not null then 1 else 0 end )inicio,
                        sum(case when ss.fecha_termino_ss is not null then 1 else 0 end )terminos
                        from alumno_carrera ac
                        inner join alumnos a on ac.id_alumno=a.id_alumno 
                        inner join carreras c on ac.id_carrera=c.id_carrera
                        inner join servicio_social ss on ac.id_alumno=ss.id_alumno
                        	and ac.id_carrera=ss.id_carrera
                        where 1=1
                        group by to_char(fecha_inicio_ss,'yyyy')";

                /* Valor de los parámetros. */
                $params = array();
            } else if ($vision == "1") {
                $tsql = "select
                        to_char(fecha_inicio_ss,'yyyymm') as id_mes,
                        c.descripcion_carrera as carrera,
                        sum(case when ss.fecha_inicio_ss is not null then 1 else 0 end )inicio,
                        sum(case when ss.fecha_termino_ss is not null then 1 else 0 end )terminos
                        from alumno_carrera ac
                        inner join alumnos a on ac.id_alumno=a.id_alumno
                        inner join carreras c on ac.id_carrera=c.id_carrera
                        inner join servicio_social ss on ac.id_alumno=ss.id_alumno
                        and ac.id_carrera=ss.id_carrera
                        where 1=1
                        and to_char(fecha_inicio_ss,'yyyymm')=?
                        group by to_char(fecha_inicio_ss,'yyyymm'),c.descripcion_carrera";

                /* Valor de los parámetros. */
                $params = array($id_anio);
            } else if ($vision == "2") {
                $tsql = "select
                        to_char(fecha_inicio_ss,'yyyy') as id_mes,
                        c.descripcion_carrera as carrera,
                        sum(case when ss.fecha_inicio_ss is not null then 1 else 0 end )inicio,
                        sum(case when ss.fecha_termino_ss is not null then 1 else 0 end )terminos
                        from alumno_carrera ac
                        inner join alumnos a on ac.id_alumno=a.id_alumno
                        inner join carreras c on ac.id_carrera=c.id_carrera
                        inner join servicio_social ss on ac.id_alumno=ss.id_alumno
                        and ac.id_carrera=ss.id_carrera
                        where 1=1
                        and to_char(fecha_inicio_ss,'yyyy')=?
                        group by to_char(fecha_inicio_ss,'yyyy'),c.descripcion_carrera";

                /* Valor de los parámetros. */
                $params = array($id_anio);
            }
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
                        echo json_encode($jsondata, JSON_UNESCAPED_UNICODE);
                        exit();
                    } else {
                        $mensaje_Transacciones = "No hay información de Reportes Bimestrales por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes Bimestrales por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR 


    //OBTENEMOS LOS REPORTES PARA PAGINA Aprobcaion de documentos > Reporte servicio social > Por Alumnos
    function Obtener_Reportes_Programas_servicio_social(
        $tx_alumno,
        $id_carrera,
        $fecha_inicio,
        $fecha_fin,
        $fecha_verifico,
        $fecha_verifico_fin,
        $anio,
        $no_registro,
        $id_programa,
        $tx_nombre_programa,
        $tx_dependencia,
        $tx_responsable,
        $tx_jefe_inmediato,
        $id_estatus,
        $id_genero,
        $num_cuenta
    ) {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            /* Valor de los parámetros. */
            $params = array();

            $tsql = "select  pss.id_programa,pss.descripcion_pss,ss.id_alumno,
            (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario) as nombre_usuario,
            c.descripcion_carrera,
            to_char(fecha_inicio_ss,'yyyy/mm/dd') fecha_inicio_ss,
            to_char(fecha_termino_ss,'yyyy/mm/dd') fecha_termino_ss,
            est.descripcion_estatus,
            ss.jefe_inmediato_ss,
            pss.responsable_pss,
            ss.id_ss,
            to_char(ssd.fecha_verifico_doc,'yyyy/mm/dd') fecha_verifico_doc
            from servicio_social ss
            inner join programas_ss pss on ss.id_programa=pss.id_programa
            inner join alumno_carrera ac on ss.id_alumno=ac.id_alumno 
            inner join alumnos a on ac.id_alumno=a.id_alumno 
            inner join carreras c on ac.id_carrera=c.id_carrera
            inner join estatus est on ss.id_estatus=est.id_estatus
            inner join usuarios u on u.id_usuario=a.id_alumno 
            inner join generos g on u.id_genero=g.id_genero
            inner join servicio_social_docs ssd on ss.id_ss=ssd.id_ss
            where 1=1 ";

            if ($tx_alumno != null && $tx_alumno != '') {
                $tsql = $tsql . " and (upper(u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario)) like ? ";
                $params[] = '%' . strtoupper($tx_alumno) . '%';
            }


            if ($id_carrera != null && $id_carrera != '0') {
                $tsql = $tsql . " and c.id_carrera=? ";
                $params[] = $id_carrera;
            }

            if ($num_cuenta != null && $num_cuenta != '') {
                $tsql = $tsql . " and a.id_alumno=? ";
                $params[] = $num_cuenta;
            }

            if ($fecha_verifico != null && $fecha_verifico != '') {
                $tsql = $tsql . " and ssd.fecha_verifico_doc >=? ";
                $params[] =  $fecha_verifico;
            }

            if ($fecha_verifico_fin != null && $fecha_verifico_fin != '') {
                $tsql = $tsql . " and ssd.fecha_verifico_doc <=? ";
                $params[] =  $fecha_verifico_fin;
            }

            if ($fecha_inicio != null && $fecha_inicio != '') {
                $tsql = $tsql . " and to_char(fecha_inicio_ss,'dd/mm/yyyy') >=? ";
                $params[] = $fecha_inicio;
            }

            if ($fecha_fin != null && $fecha_fin != '') {
                $tsql = $tsql . " and to_char(fecha_termino_ss,'dd/mm/yyyy')<=? ";
                $params[] = $fecha_fin;
            }


            if ($anio != null && $anio != '') {

                $tsql = $tsql . " and substring(pss.id_programa,0,5)=? ";
                $params[] = $anio;
            }

            if ($no_registro != null && $no_registro != '') {

                $tsql = $tsql . " and ss.id_ss=? ";
                $params[] = $no_registro;
            }




            if ($tx_jefe_inmediato != null && $tx_jefe_inmediato != '') {
                $tsql = $tsql . " and upper(ss.jefe_inmediato_ss) like ? ";
                $params[] = '%' . strtoupper($tx_jefe_inmediato) . '%';
            }

            if ($tx_responsable != null && $tx_responsable != '') {
                $tsql = $tsql . " and upper(pss.responsable_pss) like ? ";
                $params[] = '%' . strtoupper($tx_responsable) . '%';
            }

            if ($id_programa != null && $id_programa != '') {

                $tsql = $tsql . " and pss.id_programa=? ";
                $params[] = $id_programa;
            }

            if ($tx_dependencia != null && $tx_dependencia != '0') {

                $tsql = $tsql . " and pss.id_dependencia_ss=? ";
                $params[] = $tx_dependencia;
            }

            if ($tx_nombre_programa != null && $tx_nombre_programa != '') {
                $tsql = $tsql . " and upper(pss.descripcion_pss) like ? ";
                $params[] =  '%' . strtoupper($tx_nombre_programa) . '%';
            }



            if ($id_estatus != null && $id_estatus != '0') {
                $tsql = $tsql . " and est.id_estatus=? ";
                $params[] = $id_estatus;
            }

            if ($id_genero != null && $id_genero != '0') {
                $tsql = $tsql . " and g.id_genero=? ";
                $params[] = $id_genero;
            }


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
                        $mensaje_Transacciones = "No hay información de Reportes Bimestrales por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes Bimestrales por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN 


    //OBTENEMOS LOS REPORTES PARA PAGINA Aprobcaion de documentos > Reporte servicio social > Por Programas
    function Obtener_Reportes_Programas_servicio_social_pp(
        $tx_alumno,
        $id_carrera,
        $fecha_inicio,
        $fecha_fin,
        $anio,
        $no_registro,
        $id_programa,
        $tx_nombre_programa,
        $tx_dependencia,
        $tx_responsable,
        $tx_jefe_inmediato,
        $id_estatus,
        $id_genero
    ) {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            /* Valor de los parámetros. */
            $params = array();

            $tsql = "select  pss.id_programa,pss.descripcion_pss,ss.id_alumno,
                    (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario) as nombre_usuario,
                    c.descripcion_carrera,
                    to_char(fecha_inicio_ss,'yyyy/mm/dd') fecha_inicio_ss,
                    to_char(fecha_termino_ss,'yyyy/mm/dd') fecha_termino_ss,
                    est.descripcion_estatus,
                    ss.jefe_inmediato_ss,
                    pss.responsable_pss,
                    ts.desc_tipo_ss
                    from servicio_social ss
                    inner join programas_ss pss on ss.id_programa=pss.id_programa
                    inner join tipo_ss ts on pss.id_tipo_ss = ts.id_tipo
                    inner join alumno_carrera ac on ss.id_alumno=ac.id_alumno
                    inner join alumnos a on ac.id_alumno=a.id_alumno
                    inner join carreras c on ac.id_carrera=c.id_carrera
                    inner join estatus est on ss.id_estatus=est.id_estatus
                    inner join usuarios u on u.id_usuario=a.id_usuario
                    inner join generos g on u.id_genero=g.id_genero
                    where 1=1 ";

            if ($tx_alumno != null && $tx_alumno != '') {
                $tsql = $tsql . " and (upper(u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario)) like ? ";
                $params[] = '%' . strtoupper($tx_alumno) . '%';
            }


            if ($id_carrera != null && $id_carrera != '0') {
                $tsql = $tsql . " and c.id_carrera=? ";
                $params[] = $id_carrera;
            }


            if ($fecha_inicio != null && $fecha_inicio != '') {
                $tsql = $tsql . " and to_char(fecha_inicio_ss,'dd/mm/yyyy') >=? ";
                $params[] = $fecha_inicio;
            }

            if ($fecha_fin != null && $fecha_fin != '') {
                $tsql = $tsql . " and to_char(fecha_termino_ss,'dd/mm/yyyy')<=? ";
                $params[] = $fecha_fin;
            }


            if ($anio != null && $anio != '') {

                $tsql = $tsql . " and substring(pss.id_programa,0,5)=? ";
                $params[] = $anio;
            }

            if ($no_registro != null && $no_registro != '') {

                $tsql = $tsql . " and ss.id_ss=? ";
                $params[] = $no_registro;
            }




            if ($tx_jefe_inmediato != null && $tx_jefe_inmediato != '') {
                $tsql = $tsql . " and upper(ss.jefe_inmediato_ss) like ? ";
                $params[] = '%' . strtoupper($tx_jefe_inmediato) . '%';
            }

            if ($tx_responsable != null && $tx_responsable != '') {
                $tsql = $tsql . " and upper(pss.responsable_pss) like ? ";
                $params[] = '%' . strtoupper($tx_responsable) . '%';
            }

            if ($id_programa != null && $id_programa != '') {

                $tsql = $tsql . " and pss.id_programa=? ";
                $params[] = $id_programa;
            }

            if ($tx_dependencia != null && $tx_dependencia != '0') {

                $tsql = $tsql . " and pss.id_dependencia_ss=? ";
                $params[] = $tx_dependencia;
            }

            if ($tx_nombre_programa != null && $tx_nombre_programa != '') {
                $tsql = $tsql . " and upper(pss.descripcion_pss) like ? ";
                $params[] =  '%' . strtoupper($tx_nombre_programa) . '%';
            }



            if ($id_estatus != null && $id_estatus != '0') {
                $tsql = $tsql . " and est.id_estatus=? ";
                $params[] = $id_estatus;
            }

            if ($id_genero != null && $id_genero != '0') {
                $tsql = $tsql . " and g.id_genero=? ";
                $params[] = $id_genero;
            }




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
                        $mensaje_Transacciones = "No hay información de Reportes Bimestrales por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes Bimestrales por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN


    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Catalogo_Genero()
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = " select '0' as id, 'Todos' as descripcion union all select id_genero as id, descripcion_genero as descripcion from generos ";

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
                        $mensaje_Transacciones = "No hay información de Reportes Bimestrales por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes Bimestrales por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR

    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Catalogo_Estatus()
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }


            $tsql = " select 0 as id, 'Todos' as descripcion union all select id_estatus as id, descripcion_estatus as descripcion from estatus ";

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
                        $mensaje_Transacciones = "No hay información de Reportes Bimestrales por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes Bimestrales por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR


    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Catalogo_Carrera()
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }


            $tsql = " select '0' as id, 'Todos' as descripcion union all select id_carrera as id , descripcion_carrera as descripcion from carreras ";
            $params = array();


            /* Valor de los parámetros. */
            //$params = array();
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
                        $mensaje_Transacciones = "No hay información de Reportes Bimestrales por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes Bimestrales por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR




    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Catalogo_Dependencia()
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }


            $tsql = " select '0' as id, 'Todos' as descripcion union all select id_dependencia_ss, descripcion_dependencia_ss from dependencias_ss ";
            $params = array();


            /* Valor de los parámetros. */
            //$params = array();
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
                        $mensaje_Transacciones = "No hay información de Reportes Bimestrales por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes Bimestrales por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR



    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Catalogo_Tipo_Servicio()
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = " select '0' as id, 'Todos' as descripcion union all select id_tipo as id, desc_tipo_ss as descripcion from tipo_ss ";

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
                        $mensaje_Transacciones = "No hay información de Reportes Bimestrales por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes Bimestrales por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR

    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Catalogo_Recinto()
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = " select '0' as id, 'Todos' as descripcion union all select id_recinto as id, recinto as descripcion from cat_recinto ";

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
                        $mensaje_Transacciones = "No hay información de Reportes Bimestrales por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes Bimestrales por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR

    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Catalogo_Modalidad()
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = " select '0' as id, 'Todos' as descripcion union all select id_tipo_titulacion as id, descripcion_tipo_titulacion as descripcion from tipos_titulacion ";

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
                        $mensaje_Transacciones = "No hay información de Reportes Bimestrales por Autorizar.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes Bimestrales por Autorizar.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR


    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Reportes_Formatos($tx_profesor, $tx_tipo_trabajo, $fecha_inicio)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            /* Valor de los parámetros. */
            $params = array();

            $tsql = "select 
                    ip.id_propuesta as clave,
                    ge.descripcion_grado_estudio as grado,
                    (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario) as nombre_alumno,
                    sum(case when c.id_carrera = 109 then 1 else 0 end) as num_alumos_ie,
                    sum(case when c.id_carrera = 110 then 1 else 0 end) as num_alumos_ic,
                    sum(case when c.id_carrera = 111 then 1 else 0 end) as num_alumos_it,
                    sum(case when c.id_carrera = 112 then 1 else 0 end) as num_alumos_me,
                    pp.titulo_propuesta,
                    to_char(pp.fecha_aceptacion,'yyyy-mm-dd') as fecha_aceptacion
                    from  alumnos al
                    inner join alumno_carrera alc       on al.id_alumno=alc.id_alumno
                    inner join inscripcion_propuesta ip on alc.id_alumno=ip.id_alumno and alc.id_carrera=ip.id_carrera
                    inner join carreras c               on alc.id_carrera=c.id_carrera
                    inner join propuestas_profesor pp   on ip.id_propuesta=pp.id_propuesta
                    inner join profesores pf	        on pp.id_profesor=pf.id_profesor
                    inner join grados_estudio ge	    on pf.id_grado_estudio=ge.id_grado_estudio		
                    inner join tipos_propuesta  tp      on pp.id_tipo_propuesta=tp.id_tipo_propuesta
                    inner join tipos_titulacion tt      on tp.id_tipo_titulacion=tt.id_tipo_titulacion
                    inner join usuarios u               on pf.id_usuario = u.id_usuario
                    where 1=1 ";

            if ($tx_profesor != null && $tx_profesor != '') {
                $tsql = $tsql . " and upper(u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario) like ? ";
                $params[] = '%' . strtoupper($tx_profesor) . '%';
            }

            if ($tx_tipo_trabajo != null && $tx_tipo_trabajo != '') {
                $tsql = $tsql . " and upper(descripcion_tipo_propuesta) like ? ";
                $params[] = '%' . strtoupper($tx_tipo_trabajo) . '%';
            }


            if ($fecha_inicio != null && $fecha_inicio != '') {
                $tsql = $tsql . " and to_char(pp.fecha_aceptacion,'yyyy-mm-dd') >=? ";
                $params[] = $fecha_inicio;
            }



            $tsql = $tsql . "group by
                    ip.id_propuesta,
                    ge.descripcion_grado_estudio,
                    (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario) ,
                    pp.titulo_propuesta,
                    to_char(pp.fecha_aceptacion,'yyyy-mm-dd')
                    order by 1";




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
                        $mensaje_Transacciones = "No hay información de Reportes.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR 



    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Reportes_Titulacion_Alumnos($tx_sinodales, $fecha_inicio, $slc_alumnosProceso)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            /* Valor de los parámetros. */
            $params = array();

            $tsql = "select 
                to_char(alc.fecha_titulacion,'yyyy-mm-dd') as fecha_titulacion, 
                (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario) as nombre_alumno,
                al.id_alumno as numero_cuenta,	
                c.descripcion_carrera,
                tp.descripcion_tipo_propuesta,  			             
                tt.descripcion_tipo_titulacion,
                (u2.nombre_usuario || ' ' || u2.apellido_paterno_usuario || ' ' || u2.apellido_materno_usuario)  as director,
                max(case when s.num_profesor= 1 then s.nombre_sinodal_propuesto else '' end)  as nombre_sinodal_propuesto_1,
                max(case when s.num_profesor= 2 then s.nombre_sinodal_propuesto else '' end)  as nombre_sinodal_propuesto_2,
                max(case when s.num_profesor= 3 then s.nombre_sinodal_propuesto else '' end)  as nombre_sinodal_propuesto_3,
                max(case when s.num_profesor= 4 then s.nombre_sinodal_propuesto else '' end)  as nombre_sinodal_propuesto_4                
                from  alumnos al
                inner join alumno_carrera alc       on al.id_alumno=alc.id_alumno
                inner join inscripcion_propuesta ip on alc.id_alumno=ip.id_alumno and alc.id_carrera=ip.id_carrera
                inner join carreras c               on alc.id_carrera=c.id_carrera
                inner join propuestas_profesor pp   on ip.id_propuesta=pp.id_propuesta
                inner join tipos_propuesta  tp      on pp.id_tipo_propuesta=tp.id_tipo_propuesta
                inner join tipos_titulacion tt      on tp.id_tipo_titulacion=tt.id_tipo_titulacion
                inner join jurado j 	    	    ON pp.id_propuesta = j.id_propuesta 
                inner join sinodales s 		        on j.id_propuesta=s.id_propuesta AND j.version = s.version
                inner join usuarios u               on al.id_usuario = u.id_usuario
                inner join usuarios u2              on pp.id_profesor = u2.id_usuario
                inner join estatus est              on ip.id_estatus = est.id_estatus
                where 1=1 ";

            if ($tx_sinodales != null && $tx_sinodales != '') {
                $tsql = $tsql . " and upper(u2.nombre_usuario || ' ' || u2.apellido_paterno_usuario || ' ' || u2.apellido_materno_usuario)   like ? ";
                $params[] = '%' . strtoupper($tx_sinodales) . '%';
            }

            if ($fecha_inicio != null && $fecha_inicio != '') {
                $tsql = $tsql . " and to_char(alc.fecha_titulacion,'yyyy-mm-dd') >=? ";
                $params[] = $fecha_inicio;
            }

            if ($slc_alumnosProceso != null && $slc_alumnosProceso == '1') {
                $tsql = $tsql . " and case when est.id_estatus in(1,2,3,5,9,10,11) then 1 end = 1 ";
            }





            $tsql = $tsql . " group by
            alc.fecha_titulacion,
            (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario) ,
            al.id_alumno,
            c.descripcion_carrera,
            tp.descripcion_tipo_propuesta,  			             
            tt.descripcion_tipo_titulacion,
            (u2.nombre_usuario || ' ' || u2.apellido_paterno_usuario || ' ' || u2.apellido_materno_usuario)
            order by 1,2,3 ";



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
                        $mensaje_Transacciones = "No hay información de Reportes.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR

    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Reportes_Titulacion_Profesores($id_estatus)
    {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            $tsql = "select 
                    to_char(ip.fecha_inscripcion,'yyyy-mm-dd') fecha_titulacion, 	
                    (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario) as nombre_alumno,
                    al.id_alumno as numero_cuenta,
                    case when est.id_estatus = 7 then est.descripcion_estatus
                        when est.id_estatus in(1,2,3,5,9,10,11) then 'En Proceso' 
                        else est.descripcion_estatus end as descripcion_estatus, 
                    pp.titulo_propuesta,        
                    to_char(alc.fecha_titulacion,'yyyy-mm-dd') as fecha_examen_profesional   
                    from  alumnos al
                    inner join alumno_carrera alc       on al.id_alumno=alc.id_alumno
                    inner join inscripcion_propuesta ip on alc.id_alumno=ip.id_alumno and alc.id_carrera=ip.id_carrera
                    inner join carreras c               on alc.id_carrera=c.id_carrera
                    inner join propuestas_profesor pp   on ip.id_propuesta=pp.id_propuesta
                    inner join tipos_propuesta  tp      on pp.id_tipo_propuesta=tp.id_tipo_propuesta
                    inner join tipos_titulacion tt      on tp.id_tipo_titulacion=tt.id_tipo_titulacion
                    inner join usuarios u               on al.id_usuario = u.id_usuario
                    inner join estatus est              on ip.id_estatus = est.id_estatus
                    where 1=1
                    and est.id_estatus in(7,1,2,3,5,9,10,11) 
                    and tt.id_tipo_titulacion=1
                    group by
                    ip.fecha_inscripcion,
                    alc.fecha_titulacion,
                    (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario),
                    al.id_alumno,est.descripcion_estatus,
                    case when est.id_estatus = 7 then est.descripcion_estatus
                        when est.id_estatus in(1,2,3,5,9,10,11) then 'En Proceso' 
                        else est.descripcion_estatus end ,
                    pp.titulo_propuesta";



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
                        $mensaje_Transacciones = "No hay información de Reportes.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR


    //OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR
    function Obtener_Reportes_Titulacion_Ceremonia(
        $tx_alumno,
        $id_carrera,
        $fecha_inicio,
        $tx_recinto,
        $tx_modalidad
    ) {

        try {
            $cnn = new Conexion();
            $conn = $cnn->getConexion();

            if ($cnn === false) {
                throw new Exception($cnn->getError());
            }

            /* Valor de los parámetros. */
            $params = array();

            $tsql = "select 
                    to_char(alc.fecha_titulacion,'yyyy-mm-dd') fecha_titulacion,
                    (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario) as nombre_alumno,
                    c.descripcion_carrera,
                    cr.recinto as recinto, 			             
                    tt.descripcion_tipo_titulacion,
                    case when ic.id_tipo_propuesta = 8 then ic.diplomados_cursos else '' end as nombre_diplomado
                    --,tp.id_tipo_propuesta                   
                    from  alumnos al
                    inner join alumno_carrera alc       on al.id_alumno=alc.id_alumno
                    inner join inscripcion_ceremonia ic on alc.id_alumno=ic.id_alumno and alc.id_carrera=ic.id_carrera
                    inner join cat_recinto cr 	    on  alc.id_recinto=cr.id_recinto
                    inner join tipos_propuesta  tp      on ic.id_tipo_propuesta=tp.id_tipo_propuesta
                    inner join tipos_titulacion tt      on tp.id_tipo_titulacion=tt.id_tipo_titulacion
                    inner join carreras c               on alc.id_carrera=c.id_carrera
                    inner join usuarios u               on al.id_usuario = u.id_usuario
                    where tp.id_tipo_titulacion=2 ";

            if ($tx_alumno != null && $tx_alumno != '') {
                $tsql = $tsql . " and (upper(u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario)) like ? ";
                $params[] = '%' . strtoupper($tx_alumno) . '%';
            }


            if ($id_carrera != null && $id_carrera != '0') {
                $tsql = $tsql . " and c.id_carrera=? ";
                $params[] = $id_carrera;
            }


            if ($fecha_inicio != null && $fecha_inicio != '') {
                $tsql = $tsql . " and to_char(alc.fecha_titulacion,'yyyy-mm-dd') >=? ";
                $params[] = $fecha_inicio;
            }

            if ($tx_recinto != null && $tx_recinto != '0') {
                $tsql = $tsql . " and cr.id_recinto = ? ";
                $params[] = $tx_recinto;
            }

            if ($tx_modalidad != null && $tx_modalidad != '0') {
                $tsql = $tsql . " and tt.id_tipo_titulacion=? ";
                $params[] = $tx_modalidad;
            }

            $tsql = $tsql . " group by
                alc.fecha_titulacion,
                (u.nombre_usuario || ' ' || u.apellido_paterno_usuario || ' ' || u.apellido_materno_usuario),
                c.descripcion_carrera,
                cr.recinto,			             
                tt.descripcion_tipo_titulacion,
                case when ic.id_tipo_propuesta = 8 then ic.diplomados_cursos else '' end ";





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
                        $mensaje_Transacciones = "No hay información de Reportes.<br/>";
                        throw new Exception($mensaje_Transacciones);
                    }
                }
            } else {
                $error = $stmt->errorInfo();
                $mensaje_Transacciones = "Error en la sentencia SQL para obtener los Reportes.<br/>"  . $error[2];
                throw new Exception($mensaje_Transacciones);
            }
        } catch (Exception $ex) {
            $jsondata['success'] = false;
            $jsondata['data'] = array('message' => $ex->getMessage());
            echo json_encode($jsondata);
            exit();
        }
    }
    //FIN OBTENEMOS LOS REPORTES PENDIENTES POR AUTORIZAR POR EL ADMINISTRADOR




}

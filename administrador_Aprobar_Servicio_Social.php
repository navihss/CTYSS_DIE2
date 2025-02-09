<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-cache");
header("Pragma: nocache");

session_start();
if (
    !isset($_SESSION["id_tipo_usuario"]) and
    !isset($_SESSION["id_usuario"])
) {
    header('Location: index.php');
}

?>

<!DOCTYPE html>
<!--
Fecha:          Junio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para Aprobar los documentos para el Servicio Social de los Alumnos
-->

<html>

<head>

    <!--        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/jquery-ui.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="menu/estilo_menu.css" /> 
        <script src="js/jquery-1.12.4.min.js"></script>
        <script src="js/jquery-ui.min.js"></script>-->
    <script src="js/expresiones_reg.js"></script>

    <script>
        $(document).ready(function() {

            $('#btn_verSolicitudInicio').click(function(e) {
                e.preventDefault();
                $('#ventanaProcesando').dialog('open');
                //Obtenemos los datos de la Solicitud de Inicio
                var id_ss = $('#id_ss').val();
                var id_doc = 6;
                var id_estatus = 2; //YA NO ES FUNCIONAL
                obtener_Info_Documento(id_ss, id_doc, id_estatus, 'Solicitud_Inicio');

            });

            $('#btn_verCartaAceptacion').click(function(e) {
                e.preventDefault();
                $('#ventanaProcesando').dialog('open');
                //Obtenemos los datos de la Carta de Aceptación por Autorizar
                var id_ss = $('#id_ss').val();
                var id_doc = 1;
                var id_estatus = 2; //YA NO ES FUNCIONAL
                obtener_Info_Documento(id_ss, id_doc, id_estatus, 'Carta_Aceptacion');

            });

            $('#btn_verHistorialAcademico').click(function(e) {
                e.preventDefault();
                $('#ventanaProcesando').dialog('open');
                //Obtenemos los datos del Historial Académico
                var id_ss = $('#id_ss').val();
                var id_doc = 2;
                var id_estatus = 2; //YA NO ES FUNCIONAL.
                obtener_Info_Documento(id_ss, id_doc, id_estatus, 'Historial_Academico');
            });

            function obtener_Info_Documento(id_ss, id_doc, id_estatus, nom_Documento) {

                $.ajax({
                        data: {
                            Tipo_Movimiento: 'OBTENER_DOC',
                            id_ss: id_ss,
                            id_documento: id_doc,
                            id_estatus: id_estatus
                        },
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_Aprobar_Servicio_Social.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        if (respuesta.success == true) {
                            //Llenamos los datos del documento mostrado
                            $.each(respuesta.data.registros, function(key, value) {
                                $('#fecha_envio_doc').val(value['fecha_recepcion_doc']);
                                //                                   $('#estatus_doc').val(value['descripcion_estatus']);
                                $('#id_estatus_doc').val(value['id_estatus']);
                                $('#id_version_doc').val(value['id_version']);
                                $('#id_documento').val(value['id_documento']);
                                $('#desc_corta_doc').val(nom_Documento);
                                if (value['id_estatus'] != 2) {
                                    deshabilitar_Botones(true);
                                    if (value['id_estatus'] == 3) {
                                        $('#ventanaAviso').html('Documento ya ACEPTADO');
                                        $('#ventanaAvisos').dialog('open');
                                    } else if (value['id_estatus'] == 4) {
                                        $('#ventanaAviso').html('Documento ya RECHAZADO');
                                        $('#ventanaAvisos').dialog('open');
                                    }
                                } else {
                                    deshabilitar_Botones(false);
                                }
                                $('#ventanaMostrarPDF').dialog("option", "title", $('#cuenta').val() + "_" +
                                    $('#carrera_user').val() + "_" +
                                    $('#id_ss').val() + "_" +
                                    $('#id_version_doc').val() + "_" +
                                    nom_Documento + ".pdf");
                            });
                            //Mostramos el PDF
                            var tiempo = new Date();
                            var fileName = "Docs/Servicio_Social/" +
                                $('#cuenta').val() + "_" +
                                $('#carrera_user').val() + "_" +
                                $('#id_ss').val() + "_" +
                                $('#id_version_doc').val() + "_" +
                                nom_Documento + ".pdf" + "?" + tiempo;
                            var new_Object = $('#obj_PDF').clone(true);
                            new_Object.attr("type", "application/pdf");
                            new_Object.attr("data", fileName);
                            $("#obj_PDF").replaceWith(new_Object);
                            $('#ventanaProcesando').dialog('close');
                        } else {
                            $('#ventanaProcesando').dialog('close');
                            $('#ventanaAviso').html(respuesta.data.message);
                            $('#ventanaAvisos').dialog('open');

                            $('#fecha_envio_doc').val('');
                            //                               $('#estatus_doc').val('');
                            $('#id_version_doc').val(0);
                            $('#id_documento').val(0);

                            $('#ventanaMostrarPDF').dialog("option", "title", 'Aprobación de Documentos para el Servicio Social');
                            var tiempo = new Date();
                            var fileName = "Docs/Servicio_Social/" + "Sin_Seleccionar.pdf" + "?" + tiempo;
                            var new_Object = $('#obj_PDF').clone(true);
                            new_Object.attr("type", "application/pdf");
                            new_Object.attr("data", fileName);
                            $("#obj_PDF").replaceWith(new_Object);

                        }

                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisos').dialog('open');
                        $('#ventanaMostrarPDF').dialog("option", "title", 'Aprobación de Documentos para el Servicio Social');

                        var tiempo = new Date();
                        var fileName = "Docs/Servicio_Social/" + "Sin_Seleccionar.pdf" + "?" + tiempo;
                        var new_Object = $('#obj_PDF').clone(true);
                        new_Object.attr("type", "application/pdf");
                        new_Object.attr("data", fileName);
                        $("#obj_PDF").replaceWith(new_Object);

                    });
            }

            //MANEJADOR DE EVENTO CLICK PARA LOS BOTONES DE LA TABLA SERVICIOS SOCIALES
            $('#tabla_Servicios_Sociales').on("click", "button.btn_MostrarDocs", function(e) {
                e.preventDefault();
                $('#fecha_envio_doc').val($(this).data(''));
                //                    $('#estatus_doc').val($(this).data(''));
                $('#id_version_doc').val($(this).data(''));
                $('#id_documento').val($(this).data(''));

                $('#id_ss').val($(this).data('id_ss'));
                $('#cuenta').val($(this).data('id_clave_alumno'));
                $('#alumno').val($(this).data('nombre'));
                $('#carrera').val($(this).data('descripcion_carrera'));
                $('#telefono_fijo').val($(this).data('alumno_tel_fijo'));
                $('#telefono_celular').val($(this).data('alumno_tel_celular'));
                $('#descripcion_programa').val($(this).data('alumno_descripcion_programa'));
                $('#programa').val($(this).data('id_programa'));
                $('#fecha_inicio').val($(this).data('fecha_inicio_ss'));
                $('#duracion').val($(this).data('duracion_meses_ss'));
                $('#avance_creditos').val($(this).data('avance_creditos_ss'));
                $('#porcentaje_avance').val($(this).data('avance_porcentaje_ss'));
                //                    $('#tipo_remuneracion').val($(this).data('descripcion_tipo_remuneracion'));
                $('#jefe_inmediato').val($(this).data('jefe_inmediato_ss'));
                $('#correo_user').val($(this).data('correo'));
                $('#carrera_user').val($(this).data('id_carrera'));
                $('#id_user').val($(this).data('id_clave_alumno'));

                $('#ventanaMostrarPDF').dialog({
                    buttons: [{
                        id: "btn_Aceptar_Doc",
                        text: "Aceptar Doc",
                        click: function() {
                            if ($('#fecha_envio_doc').val() != '') {
                                $('#ventanaConfirmaAceptacion').dialog('open');
                            }
                        }
                    }, {
                        id: "btn_Rechazar_Doc",
                        text: "Rechazar Doc",
                        click: function() {
                            if ($('#fecha_envio_doc').val() != '') {
                                $('#ventanaConfirmaRechazo').dialog('open');
                            }
                        }
                    }, {
                        id: "btn_Cerrar",
                        text: "Cerrar",
                        click: function() {
                            $(this).dialog('close');
                            Obtener_SS_Por_Autorizar(2); //Servicios Sociales con estatus 2.Por Autorizar
                        }
                    }],
                    open: function() {
                        var fileName = "Docs/Servicio_Social/Sin_Seleccionar.pdf" + "?<?php echo date('Y-m-d_H:i:s'); ?>";
                        var new_Object = $('#obj_PDF').clone(true);
                        new_Object.attr("type", "application/pdf");
                        new_Object.attr("data", fileName);
                        $("#obj_PDF").replaceWith(new_Object);
                        deshabilitar_Botones(true);
                    },
                    close: function() {},
                    title: 'Aprobación de Documentos para el Servicio Social',
                    modal: true,
                    autoOpen: true,
                    resizable: true,
                    draggable: true,
                    height: 'auto',
                    width: 'auto',
                    show: 'slide',
                    hide: 'slide',
                    dialogClass: 'no-close',
                    closeOnEscape: false,
                    position: {
                        at: 'center top'
                    }

                }); //FIN MOSTRAR VENTANA PDF      
            }); //FIN TABLA SERVICIOS SOCIALES

            $('#ventanaConfirmaAceptacion').dialog({
                buttons: {
                    "Aceptar": function() {
                        if (!$('#nota_admin_a').val().match(miExpReg_Nota_Aceptacion)) {
                            $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #');
                            $('#ventanaAvisos').dialog('open');
                        } else {
                            $('#ventanaMensajeConfirma').text('Desea dar por Aceptado este Documento ?');
                            $('#ventanaConfirmaVoBo').dialog({
                                buttons: {
                                    "Aceptar": function() {
                                        $(this).dialog('close');
                                        $('#ventanaProcesando').dialog('open');
                                        var id_ss = $('#id_ss').val();
                                        var id_doc = $('#id_documento').val();
                                        var id_version = $('#id_version_doc').val();
                                        var id_estatus = 3; //3.Aceptado
                                        var id_administrador = $('#Id_Usuario').val();
                                        var nota = $('#nota_admin_a').val();
                                        var tipo_Mov = 'ACTUALIZAR_ESTATUS_DOC';
                                        var fecha_inicio_ss = $('#fecha_inicio').val();
                                        var id_Usr_Destinatario = $('#id_user').val();
                                        var correo_usr = $('#correo_user').val();
                                        var carrera_usr = $('#carrera_user').val();
                                        var desc_documento = $('#desc_corta_doc').val();

                                        actualiza_Estatus_Documento(tipo_Mov, id_ss, id_doc, id_version, id_estatus, id_administrador, nota, id_Usr_Destinatario, correo_usr, carrera_usr, desc_documento);
                                        actualiza_Estatus_Servicio_Social(id_ss, fecha_inicio_ss, id_Usr_Destinatario, correo_usr, carrera_usr, id_administrador);

                                    },
                                    "Cancelar": function() {
                                        $(this).dialog('close');
                                    }
                                },
                                title: 'Confirmar Aceptación',
                                modal: true,
                                autoOpen: true,
                                resizable: true,
                                draggable: true,
                                dialogClass: 'no-close ventanaConfirmaUsuario',
                                closeOnEscape: false
                            });
                        }
                    },
                    "Cancelar": function() {
                        $(this).dialog('close');
                    }
                },
                close: function() {
                    $("#nota_admin_a").val('');
                },
                title: 'Nota para la Aceptación del Documento',
                modal: true,
                autoOpen: false,
                resizable: false,
                draggable: true,
                show: 'slide',
                hide: 'slide',
                height: 'auto',
                width: '450',
                dialogClass: 'no-close',
                closeOnEscape: false

            }); //FIN CONFIRMAR ACEPTACION

            //CONFIRMAR RECHAZO DEL DOCUMENTO
            $('#ventanaConfirmaRechazo').dialog({
                buttons: {
                    "Aceptar": function() {
                        if (!$('#nota_admin').val().match(miExpReg_Nota_Rechazo)) {
                            $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #');
                            $('#ventanaAvisos').dialog('open');
                        } else {
                            $('#ventanaMensajeConfirma').text('Desea dar por Rechazado este Documento ?');
                            $('#ventanaConfirmaVoBo').dialog({
                                buttons: {
                                    "Aceptar": function() {
                                        $(this).dialog('close');
                                        $('#ventanaProcesando').dialog('open');
                                        var id_ss = $('#id_ss').val();
                                        var id_doc = $('#id_documento').val();
                                        var id_version = $('#id_version_doc').val();
                                        var id_estatus = 4; //4. Rechazado
                                        var id_administrador = $('#Id_Usuario').val();
                                        var nota = $('#nota_admin').val();
                                        var tipo_Mov = 'ACTUALIZAR_ESTATUS_DOC';
                                        var id_Usr_Destinatario = $('#id_user').val();
                                        var correo_usr = $('#correo_user').val();
                                        var carrera_usr = $('#carrera_user').val();
                                        var desc_documento = $('#desc_corta_doc').val();

                                        actualiza_Estatus_Documento(tipo_Mov, id_ss, id_doc, id_version, id_estatus, id_administrador, nota, id_Usr_Destinatario, correo_usr, carrera_usr, desc_documento);

                                    },
                                    "Cancelar": function() {
                                        $(this).dialog('close');
                                    }
                                },
                                title: 'Confirmar Rechazo',
                                modal: true,
                                autoOpen: true,
                                resizable: true,
                                draggable: true,
                                dialogClass: 'no-close ventanaConfirmaUsuario',
                                closeOnEscape: false
                            });
                        }
                    },
                    "Cancelar": function() {
                        $(this).dialog('close');
                    }
                },
                close: function() {
                    $("#nota_admin").val('');
                },
                title: 'Nota para el Rechazo del Documento',
                modal: true,
                autoOpen: false,
                resizable: false,
                draggable: true,
                height: 'auto',
                width: '450',
                show: 'slide',
                hide: 'slide',
                dialogClass: 'no-close',
                closeOnEscape: false

            }); //FIN CONFIRMAR RECHAZO DE DOCUMENTO

            function deshabilitar_Botones(estado) {
                $("#btn_Aceptar_Doc").button("option", "disabled", estado);
                $("#btn_Rechazar_Doc").button("option", "disabled", estado);
            }

            function actualiza_Estatus_Documento(tipo_Mov, id_ss, id_doc, id_version, id_estatus, id_administrador, nota, id_Usr_Destinatario, correo_usr, carrera_usr, desc_documento) {
                $('#ventanaProcesando').dialog('open');
                $.ajax({
                        data: {
                            Tipo_Movimiento: tipo_Mov,
                            id_ss: id_ss,
                            id_doc: id_doc,
                            id_version: id_version,
                            id_estatus: id_estatus,
                            id_administrador: id_administrador,
                            nota: nota,
                            id_Usr_Destinatario: id_Usr_Destinatario,
                            correo_usr: correo_usr,
                            carrera_usr: carrera_usr,
                            desc_documento: desc_documento

                        },
                        type: "POST",
                        dataType: "json",
                        async: false,
                        url: "_Negocio/n_administrador_Aprobar_Servicio_Social.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        if (respuesta.success == true) {
                            deshabilitar_Botones(true);
                            $('#id_estatus_doc').val(id_estatus);
                            $('#ventanaConfirmaAceptacion').dialog('close');
                            $('#ventanaConfirmaRechazo').dialog('close');
                        }
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAviso').html(respuesta.data.message);
                        $('#ventanaAvisos').dialog('open');
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisos').dialog('open');

                    });
            }

            function actualiza_Estatus_Servicio_Social(id_ss, fecha_inicio_ss, id_Usr_Destinatario, correo_usr, carrera_usr, id_administrador) {
                $('#ventanaProcesando').dialog('open');
                $.ajax({
                        data: {
                            Tipo_Movimiento: 'ACTUALIZAR_SERVICIO_SOCIAL',
                            id_ss: id_ss,
                            fecha_inicio_ss: fecha_inicio_ss,
                            id_Usr_Destinatario: id_Usr_Destinatario,
                            correo_usr: correo_usr,
                            carrera_usr: carrera_usr,
                            id_administrador: id_administrador
                        },
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_Aprobar_Servicio_Social.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        if (respuesta.success == true) {
                            $('#ventanaConfirmaAceptacion').dialog('close');
                            deshabilitar_Botones(true);
                        }
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAvisoAceptarServicioSocial').html(respuesta.data.message);
                        $('#ventanaAvisosAceptarServicioSocial').dialog('open');
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAvisoAceptarServicioSocial').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisosAceptarServicioSocial').dialog('open');

                    });
            }


            function Obtener_SS_Por_Autorizar(id_estatus) {
                //Obtenemos los Servicios Sociales por Autorizar
                var datos = {
                    Tipo_Movimiento: 'OBTENER_SS',
                    id_estatus: id_estatus
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_Aprobar_Servicio_Social.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:100%;">';
                        html_table = html_table + '<TR><TH>Clave Alumno</TH>\n\
                                                          <TH>Nombre</TH>\n\
                                                          <TH>Carrera</TH>\n\
                                                          <TH>Fecha de inicio</TH>\n\
                                                          <TH>Programa</TH>\n\
                                                          <TH>Estatus</TH>\n\
                                                          <TH>Acción</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                $btn_MostrarDocs = '<button class="btn_MostrarDocs btnOpcion" data-id_ss=\'' +
                                    value['id_ss'] + '\' data-id_clave_alumno=\'' + value['id_alumno'] + '\' data-nombre=\'' +
                                    value['nombre'] + '\' data-descripcion_carrera=\'' + value['descripcion_carrera'] + '\' data-id_programa=\'' +
                                    value['id_programa'] + '\' data-fecha_inicio_ss = ' + value['fecha_inicio_ss'] + ' data-duracion_meses_ss = ' +
                                    value['duracion_meses_ss'] + ' data-avance_creditos_ss=' + value['avance_creditos_ss'] + ' data-avance_porcentaje_ss= ' +
                                    value['avance_porcentaje_ss'] + ' data-descripcion_tipo_remuneracion = \'' + value['descripcion_tipo_remuneracion'] + '\' data-jefe_inmediato_ss=\'' +
                                    value['jefe_inmediato_ss'] + '\' ' +
                                    ' data-correo = \'' + value['email_usuario'] + '\' ' +
                                    ' data-id_carrera = ' + value['id_carrera'] +
                                    ' data-alumno_tel_fijo = \'' + value['telefono_fijo_alumno'] + '\' ' +
                                    ' data-alumno_tel_celular = \'' + value['telefono_celular_alumno'] + '\' ' +
                                    ' data-alumno_descripcion_programa = \'' + value['descripcion_pss'] + '\' ' + '>Revisar Docs</button>';

                                html_table = html_table + '<TR>';
                                html_table = html_table + '<TD style="text-align:left;">' + value['id_alumno'] + '</TD>';
                                html_table = html_table + '<TD>' + value['nombre'] + '</TD>';
                                html_table = html_table + '<TD>' + value['descripcion_carrera'] + '</TD>';
                                html_table = html_table + '<TD>' + value['fecha_inicio_ss'] + '</TD>';
                                html_table = html_table + '<TD>' + value['id_programa'] + '</TD>';
                                html_table = html_table + '<TD>' + value['descripcion_estatus'] + '</TD>';
                                html_table = html_table + '<TD>' + $btn_MostrarDocs + '</TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Servicios_Sociales').empty();
                            $('#tabla_Servicios_Sociales').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD colspan="7">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Servicios_Sociales').empty();
                            $('#tabla_Servicios_Sociales').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table = html_table + '<TR><TH>Clave Alumno</TH>\n\
                                                          <TH>Nombre</TH>\n\
                                                          <TH>Carrera</TH>\n\
                                                          <TH>Fecha de inicio</TH>\n\
                                                          <TH>Programa</TH>\n\
                                                          <TH>Estatus</TH>\n\
                                                          <TH>Acción</TH></TR>';
                        html_table = html_table + '<TR><TD colspan="7">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Servicios_Sociales').empty();
                        $('#tabla_Servicios_Sociales').html(html_table);
                    });

            } //fin Obtenemos Documentos pendientes de Autorizar                              


            $('#ventanaAvisos').dialog({
                buttons: {
                    "Aceptar": function() {
                        $(this).dialog('close');
                    }
                },
                title: 'Aviso',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: true,
                dialogClass: 'no-close ventanaMensajes',
                closeOnEscape: false
            });

            $('#ventanaAvisosAceptarServicioSocial').dialog({
                buttons: {
                    "Aceptar": function() {
                        $(this).dialog('close');
                    }
                },
                title: 'Aviso',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: true,
                dialogClass: 'no-close ventanaMensajes',
                closeOnEscape: false
            });

            $('#ventanaProcesando').dialog({
                title: '',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: false,
                dialogClass: 'no-close no-titlebar',
                closeOnEscape: false
            });

            function f5(that, val) {
                if (val) {
                    that.on("keydown", function(e) {
                        var code = (e.keyCode ? e.keyCode : e.which);
                        if (code == 116 || code == 8) {
                            e.preventDefault();
                        }
                    })
                } else {
                    that.off("keydown");
                }
            }

            $('input:text, textarea').focus(function(e) {
                e.preventDefault();
                f5($(document), false);
            });
            $('input:text, textarea').blur(function(e) {
                e.preventDefault();
                f5($(document), true);
            });


            $('#ventanaMostrarPDF').hide();
            $('#ventanaConfirmaAceptacion').hide();
            $('#ventanaConfirmaRechazo').hide();

            f5($(document), true);
            Obtener_SS_Por_Autorizar(2); //Servicios Sociales con estatus 2.Por Autorizar
        });
    </script>

    <!--    </head>
    <body>
        <header>
            Mi Pefil
        </header>-->
    <div>
        <div class="encabezado_Formulario">
            <div class="descripcion_Modulo">
                <p>Aprobar Servicio Social</p>
            </div>
        </div>
        <div>
            <div id="tabla_Servicios_Sociales" class="tabla_Registros">
            </div>
        </div>
        <input type="hidden" id="Id_Carrera" name="Id_Carrera" value="">
        <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
    </div>
    <div id='ventanaMostrarPDF'>
        <div>
            <div style="text-align: right;">
                <button id="btn_verSolicitudInicio" class="btn_Herramientas" style="width: 180px;">Ver Solicitud de Inicio</button>
                <button id="btn_verCartaAceptacion" class="btn_Herramientas" style="width: 180px;">Ver la Carta de Aceptación</button>
                <button id="btn_verHistorialAcademico" class="btn_Herramientas" style="width: 180px;">Ver el Historial Académico</button>
            </div>
            <div style="float: left; border: 1px grey solid; width: 340px;" class="contenido_Formulario">
                <form action="" method="post" id="frmCalificarPDF" name="frmCalificarPDF">
                    <p>
                    <div>
                        <p>
                            <label for="fecha_envio_doc" class="label">Fecha Envio Doc.:</label>
                            <input type="text" name="fecha_envio_doc" id="fecha_envio_doc" style="width: 200px;" readonly value="" />
                        </p>
                        <input type="hidden" name="id_version_doc" id="id_version_doc" value="" />
                        <input type="hidden" name="id_documento" id="id_documento" value="" />
                        </p>
                        <hr><br>

                        <p>
                            <label for="cuenta" class="label">Cuenta:</label>
                            <input type="text" name="cuenta" id="cuenta" style="width: 200px;" readonly value="" />
                            <input type="hidden" name="id_ss" id="id_ss" value="" />
                        </p>
                        <p>
                            <label for="alumno" class="label">Alumno:</label>
                            <textarea name="alumno" id="alumno" style="width: 200px;" readonly value=""></textarea>
                        </p>
                        <p>
                            <label for="carrera" class="label">Carrera:</label>
                            <input type="text" name="carrera" id="carrera" style="width: 200px;" readonly value="" />
                        </p>
                        <p>
                            <label for="telefono_fijo" class="label">Tel. Fijo:</label>
                            <textarea type="text" name="telefono_fijo" id="telefono_fijo" style="width: 200px;" readonly value=""></textarea>
                        </p>
                        <p>
                            <label for="telefono_celular" class="label">Celular:</label>
                            <input type="text" name="telefono_celular" id="telefono_celular" style="width: 200px;" readonly value="" />
                        </p>
                        <p>
                            <label for="programa" class="label">Programa Serv.Soc.:</label>
                            <input type="text" name="programa" id="programa" style="width: 200px;" readonly value="" />
                        </p>
                        <p>
                            <label for="descripcion_programa" class="label">Descripción:</label>
                            <textarea name="descripcion_programa" id="descripcion_programa" style="width: 200px;" readonly value="" />
                        </p>
                        <p>
                            <label for="fecha_inicio" class="label">Fecha de inicio:</label>
                            <input type="text" name="fecha_inicio" id="fecha_inicio" style="width: 200px;" readonly value="" />
                        </p>
                        <p>
                            <label for="duracion" class="label">Duración (meses):</label>
                            <input type="text" name="duracion" id="duracion" style="width: 200px;" readonly value="" />
                        </p>
                        <p>
                            <label for="avance_creditos" class="label">Avance en creditos:</label>
                            <input type="text" name="avance_creditos" id="avance_creditos" style="width: 200px;" readonly value="" />
                        </p>
                        <p>
                            <label for="porcentaje_avance" class="label">Porcentaje de avance:</label>
                            <input type="text" name="porcentaje_avance" id="porcentaje_avance" style="width: 200px;" readonly value="" />
                        </p>
                        <p>
                            <label for="jefe_inmediato" class="label">Jefe inmediato:</label>
                            <textarea name="jefe_inmediato" id="jefe_inmediato" style="width: 200px;" readonly value=""></textarea>
                        </p>

                        <!--<input type="hidden" id="id_ss_doc" name="id_ss_doc" value="">-->
                        <input type="hidden" id="id_documento_doc" name="id_documento_doc" value="">
                        <input type="hidden" id="id_version_doc" name="id_version_doc" value="">
                        <input type="hidden" id="id_estatus_doc" name="id_estatus_doc" value="">
                        <input type="hidden" id="id_usuario_doc" name="id_usuario_doc" value="">
                        <input type="hidden" id="desc_corta_doc" name="desc_corta_doc" value="">
                        <input type="hidden" id="correo_user" name="correo_user" value="">
                        <input type="hidden" id="carrera_user" name="correo_user" value="">
                        <input type="hidden" id="id_user" name="id_user" value="">
                    </div>
                    </p>
                </form>
            </div>
            <div id="archivoPDF" style="float: right; border: 1px grey solid; height: 500px; width: 590px; ">
                <object id="obj_PDF" width="590px" height="500px"></object>
            </div>

        </div>
    </div>

    <div id="ventanaConfirmaAceptacion">
        <div id="nota">
            <p>
                <textarea id="nota_admin_a" class="notaVoBo entrada_Dato"
                    maxlength="500" placeholder="" onkeyup="javascript:this.value=this.value.toUpperCase();"
                    title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
            </p>
        </div>
        <!--<span style="margin-top: 5px; color: #990000;">Desea dar el estatus de "Aceptado" al Documento seleccionado ?</span>         -->

    </div>
    <div id="ventanaConfirmaRechazo">

        <p>
            <textarea id="nota_admin" class="notaVoBo entrada_Dato"
                maxlength="500" placeholder="" onkeyup="javascript:this.value=this.value.toUpperCase();"
                title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
        </p>
        <!--<span style="margin-top: 5px; color: #990000;">Desea dar el estatus de "Rechazado" al Documento seleccionado ?</span>         -->

    </div>
    <div id="ventanaConfirmaVoBo">
        <span id="ventanaMensajeConfirma"></span>
    </div>


    <div id="ventanaAvisos">
        <span id="ventanaAviso"></span>
    </div>
    <div id="ventanaAvisosAceptarServicioSocial">
        <span id="ventanaAvisoAceptarServicioSocial"></span>
    </div>

    <div id="ventanaProcesando" data-role="header">
        <img id="cargador" src="css/images/engrane2.gif" /><br>
        Procesando su transacción....!<br>
        Espere por favor.
    </div>

    <!--    </body>
</html>-->
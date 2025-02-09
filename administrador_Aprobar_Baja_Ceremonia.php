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
Fecha:          Septiembre,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para aprobar las Solicitudes de Baja de ceremonia
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
            function llena_Catalogo(nom_control, tipo_movimiento, tabla_catalogo, tabla_campos,
                tabla_where, tabla_orderby) {

                var datos = {
                    Tipo_Movimiento: tipo_movimiento,
                    tabla_Catalogo: tabla_catalogo,
                    tabla_Campos: tabla_campos,
                    tabla_Where: tabla_where,
                    tabla_OrderBy: tabla_orderby
                };
                $.ajax({
                        data: datos,
                        type: 'POST',
                        dataType: 'json',
                        url: '_Negocio/n_administrador_Crear_Nueva_Cuenta.php'
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_options = '';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                //recorremos los valores de cada usuario
                                html_options = html_options + '<option value=' + value['id'] +
                                    '>' + value['descripcion'] + '</option>';
                            });
                            $('#' + nom_control).empty();
                            $('#' + nom_control).html(html_options);

                            $('#' + nom_control + ' option:first-child').attr('selected', 'selected');
                        } else {
                            $('#ventanaAviso').html(respuesta.data.message);
                            $('#ventanaAvisos').dialog('open');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisos').dialog('open');
                    });
            }
            //FIN LLENADO DE CATALOGO
            //                               
            //OBTENEMOS LAS SOLICITUDES DE BAJA
            function Obtener_Solicitudes_De_Baja() {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_SOLICITUDES'
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_Aprobar_Baja_Ceremonia.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:100%;">';
                        html_table = html_table + '<TR><TH>Clave Ceremonia</TH>\n\
                                                      <TH>Modalidad</TH>\n\
                                                      <TH>Carrera</TH>\n\
                                                      <TH>No. Cuenta</TH>\n\
                                                      <TH>Nombre</TH>\n\
                                                      <TH>Nota</TH>\n\
                                                      <TH>Acción</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                $btn_RevisarDoc = '<button class="btn_RevisarDoc btnOpcion" data-id_alumno=\'' + value['id_alumno'] + '\'' +
                                    ' data-id_ceremonia=\'' + value['id_ceremonia'] + '\'' +
                                    ' data-id_carrera = \'' + value['id_carrera'] + '\' ' +
                                    ' data-correo = \'' + value['email_usuario'] + '\' >Revisar Doc</button>';

                                html_table = html_table + '<TR>';
                                html_table = html_table + '<TD>' + value['id_ceremonia'] + '</TD>';
                                html_table = html_table + '<TD style="text-align:left;">' + value['descripcion_tipo_propuesta'] + '</TD>';
                                html_table = html_table + '<TD>' + value['id_carrera'] + '</TD>';
                                html_table = html_table + '<TD>' + value['id_alumno'] + '</TD>';
                                html_table = html_table + '<TD>' + value['nombre'] + '</TD>';
                                html_table = html_table + '<TD>' + esNulo(value['nota_baja']) + '</TD>';
                                html_table = html_table + '<TD>' + $btn_RevisarDoc + '</TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Solicitudes_Baja').empty();
                            $('#tabla_Solicitudes_Baja').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD colspan="7">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Solicitudes_Baja').empty();
                            $('#tabla_Solicitudes_Baja').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE style="width:100%;">';
                        html_table = html_table + '<TR><TH>Clave Ceremonia</TH>\n\
                                                           <TH>Modalidad</TH>\n\
                                                           <TH>Carrera</TH>\n\
                                                           <TH>No. Cuenta</TH>\n\
                                                           <TH>Nombre</TH>\n\
                                                           <TH>Nota</TH>\n\
                                                           <TH>Acción</TH></TR>';
                        html_table = html_table + '<TR><TD colspan="7">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Solicitudes_Baja').empty();
                        $('#tabla_Solicitudes_Baja').html(html_table);
                    });
            }
            //FIN OBTENEMOS LAS SOLICITUDES DE BAJA

            //EVENTO CLICK SOBRE EL BOTON REVISAR SOLICITUD
            $('#tabla_Solicitudes_Baja').on("click", "button.btn_RevisarDoc", function(e) {
                e.preventDefault();
                var id_alumno = $(this).data('id_alumno');
                $('#id_alumno').attr("value", $(this).data('id_alumno'));
                $('#id_ceremonia').attr("value", $(this).data('id_ceremonia'));
                $('#correo_user').attr("value", $(this).data('correo'));
                $('#carrera_user').attr("value", $(this).data('id_carrera'));
                $('#desc_corta_doc').attr("value", 'Solicitud_Baja_Ceremonia');

                var tituloAdjuntar = id_alumno + "_" +
                    $('#carrera_user').val() + "_" +
                    $('#id_ceremonia').val() + "_" +
                    $('#desc_corta_doc').val() + ".pdf";

                $('#ventanaSolicitud_PDF').dialog({
                    open: function() {
                        $('#ventanaSolicitud_PDF').dialog("option", "title", tituloAdjuntar);
                        var tiempo = new Date();
                        var fileName = "Docs/Baja_de_Ceremonia/" +
                            id_alumno + "_" +
                            $('#carrera_user').val() + "_" +
                            $('#id_ceremonia').val() + "_" +
                            $('#desc_corta_doc').val() + ".pdf?" + tiempo;

                        var new_Object = $('#obj_PDF_solicitudBaja').clone(false);
                        new_Object.attr("type", "application/pdf");
                        new_Object.attr("data", fileName);
                        $("#obj_PDF_solicitudBaja").replaceWith(new_Object);
                        $('#btn_autorizar').prop("disabled", '');
                        $('#btn_rechazar').prop("disabled", '');

                    },
                    title: 'Aprobar Solicitud de Baja',
                    modal: true,
                    autoOpen: true,
                    resizable: false,
                    draggable: true,
                    width: '1000',
                    height: '630',
                    dialogClass: 'no-close',
                    closeOnEscape: false,
                    position: {
                        at: 'top'
                    }
                });
            });
            //FIN EVENTO CLICK SOBRE EL BOTON REVISAR CARTA TERMINACION

            //CLICK AL BOTON ACEPTAR SOLICITUD DE BAJA
            $('#btn_autorizar').on('click', function(e) {
                e.preventDefault();
                $('#ventanaConfirmar_Aceptacion').dialog({
                    buttons: {
                        "Aceptar": function() {
                            if (!$('#nota_admin_a').val().match(miExpReg_Nota_Aceptacion)) {
                                $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #');
                                $('#ventanaAvisos').dialog('open');
                            } else {
                                $('#ventanaMensajeConfirma').text('Desea dar por Aceptada esta Solicitud de Baja de Ceremonia ?');
                                $('#ventanaConfirmaVoBo').dialog({
                                    buttons: {
                                        "Aceptar": function() {
                                            $(this).dialog('close');
                                            var id_ceremonia = $('#id_ceremonia').val();
                                            var id_estatus = 3; //3.Aceptado
                                            var id_administrador = $('#Id_Usuario').val();
                                            var nota = $('#nota_admin_a').val();
                                            var tipo_Mov = 'ACTUALIZAR_ESTATUS_CEREMONIA';

                                            var id_Usr_Destinatario = $('#id_alumno').val();
                                            var correo_usr = $('#correo_user').val();
                                            var carrera_usr = $('#carrera_user').val();
                                            var desc_documento = $('#desc_corta_doc').val();
                                            var id_tipo_baja = $('#selec_Tipo_Baja').val();

                                            actualiza_Estatus_Documento(tipo_Mov, id_ceremonia,
                                                id_estatus, id_administrador, nota, id_Usr_Destinatario,
                                                correo_usr, carrera_usr, id_tipo_baja);

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
                            $('#nota_admin_a').val('');
                            $(this).dialog('close');
                        }
                    },
                    close: function() {
                        $("#nota_admin_a").val('');
                        $(this).dialog('destroy');
                    },
                    title: 'Nota para la Aceptación de la Solicitud de Baja',
                    modal: true,
                    autoOpen: true,
                    resizable: false,
                    draggable: true,
                    height: 'auto',
                    width: '450',
                    show: 'slide',
                    hide: 'slide',
                    dialogClass: 'no-close',
                    closeOnEscape: false
                }); //FIN ventanaConfirmar_Aceptacion
            });
            //FIN CLICK AL BOTON ACEPTAR SOLICITUD DE BAJA

            //CLICK AL BOTON RECHAZAR SOLICITUD DE BAJA
            $('#btn_rechazar').on('click', function(e) {
                e.preventDefault();
                $('#ventanaConfirmar_Rechazo').dialog({
                    buttons: {
                        "Aceptar": function() {
                            if (!$('#nota_admin').val().match(miExpReg_Nota_Rechazo)) {
                                $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) . - _ #');
                                $('#ventanaAvisos').dialog('open');
                            } else {
                                $('#ventanaMensajeConfirma').text('Desea dar por Rechazada esta Solicitud de Baja ?');
                                $('#ventanaConfirmaVoBo').dialog({
                                    buttons: {
                                        "Aceptar": function() {
                                            $(this).dialog('close');
                                            var id_ceremonia = $('#id_ceremonia').val();
                                            var id_estatus = 4; //4.Rechazado
                                            var id_administrador = $('#Id_Usuario').val();
                                            var nota = $('#nota_admin').val();
                                            var tipo_Mov = 'ACTUALIZAR_ESTATUS_CEREMONIA';

                                            var id_Usr_Destinatario = $('#id_alumno').val();
                                            var correo_usr = $('#correo_user').val();
                                            var carrera_usr = $('#carrera_user').val();
                                            var desc_documento = $('#desc_corta_doc').val();
                                            var id_tipo_baja = 5; //se queda como Ceremonia Activa

                                            actualiza_Estatus_Documento(tipo_Mov, id_ceremonia,
                                                id_estatus, id_administrador, nota, id_Usr_Destinatario,
                                                correo_usr, carrera_usr, id_tipo_baja);

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
                            $("#nota_admin").val('');
                            $(this).dialog('destroy');
                        }
                    },
                    close: function() {
                        $("#nota_admin").val('');
                        $(this).dialog('destroy');
                    },
                    title: 'Nota de Rechazo para la Solicitud de Baja',
                    modal: true,
                    autoOpen: true,
                    resizable: false,
                    draggable: true,
                    height: 'auto',
                    width: '450',
                    show: 'slide',
                    hide: 'slide',
                    dialogClass: 'no-close',
                    closeOnEscape: false
                });
            }); //FIN CONFIRMAR RECHAZO DE LA SOLICITUD DE BAJA

            function actualiza_Estatus_Documento(tipo_Mov, id_ceremonia,
                id_estatus, id_administrador, nota, id_Usr_Destinatario,
                correo_usr, carrera_usr, id_tipo_baja) {

                $('#ventanaProcesando').dialog('open');
                $.ajax({
                        data: {
                            Tipo_Movimiento: tipo_Mov,
                            id_ceremonia: id_ceremonia,
                            id_estatus: id_estatus,
                            id_administrador: id_administrador,
                            nota: nota,
                            id_Usr_Destinatario: id_Usr_Destinatario,
                            correo_usr: correo_usr,
                            carrera_usr: carrera_usr,
                            id_tipo_baja: id_tipo_baja
                        },
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_Aprobar_Baja_Ceremonia.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        if (respuesta.success === true) {
                            if (id_estatus == 3) { //3.aceptado
                                $('#ventanaConfirmar_Aceptacion').dialog('close');
                            } else {
                                $('#ventanaConfirmar_Rechazo').dialog('close');
                            }
                            $('#btn_autorizar').prop("disabled", 'disabled');
                            $('#btn_rechazar').prop("disabled", 'disabled');
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


            $('#btn_cerrar').on('click', function(e) {
                e.preventDefault();
                Obtener_Solicitudes_De_Baja();
                $('#message').empty();
                $(".ui-dialog-content").dialog("close");
            });

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

            $('#ventanaProcesando').dialog({
                title: '',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: false,
                dialogClass: 'no-close no-titlebar',
                closeOnEscape: false
            });


            function esNulo(valor_) {
                if (valor_ == null) {
                    return '';
                } else {
                    return valor_;
                }
            }

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
            /*$('.entrada_Dato').focus(function(e){
                e.preventDefault();
                f5($(document),false);
            });
            $('.entrada_Dato').blur(function(e){
                e.preventDefault();
                f5($(document),true);
            }); */

            llena_Catalogo('selec_Tipo_Baja', 'CATALOGO_GENERALES', 'tipos_baja',
                'id_tipo_baja as id, descripcion_tipo_baja as descripcion',
                'id_tipo_baja IN(3,4,7,8)', 'descripcion_tipo_baja');

            Obtener_Solicitudes_De_Baja();
            $('#ventanaSolicitud_PDF').hide();
            $('#tabla_Mi_Servicio').hide();
            $('#tabla_Mis_Reportes_Bim').hide();
            $('#ventanaConfirmar_Aceptacion').hide();
            $('#ventanaConfirmar_Rechazo').hide();

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
                <p>Aprobar Solicitud de Baja de Ceremonia</p>
            </div>
            <div>
                <div>
                    <div id="tabla_Solicitudes_Baja" class="tabla_Registros">
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
    </div>

    <div id="ventanaSolicitud_PDF">
        <div id="archivoPDF_cartaterminacion" style="position: absolute; z-index: 99; float: left; border: 1px grey solid; height: 550px; width: 700px; ">
            <object id="obj_PDF_solicitudBaja" width="700px" height="550px">
            </object>

        </div>
        <div id="divOpciones_CT" style="text-align: center; float: right; width: 200px; line-height: 2.5em;">
            <button id="btn_autorizar" class="btn_Herramientas" style="width: 160px;">Aceptar Solicitud</button>
            <button id="btn_rechazar" class="btn_Herramientas" style="width: 160px;">Rechazar Solicitud</button>
            <button id="btn_cerrar" class="btn_Herramientas" style="width: 160px;">Cerrar</button>

            <input type="hidden" id="id_ceremonia" name="id_ss" value="">
            <input type="hidden" id="id_alumno" name="id_alumno" value="">
            <input type="hidden" id="correo_user" name="correo_user" value="">
            <input type="hidden" id="carrera_user" name="carrera_user" value="">
            <input type="hidden" id="desc_corta_doc" name="desc_corta_doc" value="">

        </div>

    </div>

    <div id="ventanaConfirmar_Aceptacion">
        <div>
            <label for="selec_Tipo_Baja" class="etiqueta_Parametro">Tipo de Baja:</label>
            <select id="selec_Tipo_Baja" name="selec_Tipo_Baja" class="combo_Parametro" style="width: 250px;">
            </select>
        </div>
        <textarea id="nota_admin_a" class="entrada_Dato notaVoBo" maxlength="500" onkeyup="javascript:this.value=this.value.toUpperCase();"
            style="margin-top:5px;" title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
    </div>
    <div id="ventanaConfirmar_Rechazo">
        <textarea id="nota_admin" class="entrada_Dato notaVoBo" maxlength="500" onkeyup="javascript:this.value=this.value.toUpperCase();"
            title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
    </div>
    <div id="ventanaConfirmaVoBo">
        <span id="ventanaMensajeConfirma"></span>
    </div>

    <div id="ventanaAvisos">
        <span id="ventanaAviso"></span>
    </div>
    <div id="ventanaProcesando" data-role="header">
        <img id="cargador" src="css/images/engrane2.gif" /><br>
        Procesando su transacción....!<br>
        Espere por favor.
    </div>
    <!--    </body>
</html>-->
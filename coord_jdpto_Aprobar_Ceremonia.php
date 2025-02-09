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
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para Aprobar los documentos para Ceremonia Coordinación
-->

<html>

<head>

    <!--        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="./assets/libs/jquery-ui-1.11.4/jquery-ui.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="menu/estilo_menu.css" /> 
        <script src="./assets/libs/jquery-1.12-4/jquery-1.12.4.min.js"></script>
        <script src="./assets/libs/jquery-ui-1.11.4/jquery-ui.min.js"></script>-->
    <script src="./assets/js/expresiones_reg.js"></script>

    <script>
        $(document).ready(function() {

            function Obtener_Ceremonias(id_usuario) {
                //Obtenemos las ceremonias por Autorizar
                var datos = {
                    Tipo_Movimiento: 'OBTENER_CEREMONIAS_PENDIENTES',
                    id_usuario: id_usuario
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_coord_jdpto_Aprobar_Ceremonia.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:100%;">';
                        html_table = html_table + '<TR><TH>Ceremonia</TH>\n\
                                                          <TH>Modalidad</TH>\n\
                                                          <TH>No.Cta.</TH>\n\
                                                          <TH>Alumno</TH>\n\
                                                          <TH>Carrera</TH>\n\
                                                          <TH>Fecha Alta</TH>\n\
                                                          <TH>Acción</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                $btn_MostrarDocs = '<button class="btn_MostrarDocs btnOpcion" data-id_ceremonia=\'' + value['id_ceremonia'] +
                                    '\' data-id_alumno=\'' + value['id_alumno'] +
                                    '\' data-nombre=\'' + value['nombre'] +
                                    '\' data-descripcion_modalidad=\'' + value['descripcion_tipo_propuesta'] +
                                    '\' data-correo = \'' + value['email_usuario'] +
                                    '\' data-fecha_alta = \'' + value['fecha_alta'] +
                                    '\' data-id_carrera = ' + value['id_carrera'] + '\'>Ver Docs</button>';

                                html_table = html_table + '<TR>';
                                html_table = html_table + '<TD style="text-align:left;">' + value['id_ceremonia'] + '</TD>';
                                html_table = html_table + '<TD>' + value['descripcion_tipo_propuesta'] + '</TD>';
                                html_table = html_table + '<TD>' + value['id_alumno'] + '</TD>';
                                html_table = html_table + '<TD>' + value['nombre'] + '</TD>';
                                html_table = html_table + '<TD>' + value['id_carrera'] + '</TD>';
                                html_table = html_table + '<TD>' + value['fecha_alta'] + '</TD>';
                                html_table = html_table + '<TD>' + $btn_MostrarDocs + '</TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Ceremonias').empty();
                            $('#tabla_Ceremonias').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD colspan="7">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Ceremonias').empty();
                            $('#tabla_Ceremonias').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table = html_table + '<TR><TH>Ceremonia</TH>\n\
                                                                   <TH>Modalidad</TH>\n\
                                                                   <TH>No.Cta.</TH>\n\
                                                                   <TH>Alumno</TH>\n\
                                                                   <TH>Carrera</TH>\n\
                                                                   <TH>Fecha Alta</TH>\n\
                                                                   <TH>Acción</TH></TR>';
                        html_table = html_table + '<TR><TD colspan="7">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Ceremonias').empty();
                        $('#tabla_Ceremonias').html(html_table);
                    });

            } //fin Obtenemos Documentos pendientes de Autorizar       

            //MANEJADOR DE EVENTO CLICK PARA LOS BOTONES DE LA TABLA CEREMONIAS
            $('#tabla_Ceremonias').on("click", "button.btn_MostrarDocs", function(e) {
                e.preventDefault();
                var id_ceremonia = $(this).data('id_ceremonia');
                $('#fecha_alta_ceremonia').val($(this).data('fecha_alta'));
                $('#ventanaMostrarPDF').dialog({

                    buttons: [{
                        id: "btn_Aceptar_Doc",
                        text: "Aceptar Doc",
                        click: function() {
                            $('#nota_admin_a').val('');
                            $('#ventanaConfirmaAceptacion').dialog('open');
                        }
                    }, {
                        id: "btn_Rechazar_Doc",
                        text: "Rechazar Doc",
                        click: function() {
                            $('#nota_admin').val('');
                            $('#ventanaConfirmaRechazo').dialog('open');
                        }
                    }, {
                        id: "btn_Cerrar",
                        text: "Cerrar",
                        click: function() {
                            $(this).dialog('close');
                            Obtener_Ceremonias($('#Id_Usuario').val());
                        }
                    }],
                    open: function() {
                        var fileName = "Docs/Ceremonias/Sin_Seleccionar.pdf" + "?<?php echo date('Y-m-d_H:i:s'); ?>";
                        var new_Object = $('#obj_PDF').clone(true);
                        new_Object.attr("type", "application/pdf");
                        new_Object.attr("data", fileName);
                        $("#obj_PDF").replaceWith(new_Object);
                        deshabilitar_Botones(true);
                        //LLENAMOS LA TABLA DE DOCUMENTOS PENDIENTES
                        Obtener_Documentos_Pendientes(id_ceremonia, 9); //2. Por Autorizar Coordinación/Dpto.

                    },
                    close: function() {},
                    title: 'Aprobación de Documentos para Ceremonia',
                    modal: true,
                    autoOpen: true,
                    resizable: true,
                    draggable: true,
                    height: '630',
                    width: '1000',
                    show: 'slide',
                    hide: 'slide',
                    dialogClass: 'no-close',
                    closeOnEscape: false,
                    position: {
                        at: 'center top'
                    }

                }); //FIN MOSTRAR VENTANA PDF      
            }); //FIN TABLA CEREMONIAS 

            function Obtener_Documentos_Pendientes(id_ceremonia, id_estatus) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_DOCUMENTOS_PENDIENTES',
                    id_ceremonia: id_ceremonia,
                    id_estatus: id_estatus
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_coord_jdpto_Aprobar_Ceremonia.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table = html_table + '<TR><TH>Documento</TH>\n\
                                                          <TH>Estatus</TH>\n\
                                                          <TH>Seleccionado</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                data_radio = ' data-id_ceremonia = \'' + value['id_ceremonia'] +
                                    '\' data-id_alumno =\'' + value['id_alumno'] +
                                    '\' data-id_carrera =\'' + value['id_carrera'] +
                                    '\' data-descripcion_documento =\'' + value['descripcion_documento'] +
                                    '\' data-desc_nom_archivo =\'' + value['descripcion_para_nom_archivo'] +
                                    '\' data-id_documento= ' + value['id_documento'] +
                                    ' data-version = ' + value['version'] +
                                    ' data-id_estatus= ' + value['id_estatus'];
                                radio = "<input type='radio' id=radio_" + value['id_documento'] + " name='documentos_pendientes' \n\
                                        value='' " + data_radio + ">";
                                estatus_span = "<span id=span_" + value['id_documento'] +
                                    " data-id_estatus=" + value['id_estatus'] + ">" + value['descripcion_estatus'] + "</span>";
                                html_table = html_table + '<TR>';
                                html_table = html_table + '<TD>' + value['descripcion_documento'] + '</TD>';
                                html_table = html_table + '<TD>' + estatus_span + '</TD>';
                                html_table = html_table + '<TD style="text-align:center;">' + radio + '</TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_docs_pendientes').empty();
                            $('#tabla_docs_pendientes').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD colspan="3">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Ceremonias').empty();
                            $('#tabla_Ceremonias').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table = html_table + '<TR><TH>Documento</TH>\n\
                                                          <TH>Estatus</TH>\n\
                                                          <TH>Seleccionado</TH></TR>';
                        html_table = html_table + '<TR><TD colspan="3">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_docs_pendientes').empty();
                        $('#tabla_docs_pendientes').html(html_table);
                    });

            } //fin Obtenemos Documentos pendientes de Autorizar    

            $('#tabla_docs_pendientes').on("click", "input:radio", function(e) {
                var archivo = $(this).data('id_alumno') + '_' +
                    $(this).data('id_carrera') + '_' +
                    $(this).data('id_ceremonia') + '_' +
                    $(this).data('version') + '_' +
                    $(this).data('desc_nom_archivo') + '.pdf';

                $('#id_documento_ceremonia').val($(this).data('id_documento'));
                if ($(this).data('id_estatus') == '9') {
                    deshabilitar_Botones(false);
                } else {
                    deshabilitar_Botones(true);
                }

                var fileName = "Docs/Ceremonias/" + archivo + "?<?php echo date('Y-m-d_H:i:s'); ?>";
                var new_Object = $('#obj_PDF').clone(true);
                new_Object.attr("type", "application/pdf");
                new_Object.attr("data", fileName);
                $("#obj_PDF").replaceWith(new_Object);
            });

            $('#ventanaConfirmaAceptacion').dialog({
                buttons: {
                    "Aceptar": function() {
                        control_span = 'span_' + $('#id_documento_ceremonia').val();
                        control_radio = 'radio_' + $('#id_documento_ceremonia').val();
                        var todos_revisados = 0;
                        if (!$('#nota_admin_a').val().match(/^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9\.\,\;\:\?\¿\(\)\-\_\#\n]{0,500}$/)) {
                            $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #');
                            $('#ventanaAvisos').dialog('open');
                        } else {
                            $('#ventanaMensajeConfirma').text('Desea dar por Aceptado este Documento ?');
                            $('#ventanaConfirmaVoBo').dialog({
                                buttons: {
                                    "Aceptar": function() {
                                        $(this).dialog('close');
                                        //                                                    $('#ventanaProcesando').dialog('open');
                                        $('#' + control_span).text('Aceptado');
                                        $('#' + control_span).data('id_estatus', 3);
                                        $('#radio_' + $('#id_documento_ceremonia').val()).data('id_estatus', 3);

                                        $('#id_ceremonia').val($('#' + control_radio).data('id_ceremonia'));
                                        $('#id_documento_ceremonia').val($('#' + control_radio).data('id_documento'));
                                        $('#version').val($('#' + control_radio).data('version'));
                                        $('#desc_corta_nom_archivo').val($('#' + control_radio).data('desc_nom_archivo'));
                                        $('#desc_documento').val($('#' + control_radio).data('descripcion_documento'));

                                        var resum = [];
                                        resum = verifica_Estatus();
                                        var rechazados = resum[4];
                                        var todos_revisados = 0;
                                        var datos_archivos = resum[5];
                                        if (resum[1] == 0) { //todos los docs revisados
                                            todos_revisados = 1;
                                        }

                                        actualiza_Estatus_Documento(3, $('#nota_admin_a').val(),
                                            todos_revisados, rechazados, datos_archivos); //3.Aceptado

                                        //                                                    deshabilitar_Botones(true);
                                        //                                                    $(this).dialog('close');

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
                        control_span = 'span_' + $('#id_documento_ceremonia').val();
                        control_radio = 'radio_' + $('#id_documento_ceremonia').val();
                        if (!$('#nota_admin').val().match(/^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9\.\,\;\:\?\¿\(\)\-\_\#\n]{5,500}$/)) {
                            $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #');
                            $('#ventanaAvisos').dialog('open');
                        } else {
                            $('#ventanaMensajeConfirma').text('Desea dar por Rechazado este Documento ?');
                            $('#ventanaConfirmaVoBo').dialog({
                                buttons: {
                                    "Aceptar": function() {
                                        $(this).dialog('close');
                                        //                                                    $('#ventanaProcesando').dialog('open');
                                        $('#' + control_span).text('Rechazado');
                                        $('#' + control_span).data('id_estatus', 4);

                                        $('#radio_' + $('#id_documento_ceremonia').val()).data('id_estatus', 4);

                                        $('#id_ceremonia').val($('#' + control_radio).data('id_ceremonia'));
                                        $('#id_documento_ceremonia').val($('#' + control_radio).data('id_documento'));
                                        $('#version').val($('#' + control_radio).data('version'));
                                        $('#desc_corta_nom_archivo').val($('#' + control_radio).data('desc_nom_archivo'));
                                        $('#desc_documento').val($('#' + control_radio).data('descripcion_documento'));

                                        var resum = [];
                                        resum = verifica_Estatus();
                                        var rechazados = resum[4];
                                        var todos_revisados = 0;
                                        var datos_archivos = resum[5];
                                        if (resum[1] == 0) { //ya no haya con estatus Por aut coord/dpto
                                            todos_revisados = 1;
                                        }

                                        actualiza_Estatus_Documento(4, $('#nota_admin').val(),
                                            todos_revisados, rechazados, datos_archivos); //Rechazado

                                        //                                                    deshabilitar_Botones(true);   


                                        //                                                    $(this).dialog('close');

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

            function verifica_Estatus() { //                  
                var resumen = [];
                resumen[0] = 0; //total
                resumen[1] = 0; //por autorizar coord
                resumen[2] = 0; //aceptados
                resumen[3] = 0; //rechazados
                resumen[4] = ''; // documento,version de los rechazados
                resumen[5] = ''; // datos del nombre de archivo
                var datos_archivo = '';
                $('#tabla_docs_pendientes input[type=radio]').each(function() {
                    resumen[0] += 1;
                    datos_archivo += $(this).data('id_alumno') + ',';
                    datos_archivo += $(this).data('id_carrera') + ',';
                    datos_archivo += $(this).data('id_ceremonia') + ',';
                    datos_archivo += $(this).data('version') + ',';
                    datos_archivo += $(this).data('desc_nom_archivo') + '|';

                    if ($(this).data('id_estatus') == '9') {
                        resumen[1] += 1;
                    } else if ($(this).data('id_estatus') == '3') {
                        resumen[2] += 1;
                    } else if ($(this).data('id_estatus') == '4') {
                        resumen[3] += 1;
                        resumen[4] += $(this).data('id_documento') + ',' + $(this).data('version') + ',' + $(this).data('desc_nom_archivo') + '|';
                    }
                });
                if (resumen[4] != '') {
                    resumen[4] = resumen[4].substr(0, resumen[4].length - 1);
                }
                datos_archivo = datos_archivo.substr(0, datos_archivo.length - 1);
                resumen[5] = datos_archivo;
                return resumen;

            }

            function actualiza_Estatus_Documento(id_estatus, nota, todos_revisados, rechazados, datos_archivos) {
                $('#ventanaProcesando').dialog('open');
                $.ajax({
                        data: {
                            Tipo_Movimiento: 'ACTUALIZAR_ESTATUS_DOC',
                            id_ceremonia: $('#id_ceremonia').val(),
                            id_documento: $('#id_documento_ceremonia').val(),
                            version: $('#version').val(),
                            desc_documento: $('#desc_documento').val(),
                            nota: nota,
                            id_estatus: id_estatus,
                            id_admin: $('#Id_Usuario').val(),
                            mail_admin: $('#mail_admin').val(),
                            todos_revisados: todos_revisados,
                            desc_corta_nom_archivo: $('#desc_corta_nom_archivo').val(),
                            rechazados: rechazados,
                            datos_archivos: datos_archivos,
                            fecha_alta_ceremonia: $('#fecha_alta_ceremonia').val()
                        },
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_coord_jdpto_Aprobar_Ceremonia.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        if (respuesta.success == true) {
                            if (id_estatus == 3) { //3.aceptado
                                $('#ventanaConfirmaAceptacion').dialog('close');
                            } else {
                                $('#ventanaConfirmaRechazo').dialog('close');
                            }
                            deshabilitar_Botones(true);
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
            Obtener_Ceremonias($('#Id_Usuario').val()); //
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
                <p>Aprobar Ceremonia</p>
            </div>
        </div>
        <div id="tabla_Ceremonias" class="tabla_Registros">

        </div>
    </div>

    <div id='ventanaMostrarPDF'>
        <div id="contenido_MostrarPDF" style="padding-top: 0px; text-align: left;">
            <div style="float: left;">
                <div id="" style="overflow-y: auto; text-align: left;border: 1px solid grey; height: 500px; width: 350px;">
                    <div id="tabla_docs_pendientes"></div>
                </div>
                <input type="hidden" id="id_ceremonia" name="id_ceremonia" value="">
                <input type="hidden" id="id_documento_ceremonia" name="id_documento_ceremonia" value="">
                <input type="hidden" id="version" name="version" value="">
                <input type="hidden" id="desc_documento" name="desc_documento" value="">
                <input type="hidden" id="fecha_alta_ceremonia" name="fecha_alta_ceremonia" value="">
                <input type="hidden" id="mail_admin" name="mail_admin" value="<?php echo $_SESSION['correo_usuario_sesion']; ?>">
                <input type="hidden" id="desc_corta_nom_archivo" name="desc_corta_nom_archivo" value="">
                <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
            </div>
            <div id="archivoPDF" style="float: right; border: 1px grey solid; height: 500px; width: 600px; ">
                <object id="obj_PDF" width="600px" height="500px"></object>
            </div>

        </div>
    </div>

    <div id="ventanaConfirmaAceptacion">
        <textarea id="nota_admin_a" class="entrada_Dato notaVoBo"
            maxlength="500" placeholder="" onkeyup="javascript:this.value=this.value.toUpperCase();"
            title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off">
            </textarea>
    </div>
    <div id="ventanaConfirmaRechazo">
        <textarea id="nota_admin" class="entrada_Dato notaVoBo"
            maxlength="500" placeholder="" onkeyup="javascript:this.value=this.value.toUpperCase();"
            title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off">
            </textarea>
    </div>
    <div id="ventanaAvisos">
        <span id="ventanaAviso"></span>
    </div>
    <div id="ventanaAvisosAceptarServicioSocial">
        <span id="ventanaAvisoAceptarServicioSocial"></span>
    </div>
    <div id="ventanaConfirmaVoBo">
        <span id="ventanaMensajeConfirma"></span>
    </div>
    <div id="ventanaProcesando" data-role="header">
        <img id="cargador" src="./assets/images/ui/engrane2.gif" /><br>
        Procesando su transacción....!<br>
        Espere por favor.
    </div>
    <!--Se quita el botón de home-->
    <!--    </body>
</html>-->
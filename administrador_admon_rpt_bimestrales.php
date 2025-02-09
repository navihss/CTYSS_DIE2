<!DOCTYPE html>
<!--
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para la admon. de los Reportes Bimestrales
-->
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
            $('#btn_Buscar').on('click', function(e) {
                if (!$('#id_ss_rb').val().match(miExpReg_Servicio_Social)) {
                    $('#ventanaAviso').html("Sólo puede Capturar Números");
                    $('#ventanaAvisos').dialog('open');
                } else {
                    e.preventDefault();
                    Obtener_Reportes_Bimestrales($('#id_ss_rb').val());
                }
            });

            $('#id_ss_rb').on('keyup', function(e) {
                e.preventDefault();
                $('#tabla_Reportes').empty();
                $('#info_alumno').empty();
            });

            //OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
            function Obtener_Reportes_Bimestrales(id_ss) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_REPORTES_BIMESTRALES',
                    id_ss: id_ss
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_admon_rpt_bimestrales.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:100%;">';
                        html_table += '<TR><TH>No.<br>Rpt</TH>\n\
                                        <TH>Ult. Ver.</TH>\n\
                                        <TH>F. Prog. Inicio</TH>\n\
                                        <TH>F. Prog. Término</TH>\n\
                                        <TH>Hrs Prog.</TH>\n\
                                        <TH>F. Real Inicio</TH>\n\
                                        <TH>F. Real Término</TH>\n\
                                        <TH>F. de Envío</TH>\n\
                                        <TH>Hrs realizadas</TH>\n\
                                        <TH>Estatus</TH>\n\
                                        <TH>Nota</TH>\n\
                                        <TH>Acción</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            var contador = 1;
                            var n_registros = respuesta.data.registros.length;
                            var horas_obligatorias = 0;
                            var horas_laboradas = 0;

                            $.each(respuesta.data.registros, function(key, value) {
                                $('#info_alumno').html('<b>Alumno:</b> ' + value['id_alumno'] + ' - <b>Carrera:</b> ' + value['id_carrera']);
                                var btn_RechazarRpt = '';
                                var btn_AgregarRpt = '';

                                if (value['id_estatus'] == 3) { //.3 Aceptado
                                    btn_RechazarRpt = '<button style="width:100px; height:25px;" class="btnOpcion btn_RechazarRpt" data-id_ss=\'' + value['id_ss'] + '\' ' +
                                        ' data-numero_reporte_bi=' + value['numero_reporte_bi'] +
                                        ' data-id_alumno = \'' + value['id_alumno'] + '\' ' +
                                        ' data-correo_alumno = \'' + value['email_usuario'] + '\' ' +
                                        ' data-id_carrera=' + value['id_carrera'] +
                                        ' data-fecha_prog_inicio=' + value['fecha_prog_inicio'] +
                                        ' data-fecha_prog_fin=' + value['fecha_prog_fin'] +
                                        ' data-horas_obligatorias=' + value['horas_obligatorias'] +
                                        ' data-id_version=' + value['id_version'] + '>Rechazar Rpt</button>';
                                }
                                if (contador == n_registros) {
                                    btn_AgregarRpt = '<button style="width:100px; height:25px; margin-top:2px;" class="btnOpcion btn_AgregarRpt" data-id_ss=\'' + value['id_ss'] + '\' ' +
                                        ' data-numero_reporte_bi=' + value['numero_reporte_bi'] +
                                        ' data-id_alumno = \'' + value['id_alumno'] + '\' ' +
                                        ' data-correo_alumno = \'' + value['email_usuario'] + '\' ' +
                                        ' data-id_carrera=' + value['id_carrera'] +
                                        ' data-fecha_prog_inicio=' + value['fecha_prog_inicio'] +
                                        ' data-fecha_prog_fin=' + value['fecha_prog_fin'] +
                                        ' data-horas_obligatorias=' + value['horas_obligatorias'] +
                                        ' data-id_version=' + value['id_version'] + '>Agregar Rpt</button>';
                                }
                                if (value['id_estatus'] == 1 || value['id_estatus'] == 2 || value['id_estatus'] == 3) {
                                    horas_obligatorias += value['horas_obligatorias'];
                                }
                                if (value['id_estatus'] == 3) {
                                    horas_laboradas += value['horas_laboradas'];
                                }

                                html_table += '<TR>';
                                html_table += '<TD>' + value['numero_reporte_bi'] + '</TD>';
                                html_table += '<TD style="text-align:left;">' + value['id_version'] + '</TD>';
                                html_table += '<TD style="text-align:left;">' + value['fecha_prog_inicio'] + '</TD>';
                                html_table += '<TD>' + esNulo(value['fecha_prog_fin']) + '</TD>';
                                html_table += '<TD>' + value['horas_obligatorias'] + '</TD>';
                                html_table += '<TD>' + esNulo(value['fecha_real_inicio']) + '</TD>';
                                html_table += '<TD>' + esNulo(value['fecha_real_fin']) + '</TD>';
                                html_table += '<TD>' + esNulo(value['fecha_recepcion_rpt']) + '</TD>';
                                html_table += '<TD>' + value['horas_laboradas'] + '</TD>';
                                html_table += '<TD>' + value['descripcion_estatus'] + '</TD>';
                                html_table += '<TD>' + value['nota'] + '</TD>';
                                html_table += '<TD>' + btn_RechazarRpt + btn_AgregarRpt + '</TD>';
                                html_table = html_table + '</TR>';
                                contador += 1;
                            });
                            html_table += '<tfoot><TR>';
                            html_table += '<TD></TD>';
                            html_table += '<TD></TD>';
                            html_table += '<TD></TD>';
                            html_table += '<TD></TD>';
                            html_table += '<TD>' + horas_obligatorias + '</TD>';
                            html_table += '<TD></TD>';
                            html_table += '<TD></TD>';
                            html_table += '<TD></TD>';
                            html_table += '<TD>' + horas_laboradas + '</TD>';
                            html_table += '<TD></TD>';
                            html_table += '<TD></TD>';
                            html_table += '<TD></TD>';
                            html_table = html_table + '</TR></tfoot>';

                            html_table = html_table + '</TABLE>';
                            $('#tabla_Reportes').empty();
                            $('#tabla_Reportes').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD colspan="12">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Reportes').empty();
                            $('#tabla_Reportes').html(html_table);
                            $('#info_alumno').text('');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table += '<TR><TH>No.Rpt</TH>\n\
                                             <TH>Ult. Ver.</TH>\n\
                                             <TH>F. Prog. Inicio</TH>\n\
                                             <TH>F. Prog. Término</TH>\n\
                                             <TH>Hrs Prog.</TH>\n\
                                             <TH>F. Real Inicio</TH>\n\
                                             <TH>F. Real Término</TH>\n\
                                             <TH>F. de Envío</TH>\n\
                                             <TH>Hrs realizadas</TH>\n\
                                             <TH>Estatus</TH>\n\
                                             <TH>Nota</TH>\n\
                                             <TH>Acción</TH></TR>';
                        html_table = html_table + '<TR><TD colspan="12">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Reportes').empty();
                        $('#tabla_Reportes').html(html_table);
                        $('#info_alumno').text('');
                    });
            }
            //FIN DE OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO

            //CLICK AL BOTON RECHAZAR REPORTE
            $('#tabla_Reportes').on("click", "button.btn_RechazarRpt", function(e) {
                e.preventDefault();
                var id_ss = $(this).data('id_ss');
                var numero_reporte_bi = $(this).data('numero_reporte_bi');
                var id_version = $(this).data('id_version');
                var id_estatus = 4; //4.Rechazado
                var id_administrador = $('#Id_Usuario').val();
                var tipo_Mov = 'ACTUALIZAR_ESTATUS_REPORTE';
                var fecha_prog_inicio = $(this).data('fecha_prog_inicio');
                var fecha_prog_fin = $(this).data('fecha_prog_fin');
                var horas_obligatorias = $(this).data('horas_obligatorias');
                //                    var id_alumno = $(this).data('id_alumno');

                var id_Usr_Destinatario = $(this).data('id_alumno');
                var correo_usr = $(this).data('correo_alumno');
                var carrera_usr = $(this).data('id_carrera');
                var desc_documento = "REPORTE BIMESTRAL";

                var tituloAdjuntar = '<b>Rechazar el Reporte Bimestral No. ' + numero_reporte_bi + " Versión " + id_version;
                $('#titulo_Rechazar').html(tituloAdjuntar);
                $('#ventanaConfirmarRechazar_Rpt').dialog({
                    buttons: {
                        "Aceptar": function() {
                            if (!$("#nota_admin_r").val().match(miExpReg_Nota_Rechazo)) {
                                $('#ventanaAviso').html("Debe indicar el motivo para Rechazar el Reporte y SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( )' - _ #");
                                $('#ventanaAvisos').dialog('open');
                            } else {
                                $('#ventanaMensajeConfirma_R').text('Desea generar una nueva versión de este reporte para que el Alumno lo envíe nuevamente ?');
                                $('#ventanaConfirmaVoBo').dialog({
                                    buttons: {
                                        "Aceptar": function() {
                                            $(this).dialog('close');
                                            //                                                    $('#ventanaProcesando').dialog('open');
                                            var nota = $("#nota_admin_r").val();
                                            nota = nota.concat(". DE ACEPTADO A REENVIO AUTORIZADO");

                                            actualiza_Estatus_Reporte(tipo_Mov, id_ss, numero_reporte_bi, id_version,
                                                id_estatus, id_administrador, nota, fecha_prog_inicio, fecha_prog_fin, horas_obligatorias,
                                                id_Usr_Destinatario, correo_usr, carrera_usr, desc_documento);

                                            Obtener_Reportes_Bimestrales($('#id_ss_rb').val());

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
                            $("#nota_admin_r").val('');
                            $(this).dialog('close');
                        }
                    },
                    close: function() {
                        $("#nota_admin_r").val('');
                        $(this).dialog('destroy');
                    },
                    title: 'Nota para el Rechazo del Reporte',
                    modal: true,
                    autoOpen: true,
                    resizable: false,
                    draggable: true,
                    show: 'slide',
                    hide: 'slide',
                    height: 'auto',
                    width: '450',
                    dialogClass: 'no-close',
                    closeOnEscape: false
                });
            });
            //FIN CLICK AL BOTON RECHAZAR REPORTE


            function actualiza_Estatus_Reporte(tipo_Mov, id_ss, numero_reporte_bi, id_version,
                id_estatus, id_administrador, nota, fecha_prog_inicio, fecha_prog_fin, horas_obligatorias,
                id_Usr_Destinatario, correo_usr, carrera_usr, desc_documento) {
                $('#ventanaProcesando').dialog('open');
                $.ajax({
                        data: {
                            Tipo_Movimiento: tipo_Mov,
                            id_ss: id_ss,
                            numero_reporte_bi: numero_reporte_bi,
                            id_version: id_version,
                            id_estatus: id_estatus,
                            id_administrador: id_administrador,
                            nota: nota,
                            fecha_prog_inicio: fecha_prog_inicio,
                            fecha_prog_fin: fecha_prog_fin,
                            horas_obligatorias: horas_obligatorias,
                            id_Usr_Destinatario: id_Usr_Destinatario,
                            correo_usr: correo_usr,
                            carrera_usr: carrera_usr,
                            desc_documento: desc_documento,
                            id_tema_bitacora: '125' //Admon. de Reportes Bimestrales

                        },
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_Aprobar_Reporte_Bimestral.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        if (respuesta.success == true) {
                            $('#ventanaConfirmarRechazar_Rpt').dialog('close');
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

            //VALIDAMOS LOS DATOS CAPTURADOS DEL USUARIO
            function validaDatos() {
                var datosValidos = true;
                var fechaInicio = $('#fecha_Inicio_rpt').val();
                var fechaFin = $('#fecha_Termino_rpt').val();
                var horas_obligatorias = $('#horas_obligatorias').val();
                var nota = $('#nota_admin').val();

                $('#aviso_Fecha_Inicio').hide();
                $('#aviso_Fecha_Termino').hide();
                $('#aviso_horas_obligatorias').hide();

                if (fechaInicio == '') {
                    $('#aviso_Fecha_Inicio').show();
                    datosValidos = false;
                }
                if (fechaFin == '') {
                    $('#aviso_Fecha_Termino').show();
                    datosValidos = false;
                }
                if (!horas_obligatorias.match(miExpReg_Horas_Realizadas)) {
                    $('#aviso_horas_obligatorias').show();
                    datosValidos = false;
                } else {
                    $('#aviso_horas_obligatorias').hide();
                }
                if (!nota.match(miExpReg_Nota_Rechazo)) {
                    datosValidos = false;
                }
                if (fechaInicio == '' || fechaFin == '' || horas_obligatorias == '') {
                    $('#ventanaAviso').html('Debe capturar TODA la información Requerida.');
                    $('#ventanaAvisos').dialog('open');

                    datosValidos = false;
                }
                return datosValidos;
            }

            //CLICK AL BOTON AGREGAR REPORTE
            $('#tabla_Reportes').on("click", "button.btn_AgregarRpt", function(e) {
                e.preventDefault();
                var id_ss = $(this).data('id_ss');
                var numero_reporte_bi = parseInt($(this).data('numero_reporte_bi')) + 1;
                var tipo_Mov = 'AGREGAR_REPORTE';
                var id_alumno = $(this).data('id_alumno');
                var correo_usr = $(this).data('correo_alumno');

                //                    var tituloAdjuntar = 'Reporte_Bimestral_' + id_alumno +  "_" + numero_reporte_bi + "_1.pdf";
                //                    $('#nom_Archivo').val(tituloAdjuntar);
                $('#ventanaConfirmarAgregar_Rpt').dialog({
                    buttons: {
                        "Aceptar": function() {

                            if (validaDatos()) {
                                $('#ventanaMensajeConfirma').text('Desea Agregar este Nuevo Reporte ?');
                                $('#ventanaConfirmaAgregar').dialog({
                                    buttons: {
                                        "Aceptar": function() {
                                            $(this).dialog('close');
                                            //                                                            $('#ventanaProcesando').dialog('open');
                                            //                                                            $(this).dialog('close');
                                            //                                                            $('#ventanaProcesando').dialog('open');
                                            var fecha_prog_inicio = $('#fecha_Inicio_rpt').val();
                                            var fecha_prog_fin = $('#fecha_Termino_rpt').val();
                                            var horas_obligatorias = $('#horas_obligatorias').val();
                                            var nota = $('#nota_admin').val();

                                            var id_administrador = $('#Id_Usuario').val();
                                            var correo_administrador = $('#Id_Usuario_Correo').val();;

                                            agregar_Reporte(tipo_Mov, id_ss, numero_reporte_bi, nota, fecha_prog_inicio, fecha_prog_fin, horas_obligatorias,
                                                id_administrador, correo_administrador, id_alumno, correo_usr);

                                            Obtener_Reportes_Bimestrales($('#id_ss_rb').val());

                                        },
                                        "Cancelar": function() {
                                            $(this).dialog('close');
                                        }
                                    },
                                    title: 'Confirmación para Agregar el Nuevo Reporte',
                                    modal: true,
                                    autoOpen: true,
                                    resizable: true,
                                    draggable: true,
                                    dialogClass: 'no-close ventanaConfirmaUsuario',
                                    closeOnEscape: false
                                });

                            } else {
                                $('#ventanaAviso').html("Debe indicar el motivo del nuevo reporte bimestral. SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( )' - _ #");
                                $('#ventanaAvisos').dialog('open');
                            }
                        },
                        "Cancelar": function() {
                            $('#ventanaConfirmarAgregar_Rpt input').val('');
                            $("#nota_admin").val('');
                            $(this).dialog('close');
                        }
                    },
                    open: function() {
                        $('#horas_obligatorias').focus();
                    },
                    close: function() {
                        $('#ventanaConfirmarAgregar_Rpt input').val('');
                        $("#nota_admin").val('');
                        $(this).dialog('destroy');
                    },
                    title: 'Agregar un Reporte',
                    modal: true,
                    autoOpen: true,
                    resizable: false,
                    draggable: true,
                    show: 'slide',
                    hide: 'slide',
                    height: 'auto',
                    width: '450',
                    position: {
                        at: 'center top'
                    },
                    dialogClass: 'no-close',
                    closeOnEscape: false

                }); //FIN ventanaConfirmarAgregarRpt
            });
            //FIN CLICK AL BOTON AGREGAR REPORTE

            function agregar_Reporte(tipo_Mov, id_ss, numero_reporte_bi, nota, fecha_prog_inicio, fecha_prog_fin, horas_obligatorias,
                id_administrador, correo_administrador, id_alumno, correo_usr) {
                $('#ventanaProcesando').dialog('open');
                $.ajax({
                        data: {
                            Tipo_Movimiento: tipo_Mov,
                            id_ss: id_ss,
                            numero_reporte_bi: numero_reporte_bi,
                            nota: nota,
                            fecha_prog_inicio: fecha_prog_inicio,
                            fecha_prog_fin: fecha_prog_fin,
                            horas_obligatorias: horas_obligatorias,
                            id_administrador: id_administrador,
                            correo_administrador: correo_administrador,
                            id_alumno: id_alumno,
                            correo_usr: correo_usr
                        },
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_admon_rpt_bimestrales.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        if (respuesta.success == true) {
                            $('#ventanaConfirmarAgregar_Rpt').dialog('close');
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

            //Array para dar formato en español 
            $.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: 'Previo',
                nextText: 'Próximo',
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ],
                monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                    'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
                ],
                monthStatus: 'Ver otro mes',
                yearStatus: 'Ver otro año',
                dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sáb'],
                dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                dateFormat: 'dd/mm/yy',
                firstDay: 0,
                initStatus: 'Selecciona la fecha',
                isRTL: false
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);

            $('#fecha_Inicio_rpt').datepicker({
                changeYear: true,
                changeMonth: true,
                yearRange: '1920:2050',
                onSelect: function(date) {
                    $("#fecha_Inicio_rpt ~ .ui-datepicker").hide();
                }
            });
            $('#fecha_Termino_rpt').datepicker({
                changeYear: true,
                changeMonth: true,
                yearRange: '1920:2050',
                onSelect: function(date) {
                    $("#fecha_Termino_rpt ~ .ui-datepicker").hide();
                }
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

            $('#ventanaConfirmarRechazar_Rpt').hide();
            $('#ventanaConfirmarAgregar_Rpt').hide();

            /*$('.entrada_Dato').focus(function(e){
                e.preventDefault();
                f5($(document),false);
            });
            $('.entrada_Dato').blur(function(e){
                e.preventDefault();
                f5($(document),true);
            });
            

            f5($(document),true); */

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
                <p>Administración de Reportes Bimestrales</p>
            </div>
        </div>
        <div class="barra_Parametros">
            <label for="id_ss_rb" class="etiqueta_Parametro">Id Servicio Social:</label>
            <input type="text" id="id_ss_rb" name="id_ss_rb" value="" maxlength="10" placeholder="201603-001" autocomplete="off"
                class="entrada_Dato input_Parametro" title="Sólo puede Capturar los carácteres: 0-9 -">
            <button id="btn_Buscar" class="btn_Herramientas">Buscar</button>
            <label id="info_alumno" style="padding-left:20px;"></label>
        </div>
        <div>
            <div id="tabla_Reportes" class="tabla_Registros">
            </div>
        </div>
        <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
        <input type="hidden" id="Id_Usuario_Correo" name="Id_Usuario_Correo" value="<?php echo $_SESSION['correo_usuario_sesion']; ?>">
    </div>

    <div id="ventanaConfirmarRechazar_Rpt" style="margin-top:10px;">
        <p>
            <label id="titulo_Rechazar"></label>
        </p>
        <br>
        <p>
            <textarea id="nota_admin_r" class="entrada_Dato notaVoBo" maxlength="500"
                onkeyup="javascript:this.value=this.value.toUpperCase();"
                title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
        </p>
    </div>
    <div id="ventanaConfirmarAgregar_Rpt" class="" style="padding-top: 20px;">
        <div>

            <p>
                <label for="fecha_Inicio_rpt" class="label">Fecha de Inicio:</label>
                <input type="text" name="fecha_Inicio_rpt" id="fecha_Inicio_rpt" class="input_Parametro" style="width:250px;" readonly />
                <span id="aviso_Fecha_Inicio" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
            </p>
            <p>
                <label for="fecha_Termino_rpt" class="label">Fecha de Término:</label>
                <input type="text" name="fecha_Termino_rpt" id="fecha_Termino_rpt" class="input_Parametro" style="width:250px;" readonly />
                <span id="aviso_Fecha_Termino" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
            </p>
            <p>
                <label for="horas_obligatorias" class="label">Horas Programadas:</label>
                <input type="text" name="horas_obligatorias" class="entrada_Dato input_Parametro" id="horas_obligatorias"
                    maxlength="3" placeholder="160" style="width:250px;"
                    title="Capture únicamente números enteros" class="entrada_Dato" autocomplete="off" />
                <span id="aviso_horas_obligatorias" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
            </p>
            <br>
            <p style="padding-left:15px;">
                <label for="nota_admin" class="etiqueta_Parametro">Nota:</label><br>
                <textarea id="nota_admin" class="entrada_Dato notaVoBo" style="width:360px;" maxlength="500" onkeyup="javascript:this.value=this.value.toUpperCase();"
                    title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
            </p>
        </div>
    </div>
    <div id="ventanaConfirmaVoBo">
        <span id="ventanaMensajeConfirma_R"></span>
    </div>
    <div id="ventanaConfirmaAgregar">
        <span id="ventanaMensajeConfirma"></span>
    </div>

    <div id="ventanaAvisos">
        <span id="ventanaAviso"></span>
    </div>
    <div id="ventanaProcesando" data-role="header">
        <img id="cargador" src="./assets/images/ui/engrane2.gif" /><br>
        Procesando su transacción....!<br>
        Espere por favor.
    </div>
    <!--    </body>
</html>-->
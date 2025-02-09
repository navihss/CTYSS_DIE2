<!DOCTYPE html>
<!--
Fecha:          Junio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para adjuntar los Reportes Bimestrales
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
    <script src="./assets/js/ruta_documentos.js"></script>

    <script>
        $(document).ready(function() {
            function Puede_Enviar_Rpt(fecha_termino_rpt) {
                var datos = {
                    fecha_termino_rpt: fecha_termino_rpt,
                    Tipo_Movimiento: "PUEDE_ENVIAR_RPT"
                };
                var result = false;
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        async: false,
                        url: "_Negocio/n_Alumno_Mis_Reportes.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        if (respuesta.data.message) {
                            result = "true";
                        } else {
                            result = "false";
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisos').dialog('open');
                    });
                return result;
            } //COMPARA 2 FECHAS

            //OBTENEMOS LAS CARRERAS EN LAS QUE ESTA INSCRITO EL ALUMNO PARA EL LISTBOX
            //EL KEY SERA EL ID DEL SERVICIO SOCIAL PARA PODER OBTENER LOS REPORTES BIMESTRALES DE LA CARRERA SELECCIONADA                 
            function Obtener_Carreras_Del_Alumno(id_alumno) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_CARRERAS_x_SS',
                    id_alumno: id_alumno
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_Alumno_Mis_Reportes.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_options = '';
                        if (respuesta.success == true) {
                            html_options = html_options + '<option value=-1>Seleccione su Servicio</option>';

                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                //recorremos los valores de cada columna del registro
                                html_options = html_options + '<option data-id_carrera = \'' +
                                    value['id_carrera'] + '\' value=' + value['id_ss'] +
                                    '>' + value['id_ss'] + '</option>';
                            });

                            $('#selec_Mis_Carreras').empty();
                            $('#selec_Mis_Carreras').html(html_options);

                            $('#selec_Mis_Carreras option:first-child').attr('selected', 'selected');
                            Obtener_Mis_Reportes_Bimestrales($('#selec_Mis_Carreras').val());
                        } else {
                            $('#ventanaAviso').html(respuesta.data.message);
                            $('#ventanaAvisos').dialog('open');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisos').dialog('open');
                    });

            } //FIN OBTENEMOS LAS CARRERAS-SERVICIO_SOCIAL EN LAS QUE ESTA INSCRITO EL ALUMNO  


            //CAMBIO LA SELECCION EN EL LISTBOX
            $('#selec_Mis_Carreras').change(function(e) {
                e.preventDefault();
                $('#id_carrera_doc').val($('#selec_Mis_Carreras option:selected').data('id_carrera'));
                Obtener_Mis_Reportes_Bimestrales($('#selec_Mis_Carreras').val());
            });

            //OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
            function Obtener_Mis_Reportes_Bimestrales(id_ss) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_MIS_REPORTES_BIMESTRALES',
                    id_ss: id_ss
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        async: false,
                        url: "_Negocio/n_Alumno_Mis_Reportes.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:100%;">';
                        html_table += '<TR><TH>No.<br>Rpt</TH>\n\
                                        <TH>Ver.</TH>\n\
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
                            var horas_obligatorias = 0;
                            var horas_laboradas = 0;
                            var ocultar_btnEnviar_rpt = "";
                            $.each(respuesta.data.registros, function(key, value) {
                                $btn_EnviarRpt = '';
                                //                                   ocultar_btnEnviar_rpt = "hidden";
                                var fechaHoy_mayor = Puede_Enviar_Rpt(value['fecha_prog_fin']);

                                if (fechaHoy_mayor == "true") {
                                    ocultar_btnEnviar_rpt = "";
                                }
                                if (value['id_estatus'] == 1) {
                                    $btn_EnviarRpt = '<button class="btn_EnviarRpt btnOpcion" data-id_ss=\'' + value['id_ss'] + '\' ' +
                                        ' data-numero_reporte_bi=' + value['numero_reporte_bi'] +
                                        ' data-fecha_prog_inicio=' + value['fecha_prog_inicio'] +
                                        ' data-fecha_prog_fin=' + value['fecha_prog_fin'] +
                                        ' data-ultimo_reporte = \'' + value['ultimo_reporte'] + '\' ' +
                                        ' data-id_version=' + value['id_version'] + ' ' + ocultar_btnEnviar_rpt + '>Enviar</button>';
                                }

                                if (value['id_estatus'] == 1 || value['id_estatus'] == 2 || value['id_estatus'] == 3) {
                                    horas_obligatorias += value['horas_obligatorias'];
                                }
                                if (value['id_estatus'] == 3) {
                                    horas_laboradas += value['horas_laboradas'];
                                }
                                nom_file = value['id_alumno'] + '_' +
                                    value['id_carrera'] + '_' +
                                    value['id_ss'] + '_' +
                                    value['numero_reporte_bi'] + '_' +
                                    value['id_version'] + '_Reporte_Bimestral.pdf';
                                fecha_ = new Date();
                                ruta_doc = ruta_docs_reportes_bimestrales + nom_file + '?' + fecha_;

                                html_table += '<TR>';
                                if (value['id_estatus'] == 2 || value['id_estatus'] == 3) {
                                    html_table += '<TD><a class="link_pdf" style="padding:5px; font-weight: bold; \n\
                                                background-color: #bdb76b; text-decoration:underline;" target="_blank" href="' +
                                        ruta_doc + '">' + value['numero_reporte_bi'] + '</a></TD>';
                                } else {
                                    html_table += '<TD>' + value['numero_reporte_bi'] + '</TD>';
                                }
                                html_table += '<TD style="text-align:left;">' + value['id_version'] + '</TD>';
                                html_table += '<TD style="text-align:left;">' + value['fecha_prog_inicio'] + '</TD>';
                                html_table += '<TD>' + esNulo(value['fecha_prog_fin']) + '</TD>';
                                html_table += '<TD>' + value['horas_obligatorias'] + '</TD>';
                                html_table += '<TD>' + esNulo(value['fecha_real_inicio']) + '</TD>';
                                html_table += '<TD>' + esNulo(value['fecha_real_fin']) + '</TD>';
                                html_table += '<TD>' + esNulo(value['fecha_recepcion_rpt']) + '</TD>';
                                html_table += '<TD>' + value['horas_laboradas'] + '</TD>';
                                html_table += '<TD>' + value['descripcion_estatus'] + '</TD>';
                                html_table += '<TD style="text-align:left;">' + esNulo(value['nota']) + '</TD>';
                                html_table += '<TD>' + $btn_EnviarRpt + '</TD>';
                                html_table = html_table + '</TR>';
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
                            $('#tabla_Mis_Reportes').empty();
                            $('#tabla_Mis_Reportes').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD colspan="12">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Mis_Reportes').empty();
                            $('#tabla_Mis_Reportes').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table += '<TR><TH>No.Rpt</TH>\n\
                                             <TH>Versión</TH>\n\
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
                        $('#tabla_Mis_Reportes').empty();
                        $('#tabla_Mis_Reportes').html(html_table);
                    });
            }
            //FIN DE OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO

            //EVENTO CLICK SOBRE EL BOTON ADJUNTAR REPORTE
            $('#tabla_Mis_Reportes').on("click", "button.btn_EnviarRpt", function(e) {
                e.preventDefault();
                $('#id_ss').attr("value", $(this).data('id_ss'));
                $('#id_ss_salida').attr("value", $(this).data('id_ss'));
                $('#fecha_prog_inicio').attr("value", $(this).data('fecha_prog_inicio'));
                $('#fecha_prog_fin').attr("value", $(this).data('fecha_prog_fin'));
                var anio_t = $('#fecha_prog_fin').val().substr(0, 4);
                var mes_t = $('#fecha_prog_fin').val().substr(5, 2);
                var dia_t = $('#fecha_prog_fin').val().substr(8, 2);
                var f_fin = dia_t + '/' + mes_t + '/' + anio_t;
                var anio_i = $('#fecha_prog_inicio').val().substr(0, 4);
                var mes_i = $('#fecha_prog_inicio').val().substr(5, 2);
                var dia_i = $('#fecha_prog_inicio').val().substr(8, 2);
                var f_inicio = dia_i + '/' + mes_i + '/' + anio_i;
                $('#fecha_Inicio_rpt').val(f_inicio);
                $('#fecha_Termino_rpt').val(f_fin);
                $('#numero_reporte_bi').attr("value", $(this).data('numero_reporte_bi'));
                $('#ultimo_reporte').attr("value", $(this).data('ultimo_reporte'));
                $('#id_version').attr("value", $(this).data('id_version'));
                $('#id_usuario_doc').attr("value", $('#Id_Usuario').val());
                $('#desc_corta_doc').attr("value", 'Reporte_Bimestral');
                $('#id_estatus').attr("value", 2); //2. Por Autorizar

                $("#fecha_Termino_rpt").datepicker('disable');
                if ($('#numero_reporte_bi').val() == $('#ultimo_reporte').val()) {
                    $("#fecha_Termino_rpt").datepicker('enable');
                }

                var tituloAdjuntar = 'Para el <b>Reporte Bimestral ' + $('#numero_reporte_bi').val() +
                    ', Versión ' + $('#id_version').val() +
                    '</b>. Seleccione su archivo en formato PDF. <b>(Tamaño máximo 500MB)</b>';
                $('#lblTitulo_SubirArchivo').html(tituloAdjuntar);

                $('#ventanaSubirArchivo_Rpt').dialog({
                    title: 'Adjuntar Reporte',
                    modal: true,
                    autoOpen: true,
                    resizable: true,
                    draggable: true,
                    width: '600',
                    height: 'auto',
                    dialogClass: 'no-close',
                    closeOnEscape: false,
                    position: {
                        at: 'center top'
                    }
                });
            });
            //FIN EVENTO CLICK SOBRE EL BOTON ADJUNTAR REPORTE

            $('#cancelar').click(function(e) {
                e.preventDefault();
                Obtener_Mis_Reportes_Bimestrales($('#id_ss').val());

                $('#ventanaSubirArchivo_Rpt input[type=file], input[type=text]').each(function() {
                    $(this).val('');
                });
                $('#ventanaSubirArchivo_Rpt span').each(function() {
                    $(this).hide();
                });
                $('#message').empty();
                $('#ventanaSubirArchivo_Rpt').dialog('close');
            });

            //ADJUNTAR ARCHIVO 
            $('#loading').hide();
            $('#file_rpt').on('change', function(e) {
                e.preventDefault();

                var archivo_selec = $('#file_rpt')[0].files[0];
                var archivo_nombre = archivo_selec.name;

                var archivo_extension = archivo_nombre.substring(archivo_nombre.lastIndexOf('.') + 1);
                var archivo_tamano = archivo_selec.size;
                var archivo_tipo = archivo_selec.type;

                var info_Archivo_Selec = '';
                info_Archivo_Selec += "<table class='tabla_Registros'><tr><th colspan='2'><span>Información del Archivo seleccionado:</span></th></tr>";
                info_Archivo_Selec += "<tr><td><b>Nombre del archivo:</b></td><td>" + archivo_nombre + "</td></tr>";
                info_Archivo_Selec += "<tr><td><b>Extensión:</b></td><td>" + archivo_extension + "</td></tr>";
                info_Archivo_Selec += "<tr><td><b>Tipo:</b></td><td>" + archivo_tipo + "</td></tr>";
                info_Archivo_Selec += "<tr><td><b>Tamaño:</b></td><td>" + (archivo_tamano / 1048576) + " kB</td></tr></table>";

                $('#message').html(info_Archivo_Selec);
                $('#loading').hide();

                if (parseInt(archivo_tamano) > 500000000) {
                    $('#ventanaAviso').html('El Tamaño del Archivo excede los 500 MB permitidos.');
                    $('#ventanaAvisos').dialog('open');
                    $('#file_rpt').val('');
                }

            });

            //ENVIAMOS EL ARCHIVO
            $('#frmSubirPDF').on('submit', function(e) {
                e.preventDefault();
                if (!validaDatos()) {
                    return false;
                }
                $('#ventanaProcesando').dialog('open');
                $('#loading').html('<h1>Loading...</h1>');
                $('#loading').show();
                $("#fecha_Termino_rpt").datepicker('enable');
                $.ajax({
                    url: "uploadFile_3_procesa.php",
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        $('#loading').html(data);
                        $('#file_rpt').val('');
                        $("#fecha_Termino_rpt").datepicker('disable');
                        $('#ventanaProcesando').dialog('close');
                    }
                });
            });
            //FIN ENVIO DE ARCHIVO

            //VALIDAMOS LOS DATOS CAPTURADOS DEL USUARIO
            function validaDatos() {
                var datosValidos = true;
                var fechaInicio = $('#fecha_Inicio_rpt').val();
                var fechaFin = $('#fecha_Termino_rpt').val();
                var horas_realizadas = $('#horas_realizadas').val();

                $('#aviso_Fecha_Inicio').hide();
                $('#aviso_Fecha_Termino').hide();
                $('#aviso_horas_realizadas').hide();

                if (fechaInicio == '') {
                    $('#aviso_Fecha_Inicio').show();
                    datosValidos = false;
                }
                if (fechaFin == '') {
                    $('#aviso_Fecha_Termino').show();
                    datosValidos = false;
                }
                if (!horas_realizadas.match(miExpReg_Horas_Realizadas)) {
                    $('#aviso_horas_realizadas').show();
                    datosValidos = false;
                } else {
                    $('#aviso_horas_realizadas').hide();
                }

                if (fechaInicio == '' || fechaFin == '' || horas_realizadas == '') {
                    $('#ventanaAviso').html('Debe capturar TODA la información Requerida.');
                    $('#ventanaAvisos').dialog('open');

                    datosValidos = false;
                }
                return datosValidos;
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

            //                $('#fecha_Inicio_rpt').datepicker({
            //                    changeYear : true,
            //                    changeMonth : true,
            //                    yearRange : '1920:2050',
            //                    onSelect : function(date){
            //                        $("#fecha_Inicio_rpt ~ .ui-datepicker").hide();
            //                    }
            //                });
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

            /*$('.entrada_Dato').focus(function(e){
                e.preventDefault();
                f5($(document),false);
            });
            $('.entrada_Dato').blur(function(e){
                e.preventDefault();
                f5($(document),true);
            });*/

            Obtener_Carreras_Del_Alumno($('#Id_Usuario').val());
            //f5($(document),true);
            $('#ventanaSubirArchivo_Rpt').hide();
            $('#loading').hide();
            $('#horas_realizadas').focus();
        });
    </script>

    <!--    </head>
    <body>
        <header>
            Mi Pefil
        </header>-->
    <div>
        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                    <p>Mis Reportes Bimestrales</p>
                </div>
            </div>
            <div class="barra_Parametros">
                <div id="lst_Mis_Carreras">
                    <label for="selec_Mis_Carreras" class="etiqueta_Parametro">Mi Servicio Social:</label>
                    <select id="selec_Mis_Carreras" name="selec_Mis_Carreras" class="combo_Parametro">
                    </select>
                </div>
            </div>
        </div>
        <div id="tabla_Mis_Reportes" class="tabla_Registros">
        </div>
        <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
    </div>

    <div id="ventanaSubirArchivo_Rpt" class="contenido_Formulario" style="width:400px; padding-top: 20px;">
        <div id="contenido_Subir_Archivo">
            <form action="" method="post" enctype="multipart/form-data" id="frmSubirPDF" name="frmSubirPDF">
                <p>
                    <label for="fecha_Inicio_rpt" class="label">Fecha de Inicio:</label>
                    <input type="text" name="fecha_Inicio_rpt" id="fecha_Inicio_rpt" readonly />
                    <span id="aviso_Fecha_Inicio" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                </p>
                <p>
                    <label for="fecha_Termino_rpt" class="label">Fecha de Término:</label>
                    <input type="text" name="fecha_Termino_rpt" id="fecha_Termino_rpt" readonly />
                    <span id="aviso_Fecha_Termino" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                </p>
                <p>
                    <label for="horas_realizadas" class="label">Horas realizadas en bimestre:</label>
                    <input type="text" name="horas_realizadas" class="entrada_Dato" id="horas_realizadas"
                        maxlength="3" placeholder="160"
                        title="Capture únicamente números enteros" class="entrada_Dato" autocomplete="off" />
                    <span id="aviso_horas_realizadas" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                </p>
                <div style="padding-top: 10px;">
                    <p>
                        <label id='lblTitulo_SubirArchivo' for="file_rpt"></label>
                    <div style="display: inline-block; padding-top: 10px;">
                        <input type="file" name="file_rpt" id="file_rpt" accept=".pdf" required class="tag_file">
                    </div>
                    <div style="text-align : center; padding-top: 25px;">
                        <input type="submit" name="enviarArchivo" id="enviarArchivo" value="Enviar" class="btn_Herramientas" style="width: 150px;">
                        <input type="button" name="cancelar" id="cancelar" value="Cerrar" class="btn_Herramientas" style="width: 150px;">

                        <input type="hidden" id="id_ss" name="id_ss" value="">
                        <input type="hidden" id="fecha_prog_inicio" name="fecha_prog_inicio" value="">
                        <input type="hidden" id="fecha_prog_fin" name="fecha_prog_fin" value="">
                        <input type="hidden" id="numero_reporte_bi" name="numero_reporte_bi" value="">
                        <input type="hidden" id="id_version" name="id_version" value="">
                        <input type="hidden" id="id_usuario_doc" name="id_usuario_doc" value="">
                        <input type="hidden" id="desc_corta_doc" name="desc_corta_doc" value="">
                        <input type="hidden" id="id_estatus" name="id_estatus" value="">
                        <input type="hidden" id="id_carrera_doc" name="id_carrera_doc" value="">

                        <input type="hidden" id="ultimo_reporte" name="ultimo_reporte" value="">
                    </div>
                    </p>
                </div>
            </form>

            <div id='loading' class="resultado_Carga_De_Archivo">
                <h1>Cargando el Archivo...</h1>
            </div>

            <div id="message" class="informacion_Archivo_A_Cargar"></div>

        </div>
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
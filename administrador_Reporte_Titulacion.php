<!DOCTYPE html>
<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-cache");
header("Pragma: nocache");
session_start();
if (!isset($_SESSION["id_tipo_usuario"]) and !isset($_SESSION["id_usuario"])) {
    header('Location: index.php');
}
?>
<html>

<head>
    <link href="css/bitacora.css" rel="stylesheet">
    <script src="js/expresiones_reg.js"></script>

    <script>
        $(document).ready(function() {

            $('#btn_Asistente').on('click', function(e) {
                e.preventDefault();
                $('#ventanaAsistente').dialog({
                    buttons: {
                        "Buscar": function() {

                            var checkPropuestas = $('#chk_Propuesta').prop('checked');
                            var checkCeremonias = $('#chk_Ceremonia').prop('checked');
                            var fechaInicial = $('#fecha_inicio').val().match(miExpReg_Fecha);
                            var fechaTermino = $('#fecha_termino').val().match(miExpReg_Fecha);

                            var checkElectrica = $('#chk_Electrica').prop('checked');
                            var checkComputacion = $('#chk_Computacion').prop('checked');
                            var checkTelecomunicaciones = $('#chk_Telecomunicaciones').prop('checked');

                            //  Tipo de movimiento a realizar.
                            $('#Tipo_Movimiento').val('GENERAR_REPORTE');

                            if (!checkPropuestas && !checkCeremonias) {
                                $('#ventanaAviso').html('Selecciona por lo menos un valor para el tipo.');
                                $('#ventanaAvisos').dialog('open');
                                return;
                            }

                            if (!checkComputacion && !checkElectrica && !checkTelecomunicaciones) {
                                $('#ventanaAviso').html('Selecciona por lo menos una carrera para el filtro.');
                                $('#ventanaAvisos').dialog('open');
                                return
                            }

                            if (!fechaInicial || !fechaTermino) {
                                $('#ventanaAviso').html('Las fechas de búsqueda no deben quedar vacías.');
                                $('#ventanaAvisos').dialog('open');
                                return;
                            }

                            obtenerReporte();
                        },
                        "Cerrar": function() {
                            $(this).dialog('close');
                        }
                    },
                    title: "Asistente de filtros.",
                    modal: true,
                    autoOpen: true,
                    resizable: true,
                    draggable: true,
                    width: '650',
                    height: '430',
                    closeOnEscape: false,
                    position: {
                        at: 'center top'
                    }
                });
            });

            function obtenerReporte() {

                var datos = $('#frmAsistente').serialize();
                //console.log(datos);

                $.ajax({
                    data: datos,
                    type: "POST",
                    dataType: "json",
                    url: "_Negocio/n_Usuario_Bitacora.php"
                }).done(function(respuesta, textStatus, jqXHR) {
                    console.log(respuesta);

                    var html_table = '<TABLE>';
                    html_table += '<THEAD><TR><TH style="width:10%;">No. registro</TH>\n\
        <TH style="width:10%;">No. cuenta</TH>\n\
        <TH style="width:10%;">Nombre</TH>\n\
        <TH style="width:10%;">Ap. paterno</TH>\n\
        <TH style="width:10%;">Ap. materno</TH>\n\
        <TH style="width:10%;">Nombre completo</TH>\n\
        <TH style="width:10%;">Tel. fijo</TH>\n\
        <TH style="width:10%;">Tel. celular</TH>\n\
        <TH style="width:10%;">Correo</TH>\n\
        <TH style="width:10%;">Clave carrera</TH>\n\
        <TH style="width:10%;">Carrera</TH>\n\
        <TH style="width:10%;">Motivo</TH>\n\
        <TH style="width:10%;">Fecha estimada titulación</TH>\n\
        <TH >Tipo</TH></TR></THEAD>';

                    //  Si hay registros:
                    if (respuesta.success) {

                        $.each(respuesta.data.registros, function(key, value) {
                            html_table += '<TR>';
                            html_table += '<TD style="width:10%;">' + value['no_reg'] + '</TD>';
                            html_table += '<TD style="width:10%;">' + value['no_cuenta'] + '</TD>';
                            html_table += '<TD style="width:10%;">' + value['nombre'] + '</TD>';
                            html_table += '<TD style="width:10%;">' + value['ap_pat'] + '</TD>';
                            html_table += '<TD style="width:10%;">' + value['ap_mat'] + '</TD>';
                            html_table += '<TD style="width:10%;">' + value['nombre_completo'] + '</TD>';
                            html_table += '<TD style="width:10%;">' + value['tel_fijo'] + '</TD>';
                            html_table += '<TD style="width:10%;">' + value['tel_cel'] + '</TD>';
                            html_table += '<TD style="width:10%;">' + value['correo'] + '</TD>';
                            html_table += '<TD style="width:10%;">' + value['clave_carrera'] + '</TD>';
                            html_table += '<TD style="width:10%;">' + value['carrera'] + '</TD>';
                            html_table += '<TD style="width:10%;">' + value['motivo'] + '</TD>';
                            html_table += '<TD style="width:10%;">' + value['fecha_estimada'] + '</TD>';
                            html_table += '<TD style="text-align:left;">' + value['tipo'] + '</TD>';
                            html_table += '</TR>';
                        });


                        //$('#ventanaAviso').html(respuesta.data.mm);
                        //$('#ventanaAvisos').dialog('open'); 


                    } else {
                        html_table = html_table + '<TR><TD colspan="14" style="text-align: center;"><b>' + respuesta.data.message + '</b></TD></TR>';
                    }

                    html_table = html_table + '</TABLE>';
                    $('#tabla_Mi_Bitacora').empty();
                    $('#tabla_Mi_Bitacora').html(html_table);

                    $('#ventanaAsistente').dialog('close');

                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $('#ventanaAviso').html('Ocurrió un error.');
                    $('#ventanaAvisos').dialog('open');
                });
            }

            //  Ventanas auxiliares
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
            //  ./Fin de ventanas auxiliares


            //  Formato para las fechas.
            $.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: 'Anterior',
                nextText: 'Siguiente',
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
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
            $('#fecha_termino').datepicker({
                changeYear: true,
                changeMonth: true,
                yearRange: '1920:2050',
                onSelect: function(date) {
                    $("#fecha_termino ~ .ui-datepicker").hide();
                }
            });
            $('#fecha_inicio').datepicker({
                changeYear: true,
                changeMonth: true,
                yearRange: '1920:2050',
                onSelect: function(date) {
                    $("#fecha_inicio ~ .ui-datepicker").hide();
                }
            });
            $('#limpia_FechaInicio').click(function(e) {
                e.preventDefault();
                $('#fecha_inicio').val('');
            });
            $('#limpia_FechaTermino').click(function(e) {
                e.preventDefault();
                $('#fecha_termino').val('');
            });
            //  ./Fin de formato para las fechas.


            //  Funciones auxiliares
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
            });  
            f5($(document),true); */
            //  ./Fin de funciones auxiliares.

            //  Colocar valores por defecto a las fechas.
            var fecha_Completa = new Date();
            var mes_Dos_Digitos = fecha_Completa.getMonth() + 1;
            mes_Dos_Digitos = mes_Dos_Digitos < 10 ? '0' + mes_Dos_Digitos : '' + mes_Dos_Digitos;
            var dia_Dos_Digitos = fecha_Completa.getDate();
            dia_Dos_Digitos = dia_Dos_Digitos < 10 ? '0' + dia_Dos_Digitos : '' + dia_Dos_Digitos;
            var fechaActual = dia_Dos_Digitos + "/" + mes_Dos_Digitos + "/" + fecha_Completa.getFullYear();
            $('#fecha_termino').val(fechaActual);
            $('#fecha_inicio').val(fechaActual);
            //  ./Colocar valores por defecto a las fechas.


            $('#ventanaAsistente').hide();
            $('#btn_Asistente').click();

        });
    </script>

    <div>
        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                    <p>Reporte. Alumnos de Titulación en proceso.</p>
                </div>
                <div class="barra_Herramientas">
                    <input type="submit" id="btn_Asistente" name="btn_Asistente" value="Buscar" class="btn_Herramientas" />
                </div>
            </div>
            <div>
                <div>
                    <div id="tabla_Mi_Bitacora" class="tabla_Registros">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="ventanaAsistente">
        <div>
            <form action="" method="post" id="frmAsistente" name="frmAsistente">
                <div class="caja_1_2">
                    <p>
                    <fieldset>
                        <legend>Tipo:</legend>
                        <div id="lst_Tipos">
                            <input type="checkbox" name="chk_Propuesta" value="1" checked id="chk_Propuesta"><span> Propuesta</span><br>
                            <input type="checkbox" name="chk_Ceremonia" value="1" checked id="chk_Ceremonia"><span> Ceremonia</span><br>
                        </div>
                    </fieldset>
                    </p>
                </div>
                <div class="caja_1_2">
                    <p>
                    <fieldset>
                        <legend>Carreras:</legend>
                        <div id="lst_Carreras">
                            <input type="checkbox" name="chk_Electrica" value="109" checked id="chk_Electrica"><span> Eléctrica</span><br>
                            <input type="checkbox" name="chk_Computacion" value="110" checked id="chk_Computacion"><span> Computación</span><br>
                            <input type="checkbox" name="chk_Telecomunicaciones" value="111" checked id="chk_Telecomunicaciones"><span> Telecomunicaciones</span><br>
                        </div>
                    </fieldset>
                    </p>
                </div>
                <div class="caja_3_4">
                    <p>
                    <fieldset>
                        <legend>Periodo:</legend>
                        <p>
                            Desde el día:<br>
                            <input type="text" name="fecha_inicio" readonly
                                id="fecha_inicio" />
                            <input type="button" name="limpia_FechaInicio"
                                id="limpia_FechaInicio"
                                title="Borrar la Fecha" value='X' />
                        </p>
                        <p>
                            <br>Hasta el día:<br>
                            <input type="text" name="fecha_termino" readonly
                                id="fecha_termino" />
                            <input type="button" name="limpia_FechaTermino"
                                id="limpia_FechaTermino"
                                title="Borrar la Fecha" value="X" />
                        </p>
                    </fieldset>
                    </p>
                </div>
                <!-- PASAR EL TIPO DE CONSULTA A REALIZAR -->
                <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="0">
                <input type="hidden" id="tipo_usuario" name="tipo_usuario" value="admin">
            </form>
        </div>
    </div>

    <div id="ventanaAvisos">
        <span id="ventanaAviso"></span>
    </div>

    <div id="ventanaProcesando" data-role="header">
        <img id="cargador" src="css/images/engrane2.gif" /><br>
        Procesando su transacción...<br>
        Espere por favor.
    </div>
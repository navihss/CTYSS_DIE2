<!DOCTYPE html>
<!--
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para consultar la Bitácora
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
    <link href="./assets/css/bitacora.css" rel="stylesheet">

    <script>
        $(document).ready(function() {


            function Obtener_Temas(id_tipo_usuario) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_TEMAS',
                    tipo_usuario: id_tipo_usuario
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_Usuario_Bitacora.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_options = '';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                //recorremos los valores de cada columna del registro
                                html_options += '<INPUT type="checkbox" name="chk_Tema[]" value=' + value['id_tema_bitacora'] + '>' +
                                    value['descripcion_tema_bitacora'] + '<br>';
                            });

                            $('#lst_Temas').empty();
                            $('#lst_Temas').html(html_options);

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

            } //FIN OBTENER TEMAS

            //Llenamos los catálogos
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
                        async: false,
                        url: '_Negocio/n_administrador_Crear_Nueva_Cuenta.php'
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_options = '';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                //recorremos los valores de cada usuario
                                html_options += '<INPUT type="checkbox" name="chk_Tipos_Evento[]" value=' + value['id'] + '>' +
                                    value['descripcion'] + '<br>';
                            });
                            $('#' + nom_control).empty();
                            $('#' + nom_control).html(html_options);
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

            $('#btn_Asistente').on('click', function(e) {
                e.preventDefault();
                $('#ventanaAsistente').dialog({
                    buttons: {
                        "Buscar": function() {
                            Obtener_Bitacora();
                        },
                        "Cerrar": function() {
                            $(this).dialog('close');
                        }
                    },
                    title: "Asistente de Filtros para Consultar la Bitácora",
                    modal: true,
                    autoOpen: true,
                    resizable: true,
                    draggable: true,
                    width: '800',
                    closeOnEscape: false,
                    position: {
                        at: 'center top'
                    }
                });

            });

            //OBTENEMOS LA BITACORA
            function Obtener_Bitacora() {
                $('#Tipo_Movimiento').val('OBTENER_BITACORA');
                var datos = $('#frmAsistente').serialize();
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_Usuario_Bitacora.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE>';
                        html_table += '<THEAD><TR><TH style="width:10%;">Tema</TH>\n\
                                        <TH style="width:10%;">Tipo Evento</TH>\n\
                                        <TH style="width:10%;">Día</TH>\n\
                                        <TH style="width:10%;">Usuario</TH>\n\
                                        <TH style="width:10%;">Destinatario</TH>\n\
                                        <TH >Descripción del Evento</TH></TR></THEAD>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                html_table += '<TR>';
                                html_table += '<TD style="width:10%;">' + value['descripcion_tema_bitacora'] + '</TD>';
                                html_table += '<TD style="width:10%;">' + value['descripcion_tipo_evento'] + '</TD>';
                                html_table += '<TD style="width:10%;">' + value['fecha_evento'] + '</TD>';
                                html_table += '<TD style="width:10%;">' + value['id_usuario_genera'] + '</TD>';
                                html_table += '<TD style="width:10%;">' + esNulo(value['id_usuario_destinatario']) + '</TD>';
                                html_table += '<TD style="text-align:left;">' + esNulo(value['descripcion_evento']) + '</TD>';
                                html_table += '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Mi_Bitacora').empty();
                            $('#tabla_Mi_Bitacora').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD colspan="6">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Mi_Bitacora').empty();
                            $('#tabla_Mi_Bitacora').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table += '<TR><TH>Tema</TH>\n\
                                             <TH>Tipo Evento</TH>\n\
                                             <TH>Día</TH>\n\
                                             <TH>Usuario</TH>\n\
                                             <TH>Destinatario</TH>\n\
                                             <TH>Descripción del Evento</TH></TR>';
                        html_table = html_table + '<TR><TD colspan="6">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Mi_Bitacora').empty();
                        $('#tabla_Mi_Bitacora').html(html_table);
                    });
            }
            //FIN DE OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO


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
            /*
            $('.entrada_Dato').focus(function(e){
                e.preventDefault();
                f5($(document),false);
            });
            $('.entrada_Dato').blur(function(e){
                e.preventDefault();
                f5($(document),true);
            }); */

            Obtener_Temas($('#Id_Tipo_User').val());
            llena_Catalogo('lst_Tipos_Evento', 'CATALOGO_GENERALES', 'tipo_evento',
                'id_tipo_evento as id, descripcion_tipo_evento as descripcion',
                '', 'id_tipo_evento');

            //f5($(document),true);

            var fecha_Completa = new Date()
            //Thu May 19 2011 17:25:38 GMT+1000 {}
            var mes_Dos_Digitos = fecha_Completa.getMonth() + 1;
            mes_Dos_Digitos = mes_Dos_Digitos < 10 ? '0' + mes_Dos_Digitos : '' + mes_Dos_Digitos;
            var dia_Dos_Digitos = fecha_Completa.getDate();
            dia_Dos_Digitos = dia_Dos_Digitos < 10 ? '0' + dia_Dos_Digitos : '' + dia_Dos_Digitos;
            var fechaActual = dia_Dos_Digitos + "/" + mes_Dos_Digitos + "/" + fecha_Completa.getFullYear();

            $('#fecha_termino').val(fechaActual);
            $('#fecha_inicio').val(fechaActual);

            $('#ventanaAsistente').hide();
            $('#id_usuario_genera').val($('#Id_Usuario').val());
            $('#id_usuario_destinatario').val($('#Id_Usuario').val());

            if ($('#Id_Tipo_User').val() != '1') {
                $('#id_usuario_genera').prop('readonly', 'on');
                $('#id_usuario_destinatario').prop('readonly', 'on');
            }

            $('#btn_Asistente').click();
        });
    </script>

    <!--    </head>
    <body>
        <header>
        </header>-->
    <div>
        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                    <p>Mi Bitácora</p>
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
                        <legend>Tema:</legend>
                        <div id="lst_Temas">
                        </div>
                    </fieldset>
                    </p>
                </div>
                <div class="caja_1_2">
                    <p>
                    <fieldset>
                        <legend>Tipo de Movimiento:</legend>
                        <div id="lst_Tipos_Evento"></div>
                    </fieldset>
                    </p>
                </div>
                <div class="caja_3_4">
                    <p>
                    <fieldset>
                        <legend>Usuario:</legend>
                        <p>
                            Usuario:<br>
                            <input type="text" name="id_usuario_genera"
                                id="id_usuario_genera" />
                        </p>
                        <p>
                            Usuario Destinatario:<br>
                            <input type="text" name="id_usuario_destinatario"
                                id="id_usuario_destinatario" />
                        </p>
                    </fieldset>
                    </p>
                </div>
                <div class="caja_3_4">
                    <p>
                    <fieldset>
                        <legend>Período:</legend>
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

                <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
                <input type="hidden" id="Id_Tipo_User" name="Id_Tipo_User" value="<?php echo $_SESSION['id_tipo_usuario']; ?>">
                <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="0">

                <!--                    <div style="text-align : center; padding-top: 35px;">
                            <input type="submit" name="btn_Buscar" id="btn_Buscar" value="Buscar" class="btn" style="width: 150px;">
                            <input type="button" name="btn_cerrar" id="btn_cerrar" value="Cerrar" class="btn" style="width: 150px;">

                    </div>   -->
            </form>

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
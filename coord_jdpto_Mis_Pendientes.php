<!DOCTYPE html>
<!--
Fecha:          Noviembre,2017
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para los Pendientes del Coordinador y Jefe de Dpto
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

            var id_usuario = ('<?php echo $_SESSION["id_usuario"] ?>');
            var id_tipousuario = ('<?php echo $_SESSION["id_tipo_usuario"] ?>');

            function Obtener_CeremoniasJurados_Por_Autorizar(id_usuario) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_CEREMONIAS_JURADO_PENDIENTES',
                    id_usuario: id_usuario
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_Usuario_Pendientes.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:50%;">';
                        html_table += '<TR><TH>Pendientes</TH>\n\
                                        <TH>Cantidad</TH>\n\
                                        <TH>Acción</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            var $i = 0;
                            var totalConcepto;

                            $.each(respuesta.data.registros, function(key, value) {
                                var $link_irA = '';

                                if ($i == 0) {
                                    $link_irA = '<a class="IrA link_pdf" href="#" data-archivophp=\'coord_jdpto_Aprobar_Propuesta.php\'>Ver Pendientes</a>';
                                    totalConcepto = 'Total de Propuestas';

                                    html_table += '<TR>';
                                    html_table += '<TD>' + totalConcepto + '</TD>';
                                    html_table += '<TD>' + value['total1'] + '</TD>';
                                    html_table += '<TD>' + $link_irA + '</TD>';
                                    html_table = html_table + '</TR>';

                                };

                                if ($i == 1) {
                                    $link_irA = '<a class="IrA link_pdf" href="#" data-archivophp=\'coord_jdpto_Aprobar_Jurado.php\'>Ver Pendientes</a>';
                                    totalConcepto = 'Total de Jurados';

                                    html_table += '<TR>';
                                    html_table += '<TD>' + totalConcepto + '</TD>';
                                    html_table += '<TD>' + value['total1'] + '</TD>';
                                    html_table += '<TD>' + $link_irA + '</TD>';
                                    html_table = html_table + '</TR>';

                                };

                                if ($i == 2 && id_tipousuario == 3) {
                                    $link_irA = '<a class="IrA link_pdf" href="#" data-archivophp=\'coord_jdpto_Aprobar_Ceremonia.php\'>Ver Pendientes</a>';
                                    totalConcepto = 'Total de Ceremonias';

                                    html_table += '<TR>';
                                    html_table += '<TD>' + totalConcepto + '</TD>';
                                    html_table += '<TD>' + value['total1'] + '</TD>';
                                    html_table += '<TD>' + $link_irA + '</TD>';
                                    html_table = html_table + '</TR>';

                                };

                                $i = $i + 1;
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Pendientes').empty();
                            $('#tabla_Pendientes').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD>' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Pendientes').empty();
                            $('#tabla_Pendientes').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE>';
                        var html_table = '<TABLE style="width:30%;" class="tabla_Registros">';
                        html_table += '<TR><TH>Pendientes</TH>\n\
                                        <TH>Cantidad</TH>\n\
                                        <TH>Acción</TH></TR>';

                        html_table = html_table + '<TR><TD>' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Pendientes').empty();
                        $('#tabla_Pendientes').html(html_table);
                    });
            }
            //FIN OBTENEMOS TODOS LOS PENDIENTES DEL PROFESOR

            $('#tabla_Pendientes').on("click", "a.link_pdf", function(e) {
                e.preventDefault();
                var archivoPhp = $(this).data('archivophp');
                $('div.ui-dialog').remove();
                $('#tmp_nuevo_Contenido').load(archivoPhp);
                $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));

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
            
            f5($(document),true);*/

            Obtener_CeremoniasJurados_Por_Autorizar(id_usuario);
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
                <p>Mis Pendientes</p>
            </div>
            <div class="barra_Herramientas">
            </div>
        </div>
        <div id="tabla_Pendientes" class="tabla_Registros">
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
    <!--Se quita el botón de home-->
    <!--    </body>
</html>-->
<!DOCTYPE html>
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
    <script src="js/expresiones_reg.js"></script>
    <script src="js/ruta_documentos.js"></script>

    <script>
        $(document).ready(function() {
            var id_usuario = ('<?php echo $_SESSION["id_usuario"] ?>');
            var id_tipousuario = ('<?php echo $_SESSION["id_tipo_usuario"] ?>');

            function Obtener_Pendientes(id_administrador) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_PENDIENTES_ADMINISTRADOR',
                    id_estatus: 2
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_Pendientes.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        console.log(textStatus)
                        var html_table = '<TABLE style="width:50%;">';
                        html_table += '<TR><TH> Pendientes </TH>\n\
                        <TH>Cantidad</TH>\n\
                        <TH>Acción</TH></TR>';

                        if (respuesta.success == true) {
                            var i = 0;
                            var totalConcepto;
                            //recorremos cada registro
                            console.log(respuesta.data.registros)
                            $.each(respuesta.data.registros, function(key, value) {
                                var $link_irA = '';
                                console.log("Valor del Registro (" + key + ") : " + value['total2']);
                                console.log("Valor de I: " + i);

                                if (i == 0) {
                                    $link_irA = '<a class="IrA link_pdf" href="#" data-archivophp=\'administrador_Aprobar_Servicio_Social.php\'>Ver Pendientes</a>';
                                    html_table += '<TR>';
                                    html_table += '<TD> Aceptaciones Servicio</TD>';
                                    html_table += '<TD>' + value['total2'] + '</TD>';
                                    html_table += '<TD>' + $link_irA + '</TD>';
                                    html_table = html_table + '</TR>';
                                    i = i + 1;
                                    return;
                                };
                                if (i == 1) {
                                    $link_irA = '<a class="IrA link_pdf" href="#" data-archivophp=\'administrador_Aprobar_Reporte_Bimestral.php\'>Ver Pendientes</a>';
                                    html_table += '<TR>';
                                    html_table += '<TD> Aceptacion Reportes</TD>';
                                    html_table += '<TD>' + value['total2'] + '</TD>';
                                    html_table += '<TD>' + $link_irA + '</TD>';
                                    html_table = html_table + '</TR>';
                                    i = i + 1;
                                    return;
                                };

                                if (i == 2) {
                                    $link_irA = '<a class="IrA link_pdf" href="#" data-archivophp=\'administrador_Aprobar_Ceremonia.php\'>Ver Pendientes</a>';
                                    html_table += '<TR>';
                                    html_table += '<TD> Aceptacion Ceremonias</TD>';
                                    html_table += '<TD>' + value['total2'] + '</TD>';
                                    html_table += '<TD>' + $link_irA + '</TD>';
                                    html_table = html_table + '</TR>';
                                    i = i + 1;
                                    return;
                                };


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

            $('#tabla_Pendientes').on("click", "a.link_pdf", function(e) {
                e.preventDefault();
                var archivoPhp = $(this).data('archivophp');
                $('div.ui-dialog').remove();
                $('#tmp_nuevo_Contenido').load(archivoPhp);
                $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));

            });


            var id_usuario = ('<?php echo $_SESSION["id_usuario"] ?>');

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
            
            f5($(document),true); */

            Obtener_Pendientes(id_usuario);
        });
    </script>

    <!--</head>
<body>
	<header>
       Mi Perfil
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
        <img id="cargador" src="css/images/engrane2.gif" /><br>
        Procesando su transacción....!<br>
        Espere por favor.
    </div>
    <!--     </body>
</html>-->
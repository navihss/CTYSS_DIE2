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

<html lang="en">

<head>
    <script src="./assets/js/expresiones_reg.js"></script>
    <script src="./assets/js/ruta_documentos.js"></script>

    <script>
        $(document).ready(function() {
            function Obtener_Pendientes_Alumno(id_alumno) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_PENDIENTES_ALUMNO',
                    id_estatus: 1
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_alumno_Pendientes.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:50%;">';
                        html_table += '<TR><TH> Pendientes </TH>\n\
                        <TH>Cantidad</TH>\n\
                        <TH>Acción</TH></TR>';

                        if (respuesta.success == true) {
                            var $i = 0;
                            var totalConcepto;
                            //recorremos cada registro
                            console.log(respuesta.data.registros);
                            $.each(respuesta.data.registros, function(key, value) {
                                var $link_irA = '';

                                if ($i == 0) {
                                    $link_irA = '<a class="IrA link_pdf" href="#" data-archivophp=\'alumno_Mi_Servicio.php\'>Ver Pendientes</a>';
                                    html_table += '<TR>';
                                    html_table += '<TD> Servicio Social</TD>';
                                    html_table += '<TD>' + respuesta.data.registros[0]["total5"] + '</TD>';
                                    html_table += '<TD>' + $link_irA + '</TD>';
                                    html_table = html_table + '</TR>';
                                    $i = $i + 1;
                                };
                                if ($i == 1) {
                                    $link_irA = '<a class="IrA link_pdf" href="#" data-archivophp=\'alumno_Mis_Reportes.php\'>Ver Pendientes</a>';
                                    html_table += '<TR>';
                                    html_table += '<TD> Reportes Bimestrales</TD>';
                                    html_table += '<TD>' + respuesta.data.registros[1]["total5"] + '</TD>';
                                    html_table += '<TD>' + $link_irA + '</TD>';
                                    html_table = html_table + '</TR>';
                                    $i = $i + 1;
                                };
                                /*
                                                            if($i==2){
                                                                $link_irA = '<a class="IrA link_pdf" href="#" data-archivophp=\'alumno_Mi_Titulacion_Por_Propuesta.php\'>Ver Pendientes</a>';
                                                                html_table += '<TR>';
                                                                html_table += '<TD> Mi inscripcion</TD>';
                                                                html_table += '<TD>' + respuesta.data.registros[2]["total5"] + '</TD>';
                                                                html_table += '<TD>' + $link_irA + '</TD>';
                                                                html_table = html_table + '</TR>';
                                                            };*/

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
            
            f5($(document),true);*/

            Obtener_Pendientes_Alumno(id_usuario);
        });
    </script>
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
<!DOCTYPE html>
<!--
Fecha:          Julio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para aprobar los Reportes Bimestrales
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


            //OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
            function Obtener_Reportes_Estadisticas(id_estatus) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_REPORTES_TITULACION_PROFESORES',
                    id_estatus: id_estatus
                };



                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_Reportes_Estadisticas.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:100%;" id="Exportar_a_Excel" >';
                        html_table += '<TR>\n\
                                    	   <TH>Fecha Registrada</TH>\n\
                                    	   <TH>Alumno</TH>\n\
                                    	   <TH>No. Cuenta</TH>\n\
                                    	   <TH>Estatus</TH>\n\
                                    	   <TH>T&iacutetulo Tema</TH>\n\
                                    	   <TH>Fecha Examen</TH>\n\
                                        </TR>';



                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {


                                html_table += '<TR>';
                                html_table += '<TD>' + value['fecha_titulacion'] + '</TD>';
                                html_table += '<TD>' + value['nombre_alumno'] + '</TD>';
                                html_table += '<TD>' + value['numero_cuenta'] + '</TD>';
                                html_table += '<TD>' + value['descripcion_estatus'] + '</TD>';
                                html_table += '<TD>' + value['titulo_propuesta'] + '</TD>';
                                html_table += '<TD>' + value['fecha_examen_profesional'] + ' </TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Reportes_Bimestrales').empty();
                            $('#tabla_Reportes_Bimestrales').html(html_table);

                        } else {
                            html_table = html_table + '<TR><TD colspan="6">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Reportes_Bimestrales').empty();
                            $('#tabla_Reportes_Bimestrales').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table += '<TR>\n\
                                         	   <TH>Fecha Registrada</TH>\n\
                                         	   <TH>Alumno</TH>\n\
                                         	   <TH>No. Cuenta</TH>\n\
                                         	   <TH>Estatus</TH>\n\
                                         	   <TH>T&iacutetulo Tema</TH>\n\
                                         	   <TH>Fecha Examen</TH>\n\
                                                 </TR>';
                        html_table = html_table + '<TR><TD colspan="6">...' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Reportes_Bimestrales').empty();
                        $('#tabla_Reportes_Bimestrales').html(html_table);
                    });
            }
            //FIN OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO



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


            Obtener_Reportes_Estadisticas(2);






        });

        function enviar_formulario(_var_tipo_doc, _var_nombre_doc, _var_num_repo, _var_tit_repo) {
            $("#datos_a_enviar").val($('<div />').append($("#id_tabla").eq(0).clone()).html());
            $("#FormularioExportacion").attr('action', "administrador_reporte_exportable.php?t=" + _var_tipo_doc + "&nombre_doc=" + _var_nombre_doc + "&num_repo=" + _var_num_repo + "&titulo_reporte=" + _var_tit_repo);
            $("#FormularioExportacion").submit();
        }
    </script>

</head>

<body>



    <div class="barra_Herramientas_exportables">
        <div>
            <div>
                <form action="" method="post" target="_blank" id="FormularioExportacion">
                    <a href="javascript:enviar_formulario('word','ReporteSS',2,'profesores')"><img src="css/images/office_word.png" /></a> |
                    <a href="javascript:enviar_formulario('excel','ReporteSS',2,'profesores')"><img src="css/images/office_excel.png" /></a> |
                    <a href="javascript:enviar_formulario('pdf','ReporteSS',2,'profesores')"><img src="css/images/Oficina_PDF.png" /></a>
                    <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                </form>
            </div>
        </div>

    </div>

    <div class="encabezado_Formulario">
        <div class="descripcion_Modulo">
            <p>Profesores</p>
        </div>
    </div>

    <div id="id_tabla">
        <div>
            <div id="tabla_Reportes_Bimestrales" class="tabla_Registros">
            </div>
        </div>
        <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
    </div>





    <div id="ventanaConfirmaVoBo" name="ventanaConfirmaVoBo">
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
</body>
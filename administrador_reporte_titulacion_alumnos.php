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
        <link href="./assets/libs/jquery-ui-1.11.4/jquery-ui.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="menu/estilo_menu.css" /> 
        <script src="./assets/libs/jquery-1.12-4/jquery-1.12.4.min.js"></script>
        <script src="./assets/libs/jquery-ui-1.11.4/jquery-ui.min.js"></script>-->
    <script src="./assets/js/expresiones_reg.js"></script>

    <script>
        $(document).ready(function() {


            //OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
            function Obtener_Reportes_Estadisticas(tx_sinodales,
                fecha_inicio,
                slc_alumnosProceso) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_REPORTES_TITULACION_ALUMNOS',
                    tx_sinodales: tx_sinodales,
                    fecha_inicio: fecha_inicio,
                    slc_alumnosProceso: slc_alumnosProceso
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
                                    	   <TH>Fecha Titulaci&oacuten</TH>\n\
                                    	   <TH>Alumno</TH>\n\
                                    	   <TH>No. Cuenta</TH>\n\
                                    	   <TH>Carrera</TH>\n\
                                    	   <TH>Opcion Titulaci&oacuten</TH>\n\
                                    	   <TH>Examen o Ceremonia</TH>\n\
                                    	   <TH>Director</TH>\n\
                                    	   <TH>Presidente</TH>\n\
                                    	   <TH>Secretario</TH>\n\
                                    	   <TH>Primer suplente</TH>\n\
                                    	   <TH>Segundo suplente</TH>\n\
                                        </TR>';

                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {


                                html_table += '<TR>';
                                html_table += '<TD>' + value['fecha_titulacion'] + '</TD>';
                                html_table += '<TD>' + value['nombre_alumno'] + '</TD>';
                                html_table += '<TD>' + value['numero_cuenta'] + '</TD>';
                                html_table += '<TD>' + value['descripcion_carrera'] + '</TD>';
                                html_table += '<TD>' + value['descripcion_tipo_propuesta'] + '</TD>';
                                html_table += '<TD>' + value['descripcion_tipo_titulacion'] + '</TD>';
                                html_table += '<TD>' + value['director'] + '</TD>';
                                html_table += '<TD>' + value['nombre_sinodal_propuesto_1'] + ' </TD>';
                                html_table += '<TD>' + value['nombre_sinodal_propuesto_2'] + ' </TD>';
                                html_table += '<TD>' + value['nombre_sinodal_propuesto_3'] + ' </TD>';
                                html_table += '<TD>' + value['nombre_sinodal_propuesto_4'] + ' </TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Reportes_Bimestrales').empty();
                            $('#tabla_Reportes_Bimestrales').html(html_table);

                        } else {
                            html_table = html_table + '<TR><TD colspan="11">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Reportes_Bimestrales').empty();
                            $('#tabla_Reportes_Bimestrales').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table += '<TR>\n\
                                             	   <TH>Fecha Titulacion</TH>\n\
                                            	   <TH>Alumno</TH>\n\
                                            	   <TH>No. Cuenta</TH>\n\
                                             	   <TH>Carrera</TH>\n\
                                             	   <TH>Opcion Titulaci�n</TH>\n\
                                             	   <TH>Examen o Ceremonia</TH>\n\
                                             	   <TH>Director</TH>\n\
                                             	   <TH>Sinodales</TH>\n\
                                                 </TR>';
                        html_table = html_table + '<TR><TD colspan="11">...' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Reportes_Bimestrales').empty();
                        $('#tabla_Reportes_Bimestrales').html(html_table);
                    });
            }
            //FIN OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO


            //Array para dar formato en español al datepicker
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




            $('#fechaInicio').datepicker({
                changeYear: true,
                changeMonth: true,
                yearRange: '1920:2050',
                onSelect: function(date) {
                    $("#fechaInicio ~ .ui-datepicker").hide();
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
            }); */


            Obtener_Reportes_Estadisticas("", "", "");


            //CLICK AL BOTON ACEPTAR REPORTE
            $('#btn_filtros').on('click', function(e) {


                var tx_sinodales = $("#sinodales").val();
                var fecha_inicio = $("#fechaInicio").val();
                var slc_alumnosProceso = $("#alumnosProceso").val();




                Obtener_Reportes_Estadisticas(tx_sinodales,
                    fecha_inicio,
                    slc_alumnosProceso);

                getTitulosFiltros();

                $("#tabla_Mi_Servicio").dialog('close');
            });
            //FIN CLICK AL BOTON ACEPTAR REPORTE







        });

        var titulo_filtros = '';

        function getTitulosFiltros() {
            titulo_filtros = '';

            var tx_sinodales = $("#sinodales").val();
            var fecha_inicio = $("#fechaInicio").val();
            var slc_alumnosProceso = $('#alumnosProceso option:selected').html();




            if (tx_sinodales != '') {
                titulo_filtros += '->'
                titulo_filtros += 'Asesor: ' + tx_sinodales

            }


            if (fecha_inicio != '') {
                titulo_filtros += '->'
                titulo_filtros += 'Fecha: ' + fecha_inicio

            }

            if (slc_alumnosProceso != '') {
                titulo_filtros += '->'
                titulo_filtros += 'Alumnos en Proceso: ' + slc_alumnosProceso

            }

            $("#id_titulo_filtros").html(titulo_filtros.substring(2, titulo_filtros.length));


        }

        function enviar_formulario(_var_tipo_doc, _var_nombre_doc, _var_num_repo, _var_tit_repo) {
            $("#datos_a_enviar").val($('<div />').append($("#id_tabla").eq(0).clone()).html());
            $("#FormularioExportacion").attr('action', "administrador_reporte_exportable.php?t=" + _var_tipo_doc + "&nombre_doc=" + _var_nombre_doc + "&num_repo=" + _var_num_repo + "&titulo_reporte=" + _var_tit_repo);
            $("#FormularioExportacion").submit();
        }

        $("#tabla_Mi_Servicio").hide();


        function openFormulario() {
            $("#tabla_Mi_Servicio").dialog({
                maxWidth: 600,
                maxHeight: 200,
                width: 550,
                height: 190,
                modal: true
            });
        }
    </script>

</head>

<body>



    <div class="barra_Herramientas_exportables">
        <div>
            <div>
                <form action="" method="post" target="_blank" id="FormularioExportacion">
                    <a href="javascript:openFormulario();" name="modal"><img src="./assets/images/ui/embudo.png" /> </a>|
                    <a href="javascript:enviar_formulario('word','ReporteSS',2,'alumnos')"><img src="./assets/images/ui/office_word.png" /></a> |
                    <a href="javascript:enviar_formulario('excel','ReporteSS',2,'alumnos')"><img src="./assets/images/ui/office_excel.png" /></a> |
                    <a href="javascript:enviar_formulario('pdf','ReporteSS',2,'alumnos')"><img src="./assets/images/ui/Oficina_PDF.png" /></a>
                    <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                </form>
            </div>
        </div>

    </div>

    <div class="encabezado_Formulario">
        <div class="descripcion_Modulo">
            <p>Titulaci&oacuten Alumnos</p>
        </div>
    </div>

    <div id="id_tabla">
        <div>
            <p id="id_titulo_filtros" class="titulo_filtros"></p>
        </div>
        <div>
            <div id="tabla_Reportes_Bimestrales" class="tabla_Registros">
            </div>
        </div>
        <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
    </div>

    <div id="tabla_Mi_Servicio" title="Filtros" class="contenido_Formulario ventanaInformativa" style="width:800px;">
        <table style="width:500px">

            <tr>
                <td>
                    <p>
                        <label for="sinodales" class="label">Sidonal:</label>
                        <input type="text" name="sinodales" id="sinodales" class="ventanaInformativa" style="width:200px;" placeholder="Nombre completo" autocomplete="off" class="entrada_Dato" />

                </td>

                <td>
                    <p>

                        <label for="fechaInicio" class="label alumno_ent">Fecha:</label>
                        <input type="text" name="fechaInicio" id="fechaInicio" autocomplete="off" title="dd/mm/aaaa" class="alumno_ent" style="width:200px;" />
                </td>
            </tr>
            <tr>
                <td>
                    <p>
                        <label for="alumnosProceso" class="label">Alumnos en Proceso:</label>
                        <select name="alumnosProceso" id="alumnosProceso">
                            <option value="0"> Todos</option>
                            <option value=1>En Proceso</option>
                        </select>

                </td>

                <td>
                    <p>

                </td>
            </tr>



            <tr>
                <td>

                </td>

                <td>
                    <p>
                        <button id="btn_filtros" class="btn_Herramientas" style="width: 160px;">Aceptar</button>
                </td>
            </tr>

        </table>
    </div>




    <div id="ventanaConfirmaVoBo" name="ventanaConfirmaVoBo">
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
</body>
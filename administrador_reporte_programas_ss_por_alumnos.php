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
    <style type="text/css">

    </style>

    <script src="./assets/js/expresiones_reg.js"></script>


    <script>
        $(document).ready(function() {
            var datos2;
            var queryObject = "";

            //OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
            function Obtener_Reportes_Estadisticas(tx_alumno, id_carrera,
                fecha_inicio, fecha_fin,
                fecha_verifico, fecha_verifico_fin,
                anio, no_registro,
                id_programa, tx_nombre_programa,
                tx_dependencia, tx_responsable, tx_jefe_inmediato,
                id_estatus, id_genero, num_cuenta) {



                var datos = {
                    Tipo_Movimiento: 'OBTENER_PROGRAMAS_SERVICO_SOCIAL',
                    tx_alumno: tx_alumno,
                    id_carrera: id_carrera,
                    fecha_inicio: fecha_inicio,
                    fecha_fin: fecha_fin,
                    fecha_verifico: fecha_verifico,
                    fecha_verifico_fin: fecha_verifico_fin,
                    anio: anio,
                    no_registro: no_registro,
                    id_programa: id_programa,
                    tx_nombre_programa: tx_nombre_programa,
                    tx_dependencia: tx_dependencia,
                    tx_responsable: tx_responsable,
                    tx_jefe_inmediato: tx_jefe_inmediato,
                    id_estatus: id_estatus,
                    id_genero: id_genero,
                    num_cuenta: num_cuenta
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
                                        	   <TH>Clave DGOSE</TH>\n\
                                        	   <TH>Nombre del programa</TH>\n\
                                               <TH>Id Servicio Social</TH>\n\
                                        	   <TH># cuenta</TH>\n\
                                        	   <TH>Nombre alumno</TH>\n\
                                        	   <TH>Carrera</TH>\n\
                                        	   <TH>Fecha inicio</TH>\n\
                                        	   <TH>Fecha fin</TH>\n\
                                        	   <TH>Estatus servicio social</TH>\n\
                                            </TR>';


                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {

                                html_table += '<TR>';
                                html_table += '<TD>' + value['id_programa'] + '</TD>';
                                html_table += '<TD>' + value['descripcion_pss'] + '</TD>';
                                html_table += '<TD>' + esNulo(value['id_ss']) + '</TD>';
                                html_table += '<TD>' + value['id_alumno'] + '</TD>';
                                html_table += '<TD>' + value['nombre_usuario'] + '</TD>';
                                html_table += '<TD>' + esNulo(value['descripcion_carrera']) + '</TD>';
                                html_table += '<TD>' + value['fecha_inicio_ss'] + '</TD>';
                                html_table += '<TD>' + esNulo(value['fecha_termino_ss']) + '</TD>';
                                html_table += '<TD>' + value['descripcion_estatus'] + '</TD>';
                                html_table = html_table + '</TR>';

                            });



                            html_table = html_table + '</TABLE>';
                            $('#tabla_Reportes_Bimestrales').empty();
                            $('#tabla_Reportes_Bimestrales').html(html_table);


                        } else {
                            html_table = html_table + '<TR><TD colspan="9">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Reportes_Bimestrales').empty();
                            $('#tabla_Reportes_Bimestrales').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table += '<TR>\n\
                                         	   <TH>Clave DGOSE</TH>\n\
                                         	   <TH>Nombre del programa</TH>\n\
                                               <TH>Id Servicio Social</TH>\n\
                                         	   <TH># cuenta</TH>\n\
                                         	   <TH>Nombre alumno</TH>\n\
                                         	   <TH>Carrera</TH>\n\
                                         	   <TH>Fecha inicio</TH>\n\
                                         	   <TH>Fecha fin</TH>\n\
                                         	   <TH>Estatus servicio social</TH>\n\
                                 				</TR>'
                        html_table = html_table + '<TR><TD colspan="9">...' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Reportes_Bimestrales').empty();
                        $('#tabla_Reportes_Bimestrales').html(html_table);
                    });
            }
            //FIN OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO



            //LLENAMOS LOS CATALOGOS
            function Obtener_Catalogo_Carrera(id_carrera) {

                var datos = {
                    Tipo_Movimiento: 'OBTENER_CATALOGO_CARRERA',
                    id_carrera: id_carrera
                };

                $.ajax({
                        data: datos,
                        type: 'POST',
                        dataType: 'json',
                        url: '_Negocio/n_administrador_Reportes_Estadisticas.php'
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_options = '';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                //recorremos los valores de cada usuario
                                html_options = html_options + '<option value=' + value['id'] +
                                    '>' + value['descripcion'] + '</option>';
                            });
                            $('#carrera').empty();
                            $('#carrera').html(html_options);

                            $('#carrera' + ' option:first-child').attr('selected', 'selected');


                        } else {
                            $('#ventanaAviso').html(respuesta.data.message);
                            //$('#ventanaAvisos').dialog('open');                                                                    
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                        //$('#ventanaAvisos').dialog('open');                            
                    });
            }
            //FIN LLENADO DE CATALOGO

            //LLENAMOS LOS CATALOGOS
            function Obtener_Catalogo_Estatus(id_carrera) {

                var datos = {
                    Tipo_Movimiento: 'OBTENER_CATALOGO_ESTATUS',
                    id_carrera: id_carrera
                };

                $.ajax({
                        data: datos,
                        type: 'POST',
                        dataType: 'json',
                        url: '_Negocio/n_administrador_Reportes_Estadisticas.php'
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_options = '';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                //recorremos los valores de cada usuario
                                html_options = html_options + '<option value=' + value['id'] +
                                    '>' + value['descripcion'] + '</option>';
                            });
                            $('#estatus').empty();
                            $('#estatus').html(html_options);

                            $('#estatus' + ' option:first-child').attr('selected', 'selected');


                        } else {
                            $('#ventanaAviso').html(respuesta.data.message);
                            //$('#ventanaAvisos').dialog('open');                                                                    
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                        //$('#ventanaAvisos').dialog('open');                            
                    });
            }
            //FIN LLENADO DE CATALOGO

            //LLENAMOS LOS CATALOGOS
            function Obtener_Catalogo_Genero(id_carrera) {

                var datos = {
                    Tipo_Movimiento: 'OBTENER_CATALOGO_GENERO',
                    id_carrera: id_carrera
                };

                $.ajax({
                        data: datos,
                        type: 'POST',
                        dataType: 'json',
                        url: '_Negocio/n_administrador_Reportes_Estadisticas.php'
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_options = '';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                //recorremos los valores de cada usuario
                                html_options = html_options + '<option value=' + value['id'] +
                                    '>' + value['descripcion'] + '</option>';
                            });
                            $('#genero').empty();
                            $('#genero').html(html_options);

                            $('#genero' + ' option:first-child').attr('selected', 'selected');


                        } else {
                            $('#ventanaAviso').html(respuesta.data.message);
                            //$('#ventanaAvisos').dialog('open');                                                                    
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                        //$('#ventanaAvisos').dialog('open');                            
                    });
            }
            //FIN LLENADO DE CATALOGO


            //LLENAMOS LOS CATALOGOS
            function Obtener_Catalogo_Dependencia(id_carrera) {

                var datos = {
                    Tipo_Movimiento: 'OBTENER_CATALOGO_DEPENDENCIA',
                    id_carrera: id_carrera
                };

                $.ajax({
                        data: datos,
                        type: 'POST',
                        dataType: 'json',
                        url: '_Negocio/n_administrador_Reportes_Estadisticas.php'
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_options = '';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                //recorremos los valores de cada usuario
                                html_options = html_options + '<option value=' + value['id'] +
                                    '>' + value['descripcion'] + '</option>';
                            });
                            $('#dependencia').empty();
                            $('#dependencia').html(html_options);

                            $('#dependencia' + ' option:first-child').attr('selected', 'selected');


                        } else {
                            $('#ventanaAviso').html(respuesta.data.message);
                            //$('#ventanaAvisos').dialog('open');                                                                    
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                        //$('#ventanaAvisos').dialog('open');                            
                    });
            }
            //FIN LLENADO DE CATALOGO

            //LLENAMOS LOS CATALOGOS
            function Obtener_Catalogo_Tipo_Servicio(id_carrera) {

                var datos = {
                    Tipo_Movimiento: 'OBTENER_CATALOGO_TIPO_SERVICIO_SOCIAL',
                    id_carrera: id_carrera
                };

                $.ajax({
                        data: datos,
                        type: 'POST',
                        dataType: 'json',
                        url: '_Negocio/n_administrador_Reportes_Estadisticas.php'
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_options = '';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                //recorremos los valores de cada usuario
                                html_options = html_options + '<option value=' + value['id'] +
                                    '>' + value['descripcion'] + '</option>';
                            });
                            $('#tipoSS').empty();
                            $('#tipoSS').html(html_options);

                            $('#tipoSS' + ' option:first-child').attr('selected', 'selected');


                        } else {
                            $('#ventanaAviso').html(respuesta.data.message);
                            //$('#ventanaAvisos').dialog('open');                                                                    
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                        //$('#ventanaAvisos').dialog('open');                            
                    });
            }
            //FIN LLENADO DE CATALOGO










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

            $(function() {
                $("#fechaFin").datepicker();
            });
            $(function() {
                $("#fechaVerifico").datepicker();
            });
            $(function() {
                $("#fechaVerificoFin").datepicker();
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

            //VALORES INICIALES
            Obtener_Catalogo_Carrera('0');
            Obtener_Catalogo_Estatus('0');
            Obtener_Catalogo_Genero('0');
            Obtener_Catalogo_Dependencia('0');
            Obtener_Catalogo_Tipo_Servicio('0');
            Obtener_Reportes_Estadisticas("", '0',
                "", "",
                "", "",
                "", "",
                "", "",
                "", "", "",
                "", "", "");


            //CLICK AL BOTON ACEPTAR REPORTE
            $('#btn_filtros').on('click', function(e) {


                var tx_alumno = $("#alumno").val();
                var id_carrera = $("#carrera").val();

                var num_cuenta = $("#num_cuenta").val();
                var fecha_verifico = $("#fechaVerifico").val();
                var fecha_verifico_fin = $("#fechaVerificoFin").val();

                var fecha_inicio = $("#fechaInicio").val();
                var fecha_fin = $("#fechaFin").val();

                var anio = $("#anio").val();
                var no_registro = $("#no_registro").val();

                var id_programa = $("#programa").val();
                var tx_nombre_programa = $("#nombre_programa").val();

                var tx_dependencia = $("#dependencia").val();
                var tx_responsable = $("#responsable").val();
                var tx_jefe_inmediato = $("#jefe_inmediato").val();

                var id_estatus = $("#estatus").val();
                var id_genero = $("#genero").val();


                Obtener_Reportes_Estadisticas(tx_alumno, id_carrera,
                    fecha_inicio, fecha_fin,
                    fecha_verifico, fecha_verifico_fin,
                    anio, no_registro,
                    id_programa, tx_nombre_programa,
                    tx_dependencia, tx_responsable, tx_jefe_inmediato,
                    id_estatus, id_genero, num_cuenta)
                getTitulosFiltros();

                $("#tabla_Mi_Servicio").dialog('close');
            });
            //FIN CLICK AL BOTON ACEPTAR REPORTE

        });


        var titulo_filtros = '';

        function getTitulosFiltros() {
            titulo_filtros = '';

            var tx_alumno = $("#alumno").val();
            var id_carrera = $('#carrera option:selected').html();

            var num_cuenta = $("#num_cuenta").val();
            var fecha_verifico = $("#fechaVerifico").val();
            var fecha_verifico_fin = $("#fechaVerificoFin").val();

            var fecha_inicio = $("#fechaInicio").val();
            var fecha_fin = $("#fechaFin").val();

            var anio = $("#anio").val();
            var no_registro = $("#no_registro").val();

            var id_programa = $("#programa").val();
            var tx_nombre_programa = $("#nombre_programa").val();

            var tx_dependencia = $('#dependencia option:selected').html();
            var tx_responsable = $("#responsable").val();
            var tx_jefe_inmediato = $("#jefe_inmediato").val();

            var id_estatus = $('#estatus option:selected').html();
            var id_genero = $('#genero option:selected').html();


            if (tx_alumno != '') {
                titulo_filtros += '->'
                titulo_filtros += 'Alumno: ' + tx_alumno

            }

            if (id_carrera != 'Todos') {
                titulo_filtros += '->'
                titulo_filtros += 'Carrera: ' + id_carrera

            }

            if (num_cuenta != '') {
                titulo_filtros += '->'
                titulo_filtros += 'Num. Cuenta: ' + num_cuenta

            }

            if (fecha_verifico != '') {
                titulo_filtros += '->'
                titulo_filtros += 'Fecha Verifico: ' + fecha_verifico

            }

            if (fecha_verifico_fin != '') {
                titulo_filtros += '->'
                titulo_filtros += 'Fecha Verifico Fin: ' + fecha_verifico_fin

            }

            if (fecha_inicio != '') {
                titulo_filtros += '->'
                titulo_filtros += 'Fecha Inicio: ' + fecha_inicio

            }
            if (fecha_fin != '') {
                titulo_filtros += '->'
                titulo_filtros += 'Fecha Termino: ' + fecha_fin
            }
            if (anio != '') {
                titulo_filtros += '->'
                titulo_filtros += 'Anio: ' + anio
            }
            if (no_registro != '') {
                titulo_filtros += '->'
                titulo_filtros += 'No Registro: ' + no_registro
            }
            if (id_programa != '') {
                titulo_filtros += '->'
                titulo_filtros += 'Programa: ' + id_programa
            }
            if (tx_nombre_programa != '') {
                titulo_filtros += '->'
                titulo_filtros += 'Nombre Programa: ' + tx_nombre_programa
            }
            if (tx_dependencia != 'Todos') {
                titulo_filtros += '->'
                titulo_filtros += 'Dependencia: ' + tx_dependencia
            }
            if (tx_responsable != '') {
                titulo_filtros += '->'
                titulo_filtros += 'Responsable: ' + tx_responsable
            }
            if (id_estatus != 'Todos') {
                titulo_filtros += '->'
                titulo_filtros += 'Estatus: ' + id_estatus
            }
            if (id_genero != 'Todos') {
                titulo_filtros += '->'
                titulo_filtros += 'Genero: ' + id_genero
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
                maxHeight: 500,
                width: 550,
                height: 490,
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
                    <a href="javascript:enviar_formulario('word','Profesores',3,'Programas Servicio Social por Alumnos')"><img src="./assets/images/ui/office_word.png" /></a> |
                    <a href="javascript:enviar_formulario('excel','Profesores',3,'Programas Servicio Social por Alumnos')"><img src="./assets/images/ui/office_excel.png" /></a> |
                    <a href="javascript:enviar_formulario('pdf','Profesores',3,'Programas Servicio Social por Alumnos')"><img src="./assets/images/ui/Oficina_PDF.png" /></a>
                    <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                </form>
            </div>
        </div>

    </div>

    <div class="encabezado_Formulario">
        <div class="descripcion_Modulo">
            <p>Programas Servicio Social por Alumnos</p>

        </div>
    </div>
    <div id="id_tabla">
        <div>
            <p id="id_titulo_filtros" class="titulo_filtros"></p>
        </div>

        <div>
            <div>
                <div id="tabla_Reportes_Bimestrales" class="tabla_Registros">
                </div>
            </div>
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
        </div>
    </div>


    <div id="tabla_Mi_Servicio" title="Filtros" class="contenido_Formulario ventanaInformativa" style="width:800px;">
        <table style="width:500px">

            <tr>
                <td>
                    <p>
                        <label for="alumno" class="label">Alumno:</label>
                        <input type="text" name="alumno" id="alumno" class="ventanaInformativa" style="width:200px;" placeholder="Nombre completo" autocomplete="off" class="entrada_Dato" />

                </td>

                <td>
                    <p>

                        <label for="carrera" class="label">Carrera:</label>
                        <select name="carrera" id="carrera" class="combo_Parametro" style="width:200px;">
                        </select>
                </td>
            </tr>

            <tr>
                <td>
                    <p>
                        <label for="num_cuenta" class="label">Num. Cuenta:</label>
                        <input type="text" name="num_cuenta" id="num_cuenta" class="ventanaInformativa" style="width:200px;" placeholder="Cuenta" autocomplete="off" class="entrada_Dato" />

                </td>
            </tr>

            <tr>
                <td>
                    <p>
                        <label for="fechaVerifico" class="label alumno_ent">Fecha Inicio de Verificacion:</label>
                        <input type="text" name="fechaVerifico" id="fechaVerifico" autocomplete="off" title="dd/mm/aaaa" class="alumno_ent" style="width:200px;" />
                    </p>
                </td>
                <td>
                    <p>
                        <label for="fechaVerificoFin" class="label alumno_ent">Fecha Fin de Verificacion:</label>
                        <input type="text" name="fechaVerificoFin" id="fechaVerificoFin" autocomplete="off" title="dd/mm/aaaa" class="alumno_ent" style="width:200px;" />
                    </p>
                </td>
            </tr>

            <tr>
                <td>
                    <p>
                        <label for="fechaInicio" class="label alumno_ent">Fecha de Inicio:</label>
                        <input type="text" name="fechaInicio" id="fechaInicio" autocomplete="off" title="dd/mm/aaaa" class="alumno_ent" style="width:200px;" />
                    </p>
                </td>

                <td>
                    <p>
                        <label for="fechaFin" class="label alumno_ent">Fecha de Terminacion:</label>
                        <input type="text" name="fechaFin" id="fechaFin" autocomplete="off" title="dd/mm/aaaa" class="alumno_ent" style="width:200px;" />
                    </p>
                </td>
            </tr>


            <tr>
                <td>
                    <p>
                        <label for="estatus" class="label">Anio:</label>
                        <input type="text" name="anio" id="anio" class="ventanaInformativa" style="width:200px;" placeholder="yyyymm" autocomplete="off" class="entrada_Dato" />
                    <p>
                </td>
                <td>
                    <p>
                        <label for="estatus" class="label">No Registro:</label>
                        <input type="text" name="no_registro" id="no_registro" class="ventanaInformativa" style="width:200px;" placeholder="201802-001" autocomplete="off" class="entrada_Dato" />

                    <p>
                </td>
            </tr>



            <tr>
                <td>
                    <p>
                        <label for="programa" class="label">Programa:</label>
                        <input type="text" name="programa" id="programa" class="ventanaInformativa" style="width:200px;" placeholder="2015-10/1-400" autocomplete="off" class="entrada_Dato" />
                    <p>
                </td>
                <td>
                    <p>

                        <label for="nombre_programa" class="label">Nombre Programa:</label>
                        <input type="text" name="nombre_programa" id="nombre_programa" class="ventanaInformativa" style="width:200px;">

                    <p>
                </td>
            </tr>


            <tr>
                <td>
                    <p>
                        <label for="programa" class="label">Dependencia:</label>
                        <select name="dependencia" id="dependencia" class="combo_Parametro" style="width:200px;">
                        </select>

                    <p>
                </td>
                <td>
                    <p>

                        <label for="responsable" class="label">Responsable:</label>
                        <input type="text" name="responsable" id="responsable" class="ventanaInformativa" style="width:200px;" />
                    <p>
                </td>
            </tr>


            <tr>
                <td>
                    <p>
                        <label for="jefe_inmediato" class="label">Jefe Inmediato:</label>
                        <input type="text" name="jefe_inmediato" id="jefe_inmediato" class="ventanaInformativa" style="width:200px;" />

                </td>
            </tr>

            <tr>
                <td>
                    <p>
                        <label for="genero" class="label">Genero:</label>
                        <select name="genero" id="genero" class="combo_Parametro" style="width:200px;">
                        </select>

                </td>

                <td>
                    <p>
                        <label for="estatus" class="label">Estatus:</label>
                        <select name="estatus" id="estatus" class="combo_Parametro" style="width:200px;">
                        </select>

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
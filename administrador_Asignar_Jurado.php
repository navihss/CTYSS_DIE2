<!DOCTYPE html>
<!--
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para Jurado Definitivo del Alumno
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

            //OBTENEMOS LOS JURADOS PENDIENTES
            function Obtener_Jurados_Pendientes() {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_JURADOS_PENDIENTES'
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_Asignar_Jurado.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:100%;">';
                        html_table = html_table + '<TR><TH>Propuesta</TH>\n\
                                                      <TH>Profesor</TH>\n\
                                                      <TH>Título Propuesta</TH>\n\
                                                      <TH>Jurado-Fecha Alta</TH>\n\
                                                      <TH>Acción</TH></TR>';
                        if (respuesta.success == true) {
                            var $btn_Revisar = "";
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                $btn_Revisar = '<button class="btn_Revisar btnOpcion" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' +
                                    ' data-id_profesor= \'' + value['id_profesor'] + '\'' +
                                    ' data-titulo_propuesta = \'' + value['titulo_propuesta'] + '\'' +
                                    ' data-id_version=' + value['version'] + ' ' +
                                    ' data-id_estatus= ' + value['id_estatus'] + '>Revisar Jurado</button>';
                                html_table = html_table + '<TR>';
                                html_table = html_table + '<TD>' + value['id_propuesta'] + '</TD>';
                                html_table = html_table + '<TD style="text-align:left;">' + value['nombre'] + '</TD>';
                                html_table = html_table + '<TD style="text-align:left;">' + value['titulo_propuesta'] + '</TD>';
                                html_table = html_table + '<TD style="text-align:left;">' + esNulo(value['fecha_propuesto']) + '</TD>';
                                html_table = html_table + '<TD>' + $btn_Revisar + '</TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Jurados_Pendientes').empty();
                            $('#tabla_Jurados_Pendientes').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD colspan="5" style="text-align:center;">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Jurados_Pendientes').empty();
                            $('#tabla_Jurados_Pendientes').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table = html_table + '<TR><TH>Propuesta</TH>\n\
                                                           <TH>Profesor</TH>\n\
                                                           <TH>Título Propuesta</TH>\n\
                                                           <TH>Jurado-Fecha Alta</TH>\n\
                                                           <TH>Acción</TH></TR>';
                        html_table = html_table + '<TR><TD colspan="5">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';

                        $('#tabla_Jurados_Pendientes').empty();
                        $('#tabla_Jurados_Pendientes').html(html_table);
                    });
            } //fin Obtenemos jurados pendientes   

            $('#tabla_Jurados_Pendientes').on("click", "button.btn_Revisar", function(e) {
                e.preventDefault();
                $('#Id_Propuesta').val($(this).data("id_propuesta"));
                $('#id_Version').val($(this).data("id_version"));
                $('#titulo_propuesta').val($(this).data("titulo_propuesta"));
                $('#Tipo_Movimiento').val('ACTUALIZAR_DEFINITIVOS');
                Obtener_Sinodales($(this).data("id_propuesta"), $(this).data("id_version"));
            });

            //OBTENEMOS LOS SINODALES
            function Obtener_Sinodales(id_propuesta, id_version) {
                $('#ventanaProcesando').dialog('open');
                var datos = {
                    Tipo_Movimiento: 'OBTENER_JURADOS_SELECCIONADO',
                    id_propuesta: id_propuesta,
                    id_version: id_version
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_Asignar_Jurado.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table = html_table + '<TR><TH>Sinodal Propuesto</TH>\n\
                                                      <TH>Sinodal Definitivo</TH>\n\
                                                      <TH>Acción</TH></TR>';

                        var html_table_notas = '<TABLE class="tabla_Registros">';
                        html_table_notas += '<TR class="titulos_notas"><TH>Coord./Dpto.</TH>\n\
                                                <TH>Aceptado</TH>\n\
                                                <TH>Nota</TH></TR>';

                        if (respuesta.success == true) {
                            var nombre_textarea = '';
                            var data_checkbox = '';
                            var data_textarea = '';
                            var btn_VoBo = '';
                            var btn_Buscar = '';
                            var clase_numprof = '';
                            var fue_aceptado = '';

                            $.each(respuesta.data.propuestos, function(key, value) {
                                btn_VoBo = '<button class="btn_VoBo btnOpcion" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' +
                                    ' data-num_profesor= \'' + value['num_profesor'] + '\' ' +
                                    ' data-sinodal= \'' + value['nombre_sinodal_propuesto'] + '\' ' +
                                    ' data-id_version=' + value['version'] + '>Notas</button>';
                                btn_Buscar = '<button class="btn_Buscar btnOpcion" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' +
                                    ' data-num_profesor= \'' + value['num_profesor'] + '\'' +
                                    ' data-id_version=' + value['version'] + '>Buscar</button>';

                                nombre_textarea = " id=txt_Sinodal_Def_" + value['num_profesor'] + " ";
                                data_textarea = " data-num_profesor = " + value['num_profesor'] + " ";
                                data_textarea += " data-id_profesor_seleccionado = 0  ";
                                html_table += '<TR><TD style="vertical-align:top;width:150px;">' + value['nombre_sinodal_propuesto'] + '</TD>';
                                html_table += '<TD><input type="text" class="input_Parametro" style="width:350px;" ' + nombre_textarea + data_textarea + ' readonly>' + '</TD>';
                                html_table += '<TD style="vertical-align:top;width:150px;">' + btn_Buscar + btn_VoBo + '</TD></TR>';
                            });

                            $.each(respuesta.data.vobo, function(key, value) {
                                fue_aceptado = 'SI'
                                if (value['aceptado'] == '0') {
                                    fue_aceptado = 'NO';
                                }
                                clase_numprof = " class = prof_" + value['num_profesor'];
                                html_table_notas += '<TR ' + clase_numprof + '><TD style="vertical-align:top;width:150px;">' + value['area'] + '</TD>';
                                html_table_notas += '<TD style="text-align:center; vertical-align:top;">' + fue_aceptado + '</TD>';
                                html_table_notas += '<TD><textarea style="height:4em;width:350px;" readonly>' + value['nota'] + '</textarea></TD></TR>';

                            });

                            html_table += '</TABLE>';
                            $('#tabla_Definitivo').html(html_table);

                            html_table_notas += '</TABLE>';
                            $('#tabla_Notas').html(html_table_notas);

                            $('#ventanaJuradoDef').dialog('open');
                            $('#ventanaProcesando').dialog('close');
                        } else {
                            html_table = html_table + '<TR><TD style="text-align:center;" colspan="3">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Definitivo').empty();
                            $('#tabla_Definitivo').html(html_table);
                            $('#ventanaJuradoDef').dialog('open');
                            $('#ventanaProcesando').dialog('close');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaProcesando').dialog('close');
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table = html_table + '<TR><TH>Sinodal Propuesto</TH>\n\
                                                                  <TH>Sinodal Definitivo</TH>\n\
                                                                  <TH>Acción</TH></TR>';
                        html_table = html_table + '<TR><TD style="text-align:center;" colspan="3">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Definitivo').empty();
                        $('#tabla_Definitivo').html(html_table);
                        $('#ventanaJuradoDef').dialog('open');
                    });
            } //fin Obtenemos los Sinodales

            $('#tabla_Definitivo').on("click", "button.btn_Buscar", function(e) {
                e.preventDefault();
                var num_sinodal = $(this).data('num_profesor');

                $('#ventanaBuscarProfesor').dialog({
                    buttons: {
                        "Aceptar": function() {
                            //                                $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
                            //                                $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                            $('#ventanaProcesando').dialog('open');

                            //OBTENEMOS A QUIEN SELECCIONO
                            var id_prof = $('input[type=radio]:checked').data('id_usuario');
                            var nombre_prof = $('input[type=radio]:checked').data('nombre');
                            if (!id_prof) {
                                $('#ventanaAviso').html("Debe Seleccionar a un Sinodal.");
                                $('#ventanaAvisos').dialog('open');
                            } else {
                                $(this).dialog('close');
                                $('#txt_Sinodal_Def_' + num_sinodal).val(nombre_prof);
                                $('#txt_Sinodal_Def_' + num_sinodal).data('id_profesor_seleccionado', id_prof);
                            }
                            $('#ventanaProcesando').dialog('close');
                        },
                        "Cancelar": function() {
                            $(this).dialog('close');
                        }
                    },
                    open: function() {
                        $('#resultadoBusqueda').empty();
                        $('#textoBuscar').val('');
                    },
                    title: 'Buscar',
                    modal: true,
                    autoOpen: true,
                    resizable: true,
                    draggable: true,
                    width: "650",
                    height: "500",
                    show: 'slide',
                    hide: 'slide',
                    dialogClass: 'no-close',
                    closeOnEscape: false,
                    position: {
                        at: 'center top'
                    }
                });
            });

            $('#btn_Buscar_Prof').on("click", function(e) {
                e.preventDefault();
                if (!$('#textoBuscar').val().match(miExpReg_Buscar)) {
                    $('#ventanaAviso').html("SOLO puede Capturar letras.");
                    $('#ventanaAvisos').dialog('open');
                } else {
                    Obtener_Profesores($('#textoBuscar').val());
                }
            });

            //OBTENEMOS LOS PROFESORES
            function Obtener_Profesores(textoBuscar) {
                $('#ventanaProcesando').dialog('open');
                var datos = {
                    Tipo_Movimiento: 'OBTENER_PROFESORES',
                    textoBuscar: textoBuscar
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_Asignar_Jurado.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table = html_table + '<TR><TH>Id</TH>\n\
                                                      <TH>Grado</TH>\n\
                                                      <TH>Nombre</TH>\n\
                                                      <TH>Es Externo</TH>\n\
                                                      <TH>Seleccionado</TH></TR>';
                        if (respuesta.success == true) {
                            var radio = "";
                            //recorremos cada registro
                            var data_radio = '';

                            $.each(respuesta.data.registros, function(key, value) {
                                data_radio = ' data-id_usuario = \'' + value['id_usuario'] + '\' ' +
                                    ' data-nombre= \'' + value['descripcion_grado_estudio'] + ' ' + value['nombre'] + '\' ';
                                radio = "<input type='radio' name='profesores' \n\
                                        value='" + value['id_usuario'] + "' " + data_radio + ">";
                                var es_Externo = value['es_externo'];
                                var si_es_ext = 'No';
                                if (es_Externo == 1) {
                                    si_es_ext = "Si";
                                }

                                html_table = html_table + '<TR>';
                                html_table = html_table + '<TD>' + value['id_usuario'] + '</TD>';
                                html_table = html_table + '<TD style="text-align:center;">' + value['descripcion_grado_estudio'] + '</TD>';
                                html_table = html_table + '<TD>' + value['nombre'] + '</TD>';
                                html_table = html_table + '<TD style="text-align:center;">' + esNulo(si_es_ext) + '</TD>';
                                html_table = html_table + '<TD style="text-align:center;">' + radio + '</TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#resultadoBusqueda').empty();
                            $('#resultadoBusqueda').html(html_table);
                            $('#ventanaProcesando').dialog('close');
                        } else {
                            html_table = html_table + '<TR><TD colspan="5" style="text-align:center;">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#resultadoBusqueda').empty();
                            $('#resultadoBusqueda').html(html_table);
                            $('#ventanaProcesando').dialog('close');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaProcesando').dialog('close');
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table = html_table + '<TR><TH>Id</TH>\n\
                                                               <TH>Grado</TH>\n\
                                                               <TH>Nombre</TH>\n\
                                                               <TH>Es Externo</TH>\n\
                                                               <TH>Seleccionado</TH></TR>';

                        html_table = html_table + '<TR><TD colspan="5" style="text-align:center;">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>'
                        $('#resultadoBusqueda').empty();
                        $('#resultadoBusqueda').html(html_table);
                    });
            } //fin Obtenemos los Profesores

            $('#tabla_Definitivo').on("click", "button.btn_VoBo", function(e) {
                e.preventDefault();
                var id_propuesta = $(this).data('id_propuesta');
                var id_version = $(this).data('id_version');
                var num_prof = $(this).data('num_profesor');
                var clase_prof = ".prof_" + num_prof;
                var nom_sinodal = $(this).data('sinodal');
                var tituloVentana = 'Notas para el Sinodal: ' + nom_sinodal;

                $("#tabla_Notas tr").hide();
                $(".titulos_notas").show();
                $(clase_prof).show();

                $('#ventanaNotas').dialog({
                    buttons: {
                        "Cerrar": function() {
                            $(this).dialog('close');
                        }
                    },
                    open: function() {
                        $('#ventanaNotas').dialog("option", "title", tituloVentana);
                    },
                    title: 'Notas de Coord./Dpto.',
                    modal: true,
                    autoOpen: true,
                    resizable: true,
                    draggable: true,
                    width: "650",
                    height: "500",
                    show: 'slide',
                    hide: 'slide',
                    dialogClass: 'no-close',
                    closeOnEscape: false,
                    position: {
                        at: 'center top'
                    }
                });
            });

            $('#ventanaJuradoDef').dialog({
                buttons: [{
                    id: "btn_Guardar",
                    text: "Guardar",
                    click: function() {
                        if (validaDatos()) {
                            $('#ventanaConfirmacion').dialog('open');
                        } else {
                            $('#ventanaAviso').html("Debe capturar todos los Sinodales Definitivos.");
                            $('#ventanaAvisos').dialog('open');
                        }
                    }
                }, {
                    id: "btn_Cancelar",
                    text: "Cerrar",
                    click: function() {
                        $('#ventanaJuradoDef input[type=text]').each(function() {
                            $(this).val('');
                        });
                        $('#ventanaJuradoDef span').each(function() {
                            $(this).hide();
                        });

                        $(this).dialog('close');
                    }
                }],
                title: 'Jurado Definitivo',
                modal: true,
                autoOpen: false,
                //                   resizable : true,
                draggable: true,
                height: 'auto',
                width: '700',
                dialogClass: 'no-close',
                show: 'slide',
                hide: 'slide',
                closeOnEscape: false,
                position: {
                    at: 'center top'
                },
                close: function() {
                    $('#ventanaJuradoDef input[type=text]').each(function() {
                        $(this).val('');
                    });
                    $('#ventanaJuradoDef span').each(function() {
                        $(this).hide();
                    });
                    $(this).dialog('close');
                }
            });

            //VALIDACIONES 
            function validaDatos() {
                //RECORREMOS LOS INPUT
                var datosValidos = true;
                var lista_VoBo = '';
                var refnom_textarea = '';

                $("#ventanaJuradoDef input[type=text]").each(function(index) {
                    if (($(this).prop('value') == '')) {
                        datosValidos = false;
                    }
                });

                return datosValidos;
            };
            //FIN VALIDACIONES PARA GUARDAR

            $('#ventanaConfirmacion').dialog({
                buttons: {
                    "Aceptar": function() {
                        $(this).dialog('close');
                        //                            $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
                        //                            $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                        $('#ventanaProcesando').dialog('open');

                        // Por Ajax Actualizamos el Jurado Definitivo
                        var num_prof = 0;
                        var id_prof = '';
                        var nom_prof = '';
                        var cadena_id_Sinodales_Def = '';

                        $("#ventanaJuradoDef input[type=text]").each(function(index) {
                            num_prof = $(this).data('num_profesor');
                            id_prof = $('#txt_Sinodal_Def_' + num_prof).data('id_profesor_seleccionado');
                            nom_prof = $('#txt_Sinodal_Def_' + num_prof).val();
                            cadena_id_Sinodales_Def += num_prof + "," + id_prof + "," + nom_prof + "|";
                        });

                        cadena_id_Sinodales_Def = cadena_id_Sinodales_Def.substr(0, cadena_id_Sinodales_Def.length - 1);
                        //console.log(cadena_id_Sinodales_Def);

                        $('#lista_Definitivos').val(cadena_id_Sinodales_Def);

                        var formDatos = $('#frm_Jurado_Definitivo').serializeArray();
                        //var formDatos = $('#frm_Jurado_Definitivo').serialize();
                        console.log("FormDatos");
                        console.log(formDatos);




                        $.ajax({
                                data: formDatos,
                                type: 'POST',
                                dataType: 'json',
                                url: '_Negocio/n_administrador_Asignar_Jurado.php'

                            })

                            .done(function(respuesta, textStatus, jqXHR) {

                                console.log("HOLA");
                                $('#ventanaProcesando').dialog('close');
                                console.log(textStatus);
                                console.log(respuesta);
                                console.log(respuesta.success);
                                if (respuesta.success == true) {
                                    console.log("entra al if");
                                    $("#btn_Guardar").button("option", "disabled", true);
                                    Obtener_Jurados_Pendientes();
                                } else {
                                    $("#btn_Guardar").button("option", "disabled", false);
                                }
                                $('#ventanaAviso').html(respuesta.data.message);
                                $('#ventanaAvisos').dialog('open');
                            })
                            .fail(function(jqXHR, textStatus, errorThrown) {
                                console.log(jqXHR);
                                console.log("----------------")
                                console.log(textStatus);
                                console.log("----------------")
                                console.log(errorThrown);
                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                $('#ventanaAvisos').dialog('open');

                            });
                    },
                    "Cancelar": function() {
                        $(this).dialog('close');
                    }
                },
                title: 'Jurado',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: true,
                dialogClass: 'no-close ventanaConfirmaUsuario',
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

            $('#ventanaProcesando').dialog({
                title: '',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: false,
                dialogClass: 'no-close no-titlebar',
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
            $('#ventanaBuscarProfesor').hide();
            $('#ventanaNotas').hide();
            Obtener_Jurados_Pendientes();

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
                <p>Jurado Definitivo</p>
            </div>
        </div>
        <div id="tabla_Jurados_Pendientes" class="tabla_Registros">
        </div>
    </div>
    <div id='ventanaJuradoDef' name="ventanaJuradoDef">
        <form id="frm_Jurado_Definitivo" name="frm_Jurado_Definitivo" method="" action="" autocomplete="off">
            <div id='tabla_Definitivo'>
            </div>

            <input type="hidden" id="Id_Propuesta" name="Id_Propuesta" value="">
            <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="">
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
            <input type="hidden" id="id_Version" name="id_Version" value="0">
            <input type="hidden" id="lista_Definitivos" name="lista_Definitivos" value="0">
            <input type="hidden" id="titulo_propuesta" name="titulo_propuesta" value="0">
        </form>
    </div>

    <div id='ventanaBuscarProfesor' class="barra_Parametros">
        <p>
            <label for="textoBuscar" class="etiqueta_Parametro">Nombre:</label>
            <input type="text" name="textoBuscar" id="textoBuscar" maxlength="100" placeholder="" onkeyup="javascript:this.value=this.value.toUpperCase();"
                title="Capture únicamente letras" class="entrada_Dato input_Parametro" style="width: 350px;" autocomplete="off">
            <input type="button" name="btn_Buscar_Prof" id="btn_Buscar_Prof"
                value='Buscar' class="btn_Herramientas" autocomplete="off">
        <div id='resultadoBusqueda' class="tabla_Registros" style="margin-top: 15px; height: 340px;">
        </div>
        </p>
    </div>

    <div id='ventanaNotas'>
        <div id='tabla_Notas'>
        </div>
    </div>

    <div id='ventanaConfirmacion'>
        Desea Solicitar el Jurado Propuesto ?
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
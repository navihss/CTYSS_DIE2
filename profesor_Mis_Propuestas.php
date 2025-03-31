<!DOCTYPE html>
<!--
Fecha:          Julio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para las Propuestas del Profesor
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

    <style type="text/css">
        .dia {
            display: inline-block;
            width: 250px;
            vertical-align: top;
            padding-top: 5px;
        }

        .titulo_hr {
            width: 100%;
            background-color: #c5c946;
            color: black;
        }

        .horario {
            width: 100%;
        }

        .item_horario {
            width: 125px;
            margin: 0 auto;
            padding-left: 1px;
            text-align: left;
        }
    </style>

    <script>
        $(document).ready(function() {
            //LLENAMOS LOS CATALOGOS
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
                        url: '_Negocio/n_administrador_Crear_Nueva_Cuenta.php'
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
                            $('#' + nom_control).empty();
                            $('#' + nom_control).html(html_options);

                            $('#' + nom_control + ' option:first-child').attr('selected', 'selected');
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

            //Crea la Tabla de Asesorias
            function crea_Tabla_Asesorias() {
                var datos = {
                    Tipo_Movimiento: 'OBTENER'
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_horarios_asesoria.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        if (respuesta.success == true) {
                            //recorremos cada registro DIA
                            $.each(respuesta.data.registros, function(key, value) {
                                div_dia = "<div class='dia'>";
                                div_dia += "  <div class ='titulo_hr'>";
                                div_dia += "  " + key + "";
                                div_dia += "  </div>";
                                div_dia += "  <div class = 'horario'>";
                                div_dia += "  <div class = 'item_horario'>";
                                //recorremos cada registro HORARIO
                                $.each(value, function(userkey, uservalue) {
                                    obj_chk = "<input type='checkbox' ";
                                    obj_chk += " id = 'D" + uservalue['id_dia'] + "H" + uservalue['id_horario'] + "' ";
                                    obj_chk += " name = 'chkHorario[]' ";
                                    obj_chk += " data-id_horario = " + uservalue['id_horario'];
                                    obj_chk += " data-id_dia = " + uservalue['id_dia'];
                                    obj_chk += ">";
                                    div_dia += obj_chk + uservalue['horario'] + "<br>";


                                });
                                div_dia += "  </div>";
                                div_dia += "  </div>";
                                div_dia += "</div>";

                                $('#tblHorarios_Asesoria tbody').append(div_dia);
                            });
                        } else {}
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {

                    });
            }
            //Fin de la Tabla de Asesorias

            function Obtener_Propuestas() {
                var datos = {
                    Tipo_Movimiento: 'OBTENER',
                    id_profesor: $('#Id_Usuario').val()
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_profesor_Mis_Propuestas.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:100%;">';
                        html_table += '<TR><TH>Id</TH>\n\
                                        <TH>Tipo</TH>\n\
                                        <TH>Titulo</TH>\n\
                                        <TH>Fecha de Aprobación</TH>\n\
                                        <TH>Estatus</TH>\n\
                                        <TH>Fecha de Vigencia</TH>\n\
                                        <TH>Aceptados</TH>\n\
                                        <TH>Acción</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                var $btn_Borrar = '';
                                var $btn_Editar = '';
                                var $btn_Enviar_Doc = '';
                                var $link_Info_Alumnos_Inscritos = '';
                                $link_Info_Alumnos_Inscritos = '<a class="aDagosGrales link_pdf" href="#" data-id_propuesta =\'' + value["id_propuesta"] + '\'>Ver Lista</a>';

                                //                                   var myDate = new Date(Date.parse(value['fecha_registrada'])); 
                                //                                   myDate.setFullYear(myDate.getFullYear() + 1); 

                                if (value['id_estatus'] == 2) {
                                    $btn_Borrar = '<button class="btn_Borrar_Propuesta btnOpcion" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' +
                                        ' data-id_profesor = \'' + value['id_profesor'] + '\' ' +
                                        ' data-id_estatus =' + value['id_estatus'] +
                                        ' data-id_tipo_propuesta =' + value['id_tipo_propuesta'] +
                                        ' data-id_division =' + value['id_division'] +
                                        ' data-titulo_propuesta = \'' + value['titulo_propuesta'] + '\' ' +
                                        ' data-descripcion_tipo_propuesta = \'' + value['descripcion_tipo_propuesta'] + '\' ' +
                                        '>Borrar</button>';
                                }
                                $btn_Editar = '<button class="btn_Editar btnOpcion" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' +
                                    ' data-id_profesor = \'' + value['id_profesor'] + '\' ' +
                                    ' data-id_estatus =' + value['id_estatus'] +
                                    ' data-id_tipo_propuesta =' + value['id_tipo_propuesta'] + '>Editar</button>';
                                //if (value['id_estatus'] < 3) {
                                //Se comenta codigo donde capturaba formulario y ahora mostrara para adjuntar archivo
                                /*
                                   $btn_Enviar_Doc = '<button class="btn_Enviar btnOpcion" style="margin-top:4px;" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' + 
                                            ' data-id_profesor = \'' + value['id_profesor'] + '\' ' +
                                            ' data-id_estatus =' + value['id_estatus'] + 
                                            ' data-id_tipo_propuesta =' + value['id_tipo_propuesta'] + '>Adjuntar Propuesta</button>';
                                            */
                                //}
                                $btn_Enviar_Doc = '<button class="btn_Enviar btnOpcion" style="margin-top:4px;" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' +
                                    ' data-id_profesor = \'' + value['id_profesor'] + '\' ' +
                                    ' data-id_estatus =' + value['id_estatus'] +
                                    ' data-id_tipo_propuesta =' + value['id_tipo_propuesta'] + '>Enviar Doc</button>';
                                html_table += '<TR>';
                                html_table += '<TD>' + value['id_propuesta'] + '</TD>';
                                html_table += '<TD style="text-align:left;">' + value['descripcion_tipo_propuesta'] + '</TD>';
                                html_table += '<TD style="text-align:left;">' + value['titulo_propuesta'] + '</TD>';
                                html_table += '<TD>' + esNulo(value['fecha_aceptacion']) + '</TD>';
                                html_table += '<TD>' + value['descripcion_estatus'] + '</TD>';
                                html_table += '<TD>' + esNulo(value['fecha_vigencia']) + '</TD>';
                                //                                   html_table += '<TD>' + esNulo(myDate) + '</TD>';  
                                html_table += '<TD>' + $link_Info_Alumnos_Inscritos + '</TD>';
                                html_table += '<TD style="text-align:right;">' + $btn_Borrar + $btn_Editar + $btn_Enviar_Doc + '</TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Propuestas').empty();
                            $('#tabla_Propuestas').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD colspan="7">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Propuestas').empty();
                            $('#tabla_Propuestas').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table += '<TR><TH>Id</TH>\n\
                                        <TH>Tipo</TH>\n\
                                        <TH>Titulo</TH>\n\
                                        <TH>Fecha de Aprobación</TH>\n\
                                        <TH>Estatus</TH>\n\
                                        <TH>Fecha de Vigencia</TH>\n\
                                        <TH>Inscritos</TH>\n\
                                        <TH>Acción</TH></TR>';

                        html_table = html_table + '<TR><TD colspan="7">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Propuestas').empty();
                        $('#tabla_Propuestas').html(html_table);
                    });
            }
            //FIN OBTENEMOS LAS PROPUESTAS DEL PROFESOR

            $('#tabla_Propuestas').on("click", "a.link_pdf", function(e) {
                e.preventDefault();
                var id_propuesta = $(this).data('id_propuesta');
                $('#ventana_DatosContactoAlumnos').dialog({
                    open: function() {
                        var datos = {
                            Tipo_Movimiento: 'OBTENER_DATOSGENERALES',
                            Id_Tipo_Usuario: 5,
                            Id_Usuario: 0,
                            id_inscripcion: '',
                            id_propuesta: id_propuesta
                        };
                        $.ajax({
                                data: datos,
                                type: "POST",
                                dataType: "json",
                                url: "_Negocio/n_Usuario.php"
                            })
                            .done(function(respuesta, textStatus, jqXHR) {
                                var html_table = '<TABLE  style="width:100%;>"';
                                html_table += '<TR><TH>No. Cta.</TH>\n\
                                        <TH>Alumno</TH>\n\
                                        <TH>Carrera</TH>\n\
                                        <TH>Teléfono Fijo</TH>\n\
                                        <TH>Celular</TH>\n\
                                        <TH>email</TH></TR>';

                                if (respuesta.success == true) {
                                    //recorremos cada registro
                                    $.each(respuesta.data.registros, function(key, value) {
                                        html_table += '<TR>';
                                        html_table += '<TD>' + value['id_alumno'] + '</TD>';
                                        html_table += '<TD style="text-align:left;">' + value['nombre_usuario'] + ' ' + value['apellido_paterno_usuario'] + ' ' + value['apellido_materno_usuario'] + '</TD>';
                                        html_table += '<TD style="text-align:left;">' + value['descripcion_carrera'] + '</TD>';
                                        html_table += '<TD>' + value['telefono_fijo_alumno'] + '</TD>';
                                        html_table += '<TD>' + value['telefono_celular_alumno'] + '</TD>';
                                        html_table += '<TD>' + value['email_usuario'] + '</TD>';
                                        html_table = html_table + '</TR>';
                                    });
                                    html_table = html_table + '</TABLE>';
                                    $('#tabla_DatosContactoAlumnos').empty();
                                    $('#tabla_DatosContactoAlumnos').html(html_table);

                                } else {
                                    html_table = html_table + '<TR><TD colspan="6">' + respuesta.data.message + '</TD></TR>';
                                    html_table = html_table + '</TABLE>'
                                    $('#tabla_DatosContactoAlumnos').empty();
                                    $('#tabla_DatosContactoAlumnos').html(html_table);
                                }
                            })
                            .fail(function(jqXHR, textStatus, errorThrown) {
                                var html_table = '<TABLE style="width:100%;">';
                                html_table += '<TR><TH>No. Cta.</TH>\n\
                                        <TH>Alumno</TH>\n\
                                        <TH>Carrera</TH>\n\
                                        <TH>Teléfono Fijo</TH>\n\
                                        <TH>Celular</TH>\n\
                                        <TH>email</TH></TR>';

                                html_table = html_table + '<TR><TD colspan="6">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_DatosContactoAlumnos').empty();
                                $('#tabla_DatosContactoAlumnos').html(html_table);

                            });
                    },
                    close: function() {
                        $('#tabla_DatosContactoAlumnos').empty();
                        $(this).dialog('destroy');
                    },
                    title: 'Información de Contacto de Alumnos Aceptados Actualmente',
                    modal: false,
                    autoOpen: true,
                    resizable: false,
                    draggable: true,
                    width: 'auto',
                    height: 'auto',
                    show: 'slide',
                    hide: 'slide',
                    position: 'left',
                    closeOnEscape: false
                });
            });

            $('#tabla_Propuestas').on("click", "button.btn_Borrar_Propuesta", function(e) {
                e.preventDefault();

                var id_propuesta = $(this).data("id_propuesta");
                var id_estatus = $(this).data("id_estatus");
                var id_profesor = $(this).data("id_profesor");
                var titulo_propuesta = $(this).data("titulo_propuesta");
                var id_division = $(this).data("id_division");
                var descripcion_tipo_propuesta = $(this).data("descripcion_tipo_propuesta");

                if (parseInt(id_estatus) == 2) { //2. Por Aut Admin
                    $('#ventanaConfirmarBorrado_Propuesta').dialog({
                        buttons: {
                            "Aceptar": function() {
                                if (!$('#nota').val().match(miExpReg_Nota_Rechazo)) {
                                    $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #');
                                    $('#ventanaAvisos').dialog('open');
                                } else {
                                    $('#MensajeConfirmaBorrar').text('Desea dar de Baja esta Propuesta ?');
                                    $('#ventanaConfirmacionBorrar').dialog({
                                        buttons: {
                                            "Aceptar": function() {
                                                $(this).dialog('close');
                                                $('#ventanaProcesando').dialog('open');

                                                // Por Ajax damos de baja la Propuesta
                                                $.ajax({
                                                        data: {
                                                            Tipo_Movimiento: 'BORRAR_PROPUESTA',
                                                            id_propuesta: id_propuesta,
                                                            id_profesor: id_profesor,
                                                            titulo_propuesta: titulo_propuesta,
                                                            descripcion_tipo_propuesta: descripcion_tipo_propuesta,
                                                            id_division: id_division,
                                                            nota: $('#nota').val()
                                                        },
                                                        type: "POST",
                                                        dataType: "json",
                                                        url: "_Negocio/n_profesor_Mis_Propuestas.php"
                                                    })
                                                    .done(function(respuesta, textStatus, jqXHR) {
                                                        $('#ventanaProcesando').dialog('close');
                                                        if (respuesta.success == true) {
                                                            $('#ventanaConfirmacionBorrar').dialog('close');
                                                            $('#nota').val('');
                                                            Obtener_Propuestas($('#Id_Usuario').val());
                                                            $('#ventanaConfirmarBorrado_Propuesta').dialog('close');
                                                        }
                                                        $('#ventanaAviso').html(respuesta.data.message);
                                                        $('#ventanaAvisos').dialog('open');

                                                    })
                                                    .fail(function(jqXHR, textStatus, errorThrown) {
                                                        $('#ventanaProcesando').dialog('close');
                                                        $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                                        $('#ventanaAvisos').dialog('open');
                                                    });
                                            },
                                            "Cancelar": function() {
                                                $(this).dialog('close');
                                            }
                                        },
                                        title: 'Confirmar la Baja de la Propuesta',
                                        modal: true,
                                        autoOpen: true,
                                        resizable: true,
                                        draggable: true,
                                        dialogClass: 'no-close ventanaConfirmaUsuario',
                                        closeOnEscape: false
                                    });
                                }
                            },
                            "Cancelar": function() {
                                $('#nota').val('');
                                $(this).dialog('close');
                            }
                        },
                        title: 'Baja de Propuesta',
                        modal: true,
                        autoOpen: true,
                        resizable: true,
                        draggable: true,
                        height: 'auto',
                        width: 450,
                        dialogClass: 'no-close',
                        closeOnEscape: false
                    });
                }
            });

            //BOTON EDITAR PROPUESTA
            $('#tabla_Propuestas').on("click", "button.btn_Editar", function(e) {
                e.preventDefault();
                $('#Id_Propuesta').val($(this).data("id_propuesta"));
                $('#Tipo_Movimiento').val('ACTUALIZAR');
                $('#Id_Estatus_Propuesta').val($(this).data("id_estatus"));
                $('#n_Alumnos').val(0);
                //                    alert($(this).data("id_estatus"));
                var id_propuesta = $(this).data("id_propuesta");

                $('#carrera').empty();
                var id_division = ('<?php echo $_SESSION["id_division"] ?>');
                llena_Catalogo('carrera', 'CATALOGO_GENERALES', 'carreras',
                    ' id_carrera as id, descripcion_carrera as descripcion',
                    ' id_division = ' + id_division + ' AND id_carrera NOT IN (SELECT a.id_carrera ' +
                    ' FROM propuesta_profesor_carrera_requeridos a ' +
                    ' WHERE a.id_propuesta = \'' + id_propuesta + '\')', 'descripcion_carrera');
                //                    
                Obtener_Propuesta(id_propuesta);
                //                    deshabilitaControles();
                $('#ventana_Propuesta').dialog('open');
            });
            //FIN BOTON EDITAR PROPUESTA

            //OBTENEMOS LA PROPUESTA DEL PROFESOR
            function Obtener_Propuesta(id_propuesta) {
                $('#ventanaProcesando').dialog('open');
                var datos = {
                    Tipo_Movimiento: 'SELECCIONAR',
                    id_propuesta: id_propuesta
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_profesor_Mis_Propuestas.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        //                            var finicio='';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $('#id_propuesta').val(respuesta.data.registros.Propuesta_ProfesorId_Propuesta);
                            $('#tipo_propuesta').val(respuesta.data.registros.Propuesta_ProfesorId_Tipo_Propuesta);
                            $('#titulo_prop').val(respuesta.data.registros.Propuesta_ProfesorTitulo);
                            $('#organismos_colaboradores').val(respuesta.data.registros.Propuesta_ProfesorOrganismos_Colaboradores);
                            $('#fecha_registrada').val(respuesta.data.registros.Propuesta_ProfesorFecha_Registrada);
                            $('#aceptar_inscripciones').prop('checked', (respuesta.data.registros.Propuesta_Profesoraceptar_inscripciones) == 0 ? false : true);

                            var horarios_asesoria = respuesta.data.registros.Propuesta_Profesorhorarios;
                            var carreras_n_alumnos = respuesta.data.registros.Propuesta_Profesorrequerimiento_alumnos;

                            //requerimientos carrera-alumnos
                            var arr_c_a = carreras_n_alumnos.split("|");
                            var elementos_arr = arr_c_a.length;
                            var carrera_desc = '';
                            var n_alumnos = '';
                            var btn_Borrar = '';
                            var n_alumnos_total = 0;

                            $('#tblRequerimientos tbody').html('');
                            for (i = 0; i < elementos_arr; i++) {
                                carr_alum = arr_c_a[i].split(",");
                                id_carrera = carr_alum[0];
                                carrera_desc = carr_alum[1];
                                n_alumnos = carr_alum[2];
                                n_alumnos_total += n_alumnos;
                                btn_Borrar = "<button class='btn_borrar_p btnOpcion' data-id_carrera='" + id_carrera +
                                    "' data-alumnos=" + n_alumnos +
                                    " data-desc_carrera ='" + carrera_desc + "'>Borrar</button>";
                                $("#tblRequerimientos tbody").append(
                                    "<tr>" + "<td>" + carrera_desc + "</td>" +
                                    "<td>" + n_alumnos + "</td>" +
                                    "<td>" + btn_Borrar + "</td>" +
                                    "</tr>");
                            }
                            $('#n_Alumnos').val(n_alumnos_total);

                            //horarios de asesoria
                            var arr_asesorias = horarios_asesoria.split("|");
                            var elementos_arr_asesoria = arr_asesorias.length;
                            var id_dia = '';
                            var id_horario = '';

                            for (i = 0; i < elementos_arr_asesoria; i++) {
                                dia_horario = arr_asesorias[i].split(",");
                                id_dia = dia_horario[0];
                                id_horario = dia_horario[1];
                                check_control_id = "#D" + id_dia + "H" + id_horario;
                                $(check_control_id).prop('checked', true);
                            }

                            $('#ventanaProcesando').dialog('close');
                        } else {
                            $('#ventanaProcesando').dialog('close');
                            $('#ventanaAviso').html(respuesta.data.message);
                            $('#ventanaAvisos').dialog('open');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisos').dialog('open');
                    });
            } //fin Obtenemos la Propuesta del profesor

            $('#btn_Agregar').click(function(e) {
                e.preventDefault();
                //                    habilitaControles();
                $('#Id_Estatus_Propuesta').val(0);
                $('#Tipo_Movimiento').val('AGREGAR');
                $('#carrera').empty();
                var id_division = ('<?php echo $_SESSION["id_division"] ?>');
                llena_Catalogo('carrera', 'CATALOGO_GENERALES', 'carreras',
                    'id_carrera as id, descripcion_carrera as descripcion',
                    'id_division = ' + id_division, 'descripcion_carrera');

                $('#ventana_Propuesta').dialog('open');
                $('#n_Alumnos').val(0);
            });


            $('#tblRequerimientos tbody').on('click', '.btn_borrar_p', function(e) {
                e.preventDefault();
                //                    alert($(this).data('id_carrera'));
                $("<option value='" + $(this).data('id_carrera') + "'>" + $(this).data('desc_carrera') + "</option>").appendTo("#carrera");
                $(this).parents('tr').remove();
                n_alum = parseInt($('#n_Alumnos').val()) - parseInt($(this).data('alumnos'));
                $('#n_Alumnos').val(n_alum);
            });

            $('#btn_Agregar_Requerimiento').click(function(e) {
                e.preventDefault();
                if ($('#num_alumnos').val() == '') {
                    $('#ventanaAviso').text('Indique el Número de Alumnos requeridos de la Carrera seleccionada.');
                    $('#ventanaAvisos').dialog('open');
                    return false;
                }
                if ($('#carrera').text() == '') {
                    $('#ventanaAviso').text('No existen elementos en la lista.');
                    $('#ventanaAvisos').dialog('open');
                    return false;
                }
                numero_alumnos = parseInt($('#n_Alumnos').val()) + parseInt($('#num_alumnos').val());
                if (numero_alumnos > 5) {
                    $('#ventanaAviso').text('En una Propuesta solo puede haber 5 alumnos como máximo.');
                    $('#ventanaAvisos').dialog('open');
                    return false;
                }

                var carrera_desc = $("#carrera option:selected").html();
                var n_alumnos = $('#num_alumnos').val();
                var btn_Borrar = "<button class='btn_borrar_p btnOpcion' data-id_carrera='" + $('#carrera').val() +
                    "' data-alumnos=" + n_alumnos +
                    " data-desc_carrera = '" + carrera_desc + "'>Borrar</button>";
                $("#tblRequerimientos tbody").append(
                    "<tr>" + "<td>" + carrera_desc + "</td>" +
                    "<td>" + n_alumnos + "</td>" +
                    "<td>" + btn_Borrar + "</td>" +
                    "</tr>");
                $("#carrera option:selected").remove();
                n_alum = parseInt($('#n_Alumnos').val()) + parseInt(n_alumnos);
                $('#n_Alumnos').val(n_alum);
            });

            $('#ventana_Propuesta').dialog({
                buttons: [{
                    id: "btn_Cancelar",
                    text: "Cancelar",
                    click: function() {
                        $('#ventana_Propuesta input[type=text], textarea').each(function() {
                            $(this).val('');
                        });
                        $('#ventana_Propuesta span').each(function() {
                            $(this).hide();
                        });
                        $('#tblRequerimientos tbody').html('');

                        $('#ventana_Propuesta input[type=checkbox]').each(function() {
                            $(this).prop('checked', false);
                        });

                        $(this).dialog('close');
                    }
                }],
                title: 'Propuesta',
                modal: true,
                autoOpen: false,
                //                   resizable : true,
                draggable: true,
                height: 'auto',
                width: '800',
                dialogClass: 'no-close',
                show: 'slide',
                hide: 'slide',
                closeOnEscape: false,
                position: {
                    at: 'center top'
                },
                open: function() {
                    $("#btn_Guardar").button("option", "disabled", false);
                    //                       if($('#Id_Estatus_Propuesta').val()==2 || $('#Id_Estatus_Propuesta').val()==0 ){
                    //                            $("#btn_Guardar").button("option", "disabled", false);
                    //                        }
                    //                        else{
                    //                            $("#btn_Guardar").button("option", "disabled", true);
                    //                        }                            
                },
                close: function() {
                    $('#ventana_Propuesta input[type=text], textarea').each(function() {
                        $(this).val('');
                    });
                    $('#ventana_Propuesta span').each(function() {
                        $(this).hide();
                    });
                    $('#tblRequerimientos tbody').html('');
                    $(this).dialog('close');
                }
            });

            //VALIDACIONES 
            function validaDatosEnviar() {
                $('#btn_Enviar').prop('disable', true);
                var datosValidos = true;
                var prop_objetivo = $('#prop_objetivo').val();
                var prop_problema = $('#prop_problema').val();
                var prop_metodo = $('#prop_metodo').val();
                var prop_temas = $('#prop_temas').val();
                var prop_indice = $('#prop_indice').val();
                var prop_resultados = $('#prop_resultados').val();
                if (prop_objetivo == '' || prop_problema == '' || prop_metodo == '' || prop_temas == '' || prop_indice == '' || prop_resultados == '') {
                    $('#ventanaAviso').html('Debe capturar TODA la información Requerida.');
                    $('#ventanaAvisos').dialog('open');

                    datosValidos = false;
                }
                return datosValidos;
            };

            function validaDatos() {
                $('#btn_Guardar').prop('disable', true);
                var datosValidos = true;
                var id_propuesta = $('#id_propuesta').val();
                var tipo_propuesta = $('#tipo_propuesta').val();
                var titulo_prop = $('#titulo_prop').val();
                var organimos = $('#organismos_colaboradores').val();

                $('#aviso_Titulo_Propuesta').hide();
                $('#aviso_Tipo_Propuesta').hide();
                $('#aviso_Requerimientos').hide();
                $('#aviso_Horarios').hide();
                $('#aviso_Organismos').hide();

                //        var miExpReg_Nota = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9\.\,\;\:\?\¿\(\)\-\_\#\n]{0,500}$/;
                //        var miExpReg_Titulo = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9\.\,\;\:\?\¿\(\)\-\_\#]{1,500}$/;

                if (id_propuesta == '') {
                    $('#ventanaConfirmacion').text('Desea Dar de Alta esta Propuesta ?');
                } else {
                    $('#ventanaConfirmacion').text('Desea Actualizar los datos de esta Propuesta ?');
                }

                if (!tipo_propuesta) {
                    $('#aviso_Tipo_Propuesta').show();
                    datosValidos = false;
                }
                if (!titulo_prop.match(miExpReg_Nota_Rechazo)) {
                    $('#aviso_Titulo_Propuesta').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Titulo_Propuesta').hide();
                }
                if (!organimos.match(miExpReg_Nota_Aceptacion)) {
                    $('#aviso_Organismos').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Organismos').hide();
                }

                var a = '';
                var b = '';
                var c = '';
                //Obtenemos una cadena de los requerimientos 
                $('button.btn_borrar_p').each(function(index) {
                    a = $(this).data('alumnos');
                    b = $(this).data('id_carrera');
                    c = c.concat(b, ',', a, '|');
                });

                if (c == '') {
                    $('#aviso_Requerimientos').show();
                    datosValidos = false;
                }

                var horarios_seleccionados = "";
                $('input[name="chkHorario[]"]:checked').each(function() {
                    horarios_seleccionados += $(this).data('id_dia') + "," + $(this).data('id_horario') + "|";
                });
                if (horarios_seleccionados == '') {
                    $('#aviso_Horarios').show();
                    datosValidos = false;
                }

                if (!tipo_propuesta || titulo_prop == '' || c == '' || horarios_seleccionados == '') {
                    $('#ventanaAviso').html('Debe capturar TODA la información Requerida.');
                    $('#ventanaAvisos').dialog('open');

                    datosValidos = false;
                }

                $('#btn_Guardar').prop('disable', false);
                return datosValidos;
            };

            //FIN VALIDACIONES PARA GUARDAR
            $('#ventanaEnviar').dialog({
                buttons: {
                    "Aceptar": function() {
                        $(this).dialog('close');
                    }
                },
                title: 'Propuesta',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: true,
                dialogClass: 'no-close ventanaConfirmaUsuario',
                closeOnEscape: false
            });

            $('#ventanaConfirmacion').dialog({
                buttons: {
                    "Aceptar": function() {
                        $(this).dialog('close');
                        $('#ventanaProcesando').dialog({
                            dialogClass: 'no-close'
                        });
                        $('#ventanaProcesando').dialog({
                            dialogClass: 'no-titlebar'
                        });
                        $('#ventanaProcesando').dialog('open');

                        var a = '';
                        var b = '';
                        var c = '';
                        //Obtenemos una cadena de los requerimientos 
                        $('button.btn_borrar_p').each(function(index) {
                            a = $(this).data('alumnos');
                            b = $(this).data('id_carrera');
                            c = c.concat(b, ',', a, '|');
                        });
                        c = c.substr(0, c.length - 1);

                        $('#Carrera_Alumnos').val(c);

                        //Obtenemos los horarios seleccionados
                        var horarios_seleccionados = "";
                        $('input[name="chkHorario[]"]:checked').each(function() {
                            horarios_seleccionados += $(this).data('id_dia') + "," + $(this).data('id_horario') + "|";
                        });
                        horarios_seleccionados = horarios_seleccionados.substring(0, horarios_seleccionados.length - 1);
                        $('#Mis_Horarios').val(horarios_seleccionados);

                        // Por Ajax Agregamos la Propuesta
                        formDatos = $('#frm_Propuesta').serialize();

                        var jotason = $.ajax({
                                data: formDatos,
                                type: "POST",
                                dataType: "json",
                                url: "_Negocio/n_profesor_Mis_Propuestas.php"
                            })
                            .done(function(respuesta, textStatus, jqXHR) {
                                $('#ventanaProcesando').dialog('close');
                                if (respuesta.success == true) {
                                    $('#frm_Propuesta input[type=text], textarea').each(function() {
                                        $(this).val('');
                                    });
                                    $('#frm_Propuesta span').each(function() {
                                        $(this).hide();
                                    });
                                    $('#tblRequerimientos tbody').html('');

                                    $('#De_Alta_OK').val("1");
                                } else {
                                    $('#De_Alta_OK').val("0");
                                }
                                $('#ventanaAviso_Alta_OK').html(respuesta.data.message);
                                $('#ventanaAvisos_Alta_OK').dialog('open');
                            })
                            .fail(function(jqXHR, textStatus, errorThrown) {

                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                $('#ventanaAvisos').dialog('open');
                            });
                    },
                    "Cerrar": function() {
                        $(this).dialog('close');
                    }
                },
                title: 'Propuesta',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: true,
                dialogClass: 'no-close ventanaConfirmaUsuario',
                closeOnEscape: false
            });

            $('#tabla_Propuestas').on("click", "button.btn_Enviar", function(e) {
                //e.preventDefault();
                $('#Id_Propuesta').val($(this).data("id_propuesta"));
                var id_propuesta = $(this).data("id_propuesta");
                Obtener_Mis_Documentos_Enviados(id_propuesta);
                $('#ventanaDocumentosEnviados').dialog('open');
            });

            //OBTENEMOS LOS DOCUMENTOS ENVIADOS 
            /*
            function Obtener_Mis_Documentos_Enviados(id_propuesta){

                var pkey = id_propuesta;                                  
                
                var datos = {Tipo_Movimiento : 'DOCUMENTOS_ENVIADOS',
                           id_propuesta : $('#Id_Propuesta').val()
                       };
                $.ajax({
                   data : datos,
                   type : "POST",
                   dataType : "json",
                   url : "_Negocio/n_profesor_Mis_Propuestas.php"
                })
                   .done(function(respuesta,textStatus,jqXHR){
                    if (respuesta["data"]["registros"][0]["id_tipo_propuesta"] !=2){

                      var html_table = '<TABLE class="tabla_Registros"><CAPTION> Clave de la Propuesta: ' + id_propuesta + '</CAPTION>';
                       html_table = html_table + '<TR><TH>Título</TH><TH>Versión</TH><TH>Fecha Enviado</TH><TH>Estatus</TH><TH>Nota de Admin.</TH><TH>Acción</TH></TR>';
                       if (respuesta.success == true){
                           //recorremos cada registro
                           $.each(respuesta.data.registros, function( key, value ) {
                                var $btn_EnviarDocs='';
                                var $btn_Ver_VoBo ='';
                                
                                if (value['id_estatus']==4 || value['id_estatus']==3){
                                   $btn_Ver_VoBo = '<button class="btn_Ver_VoBo btnOpcion" data-id_profesor =\'' + 
                                           value['id_profesor'] + '\' data-id_documento=' + value['id_documento'] +  
                                        ' data-id_propuesta=\'' + value['id_propuesta'] + '\' data-id_estatus=' + value['id_estatus'] +
                                        ' data-desc_documento=\'' + value['descripcion_documento'] + 
                                        '\' data-desc_documento_corta = \'' + value['descripcion_para_nom_archivo'] + '\' data-id_version=' + 
                                        value['version_propuesta']  +
                                        '>Ver VoBo</button>';                                                                            
                                }
                                if (value['id_estatus']==1){
                                   $btn_EnviarDocs = '<button class="btn_Adjuntar btnOpcion" data-id_profesor =\'' + 
                                           value['id_profesor'] + '\' data-id_documento=' + value['id_documento'] +  
                                        ' data-id_propuesta=\'' + value['id_propuesta'] + '\' data-id_estatus=' + value['id_estatus'] +
                                        ' data-desc_documento=\'' + value['descripcion_documento'] + 
                                        '\' data-desc_documento_corta = \'' + value['descripcion_para_nom_archivo'] + '\' data-id_version=' + 
                                        value['version_propuesta']  +
                                        '>Adjuntar Doc</button>';                                    
                                }
                               
                               if (value['fecha_recepcion_doc']){
                                   $dato_fecha = '<TD>' + value['fecha_recepcion_doc'] + '</TD>';
                               }
                               else{
                                   $dato_fecha = '<TD></TD>';
                               }
                               if (value['nota']){
                                   $dato_nota = '<TD>' + value['nota'] + '</TD>';
                               }
                               else{
                                   $dato_nota = '<TD></TD>';
                               }
                               nom_file= value['id_profesor']+'_'+
                                       value['id_propuesta']+'_'+
                                       value['version_propuesta']+'_'+
                                       value['descripcion_para_nom_archivo']+'.pdf';
                               fecha_=new Date();
                               ruta_doc= ruta_docs_propuestas_profesor+nom_file+'?'+ fecha_;

                               html_table = html_table + '<TR>';
                               if(value['id_estatus'] == 2 || value['id_estatus']==3){
                               html_table = html_table + '<TD style="text-align:left;"><a class="link_pdf" target="_blank" href="' + 
                                       ruta_doc +'">' + value['titulo_propuesta'] + '</a></TD>';
                                }
                                else{
                                    html_table = html_table + '<TD style="text-align:left;">' + value['titulo_propuesta'] + '</TD>';

                                }
                               html_table = html_table + '<TD style="text-align:left;">' + value['version_propuesta'] + '</TD>';
                               html_table = html_table + $dato_fecha;
                               html_table = html_table + '<TD>' + value['descripcion_estatus'] + '</TD>';
                               html_table = html_table + $dato_nota;
                               html_table = html_table + '<TD style="text-align:center;">' + $btn_EnviarDocs + $btn_Ver_VoBo + '</TD>';                                   
                               html_table = html_table + '</TR>';
                           });
                           html_table = html_table + '</TABLE>';
                           $('#tabla_Mis_Docs').empty();
                           $('#tabla_Mis_Docs').html(html_table);                                
                           }
                       else {
                           html_table = html_table + '<TR><TD colspan="6">' + respuesta.data.message + '</TD></TR>';
                           html_table = html_table + '</TABLE>'
                           $('#tabla_Mis_Docs').empty();
                           $('#tabla_Mis_Docs').html(html_table);
                       }

                    } else {
                      var html_table = '<div id="ventana_Propuesta_adj_prop" name="ventana_Propuesta_adj_prop" class="contenido_Formulario"><form id="frm_Propuesta" name="frm_Propuesta" method="post" action="profesor_Envio_Propuesta.php"><div id=tabs><p><label for="prop_objetivo" class="label">Objetivo:</label><textarea type="text" placeholder="Ingresar objetivo" name="objetivo_prop" id="prop_objetivo" value = "" style="max-height: 2em; max-width: 510px;" maxlength="500" style="text-transform:uppercase;" title="Capture únicamente letras, números y los caráctres , ; . : ¿? ( ) - # _" ></textarea></p><p><label for="prop_problema" class="label">Definición del problema:</label><textarea type="text" name="definicion_prob" placeholder="Problema a resolver" id="prop_problema" value = "" style="max-height: 2em; max-width: 510px;" maxlength="500" placeholder="" style="text-transform:uppercase;" title="Capture únicamente letras, números y los caráctres , ; . : ¿? ( ) - # _" ></textarea></p><p><label for="id_propuesta" class="label">Método:</label><textarea type="text" name="metodo" placeholder="Método a utilizar" id="prop_metodo" value = "" style="max-height: 2em; max-width: 510px;" maxlength="500" placeholder="" style="text-transform:uppercase;" title="Capture únicamente letras, números y los caráctres , ; . : ¿? ( ) - # _" ></textarea></p><p><label for="id_propuesta" class="label">Temas a utilizar:</label><textarea type="text" name="temas_prop" id="prop_temas" placeholder="Ingresar temas de la carrera a utilizar" value = "" style="max-height: 2em; max-width: 510px;" maxlength="500" style="text-transform:uppercase;" title="Capture únicamente letras, números y los caráctres , ; . : ¿? ( ) - # _" ></textarea></p><p style="height: 100px;"><label for="id_propuesta" class="label">Índice:</label><textarea type="text" rows="5" placeholder="Desglosar índice" name="indice_prop" id="prop_indice" style="text-transform:uppercase; height: auto;" title="Capture únicamente letras, números y los caráctres , ; . : ¿? ( ) - # _" ></textarea></p><p><label for="id_propuesta" class="label">Resultados Esperados:</label><textarea type="text" name="resultados_prop" rows="5" id="prop_resultados" value = "" maxlength="500" placeholder="" style="text-transform:uppercase;" title="Capture únicamente letras, números y los caráctres , ; . : ¿? ( ) - # _" ></textarea></p><p hidden><label for="id_propuesta" class="label">Id:</label><textarea type="text" name="pkey_propuesta" id="pkey_propuesta" placeholder="'+pkey+'" value = "' + pkey + '" style="max-height: 2em; max-width: 510px;" maxlength="500;" style="text-transform:uppercase;" title="Capture únicamente letras, números y los caráctres , ; . : ¿? ( ) - # _" >'+pkey+'</textarea></p>';

                      if (respuesta.success == true){
                          html_table = html_table + '</div><button class="ui-button ui-corner-all ui-widge btnEnviar" id="btnEnviar" type="submit" value="Enviar">Enviar</button></form></div>'
                          $('#tabla_Mis_Docs').empty();
                           $('#tabla_Mis_Docs').html(html_table);
                       }
                       else {
                           html_table = html_table + '<TR><TD colspan="6">' + respuesta.data.message + '</TD></TR>';
                           html_table = html_table + '</TABLE><div><p>Esto es una prueba</p></div>'
                           $('#tabla_Mis_Docs').empty();
                           $('#tabla_Mis_Docs').html(html_table);
                       }
                    }
                    

                   })
                        .fail(function(jqXHR,textStatus,errorThrown){
                            var html_table = '<TABLE class="tabla_Registros"><CAPTION> Clave de Servicio Social: ' + id_ss + '</CAPTION>';
                            html_table = html_table + '<TR><TH>Título</TH><TH>Versión</TH><TH>Fecha Enviado</TH><TH>Estatus</TH><TH>Nota de Admin.</TH><TH>Acción</TH></TR>';
                            html_table = html_table + '<TR><TD colspan="6">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Mis_Docs').empty();
                            $('#tabla_Mis_Docs').html(html_table);                                
                        });                       
            } */
            //fin Obtenemos los Documentos Enviados para Autorizar la Propuesta

            //FUNCION PARA OBTENER LOS DOCUMENTOS ENVIADOS Y SE CAMBIA PARA PODER ADJUNTAR UN PDF
            //OBTENEMOS LOS DOCUMENTOS ENVIADOS PARA AUTORIZAR EL SERVICIO SOCIAL
            function Obtener_Mis_Documentos_Enviados(id_propuesta) {
                var datos = {
                    Tipo_Movimiento: 'DOCUMENTOS_ENVIADOS',
                    id_propuesta: $('#Id_Propuesta').val()
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_profesor_Mis_Propuestas.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE class="tabla_Registros"><CAPTION> Clave de la Propuesta: ' + id_propuesta + '</CAPTION>';
                        html_table = html_table + '<TR><TH>Título</TH><TH>Versión</TH><TH>Fecha Enviado</TH><TH>Estatus</TH><TH>Nota de Admin.</TH><TH>Acción</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                var $btn_EnviarDocs = '';
                                var $btn_Ver_VoBo = '';

                                if (value['id_estatus'] == 4 || value['id_estatus'] == 3) {
                                    $btn_Ver_VoBo = '<button class="btn_Ver_VoBo btnOpcion" data-id_profesor =\'' +
                                        value['id_profesor'] + '\' data-id_documento=' + value['id_documento'] +
                                        ' data-id_propuesta=\'' + value['id_propuesta'] + '\' data-id_estatus=' + value['id_estatus'] +
                                        ' data-desc_documento=\'' + value['descripcion_documento'] +
                                        '\' data-desc_documento_corta = \'' + value['descripcion_para_nom_archivo'] + '\' data-id_version=' +
                                        value['version_propuesta'] +
                                        '>Ver VoBo</button>';
                                }
                                if (value['id_estatus'] == 1) {
                                    $btn_EnviarDocs = '<button class="btn_Adjuntar btnOpcion" data-id_profesor =\'' +
                                        value['id_profesor'] + '\' data-id_documento=' + value['id_documento'] +
                                        ' data-id_propuesta=\'' + value['id_propuesta'] + '\' data-id_estatus=' + value['id_estatus'] +
                                        ' data-desc_documento=\'' + value['descripcion_documento'] +
                                        '\' data-desc_documento_corta = \'' + value['descripcion_para_nom_archivo'] + '\' data-id_version=' +
                                        value['version_propuesta'] +
                                        '>Adjuntar Doc</button>';
                                }

                                if (value['fecha_recepcion_doc']) {
                                    $dato_fecha = '<TD>' + value['fecha_recepcion_doc'] + '</TD>';
                                } else {
                                    $dato_fecha = '<TD></TD>';
                                }
                                if (value['nota']) {
                                    $dato_nota = '<TD>' + value['nota'] + '</TD>';
                                } else {
                                    $dato_nota = '<TD></TD>';
                                }
                                nom_file = value['id_profesor'] + '_' +
                                    value['id_propuesta'] + '_' +
                                    value['version_propuesta'] + '_' +
                                    value['descripcion_para_nom_archivo'] + '.pdf';
                                fecha_ = new Date();
                                ruta_doc = ruta_docs_propuestas_profesor + nom_file + '?' + fecha_;

                                html_table = html_table + '<TR>';
                                if (value['id_estatus'] == 2 || value['id_estatus'] == 3) {
                                    html_table = html_table + '<TD style="text-align:left;"><a class="link_pdf" target="_blank" href="' +
                                        ruta_doc + '">' + value['titulo_propuesta'] + '</a></TD>';
                                } else {
                                    html_table = html_table + '<TD style="text-align:left;">' + value['titulo_propuesta'] + '</TD>';

                                }
                                html_table = html_table + '<TD style="text-align:left;">' + value['version_propuesta'] + '</TD>';
                                html_table = html_table + $dato_fecha;
                                html_table = html_table + '<TD>' + value['descripcion_estatus'] + '</TD>';
                                html_table = html_table + $dato_nota;
                                html_table = html_table + '<TD style="text-align:center;">' + $btn_EnviarDocs + $btn_Ver_VoBo + '</TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Mis_Docs').empty();
                            $('#tabla_Mis_Docs').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD colspan="6">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Mis_Docs').empty();
                            $('#tabla_Mis_Docs').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros"><CAPTION> Clave de Servicio Social: ' + id_ss + '</CAPTION>';
                        html_table = html_table + '<TR><TH>Título</TH><TH>Versión</TH><TH>Fecha Enviado</TH><TH>Estatus</TH><TH>Nota de Admin.</TH><TH>Acción</TH></TR>';
                        html_table = html_table + '<TR><TD colspan="6">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Mis_Docs').empty();
                        $('#tabla_Mis_Docs').html(html_table);
                    });
            }
            //fin Obtenemos los Documentos Enviados para Autorizar la Propuesta

            $('#ventanaDocumentosEnviados').dialog({
                buttons: {
                    "Cerrar": function() {
                        $(this).dialog('close');
                    }
                },
                title: 'Mis Documentos Enviados',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: true,
                height: '550',
                width: '850',
                show: 'slide',
                hide: 'slide',
                dialogClass: 'no-close',
                closeOnEscape: false,
                position: {
                    at: 'center top'
                }
            });

            //BOTON VER VoBo
            $('#tabla_Mis_Docs').on("click", "button.btn_Ver_VoBo", function(e) {
                e.preventDefault();
                var id_propuesta = $(this).data("id_propuesta");
                var id_documento = $(this).data("id_documento");
                var id_version = $(this).data("id_version");
                var tipo_mov = 'OBTENER_VOBO';

                $('#notas_VoBo').dialog({
                    buttons: {
                        "Cerrar": function() {
                            $(this).dialog('close');
                        }
                    },
                    open: function() {
                        $.ajax({
                                data: {
                                    Tipo_Movimiento: tipo_mov,
                                    id_propuesta: id_propuesta,
                                    id_documento: id_documento,
                                    id_version: id_version
                                },
                                type: "POST",
                                dataType: "json",
                                async: false,
                                url: "_Negocio/n_profesor_Mis_Propuestas.php"
                            })
                            .done(function(respuesta, textStatus, jqXHR) {
                                if (respuesta.success == true) {
                                    var html_notas = '';
                                    var n_nota = 1;
                                    $.each(respuesta.data.registros, function(key, value) {
                                        html_notas += '<details>';
                                        html_notas += '<summary style="margin-bottom:5px; line-height:2em; border: 1px solid black;background-color: #800000;color:white;">\n\
                                                                <b>VoBo. ' + n_nota + '.     Propuesta: ' + value['id_propuesta'] +
                                            '     Ver.: ' + value['version_propuesta'] + '  -   Revisión: ' + value['fecha_revision'] + '</b></summary>';
                                        html_notas += '<div style="margin-bottom:10px; width = 100%;border: 1px solid black; border-radius: 5px;background-color: beige;"><p style="padding:5px;">' + value['nota'] + '</p></div>';
                                        html_notas += '</details>';
                                        n_nota += 1;
                                    });
                                    $('#notas_VoBo').empty();
                                    $('#notas_VoBo').html(html_notas);

                                    $('#ventanaProcesando').dialog('close');
                                } else {
                                    $('#ventanaProcesando').dialog('close');

                                    $('#ventanaAviso').html(respuesta.data.message);
                                    $('#ventanaAvisos').dialog('open');
                                    $('#notas_VoBo').dialog('close');
                                }
                            })
                            .fail(function(jqXHR, textStatus, errorThrown) {
                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                $('#ventanaAvisos').dialog('open');
                            });
                    },
                    close: function() {
                        $('#notas_VoBo').html('');
                    },
                    title: 'VoBo.de Coordinadores / Jefes de Dpto.',
                    modal: true,
                    autoOpen: true,
                    resizable: true,
                    draggable: true,
                    height: '450',
                    width: '550',
                    show: 'slide',
                    hide: 'slide',
                    dialogClass: 'no-close',
                    closeOnEscape: false
                });
            });
            //FIN BOTON VER VoBo


            $('#tabla_Mis_Docs').on('click', "button.btn_Adjuntar", function(e) {
                e.preventDefault();
                var tituloAdjuntar = 'Para la propuesta <b>' + $(this).data('id_propuesta') +
                    ', Versión ' + $(this).data('id_version') +
                    '</b>. Seleccione su archivo <b>' + $(this).data('desc_documento') +
                    ',</b> en formato PDF. <b>(Tamaño máximo 5MB)</b><br><br>';
                $('#lblTitulo_SubirArchivo').html(tituloAdjuntar);
                //                    $('#lblTitulo_SubirArchivo').attr("data-id_ss", $(this).data('id_ss'));
                //                    $('#lblTitulo_SubirArchivo').attr("data-id_documento", $(this).data('id_documento'));
                //                    $('#lblTitulo_SubirArchivo').attr("data-id_version", $(this).data('id_version'));
                $('#id_propuesta_prof').attr("value", $(this).data('id_propuesta'));
                $('#id_documento_prof').attr("value", $(this).data('id_documento'));
                $('#id_version_prof').attr("value", $(this).data('id_version'));
                $('#id_usuario_prof').attr("value", $(this).data('id_profesor'));
                $('#desc_corta_prof').attr("value", $(this).data('desc_documento_corta'));
                $('#ventanaSubirArchivo').dialog('open');
            });

            $('#ventanaSubirArchivo').dialog({
                buttons: {
                    "Cerrar": function() {
                        Obtener_Mis_Documentos_Enviados($('#id_propuesta_prof').val());
                        $('#ventanaSubirArchivo input[type=file]').each(function() {
                            $(this).val('');
                        });
                        $('#ventanaSubirArchivo span').each(function() {
                            $(this).text('');
                        });
                        $('#message').empty();
                        $(this).dialog('close');

                    }
                },
                title: 'Adjuntar documento',
                modal: true,
                autoOpen: false,
                resizable: false,
                draggable: true,
                width: '650',
                height: 'auto',
                show: 'slide',
                hide: 'slide',
                dialogClass: 'no-close',
                closeOnEscape: false,
                position: {
                    at: 'center top'
                }
            });

            //ADJUNTAR ARCHIVO 
            $('#loading').hide();
            $(':file').change(function() {
                var archivo_selec = $('#file')[0].files[0];
                var archivo_nombre = archivo_selec.name;
                var archivo_extension = archivo_nombre.substring(archivo_nombre.lastIndexOf('.') + 1);
                var archivo_tamano = archivo_selec.size;
                var archivo_tipo = archivo_selec.type;
                //console.log(archivo_tamano);
                var info_Archivo_Selec = '';
                info_Archivo_Selec += "<table class='tabla_Registros'><tr><th colspan='2'><span>Información del Archivo seleccionado:</span></th></tr>";
                info_Archivo_Selec += "<tr><td><b>Nombre del archivo:</b></td><td>" + archivo_nombre + "</td></tr>";
                info_Archivo_Selec += "<tr><td><b>Extensión:</b></td><td>" + archivo_extension + "</td></tr>";
                info_Archivo_Selec += "<tr><td><b>Tipo:</b></td><td>" + archivo_tipo + "</td></tr>";
                info_Archivo_Selec += "<tr><td><b>Tamaño:</b></td><td>" + (archivo_tamano / 1048576) + " MB</td></tr></table>";

                $('#message').html(info_Archivo_Selec);
                $('#loading').empty();

                if (parseInt(archivo_tamano) > 500000000) {
                    $('#file').val('');
                    $('#ventanaAviso').html('El Tamaño del Archivo excede los 500 MB permitidos.');
                    $('#ventanaAvisos').dialog('open');
                }

            });

            $('#frmSubirPDF').on('submit', function(e) {
                e.preventDefault();
                $('#loading').html('<h1>Loading...</h1>');
                $('#loading').show();
                $('#ventanaProcesando').dialog('open');
                $.ajax({
                    url: "uploadFile_5_procesa.php",
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        $('#loading').html(data);
                        $('#file').val('');
                        $('#ventanaProcesando').dialog('close');
                    }
                });
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

            $('#ventanaAvisos_Alta_OK').dialog({
                buttons: {
                    "Aceptar": function() {
                        $(this).dialog('close');
                        if (($('#De_Alta_OK').val() == 1)) {
                            $('#ventana_Propuesta').dialog('close');
                        }
                        Obtener_Propuestas($('#Id_Usuario').val());
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
            var id_division = ('<?php echo $_SESSION["id_division"] ?>');

            llena_Catalogo('tipo_propuesta', 'CATALOGO_GENERALES', 'tipos_propuesta',
                'id_tipo_propuesta as id, descripcion_tipo_propuesta as descripcion',
                'id_tipo_titulacion = 1 AND id_division = ' + id_division, 'descripcion_tipo_propuesta');
            llena_Catalogo('carrera', 'CATALOGO_GENERALES', 'carreras',
                'id_carrera as id, descripcion_carrera as descripcion',
                '', 'descripcion_carrera');
            crea_Tabla_Asesorias();
            Obtener_Propuestas($('#Id_Usuario').val());
            $('#ventana_Propuesta').hide();
            $('#ventanaSubirArchivo').hide();
            $('#ventanaConfirmarBorrado_Propuesta').hide();
            $('#ventanaConfirmacionBorrar').hide();
            $('#tabs').tabs();
            $(".nexttab1").click(function() {
                var active = $("#tabs").tabs("option", "active");
                $("#tabs").tabs("option", "active", 1);
            });
            $(".nexttab2").click(function() {
                var active = $("#tabs").tabs("option", "active");
                $("#tabs").tabs("option", "active", 2);
            });

            $(".prevtab2").click(function() {
                var active = $("#tabs").tabs("option", "active");
                $("#tabs").tabs("option", "active", 0);
            });

            $(".prevtab3").click(function() {
                var active = $("#tabs").tabs("option", "active");
                $("#tabs").tabs("option", "active", 1);
            });

            $(".nexttab3").click(function() {
                if (validaDatos()) {
                    $('#ventanaConfirmacion').dialog('open');
                }
            });
            $(".btnGuardar").click(function() {

                if (validaDatos()) {
                    $('#ventanaConfirmacion').dialog('open');
                }

            });
            $(".btnEnviar").click(function() {
                if (validaDatosEnviar()) {
                    $('#ventanaEnviar').text('La propuesta se ha enviado correctamente');
                    $('#ventanaEnviar').dialog('open');
                }
            });
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
                <p>Mis Propuestas</p>
            </div>
            <div class="barra_Herramientas">
                <input type="button" id="btn_Agregar" name="btn_Agregar" value="Agregar" class="btn_Herramientas" />
            </div>
        </div>
        <div id="tabla_Propuestas" class="tabla_Registros">
        </div>
    </div>

    <div id="ventana_DatosContactoAlumnos">
        <div id="tabla_DatosContactoAlumnos" class='tabla_Registros' style="text-align: center; float: right; width: 600px; line-height: 1.1em; font-size: 0.9em;">

        </div>
    </div>

    <div id="ventana_Propuesta" name="ventana_Propuesta" class="contenido_Formulario">
        <form id="frm_Propuesta" name="frm_Propuesta" method="post" action="">
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-1">Generales</a></li>
                    <li><a href="#tabs-2">Alumnos requeridos</a></li>
                    <li><a href="#tabs-3">Colaboradores</a></li>
                </ul>

                <div id="tabs-1">
                    <p hidden>
                        <label for="id_propuesta" class="label">Propuesta:</label>
                        <input type="text" name="id_propuesta" id="id_propuesta" value=''
                            placeholder="ID Automático" autocomplete="off" readonly />
                    </p>
                    <p>
                        <label for="tipo_propuesta" class="label">Tipo propuesta:</label>
                        <select name="tipo_propuesta" id="tipo_propuesta">
                        </select>
                        <span id="aviso_Tipo_Propuesta" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="titulo_prop" class="label">Título:</label>
                        <textarea type="text" name="titulo_prop" id="titulo_prop" value=''
                            style="max-height: 2em; max-width: 510px;" maxlength="500" placeholder="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"
                            title="Capture únicamente letras, números y los caráctres , ; . : ¿? ( ) - # _" class="entrada_Dato" autocomplete="off"></textarea>
                        <span id="aviso_Titulo_Propuesta" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="aceptar_inscripciones" class="label" style="color: white; background-color: red;">Aceptar Inscripciones de Alumnos ?</label>
                        <input type="checkbox" style="height:40px; width:40px; cursor: pointer;" class="" name="aceptar_inscripciones" id="aceptar_inscripciones" />
                    </p>

                    <div id='div_Horarios_Asesoria' style="padding-top: 10px; width: 600px; margin-left: 40px;">
                        <table id="tblHorarios_Asesoria" class="tabla_Registros" style="text-align: center;border: 1px gray solid;">
                            <caption style="text-align: center;">Horarios de Asesoría para esta Propuesta</caption>
                            <thead>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                        <span id="aviso_Horarios" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>

                    </div>
                    <br>
                    <button type="button" id="btn_Cancelar" class="ui-button ui-corner-all ui-widget nexttab1">Siguiente</button>
                </div>

                <div id="tabs-2">
                    <div>
                        <div>
                            <label for="carrera"><b>Carrera:</b></label>
                            <select name="carrera" id="carrera" style="width: 250px;">
                            </select>
                            <label for="num_alumnos"><b>No. Alumnos</b>:</span>
                                <input type="number" min="1" max="5" name="num_alumnos" id="num_alumnos" value='1'
                                    maxlength="1" autocomplete="off" style="width:50px;" class="entrada_Dato" />
                                <input type="button" id='btn_Agregar_Requerimiento' name="btn_Agregar_Requerimiento" value='Agregar'
                                    class='btn_Herramientas' style="margin-left: 20px; width:80px;" />
                        </div>
                    </div>
                    <div id='div_requemientos' style="padding-top: 20px;">
                        <table id="tblRequerimientos" class="tabla_Registros" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th>Carrera</th>
                                    <th>Alumnos</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <span id="aviso_Requerimientos" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>

                    </div>
                    <button type="button" id="btn_Anterior" class="ui-button ui-corner-all ui-widget prevtab2">Anterior</button>
                    <button type="button" id="btn_Siguiente" class="ui-button ui-corner-all ui-widget nexttab2">Siguiente</button>
                </div>

                <div id="tabs-3">
                    <div>
                        <p>
                            <label for="organismos_colaboradores" class="label">Organismos Colaboradores:</label>
                            <textarea name="organismos_colaboradores" id="organismos_colaboradores"
                                value='' cols="50" rows="20"
                                maxlength="500" placeholder="" style="resize:none;max-height: 20em; height: 20em;text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"
                                title="Capture únicamente letras, números y los caráctres , ; . : ¿? ( ) - # _" class="entrada_Dato" autocomplete="off" />
                            </textarea>
                            <span id="aviso_Organismos" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                        </p>
                    </div>
                    <button type="button" id="btn_Anterior" class="ui-button ui-corner-all ui-widget prevtab3">Anterior</button>
                    <button type="button" id="btn_Guardar" class="ui-button ui-corner-all ui-widget btnGuardar">Guardar</button>
                </div>
            </div> <!-- fin tabs -->
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
            <input type="hidden" id="id_Tipo_Usuario" name="id_Tipo_Usuario" value="4">
            <input type="hidden" id="id_division" name="id_division" value="<?php echo $_SESSION['id_division']; ?>">
            <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="">
            <input type="hidden" id="Carrera_Alumnos" name="Carrera_Alumnos" value="">
            <input type="hidden" id="De_Alta_OK" name="De_Alta_OK" value="">
            <input type="hidden" id="Id_Propuesta" name="De_Alta_OK" value="">
            <input type="hidden" id="Id_Estatus_Propuesta" name="Id_Estatus_Propuesta" value="0">
            <input type="hidden" id="n_Alumnos" name="n_Alumnos" value="0">
            <input type="hidden" id="Mis_Horarios" name="Mis_Horarios" value="0">
        </form>
    </div>

    <div id='ventanaDocumentosEnviados'>
        <div id="tabla_Mis_Docs" class="tabla_Registros">
        </div>
    </div>

    <div id="ventanaSubirArchivo">
        <div id="contenido_Subir_Archivo" style="padding-top:15px;">
            <form action="" method="post" enctype="multipart/form-data" id="frmSubirPDF" name="frmSubirPDF">
                <p>
                    <label data-id_ss='x' data-id_documento="0" data-id_version="0" id='lblTitulo_SubirArchivo' for="archivoPDF"></label>
                <div style="padding-top:15px;">
                    <input type="file" name="file" id="file" accept=".pdf" required class="tag_file">
                    <input type="submit" name="enviarArchivo" id="enviarArchivo" value="Enviar" class="btn_Herramientas">
                    <input type="hidden" id="id_propuesta_prof" name="id_propuesta_prof" value="">
                    <input type="hidden" id="id_documento_prof" name="id_documento_prof" value="">
                    <input type="hidden" id="id_version_prof" name="id_version_prof" value="">
                    <input type="hidden" id="id_usuario_prof" name="id_usuario_prof" value="">
                    <input type="hidden" id="desc_corta_prof" name="desc_corta_prof" value="">
                </div>
                </p>
            </form>
            <br>
            <div id='loading' class="resultado_Carga_De_Archivo">
                <h1>Cargando el Archivo...</h1>
            </div>

            <div id="message" class="informacion_Archivo_A_Cargar"></div>

        </div>
    </div>

    <div id="ventanaConfirmacion">

    </div>
    <div id="ventanaEnviar">

    </div>
    <div id='notas_VoBo'>

    </div>

    <div id="ventanaConfirmarBorrado_Propuesta">
        <span style="font-weight: bold; margin-top: 5px">Debe de indicar los Motivos por los que desea dar de baja esta Propuesta.</span>
        <textarea id="nota" class="notaVoBo entrada_Dato" style="margin-top: 5px;"
            maxlength="500" placeholder="" onkeyup="javascript:this.value=this.value.toUpperCase();"
            title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
    </div>
    <div id="ventanaConfirmacionBorrar">
        <span id="MensajeConfirmaBorrar"></span>
    </div>


    <div id="ventanaAvisos">
        <span id="ventanaAviso"></span>
    </div>
    <div id="ventanaAvisos_Alta_OK">
        <span id="ventanaAviso_Alta_OK"></span>
    </div>

    <div id="ventanaProcesando" data-role="header">
        <img id="cargador" src="./assets/images/ui/engrane2.gif" /><br>
        Procesando su transacción....!<br>
        Espere por favor.
    </div>

    <!--    </body>
</html>-->
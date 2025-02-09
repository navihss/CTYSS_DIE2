<!DOCTYPE html>
<!--
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para la inscripción a Titulación por Ceremonia
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
            //OBTENEMOS LAS CARRERAS EN LAS QUE ESTA INSCRITO EL ALUMNO PARA EL LISTBOX
            function Obtener_Carreras_Del_Alumno() {
                var datos = {
                    Tipo_Movimiento: 'OBTENER',
                    Id_Usuario: $('#Id_Usuario').val(),
                    Id_Carrera: 0
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_Alumno_Carrera.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_options = '';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                //recorremos los valores de cada usuario
                                html_options = html_options + '<option value=' + value['id_carrera'] +
                                    '>' + value['descripcion_carrera'] + '</option>';
                            });
                            $('#selec_Mis_Carreras').empty();
                            $('#selec_Mis_Carreras').html(html_options);

                            $('#selec_Mis_Carreras option:first-child').attr('selected', 'selected');
                            $('#Id_Carrera').val($('#selec_Mis_Carreras').val());
                            Obtener_Mis_Ceremonias($('#Id_Usuario').val(), $('#Id_Carrera').val());
                        } else {
                            $('#Agregar_Servicio').prop('disable', true);
                            $('#ventanaAviso').html(respuesta.data.message);
                            $('#ventanaAvisos').dialog('open');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisos').dialog('open');
                    });

            } //FIN OBTENEMOS LAS CARRERAS EN LAS QUE ESTA INSCRITO EL ALUMNO

            $('#selec_Mis_Carreras').change(function(e) {
                e.preventDefault();
                var id_carrera_sel = $(this).val();
                $('#Id_Carrera').val(id_carrera_sel);
                Obtener_Mis_Ceremonias($('#Id_Usuario').val(), id_carrera_sel);
            });

            //OBTENEMOS LAS INSCRIPCIONES A CEREMONIA 
            function Obtener_Mis_Ceremonias(id_alumno, id_carrera) {
                var deshabilitaAgregar = false;
                var datos = {
                    Tipo_Movimiento: 'OBTENER_MIS_CEREMONIAS',
                    id_alumno: id_alumno,
                    id_carrera: id_carrera
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_Alumno_Mi_Ceremonia.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:100%;">';
                        html_table += '<TR><TH>Id Ceremonia</TH>\n\
                                        <TH>Tipo de Ceremonia</TH>\n\
                                        <TH>Fecha de Alta</TH>\n\
                                        <TH>Estatus</TH>\n\
                                        <TH>Motivo de Baja</TH>\n\
                                        <TH>Acción</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {

                                console.log(value['id_estatus']);
                                if (value['id_estatus'] != 4 && value['id_estatus'] != 13 && value['id_estatus'] != 14 && value['id_estatus'] != 15) {
                                    deshabilitaAgregar = true;
                                }
                                var btn_EnviarDoc = '';
                                var btn_BorrarDoc = '';
                                var btn_EditarDoc = '';
                                btn_EnviarDoc = '<button class="btn_EnviarDoc btnOpcion" data-id_alumno=\'' + value['id_alumno'] + '\' ' +
                                    'data-id_carrera=\'' + value['id_carrera'] +
                                    '\' data-id_ceremonia=\'' + value['id_ceremonia'] +
                                    '\' data-desc_ceremonia=\'' + value['descripcion_tipo_propuesta'] +
                                    '\' data-id_tipo_propuesta=' + value['id_tipo_propuesta'] +
                                    ' data-id_estatus = ' + value['id_estatus'] + '>Enviar Docs</button>';
                                if (value['id_estatus'] == 2 || value['id_estatus'] == 3) { //2. Por Autorizar Admin
                                    btn_BorrarDoc = '<button class="btn_Borrar_Ceremonia btnOpcion" data-id_alumno=\'' + value['id_alumno'] + '\' ' +
                                        'data-id_carrera=\'' + value['id_carrera'] +
                                        '\' data-id_ceremonia=\'' + value['id_ceremonia'] +
                                        '\' data-desc_ceremonia=\'' + value['descripcion_tipo_propuesta'] +
                                        '\' data-id_tipo_propuesta=' + value['id_tipo_propuesta'] +
                                        ' data-id_estatus = ' + value['id_estatus'] + '>Borrar</button>';
                                }
                                btn_EditarDoc = '<button class="btn_EditarDoc btnOpcion" data-id_alumno=\'' + value['id_alumno'] + '\' ' +
                                    'data-id_carrera=\'' + value['id_carrera'] +
                                    '\' data-id_ceremonia=\'' + value['id_ceremonia'] +
                                    '\' data-desc_ceremonia=\'' + value['descripcion_tipo_propuesta'] +
                                    '\' data-id_tipo_propuesta=' + value['id_tipo_propuesta'] +
                                    ' data-id_estatus = ' + value['id_estatus'] + '>Editar</button>';

                                html_table += '<TR>';
                                html_table += '<TD>' + value['id_ceremonia'] + '</TD>';
                                html_table += '<TD>' + value['descripcion_tipo_propuesta'] + '</TD>';
                                html_table += '<TD style="text-align:left;">' + value['fecha_alta'] + '</TD>';
                                html_table += '<TD>' + value['descripcion_estatus'] + '</TD>';
                                html_table += '<TD>' + esNulo(value['nota_baja']) + '</TD>';
                                html_table += '<TD style="text-align:right;">' + btn_EditarDoc + btn_EnviarDoc + btn_BorrarDoc + '</TD>';
                                html_table = html_table + '</TR>';
                                if (value['id_estatus'] != 7 && value['id_estatus'] != 6 && value['id_estatus'] != 13 && value['id_estatus'] != 14 && value['id_estatus'] != 15 && !value['llenofechaestimada']) {
                                    var id_alumno = value['id_alumno'];
                                    var id_ceremonia = value['id_ceremonia'];
                                    var tipo = value['descripcion_tipo_propuesta'];
                                    var condicion = value['llenofechaestimada'];
                                    console.log(id_alumno);
                                    console.log(id_ceremonia);
                                    console.log(tipo);
                                    console.log(condicion);
                                    /*
                                    $('#fechaEstimadaTitulacionMotivo').dialog({
                                      buttons:{
                                          "Confirmar datos" : function() {
                                              if(!$('#fechaEstimada').val().match(miExpReg_Fecha) || !$('#motivoTitulacion').val().match(miExpReg_Nota_Rechazo)){
                                                $('#ventanaAviso').html('No puede dejar estos campos vacíos. Sólo debe usar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #.');
                                                $('#ventanaAvisos').dialog('open');
                                              } else {
                                                
                                                var fechaEstimada = $('#fechaEstimada').val();
                                                var motivoTitulacion = $('#motivoTitulacion').val();

                                                var today =  $.datepicker.formatDate("dd/mm/yyyy", new Date());

                                                if(fechaEstimada < today) {
                                                  $('#ventanaAviso').html('Sólo puede seleccionar fechas futuras.');
                                                  $('#ventanaAvisos').dialog('open');
                                                  return;
                                                }

                                                $(this).dialog('close');

                                                var datosTitulacion = {Tipo_Movimiento : 'ACTUALIZAR_FECHA_ESTIMADA', 
                                                id_ceremonia : id_ceremonia, 
                                                fechaEstimada : fechaEstimada, 
                                                motivoTitulacion : motivoTitulacion
                                                }; 

                                                $.ajax({
                                                  data: datosTitulacion,
                                                  type: "POST",
                                                  dataType: "json",
                                                  url: "_Negocio/n_Alumno_Mi_Ceremonia.php"
                                                }).done(function(respuesta,textStatus,jqXHR){
                                                   $('#ventanaAviso').html('Información capturada correctamente.');
                                                  $('#ventanaAvisos').dialog('open');
                                                }).fail(function(respuesta,textStatus,errorThrown){
                                                  $('#ventanaAviso').html('Ocurrió un error al procesar la información. ' + errorThrown + respuesta + textStatus);
                                                $('#ventanaAvisos').dialog('open');
                                                });
                                              }
                                          }                     
                                     },
                                     title: 'Requerimos tu atención',
                                     modal : true,
                                     autoOpen : true,
                                     resizable : true,
                                     draggable : true,
                                     width : '500',
                                     height : '230',
                                     position : {at: 'center'},
                                     dialogClass : 'no-close',
                                     show : 'slide',
                                     hide : 'slide',
                                     closeOnEscape : false,
                                     open : function(e) {
                                      e.preventDefault();
                                       var customHTML = "<p>Para tu ceremonia con número de registro <b>" + id_ceremonia + "</b> por <b>" + tipo + "</b> requerimos que llenes la siguiente información: <br>";
                                       $('#mensajeFechaEstimadaTitulacion').html(customHTML);
                                       $.datepicker.regional['es'] = {
                                          closeText: 'Cerrar',  
                                          prevText: 'Previo',  
                                          nextText: 'Próximo', 
                                          monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',                           
                                                      'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'], 
                                          monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun', 
                                          'Jul','Ago','Sep','Oct','Nov','Dic'], 
                                          monthStatus: 'Ver otro mes', yearStatus: 'Ver otro año', 
                                          dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'], 
                                          dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sáb'], 
                                          dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'], 
                                          dateFormat: 'dd/mm/yy', firstDay: 0,  
                                          initStatus: 'Selecciona la fecha', isRTL: false
                                         };

                                         $.datepicker.setDefaults($.datepicker.regional['es']);
                                         
                                         $('#fechaEstimada').datepicker({
                                              changeYear : true,
                                              changeMonth : true,
                                              yearRange : '1920:2050',
                                              onSelect : function(date){
                                                  $("#fechaEstimada ~ .ui-datepicker").hide();
                                              }

                                         });
                                     }
                                  }); */
                                }
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Mis_Ceremonias').empty();
                            $('#tabla_Mis_Ceremonias').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD colspan="6">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Mis_Ceremonias').empty();
                            $('#tabla_Mis_Ceremonias').html(html_table);
                        }

                        if (deshabilitaAgregar) {
                            $('#btn_Agregar_Ceremonia').attr("disabled", true);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table += '<TR><TH>Id Ceremonia</TH>\n\
                                             <TH>Tipo de Ceremonia</TH>\n\
                                             <TH>Fecha de Alta</TH>\n\
                                             <TH>Estatus</TH>\n\
                                             <TH>Motivo de Baja</TH>\n\
                                             <TH>Acción</TH></TR>';
                        html_table = html_table + '<TR><TD colspan="6">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Mis_Ceremonias').empty();
                        $('#tabla_Mis_Ceremonias').html(html_table);
                    });
            }
            //FIN OBTENEMOS LAS CEREMONIAS

            //Mostramos TODAS las Ceremonias MENOS las que ya tiene agregadas
            $('#btn_Agregar_Ceremonia').on('click', function(event) {
                event.preventDefault();
                var datos = {
                    Tipo_Movimiento: 'SELECCIONAR_MODALIDAD',
                    id_alumno: $('#Id_Usuario').val(),
                    id_carrera: $('#Id_Carrera').val()
                }
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_Alumno_Mi_Ceremonia.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_NewRow = '<TABLE class="tabla_Registros">';
                        html_NewRow = html_NewRow + '<TR><TH>Modalidad de Ceremonia</TH><TH>Seleccionada</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                html_NewRow += '<TR>';
                                html_NewRow += '<TD style="text-align:left;">' + value['descripcion_tipo_propuesta'] + '</TD>';
                                html_NewRow += '<TD style="text-align:center;">\n\
                                                    <input name="modalidad" id="modalidad" type="radio" \n\
                                                    value="' + value['id_tipo_propuesta'] +
                                    '" data-descripcion_modalidad = \'' + value['descripcion_tipo_propuesta'] + '\'</TD>';
                                html_NewRow += '</TR>';
                            });
                            html_NewRow = html_NewRow + '</TABLE>';
                            $('#tabla_Seleccionar_Ceremonia').empty();
                            $('#tabla_Seleccionar_Ceremonia').html(html_NewRow);
                        } else {
                            html_NewRow = html_NewRow + '<TR><TD style="text-align:center;" colspan="2">' + respuesta.data.message + '</TD></TR></TABLE>';

                            $('#tabla_Seleccionar_Ceremonia').empty();
                            $('#tabla_Seleccionar_Ceremonia').html(html_NewRow);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_NewRow = '<TABLE class="tabla_Registros">';
                        html_NewRow = html_NewRow + '<TR><<TH>Modalidad de Ceremonia</TH><TH>Seleccionada</TH></TR>';
                        html_NewRow = html_NewRow + '<TR><TD style="text-align:center;" colspan="2">' + textStatus + '. ' + errorThrown + '</TD></TR></TABLE>';

                        $('#tabla_Seleccionar_Ceremonia').empty();
                        $('#tabla_Seleccionar_Ceremonia').html(html_NewRow);
                    });

                $('#ventanaSeleccionarCeremonia').dialog('open');
            });
            //FIN Mostramos TODAS las modalidades MENOS las que ya tiene agregadas en su carrera

            $('#ventanaSeleccionarCeremonia').dialog({
                buttons: {
                    "Agregar": function() {
                        var modalidad_Seleccionada = $('input:radio[name=modalidad]:checked').data('descripcion_modalidad');
                        if (!modalidad_Seleccionada) {
                            $('#ventanaAviso').html('Debe Seleccionar la Modalidad deseada.');
                            $('#ventanaAvisos').dialog('open');
                        } else {
                            var mensaje = "Desea agregar la Modalidad de Ceremonia " + modalidad_Seleccionada + " ?";
                            $('#ventanaConfirmacionAgregarCeremonia').html(mensaje);
                            $('#ventanaConfirmacionAgregarCeremonia').dialog('open');
                        }
                    },
                    "Cancelar": function() {
                        $(this).dialog('close');
                    }
                },
                title: 'Mi Ceremonia de Titulación',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: true,
                width: '400',
                height: '600',
                position: {
                    at: 'center top'
                },
                dialogClass: 'no-close',
                show: 'slide',
                hide: 'slide',
                closeOnEscape: false
            });

            $('#ventanaConfirmacionAgregarCeremonia').dialog({
                buttons: {
                    "Aceptar": function() {
                        $(this).dialog('close');
                        // Por Ajax insertamos la Modalidad Docs Requeridos
                        var id_usr = $('#Id_Usuario').val();
                        var id_carrera = $('#Id_Carrera').val();

                        var id_modalidad_Seleccionada = $('input:radio[name=modalidad]:checked').val();
                        var desc_modalidad = $('input:radio[name=modalidad]:checked').data('descripcion_modalidad');

                        $.ajax({
                                data: {
                                    Tipo_Movimiento: 'AGREGAR',
                                    id_alumno: id_usr,
                                    id_carrera: id_carrera,
                                    id_modalidad: id_modalidad_Seleccionada,
                                    desc_modalidad: desc_modalidad
                                },
                                type: "POST",
                                dataType: "json",
                                url: "_Negocio/n_Alumno_Mi_Ceremonia.php"
                            })
                            .done(function(respuesta, textStatus, jqXHR) {
                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html(respuesta.data.message);
                                $('#ventanaAvisos').dialog('open');
                                Obtener_Mis_Ceremonias($('#Id_Usuario').val(), $('#Id_Carrera').val());
                                //                                    Obtener_Carreras_Del_Alumno();                                        
                                $('#ventanaConfirmacionAgregarCeremonia').dialog('close');
                                $('#ventanaSeleccionarCeremonia').dialog('close');

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
                title: 'Mi Ceremonia de Titulación',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: true,
                dialogClass: 'no-close ventanaConfirmaUsuario',
                closeOnEscape: false
            });

            //BORRAMOS LA CEREMONIA
            $('#tabla_Mis_Ceremonias').on("click", "button.btn_Borrar_Ceremonia", function(e) {
                e.preventDefault();

                var id_ceremonia = $(this).data("id_ceremonia");
                var id_carrera = $(this).data("id_carrera");
                var id_estatus = $(this).data("id_estatus");
                var id_alumno = $(this).data("id_alumno");
                var descripcion_ceremonia = $(this).data("desc_ceremonia");

                if (parseInt(id_estatus) == 2) { //2. Por Aut Admin
                    $('#ventanaConfirmarBorrado_Ceremonia').dialog({
                        buttons: {
                            "Aceptar": function() {
                                if (!$('#nota').val().match(miExpReg_Nota_Rechazo)) {
                                    $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #');
                                    $('#ventanaAvisos').dialog('open');
                                } else {
                                    $('#MensajeConfirmaBorrar').text('Desea dar de Baja esta Ceremonia ?');
                                    $('#ventanaConfirmacionBorrar').dialog({
                                        buttons: {
                                            "Aceptar": function() {
                                                $(this).dialog('close');
                                                $('#ventanaProcesando').dialog('open');

                                                // Por Ajax damos de baja la Ceremonia
                                                $.ajax({
                                                        data: {
                                                            Tipo_Movimiento: 'BORRAR_CEREMONIA',
                                                            id_ceremonia: id_ceremonia,
                                                            id_carrera: id_carrera,
                                                            id_alumno: id_alumno,
                                                            descripcion_ceremonia: descripcion_ceremonia,
                                                            nota: $('#nota').val()
                                                        },
                                                        type: "POST",
                                                        dataType: "json",
                                                        url: "_Negocio/n_Alumno_Mi_Ceremonia.php"
                                                    })
                                                    .done(function(respuesta, textStatus, jqXHR) {
                                                        $('#ventanaProcesando').dialog('close');
                                                        if (respuesta.success == true) {
                                                            $('#ventanaConfirmacionBorrar').dialog('close');
                                                            $('#nota').val('');
                                                            Obtener_Mis_Ceremonias($('#Id_Usuario').val(), $('#Id_Carrera').val());
                                                            $('#ventanaConfirmarBorrado_Ceremonia').dialog('close');
                                                        }
                                                        $('#ventanaAviso').html(respuesta.data.message);
                                                        $('#ventanaAvisos').dialog('open');
                                                        $('#btn_Agregar_Ceremonia').attr("disabled", false);

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
                                        title: 'Confirmar la Baja de la Ceremonia',
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
                        title: 'Baja de Ceremonia',
                        modal: true,
                        autoOpen: true,
                        resizable: true,
                        draggable: true,
                        height: 'auto',
                        width: 450,
                        dialogClass: 'no-close',
                        closeOnEscape: false
                    });
                } else if (parseInt(id_estatus) == 3) { //Aceptada
                    $('#Tipo_Movimiento').val("SOLICITAR_BAJA_CEREMONIA");

                    var tituloAdjuntar = 'Para la Ceremonia <b>' + $(this).data('id_ceremonia') +
                        '</b>. Seleccione su archivo <b>' + 'Solicitud de Baja de Ceremonia' +
                        ',</b> en formato PDF, INDICANDO LOS MOTIVOS por los que Solicita la Baja. <b>(Tamaño máximo 500MB)</b><br><br>';
                    $('#lblTitulo_SubirArchivo').html(tituloAdjuntar);
                    $('#id_ceremonia_doc').attr("value", id_ceremonia);
                    $('#id_carrera_doc').attr("value", id_carrera);
                    $('#id_usuario_doc').attr("value", id_alumno);
                    $('#desc_tipo_propuesta').attr("value", descripcion_ceremonia);
                    $('#desc_corta_doc').attr("value", 'Solicitud_Baja_Ceremonia');
                    $('#docs_de_envio').attr("value", 'SOLICITUD_BAJA_CEREMONIA');
                    $('#ventanaSubirArchivo').dialog('open');

                }

            });

            //EDITAMOS LA CEREMONIA
            $('#tabla_Mis_Ceremonias').on("click", "button.btn_EditarDoc", function(e) {
                e.preventDefault();
                var id_tipo_propuesta = $(this).data('id_tipo_propuesta');
                var id_ceremonia = $(this).data('id_ceremonia');
                var desc_propuesta = $(this).data('desc_ceremonia');
                $('#Tipo_Movimiento').val('ACTUALIZAR');
                $('#id_tipo_propuesta').val(id_tipo_propuesta);
                $('#Id_Ceremonia').val(id_ceremonia);
                $('#Desc_Propuesta').val(desc_propuesta);

                $('#ventanaDatosCeremonia').dialog({
                    buttons: {
                        "Actualizar": function() {
                            if (validaDatos()) {
                                $('#ventanaConfirmacionActualizacion').dialog({
                                    buttons: {
                                        "Aceptar": function() {
                                            $(this).dialog('close');
                                            $('#ventanaProcesando').dialog('open');
                                            var formDatos = $('#datos_Ceremonia').serialize();
                                            $.ajax({
                                                    data: formDatos,
                                                    type: "POST",
                                                    dataType: "json",
                                                    url: "_Negocio/n_Alumno_Mi_Ceremonia.php"
                                                })
                                                .done(function(respuesta, textStatus, jqXHR) {
                                                    $('#ventanaProcesando').dialog('close');
                                                    if (respuesta.success == true) {
                                                        Obtener_Mis_Ceremonias($('#Id_Usuario').val(), $('#Id_Carrera').val());
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
                                    title: 'Mi Ceremonia',
                                    modal: true,
                                    autoOpen: true,
                                    resizable: true,
                                    draggable: true,
                                    dialogClass: 'no-close ventanaConfirmaUsuario',
                                    closeOnEscape: false
                                });
                            }
                        },
                        "Cerrar": function() {
                            $('input:text, textarea').val('');
                            $('#datos_Ceremonia span').hide();
                            $(this).dialog('close');
                        }
                    },
                    open: function() {
                        $('.camposTexto').hide();
                        //                           Obtenemos la infor de la Ceremonia
                        var datos = {
                            Tipo_Movimiento: 'OBTENER_CEREMONIA',
                            id_ceremonia: id_ceremonia
                        };
                        $.ajax({
                                data: datos,
                                type: "POST",
                                dataType: "json",
                                url: "_Negocio/n_Alumno_Mi_Ceremonia.php"
                            })
                            .done(function(respuesta, textStatus, jqXHR) {
                                $('#ventanaProcesando').dialog('close');
                                if (respuesta.success == true) {
                                    $.each(respuesta.data.registros, function(key, value) {
                                        $('#sedes').val(value['sedes']);
                                        $('#diplomados_cursos').val(value['diplomados_cursos']);
                                        $('#materias').val(value['materias']);
                                        $('#programa_posgrado').val(value['programa_posgrado']);
                                        $('#titulo_articulo').val(value['nombre_articulo']);
                                        $('#nombre_Revista').val(value['nombre_revista']);
                                        $('#desc_propuesta').text(value['descripcion_tipo_propuesta']);
                                    });
                                } else {
                                    $('#ventanaAviso').html(respuesta.data.message);
                                    $('#ventanaAvisos').dialog('open');
                                }
                            })
                            .fail(function(jqXHR, textStatus, errorThrown) {
                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                $('#ventanaAvisos').dialog('open');
                                $(this).dialog('close');
                            });
                        //Fin Obtenemos la info dela ceremonia
                        switch (id_tipo_propuesta) {
                            case 7:
                                $('#div_Articulo').show();
                                $('#div_Revista').show();
                                break;
                            case 8:
                                $('#div_Sede').show();
                                $('#div_Materias').show();
                                break;
                            case 9:
                                $('#div_Sede').show();
                                $('#div_Diplomado').show();
                                break;
                            case 10:
                                $('#div_Sede').show();
                                $('#div_Progama').show();
                                break;
                            case 11:
                            case 12:
                                $('#ventanaAviso').html('Para esta Modalidad SOLO envíe los archivos correspondientes.');
                                $('#ventanaAvisos').dialog('open');
                                $(this).dialog('close');
                                break;
                        }
                    },
                    title: 'Mi Ceremonia',
                    modal: true,
                    autoOpen: true,
                    resizable: true,
                    draggable: true,
                    width: '700',
                    height: 'auto',
                    position: {
                        at: 'center top'
                    },
                    dialogClass: 'no-close',
                    show: 'slide',
                    hide: 'slide',
                    closeOnEscape: false
                });
            });

            function validaDatos() {
                //                    $('#Actualizar_Mi_Pefil_Alumno').prop('disable',true);
                var datosValidos = true;
                var sedes = $('#sedes').val();
                var diplomados_cursos = $('#diplomados_cursos').val();
                var materias = $('#materias').val();

                var programa_posgrado = $('#programa_posgrado').val();
                var titulo_articulo = $('#titulo_articulo').val();
                var nombre_Revista = $('#nombre_Revista').val();

                $('#statusSede').hide();
                $('#statusDiplomados').hide();
                $('#statusMaterias').hide();
                $('#statusPrograma').hide();
                $('#statusArticulo').hide();
                $('#statusRevista').hide();

                //                    var miExpReg_Texto1 = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9\.\,\;\:\?\¿\(\)\-\_\#\n]{1,500}$/;
                //                    var miExpReg_Texto2= /^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9\.\,\;\:\?\¿\(\)\-\_\#\n]{1,100}$/;
                switch ($('#id_tipo_propuesta').val()) {
                    case '7':
                        if (!titulo_articulo.match(miExpReg_Direccion)) {
                            $('#statusArticulo').show();
                            datosValidos = false;
                        } else {
                            $('#statusArticulo').hide();
                        }

                        if (!nombre_Revista.match(miExpReg_Direccion)) {
                            $('#statusRevista').show();
                            datosValidos = false;
                        } else {
                            $('#statusRevista').hide();
                        }
                        break;
                    case '8':
                        if (!sedes.match(miExpReg_Nota_Rechazo)) {
                            $('#statusSede').show();
                            datosValidos = false;
                        } else {
                            $('#statusSede').hide();
                        }
                        if (!materias.match(miExpReg_Nota_Rechazo)) {
                            $('#statusMaterias').show();
                            datosValidos = false;
                        } else {
                            $('#statusMaterias').hide();
                        }
                        break;
                    case '9':
                        if (!sedes.match(miExpReg_Nota_Rechazo)) {
                            $('#statusSede').show();
                            datosValidos = false;
                        } else {
                            $('#statusSede').hide();
                        }
                        if (!diplomados_cursos.match(miExpReg_Nota_Rechazo)) {
                            $('#statusDiplomados').show();
                            datosValidos = false;
                        } else {
                            $('#statusDiplomados').hide();
                        }
                        break;
                    case '10':
                        if (!sedes.match(miExpReg_Nota_Rechazo)) {
                            $('#statusSede').show();
                            datosValidos = false;
                        } else {
                            $('#statusSede').hide();
                        }
                        if (!programa_posgrado.match(miExpReg_Direccion)) {
                            $('#statusPrograma').show();
                            datosValidos = false;
                        } else {
                            $('#statusPrograma').hide();
                        }
                        break;
                    case '11':
                    case '12':
                        $('#ventanaAviso').html('Para esta Modalidad SOLO envíe los archivos correspondientes.');
                        $('#ventanaAvisos').dialog('open');
                        $(this).dialog('close');
                        break;
                }
                //$('#Actualizar_Mi_Pefil_Alumno').prop('disable',false);
                return datosValidos;
            };

            $('#tabla_Mis_Ceremonias').on("click", "button.btn_EnviarDoc", function(e) {
                e.preventDefault();
                $('#Id_Ceremonia').val($(this).data("id_ceremonia"));
                $('#desc_ceremonia').val($(this).data("desc_ceremonia"));
                var id_ceremonia = $(this).data("id_ceremonia");
                Obtener_Mis_Documentos_Enviados(id_ceremonia, $(this).data("desc_ceremonia"));
                $('#ventanaDocumentosEnviados_Ceremonia').dialog('open');
                $('#docs_de_envio').attr("value", 'CEREMONIA');
            });

            function Obtener_Mis_Documentos_Enviados(id_ceremonia, desc_ceremonia) {
                $('#desc_tipo_propuesta').val(desc_ceremonia);
                var datos = {
                    Tipo_Movimiento: 'OBTENER_MIS_DOCUMENTOS',
                    id_ceremonia: id_ceremonia
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_Alumno_Mi_Ceremonia.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {

                        var html_table = '<TABLE class="tabla_Registros"><CAPTION> Ceremonia: ' + id_ceremonia + ' * ' + desc_ceremonia + '</CAPTION>';
                        html_table += '<TR>\n\
                                          <TH style="text-align:center;">Documento</TH>\n\
                                          <TH style="text-align:center;">Versión</TH>\n\
                                          <TH style="text-align:center;">Fecha Enviado</TH>\n\
                                          <TH style="text-align:center;">Estatus</TH>\n\
                                          <TH style="text-align:center;">Nota Administración</TH>\n\
                                          <TH style="text-align:center;">Nota Coordinación</TH>\n\
                                          <TH style="text-align:center;">Acción</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                var $btn_EnviarDocs = '';

                                if (value['id_estatus'] == 1) {
                                    $btn_EnviarDocs = '<button class="btn_Adjuntar btnOpcion" \n\
                                            data-id_alumno =\'' + value['id_alumno'] +
                                        '\' data-id_documento=' + value['id_documento'] +
                                        ' data-id_ceremonia=\'' + value['id_ceremonia'] +
                                        '\' data-id_carrera=\'' + value['id_carrera'] +
                                        '\' data-id_estatus=' + value['id_estatus'] +
                                        ' data-desc_documento=\'' + value['descripcion_documento'] +
                                        '\' data-desc_documento_corta = \'' + value['descripcion_para_nom_archivo'] +
                                        '\' data-version=' + value['version'] +
                                        '>Adjuntar</button>';
                                }
                                nom_file = value['id_alumno'] + '_' +
                                    value['id_carrera'] + '_' +
                                    value['id_ceremonia'] + '_' +
                                    value['version'] + '_' +
                                    value['descripcion_para_nom_archivo'] + '.pdf';
                                fecha_ = new Date();
                                ruta_doc = ruta_docs_ceremonias + nom_file + '?' + fecha_;

                                html_table = html_table + '<TR>';
                                if (value['id_estatus'] == 2 || value['id_estatus'] == 3) {
                                    html_table = html_table + '<TD style="text-align:left;"><a class="link_pdf" target="_blank" href="' +
                                        ruta_doc + '">' + value['descripcion_documento'] + '</a></TD>';
                                } else {
                                    html_table = html_table + '<TD style="text-align:left;">' + value['descripcion_documento'] + '</TD>';

                                }

                                //                                   html_table = html_table + '<TD style ="text-align:left;">' + value['descripcion_documento'] + '</TD>';
                                html_table = html_table + '<TD style="text-align:center;">' + value['version'] + '</TD>';
                                html_table = html_table + '<TD style="text-align:left;">' + esNulo(value['fecha_recepcion']) + '</TD>';
                                html_table = html_table + '<TD>' + value['descripcion_estatus'] + '</TD>';
                                html_table = html_table + '<TD>' + esNulo(value['nota_admin']) + '</TD>';
                                html_table = html_table + '<TD>' + esNulo(value['nota_coordinador']) + '</TD>';
                                html_table = html_table + '<TD>' + $btn_EnviarDocs + '</TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Mis_Doc_Ceremonia').empty();
                            $('#tabla_Mis_Doc_Ceremonia').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD colspan="7">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Mis_Doc_Ceremonia').empty();
                            $('#tabla_Mis_Doc_Ceremonia').html(html_table);
                        }

                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros"><CAPTION> Id_Ceremonia: ' + desc_ceremonia + '</CAPTION>';
                        html_table += '<TR>\n\
                                               <TH>Documento</TH>\n\
                                               <TH>Versión</TH>\n\
                                               <TH>Fecha Enviado</TH>\n\
                                               <TH>Estatus</TH>\n\
                                               <TH>Nota Administración</TH>\n\
                                               <TH>Nota Coordinación</TH>\n\
                                               <TH>Acción</TH></TR>';
                        html_table = html_table + '<TR><TD colspan="7">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Mis_Doc_Ceremonia').empty();
                        $('#tabla_Mis_Doc_Ceremonia').html(html_table);
                    });
            }

            $('#ventanaDocumentosEnviados_Ceremonia').dialog({
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
                width: '1000',
                show: 'slide',
                hide: 'slide',
                position: {
                    at: 'center top'
                },
                dialogClass: 'no-close',
                closeOnEscape: false
            });

            $('#tabla_Mis_Doc_Ceremonia').on('click', "button.btn_Adjuntar", function(e) {
                e.preventDefault();
                var tituloAdjuntar = 'Para la Ceremonia <b>' + $(this).data('id_ceremonia') +
                    ', Versión ' + $(this).data('version') +
                    '</b>. Seleccione su archivo <b>' + $(this).data('desc_documento') +
                    ',</b> en formato PDF. <b>(Tamaño máximo 500MB)</b><br><br>';
                $('#lblTitulo_SubirArchivo').html(tituloAdjuntar);
                $('#id_ceremonia_doc').attr("value", $(this).data('id_ceremonia'));
                $('#id_documento_doc').attr("value", $(this).data('id_documento'));
                $('#version_doc').attr("value", $(this).data('version'));
                $('#id_usuario_doc').attr("value", $(this).data('id_alumno'));
                $('#desc_corta_doc').attr("value", $(this).data('desc_documento_corta'));
                $('#id_carrera_doc').attr("value", $(this).data('id_carrera'));
                $('#ventanaSubirArchivo').dialog('open');
            });


            $('#ventanaSubirArchivo').dialog({
                buttons: {
                    "Cerrar": function() {
                        var docs_de_envio = $('#docs_de_envio').val();
                        if (docs_de_envio == 'CEREMONIA') {
                            Obtener_Mis_Documentos_Enviados($('#Id_Ceremonia').val(), $('#desc_tipo_propuesta').val());
                        } else {
                            Obtener_Mis_Ceremonias($('#Id_Usuario').val(), $('#Id_Carrera').val());
                        }

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
                position: {
                    at: 'center top'
                },
                dialogClass: 'no-close',
                closeOnEscape: false
            });

            //ADJUNTAR ARCHIVO 
            $('#loading').hide();
            $(':file').change(function() {
                var archivo_selec = $('#file')[0].files[0];
                var archivo_nombre = archivo_selec.name;
                var archivo_extension = archivo_nombre.substring(archivo_nombre.lastIndexOf('.') + 1);
                var archivo_tamano = archivo_selec.size;
                var archivo_tipo = archivo_selec.type;

                var info_Archivo_Selec = '';
                info_Archivo_Selec += "<table class='tabla_Registros'><tr><th colspan='2'><span>Información del Archivo seleccionado:</span></th></tr>";
                info_Archivo_Selec += "<tr><td><b>Nombre del archivo:</b></td><td>" + archivo_nombre + "</td></tr>";
                info_Archivo_Selec += "<tr><td><b>Extensión:</b></td><td>" + archivo_extension + "</td></tr>";
                info_Archivo_Selec += "<tr><td><b>Tipo:</b></td><td>" + archivo_tipo + "</td></tr>";
                info_Archivo_Selec += "<tr><td><b>Tamaño:</b></td><td>" + (archivo_tamano / 1048576) + " kB</td></tr></table>";

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
                var docs_de_envio = $('#docs_de_envio').val();
                var archivo_php = '';
                if (docs_de_envio == 'CEREMONIA') {
                    archivo_php = 'uploadFile_7_procesa.php';
                } else {
                    archivo_php = 'uploadFile_9_procesa.php';
                }

                $.ajax({
                    url: archivo_php,
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
            }); */

            Obtener_Carreras_Del_Alumno();
            //f5($(document),true);
            $('#ventanaDatosCeremonia').hide();
            $('#ventanaSeleccionarCeremonia').hide();
            $('#ventanaConfirmacionAgregarCeremonia').hide();
            $('#ventanaConfirmacionActualizacion').hide();
            $('#ventanaDocumentosEnviados_Ceremonia').hide();

            $('#ventanaConfirmarBorrado_Ceremonia').hide();
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
                <p>Mi Titulación por Ceremonia</p>
            </div>
            <div class="barra_Herramientas">
                <input type="submit" id="btn_Agregar_Ceremonia" name="btn_Agregar_Ceremonia" value="Agregar" class="btn_Herramientas" />
            </div>
            <div class="barra_Parametros">
                <label for="selec_Mis_Carreras" class="etiqueta_Parametro">Mi Carrera:</label>
                <select id="selec_Mis_Carreras" name="selec_Mis_Carreras" class="combo_Parametro">
                </select>
            </div>
            <div id="tabla_Mis_Ceremonias" class="tabla_Registros">
            </div>
        </div>
    </div>

    <div id="ventanaDatosCeremonia">
        <form name="datos_Ceremonia" id="datos_Ceremonia" method="" action="">
            <div>
                <p style="height: 1.5em; padding-top: .5em; background-color: #acc8cc; text-align: center; font-weight: bold; font-size: 1.3em;"><label id="desc_propuesta"></label></p>

                <div id="div_Sede" class="camposTexto datos_Ceremonia">
                    <p>
                        <label for="sedes">Sedes:</label>
                        <textarea name="sedes" id="sedes" maxlength='500' placeholder='' class="entrada_Dato"
                            style='' onkeyup='javascript:this.value=this.value.toUpperCase();'
                            title='SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #' autocomplete='off'></textarea>
                        <span id="statusSede" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                </div>
                <div id="div_Diplomado" class="camposTexto datos_Ceremonia">
                    <p>
                        <label for="diplomados_cursos">Nombre de Diplomados ó Cursos:</label>
                        <textarea name="diplomados_cursos" id="diplomados_cursos" maxlength='500' placeholder='' class="entrada_Dato"
                            onkeyup='javascript:this.value=this.value.toUpperCase();'
                            title='SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #' autocomplete='off'></textarea>
                        <span id="statusDiplomados" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                </div>
                <div id="div_Materias" class="camposTexto datos_Ceremonia">
                    <p>
                        <label for="materias">Nombre de Materias:</label>
                        <textarea name="materias" id="materias" maxlength='500' placeholder='' class="entrada_Dato"
                            onkeyup='javascript:this.value=this.value.toUpperCase();'
                            title='SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #' autocomplete='off'></textarea>
                        <span id="statusMaterias" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                </div>
                <div id="div_Progama" class="camposTexto datos_Ceremonia">
                    <p>
                        <label for="programa_posgrado">Nombre del Programa de Posgrado:</label>
                        <input type="text" name="programa_posgrado" id="programa_posgrado" maxlength="100"
                            title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #"
                            style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato" autocomplete="off" />
                        <span id="statusPrograma" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                </div>
                <div id="div_Articulo" class="camposTexto datos_Ceremonia">
                    <p>
                        <label for="titulo_articulo">Título del Artículo:</label>
                        <input type="text" name="titulo_articulo" id="titulo_articulo" maxlength="100"
                            title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #"
                            style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato" autocomplete="off" />
                        <span id="statusArticulo" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                </div>
                <div id="div_Revista" class="camposTexto datos_Ceremonia">
                    <p>
                        <label for="nombre_Revista">Nombre de la Revista:</label>
                        <input type="text" name="nombre_Revista" id="nombre_Revista" maxlength="100"
                            title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #"
                            style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato" autocomplete="off" />
                        <span id="statusRevista" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                </div>
            </div>
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
            <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="">
            <input type="hidden" id="id_tipo_propuesta" name="id_tipo_propuesta" value="">
            <input type="hidden" id="Id_Carrera" name="Id_Carrera" value="">
            <input type="hidden" id="Id_Ceremonia" name="Id_Ceremonia" value="">
            <input type="hidden" id="Desc_Propuesta" name="Desc_Propuesta" value="">
        </form>
        <!-- MODAL PARA QUIENES AÚN NO ESTÁN TITULADOS -->
        <div id="fechaEstimadaTitulacionMotivo" class="contenido_Formulario hidden" style="width: 400px;" hidden>
            <p style="height: 1.2em; margin-bottom: : .5em; text-align: center;" id="mensajeFechaEstimadaTitulacion">Para tu ceremonia necesitamos que llenes los siguientes datos</p>
            <div id="contenidoFechaTitulacion" style="margin-top: 20px;">
                <form action="" method="post" enctype="multipart/form-data" id="formFechaTitulacion">
                    <p>
                        <label for="motivoTitulacion" class="label">Motivo:</label>
                        <textarea type="text" name="motivoTitulacion" class="entrada_Dato" id="motivoTitulacion" autocomplete="off" />
                        <span id="avisoMotivoTitulacion" class="dato_Invalido"><img src="./assets/images/ui/error.ico"></span>
                    </p>
                    <p>
                        <label for="fechaEstimada" class="label">Fecha estimada para titulación</label>
                        <input type="text" name="fechaEstimada" class="entrada_Dato" id="fechaEstimada" autocomplete="off" />
                        <span id="avisoFechaEstimada" class="dato_Invalido"><img src="./assets/images/ui/error.ico"></span>
                    </p>
                </form>
            </div>
        </div>

    </div>

    <div id='ventanaDocumentosEnviados_Ceremonia'>
        <div id="tabla_Mis_Doc_Ceremonia">
        </div>
    </div>

    <div id="ventanaSubirArchivo">
        <div>
            <form action="" method="post" enctype="multipart/form-data" id="frmSubirPDF" name="frmSubirPDF">
                <p>
                    <label data-id_ceremonia='x' data-id_documento="0" data-version="0" id='lblTitulo_SubirArchivo' for="archivoPDF"></label>
                <div>
                    <input type="file" name="file" id="file" accept=".pdf" required class="tag_file">
                    <input type="submit" name="enviarArchivo" id="enviarArchivo" value="Enviar" class="btn_Herramientas">
                    <input type="hidden" id="id_ceremonia_doc" name="id_ceremonia_doc" value="">
                    <input type="hidden" id="id_documento_doc" name="id_documento_doc" value="">
                    <input type="hidden" id="version_doc" name="version_doc" value="">
                    <input type="hidden" id="id_usuario_doc" name="id_usuario_doc" value="">
                    <input type="hidden" id="desc_corta_doc" name="desc_corta_doc" value="">
                    <input type="hidden" id="id_carrera_doc" name="id_carrera_doc" value="">
                    <input type="hidden" id="desc_tipo_propuesta" name="desc_tipo_propuesta" value="">
                    <input type="hidden" id="docs_de_envio" name="docs_de_envio" value="">
                </div>
                </p>
            </form>
            <div id='loading' class="resultado_Carga_De_Archivo">
                <h1>Cargando el Archivo...</h1>
            </div>

            <div id="message" class="informacion_Archivo_A_Cargar"></div>

        </div>
    </div>

    <div id='ventanaSeleccionarCeremonia'>
        <div id="tabla_Seleccionar_Ceremonia">

        </div>

    </div>
    <div id='ventanaConfirmacionAgregarCeremonia'>
    </div>
    <div id='ventanaConfirmacionActualizacion'>
        Desea Actulizar los Datos ?
    </div>


    <div id="ventanaConfirmarBorrado_Ceremonia">
        <span style="font-weight: bold; margin-top: 5px">Debe de indicar los Motivos por los que desea dar de baja esta Ceremonia.</span>
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
    <div id="ventanaProcesando" data-role="header">
        <img id="cargador" src="./assets/images/ui/engrane2.gif" /><br>
        Procesando su transacción....!<br>
        Espere por favor.
    </div>

    <!--    </body>
</html>-->
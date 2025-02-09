<!DOCTYPE html>
<!--
Fecha:          Julio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para aprobar las Propuestas de los Profesores
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
            //OBTENEMOS LAS PROPUESTAS PENDIENTES DE AUTORIZAR
            function Obtener_Propuestas_Por_Autorizar(id_estatus, id_usuario) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER',
                    id_estatus: id_estatus,
                    id_usuario: id_usuario
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_coord_jdpto_Aprobar_Propuesta.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:100%;">';
                        html_table += '<TR><TH>Propuesta</TH>\n\
                                        <TH>Profesor</TH>\n\
                                        <TH>Tipo</TH>\n\
                                        <TH>Título</TH>\n\
                                        <TH>Fecha recibida</TH>\n\
                                        <TH>Estatus</TH>\n\
                                        <TH>Acción</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                var $btn_Revisar_Doc = '';

                                $btn_Revisar_Doc = '<button class="btn_Revisar btnOpcion" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' +
                                    ' data-id_profesor = \'' + value['id_profesor'] + '\' ' +
                                    ' data-correo_profesor = \'' + value['email_usuario'] + '\' ' +
                                    ' data-fecha_registrada = \'' + value['fecha_registrada'] + '\' ' +
                                    ' data-titulo_propuesta = \'' + value['titulo_propuesta'] + '\' ' +
                                    ' data-desc_corta_doc= \'' + value['descripcion_para_nom_archivo'] + '\' ' +
                                    ' data-id_estatus =' + value['id_estatus'] +
                                    ' data-id_documento =' + value['id_documento'] +
                                    ' data-id_version = ' + value['version_propuesta'] + '>Revisar Doc</button>';

                                html_table += '<TR>';
                                html_table += '<TD>' + value['id_propuesta'] + '</TD>';
                                html_table += '<TD style="text-align:left;">' + value['nombre'] + '</TD>';
                                html_table += '<TD>' + value['descripcion_tipo_propuesta'] + '</TD>';
                                html_table += '<TD style="text-align:left;">' + value['titulo_propuesta'] + '</TD>';
                                html_table += '<TD>' + value['fecha_recepcion_doc'] + '</TD>';
                                html_table += '<TD>' + value['descripcion_estatus'] + '</TD>';
                                html_table += '<TD>' + $btn_Revisar_Doc + '</TD>';
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
                        html_table += '<TR><TH>Propuesta</TH>\n\
                                             <TH>Profesor</TH>\n\
                                             <TH>Tipo</TH>\n\
                                             <TH>Título</TH>\n\
                                             <TH>Fecha recibida</TH>\n\
                                             <TH>Estatus</TH>\n\
                                             <TH>Acción</TH></TR>';

                        html_table = html_table + '<TR><TD colspan="7">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Propuestas').empty();
                        $('#tabla_Propuestas').html(html_table);
                    });
            }
            //FIN OBTENEMOS LAS PROPUESTAS PENDIENTES

            //EVENTO CLICK SOBRE EL BOTON REVISAR DOC
            $('#tabla_Propuestas').on("click", "button.btn_Revisar", function(e) {
                e.preventDefault();
                $id_profesor = $(this).data('id_profesor');
                $id_version = $(this).data('id_version');
                $id_propuesta = $(this).data('id_propuesta');
                $desc_corta_doc = $(this).data('desc_corta_doc');
                $('#id_profesor_doc').attr("value", $(this).data('id_profesor'));
                $('#id_propuesta_doc').attr("value", $(this).data('id_propuesta'));
                $('#id_documento_doc').attr("value", $(this).data('id_documento'));
                $('#id_version_doc').attr("value", $(this).data('id_version'));
                $('#correo_profesor').attr("value", $(this).data('correo_profesor'));
                $('#fecha_registrada').attr("value", $(this).data('fecha_registrada'));
                $('#titulo_propuesta').attr("value", $(this).data('titulo_propuesta'));
                $('#desc_corta_doc').attr("value", $(this).data('desc_corta_doc'));

                var tituloAdjuntar = $id_profesor + "_" +
                    $id_propuesta + "_" +
                    $id_version + "_" +
                    $desc_corta_doc + ".pdf"

                $('#ventanaDocumentoPDF').dialog({
                    open: function() {
                        $('#ventanaDocumentoPDF').dialog("option", "title", tituloAdjuntar);
                        var tiempo = new Date();
                        var fileName = "Docs/Propuestas_Profesor/" +
                            $id_profesor + "_" +
                            $id_propuesta + "_" +
                            $id_version + "_" +
                            $desc_corta_doc + ".pdf" + "?" + tiempo;

                        var new_Object = $('#obj_PDF_doc').clone(false);
                        new_Object.attr("type", "application/pdf");
                        new_Object.attr("data", fileName);
                        $("#obj_PDF_doc").replaceWith(new_Object);
                        Obtener_Propuesta($id_propuesta);
                        $('#btn_autorizar_Doc').prop('disabled', '');
                        $('#btn_rechazar_Doc').prop('disabled', '');

                    },
                    title: 'Aprobar Propuesta de Profesor',
                    modal: true,
                    autoOpen: true,
                    resizable: true,
                    draggable: true,
                    width: '1000',
                    height: '620',
                    show: 'slide',
                    hide: 'slide',
                    position: {
                        at: 'center top'
                    },
                    dialogClass: 'no-close',
                    closeOnEscape: false
                });

                var datos = {
                    Tipo_Movimiento: 'TRAER_INDICE',
                    id_propuesta: $(this).data('id_propuesta')
                };

                $.ajax({
                    data: datos,
                    type: "POST",
                    dataType: "json",
                    url: "_Negocio/n_coord_jdpto_Aprobar_Propuesta.php"
                }).done(function(respuesta, textStatus, jqXHR) {
                    //console.log(respuesta);

                    var html_table = '<TABLE  class="tabla_Registros" style="width:100%;">';
                    html_table += '<TR><TH>ÍNDICE</TH></TR>';
                    if (respuesta.success == true) {
                        //recorremos cada registro
                        $.each(respuesta.data.registros, function(key, value) {

                            html_table += '<TR>';
                            html_table += '<TD>' + value['indice'] + '</TD>';
                            html_table = html_table + '</TR>';
                        });
                        html_table = html_table + '</TABLE>';

                        //console.log(value['indice']);

                        var new_Object = $('#obj_PDF_doc').clone(false);
                        $("#obj_PDF_doc").replaceWith(new_Object);
                        $('#obj_PDF_doc').empty();
                        $('#obj_PDF_doc').html(html_table);
                    } else {
                        html_table = html_table + '<TR><TD>No hay índice por mostrar.</TD></TR>';
                        html_table = html_table + '</TABLE>';

                        var new_Object = $('#obj_PDF_doc').clone(false);
                        $("#obj_PDF_doc").replaceWith(new_Object);

                        $('#obj_PDF_doc').empty();
                        $('#obj_PDF_doc').html(html_table);
                    }


                }).fail(function(jqXHR, textStatus, errorThrown) {
                    $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                    $('#ventanaAvisos').dialog('open');
                });


            });
            //FIN EVENTO CLICK SOBRE EL BOTON REVISAR DOC

            //MOSTRAMOS LOS DATOS DE LA PROPUESTA
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
                        console.log(respuesta["data"]["registros"]["Propuesta_ProfesorId_Tipo_Propuesta"] === "3");

                        if (respuesta.success == true) {
                            var $propuesta_html = '';
                            var dias_de_asesoria = '';
                            var horarios_asesoria = respuesta.data.registros.Propuesta_Profesorhorarios;
                            var arr_horarios = horarios_asesoria.split("|");
                            var elementos_horarios_arr = arr_horarios.length;

                            for (i = 0; i < elementos_horarios_arr; i++) {
                                arr_desc_dia_horario = arr_horarios[i].split(",");
                                desc_dia = arr_desc_dia_horario[2];
                                desc_horario = arr_desc_dia_horario[3];
                                dias_de_asesoria += desc_dia + "->" + desc_horario + "<br>";
                            }

                            $propuesta_html += '<TABLE class="tabla_Registros">' +
                                '<CAPTION>Datos de la Propuesta</CAPTION>' +
                                '<TR><TD><b>Propuesta</b></TD><TD>' + respuesta.data.registros.Propuesta_ProfesorId_Propuesta + '</TD></TR>' +
                                '<TR><TD colspan=2><b>Tipo:</b><br>' + respuesta.data.registros.Propuesta_Profesordescripcion_tipo_propuesta + '</TD></TR>' +
                                '<TR><TD colspan=2><b>Título:</b><br>' + respuesta.data.registros.Propuesta_ProfesorTitulo + '</TD></TR>' +
                                '<TR><TD colspan=2><b>Horario para Asesorías:</b><br>' + dias_de_asesoria + '</TD></TR>' +
                                '<TR><TD colspan=2><b>En colaboración:</b><br>' + respuesta.data.registros.Propuesta_ProfesorOrganismos_Colaboradores + '</TD></TR>' +
                                '<TR><TD colspan =2><b>Alumnos Requeridos para esta Propuesta</b></TD></TR>';

                            var carreras_n_alumnos = respuesta.data.registros.Propuesta_Profesorrequerimiento_alumnos;
                            var arr_c_a = carreras_n_alumnos.split("|");
                            var elementos_arr = arr_c_a.length;
                            var carrera_desc = '';
                            var n_alumnos = '';

                            for (i = 0; i < elementos_arr; i++) {
                                carr_alum = arr_c_a[i].split(",");
                                id_carrera = carr_alum[0];
                                carrera_desc = carr_alum[1];
                                n_alumnos = carr_alum[2];
                                $propuesta_html += "<TR><TD>" + carrera_desc + '</TD><TD>' + n_alumnos + '</TD></TR>';
                            }
                            $propuesta_html += '</TABLE>';
                            //console.log($propuesta_html);
                            $('#tabla_Propuesta').html($propuesta_html);
                            $('#ventanaProcesando').dialog('close');
                        } else {
                            $propuesta_html = '<TABLE>' +
                                '<TR><TD>Asesoría</TD><TD>' + respuesta.data.message + '</TD></TR></TABLE>';
                            $('#tabla_Propuesta').html($propuesta_html);
                            $('#ventanaProcesando').dialog('close');
                        }
                        /*Comentamos codigo para que no traiga el indice y solo muestre el pdf del documento.
                        //Valida si va por el indice en ves de dejar el documento pdf-
                        if (respuesta["data"]["registros"]["Propuesta_ProfesorId_Tipo_Propuesta"] != "2" ) {

                        } else {
                        var datos = {
                            Tipo_Movimiento : 'TRAER_INDICE',
                            id_propuesta: id_propuesta
                            };


                        console.log($(this).data('id_propuesta'));
                        console.log(datos);

                        $.ajax({
                           data : datos,
                           type : "POST",
                           dataType : "json",
                           url : "_Negocio/n_administrador_Asignar_Coordinadores.php"
                        }).done(function(respuesta,textStatus,jqXHR){
                          console.log(respuesta);

                          var html_table = '<TABLE  class="tabla_Registros" style="width:100%;">';
                          html_table += '<TR><TH>ÍNDICE</TH></TR>';
                         if (respuesta.success == true){
                             //recorremos cada registro
                             $.each(respuesta.data.registros, function( key, value ) {

                                 html_table += '<TR>';
                                 html_table += '<TD>' + value['indice'] + '</TD>';
                                 html_table = html_table + '</TR>';
                             });
                             html_table = html_table + '</TABLE>';

                             //console.log(value['indice']);

                             var new_Object = $('#obj_PDF_doc').clone(false);
                             new_Object.attr("type", null);
                               new_Object.attr("data", null);
                             $("#obj_PDF_doc").replaceWith(new_Object);
                             $('#obj_PDF_doc').empty();
                             $('#obj_PDF_doc').html(html_table);                                
                         }
                         else 
                         {
                             html_table = html_table + '<TR><TD>No hay índice por mostrar.</TD></TR>';
                             html_table = html_table + '</TABLE>';

                             var new_Object = $('#obj_PDF_doc').clone(false);
                             $("#obj_PDF_doc").replaceWith(new_Object);
                             new_Object.attr("type", null);
                               new_Object.attr("data", null);

                             $('#obj_PDF_doc').empty();
                             $('#obj_PDF_doc').html(html_table);
                        }

                        }).fail(function(jqXHR,textStatus,errorThrown) {
                        $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisos').dialog('open');
                        });


                    } //Llave de condicion para traer el indice
                    */

                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $propuesta_html = '<TABLE>' +
                            '<TR><TD>Asesoría</TD><TD>La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown + '</TD></TR></TABLE>';
                        $('#tabla_Propuesta').html($propuesta_html);
                        $('#ventanaProcesando').dialog('close');
                    });
                /*$('#ventanaProcesando').dialog('open');        
                var datos = {Tipo_Movimiento : 'SELECCIONAR',
                           id_propuesta : id_propuesta
                       };
                $.ajax({
                   data : datos,
                   type : "POST",
                   dataType : "json",
                   url : "_Negocio/n_profesor_Mis_Propuestas.php"
                })
                   .done(function(respuesta,textStatus,jqXHR){
                        if (respuesta.success == true){
                           var $propuesta_html = '';
                           var dias_de_asesoria = '';
                            var horarios_asesoria = respuesta.data.registros.Propuesta_Profesorhorarios;
                            var arr_horarios = horarios_asesoria.split("|");
                            var elementos_horarios_arr = arr_horarios.length;
                            
                            for (i=0; i<elementos_horarios_arr; i++){
                                arr_desc_dia_horario = arr_horarios[i].split(",");
                                desc_dia = arr_desc_dia_horario[2];
                                desc_horario = arr_desc_dia_horario[3];
                                dias_de_asesoria += desc_dia + "->"+ desc_horario + "<br>";
                            }
                           $propuesta_html += '<TABLE class="tabla_Registros">' +
                                              '<CAPTION>Datos de la Propuesta</CAPTION>' +
                                              '<TR><TD><b>Propuesta</b></TD><TD>' + respuesta.data.registros.Propuesta_ProfesorId_Propuesta + '</TD></TR>' +
                                              '<TR><TD><b>Fecha registrada</b></TD><TD>' + respuesta.data.registros.Propuesta_ProfesorFecha_Registrada + '</TD></TR>' +
                                              '<TR><TD colspan=2><b>Tipo:</b><br>' + respuesta.data.registros.Propuesta_Profesordescripcion_tipo_propuesta + '</TD></TR>' +
                                              '<TR><TD colspan=2><b>Título:</b><br>' + respuesta.data.registros.Propuesta_ProfesorTitulo + '</TD></TR>' +
                                              '<TR><TD colspan=2><b>Horario para Asesorías:</b><br>' + dias_de_asesoria +  '</TD></TR>' + 
                                              '<TR><TD colspan=2><b>En colaboración:</b><br>' + respuesta.data.registros.Propuesta_ProfesorOrganismos_Colaboradores + '</TD></TR>' +
                                              '<TR><TD colspan =2><b>Requerimientos de la Propuesta</b></TD></TR>';
                            $('#fecha_registrada').val(respuesta.data.registros.Propuesta_ProfesorFecha_Registrada);                  
                            var carreras_n_alumnos = respuesta.data.registros.Propuesta_Profesorrequerimiento_alumnos;
                            var arr_c_a = carreras_n_alumnos.split("|");
                            var elementos_arr = arr_c_a.length;
                            var carrera_desc ='';
                            var n_alumnos = '';
                            
                            for (i=0; i<elementos_arr; i++){
                                carr_alum = arr_c_a[i].split(",");
                                id_carrera = carr_alum[0];
                                carrera_desc = carr_alum[1];
                                n_alumnos = carr_alum[2];
                                $propuesta_html += "<TR><TD>" + carrera_desc + '</TD><TD>' + n_alumnos + '</TD></TR>';
                            }
                            $propuesta_html += '</TABLE>';
                            $('#tabla_Propuesta').html($propuesta_html);
                            $('#ventanaProcesando').dialog('close');                          
                       }
                        else {
                            $propuesta_html = '<TABLE>'+
                                              '<TR><TD>Asesoría</TD><TD>' + respuesta.data.message + '</TD></TR></TABLE>';
                            $('#tabla_Propuesta').html($propuesta_html);
                            $('#ventanaProcesando').dialog('close');                                                          
                        }                                                                   
                   })
                        .fail(function(jqXHR,textStatus,errorThrown){
                            $propuesta_html = '<TABLE>'+
                                              '<TR><TD>Asesoría</TD><TD>La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown + '</TD></TR></TABLE>';
                            $('#tabla_Propuesta').html($propuesta_html);
                            $('#ventanaProcesando').dialog('close');                                                                            
                        });*/
            }
            //FIN MOSTRAMOS LOS DATOS DE LA PROPUESTA

            //CLICK AL BOTON ACEPTAR DOC
            $('#btn_autorizar_Doc').on('click', function(e) {
                e.preventDefault();
                $('#ventanaConfirmar_Aceptacion_Doc').dialog({
                    buttons: {
                        "Aceptar": function() {
                            if (!$('#nota_admin_a').val().match(miExpReg_Nota_Aceptacion)) {
                                $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #');
                                $('#ventanaAvisos').dialog('open');
                            } else {
                                $('#ventanaMensajeConfirma').text('Desea dar por Aceptado este Documento ?');
                                $('#ventanaConfirmaVoBo').dialog({
                                    buttons: {
                                        "Aceptar": function() {
                                            $(this).dialog('close');
                                            var id_propuesta_doc = $('#id_propuesta_doc').val();
                                            var id_documento_doc = $('#id_documento_doc').val();
                                            var id_version_doc = $('#id_version_doc').val();
                                            var id_estatus = 3; //3. Aceptado
                                            var id_usuario = $('#Id_Usuario').val();
                                            var nota = $('#nota_admin_a').val();
                                            var fecha_registrada = $('#fecha_registrada').val();
                                            var tipo_Mov = 'ACTUALIZAR_ESTATUS_DOC';
                                            var id_profesor = $('#id_profesor_doc').val();
                                            var correo_profesor = $('#correo_profesor').val();
                                            var titulo_propuesta = $('#titulo_propuesta').val();
                                            var desc_corta_doc = $('#desc_corta_doc').val();

                                            console.log(id_propuesta_doc);
                                            console.log(id_documento_doc);
                                            console.log(id_version_doc);
                                            console.log(id_estatus);
                                            console.log(id_usuario);
                                            console.log(nota);
                                            console.log(fecha_registrada);
                                            console.log(tipo_Mov);
                                            console.log(id_profesor);
                                            console.log(correo_profesor);
                                            console.log(titulo_propuesta);
                                            console.log(desc_corta_doc);



                                            actualiza_Estatus_Prop(tipo_Mov, id_propuesta_doc, id_documento_doc, id_version_doc,
                                                id_estatus, id_usuario, nota, fecha_registrada, id_profesor, correo_profesor, titulo_propuesta, desc_corta_doc);

                                            $('#nota_admin_a').val('');
                                        },
                                        "Cancelar": function() {
                                            $(this).dialog('close');
                                        }
                                    },
                                    title: 'Confirmar Aceptación',
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
                            $(this).dialog('close');
                            $('#nota_admin_a').val('');
                        }
                    },
                    title: 'Nota para la Aceptación del Documento',
                    modal: true,
                    autoOpen: true,
                    resizable: false,
                    draggable: true,
                    height: 'auto',
                    width: '450',
                    show: 'slide',
                    hide: 'slide',
                    dialogClass: 'no-close',
                    closeOnEscape: false
                }); //FIN ventanaConfirmar_Aceptacion_Doc
            });
            //FIN CLICK AL BOTON ACEPTAR DOC

            //CLICK AL BOTON RECHAZAR DOCUMENTO
            $('#btn_rechazar_Doc').on('click', function(e) {
                e.preventDefault();
                $('#ventanaConfirmar_Rechazo_Doc').dialog({
                    buttons: {
                        "Aceptar": function() {
                            if (!$('#nota_admin').val().match(miExpReg_Nota_Rechazo)) {
                                $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) . - _ #');
                                $('#ventanaAvisos').dialog('open');
                            } else {
                                $('#ventanaMensajeConfirma').text('Desea Rechazar este Documento ?');
                                $('#ventanaConfirmaVoBo').dialog({
                                    buttons: {
                                        "Aceptar": function() {
                                            $(this).dialog('close');
                                            //                                                    $('#ventanaProcesando').dialog('open');
                                            var id_propuesta_doc = $('#id_propuesta_doc').val();
                                            var id_documento_doc = $('#id_documento_doc').val();
                                            var id_version_doc = $('#id_version_doc').val();
                                            var id_estatus = 4; //4. Rechazado
                                            var id_usuario = $('#Id_Usuario').val();
                                            var nota = $('#nota_admin').val();
                                            var fecha_registrada = $('#fecha_registrada').val();
                                            var tipo_Mov = 'ACTUALIZAR_ESTATUS_DOC';
                                            var id_profesor = $('#id_profesor_doc').val();
                                            var correo_profesor = $('#correo_profesor').val();
                                            var titulo_propuesta = $('#titulo_propuesta').val();
                                            var desc_corta_doc = $('#desc_corta_doc').val();

                                            actualiza_Estatus_Prop(tipo_Mov, id_propuesta_doc, id_documento_doc, id_version_doc,
                                                id_estatus, id_usuario, nota, fecha_registrada, id_profesor, correo_profesor, titulo_propuesta, desc_corta_doc);

                                            $("#nota_admin").val('');
                                        },
                                        "Cancelar": function() {
                                            $(this).dialog('close');
                                        }
                                    },
                                    title: 'Confirmar Rechazo',
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
                            $("#nota_admin").val('');
                            $(this).dialog('close');
                        }
                    },
                    close: function() {
                        $("#nota_admin").val('');
                        $(this).dialog('close');
                    },
                    title: 'Nota para el Rechazo del Documento',
                    modal: true,
                    autoOpen: true,
                    resizable: false,
                    draggable: true,
                    height: 'auto',
                    width: '450',
                    show: 'slide',
                    hide: 'slide',
                    dialogClass: 'no-close',
                    closeOnEscape: false
                });
            }); //FIN CONFIRMAR RECHAZO DEL DOCUMENTO

            function actualiza_Estatus_Prop(tipo_Mov, id_propuesta_doc, id_documento_doc, id_version_doc,
                id_estatus, id_usuario, nota, fecha_registrada, id_profesor, correo_profesor, titulo_propuesta, desc_corta_doc) {
                $('#ventanaProcesando').dialog('open');
                $.ajax({
                        data: {
                            Tipo_Movimiento: tipo_Mov,
                            id_propuesta: id_propuesta_doc,
                            id_documento: id_documento_doc,
                            id_version: id_version_doc,
                            id_estatus: id_estatus,
                            id_usuario: id_usuario,
                            nota: nota,
                            fecha_registro: fecha_registrada,
                            id_profesor: id_profesor,
                            correo_profesor: correo_profesor,
                            titulo_propuesta: titulo_propuesta,
                            desc_corta_doc: desc_corta_doc
                        },
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_coord_jdpto_Aprobar_Propuesta.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        $('#ventanaProcesando').dialog('close');
                        if (respuesta.success == true) {
                            if (id_estatus == 3) { //3.Aceptado
                                $('#ventanaConfirmar_Aceptacion_Doc').dialog('close');
                            } else {
                                $('#ventanaConfirmar_Rechazo_Doc').dialog('close');
                            }
                            $('#btn_autorizar_Doc').prop('disabled', 'disabled');
                            $('#btn_rechazar_Doc').prop('disabled', 'disabled');

                            $('#ventanaAviso').html(respuesta.data.message);
                            $('#ventanaAvisos').dialog('open');
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
            }

            $('#btn_cerrar_Doc').on('click', function(e) {
                e.preventDefault();
                $('#message').empty();
                $(".ui-dialog-content").dialog("close");
                Obtener_Propuestas_Por_Autorizar(9, $('#Id_Usuario').val()); //DOCUMENTOS con estatus 9.Por Autorizar Coord/Dpto
            });

            $('#btn_ver_Docs').on('click', function(e) {
                e.preventDefault();
                $("#ventanaDocumentos_Profesor").dialog('open');
            });

            $('#ventanaDocumentos_Profesor').dialog({
                buttons: {
                    "Cerrar": function() {
                        $(this).dialog('close');
                    }
                },
                open: function() {
                    //Obtenemos los documentos compatidos del Profesor
                    //$('#ventanaProcesando').dialog('open'); 

                    var datos = {
                        Tipo_Movimiento: 'TRAER_INDICE_COMPLETO',
                        id_propuesta: $('#id_propuesta_doc').val()
                    };

                    $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_coord_jdpto_Aprobar_Propuesta.php"
                    }).done(function(respuesta, textStatus, jqXHR) {
                        //console.log(respuesta);

                        var html_table = '<TABLE  class="tabla_Registros" style="width:100%;">';


                        if (respuesta.success == true) {

                            html_table += '<TR><TH>OBJETIVO</TH><TH>DEFINICIÓN DEL PROBLEMA</TH><TH>MÉTODO</TH><TH>TEMAS A UTILIZAR</TH><TH>RESULTADOS ESPERADOS</TH></TR>';
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {

                                html_table += '<TR>';
                                html_table += '<TD>' + value['objetivo'] + '</TD>';
                                html_table += '<TD>' + value['definicion_problema'] + '</TD>';
                                html_table += '<TD>' + value['metodo'] + '</TD>';
                                html_table += '<TD>' + value['temas_utilizar'] + '</TD>';
                                html_table += '<TD>' + value['resultados_esperados'] + '</TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';

                            //console.log(value['indice']);
                            $('#tabla_DocsProfesor').empty();
                            $('#tabla_DocsProfesor').html(html_table);
                            $('#ventanaProcesando').dialog('close');
                        } else {
                            html_table += '<TR><TH>Documento</TH></TR>';
                            html_table = html_table + '<TR><TD>No hay datos por mostrar.</TD></TR>';
                            html_table = html_table + '</TABLE>';

                            $('#tabla_DocsProfesor').empty();
                            $('#tabla_DocsProfesor').html(html_table);
                        }



                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisos').dialog('open');
                    });



                    /* $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_profesor_Mis_Docs.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                            var html_table = '<TABLE class="tabla_Registros">';
                            html_table += '<TR><TH>Documento</TH></TR>';                               
                           if (respuesta.success == true){
                               //recorremos cada registro
                                var nom_archivo = '';
                                var fecha_= '';
                                var ruta_doc = '';
                                var archivos_HTML = '';
                                
                               $.each(respuesta.data.registros, function( key, value ) {
                                   if (value['compartido']==1)
                                   {
                                       html_table += '<TR><TD>';
                                       nom_archivo = value['nombre_archivo'];
                                       fecha_=new Date();
                                       ruta_doc= 'Docs/Docs_Profesores/'+nom_archivo+'?'+ fecha_;
                                       archivos_HTML = "<a href='"+ruta_doc+"' target='_blank'>"+nom_archivo+"</a>";
                                       html_table += archivos_HTML + '</TD></TR>';
                                   }
                               });
                               $('#ventanaProcesando').dialog('close');
                                html_table = html_table + '</TABLE>';
                                $('#tabla_DocsProfesor').empty();
                                $('#tabla_DocsProfesor').html(html_table);                                   
                           }
                           else
                           {
                               $('#ventanaProcesando').dialog('close');
                                html_table = html_table + '<TR><TD>' + respuesta.data.message + '</TD></TR>';
                                html_table = html_table + '</TABLE>'
                                $('#tabla_DocsProfesor').empty();
                                $('#tabla_DocsProfesor').html(html_table);                                   
                           }
                       })
                               .fail(function(jqXHR,textStatus,errorThrown){
                                    $('#ventanaProcesando').dialog('close');
                                    var html_table = '<TABLE class="tabla_Registros">';
                                    html_table += '<TR><TH>Documento</TH></TR>';
                                    html_table = html_table + '<TR><TD>' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                    html_table = html_table + '</TABLE>';
                                    $('#tabla_DocsProfesor').empty();
                                    $('#tabla_DocsProfesor').html(html_table);                                
                               });*/
                },
                title: 'Documentos Compartidos por el Profesor',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: true,
                height: 'auto',
                width: '600',
                show: 'slide',
                hide: 'slide',
                dialogClass: 'no-close',
                closeOnEscape: false,
                position: {
                    at: 'center top'
                }
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
            Obtener_Propuestas_Por_Autorizar(9, $('#Id_Usuario').val());
            $('#ventanaConfirmar_Aceptacion_Doc').hide();
            $('#ventanaConfirmar_Rechazo_Doc').hide();
            $('#ventanaDocumentoPDF').hide();
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
                <p>Aprobar Propuestas</p>
            </div>
        </div>
        <div id="tabla_Propuestas" class="tabla_Registros">
        </div>
        <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
    </div>

    <div id="ventanaDocumentoPDF">
        <div id="divOpciones_Doc" style="text-align: right; width: 970px;">
            <button id="btn_ver_Docs" class="btn_Herramientas" style="margin-left: 5px; width: 200px;">Documentos del Profesor</button>
            <button id="btn_autorizar_Doc" class="btn_Herramientas">Aceptar Doc.</button>
            <button id="btn_rechazar_Doc" class="btn_Herramientas">Rechazar Doc.</button>
            <button id="btn_cerrar_Doc" class="btn_Herramientas">Cerrar</button>

            <input type="hidden" id="id_profesor_doc" name="id_profesor_doc" value="">
            <input type="hidden" id="id_propuesta_doc" name="id_propuesta_doc" value="">
            <input type="hidden" id="id_documento_doc" name="id_documento_doc" value="">
            <input type="hidden" id="id_version_doc" name="id_version_doc" value="">
            <input type="hidden" id="fecha_registrada" name="fecha_registrada" value="">
            <input type="hidden" id="correo_profesor" name="correo_profesor" value="">
            <input type="hidden" id="titulo_propuesta" name="titulo_propuesta" value="">
            <input type="hidden" id="desc_corta_doc" name="desc_corta_doc" value="">
        </div>

        <div id="tabla_Propuesta" style="float: left; border: 1px grey solid; width: 300px;">

        </div>
        <div id="archivoPDF_doc" style="float: right; border: 1px grey solid; height: 520px; width: 650px; ">
            <object id="obj_PDF_doc" width="650px" height="520px"></object>
        </div>

    </div>

    <div id="ventanaConfirmar_Aceptacion_Doc">
        <div id="nota">
            <p>
                <textarea id="nota_admin_a" class="entrada_Dato notaVoBo" maxlength="500" onkeyup="javascript:this.value=this.value.toUpperCase();"
                    title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
            </p>
        </div>
    </div>
    <div id="ventanaConfirmar_Rechazo_Doc">
        <p>
            <textarea id="nota_admin" class="entrada_Dato notaVoBo" maxlength="500" onkeyup="javascript:this.value=this.value.toUpperCase();"
                title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
        </p>
    </div>
    <div id="ventanaConfirmaVoBo">
        <span id="ventanaMensajeConfirma"></span>
    </div>
    <div id="ventanaDocumentos_Profesor">
        <div id="tabla_DocsProfesor" class="tabla_Registros">
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
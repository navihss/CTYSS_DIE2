<!DOCTYPE html>
<!--
Fecha:          Julio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para aprobar las propuestas de profesor
-->
<?php
    header('Content-Type: text/html; charset=UTF-8');
    header("Cache-Control: no-cache");
    header("Pragma: nocache");
    session_start();
     if(!isset($_SESSION["id_tipo_usuario"]) and
        !isset($_SESSION["id_usuario"])){
    header('Location: index.php');
        }
    
?>

<html>
    <head>
        <script src="js/expresiones_reg.js"></script>        
        
        <script>
            $( document ).ready(function() {
                //Llenamos los catálogos
                function llena_Catalogo(nom_control, tipo_movimiento, tabla_catalogo, tabla_campos, 
                        tabla_where, tabla_orderby){

                    var datos = {Tipo_Movimiento : tipo_movimiento, 
                            tabla_Catalogo  : tabla_catalogo,
                            tabla_Campos    : tabla_campos,
                            tabla_Where     : tabla_where,
                            tabla_OrderBy   : tabla_orderby
                            };
                    $.ajax({
                        data : datos,
                        type : 'POST',
                        dataType : 'json',
                        url : '_Negocio/n_administrador_Crear_Nueva_Cuenta.php'
                    })
                        .done(function(respuesta,textStatus,jqXHR){
                            var html_options='';
                            if (respuesta.success == true){
                                //recorremos cada registro
                                $.each(respuesta.data.registros, function( key, value ) {
                                    //recorremos los valores de cada usuario
                                    html_options = html_options + '<INPUT type="checkbox" class ="' + tabla_catalogo + '" name="" \n\
                                                                    value=' + value['id'] + '>' + value['descripcion'] + '<br>';
                                });
                                $('#' + nom_control).empty();
                                $('#' + nom_control).html(html_options);                                
                            }
                            else {
                                $('#ventanaAviso').html(respuesta.data.message);
                                $('#ventanaAvisos').dialog('open');                                                                    
                            }
                        })
                                .fail(function(jqXHR,textStatus,errorThrown){
                                    $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                                    $('#ventanaAvisos').dialog('open');                            
                                });                            
                }     
                //FIN LLENADO DE CATALOGO

                function cargar_Catalogo(nom_control, tipo_movimiento, nom_clase){

                    var datos = {
                            Tipo_Movimiento : tipo_movimiento,
                            id_division: $('#Id_Division').val()
                            };
                    console.log(datos);
                    $.ajax({
                        data : datos,
                        type : 'POST',
                        dataType : 'json',
                        url : '_Negocio/n_Catalogos_Generales_B.php'
                    })
                        .done(function(respuesta,textStatus,jqXHR){
                            var html_options='';
                            if (respuesta.success == true){
                                //recorremos cada registro
                                $.each(respuesta.data.registros, function( key, value ) {
                                    //recorremos los valores de cada usuario
                                    html_options = html_options + '<INPUT type="checkbox" class ="' + nom_clase + '" name="" \n\
                                                                    value=' + value['id'] + '>' + value['descripcion'] + '<br>';
                                });
                                $('#' + nom_control).empty();
                                $('#' + nom_control).html(html_options);                                
                            }
                            else {
                                $('#ventanaAviso').html(respuesta.data.message);
                                $('#ventanaAvisos').dialog('open');                                                                    
                            }
                        })
                                .fail(function(jqXHR,textStatus,errorThrown){
                                    $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                                    $('#ventanaAvisos').dialog('open');                            
                                });                            
                }     
                //FIN LLENADO DE CATALOGO
                //
                //OBTENEMOS LOS DOCUMENTOS PENDIENTES DE ASIGNACION DE COORDINADORES
                function Obtener_Propuestas_Por_Autorizar(id_estatus){
                    var datos = { Tipo_Movimiento : 'OBTENER',
                                  id_estatus : id_estatus,
                                  id_division: $('#Id_Division').val()
                                };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_administrador_Asignar_Coordinadores.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE style="width:100%;">';
                           html_table += '<TR><TH>Propuesta</TH>\n\
                                        <TH>Profesor</TH>\n\
                                        <TH>Tipo</TH>\n\
                                        <TH>Título</TH>\n\
                                        <TH>Fecha recibida</TH>\n\
                                        <TH>Estatus</TH>\n\
                                        <th colspan="2">Acción</th></TR>';
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                   var $btn_Revisar_Doc = '';
                                                                       
                                   $btn_Revisar_Doc = '<button class="btn_Revisar btnOpcion" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' + 
                                            ' data-id_profesor = \'' + value['id_profesor'] + '\' ' +
                                            ' data-desc_corta_doc = \'' + value['descripcion_para_nom_archivo'] + '\' ' +
                                            ' data-id_estatus =' + value['id_estatus'] + 
                                            ' data-id_documento =' + value['id_documento'] + 
                                            ' data-id_version = ' + value['version_propuesta'] + 
                                            ' data-titulo_propuesta  = \'' + value['titulo_propuesta'] + '\' ' +
                                            ' data-correo_profesor = \'' + value['email_usuario'] + '\'>Revisar Doc</button>';
                                    $btn_bitacora = '<button id="btn_bitacora" class="btn_Bitacora btnOpcion" data-id_propuesta_bitacora=\'' + value['id_propuesta'] + '\' ' + 
                                            '>Bitacora</button>';
                                   html_table += '<TR>';
                                   html_table += '<TD>' + value['id_propuesta'] + '</TD>';
                                   html_table += '<TD style="text-align:left;">' + value['nombre'] + '</TD>';
                                   html_table += '<TD>' + value['descripcion_tipo_propuesta'] + '</TD>';
                                   html_table += '<TD style="text-align:left;">' + value['titulo_propuesta'] + '</TD>';
                                   html_table += '<TD>' + value['fecha_recepcion_doc'] + '</TD>';
                                   html_table += '<TD>' + value['descripcion_estatus'] + '</TD>';
                                   html_table += '<TD>' + $btn_Revisar_Doc + '</TD>';
                                   html_table += '<TD>' + $btn_bitacora + '</TD>';
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Propuestas').empty();
                               $('#tabla_Propuestas').html(html_table);                                
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="7">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Propuestas').empty();
                               $('#tabla_Propuestas').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
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
                //FIN OBTENEMOS LOS DOCUMENTOS PENDIENTES DE ASIGNACION DE COORDINADORES

                //EVENTO CLICK SOBRE EL BOTON REVISAR DOC
                $('#tabla_Propuestas').on("click", "button.btn_Revisar", function(e){
                    e.preventDefault();
                    $id_profesor = $(this).data('id_profesor');
                    $id_version = $(this).data('id_version');
                    $id_propuesta = $(this).data('id_propuesta');
                    $desc_corta_doc = $(this).data('desc_corta_doc');
                    $('#id_profesor_doc').attr("value", $(this).data('id_profesor'));
                    $('#id_propuesta_doc').attr("value", $(this).data('id_propuesta'));
                    $('#id_documento_doc').attr("value", $(this).data('id_documento'));
                    $('#id_version_doc').attr("value", $(this).data('id_version'));
                    $('#titulo_doc').attr("value", $(this).data('titulo_propuesta'));
                    $('#correo_profesor').attr("value", $(this).data('correo_profesor')); 
                    $('#desc_corta_doc').attr("value", $(this).data('desc_corta_doc'));


                                  

                    var tituloAdjuntar =$id_profesor + "_" + 
                            $id_propuesta +"_" +
                            $id_version + "_" +
                            $desc_corta_doc + ".pdf"                    
                    $('#ventanaDocumentoPDF').dialog({                      
                        open : function(){
                               $('#ventanaDocumentoPDF').dialog("option","title", tituloAdjuntar);
                               var tiempo = new Date();
                               var fileName ="Docs/Propuestas_Profesor/" +  
                                       $id_profesor + "_" + 
                                       $id_propuesta +"_" +
                                       $id_version + "_" +
                                       $desc_corta_doc + ".pdf" +"?" + tiempo;
                               var new_Object = $('#obj_PDF_doc').clone(false);
                               console.log(new_Object);
                               new_Object.attr("type", "application/pdf");
                               new_Object.attr("data", fileName);
                               console.log("AAAAAAAAAAAAAAAAAAAAAAAAAAA");
                               console.log(new_Object);
                               $("#obj_PDF_doc").replaceWith(new_Object);  
                               $('#btn_autorizar_Doc').prop('disabled','');
                               $('#btn_rechazar_Doc').prop('disabled','');

                               Obtener_Propuesta($id_propuesta);
                        },
                        title: 'Aprobar Propuesta de Profesor',
                        modal : true,
                        autoOpen : true,
                        resizable :false,
                        draggable : true,   
                        width : '1000',
                        height : '620',
                        show : 'slide',
                        hide : 'slide',
                        position : {at: 'center top'},
                        dialogClass : 'no-close',
                        closeOnEscape : false
                    });

 
                });
                //FIN EVENTO CLICK SOBRE EL BOTON REVISAR DOC

                //MOSTRAMOS LOS DATOS DE LA PROPUESTA
                function Obtener_Propuesta(id_propuesta){                                           
                    $('#ventanaProcesando').dialog('open');        
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
                        console.log(respuesta["data"]["registros"]["Propuesta_ProfesorId_Tipo_Propuesta"] === "3");

                        if (respuesta.success == true){
                            console.log('Entro aqui con respuesta success');
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
                                                  '<TR><TD colspan=2><b>Tipo:</b><br>' + respuesta.data.registros.Propuesta_Profesordescripcion_tipo_propuesta + '</TD></TR>' +
                                                  '<TR><TD colspan=2><b>Título:</b><br>' + respuesta.data.registros.Propuesta_ProfesorTitulo + '</TD></TR>' +
                                                  '<TR><TD colspan=2><b>Horario para Asesorías:</b><br>' + dias_de_asesoria+ '</TD></TR>' + 
                                                  '<TR><TD colspan=2><b>En colaboración:</b><br>' + respuesta.data.registros.Propuesta_ProfesorOrganismos_Colaboradores + '</TD></TR>' +
                                                  '<TR><TD colspan =2><b>Alumnos Requeridos para esta Propuesta</b></TD></TR>';
                                                  
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
                            });                                                                             
                }                
                //FIN MOSTRAMOS LOS DATOS DE LA PROPUESTA

                //Evento para mostrar historial de la propuesta
                $('#tabla_Propuestas').on("click", "button.btn_Bitacora", function(e){
                    //e.preventDefault();
                    $('#btn_bitacora').val($(this).data("id_propuesta_bitacora"));
                    var id_propuesta_bitacora = $(this).data("id_propuesta_bitacora");
                    Obtener_Bitacora_Propuestas(id_propuesta_bitacora);
                    $('#ventanaPropuestaBitacora').dialog('open');
                });
                //Fin de evento para mostrar historial

                
                
                //funcion para obtener el historial de las propuestas enviadas
				function Obtener_Bitacora_Propuestas(id_propuesta_bitacora){                                           
                    var datos = {Tipo_Movimiento : 'BITACORA_PROPUESTAS',
                               id_propuesta : id_propuesta_bitacora
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_administrador_Asignar_Coordinadores.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<table class="tabla_Registros"><caption> Clave de la Propuesta: ' + id_propuesta_bitacora + '</caption>';
                           html_table = html_table + '<tr><th>Título</th><th>Versión</th><th>Fecha Generada</th><th>Estatus</th><th>Nota</th><th>Profesor</th><th>Tipo Propuesta</tr>';
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                   
                                   if (value['fecha_generada']){
                                       $dato_fecha = '<td>' + value['fecha_generada'] + '</td>';
                                   }
                                   else{
                                       $dato_fecha = '<td></td>';
                                   }
                                   if (value['nota']){
                                       $dato_nota = '<td>' + value['nota'] + '</td>';
                                   }
                                   else{
                                       $dato_nota = '<td></td>';
                                   }
                                   if (value['profesor']){
                                       $dato_profesor = '<td>' + value['profesor'] + '</td>';
                                   }
                                   else{
                                       $dato_profesor = '<td></td>';
                                   }								   

                                   html_table = html_table + '<td style="text-align:left;">' + value['titulo_propuesta'] + '</td>';

                                   html_table = html_table + '<td style="text-align:left;">' + value['version_propuesta'] + '</td>';
                                   html_table = html_table + $dato_fecha;
                                   html_table = html_table + '<td>' + value['descripcion_estatus'] + '</td>';
                                   html_table = html_table + $dato_nota;                                   
								   html_table = html_table + $dato_profesor;
								   html_table = html_table + '<td style="text-align:left;">' + value['descripcion_tipo_propuesta'] + '</td>';
                                   html_table = html_table + '</tr>';
                               });
                               html_table = html_table + '</table>';
                               //console.log(html_table);
                               $('#tabla_bitacora_propuestas').empty();
                               $('#tabla_bitacora_propuestas').html(html_table);                                
                               }
                           else {
                               html_table = html_table + '<tr><td colspan="7">' + respuesta.data.message + '</td></tr>';
                               html_table = html_table + '</table>'
                               $('#tabla_bitacora_propuestas').empty();
                               $('#tabla_bitacora_propuestas').html(html_table);
                           }
                       })
                        .fail(function(jqXHR,textStatus,errorThrown){
								var html_table = '<table class="tabla_Registros"><caption> Clave de la Propuesta: ' + id_propuesta_bitacora + '</caption>';
								html_table = html_table + '<tr><th>Título</th><th>Versión</th><th>Fecha Generada</th><th>Estatus</th><th>Nota</th><th>Profesor</th><th>Tipo Propuesta</tr>';
                                html_table = html_table + '<tr><td colspan="7">' + textStatus + '. ' + errorThrown + '</td></tr>';
                                html_table = html_table + '</table>';
                                $('#tabla_bitacora_propuestas').empty();
                                $('#tabla_bitacora_propuestas').html(html_table);                                
                         });                                                                             
				}                
                //fin Obtenemos los Documentos Enviados para Autorizar la Propuesta

                //Ventana Modal para mostrar el historial de las propuestas
                $('#ventanaPropuestaBitacora').dialog({
                   buttons:{
                        "Cerrar" : function() {
                            $(this).dialog('close');
                        }
                   },
                   title: 'Historial de Propuestas',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                   height : '550',
                   width : '850',
                   show : 'slide',
                   hide : 'slide',
                   dialogClass : 'no-close',
                   closeOnEscape : false,                   
                   position : {at: 'center top'}                   
                });
                //Fin de ventana modal de historial de propuestas
                
                
                //CLICK AL BOTON ACEPTAR DOC
                $('#btn_autorizar_Doc').on('click',function(e){
                    e.preventDefault();
                    $('#ventanaConfirmar_Aceptacion_Doc').dialog({
                        buttons:{
                             "Aceptar" : function() {
                                var sel_Coordinaciones = '';
                                var sel_Departamentos = '';

                                $('.coordinaciones:checked').each(function(){
                                    sel_Coordinaciones += $(this).prop('value') + ',';
                                });
                                $('.departamentos:checked').each(function(){
                                   sel_Departamentos += $(this).prop('value') + ',';
                                });
                                if (sel_Coordinaciones == '' && sel_Departamentos == ''){
                                   $('#ventanaAviso').html('Debe seleccionar las Coordinaciones/Departamentos que darán el VoBo a la Propuesta.');
                                   $('#ventanaAvisos').dialog('open');                                                                 
                                   return false;
                                }
                                sel_Coordinaciones = sel_Coordinaciones.substr(0, sel_Coordinaciones.length-1);
                                sel_Departamentos = sel_Departamentos.substr(0, sel_Departamentos.length-1);

                                    $('#ventanaMensajeConfirma').text('Desea Aceptar este Documento y envíarlo a Coordinación/Depto. ?');
                                    $('#ventanaConfirmaVoBo').dialog({
                                        buttons:{
                                             "Aceptar" : function() {
                                                $(this).dialog('close');

                                                var id_propuesta_doc = $('#id_propuesta_doc').val();
                                                var id_documento_doc = $('#id_documento_doc').val();
                                                var id_version_doc = $('#id_version_doc').val();
                                                var id_estatus      =  9; //9. Por Autorizar por Coordinadores
                                                var id_administrador = $('#Id_Usuario').val();
                                                var nota=$('#nota_admin_a').val();
                                                var id_profesor = $('#id_profesor_doc').val();
                                                var titulo_propuesta  = $('#titulo_doc').val();
                                                var correo_profesor = $('#correo_profesor').val();
                                                var tipo_Mov ='ACTUALIZAR_ESTATUS_DOC';
                                                var desc_corta_doc = $('#desc_corta_doc').val();

                                                actualiza_Estatus_Doc(tipo_Mov, id_propuesta_doc, id_documento_doc, id_version_doc, 
                                                id_estatus, id_administrador, nota, sel_Coordinaciones, sel_Departamentos, 
                                                id_profesor, titulo_propuesta, correo_profesor, desc_corta_doc);

                                             },
                                             "Cancelar" : function(){
                                                $(this).dialog('close');
                                             }
                                        },
                                        title: 'Confirmar Aceptación',
                                        modal : true,
                                        autoOpen : true,
                                        resizable : true,
                                        draggable : true,
                                        dialogClass : 'no-close ventanaConfirmaUsuario',
                                        closeOnEscape : false   
                                    });                                        

                                //}                                                                  
                             },
                             "Cancelar" : function() {
                                 $('.coordinaciones').each(function(){
                                    $(this).prop('checked',false); 
                                 });
                                 $('.departamentos').each(function(){
                                    $(this).prop('checked',false); 
                                 });
                                 $('#nota_admin_a').val('');
                                 
                                 $(this).dialog('close');
                             }                       
                        },
                        title: 'Nota para la Aceptación del Documento',
                        modal : true,
                        autoOpen : true,
                        resizable : false,
                        draggable : true,
                        width : '700',
                        show : 'slide',
                        hide : 'slide',
                        dialogClass : 'no-close',
                        closeOnEscape : false                           
                    }); //FIN ventanaConfirmar_Aceptacion_Doc
                });
                //FIN CLICK AL BOTON ACEPTAR DOC

                //CLICK AL BOTON RECHAZAR DOCUMENTO
                $('#btn_rechazar_Doc').on('click',function(e){
                    e.preventDefault();
                    $('#ventanaConfirmar_Rechazo_Doc').dialog({
                        buttons:{
                             "Aceptar" : function() {

                                    $('#ventanaMensajeConfirma').text('Desea Rechazar este Documento ?');
                                    $('#ventanaConfirmaVoBo').dialog({
                                        buttons:{
                                             "Aceptar" : function() {
                                                $(this).dialog('close');
                                                var id_propuesta_doc = $('#id_propuesta_doc').val();
                                                var id_documento_doc = $('#id_documento_doc').val();
                                                var id_version_doc = $('#id_version_doc').val();
                                                var id_estatus      =  4; //4. Rechazado
                                                var id_administrador = $('#Id_Usuario').val();
                                                var nota= $('#nota_admin').val();
                                                var id_profesor = $('#id_profesor_doc').val();
                                                var titulo_propuesta  = $('#titulo_doc').val();  
                                                var correo_profesor = $('#correo_profesor').val();
                                                var tipo_Mov ='ACTUALIZAR_ESTATUS_DOC';
                                                var desc_corta_doc = $('#desc_corta_doc').val();

                                                var sel_Coordinaciones = '';
                                                var sel_Departamentos = '';

                                                actualiza_Estatus_Doc(tipo_Mov, id_propuesta_doc, id_documento_doc, id_version_doc, 
                                                id_estatus, id_administrador, nota, sel_Coordinaciones, sel_Departamentos, 
                                                id_profesor, titulo_propuesta, correo_profesor,desc_corta_doc);

                                             },
                                             "Cancelar" : function(){
                                                $(this).dialog('close');
                                             }
                                        },
                                        title: 'Confirmar Rechazo',
                                        modal : true,
                                        autoOpen : true,
                                        resizable : true,
                                        draggable : true,
                                        dialogClass : 'no-close ventanaConfirmaUsuario',
                                        closeOnEscape : false   
                                    });                            
                                //}    
                             },
                             "Cancelar" : function() {
                                 $("#nota_admin").val('');
                                 $(this).dialog('destroy');
                             }                       
                        },
                        close : function(){
                            $("#nota_admin").val('');
                            $(this).dialog('destroy');
                        },                        
                        title: 'Nota para el Rechazo del Documento',
                        modal : true,
                        autoOpen : true,
                        resizable : false,
                        draggable : true,
                        height : 'auto',
                        width : '450',                        
                        show : 'slide',
                        hide : 'slide',
                        dialogClass : 'no-close',
                        closeOnEscape : false   
                    });
                }); //FIN CONFIRMAR RECHAZO DEL DOCUMENTO
                
               function actualiza_Estatus_Doc(tipo_Mov, id_propuesta_doc, id_documento_doc, id_version_doc, 
                                    id_estatus, id_administrador, nota, sel_Coordinaciones, sel_Departamentos, 
                                    id_profesor, titulo_propuesta,correo_profesor, desc_corta_doc)
                {
                    var datos={Tipo_Movimiento : tipo_Mov,
                            id_propuesta    : id_propuesta_doc,
                            id_documento    : id_documento_doc,
                            id_version      : id_version_doc,
                            id_estatus      : id_estatus,
                            id_administrador: id_administrador,
                            nota            : nota,
                            coordinaciones  : sel_Coordinaciones,
                            departamentos   : sel_Departamentos,
                            id_profesor     : id_profesor,
                            titulo_propuesta: titulo_propuesta,
                            correo_profesor : correo_profesor,
                            desc_corta_doc  : desc_corta_doc
                            };                    
                    $('#ventanaProcesando').dialog('open');
                    $.ajax({
                        data : datos,
                        type : "POST",
                        dataType : "json",
                        url : "_Negocio/n_administrador_Asignar_Coordinadores.php"
                    })
                        .done(function(respuesta,textStatus,jqXHR){
                            $('#ventanaProcesando').dialog('close');
                            if(respuesta.success == true){
                                if(id_estatus == 4){ //4.Rechazo de Doc.
                                    $('#ventanaConfirmar_Rechazo_Doc').dialog('close');                                    
                                }
                                else{
                                    $('#ventanaConfirmar_Aceptacion_Doc').dialog('close');                                    
                                }
                               $('#btn_autorizar_Doc').prop('disabled','disabled');
                               $('#btn_rechazar_Doc').prop('disabled','disabled');

                               $('#ventanaAviso').html(respuesta.data.message);
                               $('#ventanaAvisos').dialog('open'); 
                            }
                            else{
                               $('#ventanaProcesando').dialog('close');
                               $('#ventanaAviso').html(respuesta.data.message);
                               $('#ventanaAvisos').dialog('open');                                   
                            }
                        })
                                .fail(function(jqXHR,textStatus,errorThrown){
                                    $('#ventanaProcesando').dialog('close');                                    
                                    $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                    $('#ventanaAvisos').dialog('open');                            
                                    return false;
                                });                  
                }
                
                $('#btn_cerrar_Doc').on('click',function(e){
                    e.preventDefault();
                    $('#message').empty();
                    $(".ui-dialog-content").dialog("close");
                    Obtener_Propuestas_Por_Autorizar(2); //DOCUMENTOS con estatus 2.Por Autorizar
                });

                $('#btn_ver_Docs').on('click',function(e){
                    e.preventDefault();
                    $("#ventanaDocumentos_Profesor").dialog('open');
                });

               $('#ventanaDocumentos_Profesor').dialog({                   
                   buttons:{
                        "Cerrar" : function() {
                            $(this).dialog('close');
                        }
                   },
                   open : function(){

                    //Obtenemos los documentos compatidos del Profesor
                                               //Obtenemos los documentos compatidos del Profesor
                        var datos = {Tipo_Movimiento : 'TRAER_INDICE_COMPLETO',
                           id_propuesta: $('#id_propuesta_doc').val()
                        };

                        $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_administrador_Asignar_Coordinadores.php"
                    }).done(function(respuesta,textStatus,jqXHR){
                      //console.log(respuesta);

                      var html_table = '<TABLE  class="tabla_Registros" style="width:100%;">';
                      

                     if (respuesta.success == true){

                      html_table += '<TR><TH>OBJETIVO</TH><TH>DEFINICIÓN DEL PROBLEMA</TH><TH>MÉTODO</TH><TH>TEMAS A UTILIZAR</TH><TH>RESULTADOS ESPERADOS</TH></TR>';
                         //recorremos cada registro
                         console.log(respuesta.data.registros.length);
                         $.each(respuesta.data.registros, function( key, value ) {

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
                     }
                     else {
                      html_table += '<TR><TH>Documentos</TH></TR>';
                         html_table = html_table + '<TR><TD>No hay datos por mostrar.</TD></TR>';
                         html_table = html_table + '</TABLE>';

                         $('#tabla_DocsProfesor').empty();
                         $('#tabla_DocsProfesor').html(html_table);
                     }

                  }).fail(function(jqXHR,textStatus,errorThrown) {
          $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
          $('#ventanaAvisos').dialog('open');
        });                                    
                       
                   },
                   title: 'Documentos Compartidos por el Profesor',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                    height : 'auto',
                    width : '600', 
                    show : 'slide',
                    hide : 'slide',
                   dialogClass : 'no-close',
                   closeOnEscape : false,
                   position : {at: 'center top'} 
                });                            
               
               $('#ventanaAvisos').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $(this).dialog('close');
                        }
                   },
                   title: 'Aviso',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                   dialogClass : 'no-close ventanaMensajes',
                   closeOnEscape : false
                });                            

                $('#ventanaProcesando').dialog({
                   title: '',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : false,
                   dialogClass : 'no-close no-titlebar',
                   closeOnEscape : false
                });  

                
                function esNulo(valor_){
                    if(valor_ == null){
                        return '';
                    }
                    else
                    {
                        return valor_;
                    }                            
                }

                function f5(that,val){
                    if(val)
                    {
                        that.on("keydown",function(e){
                            var code = (e.keyCode ? e.keyCode : e.which);
                            if(code == 116 || code == 8) {
                                e.preventDefault();
                            }
                        })
                    }
                    else
                    {
                        that.off("keydown");
                    }
                }
                
                Obtener_Propuestas_Por_Autorizar(2);

                cargar_Catalogo('Coordinaciones', 'OBTENER_COORDINACIONES', 'coordinaciones');
                cargar_Catalogo('Departamentos', 'OBTENER_DEPARTAMENTOS', 'departamentos');
                $('#ventanaConfirmar_Aceptacion_Doc').hide();
                $('#ventanaConfirmar_Rechazo_Doc').hide();
                $('#ventanaDocumentoPDF').hide();
            });
                        
        </script>
        

        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                    <p>Asignar Coordinadores</p>
                </div>
            </div>
            <div id="tabla_Propuestas" class="tabla_Registros">
            </div>
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
            <input type="hidden" id="Id_Division" name="Id_Usuario" value="<?php echo $_SESSION['id_division']; ?>">
        </div>
        <!-- Ventana para mostrar el historial de una propuesta -->
        <div id='ventanaPropuestaBitacora'>
            <div id="tabla_bitacora_propuestas" class="tabla_Registros"></div>
        </div>
        <!-- Fin Ventana para mostrar el historial de una propuesta -->        
        <div id="ventanaDocumentoPDF">
            <div id="divOpciones_Doc" style="text-align: right; width: 970px;">
                <button id="btn_ver_Docs" class="btn_Herramientas" style="margin-left: 5px; width: 200px;">Documentos del Profesor</button>
                <button id="btn_autorizar_Doc" class="btn_Herramientas" style="margin-left: 5px;">Aceptar Doc.</button>
                <button id="btn_rechazar_Doc" class="btn_Herramientas" style="margin-left: 5px;">Rechazar Doc.</button>
                <button id="btn_cerrar_Doc" class="btn_Herramientas" style="margin-left: 5px;">Cerrar</button>
                
                <input type="hidden" id="id_profesor_doc" name="id_profesor_doc" value="">
                <input type="hidden" id="id_propuesta_doc" name="id_propuesta_doc" value="">
                <input type="hidden" id="id_documento_doc" name="id_documento_doc" value="">
                <input type="hidden" id="id_version_doc" name="id_version_doc" value="">
                <input type="hidden" id="titulo_doc" name="titulo_doc" value="">
                <input type="hidden" id="correo_profesor" name="correo_profesor" value="">      
                <input type="hidden" id="desc_corta_doc" name="desc_corta_doc" value="">      
            </div>
            
            <div id="tabla_Propuesta" style="float: left; border: 1px grey solid; width: 300px;">                
            </div>
            
            <div id="archivoPDF_doc" style="float: right; border: 1px grey solid; height: 520px; width: 650px; ">
                <object id="obj_PDF_doc" width="650px" height="520px"></object>
            </div>
                        
        </div>
        
        <div id="ventanaConfirmar_Aceptacion_Doc" style="width: 700px;">
            <b>Seleccione las Coordinaciones/Departamentos que darán el VoBo a la Propuesta.</b><br><br>
            <div id="divCoordinaciones1" style="float: left; width: 330px;">
                <b style="color: #990000;">Coordinaciones:</b>
                <div id="Coordinaciones">
                    
                </div>
                
            </div>
            <div id="divDepartamentos" style="float: left; width: 330px;">
                <b style="color: #990000;">Departamentos:</b>
                <div id="Departamentos">
                    
                </div>
            </div>

            <div id="espacioNota" style="height: 44px;">
            </div>

            <div id="nota">
                <p>
                    <label for="nota_admin_a" class="etiqueta_Parametro">Nota:</label>
                    <textarea id="nota_admin_a" style="width: 100%; height: 130px; text-transform: uppercase;margin-top: 4px;" onkeyup="javascript:this.value=this.value.toUpperCase();"
                              title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( )' - _ #" autocomplete="off" class="entrada_Dato"></textarea>
                </p>                             
            </div>
        </div>
        <div id="ventanaDocumentos_Profesor">
            <div id="tabla_DocsProfesor" class="tabla_Registros">
            </div>
        </div>
    
        <div id="ventanaConfirmar_Rechazo_Doc">
            <p>
                <textarea id="nota_admin" style="width: 400px; height: 120px; text-transform: uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"
                          title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( )' - _ #" autocomplete="off" class="entrada_Dato"></textarea>

            </p>
        </div>

        <div id="ventanaConfirmaVoBo">
            <span id="ventanaMensajeConfirma"></span>
        </div>
        
        <div id="ventanaAvisos">
            <span id="ventanaAviso"></span>
        </div>
        <div id="ventanaProcesando" data-role="header">
            <img id="cargador" src="css/images/engrane2.gif"/><br>
            Procesando su transacción....!<br>
            Espere por favor.
        </div>
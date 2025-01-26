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
                               
                //OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
                function Obtener_Reportes_Bimestrales(id_estatus){
                    var datos = {Tipo_Movimiento : 'OBTENER_REPORTES_BIMESTRALES',
                               id_estatus : id_estatus
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_administrador_Aprobar_Reporte_Bimestral.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE style="width:100%;">';
                           html_table += '<TR><TH>Alumno</TH>\n\
                                        <TH>Id_Servicio</TH>\n\
                                        <TH>No.<br>Rpt</TH>\n\
                                        <TH>Vers.</TH>\n\
                                        <TH>F. Prog. Inicio</TH>\n\
                                        <TH>F. Prog. Término</TH>\n\
                                        <TH>Hrs Prog.</TH>\n\
                                        <TH>F. Real Inicio</TH>\n\
                                        <TH>F. Real Término</TH>\n\
                                        <TH>F. de Envío</TH>\n\
                                        <TH>Hrs realizadas</TH>\n\
                                        <TH>Nota</TH>\n\
                                        <TH>Acción</TH></TR>';
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                   $btn_RevisarRpt ='';
                                   $btn_RevisarRpt = '<button class="btn_RevisarRpt btnOpcion" data-id_ss=\'' + value['id_ss'] + '\' ' + 
                                            ' data-id_alumno = \'' + value['id_alumno'] + '\' ' +
                                            ' data-id_carrera = ' + value['id_carrera'] + ' ' +
                                            ' data-correo = \'' + value['email_usuario'] + '\' ' +
                                            ' data-desc_doc = \'' + value['descripcion_para_nom_archivo'] + '\' ' +
                                            ' data-numero_reporte_bi=' + value['numero_reporte_bi'] + 
                                            ' data-fecha_prog_inicio=' + value['fecha_prog_inicio'] +
                                            ' data-fecha_prog_fin=' + value['fecha_prog_fin'] + 
                                            ' data-horas_obligatorias=' + value['horas_obligatorias'] + 
                                            ' data-id_version=' + value['id_version']  + '>Revisar Reporte</button>';

                                   html_table += '<TR>';
                                   html_table += '<TD>' + value['id_alumno'] + '</TD>';
                                   html_table += '<TD>' + value['id_ss'] + '</TD>';
                                   html_table += '<TD>' + value['numero_reporte_bi'] + '</TD>';
                                   html_table += '<TD style="text-align:left;">' + value['id_version'] + '</TD>';
                                   html_table += '<TD style="text-align:left;">' + value['fecha_prog_inicio'] + '</TD>';
                                   html_table += '<TD>' + esNulo(value['fecha_prog_fin']) + '</TD>';
                                   html_table += '<TD>' + value['horas_obligatorias'] + '</TD>';                                                                                                         
                                   html_table += '<TD>' + esNulo(value['fecha_real_inicio']) + '</TD>';
                                   html_table += '<TD>' + esNulo(value['fecha_real_fin']) + '</TD>';
                                   html_table += '<TD>' + esNulo(value['fecha_recepcion_rpt']) + '</TD>';
                                   html_table += '<TD>' + value['horas_laboradas'] + '</TD>';
                                   html_table += '<TD>' + value['nota'] + '</TD>';
                                   html_table += '<TD>' + $btn_RevisarRpt  + '</TD>';
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Reportes_Bimestrales').empty();
                               $('#tabla_Reportes_Bimestrales').html(html_table);                                
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="13">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Reportes_Bimestrales').empty();
                               $('#tabla_Reportes_Bimestrales').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros">';
                                html_table += '<TR><TH>Alumno</TH>\n\
                                             <TH>Id_Servicio</TH>\n\
                                             <TH>No.<br>Rpt</TH>\n\
                                             <TH>Vers.</TH>\n\
                                             <TH>F. Prog. Inicio</TH>\n\
                                             <TH>F. Prog. Término</TH>\n\
                                             <TH>Hrs Prog.</TH>\n\
                                             <TH>F. Real Inicio</TH>\n\
                                             <TH>F. Real Término</TH>\n\
                                             <TH>F. de Envío</TH>\n\
                                             <TH>Hrs realizadas</TH>\n\
                                             <TH>Nota</TH>\n\
                                             <TH>Acción</TH></TR>';
                                html_table = html_table + '<TR><TD colspan="13">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Reportes_Bimestrales').empty();
                                $('#tabla_Reportes_Bimestrales').html(html_table);                                
                            });                                                                                                                                   
                }
                //FIN OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO

                //EVENTO CLICK SOBRE EL BOTON REVISAR REPORTE
                $('#tabla_Reportes_Bimestrales').on("click", "button.btn_RevisarRpt", function(e){
                    e.preventDefault();
                    var id_alumno = $(this).data('id_alumno');
                    $('#id_alumno_rpt').attr("value", $(this).data('id_alumno'));
                    $('#correo_user').attr("value", $(this).data('correo'));
                    $('#carrera_user').attr("value", $(this).data('id_carrera'));
                    $('#id_ss_rpt').attr("value", $(this).data('id_ss'));
                    $('#numero_reporte_rpt').attr("value", $(this).data('numero_reporte_bi'));
                    $('#id_version_rpt').attr("value", $(this).data('id_version'));
                    $('#fecha_prog_inicio_rpt').attr("value", $(this).data('fecha_prog_inicio'));
                    $('#fecha_prog_fin_rpt').attr("value", $(this).data('fecha_prog_fin'));
                    $('#horas_obligatorias_rpt').attr("value", $(this).data('horas_obligatorias'));
                    $('#id_carrera_rpt').attr("value", $(this).data('id_carrera'));
                    $('#desc_doc_rpt').attr("value", $(this).data('desc_doc'));
                    
                    var tituloAdjuntar = id_alumno + "_" + 
                            $('#id_carrera_rpt').val() + "_" +
                            $('#id_ss_rpt').val() + "_" +
                            $('#numero_reporte_rpt').val() + "_" + 
                            $('#id_version_rpt').val() + "_" +
                            $('#desc_doc_rpt').val() + ".pdf"; 
                    
                    $('#ventanaReporteBimestralPDF').dialog({                      
                        open : function(){
                               $('#ventanaReporteBimestralPDF').dialog("option","title", tituloAdjuntar);
                               var tiempo = new Date();
                               var fileName ="Docs/Reportes_Bimestrales/" + 
                                       id_alumno + "_" + 
                                       $('#id_carrera_rpt').val() + "_" +
                                       $('#id_ss_rpt').val() + "_" +
                                       $('#numero_reporte_rpt').val() + "_" + 
                                       $('#id_version_rpt').val() + "_" +
                                       $('#desc_doc_rpt').val() + ".pdf?" + tiempo; 
                               var new_Object = $('#obj_PDF_rptBim').clone(false);
                               new_Object.attr("type", "application/pdf");
                               new_Object.attr("data", fileName);
                               $("#obj_PDF_rptBim").replaceWith(new_Object); 
                               $('#btn_autorizar_Rpt').prop("disabled",'');
                               $('#btn_rechazar_Rpt').prop("disabled",'');

                        },
                        close: function(){
                            $(this).dialog('destroy');
                        },                        
                        title: 'Aprobar Reporte Bimestral',
                        modal : true,
                        autoOpen : true,
                        resizable :false,
                        draggable : true,   
                        width : '950',
                        height : '630',
                        show : 'slide',
                        hide : 'slide',                        
                        dialogClass : 'no-close',
                        closeOnEscape : false,
                        position : {at: 'right top'}
                    });
                });
                //FIN EVENTO CLICK SOBRE EL BOTON REVISAR REPORTE

                //MOSTRAMOS UNA VENTANA CON LOS DATOS DEL SERVICIO SOCIAL DEL ALUMNO
                $('#btn_verServicioSocial_Rpt').on('click',function(e){
                    e.preventDefault();
                    $('#tabla_Mi_Servicio').dialog({
                        open : function(){
                                var datos = {Tipo_Movimiento : 'OBTENER_DATOS_GENERALES_SS',
                                    Id_Usuario : 0,
                                    Id_Carrera : 0,
                                    clave : $('#id_ss_rpt').val()
                                };
                                $.ajax({
                                   data : datos,
                                   type : "POST",
                                   dataType : "json",
                                   async : false,
                                   url : "_Negocio/n_Alumno_Mi_Servicio.php"
                                })
                                   .done(function(respuesta,textStatus,jqXHR){
                                       if (respuesta.success == true){
                                           //recorremos cada registro
                                           $.each(respuesta.data.registros, function( key, value ) {
                                               $('#ServicioSocial').val(value['id_ss']);
                                               $('#cuenta').val(value['id_alumno']);                                               
                                               $('#alumno').val(value['nombre']);
                                               $('#carrera').val(value['descripcion_carrera']);
                                               $('#programa').val(value['id_programa']);
                                                $('#telefono_fijo').val(value['telefono_fijo_alumno']);
                                                $('#telefono_celular').val(value['telefono_celular_alumno']);
                                                $('#descripcion_programa').val(value['descripcion_pss']);
                                               $('#fecha_inicio').val(value['fecha_inicio_ss']);
                                               $('#duracion').val(value['duracion_meses_ss']);
                                               $('#avance_creditos').val(value['avance_creditos_ss']);
                                               $('#porcentaje_avance').val(value['avance_porcentaje_ss']);
                                               $('#jefe_inmediato').val(value['jefe_inmediato_ss']);
                                           });
                                        }
                                       else {
                                            $('#ventanaAviso').html(respuesta.data.message);
                                            $('#ventanaAvisos').dialog('open');                                                                                          
                                       }
                                   })
                                        .fail(function(jqXHR,textStatus,errorThrown){
                                            $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                            $('#ventanaAvisos').dialog('open');                                                                        
                                        });                              
                            
                            
                        },
                        close: function(){
                            $(this).dialog('destroy');
                        },
                        title: 'Información del Servicio Social',
                        modal : false,
                        autoOpen : true,
                        resizable :false,
                        draggable : true,   
                        width : 'auto',
                        height : 'auto',
                        show : 'slide',
                        hide : 'slide',                        
                        position : 'left',
                        closeOnEscape : false
                    });
                });
                
                //FIN MOSTRAMOS UNA VENTANA CON LOS DATOS DEL SERVICIO SOCIAL DEL ALUMNO

                //MOSTRAMOS UNA VENTANA CON LOS DATOS DE LOS REPORTES BIMESTRALES DEL ALUMNO
                 $('#btn_verCalendario_Rpt').on('click',function(e){
                    e.preventDefault();
                    $('#tabla_Mis_Reportes_Bim').dialog({
                        open : function(){
                                var datos = {Tipo_Movimiento : 'OBTENER_DATOS_GENERALES_REPORTES_BIMESTRALES',
                                    id_ss : $('#id_ss_rpt').val()
                                };
                                $.ajax({
                                   data : datos,
                                   type : "POST",
                                   dataType : "json",
                                   url : "_Negocio/n_Alumno_Mis_Reportes.php"
                                })
                                   .done(function(respuesta,textStatus,jqXHR){
                                       var html_table = '<TABLE style="width:100%;">';
                                       html_table += '<TR><TH>Alumno</TH>\n\
                                                    <TH>Id_Servicio</TH>\n\
                                                    <TH>No.<br>Rpt</TH>\n\
                                                    <TH>Vers.</TH>\n\
                                                    <TH>F. Prog. Inicio</TH>\n\
                                                    <TH>F. Prog. Término</TH>\n\
                                                    <TH>Hrs Prog.</TH>\n\
                                                    <TH>F. Real Inicio</TH>\n\
                                                    <TH>F. Real Término</TH>\n\
                                                    <TH>F. de Envío</TH>\n\
                                                    <TH>Hrs realizadas</TH>\n\
                                                    <TH>Estatus</TH>\n\
                                                    <TH>Nota</TH></TR>';
                                       if (respuesta.success == true){
                                           //recorremos cada registro
                                           var horas_obligatorias = 0;
                                           var horas_laboradas = 0;                                           
                                           $.each(respuesta.data.registros, function( key, value ) {
                                                if(value['id_estatus']==1 || value['id_estatus']==2 || value['id_estatus']==3){
                                                    horas_obligatorias += value['horas_obligatorias'];
                                                }
                                                if(value['id_estatus']==3){
                                                    horas_laboradas += value['horas_laboradas'];
                                                }                                               
                                               html_table += '<TR>';
                                               html_table += '<TD>' + value['id_alumno'] + '</TD>';
                                               html_table += '<TD style="text-align:left;">' + value['id_ss'] + '</TD>';
                                               html_table += '<TD style="text-align:left;">' + value['numero_reporte_bi'] + '</TD>';
                                               html_table += '<TD>' + esNulo(value['id_version']) + '</TD>';
                                               html_table += '<TD>' + esNulo(value['fecha_prog_inicio']) + '</TD>';
                                               html_table += '<TD>' + esNulo(value['fecha_prog_fin']) + '</TD>';
                                               html_table += '<TD>' + value['horas_obligatorias'] + '</TD>';                                                                                 
                                               html_table += '<TD>' + esNulo(value['fecha_real_inicio']) + '</TD>';
                                               html_table += '<TD>' + esNulo(value['fecha_real_fin']) + '</TD>';
                                               html_table += '<TD>' + esNulo(value['fecha_recepcion_rpt']) + '</TD>';
                                               html_table += '<TD>' + value['horas_laboradas'] + '</TD>';                                  
                                               html_table += '<TD>' + value['descripcion_estatus'] + '</TD>';                                  
                                               html_table += '<TD>' + value['nota'] + '</TD>';                                  
                                               html_table = html_table + '</TR>';
                                           });
                                            html_table += '<tfoot><TR>';
                                            html_table += '<TD></TD>';
                                            html_table += '<TD></TD>';
                                            html_table += '<TD></TD>';
                                            html_table += '<TD></TD>';
                                            html_table += '<TD>' + horas_obligatorias + '</TD>';                                                                      
                                            html_table += '<TD></TD>';
                                            html_table += '<TD></TD>';
                                            html_table += '<TD></TD>';
                                            html_table += '<TD></TD>';                                   
                                            html_table += '<TD></TD>';                                  
                                            html_table += '<TD>' + horas_laboradas + '</TD>';                                            
                                            html_table += '<TD></TD>';
                                            html_table += '<TD></TD>';
                                            html_table = html_table + '</TR></tfoot>';
                                           
                                           html_table = html_table + '</TABLE>';
                                           $('#tabla_Mis_Reportes_Bim').empty();
                                           $('#tabla_Mis_Reportes_Bim').html(html_table);                                
                                           }
                                       else {
                                           html_table = html_table + '<TR><TD colspan="13">' + respuesta.data.message + '</TD></TR>';
                                           html_table = html_table + '</TABLE>'
                                           $('#tabla_Mis_Reportes_Bim').empty();
                                           $('#tabla_Mis_Reportes_Bim').html(html_table);
                                       }
                                   })
                                        .fail(function(jqXHR,textStatus,errorThrown){
                                            var html_table = '<TABLE class="tabla_Registros">';
                                            html_table += '<TR><TH>Alumno</TH>\n\
                                                         <TH>Id_Servicio</TH>\n\
                                                         <TH>No.Rpt</TH>\n\
                                                         <TH>Vers.</TH>\n\
                                                         <TH>F. Prog. Inicio</TH>\n\
                                                         <TH>F. Prog. Término</TH>\n\
                                                         <TH>Hrs Prog.</TH>\n\
                                                         <TH>F. Real Inicio</TH>\n\
                                                         <TH>F. Real Término</TH>\n\
                                                         <TH>F. de Envío</TH>\n\
                                                         <TH>Hrs realizadas</TH>\n\
                                                         <TH>Estatus</TH>\n\
                                                         <TH>Nota</TH></TR>';

                                            html_table = html_table + '<TR><TD colspan="13">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                            html_table = html_table + '</TABLE>';
                                            $('#tabla_Mis_Reportes_Bim').empty();
                                            $('#tabla_Mis_Reportes_Bim').html(html_table);                                
                                        });                            
                            
                        },
                        close: function(){
                            $(this).dialog('destroy');
                        },
                        title: 'Información de Reportes Bimestrales',
                        modal : false,
                        autoOpen : true,
                        resizable :false,
                        draggable : true,   
                        width : '1000',
                        height : '600',
                        show : 'slide',
                        hide : 'slide',
                        closeOnEscape : false,
                        position : {at: "left top"} 
                    });
                });              
                //FIN MOSTRAMOS UNA VENTANA CON LOS DATOS DE LOS REPORTES BIMESTRALES DEL ALUMNO

                //CLICK AL BOTON ACEPTAR REPORTE
                $('#btn_autorizar_Rpt').on('click',function(e){
                    e.preventDefault();
                    $('#ventanaConfirmar_Aceptacion_Rpt').dialog({
                        buttons:{
                             "Aceptar" : function() {
                                    if(!$('#nota_admin_a').val().match(miExpReg_Nota_Aceptacion)){
                                        $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #');
                                        $('#ventanaAvisos').dialog('open');
                                    }
                                    else{ 
                                        $('#ventanaMensajeConfirma').text('Desea dar por Aceptado este Reporte ?');
                                        $('#ventanaConfirmaVoBo').dialog({
                                            buttons:{
                                                 "Aceptar" : function() {
                                                    $(this).dialog('close');
//                                                    $('#ventanaProcesando').dialog('open');
                                                    var id_ss = $('#id_ss_rpt').val();
                                                    var numero_reporte_bi = $('#numero_reporte_rpt').val();
                                                    var id_version = $('#id_version_rpt').val();
                                                    var id_estatus      =  3; //3.Aceptado
                                                    var id_administrador = $('#Id_Usuario').val();
                                                    var nota=$("#nota_admin_a").val();
                                                    var tipo_Mov ='ACTUALIZAR_ESTATUS_REPORTE';
                                                    var fecha_prog_inicio = $('#fecha_prog_inicio_rpt').val();
                                                    var fecha_prog_fin = $('#fecha_prog_fin_rpt').val();
                                                    var horas_obligatorias = $('#horas_obligatorias_rpt').val();

                                                    var id_Usr_Destinatario = $('#id_alumno_rpt').val();
                                                    var correo_usr = $('#correo_user').val();
                                                    var carrera_usr = $('#carrera_user').val();
                                                    var desc_documento = $('#desc_doc_rpt').val();


                                                    actualiza_Estatus_Reporte(tipo_Mov, id_ss, numero_reporte_bi, id_version, 
                                                    id_estatus, id_administrador, nota, fecha_prog_inicio, fecha_prog_fin, horas_obligatorias,
                                                    id_Usr_Destinatario, correo_usr, carrera_usr, desc_documento);

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
                                     }
                             },
                             "Cancelar" : function() {
                                 $("#nota_admin_a").val('');
                                 $(this).dialog('close');
                             }                       
                        },
                        close : function(){
                            $("#nota_admin_a").val('');
                            $(this).dialog('destroy');
                        },                                                
                        title: 'Nota para la Aceptación del Reporte',
                        modal : true,
                        autoOpen : true,
                        resizable : false,
                        draggable : true,
                        show : 'slide',                        
                        hide : 'slide',
                        height : 'auto',
                        width : '450',
                        dialogClass : 'no-close',
                        closeOnEscape : false
                        
                    }); //FIN ventanaConfirmar_Aceptacion_Rpt
                });
                //FIN CLICK AL BOTON ACEPTAR REPORTE

                //CLICK AL BOTON RECHAZAR REPORTE
                $('#btn_rechazar_Rpt').on('click',function(e){
                    e.preventDefault();                    
                    $('#ventanaConfirmar_Rechazo_Rpt').dialog({
                        buttons:{
                             "Aceptar" : function() {
                                    if(!$('#nota_admin').val().match(miExpReg_Nota_Rechazo)){
                                        $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) . - _ #');
                                        $('#ventanaAvisos').dialog('open');
                                    }
                                    else{
                                        $('#ventanaMensajeConfirma').text('Desea dar por Rechazado este Reporte ?');
                                        $('#ventanaConfirmaVoBo').dialog({
                                            buttons:{
                                                 "Aceptar" : function() {
                                                    $(this).dialog('close');
//                                                    $('#ventanaProcesando').dialog('open');
                                                    var id_ss = $('#id_ss_rpt').val();
                                                    var numero_reporte_bi = $('#numero_reporte_rpt').val();
                                                    var id_version = $('#id_version_rpt').val();
                                                    var id_estatus      =  4; //4.Rechazado
                                                    var id_administrador = $('#Id_Usuario').val();
                                                    var nota= $('#nota_admin').val();
                                                    var tipo_Mov ='ACTUALIZAR_ESTATUS_REPORTE';
                                                    var fecha_prog_inicio = $('#fecha_prog_inicio_rpt').val();
                                                    var fecha_prog_fin = $('#fecha_prog_fin_rpt').val();
                                                    var horas_obligatorias = $('#horas_obligatorias_rpt').val();

                                                    var id_Usr_Destinatario = $('#id_alumno_rpt').val();
                                                    var correo_usr = $('#correo_user').val();
                                                    var carrera_usr = $('#carrera_user').val();
                                                    var desc_documento = $('#desc_doc_rpt').val();

                                                    actualiza_Estatus_Reporte(tipo_Mov, id_ss, numero_reporte_bi, id_version, 
                                                    id_estatus, id_administrador, nota, fecha_prog_inicio, fecha_prog_fin, horas_obligatorias,
                                                    id_Usr_Destinatario, correo_usr, carrera_usr, desc_documento);

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
                                    }
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
                        title: 'Nota para el Rechazo del Reporte',
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
                }); //FIN CONFIRMAR RECHAZO DE REPORTE
                
               function actualiza_Estatus_Reporte(tipo_Mov, id_ss, numero_reporte_bi, id_version, 
                            id_estatus, id_administrador, nota, fecha_prog_inicio, fecha_prog_fin, horas_obligatorias,
                            id_Usr_Destinatario, correo_usr, carrera_usr, desc_documento)
                {
                    $('#ventanaProcesando').dialog('open');
                    $.ajax({
                        data : {Tipo_Movimiento : tipo_Mov,
                                id_ss           : id_ss,
                                numero_reporte_bi: numero_reporte_bi,
                                id_version      : id_version,
                                id_estatus      : id_estatus,
                                id_administrador: id_administrador,
                                nota            : nota,
                                fecha_prog_inicio : fecha_prog_inicio,
                                fecha_prog_fin  : fecha_prog_fin,
                                horas_obligatorias : horas_obligatorias,
                                id_Usr_Destinatario : id_Usr_Destinatario, 
                                correo_usr : correo_usr,
                                carrera_usr : carrera_usr,
                                desc_documento : desc_documento,
                                id_tema_bitacora : '105'  //Aprobación Reportes Bimestrales
                                },
                        type : "POST",
                        dataType : "json",
                        url : "_Negocio/n_administrador_Aprobar_Reporte_Bimestral.php"
                    })
                        .done(function(respuesta,textStatus,jqXHR){
                            if(respuesta.success===true){
                                if(id_estatus==3){ //3.aceptado
                                    $('#ventanaConfirmar_Aceptacion_Rpt').dialog('close');
                                }
                                else{
                                    $('#ventanaConfirmar_Rechazo_Rpt').dialog('close');
                                }
                                $('#btn_autorizar_Rpt').prop("disabled",'disabled');
                                $('#btn_rechazar_Rpt').prop("disabled",'disabled');
                            }                            
                            $('#ventanaProcesando').dialog('close');
                            $('#ventanaAviso').html(respuesta.data.message);
                            $('#ventanaAvisos').dialog('open');   
                        })
                                .fail(function(jqXHR,textStatus,errorThrown){
                                    $('#ventanaProcesando').dialog('close');                                    
                                    $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                    $('#ventanaAvisos').dialog('open');                            
                                });                  
                }
                
                $('#btn_cerrar_Rpt').on('click',function(e){
                    e.preventDefault();
                    $('#message').empty();
//                    $('#ventanaReporteBimestralPDF').dialog('destroy');
                    $(".ui-dialog-content").dialog("close");
                    Obtener_Reportes_Bimestrales(2); //Reportes Bimestrales con estatus 2.Por Autorizar
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
        
                Obtener_Reportes_Bimestrales(2);
                //f5($(document),true);
                $('#ventanaReporteBimestralPDF').hide();
                $('#tabla_Mi_Servicio').hide();
                $('#tabla_Mis_Reportes_Bim').hide();
                $('#ventanaConfirmar_Aceptacion_Rpt').hide();
                $('#ventanaConfirmar_Rechazo_Rpt').hide();
            });
                        
        </script>
        
        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                        <p>Aprobar Reportes Bimestrales</p>
                </div>
            </div>
            <div>
                <div id="tabla_Reportes_Bimestrales" class="tabla_Registros">
                </div>
            </div>
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
        </div>
        
        <div id="ventanaReporteBimestralPDF">
            <div id="archivoPDF_rptbim" style="position: absolute; z-index: 99; float: left; border: 1px grey solid; height: 555px; width: 700px;">
                <object id="obj_PDF_rptBim" width="700px" height="555px"></object>                
            </div>            
            
            <div id="divOpciones_RB" style="text-align: center; float: right; width: 200px; line-height: 2.5em;">
                <button id="btn_verServicioSocial_Rpt" class="btn_Herramientas" style="width: 160px;">Ver Servicio Social</button>
                <button id="btn_verCalendario_Rpt" class="btn_Herramientas" style="width: 160px;">Ver Calendario Rpts.</button>
                <button id="btn_autorizar_Rpt" class="btn_Herramientas" style="width: 160px;">Aceptar Reporte</button>
                <button id="btn_rechazar_Rpt" class="btn_Herramientas" style="width: 160px;">Rechazar Reporte.</button>
                <button id="btn_cerrar_Rpt" class="btn_Herramientas" style="width: 160px;">Cerrar</button>
                
                <input type="hidden" id="id_ss_rpt" name="id_ss_rpt" value="">
                <input type="hidden" id="id_alumno_rpt" name="id_alumno_rpt" value="">
                <input type="hidden" id="numero_reporte_rpt" name="numero_reporte_rpt" value="">
                <input type="hidden" id="id_version_rpt" name="id_version_rpt" value="">
                <input type="hidden" id="fecha_prog_inicio_rpt" name="fecha_prog_inicio_rpt" value="">
                <input type="hidden" id="fecha_prog_fin_rpt" name="fecha_prog_fin_rpt" value="">
                <input type="hidden" id="horas_obligatorias_rpt" name="horas_obligatorias_rpt" value="">
                <input type="hidden" id="id_carrera_rpt" name="id_carrera_rpt" value="">
                <input type="hidden" id="desc_doc_rpt" name="desc_doc_rpt" value="">
                
                <input type="hidden" id="correo_user" name="correo_user" value="">
                <input type="hidden" id="carrera_user" name="correo_user" value="">
                
            </div>
            
        </div>
        
        <div id="tabla_Mi_Servicio" class="contenido_Formulario ventanaInformativa">
            <p>
                <div style="display: block;">
                    <p>
                        <label for="ServicioSocial" class="label">Servicio Social:</label>
                        <input type="text" name="ServicioSocial" id="ServicioSocial" class="ventanaInformativa" readonly value=""/>   
                    </p>                

                    <p>
                        <label for="cuenta" class="label">Cuenta:</label>
                        <input type="text" name="cuenta" id="cuenta" class="ventanaInformativa" readonly value=""/>   
                    </p>                
                    <p>
                        <label for="alumno" class="label">Alumno:</label>
                        <input type="text" name="alumno" id="alumno" class="ventanaInformativa" readonly value=""/>
                    </p> 
                    <p>
                        <label for="carrera" class="label">Carrera:</label>
                        <input type="text" name="carrera" id="carrera" class="ventanaInformativa" readonly value=""/>
                    </p>
                    <p>
                        <label for="telefono_fijo" class="label">Tel. Fijo:</label>
                        <input type="text" name="telefono_fijo" id="telefono_fijo" class="ventanaInformativa" readonly value=""/>
                    </p> 
                    <p>
                        <label for="telefono_celular" class="label">Celular:</label>
                        <input type="text" name="telefono_celular" id="telefono_celular" class="ventanaInformativa" readonly value=""/>
                    </p>                                                                                     
                    <p>
                        <label for="programa" class="label">Programa Serv.Soc.:</label>
                        <input type="text" name="programa" id="programa" class="ventanaInformativa" readonly value=""/>
                    </p> 
                    <p>
                        <label for="descripcion_programa" class="label">Descripción:</label>
                        <textarea name="descripcion_programa" id="descripcion_programa" class="ventanaInformativa" readonly value=""/>
                    </p>                                                     
                    <p>
                        <label for="fecha_inicio" class="label">Fecha de inicio:</label>
                        <input type="text" name="fecha_inicio" id="fecha_inicio" class="ventanaInformativa" readonly value=""/>
                    </p> 
                    <p>
                        <label for="duracion" class="label">Duración (meses):</label>
                        <input type="text" name="duracion" id="duracion" class="ventanaInformativa" readonly value=""/>
                    </p> 
                    <p>
                        <label for="avance_creditos" class="label">Avance en creditos:</label>
                        <input type="text" name="avance_creditos" id="avance_creditos" class="ventanaInformativa" readonly value=""/>
                    </p> 
                    <p>
                        <label for="porcentaje_avance" class="label">Porcentaje de avance:</label>
                        <input type="text" name="porcentaje_avance" id="porcentaje_avance" class="ventanaInformativa" readonly value=""/>
                    </p> 
                    <p>
                        <label for="jefe_inmediato" class="label">Jefe inmediato:</label>
                        <input type="text" name="jefe_inmediato" id="jefe_inmediato" class="ventanaInformativa" readonly value=""/>
                    </p>
                </div>   
            </p>
        </div>
        
        <div id="tabla_Mis_Reportes_Bim" class="tabla_Registros">            
        </div>
        
        <div id="ventanaConfirmar_Aceptacion_Rpt">           
            <p>
                <textarea id="nota_admin_a" class="entrada_Dato notaVoBo" maxlength="500" onkeyup="javascript:this.value=this.value.toUpperCase();"
                          title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
            </p>             
        </div>
        
        <div id="ventanaConfirmar_Rechazo_Rpt">            
            <p>
                <textarea id="nota_admin" class="entrada_Dato notaVoBo" maxlength="500" onkeyup="javascript:this.value=this.value.toUpperCase();"
                         title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
            </p>             
        </div>
        <div id="ventanaConfirmaVoBo" name="ventanaConfirmaVoBo">
            <span id="ventanaMensajeConfirma"></span>
        </div>
        
    <div id="ventanaAvisos"   >
            <span id="ventanaAviso"></span>
        </div>
        <div id="ventanaProcesando" data-role="header">
            <img id="cargador" src="css/images/engrane2.gif"/><br>
            Procesando su transacción....!<br>
            Espere por favor.
        </div>

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
<!DOCTYPE html>
<!--
Fecha:          Julio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para aprobar las Cartas de Terminación
-->
<html>
    <head>
<!--        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/jquery-ui.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="menu/estilo_menu.css" /> 
        <script src="js/jquery-1.12.4.min.js"></script>
        <script src="js/jquery-ui.min.js"></script>-->
        <script src="js/expresiones_reg.js"></script>
        <script src="js/ruta_documentos.js"></script>      
        
        <script>
            $( document ).ready(function() {
                               
                //OBTENEMOS LAS CARTAS DE TERMINACIÓN
                function Obtener_Cartas_Terminacion(id_estatus){
                    var datos = {Tipo_Movimiento : 'OBTENER_CARTAS_TERMINACION',
                               id_estatus : id_estatus
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_administrador_Aprobar_Carta_Terminacion.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE style="width:100%;">';
                           html_table = html_table + '<TR><TH>Alumno</TH>\n\
                                                      <TH>Servicio Social</TH>\n\
                                                      <TH>Programa</TH>\n\
                                                      <TH>Fecha de Inicio</TH>\n\
                                                      <TH>Duracion (meses)</TH>\n\
                                                      <TH>Estatus S.S.</TH>\n\
                                                      <TH>Hrs. Obligatorias</TH>\n\
                                                      <TH>Hrs. Realizadas</TH>\n\
                                                      <TH>Hrs. Pendientes</TH>\n\
                                                      <TH>Acción</TH></TR>';
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                   $btn_RevisarDoc = '<button class="btn_RevisarDoc btnOpcion" data-id_alumno=\'' + value['id_alumno'] + '\'' + 
                                            ' data-id_ss=\'' + value['id_ss'] + '\' data-id_documento=' + value['id_documento'] +
                                            ' data-id_version = ' + value['id_version'] + ' ' +
                                            ' data-id_carrera = \'' + value['id_carrera'] + '\' ' +
                                            ' data-desc_corta_doc = \'' + value['descripcion_para_nom_archivo'] + '\' ' +
                                            ' data-correo = \'' + value['email_usuario'] + '\' >Revisar Doc</button>';
                    
                                   html_table = html_table + '<TR>';
                                   html_table = html_table + '<TD>' + value['id_alumno'] + '</TD>';
                                   html_table = html_table + '<TD>' + value['id_ss'] + '</TD>';
                                   html_table = html_table + '<TD>' + value['id_programa'] + '</TD>';
                                   html_table = html_table + '<TD style="text-align:left;">' + value['fecha_inicio_ss'] + '</TD>';
                                   html_table = html_table + '<TD style="text-align:left;">' + value['duracion_meses_ss'] + '</TD>';
                                   html_table = html_table + '<TD>' + value['descripcion_estatus'] + '</TD>';
                                   html_table = html_table + '<TD>' + value['horas_obligatorias'] + '</TD>';
                                   html_table = html_table + '<TD>' + value['horas_laboradas'] + '</TD>';
                                   html_table = html_table + '<TD>' + value['horas_pendientes'] + '</TD>';
                                   html_table = html_table + '<TD>' + $btn_RevisarDoc + '</TD>';
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Cartas_Termino').empty();
                               $('#tabla_Cartas_Termino').html(html_table);                                
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="10">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Cartas_Termino').empty();
                               $('#tabla_Cartas_Termino').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros">';
                           var html_table = '<TABLE class="tabla_Registros">';
                           var html_table = '<TABLE class="tabla_Registros">';
                           html_table = html_table + '<TR><TH>Alumno</TH>\n\
                                                      <TH>Servicio Social</TH>\n\
                                                      <TH>Programa</TH>\n\
                                                      <TH>Fecha de Inicio</TH>\n\
                                                      <TH>Duracion (meses)</TH>\n\
                                                      <TH>Estatus S.S.</TH>\n\
                                                      <TH>Hrs. Obligatorias</TH>\n\
                                                      <TH>Hrs. Realizadas</TH>\n\
                                                      <TH>Hrs. Pendientes</TH>\n\
                                                      <TH>Acción</TH></TR>';
                                html_table = html_table + '<TR><TD colspan="10">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Cartas_Termino').empty();
                                $('#tabla_Cartas_Termino').html(html_table);                                
                            });    
                }
                //FIN OBTENEMOS LAS CARTAS DE TERMINACION

                //EVENTO CLICK SOBRE EL BOTON REVISAR CARTA DE TERMINACION
                $('#tabla_Cartas_Termino').on("click", "button.btn_RevisarDoc", function(e){
                    e.preventDefault();
                    var id_alumno = $(this).data('id_alumno');
                    $('#id_alumno_ct').attr("value", $(this).data('id_alumno'));
                    $('#id_ss_ct').attr("value", $(this).data('id_ss'));
                    $('#id_documento_ct').attr("value", $(this).data('id_documento'));
                    $('#id_version_ct').attr("value", $(this).data('id_version'));
                    $('#correo_user').attr("value", $(this).data('correo'));
                    $('#carrera_user').attr("value", $(this).data('id_carrera'));
                    $('#desc_corta_doc').attr("value", $(this).data('desc_corta_doc'));

                    var tituloAdjuntar = id_alumno + "_" + 
                                       $('#carrera_user').val() + "_" +
                                       $('#id_ss_ct').val() + "_" +
                                       $('#id_version_ct').val() + "_" +
                                       $('#desc_corta_doc').val() + ".pdf";
                    
                    $('#ventanaCartaTerminacionPDF').dialog({                      
                        open : function(){
                               $('#ventanaCartaTerminacionPDF').dialog("option","title", tituloAdjuntar);
                               var tiempo = new Date();
                               var fileName ="Docs/Carta_Terminacion/" + 
                                       id_alumno + "_" + 
                                       $('#carrera_user').val() + "_" +
                                       $('#id_ss_ct').val() + "_" +
                                       $('#id_version_ct').val() + "_" +
                                       $('#desc_corta_doc').val() + ".pdf?" + tiempo; 
                               
                               var new_Object = $('#obj_PDF_cartaTerminacion').clone(false);
                               new_Object.attr("type", "application/pdf");
                               new_Object.attr("data", fileName);
//                               new_Object.attr("src", fileName);
                               $("#obj_PDF_cartaTerminacion").replaceWith(new_Object);    
                               $('#btn_autorizar_CT').prop("disabled",'');
                               $('#btn_rechazar_CT').prop("disabled",'');

                        },
                        title: 'Aprobar Carta de Terminación',
                        modal : true,
                        autoOpen : true,
                        resizable :false,
                        draggable : true,   
                        width : '1000',
                        height : '630',
                        dialogClass : 'no-close',
                        closeOnEscape : false,                        
                        position : {at: 'right top'}
                    });
                });
                //FIN EVENTO CLICK SOBRE EL BOTON REVISAR CARTA TERMINACION

                //MOSTRAMOS UNA VENTANA CON LOS DATOS DEL SERVICIO SOCIAL DEL ALUMNO
                $('#btn_verServicioSocial_CT').on('click',function(e){
                    e.preventDefault();
                    $('#tabla_Mi_Servicio').dialog({
                        open : function(){
                                var datos = {Tipo_Movimiento : 'OBTENER_DATOS_GENERALES_SS',
                                    Id_Usuario : 0,
                                    Id_Carrera : 0,
                                    clave : $('#id_ss_ct').val()
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
                        resizable :true,
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
                 $('#btn_verCalendario_CT').on('click',function(e){
                    e.preventDefault();
                    $('#tabla_Mis_Reportes_Bim').dialog({
                        open : function(){
                                var datos = {Tipo_Movimiento : 'OBTENER_DATOS_GENERALES_REPORTES_BIMESTRALES',
                                    id_ss : $('#id_ss_ct').val()
                                };
                                $.ajax({
                                   data : datos,
                                   type : "POST",
                                   dataType : "json",
                                   url : "_Negocio/n_Alumno_Mis_Reportes.php"
                                })
                                   .done(function(respuesta,textStatus,jqXHR){
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
                                                nom_file= value['id_alumno']+'_'+
                                                value['id_carrera']+'_'+
                                                value['id_ss']+'_'+
                                                value['numero_reporte_bi']+'_'+
                                                value['id_version']+'_Reporte_Bimestral.pdf';
                                                fecha_=new Date();
                                                console.log(nom_file);
                                                ruta_doc= ruta_docs_reportes_bimestrales+nom_file+'?'+ fecha_;                                                          
                                               html_table += '<TR>';
                                               html_table += '<TD>' + value['id_alumno'] + '</TD>';
                                               html_table += '<TD style="text-align:left;">' + value['id_ss'] + '</TD>';
                                               if(value['id_estatus'] == 2 || value['id_estatus']==3){
                                                html_table += '<TD><a class="link_pdf" style="padding:5px; font-weight: bold; \n\
                                                background-color: #bdb76b; text-decoration:underline;" target="_blank" href="'+
                                               ruta_doc +'">' + value['numero_reporte_bi'] + '</a></TD>';
                                               }else{
                                                html_table += '<TD style="text-align:left;">' + value['numero_reporte_bi'] + '</TD>';                                                
                                               }
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
                                           html_table = html_table + '<TR><TD colspan="12">' + respuesta.data.message + '</TD></TR>';
                                           html_table = html_table + '</TABLE>'
                                           $('#tabla_Mis_Reportes_Bim').empty();
                                           $('#tabla_Mis_Reportes_Bim').html(html_table);
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
                                                         <TH>Estatus</TH>\n\
                                                         <TH>Nota</TH></TR>';

                                            html_table = html_table + '<TR><TD colspan="12">' + textStatus + '. ' + errorThrown + '</TD></TR>';
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
                        position : {at: "left top"},
                        closeOnEscape : false                        
                    });
                });              
                //FIN MOSTRAMOS UNA VENTANA CON LOS DATOS DE LOS REPORTES BIMESTRALES DEL ALUMNO

                //CLICK AL BOTON ACEPTAR CARTA DE TERMINACION
                $('#btn_autorizar_CT').on('click',function(e){
                    e.preventDefault();
                    $('#ventanaConfirmar_Aceptacion_CT').dialog({
                        buttons:{
                             "Aceptar" : function() {
                                    if(!$('#nota_admin_a').val().match(miExpReg_Nota_Aceptacion)){
                                        $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #');
                                        $('#ventanaAvisos').dialog('open');
                                    }
                                    else{
                                        $('#ventanaMensajeConfirma').text('Desea dar por Aceptada esta Carta de Terminación ?');
                                        $('#ventanaConfirmaVoBo').dialog({
                                            buttons:{
                                                 "Aceptar" : function() {
                                                    $(this).dialog('close');
                                                    var id_ss = $('#id_ss_ct').val();
                                                    var id_documento = $('#id_documento_ct').val();
                                                    var id_version = $('#id_version_ct').val();
                                                    var id_estatus      =  3; //3.Aceptado
                                                    var id_administrador = $('#Id_Usuario').val();
                                                    var nota=$('#nota_admin_a').val();
                                                    var tipo_Mov ='ACTUALIZAR_ESTATUS_CARTA_TERMINACION';

                                                    var id_Usr_Destinatario = $('#id_alumno_ct').val();
                                                    var correo_usr = $('#correo_user').val();
                                                    var carrera_usr = $('#carrera_user').val();
                                                    var desc_documento = $('#desc_corta_doc').val();

                                                    actualiza_Estatus_Documento(tipo_Mov, id_ss, id_documento, id_version, 
                                                        id_estatus, id_administrador, nota, id_Usr_Destinatario, correo_usr, carrera_usr, desc_documento);

                                                    tipo_Mov ='ACTUALIZAR_ESTATUS_SERVICIO_SOCIAL';
                                                    id_estatus =  8; //8. Terminado
                                                    
                                                    actualiza_Estatus_Servicio_Social(tipo_Mov, id_ss, id_estatus, id_administrador, id_Usr_Destinatario, correo_usr, carrera_usr);

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
                                 $('#nota_admin_a').val('');
                                 $(this).dialog('close');
                             }                       
                        },
                        close : function(){
                            $("#nota_admin_a").val('');
                            $(this).dialog('destroy');
                        },                                                
                        title: 'Nota para la Aceptación de la Carta de Terminación',
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
                    }); //FIN ventanaConfirmar_Aceptacion_CT
                });
                //FIN CLICK AL BOTON ACEPTAR CARTA DE TERMINACIÓN

                //CLICK AL BOTON RECHAZAR CARTA DE TERMINACION
                $('#btn_rechazar_CT').on('click',function(e){
                    e.preventDefault();
                    $('#ventanaConfirmar_Rechazo_CT').dialog({
                        buttons:{
                             "Aceptar" : function() {
                                    if(!$('#nota_admin').val().match(miExpReg_Nota_Rechazo)){
                                        $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) . - _ #');
                                        $('#ventanaAvisos').dialog('open');
                                    }
                                    else{       
                                        $('#ventanaMensajeConfirma').text('Desea dar por Rechazada esta Carta de Terminación ?');
                                        $('#ventanaConfirmaVoBo').dialog({
                                            buttons:{
                                                 "Aceptar" : function() {
                                                    $(this).dialog('close');
                                                    var id_ss = $('#id_ss_ct').val();
                                                    var id_documento = $('#id_documento_ct').val();
                                                    var id_version = $('#id_version_ct').val();
                                                    var id_estatus =  4; //4.Rechazado
                                                    var id_administrador = $('#Id_Usuario').val();
                                                    var nota= $('#nota_admin').val();
                                                    var tipo_Mov ='ACTUALIZAR_ESTATUS_CARTA_TERMINACION';

                                                    var id_Usr_Destinatario = $('#id_alumno_ct').val();
                                                    var correo_usr = $('#correo_user').val();
                                                    var carrera_usr = $('#carrera_user').val();
                                                    var desc_documento = $('#desc_corta_doc').val();

                                                    actualiza_Estatus_Documento(tipo_Mov, id_ss, id_documento, id_version, 
                                                    id_estatus, id_administrador, nota, id_Usr_Destinatario, correo_usr, carrera_usr, desc_documento);
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
                        title: 'Nota de Rechazo para la Carta de Aceptación',
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
                }); //FIN CONFIRMAR RECHAZO DE LA CARTA DE TERMINACIÓN

                function actualiza_Estatus_Documento(tipo_Mov, id_ss, id_doc, id_version, id_estatus, id_administrador, nota, id_Usr_Destinatario, correo_usr, carrera_usr, desc_documento)
                {
                    $('#ventanaProcesando').dialog('open');                    
                    $.ajax({
                        data : {Tipo_Movimiento : tipo_Mov,
                                id_ss           : id_ss,
                                id_doc          : id_doc,
                                id_version      : id_version,
                                id_estatus      : id_estatus,
                                id_administrador: id_administrador,
                                nota            : nota,
                                id_Usr_Destinatario : id_Usr_Destinatario,
                                correo_usr : correo_usr, 
                                carrera_usr : carrera_usr, 
                                desc_documento : desc_documento                                
                                },
                        type : "POST",
                        async : false,
                        dataType : "json",
                        url : "_Negocio/n_administrador_Aprobar_Carta_Terminacion.php"
                    })
                        .done(function(respuesta,textStatus,jqXHR){
                            if(respuesta.success===true){
                                if(id_estatus==3){ //3.aceptado
                                    $('#ventanaConfirmar_Aceptacion_CT').dialog('close');
                                }
                                else{
                                    $('#ventanaConfirmar_Rechazo_CT').dialog('close');
                                }
                                $('#btn_autorizar_CT').prop("disabled",'disabled');
                                $('#btn_rechazar_CT').prop("disabled",'disabled');
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
                
                function actualiza_Estatus_Servicio_Social(tipo_Mov, id_ss, id_estatus, id_administrador, id_Usr_Destinatario, correo_usr, carrera_usr)
                {
                    $('#ventanaProcesando').dialog('open');                    
                    $.ajax({
                        data : {Tipo_Movimiento : tipo_Mov,
                                id_ss           : id_ss,
                                id_estatus      : id_estatus,
                                id_Usr_Destinatario : id_Usr_Destinatario,
                                correo_usr : correo_usr,
                                carrera_usr : carrera_usr,
                                id_administrador : id_administrador
                                
                                },
                        type : "POST",
                        dataType : "json",
                        async : false,
                        url : "_Negocio/n_administrador_Aprobar_Carta_Terminacion.php"
                    })
                        .done(function(respuesta,textStatus,jqXHR){
                            if(respuesta.success===true){                            
                                if(id_estatus==8){ //8.Terminado
                                    $('#ventanaConfirmar_Aceptacion_CT').dialog('close');
                                }
                                else{
                                    $('#ventanaConfirmar_Rechazo_CT').dialog('close');
                                }
                                $('#btn_autorizar_CT').prop("disabled",'disabled');
                                $('#btn_rechazar_CT').prop("disabled",'disabled');
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
                //FIN FUNCIÓN PARA ACTUALIZAR EL ESTATUS DEL SERVICIO SOCIAL
                
                $('#btn_cerrar_CT').on('click',function(e){
                    e.preventDefault();
                    Obtener_Cartas_Terminacion(2); //Cartas Terminación con estatus 2.Por Autorizar
                    $('#message').empty();
//                    $('#ventanaCartaTerminacionPDF').dialog('destroy');
                      $(".ui-dialog-content").dialog("close");
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
                /*$('.entrada_Dato').focus(function(e){
                    e.preventDefault();
                    f5($(document),false);
                });
                $('.entrada_Dato').blur(function(e){
                    e.preventDefault();
                    f5($(document),true);
                });*/
                
                Obtener_Cartas_Terminacion(2);
//                f5($(document),true);
                $('#ventanaCartaTerminacionPDF').hide();
                $('#tabla_Mi_Servicio').hide();
                $('#tabla_Mis_Reportes_Bim').hide();
//                $('#tabla_Cartas_Termino').hide();
                $('#ventanaConfirmar_Aceptacion_CT').hide();
                $('#ventanaConfirmar_Rechazo_CT').hide();
                
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
                    <p >Aprobar Carta de Terminación</p>
                </div>
                <div>
                    <div>
                        <div id="tabla_Cartas_Termino" class="tabla_Registros">
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
        </div>
        
        <div id="ventanaCartaTerminacionPDF">
            <div id="archivoPDF_cartaterminacion" style="position: absolute; z-index: 99; float: left; border: 1px grey solid; height: 550px; width: 700px; ">
                <object id="obj_PDF_cartaTerminacion" width="700px" height="550px">
                </object>
                
<!--                <embed id="obj_PDF_cartaTerminacion" width="700px" height="615px" src="" type="">
-->
                <!--<iframe id="obj_PDF_cartaTerminacion" width="700px" height="615px" src="">
                </iframe>-->

                
            </div>            
            <div id="divOpciones_CT" style="text-align: center; float: right; width: 200px; line-height: 2.5em;">
                <button id="btn_verServicioSocial_CT" class="btn_Herramientas" style="width: 160px;">Ver Servicio Social</button>
                <button id="btn_verCalendario_CT" class="btn_Herramientas" style="width: 160px;">Ver Calendario Rpts.</button>
                <button id="btn_autorizar_CT" class="btn_Herramientas" style="width: 160px;">Aceptar Crta. Term.</button>
                <button id="btn_rechazar_CT" class="btn_Herramientas" style="width: 160px;">Rechazar Crta. Term.</button>
                <button id="btn_cerrar_CT" class="btn_Herramientas" style="width: 160px;">Cerrar</button>
                
                <input type="hidden" id="id_ss_ct" name="id_ss_ct" value="">
                <input type="hidden" id="id_alumno_ct" name="id_alumno_ct" value="">
                <input type="hidden" id="id_documento_ct" name="id_documento_ct" value="">
                <input type="hidden" id="id_version_ct" name="id_version_ct" value="">

                <input type="hidden" id="correo_user" name="correo_user" value="">
                <input type="hidden" id="carrera_user" name="carrera_user" value="">
                <input type="hidden" id="desc_corta_doc" name="desc_corta_doc" value="">

            </div>
            
        </div>
        
        <div id="tabla_Mi_Servicio" class="contenido_Formulario ventanaInformativa">
            <p>
                <div style="display: block;">
                    <p>
                        <label for="ServicioSocial" class="label" >Servicio Social:</label>
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
        
        <div id="tabla_Mis_Reportes_Bim">            
        </div>
        
        <div id="ventanaConfirmar_Aceptacion_CT">            
            <p>
                <textarea id="nota_admin_a" class="entrada_Dato notaVoBo" maxlength="500" onkeyup="javascript:this.value=this.value.toUpperCase();"
                          title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
            </p>             
            <!--<span style="margin-top: 5px; color: #990000;">Desea asignar el Estatus de "Aceptado" a la Carta de Terminación seleccionada ?</span>-->
            
        </div>
        <div id="ventanaConfirmar_Rechazo_CT">
            
            <p>
                <textarea id="nota_admin" class="entrada_Dato notaVoBo" maxlength="500" onkeyup="javascript:this.value=this.value.toUpperCase();" 
                          title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
            </p>             
            <!--<span style="margin-top: 5px; color: #990000;">Desea dar el estatus de "Rechazado" a la Carta de Terminación seleccionada ?</span>-->
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
<!--    </body>
</html>-->

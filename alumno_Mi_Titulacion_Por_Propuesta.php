<!DOCTYPE html>
<!--
Fecha:          Julio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para la inscripción a Propuestas de Titulación
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
        <script src="js/ruta_documentos.js"></script>        
        
        <script>
            $( document ).ready(function() {
                //OBTENEMOS LAS CARRERAS EN LAS QUE ESTA INSCRITO EL ALUMNO PARA EL LISTBOX
                function Obtener_Carreras_Del_Alumno(){                                           
                    var datos = {Tipo_Movimiento : 'OBTENER',
                               Id_Usuario : $('#Id_Usuario').val(),
                               Id_Carrera : 0
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_Alumno_Carrera.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_options='';
                           if (respuesta.success == true){
                                //recorremos cada registro
                                $.each(respuesta.data.registros, function( key, value ) {
                                    //recorremos los valores de cada usuario
                                    html_options = html_options + '<option value=' + value['id_carrera'] +
                                            '>' + value['descripcion_carrera'] + '</option>';
                                });
                                $('#selec_Mis_Carreras').empty();
                                $('#selec_Mis_Carreras').html(html_options);
                                
                                $('#selec_Mis_Carreras option:first-child').attr('selected','selected');
                                $('#Id_Carrera').val($('#selec_Mis_Carreras').val());
                                Obtener_Mis_Inscripciones($('#Id_Usuario').val(), $('#Id_Carrera').val());
                            }
                            else {
                                $('#Agregar_Servicio').prop('disable',true);
                                $('#ventanaAviso').html(respuesta.data.message);
                                $('#ventanaAvisos').dialog('open');                                                                    
                            }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                                $('#ventanaAvisos').dialog('open');                            
                            });                 
                                       
                }//FIN OBTENEMOS LAS CARRERAS EN LAS QUE ESTA INSCRITO EL ALUMNO

                $('#selec_Mis_Carreras').change(function(e){
                    e.preventDefault();
                    var id_carrera_sel = $(this).val();
                    $('#Id_Carrera').val(id_carrera_sel);
                    Obtener_Mis_Inscripciones($('#Id_Usuario').val() ,id_carrera_sel);
                });
                                               
                //OBTENEMOS LAS INSCRIPCIONES A PROPUESTAS 
                function Obtener_Mis_Inscripciones(id_alumno, id_carrera){
                  var deshabilitaAgregar = false;
                    var datos = {Tipo_Movimiento : 'OBTENER_MIS_INSCRIPCIONES',
                               id_alumno : id_alumno,
                               id_carrera : id_carrera
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_Alumno_Mi_Titulacion_Por_Propuesta.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE style="width:100%;">';
                           html_table += '<TR><TH>Inscricion</TH>\n\
                                        <TH>Propuesta</TH>\n\
                                        <TH>Título</TH>\n\
                                        <TH>Tipo</TH>\n\
                                        <TH>Profesor</TH>\n\
                                        <TH>Fecha Inscripción</TH>\n\
                                        <TH>Fecha de Baja</TH>\n\
                                        <TH>Estatus</TH>\n\
                                        <TH>Motivo de Baja</TH>\n\
                                        <TH>Acción</TH></TR>';
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                console.log(value['id_estatus'])
                                if (value['id_estatus'] != 4 && value['id_estatus'] != 13 && value['id_estatus'] != 14 && value['id_estatus'] != 15 ){
                                      deshabilitaAgregar = true;
                                    }
                                    var btn_EnviarDoc ='';
                                    var btn_BorrarDoc ='';
                                    btn_EnviarDoc = '<button class="btn_EnviarDoc btnOpcion" data-id_alumno=\'' + $('#Id_Usuario').val() + '\' ' + 
                                     'data-id_propuesta=\'' + value['id_propuesta'] + 
                                     '\' data-id_inscripcion=\'' + value['id_inscripcion'] + 
                                     '\' data-id_carrera=' + id_carrera +
                                     ' data-id_estatus = ' + value['id_estatus'] + '>Enviar Doc</button>';
                                    if(parseInt(value['id_estatus'])==3 || parseInt(value['id_estatus'])==10){ //3. Aceptado 10.Por Autorizar Prof
                                        btn_BorrarDoc = '<button class="btn_BorrarDoc btnOpcion" style="margin-top:4px;" data-id_alumno=\'' + $('#Id_Usuario').val() + '\' ' + 
                                         'data-id_propuesta=\'' + value['id_propuesta'] + 
                                         '\' data-id_inscripcion=\'' + value['id_inscripcion'] + 
                                         '\' data-id_carrera= \'' + id_carrera + 
                                         '\' data-titulo_propuesta = \'' + value['titulo_propuesta'] +
                                         '\' data-id_estatus = ' + value['id_estatus'] + '>Enviar Baja</button>';
                                    }
                                   nom_file= value['id_profesor']+'_'+
                                           value['id_propuesta']+'_'+
                                           value['version_propuesta']+'_'+
                                           value['descripcion_para_nom_archivo']+'.pdf';
                                   fecha_=new Date();
                                   ruta_doc= ruta_docs_propuestas_profesor+nom_file+'?'+ fecha_;
                                    
                                   html_table += '<TR>';
                                   html_table += '<TD>' + value['id_inscripcion'] + '</TD>';
                                   html_table += '<TD>' + value['id_propuesta'] + '</TD>';
                                   if(value['id_estatus'] == 2 || value['id_estatus']==3){
                                   html_table = html_table + '<TD style="text-align:left;"><a class="link_pdf" target="_blank" href="' + 
                                           ruta_doc +'">' + value['titulo_propuesta'] + '</a></TD>';
                                    }
                                    else{
                                        html_table = html_table + '<TD style="text-align:left;">' + value['titulo_propuesta'] + '</TD>';

                                    }

                                   html_table += '<TD>' + value['descripcion_tipo_propuesta'] + '</TD>';
                                   html_table += '<TD style="text-align:left;">' + value['nom_profesor'] + '</TD>';
                                   html_table += '<TD>' + esNulo(value['fecha_inscripcion']) + '</TD>';
                                   html_table += '<TD>' + esNulo(value['fecha_baja']) + '</TD>';
                                   html_table += '<TD>' + value['descripcion_estatus'] + '</TD>';
                                   html_table += '<TD>' + esNulo(value['nota_baja']) + '</TD>';
                                   html_table += '<TD>' + btn_EnviarDoc + btn_BorrarDoc + '</TD>';
                                   html_table = html_table + '</TR>';

                                                                      // Mostrar modal para los alumnos que no estén titulados y aún no hayan llenado los datos para poner fecha estimada de titulación y motivo.
                                   if(value['id_estatus'] != 7 && value['id_estatus'] != 6 && value['id_estatus'] != 13 && value['id_estatus'] != 14 && value['id_estatus'] != 15 && !value['llenofechaestimada']) {
                                    var id_inscripcion = value['id_inscripcion'];

                                    console.log(id_inscripcion);
                                    
                                   }

                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Mis_Inscripciones').empty();
                               $('#tabla_Mis_Inscripciones').html(html_table);                                
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="10">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Mis_Inscripciones').empty();
                               $('#tabla_Mis_Inscripciones').html(html_table);
                           }
                           if(deshabilitaAgregar){
                            $('#btn_Agregar_PT').attr("disabled", true);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros">';
                                html_table += '<TR><TH>Inscricion</TH>\n\
                                             <TH>Propuesta</TH>\n\
                                             <TH>Título</TH>\n\
                                             <TH>Tipo</TH>\n\
                                             <TH>Profesor</TH>\n\
                                             <TH>Fecha Inscripción</TH>\n\
                                             <TH>Fecha de Baja</TH>\n\
                                             <TH>Estatus</TH>\n\
                                             <TH>Motivo de Baja</TH>\n\
                                             <TH>Acción</TH></TR>';
                                html_table = html_table + '<TR><TD colspan="10">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Mis_Inscripciones').empty();
                                $('#tabla_Mis_Inscripciones').html(html_table);                                
                            });                                                                                                                                   
                }
                //FIN OBTENEMOS LAS PROPUESTAS

                $('#tabla_Mis_Inscripciones').on("click", "button.btn_EnviarDoc", function(e){
                    e.preventDefault();
                    var id_inscripcion = $(this).data("id_inscripcion");
                    var id_documento = 2; //historial académico
                    Obtener_Mis_Documentos_Enviados(id_inscripcion, id_documento);
                    $('#ventanaDocumentosEnviados_Propuesta').dialog('open');
                });
                
                $('#tabla_Mis_Inscripciones').on("click", "button.btn_BorrarDoc", function(e){
                    e.preventDefault();
                    var id_estatus = $(this).data("id_estatus");
                    var titulo_propuesta = $(this).data("titulo_propuesta");
                    var id_alumno = $(this).data("id_alumno");
                    var id_inscripcion = $(this).data("id_inscripcion");
                    var id_propuesta = $(this).data("id_propuesta");
                    var id_carrera = $('#Id_Carrera').val();
                    var id_documento = 5; //baja de inscripción
                    if (parseInt(id_estatus) == 10) { //10. Por Aut Profesor
                        $('#ventanaConfirmarBorrado_Inscripcion').dialog({
                               buttons:{
                                    "Aceptar" : function() {
                                        if(!$('#nota').val().match(miExpReg_Nota_Rechazo)){
                                            $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #');
                                            $('#ventanaAvisos').dialog('open');
                                        }
                                        else{ 
                                            $('#MensajeConfirmaBorrar').text('Desea dar de Baja esta Solicitud de Inscripción ?');
                                            $('#ventanaConfirmacionBorrar').dialog({
                                                buttons:{
                                                     "Aceptar" : function() {                            
                                                            $(this).dialog('close');
                                                            $('#ventanaProcesando').dialog('open');

                                                            // Por Ajax damos de baja la Inscripción
                                                            $.ajax({
                                                                data : {Tipo_Movimiento : 'BORRAR_INSCRIPCION',
                                                                        id_inscripcion : id_inscripcion,
                                                                        id_carrera  : id_carrera,
                                                                        id_propuesta : id_propuesta,
                                                                        id_alumno : id_alumno,
                                                                        titulo_propuesta : titulo_propuesta,
                                                                        nota : $('#nota').val()
                                                                        },
                                                                type : "POST",
                                                                dataType : "json",
                                                                url : "_Negocio/n_Alumno_Mi_Titulacion_Por_Propuesta.php"
                                                            })
                                                                .done(function(respuesta,textStatus,jqXHR){
                                                                    $('#ventanaProcesando').dialog('close');
                                                                    if (respuesta.success == true){
                                                                        $('#ventanaConfirmacionBorrar').dialog('close');
                                                                        $('#nota').val('');
                                                                        Obtener_Mis_Inscripciones($('#Id_Usuario').val(), id_carrera);
                                                                        $('#ventanaConfirmarBorrado_Inscripcion').dialog('close');                                                                        
                                                                    }                                    
                                                                    $('#ventanaAviso').html(respuesta.data.message);
                                                                    $('#ventanaAvisos').dialog('open');
                                                                    $('#btn_Agregar_PT').attr("disabled", false);  

                                                                })
                                                                        .fail(function(jqXHR,textStatus,errorThrown){
                                                                            $('#ventanaProcesando').dialog('close');
                                                                            $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                                                            $('#ventanaAvisos').dialog('open');                            
                                                                        }); 
                                                        },
                                                   "Cancelar" : function(){
                                                        $(this).dialog('close');
                                                     }
                                                },
                                                title: 'Confirmar la Solicitud de Baja',
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
                                        $('#nota').val('');
                                        $(this).dialog('close');
                                    }                       
                               },
                               title: 'Baja de Solicitud de Inscripción',
                               modal : true,
                               autoOpen : true,
                               resizable : true,
                               draggable : true,
                               height : 'auto',
                               width : 450,
                               dialogClass : 'no-close',
                               closeOnEscape : false   
                            });                    
                    }
                    else if(id_estatus == 3) { //3. Aceptado
                        Obtener_Mis_Documentos_Enviados(id_inscripcion, id_documento);
                        $('#ventanaDocumentosEnviados_Propuesta').dialog('open');                        
                    }
                    
                });

                //OBTENEMOS LOS DOCUMENTOS ENVIADOS PARA AUTORIZAR EL SERVICIO SOCIAL
                function Obtener_Mis_Documentos_Enviados(id_inscripcion, id_documento){ 
                    var datos = {Tipo_Movimiento : 'OBTENER_DOCS_ENVIADOS',
                               id_inscripcion : id_inscripcion,
                               id_documento : id_documento
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_Alumno_Mi_Titulacion_Por_Propuesta.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE class="tabla_Registros"><CAPTION> Clave de la Inscripción: ' + id_inscripcion + '</CAPTION>';
                           html_table = html_table + '<TR><TH>Documento</TH>\n\
                                    <TH>Versión</TH>\n\
                                    <TH>Fecha Enviado</TH>\n\
                                    <TH>Estatus</TH>\n\
                                    <TH>Nota</TH>\n\
                                    <TH>Acción</TH></TR>';
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                    $btn_AdjuntarDoc='';
                                    if (value['id_estatus']==1){
                                       $btn_AdjuntarDoc = '<button class="btn_Adjuntar btnOpcion" data-id_inscripcion =\'' + value['id_inscripcion'] + 
                                            '\' data-id_documento=' + value['id_documento'] +  
                                            ' data-id_estatus=' + value['id_estatus'] +
                                            ' data-desc_documento=\'' + value['descripcion_documento'] + 
                                            '\' data-desc_documento_corta = \'' + value['descripcion_para_nom_archivo'] + 
                                            '\' data-id_propuesta = \'' + value['id_propuesta'] + 
                                            '\' data-id_carrera = \'' + value['id_carrera'] + 
                                            '\' data-id_version=' + value['numero_version']  +
                                            '>Adjuntar Docs</button>';                                    
                                    }
                                   
                                   if (value['fecha_recepcion']){
                                       $dato_fecha = '<TD>' + value['fecha_recepcion'] + '</TD>';
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
                                   nom_file= value['id_alumno']+'_'+
                                           value['id_carrera']+'_'+
                                           value['id_propuesta']+'_'+
                                           value['numero_version']+'_'+
                                           value['descripcion_para_nom_archivo']+'.pdf';
                                   fecha_=new Date();
                                   ruta_doc= ruta_docs_inscripcion_a_propuesta+nom_file+'?'+ fecha_;
                                   html_table = html_table + '<TR>'; 
                                   if(value['id_estatus'] == 2 || value['id_estatus']==3){
                                   html_table = html_table + '<TD style="text-align:left;"><a class="link_pdf" target="_blank" href="' + 
                                           ruta_doc +'">' + value['descripcion_documento'] + '</a></TD>';
                                    }
                                    else{
                                        html_table = html_table + '<TD style="text-align:left;">' + value['descripcion_documento'] + '</TD>';

                                    }
                                   
//                                   html_table = html_table + '<TD>' + value['descripcion_documento'] + '</TD>';
                                   html_table = html_table + '<TD style="text-align:left;">' + value['numero_version'] + '</TD>';
                                   html_table = html_table + $dato_fecha;
                                   html_table = html_table + '<TD>' + value['descripcion_estatus'] + '</TD>';
                                   html_table = html_table + $dato_nota;
                                   html_table = html_table + '<TD>' + $btn_AdjuntarDoc + '</TD>';                                   
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Mis_Doc_Prop').empty();
                               $('#tabla_Mis_Doc_Prop').html(html_table);                                
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="6">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Mis_Doc_Prop').empty();
                               $('#tabla_Mis_Doc_Prop').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros"><CAPTION> Clave de la Inscripción: ' + id_inscripcion + '</CAPTION>';
                                html_table = html_table + '<TR><TH>Documento</TH><TH>Versión</TH><TH>Fecha Enviado</TH><TH>Estatus</TH><TH>Nota</TH><TH>Acción</TH></TR>';
                                html_table = html_table + '<TR><TD colspan="6">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Mis_Doc_Prop').empty();
                                $('#tabla_Mis_Doc_Prop').html(html_table);                                
                            });                                                                             
                }//fin Obtenemos los Documentos Enviados para Autorizar la inscripción
                
                $('#ventanaDocumentosEnviados_Propuesta').dialog({
                   buttons:{
                        "Cerrar" : function() {
                            $(this).dialog('close');
                        }
                   },
                   title: 'Mis Documentos Enviados',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                   height : '550',
                   width : '850',
                   show : 'slide',
                   hide : 'slide',
		   position : {at: 'center top'},                   
                   dialogClass : 'no-close',
                   closeOnEscape : false                      
                });                            

                $('#tabla_Mis_Doc_Prop').on('click', "button.btn_Adjuntar", function(e){
                    e.preventDefault();
                    var tituloAdjuntar = 'Para la Inscripción <b>' + $(this).data('id_inscripcion') +
                            ', Versión ' + $(this).data('id_version') +
                            '</b>. Seleccione su archivo <b>' + $(this).data('desc_documento') + 
                            ',</b> en formato PDF. <b>(Tamaño máximo 500MB)</b><br><br>';
                    $('#lblTitulo_SubirArchivo_Insc').html(tituloAdjuntar);
                    $('#id_inscripcion').attr("value", $(this).data('id_inscripcion'));
                    $('#id_documento_insc').attr("value", $(this).data('id_documento'));
                    $('#id_version_insc').attr("value", $(this).data('id_version'));
                    $('#id_usuario_insc').attr("value", $('#Id_Usuario').val());
                    $('#desc_corta_doc').attr("value", $(this).data('desc_documento_corta'));
                    $('#id_propuesta_doc').attr("value", $(this).data('id_propuesta'));
                    $('#id_carrera_doc').attr("value", $(this).data('id_carrera'));
                    
                    $('#ventanaSubirArchivo_Inscripcion').dialog('open');
                });

                $('#ventanaSubirArchivo_Inscripcion').dialog({
                    buttons:{
                        "Cerrar" : function() {     
                            Obtener_Mis_Documentos_Enviados($('#id_inscripcion').val(), $('#id_documento_insc').val());
                            $('#ventanaSubirArchivo_Inscripcion input[type=file]').each(function(){
                                $(this).val('');
                            });
                            $('#ventanaSubirArchivo_Inscripcion span').each(function(){
                                $(this).hide();
                            });
                            $('#message').empty();
                            $(this).dialog('close');
                            
                        }
                   },                    
                    title: 'Adjuntar documento',
                    modal : true,
                    autoOpen : false,
                    resizable :false,
                    draggable : true,   
                    width : '650',
                    height : 'auto',
                    dialogClass : 'no-close',
                    closeOnEscape : false,
                    show : 'slide',
                    hide : 'slide',                    
		    position : {at: 'center top'}                    
                });

                //ADJUNTAR ARCHIVO 
                $('#loading').hide();
                $(':file').change(function(){                    
                    var archivo_selec = $('#file')[0].files[0];
                    var archivo_nombre = archivo_selec.name;
                    var archivo_extension = archivo_nombre.substring(archivo_nombre.lastIndexOf('.')+1);
                    var archivo_tamano = archivo_selec.size;
                    var archivo_tipo = archivo_selec.type;

                    var info_Archivo_Selec='';                    
                    info_Archivo_Selec += "<table class='tabla_Registros'><tr><th colspan='2'><span>Información del Archivo seleccionado:</span></th></tr>";
                    info_Archivo_Selec += "<tr><td><b>Nombre del archivo:</b></td><td>" + archivo_nombre + "</td></tr>";
                    info_Archivo_Selec += "<tr><td><b>Extensión:</b></td><td>" + archivo_extension + "</td></tr>";
                    info_Archivo_Selec += "<tr><td><b>Tipo:</b></td><td>" + archivo_tipo + "</td></tr>";
                    info_Archivo_Selec += "<tr><td><b>Tamaño:</b></td><td>" + (archivo_tamano / 1048576) + " kB</td></tr></table>";
                    
                    $('#message').html(info_Archivo_Selec);
                    $('#loading').empty();                
                    
                    if (parseInt(archivo_tamano) > 500000000){
                        $('#file').val('');
                        $('#ventanaAviso').html('El Tamaño del Archivo excede los 500 MB permitidos.');
                        $('#ventanaAvisos').dialog('open');
                    }
                    
                });

                $('#frmSubirPDF_Inscripcion').on('submit',function(e){
                    e.preventDefault();
                    $('#loading').html('<h1>Loading...</h1>');
                    $('#loading').show();
                    $('#ventanaProcesando').dialog('open');
                    $.ajax({
                        url     : "uploadFile_6_procesa.php",
                        type    : "POST",
                        data    : new FormData(this),
                        contentType : false,
                        cache   : false,
                        processData : false,
                        success  : function(data){
                            $('#loading').html(data);
                            $('#file').val('');
                            $('#ventanaProcesando').dialog('close');
                        }
                    });
                });

                $('#btn_Agregar_PT').click(function(e){
                    e.preventDefault();                   
                    Obtener_Propuestas($('#Id_Carrera').val());
                    $('#ventanaPropuestasPDF').dialog('open');
                });

               $('#ventanaPropuestasPDF').dialog({
                   open: function(){
                    var tiempo = new Date();
                    var fileName ="Docs/Propuestas_Profesor/Sin_Seleccionar.pdf"  + "?" + tiempo; 
                    var new_Object = $('#obj_PDF_prop').clone(false);
                    new_Object.attr("type", "application/pdf");
                    new_Object.attr("data", fileName);
                    $("#obj_PDF_prop").replaceWith(new_Object);                            
                },
                   title: 'Propuestas Autorizadas',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                   width : '1000',
                   height : '630',
                   show : 'slide',
                   hide : 'slide',  
                   closeOnEscape : false,                   
		   position : {at: 'center top'}                   
                });                            

                $('#tabla_Propuestas_Autorizadas').on("click","a.link_pdf", function(e){
                    e.preventDefault();
                    var id_profesor = $(this).data('id_profesor');
                    $('#ventana_DatosContactoProfesor').dialog({
                        open : function(){
                        var datos = {Tipo_Movimiento : 'OBTENER_USUARIO',
                            id_tipo_usuario : 4, //PROFESOR
                            id_usuario : id_profesor};
                        $.ajax({
                            data : datos,
                            type : "POST",
                            dataType : "json",
                            url : "_Negocio/n_administrador_Crear_Nueva_Cuenta.php"
                        })
                        .done(function(respuesta,textStatus,jqXHR){
                            if (respuesta.success == true){
                                //recorremos cada usuario
                                $.each(respuesta.data.registros, function( key, value ) {
                                    //recorremos los valores de cada usuario
                                    $nombre_Profesor = value['nombre_usuario'] + ' '+ value['apellido_paterno_usuario'] + ' ' + value['apellido_materno_usuario'];
                                    $telefonoYExtension = value['telefono_fijo_profesor'] + ' Ext. ' + value['telefono_extension_profesor'];
                                    $('#nombre').val($nombre_Profesor);
                                    $('#telOficina').val($telefonoYExtension);
                                    $('#email').val(value['email_usuario']);
                                });
                            }
                            else {
                                $('#ventanaAviso').html(respuesta.data.message);
                                $('#ventanaAvisos').dialog('open');                                                                    
                            }
                        })
                        .fail(function(jqXHR,textStatus,errorThrown){
                            $('#ventanaProcesando').dialog('close');
                            $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                            $('#ventanaAvisos').dialog('open');                            
                        });                           
                },
                close: function(){
                    $(this).dialog('destroy');
                },
                title: 'Información de Contacto',
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


                
                $('#tabla_Propuestas_Autorizadas').on("click", "button.btn_Ver_Indice", function(e){
                    e.preventDefault();
                    var tiempo = new Date();
                    var fileName ="Docs/Propuestas_Profesor/" + 
                            $(this).data('id_profesor') + "_" +
                            $(this).data('id_propuesta') + "_" +
                            $(this).data('id_version') + "_" +
                            $(this).data('desc_indice_doc') +  ".pdf" + "?" + tiempo; 
                    
                    var new_Object = $('#obj_PDF_prop').clone(false);
                    new_Object.attr("type", "application/pdf");
                    new_Object.attr("data", fileName);
                    $("#obj_PDF_prop").replaceWith(new_Object);                            
                });

                $('#tabla_Propuestas_Autorizadas').on("click", "button.btn_Inscribirme", function(e){
                    e.preventDefault();
                    var id_propuesta = $(this).data('id_propuesta');
                    var titulo_propuesta = $(this).data('titulo_propuesta');
                    var id_estatus = 10; //10. Por Autorizar Profesor
                    var id_alumno= $('#Id_Usuario').val();
                    var id_carrera = $('#Id_Carrera').val(); 

                    $('#ventanaConfirmacion').dialog({
                           buttons:{
                                "Aceptar" : function() {
//                                    $(this).dialog('close');
                                    $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
                                    $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                                    $('#ventanaProcesando').dialog('open');

                                    // Por Ajax agregamos la Inscripción
                                    $.ajax({
                                        data : {Tipo_Movimiento : 'AGREGAR',
                                                id_propuesta : id_propuesta,
                                                id_estatus : id_estatus,
                                                id_alumno : id_alumno,
                                                id_carrera : id_carrera,
                                                titulo_propuesta : titulo_propuesta
                                                },
                                        type : "POST",
                                        dataType : "json",
                                        url : "_Negocio/n_Alumno_Mi_Titulacion_Por_Propuesta.php"
                                    })
                                        .done(function(respuesta,textStatus,jqXHR){
                                            $('#ventanaProcesando').dialog('close');
                                            $('#ventanaAviso').html(respuesta.data.message);
                                            $('#ventanaAvisos').dialog('open');  

                                            Obtener_Mis_Inscripciones(id_alumno, id_carrera)                                      
                                            $('#ventanaConfirmacion').dialog('close');
                                            $('#ventanaPropuestasPDF').dialog('close');

                                        })
                                                .fail(function(jqXHR,textStatus,errorThrown){
                                                    $('#ventanaProcesando').dialog('close');
                                                    $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                                    $('#ventanaAvisos').dialog('open');                            
                                                });                                
                                },
                                "Cancelar" : function() {
                                    $(this).dialog('close');
                                }                       
                           },
                           title: 'Inscribirme a Propuesta',
                           modal : true,
                           autoOpen : true,
                           resizable : true,
                           draggable : true,
                           dialogClass : 'no-close ventanaConfirmaUsuario',
                           closeOnEscape : false   
                        });
    
    
                    });
                
                //OBTENEMOS LAS PROPUESTAS AUTORIZADAS POR CARRERA
                function Obtener_Propuestas(id_carrera){
                    var datos = {Tipo_Movimiento : 'OBTENER_PROPUESTAS_AUT_CARRERA',
                                 id_carrera : id_carrera};
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_profesor_Mis_Propuestas.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE class="tabla_Registros">';
                           html_table += '<TR><TH>Id</TH>\n\
                                        <TH>Tipo</TH>\n\
                                        <TH>Titulo</TH>\n\
                                        <TH>Profesor</TH>\n\
                                        <TH>Acción</TH></TR>';
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                   var $btn_Ver_Indice = '';
                                   var $btn_Inscribirme ='';
                                   var $link_Info_Generales ='';
                                   
                                   $link_Info_Generales ='<a class="aDagosGrales link_pdf" href="#" data-id_profesor=\'' + value["id_profesor"] + '\'>' + value["nom_profesor"] + '</a>';
                                   $btn_Ver_Indice = '<button class="btn_Ver_Indice btnOpcion" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' + 
                                            ' data-id_profesor = \'' + value['id_profesor'] + '\' ' +
                                            ' data-desc_indice_doc = \'' + value['descripcion_para_nom_archivo'] + '\' ' +
                                            ' data-id_estatus =' + value['id_estatus'] + 
                                            ' data-id_version =' + value['version_propuesta'] + 
                                            ' data-id_tipo_propuesta =' + value['id_tipo_propuesta'] + '>Ver  Indice</button>';
                                    
                                    if(value['aceptar_inscripciones']!=0)
                                    {
                                   $btn_Inscribirme = '<button class="btn_Inscribirme btnOpcion" style="margin-top:4px;" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' + 
                                            ' data-id_profesor = \'' + value['id_profesor'] + '\' ' +
                                            ' data-desc_indice_doc = \'' + value['descripcion_para_nom_archivo'] + '\' ' +
                                            ' data-id_estatus =' + value['id_estatus'] + 
                                            ' data-id_version =' + value['version_propuesta'] + 
                                            ' data-titulo_propuesta = \'' + value['titulo_propuesta'] + '\' ' +
                                            ' data-id_tipo_propuesta =' + value['id_tipo_propuesta'] + '>Inscribirme</button>';
                                    }
                                   html_table += '<TR>';
                                   html_table += '<TD>' + value['id_propuesta'] + '</TD>';
                                   html_table += '<TD>' + value['descripcion_tipo_propuesta'] + '</TD>';
                                   html_table += '<TD style="text-align:left;">' + value['titulo_propuesta'] + '</TD>';
                                   html_table += '<TD>' + value['nom_profesor'] + '</TD>';
                                   html_table += '<TD>' + $btn_Ver_Indice + $btn_Inscribirme + '</TD>';
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Propuestas_Autorizadas').empty();
                               $('#tabla_Propuestas_Autorizadas').html(html_table);                                
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="5">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Propuestas_Autorizadas').empty();
                               $('#tabla_Propuestas_Autorizadas').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros">';
                                html_table += '<TR><TH>Id</TH>\n\
                                             <TH>Tipo</TH>\n\
                                             <TH>Titulo</TH>\n\
                                             <TH>Profesor</TH>\n\
                                             <TH>Acción</TH></TR>';

                                html_table = html_table + '<TR><TD colspan="5">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Propuestas_Autorizadas').empty();
                                $('#tabla_Propuestas_Autorizadas').html(html_table);                                
                            });                                                                                                                                   
                }
                //FIN OBTENEMOS LAS PROPUESTAS AUTORIZADAS POR CARRERA

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

                
                Obtener_Carreras_Del_Alumno();
                //f5($(document),true);
                $('#ventanaPropuestasPDF').hide();
                $('#ventanaConfirmacion').hide();
                $('#ventanaDocumentosEnviados_Propuesta').hide();
                $('#ventanaSubirArchivo_Inscripcion').hide();
                $('#ventanaConfirmarBorrado_Inscripcion').hide();
                $('#ventana_DatosContactoProfesor').hide();
            });
                        
        </script>

        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                    <p>Mi Titulación por Propuesta</p>
                </div>
                <div class="barra_Herramientas">
                    <input type="submit" id="btn_Agregar_PT" name="btn_Agregar_PT" value="Agregar" class="btn_Herramientas"/>
                </div>
            </div>
            <div id="lst_Mis_Carreras" class="barra_Parametros">
                <label for="selec_Mis_Carreras" class="etiqueta_Parametro">Mi Carrera:</label>
                <select id="selec_Mis_Carreras" name="selec_Mis_Carreras" class="combo_Parametro"> 
                    </select>                            
            </div>
            <div id="tabla_Mis_Inscripciones" class="tabla_Registros">
            </div>

            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
            <input type="hidden" id="Id_Carrera" name="Id_Carrera" value=""> 

        </div>

        <div id='ventanaDocumentosEnviados_Propuesta'>
            <div id="contenido_Mis_Prop" style="padding-top: 0px; text-align: center;">
                <div id="tabla_Mis_Doc_Prop">
                </div>
            </div>
        </div>
        
        <div id="ventanaPropuestasPDF">
            <div id="archivoPDF_prop" style="position: absolute; z-index: 99; float: left; border: 1px grey solid; height: 560px; width: 450px; ">

                <object id="obj_PDF_prop" width="450px" height="560px"></object>
                
            </div>            
            <div id="tabla_Propuestas_Autorizadas" style="text-align: center; float: right; width: 500px; line-height: 1.1em; font-size: 0.9em;">
                                
            </div>
        </div>
        
        <div id="ventanaConfirmacion">
            Desea Inscribirse en la Propuesta seleccionada ?
        </div>

        <div id="ventanaConfirmarBorrado_Inscripcion">
            <span style="font-weight: bold; margin-top: 5px">Debe de indicar los Motivos por los que desea dar de baja esta Solicitud de Inscripción.</span>
            <textarea id="nota" class="notaVoBo entrada_Dato" style="margin-top: 5px;"
                      maxlength="500" placeholder="" onkeyup="javascript:this.value=this.value.toUpperCase();"
                title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
        </div>
        <div id="ventanaConfirmacionBorrar">
            <span id="MensajeConfirmaBorrar"></span>
        </div>
    
        <div id="ventana_DatosContactoProfesor" class="contenido_Formulario ventanaInformativa">
            <p>
                <div style="display: block;">
                    <p>
                        <label for="nombre" class="label">Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="ventanaInformativa" readonly value=""/>   
                    </p>                

                    <p>
                        <label for="telOficina" class="label">Tél.Oficina:</label>
                        <input type="text" name="telOficina" id="telOficina" class="ventanaInformativa" readonly value=""/>   
                    </p>                
                    <p>
                        <label for="email" class="label">e-mail:</label>
                        <input type="text" name="email" id="email" class="ventanaInformativa" readonly value=""/>
                    </p> 
                </div>   
            </p>

                    <!-- MODAL PARA QUIENES AÚN NO ESTÁN TITULADOS -->
            <div id="fechaEstimadaTitulacionMotivo" class="contenido_Formulario hidden" style="width: 400px;" hidden>
              <p style="height: 1.2em; margin-bottom:  .5em; text-align: center;" id="mensajeFechaEstimadaTitulacion">Para tu ceremonia necesitamos que llenes los siguientes datos</p>
              <div id="contenidoFechaTitulacion" style="margin-top: 20px;">
                <form action="" method="post" enctype="multipart/form-data" id="formFechaTitulacion">
                  <p>
                    <label for="motivoTitulacion" class="label">Motivo:</label>
                    <textarea type="text" name="motivoTitulacion" class="entrada_Dato" id="motivoTitulacion" autocomplete="off"/>
                    <span id="avisoMotivoTitulacion" class="dato_Invalido"><img src="css/images/error.ico"></span>
                  </p>
                  <p>
                    <label for="fechaEstimada" class="label">Fecha estimada para titulación</label>
                    <input type="text" name="fechaEstimada" class="entrada_Dato" id="fechaEstimada" autocomplete="off" />
                    <span id="avisoFechaEstimada" class="dato_Invalido"><img src="css/images/error.ico"></span>
                  </p>
                </form>
              </div>  
            </div>
        </div> 
        </div>    
   

        <div id="ventanaSubirArchivo_Inscripcion" style="width:400px; padding-top: 20px;">
            <div id="contenido_Subir_Archivo_Inscripcion" style="padding-top: 10px;">
                <form action="" method="post" enctype="multipart/form-data" id="frmSubirPDF_Inscripcion" name="frmSubirPDF_Carta">
                    <p>
                        <label id='lblTitulo_SubirArchivo_Insc' for= "archivoPDF"></label>
                        <div style="display: inline-block;">
                            <input type="file" name="file" id="file" accept=".pdf" required class="tag_file"> 
                            <input type="submit" name="enviarArchivo" id="enviarArchivo" value="Enviar" class="btn_Herramientas" style="width: 60px;">
                            
                            <input type="hidden" id="id_inscripcion" name="id_inscripcion" value="">
                            <input type="hidden" id="id_documento_insc" name="id_documento_insc" value="">
                            <input type="hidden" id="id_version_insc" name="id_version_insc" value="">
                            <input type="hidden" id="id_usuario_insc" name="id_usuario_insc" value="">
                            <input type="hidden" id="desc_corta_doc" name="desc_corta_doc" value="">
                            <input type="hidden" id="id_propuesta_doc" name="id_propuesta_doc" value="">
                            <input type="hidden" id="id_carrera_doc" name="id_carrera_doc" value="">                            
                        </div>   
                    </p>
                    <p style="text-align: center;">
                        
                    </p>
                </form>
                <div id='loading' class="resultado_Carga_De_Archivo"><h1>Cargando el Archivo...</h1></div>
                
                <div id="message" class="informacion_Archivo_A_Cargar"></div>
                
            </div>            
        </div>
        
        <div id="ventanaAvisos">
            <span id="ventanaAviso"></span>
        </div>
        <div id="ventanaProcesando" data-role="header">
            <img id="cargador" src="css/images/engrane2.gif"/><br>
            Procesando su transacción....!<br>
            Espere por favor.
        </div>

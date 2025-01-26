<!--<!DOCTYPE html>-->
<!--
Fecha:          Julio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para el envío de la Carta de Terminación
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
        <script src="js/ruta_documentos.js"></script>
        
        <script>
            $( document ).ready(function() {
                                
                $('#tabla_Mi_SS_Hras').on("click", "button.btn_EnviarDoc", function(e){
                    e.preventDefault();
                    $('#Id_Carrera').val($(this).data("id_carrera"));
                    $('#id_carrera_doc').val($(this).data("id_carrera"));
                    $('#Id_SS').val($(this).data("id_SS"));  
                    var id_ss = $(this).data("id_ss");
                    Obtener_Mis_Documentos_Enviados(id_ss);
                    $('#ventanaDocumentosEnviados_Carta').dialog('open');
                });
                
                $('#tabla_Mi_Carta').on('click', "button.btn_Adjuntar", function(e){
                    e.preventDefault();

                    var tituloAdjuntar = 'Para el Servicio Social <b>' + $(this).data('id_ss') +
                            ', Versión ' + $(this).data('id_version') +
                            '</b>. Seleccione su archivo <b>' + $(this).data('desc_documento') + 
                            ',</b> en formato PDF. <b>(Tamaño máximo 500MB)</b><br><br>';
                    $('#lblTitulo_SubirArchivo').html(tituloAdjuntar);
                    $('#id_ss_doc').attr("value", $(this).data('id_ss'));
                    $('#id_documento_doc').attr("value", $(this).data('id_documento'));
                    $('#id_version_doc').attr("value", $(this).data('id_version'));
                    $('#id_usuario_doc').attr("value", $(this).data('id_alumno'));
                    $('#desc_corta_doc').attr("value", $(this).data('desc_documento_corta'));
                    $('#ventanaSubirArchivo_Carta').dialog('open');
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
                
                $('#frmSubirPDF_Carta').on('submit',function(e){
                    e.preventDefault();

                    $('#ventanaProcesando').dialog('open');
                    $('#loading').html('<h1>Loading...</h1>');
                    $('#loading').show();
                    $.ajax({
                        url     : "uploadFile_4_procesa.php",
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

                $('#selec_Mis_Carreras').change(function(e){
                    e.preventDefault();
                    var id_carrera_sel = $(this).val();
                    $('#Id_Carrera').val(id_carrera_sel);
                    $('#id_carrera_doc').val(id_carrera_sel);
                    Obtener_SS_Hrs(id_carrera_sel);
                });
                                                
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
                                Obtener_SS_Hrs($('#Id_Carrera').val()); 
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

                //OBTENEMOS LOS SERVICIOS SOCIALES DEL ALUMNO CON LAS HORAS ACUMULADAS
                function Obtener_SS_Hrs(id_carrera){   
                    var datos = {Tipo_Movimiento : 'OBTENER_SERVICIO_SOCIAL_HORAS_LABORADAS',
                               Id_Usuario : $('#Id_Usuario').val(),
                               Id_Carrera : id_carrera,
                               clave : 0
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_Alumno_Mi_Carta_Terminacion.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE style="width:100%;"';
                           html_table = html_table + '<TR><TH>Id_Servicio</TH>\n\
                                                      <TH>Programa</TH>\n\
                                                      <TH>Fecha de Inicio</TH>\n\
                                                      <TH>Duracion (meses)</TH>\n\
                                                      <TH>Estatus Servicio Social</TH>\n\
                                                      <TH>Hrs. Prog.</TH>\n\
                                                      <TH>Hrs. Realizadas</TH>\n\
                                                      <TH>Hrs. Pendientes</TH>\n\
                                                      <TH>Acción</TH></TR>';
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                   var $btn_EnviarDoc = '';
                                   if(parseInt(value['horas_pendientes']) <= 0){
                                        $btn_EnviarDoc = '<button class="btn_EnviarDoc btnOpcion" data-id_alumno=\'' + $('#Id_Usuario').val() + '\' ' + 
                                            'data-id_SS=\'' + value['id_ss'] + '\' data-id_carrera=' + id_carrera +'>Enviar Doc</button>';
                                    
                                }
                                   html_table = html_table + '<TR>';
                                   html_table = html_table + '<TD>' + value['id_ss'] + '</TD>';
                                   html_table = html_table + '<TD>' + value['id_programa'] + '</TD>';
                                   html_table = html_table + '<TD style="text-align:left;">' + value['fecha_inicio_ss'] + '</TD>';
                                   html_table = html_table + '<TD style="text-align:left;">' + value['duracion_meses_ss'] + '</TD>';
                                   html_table = html_table + '<TD>' + value['descripcion_estatus'] + '</TD>';
                                   html_table = html_table + '<TD>' + value['horas_obligatorias'] + '</TD>';
                                   html_table = html_table + '<TD>' + (value['horas_laboradas'] == null ? 0 : value['horas_laboradas']) + '</TD>';
                                   html_table = html_table + '<TD>' + (value['horas_pendientes'] == null ? value['horas_obligatorias'] : value['horas_pendientes']) + '</TD>';
                                   html_table = html_table + '<TD>' + $btn_EnviarDoc + '</TD>';
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Mi_SS_Hras').empty();
                               $('#tabla_Mi_SS_Hras').html(html_table);                                
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="9">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Mi_SS_Hras').empty();
                               $('#tabla_Mi_SS_Hras').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros">';
                           var html_table = '<TABLE class="tabla_Registros">';
                           html_table = html_table + '<TR><TH>Id_Servicio</TH>\n\
                                                      <TH>Programa</TH>\n\
                                                      <TH>Fecha de Inicio</TH>\n\
                                                      <TH>Duracion (meses)</TH>\n\
                                                      <TH>Estatus Servicio Social</TH>\n\
                                                      <TH>Hrs. Prog.</TH>\n\
                                                      <TH>Hrs. Realizadas</TH>\n\
                                                      <TH>Hrs. Pendientes</TH>\n\
                                                      <TH>Acción</TH></TR>';
                                html_table = html_table + '<TR><TD colspan="9">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Mi_SS_Hras').empty();
                                $('#tabla_Mi_SS_Hras').html(html_table);                                
                            });                                                                             
                }//fin Obtenemos los Servicios Sociales actuales del Alumno                                                          


                //OBTENEMOS LOS DOCUMENTOS ENVIADOS PARA TERMINAR EL SERVICIO SOCIAL
                function Obtener_Mis_Documentos_Enviados(id_ss){                                           
                    var datos = {Tipo_Movimiento : 'OBTENER_MIS_CARTAS_TERMINACION',
                               clave : id_ss
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_Alumno_Mi_Carta_Terminacion.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE class="tabla_Registros"><CAPTION> Clave de Servicio Social: ' + id_ss + '</CAPTION>';
                           html_table = html_table + '<TR><TH>Documento</TH>\n\
                                    <TH>Versión</TH>\n\
                                    <TH>Fecha Enviado</TH>\n\
                                    <TH>Estatus</TH>\n\
                                    <TH>Nota</TH>\n\
                                    <TH>Acción</TH></TR>';
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                    $btn_EnviarDocs='';
                                    if (value['id_estatus']==1){
                                       $btn_EnviarDocs = '<button class="btn_Adjuntar btnOpcion" data-id_alumno =\'' + value['id_alumno'] + '\' data-id_documento=' + value['id_documento'] +  
                                            ' data-id_ss=\'' + value['id_ss'] + '\' data-id_estatus=' + value['id_estatus'] +
                                            ' data-desc_documento=\'' + value['descripcion_documento'] + 
                                            '\' data-desc_documento_corta = \'' + value['descripcion_para_nom_archivo'] + '\' data-id_version=' + value['id_version']  +
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
                                   nom_file= value['id_alumno']+'_'+
                                           value['id_carrera']+'_'+
                                           value['id_ss']+'_'+
                                           value['id_version']+'_'+
                                           value['descripcion_para_nom_archivo']+'.pdf';
                                   fecha_=new Date();
                                   ruta_doc= ruta_docs_carta_terminacion+nom_file+'?'+ fecha_;
                                   
                                   html_table = html_table + '<TR>';
                                   if(value['id_estatus']==2 || value['id_estatus']==3){
                                        html_table = html_table + '<TD><a class="link_pdf" target="_blank" href="'+
                                               ruta_doc +'">' + value['descripcion_documento'] + '</a></TD>';                                       }
                                   else{
                                        html_table = html_table + '<TD>' + value['descripcion_documento'] + '</TD>';                                       
                                   }
                                   
                                   html_table = html_table + '<TD style="text-align:left;">' + value['id_version'] + '</TD>';
                                   html_table = html_table + $dato_fecha;
                                   html_table = html_table + '<TD>' + value['descripcion_estatus'] + '</TD>';
                                   html_table = html_table + $dato_nota;
                                   html_table = html_table + '<TD  style="text-align:center;">' + $btn_EnviarDocs + '</TD>';                                   
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Mi_Carta').empty();
                               $('#tabla_Mi_Carta').html(html_table);                                
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="6">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Mi_Carta').empty();
                               $('#tabla_Mi_Carta').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros"><CAPTION> Clave de Servicio Social: ' + id_ss + '</CAPTION>';
                                html_table = html_table + '<TR><TH>Documento</TH><TH>Versión</TH><TH>Fecha Enviado</TH><TH>Estatus</TH><TH>Nota</TH><TH>Acción</TH></TR>';
                                html_table = html_table + '<TR><TD colspan="6">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Mi_Carta').empty();
                                $('#tabla_Mi_Carta').html(html_table);                                
                            });                                                                             
                }//fin Obtenemos los Documentos Enviados para Terminar el Servicio Social


                $('#ventanaDocumentosEnviados_Carta').dialog({
                   buttons:{
                        "Cerrar" : function() {
                            $(this).dialog('close');
                        }
                   },
                   title: 'Mi Carta de Terminación Enviada',
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

                $('#ventanaSubirArchivo_Carta').dialog({
                    buttons:{
                        "Cerrar" : function() {     
                            Obtener_Mis_Documentos_Enviados($('#id_ss_doc').val());
                            $('#ventanaSubirArchivo_Carta input[type=file]').each(function(){
                                $(this).val('');
                            });
                            $('#ventanaSubirArchivo_Carta span').each(function(){
                                $(this).text('');
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
                    show : 'slide',
                    hide : 'slide',
		    position : {at: 'center top'},                    
                    dialogClass : 'no-close',
                    closeOnEscape : false                   
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
                
                f5($(document),true);
                Obtener_Carreras_Del_Alumno();
                $('#ventanaSubirArchivo_Carta').hide();
                
            });
                        
        </script>
        
<!--    </head>
    <body>
        <header>
            Mi Pefil
        </header>-->
        <div>
            <div>
                <div class="encabezado_Formulario">
                    <div class="descripcion_Modulo">
                        <p>Mi Carta de Terminación</p>
                    </div>
                </div>
                <div class="barra_Parametros">
                    <div id="lst_Mis_Carreras">
                        <label for="selec_Mis_Carreras" class="etiqueta_Parametro">Mi Carrera:</label>
                        <select id="selec_Mis_Carreras" name="selec_Mis_Carreras">
                        </select>
                    </div>
                </div>
                <div>
                    <div id="tabla_Mi_SS_Hras" class="tabla_Registros">
                    </div>
                </div>
                
            </div>
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
            <input type="hidden" id="Id_Carrera" name="Id_Carrera" value="">            
            <input type="hidden" id="Id_SS" name="Id_SS" value="">            
        </div>

        <div id='ventanaDocumentosEnviados_Carta'>
            <div>
                <div id="tabla_Mi_Carta">
                </div>
            </div>
        </div>
        <div id="ventanaSubirArchivo_Carta" style="width:400px; padding-top: 20px;">
            <div id="contenido_Subir_Archivo" style="padding-top: 10px;">
                <form action="" method="post" enctype="multipart/form-data" id="frmSubirPDF_Carta" name="frmSubirPDF_Carta">
                    <p>
                        <label id='lblTitulo_SubirArchivo' for= "archivoPDF"></label>
                        <div style="display: inline-block;">
                            <input type="file" name="file" id="file" accept=".pdf" required class="tag_file"> 
                            <input type="submit" name="enviarArchivo" id="enviarArchivo" value="Enviar" class="btn_Herramientas" style="width: 60px;">
                            
                            <input type="hidden" id="id_ss_doc" name="id_ss_doc" value="">
                            <input type="hidden" id="id_documento_doc" name="id_documento_doc" value="">
                            <input type="hidden" id="id_version_doc" name="id_version_doc" value="">
                            <input type="hidden" id="id_usuario_doc" name="id_usuario_doc" value="">
                            <input type="hidden" id="desc_corta_doc" name="desc_corta_doc" value="">
                            <input type="hidden" id="id_carrera_doc" name="id_carrera_doc" value="">
                            
                        </div>   
                    </p>
                </form>
                <div id='loading' class="resultado_Carga_De_Archivo" ><h1>Cargando el Archivo...</h1></div>
                
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

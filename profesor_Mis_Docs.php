
<!DOCTYPE html>
<!--
Fecha:          Octubre,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para los Docs del Usuario
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

<!--<html>
    <head>-->
<!--        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/jquery-ui.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="menu/estilo_menu.css" /> 
        <script src="js/jquery-1.12.4.min.js"></script>
        <script src="js/jquery-ui.min.js"></script>-->
        <!--<script src="js/ruta_documentos.js"></script>-->  
        
        <script>
            $( document ).ready(function() {
            
            function Obtener_Mis_Documentos(id_usuario){                                           
                //Obtenemos los documentos del usuario
                $('.link_pdf').prop("text","");
                $('.link_pdf').prop("href","");
                $('.check').prop("disabled",true);
                $('.check').prop('checked','');
                
                var datos = {Tipo_Movimiento : 'MIS_DOCUMENTOS',
                           id_usuario : id_usuario
                       };
                $.ajax({
                   data : datos,
                   type : "POST",
                   dataType : "json",
                   url : "_Negocio/n_profesor_Mis_Docs.php"
                })
                   .done(function(respuesta,textStatus,jqXHR){
                       if (respuesta.success == true){
                           //recorremos cada registro
                            var id_doc = 0;
                            var nom_archivo = '';
                            var id_usuario = 0;
                            var compartido = 0;

                            var enlace = '';
                            var checkbox = '';
                            var checado = '';

                            var nom_file= '';
                            var fecha_= '';
                            var ruta_doc= '';

                           $.each(respuesta.data.registros, function( key, value ) {
                               id_doc = value['id_documento'];
                               nom_archivo = value['nombre_archivo'];
                               compartido = value['compartido'];
                               
                               enlace = "#link_" + id_doc;
                               checkbox = "#chk_" + id_doc;
                               checado = '';
                               if(compartido==1)
                               {
                                   checado = "checked";
                               }
                               $(enlace).text(nom_archivo);
                               if(nom_archivo){
                                   $(checkbox).prop("disabled",false);
                                   $(checkbox).prop('checked',checado);
                                   
                                    fecha_=new Date();
                                    ruta_doc= 'Docs/Docs_Profesores/'+nom_archivo+'?'+ fecha_;
                                   $(enlace).prop("href",ruta_doc);
                               }
                               
                           });
                       }
                   })
                           .fail(function(jqXHR,textStatus,errorThrown){
                                $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                                $('#ventanaAvisos').dialog('open');
                           });                                                         

            }   
                
            function Agregar(i)
            {
                var $tr = $("<tr></tr>"); 
                var $td1 = $("<td style='width:100px;'></td>");
                var $td2 = $("<td></td>");
                var $td3 = $("<td style='width:50px;'></td>");
                var $td4 = $("<td style='width:125px;'></td>");
                var $link = $("<a class='link_pdf' target='_blank' href='' data-id_doc='"+i+"' id='link_"+i+"'></a>");
                var $check = $("<input type='checkbox' class='check' data-id_doc='"+i+"' id='chk_"+i+"'/>");
                var $btn_Borrar = $("<button class='btn_Borrar btnOpcion'>Borrar</button>");
                var $btn_Adjuntar = $("<button class='btn_Adjuntar btnOpcion'>Adjuntar</button>");

                var etiqueta = "Documento " + (i-1);
                if(i==1){ etiqueta = 'C.V.';}
                
                $tr.append($td1.text(etiqueta))
                        .append($td2.append($link.prop("text","")))
                        .append($td3.append($check.prop("disabled",true)))
                        .append($td4.append($btn_Borrar))
                        .append($td4.append($btn_Adjuntar));

                $btn_Borrar.click(function(e){
                    e.preventDefault();
                    Borrar($link, $check);
                });
                
                $btn_Adjuntar.click(function(e){
                    e.preventDefault();
                    Adjuntar($link, $check);
                });
                
                $check.click(function(e){
                    e.preventDefault();
                    var $compartir = 0;
                    if($(this).is(':checked'))
                    {
                        $compartir =1;
                    }                    
                    Actualizar($link, $check, $compartir);

                });
                
                $("#tbl_Mis_Documentos tbody").append($tr);

            }

            function Actualizar($link, $check, $compartir)
            {
                if($link.text())
                {
                    id_doc = $link.data('id_doc');
                    id_usuario = $('#Id_Usuario').val();
                    compartir = $compartir;
                    var accion ='Compartir';
                    if(compartir==0){accion = 'Dejar de Compartir';}
                    $('#ventanaMensajeConfirma').text('Desea '+accion+' el Documento seleccionado ? ');
                    $('#ventanaConfirma').dialog({
                        buttons:{
                             "Aceptar" : function() {
                                    $(this).dialog('close');
                                    $('#ventanaProcesando').dialog('open');
                                    var datos = {Tipo_Movimiento : 'COMPARTIR_DOCUMENTO',
                                               id_usuario : id_usuario,
                                               id_doc : id_doc,
                                               compartir : compartir
                                           };
                                    $.ajax({
                                       data : datos,
                                       type : "POST",
                                       dataType : "json",
                                       url : "_Negocio/n_profesor_Mis_Docs.php"
                                    })
                                       .done(function(respuesta,textStatus,jqXHR){
                                           $('#ventanaProcesando').dialog('close');
                                           if (respuesta.success == true){
                                                Obtener_Mis_Documentos($('#Id_Usuario').val());
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
                            "Cancelar" : function(){
                                    $(this).dialog('close');
                                    }
                            },
                            title: 'Confirmar Compartir Documento',
                            modal : true,
                            autoOpen : true,
                            resizable : true,
                            draggable : true,
                            dialogClass : 'no-close ventanaConfirmaUsuario',
                            closeOnEscape : false   
                        });
                    }
            }
            
            function Borrar($link, $check)
            {
                if($link.text())
                {                    
                    id_doc = $link.data('id_doc');
                    id_usuario = $('#Id_Usuario').val();
                    nom_archivo = $link.text();
                    
                    $('#ventanaMensajeConfirma').text('Desea Borrar el Documento seleccionado ? ');
                    $('#ventanaConfirma').dialog({
                        buttons:{
                             "Aceptar" : function() {
                                    $(this).dialog('close');
                                    $('#ventanaProcesando').dialog('open');
                                    var datos = {Tipo_Movimiento : 'BORRAR_DOCUMENTO',
                                               id_usuario : id_usuario,
                                               id_doc : id_doc,
                                               nom_archivo : nom_archivo
                                           };
                                    $.ajax({
                                       data : datos,
                                       type : "POST",
                                       dataType : "json",
                                       url : "_Negocio/n_profesor_Mis_Docs.php"
                                    })
                                       .done(function(respuesta,textStatus,jqXHR){
                                           if (respuesta.success == true){
                                               $('#ventanaProcesando').dialog('close');
                                                Obtener_Mis_Documentos($('#Id_Usuario').val());
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
                            "Cancelar" : function(){
                                    $(this).dialog('close');
                                    }
                            },
                            title: 'Confirmar Borrado',
                            modal : true,
                            autoOpen : true,
                            resizable : true,
                            draggable : true,
                            dialogClass : 'no-close ventanaConfirmaUsuario',
                            closeOnEscape : false   
                        });
                                       
                }
            }
            
            function Adjuntar($link, $check){
                $("#id_doc").val($check.data("id_doc"));
                $("#nombre_archivo_original").val($link.prop('text'));
                $("#nombre_archivo_nuevo").val('');
                $("#id_usuario").val($("#Id_Usuario"));
                var tituloAdjuntar = 'Seleccione su archivo en formato PDF. <b>(Tamaño máximo 500MB)';
                $('#lblTitulo_SubirArchivo').html(tituloAdjuntar);                
                $('#ventanaSubirArchivo_Doc').dialog('open');                
            }
            
            $('#ventanaSubirArchivo_Doc').dialog({
                buttons:{
                    "Cerrar" : function() {     
                        Obtener_Mis_Documentos($('#Id_Usuario').val());
                        $('#ventanaSubirArchivo_Doc input[type=file]').each(function(){
                            $(this).val('');
                        });
                        $('#ventanaSubirArchivo_Doc span').each(function(){
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

                $('#nombre_archivo_nuevo').val(archivo_nombre);
                $('#id_usuario').val($('#Id_Usuario').val());
                $('#message').html(info_Archivo_Selec);
                $('#loading').empty();                

                if (parseInt(archivo_tamano) > 500000000){
                    $('#file').val('');
                    $('#ventanaAviso').html('El Tamaño del Archivo excede los 500 MB permitidos.');
                    $('#ventanaAvisos').dialog('open');
                }

            });
            $('#frmSubirPDF_Doc').on('submit',function(e){
                e.preventDefault();                
                $('#ventanaProcesando').dialog('open');
                $('#loading').html('<h1>Loading...</h1>');
                $('#loading').show();
                $.ajax({
                    url     : "uploadFile_10_procesa.php",
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

            //Pintamos la tabla para el envio/edición de documentos
            var i = 1;

            for(i=1;i<=6;i++)
            {
                Agregar(i);
                
            }

            Obtener_Mis_Documentos($('#Id_Usuario').val());
            $('#ventanaSubirArchivo_Doc').hide();
            });//fin document.ready
        </script>
        
<!--    </head>
    <body>
        <header>
            Mi Pefil
        </header>-->
        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                        <p>Mis Documentos</p>
                 </div>
                <div>
                </div>                
            </div>
            <form name="" id="" method="" action="">                
                <div class="contenido_Formulario" style="width: 800px;">
                    <div class="sombra_Formulario">
                        <div id="" class="">
                            <table id="tbl_Mis_Documentos" class="tabla_Registros">
                              <thead>
                                <tr>
                                  <th>Documento</th>
                                  <th>Archivo</th>
                                  <th>Compartido</th>
                                  <th>Acción</th>
                                </tr>
                              </thead>
                              <tbody class="tbl_tbody">
                              </tbody>
                            </table>                        
                        </div>

                    </div>
                <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
                <input type="hidden" id="id_Tipo_Usuario" name="id_Tipo_Usuario" value="<?php echo $_SESSION["id_tipo_usuario"];?>">
                <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="">
            </div>
           </form>
            <div id="ventanaSubirArchivo_Doc" style="width:400px; padding-top: 20px;">
                <div id="contenido_Subir_Doc" style="padding-top: 10px;">
                    <form action="" method="post" enctype="multipart/form-data" id="frmSubirPDF_Doc" name="frmSubirPDF_Doc">
                        <p>
                            <label id='lblTitulo_SubirArchivo' for= "archivoPDF"></label>
                            <div style="display: inline-block;">
                                <input type="file" name="file" id="file" accept=".pdf" required class="tag_file"> 
                                <input type="submit" name="enviarArchivo" id="enviarArchivo" value="Enviar" class="btn_Herramientas" style="width: 60px;">

                                <input type="hidden" id="id_doc" name="id_doc" value="">
                                <input type="hidden" id="nombre_archivo_original" name="nombre_archivo_original" value="">
                                <input type="hidden" id="nombre_archivo_nuevo" name="nombre_archivo_nuevo" value="">
                                <input type="hidden" id="id_usuario" name="id_usuario" value="">
                            </div>   
                        </p>
                    </form>
                    <div id='loading' class="resultado_Carga_De_Archivo" ><h1>Cargando el Archivo...</h1></div>
                    <div id="message" class="informacion_Archivo_A_Cargar"></div>

                </div>            
            </div>

        <div id="ventanaConfirma">
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
            
        </div>
        <!--Se elimina botón de home mostrado en la parte inferior-->
<!--    </body>
</html>-->

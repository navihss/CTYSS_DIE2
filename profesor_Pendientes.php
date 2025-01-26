<!DOCTYPE html>
<!--
Fecha:          Noviembre,2017
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para los Pendientes del Profesor
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

                function Obtener_Inscripciones_Pendientes(id_profesor){
                    var datos = {Tipo_Movimiento : 'OBTENER_TOTAL_INSCRIPCIONES_POR_AUTORIZAR',
                                 id_profesor : id_profesor,
                                 id_estatus : 10 //10. Por Autorizar Prof
                             };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_profesor_Aceptar_Alumnos.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE style="width:50%;">';
                           html_table += '<TR><TH>Pendientes</TH>\n\
                                        <TH>Cantidad</TH>\n\
                                        <TH>Acción</TH></TR>';
                           if (respuesta.success == true){
                               //recorremos cada registro
                               
                               $.each(respuesta.data.registros, function( key, value ) {
                                   var $link_irA = '';
                                   $link_irA ='<a class="IrA link_pdf" href="#" data-archivo=\'profesor_Aceptar_Alumnos.php\'>Ver Pendientes</a>';


                                   html_table += '<TR>';
                                   html_table += '<TD>Aceptaciones y Bajas</TD>';
                                   html_table += '<TD>' + value['totalinscripcionespendientes'] + '</TD>';
                                   html_table += '<TD>' + $link_irA + '</TD>';
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Pendientes').empty();
                               $('#tabla_Pendientes').html(html_table);                                
                               }
                           else {
                               html_table = html_table + '<TR><TD>' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Pendientes').empty();
                               $('#tabla_Pendientes').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE>';
                                var html_table = '<TABLE style="width:30%;" class="tabla_Registros">';
                                html_table += '<TR><TH>Pendientes</TH>\n\
                                        <TH>Cantidad</TH>\n\
                                        <TH>Acción</TH></TR>';

                                html_table = html_table + '<TR><TD>' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Pendientes').empty();
                                $('#tabla_Pendientes').html(html_table);                                
                            });                                                                                                                                   
                }
                //FIN OBTENEMOS TODOS LOS PENDIENTES DEL PROFESOR
      
            $('#tabla_Pendientes').on("click","a.link_pdf", function(e){
                e.preventDefault();
                $('div.ui-dialog').remove();
                $('#tmp_nuevo_Contenido').load('profesor_Aceptar_Alumnos.php');
                $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
                               
            });
            
                var id_usuario = ('<?php echo $_SESSION["id_usuario"]?>');


                $('#ventanaProcesando').dialog({
                   title: '',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : false,
                   dialogClass : 'no-close no-titlebar',
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


                Obtener_Inscripciones_Pendientes(id_usuario);
            });
                        
        </script>

        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                    <p>Mis Pendientes</p>
                </div>
                <div class="barra_Herramientas">
                </div>                                                                   
            </div>
            <div id="tabla_Pendientes" class="tabla_Registros">
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

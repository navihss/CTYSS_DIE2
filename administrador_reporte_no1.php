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

    <head>
    
<!--        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/jquery-ui.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="menu/estilo_menu.css" /> 
        <script src="js/jquery-1.12.4.min.js"></script>
        <script src="js/jquery-ui.min.js"></script>-->
        <script src="js/expresiones_reg.js"></script>
        
        <script>
            $( document ).ready(function() {

                               
                //OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
                function Obtener_Reportes_Estadisticas(id_estatus){
                    var datos = {Tipo_Movimiento : 'OBTENER_REPORTES_ESTADISTICAS',
                               	id_estatus : id_estatus
                           		};
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_administrador_Reportes_Estadisticas.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE style="width:100%;" id="Exportar_a_Excel" >';
                           html_table += '<TR>\n\
                                    	   <TH>Nombre</TH>\n\
                                    	   <TH>Categoria</TH>\n\
                                    	   <TH>Division</TH>\n\
                                    	   <TH>Mediante tesis o tesina y examen profesional concluidas</TH>\n\
                                    	   <TH>Mediante tesis o tesina y examen profesional proceso</TH>\n\
                                    	   <TH>Por trabajo profesional concluidas</TH>\n\
                                    	   <TH>Por trabajo profesional proceso</TH>\n\
                                    	   <TH>Por actividad de investigacion concluidas</TH>\n\
                                    	   <TH>Por actividad de investigacion proceso</TH>\n\
                                    	   <TH>Por servicio social concluidas</TH>\n\
                                    	   <TH>Por servicio social proceso</TH>\n\
                                    	   <TH>Por seminario de tesis o tesina concluidas</TH>\n\
                                    	   <TH>Por seminario de tesis o tesina proceso</TH>\n\
                                    	   <TH>Tesis esp proce</TH>\n\
                                    	   <TH>Tesis esp conclu</TH>\n\
                                    	   <TH>Por examen gral conocimientos concluidas</TH>\n\
                                    	   <TH>Por examen gral conocimientos proceso</TH>\n\
                                    	   <TH>Por act apoyo docencia conclu</TH>\n\
                                    	   <TH>Por act apoyo docencia proceso</TH>\n\
                                    	   <TH>Parti. Exam.Esp</TH>\n\
                                    	   <TH>Parti cuerpos cole.</TH>\n\
                                    	   <TH>Parti. Comites tutorales</TH>\n\
                                        </TR>';


     
                                                                                       
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {


                                   html_table += '<TR>';
                                   html_table += '<TD>' + value['id_jefe_departamento'] + '</TD>';
                                   html_table += '<TD>' + value['carrera'] + '</TD>';
                                   html_table += '<TD>' + value['division'] + '</TD>';
                                   html_table += '<TD>5 </TD>';
                                   html_table += '<TD>56 </TD>';
                                   html_table += '<TD>13 </TD>';
                                   html_table += '<TD>34 </TD>';
                                   html_table += '<TD>67 </TD>';
                                   html_table += '<TD>16 </TD>';
                                   html_table += '<TD>25 </TD>';
                                   html_table += '<TD>78 </TD>';
                                   html_table += '<TD>97 </TD>';
                                   html_table += '<TD>33 </TD>';
                                   html_table += '<TD>55 </TD>';
                                   html_table += '<TD>32 </TD>';
                                   html_table += '<TD>76 </TD>';
                                   html_table += '<TD>89 </TD>';
                                   html_table += '<TD>56 </TD>';
                                   html_table += '<TD>77 </TD>';
                                   html_table += '<TD>21 </TD>';
                                   html_table += '<TD>46 </TD>';
                                   html_table += '<TD>76 </TD>';

                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Reportes_Bimestrales').empty();
                               $('#tabla_Reportes_Bimestrales').html(html_table);      
//                                $("#datos_a_enviar").val(html_table);                          
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="13">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Reportes_Bimestrales').empty();
                               $('#tabla_Reportes_Bimestrales').html(html+html_table+html2);
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
                                html_table = html_table + '<TR><TD colspan="13">...' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Reportes_Bimestrales').empty();
                                $('#tabla_Reportes_Bimestrales').html(html_table);                                
                            });                                                                                                                                   
                }
                //FIN OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO


                
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
                }); */

                
        		Obtener_Reportes_Estadisticas(2);



            
 
           
            });

          	function enviar_formulario(_var) {
          		$("#datos_a_enviar").val( $('<div />').append( $("#id_tabla").eq(0).clone()).html());
          		$("#FormularioExportacion").attr('action', "administrador_reporte_exportable.php?t="+_var);
        		$("#FormularioExportacion").submit();
            }
                        
        </script>
        
</head>
    <body>

        
        
        <div class="barra_Herramientas_exportables">
            <div >
                <div >
                	<form action="" method="post" target="_blank" id="FormularioExportacion">
                        <a href="javascript:enviar_formulario('word')" ><img src="css/images/office_word.png"/></a> |
                        <a href="javascript:enviar_formulario('excel')"><img src="css/images/office_excel.png"/></a> |
                        <a href="javascript:enviar_formulario('pdf')"><img src="css/images/Oficina_PDF.png"/></a>
                        <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                    </form>
                </div>
            </div>

        </div>
        <div id="id_tabla">
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                        <p>Reportes y Estadisticas</p>
                </div>
            </div>
            <div>
                <div id="tabla_Reportes_Bimestrales" class="tabla_Registros">
                </div>
            </div>
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
           
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
</body>


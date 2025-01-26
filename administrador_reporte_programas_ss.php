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
<style type="text/css">
.ui-datepicker-calendar,.ui-datepicker-day { display: none; }
</style>
    	
        <script src="js/expresiones_reg.js"></script>
        
        
        <script>
            $( document ).ready(function() {
            	var datos2;
            	var queryObject = "";
                               
                //OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
                function Obtener_Reportes_Estadisticas(id_servicio_ss){
                    
                    var datos = {Tipo_Movimiento : 'OBTENER_PROGRAMAS_SERVICO_SOCIAL',
                    			id_servicio_ss : id_servicio_ss
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
                                        	   <TH>Clave DGOSE</TH>\n\
                                        	   <TH>Nombre del programa</TH>\n\
                                        	   <TH># cuenta</TH>\n\
                                        	   <TH>Nombre alumno</TH>\n\
                                        	   <TH>Carrera</TH>\n\
                                        	   <TH>Fecha inicio</TH>\n\
                                        	   <TH>Fecha fin</TH>\n\
                                        	   <TH>Estatus servicio social</TH>\n\
                                            </TR>';
                                            
                                
                           if (respuesta.success == true){
                               //recorremos cada registro
                				

                               
                               $.each(respuesta.data.registros, function( key, value ) {

                                       html_table += '<TR>';
                                       html_table += '<TD>' + value['id_programa'] + '</TD>';
                                       html_table += '<TD>' + value['descripcion_pss'] + '</TD>';
                                       html_table += '<TD>' + value['id_alumno'] + '</TD>';
                                       html_table += '<TD>' + value['nombre_usuario'] + '</TD>';
                                       html_table += '<TD>' + value['descripcion_carrera'] + '</TD>';
                                       html_table += '<TD>' + value['fecha_inicio_ss'] + '</TD>';
                                       html_table += '<TD>' + value['fecha_termino_ss'] + '</TD>';
                                       html_table += '<TD>' + value['descripcion_estatus'] + '</TD>';
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
                                html_table += '<TR>\n\
                                         	   <TH>Clave DGOSE</TH>\n\
                                         	   <TH>Nombre del programa</TH>\n\
                                         	   <TH># cuenta</TH>\n\
                                         	   <TH>Nombre alumno</TH>\n\
                                         	   <TH>Carrera</TH>\n\
                                         	   <TH>Fecha inicio</TH>\n\
                                         	   <TH>Fecha fin</TH>\n\
                                         	   <TH>Estatus servicio social</TH>\n\
                                 				</TR>'
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
                
        		Obtener_Reportes_Estadisticas("00");

        	      //CLICK AL BOTON ACEPTAR REPORTE
                $('#btn_filtros').on('click',function(e){
                	var selectedAnio = $("#select_anio" ).val();	
                	var selectedMes =$("#select_mes" ).val();
                	var id_anio=selectedAnio + selectedMes
                	
                	Obtener_Reportes_Estadisticas(id_anio)
                	$("#dialog").dialog('close');
                });
                //FIN CLICK AL BOTON ACEPTAR REPORTE

            });

          	function enviar_formulario(_var) {
          		$("#datos_a_enviar").val( $('<div />').append( $("#id_tabla").eq(0).clone()).html());
          		$("#FormularioExportacion").attr('action', "administrador_reporte_exportable.php?t="+_var);
        		$("#FormularioExportacion").submit();
            }

   
            
          //$('#dialog').hide();

         	 $("#dialog").hide();
         	
         	 
          	function openFormulario() { 
          		$("#dialog").dialog(); 
          	}





      
        </script>
        
</head>
    <body>

        
        
        <div class="barra_Herramientas_exportables">
            <div >
                <div >
                	<form action="" method="post" target="_blank" id="FormularioExportacion">
                		<a href="javascript:openFormulario();"  name="modal"><img src="css/images/embudo.png"/> </a>|
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
                <div>
                    <div id="tabla_Reportes_Bimestrales" class="tabla_Registros">
                    </div>
                </div>
                <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
           </div>
        </div>
        

 <div id="dialog" title="Filtros" class="contenido_Formulario ventanaInformativa">

        <p>
            <label for="ServicioSocial" class="label">Anio:</label>
            <select name="select_anio" id="select_anio">
            <option value="0"> Todos</option> 
            <option value="2013">2013</option> 
            <option value="2014">2014</option> 
            <option value="2015">2015</option> 
            <option value="2016">2016</option> 
            <option value="2017">2017</option> 
            <option value="2018">2018</option> 
 
            </select>
        </p> 
         <p>
            <label for="ServicioSocial" class="label">Mes:</label>
            <select name="select_mes" id="select_mes">
            <option value="0">Todos</option>
              <option value="01">Enero</option> 
              <option value="02">Febrero</option> 
              <option value="03">Marzo</option> 
              <option value="04">Abril</option> 
              <option value="05">Mayo</option> 
              <option value="06">Junio</option> 
              <option value="07">Julio</option> 
              <option value="08">Agosto</option> 
              <option value="09">Septiembre</option> 
              <option value="10">Octubre</option> 
              <option value="11">Noviembre</option> 
              <option value="12">Diciembre</option> 
            </select>
        </p>   

		 <button id="btn_filtros" class="btn_Herramientas" style="width: 160px;">Aceptar</button>
      
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


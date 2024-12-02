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
        	var titulo_grafica;
        	var titulo_reporte;
        	var titulo_anio;
        	var array_anio = new  Array();
        
            $( document ).ready(function() {
            	var datos2;
            	var queryObject = "";


            	 //OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
                function Obtener_Reportes_Anios(){
                    
                    var datos = {Tipo_Movimiento : 'OBTENER_REPORTES_ANIOS'};
               		
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_administrador_Reportes_Estadisticas.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                                                          
                           if (respuesta.success == true){                          

	                            var i =0;
                               $.each(respuesta.data.registros, function( key, value ) {
                                   //alert(value['id_anio'])

                            	   array_anio[i] = value['id_anio'];
                            	   i++;
                               });

                               //alert('array_anio.length'+ array_anio.length);
                               
                           } else {

                               
                           }
                       })
                         .fail(function(jqXHR,textStatus,errorThrown){
                               
                          });                                                                                                                                   
                }
                //FIN OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
                
                
                               
                //OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
                function Obtener_Reportes_Estadisticas(id_anio,vision){
                    
                    var datos = {Tipo_Movimiento : 'OBTENER_REPORTES_TITULADO',
                               	id_anio : id_anio,
                               	vision :vision
                           		};
               		
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_administrador_Reportes_Estadisticas.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE style="width:100%;" id="Exportar_a_Excel" >';
                           var datos_grafica;

                           if(vision =="0"){
                               html_table += '<TR>\n\
                            	   <TH>Anio</TH>\n\
                            	   <TH>Inicio</TH>\n\
                            	   <TH>Termino</TH>\n\
                                </TR>';
                                
                    			datos_grafica="[['Anio', 'Inicio', 'Termino'],"; 

                           }else{
                               html_table += '<TR>\n\
                                        	   <TH>Anio</TH>\n\
                                        	   <TH>Carrera</TH>\n\
                                        	   <TH>Inicio</TH>\n\
                                        	   <TH>Termino</TH>\n\
                                            </TR>';
                                            
                                datos_grafica="[['Carrera', 'Inicio', 'Termino'],"; 
                           }

                          	
                                                                                       
                           if (respuesta.success == true){
                               //recorremos cada registro
                               var acum_anio;
                             
                				                             
                               $.each(respuesta.data.registros, function( key, value ) {

                            	   if(vision =="0"){

                            		  
                            		   
                                       html_table += '<TR>';
                                       html_table += '<TD>' + value['id_anio'] + '</TD>';
                                       html_table += '<TD>' + value['inicio'] + '</TD>';
                                       html_table += '<TD>' + value['terminos'] + '</TD>';
                                       html_table = html_table + '</TR>';

                                       datos_grafica += "['"+value['id_anio']+"',"+ value['inicio'] +","+ value['terminos']  +"],"

     
                            	   }else{
                                	   
                            		   
                                       html_table += '<TR>';
                                       html_table += '<TD>' + value['id_mes'] + '</TD>';
                                       html_table += '<TD>' + value['carrera'] + '</TD>';
                                       html_table += '<TD>' + value['inicio'] + '</TD>';
                                       html_table += '<TD>' + value['terminos'] + '</TD>';
                                       html_table = html_table + '</TR>';
    
                                       datos_grafica += "['"+value['carrera']+"',"+ value['inicio'] +","+ value['terminos'] +"],"

                            	   }
   
                               });
                               
                               
                               
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Reportes_Bimestrales'+ id_anio).empty();
                               $('#tabla_Reportes_Bimestrales'+ id_anio).html(html_table); 


                               
                               queryObject=datos_grafica.substring(0, datos_grafica.length -1)+"]";
                               queryObject = queryObject.trim();
                               queryObject = queryObject.replace(/'/g, '"');
                               //queryObject = jQuery.parseJSON(JSON.stringify(queryObject));
                               queryObject = jQuery.parseJSON(queryObject);
                               console.log( "cadena: " + queryObject); 

                               
                               //google.charts.load("current", {packages: ["bar"]});
                               google.charts.load("current", {packages: ["corechart"]});
                               google.charts.setOnLoadCallback(function(){ setTimeout(drawChart('grafica'+ id_anio ,id_anio, vision), 100)  });
                 

                                
                         
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="13">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Reportes_Bimestrales').empty();
                               $('#tabla_Reportes_Bimestrales').html(html_table);
                               $('#grafica').empty();

                        
                               
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros">';
                                html_table += '<TR><TH>Anio</TH>\n\
                                             <TH>Carrera</TH>\n\
                                             <TH>Inicio</TH>\n\
                                             <TH>Termino</TH></TR>';
                                html_table = html_table + '<TR><TD colspan="13">...' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Reportes_Bimestrales').empty();
                                $('#tabla_Reportes_Bimestrales').html(html_table);                                
                            });                                                                                                                                   
                }
                //FIN OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO

                 
                
                      function drawChart(div_data, id_anio, vision) {
						
						var titulo_opciones;
  
                        if(vision == 0)  {
                        	titulo_grafica='Estadistica Anual'; 
                        	titulo_reporte=titulo_grafica;
                        	titulo_opciones='Anio'
                        	$('#id_titulo_reporte').html(titulo_reporte); 	
                        }
                        else if(vision == 1) {
                        	titulo_anio = id_anio;
                        	titulo_grafica='Estadistica por Carrera' + ' ' + titulo_anio;
                        	titulo_reporte=titulo_grafica;
                        	titulo_opciones='Carrera'
                        	$('#id_titulo_reporte').html(titulo_reporte); 	
                        }
                        else if(vision == 2) {
                        	titulo_anio = id_anio;
                        	titulo_grafica='Estadistica Anual por Carrera' + ' ' + titulo_anio;
                        	titulo_reporte='Estadistica Anual por Carrera';
                        	titulo_opciones='Carrera'
                        	$('#id_titulo_reporte').html(titulo_reporte); 	
                        }
                        

                        var data = google.visualization.arrayToDataTable(queryObject);

                        //var formatter = new google.visualization.NumberFormat({ pattern:'#.#' });
                        //formatter.format(data, 1);
                        //formatter.format(data, 2);  

                        var view = new google.visualization.DataView(data);
                       
                       
                
                        var options = {
                            title: titulo_grafica,
                            colors: ['#dcb635', '#394b71'],
                            bar: { groupWidth: '30%' },
                            legend: { position: 'top', maxLines: 3 },
                            height: 200,
                            
                            chartArea: {
                            	   top: 55,
                            	   height: '50%' 
                            	},
                            hAxis: {
                              title: titulo_opciones,
                              minValue: 0,
                              //slantedText: true, 
                              //slantedTextAngle: 90 //Angulo de la etiqueta 180 
                            },
                            vAxis: { 
                                title: '# Alumnos',
                                minValue: 0,
                                direction: 1,
                                slantedText: true, 
                             }
                        }

                        var chart_div = document.getElementById(div_data);

                        var chart = new google.visualization.ColumnChart(chart_div);

                        // Wait for the chart to finish drawing before calling the getImageURI() method.
                        google.visualization.events.addListener(chart, 'ready', function () {
                          chart_div.innerHTML = '<img src="' + chart.getImageURI() + '" >';
                          //$('#chart_div').html('<img src="' + chart.getImageURI() + '" >'); 	
                          console.log(chart_div.innerHTML);
                        });

                        chart.draw(data, options);
     
                        /*var chart = new google.visualization.ColumnChart(document.getElementById('grafica'));
                        chart.draw(data, options);*/
             
                      }



                        
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


            
				//VALORES INICIALES DEL REPORTE
               	html_table_cont='<table width="100%" id="id_tabla" border="1">';
               	
               	for(x=0 ; x <= 0; x++){
        			html_table_cont += '<tr valign="top"  ><td valign="top" width="30%"><div id="tabla_Reportes_Bimestrales'+ x +'" class="tabla_Registros"></div></td> <td valign="bottom" width="70%"><div id="grafica'+ x +'" ></div></td></tr>'; 
               	}     		
                html_table_cont +='</table>';
				
                $('#id_contenido').empty();
                $('#id_contenido').html(html_table_cont);
                
        		Obtener_Reportes_Estadisticas("0","0");
        		Obtener_Reportes_Anios();

        		

        		
				$('#select_anio').prop( "disabled", true );
				$('#select_mes').prop( "disabled", true );



				// HABILITAMOS LOS COMBOS
				$( "#select_vision" ).change(function() {
					
					var vision			= $("#select_vision" ).val();
					if(vision == 0 || vision == 2){
						
						$('#select_anio').prop( "disabled", true );
						$('#select_mes').prop( "disabled", true );
    					
					}else{
						
    					$('#select_anio').prop( "disabled", false );
    					$('#select_mes').prop( "disabled", false );
					}

				});

        	      //CLICK AL BOTON ACEPTAR REPORTE
                $('#btn_filtros').on('click',function(e){
                    var vision			= $("#select_vision" ).val();
                	var selectedAnio 	= $("#select_anio" ).val();	
                	var selectedMes 	= $("#select_mes" ).val();
                	var id_anio			= selectedAnio + selectedMes
					// ESTADISTICA ANUAL
					if(vision == 0 ){

		               	html_table_cont='<table width="100%" id="id_tabla" border="1">';
		               	
		               	for(x=0 ; x <= 0; x++){
		        			html_table_cont += '<tr><td valign="middle" width="30%"><div id="tabla_Reportes_Bimestrales'+ x +'" class="tabla_Registros"></div></td><td width="70%"><div id="grafica'+ x +'" ></div></td></tr>'; 
		               	}     		
		                html_table_cont +='</table>';
						
		                $('#id_contenido').empty();
		                $('#id_contenido').html(html_table_cont);
		                
						Obtener_Reportes_Estadisticas('0',vision)
						//console.log("html_table_cont:"+ html_table_cont);
						
					}else if(vision == 1 ){
						// ESTADISTICA POR CARRERA,
						if(selectedAnio == 0){
							alert('seleccione un anio')
							return;
						}
						if(selectedMes == 0){
							alert('seleccione un mes') 
							return;
						}

		               	html_table_cont='<table width="100%" id="id_tabla" border="1">';
		               	
		               	for(x=id_anio ; x <= id_anio; x++){
		        			html_table_cont += '<tr><td valign="middle" width="30%"><div id="tabla_Reportes_Bimestrales'+ x +'" class="tabla_Registros"></div></td><td width="70%"><div id="grafica'+ x +'" ></div></td></tr>'; 
		               	}     		
		                html_table_cont +='</table>';
						
		                $('#id_contenido').empty();
		                $('#id_contenido').html(html_table_cont);
		                
						Obtener_Reportes_Estadisticas(id_anio,vision)
						
					}else if(vision == 2){
						// ESTADISTICA DE TODOS LOS ANIOS
						 //alert('array_anio.length'+ array_anio.length);
						 
		               	html_table_cont='<table width="100%" id="id_tabla" border="1">';
		               	for(x=0 ; x < array_anio.length; x++){
		        			html_table_cont += '<tr><td valign="middle" width="30%"><div id="tabla_Reportes_Bimestrales'+ array_anio[x] +'" class="tabla_Registros"></div></td><td width="70%"><div id="grafica'+ array_anio[x] +'" ></div></td></tr>'; 
		               	}     		
		                html_table_cont +='</table>';
						
		                $('#id_contenido').empty();
		                $('#id_contenido').html(html_table_cont);

		                for(x=0 ; x <= array_anio.length; x++){
		                	Obtener_Reportes_Estadisticas(array_anio[x],vision);
		                }

					}


                	
                	
                	$("#dialog").dialog('close');
                });
                //FIN CLICK AL BOTON ACEPTAR REPORTE

            });

          	function enviar_formulario(_var_tipo_doc, _var_nombre_doc,_var_num_repo,_var_tit_repo ) {
              
          		$("#datos_a_enviar").val( $('<div />').append( $("#id_tabla").eq(0).clone()).html());
          		$("#FormularioExportacion").attr('action', "administrador_reporte_exportable.php?t="+_var_tipo_doc+"&nombre_doc="+_var_nombre_doc+"&num_repo="+_var_num_repo+"&titulo_reporte="+_var_tit_repo);
          		var tab_div = document.getElementById('id_tabla');
          		
        		$("#FormularioExportacion").submit();
            }

   
            
          //$('#dialog').hide();

         	 $("#dialog").hide();
         	
         	 
          	function openFormulario() { 
          		$("#dialog").dialog(); 
          	}

    		$(function () {

    			$( "#fecha" ).datepicker({
    				dateFormat: 'yymm',
    				changeMonth: true,
    			    changeYear: true,
    			    showButtonPanel: true,
    		        onClose: function(dateText, inst) { 
    		      	   var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val(); 
		               var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val(); 
		               $(this).datepicker('setDate', new Date(year, month, 1)); 
		               Obtener_Reportes_Estadisticas($( "#fecha" ).val());
    		        }});
				
    			
    			});




      
        </script>
        
</head>
    <body>

        
        
        <div class="barra_Herramientas_exportables">
            <div >
                <div >
                	<form action="" method="post" target="_blank" id="FormularioExportacion">
                		<a href="javascript:openFormulario();"  name="modal"><img src="css/images/embudo.png"/> </a>|
                        <a href="javascript:enviar_formulario('word','estadisticas',2,this.titulo_grafica)" ><img src="css/images/office_word.png"/></a> |
                        <a href="javascript:enviar_formulario('excel','estadisticas',2,this.titulo_grafica)"><img src="css/images/office_excel.png"/></a> |
                        <a href="javascript:enviar_formulario('pdf','estadisticas',2,this.titulo_grafica)"><img src="css/images/Oficina_PDF.png"/></a>
                        <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                    </form>
                </div>
            </div>
        </div>
        
        
        <div >
            <div class="encabezado_Formulario" id="id_titulo">
                <div class="descripcion_Modulo">
                        <p id="id_titulo_reporte"></p>
                </div>
            </div>
            <div style="border:3px solid #b8db44;" id="id_contenido">
           </div>
        </div>
        

 	<div id="dialog" title="Filtros" class="contenido_Formulario ventanaInformativa">
 		<p>
 			<label for="vision" class="label">Visi&oacute;n:</label>
     			<select name="select_vision" id="select_vision">
                <option value="0">Estad&iacute;stica Anual</option> 
                <option value="1">Estad&iacute;stica Por Carrera</option> 
                <option value="2">Estad&iacute;stica Anual Por Carrera</option>
            	</select>
 		</p>

        <p>
            <label for="anio" class="label">Anio:</label>
            <select name="select_anio" id="select_anio">
            <option value="0"> Selecione un anio</option> 
            <option value="2007">2007</option>
            <option value="2008">2008</option>
            <option value="2009">2009</option>
            <option value="2010">2010</option>
            <option value="2011">2011</option>
            <option value="2012">2012</option>
            <option value="2013">2013</option> 
            <option value="2014">2014</option> 
            <option value="2015">2015</option> 
            <option value="2016">2016</option> 
            <option value="2017">2017</option> 
            <option value="2018">2018</option> 
	    <option value="2019">2019</option> 
            <option value="2020">2020</option> 
            <option value="2021">2021</option> 
            <option value="2022">2022</option>
 
            </select>
        </p> 
         <p>
            <label for="mes" class="label">Mes:</label>
            <select name="select_mes" id="select_mes">
            <option value="0">Selecione un mes</option>
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


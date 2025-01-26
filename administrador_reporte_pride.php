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

        <script src="js/expresiones_reg.js"></script>
        
        <script>
            $( document ).ready(function() {

                               
                //OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
                function Obtener_Reportes_Estadisticas(tx_profesor,fecha_inicio){
                    var datos = {Tipo_Movimiento : 'OBTENER_REPORTES_ESTADISTICAS',
                        		tx_profesor 	: tx_profesor,
                        		fecha_inicio 	: fecha_inicio	
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
                                        </TR>';


     
                                                                                       
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {


                                   html_table += '<TR>';
                                   html_table += '<TD>' + value['nombre_profesor'] + '</TD>';
                                   html_table += '<TD>' + value['descripcion_categoria'] + '</TD>';
                                   html_table += '<TD>' + value['descripcion_division'] + '</TD>';
                                   html_table += '<TD>' + value['mediante_tesis_tesina_examen_prof_conc'] + '</TD>';
                                   html_table += '<TD>' + value['mediante_tesis_tesina_examen_prof_proc'] + ' </TD>';
                                   html_table += '<TD>' + value['trabajo_prof_conc'] + ' </TD>';
                                   html_table += '<TD>' + value['trabajo_prof_proc'] + ' </TD>';
                                   html_table += '<TD>' + value['actividad_inv_conc'] + ' </TD>';
                                   html_table += '<TD>' + value['actividad_inv_proc'] + ' </TD>';
                                   html_table += '<TD>' + value['servicio_social_conc'] + ' </TD>';
                                   html_table += '<TD>' + value['servicio_social_proc'] + ' </TD>';
                                   html_table += '<TD>' + value['seminario_conc'] + ' </TD>';
                                   html_table += '<TD>' + value['seminario_proc'] + ' </TD>';
                                   html_table += '<TD>' + value['tesis_esp_conc'] + ' </TD>';
                                   html_table += '<TD>' + value['tesis_esp_proc'] + ' </TD>';
                                   html_table += '<TD>' + value['examen_gral_conc'] + ' </TD>';
                                   html_table += '<TD>' + value['examen_gral_proc'] + ' </TD>';
                                   html_table += '<TD>' + value['apoyo_docedncia_conc'] + ' </TD>';
                                   html_table += '<TD>' + value['apoyo_docedncia_proc'] + ' </TD>';


                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Reportes_Bimestrales').empty();
                               $('#tabla_Reportes_Bimestrales').html(html_table);                        
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="19">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Reportes_Bimestrales').empty();
                               $('#tabla_Reportes_Bimestrales').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros">';
                                html_table += '<TR>\n\
                                             	   <TH>Nombre</TH>\n\
                                             	   <TH>Categoria</TH>\n\
                                             	   <TH>Division</TH>\n\
                                             	   <TH>Mediante tesis o tesina y examen profesional concluidas</TH>\n\
                                             	   <TH>Mediante tesis o tesina y examen profesional proceso</TH>\n\
                                             	   <TH>Por trabajo profesional concluidas</TH>\n\
                                             	   <TH>Por trabajo profesional proceso</TH>\n\
                                                 </TR>';
                                html_table = html_table + '<TR><TD colspan="7">...' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Reportes_Bimestrales').empty();
                                $('#tabla_Reportes_Bimestrales').html(html_table);                                
                            });                                                                                                                                   
                }
                //FIN OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
                
                
                //Array para dar formato en español al datepicker
                 $.datepicker.regional['es'] =  { 
                        closeText: 'Cerrar',  
                        prevText: 'Previo',  
                        nextText: 'Próximo', 
                        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',                           
                                    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'], 
                        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun', 
                        'Jul','Ago','Sep','Oct','Nov','Dic'], 
                        monthStatus: 'Ver otro mes', yearStatus: 'Ver otro año', 
                        dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'], 
                        dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sáb'], 
                        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'], 
                        dateFormat: 'dd/mm/yy', firstDay: 0,  
                        initStatus: 'Selecciona la fecha', isRTL: false
                    }; 
                $.datepicker.setDefaults($.datepicker.regional['es']); 



                
                $('#fechaInicio').datepicker({
                    changeYear : true,
                    changeMonth : true,
                    yearRange : '1920:2050',
                    onSelect : function(date){
                        $("#fechaInicio ~ .ui-datepicker").hide();
                    }
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

                
        		Obtener_Reportes_Estadisticas("","");


        	    //CLICK AL BOTON ACEPTAR REPORTE
                $('#btn_filtros').on('click',function(e){

                    
                	var tx_profesor 		= $("#profesor").val();          	
                	var fecha_inicio 		= $("#fechaInicio" ).val();	

                	Obtener_Reportes_Estadisticas(tx_profesor,fecha_inicio);

                    getTitulosFiltros();    							
                        							  							
                	$("#tabla_Mi_Servicio").dialog('close');
                });
                //FIN CLICK AL BOTON ACEPTAR REPORTE
            
 
           
            });


            var titulo_filtros='';
			function getTitulosFiltros(){
				titulo_filtros='';

            	var tx_profesor 		= $("#profesor").val();	
            	var fecha_inicio 		= $("#fechaInicio" ).val();	

            	
            	
                if(tx_profesor != ''){
                	titulo_filtros += '->'
					titulo_filtros += 'Profesor: '+ tx_profesor
					
                }
					
                if(fecha_inicio != ''){
                	titulo_filtros += '->'
					titulo_filtros += 'Fecha: '+ fecha_inicio
					
                }


				 $("#id_titulo_filtros").html(titulo_filtros.substring(2,titulo_filtros.length ));	
					

			}

          	function enviar_formulario(_var_tipo_doc, _var_nombre_doc,_var_num_repo,_var_tit_repo ) {
          		$("#datos_a_enviar").val( $('<div />').append( $("#id_tabla").eq(0).clone()).html());
          		$("#FormularioExportacion").attr('action', "administrador_reporte_exportable.php?t="+_var_tipo_doc+"&nombre_doc="+_var_nombre_doc+"&num_repo="+_var_num_repo+"&titulo_reporte="+_var_tit_repo);
        		$("#FormularioExportacion").submit();
            }

        	 $("#tabla_Mi_Servicio").hide();
          	
         	 
           	function openFormulario() { 
           		$("#tabla_Mi_Servicio").dialog(
           				{
           			        maxWidth:600,
           			        maxHeight: 200,
           			        width: 550,
           			        height: 190,
           			        modal: true
           		        }
                   		); 
           	}
                        
        </script>
        
</head>
    <body>

        
        
        <div class="barra_Herramientas_exportables">
            <div >
                <div >
                	<form action="" method="post" target="_blank" id="FormularioExportacion">
                		<a href="javascript:openFormulario();"  name="modal"><img src="css/images/embudo.png"/>
                        <a href="javascript:enviar_formulario('word','ReporteSS',1,'Pride')" ><img src="css/images/office_word.png"/></a> |
                        <a href="javascript:enviar_formulario('excel','ReporteSS',1,'Pride')"><img src="css/images/office_excel.png"/></a> |
                        <a href="javascript:enviar_formulario('pdf','ReporteSS',1,'Pride')"><img src="css/images/Oficina_PDF.png"/></a>
                        <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                    </form>
                </div>
            </div>

        </div>
        
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                        <p>Pride</p>
                </div>
            </div>
            
        <div id="id_tabla">  
            <div>
            	<p id="id_titulo_filtros" class ="titulo_filtros"></p>
            </div>  
            <div>
                <div id="tabla_Reportes_Bimestrales" class="tabla_Registros">
                </div>
            </div>
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
        </div>
        
         <div id="tabla_Mi_Servicio" title="Filtros" class="contenido_Formulario ventanaInformativa" style="width:800px;">
     		<table style="width:500px">
         		<tr>
                     <td>
                     <p>
                           <label for="profesor" class="label">Profesor:</label>
                            <input type="text" name="profesor" id="profesor" class="ventanaInformativa" style="width:200px;" placeholder="Nombre completo" autocomplete="off" class="entrada_Dato"/>
                     
                     </td>
                     
                     <td>
                        <p>
                            <label for="fechaInicio" class="label alumno_ent">Fecha:</label>
                            <input type="text" name="fechaInicio" id="fechaInicio" autocomplete="off"  title="dd/mm/aaaa" class="alumno_ent"  style="width:200px;"/>                    
                        </p>
                     </td>
                     
                 </tr>
                 <tr>
                     <td>
                     </td>
                     
                     <td>
                     <p>
                      <button id="btn_filtros" class="btn_Herramientas" style="width: 160px;">Aceptar</button>
                     </td>
                 </tr>
     		</table>
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


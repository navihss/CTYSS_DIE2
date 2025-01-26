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
                function Obtener_Reportes_Estadisticas(tx_alumno,
                                            			id_carrera,
                                            			fecha_inicio,
                                            			tx_recinto,
                                            			tx_modalidad){
                    var datos = {Tipo_Movimiento : 'OBTENER_REPORTES_TITULACION_CEREMONIA',
                            		tx_alumno 	: tx_alumno		,
                            		id_carrera 	: id_carrera	,
                            		fecha_inicio: fecha_inicio	,
                            		tx_recinto 	: tx_recinto	,
                            		tx_modalidad: tx_modalidad
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
                                    	   <TH>Fecha Titulaci&oacuten</TH>\n\
                                    	   <TH>Alumnos</TH>\n\
                                    	   <TH>Carrera</TH>\n\
                                    	   <TH>Recinto</TH>\n\
                                    	   <TH>Modalidad de Titulaci&oacuten</TH>\n\
                                    	   <TH>Curso</TH>\n\
                                        </TR>';
                                                                                                      
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {


                                   html_table += '<TR>';
                                   html_table += '<TD>' + value['fecha_titulacion'] + '</TD>';
                                   html_table += '<TD>' + value['nombre_alumno'] + '</TD>';
                                   html_table += '<TD>' + value['descripcion_carrera'] + '</TD>';
                                   html_table += '<TD>' + value['recinto'] + '</TD>';
                                   html_table += '<TD>' + value['descripcion_tipo_titulacion'] + '</TD>';
                                   html_table += '<TD>' + value['nombre_diplomado'] + ' </TD>';
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Reportes_Bimestrales').empty();
                               $('#tabla_Reportes_Bimestrales').html(html_table);      
                         
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="6">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Reportes_Bimestrales').empty();
                               $('#tabla_Reportes_Bimestrales').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros">';
                                html_table += '<TR>\n\
                                             	   <TH>Fecha Titulacion</TH>\n\
                                             	   <TH>Carrera</TH>\n\
                                             	   <TH>Opcion Titulaci�n</TH>\n\
                                             	   <TH>Examen o Ceremonia</TH>\n\
                                             	   <TH>Director</TH>\n\
                                             	   <TH>Sinodales</TH>\n\
                                                 </TR>';
                                html_table = html_table + '<TR><TD colspan="6">...' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Reportes_Bimestrales').empty();
                                $('#tabla_Reportes_Bimestrales').html(html_table);                                
                            });                                                                                                                                   
                }
                //FIN OBTENEMOS LOS REPORTES BIMESTRALES DEL SERVICIO SOCIAL DEL ALUMNO
                
                //LLENAMOS LOS CATALOGOS
                function Obtener_Catalogo_Carrera(id_carrera){

                    var datos = {Tipo_Movimiento : 'OBTENER_CATALOGO_CARRERA', 
                    			id_carrera  : id_carrera
                            };
                 
                    $.ajax({
                        data : datos,
                        type : 'POST',
                        dataType : 'json',
                        url : '_Negocio/n_administrador_Reportes_Estadisticas.php'
                    })
                        .done(function(respuesta,textStatus,jqXHR){
                            var html_options='';
                            if (respuesta.success == true){
                                //recorremos cada registro
                                $.each(respuesta.data.registros, function( key, value ) {
                                    //recorremos los valores de cada usuario
                                    html_options = html_options + '<option value=' + value['id'] +
                                            '>' + value['descripcion'] + '</option>';
                                });
                                $('#carrera').empty();
                                $('#carrera').html(html_options);
                                
                                $('#carrera' + ' option:first-child').attr('selected','selected');

                             
                            }
                            else {
                                $('#ventanaAviso').html(respuesta.data.message);
                                //$('#ventanaAvisos').dialog('open');                                                                    
                            }
                        })
                                .fail(function(jqXHR,textStatus,errorThrown){
                                    $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                                   //$('#ventanaAvisos').dialog('open');                            
                                });                            
                }     
                //FIN LLENADO DE CATALOGO
                
                //LLENAMOS LOS CATALOGOS
                function Obtener_Catalogo_Recinto(id_carrera){

                    var datos = {Tipo_Movimiento : 'OBTENER_CATALOGO_RECINTO', 
                    			id_carrera  : id_carrera
                            };
                 
                    $.ajax({
                        data : datos,
                        type : 'POST',
                        dataType : 'json',
                        url : '_Negocio/n_administrador_Reportes_Estadisticas.php'
                    })
                        .done(function(respuesta,textStatus,jqXHR){
                            var html_options='';
                            if (respuesta.success == true){
                                //recorremos cada registro
                                $.each(respuesta.data.registros, function( key, value ) {
                                    //recorremos los valores de cada usuario
                                    html_options = html_options + '<option value=' + value['id'] +
                                            '>' + value['descripcion'] + '</option>';
                                });
                                $('#recinto').empty();
                                $('#recinto').html(html_options);
                                
                                $('#recinto' + ' option:first-child').attr('selected','selected');

                             
                            }
                            else {
                                $('#ventanaAviso').html(respuesta.data.message);                                                     
                            }
                        })
                                .fail(function(jqXHR,textStatus,errorThrown){
                                    $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                                });                            
                }     
                //FIN LLENADO DE CATALOGO
                
                //LLENAMOS LOS CATALOGOS
                function Obtener_Catalogo_Modalidad(id_carrera){

                    var datos = {Tipo_Movimiento : 'OBTENER_CATALOGO_MODALIDAD', 
                    			id_carrera  : id_carrera
                            };
                 
                    $.ajax({
                        data : datos,
                        type : 'POST',
                        dataType : 'json',
                        url : '_Negocio/n_administrador_Reportes_Estadisticas.php'
                    })
                        .done(function(respuesta,textStatus,jqXHR){
                            var html_options='';
                            if (respuesta.success == true){
                                //recorremos cada registro
                                $.each(respuesta.data.registros, function( key, value ) {
                                    //recorremos los valores de cada usuario
                                    html_options = html_options + '<option value=' + value['id'] +
                                            '>' + value['descripcion'] + '</option>';
                                });
                                $('#modalidad').empty();
                                $('#modalidad').html(html_options);
                                
                                $('#modalidad' + ' option:first-child').attr('selected','selected');

                             
                            }
                            else {
                                $('#ventanaAviso').html(respuesta.data.message);                                                         
                            }
                        })
                                .fail(function(jqXHR,textStatus,errorThrown){
                                    $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                                });                            
                }     
                //FIN LLENADO DE CATALOGO
                
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

				//VALORES INICIALES
                Obtener_Catalogo_Carrera('0');
                Obtener_Catalogo_Recinto('0');
                Obtener_Catalogo_Modalidad('0');
                
        		Obtener_Reportes_Estadisticas("",
                                        		"",
                                        		"",
                                        		"",
                                        		"");

        	    //CLICK AL BOTON ACEPTAR REPORTE
                $('#btn_filtros').on('click',function(e){

                    
                	var tx_alumno 			= $("#alumno").val();
                	var id_carrera 			= $("#carrera").val();         	
                	var fecha_inicio 		= $("#fechaInicio" ).val();
                	var tx_recinto 			= $("#recinto").val(); 
                	var tx_modalidad 		= $("#modalidad").val(); 
              	

                	Obtener_Reportes_Estadisticas(tx_alumno,
                                        			id_carrera,
                                        			fecha_inicio,
                                        			tx_recinto,
                                        			tx_modalidad);

                    getTitulosFiltros();    							
                        							  							
                	$("#tabla_Mi_Servicio").dialog('close');
                });
                //FIN CLICK AL BOTON ACEPTAR REPORTE
            
 

           
            });


            var titulo_filtros='';
			function getTitulosFiltros(){
				titulo_filtros='';

				var tx_alumno 			= $("#alumno").val();
				var id_carrera 			= $('#carrera option:selected').html();        	
            	var fecha_inicio 		= $("#fechaInicio" ).val();
            	var tx_recinto 			= $('#recinto option:selected').html();  
            	var tx_modalidad 		= $('#modalidad option:selected').html();


                if(tx_alumno != ''){
                	titulo_filtros += '->'
					titulo_filtros += 'Alumno: '+ tx_alumno
					
                }
                
	            if(id_carrera != 'Todos'){
	            	titulo_filtros += '->'
					titulo_filtros += 'Carrera: '+ id_carrera
					
	            }
					
                if(fecha_inicio != ''){
                	titulo_filtros += '->'
					titulo_filtros += 'Fecha: '+ fecha_inicio
					
                }

                if(tx_recinto != ''){
                	titulo_filtros += '->'
					titulo_filtros += 'Recinto: '+ tx_recinto
					
                }

                if(tx_modalidad != ''){
                	titulo_filtros += '->'
					titulo_filtros += 'Modalidad: '+ tx_modalidad
					
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
           			        maxHeight: 300,
           			        width: 550,
           			        height: 290,
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
                	<a href="javascript:openFormulario();"  name="modal"><img src="css/images/embudo.png"/> </a>|
                        <a href="javascript:enviar_formulario('word','ReporteSS',2,'alumnos')" ><img src="css/images/office_word.png"/></a> |
                        <a href="javascript:enviar_formulario('excel','ReporteSS',2,'alumnos')"><img src="css/images/office_excel.png"/></a> |
                        <a href="javascript:enviar_formulario('pdf','ReporteSS',2,'alumnos')"><img src="css/images/Oficina_PDF.png"/></a>
                        <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                    </form>
                </div>
            </div>

        </div>
        
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                        <p>Titulaci&oacuten Ceremonia</p>
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
                   <label for="alumno" class="label">Alumno:</label>
                    <input type="text" name="alumno" id="alumno" class="ventanaInformativa" style="width:200px;" placeholder="Nombre completo" autocomplete="off" class="entrada_Dato"/>
             
             </td>
             
             <td>
             <p>

                    <label for="carrera" class="label">Carrera:</label>
                    <select name="carrera" id="carrera" class="combo_Parametro" style="width:200px;">
                    </select>
             </td>
         </tr>
         
         
          <tr>
             <td>
                <p>
                    <label for="fechaInicio" class="label alumno_ent">Fecha:</label>
                    <input type="text" name="fechaInicio" id="fechaInicio" autocomplete="off"  title="dd/mm/aaaa" class="alumno_ent"  style="width:200px;"/>                    
                </p>
             </td>
             
              <td>

             </td>
         </tr>
         
         
         <tr>
             <td>
             <p>
				<label for="recinto" class="label">Recinto:</label>
				<select name="recinto" id="recinto" class="combo_Parametro" style="width:200px;">
                </select>
              <p>
             </td>
             <td>
                 <p>
                   <label for="modalidad" class="label">Modalidad:</label>
                    <select name="modalidad" id="modalidad" class="combo_Parametro" style="width:200px;">
                    </select>
                  <p>
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


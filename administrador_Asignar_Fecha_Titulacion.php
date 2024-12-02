
<!DOCTYPE html>
<!--
Fecha:          Julio,2024
Desarrollador:  Carlos Alfonso Aguilar Castro
Objetivo:       Interfaz para asignar fecha de titulacion a un usuario
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

        <script src="js/expresiones_reg.js"></script>
        
        <script>
            $( document ).ready(function() {
              
                //VALIDAMOS LOS DATOS DE LA FORM
                function validaDatos(){
                    $('#btn_Guardar').prop('disable',true);
                    var datosValidos = true;
                    var clave = $('#clave').val();
                    var fechaTitulacion = $('#fechaTitulacion').val();
                    
                    $('#statusClave').hide();

                    if (clave =='' || fechaTitulacion =='')
                    {
                        $('#ventanaAviso').html('Debe capturar TODA la información Requerida.');
                        $('#ventanaAvisos').dialog('open');
                        
                        datosValidos = false;
                    }
                    
                    $('#btn_Guardar').prop('disable',false);
                    return datosValidos;
                };
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

                $('#fechaTitulacion').datepicker({
                    changeYear : true,
                    changeMonth : true,
                    yearRange : '1920:2100',
                    onSelect : function(date){
                        $("#fechaTitulacion ~ .ui-datepicker").hide();
                    }
                });

                $('#clave').keypress(function(){
                    $('.dato_Invalido').hide();                    
                });
                
//              Validamos que la clave exista en la BD
                $('#clave').blur(function(){
                    if ($('#clave').val()==""){
                        return;
                    }                        
                    $("#cargandoAjax").css("display", "inline");
                    var datosAPasar = {
                        Tipo_Movimiento : 'EXISTE_USUARIO',
                        claveUsuario : $('#clave').val()
                    };
		    console.log(datosAPasar);
                    var html_options='';

                    $.ajax({
                        data : datosAPasar,
                        type : "POST",
                        dataType : "json",
                        url : "_Negocio/n_administrador_Asignar_Fecha_Titulacion.php"
                    })
                            .done(function(respuesta,textStatus,jqXHR){
                                $("#cargandoAjax").css("display", "none");
                                if (respuesta.success == 'EXISTE'){ // Ya Existe La Cuenta
                                      console.log('nombre existe')
				      $('#statusClave').hide();
                                      $('#nombre').val(respuesta.data.registros[0]['nombre']);
                                      $.each(respuesta.data.registros, function( key, value ) {
                                        html_options = html_options + '<option value=' + value['id_carrera'] +
                                                '>' + value['descripcion_carrera'] + '</option>';
                                      });
                                      $('#carrera').empty();
                                      $('#carrera').html(html_options);
                                    
                                      $('#carrera' + ' option:first-child').attr('selected','selected');
				    //   Obtener_Carreras();
                                    //   $('#contrasena').focus();
                                }
                                else if (respuesta.success == 'NOEXISTE'){
                                    $('#statusClave').show();
                                    $('#statusClave').text('Este Usuario NO existe en el Sistema!');
                                    $('#nombre').val('');
                                    $('#clave').focus();
                                }
                                else
                                {
                                    $('#statusClave').show();
                                    $('#statusClave').text('');
                                    $('#ventanaAviso').html(respuesta.data.message);                                                                        
                                    $('#clave').val('');
                                    $('#ventanaAvisos').dialog('open');
                                }                                                    
                            })
                                    .fail(function(jqXHR,textStatus,errorThrown){
					$("#cargandoAjax").css("display", "none");
                                        $('#clave').prop('value','');

                                        $('#ventanaAviso').html('La solicitud ha fallado. ' + textStatus + '. ' + errorThrown);
                                        $('#ventanaAvisos').dialog('open');                            
                                    });                                                                
                    
                }); // ***** fin clave_blur
                
               
                $('#btn_Guardar').on('click',function(event){
                    event.preventDefault();
                    if (validaDatos())
                    {  
                       $('#Tipo_Movimiento').val('AGREGAR_FECHA_TITULACION');
                       $('#ventanaConfirmacion').dialog('open');
                    }
                    else
                    {
                       return false; 
                    }                          
                });

                $('#btn_Limpiar').on('click',function(event){
                    event.preventDefault();
                    oculta_StatusControl();
                    limpia_Controles();
                });

                function oculta_StatusControl(){
                    $('#statusClave').hide();
                }
 
                function limpia_Controles(){
                    $('#clave').val('');
                    $('#nombre').val('');
		    $('#fechaTitulacion').val('');
                    $('#carrera').html('');
		    $('#clave').focus();
                }
                
                $('#ventanaConfirmacion').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $(this).dialog('close');
                            $('#ventanaProcesando').dialog('open');
                            // Por Ajax insertamos la fecha de titulacion al usuario             
                            var formDatos = {
                                Tipo_Movimiento : $('#Tipo_Movimiento').val(),
                                claveUsuario : $('#clave').val(),
                                carrera : $('#carrera').val(),
				fechaTitulacion: $('#fechaTitulacion').val()
                            };
			    console.log(formDatos);

                            $.ajax({
                                data : formDatos,
                                type : "POST",
                                dataType : "json",
                                url : "_Negocio/n_administrador_Asignar_Fecha_Titulacion.php"
                            })
                                .done(function(respuesta,textStatus,jqXHR){
                                    if(respuesta.success == true){
                                        $('#ventanaProcesando').dialog('close');
                                        $('#ventanaAviso').html(respuesta.data.message);
                                        $('#ventanaAvisos').dialog('open');
                                        limpia_Controles();
                                    }
                                    else{
                                        $('#ventanaProcesando').dialog('close');
                                        $('#ventanaAviso').html(respuesta.data.message);
                                        $('#ventanaAvisos').dialog('open');
                                    }
                                })
                                        .fail(function(jqXHR,textStatus,errorThrown){
                                            $('#ventanaProcesando').dialog('close');
                                            $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                            $('#ventanaAvisos').dialog('open');                            
                                        });                                                                                                              
                        },
                        "Cancelar" : function() {
                            $(this).dialog('close');
                            $(':text:first').focus();
                        }                       
                   },
                   title: 'Asignar Fecha de Titulación',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                   dialogClass : 'no-close ventanaConfirmaUsuario',
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
                   title: 'Procesando...',
                   modal : true,
                   autoOpen : false,
                   resizable : false,
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
                               
                $(':text:first').focus();

            });
        </script>
        
        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                    <p>Asignar Fecha de Titulación</p>
                </div>
            </div>
            <form name="Administrador_Fecha_Titulacion" id="Administrador_Fecha_Titulacion" method="" action="">            
                <div class="contenido_Formulario">
                    <div class="sombra_Formulario">                            
                        <p>
                            <label for="clave" class="label">Usuario:</label>
                            <input type="text" name="clave" id="clave" maxlength="18" placeholder="" class="entrada_Dato"
                            title="Capture únicamente letras y numeros, sin espacios" autocomplete="off"/>
                            <div id="cargandoAjax" class="notificacion">
                                <span><img src="css/images/ajax-loader03.gif"/>Espere. Verificando si este Usuario Existe actualmente!</span>
                            </div>                    
                            <div class="notificacion">
                                <span id="statusClave"class="dato_Invalido"></span>
                            </div>                                    
                        </p>
                        <p>
                            <label for="nombre" class="label">Nombre:</label>
                            <input type="text" name="nombre" id="nombre" readonly />
                        </p>
                        <p>
                            <label for="carrera" class="label">Carrera:</label>
                            <select name="carrera" id="carrera" class="combo_Parametro">
                            </select>
                        </p>
                        <p>
                            <label for="fechaTitulacion" class="label">Fecha de Titulación:</label>
                            <input type="text" name="fechaTitulacion" id="fechaTitulacion" autocomplete="off"  title="dd/mm/aaaa" class="alumno_ent"/>
                            <!-- <input type="password" name="contrasena" id="contrasena" maxlength="15" placeholder="" class="entrada_Dato"
                           title="Capture únicamente letras y números, sin espacios" autocomplete="off"/>                                        -->
                            <!-- <span id="statusContrasena"  class="dato_Invalido"><img src="css/images/error.ico"/></span> -->
                        </p>

                        <div style="padding-top: 20px;">
                            <input type="submit" name="btn_Guardar" id="btn_Guardar" value="Guardar" class="btn_Herramientas">                            
                            <input type="button" name="btn_Limpiar" id="btn_Limpiar" value="Limpiar" class="btn_Herramientas">                            
                        </div>                                               
                    </div> 
                </div>
		<input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="">
           </form>
        </div>

        <div id='ventanaConfirmacion'>
                Desea asignar la fecha de titulación ?
        </div>
        
        <div id="ventanaAvisos">
            <span id="ventanaAviso"></span>
        </div>
        <div id="ventanaProcesando" data-role="header">
            <img id="cargador" src="css/images/engrane2.gif"/><br>
            Procesando su transacción....!<br>
            Espere por favor.
        </div>

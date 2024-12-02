<!DOCTYPE html>
<!--
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para VoBo del Jurado del Alumno
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

                //OBTENEMOS EL JURADO DEL ALUMNO
                function Obtener_Jurados_Pendientes(id_usuario){   
                    var datos = {Tipo_Movimiento : 'OBTENER_JURADOS_PENDIENTES',
                                id_usuario : id_usuario
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_coord_jdpto_Aprobar_Jurado.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE style="width:100%;">';
                           html_table = html_table + '<TR><TH>Propuesta</TH>\n\
                                                      <TH>Profesor</TH>\n\
                                                      <TH>Título Propuesta</TH>\n\
                                                      <TH>Jurado-Fecha Alta</TH>\n\
                                                      <TH>Acción</TH></TR>';                           
                           if (respuesta.success == true){
                               var $btn_Revisar="";
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                   $btn_Revisar = '<button class="btn_Revisar btnOpcion" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' + 
                                            ' data-id_profesor= \'' + value['id_profesor'] + '\'' +
                                            ' data-id_version=' + value['version'] + ' ' +
                                            ' data-titulo_propuesta = \'' + value['titulo_propuesta'] + '\'' +
                                            ' data-id_estatus= ' + value['id_estatus'] + '>Revisar Jurado</button>';
                                   html_table = html_table + '<TR>';
                                   html_table = html_table + '<TD>' + value['id_propuesta'] + '</TD>';
                                   html_table = html_table + '<TD style="text-align:left;">' + value['nombre'] + '</TD>';
                                   html_table = html_table + '<TD style="text-align:left;">' + value['titulo_propuesta'] + '</TD>';
                                   html_table = html_table + '<TD style="text-align:left;">' + esNulo(value['fecha_propuesto']) + '</TD>';
                                   html_table = html_table + '<TD>' + $btn_Revisar + '</TD>';
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Jurados_Pendientes').empty();
                               $('#tabla_Jurados_Pendientes').html(html_table);                                
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="5" style="text-align:center;">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Jurados_Pendientes').empty();
                               $('#tabla_Jurados_Pendientes').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros">';
                                html_table = html_table + '<TR><TH>Propuesta</TH>\n\
                                                           <TH>Profesor</TH>\n\
                                                           <TH>Título Propuesta</TH>\n\
                                                           <TH>Jurado-Fecha Alta</TH>\n\
                                                           <TH>Acción</TH></TR>';                           
                                html_table = html_table + '<TR><TD colspan="5">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
        
                                $('#tabla_Jurados_Pendientes').empty();
                                $('#tabla_Jurados_Pendientes').html(html_table);                                
                            });                                                                             
                }//fin Obtenemos el jurado del Alumno    
                
                $('#tabla_Jurados_Pendientes').on("click", "button.btn_Revisar", function(e){
                    e.preventDefault();
                    $('#Id_Propuesta').val($(this).data("id_propuesta"));                    
                    $('#id_Estatus').val($(this).data("id_estatus"));
                    $('#id_Version').val($(this).data("id_version"));
                    $('#titulo_propuesta').val($(this).data("titulo_propuesta"));
                    $('#Tipo_Movimiento').val('ACTUALIZAR_VoBo');
                    Obtener_Sinodales($('#Id_Usuario').val(),$(this).data("id_propuesta"), $(this).data("id_version"));
                    $('#ventanaJurado').dialog('open');
                });
                                                
                //OBTENEMOS LOS SINODALES
                function Obtener_Sinodales(id_usuario, id_propuesta, id_version){                                           
                    $('#ventanaProcesando').dialog('open');        
                    var datos = {Tipo_Movimiento : 'OBTENER_JURADOS_SELECCIONADO',
                               id_propuesta : id_propuesta,
                               id_version : id_version,
                               id_usuario : id_usuario
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_coord_jdpto_Aprobar_Jurado.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                            var html_table = '<TABLE class="tabla_Registros">';
                            html_table = html_table + '<TR><TH>Sinodal Propuesto</TH>\n\
                                                      <TH>Aceptado</TH>\n\
                                                      <TH>Nota</TH></TR>';                   
                            if (respuesta.success == true){
                               var nombre_checkbox='';
                               var nombre_textarea='';
                               var data_checkbox = '';
                               var data_textarea = '';
                               
                               $.each(respuesta.data.registros, function( key, value ) {
                                   var solo_lectura = '';
                                   if (value['num_profesor'] == 6){
                                       solo_lectura = ' checked disabled ';
                                   }
                                   nombre_checkbox = " id=chk_Sinodal_" + value['num_profesor'] + " " + solo_lectura + ' ';
                                   data_checkbox = " data-num_profesor = " + value['num_profesor'] + " ";
                                   nombre_textarea = " id=txt_Sinodal_" + value['num_profesor'] + " ";
                                   data_textarea = " data-num_profesor = " + value['num_profesor'] + " data-chk = 0 ";
                                                                      
                                   html_table += '<TR><TD style="vertical-align:top;width:150px;">' + value['nombre_sinodal_propuesto']  + '</TD>';
                                   html_table += '<TD style="text-align:center; vertical-align:top;width:30px;"><input type="checkbox" ' + nombre_checkbox + data_checkbox + '></TD>';
                                   html_table += "<TD><textarea  " + 
                                        "maxlength='500' placeholder='' style='height:4em; width:550px; text-transform:uppercase;' onkeyup='javascript:this.value=this.value.toUpperCase();' " +
                                        "title='SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #' autocomplete='off' " +
                                        nombre_textarea + data_textarea + ">" + "</textarea></TD></TR>";
                               });
                               html_table += '</TABLE>';                               
                               $('#tabla_VoBo').html(html_table);
                               $('#ventanaJurado').dialog('open');
                               $('#ventanaProcesando').dialog('close');    
                           }
                            else {
                                   html_table = html_table + '<TR><TD style="text-align:center;" colspan="3">' + respuesta.data.message + '</TD></TR>';
                                   html_table = html_table + '</TABLE>'
                                   $('#tabla_VoBo').empty();
                                   $('#tabla_VoBo').html(html_table);
                                   $('#ventanaJurado').dialog('open');  
                                   $('#ventanaProcesando').dialog('close');
                            }                                                                   
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                    $('#ventanaProcesando').dialog('close');
                                        var html_table = '<TABLE class="tabla_Registros">';
                                        html_table = html_table + '<TR><TH>Sinodal Propuesto</TH>\n\
                                                                  <TH>VoBo</TH>\n\
                                                                  <TH>Nota</TH></TR>';
                                        html_table = html_table + '<TR><TD colspan="3">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                        html_table = html_table + '</TABLE>';
                                        $('#tabla_VoBo').empty();
                                        $('#tabla_VoBo').html(html_table);  
                                        $('#ventanaJurado').dialog('open'); 
                            });                                                                             
                }//fin Obtenemos los Sinodales
               
                //VALIDACIONES 
                function validaDatos(){
                    //RECORREMOS LOS CHECKBOX, SI 'NO' ESTA MARCADO DEBERA DE TENER UNA NOTA
                    var datosValidos = true;                    
                    var lista_VoBo = '';
                    var refnom_textarea = '';
                    
                    $("input:checkbox").each(function(index){
                        if(!($(this).prop('checked'))){                            
                            refnom_textarea = '#txt_Sinodal_' + $(this).data('num_profesor');
                            if($(refnom_textarea).val() == ''){
                                datosValidos = false;                                
                            }
                        }
                    });
                    $("input:checkbox").each(function(index){
                            refnom_textarea = '#txt_Sinodal_' + $(this).data('num_profesor');
                            if(!$(refnom_textarea).val().match(miExpReg_Nota_Aceptacion)){
                                datosValidos = false;                                
                            }
                    });
                                       
                    return datosValidos;
                };
                //FIN VALIDACIONES PARA GUARDAR
                
                $('#ventanaJurado').dialog({
                   buttons:[{
                            id:"btn_Guardar", text:"Guardar", click: function() {
                            if (validaDatos()){
                                $('#ventanaConfirmacion').dialog('open');
                            }
                            else{
                                $('#ventanaAviso').html("Si NO ACEPTÓ a un Sinodal deberá capturar los Motivos. Y SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #");
                                $('#ventanaAvisos').dialog('open');                                                                                                    
                            }
                        }},{
                           id:"btn_Cancelar", text:"Cerrar", click: function() {                            
                            $('#ventanaJurado input[type=text]').each(function(){
                                $(this).val('');
                            });
                            $('#ventanaJurado span').each(function(){
                                $(this).hide();
                            });

                            $(this).dialog('close');
                        }}],                                           
                   title: 'Jurado',
                   modal : true,
                   autoOpen : false,
//                   resizable : true,
                   draggable : true,
                   height : 'auto',
                   width : '850',
                   dialogClass : 'no-close',
                   show : 'slide',
                   hide : 'slide',  
                   closeOnEscape : false,                   
		   position : {at: 'center top'},                   
                   close : function(){
                       $('#ventanaJurado input[type=text]').each(function(){
                           $(this).val('');
                       });
                       $('#ventanaJurado span').each(function(){
                           $(this).hide();
                       });
                       $(this).dialog('close');
                   }                                       
                });                            

                $('#ventanaConfirmacion').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $(this).dialog('close');
//                            $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
//                            $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                            $('#ventanaProcesando').dialog('open');
                            
                            // Por Ajax Actualizamos el VoBo de los Sinodales
                            var ok_ = 0;
                            var num_prof = 0;
                            var nota = '';
                            var cadena_VoBo ='';

                            $("input:checkbox").each(function(index){
                                ok_ = 0;
                                num_prof = $(this).data('num_profesor');
                                nota = $('#txt_Sinodal_' + $(this).data('num_profesor')).val();
                                if($(this).prop('checked')){
                                    ok_ =1;
                                }
                                cadena_VoBo += num_prof + ',' + ok_ + ',' + nota + '|';
                            });

                            cadena_VoBo = cadena_VoBo.substr(0,cadena_VoBo.length -1);

                            $('#lista_VoBo').val(cadena_VoBo);

                            var formDatos = $('#frm_VoBo').serialize();
                            
                            $.ajax({
                                data : formDatos,
                                type : "POST",
                                dataType : "json",
                                url : "_Negocio/n_coord_jdpto_Aprobar_Jurado.php"
                            })
                                .done(function(respuesta,textStatus,jqXHR){
                                    $('#ventanaProcesando').dialog('close');
                                    if (respuesta.success == true){
                                        $("#btn_Guardar").button("option", "disabled", true);
                                        Obtener_Jurados_Pendientes($('#Id_Usuario').val() );
                                    }
                                    else {
                                        $("#btn_Guardar").button("option", "disabled", false);
                                    }                                    
                                    $('#ventanaAviso').html(respuesta.data.message);
                                    $('#ventanaAvisos').dialog('open');                                                                    
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
                   title: 'Jurado',
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

                $('#tabla_VoBo').on("focus", "input:text, textarea", function(e){
                    e.preventDefault();
                    f5($(document),false);                    
                });
                $('#tabla_VoBo').on("blur", "input:text, textarea", function(e){
                    e.preventDefault();
                    f5($(document),true);                    
                });

                f5($(document),true);
                Obtener_Jurados_Pendientes($('#Id_Usuario').val() );
                
            });
                        
        </script>
        
<!--    </head>
    <body>
        <header>
            Mi Pefil
        </header>-->
        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                    <p>Aprobar Jurado</p>
                </div>
            </div>
            <div id="tabla_Jurados_Pendientes" class="tabla_Registros">
            </div>
        </div>
        <div id='ventanaJurado' name="ventanaJurado">    
            <form id="frm_VoBo" name="frm_VoBo" method="" action="" autocomplete="off"> 
                <div id='tabla_VoBo'>                    
                </div>

                <input type="hidden" id="Id_Propuesta" name="Id_Propuesta" value=""> 
                <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value=""> 
                <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">            
                <input type="hidden" id="id_Estatus" name="id_Estatus" value="0">
                <input type="hidden" id="id_Version" name="id_Version" value="0">
                <input type="hidden" id="lista_VoBo" name="lista_VoBo" value="0">
                <input type="hidden" id="titulo_propuesta" name="titulo_propuesta" value="0">
            </form>
        </div>

        <div id='ventanaConfirmacion'>
            Desea Actualizar sus observaciones ?
        </div>
        <div id="ventanaAvisos">
            <span id="ventanaAviso"></span>
        </div>

        <div id="ventanaProcesando" data-role="header">
            <img id="cargador" src="css/images/engrane2.gif"/><br>
            Procesando su transacción....!<br>
            Espere por favor.
        </div>
        <!--Se quita el botón de home-->
<!--    </body>
</html>-->


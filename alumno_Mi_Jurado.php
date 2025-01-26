<!DOCTYPE html>
<!--
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para la captura del Jurado
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
        
        <script>
            $( document ).ready(function() {
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
                                Obtener_Mi_Propuesta($('#Id_Usuario').val(), $('#Id_Carrera').val());
                            }
                            else {
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

                $('#selec_Mis_Carreras').change(function(e){
                    e.preventDefault();
                    var id_carrera_sel = $(this).val();
                    $('#Id_Carrera').val(id_carrera_sel);
                    Obtener_Mi_Propuesta($('#Id_Usuario').val() ,id_carrera_sel);
                });

                //OBTENEMOS EL JURADO DEL ALUMNO
                function Obtener_Mi_Propuesta(id_alumno, id_carrera){                                           
                    //Obtenemos los Servicios del Alumno
                    var datos = {Tipo_Movimiento : 'OBTENER_MI_JURADO',
                               id_usuario : id_alumno,
                               id_carrera   : id_carrera
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_Alumno_Mi_Jurado.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE style="width:100%;">';
                           html_table = html_table + '<TR><TH>Propuesta</TH>\n\
                                                      <TH>Jurado-Fecha Alta</TH>\n\
                                                      <TH>Jurado-Registrado Por</TH>\n\
                                                      <TH>Jurado-Estatus</TH>\n\
                                                      <TH>Acción</TH></TR>';                           
                           if (respuesta.success == true){
                               var $btn_Editar="";
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                   $btn_Editar = '<button class="btn_Editar btnOpcion" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' + 
                                            ' data-id_version=' + value['version'] + 
                                            ' data-titulo_propuesta=\'' + value['titulo_propuesta'] + '\' '+
                                            ' data-id_estatus= ' + value['id_estatus'] + '>Editar Jurado</button>';
                                   html_table = html_table + '<TR>';
                                   html_table = html_table + '<TD>' + value['id_propuesta'] + '</TD>';
                                   html_table = html_table + '<TD style="text-align:left;">' + esNulo(value['fecha_propuesto']) + '</TD>';
                                   html_table = html_table + '<TD style="text-align:left;">' + esNulo(value['nombre']) + '</TD>';
                                   html_table = html_table + '<TD>' + value['descripcion_estatus'] + '</TD>';
                                   html_table = html_table + '<TD>' + $btn_Editar + '</TD>';
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Mi_Jurado').empty();
                               $('#tabla_Mi_Jurado').html(html_table);                                
                               }
                           else {
                               html_table = html_table + '<TR><TD style="text-align:center;" colspan="5">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Mi_Jurado').empty();
                               $('#tabla_Mi_Jurado').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros">';
                                html_table = html_table + '<TR><TH>Propuesta</TH>\n\
                                                           <TH>Jurado-Fecha Alta</TH>\n\
                                                           <TH>Jurado-Registrado Por</TH>\n\
                                                           <TH>Jurado-Estatus</TH>\n\
                                                           <TH>Acción</TH></TR>';     
                                html_table = html_table + '<TR><TD style="text-align:center;" colspan="5">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
        
                                $('#tabla_Mi_Jurado').empty();
                                $('#tabla_Mi_Jurado').html(html_table);                                
                            });                                                                             
                }//fin Obtenemos Jurado del Alumno  
                
                $('#tabla_Mi_Jurado').on("click", "button.btn_Editar", function(e){
                    e.preventDefault();
                    $('#Id_Propuesta').val($(this).data("id_propuesta"));                    
                    $('#id_Estatus').val($(this).data("id_estatus"));
                    $('#id_Version').val($(this).data("id_version"));
                    $('#titulo_propuesta').val($(this).data("titulo_propuesta"));
                    $('#Tipo_Movimiento').val('ACTUALIZAR');
                    Obtener_Jurado($(this).data("id_propuesta"), $(this).data("id_version"));
                    $('#ventanaJurado').dialog('open');
                });
                                                
                //OBTENEMOS LOS SINODALES
                function Obtener_Jurado(id_propuesta, id_version){   
                    $('#ventanaProcesando').dialog('open');        
                    var datos = {Tipo_Movimiento : 'OBTENER_MIS_SINODALES',
                               id_propuesta : id_propuesta,
                               id_version : id_version
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_Alumno_Mi_Jurado.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                            var html_table = '<TABLE class="tabla_Registros">';
                            html_table = html_table + '<TR><TH style="text-align:center;" >No.</TH>\n\
                                                      <TH>Sinodal Propuesto</TH>\n\
                                                      <TH>Sinodal Definitivo</TH></TR>';                           
                            if (respuesta.success == true){
                               var nombre_textarea='';
                               var data_textarea = '';
                               var solo_lectura = '';
                               
                               $.each(respuesta.data.registros, function( key, value ) {                                   
                                   if($('#id_Estatus').val()=='3'){
                                       solo_lectura = ' readonly ';
                                   }
                                   nombre_textarea = ' id="Sinodal_' + value['num_profesor'] + '" ';
                                   data_textarea = " data-num_profesor = " + value['num_profesor'] + " ";                                                                      
                                   html_table += '<TR><TD style="text-align:center; vertical-align:middle;width:50px;">' + value['num_profesor'] + '</TD>';
                                   html_table += '<TD><input type="text" maxlength="100" class="input_Parametro" ' +
                                           'style="width:300px; text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" ' +
                                           'title="Capture únicamente letras y puntos" ' + solo_lectura +
                                           nombre_textarea + data_textarea + 
                                           ' value="' + esNulo(value['nombre_sinodal_propuesto']) + '" autocomplete="off"\n\
                                            placeholder="ING. ADOLFO GÚZMAN ARENAS"></TD>';
                                   html_table += '<TD style="vertical-align:middle;width:300px;">' + esNulo(value['sinodal_definitivo']) + '</TD></TR>';
                               });
                               html_table += '</TABLE>';                               
                               $('#tabla_Sinodales').html(html_table);
                               $('#ventanaProcesando').dialog('close');    

                           }
                            else {
                                   html_table = html_table + '<TR><TD style="text-align:center;" colspan="3">' + respuesta.data.message + '</TD></TR>';
                                   html_table = html_table + '</TABLE>'
                                   $('#tabla_Sinodales').empty();
                                   $('#tabla_Sinodales').html(html_table);
                                   $('#ventanaProcesando').dialog('close');
                            }                                                                   
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                    var html_table = '<TABLE class="tabla_Registros">';
                                    html_table = html_table + '<TR><TH>No.</TH>\n\
                                                              <TH>Sinodal Propuesto</TH>\n\
                                                              <TH>Sinodal Definitivo</TH></TR>';                           
                                
                                    html_table = html_table + '<TR><TD style="text-align:center;" colspan="3">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                    html_table = html_table + '</TABLE>';
                                    $('#tabla_Sinodales').empty();
                                    $('#tabla_Sinodales').html(html_table);  
                                    $('#ventanaProcesando').dialog('close');                                          
                            });                                                                             
                }//fin Obtenemos los Sinodales
               
                //VALIDACIONES 
                function validaDatos(){
                
                    //RECORREMOS LOS INPUT
                    var datosValidos = true;                    
                    var lista_VoBo = '';
                    var refnom_textarea = '';
                    
                    $("#ventanaJurado input[type=text]").each(function(index){
                        if((!$(this).prop('value').match(miExpReg_Nombre_Sinodal))){                            
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
                                $('#ventanaAviso').html("Debe capturar todos los Sinodales Propuestos. Y solo se aceptan los carácteres A-Z . Ñ");
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
                   draggable : true,
                   height : 'auto',
                   width : '750',
                   dialogClass : 'no-close',
                   show : 'slide',
                   hide : 'slide',
                   closeOnEscape : false,
		   position : {at: 'center top'},                   
                   open : function(){
                       if($('#id_Estatus').val()==11 || $('#id_Estatus').val()==0 ){
                            $("#btn_Guardar").button("option", "disabled", false);
                        }
                        else{
                            $("#btn_Guardar").button("option", "disabled", true);
                        }                            
                   },
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
                            $('#ventanaProcesando').dialog('open');
                            
                            // Por Ajax Actualizamos a los Sinodales
                            var nom_sinodal ='';
                            var control = '';
                            var json_registros ='';
                            var i=0;
                            for(i=1; i<=5; i++){
                                control = '#Sinodal_' + i;
                                nom_sinodal = $(control).val();
                                json_registros += i + ':' + nom_sinodal + '|';
                            }
                            json_registros = json_registros.substr(0,json_registros.length -1);
                            $('#id_sinodales').val(json_registros);

                            var formDatos = $('#frm_MJ').serialize();
                            
                            $.ajax({
                                data : formDatos,
                                type : "POST",
                                dataType : "json",
                                url : "_Negocio/n_Alumno_Mi_Jurado.php"
                            })
                                .done(function(respuesta,textStatus,jqXHR){
                                    $('#ventanaProcesando').dialog('close');
                                    if (respuesta.success == true){
                                        $("#btn_Guardar").button("option", "disabled", true);
                                        Obtener_Mi_Propuesta($('#Id_Usuario').val(), $('#Id_Carrera').val());
                                        
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

                $('#tabla_Sinodales').on("focus", "input:text, textarea", function(e){
                    e.preventDefault();
                    f5($(document),false);                    
                });
                $('#tabla_Sinodales').on("blur", "input:text, textarea", function(e){
                    e.preventDefault();
                    f5($(document),true);                    
                });
                
                f5($(document),true);
                Obtener_Carreras_Del_Alumno();
                
            });
                        
        </script>
        
        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                    <p>Mi Jurado</p>
                </div>
                <div class="barra_Parametros">
                    <label for="selec_Mis_Carreras" class="etiqueta_Parametro">Mi Carrera:</label>
                    <select id="selec_Mis_Carreras" name="selec_Mis_Carreras" class="combo_Parametro">
                    </select>                            
                </div>                            
                <div id="tabla_Mi_Jurado" class="tabla_Registros">
                </div>
            </div>                 
        </div>
        <div id='ventanaJurado' name="ventanaJurado">    
            <form id="frm_MJ" name="frm_MJ" method="" action="" autocomplete="off">
                <div id="tabla_Sinodales" class="tabla_Registros">                    
                </div>
                <input type="hidden" id="Id_Propuesta" name="Id_Propuesta" value=""> 
                <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value=""> 
                <input type="hidden" id="Id_Carrera" name="Id_Carrera" value="">
                <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">            
                <input type="hidden" id="id_Estatus" name="id_Estatus" value="0">
                <input type="hidden" id="id_Version" name="id_Version" value="0">
                <input type="hidden" id="id_sinodales" name="id_sinodales" value="0">
                <input type="hidden" id="titulo_propuesta" name="titulo_propuesta" value="0">
            </form>
        </div>

        <div id='ventanaConfirmacion'>
            Desea Solicitar el Jurado Propuesto ?
        </div>
        <div id="ventanaAvisos">
            <span id="ventanaAviso"></span>
        </div>

        <div id="ventanaProcesando" data-role="header">
            <img id="cargador" src="css/images/engrane2.gif"/><br>
            Procesando su transacción....!<br>
            Espere por favor.
        </div>

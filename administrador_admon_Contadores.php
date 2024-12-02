<!DOCTYPE html>
<!--
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para la admon de los Contadores para Serv Social y Propuesta del Profesor
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

                //CAMBIO LA SELECCION EN EL LISTBOX
                $('#catalogos').change(function(e){
                    e.preventDefault();
                    $('#tipo_Catalogo_Select').val($('#catalogos').val());
                    Obtener_Catalogo($('#catalogos').val());
                });
                
                $('#nuevoPeriodo').keypress(function(e){
                    e.preventDefault();
                    return false;
                });
                
                //OBTENEMOS EL CATALOGO SELECCIONADO
                function Obtener_Catalogo(cat_seleccionado){  
                    var tipo_movi="";
                    var titulo_cat ="";
                    if(cat_seleccionado == 'SS'){
                        tipo_movi = "OBTENER_ULTIMO_CONTADOR_SS";
                        titulo_cat = "Último Contador para Servicio Social";
                    }
                    else if(cat_seleccionado == 'PP'){
                        tipo_movi = "OBTENER_ULTIMO_CONTADOR_PP";
                        titulo_cat = "Último Contador para Propuesta de Profesor";
                    }
                    else if(cat_seleccionado == 'CE'){
                        tipo_movi = "OBTENER_ULTIMO_CONTADOR_CEREMONIA";
                        titulo_cat = "Último Contador para Ceremonias";
                    }
                        
                    $('#ventanaProcesando').dialog('open');        
                    var datos = {Tipo_Movimiento : tipo_movi
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_administrador_admon_Contadores.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table='';
                           if(cat_seleccionado == 'SS'){
                                html_table = '<TABLE class="tabla_Registros">';
                                html_table = html_table + '<TR><TH>Año</TH>\n\
                                                          <TH>Mes</TH>\n\
                                                          <TH>Consecutivo</TH></TR>';

                                if (respuesta.success == true){
                                   $.each(respuesta.data.registros, function( key, value ) {
                                       html_table += '<TR><TD>' + value['anio'] + '</TD>';
                                       html_table += '<TD>' + value['mes'] + '</TD>';
                                       html_table += '<TD>' + value['consecutivo'] + '</TD>';
                                       html_table += '</TR>';                                   
                                   });

                                   html_table += '</TABLE>';                               
                                   $('#tabla_Catalogo').html(html_table);
                                   $('#ventanaProcesando').dialog('close');    
                               }
                                else {
                                       html_table = html_table + '<TR><TD style="text-align:center;" colspan="3">' + respuesta.data.message + '</TD></TR>';
                                       html_table = html_table + '</TABLE>'
                                       $('#tabla_Catalogo').empty();
                                       $('#tabla_Catalogo').html(html_table);
                                       $('#ventanaProcesando').dialog('close');
                                } 
                            }
                            else{
                                html_table = '<TABLE class="tabla_Registros">';
                                html_table = html_table + '<TR><TH>Año</TH>\n\
                                                          <TH>Semestre</TH>\n\
                                                          <TH>Consecutivo</TH></TR>';

                                if (respuesta.success == true){
                                   $.each(respuesta.data.registros, function( key, value ) {
                                       html_table += '<TR><TD>' + value['anio'] + '</TD>';
                                       html_table += '<TD>' + value['semestre'] + '</TD>';
                                       html_table += '<TD>' + value['consecutivo'] + '</TD>';
                                       html_table += '</TR>';                                   
                                   });

                                   html_table += '</TABLE>';                               
                                   $('#tabla_Catalogo').html(html_table);
                                   $('#ventanaProcesando').dialog('close');    
                               }
                                else {
                                       html_table = html_table + '<TR><TD style="text-align:center;" colspan="3">' + respuesta.data.message + '</TD></TR>';
                                       html_table = html_table + '</TABLE>'
                                       $('#tabla_Catalogo').empty();
                                       $('#tabla_Catalogo').html(html_table);
                                       $('#ventanaProcesando').dialog('close');
                                } 
                            
                            }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                    var html_table = '';
                                    $('#ventanaProcesando').dialog('close');
                                    if(cat_seleccionado == 'SS'){
                                        html_table = '<TABLE class="tabla_Registros">';
                                        html_table = html_table + '<TR><TH>Año</TH>\n\
                                                                  <TH>Mes</TH>\n\
                                                                  <TH>Consecutivo</TH></TR>';
                                    }
                                    else{
                                        html_table = '<TABLE class="tabla_Registros">';
                                        html_table = html_table + '<TR><TH>Año</TH>\n\
                                                                  <TH>Semestre</TH>\n\
                                                                  <TH>Consecutivo</TH></TR>';                                        
                                    }
                                    html_table = html_table + '<TR><TD style="text-align:center;" colspan="3">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                    html_table = html_table + '</TABLE>';
                                    $('#tabla_Catalogo').empty();
                                    $('#tabla_Catalogo').html(html_table);  
                            });                                                                             
                }//fin Obtenemos el catalogo seleccionado
               
                $('#btn_Generar').on("click", function(e){
                    e.preventDefault();
                    var tipo_catalogo = $('#tipo_Catalogo_Select').val();

                    if($('#nuevoPeriodo').val() == ''){
                        $('#ventanaAviso').html('Debe indicar el Período que desea Generar.');
                        $('#ventanaAvisos').dialog('open');  
                        return false;
                    }                    
                    
                    $('#ventanaConfirmacion').dialog({
                       buttons:{
                            "Aceptar" : function() {
                                $('#ventanaConfirmacion').dialog('close');
                                $('#ventanaProcesando').dialog('open');
                                var tipo_mov ='';
                                if(tipo_catalogo == 'SS'){
                                    tipo_mov ='AGREGAR_PERIODO_SS';
                                }
                                else if(tipo_catalogo == 'PP'){
                                    tipo_mov ='AGREGAR_PERIODO_PP';
                                }
                                else if(tipo_catalogo == 'CE'){
                                    tipo_mov ='AGREGAR_PERIODO_CEREMONIA';
                                }
                                
                                // Por Ajax Generamos el Período
                                var datos = {
                                    Tipo_Movimiento : tipo_mov,
                                    periodo : $('#nuevoPeriodo').val(),
                                    id_administrador : $('#Id_Usuario').val()
                                };

                                $.ajax({
                                    data : datos,
                                    type : "POST",
                                    dataType : "json",
                                    url : "_Negocio/n_administrador_admon_Contadores.php"
                                })
                                    .done(function(respuesta,textStatus,jqXHR){
                                        $('#ventanaProcesando').dialog('close');
                                        if (respuesta.success == true){
                                            $('#ventanaBuscar').dialog('close');
                                            $('#catalogos').val(tipo_catalogo).change();
                                            $('#ventanaAviso').html(respuesta.data.message);
                                            $('#ventanaAvisos').dialog('open');                                        
                                        }
                                        else {
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
                            }                       
                       },
                       open : function(){
                           if(tipo_catalogo == 'SS'){
                                $('#ventanaConfirmacion').html('Desea Generar el nuevo Período para Servicio Social ?');
                            }
                           else if(tipo_catalogo == 'PP'){
                                $('#ventanaConfirmacion').html('Desea Generar el nuevo Período para Propuestas de Profesor ?');
                           }
                           else if(tipo_catalogo == 'CE'){
                                $('#ventanaConfirmacion').html('Desea Generar el nuevo Período para Ceremonias ?');
                           }
                           
                       },
                       title: 'Admon.de Contadores',
                       modal : true,
                       autoOpen : true,
                       resizable : true,
                       width : "450",
                       draggable : true,
                       dialogClass : 'no-close ventanaConfirmaUsuario',
                       closeOnEscape : false   
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
                
                f5($(document),true);
                $('#catalogos').val('SS').change();
                $('#ventanaConfirmacion').hide();
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
                    <p>Administración de Contadores</p>
                </div>                        
            </div>
            <div class="barra_Parametros">
                <div>
                    <p>
                        <label for="catalogos" class="etiqueta_Parametro" style="padding-right: 28px;">Generar Período para:</label>
                        <select name="catalogos" id="catalogos" class="combo_Parametro">
                            <option value="SS">Servicio Social</option>
                            <option value="PP">Propuestas de Profesor</option>
                            <option value="CE">Ceremonias</option>
                        </select>
                    </p>                                                                                                                                 
                </div>
                <div>
                    <p>
                        <label for="nuevoPeriodo" class="etiqueta_Parametro">Nuevo Período a Generar:</label>
                        <input type="number" min="2017" max="2025" 
                               name="nuevoPeriodo" id="nuevoPeriodo" class="input_Parametro" style="width: 240px;" autocomplete="off"/>
                        <input type="button" id="btn_Generar" name="btn_Generar" value="Generar" class="btn_Herramientas" style="margin-left: 20px;"/>

                    </p>                                                
                </div>                            
            </div>
            <div id="tabla_Catalogo" class="tabla_Registros" style="height: 440px;">
            </div>
                
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">            
            <input type="hidden" id="tipo_Catalogo_Select" name="tipo_Catalogo_Select" value="0">                
           
        </div>
       
        <div id='ventanaConfirmacion'>
            Desea Generar el Período indicado  ?
        </div>
        <div id="ventanaAvisos">
            <span id="ventanaAviso"></span>
        </div>

        <div id="ventanaProcesando" data-role="header">
            <img id="cargador" src="css/images/engrane2.gif"/><br>
            Procesando su transacción....!<br>
            Espere por favor.
        </div>
<!--    </body>
</html>-->



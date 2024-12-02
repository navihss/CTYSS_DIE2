<!DOCTYPE html>
<!--
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para la admon del Catálogo Coordinaciones / Departamentos
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
                    $('#tabla_Catalogo').html('');
                    Obtener_Catalogo($('#catalogos').val());
                });
                                                      
                //OBTENEMOS EL CATALOGO SELECCIONADO
                function Obtener_Catalogo(cat_seleccionado){  
                    var tipo_movi="";
                    var titulo_cat ="";
                    tipo_movi = "OBTENER_AREAS_JEFES";
                    if(cat_seleccionado == 'C'){
                        titulo_cat = "Coordinaciones";
                    }
                    else if(cat_seleccionado == 'D'){
                        titulo_cat = "Departamentos";
                    }
                    else{
                        return false;
                    }
                    $('#ventanaProcesando').dialog('open');        
                    var datos = {Tipo_Movimiento : tipo_movi,
                                tipo_catalogo : cat_seleccionado
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_administrador_admon_Coord_Dptos.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                            var html_table = '<TABLE style="width:100%;">';
                            html_table = html_table + '<TR><TH>Carrera</TH>\n\
                                                      <TH>' + titulo_cat + '</TH>\n\
                                                      <TH>Jefe Actual</TH>\n\
                                                      <TH style="text-align:center">Acción</TH></TR>';
        
                            if (respuesta.success == true){
                               var nombre_textarea='';
                               var btn_Buscar = '';

                               $.each(respuesta.data.areas, function( key, value ) {
                                   var id_jefe = 0;
                                       
                                   btn_Buscar = '<button class="btn_Buscar btnOpcion" id="btn_' + value['id'] +  '" data-id_usuario_actual =' + id_jefe + ' ' + 
                                            ' data-tipo_cat= \'' + cat_seleccionado + '\' ' +
                                            ' data-descripcion_area= \'' + value['descripcion'] + '\' ' +
                                            ' data-id_area= ' + value['id'] + '>Buscar</button>';
                                   
                                   nombre_textarea = " id=txt_area_" + value['id'] + " ";
                                   html_table += '<TR><TD style="vertical-align:text-top;width:250px;">' + value['descripcion_carrera'] + '</TD>';
                                   html_table += '<TD style="vertical-align:text-top;width:250px;">' + value['descripcion'] + '</TD>';
                                   html_table += '<TD><input type="text" class="input_Parametro" style="width:450px;" value="" ' + nombre_textarea + ' readonly>' + '</TD>';
                                   // Se comenta la siguiente linea ya que esta en espera de definicion 2024Ene27
                                   // html_table += '<TD style="text-align:center; vertical-align:text-top;width:150px;">' + btn_Buscar + '</TD></TR>';
                                   html_table += '<TD style="text-align:center; vertical-align:text-top;width:150px;"></TD></TR>';
                               });

                               html_table += '</TABLE>';                               
                               $('#tabla_Catalogo').html(html_table);

                               var id_jefe = 0;
                               var ctrl_btn = '';
                               var ctrl_txt = '';
                               var nombre = '';
                               
                               $.each(respuesta.data.jefes_actuales, function( key, value ) {
                                   if (value['id_jefe']){
                                       id_jefe = value['id_jefe'];
                                   }
                                   if (value['nombre']){
                                       nombre = value['nombre'];
                                   }
                                   
                                   ctrl_btn = '#btn_' + value['id'];
                                   $(ctrl_btn).data('id_usuario_actual', id_jefe);
                                   ctrl_txt = '#txt_area_' + value['id'];
                                   $(ctrl_txt).val(nombre);
                               });

                               $('#ventanaProcesando').dialog('close');    
                           }
                            else {
                                   html_table = html_table + '<TR><TD style="text-align:center;" colspan="4">' + respuesta.data.message + '</TD></TR>';
                                   html_table = html_table + '</TABLE>'
                                   $('#tabla_Catalogo').empty();
                                   $('#tabla_Catalogo').html(html_table);
                                   $('#ventanaProcesando').dialog('close');
                            }                                                                   
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                        $('#ventanaProcesando').dialog('close');
                                        var html_table = '<TABLE class="tabla_Registros">';
                                        html_table = html_table + '<TR><TH>Carrera</TH>\n\
                                                                  <TH>' + titulo_cat + '</TH>\n\
                                                                  <TH>Jefe Actual</TH>\n\
                                                                  <TH style="text-align:center">Acción</TH></TR>';
                                        html_table = html_table + '<TR><TD style="text-align:center;" colspan="4">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                        html_table = html_table + '</TABLE>';
                                        $('#tabla_Catalogo').empty();
                                        $('#tabla_Catalogo').html(html_table);  
                            });                                                                             
                }//fin Obtenemos el catalogo seleccionado
               
                $('#tabla_Catalogo').on("click", "button.btn_Buscar", function(e){
                    e.preventDefault();
                    var catalogo_seleccionado = $(this).data('tipo_cat');
                    var id_usuario_actual = $(this).data('id_usuario_actual');
                    var id_area = $(this).data('id_area');
                    var desc_area = $(this).data('descripcion_area');


                    $('#tipo_Catalogo_Select').val(catalogo_seleccionado);
                    $('#id_actual_coorddpto').val(id_usuario_actual);
                    $('#id_area_actualizar').val(id_area);
                    $('#ventanaBuscar').dialog({
                       buttons:{
                            "Aceptar" : function() {
//                                $(this).dialog('close');
//                                $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
//                                $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                                $('#ventanaProcesando').dialog('open');   

                                //OBTENEMOS A QUIEN SELECCIONO
                                var id_nuevo_coord_dpto = $('input[type=radio]:checked').data('id_nuevo_usuario');
                                var nombre_coord_dpto = $('input[type=radio]:checked').data('nombre');

                                $('#id_nuevo_coorddpto').val(id_nuevo_coord_dpto);
                                $('#nom_nvo_jefe').val(nombre_coord_dpto);
                                
                                $('#ventanaConfirmacion').dialog('open');
                                
                                $('#ventanaProcesando').dialog('close');   
                            },
                            "Cerrar" : function() {
                                $(this).dialog('close');
                            }                       
                       },
                       open : function(){
                           $('#ventanaBuscar').dialog('option','title',desc_area);
                           $('#resultadoBusqueda').empty();
                           $('#textoBuscar').val('');
                       },                   
                       title: 'Buscar',
                       modal : true,
                       autoOpen : true,
                       resizable : true,
                       draggable : true,
                       width : "650",
                       height : "500",
                       show : 'slide',                        
                       hide : 'slide',
                       position : {at: 'center top'},
                       dialogClass : 'no-close',
                       closeOnEscape : false                       
                    });
                });
                
                $('#btn_Buscar_Nombre').on("click",function(e){                
                    if(!$('#textoBuscar').val().match(miExpReg_Buscar)){
                        $('#ventanaAviso').html('Sólo debe Capturar letras y signos de puntuación.');
                        $('#ventanaAvisos').dialog('open');                        
                    }
                    else{
                        e.preventDefault();
                        Obtener_Ocurrencias($('#textoBuscar').val());
                    }
                });
                
                //OBTENEMOS LAS OCURRENCIAS DE LA BUSQUEDA
                function Obtener_Ocurrencias(textoBuscar){
                    var tipo_cat = $('#tipo_Catalogo_Select').val();
                    var tit_columna = 'Coordinador';
                    var tipo_mov = 'BUSCAR_COORDINADORES';
                    if(tipo_cat == "D"){
                        tipo_mov = 'BUSCAR_JEFES_DPTO';
                        tit_columna = 'Jefe de Departamento';
                    }
                    $('#ventanaProcesando').dialog('open');        
                    var datos = {Tipo_Movimiento : tipo_mov,
                               textoBuscar : textoBuscar,
                               tipo_catalogo :  $('#tipo_Catalogo_Select').val(),
                               id_coord_dpto : $('#id_area_actualizar').val()
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_Catalogos_Generales_B.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE style="width:100%;">';
                           html_table = html_table + '<TR><TH>Id</TH>\n\
                                                      <TH>' + tit_columna + '</TH>\n\
                                                      <TH>Seleccionado</TH></TR>';
                            console.log("HOLAAAAAAAAAAAAAAAAAAAAAAAAAAAA");
                            console.log(respuesta.data.registros);
                            console.log(respuesta.success);                        
                           if (respuesta.success == true){
                               var radio ="";
                               //recorremos cada registro
                               var data_radio ='';
                               
                               $.each(respuesta.data.registros, function( key, value ) {
                                   data_radio =' data-id_nuevo_usuario = \'' + value['id'] + '\' ' + 
                                           ' data-nombre= \'' + value['descripcion_grado_estudio'] + ' ' + value['nombre'] +  '\' ' ;
                                   radio = "<input type='radio' name='usuarios' \n\
                                        value='" +  value['id'] + "' " + data_radio + ">";
                                   
                                   html_table = html_table + '<TR>';
                                   html_table = html_table + '<TD>' + value['id'] + '</TD>';
                                   html_table = html_table + '<TD>' + value['nombre'] + '</TD>';
                                   html_table = html_table + '<TD style="text-align:center;">' + radio + '</TD>';
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#resultadoBusqueda').empty();
                               $('#resultadoBusqueda').html(html_table);  
                               $('#ventanaProcesando').dialog('close');
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="3" style="text-align:center;">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#resultadoBusqueda').empty();
                               $('#resultadoBusqueda').html(html_table);
                               $('#ventanaProcesando').dialog('close');
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                    $('#ventanaProcesando').dialog('close');
                                    var html_table = '<TABLE class="tabla_Registros">';
                                    html_table = html_table + '<TR><TH>Id</TH>\n\
                                                               <TH>' + tit_columna + '</TH>\n\
                                                               <TH>Seleccionado</TH></TR>';                           
                            
                                    html_table = html_table + '<TR><TD colspan="3" style="text-align:center;">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                    html_table = html_table + '</TABLE>'
                                    $('#resultadoBusqueda').empty();
                                    $('#resultadoBusqueda').html(html_table);
                            });                                                                             
                }//fin Obtenemos las ocurrencias
                                                 

                //VALIDACIONES 
                function validaDatos(){
                    var datosValidos = true;                    
                    if($('#resultadoBusqueda input[type=radio]:checked').size() == 0){      
                                datosValidos = false;                                
                    }                                      
                    return datosValidos;
                }
                //FIN VALIDACIONES PARA GUARDAR

                $('#ventanaConfirmacion').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $('#ventanaConfirmacion').dialog('close');
                            $('#ventanaProcesando').dialog('open');
                            var tipo_catalogo = $('#tipo_Catalogo_Select').val();
                            var id_jefe_nuevo = $('#id_nuevo_coorddpto').val();
                            var id_jefe_actual = $('#id_actual_coorddpto').val();
                            var nom_nvo_jefe = $('#nom_nvo_jefe').val();
                            
                            // Por Ajax Actualizamos el jefe actual
                            var datos = {
                                Tipo_Movimiento : 'ACTUALIZAR_JEFE_COORD_DPTO',
                                tipo_catalogo : tipo_catalogo,
                                id_jefe_nuevo : id_jefe_nuevo,
                                id_jefe_actual : id_jefe_actual,
                                nom_nvo_jefe    : nom_nvo_jefe,
                                id_administrador    : $('#Id_Usuario').val()
                            };

                            $.ajax({
                                data : datos,
                                type : "POST",
                                dataType : "json",
                                url : "_Negocio/n_administrador_admon_Coord_Dptos.php"
                            })
                                .done(function(respuesta,textStatus,jqXHR){
                                    $('#ventanaProcesando').dialog('close');
                                    if (respuesta.success == true){
                                        $('#ventanaBuscar').dialog('close');
                                        Obtener_Catalogo(tipo_catalogo);
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
                        if(!validaDatos()){
                            $('#ventanaAviso').html('No existe un elemento seleccionado.');
                            $('#ventanaAvisos').dialog('open');                                        
                            $(this).dialog('close');
                            return false;
                        }
                       
                       if($('#tipo_Catalogo_Select').val() == 'C'){
                            $('#ventanaConfirmacion').html('Desea Actualizar el Jefe de Coordinación con el Seleccionado ?');
                        }
                        else{
                            $('#ventanaConfirmacion').html('Desea Actualizar el Jefe de Departamento con el Seleccionado ?');
                        }
                   },
                   title: 'Admon.de Catálogos de Coord. y Dptos.',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   width : "450",
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
                /*$('.entrada_Dato').focus(function(e){
                    e.preventDefault();
                    f5($(document),false);
                });
                $('.entrada_Dato').blur(function(e){
                    e.preventDefault();
                    f5($(document),true);
                });
                
                f5($(document),true); */
                $('#ventanaBuscar').hide();
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
                    <p>Administración de Coordinaciones y Departamentos</p>
                </div>                        
                <div id="barraHerramienta">
                </div>                   
            </div>
            <div class="barra_Parametros">
                <p>
                    <label for="catalogos" class="etiqueta_Parametro">Catálogo:</label>
                    <select name="catalogos" id="catalogos" class="combo_Parametro">
                        <option value="">Seleccione un Catálogo</option>
                        <option value="C">Coordinaciones</option>
                        <option value="D">Departamentos</option>
                    </select>
                </p>                                                                                                                                 
            </div> 
            <div id="tabla_Catalogo" class="tabla_Registros">
            </div>
        </div>                
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">            
            <input type="hidden" id="tipo_Catalogo_Select" name="tipo_Catalogo_Select" value="0">
            <input type="hidden" id="id_actual_coorddpto" name="id_actual_coorddpto" value="0">
            <input type="hidden" id="id_nuevo_coorddpto" name="id_nuevo_coorddpto" value="0">
            <input type="hidden" id="id_area_actualizar" name="id_area_actualizar" value="0">
            <input type="hidden" id="nom_nvo_jefe" name="nom_nvo_jefe" value="0"> 
        </div>

        <div id='ventanaBuscar' class="barra_Parametros">
            <p>
                <label for="textoBuscar" class="etiqueta_Parametro">Nombre:</label>
                <input type="text" name="textoBuscar" id="textoBuscar" maxlength="50" onkeyup="javascript:this.value=this.value.toUpperCase();"
                       title="Capture únicamente letras" autocomplete="off" class="entrada_Dato input_Parametro" style="width: 400px;"/>
                <input type="button" name="btn_Buscar_Nombre" id="btn_Buscar_Nombre" 
                       value='Buscar' class="btn_Herramientas" value='' maxlength="50" placeholder="" style="width: 100px;"
            </p>                                                
                        
            <div id='resultadoBusqueda' class="tabla_Registros" style="padding-top: 10px;">                
            </div>
        </div>
       
        <div id='ventanaConfirmacion'>
            Desea Actualizar el Jefe de Coordinación con el Seleccionado ?
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



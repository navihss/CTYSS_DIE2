
<!DOCTYPE html>
<!--
Fecha:          Junio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para la captura de las carreras que cursa el Alumno
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
        
        <script>
            $( document ).ready(function() {

                $('#tabla_Mis_Carreras').on("click", "button", function(e){
                    e.preventDefault();
                    $('#Id_Carrera').val($(this).data("id_carrera"));
                    $('#ventanaConfirmacionBorrar').text('Desea borrar la Carrera ' + $(this).data("id_carrera") + '?');
                    $('#ventanaConfirmacionBorrar').dialog('open');
                });

                function Obtener_Carreras_Del_Alumno(){                                           
                    //Obtenemos las Carreras actuales del Alumno
                    const button_Agregar = document.getElementById('Agregar_Carrera');
                    var datos = {Tipo_Movimiento : 'OBTENER',
                               Id_Usuario : $('#Id_Usuario').val(),
                               Id_Carrera : 0,
                               Id_Division : $('#Id_Division').val(),
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_Alumno_Carrera.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE style="width:100%;">';
                           html_table = html_table + '<THEAD><TR><TH style="width:30%;">Clave</TH>\n\
                            <TH style="width:30%;">Carrera</TH>\n\
                            <TH>Estatus</TH></TR></THEAD>';
                            var num_carreras = Object.keys(respuesta.data.registros).length;
                            num_carreras = parseInt(num_carreras);
                            //console.log(num_carreras);
                           if (num_carreras > 0){
                            button_Agregar.setAttribute('disabled', '');
                           }else{
                            button_Agregar.removeAttribute('disabled');
                           }
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                   html_table = html_table + '<TR>';
                                   html_table = html_table + '<TD style="width:30%;">' + value['id_carrera'] + '</TD>';
                                   html_table = html_table + '<TD style="width:30%;text-align:left;">' + value['descripcion_carrera'] + '</TD>';
                                   html_table = html_table + '<TD>' + value['descripcion_estatus'] + '</TD>';
//                                   html_table = html_table + '<TD style="text-align:center;"><button class="btnOpcion" data-id_alumno=\'' +  
//                                            value['id_alumno'] + '\' data-id_carrera=' + value['id_carrera'] +'>Borrar</button></TD>';
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Mis_Carreras').empty();
                               $('#tabla_Mis_Carreras').html(html_table);                                
                               }
                           else {
                                var html_table = '<TABLE style="width:100%;">';
                                html_table = html_table + '<THEAD><TR><TH style="width:30%;">Clave</TH>\n\
                                 <TH style="width:30%;">Carrera</TH>\n\
                                 <TH>Estatus</TH></TR></THEAD>';
                                html_table = html_table + '</TABLE>'
                               $('#tabla_Mis_Carreras').empty();
                               $('#tabla_Mis_Carreras').html(html_table);
                           }
                       })
                               .fail(function(jqXHR,textStatus,errorThrown){
                                   var html_table = '<TABLE class="tabla_Registros">';
                                   html_table = html_table + '<TR><TH>Clave</TH><TH>Carrera</TH><TH>Estatus</TR>';
                                   html_table = html_table + '<TR><TD style="text-align:center;" colspan="3">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                   html_table = html_table + '</TABLE>';
                                   $('#tabla_Mis_Carreras').empty();
                                   $('#tabla_Mis_Carreras').html(html_table);                                
                               });                                                         
                                       
                }   //fin Obtenemos los carreras actuales del Alumno                    
                
                //Mostramos TODAS las Carreras MENOS las que ya tiene agregadas
                $('#Agregar_Carrera').on('click',function(event){
                event.preventDefault();
                var datos = {Tipo_Movimiento : 'SELECCIONAR',
                             Id_Usuario : $('#Id_Usuario').val(),
                             Id_Carrera : 0
                            }
                $.ajax({
                    data : datos,
                    type : "POST",
                    dataType : "json",
                    url : "_Negocio/n_Alumno_Carrera.php"
                })
                    .done(function(respuesta,textStatus,jqXHR){
                        var html_NewRow = '<TABLE style="width:100%;">';
                        html_NewRow = html_NewRow + '<TR><TH style="width:30%;">Clave</TH>\n\
                                    <TH style="width:30%;">Carrera</TH>\n\
                                    <TH>Seleccionada</TH></TR>';
                        if (respuesta.success == true){
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function( key, value ) {
                                html_NewRow = html_NewRow + '<TR>';
                                html_NewRow = html_NewRow + '<TD style="width:30%;">' + value['id_carrera'] + '</TD>';
                                html_NewRow = html_NewRow + '<TD style="width:30%;text-align:left;">' + value['descripcion_carrera'] + '</TD>';
                                html_NewRow = html_NewRow + '<TD style="width:30%;text-align:center;"><input name="carrera" id="carrera" type="radio" value="' + value['id_carrera'] + '"</TD>';
                                html_NewRow = html_NewRow + '</TR>';
                            });
                            html_NewRow = html_NewRow + '</TABLE>';
                            $('#tabla_Seleccionar_Carrera').empty();
                            $('#tabla_Seleccionar_Carrera').html(html_NewRow);                                
                        }
                        else {
                            html_NewRow = html_NewRow + '<TR><TD style="text-align:center;" colspan="3">' + respuesta.data.message + '</TD></TR></TABLE>';

                            $('#tabla_Seleccionar_Carrera').empty();
                            $('#tabla_Seleccionar_Carrera').html(html_NewRow);
                        }
                    })
                        .fail(function(jqXHR,textStatus,errorThrown){
                            var html_NewRow = '<TABLE style="width:100%;">';
                            html_NewRow = html_NewRow + '<TR><TH style="width:30%;">Clave</TH>\n\
                                        <TH style="width:30%;">Carrera</TH>\n\
                                        <TH>Seleccionada</TH></TR>';

                            $('#tabla_Seleccionar_Carrera').empty();
                            $('#tabla_Seleccionar_Carrera').html(html_NewRow);                                
                        });                    

                    $('#ventanaSeleccionarCarrera').dialog('open');
                });
                //FIN Mostramos TODAS las Carreras MENOS las que ya tiene agregadas
                
                
                $('#ventanaConfirmacionAgregarCarrera').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $(this).dialog('close');
//                            $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
//                            $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                            $('#ventanaProcesando').dialog('open');
//                            
                            // Por Ajax insertamos la Carrera Seleccionada
                            var id_usr = $('#Id_Usuario').val();
                            var carrera_Seleccionada = $('input:radio[name=carrera]:checked').val();
                            $.ajax({
                                data : {Tipo_Movimiento : 'AGREGAR',
                                        Id_Usuario : id_usr,
                                        Id_Carrera : carrera_Seleccionada
                                        },
                                type : "POST",
                                dataType : "json",
                                url : "_Negocio/n_Alumno_Carrera.php"
                            })
                                .done(function(respuesta,textStatus,jqXHR){
                                    $('#ventanaProcesando').dialog('close');
                                    $('#ventanaAviso').html(respuesta.data.message);
                                    $('#ventanaAvisos').dialog('open');  

                                    Obtener_Carreras_Del_Alumno();                                        
                                    $('#ventanaConfirmacionAgregarCarrera').dialog('close');
                                    $('#ventanaSeleccionarCarrera').dialog('close');
                                    
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
                   title: 'Mis Carreras',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                   dialogClass : 'no-close ventanaConfirmaUsuario',
                   closeOnEscape : false   
                });

                //BORRAR UN ALUMNO-CARRERA
                $('#ventanaConfirmacionBorrar').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $(this).dialog('close');
//                            $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
//                            $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                            $('#ventanaProcesando').dialog('open');
//                            
                            // Por Ajax borramos el Alumno-Carrea Seleccionada
                            var id_usr = $('#Id_Usuario').val();
                            var id_carrera = $('#Id_Carrera').val();

                            $.ajax({
                                data : {Tipo_Movimiento : 'BORRAR',
                                        Id_Usuario : id_usr,
                                        Id_Carrera : id_carrera
                                        },
                                type : "POST",
                                dataType : "json",
                                url : "_Negocio/n_Alumno_Carrera.php"
                            })
                                .done(function(respuesta,textStatus,jqXHR){
                                    $('#ventanaProcesando').dialog('close');
                                    $('#ventanaAviso').html(respuesta.data.message);
                                    $('#ventanaAvisos').dialog('open');  

                                    Obtener_Carreras_Del_Alumno();                                        
                                    $('#ventanaConfirmacionBorrar').dialog('close');
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
                   title: 'Mis Carreras',
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
                   closeOnEscape : false,
                   dialogClass : 'no-close no-titlebar'                   
                });  

                $('#ventanaSeleccionarCarrera').dialog({
                    buttons:{
                        "Agregar" : function() {
                            var carrera_Seleccionada = $('input:radio[name=carrera]:checked').val();
                            if(!carrera_Seleccionada){
                                $('#ventanaAviso').html('Debe Seleccionar la Carrera deseada');
                                $('#ventanaAvisos').dialog('open');                                                            
                            }
                            else{
                                var mensaje = "Desea agregar la Carrera " + carrera_Seleccionada + " ?";
                                $('#ventanaConfirmacionAgregarCarrera').html(mensaje); 
                                $('#ventanaConfirmacionAgregarCarrera').dialog('open');
                            }
                        },
                        "Cancelar" : function() {
                            $(this).dialog('close');
                        }                       
                   },
                   title: 'Carreras',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                   width : '650',
                   height : 'auto',
                   position : {at: 'center top'},
                   dialogClass : 'no-close',
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
                
                f5($(document),true);
                Obtener_Carreras_Del_Alumno();
            });
            
                        
        </script>
        
<!--    </head>
    <body>
        <header>
            Mi Pefil
        </header>-->
        <div>
                <div>
                    <div class="encabezado_Formulario">
                        <div class="descripcion_Modulo">
                            <p>Mis Carreras</p>
                        </div>
                        <div class="barra_Herramientas">
                            <input type="button" id="Agregar_Carrera" name="Agregar_Carrera" value="Agregar" class="btn_Herramientas"/>
                        </div>
                    </div>

                    <div>
                        <div id="tabla_Mis_Carreras" class="tabla_Registros">
                        </div>
                        <input type="hidden" id="Id_Carrera" name="Id_Carrera" value="">
                        <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
                        <input type="hidden" id="Id_Division" name="Id_Division" value="<?php echo $_SESSION['id_division']; ?>">
                    </div>
            
                </div>
        </div><!--fin contenido_Form-->
        <div id='ventanaSeleccionarCarrera'>
            <div id="tabla_Seleccionar_Carrera" class="tabla_Registros">

            </div>   
            
        </div>
        <div id='ventanaConfirmacionAgregarCarrera'>                
        </div>
        <div id='ventanaConfirmacionBorrar'>                
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

<!DOCTYPE html>
<?php
    header('Content-Type: text/html; charset=UTF-8');
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
?>



<html>
    <head>
        <meta http-equiv="Expires" content="0" /> 
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        
        <title>Facultad de Ingeniería - División de Ingeniería Eléctrica</title>

        <script src="js/jquery-1.12.4.min.js"></script>
        <script src="js/jquery-ui.min.js"></script>
        <link href="css/jquery-ui.css" rel="stylesheet">                
        <link href="css/acceso.css" rel="stylesheet">                
        <script src="js/expresiones_reg.js"></script>
               
        <script type="text/javascript">
           if(history.forward(1)){
             location.replace( history.forward(1) );
           }
        </script>
        
        <script>           
            $(document).ready(function(){
                function validaDatos(){
                    $('#statusUsuario').hide();
                    $('#statusContrasena').hide();

                    $('#submit').prop('disable',true);
                    var datosValidos = true;
                    var user  = $('#usuario').val();
                    var contrasena = $('#contrasena').val();
//                    var miExpReg = /^[a-zA-ZÑñ0-9]{1,18}$/;
                    if (!user.match(miExpReg_Clave)){
                        $('#statusUsuario').show();
                        datosValidos = false;
                    }
                    else{
                        $('#statusUsuario').hide();
                    }
                    if (!contrasena.match(miExpReg_Contrasena)){
                        $('#statusContrasena').show();
                        datosValidos = false;
                    }
                    else{
                        $('#statusContrasena').hide();
                    }

                $('#submit').prop('disable',false);
                    
                    return datosValidos;
                    
                } //fin validaDatos

                $('#ventanaAvisos').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $(this).dialog('close');
                        }
                   },
                   title: 'Aviso',
                   modal : true,
                   autoOpen : false,
                   resizable : false,
                   draggable : true,
                   dialogClass : 'no-close ventanaMensajes',
                   closeOnEscape : false
                });                            

                $('#ventanaProcesando').dialog({
                   title: '',
                   modal : true,
                   autoOpen : false,
                   resizable : false,
                   draggable : false,
                   closeOnEscape : false,
                   dialogClass : 'no-close no-titlebar'                   
                });

                $('#login').submit(function(event){
                   event.preventDefault();
                   if (validaDatos()) {    
//                        $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
//                        $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                        $('#ventanaProcesando').dialog('open');
                       
                        var formData = $(this).serialize();                                                                      
			console.log(formData); 
                        $.ajax({
                            data : formData,
                            type : "POST",
                            dataType : "json",
                            url : "_Negocio/n_Usuario.php"
                        })
                                .done(function(respuesta,textStatus,jqXHR){
                                    $('#ventanaProcesando').dialog('close');
                                    if (respuesta.success == true){   
					                    console.log(respuesta);
                                    	if (respuesta.fecha_titulacion == false){
                                            window.open('home.php','_self');
                                        }else{
                                            console.log('hay fecha');
					                        $('#ventanaAviso').html('Tus trámites en la Coordinación de Titulación y Servicio Social han concluido, da seguimiento en Servicios Escolares. <a href="http://escolar.ingenieria.unam.mx/titulacion/">Ir a Servicios Escolares.</a>');
                                            $('#ventanaAvisos').dialog('open');
                                        }                                                                  
                                    }
                                    else{
                                        $('#ventanaAviso').html(respuesta.data.message);
                                        $('#ventanaAvisos').dialog('open');                            
                                    }                                    
                                })
                                        .fail(function(jqXHR,textStatus,errorThrown){
                                            console.log(jqXHR.responseText);
                                            console.log(textStatus);
                                            console.log(errorThrown);
                                            $('#ventanaProcesando').dialog('close');
                                            $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                            $('#ventanaAvisos').dialog('open');                            
                                        });                        
                    } else{
                        return false;
                    }
                }); //end submit
                $('#usuario').focus();
            });     //end ready
        </script>
    </head>
    <body>
        <div class="encabezado">
            <div id="divLogo">
                <img id="escudo" src="css/images/banner_DIE2.jpg" alt="Escudo"/>
            </div>
        </div>
        <div id="contenido">
            <div id="titModulo">
                <h1>Iniciar sesión</h1>
            </div>
            <div id="panelDerecho">
                <form name="login" id="login" method="" action="">            
                    <div>
                        <input type="text" name="usuario" id="usuario" placeholder="Usuario" 
                               title="Capture su Clave de Usuario" 
                               maxlength="18" autocomplete="off" />
                    </div>
                    <div>
                        <span id="statusUsuario" class='faltaInfo'>Dato Inválido</span>                        
                    </div>
                    <div>
                        <input type="password" name="contrasena" id="contrasena" placeholder="Contraseña" 
                               maxlength="15" title="Capture su Contraseña" autocomplete="off" />
                    </div>
                    <div>
                        <span id="statusContrasena" class='faltaInfo'>Dato Inválido</span> 
                    </div>
                    <div id="divSubmit">
                        <input type="submit" name="submit" id="submit" value="Iniciar sesión" class="btn">
                    </div>
                    <!-- <div class="opcionesDeCuenta" style="height: 30px;">
                       <span>¿No tienes una cuenta? <a href="nuevaCuenta.php">Crea una</a></span>
                        <span><a href="reenviarContrasena.php">Olvidé mi contraseña</a></span><br><br><br>                   
                    </div> -->
                    <div class="" style="margin-top: 0px; height: 40px;">
                        <span><b>CTYSS_<i>mis</i>Tramites (Ver. 1.04)</b></span><br>
                    <!--  <span><a href="reenviarContrasena.php">Olvidé mi contraseña</a></span><br><br><br> -->
                    </div>
                    
                    <input type="hidden" id="Id_Tipo_Usuario" name="Id_Tipo_Usuario" value="999">
                    <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="VALIDARNoCUENTA">   
                    <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="0">
                </form>
            </div>
        </div>
<!--        <div id="pie">
            <h2>Dirección....</h2>
        </div-->
        <div id="ventanaAvisos">
            <span id="ventanaAviso" name="ventanaAviso"></span>
        </div>
        <div id="ventanaProcesando">
            <img id="cargador" src="css/images/engrane2.gif"/><br>
            Procesando su transacción....!<br>
            Espere por favor.
        </div>
    </body>
</html>

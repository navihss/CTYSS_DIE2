<!DOCTYPE html>
<!--
Fecha:          Octubre,2024
Desarrollador:  Carlos Aguilar
Objetivo:       Interfaz para consultar datos de un alumno
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
                $('#btn_Buscar').on('click',function(e){
                    e.preventDefault();
                    Obtener_Datos_Alumno($('#id_numero_cuenta').val());
                });
   
                $('#id_numero_cuenta').on('keyup',function(e){
                    e.preventDefault();
                });
                  
                //OBTENEMOS LOS DATOS DEL ALUMNO
                function Obtener_Datos_Alumno(id_num_cuenta){
                    var datos = {Tipo_Movimiento : 'OBTENER_DATOS_ALUMNO',
                               id_numero_cuenta : id_num_cuenta
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_administrador_admon_datos_alumno.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           if (respuesta.success == true){
                               var info_alumno = respuesta.data.registros[0];
                               $('#usuario').val(info_alumno['id_alumno']);
                               $('#nombre').val(info_alumno['nombre']);
                               $('#carrera').val(info_alumno['descripcion_carrera']);
                               $('#correo').val(info_alumno['email_usuario']);
                               $('#tel_fijo').val(esNulo(info_alumno['telefono_fijo_alumno']));
                               $('#celular').val(esNulo(info_alumno['telefono_celular_alumno']));
                               $('#fecha_titulacion').val(esNulo(info_alumno['fecha_titulacion']));
                            }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                $('#ventanaAvisos').dialog('open');
                            });                                                                                                                                   
                }
                //FIN DE OBTENEMOS DATOS DEL ALUMNO

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
           
            });
                        
        </script>
        
        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                    <p>Consulta Datos de Alumno</p>
                </div>                        
            </div>
            <div class="barra_Parametros">
                    <label for="id_numero_cuenta" class="etiqueta_Parametro">Numero de Cuenta:</label>
                    <input type="text" id="id_numero_cuenta" name="id_numero_cuenta" value=""  maxlength="10" placeholder="" autocomplete="off"
                           class="entrada_Dato input_Parametro" title="Sólo puede Capturar los carácteres: 0-9 -">
                    <button id="btn_Buscar" class="btn_Herramientas">Buscar</button>
                    <label id="info_alumno" style="padding-left:20px;"></label>
            </div>
            <form name="Administrador_Datos_Alumno" id="Administrador_Datos_Alumno" method="" action="">            
                <div class="contenido_Formulario">
                    <div class="sombra_Formulario">
                        <p>
                            <label for="usuario" class="label">Usuario:</label>
                            <input type="text" name="usuario" id="usuario" readonly />
                        </p>
                        <p>
                            <label for="nombre" class="label">Nombre:</label>
                            <input type="text" name="nombre" id="nombre" readonly />
                        </p>
                        <p>
                            <label for="carrera" class="label">Carrera:</label>
                            <input type="text" name="carrera" id="carrera" readonly />
                        </p>
                        <p>
                            <label for="correo" class="label">Correo:</label>
                            <input type="text" name="correo" id="correo" readonly />
                        </p>
                        <p>
                            <label for="tel_fijo" class="label">Telefono Fijo:</label>
                            <input type="text" name="tel_hijo" id="tel_fijo" readonly />
                        </p>  
                        <p>
                            <label for="celular" class="label">Celular:</label>
                            <input type="text" name="celular" id="celular" readonly />
                        </p>
                        <p>
                            <label for="fecha_titulacion" class="label">Fecha de Titulacion:</label>
                            <input type="text" name="fecha_titulacion" id="fecha_titulacion" readonly />
                        </p>
                    </div> 
                </div>
           </form>          
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
            <input type="hidden" id="Id_Usuario_Correo" name="Id_Usuario_Correo" value="<?php echo $_SESSION['correo_usuario_sesion']; ?>">
        </div>
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

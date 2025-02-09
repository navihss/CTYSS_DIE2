<!DOCTYPE html>
<!--
Fecha:          Julio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para Cambiar la Contraseña del Usuario
-->
<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-cache");
header("Pragma: nocache");
session_start();
if (
    !isset($_SESSION["id_tipo_usuario"]) and
    !isset($_SESSION["id_usuario"])
) {
    header('Location: index.php');
}

?>

<!--<html>
    <head>-->
<!--        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/jquery-ui.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="menu/estilo_menu.css" /> 
        <script src="js/jquery-1.12.4.min.js"></script>
        <script src="js/jquery-ui.min.js"></script>-->
<script src="js/expresiones_reg.js"></script>

<script>
    $(document).ready(function() {

        //VALIDAMOS LOS DATOS DE LA FORM
        function validaDatos() {
            $('#btn_Guardar').prop('disable', true);
            var datosValidos = true;
            var clave = $('#clave').val();
            var contrasena = $('#contrasena').val();
            var contrasena2 = $('#contrasena2').val();

            $('#statusClave').hide();
            $('#statusContrasena').hide();
            $('#statusContrasena2').hide();

            //                    var miExpReg_Contrasena = /^[a-zA-Z0-9]{1,15}$/;
            //                    var miExpReg_Clave = /^[a-zA-Z0-9]{1,18}$/;


            if (!clave.match(miExpReg_Clave)) {
                $('#statusClave').show();
                datosValidos = false;
            } else {
                $('#statusClave').hide();
            }
            if (!contrasena.match(miExpReg_Contrasena)) {
                $('#statusContrasena').show();
                datosValidos = false;
            } else {
                $('#statusContrasena').hide();
            }
            if (!contrasena2.match(miExpReg_Contrasena)) {
                $('#statusContrasena2').show();
                datosValidos = false;
            } else {
                $('#statusContrasena2').hide();
            }

            if (contrasena != '' && contrasena2 != '') {
                if (contrasena != contrasena2) {
                    $('#ventanaAviso').html('La Contraseña y su Confirmación deben de ser iguales.');
                    $('#ventanaAvisos').dialog('open');
                    datosValidos = false;
                }
            }

            if (clave == '' || contrasena == '' || contrasena2 == '') {
                $('#ventanaAviso').html('Debe capturar TODA la información Requerida.');
                $('#ventanaAvisos').dialog('open');

                datosValidos = false;
            }

            $('#btn_Guardar').prop('disable', false);
            return datosValidos;
        };

        $('#clave').keypress(function() {
            $('.dato_Invalido').hide();
        });

        //              Validamos que la clave exista en la BD
        $('#clave').blur(function() {
            if ($('#clave').val() == "") {
                return;
            }
            $("#cargandoAjax").css("display", "inline");
            var datosAPasar = {
                Tipo_Movimiento: 'EXISTE_USUARIO',
                claveUsuario: $('#clave').val()
            };

            $.ajax({
                    data: datosAPasar,
                    type: "POST",
                    dataType: "json",
                    url: "_Negocio/n_administrador_Crear_Nueva_Cuenta.php"
                })
                .done(function(respuesta, textStatus, jqXHR) {
                    $("#cargandoAjax").css("display", "none");
                    if (respuesta.success == 'EXISTE') { // Ya Existe La Cuenta
                        $('#statusClave').hide();
                        $('#nombre').val(respuesta.data.registros[0]['nombre']);
                        $('#contrasena').focus();
                    } else if (respuesta.success == 'NOEXISTE') {
                        $('#statusClave').show();
                        $('#statusClave').text('Este Usuario NO existe en el Sistema!');
                        $('#nombre').val('');
                        $('#clave').focus();
                    } else {
                        $('#statusClave').show();
                        $('#statusClave').text('');
                        $('#ventanaAviso').html(respuesta.data.message);
                        $('#clave').val('');
                        $('#ventanaAvisos').dialog('open');
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    $("#cargandoAjax").css("display", "none");
                    $('#clave').prop('value', '');

                    $('#ventanaAviso').html('La solicitud ha fallado. ' + textStatus + '. ' + errorThrown);
                    $('#ventanaAvisos').dialog('open');
                });

        }); // ***** fin clave_blur


        $('#btn_Guardar').on('click', function(event) {
            event.preventDefault();
            if (validaDatos()) {
                $('#Tipo_Movimiento').val('CAMBIAR_CONTRASENA');
                $('#ventanaConfirmacion').dialog('open');
            } else {
                return false;
            }
        });

        $('#btn_Limpiar').on('click', function(event) {
            event.preventDefault();
            oculta_StatusControl();
            limpia_Controles();
        });

        function oculta_StatusControl() {
            $('#statusClave').hide();
            $('#statusContrasena').hide();
            $('#statusContrasena2').hide();
        }

        function limpia_Controles() {
            $('#clave').val('');
            $('#contrasena').val('');
            $('#contrasena2').val('');
            $('#nombre').val('');
            $('#clave').focus();
        }

        $('#ventanaConfirmacion').dialog({
            buttons: {
                "Aceptar": function() {
                    $(this).dialog('close');
                    //                            $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
                    //                            $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                    $('#ventanaProcesando').dialog('open');
                    // Por Ajax insertamos al Usuario               
                    var formDatos = $('#Administrador_Nva_Contrasena').serialize();

                    $.ajax({
                            data: formDatos,
                            type: "POST",
                            dataType: "json",
                            url: "_Negocio/n_administrador_Crear_Nueva_Cuenta.php"
                        })
                        .done(function(respuesta, textStatus, jqXHR) {
                            if (respuesta.success == true) {
                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html(respuesta.data.message);
                                $('#ventanaAvisos').dialog('open');
                                limpia_Controles();
                            } else {
                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html(respuesta.data.message);
                                $('#ventanaAvisos').dialog('open');
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            $('#ventanaProcesando').dialog('close');
                            $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                            $('#ventanaAvisos').dialog('open');
                        });
                },
                "Cancelar": function() {
                    $(this).dialog('close');
                    $(':text:first').focus();
                }
            },
            title: 'Cambio de Contraseña',
            modal: true,
            autoOpen: false,
            resizable: true,
            draggable: true,
            dialogClass: 'no-close ventanaConfirmaUsuario',
            closeOnEscape: false
        });

        $('#ventanaAvisos').dialog({
            buttons: {
                "Aceptar": function() {
                    $(this).dialog('close');
                }
            },
            title: 'Aviso',
            modal: true,
            autoOpen: false,
            resizable: true,
            draggable: true,
            dialogClass: 'no-close ventanaMensajes',
            closeOnEscape: false
        });

        $('#ventanaProcesando').dialog({
            title: 'Procesando...',
            modal: true,
            autoOpen: false,
            resizable: false,
            draggable: false,
            dialogClass: 'no-close no-titlebar',
            closeOnEscape: false
            //                   show : 'slideDown',
            //                   hide: 'slideUp',
            //                   dialogClass : 'ui-state-highlight'
        });

        function f5(that, val) {
            if (val) {
                that.on("keydown", function(e) {
                    var code = (e.keyCode ? e.keyCode : e.which);
                    if (code == 116 || code == 8) {
                        e.preventDefault();
                    }
                })
            } else {
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

        $(':text:first').focus();

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
            <p>Cambio de Contraseña</p>
        </div>
    </div>
    <form name="Administrador_Nva_Contrasena" id="Administrador_Nva_Contrasena" method="" action="">
        <div class="contenido_Formulario">
            <div class="sombra_Formulario">
                <p>
                    <label for="clave" class="label">Usuario:</label>
                    <input type="text" name="clave" id="clave" maxlength="18" placeholder="" class="entrada_Dato"
                        title="Capture únicamente letras y numeros, sin espacios" autocomplete="off" />
                <div id="cargandoAjax" class="notificacion">
                    <span><img src="css/images/ajax-loader03.gif" />Espere. Verificando si este Usuario Existe actualmente!</span>
                </div>
                <div class="notificacion">
                    <span id="statusClave" class="dato_Invalido"></span>
                </div>
                </p>
                <p>
                    <label for="nombre" class="label">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" readonly />
                </p>
                <p>
                    <label for="contrasena" class="label">Contraseña Nueva:</label>
                    <input type="password" name="contrasena" id="contrasena" maxlength="15" placeholder="" class="entrada_Dato"
                        title="Capture únicamente letras y números, sin espacios" autocomplete="off" />
                    <span id="statusContrasena" class="dato_Invalido"><img src="css/images/error.ico" /></span>
                </p>
                <p>
                    <label for="contrasena2" class="label">Confirme la Contraseña:</label>
                    <input type="password" name="contrasena2" id="contrasena2" maxlength="15" placeholder="" class="entrada_Dato"
                        title="Capture únicamente letras y números, sin espacios" autocomplete="off" />
                    <span id="statusContrasena2" class="dato_Invalido"><img src="css/images/error.ico" /></span>
                </p>

                <div style="padding-top: 20px;">
                    <input type="submit" name="btn_Guardar" id="btn_Guardar" value="Guardar" class="btn_Herramientas">
                    <input type="button" name="btn_Limpiar" id="btn_Limpiar" value="Limpiar" class="btn_Herramientas">
                </div>
            </div>
        </div>
        <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
        <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="">
    </form>
</div>

<div id='ventanaConfirmacion'>
    Desea hacer el Cambio de Contraseña ?
</div>

<div id="ventanaAvisos">
    <span id="ventanaAviso"></span>
</div>
<div id="ventanaProcesando" data-role="header">
    <img id="cargador" src="css/images/engrane2.gif" /><br>
    Procesando su transacción....!<br>
    Espere por favor.
</div>

<!--    </body>
</html>-->
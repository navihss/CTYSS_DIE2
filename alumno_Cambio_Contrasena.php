<!DOCTYPE html>
<!--
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para Cambiar la Contraseña del Alumno
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

            //                    var miExpReg_Contrasena = /^[a-zA0-9]{1,15}$/;
            //                    var miExpReg_Clave = /^[0-9]{1,18}$/;


            if (!clave.match(miExpReg_NoCta)) {
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
            $('#contrasena').val('');
            $('#contrasena2').val('');
            $('#contrasena').focus();
        }

        $('#ventanaConfirmacion').dialog({
            buttons: {
                "Aceptar": function() {
                    $(this).dialog('close');
                    //                            $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
                    //                            $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                    $('#ventanaProcesando').dialog('open');
                    // Por Ajax insertamos al Usuario               
                    var formDatos = $('#Alumno_Nva_Contrasena').serialize();

                    $.ajax({
                            data: formDatos,
                            type: "POST",
                            dataType: "json",
                            url: "_Negocio/n_alumno_Cambio_Contrasena.php"
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

    });
</script>

<!--    </head>
    <body>
        <header>
            Mi Pefil
        </header>-->
<div>
    <form name="Alumno_Nva_Contrasena" id="Alumno_Nva_Contrasena" method="" action="">
        <div class="encabezado_Formulario">
            <div class="descripcion_Modulo">
                <p>Cambio de Contraseña</p>
            </div>
        </div>
        <div class="contenido_Formulario">
            <div class="sombra_Formulario">
                <p>
                    <label for="clave" class="label">Usuario:</label>
                    <input type="text" name="clave" id="clave" maxlength="9" placeholder=""
                        value="<?php echo $_SESSION['id_usuario']; ?>" readonly autocomplete="off" />
                </p>
                <p>
                    <label for="nombre" class="label">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" value="<?php echo $_SESSION['nombre_usuario']; ?>"
                        readonly />
                </p>
                <p>
                    <label for="contrasena" class="label">Contraseña Nueva:</label>
                    <input type="password" name="contrasena" id="contrasena" maxlength="15" placeholder="" class="entrada_Dato"
                        title="Capture únicamente números, sin espacios" autocomplete="off" />
                    <span id="statusContrasena" class="dato_Invalido"><img src="css/images/error.ico" /></span>
                </p>
                <p>
                    <label for="contrasena2" class="label">Confirme la Contraseña:</label>
                    <input type="password" name="contrasena2" id="contrasena2" maxlength="15" placeholder="" class="entrada_Dato"
                        title="Capture únicamente números, sin espacios" autocomplete="off" />
                    <span id="statusContrasena2" class="dato_Invalido"><img src="css/images/error.ico" /></span>
                </p>

                <div style="margin-top: 10px;">
                    <input type="submit" name="btn_Guardar" id="btn_Guardar" value="Guardar" class="btn_Herramientas" style="width: 150px;">
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
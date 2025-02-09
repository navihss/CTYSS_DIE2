<!DOCTYPE html>

<?php
header('Content-Type: text/html; charset=UTF-8');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');
?>

<html>

<head>
    <meta http-equiv="Expires" content="0" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Facultad de Ingeniería - División de Ingeniería Eléctrica</title>

    <link href="./assets/libs/jquery-ui-1.11.4/jquery-ui.css" rel="stylesheet">
    <link href="./assets/css/acceso.css" rel="stylesheet">
</head>

<body>
    <div class="encabezado">
        <div id="divLogo">
            <img id="escudo" src="./assets/images/banners/banner_DIE2.jpg" alt="Escudo" />
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
                <!--<div class="opcionesDeCuenta" style="height: 30px;">
                        <span>¿No tienes una cuenta? <a href="nuevaCuenta.php">Crea una</a></span> 
                        <span><a href="reenviarContrasena.php">Olvidé mi contraseña</a></span><br><br><br>                   
                    </div>
                -->
                <div class="" style="margin-top: 0px; height: 30px;">
                    <span><b>CTYSS_<i>mis</i>Tramites (Ver. 1.04)</b></span>
                    <li><a href="Docs/Manual_de_sistema_Servicio_Social.pdf">Manual de Sistema</a> </li>
                    <!--<span><a href="reenviarContrasena.php">Olvidé mi contraseña</a></span><br><br><br>                   -->
                </div>

                <input type="hidden" id="Id_Tipo_Usuario" name="Id_Tipo_Usuario" value="999">
                <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="VALIDARNoCUENTA">
                <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="0">
            </form>
        </div>
    </div>
    <div id="pie">
        <table>
            <td>
                <h2><strong> Apreciable alumno:</strong></h2>
                <br>

                <li>Acceda a su sesión (El usuario y contraseña es su número de cuenta).</li>
                <li>Por seguridad es importante que cambie su contraseña una vez que ingrese al sistema.</li>
                <li>Actualice su información, debe completar los datos de las 3 pestañas en su perfil.</li>
                <li>Es importante actualizar su correo electrónico ya que por ese medio le llegarán notificaciones sobre sus trámites.</li>
                <li>Para captura de clave DGOAE en inicio de Servicio Social utilizar el siguiente formato 0000-000/000-0000.<br>
                    Ejemplo: 2023-12/81-71 formato de registro -> 2023-012/081-0071</li>
                <br>
                <li>En caso de no encontrarse registrado, favor de enviar correo a ctss@ingenieria.unam.edu con su historial académico.</li>
                <li>Si enviaste tus reportes, no se visualizan en sistema y tienes habilitada la opción de subirlo, puedes hacerlo.</li>
                <li>Para reportar problemas al subir reportes, envío de términos o inicios, favor de contactar a ctss@ingenieria.unam.edu y le responderán en un lapso de 3 días hábiles, no es necesario acudir a la ventanilla.</li>
            </td>
        </table>
        <br>
        COPYRIGHT © 2023. CTYSS.
    </div>

    <div id="ventanaAvisos">
        <span id="ventanaAviso" name="ventanaAviso"></span>
    </div>
    <div id="ventanaProcesando">
        <img id="cargador" src="./assets/images/ui/engrane2.gif" /><br>
        Procesando su transacción....!<br>
        Espere por favor.
    </div>

    <script src="./assets/libs/jquery-1.12-4/jquery-1.12.4.min.js"></script>
    <script src="./assets/libs/jquery-ui-1.11.4/jquery-ui.min.js"></script>

    <script src="./assets/js/expresiones_reg.js"></script>

    <script type="text/javascript">
        if (history.forward(1)) {
            location.replace(history.forward(1));
        }
    </script>

    <script>
        $(document).ready(function() {
            function validaDatos() {
                $('#statusUsuario').hide();
                $('#statusContrasena').hide();

                $('#submit').prop('disable', true);
                var datosValidos = true;
                var user = $('#usuario').val();
                var contrasena = $('#contrasena').val();
                //                    var miExpReg = /^[a-zA-ZÑñ0-9]{1,18}$/;
                if (!user.match(miExpReg_Clave)) {
                    $('#statusUsuario').show();
                    datosValidos = false;
                } else {
                    $('#statusUsuario').hide();
                }
                if (!contrasena.match(miExpReg_Contrasena)) {
                    $('#statusContrasena').show();
                    datosValidos = false;
                } else {
                    $('#statusContrasena').hide();
                }

                $('#submit').prop('disable', false);

                return datosValidos;

            } //fin validaDatos

            $('#ventanaAvisos').dialog({
                buttons: {
                    "Aceptar": function() {
                        $(this).dialog('close');
                    }
                },
                title: 'Aviso',
                modal: true,
                autoOpen: false,
                resizable: false,
                draggable: true,
                dialogClass: 'no-close ventanaMensajes',
                closeOnEscape: false
            });

            $('#ventanaProcesando').dialog({
                title: '',
                modal: true,
                autoOpen: false,
                resizable: false,
                draggable: false,
                closeOnEscape: false,
                dialogClass: 'no-close no-titlebar'
            });

            $('#login').submit(function(event) {
                event.preventDefault();
                if (validaDatos()) {
                    //                        $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
                    //                        $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                    $('#ventanaProcesando').dialog('open');

                    var formData = $(this).serialize();
                    $.ajax({
                            data: formData,
                            type: "POST",
                            dataType: "json",
                            url: "_Negocio/n_Usuario.php"
                        })
                        .done(function(respuesta, textStatus, jqXHR) {
                            $('#ventanaProcesando').dialog('close');
                            if (respuesta.success == true) {

                                window.open('home.php', '_self');
                            } else {
                                $('#ventanaAviso').html(respuesta.data.message);
                                $('#ventanaAvisos').dialog('open');
                            }
                        })
                        .fail(function(jqXHR, textStatus, errorThrown) {
                            $('#ventanaProcesando').dialog('close');
                            $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                            $('#ventanaAvisos').dialog('open');
                        });
                } else {
                    return false;
                }
            }); //end submit
            $('#usuario').focus();
        }); //end ready
    </script>
</body>

</html>
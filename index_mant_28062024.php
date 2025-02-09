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
    <div class="opcionesDeCuenta" style="height: 30px;">
        <span>Toma en cuenta que a partir del 28 de junio de 2024 a las 13:00 hrs y hasta las 09:00 horas del 22 de julio de 2024
            inicia periodo de vacaciones administrativas para nuestra Facultad por lo que se suspenderá la atención presencial
            y/o vía sistema/correo electrónico de los servicios brindados por la Coordinación de Titulación y Servicio Social.
        </span>
        <br>
        <span>Para reportes con fecha de entrega en periodo vacacional, se pueden subir a partir del 8 de enero, durante la primera semana.</span><br><br><br>
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
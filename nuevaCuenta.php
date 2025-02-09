<!DOCTYPE html>

<?php
header('Content-Type: text/html; charset=UTF-8');
?>

<html>

<head>
    <!--
        <meta http-equiv="Expires" content="0" /> 
        <meta http-equiv="Pragma" content="no-cache" />
    -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>Nueva Cuenta de Usuario</title>

    <link href="./assets/libs/jquery-ui-1.11.4/jquery-ui.min.js" rel="stylesheet">
    <link href="./assets/css/nuevaCuenta.css" rel="stylesheet">
</head>

<body>
    <div class="encabezado">
        <div id="divLogo">
            <img id="escudo" src="./assets/images/banners/banner_DIE2.jpg" alt="Escudo" />
        </div>
    </div>

    <div id="contenido_Cuenta_Nueva">
        <div id="titModulo">
            <h1>Crear una Cuenta</h1>
        </div>
        <div id="formNuevaCuenta">
            <form name="nuevaCuenta" id="nuevaCuenta" method="" action="">
                <p>
                    <label for="nombre" class="label">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" maxlength="50"
                        title="Capture únicamente letras"
                        style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" />
                    <span id="statusNombre" class='faltaInfo'><img src="./assets/images/ui/error.ico" class="dato_Invalido" /></span>
                </p>
                <p>
                    <label for="apellidoPaterno" class="label">Apellido Paterno:</label>
                    <input type="text" name="apellidoPaterno" id="apellidoPaterno" maxlength="50"
                        title="Capture únicamente letras"
                        style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" />
                    <span id="statusApellidoPaterno" class="faltaInfo"><img src="./assets/images/ui/error.ico" class="dato_Invalido" /></span>
                </p>
                <p>
                    <label for="apellidoMaterno" class="label">Apellido Materno:</label>
                    <input type="text" name="apellidoMaterno" id="apellidoMaterno" maxlength="50"
                        title="Capture únicamente letras"
                        style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" />
                    <span id="statusApellidoMaterno" class="faltaInfo"><img src="./assets/images/ui/error.ico" class="dato_Invalido" /></span>
                </p>
                <p>
                    <label for="fechaNacimiento" class="label">Fecha de nacimiento:</label>
                    <input type="text" name="fechaNacimiento" id="fechaNacimiento" title="dd/mm/aaaa" readonly />
                    <span id="statusFechaNacimiento" class="faltaInfo"><img src="./assets/images/ui/error.ico" class="dato_Invalido" /></span>
                </p>
                <p>
                    <label for="genero" class="label">Genero:</label>
                    <select name="genero" id="genero">
                        <option value="2">Femenino</option>
                        <option value="1">Masculino</option>
                    </select>
                    <span id="statusGenero" class="faltaInfo"><img src="./assets/images/ui/error.ico" class="dato_Invalido" /></span>
                </p>
                <p>
                    <label for="carrera" class="label">Carrera:</label>
                    <select name="carrera" id="carrera">
                    </select>
                    <span id="statusCarrera" class="faltaInfo"><img src="./assets/images/ui/error.ico" class="dato_Invalido" /></span>
                </p>

                <p>
                    <label for="correo" class="label">Dirección de correo electrónico:</label>
                    <input type="email" name="correo" id="correo" maxlength="100" placeholder="miCorreo@dominio.com"
                        title="Capture su dirección de correo TAL Y COMO LA DIÓ DE ALTA CON SU PROVEEDOR" autocomplete="off" />
                    <span id="statusCorreo" class="faltaInfo"><img src="./assets/images/ui/error.ico" class="dato_Invalido" /></span>
                </p>
                <p>
                    <label for="clave" class="label">Usuario:</label>
                    <input type="text" name="clave" id="clave" maxlength="9" placeholder="086198516"
                        title="Capture su Número de Cuenta de Alumno sin guiones" autocomplete="off" />
                <div id="cargandoAjax" class="faltaInfo">
                    <span><img src="./assets/images/ui/ajax-loader03.gif" />Espere. Verificando si este Usuario está Disponible!</span>
                </div>
                <div class="notificacion">
                    <span id="statusClave" class="faltaInfo"></span>
                </div>
                </p>
                <p>
                    <label for="contrasena" class="label">Contraseña:</label>
                    <input type="password" name="contrasena" id="contrasena" maxlength="15" placeholder=""
                        title="Capture únicamente letras y números, sin espacios" autocomplete="off" />
                    <span id="statusContrasena" class="faltaInfo"><img src="./assets/images/ui/error.ico" class="dato_Invalido" /></span>
                </p>
                <p>
                    <label for="contrasena2" class="label">Vuelve a escribir la Contraseña:</label>
                    <input type="password" name="contrasena2" id="contrasena2" maxlength="15" placeholder=""
                        title="Capture únicamente letras y números, sin espacios" autocomplete="off" />
                    <span id="statusContrasena2" class="faltaInfo"><img src="./assets/images/ui/error.ico" class="dato_Invalido" /></span>
                </p>
                <p>
                    <label for="aviso_privacidad" class="label"></label>
                    <input type="checkbox" name="aviso_privacidad" id="aviso_privacidad" /><a id="enlace_privacidad" href="" style="padding-left: 10px;">Aviso de Privacidad y Términos de Uso</a>
                </p>

                <div id="barraHerramienta">
                    <div>
                        <input type="submit" name="enviar" id="enviar" value="Crear cuenta" class="btn">
                        <input type="reset" name="limpiar" id="limpiar" value="Limpiar" class="btn">
                        <input type="button" name="regresar" id="regresar" value="Regresar" class="btn">
                    </div>
                </div>
                <input type="hidden" id="Id_Tipo_Usuario" name="Id_Tipo_Usuario" value="5">
                <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="AGREGAR">
                <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="0">
                <input type="hidden" id="De_Alta_OK" name="De_Alta_OK" value="0">
            </form>
        </div>
    </div>
    <div id="ventanaAvisoPrivacidad">
        <?php
        $file = fopen($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/Config/aviso_privacidad_terminosDeUso.txt', "r") or exit("Error al leer el archivo de Aviso de Privacitad y Términos de Uso.!");
        while (!feof($file)) {
            echo fgets($file) . "<br />";
        }
        fclose($file);
        ?>
    </div>
    <div id='ventanaConfirmacion'>
        Desea crear la Cuenta ?
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
            //LLENAMOS LOS CATALOGOS
            function llena_Catalogo(nom_control, tipo_movimiento, tabla_catalogo, tabla_campos,
                tabla_where, tabla_orderby) {
                var datos = {
                    Tipo_Movimiento: tipo_movimiento,
                    tabla_Catalogo: tabla_catalogo,
                    tabla_Campos: tabla_campos,
                    tabla_Where: tabla_where,
                    tabla_OrderBy: tabla_orderby
                };
                $.ajax({
                        data: datos,
                        type: 'POST',
                        dataType: 'json',
                        url: '_Negocio/n_Catalogos_Generales.php'
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_options = '';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                //recorremos los valores de cada usuario
                                html_options = html_options + '<option value=' + value['id'] +
                                    '>' + value['descripcion'] + '</option>';
                            });
                            $('#' + nom_control).empty();
                            $('#' + nom_control).html(html_options);

                            $('#' + nom_control + ' option:first-child').attr('selected', 'selected');
                        } else {
                            $('#ventanaAviso').html(respuesta.data.message);
                            $('#ventanaAvisos').dialog('open');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisos').dialog('open');
                    });
            }
            //FIN LLENADO DE CATALOGO

            function validaDatos() {
                $('#enviar').prop('disable', true);
                var datosValidos = true;
                var nombre = $('#nombre').val();
                var apellidoPaterno = $('#apellidoPaterno').val();
                var apellidoMaterno = $('#apellidoMaterno').val();
                var fechaNacimiento = $('#fechaNacimiento').val();
                var correo = $('#correo').val();
                var clave = $('#clave').val();
                var contrasena = $('#contrasena').val();
                var contrasena2 = $('#contrasena2').val();

                $('#statusNombre').hide();
                $('#statusApellidoPaterno').hide();
                $('#statusApellidoMaterno').hide();
                $('#statusFechaNacimiento').hide();
                $('#statusCorreo').hide();
                $('#statusClave').hide();
                $('#statusContrasena').hide();
                $('#statusContrasena2').hide();

                //                    var miExpReg = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ]{1,50}$/;
                //                    var miExpReg_Contrasena = /^[a-zA-Z0-9]{1,15}$/;
                //                    var miExpReg_Fecha = /^([0-9]{2}\/[0-9]{2}\/[0-9]{4})$/;
                //                    var miExpReg_Mail = /^[a-zA-Z0-9\._-]+@[a-zA-Z0-9-]{2,}[.][a-zA-Z]{2,4}$/;
                //                    var miExpReg_Mail = /^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(.[a-zA-Z0-9-]+)*(.[a-zA-Z]{2,4})$/;
                //                    var miExpReg_NoCta = /^[0-9]{9}$/;

                if (!nombre.match(miExpReg_Nombre)) {
                    $('#statusNombre').show();
                    datosValidos = false;
                } else {
                    $('#statusNombre').hide();
                }
                if (!apellidoPaterno.match(miExpReg_Nombre)) {
                    $('#statusApellidoPaterno').show();
                    datosValidos = false;
                } else {
                    $('#statusApellidoPaterno').hide();
                }
                if (!apellidoMaterno.match(miExpReg_Nombre)) {
                    $('#statusApellidoMaterno').show();
                    datosValidos = false;
                } else {
                    $('#statusApellidoMaterno').hide();
                }

                if (!fechaNacimiento.match(miExpReg_Fecha)) {
                    $('#statusFechaNacimiento').show();
                    datosValidos = false;
                } else {
                    $('#statusFechaNacimiento').hide();
                }

                if (!correo.match(miExpReg_Mail)) {
                    $('#statusCorreo').show();
                    datosValidos = false;
                } else {
                    $('#statusCorreo').hide();
                }

                if (!clave.match(miExpReg_NoCta)) {
                    $('#statusClave').html('No. Cta. Inválido');
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

                if (nombre == '' || apellidoPaterno == '' || apellidoMaterno == '' || fechaNacimiento == '' || correo == '' ||
                    clave == '' || contrasena == '' || contrasena2 == '') {
                    $('#ventanaAviso').html('Debe capturar TODA la información Requerida.');
                    $('#ventanaAvisos').dialog('open');

                    return false;
                }

                if (!$('#aviso_privacidad').is(':checked')) {
                    $('#ventanaAviso').html('Debe Confirmar el Aviso de Privacidad y Términos de Uso.');
                    $('#ventanaAvisos').dialog('open');

                    datosValidos = false;
                }

                $('#enviar').prop('disable', false);

                return datosValidos;
            } // ***** fin validaDatos

            $(':text:first').focus();
            //                $('.select').selectmenu({
            //                    width : 200
            //                });

            //Array para dar formato en español 
            $.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: 'Previo',
                nextText: 'Próximo',
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ],
                monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                    'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
                ],
                monthStatus: 'Ver otro mes',
                yearStatus: 'Ver otro año',
                dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sáb'],
                dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                dateFormat: 'dd/mm/yy',
                firstDay: 0,
                initStatus: 'Selecciona la fecha',
                isRTL: false
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);

            $('#fechaNacimiento').datepicker({
                changeYear: true,
                changeMonth: true,
                yearRange: '1920:2050',
                onSelect: function(date) {
                    $("#fechaNacimiento ~ .ui-datepicker").hide();
                }

            });

            $('#limpiar').click(function() {
                $('#statusNombre').hide();
                $('#statusApellidoPaterno').hide();
                $('#statusApellidoMaterno').hide();
                $('#statusFechaNacimiento').hide();
                $('#statusCorreo').hide();
                $('#statusClave').hide();
                $('#statusContrasena').hide();
                $('#statusContrasena2').hide();
                $(':text:first').focus();
            });


            $('#ventanaProcesando').dialog({
                title: '',
                modal: true,
                autoOpen: false,
                resizable: false,
                draggable: false,
                closeOnEscape: false,
                dialogClass: 'no-close no-titlebar'
                //                   show : 'slideDown',
                //                   hide: 'slideUp',
                //                   dialogClass : 'ui-state-highlight'
            });


            //              Validamos que la clave exista en la UNAM y que no exista en la BD
            $('#clave').blur(function() {
                if ($('#clave').val() == "") {
                    return;
                }
                var miExpReg_NoCta = /^[0-9]{1,10}$/;
                if (!$('#clave').val().match(miExpReg_NoCta)) {
                    $('#statusClave').html('No. Cta. Inválido');
                    $('#statusClave').show();
                    return false;
                } else {
                    $('#statusClave').hide();
                }

                $("#cargandoAjax").css("display", "inline");
                var datosAPasar = {
                    claveUsuario: $('#clave').val(),
                    id_carrera: $('#carrera').val()
                };
                //$.post('validaClaveUsuarioProceso.php',datosAPasar, procesaResultado,'json');
                //return false;  //detenemos el comportamiento normal y evitamos descargue la página actual y cargue el link de la página de proceso.

                $.ajax({
                        data: datosAPasar,
                        type: "POST",
                        dataType: "json",
                        url: "validaClaveUsuarioProceso.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        $('#De_Alta_OK').val("0");
                        $("#cargandoAjax").css("display", "none");
                        if (respuesta.success == 'EXISTE') { // Ya Existe La Cuenta
                            $('#statusClave').show();
                            $('#statusClave').text('Este Usuario ya existe en el Sistema!');
                            $('#clave').prop('value', '');
                            $('#enviar').prop('disabled', true);
                        } else if (respuesta.success == 'NOEXISTE') {
                            $('#statusClave').show();
                            $('#statusClave').text('Disponible');
                            $('#enviar').prop('disabled', false);
                        } else {
                            $('#enviar').prop('disabled', true);
                            $('#statusClave').show();
                            $('#statusClave').text('');
                            $('#ventanaAviso').html(respuesta.data.message);
                            $('#clave').val('');
                            $('#enviar').prop('disabled', false);
                            $('#ventanaAvisos').dialog('open');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $("#cargandoAjax").css("display", "none");
                        $('#clave').prop('value', '');
                        $('#enviar').prop('disabled', true);

                        $('#ventanaAviso').html('La solicitud ha fallado. ' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisos').dialog('open');
                    });

            }); // ***** fin clave_blur


            $('#ventanaAvisos').dialog({
                buttons: {
                    "Aceptar": function() {
                        $(this).dialog('close');
                        abrir_Principal = $('#De_Alta_OK').val();
                        abrir_Home(abrir_Principal);
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

            $('#aviso_privacidad').on("click", function(e) {
                e.preventDefault();
                return false;
            });

            $('#enlace_privacidad').on("click", function(e) {
                e.preventDefault();
                $('#ventanaAvisoPrivacidad').dialog('open');
            });

            $('#ventanaAvisoPrivacidad').dialog({
                buttons: {
                    "Aceptar": function() {
                        $('#aviso_privacidad').prop("checked", "checked");
                        $(this).dialog('close');
                    },
                    "Declinar": function() {
                        $('#aviso_privacidad').prop("checked", "");
                        $(this).dialog('close');
                    }
                },
                title: 'Aviso de Privacidad y Términos de Uso',
                modal: true,
                autoOpen: false,
                resizable: false,
                width: "650",
                height: "400",
                draggable: true,
                dialogClass: 'no-close ventanaMensajes',
                closeOnEscape: false
            });

            function abrir_Home(abrir) {
                if (abrir == 1) {
                    window.open("home.php", "_self");
                }
            }

            $('#regresar').on("click", function(e) {
                e.preventDefault();
                window.open("index.php", "_self");
            })

            $('#ventanaConfirmacion').dialog({
                buttons: {
                    "Aceptar": function() {

                        $(this).dialog('close');
                        //                            $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
                        //                            $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                        $('#ventanaProcesando').dialog('open');

                        // Por Ajax insertamos la Nueva Cuenta
                        var formDatos = $('#nuevaCuenta').serialize();
                        //*********************************************
                        $.ajax({
                                data: formDatos,
                                type: "POST",
                                dataType: "json",
                                url: "_Negocio/n_Usuario.php"
                            })
                            .done(function(respuesta, textStatus, jqXHR) {
                                $('#ventanaProcesando').dialog('close');

                                if (respuesta.success == true) {
                                    $('#nuevaCuenta').each(function() {
                                        this.reset();
                                    });
                                    $('#De_Alta_OK').val("1");
                                } else {
                                    $('#De_Alta_OK').val("0");
                                }

                                $('#ventanaAviso').html(respuesta.data.message);
                                $('#ventanaAvisos').dialog('open');
                            })
                            .fail(function(jqXHR, textStatus, errorThrown) {
                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                $('#ventanaAvisos').dialog('open');
                            });

                        //*********************************************

                        //                            $.post('_Negocio/n_Usuario.php',formDatos, procesaResultadoCrearCuenta);
                        //                                                        
                        //                            $(this).dialog('close');
                        //                            $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
                        //                            $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                        //                            $('#ventanaProcesando').dialog('open');
                        //*********************************************
                    },
                    "Cancelar": function() {
                        $(this).dialog('close');
                        $(':text:first').focus();
                    }
                },
                title: 'Nueva Cuenta?',
                modal: true,
                autoOpen: false,
                resizable: false,
                draggable: true,
                closeOnEscape: false
                //                   show : 'slideDown',
                //                   hide: 'slideUp',
                //                   dialogClass : 'ui-state-highlight'
            });

            $('#enviar').click(function(event) {
                event.preventDefault();
                if (validaDatos()) {
                    $('#ventanaConfirmacion').dialog({
                        dialogClass: 'no-close ventanaConfirmaUsuario'
                    });
                    $('#ventanaConfirmacion').dialog('open');
                    //                     $('#nuevaCuenta').submit();
                } else {
                    return false;
                }
            }); //***** fin_enviar

            llena_Catalogo('carrera', 'CATALOGO_GENERALES', 'carreras',
                'id_carrera as id, descripcion_carrera as descripcion',
                '', 'descripcion_carrera');

        }); //end ready
    </script>
</body>

</html>
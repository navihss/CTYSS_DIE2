<!DOCTYPE html>
<!--
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para la captura del Jurado
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

<html>

<head>
    <!--        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="./assets/libs/jquery-ui-1.11.4/jquery-ui.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="menu/estilo_menu.css" /> 
        <script src="./assets/libs/jquery-1.12-4/jquery-1.12.4.min.js"></script>
        <script src="./assets/libs/jquery-ui-1.11.4/jquery-ui.min.js"></script>-->
    <script src="./assets/js/expresiones_reg.js"></script>

    <script>
        $(document).ready(function() {
            //OBTENEMOS LAS CARRERAS EN LAS QUE ESTA INSCRITO EL ALUMNO PARA EL LISTBOX
            function Obtener_Carreras_Del_Alumno() {
                var datos = {
                    Tipo_Movimiento: 'OBTENER',
                    Id_Usuario: $('#Id_Usuario').val(),
                    Id_Carrera: 0
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_Alumno_Carrera.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_options = '';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                //recorremos los valores de cada usuario
                                html_options = html_options + '<option value=' + value['id_carrera'] +
                                    '>' + value['descripcion_carrera'] + '</option>';
                            });
                            $('#selec_Mis_Carreras').empty();
                            $('#selec_Mis_Carreras').html(html_options);

                            $('#selec_Mis_Carreras option:first-child').attr('selected', 'selected');
                            $('#Id_Carrera').val($('#selec_Mis_Carreras').val());
                            Obtener_Mi_Propuesta($('#Id_Usuario').val(), $('#Id_Carrera').val());
                        } else {
                            //                                $('#Agregar_Jurado').prop('disable',true);
                            $('#ventanaAviso').html(respuesta.data.message);
                            $('#ventanaAvisos').dialog('open');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisos').dialog('open');
                    });

            } //FIN OBTENEMOS LAS CARRERAS EN LAS QUE ESTA INSCRITO EL ALUMNO

            $('#selec_Mis_Carreras').change(function(e) {
                e.preventDefault();
                var id_carrera_sel = $(this).val();
                $('#Id_Carrera').val(id_carrera_sel);
                Obtener_Mi_Propuesta($('#Id_Usuario').val(), id_carrera_sel);
            });

            //OBTENEMOS EL JURADO DEL ALUMNO
            function Obtener_Mi_Propuesta(id_alumno, id_carrera) {
                //Obtenemos los Servicios del Alumno
                var datos = {
                    Tipo_Movimiento: 'OBTENER_MI_JURADO',
                    id_usuario: id_alumno,
                    id_carrera: id_carrera
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_Alumno_Mi_Jurado.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:100%;">';
                        html_table = html_table + '<TR><TH>Propuesta</TH>\n\
                                                      <TH>Jurado-Fecha Alta</TH>\n\
                                                      <TH>Jurado-Registrado Por</TH>\n\
                                                      <TH>Jurado-Estatus</TH>\n\
                                                      <TH>Acción</TH></TR>';
                        if (respuesta.success == true) {
                            var $btn_Editar = "";
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                $btn_Editar = '<button class="btn_Editar btnOpcion" data-id_propuesta=\'' + value['id_propuesta'] + '\' ' +
                                    ' data-id_version=' + value['version'] +
                                    ' data-titulo_propuesta=\'' + value['titulo_propuesta'] + '\' ' +
                                    ' data-id_estatus= ' + value['id_estatus'] + '>Editar Jurado</button>';
                                html_table = html_table + '<TR>';
                                html_table = html_table + '<TD>' + value['id_propuesta'] + '</TD>';
                                html_table = html_table + '<TD style="text-align:left;">' + esNulo(value['fecha_propuesto']) + '</TD>';
                                html_table = html_table + '<TD style="text-align:left;">' + esNulo(value['nombre']) + '</TD>';
                                html_table = html_table + '<TD>' + value['descripcion_estatus'] + '</TD>';
                                html_table = html_table + '<TD>' + $btn_Editar + '</TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Mi_Jurado').empty();
                            $('#tabla_Mi_Jurado').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD style="text-align:center;" colspan="5">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Mi_Jurado').empty();
                            $('#tabla_Mi_Jurado').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table = html_table + '<TR><TH>Propuesta</TH>\n\
                                                           <TH>Jurado-Fecha Alta</TH>\n\
                                                           <TH>Jurado-Registrado Por</TH>\n\
                                                           <TH>Jurado-Estatus</TH>\n\
                                                           <TH>Acción</TH></TR>';
                        html_table = html_table + '<TR><TD style="text-align:center;" colspan="5">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';

                        $('#tabla_Mi_Jurado').empty();
                        $('#tabla_Mi_Jurado').html(html_table);
                    });
            } //fin Obtenemos Jurado del Alumno  

            $('#tabla_Mi_Jurado').on("click", "button.btn_Editar", function(e) {
                e.preventDefault();
                $('#Id_Propuesta').val($(this).data("id_propuesta"));
                $('#id_Estatus').val($(this).data("id_estatus"));
                $('#id_Version').val($(this).data("id_version"));
                $('#titulo_propuesta').val($(this).data("titulo_propuesta"));
                $('#Tipo_Movimiento').val('ACTUALIZAR');
                //Obtener_Jurado($(this).data("id_propuesta"), $(this).data("id_version"));
                $('#ventanaJurado').dialog('open');
            });

            function cargarProfesoresDisponibles() {
                return $.ajax({
                    data: { Tipo_Movimiento: 'OBTENER_PROFESORES_DISPONIBLES' },
                    url: '_Negocio/n_Alumno_Mi_Jurado.php',
                    type: 'POST',
                    dataType: 'json'
                });
            }

            //OBTENEMOS LOS SINODALES
            function Obtener_Jurado(id_propuesta, id_version){
                var datos = {
                    Tipo_Movimiento: 'OBTENER_MIS_SINODALES',
                    id_propuesta: id_propuesta,
                    id_version: id_version
                };
                $.ajax({
                    data: datos,
                    type: "POST",
                    dataType: "json",
                    url: "_Negocio/n_Alumno_Mi_Jurado.php"
                })
                .done(function(respuesta){
                    var html_table = '<table class="tabla_Registros">';
                    html_table += '<tr><th>No.</th><th>Sinodal Propuesto</th><th>Sinodal Definitivo</th></tr>';

                    if(respuesta.success){
                        $.each(respuesta.data.registros, function(k, val){
                            html_table += '<tr>';
                            html_table += '<td>'+ val.num_profesor +'</td>';

                            // col 2: el select
                            html_table += '<td><select class="select_sinodal" data-num_prof="'+ val.num_profesor +'">';
                            html_table += '<option value="">--Seleccione Profesor--</option>';
                            var actual = val.id_usuario || '';
                            $.each(window.profesores, function(i, p){
                                var sel = (p.id_usuario == actual) ? 'selected' : '';
                                html_table += '<option value="'+ p.id_usuario +'" '+ sel +'>'+ p.nombre_completo +'</option>';
                            });
                            html_table += '</select></td>';

                            // col 3: sinodal_definitivo (por si acaso)
                            html_table += '<td>'+ (val.sinodal_definitivo || '') +'</td>';

                            html_table += '</tr>';
                        });
                    } else {
                        html_table += '<tr><td colspan="3">'+ respuesta.data.message +'</td></tr>';
                    }
                    html_table += '</table>';
                    $('#tabla_Sinodales').html(html_table);
                })
                .fail(function(a,b,c){
                    alert("Error al obtener sinodales: "+b+" - "+c);
                });
                } //fin Obtenemos los Sinodales

            //VALIDACIONES 
            function validaDatos() {

                //RECORREMOS LOS INPUT
                var datosValidos = true;
                var lista_VoBo = '';
                var refnom_textarea = '';

                $("#ventanaJurado input[type=text]").each(function(index) {
                    if ((!$(this).prop('value').match(miExpReg_Nombre_Sinodal))) {
                        datosValidos = false;
                    }
                });

                return datosValidos;

                //                    var datosValidos = true;
                //                    var sinodal1 = $('#Sinodal_1').val();
                //                    var sinodal2 = $('#Sinodal_2').val();
                //                    var sinodal3 = $('#Sinodal_3').val();
                //                    var sinodal4 = $('#Sinodal_4').val();
                //                    var sinodal5 = $('#Sinodal_5').val();
                //                    
                //                    $('#aviso_Sinodal1').hide();
                //                    $('#aviso_Sinodal2').hide();
                //                    $('#aviso_Sinodal3').hide();
                //                    $('#aviso_Sinodal4').hide();
                //                    $('#aviso_Sinodal5').hide();
                //                   
                //                    if (sinodal1 =='')
                //                    {
                //                        $('#aviso_Sinodal1').show();
                //                        datosValidos = false;
                //                    }
                //                    if (sinodal2 =='')
                //                    {
                //                        $('#aviso_Sinodal2').show();
                //                        datosValidos = false;
                //                    }
                //                    if (sinodal3 =='')
                //                    {
                //                        $('#aviso_Sinodal3').show();
                //                        datosValidos = false;
                //                    }
                //                    if (sinodal4 =='')
                //                    {
                //                        $('#aviso_Sinodal4').show();
                //                        datosValidos = false;
                //                    }
                //                    if (sinodal5 =='')
                //                    {
                //                        $('#aviso_Sinodal5').show();
                //                        datosValidos = false;
                //                    }
                //
                //                    if (sinodal1 =='' || sinodal2 =='' || sinodal3 =='' || sinodal4=='' 
                //                         || sinodal5 =='' )
                //                    {
                //                        $('#ventanaAviso').html('Debe capturar TODA la información Requerida.');
                //                        $('#ventanaAvisos').dialog('open');
                //                        
                //                        datosValidos = false;
                //                        return datosValidos;
                //                    }
                //                    
                //                    return datosValidos;
            };
            //FIN VALIDACIONES PARA GUARDAR

            $('#ventanaJurado').dialog({
                buttons: [{
                    id: "btn_Guardar",
                    text: "Guardar",
                    click: function() {
                        if (validaDatos()) {
                            $('#ventanaConfirmacion').dialog('open');
                        } else {
                            $('#ventanaAviso').html("Debe capturar todos los Sinodales Propuestos. Y solo se aceptan los carácteres A-Z . Ñ");
                            $('#ventanaAvisos').dialog('open');
                        }
                    }
                }, {
                    id: "btn_Cancelar",
                    text: "Cerrar",
                    click: function() {
                        $('#ventanaJurado input[type=text]').each(function() {
                            $(this).val('');
                        });
                        $('#ventanaJurado span').each(function() {
                            $(this).hide();
                        });

                        $(this).dialog('close');
                    }
                }],
                title: 'Jurado',
                modal: true,
                autoOpen: false,
                //                   resizable : true,
                draggable: true,
                height: 'auto',
                width: '750',
                dialogClass: 'no-close',
                show: 'slide',
                hide: 'slide',
                closeOnEscape: false,
                position: {
                    at: 'center top'
                },
                open: function() {
                    $('#ventanaProcesando').dialog('open');
                    cargarProfesoresDisponibles().done(function(resp){
                        if(resp.success){
                            window.profesores = resp.data.profesores;
                            // AHORA sí cargar sinodales
                            Obtener_Jurado($('#Id_Propuesta').val(), $('#id_Version').val());
                        } else {
                            alert("No se pudo cargar la lista de profesores");
                        }
                        $('#ventanaProcesando').dialog('close');
                    });
                },
                close: function() {
                    $('#ventanaJurado input[type=text]').each(function() {
                        $(this).val('');
                    });
                    $('#ventanaJurado span').each(function() {
                        $(this).hide();
                    });
                    $(this).dialog('close');
                }
            });

            $('#ventanaConfirmacion').dialog({
                buttons: {
                    "Aceptar": function() {
                        $(this).dialog('close');
                        //                            $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
                        //                            $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                        $('#ventanaProcesando').dialog('open');

                        // Por Ajax Actualizamos a los Sinodales
                        var registros = '';
                        $('.select_sinodal').each(function(){
                            var num = $(this).data('num_prof');
                            var idu = $(this).val() || '0';
                            var label = $(this).find('option:selected').text();
                            if(idu == '0'){
                                registros += num + ':Sin asignar|';
                            } else {
                                registros += num + ':' + idu + ':' + label + '|';
                            }
                        });
                        if(registros.endsWith('|')){
                            registros = registros.slice(0, -1);
                        }
                        $('#id_sinodales').val(registros);

                        var formDatos = $('#frm_MJ').serialize();
                        console.log(formDatos);
                        $.ajax({
                                data: formDatos,
                                type: "POST",
                                dataType: "json",
                                url: "_Negocio/n_Alumno_Mi_Jurado.php"
                            })
                            .done(function(respuesta, textStatus, jqXHR) {
                                $('#ventanaProcesando').dialog('close');
                                if (respuesta.success == true) {
                                    //$("#btn_Guardar").button("option", "disabled", true);
                                    Obtener_Mi_Propuesta($('#Id_Usuario').val(), $('#Id_Carrera').val());

                                } else {
                                    $("#btn_Guardar").button("option", "disabled", false);
                                }
                                $('#ventanaAviso').html(respuesta.data.message);
                                $('#ventanaAvisos').dialog('open');
                            })
                            .fail(function(jqXHR, textStatus, errorThrown) {
                                console.log(jqXHR);
                                console.log(textStatus);
                                console.log(errorThrown);
                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                $('#ventanaAvisos').dialog('open');
                            });
                    },
                    "Cancelar": function() {
                        $(this).dialog('close');
                    }
                },
                title: 'Jurado',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: true,
                dialogClass: 'no-close ventanaConfirmaUsuario',
                closeOnEscape: false
            });

            //                $('#Agregar_Servicio').click(function(e){
            //                    e.preventDefault();
            //                    $('#Tipo_Movimiento').val('AGREGAR');
            //                    habilitaControles();
            //                    $('#id_Estatus_ss').val(0);
            //                    $('#ventanaServicioSocial').dialog('open');
            //                });

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
                title: '',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: false,
                dialogClass: 'no-close no-titlebar',
                closeOnEscape: false
            });

            function esNulo(valor_) {
                if (valor_ == null) {
                    return '';
                } else {
                    return valor_;
                }
            }

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

            $('#tabla_Sinodales').on("focus", "input:text, textarea", function(e) {
                e.preventDefault();
                f5($(document), false);
            });
            $('#tabla_Sinodales').on("blur", "input:text, textarea", function(e) {
                e.preventDefault();
                f5($(document), true);
            });

            f5($(document), true);
            Obtener_Carreras_Del_Alumno();

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
                <p>Mi Jurado</p>
            </div>
            <div class="barra_Parametros">
                <label for="selec_Mis_Carreras" class="etiqueta_Parametro">Mi Carrera:</label>
                <select id="selec_Mis_Carreras" name="selec_Mis_Carreras" class="combo_Parametro">
                </select>
            </div>
            <div id="tabla_Mi_Jurado" class="tabla_Registros">
            </div>
        </div>
    </div>
    <div id='ventanaJurado' name="ventanaJurado">
        <form id="frm_MJ" name="frm_MJ" method="" action="" autocomplete="off">
            <div id="tabla_Sinodales" class="tabla_Registros">
            </div>
            <input type="hidden" id="Id_Propuesta" name="Id_Propuesta" value="">
            <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="">
            <input type="hidden" id="Id_Carrera" name="Id_Carrera" value="">
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
            <input type="hidden" id="id_Estatus" name="id_Estatus" value="0">
            <input type="hidden" id="id_Version" name="id_Version" value="0">
            <input type="hidden" id="id_sinodales" name="id_sinodales" value="0">
            <input type="hidden" id="titulo_propuesta" name="titulo_propuesta" value="0">
        </form>
    </div>

    <div id='ventanaConfirmacion'>
        Desea Solicitar el Jurado Propuesto ?
    </div>
    <div id="ventanaAvisos">
        <span id="ventanaAviso"></span>
    </div>

    <div id="ventanaProcesando" data-role="header">
        <img id="cargador" src="./assets/images/ui/engrane2.gif" /><br>
        Procesando su transacción....!<br>
        Espere por favor.
    </div>
    <!--    </body>
</html>-->
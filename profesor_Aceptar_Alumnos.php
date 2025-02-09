<!DOCTYPE html>
<!--
Fecha:          Julio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para Aceptar a los Alumnos en una Propuesta
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

            function Obtener_Inscripciones_Pendientes(id_profesor) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_INSCRIPCIONES_POR_AUTORIZAR',
                    id_profesor: id_profesor,
                    id_estatus: 10 //10. Por Autorizar Prof
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_profesor_Aceptar_Alumnos.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:100%;">';
                        html_table += '<TR><TH>Inscripción</TH>\n\
                                        <TH>Alumno</TH>\n\
                                        <TH>Nombre</TH>\n\
                                        <TH>Fecha Inscrito</TH>\n\
                                        <TH>Título</TH>\n\
                                        <TH>Tipo</TH>\n\
                                        <TH>Tramita Baja</TH>\n\
                                        <TH>Acción</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                var $btn_Revisar = '';
                                var $tramita_Baja = '';
                                if (value['id_documento'] == 5) {
                                    $tramita_Baja = 'SI';
                                }
                                $btn_Revisar = '<button class="btn_Revisar btnOpcion" data-id_inscripcion=\'' + value['id_inscripcion'] + '\' ' +
                                    ' data-id_alumno = \'' + value['id_alumno'] + '\' ' +
                                    ' data-id_profesor = \'' + value['id_profesor'] + '\' ' +
                                    ' data-id_propuesta = \'' + value['id_propuesta'] + '\' ' +
                                    ' data-titulo_propuesta = \'' + value['titulo_propuesta'] + '\' ' +
                                    ' data-correo_alumno = \'' + value['email_usuario'] + '\' ' +
                                    ' data-id_carrera = \'' + value['id_carrera'] + '\' ' +
                                    ' data-id_estatus =' + value['id_estatus'] +
                                    ' data-id_version =' + value['numero_version'] +
                                    ' data-descripcion_corta_archivo = \'' + value['descripcion_para_nom_archivo'] + '\' ' +
                                    ' data-id_documento =' + value['id_documento'] + '>Revisar Doc</button>';

                                html_table += '<TR>';
                                html_table += '<TD>' + value['id_inscripcion'] + '</TD>';
                                html_table += '<TD>' + value['id_alumno'] + '</TD>';
                                html_table += '<TD>' + value['nom_alumno'] + '</TD>';
                                html_table += '<TD>' + value['fecha_inscripcion'] + '</TD>';
                                html_table += '<TD>' + value['titulo_propuesta'] + '</TD>';
                                html_table += '<TD>' + value['descripcion_tipo_propuesta'] + '</TD>';
                                html_table += '<TD>' + $tramita_Baja + '</TD>';
                                html_table += '<TD>' + $btn_Revisar + '</TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Por_Autorizar').empty();
                            $('#tabla_Por_Autorizar').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD colspan="8">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Por_Autorizar').empty();
                            $('#tabla_Por_Autorizar').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table += '<TR><TH>Inscripción</TH>\n\
                                             <TH>Alumno</TH>\n\
                                             <TH>Nombre</TH>\n\
                                             <TH>Fecha Inscrito</TH>\n\
                                             <TH>Propuesta</TH>\n\
                                             <TH>Título</TH>\n\
                                             <TH>Tipo</TH>\n\
                                             <TH>Acción</TH></TR>';

                        html_table = html_table + '<TR><TD colspan="8">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Por_Autorizar').empty();
                        $('#tabla_Por_Autorizar').html(html_table);
                    });
            }
            //FIN OBTENEMOS LAS PROPUESTAS DEL PROFESOR

            //Obtenemos los datos generales del alumno
            function Obtener_DatosGeneralesDelAlumno(id_inscripcion, id_propuesta) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_DATOSGENERALES',
                    Id_Tipo_Usuario: 5,
                    Id_Usuario: 0,
                    id_inscripcion: id_inscripcion,
                    id_propuesta: id_propuesta
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_Usuario.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                $('#cuenta').attr("value", value['id_alumno']);
                                $('#alumno').attr("value", value['nombre_usuario'] + ' ' + value['apellido_paterno_usuario'] + ' ' + value['apellido_materno_usuario']);
                                $('#carrera').attr("value", value['descripcion_carrera']);
                                $('#telefono_fijo').attr("value", value['telefono_fijo_alumno']);
                                $('#telefono_celular').attr("value", value['telefono_celular_alumno']);
                                $('#email').attr("value", value['email_usuario']);
                            });
                        } else {
                            $('#cuenta').attr("value", 'Error');
                            $('#alumno').attr("value", 'Error');
                            $('#carrera').attr("value", 'Error');
                            $('#telefono_fijo').attr("value", 'Error');
                            $('#telefono_celular').attr("value", 'Error');
                            $('#email').attr("value", 'Error');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#cuenta').attr("value", 'Error parameters');
                        $('#alumno').attr("value", 'Error parameters');
                        $('#carrera').attr("value", 'Error parameters');
                        $('#telefono_fijo').attr("value", 'Error parameters');
                        $('#telefono_celular').attr("value", 'Error parameters');
                        $('#email').attr("value", 'Error parameters');
                    });
            }

            //EVENTO CLICK SOBRE EL BOTON REVISAR DOC
            $('#tabla_Por_Autorizar').on("click", "button.btn_Revisar", function(e) {
                e.preventDefault();
                var id_alumno = $(this).data('id_alumno');
                var id_documento = $(this).data('id_documento');
                $('#id_alumno_insc').attr("value", $(this).data('id_alumno'));
                $('#id_inscripcion').attr("value", $(this).data('id_inscripcion'));
                $('#id_version_insc').attr("value", $(this).data('id_version'));
                $('#id_documento').attr("value", $(this).data('id_documento'));

                $('#id_profesor').attr("value", $(this).data('id_profesor'));
                $('#id_propuesta').attr("value", $(this).data('id_propuesta'));
                $('#titulo_propuesta').attr("value", $(this).data('titulo_propuesta'));
                $('#correo_alumno').attr("value", $(this).data('correo_alumno'));
                $('#id_carrera').attr("value", $(this).data('id_carrera'));
                $('#descripcion_corta_archivo').attr("value", $(this).data('descripcion_corta_archivo'));

                //Obtenemos los datos generales del Alumno
                $idInscripcion = $(this).data('id_inscripcion');

                Obtener_DatosGeneralesDelAlumno($idInscripcion, '');

                var tituloAdjuntar = '';
                //                    if(id_documento ==5){
                //                        tituloAdjuntar = 'Baja_Inscripcion_' + id_alumno +  "_" + $('#id_version_insc').val() + ".pdf";
                //                        tituloAdjuntar =id_alumno + "_" + 
                //                             $('#id_carrera').val() + "_" +
                //                             $('#id_propuesta').val() + "_" +
                //                             $('#id_version_insc').val() + "_" +
                //                             $('#descripcion_corta_archivo').val() + ".pdf";                                                
                //                    }
                //                    else{
                tituloAdjuntar = id_alumno + "_" +
                    $('#id_carrera').val() + "_" +
                    $('#id_propuesta').val() + "_" +
                    $('#id_version_insc').val() + "_" +
                    $('#descripcion_corta_archivo').val() + ".pdf";
                //                    }                                                                               

                $('#ventanaRevisarInscripcionPDF').dialog({
                    open: function() {
                        $('#ventanaRevisarInscripcionPDF').dialog("option", "title", tituloAdjuntar);
                        var tiempo = new Date();
                        var fileName = '';
                        if ((id_documento) == 5) {
                            fileName = "Docs/Baja_de_Propuesta/" +
                                id_alumno + "_" +
                                $('#id_carrera').val() + "_" +
                                $('#id_propuesta').val() + "_" +
                                $('#id_version_insc').val() + "_" +
                                $('#descripcion_corta_archivo').val() + ".pdf" + "?" + tiempo;
                        } else {
                            fileName = "Docs/Inscripcion_A_Propuesta/" +
                                id_alumno + "_" +
                                $('#id_carrera').val() + "_" +
                                $('#id_propuesta').val() + "_" +
                                $('#id_version_insc').val() + "_" +
                                $('#descripcion_corta_archivo').val() + ".pdf" + "?" + tiempo;
                        }

                        var new_Object = $('#obj_PDF_HA').clone(false);
                        new_Object.attr("type", "application/pdf");
                        new_Object.attr("data", fileName);
                        $("#obj_PDF_HA").replaceWith(new_Object);
                        $('#btn_autorizar_Ins').prop('disabled', '');
                        $('#btn_rechazar_Ins').prop('disabled', '');

                    },
                    title: 'Aprobar Inscripción',
                    modal: true,
                    autoOpen: true,
                    resizable: false,
                    draggable: true,
                    width: '950',
                    height: '630',
                    dialogClass: 'no-close',
                    show: 'slide',
                    hide: 'slide',
                    closeOnEscape: false,
                    position: {
                        at: 'center top'
                    }
                });
            });
            //FIN EVENTO CLICK SOBRE EL BOTON REVISAR DOC                 

            $('#btn_cerrar_Ins').on('click', function(e) {
                e.preventDefault();
                $('#message').empty();
                //                    $('#ventanaReporteBimestralPDF').dialog('destroy');
                $(".ui-dialog-content").dialog("close");
                Obtener_Inscripciones_Pendientes($('#Id_Usuario').val()); //
            });

            //CLICK AL BOTON ACEPTAR 
            $('#btn_autorizar_Ins').on('click', function(e) {
                e.preventDefault();

                $('#ventanaConfirmar_Aceptacion_Insc').dialog({
                    buttons: {
                        "Aceptar": function() {
                            if (!$('#nota_admin_a').val().match(miExpReg_Nota_Aceptacion)) {
                                $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #');
                                $('#ventanaAvisos').dialog('open');
                            } else {
                                $('#ventanaMensajeConfirma').text('Desea dar por Aceptado este Documento ?');
                                $('#ventanaConfirmaVoBo').dialog({
                                    buttons: {
                                        "Aceptar": function() {
                                            $(this).dialog('close');
                                            var id_inscripcion = $('#id_inscripcion').val();
                                            var id_documento = $('#id_documento').val();
                                            var id_version = $('#id_version_insc').val();
                                            var id_estatus = 3; //3.Aceptado
                                            var nota = $('#nota_admin_a').val();
                                            var tipo_Mov = 'ACTUALIZAR_ESTATUS_INSCRIPCION';

                                            var id_alumno = $('#id_alumno_insc').val();
                                            var id_propuesta = $('#id_propuesta').val();
                                            var titulo_propuesta = $('#titulo_propuesta').val();
                                            var id_profesor = $('#id_profesor').val();
                                            var correo_alumno = $('#correo_alumno').val();
                                            var id_carrera = $('#id_carrera').val();
                                            var desc_corta_archivo = $('#descripcion_corta_archivo').val();

                                            actualiza_Estatus_Inscripcion(tipo_Mov, id_inscripcion, id_documento,
                                                id_version, id_estatus, nota, id_alumno, id_propuesta, titulo_propuesta,
                                                id_profesor, correo_alumno, id_carrera, desc_corta_archivo);
                                        },
                                        "Cancelar": function() {
                                            $(this).dialog('close');
                                        }
                                    },
                                    title: 'Confirmar Aceptación',
                                    modal: true,
                                    autoOpen: true,
                                    resizable: true,
                                    draggable: true,
                                    dialogClass: 'no-close ventanaConfirmaUsuario',
                                    closeOnEscape: false
                                });

                            }
                        },
                        "Cancelar": function() {
                            $(this).dialog('close');
                            $('#nota_admin_a').val('');
                        }
                    },
                    close: function() {
                        $('#nota_admin_a').val('');
                    },
                    open: function() {
                        if ($('#id_documento').val() == 5) {
                            $('#ventanaConfirmar_Aceptacion_Insc').dialog("option", "title", "Nota \n\
                                    para Aceptar la Solicitud de Baja");
                        } else {
                            $('#ventanaConfirmar_Aceptacion_Insc').dialog("option", "title", "Nota \n\
                                    para Aceptar la Solicitud de Inscripción");
                        }

                    },
                    title: 'Confirmar Aceptación de la Inscripción',
                    modal: true,
                    autoOpen: true,
                    resizable: false,
                    draggable: true,
                    height: 'auto',
                    width: '450',
                    show: 'slide',
                    hide: 'slide',
                    dialogClass: 'no-close',
                    closeOnEscape: false
                }); //FIN ventanaConfirmar_Aceptacion
            });
            //FIN CLICK AL BOTON ACEPTAR 

            //CLICK AL BOTON RECHAZAR 
            $('#btn_rechazar_Ins').on('click', function(e) {
                e.preventDefault();
                $('#ventanaConfirmar_Rechazo_Insc').dialog({
                    buttons: {
                        "Aceptar": function() {
                            if (!$('#nota_admin').val().match(miExpReg_Nota_Rechazo)) {
                                $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) . - _ #');
                                $('#ventanaAvisos').dialog('open');
                            } else {
                                $('#ventanaMensajeConfirma').text('Desea dar por Rechazado este Documento ?');
                                $('#ventanaConfirmaVoBo').dialog({
                                    buttons: {
                                        "Aceptar": function() {
                                            $(this).dialog('close');
                                            var id_inscripcion = $('#id_inscripcion').val();
                                            var id_documento = $('#id_documento').val();
                                            var id_version = $('#id_version_insc').val();
                                            var id_estatus = 4; //4.Rechazado
                                            var nota = $('#nota_admin').val();
                                            var tipo_Mov = 'ACTUALIZAR_ESTATUS_INSCRIPCION';

                                            var id_alumno = $('#id_alumno_insc').val();
                                            var id_propuesta = $('#id_propuesta').val();
                                            var titulo_propuesta = $('#titulo_propuesta').val();
                                            var id_profesor = $('#id_profesor').val();
                                            var correo_alumno = $('#correo_alumno').val();
                                            var id_carrera = $('#id_carrera').val();
                                            var desc_corta_archivo = $('#descripcion_corta_archivo').val();

                                            actualiza_Estatus_Inscripcion(tipo_Mov, id_inscripcion, id_documento,
                                                id_version, id_estatus, nota, id_alumno, id_propuesta, titulo_propuesta,
                                                id_profesor, correo_alumno, id_carrera, desc_corta_archivo);
                                        },
                                        "Cancelar": function() {
                                            $(this).dialog('close');
                                        }
                                    },
                                    title: 'Confirmar Rechazo',
                                    modal: true,
                                    autoOpen: true,
                                    resizable: true,
                                    draggable: true,
                                    dialogClass: 'no-close ventanaConfirmaUsuario',
                                    closeOnEscape: false
                                });

                            }
                        },
                        "Cancelar": function() {
                            $("#nota_admin").val('');
                            $(this).dialog('destroy');
                        }
                    },
                    open: function() {
                        if ($('#id_documento').val() == 5) {
                            $('#ventanaConfirmar_Rechazo_Insc').dialog("option", "title", 'Nota para el Rechazo de la Solicitud de Baja ?');
                        } else {
                            $('#ventanaConfirmar_Rechazo_Insc').dialog("option", "title", 'Nota para el Rechazo de la Solicitud de Inscripción ?');
                        }
                    },
                    close: function() {
                        $("#nota_admin").val('');
                        $(this).dialog('destroy');
                    },
                    title: 'Confirmar Rechazo de la Inscripción',
                    modal: true,
                    autoOpen: true,
                    resizable: false,
                    draggable: true,
                    height: 'auto',
                    width: '450',
                    show: 'slide',
                    hide: 'slide',
                    dialogClass: 'no-close',
                    closeOnEscape: false
                });
            }); //FIN CONFIRMAR RECHAZO DE REPORTE

            function actualiza_Estatus_Inscripcion(tipo_Mov, id_inscripcion, id_documento, id_version,
                id_estatus, nota, id_alumno, id_propuesta, titulo_propuesta, id_profesor,
                correo_alumno, id_carrera, desc_corta_archivo) {
                var datos = {
                    Tipo_Movimiento: tipo_Mov,
                    id_inscripcion: id_inscripcion,
                    id_documento: id_documento,
                    id_version: id_version,
                    id_estatus: id_estatus,
                    nota: nota,
                    id_alumno: id_alumno,
                    id_propuesta: id_propuesta,
                    titulo_propuesta: titulo_propuesta,
                    id_profesor: id_profesor,
                    correo_alumno: correo_alumno,
                    id_carrera: id_carrera,
                    desc_corta_archivo: desc_corta_archivo
                };
                $('#ventanaProcesando').dialog('open');
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_profesor_Aceptar_Alumnos.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        if (respuesta.success == true) {
                            if (id_estatus == 4) { //4.rechazo
                                $('#ventanaConfirmar_Rechazo_Insc').dialog('close');
                            } else {
                                $('#ventanaConfirmar_Aceptacion_Insc').dialog('close');
                            }
                            $('#btn_autorizar_Ins').prop('disabled', 'disabled');
                            $('#btn_rechazar_Ins').prop('disabled', 'disabled');
                        }
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAviso').html(respuesta.data.message);
                        $('#ventanaAvisos').dialog('open');
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                        $('#ventanaAvisos').dialog('open');

                    });
            };

            $('#ventanaProcesando').dialog({
                title: '',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: false,
                dialogClass: 'no-close no-titlebar',
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
            /*
            $('.entrada_Dato').focus(function(e){
                e.preventDefault();
                f5($(document),false);
            });
            $('.entrada_Dato').blur(function(e){
                e.preventDefault();
                f5($(document),true);
            });

            f5($(document),true); */

            Obtener_Inscripciones_Pendientes($('#Id_Usuario').val());
            $('#ventanaRevisarInscripcionPDF').hide();
            $('#ventanaConfirmar_Aceptacion_Insc').hide();
            $('#ventanaConfirmar_Rechazo_Insc').hide();
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
                <p>Aceptación y Baja de Alumnos</p>
            </div>
        </div>
        <div id="tabla_Por_Autorizar" class="tabla_Registros">
        </div>
        <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
    </div>

    <div id="ventanaRevisarInscripcionPDF">
        <div id="" style="position: absolute; z-index: 99; float: left; border: 1px grey solid; height: 560px; width: 710px; ">
            <object id="obj_PDF_HA" width="700px" height="560px"></object>
        </div>
        <div id="divOpciones_RB" style="text-align: center; float: right; width: 200px; line-height: 2.5em;">
            <button id="btn_autorizar_Ins" class="btn_Herramientas" style="width: 150px;">Aceptar Doc.</button>
            <button id="btn_rechazar_Ins" class="btn_Herramientas" style="width: 150px;">Rechazar Doc.</button>
            <button id="btn_cerrar_Ins" class="btn_Herramientas" style="width: 150px;">Cerrar</button>

            <input type="hidden" id="id_inscripcion" name="id_inscripcion" value="">
            <input type="hidden" id="id_alumno_insc" name="id_alumno_insc" value="">
            <input type="hidden" id="id_documento" name="id_documento" value="">
            <input type="hidden" id="id_version_insc" name="id_version_insc" value="">

            <input type="hidden" id="id_profesor" name="id_profesor" value="">
            <input type="hidden" id="id_propuesta" name="id_propuesta" value="">
            <input type="hidden" id="titulo_propuesta" name="titulo_propuesta" value="">
            <input type="hidden" id="correo_alumno" name="correo_alumno" value="">
            <input type="hidden" id="id_carrera" name="id_carrera" value="">
            <input type="hidden" id="descripcion_corta_archivo" name="descripcion_corta_archivo" value="">

            <div id="" style="text-align: left; float: left; width: 200px; line-height: 1.5em; margin-top: 50px;">
                <table border='0'>
                    <tr>
                        <td>
                            <label for="cuenta">Cuenta:</label>
                            <input style='width: 200px;' type="text" name="cuenta" id="cuenta" class="ventanaInformativa" readonly value="" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="alumno">Alumno:</label>
                            <input style='width: 200px;' type="text" name="alumno" id="alumno" class="ventanaInformativa" readonly value="" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="carrera">Carrera:</label>
                            <input style='width: 200px;' type="text" name="carrera" id="carrera" class="ventanaInformativa" readonly value="" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="telefono_fijo">Tel. Fijo:</label>
                            <input style='width: 200px;' type="text" name="telefono_fijo" id="telefono_fijo" class="ventanaInformativa" readonly value="" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="telefono_celular">Celular:</label>
                            <input style='width: 200px;' type="text" name="telefono_celular" id="telefono_celular" class="ventanaInformativa" readonly value="" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="email">e-mail:</label>
                            <input style='width: 200px;' type="text" name="email" id="email" class="ventanaInformativa" readonly value="" />
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div id="ventanaConfirmar_Aceptacion_Insc">
        <div id="nota">
            <p>
                <textarea id="nota_admin_a" class="entrada_Dato notaVoBo" maxlength="500" onkeyup="javascript:this.value=this.value.toUpperCase();"
                    title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
            </p>
        </div>
    </div>
    <div id="ventanaConfirmar_Rechazo_Insc">
        <p>
            <textarea id="nota_admin" class="entrada_Dato notaVoBo" maxlength="500" onkeyup="javascript:this.value=this.value.toUpperCase();"
                title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>

        </p>
    </div>
    <div id="ventanaConfirmaVoBo">
        <span id="ventanaMensajeConfirma"></span>
    </div>
    <div id="ventanaAvisos">
        <span id="ventanaAviso"></span>
    </div>

    <div id="ventanaProcesando" data-role="header">
        <img id="cargador" src="./assets/images/ui/engrane2.gif" /><br>
        Procesando su transacción....!<br>
        Espere por favor.
    </div>
    <!--Se elimina botón de home mostrado en la parte inferior-->
    <!--    </body>
</html>-->
<!DOCTYPE html>
<!--
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para la admon. de los Programas de Serv Social
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
                        url: '_Negocio/n_administrador_Crear_Nueva_Cuenta.php'
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

            $('#btn_Buscar').on('click', function(e) {
                var id_programa = $('#id_programa').val();
                var desc_programa = $('#descripcion_programa').val();

                if (id_programa.length != 0) {
                    if (!$('#id_programa').val().match(miExpReg_Clave_Programa)) {
                        $('#ventanaAviso').html("Entrada inválida, capture los datos según el formato ejemplo");
                        $('#ventanaAvisos').dialog('open');
                    }
                }
                if (desc_programa.length != 0) {
                    if (!$('#descripcion_programa').val().match(miExpReg_Buscar)) {
                        $('#ventanaAviso').html("Entrada inválida, capture los datos según el formato ejemplo");
                        $('#ventanaAvisos').dialog('open');
                    }
                }
                e.preventDefault();
                Obtener_Programas(id_programa, desc_programa);
            });

            //OBTENEMOS LOS PROGRAMAS
            function Obtener_Programas(id_programa, desc_programa) {
                var datos = {
                    Tipo_Movimiento: 'OBTENER_PROGRAMAS',
                    id_programa: id_programa,
                    desc_programa: desc_programa
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_admon_Programas_SS.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        var html_table = '<TABLE style="width:100%;">';
                        html_table += '<TR><TH>ID</TH>\n\
                                        <TH>Descripción</TH>\n\
                                        <TH>Dependencia</TH>\n\
                                        <TH>Departamento</TH>\n\
                                        <TH>Responsable</TH>\n\
                                        <TH>Teléfono Servicio Social</TH>\n\
                                        <TH>Acción</TH></TR>';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function(key, value) {
                                var btn_Editar = '';
                                btn_Editar = '<button class="btnOpcion btn_Editar" ' +
                                    ' data-id_programa=\'' + value['id_programa'] + '\' ' +
                                    '>Editar</button>';
                                html_table += '<TR>';
                                html_table += '<TD>' + value['id_programa'] + '</TD>';
                                html_table += '<TD style="text-align:left;">' + value['descripcion_pss'] + '</TD>';
                                html_table += '<TD style="text-align:left;">' + esNulo(value['descripcion_dependencia_ss']) + '</TD>';
                                html_table += '<TD style="text-align:left;">' + esNulo(value['subdireccion_departamento_pss']) + '</TD>';
                                html_table += '<TD style="text-align:left;">' + esNulo(value['responsable_pss']) + '</TD>';
                                html_table += '<TD>' + esNulo(value['telefono_servicio_social_pss']) + '</TD>';
                                html_table += '<TD>' + btn_Editar + '</TD>';
                                html_table = html_table + '</TR>';
                            });
                            html_table = html_table + '</TABLE>';
                            $('#tabla_Programas').empty();
                            $('#tabla_Programas').html(html_table);
                        } else {
                            html_table = html_table + '<TR><TD style="text-align:center;" colspan="7">' + respuesta.data.message + '</TD></TR>';
                            html_table = html_table + '</TABLE>'
                            $('#tabla_Programas').empty();
                            $('#tabla_Programas').html(html_table);
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        var html_table = '<TABLE class="tabla_Registros">';
                        html_table += '<TR><TH>ID</TH>\n\
                                        <TH>Descripción</TH>\n\
                                        <TH>Dependencia</TH>\n\
                                        <TH>Departamento</TH>\n\
                                        <TH>Responsable</TH>\n\
                                        <TH>Teléfono Servicio Social</TH>\n\
                                        <TH>Acción</TH></TR>';
                        html_table = html_table + '<TR><TD colspan="7">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                        html_table = html_table + '</TABLE>';
                        $('#tabla_Programas').empty();
                        $('#tabla_Programas').html(html_table);
                    });
            }
            //FIN DE OBTENEMOS LOS PROGRAMAS DE SERVICIO SOCIAL

            $('#tabla_Programas').on("click", "button.btn_Editar", function(e) {
                $('#Tipo_Movimiento').val('ACTUALIZAR');
                var id_programa = $(this).data('id_programa');
                $('#id_programa_seleccionado').val(id_programa);
                $('#id_programa_SS').prop('disabled', 'disabled');
                $('#carrera').empty();
                llena_Catalogo('carrera', 'CATALOGO_GENERALES', 'carreras',
                    'id_carrera as id, descripcion_carrera as descripcion',
                    ' id_carrera NOT IN (SELECT b.id_carrera ' +
                    'FROM programa_carrera b ' +
                    'WHERE b.id_programa = \'' + id_programa + '\')', 'descripcion_carrera');

                Obtener_Programa(id_programa);

                $('#ventanaPrograma').dialog('open');
            });

            //OBTENEMOS EL PROGRAMA
            function Obtener_Programa(id_programa) {
                $('#ventanaProcesando').dialog('open');
                var datos = {
                    Tipo_Movimiento: 'OBTENER_PROGRAMA',
                    id_programa: id_programa
                };
                $.ajax({
                        data: datos,
                        type: "POST",
                        dataType: "json",
                        url: "_Negocio/n_administrador_admon_Programas_SS.php"
                    })
                    .done(function(respuesta, textStatus, jqXHR) {
                        //                            var finicio='';
                        if (respuesta.success == true) {
                            //recorremos cada registro
                            $('#id_programa_SS').val(respuesta.data.registros.Programa_SSid_programa);
                            $('#descripcion_programa_SS').val(respuesta.data.registros.Programa_SSdescripcion);
                            $('#dependencia').val(respuesta.data.registros.Programa_SSid_dependencia);
                            $('#tipo_programa').val(respuesta.data.registros.Programa_SStipo_programa);
                            $('#estatus').val(respuesta.data.registros.Programa_SSid_estatus);
                            $('#subdireccion').val(respuesta.data.registros.Programa_SSsubdireccion);
                            $('#responsable').val(respuesta.data.registros.Programa_SSresponsable);
                            $('#cargo_Responsable').val(respuesta.data.registros.Programa_SScargo);
                            $('#oficina_seccion').val(respuesta.data.registros.Programa_SSoficina);
                            $('#correo_Electronico').val(respuesta.data.registros.Programa_SSemail);
                            $('#telefono_Servicio_Social').val(respuesta.data.registros.Programa_SStelefono_ss);
                            $('#telefono_Dependencia').val(respuesta.data.registros.Programa_SStelefono_dependencia);
                            $('#calle_Numero').val(respuesta.data.registros.Programa_SScalle);
                            $('#colonia').val(respuesta.data.registros.Programa_SScolonia);
                            $('#delegacion_Municipio').val(respuesta.data.registros.Programa_SSdelegacion);
                            $('#codigo_Postal').val(respuesta.data.registros.Programa_SScp);
                            $('#num_exterior').val(respuesta.data.registros.Programa_SSnum_exterior);
                            $('#num_interior').val(respuesta.data.registros.Programa_SSnum_interior);
                            $('#estado').val(respuesta.data.registros.Programa_SSid_estado);
                            var carreras = respuesta.data.registros.Programa_SScarreras;
                            $('#Tipo_Movimiento').val('ACTUALIZAR');

                            var arr_c_a = carreras.split("|");
                            var elementos_arr = arr_c_a.length;
                            var carrera_desc = '';
                            var btn_Borrar = '';

                            $('#tblAplican tbody').html('');
                            for (i = 0; i < elementos_arr; i++) {
                                carr_aplica = arr_c_a[i].split(",");
                                id_carrera = carr_aplica[0];
                                carrera_desc = carr_aplica[1];
                                btn_Borrar = "<button class='btn_borrar_p btnOpcion' data-id_carrera='" + id_carrera +
                                    "' data-desc_carrera ='" + carrera_desc + "'>Borrar</button>";
                                $("#tblAplican tbody").append(
                                    "<tr>" + "<td>" + carrera_desc + "</td>" +
                                    "<td style='text-align:center;'>" + btn_Borrar + "</td>" +
                                    "</tr>");
                            }
                            $('#ventanaProcesando').dialog('close');
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
            } //fin Obtenemos el Programa

            $('#Agregar_Programa').on('click', function(e) {
                $('#Tipo_Movimiento').val('AGREGAR');
                $('#id_programa_SS').prop('disabled', '');
                $('#carrera').empty();
                llena_Catalogo('carrera', 'CATALOGO_GENERALES', 'carreras',
                    'id_carrera as id, descripcion_carrera as descripcion',
                    '', 'descripcion_carrera');
                $('#ventanaPrograma').dialog('open');
            });

            $('#ventanaPrograma').dialog({
                buttons: {
                    "Aceptar": function() {
                        if (validaDatos()) {
                            $('#ventanaConfirmaVoBo').dialog({
                                buttons: {
                                    "Aceptar": function() {
                                        $(this).dialog('close');
                                        $('#ventanaProcesando').dialog('open');
                                        // Por Ajax Agregamos la Propuesta
                                        formDatos = $('#frm_programa').serialize();
                                        $.ajax({
                                                data: formDatos,
                                                type: "POST",
                                                dataType: "json",
                                                url: "_Negocio/n_administrador_admon_Programas_SS.php"
                                            })
                                            .done(function(respuesta, textStatus, jqXHR) {
                                                $('#ventanaProcesando').dialog('close');
                                                if (respuesta.success == true) {
                                                    //                                                        $('#frm_programa input[type=text], textarea').each(function(){
                                                    //                                                            $(this).val('');
                                                    //                                                        });
                                                    //                                                        $('#frm_programa span').each(function(){
                                                    //                                                            $(this).hide();
                                                    //                                                        });                                        
                                                    //                                                        $('#aviso_Aplican tbody').html('');
                                                    //
                                                    //                                                        $('#De_Alta_OK').val("1");
                                                    $('#Tipo_Movimiento').val("ACTUALIZAR");
                                                    $('#id_programa_SS').prop('disabled', 'disabled');
                                                }
                                                $('#ventanaAviso').html(respuesta.data.message);
                                                $('#ventanaAvisos').dialog('open');
                                            })
                                            .fail(function(jqXHR, textStatus, errorThrown) {
                                                $('#ventanaProcesando').dialog('close');
                                                $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                                $('#ventanaAvisos').dialog('open');
                                            });

                                    },
                                    "Cancelar": function() {
                                        $(this).dialog('close');
                                    }
                                },
                                title: 'Confirmar',
                                modal: true,
                                autoOpen: true,
                                resizable: true,
                                draggable: true,
                                dialogClass: 'no-close ventanaConfirmaUsuario',
                                closeOnEscape: false
                            });
                        }
                    },
                    "Cerrar": function() {
                        $('#ventanaPrograma input[type=text], textarea').each(function() {
                            $(this).val('');
                        });
                        $('#ventanaPrograma span').each(function() {
                            $(this).hide();
                        });
                        $('#tblAplican tbody').html('');
                        $(this).dialog('close');
                    }
                },
                title: 'Programa para Servicio Social',
                modal: true,
                autoOpen: false,
                resizable: true,
                draggable: true,
                width: '800',
                show: 'slide',
                hide: 'slide',
                position: {
                    at: 'center top'
                },
                dialogClass: 'no-close',
                closeOnEscape: false
            });

            //VALIDACIONES 
            function validaDatos() {
                var datosValidos = true;
                var id_Programa = $('#id_programa_SS').val();
                var desc_Programa = $('#descripcion_programa_SS').val();
                var dependencia = $('#dependencia').val();
                var tipo_programa = $('#tipo_programa').val();
                var estatus = $('#estatus').val();
                var subdireccion = $('#subdireccion').val();
                var responsable = $('#responsable').val();
                var cargo_responsable = $('#cargo_Responsable').val();
                var oficina = $('#oficina_seccion').val();
                var correo_Electronico = $('#correo_Electronico').val();
                var telefono_ss = $('#telefono_Servicio_Social').val();
                var telefono_dependencia = $('#telefono_Dependencia').val();
                var calle = $('#calle_Numero').val();
                var colonia = $('#colonia').val();
                var delegacion = $('#delegacion_Municipio').val();
                var cp = $('#codigo_Postal').val();
                var num_exterior = $('#num_exterior').val();
                var num_interior = $('#num_interior').val();
                var estado = $('#estado').val();

                $('#aviso_Id_Programa').hide();
                $('#aviso_Descripcion_Programa').hide();
                $('#aviso_Subdireccion').hide();
                $('#aviso_Responsable').hide();
                $('#aviso_Cargo').hide();
                $('#aviso_Oficina').hide();
                $('#aviso_Correo_Electronico').hide();
                $('#aviso_Telefono_Servicio_Social').hide();
                $('#aviso_Telefono_Dependencia').hide();
                $('#aviso_Calle_Numero').hide();
                $('#aviso_Colonia').hide();
                $('#aviso_Delegacion_Municipio').hide();
                $('#aviso_Codigo_Postal').hide();
                $('#aviso_Num_Exterior').hide();
                $('#aviso_Num_Interior').hide();

                var b = '';
                var c = '';
                //Obtenemos una cadena de las carreras que aplican
                $('button.btn_borrar_p').each(function(index) {
                    b = $(this).data('id_carrera');
                    c = c.concat(b, '|');
                });

                if (c == '') {
                    $('#aviso_Aplican').show();
                    $('#id_carreras').val('');
                    datosValidos = false;
                } else {
                    $('#aviso_Aplican').hide();
                    c = c.substr(0, c.length - 1);
                    $('#id_carreras').val(c);
                }

                if ($('#Tipo_Movimiento').val() == 'AGREGAR') {
                    $('#ventanaMensajeConfirma').text('Desea Dar de Alta este Programa de Servicio Social ?');
                } else {
                    $('#ventanaMensajeConfirma').text('Desea Actualizar los datos de este Programa de Servicio Social ?');
                }

                if (!id_Programa.match(miExpReg_Clave_Programa)) {
                    $('#aviso_Id_Programa').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Id_Programa').hide();
                }
                if (!desc_Programa.match(miExpReg_Desc_Programa)) {
                    $('#aviso_Descripcion_Programa').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Descripcion_Programa').hide();
                }
                if (!dependencia) {
                    datosValidos = false;
                }
                if (!tipo_programa) {
                    datosValidos = false;
                }
                if (!estatus) {
                    datosValidos = false;
                }
                if (!subdireccion.match(miExpReg_Subdireccion)) {
                    $('#aviso_Subdireccion').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Subdireccion').hide();
                }
                if (!responsable.match(miExpReg_Responsable)) {
                    $('#aviso_Responsable').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Responsable').hide();
                }
                if (!cargo_responsable.match(miExpReg_Subdireccion)) {
                    $('#aviso_Cargo').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Cargo').hide();
                }
                if (!oficina.match(miExpReg_Subdireccion)) {
                    $('#aviso_Oficina').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Oficina').hide();
                }
                if (!correo_Electronico.match(miExpReg_Mail)) {
                    $('#aviso_Correo_Electronico').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Correo_Electronico').hide();
                }
                if (!telefono_ss.match(miExpReg_Telefono_SS)) {
                    $('#aviso_Telefono_Servicio_Social').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Telefono_Servicio_Social').hide();
                }
                if (!telefono_dependencia.match(miExpReg_Telefono_SS)) {
                    $('#aviso_Telefono_Dependencia').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Telefono_Dependencia').hide();
                }
                if (!calle.match(miExpReg_Direccion)) {
                    $('#aviso_Calle_Numero').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Calle_Numero').hide();
                }
                if (!colonia.match(miExpReg_Direccion)) {
                    $('#aviso_Colonia').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Colonia').hide();
                }
                if (!delegacion.match(miExpReg_Direccion)) {
                    $('#aviso_Delegacion_Municipio').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Delegacion_Municipio').hide();
                }
                if (!cp.match(miExpReg_CP)) {
                    $('#aviso_Codigo_Postal').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Codigo_Postal').hide();
                }
                if (!num_exterior.match(miExpReg_Num_Ext_SS)) {
                    $('#aviso_Num_Exterior').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Num_Exterior').hide();
                }
                if (!num_interior.match(miExpReg_Num_Int_SS)) {
                    $('#aviso_Num_Interior').show();
                    datosValidos = false;
                } else {
                    $('#aviso_Num_Interior').hide();
                }
                if (!estado) {
                    datosValidos = false;
                }

                if (datosValidos == false) {
                    $('#ventanaAviso').html('Debe capturar TODA la información Requerida.');
                    $('#ventanaAvisos').dialog('open');
                }
                return datosValidos;
            };

            //FIN VALIDACIONES PARA GUARDAR

            $('#btn_Agregar_Carrera').click(function(e) {
                e.preventDefault();
                if ($('#carrera').text() == '') {
                    $('#ventanaAviso').text('No existen elementos en la lista.');
                    $('#ventanaAvisos').dialog('open');
                    return false;
                }

                var carrera_desc = $("#carrera option:selected").html();
                var btn_Borrar = "<button class='btn_borrar_p btnOpcion' data-id_carrera='" + $('#carrera').val() +
                    "' data-desc_carrera = '" + carrera_desc + "'>Borrar</button>";
                $("#tblAplican tbody").append(
                    "<tr>" + "<td>" + carrera_desc + "</td>" +
                    "<td style='text-align:center;'>" + btn_Borrar + "</td>" +
                    "</tr>");
                $("#carrera option:selected").remove();
            });

            $('#tblAplican tbody').on('click', '.btn_borrar_p', function(e) {
                e.preventDefault();
                $("<option value='" + $(this).data('id_carrera') + "'>" + $(this).data('desc_carrera') + "</option>").appendTo("#carrera");
                $(this).parents('tr').remove();
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

            llena_Catalogo('dependencia', 'CATALOGO_GENERALES', 'dependencias_ss',
                'id_dependencia_ss as id, descripcion_dependencia_ss as descripcion',
                '', 'descripcion_dependencia_ss');
            llena_Catalogo('tipo_programa', 'CATALOGO_GENERALES', 'tipos_programa_ss',
                'id_tipo_programa_ss as id, descripcion_tipo_programa_ss as descripcion',
                '', 'descripcion_tipo_programa_ss');
            llena_Catalogo('estado', 'CATALOGO_GENERALES', 'estados_republica',
                'id_estado_republica as id, descripcion_estado_republica as descripcion',
                '', 'descripcion_estado_republica');
            llena_Catalogo('carrera', 'CATALOGO_GENERALES', 'carreras',
                'id_carrera as id, descripcion_carrera as descripcion',
                '', 'descripcion_carrera');

            /*$('.entrada_Dato').focus(function(e){
                e.preventDefault();
                f5($(document),false);
            });
            $('.entrada_Dato').blur(function(e){
                e.preventDefault();
                f5($(document),true);
            });*/

            $("#tabs").tabs();
            $('#ventanaPrograma').hide();
            $('#ventanaConfirmaVoBo').hide();


            //f5($(document),true);


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
                <p>Programas para Servicio Social</p>
            </div>
            <div class="barra_Herramientas">
                <input type="button" id="Agregar_Programa" name="Agregar_Programa" value="Agregar" class="btn_Herramientas" />
            </div>
        </div>
        <div class="barra_Parametros">
            <div id="">
                <label for="id_programa" class="etiqueta_Parametro">Id Programa:</label>
                <input type="text" id="id_programa" name="id_programa" value="" maxlength="20" placeholder="2015-10/39-287" autocomplete="off"
                    style="width: 150px;" class="entrada_Dato input_Parametro" title="">
                <label for="descripcion_programa" class="etiqueta_Parametro">Descripción Prog.:</label>
                <input type="text" id="descripcion_programa" name="descripcion_programa" value="" maxlength="100" placeholder="Prototipos Educativos" autocomplete="off"
                    style="width: 350px;" class="entrada_Dato input_Parametro" title="">

                <button id="btn_Buscar" class="btn_Herramientas">Buscar</button>
            </div>
            <div id="tabla_Programas" class="tabla_Registros">
            </div>
        </div>
        <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
        <input type="hidden" id="Id_Usuario_Correo" name="Id_Usuario_Correo" value="<?php echo $_SESSION['correo_usuario_sesion']; ?>">
    </div>

    <div id="ventanaPrograma" class="contenido_Formulario">
        <form name="frm_programa" id="frm_programa" method="" action="">
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-1">Generales</a></li>
                    <li><a href="#tabs-2">Contacto</a></li>
                    <li><a href="#tabs-3">Dirección</a></li>
                    <li><a href="#tabs-4">Carreras que Aplica</a></li>
                </ul>

                <div id="tabs-1">
                    <p>
                        <label for="id_programa_SS" class="label">ID:</label>
                        <input type="text" name="id_programa_SS" id="id_programa_SS" value='' maxlength="20"
                            title="" autocomplete="off" placeholder="2016-171/15-357"
                            style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato" />
                        <span id="aviso_Id_Programa" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="descripcion_programa_SS" class="label">Descripción:</label>
                        <textarea type="text" name="descripcion_programa_SS" id="descripcion_programa_SS" value='' maxlength="300"
                            title="Capture únicamente letras en MAYÚSCULA" autocomplete="off" placeholder="ACTUALIZACIÓN ADMINISTRATIVA Y ORGANIZATIVA"
                            onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"></textarea>
                        <span id="aviso_Descripcion_Programa" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="dependencia" class="label">Dependencia:</label>
                        <select name="dependencia" id="dependencia">
                        </select>
                    </p>
                    <p>
                        <label for="tipo_programa" class="label">Tipo de Programa:</label>
                        <select name="tipo_programa" id="tipo_programa">
                        </select>
                    </p>
                    <p>
                        <label for="estatus" class="label">Estatus:</label>
                        <select name="estatus" id="estatus">
                            <option value="5">Activo</option>
                            <option value="6">De Baja</option>
                        </select>
                    </p>

                    <p>
                        <label for="subdireccion" class="label">Subdirección / Dpto.:</label>
                        <textarea type="text" name="subdireccion" id="subdireccion" value='' maxlength="200"
                            title="Capture únicamente letras en MAYÚSCULA" autocomplete="off" placeholder=""
                            onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"></textarea>
                        <span id="aviso_Subdireccion" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="responsable" class="label">Responsable:</label>
                        <input type="text" name="responsable" id="responsable" value='' maxlength="200"
                            title="Capture únicamente letras en MAYÚSCULA" autocomplete="off" placeholder=""
                            style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato" />
                        <span id="aviso_Responsable" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="cargo_Responsable" class="label">Cargo:</label>
                        <textarea type="text" name="cargo_Responsable" id="cargo_Responsable" value='' maxlength="200"
                            title="Capture únicamente letras en MAYÚSCULA" autocomplete="off" placeholder=""
                            onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"></textarea>
                        <span id="aviso_Cargo" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="oficina_seccion" class="label">Oficina/Sección:</label>
                        <input type="text" name="oficina_seccion" id="oficina_seccion" value='' maxlength="200"
                            title="Capture únicamente letras en MAYÚSCULA" autocomplete="off" placeholder=""
                            style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato" />
                        <span id="aviso_Oficina" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>

                </div>
                <div id="tabs-2">
                    <p>
                        <label for="correo_Electronico" class="label">e-mail:</label>
                        <input type="text" name="correo_Electronico" id="correo_Electronico" maxlength="100" placeholder="miCorreo@dominio.com"
                            title="Capture su dirección de correo TAL Y COMO LA DIÓ DE ALTA CON SU PROVEEDOR" autocomplete="off" class="entrada_Dato" />
                        <span id="aviso_Correo_Electronico" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="telefono_Servicio_Social" class="label">Teléfono Servicio Social:</label>
                        <input type="text" name="telefono_Servicio_Social" id="telefono_Servicio_Social" value='' maxlength="100"
                            title="Capture solamente letras y números" autocomplete="off" placeholder="55 555 555 ext. 55"
                            style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato" />
                        <span id="aviso_Telefono_Servicio_Social" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="telefono_Dependencia" class="label">Teléfono Dependencia:</label>
                        <input type="text" name="telefono_Dependencia" id="telefono_Dependencia" value='' maxlength="100"
                            title="Capture solamente letras y números" autocomplete="off" placeholder="55 555 555 ext. 55"
                            style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato" />
                        <span id="aviso_Telefono_Dependencia" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>

                </div>
                <div id="tabs-3">

                    <p>
                        <label for="calle_Numero" class="label">Calle y No.:</label>
                        <input type="text" name="calle_Numero" id="calle_Numero" value='' maxlength="100" placeholder="AV. UNIVERSIDAD #3000"
                            title="Capture solamente letras y números" autocomplete="off"
                            style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato" />
                        <span id="aviso_Calle_Numero" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="colonia" class="label">Colonia:</label>
                        <input type="text" name="colonia" id="colonia" value='' maxlength="100" placeholder="CIUDAD UNIVERSITARIA"
                            title="Capture solamente letras y números" autocomplete="off" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato" />
                        <span id="aviso_Colonia" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="delegacion_Municipio" class="label">Delegación / Municipio:</label>
                        <input type="text" name="delegacion_Municipio" id="delegacion_Municipio" value='' maxlength="100" placeholder="CD.DE MÉXICO"
                            title="Capture solamente letras" autocomplete="off" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato" />
                        <span id="aviso_Delegacion_Municipio" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="codigo_Postal" class="label">Código Postal:</label>
                        <input type="text" name="codigo_Postal" id="codigo_Postal" value='' maxlength="5" placeholder="04510"
                            title="Capture solamente números" autocomplete="off" class="entrada_Dato" />
                        <span id="aviso_Codigo_Postal" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="num_exterior" class="label">Número Exterior:</label>
                        <input type="text" name="num_exterior" id="num_exterior" value='' maxlength="50" placeholder="120-A"
                            title="" autocomplete="off" class="entrada_Dato" />
                        <span id="aviso_Num_Exterior" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                    <p>
                        <label for="num_interior" class="label">Número Interior:</label>
                        <input type="text" name="num_interior" id="num_interior" value='' maxlength="50" placeholder="10-A"
                            title="" autocomplete="off" class="entrada_Dato" />
                        <span id="aviso_Num_Interior" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>

                    <p>
                        <label for="estado" class="label">Estado:</label>
                        <select name="estado" id="estado" class="select">
                        </select>
                        <span id="aviso_Estado" class="dato_Invalido"><img src="./assets/images/ui/error.ico" /></span>
                    </p>
                </div>

                <div id="tabs-4">
                    <div>
                        <div>
                            <label for="carrera" class="etiqueta_Parametro">Carrera:</label>
                            <select name="carrera" id="carrera" class="combo_Parametro" style="width:350px;">
                            </select>
                            <input type="button" id='btn_Agregar_Carrera' name="btn_Agregar_Carrera" value='Agregar'
                                class='btn_Herramientas' style="margin-left: 20px;" />
                        </div>
                    </div>
                    <div id='div_Aplican' style="padding-top: 10px;">
                        <table id="tblAplican" class="tabla_Registros">
                            <thead>
                                <tr>
                                    <th>Carrera que Aplican</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <span id="aviso_Aplican" class="dato_Invalido" <img src="./assets/images/ui/error.ico" /></span>
                    </div>

                </div>
            </div> <!-- fin tabs -->
            <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
            <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="">
            <input type="hidden" id="id_carreras" name="id_carreras" value="">
            <input type="hidden" id="id_programa_seleccionado" name="id_programa_seleccionado" value="">
        </form>
    </div><!--fin contenido_Form-->

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
    <!--    </body>
</html>-->
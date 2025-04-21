<!DOCTYPE html>
<!--
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para VoBo del Jurado del Alumno
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
        $(document).ready(function(){

        // 1) Mostrar lista de Jurados Pendientes (estatus=12)
        function Obtener_Jurados_Pendientes(id_usuario) {
            var datos = {
                Tipo_Movimiento: 'OBTENER_JURADOS_PENDIENTES',
                id_usuario: id_usuario
            };
            $.ajax({
                data: datos,
                type: "POST",
                dataType: "json",
                url: "_Negocio/n_coord_jdpto_Aprobar_Jurado.php"
            })
            .done(function(respuesta){
                if(!respuesta.success){
                    var html_table = '<table style="width:100%;">'
                                   + '  <tr><th>Propuesta</th><th>Profesor</th><th>Título</th><th>Fecha Alta</th><th>Acción</th></tr>'
                                   + '  <tr><td colspan="5">'+ respuesta.data.message +'</td></tr>'
                                   + '</table>';
                    $('#tabla_Jurados_Pendientes').html(html_table);
                    return;
                }

                var hayRechazados = false;
                $.each(respuesta.data.registros, function(i, v){
                    if (v.id_estatus == '20') {
                        hayRechazados = true;
                        return false;
                    }
                });

                var html_table = '<table style="width:100%;">'
                               + '  <tr>'
                               + '    <th>Propuesta</th>'
                               + '    <th>Profesor</th>'
                               + '    <th>Título</th>'
                               + '    <th>Fecha Alta</th>';
                if (hayRechazados) {
                    html_table += '    <th>Estatus</th>';
                }
                html_table += '    <th>Acción</th>'
                            + '  </tr>';


                $.each(respuesta.data.registros, function(i, v){
                    html_table += '<tr>'
                                + ' <td>'+v.id_propuesta+'</td>'
                                + ' <td>'+(v.nombre || '')+'</td>'
                                + ' <td>'+(v.titulo_propuesta || '')+'</td>'
                                + ' <td>'+(v.fecha_propuesto || '')+'</td>';
                    
                    if (hayRechazados) {
                        if (v.id_estatus == '20') {
                            html_table += '  <td><span style="color:red;font-weight:bold;">' + v.descripcion_estatus +'</span></td>';
                        } else {
                            html_table += '  <td></td>';
                        }
                    }

                    html_table += '  <td>'
                                + '    <button class="btn_Revisar"'
                                + '      data-id_propuesta="'+ v.id_propuesta +'"'
                                + '      data-id_version="'+ v.version +'"'
                                + '      data-id_estatus="'+ v.id_estatus +'"'
                                + '      data-titulo_propuesta="'+ v.titulo_propuesta +'">'
                                + '      Revisar Jurado'
                                + '    </button>'
                                + '  </td>'
                                + '</tr>';
                });
                
                html_table += '</table>';
                $('#tabla_Jurados_Pendientes').html(html_table);
            })
            .fail(function(jqXHR, textStatus, errorThrown){
                var tbl = '<table><tr><th>Error</th></tr>'
                        + '<tr><td>'+ textStatus + ' ' + errorThrown +'</td></tr></table>';
                $('#tabla_Jurados_Pendientes').html(tbl);
            });
        }

        // 2) Al hacer click en "Revisar Jurado"
        $('#tabla_Jurados_Pendientes').on("click", ".btn_Revisar", function(e){
            e.preventDefault();
            $('#Id_Propuesta').val($(this).data("id_propuesta"));
            $('#id_Estatus').val($(this).data("id_estatus"));
            $('#id_Version').val($(this).data("id_version"));
            $('#titulo_propuesta').val($(this).data("titulo_propuesta"));
            $('#Tipo_Movimiento').val('ACTUALIZAR_VoBo');

            // Primero, obtener la lista de profesores/coord/jefes (select)
            Obtener_Profesores().done(function(rsp){
                if(rsp.success){
                    // guardamos en variable global
                    window.listaProf = rsp.data.profesores;
                    // ahora obtener sinodales
                    Obtener_Sinodales($('#Id_Usuario').val(), $('#Id_Propuesta').val(), $('#id_Version').val());
                } else {
                    alert('No fue posible obtener la lista de sinodales posibles');
                }
            });
        });

        // 2.1) Obtener lista de profesores/coord/jefes
        function Obtener_Profesores(){
            return $.ajax({
                url: "_Negocio/n_coord_jdpto_Aprobar_Jurado.php",
                type: "POST",
                dataType: "json",
                data: { Tipo_Movimiento: "OBTENER_PROFESORES_COORD" }
            });
        }

        // 3) Obtener Sinodales (estatus=12) para mostrarlos en la ventana
        function Obtener_Sinodales(id_usuario, id_propuesta, id_version){
            $('#ventanaProcesando').dialog('open');
            var datos = {
                Tipo_Movimiento: 'OBTENER_JURADOS_SELECCIONADO',
                id_usuario: id_usuario,
                id_propuesta: id_propuesta,
                id_version: id_version
            };
            var estatus = $('#id_Estatus').val();
            var esRechazado = (estatus === '20');

            $.ajax({
                data: datos,
                type: "POST",
                dataType: "json",
                url: "_Negocio/n_coord_jdpto_Aprobar_Jurado.php"
            })
            .done(function(rsp){
                var html_table = '<table class="tabla_Registros">'
                            + ' <tr>'
                            + '   <th>Sinodal Propuesto</th>'
                            + '   <th>Aceptado</th>'
                            + '   <th>Reemplazar Por</th>'
                            + '   <th>Nota</th>'
                            + ' </tr>';

                if(rsp.success){
                    $.each(rsp.data.registros, function(i, sin){
                        var n = sin.num_profesor;
                        html_table += '<tr>'
                                    + '  <td>' + (sin.nombre_sinodal_propuesto||'') + '</td>'
                                    + '  <td style="text-align:center;">';
                        
                        if(esRechazado){
                            html_table += '<input type="checkbox" disabled checked>';
                        } else {
                            html_table += '<input type="checkbox" class="chkAcepta" data-num="'+n+'" checked>';
                        }

                        html_table += '  </td>'
                                    + '  <td>';

                        if(esRechazado){
                            html_table += '<select disabled>';
                        } else {
                            html_table += '<select id="selReemp_'+n+'" data-num="'+n+'">';
                        }

                        html_table += '  <option value="0">-- Sin Cambio --</option>';
                        $.each(window.listaProf, function(k,p){
                            html_table += '<option value="'+ p.id_usuario +'">'+ p.nombre_completo +'</option>';
                        });
                        html_table += '</select></td>';

                        html_table += '  <td>';
                        if(esRechazado){
                            html_table += '<textarea style="width:300px;height:2em;" readonly></textarea>';
                        } else {
                            html_table += '<textarea id="txtNota_'+n+'" style="width:300px;height:2em;"></textarea>';
                        }

                        html_table += '  </td>'
                                    + '</tr>';
                    });
                } else {
                    html_table += '<tr><td colspan="4">'+ rsp.data.message +'</td></tr>';
                }
                html_table += '</table>';
                $('#tabla_VoBo').html(html_table);

                if(esRechazado){
                    $('#btn_Guardar').hide();
                } else {
                    $('#btn_Guardar').show();
                }

                $('#ventanaJurado').dialog('open');
            })
            .fail(function(jqXHR, textStatus, errorThrown){
                var msg = '<tr><td colspan="4">Error: '+ textStatus +' '+ errorThrown +'</td></tr>';
                $('#tabla_VoBo').html('<table>'+ msg +'</table>');
                $('#ventanaJurado').dialog('open');
            })
            .always(function(){
                $('#ventanaProcesando').dialog('close');
            });
        }

        // 3.1) Construir <select> para "Reemplazar Por"
        function construirSelect(num){
            var s = '<select id="selReemp_'+num+'" data-num="'+num+'">';
            s += ' <option value="0">-- Sin Cambio --</option>';
            $.each(window.listaProf, function(k,p){
                s += '<option value="'+ p.id_usuario +'">'+ p.nombre_completo +'</option>';
            });
            s += '</select>';
            return s;
        }

        // 4) Validación: si se rechaza un sinodal, forzar que haya nota
        function validaDatos(){
            var valido = true;
            $('.chkAcepta').each(function(){
                var n = $(this).data('num');
                if(!$(this).prop('checked')){
                    // si NO se acepta, checar nota
                    var nota = $('#txtNota_'+n).val().trim();
                    if(nota===''){
                        valido=false;
                    }
                }
            });
            return valido;
        }

        // 5) Dialogo "ventanaJurado"
        $('#ventanaJurado').dialog({
            autoOpen:false,
            modal:true,
            width:850,
            buttons:[
                {
                    id:"btn_Guardar",
                    text:"Guardar",
                    click:function(){
                        if(!validaDatos()){
                            $('#ventanaAviso').html("Si rechazas un sinodal, escribe la nota.").show();
                            $('#ventanaAvisos').dialog('open');
                            return;
                        }
                        $('#ventanaConfirmacion').dialog('open');
                    }
                },
                {
                    id:"btn_Cerrar",
                    text:"Cerrar",
                    click:function(){
                        $(this).dialog('close');
                    }
                }
            ]
        });

        // 6) Diálogo de Confirmación
        $('#ventanaConfirmacion').dialog({
            autoOpen:false,
            modal:true,
            buttons:{
                "Aceptar":function(){
                    $(this).dialog('close');
                    $('#ventanaProcesando').dialog('open');

                    // Armar la cadena: "num_prof, aceptado, id_reemp, nota"
                    var cad='';
                    $('.chkAcepta').each(function(){
                        var n = $(this).data('num');
                        var ok = $(this).prop('checked') ? 1 : 0;
                        var reemp = $('#selReemp_'+n).val() || "0";
                        var nota = $('#txtNota_'+n).val().replace("|"," ");
                        cad += n + ',' + ok + ',' + reemp + ',' + nota + '|';
                    });
                    if(cad.endsWith('|')){
                        cad = cad.slice(0, -1);
                    }
                    $('#lista_VoBo').val(cad);

                    // Disparamos Ajax para guardar
                    var formDatos = $('#frm_VoBo').serialize();
                    $.ajax({
                        data: formDatos,
                        type:"POST",
                        dataType:"json",
                        url: "_Negocio/n_coord_jdpto_Aprobar_Jurado.php"
                    })
                    .done(function(rsp){
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAviso').html(rsp.data.message);
                        $('#ventanaAvisos').dialog('open');
                        if(rsp.success){
                            // recargamos
                            Obtener_Jurados_Pendientes($('#Id_Usuario').val());
                            $('#ventanaJurado').dialog('close');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown){
                        $('#ventanaProcesando').dialog('close');
                        $('#ventanaAviso').html("Error: "+ textStatus +' '+ errorThrown);
                        $('#ventanaAvisos').dialog('open');
                    });
                },
                "Cancelar":function(){
                    $(this).dialog('close');
                }
            }
        });

        // 7) Diálogo Avisos
        $('#ventanaAvisos').dialog({
            autoOpen:false,
            modal:true,
            buttons:{
                "Ok": function(){
                    $(this).dialog('close');
                }
            }
        });

        // 8) Diálogo Procesando
        $('#ventanaProcesando').dialog({
            autoOpen:false,
            modal:true,
            dialogClass:'no-close no-titlebar'
        });

        // Cargar la lista al inicio
        Obtener_Jurados_Pendientes($('#Id_Usuario').val());
        });
    </script>

    <!--    </head>
    <body>
        <header>
            Mi Pefil
        </header>-->
    <div class="encabezado_Formulario">
        <div class="descripcion_Modulo">
            <p>Aprobar Jurado (Coordinador)</p>
        </div>
    </div>

    <div id="tabla_Jurados_Pendientes" class="tabla_Registros"></div>

    <div id="ventanaJurado" style="display:none;">
        <form id="frm_VoBo" method="post" action="">
            <input type="hidden" id="Id_Propuesta"     name="Id_Propuesta">
            <input type="hidden" id="Tipo_Movimiento"  name="Tipo_Movimiento">
            <input type="hidden" id="Id_Usuario"       name="Id_Usuario" value="<?php echo $_SESSION['id_usuario'];?>">
            <input type="hidden" id="id_Estatus"       name="id_Estatus" value="0">
            <input type="hidden" id="id_Version"       name="id_Version" value="0">
            <input type="hidden" id="lista_VoBo"       name="lista_VoBo" value="0">
            <input type="hidden" id="titulo_propuesta" name="titulo_propuesta" value="0">

            <div id="tabla_VoBo"></div>
        </form>
    </div>

    <div id="ventanaConfirmacion" style="display:none;">
        ¿Desea Actualizar sus observaciones?
    </div>

    <div id="ventanaAvisos" style="display:none;">
        <span id="ventanaAviso"></span>
    </div>

    <div id="ventanaProcesando" style="display:none;">
        <img src="./assets/images/ui/engrane2.gif"/><br>
        Procesando su transacción...<br>
        Espere por favor.
    </div>
    <!--Se quita el botón de home-->
    <!--    </body>
</html>-->
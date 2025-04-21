<?php
header('Content-Type: text/html; charset=UTF-8');
header("Cache-Control: no-cache");
header("Pragma: nocache");
session_start();

// Verificar que se trata de un Jefe de Departamento
if (
    !isset($_SESSION["id_tipo_usuario"]) || 
    !isset($_SESSION["id_usuario"]) ||
    $_SESSION["id_tipo_usuario"] != 2  // 2 = Jefe de Depto
) {
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <script src="./assets/js/expresiones_reg.js"></script>
    <script>
    $(document).ready(function(){

        // 1) Función para cargar Jurados pendientes del Jefe (estatus=16,19)
        function Obtener_Jurados_Pendientes_Jefe(id_usuario) {
            var datos = {
                Tipo_Movimiento: 'OBTENER_JURADOS_PENDIENTES_JEFE',
                id_usuario: id_usuario
            };
            $.ajax({
                data: datos,
                type: "POST",
                dataType: "json",
                url: "_Negocio/n_coord_jdpto_Aprobar_Jurado.php"
            })
            .done(function(respuesta){
                var html_table = '<table style="width:100%;">'
                               + '  <tr>'
                               + '    <th>Propuesta</th>'
                               + '    <th>Profesor</th>'
                               + '    <th>Título</th>'
                               + '    <th>Estatus</th>'
                               + '    <th>Acción</th>'
                               + '  </tr>';

                if(respuesta.success){
                    $.each(respuesta.data.registros, function(i, v){
                        // Interpretar estatus
                        var descEstatus = '';
                        switch(v.id_estatus) {
                            case '16': descEstatus = 'Aprobado por Coordinador'; break;
                            case '17': descEstatus = 'En Revisión (Jefe)'; break;
                            case '19': descEstatus = 'Aprobación Parcial'; break;
                            default:   descEstatus = v.descripcion_estatus || 'Desconocido';
                        }

                        html_table += '<tr>'
                                    + '  <td>'+ v.id_propuesta +'</td>'
                                    + '  <td>'+(v.nombre || '')+'</td>'
                                    + '  <td>'+(v.titulo_propuesta || '')+'</td>'
                                    + '  <td>'+ descEstatus +'</td>'
                                    + '  <td>'
                                    + '    <button class="btn_Revisar"'
                                    + '      data-id_propuesta="'+ v.id_propuesta +'"'
                                    + '      data-id_version="'+ v.version +'"'
                                    + '      data-titulo_propuesta="'+ v.titulo_propuesta +'"'
                                    + '      data-id_estatus="'+ v.id_estatus +'">'
                                    + '      Revisar Jurado'
                                    + '    </button>'
                                    + '  </td>'
                                    + '</tr>';
                    });
                } else {
                    html_table += '<tr><td colspan="5">'+ respuesta.data.message +'</td></tr>';
                }
                html_table += '</table>';
                $('#tabla_Jurados_Pendientes').html(html_table);
            })
            .fail(function(jqXHR, textStatus, errorThrown){
                var msg = '<table><tr><th>Error</th></tr>'
                        + '<tr><td>'+ textStatus +' '+ errorThrown +'</td></tr></table>';
                $('#tabla_Jurados_Pendientes').html(msg);
            });
        }

        function Obtener_Sinodales_Jefe(id_propuesta, id_version) {
            var datos = {
                Tipo_Movimiento: 'OBTENER_JURADOS_SELECCIONADO_JEFE',
                id_propuesta: id_propuesta,
                id_version: id_version
            };
            $.ajax({
                url: "_Negocio/n_coord_jdpto_Aprobar_Jurado.php",
                type: "POST",
                dataType: "json",
                data: datos
            })
            .done(function(rsp){
                var html = '<table class="tabla_Registros">'
                        + '<tr><th>#</th><th>Sinodal</th><th>Estatus</th></tr>';
                if(rsp.success){
                    $.each(rsp.data.registros, function(i, sin){
                        var desc = (sin.id_estatus==='16') ? 'Aprobado Coord'
                                : (sin.id_estatus==='19') ? 'Parcial Coord'
                                : sin.id_estatus;
                        html += '<tr>'
                            + '<td>'+ sin.num_profesor +'</td>'
                            + '<td>'+ (sin.nombre_sinodal_propuesto||'') +'</td>'
                            + '<td>'+ desc +'</td>'
                            + '</tr>';
                    });
                } else {
                    html += '<tr><td colspan="3">'+ (rsp.data.message||'Sin datos') +'</td></tr>';
                }
                html += '</table>';
                $('#listaSinodalesJefe').html(html);
            })
            .fail(function(jqXHR, textStatus, errorThrown){
                $('#listaSinodalesJefe').html("Error: "+ textStatus +" "+ errorThrown);
            });
        }

        // 2) Cuando se da clic en "Revisar Jurado"
        $('#tabla_Jurados_Pendientes').on("click", ".btn_Revisar", function(e){
            e.preventDefault();
            $('#Id_Propuesta').val($(this).data("id_propuesta"));
            $('#id_Version').val($(this).data("id_version"));
            $('#id_Estatus').val($(this).data("id_estatus"));
            $('#titulo_propuesta').val($(this).data("titulo_propuesta"));

            // 1) Obtener sinodales del Jefe
            Obtener_Sinodales_Jefe($(this).data("id_propuesta"), $(this).data("id_version"));

            // 2) Abrir el diálogo
            $('#ventanaJurado').dialog('open');
        });

        // 3) Función para determinar y mostrar los botones de acción
        function MostrarBotonesAccion(estatus) {
            var contenido = '<div style="margin-top:20px;">';

            if(estatus == '16' || estatus == '19'){
                // Estatus 16/19 => Se puede poner en revisión (17)
                contenido += '<button class="accion-jefe" data-accion="revision">Marcar En Revisión (17)</button>';
            }
            else if(estatus == '17'){
                // Estatus 17 => Se puede aprobar final (18) o rechazar (12)
                contenido += '<button class="accion-jefe" style="margin-right:10px;" data-accion="final">Aprobación Definitiva (18)</button>';
                contenido += '<button class="accion-jefe" data-accion="rechazo">Rechazar y Devolver (12)</button>';
            }

            contenido += '</div>';
            $('#area_botones').html(contenido);
        }

        // 4) Evento al hacer clic en los botones de acción-jefe
        $(document).on('click', '.accion-jefe', function(){
            var accion = $(this).data('accion');
            $('#accion_jefe').val(accion);

            if(accion == 'rechazo') {
                // Para rechazo, solicitar nota
                $('#ventanaNotaRechazo').dialog('open');
            } else {
                // Para "revision" o "final", pasamos directo a confirmación
                $('#ventanaConfirmacion').dialog('open');
            }
        });

        // 5) Procesar la acción (Ajax)
        function ProcesarAccionJurado(){
            $('#ventanaProcesando').dialog('open');

            var datos = {
                Tipo_Movimiento: 'ACTUALIZAR_VoBo',
                Id_Propuesta: $('#Id_Propuesta').val(),
                id_Version:   $('#id_Version').val(),
                Id_Usuario:   $('#Id_Usuario').val(),
                titulo_propuesta: $('#titulo_propuesta').val(),
                accion_jefe: $('#accion_jefe').val()
            };
            // Si es rechazo, mandamos nota
            if($('#accion_jefe').val()=='rechazo'){
                datos.nota_rechazo = $('#nota_rechazo').val();
            }

            $.ajax({
                data: datos,
                type: "POST",
                dataType: "json",
                url: "_Negocio/n_coord_jdpto_Aprobar_Jurado.php"
            })
            .done(function(rsp){
                $('#ventanaProcesando').dialog('close');
                // Mostrar aviso
                $('#ventanaAviso').html(rsp.data.message || 'Respuesta sin mensaje');
                $('#ventanaAvisos').dialog('open');
                if(rsp.success){
                    // Recargar la lista
                    Obtener_Jurados_Pendientes_Jefe($('#Id_Usuario').val());
                    $('#ventanaJurado').dialog('close');
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown){
                $('#ventanaProcesando').dialog('close');
                $('#ventanaAviso').html('Error Ajax: '+ textStatus +' '+ errorThrown);
                $('#ventanaAvisos').dialog('open');
            });
        }

        // 6) Diálogos

        // Diálogo principal (ventanaJurado)
        $('#ventanaJurado').dialog({
            autoOpen:false,
            modal:true,
            width:650,
            title:'Revisión de Jurado - Jefe',
            buttons: [
                {
                    text: "Aceptar",
                    click: function () {
                        $('#accion_jefe').val('final');
                        ProcesarAccionJurado();
                    }
                },
                {
                    text: "Rechazar",
                    click: function () {
                        $('#accion_jefe').val('rechazo');
                        ProcesarAccionJurado();
                    }
                },
                {
                    text: "Cerrar",
                    click: function() {
                        $(this).dialog('close');
                    }
                }
            ]
        });

        // Diálogo para Nota de Rechazo
        $('#ventanaNotaRechazo').dialog({
            autoOpen:false,
            modal:true,
            width:500,
            title:'Nota de Rechazo',
            buttons:{
                "Continuar":function(){
                    var laNota = $('#txtNotaRechazo').val().trim();
                    if(laNota==''){
                        alert('Debe escribir una nota de rechazo');
                        return;
                    }
                    $('#nota_rechazo').val(laNota);
                    $(this).dialog('close');
                    $('#ventanaConfirmacion').dialog('open');
                },
                "Cancelar":function(){
                    $(this).dialog('close');
                }
            }
        });

        // Diálogo de Confirmación
        $('#ventanaConfirmacion').dialog({
            autoOpen:false,
            modal:true,
            width:400,
            title:'Confirmar Acción',
            buttons:{
                "Aceptar":function(){
                    $(this).dialog('close');
                    ProcesarAccionJurado();
                },
                "Cancelar":function(){
                    $(this).dialog('close');
                }
            }
        });

        // Diálogo Avisos
        $('#ventanaAvisos').dialog({
            autoOpen:false,
            modal:true,
            width:400,
            buttons:{
                "OK":function(){
                    $(this).dialog('close');
                }
            }
        });

        // Diálogo Procesando
        $('#ventanaProcesando').dialog({
            autoOpen:false,
            modal:true,
            dialogClass:'no-close no-titlebar'
        });

        // 7) Cargar los jurados pendientes del jefe al iniciar
        Obtener_Jurados_Pendientes_Jefe($('#Id_Usuario').val());
    });
    </script>
</head>
<body>

    <!-- Encabezado -->
    <div class="encabezado_Formulario">
        <div class="descripcion_Modulo">
            <p>Aprobar Jurado (Jefe de Departamento)</p>
        </div>
    </div>

    <!-- Contenedor de la tabla principal -->
    <div id="tabla_Jurados_Pendientes" class="tabla_Registros"></div>

    <!-- Diálogo principal -->
    <div id="ventanaJurado" style="display:none;">
        <form id="frm_Accion" method="post">
            <input type="hidden" id="Id_Propuesta"     name="Id_Propuesta">
            <input type="hidden" id="Id_Usuario"       name="Id_Usuario" value="<?php echo $_SESSION['id_usuario'];?>">
            <input type="hidden" id="id_Estatus"       name="id_Estatus">
            <input type="hidden" id="id_Version"       name="id_Version">
            <input type="hidden" id="titulo_propuesta" name="titulo_propuesta">
            <input type="hidden" id="accion_jefe"      name="accion_jefe">
            <input type="hidden" id="nota_rechazo"     name="nota_rechazo">
            <div id="listaSinodalesJefe"></div>
            <div id="area_botones" style="margin-top:20px;"></div>
        </form>
    </div>

    <!-- Diálogo para Nota de Rechazo -->
    <div id="ventanaNotaRechazo" style="display:none;">
        <p>Por favor indique el motivo del rechazo:</p>
        <textarea id="txtNotaRechazo" style="width:100%; height:80px;"></textarea>
    </div>

    <!-- Diálogo de Confirmación -->
    <div id="ventanaConfirmacion" style="display:none;">
        <p>¿Está seguro de realizar esta acción?</p>
    </div>

    <!-- Diálogo de Avisos -->
    <div id="ventanaAvisos" style="display:none;">
        <span id="ventanaAviso"></span>
    </div>

    <!-- Diálogo de Procesando -->
    <div id="ventanaProcesando" style="display:none;text-align:center;">
        <img src="./assets/images/ui/engrane2.gif"/><br>
        Procesando su transacción...<br>
        Espere por favor.
    </div>

</body>
</html>

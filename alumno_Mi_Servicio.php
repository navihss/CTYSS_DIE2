<!DOCTYPE html>
<!--
Fecha:          Junio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para la captura del Servicio Social y los Documentos para su Aceptación
-->
<?php
    header('Content-Type: text/html; charset=UTF-8');
    header("Cache-Control: no-cache");
    header("Pragma: nocache");
    session_start();
    
     if(!isset($_SESSION["id_tipo_usuario"]) and
        !isset($_SESSION["id_usuario"])){
    header('Location: index.php');
        }
    
?>

<html>
    <head>
<!--        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/jquery-ui.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="menu/estilo_menu.css" /> 
        <script src="js/jquery-1.12.4.min.js"></script>
        <script src="js/jquery-ui.min.js"></script>-->
        <script src="js/expresiones_reg.js"></script>
        <script src="js/ruta_documentos.js"></script>        
        
        <script>
            $( document ).ready(function() {
                
                $('#tabla_Mis_Servicios').on("click", "button.btn_Borrar", function(e){
                    e.preventDefault();
                    var docs_sin_enviar = $(this).data('docs_sin_enviar');
                    $('#docs_sin_enviar').val(docs_sin_enviar);
                    $('#Id_Carrera').val($(this).data("id_carrera"));
                    $('#Id_SS').val($(this).data("id_ss"));
                    if(docs_sin_enviar == 4){ //carta de inicio, historial, carta acept, carta de terminación
                        $('#Tipo_Movimiento').val("BORRAR");
                        $('#nvo_id_baja').val(6); //6. por el usuario 
                        $('#nvo_id_estatus').val(14); //14.Baja realizada por el Usuario
                        $('#ventanaConfirmacionBorrar').dialog('open');                       
                    }
                    else{                      
                        $('#Tipo_Movimiento').val("SOLICITAR_BAJA");
                        $('#nvo_id_baja_doc').val(5); //5. Activo hasta que el Administrador Autorice
                        $('#nvo_id_estatus_doc').val(15); //15.Baja por Autorizar Admin
                        
                        var tituloAdjuntar = 'Para el Servicio Social <b>' + $(this).data('id_ss') +
                                '</b>. Seleccione su archivo <b>' + 'Solicitud de Baja de Servicio Social' + 
                                ',</b> en formato PDF, INDICANDO LOS MOTIVOS por los que Solicita la Baja. <b>(Tamaño máximo 500MB)</b><br><br>';
                        $('#lblTitulo_SubirArchivo').html(tituloAdjuntar);
                        $('#id_ss_doc').attr("value", $(this).data('id_ss'));
                        $('#id_documento_doc').attr("value",8); //8. solicitud de baja de SS
                        $('#id_usuario_doc').attr("value", $(this).data('id_alumno'));
                        $('#desc_corta_doc').attr("value", 'Solicitud_Baja_SS');
                        $('#id_carrera_doc').attr("value", $(this).data('id_carrera'));
                        $('#docs_de_envio').attr("value", 'BAJA_SERVICIO_SOCIAL');
                        $('#ventanaSubirArchivo').dialog('open');
                    }
//                    $('#ventanaConfirmacionBorrar').text('Desea dar de Baja el Servicio Social ' + $(this).data("id_ss") + '?');
                     
                });

                $('#tabla_Mis_Servicios').on("click", "button.btn_Editar", function(e){
                    e.preventDefault();
                    $('#Id_Carrera').val($(this).data("id_carrera"));
                    $('#Id_SS').val($(this).data("id_ss"));                    
                    $('#Tipo_Movimiento').val('ACTUALIZAR');
                    $('#id_Estatus_ss').val($(this).data("id_estatus"));
                    var id_ss = $(this).data("id_ss");
                    Obtener_Servicio(id_ss);
                    deshabilitaControles();
                    $('#ventanaServicioSocial').dialog('open');
                });
                
                $('#tabla_Mis_Servicios').on("click", "button.btn_EnviarDocs", function(e){
                    e.preventDefault();
                    $('#Id_Carrera').val($(this).data("id_carrera"));
                    $('#id_carrera_doc').val($(this).data("id_carrera"));
                    $('#Id_SS').val($(this).data("id_ss"));                     
                    var id_ss = $(this).data("id_ss");
                    Obtener_Mis_Documentos_Enviados(id_ss);
                    $('#ventanaDocumentosEnviados').dialog('open');
                    $('#docs_de_envio').attr("value", 'SERVICIO_SOCIAL')
                });
                
                $('#tabla_Mis_Docs').on('click', "button.btn_Adjuntar", function(e){
                    e.preventDefault();
                    var tituloAdjuntar = 'Para el Servicio Social <b>' + $(this).data('id_ss') +
                            ', Versión ' + $(this).data('id_version') +
                            '</b>. Seleccione su archivo <b>' + $(this).data('desc_documento') + 
                            ',</b> en formato PDF. <b>(Tamaño máximo 500MB)</b><br><br>';
                    $('#lblTitulo_SubirArchivo').html(tituloAdjuntar);
                    $('#id_ss_doc').attr("value", $(this).data('id_ss'));
                    $('#id_documento_doc').attr("value", $(this).data('id_documento'));
                    $('#id_version_doc').attr("value", $(this).data('id_version'));
                    $('#id_usuario_doc').attr("value", $(this).data('id_alumno'));
                    $('#desc_corta_doc').attr("value", $(this).data('desc_documento_corta'));
                    $('#ventanaSubirArchivo').dialog('open');
                });

                //ADJUNTAR ARCHIVO 
                $('#loading').hide();
                $(':file').change(function(){                    
                    var archivo_selec = $('#file')[0].files[0];
                    var archivo_nombre = archivo_selec.name;
                    var archivo_extension = archivo_nombre.substring(archivo_nombre.lastIndexOf('.')+1);
                    var archivo_tamano = archivo_selec.size;
                    var archivo_tipo = archivo_selec.type;

                    var info_Archivo_Selec='';                    
                    info_Archivo_Selec += "<table class='tabla_Registros'><tr><th colspan='2'><span>Información del Archivo seleccionado:</span></th></tr>";
                    info_Archivo_Selec += "<tr><td><b>Nombre del archivo:</b></td><td>" + archivo_nombre + "</td></tr>";
                    info_Archivo_Selec += "<tr><td><b>Extensión:</b></td><td>" + archivo_extension + "</td></tr>";
                    info_Archivo_Selec += "<tr><td><b>Tipo:</b></td><td>" + archivo_tipo + "</td></tr>";
                    info_Archivo_Selec += "<tr><td><b>Tamaño:</b></td><td>" + (archivo_tamano / 1048576) + " MB</td></tr></table>";
                    
                    $('#message').html(info_Archivo_Selec);
                    $('#loading').empty();                
                    
                    if (parseInt(archivo_tamano) > 500000000){
                        $('#file').val('');
                        $('#ventanaAviso').html('El Tamaño del Archivo excede los 500 MB permitidos.');
                        $('#ventanaAvisos').dialog('open');
                    }
                    
                });
                
                $('#frmSubirPDF').on('submit',function(e){
                    e.preventDefault();
                    var docs_de_envio = $('#docs_de_envio').val();     
                    var archivo_php = '';
                    $('#ventanaProcesando').dialog('open');
                    $('#loading').html('<h1>Loading...</h1>');
                    $('#loading').show();
                    if(docs_de_envio == 'SERVICIO_SOCIAL'){
                        archivo_php = 'uploadFile_2_procesa.php';
                    }
                    else{
                        archivo_php = 'uploadFile_8_procesa.php';
                    }
                            
                    $.ajax({
                        url     : archivo_php,
                        type    : "POST",
                        data    : new FormData(this),
                        contentType : false,
                        cache   : false,
                        processData : false,
                        success  : function(data){
                            $('#loading').html(data);
                            $('#file').val('');
                            $('#ventanaProcesando').dialog('close');
                        }
                    });
                });

                $('#selec_Mis_Carreras').change(function(e){
                    e.preventDefault();
                    var id_carrera_sel = $(this).val();
                    $('#Id_Carrera').val(id_carrera_sel);
                    Obtener_Servicios_Del_Alumno(id_carrera_sel);
                });
                
                //Array para dar formato en español 
                $.datepicker.regional['es'] =  { 
                        closeText: 'Cerrar',  
                        prevText: 'Previo',  
                        nextText: 'Próximo', 
                        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',                           
                                    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'], 
                        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun', 
                        'Jul','Ago','Sep','Oct','Nov','Dic'], 
                        monthStatus: 'Ver otro mes', yearStatus: 'Ver otro año', 
                        dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'], 
                        dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sáb'], 
                        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'], 
                        dateFormat: 'dd/mm/yy', firstDay: 0,  
                        initStatus: 'Selecciona la fecha', isRTL: false,
                    }; 
                $.datepicker.setDefaults($.datepicker.regional['es']); 

                $( "#fecha_Inicio" ).datepicker({
                  minDate: new Date('2016/03/01')
                });

                $('#fecha_Inicio').datepicker({
                    changeYear : true,
                    changeMonth : true,
                    yearRange : '2016:2050',
                    onSelect : function(date){
                        $("#fecha_Inicio ~ .ui-datepicker").hide();
                    }

                });

                //HABILITA LOS CONTROLES PARA AGREGAR UN NUEVO SERVICIO SOCIAL
                function habilitaControles(){
                    $('#fecha_Inicio').prop('disabled',false);
                    $('#duracion').prop('disabled',false);
                    $('#Tipo_Remuneracion').prop('disabled',false);
                    $('#percepcion_Mensual').prop('disabled',false);
                    $('#clave_Programa').prop('disabled',false);
                    $('#numero_Creditos').prop('disabled',false);
                    $('#porcentaje_Avance').prop('disabled',false);
                    $('#promedio').prop('disabled',false);
                    $('#jefe_Inmediato').prop('disabled',false);
                }
                
                //DESHABILITA LOS CONTROLES PARA EDITAR LOS DATOS DE UN SERVICIO SOCIAL
                function deshabilitaControles(){
                    $('#fecha_Inicio').prop('disabled',true);
                    $('#duracion').prop('disabled',true);
//                    $('#Tipo_Remuneracion').prop('disabled',true);
//                    $('#percepcion_Mensual').prop('disabled',true);
//                    $('#otro_Tipo_Remuneracion').prop('disabled',true);
                    $('#clave_Programa').prop('disabled',true);
                    $('#numero_Creditos').prop('disabled',true);
                    $('#porcentaje_Avance').prop('disabled',true);
                    $('#promedio').prop('disabled',true);
                    $('#jefe_Inmediato').prop('disabled',true);                    
                }
                
                //VALIDACIONES 
                function validaDatos(){
//                    $('#Actualizar_Mi_Pefil_Alumno').prop('disable',true);
                    var datosValidos = true;
                    var clave = $('#clave').val();
                    var fechaInicio = $('#fecha_Inicio').val();
                    var duracion = $('#duracion').val();                    
                    var Tipo_Remuneracion = $('#Tipo_Remuneracion').val();
                    var percepcion_Mensual = $('#percepcion_Mensual').val();
                    var clave_Programa = $('#clave_Programa').val();
                    var numero_Creditos = $('#numero_Creditos').val();
                    var porcentaje_Avance = $('#porcentaje_Avance').val();
                    var promedio = $('#promedio').val();
                    var jefe_Inmediato = $('#jefe_Inmediato').val();                    
                   
                    $('#aviso_Clave').hide();
                    $('#aviso_Fecha_Inicio').hide();
                    $('#aviso_Duracion').hide();
                    $('#aviso_Tipo_Remuneracion').hide();
                    $('#aviso_Percepcion_Mensual').hide();
                    $('#aviso_Clave_Programa').hide();
                    $('#descripcion_Programa').hide();
                    $('#aviso_Numero_Creditos').hide();
                    $('#aviso_Porcentaje_Avance').hide();
                    $('#aviso_Promedio').hide();                    
                    $('#aviso_Jefe_Inmediato').hide();

                    if (clave =='')
                    {
                        $('#ventanaConfirmacionAgregarSS').text('Desea Dar de Alta este Servicio Social ?');
                    }
                    else{
                        $('#ventanaConfirmacionAgregarSS').text('Desea Actualizar los datos de este Servicio Social ?');
                    }
                    
                    if (fechaInicio =='')
                    {
                        $('#aviso_Fecha_Inicio').show();
                        datosValidos = false;
                    }
                    if (!(duracion))
                    {
                        $('#aviso_Duracion').show();
                        datosValidos = false;
                    }
                    if (!(Tipo_Remuneracion))
                    {
                        $('#aviso_Tipo_Remuneracion').show();
                        datosValidos = false;
                    }                    
                    if (!percepcion_Mensual.match(miExpReg_Percepcion))
                    {
                        $('#aviso_Percepcion_Mensual').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Percepcion_Mensual').hide();
                    }
                    
                    if (!clave_Programa.match(miExpReg_Clave_Programa))
                    {
                        $('#aviso_Clave_Programa').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Clave_Programa').hide();
                    }
                    
                    if (!numero_Creditos.match(miExpReg_EnteroSinSigno))
                    {
                        $('#aviso_Numero_Creditos').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Numero_Creditos').hide();
                    }
                    
                    if (!porcentaje_Avance.match(miExpReg_Porcentaje))
                    {
                        $('#aviso_Porcentaje_Avance').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Porcentaje_Avance').hide();
                    }
                    
                    if (!promedio.match(miExpReg_Promedio))
                    {
                        $('#aviso_Promedio').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Promedio').hide();
                    }
                    
                    if (!jefe_Inmediato.match(miExpReg_Nombre))
                    {
                        $('#aviso_Jefe_Inmediato').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Jefe_Inmediato').hide();
                    }

                    if (fechaInicio =='' || duracion =='' || Tipo_Remuneracion =='' || percepcion_Mensual=='' 
                         || clave_Programa =='' || numero_Creditos =='' || porcentaje_Avance ==''|| promedio =='' 
                         || jefe_Inmediato =='' || !(Tipo_Remuneracion) || !(duracion))
                    {
                        $('#ventanaAviso').html('Debe capturar TODA la información Requerida.');
                        $('#ventanaAvisos').dialog('open');
                        
                        datosValidos = false;
                        return datosValidos;
                    }
                    
                    var esProgExterno =programaExterno(clave_Programa);
                    if(esProgExterno){
                        if(!(porcentaje_Avance >= 35)){
                            $('#ventanaAviso').html('Para inscribirse en un Programa dentro de la Facultad de Ingeniería, debe de tener al menos 35% de créditos totales de avance.');
                            $('#ventanaAvisos').dialog('open');                        
                            datosValidos = false;                        
                            return datosValidos;                            
                        }
                    }    
                    else{
                        if(!(porcentaje_Avance >= 70)){
                            $('#ventanaAviso').html('Para inscribirse en un Programa fuera de la Facultad de Ingeniería, debe de tener al menos 70% de créditos totales de avance.');
                            $('#ventanaAvisos').dialog('open');                        
                            datosValidos = false;                        
                            return datosValidos;
                        }
                    }
//                    $('#Actualizar_Mi_Pefil_Alumno').prop('disable',false);
                    return datosValidos;
                };

                //FIN VALIDACIONES PARA GUARDAR
                
//              VALIDAMOS QUE LA CLAVE DE PROGRAMA DE SS EXISTA EN LA BD
                $('#clave_Programa').blur(function(){
                    if ($('#clave_Programa').val()==""){
                        return;
                    } 

                    $("#cargandoAjax").css("display", "inline");
                    $('#aviso_Clave_Programa').hide();
                    $('#descripcion_Programa').hide();
                    var datosAPasar = {
                        clavePrograma : $('#clave_Programa').val(),
                        claveCarrera : $('#Id_Carrera').val(),
                        id_division: $('#Id_Division').val(),
                    };
                    console.log(">>> Id_Division ANTES DE validaProgramaServicioSocial");
                    console.log(datosAPasar);
                                        
                    $.ajax({
                        data : datosAPasar,
                        type : "POST",
                        dataType : "json",
                        url : "validaProgramaServicioSocial.php"
                    })
                            .done(function(respuesta,textStatus,jqXHR){
//                                $('#ventanaProcesando').dialog('close');                        
//                                $('#De_Alta_OK').val("0");
                                $("#cargandoAjax").css("display", "none");
                                $('#aviso_Clave_Programa').hide();
                                $('#descripcion_Programa').hide();
                                if (respuesta.success == 'EXISTE'){ // Ya Existe La Cuenta
                                    $('#aviso_Clave_Programa').show();
                                    $('#descripcion_Programa').show();
                                    var resultado = respuesta.data.message;
                                    var arr_resultado = resultado.split("|");
                                    var mensaje = arr_resultado[0];
                                    var descripcion = arr_resultado[1];
                                    $('#aviso_Clave_Programa').html(mensaje);
                                    $('#descripcion_Programa').html(descripcion);
//                                    $('#clave_Programa').prop('value','');
//                                    $('#enviar').prop('disabled', true);
                                }
                                else if (respuesta.success == 'NOEXISTE'){
                                    $('#aviso_Clave_Programa').show();
                                    $('#aviso_Clave_Programa').text(respuesta.data.message);
                                    $('#clave_Programa').prop('value','');
//                                    $('#enviar').prop('disabled', false);
                                }
                                else
                                {
//                                    $('#enviar').prop('disabled', true);
                                    $('#aviso_Clave_Programa').show();                                    
                                    $('#ventanaAviso').html(respuesta.data.message);                                                                        
                                    $('#clave_Programa').val('');
                                    $('#aviso_Clave_Programa').prop('value','');
//                                    $('#enviar').prop('disabled', false);
                                    $('#ventanaAvisos').dialog('open');
                                }                                                    
                            })
                                    .fail(function(jqXHR,textStatus,errorThrown){
                                        $("#cargandoAjax").css("display", "none");
                                        $('#aviso_Clave_Programa').hide();
                                        $('#clave_Programa').val('');
//                                        $('#enviar').prop('disabled', true);

                                        $('#ventanaAviso').html('La solicitud ha fallado. ' + textStatus + '. ' + errorThrown);
                                        $('#ventanaAvisos').dialog('open');                            
                                    });                                                                
                    
                }); // ***** FIN VALIDACION PROGRAMA DE SERVICIO SOCIAL                
                
                //LLENAMOS EL COMBOBOX CON LOS TIPOS DE REMUNERACION
                var datos = {tabla_Catalogo : 'tipo_remuneracion',
                            tabla_Campos : 'id_tipo_remuneracion as id, descripcion_tipo_remuneracion as descripcion'
                            };
                $.ajax({
                    data : datos,
                    type : 'POST',
                    dataType : 'json',
                    url : '_Negocio/n_Catalogos_Generales.php'
                })
                    .done(function(respuesta,textStatus,jqXHR){
                        var html_options='';
                        if (respuesta.success == true){
                            //recorremos cada registro
                            $.each(respuesta.data.registros, function( key, value ) {
                                //recorremos los valores de cada usuario
                                html_options = html_options + '<option value=' + value['id'] +
                                        '>' + value['descripcion'] + '</option>';
                            });
                            $('#Tipo_Remuneracion').empty();
                            $('#Tipo_Remuneracion').html(html_options);
                        }
                        else {
//                            $('#Actualizar_Mi_Pefil_Alumno').prop('disable',true);
                            $('#ventanaAviso').html(respuesta.data.message);
                            $('#ventanaAvisos').dialog('open');                                                                    
                        }
                    })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                                $('#ventanaAvisos').dialog('open');                            
                            });                 

                //OBTENEMOS LAS CARRERAS EN LAS QUE ESTA INSCRITO EL ALUMNO PARA EL LISTBOX
                function Obtener_Carreras_Del_Alumno(){                                           
                    var datos = {Tipo_Movimiento : 'OBTENER',
                               Id_Usuario : $('#Id_Usuario').val(),
                               Id_Carrera : 0,
                               Id_Division : $('#Id_Division').val(),
                           };
                    console.log('>>> Se agrega division');
                    console.log(datos);
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_Alumno_Carrera.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_options='';
                           if (respuesta.success == true){
                                //recorremos cada registro
                                $.each(respuesta.data.registros, function( key, value ) {
                                    //recorremos los valores de cada usuario
                                    html_options = html_options + '<option value=' + value['id_carrera'] +
                                            '>' + value['descripcion_carrera'] + '</option>';
                                });
                                $('#selec_Mis_Carreras').empty();
                                $('#selec_Mis_Carreras').html(html_options);
                                
                                $('#selec_Mis_Carreras option:first-child').attr('selected','selected');
                                $('#Id_Carrera').val($('#selec_Mis_Carreras').val());
                                Obtener_Servicios_Del_Alumno($('#Id_Carrera').val()); 
                            }
                            else {
                                $('#Agregar_Servicio').prop('disable',true);
                                $('#ventanaAviso').html(respuesta.data.message);
                                $('#ventanaAvisos').dialog('open');                                                                    
                            }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                                $('#ventanaAvisos').dialog('open');                            
                            });                 
                                       
                }//FIN OBTENEMOS LAS CARRERAS EN LAS QUE ESTA INSCRITO EL ALUMNO

                //OBTENEMOS EL SERVICIO SOCIAL DEL ALUMNO
                function Obtener_Servicio(id_ss){                                           
                    $('#ventanaProcesando').dialog('open');        
                    var datos = {Tipo_Movimiento : 'SELECCIONAR',
                               Id_Usuario : $('#Id_Usuario').val(),
                               Id_Carrera : $('#Id_Carrera').val(),
                               clave : id_ss
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_Alumno_Mi_Servicio.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
//                            var finicio='';
                            if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                   $('#clave').val(value['id_ss']);
                                   $('#fecha_Inicio').val(value['fecha_inicio_ss']);
                                   $('#duracion').val(value['duracion_meses_ss']);
                                   $('#Tipo_Remuneracion').val(value['id_tipo_remuneracion']);
                                   $('#percepcion_Mensual').val(value['percepcion_mensual_ss']);
                                   $('#clave_Programa').val(value['id_programa']);
                                   $('#descripcion_Programa').html(value['descripcion_pss']);
                                   $('#descripcion_Programa').show();
                                   $('#numero_Creditos').val(value['avance_creditos_ss']);
                                   $('#porcentaje_Avance').val(value['avance_porcentaje_ss']);
                                   $('#promedio').val(value['promedio_ss']);
                                   $('#jefe_Inmediato').val(value['jefe_inmediato_ss']);
                               })
                               $('#ventanaProcesando').dialog('close');                          
                           }
                            else {
                                   $('#ventanaProcesando').dialog('close');
                                   $('#ventanaAviso').html(respuesta.data.message);
                                   $('#ventanaAvisos').dialog('open');                            
                            }                                                                   
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                    $('#ventanaProcesando').dialog('close');
                                    $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                    $('#ventanaAvisos').dialog('open');                            
                            });                                                                             
                }//fin Obtenemos el Servicio Social actual del Alumno                                                          

                $('#Tipo_Remuneracion').change(function(e){
                    e.preventDefault();
                    $('#percepcion_Mensual').val('0');
                    if($('#Tipo_Remuneracion').val() == 6 ){ //6. No Remunerado                        
                        $('#percepcion_Mensual').hide();
                        $('#lblpercepcion_Mensual').hide();                                                
                        $('#aviso_Tipo_Remuneracion').hide();
                        $('#aviso_Percepcion_Mensual').hide();
                    }
                    else{
                        $('#percepcion_Mensual').show();
                        $('#lblpercepcion_Mensual').show();
                    }
                    
                });
                
                //OBTENEMOS LOS SERVICIOS SOCIALES DEL ALUMNO
                function Obtener_Servicios_Del_Alumno(id_carrera){                                           
                    //Obtenemos los Servicios del Alumno
                    var datos = {Tipo_Movimiento : 'OBTENER',
                               Id_Usuario : $('#Id_Usuario').val(),
                               Id_Carrera : id_carrera,
                               clave : 0
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_Alumno_Mi_Servicio.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE style="width:100%;">';
                           html_table = html_table + '<TR><TH>Id_Servicio</TH>\n\
                                                <TH>Fecha de Inicio</TH>\n\
                                                <TH>Duracion (meses)</TH>\n\
                                                <TH>Estatus</TH>\n\
                                                <TH>Solicitud de Baja</TH>\n\
                                                <TH>Acción</TH></TR>';
                           
                           if (respuesta.success == true){
                               $('#Agregar_Servicio').prop("disabled",true);
                               var por_autorizar_admin =0;
                               var terminados = 0;
                               var aceptados = 0;
                               var bajas_por_administrador = 0;
                               
                               var $btn_Borrar ="";
                               var $btn_Editar="";
                               var $btn_EnviarDocs="";
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                    $btn_Borrar ="";
                                    $btn_Editar="";
                                    $btn_EnviarDocs="";

                                   if(value['id_estatus'] == 2){
                                       por_autorizar_admin += 1; 
                                   }
                                   if(value['id_estatus'] == 3){
                                       aceptados += 1; 
                                   }
                                   if(value['id_estatus'] == 8){
                                       terminados += 1; 
                                   }
                                   if(value['id_estatus'] == 15){
                                       bajas_por_administrador +=1;
                                   }

                                   //2. Por autorizar
                                   if (value['id_estatus'] != 8 && 
                                           value['id_estatus'] != 13 && 
                                           value['id_estatus'] != 14 &&
                                           value['id_estatus'] != 15){
                                        $btn_Borrar = '<button class="btn_Borrar btnOpcion" data-id_alumno=\'' + 
                                                $('#Id_Usuario').val() + '\' ' + 
                                                'data-id_SS=\'' + value['id_ss'] + 
                                                '\' data-id_carrera=\'' + id_carrera + 
                                                '\' data-docs_sin_enviar= ' + value['docs_sin_enviar'] + 
                                                '>Dar de Baja</button>';
                                   }
                                   $btn_Editar = '<button class="btn_Editar btnOpcion" data-id_alumno=\'' + $('#Id_Usuario').val() + '\' ' + 
                                            ' data-id_SS=\'' + value['id_ss'] + '\' data-id_carrera=' + id_carrera +
                                            ' data-id_estatus=' + value['id_estatus'] + '>Editar</button>';
                                    if (value['id_estatus'] != 15){  
                                        $btn_EnviarDocs = '<button class="btn_EnviarDocs btnOpcion" data-id_alumno=\'' + $('#Id_Usuario').val() + '\' ' + 
                                                 'data-id_SS=\'' + value['id_ss'] + '\' data-id_carrera=' + id_carrera +'>Enviar Docs</button>';
                                    } 
                                   html_table = html_table + '<TR>';
                                   html_table = html_table + '<TD>' + value['id_ss'] + '</TD>';
                                   html_table = html_table + '<TD style="text-align:left;">' + value['fecha_inicio_ss'] + '</TD>';
                                   html_table = html_table + '<TD>' + value['duracion_meses_ss'] + '</TD>';
                                   html_table = html_table + '<TD>' + value['descripcion_estatus'] + '</TD>';
                                   html_table = html_table + '<TD style="text-align:left;">' + esNulo(value['nota_baja']) + '</TD>';
                                   if(value['id_estatus'] == 14){
                                        html_table = html_table + '<TD style="text-align:right;"></TD>';
                                   }else{
                                        html_table = html_table + '<TD style="text-align:right;">' + $btn_Borrar + $btn_Editar + $btn_EnviarDocs + '</TD>';
                                   }
                                   
//                                   html_table = html_table + '<TD><button data-id_alumno=\'' + $('#Id_Usuario').val() + '\' ' + 
//                                            'data-id_SS=\'' + value['id_ss'] + '\' data-id_carrera=' + id_carrera +'>Borrar</button></TD>';
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Mis_Servicios').empty();
                               $('#tabla_Mis_Servicios').html(html_table);
                               
                                   if(por_autorizar_admin ==0 && aceptados == 0 && terminados == 0){ 
                                       $('#Agregar_Servicio').prop("disabled",false);
                                   }
                                   if(bajas_por_administrador > 3 )
                                   {
                                       $('#Agregar_Servicio').prop("disabled",true);
                                   }                                   
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="6">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Mis_Servicios').empty();
                               $('#tabla_Mis_Servicios').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros">';
                                html_table = html_table + '<TR><TH>Id_Servicio</TH><TH>Fecha de Inicio</TH><TH>Duracion (meses)</TH><TH>Estatus</TH><TH>Acción</TH></TR>';
                                html_table = html_table + '<TR><TD colspan="6">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Mis_Servicios').empty();
                                $('#tabla_Mis_Servicios').html(html_table);                                
                            });                                                                             
                }//fin Obtenemos los Servicios Sociales actuales del Alumno                                                          



                //OBTENEMOS LOS DOCUMENTOS ENVIADOS PARA AUTORIZAR EL SERVICIO SOCIAL
                function Obtener_Mis_Documentos_Enviados(id_ss){                                           
                    var datos = {Tipo_Movimiento : 'OBTENER_MIS_DOCUMENTOS',
                               Id_Usuario : $('#Id_Usuario').val(),
                               Id_Carrera : $('#Id_Carrera').val(),
                               clave : id_ss
                           };
                    $.ajax({
                       data : datos,
                       type : "POST",
                       dataType : "json",
                       url : "_Negocio/n_Alumno_Mi_Servicio.php"
                    })
                       .done(function(respuesta,textStatus,jqXHR){
                           var html_table = '<TABLE class="tabla_Registros"><CAPTION> Id_Servicio: ' + id_ss + '</CAPTION>';
                           html_table = html_table + '<TR><TH>Documento</TH><TH>Versión</TH><TH>Fecha Enviado</TH><TH>Estatus</TH><TH>Nota</TH><TH>Acción</TH></TR>';
                           if (respuesta.success == true){
                               //recorremos cada registro
                               $.each(respuesta.data.registros, function( key, value ) {
                                    $btn_EnviarDocs='';
                                    if (value['id_estatus']==1){
                                       $btn_EnviarDocs = '<button class="btn_Adjuntar btnOpcion" data-id_alumno =\'' + value['id_alumno'] + '\' data-id_documento=' + value['id_documento'] +  
                                            ' data-id_ss=\'' + value['id_ss'] + '\' data-id_estatus=' + value['id_estatus'] +
                                            ' data-desc_documento=\'' + value['descripcion_documento'] + 
                                            '\' data-desc_documento_corta = \'' + value['descripcion_para_nom_archivo'] + '\' data-id_version=' + value['id_version']  +
                                            '>Adjuntar Doc</button>';                                    
                                    }

                                   
                                   if (value['fecha_recepcion_doc']){
                                       $dato_fecha = '<TD>' + value['fecha_recepcion_doc'] + '</TD>';
                                   }
                                   else{
                                       $dato_fecha = '<TD></TD>';
                                   }
                                   if (value['nota']){
                                       $dato_nota = '<TD>' + value['nota'] + '</TD>';
                                   }
                                   else{
                                       $dato_nota = '<TD></TD>';
                                   }
                                   html_table = html_table + '<TR>';
                                   nom_file= value['id_alumno']+'_'+
                                           value['id_carrera']+'_'+
                                           value['id_ss']+'_'+
                                           value['id_version']+'_'+
                                           value['descripcion_para_nom_archivo']+'.pdf';
                                   fecha_=new Date();
                                   ruta_doc= ruta_docs_servicio_social+nom_file+'?'+ fecha_;

                                   html_table = html_table + '<TR>';
                                   if(value['id_estatus']==2 || value['id_estatus']==3){
                                       html_table = html_table + '<TD><a class="link_pdf" target="_blank" href="'+
                                               ruta_doc +'">' + value['descripcion_documento'] + '</a></TD>';
                                   }
                                   else{
                                       html_table = html_table + '<TD>' + value['descripcion_documento'] + '</TD>';
                                   }
                                   html_table = html_table + '<TD style="text-align:left;">' + value['id_version'] + '</TD>';
                                   html_table = html_table + $dato_fecha;
                                   html_table = html_table + '<TD>' + value['descripcion_estatus'] + '</TD>';
                                   html_table = html_table + $dato_nota;
                                   html_table = html_table + '<TD style="text-align:center;">' + $btn_EnviarDocs + '</TD>';                                   
                                   html_table = html_table + '</TR>';
                               });
                               html_table = html_table + '</TABLE>';
                               $('#tabla_Mis_Docs').empty();
                               $('#tabla_Mis_Docs').html(html_table);                                
                               }
                           else {
                               html_table = html_table + '<TR><TD colspan="6">' + respuesta.data.message + '</TD></TR>';
                               html_table = html_table + '</TABLE>'
                               $('#tabla_Mis_Docs').empty();
                               $('#tabla_Mis_Docs').html(html_table);
                           }
                       })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                var html_table = '<TABLE class="tabla_Registros"><CAPTION> Id_Servicio: ' + id_ss + '</CAPTION>';
                                html_table = html_table + '<TR><TH>Documento</TH><TH>Versión</TH><TH>Fecha Enviado</TH><TH>Estatus</TH><TH>Nota</TH><TH>Acción</TH></TR>';
                                html_table = html_table + '<TR><TD colspan="6">' + textStatus + '. ' + errorThrown + '</TD></TR>';
                                html_table = html_table + '</TABLE>';
                                $('#tabla_Mis_Docs').empty();
                                $('#tabla_Mis_Docs').html(html_table);                                
                            });                                                                             
                }//fin Obtenemos los Documentos Enviados para Autorizar el Servicio Social

                $('#ventanaServicioSocial').dialog({
                   buttons:[{
                            id:"btn_Guardar_ss", text:"Guardar", click: function() {
                            if (validaDatos()){
                                $('#ventanaConfirmacionAgregarSS').dialog('open');
                            }
                        }},{
                           id:"btn_Cancelar_ss", text:"Cerrar", click: function() {                            
                            $('#ventanaServicioSocial input[type=text]').each(function(){
                                $(this).val('');
                            });
                            $('#ventanaServicioSocial span').each(function(){
                                $(this).hide();
                            });

                            $(this).dialog('close');
                        }}],                                           
                   title: 'Servicio Social',
                   modal : true,
                   autoOpen : false,
//                   resizable : true,
                   draggable : true,
                   height : 'auto',
                   width : '600',
                   show : 'slide',
                   hide : 'slide',                   
                   dialogClass : 'no-close',
                   closeOnEscape : false,
                   position : {at: 'center top'},
                   open : function(){
                       if($('#id_Estatus_ss').val()==2 || $('#id_Estatus_ss').val()==0 ){
                            $("#btn_Guardar_ss").button("option", "disabled", false);
                        }
                        else{
                            $("#btn_Guardar_ss").button("option", "disabled", true);
                        }                            
                   },
                   close : function(){
                       $('#ventanaServicioSocial input[type=text]').each(function(){
                           $(this).val('');
                       })
                   }                    
                   
                });                            

                $('#ventanaConfirmacionAgregarSS').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $(this).dialog('close');
                            $('#ventanaProcesando').dialog('open');
                            // Por Ajax Agregamos el Servicio Social
                            var formDatos = $('#frm_SS').serialize();
                            console.log(">>> formDatos");
                            console.log(formDatos);
                            $.ajax({
                                data : formDatos,
                                type : "POST",
                                dataType : "json",
                                url : "_Negocio/n_Alumno_Mi_Servicio.php"
                            })
                                .done(function(respuesta,textStatus,jqXHR){
                                    $('#ventanaProcesando').dialog('close');
                                    if (respuesta.success == true){
                                        $('#ventanaServicioSocial input[type=text]').each(function(){
                                            $(this).val('');
                                        });
                                        $('#ventanaServicioSocial span').each(function(){
                                            $(this).hide();
                                        });

                                        $('#De_Alta_OK').val("1");
                                    }
                                    else {
                                        $('#De_Alta_OK').val("0");                            
                                    }                                    

                                    $('#ventanaAviso_Alta_OK').html(respuesta.data.message);
                                    $('#ventanaAvisos_Alta_OK').dialog('open');                                    
                                })
                                        .fail(function(jqXHR,textStatus,errorThrown){
                                            $('#De_Alta_OK').val("0"); 
                                            $('#ventanaProcesando').dialog('close');
                                            $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                            $('#ventanaAvisos').dialog('open');                            
                                        });                                                                                                              
                        },
                        "Cancelar" : function() {
                            $('#ventanaServicioSocial span').each(function(){
                                $(this).hide();
                            });
                            $(this).dialog('close');
                        }                       
                   },
                   title: 'Servicio Social',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                   dialogClass : 'no-close ventanaConfirmaUsuario',
                   closeOnEscape : false                      
                });

                $('#ventanaConfirmacionBorrar').dialog({
                   buttons:{
                        "Aceptar" : function() {
                                if(!$('#nota').val().match(miExpReg_Nota_Rechazo)){
                                    $('#ventanaAviso').html('En la Nota SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , . ; : ¿? ( ) - _ #');
                                    $('#ventanaAvisos').dialog('open');
                                }
                                else{ 
                                    $('#MensajeConfirma').text('Desea dar de Baja este Servicio Social ?');
                                    $('#ventanaConfirmacion').dialog({
                                        buttons:{
                                             "Aceptar" : function() {                            
                                                    $(this).dialog('close');
//                                                    $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
//                                                    $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                                                    $('#ventanaProcesando').dialog('open');
                                                    var nota = $('#nota').val();
                                                    nvo_id_estatus= $('#nvo_id_estatus').val();
                                                    nvo_id_baja = $('#nvo_id_baja').val();
                                                    id_usuario = $('#Id_Usuario').val();
                                                    tipo_mov = $('#Tipo_Movimiento').val();
                                                    clave = $('#Id_SS').val();
                                                    Id_Carrera = $('#Id_Carrera').val();

                                                    var datos ={Tipo_Movimiento : tipo_mov,
                                                            nvo_id_estatus : nvo_id_estatus,
                                                            nvo_id_baja : nvo_id_baja,
                                                            Id_Usuario : id_usuario,
                                                            nota : nota,
                                                            Id_Carrera : Id_Carrera,
                                                            clave : clave

                                                        };
                                                    $.ajax({
                                                        data : datos,
                                                        type : "POST",
                                                        dataType : "json",
                                                        url : "_Negocio/n_Alumno_Mi_Servicio.php"
                                                    })
                                                        .done(function(respuesta,textStatus,jqXHR){
                                                            $('#ventanaProcesando').dialog('close');
                                                            if (respuesta.success == true){
                                                                $('#ventanaConfirmacionBorrar').dialog('close');
                                                                $('#nota').val('');
                                                            }                                    
                                                            $('#ventanaAviso_Alta_OK').html(respuesta.data.message);
                                                            $('#ventanaAvisos_Alta_OK').dialog('open');
                                                        })
                                                                .fail(function(jqXHR,textStatus,errorThrown){
                                                                    $('#ventanaProcesando').dialog('close');
                                                                    $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                                                    $('#ventanaAvisos').dialog('open');                            
                                                                });
                                                    },
                                               "Cancelar" : function(){
                                                    $(this).dialog('close');
                                                 }
                                            },
                                            title: 'Confirmar la Baja',
                                            modal : true,
                                            autoOpen : true,
                                            resizable : true,
                                            draggable : true,
                                            dialogClass : 'no-close ventanaConfirmaUsuario',
                                            closeOnEscape : false                      
                                            
                                        });                                                                                
                                    }                                                                
                        },
                        "Cancelar" : function() {
                            $('#nota').val('');
                            $(this).dialog('close');
                        }                       
                   },
                   title: 'Dar de Baja Servicio Social',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                   show : 'slide',
                   hide : 'slide',
                   height : 'auto',
                   width : '450',                   
                   dialogClass : 'no-close',
                   closeOnEscape : false                      
                });

                $('#ventanaDocumentosEnviados').dialog({
                   buttons:{
                        "Cerrar" : function() {
                            Obtener_Servicios_Del_Alumno($('#Id_Carrera').val());
                            $(this).dialog('close');
                        }
                   },
                   title: 'Mis Documentos Enviados...',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                   height : '600',
                   width : '800',
                   show : 'slide',
                   hide : 'slide',
                   dialogClass : 'no-close',
                   closeOnEscape : false,
                   position : {at: 'center top'}
                });                            

                $('#ventanaSubirArchivo').dialog({
                    buttons:{
                        "Cerrar" : function() {
                            var docs_de_envio = $('#docs_de_envio').val();
                            if(docs_de_envio == 'SERVICIO_SOCIAL'){
                                Obtener_Mis_Documentos_Enviados($('#id_ss_doc').val());
                            }
                            else{
                                Obtener_Servicios_Del_Alumno($('#id_carrera_doc').val());
                            }
                                
                            $('#ventanaSubirArchivo input[type=file]').each(function(){
                                $(this).val('');
                            });
                            $('#ventanaSubirArchivo span').each(function(){
                                $(this).text('');
                            });
                            $('#message').empty();
                            $(this).dialog('close');
                            
                        }
                   },                    
                    title: 'Adjuntar documento',
                    modal : true,
                    autoOpen : false,
                    resizable :false,
                    draggable : true,   
                    width : '650',
                    height : 'auto',
                    dialogClass : 'no-close',
                    closeOnEscape : false,   
                    position : {at: 'center top'}
                });
                
                $('#Agregar_Servicio').click(function(e){
                    e.preventDefault();
                    $('#Tipo_Movimiento').val('AGREGAR');
                    habilitaControles();
                    $('#id_Estatus_ss').val(0);
                    $('#ventanaServicioSocial').dialog('open');
                });
                                
                $('#ventanaAvisos').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $(this).dialog('close');
                        }
                   },
                   title: 'Aviso',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                   dialogClass : 'no-close ventanaMensajes',
                   closeOnEscape : false
                });                            

                $('#ventanaAvisos_Alta_OK').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $(this).dialog('close');
                            if(($('#De_Alta_OK').val()==1)){
                                $('#ventanaServicioSocial').dialog('close');
                            }
                            Obtener_Servicios_Del_Alumno($('#Id_Carrera').val());
                        }
                   },
                   title: 'Aviso',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                   dialogClass : 'no-close ventanaMensajes',
                   closeOnEscape : false
                }); 
                
                $('#ventanaProcesando').dialog({
                   title: '',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : false,
                   dialogClass : 'no-close no-titlebar',
                   closeOnEscape : false
                });  

                function programaExterno(claveProg){
                    var posicion = -1;
                    var digitosPrograma = 0;
                    posicion = claveProg.indexOf('/');
                    if (posicion > -1){
                        digitosPrograma = claveProg.substring(posicion+1,posicion+4);
                        console.log('El valor del programa es : ' + digitosPrograma);

                        if(digitosPrograma == '081'){
                            return true;
                        }
                        else{
                            return false;
                        }
                        
                    }
                    else{
                        return false;
                    }
                }

                function esNulo(valor_){
                    if(valor_ == null){
                        return '';
                    }
                    else
                    {
                        return valor_;
                    }                            
                }

                function f5(that,val){
                    if(val)
                    {
                        that.on("keydown",function(e){
                            var code = (e.keyCode ? e.keyCode : e.which);
                            if(code == 116 || code == 8) {
                                e.preventDefault();
                            }
                        })
                    }
                    else
                    {
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
                Obtener_Carreras_Del_Alumno();
                $('#ventanaSubirArchivo').hide();
                $('#ventanaSolicitarBaja').hide();
                
            });
                        
        </script>
        
<!--    </head>
    <body>
        <header>
            Mi Pefil
        </header>-->
    <div>
        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                    <p>Mi Servicio Social</p>
                </div>
                <div class="barra_Herramientas">
                    <input type="button" id="Agregar_Servicio" name="Agregar_Servicio" value="Agregar" class="btn_Herramientas"/>
                </div>
            </div>
            <div class="barra_Parametros">
                <label for="selec_Mis_Carreras" class="etiqueta_Parametro">Mi Carrera:</label>
                <select id="selec_Mis_Carreras" name="selec_Mis_Carreras" class="combo_Parametro">                            
                </select>
            </div>            
            <div>                            
                <div id="tabla_Mis_Servicios" class="tabla_Registros">
                </div>
            </div>
        </div>
    </div><!--fin contenido_Form-->
    <div id='ventanaServicioSocial' name="ventanaServicioSocial" class="contenido_Formulario">    
            <form id="frm_SS" name="frm_SS" method="" action="" autocomplete="off">
                <p>
                    <label for="clave" class="label">Id_Servicio:</label>
                    <input type="text" name="clave" id="clave" value="" readonly/>
                    <span id="aviso_Clave" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                </p>    
                <p>
                    <label for="fecha_Inicio" class="label">Fecha de Inicio:</label>
                    <input type="text" name="fecha_Inicio" id="fecha_Inicio" readonly/>                    
                    <span id="aviso_Fecha_Inicio" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                </p>
                <p>
                    <label for="duracion" class="label">Duración (meses):</label>
                    <select name="duracion" id="duracion">
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="12">13</option>
                        <option value="12">14</option>
                        <option value="12">15</option>
                        <option value="12">16</option>
                        <option value="12">17</option>
                        <option value="12">18</option>
                        <option value="12">19</option>
                        <option value="12">20</option>
                        <option value="12">21</option>
                        <option value="12">22</option>
                        <option value="12">23</option>
                        <option value="12">24</option>                        
                    </select>
                    <span id="aviso_Duracion" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                </p>
                <p>
                    <label for="Tipo_Remuneracion" class="label">Tipo de Apoyo:</label>
                    <select name="Tipo_Remuneracion" id="Tipo_Remuneracion">
                    </select>
                    <span id="aviso_Tipo_Remuneracion" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                </p>

                <p>
                    <label for="percepcion_Mensual" id="lblpercepcion_Mensual" class="label">Percepción mensual:</label>
                    <input type="text" name="percepcion_Mensual" id="percepcion_Mensual" maxlength="10" placeholder="1500.00"
                               title="Capture únicamente números y punto, sin espacios" autocomplete="off" class="entrada_Dato"/>                    
                    <span id="aviso_Percepcion_Mensual" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                </p>
                <p>
                    <label for="clave_Programa" class="label">Clave del programa:</label>
                    <input type="text" name="clave_Programa" id="clave_Programa" maxlength="18" placeholder="2015-10/1-400"
                               title="Capture la Clave del Programa TAL Y COMO ESTÁ EN LA PÁGINA DGOSE, sin espacios" 
                               autocomplete="off" class="entrada_Dato"/>
                    <div id="cargandoAjax" class="notificacion">
                        <span><img src="css/images/ajax-loader03.gif"/>Espere. Verificando si esta Clave de Programa existe!</span>
                    </div>     
                    <div class="notificacion">
                        <span id="descripcion_Programa" class="dato_Invalido"></span>
                        <span id="aviso_Clave_Programa" class="dato_Invalido"></span>
                    </div>                                    
                </p>
                <p>
                    <label for="numero_Creditos" class="label">Número de creditos TOTALES de avance:</label>
                    <input type="text" name="numero_Creditos" id="numero_Creditos" maxlength="3" placeholder="368"
                           title="Capture únicamente números" autocomplete="off" class="entrada_Dato"/> 
                    <span id="aviso_Numero_Creditos" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                </p>
                <p>
                    <label for="porcentaje_Avance" class="label">Porcentaje de avance en creditos TOTALES:</label>
                    <input type="text" name="porcentaje_Avance" id="porcentaje_Avance" maxlength="6" placeholder="95.33"
                           title="Capture únicamente números y punto" autocomplete="off" class="entrada_Dato"/>                                        
                    <span id="aviso_Porcentaje_Avance" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                </p>                
                <p>
                    <label for="promedio" class="label">Promedio:</label>
                    <input type="text" name="promedio" id="promedio" maxlength="5" placeholder="8.72"
                           title="Capture únicamente números y punto" autocomplete="off" class="entrada_Dato"/>                                        
                    <span id="aviso_Promedio" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                </p>                
                <p>
                    <label for="jefe_Inmediato" class="label">Nombre del Jefe inmediato:</label>
                    <input type="text" name="jefe_Inmediato" id="jefe_Inmediato" maxlength="50" placeholder="LIC. ANGÉLICA GUTIÉRREZ VÁZQUEZ"
                           title="Capture únicamente letras" autocomplete="off" class="entrada_Dato" 
                           style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                    <span id="aviso_Jefe_Inmediato" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                </p>                

                <input type="hidden" id="Id_SS" name="Id_SS" value=""> 
                <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value=""> 
                <input type="hidden" id="Id_Carrera" name="Id_Carrera" value="">
                <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
                <input type="hidden" id="Id_Division" name="Id_Division" value="<?php echo $_SESSION['id_division']; ?>">
                <input type="hidden" id="De_Alta_OK" name="De_Alta_OK" value="">
                <input type="hidden" id="id_Estatus_ss" name="id_Estatus_ss" value="0">
                <input type="hidden" id="docs_sin_enviar" name="docs_sin_enviar" value="0">
                <input type="hidden" id="nvo_id_baja" name="nvo_id_baja" value="0">
                <input type="hidden" id="nvo_id_estatus" name="nvo_id_estatus" value="0">
            </form>
        </div>

        <div id='ventanaDocumentosEnviados'>
            <div id="contenido_Mis_Docs">
                <div id="tabla_Mis_Docs">
                </div>
            </div>
        </div>
        <div id="ventanaSubirArchivo">
            <div id="contenido_Subir_Archivo">
                <form action="" method="post" enctype="multipart/form-data" id="frmSubirPDF" name="frmSubirPDF">
                    <p>
                        <label data-id_ss='x' data-id_documento="0" data-id_version="0" id='lblTitulo_SubirArchivo'for= "archivoPDF">Su archivo PDF (máx. 200 KB) :</label>
                        <div style="display: inline-block;">
                            <input type="file" name="file" id="file" accept=".pdf" required class="tag_file"> 
                            <input type="submit" name="enviarArchivo" id="enviarArchivo" value="Enviar" class="btn_Herramientas">
                            <input type="hidden" id="id_ss_doc" name="id_ss_doc" value="">
                            <input type="hidden" id="id_documento_doc" name="id_documento_doc" value="">
                            <input type="hidden" id="id_version_doc" name="id_version_doc" value="">
                            <input type="hidden" id="id_usuario_doc" name="id_usuario_doc" value="">
                            <input type="hidden" id="desc_corta_doc" name="desc_corta_doc" value="">
                            <input type="hidden" id="id_carrera_doc" name="id_carrera_doc" value="">
                            <input type="hidden" id="nvo_id_estatus_doc" name="nvo_id_estatus_doc" value="">
                            <input type="hidden" id="nvo_id_baja_doc" name="nvo_id_baja_doc" value="">
                            <input type="hidden" id="docs_de_envio" name="docs_de_envio" value="">
                            
                        </div>   
                    </p>
                </form>
                <div id='loading' class="resultado_Carga_De_Archivo"><h1>Cargando el Archivo...</h1></div>
                
                <div id="message" class="informacion_Archivo_A_Cargar"></div>
                
            </div>            
        </div>
        <div id='ventanaConfirmacionAgregarSS'>                
        </div>
        <div id='ventanaConfirmacionBorrar'>
            <span style="font-weight: bold; margin-top: 5px">Debe de indicar los Motivos por los que desea dar de baja este Servicio Social.</span>
            <textarea id="nota" class="notaVoBo entrada_Dato" style="margin-top: 5px;"
                      maxlength="500" placeholder="" onkeyup="javascript:this.value=this.value.toUpperCase();"
                title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
        </div>
<!--        <div id='ventanaSolicitarBaja'>
                <form action="" method="post" enctype="multipart/form-data" id="frmSubirBaja" name="frmSubirBja">
                    <p>
                        <label id='lblTitulo_SubirBaja'></label>
                        <div style="display: inline-block;">
                            <input type="file" name="fileSolicitudBaja" id="fileSolicitudBaja" accept=".pdf" required class="tag_file"> 
                            <input type="submit" name="enviarSolicitudBaja" id="enviarSolicitudBaja" value="Enviar" class="btn_Herramientas">
                            <input type="hidden" id="id_ss_baja_doc" name="id_ss_baja_doc" value="">
                            <input type="hidden" id="id_documento_baja_doc" name="id_documento_baja_doc" value="">
                            <input type="hidden" id="id_usuario_baja_doc" name="id_usuario_baja_doc" value="">
                            <input type="hidden" id="desc_corta_baja_doc" name="desc_corta_baja_doc" value="">
                            <input type="hidden" id="id_carrera_baja_doc" name="id_carrera_baja_doc" value="">                            
                        </div>   
                    </p>
                </form>
                <div id='loading' class="resultado_Carga_De_Archivo"><h1>Cargando el Archivo...</h1></div>
                
                <div id="message" class="informacion_Archivo_A_Cargar"></div>
            
            <span style="font-weight: bold; margin-top: 5px">Debe de indicar los Motivos por los que Solicita la baja este Servicio Social.</span>
            <textarea id="nota_Solicitud_Baja" class="notaVoBo entrada_Dato" style="margin-top: 5px;"
                      maxlength="500" placeholder="" onkeyup="javascript:this.value=this.value.toUpperCase();"
                title="SOLO puede Capturar los siguientes carácteres: A-Z 0-9 , ; : ¿? ( )' - _ #" autocomplete="off"></textarea>
        </div>-->
    
        <div id="ventanaConfirmacion">
            <span id="MensajeConfirma"></span>
        </div>
    
        <div id="ventanaAvisos">
            <span id="ventanaAviso"></span>
        </div>
        <div id="ventanaAvisos_Alta_OK">
            <span id="ventanaAviso_Alta_OK"></span>
        </div>
        <div id="ventanaProcesando" data-role="header">
            <img id="cargador" src="css/images/engrane2.gif"/><br>
            Procesando su transacción....!<br>
            Espere por favor.
        </div>
<!--    </body>
</html>-->

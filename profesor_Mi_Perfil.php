
<!DOCTYPE html>
<!--
Fecha:          Julio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para modificar los datos del Profesor
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
            $( document ).ready(function() {
                
                //Llenamos los catálogos
                function llena_Catalogo(nom_control, tipo_movimiento, tabla_catalogo, tabla_campos, 
                        tabla_where, tabla_orderby){

                    var datos = {Tipo_Movimiento : tipo_movimiento, 
                            tabla_Catalogo  : tabla_catalogo,
                            tabla_Campos    : tabla_campos,
                            tabla_Where     : tabla_where,
                            tabla_OrderBy   : tabla_orderby
                            };
                    $.ajax({
                        data : datos,
                        type : 'POST',
                        dataType : 'json',                        
                        url : '_Negocio/n_administrador_Crear_Nueva_Cuenta.php'
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
                                $('#' + nom_control).empty();
                                $('#' + nom_control).html(html_options);
                                
                                $('#' + nom_control + ' option:first-child').attr('selected','selected');
                            }
                            else {
                                $('#ventanaAviso').html(respuesta.data.message);
                                $('#ventanaAvisos').dialog('open');                                                                    
                            }
                        })
                                .fail(function(jqXHR,textStatus,errorThrown){
                                    $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                                    $('#ventanaAvisos').dialog('open');                            
                                });                            
                }     
                //FIN LLENADO DE CATALOGO                
                                     

                //OBTENEMOS LOS DATOS ACTUALES DEL PROFESOR
                function Obtener_Mi_Perfil(){
                var datos = {Tipo_Movimiento : 'OBTENER_USUARIO',
                            id_tipo_usuario : 4, //PROFESOR
                            id_usuario : $('#Id_Usuario').val()}
                $.ajax({
                    data : datos,
                    type : "POST",
                    dataType : "json",
                    url : "_Negocio/n_administrador_Crear_Nueva_Cuenta.php"
                })
                    .done(function(respuesta,textStatus,jqXHR){
                        if (respuesta.success == true){
                            //recorremos cada usuario
                            $.each(respuesta.data.registros, function( key, value ) {
                                //recorremos los valores de cada usuario
                                $('#es_externo').prop('checked', value['es_externo']==0 ? false : true);
                                $('#nombre').val(value['nombre_usuario']);
                                $('#apellido_Paterno').val(value['apellido_paterno_usuario']);
                                $('#apellido_Materno').val(value['apellido_materno_usuario']);
                                $('#telefono_Fijo').val(value['telefono_fijo_profesor']);
                                $('#telefono_Extension').val(value['telefono_extension_profesor']);
                                $('#celular').val(value['telefono_celular_profesor']);
                                $('#correo_Electronico').val(value['email_usuario']);
                                $('#fecha_alta').val(value['fecha_alta_usuario']);
                                
                                $('#calle_Numero').val(value['calle_numero_profesor']);
                                $('#colonia').val(value['colonia_profesor']);
                                $('#delegacion_Municipio').val(value['delegacion_municipio_profesor']);
                                $('#codigo_Postal').val(value['codigo_postal_profesor']);
                                
                                $('#RFC').val(value['rfc_profesor']);
                                $('#CURP').val(value['curp_profesor']);
                                $('#dependencia_laboral').val(value['dependencia_laboral_profesor']);
                                $('#anio_Ingreso_FI').val(value['fecha_ingreso_fi_profesor']);
                                
                                $('#estado').val(value['id_estado_republica']).change();
                                $('#genero').val(value['id_genero']).change();
                                $('#grado_educativo').val(value['id_grado_estudio']).change();
                            });
                        }
                        else {
                            $('#ventanaAviso').html(respuesta.data.message);
                            $('#ventanaAvisos').dialog('open');                                                                    
                        }
                    })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                                $('#ventanaAvisos').dialog('open');                            
                            }); 
                 }                                     
                //fin Obtenemos los datos actuales del Profesor
                
                //Array para dar formato en español al datepicker
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
                        initStatus: 'Selecciona la fecha', isRTL: false
                    }; 
                $.datepicker.setDefaults($.datepicker.regional['es']); 

                //TAG TIPO FECHA
                $('#anio_Ingreso_FI').datepicker({
                    changeYear : true,
                    changeMonth : true,
                    yearRange : '1920:2050',
                    dialogClass : 'no-close',
                    onSelect : function(date){
                        $("#anio_Ingreso_FI ~ .ui-datepicker").hide();
                    }                    
                });
               
                //VALIDAMOS LOS DATOS DE LA FORM
                 function validaDatos(){
                    $('#btn_Guardar').prop('disable',true);
                    var datosValidos = true;
                    var nombre = $('#nombre').val();
                    var apellidoPaterno = $('#apellido_Paterno').val();
                    var apellidoMaterno = $('#apellido_Materno').val();
                    
                    var telefonoFijo = $('#telefono_Fijo').val();
                    var telefonoExtension  = $('#telefono_Extension').val();

                    var telefonoCelular = $('#celular').val();
                    var correoElectronico = $('#correo_Electronico').val();
                    var calleNumero = $('#calle_Numero').val();
                    var colonia = $('#colonia').val();
                    var delegacionMunicipio = $('#delegacion_Municipio').val();
                    var codigoPostal = $('#codigo_Postal').val();
                    
                    var RFC = $('#RFC').val();
                    var CURP = $('#CURP').val();
                    var dependencia_laboral = $('#dependencia_laboral').val();
                    var anioIngreso = $('#anio_Ingreso_FI').val();
                    
                    $('#aviso_Nombre').hide();
                    $('#aviso_Apellido_Paterno').hide();
                    $('#aviso_Apellido_Materno').hide();
                    $('#aviso_Telefono_Fijo').hide();
                    $('#aviso_Telefono_Extension').hide();
                    $('#aviso_Celular').hide();
                    $('#aviso_Correo_Electronico').hide();
                    $('#aviso_Calle_Numero').hide();
                    $('#aviso_Colonia').hide();                    
                    $('#aviso_Delegacion_Municipio').hide();
                    $('#aviso_Codigo_Postal').hide();
                    
                    $('#aviso_RFC').hide();
                    $('#aviso_CURP').hide();
                    $('#aviso_dependencia_laboral').hide();
                    $('#aviso_Anio_Ingreso_FI').hide();

//                    var miExpReg = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ]{1,50}$/;
//                    var miExpReg_RFC = /^[a-zA-Z0-9]{1,13}$/;
//                    var miExpReg_CURP = /^[a-zA-Z0-9]{1,18}$/;
//                    var miExpReg_Mail = /^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(.[a-zA-Z0-9-]+)*(.[a-zA-Z]{2,4})$/;
//                    var miExpReg_CP = /^[0-9]{5}$/;
//                    var miExpReg_DependenciaLaboral = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ]{1,100}$/;
//		    var miExpReg_Telefono_Letras = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9\.]{1,20}$/;
                    
//                    var miExpReg_Extension = /^[0-9]{1,5}$/;
//		    var miExpReg_Telefono_Celular = /^[0-9]{10}$/; 
//                    var miExpReg_Direccion = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9\#\.\.]{1,100}$/;

                    if (!nombre.match(miExpReg_Nombre))
                    {
                        $('#aviso_Nombre').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Nombre').hide();
                    }
                    
                    if (!apellidoPaterno.match(miExpReg_Nombre))
                    {
                        $('#aviso_Apellido_Paterno').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Apellido_Paterno').hide();
                    }
                    
                    if (!apellidoMaterno.match(miExpReg_Nombre))
                    {
                        $('#aviso_Apellido_Materno').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Apellido_Materno').hide();
                    }
                    
                    if (!telefonoFijo.match(miExpReg_Telefono_Letras))
                    {
                        $('#aviso_Telefono_Fijo').show();
                        datosValidos = false;
                    }   
                    else{
                        $('#aviso_Telefono_Fijo').hide();
                    }
                    
                    if (!telefonoExtension.match(miExpReg_Extension))
                    {
                        $('#aviso_Telefono_Extension').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Telefono_Extension').hide();
                    }
                    
                    if (!telefonoCelular.match(miExpReg_Telefono_Celular))
                    {
                        $('#aviso_Celular').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Celular').hide();
                    }
                    if (!correoElectronico.match(miExpReg_Mail))
                    {
                        $('#aviso_Correo_Electronico').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Correo_Electronico').hide();
                    }
                    if (!calleNumero.match(miExpReg_Direccion))
                    {
                        $('#aviso_Calle_Numero').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Calle_Numero').hide();
                    }
                    if (!colonia.match(miExpReg_Direccion))
                    {
                        $('#aviso_Colonia').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Colonia').hide();
                    }
                    if (!delegacionMunicipio.match(miExpReg_Direccion))
                    {
                        $('#aviso_Delegacion_Municipio').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Delegacion_Municipio').hide();
                    }
                    if (!codigoPostal.match(miExpReg_CP))
                    {
                        $('#aviso_Codigo_Postal').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Codigo_Postal').hide();
                    }
                    if (!RFC.match(miExpReg_RFC))
                    {
                        $('#aviso_RFC').show();
                        datosValidos = false;
                    }   
                    else{
                        $('#aviso_RFC').hide();
                    }
                    if (!CURP.match(miExpReg_CURP))
                    {
                        $('#aviso_CURP').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_CURP').hide();
                    }
                    if (!dependencia_laboral.match(miExpReg_Direccion))
                    {
                        $('#aviso_dependencia_laboral').show();
                        datosValidos = false;
                    }   
                    else{
                        $('#aviso_dependencia_laboral').hide();
                    }
                    
                    if (anioIngreso =='')
                    {
                        $('#aviso_Anio_Ingreso_FI').show();
                        datosValidos = false;
                    }                    

                    if (nombre =='' || apellidoPaterno =='' || apellidoMaterno =='' || telefonoFijo =='' || 
                         telefonoExtension =='' || telefonoCelular=='' 
                         || correoElectronico =='' || calleNumero =='' || colonia ==''|| delegacionMunicipio =='' 
                         || codigoPostal =='' || RFC =='' || CURP =='' || 
                         dependencia_laboral =='' || anioIngreso =='')
                    {
                        $('#ventanaAviso').html('Debe capturar TODA la información Requerida.');
                        $('#ventanaAvisos').dialog('open');
                        
                        datosValidos = false;
                    }
                    
                    $('#btn_Guardar').prop('disable',false);
                    return datosValidos;
                };
               
                $('#btn_Guardar').on('click',function(event){
                    event.preventDefault();
                    if (validaDatos())
                    {  
                       $('#Tipo_Movimiento').val('ACTUALIZAR');
                       $('#ventanaConfirmacion').dialog('open');
                    }
                    else
                    {
                       return false; 
                    }                          
                });

                function oculta_StatusControl(){
                    $('#statusTipo_Usuario').hide();
                    $('#statusNombre').hide();
                    $('#statusApellidoPaterno').hide();
                    $('#statusApellidoMaterno').hide();
                    $('#statusFechaNacimiento').hide();
                    $('#statusGenero').hide();
                    $('#statusPuesto').hide();
                    $('#statusCoordinacion').hide();
                    $('#statusCorreo').hide();
                    $('#statusClave').hide();                    
                    $('#statusContrasena').hide();                    
                }
                
                $('#ventanaConfirmacion').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $(this).dialog('close');
                            $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
                            $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                            $('#ventanaProcesando').dialog('open');
                            
                            // Por Ajax actualizamos los datos del Usuario               
                            var formDatos = $('#Profesor_Mi_Perfil').serialize();
                            
                            $.ajax({
                                data : formDatos,
                                type : "POST",
                                dataType : "json",
                                url : "_Negocio/n_administrador_Crear_Nueva_Cuenta.php"
                            })
                                .done(function(respuesta,textStatus,jqXHR){
                                    if(respuesta.success == true){
                                        $('#ventanaProcesando').dialog('close');
                                        $('#ventanaAviso').html(respuesta.data.message);
                                        $('#ventanaAvisos').dialog('open');
                                        oculta_StatusControl();
                                    }
                                    else{
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
                        },
                        "Cancelar" : function() {
                            $(this).dialog('close');
                            $(':text:first').focus();
                        }                       
                   },
                   title: 'Mi Perfil',
                   modal : true,
                   autoOpen : false,
                   resizable : true,
                   draggable : true,
                   dialogClass : 'no-close ventanaConfirmaUsuario',
                   closeOnEscape : false   
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
        
                $('#ventanaProcesando').dialog({
                   title: 'Procesando...',
                   modal : true,
                   autoOpen : false,
                   resizable : false,
                   draggable : false,
                   dialogClass : 'no-close no-titlebar',
                   closeOnEscape : false
//                   show : 'slideDown',
//                   hide: 'slideUp',
//                   dialogClass : 'ui-state-highlight'
                });  

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
                /*
                $('.entrada_Dato').focus(function(e){
                    e.preventDefault();
                    f5($(document),false);
                });
                $('.entrada_Dato').blur(function(e){
                    e.preventDefault();
                    f5($(document),true);
                });*/

                llena_Catalogo('estado', 'CATALOGO_GENERALES', 'estados_republica', 
                'id_estado_republica as id, descripcion_estado_republica as descripcion', 
                '', 'descripcion_estado_republica'); 
                
                llena_Catalogo('grado_educativo', 'CATALOGO_GENERALES', 'grados_estudio', 
                'id_grado_estudio as id, descripcion_grado_estudio as descripcion', 
                '', 'descripcion_grado_estudio');

                llena_Catalogo('categoria', 'CATALOGO_GENERALES', 'categorias', 'id_categoria as id, descripcion_categoria as descripcion','', 'descripcion_categoria');

                llena_Catalogo('division', 'CATALOGO_GENERALES', 'division_categoria','id_div_cat as id, id_division as descripcion','','id_division');

                //f5($(document),true);
                    
                Obtener_Mi_Perfil();
                
                $(':text:first').focus();

                $( "#tabs" ).tabs();
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
                    <p>Mi Perfil</p>
                </div>
                <div class="barra_Herramientas">
                    <input type="submit" id="btn_Guardar" name="btn_Guardar" value="Guardar" class="btn_Herramientas"/>
                </div>
            </div>
            <form name="Profesor_Mi_Perfil" id="Profesor_Mi_Perfil" method="" action="">
            <div class="contenido_Formulario">
                <div id="tabs">
                    <ul>
                      <li><a href="#tabs-1">Generales</a></li>
                      <li><a href="#tabs-2">Dirección</a></li>
                      <li><a href="#tabs-3">Laboral</a></li>
                    </ul>

                    <div id="tabs-1">
                        <p>
                            <label for="nombre" class="label">Nombre:</label>
                            <input type="text" name="nombre" id="nombre" value='' maxlength="50"
                               title="Capture únicamente letras en MAYÚSCULA" autocomplete="off" 
                               style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                            <span id="aviso_Nombre" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p> 
                        <p>
                            <label for="apellido_Paterno" class="label">Apellido Paterno:</label>
                            <input type="text" name="apellido_Paterno" id="apellido_Paterno" value='' maxlength="50"
                               title="Capture únicamente letras en MAYÚSCULA" autocomplete="off" 
                               style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                            <span id="aviso_Apellido_Paterno" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p> 
                        <p>
                            <label for="apellido_Materno" class="label">Apellido Materno:</label>
                            <input type="text" name="apellido_Materno" id="apellido_Materno" value='' maxlength="50"
                               title="Capture únicamente letras en MAYÚSCULA" autocomplete="off" 
                               style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                            <span id="aviso_Apellido_Materno" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p> 
                        <p>
                            <label for="grado_educativo" class="label">Grado Educativo:</label>
                            <select name="grado_educativo" id="grado_educativo">
                            </select>
                        </p>                                                                                                 

                        <p>
                            <label for="genero" class="label">Género:</label>
                            <select name="genero" id="genero">
                                <option value="2">Femenino</option>
                                <option value="1">Masculino</option>
                            </select>
                            <span id="aviso_Genero" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>                                                                                                 
                        <p>
                            <label for="telefono_Fijo" class="label">Teléfono Fijo:</label>
                            <input type="text" name="telefono_Fijo" id="telefono_Fijo" value='' maxlength="20"
                               title="Capture solamente letras y números" autocomplete="off" placeholder="55 555 555" 
                               style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                            <span id="aviso_Telefono_Fijo" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p> 
                        <p>
                            <label for="telefono_Extension" class="label">Ext.:</label>
                            <input type="text" name="telefono_Extension" id="telefono_Extension" value='' maxlength="5"
                               title="Capture solamente números" autocomplete="off" placeholder="99999" class="entrada_Dato"/>
                            <span id="aviso_Telefono_Extension" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p> 

                        <p>
                            <label for="celular" class="label">Celular:</label>
                            <input type="text" name="celular" id="celular" value='' maxlength="15"
                            title="Capture solamente números" autocomplete="off" placeholder="5512131055" class="entrada_Dato"/>
                            <span id="aviso_Celular" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p> 
                        <p>
                            <label for="correo_Electronico" class="label">e-mail:</label>
                            <input type="text" name="correo_Electronico" id="correo_Electronico" maxlength="100" placeholder="miCorreo@dominio.com"
                            title="Capture su dirección de correo TAL Y COMO LA DIÓ DE ALTA CON SU PROVEEDOR" autocomplete="off" class="entrada_Dato"/>
                            <span id="aviso_Correo_Electronico" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p> 
                        <p>
                            <label for="fecha_alta" class="label">Fecha de Alta:</label>
                            <input type="text" name="fecha_alta" id="fecha_alta" placeholder="dd-mm-aaaa" readonly/>
                            <span id="aviso_Fecha_Alta" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>                 
                    </div>

                    <div id="tabs-2">
                        <p>
                            <label for="calle_Numero" class="label">Calle y No.:</label>
                            <input type="text" name="calle_Numero" id="calle_Numero" value='' maxlength="100" placeholder="AV. UNIVERSIDAD #3000"
                            title="Capture solamente letras y números" autocomplete="off" 
                            style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                            <span id="aviso_Calle_Numero" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>
                        <p>
                            <label for="colonia" class="label">Colonia:</label>
                            <input type="text" name="colonia" id="colonia" value='' maxlength="100" placeholder="CIUDAD UNIVERSITARIA"
                            title="Capture solamente letras y números" autocomplete="off" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                            <span id="aviso_Colonia" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>
                        <p>
                            <label for="delegacion_Municipio" class="label">Delegación / Municipio:</label>
                            <input type="text" name="delegacion_Municipio" id="delegacion_Municipio" value='' maxlength="100" placeholder="CD.DE MÉXICO"
                            title="Capture solamente letras" autocomplete="off" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                            <span id="aviso_Delegacion_Municipio" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>
                        <p>
                            <label for="codigo_Postal" class="label">Código Postal:</label>
                            <input type="text" name="codigo_Postal" id="codigo_Postal" value='' maxlength="5" placeholder="04510"
                            title="Capture solamente números" autocomplete="off" class="entrada_Dato"/>
                            <span id="aviso_Codigo_Postal" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>
                        <p>
                            <label for="estado" class="label">Estado:</label>
                            <select name="estado" id="estado" class="select">
                            </select>
                            <span id="aviso_Estado" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p> 
                    </div>

                    <div id="tabs-3">
                        <p>
                            <label for="RFC" class="label">R.F.C. (con homoclave):</label>
                            <input type="text" name="RFC" id="RFC" value='' maxlength="13" placeholder="REMR701010B99"
                            title="Capture solo letras y números" autocomplete="off" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                            <span id="aviso_RFC" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>                                
                        <p>
                            <label for="CURP" class="label">C.U.R.P.:</label>
                            <input type="text" name="CURP" id="CURP" value='' maxlength="18" placeholder="REMR701010HCMLNS09"
                            title="Capture solo letras y números" autocomplete="off" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                            <span id="aviso_CURP" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p> 
                        <p>
                            <label for="dependencia_laboral" class="label">Dependencia donde labora:</label>
                            <input type="text" name="dependencia_laboral" id="dependencia_laboral" value='' maxlength="100" placeholder=""
                            title="Capture solo letras y números" autocomplete="off" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                            <span id="aviso_dependencia_laboral" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>                                                                
                        <p>
                            <label for="anio_Ingreso_FI" class="label">Fecha de Ingreso como profesor a la FI:</label>
                            <input type="text" name="anio_Ingreso_FI" id="anio_Ingreso_FI" value='' title="dd/mm/aaaa" readonly />
                            <span id="aviso_Anio_Ingreso_FI" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>
                        <p>
                            <label for="es_externo" class="label profesor">Es externo a la FI:</label>
                            <input type="checkbox"  class="profesor" name="es_externo" id="es_externo"/>                    
                        </p>                            
                        <p>
                            <label class="label">Categoría y división:</label>
                            <select name="categoria" id="categoria" style="width:220px">
                            </select>
                            <select name="division" id="division" style="width:220px">
                            </select>
                        </p>
                    </div>
                </div> <!-- fin tabs -->                    
                <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
                <input type="hidden" id="id_Tipo_Usuario" name="id_Tipo_Usuario" value="4">
                <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="">
           </form>
        </div>

        <div id='ventanaConfirmacion'>
                Desea Actualizar su información ?
        </div>
        
        <div id="ventanaAvisos">
            <span id="ventanaAviso"></span>
        </div>
        <div id="ventanaProcesando" data-role="header">
            <img id="cargador" src="css/images/engrane2.gif"/><br>
            Procesando su transacción....!<br>
            Espere por favor.
        </div>
        
<!--    </body>
</html>-->


<!DOCTYPE html>
<!--
Fecha:          Mayo,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para la captura de datos del Alumno
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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<!--        <script src="js/jquery-1.12.4.min.js"></script>
        <script src="js/jquery-ui.min.js"></script>
        <link href="css/jquery-ui.css" rel="stylesheet"> 
        <link href="css/home.css" rel="stylesheet"> -->
        <script src="js/expresiones_reg.js"></script>
        
        <script>
            $( document ).ready(function() {
                //Llenamos los catálogos
                //entidades_federativas
                var datos = {tabla_Catalogo : 'estados_republica',
                            tabla_Campos : 'id_estado_republica as id, descripcion_estado_republica as descripcion'
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
                            $('#estado').empty();
                            $('#estado').html(html_options);
                        }
                        else {
                            $('#Actualizar_Mi_Pefil_Alumno').prop('disable',true);
                            $('#ventanaAviso').html(respuesta.data.message);
                            $('#ventanaAvisos').dialog('open');                                                                    
                        }
                    })
                            .fail(function(jqXHR,textStatus,errorThrown){
                                $('#ventanaProcesando').dialog('close');
                                $('#ventanaAviso').html('La solicitud ha fallado.<br>' + textStatus + '. ' + errorThrown);
                                $('#ventanaAvisos').dialog('open');                            
                            });                 


                //Obtenemos los datos actuales del Alumno
                var datos = {Tipo_Movimiento : 'OBTENER_DATOS',
                            Id_Tipo_Usuario : 5,
                            Id_Usuario : $('#Id_Usuario').val()}
                $.ajax({
                    data : datos,
                    type : "POST",
                    dataType : "json",
                    url : "_Negocio/n_Usuario.php"
                })
                    .done(function(respuesta,textStatus,jqXHR){
                        if (respuesta.success == true){
                            //recorremos cada usuario
                            $.each(respuesta.data.registros, function( key, value ) {
                                //recorremos los valores de cada usuario
                                $('#nombre').val(value['nombre_usuario']);
                                $('#apellido_Paterno').val(value['apellido_paterno_usuario']);
                                $('#apellido_Materno').val(value['apellido_materno_usuario']);
                                $('#fecha_Nacimiento').val(value['fecha_nacimiento_alumno']);
                                $('#telefono_Fijo').val(value['telefono_fijo_alumno']);
                                $('#celular').val(value['telefono_celular_alumno']);
                                $('#correo_Electronico').val(value['email_usuario']);
                                $('#fecha_alta').val(value['fecha_alta_usuario']);
                                $('#calle_Numero').val(value['calle_numero_alumno']);
                                $('#colonia').val(value['colonia_alumno']);
                                $('#delegacion_Municipio').val(value['delegacion_municipio_alumno']);
                                $('#codigo_Postal').val(value['codigo_postal_alumno']);
                                $('#anio_Ingreso_FI').val(value['anio_ingreso_fi_alumno']);
                                $('#semestre_Ingreso_FI').val(value['semestre_ingreso_fi_alumno']);
                                
                                $('#estado').val(value['id_estado_republica']).change();
                                $('#genero').val(value['id_genero']).change();
                                
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
                                                        
                //fin Obtenemos los datos actuales del Alumno

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
                        initStatus: 'Selecciona la fecha', isRTL: false
                    }; 
                $.datepicker.setDefaults($.datepicker.regional['es']); 

                $('#fecha_Nacimiento').datepicker({
                    changeYear : true,
                    changeMonth : true,
                    yearRange : '1920:2050',
                    onSelect : function(date){
                        $("#fecha_Nacimiento ~ .ui-datepicker").hide();
                    }
                });

                $( "#tabs" ).tabs();
//                $(':text:first').focus();
                
               
                function validaDatos(){
                    $('#Actualizar_Mi_Pefil_Alumno').prop('disable',true);
                    var datosValidos = true;
                    var nombre = $('#nombre').val();
                    var apellidoPaterno = $('#apellido_Paterno').val();
                    var apellidoMaterno = $('#apellido_Materno').val();
                    
                    var telefonoFijo = $('#telefono_Fijo').val();
                    var telefonoCelular = $('#celular').val();
                    var correoElectronico = $('#correo_Electronico').val();
                    var calleNumero = $('#calle_Numero').val();
                    var colonia = $('#colonia').val();
                    var delegacionMunicipio = $('#delegacion_Municipio').val();
                    var codigoPostal = $('#codigo_Postal').val();
                    var estado = $('#estado').val();
                    var anioIngreso = $('#anio_Ingreso_FI').val();
                    var semestreIngreso = $('#semestre_Ingreso_FI').val();
                    
                    $('#aviso_Nombre').hide();
                    $('#aviso_Apellido_Paterno').hide();
                    $('#aviso_Apellido_Materno').hide();
                    $('#aviso_Fecha_Nacimiento').hide();
                    $('#aviso_Telefono_Fijo').hide();
                    $('#aviso_Celular').hide();
                    $('#aviso_Correo_Electronico').hide();
                    $('#aviso_Calle_Numero').hide();
                    $('#aviso_Colonia').hide();                    
                    $('#aviso_Delegacion_Municipio').hide();
                    $('#aviso_Codigo_Postal').hide();
                    $('#aviso_Anio_Ingreso_FI').hide();
                    $('#aviso_Semestre_Ingreso_FI').hide();

//                    var miExpReg = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ]{1,50}$/;
//                    var miExpReg_Mail = /^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(.[a-zA-Z0-9-]+)*(.[a-zA-Z]{2,4})$/;
//                    var miExpReg_CP = /^[0-9]{5}$/;
//                    var miExpReg_Anio = /^[0-9]{4}$/;
//                    var miExpReg_Semestre = /^[1-2]{1}$/;
//		    var miExpReg_Telefono_Letras = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ0-9\.]{1,20}$/;
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
                    
                    if (!estado){
                        $('#aviso_Estado').show();
                        datosValidos = false;                        
                    }
                    if (!anioIngreso.match(miExpReg_Anio))
                    {
                        $('#aviso_Anio_Ingreso_FI').show();
                        datosValidos = false;
                    }   
                    else{
                        $('#aviso_Anio_Ingreso_FI').hide();
                    }
                    
                    if (!semestreIngreso.match(miExpReg_Semestre))
                    {
                        $('#aviso_Semestre_Ingreso_FI').show();
                        datosValidos = false;
                    }   
                    else{
                        $('#aviso_Semestre_Ingreso_FI').hide();
                    }

                    if (nombre =='' || apellidoPaterno =='' || apellidoMaterno =='' || telefonoFijo =='' || telefonoCelular=='' 
                         || correoElectronico =='' || calleNumero =='' || colonia ==''|| delegacionMunicipio =='' 
                         || codigoPostal =='' || !(estado) || anioIngreso =='' || semestreIngreso =='')
                    {
                        $('#ventanaAviso').html('Debe capturar TODA la información Requerida.');
                        $('#ventanaAvisos').dialog('open');
                        
                        datosValidos = false;
                    }
                    
                    $('#Actualizar_Mi_Pefil_Alumno').prop('disable',false);
                    return datosValidos;
                };
                               
                $('#Actualizar_Mi_Pefil_Alumno').on('click',function(event){
                    event.preventDefault();
                    if (validaDatos())
                    {                       
                       $('#ventanaConfirmacion').dialog('open');
                    }
                    else
                    {
                       return false; 
                    }                          
                });
                
                $('#ventanaConfirmacion').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $(this).dialog('close');
                            $('#ventanaProcesando').dialog('open');
                            
                            // Por Ajax insertamos al Usuario tipo Alumno               
                            var formDatos = $('#Alumno_Mi_Perfil').serialize();
                            $.ajax({
                                data : formDatos,
                                type : "POST",
                                dataType : "json",
                                url : "_Negocio/n_Usuario.php"
                            })
                                .done(function(respuesta,textStatus,jqXHR){
                                    $('#ventanaProcesando').dialog('close');
                                    $('#ventanaAviso').html(respuesta.data.message);
                                    $('#ventanaAvisos').dialog('open');                                        
                                })
                                        .fail(function(jqXHR,textStatus,errorThrown){
                                            $('#ventanaProcesando').dialog('close');
                                            $('#ventanaAviso').dialog({ dialogClass: 'ventanaMensajes'});
                                            $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                            $('#ventanaAvisos').dialog('open');                            
                                        });                                                                                                              
                        },
                        "Cancelar" : function() {
                            $(this).dialog('close');
                            $(':text:first').focus();
                        }                       
                   },
                   title: 'Mis Datos',
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
                   title: '',
                   modal : true,
                   autoOpen : false,
                   resizable : false,
                   draggable : false,
                   closeOnEscape : false,
                   dialogClass : 'no-close no-titlebar'

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
                
                /*$('.entrada_Dato').focus(function(e){
                    e.preventDefault();
                    f5($(document),false);
                });
                $('.entrada_Dato').blur(function(e){
                    e.preventDefault();
                    f5($(document),true);
                });
                
                f5($(document),true); */
                
            });
        </script>
        
<!--    </head>
    <body>
        <header>
            Mi Pefil
        </header>-->
        <div>
            <form name="Alumno_Mi_Perfil" id="Alumno_Mi_Perfil" method="" action="">
                <div>
                    <div class="encabezado_Formulario">
                        <div class="descripcion_Modulo">
                            <p>Mis Datos</p>
                        </div>
                        <div class="barra_Herramientas">
                            <input type="submit" id="Actualizar_Mi_Pefil_Alumno" name="Actualizar_Mi_Pefil_Alumno" value="Guardar" class="btn_Herramientas"/>
                        </div> <!--fin id_barra_Herramienta-->                    
                    </div>

                    <div id="load_php" class="contenido_Formulario">
                        <div id="tabs">
                            <ul>
                              <li><a href="#tabs-1">Generales</a></li>
                              <li><a href="#tabs-2">Dirección</a></li>
                              <li><a href="#tabs-3">Escolar</a></li>
                            </ul>

                            <div id="tabs-1">
                                <p>
                                    <label for="nombre" class="label">Nombre:</label>
                                    <input type="text" name="nombre" id="nombre" maxlength="50"
                                           title="Capture únicamente letras en MAYÚSCULA" autocomplete="off" 
                                           style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                                    <span id="aviso_Nombre" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                                </p> 
                                <p>
                                    <label for="apellido_Paterno" class="label">Apellido Paterno:</label>
                                    <input type="text" name="apellido_Paterno" id="apellido_Paterno" maxlength="50"
                                    title="Capture únicamente letras en MAYÚSCULA" autocomplete="off" 
                                    style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                                    <span id="aviso_Apellido_Paterno" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                                </p> 
                                <p>
                                    <label for="apellido_Materno" class="label">Apellido Materno:</label>
                                    <input type="text" name="apellido_Materno" id="apellido_Materno" maxlength="50"
                                    title="Capture únicamente letras en MAYÚSCULA" autocomplete="off" 
                                    style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                                    <span id="aviso_Apellido_Materno" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                                </p> 
                                <p>
                                    <label for="fecha_Nacimiento" class="label">Fecha de Nacimiento:</label>
                                    <input type="text" name="fecha_Nacimiento" id="fecha_Nacimiento" title="dd/mm/aaaa" readonly/>
                                    <span id="aviso_Fecha_Nacimiento" class="dato_Invalido"><img src="css/images/error.ico"/></span>
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
                                    <input type="text" name="telefono_Fijo" id="telefono_Fijo" maxlength="20"
                                           title="" autocomplete="off" placeholder="55 555 555 EXT. 55" 
                                           style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                                    <span id="aviso_Telefono_Fijo" class="dato_Invalido"><img src="css/images/error.ico"/></span>                                    
                                </p> 
                                <p>
                                    <label for="celular" class="label">Celular:</label>
                                    <input type="text" name="celular" id="celular" maxlength="15"
                                    title="" autocomplete="off" placeholder="5512131055" class="entrada_Dato"/>
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
                                    <span id="aviso_Fecha_Alta"></span>
                                </p>                 
                            </div>

                            <div id="tabs-2">
                                <p>
                                    <label for="calle_Numero" class="label">Calle y No.:</label>
                                    <input type="text" name="calle_Numero" id="calle_Numero" maxlength="100" placeholder="AV. UNIVERSIDAD #3000"
                                    title="" autocomplete="off" 
                                    style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                                    <span id="aviso_Calle_Numero" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                                </p>
                                <p>
                                    <label for="colonia" class="label">Colonia:</label>
                                    <input type="text" name="colonia" id="colonia" maxlength="100" placeholder="CIUDAD UNIVERSITARIA"
                                    title="" autocomplete="off" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                                    <span id="aviso_Colonia" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                                </p>
                                <p>
                                    <label for="delegacion_Municipio" class="label">Delegación / Municipio:</label>
                                    <input type="text" name="delegacion_Municipio" id="delegacion_Municipio" maxlength="100" placeholder="CD.DE MÉXICO"
                                    title="" autocomplete="off" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato"/>
                                    <span id="aviso_Delegacion_Municipio" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                                </p>
                                <p>
                                    <label for="codigo_Postal" class="label">Código Postal:</label>
                                    <input type="text" name="codigo_Postal" id="codigo_Postal" maxlength="5" placeholder="04510"
                                    title="" autocomplete="off" class="entrada_Dato"/>
                                    <span id="aviso_Codigo_Postal" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                                </p>
                                <p>
                                    <label for="estado" class="label">Estado:</label>
                                    <select name="estado" id="estado">
                                    </select>
                                    <span id="aviso_Estado" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                                </p> 
                            </div>

                            <div id="tabs-3">
                                <p>
                                    <label for="anio_Ingreso_FI" class="label">Año de Ingreso a la Facultad de Ingeniería:</label>
                                    <input type="text" name="anio_Ingreso_FI" id="anio_Ingreso_FI" maxlength="4" placeholder="1988"
                                    title="" autocomplete="off" class="entrada_Dato"/>
                                    <span id="aviso_Anio_Ingreso_FI" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                                </p>
                                <p>
                                    <label for="semestre_Ingreso_FI" class="label">Semestre de Ingreso a la Facultad de Ingeniería:</label>
                                    <input type="text" name="semestre_Ingreso_FI" id="semestre_Ingreso_FI" 
                                            maxlength="1" placeholder="1"
                                    title="" autocomplete="off" class="entrada_Dato"/>
                                    <span id="aviso_Semestre_Ingreso_FI" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                                </p>                                
                            </div>
                        </div>
                        <input type="hidden" id="Id_Tipo_Usuario" name="Id_Tipo_Usuario" value="5">
                        <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
                        <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="ACTUALIZAR">                        
                    </div> <!--fin load_php-->
            
                </div> <!--fin id_form_Mi_Perfil-->            
           </form>
        </div>
    
        <div id='ventanaConfirmacion'>
                Desea Actualizar los Datos de su Perfil?
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

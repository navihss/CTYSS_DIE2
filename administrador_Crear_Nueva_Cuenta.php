
<!DOCTYPE html>
<!--
Fecha:          Julio,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para dar de alta a un Nuevo Usuario
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

            var cuentaCreada = false;
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
                
                //CAMBIO LA SELECCION DEL TIPO DE USUARIO
                $('#tipo_Usuario').change(function(e){
                    e.preventDefault();
                    var tipo_Usuario = $(this).val();
                    $('.dato_Invalido').hide();
                    $('#id_Tipo_Usuario_Nuevo').val(tipo_Usuario);
                    switch (tipo_Usuario) {
                        case '1': //administrador
                            $('.coordinador').hide();
                            $('.departamento').hide();
                            $('.admin_coordinador').show();
                            $('.alumno_ent').hide();
                            $('.profesor').hide();
                            break;
                        case '2': //jefe de departamento
                            $('.coordinador').hide();
                            $('.departamento').show();
                            $('.admin_coordinador').show();                            
                            $('.alumno_ent').hide();
                            $('.profesor').hide();
                            break;
                            
                        case '3': //coordinador
                            $('.coordinador').show();
                            $('.departamento').hide();
                            $('.admin_coordinador').show();                            
                            $('.alumno_ent').hide();
                            $('.profesor').hide();
                            break;
                        case '5': //alumno
                            $('.coordinador').hide();
                            $('.departamento').hide();
                            $('.admin_coordinador').hide();                                                        
                            $('.alumno_ent').show();
                            $('.profesor').hide();
                            break;
                        case '4': //profesor
                            $('.coordinador').hide();
                            $('.departamento').hide();
                            $('.admin_coordinador').hide();                            
                            $('.alumno_ent').hide();
                            $('.profesor').show();
                            break;
                    }
                });
                
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
                $('#fechaNacimiento').datepicker({
                    changeYear : true,
                    changeMonth : true,
                    yearRange : '1920:2050',
                    onSelect : function(date){
                        $("#fechaNacimiento ~ .ui-datepicker").hide();
                    }                    

                });
               
                //VALIDAMOS LOS DATOS DE LA FORM
                function validaDatos(){
                    $('#btn_Guardar').prop('disable',true);
                    var datosValidos = true;
                    var tipo_Usuario = $('#tipo_Usuario').val();                    
                    var nombre = $('#nombre').val();
                    var apellidoPaterno = $('#apellidoPaterno').val();
                    var apellidoMaterno = $('#apellidoMaterno').val();                    
                    var fechaNacimiento = $('#fechaNacimiento').val();
                    var genero = $('#genero').val();
                    var puesto = $('#puesto').val();
                    var coordinacion = $('#coordinacion').val();
                    var departamento = $('#departamento').val();
                    var correo = $('#correo').val();
                    var clave = $('#clave').val();
                    var contrasena = $('#contrasena').val();
                    
                    $('#statusTipo_Usuario').hide();
                    $('#statusNombre').hide();
                    $('#statusApellidoPaterno').hide();
                    $('#statusApellidoMaterno').hide();
                    $('#statusFechaNacimiento').hide();
                    $('#statusGenero').hide();
                    $('#statusPuesto').hide();
                    $('#statusCoordinacion').hide();
                    $('#statusDepartamento').hide();
                    $('#statusCorreo').hide();
                    $('#statusClave').hide();                    
                    $('#statusContrasena').hide();

//                    var miExpReg_Letras = /^[a-zA-Z áéíóúñÁÉÍÓÚÑ]{1,50}$/;
//                    var miExpReg_Contrasena = /^[a-zA-Z0-9]{1,15}$/;
//                    var miExpReg_Mail = /^[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(.[a-zA-Z0-9-]+)*(.[a-zA-Z]{2,4})$/;
//                    var miExpReg_NoCta = /^[0-9]{9}$/;
//                    var miExpReg_ClaveOtroUsuario  = /^[a-zA-Z0-9]{1,18}$/
                    var miExpReg_ClaveDeUsuario  = '';

                    if (tipo_Usuario =='5'){ //alumno
                        miExpReg_ClaveDeUsuario = /^[0-9]{1,10}$/; // miExpReg_NoCta;
                    }
                    else{
                        miExpReg_ClaveDeUsuario = miExpReg_Clave;
                    }
                        
                    if (tipo_Usuario != '5'){
                        fechaNacimiento = '01/01/2016';
                    }
                    
                    if (tipo_Usuario =='')
                    {
                        $('#tipo_Usuario').show();
                        datosValidos = false;
                    }
                                        
                    if (!nombre.match(miExpReg_Nombre))
                    {
                        $('#statusNombre').show();
                        datosValidos = false;
                    }
                    else{
                        $('#statusNombre').hide();
                    }
                    
                    if (!apellidoPaterno.match(miExpReg_Nombre))
                    {
                        $('#statusApellidoPaterno').show();
                        datosValidos = false;
                    }
                    else{
                        $('#statusApellidoPaterno').hide();
                    }
                    
                    if (!apellidoMaterno.match(miExpReg_Nombre))
                    {
                        $('#statusApellidoMaterno').show();
                        datosValidos = false;
                    }
                    else{
                        $('#statusApellidoMaterno').hide();
                    }
                    
                    if (fechaNacimiento =='')
                    {                        
                        $('#statusFechaNacimiento').show();
                        datosValidos = false;
                    }                    
                    if (genero =='')
                    {
                        $('#statusGenero').show();
                        datosValidos = false;
                    }
                    if (puesto =='')
                    {
                        $('#statusPuesto').show();
                        datosValidos = false;
                    }
                    if (coordinacion =='')
                    {
                        $('#statusCoordinacion').show();
                        datosValidos = false;
                    }
                    if (departamento =='')
                    {
                        $('#statusDepartamento').show();
                        datosValidos = false;
                    }

                    if (!correo.match(miExpReg_Mail))
                    {
                        $('#statusCorreo').show();
                        datosValidos = false;
                    }
                    else{
                        $('#statusCorreo').hide();
                    }

                    if (!clave.match(miExpReg_ClaveDeUsuario))
                    {
                        $('#statusClave').text('Dato Inválido ');
                        $('#statusClave').show();
                        datosValidos = false;
                    }
                    else{
                        $('#statusClave').hide();
                    }
                    
                    if (!contrasena.match(miExpReg_Contrasena))
                    {
                        $('#statusContrasena').show();
                        datosValidos = false;
                    }
                    else{
                        $('#statusContrasena').hide();
                    }

                    if (nombre =='' || apellidoPaterno =='' || apellidoMaterno =='' || fechaNacimiento =='' || genero=='' 
                         || puesto =='' || coordinacion =='' || correo ==''|| clave =='' 
                         || contrasena =='')
                    {
                        $('#ventanaAviso').html('Debe capturar TODA la información Requerida.');
                        $('#ventanaAvisos').dialog('open');
                        
                        datosValidos = false;
                    }
                    
                    $('#btn_Guardar').prop('disable',false);
                    return datosValidos;
                };

//              Validamos que la clave no exista en la BD
                $('#clave').blur(function(){
                    if ($('#clave').val()==""){
                        return;
                    }                        
                    $("#cargandoAjax").css("display", "inline");
                    $("#statusClave").hide();
                    var datosAPasar = {
                        Tipo_Movimiento : 'EXISTE_USUARIO',
                        claveUsuario : $('#clave').val()
                    };
                    
                    $.ajax({
                        data : datosAPasar,
                        type : "POST",
                        dataType : "json",
                        url : "_Negocio/n_administrador_Crear_Nueva_Cuenta.php"
                    })
                            .done(function(respuesta,textStatus,jqXHR){
//                                $('#ventanaProcesando').dialog('close');                        
//                                $('#De_Alta_OK').val("0");
                                $("#cargandoAjax").css("display", "none");
                                $("#statusClave").hide();
                                if (respuesta.success == 'EXISTE'){ // Ya Existe La Cuenta
                                    $('#statusClave').show();
                                    $('#statusClave').text('Este Usuario ya existe en el Sistema!');
                                    $('#clave').prop('value','');
                                    $('#contrasena').val('');
                                    $('#clave').focus();
//                                    $('#enviar').prop('disabled', true);
                                }
                                else if (respuesta.success == 'NOEXISTE'){
                                    $('#statusClave').show();
                                    $('#statusClave').text('Disponible');
                                    obtener_Clave();
//                                    $('#enviar').prop('disabled', false);
                                }
                                else
                                {
//                                    $('#enviar').prop('disabled', true);
                                    $('#statusClave').show();
                                    $('#statusClave').text('');
                                    $('#ventanaAviso').html(respuesta.data.message);                                                                        
                                    $('#clave').val('');
//                                    $('#enviar').prop('disabled', false);
                                    $('#ventanaAvisos').dialog('open');
                                }                                                    
                            })
                                    .fail(function(jqXHR,textStatus,errorThrown){
                                        $("#cargandoAjax").css("display", "none");
                                        $('#statusClave').hide();
                                        $('#clave').prop('value','');
//                                        $('#enviar').prop('disabled', true);

                                        $('#ventanaAviso').html('La solicitud ha fallado. ' + textStatus + '. ' + errorThrown);
                                        $('#ventanaAvisos').dialog('open');                            
                                    });                                                                
                    
                }); // ***** fin clave_blur
                
                //OBTENERMOS UNA CONTRASEÑA PARA EL NUEVO USUARIO
                function obtener_Clave(){
                           var datos = {Tipo_Movimiento : 'GENERA_CLAVE'}
                           $.ajax({
                                data : datos,
                                type : "POST",
                                dataType : "json",
                                url : "_Negocio/n_administrador_Crear_Nueva_Cuenta.php"
                            })
                                .done(function(respuesta,textStatus,jqXHR){
                                    if(respuesta.success == true){
                                        $('#contrasena').val(respuesta.data.message);
                                    }
                                    else{
                                        $('#contrasena').val('');
    //                                    $('#ventanaProcesando').dialog('close');
                                        $('#ventanaAviso').html('No se pudo obtener una CLAVE válida.');
                                        $('#ventanaAvisos').dialog('open');                                        
                                        
                                    }                                    
                                })
                                        .fail(function(jqXHR,textStatus,errorThrown){
                                            $('#ventanaProcesando').dialog('close');
                                            $('#ventanaAviso').html('La solicitud ha fallado <br>' + textStatus + '. ' + errorThrown);
                                            $('#ventanaAvisos').dialog('open');                            
                                        });                       
                }
                
                $('#btn_Guardar').on('click',function(event){
                    event.preventDefault();
                    if (validaDatos())
                    {  
                       $('#Tipo_Movimiento').val('AGREGAR');
                       $('#ventanaConfirmacion').dialog('open');
                    }
                    else
                    {
                       return false; 
                    }                          
                });

                $('#btn_Limpiar').on('click',function(event){
                    event.preventDefault();
                    oculta_StatusControl();
                    limpia_Controles();
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
                    $('#statusDepartamento').hide();
                    $('#statusCorreo').hide();
                    $('#statusClave').hide();                    
                    $('#statusContrasena').hide();                    
                }
 
                function limpia_Controles(){
                    $('#nombre').val('');
                    $('#apellidoPaterno').val('');
                    $('#apellidoMaterno').val('');
                    $('#fechaNacimiento').val('');
                    $('#correo').val('');
                    $('#clave').val('');
                    $('#contrasena').val('');
                    $('#es_externo').prop('checked','');
                }
                
                $('#ventanaConfirmacion').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $(this).dialog('close');
//                            $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
//                            $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                            $('#ventanaProcesando').dialog('open');
                            
                            // Por Ajax insertamos al Usuario               
                            var formDatos = $('#Administrador_Nva_Cuenta').serialize();
                            
                            $.ajax({
                                data : formDatos,
                                type : "POST",
                                dataType : "json",
                                url : "_Negocio/n_administrador_Crear_Nueva_Cuenta.php"
                            })
                                .done(function(respuesta,textStatus,jqXHR){
                                    if(respuesta.success == true){
                                        $('#ventanaProcesando').dialog('close');
                                        cuentaCreada=true;
                                        $('#ventanaAviso').html(respuesta.data.message);
                                        $('#ventanaAvisos').dialog('open');
                                        limpia_Controles();

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
                   title: 'Nueva Cuenta de Usuario',
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
                            
                            if(cuentaCreada){
                                cuentaCreada = false;
                                location.href='home.php';
                            }
                            
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

                /*$('.entrada_Dato').focus(function(e){
                    e.preventDefault();
                    f5($(document),false);
                });
                $('.entrada_Dato').blur(function(e){
                    e.preventDefault();
                    f5($(document),true);
                });

                f5($(document),true); */
                var id_division = ('<?php echo $_SESSION["id_division"]?>');
                var filterByDivision = 'id_division = ' + id_division;
                // $('#nombre').val(filterByDivision);

                llena_Catalogo('tipo_Usuario', 'CATALOGO_GENERALES', 'tipo_usuario', 
                'id_tipo_usuario as id, descripcion_tipo_usuario as descripcion', 
                '', 'descripcion_tipo_usuario'); 
                
                llena_Catalogo('puesto', 'CATALOGO_GENERALES', 'puestos_trabajo', 
                'id_puesto_trabajo as id, descripcion_puesto_trabajo as descripcion',
                    '', 'descripcion_puesto_trabajo');
                
                llena_Catalogo('coordinacion', 'CATALOGO_GENERALES', 'coordinaciones', 
                'id_coordinacion as id, descripcion_coordinacion as descripcion', 
                '', 'descripcion_coordinacion');      

                llena_Catalogo('departamento', 'CATALOGO_GENERALES', 'departamentos', 
                'id_departamento as id, descripcion_departamento as descripcion', 
                '', 'descripcion_departamento');      

                llena_Catalogo('grado_educativo', 'CATALOGO_GENERALES', 'grados_estudio', 
                'id_grado_estudio as id, descripcion_grado_estudio as descripcion', 
                '', 'descripcion_grado_estudio');      

                llena_Catalogo('carrera', 'CATALOGO_GENERALES', 'carreras', 
                    'id_carrera as id, descripcion_carrera as descripcion',
                    filterByDivision, 'descripcion_carrera');

                llena_Catalogo('categoria', 'CATALOGO_GENERALES', 'categorias', 'id_categoria as id, descripcion_categoria as descripcion','', 'descripcion_categoria');

                llena_Catalogo('division', 'CATALOGO_GENERALES', 'division_categoria','id_div_cat as id, id_division as descripcion','','id_division');
               
                
                $('.coordinador').hide();
                $('.departamento').hide();
                $('.alumno_ent').hide();
                $('.profesor').hide();
               
                $(':text:first').focus();

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
                    <p>Nueva Cuenta de Usuario</p>
                </div>
            </div>
            <form name="Administrador_Nva_Cuenta" id="Administrador_Nva_Cuenta" method="" action="">
                <div class="contenido_Formulario">
                    <div class="sombra_Formulario">
                        <p>
                            <label for="tipo_Usuario" class="label">Tipo de Usuario:</label>                
                            <select name="tipo_Usuario" id="tipo_Usuario">
                            </select>
                            <span id="statusTipo_Usuario" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>

                        <p>
                            <label for="nombre" class="label">Nombre:</label>
                            <input type="text" name="nombre" id="nombre" maxlength=""  maxlength="50"
                                   title="Capture únicamente letras" class="entrada_Dato"
                                style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off"/>
                            <span id="statusNombre" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>    
                        <p>
                            <label for="apellidoPaterno" class="label">Apellido Paterno:</label>
                            <input type="text" name="apellidoPaterno" id="apellidoPaterno"  maxlength="50"
                                   title="Capture únicamente letras" class="entrada_Dato"
                                style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off"/>                    
                            <span id="statusApellidoPaterno" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>
                        <p>
                            <label for="apellidoMaterno" class="label">Apellido Materno:</label>
                            <input type="text" name="apellidoMaterno" id="apellidoMaterno"  maxlength="50"
                                title="Capture únicamente letras" 
                                style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="entrada_Dato" autocomplete="off"/>
                            <span id="statusApellidoMaterno" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>
                        <p>
                            <label for="fechaNacimiento" class="label alumno_ent">Fecha de nacimiento:</label>
                            <input type="text" name="fechaNacimiento" id="fechaNacimiento" autocomplete="off"  title="dd/mm/aaaa" class="alumno_ent" readonly/>                    
                            <span id="statusFechaNacimiento" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>
                        <p>
                            <label for="genero" class="label">Genero:</label>                
                            <select name="genero" id="genero">
                                <option value="2">Femenino</option>
                                <option value="1">Masculino</option>
                            </select>
                            <span id="statusGenero" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>
                        <p>
                            <label for="carrera" class="label alumno_ent">Carrera:</label>                
                            <select name="carrera" id="carrera" class="alumno_ent">
                            </select>
                            <span id="statusCarrera" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>

                        <p>
                            <label for="puesto" class="label admin_coordinador">Puesto:</label>                
                            <select name="puesto" id="puesto" class="admin_coordinador">
                            </select>
                            <span id="statusPuesto" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>                           
                        <p>
                            <label for="coordinacion" class="label coordinador">Coordinación:</label>                
                            <select name="coordinacion" id="coordinacion" class="coordinador">
                            </select>
                            <span id="statusCoordinacion" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>                                                       
                        <p>
                            <label for="departamento" class="label departamento">Departamento:</label>                
                            <select name="departamento" id="departamento" class="departamento">
                            </select>
                            <span id="statusDepartamento" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>                                                       

                        <p>
                            <label for="correo" class="label">Dirección de correo electrónico:</label>
                            <input type="email" name="correo" id="correo" maxlength="100" placeholder="miCorreo@dominio.com"
                                   title="Capture su dirección de correo TAL Y COMO LA DIÓ DE ALTA CON SU PROVEEDOR" class="entrada_Dato" autocomplete="off"/>
                            <span id="statusCorreo" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>
                        <p>
                            <label for="es_externo" class="label profesor">Es externo a la FI:</label>
                            <input type="checkbox"  class="profesor" name="es_externo" id="es_externo"/>
                            <label for="grado" class="label profesor">Grado Educativo.:</label>
                            <select name="grado_educativo" id="grado_educativo" class="profesor" style="width: 120px;">
                            </select>
                            <label class="label profesor">Categoría y división:</label>
                            <select name="categoria" id="categoria" style="width:110px" class="profesor">
                            </select>
                            <select name="division" id="division" style="width:110px" class="profesor">
                            </select>
                        </p>                            
                        <p>
                            <label for="clave" class="label">Usuario:</label>
                            <input type="text" name="clave" id="clave" maxlength="18" placeholder="086198516"
                                   title="Capture la Clave del Usuario sin guiones (solo letras y números)" autocomplete="off" class="entrada_Dato"/>
                            <div id="cargandoAjax" class="notificacion">
                                <span><img src="css/images/ajax-loader03.gif"/>Espere. Verificando si este Usuario está Disponible!</span>
                            </div>  
                            <div class="notificacion">
                                <span id="statusClave"class="dato_Invalido"></span>
                            </div>                                    
                        </p>
                        <p>
                            <label for="contrasena" class="label">Contraseña:</label>
                            <input type="text" name="contrasena" id="contrasena" readonly maxlength="15" placeholder=""
                                   title="Capture únicamente letras y números, sin espacios" autocomplete="off" class="entrada_Dato"/>
                            <span id="statusContrasena" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>                            

                        <div style="padding-top: 15px;">
                                <input type="submit" name="btn_Guardar" id="btn_Guardar" value="Guardar" class="btn_Herramientas">
                                <input type="button" name="btn_Limpiar" id="btn_Limpiar" value="Limpiar" class="btn_Herramientas">
                        </div>                                               
                    </div> 

                </div>
                <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
                <input type="hidden" id="id_Tipo_Usuario_Nuevo" name="id_Tipo_Usuario_Nuevo" value="1">
                <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="">
                <input type="hidden" id="id_division" name="id_division" value="<?php echo $_SESSION['id_division'] ?>">
           </form>
        </div>

        <div id='ventanaConfirmacion'>
                Desea crear la Cuenta ?
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

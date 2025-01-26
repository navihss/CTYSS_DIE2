
<!DOCTYPE html>
<!--
Fecha:          Agosto,2016
Desarrollador:  Rogelio Reyes Mendoza
Objetivo:       Interfaz para modificar los datos del Administrador
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
                        async : false,
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

                //OBTENEMOS LOS DATOS ACTUALES DEL ADMINISTRADOR
                function muestra_Perfil(id_usuario,id_tipo_user){
                    var datos = {Tipo_Movimiento : 'OBTENER_USUARIO',
                                id_tipo_usuario : id_tipo_user, //
                                id_usuario : id_usuario}
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
                                    $('#nombre').val(value['nombre_usuario']);
                                    $('#apellido_Paterno').val(value['apellido_paterno_usuario']);
                                    $('#apellido_Materno').val(value['apellido_materno_usuario']);
                                    $('#correo_Electronico').val(value['email_usuario']);
                                    $('#fecha_alta').val(value['fecha_alta_usuario']);
                                    $('#genero').val(value['id_genero']).change();
                                    $('#puesto').val(value['id_puesto_trabajo']).change();
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
                //fin Obtenemos los datos actuales del ADMINISTRADOR
                
               
                //VALIDAMOS LOS DATOS DE LA FORM
                 function validaDatos(){
                    $('#btn_Guardar').prop('disable',true);
                    var datosValidos = true;
                    var nombre = $('#nombre').val();
                    var apellidoPaterno = $('#apellido_Paterno').val();
                    var apellidoMaterno = $('#apellido_Materno').val();                    
                    var correoElectronico = $('#correo_Electronico').val();
                    var puesto = $('#puesto').val();                    
                    var genero = $('#genero').val();
                                        
                    $('#aviso_Nombre').hide();
                    $('#aviso_Apellido_Paterno').hide();
                    $('#aviso_Apellido_Materno').hide();
                    $('#aviso_Genero').hide();
                    $('#aviso_Grado').hide();
                    $('#aviso_Correo_Electronico').hide();

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
                    
                    if (!correoElectronico.match(miExpReg_Mail))
                    {
                        $('#aviso_Correo_Electronico').show();
                        datosValidos = false;
                    }
                    else{
                        $('#aviso_Correo_Electronico').hide();
                    }
                    if (!puesto)
                    {
                        $('#aviso_Puesto').show();
                        datosValidos = false;
                    }
                    if (!genero)
                    {
                        $('#aviso_Genero').show();
                        datosValidos = false;
                    }

                    if (nombre =='' || apellidoPaterno =='' || apellidoMaterno ==''
                         || correoElectronico =='' ||
                         !puesto || !genero)
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
                    $('#statusGenero').hide();
                    $('#statusPuesto').hide();
                    $('#statusCorreo').hide();
                    $('#statusClave').hide();                    
                }
                
                $('#ventanaConfirmacion').dialog({
                   buttons:{
                        "Aceptar" : function() {
                            $(this).dialog('close');
                            $('#ventanaProcesando').dialog({ dialogClass: 'no-close' });
                            $('#ventanaProcesando').dialog({ dialogClass: 'no-titlebar'});
                            $('#ventanaProcesando').dialog('open');
                            
                            // Por Ajax actualizamos los datos del Usuario               
                            var formDatos = $('#admin_Mi_Perfil').serialize();
                            
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
                   dialogClass : 'no-close ventanaConfirmaUsuario'
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
                   dialogClass : 'no-close ventanaMensajes'
                });                            
        
                $('#ventanaProcesando').dialog({
                   title: 'Procesando...',
                   modal : true,
                   autoOpen : false,
                   resizable : false,
                   draggable : false,
                   dialogClass : 'no-close'
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
                
                llena_Catalogo('puesto', 'CATALOGO_GENERALES', 'puestos_trabajo', 
                'id_puesto_trabajo as id, descripcion_puesto_trabajo as descripcion', 
                '', 'descripcion_puesto_trabajo');                                              

                muestra_Perfil($('#Id_Usuario').val(),$('#id_Tipo_Usuario').val());

                              
                $(':text:first').focus();
            });
        </script>

        <div>
            <div class="encabezado_Formulario">
                <div class="descripcion_Modulo">
                        <p>Mis Datos</p>
                 </div>
                <div class="barra_Herramientas">
                        <input type="submit" id="btn_Guardar" name="btn_Guardar" value="Guardar" class="btn_Herramientas"/>
                 </div>
            </div>
            <form name="admin_Mi_Perfil" id="admin_Mi_Perfil" method="" action="">                
                <div class="contenido_Formulario">
                    <div class="sombra_Formulario">
                        <p>
                            <label for="nombre" class="label">Nombre:</label>
                            <input type="text" name="nombre" id="nombre" value='' maxlength="50" placeholder="" 
                                   style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"
                                   title="Capture únicamente letras" autocomplete="off" class="entrada_Dato"/>
                            <span id="aviso_Nombre" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p> 
                        <p>
                            <label for="apellido_Paterno" class="label">Apellido Paterno:</label>
                            <input type="text" name="apellido_Paterno" id="apellido_Paterno" value='' maxlength="50" placeholder="" 
                                   style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"
                            title="Capture únicamente letras" class="entrada_Dato" autocomplete="off"/>
                            <span id="aviso_Apellido_Paterno" class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p> 
                        <p>
                            <label for="apellido_Materno" class="label">Apellido Materno:</label>
                            <input type="text" name="apellido_Materno" id="apellido_Materno" value='' maxlength="50" placeholder="" 
                                   style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"
                            title="Capture únicamente letras" class="entrada_Dato" autocomplete="off"/>
                            <span id="aviso_Apellido_Materno"  class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p> 
                        <p>
                            <label for="genero" class="label">Género:</label>
                            <select name="genero" id="genero">
                                <option value="2">Femenino</option>
                                <option value="1">Masculino</option>
                            </select>
                            <span id="aviso_Genero"  class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>                                                                                                 
                        <p>
                            <label for="correo_Electronico" class="label">e-mail:</label>
                            <input type="text" name="correo_Electronico" id="correo_Electronico" placeholder="micorreo@dominio.com" value='' maxlength="100" 
                            title="Capture su dirección de correo TAL Y COMO LA DIÓ DE ALTA CON SU PROVEEDOR" class="entrada_Dato" autocomplete="off"/>
                            <span id="aviso_Correo_Electronico"  class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p> 
                        <p>
                            <label for="puesto" class="label">Puesto:</label>
                            <select name="puesto" id="puesto">
                            </select>
                            <span id="aviso_Puesto"  class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>                                                                                                 

                        <p>
                            <label for="fecha_alta" class="label">Fecha de Alta:</label>
                            <input type="text" name="fecha_alta" id="fecha_alta" placeholder="dd-mm-aaaa" readonly/>
                            <span id="aviso_Fecha_Alta"  class="dato_Invalido"><img src="css/images/error.ico"/></span>
                        </p>                 
                    
                    </div>
                <input type="hidden" id="Id_Usuario" name="Id_Usuario" value="<?php echo $_SESSION['id_usuario']; ?>">
                <input type="hidden" id="id_Tipo_Usuario" name="id_Tipo_Usuario" value="<?php echo $_SESSION["id_tipo_usuario"];?>">
                <input type="hidden" id="Tipo_Movimiento" name="Tipo_Movimiento" value="">
            </div>
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

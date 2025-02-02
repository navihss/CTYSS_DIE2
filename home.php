<?php
header("Cache-Control: no-cache");
header("Pragma: nocache");
header('Content-Type: text/html; charset=UTF-8');
session_start();
date_default_timezone_set('America/Mexico_City');
if(!isset($_SESSION["id_tipo_usuario"]) and
!isset($_SESSION["id_usuario"])){
header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">       
        <meta http-equiv="Expires" content="0" /> 
        <meta http-equiv="Pragma" content="no-cache" />  
        
  		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="js/jquery-1.12.4.min.js"></script>

        <!--<link href="css/jquery-ui.css" rel="stylesheet">-->
        <link href="jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet">
        <!--<script src="js/jquery-ui.min.js"></script>-->
        <script src="jquery-ui-1.12.1/jquery-ui.min.js"></script>
        
        <title></title>
        <link rel="stylesheet" href="css/menu.css">
        <link rel="stylesheet" href="css/home.css">
        <link rel="stylesheet" href="css/styleuno.css">
        <script type="text/javascript">
        if(history.forward(1)){
        location.replace( history.forward(1) );
        }
        </script>
        
        
        <script>
        $( document ).ready(function() {
        //Ocultamos los menús según el Tipo de Usuario
        var id_usuario = ('<?php echo $_SESSION["id_usuario"]?>');
        var id_division = ('<?php echo $_SESSION["id_division"]?>');
        switch (<?php echo $_SESSION["id_tipo_usuario"]?>){
        case 5  :   //Alumnos
        $('.menuProfesor').hide();
        $('.menuCoordinador').hide();
        $('.menuJefeDepartamento').hide();
        $('.menuAdministrador').hide();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('alumno_Mis_Pendientes.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        break;
        case 4  :   //Profesor
        $('.menuAlumno').hide();
        $('.menuCoordinador').hide();
        $('.menuJefeDepartamento').hide();
        $('.menuAdministrador').hide();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('profesor_Pendientes.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        
        break;
        case 3  :   //Coordinador
        $('.menuAlumno').hide();
        $('.menuProfesor').hide();
        $('.menuAdministrador').hide();
        $('.menuJefeDepartamento').hide();
        
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('coord_jdpto_Mis_Pendientes.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        
        break;
        case 2  :   //Jefe Dpto
        $('.menuAlumno').hide();
        $('.menuProfesor').hide();
        $('.menuCoordinador').hide();
        $('.menuAdministrador').hide();
        
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('coord_jdpto_Mis_Pendientes.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        
        break;
        case 1  :   //Administrador
        $('.menuAlumno').hide();
        $('.menuProfesor').hide();
        $('.menuCoordinador').hide();
        $('.menuJefeDepartamento').hide();

        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_Mis_Pendientes.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        break;
        }
        
        //MENU ALUMNOS
        $('#mi_Perfil_Alumno').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('alumno_Mi_Perfil.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        
        $('#mis_Carreras').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('alumno_Mis_Carreras.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        
        $('#mi_Servicio_Social').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('alumno_Mi_Servicio.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#mis_Reportes_Bimestrales').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('alumno_Mis_Reportes.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#mi_Carta_Terminacion').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('alumno_Mi_Carta_Terminacion.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        
        //Menu Mis pendientes
        $('#mis_Pendientes').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('alumno_Mis_Pendientes.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });

        $('#mi_Titulacion_Por_Propuesta').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('alumno_Mi_Titulacion_Por_Propuesta.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#mi_Jurado').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('alumno_Mi_Jurado.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        
        $('#mi_Titulacion_Por_Ceremonia').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('alumno_Mi_Ceremonia.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        
        $('#alumno_Cambio_Contrasena').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('alumno_Cambio_Contrasena.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        
        //MENU ADMINISTRADOR
        $('#mi_Perfil_Administrador').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_Mi_Perfil.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        
        $('#autorizar_Servicio_Social').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_Aprobar_Servicio_Social.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        
        $('#autorizar_Reportes_Bimestrales').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_Aprobar_Reporte_Bimestral.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });

                
            
    	//REPORTERIA
        $('#autorizar_Reportes_Estadisticas').click(function(event){
            event.preventDefault();
            $('div.ui-dialog').remove();
            $('#tmp_nuevo_Contenido').load('administrador_reporte_pride.php');
            $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));

        });

        $('#autorizar_Reportes_Titulados').click(function(event){
            event.preventDefault();
            $('div.ui-dialog').remove();
            $('#tmp_nuevo_Contenido').load('administrador_reporte_titulos_por_anio.php');
            $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));

        });

        $('#autorizar_Reportes_formatos').click(function(event){
            event.preventDefault();
            $('div.ui-dialog').remove();
            $('#tmp_nuevo_Contenido').load('administrador_reporte_escaner.php');
            $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));

        });

        $('#profesores').click(function(event){
            event.preventDefault();
            $('div.ui-dialog').remove();
            $('#tmp_nuevo_Contenido').load('administrador_reporte_programas_ss_por_alumnos.php');
            $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));

        });

        $('#historico').click(function(event){
            event.preventDefault();
            $('div.ui-dialog').remove();
            $('#tmp_nuevo_Contenido').load('administrador_reporte_programas_ss_por_programas.php');
            $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));

        });


        $('#t_alumnos').click(function(event){
            event.preventDefault();
            $('div.ui-dialog').remove();
            $('#tmp_nuevo_Contenido').load('administrador_reporte_titulacion_alumnos.php');
            $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));

        });

        $('#t_profesores').click(function(event){
            event.preventDefault();
            $('div.ui-dialog').remove();
            $('#tmp_nuevo_Contenido').load('administrador_reporte_titulacion_profesores.php');
            $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));

        });

        $('#t_ceremonias').click(function(event){
            event.preventDefault();
            $('div.ui-dialog').remove();
            $('#tmp_nuevo_Contenido').load('administrador_reporte_titulacion_ceremonia.php');
            $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));

        });

        $('#autorizar_Carta_Terminacion').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_Aprobar_Carta_Terminacion.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#asignar_Coordinadores').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_Asignar_Coordinadores.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#asignar_Jurado_Definitivo').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_Asignar_Jurado.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#autorizar_Ceremonia').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_Aprobar_Ceremonia.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#admon_Coord_Dptos').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_admon_Coord_Dptos.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#admon_rpt_bimestrales').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_admon_rpt_bimestrales.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        //Evento para el botón de mis pendientes
        $('#admon_Mis_Pendientes').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_Mis_Pendientes.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });

        $('#Nueva_Cuenta_Usuario').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_Crear_Nueva_Cuenta.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#Cambio_Contrasena').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_Cambiar_Contrasena.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#admon_Contadores').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_admon_Contadores.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#Programas_Serv_Social').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_admon_Programas_SS.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $("#Reporte_Titulacion_Proceso").click(function(event) {
            event.preventDefault();
            $('div.ui-dialog').remove();
            $('#tmp_nuevo_Contenido').load('administrador_Reporte_Titulacion.php');
            //$('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#autorizar_Solicitud_Baja_Servicio_Social').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_Aprobar_Baja_Servicio_Social.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#autorizar_Solicitud_Baja_Ceremonia').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('administrador_Aprobar_Baja_Ceremonia.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        //MENU DEL PROFESOR
        $('#prof_Mi_Perfil').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('profesor_Mi_Perfil.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#prof_Mis_Docs').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('profesor_Mis_Docs.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#prof_Mis_Propuestas').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('profesor_Mis_Propuestas.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#prof_Aceptar_Alumnos').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('profesor_Aceptar_Alumnos.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#prof_Mis_Pendientes').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('profesor_Pendientes.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        //MENU DEL COORDINADOR
        $('#coord_Mi_Perfil').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('coord_jdpto_Mi_Perfil.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#coord_Aprobar_Propuesta').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('coord_jdpto_Aprobar_Propuesta.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        
        $('#coord_Aprobar_Jurado').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('coord_jdpto_Aprobar_Jurado.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#coord_Aprobar_Ceremonia').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('coord_jdpto_Aprobar_Ceremonia.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#coord_Mis_Pendientes').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('coord_jdpto_Mis_Pendientes.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        //MENU DEL JEFE DPTO
        $('#jefedpto_Mi_Perfil').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('coord_jdpto_Mi_Perfil.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#jefedpto_Aprobar_Propuesta').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('coord_jdpto_Aprobar_Propuesta.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#jefedpto_Aprobar_Jurado').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('coord_jdpto_Aprobar_Jurado.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#jefedpto_Mis_Pendientes').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('coord_jdpto_Mis_Pendientes.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        $('#mi_Bitacora').click(function(event){
        event.preventDefault();
        $('div.ui-dialog').remove();
        $('#tmp_nuevo_Contenido').load('usuario_Bitacora.php');
        $('#nuevo_Contenido').replaceWith($('#tmp_nuevo_Contenido'));
        });
        //CERRAR MI SESION
        $('#cerrar_Mi_Sesion').click(function(event){
        event.preventDefault();
        
        $.ajax({
        type : "POST",
        dataType : "json",
        async : false,
        url : "cerrar_Mi_Sesion.php"
        });
        //                    header("index.php");
        window.open('index.php','_self');
        });
        
        }); //fin jquery
        </script>
    </head>
    <body>
        
        <div class="area_de_trabajo">
            <div id="encabezado">
                <div id="">
                    <img class="logo" src="css/images/banner_DIE2.jpg" alt="Escudo"/>
                </div>
                <!-- The Modal -->
            </div>
            <div class="datos_Usuario" id="datos_Usuario">
                <li><span><button id="myBtn"><?php echo $_SESSION['nombre_usuario'] ?></button></span></li>
                <li><span><?php echo $_SESSION['descripcion_division'] ?></span></li>
                <li><span><?php echo $_SESSION['descripcion_tipo_usuario'] ?></span></li>
                <li><span><?php echo $_SESSION['id_usuario'] ?></span></li>
            </div>
            <!-- The Modal -->
            <div id="myModal" class="modal">
                <!-- Modal content -->
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <p>Nombre: <?php echo $_SESSION['nombre_usuario'] ?></p>
                    <p>No.Cuenta: <?php echo $_SESSION['id_usuario'] ?></p>
                    <p>Telefono(s): <?php echo $_SESSION['telefono_fijo_alumno'] ?> <br> <?php echo $_SESSION['telefono_celular_alumno'] ?></p>
                    <p>Correo: <?php echo $_SESSION['correo_usuario_sesion'] ?> </p>
                </div>
            </div>
            <div id="menu" class="menu_Aplicativo">
                <ul class="nav">
                    <!--Se agrega el botón para regresar a la página principal. El icono se encuentra en las imagenes de la carpeta css-->
                    <li><a href="home.php">Ir a página principal <img src="css/images/icon_home.jpg"></a> <!--Icono para regresar a pantalla principal--></li>                    
                    <li>
                        <a href="#" class="menuAlumno">Mi Perfil<span class="flecha">&#9660</span></a>
                        <ul>
                            <li><a href="#" id="mi_Perfil_Alumno">Mis Datos<span class="flecha">&#9660</span></a></li>
                            <li><a href="#" id="mis_Carreras">Mis Carreras<span class="flecha">&#9660</span></a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="menuAlumno">Mi Servicio Social<span class="flecha">&#9660</span></a>
                        <ul>
                            <li><a href="#" id="mi_Servicio_Social">Mi Servicio Social<span class="flecha">&#9660</span></a></li>
                            <li><a href="#" id="mis_Reportes_Bimestrales">Mis Reportes Bimestrales<span class="flecha">&#9660</span></a></li>
                            <li><a href="#" id="mi_Carta_Terminacion">Mi Carta de Terminación<span class="flecha">&#9660</span></a></li>
                        </ul>
                    </li>
                    <!--Se agrega botón de pendientes para el alumno-->
                    <li>
                        <a href="#" id="mis_Pendientes" class="menuAlumno">Mis pendientes<span class="flecha">&#9660</span></a>
                    </li>

                    <!-- <li>
                        <a href="#" class="menuAlumno">Mi Titulación<span class="flecha">&#9660</span></a>
                        <ul>
                            <li><a href="#" id="mi_Titulacion_Por_Propuesta">Inscripción a Propuesta por Examen<span class="flecha">&#9660</span> </a></li>
                            <li><a href="#" id="mi_Jurado">Solicitar Jurado<span class="flecha">&#9660</span></a></li>
                            <li><a href="#" id="mi_Titulacion_Por_Ceremonia">Inscripción a Propuesta por Ceremonia<span class="flecha">&#9660</span></a></li>
                        </ul>
                    </li> -->
                    <li><a href="#" id="alumno_Cambio_Contrasena" class="menuAlumno"> Cambio de Contraseña<span class="flecha">&#9660</span></a>
                </li>
                <li>
                    <a href="#" class="menuProfesor">Mi Perfil<span class="flecha">&#9660</span></a>
                    <ul>
                        <li><a href="#" id="prof_Mi_Perfil" class="menuProfesor">Mis Datos<span class="flecha">&#9660</span></a></li>
                        <li><a href="#" id="prof_Mis_Docs" class="menuProfesor">Mis Documentos<span class="flecha">&#9660</span></a></li>
                    </ul>
                </li>
                <li><a href="#" id="prof_Mis_Propuestas" class="menuProfesor">Mis Propuestas<span class="flecha">&#9660</span></a>
            </li>
            <li><a href="#" id="prof_Aceptar_Alumnos" class="menuProfesor">Aceptación y Baja de Alumnos<span class="flecha">&#9660</span></a>
        </li>
        <li><a href="#" id="prof_Mis_Pendientes" class="menuProfesor">Mis Pendientes<span class="flecha">&#9660</span></a>
    </li>

    <li><a href="#" id="coord_Mi_Perfil" class="menuCoordinador">Mi Perfil<span class="flecha">&#9660</span></a>
</li>
<li><a href="#" id='coord_Aprobar_Propuesta' class="menuCoordinador">Aprobar Propuestas<span class="flecha">&#9660</span></a>
</li>
<li><a href="#" id='coord_Aprobar_Jurado' class="menuCoordinador">Definir Jurado<span class="flecha">&#9660</span></a>
</li>
<li><a href="#" id='coord_Aprobar_Ceremonia' class="menuCoordinador">Ceremonias por Aprobar<span class="flecha">&#9660</span></a>
</li>
<li><a href="#" id='coord_Mis_Pendientes' class="menuCoordinador">Mis Pendientes<span class="flecha">&#9660</span></a>
</li>
<li><a href="#"  id ='jefedpto_Mi_Perfil' class="menuJefeDepartamento">Mi Perfil<span class="flecha">&#9660</span></a>
</li>
<li><a href="#" id='jefedpto_Aprobar_Propuesta' class="menuJefeDepartamento">Aprobar Propuestas<span class="flecha">&#9660</span></a>
</li>
<li><a href="#" id='jefedpto_Aprobar_Jurado' class="menuJefeDepartamento">Definir Jurado<span class="flecha">&#9660</span></a>
</li>
<li><a href="#" id='jefedpto_Mis_Pendientes' class="menuJefeDepartamento">Mis Pendientes<span class="flecha">&#9660</span></a>
</li>
<li><a href="#" class="menuAdministrador">Mi Perfil<span class="flecha">&#9660</span></a>
<ul>
<li><a href="#" id="mi_Perfil_Administrador">Mis Datos<span class="flecha">&#9660</span></a></li>
</ul>
</li>
<li><a href="#" class="menuAdministrador">Aprobación de Documentos<span class="flecha">&#9660</span></a>
<ul>
<li><a href="#" id="autorizar_Servicio_Social">Para el Servicio Social<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="autorizar_Reportes_Bimestrales">Reportes Bimestrales<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="autorizar_Carta_Terminacion">Cartas de Término<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="asignar_Coordinadores">Asignar Coordinadores a Propuestas<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="asignar_Jurado_Definitivo">Asignar Jurado Definitivo<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="autorizar_Ceremonia">Documentos por Ceremonia<span class="flecha">&#9660</span></a>
<li><a href="#" id="">Solicitudes de Baja<span class="flecha">&#9660</span></a>
<ul>
<li><a href="#" id="autorizar_Solicitud_Baja_Servicio_Social">De Servicio Social<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="autorizar_Solicitud_Baja_Ceremonia">De Titulación por Ceremonia<span class="flecha">&#9660</span></a></li>
</ul>
</li>

<li><a href="#" id=autorizar_Reportes_Estadisticas>Pride<span class="flecha">&#9660</span></a></li>
<li><a href="#" id=autorizar_Reportes_Titulados>Estadisticas<span class="flecha">&#9660</span></a></li>
<li><a href="#" id=autorizar_Reportes_formatos>Formatos<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="">Reportes por Servicio Social<span class="flecha">&#9660</span></a>
  <ul>
      <li><a href="#" id="profesores">Por Alumnos<span class="flecha">&#9660</span></a></li>
      <li><a href="#" id="historico">Por Programas<span class="flecha">&#9660</span></a></li>
  </ul>
</li>

<li><a href="#" id="">Tipos De Titulaci&oacuten<span class="flecha">&#9660</span></a>
  <ul>
      <li><a href="#" id="t_alumnos">Alumnos<span class="flecha">&#9660</span></a></li>
      <li><a href="#" id="t_ceremonias">Ceremonia<span class="flecha">&#9660</span></a></li>
  </ul>
</li>
                              
</ul>
</li>
<!--Se agrega menú de pendientes para el administrador-->
<li>
    <a href="#" id="admon_Mis_Pendientes" class="menuAdministrador">Mis pendientes<span class="flecha">&#9660</span></a>
</li>
<li><a href="#" class="menuAdministrador">Administración<span class="flecha">&#9660</span></a>
<ul>
<li><a href="#" id="Nueva_Cuenta_Usuario">Crear Nueva Cuenta de Usuario<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="Cambio_Contrasena">Cambio de Contraseña<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="admon_rpt_bimestrales">Admon. Rpt. Bimestrales<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="admon_Coord_Dptos">Admon. Coord. y Dptos<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="admon_Contadores">Admon. Contadores<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="Programas_Serv_Social">Programas para Servicio Social<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="Reporte_Titulacion_Proceso">Reporte. Titulación en proceso.<span class="flecha">&#9660</span></a></li>
</ul>
<li><a href="#" id="mi_Bitacora" class="">Mi Bitácora<span class="flecha">&#9660</span></a></li>
<li><a href="#" id="cerrar_Mi_Sesion" class="">Cerrar Mi Sesión<span class="flecha">&#9660</span></a></li>
</ul>
</div> <!-- fin menu-->
</div> <!-- fin encabezado-->
<div id="contenido">
<div id="nuevo_Contenido">
</div>
<div id="tmp_nuevo_Contenido">
</div>
<input type="hidden" id="id_tpo_usuario" name="id_tpo_usuario" value="<?php echo $_SESSION['id_tipo_usuario'] ?>">
<input type="hidden" id="id_division" name="id_division" value="<?php echo $_SESSION['id_division'] ?>">
<br style="clear: both;">
<!--    <footer id="pie">
<h1>Pie de página</h1>
</footer>-->
</div> <!-- fin area de trabajo-->
<script>
// Get the modal
var modal = document.getElementById('myModal');
// Get the button that opens the modal
var btn = document.getElementById("myBtn");
// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];
// When the user clicks the button, open the modal
btn.onclick = function() {
modal.style.display = "block";
}
// When the user clicks on <span> (x), close the modal
span.onclick = function() {
modal.style.display = "none";
}
// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
if (event.target == modal) {
modal.style.display = "none";
}
}
</script>
</body>
</html>

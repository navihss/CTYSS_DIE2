<?php

/**
 * Interfaz de la Capa Negocio para crear una Nueva Cuenta de Usuario.
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */

session_start();
if (
    !isset($_SESSION["id_usuario"]) and
    !isset($_SESSION["id_tipo_usuario"]) and
    !isset($_SESSION["descripcion_tipo_usuario"]) and
    !isset($_SESSION["nombre_usuario"])
) {
    header('Location: ../index.php');
}
if (!isset($_POST['Tipo_Movimiento'])) {
    header('Location: ../index.php');
}
$id_division = 0;
if (isset($_SESSION["id_division"])) {
    $id_division = $_SESSION["id_division"];
}

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Administrador.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Coordinador.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Jefe_Departamento.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Profesor.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Alumno.php');

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_administrador_Crear_Nueva_Cuenta.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Alumno.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Catalogos_Generales.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/zonaHoraria.php');

$tipo_Movimiento = $_POST['Tipo_Movimiento'];

$obj_d_Administrador_NC = new d_administrador_Crear_Nueva_Cuenta();

switch ($tipo_Movimiento) {
    case "OBTENER_USUARIO":
        $id_usuario = $_POST['id_usuario'];
        $id_tipo_usuario = $_POST['id_tipo_usuario'];
        echo $obj_d_Administrador_NC->Obtener_Usuario($id_usuario, $id_tipo_usuario);
        break;

    case "EXISTE_USUARIO":
        $id_usuario = $_POST['claveUsuario'];
        echo $obj_d_Administrador_NC->Existe_Clave_Usuario($id_usuario, $id_division);
        break;

    case 'CATALOGO_GENERALES':

        $tabla_Catalogo = $_POST['tabla_Catalogo'];
        $tabla_Campos = $_POST['tabla_Campos'];
        $tabla_Where = $_POST['tabla_Where'];
        $tabla_OrderBy = $_POST['tabla_OrderBy'];

        $obj_Catalogos = new d_Catalogos_Generales();
        echo $obj_Catalogos->Obtener($tabla_Catalogo, $tabla_Campos, $tabla_Where, $tabla_OrderBy);
        break;
    case 'GENERA_CLAVE':
        $obj_d_Administrador_NC = new d_administrador_Crear_Nueva_Cuenta();
        echo $obj_d_Administrador_NC->genera_Clave();
        break;

    case 'AGREGAR': //AGREGAMOS UN NUEVO USUARIO                                
        $tipo_Usuario = $_POST['tipo_Usuario'];

        switch ($tipo_Usuario) {
            case    '1':  //Administrador
                $obj_Entidad = new Administrador();
                $obj_Entidad->set_Id_Usuario($_POST['clave']);
                $obj_Entidad->set_Contrasena($_POST['contrasena']);
                $obj_Entidad->set_Correo_Electronico($_POST['correo']);
                $obj_Entidad->set_Fecha_Alta(date('d-m-Y H:i:s'));
                $obj_Entidad->set_Nombre($_POST['nombre']);
                $obj_Entidad->set_Apellido_Paterno($_POST['apellidoPaterno']);
                $obj_Entidad->set_Apellido_Materno($_POST['apellidoMaterno']);
                $obj_Entidad->set_Activo(1);
                $obj_Entidad->set_Id_Tipo_Usuario($tipo_Usuario);
                $obj_Entidad->set_Id_Genero($_POST['genero']);
                $obj_Entidad->set_Id_Tipo_Baja(5);

                $obj_Entidad->set_Id_Administrador($_POST['clave']);
                $obj_Entidad->set_Id_Puesto($_POST['puesto']);
                $obj_Entidad->set_Id_Usuario($_POST['clave']);

                $id_administrador = $_POST['Id_Usuario'];

                $obj_d_Administrador_NC = new d_administrador_Crear_Nueva_Cuenta();
                echo $obj_d_Administrador_NC->Agregar_Usuario($obj_Entidad, $id_administrador, $id_division);

                break;
            case    '2':  //Jefe Dpto

                $obj_Entidad = new Jefe_Departamento();
                $obj_Entidad->set_Contrasena($_POST['contrasena']);
                $obj_Entidad->set_Correo_Electronico($_POST['correo']);
                $obj_Entidad->set_Fecha_Alta(date('d-m-Y H:i:s'));
                $obj_Entidad->set_Nombre($_POST['nombre']);
                $obj_Entidad->set_Apellido_Paterno($_POST['apellidoPaterno']);
                $obj_Entidad->set_Apellido_Materno($_POST['apellidoMaterno']);
                $obj_Entidad->set_Activo(1);
                $obj_Entidad->set_Id_Usuario($_POST['clave']);
                $obj_Entidad->set_Id_Tipo_Usuario($tipo_Usuario);
                $obj_Entidad->set_Id_Genero($_POST['genero']);
                $obj_Entidad->set_Id_Tipo_Baja(5);

                $obj_Entidad->set_Id_Jefe_Departamento($_POST['clave']);
                $obj_Entidad->set_Id_Grado_Estudio(1);
                $obj_Entidad->set_Id_Usuario($_POST['clave']);
                $obj_Entidad->set_Id_Departamento($_POST['departamento']);
                $obj_Entidad->set_Id_Puesto($_POST['puesto']);

                $id_administrador = $_POST['Id_Usuario'];

                $obj_d_Administrador_NC = new d_administrador_Crear_Nueva_Cuenta();
                echo $obj_d_Administrador_NC->Agregar_Usuario($obj_Entidad, $id_administrador, $id_division);

                break;
            case    '3':  //Coordinador

                $obj_Entidad = new Coordinador();
                $obj_Entidad->set_Id_Usuario($_POST['clave']);
                $obj_Entidad->set_Contrasena($_POST['contrasena']);
                $obj_Entidad->set_Correo_Electronico($_POST['correo']);
                $obj_Entidad->set_Fecha_Alta(date('d-m-Y H:i:s'));
                $obj_Entidad->set_Nombre($_POST['nombre']);
                $obj_Entidad->set_Apellido_Paterno($_POST['apellidoPaterno']);
                $obj_Entidad->set_Apellido_Materno($_POST['apellidoMaterno']);
                $obj_Entidad->set_Activo(1);
                $obj_Entidad->set_Id_Tipo_Usuario($tipo_Usuario);
                $obj_Entidad->set_Id_Genero($_POST['genero']);
                $obj_Entidad->set_Id_Tipo_Baja(5);

                $obj_Entidad->set_Id_Coordinador($_POST['clave']);
                $obj_Entidad->set_Id_Grado_Estudio(1);
                $obj_Entidad->set_Id_Usuario($_POST['clave']);
                $obj_Entidad->set_Id_Coordinacion($_POST['coordinacion']);
                $obj_Entidad->set_Id_Puesto($_POST['puesto']);

                $id_administrador = $_POST['Id_Usuario'];

                $obj_d_Administrador_NC = new d_administrador_Crear_Nueva_Cuenta();
                echo $obj_d_Administrador_NC->Agregar_Usuario($obj_Entidad, $id_administrador, $id_division);

                break;
            case    '4':  //Profesor
                $obj_Entidad = new Profesor();
                $obj_Entidad->set_Id_Usuario($_POST['clave']);
                $obj_Entidad->set_Contrasena($_POST['contrasena']);
                $obj_Entidad->set_Correo_Electronico($_POST['correo']);
                $obj_Entidad->set_Fecha_Alta(date('d-m-Y H:i:s'));
                $obj_Entidad->set_Nombre($_POST['nombre']);
                $obj_Entidad->set_Apellido_Paterno($_POST['apellidoPaterno']);
                $obj_Entidad->set_Apellido_Materno($_POST['apellidoMaterno']);
                $obj_Entidad->set_Activo(1);
                $obj_Entidad->set_Id_Tipo_Usuario($tipo_Usuario);
                $obj_Entidad->set_Id_Genero($_POST['genero']);
                $obj_Entidad->set_Id_Tipo_Baja(5);

                if (isset($_POST['es_externo'])) {
                    $es_externo = 1;
                } else {
                    $es_externo = 0;
                }

                $obj_Entidad->set_Id_Profesor($_POST['clave']);
                $obj_Entidad->set_Dependencia_Laboral('DEPENDENCIA');
                $obj_Entidad->set_Fecha_Ingreso_FI('2016/01/01');
                $obj_Entidad->set_RFC('RFC');
                $obj_Entidad->set_CURP('CURP');
                $obj_Entidad->set_Calle_Numero('Direccion');
                $obj_Entidad->set_Colonia('Colonia');
                $obj_Entidad->set_Delegacion_Municipio('Delegacion');
                $obj_Entidad->set_CP('CP');
                $obj_Entidad->set_Telefono_Fijo('TF');
                $obj_Entidad->set_Telefono_Extension('EXT');
                $obj_Entidad->set_Telefono_Celular('CELL');
                $obj_Entidad->set_Id_Estado(1);
                $obj_Entidad->set_Es_Externo($es_externo);
                $obj_Entidad->set_Id_Grado_Estudio($_POST['grado_educativo']);

                $id_administrador = $_POST['Id_Usuario'];

                $obj_d_Administrador_NC = new d_administrador_Crear_Nueva_Cuenta();
                echo $obj_d_Administrador_NC->Agregar_Usuario($obj_Entidad, $id_administrador, $id_division);

                break;
            case    '5':  //Alumno
                $obj_d_Alumno = new d_Alumno();
                $obj_Alumno = new Alumno();
                $obj_Alumno->set_Id_Alumno($_POST['clave']);
                $obj_Alumno->set_Calle_Numero("");
                $obj_Alumno->set_Colonia("");
                $obj_Alumno->set_Delegacion_Municipio("");
                $obj_Alumno->set_Codigo_Postal("");
                $obj_Alumno->set_Telefono_Fijo("");
                $obj_Alumno->set_Celular("");
                $obj_Alumno->set_Fecha_Nacimiento($_POST['fechaNacimiento']);
                $obj_Alumno->set_Anio_Ingreso_FI(0);
                $obj_Alumno->set_Semestre_Ingreso_FI(0);
                $obj_Alumno->set_Id_Estado(1);
                $obj_Alumno->set_IdUsuario($_POST['clave']);
                $obj_Alumno->set_Id_Usuario($_POST['clave']);
                $obj_Alumno->set_Contrasena(($_POST['contrasena']));
                $obj_Alumno->set_Nombre($_POST['nombre']);
                $obj_Alumno->set_Apellido_Paterno(($_POST['apellidoPaterno']));
                $obj_Alumno->set_Apellido_Materno(($_POST['apellidoMaterno']));
                $obj_Alumno->set_Id_Tipo_Usuario(5);
                $obj_Alumno->set_Id_Tipo_Baja(5);
                $obj_Alumno->set_Fecha_Alta(date('d-m-Y H:i:s'));
                $obj_Alumno->set_Id_Carrera($_POST['carrera']);

                $obj_Alumno->set_Correo_Electronico($_POST['correo']);
                $obj_Alumno->set_Id_Genero($_POST['genero']);
                $obj_Alumno->set_Activo(1);
                $id_administrador = $_POST['Id_Usuario'];

                echo $obj_d_Alumno->Agregar($obj_Alumno, $id_division, $id_administrador);

                break;
        }
        break;

    case 'ACTUALIZAR': //ACTUALIZAMOS LOS DATOS DEL USUARIO                                
        $tipo_Usuario = $_POST['id_Tipo_Usuario'];

        switch ($tipo_Usuario) {
            case    '1':  //Administrador
                $obj_Entidad = new Administrador();
                $obj_Entidad->set_Correo_Electronico($_POST['correo_Electronico']);
                $obj_Entidad->set_Nombre($_POST['nombre']);
                $obj_Entidad->set_Apellido_Paterno($_POST['apellido_Paterno']);
                $obj_Entidad->set_Apellido_Materno($_POST['apellido_Materno']);
                $obj_Entidad->set_Id_Genero($_POST['genero']);
                $obj_Entidad->set_Id_Tipo_Usuario($_POST['id_Tipo_Usuario']);

                $obj_Entidad->set_Id_Administrador($_POST['Id_Usuario']);
                $obj_Entidad->set_Id_Usuario($_POST['Id_Usuario']);
                $obj_Entidad->set_Id_Puesto($_POST['puesto']);
                $obj_d_Administrador_NC = new d_administrador_Crear_Nueva_Cuenta();
                echo $obj_d_Administrador_NC->Actualizar_Usuario($obj_Entidad, $id_division);

                break;
            case    '2':  //Jefe Dpto
                $obj_Entidad = new Jefe_Departamento();
                $obj_Entidad->set_Id_Usuario($_POST['Id_Usuario']);
                $obj_Entidad->set_Correo_Electronico($_POST['correo_Electronico']);
                $obj_Entidad->set_Nombre($_POST['nombre']);
                $obj_Entidad->set_Apellido_Paterno($_POST['apellido_Paterno']);
                $obj_Entidad->set_Apellido_Materno($_POST['apellido_Materno']);
                $obj_Entidad->set_Id_Tipo_Usuario($tipo_Usuario);
                $obj_Entidad->set_Id_Genero($_POST['genero']);
                $obj_Entidad->set_Id_Jefe_Departamento($_POST['Id_Usuario']);
                isset($_POST['grado']) ? $obj_Entidad->set_Id_Grado_Estudio($_POST['grado']) : '';
                $obj_Entidad->set_Id_Usuario($_POST['Id_Usuario']);
                isset($_POST['coordinacion']) ? $obj_Entidad->set_Id_Departamento($_POST['coordinacion']) : '';
                isset($_POST['puesto']) ? $obj_Entidad->set_Id_Puesto($_POST['puesto']) : '';

                $obj_d_Administrador_NC = new d_administrador_Crear_Nueva_Cuenta();
                echo $obj_d_Administrador_NC->Actualizar_Usuario($obj_Entidad, $id_division);

                break;
            case    '3':  //Coordinador

                $obj_Entidad = new Coordinador();
                $obj_Entidad->set_Id_Usuario($_POST['Id_Usuario']);
                $obj_Entidad->set_Correo_Electronico($_POST['correo_Electronico']);
                $obj_Entidad->set_Nombre($_POST['nombre']);
                $obj_Entidad->set_Apellido_Paterno($_POST['apellido_Paterno']);
                $obj_Entidad->set_Apellido_Materno($_POST['apellido_Materno']);
                $obj_Entidad->set_Id_Tipo_Usuario($tipo_Usuario);
                $obj_Entidad->set_Id_Genero($_POST['genero']);
                $obj_Entidad->set_Id_Coordinador($_POST['Id_Usuario']);
                isset($_POST['grado']) ? $obj_Entidad->set_Id_Grado_Estudio($_POST['grado']) : '';
                $obj_Entidad->set_Id_Usuario($_POST['Id_Usuario']);
                isset($_POST['coordinacion']) ? $obj_Entidad->set_Id_Coordinacion($_POST['coordinacion']) : '';
                isset($_POST['puesto']) ? $obj_Entidad->set_Id_Puesto($_POST['puesto']) : '';

                $obj_d_Administrador_NC = new d_administrador_Crear_Nueva_Cuenta();
                echo $obj_d_Administrador_NC->Actualizar_Usuario($obj_Entidad, $id_division);

                break;
            case    '4':  //Profesor
                $obj_Entidad = new Profesor();
                $obj_Entidad->set_Id_Usuario($_POST['Id_Usuario']);
                $obj_Entidad->set_Correo_Electronico($_POST['correo_Electronico']);
                $obj_Entidad->set_Nombre($_POST['nombre']);
                $obj_Entidad->set_Apellido_Paterno($_POST['apellido_Paterno']);
                $obj_Entidad->set_Apellido_Materno($_POST['apellido_Materno']);
                $obj_Entidad->set_Id_Genero($_POST['genero']);

                $obj_Entidad->set_Id_Profesor($_POST['Id_Usuario']);
                $obj_Entidad->set_Dependencia_Laboral($_POST['dependencia_laboral']);
                $obj_Entidad->set_Fecha_Ingreso_FI($_POST['anio_Ingreso_FI']);
                $obj_Entidad->set_RFC($_POST['RFC']);
                $obj_Entidad->set_CURP($_POST['CURP']);
                $obj_Entidad->set_Calle_Numero($_POST['calle_Numero']);
                $obj_Entidad->set_Colonia($_POST['colonia']);
                $obj_Entidad->set_Delegacion_Municipio($_POST['delegacion_Municipio']);
                $obj_Entidad->set_CP($_POST['codigo_Postal']);
                $obj_Entidad->set_Telefono_Fijo($_POST['telefono_Fijo']);
                $obj_Entidad->set_Telefono_Extension($_POST['telefono_Extension']);
                $obj_Entidad->set_Telefono_Celular($_POST['celular']);
                $obj_Entidad->set_Id_Estado($_POST['estado']);
                $obj_Entidad->set_Id_Grado_Estudio($_POST['grado_educativo']);

                if (isset($_POST['es_externo'])) {
                    $es_externo = 1;
                } else {
                    $es_externo = 0;
                }
                $obj_Entidad->set_Es_Externo($es_externo);

                $obj_d_Administrador_NC = new d_administrador_Crear_Nueva_Cuenta();
                echo $obj_d_Administrador_NC->Actualizar_Usuario($obj_Entidad, $id_division);

                break;
        }
        break;

    case 'CAMBIAR_CONTRASENA': //CAMBIAMOS LA CONSTRASEÑA DEL USUARIO

        $id_usuario = $_POST['clave'];
        $contrasenaNueva = $_POST['contrasena'];
        $id_administrador = $_POST['Id_Usuario'];
        $nom_usuario = $_POST['nombre'];

        echo $obj_d_Administrador_NC->Cambiar_Contrasena($id_usuario, $contrasenaNueva, $id_administrador, $nom_usuario, $id_division);
        break;
}

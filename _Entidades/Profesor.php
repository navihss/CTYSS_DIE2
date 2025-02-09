<?php

/**
 * Definición de la Clase Profesor
 * Propiedades
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */

require_once 'Usuario.php';

class Profesor extends Usuario
{
    /* Definición de Propiedades */
    private $id_profesor;
    private $dependencia_laboral_profesor;
    private $rfc_profesor;
    private $curp_profesor;
    private $calle_numero_profesor;
    private $colonia_profesor;
    private $delegacion_municipio_profesor;
    private $codigo_postal_profesor;
    private $telefono_fijo_profesor;
    private $telefono_extension_profesor;
    private $telefono_celular_profesor;
    private $id_usuario;
    private $id_estado_republica;
    private $fecha_ingreso_fi;
    private $es_externo;
    private $id_grado_estudio;

    function set_Id_Profesor($idprofesor)
    {
        $this->id_profesor = $idprofesor;
    }
    function get_Id_Profesor()
    {
        return $this->id_profesor;
    }

    function set_Dependencia_Laboral($dependencia_laboral_profesor)
    {
        $this->dependencia_laboral_profesor = $dependencia_laboral_profesor;
    }
    function get_Dependencia_Laboral()
    {
        return $this->dependencia_laboral_profesor;
    }
    function set_RFC($rfc_profesor)
    {
        $this->rfc_profesor = $rfc_profesor;
    }
    function get_RFC()
    {
        return $this->rfc_profesor;
    }
    function set_CURP($curp_profesor)
    {
        $this->curp_profesor = $curp_profesor;
    }
    function get_CURP()
    {
        return $this->curp_profesor;
    }
    function set_Calle_Numero($calle_numero_profesor)
    {
        $this->calle_numero_profesor = $calle_numero_profesor;
    }
    function get_Calle_Numero()
    {
        return $this->calle_numero_profesor;
    }
    function set_Colonia($colonia_profesor)
    {
        $this->colonia_profesor = $colonia_profesor;
    }
    function get_Colonia()
    {
        return $this->colonia_profesor;
    }
    function set_Delegacion_Municipio($delegacion_municipio_profesor)
    {
        $this->delegacion_municipio_profesor = $delegacion_municipio_profesor;
    }
    function get_Delegacion_Municipio()
    {
        return $this->delegacion_municipio_profesor;
    }
    function set_CP($codigo_postal_profesor)
    {
        $this->codigo_postal_profesor = $codigo_postal_profesor;
    }
    function get_CP()
    {
        return $this->codigo_postal_profesor;
    }
    function set_Telefono_Fijo($telefono_fijo_profesor)
    {
        $this->telefono_fijo_profesor = $telefono_fijo_profesor;
    }
    function get_Telefono_Fijo()
    {
        return $this->telefono_fijo_profesor;
    }
    function set_Telefono_Extension($telefono_extension_profesor)
    {
        $this->telefono_extension_profesor = $telefono_extension_profesor;
    }
    function get_Telefono_Extension()
    {
        return $this->telefono_extension_profesor;
    }
    function set_Telefono_Celular($telefono_celular_profesor)
    {
        $this->telefono_celular_profesor = $telefono_celular_profesor;
    }
    function get_Telefono_Celular()
    {
        return $this->telefono_celular_profesor;
    }
    function set_Id_Estado($id_estado_republica)
    {
        $this->id_estado_republica = $id_estado_republica;
    }
    function get_Id_Estado()
    {
        return $this->id_estado_republica;
    }
    function set_Id_Usuario($id_usuario)
    {
        $this->id_usuario = $id_usuario;
    }
    function get_Id_Usuario()
    {
        return $this->id_usuario;
    }
    function set_Fecha_Ingreso_FI($fecha_ingreso_fi)
    {
        $this->fecha_ingreso_fi = $fecha_ingreso_fi;
    }
    function get_Fecha_Ingreso_FI()
    {
        return $this->fecha_ingreso_fi;
    }
    function set_Es_Externo($esexterno)
    {
        $this->es_externo = $esexterno;
    }
    function get_Es_Externo()
    {
        return $this->es_externo;
    }
    function set_Id_Grado_Estudio($idgradoestudio)
    {
        $this->id_grado_estudio = $idgradoestudio;
    }
    function get_Id_Grado_Estudio()
    {
        return $this->id_grado_estudio;
    }
}

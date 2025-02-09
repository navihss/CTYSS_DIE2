<?php

/**
 * Definición de la Clase Programa para Servicio Social
 * Propiedades
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */

class Programa_SS
{
    private $id_programa;
    private $descripcion;
    private $subdireccion;
    private $responsable;
    private $cargo;
    private $email;
    private $oficina;
    private $calle;
    private $colonia;
    private $delegacion;
    private $cp;
    private $num_exterior;
    private $num_interior;
    private $telefono_ss;
    private $telefono_dependencia;
    private $id_dependencia;
    private $tipo_programa;
    private $id_estado;
    private $id_estatus;
    private $carreras;


    function set_Id_Programa($idprograma)
    {
        $this->id_programa = $idprograma;
    }
    function get_Id_Programa()
    {
        return $this->id_programa;
    }

    function set_Descripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    function get_Descripcion()
    {
        return $this->descripcion;
    }

    function set_Subdireccion($subdireccion)
    {
        $this->subdireccion = $subdireccion;
    }
    function get_Subdireccion()
    {
        return $this->subdireccion;
    }

    function set_Responsable($responsable)
    {
        $this->responsable = $responsable;
    }
    function get_Responsable()
    {
        return $this->responsable;
    }

    function set_Cargo($cargo)
    {
        $this->cargo = $cargo;
    }
    function get_Cargo()
    {
        return $this->cargo;
    }

    function set_Email($email)
    {
        $this->email = $email;
    }
    function get_Email()
    {
        return $this->email;
    }

    function set_Oficina($oficina)
    {
        $this->oficina = $oficina;
    }
    function get_Oficina()
    {
        return $this->oficina;
    }

    function set_Calle($calle)
    {
        $this->calle = $calle;
    }
    function get_Calle()
    {
        return $this->calle;
    }

    function set_Colonia($colonia)
    {
        $this->colonia = $colonia;
    }
    function get_Colonia()
    {
        return $this->colonia;
    }

    function set_Delegacion($delegacion)
    {
        $this->delegacion = $delegacion;
    }
    function get_Delegacion()
    {
        return $this->delegacion;
    }

    function set_CP($cp)
    {
        $this->cp = $cp;
    }
    function get_CP()
    {
        return $this->cp;
    }

    function set_Num_Exterior($numexterior)
    {
        $this->num_exterior = $numexterior;
    }
    function get_Num_Exterior()
    {
        return $this->num_exterior;
    }

    function set_Num_Interior($numinterior)
    {
        $this->num_interior = $numinterior;
    }
    function get_Num_Interior()
    {
        return $this->num_interior;
    }

    function set_Telefono_SS($telefonoss)
    {
        $this->telefono_ss = $telefonoss;
    }
    function get_Telefono_SS()
    {
        return $this->telefono_ss;
    }

    function set_Telefono_Dependencia($teldependencia)
    {
        $this->telefono_dependencia = $teldependencia;
    }
    function get_Telefono_Dependencia()
    {
        return $this->telefono_dependencia;
    }

    function set_Id_Dependencia($iddependencia)
    {
        $this->id_dependencia = $iddependencia;
    }
    function get_Id_Dependencia()
    {
        return $this->id_dependencia;
    }

    function set_Tipo_Programa($tipoprograma)
    {
        $this->tipo_programa = $tipoprograma;
    }
    function get_Tipo_Programa()
    {
        return $this->tipo_programa;
    }

    function set_Id_Estado($idestado)
    {
        $this->id_estado = $idestado;
    }
    function get_Id_Estado()
    {
        return $this->id_estado;
    }

    function set_Id_Estatus($idestatus)
    {
        $this->id_estatus = $idestatus;
    }
    function get_Id_Estatus()
    {
        return $this->id_estatus;
    }

    function set_Carreras($carreras)
    {
        $this->carreras = $carreras;
    }
    function get_Carreras()
    {
        return $this->carreras;
    }
}

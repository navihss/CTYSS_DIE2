<?php

/**
 * Definición de la Clase Alumno
 * Propiedades
 * @author Rogelio Reyes Mendoza
 * Mayo 2016
 */

require_once 'Usuario.php';

class Alumno extends Usuario
{
    /* Definición de Propiedades */
    private $Id_Alumno;
    private $CalleNumero;
    private $Colonia;
    private $DelegacionMunicipio;
    private $CodigoPostal;
    private $Id_Estado;
    private $TelefonoFijo;
    private $Celular;
    private $FechaNacimiento;
    private $AnioIngresoFI;
    private $SemestreIngresoFI;
    private $Id_Usuario;
    private $Id_Carrera;
    private $Id_Division;

    function set_Id_Alumno($idAlumno)
    {
        $this->Id_Alumno = $idAlumno;
    }
    function get_Id_Alumno()
    {
        return $this->Id_Alumno;
    }

    function set_Calle_Numero($calleNumero)
    {
        $this->CalleNumero = $calleNumero;
    }
    function get_Calle_Numero()
    {
        return $this->CalleNumero;
    }

    function set_Colonia($colonia)
    {
        $this->Colonia = $colonia;
    }
    function get_Colonia()
    {
        return $this->Colonia;
    }

    function set_Delegacion_Municipio($delegacionMunicipio)
    {
        $this->DelegacionMunicipio = $delegacionMunicipio;
    }
    function get_Delegacion_Municipio()
    {
        return $this->DelegacionMunicipio;
    }

    function set_Codigo_Postal($codigoPostal)
    {
        $this->CodigoPostal = $codigoPostal;
    }
    function get_Codigo_Postal()
    {
        return $this->CodigoPostal;
    }

    function set_Id_Estado($idEstado)
    {
        $this->Id_Estado = $idEstado;
    }
    function get_Id_Estado()
    {
        return $this->Id_Estado;
    }

    function set_Telefono_Fijo($telefonoFijo)
    {
        $this->TelefonoFijo = $telefonoFijo;
    }
    function get_Telefono_Fijo()
    {
        return $this->TelefonoFijo;
    }

    function set_Celular($celular)
    {
        $this->Celular = $celular;
    }
    function get_Celular()
    {
        return $this->Celular;
    }

    function set_Fecha_Nacimiento($fechaNacimiento)
    {
        $this->FechaNacimiento = $fechaNacimiento;
    }
    function get_Fecha_Nacimiento()
    {
        return $this->FechaNacimiento;
    }

    function set_Anio_Ingreso_FI($anioIngresoFI)
    {
        $this->AnioIngresoFI = $anioIngresoFI;
    }
    function get_Anio_Ingreso_FI()
    {
        return $this->AnioIngresoFI;
    }

    function set_Semestre_Ingreso_FI($semestreIngresoFI)
    {
        $this->SemestreIngresoFI = $semestreIngresoFI;
    }
    function get_Semestre_Ingreso_FI()
    {
        return $this->SemestreIngresoFI;
    }

    function set_IdUsuario($idUsuario)
    {
        $this->Id_Usuario = $idUsuario;
    }
    function get_IdUsuario()
    {
        return $this->Id_Usuario;
    }

    function set_Id_Carrera($idcarrera)
    {
        $this->Id_Carrera = $idcarrera;
    }
    function get_Id_Carrera()
    {
        return $this->Id_Carrera;
    }

    function set_Id_Division($idDivision)
    {
        $this->Id_Division = $idDivision;
    }
    function get_Id_Division()
    {
        return $this->Id_Division;
    }
    /* Fin Definición de Propiedades */
}

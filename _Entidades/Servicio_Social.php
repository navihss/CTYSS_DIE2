<?php

/**
 * Descripción de la Clase Servicio Social
 *
 * @author Rogelio Reyes Mendoza
 */
class Servicio_Social
{
    /* Definición de Propiedades */
    private $Id_SS;
    private $Fecha_Inicio;
    private $Fecha_Termino;
    private $Duracion_Meses;
    private $Avance_Creditos;
    private $Avance_Porcentaje;
    private $Promedio;
    private $Jefe_Inmediato;
    private $Percepcion_Mensual;
    private $Id_programa;
    private $Id_Tipo_Remuneracion;
    private $Id_Tipo_Baja;
    private $Id_Estatus;
    private $Id_Carrera;
    private $Id_Alumno;
    private $Id_Division;

    function set_Id_SS($idSS)
    {
        $this->Id_SS = $idSS;
    }
    function get_Id_SS()
    {
        return $this->Id_SS;
    }

    function set_Fecha_Inicio($FechaInicio)
    {
        $this->Fecha_Inicio = $FechaInicio;
    }
    function get_Fecha_Inicio()
    {
        return $this->Fecha_Inicio;
    }

    function set_Fecha_Termino($FechaTermino)
    {
        $this->Fecha_Termino = $FechaTermino;
    }
    function get_Fecha_Termino()
    {
        return $this->Fecha_Termino;
    }

    function set_Duracion_Meses($DuracionMeses)
    {
        $this->Duracion_Meses = $DuracionMeses;
    }
    function get_Duracion_Meses()
    {
        return $this->Duracion_Meses;
    }

    function set_Avance_Creditos($AvanceCreditos)
    {
        $this->Avance_Creditos = $AvanceCreditos;
    }
    function get_Avance_Creditos()
    {
        return $this->Avance_Creditos;
    }

    function set_Avance_Porcentaje($AvancePorcentaje)
    {
        $this->Avance_Porcentaje = $AvancePorcentaje;
    }
    function get_Avance_Porcentaje()
    {
        return $this->Avance_Porcentaje;
    }

    function set_Promedio($Promedio)
    {
        $this->Promedio = $Promedio;
    }
    function get_Promedio()
    {
        return $this->Promedio;
    }

    function set_Jefe_Inmediato($JefeInmediato)
    {
        $this->Jefe_Inmediato = $JefeInmediato;
    }
    function get_Jefe_Inmediato()
    {
        return $this->Jefe_Inmediato;
    }

    function set_Percepcion_Mensual($PercepcionMensual)
    {
        $this->Percepcion_Mensual = $PercepcionMensual;
    }
    function get_Percepcion_Mensual()
    {
        return $this->Percepcion_Mensual;
    }

    function set_Id_Programa($IdPrograma)
    {
        $this->Id_programa = $IdPrograma;
    }
    function get_Id_Programa()
    {
        return $this->Id_programa;
    }

    function set_Id_Tipo_Remuneracion($IdTipoRemuneracion)
    {
        $this->Id_Tipo_Remuneracion = $IdTipoRemuneracion;
    }
    function get_Id_Tipo_Remuneracion()
    {
        return $this->Id_Tipo_Remuneracion;
    }

    function set_Id_Tipo_Baja($IdTipoBaja)
    {
        $this->Id_Tipo_Baja = $IdTipoBaja;
    }
    function get_Id_Tipo_Baja()
    {
        return $this->Id_Tipo_Baja;
    }

    function set_Id_Estatus($IdEstatus)
    {
        $this->Id_Estatus = $IdEstatus;
    }
    function get_Id_Estatus()
    {
        return $this->Id_Estatus;
    }

    function set_Id_Carrera($IdCarrera)
    {
        $this->Id_Carrera = $IdCarrera;
    }
    function get_Id_Carrera()
    {
        return $this->Id_Carrera;
    }

    function set_Id_Alumno($IdAlumno)
    {
        $this->Id_Alumno = $IdAlumno;
    }
    function get_Id_Alumno()
    {
        return $this->Id_Alumno;
    }

    function set_Id_Division($idDivision)
    {
        $this->Id_Division = $idDivision;
    }
    function get_Id_Division()
    {
        return $this->Id_Division;
    }
}

<?php

/**
 * Definición de la Clase Ceremonia
 * Propiedades
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */

class Ceremonia
{
    /* Definición de Propiedades */
    private $id_ceremonia;
    private $fecha_alta;
    private $sedes;
    private $diplomados_cursos;
    private $materias;
    private $programa_posgrado;
    private $nombre_articulo;
    private $nombre_revista;
    private $id_alumno;
    private $id_carrera;
    private $id_estatus;
    private $id_tipo_propuesta;
    private $des_propuesta;

    function set_Id_Ceremonia($idceremonia)
    {
        $this->id_ceremonia = $idceremonia;
    }
    function get_Id_Ceremonia()
    {
        return $this->id_ceremonia;
    }
    function set_Fecha_Alta($fechaAlta)
    {
        $this->fecha_alta = $fechaAlta;
    }
    function get_Fecha_Alta()
    {
        return $this->fecha_alta;
    }
    function set_Sedes($sedes)
    {
        $this->sedes = $sedes;
    }
    function get_Sedes()
    {
        return $this->sedes;
    }
    function set_Diplomados_Cursos($diplomadosCursos)
    {
        $this->diplomados_cursos = $diplomadosCursos;
    }
    function get_Diplomados_Cursos()
    {
        return $this->diplomados_cursos;
    }
    function set_Materias($materias)
    {
        $this->materias = $materias;
    }
    function get_Materias()
    {
        return $this->materias;
    }
    function set_Programa_Posgrado($programaPosgrado)
    {
        $this->programa_posgrado = $programaPosgrado;
    }
    function get_Programa_Posgrado()
    {
        return $this->programa_posgrado;
    }
    function set_Nombre_Articulo($nombreArticulo)
    {
        $this->nombre_articulo = $nombreArticulo;
    }
    function get_Nombre_Articulo()
    {
        return $this->nombre_articulo;
    }
    function set_Nombre_Revista($nombreRevista)
    {
        $this->nombre_revista = $nombreRevista;
    }
    function get_Nombre_Revista()
    {
        return $this->nombre_revista;
    }
    function set_Id_Alumno($idalumno)
    {
        $this->id_alumno = $idalumno;
    }
    function get_Id_Alumno()
    {
        return $this->id_alumno;
    }
    function set_Id_Carrera($idcarrera)
    {
        $this->id_carrera = $idcarrera;
    }
    function get_Id_Carrera()
    {
        return $this->id_carrera;
    }
    function set_Id_Estatus($idestatus)
    {
        $this->id_estatus = $idestatus;
    }
    function get_Id_Estatus()
    {
        return $this->id_estatus;
    }
    function set_Id_Tipo_Propuesta($idtipoPropuesta)
    {
        $this->id_tipo_propuesta = $idtipoPropuesta;
    }
    function get_Id_Tipo_Propuesta()
    {
        return $this->id_tipo_propuesta;
    }
    function set_Desc_Propuesta($descPropuesta)
    {
        $this->des_propuesta = $descPropuesta;
    }
    function get_Desc_Propuesta()
    {
        return $this->des_propuesta;
    }
}

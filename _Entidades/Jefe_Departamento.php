<?php

/**
 * Definición de la Clase Jefe de Departamento
 * Propiedades
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */

class Jefe_Departamento extends Usuario
{
    /* Definición de Propiedades */
    private $id_jefe_Departamento;
    private $id_grado_estudio;
    private $id_departamento;
    private $id_puesto_trabajo;
    private $id_usuario;

    function set_Id_Jefe_Departamento($idJefeDepartamento)
    {
        $this->id_jefe_Departamento = $idJefeDepartamento;
    }
    function get_Id_Jefe_Departamento()
    {
        return $this->id_jefe_Departamento;
    }

    function set_Id_Grado_Estudio($idGrado)
    {
        $this->id_grado_estudio = $idGrado;
    }
    function get_Id_Grado_Estudio()
    {
        return $this->id_grado_estudio;
    }

    function set_Id_Puesto($idPuesto)
    {
        $this->id_puesto_trabajo = $idPuesto;
    }
    function get_Id_Puesto()
    {
        return $this->id_puesto_trabajo;
    }

    function set_Id_Usuario($idUsuario)
    {
        $this->id_usuario = $idUsuario;
    }
    function get_Id_Usuario()
    {
        return $this->id_usuario;
    }

    function set_Id_Departamento($idDepartamento)
    {
        $this->id_departamento = $idDepartamento;
    }
    function get_Id_Departamento()
    {
        return $this->id_departamento;
    }
}

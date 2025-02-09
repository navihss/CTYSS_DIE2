<?php

/**
 * Definición de la Clase Administrador
 * Propiedades
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */

require_once 'Usuario.php';

class Administrador extends Usuario
{
    /* Definición de Propiedades */
    private $id_administrador;
    private $id_puesto_trabajo;
    private $id_usuario;

    function set_Id_Administrador($idAdministrador)
    {
        $this->id_administrador = $idAdministrador;
    }
    function get_Id_Administrador()
    {
        return $this->id_administrador;
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
}

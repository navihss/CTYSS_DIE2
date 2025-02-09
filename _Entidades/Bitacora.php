<?php

/**
 * Definición de la Clase Bitácora
 * Propiedades
 * @author Rogelio Reyes Mendoza
 * Agosto 2016
 */

class Bitacora
{
    /* Definición de Propiedades */
    private $Id_Usuario_Genera;
    private $Id_Division;
    private $Id_Tipo_Evento;
    private $Id_Tema_Bitacora;
    private $Fecha_Evento;
    private $Id_Usuario_Destinatario;
    private $Descripcion_Evento;

    function set_Id_Usuario_Genera($idUsuarioGenera)
    {
        $this->Id_Usuario_Genera = $idUsuarioGenera;
    }
    function get_Id_Usuario_Genera()
    {
        return $this->Id_Usuario_Genera;
    }
    function set_Id_Division($idDivision)
    {
        $this->Id_Division = $idDivision;
    }
    function get_Id_Division()
    {
        return $this->Id_Division;
    }
    function set_Id_Tipo_Evento($idTipoEvento)
    {
        $this->Id_Tipo_Evento = $idTipoEvento;
    }
    function get_Id_Tipo_Evento()
    {
        return $this->Id_Tipo_Evento;
    }

    function set_Id_Tema_Bitacora($idTemaBitacora)
    {
        $this->Id_Tema_Bitacora = $idTemaBitacora;
    }
    function get_Id_Tema_Bitacora()
    {
        return $this->Id_Tema_Bitacora;
    }

    function set_Fecha_Evento($FechaEvento)
    {
        $this->Fecha_Evento = $FechaEvento;
    }
    function get_Fecha_Evento()
    {
        return $this->Fecha_Evento;
    }

    function set_Id_Usuario_Destinatario($idUsuarioDestinatario)
    {
        $this->Id_Usuario_Destinatario = $idUsuarioDestinatario;
    }
    function get_Id_Usuario_Destinatario()
    {
        return $this->Id_Usuario_Destinatario;
    }

    function set_Descripcion_Evento($descripcionEvento)
    {
        $this->Descripcion_Evento = $descripcionEvento;
    }
    function get_Descripcion_Evento()
    {
        return $this->Descripcion_Evento;
    }
}

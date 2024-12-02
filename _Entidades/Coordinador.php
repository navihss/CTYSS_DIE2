<?php

/**
 * Definición de la Clase Coordinador
 * Propiedades
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */

require_once 'Usuario.php';

class Coordinador extends Usuario {
    /* Definición de Propiedades */
    private $id_coordinador;
    private $id_grado_estudio;
    private $id_coordinacion;
    private $id_puesto_trabajo;
    private $id_usuario;
    
    function set_Id_Coordinador($idCoordinador){
        $this->id_coordinador=$idCoordinador;        
    }
    function get_Id_Coordinador(){
        return $this->id_coordinador;
    }

    function set_Id_Grado_Estudio($idGrado){
        $this->id_grado_estudio=$idGrado;        
    }
    function get_Id_Grado_Estudio(){
        return $this->id_grado_estudio;
    }
    
    function set_Id_Puesto($idPuesto){
        $this->id_puesto_trabajo=$idPuesto;        
    }
    function get_Id_Puesto(){
        return $this->id_puesto_trabajo;
    }

    function set_Id_Usuario($idUsuario){
        $this->id_usuario=$idUsuario;        
    }
    function get_Id_Usuario(){
        return $this->id_usuario;
    }        
    
    function set_Id_Coordinacion($idCoordinacion){
        $this->id_coordinacion=$idCoordinacion;        
    }
    function get_Id_Coordinacion(){
        return $this->id_coordinacion;
    }            
}

?>
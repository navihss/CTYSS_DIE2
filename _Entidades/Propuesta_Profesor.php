<?php

/**
 * Definición de la Clase Propuesta de Profesor
 * Propiedades
 * @author Rogelio Reyes Mendoza
 * Julio 2016
 */

class Propuesta_Profesor {

 /* Definición de Propiedades */
    private $Id_Propuesta;
    private $Titulo;
    private $Organismos_Colaboradores;
    private $Fecha_Registrada;
    private $horarios;
    private $Id_Tipo_Propuesta;
    private $Id_Profesor;
    private $Id_Division;
    private $Id_Estatus;
    private $requerimiento_alumnos;
    private $descripcion_tipo_propuesta;
    private $aceptar_inscripciones;

    function set_Id_Propuesta($idpropuesta){
        $this->Id_Propuesta=$idpropuesta;        
    }
    function get_Id_Propuesta(){
        return $this->Id_Propuesta;
    }    

    function set_Titulo($titulo){
        $this->Titulo=$titulo;        
    }
    function get_Titulo(){
        return $this->Titulo;
    }    

    function set_Organismos_Colaboradores($organismosColaboradores){
        $this->Organismos_Colaboradores=$organismosColaboradores;        
    }
    function get_Organismos_Colaboradores(){
        return $this->Organismos_Colaboradores;
    }    
    function set_Fecha_Registrada($fechaRegistrada){
        $this->Fecha_Registrada=$fechaRegistrada;        
    }
    function get_Fecha_Registrada(){
        return $this->Fecha_Registrada;
    }    

    function set_Horarios($asesoriaS){
        $this->horarios=$asesoriaS;        
    }
    function get_Horarios(){
        return $this->horarios;
    }    
    
    function set_Id_Tipo_Propuesta($idTipoPropuesta){
        $this->Id_Tipo_Propuesta=$idTipoPropuesta;        
    }
    function get_Id_Tipo_Propuesta(){
        return $this->Id_Tipo_Propuesta;
    }    
    function set_Id_Profesor($idProfesor){
        $this->Id_Profesor=$idProfesor;        
    }
    function get_Id_Profesor(){
        return $this->Id_Profesor;
    }

    function set_Id_Division($idDivision){
        $this->Id_Division=$idDivision;
    }
    function get_Id_Division(){
        return $this->Id_Division;
    }

    function set_Id_Estatus($idEstatus){
        $this->Id_Estatus=$idEstatus;        
    }
    function get_Id_Estatus(){
        return $this->Id_Estatus;
    }    
  
    function set_Requerimiento_Alumnos($requerimientoAlumnos){
        $this->requerimiento_alumnos=$requerimientoAlumnos;        
    }
    function get_Requerimiento_Alumnos(){
        return $this->requerimiento_alumnos;
    }    

    function set_Descripcion_Tipo_Propuesta($descripcionTipoPropuesta){
        $this->descripcion_tipo_propuesta=$descripcionTipoPropuesta;        
    }    
    function get_Descripcion_Tipo_Propuesta(){
        return $this->descripcion_tipo_propuesta;
    }    

    function set_Aceptar_Inscripciones($aceptarInscripciones){
        $this->aceptar_inscripciones = $aceptarInscripciones;
    }    
    function get_Aceptar_Inscripciones(){
        return $this->aceptar_inscripciones;
    }    
    
}

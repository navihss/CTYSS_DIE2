<?php
/**
 * Definición de la Clase Alumno Datos Generales
 * Propiedades
 * @author Rogelio Reyes Mendoza
 * Noviembre 2017
 */

class AlumnoDatosGenerales {
    /* Definición de Propiedades */
    private $Id_Inscripcion;
    private $Id_Alumno;
    private $Id_Carrera;
    private $Id_Estatus;
    private $Id_Propuesta;
    private $Email;
    private $Nombre;
    private $Apellido_Paterno;
    private $Apellido_Materno;
    private $Telefono_Fijo;
    private $Telefono_Celular;
    private $Carrera;
    private $Estatus;

    function set_Id_Inscripcion($idinscripcion){
        $this->Id_Inscripcion=$idinscripcion;        
    }
    function get_Id_Inscripcion(){
        return $this->Id_Inscripcion;        
    }
    
    function set_Id_Alumno($idAlumno){
        $this->Id_Alumno=$idAlumno;        
    }
    function get_Id_Alumno(){
        return $this->Id_Alumno;        
    }
    
    function set_Id_Carrera($idCarrera){
        $this->Id_Carrera=$idCarrera;
    }
    function get_Id_Carrera(){
        return $this->Id_Carrera;
    }    
    
    function set_Id_Estatus($idestatus){
        $this->Id_Estatus=$idestatus;
    }
    function get_Id_Estatus(){
        return $this->Id_Estatus;
    }

    function set_Id_Propuesta($idpropuesta){
        $this->Id_Propuesta=$idpropuesta;
    }
    function get_Id_Propuesta(){
        return $this->Id_Propuesta;
    }
    
    function set_Email($email){
        $this->Email=$email;
    }
    function get_Email(){
        return $this->Email;
    }

    function set_Nombre($nombre){
        $this->Nombre=$nombre;
    }
    function get_Nombre(){
        return $this->Nombre;
    }

    function set_Apellido_Paterno($apellidoPaterno){
        $this->Apellido_Paterno=$apellidoPaterno;
    }
    function get_Apellido_Paterno(){
        return $this->Apellido_Paterno;
    }

    function set_Apellido_Materno($apellidoMaterno){
        $this->Apellido_Materno=$apellidoMaterno;
    }
    function get_Apellido_Materno(){
        return $this->Apellido_Materno;
    }
    
    function set_Telefono_Fijo($telefonoFijo){
        $this->Telefono_Fijo=$telefonoFijo;
    }
    function get_Telefono_Fijo(){
        return $this->TelefonoFijo;
    }
    
    function set_Telefono_Celular($celular){
        $this->Telefono_Celular=$celular;
    }
    function get_Telefono_Celular(){
        return $this->Telefono_Celular;
    }
    
    function set_Carrera($carrera){
        $this->Carrera=$carrera;
    }
    function get_Carrera(){
        return $this->Carrera;
    }
    
    function set_Estatus($estatus){
        $this->Estatus=$estatus;
    }
    function get_Estatus(){
        return $this->Estatus;
    }
    /* Fin Definición de Propiedades */   
}


   

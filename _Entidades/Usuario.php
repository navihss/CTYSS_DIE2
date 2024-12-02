<?php
/**
 * Definición de la Clase Usuario
 * Propiedades
 * @author Rogelio Reyes Mendoza
 * Mayo 2016
 */

class Usuario {
    private $Id_Usuario;
    private $Contrasena;
    private $CorreoElectronico;
    private $Id_Tipo_Usuario;
    private $Id_Division;
    private $Fecha_Alta;        
    private $Nombre;
    private $ApellidoPaterno;
    private $ApellidoMaterno;
    private $Id_Genero;
    private $Id_Tipo_Baja;
    private $Activo;


    function set_Id_Usuario($idUsuario){
        $this->Id_Usuario = $idUsuario;
    }
    function get_Id_Usuario(){
        return $this->Id_Usuario;
    }

    function set_Contrasena($contrasena){
        $this->Contrasena = $contrasena;
    }
    function get_Contrasena(){
        return $this->Contrasena;
    }
    
    function set_Correo_Electronico($correoElectronico){
        $this->CorreoElectronico = $correoElectronico;
    }
    function get_Correo_Electronico(){
        return $this->CorreoElectronico;
    }

    function set_Id_Tipo_Usuario($idTipoUsuario){
        $this->Id_Tipo_Usuario = $idTipoUsuario;
    }
    function get_Id_Tipo_Usuario(){
        return $this->Id_Tipo_Usuario;
    }

    function set_Id_Division($idDivision){
        $this->Id_Division = $idDivision;
    }
    function get_Id_Division(){
        return $this->Id_Division;
    }
    
    function set_Fecha_Alta($idFechaAlta){
        $this->Fecha_Alta = $idFechaAlta;
    }
    function get_Fecha_Alta(){
        return $this->Fecha_Alta;
    }
    
    function set_Nombre($nombre){
        $this->Nombre = $nombre;
    }
    function get_Nombre(){
        return $this->Nombre;
    }

    function set_Apellido_Paterno($apellidoPaterno){
        $this->ApellidoPaterno = $apellidoPaterno;
    }
    function get_Apellido_Paterno(){
        return $this->ApellidoPaterno;
    }
    
    function set_Apellido_Materno($apellidoMaterno){
        $this->ApellidoMaterno = $apellidoMaterno;
    }
    function get_Apellido_Materno(){
        return $this->ApellidoMaterno;
    }    

    function set_Id_Genero($idGenero){
        $this->Id_Genero = $idGenero;
    }
    function get_Id_Genero(){
        return $this->Id_Genero;
    }    

    function set_Id_Tipo_Baja($idTipoBaja){
        $this->Id_Tipo_Baja = $idTipoBaja;
    }
    function get_Id_Tipo_Baja(){
        return $this->Id_Tipo_Baja;
    }    
    
    function set_Activo($activo){
        $this->Activo = $activo;
    }
    function get_Activo(){
        return $this->Activo;
    }
}

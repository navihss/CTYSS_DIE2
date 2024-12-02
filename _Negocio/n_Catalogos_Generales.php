<?php
/**
 * Interfaz de la Capa Negocio para la Clase Catalogos Generales.
 * @author Rogelio Reyes Mendoza
 * Junio 2016
 */
if(!isset($_POST['tabla_Catalogo']) and 
        !isset($_POST['tabla_Campos'])){
    header('Location: ../index.php');
}

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Catalogos_Generales.php');

$tabla_Catalogo = $_POST['tabla_Catalogo'];
$tabla_Campos = $_POST['tabla_Campos'];

//$tabla_Catalogo = 'estados_republica';
//$tabla_Campos = 'id_estado_republica, descripcion_estado_republica';

//switch ($tabla_Catalogo){
//    case 'estados_republica':
        $obj_Catalogos = new d_Catalogos_Generales();
//        $Condicion ='id_estado_republica > 1000';
        $Condicion ='';
        $OrderBy ='Descripcion';
        echo $obj_Catalogos->Obtener($tabla_Catalogo, $tabla_Campos, $Condicion, $OrderBy);
//        break;
//}        


?>
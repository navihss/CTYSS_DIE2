<?php

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Mis_Reportes.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');

session_start();

if(!isset($_SESSION["id_tipo_usuario"]) and
   !isset($_SESSION["id_usuario"])){
header('Location: index.php');
   }

$id_division=0;
if(isset($_SESSION["id_division"])){
    $id_division=$_SESSION["id_division"];
}
   
if(isset($_FILES["file_rpt"]["type"]))
{
    $validextensions = array("pdf");
    $temporary = explode(".", $_FILES["file_rpt"]["name"]);
    $file_extension = end($temporary);
    if ($_FILES["file_rpt"]["type"] == "application/pdf" && 
            ($_FILES["file_rpt"]["size"] < 500000000) && //Approx. 300kb puede ser subido.
            in_array($file_extension, $validextensions)) {
        
        if ($_FILES["file_rpt"]["error"] > 0){
            echo "Código de Error: " . $_FILES["file_rpt"]["error"] . "<br/><br/>";
        }
        else{
              $archivo = $_SERVER["DOCUMENT_ROOT"].'/CTYSS_DIE2/Docs/Reportes_Bimestrales/' . 
                      $_POST['id_usuario_doc'] . '_' . 
                      $_POST['id_carrera_doc'] . '_' . 
                      $_POST['id_ss'] . '_' . 
                      $_POST['numero_reporte_bi'] . '_' . 
                      $_POST['id_version'] .'_'.
                      $_POST['desc_corta_doc'] . '.pdf';                       
                      
              if (file_exists($archivo)) {
                //Boorrar el archivo que existe actualmente
                unlink($archivo);
              }
              $sourcePath = $_FILES['file_rpt']['tmp_name']; // Path del archivo subido
                
              if(move_uploaded_file($sourcePath,$archivo)) // Movemos el archivo subido a la carpeta especificada
              {
                //Actualizamos el estatus del documento a 2. Por Autorizar
                $id_ss = $_POST['id_ss'];
                $numero_reporte_bi =$_POST['numero_reporte_bi']; 
                $id_version = $_POST['id_version'];
                $id_estatus = $_POST['id_estatus'];
                $fecha_real_inicio = $_POST['fecha_Inicio_rpt'];
                $fecha_real_fin = $_POST['fecha_Termino_rpt'];
                $horas_laboradas = $_POST['horas_realizadas'];
                        
                $obj_ = new d_Alumno_Mis_Reportes();
                $array = json_decode($obj_->Actualizar_Estatus_Rpt_Enviado($id_ss, $numero_reporte_bi, 
                        $id_version, $id_estatus, $fecha_real_inicio, $fecha_real_fin, $horas_laboradas));

                $obj_Bitacora = new d_Usuario_Bitacora();
                $obj_miBitacora = new Bitacora();

                $descripcionEvento = $archivo;
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora(25);
                $obj_miBitacora->set_Id_Tipo_Evento(20);
                $obj_miBitacora->set_Id_Usuario_Genera($_POST['id_usuario_doc']);
                $obj_miBitacora->set_Id_Usuario_Destinatario('');
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($id_division);
                
                    if ($array->success){
                        $resultado_Bitacora ='';
                        $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
                        
                        echo "<span id='success' style='color:red;'><b>Archivo recibido correctamente...!!<br>" . 
                        $array->data->message . '<br>'. $resultado_Bitacora . "<b></span><br/><br>";                            
                    }
                    else{
                        unlink($archivo);
                        echo "<span id='success' style='color:red;'><b>El Archivo No se recibido correctamente. Vuelva a intentarlo!!<br>" . 
                        $array->data->message . "<b></span><br/><br>";                                                        
                    }
               }        
               else{
                        //Actualizamos el estatus del documento a 2. Sin Enviar                   
                        echo "<span id='success' style='color:red;'><b>El Archivo NO se recibió correctamente...!!<b></span><br/><br>";
               }                  
        }
    }
    else{
        echo "<span id='invalid' style='color:red;'><b>El Tamaño de archivo o el Tipo es Inválido.</b><span><br><br>";
    }
}
?>




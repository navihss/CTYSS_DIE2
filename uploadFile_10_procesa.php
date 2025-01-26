<?php
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_profesor_Mis_Docs.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');

session_start();

if(!isset($_SESSION["id_tipo_usuario"]) and
   !isset($_SESSION["id_usuario"])){
header('Location: index.php');
   }

$id_division=0;//
if(isset($_SESSION["id_division"])){
    $id_division=$_SESSION["id_division"];
}

if(isset($_FILES["file"]["type"]))
{
    $validextensions = array("pdf");
    $temporary = explode(".", $_FILES["file"]["name"]);
    $file_extension = end($temporary);
    if ($_FILES["file"]["type"] == "application/pdf" && 
            ($_FILES["file"]["size"] < 500000000) && //Approx. 300kb puede ser subido.
            in_array($file_extension, $validextensions)) {
        
        if ($_FILES["file"]["error"] > 0){
            echo "Código de Error: " . $_FILES["file"]["error"] . "<br/><br/>";
        }
        else{
              $archivo_original = 
                      $_POST['id_usuario'] . '_' .
                      $_POST['id_doc'] .'_' .
                      $_POST['nombre_archivo_original'];
              $archivo_nuevo =    
                      $_POST['id_usuario'] . '_' .
                      $_POST['id_doc'] .'_' .
                      $_POST['nombre_archivo_nuevo'];
              
              if (file_exists($archivo_original)) {
                unlink('Docs/Docs_Profesores/' . $archivo_original);
                }
//            else{
                $sourcePath = $_FILES['file']['tmp_name']; // Path del archivo subido
                
                if(move_uploaded_file($sourcePath,'Docs/Docs_Profesores/' . utf8_decode($archivo_nuevo))) // Movemos el archivo subido a la carpeta especificada
                    {
                        $obj_ = new d_profesor_Mis_Docs();
                        $array = json_decode($obj_->Agregar_Documento($_POST['id_usuario'],  $_POST['id_doc'], $archivo_nuevo));
                    
                        $obj_Bitacora = new d_Usuario_Bitacora();
                        $obj_miBitacora = new Bitacora();

                        $descripcionEvento = $archivo_nuevo;
                        $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                        $obj_miBitacora->set_Id_Tema_Bitacora(66); //Mis Documentos
                        $obj_miBitacora->set_Id_Tipo_Evento(20); //Envio de Documento
                        $obj_miBitacora->set_Id_Usuario_Genera($_POST['id_usuario']);
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
                            unlink($archivo_nuevo);
                            echo "<span id='success' style='color:red;'><b>El Archivo No se recibido correctamente. Vuelva a intentarlo!!<br>" . 
                                 $array->data->message . "<b></span><br/><br>";                                                        
                        }             
                    }        
                else{
                        //Actualizamos el estatus del documento a 2. Sin Enviar
                    
                        echo "<span id='success' style='color:red;'><b>El Archivo NO se recibió correctamente...!!<b></span><br/><br>";
                }
                  
//            }
        }
    }
    else{
        echo "<span id='invalid' style='color:red;'><b>El Tamaño de archivo o el Tipo es Inválido.</b><span><br><br>";
    }
}
?>



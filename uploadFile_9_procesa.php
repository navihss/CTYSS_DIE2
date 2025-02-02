<?php

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_Alumno_Mi_Ceremonia.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE/_Entidades/Bitacora.php');

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
              $archivo = 'Docs/Baja_de_Ceremonia/' . 
                      $_POST['id_usuario_doc'] . '_' .
                      $_POST['id_carrera_doc'] .'_' .
                      $_POST['id_ceremonia_doc'] .'_' .
                      $_POST['desc_corta_doc'] . '.pdf';
              if (file_exists($archivo)) {
                unlink($archivo);
            }
//            else{
                $sourcePath = $_FILES['file']['tmp_name']; // Path del archivo subido
//                $targetPath = "Docs/Servicio_Social/" . $_FILES['file']['name']; // Path destino para el archivo
                
                if(move_uploaded_file($sourcePath,$archivo)) // Movemos el archivo subido a la carpeta especificada
                    {
                        $obj_ = new d_Alumno_Mi_Ceremonia();
                        $array = json_decode($obj_->Solicitar_Baja_Ceremonia($_POST['id_ceremonia_doc']));

                        $obj_Bitacora = new d_Usuario_Bitacora();
                        $obj_miBitacora = new Bitacora();

                        $descripcionEvento = $archivo;
                        $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                        $obj_miBitacora->set_Id_Tema_Bitacora(45); //Inscripcion a ceremonia
                        $obj_miBitacora->set_Id_Tipo_Evento(65); //solicitud de ceremonia
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
        //                echo "<br/><b>Nombre del archivo:</b> " . $_FILES["file"]["name"] . "<br>";
        //                echo "<b>Tipo:</b> " . $_FILES["file"]["type"] . "<br>";
        //                echo "<b>Tamaño:</b> " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
        //                echo "<b>Archivo Temporal:</b> " . $_FILES["file"]["tmp_name"] . "<br>";                    
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



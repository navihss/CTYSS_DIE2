<?php
session_start();
if(!isset($_SESSION["id_usuario"]) and 
        !isset($_SESSION["id_tipo_usuario"]) and
        !isset($_SESSION["descripcion_tipo_usuario"]) and
        !isset($_SESSION["nombre_usuario"])){
    header('Location: ../index.php');
}

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Alumno_Mi_Servicio.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');

$id_division=0;
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
              $archivo = 'Docs/Carta_Terminacion/' . 
                      $_POST['id_usuario_doc'] . '_' . 
                      $_POST['id_carrera_doc'] . '_' . 
                      $_POST['id_ss_doc'] . '_' . 
                      $_POST['id_version_doc'] . '_' . 
                      $_POST['desc_corta_doc'] . '.pdf';
              if (file_exists($archivo)) {
                //Boorrar el archivo que existe actualmente
                unlink($archivo);
              }
              $sourcePath = $_FILES['file']['tmp_name']; // Path del archivo subido
                
              if(move_uploaded_file($sourcePath,$archivo)) // Movemos el archivo subido a la carpeta especificada
              {
                    //Actualizamos el estatus del documento a 2. Por Autorizar
                    $obj_ = new d_Alumno_Mi_Servicio();
                    $array = json_decode($obj_->Actualizar_Estatus_Doc_Enviado($_POST['id_ss_doc'], 
                            $_POST['id_documento_doc'], $_POST['id_version_doc'], 2));

                    $obj_Bitacora = new d_Usuario_Bitacora();
                    $obj_miBitacora = new Bitacora();

                    $descripcionEvento = $archivo;
                    $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                    $obj_miBitacora->set_Id_Tema_Bitacora(30); //Carta de terminación
                    $obj_miBitacora->set_Id_Tipo_Evento(20); //envio documento
                    $obj_miBitacora->set_Id_Usuario_Genera($_POST['id_usuario_doc']);
                    $obj_miBitacora->set_Id_Usuario_Destinatario('');
                    $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                    $obj_miBitacora->set_Id_Division($id_division);

                    if ($array->success){
                        $resultado_Bitacora ='';
                        $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
                        
                        echo "<span id='success' style='color:red;'><b>Archivo recibido correctamente...!!<br>" . 
                        $array->data->message . "<br>" . $resultado_Bitacora . "</b></span><br/><br>";                            
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





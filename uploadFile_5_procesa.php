<?php

require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_profesor_Mis_Propuestas.php');
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

if(isset($_FILES["file"]["type"]))
{
    $validextensions = array("pdf");
    $temporary = explode(".", $_FILES["file"]["name"]);
    $file_extension = end($temporary);
    if ($_FILES["file"]["type"] == "application/pdf" && 
            ($_FILES["file"]["size"] < 5000000) && //Approx. 300kb puede ser subido.
            in_array($file_extension, $validextensions)) {
        
        if ($_FILES["file"]["error"] > 0){
            echo "Código de Error: " . $_FILES["file"]["error"] . "<br/><br/>";
        }
        else{
              $archivo = 'Docs/Propuestas_Profesor/' . 
                      $_POST['id_usuario_prof'] . '_'. 
                      $_POST['id_propuesta_prof'] . '_'.                       
                      $_POST['id_version_prof'] . '_'.
                      $_POST['desc_corta_prof'] . '.pdf'; 
              if (file_exists($archivo)) {
                //Borrar el archivo que existe actualmente
                unlink($archivo);
              }
              $sourcePath = $_FILES['file']['tmp_name']; // Path del archivo subido
                
              if(move_uploaded_file($sourcePath,$archivo)) // Movemos el archivo subido a la carpeta especificada
              {
                    //Actualizamos el estatus del documento a 2. Por Autorizar
                    $obj_ = new d_profesor_Mis_Propuestas();
                    $array = json_decode($obj_->Actualizar_Estatus_Doc_Enviado($_POST['id_propuesta_prof'], 
                            $_POST['id_documento_prof'], $_POST['id_version_prof'], 2));

                    $obj_Bitacora = new d_Usuario_Bitacora();
                    $obj_miBitacora = new Bitacora();

                    $descripcionEvento = $archivo;
                    $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                    $obj_miBitacora->set_Id_Tema_Bitacora(60); //Mis Propuestas
                    $obj_miBitacora->set_Id_Tipo_Evento(20); //envio documento
                    $obj_miBitacora->set_Id_Usuario_Genera($_POST['id_usuario_prof']);
                    $obj_miBitacora->set_Id_Usuario_Destinatario('');
                    $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                    $obj_miBitacora->set_Id_Division($id_division);
                    
                    if ($array->success){
                        $resultado_Bitacora ='';
                        $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);
                        $resultado_Bitacora ='Propuesta enviada satisfactoriamente, verificar el estatus de su propuesta en 10 días hábiles';               
                        echo "<span id='success' style='color:red;'><b><br>" . 
                        $array->data->message . "<b>" . $resultado_Bitacora . "</span><br/><br>";                            
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





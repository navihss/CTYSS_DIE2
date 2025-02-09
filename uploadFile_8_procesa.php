<?php

require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Alumno_Mi_Servicio.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/d_Usuario_Bitacora.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Entidades/Bitacora.php');

session_start();

if (
    !isset($_SESSION["id_tipo_usuario"]) and
    !isset($_SESSION["id_usuario"])
) {
    header('Location: index.php');
}

$id_division = 0;
if (isset($_SESSION["id_division"])) {
    $id_division = $_SESSION["id_division"];
}

if (isset($_FILES["file"]["type"])) {
    //    $validextensions = array("jpeg", "jpg", "png", "pdf");
    $validextensions = array("pdf");
    $temporary = explode(".", $_FILES["file"]["name"]);
    $file_extension = end($temporary);
    //    if ((($_FILES["file"]["type"] == "image/png") || 
    //            ($_FILES["file"]["type"] == "image/jpg") || 
    //            ($_FILES["file"]["type"] == "image/jpeg") ||
    //            ($_FILES["file"]["type"] == "application/pdf")) && 
    //            ($_FILES["file"]["size"] < 300000) && //Aprox. 300kb puede ser subido.
    //            in_array($file_extension, $validextensions)) {
    if (
        $_FILES["file"]["type"] == "application/pdf" &&
        ($_FILES["file"]["size"] < 500000000) && //Approx. 300kb puede ser subido.
        in_array($file_extension, $validextensions)
    ) {

        if ($_FILES["file"]["error"] > 0) {
            echo "Código de Error: " . $_FILES["file"]["error"] . "<br/><br/>";
        } else {
            //              $archivo = 'Docs/Servicio_Social/' . $_FILES["file"]["name"];
            $archivo = 'Docs/Solicitud_Baja_Servicio_Social/' .
                $_POST['id_usuario_doc'] . '_' .
                $_POST['id_carrera_doc'] . '_' .
                $_POST['id_ss_doc'] . '_' .
                $_POST['desc_corta_doc'] . '.pdf';
            //            if (file_exists("Docs/Servicio_Social/" . $_FILES["file"]["name"])) {
            if (file_exists($archivo)) {
                //echo "<span id='invalid' style='color:red;'><b>". $_FILES["file"]["name"] . " Este archivo ya existe actualmente!.</b></span><br><br>";
                //Boorrar el archivo que existe actualmente
                unlink($archivo);
            }
            //            else{
            $sourcePath = $_FILES['file']['tmp_name']; // Path del archivo subido
            //                $targetPath = "Docs/Servicio_Social/" . $_FILES['file']['name']; // Path destino para el archivo

            if (move_uploaded_file($sourcePath, $archivo)) // Movemos el archivo subido a la carpeta especificada
            {
                //Actualizamos el estatus del documento a 2. Por Autorizar
                $obj_ = new d_Alumno_Mi_Servicio();
                $nota = '';
                $array = json_decode($obj_->Solicitar_Baja_SS(
                    $_POST['id_ss_doc'],
                    $_POST['id_usuario_doc'],
                    $_POST['nvo_id_estatus_doc'],
                    $_POST['nvo_id_baja_doc'],
                    $nota,
                    $_POST['id_carrera_doc']
                ));

                $obj_Bitacora = new d_Usuario_Bitacora();
                $obj_miBitacora = new Bitacora();

                $descripcionEvento = $archivo;
                $obj_miBitacora->set_Fecha_Evento(date('d-m-Y H:i:s'));
                $obj_miBitacora->set_Id_Tema_Bitacora(20); //servicio social
                $obj_miBitacora->set_Id_Tipo_Evento(60); //solicitud de baja
                $obj_miBitacora->set_Id_Usuario_Genera($_POST['id_usuario_doc']);
                $obj_miBitacora->set_Id_Usuario_Destinatario('');
                $obj_miBitacora->set_Descripcion_Evento($descripcionEvento);
                $obj_miBitacora->set_Id_Division($id_division);

                if ($array->success) {
                    $resultado_Bitacora = '';
                    $resultado_Bitacora = $obj_Bitacora->Agregar($obj_miBitacora);

                    echo "<span id='success' style='color:red;'><b>Archivo recibido correctamente...!!<br>" .
                        $array->data->message . '<br>' . $resultado_Bitacora . "<b></span><br/><br>";
                } else {
                    unlink($archivo);
                    echo "<span id='success' style='color:red;'><b>El Archivo No se recibido correctamente. Vuelva a intentarlo!!<br>" .
                        $array->data->message . "<b></span><br/><br>";
                }
                //                echo "<br/><b>Nombre del archivo:</b> " . $_FILES["file"]["name"] . "<br>";
                //                echo "<b>Tipo:</b> " . $_FILES["file"]["type"] . "<br>";
                //                echo "<b>Tamaño:</b> " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
                //                echo "<b>Archivo Temporal:</b> " . $_FILES["file"]["tmp_name"] . "<br>";                    
            } else {
                //Actualizamos el estatus del documento a 2. Sin Enviar

                echo "<span id='success' style='color:red;'><b>El Archivo NO se recibió correctamente...!!<b></span><br/><br>";
            }

            //            }
        }
    } else {
        echo "<span id='invalid' style='color:red;'><b>El Tamaño de archivo o el Tipo es Inválido.</b><span><br><br>";
    }
}

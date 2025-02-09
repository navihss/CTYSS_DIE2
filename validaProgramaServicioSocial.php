<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/CTYSS_DIE/_Datos/Conexion.php');

$clavePrograma = $_POST['clavePrograma'];
$claveCarrera = $_POST['claveCarrera'];
$id_division = $_POST['id_division'];

$jsondata = array();

$cnn = new Conexion();
$conn = $cnn->getConexion();

if ($conn === false) {
    $jsondata['success'] = 'ERROR';
    $jsondata['data']['message'] = $cnn->getError();
    echo json_encode($jsondata);
    exit();
}

/* Query parametrizado. */
$tsql = 'SELECT a.id_programa,a.id_carrera, b.descripcion_pss
             FROM programa_carrera a
                INNER JOIN programas_ss b ON a.id_programa = b.id_programa
             WHERE a.id_programa = ? AND a.id_carrera = ? AND a.id_division=?;';

/* Valor de los parámetros. */
$params = array($clavePrograma, $claveCarrera, $id_division);

/* Preparamos la sentencia a ejecutar */
$stmt = $conn->prepare($tsql);

/*Verificamos el contenido de la ejecución*/
if ($stmt) {
    /*Ejecutamos el Query*/
    $result = $stmt->execute($params);

    if ($result) {
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $descripcion_pss = $row['descripcion_pss'] . '<br>';
            $jsondata['success'] = 'EXISTE';
            $jsondata['data']['message'] = '--- Clave de Programa válida ---|' . $descripcion_pss;
            echo json_encode($jsondata);
            exit();
        } else {
            $jsondata['success'] = 'NOEXISTE';
            $jsondata['data']['message'] = '--- No existe esta Clave de Programa para la Carrera especificada ---';
            echo json_encode($jsondata);
            exit();
        }
    } else {
        $error = $stmt->errorInfo();
        $jsondata['success'] = 'ERROR';
        $jsondata['data']['message'] = ($error[2]);
        echo json_encode($jsondata);
        exit();
    }
} else {
    $jsondata['success'] = 'ERROR';
    $jsondata['data']['message'] = 'Error al ejecutar el Query.';
    echo json_encode($jsondata);
    exit();
}



//if (strcmp ($username, $user) == 0)
//{
//echo 'OK';
//}
//else
//{
//echo 'BAD';
//}
//    

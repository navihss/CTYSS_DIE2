<?php

	use App\Database\Connection;

	header('Content-Type: text/html; charset=UTF-8');
	require_once __DIR__ . '/../app/Database/Connection.php';
	require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Propuesta_Profesor.php');
	require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/zonaHoraria.php');
	require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Datos/d_Usuario_Bitacora.php');
	require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/_Entidades/Bitacora.php');

	$cnn = new Connection();
	$conn = $cnn->getConexion();
	                
	if( $conn === false )
	{
		throw new Exception($cnn->getError());     
	}

	$pkey_propuesta = $_POST['pkey_propuesta'];
	$objetivo = $_POST['objetivo_prop'];
	$definicion = $_POST['definicion_prob'];
	$metod = $_POST['metodo'];
	$temas = $_POST['temas_prop'];
	$indice = $_POST['indice_prop'];
	$resultados = $_POST['resultados_prop'];



	$sql = "INSERT INTO propuesta_tesis(id_propuesta, objetivo, definicion_problema, metodo, temas_utilizar, indice, resultados_esperados) VALUES ('$pkey_propuesta','$objetivo','$definicion','$metod','$temas','$indice','$resultados')" ;
	$sql2 = "UPDATE propuesta_version AS pv SET id_estatus = pp.id_estatus FROM propuestas_profesor AS pp WHERE pv.id_propuesta = pp.id_propuesta";
            
    /* Preparamos la sentencia a ejecutar */
    $stmt = $conn->prepare($sql);
    $stmt2 = $conn->prepare($sql2);
    if($stmt){
	    /*Ejecutamos el Query*/                
	    $result = $stmt->execute();
	    $result2 = $stmt2->execute();
	    echo "Propuesta enviada satisfactoriamente <br><a href='home.php'>Regresar</a>";
	    echo $pkey_propuesta;
	}

?>
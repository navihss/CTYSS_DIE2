<?php

/**
 * Maneja la encriptación y desencriptación de texto
 * utilizando la clase Seguridad.
 */

require_once __DIR__ . '/../app/Helpers/Seguridad.php';

use App\Helpers\Seguridad;

$suDato1 = '';
$resultadoEncriptado = '';
$descripcion = '';

if (isset($_POST["datoAEncriptar1"])) {
	$ini_array = parse_ini_file(__DIR__ . "/../Config/Aplicacion.ini");
	$suDato1 = $_POST["datoAEncriptar1"];

	if (($_POST["accionSeleccionada"] ?? '') === '1') {
		$descripcion = "Resultado de la Encriptación";
		$resultadoEncriptado = Seguridad::encriptar_AES($suDato1, $ini_array['db_semilla']);
	} else {
		$descripcion = "Resultado de la Desencriptación";
		try {
			$resultadoEncriptado = trim(Seguridad::desencriptar_AES($suDato1, $ini_array['db_semilla']));
		} catch (Exception $er) {
			$resultadoEncriptado = "Error en la Desencriptación: " . $er->getMessage();
		}
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Encriptación resultado</title>
	<link rel="stylesheet" type="text/css" href="assets/css/Seguridad.css">
</head>

<body>
	<div class="formulario">
		<h2><?php echo $descripcion; ?></h2>
		<form id="form1" method="post" action="Encriptar.php">
			<input
				name="resultado"
				id="r1"
				type="text"
				maxlength="250"
				size="70"
				value="<?php echo htmlspecialchars($resultadoEncriptado); ?>" />
			<br><br>
			<p style="text-align:center;">
				<input class="btn" type="submit" name="submit" value="Regresar" />
			</p>
		</form>
	</div>
</body>

</html>
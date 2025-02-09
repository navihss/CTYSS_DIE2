<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Encriptación resultado</title>
	<link rel="stylesheet" type="text/css" href="seguridad.css">
</head>

<body>
	<?php
	require_once 'Seguridad.php';

	$suDato1 = "";
	$resultadoEncriptado = "";
	$descripcion = "";

	if (isset($_POST["datoAEncriptar1"])) {
		$ini_array = parse_ini_file("Aplicacion.ini");
		$suDato1 = $_POST["datoAEncriptar1"];
		if ($_POST["accionSeleccionada"] == '1') {
			$descripcion = "Resultado de la Encriptación";
			$resultadoEncriptado = Seguridad::encriptar_aes($_POST["datoAEncriptar1"], $ini_array['db_semilla']);
		} else {
			$descripcion = "Resultado de la Desencriptación";
			try {
				$resultadoEncriptado = trim(Seguridad::desencriptar_aes($_POST["datoAEncriptar1"], $ini_array['db_semilla']));
			} catch (Exception $er) {
				$resultadoEncriptado = "Error en la Desencriptación.- " . $er->getMessage();
			}
		}
	}
	?>
	<div class="formulario">
		<h2><?php echo $descripcion ?></h2>
		<form id="form1" method='post' action='Encriptar.php'>
			<input name="resultado" id='r1' type="text" maxlength="250" size="70" value="<?php echo $resultadoEncriptado ?>" />
			<br>
			<br>
			<p align="center"><input class="btn" type="submit" name="submit" value="Regresar" /></p>
			<br>
		</form>
	</div>
</body>

</html>
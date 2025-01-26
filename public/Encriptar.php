<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Encriptación</title>
	<script>
		function Encrip_Dato(form) {
			document.getElementById('dato1').textContent = 'Dato a encriptar';
			document.getElementById('accionSeleccionada').value = '1';
		}

		function Desencrip_Dato(form) {
			document.getElementById('dato1').textContent = 'Dato a desencriptar';
			document.getElementById('accionSeleccionada').value = '2';
		}

		function valida_Datos() {
			var dat1 = document.getElementById('d1').value;
			if (!dat1) {
				alert('Debe capturar un dato válido.');
				return false;
			}
		}
	</script>
	<link rel="stylesheet" type="text/css" href="assets/css/Seguridad.css">
</head>

<body>
	<div class="formulario">
		<h2>Encriptar / Desencriptar información</h2>
		<form id="form1" name="form1x" method="post" action="Encriptar_Resultado.php" onsubmit="return valida_Datos();">
			<span>Su acción</span>
			<fieldset>
				<input type="radio" name="suAccion" value="1" onclick="Encrip_Dato(this.form);" checked> Encriptar
				<br>
				<input type="radio" name="suAccion" value="2" onclick="Desencrip_Dato(this.form);"> Desencriptar
			</fieldset>
			<br><br>
			<span id="dato1">Dato a encriptar</span>
			<br>
			<input name="datoAEncriptar1" id="d1" type="text" maxlength="100" required>
			<br>
			<input name="accionSeleccionada" id="accionSeleccionada" type="hidden" value="1">
			<br><br>
			<p style="text-align: center;">
				<input id="enviar" class="btn" type="submit" name="submit" value="Aceptar">
			</p>
			<br>
		</form>
	</div>
</body>

</html>
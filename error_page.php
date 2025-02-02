<?php
    session_start();

    if (isset($_SESSION['error_message'])) {
        echo "<pre>" . $_SESSION['error_message'] . "</pre>";
        unset($_SESSION['error_message']);  // Limpiar después de mostrar
    } else {
        echo "No hay errores para mostrar.";
    }
?>
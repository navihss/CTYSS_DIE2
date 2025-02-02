<?php
    // Datos de conexión
    $host = "127.0.0.1";
    $db = "die";
    $user = "postgres";
    $password = "";

    // Establecer conexión
    $conn = pg_connect("host=$host dbname=$db user=$user password=$password");

    if (!$conn) {
        echo "Error: No se pudo conectar a la base de datos.\n";
    } else {
        echo "¡Conexión exitosa con la base de datos!\n";

        // Realizar la consulta
        $query = 'SELECT * FROM usuarios LIMIT 10';
        $result = pg_query($conn, $query);

        if (!$result) {
            echo "Error en la consulta.\n";
        } else {
            // Mostrar los resultados
            while ($row = pg_fetch_assoc($result)) {
                echo "ID: " . $row['id_usuario'] . " - Nombre: " . $row['nombre_usuario'] . "\n";
            }
        }

        // Cerrar conexión
        pg_close($conn);
    }
?>

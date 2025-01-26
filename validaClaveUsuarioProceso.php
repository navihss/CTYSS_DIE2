<?php  
use App\Database\Connection;

    include "_Datos/Conexion.php";
    include "_Datos/d_Usuario.php";

    header('Content-Type: text/html; charset=UTF-8');
        
    
    $user=$_POST['claveUsuario'];
    $carrera=$_POST['id_carrera'];
    $carrera = substr($carrera, 1, 2);

    $jsondata = array();
    $obj_d_Usuario = new d_Usuario();
   

    
    $cnn = new Connection();
    $conn = $cnn->getConexion();
    
    if( $conn === false ){        
        $jsondata['success'] = 'ERROR';
        $jsondata['data']['message'] = $cnn->getError() ;
        echo json_encode($jsondata);
        exit();        
    } 
    
    /* Query parametrizado. */
    $tsql = 'SELECT id_usuario
            FROM usuarios
            WHERE id_usuario = :id_';

    /* Valor de los parámetros. */
    $params = array($user);

    /* Preparamos la sentencia a ejecutar */
    $stmt = $conn->prepare($tsql);

   /*Verificamos el contenido de la ejecución*/
    if($stmt){        
        /*Ejecutamos el Query*/
        $result = $stmt->execute($params); 

        if ($result){
            if($stmt->rowCount() > 0)
            {
                $jsondata['success'] = 'EXISTE';
                $jsondata['data']['message'] = 'Ya existe este usuario';
                echo json_encode($jsondata);
                exit();             
            }
            else {
                $jsondata['success'] = 'NOEXISTE';
                $jsondata['data']['message'] = 'No existe este usuario';
                echo json_encode($jsondata);
                exit();             
            }
        }
        else{
            $error = $stmt->errorInfo();
            $jsondata['success'] = 'ERROR';
            $jsondata['data']['message'] = ($error[2]);
            echo json_encode($jsondata);
            exit();                             
        }                
    } 
    else {    
        $jsondata['success'] = 'ERROR';
        $jsondata['data']['message'] = 'Error al ejecutar el Query.';
        echo json_encode($jsondata);
        exit();       
    }


    
    
?>

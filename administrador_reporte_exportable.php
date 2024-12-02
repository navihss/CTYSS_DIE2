<?php
ob_start();
?>

<?php
$tipo               = isset($_REQUEST['t']) ? $_REQUEST['t'] : 'excel';
$nombre_documento   =$_REQUEST['nombre_doc'] ;
$numero_reporte     =$_REQUEST['num_repo'] ;
$titulo_reporte     =$_REQUEST['titulo_reporte'] ;
$extension          = '.xls';
$etiqueta="";



if($tipo == 'word') $extension = '.doc';

// Si queremos exportar a PDF
if($tipo == 'pdf'){
    

    
    require_once ($_SERVER["DOCUMENT_ROOT"] .'/CTYSS_DIE2/lib/dompdf/dompdf_config.inc.php');
    $path=$_SERVER["DOCUMENT_ROOT"] ;
    
    $style= 
   '<html><body>
    <img src="'.$path.'/CTYSS_DIE2/css/images/ingenieria-unam.png" /><h4>'.$titulo_reporte.
    '</h4>
     <style><!--
    th {font-family: Arial; font-size: 0.85em;border-width: 0.5px;border-color: #729ea5; border: 1px solid #729ea5; background-color:#acc8cc}
    td {font-family: Arial; font-size: 0.85em;border-width: 0.5px;border: 1px solid #729ea5;}
    #redBG {background-color: red; color: #f0f0f0}
    table {border: 1px solid #729ea5;}
    body {
        margin: 15 auto;
        background-color: white;
        color: black;
        font-family: Helvetica, sans-serif, Verdana,Arial;
        font-size: 0.75em;
        width: 98%;
        height: 650px;
    }
    --></style>';
    

     //$dompdf = new DOMPDF(array('enable_remote' => true));
    $dompdf = new Dompdf();
    // Cargamos el contenido HTML.
    $dompdf->load_html(utf8_decode( $style.$_POST['datos_a_enviar'] ));
//     $dompdf->set_paper('letter', 'landscape');
    // Definimos el tama�o y orientaci�n del papel que queremos.
//     $dompdf->set_paper("A4", "portrait");

    if($numero_reporte == 1){
        $paper_size = array(0,0,3000,660);
        $dompdf->set_paper($paper_size);
    }else if($numero_reporte == 2){
        $paper_size = array(0,0,900,660);
        $dompdf->set_paper($paper_size);
    }else{
        $paper_size = array(0,0,1000,660);
        $dompdf->set_paper($paper_size);
    }
   
    
    
    
    $dompdf->render();
    ob_clean();
    $dompdf->stream($nombre_documento.".pdf");

} else{
    
    /*header("Content-type: application/vnd.ms-$tipo");
    header("Content-Disposition: attachment; filename=numero_alumnos_titulados".$extension);
    header("Pragma: no-cache");
    header("Expires: 0");  
    */
    
    
    header('Pragma: public');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
    header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
    header("Cache-Control: private");
    header("Pragma: cache");
    header('Expires: 0');
    header('Content-Transfer-Encoding: base64');
    header("Content-type: application/vnd.ms-$tipo"); // This should work for IE & Opera
    header("Content-Disposition: attachment; filename=$nombre_documento.$extension");
    
    
    
    
}

?>

<?php 
if($tipo != 'pdf'){
    
    if($tipo == 'word'){
       
        echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>";
    }
    if($tipo == 'excel'){
        
        
       // echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>";
        echo "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
    }
?>    


<head><title>Microsoft</title>
<meta http-equiv=Content-Type content=\"text/html; charset=us-ascii\"> <meta name=ProgId content=Excel.Sheet><meta name=Generator content=\"Microsoft Excel 11\">
<style><!-- 
@page
{
    size:21cm 29.7cmt;  /* A4 */
    margin:1cm 1cm 1cm 1cm; /* Margins: 2.5 cm on each side */
    mso-page-orientation: portrait;  
}

th {font-family: Arial; font-size: 0.65em;border-width: 0.5px;border-color: #729ea5; border: 1px solid #729ea5; background-color:#acc8cc}
td {font-family: Arial; font-size: 0.65em;border-width: 0.5px;border: 1px solid #729ea5;}
#redBG {background-color: red; color: #f0f0f0}
table {border: 1px solid #729ea5;}


@page Section1 { }
div.Section1 { page:Section1; }

--></style>
</head>
<body lang=ES-MX style='tab-interval:35.4pt'>

<div class=Section1>



<?php 
$path=$_SERVER["DOCUMENT_ROOT"] ;
echo '<img src="'.$path.'/CTYSS_DIE2/css/images/ingenieria-unam.png" /><h4>'.$titulo_reporte.'</h4>';

?>


</p>
&nbsp;
</p>
&nbsp;



<?php
if (isset($_POST['datos_a_enviar']) && $_POST['datos_a_enviar'] != '') echo utf8_decode($_POST['datos_a_enviar']); 
echo $etiqueta;
echo $tipo;
?>
</div>
</body>
</html>
<?php
}
ob_end_flush();
?>
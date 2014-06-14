<?php
include("pdf.php");
set_time_limit(0);
$conexion = mysql_connect('localhost', 'root', 'admin1') or die("problemas en la conexion");
mysql_select_db('crmdb', $conexion) or die("problemas en la bd");

//Select the Products you want to show in your PDF file
$result=mysql_query("select * from CRMcustomer",$conexion);

$pdf=plantilla();
$datos=array();
while($res=  mysql_fetch_array($result,MYSQL_ASSOC)){    
    $datos[]=array(
        'Id'=>$res[id],
        'Nombre'=>$res[custname],
        'Contacto'=>$res[contact],
        'Telefono'=>$res[contact_phone],
        'Email'=>$res[contact_email],
        'Direccion'=>$res[cust_address]
    );
}
$width=array(4,50,22,22,25,80);
$align=array('C','J','L','L','L','J');
$pdf->tabla($datos,'TITULO DE LA TABLA',8,$align,5,1,$width,2,'C');

$namefile="testfile";

$pdf->Output($namefile.".pdf",'I');

?>

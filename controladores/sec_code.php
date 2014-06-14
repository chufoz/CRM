<?php
extract($_REQUEST);

//***************************************************************
include("header.inc.php");
if($_POST['btn_code']){
show_code($_POST['code']);
}
else{
frmmain();
}
EndHTML();

//***************************************************************
function frmmain(){
echo "
<table width='100%' border=0>
<tr><td>
        <fieldset>
        <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;C&oacute;digo de acceso a configuraci&oacute;n de dispensarios: &nbsp;</legend>
        <form name='soft' method='POST' action=".$HTTP_SERVER_VARS['PHP_SELF'].">
                <table border=0 width=300>
                        <tr width=150>
                                <td>Codigo de seguridad:</td>
                                <td align='left'><input type='text' name='code' value='' size=13 maxlenth=11></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                                <td colspan=2 align='center'><input type='Submit' name='btn_code' value='Aceptar'></td>
                        </tr>
                </table>
        </fieldset>
</tr><td>
</table>
";
echo "<br><br><br><br>";
}
//*****************************************************************************
function message($msg_done, $red_liga, $red_msg) {

        echo "<table align=center width=500 border=0>";
        echo "<tr><td><br></td></tr>";
        echo "<tr>";
        echo "<td align=center>";
        echo $msg_done;
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td align = center>";
        echo "<b><a href='$red_liga'>$red_msg</a></b>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
}
//***********************************************************************************************************
function gen_password($code){
    $prefix=substr($code,0,4);
    $sufix=substr($code,4);

    $estacion = hexdec($prefix);
    $fecha = hexdec($sufix);
    
    $d=substr($fecha,6,2);
    $m=substr($fecha, 4,2);
    $y=substr($fecha, 0,4);

    $string=$d.$estacion.$m;
    $passwd = strtoupper(sprintf("%08s",dechex($string)));

    //Insertar registro de bitacora
    $logdate="$y-$m-$d";
    $strsql="INSERT INTO CRMsecuritylog(idusuario,estacion,fecha) VALUES('".$GLOBALS['USERID']."','$estacion','$logdate')";
    $res=mcq($strsql,$db);
        
    return $passwd;
}
//**********************************************************************************************************
function show_code($code){

$sec_code = gen_password($code);

$msg = "<b><font size=2>Clave de acceso de configuraci&oacute;n de dispensarios</font><br><br><font color='navy' size=3>$sec_code</font></b><br><br>";
message($msg,$_SERVER['PHP_SELF'],'Regresar');

}
?>

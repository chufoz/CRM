<?php

(int) $id = $_POST['idsoft'];
if($id > 0) {
   process($id);
  }
 
function process($id) {
 $sel="SELECT c.idsoft,c.nombre,m.idmodule,m.nombre as mn,m.idsoft as id FROM CRMsoft_modules as m INNER JOIN CRMsoft_catalog as c On c.idsoft=m.idsoft WHERE c.idsoft='$id' ";
 $data = unserialize(base64_decode($_POST["cdb"]));
 $conexion = mysql_connect($data['host'], $data['user'], $data['pass']);
 $link = mysql_db_query($data['database'], $sel);
 if(mysql_num_rows($link)>=1){
     $table="<br><table width='70%' class='r' border='0' >";
 while($res=  mysql_fetch_array($link)){
        $table.="<tr>
                        <td><input type='checkbox' name='idmodule[]' value='$res[idmodule]'>.-$res[mn]<input type='hidden' value='$res[mn]' name='modulos'></td>
                        </tr>";        
 }
 $table.="</table>";
 }else{
     $table='Sin modulos';
 }
    if (!function_exists("json_encode")){
	require_once("../json.php");
	$json = new Services_JSON;
	echo $json->encode($table);
    }
    else{
        echo json_encode($table);
    }
   mysql_close($conexion);
 }

?>

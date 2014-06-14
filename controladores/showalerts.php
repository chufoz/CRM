<?php
//clase para obtener las alertas por cliente
//
(int) $id = $_POST['id'];
if($id > 0) {
   process($id);
  }
 
function process($id) {
   $result ="SELECT CRMentity.CRMcustomer,CRMalerts.createdate,CRMentity.eid,CRMalerts.iduser,CRMalerts.eid as aeid,CRMalerts.severidad 
                        FROM CRMalerts  INNER JOIN CRMentity ON CRMalerts.eid = CRMentity.eid 
                        where CRMalerts.status='1' and CRMentity.CRMcustomer=$id ORDER BY CRMalerts.severidad,CRMalerts.createdate ASC";
    $data = unserialize(base64_decode($_POST["cdb"]));
     $conexion = mysql_connect($data['host'], $data['user'], $data['pass']);
      $link = mysql_db_query($data['database'], $result);
    $table="<table border=1><th style=font-size:13px>Entidad</th><th style=font-size:13px>Creador</th><th style=font-size:13px>Severidad</th><th style=font-size:13px>Creacion</th>";
   while($row = mysql_fetch_array($link)){
       $user=  user($row['iduser']);
         if($row['severidad']=='1'){
                     $color='#C62F2F';
                     $name='Alta';
                 }else{
                     $color='#E59D0D';
                     $name='Media';
                 }
   $table.="<tr><td align=center style=font-size:12px><a href=edit.php?e=$row[eid] target=_blank>$row[eid]</a></td><td>$user[FULLNAME]</td><td bgcolor=$color align=center style=color:white;font-size:12px;>$name</td><td>$row[createdate]</td></tr>";
   }
   $table.="</table>";
   
    if (!function_exists("json_encode")){
	require_once("../json.php");
	$json = new Services_JSON;
	echo $json->encode($table);
    }
    else{
        echo json_encode($table);
    }
   //mysql_close($db);
 }
 function user($id){
      $result = mysql_query("SELECT  FULLNAME From CRMloginusers where id=$id");
      $data = unserialize(base64_decode($_POST["cdb"]));
      $conexion = mysql_connect($data['host'], $data['user'], $data['pass']);
      $link = mysql_db_query($data['database'], $result);
       $res=  mysql_fetch_array($result);
     return $res;
 }
?>

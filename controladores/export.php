<?
exit();
extract($_REQUEST);
include("config.inc.php");
include("getset.php");


extract($_REQUEST);

$sql = "SELECT administrator FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
$result = mcq($sql,$db);
$list = mysql_fetch_array($result);
$list = $list[administrator];

		if ($list<>"Yes" && $list<>"yes") {
			print "<td><img src='error.gif'>&nbsp;&nbsp;This section can only be accessed by user who have administrator rights set to<br> their <b>user profile</b>. Only entering the correct password is not enough. Please<br>contact your system administrator.<br><br></td></tr>";
			print "<tr><td>[<a class='bigsort' href='javascript:history.back(1);' style='cursor:pointer'>back to main administration page</a>]</td></tr>";
			exit;
		}
$filename = "CRM-DBexport-" . $database[$repository_nr] . "-" . date("Fj-Y-Hi") . "h.CRM";

header("Content-Type: text");
header("Content-Disposition: attachment; filename=$filename" );
header("Content-Description: PHP4 Generated Data" );
header("Window-target: _top");

mysqlbackup(FALSE,"\n");

exit;

function mysqlbackup($structure_only, $crlf) {  
  global $query,$host,$database,$repository_nr,$encrypt;
 
 $res=mysql_list_tables($database[$repository_nr]);  
 $nt=mysql_num_rows($res);  

 $query = array();

 for ($a=0;$a<$nt;$a++) {  
  $row=mysql_fetch_row($res);  
  $tablename=$row[0];
	if ($encrypt) {
		print base64_encode(ereg_replace("\n","","DROP TABLE IF EXISTS $tablename")) . "\n";
	} else {
		print (ereg_replace("\n","","DROP TABLE IF EXISTS $tablename")) . ";\n";
	}

   $result=mysql_query("SHOW CREATE TABLE $tablename");
   if ($result != FALSE && mysql_num_rows($result) > 0) {
    $tmpres		   = mysql_fetch_array($result);
    $pos           = strpos($tmpres[1], ' (');
    $tmpres[1]     = substr($tmpres[1], 0, 13)
                     . $tmpres[0]
                     . substr($tmpres[1], $pos);
				 
    $b = $tmpres[1];

	if ($encrypt) {
		print base64_encode(ereg_replace("\n","",$b)) . "\n";
	} else {
		print (ereg_replace("\n","",$b)) . ";\n";
	
	}

   }
   mysql_free_result($result);
 }


 if ($structure_only == FALSE) {
	$tabres = mysql_query("SHOW TABLES");
	while ($tabs = @mysql_fetch_row($tabres)) {
			  $tablename=$tabs[0];
			 // print "Data for table $tablename<br>";
			  $result = mysql_query("SELECT * FROM $tablename");
			  $fields_cnt   = @mysql_num_fields($result);
			  while ($row = @mysql_fetch_row($result)) {
			   $table_list     = '(';
			   for ($j = 0; $j < $fields_cnt; $j++) {
				$table_list .= mysql_field_name($result, $j) . ', ';
			   }
			   $table_list = substr($table_list, 0, -2);
			   $table_list     .= ')';

			   $sql3 = 'INSERT INTO ' . $tablename 
											   . ' VALUES (';
			   for ($j = 0; $j < $fields_cnt; $j++) {
				if (!isset($row[$j])) {
				 $sql3 .= ' NULL, ';
				} else if ($row[$j] == '0' || $row[$j] != '') {
				 $type          = mysql_field_type($result, $j);
				 // a number
				 if ($type == 'tinyint' || $type == 'smallint' || $type == 'mediumint' || $type == 'int' ||
									$type == 'bigint'  ||$type == 'timestamp') {
				  $sql3 .= $row[$j] . ', ';
				 }
				 // a string
				 else {
				  $dummy  = '';
				  $srcstr = $row[$j];
				  for ($xx = 0; $xx < strlen($srcstr); $xx++) {
				   $yy = strlen($dummy);
				   if ($srcstr[$xx] == '\\')   $dummy .= '\\\\';
				   if ($srcstr[$xx] == '\'')   $dummy .= '\\\'';
				   if ($srcstr[$xx] == "\x00") $dummy .= '\0';
				   if ($srcstr[$xx] == "\x0a") $dummy .= '\n';
				   if ($srcstr[$xx] == "\x0d") $dummy .= '\r';
				   if ($srcstr[$xx] == "\x1a") $dummy .= '\Z';
				   if (strlen($dummy) == $yy)  $dummy .= $srcstr[$xx];
				  }
				  $sql3 .= "'" . $dummy . "', ";
				 }
				} else {
				 $sql3 .= "'', ";
				} // end if
			   } // end for
			   $sql3 = ereg_replace(', $', '', $sql3);
			   $sql3 .= ")".$crlf;

				if ($encrypt) {
					print base64_encode(ereg_replace("\n","",$sql3)) . "\n";
				} else {
					print (ereg_replace("\n","",$sql3)) . ";\n";
				}
			//   out(1,$sql);   

			  } 
			  @mysql_free_result($result);
			  } 
	} // end while tables
 }
?>
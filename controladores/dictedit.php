<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This file handles language administration stuff like exporting
 * and importing of language packs, completion, adding, etc.
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
 extract($_REQUEST);
if ($export) {
	include("config.inc.php");
	include("getset.php");

	
		if (!$pack_to_export) {
		    die('No language pack to export given! ($pack_to_export)');
		}
		$u=1;
	$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID = '$pack_to_export'";
	if ($debug) { print "\nSQL: $sql\n"; }
	$result= mcq($sql,$db);								
	while ($resarr=mysql_fetch_array($result)){
		$t[$u] = $resarr[LANGID] . "|||" . $resarr[TEXTID] . "|||" . $resarr[TEXT] . "";
		$t[$u] = ereg_replace("\n","",$t[$u]);
		$u++;
	}
	
	header("Content-Type: text/plain");
	header("Content-Disposition: attachment; filename=$pack_to_export.CRM.txt" );
	header("Content-Description: CRM Generated Data" );
	header("Window-target: _top");
//	print "<pre>";
	print "# CRM LANGUAGE PACK EXPORT FILE - Pack $pack_to_export.\n";
	print "# Generated " . date("F j, Y, H:i") . " on CRM version $CRM_VERSION.\n";
	print "PACK|||$pack_to_export|||" . ($u-1) . "\n";
	for ($x=1;$x<$u;$x++ ) {
//	    print $t[$x] . "\015\012";
		$t[$x] = ereg_replace("\n","",$t[$x]);
		$t[$x] = ereg_replace("\015","",$t[$x]);
		$t[$x] = ereg_replace("\012","",$t[$x]);
//	    $t[$x] = urlencode($t[$x]);
		print $t[$x] . "\n";
	}
	uselogger("Language export: $pack_to_export","");
	exit;
}

if ($_FILES['userfile']['tmp_name'] || $packurl) {
			include("config.inc.php");
			include("header.inc.php");
// Read contents of uploaded file into variable

			if ($packurl) {
				$file = base64_decode($packurl);
				//$fp=fopen($packurl,"r");
			} else {
				//$fp=fopen($_FILES['userfile']['tmp_name'],"rb");
				$file = $_FILES['userfile']['tmp_name'];
			}
			//while (!feof($fp)) {
			 //   $fc[$t] = fgets($fp,1024);
				//$t++;
			//}
			
			$fc = file($file);
				
			for ($x=0;$x<sizeof($fc);$x++ ) {
		   			$tmp=split("\|\|\|",$fc[$x]);
					$fc1[$x] = addslashes($tmp[0]);
					$fc2[$x] = addslashes($tmp[1]);
					$fc3[$x] = addslashes($tmp[2]);
			}

			//fclose($fp);
//			print "FILE RECEIVED! - $_FILES['userfile']['tmp_name']  ($_FILES['userfile']['name'])";
			$bla = $fc[2];
			$header=split("\|\|\|",$bla);
			$pack = $header[1];
			printbox("<br>Processing language pack $header[1] containing $header[2] entries.<br>");
//			print_r($header);
	
			$sql= "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='ENGLISH'";
			if ($debug) { print "\nSQL: $sql\n"; }
			$result= mcq($sql,$db);
			$result= mysql_fetch_array($result);
			
			$outp .= "Your current default language pack (ENGLISH) has $result[0] entries.<br>";
			if ($result[0]==0) {
				$JUST_DO_IT=1;
				$result[0]=$header[2];
				$outp .= "Overridden - you have no language packs at all<br>";
			}
			if (!$result[0]==$header[2]) {
			    $outp .= "<font color='#FF0000'>W A R N I N G - Values don't match!<br>";
				}

			printbox($outp);
			unset($outp);

		
			
				$sql= "SELECT TEXTID FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='ENGLISH'";
				if ($debug) { print "\nSQL: $sql\n"; }
				$result= mcq($sql,$db);								
				while ($resarr=mysql_fetch_array($result)){
							if (!in_array($resarr[TEXTID],$fc2)) {
									$outp .= "<font color='#FF0000'>Missing text identifier \"$resarr[TEXTID]\"<br>";
									$wrong=1;
							}
				}
				if ($wrong && !$JUST_DO_IT) {
							//$outp .= "Language pack cannot be imported.";
							//printbox($outp);
							//exit;

				} else {
						if ($JUST_DO_IT) {
							$outp .= "Installing base language pack<br>";
						} else {
						$outp .= "Pack is OK, all TEXTID's match.<br>";
						}
				}

				$sql= "SELECT DISTINCT LANGID FROM $GLOBALS[TBL_PREFIX]languages";
				$result= mcq($sql,$db);
					while ($resarr=mysql_fetch_array($result)){
						$tmparr[$p] = $resarr[LANGID];	  // All LANGID's in array
						$p++;
					}
					if (in_array($pack,$tmparr)) {
							$outp.="You already have a language pack $pack installed. This pack is <b>added</b> to the database, enlarging it.<br>It's better to first (or now) delete pack $pack, and import your file again.";
					}
					printbox($outp);
					unset($outp);
						for ($p=3;$p<sizeof($fc2);$p++) {

								$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]languages(LANGID,TEXTID,TEXT) VALUES('$fc1[$p]','$fc2[$p]','$fc3[$p]')";
								 mcq($sql,$db);
						}
						$outp .= "<br><font color='#FF0000'>$p Language entries were imported";
						$outp .= "<br><br>[<a class='bigsort' href='dictedit.php?tab=80' style='cursor:pointer'>back to main language management page</a>]";
						printbox($outp);
						uselogger("Language or language pack deployed ($pack, $p records)","");
			exit;
}


if ($lanlist) {
	include("config.inc.php");
	include("getset.php");
	DisplayCSS();
	print "<table border='1' width='<? echo $printbox_size;?>' bgcolor='#F2F2F2' cellspacing='0' cellpadding='4' bordercolor='#CECECE'>";
	print "<tr><td><b>Identifier</b></td><td><b>English value</b></td></tr>";
	$sql= "SELECT DISTINCT TEXTID FROM $GLOBALS[TBL_PREFIX]languages";
	$result= mcq($sql,$db);
	while ($resarr=mysql_fetch_array($result)){
			
			$sql1 = "SELECT TEXT FROM $GLOBALS[TBL_PREFIX]languages WHERE TEXTID='$resarr[TEXTID]' AND LANGID='$lanlist'";
			$result1= mcq($sql1,$db);
			$resarr1=mysql_fetch_array($result1);
			if ($resarr1[0]) {
				print "<tr><td>$resarr[TEXTID]</td>";
				print "<td>$resarr1[0]</td>";
				print "</tr>";
				$p++;
			}
	}
	print "<tr><td colspan=2>$p entries found.</td></tr>";
	exit;
}

include("header.inc.php");

print "</td></tr></table>";
AdminTabs();
MainAdminTabs("bla");
print "<table><tr><td>&nbsp;&nbsp;</td><td>";

$sql = "SELECT administrator FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
		$result = mcq($sql,$db);
		$list = mysql_fetch_array($result);
		$list = $list[administrator];

		if ($list<>"Yes" && $list<>"yes") {
			print "<table width='90%' border=0><tr><td colspan=4><font size=+2>$lang[adm]</font><br>" . $title . " CRM</td></tr>";
			print "<tr><td colspan=4><hr></td></tr>";
			print "<tr><td><img src='error.gif'>&nbsp;&nbsp;This section can only be accessed by user who have administrator rights set to<br> their <b>user profile</b>. Only entering the correct password is not enough. Please<br>contact your system administrator.<br><br></td></tr>";
			print "<tr><td>[<a class='bigsort' OnClick='javascript:history.back();' style='cursor:pointer'>back to main administration page</a>]</td></tr>";
			exit;
		}
?>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
<!--
function poplanlist(i)	{
				newWindow = window.open('dictedit.php?lanlist=' + i,'HelpWindow' ,'width=600,height=300,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
		}
//-->
</SCRIPT>
<?
if ($admpassword=="*NONE*") {
		$password=$admpassword;
} 

$sql = "SELECT administrator FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";

$result= mcq($sql,$db);
$bla= mysql_fetch_array($result);
if ($bla[administrator]<>"yes") {

	if ($password<>$admpassword) {
					print "<tr><td><form name='pwd' method='post'>";
					if ($password) {
						print "<b>$lang[wrongpwd].</b><br>";
					} else {
						print "<b>$lang[havetoenterpwd]:</b><br>"; 
					}
						print "<br><input type='password' name='password'><br><br><input type='submit' name='knop' value='Log in'>";
					   print "<input type='hidden' name='admin' value='on'>";
					   print "</form></td><tr></table></body></html>";
					   exit;
	}
} else {
//	print $lang[nopwd];
}

print "<br><br>";

?>
 	<SCRIPT LANGUAGE="javascript" SRC="cookies.js"></SCRIPT>
<?

if ($DLP) {
	GetPacksFromProjectPage();
	exit;
}

if ($deletepack) {
		$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='$deletepack'";
		mcq($sql,$db);
		printbox("Language pack $deletepack was deleted!");
		uselogger("Delete language pack $deletepack","");
}

if ($pack_to_delete) {

		$a = "Are you sure you want to delete language pack $pack_to_delete?";
		$a .= "<br><br><img src='arrow.gif'>&nbsp;<a href='dictedit.php?deletepack=$pack_to_delete&password=" . $password . "' class='bigsort'>yes</a>";
		printbox($a);
		print "</body></html>";
		exit;
}

if ($add && $val && $id_new) {
		$sql= "INSERT INTO $GLOBALS[TBL_PREFIX]languages(LANGID,TEXTID,TEXT) VALUES ('$add','$id_new','$val')";
		mcq($sql,$db);
		print "Entry added";
}
if ($add) {
		
		
		print "<table border='1' bgcolor='#F2F2F2' cellspacing='0' cellpadding='4' bordercolor='#CECECE'>";
		print "<tr><td colspan=2><b>Add a language entry for language $add</b></td></tr>";
		print "<form name=addlan method='POST'>";
		print "<tr><td>Identifier</td><td><input type='text' name='id_new' size=20></td></tr>";
	    print "<tr><td>$add</td><td><input type='text' name='val' size=100><input type='hidden' name='add' value='$add'>";
		print "<tr><td colspan=2><input type='submit' name='blabla' value='add'></td></tr>";
		print "<tr><td colspan=2><img src='arrow.gif'>&nbsp;<a href='dictedit.php' class='topnav'>back</td></tr>";
		print "</table></body></html>";
		exit;
}

if ($newlang) {

				$sql= "SELECT DISTINCT LANGID FROM $GLOBALS[TBL_PREFIX]languages";
				$result= mcq($sql,$db);
					while ($resarr=mysql_fetch_array($result)){
						$tmparr[$p] = $resarr[LANGID];	  // All LANGID's in array
						$p++;
					}
					if (in_array($newlang,$tmparr)) {
							$outp.="You already have a language pack $newlang installed. It will not be added again.";
							printbox($outp);
							
					} else {
			
							$sql= "INSERT INTO $GLOBALS[TBL_PREFIX]languages(LANGID) VALUES('$newlang')";
							mcq($sql,$db);
					
							printbox("Your language entry has been added....");
					}
				unset($newlang);
}


if ($languagecomplete) {
				print "<br><br>";
				if ($val) {
						if (!$val=="") {
								$outp = "Added your text to pack $languagecomplete, TEXTID $TEXTID<br><br>[<a class='bigsort' href='dictedit.php?tab=80' style='cursor:pointer'>back to main language management page</a>]";
								$sql= "INSERT INTO $GLOBALS[TBL_PREFIX]languages(LANGID,TEXTID,TEXT) VALUES('$languagecomplete','$TEXTID','$val')";
								mcq($sql,$db);
								printbox($outp);
								unset($outp);
						}
				}

				 $sql= "SELECT TEXTID FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='$languagecomplete'";
				  if ($debug) { print "\nSQL: $sql\n"; }
				  $result= mcq($sql,$db);								
				  $t=1;
				  while ($resarr=mysql_fetch_array($result)){
				  		$fc2[$t] = $resarr[TEXTID];
						$fc1[$t] = $resarr[TEXT];
						$t++;
				  }
				  
				   

				$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='ENGLISH'";

				$result= mcq($sql,$db);								
				while ($resarr=mysql_fetch_array($result)){
							if (!in_array($resarr[TEXTID],$fc2)) {
								$outp = "<table border=0 width=100%><tr><td><b>TEXTID</td><td><b>English value</td><td><b>$languagecomplete value</td></tr>";
							    $outp .= "<tr><td><form name='bladiebla' method='POST'><input type='hidden' name='languagecomplete' value='$languagecomplete'>";
								$sql= "SELECT TEXT FROM $GLOBALS[TBL_PREFIX]languages WHERE TEXTID='$resarr[TEXTID]' AND LANGID='ENGLISH'";
								
								$result1= mcq($sql,$db);
								$result1= mysql_fetch_array($result1);
								
								$outp .= "$resarr[TEXTID]<input type='hidden' name='TEXTID' value='$resarr[TEXTID]'></td><td>" . $result1[TEXT] . "</td><td><input type='text' name='val' size='75'>";
								$outp .="</td><td><input type='submit' name='knop' value='next'></td></tr></table>";
								$printbox_size="75%";
								printbox($outp);
								?>
								<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
								<!--
									 document.bladiebla.val.focus();
								//-->
								</SCRIPT>
								<?
								print "</body></html>";
								exit;
							}
			}
									?>
						<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
						<!--
						document.location='dictedit.php?end=all&password=<? echo $password;?>';
						//-->
						</SCRIPT>
						<?
							print "</body></html>";
		exit;
}

if ($import) {
			$a = "<table>";
			$a .=  "<tr><form method='POST' name='bla' ENCTYPE='multipart/form-data'><td colspan=6><br><INPUT TYPE='hidden' name='MAX_FILE_SIZE' value='52428800'><INPUT NAME='userfile' TYPE='file'>&nbsp;&nbsp;&nbsp;&nbsp;<a OnClick='javascript:pophelp(9)' class='topnav' cursor='click' style='cursor: help'><img src='info.gif'></a></td></tr>";
			$a .= "<tr><td><input class='txt' type=submit name=sb value='Upload'></form></td></table>";
			$legend = "Select pack file&nbsp;";
			printbox($a);
			exit;
}
if (!$newlang) {
		$legend = "CRM translation procedure";
		$buf = "You can stop and continue this session whenever you want.<br><br><img src='arrow.gif'>&nbsp;<a href='dictedit.php?import=1&password=$password' class='bigsort'>import a pack file</a>&nbsp;<img src='arrow.gif'>&nbsp;<a href='dictedit.php?password=$password&DLP=1' class='bigsort'>Install a pack file from the CRM project page (automated)</a>";
		$sql= "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='ENGLISH'";
		if ($debug) { print "\nSQL: $sql\n"; }
		$result= mcq($sql,$db);
		$result= mysql_fetch_array($result);
		$maxlan = $result[0];
//		printbox("There are $maxlan entries in the default language (ENGLISH)");
		unset($tmparr);
		unset($p);
		$p=1;
		$sql= "SELECT DISTINCT LANGID FROM $GLOBALS[TBL_PREFIX]languages";
		$result= mcq($sql,$db);
		while ($resarr=mysql_fetch_array($result)){
			if ($resarr[LANGID]<>"GLOBAL" && $resarr[LANGID]<>"ENGLISH") {
				$tmparr[$p] = $resarr[LANGID];	  // All LANGID's in array
				$p++;
			}
		}
		$bla = "<br><BR>There is/are " . ($p-2) . " language pack(s) in this version.<br>(besides the default ENGLISH and GLOBAL arrays)<br> <img src='arrow.gif'>&nbsp;<a class='bigsort' href='dictedit.php?export=1&pack_to_export=ENGLISH&password=$password'>export</a> <img src='arrow.gif'>&nbsp;<a href=\"javascript:poplanlist('ENGLISH');\" class='bigsort'>english language id list</a> <img src='arrow.gif'>&nbsp;<a class='bigsort' href='lan_entries.php?edlan=ENGLISH&password=" . $password . "'>edit</a><br><br><fieldset><table>";
		$cs = array();
		for ($c=0;$c<sizeof($tmparr)+1;$c++) {
			    if ($tmparr[$c]<>"") {
				  $sql= "SELECT TEXTID,TEXT FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='$tmparr[$c]'";

				  $result= mcq($sql,$db);								
				  $t=1;
				  unset($fc2);
				  while ($resarr=mysql_fetch_array($result)){
				  		$fc2[$t] = $resarr[TEXTID];	
						$t++;
						if ($resarr['TEXTID']=="CHARACTER-ENCODING") {
							$cs[$c] = $resarr['TEXT'] . " (from pack file)";

						} 
				  }
//				  print "<pre>$tmparr[$c]";
//				  print_r($fc2);

				$sql= "SELECT TEXTID FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='ENGLISH'";
				$result= mcq($sql,$db);		
				unset($missing);
				while ($resarr=mysql_fetch_array($result)){
							if (!in_array($resarr[TEXTID],$fc2)) {
									$missing++;
									$wrong=1;
							}
				}

				$sql= "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='$tmparr[$c]'";
			    $result= mcq($sql,$db);
			    $result= mysql_fetch_array($result);
			    if (!$missing) {
			        $bla .= "<tr><td>Language $tmparr[$c] is complete.</td><td><img src='arrow.gif'>&nbsp;<a class='bigsort' href='dictedit.php?export=1&pack_to_export=$tmparr[$c]&password=$password'>export</a>&nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<a class='bigsort' href='dictedit.php?pack_to_delete=$tmparr[$c]&password=$password'>delete</a>&nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<a class='bigsort' href='dictedit.php?add=$tmparr[$c]&password=$password'>add an entry</a>&nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<a href=\"javascript:poplanlist('$tmparr[$c]');\" class='bigsort'>language id list</a>&nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<a href='lan_entries.php?edlan=$tmparr[$c]' class='bigsort'>edit</a><br>";
			    } else {
					$ins = "";
			        $bla .= "<tr><td>Language $tmparr[$c] is missing $missing entries!</td><td><img src='arrow.gif'>&nbsp;<a class='bigsort' href='dictedit.php?export=1&pack_to_export=$tmparr[$c]&password=$password'>export</a>&nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<a class='bigsort' href='dictedit.php?pack_to_delete=$tmparr[$c]&password=$password'>delete</a>&nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<a class='bigsort' href='dictedit.php?add=$tmparr[$c]&password=$password'>add an entry</a>&nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<a href=\"javascript:poplanlist('$tmparr[$c]');\" class='bigsort'>language id list</a>&nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<a href='lan_entries.php?edlan=$tmparr[$c]' class='bigsort'>edit</a>&nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<a href='dictedit.php?languagecomplete=$tmparr[$c]&password=$password' class='bigsort'> complete </a>&nbsp;<br>";
			    }
			 } else {
			     //printbox("ERROR::Empty Language Identifier found!");
			 }
			 if (!$cs[$c]) {
				$cs[$c] = "ISO-8859-1 (by default)";
			 }
			 if ($c==0) {
				$cs[0] = "";
			 }
			 $bla.= "</td><td NOWRAP>" . $cs[$c] . "</td></tr>";
		}
			$bla .= "</table></fieldset>";
			$bla = $buf . $bla;

			$bla .= "<u>Create new language or language mask</u>: <form name='new' method='POST'><input type='text' name='newlang'><input type='hidden' name='password' value='$password'>&nbsp;&nbsp;<input type='submit' name=bla value='Submit'></form>";

			$bla .= "<br><br>If you want to use an other character encoding to be used than ISO-8859-1, add a text field called 'CHARACTER-ENCODING' (in capitals) to your language pack and give it the correct character encoding version.";


			$bla .= "<br><br>If you want a clear view of all used language tags, you can go into translation mode. This will cause all language tag names to be displayed behind the actual value. It will only be visible by you e.g. you won't bother other users.<br><br>&nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<A OnClick=\"javascript: setCookie('language_display','yes');alert('You are now in tag display mode. Refresh the screen or click a link to see all tagnames.');\" class='bigsort' style='cursor:pointer'>go to tag display mode</a>&nbsp;&nbsp;&nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<A OnClick=\"javascript: setCookie('language_display','');alert('You left tag display mode. Refresh the screen or click a link to hide the tagnames.');\" class='bigsort' style='cursor:pointer'>leave tag display mode</a>";

			$bla .= "<br><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?tab=80' style='cursor:pointer'>back to main administration page</a>";
			$printbox_size = "90%";
			printbox($bla);


print "</body></html>";
}



function printbox($msg)
{
		global $printbox_size,$legend;
		
		if (!$printbox_size) {
			$printbox_size = "70%";
		}
		
		print "<table border='0' width='$printbox_size'><tr><td colspan=2><fieldset>";
		if ($legend) {
			print "<legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$legend</legend>";
		}
		print $msg . "</fieldset></td></tr></table><br>";
	
		unset($printbox_size);
		$legend = "";

} // end func

?>
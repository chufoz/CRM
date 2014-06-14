<?
require("config.inc.php");
require("functions.php");

print "\nCRM-CTT File import function\n\n";

if (CommandlineLogin("","","")) {

	print "OK, so you want to bulk upload files into CRM...\n";
	print "To which entity number do you want to attach the files?\n";
	print " EID > ";
	$eid = readln();

	$eid = $eid * 1;

	$sql = "SELECT category FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='$eid' AND deleted<>'y'";
	$result = mcq($sql,$db);
	$row = mysql_fetch_array($result);
	if ($row[category]) {
		print "Adding files to \"" . $row[category] . "\"\n";
	} else {
		print "This entity number is not valid (non-existed, empty category, or deleted)\nBye!\n";
		exit;
	}


	print "Enter the directory in which the files are located:\n";
	print " DIR > ";
	$dir = readln();

	if (substr($dir,(strlen($dir)-1),1) <> "/") {
		$dir .= "/";
	}

	$files = array();
	$names = array();

	if ($handle = @opendir($dir)) {
	   while (false !== ($file = readdir($handle))) { 
		   if ($file != "." && $file != "..") { 
				array_push($files, $dir . "/" . $file);
				array_push($names, $file);
		   } 
	   }
	   closedir($handle); 
	} else {
		print "Fatal - directory not found!\n";
		exit;
	}

	print "\n";

	print "Type 'yes' if you really want to upload " . sizeof($files) . " files:\n";
	print " CONFIRM > ";
	$confirm = readln();
	if ($confirm<>"yes") {
		print "Ok, bye!\n";
		exit;
	}

	for ($x=0;$x<sizeof($files);$x++) {
			$fp=fopen($files[$x] ,"rb");
			$fs = filesize($files[$x]);
			$filecontent=fread($fp,$fs);
			fclose($fp);
			$filecontenttomail = $filecontent;
			$filenametomail = $names[$x];
//			$filecontent = addslashes($filecontent);
//			$sql="INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,filetype,username) VALUES('$eid','$filecontent','" . $names[$x] . "','$fs','unknown','" . $username . "')";
//			mcq($sql,$db);
//			$attachment = mysql_insert_id();
			$attachment = AttachFile($eid,$names[$x],$filecontent,"entity","[imported]");
			print "Processing: $x $files[$x]\n";
			$t++;
	}

	print "$t files attached to EID $eid\n";

	print "\nBye!\n\n";
} else {
	// access denied
	print "\nExiting...\n\n";
}


// Journalling function (Entity ID, Message)
function journal($eid,$msg,$JournalType="entity") {
		$msg = addslashes($msg);
		$sql = "INSERT INTO " . $GLOBALS[TBL_PREFIX] ."journal (eid,user,message,type) VALUES('$eid','" . $GLOBALS[USERID] . "','$msg','" . $JournalType ."')";
		mcq($sql,$db);
}

?>
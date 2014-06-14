<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * WEBDAV move script
 * Moves a file from the database to the WEBDAV area
 * Requires FILEID
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

// Include header, functions, languages etc.

// Session is stored in $GLOBALS['session_id']

extract($_REQUEST);

require("header.inc.php");


if (strtoupper($EnableWebDAVSubsystem)<>"YES") { 
	print "<table><tr><td><img src='error.gif'>&nbsp; WebDAV subsystem is disabled</td></tr></table>";
	EndHTML;
	exit;
}


// Make sure all directories are there - the EID numbers of entities with files
for ($i=0;$i<64;$i++) {
	if ($host[$i]) {
		if (!is_dir(getcwd() . "/webdav_fs/" . $i)) {
			@mkdir(getcwd() . "/webdav_fs/" . $i);
		}
	}
}	


if ($_REQUEST['AttAll'] && $_REQUEST['eid']) {

	if (CheckEntityAccess($_REQUEST['eid'])<>"ok") {
		print "<img src='error.gif'> Not allowed<br>";
		exit;
	}

	$dir = getcwd() . "/webdav_fs/" . $GLOBALS['repository_nr'] . "/" . $_REQUEST['eid'] . "/";

	if (is_dir($dir)) {
	   if ($dh = opendir($dir)) {
		   while (($file = readdir($dh)) !== false) {
				if (!strstr($file,"CRMID") && $file<>"." && $file <> "..") {
					
					   // hier file attachen
					   $filename = $file;

					   $eidw = $_REQUEST['eid'];

						if (CheckEntityAccess($_REQUEST['eid'])<>"ok") {
							print "<img src='error.gif'> Not allowed<br>";
							exit;
						}

						$target_file = getcwd() . "/webdav_fs/" . $repository_nr . "/" . $eidw . "/" . $filename;

						$fsize = filesize($target_file);
						$ftype = filetype($target_file);

						$cont = file_get_contents($target_file);
						
						print "Checking in $target_file owned by $GLOBALS[USERNAME] $fsize $ftype<br>";

//						$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,filetype,username,checked) VALUES('" . $eidw . "','" . addslashes($cont) ."','" . $filename . "','" . $fsize . "','" . $ftype . "','" . $GLOBALS[USERNAME] . "','in')";
	
//						mcq($sql,$db);
		
						AttachFile($eidw, $filename, $cont, "entity", $ftype);					
						
						unlink($target_file);

						journal($eidw,"File $filename attached from WebDAV");
				}
		   }
		   closedir($dh);
	   }
	}

	?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				document.location='edit.php?e=<? echo $eidw;?>';
			//-->
			</SCRIPT>
		<?
} elseif ($fileid || $filename_to_ins) {
	

	
	if ($fileid) {
			// OK, we know the file. Fetch it!


			$sql="SELECT $GLOBALS[TBL_PREFIX]binfiles.fileid,content,filetype,filename,koppelid,checked,checked_out_by FROM $GLOBALS[TBL_PREFIX]binfiles,$GLOBALS[TBL_PREFIX]blobs WHERE $GLOBALS[TBL_PREFIX]binfiles.fileid=$GLOBALS[TBL_PREFIX]blobs.fileid AND $GLOBALS[TBL_PREFIX]binfiles.fileid='$fileid'";
			mcq($sql,$db);
			$output = mcq($sql,$db);
			$row = @mysql_fetch_array($output);

//			print $sql . "<br>";

			$eid = $row['koppelid'];

			ExpireFormCache($eid);

			$target_dir = getcwd() . "/webdav_fs/" . $repository_nr . "/" . $eid;
			
			$ftr = split("\.",$row['filename']);
			$top = sizeof($ftr) - 1;
			for ($x=0;$x<$top;$x++) {
				$filename .= $ftr[$x] . ".";
			}

			$filename .= "CRMID_" . $row['fileid'] . "." . $ftr[$top];

			$target_file = getcwd() . "/webdav_fs/" . $repository_nr . "/" . $eid . "/" . $filename;

			$webdav_dir = "/" . $repository_nr . "/" . $eid . "/" . $filename;

			if (!is_dir($target_dir)) {
				if (!@mkdir($target_dir)) {
					print "Error. Directory could not be created ($target_dir)";
					exit;
				}
			} else {
				//print "DEBUG Dir is already there<BR>";
			}
	}



	
	if ($filename_to_ins) {
	
			// Check in a file which was manually uploaded to webdav

			$filename = base64_decode($filename_to_ins);

			if (!$eidw) {
				print "Error, which entity?";
				exit;
			}

			if (CheckEntityAccess($eidw)<>"ok") {
				print "<img src='error.gif'> Not allowed<br>";
				exit;
			}

			$target_file = getcwd() . "/webdav_fs/" . $repository_nr . "/" . $eidw . "/" . $filename;

			$fsize = filesize($target_file);
			$ftype = filetype($target_file);

			$cont = file_get_contents($target_file);
			
			print "Checking in $target_file owned by $GLOBALS[USERNAME] $fsize $ftype";

			//$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,filetype,username,checked) VALUES('" . $eidw . "','" . addslashes($cont) ."','" . $filename . "','" . $fsize . "','" . $ftype . "','" . $GLOBALS[USERNAME] . "','in')";
			
			AttachFile($eidw, $filename, $cont, "entity", $ftype);		

			ExpireFormCache($eidw);
			
			qlog("AttachFile($eidw, $filename, $cont, entity, $ftype)");

			//mcq($sql,$db);

			unlink($target_file);

			journal($eidw,"File $filename attached from WebDAV");

			?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				document.location='edit.php?e=<? echo $eidw;?>';
			//-->
			</SCRIPT>
			<?
			exit;
	
	} elseif ($checkin=="true") { // check some file in
		
		// First check if it is locked

		$result = false;
            
		$query = "SELECT owner, token, expires, exclusivelock FROM $GLOBALS[TBL_PREFIX]webdav_locks WHERE path = '" . addslashes($webdav_dir) . "'";
		//print $query;
		$res = mcq($query,$db);

		if($res) {
			$row = mysql_fetch_array($res);
			mysql_free_result($res);

			if($row) {
					print "<img src='error.gif'>&nbsp;" . $lang['stillchecked1'] . " " . $row['owner'] . $lang['stillchecked2'];
					EndHTML();
					exit;
			}
		}

		if (!file_exists($target_file)) {
			print "error - expected $target_file file isn't there";
			EndHTML;
			exit;
		}

		// OK, so the file is there, and it's not locked. Yeah!

		// Determine file ID
		
		$tmp = split("\.",$webdav_dir);

		$top = sizeof($tmp) -2;

		$top = split("_",$tmp[$top]);

		$fileid = $top[1];

		$cont = file_get_contents($target_file);

		if (CheckEntityAccess($eid)<>"ok") {
				print "<img src='error.gif'> Not allowed!<br>";
				exit;
		}


		journal($eid,"File $fileid ($target_file) checked back in from WebDAV");

		$sql = "UPDATE $GLOBALS[TBL_PREFIX]binfiles SET checked='in',checked_out_by='-' WHERE fileid='" . $fileid . "' AND koppelid='" . $eid . "'";
		mcq($sql,$db);
		$sql = "UPDATE $GLOBALS[TBL_PREFIX]blobs SET content='" . addslashes($cont) . "' WHERE fileid='" . $fileid . "'";
		mcq($sql,$db);
		unlink($target_file);

		ExpireFormCache($eid);

		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
			document.location='edit.php?e=<? echo $eid;?>';
		//-->
		</SCRIPT>
		<?


		exit;
	} elseif ($row['checked']=="out") { // the file is already checked out
		print "<img src='error.gif'>&nbsp;A file with the same name belonging to this entity is already checked out. Please check in that file first, before checking this file out. The file was last checked out by " . GetUserName($row['checked_out_by']) . "<br>";
		print "<STYLE>\nA {behavior: url(#default#AnchorClick);}\n</STYLE>";
	    
		$auth_string = "SESS_EXPI_" . $GLOBALS['USERID'] . ":" . $GLOBALS['session'] . "@";

	} else {

		if (CheckEntityAccess($eid)<>"ok") {
				print "<img src='error.gif'> Not allowed!<br>";
				exit;
		}

		
		$sql = "UPDATE $GLOBALS[TBL_PREFIX]binfiles SET checked='out', checked_out_by='" . $GLOBALS[USERID] . "' WHERE fileid='" . $fileid . "'";
		
		mcq($sql,$db);

		journal($eid,"File $fileid ($target_file) checked out to WebDAV");
	}


	
	
//	print $target_dir . " <BR> " . $target_file;
	
	if (file_exists($target_file)) {
				print "Error. The file " . $row['filename'] . " already exists in WebDav file system. Please check in the current file first before transferring this new file.";
				exit;
	}

	$fp = @fopen($target_file,"w");

	  // Write $somecontent to our opened file.
	   if (@fwrite($fp, $row['content']) === FALSE) {
		   echo "Cannot write to file ($target_file)";
		   exit;
	   }
	   
	   echo "File $target_file set";
	   
	   fclose($fp);

		chmod($target_file, 0700);
	
       
		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
			document.location='edit.php?e=<? echo $eid;?>';
		//-->
		</SCRIPT>
		<?



} else {
	print "ERROR - no file to work with";
}

?>

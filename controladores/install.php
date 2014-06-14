<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This is the CRM installer - it should be run from your browser,
 * not from the command line.
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

extract($_REQUEST);

// The main version number to install with
$version = "3.4.3";

$cfgfile = stripslashes($cfgfile);

if ($step=='dl') {
			header("Content-Type: text");
			header("Content-Disposition: attachment; filename=config.inc.php");
			header("Content-Description: PHP4 Generated Data");
			header("Window-target: _top");

			// Push attachment from variable

			print stripslashes($cfgfile);
			exit;
} elseif ($step=='write') {
	$trywrite = 1;
    if ($fp = @fopen("config.inc.php","w")) {
		$cfgfile2 = stripslashes($cfgfile);
		fputs($fp,$cfgfile2);
		fclose($fp);
        $write = "gelukt";
    } else {
        $write = "niet gelukt";
    }

	$step = 4;
}

if ($_REQUEST['AddRepository']=="1") {
	include("header.inc.php");
	print "</td></tr></table>";
	AdminTabs();
	MainAdminTabs();
	$to_tabs = array("Current repositories","New repository");
	$tabbs["main"] = array("admin.php" => "<b>Back</b>", "comment" => "bla");
	$tabbs["Current repositories"] = array("admin.php?reposman=1&resman=1&manageres=1&1156969438" => "Current repositories", "comment" => "View the list of currently configured repositories");
	$tabbs["New repository"] = array("install.php?AddRepository=1&step=1" => "New repository", "comment" => ".");	
	$navid = "New repository";
	InterTabs($to_tabs, $tabbs, $navid);

	print "<table width=100%><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><table>";

	MustBeAdmin();
	$legend = "Add a repository&nbsp;";
} else {
	print "<title>CRM-CTT $version installation procedure</title>";
	$legend = "CRM-CTT $version Installation procedure&nbsp;";
	?>
	<META HTTP-EQUIV="Expires" CONTENT="0">
	<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
	<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
	</head>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--

	// Write the correct stylesheet into the document
	if (navigator.appName.indexOf('Microsoft') > -1) {
		// IE stylesheet
		document.write('<link href="crm_dft.css" rel="stylesheet" type="text/css">');
		} else {
		// NS stylesheet (gheghe, the same :))

		document.write('<link href="crm_dft.css" rel="stylesheet" type="text/css">');
	}	
			//-->
	</SCRIPT>

	<body bgcolor="#ffffff" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" link="#336699" text="#333333" alink="white" vlink="#666666"">
<?	
}
if ($AddRepository==1){
//	print "<center><table><tr><td>Add a repository</td></tr></table></center>";	
} else {
	print "<center><img src='crm.gif'></center>";

}

 if ((@filesize("config.inc.php")>0) && ($trywrite<>1) && (!$_REQUEST['AddRepository']) && !$ovrw) {
	printbox("This version of CRM-CTT is already installed.<br><br><center>To run install again, delete or empty the config.inc.php file.");
	print "</body></html>";
	exit;

}

$GLOBALS['Installer'] = true;


if (!$step) {
		$sv = "<font color='#66CC00'>OK</font> (" . phpversion() . ")";
		$path = realpath(".");
		$webpath = $_SERVER['PHP_SELF'];
		$webhost = $_SERVER['SERVER_NAME'];

		$a = (get_loaded_extensions());

		if (!in_array("mysql",$a)) {
				$mysqlsupport = "<img src='error.gif'>&nbsp;<font color='#FF0000'>Error. MySQL PHP support not found. This is fatal, CRM-CTT only works with a PHP installation which supports MySQL</font>";
				$fatal = 1;
		} else {
				$mysqlsupport = "<font color='#66CC00'>OK</font> (Available)";
		}

		if (in_array("gd",$a)) {
				$gd = "<font color='#66CC00'>OK</font> (Installed)";
			} else {
				$gd = "<font color='#FF0000'>Not installed. You will not be able to generate pictures.</font>";
			}
		if (ini_get('register_globals')=="1" || strtolower(ini_get('register_globals'))=="on" || strtolower(ini_get('register_globals'))=="yes") {

				$rg = "<font color='#66CC00'>OK</font> (On)";
		} else {
				$rg = "<img src='error.gif'>&nbsp;<font color='#FF0000'>The PHP variable \"REGISTER_GLOBALS\" is disabled (0). This will work, though it's still expirimental!</font>";
				//$fatal = 1;
		}
		if (ini_get('allow_call_time_pass_reference')=="1" || strtolower(ini_get('allow_call_time_pass_reference'))=="on" || strtolower(ini_get('allow_call_time_pass_reference'))=="yes") {
				$rg = "<font color='#66CC00'>OK</font> (On)";
		} else {
				$rg = "<img src='error.gif'>&nbsp;<font color='#FF0000'>The PHP variable \"allow_call_time_pass_reference\" is disabled (0). This will not work.</font>";
				$fatal = 1;
		}
		$dir = ini_get("session.save_path");

		if ($f = tempnam($dir,"BLA")) {
				$fa = "<font color='#66CC00'>OK</font> ($f)";
				unlink($f);
		} else {
				$fa = "<img src='error.gif'>&nbsp;<font color='#FF0000'>The PHP variable \"session.save_path\" is pointing to directory " . $dir .". CRM-CTT was not able to write a file in this directory.<br>This is essential. This installation procedure will ask you for a new file location in the next step. Without a temporary file location, you cannot continue.</font>";
				$tmpfileerror=1;
		}

	
	
//		printheaderinst("&nbsp;<br>&nbsp;<b>Welcome to the CRM-CTT $version installation procedure</b>&nbsp;<br>&nbsp;");
		
		
		$bla = "<br>";
		$bla .= "<center><table border='0'>";
		$bla .= "<tr><td colspan=2>This procedure will help you install CRM-CTT by asking you a few questions. It consists of 4 steps.";
		$bla .= "<br><br>";
		$bla .= "This will result in a complete working copy of CRM-CTT on your system.<br><br>";
		$bla .= "<b>Things you must know before starting:</b><br><br>";
		$bla .= "- Your MySQL server hostname<br>";
		$bla .= "- Your MySQL server username and password<br>";
		$bla .= "- A (preferably) non-existing database name (crmdb might be a good idea)<br>";
		$bla .= "- The future administrator's email address<br>";
		$bla .= "- Your company name (CRM-CTT main title)<br>";
		$bla .= "- A name and password for the initial administrative login account<br><br>";
		$bla .= "Two external libraries are needed by CRM-CTT, though they are optional. CRM-CTT uses<br>the GD library";
		$bla .= "to create the statistics image, and the PEAR classes to support the<br>Microsoft&reg; Excel&reg; ";
		$bla .= "export function. If you don't have GD or PEAR, you can still<br>use CRM-CTT except for those two functions.<br>";
		$bla .= "<br><br>";
		$bla .= "</td></tr>";
		$bla .= "<tr><td colspan=2><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Checks&nbsp;</legend><table width=100% border=0><tr><td colspan=2>";
		$bla .= "<b>Required</b>:</td></tr><tr><td>";
		$bla .= "PHP version: </td><td>$sv</td></tr>";
		$bla .= "<tr><td>";
		$bla .= "MySQL support: </td><td>$mysqlsupport</td></tr>";
		$bla .= "<tr><td>";
		$bla .= "REGISTER_GLOBALS: </td><td>$rg</td></tr>";
		if (ini_get('magic_quotes_gpc') == "Yes" || ini_get('magic_quotes_gpc') == "On" || ini_get('magic_quotes_gpc') == "1") {
			$bla .= "<tr><td>";
			$bla .= "Magic quotes: </td><td><img src='error.gif'>&nbsp;<font color='#FF0000'>On</font> (CRM-CTT will try to set it off, but this can cause problems!)</td></tr>";
		} else {
			$bla .= "<tr><td>";
			$bla .= "Magic quotes: </td><td><font color='#66CC00'>OK</font> (Off)</td></tr>";			
		}
		$bla .= "<tr><td>";
		$bla .= "Temp file space: </td><td>$fa</td></tr>";
		$bla .= "<tr><td colspan=2><br><b>Optional</b>:</td></tr>";
		$bla .= "<tr><td>";
		$bla .= "GD Library: </td><td>$gd</td></tr>";
		$bla .= "<tr><td>";
		if (@include("PEAR.php")) {
			$pear = "<font color='#66CC00'>OK</font> (Installed)";
		} else {
			$pear = "<font color='#FF0000'>Error. You will not be able to use Microsoft&reg; Excel&reg; formatted exports</font>";
		}
		$bla .= "PEAR Classes:</td><td>$pear</td></tr>";
		
		$b = get_perms("config.inc.php");
		if ($fp = @fopen("config.inc.php","w")) {
			$configfile = "<font color='#66CC00'>OK</font> ($b)";
		} else {
			$configfile = "<font color='##FF0000'>Not writeable</font> ($b)";
		}
		$bla .= "<tr><td>Write access to config file:</td><td>$configfile</td></tr>";
		$b = get_perms("webdav_fs/");
		$c = fileowner("webdav_fs/");
		//$d = posix_getpwuid($c);
		//$c .= "," . $d['name'];

		if ($fp = @fopen("webdav_fs/test","w")) {
			$wd = "<font color='#66CC00'>OK</font> ($b owned by $c)";
		} else {
			$wd = "<font color='##FF0000'>Not writeable (WebDAV will NOT work)</font><br>($b owned by $c)";
			$wd .= "<br>you will have to change the owner of the directory,<br>e.g. \"chown apache.apache webdav_fs/\" and, <br>to prevent other users from accessing it, issue a<br>\"chmod 700 webdav_fs/\".";
		}
		$bla .= "<tr><td>Write access to webdav_fs/</td><td>$wd</td></tr>";
		$bla .= "<form name='inst' action='install.php' method='POST'>";
		$bla .= "</fieldset></td><tr></table></td></tr></table></center>";
		$bla .= "<center>";
		$bla .= "<br>";

		$bla .= "<input type='hidden' name='step' value='1'>";
		$bla .= "<input type='hidden' name='tmpfileerror' value='" . $tmpfileerror . "'>";
		$bla .= "<input type='hidden' name='AddRepository' value='$AddRepository'>";

		if (!$fatal) {
			$bla .= "<input type='submit' name='knop' value='Go to step 1'>";
		} else {
			$bla .= "<input type='submit' name='knop' value='Fatal error found - unable to continue' DISABLED>";
		}
		

		$bla .= "<br><br></form>";
		$bla .= "</center>";
		printbox($bla);
		print "</body></html>";
		

} elseif ($step==1) {
//		if (!$AddRepository) { printstepheaderinst("1"); }

		$bla = "<b>Please provide the following information:</b>";
		$bla .= "<form name='sql' action='install.php' method='POST'><table border=0><tr><td>";
		$bla .= "MySQL host: </td><td><input type='text' name='sqlhost' size='30'></td></tr><tr><td>";
		$bla .= "MySQL username: </td><td><input type='text' name='sqluser' size='30'></td></tr><tr><td>";
		$bla .= "MySQL password: </td><td><input type='password' name='sqlpwd' size='30'></td></tr>";
		$bla .= "<tr><td>MySQL table prefix:<br><i>(all tables will start with these characters)</i> </td><td><input type='text' name='TABLEPREFIX' size='30' value='CRM'></td></tr><tr><td>";
	
		if ($upgrade) {
			$bla .= "Mysql database:<br><i>(the existing database)</i></td><td><input type='text' name='sqldb' size='30'></td></tr>";
			$bla .= "<input type='hidden' name='upgrade' value='1'>";
		} else {
			$bla .= "Mysql database:<br><i>(This install procedure will try to create this database but<br>will continue if it already exists)</i></td><td><input type='text' name='sqldb' size='30'></td></tr>";
		}
		if ($tmpfileerror) {		
			$bla .= "<tr><td>Temp file path (e.g. /tmp): </td><td><input type='text' name='tmp_file_path' size='30'></td></tr>";
		}
		$bla .= "</td></tr></table><br>";

		$bla .= "<input type='hidden' name='AddRepository' value='$AddRepository'>";
		$bla .= "<input type='hidden' name='step' value='2'><input type='submit' name='knop' value='Go to step 2'>";
		$bla .= "</form>";
		printbox($bla);
		print "</body></html>";
	

} elseif ($step==2) {
	if (!$TABLEPREFIX) {
		printbox("Error! No TABLEPREFIX received. Press 'back' in your browser and try again");
		exit;
	} else {
		$GLOBALS['TBL_PREFIX'] = $TABLEPREFIX;
	}


	if ((!$sqlhost) || (!$sqluser) || (!$sqldb)) {
		printerror("");
	}

	testsql($sqlhost,$sqluser,$sqlpwd);
	$bla .= "Database information was OK, the connection has been tested.<br><BR>";

		$sql = "CREATE DATABASE IF NOT EXISTS `$sqldb`";
		mcqinstall($sql,$db);

		$db = @mysql_connect($sqlhost, $sqluser, $sqlpwd);


		if (@mysql_select_db($sqldb,$db)) {

			$bla .= "Database $sqldb was created and selected succesfully.<br><BR>";
		} else {
			$bla .= "<img src='error.gif'>&nbsp;&nbsp;Database $sqldb was NOT created succesfully.<br><br><i>This could not be fatal. Trying to use it anyway.</i><br><BR>";
		}
	
		

		// LANGUAGE IMPORT ROUTINE

		// The main pack file MUST BE NAMED "ENG.CRM"



		$sqla = array();
		$sqla = fillarray();
		//print "<input type=hidden name=bla value=\"";
			
			for ($x=0;$x<sizeof($sqla);$x++) {
				$result= @mysql_query($sqla[$x]) or printerror("An error occured while importing the database structure!<br><br>The reported error is: " . mysql_error () . "<br><br>Please Press 'back' in your browser and try again.");

			}

		
//		print "\">";

		$bla .= "Database structure was successfully imported.<br><BR>";
		
		$fc = file("ENG.CRM");

		$fc[0] = ""; // CRM LANGUAGE PACK EXPORT FILE - Pack ENGLISH. This is the original, distributed pack.
		$fc[1] = ""; // Generated May 9, 2006, 21:05 on CRM version CRM-CTT 3.4.2 (c) 2001-2006 - LOGTEXT ENABLED.
		$fc[2] = ""; // PACK|||ENGLISH|||318


		for ($x=3;$x<sizeof($fc);$x++) {
				$tmp=split("\|\|\|",$fc[$x]);
				$fc1[$x] = addslashes($tmp[0]);
				$fc2[$x] = addslashes($tmp[1]);
				$fc3[$x] = addslashes($tmp[2]);
		}

//		fclose($fp);
//			print "FILE RECEIVED! - $_FILES['userfile']['tmp_name']  ($_FILES['userfile']['name'])";
		$bla2 = $fc[2];
		$header=split("\|\|\|",$bla2);
		$pack = $header[1];
		//printbox("<br>Processing language pack $header[1] containing $header[2] entries.<br>");

		$outp = "<br>Installing base language pack<br>";
			for ($p=3;$p<sizeof($fc2);$p++) {

							$sql = "INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "languages(LANGID,TEXTID,TEXT) VALUES('$fc1[$p]','$fc2[$p]','$fc3[$p]')";
							 mcqinstall($sql,$db);
					}
					$outp .= "<br>$p Language entries were imported<br><BR>";
	
		printbox($bla . $outp);
		
//		tmp_file_path
		if ($tmp_file_path) {
			$dir = $tmp_file_path;
			if ($f = @tempnam($dir,"BLA")) {
					printbox("Temporary file path OK");
					unlink($f);
					$tmpdir = $tmp_file_path;
			} else {
					printbox("<img src='error.gif'>&nbsp;<font color='#FF0000'>CRM-CTT was not able to write a file in the provided temporary file directory.<br>This installation will continue, though CRM-CTT <b>will not work properly</b>. As soon as CRM-CTT is installed, go to the system administration page, select 'Change system values' and set the TMP_FILE_PATH to directory in which the webserver is allowed to place files. Please, be warned and fix this!</font>");
					$tmpdir = "ERROR!";
			}
		} else {
			$tmpdir = ini_get("session.save_path");
		}
		$sql = "UPDATE " . $GLOBALS['TBL_PREFIX'] . "settings SET value='" . addslashes($tmpdir) . "' WHERE setting='TMP_FILE_PATH'";
		mcqinstall($sql,$db);
?>
			<center><br>	
		<form name='inst' action='install.php' method='POST'>
		<input type='hidden' name='step' value='3'>
		<input type='hidden' name='upgrade' value='<? echo $upgrade;?>'>
			<? print "<input type='hidden' name='AddRepository' value='$AddRepository'>";?>
		<input type='submit' name='knop' value='Go to step 3'>
		<?
		print "<input type='hidden' name='sqldb' value='$sqldb'>";
		print "<input type='hidden' name='sqluser' value='$sqluser'>";
		print "<input type='hidden' name='sqlpwd' value='$sqlpwd'>";
		print "<input type='hidden' name='sqlhost' value='$sqlhost'>";
		print "<input type='hidden' name='TABLEPREFIX' value='" . $GLOBALS['TBL_PREFIX'] . "'>";

		?>
		</form>
    	</center>
	</body>
	</html>
	<?
} elseif ($step==3) {
	if (!$TABLEPREFIX) {
		printbox("Error! No TABLEPREFIX received. Press 'back' in your browser and try again");
		exit;
	} else {
		$GLOBALS['TBL_PREFIX'] = $TABLEPREFIX;
	}

	$legend = "Please provide the following information&nbsp;";
	
		$bla .= "<table><tr><td><form name='addinfo' action='install.php' method='POST'>";
		$bla .= "Repository name: </td><td><input type='text' name='cname' size='30' value='My Demo Company'></td></tr>";
		$bla .= "<tr><td>";
		$bla .= "Administrator's email: </td><td><input type='text' name='admemail' size='30' value=''></td></tr>";
		$bla .= "<tr><td>";
		$bla .= "Initial login account name: </td><td><input type='text' name='fstaccount' size='30' value=''></td></tr>";
		$bla .= "<tr><td>";
		$bla .= "Initial login account password: </td><td><input type='password' name='fstpwd1' size='30' value=''></td></tr>";
		$bla .= "<tr><td>";
		$bla .= "Again: </td><td><input type='password' name='fstpwd2' size='30' value=''></td></tr>";
		$bla .= "</td></tr></table>";
		
		printbox($bla);

		print "<center><br>";
		print "<input type='hidden' name='step' value='4'>";
		
		print "<input type='hidden' name='sqldb' value='$sqldb'>";
		print "<input type='hidden' name='upgrade' value='$upgrade'>";
		print "<input type='hidden' name='sqluser' value='$sqluser'>";
		print "<input type='hidden' name='sqlpwd' value='$sqlpwd'>";
		print "<input type='hidden' name='sqlhost' value='$sqlhost'>";
		print "<input type='hidden' name='AddRepository' value='$AddRepository'>";	
		print "<input type='hidden' name='TABLEPREFIX' value='" . $GLOBALS['TBL_PREFIX'] . "'>";
		?>

		<input type='submit' name='knop' value='Go to step 4'>
		</form>
		</center>
		</body>
		</html>
	<?
} elseif ($step==4) {
		if (!$TABLEPREFIX) {
		printbox("Error! No TABLEPREFIX received. Press 'back' in your browser and try again");
		exit;
		} else {
			$GLOBALS['TBL_PREFIX'] = $TABLEPREFIX;
		}
		if ((!$trywrite) && (!$upgrade)) {
		    

			if ((!$cname) || (!$admemail) || (!$fstaccount) || (!$fstpwd1) || (!$fstpwd2)) {
					printerror("");
			}
			if ($fstpwd1<>$fstpwd2) {
					printerror("Passwords do not match. Press 'back' in your browser and try again.");
			}
			if ($fstpwd1=="") {
					printerror("Sorry, password cannot be empty. Press 'back' in your browser and try again.");
			}
			if ((strlen($fstpwd1))<3) {
					printerror("Your password is too short (". strlen($fstpwd1) . ").  Press 'back' in your browser and enter one longer than 3 characters!");
			}
			if (strlen($fstaccount)<3) {
					printerror("Your accountname is too short.  Press 'back' in your browser and enter one longer than 3 characters!");
			}

			if ($logo=="") {
				$logo="*NONE*";
			}
		   $db = mysql_connect($sqlhost, $sqluser, $sqlpwd);
			mysql_select_db($sqldb,$db);
			$sqla = array();
			$sqla = datafill($fstaccount,$fstpwd1,$admemail,$cname,$logo);

			for ($x=0;$x<sizeof($sqla);$x++) {
				$result= mysql_query($sqla[$x]) or die (printerror("An error occured while importing settings!<br><br>The reported error is: " . mysql_error () . "<br><br>Please Press 'back' in your browser and try again.<br><br>Query: $sqla[$x]"));
			}

			// POST-INSTALL BUSINESS
			PostInstall();			

				$cfgfile .= '<?';
				$cfgfile .= "\n";
				$cfgfile .= '$GLOBALS[LogonPageMessage] = "";';
				$cfgfile .= "\n";
				$cfgfile .= '$host[0] = "'. $sqlhost . '";';    
				$cfgfile .= "\n";
				$cfgfile .= '$user[0] = "'. $sqluser . '";';                                        // SQL-user
				$cfgfile .= "\n";
				$cfgfile .= '$pass[0] = "'. $sqlpwd . '";';                                          // SQL-pass
				$cfgfile .= "\n";
				$cfgfile .= '$database[0] = "' . $sqldb .'";';                                // CRM-database
				$cfgfile .= "\n";
				$cfgfile .= '$table_prefix[0] = "' . $GLOBALS['TBL_PREFIX'] . '";';
				$cfgfile .= "\n";
				$cfgfile .= '?>';

		} // end if !trywrite
	
	?>
			<?
		
		if ($upgrade) {
				$cfgfile .= '<?';
				$cfgfile .= "\n";
				$cfgfile .= '$GLOBALS[\'LogonPageMessage\'] = "";';
				$cfgfile .= "\n";
				$cfgfile .= '$host[0] = "'. $sqlhost . '";';    
				$cfgfile .= "\n";
				$cfgfile .= '$user[0] = "'. $sqluser . '";';                                        // SQL-user
				$cfgfile .= "\n";
				$cfgfile .= '$pass[0] = "'. $sqlpwd . '";';                                          // SQL-pass
				$cfgfile .= "\n";
				$cfgfile .= '$database[0] = "' . $sqldb .'";';                                // CRM-database
				$cfgfile .= "\n";
				$cfgfile .= '$table_prefix[0] = "' . $GLOBALS['TBL_PREFIX'] . '";';
				$cfgfile .= "\n";
				$cfgfile .= '?>';
		}

		if ($write=="niet gelukt") {
				$b = get_perms("config.inc.php");
				?>
					<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
					<!--
					alert("Writing failed. The permissions are <? echo $b;?> which is not enough!");
					//-->
					</SCRIPT>
				<?
		} elseif ($write=="gelukt") {
				$b = get_perms("config.inc.php");
   				?>
					<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
					<!--
					alert("Writing succesfull! The permissions now are <? echo $b;?>. Change it back to at least 755!");
					//-->
					</SCRIPT>
				<?
		}

		if ($AddRepository==1) { 
			$a = sizeof($pass);

			?>
			<center><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Done!&nbsp;</legend><table border='0' width='70%'>
			<tr><td colspan=2><b>All is set. The only thing left is to edit your config file.</b>
			<br><br>
			You need to add the following text. This procedure cannot do that for you.
			<br><br>

						<?
//				$cfgfile = '$GLOBALS[\'LogonPageMessage\'] = "";';
//				$cfgfile .= "\n";
				$cfgfile .= '$host[' . $a . '] = "'. $sqlhost . '";';    
				$cfgfile .= "\n";
				$cfgfile .= '$user[' . $a . '] = "'. $sqluser . '";';                                        // SQL-user
				$cfgfile .= "\n";
				$cfgfile .= '$pass[' . $a . '] = "'. $sqlpwd . '";';                                          // SQL-pass
				$cfgfile .= "\n";
				$cfgfile .= '$database[' . $a . '] = "' . $sqldb .'";';                          // CRM-database
				$cfgfile .= "\n";
				$cfgfile .= '$table_prefix[' . $a . '] = "' . $GLOBALS['TBL_PREFIX'] . '";';
				$cfgfile .= "\n";

				print "<pre>";
				print htmlspecialchars($cfgfile);
				print "</pre>";
				?>
				<br></td></tr>
				<tr><td>When done, point your browser <a href='index.php' class='topnav'>here</a>.</td></tr></table>
				</fieldset>
				</center>
			<?
		} else {

		?>
	
	
		<center><table width='70%'><tr><td>
		<fieldset>
		<legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Done!&nbsp;</legend>
		<center><table border='0' width='70%'>
		<tr><td colspan=2><b>All is set. The only thing left is to create the config file.</b>
		<br><br>
			This can be done in three ways:<br><br>
			1. Let the install procedure try to write the file itself <br>
			<i>You must chmod 777 the file temporary to let this work!</i><br>
			2. Download the file to you browser and place it in the installation directory yourself<br>
			3. Copy the below printed text into the config.inc.php file yourself<br>
		</td></tr>
		<tr><td>
			Click this link to let the installer try to write the config.inc.php file.
			<? echo $a;?>
			<form name='dlcfg' action='install.php' method='POST'>

			<input type='hidden' name='step' value='write'>
			<input type='hidden' name='cfgfile' value='<? echo $cfgfile; ?>'>
			<? print "<input type='hidden' name='TABLEPREFIX' value='$TABLEPREFIX'><input type='hidden' name='AddRepository' value='$AddRepository'>";?>
			<input type='submit' name='knop' value='Try now'>
			</form>	

		</td></tr>
		<tr><td>
			Click this link to download the config.inc.php file.
			<form name='dlcfg' action='install.php' method='POST'>
			<input type='hidden' name='step' value='dl'>
			<input type='hidden' name='cfgfile' value='<? echo $cfgfile; ?>'>
			<? print "<input type='hidden' name='AddRepository' value='$AddRepository'>";?>
			<input type='submit' name='knop' value='Dowload config.inc.php file'>
			</form>
		</td></tr>

		<tr><td><br>If that doesn't work, create a file called config.inc.php in your installation directory containing the following:
		<br>
		<br>
		<?
		print "<pre>";
		print htmlspecialchars($cfgfile);
		print "</pre>";
		?>
		<br>
			</td></tr>
		<tr><td>When done, point your browser <a href='index.php' class='topnav'>here</a>.</td></tr>
		</table>
		</fieldset>
		</center>
		</td></tr></table>
		</center>
<?
		} // end if repository
?>

			</body>
			</html>
	<?
	
} 






function testsql($host,$user,$pass)
{
//	print "<input type=hidden name=bla value=\"";
    $link = mysql_pconnect($host, $user, $pass)
     or printerror("Could not connect to your database system. <br><br>The reported error is: " . mysql_error () . "<br><br>Please Press 'back' in your browser and try again.");

} // end func


function printerror($a)
{
		
		if (!$a) {
		    $a="You didn't provide all required information. Press 'back' in your browser and try again.";
		}
		?>
		<center><table width='75%'><tr><td><center><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Error&nbsp;</legend><table border='1' width='70%' bgcolor='#F2F2F2' cellspacing='0' cellpadding='4' bordercolor='#CECECE'>
		<tr><td colspan=2><center><img src='error.gif'>&nbsp;&nbsp;<b><? echo $a;?></b>
		</td></tr></table></fieldset></center></td></tr></table></center></body></html>
		<?
			exit;
} // end func



function printstepheaderinstinst($step)
{
		
	?>
		<center><img src='crm.gif'></center>
	    <center><table border='1' width='70%' bgcolor='#F2F2F2' cellspacing='0' cellpadding='4' bordercolor='#CECECE'>
		<tr><td colspan=2><b><center>CRM installation procedure - step <? echo $step;?></center></b></td></tr>
		</table></center>
		<br>
	<?
} // end func


function fillarray()
{
	// This function fills the sqla array with queries to create CRM-CTT database table structure
	// Last update; 3.2.0 - june 7, 2005

	$sqla = array();

	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "binfiles (fileid bigint(20) NOT NULL auto_increment, koppelid bigint(20) NOT NULL default '0', content mediumblob NOT NULL, filename varchar(200) NOT NULL default '', creation_date timestamp NOT NULL, filesize mediumint(9) NOT NULL default '0', filetype varchar(150) NOT NULL default '', username varchar(150) NOT NULL default '', checked enum('in','out') NOT NULL default 'in', checked_out_by int(11) NOT NULL default '0', type enum('entity','cust') NOT NULL default 'entity', file_subject varchar(250) NOT NULL default '', PRIMARY KEY (fileid), KEY koppelid (koppelid), KEY checked (checked)) TYPE=MyISAM COMMENT='CRM Binairy files'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "cache (stashid bigint(20) NOT NULL auto_increment, epoch varchar(20) default NULL, value longtext NOT NULL, PRIMARY KEY (stashid), FULLTEXT KEY value (value)) TYPE=MyISAM COMMENT='CRM Query cache table'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "calendar (user varchar(20) NOT NULL default '', Tijdpostzegel timestamp NOT NULL, datum date NOT NULL default '0000-00-00', basicdate varchar(8) NOT NULL default '', calendarid mediumint(5) NOT NULL auto_increment, type varchar(10) NOT NULL default '', customnum varchar(12) NOT NULL default '', emailadress varchar(150) NOT NULL default '', eID varchar(5) NOT NULL default '', PRIMARY KEY (calendarid), KEY Tijdpostzegel (Tijdpostzegel), KEY basicdate (basicdate), KEY datum (datum)) TYPE=MyISAM COMMENT='CRM Calendar entries'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "customaddons (id bigint(20) NOT NULL auto_increment, eid bigint(20) NOT NULL default '0', type varchar(50) NOT NULL default '', name varchar(50) NOT NULL default '', value longtext NOT NULL, deleted enum('y','n') NOT NULL default 'n', usr varchar(50) NOT NULL default '', PRIMARY KEY (id), KEY deleted (deleted), KEY name (name), FULLTEXT KEY value (value)) TYPE=MyISAM COMMENT='CRM Extra fields sequential table'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "customer (id int(11) NOT NULL auto_increment, readonly enum('no','yes') NOT NULL default 'no', custname varchar(200) NOT NULL default '', contact varchar(240) NOT NULL default '', contact_title varchar(240) NOT NULL default '', contact_phone varchar(50) NOT NULL default '', contact_email varchar(240) NOT NULL default '', cust_address longtext NOT NULL, cust_remarks longtext NOT NULL, cust_homepage varchar(240) NOT NULL default '', active enum('yes','no') NOT NULL default 'yes', customer_owner int(11) NOT NULL default '0', email_owner_upon_adds enum('no','yes') NOT NULL default 'no', PRIMARY KEY (id), KEY custname (custname), KEY active (active), FULLTEXT KEY cust_address (cust_address,cust_remarks)) TYPE=MyISAM COMMENT='CRM Main customer table'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "ejournal (id int(11) NOT NULL auto_increment, eid int(11) NOT NULL default '0', category varchar(150) NOT NULL default '', content longtext NOT NULL, status varchar(50) NOT NULL default 'open', priority varchar(50) NOT NULL default 'low', owner int(11) NOT NULL default '0', assignee int(11) NOT NULL default '0', CRMcustomer int(11) NOT NULL default '0', tp timestamp NOT NULL, deleted enum('n','y') NOT NULL default 'n', duedate varchar(15) NOT NULL default '', sqldate date NOT NULL default '0000-00-00', obsolete enum('y','n') NOT NULL default 'n', cdate date NOT NULL default '0000-00-00', waiting enum('n','y') NOT NULL default 'n', readonly enum('n','y') NOT NULL default 'n', closedate date NOT NULL default '0000-00-00', lasteditby bigint(20) NOT NULL default '0', createdby bigint(20) NOT NULL default '0', notify_assignee enum('n','y') NOT NULL default 'n', notify_owner enum('n','y') NOT NULL default 'n', private enum('n','y') NOT NULL default 'n', duetime varchar(4) NOT NULL default '', PRIMARY KEY (id), KEY eid (eid)) TYPE=MyISAM COMMENT='CRM Entity contents journal'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "entity (eid int(11) NOT NULL auto_increment, category varchar(150) NOT NULL default '', content longtext NOT NULL, status varchar(50) NOT NULL default 'open', priority varchar(50) NOT NULL default 'low', owner int(11) NOT NULL default '0', assignee int(11) NOT NULL default '0', CRMcustomer int(11) NOT NULL default '0', tp timestamp NOT NULL, deleted enum('n','y') NOT NULL default 'n', duedate varchar(15) NOT NULL default '', sqldate date NOT NULL default '0000-00-00', obsolete enum('y','n') NOT NULL default 'n', cdate date NOT NULL default '0000-00-00', waiting enum('n','y') NOT NULL default 'n', readonly enum('n','y') NOT NULL default 'n', closedate date NOT NULL default '0000-00-00', lasteditby bigint(20) NOT NULL default '0', createdby bigint(20) NOT NULL default '0', notify_assignee enum('n','y') NOT NULL default 'n', notify_owner enum('n','y') NOT NULL default 'n', openepoch varchar(30) NOT NULL default '', closeepoch varchar(30) NOT NULL default '', private enum('n','y') NOT NULL default 'n', duetime varchar(4) NOT NULL default '', PRIMARY KEY (eid), KEY duedate (duedate), KEY assignee (assignee), KEY owner (owner), KEY sqldate (sqldate), KEY CRMcustomer (CRMcustomer), KEY deleted (deleted), KEY openepoch (openepoch), KEY closeepoch (closeepoch)) TYPE=MyISAM COMMENT='CRM Main entity table'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "entitylocks (lockid bigint(20) NOT NULL auto_increment, lockon bigint(20) NOT NULL default '0', lockby bigint(20) NOT NULL default '0', lockepoch varchar(30) NOT NULL default '', PRIMARY KEY (lockid), KEY lockon (lockon), KEY lockepoch (lockepoch)) TYPE=MyISAM COMMENT='CRM Entity record locks'");
	
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "extrafields (id bigint(20) NOT NULL auto_increment, ordering mediumint(9) NOT NULL default '0', tabletype enum('entity','customer') NOT NULL default 'entity', hidden enum('n','y','a') NOT NULL default 'n', location varchar(40) NOT NULL default '', deleted enum('n','y') NOT NULL default 'n', fieldtype varchar(50) NOT NULL default '', name varchar(250) NOT NULL default '', options LONGTEXT NOT NULL default '', forcing enum('n','y') NOT NULL default 'n', defaultval varchar(250) default NULL, UNIQUE KEY id (id), KEY location (location), KEY tabletype (tabletype), FULLTEXT KEY name (name,options)) TYPE=MyISAM COMMENT='CRM Extra field definitions'");
	
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "help (helpid bigint(20) NOT NULL auto_increment, title varchar(240) NOT NULL default '', content longtext NOT NULL, PRIMARY KEY (helpid), FULLTEXT KEY content (content)) TYPE=MyISAM COMMENT='CRM Help contents table'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "journal (id bigint(20) NOT NULL auto_increment, eid bigint(20) NOT NULL default '0', timestamp timestamp NOT NULL, user bigint(20) NOT NULL default '0', message longtext NOT NULL, type varchar(20) NOT NULL default 'entity', PRIMARY KEY (id), KEY eid (eid,user), KEY type (type)) TYPE=MyISAM COMMENT='CRM Entity/customer journal'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "languages (id bigint(20) NOT NULL auto_increment, LANGID varchar(15) NOT NULL default '', TEXTID varchar(30) NOT NULL default '', TEXT varchar(255) NOT NULL default '', UNIQUE KEY id (id), KEY LANGID (LANGID)) TYPE=MyISAM COMMENT='CRM language table'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "loginusers (id int(11) NOT NULL auto_increment, name varchar(50) NOT NULL default '', password varchar(50) default NULL, type enum('normal','limited') NOT NULL default 'normal', active enum('yes','no') NOT NULL default 'yes', exptime varchar(40) NOT NULL default '', noexp enum('n','y') NOT NULL default 'n', FULLNAME varchar(150) NOT NULL default '', EMAIL varchar(150) NOT NULL default '', CLLEVEL varchar(50) NOT NULL default 'ro', administrator enum('yes','no') NOT NULL default 'no', LASTFILTER longtext NOT NULL, LASTSORT varchar(50) NOT NULL default '', RECEIVEDAILYMAIL enum('No','Yes') NOT NULL default 'No', RECEIVEALLOWNERUPDATES enum('n','y') NOT NULL default 'n', RECEIVEALLASSIGNEEUPDATES enum('n','y') NOT NULL default 'n', HIDEADDTAB char(1) NOT NULL default '', HIDECSVTAB char(1) NOT NULL default '', HIDESUMMARYTAB char(1) NOT NULL default '', HIDEENTITYTAB char(1) NOT NULL default '', HIDEPBTAB char(1) NOT NULL default '', SHOWDELETEDVIEWOPTION char(1) NOT NULL default '', HIDECUSTOMERTAB char(1) NOT NULL default '', SAVEDSEARCHES longtext NOT NULL, EMAILCREDENTIALS longtext NOT NULL, PRIMARY KEY (id), KEY name (name), FULLTEXT KEY SAVEDSEARCHES (SAVEDSEARCHES), FULLTEXT KEY EMAILCREDENTIALS (EMAILCREDENTIALS)) TYPE=MyISAM COMMENT='CRM User definition table'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "phonebook (id mediumint(5) NOT NULL auto_increment, Firstname varchar(50) NOT NULL default '', Lastname varchar(50) NOT NULL default '', Telephone varchar(15) NOT NULL default '', Company varchar(50) NOT NULL default '', Department varchar(50) NOT NULL default '', Title varchar(100) NOT NULL default '', Room varchar(60) NOT NULL default '', Email varchar(60) NOT NULL default '', UNIQUE KEY id (id)) TYPE=MyISAM COMMENT='CRM Phone book table'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "priorityvars (id int(11) NOT NULL auto_increment, varname varchar(50) NOT NULL default '', color varchar(7) NOT NULL default '', PRIMARY KEY (id), KEY varname (varname)) TYPE=MyISAM COMMENT='CRM Priority definitions table'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "sessions (id int(11) NOT NULL auto_increment, name varchar(50) NOT NULL default '', temp varchar(100) NOT NULL default '', active enum('yes','no') NOT NULL default 'yes', exptime varchar(40) NOT NULL default '', noexp enum('n','y') NOT NULL default 'n', PRIMARY KEY (id)) TYPE=MyISAM COMMENT='CRM Session table'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "settings (settingid bigint(20) NOT NULL auto_increment, setting varchar(150) NOT NULL default '', value longtext NOT NULL, datetime timestamp NOT NULL, discription varchar(250) NOT NULL default '', UNIQUE KEY settingid (settingid), KEY setting (setting)) TYPE=MyISAM COMMENT='CRM Main settings table'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "statusvars (id int(11) NOT NULL auto_increment, varname varchar(50) NOT NULL default '', color varchar(7) NOT NULL default '', PRIMARY KEY (id), KEY varname (varname)) TYPE=MyISAM COMMENT='CRM Status definitions table'");
	
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "triggers (tid bigint(20) NOT NULL auto_increment, onchange varchar(200) NOT NULL default '', action varchar(50) NOT NULL default '', to_value varchar(100) NOT NULL default '', template_fileid bigint(20) NOT NULL default '0', attach enum('n','y') NOT NULL default 'n', report_fileid bigint(20) NOT NULL default '0', PRIMARY KEY (tid), KEY to_value (to_value), KEY onchange (onchange)) TYPE=MyISAM COMMENT='CRM Entity value change trigger table'");
	
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "uselog (id bigint(20) NOT NULL auto_increment, ip varchar(15) NOT NULL default '', url varchar(50) NOT NULL default '', useragent varchar(255) NOT NULL default '', tijd timestamp NOT NULL, qs longtext NOT NULL, user varchar(50) NOT NULL default '', PRIMARY KEY (id), KEY tijd (tijd), KEY url (url), KEY ip (ip), KEY user (user)) TYPE=MyISAM COMMENT='CRM Main activity log table'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "webdav_locks (token varchar(255) NOT NULL default '', path varchar(200) NOT NULL default '', expires int(11) NOT NULL default '0', owner varchar(200) default NULL, recursive int(11) default '0', writelock int(11) default '0', exclusivelock int(11) NOT NULL default '0', PRIMARY KEY (token), KEY path (path), KEY expires (expires)) TYPE=MyISAM COMMENT='CRM Webdav file locks table'");
	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "webdav_properties (path varchar(255) NOT NULL default '', name varchar(120) NOT NULL default '', ns varchar(120) NOT NULL default 'DAV:', value text, PRIMARY KEY (path,name,ns)) TYPE=MyISAM COMMENT='CRM Webdav properties'");

	return($sqla);

} // end func



function datafill($fstaccount,$fstpwd1,$admemail,$cname,$logo)
{
	global $version;
	$sqla = array();

	// 1st Admin user (the dude who installs me)

	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "loginusers (name,password,administrator,CLLEVEL,FULLNAME) VALUES('$fstaccount',PASSWORD('$fstpwd1'),'Yes','rw','Administrator $fstaccount')");

	// Help content

	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (1, 'Download query result as CSV export file', 'When checked, the result of your search query will be \'pushed\' as an CSV-export file. \r\n<br><br>\r\nThis means that you will <b>not</b> see the result on screen, but  you\'ll jump to the CSV page. On that page you can select wich fields you want to export.<br><br>\r\nSee also help on <a href=\'help.php?id=4\' class=\'topnav\'>\'Exporting data to Excel\'</a>')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (2, 'Include entity contents', 'When checked, this function as a result will include the textual content of an entity in the summary. ')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (3, 'Include deleted entities', 'When checked, the query you created by using the pull-down boxes will also be ran over deleted entities in the database.\r\n<br><br>\r\nUse this function when searching for an enitity not knowing wheter or this entity is still available.\r\n<br><br>\r\nWhen found, you can easily undelete an entity by clicking \'Update in new window\' followed by unchecking the \'This is a deleted entity\' checkbox. Do not forget to click \'Save and close\' when you\'re done.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (4, 'Exporting data to CSV (Excel)', 'CSV stands for Character Separated Values.\r\n<br><br>\r\nYou can select the fields in the database you want to export. If you are unsure, just check all the boxes. You will now export all data in the database but the binairy files (the attachments).\r\n<br><br>\r\nBy clicking \'Download exportfile\' you will immediately see a download dialog box. Click \'Open\', and, if it appears again, click \'Open\' again.\r\n<br><br>\r\nIf you are looking for an export of an particular part of the database you can use the summary page, and check the \'Download result as CSV file\' option. See also help on <a href=\'help.php?id=1\' class=\'topnav\'>\'Download query result as CSV export file\'</a>.\r\n')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (5, 'Adding and modifing owners/assignees', 'You can modify owners/assignees by just clicking the name and altering the text.\r\n<br><br>\r\nUsing the last field you can add owners/assignees, one at the time.\r\n<br>\r\nIf a owner or assignee logs in with a limited account (which have to be created seprately using the same name), the user will only have rights to view the entities assigned to him- or herself, with the only right to add comments to the textual content and the right to attach files.\r\n<br><br>\r\n<b>Important!</b>\r\n<br>\r\nDo not switch users! CRM-CTT works by the number, not the name!')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (6, 'Adding and modifing customers', 'You can modify customers by just clicking the name and altering the text.\r\n<br><br>\r\nUsing the last field you can add customers, one at the time.\r\n<br><br>\r\n<b>Important!</b>\r\n<br>\r\nDo not switch customers! CRM-CTT works by the number, not the name!')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (7, 'Fulltext search through all entities', 'When typing a word, or part of it, in the \'Enitity must contain text\'-field, CRM-CTT will search through the database for entities with that exact word in the \'Category\' or the content.\r\n<br><br>\r\nWhen you\'ve also checked \'Include enitity contents\', the content will be <font color=ff3300><u>red-highlighted</u></font> when your search matched.\r\n<br><br>\r\nWildcard searches or logical-operand searches are <b>not</b> supported.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (8, 'Management Information', 'Management Information displays summaries of data gathered from the database. These summaries are mostly statistics.\r\n<br><br>\r\nCheck the summaries you want to see. If you check more boxes, the output will be longer. \r\n<br><br>\r\nIf you check all boxes, remember this slows down the server, so maybe you\'ll have to wait a little.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (9, 'Attach a file', 'Using this function you can attach a file to an enitity.\r\n<br><br>\r\nBy clicking \'browse\' you\'ll see a file select box. Browse to the file you want to attach. Click \'open\' and the path to the file will appear in the field box.\r\n<br><br>\r\nAfter you\'ve saved the enitity, the file will be attached.\r\n<br><br>\r\nYou can only attach one file at the time!\r\n')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (10, 'This entity is waiting for somebody else\'s action', 'By checking this box you let CRM-CTT know that the entity you\'re working on is stalled because somebody else has to undertake action first.\r\n<br><br>\r\nThis is mainly for management information purposes. Though; you can also use this as a search option when using the summary page.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (11, 'This entity really doesn\'t belong here.', 'By checking this box you let CRM-CTT know that the entity you\'re working on really should not be logged in this CRM.\r\n<br><br>\r\nThis is mainly for management information purposes.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (12, 'This is a deleted entity', 'When you check this box, <b>you delete the entire entity although it might not yet be closed.</b>\r\n<br><br>\r\nWhen the checkbox was already checked an you uncheck it, you <b>undelete</b> the entity.\r\n')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (13, '<center><b>Disclaimer</b></center>', '<center>(logo?)</center><br><i><b>ALL INFORMATION CONTAINED IN THIS WEBSITE IS CONFIDENTIAL</b><br><br>\r\n Although, with respect to documents available from this server, neither the author nor any other involved supplier makes any warranty, express or implied, including the warranties of merchantability and fitness for a particular purpose, or assumes any legal liability or responsibility for the accuracy,  completeness, or usefulness of any information, apparatus, product, or process disclosed, or represents that its use would not infringe privately owned rights. This should be perfectly clear for anybody who uses this website. Don\'t agree? Too bad! Be warned!</i><br>\r\n')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (14, 'S.O.B.', 'S.O.B. stands for <i>Standard Operational B*llsh*t</i>')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (16, 'Quickmenu to entities', 'You can use the <i>little</i> fieldbox in the header of every page to quickly jump to an entity.<B>You can only enter entity-numbers in this box!</b>When you enter a valid number and press TAB or ENTER you will instantly jump to the edit window of that entity.\r\n<br><br>\r\nThe <i>large</i> fieldbox is the searchbox from the summary page. When you enter a text in this box, CRM-CTT will search all entities for matching records.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (17, 'Logging into the CRM', 'You can only login to this application if a username and a password was provided to you.<br><br>\r\nIf you should be able to get in, but you cannot, use the e-mail adress as shown on the login page to send an email to the administrator of this site.\r\n<br><br>\r\nClicking Save Username will save your used username to your machine using a so-called cookie. By clicking Clear Username the cookie will be deleted. The same goes for the password.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (18, 'Brief entity overview matching query', 'By checking the Brief Overview box the result of you search query will be displayed in the format of the Brief Entity Overview. This may come in handy working with queries with many matches.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "help VALUES (19, 'Download PDF report or summary', 'Using this function you can download a file in Adobe Acrobat format which contains information about a certain entity. You can also download the management summary and an export of the phonebook in PDF-format.')");

	// Global settings
	
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings VALUES (1, 'title', '$cname', 20020520184301, 'Will appear almost anywhere.')");
	//array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings VALUES (2, 'logo', '$logo', 20020520142203, 'Location of your company logo (small, GIF or JPEG). *NONE* disables the logo.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings VALUES (4, 'admpassword', '*NONE*', 20020520162029, 'Administration password, *NONE* disables the authentication at all.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings VALUES (5, 'mipassword', '*NONE*', 20020520184155, 'Management Information password, *NONE* disables the authentication at all.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings VALUES (6, 'managementinterface', 'Off', 20020520210325, 'When set to \'on\', users authenticated as a limited user will only see the restricted managementinterface with very limited priviledges. Opposite to \'on\' is \'off\'.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings VALUES (7, 'admemail', '$admemail', 20020520140033, 'The administrators email-addres.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings VALUES (8, 'cronpassword', '', 20020520180654, 'The password used by the HTTP-GET crond job (duedate-notify-cron.php)')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings VALUES (9, 'timeout','20','','Number of minutes before a user is automatically logged off when there is no activity');");

	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'Logon message', '', NOW(), 'This message will be displayed when a user logs in.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'navtype', 'TABS', NOW(), 'Navigation bar type. Use NOTABS for normal navigation, anything else for tabs')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'langoverride', 'No', NOW(), 'Language override. No to let the user be able to choose his or her own language, yes to disable this feature and thereby force the use of the system-wide language choosen hereunder.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'EmailNewEntities', '', NOW(), 'The e-mail address to which notifications of added entities should be mailed. Leave empty for no notification.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'MonthsToShow', '7', NOW(), 'The number of months to show in the various calendar appearances.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'ShowDeletedViewOption', 'No', NOW(), 'Wheter or not CRM-CTT should display a menu tab to view the deleted entities. Options are yes or no.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'EnableCustInsert', 'No', NOW(), 'Set this to yes to enable the [customer] insert functionality, no to keep customers from logging in even if they have a customer account.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'SMTPserver', 'localhost', NOW(), 'The hostname or IP-address of your SMTP (outgoing mail) server')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'BODY_DUEDATE', '<BODY><FONT FACE=\"Tahoma\" SIZE=\"2\"><B>An alarm date of one of your entities has been reached.</B><BR><BR>This email is concerning entity @ENTITYID@, @CATEGORY@<BR><BR><BR>The history of this entity is printed below.<br>If this email was not intended for you, please contact owner @OWNER@ or assignee @ASSIGNEE@.<BR><BR>History:<BR><BR><TABLE BORDER=1><TR><TD>@CONTENTS@.</TD></TR></TABLE><BR><BR>If you do nothing, you will <I>not</I> be reminded about this entity again.<BR><BR>End of this e-mail.<BR><TABLE><TR><TD>Entity:</TD><TD>@ENTITYID@</TD></TR><TR><TD>Category:</TD><TD>@CATEGORY@</TD></TR><TR><TD>Owner:</TD><TD>@OWNER@</TD></TR><TR><TD>Assignee:</TD><TD>@ASSIGNEE@</TD></TR><TR><TD>Contents:</TD><TD>See attachment</TD></TR><TR><TD>Admin email:</TD><TD>@ADMINEMAIL@</TD></TR><TR><TD>Webhost:</TD><TD>@WEBHOST@</TD></TR><TR><TD>Title:</TD><TD>@TITLE@</TD></TR><TR><TD>Customer:</TD><TD>@CUSTOMER@</TD></TR><TR><TD>Due-date:</TD><TD>@DUEDATE@</TD></TR><TR><TD>Status:</TD><TD>@STATUS@</TD></TR><TR><TD>Priority:</TD><TD>@PRIORITY@</TD></TR></TABLE></FONT></BODY>', NOW(), 'The body of the email which will be sent to an assignee when an alarm date of a certain entity is met. Please read the manual before editing this setting.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'BODY_ENTITY_ADD', '<BODY><FONT FACE=\"Tahoma\" SIZE=\"2\"><B>A new entity was added to repository \"@TITLE@\"</B><BR><BR>This email is concerning a new entity with category \"@CATEGORY@\"<BR>This entity will be available in your CRM-CTT installation at @WEBHOST@ under EID number @ENTITYID@.<BR><BR>If this email was not intended for you, please contact @ADMINEMAIL@<BR><BR><TABLE><TR><TD>Entity:</TD><TD>@ENTITYID@</TD></TR><TR><TD>Category:</TD><TD>@CATEGORY@</TD></TR><TR><TD>Owner:</TD><TD>@OWNER@</TD></TR><TR><TD>Assignee:</TD><TD>@ASSIGNEE@</TD></TR><TR><TD>Contents:</TD><TD>See attachment</TD></TR><TR><TD>Admin email:</TD><TD>@ADMINEMAIL@</TD></TR><TR><TD>Webhost:</TD><TD>@WEBHOST@</TD></TR><TR><TD>Title:</TD><TD>@TITLE@</TD></TR><TR><TD>Customer:</TD><TD>@CUSTOMER@</TD></TR><TR><TD>Due-date:</TD><TD>@DUEDATE@</TD></TR><TR><TD>Status:</TD><TD>@STATUS@</TD></TR><TR><TD>Priority:</TD><TD>@PRIORITY@</TD></TR></TABLE></FONT></BODY>', NOW(), 'The body of the email which will be sent when a new entity is added. Please read the manual before editing this setting.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'BODY_ENTITY_EDIT', '<BODY><FONT FACE=\"Tahoma\" SIZE=\"2\"><B>One of your entities in repository \"@TITLE@\" was updated.</B><BR><BR>This email is concerning your entity with category \"@CATEGORY@\"<BR>This entity is available in your CRM-CTT installation at @WEBHOST@ under EID number @ENTITYID@. <BR><BR>If this email was not intended for you, please contact @ADMINEMAIL@<BR><BR><TABLE><TR><TD>Entity:</TD><TD>@ENTITYID@</TD></TR><TR><TD>Category:</TD><TD>@CATEGORY@</TD></TR><TR><TD>Owner:</TD><TD>@OWNER@</TD></TR><TR><TD>Assignee:</TD><TD>@ASSIGNEE@</TD></TR><TR><TD>Contents:</TD><TD>See attachment</TD></TR><TR><TD>Admin email:</TD><TD>@ADMINEMAIL@</TD></TR><TR><TD>Webhost:</TD><TD>@WEBHOST@</TD></TR><TR><TD>Title:</TD><TD>@TITLE@</TD></TR><TR><TD>Customer:</TD><TD>@CUSTOMER@</TD></TR><TR><TD>Due-date:</TD><TD>@DUEDATE@</TD></TR><TR><TD>Status:</TD><TD>@STATUS@</TD></TR><TR><TD>Priority:</TD><TD>@PRIORITY@</TD></TR></TABLE></FONT></BODY>', NOW(), 'The body of the email which will be sent when an entity is updated. Please read the manual before editing this setting.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'ShowNumOfAttachments', 'No', NOW(), 'Wether or not to show the number of attached documents in the main entity lists')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'ShowEmailButton', 'Yes', NOW(), 'Yes to show an extra button to send an e-mail to the assignee when an entity is added or edited, no to disable this option.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'ShowMainPageCalendar', 'Yes', NOW(), 'Yes to show the 3-month calendar on the main page, no to disable this option.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'Category pulldown list', '', NOW() , '')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'ForceCategoryPulldown', '', NOW() , 'Yes to show a pulldown list for the category, no to make it a text box.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'ShowRecentEditedEntities', '7', NOW(), '0 for no recent list, any number under 20 to show the most recent edited entities on the main page.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'EnableEntityJournaling', 'Yes', NOW() , 'Set this value to Yes if you want entity journaling enabled (a link will be added to the main edit entity page)')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'AutoCompleteCustomerNames', 'No', NOW() , 'Set this value to Yes if you want a text box wich auto-completes customer names instead of a pull-down menu with all customers.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'EnableEntityContentsJournaling', 'Yes', NOW() , 'Set this value to Yes if you want a drop-down box in the main entity window to switch to history states of an entity')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'DontShowPopupWindow', 'Yes', NOW() , 'No to show the javascript popup window in the entity overview, yes to disable it and make editing the entity the default action when clicking on the row.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'ShowFilterInMainList', 'Yes', NOW() , 'Wether or not to show the filter pulldowns on top of the main entity list')");
	//array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'ShowShortKeyLegend', 'Yes', NOW() , 'Wether or not to show the ShortKeys (ALT-1..ALT-9) legend on the tabs')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'LetUserSelectOwnListLayout', 'Yes', NOW() , 'Wether or not to let the user select his/her own list layout')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'BODY_TEMPLATE_CUSTOMER', '<FONT face=\"Tahoma\"><P><FONT size=2>&gt;&gt; This mail is regarding entity @ENTITYID@ , \"@CATEGORY@\" in CRM @TITLE@ at @WEBHOST@<BR>-----------------------<BR>Send from CRM-CTT<BR></FONT><A href=\"http://www.crm-ctt.com/\"><FONT size=2>http://www.crm-ctt.com</FONT></A><BR></P></FONT>', NOW() , 'The template wich is used when sending an e-mail to a customer (editable by user before sending)')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'ShowPDWASLink', 'No', NOW() , 'Yes to show the PDWAS link in the file list. PDWAS is a CRM-CTT addon which enables you to edit flies and directly save them to CRM-CTT without having to upload the file manually.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'EnableWebDAVSubsystem', 'No', NOW() , 'Yes to enable the WebDAV subsystem, no to disable it')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'DateFormat', 'dd-mm-yyyy', NOW() , 'Enter \'mm-dd-yyyy\' here to display dates in US format, anything else to display dates in international format (which is dd-mm-yyyy).')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'HideCustomerTab', 'No', NOW() , 'Set this value to \'Yes\' if you want the customer tab only to be visible to administrators')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'CustomerListColumnsToShow', 'a:5:{s:2:\"id\";b:1;s:11:\"cb_custname\";b:1;s:10:\"cb_contact\";b:1;s:16:\"cb_contact_phone\";b:1;s:9:\"cb_active\";b:1;}', NOW() , 'The columns to show in the customer list')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'ShowSaveAsNewEntityButton', 'Yes', NOW() , 'Yes to show the Save As New Entity button, no to hide it.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'AutoCompleteCategory', 'Yes', NOW() , 'Enter Yes of you would like type-ahead functionality in the category field on the main entity page')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'AutoInsertDateTime', 'No', NOW() , 'Enter Yes of you would like the date and time information inserted automatically when adding text to an entity.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'LetUserEditOwnProfile', 'Yes', NOW() , 'Set this option to \'Yes\' to enable user to change their passwords, edit their full name, and select wether or not they want to receive the daily entity overwiew email.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'EnableRepositorySwitcher', 'Yes', NOW() , 'Set this option to \'Yes\' to enable a user to dynamically switch between repositories in which the same users exist with the same password. \'No\' disables this, \'Admin\' enables it only for admins.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'BODY_ENTITY_CUSTOMER_ADD', 'You are registerd to customer @CUSTOMER@. Entity @ENTITYID@ was just coupled to that customer, so you might have to do something.', NOW() , 'The body of the e-mail which is send to the customer_owner when an entity (new or existing) is coupled to that customer, and the email_customer_upon_action checkbox in the customer properties is checked.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'BlockAllCSVDownloads', 'No', NOW() , 'Set this value to Yes if you want to block all CSV/Excel downloads for all users except for administrators.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'STANDARD_TEXT', '', NOW() , 'A list of standard comments which users can automatically insert as a reply in entities. Leave empty for no standard comments.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'CUSTOMER_LIST_TRESHOLD', '150', NOW() , 'The number of customers listed on the main customers page. If this number of customers is exceeded, the list will not appear for bandwith reasons.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'ALSO_PROCESS_DELETED', 'No', NOW() , 'Set this option to Yes if you want the duedate notify script to also process entities on their duedate, even if the entity is deleted.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'BODY_EMAILINSERT_REPLY', '<P><FONT face=\"Tahoma\" size=2><B>Your e-mail was added to repository @TITLE@</B><BR></P> <P> The number is : @EID@ Number of attachments saved: @NUM_ATTM@ </P>', NOW() , 'The body of the e-mail which is send as a reply to people who use the email_in script to log an entity')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'SUBJECT_EMAILINSERT_REPLY', 'Your e-mail to CRM-CTT was saved under number @EID@ in repository @TITLE@', NOW() , 'The subject of the e-mail which is send as a reply to people who use the email_in script to log an entity')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'ForceSecureHTTP', 'No', NOW() , 'If set to yes, CRM-CTT will redirect the user to the HTTPS equivalent of the URL he/she is using, to force secure browsing. Your webserver must be configured to accept this.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'BODY_MainPageMessage', '', NOW() , 'When set, this message will be displayed on the main page.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'InvoiceNumberPrefix', '[unconfigured]', NOW() , 'Some text to prefix invoice numbers with')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'NextInvoiceNumberCounter', '0', NOW() , 'The invoice number counter (not accessable)')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'EnableSingleEntityInvoicing', 'No', NOW() , 'Set this value to Yes to enable per-entity invoicing using the invoice icon on the main edit entity page.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'PDF-ExtraFieldsInTable', 'No', NOW() , 'Set this value to Yes to have extra fields in PDF reports show up in a table instead of each value being printed on a new line.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'EnableEntityReporting', 'Yes', NOW() , 'Set this value to Yes to be able to create per-entity or batch RTF reports (a word-icon will appear on the edit entity page and a link will be added to the main page)')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'DisplayNOToptioninfilters', 'No', NOW() , 'Set this value to Yes to have all filter drop-down boxes also contain logical NOT-operands, like status NOT open.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'AUTOASSIGNINCOMINGENTITIES', 'No', NOW() , 'Set this option to Yes to automatically assign incoming entities to the owner of the customer.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'FORCEDFIELDSTEXT', 'This message is not configured (see admin section). Probably you missed some fields in your form. ', NOW() , 'The message which is displayed when a user did not fill in all required form fields.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'EnableEntityLocking', 'Yes', NOW() , 'Set this to Yes to enable entity locking to prevent two people from editing the same entity')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'DFT_FOREGROUND_COLOR', '#c60', NOW() , 'The color of links')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'DFT_FORM_COLOR', '#c60', NOW() , 'The color form elements and values')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'DFT_PLAIN_COLOR', '#494949', NOW() , 'The color of normal, non-linked, non-form text')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'DFT_LEGEND_COLOR', '#3366FF', NOW() , 'The color of fieldset legends')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'DFT_FONT', 'Tahoma', NOW() , 'The main font')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'DFT_FONT_SIZE', '11', NOW() , 'The main font size')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'REQUIREDDEFAULTFIELDS', 'No', NOW() , 'SHOULD NOT BE VISIBLE')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'MailMethod', 'smtp', NOW() , 'The method to use for sending mail. Can be either sendmail, mail (=PHP mail) or smtp.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'MailUser', '', NOW() , 'The username of your authenticated SMTP-server (only when using authenticated SMTP)')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'MailPass', '', NOW() , 'The password of your authenticated SMTP-server (only when using authenticated SMTP)')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'UseWaitingAndDoesntBelongHere', 'No', NOW() , 'Set this value to Yes to enable the (old) waiting and doesnt belong here fields')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'PersonalTabs', '', NOW() , 'Set this to Yes to disable the main entity comment field')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'DisableCommentField', 'No', NOW() , 'Set this to Yes to disable the main entity comment field')");

	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'EMAILINBOX', '', NOW() , 'The credentials for the read-only access to an POP3 e-mail inbox')");

	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('HIDEADDTAB', 'No', NOW() , 'Set this to Yes to hide the second tab used to add entities')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('HIDECSVTAB', 'No', NOW() , 'Set this to Yes to hide the CSV tab used to download CRM-CTT exports')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('HIDEPBTAB', 'No', NOW() , 'Set this to Yes to hide the phone book tab')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('HIDESUMMARYTAB', 'No', NOW() , 'Set this to Yes to hide the summary tab')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('HIDEENTITYTAB', 'No', NOW() , 'Set this to Yes to hide main entity list tab')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('CAL_MINHOUR', '7', NOW() , 'Starting hour of day, used for scheduling enties')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('CAL_MAXHOUR', '18', NOW() , 'Ending hour of day, used for scheduling enties (24h format: for 6pm use 18')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('CAL_USEWEEKEND', 'No', NOW() , 'Wheter or not to also show the weekend days in the week view of the calendar')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('ShowMainPageLinks', '', NOW() , 'Some links to show on the main page. Leave empty for no links')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('EnableMailMergeAndInvoicing', 'No', NOW() , 'Set to Yes to enable mail merges and invoicing (even then, only for admins)')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('DefaultVAT', '19', NOW() , 'Default VAT percentage (only for use with invoicing)')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('subject_new_entity', 'A new entity was added to repository @TITLE@ (@CATEGORY@)', NOW() , 'The subject of the mail which is send when a new entity is added')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('subject_customer_couple', 'Your customer got a new entity coupled in repository @TITLE@', NOW() , 'The subject of the mail which is send to a customer owner when a new entity is coupled to his/her customer')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('subject_update_entity', 'One of your entities was updated in repository @TITLE@', NOW() , 'The subject of the mail which is send to a user owner when his/her entity was updated')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting , value , datetime , discription) VALUES ('subject_alarm', 'Alarm notification for entity @ENTITYID@ (@CATEGORY@)', NOW() , 'The subject of the mail which is send to a user owner when his/her entity reaches an alarm date')");
	
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting,value,discription) VALUES('MainListColumnsToShow','a:9:{s:2:\"id\";b:1;s:7:\"cb_cust\";b:1;s:8:\"cb_owner\";b:1;s:11:\"cb_assignee\";b:1;s:9:\"cb_status\";b:1;s:11:\"cb_priority\";b:1;s:11:\"cb_category\";b:1;s:10:\"cb_duedate\";b:1;s:12:\"cb_alarmdate\";b:1;}','non-editable by admin')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (setting,value,discription) VALUES('DBVERSION','" . $version . "','The current database version')");

	// Status and priority

	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "statusvars(varname,color) VALUES('Open','#66CC66')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "statusvars(varname,color) VALUES('Closed','#FF6633')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "statusvars(varname,color) VALUES('Awaiting closure','#3399CC')");

	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "priorityvars(varname,color) VALUES('Critical','#FF6666')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "priorityvars(varname,color) VALUES('High','#FFFF66')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "priorityvars(varname,color) VALUES('Medium','#FFFF99')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "priorityvars(varname,color) VALUES('Low','#FFFFCC')");



	// Language

	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "languages (id , LANGID , TEXTID , TEXT) VALUES ('', 'ENGLISH', 'stillchecked1', 'This file is still locked for editing by')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "languages (id , LANGID , TEXTID , TEXT) VALUES ('', 'ENGLISH', 'stillchecked2', '. Please stop editing this file before trying to check it in.')");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "languages (id , LANGID , TEXTID , TEXT) VALUES ('', 'ENGLISH', 'saveasnewentity', 'Save as new entity')");

	// Dynamic stuff

	$val = ini_get("session.save_path");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings (settingid , setting , value , datetime , discription) VALUES ('', 'TMP_FILE_PATH', '" . $val . "', NOW() , 'The path to the directory where CRM-CTT (the user under which your webserver runs) can store temporary files.')");
	


	// ------------------------------------- CRM-CTT 3.3.0 DATABASE UPDATES

	array_push($sqla,"ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "extrafields` CHANGE `options` `options` LONGTEXT NOT NULL");

	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'ENTITY_ADD_FORM', 'Default', NOW( ) , 'The HTML form template to use when a normal user adds an entity')");


	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'BODY_URGENTMESSAGE', '', NOW( ) , 'When set, this message will be displayed above <b>all</b> pages. Only use this for very urgent matters. ')");
	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'AUTH_TYPE', 'CRM-CTT Only', NOW( ) , 'The method to use for authentication. ALWAYS: user must exist in CRM-CTT. HTTP REALM: already authenticated users can log in without a password (INTRANET). LDAP: authentications with an LDAP server (allso fill in LDAP_SERVER, LDAP_PORT, LDAP_PREFIX).')");

	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'ENTITY_EDIT_FORM', 'Default', NOW( ) , 'The HTML form template to use when a normal user edits an entity')");

	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'ENTITY_LIMITED_ADD_FORM', 'Default', NOW( ) , 'The HTML form template to use when a limited user adds an entity')");

	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'ENTITY_LIMITED_EDIT_FORM', 'Default', NOW( ) , 'The HTML form template to use when a limited user edits an entity')");

	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'BODY_LIMITEDHEADER', '', NOW( ) , 'This HTML template will be shown at the top of the limited interface')");

	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'SHOW_ADMIN_TOOLTIPS', 'Yes', NOW( ) , 'Wether or not to display tool-tips in the administrative section.')");

	array_push($sqla,"CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "userprofiles (  id int(11) NOT NULL auto_increment,  name varchar(50) NOT NULL default '',  ENTITYADDFORM varchar(50) NOT NULL default '',  ENTITYEDITFORM varchar(50) NOT NULL default '',  active enum('yes','no') NOT NULL default 'yes',  CLLEVEL varchar(50) NOT NULL default 'ro',  RECEIVEDAILYMAIL enum('No','Yes') NOT NULL default 'No',  RECEIVEALLOWNERUPDATES enum('n','y') NOT NULL default 'n',  RECEIVEALLASSIGNEEUPDATES enum('n','y') NOT NULL default 'n',  HIDEADDTAB char(1) NOT NULL default '',  HIDECSVTAB char(1) NOT NULL default '',  HIDESUMMARYTAB char(1) NOT NULL default '',  HIDEENTITYTAB char(1) NOT NULL default '',  HIDEPBTAB char(1) NOT NULL default '',  SHOWDELETEDVIEWOPTION char(1) NOT NULL default '',  HIDECUSTOMERTAB char(1) NOT NULL default '',  SAVEDSEARCHES longtext NOT NULL,  EMAILCREDENTIALS longtext NOT NULL,  PRIMARY KEY  (id),  KEY name (name),  FULLTEXT KEY SAVEDSEARCHES (SAVEDSEARCHES),  FULLTEXT KEY EMAILCREDENTIALS (EMAILCREDENTIALS)) TYPE=MyISAM COMMENT='CRM User profile definition table'");

	array_push($sqla, "ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "loginusers` ADD `PROFILE` VARCHAR( 10 ) NOT NULL AFTER `password`");

	array_push($sqla, "ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "loginusers` ADD `ENTITYADDFORM` VARCHAR( 10 ) NOT NULL ,ADD `ENTITYEDITFORM` VARCHAR( 10 ) NOT NULL");

	array_push($sqla, "ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "customaddons` CHANGE `name` `name` BIGINT NOT NULL");
	array_push($sqla, "ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "customaddons` ADD INDEX ( `name` )");
	array_push($sqla, "ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "customaddons` DROP INDEX `name_2`");

	// CRM 3.3.2 Alterations

	array_push($sqla,"ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "entity` ADD `parent` BIGINT DEFAULT '0' NOT NULL");
	array_push($sqla,"ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "extrafields` ADD `sort` ENUM( 'n', 'y' ) NOT NULL");
	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'EnableEntityRelations', 'No', NOW( ) , 'Set this value to Yes to enable entity relations.')");
	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'HideChildsFromMainList', 'No', NOW( ) , 'When enabled, child entities will no longer show up on the main list.')");

	array_push($sqla, "INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'LDAP_SERVER', '', NOW( ) , 'The name of the LDAP server')");
	array_push($sqla, "INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'LDAP_PORT', '389', NOW( ) , 'The port of the LDAP server; secure=636, non-secure=389 (Default)')");
	array_push($sqla, "INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'LDAP_PREFIX', '', NOW( ) , 'The prefix to use before a username on the LDAP server. End with 1 backslash, no two.')");
	array_push($sqla, "ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "loginusers` ADD `LIMITTOCUSTOMERS` LONGTEXT NOT NULL");
	array_push($sqla, "ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "userprofiles` ADD `LIMITTOCUSTOMERS` LONGTEXT NOT NULL");

	// CRM 4.0.0 Alterations

	array_push($sqla, "CREATE TABLE `" . $GLOBALS['TBL_PREFIX'] . "internalmessages` (`id` BIGINT NOT NULL AUTO_INCREMENT ,`to` BIGINT NOT NULL ,`from` BIGINT NOT NULL ,`time` TIMESTAMP NOT NULL ,`read` ENUM( 'n', 'y' ) NOT NULL ,`body` MEDIUMTEXT NOT NULL ,PRIMARY KEY ( `id` ) ,INDEX ( `to` ) ) TYPE = MYISAM COMMENT = 'CRM-CTT Internal messages (user-to-user)'");

	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'FormFinity', 'Yes', NOW( ) , 'When set to Yes, entities will \'remember\' what form was used to create them, and the entity will always show up in that form.')");
	array_push($sqla,"ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "entity` ADD `formid` MEDIUMINT DEFAULT '0' NOT NULL");
	array_push($sqla,"ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "ejournal` ADD `formid` MEDIUMINT DEFAULT '0' NOT NULL");
	array_push($sqla,"ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "loginusers` ADD `ADDFORMS` LONGTEXT NOT NULL");
	array_push($sqla,"ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "userprofiles` ADD `ADDFORMS` LONGTEXT NOT NULL");
	
	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'UNIFIED_FROMADDRESS', '', NOW( ) , 'An address entered here, will *always* overwrite the from-address in mails. All mails will have this from-address.')");

	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'CUSTOMCUSTOMERFORM', '', NOW( ) , 'When you enter the (valid) number of a customer HTML-form here, all customer records will be shown in that form.')");

	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'DISPLAYNUMSUMINMAINLIST', 'Yes', NOW( ) , 'When set to Yes, the total value of numeric fields will be displayed under the main entity list.')");


	// CRM 3.4.1 Adjustments

	array_push($sqla, "CREATE TABLE `" . $GLOBALS['TBL_PREFIX'] . "blobs` ( `fileid` bigint(20) NOT NULL, `content` mediumblob NOT NULL,PRIMARY KEY  (`fileid`) ) TYPE=MyISAM COMMENT='Blob stand-alone table';");
	
	array_push($sqla, "ALTER TABLE " . $GLOBALS['TBL_PREFIX'] . "binfiles DROP content");
	array_push($sqla, "ALTER TABLE " . $GLOBALS['TBL_PREFIX'] . "binfiles ADD INDEX ( `filetype` ) ");
	array_push($sqla, "DELETE FROM " . $GLOBALS['TBL_PREFIX'] . "settings WHERE setting='STASH'");

	array_push($sqla, "CREATE TABLE " . $GLOBALS['TBL_PREFIX'] . "accesscache (  cacheid bigint(20) NOT NULL auto_increment,  user bigint(20) NOT NULL,  type enum('e','c') NOT NULL,  eidcid bigint(20) NOT NULL,  result enum('nok','readonly','ok') NOT NULL,  PRIMARY KEY  (cacheid),  KEY user (user),  KEY type (type)) TYPE=MyISAM COMMENT='CRM Access cache table'");

	array_push($sqla, "INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'USE_EXTENDED_CACHE', 'Yes', NOW( ) , 'Use extensive access rights and extra fields caching. Improves performance.')");
	 
	 // ----------------------------ooo
	 
	array_push($sqla, "ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "loginusers` ADD `ELISTLAYOUT` TEXT NOT NULL");
	array_push($sqla, "ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "loginusers` ADD `CLISTLAYOUT` TEXT NOT NULL");
	array_push($sqla, "CREATE TABLE `" . $GLOBALS['TBL_PREFIX'] . "contactmoments` (`id` INT NOT NULL AUTO_INCREMENT ,`eidcid` INT NOT NULL ,`type` ENUM( 'entity', 'customer' ) NOT NULL ,`user` INT NOT NULL ,`meta` TEXT NOT NULL ,`body` LONGTEXT NOT NULL ,`date` TIMESTAMP NOT NULL ,PRIMARY KEY ( `id` ) ) TYPE = MYISAM COMMENT = 'CRM Contact moments journal'");

	array_push($sqla, "ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "contactmoments` ADD `to` TEXT NOT NULL");
	
	array_push($sqla,"ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "extrafields` ADD `storetype` ENUM( 'default','3rd_table','3d_table_popup' ) NOT NULL");
	
	array_push($sqla,"ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "extrafields` ADD `accessarray` LONGTEXT NOT NULL ");

	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'CHECKFORDOUBLEADDS', 'Yes', NOW( ) , 'CRM-CTT checks if an entity is not added twice within an hour. If this bothers you, disable this check by setting this to No.')");

	array_push($sqla,"ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "extrafields` ADD `size` INT NOT NULL");

	// CRM 3.4.2 Adjustments

	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'NOBARSWINDOW', 'No', NOW( ) , 'Set this option to Yes to force a no-bars window');");

	array_push($sqla, "ALTER TABLE " . $GLOBALS['TBL_PREFIX'] . "customaddons ADD KEY eid (eid)");
	array_push($sqla, "ALTER TABLE " . $GLOBALS['TBL_PREFIX'] . "entity ADD KEY formid (formid)");


	// CRM 3.4.3 Adjustments

	array_push($sqla,"ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "loginusers` ADD `ALLOWEDSTATUSVARS` LONGTEXT NOT NULL , ADD `ALLOWEDPRIORITYVARS` LONGTEXT NOT NULL");
	array_push($sqla,"ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "userprofiles` ADD `ALLOWEDSTATUSVARS` LONGTEXT NOT NULL , ADD `ALLOWEDPRIORITYVARS` LONGTEXT NOT NULL");
	array_push($sqla,"UPDATE " . $GLOBALS['TBL_PREFIX'] . "settings SET value='Tahoma' WHERE setting='DFT_FONT'");
	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings(setting, value, discription) VALUES('Mass_Update', 'No', 'Set this to yes to enable mass entity updates using checkboxes on the main list')");
	array_push($sqla,"ALTER TABLE `" . $GLOBALS['TBL_PREFIX'] . "triggers` ADD `comment` MEDIUMTEXT NOT NULL");
	array_push($sqla,"DELETE FROM `" . $GLOBALS['TBL_PREFIX'] . "settings` WHERE setting='FormFinity'");
	//array_push($sqla,"CREATE TABLE `" . $GLOBALS['TBL_PREFIX'] . "entityformcache` (`formcacheid` mediumint(9) NOT NULL auto_increment,`eid` mediumint(9) NOT NULL,`formid` mediumint(9) NOT NULL,`user` mediumint(9) NOT NULL,`content` longtext NOT NULL,`timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, PRIMARY KEY  (`formcacheid`)) TYPE=MyISAM COMMENT='CRM-CTT Cached parsed forms'");
	array_push($sqla,"CREATE TABLE `" . $GLOBALS['TBL_PREFIX'] . "entityformcache` (  `formcacheid` mediumint(9) NOT NULL auto_increment,  `eid` mediumint(9) NOT NULL,  `formid` mediumint(9) NOT NULL,  `user` mediumint(9) NOT NULL,  `content` longtext NOT NULL,  `timestamp` timestamp(14) NOT NULL,  PRIMARY KEY  (`formcacheid`)) TYPE=MyISAM COMMENT='CRM-CTT Cached parsed forms'");
	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'PaginateMainEntityList', '30', NOW( ) , '0 for no pagination, [number] for max number of entities per page.');");

	array_push($sqla, "CREATE TABLE `" . $GLOBALS['TBL_PREFIX'] . "failoverquerystore` (`id` mediumint(9) NOT NULL auto_increment,`query` mediumblob NOT NULL,`targethost` varchar(100) NOT NULL, `lockhash` varchar(100) NOT NULL, PRIMARY KEY  (`id`)) TYPE=MyISAM COMMENT='CRM Fail-over queries to replicate'");
		
	array_push($sqla,"INSERT INTO `" . $GLOBALS['TBL_PREFIX'] . "settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'MAINTENANCE_MODE', 'No', NOW( ) , 'When set to Yes, only administrators can log in. Required when upgrading.');");

	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "settings(setting, value, discription) VALUES('ONEENTITYPERCUSTOMER', 'No', 'When enabled, only one entity per customer may exist.')");

	return($sqla);

} // end func

function PostInstall() {
	require_once("functions.php");
	AttachFileS("0","SampleHTMLReport","<P><FONT color=#000000><IMG alt=\"\" hspace=0 src=\"crm_small.png\" align=baseline border=0>&nbsp;<FONT color=#000000>[<A href=\"edit.php?e=@EID@\">edit</A>] <FONT color=#ff0000><EM>@EID@: @CATEGORY@ </EM></FONT><BR></FONT>Attachments: @NUM_ATTM@<BR>Owned by @OWNER@, assigned to @ASSIGNEE@<BR>For customer @CUSTOMER@, contact is @CUSTOMER_CONTACT@</P><HR>","entity","TEMPLATE_HTML_REPORT","Sample HTML Report (edit this in the template section)");

	$files_to_add = array("sample_invoice_template.rtf","sample_mailmerge_template.rtf","sample_invoice_template_multiple_VAT.rtf","sample_invoice_template_single_VAT.rtf");

	foreach ($files_to_add AS $file) { 
		$fp=@fopen($file,"r");
		$filecontent=@fread($fp,@filesize($file));
		@fclose($fp);
		AttachFile("0",$file,$filecontent,"entity","TEMPLATE_INVOICE");
		//array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "binfiles(koppelid,content,filename,filesize,filetype,username) VALUES('0','" . $filecontent . "','" . $file . "','" . @filesize($file) . "','TEMPLATE_INVOICE','Hidde Fennema')");		

	}

	$fp=@fopen("sample_entity_report_template.rtf","r");
	$filecontent=@fread($fp,@filesize("sample_entity_report_template.rtf"));
	@fclose($fp);
	AttachFile("0","sample_entity_report_template.rtf",$filecontent,"entity","TEMPLATE_REPORT");

	AttachFileS("0","Joes helpdesk - edit entity form template (example)","<FIELDSET><LEGEND align=left><FONT size=+1>Joe's Own Helpdesk - ticket&nbsp;@EID@: @CATEGORY@ #LOCKICON#&nbsp;</FONT></LEGEND><TABLE width=\"90%\"><TBODY><TR><TD><TABLE><TBODY><TR><TD vAlign=top><FIELDSET><LEGEND align=left>User/customer</LEGEND>#CUSTOMER#</FIELDSET></TD><TD vAlign=top><FIELDSET><LEGEND align=left>Status&nbsp;</LEGEND>#STATUS#</FIELDSET> </TD><TD vAlign=top><FIELDSET><LEGEND align=left>Priority</LEGEND>#PRIORITY#</FIELDSET> </TD><TD vAlign=top><FIELDSET><LEGEND align=left>Short problem description&nbsp;</LEGEND>#CATEGORY# </FIELDSET> </TD><TD></TD></TR></TBODY></TABLE><TABLE cellSpacing=1 cellPadding=2 width=\"100%\" border=0><TBODY><TR><TD>#CONTENTS# </TD><TD><FIELDSET><LEGEND align=left>Owner</LEGEND>#OWNER# </FIELDSET> <BR><FIELDSET><LEGEND align=left>Assignee&nbsp;</LEGEND>#ASSIGNEE# </FIELDSET> <FIELDSET><LEGEND align=left>Due date&nbsp;</LEGEND>#DUEDATE# </FIELDSET> <FIELDSET><LEGEND align=left>Due time&nbsp;</LEGEND>#DUETIME# </FIELDSET><BR>#JOURNALICON# #REPORTICON# #PDFICON# #ACTICON# #LOCKICON# #ARROWS#</TD></TR></TBODY></TABLE><BR><TABLE width=\"30%\"><TBODY><TR><TD>Read-only to other users</TD><TD>#READONLYBOX#&nbsp;</TD></TR><TR><TD>Private</TD><TD>#PRIVATEBOX#</TD></TR><TR><TD>Deleted</TD><TD>#DELETEBOX#</TD></TR></TBODY></TABLE><TABLE><TBODY><TR><TD colSpan=6><FIELDSET><LEGEND align=left>Attach file&nbsp;</LEGEND>#FILEBOX# &nbsp;&nbsp;&nbsp;&nbsp; </FIELDSET> <FIELDSET><LEGEND align=left>Current files&nbsp;</LEGEND>#FILELIST# &nbsp;&nbsp;&nbsp;&nbsp; </FIELDSET></TD></TD></TR>#SAVEBUTTON# </TD></TR></TBODY></TABLE></TD></TR></TBODY></TABLE></FIELDSET>","entity","TEMPLATE_HTML_FORM","Joes helpdesk");
	
	BringInTheForms($GLOBALS['TBL_PREFIX']);
	
//	$filecontent = addslashes($filecontent);
//	array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "binfiles(koppelid,content,filename,filesize,filetype,username) VALUES('0','" . $filecontent . "','sample_entity_report_template.rtf','" . @filesize("sample_entity_report_template.rtf") . "','TEMPLATE_REPORT','Hidde Fennema')");

}


function get_perms($file) {
   $p_bin = substr(decbin(@fileperms($file)), -9) ;
   $p_arr = explode(".", substr(chunk_split($p_bin, 1,"."), 0, 17)) ;
   $perms = ""; $i = 0;
   foreach ($p_arr as $that) { 
      $p_char = ($i%3==0 ? "r" : ($i%3==1 ? "w" :"x")); 
      $perms .= ($that=="1" ? $p_char : "-") . ($i%3==2 ? " " : "");
      $i++;
   }
   return $perms;
}
function printbox($msg)
{
		global $printbox_size,$legend;
		
		if (!$printbox_size) {
			$printbox_size = "70%";
		}
		
		print "<center><table border='0' width='$printbox_size'><tr><td colspan=2><fieldset>";
		if ($legend) {
			print "<legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$legend</legend><center>";
		}
		print $msg . "</center></fieldset></td></tr></table><br></center>";
	
		unset($printbox_size);
		$legend = "";

} // end func

function mcqinstall($sql,$db) {
	global $mysql_query_counter, $logqueries;
	$mysql_query_counter++;

	if ($logqueries) {
		$fp = fopen("qlist.txt","a");
		fputs($fp,"$mysql_query_counter: $sql\n");
		fclose($fp);
	}
	$a = mysql_query($sql) or die (handle_error_install(mysql_error(),$sql));	
	$GLOBALS['Last_Insert'] = mysql_insert_id();
	return($a);
}

function handle_error_install($mysqlerror,$sql) { 
	global $REMOTE_ADDR, $HTTP_SERVER_VARS, $HTTP_POST_VARS, $HTTP_USER_AGENT;
	$mysqlerror = ereg_replace("You have an error in your SQL syntax near","SQL Syntax error near",$mysqlerror);
	print "<table><tr><td>&nbsp;<b><font size='+1' face='Trebuchet MS'>An internal error occured.</font></b><br>&nbsp;&nbsp;&nbsp;<font size='+1' face='Trebuchet MS'>This error is fatal.</font>";
	print "<br>";
	print "This procedure cannot tell you exactly what went wrong. Your database action is cancelled, but previous database actions in the CRM page you're running are executed.<br><br>";
	print "<table width=90% border=0><tr><td>The error message from the database is:</td></tr>";
	print "<tr><td><font face='courier new'>$mysqlerror</font></td></tr>";
	print "<tr><td>&nbsp;$sql</td></tr>";
	print "<tr><td>The concerning query is:</td></tr>";
	print "<tr><td><font face='courier new'>$sql</font></td></tr>";
	$deb .= "Host: " . getenv("SERVER_NAME") . "<br>";
	$deb .= "Client: $REMOTE_ADDR<br>";
	$deb .= "Location: " . $_SERVER[PHP_SELF] . "<br>";
}

function printheaderinst($msg) {
		print "<center><table border='0' width='$printbox_size'><tr><td colspan=2><fieldset>";
		if ($legend) {
			print "<legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$legend</legend>";
		}
		print $msg . "</fieldset></td></tr></table></center><br>";
}
// Journalling function (Entity ID, Message)
function journal2($eid,$msg,$JournalType="entity") {
	global $EnableEntityJournaling;
	if (strtoupper($EnableEntityJournaling)=="YES" || (stristr($msg,"[admin]"))) {
		
		$msg = addslashes($msg);

		// $msg = base64_encode($msg);

		$sql = "INSERT INTO " . $GLOBALS[TBL_PREFIX] ."journal (eid,user,message,type) VALUES('$eid','" . $GLOBALS[USERID] . "','$msg','" . $JournalType ."')";

		mcq($sql,$db);
	}
}

function BringInTheForms($prefix) {

			// THIS FUNCTION BRINGS IN THE FORMFINITY FORMS (ESSENTIAL DURING INSTALL) - 3.4.3 detail.

			// Create master template, update entities

			mcqinstall("INSERT INTO " . $prefix . "binfiles(koppelid,filename,username,file_subject,filetype) VALUES('','Default form','Hidde Fennema','Default form','TEMPLATE_HTML_FORM')", $db);
			$t = $GLOBALS['Last_Insert'];
			
			mcqinstall("INSERT INTO " . $prefix . "blobs(fileid,content) VALUES('" . $t . "','<br /><fieldset><legend>&nbsp;&nbsp;<font size=\"+1\">Entity @EID@ #LOCKICON# &nbsp;</font></legend><table width=\"90%\">    <tbody>        <tr>            <td>            <table>                <tbody>                    <tr>                        <td valign=\"top\"><fieldset><legend>&nbsp;&nbsp;Customer&nbsp;</legend>#CUSTOMER#</fieldset></td>                        <td valign=\"top\"><fieldset><legend>&nbsp;&nbsp;Priority&nbsp;</legend>#PRIORITY#</fieldset></td>                        <td valign=\"top\"><fieldset><legend>&nbsp;&nbsp;Status&nbsp;</legend>#STATUS#</fieldset></td>                        <td valign=\"top\" nowrap=\"nowrap\"><fieldset><legend>&nbsp;&nbsp;Owner&nbsp;</legend>#OWNER#</fieldset></td>                        <td valign=\"top\"><fieldset><legend>&nbsp;&nbsp;Assignee&nbsp;</legend>#ASSIGNEE#</fieldset></td>                        <td nowrap=\"nowrap\">#PDFICON# #WEBDAVICON# #ACTICON# #REPORTICON# #PRINTICON# #INVOICEICON# #JOURNALICON# #EMAILPOPLINK#</td>                    </tr>                </tbody>            </table>            </td>        </tr>    </tbody></table><table>    <tbody>        <tr>            <td><fieldset><legend>&nbsp;&nbsp;Category&nbsp;</legend>#CATEGORY# &nbsp;</fieldset> </td>            <td><fieldset><legend>&nbsp;&nbsp;Due date&nbsp;</legend>#DUEDATE# #DUETIME#&nbsp; </fieldset> </td>        </tr>    </tbody></table><p><br />#CONTENTS#<br />#COMMENTBOX#</p><p>#ALL_EXTRA_FIELDS#</p><table>    <tbody>        <tr>            <td><br /><fieldset>            <table>                <tbody>                    <tr><td>#DELETEBOX# Deleted entity&nbsp;&nbsp;&nbsp;&nbsp;</td><tr>                        <td>#READONLYBOX# Read-only to other users&nbsp;&nbsp;&nbsp;&nbsp;</td>                    </tr>                    <tr>                        <td>#PRIVATEBOX# Private</td>                    </tr>                </tbody>            </table>            </fieldset></td>        </tr>    </tbody></table><table>    <tbody>        <tr>            <td colspan=\"6\"><fieldset>            <p><legend>&nbsp;Attach file&nbsp;</legend>#FILEBOX#<br />#FILELIST#</p>            </fieldset></td>        </tr>    </tbody></table><table width=\"90%\">    <tbody>        <tr>            <td>#EMAILDROPDOWN# #SAVEBUTTON#</td>        </tr>    </tbody></table></fieldset>')", $db);

			mcqinstall("UPDATE " . $prefix . "entity SET formid=" . $t . " WHERE formid=0", $db);
			

			mcqinstall("UPDATE " . $prefix . "userprofiles SET ADDFORMS='a:1:{i:0;s:1:\"" . $t . "\";}'", $db);

							
}

?>
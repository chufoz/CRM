<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * admin.php. This script forsees in some (most) general admin tasks
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

// USER CONFIGURABLE SETTING:
// Wether or not to allow direct SQL queries (SEE MANUAL!)
// Uncomment (e.g. remove the "//") the underlying line to disable direct admin-user queries to the database

// $Disable_direct = "1";

// Uncomment the above line to disable direct admin-user queries to the database

// END USER CONFIGURABLE SETTING -----------------------------------------------------------------------------

extract($_REQUEST);

if ($_REQUEST['reposman']) {
	$EnableRepositorySwitcherOverrule="n";
}

if ($_REQUEST['ActivityUserGraph']) {
	include("config.inc.php");
	include("getset.php");	

	$whom = GetUserName($ActivityUserGraph);

	$GLOBALS['CURFUNC'] = "ActivityUserGraph::";
	qlog("Generating user activity graph for user " . $whom);

	MustBeAdmin();

	if ($Journal) {
		DisplayUserActivityGraphJournal($ActivityUserGraph);	
	} else {
		DisplayUserActivityGraph($ActivityUserGraph);	
	}

	exit;
}
if ($_REQUEST['ExportUsers']) {
	include("config.inc.php");
	include("getset.php");	

	MustBeAdmin();
	$nonavbar=1;
	ExportUsers();
	exit;
} elseif ($_REQUEST['ExportSettings']) {
	include("config.inc.php");
	include("getset.php");	

	MustBeAdmin();
	$nonavbar=1;
	ExportSettings();
	exit;
} elseif ($_REQUEST['ExportExtraFields']) {
	include("config.inc.php");
	include("getset.php");	

	MustBeAdmin();
	$nonavbar=1;
	ExportExtraFields();
	exit;
}


include("header.inc.php");
print "</td></tr></table>";
AdminTabs($_REQUEST['tib']);
print "<table>";

if ($_REQUEST['UpdateCacheTables']) {
	UpdateCacheTables();
	print "<tr><td>Cache tables updated</td></tr></table>";
	EndHTML();
	exit;
}


if ($_REQUEST['eval']) {
	MustBeAdmin();
	qlog("Eval section was used by this user");
	print "<table><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td> Inject PHP code<br>";
	print "<form name='hidde'><input type='hidden' name='eval' value='1'><textarea name='do_eval' rows=10 cols=80>" . $_REQUEST['do_eval'] . " </textarea><br><input type='submit' value='Execute code'></form>";

	if ($_REQUEST['do_eval']) {
		qlog("Executing code: " . $_REQUEST['do_eval']);
		print "<pre>";
		$a = eval($_REQUEST['do_eval']);
		print $a;
		print "</pre>";
	} 
	
	print "</td></tr></table>";
	EndHTML();
	exit;
}
?>
 	<SCRIPT LANGUAGE="javascript" SRC="cookies.js"></SCRIPT>
<?
// Use this (undocumented) function to always create an entity for each customer which doesn't have one already
if (($_REQUEST['generateentities'] == "yes")) {
	MustBeAdmin();
	$a = CreateEntities();
	MainAdminTabs("datman");
	print "<table><tr><td>&nbsp;&nbsp;&nbsp;<font color='#FF0000'><b>For every customer an entity was created. ($a entities)</b></font></td></tr></table>";
	EndHTML();
	exit;
} elseif ($_REQUEST['ViewRelTree'] == 1) {
	MainAdminTabs("datman");
	print "<table><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><b>Root entity relationship tree</td></tr>";
	print "<tr><td></td><td>";
	PrintSisters("root","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
	print "</td></tr></table>";
	exit;
} elseif ($_REQUEST['generateentities'] == "almost") {
	MustBeAdmin();
	MainAdminTabs("datman");
	print "<table><tr><td>&nbsp;&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Auto-create entities </font></legend>This is a very dangerous function. It could fill your database with things you don't want; <b>be careful!</b>&nbsp;&nbsp;&nbsp;";
	print "<br><br>This function will create an entity for every customer (" . $lang['customer'] . ") which <u>doesn't have an entity yet</u><br>";
	print "<br>Please select the default values:<br><br>";
	print "<form name='ce' method='POST'>";
	print "<table>";

	print "<tr><td>Owner:</td><td><select name='ac_owner'>";
	$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]loginusers WHERE LEFT(FULLNAME,3) <> '@@@' AND active<>'no' ORDER BY FULLNAME";
	$result= mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		print "<option value='" . $row['id'] . "'>" . $row['FULLNAME'] . "</option>";		
	}
	print "</select></td></tr>";

	print "<tr><td>Assignee:</td><td><select name='ac_assignee'>";
	$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]loginusers WHERE LEFT(FULLNAME,3) <> '@@@' AND active<>'no' ORDER BY FULLNAME";
	$result= mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		print "<option value='" . $row['id'] . "'>" . $row['FULLNAME'] . "</option>";		
	}
	print "</select></td></tr>";

	print "<tr><td>" . $lang['status'] . ":</td><td>";
	print "<select name='ac_status'  style='width:250' size='1'>";
	$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]statusvars ORDER BY varname";
	$result= mcq($sql,$db);
	while($options = mysql_fetch_array($result)) {
		if (strtoupper(($ea[status]))==strtoupper($options[varname])) { $a="SELECTED"; } else { $a=""; }
		print "<option style='background:" . $options[color] . "' value='$options[varname]' $a>$options[varname]</option>";
	}

	print "</select></td></tr>";
	// which priority
	print "<tr><td>" . $lang['priority'] . ":</td><td>";
	print "<select name='ac_priority'  style='width:250' size='1'>";
	$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]priorityvars ORDER BY varname";
	$result= mcq($sql,$db);
	while($options = mysql_fetch_array($result)) {
		if (strtoupper(($ea[status]))==strtoupper($options[varname])) { $a="SELECTED"; } else { $a=""; }
		print "<option style='background:" . $options[color] . "' value='$options[varname]' $a>$options[varname]</option>";
	}

	print "</select></td></tr>";
	print "<tr><td>Category:</td><td><input type='text' size=70 name='ac_category' value='Auto-generated entity'></td></tr>";
	print "<tr><td><input type='hidden' name='generateentities' value='yes'><input type='submit'></td></tr>";
	print "</table></form>";
	
	print "</fieldset></td></tr></table>";
	EndHTML();
	exit;
}

if ($_REQUEST['ExpireAllFormCache']) {
	ExpireFormCache("%");
	qlog("All form cache dropped on request");
	log_msg("All form cache dropped on request","");
    print "All form cache dropped on request<br>";
}

if ($_REQUEST['MassMigrateForms']) {
	MustBeAdmin();
	MainAdminTabs("datman");
	?>
			<table width='50%'><tr><td>&nbsp;</td><td>
			<fieldset><legend>mass-migrate entity forms</legend>
			<form name='migrateform' method='post'>
			
	<?

	if ($_REQUEST['migrate_from'] && $_REQUEST['migrate_to']) {
			
			if (is_numeric($_REQUEST['migrate_from']) && is_numeric($_REQUEST['migrate_to'])) {
			
				mcq("UPDATE " . $GLOBALS['TBL_PREFIX'] . "entity SET formid='" . $_REQUEST['migrate_to'] . "' WHERE formid='" . $_REQUEST['migrate_from'] . "'", $db);
				$t = mysql_affected_rows();
				print $t . " entities updated!";
				qlog("Updated " . $t . " entities from form " . $_REQUEST['migrate_from'] . " to form " . $_REQUEST['migrate_to']);
				log_msg("Updated " . $t . " entities from form " . $_REQUEST['migrate_from'] . " to form " . $_REQUEST['migrate_to'],"");
				ExpireFormCacheByForm($_REQUEST['migrate_from']);

			}

	} else {
			print "<br />With this function you can alter the form of a set of entities.<br /><br /><table width='100%'><tr><td>Migrate all entities with form</td><td><select name='migrate_from'>";
			$sql = "SELECT fileid, filename, file_subject FROM " . $GLOBALS['TBL_PREFIX'] . "binfiles WHERE filetype='TEMPLATE_HTML_FORM'";
			$result = mcq($sql, $db);
			while ($row = mysql_fetch_array($result)) {
				$x++;
				print "<option " . $ins . " value='" . $row['fileid'] . "'>" . $row['filename'] . " (" . $row['file_subject'] . ")</option>";
			}
			print "</select></td></tr>";
			print "<tr><td>to form</td><td><select name='migrate_to'>";
			$sql = "SELECT fileid, filename, file_subject FROM " . $GLOBALS['TBL_PREFIX'] . "binfiles WHERE filetype='TEMPLATE_HTML_FORM'";
			$result = mcq($sql, $db);
			while ($row = mysql_fetch_array($result)) {
				$x++;
				print "<option " . $ins . " value='" . $row['fileid'] . "'>" . $row['filename'] . " (" . $row['file_subject'] . ")</option>";
			}
			print "</select></td></tr><tr><td><input type='submit' value='Go!'></td></tr></table>";
	}
	?>
			</form>
			</fieldset>
			</td></tr></table>
		<?

	EndHTML();
	exit;
}

if ($_REQUEST['SendAdHocEmail']) {
		Mainadmintabs("actions");
		print "<table width='70%'><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";
		print "Send e-mail to all CRM-CTT users<br>";

		if ($_REQUEST['data'] && $_REQUEST['subject']) {
	//		$_REQUEST['data'] .= "<BR>@LIST@";
			$force = 2;
			print "<pre>";
			SendPersonificatedDailyOverviewMail($_REQUEST['data'],$_REQUEST['subject']);
			print "\nDone.";
			print "</pre>";
		} else {
			print "<form name='SendAdHoc'>";
			print "<br><br><b>Subject</b>: <input type='text' name='subject' size='80'><input type='hidden' name='SendAdHocEmail' value='1'><br><br>";
			include("fckeditor/fckeditor.php");

			$oFCKeditor = new FCKeditor('data') ;
			$oFCKeditor->BasePath	= "fckeditor/" ;
			$oFCKeditor->Width = "100%";
			$oFCKeditor->Height = "600";
			$oFCKeditor->ToolbarSet = 'CRM';

			$oFCKeditor->Value		= stripslashes("<!-- Feel free to edit the source, but don't add javascript or CSS code. -->\n<table border='0'><tr><td>Type your message here. The entity list will be placed under your text and will only contain entities assigned to the recipient.</td></tr></table>\n<br><hr>@LIST@");
			$oFCKeditor->Create() ;

			print "<input type='submit' value='Send it!'></form>";
			print "</td></tr></table>";
		} 
		EndHTML();
		exit();
}

if ($_REQUEST['docbox']) {
		print "<tr><td>&nbsp;&nbsp;&nbsp;</td><td colspan=2>";
		print "<table border=0 width=100%><tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Available documentation in this installation&nbsp;</legend><table width=100% cellspacing=10>";
		print "<tr><td>Short-key overview</td><td><img src='arrow.gif'>&nbsp;<a href='index.php?shortkeys=1&1111707918' class='bigsort'>Short keys</a></td></tr>";

		if (file_exists("CRM-CTT_Adminmanual.pdf")) {
			print "<tr><td>Administration manual</td><td><img src='arrow.gif'>&nbsp;<a href='CRM-CTT_Adminmanual.pdf' target='new' class='bigsort'>CRM-CTT Administration manual</a></td></tr>";
			$av++;
		}
		if (file_exists("CRM-CTT_Non-technical_management_and_configuration_essentials.pdf")) {
			print "<tr><td>Non-technical management and configuration essentials</td><td><img src='arrow.gif'>&nbsp;<a href='CRM-CTT_Non-technical_management_and_configuration_essentials.pdf' target='new' class='bigsort'>CRM-CTT_Non-technical_management_and_configuration_essentials.pdf</a></td></tr>";
			$av++;
		}
		
		if (strtoupper($ShowPDWASLink)=="YES") {  
				if (file_exists("CRM_PDWASmanual.pdf")) {
						//print "<TR><TD>PDWAS Manual</TD><TD><img src='arrow.gif'>&nbsp;<a target='_new' class='bigsort' title='' href='CRM_PDWASmanual.pdf'>CRM_PDWASmanual.pdf</a></TD></TR>";
				}

		}
		if (file_exists("README")) {
			print "<tr><td>Readme</td><td><img src='arrow.gif'>&nbsp;<a href='README' class='bigsort' target='new'>README</a></td></tr>";
			$av++;
		}
		if (file_exists("CHANGELOG")) {
			print "<tr><td>Changelog</td><td><img src='arrow.gif'>&nbsp;<a href='CHANGELOG' class='bigsort' target='new'>CHANGELOG</a></td></tr>";
			$av++;
		}
		if (file_exists("UPGRADING")) {
			print "<tr><td>Upgrade issues</td><td><img src='arrow.gif'>&nbsp;<a href='UPGRADING' target='new' class='bigsort'>UPGRADING</a></td></tr>";
			$av++;
		}
		if (!$av) {
			print "<tr><td colspan=2>No documents found in your installation directory.</td></tr>";
		}
		print "<tr><td>List of valid template tags</td><td><img src='arrow.gif'> <a href='javascript:poplittlewindowWithBars(\"admin.php?dpstags=1&nonavbar=1\");'>List of tags</a></td></tr>";
		print "</table></fieldset><br><br>";
		EndHTML();
		exit;
} elseif ($_REQUEST['info']) {
	MustBeAdmin();
	MainAdminTabs('info');
	print "<table>";

//	print "<tr><td></td><td>Administrative section sitemap:<br>";
//	print "</td></tr>";
	

	$sqlcounter++;
	$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]customer";
	$result= mcq($sql,$db);
	$maxU1 = mysql_fetch_array($result);
	$maxC = $maxU1[0];

	$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]loginusers";
	$result= mcq($sql,$db);
	$maxU1 = mysql_fetch_array($result);
	$maxU = $maxU1[0];

	$sqlcounter++;
	$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity";
	$result= mcq($sql,$db);
	$maxU1 = mysql_fetch_array($result);
	$maxo = $maxU1[0];
	//print "<tr><td></td><td><img src='crm.png'></td></tr>";	
	print "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>You are looking at $CRM_VERSION.<br><br> Visit the CRM project page at <img src='arrow.gif'>&nbsp;<a href='http://www.crm-ctt.com' target='mnew' class='bigsort'>http://www.crm-ctt.com</a> for new releases and the latest documentation. Help is also available, as is the CRM forum.";

	print "<br><br><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<img src='info.gif'> Need support?</legend>When submitting a support request, <u>always</u> include the information in this box in your initial post:";
	print "<BR><BR>";
	print "<form name='bogusform'><textarea name='infota' rows=10 cols=80 class='plain'>";
	print "--------------- Messages ----------------\n\n";
	print CheckDatabaseSettings();

	print "----- CRM-CTT Database information ------\n\n";
	print "Software version:           " . $GLOBALS['VERSION'] . "\n";
	print "Database version:           " . $GLOBALS['DBVERSION'] . "\n";
	print "-----------------------------------------\n";
	print "Number of entities:         " . $maxo . "\n";
	print "Number of users:            " . $maxU . "\n";
	print "Number of customers:        " . $maxC . "\n";
	$list = GetExtraFields();
	$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]customaddons WHERE type<>'cust'";
	$result = mcq($sql, $db);
	$row = mysql_fetch_array($result);
	print "Number of EEF's:            " . sizeof($list) . " (" . number_format($row[0]) . " rows)\n";
	$list = GetExtraCustomerFields();
	$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]customaddons WHERE type='cust'";
	$result = mcq($sql, $db);
	$row = mysql_fetch_array($result);

	print "Number of ECF's:            " . sizeof($list) . " (" . number_format($row[0]) . " rows)\n";
	print "Authentication method:      " . $GLOBALS['AUTH_TYPE']. "\n";
	print "-----------------------------------------\n";
	$num = CountTotalNumOfRecords($GLOBALS['TBL_PREFIX']);

	print "Number of database records: " . number_format($num) . "\n";


	$sql = "SHOW TABLE STATUS";
	$result= @mcq($sql,$db);
	while ($stat = @mysql_fetch_array($result))
	{
		$size += $stat["Data_length"];
		$size += $stat["Index_lenght"];
	}							
	
	$tot_size += (($size/1024)/1024);
	$size = ceil((($size/1024)/1024)) . " MB";
	print "-----------------------------------------\n";
	print "Database size:              " . $size . "\n";
	


	print "\n-------- Environment information --------\n\n";
	print "Server id     : " . $_SERVER['SERVER_SOFTWARE'] . "\n";
	print "PHP Version   : " . phpversion() . "\n";
	$a = (get_loaded_extensions());

	if (!in_array("mysql",$a)) {
			print "MySQL Version : no MySQL support detected\n";
			$fatal = 1;
	} else {
			print "MySQL Version : ";
				$res = mcq("SHOW VARIABLES LIKE 'version'",$db);
				while ($resrow = mysql_fetch_array($res)) {
					print "" . $resrow[1] . "\n";
				}

				}

	if (in_array("gd",$a)) {
			print "GD Library    : Yes\n";
		} else {
			print "GD Library    : No\n";
		}
	if (ini_get('register_globals')=="1" || strtolower(ini_get('register_globals'))=="on" || strtolower(ini_get('register_globals'))=="yes") {

			print "Reg_Globals   : On\n";
	} else {
			print "Reg_Globals   : Off\n";
			//$fatal = 1;
	}

	if (ini_get("magic_quotes_gpc") == 1) {
			print "Magic quotes  : On\n";
	} else {
			print "Magic quotes  : Off (!!)\n";
	}
	
	print "Max exec time : " . ini_get("max_execution_time") . " sec\n";
	print "Mem limit     : " . ini_get("memory_limit") . "\n";
	print "Max POST      : " . ini_get("post_max_size") . "\n";
	print "Max file upl. : " . ini_get("upload_max_filesize") . "\n";

	

	
	
	$dir = ini_get("session.save_path");

	if ($f = tempnam($dir,"BLA")) {
			print "Tmp dir       : " . $dir . "\n";
			unlink($f);
	} else {
			print "Tmp dir       : " . $dir . " -> it's not accessable!\n";
	}
	$sql = "SELECT setting,value FROM " . $GLOBALS['TBL_PREFIX'] . "settings WHERE setting NOT LIKE '%password%' AND setting NOT LIKE '%body%' AND setting NOT LIKE '%ToShow%' AND setting NOT LIKE '%subject%' AND setting<>'REQUIREDDEFAULTFIELDS' AND setting<>'PersonalTabs' AND setting<>'EMAILINBOX' AND setting<>'RSS_FEEDS' AND setting<>'MAILPASS' ORDER BY setting";
	$res = mcq($sql, $db);
	print "\n----------- Setting Information ---------\n";
	print "Global Settings dump (excluding passwords & HTML-forms)\n\n";
	while ($row = mysql_fetch_array($res)) {
		print fillout(strtoupper($row['setting']),40) . "\t" . $row['value'] . "\n";
	}

	print "</textarea><br><input type='button' name='copybutton' value='Copy to clipboard' onclick=\"CopyToClipboard(document.bogusform.infota.value);\"'></form></fieldset></td></tr>";
	
	print "<tr><td></td><td><br><br><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<img src='info.gif'> Messages at crm-ctt.com</legend>This is a frame, in which the messages page at crm-ctt.com is shown. It will only appear if your PC currently has an internet connection.<BR><BR>";
	print "<iframe height=135 width=660 src='http://www.crm-ctt.com/message.php?version=" . $GLOBALS['VERSION'] . "'></iframe>";
	print "</fieldset></td></tr>";
	print "<tr><td></td><td><BR><BR>With special thanks to:<BR><BR><img title='This image is hosted on the project site' alt='sf.net image' src='https://sourceforge.net/sflogo.php?group_id=61096&type=1'></td></tr>";	

	print "</table>";
	if (!stristr($GLOBALS['VERSION'],$GLOBALS['DBVERSION'])) {

			print "<br><br><img src='error.gif'> Error&nbsp;<br><table width=100%>";
			//print "<tr><td>ERROR - your database is out-of-date!</td></tr>";
			print "<tr><td>Your database version is " . $GLOBALS['DBVERSION'] . " while the CRM-CTT version you're running is version " . $GLOBALS['VERSION'] . ". This can compromise your data integrity.</td></tr>";
			//print "<tr><td>&nbsp;</td></tr>";

			if ($GLOBALS['DBVERSION'] > $GLOBALS['VERSION']) {
				print "<tr><td>Please go to <img src='arrow.gif'>&nbsp;<a href='http://www.crm-ctt.com' target='new' class='bigsort'>http://www.crm-ctt.com</a> to download version " . $GLOBALS['DBVERSION'] . " of CRM-CTT.</td></tr>";
			} else {
				print "<tr><td>Please use the <img src='arrow.gif'>&nbsp;<a href='upgrade.php' class='bigsort'>database upgrade procedure</a> to upgrade your database to version " . $GLOBALS['VERSION'] . "</td></tr>";
			}


		}	
	EndHTML();
	exit;
} elseif ($_REQUEST['webdavstat']) {
	if (strtoupper($EnableWebDAVSubsystem)=="YES") { 
			print "<tr><td>&nbsp;&nbsp;&nbsp;</td><td colspan=2>";
			print "<table width=100%><tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<img src='webdav.gif'>&nbsp;WebDAV Subsystem status&nbsp;</legend><table width=100%>";
			print "<tr><td>WebDAV Subystem is enabled</td></tr>";

			if (!file_exists("webdav_fs/.htaccess")) {
					print "<tr><td><img src='error.gif'>&nbsp; WARNING - .htaccess file in webdav_fs/ directory not found. This is a severe security risk.</td></tr>";
			}

			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]binfiles WHERE checked='out'";
			$ret = mcq($sql,$db);
			$row = mysql_fetch_array($ret);
			
			if ($row[0]=="") {
				$row[0] = "0";
			}
			
			$out = $row[0];

			if ($listfiles) {
				print "<tr><td>There is/are " . $row[0] . " files checked out of the database <img src='arrow.gif'>&nbsp;<a href='admin.php?listfiles=0&password=$password&listlocks=$listlocks&webdavstat=1' class='bigsort'>Hide files</a></td></tr>";
			} else {
				print "<tr><td>There is/are " . $row[0] . " files checked out of the database <img src='arrow.gif'>&nbsp;<a href='admin.php?listfiles=1&password=$password&listlocks=$listlocks&webdavstat=1' class='bigsort'>List files</a></td></tr>";
			}

			if ($listfiles) {

				$sql = "SELECT checked,checked_out_by,fileid,username,filename,koppelid FROM $GLOBALS[TBL_PREFIX]binfiles WHERE checked='out'";
				$outp = mcq($sql,$db);


				if ($out>0) {
					print "<tr><td><table border=1 cellpadding='4'>";
					print "<tr><td><b>Filename</b></td><td><b>eid</b></td><td><b>File owner</b></td><td><b>Checked out by</b></td></tr>";
					while ($row=mysql_fetch_array($outp)) {
						print "<tr><td>$row[filename]</td><td><a href='edit.php?e=$row[koppelid]' class='bigsort'>$row[koppelid]</a></td><td>$row[username]</td><td>" . GetUserName($row[checked_out_by]) . "</td></tr>";
					}
					print "</table></td></tr>";
				} else {
					print "<tr><td><b>No files</b></td></tr>";
				}
			}


			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]webdav_locks";
			$ret = mcq($sql,$db);
			$row = mysql_fetch_array($ret);

			if ($listlocks) {
				print "<tr><td>There is/are " . $row[0] . " files are locked for editing <img src='arrow.gif'>&nbsp;<a href='admin.php?listlocks=0&password=$password&listfiles=$listfiles&webdavstat=1' class='bigsort'>Hide locks</a></td></tr>";
			} else {
				print "<tr><td>There is/are " . $row[0] . " files are locked for editing <img src='arrow.gif'>&nbsp;<a href='admin.php?listlocks=1&password=$password&listfiles=$listfiles&webdavstat=1' class='bigsort'>List locks</a></td></tr>";
			}
			


			$locks = $row[0];
			

			if ($row[0]>$out) {
				print "<tr><td><img src='error.gif'>&nbsp; WARNING - There are more locks than checked out files</td></tr>";
			}
			if ($listlocks) {

				if ($deletelock) {
					$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]webdav_locks WHERE token='" . $deletelock . "'";
					mcq($sql,$db);
				}

				$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]webdav_locks";
				$outp = mcq($sql,$db);

				if ($locks>0) {
					print "<tr><td><table border=1 cellpadding='4'>";
					print "<tr><td><b>Filename</b></td><td><b>Lock owner</b></td><td><b>Delete</b></td></tr>";
					while ($row=mysql_fetch_array($outp)) {
						print "<tr><td>$row[1]</td><td>$row[3]</td><td><img src='arrow.gif'>&nbsp;<a href='admin.php?listlocks=1&password=$password&deletelock=$row[0]&webdavstat=1' class='bigsort'>Delete this lock</a></td></tr>";
					}
					print "</table></td></tr>";
				} else {
					print "<tr><td><b>No locks</b></td></tr>";
				}
			}

			if ($_SERVER['HTTPS']=="on") {
				$http = "https://";
			} else {
				$http = "http://";
			}

			$ext_path_to_webdav_fs = $http . $_SERVER['SERVER_NAME'] . "/webdav_fs/";

			$subdir = ereg_replace("admin.php","webdav_fs/",$_SERVER['SCRIPT_NAME']) . ".htaccess";

			$uri = $_SERVER['SERVER_NAME'] . $subdir;

			$fp = fsockopen($_SERVER['SERVER_NAME'], 80, $errno, $errstr, 30);
			if (!$fp) {
			   echo "$errstr ($errno)<br />\n";
			} else {
			   $out = "GET $subdir HTTP/1.1\r\n";
			   $out .= "Host: " . $_SERVER['SERVER_NAME'] . "\r\n";
			   $out .= "Connection: Close\r\n\r\n";

			   fputs($fp, $out);
			   while (!feof($fp)) {
				   $ret .= fgets($fp, 128);
			   }
			   fclose($fp);
			}
			
			if (!stristr($ret,"403 Forbidden")) {
				print "<tr><td><img src='error.gif'>&nbsp; WARNING - The directory $subdir LOOKS WORLD-READABLE</td></tr>";
				print "<tr><td>This check is done by creating a socket connection to " . $uri . " which must return a 403 Forbidden. In this case, the 403 Forbidden header was not received.</td></tr>";

			
				print "<tr><td>According to my own investigation, your webdav directory is wide open.</td></tr>";
				
				//log_msg("WebDAV Subsystem forced to shutdown due to security-related error","");

				//$sql = "UPDATE $GLOBALS[TBL_PREFIX]settings SET value='no - AUTOMATED SHUTDOWN - webdav_fs dir was world-readable' WHERE setting='EnableWebDAVSubsystem'";
				//mcq($sql,$db);
			} else {
				print "<tr><td>Access to $http" . $uri . " was checked using a socket connection, received a 403 Forbidden which is OK</td></tr>";
			}

			print "</table></fieldset></td></tr></table></td></tr></table>";
		} else {
			print "<table><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='error.gif'> WebDAV Subsystem is disabled.</td></tr></table>";
		}
	EndHTML();
	exit;
}

/*
if ($_REQUEST['disableonscreen']) {
	// disable on-screen logging mode
} elseif ($_REQUEST['disableonscreen']) {
	// enable on-screen logging mode
}
*/

if ($_REQUEST['ImportUsers']) {
	//MainAdminTabs("ieb");
	ImportUsers();
	EndHTML();
	exit;
} elseif ($_REQUEST['ImportSettings']) {
	MainAdminTabs("ieb");
	ImportSettings();
	EndHTML();
	exit;
} elseif ($_REQUEST['ImportExtraFields']) {
	MainAdminTabs("ieb");
	ImportExtraFields();
	EndHTML();
	exit;
} elseif ($_REQUEST['ImportCSVUsers']) {
	ImportCSVUsers();
	EndHTML();
	exit;
}
if (!$nonavbar && !$_REQUEST['WhatVar']) {
	//print "</table><table border=0 width='80%'><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size=+1>$lang[adm] " . $title . "</font></legend><table border=0 width='100%'>";
	//print "</table><table border=0 width='80%'><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<table border=0 width='100%'>";
} elseif (!$nonavbar && $_REQUEST['WhatVar']) {
	print "</table><table border=0 width='80%'><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<table border=0 width='100%'>";
}


if ($_REQUEST['log']) {
	MustBeAdmin();
	MainAdminTabs("a");
	print "<table><tr><td>&nbsp;&nbsp;</td><td><table>";
	LogFunction();
	print "</table></td></tr></table>";
	EndHTML;
	exit;
}
if ($deleteclosed && !$deleteclosed1) {
			MainAdminTabs("datman");
			print "<tr><td><table><tr><td colspan=12><table border=0><tr><td colspan=2>Delete all entities with status: <form name='bla' method='post'><select name='DeleteAllWithStatus' size='1' $roins>";
			$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]statusvars ORDER BY varname";
			$result= mcq($sql,$db);
			while($options = mysql_fetch_array($result)) {
				if (strtoupper(($ea[status]))==strtoupper($options[varname])) { $a="SELECTED"; } else { $a=""; }
				print "<option value='$options[varname]' $a>$options[varname]</option>";
			}

			print "</select>&nbsp;<input type='hidden' name='deleteclosed1' value='1'><input type='hidden' name='deleteclosed' value='1'><input type='submit' value='Go!'></td></tr></table></td></tr>";
			EndHTML();
			exit;
}

if ($deleteclosed1 && $DeleteAllWithStatus) {
		MainAdminTabs("datman");
		print "<table>";
		$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE status='$DeleteAllWithStatus' AND deleted<>'y'";
		$result= mcq($sql,$db);
		$maxU1 = mysql_fetch_array($result);
		$maxo = $maxU1[0];
		print "<tr><td colspan=12>Setting $maxo entities with status <b>$DeleteAllWithStatus</b> to (logically) <b>deleted</b> status. Please confirm by clicking the button below.<br>";
		print "<form name='confirm' method='GET'>";
		print "<input type='hidden' name='DeleteAllWithStatus' value='$DeleteAllWithStatus'><input type='hidden' name='fdconfirmed' value='1'><input type='hidden' name='password' value='$password'>";
		if ($maxo>0) {
			print "</td></tr><tr><td><br><input type='submit' name='knopje' value='Confirm deletion'></td></tr></table>";
		} else {
			print "</td></tr><tr><td><br><b>Nothing to do!</b></td></tr></table>";
		}
		print "</form>";
		EndHTML();
		exit;

}
if ($fdconfirmed) {
		$epoch = date('U');
		$sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET deleted='y',closeepoch='" . $epoch . "' WHERE status='" . $DeleteAllWithStatus . "' AND deleted<>'y'";

		mcq($sql,$db);
		//print $sql;
		print "<tr><td>All entities with status '$DeleteAllWithStatus' were moved to deleted-list</td></tr>";
		log_msg("All entities with status $DeleteAllWithStatus were deleted","");		
}
if ($SendEntityList) {
	MustBeAdmin();
	if (!$_REQUEST['template']) { 
		print "<table><tr><td><form name='SingleReport' method='POST'>";
		print "<table>";
		print "<tr><td><b>E-mail to users</b><br><br></td></tr>";
		print "<tr><td>HTML Template:</td><td><select style='width:250' name='template'>";
		$sql = "SELECT fileid,filename,creation_date,username FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='0' AND filetype='TEMPLATE_HTML'";
		$result = mcq($sql,$db);
		while ($row = mysql_fetch_array($result)) {
			if ($_REQUEST['template']==$row['fileid']) {
				$ins = "SELECTED";
			} else {
				unset($ins);
			}
			print "<option $ins value = '" . $row['fileid'] ."'>" . $row['filename'] . "</option>";
		}
		print "</select><input type='hidden' name='SendEntityList' value='1'><input type='submit' name='Go'></form></td></tr></table></td></tr></table>";
		EndHTML();
		exit;
	} elseif (!$_REQUEST['data']) {
		$sql = "SELECT content,file_subject FROM $GLOBALS[TBL_PREFIX]binfiles,$GLOBALS[TBL_PREFIX]blobs WHERE $GLOBALS[TBL_PREFIX]binfiles.fileid=$GLOBALS[TBL_PREFIX]blobs.fileid AND fileid='" . $_REQUEST['template']  . "' AND LEFT(filetype,8)='TEMPLATE'";
		
		$result = mcq($sql,$db);
		$row = mysql_fetch_array($result);
		print "<table><tr><td colspan=3>";
		print "<b> WARNING - only generic template tags work when e-mailing users. Use the @LIST@ tag for the list of entities assigned to the user.</b>";
		print "</td></tr><tr><td>";
		print "<b>To: all CRM-CTT users</b><br> ";
		print "<form name='editHTMLtemplateform' METHOD='POST'>";
		print "<input type='hidden' name='SendEntityList' value='1'>";
		print "<input type='hidden' name='template' value='unimportant'>";
		print "<b> Subject: </b><input type='text' size=70 name='subject' value='" . $row['file_subject'] . "'><br><br>";
		?>
				<script language="Javascript1.2"><!-- // load htmlarea
				_editor_url = "";                     // URL to htmlarea files
				var win_ie_ver = parseFloat(navigator.appVersion.split("MSIE")[1]);
				if (navigator.userAgent.indexOf('Mac')        >= 0) { win_ie_ver = 0; }
				if (navigator.userAgent.indexOf('Windows CE') >= 0) { win_ie_ver = 0; }
				if (navigator.userAgent.indexOf('Opera')      >= 0) { win_ie_ver = 0; }
				if (win_ie_ver >= 5.5) {
				  document.write('<scr' + 'ipt src="' +_editor_url+ 'editor.js"');
				  document.write(' language="Javascript1.2"></scr' + 'ipt>');  
				} else { document.write('<scr'+'ipt>function() { return false; }</scr'+'ipt>'); }
				// --></script>
				<?
				print "<TEXTAREA name='data' ROWS='18' COLS='100'>" . stripslashes($row['content']) . "</TEXTAREA>";
				?>
				<script language="javascript1.2">
					editor_generate('data');
				</script>
			<?
		print "<br><input type='submit' value='$lang[go]'>";
		print "</form>";
		print "</table>";
		EndHTML();
		exit;
	} else {
		$force = 1;
		SendPersonificatedDailyOverviewMail($_REQUEST['data'],$_REQUEST['subject']);
	}
}
if ($_REQUEST['dpstags']) {
	print "<table><tr><td>";
	AvailableTags();
	print "</td></tr></table>";
	EndHTML();
	exit;
}
if ($_REQUEST['templates']) {
	MustBeAdmin();

	if (!$_REQUEST['t1'] && !$_REQUEST['editHTMLtemplate']) {
		$_REQUEST['t1'] = "add";
	    $_REQUEST['nav'] = "add";
	}

//	AdminTabs("templates_detail");
	$to_tabs = array("add","html","htmlreports","htmlforms","rtfreports","rtfinvoices","all","taglist");
	$tabbs["add"] =         array("admin.php?templates=1&nav=add&t1=add"           => "New template");
	$tabbs["html"] =        array("admin.php?templates=1&nav=html&t1=html"         => "Plain HTML templates");
	$tabbs["htmlforms"] =   array("admin.php?templates=1&nav=htmlforms&t1=htmlf"   => "Entity & customer forms");
	$tabbs["htmlreports"] = array("admin.php?templates=1&nav=htmlreports&t1=htmlr" => "HTML Summary page reports");
	$tabbs["rtfreports"] =  array("admin.php?templates=1&nav=rtfreports&t1=rtfr"   => "RTF Reports & mailmerge");
	$tabbs["rtfinvoices"] = array("admin.php?templates=1&nav=rtfinvoices&t1=rtfi"  => "RTF Invoices");
	$tabbs["all"] =         array("admin.php?templates=1&nav=all&t1=all"           => "All");
	$tabbs["taglist"] =     array("admin.php?templates=1&nav=taglist&t1=taglist"   => "List of available tags");

	InterTabs($to_tabs, $tabbs, $_REQUEST['nav']);

	print "</table><table width=95%<tr><td>&nbsp;&nbsp;</td><td>";


	if ($_REQUEST['nav'] == "taglist") {
			AvailableTags();
			AvailableCustomerFormTags();
			AvailableFormTags();
			print "</td></tr></table>";
			EndHTML();
			exit;
	}


	if (!$_FILES['userfile']['tmp_name'] =="" && !$_FILES['userfile']['name']=="" && !$_FILES['userfile']['size']=="" && !$_FILES['userfile']['type']=="") {
			
			//  A file was attached

				
			// Read contents of uploaded file into variable
				
				$fp=fopen($_FILES['userfile']['tmp_name'] ,"rb");
				$filecontent=fread($fp,filesize($_FILES['userfile']['tmp_name'] ));
				fclose($fp);
							
				$attachment = AttachFile(0,$_FILES['userfile']['name'],$filecontent,"entity",$_REQUEST['filetype']);

				unset($filecontent);
				unset($_FILES['userfile']['tmp_name'] );
				unset($_FILES['userfile']['name']);
				unset($_FILES['userfile']['size']);
				unset($_FILES['userfile']['type']);
				print "Template added";

	}
	if ($_REQUEST['new_HTML_template']) {
		qlog("Empty HTML tempate created");
		$attachment = AttachFile(0,$_REQUEST['new_HTML_template'],"[empty]","entity",$_REQUEST['filetype']);
		print "<font color='#FF0000'>Template created!</font>";
	}
	if ($_REQUEST['deletetemplate']) {

		if ($GLOBALS['DefaultForm'] == $_REQUEST['deletetemplate']) {
			log_msg("WARNING! Somebody tried to delete the default template, this is dangerous!");
			qlog("WARNING! Somebody tried to delete the default template, this is dangerous!");
			PrintAD("No go. This incident has been logged.");
			EndHTML();
			exit;
		}

		// First, check if this (possible) form isn't used by any entity (FormFinity)
		$row = db_GetRow("SELECT formid FROM " . $GLOBALS['TBL_PREFIX'] . "entity WHERE formid='" . $_REQUEST['deletetemplate'] . "'");

		if ($row['formid']) {
			print " <img src='error.gif'> Form " . $_REQUEST['deletetemplate'] . " is in use by one or more entities. You cannot delete it!";
		} else {
			$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]binfiles WHERE fileid='" . $_REQUEST['deletetemplate'] . "' AND LEFT(filetype,8)='TEMPLATE'";
			mcq($sql,$db);
			$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]blobs WHERE fileid='$deletefile[$c]'";
			mcq($sql,$db);

		}
	}
	if ($_REQUEST['editHTMLtemplate']) {
		if ($_REQUEST['saveHTMLtemplate'] && $_REQUEST['data'] && $_REQUEST['subject']) {
			$sql = "UPDATE $GLOBALS[TBL_PREFIX]binfiles SET file_subject='" . $_REQUEST['subject'] . "' WHERE fileid='" . $_REQUEST['saveHTMLtemplate'] . "'	AND LEFT(filetype,8)='TEMPLATE'";
			mcq($sql,$db);
			$sql = "UPDATE $GLOBALS[TBL_PREFIX]blobs SET content='" . ($_REQUEST['data']) . "' WHERE fileid='" . $_REQUEST['saveHTMLtemplate'] . "'";
			mcq($sql,$db);
			ExpireFormCacheByForm($_REQUEST['saveHTMLtemplate']);
			//print $sql;
		}
		$sql = "SELECT content,filetype,file_subject FROM $GLOBALS[TBL_PREFIX]binfiles,$GLOBALS[TBL_PREFIX]blobs WHERE $GLOBALS[TBL_PREFIX]binfiles.fileid=$GLOBALS[TBL_PREFIX]blobs.fileid AND $GLOBALS[TBL_PREFIX]blobs.fileid='" . $_REQUEST['editHTMLtemplate']  . "' AND LEFT(filetype,8)='TEMPLATE' AND koppelid=0";
		//print $sql;
		$result = mcq($sql,$db);
		$row = mysql_fetch_array($result);

		$rnd_e = db_GetRow("SELECT eid FROM " . $GLOBALS['TBL_PREFIX'] . "entity");
		$rnd_entity = $rnd_e[0];

		$rnd_c = db_GetRow("SELECT id FROM " . $GLOBALS['TBL_PREFIX'] . "customer");
		$rnd_customer = $rnd_e[0];


		print "Editing HTML template #" . $_REQUEST['editHTMLtemplate'] . "<br><br>";
		if ($row['filetype'] == "TEMPLATE_HTML_FORM") {
			print "<b>Remember; tags #CUSTOMER#, #OWNER# and #ASSIGNEE# <i>must</i> exist in your template!</b><br><br>";
			$formedit = true;
			print "<table border=1 class='crm'><tr><td>Compile test: (save template to test again)</td></tr>";
			print "<tr><td><pre>" . ValidateHTMLForm($_REQUEST['editHTMLtemplate'],"entity");
			print "</pre><input name='ex' value='Parse an example form (entity " . $rnd_entity . ")' type='button' OnClick=\"window.open('ex_html_template.php?x=" . $rnd_entity . "&form=1&data=" . $_REQUEST['editHTMLtemplate'] . "', 'logwin','width=500,height=400,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');\"></td></tr></table><br>";
		} elseif ($row['filetype'] == "TEMPLATE_HTML_CFORM") {
			print "<b>Remember; tag #CUSTOMER# <i>must</i> exist in your template!</b><br><br>";
			$formedit = true;
			print "<table border=1 class='crm'><tr><td>Compile test: (save template to test again)</td></tr>";
			print "<tr><td><pre>" . ValidateHTMLForm($_REQUEST['editHTMLtemplate'], "customer");
			print "</pre><input name='ex' value='Parse an example form (customer " . $rnd_customer . ")' type='button' OnClick=\"window.open('ex_html_template.php?x=" . $rnd_customer . "&cform=1&data=" . $_REQUEST['editHTMLtemplate'] . "', 'logwin','width=500,height=400,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');\"></td></tr></table><br>";
			$formtype = "customer";
		} else {
			print "<table border=1 class='crm'>";
			print "<tr><td><input name='ex' value='Parse it (entity " . $rnd_entity . ")' type='button' OnClick=\"window.open('ex_html_template.php?x=" . $rnd_entity . "&data=" . $_REQUEST['editHTMLtemplate'] . "', 'logwin','width=500,height=400,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');\"></td></tr></table><br>";
			$formtype = "customer";

		}

		print "<form name='editHTMLtemplateform' METHOD='post'>";
		print "<input type='hidden' name='templates' value='1'>";
		print "<input type='hidden' name='editHTMLtemplate' value='" . $_REQUEST['editHTMLtemplate'] . "'>";
		print "<input type='hidden' name='saveHTMLtemplate' value='" . $_REQUEST['editHTMLtemplate'] . "'>";
		print "<b> Subject: </b><input type='text' size=70 name='subject' value='" . $row['file_subject'] . "'>&nbsp;&nbsp;&nbsp;<input type='submit' value='Save template to database'><br><br>";
				
				include("fckeditor/fckeditor.php");

				//$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, "_samples" ) ) ;
				$oFCKeditor = new FCKeditor('data') ;
				$oFCKeditor->BasePath	= "fckeditor/" ;
				$oFCKeditor->Width = "100%";
				$oFCKeditor->Height = "600";
				$oFCKeditor->ToolbarSet = 'CRM';

				$oFCKeditor->Value		= stripslashes($row['content']);
				$oFCKeditor->Create() ;


		print "</td><td>";

		print "</td></tr></table>";
		print "<br>&nbsp;&nbsp;&nbsp;<input type='submit' value='Save template to database'>";
		print "</form><table><tr><td><br><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?templates=1' style='cursor:pointer'>Back to templates page</a><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='trigger.php' style='cursor:pointer'>To event triggers page</a><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password&joepie=1' style='cursor:pointer'>Back to main administration page</a></td></tr></table></td></tr>";
		print "</table>";
	} else {

		

		
		if ($_REQUEST['t1']) {

			// Some filter was given

			$t = $_REQUEST['t1'];

			switch ($t) {
				case "add":
					$dnd = true;
				break;
				case "html":
					$qins = " AND filetype ='TEMPLATE_HTML' ";
					$legend = "Plain HTML templates";
				break;
				case "htmlr":
					$qins = " AND filetype ='TEMPLATE_HTML_REPORT'";
					$legend = "HTML Summary page reports";
				break;
				case "htmlf":
					$qins = " AND (filetype ='TEMPLATE_HTML_FORM' OR filetype ='TEMPLATE_HTML_CFORM')";
					$legend = "Entity & customer forms";
				break;
				case "rtfr":
					$qins = " AND (filetype ='TEMPLATE_REPORT' OR filetype ='TEMPLATE_MAILMERGE')";
					$legend = "Report and mailmerge templates";
				break;
				case "rtfi":
					$qins = " AND filetype ='TEMPLATE_INVOICE' ";
					$legend = "Invoice templates";
				break;
				case "all":
					$qins = "";
					$legend = "Report, mailmerge, invoice RTF and HTML templates";
				break;

			}

/* admin.php?templates=1&nav=add&t1=add"     => "New te
admin.php?templates=1&nav=html&t1=html"   => "HTML T
admin.php?templates=1&nav=htmlforms&t1=htmlf" => "HT
admin.php?templates=1&nav=rtfreports&t1=rtfr"   => "
admin.php?templates=1&nav=rtfinvoices&t1=rtfi"   => 
admin.php?templates=1&nav=all&t1=all"     => "All do
   */                                                 

		}

		if (!$dnd) {
		
			print "<table width=100%><tr><td>&nbsp;&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>" . $legend . "</font>&nbsp;</legend><br>";
			print "<table width=100%><tr><td>&nbsp;</td><td><table cellspacing='0' cellpadding='4' border=1 bordercolor='#F0F0F0' width=100%>";
			print "<tr><td>File id</td><td>Filename/Template name</td><td>Document type</td><td>Created by</td><td>File subject</td><td>Creation date</td><td>Delete</td><td>Edit</td></tr>";

			$sql = "SELECT fileid,filename,creation_date,username,filetype,file_subject FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='0' AND LEFT(filetype,8)='TEMPLATE'" . $qins . " ORDER BY filename";
			$result = mcq($sql,$db);
			while ($row = mysql_fetch_array($result)) {
				$edit = false;
				print "<tr><td>" . $row['fileid'] . "</td><td><a class='bigsort' href='csv.php?fileid=" . $row['fileid'] ."'>" . $row['filename'] . "</a></td>";
				if ($row['filetype'] == "TEMPLATE_REPORT") {
						$type = "Entity report";
						$row['file_subject'] = "n/a";
				} elseif ($row['filetype'] == "TEMPLATE_INVOICE") {
						$type = "Invoice";
						$row['file_subject'] = "n/a";
				} elseif ($row['filetype'] == "TEMPLATE_MAILMERGE") {
						$type = "Mailmerge";
						$row['file_subject'] = "n/a";
				} elseif ($row['filetype'] == "TEMPLATE_HTML") {
						$type = "HTML";
						$edit = true;
				} elseif ($row['filetype'] == "TEMPLATE_HTML_FORM") {
						$type = "HTML Entity form";
						$edit = true;
				} elseif ($row['filetype'] == "TEMPLATE_HTML_CFORM") {
						$type = "HTML Customer form";
						$edit = true;
				} elseif ($row['filetype'] == "TEMPLATE_HTML_REPORT") {
						$type = "HTML Report";
						$edit = true;
				} else {
						$type = "Unknown";
						$row['file_subject'] = "n/a";
				}
				print "<td>" . $type . "</td>";
				if (is_numeric($row['username'])) {
					$owner = GetUserName($row['username']);
				} else {
					$owner = $row['username'];
				}

				print "<td>" . $owner . "</td><td>" . $row['file_subject'] . "</td><td>" . $row['creation_date'] . "</td><td><img src='arrow.gif'>";
			
				if ($GLOBALS['DefaultForm'] == $row['fileid']) {
						print " <font color='#B0B0B0'>Delete</font>";
					} else {
						print "<a href='admin.php?templates=1&deletetemplate=" . $row['fileid'] . "' class='bigsort'> Delete</a>";
					}
				print "<td>";
				if ($edit) {
						print "<img src='arrow.gif'><a href='admin.php?templates=1&editHTMLtemplate=" . $row['fileid'] . "' class='bigsort'> Edit</a>";
				} else {
					print "n/a";
				}
				print "</td>";
				print "</tr>";
			}
			print "</table></td></tr></table><br><br></fieldset></td></tr><tr><td></td><td><br><br>";
		}
		if ($_REQUEST['t1']=="add" || $_REQUEST['t1']=="") {

			$a = "<table width=70%>";
			$a .= "<tr><td><form name='html' method='post'>Name: <input type='text' name='new_HTML_template'>&nbsp;&nbsp;Document type: <select name='filetype'><option value='TEMPLATE_HTML'>HTML Plain template</option><option value='TEMPLATE_HTML_FORM'>HTML Entity form template</option><option value='TEMPLATE_HTML_CFORM'>HTML Customer form template</option><option value='TEMPLATE_HTML_REPORT'>HTML Summary page report template</option></select>&nbsp;&nbsp;<input class='txt' type=submit name=sb value='Create'><INPUT TYPE='hidden' name='templates' value='1'></form>";
			$a .= "</form></td></tr>";
			$a .= "<tr><td><br>Legend:<br>";
			$a .= "<ul><li>HTML Plain template: A plain HTML template to use as email, a page (custom tab) or as comment field</li>";
			$a .= "<li>HTML Entity form template: A template containing an entity edit form</li>";
			$a .= "<li>HTML Customer form template: A template containing a customer edit form</li>";
			$a .= "<li>HTML Summary page report template: A template to use as a report on the summary page</li>";
			$a .= "</ul></td></tr></table>";
			$legend = "Create a new empty HTML template&nbsp;";

			printbox($a);

			$a = "<table width=70%>";
			$a .=  "<tr><form method='POST' name='bla' ENCTYPE='multipart/form-data'><td colspan=6><br><INPUT TYPE='hidden' name='MAX_FILE_SIZE' value='52428800'><INPUT TYPE='hidden' name='templates' value='1'><INPUT NAME='userfile' TYPE='file'>&nbsp;&nbsp;&nbsp;&nbsp;<a OnClick='javascript:pophelp(9)' class='topnav' cursor='click' style='cursor: help'><img src='info.gif'></a>&nbsp;&nbsp;Document type:&nbsp;<select name='filetype'><option value='TEMPLATE_INVOICE'>RTF Invoice template</option><option value='TEMPLATE_MAILMERGE'>RTF Mailmerge template</option><option value='TEMPLATE_REPORT'>RTF Report template</option></select>&nbsp;&nbsp;<input class='txt' type=submit name=sb value='Upload'>";
			$a .= "</form></td><tr></table>";
			$legend = "Upload a new RTF template&nbsp;";
			printbox($a);

		}
	
		print "</td></tr><tr><td></td><td>";
		print "<br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password&joepie=1' style='cursor:pointer'>Back to main administration page</a><br>&nbsp;</td></tr>";
	}		
	
	if ($_REQUEST['editHTMLtemplate']) {
		print "<tr><td><table class='crm'><tr><td valign='top'>";
		AvailableTags();
		print "</td><td>";
	}

	if ($formedit == true) {
		//print "<table><tr><td>";
		if ($formtype == "customer") {
			AvailableCustomerFormTags();
		} else {
			AvailableFormTags();
		}
		//print "</td></tr></table>";
	}
	print "</td></tr></table></td></tr></table></td></tr></table>";
	
	EndHTML();
	exit;

}
if ($files) {
			?>
			<form name='delform' method='post'>
			<input type='hidden' name='password' value='<? echo $password; ?>'>
			<input type='hidden' name='kid' value=''>
			<input type='hidden' name='PhysFileDel' value='1'>
			</form>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				function appdel(i) {
					document.delform.kid.value = i;
					document.delform.submit();
				}
			//-->
			</SCRIPT>

		<?
	print "<tr><td colspan=12><table border='1' width='100%' bgcolor='#F2F2F2' cellspacing='0' cellpadding='8' bordercolor='#CECECE'>";
	print "<tr><td>FileID</td><td>Filename</td><td>Creation Date</td><td>Size</td><td>Filetype</td><td>E/C</td><td>Options</td><td>Referenced to</td><td>Parent entity status</td></tr>";
	print "Viewing files, sorted by filesize descending";

	$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]binfiles";
	$result= mcq($sql,$db);
	$bla= mysql_fetch_array($result);
	$bla = $bla[0];
	if ($bla > 600) {
		$legend = "<img src='error.gif'>";
		printbox("You have too many files in your database to use this function browser-safely. With $bla files the list would simply be too long and your browser would probably crash.");
		print "</table></td></tr></table>";
		EndHTML();
		exit;
	}

	$sql = "SELECT fileid,koppelid,filename,type,date_format(creation_date, '%a %M %e, %Y %H:%i') AS dt,filesize,filetype FROM $GLOBALS[TBL_PREFIX]binfiles ORDER BY filesize DESC";
	$result= mcq($sql,$db);
     while ($files= mysql_fetch_array($result)) {
		
		if ($files['type']<>"cust") {
			$sql1 = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE eid=$files[koppelid]";
			$result1= mcq($sql1,$db);
			$count_e1 = mysql_fetch_array($result1);
			$count_e = $count_e1[0];
			if ($count_e=="") {
				$ref = "<font color='ff0000'>Unreferenced!</font>";
				$parent = "<font color='ff0000'>n/a</font>";
			} else {
				$ref = "<font color='#33FF00'>OK</font> - $files[koppelid]";
				$parent = $count_e1[status];
			}
		} else {
			$sql1 = "SELECT * FROM $GLOBALS[TBL_PREFIX]customer WHERE id=$files[koppelid]";
			$result1= mcq($sql1,$db);
			$count_e1 = mysql_fetch_array($result1);
			$count_e = $count_e1['id'];
			if ($count_e=="") {
				$ref = "<font color='ff0000'>Unreferenced!</font>";
				$parent = "<font color='ff0000'>n/a</font>";
			} else {
				$ref = "<font color='#33FF00'>OK</font> - $files[koppelid]";
				$parent = $count_e1[status];
			}
		}


		print "<tr><td>$files[fileid]</td><td><img src='arrow.gif'>&nbsp;<a href='csv.php?fileid=$files[fileid]' class='bigsort'>$files[filename]</a></td><td>";
		print $files[dt];
		print "</td><td>";
		$a = round(($files[filesize]/1024));
		print "$a K";
		if ($files[filetype]=="") {
			$files[filetype] = "no info";
		}
		print "</td><td>$files[filetype]</td><td>$files[type]</td><td><img src='arrow.gif'>&nbsp;<a href='edit.php?e=$files[koppelid]' class='bigsort' target='_new'>Jump</a>&nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<a href='javascript:appdel($files[fileid])' class='bigsort'>Delete</a></td><td>$ref</td><td>$parent</td></tr>";
		$tot++;
		unset($parent);
		$totsize = $totsize + $files[filesize];
				 }
	print "</table></td></tr>";
	print "<tr><td>Total $totsize bytes (approx." . round($totsize/1048576) . " MB) in $tot files.</td></tr></table>";
	EndHTML();
	exit;
}
if ($_REQUEST['SaveForcedFields'] && $_REQUEST['ForcedFields'] ) {
	MustBeAdmin();
		$GLOBALS['MainForcedFields'] = array();
		$GLOBALS['MainForcedFields'][0] = array();
		$GLOBALS['MainForcedFields'][0]['fieldtype'] = "main";
		$GLOBALS['MainForcedFields'][0]['name'] = "cat";
		$GLOBALS['MainForcedFields'][0]['forcing'] = $_REQUEST['catforced'];
		$GLOBALS['MainForcedFields'][1] = array();
		$GLOBALS['MainForcedFields'][1]['fieldtype'] = "date";
		$GLOBALS['MainForcedFields'][1]['name'] = "displayDate";
		$GLOBALS['MainForcedFields'][1]['forcing'] = $_REQUEST['displayDateforced'];
		$GLOBALS['MainForcedFields'][2] = array();
		$GLOBALS['MainForcedFields'][2]['fieldtype'] = "main";
		$GLOBALS['MainForcedFields'][2]['name'] = "content";
		$GLOBALS['MainForcedFields'][2]['forcing'] = $_REQUEST['contentforced'];

		$sql = "UPDATE $GLOBALS[TBL_PREFIX]settings SET value='" . serialize($GLOBALS['MainForcedFields']) . "' WHERE setting='REQUIREDDEFAULTFIELDS'";
		mcq($sql,$db);

} elseif ($_REQUEST['ForcedFields']) {
		MustBeAdmin();
		$AMF = unserialize($GLOBALS['REQUIREDDEFAULTFIELDS']);
		if (is_array($AMF)) {
			foreach ($AMF AS $field) {

				if ($field['name'] == "cat" && $field['forcing'] == "y") {
					$cat_ins = "CHECKED";
				} else if ($field['name'] == "displayDate" && $field['forcing'] == "y") {
					$dd_ins = "CHECKED";
				} else if ($field['name'] == "content" && $field['forcing'] == "y") {
					$cont_ins = "CHECKED";
				} 
			}
		}
		print "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";
		?>
		<form name='SFF' method='post'>
		<br>
		Select which fields should be required when saving an entity:<br><br>
		<table>
			<tr>
				<td>Require category</td><td><input type='checkbox' <? echo $cat_ins;?> name='catforced' value='y'></td>
			</tr>
			<tr>
				<td>Require due date</td><td><input type='checkbox' <? echo $dd_ins;?>  name='displayDateforced' value='y'></td>
			</tr>
			<tr>
				<td>Require main text box content</td><td><input type='checkbox' <? echo $cont_ins;?>  name='contentforced' value='y'></td>
			</tr>
		</table>
		<br>
		<input type='submit' name='SaveForcedFields' value='Save'>
		</form>
		<br><br>
		</td></tr></table>
		<?
		EndHTML();
		exit;
}
if ($_REQUEST['RemoveAllEntityLocks']) {
	log_msg("All entity locks removed");
	$num = RemoveLocks(true);
	print "&nbsp;&nbsp;&nbsp;All entity locks removed (" . $num ." records)<br>";
}
if ($SuperListUsers) {
	MustBeAdmin();
	UserSectionTabs();
	log_msg("DisplayUserActivityGraph Journal=$Journal","");
	
	print "<table width='60%'><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;All users activity graphs&nbsp;</legend>";
	
	if (!$Journal && !$Uselog) {

			print "<br>CRM-CTT logs in two ways:<br>";
			print "<ul><li><img src='arrow.gif'>&nbsp;<a href='admin.php?SuperListUsers=1&Uselog=1' class='bigsort'>The use-log</a><br><br>All actions of user are logged. These actions include every page reload, logins, errors etcetera. This log may contain very much records if you have a heavily used repository; the statistics may take a while to load.</li>";
			print "<br><br><li><img src='arrow.gif'>&nbsp;<a href='admin.php?SuperListUsers=1&Journal=1' class='bigsort'>Journal entries</a><br><br>The journal entries log more specific activity rather than browse-thru statistics. In the journal things like adding a file, saving an enity, and sending an e-mail are logged. The journal is the best to use for statistics to get an overview of <i>real</i> activity of your users.</li></ul>";

			$link = "<img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password&adduser=1&userman=1' style='cursor:pointer'>Back to accounts administration page</a>";

	} else {
			

			if (!$Journal) {
					print "<b>These images are generated using the 'uselog' - e.g. they represent any action; may me <br>just viewing. To see the charts based on the entity journals, <img src='arrow.gif'>&nbsp;<a href='admin.php?SuperListUsers=1&Journal=1' class='bigsort'>click here</a>.</b><br><br>";
			} elseif($Uselog) {
					print "<b>User activity charts based on entity journals</b><br><br>";
			}
			
			if ($J) {
				$link = "";
			} else {
				$link = "<img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password&adduser=1&userman=1' style='cursor:pointer'>Back to accounts administration page</a>";
			}
			print $link;
			$sql = "SELECT id,FULLNAME,name FROM $GLOBALS[TBL_PREFIX]loginusers WHERE active <> 'no' ORDER BY FULLNAME";
			$result = mcq($sql,$db);
			while ($row = mysql_fetch_array($result)) {
				if (trim($row[FULLNAME]=="")) { $row[FULLNAME]=$row[name];}
				print "<br><b>" . GetUserName($row['id']) . "</b><br>";
				if ($Journal) {
					print "<img src='admin.php?ActivityUserGraph=" . $row['id'] . "&Journal=1'><br>";
				} else {
					print "<img src='admin.php?ActivityUserGraph=" . $row['id'] . "'&Uselog=1><br>";
				}
			}
	}
	print $link;
	print "</fieldset></td></tr></table>";
EndHTML();
	exit;

}


if ($GLOBALS[FORCED_TBL]=="1") {
		print "<tr><td><img src='error.gif'>&nbsp;<font color='#FF0000'>You have no table prefix set in your config file. Please adjust this by adding a " . '$table_prefix[' . $repository_nr . "] = \"CRM\"; to your config file section for this repository ($title)</font></td></tr>";
}

if ($admpassword=="*NONE*") {
		$password=$admpassword;
} 

if (!is_administrator()) {

	if ($password<>$admpassword) {
					print "<tr><td><form name='pwd' method='post'>";
					if ($password) {
						print "<b>$lang[wrongpwd].</b><br>";
						log_msg("Access to administrative section denied","");
					} else {
						print "<b>$lang[havetoenterpwd]:</b><br>"; 
					}
						print "<br><input type='password' name='password'><br><br><input type='submit' name='knop' value='Log in'>";
   					    print "<input type='hidden' name='admin' value='on'>";
					    print "</form></td><tr></table>";
EndHTML();
					    exit;
	}
} else {
	if (!$nonavbar) {
//		print $lang[nopwd];
	}
}

if ($EditVars) {
//	AdminTabs();
	print "</td></tr></table>";
	print "</td></tr></table>";
	MainAdminTabs("a");
//	print "</td></tr></table>";

	print "<table width=100%><tr><td>";
	//print "</td></tr></table><table width='100%'><tr><td>";
	$to_tabs = array("status","priority","newstatus","newpriority");
	$tabbs["main"] = array("admin.php" => "<b>Back</b>");
	$tabbs["status"] = array("admin.php?password=$password&EditVars=1&WhatVar=stat" => "Status values");
	$tabbs["priority"] = array("admin.php?password=$password&EditVars=1&WhatVar=prio" => "Priority values");
	$tabbs["newstatus"] = array("admin.php?password=&statusvar=X25ld18=&StatusVars=1&EditVars=1&WhatVar=newstat" => "New status value");
	$tabbs["newpriority"] = array("admin.php?password=&priorityvar=X25ld18=&priorityVars=1&EditVars=1&WhatVar=newprio" => "New priority value");

	if ($_REQUEST['WhatVar'] == "stat") {
		$navid = "status";
	} elseif ($_REQUEST['WhatVar'] == "newstat") {
		$navid = "newstatus";
	} elseif ($_REQUEST['WhatVar'] == "newprio") {
		$navid = "newpriority";
	} else {
		$navid = "priority";
	}
	InterTabs($to_tabs, $tabbs, $navid);

	if ($WhatVar=="stat" || $WhatVar=="newstat") {
		StatusVars();
	} elseif($WhatVar=="prio" || $WhatVar=="newprio") {
		priorityVars();
	}
}

if ($EditVars) {
	$legend = "Edit status and priority variables";
	$printbox_size = "100%";
	printbox("<br><img src='arrow.gif'>&nbsp;<a href='admin.php?password=$password&EditVars=1&WhatVar=stat' class='bigsort'>Edit status variables</a><br><img src='arrow.gif'>&nbsp;<a href='admin.php?password=$password&EditVars=1&WhatVar=prio' class='bigsort'>Edit priority variables</a><br><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password&joepie=1' style='cursor:pointer'>Back to main administration page</a></td></tr>");
	?>
	</table>
	</body></html>
	<?
	exit;
}


if ($ViewJournal) {
	MustBeAdmin();
	ViewJournal($VJ);
}

if ($priorityVars) {
	MustBeAdmin();
	priorityVars();
}

if ($StatusVars) {
	MustBeAdmin();
	StatusVars();
}

if ($advquery) {
	MustBeAdmin();
	AdvQuery();
}


if ($EditSysVar) {
	MustBeAdmin();
	EditSysVar($EditSysVar);
}
if ($ImportSettings) {
	MustBeAdmin();
	ImportSettings();
}

if ($_REQUEST['newuser']) {

		$sql = "SELECT name FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$newuser'";
		$result= @mcq($sql,$db);
		$maxU1 = @mysql_fetch_array($result);
		$q = $maxU1[0];
		
		if (strlen($q)>0) {
			print "<tr><td><font color='#FF3300'>ERROR: An account named $q already exists! Account not added.</font></td></tr>";
			print "<tr><td><img src='arrow.gif'>&nbsp;<a href='javascript:history.back(1);' class='bigsort'>back</a> </td></tr>";
			print "</table>";
			EndHTML();
			exit;

		} elseif ($newpassword=="" && $newpasswordconfirm=="") {
			print "<tr><td><font color='#FF3300'>ERROR: A password MUST be given.</font></td></tr>";
			print "<tr><td><img src='arrow.gif'>&nbsp;<a href='javascript:history.back(1);' class='bigsort'>back</a> </td></tr>";
			print "</table>";
				EndHTML();
			exit;

		} elseif ($newpassword==$newpasswordconfirm) {
				if ($CLLEVEL=="administrator") {
						$CLLEVEL = "rw";
						$ADMIN = "Yes";
				}
				if ($FULLNAME=="" || !$FULLNAME) { $FULLNAME=$newuser; }
				if ($CLLEVEL<>"ro" && stristr($FULLNAME,"@@@")) { $FULLNAME=$newuser; }

				if ($_REQUEST['profile']=="") {
					$_REQUEST['profile'] = "Default";
				}
                                $flag_admin = isset($_REQUEST['supervision']['1']) ? $_REQUEST['supervision']['1'] : 0 ;
                                $flag_opera = isset($_REQUEST['supervision']['2']) ? $_REQUEST['supervision']['2'] : 0 ;

				$sql= "INSERT INTO $GLOBALS[TBL_PREFIX]loginusers (name,password,FULLNAME,EMAIL,CLLEVEL,RECEIVEDAILYMAIL,administrator,active,PROFILE,ADDFORMS, flag_admin, flag_opera) VALUES('$newuser',PASSWORD('$newpassword'),'$FULLNAME','$EMAIL','$CLLEVEL','$dailymail','$ADMIN','yes','" . $_REQUEST['profile'] . "','a:1:{i:0;s:7:\"default\";}', '$flag_admin', '$flag_opera')";
                                //print "INSERT INTO $GLOBALS[TBL_PREFIX]loginusers (name,password,FULLNAME,EMAIL,CLLEVEL,RECEIVEDAILYMAIL,administrator,active,PROFILE,ADDFORMS, flag_admin, flag_opera) VALUES('$newuser',PASSWORD('$newpassword'),'$FULLNAME','$EMAIL','$CLLEVEL','$dailymail','$ADMIN','yes','" . $_REQUEST['profile'] . "','a:1:{i:0;s:7:\"default\";}', '$flag_admin', '$flag_opera')";
				
				mcq($sql,$db);
				$a = mysql_insert_id();
				log_msg("ACCOUNT ADDED - $newuser - type normal - CLLEVEL $CLLEVEL $ADMIN FULLNAME $FULLNAME","");
			    //print "<tr><td><font color='FF0000'>User $FULLNAME was added with clearance level '$CLLEVEL $ADMIN'.id $a profile " . $_REQUEST['profile'] . "</font></td></tr>";	
				$userman=1;
				usermanager($a);
				exit;
		} else {
		    print "<tr><td><font color='FF0000'>ERROR: $lang[passmatcherror]</font></td></tr>";
			print "<tr><td><img src='arrow.gif'>&nbsp;<a href='javascript:history.back(1);' class='bigsort'>back</a> </td></tr>";
			print "</table>";
			EndHTML();
			exit;
		}
}

if ($_REQUEST['userman']) {
    usermanager($_REQUEST['user_to_edit']);
	EndHTML();
	exit;
}
if ($_REQUEST['failoverman']) {
	MustBeAdmin();
	mainadmintabs();
	log_msg("Fail over section accessed");
	
	if ($_REQUEST['unlockstalled']) {
		if ($_REQUEST['unlockstalled'] == "slave") {
			$db = mysql_pconnect($GLOBALS['FO_DB'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
			mysql_select_db($GLOBALS['DBNAME'],$db); 
			$th = $GLOBALS['DBHOST'];
		} elseif ($_REQUEST['unlockstalled'] == "master") {
			$db = @mysql_pconnect($GLOBALS['DBHOST'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
			@mysql_select_db($GLOBALS['DBNAME'],$db); 
			$th = $GLOBALS['FO_DB'];		
		}
		mcq("UPDATE " . $GLOBALS['TBL_PREFIX'] . "failoverquerystore SET lockhash ='' WHERE targethost = '" . $th . "'", $db);

		log_msg("Records from " . $_REQUEST['unlockstalled'] . " database unlocked on administrator request.");

		$db = @mysql_pconnect($GLOBALS['DBHOST'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
		@mysql_select_db($GLOBALS['DBNAME'],$db);
	}

	if ($_REQUEST['droplist']) {
		if ($_REQUEST['droplist'] == "slave") {
			$db = @mysql_pconnect($GLOBALS['FO_DB'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
			@mysql_select_db($GLOBALS['DBNAME'],$db); 
			$th = $GLOBALS['DBHOST'];
		} else {
			$db = @mysql_pconnect($GLOBALS['DBHOST'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
			@mysql_select_db($GLOBALS['DBNAME'],$db);
			$th = $GLOBALS['FO_DB'];
		}
		mcq("DELETE FROM " . $GLOBALS['TBL_PREFIX'] . "failoverquerystore WHERE targethost = '" . $th . "'", $db);
		log_msg("Records from " . $_REQUEST['droplist'] . " database deleted on administrator request.");

		$db = @mysql_pconnect($GLOBALS['DBHOST'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
		@mysql_select_db($GLOBALS['DBNAME'],$db);
	}


	print "<table width=50%><tr><td>&nbsp;&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;Fail-over status <img src='arrow.gif'> <a href='admin.php?failoverman=1'>refresh</a></legend>";
	print "<table class='crm' width=100%>";
	print "<tr><td>Currently running on database</td><td>" . $GLOBALS['DBHOST'] . "</td></tr>";
	print "<tr><td>Configured slave database</td><td>" . $GLOBALS['FO_DB'] . "</td></tr>";
	if ($GLOBALS['FO_DB_IS_DOWN']) {
		print "<tr><td>Slave database status</td><td><font color='#FF0000'>DOWN</font></td></tr>";
	} else {
		print "<tr><td>Slave database status</td><td><font color='#66CC00'>OK</font></td></tr>";
	}

	$num = db_GetRow("SELECT COUNT(*) FROM " . $GLOBALS['TBL_PREFIX'] . "failoverquerystore WHERE targethost='" . $GLOBALS['FO_DB'] . "'");
	$lo_local = $num[0];
	if ($_REQUEST['viewlist']) {
		print "<tr><td>Local jobs waiting for replication </td><td>" . $num[0] . "";
		if ($num[0] > 0) {
			print "<img src='arrow.gif'> <a href='admin.php?failoverman=1&viewlist1=" . $_REQUEST['viewlist1'] . "'>hide</a> <img src='arrow.gif'> <a href='admin.php?failoverman=1&droplist=master'>empty</a> <img src='arrow.gif'> <a href='admin.php?failoverman=1&unlockstalled=master'>unlock stalled records</a></td></tr>";
		} else {
			print "</td></tr>";
		}
	} else {

		print "<tr><td>Local jobs waiting for replication </td><td>" . $num[0] . "";
		if ($num[0] > 0) {
			print "<img src='arrow.gif'> <a href='admin.php?failoverman=1&viewlist=master&viewlist1=" . $_REQUEST['viewlist1'] . "'>view</a> <img src='arrow.gif'> <a href='admin.php?failoverman=1&droplist=master'>empty</a> <img src='arrow.gif'> <a href='admin.php?failoverman=1&unlockstalled=master'>unlock stalled records</a></td></tr>";
		} else {
			print "</td></tr>";
		}
	}

	if ($GLOBALS['FO_DB_IS_DOWN']) {
		print "<tr><td>Remote jobs waiting for replication </td><td>n/a</td></tr>";
	} else {
		$db = @mysql_pconnect($GLOBALS['FO_DB'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
		if (@mysql_select_db($GLOBALS['DBNAME'],$db)) {
			$GLOBALS['IN_SYNC_FUNC'] = true;
			$num = db_GetRow("SELECT COUNT(*) FROM " . $GLOBALS['TBL_PREFIX'] . "failoverquerystore WHERE targethost='" . $GLOBALS['DBHOST'] . "'");
			unset($GLOBALS['IN_SYNC_FUNC']);
		} 
		@mysql_close($db);
		
		$db = mysql_pconnect($GLOBALS['DBHOST'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
		@mysql_select_db($GLOBALS['DBNAME'],$db);
		if ($_REQUEST['viewlist1'] == "slave") {
			print "<tr><td>Remote jobs waiting for replication </td><td>" . $num[0] . "";
			if ($num[0] > 0) {
				print "<img src='arrow.gif'> <a href='admin.php?failoverman=1&viewlist=" . $_REQUEST['viewlist'] . "'>hide</a> <img src='arrow.gif'> <a href='admin.php?failoverman=1&droplist=slave'>empty</a> <img src='arrow.gif'> <a href='admin.php?failoverman=1&unlockstalled=slave'>unlock stalled records</a></td></tr>";
			} else {
			print "</td></tr>";
			}
		} else {
			print "<tr><td>Remote jobs waiting for replication </td><td>" . $num[0] . "";
			if ($num[0] > 0) {
				print "<img src='arrow.gif'> <a href='admin.php?failoverman=1&viewlist1=slave&viewlist=" . $_REQUEST['viewlist'] . "'>view</a> <img src='arrow.gif'> <a href='admin.php?failoverman=1&droplist=slave'>empty</a> <img src='arrow.gif'> <a href='admin.php?failoverman=1&unlockstalled=slave'>unlock stalled records</a></td></tr>";
			} else {
			print "</td></tr>";
			}
		}
	}
	print "<tr><td>Replication status</td><td>";
	if ($num[0] <> 0 && $lo_local <> 0) {
		if ($GLOBALS['FO_DB_IS_DOWN']) {
			print "<font color='#6666FF'>SYNC HALTED</font> [will continue when host is up]";
		} else {
			print "<font color='#6666FF'>SYNC HALTED</font> [empty local or remote job queue to enable synchronisation]";
		}
	} else {
		print "<font color='#66CC00'>OK</font> ";
	}
	print "</td></tr>";

	if (!$GLOBALS['FO_DB_IS_DOWN']) {
		$_REQUEST['synccheck'] = true;
	}
	
	if ($_REQUEST['synccheck']) {
		$db = @mysql_pconnect($GLOBALS['FO_DB'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
		@mysql_select_db($GLOBALS['DBNAME'],$db);
		$sql = "SELECT eid, category FROM " . $GLOBALS['TBL_PREFIX'] . "entity ORDER BY eid DESC LIMIT 20";
		$res = mcq($sql, $db);
		while ($row = mysql_fetch_array($res)) {
			$string1 .= "EID: " . $row['eid'] . " CAT:" . $row['category'];
		}
		$db = mysql_pconnect($GLOBALS['DBHOST'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
		@mysql_select_db($GLOBALS['DBNAME'],$db);
		$sql = "SELECT eid, category FROM " . $GLOBALS['TBL_PREFIX'] . "entity ORDER BY eid DESC LIMIT 20";
		$res = mcq($sql, $db);
		while ($row = mysql_fetch_array($res)) {
			$string2 .= "EID: " . $row['eid'] . " CAT:" . $row['category'];
		}
		if ($string1 <> $string2) {
			print "<tr><td>Database integrity</td><td><font color='#6666FF'>Compromised</font></a> (see trace for details)</td></tr>";
			qlog("Following strings are not the same (combi of last 20 eids + category)");
			qlog($string1);
			qlog($string2);
		} else {
			print "<tr><td>Database integrity</td><td><font color='#66CC00'>OK</font></a></td></tr>";
		}
	} else {
		if ($GLOBALS['FO_DB_IS_DOWN']) {
			print "<tr><td>Database integrity</td><td>n/a</td></tr>";
		} else {
			print "<tr><td>Database integrity</td><td><img src='arrow.gif'>&nbsp;<a href='admin.php?failoverman=1&synccheck=1' class='bigsort'>Check now</a></td></tr>";
		}
	}
	print "</table>";
	print "</fieldset></td></tr></table>";
	//print "<pre>";
	//print $string1 . "\n";
	//print $string2;
	//print "</pre>";

	if ($_REQUEST['viewlist1'] == "slave") {
		$db = @mysql_pconnect($GLOBALS['FO_DB'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
		@mysql_select_db($GLOBALS['DBNAME'],$db);

		$sql = "SELECT * FROM " . $GLOBALS['TBL_PREFIX'] . "failoverquerystore WHERE targethost='" . $GLOBALS['DBHOST'] . "'";
		$res = mcq($sql, $db);
		print "<pre>";
		print "\n Remote waiting jobs\n";
		while ($row = mysql_fetch_array($res)) {
			print "&nbsp;&nbsp;&nbsp;" . $row['id'] . " " . $row['query'] . "\n";
		}
		print "</pre>";

		$db = mysql_pconnect($GLOBALS['DBHOST'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
		@mysql_select_db($GLOBALS['DBNAME'],$db);		
	} 
	if ($_REQUEST['viewlist'] == "master") {

		$sql = "SELECT * FROM " . $GLOBALS['TBL_PREFIX'] . "failoverquerystore WHERE targethost='" . $GLOBALS['FO_DB'] . "'";
		$res = mcq($sql, $db);
		print "<pre>";
		print "\n Local waiting jobs\n";
		while ($row = mysql_fetch_array($res)) {
			print "&nbsp;&nbsp;&nbsp;" . $row['id'] . " " . $row['query'] . "\n";
		}
		print "</pre>";
	}





	EndHTML();
	exit;
}

if ($deldb && $daba) {	// Delete a repository
	$daba--;

	if (!$Confirmed) {
		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$curpassword = $result1[password];


		$db = @mysql_connect($host[$daba], $user[$daba], $pass[$daba]);
		@mysql_select_db($database[$daba],$db);
		
		if ($table_prefix[$daba]=="") $table_prefix[$daba] = "CRM";
		$GLOBALS[TBL_PREFIX] = $table_prefix[$daba];

	
		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name' AND administrator='Yes'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$foreignpassword = $result1[password];

		if ($curpassword<>$foreignpassword) {
				print "<tr><tr>";
				printbox("<font color='#FF0000'>Access denied. This incident has been logged!</font>");
				log_msg("Somebody ($hidde) tried to delete this repository","");
				print "";
				EndHTML();
				exit;
		}
		
		$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='title'";
		$result= @mcq($sql,$db);
		$maxU1 = @mysql_fetch_array($result);
		$title = $maxU1[0];
		$dabb = $daba;
		$daba++;
		
		print "<tr><td>";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]binfiles";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]calendar";  
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]customaddons";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]customer";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]entity";  
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]help";    
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]languages";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]loginusers"; 
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]phonebook";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]priorityvars";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]sessions";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]settings";  
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]statusvars";  
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]uselog";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]ejournal";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]journal";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]webdav_locks";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]webdav_properties";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]triggers";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]extrafields";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]cache";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]entitylocks";
		$deltables.= "<BR>DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]userprofiles";
		$deltables.= "<BR>DROP TABLE IF EXISTS $database[$dabb].$GLOBALS[TBL_PREFIX]internalmessages";
		$deltables.= "<BR>DROP TABLE IF EXISTS $database[$dabb].$GLOBALS[TBL_PREFIX]accesscache";
		$deltables.= "<BR>DROP TABLE IF EXISTS $database[$dabb].$GLOBALS[TBL_PREFIX]users";    
		$deltables.= "<BR>DROP TABLE IF EXISTS $database[$dabb].$GLOBALS[TBL_PREFIX]contactmoments";  
		$deltables.= "<BR>DROP TABLE IF EXISTS $database[$dabb].$GLOBALS[TBL_PREFIX]blobs";  
		$deltables.= "<BR>DROP TABLE IF EXISTS $database[$dabb].$GLOBALS[TBL_PREFIX]entityformcache";
		$deltables.= "<BR>DROP TABLE IF EXISTS $database[$dabb].$GLOBALS[TBL_PREFIX]failoverquerystore";

		printbox("Are you sure you want to delete repository '$title' running on database $database[$dabb]?<br><br><font color='#FF0000'>WARNING! All your data in repository $title will be deleted!</font><br><br>SQL statements: <br><table><tr><td>$deltables</td></tr></table><br><br><img src='arrow.gif'>&nbsp;<a href='admin.php?deldb=1&daba=$daba&Confirmed=1' class='bigsort'>yes</a><br><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?manageres=1' style='cursor:pointer'>Back to main repository management page</a>");
	} else {
		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$curpassword = $result1[password];


		$db = @mysql_connect($host[$daba], $user[$daba], $pass[$daba]);
		@mysql_select_db($database[$daba],$db);
		if ($table_prefix[$daba]=="") $table_prefix[$daba] = "CRM";
		$GLOBALS[TBL_PREFIX] = $table_prefix[$daba];

		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name' AND administrator='Yes'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$foreignpassword = $result1[password];

		if ($curpassword<>$foreignpassword) {
				print "<tr><tr>";
				printbox("<font color='#FF0000'>Access denied. This incident has been logged!</font>");
				log_msg("Somebody ($hidde) tried to delete this repository","");
				print "";
				EndHTML();
				exit;
		}
		
		$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='title'";
		$result= @mcq($sql,$db);
		$maxU1 = @mysql_fetch_array($result);
		$title = $maxU1[0];
//		$sql = "DROP DATABASE $database[$daba]";
		$deltables = array();
		$p = 0;
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]binfiles";
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]calendar";  
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]customaddons";
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]customer";
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]entity";  
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]help";    
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]languages";
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]loginusers"; 
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]phonebook";
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]priorityvars";
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]sessions";
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]settings";  
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]statusvars";  
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]uselog";
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]ejournal";
		$deltables[$p++] = "DROP TABLE $database[$daba].$GLOBALS[TBL_PREFIX]journal";
		$deltables[$p++] = "DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]webdav_locks";
		$deltables[$p++] = "DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]webdav_properties";
		$deltables[$p++] = "DROP TABLE IF EXISTS $database[$daba].$GLOBALS[TBL_PREFIX]users";    
		$deltables[$p++] = "DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]triggers";
		$deltables[$p++] = "DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]extrafields";
		$deltables[$p++] = "DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]cache";
		$deltables[$p++] = "DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]entitylocks";
		$deltables[$p++] = "DROP TABLE $database[$dabb].$GLOBALS[TBL_PREFIX]userprofiles";
		$deltables[$p++] = "DROP TABLE IF EXISTS $database[$dabb].$GLOBALS[TBL_PREFIX]internalmessages";
		$deltables[$p++] = "DROP TABLE IF EXISTS $database[$dabb].$GLOBALS[TBL_PREFIX]accesscache";
		$deltables[$p++] = "DROP TABLE IF EXISTS $database[$dabb].$GLOBALS[TBL_PREFIX]contactmoments";
		$deltables[$p++] = "DROP TABLE IF EXISTS $database[$dabb].$GLOBALS[TBL_PREFIX]blobs";
		$deltables[$p++] = "DROP TABLE IF EXISTS $database[$dabb].$GLOBALS[TBL_PREFIX]entityformcache";
		$deltables[$p++] = "DROP TABLE IF EXISTS $database[$dabb].$GLOBALS[TBL_PREFIX]failoverquerystore";
		

		//printbox(" User: $user<br>Name: $name<br> Username: $username");
		for ($t=0;$t<$p;$t++) {
			//print "<br>$deltables[$t]";
			mcq($deltables[$t],$db);
		}

		printbox("Repository $title was deleted.<br><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?manageres=1' style='cursor:pointer'>Back to main repository management page</a>");
		//printbox("Repository $title was NOT deleted because this is a demo. admin.php::124.<br><br>[<a class='bigsort' href='admin.php?manageres=1' style='cursor:pointer'>Back to main repository management page</a>]");

		exit;
	}
	
exit;
} 

if ($sessionscheck && $daba) {

		include("config.inc.php");

		MustBeAdmin();

		$daba = $daba -1;
		
		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$curpassword = $result1[password];

		$db = @mysql_connect($host[$daba], $user[$daba], $pass[$daba]);
		if (@mysql_select_db($database[$daba],$db)) {
			$GLOBALS[TBL_PREFIX] = $table_prefix[$daba];
			// all ok
		} else {
			printbox("<font color='#FF0000'>This database doesn't exist and you knew it!</font>");
			print "";
			EndHTML();
			exit;
		}
		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name' AND administrator='Yes'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$foreignpassword = $result1[password];

		if ($curpassword<>$foreignpassword) {
				print "<tr><tr>";
				printbox("<font color='#FF0000'>Access denied. This incident has been logged!</font>");
				log_msg("Somebody ($name) tried to access the sessions delete function unauthorized","");
				print "";
				EndHTML();
				exit;
		}
		
		if ($sessionscheck=="delsessions") {
				$sql="DELETE FROM $GLOBALS[TBL_PREFIX]sessions";
				mcq($sql,$db);
				printbox("Sessions table $daba:$database[$daba] was emptied.");
				printbox("<img src='arrow.gif'>&nbsp;<a href='admin.php?manageres=1' class='bigsort'>Back to main repository management page</a>");
				print "";
				EndHTML();
				exit;
		}
		$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]sessions";
		$result = mcq($sql,$db);
		$list = mysql_fetch_array($result);
		$list = $list[0];
		
		printbox("Repository $daba:$database[$daba] has $list registered sessions.<br><br>Sessions are kept in the database when a user did not use the logout button when done using CRM. You can safely empty this table (to save disk space) <i>but all people currently using this repository will loose their session requiring them to login again.</i> Please mind that when you are working in this repository, you will loose your session too after emptying the session list.");
		$daba++;
		if ($list>0) {
			printbox("<img src='arrow.gif'>&nbsp;<a href='admin.php?sessionscheck=delsessions&daba=$daba' class='bigsort'>empty session table</a>");
		}
		printbox("<img src='arrow.gif'>&nbsp;<a href='admin.php?manageres=1' class='bigsort'>Back to main repository management page</a>");
		exit;
		

}
if ($checkcfg && $daba) {
	
		include("config.inc.php");
		
		MustBeAdmin();

		$daba--;

		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$curpassword = $result1[password];

		$db = @mysql_connect($host[$daba], $user[$daba], $pass[$daba]);
		$tbl = $table_prefix[$daba];
		if ($tbl=="") $tbl="CRM";
		if (@mysql_select_db($database[$daba],$db)) {
			// all ok
		} else {
			printbox("<font color='#FF0000'>This database doesn't exist and you knew it!</font>");
			print "";
			EndHTML();
			exit;
		}
		
		$sql = "SELECT password FROM " . $tbl . "loginusers WHERE name='$name' AND administrator='Yes'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$foreignpassword = $result1[password];

		if ($curpassword<>$foreignpassword) {
				print "<tr><tr>";
				printbox("<font color='#FF0000'>Access denied. This incident has been logged!</font>");
				log_msg("Somebody ($name) tried to access the Edit Extra Fields function unauthorized","");
				print "";
				EndHTML();
				exit;
		}
		check_config($tbl);
		printbox("<img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?manageres=1' style='cursor:pointer'>Back to main repository management page</a>");
		print "";
		EndHTML();
		exit;

}

if ($checkdb && $daba && $categoryvars) { // Check repository for consistancy (mostly the custom fields)

		include("config.inc.php");
		print "<table><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";
		
		MustBeAdmin();

		$daba--;

		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$curpassword = $result1[password];

		$db = @mysql_connect($host[$daba], $user[$daba], $pass[$daba]);
		if (@mysql_select_db($database[$daba],$db)) {
			// all ok
		} else {
			printbox("<font color='#FF0000'>This database doesn't exist and you knew it!</font>");
			print "";
			EndHTML();
			exit;
		}

		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name' AND administrator='Yes'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$foreignpassword = $result1[password];

		if ($curpassword<>$foreignpassword) {
				print "<tr><tr>";
				printbox("<font color='#FF0000'>Access denied. This incident has been logged!</font>");
				log_msg("Somebody ($name) tried to access the Edit extra " . strtolower($lang[category]) . " fields function unauthorized","");
				print "";
				EndHTML();
				exit;
		}
		
		if ($DeleteUnreferencedField) {
				$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name='$DeleteUnreferencedField' AND type='category'";
				mcq($sql,$db);
				$daba++;
				printbox("All data referring to $DeleteUnreferencedField has been deleted.");
				printbox("<img src='arrow.gif'>&nbsp;<a href='admin.php?checkdb=1&daba=$daba&custvars=1' class='bigsort'>back</a>");
				print "";
				EndHTML();
				
				$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('XXXXXX', 'admin.php', 'XXXXX' , 'Unreferenced " . strtolower($lang[category]) . "field $DeleteUnreferencedField (cust) in this repository deleted ','$name')";
				mcq($query,$db);
				exit;
		}

		if ($EditExtraField) {
				$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='Category pulldown list'";
				$result = mcq($sql,$db);
				$list = mysql_fetch_array($result);
				$list = $list[value];
				
				if ($DeleteExtraField) {
							$list = @explode(",",$list);
							for ($x=0;$x<sizeof($list);$x++) {
									if ($list[$x]==$DeleteExtraField) {
										$printbox_size = "100%";
										$o .= "Extra field $list[$x] deleted";

									} else {
										if ($x<sizeof($list)-2) {
											$newlist .= $list[$x] . ",";
										} else {
											$newlist .= $list[$x];
										}
									}
							}
				$sql2 = "UPDATE $GLOBALS[TBL_PREFIX]settings SET value='	$newlist' WHERE setting='Category pulldown list'";
				mcq($sql2,$db);
				printbox($o);
				$daba++;
				$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('XXXXXX', 'admin.php', 'XXXXX' , 'Extra customer field $DeleteExtraField in this repository deleted','$name')";
				mcq($query,$db);

				printbox("<img src='arrow.gif'>&nbsp;<a href='admin.php?checkdb=1&daba=$daba&categoryvars=1' class='bigsort'>back</a>");
				?>
				<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
				<!--
				document.location='admin.php?checkdb=1&daba=<? echo $daba;?>&categoryvars=1';
				//-->
				</SCRIPT>
				<?
				print "";
				EndHTML();
				exit;
		}
				if ($newval) {
						
						$sql1 = "UPDATE $GLOBALS[TBL_PREFIX]customaddons SET name='$newval' WHERE name='$EditExtraField' and type='category'";
						mcq($sql1,$db);
						$sql2 = "UPDATE $GLOBALS[TBL_PREFIX]entity SET category='$newval' WHERE category='$EditExtraField'";
						mcq($sql2,$db);
						
						

						if ($EditExtraField=="ADDNEWFIELDCRMNOW") {
							if (stristr($newval,",")) {
								printbox("<font color='#FF0000'>Commas are not allowed in field names</font><br><br>Press 'back' on your browser and try again.");
								print "";
								EndHTML();
								$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('XXXXXX', 'admin.php', 'XXXXX' , 'New extra field denied because it contained one or more commas','$name')";
								mcq($query,$db);
								exit;
							}
							$newlist = $list . "," . $newval;
							$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('XXXXXX', 'admin.php', 'XXXXX' , 'Extra customer field $newval added in this repository ','$name')";
							mcq($query,$db);
						} else {
							if (stristr($newval,",")) {
								printbox("<font color='#FF0000'>Commas are not allowed in field names</font><br><br>Press 'back' on your browser and try again.");
								print "";
								EndHTML();
								$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('XXXXXX', 'admin.php', 'XXXXX' , 'Field customer conversion denied because it contained one or more commas ','$name')";
								mcq($query,$db);
								exit;
							}
							$list = @explode(",",$list);
							for ($x=0;$x<sizeof($list);$x++) {
									if ($list[$x] == $EditExtraField) {
										$list[$x] = $newval;
									}
									if (trim($list[$x]) <> "") {
										if (($x<sizeof($list)-1) && (sizeof($list)>0)) {
											$newlist .= $list[$x] . ",";
										} else {
											$newlist .= $list[$x];
										}
									}
							}
							
						}
						if (substr($newlist,0,1) == ",") {
									$newlist = substr($newlist,1,strlen($newlist)-1);
						}
						$sql2 = "UPDATE $GLOBALS[TBL_PREFIX]settings SET value='$newlist' WHERE setting='Category pulldown list'";
						mcq($sql2,$db);
						$daba++;
						$printbox_size = "100%";
						print "Extra " . strtolower($lang[category]) . " field added<br><img src='arrow.gif'>&nbsp;<a href='admin.php?checkdb=1&daba=$daba&refresh=889234&categoryvars=1' class='bigsort'>ok</a>";
						$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('XXXXXX', 'admin.php', 'XXXXX' , 'Extra category field $EditExtraField converted to $newval in this repository ','$name')";
						mcq($query,$db);
						exit;
							
						print "";
						EndHTML();
						exit;
				}
				$daba++;
				$o = "Convert " . strtolower($lang[category]) . " field<br><br><table border=0 width=90%><form name='bla' method='POST'><tr><td>Original name:</td><td><input type='text' name='bla' value='$EditExtraField' size=60 DISABLED><input type='hidden' name='EditExtraField' value='$EditExtraField'><input type='hidden' name='daba' value='$daba'><input type='hidden' name='categoryvars' value='1'><input type='hidden' name='checkdb' value='1'><input type='hidden' name='categoryvars' value='1'></td></tr><tr><td>New name:</td><td><input type=text value='$EditExtraField' name='newval' size=60></td></tr><tr><td colspan=2><input type='submit' value='modify'></form></td></tr></table>";
				print $o;
				print "<img src='arrow.gif'>&nbsp;<a href='admin.php?checkdb=1&daba=$daba&categoryvars=1' class='bigsort'>back</a>";
				print "";
				EndHTML();
				exit;
		}
			$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('XXXXXX', 'admin.php', 'XXXXX' , 'Extra customer field section accessed ','$name')";
			mcq($query,$db);
		
			$legend = "Edit category (" . strtolower($lang[category]) . ") fields&nbsp;";
			$o = "<br>Working with repository $daba:$database[$daba]<br>";
			$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='Category pulldown list'";
			$result = mcq($sql,$db);
			$list = mysql_fetch_array($result);
			$list = $list[value];
			$ok_efl = $list;
			$list = @explode(",",$list);
			if (strtoupper($GLOBALS['ForceCategoryPulldown'])<>"YES") {
				$ot .= "<br><img src='info.gif'>&nbsp;Warning! $lang[category] fields only work when you set the FORCECATEGORYPULLDOWN directive to 'Yes' - it's not enabled now!<br>";
			}
			$ot .= "<br><b>" . $lang[category] . " fields in this repository are:</b><br><br><table border=1 width=90%>";					
			for ($x=0;$x<sizeof($list);$x++) {
				$sql2 = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name='$list[$x]' AND type='cust'";
				$result = mcq($sql2,$db);
				$list3 = mysql_fetch_array($result);
				$occ = $list3[0];
				$dabapo = $daba+1;
				$ot .= "<tr><td>$x</td><td>" . $list[$x] . "</td><td><img src='arrow.gif'>&nbsp;<a href='admin.php?EditExtraField=$list[$x]&daba=$dabapo&checkdb=1&categoryvars=1' class='bigsort'>edit</a>&nbsp;<img src='arrow.gif'>&nbsp;<a href='admin.php?EditExtraField=$list[$x]&daba=$dabapo&checkdb=1&categoryvars=1&DeleteExtraField=$list[$x]' class='bigsort'>delete</a></td></tr>";
				if ($list[$x]<>"") {
					$teller++;
				}
			}
			if ($teller>0) {
				$o .= $ot;
				unset($ot);
			}
			$o .= "</table><br>";
		
			
			
		$printbox_size = "100%";
		print $o;
		$daba++;
		print "<b>Add new " . strtolower($lang[category]) . " field:</b>";
		$o = "<table border=0 width=90%><form name='bla' method='POST'><input type='hidden' name='EditExtraField' value='ADDNEWFIELDCRMNOW'><input type='hidden' name='daba' value='$daba'><input type='hidden' name='categoryvars' value='1'><input type='hidden' name='checkdb' value='1'></td></tr><tr><td>Name</td><td><input type=text value='' name='newval' size=60>&nbsp;&nbsp;<input type='submit' value='Add'></form><br><br></td></tr></table>";
		$printbox_size = "100%";
		print $o;

		$printbox_size = "100%";
		print "<img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password' style='cursor:pointer'>Back to main administration page</a>";
		print "</td></tr></table>";
		EndHTML();
		exit;
}

if ($checkcfg && $daba) {
	
		include("config.inc.php");
		
		MustBeAdmin();

		$daba--;

		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$curpassword = $result1[password];

		$db = @mysql_connect($host[$daba], $user[$daba], $pass[$daba]);
		if (@mysql_select_db($database[$daba],$db)) {
			// all ok
		} else {
			printbox("<font color='#FF0000'>This database doesn't exist and you knew it!</font>");
			print "";
			EndHTML();
			exit;
		}
		
		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name' AND administrator='Yes'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$foreignpassword = $result1[password];

		if ($curpassword<>$foreignpassword) {
				print "<tr><tr>";
				printbox("<font color='#FF0000'>Access denied. This incident has been logged!</font>");
				log_msg("Somebody ($name) tried to access the Edit Extra Fields function unauthorized","");
				print "";
				EndHTML();
				exit;
		}
		check_config();
		printbox("<img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password' style='cursor:pointer'>Back to main administration page</a>");
		print "";
		EndHTML();
		exit;

}

if ($checkdb && $daba) { // Check repository for consistancy (mostly the custom fields)

		include("config.inc.php");
		
		MustBeAdmin();

		$daba--;
		
		if ($custvars=="1") {
			// Customer fields:
			$StNm = "Extra Customer Fields List";
			$StTp = "cust";
		} else {
			// Regular extra fields:
			$StNm = "Extra Fields List";
			$StTp = "";
		}

		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$curpassword = $result1[password];

		$db = @mysql_connect($host[$daba], $user[$daba], $pass[$daba]);
		if (@mysql_select_db($database[$daba],$db)) {
			// all ok
		} else {
			printbox("<font color='#FF0000'>This database doesn't exist and you knew it!</font>");
			print "";
			EndHTML();
			exit;
		}

		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name' AND administrator='Yes'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$foreignpassword = $result1[password];

		if ($curpassword<>$foreignpassword) {
				print "<tr><tr>";
				printbox("<font color='#FF0000'>Access denied. This incident has been logged!</font>");
				log_msg("Somebody ($name) tried to access the Edit Extra Fields function unauthorized","");
				print "";
				EndHTML();
				exit;
		}
		
		if ($DeleteUnreferencedField) {
				$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name='$DeleteUnreferencedField' AND type='$StTp'";
				mcq($sql,$db);
				$daba++;
				printbox("All data referring to $DeleteUnreferencedField has been deleted.");
				printbox("<img src='arrow.gif'>&nbsp;<a href='admin.php?checkdb=1&daba=$daba&custvars=$custvars' class='bigsort'>back</a>");
				print "";
				EndHTML();
				
				$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('XXXXXX', 'admin.php', 'XXXXX' , 'Unreferenced field $DeleteUnreferencedField in this repository deleted ','$name')";
				mcq($query,$db);
				exit;
		}

		$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='$StNm'";
		$result = mcq($sql,$db);
		$list = mysql_fetch_array($result);
		$list = trim($list[value]);
	
		$checklist = explode(",",$list);

		if ($_REQUEST['MoveUp']) {
			for ($i=0;$i<sizeof($checklist);$i++) {
				if ($checklist[$i] == $_REQUEST['MoveUp'] && ($i <> 0)) {
					$tmp = $checklist[$i-1];
					$checklist[$i-1] = $checklist[$i];
					$checklist[$i] = $tmp;
				}
			}
			for ($x=0;$x<sizeof($checklist);$x++) {
						if ($checklist[$x]<>"") {
							if ($x<>sizeof($checklist)-1) {
								$newlist .= $checklist[$x] . ",";
							} else {
								$newlist .= $checklist[$x];
							}
						}
						$newlist = str_replace(",,",",",$newlist);
			}
			$sql = "UPDATE $GLOBALS[TBL_PREFIX]settings SET VALUE='" . $newlist . "' WHERE setting='$StNm'";
			$result = mcq($sql,$db);

		} elseif ($_REQUEST['MoveDown']) {
			for ($i=sizeof($checklist)+1;$i>-1;$i--) {
				if ($checklist[$i] == $_REQUEST['MoveDown'] && ($i <> sizeof($checklist))) {
					$tmp = $checklist[$i+1];
					$checklist[$i+1] = $checklist[$i];
					$checklist[$i] = $tmp;
					$i--;
				}
			}
			for ($x=0;$x<sizeof($checklist);$x++) {
						if ($checklist[$x]<>"") {
							if ($x<>sizeof($checklist)-1) {
								$newlist .= $checklist[$x] . ",";
							} else {
								$newlist .= $checklist[$x];
							}
						}
						$newlist = str_replace(",,",",",$newlist);
			}
			$sql = "UPDATE $GLOBALS[TBL_PREFIX]settings SET VALUE='" . $newlist . "' WHERE setting='$StNm'";
			$result = mcq($sql,$db);

		}

		if ($EditExtraField) {
				
				// first check if it doesn't already exist!
				if ($newval && in_array($newval,$checklist)) {
				
					printbox("<img src='error.gif'>&nbsp;<font color='#FF0000'>Your extra field name must be unique!</font><br><img src='arrow.gif'>&nbsp;<a href='javascript:history.back(-1);' class='bigsort'>back</a>");
					print "</table>";
					EndHTML();
					exit;
				
				}
				if ($DeleteExtraField) {
							$list = @explode(",",$list);
							for ($x=0;$x<sizeof($list);$x++) {
									if ($list[$x]==$DeleteExtraField) {
										$o = "Extra field $list[$x] deleted";
									} else {
										if ($x<sizeof($list)-1) {
											$newlist .= $list[$x] . ",";
										} else {
											$newlist .= $list[$x];
										}
									}
							}
						if (substr($newlist,strlen($newlist)-1,1) == ",") {
							$newlist = substr($newlist,0,strlen($newlist)-1);
						}
						$sql2 = "UPDATE $GLOBALS[TBL_PREFIX]settings SET value='$newlist' WHERE setting='$StNm'";
						mcq($sql2,$db);

						$daba++;
						log_msg("Extra field $DeleteExtraField in this repository deleted","");


						$o .= "<br><img src='arrow.gif'>&nbsp;<a href='admin.php?checkdb=1&daba=$daba&custvars=$custvars' class='bigsort'>back</a>";
						print($o);
						?>
						<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
						<!--
						document.location='admin.php?checkdb=1&daba=<? echo $daba;?>&custvars=<? echo $custvars;?>';
						//-->
						</SCRIPT>
						<?

						//print "<pre>" . $newlist;
						EndHTML();
						exit;
				}
				if ($newval) {
						
						$sql1 = "UPDATE $GLOBALS[TBL_PREFIX]customaddons SET name='$newval' WHERE name='$EditExtraField' AND type='$StTp'";
						mcq($sql1,$db);
						
						

						if ($EditExtraField=="ADDNEWFIELDCRMNOW") {

							if (stristr($newval,",") || stristr($newval,".") || stristr($newval,"+") || stristr($newval,"'")) {
								printbox("<font color='#FF0000'>Commas, quotes, dots and + marks are not allowed in field names</font><br><br>Press 'back' on your browser and try again.");
								print "";
								EndHTML();
								$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('XXXXXX', 'admin.php', 'XXXXX' , 'New extra field denied because it contained one or more commas ','$name')";
								mcq($query,$db);
								exit;
							}
							$newlist = $list . "," . trim($newval);
							$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('XXXXXX', 'admin.php', 'XXXXX' , 'Extra field $newval added in this repository ','$name')";
							mcq($query,$db);
						} else {
							if (stristr($newval,",") || stristr($newval,".") || stristr($newval,"+")) {
								printbox("<font color='#FF0000'>Commas, dots and + marks are not allowed in field names</font><br><br>Press 'back' on your browser and try again.");
								print "";
								EndHTML();
								$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('XXXXXX', 'admin.php', 'XXXXX' , 'Field conversion denied because it contained one or more commas ','$name')";
								mcq($query,$db);
								exit;
							}
							$list = @explode(",",$list);
							for ($x=0;$x<sizeof($list);$x++) {
									if ($list[$x] == $EditExtraField) {
										$list[$x] = trim($newval);
									}
									if (trim($list[$x]) <> "") {
										if (($x<sizeof($list)-1) && (sizeof($list)>0)) {
											$newlist .= $list[$x] . ",";
										} else {
											$newlist .= $list[$x];
										}
									}
							}
							
						}
						if (substr($newlist,0,1) == ",") {
									$newlist = substr($newlist,1,strlen($newlist)-1);
						}
						$sql2 = "UPDATE $GLOBALS[TBL_PREFIX]settings SET value='$newlist' WHERE setting='$StNm'";
						mcq($sql2,$db);
						$daba++;
						print "Extra field converted</center><br><img src='arrow.gif'>&nbsp;<a href='admin.php?checkdb=1&daba=$daba&refresh=889234&custvars=$custvars' class='bigsort'>ok</a>";
						log_msg("Extra field $EditExtraField converted to $newval in this repository","");
						?>
						<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
						<!--
							document.location='admin.php?checkdb=1&daba=<? echo $daba;?>&refresh=889234&custvars=<? echo $custvars;?>'
						//-->
						</SCRIPT>
						<?
						EndHTML();
						exit;
				}
				$daba++;
				$o = "Convert extra field<br><br><table border=0 width=90%><form name='bla' method='POST'><tr><td>Original name:</td><td><input type='text' name='bla' value='$EditExtraField' size=60 DISABLED><input type='hidden' name='EditExtraField' value='$EditExtraField'><input type='hidden' name='daba' value='$daba'><input type='hidden' name='checkdb' value='1'><input type='hidden' name='custvars' value='$custvars'></td></tr><tr><td>New name:</td><td><input type=text value='$EditExtraField' name='newval' size=60></td></tr><tr><td colspan=2><input type='submit' value='modify'></form></td></tr></table>";
				printbox($o);
				printbox("<img src='arrow.gif'>&nbsp;<a href='admin.php?checkdb=1&daba=$daba&custvars=$custvars' class='bigsort'>back</a>");
				EndHTML();
				exit;
		}
			$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('XXXXXX', 'admin.php', 'XXXXX' , 'Extra field section accessed ','$name')";
			mcq($query,$db);


			$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='$StNm'";
			$result = mcq($sql,$db);
			$list = mysql_fetch_array($result);
			$list = trim($list[value]);
			$ok_efl = $list;
			$list = @explode(",",$list);
			if ($custvars) {
				print "<BR><B>Extra " . strtolower($lang['customer']) . " fields in repository $daba:$database[$daba]&nbsp;</B>";
			} else {
				print "<BR><B>Extra fields in repository $daba:$database[$daba]&nbsp;</B>";
			}
			$ot .= "<br><table cellspacing='0' cellpadding='4' border=1 bordercolor='#F0F0F0' width=90%>";				
			$ot .= "<tr><td>#</td><td>Declaration</td><td>Occurences</td><td>Type</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
			for ($x=0;$x<sizeof($list);$x++) {
				$sql2 = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name='$list[$x]' AND type='$StTp'";
				$result = mcq($sql2,$db);
				$list3 = mysql_fetch_array($result);
				$occ = $list3[0];
				$dabapo = $daba+1;
				$ot .= "<tr><td>$x</td><td nowrap>" . CleanExtraFieldName($list[$x]) . "</td><td nowrap>$occ</td><td>" . DetermineExtraFieldType($list[$x]) . "</td><td><img src='arrow.gif'>&nbsp;<a href='admin.php?&custvars=$custvars&EditExtraField=$list[$x]&daba=$dabapo&checkdb=1' class='bigsort'>Edit</a></td><td><img src='arrow.gif'>&nbsp;<a href='admin.php?EditExtraField=$list[$x]&daba=$dabapo&checkdb=1&DeleteExtraField=$list[$x]&custvars=$custvars' class='bigsort'>Delete</a></td><td><img src='arrow.gif'>&nbsp;<a href='admin.php?daba=$dabapo&checkdb=1&MoveUp=$list[$x]&custvars=$custvars' class='bigsort'>Move up</a>&nbsp;</td><td><img src='arrow.gif'>&nbsp;<a href='admin.php?daba=$dabapo&checkdb=1&MoveDown=$list[$x]&custvars=$custvars' class='bigsort'>Move down</a></td></tr>";
				if ($list[$x]<>"") {
					$teller++;
				}
			}
			if ($teller>0) {
				$o .= $ot;
				unset($ot);
			}
			$o .= "</table><br>";
			$ot = "";

			$sql = "SELECT DISTINCT name FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name<>'' AND type='$StTp'";
			$result = mcq($sql,$db);
			$ot .= "<hr><img src='error.gif'>&nbsp;There were fields found, which are not referenced to an extra field. Unreferenced fields are fields in the form of data which cannot be accessed by the user anymore because the field name is <b>not</b> on the Extra Fields List. To get this data to reoccur, click 'Correct/Add'. When you click <img src='arrow.gif'>&nbsp;delete, you will delete all the unreferenced data which could cause important data loss so handle with care. You will <b>not</b> be asked for confirmation.</font><br><br><table cellspacing='0' cellpadding='4' width=90% border=1 bordercolor='#F0F0F0'>";
			while ($list2 = mysql_fetch_array($result)) {
					//$ot .= "comparing $list2[0]";
					if (!in_array($list2[0],$list)) {
						$sql2 = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name='$list2[0]' AND type='$StTp'";
						$result2 = mcq($sql2,$db);
						$list3 = mysql_fetch_array($result2);
						$occ = $list3[0];
						$ot .= "<tr><td>$list2[0]</td><td>Occurences: $occ</td><td>" . DetermineExtraFieldType($list2[0]) . "</td><td><img src='arrow.gif'>&nbsp;<a href='admin.php?EditExtraField=$list[$x]&daba=$dabapo&checkdb=1&DeleteUnreferencedField=$list2[0]&custvars=$custvars' class='bigsort'>delete data</a></td><td><img src='arrow.gif'> <a href='admin.php?EditExtraField=ADDNEWFIELDCRMNOW&daba=" . ($daba+1) . "&checkdb=1&newval=" . urlencode($list2[0]) . "&custvars=$custvars'>Correct/add</a></td></tr>";
						$upd .= "," . $list2[0];
						$yu++;
					}
			}
			$ot .= "</table><br>";	
			//$ot .= "To fix this, update your 'Extra fields list' directive to:<br><br>$ok_efl$upd";

				print $o;

			if ($yu>0) {
				print $ot;
			} else {
				$o .= "";
			}
			unset($legend);

		$daba++;
		$o = "<HR>Add a new extra field&nbsp;";
		$o .= "<table border=0 width=90%><form name='bla' method='GET'><input type='hidden' name='EditExtraField' value='ADDNEWFIELDCRMNOW'><input type='hidden' name='custvars' value='$custvars'><input type='hidden' name='daba' value='$daba'><input type='hidden' name='checkdb' value='1'></td></tr><tr><td>Name</td><td><input type=text value='' name='newval' size=60>&nbsp;&nbsp;<input type='submit' value='Add'></form></td></tr></table>";
		$printbox_size = "100%";
		print $o;
		?>
		<br>
		Prefix any field with "HIDE_" to prevent it from showing to non-full-access users (like customers)<br>
		<ul>
			<li>Plain name for text field</li>
			<li>DD_[name]|Value1|Value2|Value3 for a dropdown box</li>
			<li>TB_[name] for a multi-line text box</li>
			<li>EML_[name] for an e-mail field</li>
			<li>LINK_[name] for a hyperlink field</li>
			<li>COMMENT_[template_num] for a template-based comment field</li>
			
		

		<?
		print "<ul><b>Available HTML templates: (click template id to add)</b>";
			$sql = "SELECT fileid, filename, file_subject FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid=0 AND filetype='TEMPLATE_HTML'";
			$result= mcq($sql,$db);
			while ($row= mysql_fetch_array($result)) {
				print "<li><a target='new' style='cursor: hand' OnClick=\"document.bla.newval.value='COMMENT_" . $row['fileid'] . "'\">" . $row['fileid'] . "</a> " . $row['filename'];
				if ($row['file_subject'] <> "") {
					print " (" . $row['file_subject'] . ")";
				}
				print "</li>";
			}
		 print "</ul>";


		print "</ul>";		
		if (strtoupper($GLOBALS['EnableMailMergeAndInvoicing'])=="YES" && $StTp=="") {
				print "<ul><li>IHC_[name] to register costs (per hour)</li>";
				print "<li>IHS_[name] for invoiceable hours spent</li>";
				print "<li>DD_VAT_[name]|percentage1|percentage2 for a local VAT field (overrules default VAT)</li>";
				print "</ul>";
		}
		print "<img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password' style='cursor:pointer'>Back to main administration page</a>";
		print "";
		EndHTML();
		exit;
}
if ($ts && $daba) {	// Show table status in plain text
							MustBeAdmin();
							$daba=$daba-1;
									

							$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
							$result= mcq($sql,$db);
							$result1= mysql_fetch_array($result);
							$curpassword = $result1[password];

							$db = @mysql_connect($host[$daba], $user[$daba], $pass[$daba]);
							if (@mysql_select_db($database[$daba],$db)) {
								// all ok
							} else {
								printbox("<font color='#FF0000'>This database doesn't exist and you knew it!</font>");
								print "";
								EndHTML();
								exit;
							}
							$tbl = $table_prefix[$daba];
							if ($tbl=="") $tbl="CRM";
							$sql = "SELECT password FROM " . $tbl . "loginusers WHERE name='$name' AND administrator='Yes'";
							$result= mcq($sql,$db);
							$result1= mysql_fetch_array($result);
							$foreignpassword = $result1[password];

							if ($curpassword<>$foreignpassword) {
									print "<tr><tr>";
									printbox("<font color='#FF0000'>Access denied. This incident has been logged!</font>");
									log_msg("Somebody ($name) tried to access the table status overview unauthorized","");
									print "";
									EndHTML();
									exit;
							}
							$sql = "SELECT value FROM " . $tbl . "settings WHERE setting='title'";
							$result= @mcq($sql,$db);
							$maxU1 = @mysql_fetch_array($result);
							$title = $maxU1[0];
							print "<table><tr><td>Table status query for database $title</td></tr></table>";
							$sql = "SHOW TABLE STATUS";
							$result= @mcq($sql,$db);
							print "<table border=1><tr><td>Name</td><td>Type</td><td>Row_format</td><td>Rows</td><td>Avg_row_length</td><td>Data_length</td><td>Max_data_length</td><td>Index_length</td><td>Data_free</td><td>Auto_increment</td><td>Create_time</td><td>Update_time</td><td>Check_time</td><td>Create_options</td><td>Comment</td>";

							while ($stat = @mysql_fetch_array($result))
								{
								print "<tr>";
								for ($g=0;$g<15;$g++) {

									print "<td>" . $stat[$g] . "&nbsp;</td>";
								}
								print "</tr>";
							}
							exit;
}
if ($dothefuckingjob_entities==1) {
		
		// Okee, import[0-(numofrecords*9)] hebben we nu.


		$list = GetExtraFields();



			if ($DELETEFIRST=="confirmed") {
					$tmp = "Current customer list emptied";
					$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]entity";
					mcq($sql,$db);
					log_msg("Entity table emptied by import","");
			}



		for ($tel=0;$tel<=($numofrecords*$func_max);$tel=$tel+$func_max) {
	
	//		Onderstaande omslachtige routine is de enige manier waarop ik het aan de praat kreeg.
	//		De array ( $import[point] ) opnemen in de SQL-query werkte niet in eerste instantie.
	//		Ik heb nu de query 'los', dwz eerst een $sql met de query vullen en dan pas uitvoeren.
	//		Wellicht zou ik de array nu wel in de $sql= op kunnen nemen maar daar heb ik nu geen 
	//		zin meer in.

			$a = ereg_replace("\[CRLF\]","\n",$import[$tel]);
			$b = ereg_replace("\[CRLF\]","\n",$import[$tel+1]);
			$c = ereg_replace("\[CRLF\]","\n",$import[$tel+2]);
			$d = ereg_replace("\[CRLF\]","\n",$import[$tel+3]);
			$e = ereg_replace("\[CRLF\]","\n",$import[$tel+4]);
			$f = ereg_replace("\[CRLF\]","\n",$import[$tel+5]);
			$g = ereg_replace("\[CRLF\]","\n",$import[$tel+6]);
			$h = ereg_replace("\[CRLF\]","\n",$import[$tel+7]);
			$i = ereg_replace("\[CRLF\]","\n",$import[$tel+8]);

			$opendate = $import[$tel+9];
			
			if (trim($opendate)<>"") {

				$day = substr($opendate,0,2);
				$mon = substr($opendate,3,2);
				$yea = substr($opendate,6,4);

				$cdate = $yea . "-" . $mon . "-" . $day;

				$openepoch = mktime (0,0,0,$mon,$day,$yea);

				$sqldate = $cdate;

			} else {
				$openepoch = date('U');		
				$cdate = date('Y-m-d');
				$sqldate = $cdate;
			}
			if (strlen($a)>0) {			// When the first field was not empty
		
				$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]entity(category,content,status,priority,owner,assignee,CRMcustomer,deleted,duedate,cdate,openepoch,sqldate) VALUES ('$a','$b','$c','$d','$e','$f','$g','$h','$i','$cdate','$openepoch','$sqldate')";

//				print $sql;
				
				mcq($sql,$db);
				$qrs++;
				
				$id_i = mysql_insert_id();

				if (!$min_id) {
					$min_id = $id_i;
				}

				for ($tmp=10;$tmp<$func_max;$tmp++) {
					// Import extra fields
					
					if (!$import[$tmp]=="") {
						$c = $tmp + $tel;
						$field = $list[$tmp-10];
						$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]customaddons(eid,type,name,value) VALUES('" . $id_i ."','','" . $field['id'] . "','" . ereg_replace("\[CRLF\]","\n",$import[$c]) . "')";
						mcq($sql,$db);
						$qrs++;
						//print $sql . "<br>";
					}
				}
				
				journal($id_i,"Entity created (automated import)","");


				$imported++;
			} else {
				$skipped++;
			}

		}
		
		echo"<tr><td>";

		log_msg("Entity table imported ($imported imported, $skipped skipped, min_EID $min_id, max_EID $id_i)","");

		//echo $tmp;		
		echo "<br>";
		//eval($lang[imported]);
		if ($skipped>=1) {
			print "$skipped $lang[ignored]";
		}
		print "<br>Imported entity $min_id to $id_i.";
		print "<br>" . $qrs . " queries executed";
		print "<br><br><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password' style='cursor:pointer'>Back to main administration page</a>";
		print "<tr><td>";
		
		//menu(justhome,1);
		EndHTML();
		exit;



}

if ($dothefuckingjob2==1) {
		
		// Okee, import[0-(numofrecords*8)] hebben we nu.
		$list = GetExtraCustomerFields();

			if ($DELETEFIRST=="confirmed") {
					$tmp = "Current customer list emtied (including extra fields)";
					$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]customer";
					mcq($sql,$db);
					$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE type='cust'";
					mcq($sql,$db);
					log_msg("Customer table emptied (including extra fields)","");
			}

		for ($tel=0;$tel<=($numofrecords*$func_max);$tel=$tel+$func_max) {
	
	//		Onderstaande omslachtige routine is de enige manier waarop ik het aan de praat kreeg.
	//		De array ( $import[point] ) opnemen in de SQL-query werkte niet in eerste instantie.
	//		Ik heb nu de query 'los', dwz eerst een $sql met de query vullen en dan pas uitvoeren.
	//		Wellicht zou ik de array nu wel in de $sql= op kunnen nemen maar daar heb ik nu geen 
	//		zin meer in.

			$a = $import[$tel];
			$b = $import[$tel+1];
			$c = $import[$tel+2];
			$d = $import[$tel+3];
			$e = $import[$tel+4];
			$f = $import[$tel+5];
			$g = $import[$tel+6];
			$h = $import[$tel+7];
			
			if (strlen($a)>0) {			// When the first field was not empty
				$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]customer (custname,contact,contact_title,contact_phone,contact_email,cust_address,cust_remarks,cust_homepage) VALUES ('$a','$b','$c','$d','$e','$f','$g','$h')";
				mcq($sql,$db);
				$qrs++;
				
				$id_i = mysql_insert_id();

				for ($tmp=8;$tmp<$func_max;$tmp++) {
					// Import extra fields
					
					if (!$import[$tmp]=="") {
						$c = $tmp + $tel;
						$field = $list[$tmp-8];
						$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]customaddons(eid,type,name,value) VALUES('" . $id_i ."','cust','" . $field['id'] . "','" . $import[$c] . "')";
						mcq($sql,$db);
						$qrs++;
						//print $sql . "<br>";
					}
				}
				
				journal($id_i,"Customer added (automated import)","customer");


				$imported++;
			} else {
				$skipped++;
			}

		}
		
		echo"<tr><td>";

		log_msg("Customer table imported ($imported imported, $skipped skipped","");

		//echo $tmp;		
		echo "<br>";
		eval($lang[imported]);
		print "<br>" . $qrs . " queries executed";
		if ($skipped>=1) {
			print "$skipped $lang[ignored]";
		}
		print "<br><br><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password' style='cursor:pointer'>Back to main administration page</a>";
		print "<tr><td>";
		
		//menu(justhome,1);

		exit;
		}


if ($dothefuckingjob==1) {
		
		// Okee, import[0-(numofrecords*8)] hebben we nu.


			if ($DELETEFIRST=="confirmed") {
					$tmp = "Old phone book emtied";
					$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]phonebook";
					mcq($sql,$db);
					log_msg("Phonebook emptied","");
			}

		for ($tel=0;$tel<=($numofrecords*8);$tel=$tel+8) {
	
	//		Onderstaande omslachtige routine is de enige manier waarop ik het aan de praat kreeg.
	//		De array ( $import[point] ) opnemen in de SQL-query werkte niet in eerste instantie.
	//		Ik heb nu de query 'los', dwz eerst een $sql met de query vullen en dan pas uitvoeren.
	//		Wellicht zou ik de array nu wel in de $sql= op kunnen nemen maar daar heb ik nu geen 
	//		zin meer in.

			$a = $import[$tel];
			$b = $import[$tel+1];
			$c = $import[$tel+2];
			$d = $import[$tel+3];
			$e = $import[$tel+4];
			$f = $import[$tel+5];
			$g = $import[$tel+6];
			$h = $import[$tel+7];
			
			if (strlen($a)>0) {			// Oftewel als de naam is ingevuld
				$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]phonebook (Firstname,Lastname,Telephone,Company,Department,Title,Room,Email) VALUES ('$a','$b','$c','$d','$e','$f','$g','$h')";
				mcq($sql,$db);
				$imported++;
			} else {
			$skipped++;
			}

		}
		
		echo"<tr><td>";
		echo eval($lang[imported]);
		log_msg("Phone book imported ($imported imported, $skipped skipped","");
		echo "<br>";
		printbox($tmp);		
		if ($skipped>1) {
			printbox("$skipped $lang[ignored]");
		}
		print "<tr><td>";
		
		//menu(justhome,1);

		exit;
		}


if ($_REQUEST['manageres']) {
//		print "</table></table>";
		MainAdminTabs();
		$to_tabs = array("Current repositories","New repository");
		$tabbs["main"] = array("admin.php" => "<b>Back</b>", "comment" => "bla");
		$tabbs["Current repositories"] = array("admin.php?reposman=1&resman=1&manageres=1&1156969438" => "Current repositories", "comment" => "View the list of currently configured repositories");
		$tabbs["New repository"] = array("install.php?AddRepository=1&step=1" => "New repository", "comment" => "Create a shiny new repository");	
		$navid = "Current repositories";
		InterTabs($to_tabs, $tabbs, $navid);

		print "<table width=100%><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><table>";

		MustBeAdmin();
		
		print "<td><b>Repository management</b></td>";
		print "<td>&nbsp;</td></tr>";
		print "<tr><td>In this section you can add and remove your CRM repositories as well as edit your extra<br> fields of all repositories where you ($name) have an <b>admin</b> account with the same password as in this repository.<br><br>Please note that when you run several repositories in one database, the displayed size will be the sum of all repository sizes!</td></tr>";
		//print "<tr><td><br><img src='arrow.gif'>&nbsp;<a href='install.php?AddRepository=1&step=1' class='bigsort'>add a repository</a>&nbsp;<img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?3=1' style='cursor:pointer'>Back to main administration page</a></td></tr>";


		print "<tr><td colspan=3><table class='crm4' width='100%'>";

		print "<tr><td><B>ResId</B></td><td><B>Host</B></td><td><B>Database</B></td><td><B>Table prefix</B></td><td><b>Repository title</b></td><td><b>Status</b></td><td><b>Size</b></td><td><b>DB version</b></td><td><b>Entities</b></td><td><b>Sessions</b></td><td><b>Total records</b></td><td><B>Fail-over</B></td><td><B>Maintenance mode</B></td><td><b></b></td><td><b></b></td><td><b></b></td><td><b></b></td></tr>";
	// Get all possible CRM repository titles from all possible databases

		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$curpassword = $result1[password];

		for ($r=0;$r<64;$r++) {
		if ($host[$r]) {
			if ($db = @mysql_connect($host[$r], $user[$r], $pass[$r])) {
					if (@mysql_select_db($database[$r],$db)) {
							// all ok
					} else {
						if ($slave[$r]) {
							$tmp = $host[$r];
							$host[$r] = $slave[$r];
							$slave[$r] = $tmp;
						} else {
							$host[$r] = $host[$r];
						}
					}
			} else {
				if ($slave[$r]) {
					$tmp = $host[$r];
					$host[$r] = $slave[$r];
					$slave[$r] = $tmp;
				} else {
					$host[$r] = $host[$r];
				}
				
			}
		}
		}


		if (sizeof($pass)>0) {
				for ($r=0;$r<64;$r++) {
					if ($user[$r]) {
						if ($db = @mysql_connect($host[$r], $user[$r], $pass[$r])) {
							if (@mysql_select_db($database[$r],$db)) {
								$tbl = $table_prefix[$r];
								if ($tbl=="") $tbl="CRM";
								$sql = "SELECT password FROM " . $tbl . "loginusers WHERE name='$name' AND administrator='Yes'";
//								$result= mcq($sql,$db);
								$result = @mysql_query($sql);	
								$result1= @mysql_fetch_array($result);
								$foreignpassword = $result1[password];

								unset($on_remote);
								unset($on_local);
								unset($size);
								unset($DBVERSION);

								if ($curpassword==$foreignpassword) {
									
									unset($tot_ent);

									$sql = "SELECT value FROM " . $tbl . "settings WHERE setting='title'";
									$result= mcq($sql,$db);
									$maxU1 = @mysql_fetch_array($result);
									$title = $maxU1[0];

									$sql = "SELECT value FROM " . $tbl . "settings WHERE setting='DBVERSION'";
									$result= mcq($sql,$db);
									$maxU1 = @mysql_fetch_array($result);
									$DBVERSION = $maxU1[0];

									$sql = "SELECT value FROM " . $tbl . "settings WHERE setting='MAINTENANCE_MODE'";
									$result= mcq($sql,$db);
									$maxU1 = @mysql_fetch_array($result);
									$maintenance_mode = $maxU1[0];
									
									$sql = "SELECT COUNT(*) FROM " . $tbl . "entity";
									$result= mcq($sql,$db);
									$res = @mysql_fetch_array($result);
									$enum = $res[0];
									
									$tot_ent += $enum;
	
									$sql = "SELECT COUNT(*) FROM " . $tbl . "sessions";
									$result= mcq($sql,$db);
									$res = mysql_fetch_array($result);
									$sess = $res[0];	

									$sql = "SHOW TABLE STATUS";
									$result= mcq($sql,$db);

									while ($stat = @mysql_fetch_array($result))
									{
										$size += $stat["Data_length"];
										$size += $stat["Index_lenght"];

									}			
									
									$tot_size += (($size/1024)/1024);
									$size = ceil((($size/1024)/1024)) . " MB";

									if ($DBVERSION=="") {
										$DBVERSION="Prior to 1.9.0";
									}

									if ($DBVERSION == $GLOBALS['VERSION']) {
										$DBVERSION = "<span style='background-color: #33FF00'>" . $DBVERSION . "</span>";
									} elseif ($DBVERSION > $GLOBALS['VERSION']) {
									} elseif ($DBVERSION < $GLOBALS['VERSION']) {
										$DBVERSION = "<span style='background-color: #FF9966'>" . $DBVERSION . "</span>";
									}

									$bla = CountTotalNumOfRecords($tbl);
									if ($GLOBALS['FAULT']) {
										$ins = "<font color='#FF0000'>ERROR</font><br>" . $GLOBALS['FAULT'];
										unset($GLOBALS['FAULT']);
									} else {
										$ins = "<font color='#33FF66'>OK</font>";
									}

									print "<tr><td>$r</td><td>" . $host[$r] . "</td><td>$database[$r]</td><td>$table_prefix[$r]</td><td>$title</td><td>" . $ins . "</td><td>" . $size . "</td><td>$DBVERSION</td><td>$enum</td><td>$sess</td>";
									unset($size);
									
									if ($bla) {
										print "<td>" . number_format($bla) . "</td>";
										$tot_rec = $tot_rec + $bla;
									} else {
										print "<td>n/a</td>";
									}

									print "<td>";
									if ($slave[$r]) {

										$db = @mysql_connect($slave[$r], $user[$r], $pass[$r]);
										if (@mysql_select_db($database[$r],$db)) {
											$sql = "SELECT eid, category FROM " . $table_prefix[$r] . "entity ORDER BY eid DESC LIMIT 20";
											$res = mcq($sql, $db);
											unset($string1);
											unset($string2);

											while ($row = @mysql_fetch_array($res)) {
												$string1 .= $row['eid'] . $row['category'];
											}

											$num = db_GetRow("SELECT COUNT(*) FROM " . $table_prefix[$r] . "failoverquerystore WHERE targethost ='" . $host[$r] . "'");
											$on_remote = $num[0];
										
											$db = @mysql_connect($host[$r],  $user[$r], $pass[$r]);
											@mysql_select_db($database[$r],$db);
											$sql = "SELECT eid, category FROM " . $table_prefix[$r] . "entity ORDER BY eid DESC LIMIT 20";
											$res = mcq($sql, $db);
											while ($row = @mysql_fetch_array($res)) {
												$string2 .= $row['eid'] . $row['category'];
											}
											$num = db_GetRow("SELECT COUNT(*) FROM " . $table_prefix[$r] . "failoverquerystore WHERE targethost ='" . $slave[$r] . "'");
											$on_local = $num[0];
											
											
											if ($string1 <> $string2) {
												print "<font color='#6666FF'>Compromised</font> ";
											} else {
												if ($on_local <> 0 && $on_remote<>0) {
													print "<font color='#6666FF'>SYNC HALTED</font> ";
												} else {
													print "<font color='#66CC00'>OK</font> ";
												}
											}
											print "Jobs local: " . $on_local . " rem. " . $on_remote;


										} else {
											$db = @mysql_connect($host[$r],  $user[$r], $pass[$r]);
											@mysql_select_db($database[$r],$db);
											print "<font color='#FF0000'>Down or unselectable</font> ";
											$num = db_GetRow("SELECT COUNT(*) FROM " . $table_prefix[$r] . "failoverquerystore WHERE targethost ='" . $slave[$r] . "'");
											$on_local = $num[0];
											print "Jobs local: " . $on_local . "";
										}

										@mysql_close($db);
										
										$db = mysql_pconnect($GLOBALS['DBHOST'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
										@mysql_select_db($GLOBALS['DBNAME'],$db);
									
									} else {
										print "No";
									}
									print "</td>";
									
									if ($maintenance_mode == "Yes") {
										print "<td><font color='#FF9900'>" . $maintenance_mode . "</font></td>";
									} else {
										print "<td><font color='#66CC00'>" . $maintenance_mode . "</font></td>";
									}	

									
									print "<td " . PrintToolTipCode("View table status") . "><a href='admin.php?ts=1&daba=" . ($r+1) . "&nonavbar=1' class='bigsort' target='_new'><img src='table.png' style='border: 0'></a></td><td " . PrintToolTipCode("Check (and delete) session records") . "><a href='admin.php?sessionscheck=1&daba=" . ($r+1) . "' class='bigsort'><img src='sessions.png' style='border: 0'></a>&nbsp;</td><td " . PrintToolTipCode("Check repository configuration") . "><a href='admin.php?checkcfg=1&daba=" . ($r+1) . "' class='bigsort'><img src='config.png' style='border: 0'></a>&nbsp;</td><td " . PrintToolTipCode("Wipe out this repository") . "><a href='admin.php?deldb=1&daba=" . ($r+1) . "' class='bigsort'><img src='delete.gif' style='border: 0'></a></td>";
									
									print "</tr>";
								} else {
									print "<tr><td>$r</td><td>$host[$r]</td><td>$database[$r]</td><td>$tbl</td><td><font color='#FF3300'>Access denied or non-existent</font></td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td></tr>";
								}
							} else {
								print "<tr><td>$r</td><td>$host[$r]</td><td>$database[$r]</td><td>n/a</td><td><font color='#FF3300'>Couldn't select database</font></td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td></tr>";
							}

						} else {
							 print "<tr><td>$r</td><td>$host[$r]</td><td>$database[$r]</td><td>n/a</td><td><font color='#FF3300'>Database host onreachable</font></td><td></td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td></tr>";
						}
					
						}
					}
					?>
			</select>
				&nbsp;&nbsp;&nbsp;
			  
            
			<?
				}
	
		print "</tr>";
		print "<br>Total entities: " . number_format($tot_ent) . " - Total records: " . number_format($tot_rec) . " - Total size: " . ceil($tot_size) . " MB<br><br>";
		print "</table></td></tr></table>";
//		log_msg("Repository management section accessed","");
		EndHTML();
		exit;
}

if ($importbody) {
		$importbody = ereg_replace("\'","",$importbody);
		$importbody = ereg_replace("\"","",$importbody);
		$importbody = explode("\n",$importbody);
//		$bericht = ereg_replace("\'","", $bericht);


		$x=0;
		?>
		<table>
		<tr><td colspan=10>
		<? echo $lang[impwarning];?><br></td></tr>
		<? 
		if ($separator==",") {
			echo "<tr><td colspan=5>$lang[commawarning]</td></tr>"; 
		} 
		?>
		<form name="controle" method="post">	
		<?
		print "<tr><td>$lang[fname]<td>$lang[lname]<td>$lang[tel]<td>$lang[company]<td>$lang[dep]<td>$lang[title]<td>$lang[room]<td>E-mail</tr>";
		
		while ((strlen($importbody[$x])>3)) {
				
				$a = explode("$separator",$importbody[$x]);

			?>
			<tr>
			<td><input type="text" name="import[]" value="<?echo $a[0];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[1];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[2];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[3];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[4];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[5];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[6];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[7];?>"></td>
			</tr>			
			<?



		$x++;
	}	
		if ($DELETEFIRST) {
				$DELETEFIRST = "confirmed";
		}
		
		?>
		<INPUT TYPE="hidden" NAME="numofrecords" value="<?echo ($x-1);?>">
		<INPUT TYPE="hidden" NAME="dothefuckingjob" VALUE="1">
		<INPUT TYPE="hidden" NAME="DELETEFIRST" VALUE="<? echo $DELETEFIRST; ?>">
		<input type="hidden" name="actuser" value="<?echo $username;?>">
		<input type="hidden" name="session" value="<?echo $session;?>">

		<tr><td>	<INPUT TYPE="submit" NAME="importbutton2" value="Import!">
		</table>
		</form>
		</body>
		</html>
		<?
		exit;
	}

if ($importbodycustomers) {
		$importbody = ereg_replace("\'","",$importbodycustomers);
		$importbody = ereg_replace("\\\"","",stripslashes($importbody));
		
//		$importbody = $importbodycustomers;
		unset($importbodycustomers);

		if (stristr($importbody,"@@@@CRM@@@@")) {
			$importbody = explode("@@@@CRM@@@@",$importbody);
			print "- importing a CRM-specific list of " . strtolower($lang[customers]) . ".<br>";
		} else {
			$importbody = explode("\n",$importbody);
		print "- importing a generic list of " . strtolower($lang[customers]) . ". Enters (line-breaks) in comments or addresses will cause errors.<br>";
		}

		
//		$bericht = ereg_replace("\'","", $bericht);


		$x=0;
		?>
		<table>
		<tr><td colspan=10>
		<? echo $lang[impwarning];?><br></td></tr>
		<? 
		if ($separator==",") {
			echo "<tr><td colspan=5>$lang[commawarning]</td></tr>"; 
		} 		
		$list = GetExtraCustomerFields();
		?>
		<form name="controle" method="post">	
		<?
		print "<tr><td>$lang[customer]</td><td>$lang[contact]</td><td>$lang[contacttitle]</td><td>$lang[contactphone]</td><td>$lang[contactemail]</td><td>$lang[customeraddress]</td><td>$lang[custremarks]</td><td>$lang[custhomepage]</td>";
		foreach ($list as $item) {
			print "<td>" . $item['name'] . "</td>";
		}
		print "</tr>";

		

		while ((strlen($importbody[$x])>3)) {
				
			
			$a = explode($separator,$importbody[$x]);
			

			?>
			<tr>
			<td><input type="text" name="import[]" value="<?echo $a[0];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[1];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[2];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[3];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[4];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[5];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[6];?>"></td>	
			<td><input type="text" name="import[]" value="<?echo $a[7];?>"></td>
				
			<?
			for ($tmp=8;$tmp<$func_max;$tmp++) {
				if ($a[$tmp]) {
					print "<td><input type='text' name='import[]' value='" . $a[$tmp] . "'></td>";
				} else { 
					print "<td><input type='text' name='import[]' value=''></td>"; 
				}
			}

			print "</tr>";


		$x++;
	}	
		if ($DELETEFIRST) {
				$DELETEFIRST = "confirmed";
		}
		
		?>
		<INPUT TYPE="hidden" NAME="func_max" value="<?echo $func_max;?>">
		<INPUT TYPE="hidden" NAME="numofrecords" value="<?echo ($x-1);?>">
		<INPUT TYPE="hidden" NAME="dothefuckingjob2" VALUE="1">
		<INPUT TYPE="hidden" NAME="DELETEFIRST" VALUE="<? echo $DELETEFIRST; ?>">
		<input type="hidden" name="actuser" value="<?echo $username;?>">
		<input type="hidden" name="session" value="<?echo $session;?>">

		<tr><td><INPUT TYPE="submit" NAME="importbutton2" value="Import!">
		</table>
		</form>
		</body>
		</html>
		<?
		exit;
	}

if ($importcust) {

			$list = GetExtraCustomerFields();

			$func_max = 9 + sizeof($list);

			print "<tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;CRM-specific export (CSV with lines terminated by CRM-specific line ends)&nbsp;</legend><table>";
			print "<tr><td></td></tr>";	
			print "<tr><td colspan=12><img src='arrow.gif'>&nbsp;<a href='csv.php?password=$password&exportcust=1&sep=;' class='bigsort'> download " . strtolower($lang[customers]) . " CSV export semicolon (;) quoted-printable</a></td></tr>";
			print "<tr><td colspan=12><img src='arrow.gif'>&nbsp;<a href='csv.php?password=$password&exportcust=1&sep=:' class='bigsort'> download " . strtolower($lang[customers]) . " CSV export colon (:) quoted-printable</a></td></tr>";
			print "<tr><td colspan=12><img src='arrow.gif'>&nbsp;<a href='csv.php?password=$password&exportcust=1&sep=,' class='bigsort'> download " . strtolower($lang[customers]) . " CSV export comma (,) quoted-printable</a></td></tr>";

//			print "<img src='arrow.gif'>&nbsp;<a href='csv.php?exportlog=1&sep=RealExcel' class='bigsort'> Microsoft&reg; Excel&reg; format</a><br>";

			print "<tr><td colspan=12></td></tr></table></fieldset></td></tr>";
			
			print "<tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;CSV export (for MS Excel)&nbsp;</legend><table>";

			print "<tr><td colspan=12><img src='arrow.gif'>&nbsp;<a href='csv.php?password=$password&exportcust=1&nle=1&sep=;' class='bigsort'> download " . strtolower($lang[customers]) . " CSV export semicolon (;) quoted-printable</a></td></tr>";
			print "<tr><td colspan=12><img src='arrow.gif'>&nbsp;<a href='csv.php?password=$password&exportcust=1&nle=1&sep=:' class='bigsort'> download " . strtolower($lang[customers]) . " CSV export colon (:) quoted-printable</a></td></tr>";
			print "<tr><td colspan=12><img src='arrow.gif'>&nbsp;<a href='csv.php?password=$password&exportcust=1&nle=1&sep=,' class='bigsort'> download " . strtolower($lang[customers]) . " CSV export comma (,) quoted-printable</a></td></tr>";
			print "<tr><td colspan=12><img src='arrow.gif'>&nbsp;<a href='csv.php?password=$password&exportcust=1&sep=RealExcel' class='bigsort'> download " . strtolower($lang[customers]) . " in Microsoft&reg; Excel&reg; format</a></td></tr>";

			print "<tr><td colspan=12></td></tr></table></fieldset></td></tr>";
			print "<tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Import " . strtolower($lang[customers]) ."&nbsp;</legend><table>";
	
			print "<tr><td>$lang[pbimpwarning]<BR><BR></td></tr>";
			print "<tr><td>If you paste the contents of a CRM-" . strtolower($lang[customers]) . "-export file in the box below, CRM can handle<br>line breaks. If you paste in a standard list of seperated values, be sure that one line is one entry.<br>CRM will detect the data type automatically.<br><br>";
			print "<pre>$lang[customer];$lang[contact];$lang[contacttitle];$lang[contactphone];$lang[contactemail];$lang[customeraddress];$lang[custremarks];$lang[custhomepage]";

			foreach($list as $item) {
				print ";" . $item['name'] . "";
			}

			print "</pre><BR><BR>After clicking 'Import' an overview will be presented to check your data before you actually import it into the database.<br><br>";
			?>
			<form name="importcustomers" method="post">
			<INPUT NAME="separator" CLASS="radio" TYPE="radio" CHECKED VALUE=";">  <? echo $lang[scqp];?><BR>
			<INPUT NAME="separator" CLASS="radio" TYPE="radio" VALUE=":"> <? echo $lang[cqp];?><BR>
			<INPUT NAME="separator" CLASS="radio" TYPE="radio" VALUE=","> <? echo $lang[kqp];?><BR><br>
			<INPUT NAME="DELETEFIRST" CLASS="radio" VALUE="1" TYPE="checkbox"> Check to empty <? $lang[customers];?> table before import (be careful!)<BR><br>
			<textarea rows=3 cols=100 name='importbodycustomers'><? echo$lang[paste];?></textarea><BR>
			<br>
			<INPUT TYPE="hidden" NAME="func_max" value="<? echo $func_max;?>">
			<INPUT TYPE="submit" NAME="importbutton" value="Import!">
			</form>
			</td></tr></table></fieldset><br>
			<img src='arrow.gif'>&nbsp;<a class='bigsort' href='javascript:history.back(1);' style='cursor:pointer'>Back to main administration page</a>
			</td></tr></table>
			</body>
			</html>
			<?

			exit;

}
if ($importbodyentity) {
		$importbody = ereg_replace("\'","",$importbodyentity);
		$importbody = ereg_replace("\\\"","",stripslashes($importbody));
		
//		$importbody = $importbodycustomers;
		unset($importbodycustomers);

		if (stristr($importbody,"@@@@CRM@@@@")) {
			$importbody = explode("@@@@CRM@@@@",$importbody);
			print "- importing a CRM-specific list of " . strtolower($lang[customers]) . ".<br>";
		} else {
			$importbody = explode("\n",$importbody);
			print "- importing a generic list of " . strtolower($lang[entities]) . ". Enters (line-breaks) in comments or addresses will cause errors.<br>";
		}

		
//		$bericht = ereg_replace("\'","", $bericht);


		$x=0;
		?>
		<table>
		<tr><td colspan=10>
		<? echo $lang[impwarning];?><br></td></tr>
		<? 
		if ($separator==",") {
			echo "<tr><td colspan=5>$lang[commawarning]</td></tr>"; 
		} 

		$list = GetExtraFields();
		?>
		<form name="controle" method="post">	
		<?
		print "<TR><TD>category</TD><TD>content</TD><TD>status</TD><TD>priority</TD><TD>owner</TD><TD>assignee</TD><TD>CRMcustomer</TD><TD>deleted</TD><TD>duedate</TD><TD>creation date</TD>";
		foreach ($list as $item) {
			print "<td>" . $item['name'] . "</td>";
		}
		print "</tr>";

		

		while ((strlen($importbody[$x])>3)) {
				
			
			$a = explode($separator,$importbody[$x]);
			
			
			
			$rule  = "<tr>";
			$rule .= "<td><input type='text' name='import[]' value='" . $a[0] . "'></td>";
			$rule .= "<td><input type='text' name='import[]' value='" . $a[1] . "'></td>";
			$rule .= "<td><input type='text' name='import[]' value='" . $a[2] . "'></td>";	
			$rule .= "<td><input type='text' name='import[]' value='" . $a[3] . "'></td>";	
			$rule .= "<td><input type='text' name='import[]' value='" . $a[4] . "'></td>";	
			$rule .= "<td><input type='text' name='import[]' value='" . $a[5] . "'></td>";	
			$rule .= "<td><input type='text' name='import[]' value='" . $a[6] . "'></td>";	
			$rule .= "<td><input type='text' name='import[]' value='" . $a[7] . "'></td>";
			$rule .= "<td><input type='text' name='import[]' value='" . $a[8] . "'></td>";
			$rule .= "<td><input type='text' name='import[]' value='" . $a[9] . "'></td>";
			
			
			for ($tmp=10;$tmp<$func_max;$tmp++) {
				$rule .= "<td><input type='text' name='import[]' value='" . $a[$tmp] . "'></td>";
			}
			$rule .= "</tr>";

			if (is_numeric($a[4]) && is_numeric($a[5]) && is_numeric($a[6])) {
				print $rule;
			} else {
				print "<tr><td colspan=200>Rule " . ($x+1) . " skipped - owner, assignee or customer not entered as numeric value</td></tr>";
			}

		$x++;
	}	
		if ($DELETEFIRST) {
				$DELETEFIRST = "confirmed";
		}
		
		?>
		<INPUT TYPE="hidden" NAME="func_max" value="<?echo $func_max;?>">
		<INPUT TYPE="hidden" NAME="numofrecords" value="<?echo ($x-1);?>">
		<INPUT TYPE="hidden" NAME="dothefuckingjob_entities" VALUE="1">
		<INPUT TYPE="hidden" NAME="DELETEFIRST" VALUE="<? echo $DELETEFIRST; ?>">
		<input type="hidden" name="actuser" value="<?echo $username;?>">
		<input type="hidden" name="session" value="<?echo $session;?>">

		<tr><td><INPUT TYPE="submit" NAME="importbutton2" value="Import!">
		</table>
		</form>
		</body>
		</html>
		<?
		exit;
	}
if ($importentity) {

			
			$list = GetExtraFields();

			$func_max = 10 + sizeof($list);

			
			print "<tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Import " . strtolower($lang[entities]) ."&nbsp;</legend><table>";
	
			print "<tr><td>$lang[pbimpwarning]<BR><BR></td></tr>";
			print "<tr><td>This is the layout which <u>must</u> be used:";
			print "<pre>category;content;status;priority;owner;assignee;CRMcustomer;deleted;duedate;creation_date";

			foreach($list as $item) {
				print ";" . $item['name'];
			}

			print "</pre>";
			?>
			Some rules:<br>
			<ul>
				<li>All dates must be in format DD-MM-YYYY (even if your setting is MM-DD-YYYY)</li>
				<li>Owner and Assignee must be in numeric format (referencing to user id's in the loginusers table)</li>
				<li>Customer must be in numeric format (referencing to customer id's in the customer table)</li>
				<li>If you leave the creation_date field empty, it will be set to today</li>
				<li>Make sure a field value doesn't contain a semicolon (or : or , - whatever you choose as separator)!</li>
				<li>Make sure a field value doesn't contain a line break! If you have text fields containing line breaks, replace them with "<b>[CRLF]</b>". When importing, CRM will convert the text [CRLF] back to a line break.</li>
				<li>To be really sure that separation is done well, use the @REALLYSAFE@ separator instead of a single character like a semicolon.</li>
				<li>No reference checking or data validation is performed - in other words, you can import as much bogus data if you want as long as the owner, assignee and customer fields are numeric.</li>

			</ul>
			<?
			print "<BR>After clicking 'Import' an overview will be presented to check your data before you actually import it into the database.<br><br>";
			?>
			<form name="importentities" method="post">
			<INPUT NAME="separator" CLASS="radio" TYPE="radio" CHECKED VALUE=";">  <? echo $lang[scqp];?><BR>
			<INPUT NAME="separator" CLASS="radio" TYPE="radio" VALUE="@REALLYSAFE@">@REALLYSAFE@<BR>
			<INPUT NAME="separator" CLASS="radio" TYPE="radio" VALUE=":"> <? echo $lang[cqp];?><BR>
			<INPUT NAME="separator" CLASS="radio" TYPE="radio" VALUE=","> <? echo $lang[kqp];?><BR><br>

			<INPUT NAME="DELETEFIRST" CLASS="radio" VALUE="1" TYPE="checkbox"> Check to empty <?echo strtolower($lang['entities']);?> table before import (no further confirmation, <font color='#FF0000'>be careful</font>!)<BR><br>
			<textarea rows=3 cols=100 name='importbodyentity'><? echo $lang[paste];?></textarea><BR>
			<br>
			<INPUT TYPE="hidden" NAME="func_max" value="<? echo $func_max;?>">
			<INPUT TYPE="submit" NAME="importbutton" value="Import!">
			</form>
			</td></tr></table></fieldset><br>
			<img src='arrow.gif'>&nbsp;<a class='bigsort' href='javascript:history.back(1);' style='cursor:pointer'>Back to main administration page</a>
			</td></tr></table>
			</body>
			</html>
			<?

			exit;

}
if ($importphone) {
			print "<table><tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[importinpb]</legend><table>";
			print "<tr><td nowrap>$lang[pbimpwarning]<BR><BR>";
			print "$lang[fname];$lang[lname];$lang[tel];$lang[company];$lang[dep];$lang[title];$lang[room];E-mail";
			print "<BR><BR>";
			?>
			<form name="importadresboek" method="post">
			<INPUT NAME="separator" CLASS="radio" TYPE="radio" CHECKED VALUE=";">  <? echo $lang[scqp];?><BR>
			<INPUT NAME="separator" CLASS="radio" TYPE="radio" VALUE=":"> <? echo $lang[cqp];?><BR>
			<INPUT NAME="separator" CLASS="radio" TYPE="radio" VALUE=","> <? echo $lang[kqp];?><BR><br>
			<INPUT NAME="DELETEFIRST" CLASS="radio" VALUE="1" TYPE="checkbox"> Check to empty phonebook before import <BR><br>
			<textarea rows=3 cols=60 name='importbody'><? echo$lang[paste];?></textarea>
			<INPUT TYPE="submit" NAME="importbutton" value="Import!">
			</form>
			</td></tr></table></fieldset>
			<br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='javascript:history.back(1);' style='cursor:pointer'>Back to main administration page</a></td>
			</td></tr></table>
			</body>
			</html>
			<?

			exit;

}

if (!$_FILES['userfile']['tmp_name']=="" && !$_FILES['userfile']['name']=="" && !$_FILES['userfile']['size']=="" && !$_FILES['userfile']['type']=="" && $importentities) {
		
		//  A file was attached

		
	
		// Read contents of uploaded file into variable
					$fp=fopen($_FILES['userfile']['tmp_name'] ,"rb");
					
					//$filecontent=fread($fp,filesize($_FILES['userfile']['tmp_name'] ));
					while (!feof($fp)) {
						$a = fgets($fp,1600000);
						$q = base64_decode($a);
						if (!$q=="") {
							mcq($q,$db);
						}
						unset($q);
					}
					fclose($fp);
					print filesize($_FILES['userfile']['tmp_name'] );




					print "<tr><td>$count queries where successfully imported, $skip were skipped</td></tr>";
					print "<tr><td><img src='arrow.gif'>&nbsp;<a class='bigsort' href='javascript:history.back(1);' style='cursor:pointer'>Back to main administration page</a></td></tr>";
					print "</table>";
EndHTML();
					exit;

}
if ($importentities) {
			print "disabled";
			/*
			MustBeAdmin();

			$legend = "Export current database (" . $database[$repository_nr] .")&nbsp;";
			$a  = "<br>To download a CRM-specific export, use the encrypted download. This file can only be read by CRM. If your database is too large to upload and import it with CRM, use the plain dowload to create a plain-text standard SQL file which can be imported into MySQL using 'mysqlimport' or similar tools.<br><br>Please remember that when downloading very large databases things like your PHP's MAX_EXECUTION_TIME vars and your MySQL installation's variables can cause errors.<br>";
			$a .= "<br><img src='arrow.gif'>&nbsp;<a href='export.php?encrypt=1' class='bigsort'>Download encrypted database dump</a>";
			$a .= "<br><br><img src='arrow.gif'>&nbsp;<a href='export.php' class='bigsort'>Download plain-text database dump</a>";
			
			printbox($a);
			
			$legend = "Import complete encrypted CRM database dump&nbsp;";
			$a = "<br><font color='#FF0000'>Warning!</font> Importing a database dump will overwrite ALL DATA in your current working database (" . $database[$repository_nr] . "). Be careful! Only use this when you have only 1 repository in this database. <br>You will <b>not</b> be asked for confirmation after uploading the file.<br><br><form ACTION='admin.php' name='ImportEntities' method='POST' ENCTYPE='multipart/form-data' id='dashed'>";
			$a .= "<INPUT TYPE='hidden' name='MAX_FILE_SIZE' value='1552428800'><img src='attach.gif'>&nbsp;&nbsp;Select CRM export file: <INPUT NAME='userfile' TYPE='file'><br><br><input type='submit' value='Import'><input type='hidden' name='importentities' value='1'>";
			printbox($a);
			print "<img src='arrow.gif'>&nbsp;<a href='admin.php?$epoch' class='bigsort'>Back to main administration page</a>";
			*/

			exit;

}

	
	if (!$sysval && !$fysdelete) {
	
	$daba = $repository_nr + 1;

	/////////// HIER
	
//	AdminTabs("main");
	print "</td></tr></table>";
	print "<table width=100%><tr><td>";

	MainAdminTabs();

	$toprint .= "<tr><td NOWRAP><table border=0 cellpadding=8 cellspacing=2 width=60%><tr><td></td><td NOWRAP valign='top'>";
	
	// Quadrant 1
	if ($_REQUEST['syscon']) {
		$toprint .= "<b>System configuration</b><br>";
		$toprint .= "<table border=0 cellpadding=0 cellspacing=12>";
		//$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?password=&sysval=1' class='bigsort'>Global system values</a></td></tr>";

		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='dictedit.php?password=$password&packman=1&tab=80' class='bigsort'" . PrintToolTipCode("Upload new language packs, create new packs, create new masks, translate CRM-CTT.") . ">Configure language packs</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?password=$password&reposman=1&resman=1&manageres=1' class='bigsort' " . PrintToolTipCode("Add new repositories, remove repositories, detailed database information.") . ">Configure repositories</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?ForcedFields=1' " . PrintToolTipCode("Select which standard entity fields <b>must</b> be entered. These fields are category, due-date, and the main text box.") . " class='bigsort'>Configure required entity fields (std. fields only)</a></td></tr>";
		//$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='customtabs.php?ovw=1' class='bigsort'>Configure custom navigation tabs</a></td></tr>";

		if (is_administrator()) {
			if ($_COOKIE['log_on_screen'] == "y") {
				$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a OnClick=\"javascript: setCookie('log_on_screen','n');alert('You left on-screen logging mode.');\" class='bigsort' " . PrintToolTipCode("Disable the pop-up log window.") . "style='cursor:pointer'>Disable on-screen logging mode</a></td></tr>";
			} else {
				$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a OnClick=\"javascript: setCookie('log_on_screen','y');alert('You are now in on-screen logging mode.');\" style='cursor:pointer' " . PrintToolTipCode("With on-screen logging, you will see a popup window each time you submit a page. This popup window contains the log information generated when loading the page.") . "class='bigsort'>Enable on-screen logging mode</a></td></tr>";
			}
			if ($_COOKIE['trace_on_screen'] == "y") {
				$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a OnClick=\"javascript: setCookie('trace_on_screen','n');alert('Trace link hidden.');\" class='bigsort' " . PrintToolTipCode("Hide trace link") . "style='cursor:pointer'>Hide trace link</a></td></tr>";
			} else {
				$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a OnClick=\"javascript: setCookie('trace_on_screen','y');alert('Trace link visible (only for you)');\" style='cursor:pointer' " . PrintToolTipCode("This makes the trace link visible - this link will be printed at the end of each page - clicking it will show the qlog trace (only for you)") . "class='bigsort'>Show trace link</a></td></tr>";
			}
			if ($_COOKIE['disable_triggers'] == "y") {
				$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a OnClick=\"javascript: setCookie('disable_triggers','n');alert('Triggers enabled.');\" class='bigsort' " . PrintToolTipCode("Enable triggers again.") . "style='cursor:pointer'><font color='#FF0000'><b>Enable triggers</b></font></a></td></tr>";
			} else {
				$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a OnClick=\"javascript: setCookie('disable_triggers','y');alert('Triggers are disabled for this session.');\" style='cursor:pointer' " . PrintToolTipCode("This will prevent triggers from going off. Very handy for administrative work.") . "class='bigsort'>Disable triggers for this session</a></td></tr>";
			}
		} else {
				$toprint .= "<tr><td>&nbsp;</td></tr>";
		
		}
//		$toprint .= "<tr><td>&nbsp;</td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?password=$password&EditVars=1&WhatVar=stat' class='bigsort'" . PrintToolTipCode("Edit the names and colors of the status and priority values you see when add or editing an entity.") . ">Edit status and priority values</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?checkdb=1&daba=$daba&password=$password&categoryvars=1' class='bigsort'" . PrintToolTipCode("Edit the category fields. This is only useful when you have the FORCECATEGORYPULLDOWN directive set to 'Yes'. You can do so in the Global Systems Values section.") . ">Edit categories ";
		if (strtoupper($lang[category])<>"CATEGORY") {
			$toprint .= "(" . strtolower($lang[category]) . ")";
		}
		$toprint .= "</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='choose_cols.php?dothis=global' class='bigsort'" . PrintToolTipCode("Configure which fields are chown in the main entity list and the main customer list. The user can override this (by clicking 'edit' on top of the list) as long as you don't disable the LETUSERSELECTOWNLISTLAYOUT directive in the Global System Values section.") . ">Edit lists lay-out</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?password=$password&log=1' " . PrintToolTipCode("Search the log table.") . "class='bigsort'>View log</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?password=$password&ViewJournal=1' class='bigsort'" . PrintToolTipCode("View entity journals.") . ">View journals</a></td></tr>";

		$toprint .= "</table><br></td>";

	} elseif ($_REQUEST['sysman']) {
		// Quadrant 2

//		$toprint .= "<tr><td valign=top NOWRAP>";
		$toprint .= "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<b>System management</b>&nbsp;</legend><br>";
		$toprint .= "<table border=0 cellpadding=0 cellspacing=8>";
		$toprint .= "</table><br></fieldset></td></tr>";
		
	} elseif ($_REQUEST['datman']) {
	// Quadrant 3

//		$toprint .= "<tr valign=top><td valign=top>";
		$toprint .= "<b>Data management</b><br>";
		$toprint .= "<table border=0 cellpadding=0 cellspacing=12>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?password=$password&files=1' class='bigsort'" . PrintToolTipCode("View a list of all the files in the database. These files are either templates or files attached to entities by users.") . ">View files in database</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?password=$password&deleteclosed=1' class='bigsort'" . PrintToolTipCode("Mass-delete entities. These entities will then be deleted logically, not physically.") . ">Delete a set of entities</a> </td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='index.php?password=$password&ShowEntitiesOpen=1&filter=viewdel' class='bigsort'" . PrintToolTipCode("View the list of deleted entities.") . ">View deleted entities</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?password=$password&fysdelete=1' class='bigsort'" . PrintToolTipCode("<b>Really</b> delete an entity, or a set of entities. You can only physically delete entities which are already logically deleted.") . ">Delete one or more entities physically (db cleanup)</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?password=$password&advquery=1' class='bigsort'" . PrintToolTipCode("Run a custom-made database (SQL) query. You can use the wizard, or just type the query yourself as long as you don't use DELETE, TRUNCATE or DROP.") . ">Advanced database query</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='checkdb.php?web=1' class='bigsort'" . PrintToolTipCode("This function checks your database for inconsistencies. Run it regularely!") . ">Database integrity check</a></td></tr>";
		if ($GLOBALS['EnableEntityLocking'] == "Yes") {
			$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?RemoveAllEntityLocks=1' class='bigsort'" . PrintToolTipCode("This will drop all existing entity locks. Not recommended!") . ">Remove all entity locks</a></td></tr>";
		}
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?generateentities=almost' class='bigsort' " . PrintToolTipCode("With this function you can create empty entities for every customer in your database which doesn't have an entity yet.") . ">Create an entity for each customer which doesn't have one yet</td></tr>";
		if ($GLOBALS['EnableEntityRelations'] == "Yes") {
			$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?ViewRelTree=1' class='bigsort' " . PrintToolTipCode("With this function view the total tree of entities and their relations") . ">Root entity relationship tree</a></td></tr>";
		}

		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?MassMigrateForms=1&datman=1' class='bigsort' " . PrintToolTipCode("Use this to alter the HTML-form of a lot of entities at the same time.") . ">Mass-migrate entity forms</a></td></tr>";
		
		$num = db_GetRow("SELECT COUNT(*) FROM " . $GLOBALS['TBL_PREFIX'] . "entityformcache");
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?ExpireAllFormCache=1&datman=1' class='bigsort' " . PrintToolTipCode("This will drop all build up form cache. Use this when experiencing problems with forms (displaying not the right information).") . ">Expire form cache</a> (<a href='admin.php?ShowFormCacheDetails=1&datman=1'>" . $num[0] . " forms cached</a>)</td></tr>";

		if ($_REQUEST['ShowFormCacheDetails']) {
			$toprint .=  "<table width='80%' class='crm'><tr><td colspan=3>Cache usage</td></tr><tr><td><B>User</B></td><td><B>Form</B></td><td><B>#cache records</B></td></tr>";
			$sql = "SELECT id, ADDFORMS FROM " . $GLOBALS['TBL_PREFIX'] . "loginusers";	
			$res1 = mcq($sql, $db);
			while ($row2 = mysql_fetch_array($res1)) {
				$sql = "SELECT user,formid,COUNT(*) FROM " . $GLOBALS['TBL_PREFIX'] . "entityformcache WHERE user=" . $row2['id'] . " GROUP BY formid";
				$res = mcq($sql, $db);
				while ($row = mysql_fetch_array($res)) {
					$toprint .=  "<tr><td>" . GetUserName($row[0]) . "</td><td>" . GetFileSubject($row[1]) . "</td><td>" . $row[2] . "</td></tr>";				
				}
			}
			$toprint .=  "</table>";

		}

		$toprint .= "</table><br></td></tr>";

	// Quadrant 4
	} elseif ($_REQUEST['ieb']) {
//		$toprint .= "<tr><td valign=top>";
		$toprint .= "<b>Import and export of data and settings</b><br>";
		$toprint .= "<table border=0 cellpadding=0 cellspacing=8>";

		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?password=$password&impphone=1&importphone=1' class='bigsort'" . PrintToolTipCode("Import your phone directory into CRM-CTT.") . ">$lang[impphone]</a></td></tr>";

		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?password=$password&importcust=1' class='bigsort'" . PrintToolTipCode("Import and export your customers to start (or stop) working with CRM-CTT.") . ">Import/export " . strtolower($lang['customers']) . "</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?password=$password&importentity=1' class='bigsort' " . PrintToolTipCode("Import entities......") . ">Import " . strtolower($lang['entities']) . "</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='syncdb.php?web=1' class='bigsort' " . PrintToolTipCode("Copy the customer table from another repository into this repository. This is very handy when using multiple repositories with the same ste of customers.") . ">Import " . strtolower($lang['customer']) . " database table from other repository</a></td></tr>";
	//	$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?ImportUsers=1' class='bigsort'>Import user-accounts (CRM-CTT layout, encrypted)</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?ImportCSVUsers=1&tib=users' class='bigsort'>Import user-accounts (Plain text CSV file)</a></td></tr>";
		
		$toprint .= "<tr><td>&nbsp;</td></tr>";

//		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?SendEntityList=1' class='bigsort'>Send templated e-mail to all users</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?ExportSettings=1' class='bigsort'>Export</a> or <img src='arrow.gif'>&nbsp;<a href='admin.php?ImportSettings=1' class='bigsort'>import</a> global settings table</td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?ExportExtraFields=1' class='bigsort'>Export</a> or <img src='arrow.gif'>&nbsp;<a href='admin.php?ImportExtraFields=1' class='bigsort'>import</a> extra field definitions</td></tr>";

		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?ExportUsers=1' class='bigsort'>Export</a> or <img src='arrow.gif'>&nbsp;<a href='admin.php?ImportUsers=1' class='bigsort'>import</a> user-accounts (CRM-CTT encrypted files)</td></tr>";
		$toprint .= "<tr><td>&nbsp;</td></tr>";


		//$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?ExportUsers=1' class='bigsort'>Export user-accounts (CRM-CTT layout, encrypted)</a></td></tr>";
		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='dump_to_disk.php?password=$password' class='bigsort' " . PrintToolTipCode("This will export all entities to disk. Advanced users only!") . ">Export everything to disk</a></td></tr>";



		$toprint .= "</table><br></td></tr>";	
	} elseif ($_REQUEST['actions']) {
		$toprint .= "<b>Administrative actions</b><br>";
		$toprint .= "<table border=0 cellpadding=0 cellspacing=8>";

		$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?SendAdHocEmail=1' class='bigsort'" . PrintToolTipCode("Send an e-mail message to all users including a list of their entities") . ">Send e-mail to all users (including assigned entity list).</a></td></tr>";
		if ($_REQUEST['emm']) { 
			mcq("UPDATE " . $GLOBALS['TBL_PREFIX'] . "settings SET value='Yes' WHERE setting='MAINTENANCE_MODE'", $db);
			$GLOBALS['MAINTENANCE_MODE'] = "Yes";
		} elseif ($_REQUEST['dmm']) { 
			mcq("UPDATE " . $GLOBALS['TBL_PREFIX'] . "settings SET value='No' WHERE setting='MAINTENANCE_MODE'", $db);
			$GLOBALS['MAINTENANCE_MODE'] = "No";
		}
		if ($GLOBALS['MAINTENANCE_MODE'] == "Yes") {
			$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?actions=1&dmm=1' class='bigsort'" . PrintToolTipCode("Disable` maintenance mode. Nobody but administrators will be able to log in.") . ">Disable maintenance mode (re-enables logins from non-admin users).</a></td></tr>";
		} else {
			$toprint .= "<tr><td><img src='arrow.gif'>&nbsp;<a href='admin.php?actions=1&emm=1' class='bigsort'" . PrintToolTipCode("Enable maintenance mode. Nobody but administrators will be able to log in.") . ">Enable maintenance mode (disables logins from non-admin users).</a></td></tr>";
		}

		$toprint .= "</table><br></td></tr>";	
	}

	print "<tr><td colspan=12>";
	print $toprint;
	print "</td></tr>";

	if (!$log && !$files && !$deleteclosed && !$edit1 && !$newuser && !$newpassword && !$PhysDelFileConfirmed && !$fysdelete && !$fconfirmed && !$fysdelid && !$fysdelete) {
		log_msg("Administrative section accessed","");
		$epoch = date('U');
		//ShowRepositorySwitcher("admin.php?" . $epoch);
		//print "</td><td align='right'><img src='crm.gif'>";
		print "</td></tr></table></td></tr></table>";
	} 

}
if ($chglansettings) {
		log_msg("System values saved","");

		if ($lan) {

				$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]languages WHERE TEXTID='current-language'";
				mcq($sql,$db);
				$sql= "SELECT TEXT FROM $GLOBALS[TBL_PREFIX]languages WHERE TEXTID='current-language'";
				if ($debug) { print "\nSQL: $sql\n"; }
				$result= mcq($sql,$db);
				$result= mysql_fetch_array($result);
				
				if ($result['TEXTID']=="") {
				    $sql= "INSERT INTO $GLOBALS[TBL_PREFIX]languages(LANGID,TEXTID,TEXT) VALUES('GLOBAL','current-language','$lan')";
						if ($debug) { print "\nSQL: $sql\n"; }
					mcq($sql,$db);
				    
				} else {
					$sql= "UPDATE $GLOBALS[TBL_PREFIX]languages SET TEXT='$lan' WHERE TEXTID='current-language'";
						if ($debug) { print "\nSQL: $sql\n"; }
					mcq($sql,$db);
				}
		}
		if ($lanmask) {

				$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]languages WHERE TEXTID='current-language-mask'";
				mcq($sql,$db);
				$sql= "SELECT TEXT FROM $GLOBALS[TBL_PREFIX]languages WHERE TEXTID='current-language-mask'";
				if ($debug) { print "\nSQL: $sql\n"; }
				$result= mcq($sql,$db);
				$result= mysql_fetch_array($result);
				
				if ($result[TEXTID]=="") {
				    $sql= "INSERT INTO $GLOBALS[TBL_PREFIX]languages(LANGID,TEXTID,TEXT) VALUES('GLOBAL','current-language-mask','$lanmask')";
						if ($debug) { print "\nSQL: $sql\n"; }
					mcq($sql,$db);
				    
				} else {
					$sql= "UPDATE $GLOBALS[TBL_PREFIX]languages SET TEXT='$lanmask' WHERE TEXTID='current-language-mask'";
						if ($debug) { print "\nSQL: $sql\n"; }
					mcq($sql,$db);
				    
				}
		}
}
if ($chgsysval) {

		}

		if ($PhysDelFileConfirmed) {
			
				$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]binfiles WHERE fileid='$kid'";
				mcq($sql,$db);
				$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]blobs WHERE fileid='$kid'";
				mcq($sql,$db);
				$files = 1;
				print "<font color='ff0000'>File $kid $lang[isdel]!</font>";
				log_msg("File #$kid physically deleted","");
		}

		if ($PhysFileDel) {
			log_msg("File list view","");

			print "<tr><td colspan=3><b>$lang[delfile] $kid</b><br><br></td></tr>";
			print "<tr><td colspan=12><table border='1' width='100%' bgcolor='#F2F2F2' cellspacing='0' cellpadding='8' bordercolor='#CECECE'>";
			print "<tr><td>FileID</td><td>Filename</td><td>Creation Date</td><td>Size</td><td>Type</td><td>Referenced</td><td>Parent</td></tr>";
			print "Viewing files";
			$sql = "SELECT fileid,koppelid,filename,creation_date,filesize,filetype FROM $GLOBALS[TBL_PREFIX]binfiles WHERE fileid='$kid'";
			$result= mcq($sql,$db);
			 while ($files= mysql_fetch_array($result)) {
				
				$sql1 = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE eid=$files[koppelid]";
				$result1= mcq($sql1,$db);
				$count_e1 = mysql_fetch_array($result1);
				$count_e = $count_e1[0];
				if ($count_e=="") {
					$ref = "<font color='ff0000'>Unreferenced!</font>";
					$parent = "<font color='ff0000'>n/a</font>";
				} else {
					$ref = "<font color='#33FF00'>OK</font>";
					if ($count_e1[status]=="close") {
						$parent .="<font color='#FF0000'>closed</font> ";
					}
					if ($count_e1[status]=="open") {
						print "<tr><td colspan=7><font color='#FF0000'>You cannot delete a file with an open parent status!</td></tr>";
						print "</table>";
EndHTML();
						exit;
					}
					if ($count_e1[status]=="awaiting closure") {
						$parent .="<font color='#6633FF'>awaiting closure</font> ";
					}
					if ($count_e1[deleted]=="y") {
						$parent .="<font color='#FF0000'>deleted</font> ";
					} else {
						print "<tr><td colspan=7><font color='#FF0000'>You cannot delete a file which has a non-deleted parent!</td></tr>";
						print "</table>";
						EndHTML();
						exit;
					}


				}

			print "<tr><td>$files[fileid]</td><td><img src='arrow.gif'>&nbsp;<a href='csv.php?fileid=$files[fileid]' class='bigsort'>$files[filename]</a></td><td>$files[creation_date]</td><td>$files[filesize]</td><td>$files[filetype]</td><td>$ref</td><td>$parent</td></tr>";
			$tot++;
			unset($parent);
			$totsize = $totsize + $files[filesize];
					 }
			
			print "<tr><td colspan=7>";
			print "<form name='2' method='POST'><input type='hidden' name='PhysDelFileConfirmed' value='1'><input type='hidden' name='password' value='$password'><input type='hidden' name='kid' value='$kid'>";
			print "<input type='submit' style='width: 250px' name='knoppie' value='Confirm delete'></form>";

		print "</table></td></tr>";
	}
if ($sysval) {
	log_msg("Change System Values section accessed","");
	print "<tr><td>&nbsp;&nbsp;</td><td colspan=3>Change global system values<br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password&joepie=1' style='cursor:pointer'>Back to main administration page</a><br></td></tr>";
	print "<tr><td></td><td colspan=3><table class='crm'><tr><td colspan =3>";
	print "<form name='bogusform' method='POST'>";
	print "Search: <input type='text' class='searchx_tabbar' name='SettingSearchQuery' OnChange='document.bogusform.submit();' value=''>";
	print "<input type='hidden' name='sysval' value='1'>";
	if ($_REQUEST['SettingSearchQuery']) {
		print " <a href='admin.php?sysval=1'>[all]</a>";
	}
	print "</form>";
	print "</td></tr><tr><td>&nbsp;&nbsp;&nbsp;";
	print "<b>Setting name</b></td><td><b>Current value</b></td><td><b>Discription</b></td></tr>";

	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
		document.bogusform.SettingSearchQuery.focus();
		function gobla(i) {
				document.location="admin.php?password=<? echo $password;?>&EditSysVar=" + i + "&<? echo $epoch;?>";
		}
	//-->
	</SCRIPT>
	<?
	
	if ($_REQUEST['SettingSearchQuery']) {
		$sql= "SELECT settingid, setting, value, discription FROM $GLOBALS[TBL_PREFIX]settings WHERE setting NOT LIKE '%STASH%' AND 	setting<>'REQUIREDDEFAULTFIELDS' AND setting LIKE '%" . $_REQUEST['SettingSearchQuery'] . "%' ORDER BY setting";
	} else {
		$sql= "SELECT settingid, setting, value, discription FROM $GLOBALS[TBL_PREFIX]settings WHERE setting NOT LIKE '%STASH%' AND setting<>'REQUIREDDEFAULTFIELDS' ORDER BY setting";
	}
	if ($debug) { print "\nSQL: $sql\n"; }
	$Returned_Rows = 0;
	$result= mcq($sql,$db);
		while ($resarr=mysql_fetch_array($result)) {
			$Returned_Rows++;
			$Num = $resarr['settingid'];
			if (($resarr[setting]<>"Extra fields list") && ($resarr[setting]<>"Extra customer fields list") && ($resarr[setting]<>"PersonalTabs") && ($resarr[setting]<>"Category pulldown list") && ($resarr[setting]<>"MainListColumnsToShow") && ($resarr[setting]<>"NextInvoiceNumberCounter") && ($resarr[setting]<>"CustomerListColumnsToShow")) {
				print "<tr onmouseout=\"javascript:style.background='#FFFFFF';\" onmouseover=\"javascript:style.background='#E9E9E9';\"><td style='cursor:pointer' OnClick='javascript:gobla(" . $resarr[settingid] . ");'><img src='arrow.gif'>&nbsp;<a class='bigsort'>" . strtoupper($resarr[setting]) . "</a></td><td style='cursor:pointer' OnClick='javascript:gobla(" . $resarr[settingid] . ");'>";
				if (substr($resarr[setting],0,4) == "BODY" || $resarr[setting] == "STANDARD_TEXT"|| $resarr[setting] == "EMAILINBOX" || stristr($resarr[setting],"Subject") || $resarr[setting] == "ShowMainPageLinks" || $resarr[setting] == "RSS_FEEDS") {
					print "&lt;click to edit&gt;</td><td  style='cursor:pointer' OnClick='javascript:gobla(" . $resarr[settingid] . ");'>$resarr[discription]</td></tr>";
				
				} elseif (stristr($resarr['setting'],"password")){
					if ($resarr['value']=="" || $resarr['value'] == "*NONE*") {
						print " -- no password set --";
					} else {
						print "********";
					}
					print "</td><td style='cursor:pointer' OnClick='javascript:gobla(" . $resarr[settingid] . ");'>$resarr[discription]</td></tr>";					
				} elseif ($resarr['setting']=="EXTRAFIELDLOCATION") {
					if ($resarr['value']=="A") {
						print "Just above text field";
					} else {
						print "Just above file list";					
					}


					print "</td><td style='cursor:pointer' OnClick='javascript:gobla(" . $resarr[settingid] . ");'>$resarr[discription]</td></tr>";	
				} elseif (strstr($resarr['setting'],"COLOR")) {
					print "<table><tr><td style='background: ". $resarr['value'] . "'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
					print "</td><td style='cursor:pointer' OnClick='javascript:gobla(" . $resarr[settingid] . ");'>$resarr[discription]</td></tr>";	
				} elseif (strstr($resarr['setting'],"_FORM")) {
						if ($resarr['value'] == "Default") {
							print "Default";
						} else {
							print $resarr['value'] . " (" . GetFileName($resarr['value']) . ")";
						}
						print "</td><td style='cursor:pointer' OnClick='javascript:gobla(" . $resarr[settingid] . ");'>$resarr[discription]</td></tr>";
				} else {
					if (strtoupper($resarr['value'])=="YES" || strtoupper($resarr['value'])=="ON") {
						$i1 = "<font color='#66CC66'><b>";
						$i2 = "</b></font>";
					} elseif (strtoupper($resarr['value'])=="NO" || strtoupper($resarr['value'])=="OFF") {
						$i1 = "<font color='#FF0000'><b>";
						$i2 = "</b></font>";
					} else {
						unset($i1);
						unset($i2);
					}

					print $i1 . "$resarr[value]&nbsp;" . $i2;

					print "</td><td style='cursor:pointer' OnClick='javascript:gobla(" . $resarr[settingid] . ");'>$resarr[discription]</td></tr>";
				}
			}
	}
	if ($Returned_Rows == 1) {
			print "<tr><td>";
			?>
				<SCRIPT LANGUAGE="JavaScript">
				<!--
				document.location = 'admin.php?password=&EditSysVar=<? echo $Num;?>';
				//-->
				</SCRIPT>
			<?
			print "</td></tr>";
	}
	print "<form name='settings' method='POST'>";
	print "<input type='hidden' name='password' value='$password'>";
	print "<input type='hidden' name='chglansettings' value='1'>";
	if (!$_REQUEST['SettingSearchQuery']) {
		print "<tr><td>Main language:</td>";
		print "<td>";
		print "<select name='lan'>";

			$sql= "SELECT TEXT FROM $GLOBALS[TBL_PREFIX]languages WHERE TEXTID='current-language' AND LANGID='GLOBAL'";
			$result= mcq($sql,$db);
			$result= mysql_fetch_array($result);
			$language_overall = $result[TEXT]; // This is the system-wide language variable, now check the user's preference
		
			$sql= "SELECT DISTINCT LANGID FROM $GLOBALS[TBL_PREFIX]languages";
			$result= mcq($sql,$db);
			while ($resarr=mysql_fetch_array($result)){
					if ((trim($resarr[LANGID])=="") || ($resarr[LANGID] == "GLOBAL")) {
				// GLOBAL is a global language setting which ought to be ignored
					} else {
						if ($language_overall==$resarr[LANGID]) {
							$ins = "SELECTED";
						} else {
							unset($ins);
						}

						print "<option value='$resarr[LANGID]' $ins>$resarr[LANGID]</option>";
					}
		
			}
			print "</select>";


			print "</td><td>System-wide default language</td></tr>";
			print "<tr><td>Language mask:</td>";
			print "<td>";
			print "<select name='lanmask'>";
			print "<option value='-'>None</option>";
			$sql= "SELECT TEXT FROM $GLOBALS[TBL_PREFIX]languages WHERE TEXTID='current-language-mask' AND LANGID='GLOBAL'";
			$result= mcq($sql,$db);
			$result= mysql_fetch_array($result);
			$mask_oa = $result[TEXT]; // This is the system-wide language variable, now check the user's preference
		
			$sql= "SELECT DISTINCT LANGID FROM $GLOBALS[TBL_PREFIX]languages";
			$result= mcq($sql,$db);
			while ($resarr=mysql_fetch_array($result)){
					if ((trim($resarr[LANGID])=="") || ($resarr[LANGID] == "GLOBAL")) {
				// GLOBAL is a global language setting which ought to be ignored
					} else {
						if ($mask_oa==$resarr[LANGID]) {
							$ins = "SELECTED";
						} else {
							unset($ins);
						}

						print "<option value='$resarr[LANGID]' $ins>$resarr[LANGID]</option>";
					}
		
			}
			print "</select>";
		print "</td><td>System-wide default language mask</td></tr>";

		print "</table>";
		print "<tr><td colspan=3><br><br>&nbsp;&nbsp;<input type='submit' name='aplysettings' value='Apply language and mask'></form></td></tr>";
		print "<tr><td colspan=2></td></tr>";
	}
}


if ($edit1) {

	for ($x=0;$x<(sizeof($t)-1);$x++) {



	    $sql = "UPDATE $GLOBALS[TBL_PREFIX]help SET title=\"$t[$x]\",content=\"$c[$x]\" WHERE helpid=\"$h_id[$x]\"";
		log_msg("Help contents edited","");
	    mcq($sql,$db);
		//print $sql;

	}
	$x = sizeof($t)-1;
	if (!$t[$x] == "") {
		$c[$x] = addslashes($c[$x]);
		$t[$x] = addslashes($t[$x]);

	    $sql = "INSERT INTO $GLOBALS[TBL_PREFIX]help(title,content) VALUES(\"$t[$x]\",\"$c[$x]\")";
		log_msg("Help item added","");
	    mcq($sql,$db); 
		//print $sql;
		print "<tr><td>Help database updated.</td></tr>";
	}

}



if ($fysconfirmed) {
	
		MustBeAdmin();
		
		print "<tr><td colspan=2>The entity and all its references were physically deleted.</td><tr>";
		$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='$fysdelid'";
		mcq($sql,$db);
		$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]calendar WHERE eid='$fysdelid'";
		mcq($sql,$db);
		$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='$fysdelid'";
		mcq($sql,$db);

		$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='$fysdelid'";
		mcq($sql,$db);
		$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]journal WHERE eid='$fysdelid'";
		mcq($sql,$db);
		$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]ejournal WHERE eid='$fysdelid'";
		mcq($sql,$db);
		log_msg("PHYSICAL ENTITY DELETE: $fysdelid","");
		qlog("PHYSICAL ENTITY DELETE: $fysdelid");
		unset($fysdelid);
		if ($fromcustlist) {
				?>
					<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
					<!--
						document.location='index.php?ShowEntitiesOpen=1&filter=custinsert';
					//-->
					</SCRIPT>
				<?
		}
}
if ($fysdelid) {

	$tot = pushentity($fysdelid);
	
	if ($tot<>1) { 
		MainAdminTabs("datman");
		print "<table>";
		print "<tr><td colspan=3><b>Nothing found. This could mean this entity is not yet deleted, or maybe it doesn't exist at all.</td></tr>"; 
		print "</table>";
		EndHTML();
		exit;
		} 
	
		print "<tr><td colspan=4><br><b>Please confirm by clicking the button below</b><br>";
		print "<form name='confirm' method='POST'><input type='hidden' name='fysdelid' value='$fysdelid'>";
		print "<input type='hidden' name='fysconfirmed' value='1'><input type='hidden' name='password' value='$password'>";
		print "</td></tr><tr><td colspan=3><br><input type='submit' name='knopje' value='Confirm physical deletion'></td></tr></table>";
		print "</form></table>";
		EndHTML();
	exit;
}

if ($fysdelete) {
		MainAdminTabs("datman");
		print "<table>";
		print "<tr><td><b>You can delete a single entity here, or delete a whole set of entities (which<br>were closed before a given date) by using the <img src='arrow.gif'> <a href='db_clean.php' class='bigsort'>database cleanup</a> function</b><br><br></td></tr>"; 
		print "<tr><td colspan=8>Delete a single entity, please enter the ID of the (already deleted) entity you whish to delete physically:</td></tr>";
		print "<tr><td colspan=8><form name='fd' method='POST'><input type='hidden' name='password' value='$password'>";
		print "<input type='text' size=3 name='fysdelid'><br><br><input type='submit' name='knopje' value='Delete'>&nbsp;<img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password&joepie=1' style='cursor:pointer'>Back to main administration page</a></form></td></tr>";
		print "</table>";
		EndHTML();
		exit;
}




if ($fhelp) {
	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]help ORDER BY title";
	$result= mcq($sql,$db);
	print "<tr><td colspan=12><table border=0 width='100%'>";
	print "<tr><td width='100%'><b><font size=+1>$lang[helpcontents]</font></b></td></tr>";
	print "<form name='edithelp' method=POST>";
	print "<input type='hidden' name='password' value='$password'>";
	print "<input type='hidden' name='edit1' value='edit1'>";
	while ($help = mysql_fetch_array($result)) {
		
		print "<tr><td>";
		print "<font size='+1'>$help[title] ($help[helpid])</font><br>";
		print "<input type=\"text\" name=\"t[]\" value=\"". ($help[title]) ."\" size=60><br>";
		print "<input type=\"hidden\" name=\"id[]\" value=\"$help[helpid]\">";
		print "<TEXTAREA rows=10 cols=80 name=\"c[]\" wrap=\"virtual\" class=\"txt\">" . ($help[content]) ."</TEXTAREA><br></td></tr>";
		print "<tr><td width='100%'></td></tr>";
	}
	print "<tr><td width='100%'>$lang[addhelp]</td></tr>";	
	print "<tr><td><input type='text' name='t[]' value='$help[title]' size=60><br>";
	print "<input type='hidden' name='h_id[]' value='$help[helpid]'>";
	print "<TEXTAREA rows=10 cols=80 name='c[]' wrap='virtual' class='txt'>$help[content]</TEXTAREA><br></td></tr>";
	print "<tr><td width='100%'></td></tr>";
	print "<tr><td width='100%'><input type='submit' value='$lang[apply]'></td></tr>";
	print "</td></tr>";
	print "</table>";
	print "</table>";
EndHTML();
	exit;
}







if ($fysdelete) {
	print "Physically delete deleted entities";
}


print "</table></td></tr></table>";
EndHTML();



function printbox($msg)
{
		global $printbox_size,$legend;
		
		if (!$printbox_size) {
			$printbox_size = "100%";
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
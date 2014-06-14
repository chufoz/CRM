<?
/*
 *********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This is the CRM updater - it should be run from your browser,
 * not from the command line.
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
*/

// Disable the menu
$nonavbar=1;

// Set error reporting level
//error_reporting(E_ERROR);

// Limit restrictions
$custinsertmode=1;
if ($_SERVER['HTTP_HOST']) {
	$web = 1;
}

// Comment or delete the following following line to skip security
// (The above line is put there deliberately)
if ($web) {
	$web = 1;
	$EnableRepositorySwitcherOverrule="n";	
	
} else {
	require("config.inc.php");
	require("functions.php");

	print "Database upgrade procedure\n\nPlease proof your identity - log on to any of the following repositories as an administrator:\n\n";
	if ($argv[1]=="-help" || $argv[1]=="--help" || $argv[1]=="help" || $argv[1]=="-h") {
		print "\nUsage:\n";
		print "\t[no arguments]\t:Interactive\n";
		exit;
	}
	if ($argv[1]) {
		$repository = $argv[1];
	} 

	if ($argv[2]) {
		$username = $argv[2];
	} 
	if ($argv[3]) {
		$password = $argv[3];
	} 
	if ($argv[4]) {
		$auto = $argv[4];
		$auto=1;
	} 
	//if (!CommandlineLogin($username,$password,$repository)) {
	//	print "Exiting...";
	//	exit;
	//}
	if (!CommandlineLogin($username,$password,$repository)) {
		print "Exiting...";
		exit;
	}

	print "Please wait ... \n\n";
	
//	print ReturnReposList();

	print $a;
	while (!$GLOBALS['end'] == true) {
		unset($host);
		unset($pass);
		unset($database);
		unset($slave);
		unset($table_prefix);
		require("config.inc.php");
		print ReturnReposList();
		CLMenu();
	}

	
	exit;
}

// And then uncomment this one:
	//include("config.inc.php");

	include("header.inc.php");
// BUT DON'T FORGET TO DELETE THIS SCRIPT AFTER USE IF YOU DISABLED SECURITY
MustBeAdmin();


?>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
<!--
// Write the correct stylesheet into the document
		if (navigator.appName.indexOf('Microsoft') > -1) {
			// IE stylesheet
			document.write('<link href="crm_dft.css" rel="stylesheet" type="text/css">');
			} else {
			// NS stylesheet (gheghe, the same :) )
			document.write('<link href="crm_dft.css" rel="stylesheet" type="text/css">');
		}
//-->
</SCRIPT>
<TR><TD><CENTER><IMG SRC='crm.gif'></CENTER></TD></TR>
<?
$legend = "DATABASE&nbsp;UPGRADE&nbsp;PROCEDURE";

/*
	$sqla=array();
	$db_ver_from = "3.4.3";
	$db_ver_to = "3.4.3";

	array_push($sqla,"SELECT * FROM PRFX@@@@@@@settings");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@failoverquerystore` CHANGE `query` `query` MEDIUMBLOB NOT NULL");
	Upgrade($db_ver_from, $db_ver_to, $sqla);

	exit;
*/


if ($A193TO194) {
	A193TO194();
} elseif ($A194TO195)	{
	A194TO195();
} elseif ($A195TO196)	{
	A195TO196();
} elseif ($A196TO2)		{
	A196TO2();
} elseif ($A2TO21)		{
	A2TO21();
} elseif ($A21TO22)		{
	A21TO22();
} elseif ($A22TO23)		{
	A22TO23();
} elseif ($A23TO24)		{
	A23TO24();
} elseif ($A24TO241)	{
	A24TO241();
} elseif ($A241TO242)	{
	A241TO242();
} elseif ($A242TO243)	{
	A242TO243();
} elseif ($A243TO244)	{
	A243TO244();
} elseif ($A244TO245)	{
	A244TO245();
} elseif ($A245TO246)	{
	A245TO246();
} elseif ($A246TO25)	{
	A246TO25();
} elseif ($A250TO251)	{
	A250TO251();
} elseif ($A251TO252)	{
	A251TO252();
} elseif ($A252TO253)	{
	A252TO253();
} elseif ($A253TO260)	{
	A253TO260();
} elseif ($A260TO261)	{
	A260TO261();
} elseif ($A261TO262)	{
	A261TO262();
} elseif ($A262TO300)	{
	A262TO300();
} elseif ($A300TO310)	{
	A300TO310();
} elseif ($A310TO320)	{
	A310TO320();
} elseif ($A320TO330)	{
	A320TO330();
} elseif ($A330TO331)	{
	A330TO331();
} elseif ($A331TO332)	{
	A331TO332();
} elseif ($A332TO340)	{
	A332TO340();
} elseif ($A340TO341)   {
	A340TO341();
} elseif ($A341TO342)   {
	A341TO342();
} elseif ($A342TO343)   {
	A342TO343();
} elseif ($_REQUEST['set_mm_on'] == "1") {
	$sqla = array();
	array_push($sqla, "UPDATE PRFX@@@@@@@settings SET value='Yes' WHERE setting='MAINTENANCE_MODE'");
	$GLOBALS['IGNORE_VERSION'] = true;
	Upgrade("%", "%", $sqla);
	?>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
		document.location='upgrade.php?mm_is_now_on';
	//-->
	</SCRIPT>
	<?
} elseif ($_REQUEST['set_mm_on'] == "2") {
	$sqla = array();
	array_push($sqla, "UPDATE PRFX@@@@@@@settings SET value='No' WHERE setting='MAINTENANCE_MODE'");
	$GLOBALS['IGNORE_VERSION'] = true;
	Upgrade("%", "%", $sqla);
	?>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
		document.location='upgrade.php?mm_is_now_on';
	//-->
	</SCRIPT>
	<?
} else	{

		$a = "<TABLE><TR><TD>This procedure converts your CRM database which is needed when you upgrade to a new version of CRM. As this script alters your database(s), only use this script after you have made backups of your database and your web directory!<br><br><b>This procedure will only convert repositories which have the correct DB version number. It will automatically upgrade any configured fail-over databases.</b><br><br>This script will upgrade all databases (repositories) found in your config.inc.php. ";

		$a .= "<br><br><U>Versions < 3.4.3 do not require maintenance mode</U>.<br><br><img src='arrow.gif'> <a href='upgrade.php?set_mm_on=1' class='bigsort'>Enable maintenance mode on all repositories</a> (required for upgrading)<br>";
		$a .= "<img src='arrow.gif'> <a href='upgrade.php?set_mm_on=2' class='bigsort'>Disable maintenance mode on all repositories</a> (required for use)<br><br>";

		$a .= "The following repositories will be upgraded:<br><br><table border=1 width=100%><tr><td><B>id</B></td><td><B>Database</B></td><td><B>DB Version</B></td><td><B># entities</B></td><td><B>Name</B></td><td><B>Maintenance mode enbled</B></td><td><B>Fail-over database</B></td></tr>";

		if (sizeof($pass)>0) {
						for ($r=0;$r<64;$r++) {
							if ($database[$r]) {
								if ($db = @mysql_connect($host[$r], $user[$r], $pass[$r])) {
									if (@mysql_select_db($database[$r],$db)) {
										$sql = "SELECT value FROM $table_prefix[$r]settings WHERE setting='title'";
										if ($result= mysql_query($sql)) {


											
											$sql = "SELECT value FROM $table_prefix[$r]settings WHERE setting='title'";
											$result= @mcq_upg($sql,$db);
											$maxU1 = @mysql_fetch_array($result);
											$title = $maxU1[0];

											$sql = "SELECT value FROM $table_prefix[$r]settings WHERE setting='DBVERSION'";
											$result= @mcq_upg($sql,$db);
											$maxU1 = @mysql_fetch_array($result);
											$version = $maxU1[0];

											$sql = "SELECT value FROM $table_prefix[$r]settings WHERE setting='MAINTENANCE_MODE'";
											$result= @mcq_upg($sql,$db);
											$maxU1 = @mysql_fetch_array($result);
											$maintenancemode = $maxU1[0];
			
											if ($version=="") { $version="Prior to 1.9.0."; }

											$sql = "SELECT COUNT(*) FROM $table_prefix[$r]entity";
											$result= @mcq_upg($sql,$db);
											$res = @mysql_fetch_array($result);
											$enum = $res[0];

											$sql = "SHOW TABLE STATUS";
											$result= @mcq_upg($sql,$db);
											while ($stat = @mysql_fetch_array($result))
											{
												$size += $stat["Data_length"];
												$size += $stat["Index_lenght"];
											}							
				
											$size = round(($size/1024)) . "K";
											
											if ($slave[$r]) {
												$ins = "mysql://" . $user[$r] . "@" . $slave[$r]. ":" . $database[$r] . "/" . $table_prefix[$r] . "*";
											} else {
												$ins = "[none]";	
											}

											$a .= "<tr><td>$r</td><td>$database[$r]</td><td>$version</td><td>$enum</td><td>$title</td><td>" . $maintenancemode . "</td><td>" . $ins . "</td></tr>";

											$totent += $enum;
										} // end if 1st query was ok
									} else {
										$a .= "<tr><td>$r</td><td>$database[$r]</td><td><font color='#ff3300'>n/a</td><td><font color='#ff3300'>n/a</td><td><font color='#FF3300'>Couldn't select database</font></td><td>n/a</td></tr>";
									}

								} else {
									$a .= "<tr><td>$r</td><td>$database[$r]</td><td>$title |<font color='#FF3300'>Database host unreachable</font></td><td>n/a</td></tr>";
								}
							}
							}
					}



		$a .= "</table></i><bR><br></TD></TR><tr><td><b>Any user who can log into one of your repositories as Admin can run this script so please be careful. Delete this script after use!<br><br></b></td></tr>";

		if ($totent > 1000) {
			$a .= "<tr><td><img src='error.gif'><font color='#FF0000'><b>You have a large database. Please consider upgrading using the command line! See the <a href='CRM-CTT_Adminmanual.pdf' target='new'>manual</a> for details.</b></font><br>&nbsp;</td></tr>";
		}

		$a .= "<TR><TD>To upgrade from database 3.4.2 to 3.4.3 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A342TO343=1' class='bigsort'>here</a>.<br></TD>";
		
		$a .= "<TR><TD>&nbsp;</TD><TD></TD></TR>";

		$a .= "<TR><TD>To upgrade from database 3.4.1 to 3.4.2 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A341TO342=1' class='bigsort'>here</a>.<br></TD>";
		$a .= "<TR><TD>To upgrade from database 3.4.0 to 3.4.1 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A340TO341=1' class='bigsort'>here</a>.<br></TD>";
		$a .= "<TR><TD>To upgrade from database 3.3.2 to 3.4.0 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A332TO340=1' class='bigsort'>here</a>.<br></TD>";
		$a .= "<TR><TD>To upgrade from database 3.3.1 to 3.3.2 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A331TO332=1' class='bigsort'>here</a>.<br></TD>";
		$a .= "<TR><TD>To upgrade from database 3.3.0 to 3.3.1 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A330TO331=1' class='bigsort'>here</a>.<br></TD>";
		$a .= "<TR><TD>To upgrade from database 3.2.0 to 3.3.0 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A320TO330=1' class='bigsort'>here</a>. <br></TD>";
		$a .= "<TR><TD>To upgrade from database 3.1.0 to 3.2.0 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A310TO320=1' class='bigsort'>here</a>.<br></TD>";
		$a .= "<TR><TD>To upgrade from database 3.0.0 to 3.1.0 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A300TO310=1' class='bigsort'>here</a>.<br></TD>";
		$a .= "<TR><TD>To upgrade from database 2.6.2 to 3.0.0 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A262TO300=1' class='bigsort'>here</a>.<br></TD>";
		$a .= "<TR><TD>To upgrade from database 2.6.1 to 2.6.2 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A261TO262=1' class='bigsort'>here</a>.<br></TD>";
		$a .= "<TR><TD>To upgrade from database 2.6.0 to 2.6.1 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A260TO261=1' class='bigsort'>here</a>.<br></TD>";

		//$a .= "<TR><TD>To upgrade from database 2.5.3 to 2.6.0 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A253TO260=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.5.2 to 2.5.3 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A252TO253=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.5.1 to 2.5.2 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A251TO252=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.5.0 to 2.5.1 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A250TO251=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.4.6 to 2.5.0 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A246TO25=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.4.5 to 2.4.6 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A245TO246=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.4.4 to 2.4.5 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A244TO245=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.4.3 to 2.4.4 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A243TO244=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.4.2 to 2.4.3 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A242TO243=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.4.1 to 2.4.2 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A241TO242=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.4.0 to 2.4.1 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A24TO241=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.3.0 to 2.4.0 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A23TO24=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.2.0 to 2.3.0 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A22TO23=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.1.0 to 2.2.0 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A21TO22=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 2.0.0 to 2.1.0 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A2TO21=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 1.9.6 to 2.0.0 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A196TO2=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 1.9.5 to 1.9.6 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A195TO196=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 1.9.4 to 1.9.5 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A194TO195=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 1.9.3 to 1.9.4 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A193TO194=1' class='bigsort'>here</a>.<br></TD>";
		//$a .= "<TR><TD>To upgrade from database 1.9.2 to 1.9.3 click <img src='arrow.gif'>&nbsp;<a href='upgrade.php?A192TO193=1' class='bigsort'>here</a>.<br></TD>";

		print "</TABLE>";

		printbox($a);

}


	


function A342TO343() {
	$db_ver_from = "3.4.2";
	$db_ver_to = "3.4.3";
	$sqla = array();
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` ADD `ALLOWEDSTATUSVARS` LONGTEXT NOT NULL , ADD `ALLOWEDPRIORITYVARS` LONGTEXT NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@userprofiles` ADD `ALLOWEDSTATUSVARS` LONGTEXT NOT NULL , ADD `ALLOWEDPRIORITYVARS` LONGTEXT NOT NULL");
	array_push($sqla,"UPDATE PRFX@@@@@@@settings SET value='Tahoma' WHERE setting='DFT_FONT'");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@settings(setting, value, discription) VALUES('Mass_Update', 'No', 'Set this to yes to enable mass entity updates using checkboxes on the main list')");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@triggers` ADD `comment` MEDIUMTEXT NOT NULL");
	array_push($sqla,"DELETE FROM `PRFX@@@@@@@settings` WHERE setting='FormFinity'");
	//array_push($sqla,"CREATE TABLE `PRFX@@@@@@@entityformcache` (`formcacheid` mediumint(9) NOT NULL auto_increment,`eid` mediumint(9) NOT NULL,`formid` mediumint(9) NOT NULL,`user` mediumint(9) NOT NULL,`content` longtext NOT NULL,`timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, PRIMARY KEY  (`formcacheid`)) TYPE=MyISAM COMMENT='CRM-CTT Cached parsed forms'");
	array_push($sqla,"CREATE TABLE `PRFX@@@@@@@entityformcache` (`formcacheid` mediumint(9) NOT NULL auto_increment,`eid` mediumint(9) NOT NULL,`formid` mediumint(9) NOT NULL,`user` mediumint(9) NOT NULL,`content` longtext NOT NULL,`timestamp` timestamp(14) NOT NULL,PRIMARY KEY  (`formcacheid`)     ) TYPE=MyISAM COMMENT='CRM-CTT Cached parsed forms'");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'PaginateMainEntityList', '30', NOW( ) , '0 for no pagination, [number] for max number of entities per page.');");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'MAINTENANCE_MODE', 'No', NOW( ) , 'When set to Yes, only administrators can log in. Required when upgrading.');");
	array_push($sqla, "CREATE TABLE `PRFX@@@@@@@failoverquerystore` (`id` mediumint(9) NOT NULL auto_increment,`query` mediumblob NOT NULL,`targethost` varchar(100) NOT NULL, `lockhash` varchar(100) NOT NULL, PRIMARY KEY  (`id`)) TYPE=MyISAM COMMENT='CRM Fail-over queries to replicate'");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@languages` ( `id` , `LANGID` , `TEXTID` , `TEXT` )VALUES ('', 'ENGLISH', 'maintenancemodeison', 'This repository is in maintenance mode. You cannot enter it now, please try again later.')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@settings(setting, value, discription) VALUES('ONEENTITYPERCUSTOMER', 'No', 'When enabled, only one entity per customer may exist.')");



	Upgrade($db_ver_from, $db_ver_to, $sqla);
}

function A341TO342() {
	$db_ver_from = "3.4.1";
	$db_ver_to = "3.4.2";
	$sqla = array();
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'NOBARSWINDOW', 'No', NOW( ) , 'Set this option to Yes to force a no-bars (fullscreen) window');");

	array_push($sqla, "ALTER TABLE PRFX@@@@@@@customaddons ADD KEY eid (eid)");
	array_push($sqla, "ALTER TABLE PRFX@@@@@@@entity ADD KEY formid (formid)");

	// Database cleanup thingies

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@accesscache` CHANGE `cacheid` `cacheid` INT NOT NULL AUTO_INCREMENT ,CHANGE `user` `user` INT NOT NULL DEFAULT '0',CHANGE `eidcid` `eidcid` INT NOT NULL DEFAULT '0'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@binfiles` CHANGE `fileid` `fileid` INT NOT NULL AUTO_INCREMENT ,CHANGE `koppelid` `koppelid` INT NOT NULL DEFAULT '0'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@blobs` CHANGE `fileid` `fileid` INT NOT NULL DEFAULT '0'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@cache` CHANGE `stashid` `stashid` INT NOT NULL AUTO_INCREMENT");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customaddons` CHANGE `id` `id` INT NOT NULL AUTO_INCREMENT , CHANGE `eid` `eid` INT NOT NULL DEFAULT '0', CHANGE `name` `name` INT NOT NULL DEFAULT '0'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@ejournal` CHANGE `lasteditby` `lasteditby` INT NOT NULL DEFAULT '0',CHANGE `createdby` `createdby` INT NOT NULL DEFAULT '0'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entity` CHANGE `lasteditby` `lasteditby` INT NOT NULL DEFAULT '0',CHANGE `createdby` `createdby` INT NOT NULL DEFAULT '0',CHANGE `parent` `parent` INT NOT NULL DEFAULT '0'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entitylocks` CHANGE `lockid` `lockid` INT NOT NULL AUTO_INCREMENT ,CHANGE `lockon` `lockon` INT NOT NULL DEFAULT '0',CHANGE `lockby` `lockby` INT NOT NULL DEFAULT '0'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` CHANGE `id` `id` INT NOT NULL AUTO_INCREMENT");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@help` CHANGE `helpid` `helpid` INT NOT NULL AUTO_INCREMENT");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@internalmessages` CHANGE `id` `id` INT NOT NULL AUTO_INCREMENT ,CHANGE `to` `to` INT NOT NULL DEFAULT '0',CHANGE `from` `from` INT NOT NULL DEFAULT '0'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@journal` CHANGE `id` `id` INT NOT NULL AUTO_INCREMENT ,CHANGE `eid` `eid` INT NOT NULL DEFAULT '0',CHANGE `user` `user` INT NOT NULL DEFAULT '0'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@languages` CHANGE `id` `id` INT NOT NULL AUTO_INCREMENT");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@settings` CHANGE `settingid` `settingid` INT NOT NULL AUTO_INCREMENT");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@triggers` CHANGE `tid` `tid` INT NOT NULL AUTO_INCREMENT ,CHANGE `template_fileid` `template_fileid` INT NOT NULL DEFAULT '0',CHANGE `report_fileid` `report_fileid` INT NOT NULL DEFAULT '0'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@uselog` CHANGE `id` `id` INT NOT NULL AUTO_INCREMENT");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@binfiles` COMMENT = 'CRM Binary files'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@ejournal` DROP INDEX `id`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@users` DROP INDEX `id`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` DROP INDEX `EMAILCREDENTIALS_2`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` DROP INDEX `EMAILCREDENTIALS_3`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@calendar` CHANGE `calendarid` `calendarid` MEDIUMINT NOT NULL AUTO_INCREMENT");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@phonebook` CHANGE `id` `id` MEDIUMINT NOT NULL AUTO_INCREMENT");

	array_push($sqla,"DELETE FROM PRFX@@@@@@@settings WHERE setting='ShowShortKeyLegend'");

	array_push($sqla, "ALTER TABLE PRFX@@@@@@@binfiles ADD INDEX ( `filetype` ) ");

	Upgrade($db_ver_from, $db_ver_to, $sqla);
}
function A340TO341() {
	$db_ver_from = "3.4.0";
	$db_ver_to = "3.4.1";
	$sqla = array();
	array_push($sqla, "CREATE TABLE `PRFX@@@@@@@blobs` ( `fileid` bigint(20) NOT NULL, `content` mediumblob NOT NULL,PRIMARY KEY  (`fileid`) ) TYPE=MyISAM COMMENT='Blob stand-alone table';");
	//array_push($sqla, "INSERT INTO PRFX@@@@@@@blobs(SELECT fileid, content FROM PRFX@@@@@@@binfiles)");
	//array_push($sqla, "ALTER TABLE PRFX@@@@@@@binfiles DROP content");
	array_push($sqla, "DELETE FROM PRFX@@@@@@@settings WHERE setting='STASH'");
        array_push($sqla, "CREATE TABLE PRFX@@@@@@@accesscache (  cacheid bigint(20) NOT NULL auto_increment,  user bigint(20) NOT NULL,  type enum('e','c') NOT NULL,  eidcid bigint(20) NOT NULL,  result enum('nok','readonly','ok') NOT NULL,  PRIMARY KEY  (cacheid),  KEY user (user),  KEY type (type)) TYPE=MyISAM COMMENT='CRM Access cache table'");
	array_push($sqla, "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'USE_EXTENDED_CACHE', 'Yes', NOW( ) , 'Use extensive access rights and extra fields caching. Improves performance.')");

// ----------------------------ooo

	array_push($sqla, "ALTER TABLE `PRFX@@@@@@@loginusers` ADD `ELISTLAYOUT` TEXT NOT NULL");
	array_push($sqla, "ALTER TABLE `PRFX@@@@@@@loginusers` ADD `CLISTLAYOUT` TEXT NOT NULL");
	array_push($sqla, "CREATE TABLE `PRFX@@@@@@@contactmoments` (`id` INT NOT NULL AUTO_INCREMENT ,`eidcid` INT NOT NULL ,`type` ENUM( 'entity', 'customer' ) NOT NULL ,`user` INT NOT NULL ,`meta` TEXT NOT NULL ,`body` LONGTEXT NOT NULL ,`date` TIMESTAMP NOT NULL ,PRIMARY KEY ( `id` ) ) TYPE = MYISAM COMMENT = 'CRM Contact moments journal'");
	array_push($sqla, "ALTER TABLE `PRFX@@@@@@@contactmoments` ADD `to` TEXT NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` ADD `storetype` ENUM( 'default', '3rd_table','3d_table_popup' ) NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` ADD `accessarray` LONGTEXT NOT NULL ");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'CHECKFORDOUBLEADDS', 'Yes', NOW( ) , 'CRM-CTT checks if an entity is not added twice within an hour. If this bothers you, disable this check by setting this to No.')");

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` ADD `size` INT NOT NULL");

	Upgrade($db_ver_from, $db_ver_to, $sqla);

}

function A332TO340() {

	$db_ver_from = "3.3.2";
	$db_ver_to = "3.4.0";
	$sqla = array();
	array_push($sqla, "CREATE TABLE `PRFX@@@@@@@internalmessages` (`id` BIGINT NOT NULL AUTO_INCREMENT ,`to` BIGINT NOT NULL ,`from` BIGINT NOT NULL ,`time` TIMESTAMP NOT NULL ,`read` ENUM( 'n', 'y' ) NOT NULL ,`body` MEDIUMTEXT NOT NULL ,PRIMARY KEY ( `id` ) ,INDEX ( `to` ) ) TYPE = MYISAM COMMENT = 'CRM-CTT Internal messages (user-to-user)'");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'FormFinity', 'No', NOW( ) , 'When set to Yes, entities will \'remember\' what form was used to create them, and the entity will always show up in that form.')");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entity` ADD `formid` MEDIUMINT DEFAULT '0' NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@ejournal` ADD `formid` MEDIUMINT DEFAULT '0' NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` ADD `ADDFORMS` LONGTEXT NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@userprofiles` ADD `ADDFORMS` LONGTEXT NOT NULL");

	// NIEUW
		
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'UNIFIED_FROMADDRESS', '', NOW( ) , 'An address entered here, will *always* overwrite the from-address in mails. All mails will have this from-address.')");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'CUSTOMCUSTOMERFORM', '', NOW( ) , 'When you enter the (valid) number of a customer HTML-form here, all customer records will be shown in that form.')");

	array_push($sqla, "UPDATE PRFX@@@@@@@loginusers SET ADDFORMS ='a:1:{i:0;s:7:\"default\";}'"); 
	array_push($sqla, "UPDATE PRFX@@@@@@@userprofiles SET ADDFORMS ='a:1:{i:0;s:7:\"default\";}'"); 
	
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'DISPLAYNUMSUMINMAINLIST', 'Yes', NOW( ) , 'When set to Yes, the total value of numeric fields will be displayed under the main entity list.')");
	

	
	Upgrade($db_ver_from, $db_ver_to, $sqla);
}


function A331TO332() {
	$db_ver_from = "3.3.1";
	$db_ver_to = "3.3.2";
	$sqla = array();
	
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entity` ADD `parent` BIGINT DEFAULT '0' NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` ADD `sort` ENUM( 'n', 'y' ) NOT NULL");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'EnableEntityRelations', 'No', NOW( ) , 'Set this value to Yes to enable entity relations.')");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'HideChildsFromMainList', 'No', NOW( ) , 'When enabled, child entities will no longer show up on the main list.')");

	array_push($sqla, "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'LDAP_SERVER', '', NOW( ) , 'The name of the LDAP server')");
	array_push($sqla, "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'LDAP_PORT', '389', NOW( ) , 'The port of the LDAP server; secure=636, non-secure=389 (Default)')");
	array_push($sqla, "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'LDAP_PREFIX', '', NOW( ) , 'The prefix to use before a username on the LDAP server. End with 1 backslash, not two.')");
	array_push($sqla, "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'LDAP_AUTO_ADD_USERS', 'NO', NOW( ) , 'Set this to Yes to auto-add an CRM-CTT useraccount when an unknown but LDAP-authenticated user logs in.')");

	array_push($sqla, "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'RSS_FEEDS', '', NOW( ) , 'The list of RSS feeds to serve. No list, no RSS.')");

	array_push($sqla, "UPDATE `PRFX@@@@@@@settings` SET `datetime` = NOW( ) ,`discription`='The method to use for authentication. ALWAYS: user must exist in CRM-CTT. HTTP REALM: already authenticated users can log in without a password (INTRANET). LDAP: authentications with an LDAP server (allso fill in LDAP_SERVER, LDAP_PORT, LDAP_PREFIX).' WHERE `setting` ='AUTH_TYPE' LIMIT 1");

	array_push($sqla, "ALTER TABLE `PRFX@@@@@@@loginusers` ADD `LIMITTOCUSTOMERS` LONGTEXT NOT NULL");
	array_push($sqla, "ALTER TABLE `PRFX@@@@@@@userprofiles` ADD `LIMITTOCUSTOMERS` LONGTEXT NOT NULL");

	Upgrade($db_ver_from, $db_ver_to, $sqla);
}

function A330TO331() {
	$db_ver_from = "3.3.0";
	$db_ver_to = "3.3.1";
	$sqla = array();
	
	//array_push($sqla,"UPDATE `PRFX@@@@@@@settings` SET `datetime` = NOW( ) ,`discription` = 'NO LONGER USED! Use miscellaneous triggers!' WHERE `setting` ='BODY_ENTITY_ADD' LIMIT 1");
	array_push($sqla,"SELECT * FROM PRFX@@@@@@@loginusers");

	Upgrade($db_ver_from, $db_ver_to, $sqla);

}

function A320TO330() {

	$db_ver_from = "3.2.0";
	$db_ver_to = "3.3.0";
	$sqla = array();
	
	// Fix the limited extra field option field

	//array_push($sqla,"UPDATE `PRFX@@@@@@@settings` SET `datetime` = NOW( ) ,`discription` = 'NO LONGER USED! Use miscellaneous triggers!' WHERE `setting` ='BODY_ENTITY_ADD' LIMIT 1");
	//array_push($sqla,"UPDATE `PRFX@@@@@@@settings` SET `datetime` = NOW( ) ,`discription` = 'NO LONGER USED! Use miscellaneous triggers!' WHERE `setting` ='BODY_ENTITY_EDIT' LIMIT 1");

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` CHANGE `options` `options` LONGTEXT NOT NULL");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'ENTITY_ADD_FORM', 'Default', NOW( ) , 'The HTML form template to use when a normal user adds an entity')");


	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'BODY_URGENTMESSAGE', '', NOW( ) , 'When set, this message will be displayed above <b>all</b> pages. Only use this for very urgent matters. ')");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'AUTH_TYPE', 'CRM-CTT Only', NOW( ) , 'The method to use for authentication. In all cases the user must exist in CRM-CTT. HTTP REALM will allow a user who is already authenticated against the webserver to log in without a password. USE ONLY FOR INTRANETS!')");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'ENTITY_EDIT_FORM', 'Default', NOW( ) , 'The HTML form template to use when a normal user edits an entity')");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'ENTITY_LIMITED_ADD_FORM', 'Default', NOW( ) , 'The HTML form template to use when a limited user adds an entity')");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'ENTITY_LIMITED_EDIT_FORM', 'Default', NOW( ) , 'The HTML form template to use when a limited user edits an entity')");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'BODY_LIMITEDHEADER', '', NOW( ) , 'This HTML template will be shown at the top of the limited interface')");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'SHOW_ADMIN_TOOLTIPS', 'Yes', NOW( ) , 'Wether or not to display tool-tips in the administrative section.')");

	array_push($sqla,"CREATE TABLE PRFX@@@@@@@userprofiles (  id int(11) NOT NULL auto_increment,  name varchar(50) NOT NULL default '',  ENTITYADDFORM varchar(50) NOT NULL default '',  ENTITYEDITFORM varchar(50) NOT NULL default '',  active enum('yes','no') NOT NULL default 'yes',  CLLEVEL varchar(50) NOT NULL default 'ro',  RECEIVEDAILYMAIL enum('No','Yes') NOT NULL default 'No',  RECEIVEALLOWNERUPDATES enum('n','y') NOT NULL default 'n',  RECEIVEALLASSIGNEEUPDATES enum('n','y') NOT NULL default 'n',  HIDEADDTAB char(1) NOT NULL default '',  HIDECSVTAB char(1) NOT NULL default '',  HIDESUMMARYTAB char(1) NOT NULL default '',  HIDEENTITYTAB char(1) NOT NULL default '',  HIDEPBTAB char(1) NOT NULL default '',  SHOWDELETEDVIEWOPTION char(1) NOT NULL default '',  HIDECUSTOMERTAB char(1) NOT NULL default '',  SAVEDSEARCHES longtext NOT NULL,  EMAILCREDENTIALS longtext NOT NULL,  PRIMARY KEY  (id),  KEY name (name),  FULLTEXT KEY SAVEDSEARCHES (SAVEDSEARCHES),  FULLTEXT KEY EMAILCREDENTIALS (EMAILCREDENTIALS)) TYPE=MyISAM COMMENT='CRM User profile definition table'");

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` ADD `PROFILE` VARCHAR( 10 ) NOT NULL AFTER `password`");

	array_push($sqla, "ALTER TABLE `PRFX@@@@@@@loginusers` ADD `ENTITYADDFORM` VARCHAR( 10 ) NOT NULL ,ADD `ENTITYEDITFORM` VARCHAR( 10 ) NOT NULL");

	array_push($sqla, "ALTER TABLE `PRFX@@@@@@@customaddons` CHANGE `name` `name` BIGINT( 20 ) DEFAULT '0' NOT NULL");

	array_push($sqla, "ALTER TABLE `PRFX@@@@@@@customaddons` DROP INDEX `name`");	
	array_push($sqla, "ALTER TABLE `PRFX@@@@@@@customaddons` DROP INDEX `name_2`");	
	array_push($sqla, "ALTER TABLE `PRFX@@@@@@@customaddons` DROP INDEX `value`");	
	array_push($sqla, "ALTER TABLE `PRFX@@@@@@@customaddons` DROP INDEX `value_2`");	
	array_push($sqla, "ALTER TABLE `PRFX@@@@@@@customaddons` ADD INDEX ( `name` )");
	
	array_push($sqla, "CREATE INDEX val ON PRFX@@@@@@@customaddons (value(20))");

	array_push($sqla, "DELETE FROM PRFX@@@@@@@settings WHERE setting='logo'");
	array_push($sqla, "DROP TABLE IF EXIST PRFX@@@@@@@users");	

	array_push($sqla, "INSERT INTO `PRFX@@@@@@@binfiles` VALUES (5470, 0, 0x3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e3c464f4e542073697a653d2b313e4a6f652773204f776e2048656c706465736b202d207469636b6574266e6273703b40454944403a204043415445474f52594020234c4f434b49434f4e23266e6273703b3c2f464f4e543e3c2f4c4547454e443e0d0a3c5441424c452077696474683d22393025223e0d0a3c54424f44593e0d0a3c54523e0d0a3c54443e0d0a3c5441424c453e0d0a3c54424f44593e0d0a3c54523e0d0a3c54442076416c69676e3d746f703e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e557365722f637573746f6d65723c2f4c4547454e443e23435553544f4d4552233c2f4649454c445345543e3c2f54443e0d0a3c54442076416c69676e3d746f703e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e537461747573266e6273703b3c2f4c4547454e443e23535441545553233c2f4649454c445345543e203c2f54443e0d0a3c54442076416c69676e3d746f703e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e5072696f726974793c2f4c4547454e443e235052494f52495459233c2f4649454c445345543e203c2f54443e0d0a3c54442076416c69676e3d746f703e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e53686f72742070726f626c656d206465736372697074696f6e266e6273703b3c2f4c4547454e443e2343415445474f525923203c2f4649454c445345543e203c2f54443e0d0a3c54443e3c2f54443e3c2f54523e3c2f54424f44593e3c2f5441424c453e0d0a3c5441424c452063656c6c53706163696e673d312063656c6c50616464696e673d322077696474683d22313030252220626f726465723d303e0d0a3c54424f44593e0d0a3c54523e0d0a3c54443e23434f4e54454e545323203c2f54443e0d0a3c54443e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e4f776e65723c2f4c4547454e443e234f574e455223203c2f4649454c445345543e203c42523e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e41737369676e6565266e6273703b3c2f4c4547454e443e2341535349474e454523203c2f4649454c445345543e200d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e4475652064617465266e6273703b3c2f4c4547454e443e234455454441544523203c2f4649454c445345543e200d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e4475652074696d65266e6273703b3c2f4c4547454e443e2344554554494d4523203c2f4649454c445345543e3c42523e234a4f55524e414c49434f4e2320235245504f525449434f4e23202350444649434f4e23202341435449434f4e2320234c4f434b49434f4e2320234152524f5753233c2f54443e3c2f54523e3c2f54424f44593e3c2f5441424c453e3c42523e0d0a3c5441424c452077696474683d22333025223e0d0a3c54424f44593e0d0a3c54523e0d0a3c54443e526561642d6f6e6c7920746f206f746865722075736572733c2f54443e0d0a3c54443e23524541444f4e4c59424f5823266e6273703b3c2f54443e3c2f54523e0d0a3c54523e0d0a3c54443e507269766174653c2f54443e0d0a3c54443e2350524956415445424f58233c2f54443e3c2f54523e0d0a3c54523e0d0a3c54443e44656c657465643c2f54443e0d0a3c54443e2344454c455445424f58233c2f54443e3c2f54523e3c2f54424f44593e3c2f5441424c453e0d0a3c5441424c453e0d0a3c54424f44593e0d0a3c54523e0d0a3c544420636f6c5370616e3d363e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e4174746163682066696c65266e6273703b3c2f4c4547454e443e2346494c45424f582320266e6273703b266e6273703b266e6273703b266e6273703b203c2f4649454c445345543e200d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e43757272656e742066696c6573266e6273703b3c2f4c4547454e443e2346494c454c4953542320266e6273703b266e6273703b266e6273703b266e6273703b203c2f4649454c445345543e3c2f54443e3c2f54443e3c2f54523e2353415645425554544f4e23203c2f54443e3c2f54523e3c2f54424f44593e3c2f5441424c453e3c2f54443e3c2f54523e3c2f54424f44593e3c2f5441424c453e3c2f4649454c445345543e20, 'Joes helpdesk - edit entity form template (example)', '2005-08-07 20:01:52', 0, 'TEMPLATE_HTML_FORM', 'Hidde Fennema', 'in', 0, 'entity', 'Joes edit entity template (example)')");
	array_push($sqla, "INSERT INTO `PRFX@@@@@@@binfiles` VALUES (5473, 0, 0x3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e3c464f4e542073697a653d2b313e4a6f652773204f776e2048656c706465736b202d206164642061206e6577200d0a7469636b65743c2f464f4e543e3c2f4c4547454e443e0d0a3c5441424c452077696474683d22393025223e0d0a3c54424f44593e0d0a3c54523e0d0a3c54443e0d0a3c5441424c453e0d0a3c54424f44593e0d0a3c54523e0d0a3c54442076416c69676e3d746f703e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e557365722f637573746f6d65723c2f4c4547454e443e23435553544f4d4552233c2f4649454c445345543e3c2f54443e0d0a3c54442076416c69676e3d746f703e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e537461747573266e6273703b3c2f4c4547454e443e23535441545553233c2f4649454c445345543e203c2f54443e0d0a3c54442076416c69676e3d746f703e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e5072696f726974793c2f4c4547454e443e235052494f52495459233c2f4649454c445345543e203c2f54443e0d0a3c54442076416c69676e3d746f703e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e53686f72742070726f626c656d206465736372697074696f6e266e6273703b3c2f4c4547454e443e2343415445474f525923200d0a3c2f4649454c445345543e203c2f54443e0d0a3c54443e3c2f54443e3c2f54523e3c2f54424f44593e3c2f5441424c453e0d0a3c5441424c452063656c6c53706163696e673d312063656c6c50616464696e673d322077696474683d22313030252220626f726465723d303e0d0a3c54424f44593e0d0a3c54523e0d0a3c54443e23434f4e54454e545323203c2f54443e0d0a3c54443e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e4f776e65723c2f4c4547454e443e234f574e455223203c2f4649454c445345543e203c42523e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e41737369676e6565266e6273703b3c2f4c4547454e443e2341535349474e454523203c2f4649454c445345543e200d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e4475652064617465266e6273703b3c2f4c4547454e443e234455454441544523203c2f4649454c445345543e200d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e4475652074696d65266e6273703b3c2f4c4547454e443e2344554554494d4523200d0a3c2f4649454c445345543e3c2f54443e3c2f54523e3c2f54424f44593e3c2f5441424c453e3c42523e0d0a3c5441424c452077696474683d22333025223e0d0a3c54424f44593e0d0a3c54523e0d0a3c54443e526561642d6f6e6c7920746f206f746865722075736572733c2f54443e0d0a3c54443e23524541444f4e4c59424f5823266e6273703b3c2f54443e3c2f54523e0d0a3c54523e0d0a3c54443e507269766174653c2f54443e0d0a3c54443e2350524956415445424f58233c2f54443e3c2f54523e0d0a3c54523e0d0a3c54443e3c2f54443e0d0a3c54443e3c2f54443e3c2f54523e3c2f54424f44593e3c2f5441424c453e0d0a3c5441424c453e0d0a3c54424f44593e0d0a3c54523e0d0a3c544420636f6c5370616e3d363e0d0a3c4649454c445345543e3c4c4547454e4420616c69676e3d6c6566743e4174746163682066696c65266e6273703b3c2f4c4547454e443e2346494c45424f5823200d0a266e6273703b266e6273703b266e6273703b266e6273703b203c2f4649454c445345543e203c2f54443e3c2f54443e3c2f54523e2353415645425554544f4e23200d0a3c2f54443e3c2f54523e3c2f54424f44593e3c2f5441424c453e3c2f54443e3c2f54523e3c2f54424f44593e3c2f5441424c453e3c2f4649454c445345543e20, 'Joes helpdesk - new entity form template (example)', '2005-08-07 16:22:43', 0, 'TEMPLATE_HTML_FORM', 'Hidde Fennema', 'in', 0, 'entity', 'Joes helpdesk - new entity form template (example)')");

  
	Upgrade($db_ver_from, $db_ver_to, $sqla);
}

function A310TO320() {
	$db_ver_from = "3.1.0";
	$db_ver_to = "3.2.0";
	$sqla = array();

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'PersonalTabs', '', NOW( ) , 'Set this to Yes to disable the main entity comment field')");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` CHANGE `options` `options` LONGTEXT NOT NULL");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'DisableCommentField', 'No', NOW( ) , 'Set this to Yes to disable the main entity comment field')");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'REQUIREDDEFAULTFIELDS', '', NOW( ) , 'Set this to Yes to disable the main entity comment field')");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'EMAILINBOX', '', NOW( ) , 'The credentials for the read-only access to an POP3 e-mail inbox')");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` ADD `EMAILCREDENTIALS` LONGTEXT NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` ADD FULLTEXT (`EMAILCREDENTIALS`)");

	// Database optimisation

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@binfiles` DROP INDEX `fileid`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@binfiles` DROP INDEX `type`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customaddons` DROP INDEX `eid`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customaddons` DROP INDEX `type`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customaddons` DROP INDEX `deleted_2`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customaddons` DROP INDEX `value_2`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customer` DROP INDEX `id`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entity` DROP INDEX `id`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` DROP INDEX `id`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@sessions` DROP INDEX `id`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@webdav_locks` DROP INDEX `token`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@webdav_locks` DROP INDEX `path_2`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@webdav_locks` DROP INDEX `path_3`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@webdav_properties` DROP INDEX `path`");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@binfiles` TYPE=MyISAM, COMMENT='CRM Binairy files'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@calendar` TYPE=MyISAM, COMMENT='CRM Calendar entries'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customaddons` TYPE=MyISAM, COMMENT='CRM Extra fields sequential table'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customer` TYPE=MyISAM, COMMENT='CRM Main customer table'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entity` TYPE=MyISAM, COMMENT='CRM Main entity table'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` TYPE=MyISAM, COMMENT='CRM Extra field definitions'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@help` TYPE=MyISAM, COMMENT='CRM Help contents table'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@journal` TYPE=MyISAM, COMMENT='CRM Entity/customer journal'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` TYPE=MyISAM, COMMENT='CRM User definition table'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@phonebook` TYPE=MyISAM, COMMENT='CRM Phone book table'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@priorityvars` TYPE=MyISAM, COMMENT='CRM Priority definitions table'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@sessions` TYPE=MyISAM, COMMENT='CRM Session table'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@settings` TYPE=MyISAM, COMMENT='CRM Main settings table'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@statusvars` TYPE=MyISAM, COMMENT='CRM Status definitions table'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@triggers` TYPE=MyISAM, COMMENT='CRM Entity value change trigger table'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@uselog` TYPE=MyISAM, COMMENT='CRM Main activity log table'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@webdav_locks` TYPE=MyISAM, COMMENT='CRM Webdav file locks table'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@webdav_properties` TYPE=MyISAM, COMMENT='CRM Webdav properties'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customaddons` MODIFY COLUMN `name` VARCHAR(50) NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` MODIFY COLUMN `name` VARCHAR(250) NOT NULL");
	//array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` MODIFY COLUMN `options` VARCHAR(250) NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` MODIFY COLUMN `defaultval` VARCHAR(250) DEFAULT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@uselog` MODIFY COLUMN `qs` LONGTEXT NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@calendar` ADD KEY `basicdate` (`basicdate`)");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@calendar` ADD KEY `datum` (`datum`)");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customaddons` ADD KEY `name` (`name`)");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entitylocks` ADD KEY `lockepoch` (`lockepoch`)");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@journal` ADD KEY `type` (`type`)");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` ADD KEY `name` (`name`)");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@priorityvars` ADD KEY `varname` (`varname`)");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@statusvars` ADD KEY `varname` (`varname`)");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@triggers` ADD KEY `to_value` (`to_value`)");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@triggers` ADD KEY `onchange` (`onchange`)");

	Upgrade($db_ver_from, $db_ver_to, $sqla);
}

function A300TO310() {
	$db_ver_from = "3.0.0";
	$db_ver_to = "3.1.0";
	$sqla = array();

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'MailMethod', 'smtp', NOW( ) , 'The method to use for sending mail. Can be either sendmail, mail (=PHP mail) or smtp.')");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'MailUser', '', NOW( ) , 'The username of your authenticated SMTP-server (only when using authenticated SMTP)')");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'MailPass', '', NOW( ) , 'The password of your authenticated SMTP-server (only when using authenticated SMTP)')");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'UseWaitingAndDoesntBelongHere', 'No', NOW( ) , 'Set this value to Yes to enable the (old) waiting and doesnt belong here fields')");

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` ADD `SAVEDSEARCHES` LONGTEXT NOT NULL");

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` ADD FULLTEXT (`SAVEDSEARCHES` )");

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` ADD `defaultval` VARCHAR( 250 ) NOT NULL");

    array_push($sqla,"ALTER TABLE `PRFX@@@@@@@ejournal` ADD INDEX ( `eid` )");

	array_push($sqla,"DELETE FROM `PRFX@@@@@@@settings` WHERE setting='REQUIREDDEFAULTFIELDS'");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'REQUIREDDEFAULTFIELDS', 'No', NOW( ) , 'SHOULD NOT BE VISIBLE')");

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` ADD `EMAILCREDENTIALS` LONGTEXT NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` ADD FULLTEXT (`EMAILCREDENTIALS`)");
	
	Upgrade($db_ver_from, $db_ver_to, $sqla);
}


function A262TO300() {
	$db_ver_from = "2.6.2";
	$db_ver_to = "3.0.0";
	$sqla = array();
	
	array_push($sqla,"CREATE TABLE `PRFX@@@@@@@cache` (  `stashid` bigint(20) NOT NULL auto_increment,  `epoch` varchar(20) default NULL,  `value` longtext NOT NULL,  PRIMARY KEY  (`stashid`),  FULLTEXT KEY `value` (`value`)) TYPE=MyISAM COMMENT='CRM Query cache table'");

	array_push($sqla,"CREATE TABLE `PRFX@@@@@@@extrafields` (`id` BIGINT NOT NULL AUTO_INCREMENT ,`ordering` MEDIUMINT NOT NULL ,`tabletype` ENUM( 'entity', 'customer' ) NOT NULL , `hidden` ENUM( 'n', 'y' ) NOT NULL , `location` ENUM( 'A', 'B', 'C', 'D', 'E' ) NOT NULL, deleted ENUM( 'n', 'y' ) NOT NULL ,`fieldtype` VARCHAR( 50 ) NOT NULL ,`name` VARCHAR( 250 ) NOT NULL ,`options` VARCHAR( 250 ) NOT NULL ,UNIQUE (`id` ),FULLTEXT (`name` ,`options` )) COMMENT = 'CRM Extra fields'");

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` CHANGE `location` `location` VARCHAR( 40 ) DEFAULT '' NOT NULL");

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` CHANGE `options` `options` LONGTEXT NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` CHANGE `name` `name` LONGTEXT NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` ADD `forcing` ENUM( 'n', 'y' ) NOT NULL");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` CHANGE `hidden` `hidden` ENUM( 'n', 'y', 'a' ) DEFAULT 'n' NOT NULL");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'FORCEDFIELDSTEXT', 'This message is not configured (see admin section). Probably you missed some fields in your form. ', NOW( ) , 'The message which is displayed when a user did not fill in all required form fields.')");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'REQUIREDDEFAULTFIELDS', 'No', NOW( ) , 'SHOULD NOT BE VISIBLE')");

	array_push($sqla,"CREATE TABLE `PRFX@@@@@@@entitylocks` (`lockid` BIGINT NOT NULL AUTO_INCREMENT ,`lockon` BIGINT NOT NULL ,`lockby` BIGINT NOT NULL ,`lockepoch` VARCHAR( 30 ) NOT NULL ,PRIMARY KEY ( `lockid` ) ) COMMENT = 'CRM Entity record locks'");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entitylocks` ADD INDEX ( `lockon` )");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` ADD INDEX ( `location` )"); 
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@extrafields` ADD INDEX ( `tabletype` )");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'EnableEntityLocking', 'No', NOW( ) , 'Set this to Yes to enable entity locking to prevent two people from editing the same entity')");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'DFT_FOREGROUND_COLOR', '#c60', NOW( ) , 'The color of links')");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'DFT_FORM_COLOR', '#c60', NOW( ) , 'The color form elements and values')");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'DFT_PLAIN_COLOR', '#000', NOW( ) , 'The color of normal, non-linked, non-form text')");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'DFT_LEGEND_COLOR', '#3366FF', NOW( ) , 'The color of fieldset legends')");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'DFT_FONT', 'MS Shell DLG', NOW( ) , 'The main font')");
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'DFT_FONT_SIZE', '11', NOW( ) , 'The main font size')");


//EnableEntityLocking

	Upgrade_Specialfor300($db_ver_from, $db_ver_to, $sqla);
}

function A261TO262() {
	$db_ver_from = "2.6.1";
	$db_ver_to = "2.6.2";
	$sqla = array();
	
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@triggers` ADD `report_fileid` BIGINT NOT NULL");
	Upgrade($db_ver_from, $db_ver_to, $sqla);
}

function A260TO261() {
	$db_ver_from = "2.6.0";
	$db_ver_to = "2.6.1";
	$sqla = array();
	
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'DisplayNOToptioninfilters', 'No', NOW( ) , 'Set this value to Yes to have all filter drop-down boxes also contain logical NOT-operands, like status NOT open.')");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'EXTRAFIELDLOCATION', 'B', NOW( ) , 'The location on the main edit entity page were the extra field boxes will appear.')");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'AUTOASSIGNINCOMINGENTITIES', 'No', NOW( ) , 'Set this option to Yes to automatically assign incoming entities to the owner of the customer.')");

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@triggers` ADD `attach` ENUM( 'n', 'y' ) NOT NULL");

	Upgrade($db_ver_from, $db_ver_to, $sqla);
}


function A253TO260() {
	$db_ver_from = "2.5.3";
	$db_ver_to = "2.6.0";
	$sqla = array();
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'EnableSingleEntityInvoicing', 'No', NOW( ) , 'Set this value to Yes to enable per-entity invoicing using the invoice icon on the main edit entity page.')");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customer` ADD `readonly` ENUM( 'no', 'yes' ) NOT NULL AFTER `id`");
	
	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'PDF-ExtraFieldsInTable', 'No', NOW( ) , 'Set this value to Yes to have extra fields in PDF reports show up in a table instead of each value being printed on a new line.')");

	array_push($sqla,"INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'EnableEntityReporting', 'Yes', NOW( ) , 'Set this value to Yes to be able to create per-entity or batch RTF reports (a word-icon will appear on the edit entity page and a link will be added to the main page)')");

	$fp=@fopen("sample_entity_report_template.rtf","r");
	$filecontent=@fread($fp,@filesize("sample_entity_report_template.rtf"));
	@fclose($fp);
	$filecontent = addslashes($filecontent);

	array_push($sqla,"INSERT INTO PRFX@@@@@@@binfiles(koppelid,content,filename,filesize,filetype,username) VALUES('0','" . $filecontent . "','sample_entity_report_template.rtf','" . @filesize("sample_entity_report_template.rtf") . "','TEMPLATE_REPORT','Upgrade to 2.6.0')");
	
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','go','Go!')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','attachindividualtoentity','Attach individual files to entity')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','attachindividualtocustomer','Attach individual files to customer dossier')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','includelog','Include log at end of document')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','rtftemplate','RTF Template')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','createreport','Create an entity report for entity')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','createinvoice','Generate invoice over entity')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','deap','Delete entities after processing')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','apsest','After processing, set entity status to')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','apseot','After processing, set entity owner to')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','apseat','After processing, set entity assignee to')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','apsrft','After processing, set readonly flag to')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','startdate','Start date')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','enddate','End date')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','defVAT','Default VAT')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','IHSwarning','IHS field not found, all qty/hours will be defaulted to 1')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','geninv','Generate invoices')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','selectsingle','Select a single customer')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','abdnya','All but inserted (not yet assigned)')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','ondaa','Only non-deleted and assigned')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','delafterproc','Delete entities after processing')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','onlyactive','Only active')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','donothing','&lt;do nothing&gt;')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','createreports','Generate entity reports')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','lefae','Leave empty for all entities')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','invoiceandmailmerge','Invoice & mailmerge')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','genmailverbose','Generate invoices and mailmerges')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','entreportverbose','Batch-generate entity RTF reports')");
	array_push($sqla,"INSERT INTO PRFX@@@@@@@languages(LANGID,TEXTID,TEXT) VALUES('ENGLISH','alled','All except deleted')");

	array_push($sqla,"CREATE TABLE `PRFX@@@@@@@triggers` (  `tid` bigint(20) NOT NULL auto_increment,  `onchange` varchar(200) NOT NULL default '',  `action` varchar(50) NOT NULL default '',  `to_value` varchar(100) NOT NULL default '',  `template_fileid` bigint(20) NOT NULL default '0',  PRIMARY KEY  (`tid`)) TYPE=MyISAM COMMENT='Event triggers for entity changes'");

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@binfiles` ADD `file_subject` VARCHAR( 250 ) NOT NULL");

	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` ADD SHOWDELETEDVIEWOPTION VARCHAR( 1 ) NOT NULL ");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@loginusers` ADD HIDECUSTOMERTAB VARCHAR( 1 ) NOT NULL ");
	array_push($sqla,"ALTER TABLE PRFX@@@@@@@customaddons ADD FULLTEXT KEY `name_crm` (`name`)");
	array_push($sqla,"ALTER TABLE PRFX@@@@@@@customaddons ADD KEY `type_crm` (`type`)");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@settings` ADD INDEX ( `setting` )");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@binfiles` ADD INDEX ( `type` )");
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entity` ADD INDEX ( `assignee` )"); 
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entity` ADD INDEX ( `owner` )"); 
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entity` ADD INDEX ( `sqldate` )"); 
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entity` ADD INDEX ( `CRMcustomer` )"); 
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entity` ADD INDEX ( `deleted` )"); 
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entity` ADD INDEX ( `openepoch` )"); 
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@entity` ADD INDEX ( `closeepoch` )"); 
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customaddons` ADD INDEX ( `deleted` )"); 
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customer` ADD INDEX ( `custname` )"); 
	array_push($sqla,"ALTER TABLE `PRFX@@@@@@@customer` ADD INDEX ( `active` )"); 

	Upgrade($db_ver_from, $db_ver_to, $sqla);
}

function A252TO253() {
	$db_ver_from = "2.5.2";
	$db_ver_to = "2.5.3";
	$sqla[0] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'ForceSecureHTTP', 'No', NOW( ) , 'If set to yes, CRM-CTT will redirect the user to the HTTPS equivalent of the URL he/she is using, to force secure browsing. Your webserver must be configured to accept this.')";
	$sqla[1] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'BODY_MainPageMessage', '', NOW( ) , 'When set, this message will be displayed on the main page.')";
	$sqla[2] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'InvoiceNumberPrefix', '[unconfigured]', NOW( ) , 'Some text to prefix invoice numbers with')";
	$sqla[3] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'NextInvoiceNumberCounter', '0', NOW( ) , 'The invoice number counter (not accessable)')";
	$sqla[4] = "ALTER TABLE `PRFX@@@@@@@binfiles` ADD `type` ENUM( 'entity', 'cust' ) DEFAULT 'entity' NOT NULL";
	$sqla[5] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'InvoiceNumberPrefix', '[unconfigured]', NOW( ) , 'Some text to prefix invoice numbers with')";

	$fp=@fopen("sample_invoice_template_multiple_VAT.rtf","r");
	$filecontent=@fread($fp,@filesize("sample_invoice_template_multiple_VAT.rtf"));
	@fclose($fp);
	$filecontent = addslashes($filecontent);

	$sqla[6] = "INSERT INTO PRFX@@@@@@@binfiles(koppelid,content,filename,filesize,filetype,username) VALUES('0','" . $filecontent . "','sample_invoice_template_multiple_VAT.rtf','" . @filesize("sample_invoice_template_multiple_VAT.rtf") . "','TEMPLATE_INVOICE','Upgrade to 2.5.3')";

	$fp=@fopen("sample_invoice_template_single_VAT.rtf","r");
	$filecontent=@fread($fp,@filesize("sample_invoice_template_single_VAT.rtf"));
	@fclose($fp);
	$filecontent = addslashes($filecontent);

	$sqla[7] = "INSERT INTO PRFX@@@@@@@binfiles(koppelid,content,filename,filesize,filetype,username) VALUES('0','" . $filecontent . "','sample_invoice_template_single_VAT.rtf','" . @filesize("sample_invoice_template_single_VAT.rtf") . "','TEMPLATE_INVOICE','Upgrade to 2.5.3')";

	Upgrade($db_ver_from, $db_ver_to, $sqla);
}
function A251TO252() {
	$db_ver_from = "2.5.1";
	$db_ver_to = "2.5.2";

	$sqla[0] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('ShowMainPageLinks', 'aHR0cDovL2NybS5pdC1jb21iaW5lLmNvbQ==', NOW( ) , 'Some links to show on the main page. Leave empty for no links')";
	$sqla[1] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('EnableMailMergeAndInvoicing', 'No', NOW( ) , 'Set to Yes to enable mail merges and invoicing (even then, only for admins)')";

	$fp=@fopen("sample_invoice_template.rtf","r");
	$filecontent=@fread($fp,@filesize("sample_invoice_template.rtf"));
	@fclose($fp);
	$filecontent = addslashes($filecontent);

	$sqla[2] = "INSERT INTO PRFX@@@@@@@binfiles(koppelid,content,filename,filesize,filetype,username) VALUES('0','" . $filecontent . "','sample_invoice_template.rtf','" . @filesize("sample_invoice_template.rtf") . "','TEMPLATE_INVOICE','Upgrade to 2.5.2')";

	$fp=@fopen("sample_mailmerge_template.rtf","r");
	$filecontent=@fread($fp,@filesize("sample_mailmerge_template.rtf"));
	@fclose($fp);
	$filecontent = addslashes($filecontent);

	$sqla[3] = "INSERT INTO PRFX@@@@@@@binfiles(koppelid,content,filename,filesize,filetype,username) VALUES('0','" . $filecontent . "','sample_mailmerge_template.rtf','" . @filesize("sample_mailmerge_template.rtf") . "','TEMPLATE_MAILMERGE','Upgrade to 2.5.2')";

	$sqla[4] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('DefaultVAT', '19', NOW( ) , 'Default VAT percentage (only for use with invoicing)')";

	unset($filecontent);

	$sqla[5] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('subject_new_entity', 'A new entity was added to repository @TITLE@ (@CATEGORY@)', NOW( ) , 'The subject of the mail which is send when a new entity is added')";

	$sqla[6] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('subject_customer_couple', 'Your customer got a new entity coupled in repository @TITLE@', NOW( ) , 'The subject of the mail which is send to a customer owner when a new entity is coupled to his/her customer')";

	$sqla[7] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('subject_update_entity', 'One of your entities was updated in repository @TITLE@', NOW( ) , 'The subject of the mail which is send to a user owner when his/her entity was updated')";

	$sqla[8] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('subject_alarm', 'Alarm notification for entity @ENTITYID@ (@CATEGORY@)', NOW( ) , 'The subject of the mail which is send to a user owner when his/her entity reaches an alarm date')";

	$sqla[9] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'BODY_EMAILINSERT_REPLY', '<P><FONT face=\"MS Shell Dlg\" size=2><B>Your e-mail was added to repository @TITLE@</B><BR></P> <P> The number is : @EID@ Number of attachments saved: @NUM_ATTM@ </P>', NOW( ) , 'The body of the e-mail which is send as a reply to people who use the email_in script to log an entity')";

	$sqla[10] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'SUBJECT_EMAILINSERT_REPLY', 'Your e-mail to CRM-CTT was saved under number @EID@ in repository @TITLE@', NOW( ) , 'The subject of the e-mail which is send as a reply to people who use the email_in script to log an entity')";

	Upgrade($db_ver_from, $db_ver_to, $sqla);


}

function A250TO251() {
	$db_ver_from = "2.5.0";
	$db_ver_to = "2.5.1";

	$sqla[0] = "ALTER TABLE `PRFX@@@@@@@loginusers` ADD `HIDEADDTAB` VARCHAR( 1 ) NOT NULL ";
	$sqla[1] = "ALTER TABLE `PRFX@@@@@@@loginusers` ADD `HIDECSVTAB` VARCHAR( 1 ) NOT NULL ";
	$sqla[2] = "ALTER TABLE `PRFX@@@@@@@loginusers` ADD `HIDESUMMARYTAB` VARCHAR( 1 ) NOT NULL ";
	$sqla[3] = "ALTER TABLE `PRFX@@@@@@@loginusers` ADD `HIDEENTITYTAB` VARCHAR( 1 ) NOT NULL ";
	$sqla[4] = "ALTER TABLE `PRFX@@@@@@@loginusers` ADD `HIDEPBTAB` VARCHAR( 1 ) NOT NULL ";
	$sqla[5] = "ALTER TABLE `PRFX@@@@@@@entity` ADD `duetime` VARCHAR( 4 ) NOT NULL ";
	$sqla[6] = "ALTER TABLE `PRFX@@@@@@@ejournal` ADD `duetime` VARCHAR( 4 ) NOT NULL ";

	$sqla[7] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('HIDEADDTAB', 'No', NOW( ) , 'Set this to Yes to hide the second tab used to add entities')";
	$sqla[8] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('HIDECSVTAB', 'No', NOW( ) , 'Set this to Yes to hide the CSV tab used to download CRM-CTT exports')";
	$sqla[9] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('HIDEPBTAB', 'No', NOW( ) , 'Set this to Yes to hide the phone book tab')";
	$sqla[10] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('HIDESUMMARYTAB', 'No', NOW( ) , 'Set this to Yes to hide the summary tab')";
	$sqla[11] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('HIDEENTITYTAB', 'No', NOW( ) , 'Set this to Yes to hide main entity list tab')";
	$sqla[12] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('CAL_MINHOUR', '7', NOW( ) , 'Starting hour of day, used for scheduling enties')";
	$sqla[13] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('CAL_MAXHOUR', '18', NOW( ) , 'Ending hour of day, used for scheduling enties (24h format: for 6pm use 18')";
	$sqla[14] = "INSERT INTO `PRFX@@@@@@@settings` (`setting` , `value` , `datetime` , `discription` ) VALUES ('CAL_USEWEEKEND', 'No', NOW( ) , 'Wheter or not to also show the weekend days in the week view of the calendar')";

	Upgrade($db_ver_from, $db_ver_to, $sqla);

}

function A246TO25() {
	$db_ver_from = "2.4.6";
	$db_ver_to = "2.5.0";

	$sqla[0] = "ALTER TABLE PRFX@@@@@@@binfiles ADD INDEX(checked)";
	$sqla[1] = "ALTER TABLE PRFX@@@@@@@entity ADD INDEX (duedate)";
	$sqla[2] = "ALTER TABLE PRFX@@@@@@@loginusers ADD `RECEIVEALLOWNERUPDATES` ENUM( 'n', 'y' ) NOT NULL";
	$sqla[3] = "ALTER TABLE PRFX@@@@@@@loginusers ADD `RECEIVEALLASSIGNEEUPDATES` ENUM( 'n', 'y' ) NOT NULL ";
	
	$val = ini_get("session.save_path");

	$sqla[4] = "INSERT INTO PRFX@@@@@@@settings ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'TMP_FILE_PATH', '" . $val . "', NOW( ) , 'The path to the directory where CRM-CTT (the user under which your webserver runs) can store temporary files.')";

	$sqla[5] = "ALTER TABLE PRFX@@@@@@@ejournal ADD `private` ENUM( 'n', 'y' ) NOT NULL";
	$sqla[6] = "ALTER TABLE PRFX@@@@@@@entity ADD `private` ENUM( 'n', 'y' ) NOT NULL";

	$sqla[7] = "INSERT INTO PRFX@@@@@@@settings ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ( '', 'ALSO_PROCESS_DELETED', 'No', NOW( ) , 'Set this option to Yes if you want the duedate notify script to also process entities on their duedate, even if the entity is deleted.')";

	Upgrade($db_ver_from, $db_ver_to, $sqla);

}

function A245TO246() {
	$db_ver_from = "2.4.5";
	$db_ver_to = "2.4.6";

	$sqla[0] = "INSERT INTO PRFX@@@@@@@settings ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'STANDARD_TEXT', '', NOW( ) , 'A list of standard comments which users can automatically insert as a reply in entities. Leave empty for no standard comments.')";

	$sqla[1] = "INSERT INTO PRFX@@@@@@@settings ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'CUSTOMER_LIST_TRESHOLD', '150', NOW( ) , 'The number of customers listed on the main customers page. If this number of customers is exceeded, the list will not appear for bandwith reasons.')";

	$sqla[2] = "ALTER TABLE `PRFX@@@@@@@entity` ADD `openepoch` VARCHAR( 30 ) NOT NULL ,ADD `closeepoch` VARCHAR( 30 ) NOT NULL";

	Upgrade($db_ver_from, $db_ver_to, $sqla);
}

function A244TO245() {
	$db_ver_from = "2.4.4";
	$db_ver_to = "2.4.5";
	$sqla[0] = "ALTER TABLE `PRFX@@@@@@@entity` ADD `notify_assignee` ENUM( 'n', 'y' ) NOT NULL";
	$sqla[1] = "ALTER TABLE `PRFX@@@@@@@entity` ADD `notify_owner` ENUM( 'n', 'y' ) NOT NULL";
	$sqla[2] = "ALTER TABLE `PRFX@@@@@@@ejournal` ADD `notify_assignee` ENUM( 'n', 'y' ) NOT NULL";
	$sqla[3] = "ALTER TABLE `PRFX@@@@@@@ejournal` ADD `notify_owner` ENUM( 'n', 'y' ) NOT NULL";

	$sqla[4] = "INSERT INTO PRFX@@@@@@@settings ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'BODY_ENTITY_CUSTOMER_ADD', 'You are registerd to customer @CUSTOMER@. Entity @ENTITYID@ was just coupled to that customer, so you might have to do something.', NOW( ) , 'The body of the e-mail which is send to the customer_owner when an entity (new or existing) is coupled to that customer, and the email_customer_upon_action checkbox in the customer properties is checked.')";

	$sqla[5] = "INSERT INTO PRFX@@@@@@@settings ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'BlockAllCSVDownloads', 'No', NOW( ) , 'Set this value to Yes if you want to block all CSV/Excel downloads for all users except for administrators.')";
	Upgrade($db_ver_from, $db_ver_to, $sqla);
}
function A243TO244() {
	$db_ver_from = "2.4.3";
	$db_ver_to = "2.4.4";
	$sqla[0] = "SELECT * FROM `PRFX@@@@@@@loginusers`";
	Upgrade($db_ver_from, $db_ver_to, $sqla);
}

function A242TO243() {
	$db_ver_from = "2.4.2";
	$db_ver_to = "2.4.3";
	$sqla[0] = "ALTER TABLE `PRFX@@@@@@@loginusers` ADD `RECEIVEDAILYMAIL` ENUM( 'No', 'Yes' ) NOT NULL";
	$sqla[1] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'LetUserEditOwnProfile', 'Yes', NOW( ) , 'Set this option to \'Yes\' to enable user to change their passwords, edit their full name, and select wether or not they want to receive the daily entity overwiew email.')";
	$sqla[2] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'EnableRepositorySwitcher', 'Yes', NOW( ) , 'Set this option to \'Yes\' to enable a user to dynamically switch between repositories in which the same users exist with the same password. \'No\' disables this, \'Admin\' enables it only for admins.')";
	$sqla[3] = "ALTER TABLE `PRFX@@@@@@@languages` ADD INDEX ( `LANGID` )";

	Upgrade($db_ver_from, $db_ver_to, $sqla);
}

function A241TO242() {
	$db_ver_from = "2.4.1";
	$db_ver_to = "2.4.2";
	$sqla[0] = "ALTER TABLE PRFX@@@@@@@customaddons ADD KEY(deleted)";
	$sqla[1] = "ALTER TABLE PRFX@@@@@@@customaddons ADD KEY(eid)";
	$sqla[2] = "ALTER TABLE PRFX@@@@@@@customaddons ADD KEY(value(20))";
	$sqla[3] = "ALTER TABLE PRFX@@@@@@@customaddons ADD KEY(type)";
	$sqla[4] = "ALTER TABLE PRFX@@@@@@@customaddons ADD KEY(name(20))";
	$sqla[5] = "ALTER TABLE PRFX@@@@@@@customer CHANGE `contact_phone` `contact_phone` VARCHAR( 50 ) NOT NULL";
	$sqla[6] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'AutoInsertDateTime', 'No', NOW( ) , 'Enter Yes of you would like the date and time information inserted automatically when adding text to an entity.')";
	Upgrade($db_ver_from, $db_ver_to, $sqla);
}

function A24TO241() {
	$db_ver_from = "2.4.0";		
	$db_ver_to = "2.4.1";

	$sqla[0] = "ALTER TABLE PRFX@@@@@@@uselog ADD INDEX(user)";
	$sqla[1] = "INSERT INTO PRFX@@@@@@@settings ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'CustomerListColumnsToShow', 'a:5:{s:2:\"id\";b:1;s:11:\"cb_custname\";b:1;s:10:\"cb_contact\";b:1;s:16:\"cb_contact_phone\";b:1;s:9:\"cb_active\";b:1;}', NOW( ) , 'The columns to show in the customer list')";
	$sqla[2] = "INSERT INTO PRFX@@@@@@@settings ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'ShowSaveAsNewEntityButton', 'Yes', NOW( ) , 'Yes to show the Save As New Entity button, no to hide it.')";
	$sqla[3] = "INSERT INTO `PRFX@@@@@@@languages` ( `id` , `LANGID` , `TEXTID` , `TEXT` ) VALUES ('', 'ENGLISH', 'saveasnewentity', 'Save as new entity')";
	$sqla[4] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'AutoCompleteCategory', 'Yes', NOW( ) , 'Enter Yes of you would like type-ahead functionality in the category field on the main entity page')";
	Upgrade($db_ver_from, $db_ver_to, $sqla);

}

function A23TO24() {
	$db_ver_from = "2.3.0";		
	$db_ver_to = "2.4.0";
	$sqla[0] = "INSERT INTO `PRFX@@@@@@@languages` ( `id` , `LANGID` , `TEXTID` , `TEXT` ) VALUES ('', 'ENGLISH', 'stillchecked2', '. Please stop editing this file before trying to check it in.')";
	$sqla[1] = "ALTER TABLE `PRFX@@@@@@@binfiles` ADD `checked` ENUM( 'in', 'out' ) NOT NULL";
	$sqla[2] = "ALTER TABLE `PRFX@@@@@@@binfiles` ADD `checked_out_by` INT NOT NULL";
	$sqla[3] = "ALTER TABLE `PRFX@@@@@@@loginusers` ADD `LASTFILTER` LONGTEXT NOT NULL";
	$sqla[4] = "ALTER TABLE `PRFX@@@@@@@loginusers` ADD `LASTSORT` VARCHAR(50) NOT NULL"; 
	$sqla[5] = "ALTER TABLE `PRFX@@@@@@@customaddons` CHANGE `name` `name` LONGTEXT NOT NULL";
	$sqla[6] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'EnableWebDAVSubsystem', 'No', NOW( ) , 'Yes to enable the WebDAV subsystem, no to disable it')";
	$sqla[7] = "CREATE TABLE `PRFX@@@@@@@webdav_locks` (  `token` varchar(255) NOT NULL default '',  `path` varchar(200) NOT NULL default '',  `expires` int(11) NOT NULL default '0',  `owner` varchar(200) default NULL,  `recursive` int(11) default '0',  `writelock` int(11) default '0',  `exclusivelock` int(11) NOT NULL default '0',  PRIMARY KEY  (`token`),  UNIQUE KEY `token` (`token`),  KEY `path` (`path`),  KEY `path_2` (`path`),  KEY `path_3` (`path`,`token`),  KEY `expires` (`expires`)) TYPE=MyISAM";
	$sqla[8] = "CREATE TABLE `PRFX@@@@@@@webdav_properties` (  `path` varchar(255) NOT NULL default '',  `name` varchar(120) NOT NULL default '',  `ns` varchar(120) NOT NULL default 'DAV:',  `value` text,  PRIMARY KEY  (`path`,`name`,`ns`),  KEY `path` (`path`)) TYPE=MyISAM";
	$sqla[9] = "INSERT INTO `PRFX@@@@@@@languages` ( `id` , `LANGID` , `TEXTID` , `TEXT` ) VALUES ('', 'ENGLISH', 'stillchecked1', 'This file is still locked for editing by')";
	$sqla[10] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'DateFormat', 'dd-mm-yyyy', NOW( ) , 'Enter \'mm-dd-yyyy\' here to display dates in US format, anything else to display dates in international format (which is dd-mm-yyyy).')";
	$sqla[11] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'HideCustomerTab', 'no', NOW( ) , 'Set this value to \'Yes\' if you want the customer tab only to be visible to administrators')";

	Upgrade($db_ver_from, $db_ver_to, $sqla);

}

function A22TO23() {
		$db_ver_from = "2.2.0";		
		$db_ver_to = "2.3.0";

		$sqla[0] = "INSERT INTO PRFX@@@@@@@settings ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'ShowPDWASLink', 'No', NOW( ) , 'Yes to show the PDWAS link in the file list. PDWAS is a CRM-CTT addon which enables you to edit flies and directly save them to CRM-CTT without having to upload the file manually.')";
           
		$sqla[1] = "ALTER TABLE PRFX@@@@@@@journal ADD `type` varchar(20) NOT NULL default 'entity'";
				   
		$sqla[2] = "ALTER TABLE PRFX@@@@@@@journal CHANGE `message` `message` LONGTEXT NOT NULL";
						   
		$sqla[3] = "ALTER TABLE PRFX@@@@@@@customer ADD `customer_owner` INT(11) NOT NULL;";
				   
		$sqla[4] = "ALTER TABLE PRFX@@@@@@@customer ADD `email_owner_upon_adds` enum('no','yes') NOT NULL DEFAULT 'no'";
				   
		$sqla[5] = "INSERT INTO PRFX@@@@@@@settings ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'BODY_ENTITY_CUSTOMER_ADD', 'You are registerd to customer @CUSTOMER@. Entity @ENTITYID@ was just coupled to that customer, so you might have to do something.', NOW( ) , 'The body of the e-mail which is send to the customer_owner when an entity (new or existing) is coupled to that customer, and the email_customer_upon_action checkbox in the customer properties is checked.')";

		Upgrade($db_ver_from, $db_ver_to, $sqla);
}

function A21TO22() {
		
		$db_ver_from = "2.1.0";		
		$db_ver_to = "2.2.0";

		$sqla[0] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'BODY_TEMPLATE_CUSTOMER', '<FONT face=\"MS Shell Dlg\"><P><FONT size=2>&gt;&gt; This mail is regarding entity @ENTITYID@ , \"@CATEGORY@\" in CRM @TITLE@ at @WEBHOST@<BR>-----------------------<BR>Send from CRM-CTT<BR></FONT><A href=\"http://www.crm-ctt.com/\"><FONT size=2>http://www.crm-ctt.com</FONT></A><BR></P></FONT>', NOW( ) , 'The template wich is used when sending an e-mail to a customer (editable by user before sending)');";

		Upgrade($db_ver_from, $db_ver_to, $sqla);

}

function A2TO21() {
		$db_ver_from = "2.0.0";		
		$db_ver_to = "2.1.0";
		
		// Version-specific queries into array [sqla]

		$sqla[0] = "ALTER TABLE `PRFX@@@@@@@binfiles` ADD INDEX ( `koppelid` )";
		$sqla[1] = "INSERT INTO PRFX@@@@@@@settings(setting,value,discription) VALUES('MainListColumnsToShow','a:9:{s:2:\"id\";b:1;s:7:\"cb_cust\";b:1;s:8:\"cb_owner\";b:1;s:11:\"cb_assignee\";b:1;s:9:\"cb_status\";b:1;s:11:\"cb_priority\";b:1;s:11:\"cb_category\";b:1;s:10:\"cb_duedate\";b:1;s:12:\"cb_alarmdate\";b:1;}','non-editable by admin')";
		$sqla[2] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'LetUserSelectOwnListLayout', 'Yes', NOW( ) , 'Wether or not to let the user select his/her own list layout')";

		Upgrade($db_ver_from, $db_ver_to, $sqla);
}


function A196TO2() {
		global $host,$user,$pass,$database,$table_prefix;

		$upgradecheck = "SELECT value FROM PRFX@@@@@@@settings WHERE setting='DBVERSION' AND value='2.0.0'";
		$sqla[0] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'DontShowPopupWindow', 'No', NOW() , 'No to show the standard javascript popup window in the entity overview, yes to disable it and make editing the entity the default action when clicking on the row.')";
		$sqla[1] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'ShowFilterInMainList', 'Yes', NOW( ) , 'Wether or not to show the filter pulldowns on top of the main entity list')";
		$sqla[2] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'ShowShortKeyLegend', 'Yes', NOW( ) , 'Wether or not to show the ShortKeys (ALT-1..ALT-9) legend on the tabs')";

		$sqla[3] = "UPDATE PRFX@@@@@@@settings SET value='2.0.0' WHERE setting='DBVERSION'";	

		for ($t=0;$t<64;$t++) {
				
				$a = "Upgrading database mysql://$user[$t]:[password]@$host[$t]/$database[$t]<br>";
				$db = mysql_connect($host[$t], $user[$t], $pass[$t]);
				mysql_select_db($database[$t],$db);

				// Catch half-configured installations:
				if ($table_prefix[$t]=="") $table_prefix[$t] = "CRM";

				$sql = "SELECT value FROM $table_prefix[$t]settings WHERE setting='title'";
				if ($result= mysql_query($sql)) {

					$upgradecheck2 = ereg_replace("PRFX@@@@@@@",$table_prefix[$t],$upgradecheck);
					$result= mcq_upg($upgradecheck2,$db);

					$e= mysql_fetch_array($result);

					if (strlen($e[0])>0) { // not good

							$a .= "<font color='#FF0000'> error. already up-to-date so it seems. skipping this repository!</font><br>$upgradecheck: $e[0]";
							$e[0] = "";
							unset($e);

					} else {

							for ($q=0;$q<sizeof($sqla);$q++) {
								$sqla[$q] = ereg_replace("PRFX@@@@@@@",$table_prefix[$t],$sqla[$q]);
								mcq_upg($sqla[$q],$db);
					}
					$a .= "<font color='#33FF00'>Repository successfully upgraded!</font>";
					}
					
					unset($e);
					$e[0] = "";
					printbox($a);
				} else {
					printbox("Error - data tables not found - config file is inconsistant (mysql://$user[$t]:[password]@$host[$t]/$database[$t]/prfx:$table_prefix[$t])");
				}
			}
			printbox("It is a good idea to delete this script from your installation directory now! (upgrade.php)<br>[ <a href='index.php?8823' class='bigsort'>to login page</a> ]</body></html>"); 

}
function A195TO196() {
		global $host,$user,$pass,$database,$table_prefix;
		$upgradecheck = "SELECT value FROM PRFX@@@@@@@settings WHERE setting='DBVERSION' AND value='1.9.6' OR value='2.0.0'";
		$sqla[0] = "ALTER TABLE `PRFX@@@@@@@customer` ADD `active` ENUM( 'yes', 'no' ) DEFAULT 'yes' NOT NULL";
		$sqla[1] = "UPDATE PRFX@@@@@@@settings SET value='1.9.6' WHERE setting='DBVERSION'";
		$sqla[2] = "INSERT INTO `PRFX@@@@@@@settings` (`settingid`, `setting`, `value`, `datetime`, `discription`) VALUES ('', 'BODY_ENTITY_EDIT', '<BODY><FONT FACE=\"Trebuchet MS\" SIZE=\"2\"><B>One of your entities in repository \"@TITLE@\" was updated.</B><BR><BR>This email is concerning your entity with category \"@CATEGORY@\"<BR>This entity is available in your CRM installation at @WEBHOST@ under EID number @ENTITYID@. <BR><BR>If this email was not intended for you, please contact @ADMINEMAIL@<BR><BR><TABLE><TR><TD>Entity:</TD><TD>@ENTITYID@</TD></TR><TR><TD>Category:</TD><TD>@CATEGORY@</TD></TR><TR><TD>Owner:</TD><TD>@OWNER@</TD></TR><TR><TD>Assignee:</TD><TD>@ASSIGNEE@</TD></TR><TR><TD>Contents:</TD><TD>See attachment</TD></TR><TR><TD>Admin email:</TD><TD>@ADMINEMAIL@</TD></TR><TR><TD>Webhost:</TD><TD>@WEBHOST@</TD></TR><TR><TD>Title:</TD><TD>@TITLE@</TD></TR><TR><TD>Customer:</TD><TD>@CUSTOMER@</TD></TR><TR><TD>Due-date:</TD><TD>@DUEDATE@</TD></TR><TR><TD>Status:</TD><TD>@STATUS@</TD></TR><TR><TD>Priority:</TD><TD>@PRIORITY@</TD></TR></TABLE></FONT></BODY>', NOW(), 'The body of the email which will be sent when an entity is updated. Please read the manual before editing this setting.')";


		for ($t=0;$t<64;$t++) {
				
				$a = "Upgrading database mysql://$user[$t]:[password]@$host[$t]/$database[$t]<br>";
				$db = mysql_connect($host[$t], $user[$t], $pass[$t]);
				mysql_select_db($database[$t],$db);

				// Catch half-configured installations:
				if ($table_prefix[$t]=="") $table_prefix[$t] = "CRM";

				$sql = "SELECT value FROM $table_prefix[$t]settings WHERE setting='title'";
				if ($result= mysql_query($sql)) {
					$upgradecheck2 = ereg_replace("PRFX@@@@@@@",$table_prefix[$t],$upgradecheck);
					$result= mcq_upg($upgradecheck2,$db);
				
					$e= mysql_fetch_array($result);

						if (strlen($e[0])>0) { // not good
								$a .= "<font color='#FF0000'> error. already up-to-date so it seems. skipping this repository!</font><br>$upgradecheck: $table_prefix[$t] :: $e[0]";
								$e[0] = "";
								unset($e[0]);
								unset($e);

						} else {

								for ($q=0;$q<sizeof($sqla);$q++) {
									$sqla[$q] = ereg_replace("PRFX@@@@@@@",$table_prefix[$t],$sqla[$q]);
									mcq_upg($sqla[$q],$db);
								}
					


						$a .= "<font color='#33FF00'>Repository successfully upgraded!</font>";
						}
					$e[0] = "";
					unset($e);
					printbox($a);
				} else {
					printbox("Error - data tables not found - config file is inconsistant (mysql://$user[$t]:[password]@$host[$t]/$database[$t]/prfx:$table_prefix[$t])");
				}
			}
			printbox("It is a good idea to delete this script from your installation directory now! (upgrade.php)<br>[ <a href='index.php?8823' class='bigsort'>to login page</a> ]</body></html>"); 

}
//	remember TABLE PREFIX !
function A194TO195() {
		global $host,$user,$pass,$database,$table_prefix;
		$upgradecheck = "SELECT value FROM PRFX@@@@@@@settings WHERE setting='EnableEntityContentsJournaling'";
		$sqla[0] = "CREATE TABLE `PRFX@@@@@@@ejournal` (`id` int(11) NOT NULL auto_increment,		     `eid` int(11) NOT NULL ,  `category` varchar(150) NOT NULL default '',  `content` longtext NOT NULL,  `status` varchar(50) NOT NULL default 'open',  `priority` varchar(50) NOT NULL default 'low',  `owner` int(11) NOT NULL default '0',  `assignee` int(11) NOT NULL default '0',  `CRMcustomer` int(11) NOT NULL default '0',  `tp` timestamp(14) NOT NULL,  `deleted` enum('n','y') NOT NULL default 'n',  `duedate` varchar(15) NOT NULL default '',  `sqldate` date NOT NULL default '0000-00-00',  `obsolete` enum('y','n') NOT NULL default 'n',  `cdate` date NOT NULL default '0000-00-00',  `waiting` enum('n','y') NOT NULL default 'n',  `readonly` enum('n','y') NOT NULL default 'n',  `closedate` date NOT NULL default '0000-00-00',  `lasteditby` bigint(20) NOT NULL default '0',  `createdby` bigint(20) NOT NULL default '0',   PRIMARY KEY  (`id`),   UNIQUE KEY `id` (`id`),   KEY `id_2` (`id`))";
		$sqla[1] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'EnableEntityContentsJournaling', 'Yes', NOW( ) , 'Set this value to Yes if you want a drop-down box in the main entity window to be able to switch to history states of an entity')";
		$sqla[2] = "UPDATE PRFX@@@@@@@settings SET value='1.9.5' WHERE setting='DBVERSION'";

for ($t=0;$t<64;$t++) {
				
				$a = "Upgrading database mysql://$user[$t]:[password]@$host[$t]/$database[$t]<br>";
				$db = mysql_connect($host[$t], $user[$t], $pass[$t]);
				mysql_select_db($database[$t],$db);

				// Catch half-configured installations:
				if ($table_prefix[$t]=="") $table_prefix[$t] = "CRM";

				$sql = "SELECT value FROM PRFX@@@@@@@settings WHERE setting='title'";
				if ($result= mysql_query($sql)) {
					$upgradecheck = ereg_replace("PRFX@@@@@@@",$table_prefix[$t],$upgradecheck);
					$result= mcq_upg($upgradecheck,$db);
					$e= mysql_fetch_array($result);

					if (strlen($e[0])>0) { // not good

							$a .= "<font color='#FF0000'> error. already up-to-date so it seems. skipping this repository!</font><br>$upgradecheck: $e[0]";
					} else {

							for ($q=0;$q<sizeof($sqla);$q++) {
								$sqla[$q] = ereg_replace("PRFX@@@@@@@",$table_prefix[$t],$sqla[$q]);
								mcq_upg($sqla[$q],$db);
					}
					$a .= "<font color='#33FF00'>Repository successfully upgraded!</font>";
					}
					printbox($a);
				} else {
					printbox("Error - data tables not found - config file is inconsistant (mysql://$user[$t]:[password]@$host[$t]/$database[$t]/prfx:$table_prefix[$t])");
				}
			}
			printbox("It is a good idea to delete this script from your installation directory now! (upgrade.php)<br>[ <a href='index.php?8823' class='bigsort'>to login page</a> ]</body></html>"); 

}

function A193TO194() {
		global $host,$user,$pass,$database,$table_prefix;
		$upgradecheck = "SELECT value FROM PRFX@@@@@@@settings WHERE setting='EnableEntityJournaling'";
		$sqla[0] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'EnableEntityJournaling', 'Yes', NOW( ) , 'Set this value to Yes if you want entity journaling enabled (a link will be added to the main edit entity page)')";
		$sqla[1] = "CREATE TABLE `PRFX@@@@@@@journal` (`id` BIGINT NOT NULL AUTO_INCREMENT ,`eid` BIGINT NOT NULL ,`timestamp` TIMESTAMP NOT NULL ,`user` BIGINT NOT NULL ,`message` VARCHAR( 250 ) NOT NULL ,PRIMARY KEY ( `id` ) ,INDEX ( `eid` , `user` ) ) COMMENT = 'CRM Entity journal'";
		$sqla[2] = "INSERT INTO `PRFX@@@@@@@settings` ( `settingid` , `setting` , `value` , `datetime` , `discription` ) VALUES ('', 'AutoCompleteCustomerNames', 'No', NOW( ) , 'Set this value to Yes if you want a text box wich auto-completes customer names instead of a pull-down menu with all customers.')";

		$sqla[3] = "UPDATE PRFX@@@@@@@settings SET value='1.9.4' WHERE setting='DBVERSION'";

		for ($t=0;$t<64;$t++) {
				
				$a = "Upgrading database mysql://$user[$t]:[password]@$host[$t]/$database[$t]<br>";
				$db = mysql_connect($host[$t], $user[$t], $pass[$t]);
				mysql_select_db($database[$t],$db);

				// Catch half-configured installations:
				if ($table_prefix[$t]=="") $table_prefix[$t] = "CRM";

				$sql = "SELECT value FROM PRFX@@@@@@@settings WHERE setting='title'";
				if ($result= mysql_query($sql)) {
					$upgradecheck = ereg_replace("PRFX@@@@@@@",$table_prefix[$t],$upgradecheck);
					$result= mcq_upg($upgradecheck,$db);
					$e= mysql_fetch_array($result);

					if (strlen($e[0])>0) { // not good

							$a .= "<font color='#FF0000'> error. already up-to-date so it seems. skipping this repository!</font><br>$upgradecheck: $e[0]";
					} else {

							for ($q=0;$q<sizeof($sqla);$q++) {
								$sqla[$q] = ereg_replace("PRFX@@@@@@@",$table_prefix[$t],$sqla[$q]);
								mcq_upg($sqla[$q],$db);
					}
					$sql = "SELECT eid FROM PRFX@@@@@@@entity";
					$result1 = mcq_upg($sql,$db);
					while ($result= mysql_fetch_array($result1)) {
						$sql = "INSERT INTO PRFX@@@@@@@journal(eid,user,message) VALUES ('$result[eid]','[upgrade]','Journaling automatically enabled - upgrade from 1.9.3 to 1.9.4')";
						mcq_upg($sql,$db);
						$jcount++;
					}
					$a .= "<font color='#33FF00'>Repository successfully upgraded!</font><br>$jcount journal entries inserted";
					}
					printbox($a);
				} else {
					printbox("Error - data tables not found - config file is inconsistant (mysql://$user[$t]:[password]@$host[$t]/$database[$t]/prfx:$table_prefix[$t])");
				}
			}
			printbox("It is a good idea to delete this script from your installation directory now! (upgrade.php)<br>[ <a href='index.php?8823' class='bigsort'>to login page</a> ]</body></html>"); 

}

function printerror($a)
{
		if (stristr($a,"reported error")) {
		    	print "\">";
		}
		if (!$a) {
		    $a="You didn't provide all required information. Press 'back' in your browser and try again.";
		}
		?>
		<center><table border='1' width='70%' bgcolor='#F2F2F2' cellspacing='0' cellpadding='4' bordercolor='#CECECE'>
		<tr><td colspan=2><center><img src='error.gif'>&nbsp;&nbsp;<b><? echo $a;?></b>
		</td></tr></table></body></html>
		<?
			exit;
} // end function
function printbox($msg)
{
		global $printbox_size,$legend;
		
		if (!$printbox_size) {
			$printbox_size = "70%";
		}
		
		print "<center><table border='0' width='$printbox_size'><tr><td colspan=2 valign='center'><fieldset>";
		if ($legend) {
			print "<legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$legend</legend>";
		}
		print "<center>" . $msg . "</center></fieldset></td></tr></table><br>";
	
		unset($printbox_size);
		$legend = "";

} // end func


function Upgrade($db_ver_from, $db_ver_to, $sqla) {
		global $legend,$name,$limit_to_db;

		require("config.inc.php");

		// OK, now browse through available repositories		
		$t = 0;
		if (!$GLOBALS['IGNORE_VERSION']) {
			array_push($sqla,"INSERT INTO `PRFX@@@@@@@uselog` ( `id` , `ip` , `url` , `useragent` , `tijd` , `qs` , `user` ) VALUES ('', 'upgrade', 'upgrade', 'upgrade', NOW( ) , 'Upgrade from " . $db_ver_from . " to " . $db_ver_to . "', 'upgrade script')");
			}

		for ($t=0;$t<64;$t++) {

			if ($slave[$t]) {
				array_push($host, $slave[$t]);
				array_push($user, $user[$t]);
				array_push($pass, $pass[$t]);
				array_push($database, $database[$t]);
				array_push($table_prefix, $table_prefix[$t]);
			}

			if (($limit_to_db <> "" && $limit_to_db == $database[$t]) || $limit_to_db=="") {
				
				if ($database[$t]) {

					$a = "Processing mysql://$user[$t]@$host[$t]/$database[$t]/$table_prefix[$t]*<br>";
					if ($GLOBALS['CL']) {
						print "Processing mysql://$user[$t]@$host[$t]/$database[$t]/$table_prefix[$t]*\n";
					}
					if (!$db = mysql_connect($host[$t], $user[$t], $pass[$t])) {
						$a .= "Database connection failed totally.<br>";
						if ($GLOBALS['CL']) {
							print "\tDatabase connection failed totally\n";
						}
						$cancel = true;
					}

					if (!mysql_select_db($database[$t],$db)) {
						$a .= "<font color='#FF0000'>Database could not be selected</font><br>";
						$cancel = true;
						if ($GLOBALS['CL']) {
							print "\tDatabase could not be selected\n";
						}

					}

					// Catch half-configured installations:
					if ($table_prefix[$t]=="") { 
						$prefix = "CRM";
						$a .= "<font color='#FF0000'>Warning - no table prefix configured. Assuming 'CRM'.</font><br>";
						if ($GLOBALS['CL']) {
							print "\tWarning: table prefix is not configured!\n";
						}

					} else {
						$prefix = $table_prefix[$t];
					}

					$sql = "SELECT value FROM $table_prefix[$t]settings WHERE setting='MAINTENANCE_MODE'";
					$result= mcq_upg($sql,$db);
					$dbv = @mysql_fetch_array($result);

					if ($dbv[0] == "No" && !$GLOBALS['IGNORE_VERSION']) {
						$cancel = true;
						$a .= "<font color='#FF0000'>Maintenance mode not enabled.</font><br>";
						if ($GLOBALS['CL']) {
							print "\tMaintenance mode not enabled.\n";
						}
					}
				
					// Check if the title can be found
					$sql = "SELECT value FROM $table_prefix[$t]settings WHERE setting='title'";
					if (!$cancel) {
						if ($result= mysql_query($sql)) { // OK, it can be found
							$sql = "SELECT value FROM $table_prefix[$t]settings WHERE setting='DBVERSION'";
							$result= mcq_upg($sql,$db);
							$dbv = mysql_fetch_array($result);
							if ($dbv[0]<>$db_ver_from && !$GLOBALS['IGNORE_VERSION']) {
								$cancel = true;
								$a .= "<font color='#FF0000'>Wrong database version. Expected $db_ver_from but got $dbv[0].</font><br>";
								if ($GLOBALS['CL']) {
									print "\tWrong database version: looking for $db_ver_from but got $dbv[0]... skipping!\n";
								}

							} else {


								// ACTUALLY UPGRADE THE REPOSITORY HERE 
								if ($GLOBALS['CL']) {
									print "\015\tRunning SQL queries: ";
								}
								$top = sizeof($sqla);
								unset($top2);
								for ($q=0;$q<sizeof($sqla);$q++) {
									$sql_to_query = ereg_replace("PRFX@@@@@@@",$prefix,$sqla[$q]);
									//print "Running SQL: " . $sql_to_query . "\n";
									mcq_upg($sql_to_query,$db);			
									$top2++;
									if ($GLOBALS['CL']) {
										print "\015\tRunning SQL queries: " . $top2 . "/" . $top;
									}

									
								}
								if ($GLOBALS['CL']) {
									print "\n";
								}

								if ($db_ver_from == "3.4.2" && $db_ver_to == "3.4.3") {
									// Create master template, update entities
									if ($GLOBALS['CL']) {
										//print "\nConverting all entities to HTML forms....";
									}
									mcq_upg("INSERT INTO " . $prefix . "binfiles(koppelid,filename,username,file_subject,filetype) VALUES('','Default form','Hidde Fennema','Default form','TEMPLATE_HTML_FORM')", $db);
									$t = $GLOBALS['Last_Insert'];
									if ($GLOBALS['CL']) {
										//print "\nCreated file $t ...\n";
									}


									mcq_upg("INSERT INTO " . $prefix . "blobs(fileid,content) VALUES('" . $t . "','<br /><fieldset><legend>&nbsp;&nbsp;<font size=\"+1\">Entity @EID@ #LOCKICON# &nbsp;</font></legend><table width=\"90%\">    <tbody>        <tr>            <td>            <table>                <tbody>                    <tr>                        <td valign=\"top\"><fieldset><legend>&nbsp;&nbsp;Customer&nbsp;</legend>#CUSTOMER#</fieldset></td>                        <td valign=\"top\"><fieldset><legend>&nbsp;&nbsp;Priority&nbsp;</legend>#PRIORITY#</fieldset></td>                        <td valign=\"top\"><fieldset><legend>&nbsp;&nbsp;Status&nbsp;</legend>#STATUS#</fieldset></td>                        <td valign=\"top\" nowrap=\"nowrap\"><fieldset><legend>&nbsp;&nbsp;Owner&nbsp;</legend>#OWNER#</fieldset></td>                        <td valign=\"top\"><fieldset><legend>&nbsp;&nbsp;Assignee&nbsp;</legend>#ASSIGNEE#</fieldset></td>                        <td nowrap=\"nowrap\">#PDFICON# #WEBDAVICON# #ACTICON# #REPORTICON# #PRINTICON# #INVOICEICON# #JOURNALICON# #EMAILPOPLINK#</td>                    </tr>                </tbody>            </table>            </td>        </tr>    </tbody></table><table>    <tbody>        <tr>            <td><fieldset><legend>&nbsp;&nbsp;Category&nbsp;</legend>#CATEGORY# &nbsp;</fieldset> </td>            <td><fieldset><legend>&nbsp;&nbsp;Due date&nbsp;</legend>#DUEDATE# #DUETIME#&nbsp; </fieldset> </td>        </tr>    </tbody></table><p><br />#CONTENTS#<br />#COMMENTBOX#</p><p>#ALL_EXTRA_FIELDS#</p><table>    <tbody>        <tr>            <td><br /><fieldset>            <table>                <tbody>                    <tr><td>#DELETEBOX# Deleted entity&nbsp;&nbsp;&nbsp;&nbsp;</td><tr>                        <td>#READONLYBOX# Read-only to other users&nbsp;&nbsp;&nbsp;&nbsp;</td>                    </tr>                    <tr>                        <td>#PRIVATEBOX# Private</td>                    </tr>                </tbody>            </table>            </fieldset></td>        </tr>    </tbody></table><table>    <tbody>        <tr>            <td colspan=\"6\"><fieldset>            <p><legend>&nbsp;Attach file&nbsp;</legend>#FILEBOX#<br />#FILELIST#</p>            </fieldset></td>        </tr>    </tbody></table><table width=\"90%\">    <tbody>        <tr>            <td>#EMAILDROPDOWN# #SAVEBUTTON#</td>        </tr>    </tbody></table></fieldset>')", $db);


									mcq_upg("UPDATE " . $prefix . "entity SET formid=" . $t . " WHERE formid=0", $db);

									$res = mcq_upg("SELECT id,ADDFORMS FROM " . $prefix . "loginusers", $db);
									while ($row = mysql_fetch_array($res)) {
										$u = unserialize($row['ADDFORMS']);
										for ($y=0;$y<sizeof($u);$y++) {
											if ($u[$y] == "default") {
												$u[$y] = $t;
											}
										}
										mcq_upg("UPDATE " . $prefix . "loginusers SET ADDFORMS='" . serialize($u) . "' WHERE id=" . $row['id'], $db);
									}
									$res = mcq_upg("SELECT id,ADDFORMS FROM " . $prefix . "userprofiles", $db);
									while ($row = mysql_fetch_array($res)) {
										$u = unserialize($row['ADDFORMS']);
										for ($y=0;$y<sizeof($u);$y++) {
											if ($u[$y] == "default") {
												$u[$y] = $t;
											}
										}
										mcq_upg("UPDATE " . $prefix . "userprofiles SET ADDFORMS='" . serialize($u) . "' WHERE id=" . $row['id'], $db);
									}


								}


								if ($db_ver_from == "3.4.0" && $db_ver_to == "3.4.1") {
									// COPY FILES HERE
									if ($GLOBALS['CL']) {
										print "\tStart copying files to bin large objects table (this may take a while)\n";
									}	
									$sql = "SELECT fileid FROM " . $prefix . "binfiles";
									$res = mcq_upg($sql,$db);
										while ($row = mysql_fetch_array($res)) {
										$sql2 = "SELECT content FROM " . $prefix . "binfiles WHERE fileid=" . $row['fileid'];
										$res2 = mcq_upg($sql2,$db);	
										$row2 = mysql_fetch_array($res2);
										if ($GLOBALS['CL']) {
											print "\015\tDuplicate file ... " . $row['fileid'] . "                                ";
										}
										mcq_upg("INSERT INTO " . $prefix . "blobs(fileid, content) VALUES('" . $row['fileid'] . "', '" . addslashes($row2['content']) . "')", $db);
										$fcpd++;									
										unset($row2);
										//print "\015\tDelete orignal file ... " . $row['fileid'] . "                             ";
										mcq_upg("UPDATE " . $prefix . "binfiles SET content='' WHERE fileid=" . $row['fileid'], $db);

									}
									if ($GLOBALS['CL']) {
										print "\n\tAlter old table, drop now empty content... \n";
									}
									mcq_upg("ALTER TABLE " . $prefix . "binfiles DROP content", $db);
									if ($GLOBALS['CL']) {
										print "\n\tOptimize table to free space... \n";
									}
									mcq_upg("OPTIMIZE TABLE " . $prefix . "binfiles", $db);
									$text = "(" . $fcpd . " files copied)";
								}
								

								if (!$GLOBALS['IGNORE_VERSION']) {
									$sql = "UPDATE " .  $prefix . "settings SET value='" . $db_ver_to . "' WHERE setting='DBVERSION'";	
									mcq_upg($sql,$db);
								}
								

								$a .= "Repository successfully upgraded $text";
								if ($GLOBALS['CL']) {
									print "\tRepository successfully upgraded. $text\n";
								}

							}

						} else {
							$a .= "<font color='#FF0000'>Tables not found.</font><br>";
							if ($GLOBALS['CL']) {
								print "\tTables not found.\n";
							}
		
							$cancel = true;
						}
					}
					
					if ($cancel) {
						$legend = "<img src='error.gif'>";
						$a .= "Repository not touched.<br>";
						if ($GLOBALS['CL']) {
							print "\tRepository not touched.\n";
						}

					} else {
						$legend = "<font color='#33FF00'>OK&nbsp;</font>";

					}
					

					unset($cancel);
					if ($GLOBALS['CL']) {
						print "Done.\n";
					} else {
						printbox($a);
		
					}
				}
		} else { // end of if limit 
			
		}
	}		
	if ($GLOBALS['CL']) {
		print "All done.\n";
	} else {
		$legend = "CRM&nbsp;";
		printbox("<img src='arrow.gif'>&nbsp;<a href='index.php?logout=1' class='bigsort'>to login page</a>&nbsp;<img src='arrow.gif'>&nbsp;<a href='upgrade.php?11=11' class='bigsort'>to main upgrade page</a>");
	}

}
function Upgrade_Specialfor300($db_ver_from, $db_ver_to, $sqla) {
		global $host,$user,$pass,$database,$table_prefix,$legend,$name,$CL;

		// OK, now browse through available repositories		
		$t = 0;

		array_push($sqla,"INSERT INTO `PRFX@@@@@@@uselog` ( `id` , `ip` , `url` , `useragent` , `tijd` , `qs` , `user` ) VALUES ('', 'upgrade', 'upgrade', 'upgrade', NOW( ) , 'Upgrade from " . $db_ver_from . " to " . $db_ver_to . "', 'upgrade script')");

		for ($t=0;$t<64;$t++) {

				$a = "Processing mysql://$user[$t]@$host[$t]/$database[$t]/$table_prefix[$t]*<br>";
				if ($CL) {
					$a = eregi_replace("<br>","\n",$a);
					$a = ereg_replace("<([^>]+)>", "", (eregi_replace("<br>","\015\012",$a)));
					print $a . "\n";
					$a = "";
				}
				if (!$db = @mysql_connect($host[$t], $user[$t], $pass[$t])) {
					$a .= "<marquee><blink>Database connection failed totally</blink></marquee><br>";
					$cancel = true;
				}

				if (!@mysql_select_db($database[$t],$db)) {
					$a .= "<font color='#FF0000'>Database could not be selected</font><br>";
					$cancel = true;
				}

				// Catch half-configured installations:
				if ($table_prefix[$t]=="") { 
					$prefix = "CRM";
					$a .= "<font color='#FF0000'>Warning - no table prefix configured. Assuming 'CRM'.</font><br>";
				} else {
					$prefix = $table_prefix[$t];
				}

				if ($CL) {
					$a = eregi_replace("<br>","\n",$a);
					$a = ereg_replace("<([^>]+)>", "", (eregi_replace("<br>","\015\012",$a)));
					print $a . "\n";
					$a = "";
				}
				// Check if the title can be found
				$sql = "SELECT value FROM $table_prefix[$t]settings WHERE setting='title'";
				if (!$cancel) {
					if ($result= mysql_query($sql)) { // OK, it can be found
						$sql = "SELECT value FROM $table_prefix[$t]settings WHERE setting='DBVERSION'";
						$result= mcq_upg($sql,$db);
						$dbv = mysql_fetch_array($result);
						if ($dbv[0]<>$db_ver_from) {
							$cancel = true;
							$a .= "<font color='#FF0000'>Wrong database version. Expected $db_ver_from but got $dbv[0].</font><br>";
						} else {
								
							$totsize = sizeof($sqla);

							// ACTUALLY UPGRADE THE REPOSITORY HERE 
							for ($q=0;$q<sizeof($sqla);$q++) {
								$sql_to_query = ereg_replace("PRFX@@@@@@@",$prefix,$sqla[$q]);
								mcq_upg($sql_to_query,$db);	
								
								if ($CL) {
									print "\015 Query: " . $q . "/" . $totsize;								
								}
							}
							$GLOBALS['TBL_SPREFIX'] = $prefix;
							UpdateExtraFields();
							
							$sql = "UPDATE " .  $prefix . "settings SET value='" . $db_ver_to . "' WHERE setting='DBVERSION'";	
							mcq_upg($sql,$db);

							$a .= "Repository successfully upgraded";
						}

					} else {
						$a .= "<font color='#FF0000'>Tables not found.</font><br>";
						$cancel = true;
					}
				}
				
				if ($cancel) {
					$legend = "<img src='error.gif'>";
					$a .= "Repository not touched.<br>";
				} else {
					$legend = "<font color='#33FF00'>OK&nbsp;</font>";

				}
				

				unset($cancel);
				if ($CL<>true) {
					printbox($a);
				} else {
					$a = eregi_replace("<br>","\n",$a);
					$a = ereg_replace("<([^>]+)>", "", (eregi_replace("<br>","\015\012",$a)));
					print $a . "\n";
					$a = "";
				}

		}		
		
	$legend = "CRM&nbsp;";
	if (!$CL) {
		printbox("<img src='arrow.gif'>&nbsp;<a href='index.php?logout=1' class='bigsort'>to login page</a>&nbsp;<img src='arrow.gif'>&nbsp;<a href='upgrade.php?11=11' class='bigsort'>to main upgrade page</a>");
	}

}
function UpdateExtraFields() {
	global $a;
	$sql = "SELECT value FROM $GLOBALS[TBL_SPREFIX]settings WHERE setting='EXTRAFIELDLOCATION'";
	$result = mcq_upg($sql,$db);
	$row = mysql_fetch_array($result);
	
	$old_names = array();
	$new_names = array();
	$old_ids   = array();
	$old_cids  = array();
	$new_ids   = array();
	$new_cids  = array();

	$location = $row['value'];

	if ($location=="A") {
		$location="Middle box, left";
	} else {
		$location="Under main text box, left";
	}

	$sql = "SELECT value FROM $GLOBALS[TBL_SPREFIX]settings WHERE setting='Extra fields list'";
	$result = mcq_upg($sql,$db);
	$row = mysql_fetch_array($result);
	if ($row['value']<>"") {
		$eflist = explode(",",$row['value']);

		//print_r($eflist);

		$queries = array();

		foreach ($eflist AS $field) {
				$num++;
				$orig = $field;
				array_push($old_names,$field);
				array_push($old_ids, "@EF" . $num . "@");
				
				if (stristr($field,"HIDE_")) {
					$hidden = "y";
					$field = ereg_replace("HIDE_","",$field);
				} else {
					$hidden = "n";
				}


				if (stristr($field,"DD_VAT_")) {

					$field = ereg_replace("DD_VAT_","",$field);

					$tmp = explode("|",$field);
					$fieldname = $tmp[0];
					$tmp[0] = "";

					$options_arr = array();
					foreach ($tmp AS $option) {
						if (trim($option)<>"") {
							array_push($options_arr,$option);
						}
					}
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('entity','" . $hidden . "','VAT drop-down','" . $fieldname . "','" . serialize($options_arr) . "','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";

				} elseif (stristr($field,"DD_")) {
					$field = ereg_replace("DD_","",$field);

					$tmp = explode("|",$field);
					$fieldname = $tmp[0];
					$tmp[0] = "";

					$options_arr = array();
					foreach ($tmp AS $option) {
						if (trim($option)<>"") {
							array_push($options_arr,$option);
						}
					}

					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('entity','" . $hidden . "','drop-down','" . $fieldname . "','" . serialize($options_arr) . "','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";

				} elseif (stristr($field,"TB_")) {
					$field = ereg_replace("TB_","",$field);
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('entity','" . $hidden . "','text area','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";

				} elseif (stristr($field,"EML_")) {
					$field = ereg_replace("EML_","",$field);
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('entity','" . $hidden . "','mail','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";

				} elseif (stristr($field,"IHC_")) {
					$field = ereg_replace("IHC_","",$field);
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('entity','" . $hidden . "','invoice cost','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";

				} elseif (stristr($field,"IHS_")) {
					$field = ereg_replace("IHS_","",$field);				
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('entity','" . $hidden . "','invoice qty','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";

				} elseif (stristr($field,"LINK_")) {
					$field = ereg_replace("LINK_","",$field);				
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('entity','" . $hidden . "','hyperlink','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";
				} elseif (stristr($field,"DATE_")) {
					$field = ereg_replace("DATE_","",$field);
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('entity','" . $hidden . "','date','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";
				} elseif (stristr($field,"COMMENT_")) {
					$field = ereg_replace("COMMENT_","",$field);
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('entity','" . $hidden . "','comment','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";
				} else {
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('entity','" . $hidden . "','textbox','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";
				}

				// Now update the trigger table

					
				$orig2 = "@EF_" . $orig . "@";
				$sql = "UPDATE $GLOBALS[TBL_SPREFIX]triggers SET onchange='EFID" . $i . "' WHERE onchange='" . base64_encode($orig2) . "'";
				//print $sql . "\n";
				mcq_upg($sql,$db);

		array_push($new_names, "@EFID" . $i . "@");
		array_push($new_cids, "@EFID" . $i . "@");
		}
	}
	$sql = "DELETE FROM $GLOBALS[TBL_SPREFIX]settings WHERE setting='Extra fields list'";
	mcq_upg($sql,$db);

	$num = 0;
	
	$sql = "SELECT value FROM $GLOBALS[TBL_SPREFIX]settings WHERE setting='Extra customer fields list'";
	$result = mcq_upg($sql,$db);
	$row = mysql_fetch_array($result);
	if ($row['value']<>"") {
		$eflist = explode(",",$row['value']);

		//print_r($eflist);
		

	
		$queries = array();

		foreach ($eflist AS $field) {

				$num++;
				array_push($old_cids, "@ECF" . $num . "@");
				array_push($old_names,$field);
				$orig = $field;
				
				if (stristr($field,"HIDE_")) {
					$hidden = "y";
					$field = ereg_replace("HIDE_","",$field);
				} else {
					$hidden = "n";
				}

//				array_push($old_cids, "ECF" . $num);

				

				if (stristr($field,"DD_VAT_")) {

					$field = ereg_replace("DD_VAT_","",$field);

					$tmp = explode("|",$field);
					$fieldname = $tmp[0];
					$tmp[0] = "";

					$options_arr = array();
					foreach ($tmp AS $option) {
						if (trim($option)<>"") {
							array_push($options_arr,$option);
						}
					}
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('customer','" . $hidden . "','VAT drop-down','" . $fieldname . "','" . serialize($options_arr) . "','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";

				} elseif (stristr($field,"DD_")) {
					$field = ereg_replace("DD_","",$field);

					$tmp = explode("|",$field);
					$fieldname = $tmp[0];
					$tmp[0] = "";

					$options_arr = array();
					foreach ($tmp AS $option) {
						if (trim($option)<>"") {
							array_push($options_arr,$option);
						}
					}

					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('customer','" . $hidden . "','drop-down','" . $fieldname . "','" . serialize($options_arr) . "','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";

				} elseif (stristr($field,"TB_")) {
					$field = ereg_replace("TB_","",$field);
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('customer','" . $hidden . "','text area','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";

				} elseif (stristr($field,"EML_")) {
					$field = ereg_replace("EML_","",$field);
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('customer','" . $hidden . "','mail','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";

				} elseif (stristr($field,"IHC_")) {
					$field = ereg_replace("IHC_","",$field);
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('customer','" . $hidden . "','invoice cost','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";

				} elseif (stristr($field,"IHS_")) {
					$field = ereg_replace("IHS_","",$field);				
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('customer','" . $hidden . "','invoice qty','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";

				} elseif (stristr($field,"LINK_")) {
					$field = ereg_replace("LINK_","",$field);				
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('customer','" . $hidden . "','hyperlink','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";
				} elseif (stristr($field,"DATE_")) {
					$field = ereg_replace("DATE_","",$field);
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('customer','" . $hidden . "','date','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";
				} elseif (stristr($field,"COMMENT_")) {
					$field = ereg_replace("COMMENT_","",$field);
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('customer','" . $hidden . "','comment','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";
				} else {
					$sql = "INSERT INTO $GLOBALS[TBL_SPREFIX]extrafields (tabletype,hidden,fieldtype,name,options,location,ordering) VALUES('customer','" . $hidden . "','textbox','" . $field . "','','" . $location . "','" . ($i*10*1.234) . "')";
					//print $sql . "\n";
					mcq_upg($sql,$db);
					$i = mysql_insert_id();
					$sql = "UPDATE $GLOBALS[TBL_SPREFIX]customaddons SET name='" . $i . "' WHERE name='" . $orig . "'";
					mcq_upg($sql,$db);
					//print $sql . "\n";
				}



				array_push($new_names, "@EFID" . $i . "@");
				array_push($new_cids, "@EFID" . $i . "@");
		}
	}
	$sql = "DELETE FROM $GLOBALS[TBL_SPREFIX]settings WHERE setting='Extra customer fields list'";
	mcq_upg($sql,$db);

	$sql = "DELETE FROM $GLOBALS[TBL_SPREFIX]settings WHERE setting='EXTRAFIELDLOCATION'";
	mcq_upg($sql,$db);

	$sql = "DELETE FROM $GLOBALS[TBL_SPREFIX]extrafields WHERE name=''";
	mcq_upg($sql,$db);

	$a .= "<BR>" . $num . " Extra fields converted to 3.0.0 format";

	//ConvertTemplatesToNewFormat($old_names,$new_names);
}

function ConvertTemplatesToNewFormat($old_names, $new_names) {		

	
	$sql = "SELECT * FROM $GLOBALS[TBL_SPREFIX]binfiles WHERE koppelid=0 AND filesize<1000000";
		$result= @mcq_upg($sql,$db);
		while ($row = @mysql_fetch_array($result)) {
					
					for ($x=0;$x<sizeof($old_names);$x++) {
						$row['content'] = str_replace("@ECF_" . $old_names[$x] . "@",$new_names[$x],$row['content']);
						$row['content'] = str_replace("@EF_" . $old_names[$x] . "@",$new_names[$x],$row['content']);

						$row['file_subject'] = str_replace("@ECF_" . $old_names[$x] . "@",$new_names[$x],$row['file_subject']);
						$row['file_subject'] = str_replace("@EF_" . $old_names[$x] . "@",$new_names[$x],$row['file_subject']);
					}

					for ($x=0;$x<sizeof($old_ids);$x++) {
						$row['content'] = str_replace($old_ids[$x],$new_ids[$x],$row['content']);
						$row['file_subject'] = str_replace($old_ids[$x],$new_ids[$x],$row['file_subject']);
					}
					for ($x=0;$x<sizeof($old_cids);$x++) {
						$row['content'] = str_replace($old_cids[$x],$new_cids[$x],$row['content']);
						$row['file_subject'] = str_replace($old_cids[$x],$new_cids[$x],$row['file_subject']);
					}



				$ins =  "INSERT INTO $GLOBALS[TBL_SPREFIX]binfiles(koppelid,content,filename,filesize,filetype,username,file_subject) ";
				$ins .= "VALUES('" . $row['koppelid'] . "','" . addslashes($row['content']) . "','" . $row['filename'] . "-converted-by-CRM-CTT" . "','" . $row['filesize'] . "','" . $row['filetype'] . "','CRM-CTT 2.6.2 to 3.0.0 upgrade procedure','" . $row['file_subject'] . "')";

				mcq_upg($ins,$db);

		}
}
function mcq_upg($sql,$db) {
	global $mysql_query_counter, $logqueries, $name, $debug, $title, $host, $pass, $user, $slave;
	if ($GLOBALS['logqueries']) {
		$mysql_query_counter++;
		@$fp = fopen("qlist.txt","a");
		@fputs($fp,"SQL: ($name) ($mysql_query_counterc) (" . date('U') . "): " . $GLOBALS['CURFUNC'] . " " . $sql ."\n");
		@fclose($fp);
		if ($GLOBALS['qlog_onscreen']) {
			$GLOBALS['pagelog'] .=  "SQL: ($name) : " . $sql . "\n";
		}
	} 
	if ($GLOBALS['query_timer']) {
		$tr = microtime_float();
	}
	$a = mysql_query($sql);

	$GLOBALS['Last_Insert'] = mysql_insert_id();
	if ($GLOBALS['query_timer']) {	
		$tm = microtime_float() - $tr;
		if ($tm > $GLOBALS['max_time_query']) {
			$GLOBALS['max_time_query'] = $tm;
			$GLOBALS['max_time_query_sql'] = $sql;
		}
	}
	return($a);
}

function ReturnReposList() {
//	global $user, $pass, $host, $database, $table_prefix;
	require("config.inc.php");
	$a = "";
	if (sizeof($pass)>0) {
						for ($r=0;$r<64;$r++) {
							if ($user[$r]) {
								if ($db = @mysql_connect($host[$r], $user[$r], $pass[$r])) {
									if (@mysql_select_db($database[$r],$db)) {
										$sql = "SELECT value FROM $table_prefix[$r]settings WHERE setting='title'";
										if ($result= mysql_query($sql)) {

											$database[$r] = trim($database[$r]);
											
											$sql = "SELECT value FROM $table_prefix[$r]settings WHERE setting='title'";
											$result= @mcq_upg($sql,$db);
											$maxU1 = @mysql_fetch_array($result);
											$title = $maxU1[0];

											$sql = "SELECT value FROM $table_prefix[$r]settings WHERE setting='DBVERSION'";
											$result= @mcq_upg($sql,$db);
											$maxU1 = @mysql_fetch_array($result);
											$version = $maxU1[0];
											
											$sql = "SELECT value FROM $table_prefix[$r]settings WHERE setting='MAINTENANCE_MODE'";
											$result= @mcq_upg($sql,$db);
											$maxU1 = @mysql_fetch_array($result);
											$mm = $maxU1[0];
			
											if ($version=="") { $version="Prior to 1.9.0."; }

											$sql = "SELECT COUNT(*) FROM $table_prefix[$r]entity";
											$result= @mcq_upg($sql,$db);
											$res = @mysql_fetch_array($result);
											$enum = $res[0];

											$sql = "SHOW TABLE STATUS";
											$result= @mcq_upg($sql,$db);
											while ($stat = @mysql_fetch_array($result))
											{
												$size += $stat["Data_length"];
												$size += $stat["Index_lenght"];
											}							
				
											$size = round(($size/1024)) . "K";
											
											$a .= "Version    : " . fillout($version,10) . "\tName: " . fillout($database[$r],20) . "\t#Ent: " . fillout($enum,6) . "\tMaintenance mode: $mm\tTitle: $title\n";
										} // end if 1st query was ok
									} else {
										$a .= "Repository : " . fillout($r,10) . "\tName: " . fillout($database[$r],20) . "\tCouldn't select database\n";
									}

								} else {
									$a .= "Repository : " . fillout($r,10) . "\tName: " . fillout($database[$r],20) . "\tTitle: $title\tDatabase host unreachable\n";
								}
							}
						}
					}
	return($a);
}
function CLMenu() {
		global $user, $pass, $host, $database, $table_prefix, $limit_to_db;
		$GLOBALS['CL'] = "Yes";
		print "\n";
		print "1		- Upgrade all 3.4.2 databases to version 3.4.3\n\n";
		print "2		- Upgrade all 3.4.1 databases to version 3.4.2\n";
		print "3		- Upgrade all 3.4.0 databases to version 3.4.1\n";
		print "4		- Upgrade all 3.3.2 databases to version 3.4.0\n";
		print "5		- Upgrade all 3.3.1 databases to version 3.3.2\n";
		print "6		- Upgrade all 3.3.0 databases to version 3.3.1\n";
		print "7		- Upgrade all 3.2.0 databases to version 3.3.0\n";
		print "8		- Upgrade all 3.1.0 databases to version 3.2.0\n";
		print "9		- Upgrade all 3.0.0 databases to version 3.1.0\n";
		print "\n";
		print "limit		- Limit upgrade to a single database\n";
		print "test		- Test upgrade procudere (harmless, but maintenance must be enabled)\n";
		print "mm on		- Enable maintenance mode on all repositories\n";
		print "mm off		- Disable maintenance mode on all repositories\n";
		print "\nexit		- Exit program\n";

		print "\n Enter option below.\n";
		print " \nCRM-CTT > ";

		$a = readln();

		if ($a == "exit") {
			$GLOBALS['end'] = true;
			return;
		}

		if ($a == "limit") {
			print "Limit to which database? (full name, case sensitive)\n";
			print "Limit   > ";
			$limit_to_db = readln();
			print "Which upgrade option? (see above, either 'test' or number)\n";
			print "Option  > ";
			$a = readln();
		}

		if ($a == "mm on") {
			$sqla = array();
			array_push($sqla, "UPDATE PRFX@@@@@@@settings SET value='Yes' WHERE setting='MAINTENANCE_MODE'");
			$GLOBALS['IGNORE_VERSION'] = true;
			Upgrade("%", "%", $sqla);
			$GLOBALS['IGNORE_VERSION'] = false;

		} elseif ($a == "mm off") {
			$sqla = array();
			array_push($sqla, "UPDATE PRFX@@@@@@@settings SET value='No' WHERE setting='MAINTENANCE_MODE'");
			$GLOBALS['IGNORE_VERSION'] = true;
			Upgrade("%", "%", $sqla);
			$GLOBALS['IGNORE_VERSION'] = false;
		} elseif ($a == "SQL") {
			print "Which query to run? Use PRFX@@@@@@@ as prefix replacement if required.\n";
			print "SQL > ";
			$sql = readln();
			unset($sqla);
			$sqla = array();
			array_push($sqla, $sql);
			$GLOBALS['IGNORE_VERSION'] = true;
			Upgrade("%", "%", $sqla);
			$GLOBALS['IGNORE_VERSION'] = false;
			
		}

	

		switch ($a) {
			case "test": 
				print "Do NOT cancel this test!\n";
				$sqla = array();
				array_push($sqla, "SELECT * FROM PRFX@@@@@@@settings");
				//array_push($sqla, "SELECT * FROM PRFX@@@@@@@uselog");
				$GLOBALS['IGNORE_VERSION'] = true;
				Upgrade("3.4.2", "hidde", $sqla);
				print "========= And now reverse this test .... =========== do NOT interrupt! =========================\n";
				Upgrade("hidde", "3.4.3", $sqla);
				$GLOBALS['IGNORE_VERSION'] = false;
			break;
			case 1:
				A342TO343();
			break;
			case 2:
				A341TO342();
			break;
			case 3:
				A340TO341();
			break;
			case 4:
				A332TO340();
			break;
			case 5:
				A331TO332();
			break;
			case 6:
				A330TO331();
			break;
			case 7:
				A320TO330();
			break;
			case 8:
				A310TO320();
			break;
			case 9:
				A300TO310();
			break;

			default:
				//print "bye-bye\n";
			break;

		}
	}
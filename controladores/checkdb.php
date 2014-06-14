<?
/* ********************************************************************
 * $GLOBALS[TBL_PREFIX] 
 * Copyright (c) 2001-2003 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This script checks the repository currently logged onto for errors and
 * inconsistencies, and optimizes all tables.
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

extract($_REQUEST);

// Set error reporting level
error_reporting(E_ERROR);

if ($_SERVER['HTTP_HOST']) {
	$web = 1;
//	print "WEB IS AAN";
//	print_r($_SERVER);
//	exit;
}

if ($web) {
	$EnableRepositorySwitcherOverrule="n";
	include("header.inc.php");
	print "</td></tr></table>";
	AdminTabs();
	MainAdminTabs("datman");
} else {
	require("config.inc.php");
	require("functions.php");

	$GLOBALS['logqueries']    = false;   // logs all queries (10% slower)
	$GLOBALS['logtext']       = false;   // logs all comments - alternative: user-num. Logs only that user. (25% slower)
	$GLOBALS['query_timer']   = false;   // logs slowest SQL query
	$GLOBALS['qlog_onscreen'] = false;   // displays pop-up containing log
	$GLOBALS['ShowTraceLink'] = false;   // displays qlog trace link at end of page (same 25% slower as 'logtext')


	print "Database integrity check\n\n";
	if ($argv[1]=="-help" || $argv[1]=="--help" || $argv[1]=="help" || $argv[1]=="-h") {
		print "\nUsage:\n";
		print "\t[no arguments]\t:Interactive\n";
		print "or:\n";
		print "\t[reposnr] [user] [pass] [y|n] - (y = auto repair, n = prompt)\n";
		print "\nExample: php -q checkdb.php 0 admin admin_pwd y\n\n";
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
	if ($argv[4] == "y") {
		$auto = $argv[4];
		$auto=1;
		print "! Auto-fix is enabled.\n";
	} 
	if (!CommandlineLogin($username,$password,$repository)) {
		print "Exiting...";
		exit;
	}

	$include="1";
	include("sumpdf.php");
	include("language.php");
	
}

$tables = array("$GLOBALS[TBL_PREFIX]accesscache", "$GLOBALS[TBL_PREFIX]binfiles", "$GLOBALS[TBL_PREFIX]blobs", "$GLOBALS[TBL_PREFIX]cache", "$GLOBALS[TBL_PREFIX]calendar", "$GLOBALS[TBL_PREFIX]contactmoments", "$GLOBALS[TBL_PREFIX]customaddons", "$GLOBALS[TBL_PREFIX]customer", "$GLOBALS[TBL_PREFIX]ejournal", "$GLOBALS[TBL_PREFIX]entity", "$GLOBALS[TBL_PREFIX]entitylocks", "$GLOBALS[TBL_PREFIX]extrafields", "$GLOBALS[TBL_PREFIX]help", "$GLOBALS[TBL_PREFIX]internalmessages", "$GLOBALS[TBL_PREFIX]journal", "$GLOBALS[TBL_PREFIX]languages", "$GLOBALS[TBL_PREFIX]loginusers", "$GLOBALS[TBL_PREFIX]phonebook", "$GLOBALS[TBL_PREFIX]priorityvars", "$GLOBALS[TBL_PREFIX]sessions", "$GLOBALS[TBL_PREFIX]settings", "$GLOBALS[TBL_PREFIX]statusvars", "$GLOBALS[TBL_PREFIX]triggers", "$GLOBALS[TBL_PREFIX]uselog", "$GLOBALS[TBL_PREFIX]userprofiles", "$GLOBALS[TBL_PREFIX]webdav_locks", "$GLOBALS[TBL_PREFIX]webdav_properties", "$GLOBALS[TBL_PREFIX]entityformcache");

if ($web) {
	print "</table><table border=0 width='65%'><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size=+1>$lang[adm]</font>&nbsp;</legend>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=+2>" . $title . "</font><table border=0 width='100%'>";
}

MustBeAdmin();


// Some language table management: (completely safe, so don't bother the user)

$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]languages WHERE TEXTID='' AND TEXT=''";
mcq($sql,$db);


if (!$go && $web) {
	$legend = "Check database&nbsp;";
	printbox("This script checks your current repository ($title) for errors, and it will optimize all its tables. On large repositories, it can take quite some time. This script can also be run from the command line.<BR><BR>Do you want to continue?<BR><BR><img src='arrow.gif'>&nbsp;<a href='checkdb.php?go=1&web=1' class='bigsort'>Yes</a><BR><BR><img src='arrow.gif'>&nbsp;<a href='javascript:history.back(-1);' class='bigsort'>No, take me back</a>");
} elseif ($input) {
	// OK two arrays of to-delete data were submitted.
	// Namely: 
	// file_td	:	files to delete
	// cf_td 	:	custom fields to delete

	$file_td =		unserialize(base64_decode($file_td));
	$cf_td =		unserialize(base64_decode($cf_td));
	$cf_td_cust =	unserialize(base64_decode($cf_td_cust));
	$journal_td =	unserialize(base64_decode($journal_td));
	$ejournal_td =	unserialize(base64_decode($ejournal_td));
	$calendar_td =	unserialize(base64_decode($calendar_td));
	$deldoubles =	unserialize(base64_decode($deldoubles));

	$queries = array();

	foreach ($file_td as $file) {
		array_push($queries,"DELETE FROM $GLOBALS[TBL_PREFIX]binfiles WHERE fileid='" . $file . "'");
		array_push($queries,"DELETE FROM $GLOBALS[TBL_PREFIX]blobs WHERE fileid='" . $file . "'");
	}
	// Custom field table can get very large - consolidate the query

	$base_q = "DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE (";
	foreach ($cf_td as $cf) {
		$base_q .= " eid='" . $cf . "' OR";
	}
	$base_q .= " eid = '122219873875983659824645whatever') AND (type='entity' OR type='')";
	array_push($queries,$base_q);

	$base_q2 = "DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE (";
	foreach ($cf_td_cust as $cf) {
		$base_q2 .= " eid='" . $cf . "' OR";
	}
	$base_q2 .= " eid = '122219873875983659824645whatever') AND type='cust'";
	array_push($queries,$base_q2);

	foreach ($journal_td as $jtd) {
		array_push($queries,"DELETE FROM $GLOBALS[TBL_PREFIX]journal WHERE eid='" . $jtd . "' AND type='entity'");
	}
	foreach ($ejournal_td as $ejtd) {
		array_push($queries,"DELETE FROM $GLOBALS[TBL_PREFIX]ejournal WHERE eid='" . $ejtd . "'");
	}
	foreach ($calendar_td as $ctd) {
		array_push($queries,"DELETE FROM $GLOBALS[TBL_PREFIX]calendar WHERE eid='" . $ctd . "'");
	}
	if (!$given_query) { $given_query = array(); }

	if (sizeof($given_query>0)) {
		foreach ($given_query as $row) {
			array_push($queries,$row);
		}
	}
	//print_r($queries);

	$queries = array_merge($queries, $deldoubles);

	for ($x=0;$x<sizeof($tables);$x++) {
		array_push($queries, "OPTIMIZE TABLE " . $tables[$x]);
	}		

	foreach($queries as $sql) {
		mcq($sql,$db);
	}

	

	print "<pre>" . sizeof($queries) . " database queries executed.\n</pre>";
	print "<img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password' style='cursor:pointer'>Back to main administration page</a>";

} else {
	
	
	if ($web) {
		print "<pre>";
	}

	/*
	print "\nRepair table...\n";


	for ($x=0;$x<sizeof($tables);$x++) {
		$sql = "REPAIR TABLE " . $tables[$x];
		mcq($sql,$db);
		//print "Pro-active fixing ... " . $tables[$x] . "\n";

		
		ob_

	}

	print "\nAll tables optimized. Starting extended reference check.\n\n";

	*/
	$sql = "SELECT COUNT(eid) FROM $GLOBALS[TBL_PREFIX]entity";
	$result = mcq($sql,$db);
	$result = mysql_fetch_array($result);
	$maxid = $result[0];
	$op=1;
	print "\nStarting recursive reference check.\n\n";

	print "Checking user references...\n";

	$eids = array();

	$sql = "SELECT eid,CRMcustomer,owner,assignee FROM $GLOBALS[TBL_PREFIX]entity";
	$ret = mcq($sql,$db);
	while ($row = mysql_fetch_array($ret)) {
		if (GetUserName($row['owner']) == "n/a" && $row['owner']<>"2147483647") {
			//print "\nUser     reference error (non-existing user): " . $row['owner'] . "\n";

			$err = 1;
		}
		if (GetUserName($row['assignee']) == "n/a" && $row['assignee']<>"2147483647") {
			//print "\nUser     reference error (non-existing user): " . $row['assignee'] . "\n";

			$err = 1;
		}
		array_push($eids,$row['eid']);
		if (!$web) {
			print "\015" . $op . "/" . $maxid;
			$op++;
		}
		
	}
	if (!$err) {
		print "\nUser references OK\n\n";

	}
	unset($err);
	print "Checking journal...\n";

	
	$sql = "SELECT count(distinct eid) FROM $GLOBALS[TBL_PREFIX]journal WHERE type='entity'";
	$result = mcq($sql,$db);
	$result = mysql_fetch_array($result);
	$top = $result[0];
	//print "\n";

	$op=1;
	$journaltd = array();
	$sql = "SELECT DISTINCT(eid) FROM $GLOBALS[TBL_PREFIX]journal where type='entity'";
	//$sql = "SELECT $GLOBALS[TBL_PREFIX]journal.eid FROM $GLOBALS[TBL_PREFIX]journal,$GLOBALS[TBL_PREFIX]entity WHERE $GLOBALS[TBL_PREFIX]entity.eid <> $GLOBALS[TBL_PREFIX]journal.eid";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if (!$web) {
			print "\015" . $op . "/" . $top;
			$op++;
		}
		if (!in_array($row['eid'],$eids)) {
			$hops++;
			$err=1;
			$serr=1;
			array_push($journaltd,$row['eid']);
		}
	}
	print "\n";

	if (!$err) {
		print "Journal references OK\n\n";

		print "Checking entity journals...\n";

	} else {
		print "Found " . $hops . " reference errors in journal\n\n";

		print "Checking entity journals...\n";

	}
	$sql = "SELECT COUNT(DISTINCT eid) FROM $GLOBALS[TBL_PREFIX]ejournal";
	$result = mcq($sql,$db);
	$result = mysql_fetch_array($result);
	$top = $result[0];

	unset($err);
	$hops=0;
	$op=1;
	if (!$web) {
		print "\015" . $op . "/" . $top;
	}
	$ejournaltd = array();
	$sql = "SELECT DISTINCT(eid) FROM $GLOBALS[TBL_PREFIX]ejournal";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {

		if (!$web) {
			print "\015" . $op . "/" . $top;
			$op++;
		}

		if (!in_array($row['eid'],$eids)) {
			$hops++;
			$err=1;
			$serr=1;
			array_push($ejournaltd,$row['eid']);
		}
	}
	print "\n";


	if (!$err) {
		print "EJournal references OK\n";

	} else {
		print "Found " . $hops . " reference errors in entity journal\n";

	}
	unset($err);
	
	print "\n";

	$ftd = array();
	
	$op=1;
	$oldpc=0;

	$sql = "SELECT count(*) FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid<>'0' AND type='entity'";
	$result = mcq($sql,$db);
	$row = mysql_fetch_array($result);
	$top = $row[0];
	print "Checking file references (entity)...\n";

	
	
	
	$sql = "SELECT koppelid,fileid FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid<>'0' AND type='entity'";
	$result = mcq($sql,$db);
	$pc1 = $top/100; // 1% van totaal

	while ($row = mysql_fetch_array($result)) {
		if (!$web) {
			print "\015" . $op . "/" . $top;
			$op++;
		}

		$sql = "SELECT eid from $GLOBALS[TBL_PREFIX]entity WHERE eid='" . $row['koppelid'] . "'";
		$result1 = mcq($sql,$db);
		$result1 = mysql_fetch_array($result1);
		if (!$result1[0]) {
			//print "File  reference error for fileid " . fillout($row['fileid'],6) . "! Entity " . fillout($row['koppelid'],6) . " doesn't exist..\n";

			$err=1;
			$serr = 1;
			array_push($ftd,$row['fileid']);
		}
	}

	print "\n";
	$sql = "SELECT count(*) FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid<>'0' AND type='cust'";
	$result = mcq($sql,$db);
	$row = mysql_fetch_array($result);
	$top = $row[0];
	print "Checking file references (customer)...\n";
	$sql = "SELECT koppelid,fileid FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid<>'0' AND type='cust'";
	$result = mcq($sql,$db);
	$pc1 = $top/100; // 1% van totaal
	$op = 0;

	while ($row = mysql_fetch_array($result)) {
		if (!$web) {
			print "\015" . $op . "/" . $top;
			$op++;
		}

		$sql = "SELECT id from $GLOBALS[TBL_PREFIX]customer WHERE id='" . $row['koppelid'] . "'";
		$result1 = mcq($sql,$db);
		$result1 = mysql_fetch_array($result1);
		if (!$result1[0]) {
			//print "File  reference error for customer fileid " . fillout($row['fileid'],6) . "! Entity " . fillout($row['koppelid'],6) . " doesn't exist..\n";

			$err=1;
			$serr = 1;
			array_push($ftd,$row['fileid']);
		}
	}
		print "\n";

	if (!$err) {
		print "File references OK\n\n";

		print "Checking calendar references...\n";

	} else {
		print "Found errors in file references\n\n";

		print "Checking calendar references...\n";

	}
	unset($err);
	$op=1;
	$sql = "SELECT count(distinct eID) FROM $GLOBALS[TBL_PREFIX]calendar";
	$result = mcq($sql,$db);
	$row = mysql_fetch_array($result);
	$top = $row[0];

	$calendartd = array();
	$sql = "SELECT DISTINCT(eID) FROM $GLOBALS[TBL_PREFIX]calendar";
	$result = mcq($sql,$db);
	if (!$web) {
		print "\015" . "0" . "/" . $top;
	}
	while ($row = mysql_fetch_array($result)) {
		if (!$web) {
			print "\015" . $op . "/" . $top;
			$op++;
		}
		$sql = "SELECT * from $GLOBALS[TBL_PREFIX]entity WHERE eid='" . $row['eID'] . "'";
		$result1 = mcq($sql,$db);
		$result1 = mysql_fetch_array($result1);
		if (!$result1[0]) {
			//print "File  reference error for fileid " . fillout($row['fileid'],6) . "! Entity " . fillout($row['koppelid'],6) . " doesn't exist..\n";

			$err=1;
			$serr = 1;
			array_push($calendartd,$row['eID']);
		}
	}
	print "\n";

	if (!$err) {
		print "Calendar references OK\n\n";

		print "Checking field references...\n";


	} else {
		print "Found errors in calendar references\n\n";

		print "Checking field references...\n";

	}
	unset($err);
	$op=1;
	$cftd = array();
	$sql = "SELECT count(id) FROM $GLOBALS[TBL_PREFIX]customaddons WHERE type='' OR type='entity'";
	$result = mcq($sql,$db);
	$row = mysql_fetch_array($result);
	$top = $row[0];
	if (!$web) {
		print "\015" . "0" . "/" . $top;
	}

	$sql = "SELECT DISTINCT(eid) FROM $GLOBALS[TBL_PREFIX]customaddons WHERE type='' OR type='entity'";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if (!$web) {
			print "\015" . "$op/" . $top;
			$op++;
		}
		$sql = "SELECT eid from $GLOBALS[TBL_PREFIX]entity WHERE eid='" . $row['eid'] . "'";
		$result1 = mcq($sql,$db);
		$result1 = mysql_fetch_array($result1);
		if (!$result1[0]) {
			$err=1;
			$serr = 1;
			array_push($cftd,$row['eid']);
		}
	}
	print "\n";

	if (!$err) {
		print "Field references OK (" . $lang['entity'] . ")\n\n";

		print "Checking field references...\n";

	} else {
		print "Found errors in field references (" . $lang['entity'] . ")\n\n";

		print "Checking field references...\n";

	}
	unset($err);
	$op=1;
	$sql = "SELECT count(id) FROM $GLOBALS[TBL_PREFIX]customaddons WHERE type='cust'";
	$result = mcq($sql,$db);
	$row = mysql_fetch_array($result);
	$top = $row[0];
	if (!$web) {
		print "\015" . "0" . "/" . $top;
	}
	$cftdcust = array();
	$sql = "SELECT id,eid,name FROM $GLOBALS[TBL_PREFIX]customaddons WHERE type='cust'";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if (!$web) {
			print "\015" . "$op/" . $top;
			$op++;
		}
		$sql = "SELECT id from $GLOBALS[TBL_PREFIX]customer WHERE id='" . $row['eid'] . "'";
		$result1 = mcq($sql,$db);
		$result1 = mysql_fetch_array($result1);
		if (!$result1[0]) {
			//print "Field reference error for customer entity field! " . $lang['customer'] . " " . fillout($row['eid'],6) . " doesn't exist.. (" . $row['name'] . "(" . $row['id'] . "))\n";

			$err=1;
			$serr=1;
			array_push($cftdcust,$row['eid']);
		}
	}
	print "\n";

	if (!$err) {
		print "Field references OK (" . $lang['customer'] . ")\n";

	} else {
		print "Found errors in field references (" . $lang['customer'] . ")\n";

	}
	unset($err);

	$blobids = array();
	$binids = array();

	print "\nChecking binary large object references (blobs table)...\n";
	$res = mcq("SELECT fileid FROM $GLOBALS[TBL_PREFIX]binfiles",$db);
	while ($row = mysql_fetch_array($res)) {
		array_push($binids,$row['fileid']);
	}
	$res = mcq("SELECT fileid FROM $GLOBALS[TBL_PREFIX]blobs",$db);
	while ($row = mysql_fetch_array($res)) {
		array_push($blobids,$row['fileid']);
	}

	foreach ($blobids AS $id) {
		$xc++;
		if (!$web) {
			print "\015" . $xc . "/" . sizeof($blobids);
		}
		if (!in_array($id,$binids)) {
			print "\nFile ID $id exists in BLOB table, but it doesn't exist in BIN table (binairy content will be deleted)\n";
			array_push($ftd,$id);
			$err=1;
			$serr=1;
		}
	}
	print "\nChecking binary large object references (binfiles table)...\n";
	foreach ($binids AS $id) {
		$xxc++;
		if (!$web) {
			print "\015" . $xxc . "/" . sizeof($blobids);
		}
		if (!in_array($id,$blobids)) {
			print "\nFile ID $id exists in BIN table, but it doesn't exist in BLOB table (file will be empty)\n";
		}
	}

	/*print "Checking parent/childs references...\n";

	$res = mcq("SELECT eid, parent FROM $GLOBALS[TBL_PREFIX]entity", $db);
	while ($row=mysql_fetch_array($res)) {
		$a = true;
		if ($row['parent'] <> 0) {
			$a = ValidateParentalRights($row['parent'],$row['eid']);

			if ($a == false) {
				//print "WRONG REF: " . $row['eid'] . " to daddy " . $row['parent'] . "\n";
			} else {
				//print "GOOD REF: " . $row['eid'] . " to daddy " . $row['parent'] . "\n";
			}
		} else {
//			print "SKIP REF: " . $row['eid'] . "\n";
		}

	}
	
*/
	print "\n";

	print "Checking for double extra field values....";
	$res1 = mcq("SELECT DISTINCT(CONCAT(eid,';',name)) AS ErrorneousValues FROM $GLOBALS[TBL_PREFIX]customaddons GROUP BY ErrorneousValues HAVING COUNT(CONCAT(eid,'-',name))>1",$db);
	
	$deldoubles = array();

	while ($res2 = mysql_fetch_array($res1)) {
		$item = explode(";", $res2[0]);
		//print "Eid " . $item[0] . " Field " . $item[1] . " - double entry. Oldest must be deleted.\n";
		    if ($item[1]) {
    			$sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid=" . $item[0] . " AND name=" . $item[1];
	    		$all = mcq($sql, $db);
			$delnums = array();
			while ($row = mysql_fetch_array($all)) {
			    array_push($delnums, $row[0]);						
			}
		    }
		sort($delnums);
		//print_r($delnums);
		//print "\n Should be del : DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid=" . $item[0] . " AND name='" . $item[1] . "' AND id<" . $delnums[sizeof($delnums)-1] . " AND id <> " . $delnums[sizeof($delnums)-1];
		
		array_push($deldoubles,"DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid=" . $item[0] . " AND name='" . $item[1] . "' AND id<" . $delnums[sizeof($delnums)-1] . " AND id <> " . $delnums[sizeof($delnums)-1]);
		//print "DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid=" . $item[0] . " AND name='" . $item[1] . "' AND id<" . $delnums[sizeof($delnums)-1] . " AND id <> " . $delnums[sizeof($delnums)-1];
		$doubles++;
		$serr = 1;
	}

	print "\n" . sizeof($deldoubles) . " double entries found. Oldest values will be deleted.";

	qlog("Checking database");


		print "\n\nFiles which can be deleted: " . sizeof($ftd) . "\n";
		print "Custom enitity field sets which can be deleted: " . sizeof($cftd) . "\n";
		print "Custom customer fields sets which can be deleted: " . sizeof($cftdcust) . "\n";
		print "Journal sets which can be deleted: " . sizeof($journaltd) . "\n";
		print "EJournal sets which can be deleted: " . sizeof($ejournaltd) . "\n";
		print "Calendar entries which can be deleted: " . sizeof($calendartd) . "\n";
		print "Extra field value records which need to be deleted: " . sizeof($deldoubles) . "\n";

	if (!$web && !$serr) {
		print "\nNothing to do. Bye!\n\n";

		exit;
	}
	
	if ($web) {
		print "</pre>";
		
		if ($serr) {
			print "<form name='cont' method='post'>";
			print "<input name='file_td' type='hidden' value='" . base64_encode(serialize($ftd)) . "'>"; 
			print "<input name='cf_td' type='hidden' value='" . base64_encode(serialize($cftd)) . "'>"; 
			print "<input name='cf_td_cust' type='hidden' value='" . base64_encode(serialize($cftdcust)) . "'>"; 
			print "<input name='journal_td' type='hidden' value='" . base64_encode(serialize($journaltd)) . "'>"; 
			print "<input name='ejournal_td' type='hidden' value='" . base64_encode(serialize($ejournaltd)) . "'>"; 
			print "<input name='calendar_td' type='hidden' value='" . base64_encode(serialize($calendartd)) . "'>"; 
			print "<input name='deldoubles' type='hidden' value='" . base64_encode(serialize($deldoubles)) . "'>"; 
			
			print "<input name='input' type='hidden' value='1'>";
			print "<input type='submit' value='Delete excess data (back-up first)'></form>";
		} else {
			print "No errors found which can be fixed automatically.<br><br>";
			print "<img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password' style='cursor:pointer'>Back to main administration page</a>";
		}
		print "</td></tr></table>";
		EndHTML();
	} else {
		if (!$auto) {
			print "\nDo you want to delete these (obviously unreachable) records? [y|n]\n CRM> ";
			$a = readln();
			if (strtoupper($a)<>'Y') {
				print "\nOK, bye!\n\n";

				exit;
			}
		}
		

		$queries = array();

	
		foreach ($ftd as $file) {
			array_push($queries,"DELETE FROM $GLOBALS[TBL_PREFIX]binfiles WHERE fileid='" . $file . "'");
			array_push($queries,"DELETE FROM $GLOBALS[TBL_PREFIX]blobs WHERE fileid='" . $file . "'");
		}
		// Custom field table can get very large - consolidate the query
		$base_q = "DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE (";
		foreach ($cftd as $cf) {
			$base_q .= " eid='" . $cf . "' OR";
		}
		$base_q .= " eid = '122219873875983659824645whatever') AND (type='' OR type='entity')";
		array_push($queries,$base_q);
		
		$base_q = "DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE ";
		foreach ($cftdcust as $cf) {
			$base_q .= " eid='" . $cf . "' OR";
		}
		$base_q .= " eid = '122219873875983659824645whatever' AND type='cust'";
		
		array_push($queries,$base_q);
		foreach ($journaltd as $jtd) {
			array_push($queries,"DELETE FROM $GLOBALS[TBL_PREFIX]journal WHERE eid='" . $jtd . "'");
		}
		foreach ($ejournaltd as $ejtd) {
			array_push($queries,"DELETE FROM $GLOBALS[TBL_PREFIX]ejournal WHERE eid='" . $ejtd . "'");
		}
		foreach ($calendartd as $ctd) {
			array_push($queries,"DELETE FROM $GLOBALS[TBL_PREFIX]calendar WHERE eID='" . $ctd . "'");
		}

		$queries = array_merge($queries, $deldoubles);

		//foreach ($given_query as $row) {
		//	array_push($queries,$row);
		//}
		//print_r($queries);

		foreach($queries as $sql) {
			mcq($sql,$db);
			//print $sql . "\n\n";
			$sqlc++;
			if (!$web) print "Executing queries, please be patient. (" . $sqlc . "/" . sizeof($queries) . ")\015";
		}

		print "\n" . sizeof($queries) . " database queries executed.\n";
	
		
	
		for ($x=0;$x<sizeof($tables);$x++) {
			if (!$web) print "\015Optimizing tables..(" . $x . "/" . sizeof($tables) . ")";
			$sql = "OPTIMIZE TABLE " . $tables[$x];
			mcq($sql,$db);
			//print "Optimizing table  ... " . $tables[$x] . "\n";
		}		
		print " ... done\n\n";

		uselogger_local("Database checked. " . sizeof($queries) . " delete statements executed","");
		qlog("Database checked. " . sizeof($queries) . " delete statements executed");
		
	}

} // end if !$go


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


function uselogger_local($comment,$dummy_extra_not_used){
	global $REMOTE_ADDR, $HTTP_SERVER_VARS, $actuser, $username, $user, $HTTP_USER_AGENT,$name,$logqueries;
		
		// here comes the mail trigger
	


	 if (getenv(HTTP_X_FORWARDED_FOR)){ 
	   $ip=getenv(HTTP_X_FORWARDED_FOR); 
	 } 
	 else { 
	   $ip=getenv(REMOTE_ADDR); 
	 } 
	
	
	if (!$comment) {
		$qs  = getenv("QUERY_STRING");
		$qs .= getenv("HTTP_POST_VARS");
		$qs .= $comment;
	} else {
		$qs = addslashes($comment);
	}

	$url = $HTTP_SERVER_VARS["PHP_SELF"];
	
	$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('$ip', '$url', '$HTTP_USER_AGENT' , '$qs','$name')";
	mcq($query,$db);

	if ($logqueries) {
		qlog("'$ip', '$url', '$HTTP_USER_AGENT' , '$qs','$name'");
	}
}
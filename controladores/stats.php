<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This file does several things :)
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

include("header.inc.php");
print "</table><table width='80%' border=0><tr><td>&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size=+1>$lang[maninfo]&nbsp;</font>&nbsp;</legend>&nbsp;</legend><table width='80%'><tr><td colspan=4></td></tr>";

if($_REQUEST['stashid']) {

				?>
				<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
				<!--
					statusWin = window.open('summary.php?wait=1', 'statusWin' ,'width=400,height=135,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
					statusWin.close();
				//-->
				</SCRIPT>
				<?
}

if ($mipassword=="*NONE*") {
		$password=$mipassword;
} 

if ($password<>$mipassword) {
				print "<tr><td><form name='pwd' method='post'>";
				if ($password) {
				    print "<b>$lang[wrongpwd].</b><br>";
				} else {
				    print "<b>$lang[plzenterpwd]:</b><br>"; 
				}
				   print "<br><input type='password' name='password'><br><br><input type='submit' name='knop' value='Log in'>";
				   print "<input type='hidden' name='summary' value='on'>";
				   print "<input type='hidden' name='entities_owned_by_users' value='on'>";
				   print "<input type='hidden' name='entities_assigned_to_users' value='on'>";
				   print "<input type='hidden' name='self_assigned' value='on'>";
				   print "<input type='hidden' name='didntbelonghere' value='on'>";
				   print "<input type='hidden' name='elseaction' value='on'>";
				   print "<input type='hidden' name='entities_per_customer' value='on'>";
   				   print "<input type='hidden' name='overdue_per_assignee' value='on'>";
   				   print "<input type='hidden' name='firstlogin' value='on'>";
				   print "</form></td><tr></table></body></html>";
				   exit;
}

$cdate = date('Y-m-d');
$time_start = microtime();
 
if ($_REQUEST['stashid']) {
	
	$eids = array();

	$sql = PopStashValue($_REQUEST['stashid']);
	$res = mcq($sql, $db);
	while ($row = mysql_fetch_array($res)) {
		array_push($eids, $row['eid']);
	}
	$num = PushStashValue($eids);

	$maxo = sizeof($eids);
	$maxU1 = sizeof($eids);
	$maxEo = sizeof($eids);
	$maxE = sizeof($eids);

	$andstring = "AND (" . $GLOBALS['TBL_PREFIX'] . "entity.eid=";
	foreach ($eids AS $eid) {
			$andstring .= $eid ." OR " . $GLOBALS['TBL_PREFIX'] . "entity.eid=";
	}
	$andstring .= $eids[0] . ")";
	$wherestring = "WHERE 1=1 " . $andstring;
}


$sqlcounter++;
$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]loginusers";
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$maxU = $maxU1[0];

$sqlcounter++;
$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity " . $wherestring;
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$maxEo = $maxU1[0];
$maxE = $maxU1[0];
$maxo = $maxU1[0];

if ($maxEo==0) {
	print "<html><body><table><tr><td><img src='error.gif'>&nbsp;<b><font face='MS Shell dlg'>You cannot create statistics on an empty database!</font></b><br>&nbsp;&nbsp;&nbsp;<font face='MS Shell dlg'>This error is fatal.</font><br><br>";
	print "<font face='MS Shell dlg'>Click <a class='bigsort' href='javascript:history.back(-1);'>here</a> to go back....</font></td></tr></table></body></html>";
	exit;
}

$sqlcounter++;
$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted<>'y' " . $andstring;
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$maxEoNc = $maxU1[0];
$maxEc = $maxU1[0];
$maxEa = $maxU1[0];
$delE = $maxU1[0];

$sqlcounter++;
$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]customer";
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$maxC = $maxU1[0];

$sqlcounter++;
$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE sqldate<'$cdate' AND deleted<>'y' " . $andstring;
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$expE = $maxU1[0];
print "<fieldset>";




DisplayMangementInformationForm($num);

print "</fieldset>";
print "<br>";

if ($firstlogin) {
    exit;
}

print "<table width='80%'><tr><td>&nbsp;&nbsp;</td><td>";
if ($summary) {
	ManagementInformationSummary($_REQUEST['qid']);   
	print "<br>";
}
if ($entities_owned_by_users) {
	entities_owned_by_users($_REQUEST['qid']);    
	print "<br>";
}
if ($entities_assigned_to_users) {
	entities_assigned_to_users($_REQUEST['qid']);    
	print "<br>";
}
if ($self_assigned) {
	self_assigned($_REQUEST['qid']);    
	print "<br>";
}
if ($overdue_per_assignee) {
	overdue_per_assignee($_REQUEST['qid']);    
	print "<br>";
}
if ($didntbelonghere) {
    didntbelonghere($_REQUEST['qid']);
	print "<br>";
}
if ($elseaction) {
	elseaction($_REQUEST['qid']);    
	print "<br>";
}
if ($entities_per_customer) {
	entities_per_customer($_REQUEST['qid']);    
	print "<br>";
}
if ($Top10_Most_active) {
	Top10_Most_active($_REQUEST['qid']);    
	print "<br>";
}
if ($Top10_Most_active_users) {
	Top10_Most_active_users();    
	print "<br>";
}
if ($Top10_Most_active_users_uselog) {
	Top10_Most_active_users_uselog();    
	print "<br>";
}
if ($Top20_Most_Slow) {
	Top20_Most_Slow($_REQUEST['qid']);    
	print "<br>";
}
if ($Top20_Most_Slow_Deleted) {
	Top20_Most_Slow_Deleted($_REQUEST['qid']);    
	print "<br>";
}
if ($summonth) {
	summonth($_REQUEST['qid']);    
	print "<br>";
}
print "</td></tr></table>";

$time_end = microtime();
$seconds = $time_end - $time_start;
$seconds = number_format($seconds,3);

EndHTML();
exit;

?>
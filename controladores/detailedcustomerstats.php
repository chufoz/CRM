<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This is the detailed customer statistics plugin for CRM-CTT
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
extract($_REQUEST);
require("header.inc.php");

// Get rid of the crappy header heritage
print "</td></tr></table>\n<!-- Detailed customer statistics -->";

// This file should generate a detailed breakdown of customer data, including extra fields.

if ($_REQUEST['qid']) {
	$q = PopStashValue($_REQUEST['qid']);
	$andstring = " AND (" . $GLOBALS['TBL_PREFIX'] . "entity.eid=";
	foreach ($q AS $eid) {
			$andstring .= $eid ." OR " . $GLOBALS['TBL_PREFIX'] . "entity.eid=";
	}
	$andstring .= $q[0] . ")";
	$wherestring = "WHERE 1=1 " . $andstring;
	}

// Customer should be given (cid=[customer_id])

if ($_REQUEST['cid']) {
	
	$cid = $_REQUEST['cid'];
	
	$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE CRMcustomer=" . $cid . " " . $andstring;
	$result= mcq($sql,$db);
	$row= mysql_fetch_array($result);
	$tot_ent = $row[0];
	
	$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted<>'y' AND CRMcustomer=" . $cid . " " . $andstring;
	$result= mcq($sql,$db);
	$row= mysql_fetch_array($result);
	$tot_open = $row[0];

	$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted='y' AND CRMcustomer=" . $cid . " " . $andstring;
	$result= mcq($sql,$db);
	$row= mysql_fetch_array($result);
	$tot_del = $row[0];
	


	print "<table width='70%'><tr><td>&nbsp;&nbsp;</td><td>";
	print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Statistic breakdown of customer: " . GetCustomerName($cid) . "&nbsp;";
	if ($_REQUEST['qid']) {
		print "<font color='#FF0000'>(subset)</font>";
	}
	print "</legend>";
	print "<table cellspacing='0' cellpadding='4' width=100% border=1 bordercolor='#F0F0F0'>";
	print "<tr><td>Total entities</td><td colspan=3>" . $tot_ent . "</td></tr>";

	print "<tr><td>Open vs. deleted</td><td>Open: " . $tot_open . "</td><td>Deleted: " . $tot_del . "</td></tr>";
	print "<tr><td>Avg. age (non-deleted entities)</td><td colspan=3>" . GetAvgEntityAge($cid, "not_del") . "</td></tr>";
	if ($tot_del > 0) {
		print "<tr><td>Avg. duration (deleted entities)</td><td colspan=3>" . GetAvgEntityAge($cid, "del") . "</td></tr>";
		print "<tr><td>Avg. age/duration (all entities)</td><td colspan=3>" . GetAvgEntityAge($cid) . "</td></tr>";
	}
	
	print "<tr><td colspan=4><br><b>" . $lang['status'] . " breakdown</b></td>";

	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]statusvars ORDER BY varname";
	$result= mcq($sql,$db);
    while ($e= mysql_fetch_array($result)) {
		
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE status='$e[varname]' AND CRMcustomer=" . $cid . " " . $andstring;
			$result1= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($result1);

			$bla = $maxU1[0];

			$pc1 = ($tot_ent/100); // dit is 1 procent
		
			$pc2 = ($bla/100); // dit is 1 procent van not [deleted]
			
			$apc = @round($bla/$pc1); // dit is het percentage
			
			print "<tr><td bgcolor='" . $e['color'] . "'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$e[varname]</td><td width=\"20%\">$bla</td><td width=\"20%\">$apc%</td></tr>";	
			$totaal=$totaal+$bla;
	}
	print "<tr><td colspan=4><br><b>" . $lang['priority'] . " breakdown</b></td>";
	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]priorityvars ORDER BY varname";
	$result= mcq($sql,$db);
    while ($e= mysql_fetch_array($result)) {
		
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE priority='$e[varname]' AND CRMcustomer=" . $cid . " " . $andstring;
			$result1= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($result1);

			$bla = $maxU1[0];

			$pc1 = ($tot_ent/100); // dit is 1 procent
		
			$pc2 = ($bla/100); // dit is 1 procent van not [deleted]
			
			$apc = @round($bla/$pc1); // dit is het percentage
			
			print "<tr><td bgcolor='" . $e['color'] . "'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$e[varname]</td><td width=\"20%\">$bla</td><td width=\"20%\">$apc%</td></tr>";	
			$totaal=$totaal+$bla;
	}
	
	print "<tr><td colspan=4><br><b>Extra field breakdown (drop-down fields only)</b></td>";

	$f_ar = GetExtraFields();

	foreach ($f_ar AS $field) {
		if ($field['fieldtype'] == "drop-down") {
			print "<tr><td colspan=4>" . $field['name'] . "</td></tr>";
			$tmp = unserialize($field['options']);
			foreach($tmp AS $option) {
				print "<td></td><td width=\"20%\">" . $option . "&nbsp;</td><td width=\"20%\">";
				
				$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity,$GLOBALS[TBL_PREFIX]customaddons WHERE $GLOBALS[TBL_PREFIX]entity.CRMcustomer=" . $cid . " AND $GLOBALS[TBL_PREFIX]entity.eid = $GLOBALS[TBL_PREFIX]customaddons.eid AND name='" . $field['id'] . "' AND value='" . $option . "'" . $andstring;
				$result1= mcq($sql,$db);
				$tmp2 = mysql_fetch_array($result1);
				print $tmp2[0];
				print "&nbsp;</td></tr>";	
			}
			//<td width=\"20%\">" . $yt . "&nbsp;</td><td width=\"20%\">" . $yt . "&nbsp;</td></tr>";	
		}
	}



	if ($_REQUEST['inc_detail']) {
			print "<tr><td colspan=2><b>Category breakdown</b></td></tr>";
			$sql = "SELECT category, status, priority FROM $GLOBALS[TBL_PREFIX]entity WHERE CRMcustomer='" . $_REQUEST['cid'] . "'" . $andstring;

			$result= mcq($sql,$db);
			while ($row = mysql_fetch_array($result)) {
				print "<tr><td>" . $row['category'] . "</td><td>" . $row['status'] . "</td><td>" . $row['priority'] . "</td></tr>";
			}
			//print "</table>";
	
	}

	if ($GLOBALS['FormFinity'] == "Yes") {
		print "<tr><td><b>Form breakdown</b></td></tr>";

			print "<tr><td></td><td width=\"20%\">Default&nbsp;</td><td width=\"20%\">";
			$num = db_GetRow("SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE formid=0 AND CRMcustomer=" . $cid . " " . $andstring);
			print $num[0];
			$num[0] = 0;
			"</td></tr>";

		
		$res = mcq("SELECT fileid, file_subject FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid=0 AND filetype='TEMPLATE_HTML_FORM'", $db);
		while ($row = mysql_fetch_array($res)) {

			print "<tr><td></td><td width=\"20%\">" . $row['file_subject'] . "&nbsp;</td><td width=\"20%\">";
			$num = db_GetRow("SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE formid=" . $row['fileid'] . " AND CRMcustomer=" . $cid . " " . $andstring);
		

			print $num[0];
			$num[0] = 0;
			"</td></tr>";
		}


		print "</td></tr>";
	}

	print "</table>";
	print "</fieldset>";
	print "</td></tr></table>";

} else {
	print "<table><tr><td>&nbsp;&nbsp;&nbsp;<img src='error.gif'> " . $lang['noaction'] . " (cid).</td></tr></table>";
}
EndHTML();


?>
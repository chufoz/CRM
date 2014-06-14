<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This script is for cleaning up CRM databases
 *
 * It will PHYSICALLY delete entity records (deleted before a given date) 
 * after which the std. "check database" function should do the rest
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
extract($_REQUEST);
$EnableRepositorySwitcherOverrule="n";
include("header.inc.php");
	print "</td></tr></table>";
	AdminTabs();
	MainAdminTabs("datman");
print "</table><table border=0 width='90%'><tr><td>&nbsp;&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size=+1>Database cleanup</font>&nbsp;</legend>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=+2>" . $title . "</font><table border=0 width='100%'>";

MustBeAdmin();

// Steps 

if (!$_GET['step']) {			// Warning message

	print "<tr><td><b>This function is very dangerous. With it, you can actually erase data which cannot be recovered.</b></td></tr>";
	print "<tr><td></td></tr>";
	print "<tr><td>It will <u>physically</u> delete entities which were deleted before a date you can select later on. It will delete <b>only</b> the entity records - you will have to run the \"check database\" function after using this function to make sure all referencing data is deleted properly.</td></tr>";
	print "<tr><td></td></tr>";
	print "<tr><td><img src='info.gif'>&nbsp;Needless to say you have to back-up first!</td></tr>";
	print "<tr><td></td></tr>";
	print "<tr><td>For now, you can safely continue; CRM-CTT will not delete anything before warning you.<br><br></td></tr>";
	
	NextStep("b","(select the date)");

} elseif ($_GET['step'] == "b") {	// Select date


	?>
		<TR><TD>Please enter the date - entities <b>closed before this date</b> will later on be deleted</TD></TR>
		<TR><TD><FORM NAME='SelectDate' METHOD='GET'><TABLE>

		<TR><TD>DAY OF MONTH (01-31, 2-char!)</TD><TD><INPUT TYPE='TEXT' SIZE='2' NAME='dom'></TD></TR>
		<TR><TD>MONTH (01-12, 2-char!)</TD><TD><INPUT TYPE='TEXT' SIZE='2' NAME='month'></TD></TR>
		<TR><TD>YEAR (4-char)</TD><TD><INPUT TYPE='TEXT' SIZE='4' NAME='year'>
		<INPUT TYPE='HIDDEN' NAME='step' VALUE='c'>
		</TD></TR>
		</TABLE>
		</FORM>
		</TD></TR>
	<?

	print "<tr><td><img src='arrow.gif'>&nbsp;<a href='javascript:document.SelectDate.submit();' class='bigsort'>Next step</a> (look at what <i>would</i> be deleted)</td></tr>";

	//NextStep("c","","Onclick=document.);
} elseif ($_GET['step'] == "c") {	// Show summary of entities which will be deleted
	if (!$_GET['dom'] || !$_GET['month'] || !$_GET['year'] || strlen($_GET['year'])<>4 || strlen($_GET['dom'])<>2 || strlen($_GET['month'])<>2 || !checkdate($_GET['month'],$_GET['dom'],$_GET['year']) ) {

		// date = false

		print "<tr><td><img src='error.gif'>&nbsp;You know what you did wrong. If not, don't use this function!</td></tr>";

	} else {

		$timestamp = @mktime(0,0,0,$_GET['month'],$_GET['dom'],$_GET['year']);


		if ($timestamp > date('U')) {
			print "<tr><td><img src='info.gif'>&nbsp;<b><font color='#FF0000'>Warning! This date is in the future!</font></b></td></tr>";
		}


		print "<tr><td>Date is valid: " . date("r",$timestamp) . "<br><br></td></tr>";
		print "<tr><td><b>Entities which will be deleted: (including attachements, journal, e-mails, etc)</b></td></tr>";
		print "<tr><td><table border=1 width=100% cellpadding=2 cellspacing=2>";
		print "<tr><td>EID</td><td>" . $lang['owner'] . "</td><td>" . $lang['assignee'] . "</td><td>" . $lang['customer'] . "</td><td>" . $lang['category'] . "</td><td><b><font color='#FF0000'># of attachments</font></b><td>close/delete date</td><td>last update</td><td>by...</td></tr>";


		$sql = "SELECT eid,owner,assignee,CRMcustomer,category,closeepoch,tp,lasteditby FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted='y' AND closeepoch<>'' AND closeepoch<'" . $timestamp . "' ORDER BY closeepoch DESC";
		$result = mcq($sql,$db);
		while ($row = mysql_fetch_array($result)) {

			if ($countit < 200) {
				$t = $row[tp]; // timestamp last edit
				$tp[jaar] = substr($t,0,4);
				$tp[maand] = substr($t,4,2);
				$tp[dag] = substr($t,6,2);
				$tp[uur] = substr($t,8,2);
				$tp[min] = substr($t,10,2);

				$numfiles = GetNumOfAttachments($row['eid']);
				$countfiles += $numfiles;
				print "<tr><td>" . $row['eid'] . "</td><td>" . GetUserName($row['owner']) . "</td><td>" . GetUserName($row['assignee']) . "</td><td>" . GetCustomerName($row['CRMcustomer']) . "</td><td>" . $row['category'] . "</td><td>" . $numfiles ."</td><td>" . date("Y-m-d",$row['closeepoch']) . "</td><td>" . $tp['jaar'] . "-" . $tp['maand'] . "-" . $tp['dag'] . "</td><td>" . GetUserName($row['lasteditby']) . "</td></tr>";
			} 
			$countit++;

		}


		print "</table><br><br></td></tr>";
	
		if ($countit) { 
			print "<tr><td>";
			print "<FORM NAME='SumAll' METHOD='GET'>";
			print "<INPUT TYPE='HIDDEN' NAME='step' VALUE='d'>";
			print "<INPUT TYPE='HIDDEN' NAME='stamp' VALUE='" . $timestamp . "'>";
			print "<INPUT TYPE='HIDDEN' NAME='numofentities' VALUE='" . $countit ."'>";
			print "<INPUT TYPE='HIDDEN' NAME='numoffiles' VALUE='" . $countfiles . "'>";
			print "</FORM>";
			if ($countit>199) {
				print "<strong>Too much entities to print. A total of " . $countit . " entities will be deleted.</strong><br><br>";
			}
			print "<img src='arrow.gif'>&nbsp;<a href='javascript:document.SumAll.submit();' class='bigsort'>Next step</a> (summary of what will be done)</td></tr>";

		} else {
			print "<tr><td><img src='error.gif'>&nbsp;Nothing to do!</td></tr>";
		}

	}
} elseif ($_GET['step'] == "d") {	// (optional) show list of files

		print "<tr><td><strong>This is the last screen before actually deleting something!</strong><br><br></td></tr>";

		print "<tr><td>Query: <b>delete all entities which were logically deleted on " . date("r",$_GET['stamp']) . " or before<br><br></td></tr>";
		print "<tr><td>Database query: DELETE FROM " . $GLOBALS[TBL_PREFIX] . "entity WHERE deleted='y' AND closeepoch<'" . $_GET['stamp'] . "'<br><br></td></tr>";

		print "<tr><td><font color='#FF0000'>" . $_GET['numofentities'] . " entities (containing " . $_GET['numoffiles'] . " attachments) will be deleted!</font><br><br></td></tr>";

		print "<tr><td>";
		print "<FORM NAME='SumAll' METHOD='GET'>";
		print "<INPUT TYPE='HIDDEN' NAME='step' VALUE='e'>";
		print "<INPUT TYPE='HIDDEN' NAME='stamp' VALUE='" . $_GET['stamp'] . "'>";
		print "</FORM>";

		print "<img src='arrow.gif'>&nbsp;<a href='javascript:document.SumAll.submit();' class='bigsort'>Delete them!</a></td></tr>";

} elseif ($_GET['step'] == "e") {	// delete the entities
		
		print "<tr><td>";
		if ($_GET['stamp']>100) {
			$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted='y' AND closeepoch<'" . $_GET['stamp'] . "'";
			mcq($sql,$db);
			//print $sql;

			print "Your query was executed. There's no way back.<BR><BR><b>You now have to run the database check to actually delete all data. Go <a href='checkdb.php?web=1' class='bigsort'>here</a> to do so.</td></tr>";
		}
		

}

print "</table>";
EndHTML();

function NextStep($step,$expl,$js="")
{
	print "<tr><td><img src='arrow.gif'>&nbsp;<a href='db_clean.php?step=" . $step . "&js=" . $js . "' class='bigsort'>Next step</a> " . $expl . "</td></tr>";
}
?>
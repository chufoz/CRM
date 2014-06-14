<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2003 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This function can set a customtab on a certain action
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
include("header.inc.php");

MustBeAdmin();
print "</td></tr></table>";
AdminTabs("customtabs");
//MainAdminTabs("");

$to_tabs = array("overview","add");
$tabbs["overview"] = array("customtabs.php?ovw=1" => "Current navigation tabs");
$tabbs["overviewb"] = array("customtabs.php?ovwb=1" => "Current global bookmarks");
$tabbs["add"] = array("customtabs.php?add=1" => "Add a new navigation tab");
$tabbs["addb"] = array("customtabs.php?addb=1" => "Add a new global bookmark");

if ($_REQUEST['add']) {
	$navid = "add";
} elseif ($_REQUEST['addb']) {
	$navid = "addb";
} elseif ($_REQUEST['ovwb']) {
	$navid = "overviewb";
} else {
	$navid = "overview";
}

InterTabs($to_tabs, $tabbs, $navid);

if ($_REQUEST['newtab'] && $_REQUEST['newtaburl']) {
	if (!is_array($GLOBALS['PersonalTabs'])) {
		$GLOBALS['PersonalTabs'] = array();
	}
	array_push($GLOBALS['PersonalTabs'],array("url" => $_REQUEST['newtaburl'],"name" => $_REQUEST['newtab'],"visible" => $_REQUEST['vf']));
	$GLOBALS['PersonalTabs'] = FlattenArray($GLOBALS['PersonalTabs']);
	mcq("UPDATE $GLOBALS[TBL_PREFIX]settings SET value='" . serialize($GLOBALS['PersonalTabs']) . "' WHERE setting='PersonalTabs'",$db);
	$_REQUEST['ovw'] = 1;
	unset($_REQUEST['add']);
} elseif ($_REQUEST['deltab'] && $_REQUEST['deltaburl']) {
	$tmparr = $GLOBALS['PersonalTabs'];
	for ($x=0;$x<sizeof($tmparr);$x++) {
		$tmparr = $GLOBALS['PersonalTabs'];
		$tmp = base64_decode($_REQUEST['deltaburl']);

		for ($x=0;$x<sizeof($tmparr);$x++) {
			if ($tmparr[$x]['url'] == $tmp) {
				unset($tmparr[$x]);
			}
		}
	}
	$tmparr = FlattenArray($tmparr);

	$GLOBALS['PersonalTabs'] = $tmparr;
	mcq("UPDATE $GLOBALS[TBL_PREFIX]settings SET value='" . serialize($GLOBALS['PersonalTabs']) . "' WHERE setting='PersonalTabs'",$db);
	$_REQUEST['ovw'] = 1;
	unset($_REQUEST['add']);
}


if ($_REQUEST['ovw']) {
	print "<table width=60%><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Current custom navigation tabs</legend>";
	print "<table class='crm' width=75%><tr><td><b>Name</b></td><td><b>URL</b></td><td>Visible for</td><td>Delete</td></tr>";
	$c=100;
	if (is_array($GLOBALS['PersonalTabs']) || sizeof($GLOBALS['PersonalTabs'])==0) {
		for ($x=0;$x<sizeof($GLOBALS['PersonalTabs']);$x++) {
			$element = $GLOBALS['PersonalTabs'][$x];
			if (is_numeric($element['visible'])) {
				$element['visible'] = GetUserName($element['visible']);
			} elseif (strstr($element['visible'],"profile_")) {
				$element['visible'] = GetUserProfiles(str_replace("profile_","",$element['visible']));
			}
			
			print "<tr><td>" . $element['name'] . "</td><td>" . $element['url'] . "</td><td>" . $element['visible'] . "</td><td><a href='customtabs.php?deltab=1&deltaburl=" . base64_encode($element['url']) . "'><img style='border:0' src='delete.gif'></a></td></tr>";
			$c++;
		}
	} else {
		print "<tr><td colspan=2>No custom navigation tabs defined</td></tr>";
	}
	print "</table></legend></td></tr></table>";

} elseif ($_REQUEST['add'] || $_REQUEST['edit']) {
	print "<table><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Add a custom navigation tab</legend>";
	print "<table>";
	print "<tr><td colspan='2'>With this function you can create a navigation tab pointing to a page within CRM-CTT or external (like your corporate webmail or -intranet).</td></tr>";
	print "<tr><td><br>Name to appear in tab bar:<form name='we' method='GET'></td><td><br><input type='text' name='newtab'></td></tr>";
	print "<tr><td><br>Visible for:</td><td><br>";
	print "<select name='vf'>";
	print "<option value='[all]'>[all] (but limited users)</option>";
	print "<option value='[admins]'>[admins only]</option>";

	$sql = "SELECT id, name FROM $GLOBALS[TBL_PREFIX]userprofiles ORDER BY name";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if ($row['FULLNAME'] == "") {
			$row['FULLNAME'] = $row['name'];
		}
		print "<option value='profile_" . $row['id'] . "'>[profile] " . $row['FULLNAME'] . "</option>";
	}

	$sql = "SELECT id, name, FULLNAME FROM $GLOBALS[TBL_PREFIX]loginusers WHERE LEFT(FULLNAME,3) <>'@@@' ORDER BY FULLNAME";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if ($row['FULLNAME'] == "") {
			$row['FULLNAME'] = $row['name'];
		}
		print "<option value='" . $row['id'] . "'>" . $row['FULLNAME'] . "</option>";
	}	

	print "</select>";
	print "</td></tr>";
	print "<tr><td>Tab URL:</td><td><TEXTAREA name='newtaburl' cols=50 rows=4></TEXTAREA></td></tr>";
	print "<tr><td colspan=2>Copy &amp; paste this URL from your browser after having made a selection on the main entity list page<br>or use any other page or one of the defaults:</td></tr></table>";
	
	PrintLinkjes();
	
	print "<table>";
	print "<tr><td></td><td><input type='submit' value='Submit'></form></td></tr>";
	print "</table></legend></td></tr></table>";
} elseif ($_REQUEST['edit']) {
	// nothin'
}

function FlattenArray($arr) {
	
	$flattened = array();

	foreach($arr AS $element) {
		array_push($flattened, $element);
	}

	return($flattened);
}

EndHTML();
?>
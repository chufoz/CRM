<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This file handles personalised start pages
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

extract($_REQUEST);
include("header.inc.php");
$date = date('d-m-Y');
$alreadyshowed = array();
$MainPageCalendar=1;
$MonthsToShow=3;

//$SetOneColumn = true;
$OneTable = true;

if ($SetOneColumn) {
	print "</table>";
	print "<table><tr><td>&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Choose contents per field&nbsp;</legend>";
	print "<table border=1><form name='bla' method='post'><input type='hidden' name='SetOneColumn' value='1'>";

	print "<tr><td><select name='f1'>";
	print "<option value='1'>Nothing</option>";
	print "<option value='2'>$lang[entitiestoday]</option>";	
	print "<option value='3'>Calendar</option>";	
	print "<option value='4'>Recent " . strtolower($lang[entities]) . "</option>";	
	print "<option value='5'>" . strtolower($lang[entities]) . " (" . strtolower($lang[owner]) . ")</option>";	
	print "<option value='6'>" . strtolower($lang[entities]) . " (" . strtolower($lang[assignee]) . ")</option>";	
	print "</select></td></tr>";

	print "<tr><td><select name='f2'>";
	print "<option value='1'>Nothing</option>";
	print "<option value='2'>$lang[entitiestoday]</option>";	
	print "<option value='3'>Calendar</option>";	
	print "<option value='4'>Recent " . strtolower($lang[entities]) . "</option>";	
	print "<option value='5'>" . strtolower($lang[entities]) . " (" . strtolower($lang[owner]) . ")</option>";	
	print "<option value='6'>" . strtolower($lang[entities]) . " (" . strtolower($lang[assignee]) . ")</option>";	
	print "</select></td></tr>";

	print "<tr><td><select name='f3'>";
	print "<option value='1'>Nothing</option>";
	print "<option value='2'>$lang[entitiestoday]</option>";	
	print "<option value='3'>Calendar</option>";	
	print "<option value='4'>Recent " . strtolower($lang[entities]) . "</option>";	
	print "<option value='5'>" . strtolower($lang[entities]) . " (" . strtolower($lang[owner]) . ")</option>";	
	print "<option value='6'>" . strtolower($lang[entities]) . " (" . strtolower($lang[assignee]) . ")</option>";	
	print "</select></td></tr>";

	print "<tr><td><select name='f4'>";
	print "<option value='1'>Nothing</option>";
	print "<option value='2'>$lang[entitiestoday]</option>";	
	print "<option value='3'>Calendar</option>";	
	print "<option value='4'>Recent " . strtolower($lang[entities]) . "</option>";	
	print "<option value='5'>" . strtolower($lang[entities]) . " (" . strtolower($lang[owner]) . ")</option>";	
	print "<option value='6'>" . strtolower($lang[entities]) . " (" . strtolower($lang[assignee]) . ")</option>";	
	print "</select></td></tr>";

	print "<tr><td><select name='f5'>";
	print "<option value='1'>Nothing</option>";
	print "<option value='2'>$lang[entitiestoday]</option>";	
	print "<option value='3'>Calendar</option>";	
	print "<option value='4'>Recent " . strtolower($lang[entities]) . "</option>";	
	print "<option value='5'>" . strtolower($lang[entities]) . " (" . strtolower($lang[owner]) . ")</option>";	
	print "<option value='6'>" . strtolower($lang[entities]) . " (" . strtolower($lang[assignee]) . ")</option>";	
	print "</select></td></tr>";

	
	print "</table><br><input type='submit' name='Submit' value='Submit'></form></fieldet></td></tr></table>";

}

if ($Set) {
	print <<<EOT
		<tr><td>&nbsp;</td><td>
		<fieldset>
			<legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Preference</legend>
			<br>
			Select your preferred lay-out<br><br>
			<img src='arrow.gif'>&nbsp;<a href='customized.php?SetOneColumn=1' class='bigsort'>One column</a>
				<br>
			<img src='arrow.gif'>&nbsp;<a href='customized.php?SetTwoColumns=1' class='bigsort'>Two columns</a>
	
		</fieldset>
		</td></tr>
EOT;
		exit;
}

qlog("Generating dashboard...");

if ($OneTable) {
	print "</table><table border=0><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><table>";
	if ($A) {
		TodaysEntities();
	}
	if ($E) {
		MainCalendar();
	}
	if ($B) {
		RecentEntities();
	}
	if ($C) {
		AllEntitiesOwned();
	}
	if ($D) {
		AllEntitiesAssignee();
	}
	AllEntitiesNotReaded();
	print "</table></td></tr></table>";
	//print "</body></html>";
	EndHTML();
	exit;
}

if ($TwoTable) {
	print "</table><table border=0><tr><td NOWRAP VALIGN='top'><table border=0>";
	if ($A) {
		TodaysEntities();
	}
	if ($B) {
		RecentEntities();
	}
	if ($E) {
		MainCalendar();
	}

	print "</table></td><td NOWRAP VALIGN='top'><table border=0>";
	if ($C) {
		AllEntitiesOwned();
	}
	if ($D) {
		AllEntitiesAssignee();
	}
	AllEntitiesNotReaded();
	print "</table>";
	print "</body></html>";

}
?>

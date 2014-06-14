<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2003 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * Handles new entity forms (e=_new_) and the edit of existing entities (e=[entity_nr])
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
extract($_REQUEST);

$nonavbar = 1;
require("header.inc.php");

MustBeAdmin();

if ($_REQUEST['submitted']) {
		for ($i=0;$i<sizeof($_REQUEST['accessarr_ro']);$i++) {
			if (@in_array("fa_" . $_REQUEST['accessarr_ro'][$i],$_REQUEST['accessarr_full'])) {
					array_push($_REQUEST['accessarr_ro'],"fa_" . $_REQUEST['accessarr_ro'][$i]);
			}
		}

		$acr = serialize($_REQUEST['accessarr_ro']);
		mcq("UPDATE " . $GLOBALS['TBL_PREFIX'] . "extrafields SET accessarray='" . $acr . "' WHERE id=" . $_REQUEST['submitted'], $db);
		$_REQUEST['field'] = $_REQUEST['submitted'];
		?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			window.close();
		//-->
		</SCRIPT>
		<?
}

$ef_name = db_GetRow("SELECT name, accessarray FROM " . $GLOBALS['TBL_PREFIX'] . "extrafields WHERE id=" . $_REQUEST['field']);
$accarr = array();

$accarr = unserialize($ef_name['accessarray']);

print "<form name='EditAccessRights' method='POST'>";
print "<br><table width=95%><tr><td>&nbsp;&nbsp;</td><td>";
print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Access rights for field " . $_REQUEST['field'] . "&nbsp;</legend><br><b>" . $ef_name['name'] . "</b><br><br>Uncheck all boxes to disable detailed access restrictions.<br>";
print "<table class='crm' width='100%'>";

print "<tr><td colspan=2><B>Profiles</B></td><td>Visible</td><td>Modify</td></tr>";

$res = mcq("SELECT name, id FROM " . $GLOBALS['TBL_PREFIX'] . "userprofiles ORDER BY id",$db);
while ($row = mysql_fetch_array($res)) {
	unset($mem);
	$members = GetProfileMembers($row['id']);
	foreach ($members AS $member) {
		$mem .= GetUserName($member) . "<BR>";
	}
	$ttc = "<img src='info.gif' " . PrintToolTipCode($mem) . "> <a style='cursor: help'" . PrintToolTipCode($mem) . "> [members] </a>";
	print "<tr><td colspan=1>" . $row['name'] . "</td><td>" . $ttc . "</td>";
	$id = "P" . $row['id'];
	if (@in_array($id,$accarr)) {
		$ins1 = "CHECKED";
	} else {
		unset($ins1);
	}
	$faid = "fa_P" . $row['id'];
	if (@in_array($faid,$accarr)) {
		$ins2 = "CHECKED";
	} else {
		unset($ins2);
	}



	print "<td><input type='checkbox' " . $ins1 . " name='accessarr_ro[]' class='crm' value='" . $id . "'></td>";
	print "<td><input type='checkbox' " . $ins2 . " name='accessarr_full[]' value='" . $faid . "'></td></tr>";

}

print "<tr><td colspan=4 align='right'><br><input type='button' name='bla' value='Cancel' OnClick='window.close();'>&nbsp;<input type='submit' value='Save and close'><br>&nbsp;</td></tr>";
print "<tr><td colspan=2><B>Users</B></td><td>Visible</td><td>Modify</td></tr>";
$res = mcq("SELECT name, id FROM " . $GLOBALS['TBL_PREFIX'] . "loginusers WHERE name NOT LIKE 'deleted_user_%' ORDER BY id",$db);
while ($row = mysql_fetch_array($res)) {
	print "<tr><td>" . $row['name'] . "</td><td>" . GetUserName($row['id']) . "</td>";
	$id = "U" . $row['id'];
	if (@in_array($id,$accarr)) {
		$ins1 = "CHECKED";
	} else {
		unset($ins1);
	}
	$faid = "fa_U" . $row['id'];
	if (@in_array($faid,$accarr)) {
		$ins2 = "CHECKED";
	} else {
		unset($ins2);
	}

	print "<td><input type='checkbox' " . $ins1 . " name='accessarr_ro[]' value='" . $id . "'></td>";
	print "<td><input type='checkbox' " . $ins2 . " name='accessarr_full[]' value='" . $faid . "'></td></tr>";

}



print "<tr><td colspan=4 align='right'><br><input type='button' name='bla' value='Cancel' OnClick='window.close();'>&nbsp;<input type='hidden' name='submitted' value='" . $_REQUEST['field'] . "'><input type='submit' value='Save and close'><br>&nbsp;</td></tr>";
print "</table>";
print "</fieldset></td></tr></table>";
print "</form>";
EndHTML();
?>
<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2003 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http//www.gnu.org/
 *
 * Handles new entity forms (e=_new_) and the edit of existing entities (e=[entity_nr])
 *
 * Check http//www.crm-ctt.com/ for more information
 **********************************************************************
 */
extract($_REQUEST);

include("header.inc.php");
print "</td></tr></table>";
AdminTabs('users');
UserSectionTabs();
print "<table><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";
MustBeAdminUser();
print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp; User profiles</legend>";
print "<br><table><tr><td>Profiles can be used to create groups of users having the same basic rights. A profile<br>overrules all user-settings you see in the table below. Administrator accounts will not be overruled.</td></tr></table><br>";

if ($_REQUEST['submitted']) {
	ProcessProfileForm($_REQUEST['profnum'], $_REQUEST['prof_name'], $_REQUEST['CLLEVEL'], $_REQUEST['n_HIDEADDTAB'], $_REQUEST['n_HIDECUSTOMERTAB'], $_REQUEST['n_HIDECSVTAB'], $_REQUEST['n_HIDEPBTAB'], $_REQUEST['n_HIDESUMMARYTAB'], $_REQUEST['n_HIDEENTITYTAB'], $_REQUEST['n_SHOWDELETEDVIEWOPTION'], $_REQUEST['dailymail'], $_REQUEST['ENTITYADDFORM'], $_REQUEST['ENTITYEDITFORM'],$_REQUEST['n_LIMITTOCUSTOMERS'],$_REQUEST['statusses'], $_REQUEST['priorities']);
}

if ($_REQUEST['dprof']) {

	DeleteProfile($_REQUEST['dprof']);

} elseif (is_numeric($_REQUEST['profnum'])) {

	$_REQUEST['EditProfile'] = $_REQUEST['profnum'];

	ProfileForm($_REQUEST['EditProfile']);

} else {

	ProfileForm($_REQUEST['EditProfile']);
}


print "</fieldset>";
print "</td></tr></table>";
EndHTML();


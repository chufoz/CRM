<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2003 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This file does several things :)
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

$GLOBALS['CURFUNC'] = "EditProfile::";

$tab = "99";

include("header.inc.php");

print "</table>";
print "&nbsp;<BR>";

$cl = GetClearanceLevel($GLOBALS['USERID']);

if (($cl<>"administrator" && $cl<>"rw" && $cl<>"full-access-own-entities"  && $cl<>"rw" && $cl<>"full-access-see-own-assigned-entities"  && $cl<>"rw" && $cl<>"full-access-see-own-assigned-and-owned-entities") || (strtoupper($LetUserEditOwnProfile)<>"YES")) {
	$legend = "<img src='error.gif'>";
	printbox("Access denied!");
	EndHTML();
} else {


	$sql = "SELECT id,name,EMAIL,RECEIVEDAILYMAIL,FULLNAME,RECEIVEALLOWNERUPDATES,RECEIVEALLASSIGNEEUPDATES,EMAILCREDENTIALS FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name NOT LIKE 'deleted_user_%'";
	$result = mcq($sql,$db);

	$row = mysql_fetch_array($result);


	if ($formsub) {
		if ($_REQUEST['email_user'] && $_REQUEST['email_password'] && $_REQUEST['email_host']) {
				$personalpops = array();
				$personalpops[0] = array();
				$personalpops[0]['popuser'] = $_REQUEST['email_user'];
				$personalpops[0]['poppass'] = $_REQUEST['email_password'];
				$personalpops[0]['pophost'] = $_REQUEST['email_host'];
				$que_ins .= " EMAILCREDENTIALS='" . serialize($personalpops) . "', ";
		} 
		if ($_REQUEST['delete_email']) {
					$que_ins .= " EMAILCREDENTIALS='', ";
		}

		if ($userfullname && $userfullname<>"" && $userfullname<>GetUserName($GLOBALS['USERID'])) {
				$que_ins .= " FULLNAME='" . $userfullname . "',";
		}
		if ($useremail && $useremail<>"" && $useremail<>GetUserEmail($GLOBALS['USERID'])) {
				$que_ins .= " EMAIL='" . $useremail . "',";
		}
		if ($dailymail && $dailymail<>"" && $dailymail<>$row['RECEIVEDAILYMAIL']) {
				$que_ins .= " RECEIVEDAILYMAIL='" . $dailymail . "',";
				$row['RECEIVEDAILYMAIL'] = $dailymail;
		}
		if ($RECEIVEALLOWNERUPDATES && $RECEIVEALLOWNERUPDATES<>"" && $RECEIVEALLOWNERUPDATES<>$row['RECEIVEALLOWNERUPDATES']) {
				$que_ins .= " RECEIVEALLOWNERUPDATES='" . $RECEIVEALLOWNERUPDATES . "',";
				$row['RECEIVEALLOWNERUPDATES'] = $RECEIVEALLOWNERUPDATES;
		}
		if ($RECEIVEALLASSIGNEEUPDATES && $RECEIVEALLASSIGNEEUPDATES<>"" && $RECEIVEALLASSIGNEEUPDATES<>$row['RECEIVEALLASSIGNEEUPDATES']) {
				$que_ins .= " RECEIVEALLASSIGNEEUPDATES='" . $RECEIVEALLASSIGNEEUPDATES . "',";
				$row['RECEIVEALLASSIGNEEUPDATES'] = $RECEIVEALLASSIGNEEUPDATES;
		}
		$sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id=" . $GLOBALS['USERID'] . " AND password=PASSWORD('" . $old_pwd . "')";
		$result = mcq($sql,$db);
		$result = mysql_fetch_array($result);


		if ($new_pwd1<>"") {
				if ($new_pwd1==$new_pwd2 && $result['id']) {
					//print "UPD pwd";
					$que_ins = " password=PASSWORD('" . $new_pwd1 . "'),";
				} else {
					print "<table><td><td><img src='error.gif'>&nbsp;<b>Password mismatch!<b> - password not  saved.</td></tr></table>";
				}
		}

		if ($que_ins) {
			$upd_query = "UPDATE $GLOBALS[TBL_PREFIX]loginusers SET " . $que_ins . " id='" . $GLOBALS['USERID'] . "' WHERE id = '" . $GLOBALS['USERID'] . "'";
			mcq($upd_query,$db);
			$GLOBALS['CURFUNC'] = "EditProfile::";
			qlog("Profile updated (" . $GLOBALS['USERNAME'] . ")");
			$GLOBALS['CURFUNC'] = "";
			ClearCacheArrays();
			uselogger("User " . GetUserName($GLOBALS['USERID']) . " just updated his/her profile","");

			$que_ins = "<font color='#66CC00'><b>OK</b></font><br><br>";
		}
	}

	if ($row['RECEIVEDAILYMAIL']=="Yes") {
		$ins = "SELECTED";
	}
	if ($row['RECEIVEALLASSIGNEEUPDATES']=="Yes") {
		$ins1 = "SELECTED";
	}
	if ($row['RECEIVEALLOWNERUPDATES']=="Yes") {
		$ins2 = "SELECTED";
	}

	print "<table><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";

	print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;" . $lang['username'] . "/"  . $lang['password'] . "/e-mail&nbsp;</legend>";

	print "<table>";

	print "<tr><td>" . $que_ins . "<form name='profile' method='POST'>";
	print "CRM " . $lang['username'] . " :</td><td>" . $GLOBALS['USERNAME'] . "</td></tr>";
	print "<tr><td>" . $lang['name'] . ":</td><td><INPUT TYPE='text' SIZE=70 NAME='userfullname' VALUE='" . GetUserName($GLOBALS['USERID']) . "'></td></tr>";
	print "<tr><td>E-mail:</td><td><INPUT TYPE='text' SIZE=70 NAME='useremail' VALUE='" . GetUserEmail($GLOBALS['USERID']) . "'></td></tr>";

	print "<tr><td>" . $lang['briefover'] . " e-mail:</td><td><select name='dailymail'><option value='No' " . $ins .">No</option><option value='Yes' " . $ins . ">Yes</option></select></td></tr>";
	
	print "<tr><td>E-mail all entity updates (" . $lang['assignee'] . "):</td><td><select name='RECEIVEALLASSIGNEEUPDATES'><option value='No'>No</option><option value='Yes' " . $ins1 . ">Yes</option></select></td></tr>";
	
	print "<tr><td>E-mail all entity updates (" . $lang['owner'] . "):</td><td><select name='RECEIVEALLOWNERUPDATES'><option value='No'>No</option><option value='Yes' " . $ins2 . ">Yes</option></select></td></tr>";

	print "<tr><td colspan=2>&nbsp;<br><b>" . $lang['edit'] . " " . $lang['password'] . "</b></td></tr>";
	print "<tr><td>Current " . $lang['password'] . "</td><td><input type='password' name='old_pwd' value=''></td></tr>";
	print "<tr><td>New " . $lang['password'] . "</td><td><input type='password' name='new_pwd1' value=''></td></tr>";
	print "<tr><td>New " . $lang['password'] . " (again)</td><td><input type='password' name='new_pwd2' value=''></td></tr>";
	print "<tr><td colspan=2>&nbsp;<br></td></tr>";

	print "<tr><td colspan=2>&nbsp;<br><b>" . $lang['edit'] . " personal POP3 e-mail (link will appear on main page)</b></td></tr>";
	
	$personalpops = GetPersonaleMailCredentials($GLOBALS['USERID']);

	print "<tr><td>Username</td><td><input type='text' name='email_user' value='" . $personalpops[0]['popuser'] . "'></td></tr>";
	print "<tr><td>" . $lang['password'] . "</td><td><input type='password' name='email_password' value=''></td></tr>";
	print "<tr><td>Host</td><td><input type='text' name='email_host' value='" . $personalpops[0]['pophost'] . "'></td></tr>";
	if ($personalpops[0]['pophost'] && $personalpops[0]['popuser'] && $personalpops[0]['poppass']) {
		print "<tr><td>Check to delete mail settings:</td><td><input type='checkbox' style='border: 0' name='delete_email' value='1'></td></tr>";
	}
	print "<tr><td colspan=2>&nbsp;<br></td></tr>";

	print "<tr><td><input type='hidden' name='formsub' value='1'><input type='submit' name='bla' value='" . $lang['save'] . "'></tr>";
	//---

	print "</fieldset>";
	print "</td></tr></table>";

	EndHTML();
}


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

?>
<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2003 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * Allows the admin to change the columns shown in the main list
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

//print_r($MainListColumnsToShow);

include("header.inc.php");
if (is_administrator() && $_REQUEST['dothis']<>"personal") {
	print "</td></tr></table>";
	AdminTabs();
	MainAdminTabs("sysman");
}

print "</table><table border=0 width='80%'><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size=+1>$lang[adm]</font>&nbsp;</legend>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=+2>" . $title . "</font><table class='crm' width='100%'>";
if ($_REQUEST['dothis']=="global" && !is_administrator()) {
		// security
		PrintAdminError();
		EndHTML();
		exit;
}

if (!is_administrator() && strtoupper($GLOBALS['LetUserSelectOwnListLayout'])<>"YES") {
		// security
		PrintAdminError();
		EndHTML();
		exit;
}
print "</table>";

if (is_administrator() && strtoupper($GLOBALS['LetUserSelectOwnListLayout'])=="YES" && !$_REQUEST['dothis'] && !$what=="CUST") {
		$legend = "?&nbsp;";
		$printbox_size = "30%";
		printbox("<b>Please choose...</b><br><br><img src='arrow.gif'>&nbsp;<a href='choose_cols.php?dothis=personal' class='bigsort'>Edit your <u>personal</u> preference</a><br>&nbsp;&nbsp;<br><img src='arrow.gif'>&nbsp;<a href='choose_cols.php?dothis=global' class='bigsort'>Edit the <u>global</u> settings</a>");
		print "</body></html>";
		exit;
} elseif (is_administrator() && $_REQUEST['dothis']) {
		//$_REQUEST['dothis'] = $_REQUEST['dothis'];
} elseif (strtoupper($GLOBALS['LetUserSelectOwnListLayout'])=="YES") {
		$_REQUEST['dothis'] = "personal";
} elseif (is_administrator()) {
		$_REQUEST['dothis'] = "global";
}

if ($_REQUEST['dothis']=="global" && !$what) {
	print "<table width=30%><tr><td>&nbsp;</td><td>";
	$legend="Which list?";
	print "<br>Which list do you like to configure?<br><br><img src='arrow.gif'>&nbsp;<a href='choose_cols.php?global=1&dothis=global&what=ML&password=$password' class='bigsort'>The entity lists</a><br><img src='arrow.gif'>&nbsp;<a href='choose_cols.php?global=1&dothis=global&what=CUST&dothis=global&password=$password' class='bigsort'>The customer list</a><br><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password&joepie=1' style='cursor:pointer'>Back to main administration page</a><br>";
	print "</td></tr></table>";
	EndHTML();
	exit;
} elseif ($what=="CUST")  {
		/*
		  `customer_owner` int(11) NOT NULL default '0',
		  `email_owner_upon_adds` enum('no','yes') NOT NULL default 'no',
		*/

		if ($form_sub) {

			$GLOBALS['CustomerListColumnsToShow'] = array();

			$GLOBALS['CustomerListColumnsToShow']['id'] = true;
			
			if ($cb_custname) {
				$GLOBALS['CustomerListColumnsToShow']['cb_custname'] = true;
			}
			if ($cb_contact) {
				$GLOBALS['CustomerListColumnsToShow']['cb_contact'] = true;
			}
			if ($cb_contact_title) {
				$GLOBALS['CustomerListColumnsToShow']['cb_contact_title'] = true;
			}
			if ($cb_contact_phone) {
				$GLOBALS['CustomerListColumnsToShow']['cb_contact_phone'] = true;
			}
			if ($cb_contact_email) {
				$GLOBALS['CustomerListColumnsToShow']['cb_contact_email'] = true;
			}
			if ($cb_cust_address) {
				$GLOBALS['CustomerListColumnsToShow']['cb_cust_address'] = true;
			}
			if ($cb_cust_remarks) {
				$GLOBALS['CustomerListColumnsToShow']['cb_cust_remarks'] = true;
			}
			if ($cb_cust_homepage) {
				$GLOBALS['CustomerListColumnsToShow']['cb_cust_homepage'] = true;
			}
			if ($cb_active) {
				$GLOBALS['CustomerListColumnsToShow']['cb_active'] = true;
			}
			
			$list = GetExtraCustomerFields();
			foreach ($list AS $field) {
				if ($field['fieldtype'] <> "List of values" && $field['fieldtype'] <> "text area" && $field['fieldtype'] <> "text area (rich text)") {
					$varname = "EFID" . $field['id'];
					if ($$varname) {
						$GLOBALS['CustomerListColumnsToShow'][$varname] = true;
					}
				}
			}

			
			
			if ($_REQUEST['dothis']=="global") {
				$sql = "UPDATE " . $GLOBALS['TBL_PREFIX'] . "settings SET value='" . serialize($GLOBALS['CustomerListColumnsToShow']) . "' WHERE setting='CustomerListColumnsToShow'";
				mcq($sql,$db);
			} elseif ($_REQUEST['dothis']=="personal") {
//				setcookie("ccolums_display" . $repository_nr, base64_encode(serialize($GLOBALS['CustomerListColumnsToShow'])));
                                $sql = ("UPDATE " . $GLOBALS['TBL_PREFIX'] . "loginusers SET CLISTLAYOUT='" . serialize($GLOBALS['CustomerListColumnsToShow']) . "' WHERE id=" . $GLOBALS['USERID']);
				mcq($sql, $db);
                                //print $sql;
                                //return;
			} else {
				print "<img src='error.gif'>&nbsp;&nbsp;&nbsp;Error encountered (1)<br>";
				print "</body></html>";
				exit;
			}
			unset($GLOBALS['CustomerListColumnsToShow']);

			print "<table><tr><td>Values are being saved ...</td></tr></table>";
			if ($cur) {
					$cur = base64_decode($cur);
			?>
				<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
				<!--
					document.location = '<? echo $cur . "&" . $epoch;?>';
				//-->
				</SCRIPT>
			<?
			} else {
			?>
				<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
				<!--
					document.location = 'choose_cols.php?dothis=<? echo $_REQUEST['dothis'] . "&" . $epoch;?>';
				//-->
				</SCRIPT>
			<?
			}
			print "</body></html>";
			exit;

		}

		$OKlan = $lang['briefover'] . ", " . strtolower($lang['delentities']) . ", " . strtolower($lang['viewinsertedentities']) . " layout";
									 

		if ($_REQUEST['dothis']=="global") {
			$legend = "<img src='error.gif'>&nbsp;";
			$printbox_size = "100%";
		
			print "<table width='80%'><tr><td>&nbsp;</td><td>";
			printbox("WARNING - the setting you enter here applies to all lists; the main list, the deleted entities list, the inserted customers list, the insert-only limited interface list, and the managementinterface list! Please be aware of the consequences. You can also enable the LetUserSelectOwnListLayout configuration option to let users choose their own preferred lay-out. <br><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password&joepie=1' style='cursor:pointer'>Back to main administration page</a><br>");
			print "</td></tr></table>";		
		
			$sql= "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='CustomerListColumnsToShow'";
			$result= mcq($sql,$db);								
			$resarr=mysql_fetch_array($result);
			$GLOBALS['CustomerListColumnsToShow'] = unserialize($resarr[0]);
			$start = "Select visible " . strtolower($lang['customer']) . " columns <font color='#FF0000'>Global setting</font>&nbsp;";
		} elseif ($_REQUEST['dothis']=="personal") {
			
			$start = "Select visible " . strtolower($lang['customer']) . " columns &nbsp;<font color='#FF0000'>(personal setting)</font>&nbsp;";
			//$GLOBALS['CustomerListColumnsToShow'] = unserialize(base64_decode($_COOKIE['ccolums_display' . $repository_nr]));
			//$WasThereACCookie = "yes";
		} else {
			print "<img src='error.gif'>&nbsp;&nbsp;&nbsp;Error encountered (2)<br>";
			exit;
		}


		print "<form name='choose_colums' method='POST'>";
		print "<table width='80%' class='crm'><tr><td>&nbsp;</td><td>";

		print $start;





		print "<table width='100%' class='crm'>";
		print "<tr><td colspan=2><b>Regular fields:</b></td></tr>";

		print "<tr><td>id</td><td>[always]<input type='hidden' name='form_sub' value='1'><input type='hidden' name='cur' value='" . $cur . "'><input type='hidden' name='dothis' value='" . $_REQUEST['dothis'] . "'></td></tr>";

		if ($GLOBALS['CustomerListColumnsToShow']['cb_custname']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		print "<tr><td>" . $lang['customer'] .   "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_custname'></td></tr>";


		if ($GLOBALS['CustomerListColumnsToShow']['cb_contact']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		print "<tr><td>" . $lang['contact'] .      "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_contact'></td></tr>";
		if ($GLOBALS['CustomerListColumnsToShow']['cb_contact_title']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		print "<tr><td>" . $lang['contacttitle'] .   "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_contact_title'  ></td></tr>";
		if ($GLOBALS['CustomerListColumnsToShow']['cb_contact_phone']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		print "<tr><td>" . $lang['contactphone'] .     "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_contact_phone'    ></td></tr>";
		if ($GLOBALS['CustomerListColumnsToShow']['cb_contact_email']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		print "<tr><td>" . $lang['contactemail'] .   "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_contact_email'  ></td></tr>";
		if ($GLOBALS['CustomerListColumnsToShow']['cb_cust_address']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		print "<tr><td>" . $lang['customeraddress'] .   "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_cust_address'  ></td></tr>";
		if ($GLOBALS['CustomerListColumnsToShow']['cb_cust_remarks']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		print "<tr><td>" . $lang['custremarks'] .    "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_cust_remarks'   ></td></tr>";
		if ($GLOBALS['CustomerListColumnsToShow']['cb_cust_homepage']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		print "<tr><td>" . $lang['custhomepage'] .  "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_cust_homepage' ></td></tr>";
		if ($GLOBALS['CustomerListColumnsToShow']['cb_active']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		print "<tr><td>Active</td><td><input type='checkbox' " . $a . " class='radio' name='cb_active'></td></tr>";

		$cf =  "<tr><td colspan=2><br><b>Custom fields:</b></td></tr>";

		$list = GetExtraCustomerFields();
		
		//print_r($GLOBALS['CustomerListColumnsToShow']);

		foreach ($list AS $field) {
				$varname = "EFID" . $field['id'];
				if ($field['fieldtype'] <> "List of values" && $field['fieldtype'] <> "text area" && $field['fieldtype'] <> "text area (rich text)") {
					if ($GLOBALS['CustomerListColumnsToShow'][$varname]) {
						$a = "CHECKED";
					} else {
						$a = "";
					}
					$cf .= "<tr><td>" . $field['name'] . "</td><td><input type='checkbox' " . $a . " class='radio' name='EFID" . $field['id'] . "'></td></tr>";
				}
		}
			
		
		print $cf;


		print "<tr><td></td><td align='right'><input type='hidden' name='what' value='CUST'><input type='submit' name='whatever' value='" . $lang['save'] . "'></td></tr>";
		print "</table>";
		print "</td></tr></table>";
		print "</form>";

	EndHTML();
	exit;
}



if ($form_sub) {

	$MainListColumnsToShowNew = array();

	$MainListColumnsToShowNew['id'] = true;
	
	if ($cb_cust) {
		$MainListColumnsToShowNew['cb_cust'] = true;
	}
	if ($cb_owner) {
		$MainListColumnsToShowNew['cb_owner'] = true;
	}
	if ($cb_assignee) {
		$MainListColumnsToShowNew['cb_assignee'] = true;
	}
	if ($cb_status) {
		$MainListColumnsToShowNew['cb_status'] = true;
	}
	if ($cb_priority) {
		$MainListColumnsToShowNew['cb_priority'] = true;
	}
	if ($cb_category) {
		$MainListColumnsToShowNew['cb_category'] = true;
	}
	if ($cb_duedate) {
		$MainListColumnsToShowNew['cb_duedate'] = true;
	}
	if ($cb_alarmdate) {
		$MainListColumnsToShowNew['cb_alarmdate'] = true;
	}
	if ($cb_lastupdate) {
		$MainListColumnsToShowNew['cb_lastupdate'] = true;
	}
	if ($cb_duration) {
		$MainListColumnsToShowNew['cb_duration'] = true;
	}
	if ($cb_creationdate) {
		$MainListColumnsToShowNew['cb_creationdate'] = true;
	}
// CUSTOMER FIELDS FROM HERE

	if ($cb_contact) {
		$MainListColumnsToShowNew['cb_contact'] = true;
	}
	if ($cb_contact_title) {
		$MainListColumnsToShowNew['cb_contact_title'] = true;
	}
	if ($cb_contact_phone) {
		$MainListColumnsToShowNew['cb_contact_phone'] = true;
	}
	if ($cb_contact_email) {
		$MainListColumnsToShowNew['cb_contact_email'] = true;
	}
	if ($cb_cust_address) {
		$MainListColumnsToShowNew['cb_cust_address'] = true;
	}
	if ($cb_cust_remarks) {
		$MainListColumnsToShowNew['cb_cust_remarks'] = true;
	}
	if ($cb_cust_homepage) {
		$MainListColumnsToShowNew['cb_cust_homepage'] = true;
	}



	$list = GetExtraFields();

	foreach ($list AS $field) {

		$varname = "EFID" . $field['id'];
		if ($$varname) {
			$MainListColumnsToShowNew[$varname] = true;
		}
	}
	
	$list = GetExtraCustomerFields();

	foreach ($list AS $field) {
		$varname = "EFID" . $field['id'];
		if ($$varname) {
			$MainListColumnsToShowNew[$varname] = true;
		}
	}
	
	
	if ($_REQUEST['dothis']=="global") {
		$sql = "UPDATE " . $GLOBALS['TBL_PREFIX'] . "settings SET value='" . serialize($MainListColumnsToShowNew) . "' WHERE setting='MainListColumnsToShow'";
		mcq($sql,$db);
	} elseif ($_REQUEST['dothis']=="personal") {
                $sql = "UPDATE " . $GLOBALS['TBL_PREFIX'] . "loginusers SET ELISTLAYOUT='" . serialize($MainListColumnsToShowNew) . "' WHERE id=" . $GLOBALS['USERID'];
		mcq($sql, $db);
	//	setcookie("colums_display" . $repository_nr, base64_encode(serialize($MainListColumnsToShowNew)));
	} else {
		print "<img src='error.gif'>&nbsp;&nbsp;Error encounterd (3)<br>";
		print "</body></html>";
		exit;
	}
	unset($MainListColumnsToShowNew);

	print "<table><tr><td>Values are being saved ...</td></tr></table>";
	if ($cur) {
			$cur = base64_decode($cur);
	?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
			document.location = '<? echo $cur . "&" . $epoch;?>';
		//-->
		</SCRIPT>
	<?
	} else {
	?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
			document.location = 'choose_cols.php?dothis=<? echo $_REQUEST['dothis'] . "&" . $epoch;?>';
		//-->
		</SCRIPT>
	<?
	}
	print "</body></html>";
	exit;

}

$OKlan = $lang['briefover'] . ", " . strtolower($lang['delentities']) . ", " . strtolower($lang['viewinsertedentities']) . " layout &nbsp;<font color='#FF0000'>(personal setting)</font>&nbsp;";
                             



if ($_REQUEST['dothis']=="personal") {
//	$MainListColumnsToShowCookie = unserialize(base64_decode($_COOKIE['colums_display'. $repository_nr]));
	if ($MainListColumnsToShowCookie<>"") {
		$MainListColumnsToShow = $MainListColumnsToShowCookie;
	} else {
	}
	$start = "$OKlan<br>";
} elseif ($_REQUEST['dothis']=="global") {
	$legend = "<img src='error.gif'>&nbsp;";
	$printbox_size = "100%";
	print "<table width='80%'><tr><td>&nbsp;</td><td>";
	printbox("WARNING - the setting you enter here applies to all lists; the main list, the deleted entities list, the inserted customers list, the insert-only limited interface list, and the managementinterface list! Please be aware of the consequences. You can also enable the LetUserSelectOwnListLayout configuration option to let users choose their own preferred lay-out. <br><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password&joepie=1' style='cursor:pointer'>Back to main administration page</a><br>");
	print "</td></tr></table>";
	$sql= "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='MainListColumnsToShow'";
	$result= mcq($sql,$db);								
	$resarr=mysql_fetch_array($result);
	$MainListColumnsToShow = unserialize($resarr[0]);
	$start = "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Select visible columns <font color='#FF0000'>Global setting</font>&nbsp;</legend>";
} else {
	print "<img src='error.gif'>&nbsp;&nbsp;Error encounterd (4)<br>";
	exit;
}


print "<form name='choose_colums' method='POST'>";
print "<table width='80%' class='crm'><tr><td>&nbsp;</td><td>";

print $start;



print "<table width='100%' border='0'>";
print "<tr><td colspan=2><b>Regular fields:</b></td></tr>";

print "<tr><td>id</td><td>[always]<input type='hidden' name='form_sub' value='1'><input type='hidden' name='cur' value='" . $cur . "'><input type='hidden' name='dothis' value='" . $_REQUEST['dothis'] . "'></td></tr>";

if ($MainListColumnsToShow['cb_cust']) {
	$a = "CHECKED";
} else {
	unset($a);
}
print "<tr><td>" . $lang['customer'] .   "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_cust'      ></td></tr>";
if ($MainListColumnsToShow['cb_owner']) {
	$a = "CHECKED";
} else {
	unset($a);
}
print "<tr><td>" . $lang['owner'] .      "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_owner'     ></td></tr>";
if ($MainListColumnsToShow['cb_assignee']) {
	$a = "CHECKED";
} else {
	unset($a);
}
print "<tr><td>" . $lang['assignee'] .   "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_assignee'  ></td></tr>";
if ($MainListColumnsToShow['cb_status']) {
	$a = "CHECKED";
} else {
	unset($a);
}
print "<tr><td>" . $lang['status'] .     "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_status'    ></td></tr>";
if ($MainListColumnsToShow['cb_priority']) {
	$a = "CHECKED";
} else {
	unset($a);
}
print "<tr><td>" . $lang['priority'] .   "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_priority'  ></td></tr>";
if ($MainListColumnsToShow['cb_category']) {
	$a = "CHECKED";
} else {
	unset($a);
}
print "<tr><td>" . $lang['category'] .   "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_category'  ></td></tr>";
if ($MainListColumnsToShow['cb_duedate']) {
	$a = "CHECKED";
} else {
	unset($a);
}
print "<tr><td>" . $lang['duedate'] .    "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_duedate'   ></td></tr>";
if ($MainListColumnsToShow['cb_alarmdate']) {
	$a = "CHECKED";
} else {
	unset($a);
}
print "<tr><td>" . $lang['alarmdate'] .  "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_alarmdate' ></td></tr>";
if ($MainListColumnsToShow['cb_lastupdate']) {
	$a = "CHECKED";
} else {
	unset($a);
}
print "<tr><td>" . $lang['lastupdate'] . "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_lastupdate'></td></tr>";

if ($MainListColumnsToShow['cb_creationdate']) {
	$a = "CHECKED";
} else {
	unset($a);
}
print "<tr><td>" . $lang['creationdate'] . "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_creationdate'></td></tr>";


if ($MainListColumnsToShow['cb_duration']) {
	$a = "CHECKED";
} else {
	unset($a);
}
print "<tr><td>Age/duration</td><td><input type='checkbox' " . $a . " class='radio' name='cb_duration'></td></tr>";



$cf =  "<tr><td colspan=2><br><b>Extra fields:</b></td></tr>";

$list = GetExtraFields();

foreach ($list AS $field) {
		$varname = "EFID" . $field['id'];
		if ($field['fieldtype'] <> "List of values" && $field['fieldtype'] <> "text area" && $field['fieldtype'] <> "text area (rich text)") {
			if ($MainListColumnsToShow[$varname]) {
				$a = "CHECKED";
			} else {
				$a = "";
			}
			$cf .= "<tr><td>" . $field['name'] . "</td><td><input type='checkbox' " . $a . " class='radio' name='EFID" . $field['id'] . "'></td></tr>";
		}
}

$cf .=  "<tr><td colspan=2><br><b>" . $lang['customer'] . " fields:</b></td></tr>";

		if ($MainListColumnsToShow['cb_contact']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		$cf .= "<tr><td>" . $lang['contact'] .      "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_contact'></td></tr>";
		if ($MainListColumnsToShow['cb_contact_title']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		$cf .= "<tr><td>" . $lang['contacttitle'] .   "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_contact_title'  ></td></tr>";
		if ($MainListColumnsToShow['cb_contact_phone']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		$cf .= "<tr><td>" . $lang['contactphone'] .     "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_contact_phone'    ></td></tr>";
		if ($MainListColumnsToShow['cb_contact_email']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		$cf .= "<tr><td>" . $lang['contactemail'] .   "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_contact_email'  ></td></tr>";
		if ($MainListColumnsToShow['cb_cust_address']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		$cf .= "<tr><td>" . $lang['customeraddress'] .   "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_cust_address'  ></td></tr>";
		if ($MainListColumnsToShow['cb_cust_remarks']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		$cf .= "<tr><td>" . $lang['custremarks'] .    "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_cust_remarks'   ></td></tr>";
		if ($MainListColumnsToShow['cb_cust_homepage']) {
			$a = "CHECKED";
		} else {
			unset($a);
		}
		$cf .= "<tr><td>" . $lang['custhomepage'] .  "</td><td><input type='checkbox' " . $a . " class='radio' name='cb_cust_homepage' ></td></tr>";

$cf .=  "<tr><td colspan=2><br><b>Extra " . $lang['customer'] . " fields:</b></td></tr>";

$list = GetExtraCustomerFields();

foreach ($list AS $field) {
		$varname = "EFID" . $field['id'];
		if ($field['fieldtype'] <> "List of values" && $field['fieldtype'] <> "text area" && $field['fieldtype'] <> "text area (rich text)") {
			if ($MainListColumnsToShow[$varname]) {
				$a = "CHECKED";
			} else {
				$a = "";
			}
			$cf .= "<tr><td>" . $field['name'] . "</td><td><input type='checkbox' " . $a . " class='radio' name='EFID" . $field['id'] . "'></td></tr>";
		}
}
print $cf;

print "<tr><td></td><td align='right'><input type='hidden' name='what' value='ML'><input type='submit' name='whatever' value='" . $lang['save'] . "'></td></tr>";

print "</table>";
print "</td></tr></table>";
print "</form>";


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
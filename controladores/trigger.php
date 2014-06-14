<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2003 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This function can set a trigger on a certain action
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
include("header.inc.php");

MustBeAdmin();
print "</td></tr></table>";
AdminTabs("triggers");

	$to_tabs = array("current","status","priority","customer","ownerassignee","extrafield","miscellaneous","admin");
	$tabbs["current"] = array("trigger.php?ovw=1" => "Overview", "comment" => "An overview of currently configured triggers.");
	$tabbs["status"] = array("trigger.php?add=status" => "New status trigger", "comment" => "Add a new trigger which fires when the status of an entity is altered");
	$tabbs["priority"] = array("trigger.php?add=priority" => "New priority trigger", "comment" => "Add a new trigger which fires when the priority of an entity is altered");
	$tabbs["customer"] = array("trigger.php?add=customer" => "New " . $lang['customer'] . " trigger", "comment" => "Add a new trigger which fires when the customer of an entity is altered");
	$tabbs["ownerassignee"] = array("trigger.php?add=ownerassignee" => "New owner/assignee trigger", "comment" => "Add a new trigger which fires when the owner or assignee of an entity is altered");
	$tabbs["extrafield"] = array("trigger.php?add=extrafield" => "New extra field trigger", "comment" => "Add a new trigger which fires when the value of an extra fiels is altered");
	$tabbs["miscellaneous"] = array("trigger.php?add=miscellaneous" => "New misc. trigger", "comment" => "Add a miscellaneous trigger (limited user add, limited user edit, duedate reached)");
	$tabbs["admin"] = array("trigger.php?add=admin" => "New administrative trigger", "comment" => "Add an administrative trigger.");

	if ($_REQUEST['add']) {
		$navid = $_REQUEST['add'];
	} else {
		$navid = "current";
	}
	
	
	InterTabs($to_tabs, $tabbs, $navid);


if ($_REQUEST['del_trigger']) {
	$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]triggers WHERE tid='" . $_REQUEST['del_trigger'] . "'";
	mcq($sql,$db);
	qlog("Deleted trigger " . $_REQUEST['del_trigger']);
	uselogger("Deleted trigger " . $_REQUEST['del_trigger'],"");
}

// ========================================================================================================================================================


if ($_REQUEST['fetch']) {
	$fetched_trigger = DB_GetRow("SELECT * FROM $GLOBALS[TBL_PREFIX]triggers WHERE tid='" . $_REQUEST['fetch'] . "'");
	//print_r($fetched_trigger);
}

// ========================================================================================================================================================

if ($_REQUEST['onchange'] && $_REQUEST['to_value'] && $_REQUEST['action']) {

	// First check if this trigger doesn't exist already
	$sql = "SELECT tid FROM $GLOBALS[TBL_PREFIX]triggers WHERE onchange='" . $_REQUEST['onchange'] ."' AND action='" . $_REQUEST['action'] . "' AND to_value='" . $_REQUEST['to_value'] . "' AND template_fileid='" . $_REQUEST['template_fileid'] . "'";
	$result = mcq($sql,$db);
	$row = mysql_fetch_array($result);
	if ($row['tid'] && !$_REQUEST['fetched']) {
		print "<table><tr><td><img src='error.gif'> Trigger not added, it exists already!</td></tr></table>";
		qlog("NOT added trigger - it already exists");
	
	} else {
		if ($_REQUEST['fetched']) {
			$sql = "UPDATE $GLOBALS[TBL_PREFIX]triggers SET onchange='" . $_REQUEST['onchange'] ."',action='" . $_REQUEST['action'] ."',to_value='" . $_REQUEST['to_value'] ."',template_fileid='" . $_REQUEST['template_fileid'] ."',attach='" . $_REQUEST['attach'] ."',report_fileid='" . $_REQUEST['report_fileid'] ."',comment='" . $_REQUEST['trigger_comment'] . "' WHERE tid=" . $_REQUEST['fetched'];

			mcq($sql,$db);
			$tr = mysql_insert_id();
			qlog("Updated trigger " . $tr);
			uselogger("Updated trigger " . $tr,"");
		} else {
			// Insert the trigger
			$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]triggers(onchange,action,to_value,template_fileid,attach,report_fileid,comment) VALUES('" . $_REQUEST['onchange'] ."','" . $_REQUEST['action'] . "','" . $_REQUEST['to_value'] . "','" . $_REQUEST['template_fileid'] . "','" . $_REQUEST['attach'] . "','" . $_REQUEST['report_fileid'] . "','" . $_REQUEST['trigger_comment'] . "')";
			mcq($sql,$db);
			$tr = mysql_insert_id();
			qlog("Added trigger " . $tr);
			uselogger("Added trigger " . $tr,"");
		}
		
	}
}
$actionlist = array();

if ($_REQUEST['add']) {
	if ($fetched_trigger['action'] == "mail ower") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,strtolower("<option $ins value='mail owner'>[mail $lang[owner]]</option>"));
	if ($fetched_trigger['action'] == "mail assignee") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,strtolower("<option $ins value='mail assignee'>[mail $lang[assignee]]</option>"));
	if ($fetched_trigger['action'] == "mail admin") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,strtolower("<option $ins value='mail admin'>[mail admin]</option>"));
	if ($fetched_trigger['action'] == "mail customer") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,strtolower("<option $ins value='mail customer'>[mail $lang[customer]]</option>"));
	array_push($actionlist,strtolower("<option value=''>--------- global entity changes -----------</option>"));
	if ($fetched_trigger['action'] == "delete entity") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,strtolower("<option $ins value='delete entity'>[delete the entity]</option>"));
	if ($fetched_trigger['action'] == "undelete entity") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,strtolower("<option $ins value='undelete entity'>[undelete the entity]</option>"));
	if ($fetched_trigger['action'] == "make entity read-only") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,strtolower("<option $ins value='make entity read-only'>[make entity read-only]</option>"));
	if ($fetched_trigger['action'] == "make entity read-write") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,strtolower("<option $ins value='make entity read-write'>[make entity read-write]</option>"));
	if ($fetched_trigger['action'] == "make entity private") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,strtolower("<option $ins value='make entity private'>[make entity private]</option>"));
	if ($fetched_trigger['action'] == "make entity public") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,strtolower("<option $ins value='make entity public'>[make entity public]</option>"));
	if ($fetched_trigger['action'] == "set closedate") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,strtolower("<option $ins value='set closedate'>[set entity closure date (stop-clock)]</option>"));
	if ($fetched_trigger['action'] == "unset closedate") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,strtolower("<option $ins value='unset closedate'>[delete entity closure date (start-clock)]</option>"));
	if ($fetched_trigger['action'] == "re-set opendate") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,strtolower("<option $ins value='re-set opendate'>[re-set entity open date (restart-clock)]</option>"));

	array_push($actionlist,strtolower("<option value=''>---------- status changes -----------------</option>"));

	$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]statusvars ORDER BY varname";
	$result= mcq($sql,$db);
	while($options = mysql_fetch_array($result)) {
		if ($fetched_trigger['action'] == "Set status to " . $options[varname]) {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}
		array_push($actionlist,"<option $ins style='background:" . $options['color'] . "' value='Set status to " . $options[varname] . "'>Set status to " . $options[varname] . "</option>");
	}

	array_push($actionlist,strtolower("<option value=''>---------- priority changes ---------------</option>"));
	$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]priorityvars ORDER BY varname";
	$result= mcq($sql,$db);
	while($options = mysql_fetch_array($result)) {
		if ($fetched_trigger['action'] == "Set priority to " . $options[varname]) {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}
		array_push($actionlist,"<option $ins style='background:" . $options['color'] . "' value='Set priority to " . $options[varname] . "'>Set priority to " . $options[varname] . "</option>");
	}

	array_push($actionlist,strtolower("<option value=''>---------- owner changes ------------------</option>"));
	if ($fetched_trigger['action'] == "Set owner to customer_owner") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,"<option $ins value='Set owner to customer_owner'>Set owner to [customer owner]</option>");

	$sql = "SELECT id, name, FULLNAME FROM $GLOBALS[TBL_PREFIX]loginusers WHERE LEFT(FULLNAME,3) <>'@@@' ORDER BY FULLNAME";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if ($row['FULLNAME'] == "") {
			$row['FULLNAME'] = $row['name'];
		}
		if ($fetched_trigger['action'] == "Set owner to " . $row['id']) {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}
		array_push($actionlist,"<option $ins value='Set owner to " . $row['id'] . "'>Set owner to " . $row['FULLNAME'] . "</option>");
	}
	$sql = "SELECT * FROM " . $GLOBALS['TBL_PREFIX'] . "extrafields WHERE fieldtype LIKE 'User-list%'";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if ($fetched_trigger['action'] == "Set owner to @EFID" . $row['id'] . "@") {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}

		if ($row['tabletype'] == "entity") {
			array_push($actionlist,"<option $ins value='Set owner to @EFID" . $row['id'] . "@'>Set owner to the user selected in " . $lang['entity'] . "-field " . $row['name'] . "</option>");
		} else {
			array_push($actionlist,"<option $ins value='Set owner to @EFID" . $row['id'] . "@'>Set owner to the user selected in " . $lang['customer'] . "-field " . $row['name'] . "</option>");
		}
	}


	array_push($actionlist,strtolower("<option value=''>---------- assignee changes ---------------</option>"));

	$sql = "SELECT id, name, FULLNAME FROM $GLOBALS[TBL_PREFIX]loginusers WHERE LEFT(FULLNAME,3) <>'@@@' ORDER BY FULLNAME";
	$result = mcq($sql,$db);
	array_push($actionlist,"<option value='Set assignee to customer_owner'>Set assignee to [customer owner]</option>");
	while ($row = mysql_fetch_array($result)) {
		if ($row['FULLNAME'] == "") {
			$row['FULLNAME'] = $row['name'];
		}
		if ($fetched_trigger['action'] == "Set assignee to " . $row['id']) {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}
		array_push($actionlist,"<option $ins value='Set assignee to " . $row['id'] . "'>Set assignee to " . $row['FULLNAME'] . "</option>");
	}

	$sql = "SELECT * FROM " . $GLOBALS['TBL_PREFIX'] . "extrafields WHERE fieldtype LIKE 'User-list%'";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		
		if ($fetched_trigger['action'] == "Set assignee to @EFID" . $row['id'] . "@") {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}

		if ($row['tabletype'] == "entity") {
			array_push($actionlist,"<option $ins value='Set assignee to @EFID" . $row['id'] . "@'>Set assignee the user selected in " . $lang['entity'] . "-field " . $row['name'] . "</option>");
		} else {
			array_push($actionlist,"<option $ins value='Set assignee to @EFID" . $row['id'] . "@'>Set assignee the user selected in " . $lang['customer'] . "-field " . $row['name'] . "</option>");
		}
	}

	array_push($actionlist,strtolower("<option value=''>---------- mail user actions --------------</option>"));

	$sql = "SELECT id, name, FULLNAME FROM $GLOBALS[TBL_PREFIX]loginusers WHERE EMAIL<>'' AND LEFT(FULLNAME,3) <>'@@@' ORDER BY FULLNAME";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if ($row['FULLNAME'] == "") {
			$row['FULLNAME'] = $row['name'];
		}
		if ($fetched_trigger['action'] == "mail user @" . $row['id'] . "@") {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}

		array_push($actionlist,"<option $ins value='mail user @" . $row['id'] . "@'>Mail user " . $row['FULLNAME'] . "</option>");
	}
	
	$sql = "SELECT * FROM " . $GLOBALS['TBL_PREFIX'] . "extrafields WHERE fieldtype LIKE 'User-list%'";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if ($fetched_trigger['action'] == "Mail user @EFID" . $row['id'] . "@") {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}
		if ($row['tabletype'] == "entity") {
			array_push($actionlist,"<option $ins value='Mail user @EFID" . $row['id'] . "@'>Mail the user selected in " . $lang['entity'] . "-field " . $row['name'] . "</option>");
		} else {
			array_push($actionlist,"<option $ins value='Mail user @EFID" . $row['id'] . "@'>Mail the user selected in  " . $lang['customer'] . "-field " . $row['name'] . "</option>");
		}
	}

	array_push($actionlist,strtolower("<option value=''>---------- mail customer actions ----------</option>"));

	$sql = "SELECT id,custname,contact_email FROM $GLOBALS[TBL_PREFIX]customer WHERE contact_email<>'' ORDER BY custname";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if ($fetched_trigger['action'] == "mail cust @" . $row['id'] . "@") {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}

		array_push($actionlist,"<option $ins value='mail cust @" . $row['id'] . "@'>Mail " . strtolower($lang['customer']) . " " . $row['custname'] . "</option>");
	}
	$templatelist = array();

	$sql = "SELECT fileid,filename,filetype FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid=0 AND filetype = 'TEMPLATE_HTML'";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {

		if ($fetched_trigger['template_fileid'] == $row['fileid']) {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}

		array_push($templatelist,"<option $ins value='" . $row['fileid'] . "'>" . $row['filename'] . "</option>");
	}


	


	array_push($actionlist,strtolower("<option value=''>---------- extend duedate actions ---------</option>"));
	if ($fetched_trigger['action'] == "duedate_extent days 1") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,"<option $ins value='duedate_extent days 1'>Extend duedate with 1 day</option>");
	if ($fetched_trigger['action'] == "duedate_extent days 2") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,"<option $ins value='duedate_extent days 2'>Extend duedate with 2 days</option>");
	if ($fetched_trigger['action'] == "duedate_extent days 3") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,"<option $ins value='duedate_extent days 3'>Extend duedate with 3 days</option>");
	if ($fetched_trigger['action'] == "duedate_extent days 4") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,"<option $ins value='duedate_extent days 4'>Extend duedate with 4 days</option>");
	if ($fetched_trigger['action'] == "duedate_extent days 5") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,"<option $ins value='duedate_extent days 5'>Extend duedate with 5 days</option>");
	if ($fetched_trigger['action'] == "duedate_extent days 10") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,"<option $ins value='duedate_extent days 10'>Extend duedate with 10 days</option>");
	if ($fetched_trigger['action'] == "duedate_extent days 20") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,"<option $ins value='duedate_extent days 20'>Extend duedate with 20 days</option>");
	if ($fetched_trigger['action'] == "duedate_extent days 30") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	array_push($actionlist,"<option $ins value='duedate_extent days 30'>Extend duedate with 30 days</option>");

	array_push($actionlist,strtolower("<option value=''>---------- change entity form id ----------</option>"));

	$sql = "SELECT fileid,filename,file_subject FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid=0 AND filetype = 'TEMPLATE_HTML_FORM'";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if ($fetched_trigger['action'] == "set form-id to " . $row['fileid']) {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}
		array_push($actionlist,"<option $ins value='set form-id to " . $row['fileid'] . "'>Set entity form to " . $row['filename'] . " (" . $row['file_subject'] . ")</option>");
	}




	$triggerlist = array();
	array_push($triggerlist,"status");
	array_push($triggerlist,"priority");
	array_push($triggerlist,"owner");
	array_push($triggerlist,"assignee");
	array_push($triggerlist,"customer");
	array_push($triggerlist,"@EF_");
}

print "</table>";
print "<table border=0 width='95%'><tr><td>&nbsp;&nbsp;";
print "</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+2'>Event triggers</font>&nbsp;</legend>";

print "<table cellspacing='2' cellpadding='8' border=0 width=90%>";

if (!$_REQUEST['add']) {
	print "<tr><td colspan=5>Using event triggers, you can let CRM-CTT mail the owner, assignee, or customer, or you can update specific entity fields with a value. This way you can create workflows. <br><BR>Please mind: event triggers bypass the 'private' flag; the trigger will fire on a private entity.</td></tr>";

	print "<tr><td colspan=5>Any entity updates caused by a trigger will <b>not</b> trigger another event to avoid looped triggers.</td></tr>";

	print "<tr><td colspan=5>All references to users and customers refer to the <u>new</u> value (e.g. <u>after</u> the entity received its update). These rules also apply<br>to newly added entities; a [something else] trigger will <u>always</u> be triggered when a new entity is added and the concerning field was given a value.</td></tr>";
}

	
	$report_select = "<select name='report_fileid'><option value=''>Don't attach</option><option value='999999999999999'>Std. PDF-report</option>";
	$sql = "SELECT fileid,filename,creation_date,username FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='0' AND filetype='TEMPLATE_REPORT'";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if ($fetched_trigger['report_fileid']==$row['fileid']) {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}
		$report_select .= "<option $ins value = '" . $row['fileid'] ."'>" . $row['filename'] . "</option>";
	}
$report_select .= "</select>";



// ========================================================================================================================================================

if ($_REQUEST['add'] == "status") {
	print "<tr><td colspan=10><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Add/edit a " . strtolower($lang['status']) . " trigger</legend><table class='crm4'>";
	//print "<tr><td colspan=5><br><b><u>" . $lang['status'] . "</u></b></td></tr>";
	print "<FORM METHOD='POST' NAME='TriggerAddForm'>";
	if ($_REQUEST['fetch']) {
		print "<input type='hidden' name='fetched' value='" . $_REQUEST['fetch'] . "'>";
	}
	print "<tr><td>On value change of field</td><td><b>$lang[status]<input type='hidden' name='onchange' value='status'></b></td></tr>";
	print "<tr><td>When the value is updated to</td><td><select name='to_value'>";
	print "<option value='@SE@'>[something else]</option>";
	$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]statusvars ORDER BY varname";
		$result= mcq($sql,$db);
		while($options = mysql_fetch_array($result)) {
			if ($options['varname'] == $fetched_trigger['to_value']) {
				$ins = "SELECTED";
			} else {
				unset($ins);
			}
			print "<option $ins style='background:" . $options[color] . "' value='$options[varname]' $a>$options[varname]</option>";
		}

	print "</select>";
	print "</td></tr><tr><td>Perform action</td><td><select name='action' onchange='CheckMailTrigger()';>";
	foreach ($actionlist AS $action) {
		print $action;
	}
	print "</select></td></tr>";
	print "<tr><td>Attach report to mail (mail actions only)</td><td>";
	print $report_select;
	print "</td></tr>";
	print "<tr><td>Mail template (mail actions only)</td><td><select name='template_fileid'>";
	print "<option value=''>[default]</option>";
			foreach ($templatelist AS $template) {
				print $template;
			}
	print "</select></td></tr>";
	if ($fetched_trigger['attach'] == "y") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	print "<tr><td>Attach mail to entity/customer</td><td><select name='attach'><option value='n'>No</option><option $ins value='y'>Yes</option></select></td></tr>";
	print "<tr><td>Comment</td><td><textarea cols='50' rows='3' name='trigger_comment'>" . $fetched_trigger['comment'] . "</textarea></td></tr>";
	print "<tr><td colspan=2><input type='hidden' name='add' value=''><input type='submit' value='" . $lang['go'] . "'></td></tr>";
	print "</form>";
	print "</table></fieldset></td></tr>";
	print "<td><td colspan=10>&nbsp;</td></tr>";

}
// ========================================================================================================================================================
if ($_REQUEST['add'] == "priority") {
	print "<tr><td colspan=10><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Add/edit a " . strtolower($lang['priority']) . " trigger</legend><table class='crm4'>";
	//print "<tr><td colspan=5><br><b><u>" . $lang[''] . "</u></b></td></tr>";
	print "<FORM METHOD='POST' NAME='TriggerAddForm'>";
	//print "<tr><td>On value change of field</td><td>When the value is updated to</td><td>perform action</td><td>Attach report to mail (mail only)</td><td>Mail template</td><td>Attach to entity/customer</td></tr>";
	if ($_REQUEST['fetch']) {
		print "<input type='hidden' name='fetched' value='" . $_REQUEST['fetch'] . "'>";
	}
	
	print "<tr><td>On value change of field</td><td><b>$lang[priority]<input type='hidden' name='onchange' value='priority'></b></td></tr>";
	print "<tr><td>When the value is updated to</td><td><select name='to_value'>";
	print "<option value='@SE@'>[something else]</option>";
	$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]priorityvars ORDER BY varname";
		$result= mcq($sql,$db);
		while($options = mysql_fetch_array($result)) {
			if ($options['varname'] == $fetched_trigger['to_value']) {
				$ins = "SELECTED";
			} else {
				unset($ins);
			}
			print "<option $ins style='background:" . $options[color] . "' value='$options[varname]' $a>$options[varname]</option>";
		}

	print "</select>";
	print "</td></tr><tr><td>Perform action</td><td><select name='action' onchange='CheckMailTrigger()';>";
	foreach ($actionlist AS $action) {
		print $action;
	}
	print "</select></td></tr>";
	print "<tr><td>Attach report to mail (mail actions only)</td><td>";
	print $report_select;
	print "</td></tr>";
	print "<tr><td>Mail template (mail actions only)</td><td><select name='template_fileid'>";
	print "<option value=''>[default]</option>";
			foreach ($templatelist AS $template) {
				print $template;
			}
	print "</select></td></tr>";
	if ($fetched_trigger['attach'] == "y") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	print "<tr><td>Attach mail to entity/customer</td><td><select name='attach'><option value='n'>No</option><option $ins value='y'>Yes</option></select></td></tr>";
	print "<tr><td>Comment</td><td><textarea cols='50' rows='3' name='trigger_comment'>" . $fetched_trigger['comment'] . "</textarea></td></tr>";
	print "<tr><td colspan=2><input type='hidden' name='add' value=''><input type='submit' value='" . $lang['go'] . "'></td></tr>";
	print "</form>";
	print "</table></fieldset></td></tr>";
	print "<td><td colspan=10>&nbsp;</td></tr>";


}
// ========================================================================================================================================================
//print "<tr><td colspan=5><br><b><u>" . $lang['owner'] . "/" . $lang['assignee'] . "</u></b></td></tr>";
if ($_REQUEST['add'] == "ownerassignee" || $_REQUEST['add'] == "assignee" || $_REQUEST['add'] == "owner") {
	print "<tr><td colspan=10><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Add/edit an " . strtolower($lang['owner']) . "/" . strtolower($lang['assignee']) . " trigger</legend><table class='crm4'>";
	print "<FORM METHOD='POST' NAME='TriggerAddForm'>";
	if ($_REQUEST['fetch']) {
		print "<input type='hidden' name='fetched' value='" . $_REQUEST['fetch'] . "'>";
	}

	//print "<tr><td>On value change of field</td><td>When the value is updated to</td><td>perform action</td><td>Attach report to mail (mail only)</td><td>Mail template</td><td>Attach to entity/customer</td></tr>";
	print "<tr><td>On value change of field</td><td>";
	print "<select name='onchange'>";
	print "<option value='owner'>" . $lang['owner'] . "</option>";
	print "<option value='assignee'>" . $lang['assignee'] . "</option>";

	print "</select>";
	print "</td></tr><tr><td>When the value is updated to</td><td><select name='to_value'>";
	print "<option value='@SE@'>[something else]</option>";
	$sql = "SELECT FULLNAME,id FROM $GLOBALS[TBL_PREFIX]loginusers ORDER BY FULLNAME";
		$result= mcq($sql,$db);
		while($options = mysql_fetch_array($result)) {
			print "<option style='background:" . $options[color] . "' value='$options[id]' $a>" . str_replace("@@@:","",$options[FULLNAME]) . "</option>";
		}
	print "</select>";
	print "</td></tr><tr><td>Perform action</td><td><select name='action' onchange='CheckMailTrigger()';>";
	foreach ($actionlist AS $action) {
		print $action;
	}

	print "</select></td></tr>";
	print "<tr><td>Attach report to mail (mail actions only)</td><td>";
	print $report_select;
	print "</td></tr>";
	print "<tr><td>Mail template (mail actions only</td><td><select name='template_fileid'>";
	print "<option value=''>[default]</option>";
			foreach ($templatelist AS $template) {
				print $template;
			}
	print "</select></td></tr>";
	if ($fetched_trigger['attach'] == "y") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	print "<tr><td>Attach mail to entity/customer (mail actions only)</td><td><select name='attach'><option value='n'>No</option><option $ins value='y'>Yes</option></select></td></tr>";
	print "<tr><td>Comment</td><td><textarea cols='50' rows='3' name='trigger_comment'>" . $fetched_trigger['comment'] . "</textarea></td></tr>";
	print "<tr><td colspan=2><input type='hidden' name='add' value=''><input type='submit' value='" . $lang['go'] . "'></td></tr>";

	print "</form>";
	print "</table></fieldset></td></tr>";
	print "<td><td colspan=10>&nbsp;</td></tr>";
}
// ========================================================================================================================================================

if ($_REQUEST['add'] == "customer") {
	print "<tr><td colspan=10><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Add/edit a " . strtolower($lang['customer']) . " trigger</legend><table class='crm4'>";
	print "<FORM METHOD='POST' NAME='TriggerAddForm'>";
	if ($_REQUEST['fetch']) {
		print "<input type='hidden' name='fetched' value='" . $_REQUEST['fetch'] . "'>";
	}

	//print "<tr><td>On value change of field</td><td>When the value is updated to</td><td>perform action</td><td>Attach report to mail (mail only)</td><td>Mail template</td><td>Attach to entity/customer</td></tr>";
	print "<tr><td>On value change of field</td><td>";

	print "<b>" . $lang['customer'] . "</b>";
	print "</td></tr>";

	$tot_opt = 0;

	print "<tr><td>When the value is updated to</td><td><input type='hidden' name='onchange' value='customer'><select name='to_value'>";
	print "<option value='@SE@'>[something else]</option>";
	$sql = "SELECT id,custname FROM $GLOBALS[TBL_PREFIX]customer ORDER BY custname";
		$result= mcq($sql,$db);
		while($options = mysql_fetch_array($result)) {
			print "<option value='$options[id]' $a>$options[custname]</option>";
			$tot_opt++;
		}
	print "</select>";
	print "</td></tr>";
	print "<tr><td>Perform action</td><td><select name='action' onchange='CheckMailTrigger()';>";
	foreach ($actionlist AS $action) {
		print $action;
	}
	print "</select></td></tr>";
	print "<tr><td>Attach report to mail (mail actions only)</td><td>";
	print $report_select;
	print "</td></tr>";
	print "<tr><td>Mail template (mail actions only)</td><td><select name='template_fileid'>";
	print "<option value=''>[default]</option>";
			foreach ($templatelist AS $template) {
				print $template;
			}
	print "</select></td></tr>";
	if ($fetched_trigger['attach'] == "y") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	print "<tr><td>Attach mail to entity/customer (mail actions only)</td><td><select name='attach'><option value='n'>No</option><option $ins value='y'>Yes</option></select></td></tr>";
	print "<tr><td>Comment</td><td><textarea cols='50' rows='3' name='trigger_comment'>" . $fetched_trigger['comment'] . "</textarea></td></tr>";
	print "<tr><td colspan=2><input type='hidden' name='add' value=''><input type='submit' value='" . $lang['go'] . "'></td></tr>";
	print "</form>";
	print "</table></fieldset></td></tr>";
	print "<td><td colspan=10>&nbsp;</td></tr>";
}
// ========================================================================================================================================================

if ($_REQUEST['add'] == "extrafield" || strstr($_REQUEST['add'],"EFID")) {
	print "<tr><td colspan=10><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Add/edit an extra field trigger</legend><table class='crm4'>";
	print "<FORM METHOD='GET' NAME='TriggerAddForm'><input type='hidden' name='add' value='extrafield'";
	if ($_REQUEST['fetch']) {
		print "<input type='hidden' name='fetched' value='" . $_REQUEST['fetch'] . "'>";
	}

	//print "<tr><td>On value change of field</td><td>When the value is updated to</td><td>perform action</td><td>Attach report to mail (mail only)</td><td>Mail template</td><td>Attach to entity/customer</td></tr>";
	

	if (!$_REQUEST['ExtraField'] && !$_REQUEST['fetch']) {
		print "<tr><td>On value change of field</td><td>";
		$efl = GetExtraFields();
		print "<select name='ExtraField'>";
		foreach ($efl AS $ef) {
			if ($ef['fieldtype']=="Button") {
				//	print "<option value='" . $ef['id'] . "'>[BUTTON] " . $ef['name'] . "</option>";
			} else {
				print "<option value='" . $ef['id'] . "'>" . $ef['name'] . "</option>";
			}
		}
		print "</select>";
		print "</td></tr>";
		print "<tr><td>When the value is set to</td><td>[select field first and click 'go']</td></tr>";
		print "<tr><td>Perform action</td><td>[select field first and click 'go']</td></tr>";
		print "<tr><td>Attach report to mail (mail actions only)</td><td>[select field first and click 'go']</td></tr>";
		print "<tr><td>Mail template (mail actions only)</td><td>[select field first and click 'go']</td></tr>";
		print "<tr><td>Attach mail to entity/customer (mail actions only)</td><td>[select field first and click 'go']</td></tr>";
		print "<tr><td>Comment</td><td>[select field first and click 'go']</td></tr>";
	} else {
		print "<tr><td>On value change of field</td><td><select DISABLED name='onchange'>";
		if ($_REQUEST['fetch']) {
			$ctrigger = db_GetRow("SELECT * FROM " . $GLOBALS['TBL_PREFIX'] . "triggers WHERE tid=" . $_REQUEST['fetch']);
			$_REQUEST['ExtraField'] = str_replace("EFID","",$ctrigger['onchange']);
		}
		$list = GetExtraFields();
		foreach ($list AS $field) {
			if ($field['id'] == $_REQUEST['ExtraField']) {
				break;
			}
		}

		print "<option>" . $_REQUEST['ExtraField'] . ":" . $field['name'] . "</option></select></td></tr>";
		print "<tr><td>When the value is set to</td><td><input type='hidden' name='onchange' value='EFID" . $_REQUEST['ExtraField'] . "'>";
		print "<select name='to_value'>";
		print "<option value='@SE@'>[something else]</option>";
		
		if ($field['fieldtype'] == "drop-down") {
			$tmp = unserialize($field['options']);			
			foreach ($tmp AS $option) {
				if ($ctrigger['to_value'] == $option) {
					$ins = "SELECTED";
				} else {
					unset($ins);
				}
				print "<option " . $ins . " value='" . $option ."'>" . $option . "</option>";
				$tot_opt++;
			}
		} elseif ($field['fieldtype'] == "User-list of all CRM-CTT users") {
			$res = mcq("SELECT * FROM " . $GLOBALS['TBL_PREFIX'] . "loginusers WHERE name NOT LIKE '%deleted_user%' ORDER BY FULLNAME",$db);
			while ($row = mysql_fetch_array($res)) {
				if ($ctrigger['to_value'] == $row['id']) {
					$ins = "SELECTED";
				} else {
					unset($ins);
				}
				print "<option " . $ins . " value='" . $row['id'] . "'>" . GetUserName($row['id']) . "</option>";
			}
		} elseif ($field['fieldtype'] == "User-list of limited CRM-CTT users") {
			$res = mcq("SELECT * FROM " . $GLOBALS['TBL_PREFIX'] . "loginusers WHERE name NOT LIKE '%deleted_user%' AND FULLNAME LIKE '%@@@%' ORDER BY FULLNAME",$db);
			while ($row = mysql_fetch_array($res)) {
				if ($ctrigger['to_value'] == $row['id']) {
					$ins = "SELECTED";
				} else {
					unset($ins);
				}
				print "<option " . $ins . " value='" . $row['id'] . "'>" . GetUserName($row['id']) . "</option>";
			}
		} elseif ($field['fieldtype'] == "User-list of administrative CRM-CTT users") {
			$res = mcq("SELECT * FROM " . $GLOBALS['TBL_PREFIX'] . "loginusers WHERE name NOT LIKE '%deleted_user%' AND administrator='yes' ORDER BY FULLNAME",$db);
			while ($row = mysql_fetch_array($res)) {
				if ($ctrigger['to_value'] == $row['id']) {
					$ins = "SELECTED";
				} else {
					unset($ins);
				}
				print "<option " . $ins . " value='" . $row['id'] . "'>" . GetUserName($row['id']) . "</option>";
			}
		}

		print "</select>";
		print "</td></tr><tr><td>Perform action</td><td><select name='action' onchange='CheckMailTrigger()';>";
		foreach ($actionlist AS $action) {
			/*
			$action_arr = split("value='",$action);
			$action_arr2 = split("'>",$action_arr[1]);
			$val = $action_arr2[0];
			if ($val == $ctrigger['action']) {
				$action = str_replace("<option","<option SELECTED",$action);
			}
			*/
			print $action;
		}
		print "</select></td></tr>";
		print "<tr><td>Attach report to mail (mail actions only)</td><td>";
		print $report_select;
		print "</td></tr>";
		print "<tr><td>Mail template (mail actions only)</td><td><select name='template_fileid'>";
		print "<option value=''>[default]</option>";
			foreach ($templatelist AS $template) {
				print $template;
			}
		print "</select></td></tr>";
	
	if ($fetched_trigger['attach'] == "y") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	print "<tr><td>Attach mail to entity/customer (mail actions only)</td><td><select name='attach'><option value='n'>No</option><option $ins value='y'>Yes</option></select>&nbsp;&nbsp;&nbsp;";
	print "<tr><td>Comment</td><td><textarea cols='50' rows='3' name='trigger_comment'>" . $fetched_trigger['comment'] . "</textarea></td></tr>";
	}
	
	if ($_REQUEST['ExtraField']) {
		print "<input type='hidden' name='add' value=''>";
	}

	print "<tr><td colspan=2><input type='submit' value='" . $lang['go'] . "'></td></tr>";
	print "</form>";
	print "</table></fieldset></td></tr>";
	print "<td><td colspan=10>&nbsp;</td></tr>";
}
// ========================================================================================================================================================

if ($_REQUEST['add'] == "miscellaneous" || $_REQUEST['add'] == "entity_add" || $_REQUEST['add'] == "entity_change" || $_REQUEST['add'] == "entity_change_select_assignee" || $_REQUEST['add'] == "entity_change_select_owner" || $_REQUEST['add'] == "limited_add" || $_REQUEST['add'] == "limited_update"  || $_REQUEST['add'] == "duedate_reached" ) {
	print "<tr><td colspan=10><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Add/edit a miscellaneous trigger</legend><table class='crm4'>";
	print "<FORM METHOD='POST' NAME='TriggerAddForm'>";
	if ($_REQUEST['fetch']) {
		print "<input type='hidden' name='fetched' value='" . $_REQUEST['fetch'] . "'>";
	}
	//print "<tr><td>On event occurring</td><td>perform action</td><td>Attach report to mail (mail only)</td><td>Mail template</td><td>Attach to entity/customer</td></tr>";
	print "<tr><td>On event occurring</td><td>";

	print "<select name='onchange'>";
	print "<option value='limited_add'>Limited user adds an entity</option>";
	print "<option value='limited_update'>Limited user updates an entity</option>";
	print "<option value='duedate_reached'>Entity reaches due-date</option>";
	print "<option value='entity_add'>Entity add</option>";
	print "<option value='entity_change'>Entity change</option>";
	print "<option value='entity_change_select_owner'>Entity change for owner (email update selectbox, action ignored)</option>";
	print "<option value='entity_change_select_assignee'>Entity change for assignee (email update selectbox, action ignored)</option>";

	$arr = GetButtons();


	foreach ($arr AS $button) {
		print "<option value='ButtonPress" . $button['id'] . "'>Button [" . $button['name'] . "] is pressed</option>";
	}

	print "</select></td></tr>";

	$tot_opt = 0;

	print "<td>Perform action</td><td><input type='hidden' name='to_value' value='Miscellaneous trigger'><select name='action'>";
	foreach ($actionlist AS $action) {
		print $action;
	}
	print "</select></td></tr>";
	print "<tr><td>Attach report to mail (mail actions only)</td><td>";
	print $report_select;
	print "</td></tr>";
	print "<tr><td>Mail template (mail actions only)</td><td><select name='template_fileid'>";
	print "<option value=''>[default]</option>";
			foreach ($templatelist AS $template) {
				print $template;
			}
	print "</select></td></tr>";
	if ($fetched_trigger['attach'] == "y") {
		$ins = "SELECTED";
	} else {
		unset($ins);
	}
	print "<tr><td>Attach mail to entity/customer (mail actions only)</td><td><select name='attach'><option value='n'>No</option><option $ins value='y'>Yes</option></select></td></tr>";
	print "<tr><td>Comment</td><td><textarea cols='50' rows='3' name='trigger_comment'>" . $fetched_trigger['comment'] . "</textarea></td></tr>";
	print "<tr><td colspan=2><input type='hidden' name='add' value=''><input type='submit' value='" . $lang['go'] . "'></td></tr>";
	print "</form>";
	print "</table></fieldset></td></tr>";
	print "<td><td colspan=10>&nbsp;</td></tr>";
}
// ========================================================================================================================================================
if ($_REQUEST['add'] == "admin" || $_REQUEST['add'] == "log_error" || $_REQUEST['add'] == "log_warning") {
	print "<tr><td colspan=10><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Administrative triggers</legend><table class='crm4'>";


	print "<FORM METHOD='POST' NAME='TriggerAddForm'>";
	if ($_REQUEST['fetch']) {
		print "<input type='hidden' name='fetched' value='" . $_REQUEST['fetch'] . "'>";
	}

//	print "<tr><td>On event occurring</td></tr><tr><td>When the value is updated to</td><td>perform action</td><td>Mail template</td><td>Mail template</td><td>&nbsp;</td></tr>";

	print "<tr><td>On event occurring</td><td>";
	print "<input type='hidden' name='onchange' value='customer'>";
	print "<select name='onchange'>";
	print "<option value='log_warning'>Warning issued in log</option>";
	print "<option value='log_error'>Error issued in log</option>";
	print "</select><input type='hidden' name='to_value' value='Administrative trigger'></td></td></tr>";
	print "<tr><td>Perform action</td><td><select name='action' onchange='CheckMailTrigger()';>";
	foreach ($actionlist AS $action) {
		if (!strstr($action,"'mail owner'") && !strstr($action,"'mail assignee'") && !strstr($action,"'mail customer'")) {
			print $action;
		}
	}
	print "</select></td></tr>";
	print "<tr><td>Mail template</td><td><select DISABLED name='template_fileid'>";
	print "<option value='0'>Notification (no template)</option>";
	print "</select></td></tr>";
	print "<tr><td>Comment</td><td><textarea cols='50' rows='3' name='trigger_comment'>" . $fetched_trigger['comment'] . "</textarea></td></tr>";
	print "<tr><td colspan=2><input type='hidden' name='add' value=''><input type='submit' value='" . $lang['go'] . "'></td></tr>";
	print "</form>";
	print "</table></fieldset></td></tr>";
	print "<td><td colspan=10>&nbsp;</td></tr>";
}
// ========================================================================================================================================================

//print "</table>";
if (!$_REQUEST['add']) {
	$tp .= "<tr><td colspan=10><table border=0 width=100%><tr><td colspan=10><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Current event triggers:</legend>";
	$tp .= "<table cellspacing='0' cellpadding='4' border=1 bordercolor='#F0F0F0' width=100%>";

	$tp .= "<tr><td><b>On event or value change of field</b></td><td><b>when the value is updated to</b></td><td><b>perform action</b></td><td><b>Mail template</b></td><td><b>Comment</b></td><td><b>Delete</b></td></tr>";

		$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]triggers ORDER BY comment";
		$result = mcq($sql,$db);
		while ($row = mysql_fetch_array($result)) {

		
			if (strstr($row['onchange'],"EFID")) {
					$list = GetExtraFields();
					$num = str_replace("EFID","",$row['onchange']);
					foreach ($list AS $field) {
						if ($field['id'] == $num) {
							break;
						}
					}

				$tp .= "<tr onmouseover=\"javascript:style.background='#E9E9E9';\" onmouseout=\"style.background='#FFFFFF';\" onclick=\"document.location='trigger.php?add=" . $row['onchange'] . "&fetch=" . $row['tid'] . "';\"><td>" . $field['name'] . "</td>";
				//$tp .= "<tr onmouseover=\"javascript:style.background='#E9E9E9';\" onmouseout=\"style.background='#FFFFFF';\" OnClick='document.location=\"trigger.php?edit=" . $row['tid'] . "&add=extrafield\";' style='cursor:pointer'><td>" . $field['name'] . "</td>";
			} else {
				//$tp .= "<tr onmouseover=\"javascript:style.background='#E9E9E9';\" onmouseout=\"style.background='#FFFFFF';\" OnClick='document.location=\"trigger.php?edit=" . $row['tid'] . "&add=" . $row['onchange'] . "\";' style='cursor:pointer'><td>" . $row['onchange'] . "</td>";
				$tp .= "<tr style='cursor: hand' onmouseover=\"javascript:style.background='#E9E9E9';\" onmouseout=\"style.background='#FFFFFF';\" onclick=\"document.location='trigger.php?add=" . $row['onchange'] . "&fetch=" . $row['tid'] . "';\"><td>" . $row['onchange'] . "</td>";
			}
			if ($row['to_value']=="@SE@") {
				$tp .= "<td>[something else]";
			} elseif ($row['onchange']=="owner" || $row['onchange']=="assignee") {
				$tp .= "<td>" . GetUserName($row['to_value']);
			} elseif ($row['onchange']=="customer") {
				$tp .= "<td>" . GetCustomerName($row['to_value']);
			} elseif ($row['onchange']=="status") {
				$tp .= "<td style=\"background='" . GetStatusColor($row['to_value']) . "'\">" . $row['to_value'];
			} elseif ($row['onchange']=="priority") {
				$tp .= "<td style=\"background='" . GetPriorityColor($row['to_value']) . "'\">" . $row['to_value'];
			} elseif (strstr($field['fieldtype'], "User-list of")) {
				$tp .= "<td>" . GetUserName($row['to_value']) . "</td>";
			} else {
				$tp .= "<td>" . $row['to_value'] . "</td>";
			}
			
			$tp .= "</td><td>";
			 if (strstr($row['action'],"mail user @")) {
				$user_ar = split("@",$row['action']);
				$user = $user_ar[1];
				$tp .= "mail user " . GetUserName($user);
			} elseif (strstr($row['action'],"mail cust @")) {
				$cust_ar = split("@",$row['action']);
				$cust = $cust_ar[1];
				$tp .= "mail " . strtolower($lang['customer']) . " " . GetCustomerName($cust);
			} else {
				$tp .= "" . $row['action'] . "";
			}
			
			$tp .= "<td>";
			if (stristr($row['action'],"mail")) {
				if ($row['template_fileid']) {
					$tp .= GetFileName($row['template_fileid']);
				} else {
					if ($row['action']=="mail owner" || $row['action']=="mail assignee") {
						$tp .= "BODY_ENTITY_EDIT";
					} elseif ($row['to_value'] == "Administrative trigger") {
						$tp .= "Notification (no template)";
					} elseif ($row['action']=="mail admin") {
						$tp .= "BODY_TEMPLATE_CUSTOMER";
					} elseif ($row['action']=="status") {
						$tp .= "BODY_ENTITY_EDIT";
					} elseif (strstr($row['action'],"mail user @")) {
						$tp .= "BODY_ENTITY_EDIT";
					} elseif (strstr($row['action'],"mail cust @") || ($row['action']=="mail customer")) {
						$tp .= "BODY_TEMPLATE_CUSTOMER";
					} else {
						$tp .= "BODY_ENTITY_EDIT";
					}
				}
			} else {
				$tp .= "n/a";
			}


			$tp .= "</td>";
			/*
			$tp .= "<td>";
			if (stristr($row['action'],"mail")) {
				if ($row['report_fileid']==0) {
						$tp .= "Don't attach anything";
					} elseif ($row['report_fileid']==999999999999999) {
						$tp .= "Std. PDF-report";
					} else {
						$tp .= GetFileName($row['report_fileid']);
					}
			} else {
				$tp .= "n/a";
			}
			$tp .= "</td>";
			*/
			$tp .= "<td>";
			$tp .= ereg_replace("\n","<br>",htmlspecialchars($row['comment']));
			$tp .= "</td>";
			$tp .= "<td><a href='trigger.php?del_trigger=" . $row['tid'] ."'><img style='border:0' src='delete.gif'></a></td></tr>";
			$c++;
		}

		if ($c<1) {
			$tp .= "<tr><td colspan=10>No triggers defined</td></tr>";
			$noprint = true;
		}
		$tp .= "</table></fieldset></td></tr></table></td></tr>";
	//}

	//if (!$noprint) {
		print $tp;
	//}
}
print "<tr><td><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password&joepie=1' style='cursor:pointer'>Back to main administration page</a><BR><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?templates=1' style='cursor:pointer'>Edit RTF and HTML templates</a>";

print "</td></tr></table>";

$tot_opt+=6;
$tot_act = sizeof($actionlist);
$tot_opt2 = pow($tot_act,$tot_opt);
//print "pow($tot_act,$tot_opt)";
//print "<br>In your installation, you can add $tot_act^$tot_opt (" . number_format($tot_opt2) . ") valid event triggers, all behaving different!<br>";

print "</fieldset>";

EndHTML();
?>
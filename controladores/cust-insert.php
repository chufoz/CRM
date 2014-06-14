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
extract($_REQUEST);

$nonavbar=1;
$custinsertmode=1;

include("header.inc.php");



// This makes sure that the sort is working (cust-insert uses "$list" whilst ShowEntitiesOpen() is using "$ShowEntitiesOpen")
if ($_REQUEST['ShowEntitiesOpen']) {
	$list = 1;
}

if ($lan) {  // personal language setting changed

				$repository_nr = $_COOKIE[repository];

				if ($repository_nr=="" || $repository_nr==0) {
					$repository_nr=0;
				}

				if ($repository_nr==sizeof($pass) || $repository_nr>sizeof($pass)) {
					$repository_nr=0;
				}

				$db = mysql_connect($host[$repository_nr], $user[$repository_nr], $pass[$repository_nr]);
				mysql_select_db($database[$repository_nr],$db);
				$sql= "UPDATE $GLOBALS[TBL_PREFIX]loginusers SET exptime='$lan' WHERE name='$name'";
				mcq($sql,$db);
				include("language.php");
		}

if (!$tab) $tab = 22;
printheader_cust(1,$tab);

?>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
<!--
function AlertUser(whichLayer) {
	//document.bgColor = '#FFE6D1';
	if (document.getElementById) {
		// this is the way the standards work
		document.getElementById(whichLayer).style.display = "block";
	}
	else if (document.all) {
		// this is the way old msie versions work
		document.all[whichlayer].style.display = "block";
	}
	else if (document.layers) {
		// this is the way nn4 works
		document.layers[whichLayer].display = "block";
	}
	document.EditEntity.changed.value = '1';

}
function leave() {
	if (document.EditEntity.changed.value == '1') {
		if (confirm('<? echo $lang[save];?>?')) {
				document.EditEntity.submit();
		}

	}
	//window.open('lwin_exit.html','','toolbar=no,menubar=no,location=no,height=150,width=250');
}
//-->
</SCRIPT>
<?

//print "<tr><td colspan=12><hr></td></tr>";
if (strtoupper($GLOBALS['EnableCustInsert'])<>"YES") {	
	print "<td><font face='MS Shell Dlg' size=2><img src='error.gif'>&nbsp;&nbsp;This section has been disabled by the system administrator.<br><br><br><i></font><font face='MS Shell Dlg' size='-2'>&nbsp;Message::SettingType EnableCustInsert::not set</i></font> (see log for more information) " . $GLOBALS['EnableCustInsert'] . "</td></tr></table>";
	uselogger("Customer insert user denied - interface disabled. To enable, Change System Values -> EnableCustInsert -> Yes","");
	EndHTML();
	exit;
}

//print "<pre>";
//print_r($_REQUEST);

if ($_REQUEST['action']=='edit') {

	if (!$addcontent=="" || ($_FILES['userfile']['tmp_name'] && $_FILES['userfile']['name'] && $_FILES['userfile']['size'])) {
	    $eid = $e;

		// First check if the status is CHANGED to closed
		$date = date('d-m-Y H:i') . "h";
		
		$sql = "SELECT content FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='$eid'";

		$result= mcq($sql,$db);
		$maxU1 = mysql_fetch_array($result);
		$oldcontent = addslashes($maxU1[0]);
		
		$addcontent = "Added at $date by $name:\n" . $addcontent ."\nEnd edit by $name.\n\n". $oldcontent;

		// Update SQL
		$sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET content='$addcontent' WHERE eid='$eid'";
		journal($e,"[limited mode] Entity updated (contents only)");
		uselogger("[limited mode] Edit entity: $eid","");
		//print $sql;
		mcq($sql,$db);
		ExpireFormCache($eid);

		if ($_FILES['userfile']['tmp_name'] && $_FILES['userfile']['name'] && $_FILES['userfile']['size']) {
				
				//print "EID is $eid ============== A FILE WAS ATTACHED";
					
					
			// Read contents of uploaded file into variable
						$fp=fopen($_FILES['userfile']['tmp_name'],"rb");
						$filecontent=fread($fp,filesize($_FILES['userfile']['tmp_name']));
						fclose($fp);
						$filecontenttomail = $filecontent;
						$filenametomail = $_FILES['userfile']['name'];
						//$filecontent = addslashes($filecontent);
						$attachment = "1";

			// insert identifier & content into $GLOBALS[TBL_PREFIX]binfiles:

						//$sql="INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,filetype,username) VALUES('$eid','$filecontent','" . $_FILES['userfile']['name'] . "','" . $_FILES['userfile']['size'] . "','" . $_FILES['userfile']['type'] . "','" . $name . "')";
						//mcq($sql,$db);
						$statusmsg="File " . $_FILES['userfile']['name'] . $lang[wasadded];
						//journal($eid,"[limited mode] File $user_name added");
						//uselogger("File $user_name attached to entity $eid","");

						//function AttachFile($koppelid,$filename,$content,$type="entity",$filetype="Unknown") {
					//function AttachFile($koppelid,$filename,$content,$type,$filetype="Unknown",$subj="") {
						$x = AttachFile($eid,$_FILES['userfile']['name'],$filecontent,"entity",$_FILES['userfile']['type']);
						//$x = AttachFile($eid,$_FILES['userfile']['name'],$filecontent,"entity","n/a");

				} else {
					//Print "Ahum, niet dus" . $_FILES['userfile']['tmp_name'] . $_FILES['userfile']['name'] . $_FILES['userfile']['size'];
				}

		ProcessTriggers("limited_update",$eid,"Miscellaneous trigger");
	}
	qlog("Navigating away from this page (1)");
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	document.location = 'cust-insert.php?list=1';
	//-->
	</SCRIPT>
	<?
	exit;
}



// Determine which [customer] is coupled to the user

$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
if ($debug) { print "\nSQL: $sql\n"; }
$result= mcq($sql,$db);
$result= mysql_fetch_array($result);

$result[FULLNAME] = split(":",$result[FULLNAME]);
$customerid = trim($result[FULLNAME][1]);
$mailto = $result[EMAIL];
$CLLEVEL = $result[CLLEVEL];
$loguser = $result[id];

$sql= "SELECT custname FROM $GLOBALS[TBL_PREFIX]customer WHERE id='$customerid'";
if ($debug) { print "\nSQL: $sql\n"; }
$result= mcq($sql,$db);
if (!$result= mysql_fetch_array($result)) {
		print "<td><font face='MS Shell Dlg' size=2><img src='error.gif'>&nbsp;&nbsp;This account is disabled.<br><br><br><i></font><font face='MS Shell Dlg' size='-2'>&nbsp;Message::CoupledID $name::not coupled (see log for more information)</i></font></td></tr></table>";
		uselogger("Customer insert user denied - acccount not coupled. Please couple this user to a $lang[customer] using the 'Manage Accounts' function","");
		EndHTML();
		exit;
} else {
	$customer = $result[custname];
}
if ($_REQUEST['e']=="") $_REQUEST['e']="_new_";

if ($e && !$action) {
	$a = GetClearanceLevel($loguser);
	if ($a<>"ro+") {
		$sql = "SELECT assignee FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='$e'";
		$result= mcq($sql,$db);
		$resarr=mysql_fetch_array($result);
		if ($resarr['assignee']<>$loguser || $a=="ro") {
			print "<font face='MS Shell Dlg' size=2><img src='error.gif'>&nbsp;&nbsp;<b>Access denied.<BR>";
			uselogger("ILLEGAL ATTEMPT BY $a USER $loguser TO ACCESS ENTITY $e","");
			exit;
		} else {
			// nothing for now
		}

	}
	$eid = $e;
	if (CheckEntityAccess($e)=="readonly") {
		//editform($e,$loguser);
			print "<table with=75%><tr><td>";
				if (is_array($GLOBALS['ADDFORMLIST']) && $eid == "_new_" && ($GLOBALS['FormFinity']=="Yes")) { // This means that the user may choose from different forms

						$to_tabs = array();
						foreach ($GLOBALS['ADDFORMLIST'] AS $form) {
							if ($form <> "default") {
								array_push($to_tabs, $form);
								qlog("Added " . GetFileSubject($form) . " TO TAB LIST!");
								$tabbs[$form] = array("edit.php?e=_new_&ftu=" . $form . "" => addslashes(GetFileSubject($form)), "comment" => 	"");
							} else {
								array_push($to_tabs, $form);
								qlog("Added default form TO TAB LIST!");
								$tabbs[$form] = array("edit.php?e=_new_&ftu=default" => $lang['add'], "comment" => "");
							}
							
						}
						
						if (is_numeric($_REQUEST['ftu'])) {
							$navid = $_REQUEST['ftu'];
						} else {
							$navid = $GLOBALS['ADDFORMLIST'][0];
						}
						
						if (sizeof($GLOBALS['ADDFORMLIST'])>1 && $e=="_new_") {
							print "</table></td></tr></table>";
							InterTabs($to_tabs, $tabbs, $navid);
							print "<table><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";
						}
						
						if (is_numeric($_REQUEST['ftu'])) {
							print CustomEditForm($_REQUEST['ftu'],$e);
						} else {
							qlog("FormType: using first in row (ftu <> numeric)");
							//MainEditForm($e);	
							if (is_numeric($GLOBALS['ADDFORMLIST'][0])) {
								print CustomEditForm($GLOBALS['ADDFORMLIST'][0],$e);
							} else {
								PrintEditForm($e);
							}
						}

				} elseif ($e <> "_new_" && is_numeric(GetEntityFormID($e)) && GetEntityFormID($eid) <> 0 && ($GLOBALS['FormFinity']=="Yes")) {
					qlog("FormType: edit - Using form template from entity");
						if (GetFileSubject(GetEntityFormID($e)) <> "") {
							print CustomEditForm(GetEntityFormID($e),$e);
						} else {
							print "<img src='error.gif'> Form " . GetEntityFormID($e) . " not found. Defaulting.";
							log_msg("ERROR: Entity " . $e . " wants to use form " . GetEntityFormID($e) . " - this form is not available. Falling back to default form.");
							PrintEditForm($e);
						}
				
				} elseif ($e=="_new_" && is_numeric($GLOBALS['ENTITY_LIMITED_ADD_FORM']) && $cl == "ro") {
					qlog("FormType: add [limited] - Using form template " . $GLOBALS['ENTITY_LIMITED_ADD_FORM']);
						print CustomEditForm($GLOBALS['ENTITY_LIMITED_ADD_FORM'],$e);

				} elseif ($e=="_new_" && is_numeric($GLOBALS['ENTITY_ADD_FORM']) && ($cl=="rw" || $cl=="administrator" || $cl == "full-access-own-entities")) {
					qlog("FormType: add [full-access] - Using form template " . $GLOBALS['ENTITY_ADD_FORM']);
						print CustomEditForm($GLOBALS['ENTITY_ADD_FORM'],$e);

				} elseif (is_numeric($e) && is_numeric($GLOBALS['ENTITY_LIMITED_EDIT_FORM']) && $cl == "ro") {
					qlog("FormType: edit [limited] - Using form template " . $GLOBALS['ENTITY_LIMITED_EDIT_FORM']);
						print CustomEditForm($GLOBALS['ENTITY_LIMITED_EDIT_FORM'],$e);

				} elseif (is_numeric($e) && is_numeric($GLOBALS['ENTITY_EDIT_FORM']) && ($cl=="rw" || $cl=="administrator" || $cl == "full-access-own-entities")) {
					qlog("FormType: edit [full-access] - Using form template " . $GLOBALS['ENTITY_EDIT_FORM']);
						print CustomEditForm($GLOBALS['ENTITY_EDIT_FORM'],$e);
				} else {
					qlog("FormType: using default entity form for entity $e");
					MainEditForm($e);
				}
		print "</td></tr></table>";
		EndHTML();
		exit;
	} else {
		print "<table><tr><td>";
		printAD("CheckEntityAccess returned 'denied' - see log for details");
		print "</td></tr></table>";
		EndHTML();
		exit;
	}
}


if ($list) {
	$ONLY_SHOW_CUSTOMER=$customerid;
	if ($CLLEVEL=="ro+") { // this user may see entity contents
		$EnableEditButton = true;
	} else {
		$EnableEditButton = false;
	}
	$ComingFromCustInsert = "1";
	$ForceGlobalLayout = true;
	$CLLEVEL="ro+";
	print "<table><tr><td>&nbsp;&nbsp;</td><td>";
	ShowEntitiesOpen();
	print "</td></tr></table>";
	EndHTML();
	exit;
}

if ($action=='add') {
	// Insert a new entity into SQL
	//

	$cdate = date('Y-m-d');
	$epoch = date('U');	
//			print "Assig - $assignee[0], Owner - $owner[0]<br>";
//	print "<pre>";
//	print_r($GLOBALS);

	if (strtoupper($GLOBALS['AUTOASSIGNINCOMINGENTITIES'])=="YES") {
		// This new entity must be auto-assigned to the customer owner
		qlog("Auto-assigning this entity to the customer owner");
		$owner = GetCustomerOwner($customerid);
		$assignee = GetCustomerOwner($customerid);
		qlog("Set o:$owner a:$assignee");
		if ($owner == "" || $owner == 0) {
			$owner = "2147483647";
		}
		if ($assignee == "" || $assignee == 0) {
			$assignee = "2147483647";
		}

	} else {
		$owner = "2147483647";
		$assignee = "2147483647";
	}

	if ($prio == "") {
		$prio = $prioty;
	}

	//$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]entity(priority,category,content,owner,assignee,CRMcustomer,status,deleted,duedate,sqldate,obsolete,cdate,waiting,openepoch,formid) VALUES('$prio', '$cat', '$content', '$owner', '$assignee', '$customerid','$status','n','$duedate','$sqldate','$obsolete','$cdate','$waiting','$epoch','$formid')";
	$sql = "SELECT FULLNAME FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id='" . $GLOBALS['USERID'] . "' LIMIT 1";
	$out = mcq($sql,$db);
	$row2 = mysql_fetch_array($out);
	$cust2 = $row2[0];
	$cust_coupled = split(":",$cust2);
	$customerid = $cust_coupled[1];

	

	$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]entity(CRMcustomer,category,owner,assignee,content,status,priority,duedate,duetime,readonly,private,openepoch,cdate,sqldate,formid) VALUES (" . $customerid . ",'" . mysql_real_escape_string($cat) . "','" . mysql_real_escape_string($owner). "','" . mysql_real_escape_string($assignee) . "','" . mysql_real_escape_string($content) . "','" . mysql_real_escape_string($status) . "','" . mysql_real_escape_string($prio) . "','" . mysql_real_escape_string($duedate) . "','" . mysql_real_escape_string($duetime) . "','" . mysql_real_escape_string($readonly) . "','" . mysql_real_escape_string($private) . "','" . date('U') . "','" . date('Y-m-d') . "','" . mysql_real_escape_string($sqldate) . "','" . mysql_real_escape_string($formid) . "')";

	
	
	$unique = md5($prioty . $cat . $content . $ownerNEW . $assigneeNEW . $CRMcustomer . $status . $duedate . $sqldate . $user_id . $duetime);

		
	// double-check if this entity is not entered already
	//$unique = md5($sql);
	$query = "SELECT value FROM $GLOBALS[TBL_PREFIX]cache WHERE value='" . base64_encode($unique) . "'";
	$result = mcq($query,$db);
	$row = mysql_fetch_array($result);
	if ($row[0]) {
		qlog("CheckExistence:: The same - " . $unique . " NOT SAVING THIS");
		$query = "SELECT MAX(eid) FROM $GLOBALS[TBL_PREFIX]entity";
		$result = mcq($query,$db);
		$row = mysql_fetch_array($result);
		$e = $row[0];
		$eid = $e;
		qlog("CheckExistence:: The same - " . $unique . " NOT SAVING THIS $e to " . $row[0]);
		print "<table><tr><td><font color='#FF0000'>This entity was not saved - it exists already.</font></td></tr></table>";
		$double = true;
	} else {
		qlog("CheckExistence:: Not the same - " . $unique);
//		AddEntity($customer,$category,$owner,$assignee,$content,$status,$priority,$duedate,$duetime,$readonly,$private,$formid);
		mcq($sql,$db);
		$eid = mysql_insert_id ();
		PushStashValue($unique);
	}

	
	if (!$_FILES['userfile']['tmp_name'] =="" && !$_FILES['userfile']['name']=="" && !$_FILES['userfile']['size']=="" && !$_FILES['userfile']['type']=="") {
		
		
			// Read contents of uploaded file into variable
			$fp=fopen($_FILES['userfile']['tmp_name'],"rb");
			$filecontent=fread($fp,filesize($_FILES['userfile']['tmp_name']));
			fclose($fp);
			$filecontenttomail = $filecontent;
			$filenametomail = $_FILES['userfile']['name'];
			//$filecontent = addslashes($filecontent);
			$attachment = "1";

			// insert identifier & content into $GLOBALS[TBL_PREFIX]binfiles:
//			$sql="INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,filetype,username) VALUES('$eid','$filecontent','" . $_FILES['userfile']['name'] . "','" . $_FILES['userfile']['size'] . "','" . $_FILES['userfile']['type'] . "','" . $name . "')";
//			mcq($sql,$db);
//			$statusmsg="File " . $_FILES['userfile']['name'] . $lang[wasadded];
//			journal($eid,"[limited mode] File $user_name added");
//			uselogger("File $user_name attached to entity $eid","");
			$x = AttachFile($eid,$_FILES['userfile']['name'],$filecontent,"entity",$_FILES['userfile']['type']);
		//	$x = AttachFile($eid,$_FILES['userfile']['name'],$filecontent,"entity","n/a");
	}
	// Now see if there were any extra fields added:
	
	// First, collect extra fields list

	$list = GetExtraFields();

	foreach ($list AS $extrafield) {
		$varname = "EFID" . $extrafield['id'];
		if ($$varname) {
				$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='$eid' AND name='" . $extrafield['id'] ."' AND type<>'cust'";
				$result = mcq($sql,$db);
				$gh = mysql_fetch_array($result);
				$value = $gh[0];

				if (mysql_affected_rows()>0) {
						if ($value <> $$varname) { 
							$sql = "UPDATE $GLOBALS[TBL_PREFIX]customaddons SET value = '" . $$varname . "',usr='" . $name . "' WHERE eid='" . $eid . "' AND name='" . $extrafield['id'] . "' AND type<>'cust'";
							ProcessTriggers("EFID" . $extrafield['id'],$eid,$$varname);
							$add_to_journal .= "\n" . CleanExtraFieldName($extrafield['name']) . " updated from $value to " . $$varname . "";
						}
				} else {
							$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]customaddons(eid, name, value, usr, type) VALUES('" . $eid . "','" . $extrafield['id'] . "','" . $$varname . "','" . $name . "','entity')";
							ProcessTriggers("EFID" . $extrafield['id'],$eid,$$varname);
							$add_to_journal .= "\n" . CleanExtraFieldName($extrafield['name']) . " updated from <nothing> to " . $$varname . "";
				}

				// And finally, execute the statement.
				mcq($sql,$db);
		}
	
	}

	if (!$double) {

		journal($eid,"[limited mode] Entity $eid added");
		journal($customerid,"[limited mode] Entity $eid added","customer");
		uselogger("Entity $eid added","");
		
		// Process triggers
		ProcessTriggers("customer",$eid,$customerid);
		ProcessTriggers("status",$eid,$status);
		ProcessTriggers("priority",$eid,$prio);
		ProcessTriggers("owner",$eid,"2147483647");
		ProcessTriggers("assignee",$eid,"2147483647");		
		ProcessTriggers("limited_add",$eid,"Miscellaneous trigger");

		$e = $eid;
		// First, check if something needs to be done!

		$webhost = getenv("HOSTNAME");
		require_once("class.phpmailer.php");

		$NoImageInclude=1;

		if (stristr($EmailNewEntities,"@")) {
			SendEmail($e,"global_new_entity","new", "", "", "", "");
		}


		if ($mailto) {
			$lim_to = $mailto;
			$NoImageInclude=1;
			SendEmail($e,"limited_add","new", "", "", "", "", "");
		}

		if (EmailTriggerForCustomerOwnerSet($customerid)) {
			$NoImageInclude=1;
			SendEmail($customerid,"customer_owner","new",$eid,"","","");
			journal($customerid,"Entity #" . $eid . " was coupled to this customer\nNotification e-mail send to " . GetUserName($row['customer_owner']), "customer");
		} else {
			journal($CRMcustomer,"Entity #" . $eid . " was coupled to this customer", "customer");
		}

		print "<table><tr><td><b>$lang[entrysaved] $eid</b></td></tr>";
	}
	print "</table>";
	EndHTML();
	exit;

} else {
//	print "ERROR. No action given. Nothing added, nothing updated.";
}
//	if (!$e
	$e = "_new_";
	$eid = "_new_";	
	$cl = GetClearanceLevel($GLOBALS['USERID']);

				
				if (is_array($GLOBALS['ADDFORMLIST']) && $eid == "_new_" && ($GLOBALS['FormFinity']=="Yes")) { // This means that the user may choose from different forms
							
						$to_tabs = array();
						foreach ($GLOBALS['ADDFORMLIST'] AS $form) {
							if ($form <> "default") {
								array_push($to_tabs, $form);
								qlog("Added " . GetFileSubject($form) . " TO TAB LIST!");
								$tabbs[$form] = array("cust-insert.php?action=none&a=2&&ftu=" . $form . "" => addslashes(GetFileSubject($form)), "comment" => 	"");
							} else {
								array_push($to_tabs, $form);
								qlog("Added default form TO TAB LIST!");
								$tabbs[$form] = array("cust-insert.php?action=none&a=2&ftu=default" => $lang['add'], "comment" => "");
							}
							
						}
						
						if (is_numeric($_REQUEST['ftu'])) {
							$navid = $_REQUEST['ftu'];
						} else {
							$navid = $GLOBALS['ADDFORMLIST'][0];
						}
						
						if (sizeof($GLOBALS['ADDFORMLIST'])>1 && $e=="_new_") {
							print "</table></td></tr></table>";
							InterTabs($to_tabs, $tabbs, $navid);
							print "<table><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";
						}
						
						if (is_numeric($_REQUEST['ftu'])) {
							print CustomEditForm($_REQUEST['ftu'],$e);
						} else {
							qlog("FormType: using first in row (ftu <> numeric)");
							//MainEditForm($e);	
							if (is_numeric($GLOBALS['ADDFORMLIST'][0])) {
								print CustomEditForm($GLOBALS['ADDFORMLIST'][0],$e);
							} else {
								PrintEditForm($e);
							}
						}

				} elseif ($e <> "_new_" && is_numeric(GetEntityFormID($e)) && GetEntityFormID($eid) <> 0 && ($GLOBALS['FormFinity']=="Yes")) {
					qlog("FormType: edit - Using form template from entity");
						if (GetFileSubject(GetEntityFormID($e)) <> "") {
							print CustomEditForm(GetEntityFormID($e),$e);
						} else {
							print "<img src='error.gif'> Form " . GetEntityFormID($e) . " not found. Defaulting.";
							log_msg("ERROR: Entity " . $e . " wants to use form " . GetEntityFormID($e) . " - this form is not available. Falling back to default form.");
							PrintEditForm($e);
						}
				
				} elseif ($e=="_new_" && is_numeric($GLOBALS['ENTITY_LIMITED_ADD_FORM']) && $cl == "ro") {
					qlog("FormType: add [limited] - Using form template " . $GLOBALS['ENTITY_LIMITED_ADD_FORM']);
						print CustomEditForm($GLOBALS['ENTITY_LIMITED_ADD_FORM'],$e);

				} elseif ($e=="_new_" && is_numeric($GLOBALS['ENTITY_ADD_FORM']) && ($cl=="rw" || $cl=="administrator" || $cl == "full-access-own-entities")) {
					qlog("FormType: add [full-access] - Using form template " . $GLOBALS['ENTITY_ADD_FORM']);
						print CustomEditForm($GLOBALS['ENTITY_ADD_FORM'],$e);

				} elseif (is_numeric($e) && is_numeric($GLOBALS['ENTITY_LIMITED_EDIT_FORM']) && $cl == "ro") {
					qlog("FormType: edit [limited] - Using form template " . $GLOBALS['ENTITY_LIMITED_EDIT_FORM']);
						print CustomEditForm($GLOBALS['ENTITY_LIMITED_EDIT_FORM'],$e);

				} elseif (is_numeric($e) && is_numeric($GLOBALS['ENTITY_EDIT_FORM']) && ($cl=="rw" || $cl=="administrator" || $cl == "full-access-own-entities")) {
					qlog("FormType: edit [full-access] - Using form template " . $GLOBALS['ENTITY_EDIT_FORM']);
						print CustomEditForm($GLOBALS['ENTITY_EDIT_FORM'],$e);
				} else {
					qlog("FormType: using default entity form");
					PrintEditForm($e);	
				}
	
	PrintFooter();
	EndHTML();

?>
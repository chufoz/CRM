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

include("config.inc.php");
include("getset.php");
include("language.php");

$loguser = $name;

$GLOBALS['Mass_Update'] = "No";


if ($lan) {  // personal language setting changed
				include("config.inc.php");
			    $name = $loguser;
				$sql= "UPDATE $GLOBALS[TBL_PREFIX]loginusers SET exptime='$lan' WHERE name='$name'";
			//	print $sql;
				mcq($sql,$db);
				include("language.php");
				uselogger("Managementinterface personal language adjusted","");
		}

if ($nomenu) {
	printheader_management();   
} else {
    printheader_management(1);   
}

print '<SCRIPT LANGUAGE="JavaScript" SRC="functions.js"></SCRIPT>';
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
function AdjustDateToUSAFormat(date) {

	if ('<? echo $DateFormat;?>'=='mm-dd-yyyy') { 

			day = date.substring(0,2);
			mon = date.substring(3,5);
			yer = date.substring(6,10);

			NewDate = mon + "-" + day + "-" + yer;
	} else {
			NewDate = date;
	}
	document.EditEntity.displayDate.value = NewDate;
	document.EditEntity.displayDate.blur();      
}
function AdjustDateToUSAFormat2(date) {

	if ('<? echo $DateFormat;?>'=='mm-dd-yyyy') { 

			day = date.substring(0,2);
			mon = date.substring(3,5);
			yer = date.substring(6,10);

			NewDate = mon + "-" + day + "-" + yer;
	} else {
			NewDate = date;
	}
	
	document.EditEntity.displayr.value = NewDate;
	document.EditEntity.displayr.blur();      
}
function AdjustEFDateToUSAFormat(date,field) {

	if ('<? echo $DateFormat;?>'=='mm-dd-yyyy') { 

			day = date.substring(0,2);
			mon = date.substring(3,5);
			yer = date.substring(6,10);

			NewDate = mon + "-" + day + "-" + yer;
	} else {
			NewDate = date;
	}
//	alert('field ' + field + ' set to ' + NewDate);
//	var obj = 'document.' + field + '.value';
//	document.field.value = NewDate;
	document.getElementById(field).value = NewDate;
	//document.field.blur();
	
}
function InsertSTDtext(text) {
	now = new Date;
	if (now.getMinutes() < 10) { 
		var minutes = "0" + now.getMinutes();
	}
	else {
		var minutes = now.getMinutes();
	}
	if (now.getHours() < 10) { 
		var hours = "0" + now.getHours();
	}
	else {
		var hours = now.getHours();
	}
	var add = "[" + now + "] -  <? echo GetUserName($GLOBALS['USERID']);?>: " + text + "\n\n";
	document.EditEntity.did_time.value = "Already done - don't do it again, it'll look messy";
	document.EditEntity.content.value = add + document.EditEntity.content.value;
	document.EditEntity.content.focus();
}

function InsertDateTime() {
	now = new Date;
	if (now.getMinutes() < 10) { 
		var minutes = "0" + now.getMinutes();
	}
	else {
		var minutes = now.getMinutes();
	}
	if (now.getHours() < 10) { 
		var hours = "0" + now.getHours();
	}
	else {
		var hours = now.getHours();
	}
	var add = "[" + now + "] - <? echo GetUserName($GLOBALS['USERID']);?>: \n\n";
	var len = add.length;

	document.EditEntity.did_time.value = "Already done - don't do it again, it'll look messy";
	document.EditEntity.content.value = add + document.EditEntity.content.value;
	document.EditEntity.content.focus();
	
}

function InsertDateTimeOnce() {
	
	if (document.EditEntity.did_time.value == "not yet") {
		now = new Date;
		if (now.getMinutes() < 10) { 
			var minutes = "0" + now.getMinutes();
		}
		else {
			var minutes = now.getMinutes();
		}
		if (now.getHours() < 10) { 
			var hours = "0" + now.getHours();
		}
		else {
			var hours = now.getHours();
		}
		var add = "[" + now + "] - <? echo GetUserName($GLOBALS['USERID']);?>: \n\n";
		document.EditEntity.did_time.value = "Already done - don't do it again, it'll look messy";
		document.EditEntity.content.value = add + document.EditEntity.content.value;
		document.EditEntity.content.focus();

	} 

}
//-->
</SCRIPT>
<?
   $name = $loguser;

if ($e) {
	if (CheckEntityAccess($e)=="nok") {
		print "<img src = 'error.gif'>&nbsp;Access denied";
		EndHTML();
		exit;
	}

}

$sql = "SELECT id,CLLEVEL FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='" . $name ."'";
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$loguser = $maxU1[0];
$CLLEVEL = $maxU1[1];

//print "<tr><td colspan=12><hr></td></tr>";

if (strtoupper($managementinterface)<>"ON") {
		print "<tr><td><img src='error.gif'>&nbsp;&nbsp;<b>The administrator disabled this interface. Please <a class='bigsort' href='mailto:" .$admemail."?subject=CRM " . $title . " question'>mail</a> the administrator for more information.<br><br><br><i>Message :: Configuration directive managementinterface is not set</i></td></tr>";
		uselogger("Managementinterface user denied - interface disabled. To enable, Change System Values -> managementinterface -> on","");
		exit;
}
if (!$_FILES['userfile']['tmp_name'] =="" && !$_FILES['userfile']['name']=="" && !$_FILES['userfile']['size']=="" && !$_FILES['userfile']['type']=="") {
	
//	print "EID is $eid";
		
		
// Read contents of uploaded file into variable
			$fp=fopen($_FILES['userfile']['tmp_name'],"rb");
			$filecontent=fread($fp,filesize($_FILES['userfile']['tmp_name']));
			fclose($fp);


// insert identifier & content into $GLOBALS[TBL_PREFIX]binfiles:

//			$sql="INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,filetype,username) VALUES('$e','$filecontent','" . $_FILES['userfile']['name'] . "','" . $_FILES['userfile']['size'] . "','" . $_FILES['userfile']['type'] . "','" . $name . "')";

			$x = AttachFile($e, $_FILES['userfile']['name'], $filecontent, "entity", $_FILES['userfile']['type']);

//			journal($e,"[limited mode] File " . $_FILES['userfile']['name'] . " added");
//			print $sql;

//			mcq($sql,$db);
			$statusmsg="File " . $_FILES['userfile']['name'] . "was added.";
			$addcontent .= "\nA file called " . $_FILES['userfile']['name'];
			uselogger("Managementinterface - file added (" . $_FILES['userfile']['name'] . ") on enity $e","");
}



if (!$loguser) {
				// whatever
		} else {
			$sql = "SELECT FULLNAME FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id='$loguser'";
			$result= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($result);
			$name = $maxU1[0];
			$addon = "You are logged on as $name.";
			uselogger("Managementinterface $name","Managementinterface $name");
		}

if ($action=='edit') {
	if (!$addcontent=="") {
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

	// TRIGGER - see if the owner or the assignee wants e-mails of all updates (must be done BEFORE the update!)

	if (EmailTriggerForOwnerSet($eid)==true) {
		SendEmail($e, "owner", "changed",  "<pre>" . $addcontent .  "<pre>" , "", $YourName, $YourAddress);
	} elseif (EmailTriggerForAssigneeSet($eid)==true) { // we only need to send 1 e-mail ofcourse
		SendEmail($e, "assignee", "changed", "<pre>" . $addcontent . "</pre>", "", $YourName, $YourAddress);
	}
//	print $sql;
	mcq($sql,$db);
	
	}

	
}

if ($e) {
	$a = GetClearanceLevel($loguser);

	print "<table width='50%'><tr><td>&nbsp;&nbsp;</td><td>";


	if ($a<>"read-only" && $a<>"read-only-all") {
		$sql = "SELECT assignee FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='$e'";
		$result= mcq($sql,$db);
		$resarr=mysql_fetch_array($result);
		if ($resarr['assignee']<>$loguser || $a=="ro") {
			print "<img src='error.gif'>&nbsp;&nbsp;<b>This incident has been logged.<BR>";
			uselogger("ERROR: ILLEGAL ATTEMPT BY $a USER $loguser TO ACCESS ENTITY $e","");
			exit;
		} else {
			// nothing for now
		}

	}
	   //editform($e,$loguser);
	   if ($e <> "_new_" && is_numeric(GetEntityFormID($e)) && GetEntityFormID($e) <> 0 && ($GLOBALS['FormFinity']=="Yes")) {
				qlog("FormType: edit - Using form template from entity");
				if (GetFileSubject(GetEntityFormID($e)) <> "") {
					print CustomEditForm(GetEntityFormID($e),$e);
				} else {
					print "<img src='error.gif'> Form " . GetEntityFormID($e) . " not found. Defaulting.";
					log_msg("ERROR: Entity " . $e . " wants to use form " . GetEntityFormID($e) . " - this form is not available. Falling back to default form.");
					MainEditForm($e);
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
			MainEditForm($e);	
		}
	
		print "</td></tr></table>";
	   EndHTML();
	   exit;
}

if ($CLLEVEL=="read-only-all") {
		$ONLY_SHOW_OWNER = $CLLEVEL;
} else {
		$ONLY_SHOW_OWNER = $loguser;
}
unset($query);
//$query = $sql;

$ForceGlobalLayout = true;
print "<table><tr><td>&nbsp;&nbsp;</td><td>";
	ShowEntitiesOpen();
print "</td></tr></table>";
EndHTML();
?>
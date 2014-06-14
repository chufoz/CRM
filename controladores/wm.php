<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2003 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * CRM-CTT E-mail client capable of transferring e-mails into entities
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
extract($_REQUEST);

$args['include_bodies'] = true;
$args['decode_bodies'] = true;
$args['decode_headers'] = true;

require("POP3.php");
require("mimeDecode.php");
require("config.inc.php");
require("getset.php");

// <TEMPORARY>

/*
$personalpops = array();
$personalpops[0] = array();
$personalpops[0]['popuser'] = "";
$personalpops[0]['poppass'] = "";
$personalpops[0]['pophost'] = "";
*/
// </TEMPORARY>

$personalpops = GetPersonaleMailCredentials($GLOBALS['USERID']);

$credentials = unserialize($GLOBALS['EMAILINBOX']);

$a = GetClearanceLevel($GLOBALS['USERID']);

if ($credentials['popvisi'] == "admin" && $a<>"administrator") {
	$_REQUEST['popbox'] = "pbox0";
	unset($credentials);
	qlog("Credentials unset - this user may not access the system mailbox");
}

if ($_REQUEST['popbox']<>"" && $_REQUEST['popbox']<>"system" && is_array($personalpops[0])) {
	$cbox = 0;
	$boxnr = str_replace("pbox","",$_REQUEST['popbox']) * 1;
	$GLOBALS['popuser'] = $personalpops[$boxnr]['popuser'];
	$GLOBALS['poppass'] = $personalpops[$boxnr]['poppass'];
	$GLOBALS['pophost'] = $personalpops[$boxnr]['pophost'];
	$GLOBALS['popbox']  = $_REQUEST['popbox'];
	$personalmail = true;
} else {
	$GLOBALS['popuser'] = $credentials['popuser'];
	$GLOBALS['poppass'] = $credentials['poppass'];
	$GLOBALS['pophost'] = $credentials['pophost'];
	$GLOBALS['popbox']  = "system";
}

$GLOBALS['popport'] = "110";

if ($credentials['popvisi'] == "admin" && $a<>"administrator" && !$personalmail) {
	include("header.inc.php");
	print "<img src='error.gif'> Access denied";
	EndHTML();
	exit;
}

// Create the class
$pop3 =& new Net_POP3();




if ($_REQUEST['dlf']) {
	ConnectToPOPbox();	
	$files_names = array();
	$files_blobs = array();
	$files_sizes = array();

	$input = $pop3->getMsg($_REQUEST['dlf']);
	$decode = new Mail_mimeDecode($input, "\r\n");
	$structure = $decode->decode($args);

	for ($i=0;$i<sizeof($structure->parts);$i++) {
		if (($structure->parts[$i]->d_parameters['filename'] <> "") && $structure->parts[$i]->body <> "") {
			if ($structure->parts[$i]->d_parameters['filename'] == base64_decode($_REQUEST['fn'])) {
				header("Content-Type: " . $structure->parts[$i]->ctype_primary . "/" . $structure->parts[$i]->ctype_secondary);
				header("Content-Disposition: attachment; filename=" . $structure->parts[$i]->d_parameters['filename']);
				header("Content-Description: CRM Generated Data" );
				header("Window-target: _top");
				print $structure->parts[$i]->body;
			} 
		}
		
	}
	// Disconnect
	$pop3->disconnect();

//	print "<pre>";
//	print_r($structure);
	exit;
} elseif ($_REQUEST['ae']) {
	$nonavbar = 1;
	require("header.inc.php");
	ConnectToPOPbox();
	
	$input		= $pop3->getMsg($_REQUEST['ae']);
	$decode		= new Mail_mimeDecode($input, "\r\n");
	$structure	= $decode->decode($args);
	$msgs		= $pop3->getParsedHeaders($_REQUEST['ae']);

	if ($_REQUEST['add_assignee']) {
		
		if ($structure->body<>"" && stristr($structure->ctype_primary,"TEXT")) {
				$body .= "\n\n" . $structure->body;
				$ct = $structure->ctype_secondary;
		} elseif($structure->parts[0]->body<>"" && stristr($structure->parts[0]->ctype_primary,"TEXT")) {
				$body .= "\n\n" . $structure->parts[0]->body;
				$ct = $structure->parts[0]->ctype_secondary;
		} elseif($structure->parts[0]->parts[0]->body<>"" && stristr($structure->parts[0]->parts[0]->ctype_primary,"TEXT")) {
				$body .= "\n\n" . $structure->parts[0]->parts[0]->body;
				$ct = $structure->parts[0]->parts[0]->ctype_secondary;
		} elseif($structure->parts[0]->parts[0]->parts[0]->body<>"" && stristr($structure->parts[0]->parts[0]->parts[0]->ctype_primary,"TEXT")) {
				$body .= "\n\n" . $structure->parts[0]->parts[0]->parts[0]->body;
				$ct = $structure->parts[0]->parts[0]->parts[0]->ctype_secondary;
		} elseif($structure->parts[1]->body<>"" && stristr($structure->parts[1]->ctype_primary,"TEXT")) {
				$body .= "\n\n" . $structure->parts[1]->body;
				$ct = $structure->parts[1]->ctype_secondary;
		} elseif($structure->parts[1]->parts[0]->body<>"" && stristr($structure->parts[1]->parts[0]->ctype_primary,"TEXT")) {
				$body .= "\n\n" . $structure->parts[1]->parts[0]->body;
				$ct = $structure->parts[1]->parts[0]->ctype_secondary;
		} elseif($structure->parts[1]->parts[0]->parts[0]->body<>"" && stristr($structure->parts[1]->parts[0]->parts[0]->ctype_primary,"TEXT")) {
				$body .= "\n\n" . $structure->parts[1]->parts[0]->parts[0]->body;
				$ct = $structure->parts[1]->parts[0]->parts[0]->ctype_secondary;
		} elseif($structure->parts[2]->body<>"" && stristr($structure->parts[2]->ctype_primary,"TEXT")) {
				$body .= "\n\n" . $structure->parts[2]->body;
				$ct = $structure->parts[2]->ctype_secondary;
		} else {
				$body = "(empty)";
		}
			
		$curb = "From: " . $msgs['From'] . "\nSubject: " . $msgs['Subject'] . "\nDate: " . $msgs['Date'] . "\n" . $body;
		$curb .= GetBody($_REQUEST['to_ent']);
		//SetBody($_REQUEST['to_ent'],strip_tags($curb));
		log_msg("An e-mail message was attached to an entity");

		if ($_REQUEST['newentityform'] == "default") {
			$_REQUEST['newentityform'] = 0;
		}
		if ($GLOBALS['FormFinity'] <> "Yes") {
			$_REQUEST['newentityform'] = 0;
		}

		$ent = AddEntity($_REQUEST['customer'],$msgs['Subject'],$_REQUEST['add_owner'],$_REQUEST['add_assignee'],strip_tags($curb),$_REQUEST['add_status'],$_REQUEST['add_priority'],"","","n","n",$_REQUEST['newentityform']);
		
		for ($i=0;$i<sizeof($structure->parts);$i++) {
			if (($structure->parts[$i]->d_parameters['filename'] <> "") && $structure->parts[$i]->body <> "") {
				AttachFile($ent,$structure->parts[$i]->d_parameters['filename'],$structure->parts[$i]->body,"entity","Unknown");
			}
		}
		?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			window.opener.document.location = 'edit.php?e=<? echo $ent;?>';
			window.close();
		//-->
		</SCRIPT>
		<?


	} else {
		ConnectToPOPbox();
		print "<form name='addentity' method='POST'>";
		print "<table class='crm' width='100%'>";
		print "<tr><td colspan='2'><font size='+1'>" . $lang['add'] . ": \"" . $msgs['Subject'] . "\"</font></td></tr>"; 
		print "<tr><td>" . $lang['customer'] . "</td><td>";
		print "\n<select name='customer' size='1'>";
		$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]customer WHERE active='yes' ORDER BY custname";
		$result= mcq($sql,$db);
		
		if ($SetCustTo) $ea[CRMcustomer] = $SetCustTo; // pre-set customer from customers page

		while ($CRMloginusertje= mysql_fetch_array($result)) {
			if ($CRMloginusertje[id]==$ea[CRMcustomer]) {
					$a = "SELECTED";
					$Customer = $ea[CRMcustomer];
			} else {
					$a = "";
			}
			 print "<option value='$CRMloginusertje[id]' $a size='1'>$CRMloginusertje[custname]</option>";
		}

		print "</select>";
		print "</td></tr>";
		print "<tr><td>" . $lang['assignee'] . "</td><td>";
		print "<select name='add_assignee' size='1'>";
					
		if ($e=="_new_") {
			$ea['owner'] = GetUserID($name);
		}

		
		$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]loginusers WHERE LEFT(FULLNAME,3) <> '@@@' AND active<>'no' ORDER BY FULLNAME";
		$result= mcq($sql,$db);

		while ($CRMloginusertje= mysql_fetch_array($result)) {
				if ($CRMloginusertje[id]==$ea[owner]) {
								$a = "SELECTED";
								$ok = 1;
				} else {
								$a = "";
				}
			if (!trim($CRMloginusertje[FULLNAME])== "") {
				print "<option value='$CRMloginusertje[id]' size='1' $a>$CRMloginusertje[FULLNAME]</option>";
			}
		}


		print "</select>";
		print "</td></tr>";
		print "<tr><td>" . $lang['owner'] . "</td><td>";
		print "<select name='add_owner' size='1'>";
					
		if ($e=="_new_") {
			$ea[owner] = GetUserID($name);
		}

		
		$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]loginusers WHERE LEFT(FULLNAME,3) <> '@@@' AND active<>'no' ORDER BY FULLNAME";
		$result= mcq($sql,$db);

		while ($CRMloginusertje= mysql_fetch_array($result)) {
				if ($CRMloginusertje[id]==$ea[owner]) {
								$a = "SELECTED";
								$ok = 1;
				} else {
								$a = "";
				}
			if (!trim($CRMloginusertje[FULLNAME])== "") {
				print "<option value='$CRMloginusertje[id]' size='1' $a>$CRMloginusertje[FULLNAME]</option>";
			}
		}


		print "</select>";
		print "</td></tr>";
		print "<tr><td>" . $lang['status'] . "</td><td>";
		print "<select name='add_status' size='1'>";
		$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]statusvars ORDER BY varname";
		$result= mcq($sql,$db);
		while($options = mysql_fetch_array($result)) {
			if (strtoupper(($ea[status]))==strtoupper($options[varname])) { $a="SELECTED"; } else { $a=""; }
			print "<option style='background:" . $options[color] . "' value='$options[varname]' $a>$options[varname]</option>";
		}

		print "</select>";
		print "</td></tr>";
		print "<tr><td>" . $lang['priority'] . "</td><td>";
		print "<select name='add_priority' size='1'>";
		$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]priorityvars ORDER BY varname";
		$result= mcq($sql,$db);
		while($options = mysql_fetch_array($result)) {
			if (strtoupper(($ea[status]))==strtoupper($options[varname])) { $a="SELECTED"; } else { $a=""; }
			print "<option style='background:" . $options[color] . "' value='$options[varname]' $a>$options[varname]</option>";
		}

		print "</select>";
		print "</td></tr>";
		if ($GLOBALS['FormFinity'] == "Yes") {
			print "<tr><td>Form:</td><td>";
			print "<select name='newentityform'>";
			$to_tabs = array();
			foreach ($GLOBALS['ADDFORMLIST'] AS $form) {
				if ($form <> "default") {
					print "<option value='" . $form . "'>" . GetFileSubject($form) . "</option>";
				} else {
					print "<option value='default'>Default</option>";
				}
			}
							
			print "</select>";
			print "</td></tr>";
		}
		print "<tr><td colspan='2'><input type='hidden' name='ae' value='" . $_REQUEST['ae'] . "'><input type='submit' value='" . $lang['go'] . "'></td></tr>";
		print "</table></form>";
	}
	EndHTML();
	$pop3->disconnect();
	exit;
} elseif ($_REQUEST['ate']) {
	$nonavbar = 1;
	require("header.inc.php");
	ConnectToPOPbox();
	
	$input		= $pop3->getMsg($_REQUEST['ate']);
	$decode		= new Mail_mimeDecode($input, "\r\n");
	$structure	= $decode->decode($args);
	$msgs		= $pop3->getParsedHeaders($_REQUEST['ate']);

	if ($_REQUEST['to_ent']) {
			
			print "attaching mail " .  $_REQUEST['ate'] . " to entity " . $_REQUEST['to_ent'];

			if (CheckEntityAccess($_REQUEST['to_ent'])=="ok") {
				for ($i=0;$i<sizeof($structure->parts);$i++) {
					if (($structure->parts[$i]->d_parameters['filename'] <> "") && $structure->parts[$i]->body <> "") {
						AttachFile($_REQUEST['to_ent'],$structure->parts[$i]->d_parameters['filename'],$structure->parts[$i]->body,"entity","Unknown");
					}
				}
			}

			if ($structure->body<>"" && stristr($structure->ctype_primary,"TEXT")) {
					$body .= "\n\n" . $structure->body;
					$ct = $structure->ctype_secondary;
			} elseif($structure->parts[0]->body<>"" && stristr($structure->parts[0]->ctype_primary,"TEXT")) {
					$body .= "\n\n" . $structure->parts[0]->body;
					$ct = $structure->parts[0]->ctype_secondary;
			} elseif($structure->parts[0]->parts[0]->body<>"" && stristr($structure->parts[0]->parts[0]->ctype_primary,"TEXT")) {
					$body .= "\n\n" . $structure->parts[0]->parts[0]->body;
					$ct = $structure->parts[0]->parts[0]->ctype_secondary;
			} elseif($structure->parts[0]->parts[0]->parts[0]->body<>"" && stristr($structure->parts[0]->parts[0]->parts[0]->ctype_primary,"TEXT")) {
					$body .= "\n\n" . $structure->parts[0]->parts[0]->parts[0]->body;
					$ct = $structure->parts[0]->parts[0]->parts[0]->ctype_secondary;
			} elseif($structure->parts[1]->body<>"" && stristr($structure->parts[1]->ctype_primary,"TEXT")) {
					$body .= "\n\n" . $structure->parts[1]->body;
					$ct = $structure->parts[1]->ctype_secondary;
			} elseif($structure->parts[1]->parts[0]->body<>"" && stristr($structure->parts[1]->parts[0]->ctype_primary,"TEXT")) {
					$body .= "\n\n" . $structure->parts[1]->parts[0]->body;
					$ct = $structure->parts[1]->parts[0]->ctype_secondary;
			} elseif($structure->parts[1]->parts[0]->parts[0]->body<>"" && stristr($structure->parts[1]->parts[0]->parts[0]->ctype_primary,"TEXT")) {
					$body .= "\n\n" . $structure->parts[1]->parts[0]->parts[0]->body;
					$ct = $structure->parts[1]->parts[0]->parts[0]->ctype_secondary;
			} elseif($structure->parts[2]->body<>"" && stristr($structure->parts[2]->ctype_primary,"TEXT")) {
					$body .= "\n\n" . $structure->parts[2]->body;
					$ct = $structure->parts[2]->ctype_secondary;
			} else {
					$body = "(empty)";
			}
			
			$curb = "From: " . $msgs['From'] . "\nSubject: " . $msgs['Subject'] . "\nDate: " . $msgs['Date'] . "\n" . $body;
			$curb .= "\n\n" . GetBody($_REQUEST['to_ent']);
			SetBody($_REQUEST['to_ent'],strip_tags($curb));
			log_msg("An e-mail message was attached to an entity");
			?>
			<SCRIPT LANGUAGE="JavaScript">
			<!--
				window.opener.document.location = 'edit.php?e=<? echo $_REQUEST['to_ent'];?>';
				window.close();
			//-->
			</SCRIPT>
			<?

	} else {

			print "<form name='addentity' method='POST'>";
			print "<table class='crm' width='100%'>";
			print "<tr><td><font size='+1'>Append: \"" . $msgs['Subject'] . "\"</font></td></tr>"; 
			print "<tr><td>To entity:</td></tr>";
			print "<tr><td><select name='to_ent'>";

			$sql = "SELECT eid, category FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted<>'y' ORDER BY eid"; 
			$result= mcq($sql,$db);
			while ($row= mysql_fetch_array($result)) {
				if (CheckEntityAccess($row['eid'])=="ok") {
					print "<option value='" . $row['eid'] . "'>" . $row['eid'] . ": " . $row['category'] . "</option>";
				}
			}

			print "</select></td></tr>";
			print "<tr><td><input type='hidden' name='ate' value='" . $_REQUEST['ate'] . "'><input type='submit' value='" . $lang['go'] . "'></td></tr>";

			print "</table></form>";
	}
	EndHTML();
	$pop3->disconnect();
	exit;
} else {
	require("header.inc.php");
	ConnectToPOPbox();
}
/*
if ($_REQUEST['dm']) {
	$pop3->deleteMsg($_REQUEST['dm']);
	// Disconnect
	$pop3->disconnect();
	>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
		document.location='wm.php?offset=<?echo $_REQUEST['offset'];?>';
	//-->
	</SCRIPT>
	
	ConnectToPOPbox();
}
*/


print "</table>";

//require("class.phpmailer.php");



if ($_REQUEST['GetBody']) {

	$input = $pop3->getMsg($_REQUEST['GetBody']);
	$str_headers = $pop3->getParsedHeaders($_REQUEST['GetBody']);

	$decode = new Mail_mimeDecode($input, "\r\n");
	$structure = $decode->decode($args);
	if ($structure->body<>"" && stristr($structure->ctype_primary,"TEXT")) {
			$body .= "\n\n" . $structure->body;
			$ct = $structure->ctype_secondary;
	} elseif($structure->parts[0]->body<>"" && stristr($structure->parts[0]->ctype_primary,"TEXT")) {
			$body .= "\n\n" . $structure->parts[0]->body;
			$ct = $structure->parts[0]->ctype_secondary;
	} elseif($structure->parts[0]->parts[0]->body<>"" && stristr($structure->parts[0]->parts[0]->ctype_primary,"TEXT")) {
			$body .= "\n\n" . $structure->parts[0]->parts[0]->body;
			$ct = $structure->parts[0]->parts[0]->ctype_secondary;
	} elseif($structure->parts[0]->parts[0]->parts[0]->body<>"" && stristr($structure->parts[0]->parts[0]->parts[0]->ctype_primary,"TEXT")) {
			$body .= "\n\n" . $structure->parts[0]->parts[0]->parts[0]->body;
			$ct = $structure->parts[0]->parts[0]->parts[0]->ctype_secondary;
	} elseif($structure->parts[1]->body<>"" && stristr($structure->parts[1]->ctype_primary,"TEXT")) {
			$body .= "\n\n" . $structure->parts[1]->body;
			$ct = $structure->parts[1]->ctype_secondary;
	} elseif($structure->parts[1]->parts[0]->body<>"" && stristr($structure->parts[1]->parts[0]->ctype_primary,"TEXT")) {
			$body .= "\n\n" . $structure->parts[1]->parts[0]->body;
			$ct = $structure->parts[1]->parts[0]->ctype_secondary;
	} elseif($structure->parts[1]->parts[0]->parts[0]->body<>"" && stristr($structure->parts[1]->parts[0]->parts[0]->ctype_primary,"TEXT")) {
			$body .= "\n\n" . $structure->parts[1]->parts[0]->parts[0]->body;
			$ct = $structure->parts[1]->parts[0]->parts[0]->ctype_secondary;
	} elseif($structure->parts[2]->body<>"" && stristr($structure->parts[2]->ctype_primary,"TEXT")) {
			$body .= "\n\n" . $structure->parts[2]->body;
			$ct = $structure->parts[2]->ctype_secondary;
	} else {
			$body = "(empty)";
	}

	$files_names = array();
	$files_blobs = array();
	$files_sizes = array();

	for ($i=0;$i<sizeof($structure->parts);$i++) {
		if (($structure->parts[$i]->d_parameters['filename'] <> "") && $structure->parts[$i]->body <> "") {
			array_push($files_names, $structure->parts[$i]->d_parameters['filename']);
			//array_push($files_blobs, $structure->parts[$i]->body);
			array_push($files_sizes, strlen($structure->parts[$i]->body));
		}
		
	}
	//print "<pre>";
	//print_r($structure);
	print "<table width=75%><tr><td>&nbsp;&nbsp;&nbsp;</td><td>";
	print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<b>Box: " . $GLOBALS['popuser'] . "@" . $GLOBALS['pophost'] . "</b></legend>";
	print "<table class='crm' width='100%'>";
	print "<tr><td><font size='+1'>" . $structure->headers['subject'] . "</font></td><td align='right' colspan=1><a href='javascript:history.back(1)' title='Back to list'><img src='list.gif' style='border:0'></a>";
//	print "&nbsp;<a href='javascript:history.back(1)' title='Reply'><img src='reply.gif' style='border:0'></a>";
//	print "&nbsp;<a href='javascript:history.back(1)' title='Forward'><img src='forward.gif' style='border:0'></a>";
	print "&nbsp;<a href=\"javascript:popAAEwindow('wm.php?popbox=" . $GLOBALS['popbox'] . "&ae=" . $_REQUEST['GetBody'] . "');\" title='Add as entity'><img src='fullscreen_maximize.gif' style='border:0'></a>";
	print "&nbsp;<a href=\"javascript:popAAEwindow('wm.php?popbox=" . $GLOBALS['popbox'] . "&ate=" . $_REQUEST['GetBody'] . "');\" title='Append to entity'><img src='append.gif' style='border:0'></a>";
//	print "&nbsp;<a href='wm.php?dm=" . $_REQUEST['GetBody'] . "' title='Delete'><img src='deletemail.gif' style='border:0'></a>";
	print "</td></tr>";

	print "<tr><td colspan=2>From: " . $str_headers['From'] . "</td>";



	print "<tr><td colspan=2>To: " . $str_headers['To'] . "</td></tr>";
	print "<tr><td colspan=2>Date: " . $str_headers['Date'] . "</td></tr>";
	print "<tr><td colspan=2>Attachments: ";
	for ($i=0;$i<sizeof($files_names);$i++) {
		print "<br><img src='arrow.gif'>&nbsp;<a href='wm.php?popbox=" . $GLOBALS['popbox'] . "&dlf=" . $_REQUEST['GetBody'] . "&fn=" . base64_encode($files_names[$i]) . "'>" . $files_names[$i] . "</a> <font color='#B1B1B1'>(" . number_format($files_sizes[$i]) . " bytes)</font>";

	}


	if (strstr($ct,"html")) {
		 print "<tr><td colspan=2>" . wordwrap($body,120) . "</td></tr>";
	} else {
		 $body = eregi_replace("javascript","javascript_PROTECTED",$body);
		 print "<tr><td colspan=2><pre>" . wordwrap (htmlspecialchars($body),120) . "</pre></td></tr>";
	}	
	
	

	print "</table>";
	print "</fieldset>";
	print "</td></tr></table>";
	


} else {

	$listing = $pop3->getListing();

	//print "<pre>";
	//print_r($listing);

	$listing = array_reverse($listing);

	print "<table width='80%'><tr><td>&nbsp;&nbsp;&nbsp;</td><td>";
	print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Incoming e-mail</legend>";
	print "<table class='crm' width='100%'>";
	print "<tr><td colspan=8><b>Box: ";
	$pbox=0;
	if (is_array($personalpops[0])) {
		print "<form name='chgbox' method='GET'><input type='hidden' name='popbox' value=''>";
		print "<select name='popselectbox' onchange=\"document.chgbox.popbox.value=document.chgbox.popselectbox[document.chgbox.popselectbox.selectedIndex].value;document.chgbox.popselectbox[document.chgbox.popselectbox.selectedIndex].value='Loading...';document.chgbox.popselectbox.disabled=true;document.chgbox.submit();\">";
		
		if ($GLOBALS['popbox'] == "system") {
			$a = "SELECTED";
		} else {
			unset($a);
		}
		if (isset($credentials)) {
			print "<option value='system' $a>" . $credentials['popuser'] . "@" . $credentials['pophost'] . "</option>";
		}
		foreach($personalpops AS $box) {
			if ("pbox" . $pbox == $GLOBALS['popbox']) {
				$a = "SELECTED";
			} else {
				unset($a);
			}
			print "<option $a value='pbox" . $pbox . "'>" . $box['popuser'] . "@" . $box['pophost'] . "</option>";
			$pbox++;
		}
		
		print "</select></form>";
	} else {
		print "<b>" . $credentials['popuser'] . "@" . $credentials['pophost'] . "</b>";
	}

	print "</td></tr>";
	print "<tr><td><b>From</b></td><td><b>Subject</b></td><td><b>Date/time</b></td><td><b>Size (bytes)</b></td><td><img src='attach.gif'></td><td><b>" . $lang['add'] . "</b></td><td><b>Append</b></a></tr>";
	//<td>" . $lang['delete'] . "</td>

	if ($_REQUEST['offset']) {
		$offzet = $_REQUEST['offset'];
	} else {
		$offzet = 0;
	}
	
	$tp = "";

	$now_dt = getdate(date("U"));

	for ($c=$offzet;$c<($offzet+16);$c++) {

		$message = $listing[$c];
		$input = $pop3->getMsg($message['msg_id']);	
		$decode = new Mail_mimeDecode($input, "\r\n");
		$structure = $decode->decode($args);


		if ($message['msg_id']) {
			$attm = 0;
			for ($i=0;$i<sizeof($structure->parts);$i++) {
				if (($structure->parts[$i]->d_parameters['filename'] <> "") && $structure->parts[$i]->body <> "") {
						$attm++;
				}
				
			}

			$list = $pop3->getParsedHeaders($message['msg_id']);
			
			$msg_dt = getdate(strtotime($list['Date']));

			if (($msg_dt['year'] == $now_dt['year']) && ($msg_dt['yday'] == $now_dt['yday'])) {
				$ins = " style=' background: #EDF376'";
				$back_color = "#EDF376";
			} else {
				$back_color = "#FFFFFF";
				unset($ins);
			}

			if (strlen($msg_dt['mday']) == 1) {
				$msg_dt['mday'] = "0" . $msg_dt['mday'];
			}
			if (strlen($msg_dt['mon']) == 1) {
				$msg_dt['mon'] = "0" . $msg_dt['mon'];
			}
			if (strlen($msg_dt['minutes']) == 1) {
				$msg_dt['minutes'] = "0" . $msg_dt['minutes'];
			}


			$date = TransformDate($msg_dt['mday'] . "-" . $msg_dt['mon'] . "-" . $msg_dt['year']) . " " . $msg_dt['hours'] . ":" . $msg_dt['minutes'] . "h";

			$tp .= "<tr $ins OnClick=\"document.location='wm.php?popbox=" . $GLOBALS['popbox'] . "&GetBody=" . $message['msg_id'] . "';\" style='cursor: pointer' onmouseover=\"style.background='#E9E9E9';\"  onmouseout=\"style.background='" . $back_color . "';\"><td nowrap>" . $list['From'] . "</td><td>" . $list['Subject'] . "</td><td nowrap>" . $date . "</td><td>" . number_format($message['size']) . "</td><td>" . $attm . "</td><td><a href=\"javascript:popAAEwindow('wm.php?popbox=" . $GLOBALS['popbox'] . "&ae=" . $message['msg_id'] . "');\" title='Add as entity'><img src='fullscreen_maximize.gif' style='border: 0'></a></td><td><a href=\"javascript:popAAEwindow('wm.php?popbox=" . $GLOBALS['popbox'] . "&ate=" . $message['msg_id'] . "');\" title='Append to entity'><img src='append.gif' style='border:0'></a></td></tr>";
			//<td>&nbsp;<a href='wm.php?dm=" . $message['msg_id'] . "&offset=" . $_REQUEST['offset'] . "' title='Delete'><img src='deletemail.gif' style='border:0'></a></td>
			unset($attm);
		}
	}

	$message = $listing[$c+1];
	if ($offzet<>0) {
		$t = $offzet - 15;
		if ($t<0) {
			$t = 0;
		}
		print "<a href='wm.php?popbox=" . $GLOBALS['popbox'] . "&offset=" . $t . "' title='Previous 15 messages'><img style='border:0' src='larrow.gif'></a>";
	} else {
		// nothin'
	}
	print "&nbsp;<a href='wm.php?popbox=" . $GLOBALS['popbox'] . "&offset=0' title='Refresh'><img style='border:0' src='getmail.gif'></a>";
	if ($message['msg_id']) {
		print "&nbsp;<a href='wm.php?popbox=" . $GLOBALS['popbox'] . "&offset=" . $c . "' title='Next 15 messages'><img style='border:0' src='rarrow.gif'></a>";
	}

	print $tp;

	print "</table>";

	print "</fieldset>";
	print "</td></tr></table>";
}

// Disconnect
$pop3->disconnect();
EndHTML();

function ConnectToPOPbox() {
	global $pop3;
	// Connect to POP box
	if(PEAR::isError( $ret= $pop3->connect($GLOBALS['pophost'] , $GLOBALS['popport'] ) )){
		echo "ERROR: Could not connect to host " . $GLOBALS['pophost'] . ":" . $GLOBALS['popport'];
		exit();
	}

	// Login to POP server
	if(PEAR::isError( $ret= $pop3->login($GLOBALS['popuser'] , $GLOBALS['poppass'],'USER' ) )){
		echo "ERROR: " . $ret->getMessage() . "\n";
		exit();
	}
}

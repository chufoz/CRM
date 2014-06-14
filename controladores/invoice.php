<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This script generates invoices and mail-merges
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
extract($_REQUEST);

if ($_SERVER['SCRIPT_NAME']) {
	$web = 1;
	$EnableRepositorySwitcherOverrule="n";	
} else {
	require("config.inc.php");
	require("functions.php");

	print "This function is not available using the command line (yet)\n\n";
	exit;

	print "Generate invoices\n\n";
	if ($argv[1]=="-help" || $argv[1]=="--help" || $argv[1]=="help" || $argv[1]=="-h") {
		print "\nUsage:\n";
		print "\t[no arguments]\t:Interactive\n";
		//print "or:\n";
		//print "\t[reposnr] [user] [pass] [y|n] - (y = auto repair, n = prompt)\n";
		print "\nExample: php -q checkdb.php 0 admin admin_pwd y\n\n";
		exit;
	}
	if ($argv[1]) {
		$repository = $argv[1];
	} 

	if ($argv[2]) {
		$username = $argv[2];
	} 
	if ($argv[3]) {
		$password = $argv[3];
	} 
	if ($argv[4]) {
		$auto = $argv[4];
		$auto=1;
	} 
	if (!CommandlineLogin($username,$password,$repository)) {
		print "Exiting...";
		exit;
	}

	$include="1";
	include("sumpdf.php");
	include("language.php");
	

	$_REQUEST['defVAT']	= $GLOBALS['DefaultVAT'];
	$_REQUEST['whichcust'] = "All";


	$sql = "SELECT fileid,filename,creation_date,username FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='0' AND filetype='TEMPLATE'";
	$result = mcq($sql,$db);
	$templates = array();
	while ($result2 = mysql_fetch_array($result)) {
		$hui++;
		$templates[$hui] = $result2['fileid'];
		print " " . $hui . " - " . $result2['filename'] . "\n";

	}

	print "\nWhich template do you want to use? (1-" . sizeof($templates) . ")\n CRM> ";
	$a = readln();

	$_REQUEST['template'] = $templates[$a];

	print "\nAttach generated invoices to customer dossier? (Y|n)\n CRM> ";
	$a = readln();

	if ($a == "y" || $a == "Y" || $a == "") {
		$_REQUEST['attach_to_dossier'] = "Yes";
	}

	print "\nStart date: (DD-MM-YYYY) \n CRM> ";
	$a = readln();
	$_REQUEST['sdate'] = $a;


	print "\nEnd date:   (DD-MM-YYYY) \n CRM> ";
	$a = readln();
	$_REQUEST['edate'] = $a;

	if (!$path) {
		print "Where do you want the report to be placed? [./]: ";
		$path = readln();
	}
	if ($path=="") {
		$path = "./";
	} else {
		$path .= "/";
	}

	$COMMAND_LINE = true;
	
}
if ($_REQUEST['little'] && $_REQUEST['stashid'] && !$_REQUEST['mailmerge']) {
	$nonavbar = 1;
	require("header.inc.php");
	print "<table><tr><td>";
	PrintMMForm(true);
	print "</td></tr></table>";
	exit;
}

if (($_REQUEST['SingleEntity'])) {
		//$nonavbar=1;
		//require_once("header.inc.php");
		require_once("config.inc.php");
		require_once("getset.php");
		require_once("functions.php");
		if (CheckEntityAccess($_REQUEST['SingleEntity']) <> "ok" && CheckEntityAccess($_REQUEST['SingleEntity']) <> "readonly") {
			PrintAD(11243);
			EndHTML();
			exit;
		}
	if ($_REQUEST['SingleEntity'] && $_REQUEST['template']) {

		$_REQUEST['DelAfterProcess']="";
		$_REQUEST['SetStatusTo']="All"; 
		$_REQUEST['SetReadonlyTo']="All";
		$_REQUEST['SetOwnerTo']="All";
		$_REQUEST['SetAssigneeTo']="All";
//		$_REQUEST['defVAT'] = $GLOBALS['DefaultVAT'];

	} else {
		$nonavbar = true;
		require("header.inc.php");
		print "<table><tr><td><form name='SingleInvoice' method='POST' target='document.window.opener'>";
		print "<input type='hidden' name='SingleEntity' value='" . $_REQUEST['SingleEntity'] . "'>";
		print "<input type='hidden' name='edate' value='01-01-8000'>";
		print "<input type='hidden' name='sdate' value='01-01-2300'>";
		print "<input type='hidden' name='defVAT' value='" . $GLOBALS['DefaultVAT'] . "'>";
		print "<input type='hidden' name='whichcust' value='" . $_REQUEST['SingleEntity'] . "'>";
		print "<table>";
		print "<tr><td><b>" . $lang['createinvoice'] . " " . $_REQUEST['SingleEntity'] . "</b><br><br></td></tr>";
		print "<tr><td>" . $lang['rtftemplate'] . ":</td><td><select style='width:250' name='template'>";
		$sql = "SELECT fileid,filename,creation_date,username FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='0' AND filetype='TEMPLATE_INVOICE'";
		$result = mcq($sql,$db);
		while ($row = mysql_fetch_array($result)) {
			if ($_REQUEST['template']==$row['fileid']) {
				$ins = "SELECTED";
			} else {
				unset($ins);
			}
			print "<option $ins value = '" . $row['fileid'] ."'>" . $row['filename'] . "</option>";
		}
		print "</select></td></tr>";
		print "<tr><td>" . $lang['includelog'] . ":</td><td><select style='width:250' name='includelog'><option value='0'>$lang[no]</option><option>$lang[yes]</option></select></td></tr>";
		print "<tr><td>" . $lang['attachindividualtocustomer'] . ":</td><td><select style='width:250' name='attach_to_dossier'><option value='Yes'>$lang[yes]</option><option>$lang[no]</option></select></td></tr>";
		print "<tr><td>" . $lang['attachindividualtoentity'] . " ". $_REQUEST['SingleEntity'] . ":</td><td><select style='width:250' name='attach_to_entity'><option value='Yes'>$lang[yes]</option><option>$lang[no]</option></select></td></tr>";
		print "<tr><td><input type='button' name='submitknop' value='$lang[go]' OnClick='doso();'></form></td></tr></table>";

		
			?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
			function doso() {
				
				window.opener.document.location="invoice.php?SingleEntity=" + document.SingleInvoice.SingleEntity.value + "&edate=01-01-8000&sdate=01-01-1970&defVAT=" + document.SingleInvoice.defVAT.value + "&whichcust=t&template=" + document.SingleInvoice.template.value + "&includelog=" + document.SingleInvoice.includelog.value + "&attach_to_dossier=" + document.SingleInvoice.attach_to_dossier.value + "&attach_to_entity=" + document.SingleInvoice.attach_to_entity.value;

				window.close();

			}
			//-->
			</SCRIPT>
			<?
		EndHTML();
		exit;
	}

}

if ((!$_REQUEST['edate'] || !$_REQUEST['sdate'] || !$_REQUEST['defVAT'] || !$_REQUEST['whichcust'] || !$_REQUEST['template'] || $_REQUEST['whichcust']=="bla") && !$COMMAND_LINE) {
	if (!$COMMAND_LINE) {
		require("header.inc.php");
	} else {
		print "COMMAND LINE PRINTING HTML MENU. THIS IS NOT GOOD";
	}
	
	if (!$nonavbar) {
		print "</table><table border=0 width='70%'><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size=+1>Invoice & mail merge</font>&nbsp;</legend>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=+2>" . $title . "</font><table border=0 width='80%'><tr><td>&nbsp;&nbsp;</td><td>";
	}

	$sql = "SELECT fileid,filename,creation_date,username FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='0' AND filetype='TEMPLATE_INVOICE'";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		$file++;
	}

	if ($file<1) {
		print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<img src='error.gif'></legend>";
		print "<br>There were no RTF templates found in this installation. Please add some templates first.<br><br></fieldset>";
		print "</td></tr></table></fieldset>";
		EndHTML();
		exit;
	}

	$list = GetExtraFields();

	foreach ($list AS $extra_field) {
				if ($extra_field['fieldtype'] == "invoice qty") {
						$IHS = true;
				}
				if ($extra_field['fieldtype'] == "invoice cost" || $extra_field['fieldtype'] == "invoice cost including VAT") {
						$IHC = true;
				}
	}


	print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[geninv]&nbsp;</legend>";
	if ($IHC) {
		print "<form name='editform' METHOD='POST'><table width='100%'>";
		if (!$IHS) {
			print "<tr><td colspan='2'><img src='info.gif'>&nbsp;$lang[IHSwarning].<br><hr></td></tr>";
		}
		print "<tr><td width='250'>$lang[startdate]:</td><td><input type='text' name='sdate' size='10' value='" . $_REQUEST['sdate'] . "' OnClick=\"javascript:popcalendarSelectEFCustomer('editform.sdate',0);\" OnKeyUp=\"javascript:popcalendarSelectEFCustomer('editform.sdate',0);\"></td></tr>";
		print "<tr><td>$lang[enddate]:</td><td><input size='10' type='text' name='edate' value='" . $_REQUEST['edate'] . "' OnClick=\"javascript:popcalendarSelectEFCustomer('editform.edate',0);\" OnKeyUp=\"javascript:popcalendarSelectEFCustomer('editform.date',0);\"></td></tr>";

		if (!$_REQUEST['defVAT']) {
			$_REQUEST['defVAT'] = $GLOBALS['DefaultVAT'];
		}
		print "<tr><td>$lang[defVAT]:</td><td><input type='text' size=1 name='defVAT' value='" . $_REQUEST['defVAT'] . "'>%</td></tr>";

		print "<tr><td>" . $lang['customers'] . ":</td><td>";
		
		if ($_REQUEST['whichcust']=="bla") {
				print "<select name=selectedcustomer size='1'>";
						$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]customer ORDER BY custname";
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

			print "<input type='hidden' name='whichcust' value='selected'>";
		} else {
			print "<select name='whichcust'  style='width:250'><option value='All'>" . $lang['all'] . "</option><option value='Only active'>$lang[onlyactive]</option><option value='bla'>$lang[selectsingle]</option></select>";
		}
		
		print "</td></tr>";
		print "<tr><td>" . $lang['entities'] . ":</td><td>";
		print "<select size='1' style='width:250' name='whichentities'>";
		print "<option>All except deleted</option>";
		print "<option>" . $lang['all'] . "</option>";
		
		if (strtoupper($GLOBALS['EnableCustInsert']) == "YES") {
			print "<option value='All but inserted (not yet assigned)'>$lang[abdnya]</option>";
			print "<option value='Only non-deleted and assigned'>$lang[ondaa]</option>";
		}
		
		print "</select>";
		print "</td></tr>";

		// which status
		print "<tr><td>" . $lang['status'] . ":</td><td>";
		print "<select name='HavingStatus'  style='width:250' size='1'>";
		print "<option value='All'>" . $lang['all'] . "</option>";
		$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]statusvars ORDER BY varname";
		$result= mcq($sql,$db);
		while($options = mysql_fetch_array($result)) {
			if (strtoupper(($ea[status]))==strtoupper($options[varname])) { $a="SELECTED"; } else { $a=""; }
			print "<option style='background:" . $options[color] . "' value='$options[varname]' $a>$options[varname]</option>";
		}

		print "</select></td></tr>";
		// which priority
		print "<tr><td>" . $lang['priority'] . ":</td><td>";
		print "<select name='HavingPriority'  style='width:250' size='1'>";
		print "<option value='All'>" . $lang['all'] . "</option>";
		$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]priorityvars ORDER BY varname";
		$result= mcq($sql,$db);
		while($options = mysql_fetch_array($result)) {
			if (strtoupper(($ea[status]))==strtoupper($options[varname])) { $a="SELECTED"; } else { $a=""; }
			print "<option style='background:" . $options[color] . "' value='$options[varname]' $a>$options[varname]</option>";
		}

		print "</select></td></tr>";
		print "<tr><td colspan=3><hr></td></tr>";
		print "<tr><td>$lang[deap]:</td><td><select style='width:250' name='DelAfterProcess'><option value='0'>$lang[no]</option><option value='1'>$lang[yes]</option></td></tr>"; 
		print "<tr><td>$lang[apsest]:</td><td>";
		print "<select name='SetStatusTo' style='width:250' size='1'>";
		print "<option value='All'>$lang[donothing]</option>";
		$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]statusvars ORDER BY varname";
		$result= mcq($sql,$db);
		while($options = mysql_fetch_array($result)) {
			if (strtoupper(($ea[status]))==strtoupper($options[varname])) { $a="SELECTED"; } else { $a=""; }
			print "<option style='background:" . $options[color] . "' value='$options[varname]' $a>$options[varname]</option>";
		}

		print "</select></td></tr>";
		
		print "<tr><td>$lang[apseot]:</td><td>";
		print "<select style='width:250' name='SetOwnerTo'  style='width:250' size='1'>";
		print "<option value='All'>$lang[donothing]</option>";
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
		print "</select></td></tr>";
		print "<tr><td>$lang[apseat]:</td><td>";
		print "<select style='width:250' name='SetAssigneeTo' size='1'>";
		print "<option value='All'>$lang[donothing]</option>";
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
		print "</select></td></tr>";
		print "<tr><td>$lang[apsrft]:</td><td>";
		print "<select style='width:250' name='SetReadonlyTo' size='1'>";
		print "<option value='All'>$lang[donothing]</option>";
		print "<option value='1' size='1'>Read-only</option>";
		print "</select></td></tr>";

		print "<tr><td colspan=3><hr></td></tr>";
		print "<tr><td>$lang[rtftemplate]:</td><td><select style='width:250' name='template'>";
		$sql = "SELECT fileid,filename,creation_date,username FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='0' AND filetype='TEMPLATE_INVOICE'";
		$result = mcq($sql,$db);
		while ($row = mysql_fetch_array($result)) {
			if ($_REQUEST['template']==$row['fileid']) {
				$ins = "SELECTED";
			} else {
				unset($ins);
			}
			print "<option $ins value = '" . $row['fileid'] ."'>" . $row['filename'] . "</option>";
		}
		print "</select></td></tr>";
		print "<tr><td>$lang[includelog]:</td><td><select style='width:250' name='includelog'><option value='0'>$lang[no]</option><option value='Yes'>$lang[yes]</option></select></td></tr>";
		print "<tr><td>$lang[attachindividualtocustomer]:</td><td><select style='width:250' name='attach_to_dossier'><option value='Yes'>$lang[yes]</option><option>$lang[no]</option></select></td></tr>";
		print "<tr><td><input type='submit' name='submitknop' value='$lang[go]'></form></td></tr></table>";
	
	} else {
		print "<br><img src='error.gif'>&nbsp;Invoicing is disabled because no extra 'Invoice cost' fields were found. CRM-CTT needs that field to generated invoices.<br><br>";
	}

	print "</fieldset><br><br>";

	PrintMMForm();

	EndHTML();

} else {

	$nonavbar=1;
	if (!$COMMAND_LINE) {
		require_once("config.inc.php");
		require_once("getset.php");
	} else {
		
		function journal($eid,$msg,$JournalType="entity") {
			global $EnableEntityJournaling;
			if (strtoupper($EnableEntityJournaling)=="YES" || (stristr($msg,"[admin]"))) {
				
				$msg = addslashes($msg);

				// $msg = base64_encode($msg);

				$sql = "INSERT INTO " . $GLOBALS[TBL_PREFIX] ."journal (eid,user,message,type) VALUES('$eid','" . $GLOBALS[USERID] . "','$msg','" . $JournalType ."')";

				mcq($sql,$db);
			}
		}
	}
	if ($_REQUEST['attach_to_dossier'] == "Yes") {
		$attach_to_customer = 1;
		qlog_special("Attaching invoices to customer dossiers");
	} else {
		qlog_special("NOT attaching invoices to customer dossiers");
	}

	$VAT 		= $_REQUEST['defVAT'];
	$FROM 		= $_REQUEST['sdate'];
	$TO 		= $_REQUEST['edate'];
	$ACTIVE 	= $_REQUEST['whichcust'];
	$ADDLOG		= $_REQUEST['includelog'];

	if ($_REQUEST['selectedcustomer']) {
		$limit_to_customer = $_REQUEST['selectedcustomer'];
	}
	

	if ($mailmerge) { 
		qlog_special("This is a mailmerge");
		$GLOBALS['CURFUNC'] = "MailMerge::";	
		if ($_REQUEST['stashid']) {
			$sql = PopStashValue($_REQUEST['stashid']);
			qlog_special("MM: from stash: $sql");
		} elseif ($ACTIVE=="All") {
			$sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]customer";
			qlog_special("MM: All");
		} elseif ($limit_to_customer) {
			$sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]customer WHERE id='" . $limit_to_customer ."'";
			qlog_special("MM: Only 1");
		} elseif ($ACTIVE=="Only active") {
			$sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]customer WHERE active = 'yes'";
			qlog_special("MM: Active only");
		} else {
			$sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]customer";
			qlog_special("MM: All (forced)");
		}

		if ($_REQUEST['attach_to_dossier'] == "Yes") {
			$attach_to_customer = 1;
			qlog_special("Attaching mailmerge to customer dossiers");
		} else {
			qlog_special("NOT attaching mailmerge to customer dossiers");
		}


		$result = mcq($sql,$db);
		$cust_list = array();
		while ($row = mysql_fetch_array($result)) {
			$auth = CheckCustomerAccess($row['id']);
			if ($auth == "ok" || $auth == "readonly") {
						array_push($cust_list,$row['id']);
			}
		}
		
		// Get template from database
		
		$sql = "SELECT content FROM $GLOBALS[TBL_PREFIX]blobs WHERE fileid='" . $_REQUEST['template'] . "'";
		$result = mcq($sql,$db);
		$result = mysql_fetch_array($result);

		$template_orig = $result[0];
		$template_array = split("\015\012",$template_orig);


		
		

		unset($result);

		foreach ($cust_list AS $klant) {
				
				$template = $template_orig;
				
				
				journal($klant,"This customer was included in a mail merge","customer");
				$template = str_replace("{\\rtf1","",$template);
				
			
				$template = ParseTemplateCustomer($template,$klant);
				$template = ParseTemplateGeneric($template);


				// strip the last }
				$template = substr($template,0,strlen($template)-1);

							
				if ($attach_to_customer) {
						$filename = "CRM-CTT-mail_merge-" . date("Fj-Y-Hi") . "h.rtf";
						$template = "{\\rtf1" . ParseTemplateGeneric($template) . "}";
//						$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,filetype,username,type) VALUES ('" . $klant . "','" . addslashes($template) . "','" . $filename . "','" . strlen($template) . "','RTF Mailmerge document','" . $GLOBALS['USERNAME'] . "','cust')";
//						$result = mcq($sql,$db);
		//function AttachFile($koppelid,$filename,$content,$type="entity",$filetype="Unknown") {
						AttachFile($klant, $filename, $template, "cust", "RTF Mailmerge document");
						qlog_special("Attached mailmerge " . $filename . " to customer " . $klant);
						$add_to_journal .= "Attached mailmerge " . $filename . "\n";
					}


				if ($not_1st) {
					$total .= "\page " . ParseTemplateGeneric($template);
					$something = true;
				} else {
					$total .= $template;
					$not_1st = true;
					$something = true;
				}
		}
	
		if ($something) {
			$total = ParseTemplateForRTF($total);
			$filename = "CRM-CTT-mail_merge-" . date("Fj-Y-Hi") . "h.rtf";
			header("Content-Type: RTF");
			header("Content-Disposition: attachment; filename=" . $filename);
			header("Content-Description: CRM Generated Data" );
			header("Window-target: _top");
			
			// output file
			
			print "{\\rtf1";
			print $total;
			print "}";
		} else {
				?>
					<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
					<!--
						alert('Empty result set');
						document.location='invoice.php';
					//-->
					</SCRIPT>
				<?
		} 

	} else { // not a mailmerge, it's an invoice job

		$td1 = explode("-",$_REQUEST['sdate']); // dd-mm-yyyy
		$td2 = explode("-",$_REQUEST['edate']); // dd-mm-yyyy

		$SDATE = "$td1[2]-$td1[1]-$td1[0]";
		$EDATE = "$td2[2]-$td2[1]-$td2[0]";

		$SDATE_EPOCH = @mktime(0,0,0,$td1[1],$td1[0],$td1[2]);
		$EDATE_EPOCH = @mktime(0,0,0,$td2[1],$td2[0],$td2[2]);

		$GLOBALS['CURFUNC'] = "Invoice::";	
		qlog_special("Period - " . $SDATE . " to " . $EDATE . " diff: " . ($EDATE_EPOCH-$SDATE_EPOCH));
		
		if (($EDATE_EPOCH-$SDATE_EPOCH)<0) {
			// period is negative, swap them, help the user :)
			$tmp = $SDATE;
			$SDATE = $EDATE;
			$EDATE = $tmp;
			qlog_special("Negative period diff - dates swapped");
			$SDATE_EPOCH = @mktime(0,0,0,$td2[1],$td2[0],$td2[2]);
			$EDATE_EPOCH = @mktime(0,0,0,$td1[1],$td1[0],$td1[2]);
			qlog_special("Period - " . $SDATE . " to " . $EDATE . " diff: " . ($EDATE_EPOCH-$SDATE_EPOCH));
		}
		
		$SDATE_EPOCH--;
		$EDATE_EPOCH++;

		$cust_list	= array();
		$processed = array();

		// Generate array with customer numbers

		$GLOBALS['CURFUNC'] = "Invoice::";
			
		if ($_REQUEST['whichentities'] == "All") {
			// nothin'
			qlog_special("Generating invoice over *all* entities");
		} elseif ($_REQUEST['whichentities'] == "All but inserted (not yet assigned)") {
			$LIMIT_ENTITIES = "AND owner<>'2147483647' AND assignee<>'2147483647'";
			qlog_special("Generating invoice over all entities except inserted entities");
		} elseif ($_REQUEST['whichentities'] == "All but deleted") {
			qlog_special("Generating invoice over all entities except deleted entities");
			$LIMIT_ENTITIES = "AND deleted<>'y'";
		} elseif ($_REQUEST['whichentities'] == "Only non-deleted and assigned") {
			$LIMIT_ENTITIES = " AND deleted<>'y' AND owner<>'2147483647' AND assignee<>'2147483647'";
			qlog_special("Generating invoice over all entities except inserted and deleted entities");
		}

		if ($_REQUEST['HavingStatus'] == "All") {
			// nothin'
			qlog_special("Generating invoice over *all* statusses");
		} else {
			$LIMIT_ENTITIES .= " AND status = '" . $_REQUEST['HavingStatus'] . "'";			
			qlog_special("Generating invoice over status " . $_REQUEST['HavingStatus']);
		}
		
		if ($_REQUEST['HavingPriority'] == "All") {
			// nothin'
			qlog_special("Generating invoice over *all* priorities");
		} else {
			$LIMIT_ENTITIES .= " AND priority = '" . $_REQUEST['HavingPriority'] . "'";			
			qlog_special("Generating invoice over priority " . $_REQUEST['HavingPriority']);
		}

		if ($ACTIVE=="All") {
			$sql = "SELECT DISTINCT(CRMcustomer) FROM $GLOBALS[TBL_PREFIX]entity";
		} elseif ($limit_to_customer) {
			$sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]customer WHERE id='" . $limit_to_customer ."'";
		} elseif ($ACTIVE=="Only active") {
			$sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]customer WHERE active = 'yes'";
		}

		if ($_REQUEST['SingleEntity']) {
			$sql = "SELECT CRMcustomer AS id FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='" .  $_REQUEST['SingleEntity'] . "'";
			qlog("Invoicing a single entity");
			if (strtoupper($GLOBALS['EnableSingleEntityInvoicing'])<>"YES") {
				print "<img src='error.gif'> Access denied";
				EndHTML();
				exit;
			}
		}
			
		$result = mcq($sql,$db);
		while ($row = mysql_fetch_array($result)) {
			array_push($cust_list,$row[0]);
			//qlog("Pushed $row[0]");
		}

		// Get template from database
		$sql = "SELECT content FROM $GLOBALS[TBL_PREFIX]blobs WHERE fileid='" . $_REQUEST['template'] . "'";
		$result = mcq($sql,$db);
		$result = mysql_fetch_array($result);

		$template_orig = $result[0];
		$template_array = split("\015\012",$template_orig);
		
		unset($result);

		foreach($template_array AS $line) {
			
			if (stristr($line,"@REPEAT@")) {
				$rep = true;
			}
			
			if ($rep) {
				$repeat .= $line;
			}
			if (stristr($line,"@ENDREPEAT@")) {
				$rep = false;
			}
		}

		$repeat = str_replace("@REPEAT@","",$repeat);
		$repeat = str_replace("@ENDREPEAT@","",$repeat);

		$list = GetExtraFields();

		foreach ($cust_list AS $klant) {

			$template = $template_orig;
			
			$GLOBALS['CURFUNC'] = "Invoice::";
			
			$jjl = GetCustomerName($klant);
			$GLOBALS['CURFUNC'] = "Invoice::";
			qlog_special("Processing valid customer $klant - " . $jjl);

			$GLOBALS['CURFUNC'] = "Invoice::";

			$sql = "SELECT id,custname,contact,cust_address FROM $GLOBALS[TBL_PREFIX]customer WHERE id='" . $klant . "'";
			$result = mcq($sql,$db);
			$customer = mysql_fetch_array($result);

			$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE CRMcustomer='$klant' " . $LIMIT_ENTITIES; // and date period
			

			if ($_REQUEST['SingleEntity']) {
				$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE eid=" . $_REQUEST['SingleEntity'];
			}
			
			$result = mcq($sql,$db);

			if ($GLOBALS['DateFormat'] == 'dd-mm-yyyy') {
				// European number format
				qlog_special("Using EUR format");
				$date = date('d-m-Y');
			} else {
				// American number format
				qlog_special("Using USA format");
				$date = date('m-d-Y');				
			}
	
			$eid_seen = "Invoice:: Entities concerned: ";

			$sumarray = array();

			unset($TCLVAT);
				while ($entity = mysql_fetch_array($result)) {

				$eid_seen .= $entity['eid'] . " ";

				$ddtmp = explode("-",$entity['duedate']); // dd-mm-yyyy
				$DUEDATE_EPOCH = @mktime(0,0,0,$ddtmp[1],$ddtmp[0],$ddtmp[2]);
				
				if (($DUEDATE_EPOCH>$SDATE_EPOCH && $DUEDATE_EPOCH<$EDATE_EPOCH) || $_REQUEST['SingleEntity']) {
					

					$GLOBALS['CURFUNC'] = "Invoice::";
					qlog_special(" --- entity " . $entity['eid'] . " - " . $entity['category']);
					unset($IHS);
					unset($IHC);
					unset($IHT);
					$hops++;


						$IHC_IncludingVAT = false;					
						unset($IHC);
//						unset($TOTIHS);
						unset($LVAT);

						unset($CVAT);
						unset($l_pc1);
						unset($CDD_VAT_tp);
						unset($l_VAT);


						foreach ($list AS $field) {
							//	qlog_special($list[$x]);
								$cfcount++;

								$sql = "SELECT id,value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='" . $entity['eid'] . "' AND deleted<>'y' AND name='" . $field['id'] . "' AND type<>'cust' ORDER BY name";
								$result1 = mcq($sql,$db);
								$record = mysql_fetch_array($result1);

								if ($field['fieldtype'] == "invoice cost" || $field['fieldtype'] == "invoice cost including VAT" || $field['fieldtype'] == "invoice qty" || $field['fieldtype'] == "VAT drop-down") {
																								
									if ($field['fieldtype'] == "invoice cost" && $IHC_IncludingVAT<>true) {
											$IHC = $record['value'];
											$IHC_IncludingVAT = false;
									} elseif ($field['fieldtype'] == "invoice cost including VAT" && strlen($record['value'])>0) {
											qlog_special("This value is already VAT inclusive (it overrules! -> value is " . $record['value'] . ")");
											$IHC = $record['value'];
											$IHC_IncludingVAT = true;
									}
									if ($field['fieldtype'] == "invoice qty") {
											$IHS = $record['value'];
									}
									if ($field['fieldtype'] == "VAT drop-down") {
											if (trim($record['value'])<>"" && is_numeric($record['value'])) {
												$LVAT = $record['value'];
											} else {
												qlog_special("Not using local VAT - it's not a number.");
											}
									} else {
											$LVAT = $VAT;
									}
								} elseif (is_numeric($record['value'])) {
									$sumarray[$field['id']] = $sumarray[$field['id']] + $record['value'];									
								}
									
								
						}

						$GLOBALS['CURFUNC'] = "Invoice::";
						if ($IHC) {
								if (!$IHS) {
									// if no hours/qty specified, use 1
									$IHS = 1;
									qlog_special(" ---- Defaulting qty to 1");
								}
								

								if ($IHC_IncludingVAT == true) {
									// The value was entered including VAT - we need to break this down
									$l_pc1 = $IHC/(100+$LVAT);
									qlog_special("1% of total amount including VAT is $l_pc1 (total is $IHC)");
									$IHC = $l_pc1 * 100;
									
									qlog_special(" 1% of inv.cost. is $l_pc1");
									//$l_pc1 = $IHC/100;
									$l_VAT = $l_pc1 * $LVAT;
									qlog_special(" VAT part ($LVAT %) of inv.cost. is $l_VAT");
									
								}
	
	
								qlog_special(" ------ FOUND RELEVANT DATA");
								array_push($processed,$entity['eid']);
								$tmp = ParseTemplateEntity($repeat,$entity['eid']);
								$IHT = $IHC * $IHS;
								$IHT_ev = $IHC * $IHS;
								$TOTIHS += $IHT_ev;
								
								if ($l_VAT=="") {
										$CVAT = ($IHT/100)*($LVAT);
								} else {
										$CVAT = $l_VAT;									
								}

								$TCLVAT += $CVAT;

								if ($GLOBALS['DateFormat'] == 'dd-mm-yyyy') {
									// European number format
									//qlog_special("Using EUR format");
									$IHT_tp		= number_format($IHT, 2, ',', '.'); 
									$IHC_tp		= number_format($IHC, 2, ',', '.');
									$CDD_VAT_tp	= number_format($CVAT, 2, ',', '.');
									$IHT_ev		= number_format($IHT_ev, 2, ',', '.');
								} else {
									// American number format
									qlog_special("Using USA format");
									$IHT_tp		= number_format($IHT); 
									$IHC_tp		= number_format($IHC);
									$CDD_VAT_tp	= number_format($CVAT);
									$IHT_ev		= number_format($IHT_ev);
								}
		

								$tmp = str_replace("\n","",$tmp);
								$tmp = str_replace("@IHC@",fixrtf($IHC_tp),$tmp);
								$tmp = str_replace("@IHT@",fixrtf($IHT_tp),$tmp);
								$tmp = str_replace("@LVAT@",fixrtf($LVAT . "%"),$tmp);
								$tmp = str_replace("@IHS@",fixrtf($IHS),$tmp);
								$tmp = str_replace("@CVAT@",fixrtf($CDD_VAT_tp),$tmp);
								$tmp = str_replace("@RVAT@",fixrtf($IHT_ev),$tmp);
								$tmp = str_replace("@EID@",fixrtf($entity['eid']),$tmp);
								$tmp = str_replace("@CATEGORY@","\b " . fixrtf($entity['category']). "\b0 ",$tmp);
								//$tmp = str_replace("@DATE@",fixrtf(TransformDate($entity['duedate'])),$tmp);
								$ins .= $tmp;
								$ins .= $rtf_hr;
								unset($tmp);
								$all_ok = true;

						} else {
								qlog_special(" ------ NO RELEVANT DATA");
						}
						unset($IHS);
						unset($IHC);
							
				} else {
					$GLOBALS['CURFUNC'] = "Invoice::";
					qlog_special("Entity $entity[eid] is due outside the selected period");
				}
			}

				if (!$hops) {
					qlog_special(" --- No entities for this customer found (in this period)");
				} else {
					unset($hops);
					$something = true;
				}	
				$TOTAL_COST_EX_VAT = $TOTIHS;
				$TOTAL_COST_INC_VAT = (($TOTAL_COST_EX_VAT/100)*(100+$VAT));
				
				if ($IHC_IncludingVAT == true) {
					$TOTAL_COST_VAT = (($TOTAL_COST_INC_VAT/100)*($VAT));
				} else {
					$TOTAL_COST_VAT = (($TOTAL_COST_EX_VAT/100)*($VAT));
				}

				$TOTAL_COST_EX_VAT		= round($TOTAL_COST_EX_VAT,2);
				$TOTAL_COST_INC_VAT		= round($TOTAL_COST_INC_VAT,2);
				$TOTAL_COST_VAT 		= round($TOTAL_COST_VAT,2);

				$TOTAL_COST_INC_LVAT    = round(($TOTIHS + $TCLVAT),2);
				
				if ($GLOBALS['DateFormat'] == 'dd-mm-yyyy') {
					// European number format
					//qlog_special("Using EUR format");
					$TOTAL_COST_EX_VAT		= number_format($TOTAL_COST_EX_VAT, 2, ',', '.'); 
					$TOTAL_COST_INC_VAT		= number_format($TOTAL_COST_INC_VAT, 2, ',', '.');
					$TOTAL_COST_VAT 		= number_format($TOTAL_COST_VAT, 2, ',', '.');    
					$TOTAL_COST_INC_LVAT	= number_format($TOTAL_COST_INC_LVAT, 2, ',', '.');    
					$TCLVAT					= number_format($TCLVAT, 2, ',', '.');    
				} else {
					// American number format
					qlog_special("Using USA format");
					$TOTAL_COST_EX_VAT		= number_format($TOTAL_COST_EX_VAT); 
					$TOTAL_COST_INC_VAT		= number_format($TOTAL_COST_INC_VAT);
					$TOTAL_COST_VAT 		= number_format($TOTAL_COST_VAT);    
					$TOTAL_COST_INC_LVAT	= number_format($TOTAL_COST_INC_LVAT);    
					$TCLVAT					= number_format($TCLVAT);    
				}
		

				$IV_DATE = TransformDate(date("d-m-Y"));
				
				// Strip the RTF header:
				$template = str_replace("{\\rtf1","",$template);
			
				if ($all_ok) {
					// set start invoice number 
					$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='NextInvoiceNumberCounter'";
					$result = mcq($sql,$db);
					$result = mysql_fetch_array($result);
					$INV_NUM = $result[0];

					$GLOBALS['NextInvoiceNumberCounter']++;
					$sql = "UPDATE $GLOBALS[TBL_PREFIX]settings SET value='" . $GLOBALS['NextInvoiceNumberCounter'] . "' WHERE setting='NextInvoiceNumberCounter'";
					mcq($sql,$db);
					$INV_NUM_STR = filloutnum("$INV_NUM",6);

					if ($GLOBALS['InvoiceNumberPrefix']) {
						$INV_NUM_STR = $GLOBALS['InvoiceNumberPrefix'] . $INV_NUM_STR;
					} else {
						$INV_NUM_STR = $INV_NUM;
					}
					qlog_special("This is invoice number " . $INV_NUM_STR);
				}

				$template = ereg_replace("@INVOICE_DATE@",fixrtf($IV_DATE),$template);
				$template = ereg_replace("@NUM@", fixrtf($INV_NUM_STR),$template);
				$template = ereg_replace("@TOTAL_COST_EX_VAT@",fixrtf($TOTAL_COST_EX_VAT),$template);
				$template = ereg_replace("@TOTAL_COST_INC_VAT@",fixrtf($TOTAL_COST_INC_VAT),$template);
				$template = ereg_replace("@TOTAL_COST_INC_LVAT@",fixrtf($TOTAL_COST_INC_LVAT),$template);
				$template = ereg_replace("@TOTAL_COST_LVAT@",fixrtf($TCLVAT),$template);
				$template = ereg_replace("@TOTAL_COST_VAT@",fixrtf($TOTAL_COST_VAT),$template);
				$template = ereg_replace("@VAT@",fixrtf($VAT),$template);

				$slist = GetExtraFields();
				
				foreach ($slist AS $field) {
					if ($sumarray[$field['id']]) {
						$template = ereg_replace("@SUMEFID" . $field['id'] . "@",fixrtf($sumarray[$field['id']]),$template);
					}
				}

				// Make sure these things are logged:
				
				$add_to_journal = "Invoice:: Invoice " . $INV_NUM_STR . " was generated for this customer.\n";
				$add_to_journal .= "Invoice:: over period: " . $_REQUEST['sdate'] . " to " . $_REQUEST['edate'] . "\n";
				$add_to_journal .= "Invoice:: total invoiced (ex VAT): " . $TOTAL_COST_EX_VAT . "\n";
				$add_to_journal .= $eid_seen . "\n";
				
				// strip the last }

				$template = substr($template,0,strlen($template)-1);
				
				$template = ParseTemplateCustomer($template,$klant);

				if ($_REQUEST['SingleEntity']) {
					$template = ParseTemplateEntity($template, $_REQUEST['SingleEntity']);
				}

				$template = ParseTemplateGeneric($template);

				// qlog_special($template);

				$template1 = split("@REPEAT@",$template);
				$template2 = split("@ENDREPEAT@",$template);

				$template = $template1[0] . $ins . $template2[1];

				unset($ins);
				
				if ($all_ok) { // only if there was someting to invoice
					$template = ParseTemplateForRTF($template);
					$template = "{\\rtf1" . $template . "}";
					if ($attach_to_customer) {
						$filename = "CRM-CTT-invoice-" . $INV_NUM_STR . "-" . date("Fj-Y-Hi") . "h.rtf";
				//		$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,filetype,username,type) VALUES ('" . $k//lant . "','" . addslashes($template) . "','" . $filename . "','" . strlen($template) . "','RTF Invoice document','" . $GLO//BALS['USERNAME'] . "','cust')";
					//	$result = mcq($sql,$db);
						AttachFile($klant, $filename, $template, "cust", "RTF Invoice document");
						qlog_special("Attached invoice " . $filename . " to customer " . $klant);
						$add_to_journal .= "Attached invoice " . $filename . "\n";
					} 
					if ($_REQUEST['attach_to_entity'] && $_REQUEST['SingleEntity']) {
						$filename = "CRM-CTT-invoice-" . $INV_NUM_STR . "-" . date("Fj-Y-Hi") . "h.rtf";
						//$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,filetype,username,type) VALUES ('" . $_REQUEST['SingleEntity'] . "','" . addslashes($template) . "','" . $filename . "','" . strlen($template) . "','RTF Invoice document','" . $GLOBALS['USERNAME'] . "','entity')";
						//$result = mcq($sql,$db);
						AttachFile($_REQUEST['SingleEntity'], $filename, $template, "entity", "RTF Invoice document");
						qlog_special("Attached invoice " . $filename . " to entity " . $_REQUEST['SingleEntity']);
						journal($_REQUEST['SingleEntity'],"Attached invoice " . $filename);
					} 

					if ($not_1st) {
						$total .= "\page " . $template;
					} else {
						$total .= $template;
						$not_1st = true;
					}
					unset($all_ok);
				}

				
				// Journal all events
				journal($klant, $add_to_journal,"customer");


				unset($template);
				unset($TOTAL_COST_EX_VAT);
				unset($TOTIHS);
				unset($TOTAL_COST_INC_VAT);
				unset($TOTAL_COST_VAT);
				unset($IV_DATE);
		


		}
		if ($something) {
			if ($ADDLOG) {
				$total .= "\page INCLUDING LOG AT REQUEST:\par \par " . $log_to_rtf;
				qlog("Adding logfile to RTF output");
			}

			foreach ($processed as $eid) {
					journal($eid,"This entity was used for generating an invoice");
					$sqlx .= "eid='" . $eid . "' OR ";
			}
			
			if ($_REQUEST['DelAfterProcess']=="1") {
				$closeepoch = date('U');
				$sqlx = "UPDATE $GLOBALS[TBL_PREFIX]entity SET deleted='y', closeepoch='" . $closeepoch . "' WHERE ";
				foreach ($processed as $eid) {
					journal($eid,"Entity was deleted after generating invoice");
					$sqlx .= "eid='" . $eid . "' OR ";
					$tel++;
				}
				$sqlx .= "1=0";
				mcq($sqlx,$db);
				uselogger ("$tel entities were deleted after generating invoices",""); 

			}
			if ($_REQUEST['SetStatusTo']<>"All") {
				$sqlx = "UPDATE $GLOBALS[TBL_PREFIX]entity SET status='" . $_REQUEST['SetStatusTo'] . "' WHERE ";
				foreach ($processed as $eid) {
					journal($eid,"Status set to " . $_REQUEST['SetStatusTo'] . " after generating invoice");
					$sqlx .= "eid='" . $eid . "' OR ";
					$tel++;
				}
				$sqlx .= "1=0";
				mcq($sqlx,$db);
				uselogger ("$tel entities got status='" . $_REQUEST['SetStatusTo'] . "' after generating invoices",""); 
			}
			if ($_REQUEST['SetReadonlyTo']<>"All") {
				$sqlx = "UPDATE $GLOBALS[TBL_PREFIX]entity SET readonly='y' WHERE ";
				foreach ($processed as $eid) {
					journal($eid,"Readonly set after generating invoice");
					$sqlx .= "eid='" . $eid . "' OR ";
					$tel++;
				}
				$sqlx .= "1=0";
				mcq($sqlx,$db);
				uselogger ("$tel entities got a readonly flag after generating invoices",""); 
			}
			if ($_REQUEST['SetOwnerTo']<>"All") {
				$sqlx = "UPDATE $GLOBALS[TBL_PREFIX]entity SET owner='" . $_REQUEST['SetOwnerTo'] . "' WHERE ";
				foreach ($processed as $eid) {
					journal($eid,"Owner set to " . GetUserName($_REQUEST['SetOwnerTo']) . " after generating invoice");
					qlog_special	 ("Owner of $eid set to " . GetUserName($_REQUEST['SetOwnerTo']) . " after generating invoice"); 
					$sqlx .= "eid='" . $eid . "' OR ";
					$tel++;
				}
				$sqlx .= "1=0";
				mcq($sqlx,$db);
			}
			if ($_REQUEST['SetAssigneeTo']<>"All") {
				$sqlx = "UPDATE $GLOBALS[TBL_PREFIX]entity SET assignee='" . $_REQUEST['SetAssigneeTo'] . "' WHERE ";
				foreach ($processed as $eid) {
					journal($eid,"Assignee set to " . GetUserName($_REQUEST['SetAssigneeTo']) . " after generating invoice");
					qlog_special("Assignee of $eid set to " . GetUserName($_REQUEST['SetAssigneeTo']) . " after generating invoice");
					$sqlx .= "eid='" . $eid . "' OR ";
					$tel++;
				}
				$sqlx .= "1=0";
				mcq($sqlx,$db);
			}
			$filename = "CRM-CTT-invoices-" . date("Fj-Y-Hi") . "h.rtf";

			if ($COMMAND_LINE) {
				$fp = fopen($path . $filename,"w");
				$f = "{\\rtf1";
				$f .= $total;
				$f .= "}";
				fputs($fp,$f);
				fclose($fp);
				print "File " . $path . $filename . " saved.\n";
			} else {
				uselogger("Invoice file generated and presented","");
				header("Content-Type: RTF");
				header("Content-Disposition: attachment; filename=" . $filename);
				header("Content-Description: CRM Generated Data" );
				header("Window-target: _top");
				
				// output file
				
				print "{\\rtf1";
				print $total;
				print "}";
			}
		} else {
			if ($COMMAND_LINE) {
				print "Empty result set (for this period)\n\n";
			} else {

				?>
					<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
					<!--
						alert('Empty result set (for this period)');
						document.location='invoice.php';
					//-->
					</SCRIPT>
				<?
			}
		} 
	} // end if not mailmerge
}

function fixrtf($rtf)
{
        $rtf = ereg_replace("[\\]","\\\\", $rtf); # vertaald \ naar \\
        $rtf = ereg_replace("{","\\{", $rtf);     # vertaald { naar \}
        $rtf = ereg_replace("}","\\}", $rtf);     # vertaald } naar \}
		$rtf = ereg_replace("\n","\par ", $rtf);  # vertaald } naar \}
        return $rtf;
}

function qlog_special($msg) {
	global $ADDLOG, $log_to_rtf;
	qlog($msg);
	if ($ADDLOG) {
		
		$log_to_rtf .= date('U') . " Invoice::" . $msg . "\par ";
	}
}
function PrintMMForm($alter_target = false) {
	global $lang;
	print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Mail merge&nbsp;</legend>";
	if ($alter_target) {
		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
		function doso() {
			
			window.opener.document.location="invoice.php?whichcust=selected&template=" + document.mailmerge.template.value + "&attach_to_dossier=" + document.mailmerge.attach_to_dossier.value + "&stashid=<? echo $_REQUEST['stashid'];?>&defVAT=selected&edate=selected&sdate=selected&mailmerge=selected=";
			window.close();

		}
		//-->
		</SCRIPT>
		<?
		print "<form name='mailmerge' METHOD='POST' target='document.window.opener'>";

	} else {
		print "<form name='mailmerge' METHOD='POST'>";
	}

	print "<table width='100%' border=0>";
	//print "<tr><td width='250'>" . $lang['customers'] . ":</td><td>";
	
	/*if ($_REQUEST['whichcust']=="bla") {
			print "<select style='width:250' name=selectedcustomer size='1'>";
					$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]customer ORDER BY custname";
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

		print "<input type='hidden' name='whichcust' value='selected'>";
	} else {
		print "<select style='width:250' name='whichcust'><option value='All'>" . $lang['all'] . "</option><option value='Only active'>$lang[onlyactive]</option><option value=bla>$lang[selectsingle]</option></select>";
	}
	
	print "</td></tr>";
	*/
	print "<tr><td>$lang[attachindividualtocustomer]:</td><td><select style='width:250' name='attach_to_dossier'><option value='Yes'>$lang[yes]</option><option>$lang[no]</option></select></td></tr>";
	print "<input type='hidden' name='defVAT' value='selected'>";
	
	print "<input type='hidden' name='edate' value='selected'>";
	print "<input type='hidden' name='sdate' value='selected'>";
	print "<input type='hidden' name='mailmerge' value='selected'>";
	print "<tr><td>RTF Template:</td><td><select style='width:250' name='template'>";
	$sql = "SELECT fileid,filename,creation_date,username FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='0' AND filetype='TEMPLATE_MAILMERGE'";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		print "<option value = '" . $row['fileid'] ."'>" . $row['filename'] . "</option>";
	}
	print "</select>";
	
	print "<tr><td>";
	if ($alter_target) {
		print "<input type='button' value='$lang[go]' Onclick='doso();'>";
	} else {
		print "<input type='submit' value='$lang[go]'>";
	}
	print "</form></td></tr></table>";
	print "</fieldset>";
}
?>
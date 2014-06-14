<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This script generates RTF entity reports (NOT Reports!)
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
extract($_REQUEST);
$start_time = date('U');
//print_r($_REQUEST);
if ($_REQUEST['template'] && $_REQUEST['attach_to_dossier'] && $_REQUEST['attach_to_entity'] && $_REQUEST['SingleEntity']) {
		include("config.inc.php");
		include("getset.php");	
		if (strtoupper($GLOBALS['EnableEntityReporting'])<>"YES") {
			print "<img src='error.gif'> Access denied";
			EndHTML();
			exit;
		}

		if (CheckEntityAccess($_REQUEST['SingleEntity'])<>"ok" && CheckEntityAccess($_REQUEST['SingleEntity'])<>"readonly") { 
			print "<img src='error.gif'>&nbsp;Private entity"; 
			EndHTML();
			exit;
		}

		// Determine customer
		$sql = "SELECT CRMcustomer FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='" . $_REQUEST['SingleEntity'] . "'";
		$result = mcq($sql,$db);
		$result = mysql_fetch_array($result);
		$customer = $result['CRMcustomer'];
	
		// Get template from database
		qlog("Retrieving template " . $_REQUEST['template'] . " from database");
		$sql = "SELECT content FROM $GLOBALS[TBL_PREFIX]blobs WHERE fileid='" . $_REQUEST['template'] . "'";
		$result = mcq($sql,$db);
		$result = mysql_fetch_array($result);
		$template = $result['content'];

		$template = ParseTemplateEntity    ($template,$_REQUEST['SingleEntity']);
		$template = ParseTemplateCustomer  ($template,$customer);
		$template = ParseTemplateGeneric   ($template);
		$template = ParseTemplateForRTF($template);
		$filename = "CRM-CTT-report-" . date("Fj-Y-Hi") . "h.rtf";

		if ($attach_to_customer) {
			$x = AttachFile($customer,$filename,$template,"cust","RTF report document");
					} 

		if ($_REQUEST['attach_to_entity'] == "Yes" && $_REQUEST['SingleEntity']) {
			$x = AttachFile($_REQUEST['SingleEntity'],$filename,$template,"entity","RTF report document");
		} 
		//$template = addslashes($template);
		header("Content-Type: RTF");
		header("Content-Disposition: attachment; filename=" . $filename);
		header("Content-Description: CRM Generated Data" );
		header("Window-target: _top");
		
		// output file
		
//		print "{\\rtf1";
		print $template;
//		print "}";


} elseif($_REQUEST['SingleEntity']) {
	$nonavbar = true;
	require("header.inc.php");
		if (strtoupper($GLOBALS['EnableEntityReporting'])<>"YES") {
			print "<img src='error.gif'> Access denied";
			EndHTML();
			exit;
		}
		if (CheckEntityAccess($_REQUEST['SingleEntity']) <> "ok" && CheckEntityAccess($_REQUEST['SingleEntity'])<>"readonly") { 
			print "<img src='error.gif'>&nbsp;Private entity"; 
			EndHTML();
			exit;
		}

	print "<table><tr><td><form name='SingleReport' method='POST' target='document.window.opener'>";
	print "<input type='hidden' name='SingleEntity' value='" . $_REQUEST['SingleEntity'] . "'>";
	print "<input type='hidden' name='edate' value='01-01-8000'>";
	print "<input type='hidden' name='sdate' value='01-01-2300'>";
	print "<input type='hidden' name='whichcust' value='" . $_REQUEST['SingleEntity'] . "'>";
	print "<table>";
	print "<tr><td><b>" . $lang['createreport'] . " " . $_REQUEST['SingleEntity'] . "</b><br><br></td></tr>";
	print "<tr><td>" . $lang['rtftemplate'] . ":</td><td><select style='width:250' name='template'>";
	$sql = "SELECT fileid,filename,creation_date,username FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='0' AND filetype='TEMPLATE_REPORT' ORDER BY filename";
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
	//print "<tr><td>" . $lang['includelog'] . ":</td><td><select style='width:250' name='includelog'><option value='0'>$lang[no]</option><option>$lang[yes]</option></select></td></tr>";
	print "<tr><td>" . $lang['attachindividualtocustomer'] . ":</td><td><select style='width:250' name='attach_to_dossier'><option value='Yes'>$lang[yes]</option><option SELECTED value='No'>$lang[no]</option></select></td></tr>";
	print "<tr><td>" . $lang['attachindividualtoentity'] . " ". $_REQUEST['SingleEntity'] . ":</td><td><select style='width:250' name='attach_to_entity'><option value='No'>$lang[no]</option><option value='Yes'>$lang[yes]</option></select></td></tr>";
	print "<tr><td><input type='button' name='submitknop' value='$lang[go]' OnClick='doso();'></form></td></tr></table>";

		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
		function doso() {
			
			window.opener.document.location="entityreport.php?SingleEntity=" + document.SingleReport.SingleEntity.value + "&whichcust=t&template=" + document.SingleReport.template.value + "&attach_to_dossier=" + document.SingleReport.attach_to_dossier.value + "&attach_to_entity=" + document.SingleReport.attach_to_entity.value;

			window.close();

		}
		//-->
		</SCRIPT>
		<?
	EndHTML();
} elseif ($_REQUEST['BatchReport'] && $_REQUEST['whichcust']<>"bla") {
		//require("header.inc.php");
		include("config.inc.php");
		include("getset.php");	
		$cust_list	= array();
		$processed = array();
		

		if (strtoupper($GLOBALS['EnableEntityReporting'])<>"YES") {
			print "<img src='error.gif'> Access denied";
			EndHTML();
			exit;
		}


		$td1 = explode("-",$_REQUEST['sdate']); // dd-mm-yyyy
		$td2 = explode("-",$_REQUEST['edate']); // dd-mm-yyyy
		
		if ($td1 && $td2) {
			$SDATE = "$td1[2]-$td1[1]-$td1[0]";
			$EDATE = "$td2[2]-$td2[1]-$td2[0]";

			$SDATE_EPOCH = @mktime(0,0,0,$td1[1],$td1[0],$td1[2]);
			$EDATE_EPOCH = @mktime(0,0,0,$td2[1],$td2[0],$td2[2]);

			$GLOBALS['CURFUNC'] = "Report::";	
			qlog("Period - " . $SDATE . " to " . $EDATE . " diff: " . ($EDATE_EPOCH-$SDATE_EPOCH));
			
			if (($EDATE_EPOCH-$SDATE_EPOCH)<0) {
				// period is negative, swap them, help the user :)
				$tmp = $SDATE;
				$SDATE = $EDATE;
				$EDATE = $tmp;
				qlog("Negative period diff - dates swapped");
				$SDATE_EPOCH = @mktime(0,0,0,$td2[1],$td2[0],$td2[2]);
				$EDATE_EPOCH = @mktime(0,0,0,$td1[1],$td1[0],$td1[2]);
				qlog("Period - " . $SDATE . " to " . $EDATE . " diff: " . ($EDATE_EPOCH-$SDATE_EPOCH));
			}

			$SDATE_EPOCH--;
			$EDATE_EPOCH++;
		}
		$GLOBALS['CURFUNC'] = "Report::";
		if (!$_REQUEST['stashid']) {
			if ($_REQUEST['whichentities'] == "All") {
				// nothin'
				qlog("Generating report over *all* entities");
			} elseif ($_REQUEST['whichentities'] == "All but inserted (not yet assigned)") {
				$LIMIT_ENTITIES = "AND owner<>'2147483647' AND assignee<>'2147483647'";
				qlog("Generating report over all entities except inserted entities");
			} elseif ($_REQUEST['whichentities'] == "All but deleted") {
				qlog("Generating report over all entities except deleted entities");
				$LIMIT_ENTITIES = "AND deleted<>'y'";
			} elseif ($_REQUEST['whichentities'] == "Only non-deleted and assigned") {
				$LIMIT_ENTITIES = " AND deleted<>'y' AND owner<>'2147483647' AND assignee<>'2147483647'";
				qlog("Generating report over all entities except inserted and deleted entities");
			}

			if ($_REQUEST['HavingStatus'] == "All") {
				// nothin'
				qlog("Generating report over *all* statusses");
			} else {
				$LIMIT_ENTITIES .= " AND status = '" . $_REQUEST['HavingStatus'] . "'";			
				qlog("Generating report over status " . $_REQUEST['HavingStatus']);
			}
			
			if ($_REQUEST['HavingPriority'] == "All") {
				// nothin'
				qlog("Generating report over *all* priorities");
			} else {
				$LIMIT_ENTITIES .= " AND priority = '" . $_REQUEST['HavingPriority'] . "'";			
				qlog("Generating report over priority " . $_REQUEST['HavingPriority']);
			}

		

			if ($_REQUEST['whichcust'] == "Only active") {
				
				$LIMIT_ENTITIES .= " AND $GLOBALS[TBL_PREFIX]customer.active<>'no'";
				
			} elseif ($_REQUEST['whichcust'] == "All") {

				// no limit

			} elseif (is_numeric($_REQUEST['whichcust'])) {
				
				$LIMIT_ENTITIES .= " AND $GLOBALS[TBL_PREFIX]entity.CRMcustomer='" . $_REQUEST['whichcust'] . "'";

			}
			
			$base_query = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity,$GLOBALS[TBL_PREFIX]customer WHERE $GLOBALS[TBL_PREFIX]entity.CRMcustomer = $GLOBALS[TBL_PREFIX]customer.id " . $LIMIT_ENTITIES;
		} else {
			// yeah cool
			$base_query = PopStashValue($_REQUEST['stashid']);

		}
		$processed = array();

		// Get template from database
		qlog("Retrieving template " . $_REQUEST['template'] . " from database");
		$sql = "SELECT filename,content FROM $GLOBALS[TBL_PREFIX]binfiles,$GLOBALS[TBL_PREFIX]blobs WHERE $GLOBALS[TBL_PREFIX]binfiles.fileid=$GLOBALS[TBL_PREFIX]blobs.fileid AND $GLOBALS[TBL_PREFIX]binfiles.fileid='" . $_REQUEST['template'] . "' AND filetype='TEMPLATE_REPORT'";
		$result = mcq($sql,$db);
		$result = mysql_fetch_array($result);
		$template = $result['content'];
		$template_name = $result['filename'];
		// Strip the RTF header
		$template = str_replace("{\\rtf1","",$template);
		// strip the last } (RTF footer)
		$orig_template = substr($template,0,strlen($template)-1);

		$result_rp = mcq($base_query,$db);
		$filename = "CRM-CTT-" . eregi_replace(".rtf","",$template_name) . "-" . date("Fj-Y-Hi") . "h.rtf";

		header("Content-Type: RTF");
		header("Content-Disposition: attachment; filename=" . $filename);
		header("Content-Description: CRM Generated Data" );
		header("Window-target: _top");
		ob_end_flush();
		flush();
		qlog("Send RTF download header to browser");
		// Start to output file (limits memory usage)
		
		print "{\\rtf1";

		while ($row=mysql_fetch_array($result_rp)) {
			if (CheckEntityAccess($row['eid'])=="ok" || CheckEntityAccess($row['eid'])=="readonly") { 
				
				if ($SDATE_EPOCH && $EDATE_EPOCH) {
					$ddtmp = explode("-",$row['duedate']); // dd-mm-yyyy
					$DUEDATE_EPOCH = @mktime(0,0,0,$ddtmp[1],$ddtmp[0],$ddtmp[2]);
					if (($DUEDATE_EPOCH>$SDATE_EPOCH && $DUEDATE_EPOCH<$EDATE_EPOCH)) {
						// all ok
						$donotinclude = false;
					} else {
						$donotinclude = true;
					}
				}
				
				if (!$donotinclude) {
					
					$template = $orig_template;

					
					journal($row['eid'],"A report (" . $filename . ") was created based on this entity","entity");

					$template = ParseTemplateEntity    ($template,$row['eid']);
					$template = ParseTemplateCustomer  ($template,$row['CRMcustomer']);
					$template = ParseTemplateGeneric   ($template);
				
					$template = ParseTemplateForRTF    ($template);

					array_push($processed,$row['eid']);				

					if ($attach_to_customer) {
									//$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,filetype,username,type) VALUES ('" . $customer . "','{\\rtf1" . addslashes($template) . "}','" . $filename . "','" . strlen($template) . "','RTF report document','" . $GLOBALS['USERNAME'] . "','cust')";
									//$result = mcq($sql,$db);
									//qlog("Attached report " . $filename . " to customer " . $klant);
									//$add_to_journal .= "Attached report " . $filename . "\n";
									
									$x = AttachFile($customer,$filename,"{\\rtf1" . $template . "}","cust","RTF report document");
								} 

					if ($_REQUEST['attach_to_entity'] == "Yes") {
							//		$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,filetype,username,type) VALUES ('" . $_REQUEST['SingleEntity'] . "','{\\rtf1" . addslashes($template) . "}','" . $filename . "','" . strlen($template) . "','RTF report document','" . $GLOBALS['USERNAME'] . "','entity')";
							//		$result = mcq($sql,$db);
							//		qlog("Attached report " . $filename . " to entity " . $_REQUEST['SingleEntity']);
							//		journal($_REQUEST['SingleEntity'],"Attached report " . $filename);

									$x = AttachFile($row['eid'],$filename,"{\\rtf1" . $template . "}","entity","RTF report document");
					} 
					
					if ($not_1st) {
						print "\page " . $template;
						$totsize += strlen($template);
						ob_end_flush();
						flush();
					} else {
						print $template;
						$totsize += strlen($template);
						ob_end_flush();
						flush();
						$not_1st = true;
					}
				} // end if !$donotinclude

			} else {
				qlog("Access to entity " . $row['eid'] . " denied.");
			}
		}	

	
		print "}";	
		

		foreach ($processed as $eid) {
				journal($eid,"This entity was used for generating a report");
				$sqlx .= "eid='" . $eid . "' OR ";
		}
		if (GetClearanceLevel($GLOBALS['USERID']) == "administrator") {
		
			if ($_REQUEST['DelAfterProcess']=="1") {
				$closeepoch = date('U');
				$sqlx = "UPDATE $GLOBALS[TBL_PREFIX]entity SET deleted='y', closeepoch='" . $closeepoch . "' WHERE ";
				foreach ($processed as $eid) {
					journal($eid,"Entity was deleted after generating report");
					$sqlx .= "eid='" . $eid . "' OR ";
					$tel++;
				}
				$sqlx .= "1=0";
				mcq($sqlx,$db);
				uselogger ("$tel entities were deleted after generating reports",""); 
				qlog("$tel entities were deleted after generating reports"); 

			}
			if ($_REQUEST['SetStatusTo']<>"All" && $_REQUEST['SetStatusTo']<>"") {
				$sqlx = "UPDATE $GLOBALS[TBL_PREFIX]entity SET status='" . $_REQUEST['SetStatusTo'] . "' WHERE ";
				foreach ($processed as $eid) {
					journal($eid,"Status set to " . $_REQUEST['SetStatusTo'] . " after generating report");
					$sqlx .= "eid='" . $eid . "' OR ";
					$tel++;
				}
				$sqlx .= "1=0";
				mcq($sqlx,$db);
				uselogger ("$tel entities got status='" . $_REQUEST['SetStatusTo'] . "' after generating reports",""); 
				qlog("$tel entities got status='" . $_REQUEST['SetStatusTo'] . "' after generating reports"); 
			}
			if ($_REQUEST['SetReadonlyTo']<>"All" && $_REQUEST['SetReadonlyTo']<>"") {
				$sqlx = "UPDATE $GLOBALS[TBL_PREFIX]entity SET readonly='y' WHERE ";
				foreach ($processed as $eid) {
					journal($eid,"Readonly set after generating report");
					$sqlx .= "eid='" . $eid . "' OR ";
					$tel++;
				}
				$sqlx .= "1=0";
				mcq($sqlx,$db);
				uselogger ("$tel entities got a readonly flag after generating reports",""); 
				qlog("$tel entities got a readonly flag after generating reports"); 			

			}
			if ($_REQUEST['SetOwnerTo']<>"All" && $_REQUEST['SetOwnerTo']<>"") {
				$sqlx = "UPDATE $GLOBALS[TBL_PREFIX]entity SET owner='" . $_REQUEST['SetOwnerTo'] . "' WHERE ";
				foreach ($processed as $eid) {
					journal($eid,"Owner set to " . GetUserName($_REQUEST['SetOwnerTo']) . " after generating report");
					qlog	 ("Owner of $eid set to " . GetUserName($_REQUEST['SetOwnerTo']) . " after generating report"); 
					$sqlx .= "eid='" . $eid . "' OR ";
					$tel++;
				}
				$sqlx .= "1=0";
				mcq($sqlx,$db);
			}
			if ($_REQUEST['SetAssigneeTo']<>"All" && $_REQUEST['SetAssigneeTo']<>"") {
				$sqlx = "UPDATE $GLOBALS[TBL_PREFIX]entity SET assignee='" . $_REQUEST['SetAssigneeTo'] . "' WHERE ";
				foreach ($processed as $eid) {
					journal($eid,"Assignee set to " . GetUserName($_REQUEST['SetAssigneeTo']) . " after generating report");
					qlog("Assignee of $eid set to " . GetUserName($_REQUEST['SetAssigneeTo']) . " after generating report");
					$sqlx .= "eid='" . $eid . "' OR ";
					$tel++;
				}
				$sqlx .= "1=0";
				mcq($sqlx,$db);
			}
		} // end if user is admin
		else {
			qlog("Skipping update parse, this user is not an admin!");
		}
		// push actual file

		
		$GLOBALS['CURFUNC'] = "Report::";
		$end_time = date('U') - $start_time;
		qlog("RTF Report presented. (took " . $end_time . " seconds)");		
		$GLOBALS['CURFUNC'] = "Report::";
		qlog("Total file size of this report is " . $totsize . " bytes");

		if ($totsize > 10367084) {
 		    $GLOBALS['CURFUNC'] = "Report::";
			$totsizeMB = round(($totsize/1024)/1024,2);
			qlog("WARNING - A requested report was larger than 10MB - decrease your template size! It's " . $totsizeMB ."MB! (" . $template_name . ")");
			uselogger("WARNING - A requested report was larger than 10MB - decrease your template size! It's " . $totsizeMB ." MB, which took ". $end_time . " seconds to create. (" . $template_name . ")","");
		}
		EndHTML(false);

} else {
	require("header.inc.php");
	if ($_REQUEST['closepopup']) {
		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
			statusWin = window.open('summary.php?wait=1', 'statusWin','width=200,height=70,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
			statusWin.close();
		//-->
		</SCRIPT>
		
		<?
	}

	if (strtoupper($GLOBALS['EnableEntityReporting'])<>"YES") {
			print "<img src='error.gif'> Access denied";
			EndHTML();
			exit;
	}
	print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[createreports]&nbsp;</legend>";

	print "<form name='editform' METHOD='POST'><input type='hidden' name='BatchReport' value='1'><table width='100%'>";
	if (!$_REQUEST['stashid']) { 

			print "<tr><td width='250'>$lang[startdate] ($lang[lefae]):</td><td><input type='text' name='sdate' size='10' value='" . $_REQUEST['sdate'] . "' OnClick=\"javascript:popcalendarSelectEFCustomer('editform.sdate',0);\" OnKeyUp=\"javascript:popcalendarSelectEFCustomer('editform.sdate',0);\"></td></tr>";
			
			print "<tr><td>$lang[enddate] ($lang[lefae]):</td><td><input size='10' type='text' name='edate' value='" . $_REQUEST['edate'] . "' OnClick=\"javascript:popcalendarSelectEFCustomer('editform.edate',0);\" OnKeyUp=\"javascript:popcalendarSelectEFCustomer('editform.date',0);\"></td></tr>";

			
			print "<tr><td>" . $lang['customers'] . ":</td><td>";
			
			if ($_REQUEST['whichcust']=="bla") {
					print "<select name='whichcust' size='1'>";
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
			print "<option value='All except deleted'>$lang[alled]</option>";
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
		
	} else {
		print "<tr><td>" . $lang['alreadyselected'] . "<input type='hidden' name='stashid' value='" . $_REQUEST['stashid'] . "'></td></tr>";
	}
	if (GetClearanceLevel($GLOBALS['USERID']) == "administrator") {
		print "<tr><td colspan=3><hr><br>Admin:</td></tr>";
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
	} // end if only admin


	print "<tr><td>$lang[rtftemplate]:</td><td><select style='width:250' name='template'>";
	$sql = "SELECT fileid,filename,creation_date,username FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='0' AND filetype='TEMPLATE_REPORT'";
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
	print "<tr><td>" . $lang['attachindividualtoentity'] . " ". $_REQUEST['SingleEntity'] . ":</td><td><select style='width:250' name='attach_to_entity'><option value='No'>$lang[no]</option><option value='Yes'>$lang[yes]</option></select></td></tr>";
	print "<tr><td>$lang[attachindividualtocustomer]:</td><td><select style='width:250' name='attach_to_dossier'><option>$lang[no]</option><option value='Yes'>$lang[yes]</option></select></td></tr>";
	print "<tr><td><input type='submit' name='submitknop' value='$lang[go]'></form></td></tr></table>";

	
	print "</fieldset><br><br>";

	EndHTML();
}
?>
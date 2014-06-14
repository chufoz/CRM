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

// echo $QUERY_STRING;

if ($csv=="PDF") {
//		print "adjusted";
		$toptab = "1";
}

if ($_REQUEST['SaveSearch']) {
	$nonavbar=1;
	include("header.inc.php");
	if ($_REQUEST['DeleteSearch']) {
		DeleteSavedSearch(base64_decode($_REQUEST['DeleteSearch']));
	}
	if ($_REQUEST['SaveSearch'] && $_REQUEST['SaveSearchName']) {
		$msg = SaveASearch(PopStashValue($_REQUEST['SaveSearch']),$_REQUEST['SaveSearchName']);
		print $msg;
	}
	print "<table width='100%'><tr><td>&nbsp;&nbsp;</td><td><br>&nbsp;&nbsp;&nbsp;<table width='80%'><tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Add last (submitted) search to bookmarks&nbsp;</legend><form name='ssform' method='GET'>";
	print $lang['name'] . ": <input type='text' name='SaveSearchName'>&nbsp;&nbsp;&nbsp;<input type='hidden' name='SaveSearch' value='" . $_REQUEST['SaveSearch'] . "'>";
	print "<input type='submit' name='" . $lang['savetodb'] . "'></form><br>&nbsp;</fieldset></td></tr>";
	print "<tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Current bookmarks&nbsp;</legend>";
	$ss = GetSavedSearches();
		if (is_array($ss)) {
			foreach ($ss as $key => $element) {
				print "<br><a href='summary.php?SaveSearch=" . $_REQUEST['SaveSearch'] . "&DeleteSearch=" . base64_encode($key) . "'><img style='border: 0' src='delete.gif'></a> &nbsp;" . $key . " ";
				$num++;
			}
		}
	if (!$num) {
		print "No bookmarks defined.";
	}
	print "</fieldset></td></tr>";
	print "</table></td></tr></table>";
	EndHTML();
	exit;
}


if (!$pushcsv) {
	include("header.inc.php");
	CheckPageAccess("summary");
} else {
	include("config.inc.php");
}

if ($_REQUEST['dsp_overrule']) {
	$csv = $_REQUEST['dsp_overrule'];
	qlog("Re-setting CSV - dsp_overrule exists, its " . $_REQUEST['dsp_overrule']);
}

if ($csv=="CSV") {
	$pushcsv=1;
} elseif ($csv=="PDF") {
	$pushpdf=1;
} elseif ($csv=="short") {
	$brief=1;
} elseif ($csv=="onscreenfull") {
	$includecontents=1;
}

uselogger("Summary page viewed","");

$return_to_list = "____b64-" . base64_encode($_SERVER['REQUEST_URI']);	

$return_to_list = "____STASH-" . PushStashValue($_SERVER['REQUEST_URI']);

if ($wildsearch) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
		statusWin = window.open('summary.php?wait=1', 'statusWin' ,'width=400,height=135,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
	//-->
	</SCRIPT>
	
	<?
}

?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
		function SearchStatusWin() {
			if (document.summ.wildsearch.value!='') {
				statusWin = window.open('summary.php?wait=1', 'statusWin' ,'width=400,height=135,directories=0,status=0,menuBar=0,scrollBars=0,resizable=0');
			}
		}

	//-->
	</SCRIPT>
<?



			$date = date('d-m-Y');
			$sqldate = date('Y-m-d');
			//$outpmenu .= "<font size=+1>" . $title . " $lang[entsum]</font>";
			// <tr><td>$lang[owner]:</td><td>$lang[assignee]:</td><td>$lang[customer]:</td><td>$lang[duedate]:</td><td>$lang[status]:</td><td>$lang[priority]:</td><td>$lang[waitingcsv]:</td></tr>
			$outpmenu .= "<form name=summ method=GET Onsubmit='javascript:SearchStatusWin()'>";
			$outpmenu .= "<input type='hidden' name='extended' value='" . $extended . "'>";
			$outpmenu .= "<div id='Advanced' style='display: none'>";			
			$outpmenu .= "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[showonly]&nbsp;";
			$outpmenu .= "<a style='cursor:pointer' class='bigsort' OnClick=\"javascript:hideLayer('Advanced');document.summ.extended.value='0'\" title='Get rid of this!'>&nbsp;&nbsp;<img src='arrow.gif'> Hide</a>";
			$outpmenu .= "</legend>";
//
			$outpmenu .= "<table width='90%'>";

//			$outpmenu .= '<iframe src="empty.html" name="uRail" id="uRail"></iframe>';


			

			$outpmenu .= "<tr><td><fieldset><legend>&nbsp;&nbsp;$lang[owner]&nbsp;</legend><select name='owner' size='1'>";
			
			$outpmenu .= "<option value='all'>$lang[all]</option>";
			$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]loginusers WHERE LEFT(FULLNAME,3) <> '@@@' ORDER BY FULLNAME";

			$result= mcq($sql,$db);

			while ($CRMloginusertje= mysql_fetch_array($result)) {
							if ($CRMloginusertje[id]==$owner) {
															$a = "SELECTED";
							} else {
															$a = "";
							}

					$outpmenu .= "<option value='$CRMloginusertje[id]' size='1' $a>$CRMloginusertje[FULLNAME]</option>";
			}
			$outpmenu .= "</select></fieldset></td><td>";
			$outpmenu .= "<fieldset><legend>&nbsp;&nbsp;$lang[assignee]&nbsp;</legend><select name='assignee' size='1'>";
			$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]loginusers WHERE LEFT(FULLNAME,3) <> '@@@' ORDER BY FULLNAME";
			$result= mcq($sql,$db);

			$outpmenu .= "<option value='all'>$lang[all]</option>";
			while ($CRMloginusertje= mysql_fetch_array($result)) {
							if ($CRMloginusertje[id]==$assignee) {
															$a = "SELECTED";
							} else {
															$a = "";
							}

					$outpmenu .= "<option value='$CRMloginusertje[id]' size='1' $a>$CRMloginusertje[FULLNAME]</option>";
			}

			$outpmenu .= "</select></fieldset></td><td><fieldset><legend>&nbsp;&nbsp;$lang[customer]&nbsp;</legend>";
			$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]customer ORDER BY custname";
			$result= mcq($sql,$db);
			$outpmenu .= "<select name='CRMcustomer' size='1'>";
			$outpmenu .= "<option value='all'>$lang[all]</option>";
			while ($CRMloginusertje= mysql_fetch_array($result)) {
							$auth = CheckCustomerAccess($CRMloginusertje['id']);
							if ($auth == "ok" || $auth == "readonly") {
								if ($CRMloginusertje[id]==$CRMcustomer) {
																$a = "SELECTED";
								} else {
																$a = "";
								}

								$outpmenu .= "<option value='$CRMloginusertje[id]' size='1' $a>$CRMloginusertje[custname]</option>";
							}
			}

			$outpmenu .= "</select></fieldset></td></tr><tr><td><fieldset><legend>&nbsp;&nbsp;$lang[duedate]&nbsp;</legend>";
			
			$outpmenu .= "<select name='duedate' size='1'>";

			$outpmenu .= "<option value='all'>$lang[all]</option>";
			$date = date('d-m-Y');
			$sqldate = date('Y-m-d');
			if ($duedate=='Today') {
				$is = "SELECTED";
			} else {
				unset($is);
			}
			$outpmenu .= "<option value='Today' $is>$lang[entitiestoday]</option>";
			
            if ($duedate=='Overdue') {
				$outpmenu .= "<option value='Overdue' SELECTED>Overdue</option>";
			} else {
				$outpmenu .= "<option value='Overdue'>Overdue</option>";
			}
            $sql= "SELECT DISTINCT duedate FROM $GLOBALS[TBL_PREFIX]entity ORDER BY sqldate";
			$result1= mcq($sql,$db);
						
			while ($CRMloginusertje= mysql_fetch_array($result1)) {
				if ($CRMloginusertje[duedate]=="") { $CRMloginusertje[duedate]='Blank';}
                                        if ($CRMloginusertje[duedate]==$duedate) {

                                           $a = "SELECTED";
                                        } else {
                                           $a = "";
                                        }
                                $outpmenu .= "<option value='$CRMloginusertje[duedate]' size='1' $a>" . TransformDate($CRMloginusertje[duedate]) . "</option>";
                        }			
                        $outpmenu .= "</select></fieldset></td>";
	
			$outpmenu .= "<td><fieldset><legend>&nbsp;&nbsp;$lang[status]&nbsp;</legend><select name='status' size='1'>";

			if ($status=="All") { $a="SELECTED"; } else { $a=""; }
			$outpmenu .= "<option value='' $a>$lang[all]</option>";

			// -------------------------------------------------------------

			$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]statusvars ORDER BY varname";
			$result= mcq($sql,$db);
			while ($result1= mysql_fetch_array($result)) {
					if ($status==$result1[varname]) { $a="SELECTED"; } else { $a=""; }
					$outpmenu .= "<option value='$result1[varname]' style='background:" . $result1[color] . "' $a>$result1[varname]</option>";
			}
						
			// -------------------------------------------------------------			
			
			$outpmenu .= "</select></fieldset></td>";

			$outpmenu .= "<td><fieldset><legend>&nbsp;&nbsp;$lang[priority]&nbsp;</legend><select name=prio size='1'>";
			if ($prio=="All") { $a="SELECTED"; } else { $a=""; }
			$outpmenu .= "<option value='' $a>$lang[all]</option>";
			$sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]priorityvars ORDER BY varname";
			$result= mcq($sql,$db);

			while ($result1= mysql_fetch_array($result)) {
					if ($prio==$result1[varname]) { $a="SELECTED"; } else { $a=""; }
					$outpmenu .= "<option style='background:" . $result1[color] . "' value='$result1[varname]' $a>$result1[varname]</option>";
			}
						
			// -------------------------------------------------------------			
			
			$outpmenu .= "</select></fieldset>";
			$outpmenu .= "</td></tr><tr>";

			

			if (strtoupper($GLOBALS['UseWaitingAndDoesntBelongHere']) == "YES") {
				$outpmenu .= "<td><fieldset><legend>&nbsp;&nbsp;$lang[waitingcsv]&nbsp;</legend><select name='waiting' size='1'>";
				if ($waiting=="all") { $a="SELECTED"; } else { $a=""; }
				$outpmenu .= "<option value='' $a>$lang[all]</option>";
				if ($waiting=="y") { $a="SELECTED"; } else { $a=""; }
				$outpmenu .= "<option value='y' $a>Yes</option>";
				if ($waiting=="n") { $a="SELECTED"; } else { $a=""; }
				$outpmenu .= "<option value='n' $a>No</option>";
				$outpmenu .= "</select>";
			}

			
			if (strtoupper($GLOBALS['ForceCategoryPulldown'])=="YES") {
				$outpmenu .= "</td><td colspan=2><fieldset><legend>&nbsp;&nbsp;$lang[category]</legend>";
				$outpmenu .= "<select name='category' class='text' width=50><option value=''>$lang[all]</option>";
				$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='Category pulldown list'";
				$result = mcq($sql,$db);
				$list = mysql_fetch_array($result);
				$list = $list[value];
				$list = @explode(",",$list);	

				if (sizeof($list)>0 && $list[0]<>"") {

					for ($t=0;$t<sizeof($list);$t++) {
						if ($list[$t]==$category) {
							$roins = "SELECTED";
						} else {
							unset($roins);
						}
						$outpmenu .= "<option $roins value='$list[$t]'>$list[$t]</option>";
					}
									
				}		
				$outpmenu .= "</select>";
				$outpmenu .= "</fieldset></td>";
			} else {
				$outpmenu .= "</td>";
			}
			$outpmenu .= "<td><fieldset><legend>&nbsp;&nbsp;Creation date</legend><select name='creation_date'><option value=''>$lang[all]</option>";
			$sql = "SELECT DISTINCT(cdate) FROM $GLOBALS[TBL_PREFIX]entity ORDER BY cdate";
			$result = mcq($sql,$db);
			while ($row = mysql_fetch_array($result)) {
				$t = $row[cdate];
				$tjaar = substr($t,0,4);
				$tmaand = substr($t,5,2);
				$tdag = substr($t,8,2);
				
				$showdate = TransformDate($tdag . "-" . $tmaand . "-" . $tjaar);
				if ($row['cdate'] == $creation_date) {
					$ins_cdate = "SELECTED";
				} else {
					unset($ins_cdate);
				}
				
				$outpmenu .= "<option value='". $row['cdate'] . "'" . $ins_cdate . ">" . $showdate . "</option>";
			}

			$outpmenu .= "</select></fieldset>";
			$outpmenu .= "</tr>";
			$outpmenu .= "<tr><td colspan=4><hr><table>";

			$list = GetExtraFields();

			foreach ($list as $field) {
					
					$element = "EFID" . $field['id'];
						if (($field['fieldtype'] <> "List of values") && ($field['fieldtype'] <> "text area") && ($field['fieldtype'] <> "text area (rich text)")) {
						//$b64item = base64_encode($item);
						$outpmenu .= "<tr><td><fieldset><legend>&nbsp;&nbsp;" . $field['name'] . "&nbsp;</legend>";
						$outpmenu .= "<select name='" . $element . "'><option value=''>" . $lang['all'] . "</option>";
						if ($field['fieldtype'] <> "date") {
							$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name='" . $field['id'] . "' AND type<>'cust' ORDER BY value";
						} else {
							// 31-12-2004
							$sql = "SELECT value, RIGHT(value,4) AS year, SUBSTRING(value,4,2) AS month, LEFT(value,2) AS day FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name='" . $field['id'] . "' AND type<>'cust' ORDER BY year,month,day";
						}
						$result2 = mcq($sql,$db);
						$done = array();
						while ($result = mysql_fetch_array($result2)) {
							if (!in_array($result['value'],$done)) {
								if ($_REQUEST[$element]==$result['value']) {
									$ins = "SELECTED";
								} else {
									unset($ins);
								}
								if (strstr($field['fieldtype'],"User-list")) {
									$outpmenu .= "<option $ins value='" . $result['value'] . "'>" . GetUserName($result['value']) . "</option>";
								} else {
									$outpmenu .= "<option $ins>" . $result['value'] . "</option>";
								}
								array_push($done, $result['value']);
							}
						
						}
						
						$outpmenu .= "</select></td></tr>";
				
						}
			}
			$outpmenu .= "</table></td></tr></fieldset>";
			if ($includecontents) {
					$ins1 = " SELECTED";
			} elseif ($includedeleted) {
					$ins2 = " CHECKED ";
			} elseif ($pushcsv) {
					$ins3 = " SELECTED ";
			} elseif ($pushpdf) {
					$ins5 = " SELECTED ";
			} elseif ($brief) {
					$ins4 = " SELECTED ";
			} elseif ($extended) {
					$ins6 = " SELECTED ";
			} else {
					$ins4 = " SELECTED ";
			}
			$outpmenu .= "</table></fieldset>";
			$outpmenu .= "<br></div>";
			$outpmenu .= "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;$lang[showonly]";
			
			$outpmenu .= "<SCRIPT LANGUAGE=\"JavaScript\">\n";
			$outpmenu .= "<!--\n";
			$outpmenu .= "	if (document.summ.extended.value=='1') {\n";
			$outpmenu .= "			showLayer('Advanced');\n";
			$outpmenu .= "	}\n";
			$outpmenu .= "//-->\n";
			$outpmenu .= "</SCRIPT>\n";
			
			$outpmenu .= "<a style='cursor:pointer' class='bigsort' OnClick=\"javascript:showLayer('Advanced');document.summ.extended.value='1';\" title='Expand'>&nbsp;&nbsp;<img src='arrow.gif'> More</a>";

			
			$outpmenu .= "</legend><table>";
			$outpmenu .= "<td colspan=3><br><fieldset><table><tr><td>$lang[incldel]:</td><td><input type=checkbox class='radio'  name='includedeleted'$ins2> <a OnClick='javascript:pophelp(3)' class='bigsort' cursor='click' style='cursor: help'><img src='info.gif'></a></td>";

			$outpmenu .= "<tr><td>$lang[showresults]:</td><td colspan=2><select name='csv'>";

			if ($_REQUEST['csv'] == "onscreen") {
				$ins = "SELECTED";
			} else {
				unset($ins);
			}
			$outpmenu .= "<option $ins value='onscreen'>$lang[verbose]</option>";
			if ($_REQUEST['csv'] == "onscreenfull") {
				$ins = "SELECTED";
			} else {
				unset($ins);
			}
			$outpmenu .= "<option $ins value='onscreenfull' $ins1>$lang[verbosewithcontents]</option>";
			if ($_REQUEST['csv'] == "short") {
				$ins = "SELECTED";
			} else {
				unset($ins);
			}
			$outpmenu .= "<option $ins value='short' $ins4>$lang[briefsum]</option>";
			if ($_REQUEST['csv'] == "CSV") {
				$ins = "SELECTED";
			} else {
				unset($ins);
			}
			$outpmenu .= "<option $ins value='CSV' $ins3>$lang[downloadsumcsv]</option>";
			if ($_REQUEST['csv'] == "PDF") {
				$ins = "SELECTED";
			} else {
				unset($ins);
			}
			$outpmenu .= "<option $ins value='PDF' $ins5>$lang[downloadsumpdf]</option>";
			if ($_REQUEST['csv'] == "entityreport") {
				$ins = "SELECTED";
			} else {
				unset($ins);
			}
			$outpmenu .= "<option $ins value='entityreport' $ins5>$lang[createreports]</option>";

			$arr = GetFilesOfType("TEMPLATE_HTML_REPORT");
			foreach ($arr AS $report) {
				if ($_REQUEST['csv'] == "REPORTID_" . $report['fileid']) {
					$ins = "SELECTED";
				} else {
					unset($ins);
				}
				$outpmenu .= "<option $ins value='REPORTID_" . $report['fileid'] . "'>" . $report['file_subject'] . "</option>";
			}
		
			$outpmenu .= "<option $ins value='managementinfo' $ins5>$lang[maninfo]</option>";
			
			$outpmenu .= "</select></td>";
			
			$outpmenu .= "<tr><td>$lang[mustcontain]:</td><td><input type='text' class='searchx' name='wildsearch' value='$wildsearch'> <a OnClick='javascript:pophelp(7)' class='bigsort' cursor='click' style='cursor: help'><img src='info.gif'></a></td></tr>";

			$ss = GetSavedSearches();

			if (is_array($ss)) {
				$outpmenu .= "<tr><td>Bookmark:</td><td><select name='bookmark' onchange=\"document.location=document.summ.bookmark[document.summ.bookmark.selectedIndex].value + '&dsp_overrule=' + document.summ.csv[document.summ.csv.selectedIndex].value;document.summ.knopje.disabled=true;document.summ.knopje.value='Loading...';\"><option value='summary.php'>-</option>";

				foreach ($ss as $key => $element) {
					//if ($_REQUEST['SelectedBookmark'] == $key) {
					//	$ins = "SELECTED";
					//} else {
						unset($ins);
					//}
					$outpmenu .= "<option " . $ins . " value='" . $element . "&SelectedBookmark=" . $key . "'>" . $key . "</option>";
				}


				$outpmenu .= "</select></td></tr>";
			}

			$outpmenu .= "</table></fieldset><br><br></td></tr>";

			$outpmenu .= "<tr><td><input type=submit value='Show' name='knopje' onclick=\"document.summ.knopje.disabled=true;document.summ.knopje.value='Loading...';document.summ.submit();\">&nbsp;";
			$outpmenu .= "&nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<a href='" . $GLOBALS['PHP_SELF'] . "' class='bigsort'>$lang[clear]</a>";

			

	// Query build-up, execute, and put into array.
	
	if ($wildsearch) {
		$wildsearch = addslashes($wildsearch);
		$wsupper = strtoupper($wildsearch);
	
		
			$cust_insert .= " OR ($GLOBALS[TBL_PREFIX]entity.category LIKE '%$wildsearch%' OR $GLOBALS[TBL_PREFIX]entity.content LIKE '%$wildsearch%')";

			$ws_entity = "OR ($GLOBALS[TBL_PREFIX]entity.category LIKE '%$wildsearch%' OR $GLOBALS[TBL_PREFIX]entity.content LIKE '%$wildsearch%')";

			$ws_customfields = "OR $GLOBALS[TBL_PREFIX]customaddons.value LIKE '%$wildsearch%'";

			$ws_binfiles = "
			
			AND (	
					$GLOBALS[TBL_PREFIX]binfiles.type='entity' 
					AND
					$GLOBALS[TBL_PREFIX]binfiles.koppelid<>'0'
					AND
					$GLOBALS[TBL_PREFIX]binfiles.koppelid<>''

						AND	
							(
								$GLOBALS[TBL_PREFIX]binfiles.filename LIKE '%$wildsearch%'
							OR	
								UCASE($GLOBALS[TBL_PREFIX]blobs.content) LIKE BINARY '%$wsupper%' 
							OR 
								UCASE($GLOBALS[TBL_PREFIX]blobs.content) LIKE BINARY '%$wildsearch%'
							OR
								(
									$GLOBALS[TBL_PREFIX]binfiles.filename LIKE '%$wildsearch%'
									AND 
									$GLOBALS[TBL_PREFIX]binfiles.filetype<>'application/octet-stream' 
									AND filetype NOT LIKE '%image%'
								)
							)

				)";
		}



	$order = "ORDER BY $GLOBALS[TBL_PREFIX]entity.deleted,sqldate,status,priority";

	$sql_src .= "<pre>\n";

	$res_array = array();
	$bla = date('U');
	// now execute all 3 queries, and put the eid results into an array, if it's not there already
	$q1 = "SELECT eid FROM $GLOBALS[TBL_PREFIX]entity WHERE 1=0 " . $cust_insert . " " . $ws_entity;

	$res = mcq($q1,$db);
	$sql_src .= "\n". $q1;
	while ($row = mysql_fetch_array($res)) {
			if (!in_array($row['eid'],$res_array) && $row['eid']<>"" && $row['eid']<>0) {
				array_push($res_array,$row['eid']);
				qlog("hit from entities ->" . $row['eid']);
				$hit = 1;
			} else {
				qlog("hit from entities (ignoring) ->" . $row['eid']);
			}
	}


	if ($wildsearch) {
		foreach ($res_array AS $arf) {
			$ws_customfields .= " AND $GLOBALS[TBL_PREFIX]customaddons.eid<>" . $arf;
			qlog("CUSTOMFIELDS: excluding $arf");
		}
		$q2 = "SELECT eid FROM $GLOBALS[TBL_PREFIX]customaddons WHERE 1=0 " . $ws_customfields;
		$sql_src .= "\n". $q2;
		$res = mcq($q2,$db);
		while ($row = mysql_fetch_array($res)) {
			if (!in_array($row['eid'],$res_array) && $row['eid']<>"" && $row['eid']<>0) {
				array_push($res_array,$row['eid']);
				qlog("hit from custom fields ->" . $row['eid']);
				$hit = 1;
			} else {
				qlog("hit from custom fields (ignoring) ->" . $row['eid']);
			}
		}
		foreach ($res_array AS $arf) {
			$ws_binfiles .= " AND $GLOBALS[TBL_PREFIX]binfiles.koppelid<>" . $arf;
			qlog("BINFILES: excluding $arf");
		}
		$q3 = "SELECT koppelid AS eid FROM $GLOBALS[TBL_PREFIX]binfiles,$GLOBALS[TBL_PREFIX]blobs WHERE $GLOBALS[TBL_PREFIX]binfiles.fileid=$GLOBALS[TBL_PREFIX]blobs.fileid " . $ws_binfiles;
		$sql_src .= "\n". $q3;
		qlog($q3);
		$res = mcq($q3,$db);
		while ($row = mysql_fetch_array($res)) {
			if (!in_array($row['eid'],$res_array) && $row['eid']<>"" && $row['eid']<>0) {
				array_push($res_array,$row['eid']);
				qlog("hit from attachments ->" . $row['eid']);
				$hit = 1;
			} else {
				qlog("hit from attachments (ignoring) ->" . $row['eid']);
			}
		}
	}

	

	if ((sizeof($res_array)==0) && ($hit<>1 && !$wildsearch)) {
		qlog("FILLING ARRAY WITH ALL EIDS");
		$res_array = array();
		$sql = "SELECT eid FROM $GLOBALS[TBL_PREFIX]entity";
		$res = mcq($sql,$db);
		while ($row = mysql_fetch_array($res)) {
			array_push($res_array,$row['eid']);
		}
	} elseif ($wildsearch && $hit<>1) {
		qlog("NO RESULTS! ($hit) ($wildsearch)");
		$no_results = 1;
	} elseif ($hit) {
		qlog("RESULTS FROM WILDSEARCH");
	}
	
	if (!$no_results) {
		$sql = "SELECT * FROM 
			$GLOBALS[TBL_PREFIX]entity, 
			$GLOBALS[TBL_PREFIX]customer
			
		
			WHERE
			$GLOBALS[TBL_PREFIX]entity.CRMcustomer = $GLOBALS[TBL_PREFIX]customer.id   
			 ";
			
		$ins_sql = "AND (1=0 ";

		// EXTRA FIELDS FILTER

		$list = GetExtraFields();

		//$q4 = "SELECT eid FROM $GLOBALS[TBL_PREFIX]customaddons WHERE 1=0 ";
		
		$res_array_filtered = array();
		$tmp_array = array();
		$res_array_wrong = array();

		foreach ($res_array AS $element) {
			// We have to process every proposed EID to see if it has extra fields attached 
			// which have values which are searched for.

			//qlog("PROCESSING $element");

			foreach ($list AS $field) {
					$element_f = "EFID" . $field['id'];
					if ($_REQUEST[$element_f]) {
						$ok = 1;
				//		qlog($element_f . " found in query");
						// OK an extra field named $item was found in the search query
						$q4 = "SELECT value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='" . $element . "' AND name='" . $field['id'] . "' AND value='" . $_REQUEST[$element_f] . "'";
						$ef_res = mcq($q4,$db);
						$row = mysql_fetch_array($ef_res);
						if ($row['value']) {
							if (!in_array($element,$res_array_wrong) && !in_array($element,$res_array_filtered)) {
								array_push($res_array_filtered,$element);
								qlog("Adding $element");
							} else {
								qlog("NOT Adding $element (marked as dirty or already there)");
							}
						} else {
							sort($res_array_filtered);//This will fix this issue
							for ($i=0;$i<sizeof($res_array_filtered);$i++) {
								if ($res_array_filtered[$i] == $element) {
									unset($res_array_filtered[$i]);
									qlog("Removing $element");
								} else {
									qlog("Not ok but nothing to remove (" . $element . ")");
								}
							}
							array_push($res_array_wrong,$element);
						}
					} else {
					//	qlog($element_f . " not found in query");
					//	qlog($_REQUEST);
					}
				
			}
		}
		
		if ($ok) {
			$res_array = $res_array_filtered;
			//print_r($res_array);
		}

		foreach ($res_array AS $id) {
			$ins_sql .= " OR " . $GLOBALS[TBL_PREFIX] . "entity.eid='" . $id . "' ";
			$eidc++;
		}
		$ins_sql .= ")";
		
		if ($eidc) {
			$sql .= $ins_sql;
		} else {
			$sql .= " AND 1=1";
		}


		if ($includedeleted) {
	//			$outpmenu .= "Include deleted";
		} else { 
	//			$outpmenu .= "Don't include deleted";
			$sql .= " AND $GLOBALS[TBL_PREFIX]entity.deleted<>'y' ";
		}

		

		if ($duedate) {
			$sqldate = date('Y-m-d');
			if ($duedate=='Blank') { $duedate=''; };

			if ($duedate=='all') { 
				// $cust_insert needs no additions
			} elseif ($duedate=='Overdue') {
						$sql .= " AND $GLOBALS[TBL_PREFIX]entity.sqldate<'$sqldate' and $GLOBALS[TBL_PREFIX]entity.status<>'close'";
			} elseif ($duedate=='Today') {
						$date = date('d-m-Y');
						$sql .= " AND $GLOBALS[TBL_PREFIX]entity.duedate='" . $date . "' ";
			} else {	
						$sql .= " AND $GLOBALS[TBL_PREFIX]entity.duedate='" . $duedate . "' ";
			}
		}

		if ($status) {
			if ($status=='all') { 
				// $cust_insert needs no additions
			} else {
						$sql .= " AND $GLOBALS[TBL_PREFIX]entity.status='" . $status . "'";
			} 
		}
		if ($category) {
			$sql .= " AND $GLOBALS[TBL_PREFIX]entity.category = '" . $category . "' ";
		}
		if ($CRMcustomer<>'all') {
			$sql .= " AND $GLOBALS[TBL_PREFIX]entity.CRMcustomer='$CRMcustomer' ";
		}
		if ($owner<>"all") {
			$sql .= " AND $GLOBALS[TBL_PREFIX]entity.owner = '" . $owner . "' ";
		}
		if ($assignee<>"all") {
			$sql .= " AND $GLOBALS[TBL_PREFIX]entity.assignee = '" . $assignee . "' ";
		}
		if ($prio) {		
			$sql .= " AND $GLOBALS[TBL_PREFIX]entity.priority = '" . $prio . "' ";
		}
		if ($waiting=="n") {
			$sql .= " AND $GLOBALS[TBL_PREFIX]entity.waiting <> 'y' ";
		} elseif ($waiting=="y") {
			$sql .= " AND $GLOBALS[TBL_PREFIX]entity.waiting = 'y' ";
		}
		if ($creation_date) {
			$sql .= " AND $GLOBALS[TBL_PREFIX]entity.cdate = '" . $creation_date ."'";
		}

			// Exclude entities added by [customers]
		$sql .= " AND $GLOBALS[TBL_PREFIX]entity.owner<>'2147483647' 
					AND $GLOBALS[TBL_PREFIX]entity.assignee<>'2147483647'";

		$sql .= " ORDER BY $GLOBALS[TBL_PREFIX]entity.deleted,sqldate,status,priority";

	} else { // end if !$no_results
		$sql = "SELECT waiting FROM  $GLOBALS[TBL_PREFIX]entity WHERE 1=0";
	}
	
	$ss = PushStashValue($GLOBALS['REQUEST_URI']);
//	print $sql;
//	exit;

	if ($_REQUEST['owner']) {
		$outpmenu .= "&nbsp;&nbsp;<img src='arrow.gif'> <a style='cursor: pointer' onclick='poplittlewindowWithBars(\"summary.php?SaveSearch=" . $ss . "\");'>Bookmark(s)</a>";
	}
	if (is_administrator() && $_REQUEST['owner']) {
//		$outpmenu .= "&nbsp;&nbsp;<img src='arrow.gif'> <a style='cursor: pointer' onclick='poplittlewindowWithBars(\"summary.php?SaveSearch=" . $ss . "\");'>Make this search a menu tab (admin)</a>";
	}
	$outpmenu .= "</td></tr></form>";
	$outpmenu .= "</table></fieldset></table>";
	//			$outpmenu .= "<hr>";

	if ((!$assignee) && (!$owner) && (!$CRMcustomer)) {
//	$outpmenu .= "$lang[please]</body></html>";
	print $outpmenu;
	unset($outpmenu);
	}



    $outpmenu .= "<table border=0 cellpadding=0 cellspacing=10>";

	$sql_src .= "\n". $sql;
	
	$bla = date('U') - $bla;
	qlog("SECONDS______________$bla");


    
	// ***********************************************************************************************************
//		print "<pre>$sql</pre>";
	// ***********************************************************************************************************


		

		// AND NOW PRINT THE MENU $outpmenu
		//entityreport
		if ($csv == "entityreport") {
				$stashid = PushStashValue($sql);

				?>
				<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
				<!--
				
				document.location='entityreport.php?stashid=<? echo $stashid;?>&closepopup=1';
				//-->
				</SCRIPT>
				<?
		} elseif ($csv == "managementinfo") {
					$stashid = PushStashValue($sql);

				?>
				<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
				<!--
				
				document.location='stats.php?stashid=<? echo $stashid;?>';
				//-->
				</SCRIPT>
				<?
		} elseif ($pushcsv) {
			
	//			$sql = base64_encode($sql);
				$stashid = PushStashValue($sql);

				?>
				<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
				<!--
				
				document.location='csv.php?stashid=<? echo $stashid;?>&query=dummy&printall=1&submitted=1';
				//-->
				</SCRIPT>
				<?

			exit;
		} elseif ($pushpdf) {
				
				$result= mcq($sql,$db);
				$tel=0;
				$printed = array();
			
			    while ($e= mysql_fetch_array($result)) {
					// HIER ONTDUBBELING
							if (!in_array($e[eid],$printed) && (CheckEntityAccess($e['eid'])<>"nok")) {
							$tel++;
							$printed[$tel] = $e[eid];
							$pdf .= $e[eid] . ",";
							$num++;
					}
				}
				// hier window weer sluiten
				
				if ($wildsearch) {
				?>
				<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
				<!--
					statusWin.close();
				//-->
				</SCRIPT>
				<?
				}
				$GLOBALS['CURFUNC'] = "CreatePDF:: ";
				$stashid = PushStashValue($pdf);
				qlog("Query is in stash ($pdf)");

				?>
				<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
				<!--
					newWindow = window.open('sumpdf.php?enc=1&stashid=<? echo $stashid;?><? echo $ins;?>&tab=30','pdf','width=640,height=630,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
				//-->
				</SCRIPT>
				<?
				sleep(3);
				?>
				<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
				<!--
					document.location='summary.php?0=9&tab=90';
				//-->
				</SCRIPT>
				<?
				exit;
		} else {
			print $outpmenu;
			// HIER SQL PRINT --------------------------------------------------------
			//print $sql;
		}

	// ***********************************************************************************************************
//	print $sql;
	
	if ($brief) {
		
		//$toURL = "index.php?ShowEntitiesOpen=1&query64=" . base64_encode($sqlcsv);
		
		$query64 = base64_encode($sql);

		$ShowFilterInMainList="no";
		$ShowSortLink="no";
		$From_Summary = true;
		
		print "<table><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><table>";
		ShowEntitiesOpen();
		print "</td></tr></table>";
//		print $sql;
		
		//	briefoverview($sql);
//			ShowEntitiesOpen();

		
	} elseif (strstr($_REQUEST['csv'],"REPORTID_")) {
		
		print "<BR><table><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";

			$result= mcq($sql,$db);
			$printed = array();
			$template_orig = GetFileContent(str_replace("REPORTID_","",$_REQUEST['csv']));

			while ($e= mysql_fetch_array($result)) {
				if (!in_array($e[eid],$printed)) {
					if (CheckEntityAccess($e['eid'])<>"nok") {
						array_push($printed, $e['eid']);
							$outp = ParseTemplateEntity($template_orig,$e['eid']);
							$outp = ParseTemplateGeneric($outp);	
							$outp = ParseTemplateCustomer($outp,$e['CRMcustomer']);
							print $outp;
							unset($outp);
							$teller++;
					} // end if access
				} // end if !in_array
			}

		print "</td></tr></table>";
		
	} else {
	
	$result= mcq($sql,$db);
    
	$printed = array();

	while ($e= mysql_fetch_array($result)) {
    
	// ***********************************************************************************************************


	if (!in_array($e[eid],$printed)) {
		if (CheckEntityAccess($e['eid'])<>"nok") {
			$tel++;
			$printed[$tel] = $e[eid];
			$outp .= "<tr><td></td><td><b>$lang[customer]</b>";
			$outp .= "</td><td><b>$lang[owner]</b></td><td><b>$lang[assignee]</b></td><td><b>Status</b></td><td><b>$lang[priority]</b>";
			$outp .= "</td><td><b>$lang[category]</b></td><td><b>$lang[duedate]</b></td><td><b>$lang[alarmdate]</b></td></tr>";

			$outp .= "<tr><td></td><td>$e[custname]</td><td>" . GetUserName($e['owner']) . "</td><td>";

			$outp .= GetUserName($e[assignee]) . "</td>";
			
			
			$outp .= "<td bgcolor='" . GetStatusColor($e[status]) . "'>";
		
			if ($e[deleted]=='y') { 
				$outp .= "$e[status] (deleted)</td>";
			} else {
				$outp .= "$e[status]</td>";
			}
			
			$outp .= "<td bgcolor='" . GetPriorityColor($e[priority]) . "'>";
			
			$outp .= "$e[priority]</td><td><b>";
			
			// If wildsearch was used, highlight the matches.

			$funkycontent = htmlspecialchars($e[content]);
			$e[content] = htmlspecialchars($e[content]);

			if ($wildsearch) {
				$wildsearch = str_replace('/','\/',$wildsearch);
				$e[content] = preg_replace("/($wildsearch)/i", "<font color=ff3300><u>\\1</u></font>", $e[content]);
				$e[category] = 	preg_replace("/($wildsearch)/i", "<font color=ff3300><u>\\1</u></font>", $e[category]);
			}

			$outp .= "$e[category] ($e[eid])</b></td>";
			$t = $e[tp]; // timestamp last edit
			$tp[jaar] = substr($t,0,4);
			$tp[maand] = substr($t,4,2);
			$tp[dag] = substr($t,6,2);
			$tp[uur] = substr($t,8,2);
			$tp[min] = substr($t,10,2);
			$cdate = explode("-",$e[cdate]);

			// Add zero's ie. 2-7-2003 must become 02-07-2003

			if (strlen($cdate[0])==1) {
					$cdate[0] = "0" . $cdate[0];
			}
			if (strlen($cdate[1])==1) {
					$cdate[1] = "0" . $cdate[1];
			}
			$cdate = $cdate[2] . "-" . $cdate[1] . "-" . $cdate[0];

			$remark .= "&nbsp;-&nbsp;$lang[created] " . TransformDate($cdate) . ", $lang[lastupdate] " . $e[tp];
//			$lu = "$tp[dag]-$tp[maand]-$tp[jaar]";
//			$remark .= TransformDate($lu);
//			$remark .= "$tp[uur]:$tp[min]h.<br>";

			if ($e[duedate]==$date) {
				$tmp1="</font>";  
				$outp .= "<td><font color='FF3300'>";
			} elseif ($e[sqldate]<$sqldate) {
				$tmp1="</u></font>";  
				$outp .= "<td><font color='FF3300'><u>";
				$remark .= "&nbsp;-&nbsp;$lang[expired]<br>";
			} else {
				$outp .= "<td>";
			}

									  
			if ($e[waiting]=="y") {
				$remark .= "&nbsp;-&nbsp;$lang[thisiswaiting]<br>";
			}
			if ($e[obsolete]=="y") {
				$remark .= "&nbsp;-&nbsp;$lang[thisdoesntbelong].<br>";
			}
			if ($e[duedate]=="") {
				$e[duedate] = "<font color='FF3300'>none</font>";	
			}					  
			$outp .= TransformDate($e[duedate]) . "$tmp1</td>";
			
			$sql3 = "SELECT basicdate FROM $GLOBALS[TBL_PREFIX]calendar WHERE eID='$e[eid]'";


			$rult2 = mcq($sql3,$db);

			if ($e5= mysql_fetch_array($rult2)) {
				$e5[basicdate] = substr($e5[basicdate],0,2) . "-" . substr($e5[basicdate],2,2) . "-" . substr($e5[basicdate],4,4);
				$outp .= "<td>" . TransformDate($e5[basicdate]) . "</td>";	    
			} else {
				$outp .= "<td><font color='FF3300'>none</font></td>";
			}
			$outp .= "<td></tr>";
			if ($includecontents) {
				$outp .= "<tr><td></td><td>Contents:</td></tr>";
				$cont = ereg_replace("\n","<BR>",$e[content]);
				$cont = trim($cont);
				if ($cont=="") {
					$cont = "-empty-";
				}
				$outp .= "<tr><td></td><td colspan=12><table border='1' width='100%' bgcolor='#F2F2F2' cellspacing='0' cellpadding='8' bordercolor='#CECECE'><tr><td>$cont</td></tr></table>";
			} else {
				//$extra = "<a class='bigsort' title=\"$funkycontent\">*</a>";
			}



			$outp .= "</td></tr>";
			if ($remark) {
				$outp .= "<tr><td></td><td colspan=7>$lang[remarks]:<br><i>" . $remark . "</i></td></tr>";
				unset($remark);
			}
			
			
			
					$list = GetExtraFields();
					if (sizeof($list)>1) {
						
						$outp .= "<tr><td></td><td colspan=4><table border='1' width='100%' bgcolor='#F2F2F2' cellspacing='0' cellpadding='8' bordercolor='#CECECE'>";
						for ($po=0;$po<sizeof($list);$po++) {
								
								$sql0 = "SELECT id,value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='$e[eid]' AND deleted<>'y' AND name='$list[$po]' ORDER BY name";
			//					print $sql;

								$result8 = mcq($sql0,$db);
								$cust= mysql_fetch_array($result8);
								$val = $cust['value'];
								$id = $cust['id'];
								if (stristr($list[$po],"EML_")) { 
									$c_email = 1;
								}
								if (stristr($list[$po],"DATE_")) { 
									$val = TransFormDate($val);
								}
								$list[$po] = CleanExtraFieldName($list[$po]);
								if ($val<>"") {
									if ($wildsearch) {
										if (stristr($val,$wildsearch) && !$c_email) {
											$val = "<font color='#FF0000'>" . $val . "</font>";
										} elseif ($c_email) {
											$val = "<a href='javascript:popEmailToEFScreen(" . $id . ")' class='bigsort'>$val</a>";
											unset($c_email);
										}
									}
									if ($c_email) {
										$outp .= "<tr><td>" . $list[$po] . ":</td><td><a href='javascript:popEmailToEFScreen(" . $id . ")' class='bigsort'>$val</a></td></tr>";
										unset($c_email);
									} else {
										$outp .= "<tr><td>" . $list[$po] . ":</td><td>" . $val . "&nbsp;</td></tr>";
									}
									
								} else {
									unset($c_email);
								}
						}
						$outp .= "</table></td></tr>";
					}
	//		}

			$toprint = "<tr><td></td><td colspan=12>$lang[hasfilessum]:<br><table border='1' width='90%' bgcolor='#F2F2F2' cellspacing='0' cellpadding='8' bordercolor='#CECECE'><tr><td><b>$lang[filename]</b></td><td><b>$lang[filesize]</b></td><td><b>$lang[creationdate]</b></td><td><b>$lang[owner]</b></td></tr>";
			
			
			$sql= "SELECT $GLOBALS[TBL_PREFIX]binfiles.filename,$GLOBALS[TBL_PREFIX]binfiles.filesize,$GLOBALS[TBL_PREFIX]binfiles.fileid,$GLOBALS[TBL_PREFIX]binfiles.filetype,$GLOBALS[TBL_PREFIX]binfiles.username,date_format($GLOBALS[TBL_PREFIX]binfiles.creation_date, '%a %M %e, %Y %H:%i') AS dt FROM $GLOBALS[TBL_PREFIX]binfiles WHERE $GLOBALS[TBL_PREFIX]binfiles.koppelid='$e[eid]' ORDER BY $GLOBALS[TBL_PREFIX]binfiles.filename";

			$result7= mcq($sql,$db);

					while ($files= mysql_fetch_array($result7)) {
						if ($wildsearch) {
							$wsupper = strtoupper($wildsearch);
							$sql2= "SELECT $GLOBALS[TBL_PREFIX]binfiles.fileid FROM $GLOBALS[TBL_PREFIX]binfiles,$GLOBALS[TBL_PREFIX]blobs WHERE $GLOBALS[TBL_PREFIX]binfiles.fileid=$GLOBALS[TBL_PREFIX]blobs.fileid AND (UCASE(content) LIKE BINARY '%$wsupper%' OR UCASE(content) LIKE BINARY '%$wildsearch%') AND $GLOBALS[TBL_PREFIX]binfiles.fileid='$files[fileid]' AND $GLOBALS[TBL_PREFIX]binfiles.filetype <> 'application/octet-stream' AND $GLOBALS[TBL_PREFIX]binfiles.filetype NOT LIKE '%image%'";
							$matches = mcq($sql2,$db);
							if (mysql_fetch_array($matches)) {
								$matches = 1;
							} else {
								$matches = 0;
							}
						}
						if ($wildsearch) {
							if (stristr($files[filename],$wildsearch)) { 
									$matchestoo=1; 
								} else {
									$matchestoo=0;
								}
						}
						
						$ownert = GetUserNameByName($files['username']);

						$toprint .= "<tr><td>";
						if ($matches==1 || $matchestoo==1) {
							$toprint .= "<font color='#FF0000'><img src='arrow.gif'>&nbsp;<a href=csv.php?fileid=$files[fileid] class='bigsort'><font color='#FF0000'>$files[filename]</a></td><td><font color='#FF0000'>$files[filesize]</td><td><font color='#FF0000'>$files[dt]</td><td><font color='#FF0000'>$ownert</td></tr></font>";
						} else {
							$toprint .= "<img src='arrow.gif'>&nbsp;<a href=csv.php?fileid=$files[fileid] class='bigsort'>$files[filename]</a></td><td>$files[filesize]</td><td>$files[dt]</td><td>$ownert</td></tr>";
						}
						$ftel++;
					}
					
					$toprint .= "</table></td></tr>";

					if ($ftel) { $outp .= $toprint; }


			$outp .= "<tr><td></td><td colspan=8 nowrap><img src='arrow.gif'>&nbsp;<a title='Edit this $GLOBALS[TBL_PREFIX]entity' class='bigsort' href='edit.php?e=$e[eid]&fromlist=$return_to_list'>$lang[edit]</a> <img src='arrow.gif'>&nbsp;<a class='bigsort' title='$lang[uinw]' href='javascript:popupdater($e[eid])'>$lang[uinw]</a>";
			$outp .= " <img src='arrow.gif'>&nbsp;<a class='bigsort' href='edit.php?d=$e[eid]' title='Delete this $GLOBALS[TBL_PREFIX]entity'>$lang[delete]</a> <img src='arrow.gif'>&nbsp;<a class='bigsort' title='Download PDF report of this entity' href='sumpdf.php?pdf=$e[eid]' target='pdf'>pdf</a></td><td>$extra</td></tr>";	
			unset($extra);
			unset($ftel);

			$outp .= "<tr><td colspan=12><hr></td></tr>";
			$teller++;
			}
		} // END IF NOT ALREADY PRINTED
	} // END IF ENTITY ACCESS
}  // END IF NOT BRIEF OVERVIEW

//************************

	$outp .= "</table><br><table border=0>";

	if (!$teller) {
	    $outp .= "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td colspan=10>$lang[noresults]</td></tr>";
		$outp = ereg_replace("@@@LEGEND@@@", $lang[noresults], $outp);
	} else {
	    $outp .= "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td colspan=10>$teller $lang[entitiesfound]";
		$outp = ereg_replace("@@@LEGEND@@@", "$teller $lang[entitiesfound]", $outp);
	    $outp .= "<tr><td colspan=12></td></tr>";
	}
		$outp .= "</table>";	
// AND NOW PRINT THE OUTPUT!

	print $outp;

	if ($wildsearch) {
	?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
			statusWin.close();
		//-->
		</SCRIPT>
	<?
	}

// Uncomment to show search query:
//print 
//qlog($sql_src);
EndHTML();
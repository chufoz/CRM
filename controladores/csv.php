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
$GLOBALS['CURFUNC'] = "GenerateExcel::";


	if ($exportlog) {
			include("config.inc.php");
			include("getset.php");
			if (strtoupper($BlockAllCSVDownloads)=="YES") {
				MustBeAdminUser();
			}
			$filename = "CRM-CTT-logexport-" . date("Fj-Y-Hi") . "h.CSV";
			$sql = "SELECT administrator FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
			$result= mcq($sql,$db);
			$bla= mysql_fetch_array($result);
			if ($bla[administrator]<>"yes") {
					uselogger("Request to download log denied.","");
					?>
						<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
						<!--
							alert('You are not authorized to perform this operation because you are not an administrator by profile. Please contact your system administrator');
							document.location = 'index.php';
						//-->
						</SCRIPT>
					<?
					EndHTML();
					exit;
			}

		
			uselogger("Log CSV downloaded","");
			$GLOBALS['CURFUNC'] = "ExportLog::";
			qlog("Log export presented");
						
			header("Content-Type: CSV");
			header("Content-Disposition: attachment; filename=$filename" );
			header("Content-Description: CRM Generated Data" );
			header("Window-target: _top");
			
			if (!$sep) { 
				$sep = ";";
			}
			print "Dumping log $title ($CRM_VERSION)\015\012";
			print "id" . $sep . "IP" . $sep . "URL" . $sep . "TIMESTAMP" . $sep . "QS" . $sep . "USER\015\012";
			
			$sql = "SELECT id,ip,url,qs,user,date_format(tijd, '%a %M %e, %Y %H:%i') AS dt FROM $GLOBALS[TBL_PREFIX]uselog ORDER BY id DESC";
			$result = mcq($sql,$db);
			while ($ee= mysql_fetch_array($result)) {
				$ee[qs] = eregi_replace($sep,"|",$ee[qs]);
				$ee[dt] = eregi_replace($sep,"|",$ee[dt]);
				print $ee[id] . $sep;
				print $ee[ip] . $sep;
				print $ee[url] . $sep;
				print $ee[dt] . $sep;
				print $ee[qs] . $sep;
				print $ee[user] . "\015\012";
			}
			
			print $lang[endofpbexport];
			exit;

	}
	if ($exportcust) {
			
			include("config.inc.php");
			include("getset.php");
			include("language.php");

			if (strtoupper($BlockAllCSVDownloads)=="YES") {
				MustBeAdminUser();
			}
			$GLOBALS['CURFUNC'] = "ExportCustomersFromQuery::";

			$filename = "CRM-CTT" . strtolower($lang[customers]) . "-export-" . date("Fj-Y-Hi") . "h.CSV";
			
			if ($sep=="RealExcel") {
				$RealExcel = 1;
				$sep = "@@@@REALEXCEL@@@@";
				$excel = array();
				uselogger("Customers Excel downloaded","");
			} else {
				uselogger("Customers CSV downloaded","");
			}
			
			if (!$RealExcel) {
				header("Content-Type: CSV");
				header("Content-Disposition: attachment; filename=$filename" );
				header("Content-Description: CRM Generated Data" );
				header("Window-target: _top");
			} else {
				$outp .= "ID" . $sep;
			}
			
			if (!$sep) { 
				$sep = ";";
			}

			$outp .= $lang[customer] . $sep . $lang[contact] . $sep . $lang[contacttitle] . $sep . $lang[contactphone] . $sep . $lang[contactemail] . $sep . $lang[customeraddress] . $sep . $lang[custremarks] . $sep . $lang[custhomepage] . $sep;
			
			$list = GetExtraCustomerFields();

			foreach ($list AS $field) {
						
						$outp .= CleanExtraFieldName($field['name']) . $sep;
				
			}

			if ($nle) {
				$outp .= "\015\012";				
			} elseif ($RealExcel) {
				// do nothing
			} else {
				$outp .= "@@@@CRM@@@@\015\012";
			}

			if ($RealExcel) {
					array_push($excel,$outp);
					unset($outp);
			} else {
					print $outp;
					unset($outp);
			}

			$list = GetExtraCustomerFields();
			//$list = explode(",",$list);
			unset($sql);

			if ($stashid) {

				$sql = PopStashValue($stashid);

				qlog("Query fetched from database: " . $sql);

			} else {
				qlog("ERROR. NO STASHID FOUND. Exporting ALL.");
				$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]customer ORDER BY custname";
			}
			qlog("Query now");
			$result = mcq($sql,$db);
			while ($ee= mysql_fetch_array($result)) {

				$auth = CheckCustomerAccess($ee['id']);
						
				if ($auth == "ok" || $auth == "readonly") {


					if ($RealExcel) {
						$outp .= "" . $ee[id] . "" . $sep;
						$outp .= "" . $ee[custname] . "" . $sep;
						$outp .= "" . $ee[contact] . "" . $sep;
						$outp .= "" . $ee[contact_title] . "" . $sep;
						$outp .= "" . $ee[contact_phone] . "" . $sep;
						$outp .= "" . $ee[contact_email] . "" . $sep;
						$outp .= "@@@@WRAPPED@@@@" . $ee[cust_address] . "" . $sep;
						$outp .= "@@@@WRAPPED@@@@" . $ee[cust_remarks] . "" . $sep;
						$outp .= "" . $ee[cust_homepage] . "" . $sep;
					} else {
						$outp .= "\"" . $ee[custname] . "\"" . $sep;
						$outp .= "\"" . $ee[contact] . "\"" . $sep;
						$outp .= "\"" . $ee[contact_title] . "\"" . $sep;
						$outp .= "\"" . $ee[contact_phone] . "\"" . $sep;
						$outp .= "\"" . $ee[contact_email] . "\"" . $sep;
						$outp .= "\"" . $ee[cust_address] . "\"" . $sep;
						$outp .= "\"" . $ee[cust_remarks] . "\"" . $sep;
						$outp .= "\"" . $ee[cust_homepage] . "\"" . $sep;
					}				

					foreach ($list AS $field) {

						$row = db_GetRow("SELECT * FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='$ee[id]' AND deleted<>'y' AND name='" . $field['id'] . "' AND type='cust' ORDER BY name");

						if ($tmp = unserialize($row['value'])) {
							foreach ($tmp AS $rij) {
								$row['value'] .= base64_decode($rij) . " - ";
							}
						}
						if ($field['fieldtype'] == "User-list of all CRM-CTT users" || $field['fieldtype'] == "User-list of limited CRM-CTT users" || $field['fieldtype'] == "User-list of administrative CRM-CTT users") {
							$row['value'] = GetUserName($row['value']);
						}

						$outp .= $row['value'] . $sep;
					}
					

					
					
					if ($nle) {
						$outp .= "\015\012";				
					} elseif($RealExcel) {
						// Do nothing
					} else {
						$outp .= "@@@@CRM@@@@\015\012";
					}

					if ($RealExcel) {
						array_push($excel,$outp);
						unset($outp);
					} else {
						print $outp;
						unset($outp);
					}
				}

			}
		$GLOBALS['CURFUNC'] = "ExportCustomers::";
		qlog("Customer export presented");
		
		if ($RealExcel) {
			RealExcel($excel,$sep);
		}
		
	exit;
}


if ($tw) {

			include("config.inc.php");
			include("getset.php");
			if (strtoupper($GLOBALS['BlockAllCSVDownloads'])=="YES") {
				MustBeAdminUser();
			}


			$sql="SELECT content,filetype,filename,koppelid FROM $GLOBALS[TBL_PREFIX]binfiles,$GLOBALS[TBL_PREFIX]blobs WHERE $GLOBALS[TBL_PREFIX]binfiles.fileid=$GLOBALS[TBL_PREFIX]blobs.fileid AND $GLOBALS[TBL_PREFIX]blobs.fileid='$fileid'";
			mcq($sql,$db);
			$output = mcq($sql,$db);
			$geg2= mysql_fetch_array($output);
			
//			header("Content-Type: TEXT/CRM");
			header("Content-Disposition: attachment; filename=CRM_TMP_FILE.PDWAS" );
			header("Content-Description: CRM Generated Data" );
			header("Window-target: _top");

			// Push attachment from database
			
			// r0
			print "# CRM PRETTY DIRTY WEB AUTHORING SYSTEM\n";
			// r1
			print "HOSTNA||" . $_SERVER['SERVER_NAME'] . "\n";
			// r2

			$a = $_SERVER['SCRIPT_NAME'];
			$path = ereg_replace("csv.php","",$a);
			print "SUBURL||http://"  . $_SERVER['SERVER_NAME'] . $path . "sub_ocf.php\n";
				    
			// r3
			print "FILEID||" . $fileid . "\n";
			// r4
			print "USERID||" . $name . "\n";
			// r5
			print "REPOSID||" . $repository_nr . "\n";
			// r6

			$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='" . $name ."'";
			mcq($sql,$db);
			$output = mcq($sql,$db);
			$pass = mysql_fetch_array($output);

			print "PASSWD||" . $pass[0] . "\n";
			// r7
			print "FILENAME||" . $geg2[filename] . "\n";
			// r8
			print "BASE64 FOLLOWS\n";
			// r8
			print base64_encode($geg2[content]);
			uselogger("$geg2[filename] from entity $geg2[koppelid] downloaded","");
			journal($geg2[koppelid],"$geg2[filename] downloaded");
		exit;

}

	if ($fileid) {
			include("config.inc.php");
			include("getset.php");

			//if (strtoupper($BlockAllCSVDownloads)=="YES") {
			//	MustBeAdminUser();
			//}
			CheckFileAccess($fileid);

			$sql="SELECT content,filetype,filename,koppelid FROM $GLOBALS[TBL_PREFIX]binfiles,$GLOBALS[TBL_PREFIX]blobs WHERE $GLOBALS[TBL_PREFIX]binfiles.fileid=$GLOBALS[TBL_PREFIX]blobs.fileid AND $GLOBALS[TBL_PREFIX]blobs.fileid='$fileid'";
			mcq($sql,$db);
			$output = mcq($sql,$db);
			$geg2= mysql_fetch_array($output);

			if (CheckEntityAccess($geg2['koppelid']) == "ok" || CheckEntityAccess($geg2['koppelid']) == "readonly") {
				
				if (!$plain) {
					header("Content-Type: $geg2[filetype]");
					header("Content-Disposition: attachment; filename=$geg2[filename]" );
					header("Content-Description: PHP4 Generated Data" );
					header("Window-target: _top");
					print $geg2[content];
				} else {
					print word2html($geg2[content]);
				}
				// Push attachment from database


				uselogger("$geg2[filename] from entity $geg2[koppelid] downloaded","");
				journal($geg2[koppelid],"$geg2[filename] downloaded");
			}
	} else {
			if ((!$submitted || $stashid) && !$_REQUEST['ListLayout']) {
				include("header.inc.php");
				
				if ($query="dummy") {
						?>
						<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
						<!--
								statusWin = window.open('summary.php?wait=1', 'statusWin' ,'width=200,height=70,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
								statusWin.close();
						//-->
						</SCRIPT>						
						<?
					}

				if (!$stashid) { 
					CheckPageAccess("csv");
				}

				$query = PopStashValue($stashid);

				// Excel CSV output
				if (strtoupper($BlockAllCSVDownloads)=="YES") {
						MustBeAdminUser();
						qlog("CSV::Access denied");
				} else {
						qlog("CSV::Access granted");
				}
			

				print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;CSV&nbsp;</legend><table><tr><td>";
				print "<form name='csvfile' method='POST' action='csv.php'><input type='hidden' name='submitted' value='1'>";
				print "<table border=0><tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[selectfields]&nbsp;</legend><table border=0 width='100%'><tr><td><table border=0>";


				print "<tr><td><input type=checkbox class='radio' value='a' name='a' CHECKED></td><td>$lang[customer]</td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='b' name='b' CHECKED></td><td>$lang[owner]</td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='c' name='c' CHECKED></td><td>$lang[assignee]</td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='d' name='d' CHECKED></td><td>$lang[status]</td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='e' name='e' CHECKED></td><td>$lang[priority]</td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='f' name='f' CHECKED></td><td>$lang[duedate]</td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='g' name='g' CHECKED></td><td>$lang[creationdate]</td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='o' name='o' CHECKED></td><td>$lang[extrafields]</td></tr>";
				print "</table></td><td><table>";
				print "<tr><td valign='top'><input type=checkbox class='radio' value='h' name='h' CHECKED></td><td>$lang[lastupdatedate]   </td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='i' name='i' CHECKED></td><td>$lang[lastupdatetime]</td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='j' name='j' CHECKED></td><td>$lang[closedate]</td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='k' name='k' CHECKED></td><td>$lang[waitingcsv]</td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='l' name='l' CHECKED></td><td>$lang[doesntcsv]</td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='m' name='m' CHECKED></td><td>$lang[category]</td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='n' name='n' CHECKED></td><td>$lang[contents]</td></tr>";
				print "<tr><td><input type=checkbox class='radio' value='p' name='p' CHECKED></td><td>$lang[customer] (detail)</td></tr>";

				print "</table></td></tr></table></fieldset></td></tr>";

				print "<tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[sep]&nbsp;</legend>";
				print "<input type=radio class='radio' name='separator' value='RealExcel' CHECKED> Microsoft&reg; Excel&reg; format <img src='excel.gif' border='0'><br>";
			
				print "<input type=radio class='radio' name='separator' border='0' value=';'> $lang[scqp]<br>";
				print "<input type=radio class='radio' name='separator' value=':'> $lang[cqp]<br>";
				print "<input type=radio class='radio' name='separator' value=','> $lang[kqp]<br>";

				
				print "</fieldset></td></tr>";

				if ($stashid) {
						
						print "<tr><td colspan=12>$lang[alreadyselected].</td></tr>";
						print "<input type='hidden' name='stashid2' value='$stashid'>";
						print "<input type='hidden' name='query2' value='dummy'>";
				}
			
				print "<tr><td colspan=2><br>$lang[csvwarning]";
				print "<tr><td><br><input type=submit name='knop' value='$lang[downloadexport]'></td></tr>";

				print "</table></form></td></tr></table>";
				EndHTML();

			} else {


				include("config.inc.php");
				include("getset.php");

				include("language.php");
				if (strtoupper($BlockAllCSVDownloads)=="YES") {
						MustBeAdminUser();
				}
				if ($_REQUEST['swat']) { // Excel CSV request from custom insert mode
						
						$sql = PopStashValue($_REQUEST['stashid2']);
						$swat = PopStashValue($_REQUEST['swat']);

						if (md5($sql) <> $swat) {
							print "This seems to be illegal or something went wrong. This incident has been logged.";
							uselogger("WARNING. A customer-insert user is fooling around with URLs. He/she tried to export an excel sheet by modifying the URL.","");
							exit;
						} else {
							//print "this is ok";

						}
				}

				//$query2=ereg_replace("\\\\##","'",$query2);
				//$query2=stripslashes($query2);
				//$query2=stripslashes($query2);

				$query2 = PopStashValue($stashid2);

				if ($separator=="RealExcel") {
					$RealExcel = 1;
					$separator = "@@@@REALEXCEL@@@@"; 
					$excel = array();
				}				
				

			if (!$query2) {
				$query2= "SELECT * FROM $GLOBALS[TBL_PREFIX]entity,$GLOBALS[TBL_PREFIX]customer,$GLOBALS[TBL_PREFIX]loginusers WHERE $GLOBALS[TBL_PREFIX]customer.id=$GLOBALS[TBL_PREFIX]entity.CRMcustomer AND $GLOBALS[TBL_PREFIX]loginusers.id=$GLOBALS[TBL_PREFIX]entity.owner ORDER BY sqldate,status,priority";
			} else {

			
			//$query2 = base64_decode($query2);
				
			if (stristr($query2,"delete ") || stristr($query2,"update ") || stristr($query2,"insert ") || stristr($query2,"drop ") || stristr($query2,"truncate ")) { 
					print "You have tried to execute an illegal statement. This incident has been logged.";	
					uselogger("ILLEGAL QUERY IN CSV EXPORT : $query2",$name);
					exit;
			}
				$sqlwasset=1;
			}
			
			$sql = $query2;


			$result= mcq($sql,$db);

				
			$filename = "CRM-CTT-CSVexport-" . date("Fj-Y-Hi") . "h.CSV";

			if ($RealExcel<>1) {
				header("Content-Type: text");
				header("Content-Disposition: attachment; filename=$filename" );
				header("Content-Description: PHP4 Generated Data" );
				header("Window-target: _top");
			} 
			
			
			if ($printall) {
				$a=1;
				$b=1;
				$c=1;
				$d=1;
				$e=1;
				$f=1;
				$g=1;
				$h=1;
				$i=1;
				$j=1;
				$k=1;
				$l=1;
				$m=1;
				$n=1;
				$o=1;
				$p=1;
			}
			
			if ($encrypt) { $separator = ";"; }

			
			if ($_REQUEST['ListLayout']) {
				// This means that the user just wants a printout of the main entity list, no more fields than that
				//print_r($GLOBALS['MainListColumnsToShow']);
				
					unset ($a);
					unset ($b);
					unset ($c);
					unset ($d);
					unset ($e);
					unset ($f);
					unset ($g);
					unset ($h);
					unset ($i);
					unset ($j);
					unset ($k);
					unset ($l);
					unset ($m);
					unset ($n);
					$o = true; // extra fields
					$p = true; // extra customer fields


				if ($GLOBALS['MainListColumnsToShow']['cb_cust'] == "1") $a = true;
				if ($GLOBALS['MainListColumnsToShow']['cb_owner'] == "1") $b = true;
				if ($GLOBALS['MainListColumnsToShow']['cb_assignee'] == "1") $c = true;
				if ($GLOBALS['MainListColumnsToShow']['cb_status'] == "1") $d = true;
				if ($GLOBALS['MainListColumnsToShow']['cb_priority'] == "1") $e = true;
				if ($GLOBALS['MainListColumnsToShow']['cb_duedate'] == "1") $f = true;
				if ($GLOBALS['MainListColumnsToShow']['cb_creationdate'] == "1") $g = true;
				if (!$GLOBALS['MainListColumnsToShow']['cb_duration'] == "1") $notime = true;
				if ($GLOBALS['MainListColumnsToShow']['cb_lastupdate'] == "1") $h = true;
				if ($GLOBALS['MainListColumnsToShow']['cb_category'] == "1") $m = true;
			}


			if (!$encrypt) {
						$outp .= "Entity ID$separator";
						if ($a) { $outp .=  "$lang[customer]$separator"; }
						if ($b) { $outp .=  "$lang[owner]$separator"; }
						if ($c) { $outp .=  "$lang[assignee]$separator"; }
						if ($d) { $outp .=  "$lang[status]$separator"; }
						if ($e) { $outp .=  "$lang[priority]$separator"; }
						if ($f) { $outp .=  "$lang[duedate]$separator"; }
						if ($g) { $outp .=  "$lang[creationdate]$separator"; }
						if ($h) { $outp .=  "$lang[lastupdatedate]$separator"; }
						if ($i) { $outp .=  "$lang[lastupdatetime]$separator"; }
						if ($j) { $outp .=  "$lang[closedate]$separator"; }
						if ($k) { $outp .=  "$lang[waitingcsv]$separator"; }
						if ($l) { $outp .=  "$lang[doesntcsv]$separator"; }
						if ($m) { $outp .=  "$lang[category]$separator"; }
						if ($n) { $outp .=  "$lang[contents]$separator"; }
						if (!$notime) $outp .= "Time" . $separator;
			
					// Extra fields

					if ($o) { 

						$list = GetExtraFields();

						foreach ($list AS $field) {				
								if ($_REQUEST['ListLayout']) {
									if ($GLOBALS['MainListColumnsToShow']["EFID" . $field['id']] == "1") {
											$outp .= $field['name'] . $separator;
//											qlog($element . " is true in ezport *************");
									}

								} else {
									$outp .= $field['name'] . $separator;
								}
						}
						unset($list);
						unset($sql);
					}

			// customer data
			if ($p) {
							if ($_REQUEST['ListLayout'] && $RealExcel) {
/*
								Array ( 
									[id] => 1 
									[cb_duedate] => 1 
									[cb_contact] => 1 
									[cb_contact_title] => 1 
									[cb_contact_phone] => 1 
									[cb_contact_email] => 1 
									[cb_cust_address] => 1 
									[cb_cust_remarks] => 1 
									[cb_cust_homepage] => 1 
								) 
*/
								if ($GLOBALS['MainListColumnsToShow']['cb_cust'] == "1") $outp .= $lang['customer'] . $separator;
								if ($GLOBALS['MainListColumnsToShow']['cb_contact'] == "1") $outp .= $lang['contact'] . $separator;
								if ($GLOBALS['MainListColumnsToShow']['cb_contact_title'] == "1") $outp .= $lang['contacttitle'] . $separator;
								if ($GLOBALS['MainListColumnsToShow']['cb_contact_phone'] == "1") $outp .= $lang['contactphone'] . $separator;
								if ($GLOBALS['MainListColumnsToShow']['cb_contact_email'] == "1") $outp .= $lang['contactemail'] . $separator;
								if ($GLOBALS['MainListColumnsToShow']['cb_cust_address'] == "1") $outp .= $lang['customeraddress'] . $separator;
								if ($GLOBALS['MainListColumnsToShow']['cb_cust_remarks'] == "1") $outp .= $lang['custremarks'] . $separator;
								if ($GLOBALS['MainListColumnsToShow']['cb_cust_homepage'] == "1") $outp .= $lang['custhomepage'] . $separator;

							} else {
								$outp .= $lang['customer'] . $separator;
								$outp .= $lang['contact'] . $separator;
								$outp .= $lang['contacttitle'] . $separator;
								$outp .= $lang['contactphone'] . $separator;
								$outp .= $lang['contactemail'] . $separator;
								$outp .= $lang['customeraddress'] . $separator;
								$outp .= $lang['custremarks'] . $separator;
								$outp .= $lang['custhomepage'] . $separator;
							}
							$list2 = GetExtraCustomerFields();
							foreach ($list2 AS $field) {

								if ($_REQUEST['ListLayout']) {
									if ($GLOBALS['MainListColumnsToShow']["EFID" . $field['id']] == "1") {
										$outp .= $field['name'] . $separator;
									}
								} else {
									$outp .= $field['name'] . $separator;
								}
							}
			}

			$outp = ereg_replace("\n","",$outp);
			$outp = ereg_replace("\012","",$outp);
			$outp = ereg_replace("\015","",$outp);
			
			if ($RealExcel) {
				array_push($excel,$outp); // push header in array instead of printing it
			} else {
				print "$outp\n";
			}	
			unset($outp); // clear buffer
			$unique = array();
			while ($ee= mysql_fetch_array($result)) {
					
					if ($ee['closeepoch']=="") {
						$nowepoch = date('U');
						$age_in_seconds = $nowepoch - $ee['openepoch'];
						$txt = "Age"; 
					} else {
						$nowepoch = $ee['closeepoch'];
						$age_in_seconds = $ee['closeepoch'] - $ee['openepoch'];
						$txt = "Duration";
					}
					if ($ee['openepoch']=="") {
						$age = "n/a";
					} else {
						if ($age_in_seconds>86400) {
							$age = "($txt: " . round($age_in_seconds/86400,2) . " days)";
						} elseif ($age_in_seconds>3600) {
							$age = "($txt: " . round($age_in_seconds/3600,2) . " hours)";
						} elseif ($age_in_seconds>60) {
							$age = "($txt: " . round($age_in_seconds/60,2) . " minutes)";
						} elseif ($age_in_seconds<>$nowepoch) {
							$age = "($txt: " . round($age_in_seconds,0) . " seconds)";
						} 

					}
					if (!in_array($ee[eid],$unique)) {
								array_push($unique,$ee[eid]) ;
								
								$e1['FULLNAME'] = GetUserName($ee['assignee']);
								$e2['FULLNAME'] = GetUserName($ee['owner']);
								$e1['name'] = GetUserName($ee['assignee']);
									
								if ($ee[waiting] == 'y') { $waiting = "Yes"; } else { $waiting = ""; }
								if ($ee[obsolete] == 'y') { $obsolete = "Yes"; } else { $obsolete = ""; }
								if ($ee[closedate] == '0000-00-00') { $ee[closedate]=''; }
								$t = $ee[tp]; // timestamp last edit
								$t = str_replace("-","",$t);
								$t = str_replace(" ","",$t);
								$t = str_replace(":","",$t);
								$tp[jaar] = substr($t,0,4);
								$tp[maand] = substr($t,4,2);
								$tp[dag] = substr($t,6,2);
								$tp[uur] = substr($t,8,2);
								$tp[min] = substr($t,10,2);

								$ee[content] = ereg_replace("\"","'",$ee[content]);

							if ($separator==";") {
										$ee[content] = ereg_replace(";",":",$ee[content]);
							} elseif ($separator==":") {
										$ee[content] = ereg_replace(":",";",$ee[content]);					
							}
							$cont = $ee[content];			


							if ($encrypt) {
								$outp .= "\"$ee[eid]\"$separator";
								if ($a) { $outp .= base64_encode($ee[custname]) .  $separator; }
								if ($b) { $outp .= base64_encode($e1[name]) .  $separator; }
								if ($c) { $outp .= base64_encode($ee[name]) .  $separator; }
								if ($d) { $outp .= base64_encode($ee[status]) .  $separator; }
								if ($e) { $outp .= base64_encode($ee[priority]) .  $separator; }
								if ($f) { $outp .= base64_encode(TransformDate($ee[duedate])) .  $separator; }
								//   "2004-03-17"
								//	  0123456789
								$t = $ee[cdate];
								$cd[jaar] = substr($t,0,4);
								$cd[maand] = substr($t,5,2);
								$cd[dag] = substr($t,8,2);

								if ($g) { $outp .= TransformDate("$cd[dag]-$cd[maand]-$cd[jaar]") . $separator; }
								
								//   "2004-03-17"
								//	  0123456789
								$t = $ee[closedate];
								$cd[jaar] = substr($t,0,4);
								$cd[maand] = substr($t,5,2);
								$cd[dag] = substr($t,8,2);

								if ($h) { $outp .= TransformDate("$cd[dag]-$cd[maand]-$cd[jaar]") . $separator; }

								if ($k) { $outp .= base64_encode($ee[waiting]) . $separator; }
								if ($l) { $outp .= base64_encode($ee[obsolete]) . $separator; }
								if ($m) { $outp .= base64_encode($ee[category]) . $separator; }
								if ($n) { $outp .= base64_encode($ee[content]) . $separator; }
								$outp .= base64_encode($age) . $separator;
							} elseif ($RealExcel) {
								$outp .= "$ee[eid]$separator";
								if ($a) { $outp .= "$ee[custname]$separator"; }
								if ($b) { $outp .= "$e2[FULLNAME]$separator"; }
								if ($c) { $outp .= "$e1[FULLNAME]$separator"; }

								if ($d) { 
									$outp .= "@@@@HEXCOLOR" . GetStatusColor($ee['status']) . "@@@@HEXCOLOR" . $ee[status]. $separator; 
								}
								if ($e) { 
									$outp .= "@@@@HEXCOLOR" . GetPriorityColor($ee['priority']) . "@@@@HEXCOLOR" . $ee[priority] . $separator; 
								}

								if ($f) { 
									$sqldate = date('Y-m-d');
									$date = date('d-m-Y');

									if ($ee[duedate]==$date) {
										$outp .= "@@@@HEXCOLOR#66FF99@@@@HEXCOLOR" . TransformDate($ee[duedate]) . $separator;
									}
									elseif ($ee[sqldate]<$sqldate) {
										$outp .= "@@@@HEXCOLOR#FF6699@@@@HEXCOLOR" . TransformDate($ee[duedate]) . $separator;
									} 
									else 
									{
										$outp .= TransformDate($ee[duedate]) . $separator; 
									}	
								}

								//if ($g) { $outp .= TransformDate($ee[cdate]) . $separator; }
								

								//   "2004-03-17"
								//	  0123456789
								$t = $ee[cdate];
								$cd[jaar] = substr($t,0,4);
								$cd[maand] = substr($t,5,2);
								$cd[dag] = substr($t,8,2);

								if ($g) { $outp .= TransformDate("$cd[dag]-$cd[maand]-$cd[jaar]") . $separator; }

									
								if ($h) { $outp .= TransformDate("$tp[dag]-$tp[maand]-$tp[jaar]") . $separator; }
								if ($i) { $outp .= "$tp[uur]:$tp[min]h.$separator"; }
								
								//   "2004-03-17"
								//	  0123456789
								$t = $ee[closedate];
								$cd[jaar] = substr($t,0,4);
								$cd[maand] = substr($t,5,2);
								$cd[dag] = substr($t,8,2);

								if ($j) { $outp .= TransformDate("$cd[dag]-$cd[maand]-$cd[jaar]") . $separator; }
								//if ($j) { $outp .= TransformDate($ee[closedate]) . $separator; }
								if ($k) { $outp .= "$waiting$separator"; }
								if ($l) { $outp .= "$obsolete$separator"; }
								if ($m) { $outp .= "@@@@WRAPPED@@@@$ee[category]$separator"; }
								if ($n) { $outp .= "@@@@WRAPPED@@@@$cont$separator"; }
								if (!$notime) {
									$outp .= $age . $separator;
								}

							} else {
								$outp .= "\"$ee[eid]\"$separator";
								if ($a) { $outp .= "\"$ee[custname]\"$separator"; }
								if ($b) { $outp .= "\"$e2[FULLNAME]\"$separator"; }
								if ($c) { $outp .= "\"$e1[FULLNAME]\"$separator"; }
								if ($d) { $outp .= "\"$ee[status]\"$separator"; }
								if ($e) { $outp .= "\"$ee[priority]\"$separator"; }
								if ($f) { $outp .= "\"" . TransformDate($ee[duedate]) . "\"$separator"; }
								if ($g) { $outp .= "\"" . TransformDate($ee[cdate]) . "\"$separator"; }
								if ($h) { $outp .= "\"" . TransformDate("$tp[dag]-$tp[maand]-$tp[jaar]") . "\"$separator"; }
								if ($i) { $outp .= "\"$tp[uur]:$tp[min]h.\"$separator"; }
								if ($j) { $outp .= "\"" . TransformDate($ee[closedate]) . "\"$separator"; }
								if ($k) { $outp .= "\"$waiting\"$separator"; }
								if ($l) { $outp .= "\"$obsolete\"$separator"; }
								if ($m) { $outp .= "\"$ee[category]\"$separator"; }
								if ($n) { $outp .= "\"$cont\"$separator"; }

								$outp .= $age . $separator;
							}
							if ($o) { // extra fields
				
								$list = GetExtraFields();

				//				print_r($list);

								foreach ($list AS $field) {
//									qlog("PROCESSING $list[$x]");
										$sql2 = "SELECT * FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='$ee[eid]' AND deleted<>'y' AND name='" . $field['id'] . "' AND type<>'cust' ORDER BY name";
					//					$outp .= $sql2;
										$result2 = mcq($sql2,$db);
										$list2 = mysql_fetch_array($result2);

										if ($tmp = unserialize($list2['value'])) {
											foreach ($tmp AS $rij) {
												$lis2['value'] .= base64_decode($rij) . " - ";
											}
										}

										if ($field['fieldtype']=="date") {
											$list2['value'] = TransFormDate($list2['value']);
										} elseif (strstr($field['fieldtype'],"User-list")) {
											$list2['value'] = GetUserName($list2['value']);
										}

										if (trim($list2['value'])=="") {
											$list2['value']="-";
										} 

										if ($encrypt) {
												$outp .= "" . base64_encode($list2[value]) . "" . $separator;
										} elseif ($RealExcel) {
												if ($_REQUEST['ListLayout']) {
													if ($GLOBALS['MainListColumnsToShow']["EFID" . $field['id']] == "1") {
															$outp .= $list2['value'] . $separator;
													}
												} else {
													$outp .= $list2['value'] . $separator;
												}
												
										} else {
												$outp .= "\"" . $list2['value'] . "\"" . $separator;
										}

									}
							
								}
							if ($p) { // customer data
										$list = GetExtraCustomerFields();
										$ee_cust = db_GetRow("SELECT * FROM $GLOBALS[TBL_PREFIX]customer WHERE id='" . $ee['CRMcustomer'] . "'");

											if ($RealExcel) {
												if ($_REQUEST['ListLayout']) {
													if ($GLOBALS['MainListColumnsToShow']['cb_cust'] == "1") $outp .= $ee_cust['custname'] . $separator;
													if ($GLOBALS['MainListColumnsToShow']['cb_contact'] == "1") $outp .= $ee_cust['contact'] . $separator;
													if ($GLOBALS['MainListColumnsToShow']['cb_contact_title'] == "1") $outp .= $ee_cust['contact_title'] . $separator;
													if ($GLOBALS['MainListColumnsToShow']['cb_contact_phone'] == "1") $outp .= $ee_cust['contact_phone'] . $separator;
													if ($GLOBALS['MainListColumnsToShow']['cb_contact_email'] == "1") $outp .= $ee_cust['contact_email'] . $separator;
													if ($GLOBALS['MainListColumnsToShow']['cb_cust_address'] == "1") $outp .= "@@@@WRAPPED@@@@" . $ee_cust['cust_address'] . "" . $separator;
													if ($GLOBALS['MainListColumnsToShow']['cb_cust_remarks'] == "1") $outp .= "@@@@WRAPPED@@@@" . $ee_cust['cust_remarks'] . "" . $separator;
													if ($GLOBALS['MainListColumnsToShow']['cb_cust_homepage'] == "1") $outp .= $ee_cust['cust_homepage'] .  $separator;
												} else {
													$outp .= "" . $ee_cust['custname'] . "" . $separator;
													$outp .= "" . $ee_cust['contact'] . "" . $separator;
													$outp .= "" . $ee_cust['contact_title'] . "" . $separator;
													$outp .= "" . $ee_cust['contact_phone'] . "" . $separator;
													$outp .= "" . $ee_cust['contact_email'] . "" . $separator;
													$outp .= "@@@@WRAPPED@@@@" . $ee_cust['cust_address'] . "" . $separator;
													$outp .= "@@@@WRAPPED@@@@" . $ee_cust['cust_remarks'] . "" . $separator;
													$outp .= "" . $ee_cust['cust_homepage'] . "" . $separator;
												}
											} else {
												$outp .= "\"" . $ee_cust['custname'] . "\"" . $separator;
												$outp .= "\"" . $ee_cust['contact'] . "\"" . $separator;
												$outp .= "\"" . $ee_cust['contact_title'] . "\"" . $separator;
												$outp .= "\"" . $ee_cust['contact_phone'] . "\"" . $separator;
												$outp .= "\"" . $ee_cust['contact_email'] . "\"" . $separator;
												$outp .= "\"" . $ee_cust['cust_address'] . "\"" . $separator;
												$outp .= "\"" . $ee_cust['cust_remarks'] . "\"" . $separator;
												$outp .= "\"" . $ee_cust['cust_homepage'] . "\"" . $separator;
											}				

											foreach ($list AS $field) {
												$list4 = db_GetRow("SELECT * FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='$ee_cust[id]' AND deleted<>'y' AND name='" . $field['id'] . "' AND type='cust' ORDER BY name");

												if ($field['fieldtype'] == "List of values") {

													$tmp = unserialize($list4['value']);
													unset($list4['value']);
													if (is_array($tmp)) {
														$list4['value'] = "";
														foreach ($tmp AS $rij) {
															$list4['value'] .= base64_decode($rij) . " - ";
														}
													} else {
														$list4['value'] = "-";
													}
												}

												if ($field['fieldtype']=="date") {
													$list4[value] = TransFormDate($list4[value]);
												} elseif (strstr($field['fieldtype'],"User-list")) {
													$list4['value'] = GetUserName($list4['value']);
												}
												if ($_REQUEST['ListLayout']) {
													if ($GLOBALS['MainListColumnsToShow']["EFID" . $field['id']] == "1") {
															$outp .= $list4['value'] . $separator;
														}
												} else {
														$outp .= $list4['value'] . $separator;
												}
												
											}

										
							}

						if (CheckEntityAccess($ee['eid']) == "ok" || CheckEntityAccess($ee['eid']) == "readonly") { // only if access is allowed
							if ($RealExcel) {
								array_push($excel,$outp); // push header in array instead of printing it
							} else {
								print "$outp\n";
							}	
						}
						unset($outp);

						$teller++;
						}
			}

			if ($RealExcel) {
					$GLOBALS['CURFUNC'] = "GenerateExcel::";
					uselogger("MS Excel Export downloaded","");
					qlog("Excel sheet array done");
					RealExcel($excel,$separator);
					qlog("Excel sheet generated - quit.");

			} else {
				$GLOBALS['CURFUNC'] = "GenerateCSV::";
				print "\n";
				if (!$encrypt) {
					if (!$teller) {
						print "$lang[noresults]\n";
					} else {
						print "$teller $lang[entitiesfound]\n";
					}

					print "$lang[repdownloaded] ". date("F j, Y, H:i") . "h.\n";
					print "(C) $title - $CRM_VERSION.\n";
					print "$lang[endofreport].";
					uselogger("CSV Export downloaded","");
					qlog("CSV sheet done");
				} else {
					uselogger("CSV Export encrypted downloaded","");
					qlog("CSV encrypted sheet done");
				}
			}
		}
	}
  }
?>
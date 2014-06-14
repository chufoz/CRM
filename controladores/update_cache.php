<?
/* ********************************************************************
 * $GLOBALS[TBL_PREFIX] 
 * Copyright (c) 2001-2003 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This script checks the repository currently logged onto for errors and
 * inconsistencies, and optimizes all tables.
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
require("config.inc.php");
require("functions.php");
require("class.phpmailer.php");

$GLOBALS['logqueries']    = false;   // logs all queries (10% slower)
$GLOBALS['logtext']       = false;   // logs all comments - alternative: user-num. Logs only that user. (25% slower)
$GLOBALS['query_timer']   = false;   // logs slowest SQL query
$GLOBALS['qlog_onscreen'] = false;   // displays pop-up containing log
$GLOBALS['ShowTraceLink'] = false;   // displays qlog trace link at end of page (same 25% slower as 'logtext')

$times = 500; // number of max checks

// As this function cannot hurt anyone, it doesn't require authorisation.

if ($argv[1] == "-f") {
		$GLOBALS['force_update'] = true;
} elseif ($argv[1] == "-c") {
		$ac = true;
} elseif ($argv[1] == "-fc") {
		$fc = true;
		if ($argv[2]) {
			$db_to_process = $argv[2];
		}
} else {
		$db_to_process = $argv[1];
} 

if ($argv[2] == "-f") {
		$GLOBALS['force_update'] = true;
} elseif ($argv[2] == "-c") {
		$ac = true;
} elseif ($argv[2] <> "") {
		$db_to_process = $argv[2];
}

if ($ac) {
	CheckIfCacheIsUsefull($db_to_process);
	exit;
}

//if ($fc) {
//	UpdateEntityFormCache();
//	exit;
//}

print "\nUpdate access cache tables of all repositories\n";
if ($GLOBALS['force_update']) {
	print "Forcing updates...\n";
}
UpdateAllCacheTables($db_to_process);

function UpdateAllCacheTables($db_to_process) {
		global $user, $pass, $host, $database,$table_prefix, $fc;
		if (sizeof($pass)>0) {
			for ($r=0;$r<64;$r++) {
					if ($db_to_process == $database[$r] || $db_to_process == "") {
						if ($db = @mysql_connect($host[$r], $user[$r], $pass[$r])) {
							if (@mysql_select_db($database[$r],$db)) {
								$GLOBALS['TBL_PREFIX'] = $table_prefix[$r];
								if ($GLOBALS['TBL_PREFIX']=="")  $GLOBALS['TBL_PREFIX'] ="CRM";
								do_language();
								print_r($lang);
								print "\nUpdating access cache tables on database " . $database[$r] . ":\n";
								if (!$fc) {
									print "\nUpdating access cache tables on database " . $database[$r] . ":\n";
									UpdateCacheTables(true,false);
								} else {
									print "\nUpdating entity form cache on database " . $database[$r] . ":\n";
									UpdateEntityFormCache(true,false);
								}
								
							} else {
								print "\n$rHost: $host[$r] Database: $database[$r] - Couldn't select database - Database could not be contacted\n";
							}

						} else {
							 print "\n$rHost: $host[$r] Database: $database[$r] - Database host unreachable\n";
						}
					} else {
						//print "\nSkipping " . $database[$r] . " on request.\n";
					}
				}
		}
}
function CheckIfCacheIsUsefull($db_to_process) {
		global $user, $pass, $host, $database, $table_prefix, $times;

		if ($db_to_process == "") {
			print "\nError: a database name must be given.\n";
			exit;			
		}

		$nu = date('U');
		$toen = $nu - 7257600;
		print "\nCRM-CTT Cache-check. This routine will tell you if using extended caching is useful.\n";
		print "It will now run a check against the given database. It is recommended to run this test\n";
		print "several times to get trustworthy results.\n\n";

		print "Checking w/o cache...\n";
		unset($GLOBALS['CheckedCustomerAccessArray']);
		unset($GLOBALS['CheckedEntityAccessArray']);



		$stt = microtime_float();
		if (sizeof($pass)>0) {
		for ($r=0;$r<64;$r++) {
			
			if ($db_to_process == $database[$r] || $db_to_process == "") {
				if ($db = @mysql_connect($host[$r], $user[$r], $pass[$r])) {
					if (@mysql_select_db($database[$r],$db)) {
						$GLOBALS['TBL_PREFIX'] = $table_prefix[$r];
						if ($GLOBALS['TBL_PREFIX']=="")  $GLOBALS['TBL_PREFIX'] ="CRM";

								$efl = GetExtraFields();
								$ecl = GetExtraCustomerFields();

							$res = mcq("SELECT id FROM " . $GLOBALS['TBL_PREFIX'] . "loginusers WHERE name NOT LIKE 'deleted_user_%' " . $sql_ins . " ORDER BY id", $db);
							while ($row = mysql_fetch_array($res)) {
								$uid = $row['id'];
								$res2 = mcq("SELECT eid from " . $GLOBALS['TBL_PREFIX'] . "entity WHERE deleted<>'y' AND UNIX_TIMESTAMP(tp) > " . $toen, $db);
								while ($row2 = mysql_fetch_array($res2)) {
									
									// Entity access check w/o cache
									$al = CheckEntityAccess($row2['eid'], $uid, true);

									// Customer access check w/o cache
									$ax = GetNumOfAttachments($row2['eid']);

									// Extra entity field value get w/o cache
									foreach($efl AS $field) {
										$bla = db_GetRow("SELECT value FROM " . $GLOBALS['TBL_PREFIX'] . "customaddons WHERE eid='" . $row2['eid'] . "' AND type<>'cust' AND name='" . $field['id']. "'");
									}
									

									//print $al . "\n";
									$cc++;
									if ($cc>$times) break;
								}
							}
							unset($cc);
							$res = mcq("SELECT id FROM " . $GLOBALS['TBL_PREFIX'] . "loginusers WHERE name NOT LIKE 'deleted_user_%' " . $sql_ins . " ORDER BY id", $db);
							while ($row = mysql_fetch_array($res)) {
								$uid = $row['id'];
								$res2 = mcq("SELECT id from " . $GLOBALS['TBL_PREFIX'] . "customer", $db);
								while ($row2 = mysql_fetch_array($res2)) {
									$al = CheckCustomerAccess($row2['id'], $uid, true);
									// Extra customer field value get w/o cache
									foreach($ecl AS $field) {
										$bla = db_GetRow("SELECT value FROM " . $GLOBALS['TBL_PREFIX'] . "customaddons WHERE eid='" . $row2['eid'] . "' AND type<>'cust' AND name='" . $field['id'] . "'");
									}
									//print $al . "\n";
									$cc++;
									if ($cc>$times) break;

								}
							}
					} else {
						print "\n$rHost: $host[$r] Database: $database[$r] - Couldn't select database - Database could not be contacted\n";
					}

				} else {
					 print "\n$rHost: $host[$r] Database: $database[$r] - Database host unreachable\n";
				}
			} else {
				//print "\nSkipping " . $database[$r] . " on request.\n";
			}
		}
	}

	

	$ett = microtime_float();
	unset($cc);
	$dtt = $ett - $stt;
	print "\nDuration: " . $dtt . "\n";
	print "\nCreating cache arrays...\n";
	//$GLOBALS['force_update'] = true;

	UpdateAllCacheTables($db_to_process);
	print "\nPreparing...\n";

	$sql = "SELECT eid FROM " . $GLOBALS['TBL_PREFIX'] . "entity WHERE deleted<>'y'";
	$res = mcq($sql,$db);
	$arr = array();
	while($row = mysql_fetch_array($res)) {
		array_push($arr, $row['eid']);
	}

	$GLOBALS['USE_EXTENDED_CACHE'] = true;

	print "\n";
	ClearCacheArrays(true);
	$stt2 = microtime_float(); // Start timer
	
	print "\nChecking WITH cache....\n";
	if (sizeof($pass)>0) {
		for ($r=0;$r<64;$r++) {
			
			if ($db_to_process == $database[$r] || $db_to_process == "") {
				if ($db = @mysql_connect($host[$r], $user[$r], $pass[$r])) {
					if (@mysql_select_db($database[$r],$db)) {
						$GLOBALS['TBL_PREFIX'] = $table_prefix[$r];

							$ExtraFieldsList = GetExtraFields();
							$ExtraCustomerFieldsList = GetExtraCustomerFields();
							BuildNumOfAttachmentsCache($arr);
//							print "\nReading cache arrays...\n";
							$res = mcq("SELECT eidcid,type,result FROM " . $GLOBALS['TBL_PREFIX'] . "accesscache WHERE user='" . $GLOBALS['USERID'] . "'", $db);
							while ($row = mysql_fetch_array($res)) {
								if ($row['type'] == "c") {
									$GLOBALS['CheckedCustomerAccessArray'][$row['eidcid']] = $row['result'];
								} else {
									$GLOBALS['CheckedEntityAccessArray'][$row['eidcid']] = $row['result'];
								}
							}

							// EXT CACHE
							if($GLOBALS['USE_EXTENDED_CACHE']) {
								$ef_array = array();
								$ec_array = array();
								$zx=0;		
								$sql = "SELECT value,name,eid,id FROM $GLOBALS[TBL_PREFIX]customaddons WHERE type<>'cust'";
								if ($filter=="viewdel") {
									$sql = "SELECT $GLOBALS[TBL_PREFIX]customaddons.value,$GLOBALS[TBL_PREFIX]customaddons.name,$GLOBALS[TBL_PREFIX]customaddons.eid FROM $GLOBALS[TBL_PREFIX]customaddons, $GLOBALS[TBL_PREFIX]entity WHERE ($GLOBALS[TBL_PREFIX]customaddons.eid=$GLOBALS[TBL_PREFIX]entity.eid) AND $GLOBALS[TBL_PREFIX]entity.deleted='y' AND $GLOBALS[TBL_PREFIX]customaddons.type<>'cust'";
								} else {
									$sql = "SELECT $GLOBALS[TBL_PREFIX]customaddons.value,$GLOBALS[TBL_PREFIX]customaddons.name,$GLOBALS[TBL_PREFIX]customaddons.eid FROM $GLOBALS[TBL_PREFIX]customaddons, $GLOBALS[TBL_PREFIX]entity WHERE ($GLOBALS[TBL_PREFIX]customaddons.eid=$GLOBALS[TBL_PREFIX]entity.eid) AND $GLOBALS[TBL_PREFIX]entity.deleted<>'y' AND $GLOBALS[TBL_PREFIX]customaddons.type<>'cust'";
								}
								$resx = mcq($sql,$db);
								while ($resxrow = mysql_fetch_array($resx)) {
									$ef_array[$resxrow['eid']][$resxrow['name']] = $resxrow['value'];
									$zx++;
								}
								qlog("Loaded entire EF table: $zx records. (EXTENTED_CACHE)");
								$zx=0;
								$sql = "SELECT value,name,eid,id FROM $GLOBALS[TBL_PREFIX]customaddons WHERE type='cust'";
								$resx = mcq($sql,$db);
								while ($resxrow = mysql_fetch_array($resx)) {
									$ec_array[$resxrow['eid']][$resxrow['name']] = $resxrow['value'];
									$zx++;
								}
								qlog("Loaded entire ECF table: $zx records. (EXTENTED_CACHE)");
							}


						if ($GLOBALS['TBL_PREFIX']=="")  $GLOBALS['TBL_PREFIX'] ="CRM";

							$res = mcq("SELECT id FROM " . $GLOBALS['TBL_PREFIX'] . "loginusers WHERE name NOT LIKE 'deleted_user_%' " . $sql_ins . " ORDER BY id", $db);
							while ($row = mysql_fetch_array($res)) {
								$res2 = mcq("SELECT eid from " . $GLOBALS['TBL_PREFIX'] . "entity WHERE deleted<>'y' AND UNIX_TIMESTAMP(tp) > " . $toen, $db);
								$uid = $row['id'];
								while ($row2 = mysql_fetch_array($res2)) {
									$al = CheckEntityAccess($row2['eid'], $uid, true);
									$ax = GetNumOfAttachments($row2['eid']);
									
									foreach ($ExtraFieldsList AS $field) {
									$element = "EFID" . $field['id'];

									if($GLOBALS['USE_EXTENDED_CACHE']) {
											$gh[0] = $ef_array[$e['eid']][$field['id']];
									} else {
										$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE type<>'cust' AND eid='" . $e['eid'] . "' AND name='" . $field['id'] ."' LIMIT 1";
										$result33 = mcq($sql,$db);
										$gh = mysql_fetch_array($result33);
									}

									if ($field['fieldtype'] == "text area") {
										$gh[0] = eregi_replace("\n","<BR>",$gh[0]);
									}
									if ($field['fieldtype'] == "date") {
										$gh[0] = TransFormDate($gh[0]);
									} elseif (strstr($field['fieldtype'],"User-list")) {
										$gh[0] = GetUserName($gh[0]);
									}

									
									$tab_depth++;

									if ($field['fieldtype'] == "numeric" || $field['fieldtype'] == "invoice cost" || $field['fieldtype'] == "invoice qty" || $field['fieldtype'] == "invoice cost including VAT") {
										// This is a numeric extra field, we'll add it up!
										$element = "EFID" . $field['id'];
										if (!is_array($sums[$field['id']])) {
											$sums[$field['id']] = array();
											$sums[$field['id']]['name']			= $field['name'];
											$sums[$field['id']]['id']			= $field['id'];
											$sums[$field['id']]['tab_depth']	= $tab_depth;
										}
										$sums[$field['id']]['sum'] += $gh['value'];
										$to_sum = true;
										if ($GLOBALS['DateFormat'] == "dd-mm-yyyy") {
											$gh[0] = number_format($gh[0],2,',','.');
										} else {
											$gh[0] = number_format($gh[0],2,'.',',');
										}
									}
									$outputbuffer .= "<td>" . $gh[0] . "&nbsp;</td>";									
									}
									foreach ($ExtraCustomerFieldsList AS $field) {
								$element = "EFID" . $field['id'];
								
									if($GLOBALS['USE_EXTENDED_CACHE']) {
										$gh[0] = $ec_array[$e['eid']][$field['id']];
									} else {
										$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE type='cust' AND eid='" . $e['CRMcustomer'] . "' AND name='" . $field['id'] ."' LIMIT 1";
										$result33 = mcq($sql,$db);
										$gh = mysql_fetch_array($result33);
									}
									
									if ($field['fieldtype'] == "text area") {
										$gh[0] = eregi_replace("\n","<BR>",$gh[0]);
									}
									if ($field['fieldtype'] == "date") {
										$gh[0] = TransFormDate($gh[0]);
									}

									$outputbuffer .= "<td>" . $gh[0] . "&nbsp;</td>";
									$tab_depth++;

									if ($field['fieldtype'] == "numeric" || $field['fieldtype'] == "invoice cost" || $field['fieldtype'] == "invoice qty" || $field['fieldtype'] == "invoice cost including VAT") {
										// This is a numeric extra field, we'll add it up!
										$element = "EFID" . $field['id'];
										if (!is_array($sums[$field['id']])) {
											$sums[$field['id']] = array();
											$sums[$field['id']]['name']			= $field['name'];
											$sums[$field['id']]['id']			= $field['id'];
											$sums[$field['id']]['tab_depth']	= $tab_depth;
										}
										$sums[$field['id']]['sum'] += $gh['value'];
										$to_sum = true;
									}
									
								
							}


									$cc++;
									if ($cc>$times) break;
								}
							}
							unset($cc);
							$res = mcq("SELECT id FROM " . $GLOBALS['TBL_PREFIX'] . "loginusers WHERE name NOT LIKE 'deleted_user_%' " . $sql_ins . " ORDER BY id", $db);
							while ($row = mysql_fetch_array($res)) {
								$uid = $row['id'];
								$res2 = mcq("SELECT id from " . $GLOBALS['TBL_PREFIX'] . "customer", $db);
								while ($row2 = mysql_fetch_array($res2)) {
									$al = CheckCustomerAccess($row2['id'], $uid, true);
									//print $al . "\n";
									$cc++;
									if ($cc>$times) break;

								}
							}
					} else {
						print "\n$rHost: $host[$r] Database: $database[$r] - Couldn't select database - Database could not be contacted\n";
					}

				} else {
					 print "\n$rHost: $host[$r] Database: $database[$r] - Database host unreachable\n";
				}
			} else {
				//print "\nSkipping " . $database[$r] . " on request.\n";
			}
		}
	}
	$ett2 = microtime_float();
	$dtt2 = $ett2 - $stt2;
	print "\nDuration NO CACHE: " . $dtt . "";
	print "\nDuration    CACHE: " . $dtt2 . "\n";

	if ($dtt<$dtt2) {
		$pc1= $dtt2/100;
		$pc = 100-($dtt/$pc1);
		print "\nWith cache was " . $pc . "% slower in this test. Bummer.\n\n";
	} else {
		$pc1= $dtt/100; // = 1%
		$pc = 100-($dtt2/$pc1);
		print "\nWith cache was " . $pc . "% faster in this test.\n\n";	
	}

}

// Journalling function (Entity ID, Message)
function journal($eid,$msg,$JournalType="entity") {
	global $EnableEntityJournaling;
	if (strtoupper($EnableEntityJournaling)=="YES" || (stristr($msg,"[admin]"))) {
		
		$msg = addslashes($msg);

		// $msg = base64_encode($msg);

		$sql = "INSERT INTO " . $GLOBALS[TBL_PREFIX] ."journal (eid,user,message,type) VALUES('$eid','" . $GLOBALS[USERID] . "','$msg','" . $JournalType ."')";

		mcq($sql,$db);
	}
}



?>
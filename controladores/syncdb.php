<?
/* ********************************************************************
 * $GLOBALS[TBL_PREFIX] 
 * Copyright (c) 2001-2003 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

if ($web) {
	include("header.inc.php");
	print "</td></tr></table>";
	AdminTabs();
	MainAdminTabs("ieb");
} else {
	require("config.inc.php");
	require("functions.php");

	print "Database copy\n\n";
	if ($argv[1]=="-help" || $argv[1]=="--help" || $argv[1]=="help" || $argv[1]=="-h") {
		print "\nUsage:\n";
		print "\t[no arguments]\t:Interactive\n";
		print "or:\n";
		print "\t[reposnr] [user] [pass] [y|n] - (y = auto copy, n = prompt)\n";
		print "\nExample: php -q syncdb.php 0 admin admin_pwd y\n\n";
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
	//include("sumpdf.php");
	include("language.php");
	
}


if ($web) {
	print "</table><table border=0 width='65%'><tr><td>&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size=+1>Database table copy</font>&nbsp;</legend>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=+2>" . $title . "</font><table border=0 width='100%'>";
}

MustBeAdmin();

if (sizeof($host)<2) { // make sure this installation has more than 1 repository
		print "<img src='error.gif'>&nbsp;You need more than 1 repository to use this function<br>";
		print "<br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password&joepie=1'>Back to main administration page</a>";
		uselogger("Customer table copy denied - this installation has only 1 repository","");
	
} else {

	if ($input) {
		
		if (strtoupper($GLOBALS['HideCustomerTab'])<>"YES") {
				print "&nbsp;&nbsp;<img src='error.gif'>&nbsp;You can only use this when having cofiguration directive HIDECUSTOMERTAB set to 'Yes'.<br>";
				uselogger("Customer table copy denied - HIDECUSTOMERTAB is not switched on","");
		} else { 

			if (!$Confirmed) {
				print "Are you sure you want to import " . strtolower($lang[customers]) . " from repository $daba ?<br><br><font color='#FF0000'>WARNING! All your " . strtolower($lang[customers]) . " in repository $title will be deleted!</font><br><br><img src='arrow.gif'>&nbsp;<a href='syncdb.php?&daba=$daba&Confirmed=1&web=1&input=1' class='bigsort'>Yes</a><br><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='syncdb.php?web=1' style='cursor:pointer'>Back</a>";
				print "<br><br>Summary:<br><br>The following records in your current database ($title) will be <b>deleted</b>:<br>";
				?>
					<ul>
						<li> Customer names, addresses, phone numbers etc. </li>
						<li> All extra fields referring to these customers </li>
						<li> All extra customer field definitions </li>
						<li> All currently existing customer journals </li>
						<li> All currently existing customer file attachments </li>
					</ul>
				Records which <b>will be transferred</b> from the source repository:
					<ul>
						<li> Customer names, addresses, phone numbers etc. </li>
						<li> All extra fields referring to these customers </li>
						<li> All extra customer field definitions </li>
						<li> Customer ownership (check this; your user references may be different in your target repository)</li>
					</ul>
				Records which will <b>not be transferred</b> from the source repository:
					<ul>
						<li> Customer journals </li>
						<li> Files attached to customers </li>
					</ul>
				<?
			} else {
				
				$daba = ereg_replace("r","",$daba);
				$daba = ($daba * 1); // make it an integer

				if ($db2 = @mysql_connect($host[$daba], $user[$daba], $pass[$daba])) {
								if (@mysql_select_db($database[$daba],$db2)) {
										
										$tbl = $table_prefix[$daba];

										$sql = "SELECT value FROM " . $tbl . "settings WHERE setting='title'";
										$result= @mcq($sql,$db2);
										$maxU1 = @mysql_fetch_array($result);
										$title_t = $maxU1[0];

										$sql = "SELECT value FROM " . $tbl . "settings WHERE setting='DBVERSION'";
										$result= @mcq($sql,$db2);
										$maxU1 = @mysql_fetch_array($result);
										$DBVERSION = $maxU1[0];
										
										if ($DBVERSION<>$GLOBALS['DBVERSION']) {
											print "<img src='error.gif'>&nbsp;Database versions not the same! Quitting!";
										} else {

											$sql = "SELECT COUNT(*) FROM " . $tbl . "customer";
											$result= @mcq($sql,$db2);
											$res = @mysql_fetch_array($result);
											$enum = $res[0];

											print "&nbsp;&nbsp;Importing " . $enum . " " . strtolower($lang['customers']) . " from repository $daba (" . $title_t . ")<br><br>";
											
											$sqla = array();

											$sql = "SELECT * FROM " . $tbl . "extrafields WHERE tabletype='customer' AND deleted<>'y'";
											$result= mcq($sql,$db2);
											$old = array();
											$new = array();
											$qs = array();
											
											while ($row = @mysql_fetch_array($result)) {
												// copy extra field settings
												array_push($qs,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "extrafields(id,ordering,tabletype,hidden,fieldtype,name,options,location,deleted) VALUES('','" . $row['ordering'] . "','" . $row['tabletype'] . "','" . $row['hidden'] . "','" . $row['fieldtype'] . "','" . $row['name'] . "','" . $row['options'] . "','" . $row['location'] . "','" . $row['hidden'] . "')");
												array_push($old,$row['id']);
											}

											// Switch to target database
											$dabb = $repository_nr;
											$db = mysql_connect($host[$dabb], $user[$dabb], $pass[$dabb]);
											mysql_select_db($database[$dabb],$db);
											
											$sql = "DELETE FROM " . $GLOBALS['TBL_PREFIX'] . "extrafields WHERE tabletype='customer'";
											mcq($sql,$db);

											foreach ($qs AS $sql) {
												mcq($sql,$db);
												array_push($new,mysql_insert_id());	
											}

											// ... and back to the source again

											$db2 = @mysql_connect($host[$daba], $user[$daba], $pass[$daba]);
											@mysql_select_db($database[$daba],$db2);

											$sql = "SELECT * FROM " . $tbl . "customer";
											$result= @mcq($sql,$db2);
											while ($row = @mysql_fetch_array($result)) {
											
												array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "customer(id, custname, contact, contact_title, contact_phone, contact_email, cust_address, cust_remarks, cust_homepage, active, customer_owner, email_owner_upon_adds) VALUES('" . addslashes($row['id']) . "','" . addslashes($row['custname']) . "','" . addslashes($row['contact']) . "','" . addslashes($row['contact_title']) . "','" . addslashes($row['contact_phone']) . "','" . addslashes($row['contact_email']) . "','" . addslashes($row['cust_address']) . "','" . addslashes($row['cust_remarks']) . "','" . addslashes($row['cust_homepage']) . "','" . addslashes($row['active']) . "','" . addslashes($row['customer_owner']) . "','" . addslashes($row['email_owner_upon_adds']) . "')");

											}
											
											$sql = "SELECT id,eid,type,name,value,deleted,usr FROM " . $tbl . "customaddons WHERE type='cust'";
											$result= @mcq($sql,$db2);
											while ($row = @mysql_fetch_array($result)) {
												for ($t=0;$t<sizeof($old);$t++) {
													if ($old[$t] == $row['name']) {
														// make translation here!
														qlog("Translated fieldname " . $old[$t] . " to " . $new[$t]);
														$row['name'] = $new[$t];
													}
												}
												array_push($sqla,"INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "customaddons(eid,type,name,value,deleted,usr) VALUES('" . addslashes($row['eid']) . "','" . addslashes($row['type']) . "','" . addslashes($row['name']) . "','" . addslashes($row['value']) . "','" . addslashes($row['deleted']) . "','" . addslashes($row['usr']) . "')");
											}
										
										

										//array_push($sqla,"UPDATE " . $GLOBALS['TBL_PREFIX'] . "settings SET value='" . $row['value'] . "' WHERE setting='Extra customer fields list'");

										$daba = $repository_nr;

										$db = mysql_connect($host[$daba], $user[$daba], $pass[$daba]);
										mysql_select_db($database[$daba],$db);

										$sql = "DELETE FROM " . $GLOBALS['TBL_PREFIX'] . "binfiles WHERE type='cust'";
										mcq($sql,$db);

										$sql = "TRUNCATE TABLE " . $GLOBALS['TBL_PREFIX'] . "customer";
										mcq($sql,$db);

										$sql = "DELETE FROM " . $GLOBALS['TBL_PREFIX'] . "customaddons WHERE type='cust'";
										mcq($sql,$db);

										$sql = "DELETE FROM " . $GLOBALS['TBL_PREFIX'] . "journal WHERE type='customer'";
										mcq($sql,$db);
										
										foreach ($sqla AS $row) {
											mcq($row,$db);
											if (strstr($row,"customer(id, custname, contact, contact_title")) {
												journal(mysql_insert_id(),"This record was imported from repository $daba (" . $title_t .")","customer");
											}
										}

										print "&nbsp;&nbsp;Imported " . sizeof($sqla) . " database records.";
										$GLOBALS['CURFUNC'] = "SyncDB::CopyCustomers::";
										qlog("Imported " . sizeof($sqla) . " records from " . $title_t . " into this repository");
										uselogger("Imported " . sizeof($sqla) . " records from " . $title_t . " into this repository","");
										} // end if db version check

								} else {
									print "Couldn't select database";
								}
				} else {
					print "Couldn't connect to database";
				}
			}
		} // end if !HideCustomerTab
	
	print "<br><br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password&joepie=1'>Back to main administration page</a>";

	} else {
		$tables = array("$GLOBALS[TBL_PREFIX]binfiles", "$GLOBALS[TBL_PREFIX]calendar", "$GLOBALS[TBL_PREFIX]customaddons", "$GLOBALS[TBL_PREFIX]customer", "$GLOBALS[TBL_PREFIX]ejournal", "$GLOBALS[TBL_PREFIX]entity", "$GLOBALS[TBL_PREFIX]help", "$GLOBALS[TBL_PREFIX]journal", "$GLOBALS[TBL_PREFIX]languages", "$GLOBALS[TBL_PREFIX]loginusers", "$GLOBALS[TBL_PREFIX]phonebook", "$GLOBALS[TBL_PREFIX]priorityvars", "$GLOBALS[TBL_PREFIX]sessions", "$GLOBALS[TBL_PREFIX]settings", "$GLOBALS[TBL_PREFIX]statusvars", "$GLOBALS[TBL_PREFIX]uselog", "$GLOBALS[TBL_PREFIX]users", "$GLOBALS[TBL_PREFIX]webdav_locks", "$GLOBALS[TBL_PREFIX]webdav_properties");
		
		if ($web) {
		//	print "<br><font size='+2'>Database table copy</font><br><br>";
		} else {
			print "Database table copy\n\n";
			print "This is not yet available using the command line. Exiting.\n\n";
			exit;
		}

		print "Using this procedure you can <b>import</b> a $lang[customer] table from another repository into the $lang[customer] table of this repository. If you want it the other way around, switch to the target repository.<br><br>In other words, with this function you will overwrite all $lang[customers] in repository $title - <b>they will all be deleted</b>";

		print "<br><br><b>This procedure will copy the following items from the chosen repository into this repository:<br>";
		
		print "<ul><li>" . $lang['customer'] . " records</li><li>Extra " . $lang['customer'] . " field values</li><li>Extra " . $lang['customer'] . " field configuration settings</li></ul>";

		print "<br>Please select the repository from which you would like to import customers into this repository:<br>";

		print "<table>";
		print "<tr><td colspan=3><table border='1' width='100%' bgcolor='#F2F2F2' cellspacing='0' cellpadding='8' bordercolor='#CECECE'>";

		print "<tr><td>ResId</td><td>Database</td><td>Table prefix</td><td><b>Repository title</b></td><td><b>Status</b></td><td><b>Size</b></td><td><b>DB version</b></td><td><b>Customers ($lang[customers])</b><td><b>Copy</b></td></tr>";
	// Get all possible CRM repository titles from all possible databases

		$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
		$result= mcq($sql,$db);
		$result1= mysql_fetch_array($result);
		$curpassword = $result1[password];

		if (sizeof($pass)>0) {
				for ($r=0;$r<64;$r++) {
					if ($r<>$repository_nr && $host[$r]) {
						if ($db = @mysql_connect($host[$r], $user[$r], $pass[$r])) {
							if (@mysql_select_db($database[$r],$db)) {
								$tbl = $table_prefix[$r];
								if ($tbl=="") $tbl="CRM";
								$sql = "SELECT password FROM " . $tbl . "loginusers WHERE name='$name' AND administrator='Yes'";
//								$result= mcq($sql,$db);
								$result = @mysql_query($sql);	
								$result1= @mysql_fetch_array($result);
								$foreignpassword = $result1[password];

								if ($curpassword==$foreignpassword) {

									$sql = "SELECT value FROM " . $tbl . "settings WHERE setting='title'";
									$result= @mcq($sql,$db);
									$maxU1 = @mysql_fetch_array($result);
									$title_t = $maxU1[0];

									$sql = "SELECT value FROM " . $tbl . "settings WHERE setting='DBVERSION'";
									$result= @mcq($sql,$db);
									$maxU1 = @mysql_fetch_array($result);
									$DBVERSION = $maxU1[0];
									
									$sql = "SELECT COUNT(*) FROM " . $tbl . "customer";
									$result= @mcq($sql,$db);
									$res = @mysql_fetch_array($result);
									$enum = $res[0];

									$sql = "SHOW TABLE STATUS";
									$result= @mcq($sql,$db);
									while ($stat = @mysql_fetch_array($result))
									{
										$size += $stat["Data_length"];
										$size += $stat["Index_lenght"];
									}							
		
									$size = ceil((($size/1024)/1024)) . " MB";
									
									if ($DBVERSION=="") {
										$DBVERSION="Prior to 1.9.0";
									} elseif ($DBVERSION<>$GLOBALS['DBVERSION']) {
										$DBVERSION .= " (be careful!!)";
									}


									print "<tr><td>$r</td><td>$database[$r]</td><td>$table_prefix[$r]</td><td>$title_t</td><td><font color='#33FF66'>OK</font></td><td>" . $size . "</td><td>" . $DBVERSION . "</td><td>$enum</td><td><img src='arrow.gif'>&nbsp;<a href='syncdb.php?input=1&web=1&daba=r" . $r . "' class='bigsort'>Import</a></a>";
									print "</td></tr>";
								} else {
									print "<tr><td>$r</td><td>Host: $host[$r] Database: $database[$r]</td><td>$tbl</td><td><font color='#FF3300'>Access denied or non-existent</font></td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>Admin accounts don't match</td></tr>";
								}
							} else {
								print "<tr><td>$r</td><td>Host: $host[$r] Database: $database[$r]</td><td>n/a</td><td><font color='#FF3300'>Couldn't select database</font></td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>Database could not be contacted</td></tr>";
							}

						} else {
							 print "<tr><td>$r</td><td>Host: $host[$r] Database: $database[$r]</td><td><font color='#FF3300'>Database host onreachable</font></td><td></td><td>n/a</td><td>n/a</td><td>n/a</td></tr>";
						}
				} else {
					print "<tr><td bgcolor='#B6B6B6'>$r</td><td bgcolor='#B6B6B6'>$database[$r]</td><td bgcolor='#B6B6B6'><font color='#FF3300'>Current repository</font></td><td bgcolor='#B6B6B6'>" . $title . "</td><td bgcolor='#B6B6B6'>n/a</td><td bgcolor='#B6B6B6'>n/a</td><td bgcolor='#B6B6B6'>n/a</td><td bgcolor='#B6B6B6'><font color='#FF3300'>These will be deleted</font></td><td bgcolor='#B6B6B6'>This is the current working repository</td></tr>";
				}
			}
				
					?>
			</select>
				&nbsp;&nbsp;&nbsp;
			  
            
			<?
				}

		print "</tr></table>";

	} // end if !$go
} // end if sizeof($host)

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


function uselogger_local($comment,$dummy_extra_not_used){
	global $REMOTE_ADDR, $HTTP_SERVER_VARS, $actuser, $username, $user, $HTTP_USER_AGENT,$name,$logqueries;
		
		// here comes the mail trigger
	


	 if (getenv(HTTP_X_FORWARDED_FOR)){ 
	   $ip=getenv(HTTP_X_FORWARDED_FOR); 
	 } 
	 else { 
	   $ip=getenv(REMOTE_ADDR); 
	 } 
	
	
	if (!$comment) {
		$qs  = getenv("QUERY_STRING");
		$qs .= getenv("HTTP_POST_VARS");
		$qs .= $comment;
	} else {
		$qs = addslashes($comment);
	}

	$url = $HTTP_SERVER_VARS["PHP_SELF"];
	
	$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('$ip', '$url', '$HTTP_USER_AGENT' , '$qs','$name')";
	mcq($query,$db);

	if ($logqueries) {
		qlog("'$ip', '$url', '$HTTP_USER_AGENT' , '$qs','$name'");
	}
}
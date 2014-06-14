<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This is the system logger plugin for CRM-CTT
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
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
	$entity = $argv[4];
} 
if ($argv[5]) {
	$action = $argv[5];
} 
if ($argv[6]) {
	$category = $argv[6];
} 
if ($repository==0) {
	// make this a string
	$repository = trim(" 0 ");
}
$reposnr = $repository;



 if ($argv[1]=="-help" || $argv[1]=="--help" || $argv[1]=="help" || $argv[1]=="-h" ||  $username=="" || $password=="" || $entity=="" || $action=="") {
	print "\nCRM-CTT Remote entity logger\n\nUsage:\n\n";
	print "Add a new entity: (all fields are required)\n\n\tphp -q ./crmlogger.php [reposnr] [user] [pass] [new] [\"customer name\"] [\"category text\"]\n";
	print "\nUpdate an existing entity: (all fields are required)\n\n\tphp -q ./crmlogger.php [reposnr] [user] [pass] [entity nr] [action=\"arg\"]\n";
	print "\nWhere action is one of: (the quotes MUST be around your arguments as shown!)\n";
	print "\n\taddlog=\"text\"";
	print "\n\taddlogfromfile=\"/path/to/file.log\"";	
	print "\n\taddfile=\"/path/to/file.doc\"";
	print "\n\tsetstatus=\"status\"";
	print "\n\tsetpriority=\"priority\"";
	print "\n\tsetowner=\"owner username\"";
	print "\n\tsetassignee=\"assignee username\"";
	print "\n\tsetduedate=\"duedate\" (syntax: DD-MM-YYYY)";
	print "\n\tsetduetime=\"duetime\" (syntax: HHMM e.g. 0930 or 1400 - only whole or half hour (00 or 30))";
	print "\n\tsetalarm=\"alarmdate\" (syntax: DD-MM-YYYY) - alarm will always be mailed to assignee";
	print "\n\tsetdeleted=\"y|n\" (syntax: 'y' for deleted, 'n' for not deleted)";
	print "\n\tsetprivate=\"y|n\" (syntax: 'y' for private, 'n' for not private)";
	print "\n\tsetreadonly=\"y|n\" (syntax: 'y' for readonly, 'n' for not readonly)";
	print "\n\nE1: php -q ./crmlogger.php 0 user user_pwd new \"Cust. X\" \"Cat. Y\" (returns entity number)\n";
	print "E2: php -q ./crmlogger.php 0 user user_pwd 40 addlog=\"This server is not responding\" (logs text to entity 40)\n";
	print "E3: php -q ./crmlogger.php 0 user user_pwd 40 addfile=\"/tmp/file.doc\" (attaches file to entity 40)\n\n";
	print "When using this script from a remote location, you need all CRM files!\n\n";
	print "-> Make sure to use 'php -q ' to run this script!\n\n";

//	print_r($argv);
	exit;
}

//require($config);


$silent = 1;
$noneedtobeadmin = 1;
$c_l = "1";


require_once("config.inc.php");

require_once("getset.php");

// Check if this is done using the command line (e.g. not the web)
CheckIfShell();

if (!CommandlineLogin($username,$password,$repository)) {
		print "Exiting...";
		exit;
} else {
	if (GetClearanceLevel($GLOBALS['USERID'])<>"logger") {
		print "This is not a logging user account. Fatal, quitting.\n";
		exit;
	}
	//include("language.php");
}

if ($entity=="new") {
		
	$customer = $action;

	if ($category=="" || $customer=="") {
		print "No category/customer found. Fatal, quitting.\n";
		$fatal_error = true;
	} else {

		$sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]customer WHERE custname='" . $customer . "'";
		$result = mcq($sql,$db);
		$row = mysql_fetch_array($result);

		$customer_id = $row[0];

		if ($customer_id == "") {
			print "Customer name could not be resolved. Fatal, quitting.\n";
			$fatal_error = true;
		} else {

			// DEFAULT ENTITY SETTINGS NOT HANDLED BY SCRIPT ARGUMENTS

			$readonly			=	"n";
			$notify_owner		=	"n";
			$notify_assignee	=	"n";
			$private			=	"n";
			$duedate			=	"";

			$priority			=	"0 - Unknown";
			$status				=	"0 - Unknown";

			$owner				=	$GLOBALS['USERID'];
			$assignee			=	$GLOBALS['USERID'];

			$content			=	"Added by logger " . $username . ", " . date('r') . ":\n";

			$openepoch = date('U');
//			$cdate = date('Ymd');
			$cdate = date('Y-m-d');
			$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]entity(priority,category,content,owner,assignee,CRMcustomer,status,deleted,duedate,sqldate,obsolete,cdate,waiting,createdby,lasteditby,readonly,notify_owner,notify_assignee,openepoch,private,duetime, formid) VALUES('" . $priority ."', '" . $category . "', '" . $content . "', '" . $owner . "', '" . $assignee . "', '" . $customer_id . "','" . $status . "','n','" . $duedate . "','3000-03-03','','" . $cdate . "','n','" . $GLOBALS['USERID'] . "','" . $GLOBALS['USERID'] . "','" . $readonly . "','" . $notify_owner . "','" . $notify_assignee . "','" . $openepoch . "','" . $private . "','" . $duetime . "'," . $GLOBALS['DefaultForm'] . ")";
			
			$ret = mcq($sql,$db);
			$eid = mysql_insert_id();
			//print "\n$sql\n";

			journal($eid,"Entity automatically created from logger");
			journal($customer_id,"Entity $eid was coupled to this customer (by crmlogger)","customer");

			ProcessTriggers("assignee",$eid,$assignee);
			ProcessTriggers("owner",$eid,$owner);		
			ProcessTriggers("status",$eid,$status);
			ProcessTriggers("priority",$eid,$priority);
			ProcessTriggers("customer",$eid,$customer_id);

			$GLOBALS['CURFUNC'] = "CRMLogger::";
			qlog("Entity " . $eid . "added by logger user " . GetUserName($GLOBALS['USERID']));

			print "$eid\n";
		} // end if customer name was resolved
	} // end if empty category or customer

} else {

	// Check security

	$sql = "SELECT owner FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='" . $entity . "'";
	$result = mcq($sql,$db);
	$row = mysql_fetch_array($result);
	if ($row['owner'] <> $GLOBALS['USERID']) {
		print "The log user ('" . GetUserName($GLOBALS['USERID']) . "') does not own this entity. Fatal, quitting.\n";
		$fatal_error = 1;
	} else {
		
		qlog("Logger: Now updating entity $entity");

		// actions are addlog, addlogfromfile, addfile, setstatus, setpriority, setowner, setassignee, setduedate, 
		
		ExpireFormCache($entity);

		// parse actions

		$eid = $entity;
		
		if (stristr($action,"addlog=")) {
			$txt = ereg_replace("addlog=","",$action);
			
			$sql = "SELECT content FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='" . $entity . "'";
			$result = mcq($sql,$db);
			$row = mysql_fetch_array($result);			
			
			$new_content = $row['content'] . "\n" . $txt;

			$sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET content='" . addslashes($new_content) . "' WHERE eid='" . $entity . "'";
			mcq($sql,$db);

			journal($entity,"Contents updated (by logger)");

		} elseif (stristr($action,"addlogfromfile=")) {
			$log_from_file = ereg_replace("addlogfromfile=","",$action);
			
			$sql = "SELECT content FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='" . $entity . "'";
			$result = mcq($sql,$db);
			$row = mysql_fetch_array($result);			
			
			$fp = fopen($log_from_file,"r");
			$txt = fread($fp,filesize($log_from_file));
			fclose($fp);

			$new_content = $row['content'] . "\n" . $txt;

			$sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET content='" . addslashes($new_content) . "' WHERE eid='" . $entity . "'";
			mcq($sql,$db);

			journal($entity,"Contents updated (by logger, from file " . $log_from_file . ")");

		} elseif (stristr($action,"addfile=")) {

			$file = ereg_replace("addfile=","",$action);
			$fp = fopen($file,"r");
			$txt = fread($fp,filesize($file));
			fclose($fp);
			
			$size = filesize($file);

			if (strstr($file,"/")) {
				$file = split("/",$file);
				$x = sizeof($file)-1;
				$filename = $file[$x];
			} elseif (strstr($file,"\\")) {
				$file = split("\\",$file);
				$x = sizeof($file)-1;
				$filename = $file[$x];
			} else {
				print "file is: " . $file;
				$filename = $file;
			}

			//$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,username) VALUES('". $entity . "','" . addslashes($txt) . "','" . $filename . "','" . $size . "','" . $GLOBALS['USERNAME'] ."')";
			//mcq($sql,$db);


//			function AttachFile($koppelid,$filename,$content,$type="entity",$filetype="Unknown") {

			$attachment = AttachFile($entity,$filename,$txt,"entity","unknown");

			journal($entity,"File " . $file . " added (by logger)");
	

		} elseif (stristr($action,"setstatus=")) {
			$to_status = ereg_replace("setstatus=","",$action);
			$sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET status = '" . $to_status . "' WHERE eid='" . $entity ."'";
			mcq($sql,$db);
			
			journal($entity,"Status set to " . $to_status . " (by logger)");
			
			ProcessTriggers("status",$eid,$to_status);

		} elseif (stristr($action,"setpriority=")) {
			$to_priority = ereg_replace("setpriority=","",$action);

			$sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET priority = '" . $to_priority . "' WHERE eid='" . $entity ."'";
			mcq($sql,$db);
			
			journal($entity,"Priority set to " . $to_priority . " (by logger)");

			ProcessTriggers("priority",$eid,$to_priority);

		} elseif (stristr($action,"setowner=")) {
			$to_owner = ereg_replace("setowner=","",$action);
			
			$sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='" . $to_owner . "'";
			$result = mcq($sql,$db);
			$row = mysql_fetch_array($result);
			if ($row['id'] == "") {
				print "Target user '" . $to_owner . "' could not be resolved. Fatal, quitting.\n";
				$fatal_error = 1;
			} else {
				$sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET owner='" . $row['id'] ."' WHERE eid='" . $entity ."'";
				mcq($sql,$db);
				journal($entity,"Updated owner to " . GetUserName($row['id']) . " (by logger)");
				ProcessTriggers("owner",$eid,$row['id']);		
			}
		} elseif (stristr($action,"setassignee=")) {
			$to_assignee = ereg_replace("setassignee=","",$action);
			
			$sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='" . $to_assignee . "'";
			$result = mcq($sql,$db);
			$row = mysql_fetch_array($result);
			if ($row['id'] == "") {
				print "Target user '" . $to_assignee . "' could not be resolved. Fatal, quitting.\n";
				$fatal_error = 1;
			} else {
				$sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET assignee='" . $row['id'] ."' WHERE eid='" . $entity ."'";
				mcq($sql,$db);
				journal($entity,"Updated owner to " . GetUserName($row['id']) . " (by logger)");
				ProcessTriggers("assignee",$eid,$row['id']);
			}
		} elseif (stristr($action,"setduedate=")) {
			$to_duedate = ereg_replace("setduedate=","",$action);

			$td1 = explode("-",$to_duedate); // dd-mm-yyyy
			$sqldate = "$td1[2]-$td1[1]-$td1[0]"; // yyyy-mm-dd

			
			$sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET duedate = '" . $to_duedate . "',sqldate='" . $sqldate ."' WHERE eid='" . $entity ."'";
			mcq($sql,$db);
			
			journal($entity,"Duedate set to " . $to_duedate . " (by logger)");

		} elseif (stristr($action,"setduetime=")) {
			$to_duetime = ereg_replace("setduetime=","",$action);
			
			$sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET duetime = '" . $to_duetime . "' WHERE eid='" . $entity ."'";
			mcq($sql,$db);
			
			journal($entity,"Duetime set to " . $to_duetime . " (by logger)");

		}  elseif (stristr($action,"setdeleted=")) {
			$to_deleted = ereg_replace("setdeleted=","",$action);
			if ($to_deleted <> "y" && $to_deleted <> "n") {
				print "You passed an invalid value. Fatal, quitting.\n";
				$fatal_error = 1;	
			} else {
				$closeepoch = date('U');
				$sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET deleted = '" . $to_deleted . "',closeepoch='" . $closeepoch . "' WHERE eid='" . $entity ."'";
				mcq($sql,$db);
				
				journal($entity,"Deleted set to " . $to_deleted . " (by logger)");
			}
		} elseif (stristr($action,"setprivate=")) {
			$setprivate = ereg_replace("setprivate=","",$action);
			if ($setprivate <> "y" && $setprivate <> "n") {
				print "You passed an invalid value. Fatal, quitting.\n";
				$fatal_error = 1;	
			} else {
			
				$sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET private = '" . $setprivate . "' WHERE eid='" . $entity ."'";
				mcq($sql,$db);
				
				journal($entity,"Private set to " . $setprivate . " (by logger)");
			}
		} elseif (stristr($action,"setreadonly=")) {
			$setreadonly = ereg_replace("setreadonly=","",$action);
			if ($setreadonly <> "y" && $setreadonly <> "n") {
				print "You passed an invalid value. Fatal, quitting.\n";
				$fatal_error = 1;	
			} else {
			
				$sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET readonly = '" . $setreadonly . "' WHERE eid='" . $entity ."'";
				mcq($sql,$db);
				
				journal($entity,"Readonly set to " . $setreadonly . " (by logger)");
			}
		} elseif (stristr($action,"setalarm=")) {
			$to_alarmdate = ereg_replace("setalarm=","",$action);
			
			$to_alarmdate = str_replace("-","",$to_alarmdate);

			$sql = "SELECT assignee FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='" . $entity . "'";
			$result = mcq($sql,$db);
			$row = mysql_fetch_array($result);		
			
			$assignee = $row[0];
			
			if ($assignee == "") {
				print "Assignee could not be resolved. Fatal, quitting.\n";
			} else {
				$sql = "SELECT EMAIL FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id='" . $assignee . "'";
				$result = mcq($sql,$db);
				$row = mysql_fetch_array($result);		
				
				$email = $row[0];
				if ($email == "") {
					print "Assignee e-mail address could not be resolved. Fatal, quitting.\n";
				} else {

					$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]calendar(user,basicdate,emailadress,eID) VALUES('". $GLOBALS['USERNAME'] . "','" . $to_alarmdate . "','" . $email . "','" . $entity . "')";
					mcq($sql,$db);
					
					journal($entity,"alarmdate set to " . $to_alarmdate . " (by logger)");
				}
			}
		}
	} // end if owner
}



// Journalling function (Entity ID, Message)
function journal($eid,$msg,$JournalType="entity") {
		$msg = addslashes($msg);
		$sql = "INSERT INTO " . $GLOBALS[TBL_PREFIX] ."journal (eid,user,message,type) VALUES('$eid','" . $GLOBALS[USERID] . "','$msg','" . $JournalType ."')";
		mcq($sql,$db);
}
?>
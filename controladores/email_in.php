<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This is the "e-mail to entity" plugin for CRM-CTT
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
//
// SENDMAIL SYNTAX; Create an alias in your /etc/mail/aliases called:
//
//	crm:  "|/path/to/php /path/to/email_in.php [reposnr] logger_user logger_pass new 'Full customer name'"
// e.g.
//  crm:  "|/usr/local/bin/php /webservers/htdocs/crmstage/email_in.php 2 loguser logpass new 'IBM'"
//
// The "new" in the line must be there.


// Logqueries : Wether or not to log *all* MySQL queries in qlist.txt 
//
// 1. This generates LOTS of data
// 2. You need to have a file called "qlist.txt" in your CRM installation
//    directory which is owned by the user under which your webserver runs
// 3. Since this file meight be webserver-readable plain text, all your data could
//    be exposed - THESE QUERIES MIGHT CONTAIN READABLE PASSWORDS!
//
// Logtext: wether or not to log remarks made by me (high-traffic)
//
// 1. This generates LOTS of data
// 2. You need to have a file called "qlist.txt" in your CRM installation
//    directory which is owned by the user under which your webserver runs
// 3. Since this file meight be webserver-readable plain text, all your data could
//    be exposed
//
// Generally NOT recommended for security reasons!

// When _not_ using logqueries and logtext, leave these lines with = 0; to 
// avoid hacking using REGISTER_GLOBALS

	$GLOBALS['logqueries']    = false;   // logs all queries
	$GLOBALS['logtext']       = false;    // logs all comments
	$GLOBALS['query_timer']   = false;    // logs slowest SQL query
	$GLOBALS['qlog_onscreen'] = false;   // displays pop-up containing log

// ---------------------------------------------------------------------------





$fp = fopen("php://stdin", "r");
while (!feof($fp)) {
	$input .= fgets($fp,1024);
}

require("functions.php");
require("config.inc.php");

// Check if this is done using the command line (e.g. not the web)
CheckIfShell();

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
	$customer = $argv[5];
} 

if ($repository==0 || $repository=="") {
	// make this a string
	$repository = trim(" 0 ");

}

 if ($argv[1]=="-help" || $argv[1]=="--help" || $argv[1]=="help" || $argv[1]=="-h" ||  $username=="" || $password=="" || $entity=="" ) {
	print "\nCRM-CTT Entity insert and update from e-mail\n\nUsage:\n\n";
	print "Add a new entity: (all fields are required)\n\n\tphp -q ./email_in.php [reposnr] [user] [pass] [new] [\"customer name\"] [\"category text\"]\n";
	print "\nUpdate an existing entity: (all fields are required)\n\n\tphp -q ./email_in.php [reposnr] [user] [pass] [entity nr] [action=\"arg\"]\n";
	
	print "When using this script from a remote location, you need the following files from your CRM-CTT installation:\n\n";
	print "\t email_in.php (this script)\n";
	print "\t config.inc.php (the config file, make sure the database host is set correctly)\n";
	print "\t functions.php (the main functions file)\n";
	print "\nThese files must be placed in the same directory as this script.\n\n";
	print "-> Make sure to use 'php -q ' to run this script!\n\n";

//	print_r($argv);
	exit;
}

if (!$username || !$password || !$entity || !$customer) {
	echo "You MUST give all required information as arguments!\n\n";
	print "$username || !$password || !$repository || !$entity || !$customer";
	exit(1);
}

//require($config);


$silent = 1;
$noneedtobeadmin = 1;

if (!CommandlineLogin($username,$password,$repository)) {
		print "Exiting...";
		exit(1);
} else {
	if (GetClearanceLevel($GLOBALS['USERID'])<>"logger") {
		print "This is not a logging user account. Fatal, quitting.\n";
		exit(1);
	} else {
		// Collect settings
		$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]settings";
		$result= mcq($sql,$db);								
		while ($resarr=mysql_fetch_array($result)){
				$$resarr['setting'] = $resarr['value'];
				$GLOBALS[$resarr['setting']] = $resarr['value'];
				if ($debug && !stristr("select * from $GLOBALS[TBL_PREFIX]settings",$sql)) {
					print "<!-- $$resarr[setting] -- $resarr[value] -->\n";
				}
		}
		print "You are connected to repository " . $title . ".\n";
	}
	//include("language.php");
}

if (strtoupper($GLOBALS['EnableCustInsert'])<>"YES") {	
		print "Cust-insert is not set (directive ENABLECUSTINSERT). Fatal, quitting.\n";
		exit;
}




qlog("EmailIn::Action/entity: " . $entity);

include("mimeDecode.php");
include("class.phpmailer.php");
$args['include_bodies'] = true;
$args['decode_bodies'] = true;

$decode = new Mail_mimeDecode($input, "\r\n");
$structure = $decode->decode($args);
//print_r($structure);
//
//exit;


if ($entity=="new") {
	$sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]customer WHERE custname='" . $customer . "'";
	$result = mcq($sql,$db);
	$row = mysql_fetch_array($result);

	$customer_id = $row[0];

	if ($customer_id == "") {
		print "Customer name could not be resolved. Fatal, quitting.\n";
		exit(1);
	}

	$cdate = date('Y-m-d');
	$epoch = date('U');	
	
	$body = "MAIL\n";
	$body .= "\nFrom: " . $structure->headers['from'];
	$body .= "\nReceived: " . $structure->headers['date'];
	$body .= "\nTo: " . $structure->headers['to'];
	
	if ($structure->body<>"") {
			$body .= "\n\n" . $structure->body;
	} elseif($structure->parts[0]->body<>"") {
			$body .= "\n\n" . $structure->parts[0]->body;
	} elseif($structure->parts[0]->parts[0]->body<>"") {
			$body .= "\n\n" . $structure->parts[0]->parts[0]->body;
	} elseif($structure->parts[0]->parts[0]->parts[0]->body<>"") {
			$body .= "\n\n" . $structure->parts[0]->parts[0]->parts[0]->body;
	} elseif($structure->parts[1]->body<>"") {
			$body .= "\n\n" . $structure->parts[1]->body;
	} elseif($structure->parts[1]->parts[0]->body<>"") {
			$body .= "\n\n" . $structure->parts[1]->parts[0]->body;
	} elseif($structure->parts[1]->parts[0]->parts[0]->body<>"") {
			$body .= "\n\n" . $structure->parts[1]->parts[0]->parts[0]->body;
	} elseif($structure->parts[2]->body<>"") {
			$body .= "\n\n" . $structure->parts[2]->body;
	}
	
//	$attm = $decode->uudecode($input);
//	print_r($attm);

	$files_names = array();
	$files_blobs = array();
	$files_sizes = array();

	// Next three lines will attach the raw input as an attachment (for debug/test purposes)
	// (only when $logtext is set)

	if ($logtext) {
		array_push($files_names, "mime-source.txt");
		array_push($files_blobs, $input);
		array_push($files_sizes, strlen($input));
	}

	for ($i=0;$i<sizeof($structure->parts);$i++) {
		if (($structure->parts[$i]->d_parameters['filename'] <> "") && $structure->parts[$i]->body <> "") {
			array_push($files_names, $structure->parts[$i]->d_parameters['filename']);
			array_push($files_blobs, $structure->parts[$i]->body);
			array_push($files_sizes, strlen($structure->parts[$i]->body));
		}
		
	}

	//print $body;
	//exit;

	$logtxt .= "Entity created\n";

	$cdate = date('Y-m-d');

	$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='AutoAssignIncomingEntities'";
	$result = mcq($sql,$db);
	$row = mysql_fetch_array($result);

	if (strtoupper($row[0])=="YES") {
		// This new entity must be auto-assigned to the customer owner
		qlog("Auto-assigning this entity to the customer owner");
		$logtxt .= "Auto-assigning this entity to the customer owner";
		$owner = GetCustomerOwner($customer_id);
		$assignee = GetCustomerOwner($customer_id);
		$logtxt .= "Set o:$owner a:$assignee";
		if ($owner == "" || $owner == 0) {
			$owner = "2147483647";
		}
		if ($assignee == "" || $assignee == 0) {
			$assignee = "2147483647";
		}

	} else {
		$owner = "2147483647";
		$assignee = "2147483647";
		$logtxt .= "default users - $row[0]";
	}


	$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]entity(priority,category,content,owner,assignee,CRMcustomer,status,deleted,duedate,sqldate,obsolete,cdate,waiting,openepoch) VALUES('[unknown]', '" . addslashes($structure->headers['subject']) . "', '" . addslashes($body) . "', '" . $owner . "', '" . $assignee ."', '$customer_id','[unknown]','n','','','','$cdate','','$epoch')";
	mcq($sql,$db);

	$eid = mysql_insert_id();

	journal($eid,$logtxt);

	// Process all triggers

	ProcessTriggers("assignee",$eid,$assignee);
	ProcessTriggers("owner",$eid,$owner);		
	ProcessTriggers("status",$eid,"[unkown]");
	ProcessTriggers("priority",$eid,"[unknown]");
	ProcessTriggers("customer",$eid,$customer_id);
	
	for ($y=0;$y<sizeof($files_names);$y++) {
//			ion AttachFile($koppelid,$filename,$content,$type,$filetype="Unknown",$subj="") {
			AttachFile($eid, $files_names[$y], $files_blobs[$y], "entity", "", "");
//			$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,username) VALUES('". $eid . "','" . addslashes($files_blobs[$y]) . "','" . $files_names[$y] . "','" . $files_sizes[$y] . "','" . $username ."')";
//			mcq($sql,$db);
			$ins_attm++;
	}

	$ManualEmailAddress = $structure->headers['from'];

	$MailBody .= $ins_attm . " attachments";

	$MailBody = $GLOBALS['BODY_EMAILINSERT_REPLY'];
	$Subject  = $GLOBALS['SUBJECT_EMAILINSERT_REPLY'];
	
	$MailBody = ParseTemplateEntity($MailBody,$eid);
	$MailBody = ParseTemplateCustomer($MailBody,$customer_id);
	$MailBody = ParseTemplateGeneric($MailBody);

	$Subject  = ParseTemplateEntity($Subject,$eid);
	$Subject  = ParseTemplateCustomer($Subject,$customer_id);
	$Subject  = ParseTemplateGeneric($Subject);

	if (stristr($structure->headers['to'],$structure->headers['from'])) {
		$ManualEmailAddress = $GLOBALS['AdminEmail'];
		$MailBody = "CRM ERROR - circular e-mail configuration. Don't set your admin e-mailaddress to the same address as your administrative e-mail address. Somebody tried to exploit this!";
		$Subject = "CRM ERROR - Possible attack";
		SendEmail($eid,"ManualMail","new", $MailBody, $Subject, $GLOBALS['AdminEmail'], $GLOBALS['AdminEmail']);
	} else {
		SendEmail($eid,"ManualMail","new", $MailBody, $Subject, $GLOBALS['AdminEmail'], $GLOBALS['AdminEmail']);
		if (stristr($EmailNewEntities,"@")) {
			SendEmail($eid,"global_new_entity","new", "", "", "", "");
		}
	}
 
} 



// Journalling function (Entity ID, Message)
function journal($eid,$msg,$JournalType="entity") {
		$msg = addslashes($msg);
		$sql = "INSERT INTO " . $GLOBALS[TBL_PREFIX] ."journal (eid,user,message,type) VALUES('$eid','" . $GLOBALS[USERID] . "','$msg','" . $JournalType ."')";
		mcq($sql,$db);
}
function uselogger($comment,$dummy_extra_not_used){
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
?>
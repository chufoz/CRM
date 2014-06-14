<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * WEBDAV file script
 * This file handles all access to the WebDav subsystem
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

ob_start();

include("config.inc.php");
$c_l = "1";
include("getset.php");


if (strtoupper($EnableWebDAVSubsystem)<>"YES") { 
	print "<img src='error.gif'>&nbsp; WebDAV subsystem is disabled";
	EndHTML;
	exit;
}

// Make sure all directories are there - the EID numbers of entities with files
for ($i=0;$i<64;$i++) {
	if ($host[$i]) {
		if (!is_dir(getcwd() . "/webdav_fs/" . $i)) {
			@mkdir(getcwd() . "/webdav_fs/" . $i);
		}
	}
}	

$sql="SELECT DISTINCT(eid) FROM $GLOBALS[TBL_PREFIX]entity";
$output = mcq($sql,$db);
while ($row = mysql_fetch_array($output)) {
	if (!is_dir(getcwd() . "/webdav_fs/" . $repository_nr . "/" . $row['eid'])) {
		mkdir(getcwd() . "/webdav_fs/" . $repository_nr . "/" . $row['eid']);
	}
}


if (!isset($_SERVER['PHP_AUTH_USER'])) {
   ShowAuthHeadersWebdav();
   //system("echo ' totaal geen nieuws'|logger");
   exit;

  } else {
		//system("echo ' USER:" . $_SERVER['PHP_AUTH_USER'] ."'|logger");
		//system("echo ' PASS:" . $_SERVER['PHP_AUTH_PW'] ."'|logger");

		if (AuthenticateUserWebdav($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'],$repository_nr)) {
			$authenticated = true;
		} else {
		   ShowAuthHeadersWebdav();
		   //system("echo ' wel geprobeerd maar werkt nie - repos $repository_nr'|logger");
		   exit;
			
		}
  }

	//ini_set("include_path", ini_get("include_path").":/usr/local/apache/htdocs");
	require_once "Filesystem.php";
	$server = new HTTP_WebDAV_Server_Filesystem();
	//$server->ServeRequest($_SERVER["DOCUMENT_ROOT"] . "/webdav_fs/");
	$subdir = $_SERVER["DOCUMENT_ROOT"] . ereg_replace("webdav_file.php","webdav_fs/",$_SERVER['SCRIPT_NAME']);
	$server->ServeRequest($subdir);
			


		
function uselogger($comment,$dummy_extra_not_used){
	global $REMOTE_ADDR, $HTTP_SERVER_VARS, $actuser, $username, $user, $HTTP_USER_AGENT,$name;
		
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
}
function journal($eid,$msg,$JournalType="entity") {
	global $EnableEntityJournaling;
	if (strtoupper($EnableEntityJournaling)=="YES" || (stristr($msg,"[admin]"))) {
		
		$msg = addslashes($msg);

		$sql = "INSERT INTO " . $GLOBALS[TBL_PREFIX] ."journal (eid,user,message,type) VALUES('$eid','" . $GLOBALS[USERID] . "','$msg','" . $JournalType ."')";

		mcq($sql,$db);
	}
}

function ActiveUserWebdav ($session){
	global $username,$expire,$lang;
	$personresult = mcq("SELECT name, temp, exptime, noexp FROM $GLOBALS[TBL_PREFIX]sessions WHERE temp='$session'",$db);
	$personarray = mysql_fetch_array ($personresult);
	$currenttime = time();
	$settime = $personarray["exptime"]+EXPIRE;
	$todaynum= date("w", $currenttime);
	$expdaynum = date("w", $settime);

	$arg = "SELECT id FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$personarray[name]'";
	$result = mcq($arg,$db);
	$row = mysql_fetch_array($result);
	$GLOBALS[USERID] = $row[id];

	if ($todaynum <> $expdaynum && $currenttime > $settime){ //als het de volgende dag is en je tijd is niet okay.
		$arg = "UPDATE $GLOBALS[TBL_PREFIX]sessions SET noexp='no' WHERE temp='$session'";//  volgende dag en tijd verlopen, noexp to no
		$row = mcq($arg,$db);
		uselogger("WEBDAV expire","");
		ShowAuthHeadersWebdav("session expired 1");
		exit;
	}
	elseif ($currenttime > $settime && $personarray[noexp]==no){ // als je tijd niet okay is en je mag niet altijd 	door
		uselogger("WEBDAV expire","");
		ShowAuthHeadersWebdav("session expired 2");
		exit;

	}
	elseif ($session <> $personarray["temp"]){
		$arg = "UPDATE $GLOBALS[TBL_PREFIX]sessions SET noexp='no' WHERE temp='$session'";//  foute session, noexp to no
		$row = mcq($arg,$db);
		uselogger("WEBDAV session","");
		ShowAuthHeadersWebdav("wrong session");
		exit;
	}
	else{
		setnewtimeWebdav ($personarray["name"], $session);
	}
  return $personarray["name"];
}

function setnewtimeWebdav ($actuser, $session){
	$exptime = time();
	$arg = "UPDATE $GLOBALS[TBL_PREFIX]sessions SET exptime='$exptime' WHERE temp='$session'";
	$row = mcq($arg,$db);
	}


?>
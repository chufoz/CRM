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

if (!$reposid) {
		$reposnr = 0;
} else {
		$reposnr = $reposid;
}

include("config.inc.php");
include("getset.php");


if (!strtoupper($ShowPDWASLink)=="YES") { 
		print "PDWAS DISABLED\n";
		exit;
}
//sub_ocf.php

if ($fileid) {
	//system("echo 'TEST NOSTATS received file $fileid $name $pass'|logger");

	$sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='" . $name ."'";
	mcq($sql,$db);
	$output = mcq($sql,$db);
	$pass2 = mysql_fetch_array($output);

	if (!$passwd==$pass2[0]) {
		print "Authorisation error\n";
		exit;
	} else {
		print "Identified as $name\n";
	}

	$cont = addslashes(base64_decode($cont));

	$sql = "SELECT koppelid,filename,filetype FROM $GLOBALS[TBL_PREFIX]binfiles WHERE fileid='" . $fileid ."'";
	mcq($sql,$db);
	$output = mcq($sql,$db);
	$bla = mysql_fetch_array($output);
	$koppelid = $bla[0];
	$filename = $bla[1];
	$filetype = $bla[2];

	print "This file must be attached to entity $koppelid\n";

	$filesize = strlen($cont);

//	$sql="INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filetype,filesize,username) VALUES('" . $koppelid . "','" . $cont . "','" . $filename . "','" . $filetype ."','" . $filesize . "','" . $name . "')";
	mcq($sql,$db);
//	$attachment = mysql_insert_id();

	$attachment = AttachFile($koppelid, $filename, $cont, "entity", $filetype);

	//journal($e,"File " . $_FILES['userfile']['name'] . " added");
	$add_to_journal .= "File " . $_FILES['userfile']['name'] . " added use CRM PDWAS";
	uselogger("File  " . $_FILES['userfile']['name'] . " added to entity $e by CRM PDWAS","");
	journal($koppelid,$add_to_journal);
	
	print "CRMALLWENTOK";

} else {
	
	print "<html><body><img src='crm.png'><!-- What are you doing here? error!--></body></html>";
}

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

// Journaling function (Entity ID, Message)
function journal($eid,$msg) {
	global $EnableEntityJournaling;
	if (strtoupper($EnableEntityJournaling)=="YES" || (stristr($msg,"[admin]"))) {
		$sql = "INSERT INTO " . $GLOBALS[TBL_PREFIX] ."journal (eid,user,message) VALUES('$eid','" . $GLOBALS[USERID] . "','$msg')";
		mcq($sql,$db);
	}
}
?>
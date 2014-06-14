<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This is the CRM-CTT RSS Feeder
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

extract($_REQUEST);


if ($_REQUEST['avail']) {
	include("header.inc.php");

	if ($_SERVER['HTTPS']=="on") {
		$http = "https://";
	} else {
		$http = "http://";
	}
	$subdir = ereg_replace("rss.php","",$_SERVER['SCRIPT_NAME']);
	$link = $http . $_SERVER['SERVER_NAME'] . $subdir . "rss.php?";
	
	$arr = unserialize($GLOBALS['RSS_FEEDS']);
	print "</table><table width=50%><tr><td>&nbsp;&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Available RSS Feeds</legend>";
	print "<table width=100% class='crm'>";
	print "<tr><td>Link</td><td>Description</td></tr>";
	$FN=1;
	foreach ($arr AS $feed) {
		$ThisLink = $link . "FN=" . $FN . "&rep=" . $repository_nr; 
		print "<tr><td><a style='cursor: pointer' title='Click to copy link to clipboard' onclick=\"CopyToClipboard('" . $ThisLink . "');\">[click to copy]</a></td><td>" . base64_decode($feed['description']) . "</td></tr>";
		$FN++;
	}
	print "</table></fieldset></td></tr></table>";
	EndHTML();
	exit;
} else {
	$GLOBALS['repository_nr'] = $_REQUEST['rep'];
	$repository_nr = $_REQUEST['rep'];

	$rss = true;

	include("config.inc.php");
	include("getset.php");
	include("language.php");

	if (!$_REQUEST['rep']) {
		$_REQUEST['rep'] = 0;
	}

	$GLOBALS['repository_nr'] = $_REQUEST['rep'];

}

if (strlen($lang['CHARACTER-ENCODING'])>2) {
	qlog("Character-encoding override in effect: " . $lang['CHARACTER-ENCODING']);
	$charset = $lang['CHARACTER-ENCODING'];
	$GLOBALS['CHARACTER-ENCODING'] = $lang['CHARACTER-ENCODING'];
} else {
	$charset = "ISO-8859-1";
	$GLOBALS['CHARACTER-ENCODING'] = "ISO-8859-1";
}

if (!isset($_SERVER['PHP_AUTH_USER'])) {
   ShowAuthHeadersRSS();
   //system("echo ' totaal geen nieuws'|logger");
   exit;

  } else {
		//system("echo ' USER:" . $_SERVER['PHP_AUTH_USER'] ."'|logger");
		//system("echo ' PASS:" . $_SERVER['PHP_AUTH_PW'] ."'|logger");

		if (AuthenticateUserWebdav($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'],$GLOBALS['repository_nr'])) {
			$authenticated = true;
			$GLOBALS['USERNAME'] = "RSS::" . $_SERVER['PHP_AUTH_USER'];
			$name = "RSS::" . $_SERVER['PHP_AUTH_USER'];
		} else {
		   ShowAuthHeadersWebdav();
		   //system("echo ' wel geprobeerd maar werkt nie - repos $repository_nr'|logger");
		   exit;
			
		}
}



//$FN = 1;

$FN--;

GenerateRSSFeed($FN,10);

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
?>
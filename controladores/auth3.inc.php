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
ob_start();
$n_E = extract($_REQUEST);

$browser="$User-Agent";
if ($_REQUEST['logout'] =="1"){
  $session = $_COOKIE['session'];
  qlog("Auth3::This user logged out");
  SetCookie("session","",time()-500); //unset cookie
  uselogger("Logoff $name","");
  $sql = "DELETE FROM $GLOBALS[TBL_PREFIX]sessions WHERE temp = '$session'";
  mcq($sql,$db);

  if ($_REQUEST['expire']) {
		$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='timeout'";
		$result= mcq($sql,$db);
		$result= mysql_fetch_array($result);
		$timeout = $result[value];
		include("language.php");
		DisplayLoginForm($lang['signedoffdue1'] . "&nbsp;" . $timeout . "&nbsp;" . $lang['signedoffdue2']);	
  } else {
	  include("language.php");
	  DisplayLoginForm($lang['signedoff']);
  }
} elseif ($_REQUEST['session']){
	//check if code is correct and if time correct and not older than 30 minutes

    $actuser = ActiveUser($_REQUEST['session']);
    
	$GLOBALS['session_id'] = $_REQUEST['session'];
	$name= $actuser;
	$GLOBALS['username'] = $actuser;
	$GLOBALS['USERNAME'] = $actuser;
	$GLOBALS['USERID']   = GetUserID($actuser);

	//uselogger("","$name");
}
elseif ($_REQUEST['name']){
   
	AuthenticateUser($_REQUEST['name'], $_REQUEST['password'], $_REQUEST['silent']);
	uselogger("Authenticate $name","Authenticate $name");
	qlog("Auth3::User $name logged in");
	$GLOBALS['username'] = $_REQUEST['name'];
	$GLOBALS['USERNAME'] = $_REQUEST['name'];
	$GLOBALS['USERID']   = GetUserID($_REQUEST['name']);

	if ($GLOBALS['USE_EXTENDED_CACHE']) {
		$uri = urlencode($_SERVER['REQUEST_URI']);
		if (trim($uri) == "") {
			$uri = "index.php";
		}
                    ?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			document.write('<link href="crm_dft.css" rel="stylesheet" type="text/css">');
			setTimeout('continueNow()',50000);

			function continueNow() {
					//document.location = " <? echo $_REQUEST['urltogo'];?>";
			}
		//-->
		</SCRIPT>		
		<center>
		<table width=100% height=100%><tr><td valign='center'><center>
		<img src='crm.gif'><br>
		<img src='movingbar.gif'>
		<br>Please wait...
		</center></td></tr></table></center>
		<iframe height=1 width=1 src='index.php?UpdateCacheTables=do&urltogo=<? echo $uri;?>'></iframe>
		<?
	}

	?>
	<SCRIPT LANGUAGE="JavaScript" SRC="cookies.js"></SCRIPT>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
		setCookie('disable_triggers','n');
	//-->
	</SCRIPT>
	
	<?
	$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]settings WHERE setting = 'Logon message'";
	$result= mcq($sql,$db);
	$result= mysql_fetch_array($result);
	$logonmsg = $result[value];
	
	if (trim($logonmsg)<>"") {
		include("language.php");
		$logonmsg = $lang[sysmsg] . ":\\n\\n" . $logonmsg;
		
		?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
			alert("<? echo $logonmsg;?>");
			//-->
			</SCRIPT>
		<?
	}
	if ($GLOBALS['USE_EXTENDED_CACHE']) {
		EndHTML();
		exit;
	}
	$session = $md5str;
	
}
else {
	include("language.php");
	DisplayLoginForm($lang[pleaseenter]);
}

function uselogger($comment,$dummy_extra_not_used){
	global $REMOTE_ADDR, $HTTP_SERVER_VARS, $actuser, $username, $user, $HTTP_USER_AGENT,$name,$logqueries;
	
	qlog(">>>>> OLD USELOGGER FUNCTION IN USE");
	
	if ($GLOBALS['pagelog']) {
		$GLOBALS['pagelog'] .= "$ip $url $HTTP_USER_AGENT  $qs $name";
	}


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

	$txt_to_email = "IP-address: $ip<BR>Page: $url<BR>Repository:" . $GLOBALS['title'] . "<BR>User: $name<BR>Message:<BR><b>$qs</b>";

	if (stristr($qs,"warning")) {
		ProcessTriggers("log_warning",0,"Administrative trigger",$txt_to_email);
	}
	if (stristr($qs,"error")) {
		ProcessTriggers("log_error",0,"Administrative trigger",$txt_to_email);
	}

	if ($GLOBALS['logqueries']) {
		qlog("'$ip', '$url', '$HTTP_USER_AGENT' , '$qs','$name'");
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
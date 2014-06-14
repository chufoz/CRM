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

include("config.inc.php");
include("getset.php");
include("language.php");

$nonavbar = 1;

//include("header.inc.php");

if ($mipassword=="*NONE*") {
		$password=$mipassword;
} 

if ($password<>$mipassword) {
				print "<tr><td><form name='pwd' method='post'>";
				if ($password) {
				    print "<b>$lang[wrongpwd].</b><br>";
				} else {
				    print "<b>$lang[plzenterpwd]:</b><br>"; 
				}
				   print "<br><input type='password' name='password'><br><br><input type='submit' name='knop' value='Log in'>";
				   print "<input type='hidden' name='summary' value='on'>";
				   print "<input type='hidden' name='entities_owned_by_users' value='on'>";
				   print "<input type='hidden' name='entities_assigned_to_users' value='on'>";
				   print "<input type='hidden' name='self_assigned' value='on'>";
				   print "<input type='hidden' name='didntbelonghere' value='on'>";
				   print "<input type='hidden' name='elseaction' value='on'>";
				   print "<input type='hidden' name='entities_per_customer' value='on'>";
   				   print "<input type='hidden' name='overdue_per_assignee' value='on'>";
   				   print "<input type='hidden' name='firstlogin' value='on'>";
				   print "</form></td><tr></table></body></html>";
				   exit;
}

// Begin super statistics

	
	if (!$_REQUEST['year']) {
		$year = date("Y");
	} else {
		$year = $_REQUEST['year'];
	}
	?>
	<HTML>
	<HEAD>
	<TITLE>CRM-CTT Statistics</TITLE>
	<script language='javascript'> 
    var arrTemp=self.location.href.split("?"); 
    var picUrl = (arrTemp.length>0)?arrTemp[1]:""; 
    var NS = (navigator.appName=="Netscape")?true:false; 

     function FitPic() { 
       iWidth = (NS)?window.innerWidth:document.body.clientWidth; 
       iHeight = (NS)?window.innerHeight:document.body.clientHeight; 
       iWidth = document.images[0].width - iWidth; 
       iHeight = document.images[0].height - iHeight; 
       window.resizeBy(iWidth + 20, iHeight + 30); 
       self.focus(); 
     }; 
	</script>
	</HEAD>
	<BODY onload='javascript:FitPic();'>
	<?
	print "<img src='superstatsimg.php?year=" . $year . "&type=" . $_REQUEST['type'] . "'>";
	
EndHTML();
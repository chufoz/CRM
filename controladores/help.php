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

// Help CSV file for CRM
extract($_REQUEST);

if (!$id) { 
	print "Error. No ID received."; 
	exit;
}

if ($id=="contents") {
	$nonavbar=1;
	$thisishelp=1;
	include("header.inc.php");
	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]help ORDER BY title";
	$result= mcq($sql,$db);
	print "<tr><td colspan=12><table border=0 width='100%'>";
	print "<tr><td width='100%'><b><font size=+1>" . $lang['helpcontents'] . "</font></b></td></tr>";
	
	while ($help = mysql_fetch_array($result)) {
		$help[title] = ereg_replace("<center>","",$help[title]);
		$help[title] = ereg_replace("</center>","",$help[title]);
		$help[title] = ereg_replace("<b>","",$help[title]);
		$help[title] = ereg_replace("</b>","",$help[title]);

		if ($help[title]<>"S.O.B.") { 
			print "<tr><td><a href='help.php?id=$help[helpid]' class='topnav' style='cursor: help'>$help[title]</a></td></tr>"; }
	}
	print "<tr><td width='100%'>&nbsp;</td></tr>";	
	print "<tr><td width='100%'><hr></td></tr>";
	print "<tr><td width='100%' align='center'>";
	?>
	<form name=dummy><input type=submit name=dummy2 value="Close this window" OnClick='javascript:window.close()'></form>
	<?
	print "</td></tr>";
	print "</table>";
	print "</table></body></html>";
} else {

	$thisishelp=1;
	$nonavbar=1;
	include("header.inc.php");
	$printbox_size="95%";
	pushhelp($id);

}

function pushhelp($id) {
	global $noc,$o;
	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]help WHERE helpid='$id'";
	$result= mcq($sql,$db);
	$help = mysql_fetch_array($result);
	
	$o .=  "<table><tr><td colspan=12><table border=0 width='100%'>";
	$o .=  "<tr><td width='100%'><b><font size=+1>$help[title]</font></b></td></tr>";
	$o .=  "<tr><td width='100%'>";
	$o .=  $help[content];
	$o .=  "</td></tr>";
	$o .=  "<tr><td width='100%'>&nbsp;</td></tr></table></table>";	

	
	printbox($o);
	print "<table width='100%'>";
	print "<tr><td width='100%' align='center'>";
	
	print "<form name=dummy><input type=submit name=dummy2 value=\"Close this window\" OnClick='javascript:window.close()'></form>";
	
	print "</td></tr>";
	print "</table>";
	print "</table>";
	
	
	print "</body></html>";
}

function printbox($msg)
{
	global $printbox_size;
	if (!$printbox_size) {
		$printbox_size = "70%";
	}
	?>
	    <center><table border='1' width='<? echo $printbox_size;?>' bgcolor='#F2F2F2' cellspacing='0' cellpadding='4' bordercolor='#CECECE'>
		<tr><td colspan=2><center><? echo $msg;?></center></td></tr>
		</table></center>
		<br>
	<?
	unset($printbox_size);
} // end func
?>
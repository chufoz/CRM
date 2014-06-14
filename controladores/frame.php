<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This file generates the framed window for printing PDF files. The
 * PDF file is loaded and printed from a 0-pixel frame (so it's in-
 * visible for the user). The top frame closes the window after 5 secs.
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
include("config.inc.php");
include("getset.php");


if ($code) {
	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Printing your document...</TITLE>
<META NAME="Generator" CONTENT="CRM-CTT">

<?
DisplayCSS();
?>
</HEAD><BODY>

	<?
	print "<center><table width='100%'><tr><td align='center'><img src='printer.gif'></td></tr><tr><td align='center'>Printing document..</td></tr></table></center>";
	?>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
<!--
	setTimeout('top.close()',10000);
//-->
</SCRIPT>

<table align="center"><tr><td>
<div style="font-size:8pt;padding:2px;border:solid black 1px">
<span id="progress1">   &nbsp;&nbsp;</span>
<span id="progress2">   &nbsp;&nbsp;</span>
<span id="progress3">   &nbsp;&nbsp;</span>
<span id="progress4">   &nbsp;&nbsp;</span>
<span id="progress5">   &nbsp;&nbsp;</span>
<span id="progress6">   &nbsp;&nbsp;</span>
<span id="progress7">   &nbsp;&nbsp;</span>
<span id="progress8">   &nbsp;&nbsp;</span>
<span id="progress9">   &nbsp;&nbsp;</span>
</div>
</td></tr></table>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
var progressEnd = 9;		// set to number of progress <span>'s.
var progressColor = 'blue';	// set to progress bar color
var progressInterval = 1000;	// set to time between updates (milli-seconds)

var progressAt = progressEnd;
var progressTimer;
function progress_clear() {
	for (var i = 1; i <= progressEnd; i++) document.getElementById('progress'+i).style.backgroundColor = 'transparent';
	progressAt = 0;
}
function progress_update() {
	progressAt++;
	if (progressAt > progressEnd) progress_clear();
	else document.getElementById('progress'+progressAt).style.backgroundColor = progressColor;
	progressTimer = setTimeout('progress_update()',progressInterval);
}
function progress_stop() {
	clearTimeout(progressTimer);
	progress_clear();
}
progress_update();		// start progress bar
</script>



</BODY>
</HTML>
	<?
	exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Printing........</TITLE>
<META NAME="Generator" CONTENT="EditPlus">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
</HEAD>
<FRAMESET ROWS="10,0">
	<FRAME SRC="frame.php?code=1" NAME="code">
	<FRAME SRC="<? echo $target;?>&print=1&stashid=<?echo $_REQUEST['stashid'];?>" NAME="pdf"> 
</FRAMESET>
</HTML>
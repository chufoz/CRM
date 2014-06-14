<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This file does several things :)
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
extract($_REQUEST);
print "<html><body>";
print "<pre>";
print "Paste error code, starting from (and including) CEC|\n";

	?>
	<form name='ec' method='POST'>
	Error code: 
	<input type='text' name='errorcode'>
	<input type='submit'>
	</form>
	<?
 print "\n";
if ($errorcode) {

	print "Processing error code:\n" . $errorcode . "\n\n";

	$errorcode = split("\|",$errorcode);

	print "Error   : " . base64_decode($errorcode[2]) . "\n";
	print "Query   : " . base64_decode($errorcode[4]) . "\n";
	print "Script  : " . base64_decode($errorcode[6]) . "\n";
	print "Version : " . base64_decode($errorcode[8]) . "\n";

}


print "</pre></body></html>";
?>
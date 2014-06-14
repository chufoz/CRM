<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This file is used to edit a complete language pack at once, 
 * always using the current language pack.
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

include("header.inc.php");

if ($upd) {
	if (!$edlan) {
		echo "EXIT mzzl";
		exit;
	}

//	print "b:  " . sizeof($valuenew) . "<br>";
	
	for ($x=0;$x<(sizeof($valuenew));$x++) {
	    $valuenew[$x] = addslashes($valuenew[$x]);
//		Adding slashes doens't work as desired	
	    $sql = "UPDATE $GLOBALS[TBL_PREFIX]languages SET TEXT='$valuenew[$x]' WHERE TEXTID='$id_lan[$x]' AND LANGID='$edlan'";
//		print "$sql<br><br>";
		mcq($sql,$db);


	}
	print "<table width=90%>";
	print "<tr><td colspan=5><font size='+1'>Edit language pack</font></td></tr>"; 
	print "<tr><td colspan=5><hr></td></tr>";
	print "<tr><td>Your changes are saved to the database.</td></tr>";
//	print "<tr><td colspan=5><hr></td></tr>";
	print "<tr><td><img src='arrow.gif'>&nbsp;<a class='bigsort' href='dictedit.php?tab=80' style='cursor:pointer'>back to main language management page</a></td></tr>";
	print "</table>";
	print "</body></html>";
	exit;

}



	print "<table><form name=editlanentries method=POST>";



	print "<tr><td colspan=5><font size='+1'>Edit language pack</font></td></tr>"; 
	print "<tr><td colspan=5><hr></td></tr>";
	print "<tr><td colspan=5>Language entries for language $edlan &nbsp;&nbsp;<img src='arrow.gif'>&nbsp;<a class='bigsort' href='dictedit.php?tab=80' style='cursor:pointer'>back to main language management page</a></td></tr>";
	print "<tr><td colspan=3>&nbsp;</td></tr>";
	//print "<tr><td>[<a class='bigsort' href='dictedit.php?tab=80' style='cursor:pointer'>back to main language management page</a>]</td></tr>";

    print "<tr><td>id</td><td></td><td><b>Tag</b></td><td>Current value</td><td><b>New value</b><input type='hidden' name='edlan' value='$edlan'></td></tr>";
	
	$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='$edlan' ORDER BY TEXTID";
	$result= mcq($sql,$db);
	while ($blad= @mysql_fetch_array($result)){
	
	print "<tr><td>$blad[id]<td></td><td><input type=hidden size='50' name='namenew[]' value='$blad[TEXTID]' DISABLED>$blad[TEXTID]";
	print "</td><td>" . $blad['TEXT'];

        print "</td><td><input type='text' name='valuenew[]'";
	$blad[TEXT] = stripslashes($blad[TEXT]);
	$blad[TEXT] = ereg_replace("<","&lt;",$blad[TEXT]);
	$blad[TEXT] = ereg_replace(">","&gt;",$blad[TEXT]);
	$blad[TEXT] = ereg_replace("\"","&quot;",$blad[TEXT]);

    print " value=\"" . $blad[TEXT] . "\" size='100'><input type=hidden name=id_lan[] value='$blad[TEXTID]'></td><td></td></tr>";
	}
    print "<tr><td colspan=5><br><input type=hidden value='1' name='upd'><input type=submit name=knop value='$lang[apply]'></td></tr></table>";

	EndHTML();
?>
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
if ($NoMenu) {
	$nonavbar=1;
	}
include("header.inc.php");
?>
<SCRIPT LANGUAGE='JavaScript'>
		<!--
		function applyalarm(e1)
		{
				window.opener.document.EditEntity.duedate.value = e1;
				window.close();
		}
	
		//-->
		</SCRIPT>
<?

//$debug = 1;

if ($content) {
	$email = array();
	$email = split(",",$content);
}
if ($Submitted && sizeof($email)<1 && $email[0]=="") {
			    print "Looks like all email addresses were deleted. The entire alarm setting has been deleted.";
				$sql= "DELETE FROM $GLOBALS[TBL_PREFIX]calendar WHERE eID='$eID'";
				if ($debug) { print "\nSQL: $sql\n"; }
				mcq($sql,$db);
				journal($eID,"Alarm setting disabled");
				if ($close_on_next_load) {

				// In this case the last update window was loaded in a pop window. Now 
				// the form is submitted, the window may close itself.. :)
						?>
						<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
						<!--
							alert('Alarm <? echo $lang[wasdeleted];?>');
							window.close();
						//-->
						</SCRIPT>
						<?
				}

} 

if (!$eID) {
		print "An error occured. No eID was found.<br>";
		exit;
	} elseif ($eID && $email && $duedate) {
	
		$dd = $duedate;

		if (IsDate($dd)) {
				
				// Date is OK
			
		} else {
		    		    ?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		    <!--
				alert("The date you entered is invalid.");
		    //-->
		    </SCRIPT>
			<?
			$error = 1;
		}
		//include("header.inc.php");		

		if (!$error) {		
		


				    




		if (strlen($dag)==1) {
				$dag = "0" . $dag;
		}
		if (strlen($maand)==1) {
				$maand = "0" . $maand;
		}
			
		for ($c=0;$c<sizeof($email);$c++ ) {
		    if ($email[$c]<>"") {
				$emails .= "$email[$c],"; 		        
		    }
		}
		$emails = substr($emails,0,(strlen($emails))-1);  // substract last comma

		print "Alarm set to $duedate. The warning will be emailed to $emails.<br>";

		$duedate = explode("-",$duedate); // Must become basicdate! No '-'s

		// Add zero's ie. 2-7-2003 must become 02-07-2003

		if (strlen($duedate[0])==1) {
				$duedate[0] = "0" . $duedate[0];
		}
		if (strlen($duedate[1])==1) {
				$duedate[1] = "0" . $duedate[1];
		}
			
		$duedate = $duedate[0] . $duedate[1] . $duedate[2];


		// $debug=1;
			

		    

		$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]calendar WHERE eID='$eID'";
		if ($debug) { print "\nSQL: $sql\n"; }
		$result= mcq($sql,$db);

		if ($result= mysql_fetch_array($result)) {
		    print "An earlier entry was updated.";
			$sql= "UPDATE $GLOBALS[TBL_PREFIX]calendar SET user='" . $GLOBALS['USERID'] . "', basicdate = '$duedate', emailadress='$emails' WHERE eid='$eID'";
			if ($debug) { print "<BR>SQL: $sql<br>"; }
			$result= mcq($sql,$db);
			journal($eID,"Alarm date updated from $OldAlarmVar to $duedate ($emails)");
		} else {
		    print "A new due date entry was added.";
			$sql= "INSERT INTO $GLOBALS[TBL_PREFIX]calendar(user,eid,emailadress,basicdate) VALUES('" . $GLOBALS['USERID'] . "','$eID','$emails','$duedate')";
			if ($debug) { print "<BR>SQL: $sql<BR>"; }
			$result= mcq($sql,$db);
			journal($eID,"Alarm date added - $duedate ($emails)");
			$exists = 0;
		}

	if ($close_on_next_load) {

	// In this case the last update window was loaded in a pop window. Now 
	// the form is submitted, the window may close itself.. :)
			?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				window.close();
			//-->
			</SCRIPT>
			<?
	}
		} // enf of if error
	 // end if there were email adresses
			if (!$error) {
				exit;   
			}
} else {
//	print "Nope $eID && $email && $duedate";
}




	$sql= "SELECT count(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted<>'y'";
	$result= mcq($sql,$db);
	$result= mysql_fetch_array($result);


	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='$eID'";
	$result= mcq($sql,$db);
	$ea = mysql_fetch_array($result);
	
//	$sql = "SELECT fullname AS name,email FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id='$ea[owner]'";
//	$result = mcq($sql,$db);
//	$owner = mysql_fetch_array($result);
//	$owner = $owner[name];
	
	$assignee = GetUserName($ea['assignee']);
	$owner = GetUserName($ea['owner']);


	print "<BODY><TABLE><TR><TD><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[alarmdate]&nbsp;</legend><table><tr><td>";
	print "<fieldset>Setting alarm date for entity $eID owned by $owner, assigned to $assignee, ";
	print "Current status: $ea[status]<br>";
	print "Priority: $ea[priority], category: $ea[category]";
	print "<hr>";

		$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]calendar WHERE eID='$eID'";
		$result= mcq($sql,$db);

		if ($result= mysql_fetch_array($result)) {
		    print "<b><img src='error.gif'>&nbsp;This entity already has an alarm date! You can alter it by using the form below.</font></b>";
			$already = 1;
		} else {				
			print "<b>Default printed values are the e-mail adresses of the assignee and the owner of this entity.</b> Since this entity does not have any alarms set yet, the given due date is suggested as the alarm date.";
			//$result['emailadress'] = GetUserEmail($ea['assignee']);
		}						
?>
		
		
</FIELDSET><BR><FIELDSET><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;E-mail&nbsp;</LEGEND>
<FORM NAME="EditEntity">
<?
print "Current due date for this entity is $ea[duedate]. ";
?>
<br><b>
Use a comma (,) to seperate more than one e-mail address, <br>just delete an adress to avoid sending an alarm note.
</b><br><br>
<?
print "<table>";
if (!$already) {
			if (GetUserEmail($ea['owner'])) {
				$result[emailadress] .= "," . GetUserEmail($ea['owner']);
			}
			if (GetUserEmail($ea['assignee'])) {
				$result[emailadress] .= "," . GetUserEmail($ea['assignee']);
			}
}
			print "<tr><td><b>To:</b></td><td>";
			print "<select name='unimp'>";
			$sql = "SELECT FULLNAME,EMAIL from $GLOBALS[TBL_PREFIX]loginusers WHERE LEFT(FULLNAME,3) <> '@@@' AND active<>'no'";
			$res1 = mcq($sql,$db);
			while ($users = mysql_fetch_array($res1)) {
				print "<option value='" . $users['EMAIL'] . "'>" . $users['FULLNAME'] . "</option>";
			}
			print "</select>";
			
			?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				function AddCurAddr() {
					
					a = document.EditEntity.content.value;
					b = document.EditEntity.unimp.value;
					c = a + "," + b;
					document.EditEntity.content.value = c;
				}
			//-->
			</SCRIPT>
			<?

			print "<input type='button' OnClick='javascript:AddCurAddr();' value='>>>' name='contentButton'>&nbsp;<input type='text' name='content' value='" . $result[emailadress] . "' size=50>";

			print "<input type='hidden' name='Submitted' value='1'>";
			print "<input type='hidden' name='CalendarPopped' value='0'>";
			
			print "</td></tr>";

print "<tr><td>$lang[alarmdate]&nbsp;&nbsp;&nbsp;&nbsp;</td>";

if ($result[basicdate]) {
    $ea[duedate] = substr($result[basicdate],0,2) . "-" . substr($result[basicdate],2,2) . "-" . substr($result[basicdate],4,4);
}

// the following is to prevent the calendar from popping up twice
// this is by far the most dirty contruction in CRM-CTT :)
// Hey, it works!

?>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
<!--
	function popcalendarfirst() {
		
		if (document.EditEntity.CalendarPopped.value=='0') {
			document.EditEntity.CalendarPopped.value='1';
			popcalendarforalarmdate();
		} else {
			document.EditEntity.CalendarPopped.value='0';
		}
	}
//-->
</SCRIPT>
<?

if ($already) {
		print "<td><input type='text' size='13' name='displayDate' value='" . TransformDate($ea[duedate]) . "' OnFocus='javascript:popcalendarfirst();'><input type='hidden' size='13' name='duedate' value='" . $ea[duedate] . "' OnChange=\"javascript:AdjustDateToUSAFormat(document.EditEntity.duedate.value);\">";    
		print "<input type='hidden' name='OldAlarmVar' value='$ea[duedate]'>";
} else {
	    print "<td><input type='text' size='13' name='displayDate' value='" . TransformDate($ea[duedate]) . "' OnFocus='javascript:popcalendarfirst();'> (suggestion)<input type='hidden' size='13' name='duedate' value='" . $ea[duedate] . "' OnChange=\"javascript:AdjustDateToUSAFormat(document.EditEntity.duedate.value);\">";
}
//close_on_next_load
if ($nonavbar) {
		print "<input type='hidden' size='13' name='close_on_next_load' value='1'>";    
		print "<input type='hidden' size='13' name='nonavbar' value='1'>";    
}
print "<input type=hidden name='eID' value='$ea[eid]'>";

print "</td></tr></table></FIELDSET><br>";
print "<INPUT TYPE='submit' VALUE='$lang[save]' name='knop'>&nbsp;";

if ($nonavbar) {
	print "<input class='txt' type='button' name=sb value='$lang[cancel]' OnClick='javascript:window.close();'>";
} else {
	print "<input class='txt' type='button' name=sb value='$lang[cancel]' OnClick='javascript:history.back(-1);'>";
}
?>
</FORM>
</TABLE></FIELDSET></TD></TR></TABLE>
</BODY>
</HTML>

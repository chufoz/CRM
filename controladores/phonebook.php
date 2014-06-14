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

if ($NoMenu) {
	$nonavbar=1;
	}

include("header.inc.php");

CheckPageAccess("pb");

?>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
<!--
function poppb()	{
				newWindow = window.open('phonebook.php?nonavbar=$nonavbar&nonavbar=1', 'myWindow2','width=800,height=200,directories=0,status=1,menuBar=0,scrollBars=1,resizable=1');
				newWindow.focus();
		}
//-->
</SCRIPT>
<?

print "<table><tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[phonebook1]&nbsp;</legend>";

if ($deleteconfirm) {
			$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]phonebook WHERE id='$deleteconfirm'";
			mcq($sql,$db);
			print "<table><tr><td>Record $deleteconfirm $lang[wasdeleted].</td></tr></table>";
			unset($search);
			unset($delete);
			unset($deleleconfirmed);
			unset($add);
			uselogger("Entry $deleteconfirm deleted from phonebook","");
}

if ($delete) {
			$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]phonebook WHERE id='$delete'";
			$result= mcq($sql,$db);
			$pb= mysql_fetch_array($result);
			print "<table width=100%><tr>";
			print "<td colspan=2><b>$lang[pbdelconf]</b></td></tr><tr>";
			print "<td><form name='addtopb' method='POST' ACTION='phonebook.php'>$lang[fname]</td><td><input size=50 type='text' name='fname' value='$pb[Firstname]' DISABLED></td></tr>";
			print "<tr><td>$lang[lname]</td><td><input size=50 type='text' name='lname' value='$pb[Lastname]' DISABLED></td></tr>";
			print "<tr><td>$lang[tel]</td><td><input size=50 type='text' name='tel' value='$pb[Telephone]' DISABLED></td></tr>";
			print "<tr><td>$lang[company]</td><td><input size=50 type='text' name='company' value='$pb[Company]' DISABLED></td></tr>";
			print "<tr><td>$lang[dep]</td><td><input size=50 type='text' name='dep' value='$pb[Department]' DISABLED></td></tr>";
			print "<tr><td>$lang[title]</td><td><input size=50 type='text' name='titles' value='$pb[Title]' DISABLED></td></tr>";
			print "<tr><td>$lang[room]</td><td><input size=50 type='text' name='room' value='$pb[Room]' DISABLED></td></tr>";
			print "<tr><td>E-mail</td><td><input size=50 type='text' name='email' value='$pb[Email]' DISABLED></td></tr>";	
			print "<tr><td colspan=2><input type='hidden' name='deleteconfirm' value='$delete'><input type='submit' name='knoppie' value='$lang[deletepb]'></td></tr></table>";
EndHTML();
			exit;

}

if ($lname && ($addfilled || $editfilled)) {
		if ($addfilled) {
			$sql="INSERT INTO $GLOBALS[TBL_PREFIX]phonebook(Firstname,Lastname,Telephone,Company,Department,Title,Room,Email) VALUES('$fname','$lname','$telep','$company','$dep','$titles','$room','$email')";
			mcq($sql,$db);
			$add=1;
			print "<table><tr><td>$lang[pbrecadded]</td></tr></table>";
			uselogger("Phonebook entry added","");
		} elseif ($editfilled) {
			$sql = "UPDATE $GLOBALS[TBL_PREFIX]phonebook SET Firstname='$fname',Lastname='$lname',Telephone='$telep',Company='$company',Department='$dep',Title='$titles',Room='$room',Email='$email' WHERE id='$edit'";
			mcq($sql,$db);
			print "<table><tr><td>$lang[pbapp]</td></tr></table>";
			uselogger("Phonebook entry $edit edited","");
		}
}

if ($add || $edit) {
		?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
			function checkform() {
				if (document.addtopb.lname.value=='') {
					alert("<? echo $lang[pbwarning];?>");
				} else {

					document.addtopb.submit();
				}
			}
			//-->	
			</SCRIPT>
		<?
		if ($edit) {
				$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]phonebook WHERE id='$edit'";
				$result= mcq($sql,$db);
				$pb= mysql_fetch_array($result);
				
			}
		if ($add) {
			print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[pbaddrec]&nbsp;</legend><table width=100%><tr>";
		} else {
			print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$pb[Firstname] $pb[Lastname]</legend><table width=100%><tr>";
		}

		print "<td><form name='addtopb' method='POST'>$lang[fname]</td><td><input size=50 type='text' name='fname' value='$pb[Firstname]'></td></tr>";
		print "<tr><td>$lang[lname]</td><td><input size=50 type='text' name='lname' value='$pb[Lastname]'></td></tr>";
		print "<tr><td>$lang[tel]</td><td><input size=50 type='text' name='telep' value='$pb[Telephone]'></td></tr>";
		print "<tr><td>$lang[company]</td><td><input size=50 type='text' name='company' value='$pb[Company]'></td></tr>";
		print "<tr><td>$lang[dep]</td><td><input size=50 type='text' name='dep' value='$pb[Department]'></td></tr>";
		print "<tr><td>$lang[title]</td><td><input size=50 type='text' name='titles' value='$pb[Title]'></td></tr>";
		print "<tr><td>$lang[room]</td><td><input size=50 type='text' name='room' value='$pb[Room]'></td></tr>";
		print "<tr><td>E-mail</td><td><input size=50 type='text' name='email' value='$pb[Email]'></td></tr>";
		
		if ($add) {
			print "<tr><td colspan=8><input type='hidden' name='addfilled' value='1'>";
			print "<input type='button' name='addknopje' value='$lang[addtopb]' OnClick='javascript:checkform();'></form></td></tr>";
		} else {
			print "<tr><td colspan=2><input type='hidden' name='editfilled' value='$edit'>";
			print "<input type='button' name='addknopje' value='$lang[apply]' OnClick='javascript:checkform();'></form></td></tr>";
		}
		print "</table></fieldset>";
EndHTML();
		exit;
}

uselogger("Phonebook accessed","");

if (!$search && !$zoek) {
	prtsrc("0");
	if ($nonavbar) {
		print "<hr>";
	}
} else {
	prtsrc("0");
	if ($nonavbar) {
		print "<hr>";
	}
	
	if ($PBwhere1=="customers") {

		print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[pbresults] '$search' &nbsp;[$lang[customers]]&nbsp;</legend>";
		print "<table width=100% class='crm'>";
		print "<tr><td><b>$lang[customer]</td><td><b>$lang[contact]</td><td><b>$lang[contacttitle]</td><td><b>$lang[contactphone]</td><td><b>$lang[contactemail]</td><td><b>$lang[customeraddress]</td><td><b>$lang[custhomepage]</td></tr>";
		$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]customer WHERE custname LIKE '%$search%' OR contact LIKE '%$search%' OR contact_title LIKE '%$search%' OR contact_phone LIKE '%$search%' OR contact_email LIKE '%$search%' OR cust_address LIKE '%$search%' OR cust_remarks LIKE '%$search%' OR cust_homepage LIKE '%$search%' ORDER BY custname";
		$result= mcq($sql,$db);
		while ($pb= mysql_fetch_array($result)) {
				$auth = CheckCustomerAccess($pb['id']);
				if ($auth == "ok" || $auth == "readonly") {
					$html = "<table><tr><td bgcolor=\'#CCCCCC\'><b>$pb[custname] $pb[contact]</b></td></tr><tr><td><img src=\'arrow.gif\'>&nbsp;<a href=\'phonebook.php?nonavbar=$nonavbar&edit=$pb[id]\' class=\'bigsort\'>Edit</a></td></tr><tr><td><img src=\'arrow.gif\'>&nbsp;<a href=\'phonebook.php?nonavbar=$nonavbar&delete=$pb[id]\' class=\'bigsort\'>Delete</td></tr></table>";
					$a = "<tr onmouseover=\"javascript:style.background='#CCCCCC';\"  onmouseout=\"javascript:style.background='#FFFFFF';\"><td><a href='phonebook.php?PBwhere1=customers&nonavbar=$nonavbar&search=$pb[custname]' class='bigsort'>$pb[custname]</a>&nbsp;</td><td><a href='phonebook.php?PBwhere1=customers&nonavbar=$nonavbar&search=$pb[contact]' class='bigsort'>$pb[contact]</a>&nbsp;</td><td><a href='phonebook.php?PBwhere1=customers&nonavbar=$nonavbar&search=$pb[contact_title]' class='bigsort'>$pb[contact_title]</a>&nbsp;</td><td><a href='phonebook.php?PBwhere1=customers&nonavbar=$nonavbar&search=$pb[contact_phone]' class='bigsort'>$pb[contact_phone]</a>&nbsp;</td><td><a href='phonebook.php?PBwhere1=customers&nonavbar=$nonavbar&search=$pb[contact_email]' class='bigsort'>$pb[contact_email]</a>&nbsp;</td><td><a href='phonebook.php?PBwhere1=customers&nonavbar=$nonavbar&search=$pb[cust_address]' class='bigsort'>$pb[cust_address]</a>&nbsp;</td><td><a href='phonebook.php?PBwhere1=customers&nonavbar=$nonavbar&search=$pb[cust_homepage]' class='bigsort'>$pb[cust_homepage]</a>&nbsp;</td></tr>";
					print $a;
					$teller++;
				}	
			}
		if ($teller==0) {
			print "<tr><td colspan=12>$lang[noresults]</td></tr>";
		} else {
			print "<tr><td colspan=12><i>$teller $lang[resfound]</i></td></tr>";
		}
		
		if ($teller>25) {
			prtsrc("1");
		} 
		print "</table></fieldset>";
		$teller=0;
	} // end if PBwhere1 == customers

	if ($PBwhere2=="phonebook") {
		if ($PBwhere1=="customers") { print "<br><br>"; } // spacer

//		print "<table border='1' width='100%' bgcolor='#F2F2F2' cellspacing='0' cellpadding='4' bordercolor='#CECECE'>";
//		print "<tr><td colspan =12>$lang[pbresults] '$search' &nbsp;[$lang[phonebook1]]</td></tr>";
		print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[pbresults] '$search' &nbsp;[$lang[phonebook1]]&nbsp;</legend>";
		print "<table width=100% class='crm'>";

		
		print "<tr><td><b>$lang[fname]</td><td><b>$lang[lname]</td><td><b>$lang[tel]</td><td><b>$lang[company]</td><td><b>$lang[dep]</td><td><b>$lang[title]</td><td><b>$lang[room]</td><td><b>E-mail</td><td><b>$lang[options]</b></td></tr>";
		$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]phonebook WHERE Firstname LIKE '%$search%' OR Lastname LIKE '%$search%' OR Telephone LIKE '%$search%' OR Company LIKE '%$search%' OR Department LIKE '%$search%' OR Title LIKE '%$search%' OR Room LIKE '%$search%' ORDER BY Lastname";
		$result= mcq($sql,$db);
		while ($pb= mysql_fetch_array($result)) {
				$html = "<table><tr><td bgcolor=\'#CCCCCC\'><b>$pb[Firstname] $pb[Lastname]</b></td></tr><tr><td><img src=\'arrow.gif\'>&nbsp;<a href=\'phonebook.php?nonavbar=$nonavbar&edit=$pb[id]\' class=\'bigsort\'>Edit</a></td></tr><tr><td><img src=\'arrow.gif\'>&nbsp;<a href=\'phonebook.php?nonavbar=$nonavbar&delete=$pb[id]\' class=\'bigsort\'>Delete</td></tr></table>";
				$a = "<tr onmouseover=\"javascript:style.background='#CCCCCC';\"  onmouseout=\"javascript:style.background='#FFFFFF';\"><td><a href='phonebook.php?PBwhere2=phonebook&nonavbar=$nonavbar&search=$pb[Firstname]' class='bigsort'>$pb[Firstname]</a>&nbsp;</td><td><a href='phonebook.php?PBwhere2=phonebook&nonavbar=$nonavbar&search=$pb[Lastname]' class='bigsort'>$pb[Lastname]</a>&nbsp;</td><td><a href='phonebook.php?PBwhere2=phonebook&nonavbar=$nonavbar&search=$pb[Telephone]' class='bigsort'>$pb[Telephone]</a>&nbsp;</td><td><a href='phonebook.php?PBwhere2=phonebook&nonavbar=$nonavbar&search=$pb[Company]' class='bigsort'>$pb[Company]</a>&nbsp;</td><td><a href='phonebook.php?PBwhere2=phonebook&nonavbar=$nonavbar&search=$pb[Department]' class='bigsort'>$pb[Department]</a>&nbsp;</td><td><a href='phonebook.php?PBwhere2=phonebook&nonavbar=$nonavbar&search=$pb[Title]' class='bigsort'>$pb[Title]</a>&nbsp;</td><td><a href='phonebook.php?PBwhere2=phonebook&nonavbar=$nonavbar&search=$pb[Room]' class='bigsort'>$pb[Room]</a>&nbsp;</td><td>";
				
				if ($pb[Email] && strstr($pb[Email],"@")) {
					$a .= "<a href='javascript:popEmailToPBScreen(" . $pb['id'] . ");' class='bigsort'>$pb[Email]</a>";
				} elseif ($pb[Email]) {
					$a .= $pb[Email];
				} else {
					$a .= "&nbsp;";
				}

				$a .= "</td><td onmouseover=\"javascript:style.background='#CCCCCC';return overlib('" . $html . "', STICKY)\"  onmouseout=\"javascript:style.background='#F2F2F2';nd();\"><img src='arrow.gif'>&nbsp;<a class='topnav' style='cursor:pointer' >Options</a></td></tr>";
				//$a = preg_replace("/($search)/i", "<u>\\1</u>", $a);
				print $a;
				$teller++;
			}
		if ($teller==0) {
			print "<tr><td colspan=12>$lang[noresults]</td></tr>";
		} else {
			print "<tr><td colspan=12><i>$teller $lang[resfound]</i></td></tr>";
		}
		
		if ($teller>25) {
			prtsrc("1");
		} 
		print "</table>";
	} // end if PBwhere2 == phonebook
} // end if $search

EndHTML();

?>
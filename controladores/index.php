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

$tab =21;
if ($_REQUEST['qlog'] && $_REQUEST['logstash']) {
	
	include("config.inc.php");
	include("getset.php");
	print "<HTML><TITLE>CRM-CTT QLOG OUTPUT</TITLE><BODY BGCOLOR='#000000'><pre>";
	print "<font color='#ffffff'>CRM-CTT Qlog Output repository $title</font>\n";
	print "<font color='#ffffff'>db: " . $database[$repository_nr] . ", host: " . $host[$repository_nr] . ", prfx: " . $table_prefix[$repository_nr] . "</font>\n\n";
	print "<font color='#ffffff'>";
	print PopStashValue($_REQUEST['logstash']);
	DropStashValue($_REQUEST['logstash']);
	print "</font></pre></BODY></HTML>";
	exit;
}

$DateFormat = "dd-mm-yyyy";
// $DateFormat = "mm-dd-yyyy";

if (filesize("config.inc.php")==0) {
    ?>
    <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
    <!--
    document.location='install.php';
    //-->
    </SCRIPT>
    <?
}
if ($SwitchReposPopup) {
	$nonavbar=1;
	include("header.inc.php");
	print "<table><tr><td>&nbsp;</td><td><br><br><b>Repository:</b><br><br>";
	ShowRepositorySwitcher("index.php?ShowEntitiesOpen=1&tab=23&1093641656","1");
	print "</td></tr></table>";
	EndHTML();
	exit;
}

if ($_REQUEST['UpdateCacheTables']) {
	$epoch = date('U');
	print "<img title='This image is hosted on the project site' alt='sf.net image' src='https://sourceforge.net/sflogo.php?group_id=61096&type=1&" . $epoch . "'>";
	print "<img title='This image is hosted on the project site' alt='sf.net image' src='crm.gif?" . $epoch . "'>";
	$nonavbar = 1;
	include("config.inc.php");
	include("getset.php");
	if ($_REQUEST['UpdateCacheTables'] == "do") {

            //UpdateCacheTables(false,false);

	UpdateCacheTables(false,$GLOBALS['USERID'], true);
	?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		parent.location="<? echo $_REQUEST['urltogo'];?>";
		//-->
		</SCRIPT>
	<?
	} else {
	?>
	<table width=100%><tr><td><center>
	<font face='Trebuchet MS' size='2'></font>
	</center></td></tr><tr><td><center>
	<img src='search.gif'>
	<br>Updating your cache tables ..
	</center></td></tr></table>
	<iframe height=1 width=1 src='index.php?UpdateCacheTables=do'></iframe>
	<?
	flush();
	ob_flush();
//	UpdateCacheTables(false,$GLOBALS['USERID']);
	
	}
	EndHTML();
	exit;
}

if ($swrepos) {
	$nonavbar=1;
	include("header.inc.php");
	ShowRepositorySwitcher("index.php?" . $epoch);
} else {
	include("header.inc.php");
}
if ($ftest) {
	TestFunkyArray();
	EndHTML();
	exit;
}

if ($lan) {
    // personal language setting changed
    $sql= "UPDATE $GLOBALS[TBL_PREFIX]loginusers SET exptime='$lan' WHERE name='$name'";
    mcq($sql,$db);
    ?>
    <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
    <!--
    document.location='index.php?lanok=1&<? echo $epoch;?>';
    //-->
    </SCRIPT>
    <?
}
?>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
<!--
function popupdater(i)
{
    newWindow = window.open('edit.php?NoMenu=1&e=' + i, 'Update' + i,'width=640,height=630,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
}

	

//-->
</SCRIPT>
<?
if ($shortkeys) {
	print "<table><tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;CRM-CTT Shortkey funtions&nbsp;</legend><table>";
	print "<tr><td colspan=2><br><b>CRM-CTT also serves keyboard warriors... </b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;<br></td></tr>";
	print "<tr><td>ALT-[1..0]<br>&nbsp;</td><td>Go to tab [1..0]<br>&nbsp;</td></tr>";
	print "<tr><td>ALT-N<br>&nbsp;</td><td>Add a customer<br>&nbsp;</td></tr>";
	print "<tr><td>ALT-A<br>&nbsp;</td><td>Go to admin section<br>&nbsp;</td></tr>";
	print "<tr><td>ALT-S</td><td>Configure systems values</td></tr>";
	print "<tr><td>ALT-U</td><td>Configure accounts</td></tr>";
	print "<tr><td>ALT-L</td><td>Configure language packs</td></tr>";
	print "<tr><td>ALT-T</td><td>Configure event triggers</td></tr>";
	print "<tr><td>ALT-R<BR>&nbsp;</td><td>Configure repositories<br>&nbsp;</td></tr>";
	print "<tr><td>ALT-P</td><td>Delete an entity physically</td></tr>";
	print "<tr><td>ALT-E</td><td>Edit extra entity fields</td></tr>";
	print "<tr><td>ALT-C</td><td>Calendar</td></tr>";
	print "<tr><td>ALT-D<br>&nbsp;</td><td>Check database<br>&nbsp;</td></tr>";
	print "<tr><td>ALT-M<br>&nbsp;</td><td>Open admin manual <img src='pdf.gif'><br>&nbsp;</td></tr>";
	print "</table></fieldset></td></tr></table>";	
	EndHTML();
	exit;
}

if ($ShowEntitiesOpen) {
    //print "<table><tr><td>&nbsp;&nbsp;</td>";
    ShowEntitiesOpen();
    //print "</td></tr></table>";
    EndHTML();
    exit;
}

if ($_REQUEST['unlock']) {
	print "Unlocking entity......";
	RemoveLocks();
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
		window.close();
	//-->
	</SCRIPT>
	<?
	EndHTML();
	exit;
}

if ($_REQUEST['if_l']) {

	print "</td></tr></table>";
	print "<table width=100%><tr><td>&nbsp;&nbsp;</td><td>";
	print "<IFRAME ID='freem' SRC='" . base64_decode($_REQUEST['if_l']) . "' style='width:98%;' frameborder='yes'></IFRAME>";
	print "</td></tr></table>";
	?>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
		document.getElementById("freem").height=document.body.clientHeight-60;
	//-->
	</SCRIPT>
	<?

	EndHTML();
	exit;

}
if ($_REQUEST['if_t']) {
	qlog("Printing template " . base64_decode($_REQUEST['if_t']) . " on request.");
	print "</td></tr></table>";
	print "<table width=100%><tr><td>&nbsp;&nbsp;</td><td>";
		
		$template = GetFileContent(base64_decode($_REQUEST['if_t']));
		$template = ParseTemplateGeneric($template);
		$template = ParseTemplateCleanup($template);

		print $template;

	print "</td></tr></table>";
	EndHTML();
	exit;

}


$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]loginusers";
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$maxU = $maxU1[0];

$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity";
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$maxE = $maxU1[0];

$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]customer";
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$maxC = $maxU1[0];

if ($maxU==0) {
    $foutje=1;
}
if ($maxC==0) {
    $foutje=$foutje+2;
}

if ($AccSetPointGerbil_EEGS) {
	Author();
	EndHTML();
	exit;
}

if ($_REQUEST['UserMessage']) {
	UserMessage();
	EndHTML();
	exit;
}

if (!$foutje) {
    print "<tr><td>&nbsp;</td><td><table width='100%'><tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;CRM $lang[main]&nbsp;</legend>";
    print "&nbsp;&nbsp;<font size='+2'>" . $title . "</font>";

    if ($mainpagequery) {
        print "<table><tr><td><fieldset><table><tr><td>";
        print base64_decode($mainpagequery);
        print "</td></tr></table></fieldset></td></tr></table>";
    }
    
    $date = date('d-m-Y');
    
    
	
	$sql = "SELECT priority,status,$GLOBALS[TBL_PREFIX]entity.eid AS id, $GLOBALS[TBL_PREFIX]entity.category AS cat,
	$GLOBALS[TBL_PREFIX]loginusers.FULLNAME AS name, $GLOBALS[TBL_PREFIX]customer.custname AS cust 
	FROM $GLOBALS[TBL_PREFIX]entity,$GLOBALS[TBL_PREFIX]customer,$GLOBALS[TBL_PREFIX]loginusers 
	WHERE duedate='$date' AND $GLOBALS[TBL_PREFIX]customer.id=$GLOBALS[TBL_PREFIX]entity.CRMcustomer 
	AND $GLOBALS[TBL_PREFIX]loginusers.id=$GLOBALS[TBL_PREFIX]entity.assignee 
	AND $GLOBALS[TBL_PREFIX]entity.deleted='n' ORDER BY $GLOBALS[TBL_PREFIX]customer.custname";
       
    
    
    print "<table width='100%' border=0><tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[entities]&nbsp;</legend><table border=0>";
    
    
    print "<tr><td colspan='3'><img src='arrow.gif'>&nbsp;<a href='customized.php?TwoTable=1&A=1&B=1&C=1&D=1&E=1' class='bigsort'>" . $fullname . "'s dashboard</a></td></tr>";
    
    $toprint0 = "<tr><td colspan=2>$lang[entitiestoday]:</td></tr>";
    $alreadyshowed = array();
    
    $result= mcq($sql,$db);
	while ($today= mysql_fetch_array($result)) {
//              while ($num<10) {
                        if (!in_array($today[id],$alreadyshowed)) {
                                if (CheckEntityAccess($today['id'])=="read-only" || CheckEntityAccess($today['id'])=="ok") {
                                                        if ($num<10) {
                                                        $toprint1 .= "<tr><td><b>&nbsp;- $today[id]</td><td style=\"background='" . GetStatusColor($today[status]) . "'\">&nbsp;<a href='edit.php?e=$today[id]' class='bigsort'><font color='#000000'>$today[status]</font></a>&nbsp;</td><td style=\"background='" . GetPriorityColor($today[priority]) . "'\">&nbsp;<a href='edit.php?e=$today[id]' class='bigsort'><font color='#000000'>$today[priority]</font></a>&nbsp;</td><td><a href='edit.php?e=$today[id]' class='bigsort'><font color='#000000'>$today[cust]</font></a></td><td><a href='edit.php?e=$today[id]' class='bigsort'><font color='#000000'>$today[cat] ($lang[assignedto] $today[name])</font></a></b></td></tr>";
                                                        }
                                                        array_push($alreadyshowed,$today[id]);
                                                        $num++;
                                                }
                                        }
//              $num++;
//              }
    }

	if ($num==20) {
		//$toprint1 .= "<tr><td>teveel</td></tr>";
	}
    if ($toprint1) {
        print $toprint0 . $toprint1;
    } else {
        print "<tr><td colspan=3>$lang[noentities].</td></tr>";
    }
    
   // Show recent edited entities
    if (!$GLOBALS['ShowRecentEditedEntities']==0) {
        print "<tr><td colspan=3>$fullname's recent " . strtolower($lang[entities]) . ":</td></tr>";
//		print "<table class='crm'>
        //	print "<br>";
        if (!is_numeric($GLOBALS['ShowRecentEditedEntities'])) {
            uselogger("ERROR: ShowRecentEditedEntities value is not a numeric value!","");
            print "<font color='#FF0000'><IMG SRC='error.gif'>&nbsp;ERROR: ShowRecentEditedEntities value is not a numeric value! Recent items list will not be shown.<BR>Please adjust the ShowRecentEditedEntities directive using the administration interface.";
        } else {
            if ($GLOBALS['ShowRecentEditedEntities']>20) {
                $GLOBALS['ShowRecentEditedEntities']=20;
            }
            $sql = "SELECT status,priority,assignee,$GLOBALS[TBL_PREFIX]entity.eid AS id, 
		   $GLOBALS[TBL_PREFIX]entity.category AS cat,tp, $GLOBALS[TBL_PREFIX]customer.custname AS cust 
		   FROM $GLOBALS[TBL_PREFIX]entity,$GLOBALS[TBL_PREFIX]customer 
		   WHERE lasteditby='" . $user_id . "' AND deleted <> 'yes' AND 
		   $GLOBALS[TBL_PREFIX]customer.id=$GLOBALS[TBL_PREFIX]entity.CRMcustomer AND 
		   $GLOBALS[TBL_PREFIX]entity.deleted='n' and status <> 'Cerrado' ORDER BY tp DESC LIMIT " .
		   $GLOBALS['ShowRecentEditedEntities'];
            $result= mcq($sql,$db);
            while ($recent= mysql_fetch_array($result)) {
		        if (CheckEntityAccess($recent['id']) <> "nok") {
	                if (!in_array($recent[id],$alreadyshowed)) {
		                print "<tr><td><b>&nbsp;- $recent[id]</td><td style=\"background-color:" . GetStatusColor($recent[status]) . "\">&nbsp;<a href='edit.php?e=$recent[id]' class='bigsort'><font color='#000000'>$recent[status]</font></a>&nbsp;</td><td style=\"background-color:" . GetPriorityColor($recent[priority]) . "\">&nbsp;<a href='edit.php?e=$recent[id]' class='bigsort'><font color='#000000'>$recent[priority]</font></a>&nbsp;</td><td><a href='edit.php?e=$recent[id]' class='bigsort'><font color='#000000'>$recent[cust]</font></a></td><td><a href='edit.php?e=$recent[id]' class='bigsort'><font color='#000000'>$recent[cat] ($lang[assignedto] " . GetUserName($recent['assignee']) . ")</font></a></b></td></tr>";
			        }
				} // end if access
            }
        }
        // else if !is_numeric
    }
    print "</table></fieldset>";
    print "</td></tr></table>";

	// Main page message
	if (!strlen($GLOBALS['BODY_MainPageMessage'])==0) {
		print "<br><table width='100%'><tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<img src='info.gif'>&nbsp;</legend>";
		print "<table><tr><td>" . $GLOBALS['BODY_MainPageMessage'] . "</td></tr></table></fieldset>";
	}

	// Main page links
    if ($GLOBALS['ShowMainPageLinks']<>"" && trim($GLOBALS['ShowMainPageLinks'])<>",") {
        print "<br><table width='100%'><tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Links&nbsp;</legend>";
		print "<table>";

		$arr = split(",",$GLOBALS['ShowMainPageLinks']);
		
		if ((sizeof($arr)>0) && (sizeof($arr)<9)) {
			foreach ($arr AS $row) {
				if (trim($row)<>"") {
					print "<tr><td><img src='arrow.gif'>&nbsp;<a target='new' href='" . stripslashes(base64_decode($row)) . "' class='bigsort'>" . htmlentities2(stripslashes(base64_decode($row))) . "</a></td></tr>";
				}
			}
		}

        print "</table></fieldset></td></tr></table>";
    }
	if (strtoupper($GLOBALS['ShowMainPageCalendar'])=="YES") {
        print "<br><table width='100%'><tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Calendar&nbsp;</legend>";
        $MainPageCalendar=1;
        $MonthsToShow=3;
        include("calendar.php");
        print "</fieldset></td></tr></table>";
    }
    //	print "<br>";

	    print "<table width='100%'><tr><td><fieldset style:width=10%><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[administration]&nbsp;</legend>";
	
    internav();
    print "</fieldset></td></tr></table>";
    
} else {
    // End if !foutje
    	FirstBoot();
	    internav();
}
// end if else !foutje

?>

<br><br>

<?

/*
*/
print "<table>";

// Check wheter or not we must now display the language select box
$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='langoverride'";
$result= mcq($sql,$db);
$resarr=mysql_fetch_array($result);

if ($resarr[0]=="no" || $resarr[0]=="No" || $resarr[0]=="NO") {
    
    print "<tr><td>$lang[language]:</td>";
    print "<td><form name='langform' method='POST'>";
    print "<select name='lan' OnChange='javascript:document.langform.submit();'>";
    $sql= "SELECT DISTINCT LANGID FROM $GLOBALS[TBL_PREFIX]languages";
    $result= mcq($sql,$db);
    while ($resarr=mysql_fetch_array($result)) {
        if ((trim($resarr[LANGID])=="") || ($resarr[LANGID] == "GLOBAL")) {
            // GLOBAL is a global language setting which ought to be ignored
        } else {
            if ($language==$resarr[LANGID]) {
                $ins = "SELECTED";
            } else {
                unset($ins);
            }
            print "<option value='$resarr[LANGID]' $ins>$resarr[LANGID]</option>";
        }
    }
    print "</select><input type=hidden name='name' value='$name'></form></td><td>";
} else {
	print "<tr><td>";
}

$epoch = date('U');
ShowRepositorySwitcher("index.php?" . $epoch);

print "</td></tr>";
print "</fieldset></td></tr></table>";

EndHTML();
?>

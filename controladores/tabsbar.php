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
require_once("tabs_inc.php");
print "<br>";

$epoch = date("U");

// The epoch value (# of seconds after 01-01-1970 00h:00m:00s) is used in links to make sure our pages our not cached 
// by Microsoft proxies. Although the META tags should prevent this, lousy software like MS ISA still caches pages.
	
// Initialize tab contents

$tabbs = array();
	
$x = 1;
$tabbs["$x+20"] = array(($back_end_url . "index.php?tab=" . ($x + 20) . "&$epoch") => "<font class='tabsbar'>$x. $lang[main]</font>");
$ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='index.php?tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";

$lock = "<img src='lock.png' style='border:0'>";

//<editor-fold defaultstate="collapsed" desc="Personal Tabs">

if (is_array($GLOBALS['PersonalTabs'])) {
    foreach ($GLOBALS['PersonalTabs'] AS $element) {
	if (is_array($element)) {
				
	    if (strstr($element['url'],"ExternalLink::")) {
		    $httplink = str_replace("ExternalLink::","",$element['url']);
		    $element['url'] = "index.php?if_l=" . base64_encode($httplink);
	    }
	    if (strstr($element['url'],"Template::")) {
		    $httplink = str_replace("Template::","",$element['url']);
		    $element['url'] = "index.php?if_t=" . base64_encode($httplink);
	    }
	    if (strstr($element['visible'],"profile_")) {
		    $profilename = GetUserProfiles(str_replace("profile_","",$element['visible']));
		    $profile = str_replace("profile_","",$element['visible']);

	    } else {
		    $profile = "abcdefg";
	    }


	    if ($element['visible'] == "[all]" || ($element['visible'] == "[admins]" && is_administrator()) || ($element['visible'] == 	$GLOBALS['USERID']) || ($GLOBALS['USERPROFILE']<>"" && ($GLOBALS['USERPROFILE'] == $profile))) {

		//	if ($element['visible'] == "[all]") qlog("Reason : all");
		//	if ($element['visible'] == "[admins]" && is_administrator()) qlog("Reason : admins only");
		//	if ($element['visible'] == 	$GLOBALS['USERID']) qlog("Reason : profile == userid");
		//	if ($GLOBALS['USERPROFILE']<>"" && ($GLOBALS['USERPROFILE'] == $profile)) qlog("Reason : in profile");

		$x++;
		$tabbs["$x+20"] = array(($element['url'] . "&PersonalTabsTSN=x&tab=" . ($x+20)) => "<font class='tabsbar'>$x. " . $element['name'] . "</font>");
		$ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='" . $element['url'] . "&PersonalTabsTSN=x&tab=" . ($x+20) . "&$epoch';\">&nbsp;tabsbar</button>";

	    }
	}
    }
}

//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="Dashboard">
    //customized.php?TwoTable=1&A=1&B=1&C=1&D=1&E=1
    $x++;
    $y = $x + 20;
    $tabbs["$x+20"] = array(($back_end_url . "customized.php?TwoTable=1&A=1&B=1&C=1&D=1&E=1&tab=" . ($x + 20) . "&$epoch") => "$x. Dashboard ");
    $ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='customized.php?TwoTable=1&A=1&B=1&C=1&D=1&E=1&tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";

//</editor-fold>

if (strtoupper($GLOBALS['HIDEADDTAB'])=="YES" && GetClearanceLevel($GLOBALS['USERID'])=="administrator") {
    $x++;
    $y = $x + 20;
    $tabbs["$x+20"] = array(($back_end_url . "edit.php?e=_new_&tab=" . ($x + 20) . "&$epoch") => "$x. $lang[add] $lock");
    $ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='edit.php?e=_new_&tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";
} elseif (strtoupper($GLOBALS['HIDEADDTAB'])<>"YES") {
    $x++;
    $y = $x + 20;
    $tabbs["$x+20"] = array(($back_end_url . "edit.php?e=_new_&tab=" . ($x + 20) . "&$epoch") => "$x. $lang[add] ");
    $ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='edit.php?e=_new_&tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";
}

if (strtoupper($GLOBALS['HIDEENTITYTAB'])=="YES" && GetClearanceLevel($GLOBALS['USERID'])=="administrator") {
    $x++;
    $tabbs["$x+20"] = array(($back_end_url . "index.php?ShowEntitiesOpen=1&tab=" . ($x + 20) . "&$epoch") => "$x. $lang[entities] $lock");
    $ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='index.php?ShowEntitiesOpen=1&tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";
} elseif (strtoupper($GLOBALS['HIDEENTITYTAB'])<>"YES") {
    $x++;
    $tabbs["$x+20"] = array(($back_end_url . "index.php?ShowEntitiesOpen=1&tab=" . ($x + 20) . "&$epoch") => "$x. $lang[entities]");
    $ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='index.php?ShowEntitiesOpen=1&tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";
}

if (strtoupper($GLOBALS['HideCustomerTab'])=="YES" && GetClearanceLevel($GLOBALS[USERID])=="administrator") {
    $x++;
    $tabbs["$x+20"] = array(($back_end_url . "customers.php?tab=" . ($x + 20) . "&$epoch") => "$x. $lang[customers] $lock");
    $ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='customers.php?tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";
} elseif (strtoupper($GLOBALS['HideCustomerTab'])<>"YES") {
    $x++;
    $tabbs["$x+20"] = array(($back_end_url . "customers.php?tab=" . ($x + 20) . "&$epoch") => "$x. $lang[customers]");
    $ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='customers.php?tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";
}

if (strtoupper($GLOBALS['HIDECSVTAB'])=="YES" && GetClearanceLevel($GLOBALS['USERID'])=="administrator") {
    $x++;
    $tabbs["$x+20"] = array(($back_end_url . "csv.php?tab=" . ($x + 20) . "&$epoch") => "$x. CSV $lock");
    $ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='csv.php?tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";
} elseif (strtoupper($GLOBALS['HIDECSVTAB'])<>"YES") {
    $x++;
    $tabbs["$x+20"] = array(($back_end_url . "csv.php?tab=" . ($x + 20) . "&$epoch") => "$x. CSV $admi3");
    $ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='csv.php?tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";
}

if (strtoupper($GLOBALS['HIDEPBTAB'])=="YES" && GetClearanceLevel($GLOBALS['USERID'])=="administrator") {
    $x++;
    $tabbs["$x+20"] = array(($back_end_url . "phonebook.php?tab=" . ($x + 20) . "&$epoch") => "$x. $lang[phonebookshort] $lock");
    $ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='phonebook.php?tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";
} elseif (strtoupper($GLOBALS['HIDEPBTAB'])<>"YES") {
    $x++;
    $tabbs["$x+20"] = array(($back_end_url . "phonebook.php?tab=" . ($x + 20) . "&$epoch") => "$x. $lang[phonebookshort]");
    $ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='phonebook.php?tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";
}
	
if (strtoupper($GLOBALS['HIDESUMMARYTAB'])=="YES" && GetClearanceLevel($GLOBALS['USERID'])=="administrator") {
    $x++;
    $tabbs["$x+20"] = array(($back_end_url . "summary.php?tab=" . ($x + 20) . "&$epoch") => "$x. $lang[summary] $lock");
    $ack .= "<button accesskey='" . $x ."' 	onclick=\"javascript:location.href='summary.php?tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";
} elseif (strtoupper($GLOBALS['HIDESUMMARYTAB'])<>"YES") {
    $x++;
    $tabbs["$x+20"] = array(($back_end_url . "summary.php?tab=" . ($x + 20) . "&$epoch") => "$x. $lang[summary]");
    $ack .= "<button accesskey='" . $x ."' 	onclick=\"javascript:location.href='summary.php?tab=" . ($x + 20) . "&$epoch';\">&nbsp;tabsbar</button>";
}
	
if (strtoupper($EnableCustInsert)=="YES") {	
    $x++;
    $sql= "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity,$GLOBALS[TBL_PREFIX]customer WHERE $GLOBALS[TBL_PREFIX]customer.id=$GLOBALS[TBL_PREFIX]entity.CRMcustomer AND $GLOBALS[TBL_PREFIX]entity.owner='2147483647' AND $GLOBALS[TBL_PREFIX]entity.assignee='2147483647' AND deleted<>'y'";
    $result= mcq($sql,$db);
    $e= mysql_fetch_array($result);

    $tabbs["$x+20"] = array(($back_end_url . "index.php?ShowEntitiesOpen=1&filter=custinsert&tab=" . ($x + 20)) => "$x. $lang[viewinsertedentities] ($e[0])");

    $ack .= "<button accesskey='" . $x . "' onclick=\"javascript:location.href='index.php?ShowEntitiesOpen=1&filter=custinsert&tab=" . ($x + 20) . "';\">&nbsp;tabsbar</button>";
}
		
if (strtoupper($ShowDeletedViewOption)=="YES") {
    $x++;
    $tabbs["$x+20"] = array(($back_end_url . "index.php?ShowEntitiesOpen=1&filter=viewdel&tab=" . ($x + 20)) => "$x. $lang[delentities]");

    $ack .= "<button accesskey='" . $x . "' 	onclick=\"javascript:location.href='index.php?ShowEntitiesOpen=1&filter=viewdel&tab=" . ($x + 20) . "';\">&nbsp;tabsbar</button>";
}

	
$req_url = base64_encode($_SERVER['REQUEST_URI']);
require_once("config.inc.php");
/*
print "<pre>";
print_r($pass);
print_r($host);
print_r($user);
print_r($database);
print_r($table_prefix);
*/

//<editor-fold defaultstate="collapsed" desc="Repository switcher">

if (strtoupper($EnableRepositorySwitcher)=="YES" || (strtoupper($EnableRepositorySwitcher)=="ADMIN" && is_administrator())) {
				
    $html = "<table width=\'100%\' cellspacing=\'0\' cellpadding=\'3\' border=\'0\'><tr><td><img src=\'reposwhite.jpg\'>&nbsp;<b>Repository</b></td></tr>";
    //$html .= "<tr><td bgcolor=\'#CCCCCC\'><b>Repository</b></td></tr>";

    $sql = "SELECT password FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='" . $GLOBALS['USERNAME'] . "'";
    $result= mcq($sql,$db);
    $result1= mysql_fetch_array($result);
    $curpassword = $result1[password];

    $county = 0;

    if (sizeof($pass)>0) {
	for ($r=0;$r<64;$r++) {
	    if ($host[$r]) {
		if ($db = @mysql_connect($host[$r], $user[$r], $pass[$r])) {
		    if (@mysql_select_db($database[$r],$db)) {
			$tbl = $table_prefix[$r];
			if ($tbl=="") $tbl="CRM";
			$sql = "SELECT password FROM " . $tbl . "loginusers WHERE name='" . $GLOBALS['USERNAME'] . "'";

			$result = @mysql_query($sql);
			$result1= @mysql_fetch_array($result);
			$foreignpassword = $result1[password];

			if ($curpassword==$foreignpassword && $GLOBALS['USERNAME']) {

			    //$id[0] = GetUserID($GLOBALS['USERNAME']);
			    $sql = "SELECT id FROM " . $tbl . "loginusers WHERE name='" . $GLOBALS['USERNAME'] . "'";
			    $result= mcq($sql,$db);
			    $id= mysql_fetch_array($result);


			    $sql = "SELECT value FROM " . $tbl . "settings WHERE setting='title'";
			    $result = mcq($sql,$db);
			    $result = mysql_fetch_array($result);
			    $outp .= "<tr><td><img src=\'arrow.gif\'> &nbsp;<a ";

			    $outp .= "href=\'index.php?swrepos=1&switchreposto=a-" . $r . "&req_url=" . $req_url . "\'";


			    $outp .= "class=\'bigsort\' href=\'a-" . $r . "\'>" . $result['value'] . "</a></td></tr>";
			    $county++;
			} else {
			    //print "<option>denied</option>";
			}
		    } else {
			//print "<option>no select</option>";
		    }

		} else {
		     //print "<option>host not found</option>";
		}
	    }
	}
				
	$outp .= "</table>";

	$db = @mysql_connect($GLOBALS['DBHOST'], $GLOBALS['DBUSER'], $GLOBALS['DBPASS']);
	@mysql_select_db($GLOBALS['DBNAME'],$db);
    } else {
	print "foutje";
    }

				
    if ($county>1) {
	$html .= $outp;
    } else {
	$dontprint = true;
    }

    $evt_ins = "<a onmouseover=\"javascript:style.background='#E9E9E9';return overlib('" . $html . "', STICKY)\" onmouseout=\"javascript:style.background='#FFFFFF';nd();\" OnBlur=\"javascript:style.background='#FFFFFF';nd();\" CLASS='bigsort' style='cursor:pointer'><img src='repos.jpg'>";

    if ($dontprint) {
	unset($evt_ins);
    }

    unset($html);
    unset($outp);
    //$evt_ins = $html;
}

//</editor-fold>

$x++;	
$tabbs["$x+20"] = array(($back_end_url . "index.php?logout=1&$epoch") => "0. $lang[logout]");
$ack .= "<button accesskey='0' 	onclick=\"javascript:location.href='index.php?logout=1&$epoch';\">&nbsp;tabsbar</button>";

$x++;
$tabbs["$x+20"] = array(($back_end_url . "") => "</a>&nbsp;#:<form name='direct' action='edit.php?tab=$x&$epoch' method=POST><input type='text' size=3 name='e' OnChange='javascript:document.direct.submit()'  OnFocus=\"javascript:document.direct.e.value=''\"></form>&nbsp;<form name='direct2' action='summary.php?tab=$x&$epoch' method='GET'><input class='searchx_tabbar' type='text' name='wildsearch'  OnChange='javascript:document.direct2.submit()' OnFocus=\"javascript:document.direct2.wildsearch.value=''\"> <input type='hidden' name ='owner' value='all'><input type='hidden' name ='assignee' value='all'><input type='hidden' name ='$GLOBALS[TBL_PREFIX]customer' value='all'><input type='hidden' name='duedate' value='all'><input type='hidden' name ='status'><input type='hidden' name ='prio'><input type='hidden' name ='waiting'></form>" . $evt_ins . "");
$x++;
$tabbs["$x+20"] = array(($back_end_url . "") => "</a><a href='http://www.crm-ctt.com' target='new'	><img src='crm_small_grey.gif' style='border:0'></a>");

$ack .= "<button accesskey='a' 	onclick=\"javascript:location.href='admin.php?info=1&$epoch';\">&nbsp;tabsbar</button>";
$ack .= "<button accesskey='s' 	onclick=\"javascript:location.href='admin.php?tab=99&sysval=1&$epoch';\">&nbsp;tabsbar</button>";

$ack .= "<button accesskey='h' 	onclick=\"javascript:location.href='index.php?shortkeys=1&$epoch';\">&nbsp;tabsbar</button>";
$ack .= "<button accesskey='d' 	onclick=\"javascript:location.href='checkdb.php?$epoch';\">&nbsp;tabsbar</button>";
$ack .= "<button accesskey='c' 	onclick=\"javascript:location.href='calendar.php?nav=1&$epoch';\">&nbsp;tabsbar</button>";

$ack .= "<button accesskey='p' 	onclick=\"javascript:location.href='admin.php?fysdelete=1&$epoch';\">&nbsp;tabsbar</button>";

$ack .= "<button accesskey='l' 	onclick=\"javascript:location.href='dictedit.php?tab=99&packman=1&$epoch';\">&nbsp;tabsbar</button>";
$ack .= "<button accesskey='u' 	onclick=\"javascript:location.href='admin.php?password=&adduser=1&userman=1&cur=1&$epoch';\">&nbsp;tabsbar</button>";
$ack .= "<button accesskey='m' 	onclick=\"javascript:newWindow = window.open('CRM-CTT_Adminmanual.pdf','Manual','width=640,height=630,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');\">&nbsp;tabsbar</button>";
$ack .= "<button accesskey='p' 	onclick=\"javascript:location.href='admin.php?fysdelete=1&$epoch';\">&nbsp;tabsbar</button>";
$ack .= "<button accesskey='e' Onclick=\"javascript:location.href='extrafields.php?tabletype=entity&ti=1';\">&nbsp;tabsbar</button>";
$ack .= "<button accesskey='r' 	onclick=\"javascript:location.href='admin.php?reposman=1&resman=1&manageres=1&$epoch';\">&nbsp;tabsbar</button>";
$ack .= "<button accesskey='f' 	onclick=\"javascript:location.href='index.php?AccSetPointGerbil_EEGS=1';\">&nbsp;tabsbar</button>";
$ack .= "<button accesskey='t' 	onclick=\"javascript:location.href='trigger.php';\">&nbsp;tabsbar</button>";
$ack .= "<button accesskey='n' 	onclick=\"javascript:location.href='customers.php?add=1.php';\">&nbsp;newcust</button>";
print '<div id="hidden" style="width: 1; height: 1; position: absolute; top: -200; left: -200; overflow: hidden">';
//print '<iframe src="empty.html" name="uRail" id="uRail"></iframe>';
print $ack;
print '</div>';
// So far for the normal menu items

$to_tabs = array();

for ($t=1;$t<$x+1;$t++) {
    array_push($to_tabs,"$t+20");
}

//print_r($to_tabs);

$tab .= " ";
$tab = trim($tab);

tabs($to_tabs, $tabbs, $tab);

//print "<button accesskey=\"h\" onclick=\"javascript:location.href='index.php';\">&nbsp;tabsbar</button>";

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
if (!$MainPageCalendar && !$weekdetail && !$nav) {
	include("config.inc.php");
	include("getset.php");
} elseif ((!$MainPageCalendar && $weekdetail) || $nav) {
	include("header.inc.php");
}

if ($_REQUEST['weekdetail'] == "this") {

	$y= date("Y");
	$year = $y;
	$weekdetail = strtotime ("last monday");
	$_REQUEST['weekdetail'] = strtotime ("last monday");
	$y= date("Y"); 
	
	$year = $y; 
	$d= date("D"); 
	if ($d == "Mon") 
	{$weekdetail = strtotime ("this monday"); 
	$_REQUEST['weekdetail'] = strtotime ("this monday"); 
	} 
	else 
	{$weekdetail = strtotime ("last monday"); 
	$_REQUEST['weekdetail'] = strtotime ("last monday"); 
	} 
	$bla=1; 

}

$is_admin = is_administrator();


if (!$strmonth) {
	$strmonth = date("m");
}
if (!$stryear) {
	$stryear= date("Y");
}
if ($_REQUEST['weekdetail']) {

	
		
		DisplayCSS();

		ShowSelectWeek($_REQUEST['weekdetail'],$_REQUEST['year'],$_REQUEST['select']);

	} else {
	//print "Jaar: $stryear -- Maand: $strmonth -- toshow: $MonthsToShow pred: $pred<br>";

	// Calculate numer of months to show - $MonthsToShow config value

	// To do so, we need the # of seconds in the # of given months
	// 2592000 = avg. # seconds in one month

	// First, capture any missing params:

	if (!$MonthsToShow) {
		log_msg("WARNING: Configuration fault - MTS Adjusted to 6MTS (non-fatal)","");
		$MonthsToShow = 6;
	}
	global $pred;

	if (!$pred) {
		$Seconds = (($MonthsToShow-1) * 2509280);
		$Seconds += 12960000;
		$pred=time()+$Seconds;
	}


	$prevmonth = $strmonth-3;
	if ($prevmonth<1) {
		$prevmonth = 12;
		$prevyear = $stryear-1;
	} else {
		$prevyear = $stryear;
	}
	$nextmonth = $strmonth+1;
	$nextpred = $pred + 12960000;
	if ($nextmonth>12) {
		$nextmonth = 1;
		$nextyear = $stryear+1;
	} else {
		$nextyear = $stryear;
	}


	if (!$MainPageCalendar) {
		print "<html><title>" . $GLOBALS['title'] . "</title>";
		print "<head>";
		DisplayCSS();
		print "</head>";


	}
	
	
	if ($_REQUEST['this'] && $_REQUEST['Cust']) {
		print "<body onunload='window.opener." . $_REQUEST['this'] . ".blur();'>";
		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
		function applydate(e1)
			{           
					window.opener.<? echo $_REQUEST['this']; ?>.value = e1;
					window.opener.<? echo $_REQUEST['this']; ?>.blur();
					window.opener.document.editform.submitknop.focus();
					window.close();
			}
		
		//-->
		</SCRIPT>
		<?
	} elseif ($_REQUEST['this']) {
		print "<body onunload='window.opener." . $_REQUEST['this'] . ".blur();'>";
		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
		function applydate(e1)
			{
					window.opener.<? echo $_REQUEST['this']; ?>.value = e1;
					window.opener.<? echo $_REQUEST['this']; ?>.blur();
					window.opener.<? echo $_REQUEST['this']; ?>HF.value = AdjustDate(e1);
					//window.opener.document.EditEntity.content.focus();
					window.close();
			}
		
		//-->
		</SCRIPT>
		<?
	} elseif (!$MainPageCalendar) {
		print "<body onunload=\"window.opener.document.EditEntity.displayr.blur();\">";
		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
		function applydate(e1)
			{
					
					window.opener.document.EditEntity.reminderdate.value = e1;
					window.opener.document.EditEntity.displayr.value = AdjustDate(e1);
					window.opener.document.EditEntity.displayr.blur();
                                                                                                    window.opener.document.EditEntity.displayr.value = AdjustDate(e1);
					window.opener.document.EditEntity.reminderdate.blur();
					window.close();
					
			}
		
		//-->
		</SCRIPT>
		<?
	}

//function prCalendar($fromyear,$frommonth,$fromday,$href,$username,$session) {

	if (!$MainPageCalendar) {

		//print "<table><tr><td class='smallsort'>Click on a date to set the due date for this entity. Click <a OnClick='javascript:window.close();' class='smallsort' style='cursor:pointer'>here</a> to cancel.</td></tr></table><br>";
		print "<table width='100%'><tr><td><a class='bigsort' href='calendar2.php?strmonth=" . $prevmonth . "&stryear=" . $prevyear . "&this=" . $this . "'>prev</a></td><td align='right'><a class='bigsort' href='calendar2.php?strmonth=" . $nextmonth . "&stryear=" . $nextyear . "&pred=" . $nextpred . "&this=" . $this . "'>next</a></td></tr></table>";

	}

		prCalendar($stryear,$strmonth,"1","bla","","");

	if (!$MainPageCalendar) {
		print "<table width='100%'><tr><td><a class='bigsort' href='calendar2.php?strmonth=" . $prevmonth . "&stryear=" . $prevyear . "&this=" . $this . "'>prev</a></td><td align='right'><a class='bigsort' href='calendar2.php?strmonth=" . $nextmonth . "&stryear=" . $nextyear . "&pred=" . $nextpred . "&this=" . $this . "'>next</a></td></tr></table>";
	}
	?>
	<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function AdjustDate(date) {
				
				if ('<? echo $DateFormat;?>'=='mm-dd-yyyy') { 
					day = date.substring(0,2);
					mon = date.substring(3,5);
					yer = date.substring(6,10);

					NewDate = mon + "-" + day + "-" + yer;
                           
				} else {
					NewDate = date;
                                                                                                               }
				
				return(NewDate);
		
			}
			
			//-->
			</SCRIPT>
	<?


	// 				window.opener.document.EditEntity.duedate.value = document.townselect.town.value;
}
function makelinks2($input)
{
	// first get http:// and etc
	$input = eregi_replace("[^\"](http://[[:alnum:]#?/&=.,-~]*)",
	" <a href=\"\\1\" target=\"_new\">\\1</a>",
	$input);
	// and at the beginning of a line
	$input = eregi_replace("(^[a-z]*://[[:alnum:]#?/&=.,-~]*)",
	" <a href=\"\\1\" target=\"_new\">\\1</a>",
	$input);
	// then get the email@hosts
	$input = eregi_replace("(([a-z0-9_]<br>\\-<br>\\.)+@([^[:space:]]*)([[:alnum:]-]))",
	"<a href=\"mailto:\\1\">\\1</a>", $input);
	return($input);
}

$functions_included = yes;

function is_leap_year($year) {
	if ((($year % 4) == 0 and ($year % 100)!=0) or ($year % 400)==0) {
		return 1;
	} else {
		return 0;
	}
}

function iso_week_days($yday, $wday) {
	return $yday - (($yday - $wday + 382) % 7) + 3;
}

function get_week_number($timestamp) {
	$d = getdate($timestamp);
	$days = iso_week_days($d["yday"], $d["wday"]);
	if ($days < 0){
		$d["yday"] += 365 + is_leap_year(--$d["year"]);
		$days = iso_week_days($d["yday"], $d["wday"]);
	}
	else {
		$d["yday"] -= 365 + is_leap_year($d["year"]);
		$d2 = iso_week_days($d["yday"], $d["wday"]);
		if (0 <= $d2) {
			/* $d["year"]++; */
			$days = $d2;
		}
	}
	return (int)($days / 7) + 1;
}


function prCalendar($fromyear,$frommonth,$fromday,$href,$username,$session) {
	global $pred, $MainPageCalendar;
	(int)$fromyear;
	(int)$frommonth;
	(int)$fromday;
	global $maincolumn,$maxcolumn,$bdarray,$bdresult;
	$maincolumn=1;
	$maxcolumn=3;

	echo "<table border=0 cellpadding=0 cellspacing=3 width='100%'>";


	//daynumber of the year-
	$yearday= date (z);
	$curyear=(int)date("Y",$pred);
	$curmonth=(int)date("m",$pred);
	$curday=(int)date("d",$pred);

	do{
		//$bdarray= mysql_fetch_array($bdresult);
	} while ($bdarray[dofy]<=$yearday && $bdarray[dofy]!="");

	if ($curyear==$fromyear){
		if ($curmonth==$frommonth){
			prMonth($curyear,$curmonth,$href,$fromday,$curday);
		}
		else {
			prMonth($fromyear,$frommonth,$href,$fromday,32);
			for ($m=$frommonth+1;$m<$curmonth;$m++){
				prMonth($fromyear,$m,$href,0,32);
			}
			prMonth($curyear,$curmonth,$href,0,$curday);
		}
	}
	else {
		prMonth($fromyear,$frommonth,$href,$fromday,32);
		if ($frommonth != 12 ){
			for ($m=$frommonth+1;$m<=12;$m++){
				prMonth($fromyear,$m,$href,0,32);
			}
			for ($y=$fromyear+1;$y<$curyear;$y++){
				for ($m=1;$m<=12;$m++){
					prMonth($y,$m,$href,0,32);
				}
			}
			for ($m=1;$m<$curmonth;$m++){
				prMonth($curyear,$m,$href,0,32);
			}
		}
    else{
			for ($y=$fromyear+1;$y<$curyear;$y++){
				for ($m=1;$m<=12;$m++){
					prMonth($y,$m,$href,0,32);
				}
			}
			for ($m=1;$m<$curmonth;$m++){
				prMonth($curyear,$m,$href,0,32);
			}
    }
		prMonth($curyear,$curmonth,$href,0,$curday);
	}
	?>
	</table>
	<?
}


function prMonth($year,$month,$href,$fd,$cd){
	global $MainPageCalendar,$lang,$monthcounter,$MonthsToShow,$select,$NoClickToWeek,$maincolumn,$maxcolumn,$bdarray,$bdresult, $color1, $color2, $nav;
	$monthcounter++;
	if ($monthcounter<($MonthsToShow+1)) {
		$todayd= date(d);
		$todaym= date(m);
		$todayy= date(Y);
		if ($maincolumn == 1 ){
			print "<tr valign=\"top\">\n";
		}
		$first=@mktime(0,0,0,$month,1,$year);
		//print cel waar maandnaam in staat
		?>
		<td class='smallsort'><table border=0 cellpadding=1 cellspacing=1><tr><td colspan="9" align="center">
		<?
		print (@date("F Y",$first));
		echo "<tr align=right><td class='smallsort'>Week</td>\n<td class='smallsort'><b>Mo</b></td><td class='smallsort'><b>Tu</b></td><td class='smallsort'><b>We</b></td><td class='smallsort'><b>Th</b></td><td class='smallsort'><b>Fr</b></td><td class='smallsort'><b>Sa</b></td><td class='smallsort'><b>Su</b></td>";
		$wd=@date("w",$first);
		if ($wd==0){
			$wd=7;
		}
		$wdtimeout=date("w");
		if ($wd==0){
			$wd=7;
		}
		$lastday=@date("d",@mktime(0,0,0,$month+1,0,$year));
		$cur=-$wd+1;
		$timeoutdone=0;
		for ($k=1;$k<7;$k++){
			$weekcur= $cur+1;
			$oldtimestamp= @mktime(0,0,0,$month,$weekcur,$year)-604800;
			$oldweek= (get_week_number($oldtimestamp));
			$newtimestamp= time();
			$newweek= (get_week_number($newtimestamp));
			$curmonth= date (m);
			$curhour= date (H);
			if ($cur<=0){
				$timestamp= @mktime(0,0,0,$month,1,$year);
				$weeknum= (get_week_number($timestamp));
				if (!$MainPageCalendar && !$nav) {
						$nonavins = "&nonavbar=1";
				} else {
						$nonavins = "&bla=1";
				}
//				echo "<tr align=right>\n<td class='smallsort' align='center'><b>$weeknum</b>\n";
				if (!$NoClickToWeek) {
					echo "<tr align=right>\n<td class='smallsort' align='center'><b><a href='calendar2.php?weekdetail=$timestamp&year=$year$nonavins&select=$select&nav=$nav' class='bigsort' alt='Go to weekview'>$weeknum</a></b>\n";
				} else {
					echo "<tr align=right>\n<td class='smallsort' align='center'><b>$weeknum</b>\n";
				}

			}
			elseif ($weekcur>$lastday){
				echo "<tr align=right>\n<td class='smallsort'>\n";
			}
			else {
				if (!$MainPageCalendar && !$nav) {
						$nonavins = "&nonavbar=1";
				} else {
						$nonavins = "&bla=1";
				}
				$timestamp= @mktime(0,0,0,$month,$weekcur,$year);
				$weeknum= (get_week_number($timestamp));
//				echo "<tr align=right>\n<td class='smallsort' align='center'><b>$weeknum</b>";
			
				if (!$NoClickToWeek) {
					echo "<tr align=right>\n<td class='smallsort' align='center'><b><a href='calendar2.php?weekdetail=$timestamp&year=$year$nonavins&select=$select' class='bigsort' alt='Go to weekview'>$weeknum</a></b>\n";
				} else {
					echo "<tr align=right>\n<td class='smallsort' align='center'><b>$weeknum</b>\n";
				}

			}

			for ($i=0;$i<7;$i++ ){
				$cur++;
				if (($cur<=0) || ($cur>$lastday)){
					//print dit voor de opvulling van de tabel, zodat de datum onder de goede dag staat
					// dagen van voorgaande en nakomende maanden
					print "<td class='smallsort'>\n";
				} elseif ($cur==$todayd && $month==$todaym && $year==$todayy){
					// vandaag
					$timestamp= @mktime(0,0,0,$month,$cur,$year);
					$maandnaam= (date("F",$timestamp));
						$cur1 = $cur;
						if (strlen($cur1)==1) $cur1 = "0" . $cur1;
						if ($MainPageCalendar) {
						//$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE duedate='$cur1-$month-$year' AND deleted<>'y'";
                            //Aggiunto da Daniele Lembo
                            $sql = "SELECT *, $GLOBALS[TBL_PREFIX]loginusers.fullname FROM $GLOBALS[TBL_PREFIX]entity, $GLOBALS[TBL_PREFIX]loginusers WHERE  $GLOBALS[TBL_PREFIX]entity.assignee =  $GLOBALS[TBL_PREFIX]loginusers.id AND reminderdate'$cur1-$month-$year'";
							//qlog($sql);
							$result= mcq($sql,$db);
							$ins = "<FONT COLOR='#000000'>";
							  while ($today= mysql_fetch_array($result)) {

									if ($today['category']<>"" && (CheckEntityAccess($today['eid']) == "ok" || CheckEntityAccess($today['eid']) == "readonly")) {
										$html .= "<a href=\'edit.php?e=" . $today['eid'] . "\' class=\'smallsort\'>";
										
										$html.= $lang[customer] . ": " . htmlentities(GetCustomerName($today['CRMcustomer']), ENT_QUOTES) . "<br>" . htmlentities($lang['status'], ENT_QUOTES) . ": " . $today['status'] . "<br>";
                                        //Aggiunta da Daniele Lembo
                                        $html.= $lang['assignee'] . ": " . htmlentities($today['FULLNAME'], ENT_QUOTES) . "<br>";
										$html.= $lang['category'] . ": " . htmlentities($today['category'], ENT_QUOTES) . "</a><br>";
										$html.= "-------------------------------<br>";
										$ins = "<FONT COLOR='#FF0000'>";
										$ins2= "onmouseover=\"javascript:return overlib('" . $html . "', STICKY)\" onmouseout=\"javascript:nd();\" style='cursor:pointer'";
									}
							  }

							  
							  print "<td bgcolor=#AAAAAA><a $ins2 href='summary.php?owner=all&assignee=all&$GLOBALS[TBL_PREFIX]customer=all&reminderdate=$cur-$month-$year&csv=onscreenfull&extended=1' class='smallsort' >$ins$cur</font></a></td>\n";
							  unset($html);
							  unset($ins);
							  unset($ins2);
						} else {
							if (strlen($cur)==1) $cur = "0" . $cur;
							if ((strlen($month)==1) && $month<10) {
								$month = "0" . $month;
							}
							print "<td bgcolor=#AAAAAA><a href=javascript:applydate('$cur-$month-$year')>$cur </a></td>\n";
						}
				
				} else {
					//Dit wordt geprint als er geen link achter zit. (niet vandaag)

					$timestamp= @mktime(0,0,0,$month,$cur,$year);
					$maandnaam= (date("F",$timestamp));
					$todaynumberofyear= date("z",$timestamp);
					if ($bdarray[1]==$month && $bdarray[2]==$cur){
						//echo "<td bgcolor=\"#FF8746\"><a href=javascript:applydate($timestamp) onMouseOver=\"Statusbalk('$cur $maandnaam wordt";
						$doen=0;
						if (strlen($cur1)==1) $cur1 = "0" . $cur1;
						//echo " jaar');return document.returnValue\">$cur</a></td>\n";

					} else {

						$cur1 = $cur;
						if (strlen($cur1)==1) $cur1 = "0" . $cur1;
						if (strlen($month)==1) $month = "0" . $month;

						if ($MainPageCalendar) {
							//$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE duedate='$cur1-$month-$year' AND deleted<>'y'";
                            //Aggiunto da Daniele Lembo
                            $sql = "SELECT $GLOBALS[TBL_PREFIX]entity.*, $GLOBALS[TBL_PREFIX]loginusers.fullname FROM $GLOBALS[TBL_PREFIX]entity,  $GLOBALS[TBL_PREFIX]loginusers WHERE  $GLOBALS[TBL_PREFIX]entity.assignee =  $GLOBALS[TBL_PREFIX]loginusers.id  AND  reminderdate='$cur1-$month-$year'";
							$result= mcq($sql,$db);
				
							$ins = "<FONT COLOR='#000000'>";
							//print "<td>$sql</td>";
							
							  while ($today= mysql_fetch_array($result)) {
									if ($today['category']<>"" && (CheckEntityAccess($today['eid']) == "ok" || CheckEntityAccess($today['eid']) == "readonly")) {
										$html .= "<a href=\'edit.php?e=" . $today[eid] . "\' class=\'smallsort\'>";
										$html.= $lang['customer'] . ": " . htmlentities(GetCustomerName($today['CRMcustomer']), ENT_QUOTES) . "<br>" . $lang['status'] . ": " . $today['status'] . "<br>";
                                        //Aggiunta da Daniele Lembo
                                        $html.= $lang['assignee'] . ": " . htmlentities($today['fullname'], ENT_QUOTES) . "<br>";
										$html.= $lang['category'] . ": " . htmlentities($today['category'], ENT_QUOTES) . "</a><br>";
										$html.= "-------------------------------<br>";
										$ins = "<FONT COLOR='#FF0000'>";
										$ins2= "onmouseover=\"javascript:return overlib('" . $html . "', STICKY)\" onmouseout=\"javascript:nd();\" style='cursor:pointer'";
									}
							  }
						
							print "<td class='smallsort'><a $ins2 href='summary.php?owner=all&assignee=all&$GLOBALS[TBL_PREFIX]customer=all&reminderdate=$cur-$month-$year&csv=onscreenfull&extended=1' class='smallsort' >$ins$cur</font></a></td>\n";
							// Bovenstaande: als geen entity gevonden!
							unset($html);
							unset($ins);
							unset($ins2);
						} else {

							if (strlen($cur)==1) $cur = "0" . $cur;
							if ((strlen($month)==1) && $month<10) {
								$month = "0" . $month;
							}
							printf ("<td class='smallsort'><a class='smallsort' href=javascript:applydate('$cur-$month-$year')>$cur</a></td>\n",$timestamp,$cur);
						}
					}
				}
			}
		}
		print "</table>\n\n";
			?>
			<form name='bogus-focus'>
				<input type='hidden' name='bogus'>
				</form>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
			window.focus();
			//-->
			</SCRIPT>
				<?
		if ($maincolumn == $maxcolumn){
			$maincolumn=1;
			echo "<tr><td colspan=\"$maxcolumn\" bgcolor=FFFFFF>\n";
		}
		else{
			$maincolumn++;
		}
	} else {// end if monthcounter < monthstoshow 
//		print "fout";
	}
}

function ShowSelectWeek($weeknum,$year,$select) {
	global $lang,$nonavbar,$MainPageCalendar;
	// weeknum contains timestamp

	$nextweek = $weeknum + 604800;
	$prevweek = $weeknum - 604800;
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
				function applydate(e1,t1)
			{
					window.opener.document.EditEntity.content.focus();
					window.opener.document.EditEntity.reminderdate.value = e1;
					//window.opener.document.EditEntity.duetime.value = t1;
					window.opener.document.EditEntity.content.focus();
					window.opener.document.EditEntity.displayr.value = AdjustDate(e1);
					window.opener.document.EditEntity.content.focus();
					window.opener.document.EditEntity.displayr.blur();
					window.opener.document.EditEntity.reminderdate.blur();
					window.opener.document.EditEntity.content.focus();
					window.close();
					window.opener.document.EditEntity.content.focus();
					window.opener.AlertUser('IsChanged');
			}
			function AdjustDate(date) {
				
				if ('dd-mm-yyyy'=='mm-dd-yyyy') { 
					day = date.substring(0,2);
					mon = date.substring(3,5);
					yer = date.substring(6,10);

					NewDate = mon + "-" + day + "-" + yer;
                                     
				} else {
					NewDate = date;
                                     
				}
				
				return(NewDate);
		
			}
	//-->
	</SCRIPT>
	<?
	
	print "<table><tr><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Calendar&nbsp;</legend><table>";
	print "<tr><td><a class='bigsort' href='calendar2.php?weekdetail=" . $prevweek . "&year=" . $year. "&nonavbar=" . $nonavbar . "'><img src='larrow.gif' style='border:0'></a>&nbsp;&nbsp;<a class='bigsort' href='calendar2.php?weekdetail=" . $nextweek . "&year=" . $year. "&nonavbar=" . $nonavbar . "'><img src='rarrow.gif' style='border:0'></a></td></tr>";
	print "<tr><td>&nbsp;</td><td>";
	
	if (strtoupper($GLOBALS['CAL_USEWEEKEND'])=="NO") {
		$NUMDAYS = 5;
	} else {
		$NUMDAYS = 7;
	}

	print "<table border='1' width='100%' bgcolor='#F2F2F2' cellspacing='0' cellpadding='4' bordercolor='#000000'>";


		
	print "<tr><td>&nbsp;</td>";
	
	$tmp = $weeknum;

	for ($i=0;$i<$NUMDAYS;$i++) {

		$a = date('l,d M Y',$tmp);
		$tmp += 86400;
		$today = date('l,d M Y');
		$basictoday = date('dMY');
		if ($a == $today) {
			$a = "<font color = '#FF0000'>" . $a . "</font>";
		} 
		

		print "<td><b>" . $a . "</b></td>";
	}
	
	print "</tr>";
	print "<tr><td>&nbsp;</td>";
	
	$tmpr = $weeknum;

	for ($i=0;$i<$NUMDAYS;$i++) {
		

		$today = date('l,d M Y');
		$basictoday = date('dmY',$tmpr);
		$tmpr += 86400;
		$sql = "SELECT eID FROM $GLOBALS[TBL_PREFIX]calendar WHERE basicdate='" . $basictoday . "' ORDER BY Tijdpostzegel LIMIT 5";


		$result = mcq($sql,$db);
		while ($row = mysql_fetch_array($result)) {
				if (CheckEntityAccess($row['eID']) == "ok" || CheckEntityAccess($row['eID']) == "readonly") {
					$b .= "&nbsp;<img src='info.gif'>&nbsp; $num " . $lang['alarmdate'] . " " . strtolower($lang['entity']) . " " . $row['eID'] . "<BR>";
				}
		} 

		if (!$b) { $b = "&nbsp;"; }

		print "<td>" . $b . "</td>";
		unset($b);
	}
	
	print "</tr>";



		$sqla=array();	

	for ($i=$GLOBALS['CAL_MINHOUR'];$i<$GLOBALS['CAL_MAXHOUR']+1;$i+=.5) {
		if ($i<10) { 
			$val = "0";
			$txt = "0";
		}
		if ($ch) {
			$val .= floor($i) . "30";
			$txt .= floor($i) . ":30 h";
			$s30 = 1;
			unset($ch);
		} else {
			$val .= floor($i) . "00";
			$txt .= floor($i) . ":00 h";
			$ch=1;
		}
	
		if (!$s30) {
			if (substr($txt,0,2) == date('G')) {
				print "<tr OnMouseover=\"javascript:style.background='#E9E9E9';\" OnMouseout=\"javascript:style.background='#FFFFFF';\"><td valign='top' NOWRAP><b><font color='#FF0000'>"  . $txt . "</font></b></td>";
			} else {
				print "<tr OnMouseover=\"javascript:style.background='#E9E9E9';\" OnMouseout=\"javascript:style.background='#FFFFFF';\"><td valign='top' NOWRAP><b>"  . $txt . "</b></td>";
			}
		} else {
			unset($s30);
		}
		$tmp = $weeknum;

		$today = date('d-m-Y');

		for ($c=0;$c<$NUMDAYS;$c++) {
			$p=0;
			unset($prt);

			$a = date('l,d M Y',$tmp);
			$dd = date('d-m-Y',$tmp);
			$tmp += 86400;

			// duedate format is DD-MM-YYYYY
			
	
			$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE LEFT(duetime,2)='" . substr($val,0,2) . "' AND reminderdate='" . $dd . "'";

			
			if (!in_array($sql,$sqla)) {

				array_push($sqla,$sql);
				
				unset($ins);
				
				$bgc = "#D4D4D4";
					
				if ((substr($val,0,2) == date('G')) && ($dd == $today)) {
					$ovwr = 1;
				} else {
					unset($ovwr);
				}

				$result = mcq($sql,$db);
				while ($row = mysql_fetch_array($result)) {
					if ($p<6) {
						if (CheckEntityAccess($row['eid']) == "ok" || CheckEntityAccess($row['eid']) == "readonly") {
							if ($prt) {
								$ins .= "<hr>";
							}

							if (strlen($cat)>25) {
								$cat = substr($cat,0,22) . "...";
							}
							$cat = str_replace(" ","&nbsp;",$row['category']);

							$ins .= "<a class='smallsort' style='background: " . GetStatusColor($row['status']) . "'><font color='#000000'>&nbsp;" . str_replace(" ","&nbsp;",$row['status']) . "&nbsp;</font></a>:<a href=\"javascript:popupdater(" . $row['eid'] .");\" class='bigsort'>" . $cat ."</a><br>" . str_replace(" ","&nbsp;",GetCustomerName($row['CRMcustomer'])) . "";
							$prt=1;
							$p++;
						}
					}
					
				}
				if ($p==6) {
					$ins .= "<br><a href='summary.php?owner=all&assignee=all&$GLOBALS[TBL_PREFIX]customer=all&reminderdate=$dd&csv=onscreenfull&extended=1' class='smallsort'>&lt;more&gt;</a>";
				}

				if (!$prt==1) {
						if ($ovwr) {
							print "<td bgcolor='$bgc' NOWRAP valign='top'>";
							print "<table border='1' cellpadding=2 cellspacing=2 bordercolor='#FF0000' width='100%' height='100%'><tr><td bgcolor='#ffffff' style='border:0'>";
							if ($select) {
								print "<a href=\"javascript:applydate('$dd','$val');\"><img style='border:0' src='timedate.gif' title='Select'></a><br>";
							} else {
								if (!$GLOBALS['HIDEADDTAB'] || $is_admin) {
									print "<a href='edit.php?e=_new_&SetDateTo=" . $dd . "&SetTimeTo=" . $val . "' class='bigsort'><img src='arrow.gif' style='border:0'> " . $lang['add'] . "</a><br>";
								}
							}
							print $ins;
							print "</td></tr></table></td>";	
							unset($ovwr);
						} else {

							print "<td bgcolor='" . $bgc . "'>"; 				
							print "&nbsp;";
							$prt=1;
							if ($select) {
								print "<a href=\"javascript:applydate('$dd','$val')\"><img style='border:0' src='timedate.gif' title='Select'></a><br>";	
							} else {
								if (!$GLOBALS['HIDEADDTAB'] || $is_admin) {
									print "<a href='edit.php?e=_new_&SetDateTo=" . $dd . "&SetTimeTo=" . $val . "' class='bigsort'><img src='arrow.gif' style='border:0'> " . $lang['add'] . "</a><br>";
								}
							}
							print "</td>";
						}
				} else {
						if ($ovwr) {
							print "<td bgcolor='#FFFFFF' NOWRAP valign='top'>";
							print "<table border='1' cellpadding=2 cellspacing=2 bordercolor='#FF0000' width='100%' height='100%'><tr><td bgcolor='#ffffff' style='border:0' valign='top'>";
							if ($select) {
								print "<a href=\"javascript:applydate('$dd','$val');\"><img style='border:0' src='timedate.gif' title='Select'></a><br>";
							} else {
								if (!$GLOBALS['HIDEADDTAB'] || $is_admin) {
										print "<a href='edit.php?e=_new_&SetDateTo=" . $dd . "&SetTimeTo=" . $val . "' class='bigsort'><img src='arrow.gif' style='border:0'> " . $lang['add'] . "</a><br>";
								}
							}
							print $ins;
							print "</td></tr></table></td>";	
							unset($ovwr);
						} else {
							print "<td bgcolor='#FFFFFF' NOWRAP valign='top'>";
							if ($select) {
								print "<a href=\"javascript:applydate('$dd','$val');\"><img style='border:0' src='timedate.gif' title='Select'></a><br>";	
							} else {
								if (!$GLOBALS['HIDEADDTAB'] || $is_admin) {
									print "<a href='edit.php?e=_new_&SetDateTo=" . $dd . "&SetTimeTo=" . $val . "' class='bigsort'><img src='arrow.gif' style='border:0'> " . $lang['add'] . "</a><br>";
								}
							}
							print $ins;
							unset($ins);
							print "</td>";
						}
				}
				
			}
//			print "</td>";
	

		}
		unset($prt);
		unset($val);
		unset($txt);
		print "</tr>";

	}

	print "</table><br></td><td>&nbsp;</td></tr></table></fieldset></td></tr></table>";
}
if (!$MainPageCalendar) {
	EndHTML();
}
?>

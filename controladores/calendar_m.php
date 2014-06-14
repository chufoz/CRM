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
include("header.inc.php");

$MainPageCalendar=1;
$SuperCalendar=1;


if ($_GET['weekdetail']) {

	ShowWeek($_GET['weekdetail'],$_GET['year']);

} else {


	if (!$strmonth) {
		$strmonth = date("m");
	}
	if (!$stryear) {
		$stryear= date("Y");
	}
	//print "Jaar: $stryear -- Maand: $strmonth -- toshow: $MonthsToShow pred: $pred<br>";

	// Calculate numer of months to show - $MonthsToShow config value

	// To do so, we need the # of seconds in the # of given months
	// 2592000 = avg. # seconds in one month

	// First, capture any missing params:



	if (!$pred) {
		$Seconds = (($MonthsToShow-1) * 2509280);
		$Seconds += 12960000;
		$pred=time()+$Seconds;
	}


	$prevmonth = $strmonth-1;
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


	//function prCalendar($fromyear,$frommonth,$fromday,$href,$username,$session) {


		print "<table><tr><td class='smallsort'>Calendar</td></tr></table><br>";
		print "<table width='100%'><tr><td><a class='bigsort' href='calendar.php?strmonth=" . $prevmonth . "&stryear=" . $prevyear . "'>prev</a></td><td align='right'><a class='bigsort' href='calendar_m.php?strmonth=" . $nextmonth . "&stryear=" . $nextyear . "&pred=" . $nextpred . "'>next</a></td></tr></table>";


	prCalendar($stryear,$strmonth,"1","bla","","");

		print "<table width='100%'><tr><td><a class='bigsort' href='calendar_m.php?strmonth=" . $prevmonth . "&stryear=" . $prevyear . "'>prev</a></td><td align='right'><a class='bigsort' href='calendar_m.php?strmonth=" . $nextmonth . "&stryear=" . $nextyear . "&pred=" . $nextpred . "'>next</a></td></tr></table>";





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
			function applydate(e1)
			{
					window.opener.document.EditEntity.content.focus();
					window.opener.document.EditEntity.duedate.value = e1;
					window.opener.document.EditEntity.content.focus();
					window.opener.document.EditEntity.displayDate.value = AdjustDate(e1);
					window.opener.document.EditEntity.content.focus();
					window.opener.document.EditEntity.displayDate.blur();
					window.opener.document.EditEntity.duedate.blur();
					window.opener.document.EditEntity.content.focus();
					window.close();
					window.opener.document.EditEntity.content.focus();
			}
		
			//-->
			</SCRIPT>
	<?
}

// 				window.opener.document.EditEntity.duedate.value = document.townselect.town.value;

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
	global $pred, $MainPageCalendar, $SuperCalendar;
	global $maincolumn,$maxcolumn,$bdarray,$bdresult;

	(int)$fromyear;
	(int)$frommonth;
	(int)$fromday;
	
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
	global $MainPageCalendar,$lang,$monthcounter,$MonthsToShow;
	$monthcounter++;
	if ($monthcounter<($MonthsToShow+1)) {
		$todayd= date(d);
		$todaym= date(m);
		$todayy= date(Y);
		global $maincolumn,$maxcolumn,$bdarray,$bdresult, $color1, $color2;
		if ($maincolumn == 1 ){
			print "<tr valign=\"top\">\n";
		}
		$first=@mktime(0,0,0,$month,1,$year);
		//print cel waar maandnaam in staat
		?>
		<td class='smallsort'><table border=0 cellpadding=1 cellspacing=1><tr><td colspan="9" align="center">
		<?
		print (date("F Y",$first));
		echo "<tr align=right><td class='smallsort'>Week</td>\n<td class='smallsort'><b>Mo</b></td><td class='smallsort'><b>Tu</b></td><td class='smallsort'><b>We</b></td><td class='smallsort'><b>Th</b></td><td class='smallsort'><b>Fr</b></td><td class='smallsort'><b>Sa</b></td><td class='smallsort'><b>Su</b></td>";
		$wd=date("w",$first);
		if ($wd==0){
			$wd=7;
		}
		$wdtimeout=date("w");
		if ($wd==0){
			$wd=7;
		}
		$lastday=date("d",@mktime(0,0,0,$month+1,0,$year));
		$cur=-$wd+1;
		$timeoutdone=0;
		for ($k=0;$k<6;$k++){
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
				echo "<tr align=right>\n<td class='smallsort' align='center'><b><a href='calendar_m.php?weekdetail=$timestamp&year=$year' class='bigsort' alt='Go to weekview'>$weeknum</a></b>\n";
			}
			elseif ($weekcur>$lastday){
				echo "<tr align=right>\n<td class='smallsort'>\n";
			}
			else {
				$timestamp= @mktime(0,0,0,$month,$weekcur,$year);
				$weeknum= (get_week_number($timestamp));
				echo "<tr align=right>\n<td class='smallsort' align='center'><b><a href='calendar_m.php?weekdetail=$timestamp&year=$year' class='bigsort' alt='Go to weekview'>$weeknum</a></b>";
			}
			for ($i=0;$i<7;$i++ ){
				$cur++;
				if (($cur<=0) || ($cur>$lastday)){
					//print dit voor de opvulling van de tabel, zodat de datum onder de goede dag staat
					print "<td class='smallsort'>\n";
				}

				elseif (1==1){

					$timestamp= @mktime(0,0,0,$month,$cur,$year);
					$maandnaam= (date("F",$timestamp));
						$cur1 = $cur;
						if (strlen($cur1)==1) $cur1 = "0" . $cur1;
						if (strlen($month)==1) $month = "0" . $month;

						if ($MainPageCalendar) {
							//$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE duedate='$cur1-$month-$year' AND deleted<>'y'";
                            //Aggiunto da Daniele Lembo
                            $sql = "SELECT *, $GLOBALS[TBL_PREFIX]loginusers.fullname FROM $GLOBALS[TBL_PREFIX]entity, $GLOBALS[TBL_PREFIX]loginusers WHERE  $GLOBALS[TBL_PREFIX]entity.assignee =  $GLOBALS[TBL_PREFIX]loginusers.id AND duedate='$cur1-$month-$year'";
							$result= mcq($sql,$db);
							$ins = "<FONT COLOR='#000000'>";
							  while ($today= mysql_fetch_array($result)) {
									if ($today[category]<>"" && (CheckEntityAccess($row['eID']) == "ok" || CheckEntityAccess($row['eID']) == "readonly")) {
										$html .= "<a href=\'edit.php?e=" . $today[eid] . "\' class=\'smallsort\'>";
										
										$html.= $lang[customer] . ": " . GetCustomerName($today[CRMcustomer]) . "<br>" . $lang[status] . ": " . $today[status] . "<br>";
                                        //Aggiunta da Daniele Lembo
                                        $html.= $lang[assignee] . ": " . $today[fullname] . "<br>";
										$html.= $lang[category] . ": " . ereg_replace("\"","",$today[category]) . "</a><br>";
										$html.= "-------------------------------<br>";
										$ins = "<FONT COLOR='#FF0000'>";
										$ins2= "onmouseover=\"javascript:return overlib('" . $html . "', STICKY)\" onmouseout=\"javascript:nd();\" style='cursor:pointer'";
									}
							  }

							  
							  print "<td bgcolor=#FFFFFF><a $ins2 href='summary.php?owner=all&assignee=all&$GLOBALS[TBL_PREFIX]customer=all&duedate=$cur-$month-$year&csv=onscreenfull' class='smallsort' >$ins$cur</font></a></td>\n";
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
				
				}
				else{

					//Dit wordt geprint als er geen link achter zit.
					$timestamp= @mktime(0,0,0,$month,$cur,$year);
					$maandnaam= (date("F",$timestamp));
					$todaynumberofyear= date("z",$timestamp);
					if ($bdarray[1]==$month && $bdarray[2]==$cur){
						echo "<td bgcolor=\"#FF8746\"><a href=javascript:applydate($timestamp) onMouseOver=\"Statusbalk('$cur $maandnaam wordt";
						$doen=0;
						do {
							$jaartal=explode ("-",$bdarray[bdate]);
							$jaren = $year - $jaartal[0];
							if ($doen==1) echo " en";
							echo " $bdarray[person] $jaren";
							$bdarray=mysql_fetch_array($bdresult);
							$doen=1;
						} while ($bdarray[1]==$month && $bdarray[2]==$cur);
						if (strlen($cur1)==1) $cur1 = "0" . $cur1;
						echo " jaar');return document.returnValue\">$cur</a></td>\n";

					}
					else{
						$cur1 = $cur;
						if (strlen($cur)==1) $cur = "0" . $cur;
						if ($MainPageCalendar) {
							//$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE duedate='$cur1-$month-$year' AND deleted<>'y'";
                            //Aggiunto da Daniele Lembo
                            $sql = "SELECT $GLOBALS[TBL_PREFIX]entity.*, $GLOBALS[TBL_PREFIX]loginusers.fullname FROM $GLOBALS[TBL_PREFIX]entity,  $GLOBALS[TBL_PREFIX]loginusers WHERE  $GLOBALS[TBL_PREFIX]entity.assignee =  $GLOBALS[TBL_PREFIX]loginusers.id  AND  duedate='$cur1-$month-$year'";
							$result= mcq($sql,$db);
							$ins = "<FONT COLOR='#000000'>";
							
							  while ($today= mysql_fetch_array($result)) {
									if ($today[category]<>"" && (CheckEntityAccess($row['eID']) == "ok" || CheckEntityAccess($row['eID']) == "readonly")) {
										$html .= "<a href=\'edit.php?e=" . $today[eid] . "\' class=\'smallsort\'>";
										$html.= $lang[customer] . ": " . GetCustomerName($today[CRMcustomer]) . "<br>" . $lang[status] . ": " . $today[status] . "<br>";
                                        //Aggiunta da Daniele Lembo
                                        $html.= $lang[assignee] . ": " . $today[fullname] . "<br>";
										$html.= $lang[category] . ": " . ereg_replace("\"","",$today[category]) . "</a><br>";
										$html.= "-------------------------------<br>";
										$ins = "<FONT COLOR='#FF0000'>";
										$ins2= "onmouseover=\"javascript:return overlib('" . $html . "', STICKY)\" onmouseout=\"javascript:nd();\" style='cursor:pointer'";
									}
							  }
						
							print "<td class='smallsort'><a $ins2 href='summary.php?owner=all&assignee=all&$GLOBALS[TBL_PREFIX]customer=all&duedate=$cur-$month-$year&csv=onscreenfull' class='smallsort' >$ins$cur</font></a></td>\n";
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
	} // end if monthcounter < monthstoshow
}

function ShowWeek($weeknum,$year) {
	global $lang;
	// weeknum contains timestamp

	$nextweek = $weeknum + 604800;
	$prevweek = $weeknum - 604800;
	
	print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Calendar&nbsp;</legend><table>";
	print "<tr><td><a class='bigsort' href='calendar_m.php?weekdetail=" . $prevweek . "&year=" . $year. "'>&lt;&lt;</a>&nbsp;&nbsp;<a class='bigsort' href='calendar_m.php?weekdetail=" . $nextweek . "&year=" . $year. "'>&gt;&gt;</a></td></tr>";
	print "<tr><td>&nbsp;</td><td>";
	
	if (strtoupper($GLOBALS['CAL_USEWEEKEND'])=="NO") {
		$NUMDAYS = 5;
	} else {
		$NUMDAYS = 7;
	}

	print "<table border='1' width='100%' bgcolor='#F2F2F2' cellspacing='0' cellpadding='4' bordercolor='#757575'>";


		
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
	
	$tmp = $weeknum;

	for ($i=0;$i<$NUMDAYS;$i++) {
		

		$today = date('l,d M Y');
		$basictoday = date('dmY',$tmp);
		$tmp += 86400;
		$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]calendar WHERE basicdate='" . $basictoday . "'";
		$result = mcq($sql,$db);
		while ($row = mysql_fetch_array($result)) {
			$b .= "&nbsp;<img src='info.gif'>&nbsp;" . $lang['alarmdate'] . " " . strtolower($lang['entity']) . " " . $row['eID'] . "<BR>";
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
				print "<tr><td><b><font color='#FF0000'>"  . $txt . "</font></b></td>";
			} else {
				print "<tr><td><b>"  . $txt . "</b></td>";
			}
		} else {
			unset($s30);
		}
		$tmp = $weeknum;

		$today = date('d-m-Y');

		for ($c=0;$c<$NUMDAYS;$c++) {

			unset($prt);

			$a = date('l,d M Y',$tmp);
			$dd = date('d-m-Y',$tmp);
			$tmp += 86400;

			// duedate format is DD-MM-YYYYY
			
	
			$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE LEFT(duetime,2)='" . substr($val,0,2) . "' AND duedate='" . $dd . "'";
			
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
						$ins .= "<img src='arrow.gif'>&nbsp;<a href='edit.php?e=" . $row['eid'] ."' class='bigsort'>" . $row['eid'] . ": " . $row['category'] ."</a><br>";
						$prt=1;
						$p++;
					}
					
				}
				if ($p==6) {
					$ins .= "<a href='summary.php?owner=all&assignee=all&$GLOBALS[TBL_PREFIX]customer=all&duedate=$dd&csv=onscreenfull' class='smallsort'>&lt;more&gt;</a>";
				}

				if (!$prt==1) {
					if ($ovwr) {
							print "<td bgcolor='$bgc'>";
							print "<table border='1' cellpadding=2 cellspacing=2 bordercolor='#FF0000' width='100%' height='100%'><tr><td bgcolor='#ffffff' style='border:0'>&nbsp;";

							print "</td></tr></table></td>";	
							unset($ovwr);
						} else {
							print "<td bgcolor='" . $bgc . "'>"; 				
							print "&nbsp;";
							$prt=1;
							print "</td>";
						}
				} else {
						if ($ovwr) {
							print "<td bgcolor='#FFFFFF'>";
							print "<table border='1' cellpadding=2 cellspacing=2 bordercolor='#FF0000' width='100%' height='100%'><tr><td bgcolor='#ffffff' style='border:0'>";
							print $ins;
							print "</td></tr></table></td>";	
						} else {
							print "<td bgcolor='#FFFFFF'>"; 				
							print $ins;
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

	print "</table><br></td><td>&nbsp;</td></tr></table></fieldset>";
}

?>
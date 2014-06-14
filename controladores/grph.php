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
$a = (get_loaded_extensions());

if (in_array("gd",$a)) {
		$gd = "Checked and OK";
	} else {
		print "<font color='#FF0000'>GD Not installed, unable to generate images.</font>";
		print "</body></html>";
		exit;
	}




if ($hits) {
		require("fgrph.php");
		include("config.inc.php");
		include("getset.php");
		$sql= "SELECT timestamp FROM $GLOBALS[TBL_PREFIX]journal";
		if ($debug) { print "\nSQL: $sql\n"; }
		$result= mcq($sql,$db);								
		while ($resarr=mysql_fetch_array($result)){
				$x++;
				$table_array[$x] = substr($resarr[timestamp],0,8);
		}

		$desc = "Activity ($title CRM " . getenv("SERVER_NAME") . ")";

} else {
		include("header.inc.php");
		print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Statistics</legend><img src='grph.php?hits=1'><br><br>";
		print "Activity stats are based on the journal file CRM keeps. An activity<br>";
		print "hit can be an added entity, viewing the summary page, editing a <br>";
		print "customer, etc. Overall it is a quite good representation of the <br>";
		print "activity of this CRM installation.<br><br>It could take a while to load as CRM generates the image on the fly.";		
		print "</fieldset></html>";
		uselogger("Statistics viewed","");
		exit;
}


sort($table_array);

$unique = array();

for ($y=0;$y<sizeof($table_array);$y++) {
	
	if (!in_array($table_array[$y], $unique)) {
	    // Found a unique entry....
		$unique[$inarray] = $table_array[$y];
		$inarray++;
	} else {
		// Non-unique, add to count table...
	    for ($z=0;$z<sizeof($unique);$z++ ) {
			if ($unique[$z]==$table_array[$y]) {
			    $count[$z]++;
				$totcount++;
				
			}
	    }
	}
}

$leeg = array();
tekenGrafiek($unique, $count, $leeg, true, $desc, 500, 300);



function WeekGraph($item) {

	$year = date("Y");
	for ($x=2;$x<14;$x++) {
	$month = date("F", @mktime (0,0,0,$x,0,0));
	$monthnumber = date("m", @mktime (0,0,0,$x,0,0));
	$totdate = $year . $monthnumber . "01";
//	$month = date("F");
	$sqlcounter++;
	// WHERE status='open'
	$sql = "SELECT eid,cdate FROM $GLOBALS[TBL_PREFIX]entity";
	if ($debug) { print $sql; }
	$result= mcq($sql,$db);
        while ($e= mysql_fetch_array($result)) {
				$t = $e[cdate];
				$tjaar = substr($t,0,4);
				if ($tjaar == $year) {

					$tmaand = substr($t,5,2);
					$tdag = substr($t,8,2);
					$tmp = date ("F", @mktime (0,0,0,$tmaand,$tdag,$tjaar));

	//				print $tmp;
					if ($month == $tmp) {
						$thismonth++;
					}
				}
		}
	if (!$thismonth) { $thismonth=0; }
	

//	$month = date("F");
	$sqlcounter++;
	//status='close' AND
	$sql = "SELECT eid,closedate FROM $GLOBALS[TBL_PREFIX]entity WHERE closedate<>'0000-00-00' AND status='close'";
	if ($debug) { print $sql; }
	$result= mcq($sql,$db);
        while ($e= mysql_fetch_array($result)) {
				$t = $e[closedate];
				$tjaar = substr($t,0,4);
				$year = date("Y");
				if ($tjaar == $year) {

						$tmaand = substr($t,5,2);
						$tdag = substr($t,8,2);
						$tmp = date ("F", @mktime (0,0,0,$tmaand,$tdag,$tjaar));
		//				print "<tr><td>Matching $tmp against $month. Date is $e[closedate] </td></tr>";
		//				print $tmp;
						if ($month == $tmp) {
							$thismonth2++;
						}
				}
		}
	// Yet try to determine the average 'open-time' in this week only

	if (!$thismonth2) { 
		$thismonth2=0; 
		}
//	print "<tr><td>$month</td><td>$thismonth</td><td>$thismonth2</td></tr>";
	$arrB[$tio] = $totdate;
	$arrA[$tio] = $thismonth;
	$arrC[$tio] = $thismonth2;
	$tio++;
	unset ($thismonth);
	unset ($thismonth2);

	
	}

	if ($item=="added") {
		$desc = "Entities added per month";
		tekenGrafiek($arrB, $arrA, $leeg, true, $desc, 500, 300);
	} elseif ($item=="closed") {
		$desc = "Entities closed per month";
		tekenGrafiek($arrB, $arrC, $leeg, true, $desc, 500, 300);
	}
}
?>
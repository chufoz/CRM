<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This creates the management PDF report
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

//include("pdf_inc.php");
include("config.inc.php");
include("getset.php");
include("pdf_inc2.php");
include("superstatsimg.php");

$sql= "SELECT type,CLLEVEL,id,FULLNAME FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='$name'";
if ($debug) { print "\nSQL: $sql\n"; }
$result= mcq($sql,$db);
$result= mysql_fetch_array($result);

//$user_id = $result[id];
$fullname = $result[FULLNAME];

$a = $result[CLLEVEL];

if ($result[CLLEVEL]=="ooro") {
	$result[type]='limited';
}
if ($result[CLLEVEL]=="ro" && !$thisishelp==1 && !$custinsertmode) {
?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
			document.location='cust-insert.php';	
		//-->
</SCRIPT>
	<?
	exit;
}

if ($a == "full-access-own-entities" || $a == "read-only-all" || $a == "full-access-own-entities" || $a == "rw" || $a == "administrator") {
	// access ok
} else {
	print "Access denied";
	exit;
}

if (($result[type]=='limited' || $result[CLLEVEL]=="read-only-all")&& !$thisishelp==1) {
	?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
		
		if (document.location!='management.php') {
			document.location='management.php';		    
		}

		//-->
</SCRIPT>
	<?
	exit;
}
$date = date("F j, Y, H:i") . "h";
$pdftitle2 = $pdftitle;
$pdftitle = "$pdftitle $CRM_VERSION. Report created $date";

$pdf=new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
line();
//pdf->Ln(4);
$pdf->SetFont('Arial','',18);
$pdf->Cell(0,4,$lang[maninfo],0,1);
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,10,$pdftitle2,0,1);
//line();

$a = array();

$cdate = date('Y-m-d');

$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]loginusers";
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$maxU = $maxU1[0];

$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity";
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$maxE = $maxU1[0];
$maxEo = $maxU1[0];

if ($maxEo==0) {
	print "<html><body><table><tr><td><img src='crm.gif'><br><img src='error.gif'>&nbsp;<b><font size='+1' face='Trebuchet MS'>You cannot create statistics on an empty database!</font></b><br>&nbsp;&nbsp;&nbsp;<font size='+1' face='Trebuchet MS'>This error is fatal.</font><br><br>";
	print "<font face='Trebuchet MS'>Click <a href='javascript:history.back(-1);'>here</a> to go back....</font></td></tr></table></body></html>";
	exit;
}

$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted='y'";
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$delE = $maxU1[0];

$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE obsolete='y'";
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$obsE = $maxU1[0];

$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE waiting='y'";
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$waiE = $maxU1[0];

$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]customer";
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$maxC = $maxU1[0];

$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE sqldate<'$cdate'";
$result= mcq($sql,$db);
$maxU1 = mysql_fetch_array($result);
$expE = $maxU1[0];

$tc=0;



include("language.php");

	$data = array();
	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]statusvars ORDER BY varname";
	$result= mcq($sql,$db);
	$totaal=0;
	while ($e= mysql_fetch_array($result)) {
			
			$rgb_col_array = hex2rgb($e['color']);

			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE status='$e[varname]'";
			$result1= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($result1);
			$bla = $maxU1[0];
			$pc1 = ($maxE/100); // dit is 1 procent
			$pc2 = ($bla/100); // dit is 1 procent van not [deleted]
			$apc = round($bla/$pc1); // dit is het percentage
			array_push($data,array($e[varname],$bla,$apc . "%","$rgb_col_array[0]","$rgb_col_array[1]","$rgb_col_array[2]"));

			$totaal=$totaal+$bla;
	}
	$header=array($lang[status],'#','%');
	$pdf->SetFont('Arial','',18);
	$pdf->SetLink($$lang[oqs]);
	$pdf->Bookmark($lang[oqs]);
	$pdf->AddLink();
	$pdf->Cell(0,6,$lang[oqs],0,1);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(0,6,"(" . $lang[allround] . ")" ,0,1);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(0,10,$lang[entities] . " : " . $maxE . ", " . $lang[ofwhich]);
	$pdf->Ln();
//	$pdf->Bookmark($lang[entities],1,-1);
	$pdf->FancyTable3col($header,$data);
	$pdf->Ln(2);

	$header=array($lang[priority],'#','%');
	$data = array();
	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]priorityvars ORDER BY varname";
	$result= mcq($sql,$db);
	$totaal=0;
	while ($e= mysql_fetch_array($result)) {
			
			$rgb_col_array = hex2rgb($e['color']);

			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE priority='$e[varname]'";
			$result1= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($result1);
			$bla = $maxU1[0];
			$pc1 = ($maxE/100); // dit is 1 procent
			$pc2 = ($bla/100); // dit is 1 procent van not [deleted]
			$apc = round($bla/$pc1); // dit is het percentage
			array_push($data,array($e[varname],$bla,$apc . "%","$rgb_col_array[0]","$rgb_col_array[1]","$rgb_col_array[2]"));

			$totaal=$totaal+$bla;
	}

	if ($totaal<>$MaxE) {
		$bla = $maxE - $totaal;
		$apc = round($bla/$pc1); // dit is het percentage
		if ($bla<>"") {
			array_push($data,array("unknown/unreferenced",$bla,$apc . "%"));
		}
	}
	$pc1 = ($maxEo/100); // dit is 1 procent
	$apc = round($delE/$pc1); // dit is het percentage
//	array_push($data,array($lang[deleted],$delE,$apc . "%"));

	$apc = round($expE/$pc1); // dit is het percentage
	if ($apc>30) { 
		$a1 = "";
		$a2 = "";
	} else {
		unset($a1);
		unset($a2);
	}

	
	$pdf->FancyTable3col($header,$data);
	$pdf->Ln(8);

	
	unset($data);
	$data=array();
	$header=array('','#','%');
	array_push($data,array($lang[edd],$expE,$apc . "%"));
	$apc = round($obsE/$pc1); // dit is het percentage
	array_push($data,array($lang[dontbelonghere],$obsE,$apc . "%"));
	
	$apc = round($waiE/$pc1); // dit is het percentage
	
	
	array_push($data,array($lang[ewfsea],$waiE,$apc . "%"));


	$pdf->FancyTable3col($header,$data);

	$weeknumber = date("W");
	$month = date("m");
	$sql = "SELECT openepoch FROM $GLOBALS[TBL_PREFIX]entity";
	$result = mcq($sql,$db);
	qlog("Calculating 'thisweek opened'");
	  while ($e2= mysql_fetch_array($result)) {
				
				if ($e2['openepoch']<>"") {
					$c_week = date("W", $e2['openepoch']);
					$c_month = date("m", $e2['openepoch']);

					$t_week = date("W");
					$t_month = date("m");
					if ($c_month == $month) {
						$thismonth++;
					}
					if ($c_week == $weeknumber) {
						$thisweek++;
					}
				}
		}
	

	if (!$thisweek) { 
		$thisweek="0"; 
		}
	qlog("Result: M:$thismonth W:$thisweek");
	$pdf->Ln(8);
	unset($data);
	$data=array();
	$apc = round($thisweek/$pc1); // dit is het percentage
	array_push($data,array("$lang[eatw] ($weeknumber)",$thisweek,$apc . "%"));


	qlog("Calculating 'thisweek & thismonth closed'");
	
	$sql = "SELECT closeepoch FROM $GLOBALS[TBL_PREFIX]entity";
	$result = mcq($sql,$db);
	
	  while ($e2= mysql_fetch_array($result)) {
				
				if ($e2['closeepoch']<>"") {
					$c_week = date("W", $e2['closeepoch']);
					$c_month = date("m", $e2['closeepoch']);

					$t_week = date("W");
					$t_month = date("m");
					if ($c_month == $t_month) {
						$thisDELmonth++;
					}
					if ($c_week == $t_week) {
						$thisDELweek++;
					}
				}
		}
	if (!$thisDELweek) { $thisDELweek="0"; }

	
	$apc = round($thisDELweek/$pc1); // dit is het percentage
	array_push($data,array($lang['ectw'] . " ($weeknumber)",$thisDELweek,$apc . "%"));

		
	if (!$thismonth) { $thismonth="0"; }
	$apc = round($thismonth/$pc1); // dit is het percentage
	array_push($data,array("$lang[eatm] ($month)",$thismonth,$apc . "%"));

	$month = date("F");
	$sqlcounter++;
	$sql = "SELECT eid,closedate FROM $GLOBALS[TBL_PREFIX]entity WHERE closedate<>'0000-00-00' and deleted='y'";
	$result= mcq($sql,$db);
        while ($e= mysql_fetch_array($result)) {
				$t = $e[closedate];
				$tjaar = substr($t,0,4);
				$year = date("Y");
				if ($tjaar == $year) {

					$tmaand = substr($t,5,2);
					$tdag = substr($t,8,2);
					$tmp = date ("F", @mktime (0,0,0,$tmaand,$tdag,$tjaar));
					if ($month == $tmp) {
						$thismonth2++;
					}
				}
		}
	if (!$thisDELmonth) { $thisDELmonth="0"; }
	$apc = round($thisDELmonth/$pc1); // dit is het percentage
	//"Entities deleted this month ($month)"
	array_push($data,array($lang['ectm'] . " ($month)",$thisDELmonth,$apc . "%"));

	$sqlcounter++;
	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE cdate<>'0000-00-00' AND closedate<>'0000-00-00' AND deleted='y'";
	$result= mcq($sql,$db);
        while ($e= mysql_fetch_array($result)) {

				$t = $e[closedate];
				$tjaar = substr($t,0,4);
				$tmaand = substr($t,5,2);
				$tdag = substr($t,8,2);
				$tmp = date ("U", @mktime (0,0,0,$tmaand,$tdag,$tjaar));
				$t = $e[cdate];
				$tjaar = substr($t,0,4);
				$tmaand = substr($t,5,2);
				$tdag = substr($t,8,2);
				$tmp2 = date ("U", @mktime (0,0,0,$tmaand,$tdag,$tjaar));
				$avg = $avg + round((($tmp-$tmp2)/86400));
				$avgc++;
	}

	
	$pdf->FancyTable3col($header,$data);
	$pdf->Ln(8);
	unset($data);
	$data=array();
	$header=array('','#','%');
	
	array_push($data,array($lang[users],$maxU,"n/a"));
	array_push($data,array($lang[customers],$maxC,"n/a"));

	
	$pdf->FancyTable3col($header,$data);
	$pdf->AddPage();

	$tc = sizeof($a);

	$filename = tempnam($GLOBALS['TMP_FILE_PATH'],"CRM_TMP_BIN_");	
	$year = date("Y");
	YearPlot($year);
	//$pdf->Image("crm.jpg",30,30,0,0,"JPG");
	$pdf->Image($filename,10,41,100,'','png','');
	unlink($filename);

	$filename = tempnam($GLOBALS['TMP_FILE_PATH'],"CRM_TMP_BIN_");	
	$year = date("Y");
	MonthPlot($year);
	//$pdf->Image("crm.jpg",30,30,0,0,"JPG");
	$pdf->Image($filename,10,99,180,'','png','');
	unlink($filename);

	
	$filename = tempnam($GLOBALS['TMP_FILE_PATH'],"CRM_TMP_BIN_");	
	$year = date("Y");
	PieStatusPlot();
	//$pdf->Image("crm.jpg",30,30,0,0,"JPG");
	$pdf->Image($filename,10,160,180,'','png','');
	unlink($filename);


	$pdf->AddPage();


// ========================================= Entities per customer

		
		$sql = "SELECT id,custname FROM $GLOBALS[TBL_PREFIX]customer ORDER BY custname";
		$result= mcq($sql,$db);
		$a[$tc] = "<pb><head><red>$lang[epc]";
		$pdf->SetLink($$lang[epc]);
		$pdf->AddLink();
		$pdf->Bookmark($lang[epc]);

		$pdf->SetFont('Arial','',18);
		$pdf->Cell(0,10,$lang[epc],0,1);
		$pdf->SetFont('Arial','',8);
		unset($data);
		$data=array();
		$header=array('Open','Deleted','Total','%',$lang[customer]);

		while ($id = mysql_fetch_array($result)) {
			$auth = CheckCustomerAccess($id['id']);
			if ($auth == "ok" || $auth == "readonly") {

		
				$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE CRMcustomer='$id[id]'";
				$pr1 = mcq($sql,$db);
				$pr = mysql_fetch_array($pr1);
				$pc1 = ($maxE/100); // dit is 1 procent
				$apc = @round($pr[0]/$pc1);

				$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE CRMcustomer='$id[id]' AND deleted='y'";
				$pr1 = mcq($sql,$db);
				$prD = mysql_fetch_array($pr1);
				
				$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted<>'y' AND CRMcustomer='$id[id]'";
				$resul1t= mcq($sql,$db);
				$maxU1 = mysql_fetch_array($resul1t);
				$open = $maxU1[0];

				
				$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE status='close' AND deleted<>'y' AND CRMcustomer='$id[id]'";
				$resul1t= mcq($sql,$db);
				$maxU1 = mysql_fetch_array($resul1t);
				$close = $maxU1[0];

				
				$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE status='awaiting closure' AND deleted<>'y' AND CRMcustomer='$id[id]'";
				$resul1t= mcq($sql,$db);
				$maxU1 = mysql_fetch_array($resul1t);
				$aclose = $maxU1[0];

				$open = fillout($open,5);
				$aclose = fillout($aclose,5);
				$close = fillout($close,5);
				$prD[0] = fillout($prD[0],5);
				$pr[0] = fillout($pr[0],5);
				$apc = fillout($apc,3);

				array_push($data,array($open,$prD[0],$pr[0],$apc . "%",$id[custname]));
				$tc++;
			}
	}
$pdf->FancyTable5col($header,$data);
$pdf->AddPage();

$filename = tempnam($GLOBALS['TMP_FILE_PATH'],"CRM_TMP_BIN_");	
$year = date("Y");
EntitiesPerCustomer();
//$pdf->Image("crm.jpg",30,30,0,0,"JPG");
$pdf->Image($filename,10,30,180,'','png','');
unlink($filename);
$pdf->AddPage();

$pdf->SetLink($$lang[eatu]);
$pdf->AddLink();
$pdf->Bookmark($lang[eatu]);
$pdf->SetFont('Arial','',18);
$pdf->Cell(0,10,$lang[eatu],0,1);
$pdf->SetFont('Arial','',8);

unset($data);
$data=array();
$header=array('Open','Deleted','Total','%',$lang[assignee]);

	
	$sql = "SELECT id,FULLNAME FROM $GLOBALS[TBL_PREFIX]loginusers ORDER BY FULLNAME";
	$result= mcq($sql,$db);
	
	while ($id = mysql_fetch_array($result)) {
		
		$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE assignee='$id[id]'";
		$pr1 = mcq($sql,$db);
		$pr = mysql_fetch_array($pr1);
		
		$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE assignee='$id[id]' AND deleted='y'";
		$pr1 = mcq($sql,$db);
		$prD = mysql_fetch_array($pr1);
		$pc1 = ($maxE/100); // dit is 1 procent
		$apc = round($pr[0]/$pc1);


		
		$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted<>'y' AND assignee='$id[id]'";
		$resul1t= mcq($sql,$db);
		$maxU1 = mysql_fetch_array($resul1t);
		$open = $maxU1[0];

		
		$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE status='close'  AND deleted<>'y' AND assignee='$id[id]'";
		$resul1t= mcq($sql,$db);
		$maxU1 = mysql_fetch_array($resul1t);
		$close = $maxU1[0];

		
		$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE status='awaiting closure' AND deleted<>'y' AND assignee='$id[id]'";
		$resul1t= mcq($sql,$db);
		$maxU1 = mysql_fetch_array($resul1t);
		$aclose = $maxU1[0];

		$open = fillout($open,5);
		$aclose = fillout($aclose,5);
		$close = fillout($close,5);
		$prD[0] = fillout($prD[0],5);
		$pr[0] = fillout($pr[0],5);
		$apc = fillout($apc,3);
		
		if (!$pr[0]==0) {	
			$a[$tc] =  "$open | $prD[0] | $pr[0] | $apc% | $id[FULLNAME]";
			array_push($data,array($open,$prD[0],$pr[0],$apc . "%",$id[FULLNAME]));
			$tc++;
			}
	}



$pdf->FancyTable5col($header,$data);
$pdf->AddPage();



$pdf->SetLink($$lang[eobu]);
$pdf->AddLink();
$pdf->Bookmark($lang[eobu]);
$pdf->SetFont('Arial','',18);
$pdf->Cell(0,10,$lang[eobu],0,1);
$pdf->SetFont('Arial','',8);
unset($data);
$data=array();
$header=array('Open','Deleted','Total','%',$lang[owner]);


			
			$sql = "SELECT id,FULLNAME FROM $GLOBALS[TBL_PREFIX]loginusers ORDER BY FULLNAME";
			$result= mcq($sql,$db);
		
		while ($id = mysql_fetch_array($result)) {
			
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE owner='$id[id]'";
			$pr1 = mcq($sql,$db);
			$pr = mysql_fetch_array($pr1);

			
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE owner='$id[id]' AND deleted='y'";
			$pr1 = mcq($sql,$db);
			$prD = mysql_fetch_array($pr1);


			$pc1 = ($maxE/100); // dit is 1 procent
			$apc = round($pr[0]/$pc1); // dit is het percentage

			
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted<>'y' AND owner='$id[id]'";
			$resul1t= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($resul1t);
			$open = $maxU1[0];

		
			$open = fillout($open,5);
			$aclose = fillout($aclose,5);
			$close = fillout($close,5);
			$prD[0] = fillout($prD[0],5);
			$pr[0] = fillout($pr[0],5);
			$apc = fillout($apc,2);
			
			if (!$pr[0]==0) {	
				$a[$tc] =  "$open | $prD[0] | $pr[0] | $apc% | $id[FULLNAME]";
					array_push($data,array($open,$prD[0],$pr[0],$apc . "%",$id[FULLNAME]));
			}
			$tc++;
	}

$pdf->FancyTable5col($header,$data);
$pdf->AddPage();

$pdf->SetLink($$lang[etdbh]);
$pdf->AddLink();
$pdf->Bookmark($lang[etdbh]);
$pdf->SetFont('Arial','',18);
$pdf->Cell(0,10,$lang[etdbh],0,1);
$pdf->SetFont('Arial','',8);
unset($data);
$data=array();
$header=array('Entities','out of','total','%',$lang[owner]);


			

			
			$sql = "SELECT DISTINCT owner FROM $GLOBALS[TBL_PREFIX]entity WHERE obsolete='y' ORDER BY owner";
			$result= mcq($sql,$db);
			while ($id = mysql_fetch_array($result)) {

			$pdf_name = GetUserName($id['owner']);
			
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE owner='$id[owner]'";
			$resul1t= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($resul1t);
			$top1 = $maxU1[0];
			
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE owner='$id[owner]' AND obsolete='y'";
			$resul1t= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($resul1t);
			$top2 = $maxU1[0];

			$top1pc = $top1 / 100;
			$top2pc = round($top2 / $top1pc);
			$top2 = fillout($top2,3);
			$top1 = fillout($top1,3);
			$top2pc = fillout($top2pc,4);
			$a[$tc] = $top2 . " out of " . $top1 . "    " . $top2pc . "%     " . $pdf_name;
			array_push($data,array($top2,"out of", $top1,$top2pc . "%",$pdf_name));
			$tc++;
			}


			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE status='open'";
			$result= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($result);
			$maxtmp = $maxU1[0];
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE obsolete='y' AND status='open'";
			$resul1t= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($resul1t);
			$top3 = $maxU1[0];

			$tmp = $maxtmp / 100;
			
			if ($top3<>0) {
				$prt = round($top3 / $tmp);
			}
			$a[$tc] = " ";
				$tc++;
			$a[$tc] = "On avarage $prt% of all open entities doesn't belong here.";
			$pdf->Cell(0,10,"On avarage $prt% of all open entities doesn't belong here.",0,1);
			$tc++;

			
			$pdf->FancyTable5col($header,$data);
			$pdf->AddPage();

			$pdf->SetLink($$lang[sae]);
			$pdf->AddLink();
			$pdf->Bookmark($lang[sae]);
			$pdf->SetFont('Arial','',18);
			$pdf->Cell(0,10,$lang[sae],0,1);
			$pdf->SetFont('Arial','',8);
			unset($data);
			$data=array();
			$header=array('Entities','out of','total','%',$lang[owner] . "/" . $lang[assignee]);

			
			
			$sql = "SELECT DISTINCT owner FROM $GLOBALS[TBL_PREFIX]entity WHERE $GLOBALS[TBL_PREFIX]entity.owner=$GLOBALS[TBL_PREFIX]entity.assignee ORDER BY owner";
			$result= mcq($sql,$db);
			while ($id = mysql_fetch_array($result)) {

			$pdf_name = GetUserName($id['owner']);
			
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE owner='$id[owner]'";
			$resul1t= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($resul1t);
			$top1 = $maxU1[0];
			
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE owner='$id[owner]' AND $GLOBALS[TBL_PREFIX]entity.owner=$GLOBALS[TBL_PREFIX]entity.assignee";
			$resul1t= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($resul1t);
			$top2 = $maxU1[0];

			$pdf_name = GetUserName($id['owner']);

			if ($pdf_name=="2147483647") {
					$pdf_name = "[unassigned]";
			}

			$top1pc = $top1 / 100;
			$top2pc = round($top2 / $top1pc);
			
			$top2 = fillout($top2,3);
			$top1 = fillout($top1,3);
			$top2pc = fillout($top2pc,4);
			$a[$tc] = $top2 . " out of " . $top1 . "    " . $top2pc . "%     " . $pdf_name;
			array_push($data,array($top2,"out of", $top1,$top2pc . "%",$pdf_name));
			$tc++;

			}

			$pdf->FancyTable5col($header,$data);
			$pdf->AddPage();
			
			$pdf->SetLink($$lang[oepa]);
			$pdf->AddLink();
			$pdf->Bookmark($lang[oepa]);
			$pdf->SetFont('Arial','',18);
			$pdf->Cell(0,10,$lang[oepa],0,1);
			$pdf->SetFont('Arial','',8);

			unset($data);
			$data=array();
			$header=array('Entities','out of','total','%',$lang[owner] . "/" . $lang[assignee]);

			$sql = "SELECT DISTINCT assignee FROM $GLOBALS[TBL_PREFIX]entity WHERE sqldate<'$cdate' AND deleted<>'y' ORDER BY assignee";
			$result= mcq($sql,$db);
			while ($id = mysql_fetch_array($result)) {

				$pdf_name = GetUserName($id['assignee']);

				if ($pdf_name=="2147483647") {
						$pdf_name = "[unassigned]";
				}
				
				$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted<>'y' AND assignee='$id[assignee]'";
				$resul1t= mcq($sql,$db);
				$maxU1 = mysql_fetch_array($resul1t);
				$top1 = $maxU1[0];
				
				$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted<>'y' AND assignee='$id[assignee]' AND sqldate < '$cdate'";
				$resul1t= mcq($sql,$db);
				$maxU1 = mysql_fetch_array($resul1t);
				$top2 = $maxU1[0];

				$top1pc = $top1 / 100;
				$top2pc = round($top2 / $top1pc);
				$top2 = fillout($top2,3);
				$top1 = fillout($top1,3);
				$top2pc = fillout($top2pc,3);
				$a[$tc] = $top2 . " out of " . $top1 . "    " . $top2pc . "%     " . $pdf_name;
				array_push($data,array($top2,"out of", $top1,$top2pc . "%",$pdf_name));
				$tc++;
			
			}

			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE sqldate<'$cdate' AND deleted<>'yes'";
			$resul1t= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($resul1t);
			$top3 = $maxU1[0];
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE deleted<>'yes'";
			$result= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($result);
			$maxNC = $maxU1[0];

			//$maxNC = $maxE;

			$tmp = $maxNC / 100;
			$prt = round($top3 / $tmp);
			
			
			$pdf->FancyTable5col($header,$data);
			$pdf->Ln(8);
			$pdf->Cell(0,10,"On avarage $prt% of all non-deleted entities is overdue.",0,1);
			$pdf->Cell(0,0,"Other assignees have no overdue entities.",0,1);

			$pdf->AddPage();

			unset($data);
			$data = array();

			$pdf->SetLink($aa);
			$pdf->AddLink();
			$pdf->Bookmark("Most active " . strtolower($lang['entities']) . " (journal)");
			$pdf->SetFont('Arial','',18);
			$pdf->Cell(0,10,"Most active " . strtolower($lang['entities']) . " (journal)",0,1);
			$pdf->SetFont('Arial','',8);

//			function Top10_Most_active() {
//			global $lang;
		// This function calculates the 10 most active entities (based on the journal)

			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]journal";
			$result = mcq($sql,$db);
			$result = mysql_fetch_array($result);
			$max = $result[0];

			$header = array("EID","#","Act. %",$lang['customer'],$lang['category']);
			
			$sql = "select $GLOBALS[TBL_PREFIX]journal.eid,$GLOBALS[TBL_PREFIX]entity.category AS cat,$GLOBALS[TBL_PREFIX]customer.custname AS customer,COUNT(*) AS num from $GLOBALS[TBL_PREFIX]journal,$GLOBALS[TBL_PREFIX]entity,$GLOBALS[TBL_PREFIX]customer WHERE $GLOBALS[TBL_PREFIX]entity.CRMcustomer = $GLOBALS[TBL_PREFIX]customer.id AND $GLOBALS[TBL_PREFIX]journal.eid=$GLOBALS[TBL_PREFIX]entity.eid GROUP BY eid HAVING COUNT(*) > 1 ORDER BY num DESC LIMIT 20;";
			$result = mcq($sql,$db);
			while ($row = mysql_fetch_array($result)) {
				// Calculate percentage of total activity
					
				$pc = round($row['num'] / ($max / 100));

				array_push($data,array($row['eid'],$row['num'],$pc . "%",$row['customer'],$row['cat']));
			}
			//print "</table>";
		

			$pdf->FancyTable5colSpecial($header,$data);
			$pdf->Ln(8);
			unset($data);

//			function Top10_Most_active_users() {
//	global $lang;
// This function calculates the 10 most active entities (based on the journal & uselog)

			$pdf->AddPage();
			unset($data);


			$pdf->SetLink($ab);
			$pdf->AddLink();

			$pdf->SetFont('Arial','',18);
			$pdf->Cell(0,10,"Most active " . strtolower($lang['users']) . " (journal)",0,1);
			$pdf->SetFont('Arial','',8);
			$pdf->Bookmark("Most active " . strtolower($lang['users']) . " (journal)");

			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]journal";
			$result = mcq($sql,$db);
			$result = mysql_fetch_array($result);
			$max = $result[0];

			$data = array();
	
			$header = array($lang['owner'] . "/" . $lang['assignee'],"Act.","Act.%");
			
			$sql = "select $GLOBALS[TBL_PREFIX]journal.user,$GLOBALS[TBL_PREFIX]loginusers.id AS userid,COUNT(*) AS num from $GLOBALS[TBL_PREFIX]journal,$GLOBALS[TBL_PREFIX]loginusers WHERE $GLOBALS[TBL_PREFIX]journal.user=$GLOBALS[TBL_PREFIX]loginusers.id GROUP BY user HAVING COUNT(*) > 1 ORDER BY num DESC LIMIT 20;";
			$result = mcq($sql,$db);
			while ($row = mysql_fetch_array($result)) {
				// Calculate percentage of total activity
					
				$pc = round($row['num'] / ($max / 100));
				
				array_push($data,array(GetUserName($row['userid']),$row['num'],$pc . "%"));
			}

			$pdf->FancyTable3col($header,$data);
			$pdf->Ln(8);
			unset($data);

			$filename = tempnam($GLOBALS['TMP_FILE_PATH'],"CRM_TMP_BIN_");	
			$year = date("Y");
			ActivityPerUser();
			$pdf->Image($filename,10,130,180,'','png','');
			unlink($filename);
			$pdf->AddPage();

		// Top10_Most_active_users_uselog() {

		// This function calculates the 10 most active entities (based on the journal & uselog)
		
			$pdf->SetLink($ac);
			$pdf->AddLink();

			$pdf->SetFont('Arial','',18);
			$pdf->Cell(0,10,"Most active " . strtolower($lang['users']) . " (uselog)",0,1);
			$pdf->SetFont('Arial','',8);
			$pdf->Bookmark("Most active " . strtolower($lang['users']) . " (uselog)");

			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]uselog WHERE user<>''";
			$result = mcq($sql,$db);
			$result = mysql_fetch_array($result);
			$max = $result[0];


			$data = array();
	
			$header = array($lang['owner'] . "/" . $lang['assignee'],"Act.","Act.%");
			
			$sql = "select $GLOBALS[TBL_PREFIX]uselog.user,$GLOBALS[TBL_PREFIX]loginusers.id AS userid,COUNT(*) AS num from $GLOBALS[TBL_PREFIX]uselog,$GLOBALS[TBL_PREFIX]loginusers WHERE $GLOBALS[TBL_PREFIX]uselog.user=$GLOBALS[TBL_PREFIX]loginusers.name GROUP BY user HAVING COUNT(*) > 1 ORDER BY num DESC LIMIT 20;";
			$result = mcq($sql,$db);
			while ($row = mysql_fetch_array($result)) {
				// Calculate percentage of total activity
					
				$pc = round($row['num'] / ($max / 100));
				
				array_push($data,array(GetUserName($row['userid']),$row['num'],$pc . "%"));
			}

			$pdf->FancyTable3col($header,$data);
			$pdf->Ln(8);
			unset($data);
			$pdf->AddPage();
/*
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]uselog WHERE user<>''";
			$result = mcq($sql,$db);
			$result = mysql_fetch_array($result);
			$max = $result[0];

			print "<tr><td colspan=4><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Top20 " . $lang['users'] . " (" . $max ." use logs)</legend>&nbsp;</legend><table border=0>";
			print "</table><table border=1' cellspacing='0' cellpadding='2' bordercolor='#CECECE' width='100%'>";
			print "<tr><td><b>" . $lang['owner'] ."/" . $lang['assignee'] . "<b></td><td><b>Activity</b></td><td><b>Activity %</b></td><td>Activity % bar</td></tr>";
			
			$sql = "select $GLOBALS[TBL_PREFIX]uselog.user,$GLOBALS[TBL_PREFIX]loginusers.id AS userid,COUNT(*) AS num from $GLOBALS[TBL_PREFIX]uselog,$GLOBALS[TBL_PREFIX]loginusers WHERE $GLOBALS[TBL_PREFIX]uselog.user=$GLOBALS[TBL_PREFIX]loginusers.name GROUP BY user HAVING COUNT(*) > 1 ORDER BY num DESC LIMIT 20;";
			$result = mcq($sql,$db);
			while ($row = mysql_fetch_array($result)) {
				// Calculate percentage of total activity
					
				$pc = round($row['num'] / ($max / 100));

				print "<tr><td>" . GetUserName($row['userid']) . "</td><td>" . $row['num'] . "</td><td>" . $pc . "%</td><td><img src='pixel.gif' width='" . $pc . "' height=10></td></tr>";
			}
			print "</table>";
*/

			// Top 20 most slow entities

			$pdf->SetLink($ad);
			$pdf->AddLink();

			$pdf->SetFont('Arial','',18);
			$pdf->Cell(0,10,"Top 20 slow " . strtolower($lang['entities']),0,1);
			$pdf->SetFont('Arial','',8);
			$pdf->Bookmark("Top 20 slow " . strtolower($lang['entities']));

			$data = array();
			$now = date('U');
//			$header=array('Entities','out of','total','%',$lang[owner] . "/" . $lang[assignee]);
			$header=array("EID",$lang['customer'],"Age",$lang['category']);
			
			$sql = "select eid,$GLOBALS[TBL_PREFIX]customer.custname AS customer,category AS cat,COUNT(*), (" . $now . "-openepoch) AS ep FROM $GLOBALS[TBL_PREFIX]entity,$GLOBALS[TBL_PREFIX]customer WHERE $GLOBALS[TBL_PREFIX]entity.CRMcustomer = $GLOBALS[TBL_PREFIX]customer.id AND deleted<>'y' AND (" . $now . "-openepoch)>0 AND openepoch<>'' GROUP BY ep ORDER BY ep DESC LIMIT 20";
			$result = mcq($sql,$db);
			while ($row = mysql_fetch_array($result)) {
				// Calculate age
				$age_in_seconds = $row['ep'];

				if ($age_in_seconds>86400) {
					$age = round($age_in_seconds/86400,2) . " days";
				} elseif ($age_in_seconds>3600) {
					$age = round($age_in_seconds/3600,2) . " hours";
				} elseif ($age_in_seconds>60) {
					$age = round($age_in_seconds/60,2) . " minutes";
				} elseif ($age_in_seconds<>$nowepoch) {
					$age = round($age_in_seconds,0) . " seconds";
				} 
				array_push($data,array($row['eid'],$row['customer'],$age,$row['cat'],));
			}
			
			$pdf->FancyTable4colWide($header,$data);
			$pdf->Ln(8);
			unset($data);
			
			// Top 20 most slow DELETED entities
			$pdf->AddPage();
			$pdf->SetLink($ae);
			$pdf->AddLink();
			$pdf->SetFont('Arial','',18);
			$pdf->Cell(0,10,"Top 20 slow " . strtolower($lang['deleted']) . " " . strtolower($lang['entities']),0,1);
			$pdf->SetFont('Arial','',8);
			$pdf->Bookmark("Top 20 slow " . strtolower($lang['deleted']) . " " . strtolower($lang['entities']));
			$data = array();
			$now = date('U');
//			$header=array('Entities','out of','total','%',$lang[owner] . "/" . $lang[assignee]);
			$header=array("EID",$lang['customer'],"Duration",$lang['category']);
			
			$sql = "select eid,category AS cat,$GLOBALS[TBL_PREFIX]customer.custname AS customer,COUNT(*), (closeepoch-openepoch) AS ep FROM $GLOBALS[TBL_PREFIX]entity, $GLOBALS[TBL_PREFIX]customer WHERE $GLOBALS[TBL_PREFIX]entity.CRMcustomer = $GLOBALS[TBL_PREFIX]customer.id AND deleted='y' AND (closeepoch-openepoch)>0 GROUP BY ep ORDER BY ep DESC LIMIT 20";
			$result = mcq($sql,$db);
			while ($row = mysql_fetch_array($result)) {
				// Calculate age
				$age_in_seconds = $row['ep'];

				if ($age_in_seconds>86400) {
					$age = round($age_in_seconds/86400,2) . " days";
				} elseif ($age_in_seconds>3600) {
					$age = round($age_in_seconds/3600,2) . " hours";
				} elseif ($age_in_seconds>60) {
					$age = round($age_in_seconds/60,2) . " minutes";
				} elseif ($age_in_seconds<>$nowepoch) {
					$age = round($age_in_seconds,0) . " seconds";
				} 
				array_push($data,array($row['eid'],$row['customer'],$age,$row['cat'],));
			}
			
			$pdf->FancyTable4colWide($header,$data);
			$pdf->Ln(8);
			unset($data);


$pdf->Output(); // print the report!

$GLOBALS['CURFUNC'] = "ManSumPDF::";
qlog("PDF Report (management summary) presented.");

exit;

?>
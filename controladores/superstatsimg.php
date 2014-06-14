<?php

require_once("config.inc.php");
require_once("getset.php");
require_once("language.php");

NeedJP();



if (!$_REQUEST['year']) {
	$year = date("Y");
} else {
	$year = $_REQUEST['year'];
}

if ($_REQUEST['type'] == "wp") {
	MonthPlot($year);
} elseif ($_REQUEST['type'] == "psp") {
	PieStatusPlot();
} elseif ($_REQUEST['type'] == "pspo") {
	PieStatusPlot(true);
} elseif ($_REQUEST['type'] == "apu") {
	ActivityPerUser();
} elseif ($_REQUEST['type'] == "epc") {
	EntitiesPerCustomer();
} elseif ($_REQUEST['type'] == "apw") {
	ActivityPerWeek($year);
} elseif ($_REQUEST['type'] == "mp") {
	YearPlot($year);
}

function YearPlot($year) {
	global $lang, $filename;
	$data1y = array();
	$data2y = array();
	for ($x=2;$x<14;$x++) {
		$month = date("F", @mktime (0,0,0,$x,0,0));
		$sqlcounter++;
		// WHERE status='open'
		$sql = "SELECT openepoch, closeepoch FROM $GLOBALS[TBL_PREFIX]entity";
		if ($debug) { print $sql; }
		$result= mcq($sql,$db);
			while ($e= mysql_fetch_array($result)) {
			
				$c_week = date("W", $e['openepoch']);
				$c_month = date("F", $e['openepoch']);
				$c_year = date("Y", $e['openepoch']);
				$t_week = date("W");
				$t_month = date("m");
				if ($c_month == $month && $c_year == $year) {
					$thismonth++;
					//print $e['eid'] . "<BR><BR>";
				}
			
				if ($e['closeepoch']<>"") {
					$c_week = date("W", $e['closeepoch']);
					$c_month = date("F", $e['closeepoch']);
					$c_year = date("Y", $e['closeepoch']);
					$t_week = date("W");
					$t_month = date("m");
					if ($c_month == $month && $c_year == $year) {
						$thismonth2++;
						//print "NEXT nbla $c_month == $month && $c_year == $year";
					}
				}
			
					
		}

		if (!$thismonth) { $thismonth=0; }
		if (!$thismonth2) { $thismonth2=0; }

		array_push($data1y,$thismonth);
		array_push($data2y,$thismonth2);
		unset($thismonth);
		unset($thismonth2);

	}

	/*
	print "<pre>";
	print "openend:\n";
	print_r($ydata);
	print "closed:\n";
	print_r($y2data);
	exit;
	*/

	$datax=array("Jan","Feb","Mar","Apr","Maj","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

	
	// Create the graph. These two calls are always required
	$graph = new Graph(400,200,"auto");	
	$graph->SetScale("textlin");


	// Use built in font
	$graph->title->SetFont(FF_FONT1,FS_BOLD);

	// Slightly adjust the legend from it's default position in the
	// top right corner. 
	$graph->legend->Pos(0.05,0.5,"right","center");
	$graph->SetShadow();
	$graph->img->SetMargin(40,30,20,40);

	// Create the bar plots
	$b1plot = new BarPlot($data1y);
	$b1plot->SetFillColor("orange");
	$b2plot = new BarPlot($data2y);
	$b2plot->SetFillColor("blue");
	$b1plot->SetLegend("Added");
	$b2plot->SetLegend("Deleted");
	$b1plot->SetFillGradient('navy','orange',GRAD_RAISED_PANEL);
	$b2plot->SetFillGradient('navy','blue',GRAD_RAISED_PANEL);
	// Create the grouped bar plot
	$gbplot = new GroupBarPlot(array($b1plot,$b2plot));

	// ...and add it to the graPH
	$graph->Add($gbplot);

	$graph->title->Set($lang['entities'] . " added/deleted per month (". $year . ")");
	$graph->xaxis->title->Set("Month");
	$graph->yaxis->title->Set("# entitities");

	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

	// Display the graph
	$graph->Stroke($filename);

}

function MonthPlot($year) {
	global $lang, $filename;
	$data1y = array();
	$data2y = array();
	for ($weeknumber=0;$weeknumber<53;$weeknumber++) {

		$sql = "SELECT openepoch,closeepoch FROM $GLOBALS[TBL_PREFIX]entity";

		$result= mcq($sql,$db);
		while ($e= mysql_fetch_array($result)) {
				
				if ($e['openepoch']<>"") {
					$c_week = date("W", $e['openepoch']);
					$c_year = date("Y", $e['openepoch']);

					$t_week = date("W");

					if ($c_week == $weeknumber && $c_year == $year) {
						$thisweek++;
					} else {
						//print "$c_week == $weeknumber && $c_year == $year <br>";
					}

				}
	
						
					if ($e['closeepoch']<>"") {
						$c_week = date("W", $e['closeepoch']);
						$c_year = date("Y", $e['closeepoch']);

						$t_week = date("W");

						if ($c_week == $weeknumber && $c_year == $year) {
							$thisweek2++;
						} 
					}
			}


		if (!$thisweek2) { $thisweek2=0; }
		if (!$thisweek) { $thisweek=0; }		
	
		array_push($data1y,$thisweek);
		array_push($data2y,$thisweek2);
	
		unset($thisweek);
		unset($thisweek2);
		}
		// Create the graph. These two calls are always required
	$graph = new Graph(800,200,"auto");	
	$graph->SetScale("textlin");

	$graph->SetShadow();
	$graph->img->SetMargin(40,30,20,40);

	// Create the bar plots
	$b1plot = new BarPlot($data1y);
	$b1plot->SetFillColor("orange");
	$b2plot = new BarPlot($data2y);
	$b2plot->SetFillColor("blue");
	$b1plot->SetLegend("Added");
	$b2plot->SetLegend("Deleted");

	// Create the grouped bar plot
	$gbplot = new GroupBarPlot(array($b1plot,$b2plot));

	// ...and add it to the graPH
	$graph->Add($gbplot);

	$graph->title->Set($lang['entities'] . " added/deleted per week (". $year . ")");
	$graph->xaxis->title->Set("Week");
	$graph->yaxis->title->Set("# entitities");

	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

	// Display the graph
	$graph->Stroke($filename);
}


function PieStatusPlot($openonly=false) {
	global $lang,$filename;
	$legend = array();
	$data = array();

	if ($openonly) {
		$sql_ins = " AND deleted<>'y'";
		$text_ins = " (non-deleted entities)";
	} else {
		$text_ins = " (all entities)";
	}

	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]statusvars ORDER BY varname";
	$result= mcq($sql,$db);
    while ($e= mysql_fetch_array($result)) {
		
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE status='$e[varname]'" . $sql_ins;
			$result1= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($result1);

			$bla = $maxU1[0];
			
			array_push($data, $maxU1[0]);
			array_push($legend, $e['varname']);
		
	}

//	$data = array(100,100,100,100);

	$graph = new PieGraph(800,500,"auto");
	$graph->SetShadow();

	$graph->title->Set("Status per entity " . $text_ins);
	$graph->title->SetFont(FF_FONT1,FS_BOLD);

	$p1 = new PiePlot3D($data);
	$p1->SetAngle(20);
	$p1->SetSize(0.5);
	$p1->SetCenter(0.45);
	$p1->SetLegends($legend);

	$graph->Add($p1);
	$graph->Stroke($filename);
}
function ActivityPerUser() {
	global $lang,$filename;
	$legend = array();
	$data = array();

	if ($openonly) {
		$sql_ins = " AND deleted<>'y'";
		$text_ins = " (non-deleted entities)";
	} else {
		$text_ins = " (all entities)";
	}
	$sql = "select $GLOBALS[TBL_PREFIX]journal.user,$GLOBALS[TBL_PREFIX]loginusers.id AS userid,COUNT(*) AS num from $GLOBALS[TBL_PREFIX]journal,$GLOBALS[TBL_PREFIX]loginusers WHERE $GLOBALS[TBL_PREFIX]journal.user=$GLOBALS[TBL_PREFIX]loginusers.id GROUP BY user HAVING COUNT(*) > 1 ORDER BY num DESC LIMIT 20;";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
			array_push($data, $row['num']);
			array_push($legend, GetUserName($row['userid']));
	}
			
//			array_push($data, $maxU1[0]);
//			array_push($legend, $e['varname']);
		


//	$data = array(100,100,100,100);

	$graph = new PieGraph(800,500,"auto");
	$graph->SetShadow();

	$graph->title->Set("Activity per user");
	$graph->title->SetFont(FF_FONT1,FS_BOLD);

	$p1 = new PiePlot3D($data);
	$p1->SetAngle(20);
	$p1->SetSize(0.5);
	$p1->SetCenter(0.45);
	$p1->SetLegends($legend);

	$graph->Add($p1);
	$graph->Stroke($filename);
}
function EntitiesPerCustomer() {
	global $lang,$filename;
	$legend = array();
	$data = array();

	if ($openonly) {
		$sql_ins = " AND deleted<>'y'";
		$text_ins = " (non-deleted entities)";
	} else {
		$text_ins = " (all entities)";
	}
	$sql = "SELECT id,custname FROM $GLOBALS[TBL_PREFIX]customer ORDER BY custname";
	$result= mcq($sql,$db);
	
	while ($id = mysql_fetch_array($result)) {
		$auth = CheckCustomerAccess($id['id']);
		if ($auth == "ok" || $auth == "readonly") {

			$sqlcounter++;
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE CRMcustomer='$id[id]'";
			$resul1t= mcq($sql,$db);
			$maxU1 = mysql_fetch_array($resul1t);
			$open = $maxU1[0];

			array_push($data, $open);
			array_push($legend, $id[custname]);
		}
		//print "<tr><td><a href='detailedcustomerstats.php?cid=" . $id['id'] . "' class='bigsort'>" . $id[custname] . "</a></td><td>$open</td><td>$prD[0]</td><td>$pr[0]</td><td>$apc%</td><td width='100'><img src='pixel.gif' width='$apc' height=10></td></tr>\n";

	}
			
		


//	$data = array(100,100,100,100);

	$graph = new PieGraph(800,500,"auto");
	$graph->SetShadow();

	$graph->title->Set($lang['entities'] . " per " . $lang['customer']);
	$graph->title->SetFont(FF_FONT1,FS_BOLD);

	$p1 = new PiePlot3D($data);
	$p1->SetAngle(20);
	$p1->SetSize(0.5);
	$p1->SetCenter(0.45);
	$p1->SetLegends($legend);

	$graph->Add($p1);
	$graph->Stroke($filename);
}
?>

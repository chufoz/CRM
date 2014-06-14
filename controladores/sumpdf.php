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
// Summary using PDF


if (!$include) {

		//include("pdf_inc.php");
		require_once("config.inc.php");
		require_once("getset.php");
		require_once("language.php");

} // end if ! $include

if (strtoupper($BlockAllCSVDownloads)=="YES") {
		MustBeAdminUser();
		qlog("Access denied");
} else {
		qlog("Access granted");
}

$pdf_filename = "CRM-CTT_PDF_Entity_Summary_" . $_REQUEST['pdf'] . "_.pdf";


//header("Content-Type: Application/pdf");
//header("Content-Disposition: attachment; filename=$filename" );
//header("Content-Description: CRM Generated Data" );
//header("Window-target: _top");

$GLOBALS['CURFUNC'] = "SumPDF::";

//$pdf_title = $pdf_title . " $lang[entsum]";
$date = date("F j, Y, H:i") . "h";
$pdf_title2 = $pdf_title;
//$pdf_title = "$pdf_title $lang[entsum] - $CRM_VERSION. $date";
$pdf_title = "crm-ctt $lang[entsum]";
$pdf_title_link = "http://www.crm-ctt.com";
require_once("pdf_inc2.php");
$tc=1;


$a = GetClearanceLevel($GLOBALS[USERID]);

$GLOBALS['CURFUNC'] = "SumPDF::";

// $pdf should contain a comma separated list of all to entities to be printed

if (!$include) {
	uselogger("PDF Summary downloaded","");
	$GLOBALS['CURFUNC'] = "SumPDF::";
	qlog("PDF Summary requested");
}

if ($enc) {
	$pdf = base64_decode($pdf);
}

if ($stashid) {
	$pdf = PopStashValue($stashid);
}

qlog("PDF is $pdf");

if ($file) {
	unset($pdf);
	$file = base64_decode($file);
	$fp = fopen($file,"r");
	while (!feof($fp)) {
		$pdf .= fread($fp,filesize($file));
	}
	fclose($fp);
	unlink($file);
	qlog("PDF is $pdf");
}



if (!$include) {
	
	$pdfa = split(",",$pdf);
	sort($pdfa);
	if ($print) {
		$NoImageInclude=1;
		StartPrintPDF();
	} else {
		StartPDF();	
	}
//	
	$pdfa2 = array();

	foreach($pdfa AS $element) {
		if (CheckEntityAccess($element) <> "nok") {
			array_push($pdfa2,$element);
		} else {
			qlog("Access to entity $element was denied in PDF export");
		}
	}

	// Print table of contents when there is more than one summary to report
	if (sizeof($pdfa2)>1) {
		toc($pdfa2);
	}
}

if (!$include) {
	CreatePDF($pdfa2);
}

// Useless, only for logging:
ClearCacheArrays();
?>
<?
/* ********************************************************************
 * $GLOBALS[TBL_PREFIX] 
 * Copyright (c) 2001-2003 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This script checks the repository currently logged onto for errors and
 * inconsistencies, and optimizes all tables.
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

$fp = fopen("qlist.txt","r");
//while (!feof($fp)) {
//	$line = fgets($fp,1);
//	$num++;
//}
//fclose($fp);
//$fp = fopen("qlist.txt","r");

$cache = 0;
$pages = 0;
$users = array();
$titles = array();
$queries = array();

print " Please wait ... \n\n";
while (!feof($fp)) {
	$lnum++;
	if ($lnum == ($lastnum+100)) {
		print "\015 " . $lnum . "/" . $num;
		$lastnum = $lnum;
	}
	$line = fgets($fp,1024);
	$linarr = explode(" ", $line);
	if (!$first) {
		if (is_numeric($linarr[0])) {
			$a = strftime("%Y-%m-%d %H:%M:%S", $linarr[0]);
			print " Start time : " . $a . "\n";
			$first = true;
		}	
	} elseif (is_numeric($linarr[0])) {
			if (!is_numeric($linarr[1]) && !$lastwasnewline) {
		//		print $linarr[1] . "\n";
				$user = $linarr[1];
				$users[$user]++;
			}
		
			$last = strftime("%Y-%m-%d %H:%M:%S", $linarr[0]);
	}
	if ($lastwasnewline) {
		$title = split("s ", $line);
		$title = $title[1];
		$title = substr($title,1,strlen($title)-1);
		$title = substr($title,0,strlen($title)-2);
		$titles[$title]++;
	}

	if (strstr($line,"CACHE")) {
		$cache++;
	}
	if (strstr($line,"RSS")) {
		$RSS++;
	}
	if (strstr($line,"CONCERNING QUERY")) {
		$bla = explode(":   CONCERNING QUERY    : ",$line);
		$queries[$bla[1]]++;
	}
	unset($lastwasnewline);
	if (strstr($line,"=============================================================================")) {
		$pages++;
		$lastwasnewline = true;
	}
	$lastline = $line;
	unset($linarr);
}
print "\n Pages      : " . number_format($pages) . "\n";
print " Cache hits : " . number_format($cache) . "\n";
print " RSS hits   : " . number_format($RSS) . "\n\n";

//sort($users);

foreach($users AS $username => $usertimes) {
	if ($usertimes>40) {
		print " " . fillout($username,30) . " - " . number_format($usertimes) . " hits\n";
	}
}
print "\n";
foreach($titles AS $title => $titletimes) {
	if ($titletimes > 4) {
		print " " . fillout($title,30) . " - " . number_format($titletimes) . " pages\n";
	}
}
print "\n";
foreach($queries AS $query => $querytimes) {
		if ($querytimes > $maxq) {
			$maxq = $querytimes;
		}
}
print "Query most tagged as slow:";
foreach($queries AS $query => $querytimes) {
	if ($querytimes > ($maxq-10)) {
		print " " . fillout($query,30) . " - " . number_format($querytimes) . " queries\n";
	}
}
print "\n Last hit   : " . $last . "\n";
print " Total lines parsed: " . $lnum . "\n";

//=============================================================================
//index.php 19-11-2005 17:05:29s (Demo (with WebDAV))
//1125003223 NOUSER :  GetClearanceLevel:: No profile override...
//1125003223 NOUSER :  GetClearanceLevel:: GetClearanceLevel 1 administrator administrator
//1125003223 hidde :  GetClearanceLevel:: hit 8 CACHE GetClearanceLevel 1  administrator
//1125003223 hidde :   hit 9 CACHE This entity is NOT locked

function fillout($var,$len) {
		while (strlen($var)<$len) {
				$var = $var . " ";
		}
		if ($var=="____0") {
			$var="_____";
		}
	return $var;
}

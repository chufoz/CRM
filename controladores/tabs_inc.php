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
function tabs($sections, $pages, $navid){

	global $phpAds_TextDirection;
	$phpAds_TextAlignRight="right";
	$phpAds_TextAlignLeft="left";

	echo "<table border='0' cellpadding='0' cellspacing='0' width='100%' background='stab-bg.gif'><tr height='24'>";
	echo "<td width='40'><img src='stab-bg.gif' width='40' height='24'></td><td width='600' align='".$phpAds_TextAlignLeft."'>";
	echo "<table border='0' cellpadding='0' cellspacing='0'><tr height='24'>";
	// Prepare Navigation
	//echo $pages	= $phpAds_nav;
	echo "<td></td>";

	for ($i=0; $i<count($sections);$i++)
	{
		list($sectionUrl, $sectionStr) = each($pages["$sections[$i]"]);
		

		$tmpnav = $navid - 20;
		$navid2 = $tmpnav+20;
		$navid3 = "$tmpnav+20";

		unset($tmpnav);

		if ($navid2 == $sections[$i] || $navid3 == $sections[$i]) {
			$selected = true;
		} else {
			unset($selected);
		}
		

		if ($selected)
		{
			echo "<td background='stab-sb.gif' valign='middle' nowrap>";
			
			if ($i > 0) 
				echo "<img src='stab-mus.gif' align='absmiddle'></td>";
			else
				echo "<img src='stab-bs.gif' align='absmiddle'></td>";
			
			echo "<td class='tab-s' background='stab-sb.gif' valign='middle' nowrap>";
			echo "&nbsp;&nbsp; <a href='".$sectionUrl."'><font class='tabsbarSelected'>".$sectionStr."</font></a></td>";
		}
		else
		{
			echo "<td background='stab-ub.gif' valign='middle' nowrap>";
			
			if ($i > 0) 
				if ($previousselected) 
					echo "<img src='stab-msu.gif' align='absmiddle'></td>";
				else
					echo "<img src='stab-muu.gif' align='absmiddle'></td>";
			else
				echo "<img src='stab-bu.gif' align='absmiddle'></td>";
			
			echo "<td background='stab-ub.gif' valign='middle' nowrap>";
			echo "&nbsp;&nbsp;<a class='bigsort' href='".$sectionUrl."'><font class='tabsbar'>".$sectionStr."</font></a></td>";
		}
		
		$previousselected = $selected;
	}
	
	if ($previousselected){
		echo "<td><img src='stab-es.gif'></td>";
	}
	else{
		echo "<td><img src='stab-eu.gif'></td><td bgimage='stab-bg.gif'></td>";
	}
	echo "</tr></table>";
	echo "</td><td>&nbsp;</td></tr></table>";
	echo "<table border='0' cellspacing='0' cellpadding='0' class='dialog'>";
	echo "<tr><td width='40'>&nbsp;</td><td><br>";
}

?>
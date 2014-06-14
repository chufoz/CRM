<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This file exports all contents of CRM-CTT to disk
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
extract($_REQUEST);
$EnableRepositorySwitcherOverrule="n";

if ($wait) {
			// This is the code for the search-window with the animated gif
			// in it which appears only when searching for random text strings
			// when the SearchAttachments directive is set to 'Yes'
			?>
			<HTML>
			<TITLE>Exporting........................................................</TITLE>
			<BODY>
			<table width=100%><tr><td><center><font face='MS Shell Dlg' size='2'>
			
			<?
				echo base64_decode($opm);
				?>
			<br><br>
			<img src='movingbar.gif'><br><br>
			Currently processing <? echo $start;?> - <? echo ($start+100);?> of <? echo $end;?><br>
			</center></td></tr></table>
			</BODY></HTML>
			<?
				exit;
}
if ($start) {
//	$nonavbar=1;
}
include("header.inc.php");
	print "</td></tr></table>";
	AdminTabs();
	MainAdminTabs("ieb");
$include=1;
include("sumpdf.php");

print "</table><table border=0 width='65%'><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size=+1>$lang[adm]</font>&nbsp;</legend>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size=+2>" . $title . "</font><table border=0 width='100%'>";

MustBeAdmin();

		print "<table width='100%'><tr><td>";
		print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp; Dump all CRM-CTT content to disk&nbsp;</legend>";
		print "<table width='100%'><tr><td>";

if (!$step) {
		print "<br>This procedure will export <b>all</b> entities (including files and customer information except for customer attachments) to a file/directory structure on the disk on the server.<br><br>";
		print "To be able to do this, you must create a directory called 'export' in your CRM-CTT installation directory, and make sure your webserver has permission to write in this directory. <br><Br>";
		print "Click <img src='arrow.gif'>&nbsp;<a href='dump_to_disk.php?step=1' class='bigsort'>here</a> when you've done so.<br>";

} elseif ($step==1) {
		
		if (is_writeable("export/")) {

			print "Directory export/ found and is writeable.<br><br>";
			print "Click <img src='arrow.gif'>&nbsp;<a href='dump_to_disk.php?step=2' class='bigsort'>here</a> to start the export.<br>";
			print "<br><b>CRM-CTT will export in batches; you will see the page refreshing several<br>times, depending on the size of your repository. This behaviour is normal.</b><br>";
	
		} else {
			
			print "<img src='error.gif'>&nbsp;Directory export/ is not writeable, or totally not there. Click <img src='arrow.gif'>&nbsp;<a href='dump_to_disk.php?step=1' class='bigsort'>here</a> to try again.<br>";

		}



} elseif ($step==2) {
		
		print "<pre>";
		print "Creating directory structure...\015\012";
		
		@mkdir("export/" . $repository_nr);

		$eids = array();

		$sql="SELECT DISTINCT(eid) FROM $GLOBALS[TBL_PREFIX]entity ";
		$output = mcq($sql,$db);
		while ($row = mysql_fetch_array($output)) {

			array_push($eids,$row['eid']);
			
			if (!is_dir(getcwd() . "/export/" . $repository_nr . "/" . $row['eid'])) {
				@mkdir(getcwd() . "/export/" . $repository_nr . "/" . $row['eid']);
				$num++;
			}
		}
	
		print "$num directories created.<br><br>";
		print "\015\012\015\012</pre>Click <img src='arrow.gif'>&nbsp;<a href='dump_to_disk.php?step=3&GeenPlaatjes=1' class='bigsort'>here</a> to continue <br>";
		//print "\015\012\015\012</pre>Click <img src='arrow.gif'>&nbsp;<a href='dump_to_disk.php?step=3' class='bigsort'>here</a> to continue and generate PDF summaries <b>with</b> activity images (much, much slower)<br><br><br>(this is the last question)<br>";



}  elseif ($step==3) {
		$eids_total = array();
		$eids = array();

		$sql="SELECT DISTINCT(eid) FROM $GLOBALS[TBL_PREFIX]entity";
		$output = mcq($sql,$db);
		while ($row = mysql_fetch_array($output)) {
			array_push($eids_total,$row['eid']);
		}

		$max = sizeof($eids_total);
	
		if (!$start) {
			$start = 0;
		}

		$opm = base64_encode("Generating PDF summaries ...");
		
		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
			statusWin = window.open('dump_to_disk.php?wait=1&start=<? echo $start;?>&end=<? echo $max;?>&opm=<? echo $opm;?>', 'statusWin' ,'width=400,height=130,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
		//-->
		</SCRIPT>
		
		<?
		
		

		print "\015\012\015\012Dumping entity contents in PDF format. This will take quite some time.";
		
		$subdir = ereg_replace("dump_to_disk.php","",$_SERVER['SCRIPT_NAME']);

		$sql="SELECT DISTINCT(eid) FROM $GLOBALS[TBL_PREFIX]entity LIMIT " . $start .",100";
		$output = mcq($sql,$db);
		while ($row = mysql_fetch_array($output)) {
			array_push($eids,$row['eid']);
		}

		foreach($eids as $eid) {
			$file = "export/" . $repository_nr . "/" . $eid . "/EntityContents-" . $eid . ".pdf";
			StartPDF();
			$NoImageInclude=$GeenPlaatjes;
			cat_sum($eid);
			$date = date("F j, Y, H:i") . "h";
			$pdf->Output($file);
		}

		print "</pre>";

		$newstart = $start + 100;
	
		if ($start<sizeof($eids_total)) {
			?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				document.location='dump_to_disk.php?step=3&GeenPlaatjes=<? echo $GeenPlaatjes;?>&start=<? echo $newstart;?>';
			//-->
			</SCRIPT>
			<?
		} else {
			?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				document.location='dump_to_disk.php?step=4';
			//-->
			</SCRIPT>
			<?
		}


} elseif ($step==4) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
		statusWin = window.open('dump_to_disk.php?wait=1', 'statusWin' ,'width=400,height=130,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
		statusWin.close();
		//-->
	</SCRIPT>
	<?
	
	print "Done creating PDF summaries.<br><br>";
	print "Click <img src='arrow.gif'>&nbsp;<a href='dump_to_disk.php?step=5' class='bigsort'>here</a> to start the file export.<br>";
	?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				document.location='dump_to_disk.php?step=5';
			//-->
			</SCRIPT>
			<?
	
} elseif ($step==5) {
		
		$eids_total = array();
		$eids = array();

		$sql="SELECT DISTINCT(eid) FROM $GLOBALS[TBL_PREFIX]entity";
		$output = mcq($sql,$db);
		while ($row = mysql_fetch_array($output)) {
			array_push($eids_total,$row['eid']);
		}

		$max = sizeof($eids_total);
		if (!$start) {
			$start = 0;
		}

		$opm = base64_encode("Copying files from database to disk ...");
		

		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
			statusWin = window.open('dump_to_disk.php?wait=1&start=<? echo $start;?>&end=<? echo $max;?>&opm=<? echo $opm;?>', 'statusWin' ,'width=400,height=130,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
		//-->
		</SCRIPT>
		
		<?
		

		$sql="SELECT DISTINCT(eid) FROM $GLOBALS[TBL_PREFIX]entity LIMIT " . $start .",100";
		$output = mcq($sql,$db);
		while ($row = mysql_fetch_array($output)) {
			array_push($eids,$row['eid']);
		}

		print "<pre>";

		foreach($eids as $eid) {
			$sql = "SELECT filename, content FROM $GLOBALS[TBL_PREFIX]binfiles,$GLOBALS[TBL_PREFIX]blobs WHERE $GLOBALS[TBL_PREFIX]binfiles.fileid=$GLOBALS[TBL_PREFIX]blobs.fileid AND koppelid='" . $eid . "' AND type='entity'";
			$result = mcq($sql,$db);
			print "Check $eid\015\012";
			while($row = mysql_fetch_array($result)) {
				$fp = fopen("export/" . $repository_nr . "/" . $eid . "/" . $row['fileid'] . "-" . $row['filename'],"w");
				fputs($fp,$row['content']);
				fclose($fp);
			}

		}

		print "</pre>";

		$newstart = $start + 100;
	
		if ($start<sizeof($eids_total)) {
			?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				document.location='dump_to_disk.php?step=5&start=<? echo $newstart;?>';
			//-->
			</SCRIPT>
			<?
		} else {
			?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				document.location='dump_to_disk.php?step=6';
			//-->
			</SCRIPT>
			<?
		}


		

} elseif ($step==6) {
		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
			statusWin = window.open('dump_to_disk.php?wait=1', 'statusWin' ,'width=400,height=130,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
			statusWin.close();
			//-->
		</SCRIPT>
		<?
		print "Done exporting files.<br><br>";
		print "Click <img src='arrow.gif'>&nbsp;<a href='dump_to_disk.php?step=7' class='bigsort'>here</a> to create the root index file.<br>";
		?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				document.location='dump_to_disk.php?step=7';
			//-->
			</SCRIPT>
			<?

} elseif ($step==7) {

		$date = date("F j, Y, H:i") . "h";
		
		$txtfile = "CRM-CTT Directory index created $date\015\012\015\012";

		$sql="SELECT eid,category,owner,assignee FROM $GLOBALS[TBL_PREFIX]entity ORDER BY eid";
		$output = mcq($sql,$db);
		while ($row = mysql_fetch_array($output)) {
			$txtfile .= $row['eid'] . "/ - " . GetUsername($row['owner']) . " - " . GetUserName($row['assignee']) . " - " . $row['category'] . "\015\012";
		}	
		$txtfile .= "\015\012 End of root index file\015\012";

		$fp = fopen("export/" . $repository_nr . "/index.txt","w");
		fputs($fp,$txtfile);
		fclose($fp);

		print "Done creating index file.<br><br>";
		print "Click <img src='arrow.gif'>&nbsp;<a href='dump_to_disk.php?step=8' class='bigsort'>here</a> to create the customer information.<br>";
		?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				document.location='dump_to_disk.php?step=8';
			//-->
			</SCRIPT>
			<?

} elseif ($step==8) {

		$date = date("F j, Y, H:i") . "h";
		
		print "<pre>";

		$sql="SELECT eid,CRMcustomer FROM $GLOBALS[TBL_PREFIX]entity ORDER BY eid";
		$output = mcq($sql,$db);
		while ($row = mysql_fetch_array($output)) {
			$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]customer WHERE id=" . $row['CRMcustomer'];
			$result= mcq($sql,$db);
			$pb= mysql_fetch_array($result);
			$txtfile = "CRM-CTT " . $lang['customer'] . " info $date\015\012\015\012";
			$txtfile .= $lang[customer] . " : " . $pb[custname] . "\015\012";
			$txtfile .= $lang[contact] . " : " . $pb[contact] . "\015\012";
			$txtfile .= $lang[contacttitle] . " : " . $pb[contact_title] . "\015\012";
			$txtfile .= $lang[contactphone] . " : " . $pb[contact_phone] . "\015\012";
			$txtfile .= $lang[contactemail] . " : " . $pb[contact_email] . "\015\012";
			$txtfile .= $lang[customeraddress] . " : " . ereg_replace("\n","\015\012",$pb[cust_address])  . "\015\012";

			$txtfile .= "\015\012";
			$txtfile .= "\015\012 End of " . $lang['customer'] . " file\015\012";

			$fp = fopen("export/" . $repository_nr . "/" . $row['eid'] . "/customer.txt","w");
			//print "export/" . $repository_nr . "/" . $row['eid'] . "/customer.txt\n";
			fputs($fp,$txtfile);
			fclose($fp);
			unset($txtfile);
			$num++;
		}	
		
		?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				document.location='dump_to_disk.php?step=9';
			//-->
			</SCRIPT>
			<?
		
} elseif ($step==9) {

		// dump customer data

		$date = date("F j, Y, H:i") . "h";
		
		print "<pre>";

		$sql="SELECT id FROM $GLOBALS[TBL_PREFIX]customer ORDER BY id";
		$output = mcq($sql,$db);
		while ($row = mysql_fetch_array($output)) {
			
			$path = "export/" . $repository_nr . "/customer-" . $row['id']; 
			mkdir($path); 
			$filename = $path . "/CRM-CTT-CustomerExport-" . $row['id'] . ".pdf";
			
			$stashid = PushStashValue("SELECT * FROM $GLOBALS[TBL_PREFIX]customer WHERE id=" . $row['id']);
			
			$custpdf++;

			?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				statusWin = window.open('customers.php?pdf=1&stashid=<? echo $stashid;?>&to_file=<? echo $filename;?>', 'statusWin' ,'width=400,height=130,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
				//-->
			</SCRIPT>
			<?
			
			$sql= "SELECT filename,content FROM $GLOBALS[TBL_PREFIX]binfiles,$GLOBALS[TBL_PREFIX]blobs WHERE $GLOBALS[TBL_PREFIX]binfiles.fileid=$GLOBALS[TBL_PREFIX]blobs.fileid AND koppelid='" . $row['id'] . "' AND type='cust' ORDER BY filename,creation_date";
			$result= mcq($sql,$db);

			while ($row2 = mysql_fetch_array($result)) {
				$fp = fopen($path . "/" . $row2['fileid'] . "-" . $row2['filename'],"w");
				fputs($fp,$row2['content']);
				fclose($fp);	
				$custfiles++;
			}
		
		}	
		
		?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				statusWin = window.open('index.php', 'statusWin' ,'width=400,height=130,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
				statusWin.close();
				
				document.location='dump_to_disk.php?step=10&custpdf=<? echo $custpdf;?>&custfiles=<? echo $custfiles;?>';
			//-->
			</SCRIPT>
			<?
		

} elseif ($step==10) {

		// try to rename the directories to more readable names
		print "<pre>";
		$sql="SELECT eid,category FROM $GLOBALS[TBL_PREFIX]entity ORDER BY eid";
		$output = mcq($sql,$db);
		while ($row = mysql_fetch_array($output)) {
			
			$cat = $row['category'];
			$cat = eregi_replace("\n","",$cat);
			$cat = eregi_replace("'","",$cat);
			$cat = eregi_replace("\"","",$cat);
			$cat = eregi_replace("&","",$cat);
			$cat = eregi_replace("~","",$cat);
			$cat = eregi_replace("`","",$cat);
			$cat = eregi_replace("#","",$cat);
			$cat = eregi_replace("$","",$cat);
			$cat = eregi_replace("%","",$cat);
			$cat = eregi_replace("^","",$cat);
			$cat = eregi_replace("\*","",$cat);
			$cat = eregi_replace(";","",$cat);
			$cat = eregi_replace(":","",$cat);
			$cat = eregi_replace("<","",$cat);
			$cat = eregi_replace(">","",$cat);
			$cat = eregi_replace("\?","",$cat);
			$cat = str_replace("/","",$cat);
			$cat = str_replace("\\","",$cat);
			$cat = str_replace(".","",$cat);
			$cat = eregi_replace("\|","",$cat);
			$cat = eregi_replace("{","",$cat);
			$cat = eregi_replace("}","",$cat);
			$cat = eregi_replace("\[","",$cat);
			$cat = eregi_replace("\]","",$cat);
			$cat = eregi_replace("=","",$cat);
			$cat = eregi_replace("\+","",$cat);

			@rename("export/" . $repository_nr . "/" . $row['eid'],"export/" . $repository_nr . "/" . $row['eid'] . " - " . $cat);
		}

		$sql="SELECT count(*) FROM $GLOBALS[TBL_PREFIX]entity";
		$output = mcq($sql,$db);
		$eids_total = array();
		$row = mysql_fetch_array($output);
		$max = $row[0];

		$sql="SELECT count(*) FROM $GLOBALS[TBL_PREFIX]binfiles";
		$output = mcq($sql,$db);
		$eids_total = array();
		$row = mysql_fetch_array($output);
		$maxfiles = $row[0];
		



		print "$max directories created\n";
		print "$max PDF files created\n";
		print "$maxfiles files copied from database\n";
		print "$max customer information files created\n";
		print "$custpdf customer PDF exports created\n";
		print "$custfiles customer attachments exported\n";
		print "1 root index file created\n";
		print "</pre>";
		print "CRM Export done. Please make sure to move the export for it is now world-readable.<br><br>";
}

print "<br><img src='arrow.gif'>&nbsp;<a class='bigsort' href='admin.php?password=$password' style='cursor:pointer'>Back to main administration page</a>";

print "</td></tr></table>";
print "</fieldset>";
print "</td></tr></table>";

print "</body></html>";
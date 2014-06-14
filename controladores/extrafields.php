<?php
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This file does several things :)
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

 // This script handles editing of extra fields

include("header.inc.php");
// Get rid of the crappy header leftovers
print "</td></tr></table>";
?>
 	<SCRIPT LANGUAGE="javascript" SRC="cookies.js"></SCRIPT>
<?

AdminTabs("ef");

$to_tabs = array("entity","customer","newentity","newcustomer");
$tabbs["main"] = array("admin.php" => "<b>Back</b>", "comment" => "bla");
$tabbs["entity"] = array("extrafields.php?tabletype=entity&ti=1" => "Extra entity fields", "comment" => "Extra fields currently configured in the entity table.");
$tabbs["customer"] = array("extrafields.php?tabletype=customer&ti=2" => "Extra " . strtolower($lang['customer']) . " fields", "comment" => "Extra fields currently configured in the customers table.");
$tabbs["newentity"] = array("extrafields.php?editextrafield=new&tabletype=entity&ti=3" => "New extra entity field", "comment" => "Add a new field to the entity table.");
$tabbs["newcustomer"] = array("extrafields.php?editextrafield=new&tabletype=customer&ti=4" => "New extra " . strtolower($lang['customer']) . " field", "comment" => "Add a new field to the customer table.");

if ($_REQUEST['ti'] == 1) {
	$navid = "entity";
} elseif ($_REQUEST['ti'] == 2) {
	$navid = "customer";
} elseif ($_REQUEST['ti'] == 3) {
	$navid = "newentity";
} else {
	$navid = "newcustomer";
}

if ($_REQUEST['editextrafield'] <> "new" && !$_REQUEST['ti']) {
	unset($navid);
}
InterTabs($to_tabs, $tabbs, $navid);



MustBeAdmin();
//print "<pre>";
//print_r($_REQUEST);
//print "</pre>";

$fieldtypes = array("drop-down","checkbox","drop-down based on customer list of values","textbox","text area","text area (rich text)","numeric","mail","hyperlink","invoice cost","invoice cost including VAT","invoice qty","comment","date","VAT drop-down","User-list of all CRM-CTT users","User-list of limited CRM-CTT users","User-list of administrative CRM-CTT users","List of values","Button");

$locations  = array("Middle box, left","Middle box, right","Under main text box, left","Under main text box, right");

print "<table width='100%'><tr><td>&nbsp;&nbsp;&nbsp;</td><td>";
print "<table width='90%'><tr><td>";
print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Edit Extra Fields (" . $_REQUEST['tabletype'] . ")</font></legend>";
print "<br>This is where you can add and edit extra fields in your repository.<br>";

if ($_REQUEST['submitted'] && $_REQUEST['tabletype']) {

	$arr = array();
	foreach ($_REQUEST['newoption'] AS $arrfield) {
		if ($arrfield <> "") {
			array_push($arr, $arrfield);
		}
	}
	
	if ($_REQUEST['newtype'] == "drop-down based on customer list of values") {
		$options = $_REQUEST['options'];
	} else {
		$options = serialize($arr);
	}

	if ($_REQUEST['newtype'] == "comment") {
		$_REQUEST['newname'] = $_REQUEST['newHTMLtemplate'];
	} elseif ($_REQUEST['newtype'] == "drop-down" || $_REQUEST['newtype'] == "VAT drop-down") {
		unset($option);
		if (sizeof($arr)==0) {
			$dontgo = true;
		} 
	}
	
	if ($_REQUEST['newtype'] == "text area") {
		$_REQUEST['options'] = $_REQUEST['boxsize1'] . ":" . $_REQUEST['boxsize2'] . ":" . $_REQUEST['ShowTimeDateInputClockThingy'];
		$options = $_REQUEST['options'];
	}
	if ($_REQUEST['newtype'] == "text area (rich text)") {
		$_REQUEST['options'] = $_REQUEST['boxsize3'] . ":" . $_REQUEST['boxsize4'] . ":" . $_REQUEST['ShowTimeDateInputClockThingy'];
		$options = $_REQUEST['options'];
	}



	if ($_REQUEST['newhidden'] == "") {
		$_REQUEST['newhidden'] = "n";
	} 

	if ($_REQUEST['newtype'] == "checkbox") {
		$_REQUEST['defaultval'] = $_REQUEST['cb_unchecked'];
		$options = $_REQUEST['cb_checked'];
	}

	
	if ($dontgo <> true) {
		if ($_REQUEST['editextrafield'] == "new") {
			$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]extrafields(options,name,ordering,fieldtype,location,hidden,tabletype,forcing,defaultval,sort,storetype,size) VALUES ('" . $options . "','" . $_REQUEST['newname'] . "','" . $_REQUEST['neworder'] . "','" . $_REQUEST['newtype'] . "','" . $_REQUEST['newlocation'] . "','" . $_REQUEST['newhidden'] . "','" . $_REQUEST['tabletype'] . "','" . $_REQUEST['forcing'] . "','" . addslashes($_REQUEST['defaultval']) . "','" . $_REQUEST['sortwhendisplayed'] . "','" . $_REQUEST['storetype'] . "','" . $_REQUEST['boxsize'] . "')";
			mcq($sql,$db);
			$new_field_name = mysql_insert_id();
			$sql = "UPDATE $GLOBALS[TBL_PREFIX]extrafields SET ordering='" . ($new_field_name*10*1.234) . "' WHERE id='" . $new_field_name . "'";
			mcq($sql,$db);

			if ($_REQUEST['defaultval']) {
				// A default value was added, so we have to create this field for all entities or customers. If we don't, stats will suffer.

				$id_ar = array();

				if ($_REQUEST['tabletype'] == "customer") {
					$sql = "SELECT DISTINCT(id) FROM $GLOBALS[TBL_PREFIX]customer";
				} elseif ($_REQUEST['tabletype'] == "entity") {
					$sql = "SELECT DISTINCT(eid) FROM $GLOBALS[TBL_PREFIX]entity";
				} else {
					qlog("ERROR: Impossible error. Extrafields, create, defaultval.");
				}
				$result= mcq($sql,$db);								
				while ($row=mysql_fetch_array($result)){
					array_push($id_ar,$row[0]);
				}

				if ($_REQUEST['tabletype'] == "customer") {
					$type = "cust";
				} else {
					$type = "";
				}

				foreach($id_ar AS $id) {
					$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]customaddons(eid,type,name,value,usr) VALUES('" . $id . "','" . $type . "','" .  $new_field_name . "','" . $_REQUEST['defaultval'] . "','" . $GLOBALS['USERID'] . "')";
					mcq($sql, $db);
					$qc++;
				}
				print "<br><b><font color='#FF0000'>" . $qc . " records with default values created.</font><br>";
				qlog("Created " . $qc . " records containing the default value.");
			}

		} else {
			$sql = "UPDATE $GLOBALS[TBL_PREFIX]extrafields SET options='" . $options . "', name='" . $_REQUEST['newname'] . "', ordering='" . $_REQUEST['neworder'] . "', fieldtype='" . $_REQUEST['newtype'] . "', location='" . $_REQUEST['newlocation'] . "', hidden='" . $_REQUEST['newhidden'] . "', forcing='" . $_REQUEST['forcing'] . "', defaultval='" . addslashes($_REQUEST['defaultval']) . "', sort='" . $_REQUEST['sortwhendisplayed'] . "', storetype='" . $_REQUEST['storetype'] . "', size='" . $_REQUEST['boxsize'] . "'  WHERE id='" . $_REQUEST['editextrafield'] . "'";
			mcq($sql,$db);
			
			
			// When nescessary, expire concerded form cache

			if ($_REQUEST['tabletype'] == "entity") {
				$sql = "SELECT " . $GLOBALS['TBL_PREFIX'] . "binfiles.fileid FROM " . $GLOBALS['TBL_PREFIX'] . "binfiles, " . $GLOBALS['TBL_PREFIX'] . "blobs WHERE " . $GLOBALS['TBL_PREFIX'] . "binfiles.fileid = " . $GLOBALS['TBL_PREFIX'] . "blobs.fileid AND filetype='TEMPLATE_HTML_FORM' AND (" . $GLOBALS['TBL_PREFIX'] . "blobs.content LIKE '%EFID" .  $_REQUEST['editextrafield'] . "%' OR " . $GLOBALS['TBL_PREFIX'] . "blobs.content LIKE '%ALL_EXTRA_FIELDS%')";
				$res = mcq($sql, $db);
				while ($row = mysql_fetch_array($res)) {
					ExpireFormCacheByForm($row[0]);
					print "<br>Form cache of form " . $row[0] . " expired.";
					qlog("Form cache of form " . $row[0] . " expired");
				}
				
			} else {
				qlog("Expiring form cache not needed.");
			}

			if ($_REQUEST['defaultval']) {
				// A default value was added, so we have to create this field for all entities or customers. If we don't, stats will suffer.

				$id_ar = array();

				if ($_REQUEST['tabletype'] == "customer") {
					$sql = "SELECT DISTINCT(id) FROM $GLOBALS[TBL_PREFIX]customer";
				} elseif ($_REQUEST['tabletype'] == "entity") {
					$sql = "SELECT DISTINCT(eid) FROM $GLOBALS[TBL_PREFIX]entity";
				} else {
					qlog("ERROR: Impossible error. Extrafields, create, defaultval.");
				}
				$result= mcq($sql,$db);								
				while ($row=mysql_fetch_array($result)){
					array_push($id_ar,$row[0]);
				}
			
			
				$sql = "SELECT eid FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name='" . $_REQUEST['editextrafield'] . "'";
				$result= mcq($sql,$db);	
				$id_ex = array();
				while ($row=mysql_fetch_array($result)){
					array_push($id_ex,$row[0]);
				}

				if ($_REQUEST['tabletype'] == "customer") {
					$type = "cust";
				} else {
					$type = "";
				}
				
				$qc = 0;

				foreach($id_ar AS $id) {
					if (!in_array($id,$id_ex)) {
						$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]customaddons(eid,type,name,value,usr) VALUES('" . $id . "','" . $type . "','" .  $_REQUEST['editextrafield'] . "','" . $_REQUEST['defaultval'] . "','" . $GLOBALS['USERID'] . "')";
						mcq($sql, $db);
						$qc++;
					}
				}
				print "<br><b><font color='#FF0000'>" . $qc . " records with default values created.</font><br>";
				qlog("Created " . $qc . " records containing the default value.");
			}


		}
	} else {
		?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				alert('A drop-down box must have options! Field is not saved.');
			//-->	
			</SCRIPT>
		<?
	}
		
	unset($_REQUEST['editextrafield']);

	if ($_REQUEST['req_url']) {
		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
			document.location='<? echo base64_decode($_REQUEST['req_url']);?>';
		//-->
		</SCRIPT>
		<?
	}

} elseif ($_REQUEST['delfield']) {
	
	$sql = "UPDATE $GLOBALS[TBL_PREFIX]extrafields SET deleted='y' WHERE id='" . $_REQUEST['delfield'] . "'";
	mcq($sql,$db);

	if ($_REQUEST['tabletype'] == "entity") {
		$sql = "SELECT " . $GLOBALS['TBL_PREFIX'] . "binfiles.fileid FROM " . $GLOBALS['TBL_PREFIX'] . "binfiles, " . $GLOBALS['TBL_PREFIX'] . "blobs WHERE " . $GLOBALS['TBL_PREFIX'] . "binfiles.fileid = " . $GLOBALS['TBL_PREFIX'] . "blobs.fileid AND filetype='TEMPLATE_HTML_FORM' AND (" . $GLOBALS['TBL_PREFIX'] . "blobs.content LIKE '%EFID" .  $_REQUEST['delfield'] . "%' OR " . $GLOBALS['TBL_PREFIX'] . "blobs.content LIKE '%ALL_EXTRA_FIELDS%')";
		$res = mcq($sql, $db);
		while ($row = mysql_fetch_array($res)) {
			ExpireFormCacheByForm($row[0]);
			print "<br>Form cache of form " . $row[0] . " expired.";
			qlog("Form cache of form " . $row[0] . " expired");
		}
		
	} else {
		qlog("Expiring form cache not needed.");
	}

} elseif ($_REQUEST['undelfield']) {
	
	$sql = "UPDATE $GLOBALS[TBL_PREFIX]extrafields SET deleted='n' WHERE id='" . $_REQUEST['undelfield'] . "'";
	mcq($sql,$db);

} elseif ($_REQUEST['deldatabyname']) {
	
	$sql = PopStashValue($_REQUEST['deldatabyname']);
//	print $sql;
	mcq($sql,$db);

}



if ($_REQUEST['editextrafield']) {

	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]extrafields WHERE id='" . $_REQUEST['editextrafield'] . "'";
	$result = mcq($sql,$db);
	$row = mysql_fetch_array($result);
	if ($_REQUEST['editextrafield'] <> "new") {
		print "<br>Editing extra field " . $_REQUEST['editextrafield'];
	} else {
		print "<br>New extra field";
	}
	print "<form name='EditField' method='POST'>";
	print "<input type='hidden' name='req_url' value='" . $_REQUEST['req_url'] . "'>";
	print "<table cellspacing='0' cellpadding='4' width=100% border=1 bordercolor='#F0F0F0'>";
	print "<tr><td>Name</td><td " . PrintToolTipCode('A name for your field. Can be anything you want.') .">";
	
	print "<input type='text' name='newname' size=60 value=\"" . str_replace("\"","'",$row['name']) . "\"'>";
	print "</td><td><table width=1 border=0><tr>";
	print "<td  " . PrintToolTipCode('Make text red') ."style='background: red; cursor: pointer' OnClick=\"a=document.EditField.newname.value;document.EditField.newname.value='<font color=red>' + a + '</font>'\">&nbsp;&nbsp;</td>";
	
	print "<td  " . PrintToolTipCode('Make text blue') ."style='background: #3366FF; cursor: pointer' OnClick=\"a=document.EditField.newname.value;document.EditField.newname.value='<font color=#3366FF>' + a + '</font>'\">&nbsp;&nbsp;</td>";
	print "<td  " . PrintToolTipCode('Make text green') ."style='background: green; cursor: pointer' OnClick=\"a=document.EditField.newname.value;document.EditField.newname.value='<font color=green>' + a + '</font>'\">&nbsp;&nbsp;</td>";
	print "<td  " . PrintToolTipCode('Italic') ." style='cursor: pointer' OnClick=\"a=document.EditField.newname.value;document.EditField.newname.value='<i>' + a + '</i>'\"><font face='Times'><i>I</i></font>&nbsp;</td>";
	print "<td  " . PrintToolTipCode('Bold') ." style='cursor: pointer' OnClick=\"a=document.EditField.newname.value;document.EditField.newname.value='<b>' + a + '</b>'\"><font face='Times'><b>B</b></font></td>";
	print "<td  " . PrintToolTipCode('Underline') ." style='cursor: pointer' OnClick=\"a=document.EditField.newname.value;document.EditField.newname.value='<u>' + a + '</u>'\"><font face='Times'><u>U</u></font></td>";
	print "<td  " . PrintToolTipCode('Clear all markup from name') ." style='cursor: pointer' OnClick=\"a=document.EditField.newname.value;document.EditField.newname.value=stripHTML(a);\"><font face='Times'><b>*</b></font></td>";

	print "</tr></table>";
	print "</td></tr>";


	print "<tr><td>Type</td><td colspan=2 " . PrintToolTipCode('The type of the field...') ."><select name='newtype' OnChange='SetDivOpen();'>";
	foreach ($fieldtypes AS $type) {
		if ($row['fieldtype'] == $type) {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}
		if ($_REQUEST['tabletype'] <> "entity" && $type=="drop-down based on customer list of values") {
		} else {
			print "<option " . $ins . " value='" . $type . "'>" . $type . "</option>";
		}
	}
	print "</select>";
	print "</td></tr>";
	print "<tr><td>&nbsp;</td><td colspan=2>&nbsp;";
	print "<div id='dd_button' style='display: none'><table width='100%'>";
	print "<tr><td>After creating a button, use <img src='arrow.gif'>&nbsp;<a href='trigger.php?add=miscellaneous'>miscellaneous triggers</a> to set actions to this button! <br><br> With buttons you can combine the advanced access rights possibilities of an<br>extra field with a form element. Other triggers will also go off when appropriate,<br>but the button trigger will always go last.<br><br>A button will <b>always</b> save the entity!</td></tr>";	
	print "</table></div>";	

	print "<div id='ddoptions_LOV' style='display: none'><table width='100%'>";
	print "<tr><td colspan=2>Which customer field: (must be of type 'list of values')<br><br></td></tr>";
	print "<tr><td colspan=2><select name='options'>";
	$res = mcq("SELECT * FROM $GLOBALS[TBL_PREFIX]extrafields WHERE tabletype='customer' AND fieldtype='List of values'", $db);
	while ($row2 = mysql_fetch_array($res)) {
		if ($row['options'] == $row2['id']) {
			$tins = "SELECTED";
		} else {
			unset($tins);
		}

		print "<option " . $tins . " value='" . $row2['id'] . "'>" . $row2['name'];
		print "</option>";
	}
	print "</select><br><br></td></tr>";	
	print "</table></div>";
	
	print "<div id='ddoptions_CHECKBOX' style='display: none'><table width='100%'>";
	if ($row['options'] == "") $row['options'] = "Yes";
	if ($row['defaultval'] == "") $row['defaultval'] = "No";
	print "<tr><td colspan=2>Value when checked</td><td><input type='text' name='cb_checked' value='" . $row['options'] . "'></td></tr>";
	print "<tr><td colspan=2>Value when unchecked</td><td><input type='text' name='cb_unchecked' value='" . $row['defaultval'] . "'></td></tr>";

	print "</table></div>";

	if ($row['size'] == "") {
		if ($row['fieldtype'] == "numeric") {
			$row['size'] = 8;
		} else {
			$row['size'] = 50;
		}
	}

	print "<div id='sizeoptions' style='display: none'><table width='50%'>";	
	print "<tr><td colspan=2>Size (in characters):&nbsp;&nbsp; <input size=2 name='boxsize' value='" . $row['size'] . "'></td></tr>";
	print "</table></div>";
	print "<div id='sizeoptionstextarea' style='display: none'><table width='50%'>";	
	$sa = explode(":", $row['options']);

	if ($sa[0] == "Yes") {
		$sa[0] = "";
	}

	print "<tr><td colspan=2>Number of columns (in characters)</td><td " . PrintToolTipCode('The width of of the box in characters') ."><input size=2 name='boxsize1' value='" . $sa[0] . "'></td></tr>";
	print "<tr><td colspan=2>Number of rows (in characters)</td><td " . PrintToolTipCode('The height of the box in characters') ."><input size=2 name='boxsize2' value='" . $sa[1] . "'></td></tr>";
	if ($sa[2] == "y") {
		$ins2 = "selected";
	} else {
		$ins1 = "selected";
	}
	print "<tr><td colspan=2>Show insert date/time clock <img src='timedate.gif'></td><td><select name='ShowTimeDateInputClockThingy'><option value='y' $ins2>Yes</option><option value='n' $ins1>No</option></select></td></tr>";
	
	print "</table></div>";
	print "<div id='sizeoptionstextarea_rt' style='display: none'><table width='50%'>";	
	$sa = explode(":", $row['options']);
	if ($sa[0] > 100) {
		 $sa[0] = 100;
	}
	if (!is_numeric($sa[0])) $sa[0] = "100";
	if (!is_numeric($sa[1])) $sa[1] = "400";

	print "<tr><td colspan=2>Width </td><td " . PrintToolTipCode('The width of the box relative to the width of the frame in which it is displayed.') ."><input size=2 name='boxsize3' value='" . $sa[0] . "'> %</td></tr>";
	print "<tr><td colspan=2>Height </td><td " . PrintToolTipCode('The height of the box in pixels. A normal value would be 400.') ."><input size=2 name='boxsize4' value='" . $sa[1] . "'> pixels</td></tr>";

	print "</table></div>";
	print "<div id='ddoptions' style='display: none'><table width='100%'>";
	print "<tr><td colspan=2>Enter your options here:</td></tr>";
	
	$arr = unserialize($row['options']);
	if (is_array($arr)) {
		foreach ($arr AS $option) {
			$i++;
			print "<tr><td>Option " . $i . "</td><td colspan=2><input type='text' name='newoption[]' value='" . $option . "'></td></tr>";
		}
	}
	unset($option);
	for ($t=0;$t<10;$t++) {
			$i++;
			print "<tr><td>Option " . $i . "</td><td colspan=2><input type='text' name='newoption[]' value='" . $option . "'></td></tr>";
	}
	if ($row['sort'] == 'y') {
		$t = "CHECKED";
	}
	print "<tr><td>Sort list alphabetically in forms:</td><td colspan=2><input $t type='checkbox' style='border: 0' name='sortwhendisplayed' value='y'></td></tr>";
	print "</table><br>To add more options, press save, and edit this field again. It will show 10 new empty fields.</div>";
	
	print "<div id='commentoptions' style='display: none'><table width='50%'>";
	print "<tr><td colspan='2'>Select a template to display on this extra field location: <br><br><select name='newHTMLtemplate'>";
	$sql = "SELECT fileid, filename, file_subject FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid=0 AND filetype='TEMPLATE_HTML'";
	$result= mcq($sql,$db);
	while ($row2= mysql_fetch_array($result)) {
		if ($row['name'] == $row2['fileid']) {
			$tins = "SELECTED";
		} else {
			unset($tins);
		}

		print "<option " . $tins . " value='" . $row2['fileid'] . "'>" . $row2['filename'];
		if ($row2['file_subject'] <> "") {
			print " (" . $row2['file_subject'] . ")";
		}
		print "</option>";
	}
	print "</select></td></tr>";
	if ($_REQUEST['tabletype'] == "customer") {	
		print "<tr><td>You can use customer tags in your template (not entity tags).</td></tr>";
	} else {
		print "<tr><td>You can use entity tags as well as customer tags in your template.</td></tr>";
	}
	print "<tr><td> <img src='arrow.gif'>&nbsp;<a target=new class='bigsort' href='admin.php?templates=1' style='cursor:pointer'>Edit RTF and HTML templates in new window</a></td></tr>";
	print "</table></div>";
	
	print "</td></tr>";
	if ($row['hidden'] == "y") {
		$ins = "SELECTED";
	} elseif ($row['hidden'] == "a") {
		$inst = "SELECTED";
	} else {
		unset($ins);
	}
	if ($_REQUEST['editextrafield']<>"new") {
		print "<tr><td>Order</td><td colspan=2 " . PrintToolTipCode('This field sets the order in which the fields are processed, and in sometimes the way they are shown (when you use the default entity form).') . "><input type='text' name='neworder' size='10' value='" . $row['ordering'] . "'></td></tr>";
	}

//	if ($_REQUEST['tabletype'] <> "customer") {	
		print "<tr><td>Visible for:</td><td colspan=2 " . PrintToolTipCode('A basic setting which determines who can see this field. For more advanced access control, use detailed access restrictions.') .">";
		print "<select name='newhidden'>";
		print "	<option value='n'>Everybody</option>";
		print "	<option value='y' $ins>Everybody but limited users</option>";
		print "	<option value='a' $inst>Administrators only</option></select>";
		print "</td></tr>";
//	}
		if ($_REQUEST['editextrafield']<>"new") {
			if (is_array(GetExtraFieldAccessRestrictions($_REQUEST['editextrafield']))) {
				print "<tr><td>Detailed access restrictions:</td><td colspan=2 " . PrintToolTipCode('Specific access rights are set for this field.') . ">";
				print "<font color='#FF0000'>[restrictions apply]</font>";
			} else {
				print "<tr><td>Detailed access restrictions:</td><td colspan=2 " . PrintToolTipCode('No specific access rights are set for this field.') . ">";
				print "[none set]";
			}
			print "	[ <a href='javascript:PopRightsChooser(" . $_REQUEST['editextrafield'] . ");'>select</a> ]";
		} else {
			print "<tr><td>Detailed access restrictions:</td><td colspan=2 " . PrintToolTipCode('No specific access rights are set for this field.') . ">";
				print "[none set, save first]";
		}
		
		print "</td></tr>";
//	
	
	if ($row['storetype'] == "default") {
		$ins = "SELECTED";
	} elseif ($row['storetype'] == "3rd_table") {
		$inst = "SELECTED";
	} elseif ($row['storetype'] == "3rd_table_popup") {
		$inst2 = "SELECTED";
	}
		/*print "<tr><td>Store type:</td><td colspan=2>";
		print "<select name='storetype'>";
		print "	<option value='default' $ins>Normal, single field</option>";
		print "	<option value='3rd_table' $inst>Table wich can contain multiple values (presented in-line)</option>";
		print "	<option value='3rd_table_popup' $inst2>Table wich can contain multiple values (presented in popup window)</option>";
		print "</td></tr>";
		*/

	if ($row['forcing'] == "y") {
		$ins = "CHECKED";
	} else {
		unset($ins);
	}

	print "<tr><td>Make this a required field:</td><td colspan=2 " . PrintToolTipCode('When checked, the user will have to give this field a value before saving the entity.') ."><input type='checkbox' style='border:0' name='forcing' value='y'" . $ins . "></td></tr>";
	print "<tr><td>Default value:</td><td colspan=2 " . PrintToolTipCode('The value of this field, when nothing has been entered by a user.') ."><input type='text' name='defaultval' value='" . $row['defaultval'] . "'>";

	
	if (is_numeric($_REQUEST['editextrafield'])) {
		//print "&nbsp;&nbsp;(only for newly added entities or customers)";
	}
	print "</td></tr>";

	if ($_REQUEST['tabletype'] <> "customer") {	

		print "<td>Location:</td><td colspan=2 " . PrintToolTipCode('This determines where the field will be printed. See the manual (ALT-M) for more information.') ."><select name='newlocation'>";
		foreach ($locations AS $loc) {
			if ($row['location'] == $loc) {
				$ins = "SELECTED";
			} else {
				unset($ins);
			}
			print "<option " . $ins . ">" . $loc . "</option>";
		}
		print "</select> (for default entity form only)";
		print "</td></tr>";
	}	
	
	if (!$_REQUEST['req_url']) {
		$loc = "extrafields.php?tabletype=" . $_REQUEST['tabletype'];
	} else {
		$loc = base64_decode($_REQUEST['req_url']);
	}
	
	if ($_REQUEST['tabletype'] == "entity") {
		print "<input type='hidden' name='ti' value='1'>";
	} else {
		print "<input type='hidden' name='ti' value='2'>";
	}
	print "<tr><td colspan='3' align='right'><input type='hidden' name='tabletype' value='" . $_REQUEST['tabletype'] . "'><input type='button' value='Cancel' OnClick=\"document.location='" . $loc . "'\">&nbsp;<input name='submitted' type='submit' value='Save to database'></td></tr>";

	print "</table>";
	print "</form>";
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
		function SetDivOpen() {
			if (document.EditField.newtype.value == 'drop-down' || document.EditField.newtype.value == 'VAT drop-down') {
				showLayer('ddoptions');
			} else {
				hideLayer('ddoptions');
			}
			if (document.EditField.newtype.value == 'textbox' || document.EditField.newtype.value == 'numeric' || document.EditField.newtype.value == 'mail' || document.EditField.newtype.value == 'hyperlink' || document.EditField.newtype.value == 'invoice cost' || document.EditField.newtype.value == 'invoice cost including VAT' || document.EditField.newtype.value == 'invoice qty' || document.EditField.newtype.value == 'List of values') {
				showLayer('sizeoptions');
			} else {
				hideLayer('sizeoptions');
			}
			if (document.EditField.newtype.value == 'comment') {
				showLayer('commentoptions');
			} else {
				hideLayer('commentoptions');
			}
			if (document.EditField.newtype.value == 'text area') {
				showLayer('sizeoptionstextarea');
				if (document.EditField.boxsize1.value=='')
				{
					document.EditField.boxsize1.value = '100';
				}
				if (document.EditField.boxsize2.value=='')
				{
					document.EditField.boxsize2.value = '10';
				}
			} else {
				hideLayer('sizeoptionstextarea');
			}
			if (document.EditField.newtype.value == 'text area (rich text)') {
				showLayer('sizeoptionstextarea_rt');
				if (document.EditField.boxsize3.value == '')
				{
					document.EditField.boxsize3.value = '100%';
				}
				if (document.EditField.boxsize4.value == '')
				{
					document.EditField.boxsize4.value = '400';
				}
			}
			if (document.EditField.newtype.value == 'drop-down based on customer list of values') {
				showLayer('ddoptions_LOV');
			} else {
				hideLayer('ddoptions_LOV');
			}
			if (document.EditField.newtype.value == 'checkbox') {
				showLayer('ddoptions_CHECKBOX');
				document.EditField.defaultval.disabled = true;			
				document.EditField.forcing.disabled = true;			
			} else {
				hideLayer('ddoptions_CHECKBOX');
				document.EditField.defaultval.disabled = false;			
				document.EditField.forcing.disabled = false;			
				document.EditField.defaultval.value = '';
			}
			
			if (document.EditField.newtype.value == 'Button') {
				showLayer('dd_button');
			} else {
				hideLayer('dd_button');
			}
			if (document.EditField.newtype.value == 'List of values') {
				document.EditField.defaultval.disabled = true;			
				document.EditField.forcing.disabled = true;			
			} else if (!document.EditField.newtype.value == 'checkbox') {
				document.EditField.defaultval.disabled = false;			
				document.EditField.forcing.disabled = false;		
				document.EditField.defaultval.value = '';		
			}
		}
		SetDivOpen();
	//-->
	</SCRIPT>
	<?
} else {

	print "<br><br><B><font color='#FF0000'>Inline edit mode</font>:</b><br><br>";
	
	print "When enabled, this function will make all extra field names a link directly to the edit extra field page of that field. This is<br>very useful when working on your extra fields. It will only be visible to you - you won't bother other users.<br><br>&nbsp;&nbsp;";
	if ($GLOBALS['ef_inline_edit'] <> "yes") {
		print "<img src='arrow.gif'>&nbsp;<A OnClick=\"javascript: setCookie('ef_inline_edit','yes');alert('You are now in inline edit mode.');\" class='bigsort' style='cursor:pointer'>go to inline edit mode</a>&nbsp;&nbsp;&nbsp;&nbsp;";
	} else {
		print "<img src='arrow.gif'>&nbsp;<A OnClick=\"javascript: setCookie('ef_inline_edit','');alert('You left inline edit mode. Refresh the screen or click a link to hide the links.');\" class='bigsort' style='cursor:pointer'>leave inline edit mode</a>";
	}
	
	print "<br><br>Extra fields currently in this repository:<br><br>";
	print "<table width=100% class='crm'>";
	print "<tr><td>ID</td><td>Order</td><td>Type</td><td>Name</td><td>Occurences</td><td>Visible for</td>";
	print "<td>Detailed access restrictions</td>";
	if ($_REQUEST['tabletype'] <> "customer") {
		print "<td>Location</td>";
	}
	print "<td>Required</td><td>Delete</td>";




	if ($_REQUEST['tabletype'] == "entity") {
		$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]extrafields WHERE tabletype='" . $tabletype . "' AND deleted<>'y' ORDER BY location,ordering";
	} else {
		$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]extrafields WHERE tabletype='" . $tabletype . "' AND deleted<>'y' ORDER BY ordering";
	}
	


	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		
		if ($_REQUEST['tabletype'] == "entity") {
			$box = $row['location'];
			if ($box<>$oldbox) {
				print "<tr height='5'><td colspan=10 style='background:#EDF376'></td></tr>";
				$oldbox = $box;
			}
		}

		print "<tr onmouseover=\"javascript:style.background='#E9E9E9';\" onmouseout=\"style.background='#FFFFFF';\" OnClick='document.location=\"extrafields.php?editextrafield=" . $row['id'] . "&tabletype=" . $_REQUEST['tabletype'] . "\";' style='cursor:pointer'>";
		print "<td>" . $row['id'] . "</td><td>" . $row['ordering'] . "</td><td>" . $row['fieldtype'] . "</td><td>";
		if ($row['fieldtype'] == "comment") {
			print "[template " . GetFileName($row['name']) . "]";
		} else {
			print $row['name'];
		}
		print "</td><td>";
		$sql2 = "SELECT count(*) AS count FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name='" . $row['id'] . "'";
		$result2 = mcq($sql2,$db);
		$row2 = mysql_fetch_array($result2);
		print $row2['count'];
		print "</td>";
		if ($row['hidden'] == "y") {
			$txt = "<td style='background:#EDF376'>Everybody but limited users</td>";
		} elseif ($row['hidden'] == "a") {
			$txt = "<td style='background:#CF8B7A'>Administrators only</td>";
		} else {
			$txt = "<td>Everybody</td>";
		} 
		if (is_array(GetExtraFieldAccessRestrictions($row['id']))) {
			$txt .= "<td style='background:#EDF376' " . PrintToolTipCode('Specific access rights are set for this field.') . ">Apply</td>";
		} else {
			$txt .= "<td>None set</td>";
		}
		if ($row['forcing'] == "y") {
			$txt2 = "<td style='background:#EDF376'>Yes</td>";
		} else {
			$txt2 = "<td>No</td>";
		} 
		print $txt;

		if ($_REQUEST['tabletype'] <> "customer") {

			print "<td>" . $row['location'] . "</td>";
		}

		print $txt2;
		print "</td><td><a href='extrafields.php?tabletype=" . $_REQUEST['tabletype'] . "&delfield=" . $row['id'] ."&ti=" . $_REQUEST['ti'] . "'><img style='border:0' src='delete.gif'></a></td></tr>";
	}
	print "</table>";

	$tp .= "<br>Unreferenced extra fields currently in this repository:<br><br>";
	$tp .= "<table width=100% class='crm'>";
	$tp .= "<tr><td>Name</td><td>Occurences</td><td>Delete data</td><td>Restore field</td></tr>";
	
	

	if ($_REQUEST['tabletype'] == "customer") {
		$ins1 = " WHERE type='cust'";
		$ins2 = " AND type='cust'";
		$list = GetExtraCustomerFields();

	} else {
		$list = GetExtraFields();
		$ins1 = " WHERE type<>'cust'";
		$ins2 = " AND type<>'cust'";
	}
	
	$valid_fields = array();
	$faulty_names = array();

	foreach ($list AS $field) {
		array_push($valid_fields,$field['id']);
	}

	$ret = array();
	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]extrafields ORDER BY ordering";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		$ret[$row['id']] = $row['name'];
	}

	$sql = "SELECT DISTINCT(name) FROM $GLOBALS[TBL_PREFIX]customaddons " . $ins1 . "";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if (!in_array($row['name'],$valid_fields)) {
			array_push($faulty_names,$row['name']);
		}
	}

	foreach ($faulty_names AS $Name) {

			$sql = "SELECT count(*) AS count FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name='" . $Name . "' " . $ins2 . " GROUP BY name";
			$result = mcq($sql,$db);
			$row = mysql_fetch_array($result);

			$faultyfieldcount++;

			$tp .= "<tr>";

			if ($_REQUEST['tabletype'] == "entity") {
					$i = PushStashValue("DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name ='" . $Name ."' AND type<>'cust'");
			} else {
					$i = PushStashValue("DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name ='" . $Name ."' AND type='cust'");
			}



			if ($ret[$Name]<>"") {
				$restore = "<a href='extrafields.php?tabletype=" . $_REQUEST['tabletype'] . "&undelfield=" . $Name ."'>Restore</a>";
				
				if ($_REQUEST['tabletype'] == "entity") {
					$ins = "AND type<>'cust'";
				} else {
					$ins = "AND type='cust'";
				}
				$i = PushStashValue("DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name ='" . $Name ."' " . $ins);
				//print PopStashValue($i);
				$Name = $ret[$Name];
			} else {
				$restore = "n/a";
			}
			$tp .= "<td>" . $Name . "</td><td>" . $row['count'] . "</td>";

			$tp .= "<td><a href='extrafields.php?tabletype=" . $_REQUEST['tabletype'] . "&deldatabyname=" . $i ."'><img style='border:0' src='delete.gif'></a></td><td>" . $restore . "&nbsp;</td></tr>";
		}
	
	$tp .= "</table>";

	if ($faultyfieldcount>0) {
		print $tp;
	}

}

?>
<br><br>
<b>Legend:</b><br>
<table width='100%' border='0' cellpadding=3 cellspacing=5>
	<tr>
		<td>ID</td>
		<td>The extra field number</td>
	</tr>
	<tr>
		<td>Order</td>
		<td>The order number. Within a box, fields will be ordered by this number.</td>
	</tr>
	<tr>
		<td>Occurrences</td>
		<td>The total number of fields of this type which actually have a value.</td>
	</tr>
	<tr>
		<td>Required</td>
		<td>Fields having this property are required; the user will not be able to save an entity or customer without having given this field a value.</td>
	</tr>
	<tr>
		<td>Default value</td>
		<td>The default value (when adding a new entity or customer)</td>
	</tr>
	<?
	if ($_REQUEST['tabletype']<>"customer") {
	?>
	<tr>
		<td>Location</td>
		<td>The location where the field will appear. (only when using default forms)</td>
	</tr>
	<?
	}
	?>
	
	<tr>
		<td colspan='2'><b>Feel free to use HTML-tags in your field names to create a more readable entity form.</b></td>
	</tr>
</table>
<?

print "</fieldset>";
print "</td></tr></table>";
print "</td></tr></table>";
EndHTML();

<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2003 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * language.php loads language packs
 *
 *  1st: layer 0 : Main English pack for security reasons
 *  2nd: layer 1 : System wide language pack
 *  3rd: layer 2 : User preference language pack (if not overruled)
 *  4th: layer 3 : System language mask
 *  5th: layer 4 : Tag-display mode
 *
 *	Several extensive checks. This routine is not ment to be fast;
 *  it's meant to be safe and consistent.
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
$GLOBALS['CURFUNC'] = "LoadLanguage::";
$lang = array();

// Layer 0: Security layer

$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='ENGLISH'";
$result= mcq($sql,$db);								
while ($resarr=mysql_fetch_array($result)){
		$lang[$resarr[TEXTID]] = ereg_replace("\015","",(ereg_replace("\n","",stripslashes($resarr[TEXT]))));
		$tel++;
}

qlog("Loading language ENGLISH hard-coded ($tel tags)");
unset($tel);

$sql= "SELECT TEXT FROM $GLOBALS[TBL_PREFIX]languages WHERE TEXTID='current-language' AND LANGID='GLOBAL'";
$result= mcq($sql,$db);
$result= mysql_fetch_array($result);

$language = $result[TEXT]; // This is the system-wide language variable

qlog("Default language from database is $language");

// Layer 1: System default language
if ($language<>"ENGLISH") {
	$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='$language'";
	$result= mcq($sql,$db);								
	while ($resarr=mysql_fetch_array($result)){
			$lang[$resarr[TEXTID]] = ereg_replace("\015","",(ereg_replace("\n","",stripslashes($resarr[TEXT]))));
			$tel++;
	}
	qlog("Default language $language loaded ($tel tags)");
	$$language=1;
} else {
	qlog("Language $language not loaded - it is already in memory");
	$$language=1;
}
// Layer 2: User preferred language

	$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]settings WHERE setting='langoverride'";
	$result= mcq($sql,$db);
	$resarr=mysql_fetch_array($result);
	
	if (strtoupper($resarr[0])=="NO") {

			$sql= "SELECT exptime FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id='" . $GLOBALS['USERID'] . "'";
			$result= mcq($sql,$db);
			$result= mysql_fetch_array($result);

			if ($result[exptime]) {

					$language = $result[exptime];
					// Now check if this language is really an existing language pack
					$sql= "SELECT DISTINCT LANGID FROM $GLOBALS[TBL_PREFIX]languages";
					$result= mcq($sql,$db);

					while ($resarr=mysql_fetch_array($result)){
						$tmparr[$p] = $resarr[LANGID];	  // All LANGID's in array
						$p++;
					}

				qlog("User-preferred is $language (not loaded yet)");

				// Hard-coded correction if pack is inconsistent
				if (!@in_array($language,$tmparr)) {
					$language = "ENGLISH";
					qlog("Due to an impossible error, the language is set back to default ENGLISH");
				}

			}

	} else {
		qlog("User language selection is overridden - " . strtoupper($resarr[0]));
	}

//qlog("Current language is $language (not loaded yet)");

if (!$$language==1 && $language<>"") {
	$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='$language'";
	$result= mcq($sql,$db);

	unset($tel);
	while ($resarr=mysql_fetch_array($result)){
				$lang[$resarr[TEXTID]] = ereg_replace("\015","",(ereg_replace("\n","",stripslashes($resarr[TEXT]))));
				$tel++;
	}
	$$language=2;
	qlog("Loading user preferred language $language ($tel tags)");
} elseif ($$language==1) {
	qlog("User-preferred language not loaded - it is the same as the system default");
	$$language=2;
} elseif ($language=="ENGLISH") {
	
}
// And now, a hard-coded check:

if (trim($lang[main])=="") {
		print "ERROR. Language settings are not correct. Trying to correct this...";
		qlog("Missing a tag which should be there. Defaulting to ENGLISH");
		$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='ENGLISH'";
		$result= mcq($sql,$db);								
		while ($resarr=mysql_fetch_array($result)){
				$lang[$resarr[TEXTID]] = ereg_replace("\015","",(ereg_replace("\n","",stripslashes($resarr[TEXT]))));
				$tel++;
		}

}
// And another hard-coded check:

if (trim($lang[main])=="") {
		print "<BR>ERROR. Language settings are not correct (probably no (complete) language pack installed). This is fatal. Please re-install this repository.";
		qlog("After correcting, the tag still isn't there. Giving up");
		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
		setCookie('passwordbla','')
		setCookie('namebla','')
		setCookie('repository','')
		//-->
		</SCRIPT>
		<?
//		exit;
}

// Layer 3: System default mask

$sql= "SELECT TEXT FROM $GLOBALS[TBL_PREFIX]languages WHERE TEXTID='current-language-mask' AND LANGID='GLOBAL'";
$result= mcq($sql,$db);
$result= mysql_fetch_array($result);

$language_mask = $result[TEXT]; 
// This is the system-wide mask variable


if ($language_mask<>"-") {
	qlog("Mask is $language_mask (not loaded yet)");
	if (!($$language_mask==2)) {
		$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]languages WHERE LANGID='$language_mask'";
		$result= mcq($sql,$db);								
		while ($resarr=mysql_fetch_array($result)){
				$lang[$resarr[TEXTID]] = ereg_replace("\015","",(ereg_replace("\n","",stripslashes($resarr[TEXT]))));
				$num++;
		}
		qlog("Loading mask $language_mask ($num tags)");
		unset($num);
	} else {
		qlog("Mask $language_mask not loaded - it is already loaded");
	}
} else {
	qlog("No mask selected (nor loaded)");
}


// Now trap the translation mode (tag display mode)
// Layer 4: Tag display mode (undocumented)

if ($language_display=="yes") {

	$sql= "SELECT DISTINCT TEXTID FROM $GLOBALS[TBL_PREFIX]languages";
		$result= mcq($sql,$db);
		while ($resarr=mysql_fetch_array($result)){
			$lang[$resarr[TEXTID]] .= " (" . $resarr[TEXTID] . ")";	  // All LANGID's in array
			$p++;
		}

}

$GLOBALS['CURFUNC'] = "";
?>
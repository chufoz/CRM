<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This script generates RTF entity reports (NOT Reports!)
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */

// <TEMPORARY SETTINGS WHICH SHOULD RESIDE IN MAIN SETTINGS TABLE>

$GLOBALS['ENABLESUBSCRIPTIONS'] = "Yes";
$GLOBALS['AUTOPROCESSSUBSCRIPTIONS'] = "Yes";
$GLOBALS['AUTOPROCESSSUBSCRIPTION_TYPE'] = "rw";

// </TEMPORARY SETTINGS WHICH SHOULD RESIDE IN MAIN SETTINGS TABLE>


print "Dichtgezet ln. 22 subscribe.php - te gevaarlijk om open te laten.";
exit;

extract($_REQUEST);

if ($_REQUEST['new']) {
	$dni = 1;
	$nonavbar = 1;
	include("header.inc.php");
	$mode = "external";
} else {
	include("header.inc.php");
	MustBeAdmin();
	$mode = "internal";
}

if ($GLOBALS['ENABLESUBSCRIPTIONS'] == "Yes") {
	
	if ($_REQUEST['new'] && $mode == "external" && $_REQUEST['username'] && $_REQUEST['fullname'] && $_REQUEST['email']) {

		?>
			<table><tr><td>&nbsp;&nbsp;&nbsp;</td><td>
				<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Subscribe to CRM-CTT repository <? echo $GLOBALS['title'];?></legend>
					<table cellspacing='4' cellpadding='4'>
						<tr><td>
		<?
		$i = date('U');
		$pwd = str_replace("=","Q",base64_encode($_REQUEST['username']) . ($i/32325325243.14)*$i);


		if ($GLOBALS['AUTOPROCESSSUBSCRIPTIONS'] == "Yes") {
			$sql = "INSERT INTO " . $GLOBALS['TBL_PREFIX'] . "loginusers(name,password,EMAIL,FULLNAME,CLLEVEL) VALUES('" . $_REQUEST['username'] . "',PASSWORD('" . $pwd . "'),'" . $_REQUEST['email'] . "','" . $_REQUEST['fullname'] . "','rw')";
			
			mcq($sql,$db);
			// Create a new mail class

			$mail = new PHPMailer();
			$mail->From     = $GLOBALS['admemail'];
			$mail->FromName = "CRM-CTT Site administrator";
			$mail->Host     = $SMTPserver;
			$mail->Mailer   = $GLOBALS['MailMethod'];
				// AUTHENTICATED SMTP SERVERS USE THIS: (not tested)
			if ($GLOBALS['MailUser'] <> "" && $GLOBALS['MailPass'] <> "") {
				$mail->Username = $GLOBALS['MailUser'];
				$mail->Password = $GLOBALS['MailPass'];
			}
			$mail->IsHTML(false);
			$MailSubject = "Your CRM-CTT account (" . $GLOBALS['title'] . ")";
			$MailBody = "Your CRM-CTT password is " . $pwd . "\n";
			$MailBody .= "\nThis password is very complex - we hope you change it ASAP.\n\nThis mail contains very little information - this is intented. If you didn't expect this e-mail you can safely delete it. It is not spam.";

			qlog("User subscribed, added immediately. (" . $_REQUEST['username'] . " / " . $_REQUEST['fullname'] . " / " . $_REQUEST['email']);
			$mail->Body    = stripslashes($MailBody);
			//$mail->AltBody = stripslashes($MailBody);
			$mail->AddAddress($_REQUEST['email'],$_REQUEST['fullname']);
			$mail->Subject = $MailSubject;
			if(!$mail->Send()) {
					echo "<font color='#FF0000'>There has been a mail error: " . $mail->ErrorInfo . ". Please contact your system administrator.</font><br>";
					log_msg("Sending e-mail to subscriber failed:" . $mail->ErrorInfo,"");
					qlog("E-mail NOT sent.. ERROR: " . $mail->ErrorInfo);
			} else {
					print "An e-mail was send to " . $_REQUEST['email'] . " containing your password for user-account " . $_REQUEST['username'] . ".<br><br>Please alter your password immediately.";
					print "<br><br><img src='arrow.gif'>&nbsp;<a href='index.php?logout=1' class='bigsort'>To the CRM-CTT login page</a>";
					print "<br><br><img src='arrow.gif'>&nbsp;<a href='http://www.crm-ctt.com' class='bigsort'>To the CRM-CTT project page</a>";
			}

		} else {
			print "Your request has been submitted to the crew. When your membership is approved you will receive an e-mail at " . $_REQUEST['email'];
			print "<br><br><img src='arrow.gif'>&nbsp;<a href='http://www.crm-ctt.com' class='bigsort'>To the CRM-CTT project page</a>";
		}
		?>
						</td></tr>
					</table>
				</fieldset>
			</td></tr>
		</table>
		<?			
	} elseif ($_REQUEST['new'] && $mode == "external") {
			?>
			<table><tr><td>&nbsp;&nbsp;&nbsp;</td><td>
				<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Subscribe to CRM-CTT repository <? echo $GLOBALS['title'];?></legend>
				<form name='subscribe' method='post'>
					<table cellspacing='4' cellpadding='4'>
						<tr><td>
							<img src='crmlogosmall.gif'>&nbsp;&nbsp;&nbsp;* Proposed user-name:
							</td><td>
							<input type='text' name='username' value=''>
							<input type='hidden' name='new' value='true'>
						</td></tr>
						<tr><td>
							<img src='crmlogosmall.gif'>&nbsp;&nbsp;&nbsp;* Full name:
							</td><td>
							<input type='text' name='fullname' value=''>
						</td></tr>
						<tr><td>
							<img src='crmlogosmall.gif'>&nbsp;&nbsp;&nbsp;* E-mail address:
							</td><td>
							<input type='text' name='email' value=''>
						</td></tr>
						<tr><td>
						<img src='crmlogosmall.gif'>&nbsp;&nbsp;&nbsp;Remarks:
							</td><td>
							<textarea cols=17 rows=5 name='remarks'></textarea>
						</td></tr>
						<tr><td>
							</td><td>
							<input type='button' name='submit_form' OnClick='checkSUBform();' value='Submit request'>
						</td></tr>
				</fieldset>
				</form>
			</td></tr></table>
			<SCRIPT LANGUAGE="JavaScript">
			<!--
				function checkSUBform() {
					var x='';
					if (document.subscribe.username.value != '' && document.subscribe.fullname.value != '' && document.subscribe.email.value != '' && isValidEmail(document.subscribe.email.value)) {
						document.subscribe.submit();
					} else {
						if (document.subscribe.username.value == '') {
								document.subscribe.username.style.background='#D6E3FC';
								x += 'Enter a username. ';
						} else {
								document.subscribe.username.style.background='#FFFFFF';
						}
						if (document.subscribe.fullname.value == '') {
								document.subscribe.fullname.style.background='#D6E3FC';
								x += 'Enter your full name. ';
						} else {
								document.subscribe.fullname.style.background='#FFFFFF';
						}
						if (document.subscribe.email.value == '' || isValidEmail(document.subscribe.email.value) == false) {
								document.subscribe.email.style.background='#D6E3FC';
								if (isValidEmail(document.subscribe.email.value) == false) {
									x += 'The e-mail address is not valid.';
								}
						} else {
								document.subscribe.email.style.background='#FFFFFF';
						}
	
						alert('Not all required fields are ok. ' + x);
					}
				}
			//-->
			</SCRIPT>
			<?
	} elseif ($mode == "internal") {
		print "hallo dude";
	}
} else {
	print "<img src='error.gif'> This feature is disabled by the administrator.";
}

EndHTML();
?>
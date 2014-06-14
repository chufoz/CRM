function is_numeric(strString)
   //  check for valid numeric strings	
   {
   var strValidChars = "0123456789.,";
   var strChar;
   var blnResult = true;

   if (strString.length == 0) return false;

   //  test strString consists of valid characters listed above
   for (i = 0; i < strString.length && blnResult == true; i++)
      {
      strChar = strString.charAt(i);
      if (strValidChars.indexOf(strChar) == -1)
         {
         blnResult = false;
         }
      }
   return blnResult;
   }
function stat(message) {
  status=message;
  document.returnValue = true;
}

function CheckMailTrigger() {
	y = document.TriggerAddForm.action.options[TriggerAddForm.action.selectedIndex].value.substring(0, 4);
	if (y == 'mail')
	{
		document.TriggerAddForm.template_fileid.disabled = false;
		document.TriggerAddForm.attach.disabled = false;
		document.TriggerAddForm.report_fileid.disabled = false;
	} else {
		document.TriggerAddForm.template_fileid.disabled = true;
		document.TriggerAddForm.attach.disabled = true;
		document.TriggerAddForm.report_fileid.disabled = true;
	}
}

function CopyToClipboard(text) {
	document.clipboardform.clipboardvalue.value = text;
	temp1 = clipboardform.clipboardvalue.createTextRange();
	temp1.execCommand( 'copy' );
}
function ExecuteButton(buttonid) {
	document.EditEntity.e_button.value = buttonid;	
	CheckForm();
}
function popupdater(i)	{	
	newWindow = window.open('edit.php?NoMenu=1&e=' + i, 'Update' + i,'width=680,height=630,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
	}	
	
function PopRightsChooser(i)	{	
	newWindow = window.open('choose_rights.php?1&field=' + i, 'Update' + i,'width=600,height=400,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
	}	
function popExportWindow(q)	{	
	ExportWindow = window.open('customers.php?nonavbar=1&export=1&stashid=' + q, 'Export','width=370,height=150,directories=0,status=1,menuBar=0,scrollBars=1,resizable=1');
	}		
function popcustomereditscreen(i)	{	
	newWindow = window.open('customers.php?keeplocked=1&editcust=1&nonavbar=1&custid=' + i, 'Update' + i,'width=640,height=630,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
	}		
function hideLayer(whichLayer) {
	if (document.getElementById) {
		// this is the way the standards work
		document.getElementById(whichLayer).style.display = "none";
	}
	else if (document.all) {
		// this is the way old msie versions work
		document.all[whichlayer].style.display = "none";
	}
	else if (document.layers) {
		// this is the way nn4 works
		document.layers[whichLayer].display = "none";
	}
}
function showLayer(whichLayer) {
		//	alert(whichLayer);
	if (document.getElementById) {
		// this is the way the standards work
		document.getElementById(whichLayer).style.display = "block";
	}
	else if (document.all) {
		// this is the way old msie versions work
		document.all[whichlayer].style.display = "block";
	}
	else if (document.layers) {
		// this is the way nn4 works
		document.layers[whichLayer].display = "block";
	}
}
function isValidEmail (emailStr) {
	/* The following pattern is used to check if the entered e-mail address
	   fits the user@domain format.  It also is used to separate the username
	   from the domain. */
	var emailPat=/^(.+)@(.+)$/
	/* The following string represents the pattern for matching all special
	   characters.  We don't want to allow special characters in the address. 
	   These characters include ( ) < > @ , ; : \ " . [ ]    */
	var specialChars="\\(\\)<>@,;:\\\\\\\"\\.\\[\\]"
	/* The following string represents the range of characters allowed in a 
	   username or domainname.  It really states which chars aren't allowed. */
	var validChars="\[^\\s" + specialChars + "\]"
	/* The following pattern applies if the "user" is a quoted string (in
	   which case, there are no rules about which characters are allowed
	   and which aren't; anything goes).  E.g. "jiminy cricket"@disney.com
	   is a legal e-mail address. */
	var quotedUser="(\"[^\"]*\")"
	/* The following pattern applies for domains that are IP addresses,
	   rather than symbolic names.  E.g. joe@[123.124.233.4] is a legal
	   e-mail address. NOTE: The square brackets are required. */
	var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/
	/* The following string represents an atom (basically a series of
	   non-special characters.) */
	var atom=validChars + '+'
	/* The following string represents one word in the typical username.
	   For example, in john.doe@somewhere.com, john and doe are words.
	   Basically, a word is either an atom or quoted string. */
	var word="(" + atom + "|" + quotedUser + ")"
	// The following pattern describes the structure of the user
	var userPat=new RegExp("^" + word + "(\\." + word + ")*$")
	/* The following pattern describes the structure of a normal symbolic
	   domain, as opposed to ipDomainPat, shown above. */
	var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$")


	/* Finally, let's start trying to figure out if the supplied address is
	   valid. */

	/* Begin with the coarse pattern to simply break up user@domain into
	   different pieces that are easy to analyze. */
	var matchArray=emailStr.match(emailPat)
	if (matchArray==null) {
	  /* Too many/few @'s or something; basically, this address doesn't
		 even fit the general mould of a valid e-mail address. */
		//alert("Email address seems incorrect (check @ and .'s)")
		return false
	}
	var user=matchArray[1]
	var domain=matchArray[2]

	// See if "user" is valid 
	if (user.match(userPat)==null) {
		// user is not valid
		//alert("The username doesn't seem to be valid.")
		return false
	}

	/* if the e-mail address is at an IP address (as opposed to a symbolic
	   host name) make sure the IP address is valid. */
	var IPArray=domain.match(ipDomainPat)
	if (IPArray!=null) {
		// this is an IP address
		  for (var i=1;i<=4;i++) {
			if (IPArray[i]>255) {
				//alert("Destination IP address is invalid!")
			return false
			}
		}
		return true
	}

	// Domain is symbolic name
	var domainArray=domain.match(domainPat)
	if (domainArray==null) {
		//alert("The domain name doesn't seem to be valid.")
		return false
	}

	/* domain name seems valid, but now make sure that it ends in a
	   three-letter word (like com, edu, gov) or a two-letter word,
	   representing country (uk, nl), and that there's a hostname preceding 
	   the domain or country. */

	/* Now we need to break up the domain to get a count of how many atoms
	   it consists of. */
	var atomPat=new RegExp(atom,"g")
	var domArr=domain.match(atomPat)
	var len=domArr.length
	if (domArr[domArr.length-1].length<2 || 
		domArr[domArr.length-1].length>3) {
	   // the address must end in a two letter or three letter word.
	   //alert("The address must end in a three-letter domain, or two letter country.")
	   return false
	}

	// Make sure there's a host name preceding the domain.
	if (len<2) {
	   var errStr="This address is missing a hostname!"
	   //alert(errStr)
	   return false
	}

	// If we've gotten this far, everything's valid!
	return true;
}
function isValidEmail2(str) {
   return (str.indexOf(".") > 3) && (str.indexOf("@") > 1);
}


function leaveUnlock() {
		if (document.EditEntity.unlock.value == '1') {
			PopUnlockWindow(1);
		}
}
function LockWarning() {
		alert("You have lost your exclusive write lock to this entity");
}
function stripHTML(str)	{ 
	return str.replace(/<[^>]*>/g, "");
}
function PopUnlockWindow(e) {
	if (event.clientY < 0) {
			newWindow = window.open('index.php?unlock=' + e, 'UnLockWindow','width=1,height=1,directories=0,status=1,menuBar=0,scrollBars=1,resizable=1');
	}
}
function popcalendar()	{
				document.EditEntity.duedate.blur();
				newWindow = window.open('calendar.php?select=1', 'myWindow2','width=570,height=400,directories=0,status=1,menuBar=0,scrollBars=1,resizable=1');
				newWindow.focus();
		}
function popreminder()	{
				document.EditEntity.reminderdate.blur();
				newWindow = window.open('calendar2.php?select=1', 'myWindow2','width=570,height=400,directories=0,status=1,menuBar=0,scrollBars=1,resizable=1');
				newWindow.focus();
		}

function popcalendarSelect(field,dummy)	{
				newWindow = window.open('calendar.php?NoClickToWeek=1&select=1&this=' + field, 'myWindow2','width=570,height=400,directories=0,status=1,menuBar=0,scrollBars=1,resizable=1');
				newWindow.focus();
}
function popcalendarSelectEFCustomer(field,dummy)	{
				newWindow = window.open('calendar.php?NoClickToWeek=1&select=1&Cust=1&this=' + field, 'myWindow2','width=570,height=400,directories=0,status=1,menuBar=0,scrollBars=1,resizable=1');
				newWindow.focus();
}
function popcalendarforalarmdate()	{
				document.EditEntity.duedate.blur();
				newWindow = window.open('calendar.php?NoClickToWeek=1', 'myWindow2','width=570,height=400,directories=0,status=1,menuBar=0,scrollBars=1,resizable=1');
				newWindow.focus();
		}		
function popEmailToCustomerScreen(eid)	{
				newWindow = window.open('edit.php?SendEmailToCustomer=1&nonavbar=1&EntityID=' + eid, 'SendMailWindow' + eid,'width=570,height=485,directories=0,status=1,menuBar=0,scrollBars=1,resizable=0');
				newWindow.focus();
		}

function popEmailToCustomerScreenCust(cust)	{
				newWindow = window.open('edit.php?SendEmailToCustomer=1&nonavbar=1&CustID=' + cust, 'SendMailWindow' + cust,'width=570,height=485,directories=0,status=1,menuBar=0,scrollBars=1,resizable=0');
				newWindow.focus();
		}
function popEmailToPBScreen(pb)	{
				newWindow = window.open('edit.php?SendEmailToPB=1&nonavbar=1&PBID=' + pb, 'SendMailWindow' + pb,'width=570,height=485,directories=0,status=1,menuBar=0,scrollBars=1,resizable=0');
				newWindow.focus();
		}
function popEmailToEFScreen(pb) {
				newWindow = window.open('edit.php?SendEmailToPB=2&nonavbar=1&PBID=' + pb, 'SendMailWindow' + pb,'width=570,height=485,directories=0,status=1,menuBar=0,scrollBars=1,resizable=0');
				newWindow.focus();
		}

function popEmailNotifyScreen(eid) {
				newWindow = window.open('edit.php?SendEmailToOtherUsers=1&nonavbar=1&EntityID=' + eid, 'NotifyWindow' + eid,'width=870,height=585,directories=0,status=1,menuBar=0,scrollBars=1,resizable=0');
				newWindow.focus();
		}
function popcolorchooser(statvar)	{
				<!-- document.statvars.varcolor.blur(); -->
				newWindow = window.open('choose_col.php?var=' + statvar, 'myWindow2','width=680,height=300,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
				newWindow.focus();
		}
function popalarm(i)	{
				newWindow = window.open('date.php?nonavbar=1&eID=' + i , 'MyAlarmWindow','width=586,height=416,directories=0,status=1,menuBar=0,scrollBars=1,resizable=1');
		}
function popActivityGraph(i)	{
				newWindow = window.open('edit.php?ActivityGraph=' + i , 'MyGraphWindow' + i,'width=340,height=220,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
		}
function popActivityCustomerGraph(i)	{
				newWindow = window.open('customers.php?ActivityCustomerGraph=' + i , 'MyGraphWindow' + i,'width=340,height=220,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
		}
function popUserActivityGraph(i)	{
				newWindow = window.open('admin.php?ActivityUserGraph=' + i , 'MyGraphWindow' + i,'width=620,height=310,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
		}
function pophelp(i)	{
				newWindow = window.open('help.php?id=' + i, 'HelpWindow' + i,'width=350,height=300,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
		}
function popjournal(eid) {
				newWindow = window.open('edit.php?journal=1&eid=' + eid, 'JournalWindow' + eid,'width=850,height=300,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
		}
function popcustomerjournal(custid) {
				newWindow = window.open('edit.php?journal=1&custid=' + custid, 'JournalWindow' + custid,'width=850,height=300,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
		}
function popPDFprintwindow(i) {
				pdfwin = window.open('frame.php?target=' + i, 'pdfwin','width=200,height=120,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
}
function popPDFwindow(i) {
				pdfwin = window.open(i, 'pdfwin','width=640,height=480,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
}
function poplittlewindow(i) {
				pdfwin = window.open(i, 'littlewin','width=500,height=150,directories=0,status=0,menuBar=0,scrollBars=0,resizable=1');
}
function popAAEwindow(i) {
				pdfwin = window.open(i, 'littlewin','width=700,height=200,directories=0,status=0,menuBar=0,scrollBars=0,resizable=1');
}
function poplittlewindowWithBars(i) {
				pdfwin = window.open(i, 'littlewin','width=500,height=400,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
}
function poplittleLogwindowWithBars(i) {
				pdfwin = window.open(i, 'logwin','width=500,height=400,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
}

function increaseNotesHeight(thisTextarea, add) {
	if (thisTextarea) {
		newHeight = parseInt(thisTextarea.style.height) + add;
		thisTextarea.style.height = newHeight + "px";
	}
}

function decreaseNotesHeight(thisTextarea, subtract) {
	if (thisTextarea) {
		if ((parseInt(thisTextarea.style.height) - subtract) > 20) {
			newHeight = parseInt(thisTextarea.style.height) - subtract;
			thisTextarea.style.height = newHeight + "px";
		}
		else {
			newHeight = 30;
			thisTextarea.style.height = "30px";
		}			
	}
}

function validateDate (strDate) {

   var parsedDate = strDate.split ("-");
   if (parsedDate.length != 3) return false;
   var day, month, year;
   day = parsedDate[0];
   month = parsedDate[1]-1;
   year = parsedDate[2];
	
	var strDate2;
	strDate2 = parsedDate[1] + "-" + day + "-" + year;
  
   var objDate = new Date (strDate2);
   if (month != objDate.getMonth()) return false;
   if (day != objDate.getDate()) return false;
   if (year != objDate.getFullYear()) return false;

   return true;
} 

function autoComplete (field, select, property, forcematch) {
	var found = false;
	for (var i = 0; i < select.options.length; i++) {
	if (select.options[i][property].toUpperCase().indexOf(field.value.toUpperCase()) == 0) {
		found=true; break;
		}
	}
	if (found) { select.selectedIndex = i; }
	else { select.selectedIndex = -1; }
	if (field.createTextRange) {
		if (forcematch && !found) {
			field.value=field.value.substring(0,field.value.length-1); 
			return;
			}
		var cursorKeys ="8;46;37;38;39;40;33;34;35;36;45;";
		if (cursorKeys.indexOf(event.keyCode+";") == -1) {
			var r1 = field.createTextRange();
			var oldValue = r1.text;
			var newValue = found ? select.options[i][property] : oldValue;
			if (newValue != field.value) {
				field.value = newValue;
				var rNew = field.createTextRange();
				rNew.moveStart('character', oldValue.length) ;
				rNew.select();
				}
			}
		}
}
function setChecked ( chkBoxObj, state )
{
	  if ( state == 'y' )
		 chkBoxObj.checked=1;
	  else
		 chkBoxObj.checked=0;
}
					   
function setContents2()
 {
		document.EditEntity.content.value=EditEntity.hoelehut.options[EditEntity.hoelehut.selectedIndex].cont;
		document.EditEntity.prioty.value=EditEntity.hoelehut.options[EditEntity.hoelehut.selectedIndex].prio;
		document.EditEntity.assigneeNEW.value=EditEntity.hoelehut.options[EditEntity.hoelehut.selectedIndex].assignee;
		document.EditEntity.CRMcustomer.value=EditEntity.hoelehut.options[EditEntity.hoelehut.selectedIndex].customer;
		document.EditEntity.status.value=EditEntity.hoelehut.options[EditEntity.hoelehut.selectedIndex].status;
		document.EditEntity.deleted.value=EditEntity.hoelehut.options[EditEntity.hoelehut.selectedIndex].deleted;
		document.EditEntity.cat.value=EditEntity.hoelehut.options[EditEntity.hoelehut.selectedIndex].cat;
		document.EditEntity.duedate.value=EditEntity.hoelehut.options[EditEntity.hoelehut.selectedIndex].due;
		document.EditEntity.ownerNEW.value=EditEntity.hoelehut.options[EditEntity.hoelehut.selectedIndex].owner;

		setChecked ( document.EditEntity.obsolete , EditEntity.hoelehut.options[EditEntity.hoelehut.selectedIndex].obsolete);
		setChecked ( document.EditEntity.waiting , EditEntity.hoelehut.options[EditEntity.hoelehut.selectedIndex].waiting);
		setChecked ( document.EditEntity.deleted , EditEntity.hoelehut.options[EditEntity.hoelehut.selectedIndex].deleted);
		setChecked ( document.EditEntity.readonly , EditEntity.hoelehut.options[EditEntity.hoelehut.selectedIndex].readonly);
 }	
 function setCheckboxes(form)
{

	if (document.MassUpdate.check_status.value == 'on')
	{
		for (var i = 0; i < document.MassUpdate.elements.length; i++){    
			eval("document.MassUpdate[" + i + "].checked = false");  
		} 
		document.MassUpdate.check_status.value = 'off';
	} else {
		for (var i = 0; i < document.MassUpdate.elements.length; i++){    
			eval("document.MassUpdate[" + i + "].checked = true");  
		} 
		document.MassUpdate.check_status.value = 'on';
	}
} // end of the 'setCheckboxes()' function

function toggleDiv(divid){
    if(document.getElementById(divid).style.display == 'none'){
	document.getElementById(divid).style.display = 'block';
    }else{
	document.getElementById(divid).style.display = 'none';
    }
}
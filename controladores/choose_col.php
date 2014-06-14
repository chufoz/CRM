<?
$nonavbar=1;
include("header.inc.php");
?>

<SCRIPT LANGUAGE="JavaScript" type="text/javascript">

var SetBG = top.SetBG ;
var Tog = top.Tog ;

var colordelim = "000|003|006|009|00C|00F|030|033|036|039|03C|03F|060|063|066|069|06C|06F|090|093|096|099|09C|09F|0C0|0C3|0C6|0C9|0CC|0CF|0F0|0F3|0F6|0F9|0FC|0FF|300|303|306|309|30C|30F|330|333|336|339|33C|33F|360|363|366|369|36C|36F|390|393|396|399|39C|39F|3C0|3C3|3C6|3C9|3CC|3CF|3F0|3F3|3F6|3F9|3FC|3FF|600|603|606|609|60C|60F|630|633|636|639|63C|63F|660|663|666|669|66C|66F|690|693|696|699|69C|69F|6C0|6C3|6C6|6C9|6CC|6CF|6F0|6F3|6F6|6F9|6FC|6FF|900|903|906|909|90C|90F|930|933|936|939|93C|93F|960|963|966|969|96C|96F|990|993|996|999|99C|99F|9C0|9C3|9C6|9C9|9CC|9CF|9F0|9F3|9F6|9F9|9FC|9FF|C00|C03|C06|C09|C0C|C0F|C30|C33|C36|C39|C3C|C3F|C60|C63|C66|C69|C6C|C6F|C90|C93|C96|C99|C9C|C9F|CC0|CC3|CC6|CC9|CCC|CCF|CF0|CF3|CF6|CF9|CFC|CFF|F00|F03|F06|F09|F0C|F0F|F30|F33|F36|F39|F3C|F3F|F60|F63|F66|F69|F6C|F6F|F90|F93|F96|F99|F9C|F9F|FC0|FC3|FC6|FC9|FCC|FCF|FF0|FF3|FF6|FF9|FFC|FFF" ;

var colors = colordelim.split( "|" ) ;

function WriteColorTable( document, ncols ) {
    document.writeln( "<TABLE border=2>" ) ;

    for( var i = 0 ; i < colors.length ; i++ ) {
	if( (i % ncols) == 0 ) {
	    document.write( "\n<TR>" ) ;
	}
	var rgb = colors[i].split( "" ) ;
	var bgcol = "#"+rgb[0]+rgb[0]+rgb[1]+rgb[1]+rgb[2]+rgb[2] ;
	document.write( "<TD bgcolor=\"" + bgcol + "\">" +
		"<A href=\'javascript:Tog();\' " +
		"onMouseOver=\'SetBG(\"" + bgcol + "\");\'>" +
		"&nbsp;&nbsp;&nbsp;</A></TD>" ) ;
    }
    document.writeln( "</TABLE>" ) ;
}

// int.toString(radix) is not reliable:
// var x = 160 ; x.toString(16) ; produces ":0"??
var hexvals = new Array( "0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F" ) ;

function WriteGrayTable( document, ncols ) {
    document.writeln( "<TABLE border=2><TR>" ) ;

    // Include 0 and 255 in the range.
    var grayincr = 255.0 / (ncols-1) ;
    var grayval = 0.0 ;
    for( var i = 0 ; i < ncols ; i++ ) {
	var igray = Math.round( grayval ) ;
	var graystr = (igray < 16) ? "0"+hexvals[igray] :
		hexvals[Math.floor(igray/16)]+hexvals[igray%16] ;
	var bgcol = "#"+graystr+graystr+graystr ;
	document.write( "<TD bgcolor=\"" + bgcol + "\">" +
		"<A href=\'javascript:Tog();\' " +
		"onMouseOver=\'SetBG(\"" + bgcol + "\");\'>" +
		"&nbsp;&nbsp;&nbsp;</A></TD>" ) ;
	grayval += grayincr ;
    }
    document.writeln( "</TABLE>" ) ;
}

</SCRIPT>

</HEAD>

<BODY>

<BR>
<table><tr><td>
<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Please choose and click on a color for status variable '<? echo $var ?>'</legend>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
    WriteColorTable( document, 36 ) ;
    WriteGrayTable( document, 36 ) ;
	
</SCRIPT>


<SCRIPT LANGUAGE="JavaScript" type="text/javascript">

function Tog() {
	document.choosecol.ignored.style.background = document.choosecol.hex.value;
	document.choosecol.ignored.value = '<? echo $var; ?>';
	document.choosecol.hidden.value = document.choosecol.hex.value;
}

function doit () {
				window.opener.document.statvars.varcolor.value = document.choosecol.hidden.value;
				window.close();
}

function SetBG( color ) {
    // Could be a problem for some browsers.
    //location.reload() ;
//    document.bgColor = color ;

    document.choosecol.hex.value = color ;

}

</SCRIPT>

<table><tr><td>
<FORM NAME='choosecol'>
<INPUT type='text' name='hex' value="" size=8>
<INPUT type='text' name='ignored' value="" size=40 class='headline'>
<INPUT type='hidden' name='hidden' value="" size=40>
<INPUT type='submit' name='but' value='Accept' OnClick='javascript:doit();'>
</FORM>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
<!--
document.choosecol.ignored.value = '<? echo $var; ?>';
//-->
</SCRIPT>
</TD></TR></TABLE></FIELDSET>
</HTML>
</BODY>
</HTML>

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

AdminTabs("vpn");

$to_tabs = array("info","list","add");
$tabbs["info"] = array("admin_vpn.php" => "Informacion", "comment" => "Informacion");
$tabbs["list"] = array("admin_vpn.php?list=1" => "VPN's", "comment" => "VPN's");
$tabbs["add"]  = array("admin_vpn.php?add=1" => "A&ntilde;adir VPN", "comment" => "A&ntilde;adir VPN");

if ($_REQUEST['list'] == 1 or $_REQUEST['edit']=='1') {
    $navid = "list";
}
elseif ($_REQUEST['add'] == 1) {
    $navid = "add";
}
else{
    $navid = "info";
}
InterTabs($to_tabs, $tabbs, $navid);



MustBeAdmin();
//print "<pre>";
//print_r($_REQUEST);
//print "</pre>";


if($_REQUEST['editvpn']){
    add_edit_vpn($_REQUEST);
}
elseif($_REQUEST['deletevpn']){
    delete_vpn($_REQUEST);
}
elseif($_REQUEST['list']){
    list_vpn();
}
elseif ($_REQUEST['edit']=='1'){
    frm_vpn($vid);
}
elseif ($_REQUEST['add']=='1'){
    frm_vpn();
}
else{
    info();
}
//*****************************************************************************
function info(){
echo "
    <table width='100%' border=0>
	<tr>
	    <td width='22'>&nbsp;</td>
	    <td>
	    <table width='90%'>
		<tr>
		    <td>
		    <fieldset>
		    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Informaci&oacute;n</font></legend>

		    Desde este modulo se deber&aacute;n de administrar las VPN's para las estaciones de servicio
		    </fieldset>
		    </td>
		</tr>
	    </table>
	    </td>
	</tr>
    </table>
";
}
//*****************************************************************************
function list_vpn(){

    $sql="
    SELECT
    vpn_id as id,
    vpn_nombre as nombre,
    vpn_bdcentral as bdcentral,
    vpn_ip_dns as ip_dns
    FROM $GLOBALS[TBL_PREFIX]vpn
    WHERE vpn_status='1'
    ORDER BY vpn_nombre ASC
    ";
    $result= mcq($sql,$db);
    $vpns = array();
    while ($row=mysql_fetch_array($result)){
	$vpns[]=array(
	    "id"	    =>	$row['id'],
	    "nombre"	    =>	$row['nombre'],
	    "bdcentral"	    =>	$row['bdcentral'],
	    "ip_dns"	    =>	$row['ip_dns']
	);
    }


    echo "
    <table width='100%' border=0>
	<tr>
	    <td width='22'>&nbsp;</td>
	    <td>
	    <table width='90%'>
		<tr>
		    <td>
		    <fieldset>
		    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Listado de VPN's activas:</font></legend>

		    <table width='750' class='crm'>
		    <tr>
			<td width='50'></td>
			<td width='300' align='center'><b>Nombre</b></td>
			<td width='200' align='center'><b>BD central</b></td>
			<td width='200' align='center'><b>IP / DNS</b></td>
		    </tr>";

		    if (sizeof($vpns)>0){
			foreach($vpns as $vpn){

			    echo "
			    <tr>
			    <td align='center'><a href='admin_vpn.php?edit=1&vid=$vpn[id]'>Editar</a></td>
			    <td align='left'>$vpn[nombre]</td>
			    <td align='left'>$vpn[bdcentral]</td>
			    <td align='left'>$vpn[ip_dns]</td>
			    </tr>
			    ";
			}
		    }
		    else{
			echo "
			    <tr>
			    <td align='center' colspan=4>NO SE ENCONTRARON REGISTROS</td>
			    </tr>
			    ";
		    }
		    echo "
		    </table><br><br><br><br>
		    </fieldset>
		    </td>
		</tr>
	    </table>
	    </td>
	</tr>
    </table>
";
}
//*****************************************************************************
function frm_vpn($vid=0){

if ($vid!=0){
    $sql="
    SELECT
    vpn_id as id,
    vpn_nombre as nombre,
    vpn_bdcentral as bdcentral,
    vpn_ip_dns as ip_dns
    FROM $GLOBALS[TBL_PREFIX]vpn
    WHERE vpn_id=$vid
    ";
    $result= mcq($sql,$db);
    $datos = mysql_fetch_array($result);
    $action='edit';
    $label="Editar VPN";
    $delete="<input type='submit' name='deletevpn' value='Eliminar'>&nbsp;&nbsp;";

}
else{
    $new = true;
    $datos=array("id"=>'0','nombre'=>'','bdcentral'=>'','ip_dns'=>'');
    $action='new';
    $label="Nueva VPN";
    $delete="";
}

echo
"
<table width='100%'>
	<tr>
	    <td width='22'>&nbsp;</td>
	    <td>
	    <table width='90%'>
		<tr>
		    <td>
		    <fieldset>
		    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$label</font></legend>
		    <form name='Editgroup' method='POST'>
			<table cellspacing='0' cellpadding='4' width='700' border=1 bordercolor='#F0F0F0'>
			    <tr>
				<td>Nombre:</td>
				<td " . PrintToolTipCode('Nombre de la VPN.') ."><input type='text' name='nombre' size=70 value=\"" . str_replace("\"","'",$datos['nombre']) . "\"'></td>
			    </tr>
			    <tr>
				<td>BD central:</td>
				<td " . PrintToolTipCode('Nombre de base de datos central.') ."><input type='text' name='bdcentral' size=70 value=\"" . str_replace("\"","'",$datos['bdcentral']) . "\"'></td>
			    </tr>
			    <tr>
				<td>IP / DNS servidor central (servidor externo):</td>
				<td " . PrintToolTipCode('IP / DNS servidor central.') ."><input type='text' name='ip_dns' size=70 value=\"" . str_replace("\"","'",$datos['ip_dns']) . "\"'></td>
			    </tr>
			    <tr>
				<td colspan='2'>
				$delete<input type='submit' name='editvpn' value='Guardar'>
				<input type='hidden' name='vpnid' value='$vid'>
				<input type='hidden' name='action' value='$action'>
				</td>
			    </tr>
			</table>

		    <form>
		    </fieldset>
		    </td>
		</tr>
	    </table>
	    </td>
	</tr>
    </table>
";

}
//*****************************************************************************
function add_edit_vpn($request){
//echo "<pre>";
//print_r($request);
//echo "</pre>";
    

    if ($request['action']=='new'){
	$nombre		=   $request['nombre'];
	$bdcentral	=   $request['bdcentral'];
	$ip_dns		=   $request['ip_dns'];

	$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]vpn(vpn_nombre, vpn_bdcentral, vpn_ip_dns, vpn_status) VALUES (ucase('$nombre'),'$bdcentral','$ip_dns','1')";
	mcq($sql,$db);

    }
    else{
	$nombre		=   $request['nombre'];
	$bdcentral	=   $request['bdcentral'];
	$ip_dns		=   $request['ip_dns'];
	$vpnid		=   $request['vpnid'];
	$sql = "UPDATE $GLOBALS[TBL_PREFIX]vpn SET vpn_nombre=ucase('$nombre'), vpn_bdcentral='$bdcentral', vpn_ip_dns='$ip_dns' WHERE vpn_id='$vpnid'";
	mcq($sql,$db);
    }
    //echo $sql;
    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=admin_vpn.php?list=1\">";

}
//*****************************************************************************
function delete_vpn($request){
//echo "<pre>";
//print_r($request);
//echo "</pre>";

    $vpnid	=   $request['vpnid'];
    
    //Verificar estaciones ligadas a la VPN
    $sql = "SELECT id, custname from $GLOBALS[TBL_PREFIX]customer WHERE id_vpn = $vpnid";
    $result = mcq($sql, $db);
    if (mysql_num_rows($result)>0){
	echo "
	<table width='100%' border=0>
	<tr><td width='35'>&nbsp;</td><td><table width='90%'><tr><td>";
	echo "<b>No se puede eliminar la VPN, existen clientes asociados:</b>";
	echo "<ul>";
	while($row=mysql_fetch_array($result)){
	    echo "<li>$row[custname]</li>";
	}
	echo "</ul>
	</td></tr></table></td></tr></table>
	";
	return;
    }

    $sql = "UPDATE $GLOBALS[TBL_PREFIX]vpn SET vpn_status='0' WHERE vpn_id='$vpnid'";
    mcq($sql,$db);

    //echo $sql;
    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=admin_vpn.php?list=1\">";

}
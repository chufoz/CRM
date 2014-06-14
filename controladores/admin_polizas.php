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

AdminTabs("polizas");

$to_tabs = array("info","list","add","all");
$tabbs["info"] = array("admin_polizas.php" => "Informacion", "comment" => "Informacion");
$tabbs["list"] = array("admin_polizas.php?list=1" => "Polizas", "comment" => "Polizas");
$tabbs["add"]  = array("admin_polizas.php?add=1" => "A&ntilde;adir poliza", "comment" => "A&ntilde;adir poliza");
$tabbs["all"]  = array("admin_polizas.php?all=1&type=0&status=1" => "Polizas por cliente", "comment" => "Polizas por cliente");

if ($_REQUEST['list'] == 1 or $_REQUEST['edit']=='1') {
    $navid = "list";
}
elseif ($_REQUEST['add'] == 1) {
    $navid = "add";
}
elseif ($_REQUEST['all'] == 1 or $_REQUEST['viewall']) {
    $navid = "all";
}
else{
    $navid = "info";
}
InterTabs($to_tabs, $tabbs, $navid);



MustBeAdmin();
//print "<pre>";
//print_r($_REQUEST);
//print "</pre>";


if($_REQUEST['editpol']){
    add_edit_pol($_REQUEST);
}
elseif($_REQUEST['list']){
    list_pol();
}
elseif ($_REQUEST['edit']=='1'){
    frm_pol($pid);
}
elseif ($_REQUEST['add']=='1'){
    frm_pol();
}
elseif ($_REQUEST['all']=='1' or $_REQUEST['viewall']){
    all_policies();
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

		    Modulo de administracion de polizas.
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
function list_pol(){

    $sql="
    SELECT
    cpol_id as id,
    cpol_nombre as nombre,
    cpol_descripcion as descripcion
    FROM $GLOBALS[TBL_PREFIX]catpolizas
    ORDER BY cpol_id ASC
    ";
    $result= mcq($sql,$db);
    $polizas = array();
    while ($row=mysql_fetch_array($result)){
	$polizas[]=array(
	    "id"	    =>	$row['id'],
	    "nombre"	    =>	$row['nombre'],
	    "descripcion"   =>	$row['descripcion']
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
		    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Listado de Polizas:</font></legend>

		    <table width='650' class='crm'>
		    <tr>
			<td width='50'></td>
			<td width='300' align='center'><b>Nombre</b></td>
			<td width='300' align='center'><b>Descripcion</b></td>
		    </tr>";

		    if (sizeof($polizas)>0){
			foreach($polizas as $poliza){

			    echo "
			    <tr>
			    <td align='center'><a href='admin_polizas.php?edit=1&pid=$poliza[id]'>Editar</a></td>
			    <td align='left'>$poliza[nombre]</td>
			    <td align='left'>$poliza[descripcion]</td>
			    </tr>
			    ";
			}
		    }
		    else{
			echo "
			    <tr>
			    <td align='center' colspan=3>NO SE ENCONTRARON REGISTROS</td>
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
function frm_pol($pid=0){

if ($pid!=0){
    $sql="
    SELECT
    cpol_id as id,
    cpol_nombre as nombre,
    cpol_descripcion as descripcion
    FROM $GLOBALS[TBL_PREFIX]catpolizas
    WHERE cpol_id=$pid
    ";
    $result= mcq($sql,$db);
    $datos = mysql_fetch_array($result);
    $action='edit';
    $label="Editar Poliza";

}
else{
    $new = true;
    $datos=array("id"=>'0','nombre'=>'','descripcion'=>'');
    $action='new';
    $label="Nueva Poliza";
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
				<td " . PrintToolTipCode('Nombre de la Poliza.') ."><input type='text' name='nombre' size=70 value=\"" . str_replace("\"","'",$datos['nombre']) . "\"'></td>
			    </tr>
			    <tr>
				<td>Descripcion:</td>
				<td " . PrintToolTipCode('Descripcion.') ."><textarea name='descripcion' cols=50 rows=10>" . str_replace("\"","'",$datos['descripcion']) . "</textarea></td>
			    </tr>
			    <tr>
				<td colspan='2'>
				<input type='submit' name='editpol' value='Guardar'>
				<input type='hidden' name='pid' value='$pid'>
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
function add_edit_pol($request){
//echo "<pre>";
//print_r($request);
//echo "</pre>";


    if ($request['action']=='new'){
	$nombre		=   $request['nombre'];
	$descripcion	=   $request['descripcion'];

	$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]catpolizas(cpol_nombre, cpol_descripcion) VALUES (ucase('$nombre'),'$descripcion')";
	mcq($sql,$db);

    }
    else{
	$nombre		=   $request['nombre'];
	$descripcion	=   $request['descripcion'];
	$pid		=   $request['pid'];
	$sql = "UPDATE $GLOBALS[TBL_PREFIX]catpolizas SET cpol_nombre=ucase('$nombre'), cpol_descripcion='$descripcion' WHERE cpol_id='$pid'";
	mcq($sql,$db);
    }
    //echo $sql;
    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=admin_polizas.php?list=1\">";

}
//*****************************************************************************
function all_policies(){

    $tipos = GetCatPolizas();

    //Parametros de busqueda
    $params['status']=$_REQUEST['status'];
    $params['type']=$_REQUEST['type'];
    $polizas = GetAllPolicies($params);
    //printarray($polizas);

    $st_array=array(
		array('val'=>'2','label'=>'Todas'),
		array('val'=>'1','label'=>'Activa'),
		array('val'=>'0','label'=>'Cancelada / Vencida')
	    );
    echo "
    <fieldset>
    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Polizas por cliente:&nbsp;&nbsp;</legend>
    <form name='policies' method='POST'>
	<table cellspacing='0' cellpadding='4' width='400' border=1 bordercolor='#F0F0F0'>
	    <tr>
		<td>Tipo:</td>
		<td>
		    <select name='type'><option value='0'>Todos</option>";
		    foreach($tipos as $tipo){
			$sel = $tipo['id']==$params['type'] ? 'selected' : '';
			echo "<option value='$tipo[id]' $sel>$tipo[nombre]</option>";
		    }
		    echo "
		    </select>
		</td>
		<td>Status:</td>
		<td><select name='status'>";
		foreach($st_array as $st){
			$sel = $st['val']==$params['status'] ? 'selected' : '';
			echo "<option value='$st[val]' $sel>$st[label]</option>";
		    }
		echo "
		    </select>
		</td>
		<td colspan='2'>
		<input type='submit' name='viewall' value='Buscar'>
		</td>
	    </tr>
	</table>
    <form><br><br>

    <table border=0 width=1000 class='crm'>
	<tr>
	    <td align='center'><b>CLIENTE</b></td>
	    <td align='center'><b>POLIZA</b></td>
	    <td align='center'><b>DESDE</b></td>
	    <td align='center'><b>HASTA</b></td>
	    <td align='center'><b>MODO PAGO</b></td>
	    <td align='center'><b>CONTRATO</b></td>
	    <td align='center'><b>ESTATUS</b></td>
	</tr>
	";
	if (sizeof($polizas)>0){
	    foreach($polizas as $poliza){
		$status = $poliza['status'] == '1' ? "<font color='green'><b>ACTIVA</b>" : "<font color='red'></b>CANCELADA / VENCIDA</b></font>";
		echo "
		    <tr>
			<td>$poliza[custname]</td>
			<td>$poliza[nombre]</td>
			<td>$poliza[fini]</td>
			<td>$poliza[ffin]</td>
			<td>$poliza[modopago]</td>
			<td>$poliza[contrato]</td>
			<td align='center'>$status</td>
		    </tr>
		";
	    }
	}
	else{
	    echo "<tr><td align='center' colspan='7'>No se encontraron registros</td></tr>";
	}
	echo "
    </table>
    </fieldset><br>";
}
//*****************************************************************************
function GetAllPolicies($params){


    $filter = "";
    if (isset($params['status'])){
	if ($params['status']=='1' or $params['status']=='0'){
	$filter.=" AND pol_status='$params[status]'";
	}
    }
    if (isset($params['type'])){
	if ($params['type']!=0){
	$filter.=" AND pol_cpid='$params[type]'";
	}
    }

    $sql = "
    SELECT
    a.pol_id as id,
    a.pol_custid as custid,
    a.pol_cpid as cpid,
    b.cpol_nombre as nombre,
    DATE_FORMAT(a.pol_fini,'%d/%m/%Y') as fini,
    DATE_FORMAT(a.pol_ffin,'%d/%m/%Y') as ffin,
    CASE a.pol_modopago
	WHEN 'A' THEN 'ANUAL'
	WHEN 'S' THEN 'SEMESTRAL'
	WHEN 'T' THEN 'TRIMESTRAL'
	WHEN 'M' THEN 'MENSUAL'
	WHEN 'P' THEN 'PAGADO'
    END as modopago,
    a.pol_modopago as modo_pago,
    a.pol_contrato as contrato,
    DATE_FORMAT(a.pol_ultpago,'%d/%m/%Y') as ultimopago,
    DATE_FORMAT(a.pol_proxpago,'%d/%m/%Y') as proximopago,
    a.pol_status as status,
    a.pol_status_venc as vencimiento,
    a.pol_active as active,
    c.custname as custname
    FROM CRMpolizas as a
    INNER JOIN CRMcatpolizas as b on a.pol_cpid=b.cpol_id
    INNER JOIN CRMcustomer as c on a.pol_custid = c.id
    WHERE 1
    $filter
    ";
    $result = mcq_array($sql);
    return $result;
}

//*****************************************************************************
function GetCatPolizas(){

    $sql = "SELECT cpol_id as id, cpol_nombre as nombre FROM CRMcatpolizas";
    $result = mcq($sql,$db);

    $data = array();
    while ($row=mysql_fetch_array($result)){
	$data[]=array('id'=>$row['id'],'nombre'=>$row['nombre']);
    }
    return $data;

}
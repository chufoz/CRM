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

AdminTabs("groups");

$to_tabs = array("info","listes","addes","listspr","addspr");
$tabbs["info"] = array("admin_groups.php" => "Informacion", "comment" => "Informacion");
$tabbs["listes"] = array("admin_groups.php?list=1&type=0" => "Grupos de E.S.", "comment" => "Grupos de E.S.");
$tabbs["addes"]  = array("admin_groups.php?add=1&type=0" => "A&ntilde;adir grupo de E.S.", "comment" => "A&ntilde;adir grupo de E.S..");
$tabbs["listspr"] = array("admin_groups.php?list=1&type=1" => "Grupos Soporte", "comment" => "Grupos de Soporte.");
$tabbs["addspr"]  = array("admin_groups.php?add=1&type=1" => "A&ntilde;adir grupo Soporte", "comment" => "A&ntilde;adir grupo de Soporte.");

if ($_REQUEST['list'] == 1 or $_REQUEST['edit']=='1') {
    $navid = $_REQUEST['type']=='0' ? "listes" : "listspr";
}
elseif ($_REQUEST['add'] == 1) {
    $navid = $_REQUEST['type'] == '0' ? "addes" : "addspr";
}
else{
    $navid = "info";
}
InterTabs($to_tabs, $tabbs, $navid);



MustBeAdmin();
//print "<pre>";
//print_r($_REQUEST);
//print "</pre>";


if($_REQUEST['editgroup']){
    add_edit_group($_REQUEST);
}
elseif($_REQUEST['deletegroup']){
    delete_group($_REQUEST);
}
elseif($_REQUEST['list']){
    list_groups();
}
elseif ($_REQUEST['edit']=='1'){
    frm_group($gid);
}
elseif ($_REQUEST['add']=='1'){
    frm_group();
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

		    Desde este modulo se deber&aacute;n de dar de alta los grupos de estaciones de servicio, asi como grupos de soporte
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
function list_groups(){
    
    $type=$_REQUEST['type'];
    $label = $type=='0' ? "E.S." : "Soporte";

    $sql="
    SELECT
    grp_id as id,
    grp_nombre as nombre,
    grp_admin as administrador,
    grp_oper as operativo
    FROM $GLOBALS[TBL_PREFIX]grupos
    WHERE grp_active='1' AND grp_type='$type'
    ORDER BY grp_nombre ASC
    ";
    $result= mcq($sql,$db);
    $grupos = array();
    while ($row=mysql_fetch_array($result)){
	$grupos[]=array(
	    "id"	    =>	$row['id'],
	    "nombre"	    =>	$row['nombre'],
	    "administrador" =>	$row['administrador'],
	    "operativo"	    =>	$row['operativo']
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
		    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Listado de grupos: $label</font></legend>

		    <table width='700' class='crm'>
		    <tr>
			<td width='50'></td>
			<td width='650' align='center'><b>Nombre</b></td>
		    </tr>";

		    if (sizeof($grupos)>0){
			foreach($grupos as $grupo){

			    echo "
			    <tr>
			    <td align='center'><a href='admin_groups.php?edit=1&type=$type&gid=$grupo[id]'>Editar</a></td>
			    <td align='left'>$grupo[nombre]</td>
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
function frm_group($gid=0){

$type = $_REQUEST['type'];
$typelbl = $type=='0' ? "E.S." : "Soporte";

if ($gid!=0){
    $sql="
    SELECT
    grp_id as id,
    grp_nombre as nombre,
    grp_admin as administrador,
    grp_oper as operativo
    FROM $GLOBALS[TBL_PREFIX]grupos
    WHERE grp_id=$gid AND grp_type='$type'
    ";
    $result= mcq($sql,$db);
    $datos = mysql_fetch_array($result);
    $action='edit';
    $label="Editar grupo: $typelbl";
    $delete="<input type='submit' name='deletegroup' value='Eliminar'>&nbsp;&nbsp;";

}
else{
    $new = true;
    $datos=array("id"=>'0','nombre'=>'','administrativo'=>'','operativo'=>'');
    $action='new';
    $label="Nuevo grupo: $typelbl";
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
				<td " . PrintToolTipCode('Nombre del grupo.') ."><input type='text' name='groupname' size=70 value=\"" . str_replace("\"","'",$datos['nombre']) . "\"'></td>
			    </tr>
			    <tr>
				<td valign=top>Contacto Administrativo:</td>
				<td " . PrintToolTipCode('Contacto Administrativo.') ."><textarea name='contact_adm' rows=5 cols=50>".str_replace("\"","'",$datos['administrador'])."</textarea></td>
			    </tr>
			    <tr>
				<td valign=top>Contacto Operativo:</td>
				<td " . PrintToolTipCode('Contacto Operativo.') ."><textarea name='contact_oper' rows=5 cols=50>".str_replace("\"","'",$datos['operativo'])."</textarea></td>
			    </tr>
			    <tr>
				<td colspan='2'>
				$delete<input type='submit' name='editgroup' value='Guardar'>
				<input type='hidden' name='groupid' value='$gid'>
				<input type='hidden' name='type' value='$type'>
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
function add_edit_group($request){
//echo "<pre>";
//print_r($request);
//echo "</pre>";
    $type=$_REQUEST['type'];

    if ($request['action']=='new'){
	$nombre		=   $request['groupname'];
	$contact_adm	=   $request['contact_adm'];
	$contact_oper	=   $request['contact_oper'];

	$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]grupos(grp_nombre, grp_admin, grp_oper, grp_type) VALUES (ucase('$nombre'),ucase('$contact_adm'),ucase('$contact_oper'),'$type')";
	mcq($sql,$db);

    }
    else{
	$nombre		=   $request['groupname'];
	$contact_adm	=   $request['contact_adm'];
	$contact_oper	=   $request['contact_oper'];
	$groupid	=   $request['groupid'];
	$sql = "UPDATE $GLOBALS[TBL_PREFIX]grupos SET grp_nombre=ucase('$nombre'), grp_admin=ucase('$contact_adm'), grp_oper=ucase('$contact_oper') WHERE grp_id='$groupid' AND grp_type='$type'";
	mcq($sql,$db);
    }
    //echo $sql;
    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=admin_groups.php?list=1&type=$type\">";
    
}
//*****************************************************************************
function delete_group($request){
//echo "<pre>";
//print_r($request);
//echo "</pre>";

    $groupid	=   $request['groupid'];
    $type = $request['type'];

    $field = $type='0' ? 'id_customer_group' : 'id_support_group';

    //Verificar estaciones ligadas al grupo
    $sql = "SELECT id, custname from $GLOBALS[TBL_PREFIX]customer WHERE $field = $groupid";
    $result = mcq($sql, $db);
    if (mysql_num_rows($result)>0){
	echo "
	<table width='100%' border=0>
	<tr><td width='35'>&nbsp;</td><td><table width='90%'><tr><td>";
	echo "<b>No se puede eliminar el grupo, existen clientes asociados:</b>";
	echo "<ul>";
	while($row=mysql_fetch_array($result)){
	    echo "<li>$row[custname]</li>";
	}
	echo "</ul>
	</td></tr></table></td></tr></table>
	";
	return;
    }
    
    $sql = "UPDATE $GLOBALS[TBL_PREFIX]grupos SET grp_active='0' WHERE grp_id='$groupid' and grp_type='$type'";
    mcq($sql,$db);

    //echo $sql;
    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=admin_groups.php?list=1&type=$type\">";

}
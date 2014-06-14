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
<script type="text/javascript" LANGUAGE="javascript" SRC="cookies.js"></script>
<?

AdminTabs("plantillas");

$to_tabs = array("list","add");
$tabbs["list"] = array("admin_plantillas.php?list=1" => "Plantillas", "comment" => "Plantillas");
$tabbs["add"]  = array("admin_plantillas.php?add=1" => "A&ntilde;adir plantilla", "comment" => "A&ntilde;adir plantilla");

if ($_REQUEST['list'] == 1 or $_REQUEST['edit']=='1') {
    $navid = "list";
}
elseif ($_REQUEST['add'] == 1) {
    $navid = "add";
}
else{
    $navid = "list";
}
InterTabs($to_tabs, $tabbs, $navid);



MustBeAdmin();
//print "<pre>";
//print_r($_REQUEST);
//print "</pre>";

if($_REQUEST['edittemplate']){
    add_edit_template($_REQUEST);
}
elseif($_REQUEST['list']){
    list_templates();
}
elseif ($_REQUEST['edit']=='1'){
    frm_template($_REQUEST['tid']);
}
elseif ($_REQUEST['add']=='1'){
    frm_template();
}
else{
    list_templates();
}

//*****************************************************************************
function list_templates(){

    $sql="
    SELECT
    id_template as id,
    nombre as nombre,
    estatus as descripcion
    FROM CRMtemplates_main
    WHERE estatus='1'
    ORDER BY nombre ASC
    ";
    $result= mcq($sql,$db);
    $templates = array();
    while ($row=mysql_fetch_array($result)){
	$templates[]=array(
	    "id"	    =>	$row['id'],
	    "nombre"	    =>	$row['nombre'],
	    "estatus"	    =>	$row['estatus']
	);
    }

    echo "
    <table width='100%' border=0>
	<tr>
	    <td width='22'>&nbsp;</td>
	    <td>
	    <table width='800'>
		<tr>
		    <td>
		    <fieldset>
		    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Listado de Plantillas:</font></legend>

		    <table width='650' class='crm'>
		    <tr>
			<td width='50'></td>
			<td width='400' align='center'><b>Nombre</b></td>
		    </tr>";

		    if (sizeof($templates)>0){
			foreach($templates as $template){

			    echo "
			    <tr>
			    <td align='center'><a href='admin_plantillas.php?edit=1&tid=$template[id]'>Editar</a></td>
			    <td align='left'>$template[nombre]</td>
			    </tr>
			    ";
			}
		    }
		    else{
			echo "
			    <tr>
			    <td align='center' colspan='2'>NO SE ENCONTRARON REGISTROS</td>
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
function frm_template($tid=0){

if ($tid!=0){
    $sql="
    SELECT
    id_template as id,
    nombre, estatus,
    category as category,
    owner as owner,
    asignee as asignee,
    content as content
    FROM CRMtemplates_main
    WHERE id_template=$tid and estatus='1'
    ";
    $result= mcq($sql,$db);
    $datos = mysql_fetch_array($result);

    $sql="
    SELECT
    id,
    id_template,
    category,
    owner,
    asignee,
    content,
    estatus
    FROM CRMtemplates_entity
    WHERE id_template=$tid and estatus='1'
    ";
    $result= mcq($sql,$db);
    $entities=array();
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	$entities[]=$row;
    }


    $action='edit';
    $label="Editar Plantilla";

}
else{
    $new = true;
    $datos=array("id"=>'0','nombre'=>'','category'=>'','owner'=>'','asignee'=>'','content'=>'');
    $action='new';
    $label="Nueva Plantilla";
}

/*
$priorities = GetPriorities();
$list_priorities ="";
foreach ($priorities as $key=>$priority){
    $color = GetPriorityColor($priority);
    $list_priorities.="<option style='background:$color' value='$key'>$priority</option>";
}
*/
$sql = "SELECT id,FULLNAME FROM CRMloginusers WHERE LEFT(FULLNAME,3) <> '@@@' AND active<>'no' ORDER BY FULLNAME";
$users = mcq_array($sql);
$list_users="";
foreach($users as $key=>$user){
    $list_users.="<option value='$user[id]'>$user[FULLNAME]</option>";
}

//propietario y asignado de tarea principal
$owner_main="";
foreach($users as $key=>$user){
    $sel = $datos['owner'] == $user['id'] ? 'selected' : '';
    $owner_main.="<option value='$user[id]' $sel>$user[FULLNAME]</option>";
}

$asignee_main="";
foreach($users as $key=>$user){
    $sel = $datos['asignee'] == $user['id'] ? 'selected' : '';
    $asignee_main.="<option value='$user[id]' $sel>$user[FULLNAME]</option>";
}


//<editor-fold defaultstate="collapsed" desc="Javascript">
?>
<script type="text/javascript">
    
    var priorities = "<?php echo $list_priorities;?>";
    var users = "<?php echo $list_users;?>";

    $(document).ready(function(){
	$("#addentity").click(function(){
	    var newTr =
	    "<tr><td>"+
		"<br><table border=0 class='none' style='border: 2px solid #999;'>"+
		    "<tr><td colspan='5' align='left'><img src='imgs/details_close.png' alt='' title='Eliminar' style='cursor:pointer'></td></tr>"+
		    "<tr>"+
			"<td width='10'>&nbsp;</td>"+
			"<td align='left'><b>Categoria:</b></td>"+
			"<td><b>Propietario:</b></td>"+
			"<td><b>Asignado:</b></td>"+
			"<td width='10'>&nbsp;</td>"+
		    "</tr>"+
		    "<tr>"+
			"<td width='10'>&nbsp;</td>"+
			"<td><input type='hidden' name='id[]' value='0'><input type='text' name='category[]' size='50' maxlength='150'></td>"+
			"<td><select name='owner[]'>"+users+"</td>"+
			"<td><select name='asignee[]'>"+users+"</td>"+
			"<td width='10'>&nbsp;</td>"+
		    "</tr>"+
		    "<tr>"+
			"<td width='10'>&nbsp;</td>"+
			"<td colspan='3' align='left'><b>Comentarios:</b></td>"+
			"<td width='10'>&nbsp;</td>"+
		    "</tr>"+
		    "<tr>"+
			"<td width='10'>&nbsp;</td>"+
			"<td colspan='3' align='left'><textarea style='height: 70' rows='10' cols='90' name='content[]' wrap='virtual' class='txt'></textarea></td>"+
			"<td width='10'>&nbsp;</td>"+
		    "</tr>"+
		    "<tr><td colspan='5'>&nbsp;</td></tr>"+
		"</table><br><br>"+
	    "</td></tr>";
	    $("#entities tbody:first").append(newTr);
	});

	$(document).delegate("#entities tbody tr img","click",function(){
	    var nTd = $(this).parent('td');
	    var nTbl = nTd.parents('table:first')
	    var nTr = nTbl.parents('tr:first');
	    nTr.remove();
	    return false;
	});

    });
</script>
<?php
//</editor-fold>

echo"
<table width='100%' border=0>
	<tr>
	    <td width='22'>&nbsp;</td>
	    <td>
	    <table width='800' border=0>
		<tr>
		    <td>
		    <fieldset>
		    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$label</font></legend>
		    <form name='Edittemplate' method='POST' autocomplete='off'>
			<table cellspacing='0' cellpadding='4' width='800' border=1 bordercolor='#F0F0F0'>
			    <tr>
				<td>Nombre:</td>
				<td><input type='text' name='nombre' size=70 value=\"" . str_replace("\"","'",$datos['nombre']) . "\"'></td>
			    </tr>
			    <tr><td colspan='2' align='left'><b><br>TAREA PRINCIPAL</b></td></tr>
			    <tr>
				<td colspan=2>
				    <table border=0 class='none' style='border: 2px solid #800000;'>
					<tr><td colspan='5' align='center'>&nbsp;</td></tr>
					<tr>
					    <td width='10'>&nbsp;</td>
					    <td><b>Categoria:</b></td>
					    <td><b>Propietario:</b></td>
					    <td><b>Asignado:</b></td>
					    <td width='10'>&nbsp;</td>
					</tr>
					<tr>
					    <td width='10'>&nbsp;</td>
					    <td><input type='text' name='category_main' size='50' maxlength='150' value='$datos[category]'></td>
					    <td><select name='owner_main'>$owner_main</td>
					    <td><select name='asignee_main'>$asignee_main</td>
					    <td width='10'>&nbsp;</td>
					</tr>
					<tr>
					    <td width='10'>&nbsp;</td>
					    <td colspan='3' align='left'><b>Comentarios:</b></td>
					    <td width='10'>&nbsp;</td>
					</tr>
					<tr>
					    <td width='10'>&nbsp;</td>
					    <td colspan='3' align='left'><textarea style='height: 70' rows='10' cols='90' name='content_main' wrap='virtual' class='txt' >$datos[content]</textarea></td>
					    <td width='10'>&nbsp;</td>
					</tr>
					<tr><td colspan='5'>&nbsp;</td></tr>
				    </table>
				</td>
			    <tr>
			    <tr>
				<td colspan='2'>
				<fieldset>
				<legend><b>TAREAS HIJAS:</b></legend>
				<table id='entities'>
				    <tbody>";
				    foreach ($entities as $entity){

					$owners="";
					foreach($users as $key=>$user){
					    $sel = $entity['owner'] == $user['id'] ? 'selected' : '';
					    $owners.="<option value='$user[id]' $sel>$user[FULLNAME]</option>";
					}

					$asignees="";
					foreach($users as $key=>$user){
					    $sel = $entity['asignee'] == $user['id'] ? 'selected' : '';
					    $asignees.="<option value='$user[id]' $sel>$user[FULLNAME]</option>";
					}

					echo "
					<tr><td>
					    <br>
					    <table border=0 class='none' style='border: 2px solid #999;'>
						<tr><td colspan='5'>&nbsp;</td></tr>
						<tr>
						    <td width='10'>&nbsp;</td>
						    <td><b>Categoria:</b></td>
						    <td><b>Propietario:</b></td>
						    <td><b>Asignado:</b></td>
						    <td width='10'>&nbsp;</td>
						</tr>
						<tr>
						    <td width='10'>&nbsp;</td>
						    <td><input type='hidden' name='id[$entity[id]]' value='$entity[id]'><input type='text' name='category[$entity[id]]' size='50' maxlength='150' value='$entity[category]'></td>
						    <td><select name='owner[$entity[id]]'>$owners</td>
						    <td><select name='asignee[$entity[id]]'>$asignees</td>
						    <td width='10'>&nbsp;</td>
						</tr>
						<tr>
						    <td width='10'>&nbsp;</td>
						    <td colspan='3' align='left'><b>Comentarios:</b></td>
						    <td width='10'>&nbsp;</td>
						</tr>
						<tr>
						    <td width='10'>&nbsp;</td>
						    <td colspan='3' align='left'><textarea style='height: 70' rows='10' cols='90' name='content[$entity[id]]' wrap='virtual' class='txt' >$entity[content]</textarea></td>
						    <td width='10'>&nbsp;</td>
						</tr>
						<tr>
						    <td width='10'>&nbsp;</td>
						    <td colspan='3' align='left'><input type='checkbox' name='delete[$entity[id]]'><b>Eliminar</b></td>
						    <td width='10'>&nbsp;</td>
						</tr>
						<tr><td colspan='5'>&nbsp;</td></tr>
					    </table>
					    <br>
					    <br>
					</td></tr>";
				    }
				    echo "
				    </tbody>
				</table>
				</fieldset>
				</td>
			    </tr>
			    <tr>
				<td colspan='2' align='right'>
				    <input type='button' id='addentity' name='addentity' value='Agregar tarea hija'>
				</td>
			    </tr>
			    <tr>
				<td colspan='2' align='left'>
				<input type='submit' name='edittemplate' value='Guardar Plantilla'>
				<input type='hidden' name='tid' value='$tid'>
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
    </table><br><br><br><br>
";

}
//*****************************************************************************
function add_edit_template($request){

    if ($request['action']=='new'){
	$nombre	    = $request['nombre'];
	$category   = $request['category_main'];
	$owner	    = $request['owner_main'];
	$asignee    = $request['asignee_main'];
	$content    = $request['content_main'];

	$categorias	= $request['category'];
	$propietario	= $request['owner'];
	$asignado	= $request['asignee'];
	$contenido	= $request['content'];

	$sql = "INSERT INTO CRMtemplates_main(nombre,estatus,category,owner,asignee,content) VALUES (ucase('$nombre'),'1','$category','$owner','$asignee','$content')";
	mcq($sql,$db);
	$tid = mysql_insert_id();

	foreach($categorias as $key => $value){
	    $sql = "INSERT INTO CRMtemplates_entity(
		    id_template,
		    category,
		    owner,
		    asignee,
		    content,
		    estatus
		    ) VALUES (
		    $tid,
		    '".$categorias[$key]."',
		    ".$propietario[$key].",
		    ".$asignado[$key].",
		    '".$contenido[$key]."',
		    '1'
		    )";
	    mcq($sql,$db);
	}

    }
    else{
	$nombre		= $request['nombre'];
	$category	= $request['category_main'];
	$owner		= $request['owner_main'];
	$asignee	= $request['asignee_main'];
	$content	= $request['content_main'];
	$tid		= $request['tid'];

	$ids		= $request['id'];
	$categorias	= $request['category'];
	$propietario	= $request['owner'];
	$asignado	= $request['asignee'];
	$contenido	= $request['content'];
	$borrado	= $request['delete'];
	
	$nombre		=   $request['nombre'];
	$descripcion	=   $request['descripcion'];
	$pid		=   $request['pid'];

	//Actualizacion de la plantilla
	$sql = "UPDATE CRMtemplates_main SET nombre=UCASE('$nombre'), category='$category', owner='$owner', asignee='$asignee', content='$content' WHERE id_template=$tid";
	mcq($sql,$db);

	//Actualizacion de entidades
	foreach ($ids as $key=>$id){

	    if ($id==0){//Entidad nueva
		$sql = "INSERT INTO CRMtemplates_entity(
			id_template,category,owner,asignee,content,estatus) VALUES (
			$tid,'".$categorias[$key]."',".$propietario[$key].",".$asignado[$key].",'".$contenido[$key]."','1')";
	    }
	    else{//Entidad editada

		$status = isset($borrado[$key]) ? '0' : '1';

		$sql = "UPDATE CRMtemplates_entity SET
			category='".$categorias[$key]."', owner=".$propietario[$key].", asignee=".$asignado[$key].",
			content='".$contenido[$key]."', estatus='$status'
			WHERE id=$id AND id_template=$tid";
	    }
	    
	    mcq($sql,$db);
	}
	
	
    }
    //echo $sql;
    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=admin_plantillas.php?list=1\">";

}


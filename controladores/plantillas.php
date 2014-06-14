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


if ($_REQUEST['btn_continuar']){
    template_form($_REQUEST);
}
elseif ($_REQUEST['submit_template']){
    process_template();
}
elseif($_REQUEST['list']){
    list_templates();
}
elseif ($_REQUEST['select']=='1'){
    template_tasks($_REQUEST['tid']);
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
		    <br><br><br>
		    <table width='650' class='crm'>
		    <tr>
			<td width='50'></td>
			<td width='400' align='center'><b>Nombre</b></td>
		    </tr>";

		    if (sizeof($templates)>0){
			foreach($templates as $template){

			    echo "
			    <tr>
			    <td align='center'><a href='plantillas.php?select=1&tid=$template[id]'>Seleccionar</a></td>
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
function template_tasks($tid){

    $sql="
    SELECT
    id_template as id, nombre, estatus, category as category, owner as owner, asignee as asignee, content as content
    FROM CRMtemplates_main
    WHERE id_template=$tid and estatus='1'
    ";
    $result= mcq($sql,$db);
    $datos = mysql_fetch_array($result,MYSQL_ASSOC);

    $sql="
    SELECT
    id,id_template,category,owner,asignee,content, estatus
    FROM CRMtemplates_entity
    WHERE id_template=$tid and estatus='1'
    ";
    $result= mcq($sql,$db);
    $entities=array();
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	$entities[]=$row;
    }
    //var_export($datos);
    echo "
    <form name='select_entities' method='POST' autocomplete='off'>
    <table width='100%' border=0>
	<tr>
	    <td width='22'>&nbsp;</td>
	    <td>
	    <table width='800'>
		<tr>
		    <td>
		    <fieldset>
		    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Plantillas:</font></legend>
		    <br><br><br>
		    <table width=650><tr><td align='center'><b>SELECCION DE TAREAS</b></td></tr></table>
		    <table width='650' class='crm' style='margin: 20px;'>
		    <tr>
			<td align='center'>
			    <input type='hidden' name='tid' value='$tid'>
			    <font size='+1'><b>Plantilla: </b>$datos[nombre]</font><br><br>
			</td>
		    </tr>
		    <tr>
			<td align='left'>
			    <b>Tarea principal: </b>$datos[category]
			</td>
		    </tr>
		    <tr>
			<td align='left'><br><b>Seleccione tareas hijas:</b></td>
		    </tr>
		    ";

		    if (sizeof($entities)>0){
			foreach($entities as $entity){
			    echo "<tr><td align='left'><input type='checkbox' name='tasks[]' value='$entity[id]' checked>&nbsp; $entity[category]</td></tr>";
			}
			echo "<tr><td align='center'><input type='submit' name='btn_continuar' value='Continuar'></td></tr>";
		    }
		    else{
			echo "<tr><td align='center'>NO SE ENCONTRARON REGISTROS</td></tr>";
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
    </form>
    ";
}
//*****************************************************************************
function template_form($request){
    
    $tid=$request['tid'];
    $tasks = isset($request['tasks']) ? $request['tasks'] : array();
    $priorities = GetPriorities();
    
    $list_priorities ="";
    foreach ($priorities as $key=>$priority){
	$color = GetPriorityColor($priority);
	$list_priorities.="<option style='background:$color' value='$key'>$priority</option>";
    }
    $GLOBALS['list_priorities']=$list_priorities;

    $sql = "SELECT id,FULLNAME FROM CRMloginusers WHERE LEFT(FULLNAME,3) <> '@@@' AND active<>'no' ORDER BY FULLNAME";
    $users = mcq_array($sql);
    $GLOBALS['users']=$users;

    $sql="
    SELECT
    id_template as id, nombre, estatus, category as category, owner as owner, asignee as asignee, content as content
    FROM CRMtemplates_main
    WHERE id_template=$tid and estatus='1'
    ";
    $result= mcq($sql,$db);
    $datos = mysql_fetch_array($result,MYSQL_ASSOC);

    $sql="
    SELECT
    id,id_template,category,owner,asignee,content, estatus
    FROM CRMtemplates_entity
    WHERE id_template=$tid and estatus='1'
    ";
    $result= mcq($sql,$db);
    $entities=array();
    while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	$entities[]=$row;
    }

    //Formulario
    ?>
    <form name='entities' method='POST' autocomplete='off'>
    <table width='100%' border=0>
	<tr>
	    <td width='22'>&nbsp;</td>
	    <td>
	    <table width='1000'>
		<tr>
		    <td>
			<fieldset>
			<legend>&nbsp;<img src='crmlogosmall.gif' alt="">&nbsp;&nbsp;<font size='+1'>Plantilla:</font></legend>
			<table width="90%">
			    <tr>
				<td>
				<?php
				entity_form_main($datos);
				foreach($entities as $entity){
				    if (in_array($entity['id'], $tasks)){
					entity_form_children($entity);
				    }
				}
				?>
				<table width="90%">
				    <tr>
				    <td><input type='submit' id="submit_template" name="submit_template" value='Guardar en la base de datos'></td>
				    </tr>
				</table>
				</td>
			    </tr>
			</table>
			</fieldset>
		    </td>
		</tr>
	    </table>
	    </td>
	</tr>
    </table>
    </form>
    <br><br><br><br>
    <div id='dialog' title=''></div>
    <?
}
//******************************************************************************
function entity_form_main($datos){

    $list_priorities = $GLOBALS['list_priorities'];
    $users = $GLOBALS['users'];

    $owner="";
    foreach($users as $key=>$user){
	$sel = $datos['owner'] == $user['id'] ? 'selected' : '';
	$owner.="<option value='$user[id]' $sel>$user[FULLNAME]</option>";
    }

    $asignee="";
    foreach($users as $key=>$user){
	$sel = $datos['asignee'] == $user['id'] ? 'selected' : '';
	$asignee.="<option value='$user[id]' $sel>$user[FULLNAME]</option>";
    }


    $conn_db = array('host' => $GLOBALS['host'][0], 'user' => $GLOBALS['user'][0], 'pass' => $GLOBALS['pass'][0], 'database' => $GLOBALS['database'][0]);
    $conn_db = base64_encode(serialize($conn_db));
    ?>
    <script type='text/javascript'>
	$(document).ready(function(){
	    var cnn='<?php echo $conn_db;?>';
	    
	    var info = function (eid,cnn){
		var content;
		$.ajax({
		    async: false,
		    url: "autocomplete.php?parent=1",
		    dataType: 'json',
		    cache: false,
		    data:{
			eid:eid,
			cnn:cnn
		    },
		    success: function(result) {
			if (result){
			    content = result;
			}
		    }
		});
		return content;
	    };

	    $("#dialog").dialog({
		bgiframe: true,
		autoOpen: false,
		modal: true,
		minWidth:650,
		closeOnEscape: true,
		resizable:false,
		draggable:false
	    });

	    $("#CRMcustomer_name").focus()
	    $("#CRMcustomer_name").autocomplete2("autocomplete.php?customer=1&cdb="+cnn,{
		width: 700,
		matchContains: true,
		selectFirst: false
	    }).result(function(event, data, formatted) {
		$("#CRMcustomer").val(data[1]),
		$("#CRMcustomer_name").attr('disabled','disabled');
		$("#clean").attr('src','imgs/delete.png');
	    });

	    $("#clean").click(function(){
		$("#clean").attr('src','imgs/nodelete.png');
		$("#CRMcustomer").val(''),
		$("#CRMcustomer_name").attr('disabled',false);
		$("#CRMcustomer_name").val('');
		$("#CRMcustomer_name").focus();
	    });

	    $("#submit_template").click(function(){
		if ($("#CRMcustomer").val()==''){
		    $("#CRMcustomer_name").focus()
		    alert('No se ha seleccionado cliente');
		    return false;
		}
	    });

	    $("#verify").click(function(){
		if ($("#idparent").val()==''){
		    return false;
		}
		var content = info($("#idparent").val(),cnn);
		$('#dialog').dialog('option', 'title', 'Informaci&oacute;n de tarea padre');
		$('#dialog').html(content);
		$("#dialog").dialog("open");
	    });
	});
    </script>
    <fieldset style="border: 2px solid #800000;">
    <legend><font size="+1">Tarea principal:</font></legend>
    <table style="width: 800px; height: 50px;">
	<tr>
	    <td valign="top">
		<fieldset><legend>Customer</legend>
		<input type='text' id='CRMcustomer_name' size='90' >
		<input type='hidden' id='CRMcustomer' name='CRMcustomer' >
		<img alt='' src='imgs/nodelete.png' align='top' id='clean' style='cursor: pointer;'>
		</fieldset>
	    </td>
	    <td valign="top">
		<fieldset>
		<legend>Priority</legend>
		<select name='priority_main' size='1'  ><?php echo $list_priorities;?></select>
		</fieldset>
	    </td>
	</tr>
    </table>
    <table style="width: 800px; height: 50px;">
	<tr>
	    <td valign="top">
		<fieldset><legend>Tarea relacionada</legend>
		<input type='text' id='idparent' name="idparent" size='10' >
		<a href="javascript:;" id="verify">Verificar</a>
		</fieldset>
	    </td>
	</tr>
    </table>
    <table style="width: 800px; height: 50px;">
	<tr>
	    <td>
		<fieldset>
		<legend>Category</legend>
		<input type='text' name='category_main' value='<?php echo $datos['category'];?>'  size='50'>
		</fieldset>
	    </td>
	    <td>
		<fieldset><legend>Owner</legend>
		<select name='owner_main' size='1'><?php echo $owner;?></select>
		</fieldset>
	    </td>
	    <td>
		<fieldset><legend>Assignee</legend>
		<select name='assignee_main' size='1'><?php echo $asignee;?></select>
		</fieldset>
	    </td>
	</tr>
    </table>
    <table style="width: 800px; height: 50px;">
	<tr>
	    <td>
		<fieldset><legend>Comment</legend>
		<textarea style='height: 70' rows=10 cols=90 name='content_main' wrap='virtual' class='txt'><?php echo $datos['content'];?></textarea>
		</fieldset>
	    </td>
	</tr>
    </table>
    </fieldset>
    <br><br>
    <?php
}
//******************************************************************************
function entity_form_children($datos){
    $list_priorities = $GLOBALS['list_priorities'];
    $users = $GLOBALS['users'];

    $owner="";
    foreach($users as $key=>$user){
	$sel = $datos['owner'] == $user['id'] ? 'selected' : '';
	$owner.="<option value='$user[id]' $sel>$user[FULLNAME]</option>";
    }

    $asignee="";
    foreach($users as $key=>$user){
	$sel = $datos['asignee'] == $user['id'] ? 'selected' : '';
	$asignee.="<option value='$user[id]' $sel>$user[FULLNAME]</option>";
    }
    ?>
    <fieldset style="border: 2px solid #999;">
    <legend><a href='javascript:;' onmousedown="toggleDiv('entity_<?php echo $datos['id'];?>');"><font size="+1">Tarea hija: </font><?php echo $datos['category'];?></a></legend>
    <div id='entity_<?php echo $datos['id'];?>' style='display:none'>
    <table style="width: 800px; height: 50px;">
	<tr>
	    <td>
		<fieldset>
		<legend>Category</legend>
		<input type="hidden" name="entity[]" value="<?php echo $datos['id']?>">
		<input type='text' name='category[]' value='<?php echo $datos['category'];?>'  size='50'>
		</fieldset>
	    </td>
	    <td>
		<fieldset><legend>Owner</legend>
		<select name='owner[]' size='1'><?php echo $owner;?></select>
		</fieldset>
	    </td>
	    <td>
		<fieldset><legend>Assignee</legend>
		<select name='assignee[]' size='1'><?php echo $asignee;?></select>
		</fieldset>
	    </td>
	    <td valign="top">
		<fieldset>
		<legend>Priority</legend>
		<select name='priority[]' size='1'  ><?php echo $list_priorities;?></select>
		</fieldset>
	    </td>
	</tr>
    </table>
    <table style="width: 800px; height: 50px;">
	<tr>
	    <td>
		<fieldset><legend>Comment</legend>
		<textarea style='height: 70' rows=10 cols=90 name='content[]' wrap='virtual' class='txt'><?php echo $datos['content'];?></textarea>
		</fieldset>
	    </td>
	</tr>
    </table>
    </div>
    </fieldset>
    <br><br>
    <?php
}
//******************************************************************************
function process_template(){

    $prioridades = GetPriorities();
    
    //Datos de la tarea principal
    $custid	    =$_REQUEST['CRMcustomer'];
    $priority_main  =$_REQUEST['priority_main'];
    $category_main  =$_REQUEST['category_main'];
    $owner_main	    =$_REQUEST['owner_main'];
    $asignee_main   =$_REQUEST['assignee_main'];
    $content_main   =$_REQUEST['content_main'];
    $userid	    =$GLOBALS['USERID'];
    $parent	    =$_REQUEST['idparent'] != '' ? intval($_REQUEST['idparent']) : '0';

    //<editor-fold defaultstate="collapsed" desc="Insercion de tarea principal">
    $sql="
	INSERT INTO CRMentity(
	priority,
	category,
	content,
	owner,
	assignee,
	CRMcustomer,
	status,
	sqldate,
	cdate,
	createdby,
	lasteditby,
	openepoch,
	formid,
	start_date,
	parent
	) VALUES(
	'".$prioridades[$priority_main]."',
	'$category_main',
	'$content_main',
	'$owner_main',
	'$asignee_main',
	'$custid',
	'Abierto',
	'". date('Y-m-d') . "',
	'". date('Y-m-d') . "',
	'$userid',
	'$userid',
	'". time() . "',
	'22',
	'".date('Y-m-d H:i:s')."',
	'$parent'
	)
    ";

    mcq($sql, $db);
    $lastid =  mysql_insert_id();
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="Comentario inicial">
    $sqldetail = "
	INSERT INTO CRMentitydetail (
	ed_eid,
	ed_uid,
	ed_date,
	ed_comment
	) VALUES(
	'$lastid',
	'$userid',
	'".date('Y-m-d H:i:s')."',
	'Creacion de Nueva Actividad Como  Propietario:".GetUserName($owner_main)." y Asignado a: ".GetUserName($asignee_main)."'
	)
    ";
    mcq($sqldetail, $db);
    //</editor-fold>

    $entities	= $_REQUEST['entity'];
    $category	= $_REQUEST['category'];
    $owner	= $_REQUEST['owner'];
    $asignee	= $_REQUEST['assignee'];
    $priority	= $_REQUEST['priority'];
    $content	= $_REQUEST['content'];

    foreach($entities as $key=>$entity){

	$contenido = (! get_magic_quotes_gpc ()) ? addslashes ($content[$key]) : $content[$key];

	//<editor-fold defaultstate="collapsed" desc="Insercion de tarea hija">
	$sql="
	    INSERT INTO CRMentity(
	    priority,
	    category,
	    content,
	    owner,
	    assignee,
	    CRMcustomer,
	    status,
	    sqldate,
	    cdate,
	    createdby,
	    lasteditby,
	    openepoch,
	    formid,
	    start_date,
	    parent
	    ) VALUES(
	    '".$prioridades[$priority[$key]]."',
	    '".$category[$key]."',
	    '$contenido',
	    '".$owner[$key]."',
	    '".$asignee[$key]."',
	    '$custid',
	    'Abierto',
	    '". date('Y-m-d') . "',
	    '". date('Y-m-d') . "',
	    '$userid',
	    '$userid',
	    '". time() . "',
	    '22',
	    '".date('Y-m-d H:i:s')."',
	    '$lastid'
	    )
	";
	mcq($sql, $db);
	$entity_id =  mysql_insert_id();
	//</editor-fold>

	//<editor-fold defaultstate="collapse" desc="Comentario inicial">
	$sqldetail = "
	    INSERT INTO CRMentitydetail (
	    ed_eid,
	    ed_uid,
	    ed_date,
	    ed_comment
	    ) VALUES(
	    '$entity_id',
	    '$userid',
	    '".date('Y-m-d H:i:s')."',
	    'Creacion de Nueva Actividad Como  Propietario:".GetUserName($owner[$key])." y Asignado a: ".GetUserName($asignee[$key])."'
	    )
	";
	mcq($sqldetail, $db);
	//</editor-fold>
    }
    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=edit.php?e=$lastid\">";
    



}
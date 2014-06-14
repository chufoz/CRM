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

AdminTabs("tags");

$to_tabs = array("catalogo");
$tabbs["catalogo"] = array("admin_tags.php" => "Administracion", "comment" => "Administracion");

$navid = "catalogo";

InterTabs($to_tabs, $tabbs, $navid);

MustBeAdmin();

if(isset($_GET['tagclass']) && $_GET['tagclass']=='1'){
    __clasificated();
}
else{
    __unclasificated();
}

function __menu(){
    $checked = $_GET['tagclass'] == 0 ? "checked" : '' ;
    $checked2 = $_GET['tagclass'] == 1 ? "checked" : '' ;
    ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $('input[name="tagclass"]').change(function(){
                    $("#tagFilter").submit();
                }); 
            });
        </script>
        <div align='center'>
            <form id="tagFilter" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="GET">
                <input type="radio" name='tagclass' value="0" <?php echo $checked; ?>>Sin Clasificar
                <input type="radio" name='tagclass' value="1" <?php echo $checked2; ?>>Clasificados
            </form>
        </div>
        
    <?php
}
//******************************************************************************
function __unclasificated(){
    $tags = getTagsUnclass();
    $parents = getTagsClass();
    //print_array($tags);
    $conn_db = array('host' => $GLOBALS['host'][0], 'user' => $GLOBALS['user'][0], 'pass' => $GLOBALS['pass'][0], 'database' => $GLOBALS['database'][0]);
    $conn_db = base64_encode(serialize($conn_db));
?>
    <LINK href="jquery/external/datatable.css" rel="StyleSheet" type="text/css">
    <script type="text/javascript" language="javascript" src="jquery/external/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
	var dTable;
	$(document).ready(function(){
	    dTable=$('#listTags').dataTable({
		"bAutoWidth": false,
		"bSort": true,
		"bLengthChange": false,
		"sPaginationType": 'full_numbers',
		"iDisplayLength": 25,
		"bDeferRender": true,
		"oLanguage":{
		    "oPaginate":{
			"sFirst":"&lt;&lt;",
			"sLast":"&gt;&gt;",
			"sNext":"&gt;",
			"sPrevious":"&lt;"
		    },
		    "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
		    "sInfoEmpty": "Mostrado 0 a 0 de 0 registros",
		    "sInfoFiltered": "(filtrados de _MAX_ registros totales)",
		    "sSearch": "Buscar:",
		    "sEmptyTable": "No se encontraron registros",
		    "sZeroRecords": "No se encontraron registros"
		},
		"aoColumnDefs": [
		    /*{ "bVisible": false, "aTargets": [ 0 ] },*/
		    { "iDataSort": 0, "aTargets": [ 0 ] }
		]
	    });

	    dTable.fnSort( [ [1,'asc'] ] );
            
            $("#dialog").dialog({
		bgiframe: true,
		autoOpen: false,
		modal: true,
		minWidth:450,
		closeOnEscape: true,
		resizable:false,
		draggable:false/*,
		hide: "fade"*/
	    });
            $("#dialog2").dialog({
		bgiframe: true,
		autoOpen: false,
		modal: true,
		minWidth:450,
		closeOnEscape: true,
		resizable:false,
		draggable:false/*,
		hide: "fade"*/
	    });
            
            $(document).on('click','#listTags tbody td[name!="link"]',function(){
                $("#listTags input").val('');
                
                var idReg = $(this).parents('tr').attr('idReg');
                $.ajax({
                    url: "autocomplete.php?tags=5",
                    dataType: "json",
                    data: {
                        id: idReg,
                        db: '<?php echo $conn_db; ?>'
                    },
                    success: function(data) {
                        $("#tag_id").val(data.id);
                        $("#tag_name").val(data.name);
                        $("#tag_solution").val(data.solution);
                        if(data.status=='0'){
                            $("#status0").attr('checked','checked');
                            $("#status1").removeAttr('checked');
                        }else{
                            $("#status1").attr('checked', 'checked');
                            $("#status0").removeAttr('checked');
                        }
                        
                        $("#tag_parent").val(data.parent);
                        $("#tag_parent_name").val(data.parent_name);
                        
                        $("#dialog").dialog("open");
                    }
                });
            });
            
            $("#accept").on('click', function(){
                var data = Object();
                data.id = $("#tag_id").val();
                data.name = $("#tag_name").val();
                data.solution = $("#tag_solution").val();
                data.status = $('input[name="status"]:checked').val();
                data.parent = $("#tag_parent").val();
                
                $.ajax({
                    url: "autocomplete.php?tags=6",
                    dataType: "json",
                    data: {
                        data: data,
                        db: '<?php echo $conn_db; ?>'
                    },
                    success: function() {        
                        $("#dialog").dialog("close");
                        location.reload();
                    }
                });
            });
            
            $(document).on('click','#listTags tbody td[name="link"]',function(){
                $("#tickets").html('');
                var idReg = $(this).parents('tr').attr('idReg');
                $.ajax({
                    url: "autocomplete.php?tags=7",
                    dataType: "json",
                    data: {
                        id: idReg,
                        db: '<?php echo $conn_db; ?>'
                    },
                    success: function(data) {
                        $("#tickets").append(data);
                        $("#dialog2").dialog("open");
                    }
                });
            });
            
            $("#exit").on('click', function(){
                $("#dialog2").dialog("close");
            });
	});
    </script>
    <div id='dialog2' title='Incidencias' align='center'>
    <br>
    <table border=0>
        <tr>
            <td width=100% align='left' id="tickets">
                
            </td>
        </tr>
        <tr>
            <td colspan="2" height='15px'></td>
        </tr>
        <tr>
            <td align="center">
                <b id='exit' class="buttons3 pointer">Salir</b>
            </td>
        </tr>
    </table>
    </div>
    <div id='dialog' title='Editar Etiqueta' align='center'>
    <br>
    <table border=0 class=''>
        <tr>
            <td width=30% align='left'><b>Descripcion:</b></td>
            <td width=70% align='right'>
                <input id='tag_name' type="text" value="">
            </td>
        </tr>
        <tr>
            <td width=30% align='left'><b>Solucion:</b></td>
            <td width=70% align='right'>
                <input id='tag_solution' type="text" value="">
            </td>
        </tr>
        <tr>
            <td align='left'><b>Padre:</b></td>
            <td width=70% align='right'>
                <select id="tag_parent" style="width:157px">
                    <option value="0"></option>
                    <?php
                    foreach ($parents as $value) {
                        echo "<option value='$value[id]'>$value[name]</option>";
                    }
                    ?>
                </select> 
            </td>
        </tr>
        <tr>
            <td width=30% align='left'><b>Estatus:</b></td>
            <td width=70% align='right'>
                <input type="radio" value="1" id ="status1" name='status'>Habilitado
                <input type="radio" value="0" id ="status0" name='status'>Inhabilitado
            </td>
        </tr>
        <tr>
            <td colspan="2" height='15px'></td>
        </tr>
        <tr>
            <td align="center" colspan="2">
                <b id='accept' class="buttons3 pointer">Aceptar</b>
                <input type="hidden" value="" id ="tag_id">
            </td>
        </tr>
    </table>
    <br>
    </div>
    <table width='100%' border='0' height='100%'>
	<tr>
	    <td width='22'>&nbsp;</td>
	    <td valign='top'>
	    <table width='90%'>
		<tr>
		    <td>
		    <fieldset>
		    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Informaci&oacute;n</font></legend>
                    <?php __menu();?>
                    <div align='center'>
                        <form id='formSelectTags' name="selectTags" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST">
                        <table class='crm3' cellspacing=0 cellpadding=0 border='0'>
                            <tr>
                                <td>
                                    <table cellspacing='0' cellpadding='2' width='100%' border='0'>
                                        <tr>
                                            <td width='auto' height='60' align='center'>
                                                <b>LISTADO DE ETIQUETAS SIN CLASIFICAR</b>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class='crm' cellspacing='0' cellpadding='2' border='0' id='listTags'>
                                        <thead>
                                            <tr class=''>
                                                <th class='' align='center' width='40'><b>ID</b></th>
                                                <th class='' align='center' width='250'><b>Descripcion</b></th>
                                                <th class='' align='center' width='220'><b>Solucion</b></th>
                                                <th class='' align='center' width='130'><b>Estatus</b></th>
                                                <th class='' align='center' width='130'><b>Incidencias</b></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $class = 'rs2';
                                        foreach($tags as $row){
                                            $class = ($class=='') ? '' : '';
                                            $status = $row['status'] == 0 ? 'Inhabilitado' : "Habilitado" ;
                                        ?>
                                            <tr class='pointer <?php echo $class; ?>'
                                                idReg="<?php echo $row['id']; ?>">
                                                <td class='' align='center'>
                                                    <?php echo $row['id']; ?>&nbsp;
                                                </td>
                                                <td class='' align='left'>
                                                    <?php echo $row['name']; ?>
                                                </td>
                                                <td class='' align='center'>
                                                    <?php echo $row['solution']; ?>
                                                </td>
                                                <td class='' align='center'>
                                                    <?php echo $status; ?>
                                                </td>
                                                <td name='link' align='center'>
                                                    tickets
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <form>
                        </div>
		    </fieldset>
		    </td>
		</tr>
	    </table>
	    </td>
	</tr>
    </table>
<?php
}
//******************************************************************************
function __clasificated(){
    $tags = getTagsClass();
    
    $conn_db = array('host' => $GLOBALS['host'][0], 'user' => $GLOBALS['user'][0], 'pass' => $GLOBALS['pass'][0], 'database' => $GLOBALS['database'][0]);
    $conn_db = base64_encode(serialize($conn_db));
?>
    <LINK href="jquery/external/datatable.css" rel="StyleSheet" type="text/css">
    <script type="text/javascript" language="javascript" src="jquery/external/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
	var dTable;
	$(document).ready(function(){
	    dTable=$('#listTags').dataTable({
		"bAutoWidth": false,
		"bSort": true,
		"bLengthChange": false,
		"sPaginationType": 'full_numbers',
		"iDisplayLength": 25,
		"bDeferRender": true,
		"oLanguage":{
		    "oPaginate":{
			"sFirst":"&lt;&lt;",
			"sLast":"&gt;&gt;",
			"sNext":"&gt;",
			"sPrevious":"&lt;"
		    },
		    "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
		    "sInfoEmpty": "Mostrado 0 a 0 de 0 registros",
		    "sInfoFiltered": "(filtrados de _MAX_ registros totales)",
		    "sSearch": "Buscar:",
		    "sEmptyTable": "No se encontraron registros",
		    "sZeroRecords": "No se encontraron registros"
		},
		"aoColumnDefs": [
		    /*{ "bVisible": false, "aTargets": [ 0 ] },*/
		    { "iDataSort": 0, "aTargets": [ 0 ] }
		]
	    });

	    dTable.fnSort( [ [1,'asc'] ] );
            
            $("#dialog").dialog({
		bgiframe: true,
		autoOpen: false,
		modal: true,
		minWidth:450,
		closeOnEscape: true,
		resizable:false,
		draggable:false/*,
		hide: "fade"*/
	    });
            
            $(document).on('click',"#listTags tbody td",function(){
                $("#listTags input").val('');
                $('#tag_parent option').show();
                var idReg = $(this).parents('tr').attr('idReg');
                
                $('#tag_parent option[value="'+idReg+'"]').hide();
                
                $.ajax({
                    url: "autocomplete.php?tags=5",
                    dataType: "json",
                    data: {
                        id: idReg,
                        db: '<?php echo $conn_db; ?>'
                    },
                    success: function(data) {
                        $("#tag_id").val(data.id);
                        $("#tag_name").val(data.name);
                        $("#tag_solution").val(data.solution);
                        if(data.status=='0'){
                            $("#status0").attr('checked','checked');
                            $("#status1").removeAttr('checked');
                        }else{
                            $("#status1").attr('checked', 'checked');
                            $("#status0").removeAttr('checked');
                        }
                        
                        $("#tag_parent").val(data.parent);
                        
                        $("#dialog").dialog("open");
                    }
                });
            });
            
            $("#accept").on('click', function(){                
                var data = Object();
                data.id = $("#tag_id").val();
                data.name = $("#tag_name").val();
                data.solution = $("#tag_solution").val();
                data.status = $('input[name="status"]:checked').val();
                data.parent = $("#tag_parent").val();
                
                $.ajax({
                    url: "autocomplete.php?tags=6",
                    dataType: "json",
                    data: {
                        data: data,
                        db: '<?php echo $conn_db; ?>'
                    },
                    success: function() {        
                        $("#dialog").dialog("close");
                        location.reload();
                    }
                });
            
            });
	});
    </script>
    <div id='dialog' title='Editar Etiqueta' align='center'>
    <br>
    <table border=0 class=''>
        <tr>
            <td width=30% align='left'><b>Descripcion:</b></td>
            <td width=70% align='right'>
                <input id='tag_name' type="text" value="">
            </td>
        </tr>
        <tr>
            <td width=30% align='left'><b>Solucion:</b></td>
            <td width=70% align='right'>
                <input id='tag_solution' type="text" value="">
            </td>
        </tr>
        <tr>
            <td align='left'><b>Padre:</b></td>
            <td width=70% align='right'>
                <select id="tag_parent" style="width:157px">
                    <option value="0"></option>
                    <?php
                    foreach ($tags as $value) {
                        echo "<option value='$value[id]'>$value[name]</option>";
                    }
                    ?>
                </select> 
            </td>
        </tr>
        <tr>
            <td width=30% align='left'><b>Estatus:</b></td>
            <td width=70% align='right'>
                <input type="radio" value="1" id ="status1" name='status'>Habilitado
                <input type="radio" value="0" id ="status0" name='status'>Inhabilitado
            </td>
        </tr>
        <tr>
            <td colspan="2" height='15px'></td>
        </tr>
        <tr>
            <td align="center" colspan="2">
                <b id='accept' class="buttons3 pointer">Aceptar</b>
                <input type="hidden" value="" id ="tag_id">
            </td>
        </tr>
    </table>
    <br>
    </div>
    <table width='100%' border='0' height='100%'>
	<tr>
	    <td width='22'>&nbsp;</td>
	    <td valign='top'>
	    <table width='90%'>
		<tr>
		    <td>
		    <fieldset>
		    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Informaci&oacute;n</font></legend>
                    <?php __menu();?>
                    <div align='center'>
                        <form id='formSelectTags' name="selectTags" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST">
                        <table class='crm3' cellspacing=0 cellpadding=0 border='0'>
                            <tr>
                                <td>
                                    <table cellspacing='0' cellpadding='2' width='100%' border='0'>
                                        <tr>
                                            <td width='auto' height='60' align='center'>
                                                <b>LISTADO DE ETIQUETAS SIN CLASIFICAR</b>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class='crm' cellspacing='0' cellpadding='2' border='0' id='listTags'>
                                        <thead>
                                            <tr class=''>
                                                <th class='' align='center' width='40'><b>ID</b></th>
                                                <th class='' align='center' width='250'><b>Descripcion</b></th>
                                                <th class='' align='center' width='220'><b>Solucion</b></th>
                                                <th class='' align='center' width='130'><b>Estatus</b></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $class = 'rs2';
                                        foreach($tags as $row){
                                            $class = ($class=='') ? '' : '';
                                            $status = $row['status'] == 0 ? 'Inhabilitado' : "Habilitado" ;
                                        ?>
                                            <tr class='pointer <?php echo $class; ?>'
                                                idReg="<?php echo $row['id']; ?>">
                                                <td class='' align='center'>
                                                    <?php echo $row['id']; ?>&nbsp;
                                                </td>
                                                <td class='' align='left'>
                                                    <?php echo $row['name']; ?>
                                                </td>
                                                <td class='' align='center'>
                                                    <?php echo $row['solution']; ?>
                                                </td>
                                                <td class='' align='center'>
                                                    <?php echo $status; ?>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <form>
                        </div>
		    </fieldset>
		    </td>
		</tr>
	    </table>
	    </td>
	</tr>
    </table>
<?php
}
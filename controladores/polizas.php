<?php
extract($_REQUEST);
include("header.inc.php");
//include("funcictc.php");
//printarray($GLOBALS);
$cust = GetCustomer($_REQUEST['custid']);
echo "
<fieldset>
<legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Cliente $cust[id]: $cust[custname]</font>&nbsp;</legend><br>";
navigator();
if ($_REQUEST['add']==1){
    frm_poliza('');
}
elseif ($_REQUEST['btn_editpoliza']){
    update_poliza();
}
elseif ($_REQUEST['edit']==1){
    frm_editpoliza('');
}
elseif($_REQUEST['btn_newpoliza']){
    validate_poliza();
}
elseif($_REQUEST['btn_payment']){
    update_payment();
}
elseif($_REQUEST['pay']=='1'){
    view_payments();
}
else{
    view_polizas();
}

echo "</fieldset>";
EndHTML();

//*****************************************************************************
function frm_poliza($input){

    $cust = GetCustomer($_REQUEST['custid']);
    $cat = GetCatPolizas();

    $modospago=array(
	array('val'=>'A','label'=>'Anual'),
	array('val'=>'S','label'=>'Semestral'),
	array('val'=>'T','label'=>'Trimestral'),
	array('val'=>'M','label'=>'Mensual'),
	array('val'=>'P','label'=>'Pagado')
    );

    //Error checking

    $errcustid = isset($input['custid_err']) ? "<tr><td></td><td>$input[custid_err]</td></tr>" : "";

    $lbltipo = isset($input['tipo_err']) ? "<font color='red'>Tipo de poliza:</font>" : "Tipo de poliza:";
    $errtipo = isset($input['tipo_err']) ? "<tr><td></td><td>$input[tipo_err]</td></tr>" : "";

    $lblfini = isset($input['fechaini_err']) ? "<font color='red'>Fecha Inicial:</font>" : "Fecha Inicial:";
    $errfini = isset($input['fechaini_err']) ? "<tr><td></td><td>$input[fechaini_err]</td></tr>" : "";

    $lblffin = isset($input['fechafin_err']) ? "<font color='red'>Fecha Final:</font>" : "Fecha Final:";
    $errffin = isset($input['fechafin_err']) ? "<tr><td></td><td>$input[fechafin_err]</td></tr>" : "";

    $lblmodopago = isset($input['modo_pago_err']) ? "<font color='red'>Modo de pago:</font>" : "Modo de pago:";
    $errmodopago = isset($input['modo_pago_err']) ? "<tr><td></td><td>$input[modo_pago_err]</td></tr>" : "";

    $lblcontrato = isset($input['contrato_err']) ? "<font color='red'>Contrato:</font>" : "Contrato:";
    $errcontrato = isset($input['contrato_err']) ? "<tr><td></td><td>$input[contrato_err]</td></tr>" : "";

    //Valores iniciales
    $fechaini = isset ($input['fechaini']) ? $input['fechaini'] : '';
    $fechafin = isset ($input['fechafin']) ? $input['fechafin'] : '';
    $contrato = isset ($input['contrato']) ? $input['contrato'] : '';

    ?>
    <script type="text/javascript">
    $(document).ready(function() {

	$( "#fini,#ffin" ).datepicker(
	$.extend({}, 
	$.datepicker.regional["es"], {
	showStatus: true,
	showOn: "both",
	buttonImage: "calendar.png",
	buttonImageOnly: true,
	duration: "",
	appendText: " dd/mm/aaaa",
	beforeShow: customRange,
	changeYear: true,
	changeMonth: true,
	showButtonPanel: true,
	onSelect: calc
	}
	));
	$('img.ui-datepicker-trigger').css({'cursor' : 'pointer', "vertical-align" : 'middle'});

    });

    function calc(value,input){
	if (input.id=="fini"){
	    olddate = $("#fini").datepicker("getDate");
	    newdate = new Date(olddate.getFullYear()+1,olddate.getMonth(),olddate.getDate()-1);
	    strnewdate = newdate.getDate()+"/"+(newdate.getMonth()+1)+"/"+newdate.getFullYear();
	    $("#ffin").datepicker("setDate",strnewdate);
	}
	else if (input.id=="ffin"){
	    olddate = $("#ffin").datepicker("getDate");
	    newdate = new Date(olddate.getFullYear()-1,olddate.getMonth(),olddate.getDate()+1);
	    strnewdate = newdate.getDate()+"/"+(newdate.getMonth()+1)+"/"+newdate.getFullYear();
	    $("#fini").datepicker("setDate",strnewdate);
	}
    }
    function customRange(input) {
	
	return {
	    minDate: (input.id == "ffin" ? $("#fini").datepicker("getDate") : null),
	    maxDate: (input.id == "fini" ? $("#ffin").datepicker("getDate") : null)
	};
    }

    
    </script>
    <?php
    echo "
    <form name='AddPolicy' method='POST' action='polizas.php'>
    <fieldset>
    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Agregar poliza:</font>&nbsp;</legend>
    <table border=0 cellspacing='0' cellpadding='4' width='700'>
	<tr><td height='10' colspan='2'>&nbsp;</td></tr>
	$errcustid
	<tr>
	    <td><b>$lbltipo</b></td>
	    <td>
		<input type='hidden' name='tab' value='$_REQUEST[tab]'>
		<input type='hidden' name='custid' value='$cust[id]'>
		<select name='tipo'>
		";
		$sel = isset($input['tipo']) ? $input['tipo'] : '0';
		foreach($cat as $tipo){
		    $checked = $tipo['id'] == $sel ? 'SELECTED' : "";
		    echo "<option value='$tipo[id]' $checked>$tipo[nombre]</option>";
		}
		echo "
		</select>
	    </td>
	</tr>
	$errtipo
	<tr>
	    <td><b>$lblfini</b></td>
	    <td valing='top'>
		    <input type='text' name='fechaini' id='fini' size='12' maxlength='10' value='$fechaini'>
	    </td>
	</tr>
	$errfini
	<tr>
	    <td><b>$lblffin</b></td>
	    <td>
		<input type='text' name='fechafin' id='ffin' size='12' maxlength='10' value='$fechafin'>
	    </td>
	</tr>
	$errffin
	<tr>
	    <td><b>$lblmodopago</b></td>
	    <td>
		<select name='modo_pago'>";
		    $sel = isset($input['modo_pago']) ? $input['modo_pago'] : '';
		    foreach($modospago as $modopago){
			$checked = $modopago['val'] == $sel ? 'SELECTED' : "";
			echo "<option value='$modopago[val]' $checked>$modopago[label]</option>";
		    }
		echo "
		</select>
	    </td>
	</tr>
	$errmodopago
	<tr>
	    <td><b>$lblcontrato</b></td>
	    <td>
		<input type='text' name='contrato' size='40' maxlength='255' value='$contrato'>
	    </td>
	</tr>
	$errcontrato
	<tr><td></td><td align='left'><input type='reset' value='Limpiar'>&nbsp;&nbsp;&nbsp;<input type='submit' name='btn_newpoliza' value='Aceptar'></td></tr>
	<tr><td height=10 colspan=2>&nbsp;</td></tr>
    </table>
    </fieldset>
    </form>
";
//printarray($_REQUEST);
}
//*****************************************************************************
function frm_editpoliza(){

    $params['custid']=$_REQUEST['custid'];
    $params['pid']=$_REQUEST['pid'];
    $poliza = GetPolizasByCustomer($params);
    //printarray($poliza);

    $status = $poliza['status'] == '1' ? 'checked' : '';
    $active = $poliza['active'] == '1' ? 'checked' : '';


    ?>
    <script type="text/javascript">
    $(document).ready(function() {

	$("#status").click(function(){
	    if ($("#status").attr("checked")==false){
		$("#alerta").text("Advertencia: Si realiza la cancelacion de la poliza, esta no se podra recuperar.");
	    }
	    else{
		$("#alerta").text("");
	    }
	   
	});

	$("#active").click(function(){
	    if ($("#active").attr("checked")==false){
		$("#alertactive").text("Advertencia: Si inhabilita la poliza, se desactivaran caracteristicas de la aplicacion.");
	    }
	    else{
		$("#alertactive").text("");
	    }

	});

    

	$("#EditPolicy").submit(function(){
	   if($("#contrato").val()==''){
		alert("Favor de proporcionar numero de contrato");
		$("#contrato").focus();
		return false;
	   }
	});

    });


    </script>
    <?php
    echo "
    <form name='EditPolicy' method='POST' id='EditPolicy' action='polizas.php'>
    <fieldset>
    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Editar poliza:</font>&nbsp;</legend>
    <table border=0 cellspacing='0' cellpadding='4' width='700'>
	<tr><td width=150></td><td width=550></td></tr>
	<tr>
	    <td><b>Tipo de poliza:</b></td>
	    <td>
		<input type='hidden' name='tab' value='$_REQUEST[tab]'>
		<input type='hidden' name='custid' value='$poliza[custid]'>
		<input type='hidden' name='pid' value='$poliza[id]'>
		$poliza[nombre]
	    </td>
	</tr>
	<tr>
	    <td><b>Fecha Inicial:</b></td>
	    <td>
		$poliza[fini]
	    </td>
	</tr>
	<tr>
	    <td><b>Fecha Final:</b></td>
	    <td>
		$poliza[ffin]
	    </td>
	</tr>
	<tr>
	    <td><b>Modo de Pago:</b></td>
	    <td>
		$poliza[modopago]
	    </td>
	</tr>
	<tr>
	    <td><b>Contrato:</b></td>
	    <td>
		<input type='text' name='contrato' id='contrato' size='40' maxlength='255' value='$poliza[contrato]'>
	    </td>
	</tr>
	<tr>
	    <td><b>Vigencia:</b></td>
	    <td>
		<input type='checkbox' name='status' id='status' value='y' $status>&nbsp;<font color='red'><span id='alerta'></span></font>
	    </td>
	</tr>
	<tr>
	    <td><b>Activa:</b></td>
	    <td>
		<input type='checkbox' name='active' id='active' value='y' $active>&nbsp;<font color=red><span id='alertactive'></span></font>
	    </td>
	</tr>
	<tr>
	    <td></td>
	    <td align='left'>
		<input type='reset' value='Limpiar'>&nbsp;&nbsp;&nbsp;<input type='submit' name='btn_editpoliza' value='Aceptar'>
	    </td>
	</tr>
	<tr><td height=10 colspan=2>&nbsp;</td></tr>
    </table>
    </fieldset>
    </form>
";
//printarray($_REQUEST);
}
//*****************************************************************************
function validate_poliza(){
    $data['custid']	= $_REQUEST['custid'];
    $data['tipo']	= $_REQUEST['tipo'];
    $data['fechaini']   = $_REQUEST['fechaini'];
    $data['fechafin']   = $_REQUEST['fechafin'];
    $data['modo_pago']  = $_REQUEST['modo_pago'];
    $data['contrato']   = $_REQUEST['contrato'];

    //Verificacion de datos
    $flag_err=false;
    if ($data['custid']==''){
	$data['custid_err']="No se ha seleccionado el cliente";
	$flag_err=true;
    }
    if($data['tipo']==''){
	$data['tipo_err']="No se ha seleccionado tipo de poliza";
	$flag_err=true;
    }
    if($data['fechaini']==''){
	$data['fechaini_err']='No se ha proporcionado fecha inicial';
	$flag_err=true;
    }
    if($data['fechafin']==''){
	$data['fechafin_err']='No se ha proporcionado fecha final';
	$flag_err=true;
    }
    if($data['modo_pago']==''){
	$data['modo_pago_err']='No se ha proporcionado el modo de pago';
	$flag_err=true;
    }
    if($data['contrato']==''){
	$data['contrato_err']='No se ha proporcionado el contrato';
	$flag_err=true;
    }
    $duplicate = val_poliza($data);
    if (sizeof($duplicate)>0){
	$data['tipo_err']='Existe una poliza vigente para este tipo, seleccione otro tipo de poliza, u otro periodo de vigencia';
	$flag_err=true;
    }

    
    //printarray($data);
    if ($flag_err==true){
	frm_poliza($data);
    }
    else{
	insert_poliza($data);
    }

}
//*****************************************************************************
function insert_poliza($data){
    //printarray($data);
    $fini = explode("/", $data['fechaini']);
    $fi = $fini['2']."-".$fini[1]."-".$fini[0];

    $ffin = explode("/", $data['fechafin']);
    $ff = $ffin['2']."-".$ffin[1]."-".$ffin[0];

    //Generar registro de poliza-----------------------------------
    $sql = "
    INSERT INTO CRMpolizas(
    pol_custid,
    pol_cpid,
    pol_fini,
    pol_ffin,
    pol_modopago,
    pol_contrato,
    pol_ultpago,
    pol_proxpago,
    pol_status,
    pol_status_venc,
    pol_userid
    )
    VALUES(
    $data[custid],
    $data[tipo],
    '$fi',
    '$ff',
    '$data[modo_pago]',
    '$data[contrato]',
    '0000-00-00',
    '0000-00-00',
    '1',
    '0',
    '$GLOBALS[USERID]'
    )
    ";

    mcq($sql, $db);

    $id=mysql_insert_id();

    //-------------------------------------------------------------

    //Generar registro de pagos------------------------------------

    if ($data['modo_pago']=='A'){//Pago Anual
	$num=1;
	$offset=12;
    }
    elseif($data['modo_pago']=='S'){//Pago Semestral
	$num=2;
	$offset=6;
    }
    elseif($data['modo_pago']=='T'){//Pago Trimestral
	$num=4;
	$offset=3;
    }
    elseif($data['modo_pago']=='M'){//Pago Mensual
	$num=12;
	$offset=1;
    }
    else{
	$num=0;
	$offset=0;
    }

    $inicial = $fi;
    for ($i=1;$i<=$num;$i++){
	//Obtener el intervalo de fechas
	$ini = explode("-", $inicial);
	$final  = date("Y-m-d",mktime(0, 0, 0, $ini[1]+$offset  , $ini[2]-1, $ini[0]));

	$sql="
	INSERT INTO CRMpagos(
	pag_polid,
	pag_fini,
	pag_ffin,
	pag_num,
	pag_status,
	pag_notify
	)
	VALUES(
	$id,
	'$inicial',
	'$final',
	'$i',
	'1',
	'0'
	)
	";

	mcq($sql, $db);
	//echo $sql."<br>";
	
	$inicial=date("Y-m-d",mktime(0, 0, 0, $ini[1]+$offset  , $ini[2], $ini[0]));
    }

    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=polizas.php?custid=$data[custid]&tab=$_REQUEST[tab]\">";
    
    //-----------------------------------------------------------
    

}
//*****************************************************************************
function update_poliza(){
    $params['custid']=$_REQUEST['custid'];
    $params['pid']=$_REQUEST['pid'];
    $poliza = GetPolizasByCustomer($params);
    
    $id = $_REQUEST['pid'];
    $contrato = $_REQUEST['contrato'];
    $status = isset($_REQUEST['status']) ? '1' : '0';
    $active = isset($_REQUEST['active']) ? '1' : '0';

    $sql = "UPDATE CRMpolizas SET pol_contrato='$contrato', pol_status='$status', pol_active='$active' WHERE pol_id=$id";
    mcq($sql, $db);

    //Si el estatus es igual al '0', se hace la cancelacion de los pagos pendientes
    if ($status=='0'){
	$sql = "UPDATE CRMpagos SET pag_status='0', pag_factura='CANCELADO', pag_userid='$GLOBALS[USERID]' WHERE pag_polid=$id AND pag_status='1'";
	mcq($sql, $db);
    }

    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=polizas.php?custid=$_REQUEST[custid]&tab=$_REQUEST[tab]\">";
}
//*****************************************************************************
function update_payment(){

    if ($_REQUEST['fpago']=='' or $_REQUEST['factura']==''){
	echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=polizas.php?pay=1&custid=$_REQUEST[custid]&tab=$_REQUEST[tab]&pid=$_REQUEST[pid]\">";
    }

    //printarray($_REQUEST);

    $freg = date("Y-m-d");
    $fpago = explode("/",$_REQUEST['fpago']);
    $fpago = $fpago[2]."-".$fpago[1]."-".$fpago[0] ;
    $factura = $_REQUEST['factura'];
    $id = $_REQUEST['id'];
    $pid = $_REQUEST['pid'];

    //Actualizar pago
    $sql = "
    UPDATE CRMpagos SET
    pag_factura = '$factura',
    pag_fpago = '$fpago',
    pag_freg = '$freg',
    pag_status = '0',
    pag_userid = '$GLOBALS[USERID]'
    WHERE pag_id = $id
    ";
    mcq($sql, $db);

    //Actualizar poliza

    //Calcular fecha del proximo pago
    //Verificar si es el ultimo pago de la poliza
    if (
	    ($_REQUEST['modopago']=='M' and $_REQUEST['num']=='12') or
	    ($_REQUEST['modopago']=='T' and $_REQUEST['num']=='4') or
	    ($_REQUEST['modopago']=='S' and $_REQUEST['num']=='2') or
	    ($_REQUEST['modopago']=='A' and $_REQUEST['num']=='1')
    ){
	$proxpago = '0000-00-00';
    }
    else{
	if ($_REQUEST['modopago']=='A'){//Pago Anual
	    $offset=12;
	}
	elseif($_REQUEST['modopago']=='S'){//Pago Semestral
	    $offset=6;
	}
	elseif($_REQUEST['modopago']=='T'){//Pago Trimestral
	    $offset=3;
	}
	elseif($_REQUEST['modopago']=='M'){//Pago Mensual
	    $offset=1;
	}

	//Definir aqui si el proximo pago se obtiene a partir de la fecha inicial
	//de la vigencia, o de la fecha final

	$fecharef = explode("/", $_REQUEST['fini']);
	$proxpago  = date("Y-m-d",mktime(0, 0, 0, $fecharef[1]+$offset  , $fecharef[0], $fecharef[2]));
    }
    //echo $proxpago;
    $sql = "
    UPDATE CRMpolizas SET
    pol_ultpago = '$fpago',
    pol_proxpago = '$proxpago'
    WHERE pol_id = $pid
    ";
    mcq($sql, $db);

    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=polizas.php?pay=1&custid=$_REQUEST[custid]&tab=$_REQUEST[tab]&pid=$_REQUEST[pid]\">";
}
//*****************************************************************************
function navigator(){
  echo "
    <fieldset>
    <table border=0 cellspacing='0' cellpadding='4' width='100%'>
	<tr>
	    <td>
		<img src='arrow.gif'>&nbsp;<a href='customers.php?det=1&c_id=$_REQUEST[custid]&tab=$_REQUEST[tab]&$epoch' class='bigsort'>Cliente</a>
		&nbsp;|&nbsp;&nbsp; <img src='arrow.gif'>&nbsp;<a href='polizas.php?custid=$_REQUEST[custid]&tab=$_REQUEST[tab]&$epoch' class='bigsort'>Polizas</a>
		&nbsp;|&nbsp;&nbsp; <img src='arrow.gif'>&nbsp;<a href='polizas.php?add=1&custid=$_REQUEST[custid]&tab=$_REQUEST[tab]&$epoch' class='bigsort'>Agregar poliza</a>
	    </td>
	</tr>
    </table>
    </fieldset><br>
";
}
//*****************************************************************************
function view_polizas(){
    
    //Parametros de busqueda
    $params['custid']=$_REQUEST['custid'];
    $params['status']='1';
    $polizas = GetPolizasByCustomer($params);
    //printarray($polizas);
    echo "
    <fieldset>
    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Polizas vigentes:&nbsp;&nbsp;</legend>
    <table border=0 width=1000 class='crm'>
	<tr>
	    <td></td>
	    <td align='center'><b>POLIZA</b></td>
	    <td align='center'><b>DESDE</b></td>
	    <td align='center'><b>HASTA</b></td>
	    <td align='center'><b>MODO PAGO</b></td>
	    <td align='center'><b>CONTRATO</b></td>
	    <td align='center'><b>ESTADO</b></td>
	    <td></td>
	</tr>
	";
	if (sizeof($polizas)>0){
	    foreach($polizas as $poliza){
		$active = $poliza['active'] == '1' ? "<font color='green'><b>ACTIVA</b>" : "<font color='red'></b>INHABILITADA</b></font>";
		echo "
		    <tr>
			<td align='center'><b><a href='polizas.php?edit=1&custid=$_REQUEST[custid]&tab=$_REQUEST[tab]&pid=$poliza[id]'>Editar</a></b></td>
			<td>$poliza[nombre]</td>
			<td>$poliza[fini]</td>
			<td>$poliza[ffin]</td>
			<td>$poliza[modopago]</td>
			<td>$poliza[contrato]</td>
			<td align='center'>$active</td>
			<td align='center'><b><a href='polizas.php?pay=1&custid=$_REQUEST[custid]&tab=$_REQUEST[tab]&pid=$poliza[id]'>Pagos</a></b></td>
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

    //Parametros de busqueda
    $params['custid']=$_REQUEST['custid'];
    $params['status']='0';
    $polizas = GetPolizasByCustomer($params);
    //printarray($polizas);
    echo "
    <fieldset>
    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Polizas canceladas o vencidas:&nbsp;&nbsp;</legend>
    <table border=0 width=1000 class='crm'>
	<tr>
	    <td align='center'><b>POLIZA</b></td>
	    <td align='center'><b>DESDE</b></td>
	    <td align='center'><b>HASTA</b></td>
	    <td align='center'><b>MODO PAGO</b></td>
	    <td align='center'><b>CONTRATO</b></td>
	    <td></td>
	</tr>
	";
	if (sizeof($polizas)>0){
	    foreach($polizas as $poliza){
		echo "
		    <tr>
			<td>$poliza[nombre]</td>
			<td>$poliza[fini]</td>
			<td>$poliza[ffin]</td>
			<td>$poliza[modopago]</td>
			<td>$poliza[contrato]</td>
			<td align='center'><b><a href='polizas.php?pay=1&custid=$_REQUEST[custid]&tab=$_REQUEST[tab]&pid=$poliza[id]'>Pagos</a></b></td>
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
function view_payments(){
    
    
    $params['custid']=$_REQUEST['custid'];
    $params['pid']=$_REQUEST['pid'];
    $poliza = GetPolizasByCustomer($params);
    //printarray($poliza);

    $status = $poliza['status'] == '1' ? 'Activa' : 'Cancelada';
    
    echo "
    <fieldset>
    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Informacion de poliza:&nbsp;</legend>
    <table border=0 cellspacing='0' cellpadding='4' width='500'>
	<tr><td height='10' colspan='2'>&nbsp;</td></tr>
	<tr>
	    <td><b>Tipo de poliza:</b></td>
	    <td>
		$poliza[nombre]
	    </td>
	</tr>
	<tr>
	    <td><b>Fecha Inicial:</b></td>
	    <td>
		$poliza[fini]
	    </td>
	</tr>
	<tr>
	    <td><b>Fecha Final:</b></td>
	    <td>
		$poliza[ffin]
	    </td>
	</tr>
	<tr>
	    <td><b>Modo de Pago:</b></td>
	    <td>
		$poliza[modopago]
	    </td>
	</tr>
	<tr>
	    <td><b>Contrato:</b></td>
	    <td>
		$poliza[contrato]
	    </td>
	</tr>
	<tr>
	    <td><b>Vigencia:</b></td>
	    <td>
		$status
	    </td>
	</tr>
	
	<tr><td height=10 colspan=2>&nbsp;</td></tr>
    </table>
    </fieldset><br>
    ";
    
       
    //Parametros de busqueda
    $payments = GetPaymentsByPolicy($poliza['id']);

    ?>
    <script type="text/javascript">
    $(document).ready(function() {

	$( "#fpago" ).datepicker(
	$.extend({},
	$.datepicker.regional["es"], {
	showStatus: true,
	showOn: "both",
	buttonImage: "calendar.png",
	buttonImageOnly: true,
	duration: "",
	changeYear: true,
	changeMonth: true,
	showButtonPanel: true
	}
	));
	$('img.ui-datepicker-trigger').css({'cursor' : 'pointer', "vertical-align" : 'middle'});

	$("#Editpayment").submit(function(){
	   if($("#fpago").val()==''){
		alert("Favor de proporcionar la fecha de pago");
		$("#fpago").focus();
		return false;
	   }
	   if($("#factura").val()==''){
		alert("Favor de proporcionar la factura");
		$("#factura").focus();
		return false;
	   }
	});

    });

    </script>
    <?php

    echo "
    <form name='Editpayment' method='POST' id='Editpayment' action='polizas.php'>
    <fieldset>
    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Pagos:&nbsp;&nbsp;</legend>
    <table border=0 width=800 class='crm'>
	<tr>
	    <td align='center'><b>NO. PAGO</b></td>
	    <td align='center'><b>FECHA INICIAL</b></td>
	    <td align='center'><b>FECHA FINAL</b></td>
	    <td align='center'><b>FECHA PAGO</b></td>
	    <td align='center'><b>FECHA CAPTURA</b></td>
	    <td align='center'><b>FACTURA</b></td>
	    <td align='center'><b>TICKET</b></td>
	    <td align='center'><b>ESTADO</b></td>
	</tr>
	";
	if (sizeof($payments)>0){
	    
	    $flag = true;
	    foreach($payments as $payment){
		$st = $payment['status'] == '0' ? "PAGADO" : "NO PAGADO";
		$fpago = $payment['fpago']=='00/00/0000' ? '' : $payment['fpago'];
		$freg = $payment['freg']=='00/00/0000' ? '' : $payment['freg'];
		$ticket = $payment['entity'] == '0' ? '' : "<a href='edit.php?e=$payment[entity]'>".$payment['entity']."</a>";

		if ($flag == true and $payment['status']=='1'){
		    echo "
		    <tr>
			<td align='center'>$payment[num]</td>
			<td align='center'>$payment[fini]</td>
			<td align='center'>$payment[ffin]</td>
			<td align='center'><input type='text' name='fpago' id='fpago' size='15' maxlength='15'></td>
			<td align='center'>$freg</td>
			<td align='center'><input type='text' name='factura' id='factura' size='15' maxlength='15'></td>
			<td align='center'>$ticket</td>
			<td align='center'>$st</td>
		    </tr>
		    ";
		    $flag = false;
		    $pago = $payment;
		}
		else{
		    echo "
			<tr>
			    <td align='center'>$payment[num]</td>
			    <td align='center'>$payment[fini]</td>
			    <td align='center'>$payment[ffin]</td>
			    <td align='center'>$fpago</td>
			    <td align='center'>$freg</td>
			    <td align='center'>$payment[factura]</td>
			    <td align='center'>$ticket</td>
			    <td align='center'>$st</td>
			</tr>
		    ";
		}
	    }

	    if ($flag==false){
	    echo "
		<tr>
		    <td colspan='7'>
			<input type='hidden' name='id' value=$pago[id]>
			<input type='hidden' name='custid' value=$poliza[custid]>
			<input type='hidden' name='pid' value=$poliza[id]>
			<input type='hidden' name='tab' value=$_REQUEST[tab]>
			<input type='hidden' name='fini' value=$pago[fini]>
			<input type='hidden' name='ffin' value=$pago[ffin]>
			<input type='hidden' name='num' value=$pago[num]>
			<input type='hidden' name='modopago' value=$poliza[modo_pago]>
		    </td>
		    <td align='center'>
			<input type='submit' name='btn_payment' value='Actualizar'>
		    </td>
		</tr>
	    ";
	    }
	}
	else{
	    echo "<tr><td align='center' colspan='4'>No se encontraron registros</td></tr>";
	}
	echo "
    </table>
    </fieldset>
    </form>
    <br>";

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

//*****************************************************************************
function GetPaymentsByPolicy($pid){

    $sql="
    SELECT
    pag_id as id,
    DATE_FORMAT(pag_fini,'%d/%m/%Y') as fini,
    DATE_FORMAT(pag_ffin,'%d/%m/%Y') as ffin,
    DATE_FORMAT(pag_fpago,'%d/%m/%Y') as fpago,
    DATE_FORMAT(pag_freg, '%d/%m/%Y') as freg,
    pag_factura as factura,
    pag_entity as entity,
    pag_num as num,
    pag_status as status,
    pag_notify as notify
    FROM
    CRMpagos
    WHERE
    pag_polid=$pid
    ";

    $result = mcq_array($sql);
    return $result;
}
//*****************************************************************************
function val_poliza($data){

    $fini = explode("/", $data['fechaini']);
    $fi = $fini['2']."-".$fini[1]."-".$fini[0];

    $ffin = explode("/", $data['fechafin']);
    $ff = $ffin['2']."-".$ffin[1]."-".$ffin[0];

    $sql="
    SELECT
    pol_id
    FROM
    CRMpolizas
    WHERE
    pol_custid=$data[custid]
    AND pol_cpid = '$data[tipo]'
    AND pol_status='1'
    AND pol_ffin > '$fi'
    ";

    $result = mcq_array($sql);
    return $result;
}
?>

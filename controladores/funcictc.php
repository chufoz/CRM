<?php
/* Funciones y librerias que se agregan a el CRM */

function checkIfFollower($listfollowers, $userid){
	// Verifica que el usuario conectado no este en la lista de seguidores 
	$valchkbox = "";
	$valfollow = 0;
	$list = explode(",", $listfollowers);
	if (in_array($userid, $list)) {
		$valchkbox = "CHECKED";
		$valfollow = 1;
	}
	$htmltxt = "
                    <input type='hidden' name='follow_posted' value='$valfollow'>
                    <input type='checkbox' class='radio' name=follow value='y' $valchkbox OnChange=javascript:AlertUser('IsChanged');>
                    Seguimiento
                    ";
	return $htmltxt;
}
/**
 * Da formato a la fecha para tener el formato de mysql
 * @param string $strdate Recibe formato <b style='color:blue'>dd/mm/aaaa</b>
 * @return string Devuelve formato <b style='color:blue'>aaaa/mm/dd</b>
 */
function formatDatetoMysql($strdate) {
        $spldate1=split("[/]",$strdate);
        return $spldate1[2]."-".$spldate1[1]."-".$spldate1[0];
}

function updateListFollow($userid, $listfollowers, $type) {
	// Inserta o borra de la lista de seguidores a el usuario logeado 
	$found = False;
	$list = $listfollowers != '' ? explode(",", $listfollowers) : array();

	if (in_array($userid, $list)) {
		$found = True;
	}
	if ($type) { // Agregar a el arreglo
		if (! $found) {
			$list[]=$userid;
			//$listfollowers .= $userid . ",";
		}
	} else { // Eliminar del arreglo
		if ($found) {
			//$newlist = str_replace($userid . ",", "", $listfollowers);
			//$listfollowers = $newlist;
			removeFromArray($list, $userid);
		}
	}
	return implode(",", $list);
}


function removeFromArray(&$array, $val){
    foreach($array as $key=>$value){
	if($value == $val){
	    unset($array[$key]);
	    $array = array_values($array);
	    return true;
	    break;
	}
    }
}


function GetUser($user_id) {
    if ($user_id){
        $row = db_GetRow("
                SELECT
                    id,
                    name,
                    FULLNAME,
                    EMAIL,
                    administrator,
                    password,
                    PROFILE,
                    flag_invoice,
                    flag_admin,
                    flag_opera,
                    HIDEENTITYTAB,
                    RECEIVEALLOWNERUPDATES,
                    ADDFORMS,
                    EMAILCREDENTIALS,
                    HIDEPBTAB,
                    RECEIVEDAILYMAIL,
                    ALLOWEDPRIORITYVARS,
                    ENTITYADDFORM,
                    HIDESUMMARYTAB,
                    SAVEDSEARCHES,
                    ALLOWEDSTATUSVARS,
                    ENTITYEDITFORM,
                    LASTFILTER,
                    SHOWDELETEDVIEWOPTION,
                    CLISTLAYOUT,
                    LASTSORT,
                    active,
                    noexp,
                    CLLEVEL,
                    HIDEADDTAB,
                    LIMITTOCUSTOMERS,
                    CUSTOMERREADONLY,
                    HIDECSVTAB,
                    exptime,
                    type,
                    ELISTLAYOUT,
                    HIDECUSTOMERTAB,
                    RECEIVEALLASSIGNEEUPDATES
                FROM
                CRMloginusers
                WHERE
                id=" . $user_id
                );
        $user = $row[0];
        $ret = $row;
        qlog("$user_id " . $user);
    }
    else{
        $ret = "";
        qlog("WARNING - GetCustomer - funcictc.php : Funcion llamada con parametro de entrada vacio");
    }
    return($ret);
}

function GetCustomer($cust_id) {
    if ($cust_id){
        $sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]customer WHERE id='$cust_id'";
	$result= mcq($sql,$db);
	$ret= mysql_fetch_array($result);
    }
    else{
        $ret = "";
        qlog("WARNING - GetCustomer - funcictc.php : Funcion llamada con parametro de entrada vacio");
    }
    return($ret);
}

function GetEntity($eid) {
        $entity = db_GetRow("
                SELECT
                    a.eid as eid,
                    a.category as category,
                    a.content as content,
                    a.status as status,
                    b.color as status_color,
                    a.priority as priority,
                    c.color as priority_color,
                    a.owner as owner,
                    a.assignee as assignee,
                    a.CRMcustomer as customer,
                    a.tp,
                    a.deleted,
                    a.duedate,
                    a.sqldate,
                    a.obsolete,
                    a.cdate,
                    a.waiting,
                    a.readonly,
                    a.closedate,
                    a.lasteditby,
                    a.createdby,
                    a.notify_assignee,
                    a.notify_owner,
                    a.openepoch,
                    a.closeepoch,
                    a.private,
                    a.duetime,
                    a.parent as parent,
                    a.finish_date as finish_date,
                    a.close_date as close_date,
                    a.start_date as start_date,
                    a.followers as followers
                FROM
                $GLOBALS[TBL_PREFIX]entity as a
                INNER JOIN
                $GLOBALS[TBL_PREFIX]statusvars as b on a.status=b.varname
                INNER JOIN
                $GLOBALS[TBL_PREFIX]priorityvars as c on a.priority=c.varname
                WHERE
                a.eid=" . $eid
                );
        
    return $entity;
}
//*****************************************************************************
/**
 * Busqueda general de polizas<br>
 * @param array $params arreglo de datos<br>
 * LLaves:<br>
 * - <b>[custid]</b> ID de cliente<br>
 * - <b>[status]</b> Status de poliza (1 vigente, 0 caducada)<br>
 * - <b>[pid]</b> ID de poliza<br>
 * @return array Arreglo con resultado de la consulta<br>
 * Si se proporciona 'pid' se obtiene un arreglo unidimensional<br>
 * De lo contrario se obtiene un arreglo multidimensional<br>
 * <b>LLaves:</b><br>
 * - <b>[id]</b> ID de poliza<br>
 * - <b>[custid]</b> ID de cliente<br>
 * - <b>[cpid]</b> ID de tipo de poliza<br>
 * - <b>[nombre]</b> Nombre de poliza <br>
 * - <b>[fini]</b> Fecha inicial <br>
 * - <b>[ffin]</b> Fecha finla <br>
 * - <b>[modopago]</b> Modo de pago <br>
 * - <b>[modo_pago]</b> Valor ENUM del modo de pago<br>
 * - <b>[contrato]</b> Contrato<br>
 * - <b>[ultimopago]</b> Fecha del ultimo pago<br>
 * - <b>[proximopago]</b> Fecha del proximo pago <br>
 * - <b>[status]</b> Status (1 Vigente, 0 Cancelada o vencida)<br>
 * - <b>[vencimiento]</b> Vencimiento (0 Al corriente, 1 Pagos vencidos)
 */
function GetPolizasByCustomer($params){


    $filter = "";
    if (isset($params['status'])){
	$filter.=" AND pol_status='$params[status]'";
    }
    if (isset($params['pid'])){
	$filter.=" AND pol_id='$params[pid]'";
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
    a.pol_active as active
    FROM CRMpolizas as a
    INNER JOIN CRMcatpolizas as b on a.pol_cpid=b.cpol_id
    WHERE
    pol_custid=$params[custid]
    $filter
    ";
    $result = isset($params['pid']) ? db_GetRow($sql) : mcq_array($sql);
    return $result;
}

function last_entities($cid){
    if($cid==''){
        return 0;
    }
    $yesterday  = date("Y-m-d",mktime(0, 0, 0, date("m") , (date("d")-1), date("Y")));
    $sql= "
        SELECT
        CRMentity.eid
        FROM
        CRMentity,
        CRMcustomer
        WHERE
        CRMcustomer.id=CRMentity.CRMcustomer
        AND
        deleted<>'y'
        AND
        owner<>'2147483647'
        AND
        assignee<>'2147483647'
        AND (
        status='Abierto'
        OR
        status='En espera')
        AND
        cdate>='$yesterday'
        AND
        id='$cid'
        limit 1
        ";
    $result = mcq($sql,$db);

    $return = mysql_fetch_array($result);
    if($return==''){
        return 0;
    }
    return $return['eid'];
}

function diff_date($start, $end){
    $start = strtotime($start);
    $end = strtotime($end);
    $diff = round(abs($end - $start), 2);
    
    if ($diff > 86400){
            $age = number_format($diff/86400, 2, ".", ",") .' dias';
    }
    elseif ($diff > 3600) {
            $age = number_format($diff/3600, 2, ".", ",") . ' horas';
    }
    elseif ($diff > 60){
            $age = number_format($diff/60, 2, ".", ",") .' minutos';
    }
    else{
            $age = number_format($diff, 2, ".", ",") .' segundos';
    }
    return $age;
}


function alerts($eid,$user){
    //Verificar el usuario admin
   $useradmin="Select flag_admin From CRMloginusers where id='$user'";
    $douseradmin=  mcq($useradmin, $db);
    $fetch=  mysql_fetch_array($douseradmin);
    if($fetch['flag_admin']=='1'){
        //si es usuario administrador entonces
        
     //checamos las alertas activas existentes para la entidad especificada   
     $select="Select * From CRMalerts where eid='$eid' and status!='0'";
     $result=  mcq($select, $db);
     
    if(mysql_num_rows($result)>=1){//si la entidad tiene una alerta activa mostramos opciones para poder editrara
        //cambiar la severidad o terminarla.
        $sever=  mysql_fetch_array($result);
            if($sever['severidad']=='1'){
            $checkA="CHECKED";
         }else{
             $checkM="CHECKED";
     }
        $alert.="
            <fieldset>
	    <legend>Alertas</legend>
	    <table width='900'><tr></td>
		    <table>
                     <tr>
                        <td>
                            <input type='radio' name='upalert' value='1' $checkA><span style='background: #F94646;'>&nbsp;Alta&nbsp;</span>
                        </td>
                        <td>
                            <input type='radio' name='upalert' value='2' $checkM><span style='background: #E2E052;'>&nbsp;Media&nbsp;</span>
                        </td>
                        <td>
                            <input type='radio' name='upalert' value='0'><span style='background: #66CC66;'>&nbsp;Ninguna&nbsp;</span>
                        </td>
                    </tr>
		    </table>
	    </td></tr></table>
            </fieldset>";
        return $alert;
     }else{//si no existe activa una alerta para esa entidad mostramos opciones para agregar alguna.
        $alert .= "
	    <fieldset>
	    <legend>Alertas</legend>
	    <table width='900'>
		<tr>
		    <td>
			<table>
			 <tr>
			    <td>
				<input type='radio' name='alert' value='1'><span style='background: #F94646;'>&nbsp;Alta&nbsp;</span>
			    </td>
			    <td>
				<input type='radio' name='alert' value='2'><span style='background: #E2E052;'>&nbsp;Media&nbsp;</span>
			    </td>
			    <td>
				<input type='radio' name='alert' value='' checked><span style='background: #66CC66;'>&nbsp;Ninguna&nbsp;</span>
			    </td>
			</tr>
			</table>
		    </td>
		</tr>
	    </table>
	    </fieldset>";
        return $alert;                  
               
        }
    }
}
function pre($id){
    $alerts="SELECT CRMloginusers.FULLNAME,CRMentity.CRMcustomer,CRMentity.category,CRMalerts.iduser,CRMalerts.severidad,CRMalerts.status,CRMalerts.eid 
                    FROM CRMalerts  INNER JOIN CRMentity INNER JOIN CRMloginusers ON CRMalerts.eid = CRMentity.eid AND CRMalerts.iduser=CRMloginusers.id 
                    WHERE CRMentity.CRMcustomer='$id' AND CRMalerts.status<>0 ORDER BY CRMalerts.severidad,CRMalerts.createdate ASC";
                     $sho=  mcq($alerts, $db);
                    $rowalert=mysql_fetch_array($sho);
                    return $rowalert[eid];
}

function showalert($eid){
    //------------seleccion de los clientes con alertas
    $alerts="SELECT CRMentity.CRMcustomer,CRMalerts.createdate,CRMentity.eid,CRMalerts.iduser,CRMalerts.eid as aeid,CRMalerts.severidad
	    FROM CRMalerts  INNER JOIN CRMentity ON CRMalerts.eid = CRMentity.eid
	    where CRMalerts.status='1' ORDER BY CRMalerts.severidad,CRMalerts.createdate ASC";
    $do=mcq($alerts,$db);
    $con=array();
    //error_log(str_replace(array("\t","\n")," ",$alerts));
    while($fetch=  mysql_fetch_array($do)){

	//--------seleccion de todas las entidades donde aparecen los clientes
	$dos="Select CRMcustomer,eid From CRMentity where CRMcustomer='$fetch[CRMcustomer]'";
	$do2=  mcq($dos, $db);
	$content=array();
	//almacenamos los valores de la consulta en un arreglo
	while($row=mysql_fetch_array($do2)){
	    $content[]=$row;
	}
	//si almacenamos algo enonces
	if (count($content)>=1){
	    $user=  GetCustomer(GetEntityCustomer($eid));//obtenemos el nombre de cliente  de la entidad
	    $creator=  GetUserName($fetch['iduser']);//obtenemos el usuario creador
	    //$comment=  GetEntityCategory($fetch['aeid']);
	    //recorremos y mostramos la alarma en este caso un icono al cual hacer click despliega un mensaje indicando las alarmas existentes para ese cliente.
	    foreach($content as $val){
		if($eid==$val[eid]){
		    if($fetch['severidad']=='1'){
			$alert= "<img src='alert/alerta_alta.png' align='top' id='content'  Onclick=\"javascript:jAlert('<strong>Cliente: </strong> $user[custname] <br><br><strong>Pendientes:</strong>".muestra($fetch[CRMcustomer])."<br>', 'Pendiente Administrativo Con Severidad Alta','alta');\" > ";
		    }elseif($fetch['severidad']=='2'){
			$alert= "<img src='alert/alerta_media.png' align='top' id='content'  Onclick=\"javascript:jAlert('<strong>Cliente:  </strong>$user[custname]<br><br><strong>Pendientes:</strong>".muestra($fetch[CRMcustomer])."<br>', 'Pendiente Administrativo Con Severidad Media','media');\" > ";
		    }
		    return $alert;
		}
	    }
	}else{
	    echo "No existe el cliente";
	}
    }
} 
//funcion para obtener recordatorios para un cliente.
//devolvemos una tabla con los valores de cada recordatorio para cada cliente.
function muestra($customer){ 
 $select="SELECT CRMentity.CRMcustomer,CRMalerts.createdate,CRMentity.eid,CRMalerts.iduser,CRMalerts.eid as aeid,CRMalerts.severidad
	    FROM CRMalerts  INNER JOIN CRMentity ON CRMalerts.eid = CRMentity.eid
	    where CRMalerts.status='1' and CRMentity.CRMcustomer='".$customer."' ORDER BY CRMalerts.severidad,CRMalerts.createdate ASC";
 $doselect=  mcq($select, $db);
 if(mysql_num_rows($doselect)>=1){
 $alerts="<table border=1><th style=font-size:13px>Entidad</th><th style=font-size:13px>Creador</th><th style=font-size:13px>Severidad</th><th style=font-size:13px>Creacion</th>";
 while($fe=  mysql_fetch_array($doselect)){
     if($fe[severidad]=='1'){
	 $color='#C62F2F';
	 $name='Alta';
     }else{
	 $color='#E59D0D';
	 $name='Media';
     }
     $alerts.="<tr><td align=center style=font-size:12px><a href=edit.php?e=$fe[eid] target=_blank>$fe[eid]</a></td><td align=center style=font-size:12px>".GetUserName($fe[iduser])."</td><td bgcolor=$color align=center style=color:white;font-size:12px;>$name</td><td>$fe[createdate]</td></tr>";
 }
 $alerts.="</table>";
 }
 return $alerts;
}

function showreminders(){
$showreminders="Select eid,reminderdate From CRMreminders where createdby='".$GLOBALS['USERID'] . "' and STR_TO_DATE(reminderdate, '%d-%m-%Y')>='".Date('Y-m-d')."' ORDER BY STR_TO_DATE(reminderdate, '%d-%m-%Y') ASC";
$doshowreminders=mcq($showreminders,$db);              
  if(mysql_num_rows($doshowreminders)>0){
      $table="<table border=1 width=100% class='showreminders'>
		    <th style='font-size:11px' bgcolor='#D8D8D8'><b>Entidad</b></th>
		    <th style='font-size:11px' bgcolor='#D8D8D8'><b>Categoria</b></th>
		    <th style='font-size:11px' bgcolor='#D8D8D8'><b>Fecha</b></th>
		    <th style='font-size:11px' bgcolor='#D8D8D8'><b>Recordar En:</b></th>";
  while($res=  mysql_fetch_array($doshowreminders)){
	   $day = substr($res['reminderdate'],0,2);
	  $mon = substr($res['reminderdate'],3,2);
	  $yer = substr($res['reminderdate'],6,10);
	  $NewDate = $yer . "-" . $mon . "-" . $day;
	  $fecha=strtotime($NewDate)-strtotime(Date('Y-m-d'));
	   if($fecha>0){
	   $recordar=" ".$fecha/(60*60*24)." dia(s)";
	   $bg="#30AE36";
	   $table.="<tr><td align=center><a href=edit.php?e=$res[eid] target=_blank>$res[eid]</a></td><td>".  GetEntityCategory($res[eid])."</td><td align=center width=100>$res[reminderdate]</td><td bgcolor=$bg align=center width=15%>$recordar</td></tr>";
	   }else{
	   if($fecha==0){
	       $bg="#F1F444";
	  $recordar=" Hoy";
	   $table.="<tr><td align=center><a href=edit.php?e=$res[eid] target=_blank>$res[eid]</a></td><td>".  GetEntityCategory($res[eid])."</td><td align=center width=100>$res[reminderdate]</td><td bgcolor=$bg align=center>$recordar</td></tr>";
	  }
	 }
    }
    $table.="</table>";
  }
  return $table;
}


/**
 * Funciones asociadas a panel de informacion de cliente
 * Datos de conexion se pasan como parametro, ya que son invocadas
 * desde el script autocomplete.php via ajax
 */

// <editor-fold defaultstate="collapsed" desc="Panel de informacion de cliente">


function show_panel($custid,$eid,$cnn){

    $data = unserialize(base64_decode($cnn));
    $link = mysql_connect($data['host'], $data['user'], $data['pass']);
    $db = mysql_select_db($data['database'], $link);

    //Verificar notas importartes del cliente, grupo y VPN

    $query = "SELECT id_customer_group, id_vpn, vpn_master, cust_notes FROM CRMcustomer WHERE id=$custid";
    $result = mcq($query, $db);

    $data = mysql_fetch_array($result,MYSQL_ASSOC);

    //$cnn = array('host' => $GLOBALS['host'][0], 'user' => $GLOBALS['user'][0], 'pass' => $GLOBALS['pass'][0], 'database' => $GLOBALS['database'][0]);
    //$cnn = base64_encode(serialize($cnn));
    
    $icons=array();
    //Notas importantes
    if ($data['cust_notes']!=''){
	$icons[]=array(
	    'src'=>'imgs/info2.png',
	    'alt'=>'',
	    'id'=>'info',
	    'title'=>'Notas importantes'
	);
    }

    //Grupo
    if ($data['id_customer_group']>0){
	$icons[]=array(
	    'src'=>'imgs/group.png',
	    'alt'=>'',
	    'id'=>'grupo',
	    'title'=>'Informacion de grupo'
	);
    }

    //VPN
    if ($data['id_vpn']>0){
	$img = $data['vpn_master'] == '1' ? 'vpn_server.png' : 'vpn_client.png';
	$icons[]=array(
	    'src'=>$img,
	    'alt'=>'',
	    'id'=>'vpn',
	    'title'=>'Informacion de VPN'
	);
    }

    //Polizas

    //Polizas vigentes
    $params['custid']=$custid;
    $params['status']='1';
    $polizas = GetPolizasByCustomer($params);

    foreach ($polizas as $poliza){

	if ($poliza['cpid']=='2'){
	    $img = $poliza['active']=='1' ? 'imgs/cv_enabled.png' : 'imgs/cv_disabled.png';
	    $icons[]=array(
		'src'=>$img,
		'alt'=>'',
		'id'=>'cvmax',
		'title'=>'Informacion de polizas'
	    );
	}

	if ($poliza['cpid']=='4'){
	    $img = $poliza['active']=='1' ? 'imgs/ia_enabled.png' : 'imgs/ia_disabled.png';
	    $icons[]=array(
		'src'=>$img,
		'alt'=>'',
		'id'=>'iadmin',
		'title'=>'Informacion de polizas'
	    );
	}
    }

    $icons[]=array(
	    'src'=>'imgs/pump.png',
	    'alt'=>'',
	    'id'=>'pumps',
	    'title'=>'Informacion de dispensarios'
	);

    $icons[]=array(
	    'src'=>'imgs/server.png',
	    'alt'=>'',
	    'id'=>'server',
	    'title'=>'Informacion de servidor'
	);

    if(intval($eid)>0){

    $icons[]=array(
	    'src'=>'imgs/procesos.png',
	    'alt'=>'',
	    'id'=>'proces',
	    'title'=>'Procesos activos por usuario'
	);
    }

    $panel="";
    $panel.="<table border='0'><tr>";
    foreach($icons as $icon){
	$panel.="<td width='30' align='center'><a title='$icon[title]' href='#' id='$icon[id]' style='border-bottom: 1px none;'><img alt='$icon[alt]'src='$icon[src]' style='border:0'></a></td>";
    }
    $panel.="</tr></table>";
    $panel.="<div id='dialog' title=''></div>";
    $panel.="
    <script type='text/javascript'>
	var custid='$custid';
	var cnn='$cnn';
	var userid='$GLOBALS[USERID]';
	var eid = '$eid';
	$(document).ready(function() {

	    $(\"#dialog\").dialog({
		bgiframe: true,
		autoOpen: false,
		modal: true,
		minWidth:650,
		closeOnEscape: true,
		resizable:false,
		draggable:false,
		hide: \"fade\"
	    });

	    $('#info,#grupo,#vpn,#cvmax,#iadmin,#pumps,#server').click(function(){
		var title = $(this).attr('title');
		var content = info($(this).attr('id'),custid,cnn);
		$('#dialog').dialog('option', 'title', title);
		$('#dialog').html(content);
		$(\"#dialog\").dialog(\"open\");
	    });

	    $('#proces').click(function(){
		var title = $(this).attr('title');
		var content = proceso($(this).attr('id'),userid,eid,cnn);
		$('#dialog').dialog('option', 'title', title);
		$('#dialog').html(content);
		$(\"#dialog\").dialog(\"open\");
	    });

	    $(document).delegate('input[name=update_proces]','click',function(){
		var idp = $('input[name=procesos]:checked').val();
		var update = update_proces(idp,eid,cnn)
		$('#dialog').dialog('close');
	    });

	    
	});

	var info = function (name,custid,cnn){
	    var content;
	    $.ajax({
		async: false,
		url: \"autocomplete.php?getinfo=1\",
		dataType: 'json',
		cache: false,
		data:{
		    name:name,
		    custid:custid,
		    cnn:cnn
		},
		success: function(result) {
		    if (result){
			content = result;
		    }
		}
	    });
	    return content;
	}

	var proceso = function (name,userid,eid,cnn){
	    var content;
	    $.ajax({
		async: false,
		url: \"autocomplete.php?getinfo=1\",
		dataType: 'json',
		cache: false,
		data:{
		    name:name,
		    userid:userid,
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
	}

	var update_proces = function (idp,eid,cnn){
	    $.ajax({
		async: false,
		url: \"autocomplete.php?update_proces=1\",
		dataType: 'json',
		cache: false,
		data:{
		    idp:idp,
		    eid:eid,
		    cnn:cnn
		},
		success: function(result) {}
	    });
	}

    </script>
    ";

    return $panel;
}

function get_client_info($custid,$cnn){

    $data = unserialize(base64_decode($cnn));
    $link = mysql_connect($data['host'], $data['user'], $data['pass']);
    $db = mysql_select_db($data['database'], $link);

    $query = "SELECT cust_notes FROM CRMcustomer WHERE id=$custid";
    $result = mcq($query, $db);

    if (mysql_num_rows($result)>0){
	$data = mysql_fetch_array($result,MYSQL_ASSOC);
	return nl2br($data['cust_notes']);
    }
    else{
	$noinfo="
	<table border=0 width='100%' class='crm'>
	    <tr>
		<td align='center'>NO EXISTE INFORMACION DISPONIBLE</td>
	    </tr>
	</table>
	<br>";

	return $noinfo;
    }

}

function get_group_info($custid,$cnn){

    $data = unserialize(base64_decode($cnn));
    $link = mysql_connect($data['host'], $data['user'], $data['pass']);
    $db = mysql_select_db($data['database'], $link);

    $sql = "
    SELECT
    a.grp_id as id,
    a.grp_nombre as nombre,
    a.grp_admin as admin,
    a.grp_oper as oper
    FROM CRMgrupos as a
    INNER JOIN CRMcustomer as b on a.grp_id=b.id_customer_group
    WHERE
    b.id=$custid
    AND a.grp_active='1'
    AND a.grp_type='0'";
    $result = mcq($sql, $db);

    $groupset="";
    
    if (mysql_num_rows($result)>0){
	$group_data=mysql_fetch_array($result);

	//members
	$sql = "SELECT id, custname FROM CRMcustomer WHERE id_customer_group='$group_data[id]' ORDER BY custname";
	$result = mcq($sql, $db);
	$members=array();
	while ($row=mysql_fetch_array($result)){
	    $members[] = $row['id'] == $custid ? "<b>$row[custname]</b>" : $row['custname'];
	}
	$groupset = "
	<table border=0 class='crm'>
	    <tr>
		<td width=30%><b>Nombre:</b></td>
		<td width=70%>".nl2br($group_data['nombre'])."</td>
	    </tr>
	    <tr>
		<td width=30% valign='top'><b>Contacto Administrativo:</b></td>
		<td width=70%>".nl2br($group_data['admin'])."</td>
	    </tr>
	    <tr>
		<td width=30% valign='top'><b>Contacto Operativo:</b></td>
		<td width=70%>".nl2br($group_data['oper'])."</td>
	    </tr>
	    <tr>
		<td colspan='2' align=center><b>Miembros:</b></td>
	    </tr>
	    <tr>
		<td colspan='2' align='left'>".implode("<br>", $members)."</td>
	    </tr>
	</table>
	<br>";
    }
    else{
	$groupset="
	    <table border=0 class='crm'>
	    <tr>
		<td width=100% align='center'>LA ESTACION NO PERTENECE A UN GRUPO</td>
	    </tr>
	</table>
	<br>
	";
    }
    return $groupset;

}

function get_vpn_info($custid,$cnn){

    $data = unserialize(base64_decode($cnn));
    $link = mysql_connect($data['host'], $data['user'], $data['pass']);
    $db = mysql_select_db($data['database'], $link);

    $sql = "
    SELECT
    a.vpn_id as id,
    a.vpn_nombre as nombre,
    a.vpn_bdcentral as bdcentral,
    a.vpn_ip_dns as ip_dns
    FROM CRMvpn as a
    INNER JOIN CRMcustomer as b on a.vpn_id=b.id_vpn
    WHERE
    b.id=$custid
    AND a.vpn_status='1'
    ";
    $result = mcq($sql, $db);
    if (mysql_num_rows($result)>0){
	$vpn_data=mysql_fetch_array($result);

	//members
	$sql = "SELECT id, custname, ip_vpn, vpn_master FROM CRMcustomer WHERE id_vpn='$vpn_data[id]' ORDER BY custname";
	$result = mcq($sql, $db);
	$members=array();
	while ($row=mysql_fetch_array($result)){
	    $members[]=array('ip_vpn'=>$row[ip_vpn],'custname'=>$row['custname'],'master'=>$row['vpn_master'],'id'=>$row['id']);
	}
	$vpnset = "
	<table border=0 class='crm'>
	    <tr>
		<td width=30%><b>Nombre:</b></td>
		<td width=70%>".nl2br($vpn_data['nombre'])."</td>
	    </tr>
	    <tr>
		<td width=30% valign='top'><b>Base de datos central:</b></td>
		<td width=70%>".nl2br($vpn_data['bdcentral'])."</td>
	    </tr>
	    <tr>
		<td width=30% valign='top'><b>IP / DNS:</b></td>
		<td width=70%>".nl2br($vpn_data['ip_dns'])."</td>
	    </tr>
	    <tr><td colspan='2' align='center'><b>Miembros:</b></td></tr>
	    <tr>
		<td colspan='2'>
		    <table class='crm' width='100%'>
			<tr>
			    <td>IP</td>
			    <td>Nombre</td>
			</tr>";
			foreach ($members as $member){
			    if ($member['master']=='1'){
				$vpnset.= "<tr><td><font color='navy'><b>$member[ip_vpn]</b></font></td><td><font color='navy'><b>$member[custname]</b></font></td></tr>";
			    }
			    else{
				$vpnset.= $member['id'] == $custid ? "<tr><td><b>$member[ip_vpn]</b></td><td><b>$member[custname]</b></td></tr>" : "<tr><td>$member[ip_vpn]</td><td>$member[custname]</td></tr>";
			    }

			}
			$vpnset.= "
		    </table>
		</td>
	    </tr>
	</table>
	<br>";


    }
    else{
	$vpnset="
	    <table border=0 class='crm'>
	    <tr>
		<td width=100% align='center'>LA ESTACION NO PERTENECE A UNA RED VPN</td>
	    </tr>
	</table>
	<br>
	";
    }
    return $vpnset;
    

}

function get_polizas_info($custid,$cnn){

    $data = unserialize(base64_decode($cnn));
    $link = mysql_connect($data['host'], $data['user'], $data['pass']);
    $db = mysql_select_db($data['database'], $link);

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
    a.pol_active as active
    FROM CRMpolizas as a
    INNER JOIN CRMcatpolizas as b on a.pol_cpid=b.cpol_id
    WHERE
    pol_custid=$custid
    AND pol_status='1'
    ";

    $polizas = mcq_array($sql);

    //Polizas vigentes
    $polizasset = "";
    if (sizeof($polizas)>0){
	$polizasset .= "
	<table border=0 width='500' class='crm'>
	    <tr>
		<td align='center' width='25%'><b>POLIZA</b></td>
		<td align='center' width='25%'><b>PERIODO</b></td>
		<td align='center' width='25%'><b>ESTADO DE COBRO</b></td>
		<td align='center' width='25%'><b>ESTADO</b></td>
	    </tr>";
	    foreach($polizas as $poliza){
		$stpago = $poliza['vencimiento'] == '0' ? 'AL CORRIENTE' : 'PAGOS VENCIDOS';
		$active = $poliza['active'] == '1' ? "<font color='green'><b>ACTIVA</b></font>" : "<font color='red'><b>INHABILITADA</b></font>";
		$polizasset.= "
		<tr>
		    <td>$poliza[nombre]</td>
		    <td>$poliza[fini]-$poliza[ffin]</td>
		    <td>$stpago</td>
		    <td align='center'>$active</td>
		</tr>
		";
	    }
	    $polizasset.= "
	</table>
	<br>";
    }
    else{
	$polizasset="
	    <table border=0 class='crm'>
	    <tr>
		<td width=100% align='center'>NO SE ENCONTRARON POLIZAS ACTIVAS</td>
	    </tr>
	</table>
	<br>
	";
    }
    return $polizasset;


}

function get_pumps_info($custid,$cnn){

    $data = unserialize(base64_decode($cnn));
    $link = mysql_connect($data['host'], $data['user'], $data['pass']);
    $db = mysql_select_db($data['database'], $link);

    $sql = "
    SELECT
    a.id,
    a.nodispenser,
    c.description as brand,
    d.description as generation,
    a.model, a.sernum as serie,
    a.datemanufacter,
    b.macaddress,
    b.ip,
    b.docsize,
    b.typecard,
    b.localversion,
    b.netversion,
    b.cenamver,
    b.typeconn,
    e.version as cenam
    FROM CRMdispenser as a
    INNER JOIN CRMmotherboard as b ON a.id = b.iddispenser
    INNER JOIN CRMbrands as c ON a.brand = c.id
    INNER JOIN CRMgeneration as d ON a.generation = d.id
    INNER JOIN CRMrelcenam as e ON b.cenamver=e.id
    WHERE
    a.idcustomer = $custid ORDER BY a.nodispenser";

    $pumps = mcq_array($sql);

    if (sizeof($pumps)>0){

	$pumpset .= "
	<table border=0 width='630' class='crm'>
	    <tr>
		<td align='center'><b>DISP.</b></td>
		<td align='center'><b>GENERACION</b></td>
		<td align='center'><b>MODELO</b></td>
                <td align='center'><b>SERIE</b></td>
		<td align='center'><b>IP</b></td>
		<td align='center'><b>MAC</b></td>
		<td align='center'><b>CENAM</b></td>
		<td align='center'><b>SOFTWARE</b></td>
	    </tr>";
	    foreach($pumps as $pump){

		//Software
		$sql="
		SELECT
		CONCAT(c.nombre,' ',b.version) as version
		FROM CRMdispenser as a
		INNER JOIN CRMsoft_pumps as b ON a.id=b.idpump
		INNER JOIN CRMsoft_catalog as c ON b.idsoft=c.idsoft
		WHERE 1
		AND a.idcustomer='$custid'
		AND a.id = $pump[id]
		";
		$result = mcq($sql,$db);
		$software="";
		if (mysql_num_rows($result)>0){
		    $soft=array();
		    while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
			$soft[]=$row['version'];
		    }
		    $software = implode(" / ", $soft);
		}

		$pumpset.= "
		<tr>
		    <td align='center'>$pump[nodispenser]</td>
		    <td align='center'>$pump[generation]</td>
		    <td align='center'>$pump[model]</td>
                    <td align='center'>$pump[serie]</td>
		    <td align='center'>$pump[ip]</td>
		    <td align='center'>$pump[macaddress]</td>
		    <td align='center'>$pump[cenam]</td>
		    <td align='center'>$software</td>
		</tr>
		";
	    }
	    $pumpset.= "
	</table>
	<br>";

    }
    else{
	$pumpset="
	<table border=0 width='100%' class='crm'>
	    <tr>
		<td align='center'>NO EXISTE INFORMACION DISPONIBLE</td>
	    </tr>
	</table>
	<br>";
    }

    return $pumpset;
    //error_log(var_export($pumps,1));

}

function get_server_info($custid,$cnn){
    

    $data = unserialize(base64_decode($cnn));
    $link = mysql_connect($data['host'], $data['user'], $data['pass']);
    $db = mysql_select_db($data['database'], $link);

    $sql = "
    SELECT
    CONCAT(a.os,' ',a.version) as os,
    CONCAT(a.brandmb,' ',a.modelmb,' ',a.ramcap) AS hw,
    a.hdtypeconf as 'hd',
    CASE a.serverorigin
	WHEN '0' THEN 'ICTC'
	WHEN '1' THEN 'ASIC'
	WHEN '2' THEN 'CLIENTE'
    END as origen,
    CASE
	WHEN a.dateprovpurch='0000-00-00' THEN 'N/D'
	ELSE DATE_FORMAT('%d/%m/%Y',a.dateprovpurch)
    END as cproveedor,
    CASE
	WHEN a.datecusrpurch='0000-00-00' THEN 'N/D'
	ELSE DATE_FORMAT('%d/%m/%Y',a.datecusrpurch)
    END as ccliente,
    CASE a.primarysrv
	WHEN '1' THEN 'SI'
	WHEN '0' THEN 'NO'
    END as primario,
    b.host as hostname,
    b.noipaccount as noip,
    b.mgateway as gateway,
    b.dgateway as interfaz,
    b.httpport as http,
    b.sshport as ssh
    FROM
    CRMesservers as a
    INNER JOIN CRMsrvconninfo as b ON a.id=b.idserver
    WHERE 1
    AND idcustomer =$custid
    AND status='1'
    ORDER BY priority
    ";

    $servers = mcq_array($sql);

    if (sizeof($servers)>0){

	$serverset="";
	foreach($servers as $server){

	    $serverset .= "
	    <table border=0 width='600' class='crm'>
	    <tr>
		<td align='center' colspan='4'><b>SERVIDOR  $server[hostname]</b></td>
	    </tr>
	    <tr>
		<td align='left'><b>OS</b></td>
		<td align='left'>$server[os]</td>
		<td align='left'><b>HW</b></td>
		<td align='left'>$server[hw]</td>
	    </tr>
	    <tr>
		<td align='left'><b>CONF. HD</b></td>
		<td align='left'>$server[hd]</td>
		<td align='left'><b>ORIGEN</b></td>
		<td align='left'>$server[origen]</td>
	    </tr>
	    <tr>
		<td align='left'><b>NOIP</b></td>
		<td align='left'>$server[noip]</td>
		<td align='left'><b>PRIMARIO</b></td>
		<td align='left'>$server[primario]</td>
	    </tr>
	    <tr>
		<td align='left'><b>GATEWAY</b></td>
		<td align='left'>$server[gateway]</td>
		<td align='left'><b>INTERFAZ</b></td>
		<td align='left'>$server[interfaz]</td>
	    </tr>
	    <tr>
		<td align='left'><b>HTTP</b></td>
		<td align='left'>$server[http]</td>
		<td align='left'><b>SSH</b></td>
		<td align='left'>$server[ssh]</td>
	    </tr>
	    <tr>
		<td align='left'><b>COMPRA PROV.</b></td>
		<td align='left'>$server[cproveedor]</td>
		<td align='left'><b>COMPRA CLIENTE</b></td>
		<td align='left'>$server[ccliente]</td>
	    </tr>

	</table>
	<br>";

	}

	
    }
    else{
	$serverset="
	<table border=0 width='100%' class='crm'>
	    <tr>
		<td align='center'>NO EXISTE INFORMACION DISPONIBLE</td>
	    </tr>
	</table>
	<br>";
    }
    return $serverset;

}

function get_procesos($userid,$eid,$cnn){


    $data = unserialize(base64_decode($cnn));
    $link = mysql_connect($data['host'], $data['user'], $data['pass']);
    $db = mysql_select_db($data['database'], $link);

    //Verificar si el proceso pertenece a otro usuario

    $sql = "
    SELECT idproceso FROM CRMentity WHERE eid = $eid
    ";
    $result = mcq($sql, $db);
    $idproceso = mysql_result($result, 0);

    if ($idproceso!=0){
	$sql="SELECT a.iduser, b.FULLNAME, a.descripcion FROM CRMprocesosbatch AS a INNER JOIN CRMloginusers as b ON a.iduser=b.id WHERE a.idproceso=$idproceso";
	$result = mcq($sql, $db);
	$uid = mysql_result($result, 0, 0);
	$uname = mysql_result($result, 0, 1);
	$desc = mysql_result($result, 0, 2);

    }

    if ($idproceso==0 or $userid==$uid){

	$sql = "
	SELECT
	idproceso as id,
	iduser,
	descripcion,
	estatus
	FROM
	CRMprocesosbatch
	WHERE iduser=$userid
	AND estatus='ACTIVO'
	";

	$procesos = mcq_array($sql);

	if (sizeof($procesos)>0){
	    $default = $idproceso == '0' ? 'CHECKED' : '';
	    $proces_set .= "
	    <table border=0 width='600' class='crm'>
		<tr>
		    <td align='center' colspan='2'><b>Proceso asignado</b></td>
		</tr>
		<tr>
		    <td align='center' width='30'><input type='radio' name='procesos' id='procesos_0' value='0' $default></td>
		    <td align='left'>Ninguno</td>
		</tr>
		";
		foreach($procesos as $proceso){
		    $check = $proceso['id'] == $idproceso ? 'CHECKED' : '';
		    $proces_set.= "
		    <tr>
			<td align='center' width='30'><input type='radio' name='procesos' id='procesos_$proceso[id]' value='$proceso[id]' $check></td>
			<td align='left'>$proceso[descripcion]</td>
		    </tr>
		    ";
		}
		$proces_set.= "
		<tr>
		    <td align='center' colspan='2'><input type='button' id='update_proces' name='update_proces' value='Actualizar'></td>
		</tr>
	    </table>
	    <br>
	    ";
	}
	else{
	    $proces_set="
	    <table border=0 width='100%' class='crm'>
		<tr>
		    <td align='center'>NO EXISTEN PROCESOS ACTIVOS</td>
		</tr>
	    </table>
	    <br>";
	}
    }
    else{
	
	$proces_set="
	<table border=0 width='600' class='crm'>
	    <tr>
		<td align='center' colspan='2'><b>El ticket ya se encuentra asignado a un proceso</b></td>
	    </tr>
	    <tr>
		<td align='left' width='200'><b>Creador</b></td>
		<td align='left'><b>Proceso</b></td>
	    </tr>
	    <tr>
		<td align='left'>$uname</td>
		<td align='left'>$desc</td>
	    </tr>
	</table>
	<br>";

    }
    return $proces_set;

}

function get_parent($eid,$cnn){

    $data = unserialize(base64_decode($cnn));
    $link = mysql_connect($data['host'], $data['user'], $data['pass']);
    $db = mysql_select_db($data['database'], $link);
    $sql = "
    SELECT
    a.category,
    a.content,
    a.priority,
    a.owner,
    a.assignee,
    a.CRMcustomer,
    b.custname
    FROM
    CRMentity as a
    INNER JOIN CRMcustomer as b on a.CRMcustomer=b.id
    WHERE a.eid=$eid
    ";

    $result = mcq($sql, $db);

    if (mysql_num_rows($result)>0){

	$data = mysql_fetch_array($result,MYSQL_ASSOC);

	$sql="SELECT id,FULLNAME,name FROM CRMloginusers WHERE id=$data[owner]";
	$result = mcq($sql, $db);
	$owner = mysql_result($result, 0, 1);

	$sql="SELECT id,FULLNAME,name FROM CRMloginusers WHERE id=$data[assignee]";
	$result = mcq($sql, $db);
	$assignee = mysql_result($result, 0, 1);

	$parent="
	    <table border=0 width='600' class='crm'>
		<tr>
		    <td align='center' colspan='3'>TICKET: <b><a href='edit.php?e=$eid' target='_blank' style='color: navy;'>$eid</a></b></td>
		</tr>
		<tr>
		    <td align='center' colspan='3'>Cliente: <b>$data[custname]</b></td>
		</tr>
		<tr>
		    <td align='left'>Propietario: <b>$owner</b></td>
		    <td align='left'>Asignado: <b>$assignee</b></td>
		    <td align='left'>Prioridad: <b>$data[priority]</b></td>
		</tr>
		<tr>
		    <td align='center' colspan='3'><b>Categoria</b></td>
		</tr>
		<tr>
		    <td align='left' colspan='3'>$data[category]</td>
		</tr>
		<tr>
		    <td align='center' colspan='3'><b>Descripcion</b></td>
		</tr>
		<tr>
		    <td align='left' colspan='3'>$data[content]</td>
		</tr>
	    </table>
	    <br>";
    }
    else{
	$parent="
	<table border=0 class='crm' width='600'>
	<tr>
	    <td width=100% align='center'>NO SE ENCONTRO NINGUNA TAREA RELACIONADA</td>
	</tr>
	</table>
	<br>
	";
    }
    return $parent;
}

function update_idproceso($idp,$eid,$cnn){

    $data = unserialize(base64_decode($cnn));
    $link = mysql_connect($data['host'], $data['user'], $data['pass']);
    $db = mysql_select_db($data['database'], $link);

    $sql="UPDATE CRMentity SET idproceso=$idp WHERE eid=$eid";
    mcq($sql,$db);
    return true;
}

// </editor-fold>

//INSERTA TAGS EN DB
function insertTags($tags, $entity, $cnn){
    if(count($tags)>0){
        $tags = array_unique($tags);
        foreach ($tags as $value) {
            //funcion insertar db
            $sql="INSERT INTO CRMentitytag (id, identity, idtag, status)
                          VALUES ('', $entity, '$value', '1')";
            mcq($sql, $cnn);
        }
    }
}

//UPDATE TAGS EN DB
function updateTags($tags,$entity, $cnn){    
    //Actualizamos todos los registros a cero
    $sql="UPDATE CRMentitytag SET status='0' WHERE identity=$entity";
    mcq($sql, $cnn);
    
    if(count($tags)<=0){
        return;
    }
    $tags = array_unique($tags);
    $_strTags = implode(',', $tags);
    //natsort($tags);
    
    //Traemos registros 
    $sql="SELECT idtag FROM CRMentitytag WHERE identity=$entity AND idtag in ($_strTags)";
    $result = mcq($sql, $cnn);
    if (mysql_num_rows($result)>0){
        $data = array();
        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
            array_push($data, $row['idtag']);
        }

        foreach ($tags as $value) {
            //Observamos si existe en el registro los tags y aseguramos status 1,
            //si no existe lo creamos
            if (in_array($value, $data)){
                $sql="UPDATE CRMentitytag SET status='1' WHERE identity=$entity AND idtag = '$value'";
            }else{
                $sql="INSERT INTO CRMentitytag (id, identity, idtag, status)
                      VALUES ('', $entity, '$value', '1')";
            }
            mcq($sql, $cnn);
        }
    }else{
        insertTags($tags, $entity, $cnn);
    }
}

function getTags($cnn){
    $dbase = unserialize(base64_decode($cnn));
    $link = mysql_connect($dbase['host'], $dbase['user'], $dbase['pass']);
    $db = mysql_select_db($dbase['database'], $link);
        
    $sql = "SELECT id, name, parent, flag, solution FROM CRMcttags WHERE name like '$_GET[req]%' AND status='1' AND flag='1'";
    $data = mcq_array($sql);
    if(count($data)==0){
        $data[] = array('id'=>'0', 'name'=>$_GET['req'], 'parent'=>'0', 'flag'=>'0', 'solution'=>'');
    }
    return $data;
}
function getAllTags(){
    $sql = "SELECT id, name, parent, flag, solution FROM CRMcttags WHERE status='1' AND (flag='1' AND parent='0')";
    $result = mcq($sql, $db);
    while ($row = mysql_fetch_array($result)) {
        $data[] = $row;
    }
    return $data;
}
function getTagsByEntity($eid, $cnn){
    $dbase = unserialize(base64_decode($cnn));
    $link = mysql_connect($dbase['host'], $dbase['user'], $dbase['pass']);
    $db = mysql_select_db($dbase['database'], $link);
    
    $sql = "
    SELECT
    a.id as id,
    a.name as name,
    a.parent as parent,
    a.flag as flag,
    a.solution as solution
    FROM
    CRMcttags as a
    INNER JOIN
    CRMentitytag as b
    ON a.id=b.idtag
    WHERE
    b.identity='$eid'
    AND b.status='1' AND a.flag='1'
    ";
     
    $tmp = mcq_array($sql);    
    if(count($tmp)==0){
        $data=false;
    }else{
        $i=0;
        foreach ($tmp as $reg) {
            $tmpTags = array();
            if($reg['parent']!=0){
                getTagsParents($reg['parent'], $cnn, $tmpTags);
            }
            $tmpTags[]=$reg;
            $data[$i] = $tmpTags;
            unset ($tmpTags);
            $i++;
        }
    }
    
    return $data;
}
function getTagsById($id, $cnn){
    $dbase = unserialize(base64_decode($cnn));
    $link = mysql_connect($dbase['host'], $dbase['user'], $dbase['pass']);
    $db = mysql_select_db($dbase['database'], $link);
    
    $sql = "SELECT a.id as id, a.name as name, a.parent as parent,
    a.flag as flag, a.solution as solution FROM CRMcttags as a WHERE a.id='$id'";
    $tmp = mcq_array($sql);
    
    
    $i=0;
    foreach ($tmp as $reg) {
        $tmpTags = array();
        if($reg['parent']!=0){
            getTagsParents($reg['parent'], $cnn, $tmpTags);
        }
        $tmpTags[]=$reg;
        $data[$i] = $tmpTags;
        unset ($tmpTags);
        $i++;
    }
    return $data;
}
function getTagsByArray($array){
    $_strTags = implode(',', $array);
    $sql = "SELECT a.id as id, a.name as name, a.parent as parent,
    a.flag as flag, a.solution as solution FROM CRMcttags as a WHERE a.id in($_strTags)";
    $result = mcq($sql, $db);
    while ($row = mysql_fetch_array($result)) {
        $data[] = $row;
    }
    return $data;
}
function getTagsParents($parent, $cnn, &$array){
    $dbase = unserialize(base64_decode($cnn));
    $link = mysql_connect($dbase['host'], $dbase['user'], $dbase['pass']);
    $db = mysql_select_db($dbase['database'], $link);
        
    $sql = "SELECT id, name, parent, flag, solution, status
        FROM CRMcttags WHERE id = '$parent' AND status='1'";
    $result = mcq($sql, $dbase);
    $tmp = mysql_fetch_array($result,MYSQL_ASSOC);
    if($tmp['status']==1){
        array_unshift($array, $tmp);
        if($tmp['parent']!=0){
            getTagsParents($tmp['parent'], $cnn, $array);
        }
    }
}
function insertNewTag($val, $cnn){
    $dbase = unserialize(base64_decode($cnn));
    $link = mysql_connect($dbase['host'], $dbase['user'], $dbase['pass']);
    $db = mysql_select_db($dbase['database'], $link);
    
    $sql = "
    INSERT INTO CRMcttags (name, parent, flag, solution, status)
    VALUES ('$val', '', '1', '', '1')";
    mcq($sql, $db);
    return mysql_insert_id();
}
function getTagsUnclass(){
    $sql = "SELECT id, name, parent, flag, solution, status FROM CRMcttags WHERE flag='1' AND parent='0'";
    $data = mcq_array($sql);
    return $data;
}
function getTagsClass(){
    $sql = "SELECT id, name, parent, flag, solution, status FROM CRMcttags WHERE parent!='0' OR (flag='0' AND parent='0')";
    $data = mcq_array($sql);
    return $data;
}
function getTag($id, $db){
    $dbase = unserialize(base64_decode($db));
    $link = mysql_connect($dbase['host'], $dbase['user'], $dbase['pass']);
    $db = mysql_select_db($dbase['database'], $link);
    
    $sql = "SELECT a.id as id, a.name as name, a.parent as parent,
    a.flag as flag, a.solution as solution, a.status as status FROM CRMcttags as a WHERE a.id='$id'";
    
    $result = mcq($sql, $db);
    $data = mysql_fetch_array($result,MYSQL_ASSOC);
    return $data;
}
function updateTag($data, $db){
    $dbase = unserialize(base64_decode($db));
    $link = mysql_connect($dbase['host'], $dbase['user'], $dbase['pass']);
    $db = mysql_select_db($dbase['database'], $link);
    
    $sql = "UPDATE CRMcttags as a SET a.name ='$data[name]', a.parent ='$data[parent]',
    a.solution ='$data[solution]', a.status ='$data[status]' WHERE a.id='$data[id]'";
    
    $result = mcq($sql, $db);
}
function getEntitysByTag($id, $cnn){
    $dbase = unserialize(base64_decode($cnn));
    $link = mysql_connect($dbase['host'], $dbase['user'], $dbase['pass']);
    $db = mysql_select_db($dbase['database'], $link);
    
    $sql = "
    SELECT
    a.id as id,
    a.identity as eid
    FROM
    CRMentitytag as a
    WHERE
    a.idtag='$id'
    AND a.status='1'
    ";
     
    $data = mcq_array($sql);    
    
    return $data;
}
function getTreeTags($cnn){
    $dbase = unserialize(base64_decode($cnn));
    $link = mysql_connect($dbase['host'], $dbase['user'], $dbase['pass']);
    $db = mysql_select_db($dbase['database'], $link);
    //Devuelve a los padres principales, se caracterizan por
    //pork no tienen padres encima y ademas son flag 0
    $sql = "SELECT id, name, parent, flag, solution FROM CRMcttags WHERE status='1' AND (flag='0' AND parent='0')";
    $data = mcq_array($sql,$db);
    
    $i=0;
    foreach ($data as $reg) {
        $data[$i]['children'] = getAllChildTags($reg['id'], $cnn);
        $i++;
    }
    return $data;
}
function getAllChildTags($id, $cnn){
    $dbase = unserialize(base64_decode($cnn));
    $link = mysql_connect($dbase['host'], $dbase['user'], $dbase['pass']);
    $db = mysql_select_db($dbase['database'], $link);
    
    $sql = "SELECT id, name, parent, flag, solution FROM CRMcttags WHERE status='1' AND parent='$id'";
    
    $data = mcq_array($sql,$db);
    
    $i=0;
    foreach ($data as $reg) {
        $data[$i]['children'] = getAllChildTags($reg['id'], $cnn);
        $i++;
    }
    return $data;
}

function ShowEntitiesOpen(){
    $conn_db = array('host' => $GLOBALS['host'][0], 'user' => $GLOBALS['user'][0], 'pass' => $GLOBALS['pass'][0], 'database' => $GLOBALS['database'][0]);
    $conn_db = base64_encode(serialize($conn_db));
//print_array($_REQUEST);
    $filter['customer']=isset($_REQUEST['pdfiltercustomer']) ? $_REQUEST['pdfiltercustomer'] : 'all';
    $filter['owner']=isset($_REQUEST['pdfilterowner']) ? $_REQUEST['pdfilterowner'] : 'all';
    $filter['assignee']=isset($_REQUEST['pdfilterassignee']) ? $_REQUEST['pdfilterassignee'] : 'all';
    $filter['status']=isset($_REQUEST['pdfilterstatus']) ? $_REQUEST['pdfilterstatus'] : array('Abierto');
    $filter['priority']=isset($_REQUEST['pdfilterpriority']) ? $_REQUEST['pdfilterpriority'] : array('Alto','Bajo','Critico','Medio');
    $filter['category']=isset($_REQUEST['pdfiltercategory']) ? $_REQUEST['pdfiltercategory'] : "";
    $filter['fini']=isset($_REQUEST['pdfilterfini']) ? $_REQUEST['pdfilterfini'] : "";
    $filter['ffin']=isset($_REQUEST['pdfilterffin']) ? $_REQUEST['pdfilterffin'] : "";
    $filter['fecha']=isset($_REQUEST['pdfilterfecha']) ? $_REQUEST['pdfilterfecha'] : "";
    $filter['duedate']=isset($_REQUEST['pdfilterduedate']) ? $_REQUEST['pdfilterduedate'] : "";
    $filter['tags']=isset($_REQUEST['pdfilterTags']) ? $_REQUEST['pdfilterTags'] : NULL;

    //Inputs de filtrado

    // <editor-fold defaultstate="collapsed" desc="Customer">

    $sql = "SELECT id,custname FROM $GLOBALS[TBL_PREFIX]customer ORDER BY custname";
    $result = mcq_array($sql, $db);
    $customer = "<select name='pdfiltercustomer' id='pdfiltercustomer'><option value='all'>Todo</option>";
    foreach($result as $cust){
	$sel = $filter['customer'] == $cust['id'] ? 'selected' : '';
	$customer.="<option value='$cust[id]' $sel>$cust[custname]</option>";
    }
    $customer.="</select>";
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Owner">
    $sql = "SELECT id,FULLNAME FROM $GLOBALS[TBL_PREFIX]loginusers WHERE active='yes' ORDER BY FULLNAME";
    $result = mcq_array($sql, $db);
    $owner = "<select name='pdfilterowner' id='pdfilterowner'><option value='all'>Todo</option>";
    foreach($result as $user){
	$sel = $filter['owner'] == $user['id'] ? 'selected' : '';
	$owner.="<option value='$user[id]' $sel>$user[FULLNAME]</option>";
    }
    $owner.="</select>";
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Asignee">
    $sql = "SELECT id,FULLNAME FROM $GLOBALS[TBL_PREFIX]loginusers WHERE active='yes' ORDER BY FULLNAME";
    $result = mcq_array($sql, $db);
    $assignee = "<select name='pdfilterassignee' id='pdfilterassignee'><option value='all'>Todo</option>";
    foreach($result as $user){
	$sel = $filter['assignee'] == $user['id'] ? 'selected' : '';
	$assignee.="<option value='$user[id]' $sel>$user[FULLNAME]</option>";
    }
    $assignee.="</select>";
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Status">
    $sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]statusvars ORDER BY varname";
    $result = mcq_array($sql, $db);
    $status="";
    $arr_status=array();
    foreach($result as $st){
	$chk = in_array($st['varname'], $filter['status']) ? 'checked' : '';
	$status.="<input type='checkbox' name='pdfilterstatus[]' id='pdfilterstatus_$st[id]' value='$st[varname]' $chk><span style='background: $st[color]'>&nbsp;$st[varname]&nbsp;</span>";
	$arr_status[]=$st['varname'];
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Etiquetas">
    //@todo deja seleccionados los filtros de tags
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Priority">
    $sql = "SELECT varname,id,color FROM $GLOBALS[TBL_PREFIX]priorityvars ORDER BY varname";
    $result = mcq_array($sql, $db);
    $priority="";
    $arr_priority=array();
    foreach($result as $pr){
	$chk = in_array($pr['varname'], $filter['priority']) ? 'checked' : '';
	$priority.="<input type='checkbox' name='pdfilterpriority[]' id='pdfilterpriority_$pr[id]' value='$pr[varname]' $chk><span style='background: $pr[color]'>&nbsp;$pr[varname]&nbsp;</span>";
	$arr_priority[]=$pr['varname'];
    }
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Category">
    $category="<input type='text' name='pdfiltercategory' id='pdfiltercategory' value='$filter[category]' size='150' maxlength='250'>";
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Periodo">
    $sel_fini = $filter['fecha'] == '1' ? "SELECTED" : '';
    $sel_ffin = $filter['fecha'] == '2' ? "SELECTED" : '';
    $period="
    &nbsp;Desde&nbsp;<input type='text' name='pdfilterfini' id='pdfilterfini' value='$filter[fini]' size='11' maxlength='12'>&nbsp;&nbsp;
    &nbsp;Hasta&nbsp;<input type='text' name='pdfilterffin' id='pdfilterffin' value='$filter[ffin]' size='11' maxlength='12'>&nbsp;&nbsp;
    <select name='pdfilterfecha' id='pdfilterfecha'><option value='1' $sel_fini>Inicio</option><option value='2' $sel_ffin>Fin</option></select>
    ";
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Vencimiento">
    $check = $filter['duedate'] == '1' ? "CHECKED" : '';
    $vencimiento="
    <input type='checkbox' name='pdfilterduedate' id='pdfilterduedate' value='1' $check>
    ";
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Filtrado">

    $mpfilter="";
    if ($_REQUEST['last_entyties']){
	// <editor-fold defaultstate="collapsed" desc="ACTIVIDADES RECIENTES DEL CLIENTE">
	$yesterday = date("Y-m-d", mktime(0, 0, 0, date("m"), (date("d") - 1), date("Y")));

	$mpfilter="
	AND a.status IN ('Abierto','En espera','Revision Operativa','Revision administrativa')
	AND a.cdate>='$yesterday'
	AND a.CRMcustomer='$_REQUEST[pdfiltercustomer]'
	";
	// </editor-fold>
    }
    elseif ($_REQUEST['my_activities']) {
        // <editor-fold defaultstate="collapsed" desc="MIS ACTIVIDADES">
	$mpfilter = "
	AND (a.status='Abierto' OR a.status='En espera')
	AND a.assignee = $GLOBALS[USERID]
	";
	//// </editor-fold>
    }
    elseif ($_REQUEST['open_group']) {
	// <editor-fold defaultstate="collapsed" desc="SUPERVISION TAREAS ABIERTAS (GRUPO)">

        $filter_sto = mcq("Select id from $GLOBALS[TBL_PREFIX]loginusers where profile=$_REQUEST[profile]", $db);
        while ($row = mysql_fetch_array($filter_sto)) {
            $filter_data_sto[] = $row['id'];
        }
        $group = implode($filter_data_sto, ",");

        $mpfilter = "
	AND (a.status='Abierto' OR a.status='En espera')
	AND a.assignee in ($group)
	";
	// </editor-fold>
    }
    elseif ($_REQUEST['sta']) {
	// <editor-fold defaultstate="collapsed" desc="QUERY - SUPERVISION TAREAS ADMINISTRATIVAS">

        $filter_sta = mcq("Select id from $GLOBALS[TBL_PREFIX]loginusers where profile=$_REQUEST[profile]", $db);
        while ($row = mysql_fetch_array($filter_sta)) {
            $filter_data_sta[] = $row['id'];
        }
        $group = implode($filter_data_sta, ",");

        $mpfilter = "
	AND status='Revision administrativa'
	AND assignee in ($group)
	";
	// </editor-fold>
    }
    elseif ($_REQUEST['sto']) {
	// <editor-fold defaultstate="collapsed" desc="QUERY - SUPERVISION TAREAS OPERATIVAS">

        $filter_sto = mcq("Select id from $GLOBALS[TBL_PREFIX]loginusers where profile=$_REQUEST[profile]", $db);
        while ($row = mysql_fetch_array($filter_sto)) {
            $filter_data_sto[] = $row['id'];
        }
        $group = implode($filter_data_sto, ",");

        $mpfilter = "
	AND status='Revision Operativa'
	AND assignee in ($group)
	";
	// </editor-fold>
    }
    else{
	if ($filter['customer'] <> "all") {
	    $mpfilter .= " AND a.CRMcustomer = '" . $filter['customer'] . "'";
	}
	if ($filter['owner'] <> "all") {
	    $mpfilter .= " AND a.owner = '" . $filter['owner'] . "'";
	}
	if ($filter['assignee'] <> "all") {
	    $mpfilter .= " AND a.assignee = '" . $filter['assignee'] . "'";
	}
	if (sizeof($filter['status'])>0) {
	    if (sizeof(array_diff($arr_status,$filter['status']))>0){
		$mpfilter .= " AND a.status IN ('". implode("','",array_values($filter['status']))."')";
	    }
	}
	if (sizeof($filter['priority'])>0) {
	    if (sizeof(array_diff($arr_priority,$filter['priority']))>0){
		$mpfilter .= " AND a.priority IN ('". implode("','",array_values($filter['priority']))."')";
	    }
	}
	if ($filter['category'] !='') {
	    $mpfilter .= " AND a.category LIKE '%".$filter['category']."%'";
	}

	if ($filter['fini'] !='' and $filter['ffin']!='') {
	    $fini = implode("-",array_reverse(explode("/", $filter['fini'])));
	    $ffin = implode("-",array_reverse(explode("/", $filter['ffin'])));
	    if ($filter['fecha']=='1'){
		$mpfilter .= " AND a.start_date BETWEEN '$fini 00:00:00' AND '$ffin 23:59:59'";
	    }
	    elseif($filter['fecha']=='2'){
		$mpfilter .= " AND a.close_date BETWEEN '$fini 00:00:00' AND '$ffin 23:59:59'";
	    }
	}

	if ($filter['duedate'] !='') {
	    $mpfilter .= " AND a.duedate <> ''";
	}
	/*
        if (sizeof($filter['tags']) > 0) {
            $_strTags = implode(',', $filter['tags']);
	    $mpfilter .= " AND g.idtag in ($_strTags) AND g.status='1'";
            
            //Hacemos busqueda de los tags para añadirlos en un inico de acuerdo a los anteriores
            $_tagspost = getTagsByArray($filter['tags']);
            $_tagsposthtml = '';
            foreach ($_tagspost as $value) {
                $_tagsposthtml .= "<input type='hidden' name='pdfilterTags[]' value='".$value['id']."'>";
            }
	}
	 *
	 */
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Query">

    $sql = "
    SELECT
    a.eid as eid,
    a.category as category,
    a.status as status,
    e.color as status_color,
    a.priority as priority,
    f.color as priority_color,
    a.owner as id_owner,
    c.FULLNAME as owner,
    a.assignee as id_assignee,
    d.FULLNAME as assignee,
    a.CRMcustomer as cid,
    b.custname as custname,
    a.start_date as startdate,
    a.close_date as closedate,
    a.duedate as duedate,
    DATE_FORMAT(a.start_date,'%Y-%m-%d') as creacion
    FROM CRMentity as a
    INNER JOIN CRMcustomer as b ON b.id=a.CRMcustomer
    INNER JOIN CRMloginusers as c ON a.owner=c.id
    INNER JOIN CRMloginusers as d ON a.assignee=d.id
    INNER JOIN CRMstatusvars as e ON a.status=e.varname
    INNER JOIN CRMpriorityvars as f ON a.priority=f.varname
    /*INNER JOIN CRMentitytag as g ON a.eid=g.identity*/
    WHERE 1
    AND deleted<>'y'
    AND owner<>'2147483647'
    AND assignee<>'2147483647'
    $mpfilter
    /*GROUP BY a.eid*/
    ORDER BY
    a.eid DESC,
    a.sqldate DESC,
    a.status ASC,
    a.priority ASC
    ";
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Ligas de filtrado por supervison">
    $sta = mcq("Select flag_admin, flag_opera, profile, administrator  from $GLOBALS[TBL_PREFIX]loginusers where id=$GLOBALS[USERID]", $db);
    $sta = mysql_fetch_array($sta,MYSQL_ASSOC);
    $links=array();

    if ($sta['flag_admin'] == 1) {//Supervision tareas administrativas
	$links[]="<a href='index.php?ShowEntitiesOpen=1&tab=$GLOBALS[tab]&sta=1&profile=$sta[profile]&pdfiltercustom=1'><b> Supervisar Tareas Administrativas</b></a>";
    }
    if ($sta['flag_opera'] == 1) {//Supervision tareas operativas
	$links[]="<a href='index.php?ShowEntitiesOpen=1&tab=$GLOBALS[tab]&sto=1&profile=$sta[profile]&pdfiltercustom=1'><b> Supervisar Tareas Operativas</b></a>";
    }
    if ($sta['administrator'] == 'yes') {//Supervision tareas abiertas (grupo)
	$links[]="<a href='index.php?ShowEntitiesOpen=1&tab=$GLOBALS[tab]&open_group=1&profile=$sta[profile]&pdfiltercustom=1'><b> Supervisar Tareas Abiertas</b></a>";
    }
    //Mis actividades
    $links[]="<a href='index.php?ShowEntitiesOpen=1&my_activities=1&tab=$GLOBALS[tab]&pdfiltercustom=1'><b> Mis Actividades</b></a>";

    $supervision="";
    if (sizeof($links)>0) {
	$supervision = "<tr><td colspan='3' align='center'>".implode("&nbsp;&nbsp;&nbsp;&nbsp;", $links)."</td></tr><tr><td colspan='3' height='5'></td></tr>";
    }
    // </editor-fold>


    //Se verifica que se hayan recibido parametros de filtrado
    $pdfilter = preg_grep("/^pdfilter/", array_keys($_REQUEST));
    if (sizeof($pdfilter)>0){
	$entities = mcq_array($sql, $db);
    }

    // <editor-fold defaultstate="collapsed" desc="Javascript">
    ?>    
    <LINK href="jquery/external/datatable.css" rel="StyleSheet" type="text/css">
    <script type="text/javascript" language="javascript" src="jquery/external/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
	var dTable;
	$(document).ready(function(){
	    $("#btnfilter").click(function(){
		var status_checked=$("input[id^=pdfilterstatus]:checked").length;
		var priority_checked=$("input[id^=pdfilterpriority]:checked").length;
		if (status_checked==0){
		    alert("Seleccione al menos un Estatus de actividad");
		    return false;
		}
		if (priority_checked==0){
		    alert("Seleccione al menos una Prioridad de actividad");
		    return false;
		}
	    });

	    $("#estado").click(function(){
		$("input[id^=pdfilterstatus]").attr("checked", "checked");
	    });
	    $("#prioridad").click(function(){
		$("input[id^=pdfilterpriority]").attr("checked", "checked");
	    });

	    $("#pdfilterfecha").change(function(){
		if ($(this).val()=='2'){
		    $("input[id^=pdfilterstatus]").each(function(){
			if ($(this).val()=='Cerrado'){
			    $(this).attr('checked','checked');
			}
		    });
		}
	    });

	    $( "#pdfilterfini,#pdfilterffin" ).datepicker(
		$.extend({},
		$.datepicker.regional["es"], {
		    showStatus: true,
		    showOn: "both",
		    buttonImage: "calendar.png",
		    buttonImageOnly: true,
		    duration: "",
		    appendText: "",
		    beforeShow: customRange,
		    changeYear: true,
		    changeMonth: true,
		    showButtonPanel: true,
		    onSelect: filldate
		}
	    ));
	    $('img.ui-datepicker-trigger').css({'cursor' : 'pointer', "vertical-align" : 'middle', "margin-left":"2px"});
	    $("#ui-datepicker-div").css({ 'display': 'none' });

	    function filldate(value,input){
		if (input.id=="pdfilterfini"){
		    if ($('#pdfilterffin').val()==''){
			$('#pdfilterffin').val($('#pdfilterfini').val());
		    }
		}
		else if (input.id=="pdfilterffin"){
		    if ($('#pdfilterfini').val()==''){
			$('#pdfilterfini').val($('#pdfilterffin').val());
		    }
		}

	    }

	    function customRange(input) {
		return {
		    minDate: (input.id == "pdfilterffin" ? $("#pdfilterfini").datepicker("getDate") : null),
		    maxDate: (input.id == "pdfilterfini" ? $("#pdfilterffin").datepicker("getDate") : null)
		};
	    }

	    dTable=$('#entities').dataTable({
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
		    { "bVisible": false, "aTargets": [ 0 ] },
		    { "iDataSort": 0, "aTargets": [ 1 ] }
		]
	    });

	    dTable.fnSort( [ [1,'desc'],[0,'desc'] ] );
            
            //Tags
            $("#dialogTags").dialog({
		bgiframe: true,
		autoOpen: false,
		modal: true,
		minWidth:450,
		closeOnEscape: true,
		resizable:false,
		draggable:false,
		hide: "fade",
                width: 'auto',
                height : 'auto',
                resize: "auto",
                resizeStop: function(event, ui) {
                    $(this).dialog('option','position','center');
                }
	    });
            
            <?php //<editor-fold defaultstate="collapsed" desc="Treeview"> ?>
		/*
                tree = $("#treeview")
                    .jstree({
                        "themes" : {
                            "theme" : "default",
                            "dots" : false,
                            "icons" : false
                        },
                        "json_data" : {
            	            "ajax" : {
            	                url: "autocomplete.php?tags=8",
                                dataType: "json",
            	                data : {
                                    ids: '<?php //echo $_strTags?>',
                                    db: '<?php //echo $conn_db; ?>'
            	                },
                                success: function (result) {
                                    //console.log(result);
                                    return result;
                                }
            	            }
            	        },  
                        "plugins" : ["themes", "json_data", "checkbox", "sort", "ui"]
                    })
                    // 1) if using the UI plugin bind to select_node
                    .bind("select_node.jstree", function (event, data) {
                        // `data.rslt.obj` is the jquery extended node that was clicked
                        //console.log(data.rslt.obj.attr("id"));
                    })
                    // 2) if not using the UI plugin - the Anchor tags work as expected
                    //    so if the anchor has a HREF attirbute - the page will be changed
                    //    you can actually prevent the default, etc (normal jquery usage)
                    //.delegate("a", "click", function (event, data) { event.preventDefault(); })
		    */

            <?php //</editor-fold> ?>
            
            //Evento click para mostar treeview
	    /*
            $("#tagsList").on('click', function(){
                $("#pdfilterTags").find('input').each(function (){
                    var tmpid = $(this).val();
                    $("#treeview li").each(function(){
                       if($(this).attr('value')==tmpid) {
                           $(this).removeClass('jstree-unchecked');
                           $(this).addClass('jstree-checked');
                       }else{
                           $(this).addClass('jstree-unchecked');
                           $(this).removeClass('jstree-checked');
                       }
                    });
                });                
                $("#dialogTags").dialog("open");
            });
	    */
            
            //Evento continue para filtrar
	    /*
            $("#continue").on('click', function(){
                $("#pdfilterTags").html('');
                ////////////// revisa que opciones estan seleccionadas 
                $("#treeview li").each(function(){
                    if($(this).hasClass('jstree-checked')){
                        var str = "<input type='hidden' name='pdfilterTags[]' value='"+$(this).attr('value')+"'>";
                        $("#pdfilterTags").append(str);
                    }
                });
                //////////////
                $("#dialogTags").dialog("close");
            });
	    */
            
            $('body').css({'height':'100%'});
	});
    </script>
    <?php
    // </editor-fold>
    ?>
    <table width="100%">
	<tr>
	    <td>
		<fieldset>
		    <legend>&nbsp;<img src='crmlogosmall.gif' alt="">&nbsp;&nbsp;Actividades:&nbsp;&nbsp;</legend>
		    <table align="center">
			<tr>
			    <td>
				<?php
				// <editor-fold defaultstate="collapsed" desc="Formulario de filtrado">
				?>
				<br>
				<fieldset>
				    <legend>Filtrado:</legend>
				    <form name="filter" method="GET" action="<?php echo $_SERVER['PHP_SELF'];?>" autocomplete="off">
				    <input type="hidden" name="ShowEntitiesOpen" value="1">
				    <input type="hidden" name="tab" value="<?php echo $GLOBALS['tab'];?>">
				    <table>
					<tr>
					    <td align="left"><b>Cliente:</b></td>
					    <td align="left"><b>Creador:</b></td>
					    <td align="left"><b>Responsable:</b></td>
					</tr>
					<tr>
					    <td align="left"><?php echo $customer;?></td>
					    <td align="left"><?php echo $owner;?></td>
					    <td align="left"><?php echo $assignee;?></td>
					</tr>
					<tr><td colspan="3" height="5"></td></tr>
					<tr>
					    <td align="left"><label id="estado" style="cursor: pointer;"><b>Estado: </b></label>&nbsp;<?php echo $status;?></td>
					    <td colspan="2" align="left"><label id="prioridad" style="cursor: pointer;"><b>Prioridad: </b></label>&nbsp;<?php echo $priority;?></td>
					</tr>
					<tr><td colspan="3" height="5"></td></tr>
					<tr><td colspan="3" align="left"><b>Categoria:</b>&nbsp;&nbsp;<?php echo $category;?></td></tr>
					<tr><td colspan="3" height="5"></td></tr>
					<!--
                                        <tr><td colspan="3" align="left">
                                                <b>Etiquetas:</b>
                                                <b id='tagsList' style="color: red;" class="buttons3 pointer">
                                                    &nbsp;+&nbsp;
                                                </b>
                                                <b  id='pdfilterTags'>
                                                    <?php //echo $_tagsposthtml; ?>
                                                </b>
                                                <br>
                                                <div id='dialogTags' title='Listado de Etiquetas' align='center'>
                                                    <div id="treeview" align="left">
                                                    </div>
                                                    <br>
                                                    <b id='continue' class="buttons3 pointer">Continuar</b>
                                                    <br>
                                                    <br>
                                                </div>
                                            </td>
                                        </tr>
					-->
					<tr><td colspan="3" height="5"></td></tr>
					<tr><td colspan="3" align="left"><b>Fecha:</b>&nbsp;&nbsp;<?php echo $period;?>&nbsp;&nbsp;<b>Vencimiento:</b>&nbsp;&nbsp;<?php echo $vencimiento;?> Si/No</td></tr>

					<tr><td colspan="3" height="5"></td></tr>
					<?php echo $supervision;?>
					<tr>
					    <td colspan="3" align="center">
						<input type="submit" name="btnfilter" id="btnfilter" value="Filtrar">&nbsp;&nbsp;
						<input type="reset" name="btnreset" id="btnreset" value="Limpiar">
					    </td>
					</tr>
				    </table>
				    </form>
				</fieldset>
				<br>
				<?php
				// </editor-fold>
				?>
			    </td>
			</tr>
		    </table>
		    <table>
			<tr>
			    <td>
				<?php
				// <editor-fold defaultstate="collapsed" desc="Tabla de resultados">
				?>
				<table id="entities" align="center" class="crm">
				    <thead>
					<tr>
					    <th></th>
					    <th width="45">ID</th>
					    <th width="400">Cliente</th>
					    <th width="200">Creador</th>
					    <th width="200">Responsable</th>
					    <th width="130">Estado</th>
					    <th width="60">Prioridad</th>
					    <th width="300">Categoria</th>
					    <th width="105">Creacion</th>
					    <th width="120">Vencimiento</th>
					    <th width="120">Edad / Duracion</th>
					</tr>
				    </thead>
				    <tbody>
					<?php
					foreach($entities as $entity){

					    if ($entity['status'] <> "Cerrado") {
						$nowepoch = date('U');
						$txt = "Edad";
					    } else {
						$nowepoch = strtotime($entity['closedate']);
						$txt = "Duracion";
					    }

					    $age_in_seconds = $nowepoch - strtotime($entity['startdate']);
					    if ($age_in_seconds > 86400) {
						$age = "" . round($age_in_seconds / 86400, 2) . " Dias";
					    } elseif ($age_in_seconds > 3600) {
						$age = " " . round($age_in_seconds / 3600, 2) . " hrs";
					    } elseif ($age_in_seconds > 60) {
						$age = "" . round($age_in_seconds / 60, 2) . " min";
					    } elseif ($age_in_seconds <> $nowepoch) {
						$age = "" . round($age_in_seconds, 0) . " sec";
					    }

					    $duedate = implode("-",array_reverse(explode("-", $entity['duedate'])));

					    echo "
					    <tr>
						<td>$entity[eid]</td>
						<td><b><a href='edit.php?e=$entity[eid]'>$entity[eid]</a></b></td>
						<td>$entity[custname]</td>
						<td>$entity[owner]</td>
						<td>$entity[assignee]</td>
						<td align='center' bgcolor='$entity[status_color]'>$entity[status]</td>
						<td align='center' bgcolor='$entity[priority_color]'>$entity[priority]</td>
						<td>$entity[category]</td>
						<td align='center'>$entity[creacion]</td>
						<td align='center'>$duedate</td>
						<td>$txt $age</td>
					    </tr>
					    ";
					}
					?>
				    </tbody>
				</table>
				<br><br><br><br>
				<?php
				// </editor-fold>
				?>
			    </td>
			</tr>
		    </table>
		</fieldset>
	    </td>
	</tr>
    </table>
    <?php

}

function getInfoHw($cnn, $params){
    $data = unserialize(base64_decode($cnn));
    $link = mysql_connect($data['host'], $data['user'], $data['pass']);
    $db = mysql_select_db($data['database'], $link);

    $filter='';
    
    if($params[customer]!=''){
        $filter.= " AND c.custname like '%$params[customer]%'";
    }
    if($params[server]!=''){
        $filter.= " AND b.host like '%$params[server]%'";
    }
    if($params[noip]!=''){
        $filter.= " AND b.noipaccount like '%$params[noip]%'";
    }
    
    if($params[serie]!=''){
        $filter.= " AND d.sernum like '%$params[serie]%'";
    }
    if($params[model]!=''){
        $filter.= " AND d.model like '%$params[model]%'";
    }
    if($params[generation]!=''){
        $filter.= " AND d.generation = '$params[generation]'";
    }
    
    $sql = "
    SELECT
    CONCAT(a.os,' ',a.version) as os,
    CONCAT(a.brandmb,' ',a.modelmb,' ',a.ramcap) AS hw,
    a.hdtypeconf as 'hd',
    CASE a.serverorigin
	WHEN '0' THEN 'ICTC'
	WHEN '1' THEN 'ASIC'
	WHEN '2' THEN 'CLIENTE'
    END as origen,
    CASE
	WHEN a.dateprovpurch='0000-00-00' THEN 'N/D'
	ELSE DATE_FORMAT('%d/%m/%Y',a.dateprovpurch)
    END as cproveedor,
    CASE
	WHEN a.datecusrpurch='0000-00-00' THEN 'N/D'
	ELSE DATE_FORMAT('%d/%m/%Y',a.datecusrpurch)
    END as ccliente,
    CASE a.primarysrv
	WHEN '1' THEN 'SI'
	WHEN '0' THEN 'NO'
    END as primario,
    b.host as hostname,
    b.noipaccount as noip,
    b.mgateway as gateway,
    b.dgateway as interfaz,
    b.httpport as http,
    b.sshport as ssh,
    c.custname as custname,
    c.id as idcustomer,
    c.cust_homepage as dns
    FROM
    CRMcustomer as c
    INNER JOIN CRMesservers as a ON c.id=a.idcustomer
    INNER JOIN CRMsrvconninfo as b ON a.id=b.idserver
    LEFT JOIN CRMdispenser as d ON c.id=d.idcustomer
    WHERE 1
    $filter
    AND status='1'
    GROUP BY b.host
    ORDER BY priority
    ";

    $data = mcq_array($sql);
    return $data;
}

function regReportHw($params, $cnn){
    $data = getInfoHw($cnn, $params);
    $html = '';
    if(count($data)>0){
        foreach ($data as $reg) {
            $html.=
            "<tr>
                <td align='center'>
                    <img idreg='$reg[idcustomer]' alt='server'
                         src='imgs/edit.png' name='server' class='pointer'>
                </td>
                <td>$reg[custname]</td>
                <td>$reg[hostname]</td>
                <td>$reg[noip]</td>
                <td>$reg[dns]</td>
                <td align='center'>
                    <img idreg='$reg[idcustomer]' alt='pumps'
                         src='imgs/server.png' name='pumps' class='pointer'>
                </td>
             </tr>
            ";
        }
    }
    return $html;
}

function getStatisticTags($cnn, $params){
    $dbase = unserialize(base64_decode($cnn));
    $link = mysql_connect($dbase['host'], $dbase['user'], $dbase['pass']);
    $db = mysql_select_db($dbase['database'], $link);
    
    $filter='';
    if(count($params)>0){
        $strIdtag = implode(',', $params);
        $filter = " AND b.idtag in ($strIdtag)";
    }
    
    $sql = "
        SELECT 
        a.id as idtag,
        b.id,
        COUNT(*) as total,
        a.name as name,        
        a.parent as parent        
        FROM CRMcttags as a
        LEFT JOIN CRMentitytag as b ON a.id=b.idtag and a.status='1'
        WHERE a.status='1'       
        AND NOT(a.flag='1' AND a.parent='0')         
        $filter
        GROUP BY a.id";
    $data = mcq_array($sql);
    //error_log(str_replace("\n", '', $sql));
    if(count($data)==0){
        foreach ($params as $value) {
            $tmp = getTag($value, $cnn);            
            $data[]=array('idtag'=>$value, 'total'=>0, 'name'=>$tmp['name'], 'parent'=>$tmp['parent']);
        }
    }
    
    return $data;
}
function getTotalTags($cnn){
    $dbase = unserialize(base64_decode($cnn));
    $link = mysql_connect($dbase['host'], $dbase['user'], $dbase['pass']);
    $db = mysql_select_db($dbase['database'], $link);
        
    $sql = "
        SELECT COUNT(*) as total
        FROM CRMcttags 
        WHERE status='1' AND NOT(flag='1' AND parent='0')";
    $result = mcq($sql, $db);
    $data = mysql_fetch_array($result,MYSQL_ASSOC);
    if(count($data)==0){
        $data=array('total'=>0);
    }
    return $data['total'];
}
function getGenerationDisp($cnn){
    $dbase = unserialize(base64_decode($cnn));
    $link = mysql_connect($dbase['host'], $dbase['user'], $dbase['pass']);
    $db = mysql_select_db($dbase['database'], $link);
        
    $sql = "
        SELECT id, description
        FROM CRMgeneration
        ";
    $data = mcq_array($sql);
    
    return $data;
}

function AllEntitiesNotReaded() {

    global $fullname, $lang, $user_id, $alreadyshowed;
    print "<tr><td NOWRAP><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$fullname's " . strtolower($lang[entities]) . " (No Leido)&nbsp;</legend><table width='100%' border=0>";

    $sql = "
    SELECT
    DISTINCT
    CRMentity.eid AS id,
    duedate,
    CRMentity.category AS cat,
    tp,
    CRMloginusers.FULLNAME AS name,
    CRMcustomer.custname AS cust,
    CRMentity.status AS status
    FROM CRMentity,CRMcustomer,CRMloginusers,CRMentitydetail
    WHERE
    assignee='" . $user_id . "'
    AND deleted <> 'yes'
    AND CRMcustomer.id=CRMentity.CRMcustomer
    AND CRMloginusers.id=CRMentity.assignee 
    AND CRMentity.eid=CRMentitydetail.ed_eid
    AND CRMentity.deleted='n'
    AND CRMentity.status<>'Cerrado'
    AND CRMentitydetail.readby_assignee='no'
    UNION
    SELECT
    DISTINCT
    CRMentity.eid AS id,
    duedate,
    CRMentity.category AS cat,
    tp,
    CRMloginusers.FULLNAME AS name,
    CRMcustomer.custname AS cust,
    CRMentity.status AS status
    FROM CRMentity,CRMcustomer,CRMloginusers,CRMentitydetail
    WHERE
    owner='" . $user_id . "'
    AND deleted <> 'yes'
    AND CRMcustomer.id=CRMentity.CRMcustomer
    AND CRMloginusers.id=CRMentity.assignee
    AND CRMentity.eid=CRMentitydetail.ed_eid
    AND CRMentity.deleted='n'
    AND CRMentity.status<>'Cerrado'
    AND CRMentitydetail.readby_owner='no'
    ORDER BY tp DESC";

    $result = mcq($sql, $db);
    while ($recent = mysql_fetch_array($result)) {
	$vf=vencimiento($recent['duedate']);
	if($vf>0){
	    if(strpos($vf,'.')!=null){
	    $vf=number_format($vf,2);}
	    $colordue='F86363';//color para las fechas vencidas
	    $comment="Vencido Hace ".$vf." dias";//comentario de vencimiento
	}elseif($vf<0){
	    $vf*=-1;
	    $colordue='30AE36';
	    $comment="Vence en ".$vf." dia(s)";
	}elseif($vf=="cero"){
	    $colordue='F1F444';
	    $comment="Vence Hoy ";
       }else{
	 $colordue='white';
	 $comment="";
	}
	$color = GetStatusColor($recent['status']);
	if (CheckEntityAccess($recent['id']) == "ok" || CheckEntityAccess($recent['id']) == "readonly") {
	    if (!in_array($recent[id], $alreadyshowed)) {
		print "<tr><td NOWRAP width='4%' bgcolor='$color'><b>&nbsp;- $recent[id]</td>
		<td NOWRAP bgcolor='$colordue'><a href='edit.php?e=$recent[id]' class='bigsort'>$recent[cust]</a></td>
		<td NOWRAP bgcolor='$colordue'><a href='edit.php?e=$recent[id]' class='bigsort'>$recent[cat] ($lang[assignedto] $recent[name])</a></b></td>
		<td NOWRAP bgcolor='$colordue'>$comment</td>
		</tr>";
		$count++;
	    }
	}
    }
    if ($count < 1) {
        print "<tr><td NOWRAP width='10%'>$lang[noresults]</td></tr>";
    }
    print "</table></fieldset></td></tr>";
}

?>

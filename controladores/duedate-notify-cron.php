<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This is the duedate notifier
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
set_time_limit(0);
ini_set('memory_limit', '32M');
extract($_REQUEST);
$c_l = "1";
include ("config.inc.php");
include ("getset.php");
include ("language.php");

print "<pre>\n\n";

if ($password<>$GLOBALS['cronpassword']) {
    print "Incorrect user authentication string for this repository. Quitting.\n\n";
	exit;
}

if (!$reposnr) {
	print "WARNING!\n\nNo repository number submitted (or the value = 0, which doesn't get passed to the webserver). Assuming repos# 0!\n\n";
	$reposnr=0;
} 



$db = mysql_connect($host[$reposnr], $user[$reposnr], $pass[$reposnr]);
mysql_select_db($database[$reposnr],$db);

$webhost = getenv("HOSTNAME");

// Check for alarm dates in the calendar table:
CheckForAlarmDates();

$GLOBALS['CRON_RUNNING'] = true;

print "Synchronising fail-over database ..\n";
//SynchroniseAllFailOverDatabases();
SynchroniseFailOverDatabase();

// Check for duedates to trigger in the entity table:

      // >X - Initialize this variable so the display looks good when 0 triggers fired.
      $trgd = 0;

	$sqldate = date('Y-m-d');
	qlog(" Due date is . $sqldate");
	$sql = "SELECT eid FROM $GLOBALS[TBL_PREFIX]entity WHERE sqldate='" . $sqldate . "'";
	$result= mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
              // >X - the following line has been added to clear out the array of
              // E-mail messages sent for each ticket.  Without it, each user will
              // only receive one E-mail per time this script is run, no matter how
              // many tickets they have due.
              unset ($GLOBALS['email_send_to']);
		ProcessTriggers("duedate_reached",$row['eid'],"Miscellaneous trigger");
		qlog("Enabling due-date trigger for entity " . $row['eid']);
		$trgd++;
	}

print "--------------------------------------------------------------------\n";
print $trgd . " triggers fired (duedate reached)\n";
print "--------------------------------------------------------------------\n";
notifypolicies();
print "\n\n********************************************************************\n";
print "Envio de recordatorios:\n";
print "********************************************************************\n\n";
SendPersonificatedDailyOverviewMail("si");
print "\n\n********************************************************************\n";
print "Envio de tareas pendientes:\n";
print "********************************************************************\n\n";
SendPersonificatedDailyOverviewMail("no");
Send_Dail_Mail_Admin();


print "\n</pre>\n";

// Journalling function (Entity ID, Message)
function journal($eid,$msg,$JournalType="entity") {
	global $EnableEntityJournaling;
	if (strtoupper($EnableEntityJournaling)=="YES" || (stristr($msg,"[admin]"))) {
		
		$msg = addslashes($msg);

		// $msg = base64_encode($msg);

		$sql = "INSERT INTO " . $GLOBALS[TBL_PREFIX] ."journal (eid,user,message,type) VALUES('$eid','" . $GLOBALS[USERID] . "','$msg','" . $JournalType ."')";

		mcq($sql,$db);
	}
}
function uselogger($comment,$dummy_extra_not_used){
	global $REMOTE_ADDR, $HTTP_SERVER_VARS, $actuser, $username, $user, $HTTP_USER_AGENT,$name,$logqueries;
		
		// here comes the mail trigger
	


	 if (getenv(HTTP_X_FORWARDED_FOR)){ 
	   $ip=getenv(HTTP_X_FORWARDED_FOR); 
	 } 
	 else { 
	   $ip=getenv(REMOTE_ADDR); 
	 } 
	
	
	if (!$comment) {
		$qs  = getenv("QUERY_STRING");
		$qs .= getenv("HTTP_POST_VARS");
		$qs .= $comment;
	} else {
		$qs = addslashes($comment);
	}

	$url = $HTTP_SERVER_VARS["PHP_SELF"];
	
	$query ="INSERT into $GLOBALS[TBL_PREFIX]uselog (ip, url, useragent, qs, user) VALUES ('$ip', '$url', '$HTTP_USER_AGENT' , '$qs','$name')";
	mcq($query,$db);

	if ($logqueries) {
		qlog("'$ip', '$url', '$HTTP_USER_AGENT' , '$qs','$name'");
	}
}

function notifypolicies(){
    print "Creando notificaciones de polizas.............\n";
    //Buscar pagos vencidos no notificados-------------------------------------

    $offset = 0;
    $fref  = date("Y-m-d",mktime(0, 0, 0, date('m')  , date('d')+$offset, date('Y')));

    $sql = "
    SELECT
    a.pag_id as id,
    a.pag_polid,
    DATE_FORMAT(a.pag_fini,'%d/%m/%Y') as fini,
    DATE_FORMAT(a.pag_ffin,'%d/%m/%Y') as ffin,
    a.pag_num as num,
    a.pag_status,
    a.pag_notify,
    b.pol_custid as custid,
    b.pol_modopago as modopago,
    c.customer_resp as responsable,
    d.cpol_nombre as nombre
    from CRMpagos AS a
    INNER JOIN CRMpolizas AS b ON a.pag_polid=b.pol_id
    INNER JOIN CRMcustomer AS c ON b.pol_custid=c.id
    INNER JOIN CRMcatpolizas AS d ON b.pol_cpid=d.cpol_id
    where
    a.pag_status='1' and a.pag_notify='0'
    and a.pag_fini < '$fref'
    ";
    $result = mcq_array($sql, $db);

    $status = "Abierto";
    $prioridad = "Medio";
    $sqldate = "3003-01-01";
    $cdate = date('Y-m-d');
    $openepoch = date('U');
    $formid=22;
    $start = date("Y-m-d H:i:s");

    foreach($result as $row){


	if ($row['responsable']!=0){

	    if ($row['modopago']=='A'){
		$np='1';
	    }
	    elseif ($row['modopago']=='S'){
		$np='2';
	    }
	    elseif ($row['modopago']=='T'){
		$np='4';
	    }
	    elseif ($row['modopago']=='M'){
		$np='12';
	    }

	    //Crear tickets de notificacion de cobro---------------------------

	    $insert = "
	    INSERT INTO CRMentity(
	    category,
	    content,
	    status,
	    priority,
	    owner,
	    assignee,
	    CRMcustomer,
	    sqldate,
	    cdate,
	    lasteditby,
	    createdby,
	    openepoch,
	    formid,
	    start_date
	    )
	    VALUES(
	    'Cobro de poliza $row[nombre]',
	    'Cobro de poliza $row[nombre], periodo del $row[fini] Al $row[ffin], pago $row[num]/$np',
	    '$status',
	    '$prioridad',
	    $row[responsable],
	    $row[responsable],
	    $row[custid],
	    '$sqldate',
	    '$cdate',
	    $row[responsable],
	    $row[responsable],
	    '$openepoch',
	    $formid,
	    '$start'
	    )
	    ";
	    $result = mcq($insert,$db);
	    $id = mysql_insert_id();

	    //Actualizar status de notificacion en pagos ----------------------

	    $sql = "
	    UPDATE CRMpagos SET pag_notify='1', pag_entity=$id WHERE pag_id = $row[id]
	    ";

	    $result = mcq($sql, $db);
	}
    }

    //-------------------------------------------------------------------------

    //Verificar polizas vencidas-----------------------------------------------

    $today = date("Y-m-d");
    $sql = "
    SELECT pol_id as id FROM CRMpolizas WHERE pol_status='1' and pol_ffin < '$today'
    ";
    $polizas = mcq_array($sql);

    foreach($polizas as $poliza){

	//Verificar si la poliza tiene pagos pendientes, en caso de no haber
	//pagos pendientes, se marca la poliza como vencida
	$sql = "SELECT * FROM CRMpagos WHERE pag_polid = $poliza[id] and pag_status='1'";
	$pagos = mcq_array($sql);

	if (sizeof($pagos)==0){
	    $sql = "UPDATE CRMpolizas SET pol_status = '0' WHERE pol_id = $poliza[id]";
	    mcq($sql, $db);
	}


    }

    //-------------------------------------------------------------------------


    //Verificar polizas con pagos vencidos-------------------------------------

    //Dias de tolerancia, fecha actual menos N dias
    $offset = 0;
    $fref  = date("Y-m-d",mktime(0, 0, 0, date('m')  , date('d')-$offset, date('Y')));

    //Buscar polizas activas con pagos vencidos
    $sql = "
    SELECT
    a.pag_polid as pid,
    count(*) as numpagos
    from CRMpagos AS a
    INNER JOIN CRMpolizas AS b ON a.pag_polid=b.pol_id
    where
    a.pag_status='1'
    and a.pag_fini < '$fref'
    group by a.pag_polid
    ";
    $result = mcq_array($sql, $db);

    //Si presentan pagos vencidos, se activa la bandera de vencimiento de la poliza
    foreach($result as $poliza){
	$sql = "
	UPDATE CRMpolizas SET pol_status_venc='1' WHERE pol_id = $poliza[pid]
	";

	$result = mcq($sql, $db);
    }

    //-------------------------------------------------------------------------


    //Verificar polizas marcadas como vencidas---------------------------------

    //Dias de tolerancia, fecha actual menos N dias
    $offset = 0;
    $fref  = date("Y-m-d",mktime(0, 0, 0, date('m')  , date('d')-$offset, date('Y')));

    //Buscar polizas activas con pagos vencidos
    $sql = "
    SELECT
    pol_id as pid
    FROM CRMpolizas
    WHERE pol_status='1' AND pol_status_venc='1'
    ";
    $result = mcq_array($sql, $db);

    //Buscar si presentan pagos vencidos

    foreach($result as $poliza){

	$sql = "SELECT count(*) as num FROM CRMpagos WHERE pag_status='1' AND pag_fini<'$fref' AND pag_polid=$poliza[pid]";
	$result = mcq($sql, $db);
	$data = mysql_fetch_array($result);

	//Si no presentan pagos vencidos, se desactiva la bandera de vencimiento de la poliza
	if ($data['num']==0){
	    $sql = "
	    UPDATE CRMpolizas SET pol_status_venc='0' WHERE pol_id = $poliza[pid]
	    ";
	    $result = mcq($sql, $db);
	}

    }


    //-------------------------------------------------------------------------
    print "Terminado\n";
}
function Send_Dail_Mail_Admin() {
    print "\n\n********************************************************************\n";
    print "Generando notificaciones administrativas\n";
    print "********************************************************************\n\n";
    //obtenemos colores status
    $sql ="".
        "Select varname, color ".
        "from $GLOBALS[TBL_PREFIX]statusvars ".
        "";
    $result = mcq($sql,$db);
    while($row = mysql_fetch_assoc($result)){
        switch ($row['varname']) {
                    case 'Abierto':
                        $color['Abierto']= $row['color'];
                        breaK;
                    case 'En espera':
                        $color['En espera']= $row['color'];
                        breaK;
                    case 'Revision Operativa':
                        $color['opera']= $row['color'];
                        breaK;
                    case 'Revision administrativa':
                        $color['admin']= $row['color'];
                        breaK;
        }
    }
    
    //seleccionar administradores
    $sql ="".
        "Select a.id as id, a.EMAIL as mail, a.FULLNAME as nombre, a.PROFILE as grupo, b.name as name_group ".
        "from $GLOBALS[TBL_PREFIX]loginusers as a, $GLOBALS[TBL_PREFIX]userprofiles as b ".
        "where a.administrator='yes' AND a.RECEIVEDAILYMAIL in ('Yes', '') AND a.EMAIL!='' AND b.id = a.PROFILE";
    //print $sql;
    $result = mcq($sql,$db);
    $i=1;
    while($row = mysql_fetch_assoc($result)){
        //print_array($row);
        //seleccionar al personal del grupo
        $datos[$i][$row['id']]['grupo'] = $row['name_group'];
        $datos[$i][$row['id']]['nombre'] = $row['nombre'];
        $datos[$i][$row['id']]['mail'] = $row['mail'];
        $sql = "".
            "Select id, FULLNAME as responsable ".
            "from $GLOBALS[TBL_PREFIX]loginusers ".
            "where PROFILE=$row[grupo] ";
        $responsables = mcq($sql,$db);
        $j=0;
        while($reg = mysql_fetch_assoc($responsables)){
            $datos[$i][$row['id']]['tareas'][$j]["responsable"] = $reg['responsable'];
            //se obtien la tarea mas antigua del responsable
            $sql = "".
            "SELECT eid, cdate, DATE_FORMAT(tp, \"%Y-%m-%d %H:%i:%s\") as mdate, status FROM CRMentity where assignee= $reg[id] and status in ('Abierto', 'En espera') order by cdate ASC limit 1";
            $r = mcq($sql,$db);
            while($old = mysql_fetch_assoc($r)){
                $hoy = date("Y-m-d H:i:s");
                $diff_mod = round(abs(strtotime($old['mdate'])-strtotime($hoy))/60/60/24, 2);
                $datos[$i][$row['id']]['tareas'][$j]["old_id"]=$old['eid'];
                $datos[$i][$row['id']]['tareas'][$j]["old_create"]=$old['cdate'];
                $datos[$i][$row['id']]['tareas'][$j]["old_age"]=str_replace("Age:", "", GetEntityAge($old['eid']));
                $datos[$i][$row['id']]['tareas'][$j]["old_change"]=$old['mdate'];
                $datos[$i][$row['id']]['tareas'][$j]["old_inactivo"]=$diff_mod." days";
                $datos[$i][$row['id']]['tareas'][$j]["old_status"]=$old['status'];
            }
            //se obtien media de tiempos de respuesta
            $sql = "".
            "SELECT eid, DATE_FORMAT(tp, \"%Y-%m-%d %H:%i:%s\") as mdate FROM CRMentity where assignee= $reg[id] and status in ('Abierto', 'En espera')";
            $ravg = mcq($sql,$db);
            $diff_mod = 0;
            $div=0;
            while($avg = mysql_fetch_assoc($ravg)){
                $diff_mod += round(abs(strtotime($avg['mdate'])-strtotime($hoy))/60/60/24, 2);
                //$avg_age = str_replace("Age:", "", GetEntityAge($avg['eid']));
                //$avg_age = str_replace("d", "", GetEntityAge($avg_age));
                $div++;
            }
            $datos[$i][$row['id']]['tareas'][$j]["avg_inactivo"]= $div > 0 ? number_format(($diff_mod/$div), 2, '.', ',') : 0;
            //$datos[$i][$row['id']]['tareas'][$j]["avg_age"]= ($avg_age/$div);
            //contabilizar tareas por responsable
            $sql = "".
                "Select COUNT(*) as cantidad, status ".
                "from $GLOBALS[TBL_PREFIX]entity ".
                "where assignee=$reg[id] AND status!='Cerrado' group by status";
            $num = mcq($sql,$db);
            while($task = mysql_fetch_assoc($num)){
                switch ($task['status']) {
                    case 'Abierto':
                        $datos[$i][$row['id']]['tareas'][$j]["Abiertas"] = $task['cantidad'];
                        breaK;
                    case 'En espera':
                        $datos[$i][$row['id']]['tareas'][$j]["En espera"] = $task['cantidad'];
                        breaK;
                    case 'Revision administrativa':
                        $datos[$i][$row['id']]['tareas'][$j]["Rev. Administrativa"] = $task['cantidad'];
                        breaK;
                    case 'Revision Operativa':
                        $datos[$i][$row['id']]['tareas'][$j]["Rev. Operativa"] = $task['cantidad'];
                        breaK;
                }
                $datos[$i][$row['id']]['tareas'][$j]["Abiertas"]  = isset($datos[$i][$row['id']]['tareas'][$j]["Abiertas"]) ? $datos[$i][$row['id']]['tareas'][$j]["Abiertas"] : 0 ;
                $datos[$i][$row['id']]['tareas'][$j]["En espera"]  = isset($datos[$i][$row['id']]['tareas'][$j]["En espera"]) ? $datos[$i][$row['id']]['tareas'][$j]["En espera"] : 0 ;
                $datos[$i][$row['id']]['tareas'][$j]["Rev. Administrativa"]  = isset($datos[$i][$row['id']]['tareas'][$j]["Rev. Administrativa"]) ? $datos[$i][$row['id']]['tareas'][$j]["Rev. Administrativa"] : 0 ;
                $datos[$i][$row['id']]['tareas'][$j]["Rev. Operativa"]  = isset($datos[$i][$row['id']]['tareas'][$j]["Rev. Operativa"]) ? $datos[$i][$row['id']]['tareas'][$j]["Rev. Operativa"] : 0 ;
            }
            $j++;
        }
        $i++;
    }
    //print_array($datos);
    $count=0;
    //se genera reporte html
    foreach($datos as $reg){
        //print_array($reg);
        foreach($reg as $info){
            $html = "<table border=1 class='crm' width='1024px'>";
            $html .= "<tr><td align='center' colspan='8' bgcolor='#3b5998'><b style='color: white;'>Resumen de Actividades del Grupo : $info[grupo]</b></td></tr>";
            $html .= "
                    <tr>
                        <td align='center'><font face='Tahoma' size='-1'><b>" . "Responsable" . "</b></font>
                        <td align='center' bgcolor='" . $color['Abierto'] . "'><font face='Tahoma' size='-1'><b>" . "Abiertas" . "</b></font>
                        <td align='center' bgcolor='" . $color['En espera'] . "'><font face='Tahoma' size='-1'><b>" . "En espera" . "</b></font>
                        <td align='center' bgcolor='" . $color['opera'] . "'><font face='Tahoma' size='-1'><b>" . "Rev. Operativa" . "</b></b></font>
                        <td align='center' bgcolor='" . $color['admin'] . "'><font face='Tahoma' size='-1'><b>" . "Rev. Administrativa" . "</b></font></td>
                    </tr>";
            foreach($info['tareas'] as $r1){
                $html .= "
                    <tr>
                        <td><font face='Tahoma' size='-1'>" . $r1['responsable'] . "</font></td>
                        <td align='center'><font face='Tahoma' size='-1'>" . $r1['Abiertas'] . "</font></td>
                        <td align='center'><font face='Tahoma' size='-1'>" . $r1['En espera'] . "</font></td>
                        <td align='center'><font face='Tahoma' size='-1'>" . $r1['Rev. Operativa'] . "</font></td>
                        <td align='center'><font face='Tahoma' size='-1'>" . $r1['Rev. Administrativa'] . "</font></td>
                    </tr>
                    ";
                }
            $html .= "</table></html><br><br>";
            $html .= "<table border=1 class='crm' width='1024px'>";
            $html .= "<tr><td align='center' colspan='8' bgcolor='#3b5998'><b style='color: white;'>Resumen de Actividades mas Antiguas del Grupo : $info[grupo]</b></td></tr>";
            $html .= "
                    <tr>
                        <td align='center'><font face='Tahoma' size='-1'><b>" . "Responsable" . "</b></font>
                        <td align='center'><font face='Tahoma' size='-1'><b>" . "Ticket" . "</b></font>
                        <td align='center'><font face='Tahoma' size='-1'><b>" . "Fecha de Creacion" . "</b></font>
                        <td align='center'><font face='Tahoma' size='-1'><b>" . "Duracion" . "</b></b></font>
                        <td align='center'><font face='Tahoma' size='-1'><b>" . "Fecha de Modificacion" . "</b></font></td>
                        <td align='center'><font face='Tahoma' size='-1'><b>" . "Dias de Inactividad" . "</b></font></td>
                    </tr>";
            foreach($info['tareas'] as $r1){
                $html .= "
                    <tr>
                        <td><font face='Tahoma' size='-1'>" . $r1['responsable'] . "</font></td>
                        <td align='center' bgcolor='" . $color[$r1['old_status']] . "'><font face='Tahoma' size='-1'>" . $r1['old_id'] . "</font></td>
                        <td align='center' bgcolor='" . $color[$r1['old_status']] . "'><font face='Tahoma' size='-1'>" . $r1['old_create'] . "</font></td>
                        <td align='center' bgcolor='" . $color[$r1['old_status']] . "'><font face='Tahoma' size='-1'>" . $r1['old_age'] . "</font></td>
                        <td align='center' bgcolor='" . $color[$r1['old_status']] . "'><font face='Tahoma' size='-1'>" . $r1['old_change'] . "</font></td>
                        <td align='center' bgcolor='" . $color[$r1['old_status']] . "'><font face='Tahoma' size='-1'>" . $r1['old_inactivo'] . "</font></td>
                    </tr>
                    ";
                }
            $html .= "</table></html>";
            $html .= "</table></html><br><br>";
            $html .= "<table border=1 class='crm' width='1024px'>";
            $html .= "<tr><td align='center' colspan='8' bgcolor='#3b5998'><b style='color: white;'>Promedio en los Tiempos de respuesta de las Actividades del Grupo : $info[grupo]</b></td></tr>";
            $html .= "
                    <tr>
                        <td align='center'><font face='Tahoma' size='-1'><b>" . "Responsable" . "</b></font>
                        <td align='center'><font face='Tahoma' size='-1'><b>" . "Promedio Inactividad" . "</b></font></td>
                    </tr>";
            foreach($info['tareas'] as $r1){
                $html .= "
                    <tr>
                        <td><font face='Tahoma' size='-1'>" . $r1['responsable'] . "</font></td>
                        <td align='center'><font face='Tahoma' size='-1'>" . $r1['avg_inactivo'] . "</font></td>
                    </tr>
                    ";
                }
                $html .= "</table></html>";
            //echo $html;
                $count++;
            
        $mail = new PHPMailer();
        $mail->From = $GLOBALS['admemail'];
        $mail->FromName = "CRM Notification Manager";

        $mail->Host     = $GLOBALS['SMTPserver'];
        $mail->Mailer   = $GLOBALS['MailMethod'];
        
        if ($GLOBALS['MailUser'] <> "" && $GLOBALS['MailPass'] <> "") {
                $mail->Username = $GLOBALS['MailUser'];
                $mail->Password = $GLOBALS['MailPass'];
        }
        $mail->IsHTML(true);

        $plainhtml2b = $html;
        $html = "<html><head>". DisplayCSS(true) . "</head><body>" . $html . "</body></html>";

        $html = ParseTemplateGeneric($html);

        $mail->Body    = stripslashes($html);
        $text_MailBody = ereg_replace("<([^>]+)>", "", (eregi_replace("<br>","\015\012",$plainhtml2b)));
        $mail->AltBody = stripslashes($text_MailBody);

        $mail->AddAddress($info['mail'],$info['nombre']);

        $mail->Subject = "Resumen de Actividades de $info[grupo]";
                
        if (!trim($info['mail']=="") && $count>0) {
	print "Enviado resumen de actividades de grupo a $info[nombre] - $info[mail]\n";
        qlog("Sending daily entity overview to " . $info['nombre'] . " (" . $info['mail'] . ") - $count\n");
                //print "Sending entity overview to " . $info['nombre'] . " ($count entities)\n";
                if(!$mail->Send()) {
                        echo "<font color='#FF0000'>There has been a mail error sending to ". $info['mail'] . ":" . $mail->ErrorInfo . ". [Error en el envio de resumen de actividades del grupo].</font><br>";
                        $add_to_journal .= "\nSending e-mail to $info[nombre] failed:" . $mail->ErrorInfo;
                        qlog("E-mail NOT sent.. ERROR: " . $mail->ErrorInfo);
                } else {
                        $add_to_journal .= "\nNotification e-mail sent to $info[nombre]";
                }
        }
        $mail->ClearAddresses();
        $mail->ClearAttachments();
        }
    }
}
?>

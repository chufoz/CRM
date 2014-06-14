<?php
/* * *******************************************************************
 * CRM 
 * Copyright (c) 2001-2003 hidde@it-combine.com
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * Handles new entity forms (e=_new_) and the edit of existing entities (e=[entity_nr])
 *
 * Check http://www.crm-ctt.com/ for more information
 * *********************************************************************
 */
session_name('crm');
if (!isset ($_COOKIE[ini_get('session.name')])) {
    session_start();
}
/*echo "<pre>";
print_r($_POST);
echo "</pre>";*/

extract($_REQUEST);
$tmp_stash = $e;  // stash the entity number!

$GLOBALS['CURFUNC'] = "Edit::";

if ($NoMenu) {
    $nonavbar = 1;
}

if ($_REQUEST['action'] == 'add') {
    $tab = "22";
    $e = "_new_";
    $_REQUEST['e'] = "_new_";
    $_REQUEST['eid'] = "_new_";
}
if ($e == "_new_") {
    $tab = "0";
}

if ($ActivityGraph) {
    include("config.inc.php");
    include("getset.php");
    DisplayEntityActivityGraph($ActivityGraph);
    exit;
}
if ($ActivityGraph2) {
    include("config.inc.php");
    include("getset.php");
    DisplayEntityActivityGraph2($ActivityGraph2);
    exit;
}


if ($_REQUEST['journal']) {  // Show journal of a certain entity
    include("config.inc.php");
    include("getset.php");
    include("language.php");
    DisplayCSS();
    ShowJournal($_REQUEST['eid']);
    EndHTML();
    exit;
} else {
    include("header.inc.php");
}

if ($_REQUEST['saveasnew']) {
    $action = 'add';
    $_REQUEST['action'] = "add";
    $e = "_new_";
    $eid = "_new_";
    $_REQUEST['e'] = "_new_";
    $_REQUEST['eid'] = "_new_";
    $tmp_stash = "_new_";
    qlog("This is a SAVE AS NEW request!");
}

if ($_REQUEST['MassUpdateButton']) {

    $GLOBALS['CURFUNC'] = "MassUpdate::";

    $action = $_REQUEST['SelectedAction'];
    $entities = $_REQUEST['AlterObjectProperty'];

    if (substr($action, 0, 2) == "s_") {
        $status_to = str_replace("s_", "", $action);
        $x = GetStatusses();
        print "Status to " . $status_to . " which is " . $x[$status_to] . "<br>";
        $GLOBALS['CURFUNC'] = "MassUpdate::";
        foreach ($entities AS $entity) {
            if (CheckEntityAccess($entity) == "ok") {
                mcq("UPDATE " . $GLOBALS['TBL_PREFIX'] . "entity SET status='" . $x[$status_to] . "' WHERE eid=" . $entity, $db);
                ExpireFormCache($entity);
                journal($entity, "Status set to " . $x[$status_to] . " by MassUpdate");
                ProcessTriggers("status", $entity, $x[$status_to]);
                $GLOBALS['CURFUNC'] = "MassUpdate::";
                qlog("Eid " . $entity . " status set to " . $x[$status_to] . " by MassUpdate");
            } else {
                qlog("Access to entity " . $entity . " was denied!");
            }
        }
    } elseif (substr($action, 0, 2) == "p_") {
        $prio_to = str_replace("p_", "", $action);
        $x = GetPriorities();
        print "Priority to " . $prio_to . " which is " . $x[$prio_to] . "<br>";
        $GLOBALS['CURFUNC'] = "MassUpdate::";
        foreach ($entities AS $entity) {
            if (CheckEntityAccess($entity) == "ok") {
                mcq("UPDATE " . $GLOBALS['TBL_PREFIX'] . "entity SET priority='" . $x[$prio_to] . "' WHERE eid=" . $entity, $db);
                $GLOBALS['CURFUNC'] = "MassUpdate::";
                ExpireFormCache($entity);
                journal($entity, "Priority set to " . $x[$prio_to] . " by MassUpdate");
                qlog("Eid " . $entity . " priority set to " . $x[$prio_to] . " by MassUpdate");
                ProcessTriggers("priority", $entity, $x[$prio_to]);
            } else {
                qlog("Access to entity " . $entity . " was denied!");
            }
        }
    } elseif ($action == "del") {
        $GLOBALS['CURFUNC'] = "MassUpdate::";
        // delete entities
        foreach ($entities AS $entity) {
            if (CheckEntityAccess($entity) == "ok") {
                DeleteEntity($entity);
                journal($entity, "Entity deleted using MassUpdate");
                ProcessTriggers("status", $entity, $x[$prio_to]);
                $GLOBALS['CURFUNC'] = "MassUpdate::";
                ExpireFormCache($entity);
                qlog("Eid " . $entity . " deleted by MassUpdate");
            } else {
                qlog("Access to entity " . $entity . " was denied!");
            }
        }
    } elseif ($action == "undel") {
        $GLOBALS['CURFUNC'] = "MassUpdate::";
        // delete entities
        foreach ($entities AS $entity) {
            if (CheckEntityAccess($entity) == "ok") {
                UnDeleteEntity($entity);
                journal($entity, "Entity un-deleted using MassUpdate");
                ExpireFormCache($entity);
                ProcessTriggers("status", $entity, $x[$prio_to]);
                $GLOBALS['CURFUNC'] = "MassUpdate::";
                qlog("Eid " . $entity . " un-deleted by MassUpdate");
            } else {
                qlog("Access to entity " . $entity . " was denied!");
            }
        }
    }
    // In this case the last update window was accessed from the main list,
    // after saving, go back to the list.
    if (strstr($fromlisturl, "____STASH-")) {
        $url_to_go_to = PopStashValue(ereg_replace("____STASH-", "", $fromlisturl));
    } elseif (strstr($fromlisturl, "____b64-")) {
        $fromlisturl = ereg_replace("____b64-", "", $fromlisturl);
        $url_to_go_to = base64_decode($fromlisturl);
    } else {
        $url_to_go_to = "index.php?ShowEntitiesOpen=1&tab=99";
    }

    qlog("Redirecting this user!");
    ?>
    <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
        <!--
        document.location='<? echo $url_to_go_to; ?>';
        //-->
    </SCRIPT>
    <?php
    $GLOBALS['CURFUNC'] = "Edit::";
}

if ($_REQUEST['entity'] && $_REQUEST['newform'] && is_administrator()) {
    if ($_REQUEST['newform'] == "default") {
        $_REQUEST['newform'] = 0;
    }
    qlog("Administrative form change of entity " . $_REQUEST['entity'] . " requested");
    mcq("UPDATE " . $GLOBALS['TBL_PREFIX'] . "entity SET formid = " . $_REQUEST['newform'] . " WHERE eid=" . $_REQUEST['entity'], $db);
    ExpireFormCache($_REQUEST['entity']);
    log_msg("Form change of entity " . $_REQUEST['entity'] . " to " . $_REQUEST['newform'] . " by administrator");
    ?>
    <SCRIPT LANGUAGE="JavaScript">
        <!--
        document.location="edit.php?e=<? echo $_REQUEST['entity']; ?>";
        //-->
    </SCRIPT>
    <?php
    EndHTML();
    exit;
}

if ($_REQUEST['e']) {
    $e = $_REQUEST['e'];
    $eid = $_REQUEST['e'];
    qlog("EID is (e) " . $_REQUEST['e']);
} elseif ($_REQUEST['eid']) {
    $e = $_REQUEST['eid'];
    $eid = $_REQUEST['eid'];
    $_REQUEST['e'] = $_REQUEST['eid'];
    qlog("EID Corrected!");
}

if ($_REQUEST['saveasnew']) {
    qlog("Entity $eid used as template for new entity :: $action");
    $action = 'add';
    $e = "_new_";
}

if ($tmp_stash == "_new_" || $e == "_new_") {
    CheckPageAccess("add");
}

if (CheckEntityAccess($tmp_stash) == "nok") {
    //print "<img src='error.gif'>&nbsp;Private entity"; 
    printAD("Access to this page is denied. (edit:tmp_stash:nok)");
    EndHTML();
} else {
    
    if (CheckEntityAccess($tmp_stash) == "readonly") {
        $roins = "DISABLED";
        $readonly = 1;
        $nolocking = true;
    }
    $e = $tmp_stash;

    $sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]loginusers";
    $result = mcq($sql, $db);
    $maxU1 = mysql_fetch_array($result);
    $maxU = $maxU1[0];

    $sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity";
    $result = mcq($sql, $db);
    $maxU1 = mysql_fetch_array($result);
    $maxE = $maxU1[0];

    $sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]customer WHERE active='yes'";
    $result = mcq($sql, $db);
    $maxU1 = mysql_fetch_array($result);
    $maxC = $maxU1[0];

    if ($maxU == 0) {
        $foutje = 1;
    }
    if ($maxC == 0) {
        $foutje = $foutje + 2;
    }
    //require("class.phpmailer.php");
    if ($foutje) {
        print "<table width='40%'><tr><td valign=top><img src='error.gif'>&nbsp;&nbsp;&nbsp;</td><td>";
        if ($foutje == 1) {
            print "$lang[nousers1]";
        } elseif ($foutje == 2) {
            print "$lang[nocustomers1]";
        } elseif ($foutje == 3) {
            print "$lang[nousersandnocustomers1]";
        }
        print "</td></tr></table></body></html>";
        exit;
    } // end if foutje
    ?>
    <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
        <!--
        function AlertUser(whichLayer) {
            //document.bgColor = '#FFE6D1';
            if (document.getElementById) {
                // this is the way the standards work
                document.getElementById(whichLayer).style.display = "block";
            }
            else if (document.all) {
                // this is the way old msie versions work
                document.all[whichlayer].style.display = "block";
            }
            else if (document.layers) {
                // this is the way nn4 works
                document.layers[whichLayer].display = "block";
            }
            document.EditEntity.changed.value = '1';

        }
        function leave() {
            if (document.EditEntity.changed.value == '1') {
                if (confirm('<? echo $lang[save]; ?>?')) {
                    CheckForm();
                }
            }
            //window.open('lwin_exit.html','','toolbar=no,menubar=no,location=no,height=150,width=250');
        }
        
        //-->
    </SCRIPT>

    <?php
    // #DDDDC8 #FFE9D2

    print "<table>";
    print "<tr><td>";
    //<editor-fold defaultstate="collapsed" desc="DUEDATE">
    if ($duedate) {
        $sqldate1 = explode("-", $duedate);
        $sqldate = "$sqldate1[2]-$sqldate1[1]-$sqldate1[0]";

        if (IsDate($duedate) == false) {
            ?>
            <script LANGUAGE="JavaScript" type="text/javascript">
                <!--
                alert("<? echo $lang[dateinvalid]; ?>");
                -->
            </script>
            <?php
            $duedate = "";
            $sqldate = "";
        }
 
        $duedate = explode("-", $duedate);

        // Add zero's ie. 2-7-2003 must become 02-07-2003

        if (strlen($duedate[0]) == 1) {
            $duedate[0] = "0" . $duedate[0];
        }
        if (strlen($duedate[1]) == 1) {
            $duedate[1] = "0" . $duedate[1];
        }

        $duedate = $duedate[0] . "-" . $duedate[1] . "-" . $duedate[2];
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="SQLDATE">
    else {
        // 
        // The following tric is very nasty... for sorting purposes the sqldate is always filled
        // in this case with a date in the far future.
        //
		// This sqldate value will *never* in any case be shown to the user
        //
    $sqldate = "3003-01-01";
    }
    //</editor-fold>
    // Non-entered values mean no in this case:

    $readby_owner = $_REQUEST['ownerNEW'] != $GLOBALS['USERID'] ? 'no' : 'yes';
    $readby_assignee = $_REQUEST['assigneeNEW'] != $GLOBALS['USERID'] ? 'no' : 'yes';

    if (array_key_exists('deleted_posted', $_REQUEST)) {
        if ($_REQUEST['deleted'] == "") {
            $deleted = 'n';
            $_REQUEST['deleted'] = "n";
        }
    }
    if (array_key_exists('obsolete_posted', $_REQUEST)) {
        if ($_REQUEST['obsolete'] == "") {
            $obsolete = 'n';
            $_REQUEST['obsolete'] = "n";
        }
    }
    if (array_key_exists('waiting_posted', $_REQUEST)) {
        if ($_REQUEST['waiting'] == "") {
            $waiting = 'n';
            $_REQUEST['waiting'] = "n";
        }
    }
    if (array_key_exists('readonly_posted', $_REQUEST)) {
        if ($_REQUEST['readonly'] == "") {
            $readonly = 'n';
            $_REQUEST['readonly'] = "n";
        }
    }
    if (array_key_exists('private_posted', $_REQUEST)) {
        if ($_REQUEST['private'] == "") {
            $private = 'n';
            $_REQUEST['private'] = "n";
        }
    }
    if (array_key_exists('notify_owner_posted', $_REQUEST)) {
        if ($_REQUEST['notify_owner'] == "") {
            $notify_owner = 'n';
            $_REQUEST['notify_owner'] = "n";
        } else {
            $notify_owner = 'y';
            $_REQUEST['notify_owner'] = "y";
        }
    }
    if (array_key_exists('notify_assignee_posted', $_REQUEST)) {
        if ($notify_assignee == "") {
            $notify_assignee = 'n';
            $_REQUEST['notify_assignee'] = "n";
        } else {
            $notify_assignee = 'y';
            $_REQUEST['notify_assignee'] = "y";
        }
    }    
    //<editor-fold defaultstate="collapsed" desc="ADD-COMMENT ACTIVITY">
    
    if ($_POST['btn_detentity'] || $_REQUEST['comment'] <> "") {
        
            $sql = "
	    INSERT INTO $GLOBALS[TBL_PREFIX]entitydetail (ed_eid,ed_uid,ed_date,ed_comment,readby_owner,readby_assignee)
	    VALUES($eid," . $_POST['uid'] . ",'" . date("Y-m-d H:i:s") . "','" . $_POST['comment'] . "','$readby_owner','$readby_assignee')";
            mcq($sql, $db);
            $EmailTo = verifiySendMail($GLOBALS['USERID'], $_REQUEST['ownerNEW'], $_REQUEST['assigneeNEW'], $EmailTo);
        
        //$EmailTo="ownerassignee";
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="EDIT ACTIVITY">
    if ($_REQUEST['action'] == 'edit') {
        //<editor-fold defaultstate="collapsed" desc="UPDATE TAGS">
        updateTags($_POST['tags'], $eid, $db);
        //</editor-fold>
        // Check if a lock exists
        $tmp = CheckLock($eid);
        if ($tmp) {
            qlog("WARNING: Somehow this user (" . GetUserName($GLOBALS['USERID']) . ") tried to save an entity which was locked ($eid).");
            log_msg("WARNING: Somehow this user (" . GetUserName($GLOBALS['USERID']) . ") tried to save an entity which was locked. ($eid)", "");
            print $tmp;
            EndHTML();
            exit;
        }

        // Catch a half-updated inserted entity 

        if ($ownerNEW == "2147483647" && $assigneeNEW <> "2147483647") {
            $ownerNEW = $assigneeNEW;
        }
        if ($assigneeNEW == "2147483647" && $ownerNEW <> "2147483647") {
            $assigneeNEW = $ownerNEW;
        }

        // Update SQL
        //	$cat = ereg_replace("'","\"",$cat);
        //$content = ereg_replace("'","\'",$content);
        //	qlog("Cont: $content");
        $content = mres($_REQUEST['content']);
        $cat = mres($_REQUEST['cat']);
        //	qlog("Cont: $content 0000000000000000000000000000000000000000000000000000000000000000000" );
        //	qlog("magic_quotes_gpc: " . ini_get(magic_quotes_gpc));
        // Select old values
        $sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='$eid'";
        $result = mcq($sql, $db);
        $old_values = mysql_fetch_array($result);

        if ((GetClearanceLevel($GLOBALS['USERID']) == "full-access-own-entities") && ($old_values['assignee'] <> $GLOBALS['USERID'])) {
            //print "<img src='error.gif'>&nbsp;Access denied.";
            printAD("223");
            uselogger("WARNING: User " . GetUserName($GLOBALS['USERID']) . " tried to POST data to an entity (" . $e . ") to which he/she has no access!", "");
            qlog("User " . GetUserName($GLOBALS['USERID']) . " tried to POST data to an entity (" . $e . ") to which he/she has no access!");
            EndHTML();
            exit;
        }

        if ($ownerNEW == "") {
            $ownerNEW = $old_values['owner'];
            qlog("ERROR - No owner received - reset to old value");
        }

        if ($assigneeNEW == "") {
            $assigneeNEW = $old_values['assignee'];
            qlog("ERROR - No assignee received - reset to old value");
        }

        // TRIGGER - see if the owner or the assignee wants e-mails of all updates (must be done BEFORE the update!)
//		if (EmailTriggerForOwnerSet($eid)==true || GlobalEmailTriggerForOwnerSet($eid)==true) {
//			$in_line = true;
//			SendEmail($e, "owner", "changed", "<pre>" . $add_to_journal . "<pre>", "", $YourName, $YourAddress);
//		} 
//		if (EmailTriggerForAssigneeSet($eid)==true || GlobalEmailTriggerForAssigneeSet($eid)==true) { 
//			$in_line = true;
//			SendEmail($e, "assignee", "changed", "<pre>" . $add_to_journal . "</pre>", "", $YourName, $YourAddress);
//		}

        if ($ownerNEW <> "" && $assigneeNEW <> "") {
            $int = " assignee='$assigneeNEW',owner='$ownerNEW',deleted='$deleted', ";
        }
        
        //Edit Reminders
        if($_REQUEST['displayr']!=""){//si el recordatorio no esta vacio
            if(datecheck($_REQUEST['displayr'])===false){//verificamos la fecha
                echo "<img src='error.gif'><font color='#990000' size=1.5>La fecha  del recordatorio no es correcta intenta con dd-mm-yyyy</font>";
            }
            else{//en caso de que la fecha sea correcta
                //checar fecha de recordatorio
                //actualiza @reminderdate en bd al hacer POST con nuevo valor en el edit del form o inserta el recordatorio en caso de no existir
                $sqlre="Select reminderdate from $GLOBALS[TBL_PREFIX]reminders where eid='".$eid."' and createdby='".$user_id."' "  ;
                $consulre=mcq($sqlre,$db);
                if(mysql_num_rows($consulre)>0){
                    $rowre = mysql_fetch_array($consulre);   
                    if($rowre['reminderdate']!=$_REQUEST['displayr']){  
                      $day = substr($_REQUEST['displayr'],0,2);
                      $mon = substr($_REQUEST['displayr'],3,2);
                      $yer = substr($_REQUEST['displayr'],6,10);                                               
                      $NewDate = $yer . "-" . $mon . "-" . $day;
                      $times = strtotime($NewDate );
                      $reminders=date("d-m-Y",$times);
                      $sqlrem = "UPDATE $GLOBALS[TBL_PREFIX]reminders SET reminderdate='".trim($reminders)."' WHERE eid='$eid' and createdby='$user_id' ";
                      mcq($sqlrem, $db);
                    }  
                }else{
                    $day = substr($_REQUEST['displayr'],0,2);
                    $mon = substr($_REQUEST['displayr'],3,2);
                    $yer = substr($_REQUEST['displayr'],6,10);                                               
                    $NewDate = $yer . "-" . $mon . "-" . $day;
                    $times = strtotime($NewDate );
                    $hoy=  strtotime(Date('d-m-Y'));
                    $reminders=date("d-m-Y",$times);
                    $time=($times-$hoy)/(60*60*24);
                    if($time>=1){
                        $sqlreminder="Insert Into $GLOBALS[TBL_PREFIX]reminders(eid,reminderdate,createdby) Values(' ".$old_values[eid]."','" .trim($_REQUEST['displayr'])."','".$user_id."')";
                        mcq($sqlreminder, $db);
                   }else{
                       echo "<img src='error.gif'><font color='#990000' size=1>No Puedo Recordar dias Pasados..</font>";
                   }
                }   
            }//si el usuario borra la fecha del recordatorio 
        }
        else{
           $deleterem="Delete From  CRMreminders where eid='".$eid."' And createdby='".$user_id."' ";
            //mcq($deleterem,$db);
        }
        //--------------------------------------------Alerts Update--------------------------------------------------
        $select="Select * From CRMalerts where eid='$eid' and status != '0'";
        $result=  mcq($select, $db);
          if(mysql_num_rows($result)>0){     
            $doalert=mysql_fetch_array($result);
            //if($doalert['status']!='0'){
               if($doalert['severidad']!=$_REQUEST['upalert']){
		    $readby_owner = $_REQUEST['onwerNEW'] != $_POST['uid'] ? 'no' : 'yes';
		    $readby_assignee = $_REQUEST['assigneeNEW'] != $_POST['uid'] ? 'no' : 'yes';

                    if($_REQUEST['upalert']=='1'){
                          $updateAlert= "UPDATE $GLOBALS[TBL_PREFIX]alerts SET severidad='1' WHERE eid='$eid' and status='1' ";
                          mcq($updateAlert, $db);
                          $insertdetail="Insert into $GLOBALS[TBL_PREFIX]entitydetail(ed_eid,ed_uid,ed_date,ed_comment,readby_owner,readby_assignee) Values('".$eid."','".$user_id."','".Date('Y-m-d h:i:s')."','Cambio la Severidad en Pendiente Administrativo de: Media a Alta ','$readby_owner','$readby_assignee')";
                          mcq($insertdetail, $db);
                         // $EmailTo = verifiySendMail($GLOBALS['USERID'], $_REQUEST['ownerNEW'], $_REQUEST['assigneeNEW'], $EmailTo);
                    }elseif($_REQUEST['upalert']=='2'){
			  $updateAlert= "UPDATE $GLOBALS[TBL_PREFIX]alerts SET severidad='2' WHERE eid='$eid' and status='1'";
                          mcq($updateAlert, $db);
			  $insertdetail="Insert into $GLOBALS[TBL_PREFIX]entitydetail(ed_eid,ed_uid,ed_date,ed_comment,readby_owner,readby_assignee) Values('".$eid."','".$user_id."','".Date('Y-m-d h:i:s')."','Cambio la Severidad en Pendiente Administrativo de: Alta a Media ','$readby_owner','$readby_assignee')";
                          mcq($insertdetail, $db);
                           //$EmailTo = verifiySendMail($GLOBALS['USERID'], $_REQUEST['ownerNEW'], $_REQUEST['assigneeNEW'], $EmailTo);
                    }else{
                          $updateAlert= "UPDATE $GLOBALS[TBL_PREFIX]alerts SET status='0' WHERE eid='$eid' and status='1'";
                          mcq($updateAlert, $db);
                          $insertdetail="Insert into $GLOBALS[TBL_PREFIX]entitydetail(ed_eid,ed_uid,ed_date,ed_comment,readby_owner,readby_assignee) Values('".$eid."','".$user_id."','".Date('Y-m-d h:i:s')."','Pendiente Administrativo Cerrado ','$readby_owner','$readby_assignee')";
                          mcq($insertdetail, $db);
                           //$EmailTo = verifiySendMail($GLOBALS['USERID'], $_REQUEST['ownerNEW'], $_REQUEST['assigneeNEW'], $EmailTo);
                    }
             }   
           }else{
            if($doalert['severidad']!=$_REQUEST['alert']){
                if($_REQUEST['alert']=='1'){
                    $severidad='Alta';
                }else{
                    $severidad='Media';
                }
                 $insertalert="Insert Into $GLOBALS[TBL_PREFIX]alerts(eid,iduser,severidad,createdate,status) Values('".$eid."','" .$user_id."','".$_REQUEST['alert']."','".Date('Y-m-d H:i:s')."','1')";
                mcq($insertalert, $db);
                $insertdetail="Insert into $GLOBALS[TBL_PREFIX]entitydetail(ed_eid,ed_uid,ed_date,ed_comment,readby_owner,readby_assignee) Values('".$eid."','".$user_id."','".Date('Y-m-d h:i:s')."','Nuevo Pendiente Administrativo con Severidad $severidad','$readby_owner','$readby_assignee')";
                mcq($insertdetail, $db);
               //$EmailTo = verifiySendMail($GLOBALS['USERID'], $_REQUEST['ownerNEW'], $_REQUEST['assigneeNEW'], $EmailTo);
            }
        }
        // If this entity was deleted in this action, set the close date
        if ($old_values['deleted'] <> $deleted && $deleted == "y") {

            $sql = "SELECT closedate,closeepoch FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='" . $eid . "'";
            $result = mcq($sql, $db);
            $row = mysql_fetch_array($result);

            if ($row['closeepoch'] == "") {
                $closedate = date('Y-m-d');
                $closeepoch = date('U');
                $addon = ",closedate='" . $closedate . "',closeepoch='" . $closeepoch . "'";
            } else {
                qlog("NOT Resetting the close date!");
            }
        }

        $add_to_journal = "Entity values $eid committed to database";

        // Check which fields are actually set
        // Check which variables were passed through (we don't want to delete non-posted fields)
        // CRMcustomer, assigneeNEW and ownerNEW will always be posted!

        if (is_numeric($_REQUEST['parent']) && ($_REQUEST['parent'] <> 0)) {
            if (ValidateParentalRights($_REQUEST['parent'], $eid) == false) {
                print "Not possible to set this entity as child from " . $_REQUEST['parent'];
                uselogger("WARNING: Not possible to set this entity ($eid) as child from " . $_REQUEST['parent'], "");
                $_REQUEST['parent'] = 0;
                $parent = 0;
            }
        }

        $int .= (array_key_exists('status', $_REQUEST)) ? "status='$status'," : "";
        $int .= (array_key_exists('prioty', $_REQUEST)) ? "priority='$prioty'," : "";
        $int .= (array_key_exists('cat', $_REQUEST)) ? "category='$cat'," : "";
        $int .= (array_key_exists('content', $_REQUEST)) ? "content='$content'," : "";
        $int .= (array_key_exists('readonly', $_REQUEST)) ? "readonly='$readonly'," : "";
        $int .= (array_key_exists('notify_owner', $_REQUEST)) ? "notify_owner='$notify_owner'," : "";
        $int .= (array_key_exists('notify_assignee', $_REQUEST)) ? "notify_assignee='$notify_assignee'," : "";
        $int .= (array_key_exists('private', $_REQUEST)) ? "private='$private'," : "";
        $int .= (array_key_exists('duetime', $_REQUEST)) ? "duetime='$duetime'," : "";
        $int .= (array_key_exists('duedate', $_REQUEST)) ? "duedate='$duedate'," : "";
        $int .= (array_key_exists('parent', $_REQUEST)) ? "parent='$parent'," : "";      
        $int .= (array_key_exists('obsolete', $_REQUEST)) ? "obsolete='$obsolete'," : "";
        $int .= (array_key_exists('waiting', $_REQUEST)) ? "waiting='$waiting'," : "";

        //Verifica si se cambio el follower
        $valnewfollow = 0;
        $updatefollowlist = 0;
        $listfollow = $old_values["followers"];
        if (array_key_exists('follow', $_REQUEST)) {
            $valnewfollow = 1;
        }
        if ($_REQUEST['follow_posted'] != $valnewfollow) {
            $updatefollowlist = 1;
            if ($valnewfollow == 1) {
                $listfollow = updateListFollow($GLOBALS["USERID"], $old_values["followers"], 1);
            } else {
                $listfollow = updateListFollow($GLOBALS["USERID"], $old_values["followers"], 0);
            }
        }


        // Update to new values
        $sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET CRMcustomer='$CRMcustomer', $int sqldate='$sqldate' " . $addon . ",lasteditby='" . $user_id . "', assignee='$assigneeNEW',owner='$ownerNEW', followers='$listfollow' WHERE eid='$eid'";

        // Clear build-up form cache for this entity
        ExpireFormCache($eid);

        // For Fabrice, to check if this entity is saved without a customer

        if ($CRMcustomer == "" || $CRMcustomer == "0") {
            uselogger("ERROR. Entity " . $eid . " was saved without a customer!", "");
        }

        // Execute query

        mcq($sql, $db);

        // Clear the access cache tables
        ClearAccessCache($eid, 'e');

        // Now see if there were any extra fields added:
        // First, collect extra fields list

        $list = GetExtraFields();

        // Second, get all extra fields of this entity
        $af = array();
        $allfield_sql = "SELECT eid,value,name FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='$eid' AND type<>'cust'";
        $res = mcq($allfield_sql, $db);
        while ($row = mysql_fetch_array($res)) {
            $af[$row['name']] = $row;
        }


        # >X - Check each possible extra field submitted by the edit form.
        # Insert, update, or delete as appropriate.
        #
        $efield_trigger_varnames = array();
        $efield_trigger_ent_ids = array();
        $efield_trigger_field_vals = array();
        foreach ($list as $extrafield) {
            $efield_id = $extrafield['id'];
            $efield_name = $extrafield['name'];
            $efield_varname = "EFID" . $efield_id;
            $efield_form_value = $$efield_varname;

            //$efield_sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='$eid' AND name='" . $efield_id ."' AND type<>'cust'";
            //$efield_result = mcq ($efield_sql, $db);
            //$efield_row = mysql_fetch_array ($efield_result);
            //$efield_curr_value = $efield_row['value'];

            $efield_curr_value = $af[$efield_id]['value'];

            # debugging output
            # print "efield_curr_value: '" . $efield_curr_value . "'<br>";

            if (is_array($_REQUEST[$efield_varname])) {
                $tmp = array();
                foreach ($_REQUEST[$efield_varname] AS $row) {
                    if ($row <> "") {
                        array_push($tmp, base64_encode($row));
                    }
                }
                $efield_form_value = serialize($tmp);
            }



            # If form has no value and database has no value, then there's no possible update (do nothing).
            # If form has value and database has value and values MATCH, then user has made no change (do nothing).
            # If form has no value and database has value, then user wants to delete current value (delete).
            # If form has value and database has no value, then user wants to set value for first time (insert).
            # If form has value and database has value and values DO NOT match, then user wants to change value (update).


            if ($efield_form_value == $efield_curr_value) {
                qlog("Field " . $efield_varname . " left alone, no change in value.");
                continue;
            } elseif (!array_key_exists($efield_varname, $_REQUEST)) {
                qlog("Ignoring extra field " . $efield_varname . " - it was not posted to the webserver");
                continue;
            } elseif ((!$efield_form_value) && $efield_curr_value) {
                $efield_sql = "DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='" . $eid . "' AND name='" . $efield_id . "' AND type<>'cust'";
                $add_to_journal .= "\n" . $efield_name . " updated from [" . FunkifyLOV($efield_curr_value) . "] to *nothing*";
                qlog("Field " . $efield_varname . " was emptied.");
            } elseif ($efield_curr_value) {
                $efield_sql = "UPDATE $GLOBALS[TBL_PREFIX]customaddons SET value = '" . mres($efield_form_value) . "',usr='" . $name . "' WHERE eid='" . $eid . "' AND name='" . $efield_id . "' AND type<>'cust'";
                $add_to_journal .= "\n" . $efield_name . " updated from [" . FunkifyLOV($efield_curr_value) . "] to [" . FunkifyLOV($efield_form_value) . "]";
                qlog("Field " . $efield_varname . " was updated.");
            } else {
                $efield_sql = "INSERT INTO $GLOBALS[TBL_PREFIX]customaddons(eid, name, value, usr, type) VALUES('" . $eid . "','" . $efield_id . "','" . mres($efield_form_value) . "','" . $name . "','entity')";
                $add_to_journal .= "\n" . $efield_name . " updated from *nothing* to [" . FunkifyLOV($efield_form_value) . "]";
                qlog("Field " . $efield_varname . " was updated.");
            }

            mcq($efield_sql, $db);

            $efield_trigger_varname = "EFID" . $efield_id;
            $efield_trigger_ent_id = $eid;
            $efield_trigger_field_val = $efield_form_value;

            array_push($efield_trigger_varnames, $efield_trigger_varname);
            array_push($efield_trigger_ent_ids, $efield_trigger_ent_id);
            array_push($efield_trigger_field_vals, $efield_trigger_field_val);
        }

        # >X - Process the extra field triggers, if any.
        #
		for ($trigger_count = 0; $efield_trigger_varnames[$trigger_count]; $trigger_count++) {
            $efield_trigger_varname = $efield_trigger_varnames[$trigger_count];
            $efield_trigger_ent_id = $efield_trigger_ent_ids[$trigger_count];
            $efield_trigger_field_val = $efield_trigger_field_vals[$trigger_count];

            ProcessTriggers($efield_trigger_varname, $efield_trigger_ent_id, $efield_trigger_field_val);
        }

        // Triggers for altering an entity
        ProcessTriggers("entity_change", $eid, "");

        if (EmailTriggerForOwnerSet($eid)) {
            ProcessTriggers("entity_change_select_owner", $eid, "");
        }
        if (EmailTriggerForAssigneeSet($eid)) {
            ProcessTriggers("entity_change_select_assignee", $eid, "");
        }

        // Let's see what has changed
        // T R A C K  C H A N G E S  F O R  J O U R N A L -------------------------------------------------------------

        if ($old_values['notify_owner'] <> $_REQUEST['notify_owner']) {
            $add_to_journal .= "\n Owner e-mail notification switched to " . $_REQUEST['notify_owner'];
        }
        if ($old_values['notify_assignee'] <> $_REQUEST['notify_assignee']) {
            $add_to_journal .= "\n Assignee e-mail notification switched to " . $_REQUEST['notify_owner'];
        }

        if ($old_values['assignee'] <> $_REQUEST['assigneeNEW']) {
            $add_to_journal .= "\n" . $lang['assignee'] . " updated from [" . GetUserName($old_values['assignee']) . "] to " . GetUserName($_REQUEST['assigneeNEW']) . "";
            ProcessTriggers("assignee", $eid, $_REQUEST['assigneeNEW']);
            $oldasigned = GetUserName($old_values['assignee']);
            //**********************************************
            $fecha = date("Y-m-d H:i:s");
            $nombre = mysql_result(mcq("SELECT FULLNAME from $GLOBALS[TBL_PREFIX]loginusers WHERE id=$_REQUEST[assigneeNEW]", $db), 0, 0);
            $sql = "INSERT INTO $GLOBALS[TBL_PREFIX]entitydetail (ed_eid,ed_uid,ed_date,ed_comment,readby_owner,readby_assignee) VALUES($eid," . $_REQUEST['uid'] . ",'$fecha','Cambio El Asignado De  $oldasigned A $nombre  ','$readby_owner','$readby_assignee')";
            mcq($sql, $db);
            $EmailTo = verifiySendMail($GLOBALS['USERID'], $_REQUEST['ownerNEW'], $_REQUEST['assigneeNEW'], $EmailTo);
            // $EmailTo="ownerassignee";
            //**********************************************
        }

        if ($old_values['owner'] <> $_REQUEST['ownerNEW']) {
            $add_to_journal .= "\n" . $lang[owner] . " updated from [" . GetUserName($old_values['owner']) . "] to " . GetUserName($_REQUEST['ownerNEW']) . "";
            ProcessTriggers("owner", $eid, $_REQUEST['ownerNEW']);
            $oldowner = GetUserName($old_values['owner']);
            //**********************************************
            $fecha = date("Y-m-d H:i:s");
            $nombre = mysql_result(mcq("SELECT FULLNAME from $GLOBALS[TBL_PREFIX]loginusers WHERE id=$_REQUEST[ownerNEW]", $db), 0, 0);
            $sql = "INSERT INTO $GLOBALS[TBL_PREFIX]entitydetail (ed_eid,ed_uid,ed_date,ed_comment,readby_owner,readby_assignee) VALUES($eid," . $_REQUEST['uid'] . ",'$fecha','Cambio El  Propietario De $oldowner A $nombre','$readby_owner','$readby_assignee')";
            mcq($sql, $db);
            $EmailTo = verifiySendMail($GLOBALS['USERID'], $_REQUEST['ownerNEW'], $_REQUEST['assigneeNEW'], $EmailTo);
            //$EmailTo="ownerassignee";
            //**********************************************
        }

        if ($old_values['category'] <> $_REQUEST['cat']) {
            $add_to_journal .= "\n$lang[category] updated from \"" . $result['category'] . "\" to \"$cat\"";
        }

        if ($old_values['status'] <> $_REQUEST['status']) {
            $add_to_journal .= "\n$lang[status] updated from [$result[status]] to " . $_REQUEST['status'];
            ProcessTriggers("status", $eid, $_REQUEST['status']);
            //*************
            $fstatus = date("Y-m-d H:i:s");

            $sql = "INSERT INTO $GLOBALS[TBL_PREFIX]entitydetail (ed_eid,ed_uid,ed_date,ed_comment,readby_owner,readby_assignee) VALUES($eid," . $_REQUEST['uid'] . ",'$fstatus','Cambio de status de:  $old_values[status]   a: $_REQUEST[status]','$readby_owner','$readby_assignee')";
            mcq($sql, $db);

            if ($_REQUEST['status'] == 'Cerrado') {
                $sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET close_date='$fstatus' WHERE eid=$eid ";
                mcq($sql, $db);
                $EmailTo = verifiySendMail($GLOBALS['USERID'], $_REQUEST['ownerNEW'], $_REQUEST['assigneeNEW'], $EmailTo);
                //$EmailTo="ownerassignee";
            } elseif ($_REQUEST['status'] == 'Revision administrativa') {
                $sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET finish_date='$fstatus' WHERE eid=$eid ";
                mcq($sql, $db);
                $EmailTo = verifiySendMail($GLOBALS['USERID'], $_REQUEST['ownerNEW'], $_REQUEST['assigneeNEW'], $EmailTo);
            // $EmailTo="ownerassignee";
            }
            //*************
        }

        if (($old_values['private'] <> $_REQUEST['private']) && isset($_REQUEST['private']))
            $add_to_journal .= "\nprivate updated from [" . $old_values['private'] . "] to " . $_REQUEST['private'];

        if ($old_values['priority'] <> $_REQUEST['prioty']) {
            $add_to_journal .= "\n$lang[priority] updated from [$old_values[priority]] to " . $_REQUEST['prioty'];
            ProcessTriggers("priority", $eid, $_REQUEST['prioty']);
            //**********************************************
            $fecha = date("Y-m-d H:i:s");
            $nombre = mysql_result(mcq("SELECT FULLNAME from $GLOBALS[TBL_PREFIX]loginusers WHERE id=$_REQUEST[ownerNEW]", $db), 0, 0);
            $sql = "INSERT INTO $GLOBALS[TBL_PREFIX]entitydetail (ed_eid,ed_uid,ed_date,ed_comment,readby_owner,readby_assignee) VALUES($eid," . $_REQUEST['uid'] . ",'$fecha','Cambio de prioridad : $old_values[priority] a: $_REQUEST[prioty]','$readby_owner','$readby_assignee')";
            mcq($sql, $db);
            $EmailTo = verifiySendMail($GLOBALS['USERID'], $_REQUEST['ownerNEW'], $_REQUEST['assigneeNEW'], $EmailTo);
            // $EmailTo="ownerassignee";
            //**********************************************
        }
        if ($old_values['CRMcustomer'] <> $_REQUEST['CRMcustomer']) {

            $add_to_journal .= "\n$lang[customer] updated from [" . GetCustomerName($old_values['CRMcustomer']) . "] to " . GetCustomerName($_REQUEST['CRMcustomer']);

            // for now, the next line will only send a mail to the customer when requested
            ProcessTriggers("customer", $eid, $_REQUEST['CRMcustomer']);

            // TRIGGER
            // The entity is coupled to a new customer which must be logged
            // in the customer journal and maybe someone must get an e-mail

            if (EmailTriggerForCustomerOwnerSet($_REQUEST['CRMcustomer'])) {
                SendEmail($_REQUEST['CRMcustomer'], "customer_owner", "new", $eid, "", "", "");
                journal($_REQUEST['CRMcustomer'], "Entity #" . $eid . " was coupled to this customer\nNotification e-mail send to " . GetUserName($row['customer_owner']), "customer");
            } else {
                journal($_REQUEST['CRMcustomer'], "Entity #" . $eid . " was coupled to this customer", "customer");
            }
        }

        if ($_REQUEST['e_button']) {
            $x = GetButtons($_REQUEST['e_button']);
            if ($x['fieldtype'] == "Button") {
                // So, a button was pressed (and the user has the rights to press it)
                qlog("An extra field button was pressed. Processing triggers.");
                ProcessTriggers("ButtonPress" . $_REQUEST['e_button'], $eid, "");
            }
        }
        if (($old_values['deleted'] <> $_REQUEST['deleted']) && isset($_REQUEST['deleted'])) {
            $add_to_journal .= "\n" . $lang['deleted'] . "updated from " . $old_values['deleted'] . " to " . $_REQUEST['deleted'];
        //ProcessTriggers("deleted",$eid,$status);
        }
        if (($old_values['duedate'] <> $_REQUEST['duedate']) && isset($_REQUEST['duedate']))
            $add_to_journal .= "\n" . $lang['duedate'] . " updated from [" . $old_values['duedate'] . "] to " . $_REQUEST['duedate'];
        if (($old_values['readonly'] <> $_REQUEST['readonly']) && isset($_REQUEST['readonly']))
            $add_to_journal .= "\nRead-only updated from [" . $old_values['readonly'] . "] to " . $_REQUEST['readonly'];
        if (($old_values['waiting'] <> $_REQUEST['waiting']) && isset($_REQUEST['waiting']))
            $add_to_journal .= "\n" . $lang['iswaiting'] . " updated from [" . $old_values['waiting'] . "] to " . $_REQUEST['waiting'];
        if (($old_values['duetime'] <> $_REQUEST['duetime']) && isset($_REQUEST['duetime']))
            $add_to_journal .= "\nDue time updated from [" . $old_values['duetime'] . "] to " . $_REQUEST['duetime'] . "\n";
        if (($old_values['content'] <> $_REQUEST['content']) && isset($_REQUEST['content']))
            $add_to_journal .= "\nContents updated\n";

        //kaveh arkasoft.com make duplicate insert into ejournal

        $sql = "INSERT INTO $GLOBALS[TBL_PREFIX]ejournal(eid,priority,category,content,owner,readonly,assignee,CRMcustomer,status,deleted,duedate,sqldate,obsolete,cdate,waiting,createdby,lasteditby,notify_owner,notify_assignee,private,duetime) VALUES('$eid','$prioty', '$cat', '$content', '$ownerNEW', '$readonly', '$assigneeNEW', '$CRMcustomer','$status','$deleted','$duedate','$sqldate','$obsolete','$cdate','$waiting','" . $user_id . "','" . $user_id . "','" . $notify_owner . "','" . $notify_assignee . "','" . $private . "','" . $duetime . "')";
        mcq($sql, $db);

        uselogger("Entity $eid updated", "");
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="ADD ACTIVITY">
    elseif ($_REQUEST['action'] == 'add') {

        if ($GLOBALS['ONEENTITYPERCUSTOMER'] == "Yes") { // For Natasja
            $cust = $CRMcustomer;
            $row = db_GetRow("SELECT * FROM " . $GLOBALS['TBL_PREFIX'] . "entity WHERE deleted<>'y' AND CRMcustomer='" . $cust . "'", $db);
            if ($row['eid']) {
                qlog("This customer alread has an non-deleted entity and ONEENTITYPERCUSTOMER is enabled. Not adding this.");
                printAD("Double add is not allowed. Close other entity of " . $lang['customer'] . " \"" . GetCustomerName($cust) . "\" first");
                EndHTML();
                exit;
            }
        }

        // Insert a new entity into SQL
     
        $cdate = date('Y-m-d');
        $fecha = date('Y-m-d H:i:s');
        $openepoch = date('U');
        $time = strtotime($_REQUEST['displayr'] );
        $reminder=date("m-d-Y",$time);
//$cat = ereg_replace("'","\"",$cat);
        //$content = ereg_replace("'","\'",$content);
        $content = mres($_REQUEST['content']);
        $cat = mres($_REQUEST['cat']);
        $parent_entity = isset($_REQUEST['parent_entity']) ? $_REQUEST['parent_entity'] : 0;
        $sql = "INSERT INTO $GLOBALS[TBL_PREFIX]entity(priority,category,content,owner,assignee,CRMcustomer,status,deleted,duedate,sqldate,obsolete,cdate,waiting,createdby,lasteditby,readonly,notify_owner,notify_assignee,
        openepoch,private,duetime,formid,start_date, parent) VALUES('". $prioty . "', '". $cat . "', '"
                . $content . "', '". $ownerNEW . "', '". $assigneeNEW . "', '". $CRMcustomer . "','". $status . "','"
                . $GLOBALS['deleted'] . "','" . trim($_REQUEST['displayDate']) . "','". $sqldate . "','". $obsolete . "','". $cdate . "','"
                . $waiting . "','". $user_id . "','" . $user_id . "','". $readonly . "','". $notify_owner . "','". $notify_assignee . "','". $openepoch . "','". $private . "','". $duetime . "','".  $_REQUEST['formid'] ."', NOW(), $parent_entity)";
        
        
        
        $unique = md5($prioty . $cat . $content . $ownerNEW . $assigneeNEW . $CRMcustomer . $status . $duedate . $sqldate . $user_id . $duetime);

        // double-check if this entity is not entered already
        //$unique = md5($sql);
        $query = "SELECT value FROM $GLOBALS[TBL_PREFIX]cache WHERE value='" . base64_encode($unique) . "'";
        $result = mcq($query, $db);
        $row = mysql_fetch_array($result);
        if ($row[0]) {
            qlog("CheckExistence:: The same - " . $unique . " NOT SAVING THIS");
            $query = "SELECT MAX(eid) FROM $GLOBALS[TBL_PREFIX]entity";
            $result = mcq($query, $db);
            $row = mysql_fetch_array($result);
            $e = $row[0];
            $eid = $e;
            $_REQUEST['e'] = $e;
            $_REQUEST['eid'] = $e;
            qlog("CheckExistence:: The same - " . $unique . " NOT SAVING THIS $e to " . $row[0]);
                    
        } else {
            qlog("CheckExistence:: Not the same - " . $unique);
            mcq($sql, $db); //insert new entity
            $consulta = mysql_insert_id(); //get the last eid inserted 
            $query = "SELECT owner,assignee FROM $GLOBALS[TBL_PREFIX]entity where eid='$consulta'";
            $result = mcq($query, $db);
            $row = mysql_fetch_array($result);
            $namefull = GetUserName($row[0]);
            $name2 = GetUserName($row[1]);
            $sqldetail = "INSERT INTO $GLOBALS[TBL_PREFIX]entitydetail (ed_eid,ed_uid,ed_date,ed_comment,readby_owner,readby_assignee)
                                   VALUES('" . $consulta . "','" . $user_id . "','" . $fecha . "','Creacion de Nueva Actividad Como  Propietario:$namefull  y Asignado a: $name2 ','$readby_owner','$readby_assignee')";
            mcq($sqldetail, $db);   //insert comment to first entity whit user creator ,owner and asigned
            //insert Reminders
            if($_REQUEST['displayr']>=Date('d-m-Y')){
            $sqlreminder="Insert Into $GLOBALS[TBL_PREFIX]reminders(eid,reminderdate,createdby) Values('".$consulta."','" .trim($_REQUEST['displayr'])."','".$user_id."')";
            mcq($sqlreminder, $db);  
            
            
            }
            //Insert @alert, al ingresar nueva entidad
                        if($_REQUEST['alert']==1 or $_REQUEST['alert']==2){
                        $sqlalerts="Insert Into $GLOBALS[TBL_PREFIX]alerts(eid,iduser,severidad,createdate,status) Values('".$consulta."','" .$user_id."','".$_REQUEST['alert']."','".Date('Y-m-d h:i:s')."','1')";
                         mcq($sqlalerts, $db);
                        $insertdetail="Insert into $GLOBALS[TBL_PREFIX]entitydetail(ed_eid,ed_uid,ed_date,ed_comment,readby_owner,readby_assignee) Values('".$eid."','".$user_id."','".Date('Y-m-d h:i:s')."','Pendiente Administrativo Cerrado ','$readby_owner','$readby_assignee')";
                         mcq($insertdetail, $db);
                
            }
            $eid = $consulta; // mysql_insert_id();
            $e = $eid;
            $keid = $eid;
            $_REQUEST['e'] = $e;
            $_REQUEST['eid'] = $e;

            if ($GLOBALS['CHECKFORDOUBLEADDS'] == "Yes") { // do not push this value if it is disabled
                PushStashValue($unique);
            } else {
                qlog("Entity double-add check is disabled! (not saving MD5)");
            }

            // Make a customer journal entry 

            journal($CRMcustomer, "Entity " . $eid . " added and coupled to this " . $lang[customer], "customer");

            //With help of kaveh@arkasoft.com; make duplicate entry in ejournal
            $sql = "INSERT INTO $GLOBALS[TBL_PREFIX]ejournal(eid,priority,category,content,owner,assignee,CRMcustomer,status,deleted,duedate,sqldate,obsolete,cdate,waiting,createdby,lasteditby,notify_owner,notify_assignee,formid) VALUES('$eid','$prioty', '$cat', '$content', '$ownerNEW', '$assigneeNEW', '$CRMcustomer','$status','n','$duedate','$sqldate','$obsolete','$cdate','$waiting','" . $user_id . "','" . $user_id . "','" . $notify_owner . "','" . $notify_assignee . "','" . $_REQUEST['formid'] . "')";
            mcq($sql, $db);

            $add_to_journal .= "\nEntity created - " . $$list[$x] . "\n";

            uselogger("Entity $eid added", "");

            print "<b>$lang[entrysaved] $eid</b><br>";

            // Now see if there were any extra fields added:
            # >X - Check each possible extra field submitted by the edit form.
            # Insert, update, or delete as appropriate.
            #
            $list = GetExtraFields();
            $efield_trigger_varnames = array();
            $efield_trigger_ent_ids = array();
            $efield_trigger_field_vals = array();
            foreach ($list as $extrafield) {
                $efield_id = $extrafield['id'];
                $efield_name = $extrafield['name'];
                $efield_varname = "EFID" . $efield_id;
                $efield_form_value = $$efield_varname;

                # debugging output
                # print $efield_id . ", " . $efield_varname . ", " . $efield_form_value . "<br>";

                $efield_sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='$eid' AND name='" . $efield_id . "' AND type<>'cust'";

                # debugging output
                # print "SQL=" . $efield_sql . "<br>";

                $efield_result = mcq($efield_sql, $db);
                $efield_row = mysql_fetch_array($efield_result);
                $efield_curr_value = $efield_row['value'];

                # debugging output
                # print "efield_curr_value: '" . $efield_curr_value . "'<br>";
                # If form has no value and database has no value, then there's no possible update (do nothing).
                # If form has value and database has value and values MATCH, then user has made no change (do nothing).
                # If form has no value and database has value, then user wants to delete current value (delete).
                # If form has value and database has no value, then user wants to set value for first time (insert).
                # If form has value and database has value and values DO NOT match, then user wants to change value (update).
                # debugging output
                # print "I'm here!<br>";

                if ($efield_form_value == $efield_curr_value) {
                    continue;
                } elseif ((!$efield_form_value) AND $efield_curr_value) {
                    # debugging output
                    # print "I'm about to delete the value for extra field # " . $efield_id . "!<br>";
                    if (array_key_exists($efield_varname, $_REQUEST)) {
                        $efield_sql = "DELETE FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='" . $eid . "' AND name='" . $efield_id . "' AND type<>'cust'";
                        $add_to_journal .= "\n" . CleanExtraFieldName($efield_name) . " updated from [$efield_curr_value] to *nothing* (NOT!)";
                    }
                } elseif ($efield_curr_value) {
                    # debugging output
                    # print "I'm here 2!<br>";

                    $efield_sql = "UPDATE $GLOBALS[TBL_PREFIX]customaddons SET value = '" . mres($efield_form_value) . "',usr='" . $name . "' WHERE eid='" . $eid . "' AND name='" . $efield_id . "' AND type<>'cust'";
                    $add_to_journal .= "\n" . CleanExtraFieldName($efield_name) . " updated from [$efield_curr_value] to [" . $efield_form_value . "]";
                } else {
                    # debugging output
                    # print "I'm here 3!<br>";

                    $efield_sql = "INSERT INTO $GLOBALS[TBL_PREFIX]customaddons(eid, name, value, usr, type) VALUES('" . $eid . "','" . $efield_id . "','" . @mres($efield_form_value) . "','" . $name . "','entity')";
                    $add_to_journal .= "\n" . CleanExtraFieldName($efield_name) . " updated from *nothing* to [" . $efield_form_value . "]";
                }

                # debugging output
                # print "SQL=" . $efield_sql . "<br>";

                mcq($efield_sql, $db);

                $efield_trigger_varname = "EFID" . $efield_id;
                $efield_trigger_ent_id = $eid;
                $efield_trigger_field_val = $efield_form_value;

                array_push($efield_trigger_varnames, $efield_trigger_varname);
                array_push($efield_trigger_ent_ids, $efield_trigger_ent_id);
                array_push($efield_trigger_field_vals, $efield_trigger_field_val);
            }

            # debugging output
            # print "efield_trigger_varnames...<br>";
            # print_r ($efield_trigger_varnames);
            # print "<br>";
            # print "efield_trigger_ent_ids...<br>";
            # print_r ($efield_trigger_ent_ids);
            # print "<br>";
            # print "efield_trigger_field_vals...<br>";
            # print_r ($efield_trigger_field_vals);
            # print "<br>";
            # >X - Process the extra field triggers, if any.
            #
            for ($trigger_count = 0; $efield_trigger_varnames[$trigger_count]; $trigger_count++) {
                $efield_trigger_varname = $efield_trigger_varnames[$trigger_count];
                $efield_trigger_ent_id = $efield_trigger_ent_ids[$trigger_count];
                $efield_trigger_field_val = $efield_trigger_field_vals[$trigger_count];

                ProcessTriggers($efield_trigger_varname, $efield_trigger_ent_id, $efield_trigger_field_val);
            }

            ProcessTriggers("entity_add", $eid, "");
            ProcessTriggers("status", $eid, $status);
            ProcessTriggers("priority", $eid, $prioty);
            ProcessTriggers("owner", $eid, $ownerNEW);
            ProcessTriggers("assignee", $eid, $assigneeNEW);
            ProcessTriggers("customer", $eid, $CRMcustomer);

            if ($_REQUEST['e_button']) {
                $x = GetButtons($_REQUEST['e_button']);
                if ($x['fieldtype'] == "Button") {
                    // So, a button was pressed (and the user has the rights to press it)
                    qlog("An extra field button was pressed. Processing triggers.");
                    ProcessTriggers("ButtonPress" . $_REQUEST['e_button'], $eid, "");
                }
            }

            // Check if there is a global e-mail address set to send notifications of new entities to
            //if (stristr($EmailNewEntities,"@")) {
            //SendEmail($eid,"global_new_entity","new", "", "", "", "");
            //}
            // Check if the owner of the customer to which this entity was assigned needs to be informed 

            $sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]customer WHERE id='" . $CRMcustomer . "'";
            $outp = mcq($sql, $db);
            $row = mysql_fetch_array($outp);

            if ($row['email_owner_upon_adds'] == "yes") { // The customer-owner needs to receive an e-mail
                //	SendEmail($eid,$who,$NewOrChanged, $BodyData, $SubjectData, $FromName, $FromAddress) { 
               SendEmail($CRMcustomer, "customer_owner", "new", $eid, "", "", "");
               //ProcessTriggers("entity_add_for_assignee",$eid,"");
            }

            //**********************************************
            //envio forzado de correo a creador y responsable
            //SendEmail($CRMcustomer, "customer_owner", "new", $eid, "", "", "");
            $EmailTo = verifiySendMail($GLOBALS['USERID'], $_REQUEST['ownerNEW'], $_REQUEST['assigneeNEW'], $EmailTo);
            //$EmailTo = verifiySendMail($GLOBALS['USERID'], $_REQUEST['ownerNEW'], $_REQUEST['assigneeNEW'], $EmailTo);
            //$EmailTo="ownerassignee";
            //**********************************************

            $wasnew = 1;
            // Set $e to $eid to print the form again with the new entered values.
            $e = $keid;
            $_REQUEST['e'] = $keid;
            $eid = $keid;

            // First, check if something needs to be done!
        }
        insertTags($_POST['tags'], $eid, $conn_db);//INSERTA TAGS EN DB
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="ATTACH FILE">

    if (!$_FILES['userfile']['tmp_name'] == "" && !$_FILES['userfile']['name'] == "" && !$_FILES['userfile']['size'] == "" && !$_FILES['userfile']['type'] == "") {

        //  A file was attached
        // Read contents of uploaded file into variable

        $fp = fopen($_FILES['userfile']['tmp_name'], "rb");
        $filecontent = fread($fp, filesize($_FILES['userfile']['tmp_name']));
        fclose($fp);
        //$filecontent = addslashes($filecontent);
        // insert identifier & content into $GLOBALS[TBL_PREFIX]binfiles:
        //$sql="INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,filetype,username) VALUES('$e','$filecontent','" . $_FILES['userfile']['name'] . "','" . $_FILES['userfile']['size'] . "','" . $_FILES['userfile']['type'] . "','" . $name . "')";
        //mcq($sql,$db);
        //$attachment = mysql_insert_id();
        //journal($e,"File " . $_FILES['userfile']['name'] . " added");
        //$add_to_journal .= "File " . $_FILES['userfile']['name'] . " added";
        //uselogger("File  " . $_FILES['userfile']['name'] . " added to entity $e","");

        $x = AttachFile($e, $_FILES['userfile']['name'], $filecontent, "entity", $_FILES['userfile']['type']);

        unset($filecontent);
        unset($_FILES['userfile']['tmp_name']);
        unset($_FILES['userfile']['name']);
        unset($_FILES['userfile']['size']);
        unset($_FILES['userfile']['type']);
    } else {
        qlog("No file received!");
    }

    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="SendEmailToPB">
    if ($_GET['SendEmailToPB'] && !$SendEmailToOtherUsers) {
        // Send an e-mail to someone from the phonebook
        if (!$_GET['PBID']) {
            print "<img src='error.gif'>&nbsp;No e-mail address id received. Fatal.";
        } else {
            print "</td></tr></table><table width=100%><tr><td>";

            print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;E-mail to $customer</legend>";
            //  dirty debug
            print "<form name='EditEntity'><input type='hidden' name='changed'><input type='hidden' name='unlock'></form>";

            print "<form name='composeform' method='POST'><table width=100%>";

            if ($_GET['SendEmailToPB'] == 1) {
                $sql = "SELECT Firstname,Lastname,Email FROM $GLOBALS[TBL_PREFIX]phonebook WHERE id='" . $_GET['PBID'] . "'";
                $result = mcq($sql, $db);
                $row = mysql_fetch_array($result);

                $receipient_name = $row['Firstname'] . " " . $row['Lastname'];
                $receipient_mail = $row['Email'];

                qlog("This is an email to someone in the phonebook");
            } elseif ($_GET['SendEmailToPB'] == 2) {
                $sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE id='" . $_GET['PBID'] . "'";
                $result = mcq($sql, $db);
                $row = mysql_fetch_array($result);

                $receipient_name = $row[0];
                $receipient_mail = $row[0];
                qlog("This is an e-mail to an extra field value (" . $row[0] . ")");
                unset($EntityID);
            }
            print "<tr><td><b>To:</b></td><td><b>" . $receipient_name . " (" . $receipient_mail . ")</b></td></tr>";
            print "<input type='hidden' name='SendEmailToOtherUsers' value='1'>";
            print "<input type='hidden' name='ManualEmailAddress' value='" . $receipient_mail . "'>";
            print "<input type='hidden' name='unimp'>";
            print "<input type='hidden' name='SendToMailAddrButton'>";
            //print "<tr><td><b>" . $lang['entity']. ":</b></td><td><b>" . $enti['category'] . " (" . $enti['eid'] . ")</b></td></tr>";
            $sql = "SELECT FULLNAME,EMAIL FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='" . $name . "'";
            $res1 = mcq($sql, $db);
            $tmp = mysql_fetch_array($res1);

            $your_name = $tmp['FULLNAME'];
            $your_email = $tmp['EMAIL'];

            $webhost = getenv("HOSTNAME");

            $sub = ereg_replace('\"', "'", $enti['category']);

            print "<tr><td><b>From:</b></td><td>[CRM] " . $your_name . " (" . $your_email . ")</td></tr>";
            if ($EntityID) {
                print "<tr><td><b>Subject:</b></td><td><input type='text' name='SubjectData' size=75 value='CRM " . $lang['entity'] . " " . $enti[eid] . " [" . $sub . "]'></td></tr>";
            } else {
                print "<tr><td><b>Subject:</b></td><td><input type='text' name='SubjectData' size=75 value=''></td></tr>";
            }
            print "<tr><td colspan=2>";

            print "<input type='hidden' name='SendIt' value='1'>";
            print "<input type='hidden' name='nonavbar' value='1'>";
            print "<input type='hidden' name='FromName' value='[CRM] " . $your_name . "'>";
            print "<input type='hidden' name='FromAddress' value='" . $your_email . "'>";
            print "<input type='hidden' name='SendIDCust' value='1'>";
            print "<input type='hidden' name='ManualMail' value='1'>";
            ?>
            <script language="Javascript1.2"><!-- // load htmlarea
                _editor_url = "";                     // URL to htmlarea files
                var win_ie_ver = parseFloat(navigator.appVersion.split("MSIE")[1]);
                if (navigator.userAgent.indexOf('Mac')        >= 0) { win_ie_ver = 0; }
                if (navigator.userAgent.indexOf('Windows CE') >= 0) { win_ie_ver = 0; }
                if (navigator.userAgent.indexOf('Opera')      >= 0) { win_ie_ver = 0; }
                if (win_ie_ver >= 5.5) {
                    document.write('<scr' + 'ipt src="' +_editor_url+ 'editor.js"');
                    document.write(' language="Javascript1.2"></scr' + 'ipt>');  
                } else { document.write('<scr'+'ipt>function() { return false; }</scr'+'ipt>'); }
                                                        // --></script>
            <?
            print "<TEXTAREA name='data' ROWS='18' COLS='100'>" . $MailBody . "</TEXTAREA>";
            ?>
            <script language="javascript1.2">
            						
                var config = new Object();    // create new config object

                config.width = "100%";
                config.height = "290px";
                config.bodyStyle = 'background-color: white; font-family: "Verdana"; font-size: x-small;';
                config.debug = 0;

                // NOTE:  You can remove any of these blocks and use the default config!

                config.toolbar = [
                    ['fontname'],
                    ['fontsize'],
                    //	['fontstyle'],
                    ['linebreak'],
                    ['bold','italic','underline','separator'],
                    //  ['strikethrough','subscript','superscript','separator'],
                    ['justifyleft','justifycenter','justifyright','separator'],
                    ['OrderedList','UnOrderedList','Outdent','Indent','separator'],
                    ['forecolor','backcolor','separator'],
                    ['HorizontalRule','Createlink','htmlmode'],
                    //	['about','help','popupeditor'],
                ];
                config.fontnames = {
                    "Arial":           "arial, helvetica, sans-serif",
                    "Courier New":     "courier new, courier, mono",
                    "Georgia":         "Georgia, Times New Roman, Times, Serif",
                    "Tahoma":          "Tahoma, Arial, Helvetica, sans-serif",
                    "Times New Roman": "times new roman, times, serif",
                    "Verdana":         "Verdana, Arial, Helvetica, sans-serif",
                    "impact":          "impact",
                    "WingDings":       "WingDings",
                    "Trebuchet MS":	   "Trebuchet MS"
                };
            						
                editor_generate('data',config);
            </script>

            <?php
            print "</td></tr>";
            print "<tr><td colspan=2>";
            if ($EntityID) {
                print "<INPUT TYPE='submit' NAME='Send.x' VALUE='E-mail!'>&nbsp;<input type='button' value='" . $lang['cancel'] . "' OnClick='javascript:window.close()'></td></tr>";
            } else {
                print "<INPUT TYPE='submit' NAME='Send.x' VALUE='E-mail!'>&nbsp;<input type='button' value='" . $lang['cancel'] . "' OnClick='javascript:window.close()'></td></tr>";
            }
            print "</form></table></fieldset>";
            print "</td></tr><tr><td colspan=2><table>";
            print "</table></td></tr></table>";
        } // end if !PBID
        EndHTML();
        exit;
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="SEND EMAIL TO CUSTOMER OR OTHER USERS">
    if ($SendEmailToCustomer || $SendEmailToOtherUsers) {
        if ($SendIt && $data && ($SendID || $SendIDCust || $emailmerge)) {
            print "E-mail sent";
            // Linda
            if ($SendID) {
                $sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='$SendID'";
                $res1 = mcq($sql, $db);
                $enti = mysql_fetch_array($res1);
            } else {
                $enti['CRMcustomer'] = $SendIDCust;
                $SendID = $SendIDCust;
            }

            $customer = GetCustomerName($enti['CRMcustomer']);

            if ($emailmerge) {
                $res = mcq(PopStashValue($emailmerge), $db);
                while ($row = mysql_fetch_array($res)) {
                    $SendToMailAddr .= "," . $row['contact_email'];
                    journal($row['id'], "This customer was included in an e-mail merge:\n$data", "customer");
                }
            }

            if ($_GET['SendEmailToOtherUsers'] || $emailmerge) {
                $recp_list = split(",", $SendToMailAddr);
            }
            if ($ManualMail) {
                $ManualEmailAddress = $_POST['ManualEmailAddress'];
                SendEmail($SendID, "ManualMail", "new", $data, $SubjectData, $FromName, $FromAddress);
                uselogger("Manual e-mail sent from " . $FromName . " to " . $ManualEmailAddress . " containing text: " . $data, "");
            } elseif ($SendIDCust) {
                SendEmail($SendID, "customerdirect", "new", $data, $SubjectData, $FromName, $FromAddress);
            //journal($SendID,"Notification e-mail sent to " . strtolower($lang['customer']) . " " . $customer . "\n\n" . $data, "customer");
            } else {
                SendEmail($SendID, "customer", "new", $data, $SubjectData, $FromName, $FromAddress);
            //journal($SendID,"Notification e-mail sent to " . strtolower($lang['customer']) . " " . $customer,"customer");
            }

            //print "$SendID,customer,new, $data, $SubjectData, $FromName, $FromAddress";
            ?>

            <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
                <!--
                window.close();
                //-->
            </SCRIPT>
            <?php
        //EndHTML();
        } else {

            if (!$EntityID && !$CustID && !$CustomerArray) {
                print "Reference to entity not found";
            } else {

                // Fetch the content of the entity

                if ($EntityID) {
                    $sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='$EntityID'";
                    $res1 = mcq($sql, $db);
                    $enti = mysql_fetch_array($res1);

                    $customer = GetCustomerName($enti['CRMcustomer']);
                    $owner = GetUserName($enti['owner']);
                    $assignee = GetUserName($enti['assignee']);

                    $sql = "SELECT custname,contact_email,contact FROM $GLOBALS[TBL_PREFIX]customer WHERE id='" . $enti['CRMcustomer'] . "'";
                    $res1 = mcq($sql, $db);
                    $tmp = mysql_fetch_array($res1);

                    $receipient_name = $tmp['contact'];
                    $receipient_mail = $tmp['contact_email'];
                } elseif ($CustID) {

                    $sql = "SELECT custname,contact_email,contact FROM $GLOBALS[TBL_PREFIX]customer WHERE id='" . $CustID . "'";
                    $res1 = mcq($sql, $db);
                    $tmp = mysql_fetch_array($res1);

                    $receipient_name = $tmp['contact'];
                    $receipient_mail = $tmp['contact_email'];

                    $EntityID = $CustID;

                    $SendingToCustomer = true;

                    //$SendEmailToOtherUsers=true;
                } elseif ($CustomerArray) {
                    if (!$stashid) {
                        print "Error. There is something seriously wrong. ID7723-1<br>";
                    } else {
                        qlog("This is an e-mail merge");
                        $emailmerge = true;
                        $receipient_name = "e-mail merge";

                        $res1 = mcq(PopStashValue($stashid), $db);
                        while ($row = mysql_fetch_array($res1)) {
                            if (strlen($row['contact_email']) > 5) {
                                $receipient_mail .= "," . $row['contact_email'];
                            } else {
                                uselogger("ERROR - Cannot use this customer (" . $row['custname'] . ") in e-mail merge because e-mail address is not (properly) set", "");
                                qlog("ERROR - Cannot use this customer (" . $row['custname'] . ") in e-mail merge because e-mail address is not (properly) set");
                            }
                        }
                    }
                }

                if ($receipient_mail == "" && !$SendEmailToOtherUsers) {
                    print "<img src='error.gif'>&nbsp;Error - this " . strtolower($lang['customer']) . " has no e-mail address set";
                    print "<br><br><a href='javascript:window.close()' class='bigsort'>" . $lang['cancel'] . "</a>";
                    EndHTML;
                    exit;
                }
                print "</td></tr></table><table width=100%><tr><td>";

                print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;E-mail to $customer</legend>";
                //  dirty debug
                print "<form name='EditEntity'><input type='hidden' name='changed'></form>";

                print "<form name='composeform' method='POST'>";

                if ($emailmerge) {
                    print "<input type='hidden' name='SendEmailToOtherUsers' value='1'>";
                    print "<input type='hidden' name='emailmerge' value='" . $stashid . "'>";
                }

                print "<table width=100%>";
                
                //<editor-fold defaultstate="collapsed" desc="SEND EMAIL TO OTHER USERS">
                if ($_GET['SendEmailToOtherUsers']) {
                    print "<tr><td><b>To:</b></td><td>";
                    print "<select name='unimp'>";
                    $sql = "SELECT FULLNAME,EMAIL from $GLOBALS[TBL_PREFIX]loginusers WHERE LEFT(FULLNAME,3) <> '@@@' AND active<>'no'";
                    $res1 = mcq($sql, $db);
                    while ($users = mysql_fetch_array($res1)) {
                        print "<option value='" . $users['EMAIL'] . "'>" . $users['FULLNAME'] . "</option>";
                    }
                    print "</select>";
                    ?>
                    <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
                        <!--
                        function AddCurAddr() {						
                            a = document.composeform.SendToMailAddr.value;
                            b = document.composeform.unimp.value;
                            if(a==''){
                                c=b;
                            }else{
                                c = a + "," + b;
                            }
                            document.composeform.SendToMailAddr.value = c;
                        }
                        //-->
                    </SCRIPT>
                    <?php
                    print "<input type='button' OnClick='javascript:AddCurAddr();' value='>>>' name='SendToMailAddrButton'>&nbsp;<input type='text' name='SendToMailAddr' size=50>";

                    print "<input type='hidden' name='SendEmailToOtherUsers' value='1'>";
                    print "</td></tr>";
                }
                //</editor-fold>
                //<editor-fold defaultstate="collapsed" desc="SEND EMAIL TO CUSTOMER">
                else {
                    print "<tr><td><b>To:</b></td><td><b>" . $receipient_name . " (" . $receipient_mail . ")</b></td></tr>";
                    print "<input type='hidden' name='SendEmailToCustomer' value='1'>";
                    print "<input type='hidden' name='SendToMailAddr' value='unimportant'>";
                    print "<input type='hidden' name='unimp'>";
                    print "<input type='hidden' name='SendToMailAddrButton'>";
                }
                //</editor-fold>
                print "<tr><td><b>" . $lang['entity'] . ":</b></td><td><b>" . $enti['category'] . " (" . $enti['eid'] . ")</b></td></tr>";

                $sql = "SELECT FULLNAME,EMAIL FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='" . $name . "'";
                $res1 = mcq($sql, $db);
                $tmp = mysql_fetch_array($res1);

                $your_name = $tmp['FULLNAME'];
                $your_email = $tmp['EMAIL'];

                $webhost = getenv("HOSTNAME");

                $sub = ereg_replace('\"', "'", $enti['category']);

                print "<tr><td><b>From:</b></td><td>[CRM] " . $your_name . " (" . $your_email . ")</td></tr>";
                if (!$SendingToCustomer) {
                    print "<tr><td><b>Subject:</b></td><td><input type='text' name='SubjectData' size=75 value='CRM " . $lang['entity'] . " " . $enti[eid] . " [" . $sub . "]'></td></tr>";
                } else {
                    print "<tr><td><b>Subject:</b></td><td><input type='text' name='SubjectData' size=75 value=''></td></tr>";
                }
                print "<tr><td colspan=2>";

                print "<input type='hidden' name='SendIt' value='1'>";
                print "<input type='hidden' name='nonavbar' value='1'>";
                print "<input type='hidden' name='FromName' value='[CRM] " . $your_name . "'>";
                print "<input type='hidden' name='FromAddress' value='" . $your_email . "'>";

                if (!$SendingToCustomer) {
                    print "<input type='hidden' name='SendID' value='" . $enti['eid'] . "'>";
                } else {
                    print "<input type='hidden' name='SendIDCust' value='" . $CustID . "'>";
                }

                if (!$CustID) {
                    $MailBody = $BODY_TEMPLATE_CUSTOMER;
                    $MailBody = ParseTemplateEntity($MailBody, $enti['eid']);
                    $MailBody = ParseTemplateCustomer($MailBody, $enti['CRMcustomer']);
                    $MailBody = ParseTemplateGeneric($MailBody);
                }
                ?>
                <?php
                //print "<input type='hidden' name='data' value='" . $MailBody . "'>";
                include("fckeditor/fckeditor.php");

                $oFCKeditor = new FCKeditor('data');
                $oFCKeditor->BasePath = "fckeditor/";
                //$oFCKeditor->Config['SkinPath'] = 'fckeditor/editor/skins/silver/' ;
                $oFCKeditor->Width = "100%";
                $oFCKeditor->Height = "400";
                $oFCKeditor->ToolbarSet = 'CRMUSER';

                $oFCKeditor->Value = $MailBody;
                $oFCKeditor->Create();
                print "</td></tr>";
                print "<tr><td colspan=2>";
                if ($EntityID) {
                    print "<INPUT TYPE='submit' NAME='Send.x' VALUE='E-mail!'>&nbsp;<input type='button' value='" . $lang['cancel'] . "' OnClick='javascript:window.close()'></td></tr>";
                } else {
                    print "<INPUT TYPE='submit' NAME='Send.x' VALUE='E-mail!'>&nbsp;<input type='button' value='" . $lang['cancel'] . "' OnClick='javascript:window.close()'></td></tr>";
                }
                print "</table></fieldset>";
                print "</td></tr><tr><td colspan=2><table>";

                if ($SendingToCustomer) {
                    $sql = "SELECT filename,creation_date,date_format(creation_date, '%a %M %e, %Y %H:%i') AS dt,filesize,fileid,username FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='$EntityID' AND type='cust' ORDER BY filename,creation_date";
                } else {
                    $sql = "SELECT filename,creation_date,date_format(creation_date, '%a %M %e, %Y %H:%i') AS dt,filesize,fileid,username FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='$EntityID' AND type='entity' ORDER BY filename,creation_date";
                }
                $result = mcq($sql, $db);
                print "<tr><td><img src='attach.gif' style='border:0'></td><td>$lang[filename]</td>";

                print "<td>Size</td></tr>";

                while ($files = mysql_fetch_array($result)) {

                    print "\n<tr><td><input type='checkbox' style='border:0' name='attfile[]' value='" . $files['fileid'] . "'></td><td>" . $files['filename'] . "</td><td>" . $files['filesize'] . "</td></tr>";
                }

                print "</table></td></tr></table>";
            }
            EndHTML();
            exit;
        }
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="EDIT ACTIVITY-UPDATE FOLLOWERS">
    if ($_REQUEST['action'] == 'edit') {
        // Se actualizara la lista de los followers
        if ($listfollow <> "") {
            $list = explode(",", $listfollow);
            if (in_array($GLOBALS["USERID"], $list)) { // Si el usuario esta en la lista
                removeFromArray($list, $GLOBALS["USERID"]);
            }
            if (in_array($_REQUEST['ownerNEW'], $list)) {//Si el propietario esta en la lista
                removeFromArray($list, $_REQUEST['ownerNEW']);
            }
            if (in_array($_REQUEST['assigneeNEW'], $list)) {//Si el responsable esta en la lista
                removeFromArray($list, $_REQUEST['assigneeNEW']);
            }
            $listfollowers = implode(",", $list);

            if ($listfollowers != '') {

                $sql = "SELECT FULLNAME,EMAIL FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='" . $name . "'";
                $res1 = mcq($sql, $db);
                $tmp = mysql_fetch_array($res1);
                $YourName = "[CRM] " . $tmp['FULLNAME'];
                $YourAddress = $tmp['EMAIL'];

                $sql = "SELECT FULLNAME, EMAIL FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id IN ($listfollowers)";
                $res1 = mcq($sql, $db);

                while ($row = mysql_fetch_array($res1)) {
                    $recp_list[] = $row['EMAIL'];
                }
                SendEmail($e, "follower", "changed", "", "", $YourName, $YourAddress);
            }
        }
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="EMAIL TO">
    if ($EmailTo) {

        //function SendEmail($eid,$who,$NewOrChanged, $BodyData [opt], $SubjectData [opt]) 

        $sql = "SELECT FULLNAME,EMAIL FROM $GLOBALS[TBL_PREFIX]loginusers WHERE name='" . $name . "'";
        $res1 = mcq($sql, $db);
        $tmp = mysql_fetch_array($res1);

        $YourName = "[CRM] " . $tmp['FULLNAME'];
        $YourAddress = $tmp['EMAIL'];

        if ($action == "add" ) {
            $NewOrChanged = "new";
        }
        elseif ($action == "edit") {
            $NewOrChanged = "changed";
        }

        if ($EmailTo == "owner") {
            SendEmail($e, "owner", $NewOrChanged, "", "", $YourName, $YourAddress);
        } elseif ($EmailTo == "assignee") {
            SendEmail($e, "assignee", $NewOrChanged, "", "", $YourName, $YourAddress);
        } elseif ($EmailTo == "ownerassignee") {
            SendEmail($e, "owner", $NewOrChanged, "", "", $YourName, $YourAddress);
            SendEmail($e, "assignee", $NewOrChanged, "", "", $YourName, $YourAddress);
        } elseif ($EmailTo == "customer") {
            ?>
            <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
                popEmailToCustomerScreen('<? echo $e; ?>');
            </SCRIPT>
            <?php
        } elseif ($EmailTo == "all") {
            SendEmail($e, "owner", $NewOrChanged, "", "", $YourName, $YourAddress);
            SendEmail($e, "assignee", $NewOrChanged, "", "", $YourName, $YourAddress);
            ?>
            <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
                popEmailToCustomerScreen('<? echo $e; ?>');
            </SCRIPT>
            <?php
        }
    }
    //</editor-fold>
    
    if ($action == 'edit' || $action == 'add')
        journal($e, $add_to_journal);

    if ($fconfirmed) {
        for ($c = 0; $c < sizeof($deletefile); $c++) {
            $sql = "SELECT filename FROM $GLOBALS[TBL_PREFIX]binfiles WHERE fileid='$deletefile[$c]'";
            $result = mcq($sql, $db);
            $filename = mysql_fetch_array($result);
            $filename = $filename[filename];
            $sql = "DELETE FROM $GLOBALS[TBL_PREFIX]binfiles WHERE fileid='$deletefile[$c]'";
            mcq($sql, $db);
            $sql = "DELETE FROM $GLOBALS[TBL_PREFIX]blobs WHERE fileid='$deletefile[$c]'";
            mcq($sql, $db);

            uselogger("File deleted: $deletefile[$c] - $filename", "");
            journal($e, "File $filename (#" . $deletefile[$c] . ") deleted");
        }
        unset($deletefile);
        if ($NoMenu) {
            ?>
            <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
                <!--
                window.close();
                //-->
            </SCRIPT>
            <?php
        }
    }

    if ($deletefile) {
        $a = GetClearanceLevel($GLOBALS[USERID]);
        if ($a == "full-access-own-entities") {
            $sql = "SELECT assignee FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='$eid'";
            $result = mcq($sql, $db);
            $resarr = mysql_fetch_array($result);
            //print "<img src='error.gif'>&nbsp;Access denied";
            printAD("23454");
            EndHTML();
            exit;
        }
        print "<table><tr><td>";
        print "$lang[deleting1] " . sizeof($deletefile) . " $lang[deleting2]<br>";
        print "<form name='confirm' method='POST'>";
        for ($c = 0; $c < sizeof($deletefile); $c++) {
            print "<input type='hidden' name='deletefile[]' value='$deletefile[$c]'>";
        }
        print "<br><input type='hidden' name='fconfirmed' value='1'>";
        print "<input type='submit' name='knopje' value='$lang[confdel]'>";
        print "<pre>";
        for ($r = 0; $r < sizeof($deletefile); $r++) {
            print $lang[delete] . " " . $deletefile[$r] . " - " . GetFileName($deletefile[$r]) . "\n";
        }
        print "</pre>";
        print "<input type='hidden' name='NoMenu' value='$NoMenu'>";
        print "<input type='hidden' name='e' value='$e'>";
        print "</form></td></tr></table>";
        EndHTML();
        exit;
    }
    // Variables to expect are $prio, $cat, $conten, $owner, $assignee, $add, $CRMcustomer, $status
    if ($d) {
        // in case of deletion
        $a = GetClearanceLevel($GLOBALS[USERID]);
        if ($a == "full-access-own-entities") {
            $sql = "SELECT assignee FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='$eid'";
            $result = mcq($sql, $db);
            $resarr = mysql_fetch_array($result);
            printAD("224345");
            EndHTML();
            exit;
        }

        journal($d, "Entity deleted");

        $sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]entity WHERE eid='$d'";
        $result = mcq($sql, $db);
        $ea = mysql_fetch_array($result);

        $sql = "SELECT FULLNAME FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id='$ea[owner]'";
        $result = mcq($sql, $db);
        $owner = mysql_fetch_array($result);
        $owner = $owner[FULLNAME];

        $sql = "SELECT FULLNAME FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id='$ea[assignee]'";
        $result = mcq($sql, $db);
        $assignee = mysql_fetch_array($result);
        $assignee = $assignee[FULLNAME];

        $t = $ea[tp]; // timestamp last edit
        $tp[jaar] = substr($t, 0, 4);
        $tp[maand] = substr($t, 4, 2);
        $tp[dag] = substr($t, 6, 2);
        $tp[uur] = substr($t, 8, 2);
        $tp[min] = substr($t, 10, 2);
        $cdate = explode("-", $ea[cdate]);

        // Add zero's ie. 2-7-2003 must become 02-07-2003

        if (strlen($cdate[0]) == 1) {
            $cdate[0] = "0" . $cdate[0];
        }
        if (strlen($cdate[1]) == 1) {
            $cdate[1] = "0" . $cdate[1];
        }

        $lastupdate = "$tp[dag]-$tp[maand]-$tp[jaar] $tp[uur]:$tp[min]h.";

        print "$lang[deletingentity] $d $lang[ownedby] $owner, $lang[assignedto] $assignee.<br>";
        print "$lang[curstat]: $ea[status], $lang[created] $cdate, $lang[lastupdate] $tp[dag]-$tp[maand]-$tp[jaar] $tp[uur]:$tp[min]h.<br>";
        print "$lang[priority]: $ea[priority], $lang[category]: $ea[category]";
        print "<hr>";

        print "<img src='arrow.gif'>&nbsp;<a class='bigsort' href=$PHP_SELF?dconfirmed=$d>$lang[confirmdel]</a><br>";
        } elseif ($dconfirmed) {
        $sqldate = date('Y-m-d');
        $closeepoch = date('U');
        $sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET closedate='$sqldate',closeepoch='" . $closeepoch . "' WHERE eid='$dconfirmed'";
        mcq($sql, $db);
        uselogger("Entity $CloseEntity closed", "");

        $sql = "UPDATE $GLOBALS[TBL_PREFIX]entity SET deleted='y' WHERE eid='$dconfirmed'";
        mcq($sql, $db);

        //kaveh@arkasoft.com delete entity journal	
        $sql = "DELETE FROM $GLOBALS[TBL_PREFIX]ejournal where eid='$dconfirmed'";
        mcq($sql, $db);

        print "<font color=ff3300>Entity deleted.<br></font>";
         } elseif ($eid) {
        //$e = $_REQUEST['e'];			
        // case is edit of add

        if ($_REQUEST['close_on_next_load']) {

            // In this case the last update window was loaded in a pop window. Now the form is
            // submitted, the window may close itself
            ?>
            <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
                <!--
                window.close();
                //-->
            </SCRIPT>
            <?php
        }

        if ($_REQUEST['fromlistnow']) {

            // In this case the last update window was accessed from the main list,
            // after saving, go back to the list.
            if (strstr($fromlisturl, "____STASH-")) {
                $url_to_go_to = PopStashValue(ereg_replace("____STASH-", "", $fromlisturl));
            } elseif (strstr($fromlisturl, "____b64-")) {
                $fromlisturl = ereg_replace("____b64-", "", $fromlisturl);
                $url_to_go_to = base64_decode($fromlisturl);
            } else {
                $url_to_go_to = "index.php?ShowEntitiesOpen=1&tab=99";
            }

            qlog("Redirecting this user!");
            ?>
            <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
                <!--

                document.location='<? echo $url_to_go_to; ?>';
                //-->
            </SCRIPT>
            <?php
        } else {
            
        }

        if ($keid) {
            $eid = $keid;
            $e = $keid;
        }

        if ($e <> "_new_") {
            $e = trim($e);
            $e = $e * 1;
        }

        $cl = GetClearanceLevel($GLOBALS['USERID']);

	//Si se viene de un submit, se recarga la pagina para evitar resubmits
	if(isset($_REQUEST['eid'])){
	    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=edit.php?e=$_REQUEST[eid]\">";
	    return;
	}
        // DETERMINE FORM TYPE!

        if (is_array($GLOBALS['ADDFORMLIST']) && $eid == "_new_" && ($GLOBALS['FormFinity'] == "Yes")) { // This means that the user may choose from different forms
            $to_tabs = array();
            foreach ($GLOBALS['ADDFORMLIST'] AS $form) {
                if ($form <> "default") {
                    $subj = GetFileSubject($form);
                    if ($subj) {
                        array_push($to_tabs, $form);
                        qlog("Added " . $subj . " to tab list!");
                        $tabbs[$form] = array("edit.php?e=_new_&ftu=" . $form . "&SetCustTo=" . $_REQUEST['SetCustTo'] => addslashes(GetFileSubject($form)), "comment" => "");
                        $added_tabs++;
                    }
                }
                /*
                  elseif ($form <> $GLOBALS['DefaultForm']) {
                  array_push($to_tabs, $form);
                  qlog("Added default form to tab list!");
                  $tabbs[$form] = array("edit.php?e=_new_&ftu=" . $GLOBALS['DefaultForm'] . "&SetCustTo=" . $_REQUEST['SetCustTo'] => $lang['add'], "comment" => "");
                  }
                 */
            }

            if (is_numeric($_REQUEST['ftu'])) {
                $navid = $_REQUEST['ftu'];
            } else {
                $navid = $GLOBALS['ADDFORMLIST'][0];
            }

            if (sizeof($GLOBALS['ADDFORMLIST']) > 1) {
                if ($added_tabs > 1) {
                    print "</table></td></tr></table>";
                    InterTabs($to_tabs, $tabbs, $navid);
                    print "<table><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";
                }
            }

            if (is_numeric($_REQUEST['ftu'])) {
                print CustomEditForm($_REQUEST['ftu'], $e);
                qlog("Building Custom Edit Form ($e)");
            } else {
                qlog("FormType: using first in row (ftu <> numeric)");
                //MainEditForm($e);	
                if (is_numeric($GLOBALS['ADDFORMLIST'][0])) {
                    qlog("Building Custom Edit Form ($e)");
                    print CustomEditForm($GLOBALS['ADDFORMLIST'][0], $e);
                } else {
                    //MainEditForm($e);
                    qlog("Building Default Emergency Edit Form ($e)");
                    print CustomEditForm($GLOBALS['DefaultForm'], $e);
                }
            }
        } elseif ($e <> "_new_" && is_numeric(GetEntityFormID($e)) && GetEntityFormID($eid) <> 0 && ($GLOBALS['FormFinity'] == "Yes")) {
            qlog("FormType: edit - Using form template from entity");
            if (GetFileSubject(GetEntityFormID($e)) <> "") {
                print CustomEditForm(GetEntityFormID($e), $e);
            } else {
                print "<img src='error.gif'> Form " . GetEntityFormID($e) . " not found. Defaulting.";
                log_msg("ERROR: Entity " . $e . " wants to use form " . GetEntityFormID($e) . " - this form is not available. Falling back to default form.");
            //qlog("Building Main Edit Form ($e)");
                qlog("Building Default Emergency Edit Form ($e)");
                print CustomEditForm($GLOBALS['DefaultForm'], $e);
            //MainEditForm($e);
            }
        } elseif ($e == "_new_" && is_numeric($GLOBALS['ENTITY_LIMITED_ADD_FORM']) && $cl == "ro") {
            qlog("FormType: add [limited] - Using form template " . $GLOBALS['ENTITY_LIMITED_ADD_FORM']);
            print CustomEditForm($GLOBALS['ENTITY_LIMITED_ADD_FORM'], $e);
        } elseif ($e == "_new_" && is_numeric($GLOBALS['ENTITY_ADD_FORM']) && ($cl == "rw" || $cl == "administrator" || $cl == "full-access-own-entities")) {
            qlog("FormType: add [full-access] - Using form template " . $GLOBALS['ENTITY_ADD_FORM']);
            print CustomEditForm($GLOBALS['ENTITY_ADD_FORM'], $e);
        } elseif (is_numeric($e) && is_numeric($GLOBALS['ENTITY_LIMITED_EDIT_FORM']) && $cl == "ro") {
            qlog("FormType: edit [limited] - Using form template " . $GLOBALS['ENTITY_LIMITED_EDIT_FORM']);
            print CustomEditForm($GLOBALS['ENTITY_LIMITED_EDIT_FORM'], $e);
        } elseif (is_numeric($e) && is_numeric($GLOBALS['ENTITY_EDIT_FORM']) && ($cl == "rw" || $cl == "administrator" || $cl == "full-access-own-entities")) {
            qlog("FormType: edit [full-access] - Using form template " . $GLOBALS['ENTITY_EDIT_FORM']);
            print CustomEditForm($GLOBALS['ENTITY_EDIT_FORM'], $e);
        } else {
	    //qlog("FormType: using default entity form");
	    //qlog("Building Main Edit Form ($e)");
            qlog("Building Default Emergency Edit Form ($e)");
            print CustomEditForm($GLOBALS['DefaultForm'], $e);
	    //MainEditForm($e);
        }

        printAdminFormChanger($e);

        EndHTML();
    } else {

        qlog("WARNING: Don't know what to do! Redirecting this user (" . GetUserName($GLOBALS['USERID']) . ") to the main page!");
        ?>
        <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
            <!--
            document.location='index.php';
            //-->
        </SCRIPT>
        <?php
        print "</TD></TR></TABLE>";
        EndHTML();
    }
} // end if access to entity


?>


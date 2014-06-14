<?php

/**
 * ICTC
 * @Copyright (c) 2012 
 * @author
 * Cvillafuerte (cvillafuerte@ictc.com.mx)
 * PaKo Anguiano
 * 
 */
include("header.inc.php");

if ($_REQUEST['soft'] == '1') {
    info();
    sofxclie();
}
elseif ($_REQUEST['form'] == '1') {
    info();
    softwarexcliente();
}
elseif ($_REQUEST['clientes'] == '1') {
    info();
    allcustomers();
}
elseif ($_REQUEST['grupos'] == '1') {
    info();
    customersgroup();
}
elseif ($_REQUEST['qgroup'] == '1') {
    info();
    queryxgrupos();
}
elseif($_REQUEST['allsoft']=='1') {
    info();
    clientesxsoftware();
}
elseif($_REQUEST['dosoft']=='1'){
    info();
    queryclienxsoft();
}
elseif($_REQUEST['modulos']=='1'){
    info();
    xmodulos();
}
elseif($_REQUEST['domodulos']=='1'){
    info();
    querymodulos();
}
elseif($_REQUEST['polxclie']=='1'){
    info();
    polizaxcliente();
}
elseif($_REQUEST['qpolxclie']=='1'){
    info();
    querypolxclie();
}
elseif($_REQUEST['cliexpol']=='1'){
    info();
    clientesxpoliza();
}
elseif($_REQUEST['qcliexpol']=='1'){
    info();
    querycliexpol();
}
elseif($_REQUEST['fbatch']=='1'){
    info();
    formBatch();
}
elseif($_REQUEST['batch']=='1'){
    info();
    batch();
}
elseif($_REQUEST['procesos']=='1'){
    info();
    generarproceso();
}
elseif($_REQUEST['tproceso']=='1'){
    terminarproceso();
}
elseif($_REQUEST['proceso_new']=='1'){
    info();
    form_process();
}
elseif(isset($_REQUEST['new_process'])){
    new_process();
}
elseif($_REQUEST['pdf']=='1'){
    info();
    crearpdf();
}
elseif($_REQUEST['procesosend']=='1'){
    info();
    procesosend();
}
elseif($_REQUEST['mail']=='1'){
    info();
    sendmail();
}
elseif($_REQUEST['fmail']=='1'){
    info();
    formmail();
}
elseif($_REQUEST['hw']=='1'){
    info();
    formhw();
}
elseif($_REQUEST['tags']=='1'){
    info();
    formTags();
}
else{
    info();
}
EndHTML();

//********* Menu de Inicio*************
function info() {
    $chec = " <script type='text/javascript' src='reportes/reportes.js'></script>";              
    echo $chec;
    $menu = "
        <fieldset style='width:1300;margin-left:25px;' ><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Reportes</legend>
            <table  width='100%'align='center'>
                <tr>
                    <td>
                        <ul id='navigation'>                 
                            <li class='sub'>
                            <a href='#'><img src='reportes/alien.png' align='absmiddle'>Clientes</a>
                                <ul>
                                    <li><a href='reportes.php?clientes=1&tab=$_REQUEST[tab]'>Todos Los Clientes</a></li>
                                    <li><a href='reportes.php?grupos=1&tab=$_REQUEST[tab]'>Clientes Por Grupo</a></li>
                                </ul>
                            </li>
                            <li class='sub'>
                                <a href='#'><img src='reportes/soft.png' align='absmiddle'>Software</a>
                                <ul>
                                    <li><a href='reportes.php?soft=1&tab=$_REQUEST[tab]'>Software x Cliente</a></li>
                                    <li><a href='reportes.php?allsoft=1&tab=$_REQUEST[tab]'>Clientes x Software</a></li>
                                    <li><a href='reportes.php?modulos=1&tab=$_REQUEST[tab]'>x Software,Version y Modulos</a></li>
                                </ul>
                            </li>
                                   <li class='sub'>
                                <a href='#'><img src='reportes/poliza.png' align='absmiddle'>Polizas</a>
                                <ul>
                                    <li><a href='reportes.php?polxclie=1&tab=$_REQUEST[tab]'>Polizas x Cliente</a></li>
                                    <li><a href='reportes.php?cliexpol=1&tab=$_REQUEST[tab]'>Clientes x Tipo Poliza</a></li>
                                </ul>
                            </li>
			    <li class='sub'>
                                <a href='#'><img src='reportes/configure.png' align='absmiddle'>Procesos</a>
                                <ul>
                                    <li><a href='reportes.php?procesos=1&tab=$_REQUEST[tab]'>Proceso Activos</a></li>
                                    <li><a href='reportes.php?procesosend=1&tab=$_REQUEST[tab]'>Procesos Terminados</a></li>
				    <li><a href='reportes.php?proceso_new=1&tab=$_REQUEST[tab]'>Nuevo proceso</a></li>
                                </ul>
                            </li>
                            <li class='sub'>
                                <a href='reportes.php?hw=1&tab=$_REQUEST[tab]'>
                                    <img src='imgs/server.png' align='absmiddle'>
                                        Hardware
                                </a>
                            </li>
                            <li class='sub'>
                                <a href='reportes.php?tags=1&tab=$_REQUEST[tab]'>
                                    <img src='reportes/soft.png' align='absmiddle'>
                                        Etiquetas
                                </a>
                            </li>
                        </ul>                            
                    </td>
                </tr>                
            </table>";
    echo $menu;
}

//********Get Datos del Cliente**************
function clientedata($idclient) {
    $select = "select id, custname, contact,contact_title,contact_email,contact_phone,cust_address From CRMcustomer where id='$idclient'";
    $doselct = mcq($select, $db);
    $res = mysql_fetch_array($doselct);
    return $res;
}

function head(){
     return " <th>Software Name</th>
                    <th>Version</th>
                  <th>Descripcion</th>
                  <th>Modulos</th>
                  <th>status</th>";
}
//***Resultado del Reporte 
//***Software Por Cliente
//***Opcion Software de Servidor, Dispensario o Ambos

//*********Captura de Informacion****
function sofxclie() {
$conn_db=  conexion();
    $form = "
        <div class='login-block'>
            <form action='reportes.php?form=1&tab=$_REQUEST[tab]' method='post'>
                <br>              
                <p><label for='sharepoint-user-name'>Client Name</label></p><p><input type='text' name='cliente' id='sharepoint-user-name' ></p>
                        <input type='hidden' id='sharepoint-company-name' name='id'>
                        <p><label for='sharepoint-user-name'>Dispositivo:</label></p>
    <fieldset>
                <p><input type='checkbox' name='d1' id='sharepoint-company' value='1'/><label for='sharepoint-user-name'>Servidor</label></p><br>
                <p><input type='checkbox' name='d2' id='sharepoint-company' value='2'/><label for='sharepoint-user-name'>Dispensario</label></p></fieldset><br>
                <p><input type='submit' id='sharepoint-submit' class='button' value='Generar'></p><br>
            </form>
                    </div>
            <script type='text/javascript'>
                            $(\"#sharepoint-user-name\").focus()
                            $(\"#sharepoint-user-name\").autocomplete2(\"autocomplete.php?customer=1&cdb=$conn_db\", {
                                width: 497,
                                matchContains: true,
                                selectFirst: false
                            }).result(function(event, data, formatted) {
                                $(\"#sharepoint-company-name\").val(data[1])
                            });
                            </script>";
    echo $form;
}

function softwarexcliente() {
        $chec = " <script type='text/javascript' src='reportes/reportes.js'></script>";              
    echo $chec;
    if($_REQUEST[id]!=''){
    //-----------------------------------------------
    //obtenemos los datos del cliente y Mostramos sus datos     
    $fes = clientedata($_REQUEST[id]);
    $reporttable = " <form name='softclie'  method='post'><br>
                                     <div align='center' class='headers'><b>REPORTE DE SOFTWARE POR CLIENTE</b></div>
                                    <h3>
                                    <b>Cliente : </b>$fes[custname]<img src='crm.gif' align='right'><br>
                                    <b>Contacto: </b>$fes[contact]<br>
                                    <b>Phone De Contacto:</b>$fes[contact_phone]<br>
                                    <b>Direccion:</b>$fes[cust_address]</h3><br>
                                   <table  align='center' width='100%' class='report' >
                                         <tr><td colspan='9' align='right'>
                                    <input type='image' src='reportes/procesos.png' id='sharepoint-submit' class='button' name='batch' onclick='javascript:OnSubmitForm(\"batch\",\"softclie\",\"rebatch\");' value='Generar Batch' />
                             <input type='image' src='reportes/Acrobat.png' name='pd'  id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"pdf\",\"softclie\",\"softclie\");' value='Generar PDF' />
                            <input type='image' src='reportes/mail.png' name='pd'  id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"mail\",\"softclie\",\"softclie\");' value='Generar Mail' />   
                                <input type='hidden' name='checkid[]' value='$fes[id]'></td></tr>";
                                    
    //--------------------------------------------
    if ($_REQUEST['d1'] == '1' && $_REQUEST['d2'] == '') {
        $con = querysoftxcliente($_REQUEST[id],'1', '');
     $reporttable.="<tr><td colspan='5'><div class='headers'>SERVIDOR</div></td></tr>";
             $reporttable.=head();
        while ($fe = mysql_fetch_array($con)) {
             if($fe[estatus]=='inactivo'){
                $estatus='#FA5858';
            }else{
                $estatus='black';
            }
            $reporttable.="                   
                                    <tr >
                                        <td>$fe[nombre]</td>
                                         <td>$fe[version]</td>
                                         <td>$fe[descripcion]</td>
                                        <td>" . modulos($fe[id]) . "<br>\n</td>
                                        <td width='5%' style='color:$estatus'>$fe[estatus]<input type='hidden' name='dispositivo' value='1'></td>
                                        
                                  </tr>";
        }
    } elseif ($_REQUEST['d2'] == '2' && $_REQUEST['d1'] == '') {
        $select = "Select nodispenser From CRMdispenser where idcustomer=$_REQUEST[id]";
        $doselect = mcq($select, $db);
        $reporttable.="<tr><td colspan='5'><div class='headers'>DISPENSARIOS</div></td></tr>";
        $reporttable.=head();
        while ($f = mysql_fetch_array($doselect)) {
            $reporttable.="<tr><td colspan='5' bgcolor='#E1E8F1'><b>Dispensario $f[nodispenser]</b></td><tr>";
            $con = querysoftxcliente($_REQUEST[id],'2', $f[nodispenser]);
            while ($fecon = mysql_fetch_array($con)) {
                 if($fecon[estatus]=='inactivo'){
                $estatus='#FA5858';
            }else{
                $estatus='black';
            }
                $reporttable.="                   
                                    <tr>
                                        <td>" . $fecon['nombre'] . "</td> 
                                            <td>$fecon[version]</td>
                                         <td>" . $fecon['descripcion'] . "</td>
                                             <td></td>
                                        <td width='5%' style='color:$estatus'>" . $fecon['estatus'] . "
                                            <input type='hidden' name='dispositivo' value='2'>
                                            
                                            </td>
                                  </tr>";
            }
            $reporttable.="<input type='hidden' name='dispensario[]' value='$f[nodispenser]'>";
        }
        //si selecciona ambos dispositivos
    } else {
        $con = querysoftxcliente($_REQUEST[id],'1', '');
        $reporttable.="<tr><td colspan=5><div class='headers'>SERVIDOR</div></td></tr>";
        $reporttable.=head();
        while ($fe = mysql_fetch_array($con)) {
            if($fe[estatus]=='inactivo'){
                $estatus='#FA5858';
            }else{
                $estatus='black';
            }
            $reporttable.="                   
                                    <tr>
                                        <td>" . $fe['nombre'] . "</td>
                                            <td>$fe[version]</td>
                                         <td>" . $fe['descripcion'] . "</td>
                                        <td>" . modulos($fe[id]) . "<br>\n</td>
                                        <td style='color:$estatus;' width='5%'>" . $fe['estatus'] . "<input type='hidden' name='idsoft' value='$fe[id]'></td>
                                  </tr>";
        }
        $select = "Select nodispenser From CRMdispenser where idcustomer=$_REQUEST[id]";
        $doselect = mcq($select, $db);
        $reporttable.="<tr><td colspan='5'><div class='headers'>DISPENSARIOS</div></td></tr>";
        while ($f = mysql_fetch_array($doselect)) {
            $reporttable.="<tr><td colspan='5' bgcolor='#DEDEDE'><b>Dispensario $f[nodispenser]</b></td><tr>";
            $con = querysoftxcliente($_REQUEST[id],'2', $f[nodispenser]);
            while ($fecon = mysql_fetch_array($con)) {
                 if($fecon[estatus]=='inactivo'){
                $estatus='#FA5858';
            }else{
                $estatus='black';
            }
                $reporttable.="                   
                                    <tr>
                                        <td>" . $fecon['nombre'] . "</td>
                                            <td>$fecon[version]</td>
                                        <td>" . $fecon['descripcion'] . "</td>
                                        <td></td>
                                        <td style='color:$estatus;' width='5%'>" . $fecon['estatus'] . "<input type='hidden' name='idsoft' value='$fe[id]'></td>
                                  </tr>";
            }
                $reporttable.="<input type='hidden' name='dispensario[]' value='$f[nodispenser]'>";
        }
        $reporttable.="<tr><td><input type='hidden' name='dispositivo' value='12'></td></tr>";
    
    }
    $reporttable.="</table></fieldset></form>";
    }else{
        $reporttable.="<div class='login-block'><p><label for='sharepoint-user-company'>No especificaste un cliente....</label></p></div>";
    }
    echo $reporttable;
}

//*************Querys para reporte Software por cliente*******
function querysoftxcliente($clie,$dispositivo, $nodis) {
    if ($dispositivo == '1') {

        $selectserv = "Select CRMsoft_server.id, CRMsoft_server.version, CRMsoft_server.estatus,
                                    CRMsoft_catalog.nombre, CRMsoft_catalog.descripcion,CRMcustomer.id as cid
                                    From CRMsoft_server INNER JOIN  CRMsoft_catalog INNER JOIN CRMcustomer
                                    ON CRMsoft_server.idsoft=CRMsoft_catalog.idsoft AND CRMsoft_server.custid=CRMcustomer.id 
                                    Where CRMsoft_server.custid='$clie'";
        $doselect = mcq($selectserv, $db);
        return $doselect;
    } elseif ($dispositivo == '2') {
        $selectdisp = "Select CRMsoft_pumps.version,CRMsoft_pumps.estatus, CRMsoft_catalog.nombre, CRMcustomer.id as cid,
                                      CRMsoft_catalog.descripcion From CRMsoft_pumps INNER JOIN  CRMsoft_catalog INNER JOIN CRMcustomer
                                       INNER JOIN CRMdispenser ON CRMsoft_pumps.idpump=CRMdispenser.id AND CRMdispenser.idcustomer=CRMcustomer.id
                                       AND CRMsoft_pumps.idsoft=CRMsoft_catalog.idsoft Where CRMdispenser.idcustomer='$clie' and CRMdispenser.nodispenser='$nodis'";
        $dose = mcq($selectdisp, $db);
        return $dose;
    }
}

//**********Modulos Por Software***********
function modulos($idsoft) {
    $modulos = "SELECT CRMsoft_modules.idmodule,CRMsoft_modules.nombre as n,CRMsoft_server_modules.idinstall,CRMsoft_server_modules.idmodule 
    FROM CRMsoft_server_modules INNER JOIN CRMsoft_modules ON CRMsoft_server_modules.idmodule=CRMsoft_modules.idmodule
    where CRMsoft_server_modules.idinstall='$idsoft' and CRMsoft_server_modules.estatus='activo'";
    $domodulos = mcq($modulos, $db);
    if (mysql_num_rows($domodulos) >= 1) {
        $table = "";
        while ($f = mysql_fetch_array($domodulos)) {
            $table.="-" . $f[n] . "\n";
        }
        return $table;
    } else {
        return "";
    }
}

//****Reporte de todos los clientes 
function allcustomers() {
     $chec = " <script type='text/javascript' src='reportes/reportes.js'></script>";              
    echo $chec;
    $allcustomers = "Select id,custname,contact,contact_title,contact_phone,contact_email,cust_address From CRMcustomer where active='yes' Order By id ASC";
    $doallcustomer = mcq($allcustomers, $db);
    $form = " <h1><img src='crm.gif'><br>Reporte De Todos Los Clientes</h1>";
    $form.="<form name='allcustomers' method='post' >
                    <table class='report'>
                    <tr><td bgcolor='#E1E8F1' colspan='9' align='right'>
                    <input type='image' src='reportes/procesos.png'' id='sharepoint-submit' class='button' name='batch' onclick='javascript:OnSubmitForm(\"batch\",\"allcustomers\",\"rebatch\");' value='Generar Batch' />
                    <input type='image' src='reportes/Acrobat.png' name='pd'  id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"pdf\",\"allcustomers\",\"reclie\");' value='Generar PDF' />
                     <input type='image' src='reportes/mail.png' name='pd'  id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"mail\",\"allcustomers\",\"allcustomer\");' value='Generar Mail' />   
                    </td>
                    </tr>                   
                    <th><img src='reportes/check.png' onclick='javascript:seleccionar_todo(\"allcustomers\")' align='absmiddle' ><img src='reportes/nocheck.png' align='absmiddle' onclick='javascript:deseleccionar_todo(\"allcustomers\")' ></th>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Titulo de Contacto</th>
                    <th>Telefono de Contacto</th>
                    <th>Email de contacto</th>
                    <th>Direccion de Cliente</th>";
    $i = 0;
    while ($res = mysql_fetch_array($doallcustomer)) {
        $por = $i % 2;
        $form.="<tr class='fila_$por'>
                                <td><input type='checkbox' name='checkid[]' value='$res[id]'></td>
                                <td>$res[id]</td>
                                <td>$res[custname]</td>
                                <td>$res[contact]</td>
                                <td>$res[contact_title]</td>
                                <td>$res[contact_phone]</td>
                                <td>$res[contact_email]</td>
                                <td>$res[cust_address]</td>
                                <tr>";
        $i++;
    }
    $form.="</table></form>";
    echo $form;
}

//*****Form De especificacion de Grupos******
function customersgroup() {

    $selectgroup = "Select * From CRMgrupos where grp_active='1'";
    $doselectgroup = mcq($selectgroup, $db);
    $form = "<div class='login-block'>
            <form action='reportes.php?qgroup=1&tab=$_REQUEST[tab]' method='post'>
                <br>              
                <p><label for='sharepoint-user-name'>Selecciona Grupos</label></p><br>
               <p>";
    if ($_GET['sel'] == 'no') {
        $form.= "<label for='sharepoint-user-name' style='color:red;'>Selecciona Por Lo Menos un Grupo</label><br>";
    }
    $form.="<input type='checkbox' value='0' name='grupos[]'>0.-SIN GRUPO<br>";
    while ($res = mysql_fetch_array($doselectgroup)) {
        $form.="<input type='checkbox' value='$res[grp_id]' name='grupos[]'>$res[grp_id].-$res[grp_nombre]<br>";
    }
    
    $form.="</p>
                            <p><input type='submit' id='sharepoint-submit' class='button' value='Generar'></p><br>
                            </form>
                    </div>";
    echo $form;
}

//********Querys para generar reporte por modulos*********
function queryxgrupos() {
    $chec = " <script type='text/javascript' src='reportes/reportes.js'></script>";              
    echo $chec;
    if ($_REQUEST['grupos'] != '') {
        $form = "<h1><img src='crm.gif'><br>Reporte de Clientes Por Grupo</h1><br><br>
                    <form  method='post' name='grupos'>                    
                    <table class='report'>
                    <tr><td colspan='9' align='right' bgcolor='#E1E8F1'>
                    <input type='image' src='reportes/procesos.png'  id='sharepoint-submit' class='button' name='batch' onclick='javascript:OnSubmitForm(\"batch\",\"grupos\");' value='Generar Batch' />
                    <input type='image' src='reportes/Acrobat.png' name='pd'  id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"pdf\",\"grupos\",\"reclie\");' value='Generar PDF' />
                   <input type='image' src='reportes/mail.png' name='pd'  id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"mail\",\"grupos\",\"grupos\");' value='Generar Mail' />   </td></tr>                                     
                    <th><img src='reportes/check.png' onclick='javascript:seleccionar_todo(\"grupos\")' align='absmiddle' ><img src='reportes/nocheck.png' align='absmiddle' onclick='javascript:deseleccionar_todo(\"grupos\")' ></th>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Titulo de Contacto</th>
                    <th>Telefono de Contacto</th>
                    <th>Email de contacto</th>
                    <th>Direccion de Cliente</th>
                    <th>Grupo</th>";
        foreach ($_REQUEST[grupos] as $g) {
            $selectgroup = "Select * From CRMgrupos where grp_active='1' and grp_id='$g'";
            $doselectgroup = mcq($selectgroup, $db);
            $grupos = mysql_fetch_array($doselectgroup);

            $clientgroups = "Select id,custname,contact,contact_title,id_customer_group,contact_phone,contact_email,cust_address From CRMcustomer where active='yes' and id_customer_group='$g' Order By id ASC";
            $doclientgroups = mcq($clientgroups, $db);
            if (mysql_num_rows($doclientgroups) >= 1) {
                $i = 0;
                while ($resul = mysql_fetch_array($doclientgroups)) {
                    $por = $i % 2;
                    $form.="<tr class='fila_$por'>
                            <td><input type='checkbox' name='checkid[]' value='$resul[id]' ></td>
                                <td>$resul[id]</td>
                                <td>$resul[custname]</td>
                                <td>$resul[contact]</td>
                                <td>$resul[contact_title]</td>
                                <td>$resul[contact_phone]</td>
                                <td>$resul[contact_email]</td>
                                <td>$resul[cust_address]</td>
                                <td>$grupos[grp_id].-$grupos[grp_nombre]</td></tr>";
                    $i++;
                }
            } else {
                $form = "<br><br><h3 align='center'>No hay clientes en el Grupo</h3>";
            }
        }
        $form.="</table></form>";
        echo $form;
    } else {
        echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=reportes.php?grupos=1&tab=$_REQUEST[tab]&sel=no\">";
    }
}

//*********Clientes de acuerdo a un software*********
function clientesxsoftware(){
    $select="Select * From CRMsoft_catalog";
    $doselect=  mcq($select, $db);
    $form="
        <div class='login-block'>
            <form action='reportes.php?dosoft=1&tab=$_REQUEST[tab]' method='post'>
                <br>              
                <p><label for='sharepoint-user-name'>Nombre Software</label></p>
                <p><Select name='software'>";
                while($res=  mysql_fetch_array($doselect)){
                $form.="<option value='$res[idsoft]'>$res[nombre]</option>";                
                }
                        $form.="</select></p>
                <p><label for='sharepoint-user-company'>Version:</label></p>
                <p><input type='text' name='version' id='sharepoint-company'></p>
                <p><input type='submit' id='sharepoint-submit' class='button' value='Generar'></p><br>
            </form>
                    </div>";
                        echo $form;
}

//********querys & show de Report clientes por sotware y version********
function queryclienxsoft(){
        $chec = " <script type='text/javascript' src='reportes/reportes.js'></script>";              
    echo $chec;
    $cate="Select ca.idcategory,c.nombre From CRMsoft_categories as ca INNER JOIN CRMsoft_catalog as c ON c.idcategory=ca.idcategory  where c.idsoft='$_REQUEST[software]'";
    $docate=mcq($cate,$db);
    $rescate=  mysql_fetch_array($docate);
    if($rescate[idcategory]!='2'){
        if($_REQUEST[version]!=''){
    $sel="Select cl.id,s.version,s.idsoft,c.nombre,c.idsoft,s.estatus From CRMsoft_server as s INNER JOIN CRMsoft_catalog as c INNER JOIN CRMcustomer as cl ON cl.id=s.custid AND s.idsoft=c.idsoft
    where c.idsoft='$_REQUEST[software]' And s.version='$_REQUEST[version]' ORDER BY cl.id ASC";
        $dosel=  mcq($sel, $db);
        $th='';
        }else{
             $sel="Select cl.id,s.version,s.idsoft,c.nombre,c.idsoft,s.estatus From CRMsoft_server as s INNER JOIN CRMsoft_catalog as c INNER JOIN CRMcustomer as cl ON cl.id=s.custid AND s.idsoft=c.idsoft
    where c.idsoft='$_REQUEST[software]' ORDER BY cl.id ASC";
        $dosel=  mcq($sel, $db);
        $th='';
        }
    }else{
        if($_REQUEST[version]!=''){
       $sel="Select cl.id,p.version,p.idpump,p.idsoft,c.nombre,d.nodispenser,p.estatus From CRMcustomer as cl INNER JOIN CRMdispenser as d INNER JOIN CRMsoft_pumps as p INNER JOIN CRMsoft_catalog as c 
           ON d.id=p.idpump And p.idsoft=c.idsoft And d.idcustomer=cl.id Where p.idsoft='$_REQUEST[software]' And p.version='$_REQUEST[version]' ORDER BY cl.id,d.nodispenser ASC";
       $dosel=mcq($sel,$db);
       $th="<th>No. Dispensario</th>";
        }else{
       $sel="Select cl.id,p.version,p.idpump,p.idsoft,c.nombre,d.nodispenser,p.estatus From CRMcustomer as cl INNER JOIN CRMdispenser as d INNER JOIN CRMsoft_pumps as p INNER JOIN CRMsoft_catalog as c 
           ON d.id=p.idpump And p.idsoft=c.idsoft And d.idcustomer=cl.id Where p.idsoft='$_REQUEST[software]' ORDER BY cl.id,d.nodispenser ASC";
       $dosel=mcq($sel,$db);
       $th="<th>No. Dispensario</th>";     
        }
    }
    if(mysql_num_rows($dosel)>=1){  
    $table="<br>
                    <div class='headers'>Reporte De Clientes Por Software</div><br>
                    <form  method='post' name='clientexsof'>             
                    <table class='report' width='100%'>
                    <tr><td colspan='9' align='right' bgcolor='#E1E8F1'>
                                <input type='image' src='reportes/procesos.png'  id='sharepoint-submit' class='button' name='batch' onclick='javascript:OnSubmitForm(\"batch\",\"clientexsof\",\"rebatch\");' value='Generar Batch' />
                             <input type='image' src='reportes/Acrobat.png'   id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"pdf\",\"clientexsof\",\"resof\");' value='Generar PDF' />
                              <input type='image' src='reportes/mail.png' name='pd'  id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"mail\",\"clientexsof\",\"clientexsof\");' value='Generar Mail' />   
                              </td></tr>                                       
                    <th><img src='reportes/check.png' onclick='javascript:seleccionar_todo(\"clientexsof\")' align='absmiddle' ><img src='reportes/nocheck.png' align='absmiddle' onclick='javascript:deseleccionar_todo(\"clientexsof\")' ></th>
                    <th>Id</th>                    
                    <th>Nombre</th>";
                       $table.=$th;
                   $table.=" <th>Software</th>
                    <th>Version</th>
                    <th>estatus</th>";
    while($res=  mysql_fetch_array($dosel)){
        $color='black';
        if($res[estatus]=='inactivo'){
            $color='red';
        }
        $cliente=  clientedata($res[id]);
        $table.="<tr>
                                <td width='5%' align='center'><input type='checkbox' name='checkid[]' value='$res[id]-$res[nodispenser]-1'></td>
                                <td>$res[id]</td>                               
                                <td>$cliente[custname]</td> ";
                                 if($rescate[idcategory]=='2'){
                                   
                                     $table.="<td>Dispensario $res[nodispenser]</td>";
                                }
                                      $table.=" <td>$res[nombre]</td>
                                <td>$res[version]</td>
                                <td style='color:$color'>$res[estatus]
                                <input type='hidden' name='softid' value='$res[idsoft]'>
                                <input type='hidden' name='version' value='$_REQUEST[version]'>
                                <input type='hidden' name='categoria' value='$rescate[idcategory]'>
                                <input type='hidden' name='nodis[]' value='$res[nodispenser]'></td>
                        </tr>";
    }
    $table.="</table></form>";
    }else{
        $table="<div class='login-block'><p><label for='sharepoint-user-company'>No Existen Clientes Con:<br> Software:<b>$rescate[nombre]</b> y Version:<b>$_REQUEST[version]</b></label></p></div>";
    }
    echo $table;
}

//************Form De especificacion de Modulos****************
function xmodulos(){
    $conn_db=  conexion();
     $chec = " <script type='text/javascript' src='reportes/reportes.js'></script>";              
    echo $chec;        
        $select="Select * From CRMsoft_catalog where idcategory<>2";
    $doselect=  mcq($select, $db);    
     $form="
        <div class='login-block'>
            <form action='reportes.php?domodulos=1&tab=$_REQUEST[tab]' method='post' name='modulos'>
                <br>         
                <p><label for='sharepoint-user-name'>Nombre Software</label></p>
                <p><Select name='softmodule' onchange='javascript:changeselect(\"$conn_db\");'>
                        <option value='0' selected></option>";
                while($res=  mysql_fetch_array($doselect)){
                $form.="<option value='$res[idsoft]'>$res[nombre]</option>";                
                }
                $form.="</select></p>
                    <p><label for='sharepoint-user-company'>Modulos:</label></p>
                    <fieldset style='height:100px;width:485px'>
                        <p id='p'></p>
                <p><input type='hidden' name='d1' id='sharepoint-company' value='1'/></p><br>
                </fieldset><br>            
                 <p><label for='sharepoint-user-company'>Version:</label></p>
                <p><input type='text' name='version' id='sharepoint-company'></p>
                <p><input type='submit' id='sharepoint-submit' class='button' value='Generar'></p><br>
                   
            </form>
                    </div>";                   
                    echo $form;    
}

//**********querys & show de reporte de modulos***********
function querymodulos(){
    if($_REQUEST[softmodule]!='0'){
     $chec = " <script type='text/javascript' src='reportes/reportes.js'></script>";              
    echo $chec;
        
        if(isset($_REQUEST[idmodule])){
            $form = "<br>
                    <div class='headers'>Reporte de Clientes Por Modulos</div><br>
                    <form method='post' name='sofmodulos'>
                    <table class='report' width='100%'>
                    <tr><td colspan='9' align='right' bgcolor='#E1E8F1'>
                    <input type='image' src='reportes/procesos.png'  id='sharepoint-submit' class='button' name='batch' onclick='javascript:OnSubmitForm(\"batch\",\"sofmodulos\",\"rebatch\");' value='Generar Batch' />
                    <input type='image' src='reportes/Acrobat.png'  name='pd'  id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"pdf\",\"sofmodulos\",\"resofm\");' value='Generar PDF' />
                    <input type='image' src='reportes/mail.png' name='pd'  id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"mail\",\"sofmodulos\",\"sofmodulos\");' value='Generar Mail' />   </td></tr>                                                           
                    <th><img src='reportes/check.png' onclick='javascript:seleccionar_todo(\"sofmodulos\")' align='absmiddle' ><img src='reportes/nocheck.png' align='absmiddle' onclick='javascript:deseleccionar_todo(\"sofmodulos\")' ></th>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Software</th>
                    <th>Version</th>
                    <th>Estatus Software</th>
                    <th>Modulo</th>
                    <th>Estatus Modulo</th>";
            foreach ($_REQUEST[idmodule] as $idmodule){
   if($_REQUEST[version]==''){
       $sel="SELECT cl.id,mos.idinstall,mos.idmodule,m.nombre,c.nombre as cn,s.version,s.estatus,mos.estatus as ms,s.idsoft 
                FROM CRMsoft_server as s INNER JOIN CRMsoft_server_modules as mos INNER JOIN CRMsoft_catalog as c INNER JOIN CRMsoft_modules as m INNER JOIN CRMcustomer as cl 
                ON s.id=mos.idinstall And mos.idmodule=m.idmodule And s.idsoft=c.idsoft And cl.id=s.custid where s.idsoft='$_REQUEST[softmodule]' and mos.idmodule='$idmodule' Order By cl.id ASC";
        $dosel=  mcq($sel, $db);
        }else{
        $sel="SELECT cl.id,mos.idinstall,mos.idmodule,m.nombre,c.nombre as cn,s.version,s.idsoft 
                FROM CRMsoft_server as s INNER JOIN CRMsoft_server_modules as mos INNER JOIN CRMsoft_modules as m INNER JOIN CRMsoft_catalog as c INNER JOIN CRMcustomer as cl 
                ON s.id=mos.idinstall And mos.idmodule=m.idmodule And  s.idsoft=c.idsoft And cl.id=s.custid where s.idsoft='$_REQUEST[softmodule]' and mos.idmodule='$idmodule' And s.version='$_REQUEST[version]'";
        $dosel=  mcq($sel, $db);
        }

        while($fet=  mysql_fetch_array($dosel)){             
               $color=$fet[estatus]=='inactivo'?'red':'black';
               $color2=$fet[ms]=='inactivo'?'red':'black';               
            $cliente= clientedata($fet[id]);
            $form.="<tr>
                                <td width='5%' align='center'><input type='checkbox' name='checkid[]' value='$fet[id]-$fet[idmodule]-1'></td>                                
                                <td>$fet[id]</td>
                                <td>$cliente[custname]</td>                                
                                <td>$fet[cn]</td>   
                                <td>$fet[version]</td>
                                <td style='color:$color;'>$fet[estatus]</td>
                                <td>$fet[nombre]</td>                                
                                <td style='color:$color2;'>$fet[ms]
                                <input type='hidden' name='softid' value='$fet[idsoft]'>
                                <input type='hidden' name='version' value='$_REQUEST[version]'>
                                </td>
                            </tr>";
            }
            //}else{
               // $form.= "<div class='login-block'><p><label for='sharepoint-user-company'>Especifica Un:<b> Modulos</b></label></p></div>";
            //}
        }
    }else{
                $form.= "<div class='login-block'><p><label for='sharepoint-user-company'>Especifica Un:<b> Modulo</b></label></p></div>";
            }
    }else{
    $form.= "<div class='login-block'><p><label for='sharepoint-user-company'>Especifica Un:<b> Software</b></label></p></div>";
    }
        $form.="</table></form>";
        echo $form;
    } 
    
//************Form Polizas Por Cliente****************    
function polizaxcliente(){
   $conn_db=  conexion();
    $form = "
        <div class='login-block'>
            <form action='reportes.php?qpolxclie=1&tab=$_REQUEST[tab]' method='post'>
                <br>              
                <p><label for='sharepoint-user-name'>Client Name</label></p>
                 <p><input type='text' name='cliente' id='sharepoint-user-name' ></p>
                        <input type='hidden' id='sharepoint-company-name' name='id'>
                <p><input type='submit' id='sharepoint-submit' class='button' value='Generar'></p><br>
            </form>
                    </div>
            <script type='text/javascript'>
                            $(\"#sharepoint-user-name\").focus()
                            $(\"#sharepoint-user-name\").autocomplete2(\"autocomplete.php?customer=1&cdb=$conn_db\", {
                                width: 497,
                                matchContains: true,
                                selectFirst: false
                            }).result(function(event, data, formatted) {
                                $(\"#sharepoint-company-name\").val(data[1])
                            });
                            </script>";
    echo $form;
}

//************Querys  & show Polizas Por cliente********
function querypolxclie(){
         $chec = " <script type='text/javascript' src='reportes/reportes.js'></script>";              
    echo $chec;
    $select="Select cp.cpol_nombre,cp.cpol_descripcion,p.pol_id,p.pol_custid,p.pol_fini,p.pol_ffin,p.pol_modopago,p.pol_contrato,p.pol_ultpago,p.pol_proxpago,p.pol_status,p.pol_status_venc,p.pol_active
From CRMpolizas as p INNER JOIN CRMcatpolizas as cp ON p.pol_cpid=cp.cpol_id where p.pol_custid='$_REQUEST[id]' ORDER BY p.pol_status DESC";
    $doselect=  mcq($select, $db);
    $fe=  mysql_fetch_array($doselect);
    $cliente=  clientedata($fe[pol_custid]);
        $do=  mcq($select, $db);
    if(mysql_num_rows($do)>=1){
    $form="<br><form name='polxcliente' method='post'>
                                      <fieldset>
                                    <div class='headers'>REPORTE DE POLIZAS POR CLIENTE</div>                                             
                                    <h3>                                        
                                    <b>Cliente : </b>$cliente[custname]<img src='crm.gif' align='right'><br>
                                    <b>Contacto: </b>$cliente[contact]<br>
                                    <b>Phone De Contacto:</b>$cliente[contact_phone]<br>
                                    <b>Direccion:</b>$cliente[cust_address]</h3><br>          
                                    <table class='report' width='100%' align='center'>
                                    <tr><td colspan='11' align='right' bgcolor='#E1E8F1'>
                                    <input type='image' src='reportes/procesos.png'  id='sharepoint-submit' class='button' name='batch' onclick='javascript:OnSubmitForm(\"batch\",\"polxcliente\",\"rebatch\");' value='Generar Batch' />
                                    <input type='image' src='reportes/Acrobat.png'  name='pd'  id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"pdf\",\"polxcliente\",\"repoliza\");' value='Generar PDF' />
                                    <input type='image' src='reportes/mail.png' name='pd'  id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"mail\",\"polxcliente\",\"polxcliente\");' value='Generar Mail' />                                
        <input type='hidden' name='checkid[]' value='$cliente[id]'></td></tr>
                                    <th>IdPoliza</th>
                                    <th>Pol Name</th>
                                    <th>Poliza Desc.</th>
                                    <th>Fecha inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Modo pago</th>
                                    <th>Contrato</th>
                                    <th>Ultimo Pago</th>
                                    <th>Porximo Pago</th>
                                    <th>Status</th>
                                    <th>Active</th>";

    while($resul=  mysql_fetch_array($do)){        
        $status=$resul[pol_status]=='1'?'VIGENTE':'CANCELADA';
        $color=$resul[pol_status]=='1'?'black':'red';
        $active=$resul[pol_active]=='1'?'ACTIVA':'INHABILITADA';
        $color2=$resul[pol_active]=='1'?'black':'red';
        $form.="<tr>
                                <td><a href='polizas.php?custid=$cliente[id]&tab=$_REQUEST[tab]' target='_black'>$resul[pol_id]</td>
                                <td>$resul[cpol_nombre]</td>
                                <td>$resul[cpol_descripcion]</td>
                                <td>$resul[pol_fini]</td>
                                <td>$resul[pol_ffin]</td>
                                <td>$resul[pol_modopago]</td>
                                <td>$resul[pol_contrato]</td>
                                <td>$resul[pol_ultpago]</td>
                                <td>$resul[pol_proxpago]</td>
                                <td style='color:$color;'>$status</td>
                                <td style='color:$color2;'>$active</td>
                        </tr>";
        }
    }else{
        $form="<div class='login-block'><p><label for='sharepoint-user-company'><b>Cliente Sin Polizas </b></label></p></div>";
    }
        $form.="</table></form>";
        echo $form;
}

//***********Form Clientes Por Poliza*******************
function clientesxpoliza(){
      $chec = " <script type='text/javascript' src='reportes/reportes.js'></script>";              
    echo $chec;
    $catpolizas = "Select * From CRMcatpolizas";
    $docatpolizas = mcq($catpolizas, $db);
    $form = "<div class='login-block'>
            <form action='reportes.php?qcliexpol=1&tab=$_REQUEST[tab]' method='post' name='cliexpol'>
                <br>              
                <p><label for='sharepoint-user-name'>Selecciona Tipo Poliza</label></p><br>
               <p>";
    if ($_GET['sel'] == 'no') {
        $form.= "<label for='sharepoint-user-name' style='color:red;'>Selecciona Por Lo Menos Un Tipo de poliza</label><br>";
    }
   
    
    $form.="<input type='checkbox' value='0' name='polizas[]' onclick='javascript:showstatus(\"0\");'>SIN POLIZAS<br>";
    while ($res = mysql_fetch_array($docatpolizas)) {
        $form.="<input type='checkbox' value='$res[cpol_id]' name='polizas[]' onclick='javascript:showstatus(\"1\");'>$res[cpol_nombre]<input type='hidden' value='$res[cpol_nombre]' name='poliza'><br>";
    }
    $form.="<hr style='width:80%;'>";
     if ($_GET['sel'] == 'nopol') {
        $form.= "<label for='sharepoint-user-name' style='color:red;'>Selecciona Estatus</label><br>";
    }
    $form.="<p><label for='sharepoint-user-name'>Estatus</label></p><br>
                               <p style='visibility:hidden;' id='pcheck'><input type='checkbox' name='estatus[]' value='1' >VIGENTE<br>
                                       <input type='checkbox' name='estatus[]' value='0'>CANCELADA</p>
                                       <hr style='width:80%;'>";
    $form.="</p>
                            <input type='submit' id='sharepoint-submit' class='button' value='Generar'><br>
                            </form>
                    </div>";
    echo $form;
}

//***********Querys & show Cliente por Poliza*****************+
function querycliexpol(){
             $chec = " <script type='text/javascript' src='reportes/reportes.js'></script>";              
    echo $chec;
    $form="<br><form name='cliepolizas' method='post'>                    
                     <div class='headers'>REPORTE DE CLIENTES POR POLIZA</div><br>
                    <table class='report' width='100%' align='center'>
                    <tr><td colspan='9' align='right' bgcolor='#E1E8F1'>
                    <input type='image' src='reportes/procesos.png'   id='sharepoint-submit' class='button' name='batch' onclick='javascript:OnSubmitForm(\"batch\",\"cliepolizas\",\"rebatch\");' value='Generar Batch' />
                             <input type='image' src='reportes/Acrobat.png'   name='pd'  id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"pdf\",\"cliepolizas\",\"repoliza\");' value='Generar PDF' />
                             <input type='image' src='reportes/mail.png' name='pd'  id='sharepoint-submit' class='button' onclick='javascript:OnSubmitForm(\"mail\",\"cliepolizas\",\"cliepolizas\");' value='Generar Mail' /></td></tr>                                      
                    <th><img src='reportes/check.png' onclick='javascript:seleccionar_todo(\"cliepolizas\")' align='absmiddle' ><img src='reportes/nocheck.png' align='absmiddle' onclick='javascript:deseleccionar_todo(\"cliepolizas\")' ></th>
                    <th>Id</th>
                    <th>Nombre</th>
                   <th>Poliza</th>
                    <th>Contrato</th>
                    <th>Estatus</th>
                    <th>Activa</th>";
  if($_REQUEST[polizas]!=''){
      foreach ($_REQUEST[polizas] as $poliza){
          if($poliza!='0'){
          if($_REQUEST[estatus]!=''){
               foreach ($_REQUEST[estatus] as $estatus){
          $pol="select cp.cpol_id,cp.cpol_nombre,cp.cpol_descripcion,cl.id,p.pol_contrato,p.pol_id,p.pol_custid,p.pol_status,p.pol_active From CRMcatpolizas as cp INNER JOIN CRMpolizas as p On p.pol_cpid=cp.cpol_id INNER JOIN
        CRMcustomer as cl ON cl.id=p.pol_custid where cp.cpol_id='$poliza' And p.pol_status='$estatus' ORDER BY cl.id ASC";
        $dopol=  mcq($pol, $db);
        $con=0;
        while($r=  mysql_fetch_array($dopol)){
                $status=$r[pol_status]=='1'?'VIGENTE':'CANCELADA';
                $color=$r[pol_status]=='1'?'black':'red';
                $active=$r[pol_active]=='1'?'ACTIVA':'INHABILITADA';
                $color2=$r[pol_active]=='1'?'black':'red';
            $cliente=  clientedata($r[pol_custid]);
          $form.="<tr>
                              <td><input type='checkbox' name='checkid[]' value='$r[id]-$r[cpol_id]-$r[pol_status]' ></td>
                                <td><a href='polizas.php?custid=$cliente[id]&tab=27&1343253668'> $cliente[id]</a</td>
                                <td>$cliente[custname]</td>
                               <td>$r[cpol_nombre]</td>
                               <td>$r[pol_contrato]</td>
                               <td style='color:$color;'>$status</td>
                               <td style='color:$color2;'>$active<input type='hidden' name='status' value='$estatus'></td>
            
                            </tr>";
          $con++;
        }
        $form.="<tr><td align='right' colspan='9' bgcolor='#58ACFA'><b>Polizas Totales: $con</b></td></tr>";
        }
        }else{
            echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=reportes.php?cliexpol=1&tab=$_REQUEST[tab]&sel=nopol\">";
        }
      }else{
             $pol="select id as pol_custid From CRMcustomer where id not in(select pol_custid From CRMpolizas)";
            $dopol=  mcq($pol, $db);
            $status='';     
                   while($r=  mysql_fetch_array($dopol)){           
            $cliente=  clientedata($r[pol_custid]);
          $form.="<tr>
                              <td><input type='checkbox' name='checkid[]' value='$cliente[id]' ></td>
                              <td>$cliente[id]</td>
                                <td>$cliente[custname]</td>
                                <td>--</td>
                                <td>--</td>                                
                               <td>--</td>
                               <td>--</td>
                            </tr>";
          $con++;
        }
         $form.="<tr><td align='right' colspan='9' bgcolor='#58ACFA'><b>Polizas Totales: $con</b></td></tr>";
      }
    }
  }else{
        echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=reportes.php?cliexpol=1&tab=$_REQUEST[tab]&sel=no\">";
    }
    $form."</table></form>";
    echo $form;
}

//*******Datos Para conexion****
function conexion(){
    $conn_db = array('host' => $GLOBALS['host'][0], 'user' => $GLOBALS['user'][0], 'pass' => $GLOBALS['pass'][0], 'database' => $GLOBALS['database'][0]);
    $conn_db = base64_encode(serialize($conn_db));
    return $conn_db;
}

//***************Form para proceso Batch****************
function formBatch(){
    $chec = " <script type='text/javascript' src='reportes/reportes.js'></script>";
    echo $chec;
    $propietario="Select * From CRMloginusers";
    $dopropietario=mcq($propietario,$db);
    $do=  mcq($propietario, $db);
    $form.="<div class='login-block'>
            <form name='batch' action='reportes.php?batch=1&tab=$_REQUEST[tab]' method='post'>";    
    if($_REQUEST[checkid]!=""){
	foreach ($_REQUEST[checkid] as $idgroup){
	    $pos = strrpos($idgroup,'-');
	    if($pos===FALSE){
                $form.="<input type='hidden' name='id[]' value='$idgroup'>";
	    }else{
		$id=  substr($idgroup,0,$pos);
		$id2= strpos($id,'-');
		$ch=  substr($id, 0,$id2);
		$form.="<input type='hidden' name='id[]' value='$ch'>";
	    }
	}

	$form.="<br><p><label for='sharepoint-user-name'>Categoria</label></p>
	<p><input type='text' name='categoria' id='sharepoint-user-name' ></p>
	<p><label for='sharepoint-user-name'>Propietario:</label></p>
	<p><select name='propietario'>";

	while($fe=  mysql_fetch_array($dopropietario)){
	    $form.="<option value='$fe[id]'>$fe[FULLNAME]</option>";
	}
	$form.="</select></p>
	<p><label for='sharepoint-user-name'>Asignado:</label></p>
	<p><select name='asignado' id='sharepoint-user-name'>";

	while($fet=  mysql_fetch_array($do)){
	    $form.="<option value='$fet[id]'>$fet[FULLNAME]</option>";
	}
       $form.="</select> </p>
       <p><label for='sharepoint-user-name'>Descripcion:</label></p>
	<p><textarea name='comment' id='sharepoint-user-name' class='comment' ></textarea></p>
  
	<p><label for='sharepoint-user-name'>Duedate:</label></p>
	<p><input type='text' name='duedate' id='duedate' onFocus='javascript:calendario(\"duedate\");'></p>

	<p><label for='sharepoint-user-name'>Reminder:</label></p>
	<p><input type='text' name='reminder' id='reminder' onFocus='javascript:calendario(\"reminder\");'></p>
  
	<input type='button' id='sharepoint-submit' class='b' value='Generar' onclick='javascript:valida_envia();'><br><br>
	</form>
	</div>";
	echo $form;
    }
    else{
	echo "<script type='text/javascript'>
	validabatch();
	</script>";
    }
}

//***************Form para proceso Batch (sin actividades)****************
function form_process(){
    $form.="
    <br><br>
    <div class='login-block'>
	<form name='batch' action='reportes.php' method='post' autocomplete='off'><br><br>
	    <p><label for='sharepoint-user-name'>Nuevo proceso :</label></p><br>
	    <p><input type='text' name='proceso'  ></p>
	    <input type='submit' id='sharepoint-submit' class='b' value='Generar' name='new_process'><br><br>
	</form>
    </div>
    <table style='clear:both;' align='center' width='680'><tr><td><b>Nota:</b> Las tareas deberan asociarse posteriormente al proceso de forma manual</td></tr></table>
    <br><br><br>
    ";
    echo $form;
}

//***************Nuevo proceso Batch (sin actividades)****************
function new_process(){

    $sql="
    INSERT INTO CRMprocesosbatch(
    iduser,
    descripcion,
    estatus
    ) VALUES(
    '$GLOBALS[USERID]',
    '$_REQUEST[proceso]',
    'ACTIVO')
    ";
    mcq($sql,$db);
    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=reportes.php?procesos=1&tab=$_REQUEST[tab]\">";
}

//**************Query Proceso Batch
//**************Creacion de Entidades
function batch(){    
  $duedate=  str_replace("/","-",$_REQUEST[duedate]);
  $reminder=  str_replace("/","-",$_REQUEST[reminder]);
   $insertproceso="Insert Into CRMprocesosbatch(iduser,descripcion,estatus) Values('$GLOBALS[USERID]','$_REQUEST[categoria]','ACTIVO')";
   mcq($insertproceso,$db);
   $ultiproceso=  mysql_insert_id();
    foreach ($_REQUEST[id] as $id){
    $select="INSERT INTO CRMentity(category,content,status,priority,owner,assignee,CRMcustomer,sqldate,cdate,lasteditby,createdby,openepoch,formid,start_date,duedate,idproceso)
        VALUES('".$_REQUEST[categoria]."','".$_REQUEST[comment]."','Abierto','Alto','".$_REQUEST[propietario]."','".$_REQUEST[asignado]."','$id',
            '".Date("Y-m-d")."','".Date('Y-m-d')."','".$GLOBALS['USERID']."','".$GLOBALS['USERID']."','".date('U')."','22','".date("Y-m-d H:i:s")."','$duedate','$ultiproceso') ";
   mcq($select, $db);
    $ultimo=  mysql_insert_id();
    $sqldetail = "INSERT INTO CRMentitydetail (ed_eid,ed_uid,ed_date,ed_comment) 
                                   VALUES('" . $ultimo . "','" . $GLOBALS['USERID'] . "','" .Date('Y-m-d H:i:s'). "','Creacion de Nueva Actividad Como  Propietario:".  GetUserName($_REQUEST[propietario])."  y Asignado a: ".  GetUserName($_REQUEST[asignado])." ')";
    mcq($sqldetail, $db);
    
    $insertreminder="Insert Into CRMreminders(eid,reminderdate,createdby) Values('$ultimo','$reminder','".$GLOBALS['USERID']."')";
    mcq($insertreminder, $db);
    }
    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=reportes.php?procesos=1&tab=$_REQUEST[tab]\">";
  
}

//*********Generacio  de Proceso Batch
function generarproceso(){
    $chec = " <script type='text/javascript' src='reportes/reportes.js'></script>";              
    echo $chec;

    $conprocesos="
	Select
	idproceso,
	iduser,
	descripcion,
	estatus
	From CRMprocesosbatch
	Where iduser='$GLOBALS[USERID]'
	and estatus='ACTIVO'
	ORDER BY idproceso
    ";

    $docon=  mcq($conprocesos, $db);
    if(mysql_num_rows($docon)>=1){
	$form="
	<script type='text/javascript'>
	    $(function() {
		var moveLeft = 10;
		var moveDown = 10;
		$('img#mg').hover(
		    function(e) {
			$('div#pop-up').show();
		    },
		    function() {
			$('div#pop-up').hide();
		    }
		);
		$('img#mg').mousemove(function(e) {
		    $(\"div#pop-up\").css('top', e.pageY + moveDown).css('left', e.pageX + moveLeft);
		});

		$('input[id^=ocultar]').change(function(){
		    if ($(this).attr('checked')=='checked'){
			$('tr[id^=close]',$(this).parents('table:first')).hide();
		    }
		    else{
			$('tr[id^=close]',$(this).parents('table:first')).show();
		    }
		});
		$('tr[id^=close]').hide();

	    });
	</script>
	<div id='pop-up' align='center'>
	    <h3>Terminar Proceso<br>......</h3>
	</div>
	<br>";
                            
	while ($con=  mysql_fetch_array($docon)){
	    $form.="
	    <table class='report' width='100%' align='center' id='$con[idproceso]'>
	    <tr><td colspan=4 align='right'>
	    <div class='proces'>
		<b>Proceso:</b>$con[idproceso]<br>
		<b>Usuario:</b>".GetUserName($con[iduser])."<br>
		<b>Descripcion:</b>$con[descripcion]<br>
		<b>Estatus:</b>$con[estatus]<br>
		<b>Ocultar cerrados:</b><input type='checkbox' name='ocultar[$con[idproceso]]' id='ocultar[$con[idproceso]]' value='$con[idproceso]' checked>
	    </div>
           </td></tr>";
	    
	    $selprocesos="
	    Select
	    p.idproceso,
	    p.iduser,
	    p.descripcion,
	    p.estatus,
	    e.eid,
	    e.status,
	    e.category,
	    e.CRMcustomer,
	    cl.custname
	    From CRMprocesosbatch as p
	    INNER JOIN CRMentity as e ON p.idproceso=e.idproceso
	    INNER JOIN CRMcustomer as cl ON e.CRMcustomer=cl.id
	    Where p.idproceso='$con[idproceso]'
	    ORDER BY e.eid
	    ";

	    $doselprocesos=  mcq($selprocesos, $db);
	    $form.="
		<th>ID</th>
		<th>Cliente</th>
		<th>Entidad</th>
		<th>Estatus</th>
		";

	    while($resul=  mysql_fetch_array($doselprocesos)){
		if($resul['status']=='Cerrado'){
		    $color='red';
		}elseif($resul['status']=='Revision Operativa'){
		    $color='orange';
		}elseif ($resul['status']=='Revision administrativa') {
		    $color='F0DA35';
		}elseif($resul['status']=='En espera'){
		    $color='blue';
		}else{
		    $color='green';
		}

		$rowid = $resul['status'] == 'Cerrado' ? "closed_$con[idproceso]_$resul[eid]": "open_$con[idproceso]_$resul[eid]";

		$form.="
		<tr id='$rowid'>
		    <td>$resul[CRMcustomer]</td>
		    <td>$resul[custname]</td>
		    <td width='5%' align='center'><a href='edit.php?e=$resul[eid]' target='_blank'>$resul[eid]</a></td>
		    <td style='color:$color;' width='7%' align='center'>$resul[status]</td>
		</tr>";
	    }

	    $form.="
	    <tr>
		<td colspan='4' align='right'>
		    <a href='javascript:alerta(\"$con[idproceso]\");' ><img src='reportes/terminar.png' align='center' id='mg'></a>
		</td>
	    </tr>
	    </table>
	    ";
	}
    }
    else{
        $form="<table class='report' width='100%' align='center'><tr><td>Sin Procesos Activos</td></tr>";
    }

    $form.="<br><br><br><br>";

    echo $form;
}

//********Procesos Terminados*****
function procesosend(){
    $chec = " <script type='text/javascript' src='reportes/reportes.js'></script>";              
    echo $chec;
    $conprocesos="Select idproceso,iduser,descripcion,estatus From CRMprocesosbatch Where iduser='$GLOBALS[USERID]' and estatus='TERMINADO'";
    $docon=  mcq($conprocesos, $db);
        if(mysql_num_rows($docon)>=1){    
      $form="<br><table class='report' width='100%' align='center'>";
                            
    while ($con=  mysql_fetch_array($docon)){
        $form.="<tr><td colspan=4 align='right'>
        <div class='proces'>
            <b>Proceso:</b>$con[idproceso]<br> 
            <b>Usuario:</b>".GetUserName($con[iduser])."<br>
           <b>Descripcion:</b>$con[descripcion]<br>
           <b>Estatus:</b>$con[estatus]                
          </div>            
           </td></tr>";
    $selprocesos="Select p.idproceso,p.iduser,p.descripcion,p.estatus,e.eid,e.status,e.category,e.CRMcustomer,cl.custname From CRMprocesosbatch as p INNER JOIN CRMentity as e ON p.idproceso=e.idproceso INNER JOIN CRMcustomer as cl
        ON e.CRMcustomer=cl.id Where p.idproceso='$con[idproceso]'";    
    $doselprocesos=  mcq($selprocesos, $db);
    $form.="<th>ID</th>
                    <th>Cliente</th>
                    <th>Entidad</th>
                    <th>Estatus</th>";
    while($resul=  mysql_fetch_array($doselprocesos)){
        if($resul[status]=='Cerrado'){
            $color='red';
        }elseif($resul[status]=='Revision Operativa'){
            $color='orange';
        }elseif ($resul[status]=='Revision administrativa') {
                    $color='F0DA35';
                }elseif($resul[status]=='En espera'){
                    $color='blue';
                }else{
                    $color='green';
                }
        $form.="<tr>                                
                                <td>$resul[CRMcustomer]</td>
                                <td>$resul[custname]</td>
                                <td width='5%' align='center'><a href='edit.php?e=$resul[eid]' target='_blank'>$resul[eid]</a></td>
                                <td style='color:$color;' width='7%' align='center'>$resul[status]</td>
                      </tr>";
    }
      }
    }else{
        $form='Sin Procesos Activos';  
        }
         $form.="</table>";
    echo $form;
}

//****Cerrar Procesos*******
function terminarproceso(){
    $updatepro="Update CRMprocesosbatch set estatus='TERMINADO' WHERE idproceso='$_REQUEST[proce]'";
    mcq($updatepro, $db);
    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=reportes.php?procesos=1&tab=$_REQUEST[tab]\">";
}   

//******Creacionn de PDFS******
function crearpdf(){    
include("reportes/pdf.php");
set_time_limit(0);

$pdf=plantilla();
$datos=array();
//*********Reporte de Por Clientes*********************************************
if($_REQUEST[r]=='reclie'){
    foreach($_REQUEST[checkid] as $ch){
        $select="select * from CRMcustomer where id='$ch'";
        $result=  mcq($select, $db);
        while($res=  mysql_fetch_array($result,MYSQL_ASSOC)){  
            if($_REQUEST[tiprep]=='allcustomers'){
            $datos[]=array('Id'=>$res[id],'Nombre'=>$res[custname],'Contacto'=>$res[contact],'Telefono'=>$res[contact_phone],'Email'=>$res[contact_email],'Direccion'=>$res[cust_address]);
            $width=array(6,45,25,25,25,40);
        $align=array('C','J','L','L','L','J');
            }
            //***Clientes por grupo********
            elseif($_REQUEST[tiprep]=='grupos'){
                 $selectgroup = "Select grp_nombre From CRMgrupos where grp_active='1' and grp_id='$res[id_customer_group]'";
            $doselectgroup = mcq($selectgroup, $db);
            $grupos = mysql_fetch_array($doselectgroup);
            $datos[]=array('Id'=>$res[id],'Nombre'=>$res[custname],'Contacto'=>$res[contact],'Telefono'=>$res[contact_phone],'Email'=>$res[contact_email],'Direccion'=>$res[cust_address],'Grupo'=>$grupos[grp_nombre]);    
        $width=array(6,45,25,25,25,40,15);
        $align=array('C','J','L','L','L','J','L');
            }       
        }     
    }
      $pdf->tabla($datos,'REPORTE POR CLIENTES',8,$align,4,1,$width,2,'C');    
    $namefile="ReporteClientes";
}     //**********Clientes Por Software********
  elseif ($_REQUEST[r]=='resof') {
    foreach ($_REQUEST[checkid] as $che){
        $pos = strrpos($che,'-');
                if($pos===False){          
                }else{
                    $che2=substr($che,0,$pos);  
                    $pos2=  strrpos($che2, '-');
                    $dis=  substr($che2, $pos2+1,$pos2);
                    $che=  substr($che2, 0,$pos2);
                }
          $clientes=  clientedata($che);
         if($_REQUEST[categoria]!='2'){
            if($_REQUEST[version]!=''){
                        $do=  cxs($_REQUEST[softid], $_REQUEST[version], $che, $dis,'1');
            }else{
                        $do=  cxs($_REQUEST[softid], $_REQUEST[version], $che, $dis,'2');       
            }
           $dosel=  mcq($do, $db);
            while($sof=  mysql_fetch_array($dosel,MYSQL_ASSOC)){
            $datos[]=array(
                'Id'=>$clientes[id],'Nombre'=>$clientes[custname],'software'=>$sof[nombre],'Version'=>$sof[version],'Estatus'=>$sof[estatus]);    
                }     
            $width=array(6,70,25,25,20);
            $align=array('C','J','L','L','L');
        }else{
            if($_REQUEST[version]!=''){
                        $do=  cxs($_REQUEST[softid], $_REQUEST[version], $che, $dis,'3');
      }else{
                        $do=  cxs($_REQUEST[softid], $_REQUEST[version], $che, $dis,'4');
          }            
           $dosel=  mcq($do, $db);
            while($sof=  mysql_fetch_array($dosel,MYSQL_ASSOC)){
                $datos[]=array(
                'Id'=>$clientes[id],'Nombre'=>$clientes[custname],'Dispensario'=>$sof[nodispenser],'software'=>$sof[nombre],'Version'=>$sof[version],'Estatus'=>$sof[estatus]);    
            }
                 $width=array(6,70,25,20,20,20);
                $align=array('C','J','L','L','L','C');
        }                         
    }               
            $pdf->tabla($datos,'REPORTE POR SOFTWARE',8,$align,6,1,$width,2,'C');    
            $namefile="ReporteClientes";
}
    //**************Reporte Por Modulos*************
    elseif($_REQUEST[r]=='resofm'){        
         foreach ($_REQUEST[checkid] as $che){
             $pos = strrpos($che,'-');
                if($pos===False){          
                }else{
                    $che2=substr($che,0,$pos);  
                    $pos2=  strrpos($che2, '-');
                    $modulo=  substr($che2, $pos2+1,$pos2);
                    $che=  substr($che2, 0,$pos2);
                }
                $clientes=  clientedata($che);
                if($_REQUEST[version]==''){
                    $sel=  sxm($_REQUEST[softid], $modulo, $_REQUEST[version], $che, '1');
                }else{
                    $sel=  sxm($_REQUEST[softid], $modulo, $_REQUEST[version], $che, '2');
                }
                $dosel=  mcq($sel, $db);
                while($sof=  mysql_fetch_array($dosel,MYSQL_ASSOC)){
                    $datos[]=array('Id'=>$clientes[id],'Nombre'=>$clientes[custname],'Software'=>$sof[cn],'Version'=>$sof[version],'Estatus Software'=>$sof[estatus],'Modulo'=>$sof[nombre],'Estatus Modulo'=>$sof[ms]);          
                }
                  $width=array(6,70,15,15,20,30,20);
                 $align=array('C','J','L','L','C','L','C');
           }
            $pdf->tabla($datos,'REPORTE DE SOFTWARE POR MODULO',8,$align,6,1,$width,2,'C');    
            $namefile="ReporteClientes";
        }
    //**************Reporte Por Polizas***************
    elseif($_REQUEST[r]=='repoliza'){
        if($_REQUEST[tiprep]=='polxcliente'){
            foreach ($_REQUEST[checkid] as $che){
                    $select="Select cp.cpol_nombre,cp.cpol_descripcion,p.pol_id,p.pol_custid,p.pol_fini,p.pol_ffin,p.pol_modopago,p.pol_contrato,p.pol_ultpago,p.pol_proxpago,p.pol_status,p.pol_status_venc,p.pol_active
                    From CRMpolizas as p INNER JOIN CRMcatpolizas as cp ON p.pol_cpid=cp.cpol_id where p.pol_custid='$che' ORDER BY p.pol_status DESC";
                    $do=  mcq($select, $db);
                    while($sof=  mysql_fetch_array($do,MYSQL_ASSOC)){
                        $status=$sof[pol_status]=='1'?'VIGENTE':'CANCELADA';                                         
                        $active=$sof[pol_active]=='1'?'ACTIVA':'INHABILITADA';
                        $datos[]=array('Poliza'=>$sof[cpol_nombre],'Descripcion'=>$sof[cpol_descripcion],'Fecha Inicio'=>$sof[pol_fini],'Fecha fin'=>  $sof[pol_ffin],'Pago'=>$sof[pol_modopago],
                         'Contrato'=>$sof[pol_contrato],'Ultimo Pago'=>$sof[pol_ultpago],'Proximo Pago'=>$sof[pol_proxpago],'Estatus'=>$status,'Activa'=>$active);    
                    }
                    $width=array(25,30,15,15,8,35,15,15,15,17);
                    $align=array('L','L','C','C','C','L','C','C','C','C');  
                    $cliente=  clientedata($che);
                    $titulo="$cliente[custname]\nREPORTE DE POLIZAS POR CLIENTE";
            }                            
        }else{        
        foreach($_REQUEST[checkid] as $che){
              $clientes=  clientedata($che);
            $pos = strrpos($che,'-');
                if($pos===False){                  
        $datos[]=array( 'Id'=>$clientes[id],'Nombre'=>$clientes[custname],'Poliza'=>'--','Contrato'=>'--','Estatus'=>'--','Activa'=>'--');    
        $width=array(10,70,20,20,20,20);
        $align=array('C','J','C','C','C','C');                
                }else{
                    $status=  substr($che, $pos+1);
                    $che2=substr($che,0,$pos);
                    $pos2=  strrpos($che2, '-');
                    $poliza=  substr($che2, $pos2+1,$pos);
                     $che=  substr($che2,0,$pos2);                                    
                $pol="select cp.cpol_id,cp.cpol_nombre,cp.cpol_descripcion,cl.id,p.pol_contrato,p.pol_id,p.pol_custid,p.pol_status,p.pol_active From CRMcatpolizas as cp INNER JOIN CRMpolizas as p On p.pol_cpid=cp.cpol_id INNER JOIN
        CRMcustomer as cl ON cl.id=p.pol_custid where cl.id='$che' And cp.cpol_id='$poliza' And p.pol_status='$status' ORDER BY cl.id ASC";
                $dopol=mcq($pol,$db);
                  while($sof=  mysql_fetch_array($dopol,MYSQL_ASSOC)){
                $status=$sof[pol_status]=='1'?'VIGENTE':'CANCELADA';                                         
                 $active=$sof[pol_active]=='1'?'ACTIVA':'INHABILITADA';
        $datos[]=array( 'Id'=>$clientes[id],'Nombre'=>$clientes[custname],'Poliza'=>$sof[cpol_nombre],'Contrato'=>$sof[pol_contrato],'Estatus'=>$status,'Activa'=>$active);    
        $width=array(6,70,25,40,15,18);
        $align=array('C','J','C','L','C','C');
                }
           }
        }
    }
         $pdf->tabla($datos,$titulo,8,$align,6,1,$width,2,'C');    
    $namefile="ReporteClientes";
    }
    //************Reporte Software Por Cliente********
    elseif($_REQUEST[r]=='softclie'){
        foreach ($_REQUEST[checkid] as $check)
        if($_REQUEST[dispositivo]=='1'){
            $cliente=  clientedata($check);
            $titulo="$cliente[custname]\nSOFTWARE DE SERVIDOR";
            $dosof=  querysoftxcliente($check, $_REQUEST[dispositivo], '');
           while($sof=  mysql_fetch_array($dosof,MYSQL_ASSOC)){
                $datos[]=array('Software'=>$sof[nombre],'Version'=>$sof[version],'Descripcion'=>$sof[descripcion],'Modulos'=>  modulos($sof[id]."\n"),'Estatus'=>$sof[estatus]);    
                }
                $width=array(20,10,60,40,15);
      $align=array('L','C','L','L','C');  
        }elseif($_REQUEST[dispositivo]=='2'){
              $cliente=  clientedata($check);
              $titulo="$cliente[custname]\nSOFTWARE DE SERVIDOR";
               foreach ($_REQUEST[dispensario] as $dis){
                  $selectdisp = querysoftxcliente($check, $_REQUEST[dispositivo], $dis);
                  while($sof=  mysql_fetch_array($selectdisp,MYSQL_ASSOC)){                  
                    $datos[]=array('No.Dispensario'=>"Dispensario $dis",'Software'=>$sof[nombre],'Version'=>$sof[version],'Descripcion'=>$sof[descripcion],'Estatus'=>$sof[estatus]);    
                  }
                } 
                $width=array(20,20,10,60,15);
                $align=array('L','L','C','L','C');  
       }elseif($_REQUEST[dispositivo]=='12'){
              $dosof=  querysoftxcliente($check,'1', '');
              while($sof=  mysql_fetch_array($dosof,MYSQL_ASSOC)){
                    $datos[]=array('Dispositivo'=>'Servidor','Software'=>$sof[nombre],'Version'=>$sof[version],'Modulos'=>  modulos($sof[id]."\n"),'Descripcion'=>$sof[descripcion],'Estatus'=>$sof[estatus]);    
              } 
              foreach ($_REQUEST[dispensario] as $dis){
                 $selectdisp = querysoftxcliente($check, '2', $dis);
                 while($sof2=  mysql_fetch_array($selectdisp,MYSQL_ASSOC)){                  
                    $datos[]=array( 'Dispositivo'=>"Dispensario $dis",'Software'=>$sof2[nombre], 'Version'=>$sof2[version],'Modulos'=>'','Descripcion'=>$sof2[descripcion],'Estatus'=>$sof2[estatus]);    
                }
             }
              $cliente=  clientedata($check);
              $titulo="$cliente[custname]\nREPORTE DE SOFTWARE";
              $width=array(20,20,15,30,60,15);
              $align=array('L','L','C','L','L','C');  
       }          
       $pdf->tabla($datos,$titulo,8,$align,6,1,$width,2,'C');    
       $namefile="ReporteClientes";     
    }    
    echo "<pre>";
//print_r($_REQUEST);
  echo "</pre>";
//************************end report cliente*********************************
$pdf->Output($namefile.".pdf",'I');

}

//**********Querys pdf,mails********
function cxs($idsoft,$version,$clie,$dis,$i){
    if($i=='1'){
    $sel="Select cl.id,s.version,s.idsoft,c.nombre,c.idsoft,s.estatus From CRMsoft_server as s INNER JOIN CRMsoft_catalog as c INNER JOIN CRMcustomer as cl ON cl.id=s.custid AND s.idsoft=c.idsoft
    where c.idsoft='$idsoft' And s.version='$version' and cl.id='$clie' ORDER BY cl.id ASC";
         return $sel;
    }elseif($i=='2'){
             $sel="Select cl.id,s.version,s.idsoft,c.nombre,c.idsoft,s.estatus From CRMsoft_server as s INNER JOIN CRMsoft_catalog as c INNER JOIN CRMcustomer as cl ON cl.id=s.custid AND s.idsoft=c.idsoft
    where c.idsoft='$idsoft' and cl.id='$clie' ORDER BY cl.id ASC";
        return $sel;
    }elseif($i=='3'){
       $sel="Select cl.id,p.version,p.idpump,p.idsoft,c.nombre,d.nodispenser,p.estatus From CRMcustomer as cl INNER JOIN CRMdispenser as d INNER JOIN CRMsoft_pumps as p INNER JOIN CRMsoft_catalog as c 
           ON d.id=p.idpump And p.idsoft=c.idsoft And d.idcustomer=cl.id Where p.idsoft='$idsoft' And p.version='$version' and cl.id='$clie' And d.nodispenser='$dis' ORDER BY cl.id,d.nodispenser ASC";  
       return $sel;
    }elseif($i=='4'){
       $sel="Select cl.id,p.version,p.idpump,p.idsoft,c.nombre,d.nodispenser,p.estatus From CRMcustomer as cl INNER JOIN CRMdispenser as d INNER JOIN CRMsoft_pumps as p INNER JOIN CRMsoft_catalog as c 
           ON d.id=p.idpump And p.idsoft=c.idsoft And d.idcustomer=cl.id Where p.idsoft='$idsoft' and cl.id='$clie' And d.nodispenser='$dis' ORDER BY cl.id,d.nodispenser ASC";
       return $sel;
    }
}
function sxm($softid,$modulo,$version,$clie,$i){
     if($i=='1'){
                    $sel="SELECT cl.id,mos.idinstall,mos.idmodule,m.nombre,c.nombre as cn,s.version,s.estatus,mos.estatus as ms,s.idsoft 
                    FROM CRMsoft_server as s INNER JOIN CRMsoft_server_modules as mos INNER JOIN CRMsoft_catalog as c INNER JOIN CRMsoft_modules as m INNER JOIN CRMcustomer as cl 
                    ON s.id=mos.idinstall And mos.idmodule=m.idmodule And s.idsoft=c.idsoft And cl.id=s.custid where s.idsoft='$softid' And mos.idmodule='$modulo' And cl.id='$clie' Order By cl.id ASC";
                    return $sel;
     }elseif($i=='2'){
                    $sel="SELECT cl.id,mos.idinstall,mos.idmodule,m.nombre,c.nombre as cn,s.version,s.idsoft 
                    FROM CRMsoft_server as s INNER JOIN CRMsoft_server_modules as mos INNER JOIN CRMsoft_modules as m INNER JOIN CRMsoft_catalog as c INNER JOIN CRMcustomer as cl 
                    ON s.id=mos.idinstall And mos.idmodule=m.idmodule And  s.idsoft=c.idsoft And cl.id=s.custid where s.idsoft='$softid' And mos.idmodule='$modulo' And s.version='$version' And cl.id='$clie'";
                    return $sel;
                }
}
//*******Mail*******
function sendmail(){
    echo "<pre>";
  //print_r($_REQUEST);
  echo "</pre>";
$data = unserialize(base64_decode($_REQUEST["form"]));
$mails=  explode(",", $_REQUEST[mails]);
//print_r($mails);
        $head="<style type='text/CSS'>
                     .fila_0 { background-color: #FFFFFF;}        
                    .fila_1 { background-color: #E1E8F1;}
                    </style>
                    <form>
                    <table class='report' border=1 width=80% align=center> ";                  
                    $th="<th bgcolor='#E1E8F1'>Id</th>
                    <th bgcolor='#E1E8F1'>Nombre</th>
                    <th bgcolor='#E1E8F1'>Contacto</th>
                    <th bgcolor='#E1E8F1'>Titulo de Contacto</th>
                    <th bgcolor='#E1E8F1'>Telefono de Contacto</th>
                    <th bgcolor='#E1E8F1'>Email de contacto</th>
                    <th bgcolor='#E1E8F1'>Direccion de Cliente</th>";
                       $ths="<th bgcolor='#E1E8F1'>Id</th>
                    <th bgcolor='#E1E8F1'>Nombre</th>
                    <th bgcolor='#E1E8F1'>Dispositivo</th>
                    <th bgcolor='#E1E8F1'>Software</th>
                    <th bgcolor='#E1E8F1'>Version</th>
                    <th bgcolor='#E1E8F1'>Estatus</th>";
                       $thm="<th bgcolor='#E1E8F1'>Id</th>
                    <th bgcolor='#E1E8F1'>Nombre</th>
                    <th bgcolor='#E1E8F1'>Software</th>
                    <th bgcolor='#E1E8F1'>Version</th>
                    <th bgcolor='#E1E8F1'>Estatus</th>
                    <th bgcolor='#E1E8F1'>Modulo</th>
                    <th bgcolor='#E1E8F1'>Estatus</th>";
                       $thp="<th bgcolor='#E1E8F1'>Id</th>
                    <th bgcolor='#E1E8F1'>Nombre</th>
                    <th bgcolor='#E1E8F1'>Poliza</th>
                    <th bgcolor='#E1E8F1'>Contrato</th>
                    <th bgcolor='#E1E8F1'>Estatus</th>
                    <th bgcolor='#E1E8F1'>Activa</th>";
                       $thc="
                           <th bgcolor='#E1E8F1'>Software</th>
                           <th bgcolor='#E1E8F1'>Version</th>
                    <th bgcolor='#E1E8F1'>Descripcion</th>
                    <th bgcolor='#E1E8F1'>Modulo</th>
                    <th bgcolor='#E1E8F1'>Status</th>";
                       $thpo="<th bgcolor='#E1E8F1'>IdPoliza</th>
                                    <th bgcolor='#E1E8F1'>Pol Name</th>
                                    <th bgcolor='#E1E8F1'>Poliza Desc.</th>
                                    <th bgcolor='#E1E8F1'>Fecha inicio</th>
                                    <th bgcolor='#E1E8F1'>Fecha Fin</th>
                                    <th bgcolor='#E1E8F1'>Modo pago</th>
                                    <th bgcolor='#E1E8F1'>Contrato</th>
                                    <th bgcolor='#E1E8F1'>Ultimo Pago</th>
                                    <th bgcolor='#E1E8F1'>Porximo Pago</th>
                                    <th bgcolor='#E1E8F1'>Status</th>
                                    <th bgcolor='#E1E8F1'>Active</th>";
    //*****Coreo Todos los clientes******   
    if($data[htm]=='allcustomer'){
        foreach($data[checkid] as $che){
        $allcustomers = "Select id,custname,contact,contact_title,contact_phone,contact_email,cust_address From CRMcustomer where active='yes' And id='$che'Order By id ASC";
    $doallcustomer = mcq($allcustomers, $db);    
    $i = 0;
    while ($res = mysql_fetch_array($doallcustomer)) {
        $por = $i % 2;
        $body.="<tr class='fila_$por'>
                                <td>$res[id]</td>
                                <td>$res[custname]</td>
                                <td>$res[contact]</td>
                                <td>$res[contact_title]</td>
                                <td>$res[contact_phone]</td>
                                <td>$res[contact_email]</td>
                                <td>$res[cust_address]</td>
                                <tr>";
        $i++;
    }
        }
    $html.=$head;
      $html.=$th;
      $html.=$body;
    }elseif ($data[htm]=='grupos') {                   
        foreach ($data[checkid] as $g) {
            $clientgroups = "Select id,custname,contact,contact_title,id_customer_group,contact_phone,contact_email,cust_address From CRMcustomer where active='yes' and id='$g' Order By id ASC";
            $doclientgroups = mcq($clientgroups, $db);
            if (mysql_num_rows($doclientgroups) >= 1) {
                $i = 0;
                while ($resul = mysql_fetch_array($doclientgroups)) {
                    $por = $i % 2;
                    $body.="<tr class='fila_$por'>
                                <td>$resul[id]</td>
                                <td>$resul[custname]</td>
                                <td>$resul[contact]</td>
                                <td>$resul[contact_title]</td>
                                <td>$resul[contact_phone]</td>
                                <td>$resul[contact_email]</td>
                                <td>$resul[cust_address]</td></tr>";
                    $i++;
                }
                
            } else {
                $html.= "<br><br><h3 align='center'>No hay clientes en el Grupo</h3>";
            }
        }     
           $html.=$head;
      $html.=$th;
      $html.=$body;
}elseif($data[htm]=='clientexsof'){                     
             foreach ($data[checkid] as $che){
                     $pos = strrpos($che,'-');
                if($pos===False){          
                }else{
                    $che2=substr($che,0,$pos);  
                    $pos2=  strrpos($che2, '-');
                    $dis=  substr($che2, $pos2+1,$pos2);
                    $che=  substr($che2, 0,$pos2);
                }
             if($data[categoria]!='2'){
        if($data[version]!=''){
            $do=  cxs($data[softid], $data[version], $che, $dis,'1');
        }else{
             $do=  cxs($data[softid], $data[version], $che, $dis,'2');
        }
    }else{
        if($data[version]!=''){
             $do=  cxs($data[softid], $data[version], $che, $dis,'3');
        }else{
            $do=  cxs($data[softid], $data[version], $che, $dis,'4');
          
        }        
 }
 $dosel=  mcq($do, $db);
    if(mysql_num_rows($dosel)>=1){  
      while($res=  mysql_fetch_array($dosel)){
        $color=$res[estatus]=='inactivo'?'red':'black';          
        $cliente=  clientedata($res[id]);
        $body.="<tr>
                                <td>$res[id]</td>                               
                                <td>$cliente[custname]</td> ";
                                 if($data[categoria]=='2'){                                   
                                     $body.="<td>Dispensario $res[nodispenser]</td>";
                                }else{
                                    $body.="<td>Servidor</td>";
                                }
                                      $body.=" <td>$res[nombre]</td>
                                <td>$res[version]</td>
                                <td style='color:$color'>$res[estatus]
                                <input type='hidden' name='softid' value='$res[idsoft]'>
                                <input type='hidden' name='version' value='$data[version]'>
                                <input type='hidden' name='categoria' value='$rescate[idcategory]'></td>
                        </tr>";
    }    
      }else{
        $body="<div class='login-block'><p><label for='sharepoint-user-company'>No Existen Clientes Con:<br> Software:<b>$soft[nombre]</b> y Version:<b>$data[version]</b></label></p></div>";
    }             
                         }  
             
         $html.=$head;
         $html.=$ths;
         $html.=$body;
}elseif($data[htm]=='sofmodulos'){
    foreach ($data[checkid] as $che){
         $cliente=  clientedata($che);
       $pos = strrpos($che,'-');
                if($pos===False){          
                }else{
                    $che2=substr($che,0,$pos);  
                    $pos2=  strrpos($che2, '-');
                    $modulo=  substr($che2, $pos2+1,$pos2);
                    $che=  substr($che2, 0,$pos2);
                }
                 if($data[version]==''){
                    $sel=  sxm($data[softid], $modulo, $data[version], $che, '1');
                }else{
                    $sel=  sxm($data[softid], $modulo, $data[version], $che, '2');
                }
                $dosel=  mcq($sel, $db);
                while($fet=  mysql_fetch_array($dosel)){
                    $body.="<tr><td>$fet[id]</td>
                                <td>$cliente[custname]</td>                                
                                <td>$fet[cn]</td>   
                                <td>$fet[version]</td>
                                <td style='color:$color;'>$fet[estatus]</td>
                                <td>$fet[nombre]</td>                                
                                <td style='color:$color2;'>$fet[ms]
                                <input type='hidden' name='softid' value='$fet[idsoft]'>
                                <input type='hidden' name='version' value='$data[version]'></td></tr>";
                }
    }
    $html.=$head;
         $html.=$thm;
         $html.=$body;
}elseif($data[htm]=='cliepolizas'){
    foreach($data[checkid] as $che){
        $cliente=  clientedata($che);
         $pos = strrpos($che,'-');
          if($pos===False){
              $pol="select id as pol_custid From CRMcustomer where id not in(select pol_custid From CRMpolizas) And id='$che'";
              $dopol=  mcq($pol, $db);
          while($resul=  mysql_fetch_array($dopol)){
         $body.="<tr>
                               <td><a href='polizas.php?custid=$cliente[id]&tab=27&1343253668'> $cliente[id]</a</td>
                                <td>$cliente[custname]</td>
                               <td>--</td>
                               <td>--</td>
                               <td style='color:$color;'>--</td>
                               <td style='color:$color2;'>--</td>            
                            </tr>";
          }
                }else{
          $status=  substr($che, $pos+1);
          $che2=substr($che,0,$pos);
          $pos2=  strrpos($che2, '-');
          $poliza=  substr($che2, $pos2+1,$pos);
          $che=  substr($che2,0,$pos2);    
            $pol="select cp.cpol_id,cp.cpol_nombre,cp.cpol_descripcion,cl.id,p.pol_contrato,p.pol_id,p.pol_custid,p.pol_status,p.pol_active From CRMcatpolizas as cp INNER JOIN CRMpolizas as p On p.pol_cpid=cp.cpol_id INNER JOIN
        CRMcustomer as cl ON cl.id=p.pol_custid where cp.cpol_id='$poliza' And cl.id='$che' And p.pol_status='$status' ORDER BY cl.id ASC";                
          $dopol=  mcq($pol, $db);
          while($resul=  mysql_fetch_array($dopol)){
                  $status=$resul[pol_status]=='1'?'VIGENTE':'CANCELADA';
                $color=$resul[pol_status]=='1'?'black':'red';
                $active=$resul[pol_active]=='1'?'ACTIVA':'INHABILITADA';
                $color2=$resul[pol_active]=='1'?'black':'red';
         $body.="<tr>
                               <td><a href='polizas.php?custid=$cliente[id]&tab=27&1343253668'> $cliente[id]</a</td>
                                <td>$cliente[custname]</td>
                               <td>$resul[cpol_nombre]</td>
                               <td>$resul[pol_contrato]</td>
                               <td style='color:$color;'>$status</td>
                               <td style='color:$color2;'>$active<input type='hidden' name='status' value='$estatus'></td>            
                            </tr>";
         }
      }
    }
        $html.=$head;
         $html.=$thp;
         $html.=$body;
}elseif($data[htm]=='softclie'){
    foreach ($data[checkid] as $che){}
    $fes=clientedata($che);
    if($data[dispositivo]=='1'){
        $bod.="<tr><td colspan=6><h3>
                                    <b>Cliente : </b>$fes[custname]<img src='crm.gif' align='right'><br>
                                    <b>Contacto: </b>$fes[contact]<br>
                                    <b>Phone De Contacto:</b>$fes[contact_phone]<br>
                                    <b>Direccion:</b>$fes[cust_address]</h3></td></tr>";
        $sof=  querysoftxcliente($che, '1',"");
      
         while ($fe = mysql_fetch_array($sof)) {
            $body.=" <tr><td>Servidor</td>
                                        <td>$fe[nombre]</td> 
                                        <td>$fe[version]</td>
                                         <td>$fe[descripcion]</td>
                                        <td>" . modulos($fe[id]) . "<br>\n</td>
                                        <td width='5%' >$fe[estatus]</td>                            
                                  </tr>";
        }
         $html.=$head;
             $html.=$bod;
         $html.=$thc;
         $html.=$body;
    }elseif($data['dispositivo']=='2'){
        $bod.="<tr><td colspan=6><h3>
                                    <b>Cliente : </b>$fes[custname]<img src='crm.gif' align='right'><br>
                                    <b>Contacto: </b>$fes[contact]<br>
                                    <b>Phone De Contacto:</b>$fes[contact_phone]<br>
                                    <b>Direccion:</b>$fes[cust_address]</h3></td></tr>";
        foreach($data[dispensario] as $dis){
         $sof=  querysoftxcliente($che, '2',$dis);
          while ($fecon = mysql_fetch_array($sof)) {
                $body.="<tr>
                                    <td>Dispensario $dis</td>
                                        <td>" . $fecon['nombre'] . "</td> 
                                            <td>$fecon[version]</td>
                                         <td>" . $fecon['descripcion'] . "</td>
                                             <td></td>
                                        <td>" . $fecon['estatus'] . "</td>
                                  </tr>";
            }
        }
         $html.=$head;
             $html.=$bod;
         $html.=$thc;
         $html.=$body;
    }elseif($data['dispositivo']=='12'){
          $bod.="<tr><td colspan=6><h3>
                                    <b>Cliente : </b>$fes[custname]<img src='crm.gif' align='right'><br>
                                    <b>Contacto: </b>$fes[contact]<br>
                                    <b>Phone De Contacto:</b>$fes[contact_phone]<br>
                                    <b>Direccion:</b>$fes[cust_address]</h3></td></tr>";
          $dosof=  querysoftxcliente($che,'1', '');
          $body.="<tr><td colspan='5' bgcolor=#E1E8F1>Servidor</td></tr>";
              while($sof=  mysql_fetch_array($dosof,MYSQL_ASSOC)){
              $body.=" <tr>
                                        <td>$sof[nombre]</td> 
                                        <td>$sof[version]</td>
                                         <td>$sof[descripcion]</td>
                                        <td>" . modulos($sof[id]) . "<br>\n</td>
                                        <td width='5%' >$sof[estatus]</td>                            
                                  </tr>";              
                                  } 
              foreach ($data[dispensario] as $dis){
                  $body.="<tr>
                                    <td colspan=5 bgcolor=#E1E8F1>Dispensario $dis</td></tr>";
                 $selectdisp = querysoftxcliente($che, '2', $dis);
                 while($sof2=  mysql_fetch_array($selectdisp,MYSQL_ASSOC)){                  
             $body.="<tr>
                                        <td>" . $sof2['nombre'] . "</td> 
                                            <td>$sof2[version]</td>
                                         <td>" . $sof2['descripcion'] . "</td>
                                             <td></td>
                                        <td>" . $sof2['estatus'] . "</td>
                                  </tr>";             
                                  }
             }
             
         $html.=$head;
             $html.=$bod;
         $html.=$thc;
         $html.=$body;
    }         
}elseif($data[htm]=='polxcliente'){
        foreach ($data[checkid] as $chec){}
         $select="Select cp.cpol_nombre,cp.cpol_descripcion,p.pol_id,p.pol_custid,p.pol_fini,p.pol_ffin,p.pol_modopago,p.pol_contrato,p.pol_ultpago,p.pol_proxpago,p.pol_status,p.pol_status_venc,p.pol_active
                    From CRMpolizas as p INNER JOIN CRMcatpolizas as cp ON p.pol_cpid=cp.cpol_id where p.pol_custid='$chec' ORDER BY p.pol_status DESC";
                    $do=  mcq($select, $db);
                    while($resul=  mysql_fetch_array($do,MYSQL_ASSOC)){
                        $status=$resul[pol_status]=='1'?'VIGENTE':'CANCELADA';                                         
                        $active=$resul[pol_active]=='1'?'ACTIVA':'INHABILITADA';
                        $body.="<tr>
                                <td><a href='polizas.php?custid=$cliente[id]&tab=$data[tab]' target='_black'>$resul[pol_id]</td>
                                <td>$resul[cpol_nombre]</td>
                                <td>$resul[cpol_descripcion]</td>
                                <td>$resul[pol_fini]</td>
                                <td>$resul[pol_ffin]</td>
                                <td>$resul[pol_modopago]</td>
                                <td>$resul[pol_contrato]</td>
                                <td>$resul[pol_ultpago]</td>
                                <td>$resul[pol_proxpago]</td>
                                <td style='color:$color;'>$status</td>
                                <td style='color:$color2;'>$active</td>
                        </tr>";

                    }
                       $html.=$head;
        $html.=$thpo;
         $html.=$body;
    }
         $pie.="</table></form>";      
      $html.=$pie;
       //echo $html;
       
       $mail = new PHPMailer();
        $mail->From = $GLOBALS['admemail'];
        $mail->FromName = "CRM Reports";

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
        foreach($mails as $m){
        $mail->AddAddress($m,$m);
        }
        $mail->Subject = "CRM Reports";
                
        //if (!trim($info['mail']=="") && $count>0) {
        foreach ($mails as $m){
	print "<h3>Reporte Enviado a <b>$m</b></h3>"."<br>";        
        qlog("Sending daily entity overview to " . 'nombre' . " (" . $m . ") - $count\n");
        }
                //print "Sending entity overview to " . $info['nombre'] . " ($count entities)\n";
                if(!$mail->Send()) {
                        echo "<font color='#FF0000'>There has been a mail error sending to " . ":" . $mail->ErrorInfo . ". [Error en el envio de resumen de actividades del grupo].</font><br>";
                        $add_to_journal .= "\nSending e-mail to failed:" . $mail->ErrorInfo;
                        qlog("E-mail NOT sent.. ERROR: " . $mail->ErrorInfo);
                } else {
                        $add_to_journal .= "\nNotification e-mail sent to ";
                }
        //}
        $mail->ClearAddresses();
        $mail->ClearAttachments();

}

function formmail(){
  $re=array();
  foreach($_REQUEST as $k=>$v){
      $re[$k]=$v;
  }
  $c = base64_encode(serialize($re));
     $chec = "<script type='text/javascript' src='reportes/reportes.js'> </script>";
    echo $chec;
    $propietario="Select * From CRMloginusers";
    $dopropietario=mcq($propietario,$db);
    $form.="<div class='login-block'>
            <form name='ch' action='reportes.php?mail=1&tab=$_REQUEST[tab]' method='post'>";    
     $form.="<br>
                <p><label for='sharepoint-user-name'>Correo Usuario:</label></p>
                <p><select name='propietario' onchange='javascript:muestraentextarea(this.options[this.selectedIndex].value)'>";
                while($fe=  mysql_fetch_array($dopropietario)){
                $form.="<option value='$fe[EMAIL]'>$fe[FULLNAME]</option>";
                }                
               $form.="</select><p><label for='sharepoint-user-name'>Otro Correo:</label></p>
                <p><input type='text' name='duedate' id='otro' onFocus='javascript:calendario(\"duedate\");'>
                <img src='reportes/edit-add.png' align='absmiddle' onclick='muestraentextarea(document.getElementById(\"otro\").value)'></p>     
                   <p><label for='sharepoint-user-name'>Correos:</label></p>
                <p><textarea name='mails' id='txt' class='comment' size='7' style='width: 500px;'></textarea></p>       
                <input type='submit' id='sharepoint-submit' class='b' value='Generar'><br><br>
                <input type='hidden' name='form' value='$c'>
            </form>
                    </div>";
               echo $form;
}
//<editor-fold defaultstate="collapsed" desc="SEARCH FORM OF HARDWARE">
/**
 * Devuelve HTML de busqueda por Hardware
 * 
 */
function formhw() {
$conn_db=  conexion();
$generations = getGenerationDisp($conn_db);
//print_array($generations);
$select='
    <select id="generation">
    <option value=""></option>
    ';
foreach ($generations as $reg) {
    $select.="
        <option value='$reg[id]'>$reg[description]</option>
        ";
}
$select.='</select>';
?>
<LINK href="jquery/external/datatable.css" rel="StyleSheet" type="text/css">
<script type="text/javascript" language="javascript" src="jquery/external/jquery.dataTables.min.js"></script>
<script type='text/javascript'>
    var dTable;
    var cnn = '<?php echo $conn_db; ?>';
    $(document).ready(function(){
        var oCustomer = $("#customer");
        var oServer = $("#server");
        var oNoip = $("#noip");
        var oSerie = $("#serie");
        var oModel = $("#model");
        var oGeneration = $("#generation");
        
        var oResumen = $('#resumenHw');
        var oSearch = $("#search");
        var oFormSearch = $("#formSearch");
        
        oCustomer.focus()
        
        
        $("#dialog").dialog({
            bgiframe: true,
            autoOpen: false,
            modal: true,
            minWidth:650,
            closeOnEscape: true,
            resizable:false,
            draggable:false,
            hide: "fade"
        });
        
        var info = function (name,custid,cnn){
	    var content;
	    $.ajax({
		async: false,
		url: "autocomplete.php?getinfo=1",
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
        
        dTable = oResumen.dataTable({
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
                         { "bSortable": false, "aTargets": [ 0, 5 ] }
                    ]
                });

                dTable.fnSort( [ [1,'desc'] ] );
                
                //Click button Search
                oSearch.click(function(){
                    var params = Object();
                    params.customer = oCustomer.val();
                    params.server = oServer.val();
                    params.noip = oNoip.val();
                    params.serie = oSerie.val();
                    params.model = oModel.val();
                    params.generation = oGeneration.val();
                    
                    $.ajax({
                        url: "autocomplete.php?hw=1",
                        dataType: "json",
                        data : {
                            params:params,
                            db: cnn
                        },
                        success: function (result) {
                            dTable.fnDestroy();
                            
                            $('#registers').html(result);
                            
                            dTable = oResumen.dataTable({
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
                                             { "bSortable": false, "aTargets": [ 0, 5 ] }
                                        ]
                                    });

                            dTable.fnSort( [ [1,'desc'] ] );
                            __popup();
                        }
                    });
                    
                });
                //Enter inputs
                oFormSearch.find('input').on('keypress',function(e){
                    if(e.keyCode==13){
                        var nextIndex = parseFloat($(this).attr('tabindex'))+1;
                        oFormSearch.find('input[tabindex="'+nextIndex+'"]').focus();
                    }
                });
                
                //Popup
                function __popup(){
                    oResumen.find('img').on('click', function(){                        
                        var idcustomer = $(this).attr('idreg');
                        var option = $(this).attr('name');
                        var title = '';

                        if(option=='server'){
                            var title = 'Detalles de servidor';
                        }else{
                            var title = 'Detalles de dispensarios';
                        }
                        var content = info(option,idcustomer,cnn);

                        $('#dialog').dialog('option', 'title', title);
                        $('#dialog').html(content);
                        $("#dialog").dialog("open");
                    });
                }
                
                $("body").css({
                    "height":"100%"
                });
    });
</script>
<div style="text-align: right; width: 1100px;">
    <br>
    <!--
    <img id='reporthwpdf' alt="PDF" src="reportes/Acrobat.png">
    <img id='reporthwmail' alt="PDF" src="reportes/mail.png">
    -->
</div>
<div id='dialog' title=''></div>
<div class='divTable tableReport' style="width: 1100px;">
    <br>              
    <div class="divTr" style="width: 20%;">
        <div id="formSearch" class="divTable borderRight">
            <div class="divTr">
                <div class="divTd">
                    <label for="customer">
                        Cliente :
                    </label>
                </div>
            </div>
            <div class="divTr">
                <div class="divTd">
                    <input type='text' name='customer' id='customer' tabindex="1">
                </div>
            </div>
            <div class="divTr">
                <div class="divTd spaceTr">
                    <label for="server">
                        Servidor :
                    </label>
                </div>
            </div>
            <div class="divTr">
                <div class="divTd">
                    <input type='text' name='server' id='server' tabindex="2">
                </div>
            </div>
            <div class="divTr">
                <div class="divTd spaceTr">
                    <label for="noip">
                        No-Ip :
                    </label>
                </div>
            </div>
            <div class="divTr">
                <div class="divTd">
                    <input type='text' name='noip' id='noip' tabindex="3">
                </div>
            </div>
            <div class="divTr">
                <div class="divTd spaceTr">
                    <label for="serie">
                        Serie dispensario :
                    </label>
                </div>
            </div>
            <div class="divTr">
                <div class="divTd">
                    <input type='text' name='serie' id='serie' tabindex="4">
                </div>
            </div>
            <div class="divTr">
                <div class="divTd spaceTr">
                    <label for='model'>
                        Modelo dispensario :
                    </label>
                </div>
            </div>
            <div class="divTr">
                <div class="divTd">
                    <input type='text' name='cliente' id='model' tabindex="5">
                </div>
            </div>
            <div class="divTr">
                <div class="divTd spaceTr">
                    <label for='generation'>
                        Generacion :
                    </label>
                </div>
            </div>
            <div class="divTr">
                <div class="divTd">
                    <?php echo $select; ?>
                </div>
            </div>
            <div class="divTr">
                <div class="divTd spaceTr" style="text-align: center;">
                    <input type='button' name='search' id='search' value="Buscar" tabindex="6">
                </div>
            </div>
            <br>
        </div>
        <div class="divTd borderLeft">
            <br>
            <!-- This content is generate by ajax -->
            <table id="resumenHw" align="center" class="crm">
                <thead>
                    <tr>
                        <th width="30"></th>
                        <th width="270">Cliente</th>
                        <th width="160">Servidor</th>
                        <th width="160">Cuenta No-Ip</th>
                        <th width="160">DNS</th>
                        <th width="120">Dispensarios</th>
                    </tr>
                </thead>
                <tbody id="registers">

                </tbody>
            </table>
            <br>
            <br>
        </div>
    </div>
    <br>
</div>
<?php
}

//<editor-fold defaultstate="collapsed" desc="SEARCH FORM OF TAGS">
/**
 * Devuelve HTML de Estadisticas de Etiquetas
 * 
 */
function formTags() {
$conn_db=  conexion();
//$dataTags = getStatisticTags($conn_db);
//print_array($dataTags);
?>
<!--[if lt IE 9]>
<script language="javascript" type="text/javascript" src="jquery/external/excanvas.js"></script>
<![endif]-->
<script language="javascript" type="text/javascript" src="jquery/external/jquery.jqplot.js"></script>
<link rel="stylesheet" type="text/css" href="jquery/external/jquery.jqplot.css" />
<script type="text/javascript" src="jquery/external/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="jquery/external/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="jquery/external/plugins/jqplot.pointLabels.min.js"></script>
<script type='text/javascript'>
    var cnn = '<?php echo $conn_db; ?>';
    $(document).ready(function(){
        var params=[];

        var nTags = function(){
            var n=0;
            $.ajax({
                    async:false,
                    url: "autocomplete.php?ntags=1",
                    dataType: "json",
                    data : {
                        db: cnn
                    },
                    success: function (result) {
                        n = result;
                    }
                });
            return n;
        }
        $.ajax({
                url: "autocomplete.php?stags=1",
                dataType: "json",
                data : {
                    db: cnn,
                    params:params
                },
                success: function (result) {
                    var n = nTags();
                    __ploting(result,n);
                }
            });
            
        function __ploting(data,n){
            if(n==0){
                n=nTags();
            }
            var raiz = Math.sqrt(n);
            var width=parseFloat(n*125)/(raiz);
            $('#chartdiv').css('height',width);
            
            
            var s1 = Array();
            var s2 = Array();
            
            var maxIncidents = parseInt(data[0].total) ;
            var labels = [];
            
            for(var reg in data){
                //console.log(data[reg])
                s1.unshift([data[reg].total]);
                labels.unshift({label:data[reg].name});
                if (parseFloat(data[reg].total) > maxIncidents){
                    maxIncidents = parseInt(data[reg].total);
                }
            }
            //var s1 = [2, 6, 7, 10];
            //var s2 = [7, 5, 3, 4];
            //var s3 = [14, 9, 3, 8];
            if(typeof(plot)!='undefined'){
                $('#chartdiv').empty();
                plot=null;
            }
            plot = $.jqplot('chartdiv', s1, {
                // Tell the plot to stack the bars.
                stackSeries: false,
                captureRightClick: true,
                seriesDefaults:{
                    renderer:$.jqplot.BarRenderer,
                    rendererOptions: {
                        // Put a 30 pixel margin between bars.
                        barMargin: 30,
                        // Highlight bars when mouse button pressed.
                        // Disables default highlighting on mouse over.
                        highlightMouseDown: true,
                        barDirection: 'horizontal'
                    },
                    pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
                    shadowAngle: 135
                },
                axes: {
                    xaxis: {
                        min:0,
                        max:maxIncidents*(1.5)
                    },
                    yaxis: {
                        renderer: $.jqplot.CategoryAxisRenderer
                    }
                },
                legend: {
                    show: true,
                    location: 'e',
                    placement: 'outside'
                },
                axesDefaults:{
                    showTicks:false
                },
                series:labels
            });
            // Bind a listener to the "jqplotDataClick" event.  Here, simply change
            // the text of the info3 element to show what series and ponit were
            // clicked along with the data for that point.
            $('#chartdiv').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
                console.log('series: '+seriesIndex+', point: '+pointIndex+', data: '+data);
            });

            /*
            $("#reporthwmail").click(function(){
                toggle = !plot.series[0].show;
                plot.series[0].show=toggle;
                plot.axes.yaxis.render=$.jqplot.CategoryAxisRenderer;
                plot.replot(); 
            });*/
            //$('#chartdiv').height($('#chartdiv').parent().height() * 0.96);
            
            $(".jqplot-yaxis-tick").hide();
        }
        
        <?php //<editor-fold defaultstate="collapsed" desc="Treeview"> ?>
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
                                ids: '<?php echo $_strTags?>',
                                db: '<?php echo $conn_db; ?>'
                            },
                            success: function (result) {
                                //console.log(result);
                                return result;
                            }
                        }
                    },  
                    "plugins" : ["themes", "json_data", /*"html_data",*/ "checkbox", "sort", "ui"]
                })
                // 1) if using the UI plugin bind to select_node
                .bind("select_node.jstree", function (event, data) {
                    // `data.rslt.obj` is the jquery extended node that was clicked
                    //console.log(data.rslt.obj.attr("id"));
                })
                
                // 2) if not using the UI plugin - the Anchor tags work as expected
                //    so if the anchor has a HREF attirbute - the page will be changed
                //    you can actually prevent the default, etc (normal jquery usage)
                /*.delegate(".jstree-checkbox", "click", function (event, data) {
                    console.log(data.rslt.obj.attr("id"));
                    event.preventDefault();
                });*/
    
                //$.jstree._instance.prototype.check_node = function(node){
                /*.bind("change_state.jstree", "click", function (event, data) {
                    //console.log(data.rslt.attr('value'));
                    toggle = !plot.series[0].show;
                    plot.series[0].show=toggle;
                    plot.axes.yaxis.render=$.jqplot.CategoryAxisRenderer;
                    plot.replot(); 
                });*/
                
                
            <?php //</editor-fold> ?>
                
        $("#graph").click(function(){
            params=[];
            $("#treeview li").each(function(){
                if($(this).hasClass('jstree-checked')){
                    params.push($(this).attr('value'));
                }
            });
            $.ajax({
                url: "autocomplete.php?stags=1",
                dataType: "json",
                data : {
                    db: cnn,
                    params:params
                },
                success: function (result) {
                    __ploting(result, params.length);
                }
            });
        });
    });
</script>
<div style="text-align: right; width: 1100px;">
    <br>
    <!--
    <img id='reporthwpdf' alt="PDF" src="reportes/Acrobat.png">
    <img id='reporthwmail' alt="PDF" src="reportes/mail.png">
    -->
</div>
<div class='divTable tableReport' style="width: 1100px;">
    <br>              
    <div  class="divTr" style="width: 100%;">
        <div class="divTd">
            <table>
                <tr>
                    <td width='auto' valign='top'>
                        <div id="treeview"><!--AJAX--></div>
                        <div style="text-align: center;">
                            <br/>
                            <b id='graph' class="buttons3 pointer">Graficar</b>
                        </div>
                    </td>
                    <td width='auto'>
                        <div id="chartdiv" style="height:600px;width:500px; text-align: center; "></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <br>
</div>
<?php
}
/*
  echo "<pre>";
  print_r($_REQUEST);
  echo "</pre>";
 */
?>
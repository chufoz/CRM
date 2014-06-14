<?php
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This file does several things :)
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
 

extract($_REQUEST);


include("sqlfiles.php");
include("tmplesdev.php");
//include("funcictc.php");

if ($NoMenu) {
	$nonavbar=1;
	}

if ($active=="") {
	$active = "no";
}

//print "<pre>";
//print_r($_REQUEST);
//print "</pre>";

if ($_REQUEST['CheckCustomer']) {
			$nonavbar = 1;
			include("header.inc.php");
			print "<table cellspacing='0' cellpadding='4' border=1 bordercolor='#F0F0F0' width=90%>";
			$sql = "select id,custname from $GLOBALS[TBL_PREFIX]customer WHERE SOUNDEX('" . $_REQUEST['CheckCustomer'] . "') = SOUNDEX(custname)";
			//qlog($sql);
			$result = mcq($sql,$db);
			while ($row= mysql_fetch_array($result)) {
				$ins .= "<tr><td>" . $row['id'] . "</td><td>" . $row['custname'] . "</td></tr>";
			}
			$a = mysql_affected_rows();
			if ($a>0) {
				print "<tr><td colspan=2>CRM-CTT Thinks this customer already exists in your database.<br><br>The following similar customers were 	found:</td></tr>";
				print $ins;
			} else {
				print "<tr><td colspan=2>CRM-CTT doesn't think this customer already exists in your database.";
			}
			print "</table>";
			EndHTML();
			exit;
	}

if ($pdf) {
	include("pdf_inc2.php");
	include("config.inc.php");
	include("getset.php");
	include("language.php");

	// Security

	if (strtoupper($HideCustomerTab)=="YES" && GetClearanceLevel($GLOBALS[USERID])<>"administrator") {
		print "<img src='error.gif'>&nbsp;Access denied";
		exit;
	} elseif (GetClearanceLevel($GLOBALS['USERID'])<>"rw" && GetClearanceLevel($GLOBALS['USERID'])<>"administrator") {
		print "<img src='error.gif'>&nbsp;Access denied";
		exit;
	}

	

	$date = date("F j, Y, H:i") . "h";
	$pdftitle2 = $pdftitle;
	$pdftitle = "$title $CRM_VERSION. Report created $date";
	
	$park = $CTG;

	if ($_GET['print']) {
		$NoImageInclude=1;
		StartPrintPDF();
	} else {
		StartPDF();	
	}

	qlog("SID: " . $_REQUEST['stashid']);

	$pdf->Cell(0,0,$pdftitle2,0,1);
	$pdf->SetFont('Arial','',14);
	$pdf->Cell(0,10,$lang[customers],0,1);

	$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]customer ORDER BY custname";
	$result= mcq($sql,$db);
	$maxU1 = mysql_fetch_array($result);
	$maxcust = $maxU1[0];

	if ($maxcust==0) {
		print "<html><body><table><tr><td><img src='crm.gif'><br><img src='error.gif'>&nbsp;<b><font size='+1' face='Trebuchet MS'>You cannot export an empty database!</font></b><br>&nbsp;&nbsp;&nbsp;<font size='+1' face='Trebuchet MS'>This error is fatal.</font><br><br>";
		print "<font face='Trebuchet MS'>Click <a href='javascript:history.back(-1);'>here</a> to go back...</font></td></tr>\n</table>";
	EndHTML();
		exit;
	}
	$a = array();

	$cdate = date('Y-m-d');
	
	if ($_REQUEST['stashid']) {
				$sql = PopStashValue($_REQUEST['stashid']);
				qlog("Query fetched from database: " . $sql);

	} else {
				qlog("ERROR. NO STASHID FOUND. Exporting ALL.");
				$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]customer ORDER BY custname";
	}
	
	qlog("Executing: " . $sql);

	$result_customer= mcq($sql,$db);
	while ($pb= mysql_fetch_array($result_customer)) {

			$auth = CheckCustomerAccess($pb['id']);
			if ($auth == "ok" || $auth == "readonly") {

				if ($fst) {
						$pdf->AddPage();
				} else {
						$fst = 1;
				}
				
				$pb[custname]		= fillout($pb[custname],30);
				$pb[contact]		= fillout($pb[contact],15);
				$pb[contact_phone]	= fillout($pb[contact_phone],11);
				$pb[cust_homepage]	= fillout($pb[cust_homepage],20);
				$pb[cust_address]	= fillout($pb[cust_address],20);
				$pb[cust_remarks]	= fillout($pb[cust_remarks],7);
				$pb[contact_email]	= fillout($pb[contact_email],20);
				
				$pdf->Bookmark($pb[custname]);
				$pdf->SetFont('Arial','',10);
				$pdf->SetFillColor(0,0,128);
				$pdf->SetTextColor(255);
				$pdf->Cell(0,4,$lang[customer] . " : " .          $pb[custname],1,1,'L',1);
	//			$pdf->Cell(0,6,($list[$po]),1,1,'L',1);
				$pdf->SetFont('Arial','',8);
				$pdf->SetFillColor(0,0,0);
				$pdf->SetTextColor(128,0,0);
				$pdf->Cell(0,4,$lang[contact] . " : ",0,1);
				$pdf->SetTextColor(0);
				$pdf->Cell(0,4,$pb[contact],0,1);
				$pdf->SetTextColor(128,0,0);
				$pdf->Cell(0,4,$lang[contacttitle]. " : ",0,1);
				$pdf->SetTextColor(0);
				$pdf->Cell(0,4,$pb[contact_title],0,1);
				$pdf->SetTextColor(128,0,0);
				$pdf->Cell(0,4,$lang[contactphone] . " : ",0,1);
				$pdf->SetTextColor(0);
				$pdf->Cell(0,4,$pb[contact_phone],0,1);
				$pdf->SetTextColor(128,0,0);
				$pdf->Cell(0,4,$lang[contactemail] . " : ",0,1);
				$pdf->SetTextColor(0);
				$pdf->Cell(0,4,$pb[contact_email],0,1);
				$pdf->SetTextColor(128,0,0);
				$pdf->Cell(0,4,$lang[customeraddress] . " : ",0,1);
				$pdf->SetTextColor(0);
				$n = explode("\n",$pb[cust_address]);
				for ($n1=0;$n1<sizeof($n);$n1++) {
					$nt = wordwrap($n[$n1], 120, "HOPS!", 1);
					$nta = explode("HOPS!",$nt);
					for ($i=0;$i<sizeof($nta);$i++) {
						$pdf->Cell(0,4,trim($nta[$i]),0,1);
					}
				}
				//line();
				$pdf->SetTextColor(128,0,0);
				$pdf->Cell(0,4,$lang[custremarks] . " : ",0,1);
				$pdf->SetTextColor(0);
				$n = explode("\n",$pb[cust_remarks]);
				for ($n1=0;$n1<sizeof($n);$n1++) {
					$nt = wordwrap($n[$n1], 120, "HOPS!", 1);
					$nta = explode("HOPS!",$nt);
					for ($i=0;$i<sizeof($nta);$i++) {
						$pdf->Cell(0,4,trim($nta[$i]),0,1);
					}
				}
				//line();
				$pdf->SetTextColor(128,0,0);
				$pdf->Cell(0,4,$lang[custhomepage] . " : ",0,1);
				$pdf->SetTextColor(0);
				$pdf->Cell(0,4,$pb[cust_homepage],0,1);

				// Extra fields list		

			$list = GetExtraCustomerFields();

			$sql110 = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='$pb[id]' AND type='cust'";
			$result1 = mcq($sql110,$db);
			$num = mysql_fetch_array($result1);

			if (sizeof($list)>1) {
				$pdf->Ln(6);
	//			line();	
				$pdf->SetFont('Arial','',8);
				$data = array();
				//$header=array("Field","Value");

				foreach ($list AS $field) {
						

						$sql0 = "SELECT value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='$pb[id]' AND deleted<>'y' AND name='" . $field['id'] . "' AND type='cust' ORDER BY name";
	//					print $sql;

						$result8 = mcq($sql0,$db);
						$cust= mysql_fetch_array($result8);
						$val = $cust[value];
						
						//$list[$po] = CleanExtraFieldName($list[$po]);
						
						$val = $cust[value];
						$val = FunkifyLOV($val);

						if ($val=="xfdfg") {
							// do nothing
						} else {
							line();	
							$pdf->SetFont('Arial','',8);
							$pdf->SetFillColor(255,255,255);
							$pdf->SetTextColor(128,0,0);
							$pdf->Cell(0,6,($field['name']),0,1,'L',1);

							$pdf->SetFont('Arial','',8);
							$pdf->SetFillColor(0,0,0);
							$pdf->SetTextColor(0);
							//Have to convert custom fields to multiline

							$n = explode("\n",$val);

							

							for ($n1=0;$n1<sizeof($n);$n1++) {
								$nt = wordwrap($n[$n1], 120, "HOPS!", 1);
								$nta = explode("HOPS!",$nt);
								for ($i=0;$i<sizeof($nta);$i++) {
									$pdf->Cell(0,4,trim($nta[$i]),0,1);
								}
							}
						}

				}
				//$pdf->FancyTable2col($header,$data);
				unset($header);
				unset($data);
				unset($num);
				unset($list);
			}
			//*************************************************************
			//----------------Datos generales
			$datos=mcq("SELECT * FROM CRMdetgen WHERE dg_idc=".$pb['id']."",$db);
			if (mysql_num_rows($datos)>0){
			$pdf->AddPage();
			$data=mysql_fetch_array($datos);
			$pdf->ln(5);
			$pdf->SetFont('Arial','B',12);
			$pdf->SetTextColor(0,0,128);
			$pdf->Cell(0,4,"INFORMACION GENERAL",0,1);
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Generales:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(40,4,"Numero de estacion : ",0,0);
			$pdf->Cell(0,4,$data['dg_numest'],0,1);
			$pdf->Cell(40,4,"Nombre : ",0,0);
			$pdf->Cell(0,4,$data['dg_nombre'],0,1);
			$pdf->Cell(40,4,"Nombre de estacion: ",0,0);
			$pdf->Cell(0,4,$data['dg_nomest'],0,1);
			$pdf->Cell(40,4,"Direccion : ",0,0);
			$pdf->Cell(0,4,$data['dg_direccion'],0,1);
			$pdf->Cell(40,4,"Telefono : ",0,0);
			$pdf->Cell(0,4,$data['dg_telefono'],0,1);
			$pdf->Cell(40,4,"Contacto : ",0,0);
			$pdf->Cell(0,4,$data['dg_contacto'],0,1);
            $pdf->Cell(40,4,"Puerto HTTP : ",0,0);
            $pdf->Cell(0,4,$data['dg_puerto_http'],0,1);
            $pdf->Cell(40,4,"Puerto SSH : ",0,0);
            $pdf->Cell(0,4,$data['dg_puerto_ssh'],0,1);
			
			$pdf->ln(5);
			$pdf->SetTextColor(128,0,0);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(0,4,"Host Name:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFont('Arial','',8);
			$pdf->ln(2);
			$pdf->Cell(40,4,"Interno : ",0,0);
			$pdf->Cell(0,4,$data['dg_host_int'],0,1);
			$pdf->Cell(40,4,"Externo : ",0,0);
			$pdf->Cell(0,4,$data['dg_host_ext'],0,1);
			
			$pdf->ln(5);
			$pdf->SetTextColor(128,0,0);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(0,4,"Conexion a Internet:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(40,4,"Modo : ",0,0);

			if ($data['dg_modo_cnx']==0){
			$modo="NA";
			}
			elseif($data['dg_modo_cnx']==1){
			$modo="Maestro";
			}
			elseif($data['dg_modo_cnx']==2){
			$modo="Esclavo";
			}
			$pdf->Cell(0,4,$modo,0,1);
			$pdf->SetFont('Arial','BU',8);
			$pdf->Cell(40,4,"Router",0,1);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(40,4,"Marca : ",0,0);
			$pdf->Cell(0,4,$data['dg_rout_marca'],0,1);
			$pdf->Cell(40,4,"Modelo : ",0,0);
			$pdf->Cell(0,4,$data['dg_rout_mod'],0,1);
			$pdf->Cell(40,4,"IP Address : ",0,0);
			$pdf->Cell(0,4,$data['dg_rout_ip'],0,1);
			$pdf->SetFont('Arial','BU',8);
			$pdf->Cell(40,4,"Puertos Abiertos",0,1);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"Interno",0,0);
			$pdf->Cell(40,4,"Externo",0,0);
			$pdf->Cell(40,4,"Nombre",0,1);
			$pdf->SetFont('Arial','',8);
			$puertos=unserialize($data['dg_puertos']);
			foreach($puertos as $value){
				$puerto= explode("|",$value);
				$pdf->Cell(40,4,$puerto[0],0,0);
				$pdf->Cell(40,4,$puerto[1],0,0);
				$pdf->Cell(40,4,$puerto[2],0,1);
			}
			
			}
			//----------------------------Datos de programas
			$datos=mcq("SELECT * FROM CRMdetprog WHERE dp_idc=".$pb['id']."",$db);
			if (mysql_num_rows($datos)>0){
			$pdf->AddPage();
			$data=mysql_fetch_array($datos);
			$pdf->ln(5);
			$pdf->SetFont('Arial','B',12);
			$pdf->SetTextColor(0,0,128);
			$pdf->Cell(0,4,"INFORMACION DE PROGRAMAS",0,1);
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Software:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"Programa",0,0);
			$pdf->Cell(40,4,"Version",0,0);
			$pdf->Cell(40,4,"Fecha de Actualizacion",0,1);
			$pdf->SetFont('Arial','',8);
			$prog=unserialize($data['dp_soft']);
			foreach($prog as $value){
				$ln= explode("|",$value);
				$soft=mysql_result(mcq("SELECT soft_nombre FROM CRMsoftware WHERE soft_id=$ln[0]",$db),0,0);
				$ver=mysql_result(mcq("SELECT CONCAT(vers_ver,'-',vers_rev) FROM CRMversiones WHERE vers_id=$ln[1]",$db),0,0);
				$pdf->Cell(40,4,$soft,0,0);
				$pdf->Cell(40,4,$ver,0,0);
				$pdf->Cell(40,4,$ln[2],0,1);
			}
			$pdf->ln(5);
			$pdf->SetTextColor(128,0,0);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(0,4,"Base de Datos:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"",0,0);
			$pdf->Cell(40,4,"Version",0,0);
			$pdf->Cell(40,4,"Fecha de Actualizacion",0,1);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(40,4,"SMAX",0,0);
			$pdf->Cell(40,4,$data['dp_vsmax'],0,0);
			$pdf->Cell(40,4,$data['dp_fsmax'],0,1);
			$pdf->Cell(40,4,"SCLIE",0,0);
			$pdf->Cell(40,4,$data['dp_vsclie'],0,0);
			$pdf->Cell(40,4,$data['dp_fsclie'],0,1);
			$pdf->Cell(40,4,"CONVOL",0,0);
			$pdf->Cell(40,4,$data['dp_vconvol'],0,0);
			$pdf->Cell(40,4,$data['dp_fconvol'],0,1);
			}
			//----------------------------Datos del servidor
			$datos=mcq("SELECT * FROM CRMdetserv WHERE ds_idc=".$pb['id']."",$db);
			if (mysql_num_rows($datos)>0){
			$pdf->AddPage();
			$data=mysql_fetch_array($datos);
			$pdf->ln(5);
			$pdf->SetFont('Arial','B',12);
			$pdf->SetTextColor(0,0,128);
			$pdf->Cell(0,4,"INFORMACION DEL SERVIDOR",0,1);
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Sistema Operativo:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"S.O.",0,0);
			$pdf->Cell(40,4,"Version",0,1);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(40,4,$data['ds_so'],0,0);
			$pdf->Cell(40,4,$data['ds_so_vers'],0,1);
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Tarjeta Madre:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"Marca",0,0);
			$pdf->Cell(40,4,"Modelo",0,1);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(40,4,$data['ds_mb_marca'],0,0);
			$pdf->Cell(40,4,$data['ds_mb_mod'],0,1);
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Procesador:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"Marca",0,0);
			$pdf->Cell(40,4,"Modelo",0,1);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(40,4,$data['ds_pr_marca'],0,0);
			$pdf->Cell(40,4,$data['ds_pr_mod'],0,1);
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Memoria RAM:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"Marca",0,0);
			$pdf->Cell(40,4,"Modelo",0,0);
			$pdf->Cell(40,4,"Capacidad",0,1);
			$pdf->SetFont('Arial','',8);
			$ram=unserialize($data['ds_ram']);
			foreach($ram as $value){
				$ln= explode("|",$value);
				$pdf->Cell(40,4,$ln[0],0,0);
				$pdf->Cell(40,4,$ln[1],0,0);
				$pdf->Cell(40,4,$ln[2],0,1);
			}
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Discos(s) Duro(s):",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"Tipo",0,0);
			$pdf->Cell(40,4,"Marca",0,0);
			$pdf->Cell(40,4,"Modelo",0,0);
			$pdf->Cell(40,4,"Capacidad",0,1);
			$pdf->SetFont('Arial','',8);
			$hd=unserialize($data['ds_hd']);
			foreach($hd as $value){
				$ln= explode("|",$value);
				$pdf->Cell(40,4,$ln[0],0,0);
				$pdf->Cell(40,4,$ln[1],0,0);
				$pdf->Cell(40,4,$ln[2],0,0);
				$pdf->Cell(40,4,$ln[3],0,1);
			}
			$pdf->ln(2);
			$pdf->Cell(40,4,"Arreglo RAID : ",0,0);
			$pdf->Cell(0,4,$data['ds_raid'],0,1);
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Tarjetas de Red:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"Marca",0,0);
			$pdf->Cell(40,4,"Modelo",0,0);
			$pdf->Cell(40,4,"IP Address",0,0);
			$pdf->Cell(40,4,"MAC Address",0,1);
			$pdf->SetFont('Arial','',8);
			$eth=unserialize($data['ds_eth']);
			foreach($eth as $value){
				$ln= explode("|",$value);
				$pdf->Cell(40,4,$ln[0],0,0);
				$pdf->Cell(40,4,$ln[1],0,0);
				$pdf->Cell(40,4,$ln[2],0,0);
				$pdf->Cell(40,4,$ln[3],0,1);
			}
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Floppy:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"Marca",0,0);
			$pdf->Cell(40,4,"Modelo",0,1);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(40,4,$data['ds_fd_marca'],0,0);
			$pdf->Cell(40,4,$data['ds_fd_mod'],0,1);
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Unidades de Almacenamiento:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"Tipo",0,0);
			$pdf->Cell(40,4,"Marca",0,0);
			$pdf->Cell(40,4,"Modelo",0,1);
			$pdf->SetFont('Arial','',8);
			$uni=unserialize($data['ds_unidades']);
			foreach($uni as $value){
				$ln= explode("|",$value);
				$pdf->Cell(40,4,$ln[0],0,0);
				$pdf->Cell(40,4,$ln[1],0,0);
				$pdf->Cell(40,4,$ln[2],0,1);
			}
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Configuracion de Particiones:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"Particion",0,0);
			$pdf->Cell(40,4,"Montaje",0,0);
			$pdf->Cell(40,4,"Tama�o",0,1);
			$pdf->SetFont('Arial','',8);
			$part=unserialize($data['ds_particiones']);
			foreach($part as $value){
				$ln= explode("|",$value);
				$pdf->Cell(40,4,$ln[0],0,0);
				$pdf->Cell(40,4,$ln[1],0,0);
				$pdf->Cell(40,4,$ln[2],0,1);
			}
			}
			//----------------------------Datos de dispositivos
			$datos=mcq("SELECT * FROM CRMdetdisp WHERE dd_idc=".$pb['id']."",$db);
			if (mysql_num_rows($datos)>0){
			$pdf->AddPage();
			$data=mysql_fetch_array($datos);
			$pdf->ln(5);
			$pdf->SetFont('Arial','B',12);
			$pdf->SetTextColor(0,0,128);
			$pdf->Cell(0,4,"INFORMACION DE DISPOSITIVOS",0,1);
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Dispensarios:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(15,4,"# Disp",0,0);
			$pdf->Cell(30,4,"MAC Address",0,0);
			$pdf->Cell(30,4,"IP Asignada",0,0);
			$pdf->Cell(30,4,"Capacidad D.O.C.",0,0);
			$pdf->Cell(30,4,"Modo de Operacion",0,0);
			$pdf->Cell(25,4,"Version Draco",0,0);
			$pdf->Cell(25,4,"Version Gemini",0,1);
			$pdf->SetFont('Arial','',8);
			$disp=unserialize($data['dd_disp']);
			foreach($disp as $value){
				$ln= explode("|",$value);
				if ($ln[4]=='0'){
				$mod_op="Terminal";
				}
				elseif($ln[4]=='1'){
				$mod_op="Autonomo";
				}
				$pdf->Cell(15,4,$ln[0],0,0);
				$pdf->Cell(30,4,$ln[1],0,0);
				$pdf->Cell(30,4,$ln[2],0,0);
				$pdf->Cell(30,4,$ln[3],0,0);
				$pdf->Cell(30,4,$mod_op,0,0);
				$pdf->Cell(25,4,$ln[5],0,0);
				$pdf->Cell(25,4,$ln[6],0,1);
			}
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Ultramax:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"Sistema Operativo",0,0);
			$pdf->Cell(40,4,"IP Address",0,0);
			$pdf->Cell(40,4,"Version",0,1);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(40,4,$data['dd_umax_so'],0,0);
			$pdf->Cell(40,4,$data['dd_umax_ip'],0,0);
			$pdf->Cell(40,4,$data['dd_umax_ver'],0,1);
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Sistema de Telemedicion:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(40,4,"Marca",0,0);
			$pdf->Cell(40,4,"Modelo",0,1);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(40,4,$data['dd_stm_marca'],0,0);
			$pdf->Cell(40,4,$data['dd_stm_mod'],0,1);
			$pdf->ln(5);
			$pdf->SetFont('Arial','',10);
			$pdf->SetTextColor(128,0,0);
			$pdf->Cell(0,4,"Tanques:",0,1);
			$pdf->SetTextColor(0,0,0);
			$pdf->ln(2);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(25,4,"# Tanque",0,0);
			$pdf->Cell(25,4,"Producto",0,0);
			$pdf->Cell(25,4,"Cap. Total",0,0);
			$pdf->Cell(25,4,"Cap. Operativa",0,0);
			$pdf->Cell(25,4,"Cap. Util",0,0);
			$pdf->Cell(25,4,"Cap. Fondaje",0,0);
			$pdf->Cell(25,4,"Volumen Minimo",0,1);
			$pdf->SetFont('Arial','',8);
			$tqs=unserialize($data['dd_tqs']);
			foreach($tqs as $value){
				$ln= explode("|",$value);
				if ($ln[1]=='1'){
				$prod="Diesel";
				}
				elseif($ln[1]=='2'){
				$prod="Magna";
				}
				elseif($ln[1]=='3'){
				$prod="Premium";
				}
				$pdf->Cell(25,4,$ln[0],0,0);
				$pdf->Cell(25,4,$prod,0,0);
				$pdf->Cell(25,4,$ln[2],0,0);
				$pdf->Cell(25,4,$ln[3],0,0);
				$pdf->Cell(25,4,$ln[4],0,0);
				$pdf->Cell(25,4,$ln[5],0,0);
				$pdf->Cell(25,4,$ln[6],0,1);
			}

			}

			
			
		}
			
		
		$pdf->Ln(8);
	
			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='$pb[id]' AND $GLOBALS[TBL_PREFIX]binfiles.type='cust'";
			$result = mcq($sql,$db);
			$num = mysql_fetch_array($result);
//			qlog("Number of files attached to this customer: " . $num[0]);
			if ($num[0]>0) {
				line();
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(0,10,$lang['customer'] . " files:",0,1);
				$pdf->SetFont('Arial','',8);
				$data = array();
				$header=array("Creation date",'Size','User','Filename');

				if ($DateFormat=="mm-dd-yyyy") {
					$sql= "SELECT filename,creation_date,filesize,fileid,username,date_format(creation_date, '%m-%d-%Y %H:%i') AS dt FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='$pb[id]' AND type='cust' ORDER BY filename";
				} else {
					$sql= "SELECT filename,creation_date,filesize,fileid,username,date_format(creation_date, '%d-%m-%Y %H:%i') AS dt FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='$pb[id]' AND type='cust' ORDER BY filename";
				}
				$result7= mcq($sql,$db);
				while ($files= mysql_fetch_array($result7)) {

					$ownert = $files[username];

					$url = "http://" . $_SERVER['SERVER_NAME'] . $subdir . "csv.php?fileid=" . $files['fileid'];
					array_push($data,array($files['dt'],round(($files[filesize]/1024)). "K",$ownert,$url,$files['filename']));
					$ftel++;
					$tel++;
				}
					$pdf->FancyTable4colSinglePDF($header,$data);
			
				
			}
	}
	//$pdf->Cell(0,4,$lang[endofpbexport],0,1);
	if ($_REQUEST['to_file']) {
		$pdf->Output($_REQUEST['to_file']);
	} else {
		$pdf->Output();
	}

	log_msg("Customer PDF export downloaded","");
	exit;
} elseif ($ActivityCustomerGraph) {
	include("config.inc.php");
	include("getset.php");	

	DisplayCustomerActivityGraph($ActivityCustomerGraph);
	exit;
} elseif ($_REQUEST['mm'] && $_REQUEST['stashid']) {
	$nonavbar = 1;
	include("header.inc.php");
	print "<table><tr><td><form name='SingleReport' method='POST' target='document.window.opener'>";
	print "<table>";
	print "<tr><td><b>E-mail merge</b><br><br></td></tr>\n";
	print "<tr><td>" . $lang['rtftemplate'] . ":</td><td><select style='width:250' name='template'>";
	$sql = "SELECT fileid,filename,creation_date,username FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='0' AND filetype='TEMPLATE_HTML'";
	$result = mcq($sql,$db);
	while ($row = mysql_fetch_array($result)) {
		if ($_REQUEST['template']==$row['fileid']) {
			$ins = "SELECTED";
		} else {
			unset($ins);
		}
		print "<option $ins value = '" . $row['fileid'] ."'>" . $row['filename'] . "</option>";
	}
	print "</select></td></tr>\n";

	$list = GetExtraCustomerFields();

	$opt =  "<tr><td>Field:</td><td>";
	$opt .=  "<select name='mm_field'>";
	$opt .=  "<option value='std'>" . $lang['contactemail'] . "</option>";
	foreach ($list AS $field) {
		if ($field['fieldtype']=="mail") {
				$opt .=  "<option value='EFID" . $field['id'] . "'>" . $field['name'] . "</option>";
				$a++;
		}
	}
	$opt .=  "</select></td></tr>\n";
	
	if ($a) {
		print $opt;
	}

	print "<tr><td>" . $lang['attachindividualtocustomer'] . ":</td><td><select style='width:250' name='attach_to_dossier'><option value='Yes'>$lang[yes]</option><option SELECTED value='No'>$lang[no]</option></select></td></tr>\n";

	print "<tr><td><input type='hidden' name='mm_field'><input type='button' name='submitknop' value='$lang[go]' OnClick='doso();'></form></td></tr>\n</table>";

		?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		<!--
		function doso() {
			
			window.opener.document.location="customers.php?mm2=1&template=" + document.SingleReport.template.value + "&attach_to_dossier=" + document.SingleReport.attach_to_dossier.value + "&mm_field=" + document.SingleReport.mm_field.value + "&stashid=<? echo $_REQUEST['stashid'];?>";

			window.close();

		}
		//-->
		</SCRIPT>
		<?php
		EndHTML();
		exit;
} elseif ($_REQUEST['mm3'] && $_REQUEST['stashid'] && $_REQUEST['template'] && $_REQUEST['subject'] && $_REQUEST['data']) {
	include("header.inc.php");
	$sql = PopStashValue($_REQUEST['stashid']);
	$result = mcq($sql,$db);
	if ($_REQUEST['attach_to_dossier']=="np") {
		$atd = false;
	} else {
		$atd = true;
	}
	$filename = "CRM-CTT-E-mailmerge-" . date("Fj-Y-Hi") . "h.HTML";
	while ($row = mysql_fetch_array($result)) {
		
		if ($_REQUEST['mm_field']<>"std" && $_REQUEST['mm_field']<>"") {
			$query = "SELECT value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE name='" . str_replace("EFID","", $_REQUEST['mm_field']) . "' AND type='cust' AND eid='" . $row['id'] . "'";
			$result2 = mcq($query,$db);
			$rowtje = mysql_fetch_array($result2);
			$email_to = $rowtje['value'];
		} else {
			$email_to = $row['contact_email'];
		}
		if ($email_to) {
			$msg .= RealMail($_REQUEST['data'],"",$row['id'],"","",$email_to,0,$_REQUEST['subject'],$atd,$filename);
		}
	}
	print "<pre>";
	print $msg;
	print "</pre>";

	EndHTML();
	exit;
} elseif ($_REQUEST['mm2'] && $_REQUEST['stashid'] && $_REQUEST['template']) {
	include("header.inc.php");

	$sql = "SELECT content,file_subject FROM $GLOBALS[TBL_PREFIX]binfiles,$GLOBALS[TBL_PREFIX]blobs WHERE $GLOBALS[TBL_PREFIX]binfiles.fileid=$GLOBALS[TBL_PREFIX]blobs.fileid AND $GLOBALS[TBL_PREFIX]binfiles.fileid='" . $_REQUEST['template']  . "' AND LEFT(filetype,8)='TEMPLATE'";
	//print $sql;
	$result = mcq($sql,$db);
	$row = mysql_fetch_array($result);
	print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;E-mail merge </legend><table><tr><td>";
	print "<b>To:</b> ";
	$sql = PopStashValue($_REQUEST['stashid']);
	$result = mcq($sql,$db);
	while ($row2 = mysql_fetch_array($result)) {
		$to .= $row2['contact'] . ",";
	}
	print $to . "<br>";
	print "<form name='editHTMLtemplateform' METHOD='POST'>";
	print "<input type='hidden' name='mm_field' value='" . $_REQUEST['mm_field'] . "'>";
	print "<input type='hidden' name='mm3' value='1'>";
	print "<input type='hidden' name='stashid' value='" . $_REQUEST['stashid'] . "'>";
	print "<input type='hidden' name='attach_to_dossier' value='" . $_REQUEST['attach_to_dosier'] . "'>";
	print "<b> Subject: </b><input type='text' size=70 name='subject' value='" . $row['file_subject'] . "'><br><br>";

	//print "<input type='hidden' name='data' value='" .  $row['content'] . "'>";
	include("fckeditor/fckeditor.php");

	$oFCKeditor = new FCKeditor('data') ;
	$oFCKeditor->BasePath	= "fckeditor/" ;
	//$oFCKeditor->Config['SkinPath'] = 'fckeditor/editor/skins/silver/' ;
	$oFCKeditor->Width = "100%";
	$oFCKeditor->Height = "400";
	$oFCKeditor->ToolbarSet = 'CRMUSER';

	$oFCKeditor->Value		= $row['content'];
	$oFCKeditor->Create() ;

	
	print "<br><input type='submit' value='$lang[go]'>";
	print "</form>";
	print "</table></fieldset>";
	EndHTML();
	exit;

} else {
	include("header.inc.php");
	$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]customer";
	$result= mcq($sql,$db);
	$maxcust= mysql_fetch_array($result);
	$maxcust=$maxcust[0];

	$list = GetExtraCustomerFields();

	foreach ($list as $field) {
	$element = "EFID" . $field['id'];
	if ($_GET[$element]) {
		// OK an extra field named $item was found in the search query

		$cust_insert = " ($GLOBALS[TBL_PREFIX]customaddons.name='" . $element . "' AND $GLOBALS[TBL_PREFIX]customaddons.value='" . $_GET[$element] . "' AND $GLOBALS[TBL_PREFIX]customaddons.type='cust')";
		$efi = 1;
	} else {
		//print "$b64_item not found ($item)<br>";
	}
	}
//	trigger_error("Cannot divide by zero", E_USER_ERROR);
//	print_r(debug_backtrace());
//debug_print_backtrace();
	

	if (strtoupper($HideCustomerTab)=="YES" && GetClearanceLevel($GLOBALS[USERID])<>"administrator") {
		print "<img src='error.gif'>&nbsp;Access denied";
		exit;
	}

	$ttab = $_REQUEST['tab'] != '' ? "&tab=".$_REQUEST['tab'] : '';

	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	function poppb(){
		newWindow = window.open('customers.php?nonavbar=$nonavbar&nonavbar=1', 'myWindow2','width=800,height=200,directories=0,status=1,menuBar=0,scrollBars=1,resizable=1');
		newWindow.focus();
	}

		
	function gobla(i) {
		document.location="customers.php?det=1"+"<?php echo $ttab;?>"+"&c_id=" + i + "&<? echo $epoch;?>";
	}
	//-->
	</SCRIPT>
	<?php

	if ($export) {
			if (strtoupper($BlockAllCSVDownloads)=="YES") {
					MustBeAdminUser();
					qlog("Access denied");
			} else {
					qlog("Access granted");
			}


			$store = "$file";
				if ($query) {
					$ins = "<br>" . $lang['alreadyselected'];
				}
			print "<table><tr><td>";
			print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Export " . strtolower($lang['customers']) . "&nbsp;</legend><table>";
			print "<tr><td colspan=12>";
		//	print $query;
			print "<img src='arrow.gif'>&nbsp;<a href=\"javascript:window.opener.location='csv.php?password=$password&exportcust=1&sep=RealExcel&close=1&stashid=" . $stashid . "';window.close();\" class='bigsort'> Export " . strtolower($lang[customers]) . " in Microsoft&reg; Excel&reg; format <img src='excel.gif' border='0'></a></td></tr>\n";
			print "<tr><td colspan=12><img src='arrow.gif'>&nbsp;<a href=\"javascript:window.opener.location='csv.php?password=$password&exportcust=1&nle=1&sep=;&close=1&stashid=" . $stashid . "';window.close();\"'' class='bigsort'> Export " . strtolower($lang[customers]) . " " . strtolower($lang[scqp]) . "</a></td></tr>\n";
			print "<tr><td colspan=12><img src='arrow.gif'>&nbsp;<a href=\"javascript:window.opener.location='csv.php?password=$password&exportcust=1&nle=1&sep=:&close=1&stashid=" . $stashid . "';window.close();\"'' class='bigsort'> Export " . strtolower($lang[customers]) . " " . strtolower($lang[cqp]) . "</a></td></tr>\n";
			print "<tr><td colspan=12><img src='arrow.gif'>&nbsp;<a href=\"javascript:window.opener.location='csv.php?password=$password&exportcust=1&nle=1&sep=,&close=1&stashid=" . $stashid . "';window.close();\"'' class='bigsort'> Export " . strtolower($lang[customers]) . " " . strtolower($lang[kqp]) . "</a></td></tr>\n";
			print "<tr><td colspan=12><img src='arrow.gif'>&nbsp;<a href=\"javascript:popPDFwindow('customers.php?pdf=1&close=1&stashid=" . $stashid . "');window.close();\"' class='bigsort'> Export " . strtolower($lang[customers]) . " in PDF format</a><img src='pdf.gif' height='12' width='12'><br>" . $ins . "</td></tr>\n";
			print "</table></fieldset></td></tr>\n</table>";
			
			EndHTML();
			exit;
	}


	if ($_REQUEST['addfilled'] || $_REQUEST['editfilled']) {
		
		if ($_REQUEST['addfilled']) {
	
			$a = GetClearanceLevel($GLOBALS[USERID]);
			if ($a<>"rw" && $a<>"administrator") {
						print "<img src='error.gif'>&nbsp;Access denied";
						exit;
			}
			


			extract($_REQUEST);
			
			$sql="INSERT INTO $GLOBALS[TBL_PREFIX]customer(contact,custname,contact_title,contact_phone,cust_homepage,cust_address,cust_remarks,contact_email,active,email_owner_upon_adds,customer_owner,readonly,rfc,cust_notes) VALUES('" . mysql_real_escape_string($contactnew)  . "','" . mysql_real_escape_string($custnamenew) . "','" . mysql_real_escape_string($contact_titlenew) . "','" . mysql_real_escape_string($contact_phonenew) . "','" . mysql_real_escape_string($cust_homepagenew) . "','" . mysql_real_escape_string($cust_addressnew) . "','" . mysql_real_escape_string($cust_remarksnew) . "','" . mysql_real_escape_string($contact_emailnew) . "','yes','" . mysql_real_escape_string($email_owner_upon_adds) . "','" . mysql_real_escape_string($customer_ownernew) . "','" . mysql_real_escape_string($readonlycust) . "','" . mysql_real_escape_string(strtoupper($rfcnew)) . "','" . mysql_real_escape_string(strtoupper($cust_notesnew)) . "')";
			
			mcq($sql,$db);
			$eid = mysql_insert_id ();

			journal($eid,"Customer added\n\n$contactnew - $custnamenew - $contact_titlenew - $contact_phonenew - $cust_homepagenew - $cust_addressnew - $cust_remarksnew - $contact_emailnew", "customer");
			
			$add=1;
			log_msg("Customer $customernew added","");
				// First, collect extra fields list

			$list = GetExtraCustomerFields();
			
			// Then, check the existence of variables named $$list[$x]

			foreach ($list AS $extrafield) {
				$varname = "EFID" . $extrafield['id'];
				if ($$varname) {
					//print "FOUND $$varname $varname";
						
						$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='$eid' AND name='" . $extrafield['id'] ."' AND type='cust'";
						$result = mcq($sql,$db);
						$gh = mysql_fetch_array($result);
						$value = $gh[0];
						if (is_array($_REQUEST[$varname])) {
							qlog("WARNING - THIS IS AN EXTRA ARRAY FIELD!");
							$tmp = array();
							foreach($_REQUEST[$varname] AS $row) {
								if ($row <> "") {
									array_push($tmp, base64_encode($row));
								}
							}
							$$varname = serialize($tmp);
						}

						if (mysql_affected_rows()>0) {
								if ($value <> $$varname) { 
									$sql = "UPDATE $GLOBALS[TBL_PREFIX]customaddons SET value = '" . mysql_real_escape_string($$varname) . "',usr='" . $name . "'" . $sqlins . " WHERE eid='" . $eid . "' AND name='" . $extrafield['id'] . "' AND type='cust'";
									ProcessTriggers("EFID" . $extrafield['id'],$eid,$$tmp);
									$add_to_journal .= "\n" . CleanExtraFieldName($extrafield['name']) . " updated from $value to " . $$tmp . "";
								}
						} else {

								if ($extrafield['storetype'] <> "default") {
									$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]customaddons(eid, name, value, usr, type) VALUES('" . $eid . "','" . $extrafield['id'] . "','" . mysql_real_escape_string($$varname) . "','" . $name . "','cust')";
								} else {
									$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]customaddons(eid, name, value, usr, type) VALUES('" . $eid . "','" . $extrafield['id'] . "','" . mysql_real_escape_string($$varname) . "','" . $name . "','cust')";
								}
						
								ProcessTriggers("EFID" . $extrafield['id'],$eid,mysql_real_escape_string($$tmp));
								$add_to_journal .= "\n" . CleanExtraFieldName($normal) . " updated from <nothing> to " . mysql_real_escape_string($$tmp) . "";
						}

						// And finally, execute the statement.
						//print $sql . "<br><br>";

						mcq($sql,$db);
				}
			
			}
			journal($eid, $add_to_journal,"customer");
			?>
			<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			<!--
				document.location='customers.php';
			//-->
			</SCRIPT>
			<?php
		} elseif ($editfilled && !$fconfirmed) {
			// First, collect extra fields list
				
				if (CheckCustomerAccess($_REQUEST['editfilled']) <> "ok") {
					print "<img src='error.gif'>&nbsp;Access denied";
					EndHTML();
					exit;
				}

			$gh = db_GetRow("SELECT readonly,customer_owner FROM $GLOBALS[TBL_PREFIX]customer WHERE id=" . $editfilled);

			if (($gh['customer_owner'] <> $GLOBALS['USERID']) && (!is_administrator()) && ($gh['readonly']=='yes')) {
				print "<img src='error.gif'> Access denied";
				log_msg("WARNING - DOUBLE RISK. Somebody tried a direct post to adjust customer dossier " . $editfilled,"");
				EndHTML();
				exit;
			}
			$eid = $editfilled;

			$list = GetExtraCustomerFields();

		
			foreach ($list AS $extrafield) {
				$varname = "EFID" . $extrafield['id'];
				if ($$varname) {
					//print "FOUND $$varname $varname";
						
						$sql = "SELECT value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='$eid' AND name='" . $extrafield['id'] ."' AND type='cust'";
						$result = mcq($sql,$db);
						$gh = mysql_fetch_array($result);
						$value = $gh[0];

						if (is_array($_REQUEST[$varname])) {
							qlog("WARNING - THIS IS AN EXTRA ARRAY FIELD!");
							$tmp = array();
							foreach($_REQUEST[$varname] AS $row) {
								if ($row <> "") {
									array_push($tmp, base64_encode($row));
								}
							}
							$$varname = serialize($tmp);
						}
						if (mysql_affected_rows()>0) {
								if ($value <> $$varname) { 
									$sql = "UPDATE $GLOBALS[TBL_PREFIX]customaddons SET value = '" . mysql_real_escape_string($$varname) . "',usr='" . $name . "' WHERE eid='" . $eid . "' AND name='" . $extrafield['id'] . "' AND type='cust'";
									//ProcessTriggers("EFID" . $extrafield['id'],$eid,$$tmp);
									$add_to_journal .= "\n" . CleanExtraFieldName($extrafield['name']) . " updated from $value to " . mysql_real_escape_string($$varname) . "";
								}
						} else {
								$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]customaddons(eid, name, value, usr, type) VALUES('" . $eid . "','" . $extrafield['id'] . "','" . mysql_real_escape_string($$varname) . "','" . $name . "','cust')";
								//ProcessTriggers("EFID" . $extrafield['id'],$eid,$$tmp);
								$add_to_journal .= "\n" . CleanExtraFieldName($normal) . " updated from <nothing> to " . $$varname . "";
						}

						// And finally, execute the statement.
						//print $sql . "<br><br>";
						mcq($sql,$db);

						

					}
			}
			$add_to_journal = "Customer " . $eid . " edited\n" . $add_to_journal;
			
		//	if (array_key_exists('activenew',$_REQUEST)) {
				if ($_REQUEST['activenew'] == "") {
					$_REQUEST['activenew'] = "no";
				} else {
					$_REQUEST['activenew'] = "yes";
				}
	//		}
				if ($_REQUEST['email_owner_upon_adds'] == "") {
					$_REQUEST['email_owner_upon_adds'] = "no";
				} else {
					$_REQUEST['email_owner_upon_adds'] = "yes";
				}

		//	if (array_key_exists('readonlycust',$_REQUEST)) {
				if ($_REQUEST['readonlycust'] == "") {
					$_REQUEST['readonlycust'] = "no";
				} else {
					$_REQUEST['readonlycust'] = "yes";
				}

				if ($_REQUEST['vpn_master'] == "") {
					$_REQUEST['vpn_master'] = "0";
				} else {
					$_REQUEST['vpn_master'] = "1";
				}
//			}

			// If the customer name was changed, expire all cache (ouch!)
			if ($_REQUEST['custnamenew']) {
				if (GetCustomerName($eid) <> $_REQUEST['custnamenew']) {
					ExpireFormCache("%");
				}
			}

			$fields = array("custnamenew","contactnew","contact_titlenew","contact_phonenew","cust_homepagenew","cust_addressnew","cust_remarksnew","contact_emailnew","activenew","email_owner_upon_adds","customer_ownernew","id_customer_groupnew","id_support_groupnew","id_vpnnew","ip_vpnnew","vpn_master","customer_respnew","rfcnew","cust_notesnew");
			

			foreach ($fields AS $field) {
				if ($_REQUEST[$field]!='') {
					qlog("$field is submitted!");
					$sql_ins .= ", " . ereg_replace("new","",ereg_replace("readonlycust","readonly",$field)) . "='" . mysql_real_escape_string($_REQUEST[$field]) . "'";
					$add_to_journal .= "\n" . $field . " to " . $_REQUEST[$field];
				}
			
			}
			
			journal($eid, $add_to_journal,"customer"); 			
			$sql = "UPDATE $GLOBALS[TBL_PREFIX]customer SET readonly='" . $_REQUEST['readonlycust'] . "', active='" . $activenew . "' " . $sql_ins . " WHERE id='" . $eid . "'";
			
			mcq($sql,$db);
			log_msg("Customer $editfilled edited","");

			// Clear the access cache tables
			ClearAccessCache($eid,'c');
//			print $sql;
		
		$det = 1;
		$c_id = $editfilled;
	}
	if ($closeonnextload1) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	window.close();
	//-->
	</SCRIPT>
	<?php
	}
	}
        
	if ($_REQUEST['det'] && $_REQUEST['c_id']) {
			if ($_REQUEST['fconfirmed']) {

					if (CheckCustomerAccess($_REQUEST['c_id']) == "ok") {
						

						for ($c=0;$c<sizeof($deletefile);$c++) {
							$sql = "SELECT filename FROM $GLOBALS[TBL_PREFIX]binfiles WHERE fileid='$deletefile[$c]'";
							$result = mcq($sql,$db);
							$filename = mysql_fetch_array($result);
							$filename = $filename[filename];
							$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]binfiles WHERE fileid='$deletefile[$c]'";
							mcq($sql,$db);
							$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]blobs WHERE fileid='$deletefile[$c]'";
							mcq($sql,$db);

							log_msg("File deleted: $deletefile[$c] - $filename","");
							journal($c_id,"File $filename (#" . $deletefile[$c] . ") deleted","customer");

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
						?>
						
									<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
									<!--
										document.location='customers.php?det=1&c_id=<? echo $c_id;?>';
									//-->
									</SCRIPT>
									<?php
					}
						
			} 
			if ($_REQUEST['somedelete'] || is_array($_REQUEST['deletefile'])) {
				$a = GetClearanceLevel($GLOBALS[USERID]);

				if (CheckCustomerAccess($_REQUEST['c_id']) <> "ok") {
					printAD();
					EndHTML();
					exit;
				}
				print "<table><tr><td>";
				print "$lang[deleting1] " . sizeof($deletefile) . " $lang[deleting2]<br>";
				print "<form name='confirm' method='POST'>";
					for ($c=0;$c<sizeof($deletefile);$c++) {
						print "<input type='hidden' name='deletefile[]' value='$deletefile[$c]'>";
					}
				print "<br><input type='hidden' name='fconfirmed' value='1'><input type='hidden' name='c_id' value='$c_id'><input type='hidden' name='editfilled' value='$c_id'><input type='hidden' name='det' value='1'>";
				print "<input type='submit' name='knopje' value='$lang[confdel]'>";
				print "<pre>";
				for ($r=0;$r<sizeof($deletefile);$r++) {
					print $lang[delete] . " " . $deletefile[$r] . " - " . GetFileName($deletefile[$r]) . "\n";
				}
				print "</pre>";
				print "</form></td></tr>\n</table>";
				EndHTML();
				exit;
		}
			
		if (!$_FILES['userfile']['tmp_name'] =="" && !$_FILES['userfile']['name']=="" && !$_FILES['userfile']['size']=="" && !$_FILES['userfile']['type']=="") {
			
			//  A file was attached

				
			// Read contents of uploaded file into variable
				
				$fp=fopen($_FILES['userfile']['tmp_name'] ,"rb");
				$filecontent=fread($fp,filesize($_FILES['userfile']['tmp_name'] ));
				fclose($fp);
//				$filecontent = addslashes($filecontent);

			// insert identifier & content into $GLOBALS[TBL_PREFIX]binfiles:

				//$sql="INSERT INTO $GLOBALS[TBL_PREFIX]binfiles(koppelid,content,filename,filesize,filetype,username,type) VALUES('$c_id','$filecontent','" . $_FILES['userfile']['name'] . "','" . $_FILES['userfile']['size'] . "','" . $_FILES['userfile']['type'] . "','" . $name . "','cust')";

				//mcq($sql,$db);
				//$attachment = mysql_insert_id();

				$attachment = AttachFile($c_id,$_FILES['userfile']['name'],$filecontent,"cust",$_FILES['userfile']['type']);

				log_msg("File  " . $_FILES['userfile']['name'] . " added to customer $e","");

				unset($filecontent);

				journal($c_id,"File " . $_FILES['userfile']['name'] . " added", "customer");
			}

			$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]customer WHERE id='$c_id'";
			$stashid = PushStashValue($sql);
			$result= mcq($sql,$db);
			$cust= mysql_fetch_array($result);

			print "</table>";
			
			if (is_numeric($GLOBALS['CUSTOMCUSTOMERFORM'])) {
				?>
					<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
					<!--
						document.location='customers.php?editcust=1&custid=<? echo $c_id;?>';
					//-->
					</SCRIPT>
				<?php
			}
			
			if (CheckCustomerAccess($_REQUEST['c_id']) <> "ok" && CheckCustomerAccess($_REQUEST['c_id']) <> "readonly") {
				print "<table><tr><td>";
				printAD("see trace for details");
				print "</td></tr></table>";
				EndHTML();
				exit;
			}
//**********************************************************************************************
/**
 * @author Aldo Vargas
 */
			if ($_POST['btnadddisp']){
				editDetailDisp('_add_');
				exit;
			
			}
			if ($_POST['btneditdisp']){
				$v = editDbDisp("_update_");
				viewDetailsDevices($v);
				exit;
			}
			if ($_POST['btnadisp']){
				$v = editDbDisp("_add_");
				viewDetailsDevices($v);
				exit;
			}
			if ($_POST['btndeldisp']){
				$v = editDBDisp('_del_');
 				viewDetailsDevices($v);
				exit;
			}
			if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_GET['edisp']){
				editDetailDisp('_edit_');
				exit;
			}
			if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_REQUEST['view_det_gen']){
				view_details_gen();
				EndHTML();
				exit;
			}
			if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_REQUEST['btn_edit_gen']){
				edit_details_gen();
				EndHTML();
				exit;
			}
			if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_REQUEST['btn_mod_gen']){
				mod_details_gen();
				EndHTML();
				exit;
			}
			//---------------------------------------------------------------
			if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_REQUEST['view_det_prog']){
				view_details_prog();
				EndHTML();
				exit;
			}
			if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_REQUEST['btn_edit_prog']){
				edit_details_prog();
				EndHTML();
				exit;
			}
			if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_REQUEST['btn_mod_prog']){
				mod_details_prog();
				EndHTML();
				exit;
			}
			//----------------------------------------------------------------
			if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_REQUEST['view_det_serv']){
				viewDetailsServers();
                                                                                EndHTML();
				exit;
			}
                        if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_REQUEST['newserver']) {
				if($_REQUEST['edit']==1){
                                    updateServer($_REQUEST);
                                }else{
                                    addServer($_REQUEST);
                                }
                                viewDetailsServers();
                                EndHTML();
				exit;
			}
                        if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_REQUEST['addserver']) {
				viewAddServers();
                                EndHTML();
				exit;
			}
			if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_REQUEST['btn_edit_serv']){
				viewAddServers($status=0);
				EndHTML();
				exit;
			}
			//-----------------------------------------------------------------
			if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_REQUEST['view_det_disp']){
				viewDetailsDevices();
				exit;
			}
/*			if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_REQUEST['btn_edit_disp']){
				edit_details_disp();
				EndHTML();
				exit;
			}
			if ($_REQUEST['c_id'] and $_REQUEST['det'] and $_REQUEST['btn_mod_disp']){
				mod_details_disp();
				EndHTML();
				exit;
			}*/
//************************************************************************************************
		//*********************************************************
		$sql="SELECT CUSTOMERREADONLY FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id=$GLOBALS[USERID]";
		$result=mcq($sql,$db);
		$customer_readonly=mysql_result($result,0,0);
		//******************************************
				
		//*********************************************************
		//Grupos
		$sql = "SELECT grp_id as id, grp_nombre as nombre, grp_admin as admin, grp_oper as oper FROM $GLOBALS[TBL_PREFIX]grupos WHERE grp_id='$cust[id_customer_group]' AND grp_active='1' AND grp_type='0'";
		$result = mcq($sql, $db);
		if (mysql_num_rows($result)>0){
		$group_data=mysql_fetch_array($result);

		//members
		$sql = "SELECT id, custname FROM $GLOBALS[TBL_PREFIX]customer WHERE id_customer_group='$cust[id_customer_group]' ORDER BY custname";
		$result = mcq($sql, $db);
		$members=array();
		while ($row=mysql_fetch_array($result)){
		    $members[] = $row['custname'] == $cust['custname'] ? "<b>$row[custname]</b>" : $row['custname'];
		}
		$groupset = "
		<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<a href='javascript:;' onmousedown=\"toggleDiv('groupset');\">Informacion de grupo:</a>&nbsp;&nbsp;</legend>
		<div id='groupset' style='display:none'>
		<table border=0 width=600 class='crm'>
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
		</div>
		</fieldset><br>";
		}

		$sql = "SELECT grp_id as id, grp_nombre as nombre, grp_admin as admin, grp_oper as oper FROM $GLOBALS[TBL_PREFIX]grupos WHERE grp_id='$cust[id_support_group]' AND grp_active='1' AND grp_type='1'";
		$result = mcq($sql, $db);
		if (mysql_num_rows($result)>0){
		$group_data=mysql_fetch_array($result);
		$supportset = "
		<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<a href='javascript:;' onmousedown=\"toggleDiv('supportset');\">Informacion de grupo de soporte:</a>&nbsp;&nbsp;</legend>
		<div id='supportset' style='display:none'>
		<table border=0 width=600 class='crm'>
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
		</table>
		</div>
		</fieldset><br>";
		}

		//VPN
		$sql = "SELECT vpn_id as id, vpn_nombre as nombre, vpn_bdcentral as bdcentral, vpn_ip_dns as ip_dns FROM $GLOBALS[TBL_PREFIX]vpn WHERE vpn_id='$cust[id_vpn]' AND vpn_status='1'";
		$result = mcq($sql, $db);
		if (mysql_num_rows($result)>0){
		$vpn_data=mysql_fetch_array($result);

		//members
		$sql = "SELECT id, custname, ip_vpn, vpn_master FROM $GLOBALS[TBL_PREFIX]customer WHERE id_vpn='$cust[id_vpn]' ORDER BY custname";
		$result = mcq($sql, $db);
		$members=array();
		while ($row=mysql_fetch_array($result)){
		    $members[]=array('ip_vpn'=>$row[ip_vpn],'custname'=>$row['custname'],'master'=>$row['vpn_master'],'id'=>$row['id']);
		}
		$vpnset = "
		<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<a href='javascript:;' onmousedown=\"toggleDiv('vpnset');\">Informacion de VPN:</a>&nbsp;&nbsp;</legend>
		<div id='vpnset' style='display:none'>
		<table border=0 width=600 class='crm'>
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
					$vpnset.= $member['id'] == $cust['id'] ? "<tr><td><b>$member[ip_vpn]</b></td><td><b>$member[custname]</b></td></tr>" : "<tr><td>$member[ip_vpn]</td><td>$member[custname]</td></tr>";
				    }
				
				}
				$vpnset.= "
			    </table>
			</td>
		    </tr>
		</table>
		</div>
		</fieldset><br>";
		}

		//Polizas vigentes
		$params['custid']=$cust['id'];
		$params['status']='1';
		$polizas = GetPolizasByCustomer($params);
		$polizasset = "";
		if (sizeof($polizas)>0){
		    $polizasset .= "
		    <fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<a href='javascript:;' onmousedown=\"toggleDiv('polizasset');\">Polizas Vigentes:</a>&nbsp;&nbsp;</legend>
		    <div id='polizasset' style='display:none'>
		    <table border=0 width=1000 class='crm'>
			<tr>
			    <td align='center'><b>POLIZA</b></td>
			    <td align='center'><b>DESDE</b></td>
			    <td align='center'><b>HASTA</b></td>
			    <td align='center'><b>MODO PAGO</b></td>
			    <td align='center'><b>CONTRATO</b></td>
			    <td align='center'><b>ULTIMO PAGO</b></td>
			    <td align='center'><b>PROXIMO PAGO</b></td>
			    <td align='center'><b>ESTADO DE COBRO</b></td>
			    <td align='center'><b>ESTADO</b></td>
			</tr>";
			foreach($polizas as $poliza){
			    $upago = $poliza['ultimopago'] == '00/00/0000' ? '' : $poliza['ultimopago'];
			    $ppago = $poliza['proximopago'] == '00/00/0000' ? '' : $poliza['proximopago'];
			    $stpago = $poliza['vencimiento'] == '0' ? 'AL CORRIENTE' : 'PAGOS VENCIDOS';
			    $active = $poliza['active'] == '1' ? "<font color='green'><b>ACTIVA</b></font>" : "<font color='red'><b>INHABILITADA</b></font>";
			    $polizasset.= "
			    <tr>
				<td>$poliza[nombre]</td>
				<td>$poliza[fini]</td>
				<td>$poliza[ffin]</td>
				<td>$poliza[modopago]</td>
				<td>$poliza[contrato]</td>
				<td>$upago</td>
				<td>$ppago</td>
				<td>$stpago</td>
				<td align='center'>$active</td>
			    </tr>
			    ";
			}
			$polizasset.= "
		    </table>
		    </div>
		    </fieldset><br>";
		}
		//*********************************************************
			//print "<table width='80%'><tr><td>&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$lang[customer] $c_id: $cust[custname]</font>&nbsp;";
                        print "<table width='80%'><tr><td>&nbsp;</td><td><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$lang[customer]: $cust[custname]</font>&nbsp;";

			print "</legend>";
			print "<table class='crm' width='100%'>";
			print "<tr><td><img src='arrow.gif'>&nbsp;<a href='customers.php?tab=$_REQUEST[tab]&$epoch' class='bigsort'>$lang[customers]</a>";
			if ($cust['readonly'] == "yes") {
				$ro = "yes";
				if ($cust['customer_owner'] <> $GLOBALS['USERID']) {
					$readonly = true;
				}
			} else {
				$ro = "no";
			}
			
			//********************************
			if ($customer_readonly!='1'){
			print " | <img src='arrow.gif'><img src='arrow.gif'>&nbsp;<a href='customers.php?editcust=1&custid=$c_id&tab=$_REQUEST[tab]&$epoch' class='bigsort'>$lang[edit]</a>";
			}
			//********************************
			require_once("funcictc.php");
			$userdata = GetUser($GLOBALS['USERID']);
			if ($userdata['flag_admin']=='1' or $userdata['flag_opera']=='1'){
			    print " | <img src='arrow.gif'><img src='arrow.gif'><img src='arrow.gif'>&nbsp;<a href='polizas.php?custid=$c_id&tab=$_REQUEST[tab]&$epoch' class='bigsort'>Polizas</a>";
			}
			//*********************************

			print "</td><td align='right' colspan=4>&nbsp;<a href='javascript:popActivityCustomerGraph(" . $c_id .");' title='Show activity graph'><img src='graph.gif' border=1></a>&nbsp;&nbsp;<a href='javascript:popcustomerjournal(" . $cust['id'] . ")' class='bigsort'><img src='journal.gif' style='border:0' title='Show journal'></a>&nbsp;<a href=\"javascript:popPDFwindow('customers.php?pdf=1&stashid=" . $stashid . "')\" class='bigsort'><img src='pdf.gif' style='border:0' title='Show PDF'></a>";
			if ($GLOBALS['EnableMailMergeAndInvoicing']) {
				print "&nbsp;<a title='Use results for mail merge' href=\"javascript:poplittlewindow('invoice.php?little=1&stashid=" . $stashid . "&mm=1')\"><img style='border:0' src='word.gif'></a>";
			}
			print "&nbsp;<a class='bigsort' style='cursor:pointer' title='Print on default printer' href=\"javascript:popPDFprintwindow('customers.php?pdf=1&stashid=" . $stashid . "&print=1')\"><img src='print.gif' style='border:0'></a>";
			print "</td></tr>\n";

			print "<tr><td>$lang[contact]</td><td>$cust[contact]&nbsp;</td></tr>\n";
			print "<tr><td>RFC:</td><td>$cust[rfc]&nbsp;</td></tr>\n";
			print "<tr><td>$lang[contacttitle]</td><td>$cust[contact_title]&nbsp;</td></tr>\n";
			print "<tr><td>$lang[contactphone]</td><td>$cust[contact_phone]&nbsp;</td></tr>\n";
			print "<tr><td>$lang[contactemail]</td><td><a href='javascript:popEmailToCustomerScreenCust(" . $cust[id] . ")' class='bigsort'>$cust[contact_email]</a>&nbsp;</td></tr>\n";
			print "<tr><td>$lang[customeraddress]</td><td>" . ereg_replace("\n","<BR>",$cust[cust_address]) . "&nbsp;</td></tr>\n";
			print "<tr><td>$lang[custremarks]</td><td>" . ereg_replace("\n","<BR>",$cust[cust_remarks]) . "&nbsp;</td></tr>\n";
			print "<tr><td>Notas importantes:</td><td>$cust[cust_notes]&nbsp;</td></tr>\n";
			
			//if (!stristr($cust[cust_homepage],"http://")) {
			//			$cust[cust_homepage] = "http://" . $cust[cust_homepage];
			//		}

			$sql = "SELECT b.httpport as httpport, b.sshport as sshport FROM CRMesservers as a INNER JOIN CRMsrvconninfo as b on a.id=b.idserver AND a.idcustomer=$cust[id] and a.status='1'";
			$result = mcq_array($sql);

			$cust_homepage = $cust['cust_homepage'];
			$cust_homepage = str_replace("http://","",$cust_homepage);
			$pos = strstr($cust_homepage,':');
			$cust_homepage = $pos != false ? str_replace($pos,"",$cust_homepage) : $cust_homepage;
			
			if (sizeof($result)==0){
			    echo "<tr>
				    <td>$lang[custhomepage]</td>
				    <td><a href='http://$cust_homepage' target='_new' class='bigsort'>$cust_homepage</a>&nbsp;</td>
				</tr>\n";
			}
			else{
			    foreach($result as $cnn){
				$port = ($cnn['httpport'] == 0 or $cnn['httpport']==80) ? '' : ":$cnn[httpport]";
				echo "
				    <tr>
					<td>$lang[custhomepage]</td>
					<td>
					    <a href='http://$cust_homepage$port' target='_new' class='bigsort'>$cust_homepage$port</a>&nbsp;
					</td>
				    </tr>\n";
			    }
			}

			$list = GetExtraCustomerFields();

			foreach($list AS $field) {

				$sql = "SELECT id,value FROM $GLOBALS[TBL_PREFIX]customaddons WHERE eid='$c_id' AND deleted<>'y' AND name='" . $field['id'] . "' AND type='cust' ORDER BY name";
				$result = mcq($sql,$db);
				$cust1= mysql_fetch_array($result);

				print "<tr><td>" . $field['name'] . "</td>";
				if ($field['fieldtype'] == "text area") {
					$cust1[value] = ereg_replace("\n","<BR>",$cust1[value]);
				}
				if ($field['fieldtype'] == "mail") {
					print "<td><a href='javascript:popEmailToEFScreen(" . $cust1['id'] . ")' class='bigsort'>" .$cust1[value] . "</a>&nbsp;</td></tr>\n";
				} elseif ($field['fieldtype'] == "hyperlink"){
					print "<td>";
					if (strlen($cust1[value])>4) {
						if (!strstr("http://",$cust1[value])) {
							$cust1[value] = "http://" . $cust1[value];
						}
						print $cust1[value] . "&nbsp;<a href='" . $cust1[value] . "' target='new'><img src='fullscreen_maximize.gif' style='border:0' height=16 width='16'></a>";
					}
					print "</td></tr>\n";
				} elseif ($field['fieldtype'] == "date") {
					print "<td>" . Transformdate($cust1[value]) . "&nbsp;</td></tr>\n";
				} elseif (strstr($field['fieldtype'],"User-list")) {
					print "<td>" . GetUserName($cust1['value']) . "&nbsp;</td></tr>\n";
				} elseif ($field['fieldtype'] == "List of values") {
					print "<td>" . FunkifyLOV($cust1['value']) . "&nbsp;</td></tr>\n";
				} else {
					print "<td>" . $cust1[value] . "&nbsp;</td></tr>\n";
				}
			}

			
			
			print "<tr><td colspan=2><hr></td></tr>\n";
			print "<tr><td>Responsable:</td><td>" . GetUserName($cust['customer_resp']) . "</td></tr>\n";
			print "<tr><td>" . $lang[customer] . " " . strtolower($lang[owner]) . ":</td><td>" . GetUserName($cust['customer_owner']) . "</td></tr>\n";
			

			print "<tr><td>$lang[readonly]:</td><td>" . $ro . "&nbsp;</td></tr>\n";
			print "<tr><td>E-mail $lang[owner]:</td><td>" . $cust['email_owner_upon_adds'] . "</td></tr>\n";                                  
			print "<tr><td colspan=2><hr></td></tr>\n";
                                                   
                        
                                                            print "</table>";
                                                            print "
			    <fieldset><legend><img src='crmlogosmall.gif'>&nbsp;&nbsp;Actividades&nbsp;&nbsp;</legend>
				<form name=details>
				    <table border=0>
					<tr>
					    <td width=30></td>
					    <td width=100 align=center><input type='button' OnClick='javascript:document.location=\"edit.php?e=_new_&tab=24&SetCustTo=$_REQUEST[c_id]\" ' name='Agregar' value='Agregar Registro'></a></td>
					    <td width=30> </td>
					    <td width=100 align=center> <input type='button' OnClick='javascript:document.location=\"index.php?ShowEntitiesOpen=1&pdfiltercustomer=$_REQUEST[c_id]\" 'name='consultar' value='Consultar  Registro'></td>
					    <td width=30></td>
					    </tr>
				    </table>
				</form>
			    </fieldset>
			<br>
			";		
//****************************************************************************************
			echo "
			<br>
			    <fieldset><legend><img src='crmlogosmall.gif'>&nbsp;&nbsp;Detalles&nbsp;&nbsp;</legend>
				<form name=details>
				    <table border=0 >
					<tr>
					    <td width=30></td>
					    <td width=100 align=center><input type='submit' name='view_det_gen' value='Ver Informacion General'></td>
					    <td width=30></td>
					    <td width=100 align=center><input type='submit' name='view_det_prog' value='Ver Informacion de Programas'></td>
					    <td width=30></td>
					    <td width=100 align=center><input type='submit' name='view_det_serv' value='Ver Informacion del Servidor'></td>
					    <td width=30></td>
					    <td width=100 align=center><input type='submit' name='view_det_disp' value='Ver Informacion de Dispositivos'></td>
                                                                                                        <td width=30></td>
					    <td><input type='hidden' name='c_id' value=".$_REQUEST['c_id']."><input type='hidden' name='det' value=1></td>
					</tr>
				    </table>
				</form>
			    </fieldset>
			<br>
			";
//*******************************************************************************************
			echo $groupset;
			echo $supportset;
			echo $vpnset;
			echo $polizasset;

			print "<table border=0 width='100%'><tr><td><fieldset><table border=0>";
			

			$sql= "SELECT filename,creation_date,date_format(creation_date, '%a %M %e, %Y %H:%i') AS dt,filesize,fileid,username FROM $GLOBALS[TBL_PREFIX]binfiles WHERE koppelid='$c_id' AND type='cust' ORDER BY filename,creation_date";
			$result= mcq($sql,$db);
			
			

			$toprint .= "<tr><td>$lang[filename]</td>";

			$toprint.= "<td>$lang[filesize]</td><td>$lang[creationdate]</td><td>$lang[owner]</td><td>$lang[deletefile]</td></tr>\n";
			print "<form name='AddFileToCust' method='POST' ENCTYPE='multipart/form-data' id='dashed'>";
			while ($files= mysql_fetch_array($result)) {
				$ownert = ($files['username']);
				unset($ins_rec1);				

				
				
				unset($filename);


				if (stristr("@@@:",$ownert[FULLNAME])) {
					$ownert = "&nbsp;&nbsp;&nbsp;n/a";
				}

				$toprint .= "<tr><td>";
				
		
				$toprint .= "<img src='arrow.gif'>&nbsp;<a href=csv.php?fileid=$files[fileid] class='bigsort'>$files[filename]</a> $ins_rec1";
			
				
				$toprint .="</td>";
		

						
					
				$toprint .= "<td>";
				$toprint .= ceil(($files[filesize]/1024)). "K";
				$toprint .= "</td><td>$files[dt]</td><td>$ownert</td><td>";
				if (!$readonly) {
					$toprint .= "<input type='checkbox' class='radio' name=deletefile[] OnChange='document.AddFileToCust.somedelete.value=1' value='$files[fileid]' $roins></td></tr>\n";
				}
				$ftel++;
			}
			if ($ftel) { 
				print $toprint . "</td></tr>\n"; 
			}
			
			if (!$readonly) {
				print "<tr><td colspan=6><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;$lang[attachfile]&nbsp;</legend>";
				print "<INPUT TYPE='hidden' name='MAX_FILE_SIZE' value='52428800'><input type='hidden' name='somedelete' value=''><input type='hidden' 	name='det' value='1'><input type='hidden' name='c_id' value='$c_id'><INPUT NAME='userfile' TYPE='file' $roins>&nbsp;<input type='submit' 	value='" . $lang['save'] . "'></form>&nbsp;&nbsp;&nbsp;&nbsp;<a OnClick='javascript:pophelp(9)' class='bigsort' cursor='click' style='cursor: help'><img src='info.gif'></a></td></tr>\n</table></fieldset>";
			} else {
				print "<tr><td>";
			}

			print "<br>";
			//print "</fieldset>";

			print "<br><img src='arrow.gif'>&nbsp;<a href='customers.php?$epoch' class='bigsort'>$lang[customers]</a>";
			
			if ($readonly) {
				// niks
			} else {
				//***********************************
				if ($customer_readonly!='1'){
				print " | <img src='arrow.gif'><img src='arrow.gif'>&nbsp;<a href='customers.php?editcust=1&custid=$c_id&$epoch' class='bigsort'>$lang[edit]</a>";
				}
				//***********************************
			}
			EndHTML();
			exit();
	}
	if ($deleteconfirm) {
			$a = GetClearanceLevel($GLOBALS[USERID]);
			if ($a<>"rw" && $a<>"administrator") {
					print "<img src='error.gif'>&nbsp;Access denied";
					EndHTML();
					exit;
			}
				$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]customer WHERE id='$deleteconfirm'";
				$result= mcq($sql,$db);
				$cust= mysql_fetch_array($result);

			if (($cust['customer_owner'] <> $GLOBALS['USERID']) && (!is_administrator()) && ($cust['readonly']=='yes')) {
				print "<img src='error.gif'> Access denied";
				log_msg("WARNING - DOUBLE RISK. Somebody tried a direct post to delete customer dossier " . $deleteconfirm,"");
				EndHTML();
				exit;
			}

			$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]customer WHERE id='$deleteconfirm'";
			mcq($sql,$db);

	//			$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]journal WHETE eid='$deleteconfirm' AND type='customer'";
	//			mcq($sql,$db);

			print "<table><tr><td>Record $deleteconfirm (+journal) $lang[wasdeleted] .</td></tr>\n</table>";
			unset($search);
			unset($delete);
			unset($deleleconfirmed);
			unset($add);
			log_msg("Entry $deleteconfirm deleted from customer table","");
	}

	if ($delete) {

			$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE CRMcustomer='$delid'";
			$resbla = mcq($sql,$db);
			$count = mysql_fetch_array($resbla);
			$count = $count[0];
			$a = GetClearanceLevel($GLOBALS[USERID]);
			if ($a<>"rw" && $a<>"administrator") {
					print "<img src='error.gif'>&nbsp;Access denied";
					exit;
					
			}
			
			if ($count>0) {
				print "Illegal move. This incident has been logged.";
				log_msg("Someone tried to delete a customer with entities by adjusting HTML code","");
				exit;
			} 
			$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]customer WHERE id='$delid'";
			$result= mcq($sql,$db);
			$cust= mysql_fetch_array($result);
			
			if (($cust['customer_owner'] <> $GLOBALS['USERID']) && (!is_administrator()) && ($gh['readonly']=='yes')) {
				print "<img src='error.gif'> Access denied";
				log_msg("WARNING - DOUBLE RISK. Somebody tried a direct post to adjust customer dossier " . $editfilled,"");
				EndHTML();
				exit;
			}
			if (!stristr($cust[cust_homepage],"http://")) {
						$cust[cust_homepage] = "http://" . $cust[cust_homepage];
			}
			
			print "<form name='delconf' method='POST'><fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$lang[customer]: $cust[custname]</font>&nbsp;</legend>";
			print "<table width=100%><tr>";
			print "<td colspan=2><b>$lang[pbdelconf]</b></td></tr>\n<tr>";
			print "<tr><td>$lang[contact]</td><td><input  DISABLED type='text' name='contactnew' value='$cust[contact]' size=40></td></tr>\n";
			print "<tr><td>$lang[contacttitle]</td><td><input type='text' name='contact_titlenew' value='$cust[contact_title]' size=40 DISABLED></td></tr>\n";
			print "<tr><td>$lang[contactphone]</td><td><input type='text' name='contact_phonenew' value='$cust[contact_phone]' size=15 DISABLED></td></tr>\n";
			print "<tr><td>$lang[contactemail]</td><td><input type='text' name='contact_emailnew' value='$cust[contact_email]' size=40 DISABLED></td></tr>\n";
			print "<tr><td>$lang[customeraddress]</td><td><textarea rows=8 cols=60 wrap='virtual' class='txt' name='cust_addressnew'  DISABLED>$cust[cust_address]</textarea></td></tr>\n";
			print "<tr><td>$lang[custremarks]</td><td><textarea rows=12 cols=60 wrap='virtual' class='txt' name='content' DISABLED>$cust[cust_remarks]</textarea></td></tr>\n";
			print "<tr><td>$lang[custhomepage]</td><td><input type='text' name='cust_homepagenew' value='$cust[cust_homepage]' size=40 DISABLED></td></tr>\n</table></fieldset>";
			print "<br><input type='hidden' name='deleteconfirm' value='$delid'><input type='submit' name='knoppie' value='$lang[deletepb]'></form></td></tr>\n</table>";
	EndHTML();
			exit;
	}


	if ($add || $editcust) {

		if (CheckCustomerAccess($custid)=="readonly") {
			$readonly = true;
			$roins = "DISABLED";
			$formaction = "index.php?logout=1";
		} elseif (CheckCustomerAccess($custid)<>"ok") {
			printAD("not_authorized: " . CheckCustomerAccess($custid));
			EndHTML();
			exit;
		} else {
			$formaction = "customers.php";
			$readonly = false;
		}

			if (is_numeric($GLOBALS['CUSTOMCUSTOMERFORM'])) {
				if ($_REQUEST['add']) {
					$c_add = "YES";
				}

				print CustomCustomerForm($GLOBALS['CUSTOMCUSTOMERFORM'], $custid, $c_add);

				EndHTML();
				exit;
			} else {

			print "<form name='EditEntity' method='POST' action='" . $formaction . "'><input type='hidden' name='changed'>";
			print PrintExtraFieldForceJavascript("EditEntity",$type="customer");
			if ($editcust) {
				$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]customer WHERE id='$custid'";
				$result= mcq($sql,$db);
				$cust= mysql_fetch_array($result);
				if ($nonavbar) {
					print "<table width='100%'><tr><td>";				
				} else {
					print "</td></tr>\n</table><table width='80%'><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>";				
				}
				

				print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$lang[customer] $custid: $cust[custname]</font>&nbsp;</legend>";
				print "<table cellspacing='0' cellpadding='4'  border='0'>";
				if ($nonavbar) {
					$wt = "100%";
				} else {
					$wt = "80%";
				}

				print "<input type='hidden' name='editfilled' value='$custid'>";
				$sql = "SELECT active FROM $GLOBALS[TBL_PREFIX]customer WHERE id='$custid'";
				$result= mcq($sql,$db);
				$bla = mysql_fetch_array($result);
				$bla = $bla[0];

				if ($bla == "yes") $cyn = "CHECKED";

				print "<tr><td><b>Active</b>&nbsp;&nbsp;<input type='checkbox' class='radio' name='activenew' value='yes'  $cyn $roins></td></tr>\n";
				$sql = "SELECT COUNT(*) FROM $GLOBALS[TBL_PREFIX]entity WHERE CRMcustomer='$cust[id]'";
				$resbla = mcq($sql,$db);
				$count = mysql_fetch_array($resbla);
				$count = $count[0];

				if ($count>0) {
					$ins = "DISABLED";
					$ins2 = "($lang[custdelexplain])";
				} 

				$ins3 = "&nbsp;&nbsp;<input type='button' OnClick='javascript:document.location=\"customers.php?delete=1&delid=$cust[id]\"' name='do' value='$lang[delete]' $ins $roins> <br><table><tr><td>$ins2</td></tr>\n</table>";

				

				} else {
					print "<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$lang[addcust]</font>&nbsp;</legend>";
					print "<table border=0 cellspacing='0' cellpadding='4' width='100%'>";
					print "<input type='hidden' name='addfilled' value='1' $roins>";				
					
				}
			print "<tr><td><fieldset><legend>&nbsp;$lang[customer]</legend><input type='text' $roins name='custnamenew' value='" . htmlentities($cust['custname'], ENT_QUOTES) . "' size=40> <a OnClick=\"poplittlewindowWithBars('customers.php?CheckCustomer=' + document.EditEntity.custnamenew.value);\" style='cursor: pointer'>[check]</a></fieldset></td>";
			print "<td><fieldset><legend>&nbsp;RFC:</legend><input $roins type='text' name='rfcnew' value='" . ($cust['rfc']) . "' size=13></fieldset></td></tr>\n";
			print "<tr><td><fieldset><legend>&nbsp;$lang[contact]</legend><input $roins type='text' name='contactnew' value='" . ($cust['contact']) . "' size=40></fieldset></td>\n";
			print "<td><fieldset><legend>&nbsp;$lang[contacttitle]</legend><input $roins type='text' name='contact_titlenew' value='" . htmlentities($cust['contact_title'], ENT_QUOTES) . "' size=40></fieldset></td></tr>\n";
			
			print "<tr><td><fieldset><legend>&nbsp;$lang[contactphone]</legend><input $roins type='text' name='contact_phonenew' value='" . htmlentities($cust['contact_phone'], ENT_QUOTES) . "' size=15></fieldset></td>\n";
			print "<td><fieldset><legend>&nbsp;$lang[contactemail]</legend><input $roins type='text' name='contact_emailnew' value='" . htmlentities($cust['contact_email'], ENT_QUOTES) . "' size=40></fieldset></td></tr>\n";
			print "<tr><td colspan=2><fieldset><legend>&nbsp;$lang[customeraddress]</legend><textarea $roins rows=8 cols=80 wrap='virtual' class='txt' name='cust_addressnew'>" . htmlentities($cust['cust_address'], ENT_QUOTES) . "</textarea>&nbsp;</fieldset></td></tr>\n";

			print "<tr><td colspan=2><fieldset><legend>&nbsp;$lang[custremarks]</legend><textarea $roins rows=12 cols=80 wrap='virtual' class='txt' name='cust_remarksnew'>" . htmlentities($cust['cust_remarks'], ENT_QUOTES) . "</textarea>&nbsp;</fieldset></td></tr>\n";
			print "<tr><td colspan=2><fieldset><legend>&nbsp;Notas importantes</legend><textarea $roins rows=12 cols=80 wrap='virtual' class='txt' name='cust_notesnew'>" . htmlentities($cust['cust_notes'], ENT_QUOTES) . "</textarea>&nbsp;</fieldset></td></tr>\n";
			print "<tr><td colspan=2><fieldset><legend>&nbsp;$lang[custhomepage]</legend><input type='text' $roins name='cust_homepagenew' value='" . htmlentities($cust['cust_homepage'], ENT_QUOTES) . "' size=40></fieldset></td></tr>\n";


			//Obtener lo grupos de gasolineras******************************************************

			$sql = "SELECT grp_id as id, grp_nombre as nombre FROM $GLOBALS[TBL_PREFIX]grupos WHERE grp_active='1' and grp_type='0' ORDER BY grp_nombre";
			$result = mcq($sql, $db);
			echo "<tr><td colspan=2><fieldset><legend>Pertenece al grupo:</legend>&nbsp&nbsp;<select name='id_customer_groupnew'>
			    <option value='0'>Ninguno</option>";
			while ($row=mysql_fetch_array($result)){
			    $checked = $cust['id_customer_group'] == $row['id'] ? "SELECTED" : "";
			    echo "<option value='$row[id]' $checked>$row[nombre]</option>";
			}
			echo "</select></fieldset></td></tr>";
			//**************************************************************************************

			//Obtener lo grupos de soporte**********************************************************

			$sql = "SELECT grp_id as id, grp_nombre as nombre FROM $GLOBALS[TBL_PREFIX]grupos WHERE grp_active='1' and grp_type='1' ORDER BY grp_nombre";
			$result = mcq($sql, $db);
			echo "<tr><td colspan=2><fieldset><legend>Grupo de soporte:</legend>&nbsp&nbsp;<select name='id_support_groupnew'>
			    <option value='0'>Ninguno</option>";
			while ($row=mysql_fetch_array($result)){
			    $checked = $cust['id_support_group'] == $row['id'] ? "SELECTED" : "";
			    echo "<option value='$row[id]' $checked>$row[nombre]</option>";
			}
			echo "</select></fieldset></td></tr>";
			//**************************************************************************************

			//Obtener VPNs**************************************************************************

			$sql = "SELECT vpn_id as id, vpn_nombre as nombre FROM $GLOBALS[TBL_PREFIX]vpn WHERE vpn_status='1' ORDER BY vpn_nombre";
			$result = mcq($sql, $db);

			echo "
			<tr>
			    <td colspan=2><fieldset><legend>Configuraciones VPN:</legend>&nbsp&nbsp;
			    <select name='id_vpnnew'>
				<option value='0'>Ninguno</option>";
				while ($row=mysql_fetch_array($result)){
				$checked = $cust['id_vpn'] == $row['id'] ? "SELECTED" : "";
				echo "<option value='$row[id]' $checked>$row[nombre]</option>";
				}
			echo "</select>";
			echo "&nbsp;&nbsp;Direcci&oacute;n IP (VPN)&nbsp;<input type='text' name='ip_vpnnew' value='$cust[ip_vpn]' maxlength='15' size='20'>";
			$vpn = $cust['vpn_master'] == '1' ? 'CHECKED' : '';
			echo "&nbsp;&nbsp;Servidor maestro&nbsp;<input type='checkbox' value='y' name='vpn_master' $vpn>";
			echo "</fieldset></td></tr>";
			//**************************************************************************************



			print "<tr><td colspan=4>";

			print str_replace("</tr>","<tr>\n",ExtraFieldsBox($custid,$readonly,"%","customer"));
			print "</td></tr>\n";

			//****************************PERSONA RESPONSABLE*******
			print "<tr><td>Responsable:</td><td><select name='customer_respnew'>";

			$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]loginusers WHERE active='yes' AND FULLNAME NOT LIKE '@@@%' ORDER BY name";
			$result= mcq($sql,$db);

			while ($CRMloginusers= mysql_fetch_array($result)) {
				if ($CRMloginusers[id]==$cust['customer_resp']) {
						$a = "SELECTED";
				} else {
						$a = "";
				}
				 print "<option value='" . $CRMloginusers[id] . "' " . $a . " size='1'>" . $CRMloginusers['FULLNAME'] . "</option>";
			}

			print "</select>";

			print "</td></tr>\n";
			//******************************************************


			print "<tr><td>" . $lang['customer'] . " " . strtolower($lang['owner']) . ":</td><td><select name='customer_ownernew'>";
			
			$sql= "SELECT * FROM $GLOBALS[TBL_PREFIX]loginusers WHERE active='yes' AND FULLNAME NOT LIKE '@@@%' ORDER BY name";
			$result= mcq($sql,$db);
			
			while ($CRMloginusertje= mysql_fetch_array($result)) {
				if ($CRMloginusertje[id]==$cust['customer_owner']) {
						$a = "SELECTED";
						$owner = $cust['customer_owner'];
				} else {
						$a = "";
				}
				 print "<option value='" . $CRMloginusertje[id] . "' " . $a . " size='1'>" . $CRMloginusertje['FULLNAME'] . "</option>";
			}

			print "</select>";
			
			print "</td></tr>\n";
			print "<tr><td>$lang[readonly]:</td><td>";

			if ($cust['readonly']=="yes") {
				$ins = "CHECKED";
			} else {
				$ins = "";
			}

			print "<input class='radio' type='checkbox' value='y' name='readonlycust' $ins>";
			print "</td></tr>\n";
			print "<tr><td>E-mail $lang[owner]:</td><td>";
			if ($cust['email_owner_upon_adds']=="yes") {
				$ins = "CHECKED";
			} else {
				$ins = "";
			}

			print "<input class='radio' type='checkbox' value='yes' name='email_owner_upon_adds' $ins>";
			print "</td></tr>\n";
			print "</table>";
			if ($nonavbar) {
				print "<br><input type='hidden' name='closeonnextload' value='1'><input $roins type='button' OnClick='CheckForm();' name='submitknop' value='$lang[saveclose]'>&nbsp;&nbsp;<input type='button' name='do' value='$lang[cancel]' Onclick='window.close();'>";
			} else {
				print "<br><input type='button' OnClick='CheckForm();' name='do' $roins value='$lang[save]'>&nbsp;&nbsp;<input type='button' name='submitknop' value='$lang[cancel]' Onclick='javascript:history.back(1);'>";
			}
			print "$ins3";
			print "<input type='hidden' name='tab' value='$_REQUEST[tab]'>";
			print "</form>";
			if ($nonavbar) {
				print "</td></tr>\n</table>";				
			}
			print "";
	EndHTML();
			exit;
	} // end if GLOBALS CUSTOMCUSTFORM
} // end if editcust


	log_msg("Customer overview accessed","");
	
	if (!$search && !$zoek) {
	//	prtsrc("0");
		if ($maxcust<$GLOBALS['CUSTOMER_LIST_TRESHOLD'] || $_REQUEST['stashid']) {
		    DspSearchResults("","");

		} else {
		    //prtsrc2("0");
		    DspSearchResults("","");
		}

		if ($nonavbar) {
			print "<hr>";
		}
	} else {
		DspSearchResults($search,$cust_insert);
	}
}
EndHTML();
//******************View Catalog
function SelectsCatalog(){
         $selectcatalog="Select CRMsoft_catalog.idsoft,CRMsoft_catalog.nombre,CRMsoft_catalog.descripcion,CRMsoft_categories.descripcion as cd,CRMsoft_categories.idcategory
                                From CRMsoft_catalog  INNER JOIN CRMsoft_categories ON  CRMsoft_catalog.idcategory=CRMsoft_categories.idcategory";
    $doselect=  mcq($selectcatalog, $db);
    return $doselect;
}
//**********modulos disponibles
function namemodulo($idsoft){
    $modulos="SELECT CRMsoft_modules.idmodule,CRMsoft_modules.nombre as n,CRMsoft_catalog.nombre,CRMsoft_catalog.idsoft FROM CRMsoft_catalog INNER JOIN CRMsoft_modules ON CRMsoft_catalog.idsoft=CRMsoft_modules.idsoft
    where CRMsoft_catalog.idsoft='$idsoft'";
    $domodulos=  mcq($modulos, $db);
    if(mysql_num_rows($domodulos)>=1){
        $table="<table>";
   while($f=  mysql_fetch_array($domodulos)){
       $table.="<tr><td><a href='customers.php?btn_edit_gen=Editar&det=1&c_id=$_REQUEST[c_id]&ids=$f[idsoft]&idm=$f[idmodule]&module=1'><img src='pencil.png' align='top'></a></td><td>" .$f[idmodule].".-".$f[n]. "</td></tr>";
   }
   $table.="</table>";
   return $table;
}else{
    return "";
}
}
//**********************************************************
function view_details_gen(){
   
    ?>
                                        <style type="text/css">
                                            .fila_0 { background-color: #FFFFFF;}
                                   .fila_1 { background-color: #E1E8F1;}
                                        </style>
                                       <?php
                                        /*
    $doselectcatalog=  SelectsCatalog();
    $table="<table id='miTabla' border=1 align=center><th></th><th>Nombre</th><th>Descripcion</th><th>Categoria</th><th>Modulo</th>";
    $i=0;
    while($fetch=  mysql_fetch_array($doselectcatalog)){
        $por=$i%2;
        $table.="<tr>
                            <td><a href='customers.php?btn_edit_gen=Editar&det=1&c_id=$_REQUEST[c_id]&ids=$fetch[idsoft]&idm=$fetch[idmodule]&idc=$fetch[idcategory]'><img src='pencil.png' align='top'></a></td>
                            <td class='fila_$por'>$fetch[nombre]</td>
                            <td class='fila_$por'>$fetch[descripcion]</td>
                            <td class='fila_$por'>$fetch[cd]</td>
                            <td class='fila_$por'>".namemodulo($fetch['idsoft'])."</td>
                        </tr>";
        $i++;
    }
    $table.="<tr>
                     <td colspan=5 align=right>
                     <form> 
                     <input type='submit' name='btn_edit_gen' value='AgregarSoft'>
                    <input type='submit' name='btn_edit_gen' value='AgregarModulo'>
                    <input type='submit' name='btn_edit_gen' value='AgregarCategory'>
                    </td>
                     <input type='hidden' name='det' value=1>
                     <input type='hidden' name='c_id' value=".$_REQUEST['c_id'].">
	</form></td></tr>
        </table>";
    echo $table;*/
    //-----------------------------------------------------------------------------

$sql="SELECT custname FROM CRMcustomer WHERE id=".$_REQUEST['c_id']."";
$res=mcq($sql,$db);
$nombre=mysql_result($res,0,0);

$datos="SELECT * FROM CRMdetgen WHERE dg_idc=".$_REQUEST['c_id']."";
$result=mcq($datos,$db);
$data=mysql_fetch_array($result);
if (mysql_num_rows($result)>0){
$puertos=unserialize($data['dg_puertos']);
}

if ($data['dg_modo_cnx']==0){
$modo="NA";
}
elseif($data['dg_modo_cnx']==1){
$modo="Maestro";
}
elseif($data['dg_modo_cnx']==2){
$modo="Esclavo";
}
//Detalles->Ver Informacion General
echo  "
	<table width='80%' border=0>
		<tr>
			<td>&nbsp;</td>
			<td>

				<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$nombre</font>&nbsp;</legend><br><br>
					
					<fieldset><legend>Generales</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'>Numero de estacion:</td>
							<td>".$data['dg_numest']."</td>
						</tr>
						<tr>
							<td width='150'>Nombre:</td>
							<td>".$data['dg_nombre']."</td>
						</tr>
						<tr>
							<td width=150>Nombre de estacion: </td>
							<td>".$data['dg_numest']."</td>
						</tr>
						<tr>
							<td width='150'>Direccion:</td>
							<td>".$data['dg_direccion']."</td>
						</tr>
						<tr>
							<td width='150'>Ciudad:</td>
							<td>".$data['dg_ciudad']."</td>
						</tr>
						<tr>
							<td width='150'>Telefono:</td>
							<td>".$data['dg_telefono']."</td>
						</tr>
						<tr>
							<td width='150'>Contacto:</td>
							<td>".$data['dg_contacto']."</td>
						</tr>
                        <tr>
                            <td width='150'>Puerto HTTP:</td>
                            <td>".$data['dg_puerto_http']."</td>
                        </tr>
                        <tr>
                            <td width='150'>Puerto SSH:</td>
                            <td>".$data['dg_puerto_ssh']."</td>
                        </tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Host Name</legend>
					<table width='100%' class='crm'>
						<tr>
							<td width='150'>Interno:</td>
							<td>".$data['dg_host_int']."</td>
						</tr>
						<tr>
							<td width='150'>Externo:</td>
							<td>".$data['dg_host_ext']."</td>
						</tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Conexion a Internet</legend>
					<table width='100%' class='crm'>
						<tr>
							<td width='150'>Modo:</td>
							<td colspan=3>".$modo."</td>
						</tr>
						<tr>
							<td colspan=4><b><u>Router</u></b></td>
						</tr>
						<tr>
							<td width='150'>Marca:</td>
							<td colspan=3>".$data['dg_rout_marca']."</td>
						</tr>
						<tr>
							<td width='150'>Modelo:</td>
							<td colspan=3>".$data['dg_rout_mod']."</td>
						</tr>
						<tr>
							<td width='150'>IP Address:</td>
							<td colspan=3>".$data['dg_rout_ip']."</td>
						</tr>
						<tr>
							<td colspan=4><b><u>Puertos Abiertos</u></b></td>
						</tr>
						<tr>
							<td width='150'><b>Interno</b></td>
							<td width='150'><b>Externo</b></td>
							<td width='150'><b>Nombre</b></td>
							<td></td>
						</tr>";
						if (count($puertos)>0){
						foreach($puertos as $value){
						$puerto= explode("|",$value);
						echo "<tr><td>".$puerto[0]."</td><td>".$puerto[1]."</td><td>".$puerto[2]."</td><td></td></tr>";
						}
						}
					echo "
					</table>
					</fieldset>
					
					<br><hr><br>";				
 
                                        
//*********************************************************
				$sql="SELECT CUSTOMERREADONLY FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id=$GLOBALS[USERID]";
				$result=mcq($sql,$db);
				$customer_readonly=mysql_result($result,0,0);
				//******************************************
				if ($customer_readonly!='1'){
				echo "
				<form>
				<table width=100% border=0>
					<td align='right'>
						
						<input type='hidden' name='det' value=1>
						<input type='hidden' name='c_id' value=".$_REQUEST['c_id'].">
				</table>
				</form>";
				}
				echo "
				</fiedset>	</td></tr>
	</table><br><br><br><br>";
}
//**********************************************************
function edit_details_gen(){
    /*
    $categoria="Select * From CRMsoft_categories";
    $docategoia=mcq($categoria,$db);
    $soft="Select * From CRMsoft_catalog";
    $dosoft=  mcq($soft, $db);
              $module="Select * From CRMsoft_modules";
    $domodule=  mcq($module, $db);
    if($_REQUEST['btn_edit_gen']!='Editar'){
//*******************agregr
$form="<form>
    <fieldset width='200px'><legend>Catalogo de Software</legend>
    <table border=1 align=center>
                    <tr>";
                        if($_REQUEST['btn_edit_gen']=='AgregarSoft'){
                            $form.="<td bgcolor='E1E8F1'><b>Nombre:</b></td>
                                            <td><input type='text' name='softname'>
                                            <input type='hidden' name='software'  value='inserts'></fieldset></td>
                     </tr>
                     <tr>
                        <td width='150' bgcolor='E1E8F1'><b>Descripcion:</b></td>
                        <td><input type='text' name='softdescription'></td>
                    </tr>";
                             $form.="<tr>
                                                <td width='150' bgcolor='E1E8F1'><b>Categoria:</b></td>
                                                <td><Select name='softcategory' style='width:132px;'><option value='0' selected></option>";
                                                while($fet=  mysql_fetch_array($docategoia)){
                                                $form.="<option value='$fet[idcategory]'>$fet[descripcion]</option>";
                                                }
                            $form.="</td>
                                            </tr>";
                        }elseif($_REQUEST['btn_edit_gen']=='AgregarModulo'){
                        $form.="<tr>
                                            <td width='150' bgcolor='E1E8F1'><b>Modulo:</b></td>
                                            <td><input type='text' name='softmodulo'>
                                            <input type='hidden' name='module' value='insertm'></td>      
                      </tr>";
                         $form.="<tr>
                                              <td width='150' bgcolor='E1E8F1'><b>Software:</b></td>
                                              <td><Select name='soft' style='width:132px;'>
                                              <option value='0' selected></option>";
                                              while($fet=  mysql_fetch_array($dosoft)){
                                               $form.="<option value='$fet[idsoft]'>$fet[nombre]</option>";
                                                }
                            $form.="</td>
                                        </tr>
                                        <tr>";
                        }elseif($_REQUEST['btn_edit_gen']=='AgregarCategory'){
                            $form.="<td  bgcolor='E1E8F1'><b>Categoria:</b></td>
                                            <td><input type='text' name='categoryname'>
                                            <input type='hidden' name='category'  value='insertc'></td>";
                        }
                    $form.="</tr>
                            <tr>
                                    <td colspan='2' align='right'>
		<input type='submit' name='btn_mod_gen' value='Aceptar'>
		<input type='hidden' name='det' value=1>
		<input type='hidden' name='c_id' value=".$_REQUEST['c_id'].">
                                        </td>
                            </tr>";
                    $form.="</table></fieldset></form>";
                //******************************
    }else{
        $softw="Select  * From CRMsoft_catalog where idsoft=$_REQUEST[ids]";
        $dosoftw=mcq($softw,$db);
        $sof=  mysql_fetch_array($dosoftw);
      $form="<form>
    <fieldset width='200px'><legend>Catalogo de Software</legend>
    <table border=1 align=center>
                    <tr>";
      if($_REQUEST['module']!='1'){
                            $form.="<td bgcolor='E1E8F1'><b>Nombre:</b></td>
                                            <td><input type='text' name='softname' value=$sof[nombre]>
                                            <input type='hidden' name='software'  value='$sof[idsoft]'></fieldset></td>
                     </tr>
                     <tr>
                        <td width='150' bgcolor='E1E8F1'><b>Descripcion:</b></td>
                        <td><input type='text' name='softdescription' value='$sof[descripcion]'></td>
                    </tr>";
                             $form.="<tr>
                                                <td width='150' bgcolor='E1E8F1'><b>Categoria:</b></td>
                                                <td><Select name='softcategory' style='width:132px;'><option value='0' selected></option>";
                                                 while($fet=  mysql_fetch_array($docategoia)){
                                                    if($fet['idcategory']==$_REQUEST['idc']){                                                                                                                                                  
                                                    $form.= "<Option value='$fet[idcategory]' selected>$fet[descripcion]</option>";    
                                                   }else{
                                                    $form.= "<Option value='$fet[idcategory]' >$fet[descripcion]</option>";
                                                   }
                                                 }
                                                                                                                
                    $form.="</td>
                                        </tr>
                            <tr>
                                    <td colspan='2' align='right'>
		<input type='submit' name='btn_mod_gen' value='Guardar'>
		<input type='hidden' name='det' value=1>
		<input type='hidden' name='c_id' value=".$_REQUEST['c_id']."> </td> </tr>";
                    
        }else{
             $selemodulo="Select * From CRMsoft_modules where idsoft='$_REQUEST[ids]' and idmodule='$_REQUEST[idm]'" ;
                                                 $doselemodulo=  mcq($selemodulo, $db);             
                                                 $res=  mysql_fetch_array($doselemodulo);
              $form.="<tr>
                                              <td width='150' bgcolor='E1E8F1'><b>Software Name:</b></td>
                                              <td><input type='text' name='softname' value='$sof[nombre]' disabled='true'></td>                                          
                             </tr>
                                <tr>
                                        <td width='150' bgcolor='E1E8F1'><b>Module:</b>
                                                 <input type='hidden' name='idm'  value='$_REQUEST[idm]'>
                                                 <input type='hidden' name='ids'  value='$_REQUEST[ids]'></td>
                                          <td><input type='text' name='moduloname' value=$res[nombre]></td>";
                                                            
                                $form.="</tr>tr>
                                    <td colspan='2' align='right'>
		<input type='submit' name='btn_mod_gen' value='EditarModulo'>
		<input type='hidden' name='det' value=1>
		<input type='hidden' name='c_id' value=".$_REQUEST['c_id']."> </td> </tr>";
                    }
                    $form.="
                           </table></fieldset></form>";
    }
    echo $form;*/
    
$sql="SELECT custname FROM CRMcustomer WHERE id=".$_REQUEST['c_id']."";
$res=mcq($sql,$db);
$nombre=mysql_result($res,0,0);

$datos="SELECT * FROM CRMdetgen WHERE dg_idc=".$_REQUEST['c_id']."";
$data=mysql_fetch_array(mcq($datos,$db));
$puertos=unserialize($data['dg_puertos']);
echo  "
<form>
	<table width='80%' border=0>
		<tr>
			<td>&nbsp;</td>
			<td>
				<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$nombre</font>&nbsp;</legend><br><br>
					
					<fieldset><legend>Generales</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'>Numero de estacion:</td>
							<td><input type='text' name='numest' value='".$data['dg_numest']."'></td>
						</tr>
						<tr>
							<td width='150'>Nombre:</td>
							<td><input type='text' name='nombre' value='".$data['dg_nombre']."' size=40></td>
						</tr>
						<tr>
							<td width=150>Nombre de estacion: </td>
							<td><input type='text' name='nomest' value='".$data['dg_nomest']."'></td>
						</tr>
						<tr>
							<td width='150'>Direccion:</td>
							<td><input type='text' name='direccion' value='".$data['dg_direccion']."' size=70></td>
						</tr>
						<tr>
							<td width='150'>Ciudad:</td>
							<td><input type='text' name='ciudad' value='".$data['dg_ciudad']."'></td>
						</tr>
						<tr>
							<td width='150'>Telefono:</td>
							<td><input type='text' name='telefono' value='".$data['dg_telefono']."'></td>
						</tr>
						<tr>
							<td width='150'>Contacto:</td>
							<td><input type='text' name='contacto' value='".$data['dg_contacto']."'></td>
						</tr>
                        <tr>
                            <td width='150'>Puerto HTTP:</td>
                            <td><input type='text' name='puertohttp' value='".$data['dg_puerto_http']."'></td>
                        </tr>
                        <tr>
                            <td width='150'>Puperto SSH:</td>
                            <td><input type='text' name='puertossh' value='".$data['dg_puerto_ssh']."'></td>
                        </tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Host Name</legend>
					<table width='100%' class='crm'>
						<tr>
							<td width='150'>Interno:</td>
							<td><input type='text' name='host_int' value='".$data['dg_host_int']."'></td>
						</tr>
						<tr>
							<td width='150'>Externo:</td>
							<td><input type='text' name='host_ext' value='".$data['dg_host_ext']."'></td>
						</tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					";
					if ($data['dg_modo_cnx']==0){
					$cnx0="SELECTED";
					$cnx1="";
					$cnx2="";
					}
					elseif($data['dg_modo_cnx']==1){
					$cnx0="";
					$cnx1="SELECTED";
					$cnx2="";
					}
					elseif($data['dg_modo_cnx']==2){
					$cnx0="";
					$cnx1="";
					$cnx2="SELECTED";
					}
					echo "
					<fieldset><legend>Conexion a Internet</legend>
					<table width='100%' class='crm'>
						<tr>
							<td width='150'>Modo:</td>
							<td colspan=3>
								<select name='modo_cnx'>
								<option value=0 $cnx0></option>
								<option value=1 $cnx1>Maestro</option>
								<option value=2 $cnx2>Esclavo</option>
								</select>
								
							</td>
						</tr>
						<tr>
							<td colspan=4><b><u>Router</u></b></td>
						</tr>
						<tr>
							<td width='150'>Marca:</td>
							<td colspan=3><input type='text' name='rout_marca' value='".$data['dg_rout_marca']."'></td>
						</tr>
						<tr>
							<td width='150'>Modelo:</td>
							<td colspan=3><input type='text' name='rout_mod' value='".$data['dg_rout_mod']."'></td>
						</tr>
						<tr>
							<td width='150'>IP Address:</td>
							<td colspan=3><input type='text' name='rout_ip' value='".$data['dg_rout_ip']."' maxlength=15></td>
						</tr>
					</table>
					<table width='100%' class='crm' id='tbl_ports'>
						<tr>
							<td colspan=4><b><u>Puertos Abiertos</u></b></td>
						</tr>
						<tr>
							<td width='150'><b>Interno</b></td>
							<td width='150'><b>Externo</b></td>
							<td width='150'><b>Nombre</b></td>
							<td><input type='button' value='A&ntildeadir' onclick='addRowToTable();' />&nbsp;&nbsp;<input type='button' value='Eliminar' onclick='removeRowFromTable();' /></td>
						</tr>";
						foreach($puertos as $value){
						$puerto= explode("|",$value);
						echo "<tr>
										<td width=150><input type='text' name='int_port[]' value='".$puerto[0]."'></td>
										<td width=150><input type='text' name='ext_port[]' value='".$puerto[1]."'></td>
										<td width=150><input type='text' name='nom_port[]' value='".$puerto[2]."'></td>
										<td></td>
									</tr>";
						}
						echo "
					</table>
					</fieldset>
					
					<br><hr><br>
				
				<table width=100% border=0>
					<tr>
						<td align='right'>
						<input type='submit' name='btn_mod_gen' value='Aceptar'>
						<input type='hidden' name='det' value=1>
						<input type='hidden' name='c_id' value=".$_REQUEST['c_id'].">
						</td>
					</tr>
				</table>

				</fiedset>
			</td>
		</tr>
		
	
	</table><br><br><br><br>
</form>
	";
?>
<script type="text/javascript">
function addRowToTable()
{
  var tbl = document.getElementById('tbl_ports');
  var lastRow = tbl.rows.length;
  // if there's no header row in the table, then iteration = lastRow + 1
  var iteration = lastRow;
  var row = tbl.insertRow(lastRow);
  
  if (iteration>6){
  return false;
  }
  
  var cell1 = row.insertCell(0);
  var input1 = document.createElement('input');
  input1.type='text';
    input1.name='int_port[]';
  cell1.appendChild(input1);
  
  var cell2 = row.insertCell(1);
  var el = document.createElement('input');
  el.type = 'text';
  el.name = 'ext_port[]';
  //el.id = 'txtRow' + iteration;
  //el.size = 40;
  
  //el.onkeypress = keyPressTest;
  cell2.appendChild(el);
  
  var cell3 = row.insertCell(2);
  var input3 = document.createElement('input');
  input3.type='text';
  input3.name='nom_port[]';
  cell3.appendChild(input3);
  
  var cell4 = row.insertCell(3);
}
function removeRowFromTable()
{
  var tbl = document.getElementById('tbl_ports');
  var lastRow = tbl.rows.length;
  if (lastRow > 2) tbl.deleteRow(lastRow - 1);
}
</script>
<?php     
}
//**********************************************************
function mod_details_gen(){
    /*
    if($_REQUEST['btn_mod_gen']=='Guardar'){
        $update="Update CRMsoft_catalog set nombre='$_REQUEST[softname]',descripcion='$_REQUEST[softdescription]',idcategory='$_REQUEST[softcategory]' where idsoft='$_REQUEST[software]'";
        mcq($update,$db);
    }elseif($_REQUEST['btn_mod_gen']=='EditarModulo'){
    $update="Update CRMsoft_modules set nombre='$_REQUEST[moduloname]' where idsoft='$_REQUEST[ids]' and idmodule='$_REQUEST[idm]'";
        mcq($update,$db);
}else{
    if($_REQUEST['category']=='insertc'){
        $insertcat="Insert Into CRMsoft_categories(descripcion) Values('".$_REQUEST['categoryname']."')";
        mcq($insertcat, $db);
   }elseif($_REQUEST['software']=='inserts'){
    $insert="Insert Into CRMsoft_catalog(nombre,descripcion,idcategory) Values('".$_REQUEST['softname']. "','".$_REQUEST['softdescription']."','".$_REQUEST['softcategory']."')  ";
   mcq($insert, $db); 
   }elseif($_REQUEST['module']=='insertm'){
          $insertmodulo="Insert into CRMsoft_modules(idsoft,nombre) Values('".$_REQUEST[soft]."','".$_REQUEST['softmodulo']."')";
          mcq($insertmodulo,$db);
   }
}
   //print_r($_REQUEST);
   $c_id=$_REQUEST['c_id'];
   echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=customers.php?view_det_gen=Ver+Informacion+General&det=1&c_id=$c_id\">";
  */
$strchk="SELECT * FROM CRMdetgen WHERE dg_idc=".$_REQUEST['c_id']."";
$chk=mcq($strchk,$db);

$int_port=$_REQUEST['int_port'];
$ext_port=$_REQUEST['ext_port'];
$nom_port=$_REQUEST['nom_port'];
$puertos = array();
foreach ($int_port as $key => $value){
$puerto=$int_port[$key]."|".$ext_port[$key]."|".$nom_port[$key];
if ($int_port[$key]!=""){
$puertos[]=$puerto;
}
}
$puertos=serialize($puertos);


if (mysql_num_rows($chk)==0){
$strsql="INSERT INTO CRMdetgen(dg_idc,dg_numest,dg_nombre,dg_nomest,dg_direccion,dg_ciudad,dg_telefono,dg_contacto,dg_host_int,dg_host_ext,dg_modo_cnx,dg_rout_marca,dg_rout_mod,dg_rout_ip,dg_puertos,dg_puerto_http,dg_puerto_ssh) VALUES(
				'".$_REQUEST['c_id']."','".$_REQUEST['numest']."','".$_REQUEST['nombre']."','".$_REQUEST['nomest']."','".$_REQUEST['direccion']."','".$_REQUEST['ciudad']."','".$_REQUEST['telefono']."','".$_REQUEST['contacto']."',
				'".$_REQUEST['host_int']."','".$_REQUEST['host_ext']."',".$_REQUEST['modo_cnx'].",'".$_REQUEST['rout_marca']."','".$_REQUEST['rout_mod']."','".$_REQUEST['rout_ip']."','$puertos','".$_REQUEST['puertohttp']."','".$_REQUEST['puertossh']."')";
}
else{
$strsql="UPDATE CRMdetgen SET dg_numest='".$_REQUEST['numest']."', dg_nombre='".$_REQUEST['nombre']."', dg_nomest='".$_REQUEST['nomest']."', dg_direccion='".$_REQUEST['direccion']."', dg_ciudad='".$_REQUEST['ciudad']."',
				dg_contacto='".$_REQUEST['contacto']."', dg_host_int='".$_REQUEST['host_int']."', dg_host_ext='".$_REQUEST['host_ext']."', dg_modo_cnx=".$_REQUEST['modo_cnx'].", dg_rout_marca='".$_REQUEST['rout_marca']."', 
				dg_rout_mod='".$_REQUEST['rout_mod']."',dg_rout_ip='".$_REQUEST['rout_ip']."', dg_puertos='$puertos'
                ,dg_puerto_http='".$_REQUEST['puertohttp']."',dg_puerto_ssh='".$_REQUEST['puertossh']."' 
                WHERE dg_idc='".$_REQUEST['c_id']."'";
}
mcq($strsql,$db);
$c_id=$_REQUEST['c_id'];
echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=customers.php?det=1&c_id=$c_id\">";
}
//*************Ver Los Programas Instalados***************
function view_details_prog(){?>
                                        <style type="text/css">
                                    .fila_0 { background-color: #FFFFFF;}
                                    .fila_1 { background-color: #E1E8F1;}
                                        </style>
                                        <?php
$sql="SELECT custname FROM CRMcustomer WHERE id=".$_REQUEST['c_id']."";
$res=mcq($sql,$db);
$nombre=mysql_result($res,0,0);

$datos="SELECT * FROM CRMdetprog WHERE dp_idc=".$_REQUEST['c_id']."";
$result=mcq($datos,$db);
$data=mysql_fetch_array($result);
if (mysql_num_rows($result)>0){
$prog=unserialize($data['dp_soft']);
}
$version="Select CRMsoft_catalog.nombre as cn,CRMsoft_server.version,CRMsoft_server.estatus ,CRMsoft_categories.descripcion,CRMsoft_catalog.idsoft,CRMsoft_server.id
                    From CRMsoft_server INNER JOIN CRMsoft_catalog  INNER JOIN CRMsoft_categories
                    ON CRMsoft_server.idsoft=CRMsoft_catalog.idsoft 
                    AND CRMsoft_catalog.idcategory=CRMsoft_categories.idcategory
                    where CRMsoft_server.custid='$_REQUEST[c_id]' order by CRMsoft_server.estatus ASC";
$doversion=mcq($version,$db);

echo  "
	<table width='80%' >
		<tr>
			<td>&nbsp;</td>
			<td>
				<fieldset><legend>&nbsp;<a href='customers.php?search_name=&search=&c_id=$_REQUEST[c_id]&det=1&uitgeklapt=&nonavbar=&zoek=Search&tab=26'>
                                                                                                <img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$nombre</font>&nbsp;</legend></a><br><br>					
					<fieldset>
                                                                                                      <table class='crm' width='100%'><tr>
                                                                                                    <td width='100' align='left' bgcolor='#E1E8F1'style='font-size:14px;'><img src='server.png'><b>SERVIDOR</b></td>";
                                                                                                    if(isadmin($GLOBALS[USERID])){                                                                                          
                                                                                                    echo "<td width='3%' align='center'><a href='customers.php?btn_edit_prog=AgregarSoftware&det=1&c_id=$_REQUEST[c_id]'><img src='add.png' align='top'></a>";                                                                                         
                                                                                                     }
                                                                                                    echo "</td></tr></table>
					<table class='crm' width='100%'>";
                                                                                                                                            if(mysql_num_rows($doversion)>=1){
                                                                                                                                            echo "<th width='150' align='left' bgcolor='#D8D8D8'>Nombre</th>
							<th width='150' align='left'bgcolor='#D8D8D8'>Version</th>							
                                                                                                                                            <th width='150' align='left' bgcolor='#D8D8D8'>Modulos</th>
                                                                                                                                            <th width='150' align='left' bgcolor='#D8D8D8'>Categoria</th>
                                                                                                                                            <th align='left' bgcolor='#D8D8D8'>status</th>";
                                                                                                                                            }else{
                                                                                                                                                echo  "<th align='center' bgcolor='#D8D8D8' width=2000>No Existe Software</th>";
                                                                                                                                            }
                                                                                                                                              
                                                                                                                                $i = 0 ;
                                                                                                                            while($fe=  mysql_fetch_array($doversion)){
                                                                                                                                $por=$i%2;
                                                                                                                                if($fe[estatus]=='activo'){
                                                                                                                                   $color='76B062';
                                                                                                                               }else{$color="F08282";}
						    echo"  <tr>
                                                                                                                                      <td class='fila_$por' width='150' align='center'>$fe[cn]</td>
							<td class='fila_$por' width='150' align='center'>$fe[version]</td>							
                                                                                                                                            <td class='fila_$por' width='150' align='left'>". modulos($fe[id])."</td>
                                                                                                                                            <td class='fila_$por' width='150' align='center'>$fe[descripcion]</td>
                                                                                                                                            <td width='63' align='center' bgcolor=$color>$fe[estatus]</td>";
                                                                                                                                                    if(isadmin($GLOBALS[USERID])){  
                                                                                                                                             echo "<td width='10' align='center'><a href='customers.php?btn_edit_prog=Añadir&det=1&ids=$fe[id]&accion=edit&idca=$fe[idsoft]&c_id=$_REQUEST[c_id]&nombre=$fe[cn]'><img src='gtk-edit.png' align='top'></a></td> ";
                                                                                                                                                    }
						 echo "</tr>";
                                                                                                                                           $i++;
                                                                                                                                            }
					echo "</table>
					</fieldset><br>";
				//*********************************************************
				$sql="SELECT CUSTOMERREADONLY FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id=$GLOBALS[USERID]";
				$result=mcq($sql,$db);
				$customer_readonly=mysql_result($result,0,0);
				//******************************************
				if ($customer_readonly!='1'){
				echo "
				<form>
				<table width=100% border=0>
					<tr>
						<td align='right'>";
						//<input type='submit' name='btn_edit_prog' value='AgregarSoftware'>
						echo "<input type='hidden' name='det' value=1>
						<input type='hidden' name='c_id' value=".$_REQUEST['c_id'].">
						</td>
					</tr>
				</table>
				</form>";
				}
                                 /*
                                //----------Dispensarios-----------------
                                $versionserver="SELECT CRMdispenser.nodispenser,CRMsoft_pumps.idpump,CRMsoft_pumps.version,CRMsoft_catalog.nombre,CRMsoft_categories.descripcion,CRMsoft_pumps.estatus,CRMsoft_pumps.id,CRMsoft_catalog.idsoft
                                    From CRMsoft_pumps INNER JOIN CRMsoft_catalog INNER JOIN CRMsoft_categories INNER JOIN CRMdispenser
                                    On CRMsoft_pumps.idsoft=CRMsoft_catalog.idsoft and CRMsoft_catalog.idcategory=CRMsoft_categories.idcategory and CRMsoft_pumps.idpump=CRMdispenser.id 
                                    where CRMdispenser.idcustomer='$_REQUEST[c_id]' order by CRMsoft_pumps.estatus,CRMdispenser.nodispenser ASC";
                                    $doversionserver=mcq($versionserver,$db);
                                     $dispensario="Select nodispenser From CRMdispenser where idcustomer=$_REQUEST[c_id]";
                                        $dodispenser=mcq($dispensario,$db);
                                                                                    echo"<br><hr><br>
                                                                                                        <fieldset><legend>Software De Dispensario</legend>
					<table class='crm' width='100%'>
                                                                                                                                            <th width='150' align='left'>No.Dispensario</th>
                                                                                                                                            <th width='150' align='left'>Software De Dispensario</th>
							<th width='150' align='left'>Version</th>							
                                                                                                                                            <th width='150' align='left'>Categoria</th>
                                                                                                                                            <th width='150' align='left'>status</th>";
                                                                                                                    
                                                                                                                                 $i = 0 ;
                                                                                                                                  while($fes=  mysql_fetch_array($doversionserver)){
                                                                                                                                      $por=$i%2;
                                                                                                                                if($fes[estatus]=='activo'){
                                                                                                                                   $color='76B062';
                                                                                                                               }else{$color="F08282";
                                                                                                                               }
                                                                                                                           echo "<tr>
                                                                                                                                            <td class='fila_$por' width='150' align='center'>Dispensario $fes[nodispenser]<input type='hidden' name='ids' value='$fes[id]'></td>
							<td class='fila_$por' width='150' align='center'>$fes[nombre]</td>
							<td class='fila_$por' width='150' align='center'>$fes[version]</td>							
                                                                                                                                            <td class='fila_$por' width='150' align='center'>$fes[descripcion]</td>
                                                                                                                                            <td width='150' align='center' bgcolor=$color>$fes[estatus]</td>
                                                                                                                                             <td width='10'><a href='customers.php?btn_edit_prog=Añadir&det=1&ids=$fes[id]&accion=edits&idca=$fes[idsoft]&c_id=$_REQUEST[c_id]&nombre=$fes[nombre]'><img src='pencil.png' align='top'></a>                                                                                                                                     						
						</tr>";
                                                                                                                           $i++;
                                                                                                                                             }
                                                                                                                          
					echo "</table></fieldset>";*/
                                        //********************* dispensarios**********************************************************
                                        $dispensario="Select id,nodispenser From CRMdispenser where idcustomer='$_REQUEST[c_id]' ORDER BY CRMdispenser.nodispenser ASC";
                                        $dodispenser=mcq($dispensario,$db);
                                        echo"<br><hr><br>
                                                                               <fieldset>
					<table class='crm' width='100%'>";
                                                                                                                                  while($fes=  mysql_fetch_array($dodispenser)){ 
                                                                                                                           echo "<tr>
                                                                                                                                            <td width='150' align='left' bgcolor='#E1E8F1' style='font-size:14px;'><img src='dispensario.png' align='bottom'><b>DISPENSARIO $fes[nodispenser]</b><input type='hidden' name='ids' value='$fes[id]'></td>";
                                                                                                                                   if(isadmin($GLOBALS[USERID])){  
                                                                                                                                echo" <td width='3%' align='center'><a href='customers.php?btn_edit_prog=AgregarDispensario&det=1&c_id=$_REQUEST[c_id]&nodispenser=$fes[nodispenser]&id=$fes[id]'><img src='add.png' align='top'></a>";                                                                                                                                     						           
                                                                                                                                   }
                                                                                                                                echo "</tr>
                                                                                                                                      
                                                                                                                            <tr>
                                                                                                                                      <td >
                                                                                                                                           <table>";
                                                                                                                                           
                                                                                                                                          
                                                                                                                           $selectdis="SELECT CRMsoft_catalog.nombre,CRMdispenser.nodispenser,CRMdispenser.idcustomer,CRMdispenser.id,CRMsoft_pumps.idsoft,CRMsoft_pumps.estatus,CRMsoft_pumps.version,
                                                                                                                                                  CRMsoft_pumps.id,CRMsoft_categories.descripcion
                                                                                                                                            FROM CRMdispenser INNER JOIN CRMsoft_pumps INNER JOIN CRMsoft_catalog INNER JOIN CRMsoft_categories 
                                                                                                                                            ON CRMdispenser.id=CRMsoft_pumps.idpump and CRMsoft_pumps.idsoft=CRMsoft_catalog.idsoft and CRMsoft_catalog.idcategory=CRMsoft_categories.idcategory
                                                                                                                                            WHERE CRMdispenser.idcustomer='$_REQUEST[c_id]' and CRMdispenser.nodispenser='$fes[nodispenser]'";
                                                                                                                           $doselectdis=mcq($selectdis,$db);   
                                                                                                                           if(mysql_num_rows($doselectdis)>=1){
                                                                                                                                 echo "<th align=left bgcolor='#D8D8D8'>Nombre de Software</th>
                                                                                                                                                <th align=left bgcolor='#D8D8D8'>Version</th>
                                                                                                                                                <th align=left bgcolor='#D8D8D8'>Categoria</th>
                                                                                                                                                <th align=left bgcolor='#D8D8D8'>status</th>";
                                                                                                                           }else{
                                                                                                                              echo  "<th align='center' bgcolor='#D8D8D8' width=2000>No Existe Software</th>";
                                                                                                                           }
                                                                                                                                  while($fetch=  mysql_fetch_array($doselectdis)){
                                                                                                                                if($fetch[estatus]=='activo'){
                                                                                                                                   $color='76B062';
                                                                                                                               }else{$color="F08282";
                                                                                                                               }
                                                                                                                           echo "<tr>
                                                                                                                                            <td  width='450' align='center'>$fetch[nombre]</td>
							<td  width='450' align='center'>$fetch[version]</td>
							<td  width='450' align='center'>$fetch[descripcion]</td>							
                                                                                                                                            <td width='150' align='center' bgcolor=$color>$fetch[estatus]</td>";
                                                                                                                                                    if(isadmin($GLOBALS[USERID])){  
                                                                                                                                             echo "<td width='20' align='center'><a href='customers.php?btn_edit_prog=Añadir&det=1&ids=$fetch[id]&accion=edits&idca=$fetch[idsoft]&c_id=$_REQUEST[c_id]&nombre=$fetch[nombre]'><img src='gtk-edit.png' align='top'></a>";
                                                                                                                                                    }
                                                                                                                                             echo "</tr>";                                                                                                                        
                                                                                                                                             }
                                                                                                                                          echo "</table>
                                                                                                                                      </td>
                                                                                                                               </tr><tr><td colspan='2'><br></td><tr>";                                                                                                                        
                                                                                                                                             }                                                     
					echo "</table></fieldset>";
                                        //********************************************************************************************************************
                                        if ($customer_readonly!='1'){
				echo "<br><form>
				<table width=100% border=0>
					<tr>
						<td align='right'>";
                                                                                                                        if(isadmin($GLOBALS[USERID])){
						echo "<input type='submit' name='view_det_disp' value='AgregarDispensario'>";
                                                                                                                        }
                                                                                                                        echo "<input type='hidden' name='det' value=1>
						<input type='hidden' name='c_id' value=".$_REQUEST['c_id'].">
						</td>
					</tr>
                                        <tr><td></td></tr>
                                        
				</table>
				</form>
				 <br><hr><br>  
                                                                                 ";
				}
                                
				/*
				echo "
				</fiedset>
			</td>
		</tr>
	</table><br><br><br><br>
	";*/
}
//***Checar el tipo de Usuario
function isadmin($user){
 $useradmin="Select flag_admin From CRMloginusers where id='$user'";
    $douseradmin=  mcq($useradmin, $db);
    $fetch=  mysql_fetch_array($douseradmin);
    if($fetch['flag_admin']=='1'){
        return true;
    }
}
//*************Checar Modulos de Programas**************
function modulos($idsoft){
  $modulos="SELECT CRMsoft_modules.idmodule,CRMsoft_modules.nombre as n,CRMsoft_server_modules.idinstall,CRMsoft_server_modules.idmodule 
    FROM CRMsoft_server_modules INNER JOIN CRMsoft_modules ON CRMsoft_server_modules.idmodule=CRMsoft_modules.idmodule
    where CRMsoft_server_modules.idinstall='$idsoft' and CRMsoft_server_modules.estatus='activo'";
    $domodulos=  mcq($modulos, $db);
    if(mysql_num_rows($domodulos)>=1){
        $table="";
       while($f=  mysql_fetch_array($domodulos)){
       $table.=" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-".$f[n]."<br>";
   }
   return $table;
}else{
    return "";
}
}
//**************Software disponible************************
function programasrestantes($customer){
    $restantes="select CRMsoft_catalog.idsoft,CRMsoft_catalog.nombre from 
                           CRMsoft_catalog where CRMsoft_catalog.idsoft not in (select CRMsoft_server.idsoft from CRMsoft_server where CRMsoft_server.custid='$customer') and CRMsoft_catalog.idcategory<>'2'";
    $dorestantes=mcq($restantes,$db);
    $select="<select name='softname' style='width:150px;'>
                    <option value='0'></option>";
    while($res=  mysql_fetch_array($dorestantes)){
        $select.="<option value=$res[idsoft]>$res[nombre]</option>";
    }
    $select.="</select>";
    return $select;
    
}
//**************Software Disponible para dispensarios*****
function dispensariosrestantes($customer){
 
    $restantes="select idsoft,nombre from CRMsoft_catalog where idcategory='2'";
    $dorestantes=mcq($restantes,$db);
    $select="<select name='softname' style='width:150px;' >
                    <option value='0'></option>";
    while($res=  mysql_fetch_array($dorestantes)){
        $select.="<option value=$res[idsoft]>$res[nombre]</option>";
    }
    $select.="</select>";
    return $select;
  
}
//*********Dispensarios Disponibles Por Usuario***********
function dispensarios($customer){
       $selectdis="Select id,nodispenser From CRMdispenser where idcustomer='$_REQUEST[c_id]'";
        $doselectsdis=mcq($selectdis,$db);
        if(mysql_num_rows($doselectsdis)>=1){
               $select="<select id='softname' name='iddispenser' style='width:155px;'  >
                    <option value='0'></option>";
        while($res=  mysql_fetch_array($doselectsdis)){
       $select.="<option value=$res[id]>Dispensario No.-$res[nodispenser]</option>";
        }
    $select.="</select>";
    return $select;       
        }else{
            return $select.="No existe Hardware de Dispensario";
            }        
}
//********Actualizar o Insertar Programa*******************
function edit_details_prog(){
                                       
$sql="SELECT custname FROM CRMcustomer WHERE id=".$_REQUEST['c_id']."";
$res=mcq($sql,$db);
$nombre=mysql_result($res,0,0);
$catalog="Select * From CRMsoft_catalog where idsoft='$_REQUEST[idca]'";
$docatalog=  mcq($catalog, $db);
$recatalogo=  mysql_fetch_array($docatalog);
$catalogo="Select * From CRMsoft_catalog";
$docatalogo=  mcq($catalogo, $db);

$modulos="Select * From CRMsoft_modules where idsoft='$_REQUEST[idca]'";
$domodulos=mcq($modulos,$db);
if($_REQUEST['accion']=='edit'){
    $sever="Select * From CRMsoft_server where id='$_REQUEST[ids]'";
    $do=mcq($sever,$db);
    $e=  mysql_fetch_array($do);
    if($e['estatus']=='activo'){
        $s='selected';
        $a='';
    }else{
        $s='';
        $a='selected';
    }
}
if($_REQUEST['accion']=='edits'){
     $severs="Select * From CRMsoft_pumps where id='$_REQUEST[ids]'";
    $dos=mcq($severs,$db);
    $e=  mysql_fetch_array($dos);
        if($e['estatus']=='activo'){
        $s='selected';
        $a='';
    }else{
        $s='';
        $a='selected';
    }
}
echo  "<form><table width='80%' border=0>
		<tr><td>&nbsp;</td>
			<td><fieldset><legend>&nbsp;<a href='customers.php?search_name=&search=&c_id=$_REQUEST[c_id]&det=1&uitgeklapt=&nonavbar=&zoek=Search&tab=26'>
                                                            <img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$nombre</font>&nbsp;</a></legend><br><br>					
					<fieldset><legend>Software</legend>
					<table class='crm' align='center'>";
                                                                                                                                      // agregamos Un Nuevo Software
                                                                                                                          if($_REQUEST['btn_edit_prog']=='AgregarSoftware'){
                                                                                                                              echo"<tr><td width='100' bgcolor='E1E8F1' colspan=2 align='center'><b>Agregar Software</b><br><br></td>";
                                                                                                                                               echo"<tr><td width='100' bgcolor='E1E8F1'><b>Programa</b></td>";
                                                                                                                                               echo "<td>";
                                                                                                                                               echo programasrestantes($_REQUEST['c_id']);      
                                                                                                                                               echo "</td></tr>";
                                                                                                                                               echo"<tr><td width='100' bgcolor='E1E8F1'><b>Version</b></td>
                                                                                                                                                                  <td width='150'><input type='text' name='version' style='width:150px;'></td></tr>";
                                                                                                                                       //Nuevo Software de Dispensario
                                                                                                                           }elseif($_REQUEST['btn_edit_prog']=='AgregarDispensario'){
                                                                                                                                        //Verificamos Si existe Algun Hardware de  Dispensario para el cliente 
                                                                                                                                                        echo"<tr><td width='100' bgcolor='E1E8F1' colspan=2 align='center'><b>Dispensario ".$_REQUEST['nodispenser']."</b><br>Agregar Software <br></td>";
                                                                                                                                                         echo"<tr><td width='100' bgcolor='E1E8F1'><b>Programa</b></td>";
                                                                                                                                                         echo"<td>";
                                                                                                                                                        echo dispensariosrestantes($_REQUEST['c_id'])."</td></tr>";
                                                                                                                                                        echo"<tr><td width='100' bgcolor='E1E8F1'><b>Version</b></td>
                                                                                                                                                                  <td width='100'><input type='text' name='version' id='version' style='width:150px;'>
                                                                                                                                                                  <input type='hidden' value='Dispensario' name='insertdispenser'>
                                                                                                                                                                  <input type='hidden' value='$_REQUEST[id]' name='iddispenser'>
                                                                                                                                                        <input type='hidden' value='$_REQUEST[nodispenser]' name='nodispenser'></td></tr>";
                                                                                                                                                        if($_REQUEST['show']=='si'){
                                                                                                                                                            $show="<tr><td style='color:red;'>Ya existe el Software</td><tr>";
                                                                                                                                                            echo $show;
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                    //Edicion de Datos(Version,Modulos o Estatus)
                                                                                                                        if($_REQUEST['accion']=='edit' or $_REQUEST['accion']=='edits'){
                                                                                                                              if($_REQUEST['addmodulo']=='yes'){
                                                                                                                                  if(mysql_num_rows($domodulos)>=1){
                                                                                                                                                        echo "<tr><td width='200' bgcolor='E1E8F1' colspan=2 align=center>
                                                                                                                                                            <b> Software $recatalogo[nombre] $e[version]</b><br>Agregar Modulos<br>
                                                                                                                                                                            <input name='edit' type='hidden' value='$_REQUEST[accion]'>
                                                                                                                                                                            <input name='ids' type='hidden' value='$_REQUEST[ids]'></td>";
                                                                                                                                                       if($_REQUEST['accion']=='edit'){
                                                                                                                                                         
                                                                                                                                                        echo "<tr>
                                                                                                                                                                            <td width='100' bgcolor='E1E8F1' align=center><b>Disponibles</b></td>";
                                                                                                                                                        echo "<td width='200' >";            
                                                                                                                                                                    while($fetc=  mysql_fetch_array($domodulos)){
                                                                                                                                                                        $modulos=estupidosmodulos($fetc[idmodule]);
                                                                                                                                                                           if($modulos[idmodule]==$fetc[idmodule] ){
                                                                                                                                                                                echo "<input type='checkbox' value='$fetc[idmodule]' checked name='modulo[]'>$fetc[nombre]
                                                                                                                                                                                <input type='hidden' name='oldactivo[]' value='$fetc[idmodule]'><br>";
                                                                                                                                                                            }else{
                                                                                                                                                                                 echo "<input type='checkbox' value='$fetc[idmodule]' name='modulo[]' >$fetc[nombre]
                                                                                                                                                                                <input type='hidden' name='oldinactivo[]' value='$fetc[idmodule]'><br>";
                                                                                                                                                                            }      
                                                                                                                                                                         }                                                                                                                                        
                                                                                                                                                        echo "</td></tr>";
                                                                                                                                                       }
                                                                                                                                  }else{
                                                                                                                                      echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=customers.php?view_det_prog=Ver+Informacion+de+Programas&det=1&c_id=$_REQUEST[c_id]\">";
                                                                                                                                  }
                                                                                                                                                        
                                                                                                                              }else{
                                                                                                                                                       //Mostramos Nombre De Programa a Editar en Caso de Nno agregar nada 
                                                                                                                                                        echo "<tr><td bgcolor='E1E8F1' colspan=2 align=center><b>Editar Software</b><br><br></td></tr>";
                                                                                                                                                        echo "<tr><td width='100' bgcolor='E1E8F1'><b>Programa</b></td>
                                                                                                                                                                            <td ><Input type='text' name='softname' value=$_REQUEST[nombre] disabled='true' style='width:200px;'></td></tr>";
                                                                                                                                                        echo "<tr><td width='100' bgcolor='E1E8F1'><b>Version</b>
                                                                                                                                                                            <input name='edit' type='hidden' value='$_REQUEST[accion]'>
                                                                                                                                                                            <input name='ids' type='hidden' value='$_REQUEST[ids]'></td>
                                                                                                                                                                            <td width='100'><input type='text' name='version' Value='$e[version]' style='width:200px;'></td></tr>";
                                                                                                                                                       if($_REQUEST['accion']=='edit'){
                                                                                                                                                         
                                                                                                                                                        echo "<tr>
                                                                                                                                                                            <td width='100' bgcolor='E1E8F1'><b>Modulos</b></td>";
                                                                                                                                                        echo "<td width='200'>";            
                                                                                                                                                                    while($fetc=  mysql_fetch_array($domodulos)){
                                                                                                                                                                               $modulos=estupidosmodulos($fetc[idmodule]);
                                                                                                                                                                               //echo $modulos[estatus];
                                                                                                                                                                           if($modulos[idmodule]==$fetc[idmodule] && $modulos[estatus]=='activo'){
                                                                                                                                                                                echo "<input type='checkbox' value='$fetc[idmodule]' checked name='modulo[]'>$fetc[nombre]
                                                                                                                                                                                <input type='hidden' name='oldactivo[]' value='$fetc[idmodule]'><br>";
                                                                                                                                                                            }else{
                                                                                                                                                                                 echo "<input type='checkbox' value='$fetc[idmodule]' name='modulo[]'>$fetc[nombre]</option>
                                                                                                                                                                                <input type='hidden' name='oldinactivo[]' value='$fetc[idmodule]'><br>";
                                                                                                                                                                            }      
                                                                                                                                                                         }                                                                                                                                        
                                                                                                                                                        echo "</td></tr>";
                                                                                                                                                       }
                                                                                                                                                        echo "<tr>
                                                                                                                                                                        <td width='100' bgcolor='E1E8F1'><b>Status</b></td>
                                                                                                                                                                        <td ><Select name='status' style='width:200px;'>
                                                                                                                                                                            <option value='activo' $s >Activo</option>
                                                                                                                                                                            <option value='inactivo' $a >Inactivo</option>
                                                                                                                                                                            </select>
                                                                                                                                                       
                                                                                                                                                                        </td></tr>";
                                                                                                                                                        
                                                                                                                                                                        }
                                                                                                                            }
                                                                                                                            echo "</tr></table></fieldset>					
                                                                                                                                      																	
                                                                                                                                      <table width=21% border=0 align='center'>
                                                                                                                                            <tr>
                                                                                                                                                <td align='right'>
                                                                                                                                                    <input type='submit' name='btn_mod_prog' value='Aceptar'>
                                                                                                                                                    <input type='submit' name='btn_mod_prog' value='Cancelar'>
                                                                                                                                                    <input type='hidden' name='det' value=1>
                                                                                                                                                    <input type='hidden' name='c_id' value=".$_REQUEST['c_id'].">
                                                                                                                                                </td>
                                                                                                                                           </tr>
                                                                                                                                        </table>
                                                                    </fiedset></td></tr></table><br><br><br><br></form>";                                 
}
//************function para agregaR MODULOS
function estupidosmodulos($idmodule){
 $severm="Select * From CRMsoft_server_modules where idinstall='$_REQUEST[ids]' and idmodule='$idmodule'";
    $dom=mcq($severm,$db);
    if(mysql_num_rows($dom)>=1){
       //while(
               $em=  mysql_fetch_array($dom);//){
          // if($idmodule==$em[idmodule] && $em[estatus]=='activo' or $em[estatus]=='inactivo' ){
               return $em;
           //}
              //    }
   }else{
       return "0";
   } 
}
//*************checar cambios en modulos********
function checarmodulos($modulo,$activo,$inactivo){
//------------------Cambian de activos a inactivos-------------------------
        foreach ($activo as $val) {
            $encontrado=false;
            foreach ($modulo as $val2) {
            if ($val == $val2){
            $encontrado=true;
            $break;
        }
    }
    if ($encontrado == false){
        $update="Update CRMsoft_server_modules SET estatus='inactivo' where idinstall='$_REQUEST[ids]' and idmodule='$val'";
        mcq($update,$db);
           //echo "Se desactivo $val software $_REQUEST[ids]<br>\n";
    }
}
//----------------------------Cambian de inactivos a Activos---------
  foreach ($inactivo as $i) {
    foreach ($modulo as $v) {
        if ($i == $v){
            $existe=  estupidosmodulos($v);
            if($existe[idmodulo]!='0'){
            $update="Update CRMsoft_server_modules SET estatus='activo' where idinstall='$_REQUEST[ids]' and idmodule='$i'";
            mcq($update,$db);
            //echo "se activo $i<br>";
                }else{
               //     echo "se inserto $i<br>";
                     $insertmodules="INSERT INTO CRMsoft_server_modules(idinstall,idmodule,estatus) VALUES('$_REQUEST[ids]','$i','activo')";
                  mcq($insertmodules,$db);
                }
             }
        }
    }
    
}
//************Insert-Update en BD de Programas***********
function mod_details_prog(){
    if($_REQUEST['btn_mod_prog']!='Cancelar'){
if($_REQUEST['edit']=='edit'){
    //----------Actualizar soft_server------------------
    if($_REQUEST[version]!=''){
    $update="Update CRMsoft_server SET version='$_REQUEST[version]',estatus='$_REQUEST[status]' where id='$_REQUEST[ids]'";
    mcq($update,$db);
    }
    //---------------------Modulos-------------------------
    checarmodulos($_REQUEST['modulo'],$_REQUEST['oldactivo'],$_REQUEST['oldinactivo']);
    //---------------------------------------------------------
}elseif($_REQUEST['edit']=='edits'){ 
    //editar datos del Software Dispensarios
    if($_REQUEST[version]!=''){
    $update="Update CRMsoft_pumps SET version='$_REQUEST[version]',estatus='$_REQUEST[status]' where id='$_REQUEST[ids]'";
    mcq($update,$db);    
    }
}else{
    //**********Agregar Software de Dispensarios******
    if($_REQUEST['insertdispenser']=='Dispensario'){
        if($_REQUEST['softname']!='0'){
         $restantes="Select CRMsoft_pumps.idsoft From CRMsoft_pumps INNER JOIN CRMdispenser ON CRMsoft_pumps.idpump=CRMdispenser.id 
                                where CRMdispenser.idcustomer='$_REQUEST[c_id]' and CRMdispenser.nodispenser='$_REQUEST[nodispenser]' and CRMsoft_pumps.idsoft='$_REQUEST[softname]'";
         $do=  mcq($restantes, $db);
         $fdis= mysql_fetch_array($do);
         if(mysql_num_rows($do)>=1){
                       header("Location:customers.php?btn_edit_prog=AgregarDispensario&det=1&c_id=$_REQUEST[c_id]&&nodispenser=$_REQUEST[nodispenser]&id=$_REQUEST[iddispenser]&show=si");
         }else{
              $insertdis="INSERT INTO CRMsoft_pumps(idpump,idsoft,version,estatus) VALUES( '".$_REQUEST['iddispenser']."','".$_REQUEST['softname']."','".$_REQUEST['version']."','activo')";
        mcq($insertdis, $db);             
       // echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=customers.php?view_det_prog=Ver+Informacion+de+Programas&det=1&c_id=$c_id\">";
                    }
    }//if num_rows
    }else{
    //**********Agregar Software *********************
        if($_REQUEST['softname']!='0'){
    $insert="INSERT INTO CRMsoft_server(custid,idsoft,version,estatus) VALUES('".$_REQUEST['c_id']."','".$_REQUEST['softname']."','".$_REQUEST['version']."','activo')";
    mcq($insert,$db);
    $ultimo=  mysql_insert_id();
    header("Location:customers.php?btn_edit_prog=Añadir&det=1&ids=$ultimo&accion=edit&idca=$_REQUEST[softname]&c_id=$_REQUEST[c_id]&addmodulo=yes");
      }else{
                        header("Location:customers.php?btn_edit_prog=AgregarSoftware&det=1&c_id=$_REQUEST[c_id]");
                    }   
    //---------------------------------------------------------------------
    }
}
$c_id=$_REQUEST['c_id'];
    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=customers.php?view_det_prog=Ver+Informacion+de+Programas&det=1&c_id=$c_id\">";
    }else{
   /*
    echo "<pre>";
   print_r($_REQUEST)."<br>";
   echo "</pre>";*/
/*
$ids=$_REQUEST['ids'];
$vers=$_REQUEST['versiones'];
$act=$_REQUEST['fupdate'];
$soft = array();
foreach ($ids as $key => $value){
$ln=$ids[$key]."|".$vers[$key]."|".$act[$key];
if ($ids[$key]!=""){
$soft[]=$ln;
}
}
$soft=serialize($soft);

if (mysql_num_rows($chk)==0){
$strsql="INSERT INTO CRMdetprog (dp_idc,dp_soft,dp_vsmax,dp_fsmax,dp_vsclie,dp_fsclie,dp_vconvol,dp_fconvol) VALUES(
				'".$_REQUEST['c_id']."','$soft','".$_REQUEST['vsmax']."','".$_REQUEST['fsmax']."','".$_REQUEST['vsclie']."','".$_REQUEST['fsclie']."','".$_REQUEST['vconvol']."','".$_REQUEST['fconvol']."')";
}
else{
$strsql="UPDATE CRMdetprog WHERE dp_idc='".$_REQUEST['c_id']."'";
}*/
$c_id=$_REQUEST['c_id'];
echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=customers.php?view_det_prog=Ver+Informacion+de+Programas&det=1&c_id=$c_id\">";
    }
    
}
/**
 * Muesta los detalles del servidor
 * @deprecated 16/04/2011
 * @see <b style='color:green'>viewDetailsServers()</b> -
 *      <b style='color:blue'>customer.php</b>
 */
function view_details_serv(){
$sql="SELECT custname FROM CRMcustomer WHERE id=".$_REQUEST['c_id']."";
$res=mcq($sql,$db);
$nombre=mysql_result($res,0,0);

$datos="SELECT * FROM CRMdetserv WHERE ds_idc=".$_REQUEST['c_id']."";
$result=mcq($datos,$db);
$data=mysql_fetch_array($result);
if (mysql_num_rows($result)>0){
$ram=unserialize($data['ds_ram']);
$hd=unserialize($data['ds_hd']);
$eth=unserialize($data['ds_eth']);
$unidades=unserialize($data['ds_unidades']);
$particiones=unserialize($data['ds_particiones']);
}

echo  "
	<table width='80%' border=0>
		<tr>
			<td>&nbsp;</td>
			<td>
				<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$nombre</font>&nbsp;</legend><br><br>
					
					<fieldset><legend>Sistema Operativo</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>S.O.</b></td>
							<td width='150'><b>Version</b></td>
							<td></td>
						</tr>
						<tr>
							<td>".$data['ds_so']."</td>
							<td>".$data['ds_so_vers']."</td>
							<td></td>
						</tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Tarjeta Madre</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td></td>
						</tr>
						<tr>
							<td>".$data['ds_mb_marca']."</td>
							<td>".$data['ds_mb_mod']."</td>
							<td></td>
						</tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Procesador</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td></td>
						</tr>
						<tr>
							<td>".$data['ds_pr_marca']."</td>
							<td>".$data['ds_pr_mod']."</td>
							<td></td>
						</tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Memoria RAM</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td width='150'><b>Capacidad</b></td>
							<td></td>
						</tr>";
						if (count($ram)>0){
						foreach($ram as $value){
						$ln= explode("|",$value);
						echo "<tr><td>".$ln[0]."</td><td>".$ln[1]."</td><td>".$ln[2]."</td><td></td></tr>";
						}
						}
					echo "
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Disco(s) Duro(s)</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Tipo</b></td>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td width='150'><b>Capacidad</b></td>
							<td></td>
						</tr>";
						if (count($hd)>0){
						foreach($hd as $value){
						$ln= explode("|",$value);
						echo "<tr><td>".$ln[0]."</td><td>".$ln[1]."</td><td>".$ln[2]."</td><td>".$ln[3]."</td><td></td></tr>";
						}
						}
					echo "
						<tr><td colspan=5>&nbsp;</td></tr>
						<tr><td><b>Arreglo Raid</b></td><td>".$data['ds_raid']."</td><td colspan=3></td></tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Tarjetas de Red</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td width='150'><b>IP Address</b></td>
							<td width='150'><b>MAC Address</b></td>
							<td></td>
						</tr>";
						if (count($eth)>0){
						foreach($eth as $value){
						$ln= explode("|",$value);
						echo "<tr><td>".$ln[0]."</td><td>".$ln[1]."</td><td>".$ln[2]."</td><td>".$ln[3]."</td><td></td></tr>";
						}
						}
					echo "
					</table>
					</fieldset>

					<br><hr><br>
					
					<fieldset><legend>Floppy</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td></td>
						</tr>
						<tr>
							<td>".$data['ds_fd_marca']."</td>
							<td>".$data['ds_fd_mod']."</td>
							<td></td>
						</tr>
					</table>
					</fieldset>

					<br><hr><br>
					
					<fieldset><legend>Unidades de Almacenamiento</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Tipo</b></td>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td></td>
						</tr>";
						if (count($unidades)>0){
						foreach($unidades as $value){
						$ln= explode("|",$value);
						echo "<tr><td>".$ln[0]."</td><td>".$ln[1]."</td><td>".$ln[2]."</td><td></td></tr>";
						}
						}
					echo "
					</table>
					</fieldset>

					<br><hr><br>
					
					<fieldset><legend>Configuracion de Particiones</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Particion</b></td>
							<td width='150'><b>Montaje</b></td>
							<td width='150'><b>Tama�o</b></td>
                            <td width='150'><b>Filesystem</b></td>
							<td></td>
						</tr>";
						if (count($particiones)>0){
						foreach($particiones as $value){
						$ln= explode("|",$value);
						echo "<tr><td>".$ln[0]."</td><td>".$ln[1]."</td><td>".$ln[2]."</td><td>".$ln[3]."</td><td></td></tr>";
						}
						}
					echo "
					</table>
					</fieldset>
					
					<br><hr><br>";
				
				//*********************************************************
				$sql="SELECT CUSTOMERREADONLY FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id=$GLOBALS[USERID]";
				$result=mcq($sql,$db);
				$customer_readonly=mysql_result($result,0,0);
				//******************************************
				if ($customer_readonly!='1'){
				echo "
				<form>
				<table width=100% border=0>
					<tr>
						<td align='right'>
						<input type='submit' name='btn_edit_serv' value='Editar'>
						<input type='hidden' name='det' value=1>
						<input type='hidden' name='c_id' value=".$_REQUEST['c_id'].">
						</td>
					</tr>
				</table>
				</form>";
				}

				echo "
				</fiedset>
			</td>
		</tr>
	</table><br><br><br><br>
	";
}
/**
 * Muesta en pantalla campos para editar los detalles del servidor
 * @deprecated 16/04/2011
 * @see <b style='color:green'>viewEditServers()</b> -
 *      <b style='color:blue'>customer.php</b>
 */
function edit_details_serv(){
$sql="SELECT custname FROM CRMcustomer WHERE id=".$_REQUEST['c_id']."";
$res=mcq($sql,$db);
$nombre=mysql_result($res,0,0);

$datos="SELECT * FROM CRMdetserv WHERE ds_idc=".$_REQUEST['c_id']."";
$data=mysql_fetch_array(mcq($datos,$db));
$ram=unserialize($data['ds_ram']);
$hd=unserialize($data['ds_hd']);
$eth=unserialize($data['ds_eth']);
$unidades=unserialize($data['ds_unidades']);
$particiones=unserialize($data['ds_particiones']);

echo  "
<form>
	<table width='80%' border=0>
		<tr>
			<td>&nbsp;</td>
			<td>
				<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$nombre</font>&nbsp;</legend><br><br>
					
					<fieldset><legend>Sistema Operativo</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>S.O.</b></td>
							<td width='150'><b>Version</b></td>
							<td></td>
						</tr>
						<tr>
							<td><input type='text' name='so' value='".$data['ds_so']."'></td>
							<td><input type='text' name='so_vers' value='".$data['ds_so_vers']."'></td>
							<td>&nbsp;</td>
						</tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Tarjeta Madre</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td></td>
						</tr>
						<tr>
							<td><input type='text' name='mb_marca' value='".$data['ds_mb_marca']."'></td>
							<td><input type='text' name='mb_mod' value='".$data['ds_mb_mod']."'></td>
							<td>&nbsp;</td>
						</tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Procesador</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td></td>
						</tr>
						<tr>
							<td><input type='text' name='pr_marca' value='".$data['ds_pr_marca']."'></td>
							<td><input type='text' name='pr_mod' value='".$data['ds_pr_mod']."'></td>
							<td></td>
						</tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Memoria RAM</legend>
					<table class='crm' width='100%' id='ram'>
						<tr>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td width='150'><b>Capacidad</b></td>
							<td><input type='button' value='A&ntildeadir' onclick='addRowToTable(\"ram\");' />&nbsp;&nbsp;<input type='button' value='Eliminar' onclick='removeRowFromTable(\"ram\");' /></td>
						</tr>";
						foreach($ram as $value){
						$mem= explode("|",$value);
						echo "<tr>
										<td width=150><input type='text' name='ram_marca[]' value='".$mem[0]."'></td>
										<td width=150><input type='text' name='ram_mod[]' value='".$mem[1]."'></td>
										<td width=150><input type='text' name='ram_cap[]' value='".$mem[2]."'></td>
										<td></td>
									</tr>";
						}
						echo "
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Disco(s) Duro(s)</legend>
					<table class='crm' width='100%' id='hd'>
						<tr>
							<td width='150'><b>Tipo</b></td>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td width='150'><b>Capacidad</b></td>
							<td><input type='button' value='A�adir' onclick='addRowToTable(\"hd\");' />&nbsp;&nbsp;<input type='button' value='Eliminar' onclick='removeRowFromTable(\"hd\");' /></td>
						</tr>";
						foreach($hd as $value){
						$dd= explode("|",$value);
						echo "<tr>
										<td width=150><input type='text' name='hd_tipo[]' value='".$dd[0]."'></td>
										<td width=150><input type='text' name='hd_marca[]' value='".$dd[1]."'></td>
										<td width=150><input type='text' name='hd_mod[]' value='".$dd[2]."'></td>
										<td width=150><input type='text' name='hd_cap[]' value='".$dd[3]."'></td>
										<td></td>
									</tr>";
						}
						echo "
						</table><br>
						<table width=100% class='crm'>
						<tr>
							<td width=150><b>Arreglo Raid</b></td>
							<td width=150><input type='text' name='raid' value='".$data['ds_raid']."'></td>
							<td></td>
						</tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Tarjetas de Red</legend>
					<table class='crm' width='100%' id='eth'>
						<tr>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td width='150'><b>IP Address</b></td>
							<td width='150'><b>MAC Address</b></td>
							<td><input type='button' value='A&ntildeadir' onclick='addRowToTable(\"eth\");' />&nbsp;&nbsp;<input type='button' value='Eliminar' onclick='removeRowFromTable(\"eth\");' /></td>
						</tr>";
						foreach($eth as $value){
						$red= explode("|",$value);
						echo "<tr>
										<td width=150><input type='text' name='red_marca[]' value='".$red[0]."'></td>
										<td width=150><input type='text' name='red_mod[]' value='".$red[1]."'></td>
										<td width=150><input type='text' name='red_ip[]' value='".$red[2]."'></td>
										<td width=150><input type='text' name='red_mac[]' value='".$red[3]."'></td>
										<td></td>
									</tr>";
						}
						echo "
					</table>
					</fieldset>

					<br><hr><br>
					
					<fieldset><legend>Floppy</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td></td>
						</tr>
						<tr>
							<td><input type='text' name='fd_marca' value='".$data['ds_fd_marca']."'></td>
							<td><input type='text' name='fd_mod' value='".$data['ds_fd_mod']."'></td>
							<td></td>
						</tr>
					</table>
					</fieldset>

					<br><hr><br>
					
					<fieldset><legend>Unidades de Almacenamiento</legend>
					<table class='crm' width='100%' id='unidades'>
						<tr>
							<td width='150'><b>Tipo</b></td>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td><input type='button' value='A�adir' onclick='addRowToTable(\"unidades\");' />&nbsp;&nbsp;<input type='button' value='Eliminar' onclick='removeRowFromTable(\"unidades\");' /></td>
						</tr>";
						foreach($unidades as $value){
						$unidad= explode("|",$value);
						echo "<tr>
										<td width=150><input type='text' name='uni_tipo[]' value='".$unidad[0]."'></td>
										<td width=150><input type='text' name='uni_marca[]' value='".$unidad[1]."'></td>
										<td width=150><input type='text' name='uni_modelo[]' value='".$unidad[2]."'></td>
										<td></td>
									</tr>";
						}
						echo "
					</table>
					</fieldset>

					<br><hr><br>
					
					<fieldset><legend>Configuracion de Particiones</legend>
					<table class='crm' width='100%' id='particiones'>
						<tr>
							<td width='150'><b>Particion</b></td>
							<td width='150'><b>Montaje</b></td>
							<td width='150'><b>Tama&ntildeo</b></td>
                            <td width='150'><b>Filesystem</b></td>
							<td><input type='button' value='A&ntildeadir' onclick='addRowToTable(\"particiones\");' />&nbsp;&nbsp;<input type='button' value='Eliminar' onclick='removeRowFromTable(\"particiones\");' /></td>
						</tr>";
						foreach($particiones as $value){
						$part= explode("|",$value);
						echo "<tr>
										<td width=150><input type='text' name='part_nom[]' value='".$part[0]."'></td>
										<td width=150><input type='text' name='part_mount[]' value='".$part[1]."'></td>
										<td width=150><input type='text' name='part_size[]' value='".$part[2]."'></td>
                                        <td width=150><input type='text' name='part_filesystem[]' value='".$part[3]."'></td>
										<td></td>
									</tr>";
						}
						echo "
					</table>
					</fieldset>
					
					<br><hr><br>
					
				<table width=100% border=0>
					<tr>
						<td align='right'>
						<input type='submit' name='btn_mod_serv' value='Aceptar'>
						<input type='hidden' name='det' value=1>
						<input type='hidden' name='c_id' value=".$_REQUEST['c_id'].">
						</td>
					</tr>
				</table>


				</fiedset>
			</td>
		</tr>
	</table><br><br><br><br>
</form>
";
?>
<script type="text/javascript">
function addRowToTable(tabla)
{
  var tbl = document.getElementById(tabla);
  var lastRow = tbl.rows.length;
  // if there's no header row in the table, then iteration = lastRow + 1
  var iteration = lastRow;
  var row = tbl.insertRow(lastRow);
  
	if (tabla=='ram'){
		if (iteration>4){
			return false;
		}
  
		var cell1 = row.insertCell(0);
		var input1 = document.createElement('input');
		input1.type='text';
		input1.name='ram_marca[]';
		cell1.appendChild(input1);
  
		var cell2 = row.insertCell(1);
		var input2 = document.createElement('input');
		input2.type = 'text';
		input2.name = 'ram_mod[]';
		cell2.appendChild(input2);
  
		var cell3 = row.insertCell(2);
		var input3 = document.createElement('input');
		input3.type='text';
		input3.name='ram_cap[]';
		cell3.appendChild(input3);
  
		var cell4 = row.insertCell(3);
  }
  if (tabla=='hd'){
		if (iteration>4){
			return false;
		}
  
		var cell1 = row.insertCell(0);
		var input1 = document.createElement('input');
		input1.type='text';
		input1.name='hd_tipo[]';
		cell1.appendChild(input1);
  
		var cell2 = row.insertCell(1);
		var input2 = document.createElement('input');
		input2.type = 'text';
		input2.name = 'hd_marca[]';
		cell2.appendChild(input2);
  
		var cell3 = row.insertCell(2);
		var input3 = document.createElement('input');
		input3.type='text';
		input3.name='hd_mod[]';
		cell3.appendChild(input3);
		
		var cell4 = row.insertCell(3);
		var input4 = document.createElement('input');
		input4.type='text';
		input4.name='hd_cap[]';
		cell4.appendChild(input4);
  
		var cell5 = row.insertCell(4);
  }
  if (tabla=='eth'){
		if (iteration>3){
			return false;
		}
  
		var cell1 = row.insertCell(0);
		var input1 = document.createElement('input');
		input1.type='text';
		input1.name='red_marca[]';
		cell1.appendChild(input1);
  
		var cell2 = row.insertCell(1);
		var input2 = document.createElement('input');
		input2.type = 'text';
		input2.name = 'red_mod[]';
		cell2.appendChild(input2);
  
		var cell3 = row.insertCell(2);
		var input3 = document.createElement('input');
		input3.type='text';
		input3.name='red_ip[]';
		cell3.appendChild(input3);
		
		var cell4 = row.insertCell(3);
		var input4 = document.createElement('input');
		input4.type='text';
		input4.name='red_mac[]';
		cell4.appendChild(input4);
  
		var cell5 = row.insertCell(4);
  }
  if (tabla=='unidades'){
		if (iteration>3){
			return false;
		}
  
		var cell1 = row.insertCell(0);
		var input1 = document.createElement('input');
		input1.type='text';
		input1.name='uni_tipo[]';
		cell1.appendChild(input1);
  
		var cell2 = row.insertCell(1);
		var input2 = document.createElement('input');
		input2.type = 'text';
		input2.name = 'uni_marca[]';
		cell2.appendChild(input2);
  
		var cell3 = row.insertCell(2);
		var input3 = document.createElement('input');
		input3.type='text';
		input3.name='uni_modelo[]';
		cell3.appendChild(input3);
  
		var cell4 = row.insertCell(3);
  }
  if (tabla=='particiones'){
		if (iteration>10){
			return false;
		}
  
		var cell1 = row.insertCell(0);
		var input1 = document.createElement('input');
		input1.type='text';
		input1.name='part_nom[]';
		cell1.appendChild(input1);
  
		var cell2 = row.insertCell(1);
		var input2 = document.createElement('input');
		input2.type = 'text';
		input2.name = 'part_mount[]';
		cell2.appendChild(input2);
  
		var cell3 = row.insertCell(2);
		var input3 = document.createElement('input');
		input3.type='text';
		input3.name='part_size[]';
		cell3.appendChild(input3);
        
        var cell4 = row.insertCell(3);
        var input4 = document.createElement('input');
        input4.type='text';
        input4.name='part_filesystem[]';
        cell4.appendChild(input4);
  
		var cell4 = row.insertCell(4);
  }
}
function removeRowFromTable(tabla)
{
  var tbl = document.getElementById(tabla);
  var lastRow = tbl.rows.length;
  if (lastRow > 1) tbl.deleteRow(lastRow - 1);
}
</script>
<?php
}
/**
 * Obtiene los detalles del servidor de base de datos
 * @deprecated 16/04/2011
 * @see <b style='color:green'>getDbdServers()</b> -
 *      <b style='color:blue'>sqlfiles.php</b>
 */
function mod_details_serv(){
    $strchk="SELECT * FROM CRMdetserv WHERE ds_idc=".$_REQUEST['c_id']."";
    $chk=mcq($strchk,$db);

    $ram_marca=$_REQUEST['ram_marca'];
    $ram_mod=$_REQUEST['ram_mod'];
    $ram_cap=$_REQUEST['ram_cap'];

    $hd_tipo=$_REQUEST['hd_tipo'];
    $hd_marca=$_REQUEST['hd_marca'];
    $hd_mod=$_REQUEST['hd_mod'];
    $hd_cap=$_REQUEST['hd_cap'];

    $red_marca=$_REQUEST['red_marca'];
    $red_mod=$_REQUEST['red_mod'];
    $red_ip=$_REQUEST['red_ip'];
    $red_mac=$_REQUEST['red_mac'];

    $uni_tipo=$_REQUEST['uni_tipo'];
    $uni_marca=$_REQUEST['uni_marca'];
    $uni_modelo=$_REQUEST['uni_modelo'];

    $part_nom=$_REQUEST['part_nom'];
    $part_mount=$_REQUEST['part_mount'];
    $part_size=$_REQUEST['part_size'];
    $part_filesystem=$_REQUEST['part_filesystem'];

    $ram = array();
    foreach ($ram_marca as $key => $value){
    $ln=$ram_marca[$key]."|".$ram_mod[$key]."|".$ram_cap[$key];
    if ($ram_marca[$key]!=""){$ram[]=$ln;}
    }
    $ram=serialize($ram);

    $hd = array();
    foreach ($hd_tipo as $key => $value){
    $ln=$hd_tipo[$key]."|".$hd_marca[$key]."|".$hd_mod[$key]."|".$hd_cap[$key];
    if ($hd_tipo[$key]!=""){$hd[]=$ln;}
    }
    $hd=serialize($hd);

    $red = array();
    foreach ($red_marca as $key => $value){
    $ln=$red_marca[$key]."|".$red_mod[$key]."|".$red_ip[$key]."|".$red_mac[$key];
    if ($red_marca[$key]!=""){$red[]=$ln;}
    }
    $red=serialize($red);

    $unidad = array();
    foreach ($uni_tipo as $key => $value){
    $ln=$uni_tipo[$key]."|".$uni_marca[$key]."|".$uni_modelo[$key];
    if ($uni_tipo[$key]!=""){$unidad[]=$ln;}
    }
    $unidad=serialize($unidad);

    $particion = array();
    foreach ($part_nom as $key => $value){
    $ln=$part_nom[$key]."|".$part_mount[$key]."|".$part_size[$key]."|".$part_filesystem[$key];
    if ($part_nom[$key]!=""){$particion[]=$ln;}
    }
    $particion=serialize($particion);

    if (mysql_num_rows($chk)==0){
    $strsql="INSERT INTO CRMdetserv(ds_idc,ds_so,ds_so_vers,ds_mb_marca,ds_mb_mod,ds_pr_marca,ds_pr_mod,ds_ram,ds_hd,ds_raid,ds_eth,ds_fd_marca,ds_fd_mod,ds_unidades,ds_particiones) VALUES(
                                    ".$_REQUEST['c_id'].",'".$_REQUEST['so']."','".$_REQUEST['so_vers']."','".$_REQUEST['mb_marca']."','".$_REQUEST['mb_mod']."','".$_REQUEST['pr_marca']."','".$_REQUEST['pr_mod']."',
                                    '".$ram."','".$hd."','".$_REQUEST['raid']."','".$red."','".$_REQUEST['fd_marca']."','".$_REQUEST['fd_mod']."','".$unidad."','".$particion."')";
    }
    else{
    $strsql="UPDATE CRMdetserv SET ds_so='".$_REQUEST['so']."', ds_so_vers='".$_REQUEST['so_vers']."', ds_mb_marca='".$_REQUEST['mb_marca']."', ds_mb_mod='".$_REQUEST['mb_mod']."',
                                    ds_pr_marca='".$_REQUEST['pr_marca']."', ds_pr_mod='".$_REQUEST['pr_mod']."', ds_ram='".$ram."', ds_hd='".$hd."',ds_raid='".$_REQUEST['raid']."',ds_eth='".$red."',
                                    ds_fd_marca='".$_REQUEST['fd_marca']."', ds_fd_mod='".$_REQUEST['fd_mod']."', ds_unidades='".$unidad."', ds_particiones='".$particion."' WHERE ds_idc='".$_REQUEST['c_id']."'";
    }
    mcq($strsql,$db);
    $c_id=$_REQUEST['c_id'];
    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=customers.php?det=1&c_id=$c_id\">";
}
//**********************************************************
function view_details_disp(){
$sql="SELECT custname FROM CRMcustomer WHERE id=".$_REQUEST['c_id']."";
$res=mcq($sql,$db);
$nombre=mysql_result($res,0,0);

$datos="SELECT * FROM CRMdetdisp WHERE dd_idc=".$_REQUEST['c_id']."";
$result=mcq($datos,$db);
$data=mysql_fetch_array($result);
if (mysql_num_rows($result)>0){
$disp=unserialize($data['dd_disp']);
$tqs=unserialize($data['dd_tqs']);
}
echo  "
	<table width='80%' border=0>
		<tr>
			<td>&nbsp;</td>
			<td>
				<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$nombre</font>&nbsp;</legend><br><br>
					
					<fieldset><legend>Dispensarios</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='50'><b># Disp</b></td>
							<td width='100'><b>MAC Address</b></td>
							<td width='100'><b>IP Asignada</b></td>
							<td width='120'><b>Capacidad D.O.C.</b></td>
							<td width='120'><b>Modo de Operacion</b></td>
							<td width='120'><b>Version Draco</b></td>
							<td width='120'><b>Version Gemini</b></td>
							
						</tr>";
						if (count($disp)>0){
						foreach($disp as $value){
						$ln= explode("|",$value);
						echo "
							<tr>
								<td>".$ln[0]."</td>
								<td>".$ln[1]."</td>
								<td>".$ln[2]."</td>
								<td>".$ln[3]."</td>";
								if ($ln[4]=='0'){
								$modo="Terminal";
								}
								elseif ($ln[4]=='1'){
								$modo="Autonomo";
								}
								echo "
								<td>".$modo."</td>
								<td>".$ln[5]."</td>
								<td>".$ln[6]."</td>
							</tr>";
						}
						}
						echo "
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Ultramax</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Sistema Operativo</b></td>
							<td width='150'><b>IP Address</b></td>
							<td width='150'><b>Version</b></td>
							<td></td>
						</tr>
						<tr>
							<td>".$data['dd_umax_so']."&nbsp;</td>
							<td>".$data['dd_umax_ip']."&nbsp;</td>
							<td>".$data['dd_umax_ver']."&nbsp;</td>
							<td></td>
						</tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Sistema de Telemedicion</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td></td>
						</tr>
						<tr>
							<td>".$data['dd_stm_marca']."</td>
							<td>".$data['dd_stm_mod']."</td>
							<td></td>
						</tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Tanques</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='80'><b># Tanque</b></td>
							<td width='80'><b>Producto</b></td>
							<td width='80'><b>Cap. Total</b></td>
							<td width='100'><b>Cap. Operativa</b></td>
							<td width='80'><b>Cap. Util</b></td>
							<td width='80'><b>Cap. Fondaje</b></td>
							<td width='100'><b>Volumen Minimo</b></td>
							<td></td>
						</tr>
						</tr>";
						if (count($tqs)>0){
						foreach($tqs as $value){
						$ln= explode("|",$value);
						if ($ln[1]=='1'){
						$prod="Diesel";
						}
						elseif($ln[1]=='2'){
						$prod="Magna";
						}
						elseif($ln[1]=='3'){
						$prod="Premium";
						}
						echo "
							<tr>
								<td>".$ln[0]."</td>
								<td>".$prod."</td>
								<td>".$ln[2]."</td>
								<td>".$ln[3]."</td>
								<td>".$ln[4]."</td>
								<td>".$ln[5]."</td>
								<td>".$ln[6]."</td>
								<td></td>
								</tr>";
						}
						}
						echo "
					</table>
					</fieldset>
					
					<br><hr><br>";
				
				//*********************************************************
				$sql="SELECT CUSTOMERREADONLY FROM $GLOBALS[TBL_PREFIX]loginusers WHERE id=$GLOBALS[USERID]";
				$result=mcq($sql,$db);
				$customer_readonly=mysql_result($result,0,0);
				//******************************************
				if ($customer_readonly!='1'){
				echo "
				<form>
				<table width=100% border=0>
					<tr>
						<td align='right'>
						<input type='submit' name='btn_edit_disp' value='Editar'>
						<input type='hidden' name='det' value=1>
						<input type='hidden' name='c_id' value=".$_REQUEST['c_id'].">
						</td>
					</tr>
				</table>
				</form>";
				}
				
				echo "
				</fiedset>
			</td>
		</tr>
	</table><br><br><br><br>
	";
}
//**********************************************************
function edit_details_disp(){
$sql="SELECT custname FROM CRMcustomer WHERE id=".$_REQUEST['c_id']."";
$res=mcq($sql,$db);
$nombre=mysql_result($res,0,0);

$datos="SELECT * FROM CRMdetdisp WHERE dd_idc=".$_REQUEST['c_id']."";
$data=mysql_fetch_array(mcq($datos,$db));
$disp=unserialize($data['dd_disp']);
$tqs=unserialize($data['dd_tqs']);
echo  "
<form>
	<table width='80%' border=0>
		<tr>
			<td>&nbsp;</td>
			<td>
				<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>$nombre</font>&nbsp;</legend><br><br>
					
					<fieldset><legend>Dispensarios</legend>
					<table class='crm' width='100%' id='disp'>
						<tr>
							<td width='50'><b># Disp</b></td>
							<td width='100'><b>MAC Address</b></td>
							<td width='90'><b>IP Asignada</b></td>
							<td width='110'><b>Capacidad D.O.C.</b></td>
							<td width='80'><b>Modo de Op.</b></td>
							<td width='90'><b>Version Draco</b></td>
							<td width='100'><b>Version Gemini</b></td>
							<td width='140'><input type='button' value='A�adir' onclick='addRowToTable(\"disp\");'>&nbsp;&nbsp;<input type='button' value='Eliminar' onclick='removeRowFromTable(\"disp\");'></td>
						</tr>";
						foreach($disp as $value){
						$ln= explode("|",$value);
						echo "<tr>
										<td><input type='text' name='disp_num[]' value='".$ln[0]."' size=5></td>
										<td><input type='text' name='disp_mac[]' value='".$ln[1]."' size=16></td>
										<td><input type='text' name='disp_ip[]' value='".$ln[2]."' size=14 maxlength=15></td>
										<td><input type='text' name='disp_doc[]' value='".$ln[3]."' size=18></td>";
										if ($ln[4]==0){
										$mod0="SELECTED";
										$mod1="";
										}
										elseif ($ln[4]==1){
										$mod0="";
										$mod1="SELECTED";
										}
										else{
										$mod0="";
										$mod1="";
										}
										echo "
										<td>
										<select name='disp_modo[]'><option value=0 $mod0>Terminal</option><option value=1 $mod1>Autonomo</option></select></td>
										<td><input type='text' name='disp_vdraco[]' value='".$ln[5]."' size=8></td>
										<td><input type='text' name='disp_vgemini[]' value='".$ln[6]."' size=8></td>
										<td</td>
									</tr>";
						}
						echo "
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Ultramax</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Sistema Operativo</b></td>
							<td width='150'><b>IP Address</b></td>
							<td width='150'><b>Version</b></td>
							<td></td>
						</tr>
						<tr>
							<td><input type='text' name='umax_so' value='".$data['dd_umax_so']."'></td>
							<td><input type='text' name='umax_ip' value='".$data['dd_umax_ip']."'></td>
							<td><input type='text' name='umax_ver' value='".$data['dd_umax_ver']."'></td>
							<td></td>
						</tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Sistema de Telemedicion</legend>
					<table class='crm' width='100%'>
						<tr>
							<td width='150'><b>Marca</b></td>
							<td width='150'><b>Modelo</b></td>
							<td></td>
						</tr>
						<tr>
							<td><input type='text' name='stm_marca' value='".$data['dd_stm_marca']."'></td>
							<td><input type='text' name='stm_mod' value='".$data['dd_stm_mod']."'></td>
							<td></td>
						</tr>
					</table>
					</fieldset>
					
					<br><hr><br>
					
					<fieldset><legend>Tanques</legend>
					<table class='crm' width='100%' id='tqs'>
						<tr>
							<td width='80'><b># Tanque</b></td>
							<td width='80'><b>Producto</b></td>
							<td width='80'><b>Cap. Total</b></td>
							<td width='100'><b>Cap. Operativa</b></td>
							<td width='80'><b>Cap. Util</b></td>
							<td width='80'><b>Cap. Fondaje</b></td>
							<td width='100'><b>Volumen Minimo</b></td>
							<td><input type='button' value='A&ntilde;adir' onclick='addRowToTable(\"tqs\");'>&nbsp;&nbsp;<input type='button' value='Eliminar' onclick='removeRowFromTable(\"tqs\");'></td>
						</tr>";
						foreach($tqs as $value){
						$ln= explode("|",$value);
						if ($ln[1]=='1'){
						$prod1="SELECTED";
						$prod2="";
						$prod3="";
						}
						elseif ($ln[1]=='2'){
						$prod1="";
						$prod2="SELECTED";
						$prod3="";
						}
						if ($ln[1]=='3'){
						$prod1="";
						$prod2="";
						$prod3="SELECTED";
						}
						echo "<tr>
										<td><input type='text' name='tqs_num[]' value='".$ln[0]."' size=5></td>
										<td><select name='tqs_prod[]'><option value=1 $prod1>Diesel</option><option value=2 $prod2>Magna</option><option value=3 $prod3>Premium</option></select></td>
										<td><input type='text' name='tqs_captot[]' value='".$ln[2]."' size=8></td>
										<td><input type='text' name='tqs_capop[]' value='".$ln[3]."' size=8></td>
										<td><input type='text' name='tqs_caput[]' value='".$ln[4]."' size=8></td>
										<td><input type='text' name='tqs_capfon[]' value='".$ln[5]."' size=8></td>
										<td><input type='text' name='tqs_volmin[]' value='".$ln[6]."' size=8></td>
										<td></td>
									</tr>";
						}
						echo "

					</table>
					</fieldset>
					
					<br><hr><br>
					
					<table width=100% border=0>
					<tr>
						<td align='right'>
						<input type='submit' name='btn_mod_disp' value='Aceptar'>
						<input type='hidden' name='det' value=1>
						<input type='hidden' name='c_id' value=".$_REQUEST['c_id'].">
						</td>
					</tr>
					</table>
					
				</fiedset>
			</td>
		</tr>
	</table><br><br><br><br>
</form>
";
?>
<script type="text/javascript">
function addRowToTable(tabla)
{
  var tbl = document.getElementById(tabla);
  var lastRow = tbl.rows.length;
  // if there's no header row in the table, then iteration = lastRow + 1
  var iteration = lastRow;
  var row = tbl.insertRow(lastRow);
  
	if (tabla=='disp'){
		if (iteration>12){
			return false;
		}
  
		var cell1 = row.insertCell(0);
		var input1 = document.createElement('input');
		input1.type='text';
		input1.name='disp_num[]';
		input1.size=5;
		cell1.appendChild(input1);
  
		var cell2 = row.insertCell(1);
		var input2 = document.createElement('input');
		input2.type = 'text';
		input2.name = 'disp_mac[]';
		input2.size=16;
		cell2.appendChild(input2);
  
		var cell3 = row.insertCell(2);
		var input3 = document.createElement('input');
		input3.type='text';
		input3.name='disp_ip[]';
		input3.size=14;
		cell3.appendChild(input3);
		
		var cell4 = row.insertCell(3);
		var input4 = document.createElement('input');
		input4.type='text';
		input4.name='disp_doc[]';
		input4.size=18
		cell4.appendChild(input4);
		
		var cell5 = row.insertCell(4);
		var sel = document.createElement('select');
		sel.name='disp_modo[]';
		sel.options[0] = new Option('Terminal', '0');
		sel.options[1] = new Option('Autonomo', '1');
		cell5.appendChild(sel);
		
		var cell6 = row.insertCell(5);
		var input6 = document.createElement('input');
		input6.type='text';
		input6.name='disp_vdraco[]';
		input6.size=8;
		cell6.appendChild(input6);
		
		var cell7 = row.insertCell(6);
		var input7 = document.createElement('input');
		input7.type='text';
		input7.name='disp_vgemini[]';
		input7.size=8;
		cell7.appendChild(input7);
  
		var cell8 = row.insertCell(7);
  }
  if (tabla=='tqs'){
		if (iteration>12){
			return false;
		}
  
		var cell1 = row.insertCell(0);
		var input1 = document.createElement('input');
		input1.type='text';
		input1.name='tqs_num[]';
		input1.size=5
		cell1.appendChild(input1);
		
		var cell2 = row.insertCell(1);
		var sel = document.createElement('select');
		sel.name='tqs_prod[]';
		sel.options[0] = new Option('Diesel', '1');
		sel.options[1] = new Option('Magna', '2');
		sel.options[2] = new Option('Premium', '3');
		cell2.appendChild(sel);
  
		var cell3 = row.insertCell(2);
		var input3 = document.createElement('input');
		input3.type = 'text';
		input3.name = 'tqs_captot[]';
		input3.size=8;
		cell3.appendChild(input3);
		
		var cell4 = row.insertCell(3);
		var input4 = document.createElement('input');
		input4.type = 'text';
		input4.name = 'tqs_capop[]';
		input4.size=8;
		cell4.appendChild(input4);
		
		var cell5 = row.insertCell(4);
		var input5 = document.createElement('input');
		input5.type = 'text';
		input5.name = 'tqs_caput[]';
		input5.size=8;
		cell5.appendChild(input5);
  
		var cell6 = row.insertCell(5);
		var input6 = document.createElement('input');
		input6.type = 'text';
		input6.name = 'tqs_capfon[]';
		input6.size=8;
		cell6.appendChild(input6);
		
		var cell7 = row.insertCell(6);
		var input7 = document.createElement('input');
		input7.type = 'text';
		input7.name = 'tqs_volmin[]';
		input7.size=8;
		cell7.appendChild(input7);
  
		var cell8 = row.insertCell(7);
  }
}
function removeRowFromTable(tabla)
{
  var tbl = document.getElementById(tabla);
  var lastRow = tbl.rows.length;
  if (lastRow > 1) tbl.deleteRow(lastRow - 1);
}
</script>
<?php
}
//**********************************************************
function mod_details_disp(){
$strchk="SELECT * FROM CRMdetdisp WHERE dd_idc=".$_REQUEST['c_id']."";
$chk=mcq($strchk,$db);

$disp_num=$_REQUEST['disp_num'];
$disp_mac=$_REQUEST['disp_mac'];
$disp_ip=$_REQUEST['disp_ip'];
$disp_doc=$_REQUEST['disp_doc'];
$disp_modo=$_REQUEST['disp_modo'];
$disp_vdraco=$_REQUEST['disp_vdraco'];
$disp_vgemini=$_REQUEST['disp_vgemini'];

$tqs_num=$_REQUEST['tqs_num'];
$tqs_prod=$_REQUEST['tqs_prod'];
$tqs_captot=$_REQUEST['tqs_captot'];
$tqs_capop=$_REQUEST['tqs_capop'];
$tqs_caput=$_REQUEST['tqs_caput'];
$tqs_capfon=$_REQUEST['tqs_capfon'];
$tqs_volmin=$_REQUEST['tqs_volmin'];

$disp = array();
foreach ($disp_num as $key => $value){
$ln=$disp_num[$key]."|".$disp_mac[$key]."|".$disp_ip[$key]."|".$disp_doc[$key]."|".$disp_modo[$key]."|".$disp_vdraco[$key]."|".$disp_vgemini[$key];
if ($disp_num[$key]!=""){$disp[]=$ln;}
}
$disp=serialize($disp);

$tqs = array();
foreach ($tqs_num as $key => $value){
$ln=$tqs_num[$key]."|".$tqs_prod[$key]."|".$tqs_captot[$key]."|".$tqs_capop[$key]."|".$tqs_caput[$key]."|".$tqs_capfon[$key]."|".$tqs_volmin[$key];
if ($tqs_num[$key]!=""){$tqs[]=$ln;}
}
$tqs=serialize($tqs);

if (mysql_num_rows($chk)==0){
$strsql="INSERT INTO CRMdetdisp(dd_idc,dd_disp,dd_stm_marca,dd_stm_mod,dd_tqs,dd_umax_so,dd_umax_ip,dd_umax_ver) VALUES(
				".$_REQUEST['c_id'].",'".$disp."','".$_REQUEST['stm_marca']."','".$_REQUEST['stm_mod']."','".$tqs."','".$_REQUEST['umax_so']."','".$_REQUEST['umax_ip']."','".$_REQUEST['umax_ver']."')";
}
else{
$strsql="UPDATE CRMdetdisp SET dd_disp='".$disp."', dd_stm_marca='".$_REQUEST['stm_marca']."', dd_stm_mod='".$_REQUEST['stm_mod']."', dd_tqs='".$tqs."', dd_umax_so='".$_REQUEST['umax_so']."',dd_umax_ip='".$_REQUEST['umax_ip']."', dd_umax_ver='".$_REQUEST['umax_ver']."'
				WHERE dd_idc='".$_REQUEST['c_id']."'";
}
mcq($strsql,$db);
$c_id=$_REQUEST['c_id'];
echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=customers.php?det=1&c_id=$c_id\">";

}

function checkCenamVer($idcenam){
	$sql = "SELECT version FROM $GLOBALS[TBL_PREFIX]relcenam WHERE id = ". $idcenam ."";
	$result = mcq($sql, $db);
	$data = mysql_fetch_row($result);
	return $data[0];
}
function checkTypeConn($idtype){
	$sql = "SELECT connstring FROM $GLOBALS[TBL_PREFIX]reltconn WHERE id = ". $idtype ."";
	$result = mcq($sql, $db);
	$data = mysql_fetch_row($result);
	return $data[0];
}

function checkTypeCard($idconn) {
	/*Busca el tipo de tarjeta*/
	$sql = "SELECT cardstring FROM $GLOBALS[TBL_PREFIX]typecard WHERE id = ". $idconn ."";
	$result = mcq($sql, $db);
	$data = mysql_fetch_row($result);
	return $data[0];
}
/**
 * Busca el tipo de marca
 */
function checkBrand($idconn) {
	$sql = "SELECT description FROM $GLOBALS[TBL_PREFIX]brands WHERE id = ". $idconn ."";
	$result = mcq($sql, $db);
	$data = mysql_fetch_row($result);
	return $data[0];
}
/**
 * Busca el tipo de generacion
 */
function checkGeneration($idconn) {
	$sql = "SELECT description FROM $GLOBALS[TBL_PREFIX]generation WHERE id = ". $idconn ."";
	$result = mcq($sql, $db);
	$data = mysql_fetch_row($result);
	return $data[0];
}

function createLink($baselink, $name, $id) {
	$link = "<a href='" . $baselink . "&$name=$id'>Editar</a>";
	return $link;
}
/**
 * Obtiene el nombre el cliente
 * @param int $id Id del cliente
 * @return string Nombre del cliente
 */
function getNameCustomer($id){
	$sql="SELECT custname FROM $GLOBALS[TBL_PREFIX]customer WHERE id=".$id."";
	$res=mcq($sql,$db);
	return mysql_result($res,0,0);
}

function getListCenam(){
	/*Obtiene la lista las versiones disponibles del cenam*/
	$sql = "SELECT id, version FROM $GLOBALS[TBL_PREFIX]relcenam ORDER BY id";
	$result = mcq($sql, $db);
	return $result;
}

function getListTypeConn(){
	/*Obtiene los registors del tipo de conexion*/
	$sql = "SELECT id, connstring FROM $GLOBALS[TBL_PREFIX]reltconn ORDER BY id";
	$result = mcq($sql, $db);
	return $result;
}

function getListCards(){
	/*Obtiene los registros de los tipos de carga*/
	$sql = "SELECT id, cardstring FROM $GLOBALS[TBL_PREFIX]typecard ORDER BY id";
	$result = mcq($sql, $db);
	return $result;
}
/**
 * Obtiene los registros de los tipos de marca de los dispositivos
 */
function getListBrand(){
	$sql = "SELECT id, description FROM $GLOBALS[TBL_PREFIX]brands ORDER BY id";
	$result = mcq($sql, $db);
	return $result;
}
/**
 * Obtiene los registros de los tipos de generacion de los dispositivos
 */
function getListGeneration(){
	$sql = "SELECT id, description FROM $GLOBALS[TBL_PREFIX]generation ORDER BY id";
	$result = mcq($sql, $db);
	return $result;
}

function createHtmlList($name, $recordset, $value){
/*Crea una lista HTML a partir de un recordset dado y con un solo elemento de muestra*/
	$inputdisp = "<select name='$name'>";
	while ($row = mysql_fetch_row($recordset)){
		$valsel = "";
		if ($value == $row[0])
			$valsel = "SELECTED";
		$inputdisp .= "<option value=". $row[0] ." $valsel>". $row[1] ."</option>";
	}
	$inputdisp .= "</select>";
	return $inputdisp;

}

function addConfirm($label){
	/*Funcion para agregar en un input si confirma que desea borrar*/
	return "onclick='x=confirm(\"Realmente desea $label \");if(x){return true;}else{return false;}'";
}
/**
 * Funcion para crear las formas en la edicion del dispensarios
 * @return string Devuelve los inpunt del formulario para editar
 */
function cEditFormDisp(){
	// Obtenemos la informacion del dispensario que queremos editar
	$sql = "SELECT a.id, a.nodispenser, a.brand, a.generation, a.model, a.sernum, a.datemanufacter, b.macaddress,
			b.ip, b.docsize, b.typecard, b.localversion, b.netversion, b.cenamver,
			b.typeconn FROM $GLOBALS[TBL_PREFIX]dispenser as a INNER JOIN 
			$GLOBALS[TBL_PREFIX]motherboard as b ON a.id = b.iddispenser WHERE
			a.id = ". $_REQUEST['edisp'] ."";
	$result = mcq($sql, $db);
	$data = mysql_fetch_array($result,MYSQL_ASSOC);
	// Lo ponemos en un array para ser enviado la plantilla
	$adatadisp = array();
	foreach ($data as $name=>$value) {
		$inputdisp = "";
		if ($name == "id") {
			$inputdisp = "<input type='hidden' name='det' value=1> \n
				<input type='hidden' name='iddispenser' value='". $_REQUEST['edisp'] ."'>\n
				<input type='hidden' name='c_id' value='". $_REQUEST['c_id'] ."'> \n
				<input type='submit' name='btneditdisp' value='Aplicar'>
				&nbsp; &nbsp;
				<input type='submit' name='btndeldisp' value='Eliminar' ". addConfirm("Eliminar el Dispensario") ." >
				";
		}
		else {
			if ($name == "cenamver") {
				$val = checkCenamVer($value);
				$listcenam = getListCenam();
				$inputdisp = createHtmlList("vcenam", $listcenam, $value);
			}
			elseif ($name == "typeconn") {
				$val = checkTypeConn($value);
				$listconn = getListTypeConn();
				$inputdisp = createHtmlList("tconn", $listconn, $value);
			}
			elseif ($name == "typecard") {
				$val = checkTypeCard($value);
				$listcard  = getListCards();
				$inputdisp = createHtmlList("tcard", $listcard, $value);
			
			}
			elseif ($name == "nodispenser"){
				$inputdisp = "$value";
			}
                        elseif ($name == "brand") {
                                $val = checkBrand($value);
				$listbrand  = getListBrand();
				$inputdisp = createHtmlList("brand", $listbrand, $value);
			}
                        elseif ($name == "generation") {
                                $val = checkGeneration($value);
				$listgeneration  = getListGeneration();
				$inputdisp = createHtmlList("generation", $listgeneration, $value);
			}
			else {
				$inputdisp = "<input type='text' name='$name' value='$value'>";
			}
		}
		array_push($adatadisp, $inputdisp);
	}
	return $adatadisp;
}
/**
 * Funcion para crear las formas para agregar dispensarios
 * @return string Devuelve los inpunt del formulario para agregar
 */
function cAddFromDisp(){
	$data = array("id", "nodispenser", "brand", "generation", "model", "sernum", "datemanufacter",
			"macaddress", "ip", "docsize", "typecard", "localversion", 
			"netversion", "cenamver", "typeconn");
	$adatadisp = array();
	foreach ($data as $name){
		$inputdisp = "";
		if ($name == "id") {
			$inputdisp = "<input type='hidden' name='det' value=1> \n
				<input type='hidden' name='c_id' value='". $_REQUEST['c_id'] ."'> \n
				<input type='submit' name='btnadisp' value='Agregar'>
				";
		}
		else {
			if ($name == "cenamver") {
				$listcenam = getListCenam();
				$inputdisp = createHtmlList("vcenam", $listcenam, "");
			}
			elseif ($name == "typeconn") {
				$listconn = getListTypeConn();
				$inputdisp = createHtmlList("tconn", $listconn, "");
			}
			elseif ($name == "typecard") {
				$listcard  = getListCards();
				$inputdisp = createHtmlList("tcard", $listcard, "");
			}
                        elseif ($name == "brand") {
				$listbrand  = getListBrand();
				$inputdisp = createHtmlList("brand", $listbrand, "");
			}
                        elseif ($name == "generation") {
				$listgeneration  = getListGeneration();
				$inputdisp = createHtmlList("generation", $listgeneration, "");
			}
			else {
				$inputdisp = "<input type='text' name='$name' value='$value'>";
			}
		}
		array_push($adatadisp, $inputdisp);
	
	}
	return $adatadisp;
}
/**
 * Funcion para mostar en pantalla editar/agregar/eliminar dispensarios
 * @param string $mode __update__ -> para actualizar dispensarios <br>
 *                     __add__ -> para agregar dispensarios <br>
 *                     __del__ -> para borrar dispensarios
 * @return html Muestra el formulario para editar informacion del dispensario
 */
function editDetailDisp($mode){
	if ($mode == '_edit_') {
		$adatadisp = cEditFormDisp();
                $label = "Editar";
	}
	elseif ($mode == '_add_'){
		$adatadisp = cAddFromDisp();
                $label = "Agregar";
	}
	$baselink = "<a href='?view_det_disp=". $_GET['view_det_disp'] ."&c_id=". $_GET['c_id'] .
			"&det=". $_GET['det'] ."'>Regresar</a>";
	$graldata = genBrowser($baselink, '50%');
	$graldata .= editDispenser($label, $adatadisp);

	print maintmpl(getNameCustomer($_REQUEST['c_id']), $graldata, '50%');
	return;
}

function checkIfDispExists($nodispenser, $idclien){
	$val = dbSearchDisp($nodispenser, $idclien);
	if ($val > 0)
		return 1;
	return 0;
}
/**
 * Funcion para editar los datos en la DB de los dispensarios
 * @param string $mode __update__ -> para actualizar dispensarios <br>
 *                     __add__ -> para agregar dispensarios <br>
 *                     __del__ -> para borrar dispensarios
 * @return string Devuelve el mensaje de exito o error
 */
function editDbDisp($mode){
	$val = "! Cambios Realizados";
	if ($mode == "_update_"){
		updateDbDisp($_REQUEST);
		updateDbMb($_REQUEST);
	}
	elseif ($mode == "_add_"){
		/**
                 * @TODO: Verificar la validez de los campos
                 */
		if ($_REQUEST['nodispenser'] == "" or checkIfDispExists($_REQUEST['nodispenser'], $_REQUEST['c_id'])){
			$val = "Valor no valido o dispensario ya se encuentra en el sistema";
		} else {
			$id = addDBDisp($_REQUEST);
			addDBMb($_REQUEST, $id);
		}
	}
	elseif ($mode == "_del_"){
		delDBDisp($_REQUEST['iddispenser'], $_REQUEST['c_id']);
	}
	return $val;
}
//*****************************
function soft_pumps($customer,$dispenser){
    $selectdis="SELECT CRMsoft_catalog.nombre,CRMdispenser.nodispenser,CRMdispenser.idcustomer,CRMdispenser.id,CRMsoft_pumps.idsoft,CRMsoft_pumps.estatus,CRMsoft_pumps.version,
                                                                                                                                                  CRMsoft_pumps.id,CRMsoft_categories.descripcion
                                                                                                                                            FROM CRMdispenser INNER JOIN CRMsoft_pumps INNER JOIN CRMsoft_catalog INNER JOIN CRMsoft_categories 
                                                                                                                                            ON CRMdispenser.id=CRMsoft_pumps.idpump and CRMsoft_pumps.idsoft=CRMsoft_catalog.idsoft and CRMsoft_catalog.idcategory=CRMsoft_categories.idcategory
                                                                                                                                            WHERE CRMdispenser.idcustomer='$customer' and CRMdispenser.nodispenser='$dispenser'";
                                                                                                                           $doselectdis=mcq($selectdis,$db);  
                                                                                                                           $info=  array();
                                                                                                                           
                                                                                                                           while($fetch=  mysql_fetch_array($doselectdis)){
                                                                                                                               $info[]=$fetch;
                                                                                                                               
                                                                                                                           }
                                                                                                                           return $info;
}

/**
 * Funcion que se encarga de mostrar los datos de los dispositivos en pantalla
 * @param bool $label 
 */

function viewDetailsDevices($label=false){
	// Primero obtenemos el nombre del cliente para mostralo
	$nombre = getNameCustomer($_REQUEST['c_id']);
	
	$graldata = "";

	if ($label) {
            $graldata .= genHtmlInfo($label);
	}
	
	//Vamos a obtener la informacion de los dispensarios conectados a la estacion
	$sql = "SELECT a.id, a.nodispenser, c.description as brand, d.description as generation, a.model, a.sernum, a.datemanufacter, b.macaddress,
			b.ip, b.docsize, b.typecard, b.localversion, b.netversion, b.cenamver,
			b.typeconn FROM $GLOBALS[TBL_PREFIX]dispenser as a INNER JOIN 
			$GLOBALS[TBL_PREFIX]motherboard as b ON a.id = b.iddispenser
                        INNER JOIN
			$GLOBALS[TBL_PREFIX]brands as c ON a.brand = c.id
                        INNER JOIN
			$GLOBALS[TBL_PREFIX]generation as d ON a.generation = d.id
                        WHERE
			a.idcustomer = ". $_REQUEST['c_id'] ." ORDER BY a.id";
	$result = mcq($sql, $db);
	$datadisp = "";
	$baselink = "?view_det_disp=". $_GET['view_det_disp'] ."&c_id=". $_GET['c_id'] .
				"&det=". $_GET['det'] ."";
	while ($row = mysql_fetch_array($result)) {
            $id = $row[nodispenser];
            $dis=soft_pumps($_REQUEST['c_id'], $id);
		$datadisp .= "
                        <tr class='myHiddenDiv'><td>
                            <div class='myHiddenDiv' id='myHiddenDiv_$id'>
                                <div class='popup'>
                                    <div class='popup-header'>
                                        <h2>Dispensario $id</h2>
                                        <a href='javascript:;' onclick=\"$.closePopupLayer('myStaticPopup_$id')\" title='Cerrar' class='close-link'>Cerrar</a>
                                    </div>
                                    <div class='popup-body'>
                                        <table>
                                            <tr><td colspan='2'><hr></td></tr>
                                            <tr><td  width='100'colspan='2'><b>SOFTWARE</b><hr></td></tr>";
                                                foreach($dis as $d){
                                            $datadisp.="<tr><td width='200'><b>Nombre:</b></td>
                                                <td width='100'align=right>$d[nombre]</td>
                                            </tr>
                                                    <tr><td width='200'><b>Version</b></td>
                                                <td width='100'align=right>$d[version]</td>
                                            </tr>";
                                                }
                                            $datadisp.="
                                            <tr><td colspan='2'><hr></td></tr>
                                            <tr><td width='100' colspan='2'><b>HARDWARE</b><hr></td></tr>
                                            <tr><td width='200'><b>No. Serie:</b></td>
                                                <td width='100'align=right>$row[sernum]</td>
                                            </tr>
                                            <tr><td ><b>Fecha Fab:</b></td>
                                                <td align=right>$row[datemanufacter]</td>
                                            </tr>
                                            <tr><td ><b>MacAddr:</b></td>
                                                <td align=right>$row[macaddress]</td>
                                            </tr>
                                            <tr><td ><b>DOC Size:</b></td>
                                                <td align=right>$row[docsize] MB</td>
                                            </tr>
                                            <tr><td ><b>Tipo MB:</b></td>
                                                <td align=right>". checkTypeCard($row[typecard]) ."</td>
                                            </tr>
                                            <tr><td ><b>Version Local:</b></td>
                                                <td align=right>$row[localversion]</td>
                                            </tr>
                                            <tr><td ><b>Version Red:</b></td>
                                                <td align=right>$row[netversion]</td>
                                            </tr>
                                            <tr><td ><b>Version Cenam :</b></td>
                                                <td align=right>". checkCenamVer($row[cenamver]) ."</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td></tr>
			<tr>
                            <td>
                                <b style='color: blue'>
                                    <a href='javascript:;' onclick='openStaticPopup($id)' title='Dispensario $id'>Dispensario $id</a>
                                </b>
                            </td>
                            <td>". createLink($baselink, "edisp", $row[id]) ."</td>
                            <td>$row[brand]</td>
                            <td>$row[generation]</td>
                            <td>$row[model]</td>
                            <td align=right>$row[ip]</td>
                            <td align=right>". checkTypeConn($row[typeconn]) ."</td>
			</tr>
		";
	}
	// Lo agregamos a la plantilla de los dispensarios
	$graldata .= genTableDisp($datadisp, $_REQUEST['c_id']);
	
	/*TODO: Falta agregar la parte del Ultramax y del Sistema de Telemedicion*/

	//Mostramos la pagina completa
	print maintmpl($nombre, $graldata, '1024px');

}

/*********************Relacion de los servidores ****************************/
/**
 * Se genera el parseo de las variables que indica el origen del server
 * @param array $data
 * int serverorigin - (0->local, 1->Asic, 2-> Cliente) <br>
 * string dateprovpurch - Fecha de compra del proveedor <br>
 * string datecusrpurch - Fecha de venta al cliente <br>
 * @return array Informacion del servidor
 */
function processDGral($data){
	switch ($data['serverorigin']) {
		case "0":
			$val = "Ictc";
			break;
		case "1":
			$val = "Asic";
			break;
		case "2":
			$val = "Cliente";
			break;
	}
	$data['serverorigin'] = $val;
	if ($data['dateprovpurch'] == '0000-00-00') 
		$data['dateprovpurch'] = "N/D";
	if ($data['datecusrpurch'] == '0000-00-00') 
		$data['datecusrpurch'] = "N/D";
	return $data;
}
/**
 * Muestra en pantalla los datos encontrados <br>
 * respecto a los servidores de la Estacion
 * @param bool $label default false
 */
function viewDetailsServers($label=false){
    $nombre = getNameCustomer($_REQUEST['c_id']);
    
    $graldata = "";

    if ($label) {
        $graldata .= genHtmlInfo($label);
    }

    $dservers = getDbdServers($_REQUEST['c_id']);
    if (mysql_num_rows($dservers) == 0){
        $graldata = genHtmlInfo("No hay informacion de servidores");
        $graldata .= buttonAddServer($_REQUEST['c_id']);
    }
    else{
        $graldata ="
            <script type='text/javascript'>
                    $.setupJMPopups({
                        screenLockerBackground: '#003366',
                        screenLockerOpacity: '0.7'
                    });

                    function openStaticPopup(id) {
                        var static = 'myStaticPopup_' + id;
                        var hidden = 'myHiddenDiv_'+ id;
                        $.openPopupLayer({
                            name: static,
                            width: 350,
                            target: hidden
                        });
                    }
		</script>
            <table class='crm'>
            <tr>
                <td width='80px' align='center'></td>
                <td></td>
                <td width='150px' align='center'><b>Sistema Operativo</b></td>
                <td width='100px' align='center'><b>Version</b></td>
                <td width='100px' align='center'><b>Origen</b></td>
                <td width='150px' align='center'><b>Compra Proveedor</b></td>
                <td width='150px' align='center'><b>Compra Cliente</b></td>
                <td width='150px' align='left'><b>Host</b></td>
            </tr>";
        while ($row = mysql_fetch_array($dservers, MYSQL_ASSOC)){
            $datagral = processDGral($row);
            $graldata .= createTableServers($row['priority'], $datagral);

        }
        $graldata .= buttonAddServer($_REQUEST['c_id']);
        $graldata .="
            </table>
            ";
    }


    // Mostramos la pantalla
    print maintmpl($nombre, $graldata, '1024px');
    return;
}
/**
 * Muestra en pantalla un formulario para editar los detalles del servidor <br>
 * @author PaKo Anguiano
 * @param int $status 1-> Indica que se agrega un servidor<br>
 *                    0-> Indica que se edita un servidor<br>
 */
function viewAddServers($status=1){
    $nombre = getNameCustomer($_REQUEST['c_id']);

    $graldata = "";
    $graldata .= createTableAddServers($_REQUEST['c_id'], $status);
    
    // Mostramos la pantalla
    print maintmpl($nombre, $graldata, '50%');
    return;
}
?>
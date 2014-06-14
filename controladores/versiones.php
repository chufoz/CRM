<?php

extract($_REQUEST);

//***************************************************************
include("header.inc.php");
if($_POST['btn_addsoft']){//Añadir software
addsoft();
}
elseif($_POST['modsoft']){//Modificar sofware
modsoft();
}
elseif($_POST['delsoft']){//Borrar software
delsoft();
}
elseif($_POST['btn_addver']){//Añadir version
addver();
}
elseif($_POST['modver']){//Modificar version
modver();
}
elseif($_POST['delver']){//Borrar version
delver();
}
elseif ($_GET['addsoft']){//Formulario alta de software
frmaltasoft();
}
elseif ($_GET['addver']){//Formulario alta de version
frmaltaver();
}
elseif ($_GET['editsoft']){//Listado de Software para modificacion
listasoft();
}
elseif($_GET['dets'] and $_GET['ids']){//Formulario Modificacion de software
frmmodsoft();
}
elseif($_GET['detv'] and $_GET['idv']){//Formulario Modificacion de version
frmmodver();
}
else{
frmmain();
}
EndHTML();

//***************************************************************



//***************************************************************
function addsoft(){
	if ($_POST['nom']=="" or $_POST['desc']==""){
	message("<font color=red>Error: <br>Alguno de los campos se encuentra vacio</font>",$_SERVER['PHP_SELF'],'Regresar');
	}
	else{
	$chk="SELECT * FROM CRMsoftware WHERE soft_nombre='".$_POST['nom']."'";
	$res=mcq($chk,$db);
		if (mysql_num_rows($res)>0){
		message("Error: El programa ya se encuentra registrado",$_SERVER['PHP_SELF'],'Regresar');
		exit();
		}
		else{
		$sql="INSERT INTO CRMsoftware (soft_nombre,soft_desc) VALUES (UCASE('".$_POST['nom']."'),UCASE('".$_POST['desc']."')) ";
		mcq($sql,$db);
		message("El registro fue insertado exitosamente",$_SERVER['PHP_SELF'],'Regresar');
		}
	}
}
//**************************************************************
function addver(){
	if ($_POST['prog']=="" or $_POST['ver']=="" or $_POST['rev']==""){
	message("<font color=red>Error: <br>Alguno de los campos se encuentra vacio</font>",$_SERVER['PHP_SELF'],'Regresar');
	}
	else{
		$sql="SELECT * FROM CRMversiones WHERE vers_sid=".$_POST['prog']." AND vers_ver='".$_POST['ver']."' AND vers_rev='".$_POST['rev']."'";
		$res=mcq($sql,$db);
		if (mysql_num_rows($res)>0){
			message("<font color=red>Error: La version ya existe</font>",$_SERVER['PHP_SELF'],'Regresar');
		}
		else{
			$sql="INSERT INTO CRMversiones (vers_sid,vers_ver,vers_rev,vers_com) VALUES ('".$_POST['prog']."','".$_POST['ver']."','".$_POST['rev']."','".$_POST['coms']."') ";
			mcq($sql);
			message("El registro fue insertado exitosamente",$_SERVER['PHP_SELF'],'Regresar');
		}
	}
}
//*********************************************************
function frmaltasoft(){
echo "
<table width='100%' border=0>
<tr><td>
	<fieldset>
	<legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Alta de Software: &nbsp;</legend>
	<form name='soft' method='POST' action=".$HTTP_SERVER_VARS['PHP_SELF'].">
		<table width=500 border=0>
			<tr>
				<td width=100>Nombre:</td>
				<td width=400><input type='text' name='nom' value=''></td>
			</tr>
			<tr>
				<td>Descripción:</td>
				<td align='left'><input type='text' name='desc' value='' size=70></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td colspan=2 align='center'><input type='Submit' name='btn_addsoft' value='Aceptar'></td>
			</tr>
		</table>
	</fieldset>
</tr><td>
</table>
";
}
//*********************************************************
function frmaltaver(){
$sql="SELECT soft_id, soft_nombre from CRMsoftware ORDER BY soft_nombre";
$res=mcq($sql,$db);
echo "
<table width='100%' border=0>
<tr><td>
	<fieldset>
	<legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Alta de Versión: &nbsp;</legend>
	<form name='soft' method='POST' action=".$HTTP_SERVER_VARS['PHP_SELF'].">
		<table width=500 border=0>
			<tr>
				<td width=100>Programa:</td>
				<td width=400>
				<select name='prog'>
				<option value=''></option>";
				while($row=mysql_fetch_row($res)){
					echo "<option value=$row[0]>$row[1]</option>";
				}
				echo"
				</select>
				</td>
			</tr>
			<tr>
				<td width=100>Versión:</td>
				<td width=400><input type='text' name='ver' value=''></td>
			</tr>
			<tr>
				<td>Revision:</td>
				<td align='left'><input type='text' name='rev' value=''></td>
			</tr>
			<tr>
				<td valign='top'>Comentarios:</td>
				<td align='left'>
					<textarea cols=40 rows=8 name='coms'></textarea>
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td colspan=2 align='center'><input type='Submit' name='btn_addver' value='Aceptar'></td>
			</tr>
		</table>
	</fieldset>
</tr><td>
</table>
";
}
//*********************************************************
function frmmodsoft(){
$sql="SELECT soft_id, soft_nombre, soft_desc from CRMsoftware WHERE soft_id=".$_GET['ids']."";
$res=mcq($sql,$db);
$data=mysql_fetch_row($res);
echo "
<table width='100%' border=0>
<tr><td>
	<fieldset>
	<legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Alta de Software&nbsp;</legend>
	<form name='soft' method='POST' action=".$HTTP_SERVER_VARS['PHP_SELF'].">
		<table width=500 border=0>
			<tr>
				<td width=100>Nombre:</td>
				<td width=400><input type='text' name='nom' value='$data[1]'></td>
			</tr>
			<tr>
				<td>Descripción:</td>
				<td align='left'><input type='text' name='desc' value='$data[2]' size=70></td>
			</tr>
			<tr><td>&nbsp;<input type='hidden' name='ids' value='$data[0]'></td></tr>
			<tr>
				<td colspan=2 align='center'><input type='Submit' name='modsoft' value='Modificar'>&nbsp;&nbsp;<input type='Submit' name='delsoft' value='Eliminar'></td>
			</tr>
		</table>
	</fieldset>
</tr><td>
</table>
";
}
//***********************************************************
function modsoft(){
	if ($_POST['nom']=="" or $_POST['desc']==""){
		message("<font color=red>Error: <br>Alguno de los campos se encuentra vacio</font>",$_SERVER['PHP_SELF'],'Regresar');
		}
	else{
			$sql="UPDATE CRMsoftware SET soft_nombre=UCASE('".$_POST['nom']."'), soft_desc=('".$_POST['desc']."') WHERE soft_id=".$_POST['ids']."";
			mcq($sql,$db);
			message("El registro fue modificado exitosamente",$_SERVER['PHP_SELF'],'Regresar');
	}
}
//************************************************************
function delsoft(){
	$sql="SELECT * FROM CRMversiones WHERE vers_sid = ".$_POST['ids']."";
	$res=mcq($sql,$db);
	if (mysql_num_rows($res)>0){
		message("<font color=red>Error: <br>No se puede eliminar el registro mientras haya versiones asociadas</font>",$_SERVER['PHP_SELF'],'Regresar');
	}
	else{
		$sql="DELETE FROM CRMsoftware WHERE soft_id=".$_POST['ids']."";
		mcq($sql,$db);
		message("El registro fue eliminado exitosamente",$_SERVER['PHP_SELF'],'Regresar');
	}

}
//*********************************************************
function frmmodver(){
$sql="SELECT a.vers_id, b.soft_nombre, a.vers_ver, a.vers_rev, a.vers_com
			FROM CRMversiones AS a INNER JOIN CRMsoftware AS b ON a.vers_sid=b.soft_id WHERE a.vers_id=".$_GET['idv']."";
$res=mcq($sql,$db);
$data=mysql_fetch_row($res);

echo "
<table width='100%' border=0>
<tr><td>
	<fieldset>
	<legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Modificar Version&nbsp;</legend>
	<form name='soft' method='POST' action=".$HTTP_SERVER_VARS['PHP_SELF'].">
		<table width=500 border=0>
			<tr>
				<td width=100>Nombre:</td>
				<td width=400>$data[1]</td>
			</tr>
			<tr>
				<td>Version:</td>
				<td>$data[2]</td>
			</tr>
			<tr>
				<td>Revision:</td>
				<td>$data[3]</td>
			</tr>
			<tr>
				<td valign='top'>Comentarios:</td>
				<td><textarea cols=40 rows=8 name='coms'>$data[4]</textarea></td>
			</tr>
			<tr><td>&nbsp;<input type='hidden' name='idv' value='$data[0]'></td></tr>
			<tr>
				<td colspan=2 align='center'><input type='Submit' name='modver' value='Modificar'>&nbsp;&nbsp;<input type='Submit' name='delver' value='Eliminar'></td>
			</tr>
		</table>
	</fieldset>
</tr><td>
</table>
";
}
//************************************************************
function modver(){
	$sql="UPDATE CRMversiones SET vers_com='".$_POST['coms']."' WHERE vers_id=".$_POST['idv']."";
	mcq($sql,$db);
	message("El registro fue modificado exitosamente",$_SERVER['PHP_SELF'],'Regresar');
}
//************************************************************
function delver(){
$sql="SELECT dp_soft FROM CRMdetprog";
$res=mcq($sql,$db);
$vers=array();
while ($row=mysql_fetch_row($res)){
$prog=unserialize($row[0]);
	foreach ($prog as $key => $value){
		$ln=explode("|",$value);
		$vers[]=$ln[1];
	}
}
$vers=array_unique($vers);
if (in_array($_POST['idv'],$vers)==true){
	message("Error: no puede eliminarse el registro",$_SERVER['PHP_SELF'],'Regresar');
}
else{
	$sql="DELETE FROM CRMversiones WHERE vers_id=".$_POST['idv']."";
	mcq($sql,$db);
	message("El registro fue eliminado exitosamente",$_SERVER['PHP_SELF'],'Regresar');
}
}
//*********************************************************
function listasoft(){
	$sql="SELECT soft_id, soft_nombre, soft_desc FROM CRMsoftware";
	$res=mcq($sql,$db);
	echo "
	<table border=0 width=100%>
		<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td>
			<fieldset>
				<legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Programas registrados: &nbsp;</legend>
				<table border=0 width=600 class=crm>
					";
					if (mysql_num_rows($res)==0){
						echo "<tr><td align=center>No existen registros</td></tr>";
					}
					else{
					echo "<tr><td align=center><b>Nombre</b></td><td align=center><b>Descripcion</b></td></tr>";
						while ($soft=mysql_fetch_row($res)){
							echo "
							<tr onmouseover=\"style.background='#CCCCCC';\"  onmouseout=\"style.background='#FFFFFF';\">
								<td style='cursor:pointer'  OnClick='gobla(" . $soft[0] . ");'>$soft[1]</td>
								<td style='cursor:pointer'  OnClick='gobla(" . $soft[0] . ");'>$soft[2]</td>
							</tr>";
						}
					}
					echo"
					
				</table>
			</fieldset>
			</td>
		</tr>
	</table>
				";
}
//**************************************************************
function frmmain(){
	$sql="SELECT * from CRMsoftware ORDER BY soft_nombre";
	$res=mcq($sql,$db);
		echo "
		<table width='100%' border=0>
			<tr>
				<td><br>
					<fieldset><legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Versiones de Software:&nbsp;</legend>
							<table width=400 border=0>
								<tr>
									<td>
										<img src='arrow.gif'>&nbsp;
										<a href='".$HTTP_SERVER_VARS['PHP_SELF']."?addsoft=1' class='bigsort'>Añadir Software</a>
										";
										if (mysql_num_rows($res)>0){
										echo "
										<br>
										<img src='arrow.gif'>&nbsp;
										<a href='".$HTTP_SERVER_VARS['PHP_SELF']."?editsoft=1' class='bigsort'>Editar Software</a>
										<br>
										<img src='arrow.gif'>&nbsp;
										<a href='".$HTTP_SERVER_VARS['PHP_SELF']."?addver=1' class='bigsort'>Añadir Version</a>
										";
										}
										echo "
										<br><br>
									</td>
								</tr>
							</table>
					</fieldset>
				</td>
			</tr>
		</table>
		";

while ($row=mysql_fetch_row($res)){

$sql="SELECT a.vers_id, b.soft_nombre, a.vers_ver, a.vers_rev
			FROM CRMversiones AS a INNER JOIN CRMsoftware AS b ON a.vers_sid=b.soft_id WHERE b.soft_id=$row[0] ORDER BY b.soft_nombre,a.vers_ver,a.vers_rev ASC";
$vers=mcq($sql,$db);
echo "
<table border=0>
<br><br>
	<tr>
		<td>
		<fieldset>
			<legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;Versiones : &nbsp;$row[1]</legend>
			<table border=0 width=600 class=crm>
				";
				if (mysql_num_rows($vers)==0){
					echo "<tr><td align=center>No existen registros</td></tr>";
				}
				else{
				echo "<tr><td align=center><b>Programa</b></td><td align=center><b>Version</b></td><td align=center><b>Revision</b></td>";
					while ($ver=mysql_fetch_row($vers)){
						echo "
						<tr onmouseover=\"style.background='#CCCCCC';\"  onmouseout=\"style.background='#FFFFFF';\">
							<td style='cursor:pointer'  OnClick='goblav(" . $ver[0] . ");' align=center>$ver[1]</td>
							<td style='cursor:pointer'  OnClick='goblav(" . $ver[0] . ");' align=center>$ver[2]</td>
							<td style='cursor:pointer'  OnClick='goblav(" . $ver[0] . ");' align=center>$ver[3]</td>
						</tr>";
					}
				}
				echo"
				
			</table>
		</fieldset>
		</td>
	</tr>
</table>
";
}
echo "<br><br><br><br>";
}
//*****************************************************************************
?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	function gobla(i) {
	document.location="versiones.php?dets=1&ids=" + i + "";
			}
	function goblav(i) {
	document.location="versiones.php?detv=1&idv=" + i + "";
			}
	//-->
	</SCRIPT>
<?php
//************************
//*****************************************************************************
function message($msg_done, $red_liga, $red_msg) {

        echo "<table align=center width=500 border=0>";
        echo "<tr><td><br></td></tr>";
        echo "<tr>";
        echo "<td align=center>";
        echo $msg_done;
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td align = center>";
        echo "<b><a href='$red_liga'>$red_msg</a></b>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
}
?>

<?php
/* ********************************************************************
 * ICTC
 * Copyright (c) 2012 
 * Cvillafuerte (cvillafuerte@ictc.com.mx)
 **********************************************************************
 */

 // This script handles editing of extra fields

include("header.inc.php");
// Get rid of the crappy header leftovers
print "</td></tr></table>";
?>
 	<SCRIPT LANGUAGE="javascript" SRC="cookies.js"></SCRIPT>
<?

AdminTabs("catalogo");
$to_tabs=array('info','todos');
$tabbs["info"] = array("sof_catalog.php" => "Informacion", "comment" => "Informacion");
$tabbs["todos"] = array("sof_catalog.php?todos=1" => "Catalogo", "comment" => "Todos");
$tabbs['add']=array('sof_catalog.php?add=1'=>'Agregar','comment'=>'Agregar');
$tabbs['addquery']=array('sof_catalog.php?do=Enviar'=>'Agregar','comment'=>'Agregar');
$tabbs['edit']=array('sof_catalog.php?edit=yes'=>'Editar','comment'=>'Editar');


if($_REQUEST['todos']=='1'){
    $navid='todos';
}elseif($_REQUEST['add']=='1' or $_REQUEST['software']=='addsoft' or $_REQUEST['modulos']=='addmodulo' or $_REQUEST['categoria']=='addcategoria'){
    $navid='add';
}elseif($_REQUEST['do']=='Enviar'){
    $navid='do';
}elseif($_REQUEST['edit']=='yes'){
    $navid='edit';
}else{
    $navid='info';
}

InterTabs($to_tabs, $tabbs, $navid);
MustBeAdmin();

if($_REQUEST['todos']=='1'){
    showcatalog();
}
elseif($_REQUEST['add']=='1' or $_REQUEST['software']=='addsoft' or $_REQUEST['modulos']=='addmodulo' or $_REQUEST['categoria']=='addcategoria'){
    addcatalog();
    
    }elseif($_REQUEST['do']=='AddSoftware' or $_REQUEST['do']=='AddModulo' or $_REQUEST['do']=='AddCategory'){
        addquery();
    }elseif($_REQUEST['edit']=='yes'){
        edit();
    }elseif($_REQUEST['editsoft']=='Editar' or $_REQUEST['EditModulo']=='EditModulo' or $_REQUEST['EditCategoria']=='EditCategoria'){
        processedit();
    }
        else{
        info();
    }
//************Mostrar Catalogo*******
function showcatalog(){
                                     ?>
                                        <style type="text/css">
                                            .fila_0 { background-color: #FFFFFF;}
                                            .fila_1 { background-color: #E1E8F1;}
                                        </style>
                                        <?php
       $selectcatalog="Select CRMsoft_catalog.idsoft,CRMsoft_catalog.nombre,CRMsoft_catalog.descripcion,CRMsoft_categories.descripcion as cd,CRMsoft_categories.idcategory
                                From CRMsoft_catalog  INNER JOIN CRMsoft_categories ON  CRMsoft_catalog.idcategory=CRMsoft_categories.idcategory";
       $doselect=  mcq($selectcatalog, $db);
       $table="<table ' border=1 align=center>
                        <th bgcolor='#E1E8F1'></th>
                        <th bgcolor='#E1E8F1'>Nombre</th>
                        <th bgcolor='#E1E8F1'>Descripcion</th>
                        <th bgcolor='#E1E8F1'>Categoria</th>
                        <th bgcolor='#E1E8F1'>Modulo</th>";
         $i = 0 ;
        while($fetch=  mysql_fetch_array($doselect)){
            $por=$i%2;
             $table.= "<tr>
                            <td width=5% align=center><a href='sof_catalog.php?edit=yes&softid=$fetch[idsoft]&nombre=$fetch[nombre]&idc=$fetch[idcategory]'><img src='gtk-edit.png' align='top'></a></td>
                            <td class='fila_$por'>$fetch[nombre]</td>
                            <td class='fila_$por'>$fetch[descripcion]</td>
                            <td class='fila_$por'><a href='sof_catalog.php?edit=yes&idc=$fetch[idcategory]&nombre=$fetch[cd]&editcategory=yes'>   <img src='gtk-edit.png' align='top'></a>&nbsp;&nbsp;$fetch[cd]</td>
                            <td class='fila_$por'>".  namemodulo($fetch[idsoft])."</td>
                        </tr>";
             $i++;
       
    }
    $table.="<tr>
                     <td colspan=5 align=right>
                     <form> 
                     <input type='submit' name='software' value='addsoft'>
                    <input type='submit' name='modulos' value='addmodulo'>
                    <input type='submit' name='categoria' value='addcategoria'>
                    </td>
                     
	</form></td></tr>
        </table>";
    echo $table;
    }
//**********modulos disponibles******
function namemodulo($idsoft){
    $modulos="SELECT CRMsoft_modules.idmodule,CRMsoft_modules.nombre as n,CRMsoft_catalog.nombre,CRMsoft_catalog.idsoft FROM CRMsoft_catalog INNER JOIN CRMsoft_modules ON CRMsoft_catalog.idsoft=CRMsoft_modules.idsoft
    where CRMsoft_catalog.idsoft='$idsoft'";
    $domodulos=  mcq($modulos, $db);
    if(mysql_num_rows($domodulos)>=1){
        $table="<table>";
   while($f=  mysql_fetch_array($domodulos)){
       $table.="<tr>
                            <td><a href='sof_catalog.php?edit=yes&idm=$f[idmodule]&nombre=$f[n]&editmodulo=yes'><img src='gtk-edit.png' align='top'></a></td>
                            <td>        &nbsp;&nbsp;.-".$f[n]. "</td>
                        </tr>";
   }
   $table.="</table>";
   return $table;
}else{
    return "";
}
}
//*************Info********************
function info(){
echo "
    <table width='100%' border=0>
	<tr>
	    <td width='22'>&nbsp;</td>
	    <td>
	    <table width='90%'>
		<tr>
		    <td>
		    <fieldset>
		    <legend>&nbsp;<img src='crmlogosmall.gif'>&nbsp;&nbsp;<font size='+1'>Informaci&oacute;n</font></legend>

		    Modulo de administracion de Catalogo de Software
		    </fieldset>
		    </td>
		</tr>
	    </table>
	    </td>
	</tr>
    </table>
";
}
//*****************Agregar***********
function addcatalog(){
    $soft="Select * From CRMsoft_catalog";
    $dosoft=  mcq($soft, $db);
    $categoria="Select * From CRMsoft_categories";
    $docategoia=mcq($categoria,$db);
    $form="<form>
                          
                                       <table align=center>";
                                            if($_REQUEST['software']=='addsoft'){
                                                $form.="<tr><td bgcolor='E1E8F1' colspan=2 align=center><b>Agregar Software</b><br><br></td></tr>
                                                    <tr>
                                                  <td bgcolor='E1E8F1'><b>Nombre:</b></td><td><input type='text' name='softname' style='width:200px;'><br>
                                                  </td>
                                            </tr>
                                        <tr>
                                                <td bgcolor='E1E8F1'><b>Descripcion:</b></td>
                                                <td><textarea rows=2 cols=20 name='descripcion' style='width:200px;'></textarea>
                                                </td>
                                        </tr>
                                        <tr>
                                               <td bgcolor='E1E8F1'><b>Category</b></td>
                                               <td>
                                                 <Select name='softcategory' style='width:200px;'>
                                                 <option value='0' selected></option>";
                                                while($fet=  mysql_fetch_array($docategoia)){
                                                $form.="<option value='$fet[idcategory]'>$fet[descripcion]</option>";
                                                }
                               $form.="</td>
                                      </tr>
                                      <tr>
                                            <td colspan=2 align=right>
                                                <input type='submit' name='do' value='AddSoftware'>
                                                </td>
                                      </tr>";        
                                            }else{
                                            if($_REQUEST['modulos']=='addmodulo'){     
                    $form.="<tr><td bgcolor='E1E8F1' colspan=2 align=center><b>Agregar Modulo</b><br><br></td></tr>
                                        <tr>
                                            <td bgcolor='E1E8F1'><b>Modulo:</b></td>
                                            <td><input type='text' style='width:200px;' name='softmodulo'>
                                            </td>      
                      </tr>";
                         $form.="<tr>
                                              <td bgcolor='E1E8F1'><b>Software:</b></td>
                                              <td><Select name='soft' style='width:200px;'>
                                              <option value='0' selected></option>";
                                              while($fet=  mysql_fetch_array($dosoft)){
                                               $form.="<option value='$fet[idsoft]'>$fet[nombre]</option>";
                                                }
                            $form.="</td></tr>
                                                <tr>
                                                <td colspan=2 align=right>
                                                <input type='submit' name='do' value='AddModulo'></td></tr> ";
                                            }
                                            else
                                                if ($_REQUEST['categoria']=='addcategoria') {
                                                    $form.="<tr><td bgcolor='E1E8F1' colspan=2 align=center><b>Agregar Categoria</b><br><br></td></tr>
                                                        <tr><td  bgcolor='E1E8F1'><b>Categoria:</b></td>
                                            <td><input type='text' name='categoryname'></td>
                                            </tr>
                                            <tr><td colspan=2 align=right>
                                            <input type='submit' name='do' value='AddCategory'></td></tr>";                
                                                     }
                                            }
                                $form.="</table>

                    </form>";
echo $form;
}
//**********Ejecutar Agregar*********
function addquery(){
    if($_REQUEST['do']=='AddSoftware'){
    $insertsof="Insert Into CRMsoft_catalog(nombre,descripcion,idcategory) Values('".$_REQUEST['softname']."','".$_REQUEST['descripcion']."','".$_REQUEST['softcategory']."')";
  mcq($insertsof,$db);

    }
    if($_REQUEST['do']=='AddModulo'){
    $insertmodulo="Insert Into CRMsoft_modules(nombre,idsoft)Values('".$_REQUEST['softmodulo']."','".$_REQUEST['soft']."')";
   mcq($insertmodulo, $db);
    }
    if($_REQUEST['do']=='AddCategory'){
      $insertcategory="Insert Into CRMsoft_categories(descripcion)Values('".$_REQUEST['categoryname']."')";  
      mcq($insertcategory, $db);
    }
 echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=sof_catalog.php?todos=1\">";
}
//***********Editar*******************
function edit(){
    $select="select * from CRMsoft_catalog where idsoft='$_REQUEST[softid]'";
    $do=mcq($select,$db);
    $fe=  mysql_fetch_array($do);
    $categoria="Select * From CRMsoft_categories";
    $docategoia=mcq($categoria,$db);
    $modulos="Select * From CRMsoft_modules where idsoft='$_REQUEST[softid]'";
$domodulos=mcq($modulos,$db);
    $form="<form><table align=center>";
                        if($_REQUEST['editmodulo']=='yes'){
                            $form.="<tr><td bgcolor='E1E8F1' colspan=2 align=center><b>Edit Modulo</b><br><br></td></tr><tr>
                            <td   bgcolor='E1E8F1'><b>Nombre Modulo</b></td>
                            <td><input type='text' name='moduleedit' style='width:200px;' Value='$_REQUEST[nombre]'>
                                        <input type='hidden' name='idm' value='$_REQUEST[idm]'></td>
                            </tr>
                            <tr><td colspan=2 align=right><input type='submit' name='EditModulo' value='EditModulo'></td></tr>";
                        }elseif($_REQUEST['editcategory']=='yes'){
                    $form.="<tr><td bgcolor='E1E8F1' colspan=2 align=center><b>Edit Categoria</b><br><br></td></tr><tr>
                            <td bgcolor='E1E8F1'><b>Nombre Categoria</b></td>
                            <td><input type='text' name='categoryedit' style='width:200px;' Value='$_REQUEST[nombre]'>
                                        <input type='hidden' name='idc' value='$_REQUEST[idc]'></td>
                            </tr>
                            <tr><td colspan=2 align=right><input type='submit' name='EditCategoria' value='EditCategoria'></td></tr>";
}else{
         $form.="<tr><td bgcolor='E1E8F1' colspan=2 align=center><b>Edit Sotware</b><br><br></td></tr>
                            <tr>
                            <td bgcolor='E1E8F1'><b>Nombre:</b></td>
                            <td ><input type='text' name='softname' style='width:200px;' value='$fe[nombre]'>
                                      <input type='hidden' name='software' value='$fe[idsoft]'></td>
                        </tr>
                        <tr>
                             <td bgcolor='E1E8F1'><b>Descripcion:</b></td>
                             <td><input type='text' name='descripcion' style='width:200px;' value='$fe[descripcion]'></td>
                        </tr>
                         
                          <tr>
                               <td bgcolor='E1E8F1'><b>Categoria:</b></td>
                              <td><Select name='softcategory' style='width:200px;'><option value='0' selected></option>";
                                      while($fet=  mysql_fetch_array($docategoia)){
                                         if($fet['idcategory']==$_REQUEST['idc']){                                                                                                                                                  
                                           $form.= "<Option value='$fet[idcategory]' selected>$fet[descripcion]</option>";    
                                          }else{
                                             $form.= "<Option value='$fet[idcategory]' >$fet[descripcion]</option>";
                                         }
                                       }                                                                                      
                    $form.="</td>
                        </tr>
                        <tr><td colspan=2 align=right><input type=submit name='editsoft' value='Editar'></td></tr>";
}
                    $form.="</table></form>";
    echo $form;
}

//*********Ejecutar Editar************
function processedit(){

     $update="Update CRMsoft_catalog set nombre='$_REQUEST[softname]',descripcion='$_REQUEST[descripcion]',idcategory='$_REQUEST[softcategory]' where idsoft='$_REQUEST[software]'";
       mcq($update,$db);
    
    if($_REQUEST['EditModulo']=='EditModulo'){
          $insertmodulo="Update CRMsoft_modules Set nombre='$_REQUEST[moduleedit]' where idmodule='$_REQUEST[idm]'";
          mcq($insertmodulo,$db);
    }
    if($_REQUEST['EditCategoria']=='EditCategoria'){
          $insert="Update CRMsoft_categories Set descripcion='$_REQUEST[categoryedit]' where idcategory='$_REQUEST[idc]'";
          mcq($insert,$db);
    }
    echo "<META HTTP-EQUIV=\"Refresh\" Content=\"0; URL=sof_catalog.php?todos=1\">";
}

?>

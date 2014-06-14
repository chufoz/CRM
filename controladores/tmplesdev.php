<?php
/*Plantilla de los dispositivos conectados a la estacion de servicio*/

/**
 * Funcion que contiene la ventana principal para los dispositivos
 * @param string $es Nombre de la Estacion
 * @param html $data Datos a mostrar
 * @param string $width Ancho de la tabla (px,%)<br> default 90%
 * @return html Tabla con los datos a mostrar
 */
function maintmpl($es, $data, $width='90%'){
$table = <<< EOF
	<table width='$width' border=0>
		<tr>
			<td>&nbsp;</td>
			<td>
				<fieldset>
                                    <legend>&nbsp;
                                        <img src='crmlogosmall.gif'>&nbsp;&nbsp;
                                            <font size='+1'>
                                                <a href='customers.php?det=1&tab=26&c_id=$_REQUEST[c_id]'>{$es}</a>
                                            </font>&nbsp;
                                    </legend>
				$data
				</fiedset>
			</td>
		</tr>
	</table><br><br><br><br>
EOF;

return $table;

}
/**
 * Plantilla para el manejo de la informacion
 * @param string $data Hmtl con la informacion a mostrar
 * @return string Html con la tabla generada para mostrar la informacion en pantalla
 */
function genHtmlInfo($data) {
$table = <<< EOF
	<table  width='100%'>
		<tr>
			<td>
				<b>{$data}<b>
			</td>
		</tr>
	</table>
	<br>
EOF;
return $table;
}
/**
 * Plantilla para el manejo de la informacion similar a
 * <b style='color:blue'>genHtmlInfo</b>
 * @param string $data Hmtl con la informacion a mostrar
 * @param string $width Define el ancho de la tabla
 * @return string Html con la tabla generada para mostrar la informacion<br>
 *                en pantalla
 */
function genBrowser($data, $width='100%'){
$table = <<< EOF
	<table width='$width' border='0'>
		<tr>
			<td>
				{$data}
			</td>
		</tr>
	</table>
	<br>
EOF;
return $table;
}
/**
 * Plantilla para mostrar el boton de agregar nuevo servidor
 * @param int $idcustomer id del cliente
 * @return html Regresa tabla con boton para Agregar servidor
 */
function buttonAddServer($idcustomer){
$table = <<< EOF
	<table width='100%' border='0'>
                <tr><td height = '25px'></td></tr>
		<tr>
			<td align='right'>
				<form name="addserver" method='POST'>
					<input type='hidden' name='det' value=1> 
					<input type='hidden' name='c_id' value='{$idcustomer}'>
                                        <input type='hidden' name='view_det_serv' value=''>
					<input type='submit' name='addserver' value='Agregar Servidor'>
				</form>
			</td>
		</tr>
	</table>
EOF;
return $table;
}
/**
 * Plantilla para visualizar los dispensario
 * @param string $datadisp Contenido html para desplegar en pantalla
 * @param int $id Id del cliente
 * @return html Regresa tabla con informacion de los dispensarios
 */
function genTableDisp($datadisp, $id){
$table = <<< EOF
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
	<table class='crm' width='100%'>
		<tr>
                        <td width='60px'></td>
                        <td width='20'></td>
                        <td width='100px' align='center'><b>Marca</b></td>
                        <td width='100px' align='center'><b>Generacion</b></td>
			<td width='100px' align='center'><b>Modelo</b></td>
			<td width='100px' align='center'><b>IP</b></td>
			<td width='100px' align='center'><b>T. Conn</b></td>
		</tr>
		$datadisp
	</table>
        <table><tr><td height='35px'></td></tr></table>
        <table width='100%'>
            <tr>
                <td align='right'>
                    <form name="tbldisp" method='POST'>
                    <input type='submit' name='btnadddisp' value='Agregar'>
                    <input type='hidden' name='det' value=1>
                    <input type='hidden' name='c_id' value='{$id}'>
                    </from>
                </td>
            </tr>
        </table>
EOF;
return $table;
}
/**
 *Plantilla de creacion de la tabla de los servidores
 * @param int $id Numero de Servidor
 * @param array $datagral 
 * string os           -  Sistema operativo <br>
 * string version      -  Version de OS<br>
 * string serverorigin -  Proveedor<br>
 * string dateprovpurch-  Fecha de Compra al Proveedor<br>
 * string datecusrpurch-  Fecha de Compra del Cliente<br>
 * int customer        -  Id del cliente
 * @author PaKo Anguiano
 * @return html
 */
function createTableServers($id, $datagral){
$table = <<< EOF
            <tr class='myHiddenDiv'><td>
                <div class='myHiddenDiv' id="myHiddenDiv_$id">
                    <div class="popup">
                        <div class="popup-header">
                            <h2>Servidor $id</h2>
                            <a href="javascript:;" onclick="$.closePopupLayer('myStaticPopup_$id')" title="Cerrar" class="close-link">Cerrar</a>
                        </div>
                        <div class="popup-body">
                            <table>
                                <tr>
                                    <td width='10px'></td>
                                    <td width='200px'><b>Marca MB :</b></td>
                                    <td width='100px'>$datagral[brandmb]</td>
                                </tr>
                                <tr>
                                    <td width='10px'></td>
                                    <td><b>Modelo MB :</b></td>
                                    <td>$datagral[modelmb]</td>
                                </tr>
                                <tr>
                                    <td width='10px'></td>
                                    <td><b>RAM :</b></td>
                                    <td>$datagral[ramcap]</td>
                                </tr>
                                <tr>
                                    <td width='10px'></td>
                                    <td><b>Tipo conf. HD :</b></td>
                                    <td>$datagral[hdtypeconf]</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </td></tr>
            <tr>
                <td align='left'>
                    <b style='color: blue'>
                        <a href="javascript:;" onclick="openStaticPopup($id)" title="Servidor $id">Servidor {$id}</a>
                    </b>
                </td>
                <td><a href='customers.php?c_id=$datagral[idcustomer]&det=1&btn_edit_serv=$datagral[id]'>editar</a></td>
                <td>$datagral[os]</td>
                <td>$datagral[version]</td>
                <td>$datagral[serverorigin]</td>
                <td>$datagral[dateprovpurch]</td>
                <td>$datagral[datecusrpurch]</td>
                <td>$datagral[host]</td>
            </tr>
EOF;
return $table;

}
/**
 *Plantilla para agregar/editar/eliminar servidor
 * @param int $idcustomer Id del cliente
 * @author PaKo Anguiano
 * @return html
 */
function createTableAddServers($idcustomer, $status){
	$primarysrvvalue = "";	
    if(!$status){
        $edit = "<input type='hidden' name='edit' value='1'>";
        $delete = "
        <table width='100%' border='0'>
            <tr><td height = '15px'></td></tr>
            <tr>
                <td align='left'>
                    <input type='radio' name='status' value='1' checked>Activo<BR>
                    <input type='radio' name='status' value='0'>Eliminar<BR>
                </td>
            </tr>
        </table>";
        $datos = getServerbyID($_REQUEST['btn_edit_serv']);
		if ($datos['primarysrv'] == '1') {
			$primarysrvvalue = "CHECKED";
		}
	$cnn = getSrvConByID($_REQUEST['btn_edit_serv']);
    }else{
        $edit = "";
        $datos['os'] ="";
        $datos['version'] ="";
        $datos['brandmb'] ="";
        $datos['modelmb'] ="";
        $datos['ramcap'] ="";
        $datos['hdtypeconf'] ="";
        $datos['serverorigin'] ="";
        $datos['dateprovpurch'] ="";
        $datos['datecusrpurch'] ="";
        $datos['status'] =1;
		$primarysrvvalue = "CHECKED";
	$cnn['host']="";
	$cnn['noipaccount']="";
	$cnn['mgateway']="";
	$cnn['dgateway']="";
	$cnn['httpport']="";
	$cnn['sshport']="";

    }
    
    $table = <<< EOF
        <script type="text/javascript">
        $(document).ready(function() {
            $( "#new_fch_buy,#new_fch_sel" ).datepicker(
                $.extend({},
                $.datepicker.regional["es"], {
                    showStatus: true,
                    showOn: "both",
                    buttonImage: "calendar.png",
                    buttonImageOnly: true,
                    duration: "",
                    appendText: " dd/mm/aaaa",
                    changeYear: true,
                    changeMonth: true,
                    showButtonPanel: true
                }
            ));
            $('img.ui-datepicker-trigger').css({
                'cursor' : 'pointer',
                "vertical-align" : 'middle'
            });
            $('select [value="$datos[serverorigin]"]').attr("selected","selected");
        });
        </script>
        <form name="newaddserver" method='POST'>
		<table class='crm'>
			<tr>
				<td> 
					<input type='checkbox' class='radio' name='primarysrv' $primarysrvvalue>Servidor Primario
				</td>
			</tr>
		</table><br>
        <table class='crm'>
            <tr>
                <td width='150px' align='center'><b>Sistema Operativo</b></td>
                <td width='100px' align='center'><b>Version</b></td>
                <td width='100px' align='center'><b>Origen</b></td>
                <td width='150px' align='center'><b>Compra Proveedor</b></td>
                <td width='150px' align='center'><b>Compra Cliente</b></td>
            </tr>
            <tr>
                <td>
                    <input type='text' name='new_os' style='text-transform: uppercase;' value='$datos[os]'>
                </td>
                <td>
                    <input type='text' name='new_version' style='text-transform: uppercase;' value='$datos[version]'>
                </td>
                <td><select name='new_prov'>
                        <option value='0'>ICTC</option>
                        <option value='1'>ASIC</option>
                        <option value='2'>CLIENTE</option>
                    </select>
                </td>
                <td><input type='text' name='new_fch_buy' id='new_fch_buy' value='$datos[dateprovpurch]'></td>
                <td><input type='text' name='new_fch_sel' id='new_fch_sel' value='$datos[datecusrpurch]'></td>
            </tr>
        </table>
        <table><tr><td height = '25px'></td></tr></table>
        <table class='crm'>
            <tr>
                <td width='150px' align='center'><b>Marca MB</b></td>
                <td width='100px' align='center'><b>Modelo MB</b></td>
                <td width='100px' align='center'><b>Capacidad RAM</b></td>
                <td width='150px' align='center'><b>Tipo configuracion HD</b></td>
            </tr>
            <tr>
                <td>
                    <input type='text' name='brandmb' style='text-transform: uppercase;' value='$datos[brandmb]'>
                </td>
                <td>
                    <input type='text' name='modelmb' style='text-transform: uppercase;' value='$datos[modelmb]'>
                </td>
                <td>
                    <input type='text' name='ramcap' style='text-transform: uppercase;' value='$datos[ramcap]'>
                </td>
                <td>
                    <input type='text' name='hdtypeconf' style='text-transform: uppercase;' value='$datos[hdtypeconf]'>
                </td>
            </tr>
        </table>
	<table><tr><td height = '25px'></td></tr></table>
        <table class='crm'>
            <tr>
                <td width='150px' align='center'><b>Nombre de Host</b></td>
                <td width='100px' align='center'><b>Cuenta NOIP</b></td>
                <td width='100px' align='center'><b>Gateway</b></td>
                <td width='150px' align='center'><b>Interfaz</b></td>
		<td width='150px' align='center'><b>Puerto HTTP</b></td>
		<td width='150px' align='center'><b>Puerto SSH</b></td>
            </tr>
            <tr>
                <td>
                    <input type='text' name='srvhost' value='$cnn[host]'>
                </td>
                <td>
                    <input type='text' name='noipaccount' value='$cnn[noipaccount]'>
                </td>
                <td>
                    <input type='text' name='mgateway' value='$cnn[mgateway]'>
                </td>
                <td>
                    <input type='text' name='dgateway' value='$cnn[dgateway]'>
                </td>
		<td>
                    <input type='text' name='httpport' value='$cnn[httpport]'>
                </td>
		<td>
                    <input type='text' name='sshport' value='$cnn[sshport]'>
                </td>
            </tr>
        </table>
        $delete
        <table width='100%' border='0'>
            <tr><td height = '15px'></td></tr>
            <tr>
                <td width='auto'></td>
                <td align='right' width='60px'>
                    <input type='hidden' name='det' value=1>
                    <input type='hidden' name='c_id' value='{$idcustomer}'>
                    <input type='submit' name='newserver' value='Aceptar'>
                    $edit
                </td>
                <td align='right' width='60px'>
                    <input type='hidden' name='view_det_serv' value=''>
                    <input type='hidden' name='addserver' value=''>
                    <input type='reset' name='reset' value='Limpiar'>
                </td>
            </tr>
        </table>
        </form>
EOF;
    return $table;
}
/**
 *Plantilla de editar/agregar/eliminar dispensarios
 * @param int $label Muesta que se esta haciendo
 * @param array $adata
 * string            -  Sistema operativo <br>
 * string       -  Version de OS<br>
 * string  -  Proveedor<br>
 * string -  Fecha de Compra al Proveedor<br>
 * string -  Fecha de Compra del Cliente<br>
 * int         -  Id del cliente
 * @return html
 */
function editDispenser($label, $adata){
$table = <<< EOF
	<fieldset><legend>{$label} Dispensario</legend>
	<form name='editdisp' method='POST'>
	<table class='crm'  width='100%' align='center' border='0'>
		<tr>
			<td align='center'><b>No. de Dispensario</b></td>
                        <td align='center'><b>Marca</b></td>
                        <td align='center'><b>Generacion</b></td>
			<td align='center'><b>Modelo</b></td>
			<td align='center'><b>N. Serie</b></td>
			<td align='center'><b>F. Fab</b></td>
                </tr>
                <tr>
                        <td>{$adata[1]}</td>
                        <td>{$adata[2]}</td>
                        <td>{$adata[3]}</td>
                        <td>{$adata[4]}</td>
                        <td>{$adata[5]}</td>
                        <td>{$adata[6]}</td>
                </tr>
        </table>
        <table><tr><td height = '25px'></td></tr></table>
        <table class='crm'  width='100%' align='center' border='0'>
                <tr>
                        <td align='center'><b>Mac Address</b></td>
                        <td align='center'><b>IP</b></td>
                        <td align='center'><b>DOC Size</b></td>
                        <td align='center'><b>V. MB.</b></td>
                        <td align='center'><b>V. Local</b></td>
                        <td align='center'><b>V. Remota</b></td>
		</tr>
                <tr>
			<td>{$adata[7]}</td>
			<td>{$adata[8]}</td>
			<td>{$adata[9]}</td>
			<td>{$adata[10]}</td>
			<td>{$adata[11]}</td>
			<td>{$adata[12]}</td>
		</tr>
        </table>
        <table><tr><td height = '25px'></td></tr></table>
        <table class='crm'  width='100%' align='center' border='0'>
		<tr>
			<td align='center'><b>V. Cenam</b></td>
			<td align='center'><b>T. Conn</b></td>
                </tr>
                <tr>
			<td>{$adata[13]}</td>
			<td>{$adata[14]}</td>
                </tr>
		
	</table>
        <table><tr><td height = '25px'></td></tr></table>
        <table width='100%'>
            <tr>
                <td colspan = '2' align='right'>
                    {$adata[0]}
                </td>
            </tr>
        </table>
	</form>
	</fieldset>
EOF;
return $table;
}



?>

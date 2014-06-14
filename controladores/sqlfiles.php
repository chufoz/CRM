<?php
/*funciones de acceso a la base de datos (como si fueran 3capas*/
function esql($value) {
	return mysql_real_escape_string($value);
}

function updateDbDisp($data){
	/*Actualiza la base de datos de dispensarios*/
	$sql = "UPDATE $GLOBALS[TBL_PREFIX]dispenser SET
                brand = '". esql($data['brand']) ."',
                generation = '". esql($data['generation']) ."',
		model = '". esql($data['model']) ."', 
		sernum = '". esql($data['sernum']) ."', datemanufacter = 
		'". esql($data['datemanufacter']) ."' WHERE id = 
		'". esql($data['iddispenser']) ."'";
	mcq($sql,$db);
	return;

}

function updateDbMb($data){
	/* Actualiza la tabla de tarjetas madre */
	$sql = "UPDATE $GLOBALS[TBL_PREFIX]motherboard SET 
		macaddress = '". esql($data['macaddress']) ."', ip = 
		'". esql($data['ip']) ."', docsize = '". esql($data['docsize']) ."',
		typecard = '". esql($data['tcard']) ."', localversion = 
		'". esql($data['localversion']) ."', netversion = 
		'". esql($data['netversion']) ."', cenamver = 
		'". esql($data['vcenam']) ."', typeconn = 
		'". esql($data['tconn']) ."' WHERE iddispenser = 
		'". esql($data['iddispenser']) ."'";
	mcq($sql,$db);
	return;
}

function addDBDisp($data){
	$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]dispenser (idcustomer, nodispenser,model, brand, generation,
			sernum, datemanufacter) VALUES ('". esql($data['c_id']) ."', 
			'". esql($data['nodispenser']) ."', '". esql($data['model']) ."',
                        '". esql($data['brand']) ."',
                        '". esql($data['generation']) ."',
			'". esql($data['sernum']) ."', 
			'". esql($data['datemanufacter']) ."')";
	mcq($sql, $db);
	$sql = "SELECT LAST_INSERT_ID()";
	$val = mysql_fetch_row(mcq($sql, $db));
	return $val[0];
}

function addDBMb($data, $id){
	$sql = "INSERT INTO $GLOBALS[TBL_PREFIX]motherboard (iddispenser, macaddress,
		ip, docsize, typecard, localversion, netversion, cenamver, typeconn) 
		VALUES ('". $id ."', '". esql($data['macaddress']) ."', 
		'". esql($data['ip']) ."', '". esql($data['docsize']) ."',
		'". esql($data['tcard']) ."', '". esql($data['localversion']) ."',
		'". esql($data['netversion']) ."', '". esql($data['vcenam']) ."',
		'". esql($data['tconn']) ."')";
	mcq($sql, $db);
	return;
}

function dbSearchDisp($nodisp, $idclien){
	$sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]dispenser WHERE nodispenser = $nodisp 
		AND idcustomer = $idclien";
	return mysql_num_rows(mcq($sql, $db));
}

function delDBDisp($iddisp, $idclien) {
	$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]motherboard WHERE iddispenser = $iddisp";
	mcq($sql, $db);
	$sql = "DELETE FROM $GLOBALS[TBL_PREFIX]dispenser WHERE id = $iddisp AND 
		idcustomer = $idclien";
	mcq($sql, $db);
	return ;
}

function getDbdServers($idcustomer){
	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]esservers as a
                INNER JOIN $GLOBALS[TBL_PREFIX]srvconninfo as b
                WHERE a.id=b.idserver AND 
                idcustomer =
		$idcustomer  AND status='1' ORDER BY priority";
	return mcq($sql, $db);
}
/**
 * Obtiene datos de un servidor
 * @param int $id Id del resitro del servidor
 * @return array Devuelve toda las informacion del servidor en un array asociativo
 */
function getServerbyID($id){
	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]esservers WHERE id = $id
                AND status='1'";
	return mysql_fetch_array(mcq($sql, $db), MYSQL_ASSOC);
}
/**
 * Obtiene datos de conexion de un servidor
 * @param int $id Id del registro del servidor
 * @return array Devuelve toda la informacion de conexion del servidor en un array asociativo
 */
function getSrvConbyID($id){
	$sql = "SELECT * FROM $GLOBALS[TBL_PREFIX]srvconninfo WHERE idserver = $id";
	return mysql_fetch_array(mcq($sql, $db), MYSQL_ASSOC);
}
/**
 * Agrega Servidor a base de datos
 * @param array $params   int [c_id] - Id del cliente<br>
 *                        string [new_os] - OS <br>
 *                        string [new_version] - Version del OS<br>
 *                        string [brandmb] - Marca mother board<br>
 *                        string [modelmb] - Modelo mother board<br>
 *                        string [ramcap] - Tamaño memoria ram<br>
 *                        string [hdtypeconf] - ?<br>
 *                        int [new_prov] - Provedor (0->ictc,1->asic,2->cliente)<br>
 *                        string [new_fch_buy] - Fecha de compra dd/mm/aaaa<br>
 *                        string [new_fch_sel] -Fecha de venta dd/mm/aaaa
 */
function addServer($params){
    $fcha_buy = formatDatetoMysql($_REQUEST[new_fch_buy]);
    $fcha_sel = formatDatetoMysql($_REQUEST[new_fch_sel]);

    $sql = "SELECT count(*)+1 FROM $GLOBALS[TBL_PREFIX]esservers WHERE idcustomer =
		'$_REQUEST[c_id]'";
    $result = mysql_fetch_row(mcq($sql,$db));
    $priority = $result['0'];

    $sql = "INSERT INTO
            $GLOBALS[TBL_PREFIX]esservers(
            idcustomer, priority, os, version, brandmb, modelmb, ramcap,
            hdtypeconf, serverorigin, dateprovpurch, datecusrpurch, status
            )
            VALUES(
            '$_REQUEST[c_id]', '$priority', '$_REQUEST[new_os]', '$_REQUEST[new_version]',
            '$_REQUEST[brandmb]', '$_REQUEST[modelmb]', '$_REQUEST[ramcap]',
            '$_REQUEST[hdtypeconf]', '$_REQUEST[new_prov]', '$fcha_buy',
            '$fcha_sel', '1'
            )
        ";

    mcq($sql,$db);

    $id=mysql_insert_id();

    $sql="
	INSERT INTO $GLOBALS[TBL_PREFIX]srvconninfo(
	idserver,
	host,
	noipaccount,
	mgateway,
	dgateway,
	httpport,
	sshport
	)
	VALUES(
	$id,
	'$_REQUEST[srvhost]',
	'$_REQUEST[noipaccount]',
	'$_REQUEST[mgateway]',
	'$_REQUEST[dgateway]',
	'$_REQUEST[httpport]',
	'$_REQUEST[sshport]'
	)
	";
    mcq($sql,$db);
    return;
}
/**
 * Actualiza datos del servidor
 */
function updateServer($params){
	$primarysrv = '0';
    $fcha_buy = $_REQUEST[new_fch_buy]!='0000-00-00'?formatDatetoMysql($_REQUEST[new_fch_buy]):$_REQUEST[new_fch_buy];
    $fcha_sel = $_REQUEST[new_fch_sel]!='0000-00-00'?formatDatetoMysql($_REQUEST[new_fch_sel]):$_REQUEST[new_fch_sel];
	if ($_REQUEST[primarysrv] == 'on')
		$primarysrv = '1'; 
	if ($_REQUEST[status] == '0')
		$primarysrv = '0';


    $sql = "UPDATE
            $GLOBALS[TBL_PREFIX]esservers
            SET
            os = '$_REQUEST[new_os]',
            version = '$_REQUEST[new_version]',
            brandmb = '$_REQUEST[brandmb]',
            modelmb = '$_REQUEST[modelmb]',
            ramcap = '$_REQUEST[ramcap]',
            hdtypeconf = '$_REQUEST[hdtypeconf]',
            serverorigin = '$_REQUEST[new_prov]',
            dateprovpurch = '$fcha_buy',
            datecusrpurch = '$fcha_sel',
            status = '$_REQUEST[status]',
			primarysrv = '$primarysrv'
            WHERE id = $_REQUEST[btn_edit_serv]
        ";

    mcq($sql,$db);

    $sql = "SELECT id FROM $GLOBALS[TBL_PREFIX]srvconninfo WHERE idserver=$_REQUEST[btn_edit_serv]";
    $result = mcq($sql,$db);
    if (mysql_num_rows($result)>0){
	$sql = "
	UPDATE $GLOBALS[TBL_PREFIX]srvconninfo SET
	host='$_REQUEST[srvhost]',
	noipaccount='$_REQUEST[noipaccount]',
	mgateway='$_REQUEST[mgateway]',
	dgateway='$_REQUEST[dgateway]',
	httpport='$_REQUEST[httpport]',
	sshport='$_REQUEST[sshport]'
	WHERE idserver=$_REQUEST[btn_edit_serv]";
    }
    else{
	$sql="
	INSERT INTO $GLOBALS[TBL_PREFIX]srvconninfo(
	idserver,
	host,
	noipaccount,
	mgateway,
	dgateway,
	httpport,
	sshport
	)
	VALUES(
	$_REQUEST[btn_edit_serv],
	'$_REQUEST[srvhost]',
	'$_REQUEST[noipaccount]',
	'$_REQUEST[mgateway]',
	'$_REQUEST[dgateway]',
	'$_REQUEST[httpport]',
	'$_REQUEST[sshport]'
	)
	";
    }
    mcq($sql,$db);

    return;
}
?>

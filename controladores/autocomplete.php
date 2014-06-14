<?php
/**
 * @copyright ICTC
 */
include("functions.php");
require_once("json.php");

//error_log(var_export($_GET,1));

if ($_GET["customer"]){
    $q = $_GET["q"];
    $queryString = mysql_escape_string($q);
    if(strlen($queryString) >=3) {
        $query = "SELECT id, custname FROM CRMcustomer WHERE custname LIKE '%$queryString%' LIMIT 10";
        if($query) {
            $data = unserialize(base64_decode($_GET["cdb"]));
            $conexion = mysql_connect($data['host'], $data['user'], $data['pass']);
            $link = mysql_db_query($data['database'], $query);
            $content=array();
            while($row=mysql_fetch_array($link)){
                    $content[]=$row;
            }
            if (count($content)>=1){
                foreach($content as $val){
                    $alerts="SELECT CRMloginusers.FULLNAME,CRMentity.CRMcustomer,CRMentity.category,CRMalerts.iduser,CRMalerts.severidad,CRMalerts.status,CRMalerts.eid 
                    FROM CRMalerts  INNER JOIN CRMentity INNER JOIN CRMloginusers ON CRMalerts.eid = CRMentity.eid AND CRMalerts.iduser=CRMloginusers.id 
                    WHERE CRMentity.CRMcustomer='$val[id]' AND CRMalerts.status<>0 ORDER BY CRMalerts.severidad,CRMalerts.createdate ASC";
                     $linkalerts = mysql_db_query($data['database'], $alerts);
                    $rowalert=mysql_fetch_array($linkalerts);
                    $e = last_entities($val['id']);
                    $flag = ( $e == 0 ) ? "0" : $e;
                    //echo $val['custname']."|".$val['id']."|".$flag;
                    echo $val['custname']."|".$val['id']."|".$flag."|".$rowalert['CRMcustomer']."|".$rowalert['severidad']."|".$rowalert['status']."|".$rowalert['eid']."|".$rowalert['category']."|".$rowalert['FULLNAME'];
                    echo "\n";
                }
            }
            else{
                echo "No existe el cliente";
            }
        }
        else {
            echo 'ERROR: Hay un problema con la consulta a base de datos';
        }
    }
    else {
        echo "Ingrese por lo menos 3 caracteres!". $GLOBALS['host'][0]."";
    }
}

if ($_GET['getinfo']=='1'){
    if ($_GET['name']=='info'){
	$result = get_client_info($_GET['custid'],$_GET['cnn']);
	$json = new Services_JSON;
	echo $json->encode($result);
    }
    if ($_GET['name']=='grupo'){
	$result = get_group_info($_GET['custid'],$_GET['cnn']);
	$json = new Services_JSON;
	echo $json->encode($result);
    }

    if ($_GET['name']=='vpn'){
	$result = get_vpn_info($_GET['custid'],$_GET['cnn']);
	$json = new Services_JSON;
	echo $json->encode($result);
    }

    if ($_GET['name']=='iadmin' or $_GET['name']=='cvmax'){
	$result = get_polizas_info($_GET['custid'],$_GET['cnn']);
	$json = new Services_JSON;
	echo $json->encode($result);
    }

    if ($_GET['name']=='pumps'){
	$result = get_pumps_info($_GET['custid'],$_GET['cnn']);
	$json = new Services_JSON;
	echo $json->encode($result);
    }

    if ($_GET['name']=='server'){
	$result = get_server_info($_GET['custid'],$_GET['cnn']);
	$json = new Services_JSON;
	echo $json->encode($result);
    }

    if ($_GET['name']=='proces'){
	$result = get_procesos($_GET['userid'],$_GET['eid'],$_GET['cnn']);
	$json = new Services_JSON;
	echo $json->encode($result);
    }
}

if ($_GET['getpanel']=='1'){
    $result = show_panel($_GET['custid'],$_GET['eid'],$_GET['cnn']);
    $json = new Services_JSON;
    echo $json->encode($result);
}

if ($_GET['parent']=='1'){
    $result = get_parent($_GET['eid'],$_GET['cnn']);
    $json = new Services_JSON;
    echo $json->encode($result);
}

if ($_GET['update_proces']=='1'){
    $result = update_idproceso($_GET['idp'],$_GET['eid'],$_GET['cnn']);
}


if(isset($_GET['tags']) && $_GET['tags']==1){
    ob_start();    
    if($_GET['eid']!='_new_'){
        $data = getTagsByEntity($_GET['eid'],$_GET['cnn']);
        __tagit($data);
    }
    $html = ob_get_clean();
    $json = new Services_JSON();
    echo $json->encode($html);
}
if(isset($_GET['tags']) && $_GET['tags']==2){
    ob_start();
    $data = getTags($_GET['cnn']);
    $json = new Services_JSON();
    echo $json->encode($data);
}
if(isset($_GET['tags']) && $_GET['tags']==3){
    ob_start();    
    if($_GET['id']){
        $data = getTagsById($_GET['id'],$_GET['cnn']);
        __tagit($data);
    }
    $html = ob_get_clean();
    $json = new Services_JSON();
    echo $json->encode($html);
}
if(isset($_GET['tags']) && $_GET['tags']==4){
    ob_start();
    $id = insertNewTag($_GET['name'], $_GET['cnn']);
    $data[0][0]['id']=$id;
    $data[0][0]['solution']='http://www.google.com';
    $data[0][0]['parent']='0';
    $data[0][0]['flag']='1';
    $data[0][0]['name']=$_GET['name'];
    __tagit($data);
    $html = ob_get_clean();
    $json = new Services_JSON();
    echo $json->encode($html);
}
if(isset($_GET['tags']) && $_GET['tags']==5){
    //obtenemos info del tag
    $data = getTag($_GET['id'], $_GET['db']);
    //obtenemos info del parent
    $tmp = getTag($data['parent'], $_GET['db']);
    $data['paren_name']=$tmp['name'];
    
    $json = new Services_JSON();
    echo $json->encode($data);
}
if(isset($_GET['tags']) && $_GET['tags']==6){
    updateTag($_GET['data'], $_GET['db']);
}
if(isset($_GET['tags']) && $_GET['tags']==7){    
    $tmp = getEntitysByTag($_GET['id'], $_GET['db']);
    $data = __aref($tmp);
    $json = new Services_JSON();
    echo $json->encode($data);
}
if(isset($_GET['tags']) && $_GET['tags']==8){
    $db = $_GET['db'];
    $tmp = getTreeTags($db);
    
    $data = __consTree($tmp);
    /*
    $array1[]=array(
            "data"=>"Parent2",
            "state"=> "closed",
            "children"=>$array2,
            "attr" => array( "id"=>"t3", "value"=>"3")
            );
    */
    $json = new Services_JSON();
    echo $json->encode($data);
}
if(isset($_GET['hw']) && $_GET['hw']==1){
    $html = regReportHw($_GET['params'], $_GET['db']);
    $json = new Services_JSON();
    echo $json->encode($html);
}
if(isset($_GET['stags']) && $_GET['stags']==1){
    $data = getStatisticTags($_GET['db'], $_GET['params']);
    $json = new Services_JSON();
    echo $json->encode($data);
}
if(isset($_GET['ntags']) && $_GET['ntags']==1){
    $data = getTotalTags($_GET['db']);
    $json = new Services_JSON();
    echo $json->encode($data);
}

function __tagit($data){
    foreach ($data as $array) {
        ?>
        <div style='padding-top: 5px; padding-left: 10px; float: left;'>
        <?php
        foreach ($array as $reg) {
            $help ='';
            $href_target ='';
            if($reg['solution']!=''){
                $help = 'lamphelp pointer';
                $href_target ='_blank';
            }
            $reg['solution'] = $reg['solution']==''? "#": $reg['solution'];
        ?>
        <input name="tags[]" type="hidden" value="<?php echo $reg['id']; ?>"/>
        <a href="<?php echo $reg["solution"]; ?>" target="<?php echo $href_target; ?>">
        <input parent="<?php echo $reg['parent']; ?>" uniqid="<?php echo $reg['id']; ?>"
               flag="<?php echo $reg['flag']; ?>"
               class="buttons3 <?php echo $help; ?>"
               type="button"
               value="<?php echo $reg['name']; ?>"/>
        <?php
        }
        ?>
        </a>
        <b name='closeTag' class='pointer' style="color: red;">&nbsp;X</b>

        </div>
        <?php
        $i++;
    }
}
function __aref($data){
    if(count($data)>0){
        foreach ($data as $value) {
            $html .= "&nbsp;&nbsp;
                      <a href='/crm/edit.php?e=$value[eid]' target='_blank'>
                        $value[eid]
                      </a>,";
        }
        $html = substr($html, 0, -1);
    }else{
        $html='No existen registros';
    }
    return $html;
}
function __consTree($tmp){
    $i=0;
    foreach ($tmp as $value) {
        $_tmp = array(
                        "data"=>"$value[name]",
                        "attr"=>array("id"=>"tag_$value[id]", "value"=>"$value[id]"
                         )
                    );
        if($value['flag']==0 && count($value['children'])>0){
            $_tmp["state"]="closed";
        }
        if(count($value['children'])>0){
            //echo count($value['children']);
            $_tmp["children"]=$value['children'];
            $j=0;
            foreach ($_tmp['children'] as $value2) {
                //print_array($value2);
                $_tmp['children'][$j] = array(
                        "data"=>"$value2[name]",
                        "attr"=>array("id"=>"tag_$value2[id]", "value"=>"$value2[id]"
                         )
                    );
                if($value2['flag']==0 && count($value2['children'])>0){
                    $_tmp["state"][$j]="closed";
                }
                $_tmp['children'][$j]['children'] = __consTree($value2['children'], $arr);
                $j++;
            }
        }
        $data[$i] = $_tmp;
        $i++;
    }
    //error_log(var_export($data,1));
    return $data;
}
return ;
?>
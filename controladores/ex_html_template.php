<?
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
$nonavbar=1;
require_once("header.inc.php");

if ($_REQUEST['form']) {
	$template = ParseFormTemplate($_REQUEST['data'],$_REQUEST['x'],'ex_html_template.php',false);
} elseif ($_REQUEST['cform']) {
	$template = ParseCustomerFormTemplate($_REQUEST['data'],$_REQUEST['x'],'ex_html_template.php',false);
} else {

	$template = GetFileContent($_REQUEST['data']);

	$template = ParseTemplateEntity($template, $_REQUEST['x']);
	$template = ParseTemplateGeneric($template);
	$template = ParseTemplateCustomer($template, $_REQUEST['x']);
}

$template = ParseTemplateCleanUp($template);


print $template;

exit;

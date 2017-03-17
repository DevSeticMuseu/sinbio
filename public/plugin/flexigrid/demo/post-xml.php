<?php
require_once("../../../constantes.php");
require_once(PATH."classe/class.FachadaSeguranca.php");

$oFachadaSeguranca = new FachadaSeguranca();

$vWhere[] = "ativo = 1";
$voUsuario = $oFachadaSeguranca->recuperaTodosUsuario(SPDB,$vWhere);
unset($vWhere);
$nTotal = $oFachadaSeguranca->recuperaTotalUsuario(SPDB);

printvar($nTotal, $voUsuario);
/*
 * page	1
qtype	nm_usuario
query	
rp	15
sortname	id
sortorder	asc
 */


/*
header("Content-type: text/xml");
$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$xml .= "<rows>";
$xml .= "<page>".$_POST['page']."</page>";
$xml .= "<total>$total</total>";
foreach($rows AS $row){
	$xml .= "<row id='".$row['id']."'>";
	$xml .= "<cell><![CDATA[".$row['id']."]]></cell>";
	$xml .= "<cell><![CDATA[".utf8_encode($row['nm_usuario'])."]]></cell>";
	//$xml .= "<cell><![CDATA[".print_r($_POST,true)."]]></cell>";
	$xml .= "<cell><![CDATA[".utf8_encode($row['login'])."]]></cell>";
	$xml .= "</row>";
}

$xml .= "</rows>";
echo $xml;
*/
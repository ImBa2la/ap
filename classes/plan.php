<?
class plan extends module{
function run(){
	global $_out,$_params;
	$xml = $this->getSection()->getXML();
	$res = $xml->query('./*',$this->getRootElement());
	$_out->addSectionContent($res);
}
}
?>
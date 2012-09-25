<?
class main extends module{
function run(){
	global $_events;
	$_events->addListener('SectionReady',$this);
	$_events->addListener('PageReady',$this);
}
function onSectionReady(){
	global $_sec,$_params;
	switch($_sec->getId()){
		case 'sitemap':
			vacancy::structInit();	
			break;
	}
	if($_params->exists('print')){
		$xml = $_sec->getXML();
		if($e = $xml->query('/section/template')->item(0))
			$e->setAttribute('id','print');
		else
			$xml->de()->appendChild($xml->createElement('template',array('id' => 'print')));
		
	}
}
function onPageReady(){
	global $_sec,$_out;
	
	if(
		$_sec->getTemplate()->getId() == 'default.xsl' && (
			$tempList = $_sec->getTemplateList() && 
			!$tempList->getNum()
		) || !$tempList
	){
		$_sec->getTemplate()->addTemplate('def.xsl');
	}
}
}
?>
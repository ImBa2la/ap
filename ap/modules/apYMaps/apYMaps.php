<?
class apYMaps extends module{
function __construct(DOMElement $e,structure $struct){
	parent::__construct($e,$struct);
}
function run(){}
function install(){
	if($sec = ap::getClientSection($this->getSection()->getId())){
		$modules = $sec->getModules();
		if(!$modules->getById($this->getId())){
			
			$modules->add('ymaps',$this->getTitle(),$this->getId());
			$this->getRootElement()->setAttribute('readonly','readonly');
			$modules->getXML()->save();
		}
		return true;
	}
}
function uninstall(){
	if($sec = ap::getClientSection($this->getSection()->getId())){
		$modules = $sec->getModules();
		if($modules->remove($this->getId()))
			$modules->getXML()->save();
		return true;
	}
}
function settings($action){
	global $_out;
	$xml = new xml(PATH_MODULE.__CLASS__.'/data.xml');
	if($e = $xml->getElementById('ymaps_form_settings')){
		$form = new form($e);
		$form->replaceURI(array(
			'MD'=>$this->getId(),
			'ID'=>$this->getSection()->getId()
		));
		//Получаем список шаблонов и добавляем опции к селекту
		$tpls = apTemplateManager::getAllTemplates();
		$select = $form->getField('map_tpl');
		foreach($tpls as $item){
			$select->addOption($item,$item);
		}
		switch($action){
			case 'update':
			case 'apply_update':
				$form->save($_REQUEST);
				return;
		}
		$form->load();
		$_out->addSectionContent($form->getRootElement());
	}else throw new Exception('Form not found',EXCEPTION_XML);
}
}
?>
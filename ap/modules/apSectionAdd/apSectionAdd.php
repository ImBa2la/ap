<?
class apSectionAdd extends module{
function getMessage(){
	switch(param('mess')){
		case 'add':
		case 'apply':
			return 'Раздел успешно добавлен';
		case 'fail':
			return 'Ошибка добавления раздела';
	}
}
function getQueryPath($parent){
	if($parent!='apStruct'){
		$query = '';
		$sec = ap::getClientSection($parent);
		do{
			$query = '/sec[@id="'.htmlspecialchars($sec->getId()).'"]'.$query;
		}while($sec = $sec->getParent());
		return $query;
	}
}
function redirect($action,$id = null){
	$param = array();
	if(trim($action)){
		$param['mess'] = $action;
	}
	switch($action){
		case 'apply':
			if($id){
				$param['id'] = '_s_'.$id;
			}
			break;
	}
	header('Location: '.ap::getUrl($param));
	die;
}
static function getForm(){
	$xml = new xml(PATH_MODULE.__CLASS__.'/form/add.xml');
	$xml = ap::translate($xml,array(
		'//@title',
		'//field[@label]/@label',
		'//button[@value]/@value',
		'//rowlist/col[@header]/@header',
	),PATH_MODULE.__CLASS__.'/translate.php');
	return new form($xml->de());
}
function run(){
	global $_out,$_struct,$_sec;
	if(ap::isCurrentModule($this)){
		ap::addMessage($this->getMessage());
		
		$action = param('action');
		$nowId = $this->getSection()->getId();
		$form = $this->getForm();
		
		//если это корневой раздел то показываем форму по добавлению нового раздела
		switch($action){
			case 'ajax':
				if($parent = param('parent')){
					if(($sec_parent = ap::getClientSection($parent)) || ($parent=='apStruct')){
						header('Content-type: text/xml');
						$xml = new xml(null,'seclist',false);
						$res = ap::getClientStructure()->query($parent == 'apStruct' ? '/structure/sec' : '//sec[@id="'.$sec_parent->getId().'"]/sec');
						foreach($res as $sec){
							$xml->de()->appendChild($xml->createElement('sec',array(
								'id'	=>	$sec->getAttribute('id'),
								'title'	=>	$sec->getAttribute('title'),
							)));
						}
						echo $xml;
					}
					die;
				}
				if($translate = param('translate')){
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL,'http://translate.google.ru/translate_a/t?ie=UTF-8&client=x&text='.urlencode($translate).'&sl=en&tl=ru');
					curl_setopt($curl, CURLOPT_TIMEOUT, 3);
					curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:15.0) Gecko/20120822 Firefox/15.0 SeaMonkey/2.12');
					if(($res = curl_exec($curl))){
						echo $res;
					}else{
						echo json_encode(array('code'=>5,'error'=>1));
					}
					curl_close($curl);
					die;
				}
				if($issetid = param('isset')){
					echo ap::getClientSection($issetid) ? '0' : '1';
					die;
				}
				break;
			case 'add':
			case 'apply':
				if(is_array($sec = param('sec'))
					&& $sec['id']
					&& (preg_match('#^[a-z]{1}[a-z0-9_-]{2,50}$#',$sec['id']))
					&& !ap::getClientSection($sec['id'])
				){
					$form->replaceURI(array('PATH' => $this->getQueryPath(param('parent'))));
					$form->save($_REQUEST);
					$_struct->addSection($sec['id'],$sec['title']);
					/* позиция */
					if(($id_pos = param('position'))
						&& ($struct = ap::getClientStructure(false))
					){
						if(($sec_new = $struct->getSection($sec['id']))
							&& ($sec_pos = $struct->getSection($id_pos))
						){
							$sec_pos->getElement()->parentNode->insertBefore($sec_new->getElement(),$sec_pos->getElement());
							$struct->save();
						}
					}
					/* установить выбранный шаблон раздела */
					if($tpl_id = param('template'))
						apSectionTemplate::applyTemplate(ap::id($sec['id']),$tpl_id);
					$this->redirect($action,$sec["id"]);
				}else $this->redirect('fail');
				break;
			default:
				$form->replaceURI(array('PATH' => ''));
				//список разделов
				if($ff = $form->getField('parent')){
					$s = ap::getClientStructure()->getSection(ap::id($nowId));
					$ff->addOption('apStruct','Корень');
					$ar = array();
					if($s) $p = $s->getParent();
					$this->seclist(ap::getClientStructure()->de(),$ff,$ar);
					$ff->setValue($p ? $p->getId() : 'apStruct');
				}
				
				// список разделов шаблонов
				if($ff = $form->getField('template')){
					$tl = apSectionTemplate::getPackages();
					if($tl->getNum()){
						foreach($tl as $e)
							$ff->addOption($e->getAttribute('id'),$e->getAttribute('title'));
					}else $ff->remove();
				}
				$_sec->getTemplate()->addTemplate('../../modules/'.__CLASS__.'/template/sectionadd.xsl');
				$_out->elementIncludeTo($form->getRootElement(),'/page/section');
				break;
		}
	}
}
static function addSection($id,$title,$section = null){
	global $_struct;
	if($s = ap::getClientStructure()->addSection($id,$title,$section)){
		if($section instanceof section) $section = $_struct->getElementById($section->getId());
		$_struct->addSection($id,$title,$section);
	}
	ap::getClientStructure()->save();
	return $s;
}
static function seclist($e,&$ff,&$exclude){ //список разделов для селекта
	$xml = new xml($e);
	$shift = str_repeat('- ',$xml->evaluate('count(ancestor-or-self::sec)',$e));
	$res = $xml->query('sec',$e);
	foreach($res as $sec){
		if(!in_array($sec->getAttribute('id'),$exclude)){
			$ff->addOption($sec->getAttribute('id'),$shift.$sec->getAttribute('title'));
			apSectionAdd::seclist($sec,$ff,$exclude);
		}
	}
}
}
?>
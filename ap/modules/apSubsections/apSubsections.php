<?
class apSubsections extends module{
private $rl;
function ajax($params,$xml,$mysql){
	if($parent = $params['parent']){
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
	if($translate = $params['translate']){
		#$res = @file_get_contents(urldecode('http://translate.google.ru/translate_a/t?ie=UTF-8&client=x&text='.$translate.'&sl=ru&tl=en'));
		# voice google $res = file_get_contents('http://translate.google.ru/translate_tts?ie=UTF-8&q=%D0%A1%D0%BA%D0%BE%D1%80%D0%B5%D0%B5%20%D0%B2%D1%81%D0%B5%D0%B3%D0%BE%20%D0%BD%D1%83%D0%B6%D0%BD%D0%BE%20%D0%BF%D0%B5%D1%80%D0%B5%D0%B4%D0%B0%D0%B2%D0%B0%D1%82%D1%8C%20%D0%B2%D1%81%D0%B5%20%D1%8D%D1%82%D0%B8%20%D0%BF%D0%B0%D1%80%D0%B0%D0%BC%D0%B5%D1%82%D1%80%D1%8B&tl=ru&total=1&idx=0&textlen=47');
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
	if($issetid = $params['isset']){
		echo ap::getClientSection($issetid) ? '0' : '1';
		die;
	}
}
function getRow(){
	return param('row');
}
function setRow($v){
	setParam('row',$v);
}
function getMessSessionName(){
	return $this->getSection()->getId().'_'.$this->getId();
}
function setMessage($mess){
	if($mess){
		if(!session_id() && !headers_sent()) session_start();
		$_SESSION['apMess'][$this->getMessSessionName()] = $mess;
	}
}
function getMessage(){
	if(!session_id() && !headers_sent()) session_start();
	$mess = null;
	switch($_SESSION['apMess'][$this->getMessSessionName()]){
		case 'delete_ok':
			$mess = 'Раздел удален'; break;
		case 'delete_fail':
			$mess = 'Ошибка, запись не удалена'; break;
		case 'update_ok':
			$mess = 'Информация успешно обновлена'; break;
		case 'update_fail':
			$mess = 'Ошибка обновления информации'; break;
		case 'add_ok':
			$mess = 'Раздел добавлен'; break;
		case 'add_fail':
			$mess = 'При добавлении раздела произошла ошибка'; break;
	}
	$_SESSION['apMess'] = array();
	return $mess;
}
function redirect($mess = null){
	$param = array();
	$action = param('action');
	if($action && ($row = $this->getRow())){
		switch($action){
			case 'apply_update':
			case 'apply_add':
				$param['action'] = 'edit';
				$param['row'] = $row;
		}
	}
	if($page = param('page')) $param['page'] = $page;
	$this->setMessage($mess);
	header('Location: '.ap::getUrl($param));
	die;
}
function getForm($action){
	$xml = $this->getSection()->getXML();
	$form_element = null;
	switch($action){
		case 'update':
		case 'apply_update':
		case 'edit':
			$form_element = $xml->getElementById('form_edit');
			break;
		case 'new':
		case 'add':
		case 'apply_add':
		default:
			$form_element = $xml->getElementById('form_add');
			break;
	}
	if($form_element){
		$form = new form($form_element);
		return $form;
	}
}
function getList(){
	if($list_element = $this->query('./rowlist')->item(0)){
		$res = $this->getSection()->query('sec');
		$rl = new rowlist($list_element,$res->length,param('page'));
		$s = $rl->getStartIndex();
		$f = $rl->getFinishIndex();

		foreach($res as $i => $sec){
			if($i<$s) continue;
			elseif($i>$f) break;
			$rl->addRow($sec->getAttribute('id'),array(
				'sort'=>$i+1,
				'title'=>$sec->getAttribute('title'),
			));
		}
		$rl->setFormAction(preg_replace('/&?mess=[\w_]*/','',$_SERVER['REQUEST_URI']));
		return $rl;
	}
}
function getSecPath(){
	$sec = ap::getClientSection($this->getSection()->getId());
	$out = '';
	while($sec){
		$out = "sec[@id='".$sec->getId()."']/".$out;
		$sec = $sec->getParent();
	}
	return $out;
}
function onAdd(){
	if(($id = param('alias'))
		&& (preg_match('#^[a-z]{1}[a-z0-9_-]{2,50}$#',$id))
		&& !ap::getClientSection($id)
		&& ($title = param('title'))
		&& ($tpl = $this->evaluate('string(template/@id)'))
		&& ($sec = apSectionAdd::addSection($id,$title,ap::getClientSection($this->getSection()->getId())))
	){
		apSectionTemplate::applyTemplate($id,$tpl);
		param('row',$sec->getId());
		return true;
	}
	return false;
}
function run(){
	global $_out;
	if(ap::isCurrentModule($this)){
		ap::addMessage($this->getMessage());
		$action = param('action');
		$form = $this->getForm($action);
		$row = $this->getRow();
		switch($action){
			case 'move':
				if($row
					&& ($pos = param('pos'))
					&& ($sec = ap::getClientSection($this->getSection()->getId()))
					&& ($tl = new taglist($sec->getElement(),'sec'))
					&& ($tl->move($tl->getById($row),$pos))
				){
					$tl->getXML()->save();
					$this->redirect('move_ok');
				}else $this->redirect('move_fail');
				break;
			case 'delete':
				if($row){
					if(!is_array($row)) $row = array($row);
					foreach($row as $id) if($id) apSectionEdit::removeSection($id);
					$this->redirect('delete_ok');
				}else $this->redirect('delete_fail');
				break;
			case 'update':
			case 'apply_update':
				if($row){
					$form->replaceURI(array(
						'ID' => $row,
						'PATH' => $this->getSecPath()
					));
					$form->save($_REQUEST,$row);
					$this->redirect('update_ok');
				}else $this->redirect('update_fail');
				break;
			case 'add':
			case 'apply_add':
				if($this->onAdd($action))
					$this->redirect('add_ok');
				else $this->redirect('add_fail');
				break;
			case 'edit':
				if($row){
					$form->replaceURI(array(
						'ID' => $row,
						'PATH' => $this->getSecPath()
					));
					$form->load($row);
				}
				$_out->addSectionContent($form->getRootElement());
				break;
			case 'new':
				if($ff = $form->getField('date'))
					$ff->setValue(date('d.m.Y'));
				if($ff = $form->getField('alias'))
					$ff->setValue($form->getRootElement()->getAttribute('pref'));
				$_out->addSectionContent($form->getRootElement());
				$this->getSection()->getTemplate()->addTemplate('../../modules/'.__CLASS__.'/tpl.xsl');
				break;
			default:
				if($rl = $this->getList()){
					$_out->addSectionContent($rl->getRootElement());
				}
		}
	}
}
function install(){
	$xml_data = new xml(PATH_MODULE.__CLASS__.'/data.xml');
	$xml_sec = $this->getSection()->getXML();
	$ar = array('form_edit','form_add','list');
	foreach($ar as $id){
		$e = $xml_data->query('//*[@id="'.$id.'"]')->item(0);
		if($e && !$xml_sec->evaluate('count(./*[@id="'.$id.'"])',$this->getRootElement()))
			$xml_sec->elementIncludeTo($e,$this->getRootElement());
	}
	$xml_sec->save();
	return true;
}
function settings($action){
	global $_out;
	$xml = new xml(PATH_MODULE.__CLASS__.'/data.xml');
	if($e = $xml->getElementById('settings')){
		$form = new form($e);
		if(($ff = $form->getField('section_template'))
			&& ($tl = apSectionTemplate::getPackages())
		){
			$val = $ff->getValue();
			foreach($tl as $e){
				$ff->addOption($e->getAttribute('id'),$e->getAttribute('title'));
				if($val==$e->getAttribute('id')) $ff->setValue($val);
			}
		}
		$form->replaceURI(array(
			'MD'=>$this->getId(),
			'ID'=>$this->getSection()->getId()
		));
		switch($action){
			case 'update':
			case 'apply_update':
				$form->save($_REQUEST);
				return;
		}
		$form->load();
		$_out->addSectionContent($form->getRootElement());
	}
}
}
?>
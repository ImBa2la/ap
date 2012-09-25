<?
class apXmlEditor extends module{
private $forms;
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
		case 'copy_ok':
			$mess = 'Р—Р°РїРёСЃСЊ СѓСЃРїРµС€РЅРѕ СЃРєРѕРїРёСЂРѕРІР°РЅР°'; break;
		case 'copy_fail':
			$mess = 'РџСЂРё РєРѕРїРёСЂРѕРІР°РЅРёРё Р·Р°РїРёСЃРё РїСЂРѕРёР·РѕС€Р»Р° РѕС€РёР±РєР°'; break;
		case 'delete_ok':
			$mess = 'Р—Р°РїРёСЃСЊ СѓРґР°Р»РµРЅР°'; break;
		case 'delete_fail':
			$mess = 'РћС€РёР±РєР°, Р·Р°РїРёСЃСЊ РЅРµ СѓРґР°Р»РµРЅР°'; break;
		case 'update_ok':
			$mess = 'Р—Р°РїРёСЃСЊ СѓСЃРїРµС€РЅРѕ РѕР±РЅРѕРІР»РµРЅР°'; break;
		case 'edit_fail':
			$mess = 'РЈРєР°Р·Р°РЅРЅР°СЏ Р·Р°РїРёСЃСЊ РЅРµ РјРѕР¶РµС‚ Р±С‹С‚СЊ РѕС‚СЂРµРґР°РєС‚РёСЂРѕРІР°РЅР° РёР»Рё РµРµ РЅРµ СЃСѓС‰РµСЃС‚РІСѓРµС‚'; break;
		case 'update_fail':
			$mess = 'РћС€РёР±РєР° РѕР±РЅРѕРІР»РµРЅРёСЏ Р·Р°РїРёСЃРё'; break;
		case 'add_ok':
			$mess = 'Р—Р°РїРёСЃСЊ РґРѕР±Р°РІР»РµРЅР°'; break;
		case 'add_fail':
			$mess = 'РџСЂРё РґРѕР±Р°РІР»РµРЅРёРё Р·Р°РїРёСЃРё РїСЂРѕРёР·РѕС€Р»Р° РѕС€РёР±РєР°'; break;
		case 'move_ok':
			$mess = 'РџРѕР·РёС†РёСЏ Р·Р°РїРёСЃРё РёР·РјРµРЅРµРЅР°'; break;
		case 'move_fail':
			$mess = 'РџСЂРё РёР·РјРµРЅРµРЅРёРё РїРѕР·РёС†РёРё Р·Р°РїРёСЃРё РїСЂРѕРёР·РѕС€Р»Р° РѕС€РёР±РєР°'; break;
	}
	$_SESSION['apMess'] = array();
	return $mess;
}

function redirect($mess = null){
	$param = array();
	$action = param('action');
	if($action && ($row = $this->getRow())){
		switch($action){
			case 'apply_update_pathForm':
			case 'apply_add_pathForm':
				$param['action'] = 'edit_pathForm';
				$param['row'] = $row;
				break;
			case 'apply_update':
			case 'apply_add':
				$param['action'] = 'edit';
				$param['row'] = $row;
				break;
			case 'edit_pathForm':
				$param['action'] = 'new_pathForm';
				break;
			case 'edit_form':
				$param['action'] = 'new_form';
		}
	}
	if($page = param('page')) $param['page'] = $page;
	$this->setMessage($mess);
	header('Location: '.ap::getUrl($param));
	die;
}
function run(){
	global $_out,$_sec;
	if(ap::isCurrentModule($this)):
		ap::addMessage($this->getMessage());
		switch($this->getid()):
			case 'forms':
				$this->forms = new xml('xml/forms.xml');
				$action	= param('action');
				$type	= param('type')?param('type'):'form';
				$row	= param('row');
				$form	= $this->getForm($action);
				$tl		= $this->getTagList($type);

				switch($action){
					case 'delete':
						if(!is_array($row)) $row = array($row);
						$counter = 0;
						foreach($row as $id){
							if($id
								&& ($e = $tl->getById($id))
							){
								$counter++;
								$tl->remove($e);
							}
						}
						if($counter && $counter==count($row)){
							$tl->getXML()->save();
							$this->redirect('delete_ok');
						}else $this->redirect('delete_fail');
						break;
					/*case 'update':
					case 'apply_update':
						if($row){
							$_REQUEST['fId']=$row;
							$form->replaceURI(array('FID'=>$row));
							$form->save($_REQUEST);
							$this->redirect('update_ok');
						}else $this->redirect('update_fail');
						break;
					case 'add':
					case 'apply_add':
						if(($title = param('fTitle')) 
							&& ($id = param('fId'))
							&& (!$tl->getById($id))
						){
							$value = array(
								'id'=>$id,
								'date'=>date('d.m.Y H:i'),
								'stamp'=>time()
							);
							if(!param('method')) $value['method'] = 'post';
							$tl->append($value);
							$tl->getXML()->save();
							$form->replaceURI(array('FID'=>$id));
							$form->load();
							$form->save($_REQUEST);
							$this->redirect('add_ok');
						}else $this->redirect('add_fail');
						break;*/
					case 'clone':
						if($_REQUEST['fId'] 
								&& !$tl->getById($_REQUEST['fId'])
								&& $row 
								&& ($node = $tl->getById($row)) 
								&& ($copy = $node->cloneNode())
							){
							$copy->setAttribute('id',$_REQUEST['fId']);
							$tl->append(null,null,$copy);
							$tl->getXML()->save();
							$this->redirect('copy_ok');
						}else $this->redirect('copy_fail');
						break;
					case 'edit_form':
						if($row	&& $tl->getById($row)){
							$form->replaceURI(array(
								'PATH'	=> '/ap/xml/forms.xml',
								'FID'=>$row,
								'FORM'=>$tl->getById($row)->getAttribute('title'))
							);
						}else $this->redirect('edit_form_fail');
						/*if($row 
								&& ($rl = $this->getList('attr',$row))
								&& $tl->getById($row)
						){
								$form->replaceURI(array('FID'=>$row,'FORM'=>$tl->getById($row)->getAttribute('title')));
								$_out->addSectionContent($rl->getRootElement());
						}
						else $this->redirect('edit_form_fail');*/
					case 'new_form':
						$_out->elementIncludeTo($form->getRootElement(),'/page/section');
						break;
					default:
						$form = new form($this->forms->getElementById($_sec->getId().'_filter'));
						//$form->load(); //РЅСѓР¶РЅРѕ РїРёСЃР°С‚СЊ СЃС…РµРјСѓ РґР»СЏ urlSchema, С‡С‚Рѕ Р±С‹ РґР°РЅРЅС‹Рµ Р·Р°Р±РёСЂР°Р»РёСЃСЊ РїРѕ РЈР Р› РёР»Рё С„РёР»СЊС‚СЂРѕРІР°С‚СЊ Р°СЏРєСЃРѕРј
						$_out->addSectionContent($form->getRootElement());
						if($rl = $this->getList($type)){
							$_out->addSectionContent($rl->getRootElement());
						}
				}
				/*if($e = $forms->query('/forms/rowlist[@id="'.$_sec->getId().'_'.$q.'"]')->item(0)){
					echo $e->getAttribute('id');
					//$form = new form($e);
					//if(param('action')=='save')$form->save($_REQUEST);
					//$form->load();
					//$_out->elementIncludeTo($form->getRootElement(),'/page/section');
				}*/
				break;
			case 'forms_settings': // functions for interface settings module
				$form	= $this->getFormExt(param('action'));
				$tlPaths= $this->getTagListExt('paths');
				$tlTags	= $this->getTagListExt('tags');
				$row	= param('row');
				
				switch(param('action')){
					
					//action for paths
					case 'delete_pathForm':
						if(!is_array($row)) $row = array($row);
						$counter = 0;
						foreach($row as $id){
							if($id && $tlPaths
								&& ($e = $tlPaths->getById($id))
							){
								$counter++;
								$tlPaths->remove($e);
							}
						}
						if($counter && $counter==count($row)){
							$tlPaths->getXML()->save();
							$this->redirect('delete_ok');
						}else $this->redirect('delete_fail');
						break;
					case 'update_pathForm':
					case 'apply_update_pathForm':
						if($row){
							$form->replaceURI(array('OID'=>$row));
							$form->save($_REQUEST);
							$this->redirect('update_ok');
						}else $this->redirect('update_fail');
						break;
					case 'add_pathForm':
					case 'apply_add_pathForm':
						if($tlPaths){
							$value = array(
								'id'=>$tlPaths->generateId('o'),
								'title'=>param('title'),
								'path'=>param('path')
							);
							$tlPaths->append($value);
							$tlPaths->getXML()->save();
							$this->redirect('add_ok');
						}else $this->redirect('add_fail');
						break;
					case 'edit_pathForm':
						if($row	&& $tlPaths->getById($row)){
							$form->replaceURI(array('OID'=>$row));
							$form->load();
						}else $this->redirect('edit_form_fail');
					case 'new_pathForm':
						$_out->elementIncludeTo($form->getRootElement(),'/page/section');
						break;
					default://default show row lists paths and tags
						if($rl = $this->getListExt('paths'))
							$_out->addSectionContent($rl->getRootElement());
						if($rl = $this->getListExt('tags'))
							$_out->addSectionContent($rl->getRootElement());
						break;
				}
				break;
		endswitch;
	endif;	
}
function getTagListExt($type){
	$xml = $this->getSection()->getXML();
	switch($this->getId()):
			case 'forms_settings':
				switch($type){
					case 'paths':
						$nodeName = 'option';
						$e = $xml->dd()->getElementsByTagName('paths')->item(0);
						break;
					case 'nodes':
						$nodeName = 'option';
						$e = $xml->dd()->getElementsByTagName('nodes')->item(0);
						break;
				}
				break;
			case 'forms':
				break;
	endswitch;
	if($e instanceof DOMElement)
		return new taglist($e,$nodeName);
	else return false;
}
function getFormExt($action){
	global $_sec;
	$fe = null;	
	$xml = $this->getSection()->getXML();
	
	switch($this->getId()):
			case 'forms_settings':
				switch($action){
					case 'update_pathForm':
					case 'apply_update_pathForm':
					case 'edit_pathForm':
						$fe = $xml->getElementById('apXmlEditor_settings_path_edit');
						break;
					case 'new_pathForm':
					case 'add_pathForm':
					case 'apply_add_pathForm':
					default:
						$fe = $xml->getElementById('apXmlEditor_settings_path_add');
						break;
				}
				break;
			case 'forms':
				break;
	endswitch;
	if($fe){
		$form = new form($fe);		
		return $form;
	}
}
function getListExt($action){
	$xml = $this->getSection()->getXML();
	switch($this->getId()):
			case 'forms_settings':
				if($e = $xml->getElementById("apXmlEditor_settings_path")){
					$q = './option';
					switch($action){
						case 'paths':
							$from = $xml->dd()->getElementsByTagName('paths')->item(0);
							break;
						case 'tags':
							$from = $xml->dd()->getElementsByTagName('tags')->item(0);
							break;
					}

					$list = $xml->query($q,$from);
					$rl = new rowlist($e,$list->length,param('page'));
					$s = $rl->getStartIndex();
					$f = $rl->getFinishIndex();
					foreach($list as $i => $row){
						if($i<$s) continue;
						elseif($i>$f) break;
						$rl->addRow($row->getAttribute('id'),array(
								'sort'=>$i+1,
								'title'=>$row->getAttribute('title'),
								'path'=>$row->getAttribute('path')
							));
					}
					$rl->setFormAction(preg_replace('/&?mess=[\w_]*/','',$_SERVER['REQUEST_URI']));
					return $rl;
				}
				
			case 'forms':
				break;
	endswitch;
}
function getTagList($type){
	if($e = $this->forms->de())
		return new taglist($e,$type);
	else throw new Exception('Not found elements type of "'.$type.'"');
}
function getList($type,$id = false){
	global $_sec;
	if($e = $this->forms->getElementById($_sec->getId().'_list_'.(($type=='form' || $type=='rowlist')?'forms':'fields'))){
		switch($type){
			case 'form': 
				$q = './/form';
				$from = $this->forms->de();
				$e->setAttribute('title','РџРµСЂРµС‡РµРЅСЊ С„РѕСЂРј'); break;
			case 'rowlist': 
				$q = './/rowlist';
				$from = $this->forms->de();
				$e->setAttribute('title','РџРµСЂРµС‡РµРЅСЊ СЃРїРёСЃРєРѕРІ'); break;
			case 'field': 
				if(!$id) return false;
				$q = './field';
				$from = $this->forms->getElementById($id);
				$e->setAttribute('title','РџРµСЂРµС‡РµРЅСЊ РїРѕР»РµР№'); break;
			case 'attr': 
				if(!$id) return false;
				$q = './@*';
				$from = $this->forms->getElementById($id);
				$e->setAttribute('title','РџРµСЂРµС‡РµРЅСЊ Р°С‚С‚СЂРёР±СѓС‚РѕРІ'); break;
			default: 
				return false;
		}
		
		$list = $this->forms->query($q,$from);
		$rl = new rowlist($e,$list->length,param('page'));
		$s = $rl->getStartIndex();
		$f = $rl->getFinishIndex();
		foreach($list as $i => $row){
			if($i<$s) continue;
			elseif($i>$f) break;
			switch(true){
				case $row instanceof DOMAttr:
					$rl->addRow($row->name,array(
						'sort'=>$i+1,
						'name'=>$row->name,
						'value'=>$row->value
					));
					break;
				case $row instanceof DOMElement:
					$rl->addRow($row->getAttribute('id'),array(
						'sort'=>$i+1,
						'title'=>$row->getAttribute('title'),
						'date'=>$row->getAttribute('date')
					));
					break;
			}
		}
		$rl->setFormAction(preg_replace('/&?mess=[\w_]*/','',$_SERVER['REQUEST_URI']));
		//$rl->getXML()->save('temp.xml'); die;
		return $rl;
	}
}
private function getForm($action, $type = 'form'){
	global $_sec;
	$fe = null;
	switch($action){
		case 'update':
		case 'apply_update':
		case 'edit_form':
			$fe = $this->forms->getElementById($_sec->getId().'_editForm');
			break;
		case 'new_form':
		case 'add':
		case 'apply_add':
		default:
			$fe = $this->forms->getElementById($_sec->getId().'_addNewForm');
			break;
	}
	
	if($fe){
		$form = new form($fe);		
		return $form;
	}
}
}
?>
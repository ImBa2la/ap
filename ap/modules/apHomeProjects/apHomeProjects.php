<?
class apHomeProjects extends module{
function getRow(){
	/*if($row = param('row')){
		if(is_array($row)) foreach($row as $i => $r) $row[$i] = intval($r);
		else $row = intval($row);
	}
	//return $row;*/
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
			$mess = 'Статья удалена'; break;
		case 'delete_fail':
			$mess = 'Ошибка, запись не удалена'; break;
		case 'update_ok':
			$mess = 'Информация успешно обновлена'; break;
		case 'update_fail':
			$mess = 'Ошибка обновления информации'; break;
		case 'add_ok':
			$mess = 'Запись добавлена'; break;
		case 'add_fail':
			$mess = 'При добавлении записи произошла ошибка'; break;
		case 'move_ok':
			$mess = 'Позиция записи изменена'; break;
		case 'move_fail':
			$mess = 'При изменении позиции записи произошла ошибка'; break;
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
	if(($ff = $xml->query('/section/modules/module[@id="'.$this->getId().'"]/form/field[@type="select" and @name="plan" and not(option)]'))
			&& ($dir = $xml->evaluate('string(//module[@id="'.$this->getId().'"]/@path)'))
	){
		foreach (glob('../'.$dir."*.png") as $filename['path']) {
			$filename['info'] = pathinfo($filename['path']);
			foreach($ff as $field)
				$field->appendChild($xml->createElement('option',array('value'=>$dir.$filename['info']['filename'].'.'.$filename['info']['extension']),$filename['info']['filename']));
		}
	}
	
	$form_element = null;
	switch($action){
		case 'update':
		case 'apply_update':
		case 'edit':
			$form_element = $xml->getElementById('form_edit_projects');
			break;
		case 'new':
		case 'add':
		case 'apply_add':
		default:
			$form_element = $xml->getElementById('form_add_projects');
			break;
	}
	
	if($form_element){
		$form = new form($form_element);		
		return $form;
	}
}
function getList($id = null, $mid = null){
	if(($list_element = $this->query('./rowlist')->item(0))
			&& isset($id)
			&& isset($mid)
			&& ($clientSec = ap::getClientSection($id))
			&& ($clientModule = $clientSec->getXML()->query('/section/modules/module[@id="'.$mid.'"]')->item(0))){
		$res = $clientSec->getXML()->query('./plan',$clientModule);
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
	} else throw new Exception('Rowlist not found');
}
function getClientList(){
	if($form = $this->getForm('edit'))
		$form->replaceURI(array('MD' => $this->getId(),'SECTION' => $this->getSection()->getId()));
	if($form
		&& ($uri = $form->getRootElement()->getAttribute('baseURI').'')
	){
		//получаем тэг со списком файлов
		$scheme = new xmlScheme();
		if(!($n = $scheme->getNode($uri))){
			$scheme->add($uri.'/plan',null);
			$scheme->save();
			$n = $scheme->getNode($uri);
		}
		if($n instanceof DOMElement){
			return new taglist($n,'plan');		
		}
	}
}
function run(){
	global $_out;
	if(ap::isCurrentModule($this)){
		ap::addMessage($this->getMessage());
		$action = param('action');
		$form = $this->getForm($action);
		$cl = $this->getClientList();
		$row = $this->getRow();
		
		switch($action){
			case 'move':
				if($row
					&& ($pos = param('pos'))
					&& ($rl = $this->getList($this->getSection()->getId(),$this->getId()))
					&& ($clRow = $cl->getById($row))
					&& $cl->move($clRow,$pos)
				){
					$cl->getXML()->save();
					$this->redirect('move_ok');
				}else $this->redirect('move_fail');
				break;
			case 'delete':
				if(!is_array($row)) $row = array($row);
				$counter = 0;
				foreach($row as $id){
					if($id
						&& ($e = $cl->getById($id))
					){
						$counter++;
						$cl->remove($e);
					}
				}
				if($counter && $counter==count($row)){
					$cl->getXML()->save();
					$this->redirect('delete_ok');
				}else $this->redirect('delete_fail');
				break;
			case 'update':
			case 'apply_update':
				if($row
					&& $cl->getById($row)
				){
					$form->replaceURI(array('ROW'=>$row));
					$values = $this->initImages($form,true);
					$values = array_merge($_REQUEST,$values);
					$form->save($values);
					$this->updateImagesSize($form);
					$this->redirect('update_ok');
				}else $this->redirect('update_fail');
				break;
			case 'add':
			case 'apply_add':
				if(($title = param('title'))){
					$cl->append(array('id'=>$cl->generateId('p'),'title'=>$title));
					$cl->getXML()->save();
					$form->replaceURI(array('ROW' => 'p'.$cl->getNum()));
					$values = $this->initImages($form,true);
					$values = array_merge($_REQUEST,$values);
					$form->save($values);
					$this->updateImagesSize($form);
					$this->redirect('add_ok');
				}else $this->redirect('add_fail');
				break;
			case 'edit':
				$pos = $cl->getPos($cl->getById($row))+1;
				$form->replaceURI(array('SECTION'=>$this->getSection()->getId(),'MD'=>$this->getId(),'ROW'=>$row));
				$form->load();
			case 'new':
				$this->initImages($form,false);
				$form->load();
				$_out->elementIncludeTo($form->getRootElement(),'/page/section');
				break;
			default:
				if($rl = $this->getList($this->getSection()->getId(),$this->getId())){
					$_out->addSectionContent($rl->getRootElement());
				}
		}
	}
}
function install(){
	$xml_data = new xml(PATH_MODULE.__CLASS__.'/data.xml');
	$xml_sec = $this->getSection()->getXML();
	$ar = array('form_edit_projects','form_add_projects','project_list');
	foreach($ar as $id){
		$e = $xml_data->query('//*[@id="'.$id.'"]')->item(0);
		if($e && !$xml_sec->evaluate('count(./*[@id="'.$id.'"])',$this->getRootElement()))
			$xml_sec->elementIncludeTo($e,$this->getRootElement());
	}
	$xml_sec->save();
	
	if($sec = ap::getClientSection($this->getSection()->getId())){
		$modules = $sec->getModules();
		if(!$modules->getById($this->getId())){
			$modules->add('plan',$this->getTitle(),$this->getId());
			$modules->getXML()->save();
		}
		return true;
	}
}
function uninstall(){
	/*if($form = $this->getForm()){
		//удаляем галерею
		$formFields = $form->getFields('@type="image"');
		foreach($formFields as $ff){
			$formats = array();
			$res = $ff->query('param');
			foreach($res as $param) $formats[] = $param->parentNode->removeChild($param);
			
			$scheme = new xmlScheme();
			if(($n = $scheme->getNode($ff->getURI()))
				&& $n instanceof DOMElement
			){
				$tl = new taglist($n,'img');
				foreach($tl as $img){
					$fieldName = $ff->getName().'_IMAGE_ID_'.$img->getAttribute('id');
					foreach($formats as $param){
						$e = $ff->getRootElement()->appendChild($param->cloneNode(true));
						$e->setAttribute('name',$fieldName);
						$e->setAttribute('uri',str_replace('%IMG_NAME%',$this->getGalleryImageName($img->getAttribute('id')),$e->getAttribute('uri')));
					}
				}
				$ff->removeImageFiles();
				$n->parentNode->removeChild($n);
				$tl->getXML()->save();
			}
		}
	}*/
	
	if($sec = ap::getClientSection($this->getSection()->getId())){
		$modules = $sec->getModules();
		if($modules->remove($this->getId()))
			$modules->getXML()->save();
		return true;
	}
}

//Галерея
function getGalleryImageName($id){
	return implode('_',array($this->getSection()->getId(),$this->getId(),$id));
}
function initImages($form,$isUpdate){
	$formFields = $form->getFields('@type="image"');
	$values = array();
	foreach($formFields as $ff){
		//форматы картинок
		$formats = array();
		$res = $ff->query('param');
		foreach($res as $param) $formats[] = $param->parentNode->removeChild($param);
		//получаем тэг со списком картинок, создаем объект списка
		$scheme = new xmlScheme();
		$scheme->add($ff->getURI().'/@name',$ff->getName());
		$scheme->save();
		if(($n = $scheme->getNode($ff->getURI()))
			&& $n instanceof DOMElement
		){
			$tl = new taglist($n,'img');
			
			//добавляем новые картинки
			$arNewImages = array();
			if($isUpdate && isset($_REQUEST[$fieldName = $ff->getName().'___new']) && is_array($_REQUEST[$fieldName])){
				$fieldNameTitle = 'title_'.$fieldName;
				foreach($_REQUEST[$fieldName] as $j => $src){
					if(file_exists($path = $_SERVER['DOCUMENT_ROOT'].$src)){
						//делаем id
						$i = 1;
						while($tl->getById($id = "i$i"))$i++;
						
						$arImg = array();
						$arPrev = array();
						//добавляем поля в форму
						$values[$name = $ff->getName().'_IMAGE_ID_'.$id] = $src;
						foreach($formats as $param){
							$e = $ff->getRootElement()->appendChild($param->cloneNode(true));
							$e->setAttribute('name',$name);
							$e->setAttribute('uri',str_replace('%IMG_NAME%',$this->getGalleryImageName($id),$e->getAttribute('uri')));
							if($param->hasAttribute('preview')) $arPrev[] = $e;
							else $arImg[] = $e;
						}
						
						if(!count($arImg) && count($arPrev)){						
							$arImg = $arPrev;
							$arPrev = array();
						}
						
						if(count($arImg)
							&& ($src_rel = $ff->getImagePath(form::getURI($arImg[0])))
						){//добавляем в XML список картинок
							if(strpos($src_rel,PATH_ROOT)===0)
								$src_rel = substr($src_rel,strlen(PATH_ROOT));
							$arNewImages[$id] = $tl->append(array(
									'id'=>$id
									,'src'=>$src_rel
								));
							//превью
							if(count($arPrev)
								&& ($src_rel = $ff->getImagePath(form::getURI($arPrev[0])))
							){
								if(strpos($src_rel,PATH_ROOT)===0)
									$src_rel = substr($src_rel,strlen(PATH_ROOT));
								$arNewImages[$id]->appendChild($tl->getXML()->createElement('preview',array(
										'src'=>$src_rel
									)));
							}
							
							if(isset($_REQUEST[$fieldNameTitle])
								&& isset($_REQUEST[$fieldNameTitle][$j])
								&& $_REQUEST[$fieldNameTitle][$j]
							){
								$arNewImages[$id]->setAttribute('title',mb_substr($_REQUEST[$fieldNameTitle][$j],0,127));
							}
						}
					}
				}
			}
			
			//заполняем форму текущими картинками
			$rowsToDelete = array();
			foreach($tl as $img){
				$fieldName = $ff->getName().'_IMAGE_ID_'.$img->getAttribute('id');
				if($isUpdate
					&& !isset($_REQUEST[$ff->getName()][$fieldName])
					&& !isset($arNewImages[$img->getAttribute('id')]))
				{ //определяем картинки для удаления
					$values[$fieldName] = jpgScheme::VALUE_DELETE;
					$rowsToDelete[] = $img;
				}
				foreach($formats as $param){
					if(!$isUpdate && !$param->hasAttribute('preview')) continue;
					$e = $ff->getRootElement()->appendChild($param->cloneNode(true));
					$e->setAttribute('name',$fieldName);
					$e->setAttribute('uri',str_replace('%IMG_NAME%',$this->getGalleryImageName($img->getAttribute('id')),$e->getAttribute('uri')));
					if(!$isUpdate && $img->hasAttribute('title'))
						$e->setAttribute('title',$img->getAttribute('title'));
				}
			}
			//обновляем данные
			if($isUpdate){
				//удаляем
				foreach($rowsToDelete as $e) $e->parentNode->removeChild($e);
				//обновляем тайтлы
				if(isset($_REQUEST[$fieldNameTitle = 'title_'.$ff->getName()])
					&& is_array($_REQUEST[$fieldNameTitle])
				) foreach($_REQUEST[$fieldNameTitle] as $str => $title){
					if(preg_match('/'.$ff->getName().'_IMAGE_ID_(i[0-9]+)/',$str,$m)
						&& ($e = $tl->getById($m[1]))
					){
						if($title) $e->setAttribute('title',$title);
						else $e->removeAttribute('title');
					}
				}
				//пересортировываем
				$sortOrder = isset($_REQUEST[$ff->getName().'_sort_order']) ? explode(',',$_REQUEST[$ff->getName().'_sort_order']) : array();
				foreach($sortOrder as $i => $str){
					if(preg_match('/id(i[0-9]+)/',$str,$m)
						&& ($e = $tl->getById($m[1]))
					) $tl->move($e,$i+1);
					elseif(preg_match('/new[0-9]+/',$str)
						&& ($e = array_shift($arNewImages))
					) $tl->move($e,$i+1);
				}
			}
			$tl->getXML()->save();
		}
		$ff->getRootElement()->setAttribute('target',$ff->getURI());
		$ff->getRootElement()->removeAttribute('uri');
	}
	return $values;
}
function updateImagesSize($form){
	$formFields = $form->getFields('@type="image"');
	foreach($formFields as $ff){
		$scheme = new xmlScheme();
		if(($n = $scheme->getNode($ff->getRootElement()->getAttribute('target')))
			&& $n instanceof DOMElement
		){
			$tl = new taglist($n,'img');
			foreach($tl as $img){
				if($img->hasAttribute('width') && $img->hasAttribute('height')) continue;
				$fieldName = $ff->getName().'_IMAGE_ID_'.$img->getAttribute('id');
				if(($res = $ff->query('param[@name="'.htmlspecialchars($fieldName).'"]'))
					&& ($e = $res->item(0))
					&& ($src_rel = $ff->getImagePath(form::getURI($e)))
				){
					list($w,$h) = getimagesize($src_rel);
					if($w && $h){
						$img->setAttribute('width',$w);
						$img->setAttribute('height',$h);
					}
					if(($prv = $tl->getXML()->query('preview',$img)->item(0))
						&& ($e = $res->item(1))
						&& ($src_rel = $ff->getImagePath(form::getURI($e)))
					){
						list($w,$h) = getimagesize($src_rel);
						if($w && $h){
							$prv->setAttribute('width',$w);
							$prv->setAttribute('height',$h);
						}
					}
				}
			}
			$tl->getXML()->save();
		}
	}
}

function settings($action){
	global $_out;
	$xml = new xml(PATH_MODULE.__CLASS__.'/data.xml');
	if($e = $xml->getElementById('settings')){
		$form = new form($e);
		$form->replaceURI(array(
			'MODULE'=>$this->getId(),
			'SECTION'=>$this->getSection()->getId()
		));
		switch($action){
			case 'update':
			case 'apply_update':
				$form->save($_REQUEST);
				return;
		}
		$form->load();
		if(($ff = $form->getField('planPath')) && !$ff->getValue())
			$ff->setValue('userfiles/projects/plans/');
		$_out->addSectionContent($form->getRootElement());
	}
}

}
?>
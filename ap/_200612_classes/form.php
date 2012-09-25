<?
class form{
private $e;
private $schemeCache = array();
function __construct(DOMElement $e){
	$this->e = $e;
}
function setTitle($val){
	$this->e->setAttribute('title',$val);
}
function setURL($val){
	$this->getRootElement()->setAttribute('action',$val);
}
function getXML(){
	return new xml($this->e->ownerDocument);
}
function getRootElement(){
	return $this->e;
}
function clearSchemeCache(){
	$this->schemeCache = array();
}
function getSchemeCache(){
	return $this->schemeCache;
}
function getSchemeCacheObject($uri){
	if($className = form::getSchemeClassName($uri)){
		
		if(isset($this->schemeCache[$className]))
			return $this->schemeCache[$className];
		if($o = form::getSchemeObject($uri))
			return $this->schemeCache[$className] = $o;
	}
}
static function getSchemeClassName($uri){
	//echo 'test: '.$url['scheme'].'<br />';
	if(($url = parse_url($uri)) && isset($url['scheme'])){
		if($url['scheme']=='file' && preg_match('/[\w_]*\.([\w_]+)+/',$url['path'],$res)){
			$url['scheme'] = $res[1];
		}
		return $url['scheme'].'Scheme';
	}
}
static function getSchemeObject($uri){
	if(($className = form::getSchemeClassName($uri)) && class_exists($className))
		return new $className;
}
static function getBaseURI(DOMElement $e){
	while($e = $e->parentNode)
		if($e->hasAttribute('baseURI')) return $e->getAttribute('baseURI');
		elseif(strtolower($e->tagName)=='form') break;
}
static function getURI(DOMElement $e){
	$uri = $e->getAttribute('uri');
	$url = parse_url($uri);
	if(!isset($url['scheme']) && ($baseURI = form::getBaseURI($e)))
		$uri = $baseURI.$uri;
	return $uri;
}
function load(){
	$xml = $this->getXML();
	$res = $xml->query('.//param[@uri] | .//field[@uri]',$this->e);
	$this->clearSchemeCache();
	foreach($res as $f){
		$uri = form::getURI($f);
		if($scheme = $this->getSchemeCacheObject($uri)){
			$ff = $this->getField($f);
			$ff->setValue($scheme->get($uri));
		}
	}
}
function save($data){
	$this->clearSchemeCache();
	$xml = $this->getXML();
	$res = $xml->query('.//field[@uri] | .//param[@uri]',$this->e);
	foreach($res as $f){
		$uri = form::getURI($f);
		
		if($scheme = $this->getSchemeCacheObject($uri)){
			$fieldName = $f->getAttribute('name');
			if(preg_match('/^([\w\-]+)\[([\w\-]*)\]$/',$fieldName,$matches))
				$fieldName = $matches[1];
			$val = @$data[$fieldName];
			if($f->hasAttribute('saveIfNoEmpty') && !$val) continue;
			if($f->hasAttribute('saveMD5')){
				if(is_array($val)){
					$data[$fieldName][$matches[2]] =
					$val[$matches[2]] = md5($val[$matches[2]]);
				}else{
					$val = md5($val);
				}
			}
			if($f->getAttribute('type')=='checkbox' && !$val) $val = 0;
			if(($f->getAttribute('type')=='checkbox') && ($f->getAttribute('name')=='autocheck') && $val) $val = 'autocheck';
			$scheme->add($uri,$val);
		}
	}
	$schemes = $this->getSchemeCache();
	foreach($schemes as $scheme) $scheme->save();
}
function replaceURI($v){
	if(!is_array($v) || !count($v)) return;
	$xml = $this->getXML();
	$res = $xml->query('//@*',$this->e);
	foreach($res as $i => $attr){
		foreach($v as $search => $replace){
			$attr->value = str_replace('%'.$search.'%',$replace,htmlspecialchars($attr->value));
		}
	}
}
static function replaceURIStatic($v,$e){
	if(!is_array($v) || !count($v)) return;
	$xml = $this->getXML();
	$res = $xml->query('//@uri | //@baseURI | //@target' ,$e);
	foreach($res as $i => $attr){
		foreach($v as $search => $replace){
			$attr->value = str_replace('%'.$search.'%',$replace,htmlspecialchars($attr->value));
		}
	}
}
function getField($field){
	$xml = $this->getXML();
	if(is_object($field)){
		if($field instanceof DOMElement) $e = $field;
		elseif($field instanceof formField) return $field;
	}elseif($field){
		$e = $xml->query('.//*[(name()="field" or name()="param") and @name="'.htmlspecialchars($field).'"]',$this->e)->item(0);
	}
	if($e){
		switch($e->getAttribute('type')){
			case 'image':
				return new formImageField($e);
			case 'select':
				return new formSelect($e);
			case 'checkbox':
				return new formCheckbox($e);
			case 'banner':
				return new formBanner($e);
			default:
				if($e->tagName=='param'){
					return new formHiddenField($e);
				}else
					return new formField($e);
		}
	}
}
function removeField($field){
	$xml = $this->getXML();
	if(is_object($field)){
		if($field instanceof DOMElement) $e = $field;
		elseif($field instanceof formField) return $field;
	}elseif($field){
		$e = $xml->query('.//*[(name()="field" or name()="param") and @name="'.htmlspecialchars($field).'"]',$this->e)->item(0);
	}
	if($e){
		return $e->parentNode->removeChild($e);
	}
}
function getFields($xpath = null){
	$res = $this->getXML()->query('.//*[(name()="field" or name()="param")'.($xpath ? ' and '.$xpath : null).']',$this->getRootElement());
	$ar = array();
	foreach($res as $e)
		if($ff = $this->getField($e)) $ar[] = $ff;
	return $ar;
}
}

class formField{
protected $e;
function __construct(DOMElement $e){
	$this->e = $e;
}
function getXML(){
	return new xml($this->e);
}
function getRootElement(){
	return $this->e;
}
function query($query){
	return $this->getXML()->query($query,$this->getRootElement());
}
function getName(){
	return $this->getRootElement()->getAttribute('name');
}
function getType(){
	return $this->getRootElement()->getAttribute('type');
}
function getURI(){
	return form::getURI($this->getRootElement());
}
function replaceURI($v){
	if(!$this->getRootElement()->hasAttribute('uri') || !is_array($v) || !count($v)) return;
	$uri = $this->getRootElement()->getAttribute('uri');
	foreach($v as $search => $replace)
		$uri = str_replace('%'.$search.'%',$replace,$uri);
	$this->getRootElement()->setAttribute('uri',$uri);
}
function setValue($value){
	xml::setElementText($this->e,$value);
}
function getValue(){
	return xml::getElementText($this->e);
}
function remove(){
	return $this->getRootElement()->parentNode->removeChild($this->getRootElement());
}
}

class formCheckbox extends formField{
function setValue($value){
	if($value)
		$this->e->setAttribute('checked','checked');
}
function getValue(){
	return $this->e->hasAttribute('checked');
}
}

class formHiddenField extends formField{
function setValue($value){
	$this->getRootElement()->setAttribute('value',$value);
}
function getValue(){
	return $this->getRootElement()->getAttribute('value');
}
}

class formImageField extends formField{
function setValue($value){
	//$this->getRootElement()->setAttribute('value',$value);
}
function getValue(){
	//return $this->getRootElement()->getAttribute('value');
}
static function imageExists($uri){
	if(($v = jpgScheme::parseURI($uri)) && $v['path']){
		return file_exists($v['path']);
	}
}
function getPreviewSize(){
	$ar = array();
	$tmp = explode('&',parse_url($this->getURI(),PHP_URL_QUERY));
	foreach($tmp as $pair){
		$v = explode('=',$pair);
		$ar[$v[0]] = $v[1];
	}
	return array('width' => isset($ar['w']) ? $ar['w'] : false
		,'height' => isset($ar['h']) ? $ar['h'] : false);
}
function setPreviewSize($w = null,$h = null){
	$uri = parse_url($this->getRootElement()->getAttribute('uri'));
	//vdump($uri);
	$str = (isset($uri['scheme']) ? $uri['scheme'].':' : null)
		.(isset($uri['host']) ? '//'.$uri['host'] : null)
		.(isset($uri['path']) ? $uri['path'] : null)
		.($w || $h ? '?' : null)
		.($w ? 'w='.$w : null)
		.($w && $h ? '&' : null)
		.($h ? 'h='.$h : null);
	$this->getRootElement()->setAttribute('uri',$str);
}
static function getImagePath($uri){
	if($v = jpgScheme::parseURI($uri)) return $v['path'];
}
function removeImageFiles(){
	$res = $this->getXML()->query('param[@uri]',$this->getRootElement());
	foreach($res as $p)
		if(($path = $this->getImagePath(form::getURI($p))) && file_exists($path))
			unlink($path);
}
}

class formSelect extends formField{
function setValue($value){
	$this->e->setAttribute('value',$value);
}
function getValue(){
	return $this->e->getAttribute('value');
}
function addOption($value,$text){
	$xml = new xml($this->e);
	$this->e->appendChild($xml->createElement('option',array('value'=>$value),$text));
}
}
?>
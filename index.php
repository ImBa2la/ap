<?php
function autoload($class){
	if(file_exists($path = 'ap/classes/'.$class.'.php') || file_exists($path = 'classes/'.$class.'.php'))
		require_once $path;
}
spl_autoload_register('autoload');
require 'ap/lib/default.php';

define('EXCEPTION_404',1);
define('EXCEPTION_MYSQL',2);
define('EXCEPTION_TPL',3);
define('EXCEPTION_XML',4);

$temp = pathinfo($_SERVER['PHP_SELF'],PATHINFO_DIRNAME);
if($temp=='\\') $temp = '/';
elseif(substr($temp,-1)!='/') $temp.= '/';
define('BASE_URL',$temp);

class params{
	private $ar;
	function __construct(){$this->ar = params::parse('page','row');}
	function exists($v){return in_array($v,$this->ar);}
	function pop(){return array_pop($this->ar);}
	function shift(){return array_shift($this->ar);}
	static function get(){
		$uriPath = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
		$ar = explode('/',trim(
			BASE_URL=='/' || !BASE_URL
				? $uriPath
				: str_replace(BASE_URL,'',$uriPath)
			,'/'));
		foreach($ar as $i => $val) $ar[$i] = urldecode($val);
		return $ar;
	}
	static function parse(){
		$ar = params::get();
		param('id',array_shift($ar));
		if(!is_array($ar) || !($num = count($ar))) return array();
		$args = func_get_args();
		foreach($args as $prefix){
			if(!$num) break;
			foreach($ar as $i => $v)
				if(preg_match('/^'.$prefix.'([[0-9a-z_\-]+)$/',$v,$res)){
					param(trim($prefix,'_'),$res[1]);
					unset($ar[$i]);
					$num = count($ar);
					break;
				}
		}
		return array_values($ar);
	}
}
try{
	$_params = new params();
	$_site = new site('xml/site.xml');
	$_struct = new structure('xml/structure.xml');
	$_out = new out();
	$_events = new events('xml/events.xml');
	$_events->addEvent('SectionReady');
	$_events->addEvent('PageReady');
	
	$modules = $_site->getModules();
	if(!$modules->hasModule('main'))
		$modules->move($modules->add('main'),1);
	$modules->run();
	
	$_sec = $_struct->getCurrentSection();
	$_events->happen('SectionReady');
	$_sec->getModules()->run();
	$_out->xmlInclude($_struct);
	$_out->xmlInclude($_site);
	$_events->happen('PageReady');
	
	$_out->de()->setAttribute('host',$_SERVER['HTTP_HOST']);
	$_tpl = $_sec->getTemplate();
	$_out->save('temp.xml');
	echo $_tpl->transform($_out);
}catch(Exception $e){
	switch($e->getCode()){
		case EXCEPTION_404:
			header("HTTP/1.0 404 Not Found");
			$_site = new site('xml/site.xml');
			$_struct = new structure('xml/structure.xml');
			$_out = new out();
			
			$_out->setMeta('title','404 страница не найдена');
			$_sec = $_struct->addSection('error404','404');
			$_sec->setSelected(true);
			
			$_out->xmlInclude($_struct);
			$_out->xmlInclude($_site);
			$_out->de()->setAttribute('host',$_SERVER['HTTP_HOST']);
			
			$_tpl = new template($_struct->getTemplatePath().'default.xsl');
			$_tpl->addTemplate($_struct->getTemplatePath().'404.xsl');
			echo $_tpl->transform($_out);
			break;
		default:
			echo 'Exception: '.$e->getMessage();
	}	
}
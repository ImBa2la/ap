<?
class ymaps extends module{
function run(){
	global $_out,$_params;
	$xml = $this->getSection()->getXML();
	//Если есть карта
	
	if(('map'==array_shift($_params))
		&& ($map['xml'] = $xml->query('./map',$this->getRootElement())->item(0))
		&& ($map['id'] = $map['xml']->getAttribute('id'))
		&& ($map['key'] = $map['xml']->getAttribute('key'))
		&& ($map['tpl'] = new template('xml/templates/'.$map['xml']->getAttribute('tpl').'.xsl'))){
		$map['in'] = new xml(file_get_contents('http://maps.yandex.ru/export/usermaps/'.$map[id].'/'));
		$map['in']->de()->setAttribute('key',$map['key']);
		$map['out'] = $map['tpl']->transform($map['in']);
		echo $map['out'];
		die;
	}
}
}
?>
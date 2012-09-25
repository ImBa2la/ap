<?
class vacancy extends module{
function run(){
	global $_out;
	if($xml = $this->getVacancyXML(param('row')))
		$_out->addSectionContent($xml);
}
static function createVacancyElement(xml $xml,$r = array()){
	$arFields = array('title','company','city','responsibility','requirements','desc');
	$e = $xml->createElement('vacancy',array(
			'id'	=> $r['id']
			,'date'	=> date('d.m.Y',strtotime($r['date']))
		));
	if($r['type_employer']) $e->setAttribute('agency','agency');
	foreach($arFields as $name)
		if($r[$name]) $e->appendChild($xml->createElement($name,null,$r[$name]));
	
	$eSalary = $e->appendChild($xml->createElement('salary'));
	if($v = $r['salary_from'])
		$eSalary->appendChild($xml->createElement('min',null,number_format($v,0,',',' ')));
	if($v = $r['salary_to'])
		$eSalary->appendChild($xml->createElement('max',null,number_format($v,0,',',' ')));
		
	$eContact = $e->appendChild($xml->createElement('contact'));
	if($v = $r['contact_face'])
		$eContact->appendChild($xml->createElement('person',null,$v));
	if($v = $r['contact_phone'])
		$eContact->appendChild($xml->createElement('phone',null,$v));
	if($v = $r['email'])
		$eContact->appendChild($xml->createElement('email',null,$v));
	return $e;
}
function getVacancyXML($id = null){
	$mysql = new mysqlTable('vacancy');
	
	if(($id = intval($id))
		&& $mysql->getRow($rs,'`id`='.$id.' AND `section`='.mysql::str($this->getSection()->getId()))
		&& ($r = mysql_fetch_assoc($rs))
	){
		$xml = new xml();
		$xml->dd()->appendChild(vacancy::createVacancyElement($xml,$r));
		return $xml;
	}
	
	$limit = $mysql->getLimit(param('page'),20,$condition = '`section`='.mysql::str($this->getSection()->getId()).' AND `active`=1');
	$xml = new xml();
	$xml->dd()->appendChild($xml->createElement('vacancies',array(
		'rows'		=> $limit['num_rows']
		,'pages'	=> $limit['page_num']
		,'pagesize'	=> $limit['page_size']
		,'page'		=> $limit['page_current']
		,'pageParam'=> 'page')));
	
	if($mysql->getRow($rs,$condition,$limit['limit_string'])){
		while($r = mysql_fetch_assoc($rs))
			$xml->de()->appendChild(vacancy::createVacancyElement($xml,$r));
		return $xml;
	}
}
static function getVacancyAnnounceXML(){
	global $_struct;
	$xml = new xml(null,'vacancies',false);
	
	function getRuMonth($v){
		switch($v){
			case 1: return 'января';
			case 2: return 'февраля';
			case 3: return 'марта';
			case 4: return 'апреля';
			case 5: return 'мая';
			case 6: return 'июня';
			case 7: return 'июля';
			case 8: return 'августа';
			case 9: return 'сентября';
			case 10: return 'октября';
			case 11: return 'ноября';
			case 12: return 'декабря';
		}
	}
	
	$xml->de()->setAttribute('datetime',date('Y-m-d'));
	$xml->de()->setAttribute('date',date('d '.getRuMonth(date('m')).' Y г.'));
	
	
	$ns = $_struct->query('//sec[@id="list_of_vacancies"]/sec');
	foreach($ns as $e)
		$xml->de()->appendChild($xml->createElement('department',array(
				'id' => $e->getAttribute('id')
				,'title' => $e->getAttribute('title')
			)));
	$mysql = new mysql();
	
	if($rs = $mysql->query('SELECT v2.*
FROM `'.$mysql->getTableName('vacancy').'` AS v1
LEFT JOIN `'.$mysql->getTableName('vacancy').'` AS v2 ON v1.`section`=v2.`section` AND v2.`date` = (SELECT MAX(`date`) FROM `'.$mysql->getTableName('vacancy').'` WHERE `section`=v1.`section` AND `active`=1)
WHERE v1.`active`=1
GROUP BY v1.`section`')
	){
		while($r = mysql_fetch_assoc($rs))
			if($e = $xml->query('department[@id = "'.$r['section'].'"]')->item(0))
				$e->appendChild(vacancy::createVacancyElement($xml,$r));
		return $xml;
	}
}
static function structInit(){
	global $_struct;
	$mysql = new mysql();
	$cvList = $_struct->query('./sec',$_struct->getElementById('list_of_vacancies'));
	foreach($cvList as $cvItem){
		$q = 'select `title`,`id` from `'.$mysql->getTableName('vacancy').'` where `active`=1 AND `section`='.mysql::str($cvItem->getAttribute('id'));
		$rs = $mysql->query($q);
		while($r = mysql_fetch_assoc($rs)){
			$cvItem->appendChild($_struct->createElement(
				'item'
				,array('id'=>$r['id'],'title'=>$r['title'])
			));
		}
	}
}
}
?>
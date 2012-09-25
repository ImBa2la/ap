<?
class apVacancy extends apArticles{
protected $table = 'vacancy';
function getList(){
	$rl = parent::getList();
	$rl->getXML()->save('temp.xml');
	$cond['str'] = '';
	$cond['arr'] = array();
	if(is_array($c = param('cond'))){
		$xml = $this->getSection()->getXML();
		$le = $this->query('rowlist[@id="article_list"]')->item(0);
		$filterFields = $xml->query('filter/field',$le);
		$cond['logic'] = $xml->evaluate('string(filter/@logic)',$le);
		foreach($filterFields as $f){
			switch(strtolower($f->getAttribute('operator'))){
				case 'like':	$op = ' LIKE "%{VALUE}%"'; break;
				case 'lt':		$op = ' < "{VALUE}"'; break;
				case 'gt':		$op = ' > "{VALUE}"'; break;
				case 'lte':		$op = ' <= "{VALUE}"'; break;
				case 'gte':		$op = ' >= "{VALUE}"'; break;
				default:		$op = ' = "{VALUE}"'; #equal
			}
			if($c[$f->getAttribute('name')] && $c[$f->getAttribute('name')] != $f->getAttribute('label'))
				$cond['arr'][] = '`'.$f->getAttribute('name').'`'.str_replace('{VALUE}', $c[$f->getAttribute('name')], $op);
		}
		$cond['str'] = implode(
				' '.(strtolower($cond['logic']) == 'or'?'OR':'AND').' '
				,$cond['arr']
			);
	}
			
	$order = array('col' => 'sort','order' => 'desc');
	if(is_array($o = param('order'))){
		$order = array(
			'col' => $o[0],
			'order' => $o[1]
		);
	}
	$params = $rl->setQueryParams(array());
	if($cond['str']) $params['cond'] .= ' AND '.$cond['str'];
	$params['sortcontrol'] = false;
	$params['order'] = '`'.$order['col'].'` '.$order['order'];
	$params['agregate'] = 'count(*) as count';
	$rl->setOrder($order['col'],$order['order']);
	$rl->setQueryParams($params);
	
	return $rl;
}
function showList(){
	global $_out;
	if($rl = $this->getList()){
		$rl->build();
		if(($numRows = $rl->getNumPages()) < param('page')) #condition for overflow pages
			header('Location: '.str_replace('page='.param('page'),'page='.$numRows ,$_SERVER['REQUEST_URI']) );
		$urlParams = array();
		if(is_array($c = param('cond')))
			foreach($c as $k=>$v)
				$urlParams['cond['.$k.']'] = $v;
		$rl->setFilter($c);
		$rl->getRootElement()->setAttribute('sortUri',ap::getUrl($urlParams));
		$_out->addSectionContent($rl->getRootElement());
	}
}
function onAdd($action){
	if($row = parent::onAdd($action)){
		$mysql = new mysql();
		$mysql->update($this->table, array('send'=>1), '`id`='.mysql::str($row));
		return true;
	}
}
function install(){
	$mysql = new mysql();
	if(!$mysql->hasTable($this->table)){
		$mysql->query('CREATE TABLE `'.$mysql->getTableName($this->table).'` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`section` varchar(63) DEFAULT NULL,
		`module` varchar(15) DEFAULT NULL,
		`date` datetime DEFAULT NULL,
		`title` varchar(55) NOT NULL,
		`company` varchar(55) NOT NULL,
		`city` varchar(55) NOT NULL,
		`email` varchar(55) NOT NULL,
		`contact_face` varchar(55) NOT NULL,
		`contact_phone` varchar(55) NOT NULL,
		`type_employer` tinyint(1) unsigned DEFAULT "0",
		`salary_from` int(11) unsigned DEFAULT NULL,
		`salary_to` int(11) unsigned DEFAULT NULL,
		`responsibility` text,
		`requirements` text,
		`desc` text,
		`active` tinyint(1) unsigned DEFAULT NULL,
		`send` tinyint(1) unsigned NOT NULL DEFAULT "1",
		`sort` int(10) unsigned NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8');	
	}
	if(parent::install()){
		return true;
	}
}
}
?>
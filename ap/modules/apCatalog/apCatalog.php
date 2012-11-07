<?
class apCatalog extends apArticles{
protected $table = 'catalog';
protected $tableImages = 'catalog_images';
function fillProducers($form){
	$mysql = new mysql();
	$ff = $form->getField('producer_id');
	$res = $mysql->query('	SELECT `id`,`title` 
							FROM `'.$mysql->getTableName('articles').'` 
							WHERE `section`="catalog" AND `module`="m4" AND `active` = 1 
							ORDER BY `sort`'
	);
	if($ff && (mysql_num_rows($res) > 0))
		while($r = mysql_fetch_assoc($res))
			$ff->addOption($r['id'],$r['title']);
}
function getList(){
	$rl = parent::getList();
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
function onEdit($action){
	$this->fillProducers($this->getForm($action));
	parent::onEdit($action);
	
}
function onNew($action){
	$this->fillProducers($this->getForm($action));
	parent::onNew($action);
}
function install(){
	$mysql = new mysql();
	if(!$mysql->hasTable($this->table)){
		$mysql->query('CREATE TABLE `'.$mysql->getTableName($this->table).'` (
	`id` int(9) unsigned NOT NULL AUTO_INCREMENT,
	`section` varchar(63) DEFAULT NULL,
	`module` varchar(63) DEFAULT NULL,
	`date` datetime DEFAULT NULL,
	`title` varchar(63) NOT NULL,
	`price` double(10,2) DEFAULT NULL,
	`producer_id` int(9) DEFAULT NULL,
	`desc` text,
	`capacity_cooling` double(3,2) DEFAULT NULL,
	`capacity_heating` double(3,2) DEFAULT NULL,
	`noise_level_min` varchar(55) DEFAULT NULL,
	`noise_level_max` varchar(55) DEFAULT NULL,
	`mode` varchar(55) DEFAULT NULL,
	`mode_ventilation` tinyint(1) DEFAULT "0",
	`mode_dehumidification` tinyint(1) DEFAULT "0",
	`mode_night` tinyint(1) DEFAULT "0",
	`mode_auto` tinyint(1) DEFAULT "0",
	`motion_sensor` tinyint(1) DEFAULT "0",
	`remote_control` tinyint(1) DEFAULT "0",
	`inverter_power_control` tinyint(1) DEFAULT "0",
	`air_filters` tinyint(1) DEFAULT "0",
	`active` tinyint(1) unsigned DEFAULT "1",
	`sort` int(9) unsigned NOT NULL,
	PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8');	
	}
	if(parent::install()){
		return true;
	}
}
}
?>
<?php
class eProducers extends apArticles {
function fillSelect($form){
	$mysql = new mysql();
	$ff = $form->getField('country');
	$res = $mysql->query('	SELECT `id`,`title` 
							FROM `'.$mysql->getTableName('articles').'` 
							WHERE `section`="catalog" AND `module`="m1" AND `active` = 1 
							ORDER BY `sort`'
	);
	if($ff && (mysql_num_rows($res) > 0))
		while($r = mysql_fetch_assoc($res))
			$ff->addOption($r['id'],$r['title']);
}
function onEdit($action){
	$this->fillSelect($this->getForm($action));
	parent::onEdit($action);
	
}
function onNew($action){
	$this->fillSelect($this->getForm($action));
	parent::onNew($action);
}
function onAdd($action){
	$mysql = new mysql();
	$_REQUEST['article_id'] = $mysql->getNextId('articles');
	if(parent::onAdd($action)) 
		return true;
}
function onDelete($action){
	if(($row = $this->getRow())
		&& parent::onDelete($row)
	){
		if(!is_array($row)) $row = array($row);
		$mysql = new mysql();
		$mysql->query('delete from `'.$mysql->getTableName('producer_country').'` where `article_id` in ('.implode(',',$row).')');
		return true;
	}
}
}

?>

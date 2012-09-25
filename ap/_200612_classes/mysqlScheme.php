<?
class mysqlScheme{
private $values = array();
private $cache = array();
function add($uri,$value){
	if(($url = parse_url($uri))
		&& isset($url['host'])
		&& isset($url['path'])
		&& isset($url['fragment'])
	){
		$url['path'] = trim($url['path'],'/');
		$cond = isset($url['query']) ? $this->parseQuery($url['query']) : '__new__';
		$this->values[$url['host']][$url['path']][$cond][$url['fragment']] = $value;
	}
}
function save(){
	foreach($this->values as $con => $tables){
		$mysql = new mysql($con);
		foreach($tables as $table => $conditions){
			foreach($conditions as $condition => $columns){
				if(!$condition) continue;
				if($condition == '__new__'){
					$row = array();
					foreach($columns as $name => $value)
						$row['`'.$name.'`'] = '"'.addslashes($value).'"';
					$query = 'INSERT INTO `'.$mysql->getTableName($table).'` ('.implode(',',array_keys($row)).') VALUES ('.implode(',',$row).')';
				}else{
					$row = array();
					foreach($columns as $name => $value)
						$row[] = '`'.$name.'`="'.addslashes($value).'"';
					$query = 'UPDATE `'.$mysql->getTableName($table).'` SET '.implode(',',$row).' WHERE '.$condition;
				}
				$mysql->query($query);
			}
		}
	}
}
function get($uri){
	if(($url = parse_url($uri))
		&& isset($url['host'])
		&& isset($url['path'])
		&& isset($url['fragment'])
		&& isset($url['query'])
		&& ($cond = $this->parseQuery($url['query']))
	){
		$url['path'] = trim($url['path'],'/');
		if(($row = $this->getRow($url['host'],$url['path'],$cond))
			&& isset($row[$url['fragment']])
		){
			return $row[$url['fragment']];
		}
	}
}
function getRow($con,$table,$cond){
	if(isset($cache[$con][$table][$cond]))
		return $cache[$con][$table][$cond];
	if($table && $cond){
		$mysql = new mysql($con);
		$query = 'SELECT * FROM `'.$mysql->getTableName($table).'` WHERE '.$cond;
		if($rs = $mysql->query($query)){
			return $cache[$con][$table][$cond] = mysql_fetch_assoc($rs);
		}
	}
}
static function parseQuery($val){
	if($val){
		$tmp1 = explode('&',$val);
		$tmp2 = array();
		foreach($tmp1 as $pair){
			$tmp3 = explode('=',$pair);
			$tmp2[] = '`'.$tmp3[0].'`="'.addslashes($tmp3[1]).'"';
		}
		return implode(' AND ',$tmp2);
	}
}
}
?>
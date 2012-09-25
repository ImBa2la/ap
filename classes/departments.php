<?
class departments extends module{
function run(){
	global $_out;
	if($xml = vacancy::getVacancyAnnounceXML())
		$_out->addSectionContent($xml);
}
}
?>
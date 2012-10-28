<?
require_once '../classes/images.php';
$img = new images(
		$_GET['src']
		,isset($_GET['w'])		? intval($_GET['w'])	: null
		,isset($_GET['h'])		? intval($_GET['h'])	: null
		,isset($_GET['fixed'])	? true					: false
		,isset($_GET['ha'])		? $_GET['ha']			: 'center'
		,isset($_GET['va'])		? $_GET['va']			: 'middle'
		,isset($_GET['rgb'])	? $_GET['rgb']			: null
		,isset($_GET['alpha'])	? true					: false
		,isset($_GET['max'])	? $_GET['max']			: 1024
	);
	if(isset($_GET['waterMark'])){
		$water = new images(
			$_GET['waterMark']
			,isset($_GET['waterW'])		? intval($_GET['waterW'])	: null
			,isset($_GET['waterH'])		? intval($_GET['waterH'])	: null
			,isset($_GET['waterFixed'])	? true						: false
			,isset($_GET['waterHa'])	? $_GET['waterHa']			: 'center'
			,isset($_GET['waterVa'])	? $_GET['waterVa']			: 'middle'
			,isset($_GET['waterRGB'])	? $_GET['waterRGB']			: null
			,isset($_GET['waterAlpha'])	? true						: false
			,isset($_GET['waterMax'])	? $_GET['waterMax']			: 1024	
		);
		$img->addWatermark(
			$water
			,isset($_GET['waterOffsetX'])	? $_GET['waterOffsetX'] : 0
			,isset($_GET['waterOffsetY'])	? $_GET['waterOffsetY'] : 0
			,isset($_GET['waterOpacity'])	? $_GET['waterOpacity'] : 100
			,isset($_GET['waterAlignH'])	? $_GET['waterAlignH']	: null
			,isset($_GET['waterAlignV'])	? $_GET['waterAlignV']	: null
		);
	}
$img->imageStream();
if(isset($_GET['waterMark'])) $water->__destruct();
$img->__destruct();
die;

?>
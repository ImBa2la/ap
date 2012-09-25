<?
header("Content-Type:text/html; charset=UTF-8");
require("../ap/classes/xml.php");

if(($_REQUEST['action'] == 'save') 
	&& file_exists($_SERVER['DOCUMENT_ROOT'].$_REQUEST['xmlFileName']) 
	&& isset($_REQUEST['xmlString'])
):
	if(strlen($_REQUEST['xmlString']) < 20) return false;
	$xml = new xml();
	$xml->dd()->loadXML($_REQUEST['xmlString']);
	$xml->dd()->encoding = 'utf-8';
	$xml->dd()->formatOutput = true;
	
	if($_REQUEST['xmlElement']){
		$xmlParent = new xml($_SERVER['DOCUMENT_ROOT'].$_REQUEST['xmlFileName']);
		$xmlNewNode = $xmlParent->importNode($xml->getElementById($_REQUEST['xmlElement']),true);
		$xmlOldNode = $xmlParent->getElementById($_REQUEST['xmlElement']);	
		$xmlOldNode->parentNode->replaceChild(
			$xmlNewNode
			,$xmlOldNode
		);
		$xmlParent->save();
		return true;
	}else{
		$xml->save($_SERVER['DOCUMENT_ROOT'].$_REQUEST['xmlFileName']);
		return true;
	}
	return false;
	die;
endif;

if(isset($_REQUEST['string'])):
	$xmlEditor['func'] = 'loadXmlFromString';
	$xmlEditor['load'] = $_REQUEST['string'];
elseif(isset($_REQUEST['fid']) && isset($_REQUEST['file'])):
	$xml = new xml($_SERVER['DOCUMENT_ROOT'].$_REQUEST['file']);
	$xml->dd()->formatOutput = false;

	if($edit = $xml->getElementById($_REQUEST['fid'])):
		$tmp = new xml();
		$edit = $tmp->importNode($edit,true);
		$tmp->dd()->appendChild($edit);

		$xmlEditor['func'] = 'loadXmlFromString';
		$xmlEditor['load'] = addslashes(preg_replace(array("#>[^<]*<#xi","#[\n\t\r]#"),array("><",""),substr($tmp->__toString(),39)));
	endif;		
elseif(isset($_REQUEST['file'])):
	$xmlEditor['func'] = 'loadXmlFromFile';
	$xmlEditor['load'] = $_REQUEST['file'];
endif;
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Live XML Editor</title>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
<!--<script type="text/javascript" src="js/ext/jquery-color.js"></script>-->
<script type="text/javascript" src="xmlEditor.js"></script>
<link href="main.css" type="text/css" rel="stylesheet"/>
</head>

<body>
<script type="text/javascript">
$(document).ready(function(){
	xmlEditor.<?=$xmlEditor['func']?>('<?=$xmlEditor['load']?>', "#xml", function(){
		console.timeEnd("loadingXML");
		$("#xml").show();
		$("#actionButtons").show();																																				
		xmlEditor.renderTree();
		$("button#saveFile").show().click(function(){
			//GLR.messenger.show({msg:"Generating file...", mode:"loading"});
			$.get(
				"xmlEditor.php"
				,{
					 action		:"save"
					,xmlFileName:"<?=$_REQUEST['file']?>"
					,xmlString	:xmlEditor.getXmlAsString()
					,xmlElement	:"<?=$_REQUEST['fid']?>"
				}
				,function(data){
					alert(data);
					//$('.out').text(data);
					if (data.error){
						//GLR.messenger.show({msg:data.error,mode:"error"});
					}
					else {
						alert("Successfull!");
						//GLR.messenger.inform({msg:"Done.", mode:"success"});
						/*if (!$("button#viewFile").length){
							$("<button id='viewFile'>View Updated File</button>")
								.appendTo("#actionButtons div")
								.click(function(){ window.open(data.filename); });
						}*/
					}
				} 
				//,"json"
			);
		});
	});

	//$("#xml").html("<span style='color:#f30;'>Please upload a valid XML file.</span>").show();
});
</script>
	<div id="xml" style="display:none;"></div>
	<div id="actionButtons" style="display:none;">
		<div></div>
		<div id="nodePath"></div>
		<button id="saveFile">Save XML</button>
	</div>
	<div class="out"></div>
</body>
</html>
<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/page/section">
	<header><h1><xsl:value-of select="$_sec/@title" /></h1></header>
	
	<xsl:apply-templates select="html[@type='address']"/>
	
	<div class="map">
		<menu>
			<input type="button" value="Схема" name="bschema" onclick="onMapButton(this)" class="selected" />
			<input type="button" value="Карта" name="bymap" onclick="onMapButton(this)"/>
		</menu>
		<div id="ymap" style="width:367px; height:464px;"></div>
		<div id="schema" style="width:367px; height:464px;"><img src="images/schema.png" width="367" height="464" alt="Схема проезда" /></div>
		<div class="print"><a href="{$_sec/@id}/print/" target="_blank">напечатать схему</a></div>
	</div>
	
	<div class="contact">
		<xsl:apply-templates select="html[@type='phones']"/>
		<xsl:apply-templates select="form"/>
	</div>

<script type="text/javascript">function fid_134745022701853303813(ymaps){var map=new ymaps.Map("ymap", {center: [37.584453087310756, 54.16067734484621], zoom: 14, type: "yandex#map"});map.controls.add("zoomControl").add("mapTools").add(new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid", "yandex#publicMap"]));map.geoObjects.add(new ymaps.Placemark([37.58323, 54.158335], {balloonContent: "г. Тула, пр. Ленина, д.108, оф.218 (2-й этаж)"}, {preset: "twirl#redDotIcon"}));};</script>
<script type="text/javascript" src="http://api-maps.yandex.ru/2.0/?coordorder=longlat&amp;load=package.full&amp;wizard=constructor&amp;lang=ru-RU&amp;onload=fid_134745022701853303813"></script>
<script type="text/javascript">
function onMapButton(b){
	todo.loop(b.parentNode.getElementsByTagName('input'),function(){
		this.className='';
		todo.setClass(this,'selected',false);
		todo.get(this.name.substr(1)).style.display='none';
	});
	todo.setClass(b,'selected',true);
	todo.get(b.name.substr(1)).style.display='';
}
</script>
</xsl:template>
</xsl:stylesheet>
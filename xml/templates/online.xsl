<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/page/section">
	<xsl:apply-templates select="plan" />
	<script type="text/javascript">
		todo.onload(function(){todo.gallery('gallery');});
		<xsl:for-each select="plan">
			function openGallery<xsl:value-of select="@id" />(){todo.get('view_all_gallery_<xsl:value-of select="@id" />').getElementsByTagName('a')[0].onclick();};
		</xsl:for-each>		
	</script>
	<span class="last"><!-- // --></span>
	<span class="sky"><!-- // --></span>
	<!--<span class="ground"><xsl:comment /></span>-->
</xsl:template>

<xsl:template match="/page/section/plan">
	<article class="plan">
		<h1><xsl:value-of select="@title" /></h1>
		<div class="schema">
			<xsl:if test="@planPath"><img src="{@planPath}" alt="{@title}" /></xsl:if>
			
			<!-- Ссылки на 3д панораму (еще не предусмотрены договором и не реализованы)
			<a href="#" style="top:33px; left:47px;"><xsl:comment /></a>
			<a href="#" style="top:16px; left:198px;" class="right"><xsl:comment /></a>
			-->
		</div>
		<xsl:apply-templates select="gallery[img]" />
		<xsl:if test="position() = last()"><a href="request/" class="link-request">Задайте вопрос</a></xsl:if>
	</article>
	<span class="hr"><!-- // --></span>
</xsl:template>
<xsl:template match="/page/section/plan/gallery[img]">
	<section class="gallery">
		<h1>Фотогаллерея</h1>
		<ul>
			<xsl:apply-templates select="img[preview]" mode='show' />
		</ul>
		<xsl:if test="count(img) > 9">
			<div id="view_all_gallery_{parent::plan/@id}"><xsl:apply-templates select="img[preview]" mode="view_all"/></div>
			<a href="javascript:openGallery{parent::plan/@id}()" class="link-all-photos">смотреть все фотографии</a>
		</xsl:if>
	</section>
</xsl:template>
<xsl:template match="/page/section/plan/gallery/img[preview]" mode="show">
	<xsl:if test="position() &lt;= 9">
		<li>
			<a href="{@src}" rel="gallery[{parent::gallery/parent::plan/@id}]">
				<img src="{preview/@src}" alt="{parent::gallery/parent::plan/@title}" width="{preview/@width}" height="{preview/@height}" />
			</a>
		</li>
	</xsl:if>
</xsl:template>
<xsl:template match="/page/section/plan/gallery/img" mode="view_all">
	<a href="{@src}" rel="gallery[{parent::gallery/parent::plan/@id}all]" style="display:none;"><xsl:comment/></a>
</xsl:template>
</xsl:stylesheet>

<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/page/section">
	<header><h1><xsl:value-of select="$_sec/@title" /></h1></header>
	<xsl:apply-templates/>
</xsl:template>

<xsl:template match="personnel">
	<section class="collegi">
		<header>
			<h1>Сотрудники</h1>
		</header>
		<div>
			<xsl:apply-templates select="row"/>
		</div>
	</section>
</xsl:template>

<xsl:template match="personnel/row">
	<figure>
		<xsl:apply-templates select="img/preview"/>
		<figcaption>
			<xsl:apply-templates select="announce"/>
			<xsl:apply-templates select="title"/>
		</figcaption>
	</figure>
	<xsl:if test="(count(preceding-sibling::row) + 1) mod 3 = 0">
		<br class="clear"/>
	</xsl:if>
</xsl:template>

<xsl:template match="personnel/row/title">
	<xsl:variable name="space"><xsl:text> </xsl:text></xsl:variable>
	<div class="name">
		<xsl:choose>
			<xsl:when test="contains(text(),$space)">
				<span><xsl:value-of select="substring-before(text(),$space)"/></span>
				<xsl:value-of select="substring-after(text(),$space)"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="text()"/>
			</xsl:otherwise>
		</xsl:choose>
	</div>
</xsl:template>

<xsl:template match="personnel/row/announce">
	<div class="job"><xsl:value-of select="text()"/></div>
</xsl:template>

<xsl:template match="personnel/row/img/preview">
	<img src="{@src}" alt="{ancestor::row/title/text()}" width="{@width}" height="{@height}"/>
</xsl:template>

</xsl:stylesheet>
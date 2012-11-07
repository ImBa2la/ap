<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:exsl="http://exslt.org/common">

<xsl:output media-type="text/html" method="html" omit-xml-declaration="yes" indent="no" encoding="utf-8"/>
<!-- Текущий раздел -->
<xsl:variable name="_sec" select="/page/structure//sec[@selected='selected']"/>
<!-- Базовый URL -->
<xsl:variable name="_base_url">http://<xsl:value-of select="/page/@host" />/</xsl:variable>

<!-- Заголовочные тэги -->
<xsl:template name="head">
	<title>
		<xsl:choose>
			<xsl:when test="/page/meta[@name='title']/text()">
				<xsl:value-of select="/page/meta[@name='title']/text()" />
				<xsl:text> - </xsl:text>
				<xsl:value-of select="/page/site/@name"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$_sec/@title"/>
				<xsl:text> - </xsl:text>
				<xsl:value-of select="/page/site/@name"/>
			</xsl:otherwise>
		</xsl:choose>
	</title>
	
	<meta name="keywords">
		<xsl:attribute name="content">
			<xsl:choose>
				<xsl:when test="/page/meta[@name='keywords']/text()">
					<xsl:value-of select="/page/meta[@name='keywords']/text()" />
				</xsl:when>
				<xsl:otherwise><!-- default keywords --></xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
	</meta>
	
	<meta name="description">
		<xsl:attribute name="content">
			<xsl:choose>
				<xsl:when test="/page/meta[@name='description']/text()">
					<meta name="description" content="{/page/meta[@name='description']/text()}" />
				</xsl:when>
				<xsl:otherwise><!-- default description --></xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
	</meta>
</xsl:template>

<!-- Остальное -->
<xsl:template match="html">
	<xsl:if test="not(//form/final/text())"><xsl:value-of select="text()"  disable-output-escaping="yes" /></xsl:if>
</xsl:template>

</xsl:stylesheet>
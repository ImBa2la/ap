<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/page/section">
	<article class="content">
		<h1><xsl:value-of select="$_sec/@title"/></h1>
		<xsl:choose>
			<xsl:when test="final"><xsl:apply-templates select="final" /></xsl:when>
			<xsl:otherwise><xsl:apply-templates select="html" /><xsl:apply-templates select="form" /></xsl:otherwise>
		</xsl:choose>
		</article>
</xsl:template>
</xsl:stylesheet>
<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/page/section">
	<header><h1><xsl:value-of select="$_sec/@title" /></h1></header>
	<xsl:apply-templates/>
</xsl:template>
</xsl:stylesheet>
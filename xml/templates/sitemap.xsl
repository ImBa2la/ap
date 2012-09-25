<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/page/section">
	<header><h1><xsl:value-of select="$_sec/@title" /></h1></header>
	<xsl:apply-templates select="/page/structure" mode="sitemap"/>
</xsl:template>

<xsl:template match="/page/structure" mode="sitemap">
	<nav>
		<ul>
			<xsl:apply-templates select="sec" mode="sitemap"/>
		</ul>
	</nav>
</xsl:template>

<xsl:template match="/page/structure//sec | /page/structure//item" mode="sitemap">
	<li>
		<a>
			<xsl:attribute name="href">
				<xsl:choose>
					<xsl:when test="@id = 'home'">
						<xsl:value-of select="$_base_url"/>
					</xsl:when>
					<xsl:when test="name() = 'item'">
						<xsl:value-of select="../@id"/>
						<xsl:text>/row</xsl:text><xsl:value-of select="@id"/><xsl:text>/</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@id"/>
						<xsl:text>/</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<xsl:value-of select="@title"/>
			
		</a>
		<xsl:if test="sec | item">
			<ul><xsl:apply-templates select="sec | item" mode="sitemap" /></ul>
		</xsl:if>
	</li>
</xsl:template>

</xsl:stylesheet>
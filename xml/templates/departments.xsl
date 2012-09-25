<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="vacancies">
	<div class="columns">
		<ul id="vacancies">
			<xsl:apply-templates select="department"/>
		</ul>
	</div>
	<script type="text/javascript">listColumns(todo.get('vacancies'),4,6);</script>
</xsl:template>

<xsl:template match="vacancies/department">
	<li>
		<h2><a href="{@id}/"><xsl:value-of select="@title"/></a></h2>
		<xsl:apply-templates select="vacancy"/>
	</li>
</xsl:template>

<xsl:template match="vacancies/department/vacancy">
	<p>
		<xsl:value-of select="title"/>
		<xsl:apply-templates select="salary"/>
	</p>
</xsl:template>

<xsl:template match="vacancy/salary">
	<xsl:if test="min or max">
		<br/>
		<xsl:if test="min">
			<xsl:text>от </xsl:text>
			<xsl:value-of select="min"/>
		</xsl:if>
		<xsl:if test="max">
			<xsl:if test="min">
				<xsl:text> … </xsl:text>
			</xsl:if>
			<xsl:text>до </xsl:text>
			<xsl:value-of select="max"/>
		</xsl:if>
		<xsl:text> руб/мес</xsl:text>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>
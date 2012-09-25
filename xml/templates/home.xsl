<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/page/section">
	<header class="home">
		<h1>Вакансии</h1>
		<time datetime="{vacancies/@datetime}"><xsl:value-of select="vacancies/@date"/></time>
	</header>
	<xsl:apply-templates select="vacancies"/>
	
	<div class="coloms-two">
		<section>
			<h1>Работодателям</h1>
			<xsl:apply-templates select="html[@type='employers']"/>
		</section>
		<section>
			<h1>Соискателям</h1>
			<xsl:apply-templates select="html[@type='applicants']"/>
		</section>
	</div>

</xsl:template>

</xsl:stylesheet>
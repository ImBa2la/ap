<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="page_navigator.xsl"/>

<xsl:template match="/page/section">
	<xsl:apply-templates/>
</xsl:template>

<!-- подробно -->
<xsl:template match="section/vacancy">
	<header>
		<h1><xsl:value-of select="title"/></h1>
	</header>
	<dl class="vacancy">
		<dt>Отрасль/специализации</dt>
		<dd><xsl:value-of select="$_sec/@title"/></dd>
		<xsl:apply-templates/>
	</dl>
	<div class="print"><a href="{$_sec/@id}/row{@id}/print/" target="_blank">напечатать</a></div>
	<div class="clickmy"><a href="cv_form/">откликнуться</a></div>
	<div class="backpage"><a href="{$_sec/@id}/">к списку вакансий</a></div>
</xsl:template>

<xsl:template match="section/vacancy/title">
	<dt>Должность</dt>
	<dd><xsl:value-of select="text()"/></dd>
</xsl:template>

<xsl:template match="section/vacancy/company">
	<dt>Название компании</dt>
	<dd><xsl:value-of select="text()"/></dd>
</xsl:template>

<xsl:template match="section/vacancy/city">
	<dt>Город</dt>
	<dd><xsl:value-of select="text()"/></dd>
</xsl:template>

<xsl:template match="section/vacancy/responsibility">
	<dt>Обязанности</dt>
	<dd><xsl:value-of select="text()" disable-output-escaping="yes"/></dd>
</xsl:template>

<xsl:template match="section/vacancy/requirements">
	<dt>Требования</dt>
	<dd><xsl:value-of select="text()" disable-output-escaping="yes"/></dd>
</xsl:template>

<xsl:template match="section/vacancy/desc">
	<dt>Дополнительная информация</dt>
	<dd><xsl:value-of select="text()" disable-output-escaping="yes"/></dd>
</xsl:template>

<xsl:template match="section/vacancy/contact/person">
	<dt>Контактное лицо</dt>
	<dd><xsl:value-of select="text()"/></dd>
</xsl:template>

<xsl:template match="section/vacancy/contact/phone">
	<dt>Телефон</dt>
	<dd><xsl:value-of select="text()"/></dd>
</xsl:template>

<xsl:template match="section/vacancy/contact/email">
	<dt>Электронная почта</dt>
	<dd><a href="mailto:{text()}"><xsl:value-of select="text()"/></a></dd>
</xsl:template>

<xsl:template match="section/vacancy/salary">
	<dt>Зарплата</dt>
	<dd>
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
	</dd>
</xsl:template>



<!-- список -->
<xsl:template match="vacancies">
	<header><h1><xsl:value-of select="$_sec/@title" /></h1></header>
	<table>
		<thead>
			<tr>
				<th scope="col" rowspan="2">дата</th>
				<th scope="col" rowspan="2">вакансии</th>
				<th scope="col" rowspan="2">компания</th>
				<th scope="col" rowspan="2">регион</th>
				<th scope="col" colspan="2">уровень оплаты<br/>(руб./мес.)</th>
			</tr>
			<tr>
				<th scope="col">мин.</th>
				<th scope="col">макс.</th>
			</tr>
		</thead>
		<tbody>
			<xsl:apply-templates select="vacancy"/>
		</tbody>
	</table>
	<xsl:call-template name="page_navigator">
		<xsl:with-param name="numpages" select="number(@pages)"/>
		<xsl:with-param name="page" select="number(@page)"/>
		<xsl:with-param name="url">
			<xsl:value-of select="$_sec/@id"/><xsl:text>/</xsl:text>
		</xsl:with-param>
		<xsl:with-param name="pageparam" select="@pageParam"/>
	</xsl:call-template>
	<div class="backpage"><a href="list_of_vacancies/">к списку отраслей</a></div>
</xsl:template>

<xsl:template match="vacancies/vacancy">
	<tr>
		<td class="center"><xsl:value-of select="@date"/></td>
		<td><a href="{$_sec/@id}/row{@id}/"><xsl:value-of select="title"/></a></td>
		<td><xsl:value-of select="company"/></td>
		<td><xsl:value-of select="city"/></td>
		<td class="right"><xsl:choose>
			<xsl:when test="salary/min">
				<xsl:value-of select="salary/min"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>&#8211;</xsl:text>
			</xsl:otherwise>
		</xsl:choose></td>
		<td class="right"><xsl:choose>
			<xsl:when test="salary/max">
				<xsl:value-of select="salary/max"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>&#8211;</xsl:text>
			</xsl:otherwise>
		</xsl:choose></td>
	</tr>
</xsl:template>

</xsl:stylesheet>
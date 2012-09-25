<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
	<!ENTITY copy   "&#169;">
	<!ENTITY reg    "&#174;">
	<!ENTITY trade  "&#8482;">
	<!ENTITY mdash  "&#8212;">
	<!ENTITY laquo  "&#171;">
	<!ENTITY raquo  "&#187;">
	<!ENTITY ldquo  "&#8220;">
	<!ENTITY rdquo  "&#8221;"> 
	<!ENTITY pound  "&#163;">
	<!ENTITY yen    "&#165;">
	<!ENTITY euro   "&#8364;">
]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="elements.xsl"/>
<xsl:output media-type="text/html" method="html" omit-xml-declaration="yes" indent="no" encoding="utf-8"/>

<!-- Шаблон страницы -->
<xsl:template match="/">
<xsl:text disable-output-escaping="yes">&lt;!DOCTYPE HTML&gt;</xsl:text>
<html>
<head>
<base href="{$_base_url}"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="shortcut icon" href="favicon.ico"/>
<xsl:call-template name="head"/>
<link href="css/default.css" rel="stylesheet" type="text/css" />
<link href="css/other.css" rel="stylesheet" type="text/css"/>
<xsl:text disable-output-escaping="yes">&lt;!--[if lte IE 8]&gt;</xsl:text>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<xsl:text disable-output-escaping="yes">&lt;![endif]--&gt;</xsl:text>
<script type="text/javascript" src="js/todo.js"></script>
<xsl:apply-templates select="/page/structure//sec[@id='list_of_vacancies']" mode="subscribe" />
<script type="text/javascript" src="js/podpiska.js"></script>
<script type="text/javascript" src="js/default.js"></script>
<script type="text/javascript" src="js/columns.js"></script>
</head>
<body>
<div class="conteiner">
	<div class="head">
		<header>
			<h1><a href="{$_base_url}"><img src="images/logo.png" width="264" height="30" alt="{/page/site/@name}"/></a></h1>
			<address>
				<a href="mailto:info@loes.ru" class="email">Написать письмо</a>
				<p class="phones">+7(4872)35-26-35<br/>
					+7(4872)23-55-92</p>
			</address>
		</header>
		<xsl:apply-templates select="/page/structure" mode="menu"/>
		<div class="s1"></div>
	</div>
	<div class="content">
		<article>
			<xsl:apply-templates select="/page/section"/>
		</article>
		<div class="sidebar">
			<nav>
				<xsl:apply-templates select="$_sec" mode="sidebar"/>
				<h2>Работодателю:</h2>
				<ul class="pod-menu pod-menu-color-1">
					<li><a href="ticket_form/">Отправить заявку</a></li>
				</ul>
				<h2>Соискателям:</h2>
				<ul class="pod-menu pod-menu-color-2">
					<li><a href="javascript:pd.open()">Подписаться на вакансии</a></li>
					<li><a href="cv_form/">Отправить резюме</a></li>
				</ul>
			</nav>
		</div>
	</div>
</div>
<footer>
		<div class="s2"></div>
		<xsl:apply-templates select="/page/structure" mode="menu">
			<xsl:with-param name="footer" select="1" />
		</xsl:apply-templates>
		<address><span>300026</span><br/>
			г. Тула, пр. Ленина,<br/>
			д. 108, оф. 218.</address>
		<small>Copyright © 2012 by The Loes-consulting Company. All rights reserved.<br/>
			Разработка и продвижение сайта <a href="http://forumedia.ru/" target="_blank">Forumedia</a></small>
	</footer>

</body>	
</html>
</xsl:template>

<!-- js variable for ajax subscribe -->
<xsl:template match="/page/structure//sec[@id='list_of_vacancies']" mode="subscribe">
	<script type="text/javascript">
		window.cvList = '<xsl:apply-templates select="sec" mode="subscribe" />';
	</script>
</xsl:template>

<xsl:template match="/page/structure//sec[@id='list_of_vacancies']/sec" mode="subscribe">
	<xsl:variable name="fid">
		<xsl:text>list</xsl:text><xsl:value-of select="position()"/>
	</xsl:variable>
	<li>
		<input type="checkbox" name="list[]" value="{@id}" id="{$fid}" />
		<label for="{$fid}"><xsl:value-of select="@title"/></label>
	</li>
</xsl:template>

<xsl:template match="/page/structure" mode="menu">
	<xsl:param name="footer" />
	<nav>
		<ul>
			<xsl:apply-templates select="sec[contains(@class,'menu')]" mode="menu"/>
			<xsl:if test="$footer = 1"><li>
				<xsl:if test="$_sec/ancestor-or-self::sec/@id = 'sitemap'">
					<xsl:attribute name="class">selected</xsl:attribute>
				</xsl:if>
				<a href="sitemap/">Карта сайта</a>
			</li></xsl:if>
		</ul>
	</nav>
</xsl:template>

<xsl:template match="/page/structure//sec" mode="menu">
	<li>
		<xsl:if test="$_sec/ancestor-or-self::sec/@id = @id">
			<xsl:attribute name="class">selected</xsl:attribute>
		</xsl:if>
		<a>
			<xsl:attribute name="href">
				<xsl:choose>
					<xsl:when test="@id = 'home'">
						<xsl:value-of select="$_base_url"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@id"/>
						<xsl:text>/</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<xsl:value-of select="@title"/>
		</a>
		<xsl:if test="sec and parent::structure">
			<ul><xsl:apply-templates select="sec" mode="menu"/></ul>
		</xsl:if>
	</li>
</xsl:template>

<xsl:template match="/page/structure//sec" mode="sidebar">
	<xsl:variable name="ns" select="ancestor-or-self::sec[parent::structure]/sec"/>
	<xsl:if test="count($ns)">
		<ul class="top-menu"><xsl:apply-templates select="$ns" mode="sidebar_item"/></ul>
	</xsl:if>
</xsl:template>

<xsl:template match="/page/structure//sec[@id='home']" mode="sidebar" priority="1">
	<ul class="top-menu">
		<xsl:apply-templates select="/page/structure/sec[@id='applicants']/sec" mode="sidebar_item"/>
	</ul>
</xsl:template>

<xsl:template match="/page/structure//sec" mode="sidebar_item">
	<li>
		<xsl:if test="$_sec/ancestor-or-self::sec/@id = @id">
			<xsl:attribute name="class">selected</xsl:attribute>
		</xsl:if>
		<a href="{@id}/"><xsl:value-of select="@title"/></a>
	</li>
</xsl:template>

</xsl:stylesheet>
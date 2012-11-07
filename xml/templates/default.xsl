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
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:exsl="http://exslt.org/common">
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
<xsl:text disable-output-escaping="yes">&lt;!--[if lte IE 8]&gt;</xsl:text>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<xsl:text disable-output-escaping="yes">&lt;![endif]--&gt;</xsl:text>
<script type="text/javascript" src="js/todo.js"></script>
<script type="text/javascript" src="js/default.js"></script>
</head>
<body>
	<header>
		<h1><a href="{$_base_url}"><img src="images/logo.png" width="264" height="30" alt="{/page/site/@name}"/></a></h1>
	</header>
	<article>
		<xsl:apply-templates select="/page/section"/>
	</article>
	<footer>
	</footer>

</body>	
</html>
</xsl:template>

</xsl:stylesheet>
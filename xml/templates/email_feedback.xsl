<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
	<!ENTITY copy   "&#169;">
	<!ENTITY reg    "&#174;">
	<!ENTITY trade  "&#8482;">
	<!ENTITY mdash  "&#8212;">
	<!ENTITY ldquo  "&#8220;">
	<!ENTITY rdquo  "&#8221;"> 
	<!ENTITY pound  "&#163;">
	<!ENTITY yen    "&#165;">
	<!ENTITY euro   "&#8364;">
]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output media-type="text/html" method="html" omit-xml-declaration="yes" indent="yes" encoding="utf-8"/>
<xsl:decimal-format name="rur" decimal-separator="," grouping-separator="."/>

<!-- Шаблон письма -->
<xsl:template match="/">
<html>
<head>
	<title>Вопрос с сайта <xsl:value-of select="/email/@name" /></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
	*{padding:0;margin:0;}
	table {border-spacing:0 0; border-collapce:collapse; border:0;}
	table td, table th {border:0; vertical-align:middle;}
	table th p, table td p, table th span, table td span {margin:0; padding:0;}
	a{color: #557186; text-decoration:underline;}
	a:hover{color:#C2AE8B;}
	.t td a			{color:#846F54;text-decoration:underline;}
	.t td a:hover	{text-decoration:none;}
	a img			{border:0;}
	</style>
</head>
<body style="font-size:14px; font-family:Arial, Helvetica, sans-serif;color:#000; width:100%;height:100%; padding:0;margin:0;">
	<xsl:apply-templates />
</body>
</html>
</xsl:template>
<xsl:template match="/email">
	<div style="height:20px; background-color:#7B909E;width:100%;border-bottom:5px solid #ECE6DC;"></div>
	<table width="100%" border="0" style="border-bottom:1px solid #cccccc;">
		<thead>
			<tr>
				<td style="border:0; vertical-align:middle;" width="5%"><p style="margin:0; padding:0;"><xsl:call-template name="nbsp" /></p></td>
				<td style="border:0; vertical-align:middle;" width="45%"><p style="margin:0; padding:0;"><a style="color:#846F54;text-decoration:underline;"  href="http://{@domain}/"><img style="margin-top:10px; margin-left: 50px;" src="http://{@domain}/images/logo_letter.png" alt="КА Лоэс Консалтинг" border="0" width="207" height="24" /></a></p></td>
				<td style="border:0; vertical-align:middle; text-align:right; padding-right:50px;" width="45%">
					<p style="margin:0; padding:0;"><a style="color:#846F54;text-decoration:underline;"  href="http://{@domain}/">
						<xsl:value-of select="@domain" />
					</a></p>
				</td>
				<td style="border:0; vertical-align:middle;" width="5%"><p style="margin:0; padding:0;"><xsl:call-template name="nbsp" /></p></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="border:0; vertical-align:middle;"><p style="margin:0; padding:0;"><xsl:call-template name="nbsp" /></p></td>
				<td colspan="4"><table width="100%" style="width:100%;border-collapse:collapse;border-width:1px; border-style:solid; border-color:#fff; margin-bottom:20px;"><tbody>
					<caption><p style="margin:0; padding:0;"><h1>Новое сообщение с сайта</h1></p></caption>
					<xsl:apply-templates />
				</tbody></table></td>
				<td style="border:0; vertical-align:middle;"><p style="margin:0; padding:0;"><xsl:call-template name="nbsp" /></p></td>
			</tr>
			<tr>
				<td style="border:0; vertical-align:middle;"><p style="margin:0; padding:0;"><xsl:call-template name="nbsp" /></p></td>
				<td style="border:0; vertical-align:middle;">
					<p style="padding-bottom:10px;">С уважением</p>
					<p style="padding-bottom:10px;"><xsl:value-of select="@name" /><br/>300025 г. Тула, пр. Ленина, д, 108, оф 218<br/>E-mail: <a style="color:#846F54;text-decoration:underline;"  href="mailto:{@email}"><xsl:value-of select="@email" /></a><br/>+7(4872)23-55-92<br/>+7(4872)35-05-25</p>
				</td>
				<td  colspan="2" style="border:0; vertical-align:middle;"><p style="margin:0; padding:0;"><xsl:call-template name="nbsp" /></p></td>
			</tr>
		</tbody>
	</table>
</xsl:template>

<xsl:template match="/email/field">
	<tr>
		<th width="30%"><p style="margin:0; padding:0;"><xsl:value-of select="@label" /></p></th>
		<td width="70%"><p style="margin:0; padding:0;"><xsl:value-of select="text()" disable-output-escaping="yes" /></p></td>
	</tr>
</xsl:template>

<xsl:template name="nbsp">
	<xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
</xsl:template>
</xsl:stylesheet>
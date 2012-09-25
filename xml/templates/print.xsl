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
<xsl:text disable-output-escaping="yes">&lt;!--[if lte IE 8]&gt;</xsl:text>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<xsl:text disable-output-escaping="yes">&lt;![endif]--&gt;</xsl:text>
<style type="text/css">
*{padding:0;margin:0;}
body,html{width:100%;height:100%;}
body{font:14px Arial, Helvetica, sans-serif;color:#000;}
a{color: #557186;text-decoration:underline;}
a:hover{color:#C2AE8B;}
p{padding-bottom:10px;}
ol,ul {padding:0 0 10px 20px; overflow:auto;}
li {padding-bottom:5px;line-height:16px;}
ol ol,ul ul,ol ul,ul ol{overflow:auto;}
article table{width:100%;border-collapse:collapse;border:1px solid #fff;margin-bottom:20px;}
article table th{color:#fff;background:#CEBEA2;border:2px solid #fff;font-weight:100;font-size:12px;}
article table td{color:#846F54;background:#FBF7EE;border:2px solid #fff;padding:2px 4px;font-size:12px;}
article table td a{color:#846F54;text-decoration:underline;}
article table td a:hover{text-decoration:none;}
article table td.center{text-align:center;}
article table td.right{text-align:right;}
a img{border:0;}
form{display:none;}
</style>
</head>
<body>
<table width="100%" border="0">
	<thead>
		<tr>
			<td colspan="4"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
		</tr>
		<tr>
			<td width="5%"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
			<td width="45%"><a href="http://loes.ru"><img class="logo" src="data:;base64,iVBORw0KGgoAAAANSUhEUgAAAM8AAAAYCAMAAABqZFn6AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABhQTFRF////lqi2c4ydtsLMX3uP6u3w0dnf3+XpvvercgAAA9RJREFUeNrMWImOHCkMxRf8/x8H4wNDVTKtzkba0qgFFM/2wxc1rf33DzL3JszYBBuSzVkaN2BmmosyZ2h/+g4Yx4JR2+O+ZDE0E7VWJkBciv52miM4gP/iYelqclNLCH0OQMu0ZhZJy782X3T2bTk2UX0yMVFrhSUk6MbmyxVYDpW+sR04cLLPtPBJfcJAwafzwQcnDHU4bcqxCSVwCg8+2AufDYynq0//ig/miD3eQH/I53NnCz6TkOhq4cNrG5Xxsm36l5wPO+aFTwEWu1j+hs8mZkfp/gkfzA2QfKAd/oESb3DE26TO+O4fuOINjnjTYPkq4IIGqTsidMVN0Xow9aw5oOlpqxjseiAmJGtAjmmFm9quIFvReoAmZdUD01WB/hDjdwHnfHqh8394gAd+FXDGZ3yZff/qkWkO1IAbWtkZehSZPVkLGp1MPfjMaC10DmxDIC841B/oJmtCsuPEa+RS6Q9NkUiRF/IiFPdWF0NHhVOLiPx9TsDUqidxcbSoJaUj7RW7VEUBhQe646ziNdIh4pb08Z2kMrPqyItQTa6isy3W2T8UrgaKZSVxJC1E6fCUGbaKtTOf2IfqG02PHraPRgxCI/kM/i0fUz58GWqJkqjdfUkfXjfckn2EmHFBJXIP7EP1hQa3ErIL5qEmHzMtDuePfNLL1EpSp87isgSrwePsW7UYPLCn6gsdDSswoGUWCh88+BB/xEfQfBzWY8mr6+gtns76hlx7z409VdNbIUy79XpUcvH2j/BnfChCwN/uM4yVrDZyNE6PfSgR98DirlPQbrRlfhYENQR2SNx8UFM1+BShNx90CZ5tsq8+cZx9lRv8DZ8lfpzpk1grPUTwxkfIrcKM+9KZLz5kXw3yEHrxGelhTLfK6Z98/c5HD8JkPLGPeIN23rKAolPI2rr7+pk/qyLiB/EGqWIf6bizO2195g9ZokJwPrF/yh+djW03RZmlF/8AWsj9yEcwA3Zw9JTHN0CyHedFHLKI284H9lR9on1z8DFDdvU++OTp/MSHioZooC6n1+5WDKQHnxU5vb1gL9UH2guq8+nxzcP7a6Hykc/4YAkp4gjhUrjJOJGLpGh5A/v2R6bQXfQv1Qcasq1gifsMuJ0/nbe0H/j0u8V0M27eMiFMmKoFuF4VKa6OGV+RQif2qbqilchiZf8lYDo/lc/8aZ/xgTsdwC+N+x7j3Qd78WK83fkSKXRiH6pvdN6Md9yHiysfj/uf+cwqXspVj+nqOKXnEfWjB5Z9PXpJDA9sDGVL2GjfOvSnJ2bORkiMT5Cer+RF6Nb3S4ABAP78HqG5LRD6AAAAAElFTkSuQmCC" width="207" height="24"/></a></td>
			<td width="45%" style="text-align:right;"><p>Рекрутинговое агентство ЛоэсКонсалтинг<br/>300025 г. Тула, пр. Ленина, д, 108, оф 218<br/>E-mail: <a href="mailto:consult@loes.ru">consult@loes.ru</a><br/>+7(4872)23-55-92<br/>+7(4872)35-05-25</p><p><a href="http://loes.ru">www.loes.ru</a></p></td>
			<td width="5%"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
			<td colspan="2" style="border-bottom:3px solid #5F7B8F;"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
			<td><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
		</tr>
		<tr>
			<td><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
			<td colspan="2">
				<article>
					<xsl:apply-templates select="/page/section"/>
				</article>
			</td>
			<td><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
		</tr>
	</tbody>
</table>
<script type="text/javascript">
print();
</script>
</body>	
</html>
</xsl:template>

</xsl:stylesheet>
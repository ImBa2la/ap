<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8"/>

<xsl:template match="/page/section/subscribers" priority="1">
	<table class="rows">
		<th>№</th>
		<th class="cntr">E-mail</th>
		<th class="cntr">Состояние</th>
		<xsl:apply-templates />
	</table>
<script type="text/javascript">
todo.onload(function(){
	var data = {
		<xsl:for-each select="row"><xsl:value-of select="@id" />:{
		<xsl:value-of select="id"/>
			email:"<xsl:value-of select="@email"/>",
			string:"<xsl:for-each select="subscribe"><xsl:value-of select="@id" /><xsl:if test="not(position() = last())">,</xsl:if></xsl:for-each>",
			subs :{<xsl:for-each select="subscribe"><xsl:value-of select="position()" />:"<xsl:value-of select="@id" />"<xsl:if test="not(position() = last())">,</xsl:if></xsl:for-each>}
		}<xsl:if test="not(position() = last())">,</xsl:if>
		</xsl:for-each>
	}
	for(p in data){
		todo.ajax(
			 '../classes/ajax.php'
			,function(o){return function(text,xml){
				console.log(text);
				var json = eval('('+text+')');
				if(json.code == 0){
					var tr = todo.get('uid'+json.uid),td = null,a=null;
					if((td = tr.children[2]) &amp;&amp; (td.tagName.toLowerCase() == 'td')
						&amp;&amp; (td.className == 'switch') &amp;&amp; (a = td.children[0])
						&amp;&amp; (a.tagName.toLowerCase() == 'a')
					){//magic number - 3rd col
						a.className = 'on'
					}
				}
			};}(this)
			,{
				'ap':'ap',
				'section':'<xsl:value-of select="$_sec/@id"/>',
				'md':'<xsl:value-of select="/page/section/@module"/>',
				'email':data[p].email,
				'categories':data[p].string,
				'uid':p
			});
	}
});
</script>
</xsl:template>
<xsl:template match="/page/section/subscribers/row" priority="1">
	<tr id="uid{@id}">
		<td><xsl:value-of select="position()" /></td>
		<td class="cntr"><xsl:value-of select="@email" /></td>
		<td class="switch"><a href="#" class="off"></a></td>
	</tr>
</xsl:template>

</xsl:stylesheet>
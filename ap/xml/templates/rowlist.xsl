<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8"/>
<xsl:include href="pagination.xsl"/>

<xsl:template match="rowlist">
	<xsl:if test="@title">
		<h1><xsl:value-of select="@title"/></h1>
	</xsl:if>
	<xsl:variable name="id" select="generate-id()"/>
	<xsl:variable name="url">
		<xsl:choose>
				<xsl:when test="@sortUri"><xsl:value-of select="@sortUri" /></xsl:when>
				<xsl:otherwise>
					<xsl:text>?id=</xsl:text>
					<xsl:value-of select="$_sec/@id"/>
					<xsl:text disable-output-escaping="yes">&amp;md=</xsl:text>
					<xsl:value-of select="/page/tabs/tab[@selected='selected']/@id"/>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="headers/h[@sort='asc' or @sort='desc']">
				<xsl:text disable-output-escaping="yes">&amp;order[]=</xsl:text>
				<xsl:value-of select="headers/h[@sort='asc' or @sort='desc']/@name"/>
				<xsl:text disable-output-escaping="yes">&amp;order[]=</xsl:text>
				<xsl:value-of select="headers/h[@sort='asc' or @sort='desc']/@sort"/>
			</xsl:if>
	</xsl:variable>
	<script type="text/javascript" src="js/dd.target.js"></script>
	<script type="text/javascript" src="js/dd.object.js"></script>
	<script type="text/javascript" src="js/dd.master.js"></script>
	<script type="text/javascript"><xsl:text disable-output-escaping="yes">
todo.onload(function(){
	var  tb = document.getElementsByTagName('table').item(0)
		,dt = todo.get('sidemenu').getElementsByTagName('dt');
	if(tb.className.toLowerCase() == 'rows'){
		var tr = tb.getElementsByTagName('tr');
		for(var i = 1; i &lt; tr.length; i++) new DragObject(tr.item(i),onChange);
	}
	for(var j = 0; j &lt; dt.length; j++) {
		new DropTarget(dt.item(j),onChange,handlerEnter,handlerLeave);
	}
});
function handlerEnter(){this._.style.backgroundColor = (this._.accept == 'accept')?'#b3e9b3':'#e9b3b3';}
function handlerLeave(){this._.style.backgroundColor = '';}
function onChange(o,t,act){
	var obj = {};
	if((obj.row = parseInt(o._.firstChild.firstChild.value))
		&amp;&amp; (obj.target = t._.attributes['section'].value)
	){
		todo.ajax(window.location.href,function(){return function(data){
			if(data &amp;&amp; (data.substr(0,1) == '{')){
				var  json = eval('('+ data +')');
				if(!json.error){
					switch(json.code){
						case 0 : 
							window.location.href="?id="+json.responce.id+"&amp;md="+json.responce.md; 
							break;
						case 10:
							t._.accept = 'accept';
							break;
					}
					
				}else{}
			}
		};}(),{'target':obj.target,'row':obj.row,'action':'change','onDragSuccess':(act)?1:0},'POST',true);
	}else{
		console.log('Wrong params');
	}
	return false;	
}
function onDel(){return confirm('Подтвердите удаление');};
function onMove(a){var p=parseInt(prompt('Введите новую позицию'));if(p){if(isNaN(p)||p&lt;=0)alert('Позиция должна быть целым числом больше ноля');else{a.href=a.href+'&amp;pos='+p;return true}};return false;};
function onSwitch(a){
	if(!a._ajaxFlag){
		a._ajaxFlag=true;
		todo.ajax(a.href,function(text,xml){try{
			var val=xml.getElementsByTagName('value')[0],mess=xml.getElementsByTagName('message')[0];
			if(mess)alert(mess.firstChild.data);
			a.className=val.firstChild.data;
			a._ajaxFlag=false;
		}catch(er){}},{'ajax':1,'active':a.className,'xx':(new Date).getSeconds()});
	};
	return false;
};
function onMultiDel(e){
	if(!e.form.getElementsByTagName('table')[0]._getSelectedItems().length){
		alert('Ничего не выбрано');
		return;
	};
	if(confirm('Подтвердите удаление')){
		e.form.action.value='delete';
		e.form.submit();
	};
};</xsl:text>
	</script>
	<xsl:apply-templates select="filter">
		<xsl:with-param name="url" select="$url" />
	</xsl:apply-templates>
	
	<form id="{$id}" action="{@uri}#{$id}" method="post">
		<xsl:call-template name="page_navigator">
			<xsl:with-param name="numpages" select="number(@numPages)"/>
			<xsl:with-param name="page" select="number(@curPage)"/>
			<xsl:with-param name="url" select="$url" />
		</xsl:call-template>
		<table class="rows">
			<xsl:apply-templates select="headers"/>
			<xsl:apply-templates select="row"/>
		</table>
		<xsl:call-template name="page_navigator">
			<xsl:with-param name="numpages" select="number(@numPages)"/>
			<xsl:with-param name="page" select="number(@curPage)"/>
			<xsl:with-param name="url" select="$url" />
		</xsl:call-template>
		<xsl:apply-templates select="totals" />
		<input type="hidden" name="action" value=""/>
		<xsl:apply-templates select="actions/action"/>
		<xsl:if test="@add">
			<input type="submit" value="Добавить" class="add" onclick="this.form.action.value='new';"/>
		</xsl:if>
		<xsl:if test="@delete">
			<input type="button" value="Удалить выбранное" class="del" onclick="onMultiDel(this)"/>
		</xsl:if>
	</form>
</xsl:template>

<!-- Ячейки -->
<xsl:template match="rowlist/actions/action">
	<input value="{@title}" onclick="this.form.action.value='{@name}';">
		<xsl:if test="@class"><xsl:attribute name="class"><xsl:value-of select="@class"/></xsl:attribute></xsl:if>
		<xsl:attribute name="type">
			<xsl:choose>
				<xsl:when test="@type"><xsl:value-of select="@type"/></xsl:when>
				<xsl:otherwise>submit</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
	</input>
</xsl:template>
<xsl:template match="rowlist/actions/action[@name='delete']" priority="1">
	<input type="button" value="Удалить выбранное" class="del" onclick="onMultiDel(this)"/>
</xsl:template>

<!-- Заголовки -->
<xsl:template match="rowlist/headers">
	<tr>
		<xsl:if test="not(parent::rowlist/@nocheckbox)">
			<th scope="col" class="cntr"><input class="select_all_rows" type="checkbox"/></th>
		</xsl:if>
		<xsl:apply-templates/>
		<th scope="col"></th>
	</tr>
</xsl:template>
<xsl:template match="rowlist/headers/h">
	<th scope="col">
		<xsl:if test="@class"><xsl:attribute name="class"><xsl:value-of select="@class"/></xsl:attribute></xsl:if>
		<xsl:value-of select="text()"/>
	</th>
</xsl:template>
<xsl:template match="rowlist/headers/h[@sort='asc']" priority="1">
	<th scope="col">
		<xsl:if test="@class"><xsl:attribute name="class"><xsl:value-of select="@class"/></xsl:attribute></xsl:if>
		<a href="{ancestor::rowlist/@sortUri}&amp;order[]={@name}&amp;order[]=desc" class="{@sort}"><xsl:value-of select="text()"/><span></span></a>
	</th>
</xsl:template>
<xsl:template match="rowlist/headers/h[@sort='desc' or @sort='sort']" priority="1">
	<th scope="col">
		<xsl:if test="@class"><xsl:attribute name="class"><xsl:value-of select="@class"/></xsl:attribute></xsl:if>
		<a href="{ancestor::rowlist/@sortUri}&amp;order[]={@name}&amp;order[]=asc" class="{@sort}"><xsl:value-of select="text()"/><span></span></a>
	</th>
</xsl:template>

<!-- Ячейки -->
<xsl:template match="rowlist/row">
	<tr>
		<xsl:if test="not(parent::rowlist/@nocheckbox)">
			<td class="cntr"><input class="select_row" type="checkbox" name="row[]" value="{@id}"/></td>
		</xsl:if>
		<xsl:apply-templates select="cell"/>
		<xsl:apply-templates select="buttons"/>
	</tr>
</xsl:template>
<xsl:template match="rowlist/row/cell">
	<xsl:variable name="class"><xsl:call-template name="cellClass"/></xsl:variable>
	<td>
		<xsl:if test="string-length($class)">
			<xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute>
		</xsl:if>
		<xsl:value-of select="text()" disable-output-escaping="yes"/>
	</td>
</xsl:template>
<xsl:template match="rowlist/row/cell[@name='active']" priority="1">
	<td class="switch"><a href="?id={$_sec/@id}&amp;md={/page/tabs/tab[@selected='selected']/@id}&amp;row={ancestor::row/@id}&amp;action={@name}" class="on" onclick="return onSwitch(this);">
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="string-length(text())">off</xsl:when>
				<xsl:otherwise>on</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
	</a></td>
</xsl:template>
<xsl:template name="cellClass">
	<xsl:variable name="pos" select="count(preceding-sibling::cell) + 1"/>
	<xsl:value-of select="ancestor::rowlist/headers/h[$pos]/@class"/>
</xsl:template>

<!-- Кнопки -->
<xsl:template match="rowlist/row/buttons">
	<td class="btn">
		<xsl:apply-templates select="b"/>
	</td>
</xsl:template>
<xsl:template match="rowlist/row/buttons/b">
	<a href="?id={$_sec/@id}&amp;md={/page/tabs/tab[@selected='selected']/@id}&amp;row={ancestor::row/@id}&amp;action={@action}&amp;page={ancestor::rowlist/@curPage}" class="{@action}">
		<xsl:if test="@title"><xsl:attribute name="title"><xsl:value-of select="@title"/></xsl:attribute></xsl:if>
		<xsl:choose>
			<xsl:when test="@action='delete'"><xsl:attribute name="onclick">return onDel()</xsl:attribute></xsl:when>
			<xsl:when test="@action='move'"><xsl:attribute name="onclick">return onMove(this)</xsl:attribute></xsl:when>
		</xsl:choose>
	</a>
</xsl:template>

<!-- Статистика -->
<xsl:template match="totals">
	<xsl:if test="@header"><h2><xsl:value-of select="@header" /></h2></xsl:if>
    <table class="totals">
        <xsl:apply-templates select="total" />
    </table>
</xsl:template>
<xsl:template match="totals/total">
    <tr>
        <td><xsl:value-of select="@header" /></td>
        <td colspan="{count(ancestor::rowlist/headers/h) -1}" align="right">
            <xsl:value-of select="@value" />
        </td>
    </tr>
</xsl:template>

<!-- Фильтр -->
<xsl:template match="filter">
	<xsl:param name="url" />
	<form id="filter" class="default" action="{$url}" method="post" >
		<xsl:apply-templates select="field" />
		<input type="submit" class="filter" value="{@label}" />
	</form>
</xsl:template>
<xsl:template match="filter/field[@type='text']">
	<script type="text/javascript">
	<xsl:text></xsl:text>
	</script>
	<input type="{@type}" name="cond[{@name}]" id="{@name}" maxlength="255" value="{@label}" 
		onfocus="this.value = (this.value == '{@label}')? '':this.value;" 
		onblur="this.value = (this.value == '')?'{@label}':this.value;"
	>
		<xsl:attribute name="value">
			<xsl:choose>
				<xsl:when test="@value"><xsl:value-of select="@value" /></xsl:when>
				<xsl:otherwise><xsl:value-of select="@label" /></xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<xsl:if test="@size">
			<xsl:attribute name="size"><xsl:value-of select="@size"/></xsl:attribute>
		</xsl:if>
	</input>
</xsl:template>

</xsl:stylesheet>
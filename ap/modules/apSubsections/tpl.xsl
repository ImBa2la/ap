<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8"/>

<xsl:template match="/page/section/form//field[@name='alias']" priority="1">
	<div class="field text">
		<label for="{@name}">*<xsl:value-of select="@label"/></label>
		<input type="{@type}" name="{@name}" id="{@name}" maxlength="63" value="{text()}">
			<xsl:if test="@size">
				<xsl:attribute name="size"><xsl:value-of select="@size"/></xsl:attribute>
			</xsl:if>
		</input>
	</div>
<script type="text/javascript">
<xsl:text disable-output-escaping="yes">
todo.onload(function(){
var form = todo.get('form_add');
	var  title=todo.get('title')
		,pref='</xsl:text><xsl:value-of select="/page/section/form//field[@name='alias']/text()" /><xsl:text disable-output-escaping="yes">'
		,input=todo.get('</xsl:text><xsl:value-of select="@name"/><xsl:text disable-output-escaping="yes">')
		,testid=function(){
			if(this.value.match(/^[a-z]{1}[a-z0-9_-]{2,50}$/i)){
				this._chars = true;
				todo.ajax('../classes/ajax.php',function(o){return function(text,xml){
					o._new_id=text=='1';
					o.style.border='1px solid '+(o._new_id?'green':'red');
				};}(this),{
					'section':'</xsl:text><xsl:value-of select="$_sec/@id"/><xsl:text disable-output-escaping="yes">',
					'md':'</xsl:text><xsl:value-of select="/page/section/@module"/><xsl:text disable-output-escaping="yes">',
					'action':'apSubsections',
					'isset':this.value
				});
			}else{
				this._chars=false;
				this.style.border='1px solid red';
			}
		}
		,translate=function(){
			if(this.value){
				todo.ajax('../classes/ajax.php',function(o){return function(text,xml){
					var  json = eval('('+ text +')');
					input.value = pref;
					if(!json.error &amp;&amp; json.sentences.length &gt; 0){
						input.value += json.sentences[0].translit.toLowerCase().replace(/\s/gi,'_');
					}
					testid.call(input);
				};}(this),{
					'section':'</xsl:text><xsl:value-of select="$_sec/@id"/><xsl:text disable-output-escaping="yes">',
					'md':'</xsl:text><xsl:value-of select="/page/section/@module"/><xsl:text disable-output-escaping="yes">',
					'translate':this.value
				});
			}
		};
	if(title){
		if(title.addEventListener){
			title.addEventListener('change',translate,false);
			title.addEventListener('keyup',translate,false);
		}else{
			title.attachEvent('onchange',translate);
			title.attachEvent('onkeyup',translate);
		};
	}
	if(input){
		testid.call(input);
		if(input.addEventListener){
			input.addEventListener('change',testid,false);
			input.addEventListener('keyup',testid,false);
		}else{
			input.attachEvent('onchange',testid);
			input.attachEvent('onkeyup',testid);
		};
	}
	for(var i=0; i &lt; form.elements.length; i++){
		for(var j=0; j &lt; form.elements[i].attributes.length; j++){
			if(form.elements[i].attributes[j].name == 'class' &amp;&amp; form.elements[i].attributes[j].value == 'ok'){
				form.elements[i].oldOnclick = form.elements[i].onclick;
				form.elements[i].onclick = function(i){
					this.oldOnclick();
					if(input.style.borderColor == 'red'){
						alert('Неверное значение поля "Ссылка"');
						return false;
					}
				};
			}
		}
	}
});
</xsl:text>
</script>
</xsl:template>
<xsl:template match="/page/section/form//field[contains(@check,'sectionId')]" mode="fieldcheck">if(!this['<xsl:value-of select="@name"/>'].value.match(/^[a-z]{1}[a-z0-9_-]{2,50}$/i)){alert('Поле "<xsl:value-of select="@label"/>" должно содержать не менее трех латинских символов\n в нижнем регистре, без пробелов и не должно совпадать с сылками других разделов');return false;};</xsl:template>

</xsl:stylesheet>
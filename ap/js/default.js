// JavaScript Document
function editor(id,prop){
	var v={
// General options
mode : "none",
theme : "advanced",
plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
language : "ru",

// Theme options
theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
theme_advanced_statusbar_location : "bottom",
relative_urls : false,

theme_advanced_resize_horizontal : false,
theme_advanced_resizing : true,

// Example content CSS (should be your site CSS)
content_css : "css/tinymce.css",

// Drop lists for link/image/media/template dialogs
template_external_list_url : "lists/template_list.js",
external_link_list_url : "lists/link_list.js",
external_image_list_url : "lists/image_list.js",
media_external_list_url : "lists/media_list.js",


// Replace values for the template plugin
template_replace_values : {
	username : "Some User",
	staffid : "991234"
},

file_browser_callback: function(field_name, url, type, win){
		tinyMCE.activeEditor.windowManager.open({
			file: 'uploader.php?opener=tinymce&type='+type,
			title: 'Active Page File Manager',
			width: 700,
			height: 500,
			resizable: "yes",
			inline: true,
			close_previous: "no",
			popup_css: false
		},{
			callback: function(url){
				win.document.getElementById(field_name).value=url;
				if(typeof(win.ImageDialog) != "undefined"){
					if(win.ImageDialog.getImageData)win.ImageDialog.getImageData();
					if(win.ImageDialog.showPreviewImage)win.ImageDialog.showPreviewImage(url);
				};	
			}
		});
		return false;
	}
};
	if(typeof(prop)=='object')for(var i in prop)v[i]=prop[i];
	tinyMCE.init(v);
	if(id&&v.mode=='none')tinyMCE.execCommand("mceAddControl",true,id);
};

todo.onload(function(){
	initDropMenu('sidemenu');
	todo.loop(document.getElementsByTagName('table'),function(){
		if(this.className=='rows'){
			this._getSelectedItems=function(){
				var inp=this.getElementsByTagName('input'),v=[];
				for(var i=0;i<inp.length;i++)if(inp[i].className=='select_row'&&inp[i].checked)v.push(inp[i].value);
				return v;
			};
			todo.loop(this.getElementsByTagName('input'),function(){
				if(this.type.toLowerCase()!='checkbox')return;
				switch(this.className){
					case 'select_row':
						this._checkSelection=function(){this.parentNode.parentNode.className=this.checked?'selected':null;};
						this.onclick=function(){try{
							var el=this.parentNode.offsetParent.rows[0].getElementsByTagName('input');
							for(var i=0;i<el.length;i++)if(el[i].className=='select_all_rows')el[i]._checkSelection();
							this._checkSelection();
						}catch(er){}};
						this._checkSelection();
						break;
					case 'select_all_rows':
						this._checkSelection=function(){
							this.checked=true;
							var el=this.parentNode.offsetParent.getElementsByTagName('input');
							for(var i=0;i<el.length;i++)if(el[i].className=='select_row'&&!el[i].checked){
								this.checked=false;
								return;
							}
						};
						this.onclick=function(){try{
							var el=this.parentNode.offsetParent.getElementsByTagName('input');
							for(var i=0;i<el.length;i++)if(el[i].className=='select_row'){
								el[i].checked=this.checked;
								el[i]._checkSelection();
							}
						}catch(er){}};
						this._checkSelection();
						break;
				}
			});
		}
	});
});

//helper
function fixEvent(e) {
	e = e || window.event;// получить объект событие для IE

	if ( e.pageX == null && e.clientX != null ) {// добавить pageX/pageY для IE
		var html = document.documentElement;
		var body = document.body;
		e.pageX = e.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0)
		e.pageY = e.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0)
	}
	if (!e.which && e.button) {// добавить which для IE
		e.which = e.button & 1 ? 1 : ( e.button & 2 ? 3 : ( e.button & 4 ? 2 : 0 ) );
	}

	return e;
}

function getOffset(elem) {
    if (elem.getBoundingClientRect) {
        return getOffsetRect(elem)
    } else {
        return getOffsetSum(elem)
    }
}

function getOffsetRect(elem) {
    var box = elem.getBoundingClientRect()
 
    var body = document.body
    var docElem = document.documentElement
 
    var scrollTop = window.pageYOffset || docElem.scrollTop || body.scrollTop
    var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft
    var clientTop = docElem.clientTop || body.clientTop || 0
    var clientLeft = docElem.clientLeft || body.clientLeft || 0
    var top  = box.top +  scrollTop - clientTop
    var left = box.left + scrollLeft - clientLeft
 
    return { top: Math.round(top) - 114, left: Math.round(left) }
}

function getOffsetSum(elem) {
    var top=0, left=0
    while(elem) {
        top = top + parseInt(elem.offsetTop)
        left = left + parseInt(elem.offsetLeft)
        elem = elem.offsetParent        
    }
 
    return {top: top - 114, left: left}
}
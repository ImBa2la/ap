// JavaScript Document
/* common func */
todo.hc = function(s, c) {return ~(' ' + s + ' ').indexOf(' ' + c + ' ')} /*check string (classes)*/
todo.ct = function(t) {return document.createTextNode(t)} //create text node
todo.append = function(n, e) {e = e || document.body;return e.appendChild(n);} //appendChild
todo.cc = function(o, add, del) { /*cnangeClass*/
	var o = o || {}, n = 'className', cN = (undefined != o[n]) ? o[n] : o, ok = 0;
	if ('string' !== typeof cN) return false;
	var re = new RegExp('(\\s+|^)' + del + '(\\s+|$)', 'g');
	if (add) /*addClass*/
		if (!todo.hc(cN, add)) {cN += ' ' + add;ok++;}
	if (del) /*delClass*/
		if (todo.hc(cN, del)) {cN = cN.replace(re, ' ');ok++;}
	if (!ok) return false
	if ('object' == typeof o) o[n] = cN;
	else return cN;
}
todo.bind = function(func, context /*, args*/) {
	/* bind(func, context, аргументы) 
	 * bind(obj, 'method', аргументы).
	 */
	var args = [].slice.call(arguments, 2);
	if (typeof context == "string") { 
		args.unshift(func[context], context);
		return bind.apply(this, args);
	}  
	function wrapper() {
		var unshiftArgs = args.concat( [].slice.call(arguments) );
		return func.apply(context, unshiftArgs);
	}
	return wrapper;
}
var pd = function(){
	var adde = function(el,f){
		if(document.addEventListener){
			el.addEventListener('click',f,false);
		}else{
			el['onclick'] = f;
		}
	}
	var createLi = function(name,value){
		var li = todo.create('li');
		li.appendChild(todo.create('input',{'id':value,'type':'checkbox'}));
		li.appendChild(todo.create('label',{'for':value},name));
		return li;
	}
	// vars
	var inp
		,firstopen = true
		,fields = []
		,maindiv = todo.create('div',{'id':'podpiska'},null,{'display':'none'})
		,background = todo.create('div',{'id':'background'},null,{'display':'none'})
	//constructor
	var sehHtml = function(e){e.innerHTML = '<div id="podpiska-message"><form id="subscribe-form" method="post"><fieldset class="top"><legend>Подписка на вакансии</legend><div><label for="lastname">Фамилия:</label><input type="text" name="lastname" id="lastname"></div><div><label for="email">E-mail:</label><input type="text" name="email" id="email"></div><div><label for="name">Имя:</label><input type="text" name="name" id="name"></div></fieldset><fieldset><legend>Выберите интересующие вакансии</legend><div class="select-v"><a href="javascript:void(0);" id="podpiska-sel">выбрать все</a> | <a href="javascript:void(0);" id="podpiska-unsel">отменить все выбранные</a></div><div class="list-v" id="podpiskainput"><ul id="podpiskalist">'+window.cvList+'</ul></div></fieldset><input class="submit" type="submit" id="send-subscribe" value="отправить"/></form><div id="final" class="good"></div></div><div class="s1"><div class="s2"></div></div><span href="#" class="close" id="podpiska-close">закрыть</span>';};
	var selall = function(){if(inp=document.getElementById('podpiskainput').getElementsByTagName('input'))for(var i in inp)if(inp[i].tagName)inp[i].checked = true;}
	var unselall = function(){if(inp=document.getElementById('podpiskainput').getElementsByTagName('input'))for(var i in inp)if(inp[i].tagName)inp[i].checked = false;}
	var open = function(){
		maindiv.style.display = 'block';
		background.style.display = 'block';
		if(firstopen){
			var ul = document.getElementById('podpiskalist');
			for(var i in fields){
				ul.appendChild(createLi(fields[i],i));
			}
			listColumns(ul,3,10);
		}
		firstopen = false;
		return false;
	}
	var close = function(e){todo.get('final').style.display = 'none'; todo.get('subscribe-form').style.display = ''; maindiv.style.display = 'none';background.style.display = 'none';return false;}
	var add = function(name,value){
		fields[value] = name;
	}
	var unsubscribe = function(key){
		todo.ajax(
			'../classes/ajax.php'
			,function(){return function(data){
				var json = eval('('+data+')'),e = todo.get('final'), f = todo.get('subscribe-form');
				open();
				f.style.display = 'none';
				e.style.display = 'block';
				if(json.code == 0 && !json.error){
					e.innerHTML = 'Вы были успешно отписались от рассылки';
					setTimeout(function(){close();},5000);
				}else{//unsubscribe error
					e.innerHTML = 'Ошибка! Возможно зашифрованный ключ некорректен';
					setTimeout(function(){todo.get('final').style.display = 'none'; f.style.display = '';},7000);
				}
			};}()
			,{
				'ap':true,
				'section':'list_of_vacancies',
				'md':'m4',
				'act':'unsubscribe',
				'key':key
			}
		);
	}
	var post = function(f){
		var params = {
			md		:'m4',
			section	:'list_of_vacancies',
			act		:'subscribe',
			active	:1,
			name	:todo.get('name').value,
			email	:todo.get('email').value,
			lastname:todo.get('lastname').value
		};
		for(var i=0,str='',j=0; i < f['list[]'].length; i++){//NOTE! Number of checkboxes should be > 1
			if(f['list[]'][i].checked){
				str += (j>0?'&':'')+'list['+f['list[]'][i].value+']='+f['list[]'][i].value;
				j++;
			}
		}	
		todo.ajax('classes/ajax.php?'+str,function(data){
			console.log(data);
			var json = eval('('+data+')'),e = todo.get('final');
			f.style.display = 'none';
			e.style.display = 'block';
			if(json.code == 0 && !json.error){
				e.innerHTML = 'Вы были успешно подписаны на указанные вакансии';
				setTimeout(function(){close();},5000);
			}else{//subscribe error
				e.innerHTML = 'Во время выполнения подписки произошла ошибка, возможно были не корректно заполнены поля или не указана ни одна вакансия, попробуйте еще раз.';
				setTimeout(function(){todo.get('final').style.display = 'none'; f.style.display = '';},7000);
			}
		},params,'get');
		return false;
	}
	var esc = function(e){
		e = e || window.event;
		if (27 == e.keyCode) close();
	}
	var init = function(){
		if(document.body){
			document.onkeydown = esc;
			sehHtml(maindiv);
			document.body.appendChild(background);
			document.body.appendChild(maindiv);
			todo.get('subscribe-form').onsubmit = function(){return post(this);}
			adde(todo.get('podpiska-sel'),selall);
			adde(todo.get('podpiska-unsel'),unselall);
			adde(todo.get('podpiska-close'),close);
			adde(background,close);
		}else{
			setTimeout(function(){init()},50);
		}
	};
	init();
	
	return {
		_:maindiv,
		add:function(name,value){add(name,value);},
		open:function(){open()},
		close:function(){close()},
		selall:function(){selall()},
		unselall:function(){unselall()},
		post:function(){post()},
		unsubscribe:function(key){unsubscribe(key);}
	}
};
pd=new pd();
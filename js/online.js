$(document).ready(function(){
	$('.menu-projects a').css('opacity','0');

	$('span.select-project').click(function(){		
		if($(this).attr('rel') != 'show'){
			//$(this).animate({'paddingBottom':'5px'},500);
			$(this).siblings('a').animate({height:'30px'},{duration:500,complete:showO, queue:false});
			$(this).attr('rel','show');
		}else{
			$(this).removeAttr('rel');
			$(this).siblings('a').animate({opacity: '0'},{duration:500, complete:hideH, queue:false});
		}
	});
	function showO(){$(this).animate({opacity:1},{duration:500,queue:false}).css('visibility','visible');$(this).siblings('span.select-project').css('backgroundImage','url(images/online-selected-bg-up.gif)')}
	function hideH(){$(this).animate({height:'0'},{duration:500,queue:false}).css('visibility','hidden'); $(this).siblings('span.select-project')/*.animate({'paddingBottom':'0'},{duration:100,queue:false})*/.css('backgroundImage','url(images/online-selected-bg-down.gif)');}
})
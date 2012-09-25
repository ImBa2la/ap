todo.onload(function(){	
	//unsubscribe action
	if(window.location.hash.match(/unsubscribe/i)){
		pd.unsubscribe(window.location.hash.substr(13));
	}
});
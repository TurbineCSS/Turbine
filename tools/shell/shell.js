window.addEvent('domready', function(){

	$('go').addEvent('click', function(){
		// Request CSS code
		var csscode = '';
		var csspRequest = new Request({
			url: 'getcss.php',
			data: 'css=' + $('cssp').value,
			onSuccess: function(txt){
				// Update textarea
				csscode = txt;
				$('css').value = txt
				// Apply iframe operations
				var result = new IFrame($('result'));
				result.contentDocument.defaultView.document.getElements('body')[0].innerHTML = $('html').value;
				result.contentDocument.defaultView.document.getElements('style')[0].innerHTML = csscode;
			}
		}).send();
	});

});
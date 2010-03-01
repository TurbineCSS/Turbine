window.addEvent('domready', function(){

	$('go').addEvent('click', function(){
		// Request CSS code
		var csscode = '';
		var browservars = '';
		$$('#browservars input[type=text]').each(function(el){
			browservars += '&' + el.get('name') + '=' + el.value;
		});
		var csspRequest = new Request({
			url: 'getcss.php',
			data: 'css=' + $('cssp').value + browservars,
			onSuccess: function(txt){
				// Update textarea
				csscode = txt;
				$('css').value = txt
				// Apply iframe operations
				var result = new IFrame($('result'));
				result.contentDocument.getElements('body')[0].set('html', $('html').value);
				var css = result.contentDocument.getElements('style')[0];
				// <style>-workaround for IE, see http://www.phpied.com/dynamic-script-and-style-elements-in-ie/
				if(css.styleSheet){
					css.styleSheet.cssText = csscode; // IE
				}
				else{
					css.set('text', csscode); // Normal Browsers
				}
			}
		}).send();
	});

});
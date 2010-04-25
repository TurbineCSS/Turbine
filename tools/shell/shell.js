window.addEvent('domready', function(){

	// Input elements
	var turbineinput = $('cssp');
	var cssinput = $('css');
	var htmlinput = $('html');
	var interactive = $('interactive');
	var browserinputs = $$('#browservars input[type=text]');

	// Evaluate code
	var evaluate = function(){
		// Request CSS code
		var csscode = '';
		var browservars = '';
		browserinputs.each(function(el){
			browservars += '&' + el.get('name') + '=' + el.value;
		});
		var csspRequest = new Request({
			url: 'getcss.php',
			data: 'css=' + turbineinput.value + browservars,
			onSuccess: function(txt){
				// Update textarea
				cssinput.value = txt
				// Apply iframe operations
				var result = new IFrame($('result'));
				result.contentDocument.getElements('body')[0].set('html', htmlinput.value);
				var css = result.contentDocument.getElements('style')[0];
				// <style>-workaround for IE, see http://www.phpied.com/dynamic-script-and-style-elements-in-ie/
				if(css.styleSheet){
					css.styleSheet.cssText = txt; // IE
				}
				else{
					css.set('text', txt); // Normal Browsers
				}
			}
		}).send();
	};

	// Evaluate on button click
	$('go').addEvent('click', evaluate);

	// Evaluate in interactive mode
	$$([turbineinput, htmlinput, browserinputs]).addEvent('keyup', function(){
		if(interactive.checked){
			evaluate();
		}
	});


	// Add editor functionality
	var turbineEditor = new Editor('cssp');
	var htmlEditor = new Editor('html');


});
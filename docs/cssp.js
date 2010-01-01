window.addEvent('domready', function(){

	var toc = $$('#toc > ul')[0];
	var tocLinks = toc.getElements('a')

	var tocScroll = new Fx.SmoothScroll({
		links: tocLinks
	});

});
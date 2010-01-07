window.addEvent('domready', function(){

	var toc = $$('#toc > ul')[0];

	// Smooth scroll
	var tocLinks = toc.getElements('a');
	var tocScroll = new Fx.SmoothScroll({
		links: tocLinks
	});

	// Folding sub navigation
	var subToc = toc.getElements('ul');
	subToc.each(function(sub){
		var parentLink = sub.getPrevious('a');
		parentLink.addEvent('click', function(){
			subToc.slide('out');
			sub.slide('in');
		});
	});
	subToc.slide('hide');
	subToc[0].slide('in');

});
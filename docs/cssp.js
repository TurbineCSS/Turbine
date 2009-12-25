window.addEvent('domready', function(){

	var toc = $$('#toc > ul')[0];
	var tocLinks = toc.getElements('a')

	var tocSlide = new Fx.Slide(toc).hide();

	$$('#header h2').addEvent('click', function(){
		tocSlide.toggle();
	});

	toc.addEvent('click', function(e){
		e.stop();
	});

	$$('body').addEvent('click', function(){
		tocSlide.slideOut();
	});

	var tocScroll = new Fx.SmoothScroll({
		links: tocLinks
	});

});
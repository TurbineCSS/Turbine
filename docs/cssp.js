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


	// Hightlighting
	var indention_char = "\t";
	var rex = {
		comment: /(.*?)(\/\/(?:.*?))$/
	};
	// Get a line's indention level
	function get_indention_level(line){
		var level = 1;
		if(line){
			if(line.substr(0, indention_char.length) == indention_char){
				level = level + get_indention_level(line.substr(indention_char.length));
			}
		}
		return level;
	}
	// Highlight code areas
	var areas = $$('pre.cssp');
	areas.each(function(area){
		var newlines = [];
		var lines = area.get('text').split("\n");
		var num = lines.length;
		for(var i = 0; i < num; i++){
			var line = lines[i];
			var nextline = lines[i + 1];
			// @rules
			if(line.substr(0, 6) == '@media'){
				line = '<span class="at media">' + line + '</span>';
			}
			else if(line.substr(0, 7) == '@import'){
				line = '<span class="at import">' + line + '</span>';
			}
			// Selector style line?
			if(get_indention_level(line) + 1 == get_indention_level(nextline)){
				line = line.replace(/,/g, '<span class="ch">,</span>');
				line = '<span class="se">' + line + '</span>';
			}
			else{
				line = line.replace(/:/g, '<span class="ch">:</span>');
				line = line.replace(/;/g, '<span class="ch">;</span>');
			}
			// Strings
			// TODO
			// Comments
			var matches = rex.comment.exec(line);
			if(matches && matches.length == 3){
				line = matches[1] + '<span class="co">' + matches[2] + '</span>';
			}
			newlines.push(line);
		}
		var code = newlines.join("\n")
		// Use spaces instead of tabs
		code = code.replace(/\t/g, '<span class="tab">&raquo;   </span>');
		// Highlight !important
		code = code.replace(/!important/g, '<span class="im">!important</span>');
		area.set('html', code);
	});


});
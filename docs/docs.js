window.addEvent('domready', function(){


	var toc = $$('#toc > ul')[0];


	// Smooth scroll
	var smoothLinks = $$('#toc > ul a, a.smoothscroll');
	var tocScroll = new Fx.SmoothScroll({
		links: smoothLinks
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
	var indention_char = "    ";
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
		var block_comment_state = false;
		var newlines = [];
		var lines = area.get('text').split("\n");
		var num = lines.length;
		for(var i = 0; i < num; i++){
			var line = lines[i];
			var nextline = lines[i + 1];
			// Block comment
			if(line.substr(0, 2) == '--'){
				if(block_comment_state == false){
					line = '<span class="co">' + line;
					block_comment_state = true;
				}
				else{
					line = line + '</span>';
					block_comment_state = false;
				}
			}
			// @rules
			if(line.substr(0, 1) == '@'){
				line = '<span class="at">' + line + '</span>';
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
			// Strings (TODO)
			// Comments
			var matches = /(.*?)(\/\/(?:.*?))$/.exec(line);
			if(matches && matches.length == 3){
				line = matches[1] + '<span class="co">' + matches[2] + '</span>';
			}
			newlines.push(line);
		}
		var code = newlines.join("\n")
		// Highlight !important
		code = code.replace(/!important/g, '<span class="im">!important</span>');
		area.set('html', code);
	});


});
var CSSPEditor = new Class({


	Extends: Editor,


	highlight:function(){
		var get_indention_level = function(line){
			var level = 1;
			if(line){
				if(line.substr(0, this.options.tab.length) == this.options.tab){
					level = level + get_indention_level(line.substr(this.options.tab.length));
				}
			}
			return level;
		}.bind(this);
		var lines = this.editor.get('html').split('<br>');
		var num = lines.length;
		var newlines = [];
		for(var i = 0; i < num; i++){
			var line = lines[i].replace(/<(?:.|\s)*?>/g, '');
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
			// Strings (TODO)
			// Comments
			var matches = /(.*?)(\/\/(?:.*?))$/.exec(line);
			if(matches && matches.length == 3){
				line = matches[1] + '<span class="co">' + matches[2] + '</span>';
			}
			newlines.push(line);
		}
		var code = newlines.join('<br>');
		// !important
		code = code.replace(/!important/g, '<span class="im">!important</span>');
		this.editor.set('html', code);
	},


	optimize: function(){
		var lines = this.editor.get('html').split('<br>');
		var num = lines.length;
		var newlines = [];
		for (var i = 0; i < num; i++){
			var line = lines[i].replace(/<(?:.|\s)*?>/g, '');
			var matches = /^(.*?)(;)$/.exec(line);
			if(matches && matches.length == 3){
				line = matches[1];
			}
			newlines.push(line);
		}
		var code = newlines.join('<br>');
		this.editor.set('html', code);
	}

});
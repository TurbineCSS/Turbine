var Editor = new Class({


	Implements: [Options, Events],
	indentionChar: '',
	editor: null,


	initialize: function(editor){
		this.editorSetup(editor);
	},


	// Setup the editor
	editorSetup: function(editor){
		this.editor = document.id(editor);
		this.editor.addEvent('keypress', function(e){
			this.handleKeypress(e);
		}.bind(this));
		this.editor.addEvent('keyup', function(e){
			this.handleKeyup(e);
		}.bind(this));
	},


	// Get indention char(s)
	getIndentionChar: function(){
		var lines = this.editor.value.split(/\n/);
		var linecount = lines.length;
		for(var i = 0; i < linecount; i++){
			line = lines[i];
			nextline = lines[i + 1];
			if(nextline && line.trim() != '' && nextline.trim() != ''){
				var matches = nextline.match(/^([\s]+)(.*?)$/);
				if(matches.length == 3 && matches[1].length > 0 && matches[2].substr(0, 1) != '@'){
					this.indentionChar = matches[1];
					return;
				}
			}
		}
	},


	// Handle keys on keypress event
	handleKeypress: function(event){
		// Get/update indention char(s)
		// Not needed as long as handleEnter() does nothing
		// this.getIndentionChar();
		// Tabs
		if(event.code == 9){
			this.handleTab(event);
		}
		// Fire keypress event for this editor
		this.fireEvent('keypress');
	},


	// Handle keys on keyup event
	handleKeyup: function(event){
		// Enter
		if(event.code == 13){
			this.handleEnter(event);
		}
		// Fire keyup event for this editor
		this.fireEvent('keyup');
	},


	// Handle tab insertion
	handleTab: function(event){
		event.stop();
		var target = event.target;
		var ss = target.selectionStart;
		var se = target.selectionEnd;
		target.value = target.value.slice(0, ss).concat("\t").concat(target.value.slice(ss, target.value.length));
		if(ss == se){
			target.selectionStart = ++ss;
			target.selectionEnd = ++ss;
		}
		else{
			target.selectionStart = ++ss;
			target.selectionEnd = ++se;
		}
	},


	// Handle line break insertion
	handleEnter: function(event){
		// TODO
	}

});
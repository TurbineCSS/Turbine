var Editor = new Class({


	Implements: [Options, Events],
	options: {
		tab: "\t",
		autosave: {
			enabled: true,
			slots: 5,
			interval: 6000,
			menubar: null
		}
	},
	editor: null,
	menubar: null,
	buffer: '',


	initialize: function(editor, options){
		this.setOptions(options);
		this.editor = document.id(editor);
		this.editorSetup();
		this.menubarSetup();
		return this;
	},


	editorSetup: function(){
		this.editor.addEvent('keypress', function(e){
			this.handleKeypress(e);
		}.bind(this));
		this.editor.addEvent('keyup', function(e){
			this.handleKeyup(e);
		}.bind(this));
	},


	menubarSetup: function(){
		var ed = this;
		if(this.options.menubar !== null){
			this.menubar = document.id(this.options.menubar);
			var menulinks =[
				new Element('a', {
					text: 'Save', href: '#', events:{
						'click':function(event){
							ed.save();
							event.stop();
						}
					}
				}),
				new Element('a', {
					text: 'Revert', href: '#', events:{
						'click':function(event){
							ed.revert();
							event.stop();
						}
					}
				})
			];
			menulinks.each(function(link){
				link.inject(new Element('li').inject(this.menubar));
			}.bind(this));
		}
	},

	handleKeypress: function(e){
		if(e.code == 9){ // Tab
			var selection = window.getSelection();
			if(selection.isCollapsed === true){
				this.handleSingleTab(e);
			}
			else{
				e.stop();
			}
		}
		this.fireEvent('keypress');
	},


	handleKeyup: function(e){
		if(e.code == 13){ // Enter
			this.handleEnter(e);
		}
		this.fireEvent('keyup');
	},


	handleSingleTab: function(e){
		e.stop();
		this.insert(this.options.tab);
		this.fireEvent('keytab');
	},


	handleEnter: function(e){
		var insert = this.insert('');
		var prev = insert.previousSibling.previousSibling;
		if(prev.nodeValue.trim() != ''){
			var starttabs =  this.getTabCount(prev.textContent);
			if(starttabs > 0){
				var tabs = '';
				for(var i = 0; i < starttabs; i++){
					tabs += this.options.tab;
				}
				insert.textContent = tabs;
			}
		}
		this.fireEvent('enter');
	},


	insert: function(text){
		var insert = document.createTextNode(text);
		var range = window.getSelection().getRangeAt(0);
		range.insertNode(insert);
		this.setCursorAfter(insert);
		return insert;
	},


	getTabCount: function(text){
		var num = 0;
		if(text){
			if(text.substr(0, this.options.tab.length) == this.options.tab){
				num++;
				num = num + this.getTabCount(text.substr(this.options.tab.length));
			}
		}
		return num;
	},


	setCursorAfter: function(node){
		var selection = window.getSelection();
		var range = document.createRange();
		range.setStartAfter(node);
		range.setEndAfter(node);
		selection.removeAllRanges();
		selection.addRange(range);
	},


	save: function(){
		if(typeof window.localStorage !== 'undefined'){
			var data = this.editor.get('html');
			data = data.replace(/<br[^>\/]*\>/g, 'NEWLINENEWLINE');
			window.localStorage.setItem('save', data);
			console.log('Saved!');
		}
	},


	revert: function(){
		if(typeof window.localStorage !== 'undefined'){
			var data = window.localStorage.getItem('save');
			if(data){
				data = data.replace(/NEWLINENEWLINE/g, "\n");
				this.editor.set('html', data);
				console.log('Loaded!');
			}
		}
	}


});
var Ide = new Class({

	Implements: [Options, Events],
	options: {
		tab: "\t",
		autosave: {
			enabled: true,
			slots: 5,
			interval: 6000
		}
	},
	ide: null,
	buffer: '',

	initialize: function(ide, options){
		this.setOptions(options);
		this.ide = document.id(ide);
		this.ideSetup();
		return this;
	},

	ideSetup: function(){
		this.ide.addEvent('keypress', function(e){
			this.handleKeypress(e);
		}.bind(this));
		this.ide.addEvent('keyup', function(e){
			this.handleKeyup(e);
		}.bind(this));
		$('savelink').addEvent('click', function(e){
			this.save(e);
		}.bind(this));
		$('revertlink').addEvent('click', function(e){
			this.revert(e);
		}.bind(this));
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
	},

	handleKeyup: function(e){
		if(e.code == 13){ // Enter
			this.handleEnter(e);
		}
	},

	handleSingleTab: function(e){
		e.stop();
		this.insert(this.options.tab);
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

	save: function(e){
		e.stop();
		if(typeof window.localStorage !== 'undefined'){
			var data = this.ide.get('html');
			console.log(data);
			data = data.replace(/<br[^>\/]*\>/g, 'NEWLINENEWLINE');
			window.localStorage.setItem('save', data);
			alert('Gespeichert!');
		}
		else{
			alert('if(browser != Firefox 3.5+){ funktionieren = false; }');
		}
	},

	revert: function(e){
		e.stop();
		if(typeof window.localStorage !== 'undefined'){
			var data = window.localStorage.getItem('save');
			if(data){
				data = data.replace(/NEWLINENEWLINE/g, "\n");
				this.ide.set('text', data);
				alert('Geladen!');
			}
			else{
				alert('Nichts zum laden da!');
			}
		}
		else{
			alert('if(browser != Firefox 3.5+){ funktionieren = false; }');
		}
	}

});
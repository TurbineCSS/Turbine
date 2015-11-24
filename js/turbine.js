window.addEvent('domready',function(){$$('span.email').set('html','<a href="mailto:peter@peterkroener.de">peter@<wbr>peterkroener.de</a>');var smoothLinks=$$('#toc > ul a, a.smoothscroll');var tocScroll=new Fx.SmoothScroll({links:smoothLinks});$$('pre.css').each(function(area){var newlines=[];var lines=area.get('text').split("\n");var num=lines.length;for(var i=0;i<num;i++){var line=lines[i];var nextline=lines[i+ 1];if(line.substr(0,1)=='@'){line='<span class="at">'+ line+'</span>';}
else if(line.substr(-1,1)=='{'){line='<span class="se">'+ line+'</span>';}
line=line.replace(/,/g,'<span class="ch">,</span>');line=line.replace(/:/g,'<span class="ch">:</span>');line=line.replace(/;/g,'<span class="ch">;</span>');line=line.replace(/\{/g,'<span class="ch2">{</span>');line=line.replace(/\}/g,'<span class="ch2">}</span>');newlines.push(line);}
var code=newlines.join("\n")
code=code.replace(/!important/g,'<span class="im">!important</span>');area.set('html',code);});var indention_char="    ";function get_indention_level(line){var level=1;if(line){if(line.substr(0,indention_char.length)==indention_char){level=level+ get_indention_level(line.substr(indention_char.length));}}
return level;}
$$('pre.turbine').each(function(area){var block_comment_state=false;var newlines=[];var lines=area.get('text').split("\n");var num=lines.length;for(var i=0;i<num;i++){var line=lines[i];var nextline=lines[i+ 1];if(line.substr(0,2)=='--'){if(block_comment_state==false){line='<span class="co">'+ line;block_comment_state=true;}
else{line=line+'</span>';block_comment_state=false;}}
if(line.substr(0,1)=='@'){line='<span class="at">'+ line+'</span>';}
if(get_indention_level(line)+ 1==get_indention_level(nextline)){line=line.replace(/,/g,'<span class="ch">,</span>');line='<span class="se">'+ line+'</span>';}
else{line=line.replace(/:/g,'<span class="ch">:</span>');line=line.replace(/;/g,'<span class="ch">;</span>');}
var matches=/(.*?)(\/\/(?:.*?))$/.exec(line);if(matches&&matches.length==3){line=matches[1]+'<span class="co">'+ matches[2]+'</span>';}
newlines.push(line);}
var code=newlines.join("\n")
code=code.replace(/!important/g,'<span class="im">!important</span>');area.set('html',code);});var toc=$$('#toc > ul')[0];if(toc){var subToc=toc.getElements('ul');subToc.each(function(sub){var parentLink=sub.getPrevious('a');parentLink.addEvent('click',function(){subToc.slide('out');sub.slide('in');});});subToc.slide('hide');subToc[0].slide('in');}
var shell=$('shell');if(shell){var supersized=false;var headline=$$('h2')[0];headline.set('text',headline.get('text')+' | ');var link=new Element('a',{'text':'Resize','href':'#'}).inject(headline);link.addEvent('click',function(e){if(supersized){shell.setStyles({'position':'static'});$('footer').setStyle('display','block');supersized=false;}
else{shell.setStyles({'position':'absolute','left':'1%','right':'1%'});$('footer').setStyle('display','none');supersized=true;}
e.stop();});}
var turbineinput=$('cssp');var cssinput=$('css');var htmlinput=$('html');var interactive=$('interactive');var browserinputs=$$('#browservars input[type=text]');if(turbineinput&&cssinput){var evaluate=function(){var csscode='';var browservars='';browserinputs.each(function(el){browservars+='&'+ el.get('name')+'='+ el.value;});var csspRequest=new Request({url:'lib/shell/getcss.php',data:'css='+ turbineinput.value+ browservars,onSuccess:function(txt){cssinput.value=txt
var result=new IFrame($('result'));result.contentDocument.getElements('body')[0].set('html',htmlinput.value);var css=result.contentDocument.getElements('style')[0];if(css.styleSheet){css.styleSheet.cssText=txt;}
else{css.set('text',txt);}}}).send();};$('go').addEvent('click',evaluate);$$([turbineinput,htmlinput,browserinputs]).addEvent('keyup',function(){if(interactive.checked){evaluate();}});}});
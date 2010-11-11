Turbine
=======

![Turbine logo][1]

**→ [Online documentation][2]**

Turbine is a collection of PHP-powered tools that are designed to decrease css development time and web developer headache. This includes:

 - A new, minmal syntax – the less you have have to type, the more you get done
 - Packing, gzipping and automatic minification of multiple style files
 - Constants (also known as "css variables") and selector aliases as well as nested css selectors
 - Oop-like inheritance, extension and templating features
 - Built-in device-, browser- and os sniffing
 - Many automatic bugfixes and enhancements for older browsers
 - Fully exensible through a very simple plugin system. A basic understanding of PHP is enough to add completely new features to Turbine
 - A CSS to Turbine converter and a shell app for experiments and development

Example
-------

Turbine takes this...


    #foo
        color:red
        div.foo, div.bar
            margin, padding:4px
            border-radius:4px

and turns it into:

    #foo {
        color: red;
    }
    #foo div.foo, #foo div.bar {
        margin: 4px;
        padding: 4px;
        -moz-border-radius: 4px;
        -khtml-border-radius: 4px;
        -webkit-border-radius: 4px;
        border-radius: 4px;
    }


It is somewhat compareable to [Sass][3] and [Scaffold][4], but more radically geared towards getting as much done as possible in as few keystrokes as possible.

We need your help!
------------------

Turbine is still in active development. Help us to make it better!

 - Download Turbine, test it and [report any bugs you find][5]
 - [Fork it][6] and tackle some bugs
 - Help improving the [browser sniffer][7]


  [1]: http://turbine.peterkroener.de/turbine.png
  [2]: http://turbine.peterkroener.de/
  [3]: http://sass-lang.com/
  [4]: http://github.com/anthonyshort/csscaffold
  [5]: http://github.com/SirPepe/Turbine/issues
  [6]: http://github.com/SirPepe/Turbine
  [7]: http://github.com/SirPepe/Turbine-Browser

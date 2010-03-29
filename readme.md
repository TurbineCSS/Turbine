Turbine
=======

[Online documentation][3] (incomplete)

Turbine is a collection of PHP-powered tools that are designed to decrease css devolopment time and web developer headache. This includes:

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

Turbine takes something like this...


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


It is somewhat compareable to [Sass][1] and [Scaffold][2], but more radically geared towards getting as much done as possible in as few keystrokes as possible.

  [1]: http://sass-lang.com/
  [2]: http://github.com/anthonyshort/csscaffold
  [3]: http://turbine.peterkroener.de/
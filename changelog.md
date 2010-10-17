Changelog
=========


1.0.11
------
  - Fix: Box shadows with zero offset now work in IE
  - Fix: Prevent trailing semicolons in file list from throwing errors about missing files
  - Fix: Box sizing should now work in IE 6 and 7


1.0.10
------
  - Fix: Semicolons were not stripped vom @turbine's plugin rule
  - Improvement: Updated browser sniffer


1.0.9
-----
  - Fix: Box sizing turned out not to work in IE < 8 at all. Fixed.
  - Improvement: Added reasonable comments to boxsizing.htc
  - Improvement: Added "Vary: Accept-Encoding" header to css output


1.0.8
-----
  - Fix: Gradients couldn't be disabled in IE
  - Fix: "none" didn't work as a value for box-shadow in IE
  - Fix: IE8 doesn't need the htc file to support box-sizing
  - Improvement: Better soft shadow strength calculation for box-shadow in IE
  - Improvement: Remove duplicate filter values before output


1.0.7
-----
  - Fix: IE didn't use background properties when a gradient declaration was present
  - Fix: -ms-filter was not quoted correctly
  - Fix: The -ms-filter property was not always ouput after the filter property, which is necessary because IE8 sometimes interprets -ms-filter AND filter
  - Improvement: Made some plugins independent of ua sniffing
  - Improvement: Transform plugin displays an error when width and height of an element are not defined (which in required for transforms in IE)


1.0.6
-----
  - Fix: Allow directories inside the base directory
  - Fix: Parser wasn't handling the values of @css lines correctly
  - Improvement: Allow explicit closing of @media blocks using "@media none"
  - Improvement: Fail silently when version.txt goes missing
  - Improvement: Display an error when a plugin that doesn't exist is called
  - Improvement: Display an error when a file outside the base directory is being accessed


1.0.5
-----
  - Fix: Prevent directory traversal by only allowing turbine to access files in the css_base_dir
  - Fix: Made background gradient plugin more tolerant to whitespace


1.0.4
-----
  - Fix: Background gradient sometimes didn't work in Opera
  - Fix: Multiple filters in IE8 didn't work


1.0.3
-----
  - Fix: Inheritance from multiple instances of the same selector failed
  - Improvement: Better debugging output when dealing with inheritance


1.0.2
-----
  - Fix: Bugfix plugin inserted unnecessary behavior values
  - Fix: Wrong paths for htc files in bugfix plugin
  - Fix: Removed non-existing IE6 plugin from legacy meta plugin


1.0.1
-----
  - Fix: IE7 didn't recieve images as MHTML
  - Fix: Loader plugin preserved original file paths for included modules
  - Fix: Box sizing wasn't part of CSS3 meta plugin


1.0.0
-----
  - Initial Version

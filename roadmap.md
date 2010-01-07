Version 1 Beta 1
================

Todo
----

Missing features:

 * [WIP] Documentation: Everything
 * plugins/browser.php: Everything
 * plugins/inline_block.php: Add usage and example
 * [DONE] plugins/sprite_position.php: Add usage and example
 * plugins/os.php: Add os version detection
 * plugins/transparent_backgrounds.php: Add IE6 gradient filter

Known bugs:

 * [DONE] cssp.php: Aliased selectors are not inserted at their original position (L129)
 * cssp.php: Inheritance does not work for elements in arrays (L161)
 * [DONE] parser.php: Fix sloppy merge (L446)
 * [DONE] parser.php: Rewrite and unify merging of regular selectors and @font-face (L513-564)
Thank you for downloading Rounded PHP!

Rounded PHP provides an easy, intuitive solution for generating
rounded corner images.

Why use this package? Perfectly smooth anti-aliased rounded corners
are a nice reason. I decided I didn't like most of the code I found
out there for making anti-aliased corners. Now, I'm no expert, but
I like to keep a clean coding practice, and as simple as possible.
So, I took my time with this one. And I believe I've created a very
fast and efficient solution that is as extensible as it is usefull.
The anti-aliasing is not 'interpolated' like most of the scripts out
there, this one computes percentage of a pixel consumed by the arc
and generates the closest possible pixel colors. Excessive looping
was a major concern in my scripts and I reduced the amount of
computation to a minimum. In regards to the common concern of image
processing in php being fairly taxing on system memory, I also chose
to not use recursive algorithms.

In png format, each color, whether it be the border, background, or
foreground, can have variable opacities. great for overlays or drop-
shadow effects.

ugh... enough nerd talk. time for a drink, and time for you to make
some rounded corners.

enjoy my first-ever release.

For more information,
 - consult http://dev.kingthief.com
 - visit http://www.sourceforge.net/projects/roundedphp
 - or email me at dev@kingthief.com


This README file includes the following:

 1. Package contents and file structure

 2. Requirements

 3. Basic Installation

 4. Use and Implementation



#######################
#                     #
# 1. Package Contents #
#                     #
#######################

  /

    / LICENSE.txt		: licensing information

    / README.txt		: this file

    / CHANGELOG.txt		: Changes made between releases

    / RoundedPHP

      / HTACCESS.txt		: sample htaccess file to use

      / rounded.php		: script used to create images
                                  on the fly



###################
#                 #
# 2. Requirements #
#                 #
###################

 - Webserver runing PHP 5+

 - GD v2 extension enabled for PHP (A MUST!)



#########################
#                       #
# 3. Basic Installation #
#                       #
#########################

 - place the rounded.php file somewhere in your site. This is the file
   you will request in 'src' attributes within image tags or in css
   on your site

 - start making some rounded corners!

 * Note: To use transparent PNGs created by Rounded PHP in IE 5.5+,
         please download iepngfix at http://www.twinhelix.com.
         Refer to that site for configuration and detailed instructions on using
         the package.



#############################
#                           #
# 4. Use and Implementation #
#                           #
#############################

Below is an example on how to implement this into your site:

 <img src="/path/to/rounded.php?shape=corner&r=20&bw=2&bc=f00&bg=FFF&fg=FF0000&f=png"/>

- or with mod_rewrite rule enabled

 <img src="/rounded/sh_r/r_20/bw_2/bc_f00/bg_FFF/fg_FF0000/f_png/"/>

basically, rounded.php will return a generated image. request the
rounded.php file with a set of parameters to get the output you want.

Parameter variables come in 2 flavors, short and long-hand. the short-hand versions
can be seen in brackets below each variable (short-hand should be your preferred method).

ACCEPTED PARAMETERS:

 shape = {'r' (or 'rect' or 'rectangle'), 'c' (or 'corner'), 's' (or 'side')}
 [sh]
						# shape of output image
						# (rounded rectangle, rounded corner,
						# side of rectangle)

 radius = {1, 2, ... , n}			# outer radius of the rounded corners
 [r]

 width = {2, 3, ... , n}			# width of rounded rectangle (and top
 [w]						# and bottom side images)

 height = {2, 3, ... , n}			# height of rounded rectangle (and left
 [h]						# and right side images)

 foregroundcolor = 3 or 6 character hex code	# foreground color (inside of arc)
 [fgc]

 backgroundcolor = 3 or 6 character hex code	# background color
 [bgc]

 bordercolor = 3 or 6 character hex code	# border color
 [bc]

 borderwidth = {0, 1, ... , n}			# border width
 [bw]

 orientation = {'tl' (or 'lt'), 'tr' (or 'rt'), 'bl' (or 'lb'), 'br' (or 'rb')}
 [o]
						# orientation of a corner image
						# if that shape is requested

 side = {'t' (or 'top'), 'l' (or 'left'), 'r' (or 'right'), 'b' (or 'bottom')}
 [si]
						# which side of the rectangle to generate
						# if that shape is requested

 antialias = {1, 0}				# toggle antialias support on or off
 [aa]

 format = {'png', 'gif', 'jpg' (or 'jpeg')}	# image output format
 [f]

 backgroundopacity = {0, 1, ... , 100}		# % opacity value for the background color
 [bgo]

 borderopacity = {0, 1, ... , 100}		# % opacity value for the border color
 [bo]

 foregroundopacity = {0, 1, ... , 100}		# % opacity value for the foreground color
 [fgo]

 transparentcolor = 3 or 6 character hex code	# transparent color used in GIF format
 [tc]


that's basically it!
contact me with any questions or suggestions <dev@kingthief.com>

thanks for downloading!

 - Nevada Kent
<?php
/**
 * Rounded PHP, Rounded corners made easy.
 *
 * rounded.php
 *
 * PHP version 5, GD version 2
 *
 * Copyright (C) 2008 Tree Fort LLC
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 * 
 * @category	Rounded PHP
 * @package		<none>
 * @author		Nevada Kent <dev@kingthief.com>
 * @version		2.0
 * @link		http://dev.kingthief.com
 * @link		http://dev.kingthief.com/demos/roundedphp
 * @link		http://www.sourceforge.net/projects/roundedphp
 */

if (isset($_GET['params'])) {
	$params = preg_replace(array('/^[\/]+/', '/[\/]+$/'), '', $_GET['params']);
	$vars = array();
	$params = explode('/', $params);
	foreach ($params as $param) {
		$a = explode('_', $param);
		$vars[$a[0]] = $a[1];
	}
	extract($vars);
} else
	extract($_GET);

# Options =
#  - Shape: 'c' (or 'corner'), 'r' (or 'rectangle'), 's' (or 'side')
#  - Radius: (integer >= 0)
#  - Width: (integer >= 2)
#  - Height: (integer >= 2)
#  - Border Width: (integer >= 0)
#  - Foreground Color: (hex code - 3 or 6 char)
#  - Background Color: (hex code - 3 or 6 char)
#  - Border Color: (hex code - 3 or 6 char)
#  - Orientation: 'tl' (or 'lt'), 'tr' (or 'rt'), 'bl' (or 'lb'), 'br' (or 'rb')
#  - Side: 't', 'top', 'l', 'left', 'b', 'bottom', 'r', 'right'
#  - Antialias: 1, 0
#  - Format: 'png', 'gif', 'jpg' (or 'jpeg')
#  - Background Opacity: (0 <= integer <= 100)
#  - Border Opacity: (0 <= integer <= 100)
#  - Foreground Opacity: (0 <= integer <= 100)
#  - Transparent Color: (hex code - 3 or 6 char)

$shape = isset($shape) ? strval($shape) : (isset($sh) ? strval($sh) : 'c');
$radius = isset($radius) ? intval($radius) : (isset($r) ? intval($r) : 10);
$width = isset($width) ? intval($width) : (isset($w) ? intval($w) : 100);
$height = isset($height) ? intval($height) : (isset($h) ? intval($h) : 100);
$foregroundcolor = isset($foregroundcolor) ? strval($foregroundcolor) : (isset($fgc) ? strval($fgc) : 'CCCCCC');
$backgroundcolor = isset($backgroundcolor) ? strval($backgroundcolor) : (isset($bgc) ? strval($bgc) : 'FFFFFF');
$bordercolor = isset($bordercolor) ? strval($bordercolor) : (isset($bc) ? strval($bc) : '000000');
$borderwidth = isset($borderwidth) ? intval($borderwidth) : (isset($bw) ? intval($bw) : 0);
$orientation = isset($orientation) ? strval($orientation) : (isset($o) ? strval($o) : 'tl');
$side = isset($side) ? strval($side) : (isset($si) ? strval($si) : 'top');
$antialias = isset($antialias) ? (bool) intval($antialias) : (isset($aa) ? (bool) intval($aa) : true);
$format = isset($format) ? strval($format) : (isset($f) ? strval($f) : 'png');
$backgroundopacity = isset($backgroundopacity) ? intval($backgroundopacity) : (isset($bgo) ? intval($bgo) : 100);
$borderopacity = isset($borderopacity) ? intval($borderopacity) : (isset($bo) ? intval($bo) : 100);
$foregroundopacity = isset($foregroundopacity) ? intval($foregroundopacity) : (isset($fgo) ? intval($fgo) : 100);
$transparentcolor = isset($transparentcolor) ? strval($transparentcolor) : (isset($tc) ? strval($tc) : NULL);

switch (strtolower($format)) {
	case 'jpg' :
	case 'jpeg' :
		$transparentcolor = NULL;
	case 'gif' :
		$backgroundopacity = 100;
		$borderopacity = 100;
		$foregroundopacity = 100;
		break;
	case 'png' :
		$transparentcolor = NULL;
		break;
}

$params = array(
	'radius'			=> $radius,
	'width'				=> $width,
	'height'			=> $height,
	'borderwidth'		=> $borderwidth,
	'orientation'		=> $orientation,
	'side'				=> $side,
	'antialias'			=> $antialias,
	'colors'			=> array(
		'foreground'	=> new Color($foregroundcolor, $foregroundopacity / 100),
		'border'		=> new Color($bordercolor, $borderopacity / 100),
		'background'	=> new Color($backgroundcolor, $backgroundopacity / 100)
	)
);

switch (strtolower($shape)) {
	case 'r' :
	case 'rect' :
	case 'rectangle' :
		$img = Rectangle::create($params);
		break;
	case 's' :
	case 'side' :
		$img = Side::create($params);
		break;
	case 'c' :
	case 'corner' :
	default :
		$img = Corner::create($params);
		break;
}

imagesavealpha($img, true);

if (!is_null($transparentcolor) && $transparentcolor) {
	$color = new Color($transparentcolor);
	imagecolortransparent($img, $color->getColorResource($img));
}

header('Cache-Control: max-age=3600, must-revalidate');
header('Pragma: cache');

switch (strtolower($format)) {
	case 'jpg' :
	case 'jpeg' :
		header('Content-Type: image/jpeg');
		imagejpeg($img, '', 100);
		break;
	case 'gif' :
		header('Content-Type: image/gif');
		imagegif($img);
		break;
	case 'png' :
	default :
		header('Content-Type: image/png');
		imagepng($img);
		break;
}

####################################################################################

/**
 * Limit
 *
 * Constrain a numeral to fall between two values
 *
 * @access	public
 * @param	mixed	$val	value to constrain
 * @param	mixed	$c1		first contraint
 * @param	mixed	$c2		second constraint
 * @return	mixed			constrained value
 */
function limit($val, $c1, $c2) {
	return min(max($val, min($c1, $c2)), max($c1, $c2));
}

/**
 * ImageFlipHorizontal
 *
 * Flip an image horizontally
 *
 * @access	public
 * @param	image	$old	image resource for original image
 * @return	void
 */
function imageFlipHorizontal(&$old)
{
	$w = imagesx($old);
	$h = imagesy($old);
	$new = imagecreatetruecolor($w, $h);
	imagealphablending($new, false);
	for ($x = 0; $x < $w; $x++)
		imagecopy($new, $old, $x, 0, $w - $x - 1, 0, 1, $h);
	$old = $new;
}

/**
 * ImageFlipVertical
 *
 * Flip an image vertically
 *
 * @access	public
 * @param	image	$old	image resource for original image
 * @return	void
 */
function imageFlipVertical(&$old)
{
	$w = imagesx($old);
	$h = imagesy($old);
	$new = imagecreatetruecolor($w, $h);
	imagealphablending($new, false);
	for ($y = 0; $y < $h; $y++)
		imagecopy($new, $old, 0, $y, 0, $h - $y - 1, $w, 1);
	$old = $new;
}

/**
 * Area
 *
 * Given a value for x = n, computes the area under a circular arc
 * from x = 0 -> n, with the cirle centerd at the orgin
 *
 * @access	public
 * @param	int		$x	x-coordinate for the pixel
 * @param	int		$r	radius of the arc
 * @return	float	area under the arc
 */
function area($x, $r)
{
	return ($x * loc($x, $r) + $r * $r * asin($x / $r)) / 2;
}

/**
 * IsInside
 *
 * Helper method to determine if a coordinate lies inside
 * of the arc.
 *
 * @access	public
 * @param	int		$x	x-coordinate
 * @param	int		$y	y-coordinate
 * @param	int		$r	radius of the arc
 * @return	bool	true if coordinate lies inside bounds of arc
 */
function isInside($x, $y, $r)
{
	return $x * $x + $y * $y <= $r * $r;
}

/**
 * LawOfCosines (loc)
 *
 * Used to calculate length of opposite side
 * of a right triangle, given the length of the
 * hypotenuse and one side.
 *
 * @access	public
 * @param	int		$xy		Length of either side of the right triangle
 * @param	int		$h		Length of the hypotenuse
 * @return	int		Length of the unknown side
 */
function loc($xy, $r)
{
	return sqrt($r * $r - $xy * $xy);
}

/**
 * Class used to convert hex codes to hexidecimal rgb values
 * and store opacity values
 *
 * Use:
 *  $color = new Color('FFAAEE', 0.3);
 *  $resrc = $color->getColorResource($image);
 */
class Color
{
	public $hex,			# original hex code
		   $red = 0,		# red hexidecimal value
		   $green = 0,		# green hexidecimal value
		   $blue = 0,		# blue hexidecimal value
		   $opacity = 1;	# opacity
	
	/**
	 * Constructor for the Color object.
	 *
	 * @access	public
	 * @param	string	$hex		3 or 6 character hex code
	 * @param	float	$opacity	decimal from 0 to 1
	 * @return	void
	 */
	public function __construct($hex, $opacity = 1) {
		$this->hex = preg_replace('/[^a-fA-F0-9]+/', '', $hex);
		
		if (preg_match('/^([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])$/', $this->hex, $m)) {
			$this->red = hexdec($m[1] . $m[1]);
			$this->green = hexdec($m[2] . $m[2]);
			$this->blue = hexdec($m[3] . $m[3]);
		} else if (preg_match('/^([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})$/', $this->hex, $m)) {
			$this->red = hexdec($m[1]);
			$this->green = hexdec($m[2]);
			$this->blue = hexdec($m[3]);
		}
		
		$this->opacity = limit($opacity, 0, 1);
	}
	
	/**
	 * GetColorResource
	 *
	 * Retreives allocated color object for an image
	 *
	 * @access	public
	 * @param	image	$image	image resource created by imagecreatetruecolor
	 * @return	color	color resource allocated for supplied image
	 */
	public function getColorResource($image) {
		return imagecolorallocatealpha($image, $this->red, $this->green, $this->blue, 127 * (1 - $this->opacity));
	}
}

/**
 * Class used to create rounded corner images with optional borders
 *
 * Use:
 *  $params = array(
 *  	'radius'		=> 15,
 * 		'orientation'	=> 'bl',
 *		'borderwidth'	=> 2
 *  );
 *  $img = Corner::create($params);
 *  header('Content-Type: image/png');
 *  imagepng($img);
 */
class Corner
{
	private $radius = 10,				# radius of corner
			$orientation = 'tl',		# orientation of corner
			$borderwidth = 0,			# width of border
			$antialias = true;			# antialias flag
	
	/**
	 * Constructor for the Corner object.
	 *
	 * @access	public
	 * @param	array	$params	Associative array of custom parameters:
	 *								- radius		: {1, 2, ... , n}
	 *								- orientation	: {'tl', 'tr', 'br', 'bl'}
	 *								- borderwidth	: {0, 1, ... , n}
	 *								- antialias		: {true, false}
	 *								- colors		: array of color objects [foreground, border, background]
	 * @return	void
	 */
	public function __construct($params)
	{
		if (is_array($params))
			foreach($params as $param => $value)
				$this->{$param} = $value;
		
		$this->radius = max(intval($this->radius), 1);
		$this->borderwidth = limit($this->borderwidth, 0, $this->radius);
		$this->orientation = strtolower($this->orientation);
	}
	
	/**
	 * Image
	 *
	 * Used to build the actual image resource.
	 *
	 * @access	public
	 * @return	image resource for final rounded corner
	 */
	public function image()
	{
		$this->image = imagecreatetruecolor($this->radius, $this->radius);
		imagealphablending($this->image, false);
		
		$this->draw();
		
		switch ($this->orientation) {
			case 'br' :
			case 'rb' :
				break;
			case 'bl' :
			case 'lb' :
				imageFlipHorizontal($this->image);
				break;
			case 'tr' :
			case 'rt' :
				imageFlipVertical($this->image);
				break;
			case 'tl' :
			case 'lt' :
			default :
				imageFlipHorizontal($this->image);
				imageFlipVertical($this->image);
				break;
		}
		
		return $this->image;
	}
	
	/**
	 * Draw
	 *
	 * Draws the arcs on the image. Includes border and
	 * opacity levels.
	 * Always draws quadrant IV of a circle with center
	 * positioned at (0,0).
	 *
	 * @access	private
	 * @return	void
	 */
	private function draw()
	{
		$c = $this->colors['background']->getColorResource($this->image);
		imagefilledrectangle($this->image, 0, 0, $this->radius - 1, $this->radius - 1, $c);
		
		if ($this->borderwidth > 0) {
			$c = $this->colors['border']->getColorResource($this->image);
			imagefilledellipse($this->image, 0, 0, ($this->radius - 1) * 2, ($this->radius - 1) * 2, $c);
			$this->drawAA($this->radius, $this->colors['border'], $this->colors['background']);
		}
		
		if ($this->radius - $this->borderwidth > 0) {
			$c = $this->colors['foreground']->getColorResource($this->image);
			imagefilledellipse($this->image, 0, 0, ($this->radius - $this->borderwidth - 1) * 2, ($this->radius - $this->borderwidth - 1) * 2, $c);
			if ($this->borderwidth > 0)
				$this->drawAA($this->radius - $this->borderwidth, $this->colors['foreground'], $this->colors['border']);
			else
				$this->drawAA($this->radius, $this->colors['foreground'], $this->colors['background']);
		}
	}
	
	/**
	 * DrawAA
	 *
	 * Draws the antialiasing around each arc
	 *
	 * @access	private
	 * @param	int		$r	radius of arc
	 * @param	Color	$c1	Color object inside arc
	 * @param	Color	$c2	Color object outside arc
	 * @return	void
	 */
	private function drawAA($r, $c1, $c2) {
		if (!$this->antialias)
			return;
		
		$px = array_fill(0, $r, array_fill(0, $r, false));
		
		for ($x = 0; $x < $r; $x++) {
			for ($y = ceil(loc($x, $r)) - 1; $y > -1; $y--) {
				if ($px[$x][$y])
					return;
				
				if (isInside($x + 1, $y + 1, $r))
					break;
				
				$color = $this->blendColors($c1, $c2, $this->computeRatio($x, $y, $r));
				$c = $color->getColorResource($this->image);
				
				imagesetpixel($this->image, $x, $y, $c);
				$px[$x][$y] = true;
				
				if ($x <> $y) {
					imagesetpixel($this->image, $y, $x, $c);
					$px[$y][$x] = true;
				}
			}
		}
	}
	
	/**
	 * ComputeRatio
	 *
	 * Determines the ratio of two colors to be blended
	 *
	 * @access	private
	 * @param	int		$x	x-coordinate for the pixel
	 * @param	int		$y	y-coordinate for the pixel
	 * @param	int		$r	radius of the arc
	 * @return	int		value for color ratio (0 <= r <= 1)
	 */
	private function computeRatio($x, $y, $r)
	{
		if (!$this->antialias)
			return 1;
		
		$x_a = min($x + 1, loc($y, $r));
		$x_b = max($x, loc($y + 1, $r));
		return area($x_a, $r) - area($x_b, $r) + $x_b - $x - $y * ($x_a - $x_b);
	}
	
	/**
	 * BlendColors
	 *
	 * Blends 2 colors, giving attention to both
	 * the ratio of color amounts, and the opacity
	 * level of each color
	 *
	 * @access	private
	 * @param	Color	$c1	1st color
	 * @param	Color	$c2	2nd color
	 * @param	float	$r	ratio of blend (0.7 means 70% of color 1)
	 */
	private function blendColors($c1, $c2, $r)
	{
		$o1 = $c1->opacity * $r;
		$o2 = $c2->opacity * (1 - $r);
		$o = $o1 + $o2;
		
		$o_r = $o == 0 ? 0 : $o2 / $o;
		
		$r = str_pad(dechex($c1->red - $o_r * ($c1->red - $c2->red)), 2, '0', STR_PAD_LEFT);
		$g = str_pad(dechex($c1->green - $o_r * ($c1->green - $c2->green)), 2, '0', STR_PAD_LEFT);
		$b = str_pad(dechex($c1->blue - $o_r * ($c1->blue - $c2->blue)), 2, '0', STR_PAD_LEFT);
		
		return new Color($r . $g . $b, $o);
	}
	
	/**
	 * Create
	 *
	 * Method used as a factory for corner images.
	 * Offers a quick way to send parameters and return
	 * an image resource for output.
	 *
	 * @static
	 * @access	public
	 * @param	array	$params	Associative array of custom parameters:
	 *								- (See constructor docs for accepted values)
	 * @return	image	resource for generated rounded corner
	 */
	public static function create($params)
	{
		$c = new Corner($params);
		return $c->image();
	}
}

/**
 * Class used to create rounded side images with optional borders
 *
 * Use:
 *  $params = array(
 *  	'radius'	=> 15,
 * 		'side'		=> 'left',
 *		'height'	=> 400
 *  );
 *  $img = Side::create($params);
 *  header('Content-Type: image/png');
 *  imagepng($img);
 */
class Side
{
	private $width = 100,				# width of rectangle
			$height = 100,				# height of rectangle
			$radius = 10,				# radius of corner
			$borderwidth = 0,			# width of border
			$side = 'top',				# side of the rectangle to generate
			$antialias = true;			# antialias flag
	
	/**
	 * Constructor for the Side object.
	 *
	 * @access	public
	 * @param	array	$params	Associative array of custom parameters:
	 *								- width			: {2, 3, ... , n}
	 *								- height		: {2, 3, ... , n}
	 *								- radius		: {1, 2, ... , n}
	 *								- borderwidth	: {0, 1, ... , n}
	 *								- side			: side of the rectangle to render {'r', 'l', 't', 'b'}
	 *								- antialias		: {true, false}
	 *								- colors		: array of color objects [foreground, border, background]
	 * @return	void
	 */
	public function __construct($params)
	{
		if (is_array($params))
			foreach($params as $param => $value)
				$this->{$param} = $value;
		
		$this->width = max($this->width, 1);
		$this->height = max($this->height, 1);
		$this->side = strtolower($this->side);
		
		switch ($this->side) {
			case 'l' :
			case 'left' :
			case 'r' :
			case 'right' :
				$this->width = $this->radius = limit($this->radius, 1, floor($this->height / 2));
				break;
			case 't' :
			case 'top' :
			case 'b' :
			case 'bottom' :
			default :
				$this->height = $this->radius = limit($this->radius, 1, floor($this->width / 2));
				break;
		}
		
		$this->borderwidth = limit($this->borderwidth, 0, $this->radius);
	}
	
	/**
	 * Image
	 *
	 * Used to build the actual image resource.
	 *
	 * @access	public
	 * @return	image resource for rounded rectangle side
	 */
	public function image()
	{
		$this->image = imagecreatetruecolor($this->width, $this->height);
		imagealphablending($this->image, false);
		
		$color = $this->colors['foreground']->getColorResource($this->image);
		imagefilledrectangle($this->image, 0, 0, $this->width - 1, $this->height - 1, $color);
		
		if ($this->borderwidth > 0) {
			$color = $this->colors['border']->getColorResource($this->image);
			
			switch ($this->side) {
				case 'l' :
				case 'left' :
					imagefilledrectangle($this->image, 0, 0, $this->borderwidth - 1, $this->height - 1, $color);
					break;
				case 'r' :
				case 'right' :
					imagefilledrectangle($this->image, $this->width - $this->borderwidth, 0, $this->width - 1, $this->height - 1, $color);
					break;
				case 'b' :
				case 'bottom' :
					imagefilledrectangle($this->image, 0, $this->height - $this->borderwidth, $this->width - 1, $this->height - 1, $color);
					break;
				case 't' :
				case 'top' :
				default :
					imagefilledrectangle($this->image, 0, 0, $this->width - 1, $this->borderwidth - 1, $color);
					break;
			}
		}
		
		$params = array(
			'radius'		=> $this->radius,
			'orientation'	=> 'tl',
			'colors'		=> $this->colors,
			'borderwidth'	=> $this->borderwidth,
			'antialias'		=> $this->antialias
		);
		
		$img = Corner::create($params);
		
		if ($this->side == 't' || $this->side == 'top' || $this->side == 'l' || $this->side == 'left')
			imagecopy($this->image, $img, 0, 0, 0, 0, $this->radius, $this->radius);
		
		imageFlipVertical($img);
		
		if ($this->side == 'l' || $this->side == 'left' || $this->side == 'b' || $this->side == 'bottom')
			imagecopy($this->image, $img, 0, $this->height - $this->radius, 0, 0, $this->radius, $this->radius);
		
		imageFlipHorizontal($img);
		
		if ($this->side == 'b' || $this->side == 'bottom' || $this->side == 'r' || $this->side == 'right')
			imagecopy($this->image, $img, $this->width - $this->radius, $this->height - $this->radius, 0, 0, $this->radius, $this->radius);
		
		imageFlipVertical($img);
		
		if ($this->side == 'r' || $this->side == 'right' || $this->side == 't' || $this->side == 'top')
			imagecopy($this->image, $img, $this->width - $this->radius, 0, 0, 0, $this->radius, $this->radius);
		
		imagedestroy($img);
		
		return $this->image;
	}
	
	/**
	 * Create
	 *
	 * Method used as a factory for rectangle side images.
	 * Offers a quick way to send parameters and return
	 * an image resource for output.
	 *
	 * @static
	 * @access	public
	 * @param	array	$params	Associative array of custom parameters:
	 *								- (See constructor docs for accepted values)
	 * @return	image resource for generated side image
	 */
	public static function create($params)
	{
		$s = new Side($params);
		return $s->image();
	}
}

/**
 * Class used to create rounded rectangle images with optional borders
 *
 * Use:
 *  $params = array(
 *  	'radius'		=> 15,
 * 		'width'			=> 300,
 *		'height'		=> 500,
 *		'background'	=> 'FF0000'
 *  );
 *  $img = Rectangle::create($params);
 *  header('Content-Type: image/png');
 *  imagepng($img);
 */
class Rectangle
{
	private $width = 100,				# width of rectangle
			$height = 100,				# height of rectangle
			$radius = 10,				# radius of corner
			$borderwidth = 0,			# width of border
			$antialias = true;			# antialias flag
	
	/**
	 * Constructor for the Rectangle object.
	 *
	 * @access	public
	 * @param	array	$params	Associative array of custom parameters:
	 *								- width			: {2, 3, ... , n}
	 *								- height		: {2, 3, ... , n}
	 *								- radius		: {1, 2, ... , n}
	 *								- borderwidth	: {0, 1, ... , n}
	 *								- antialias		: {true, false}
	 *								- colors		: array of color objects [foreground, border, background]
	 * @return	void
	 */
	public function __construct($params)
	{
		if (is_array($params))
			foreach($params as $param => $value)
				$this->{$param} = $value;
		
		$this->width = max($this->width, 2);
		$this->height = max($this->height, 2);
		$this->radius = limit($this->radius, 1, floor(min($this->width, $this->height) / 2));
		$this->borderwidth = limit($this->borderwidth, 0, ceil(min($this->width, $this->height) / 2));
	}
	
	/**
	 * Image
	 *
	 * Used to build the actual image resource.
	 *
	 * @access	public
	 * @return	image resource for rounded rectangle
	 */
	public function image()
	{
		$this->image = imagecreatetruecolor($this->width, $this->height);
		imagealphablending($this->image, false);
		
		$color = $this->colors['border']->getColorResource($this->image);
		imagefilledrectangle($this->image, 0, 0, $this->width - 1, $this->height - 1, $color);
		
		if ($this->borderwidth < min($this->width, $this->height) / 2) {
			$color = $this->colors['foreground']->getColorResource($this->image);
			imagefilledrectangle($this->image, $this->borderwidth, $this->borderwidth, $this->width - $this->borderwidth - 1, $this->height - $this->borderwidth - 1, $color);
		}
		
		$params = array(
			'radius'		=> $this->radius,
			'orientation'	=> 'tl',
			'colors'		=> $this->colors,
			'borderwidth'	=> $this->borderwidth,
			'antialias'		=> $this->antialias
		);
		
		$img = Corner::create($params);
		imagecopy($this->image, $img, 0, 0, 0, 0, $this->radius, $this->radius);
		
		imageFlipVertical($img);
		imagecopy($this->image, $img, 0, $this->height - $this->radius, 0, 0, $this->radius, $this->radius);
		
		imageFlipHorizontal($img);
		imagecopy($this->image, $img, $this->width - $this->radius, $this->height - $this->radius, 0, 0, $this->radius, $this->radius);
		
		imageFlipVertical($img);
		imagecopy($this->image, $img, $this->width - $this->radius, 0, 0, 0, $this->radius, $this->radius);
		
		imagedestroy($img);
		
		return $this->image;
	}
	
	/**
	 * Create
	 *
	 * Method used as a factory for rectangle images.
	 * Offers a quick way to send parameters and return
	 * an image resource for output.
	 *
	 * @static
	 * @access	public
	 * @param	array	$params	Associative array of custom parameters:
	 *								- (See constructor docs for accepted values)
	 * @return	image resource of rounded rectangle
	 */
	public static function create($params)
	{
		$r = new Rectangle($params);
		return $r->image();
	}
}

?>
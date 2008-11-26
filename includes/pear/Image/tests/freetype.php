<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is a visual test case, testing basic Freetype support within GD
 * 
 * If this fails, a basic requirement of Image_Graph is not met, and it is as
 * such not a Image_Graph failure but a (missing) component of PHP all together.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version. This library is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser
 * General Public License for more details. You should have received a copy of
 * the GNU Lesser General Public License along with this library; if not, write
 * to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307 USA
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Tests
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id: freetype.php,v 1.2 2005/02/21 20:49:58 nosey Exp $
 * @link       http://pear.php.net/package/Image_Graph
 */

// SPECIFY HERE WHERE A TRUETYPE FONT CAN BE FOUND
$testFont = 'c:/windows/fonts/Arial.ttf';

if (!file_exists($testFont)) {
    die('The font specified cannot be found (' . $testFont .')! Please specify an existing font');
}

// create a true color image (requires GD2)
$image = ImageCreateTrueColor(600, 200);
ImageAlphaBlending($image, true);

// allocate some colors
$black = ImageColorAllocate($image, 0, 0, 0);   
$red = ImageColorAllocate($image, 0xff, 0, 0);   
$green = ImageColorAllocate($image, 0, 0xff, 0);   
$blue = ImageColorAllocate($image, 0, 0, 0xff);   
$white = ImageColorAllocate($image, 0xff, 0xff, 0xff);   

// create a frame
ImageFilledRectangle($image, 0, 0, 599, 199, $white);
ImageRectangle($image, 0, 0, 599, 199, $black);

// output some text using the specified font
$y = 20;
$text = 'Your Freetype installation with GD works';
for ($i = 12; $i <= 20; $i++) {
    $box = ImageTTFBbox($i, 0, $testFont, $text);
    $x = 300 - (max($box[0], $box[2], $box[4], $box[6]) - min($box[0], $box[2], $box[4], $box[6])) / 2;
    ImageTTFText($image, $i, 0, $x, $y, $black, $testFont, $text);
    $y += max($box[1], $box[3], $box[5], $box[7]) - min($box[1], $box[3], $box[5], $box[7]); 
} 

// output the test image
header('Content-Type: image/png');
ImagePNG($image);

?>
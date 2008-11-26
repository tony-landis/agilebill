<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is a visual test case, testing basic GD2 support
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
 * @version    CVS: $Id: gd.php,v 1.2 2005/02/21 20:49:58 nosey Exp $
 * @link       http://pear.php.net/package/Image_Graph
 */

// create a true color image (requires GD2)
$image = ImageCreateTrueColor(400, 300);
ImageAlphaBlending($image, true);

// allocate some colors
$black = ImageColorAllocate($image, 0, 0, 0);   
$red = ImageColorAllocate($image, 0xff, 0, 0);   
$green = ImageColorAllocate($image, 0, 0xff, 0);   
$blue = ImageColorAllocate($image, 0, 0, 0xff);   
$white = ImageColorAllocate($image, 0xff, 0xff, 0xff);   

// create a frame
ImageFilledRectangle($image, 0, 0, 399, 299, $white);
ImageRectangle($image, 0, 0, 399, 299, $black);

// draw some lines
ImageLine($image, 200, 50, 350, 150, $red);
ImageLine($image, 200, 60, 350, 160, $green);
ImageLine($image, 200, 70, 350, 170, $blue);

// draw some overlapping alpha blended boxes
$redAlpha = ImageColorAllocateAlpha($image, 0xff, 0, 0, 75); 
$blueAlpha = ImageColorAllocateAlpha($image, 0, 0xff, 0, 75); 
$greenAlpha = ImageColorAllocateAlpha($image, 0, 0, 0xff, 75);

ImageFilledRectangle($image, 50, 50, 90, 90, $redAlpha); 
ImageFilledRectangle($image, 60, 80, 100, 120, $greenAlpha); 
ImageFilledRectangle($image, 80, 60, 120, 100, $blueAlpha); 

// write some _default_ text
for ($font = 1; $font <= 5; $font++) {
    ImageString($image, $font, 50, 150 + $font * 20, 'Testing GD output', $black);
}

ImageString($image, 3, 51, 21, 'Congratulations! The GD2 installation works', $black);
ImageString($image, 3, 50, 20, 'Congratulations! The GD2 installation works', $red);

// output the test image
header('Content-Type: image/png');
ImagePNG($image);

?>
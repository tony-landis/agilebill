<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is a visual test case, testing canvas support for gradient fillings.
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
 * @package    Image_Canvas
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id: gradients.php,v 1.2 2005/08/03 21:17:48 nosey Exp $
 * @link       http://pear.php.net/pepr/pepr-proposal-show.php?id=212
 */  

require_once 'Image/Canvas.php';

$canvas =& Image_Canvas::factory(
    'png', 
    array('width' => 605, 'height' => 350)
);

$gradient = array(
    'type' => 'gradient', 
    'start' => 'yellow', 
    'end' => 'maroon' 
);

$directions = array('horizontal', 'vertical', 'horizontal_mirror', 'vertical_mirror', 'diagonal_tl_br', 'diagonal_bl_tr', 'radial');

$space = 10;
$size = 75;

$canvas->setLineColor('black');
$canvas->rectangle(array('x0' => 0, 'y0' => 0, 'x1' => $canvas->getWidth() - 1, 'y1' => $canvas->getHeight() - 1));

$i = 0;
foreach ($directions as $direction) {
    $gradient['direction'] = $direction;

    $x = $space + ($i * ($size + $space));

    $y = $space;
    $canvas->setGradientFill($gradient);
    $canvas->rectangle(array('x0' => $x, 'y0' => $y, 'x1' => $x + $size, 'y1' => $y + $size));

    $y += $size + $space;
    $canvas->setGradientFill($gradient);
    $canvas->ellipse(array('x' => $x + $size / 2, 'y' => $y + $size / 2, 'rx' => $size / 2, 'ry' => $size / 2));

    $y += $size + $space;
    $canvas->setGradientFill($gradient);
    $canvas->pieslice(array('x' => $x + $size / 2, 'y' => $y + $size / 2, 'rx' => $size / 2, 'ry' => $size / 2, 'v1' => 45, 'v2' => 270));

    $y += $size + $space;
    $points = array();
    $points[] = array('x' => $x + $size / 3, 'y' => $y);
    $points[] = array('x' => $x + $size, 'y' => $y + $size / 2);
    $points[] = array('x' => $x + $size / 3, 'y' => $y + 3 * $size / 4);
    $points[] = array('x' => $x + $size / 5, 'y' => $y + $size);
    $points[] = array('x' => $x, 'y' => $y + $size / 3);
    $y += $size + $space;
    $canvas->setGradientFill($gradient);
    foreach ($points as $point) {
        $canvas->addVertex($point);
    }
    $canvas->polygon(array('connect' => true));
    $i++;
}

$canvas->show();

?>
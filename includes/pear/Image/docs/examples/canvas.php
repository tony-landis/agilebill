<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is just a basic example with some non-specific geometric shapes and
 * texts.
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
 * @version    CVS: $Id: canvas.php,v 1.1 2005/08/03 21:11:22 nosey Exp $
 * @link       http://pear.php.net/pepr/pepr-proposal-show.php?id=212
 */

include_once 'Image/Canvas.php';

$Canvas =& Image_Canvas::factory((isset($_GET['canvas']) ? $_GET['canvas'] : 'png'), array('width' => 400, 'height' => 300));

$Canvas->setLineColor('black');
$Canvas->rectangle(array('x0' => 0, 'y0' => 0, 'x1' => 399, 'y1' => 299));

$Canvas->setGradientFill(array('direction' => 'horizontal', 'start' => 'red', 'end' => 'blue'));
$Canvas->setLineColor('black');
$Canvas->ellipse(array('x' => 199, 'y' => 149, 'rx' => 50, 'ry' => 50));

$Canvas->setFont(array('name' => 'Arial', 'size' => 12));
$Canvas->addText(array('x' => 0, 'y' => 0, 'text' => 'Demonstration of what Image_Canvas do!'));

$Canvas->setFont(array('name' => 'Times New Roman', 'size' => 12));
$Canvas->addText(array('x' => 399, 'y' => 20, 'text' => 'This does not demonstrate what is does!', 'alignment' => array('horizontal' => 'right')));

$Canvas->setFont(array('name' => 'Courier New', 'size' => 7, 'angle' => 270));
$Canvas->addText(array('x' => 350, 'y' => 50, 'text' => 'True, but it\'s all independent of the format!', 'alignment' => array('horizontal' => 'right')));

$Canvas->setFont(array('name' => 'Garamond', 'size' => 10));
$Canvas->addText(array('x' => 199, 'y' => 295, 'text' => '[Changing format is done by changing 3 letters in the source]', 'alignment' => array('horizontal' => 'center', 'vertical' => 'bottom')));

$Canvas->addVertex(array('x' => 50, 'y' => 200));
$Canvas->addVertex(array('x' => 100, 'y' => 200));
$Canvas->addVertex(array('x' => 100, 'y' => 250));
$Canvas->setFillColor('red@0.2');
$Canvas->polygon(array('connect' => true));

$Canvas->image(array('x' => 398, 'y' => 298, 'filename' => './pear-icon.png', 'alignment' => array('horizontal' => 'right', 'vertical' => 'bottom')));

$Canvas->show();
?>
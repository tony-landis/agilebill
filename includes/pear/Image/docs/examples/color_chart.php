<?php
/**
 * Not a real usage example for Image_Graph.
 * 
 * Main purpose: 
 * Color chart of named colors
 * 
 * Other: 
 * Using canvass "outside" Image_Graph
 * 
 * $Id: color_chart.php,v 1.2 2005/08/03 21:21:52 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
$file = file('./data/colors.txt');

require_once 'Image/Canvas.php';
require_once 'Image/Graph/Color.php';
require_once 'Image/Graph/Constants.php';

$Canvas =& Image_Canvas::factory('gd', array('width' => 600, 'height' => 1200));

$i = 0;
$cols = 10;
$Width = ($Canvas->getWidth() / $cols);
$rows = count($file) / $cols;
$rows = floor($rows) + ($rows > floor($rows) ? 1 : 0);
$Height = ($Canvas->getHeight() / $rows);
while (list($id, $color) = each($file)) {
    $color = trim($color);
    $x = ($i % $cols) * $Width + $Width / 2;
    $y = floor($i / $cols) * $Height;
    $Canvas->setLineColor('black');
    $Canvas->setFillColor($color);        
    $Canvas->rectangle($x - $Width / 4, $y, $x + $Width / 4, $y + $Height / 3);
    $Canvas->write($x, $y + $Height / 3 + 3, $color, IMAGE_GRAPH_ALIGN_CENTER_X + IMAGE_GRAPH_ALIGN_TOP);
    
    $rgbColor = Image_Graph_Color::color2RGB($color);
    $rgbs = 'RGB: ';
    unset($rgbColor[3]); 
    while (list($id, $rgb) = each($rgbColor)) {
        $rgbs .= ($rgb < 0x10 ? '0' : '') . dechex($rgb);
    }       
    $Canvas->write($x, $y + $Height / 3 + 13, $rgbs, IMAGE_GRAPH_ALIGN_CENTER_X + IMAGE_GRAPH_ALIGN_TOP);
    $i++;
}

$Canvas->done();      
?>
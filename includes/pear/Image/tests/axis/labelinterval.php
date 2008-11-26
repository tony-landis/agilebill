<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is a visual test case, testing setting of label interval's.
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
 * @version    CVS: $Id: labelinterval.php,v 1.4 2005/08/03 21:21:58 nosey Exp $
 * @link       http://pear.php.net/package/Image_Graph
 */

require_once 'Image/Graph.php';    

// create the graph
$Graph =& Image_Graph::factory('graph', array(800, 600));
// add a TrueType font
$Font =& $Graph->addNew('ttf_font', 'Verdana');
// set the font size to 7 pixels
$Font->setSize(7);

$Graph->setFont($Font);

// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Testing Changing Axis Label Intervals (Bar Charts also test label distance)', 10)),               
        $Matrix = Image_Graph::factory('Image_Graph_Layout_Matrix', array(4, 4)),           
        5            
    )
);

$DS[0] =& Image_Graph::factory('dataset', array(array(0 => 1, 1 => 2, 2 => 0, 3 => 1, 4 => 4)));
$DS[1] =& Image_Graph::factory('dataset', array(array('A' => 1, 'B' => 2, 'C' => 0, 'D' => 1, 'E' => 4)));

$DS[2] =& Image_Graph::factory('dataset', array(array(0 => 1, 1 => 2, 2 => -2, 3 => 1, 4 => 4)));
$DS[3] =& Image_Graph::factory('dataset', array(array('A' => 1, 'B' => 2, 'C' => -2, 'D' => 1, 'E' => 4)));

for ($row = 0; $row < 4; $row++) {
    for ($col = 0; $col < 4; $col++) {
        if (isset($DS[$col])) {
            $Plotarea =& $Matrix->getEntry($row, $col);
            $AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
            $AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);

            if ($row > 2) {
                $AxisX->setLabelInterval(3);
                $AxisX->setTitle('"Odd" interval');
            } elseif ($row > 0) {
                $AxisX->setLabelInterval(2);
                $AxisX->setTitle('Changed interval');
            } else {
                $AxisX->setTitle('Default interval');
            }
            
            if ($row > 2) {
                $AxisY->setLabelInterval(0.25);
                $AxisY->setTitle('Small interval', 'vertical');
            } elseif ($row > 1) {
                $AxisY->setLabelInterval(2);
                $AxisY->setTitle('Changed interval', 'vertical');
            } else {
                $AxisY->setTitle('Default interval', 'vertical');
            }
            
            if ($col > 1) {
                $Plot =& $Plotarea->addNew('bar', $DS[$col]);
                $Plot->setLineColor('gray');
                $Plot->setFillColor('blue@0.2');
            } else {
                $Plot =& $Plotarea->addNew('line', $DS[$col]);
                $Plot->setLineColor('red');
            }
            
            $Plotarea->setBackgroundColor('blue@0.2');
        }
    }
}

$Graph->done();
?>
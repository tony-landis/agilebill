<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is a visual test case, testing the logarithmic axis.
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
 * @version    CVS: $Id: logarithmic.php,v 1.4 2005/08/03 21:21:58 nosey Exp $
 * @link       http://pear.php.net/package/Image_Graph
 */

require_once 'Image/Graph.php';    

// create the graph
$Graph =& Image_Graph::factory('graph', array(600, 400));
// add a TrueType font
$Font =& $Graph->addNew('ttf_font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(7);

$Graph->setFont($Font);

// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Testing Logarihtmic Axis', 10)),               
        $Matrix = Image_Graph::factory('Image_Graph_Layout_Matrix', array(2, 2, false)),           
        5            
    )
);

$Matrix->setEntry(0, 0, Image_Graph::factory('plotarea', array('axis', 'axis')));
$Matrix->setEntry(0, 1, Image_Graph::factory('plotarea', array('axis', 'axis_log')));
$Matrix->setEntry(1, 0, Image_Graph::factory('plotarea', array('axis_log', 'axis')));
$Matrix->setEntry(1, 1, Image_Graph::factory('plotarea', array('axis_log', 'axis_log')));

function sqr($x) { return $x * $x; }

$DS =& Image_Graph::factory('function', array(1, 10, 'sqr', 9));

for ($row = 0; $row < 2; $row++) {
    for ($col = 0; $col < 2; $col++) {
        $Plotarea =& $Matrix->getEntry($row, $col);
        
        $AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
        if (is_a($AxisX, 'Image_Graph_Axis_Logarithmic')) {
            $AxisX->setTitle('Logarithmic');
        } else {
            $AxisX->setTitle('Normal');
        }
        
        $AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
        if (is_a($AxisY, 'Image_Graph_Axis_Logarithmic')) {
            $AxisY->setTitle('Logarithmic', 'vertical');
        } else {
            $AxisY->setTitle('Normal', 'vertical');
        }

        $Plot =& $Plotarea->addNew('line', $DS);
        $Plot->setLineColor('red');
    }
}

$Graph->done();
?>